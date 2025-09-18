<?php
// ====== Setup dasar & helper ======
header('Content-Type: application/json; charset=utf-8');

ini_set('display_errors', 0);               // sembunyikan error ke output
ini_set('log_errors', 1);                   // tetap log ke error_log
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1] ?? '';
$public_base_directory = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/') . "/" . $privat_base_directory;

require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "htmlawed", "mailgen");

// NOTE: FlashAlerts tidak digunakan & berpotensi memicu warning → dihapus.
// $flash  = new FlashAlerts;

function respond_and_exit($con, $payload, int $httpCode = 200, bool $rollback = true)
{
  if (!headers_sent()) {
    http_response_code($httpCode);
  }
  // Tutup koneksi dengan aman
  if ($con) {
    try {
      if ($rollback) {
        $con->rollBack();
      }
    } catch (\Throwable $e) { /* ignore */
    }
    try {
      $con->clearError();
    } catch (\Throwable $e) { /* ignore */
    }
    try {
      $con->close();
    } catch (\Throwable $e) { /* ignore */
    }
  }

  if (is_string($payload)) {
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
  } else {
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
  }
  exit;
}

function as_array($v)
{
  return is_array($v) ? $v : [];
}
function val($a, $k, $def = null)
{
  return (is_array($a) && array_key_exists($k, $a)) ? $a[$k] : $def;
}

// ====== Koneksi & transaksi ======
$con = new Connection();
$oke = true;
$con->beginTransaction();
$con->clearError();

// ====== Ambil input request ======
$input = file_get_contents("php://input");
if (!$input) {
  respond_and_exit($con, ["error" => "Bad Request: empty body"], 400, true);
}

$data = json_decode($input, true);
if (!$data) {
  respond_and_exit($con, ["error" => "Bad Request: invalid JSON"], 400, true);
}

// ====== Proses payload ======
foreach ($data as $key) {
  if (!is_array($key)) {
    continue;
  }

  $type = val($key, 'type');
  // if ($type !== "SALES_RECEIPT") {
  //   continue;
  // }
  if ($type == "SALES_RECEIPT") {
    $dataList = as_array(val($key, 'data'));
    foreach ($dataList as $key2) {
      $action = val($key2, 'action');
  
      if ($action === "WRITE") { // ADD SALES RECEIPT
        $salesReceiptId = val($key2, 'salesReceiptId');
        $query = http_build_query(['id' => $salesReceiptId]);
  
        $urlnya = 'https://zeus.accurate.id/accurate/api/sales-receipt/detail.do?' . $query;
        $result = curl_get($urlnya);
  
        if (!is_array($result) || val($result, 's') === false) {
          $msg = is_array($result) && isset($result['d'][0]) ? $result['d'][0] . " - Response dari Accurate" : "Gagal memanggil API Accurate";
          respond_and_exit($con, $msg, 502, true);
        }
  
        $transDate = val(val($result, 'd', []), 'transDate');
        $date_payment = $transDate ? tgl_db($transDate) : null;
  
        $detail_invoice_accurate = as_array(val(val($result, 'd', []), 'detailInvoice'));
  
        foreach ($detail_invoice_accurate as $value) {
          // ====== Ambil invoice lokal berdasarkan invoiceId Accurate ======
          $invoiceIdAcc = val($value, 'invoiceId');
          if (!$invoiceIdAcc) {
            continue;
          }
  
          $data_invoice = "SELECT * FROM pro_invoice_admin WHERE id_accurate ='" . addslashes($invoiceIdAcc) . "'";
          $res_invoice  = $con->getRecord($data_invoice);
          if (!$res_invoice) {
            continue;
          }
  
          $data_customer = "SELECT * FROM pro_customer where id_customer = '" . addslashes($res_invoice['id_customer']) . "'";
          $row_customer = $con->getRecord($data_customer);
          $credit_limit_temp = $row_customer['credit_limit_temp'];
          $credit_limit_used = $row_customer['credit_limit_used'];
  
          $total_bayar_syop = floatval(val($res_invoice, 'total_bayar', 0));
          $tgl_kirim_inv    = val($res_invoice, 'tgl_invoice_dikirim');
  
          $total_payment    = round(floatval(val($value, 'invoicePayment', 0)));
  
          // ====== Detail invoice ke Accurate ======
          $query_invoice = http_build_query(['id' => $invoiceIdAcc]);
          $urlnya_invoice = 'https://zeus.accurate.id/accurate/api/sales-invoice/detail.do?' . $query_invoice;
          $result_invoice = curl_get($urlnya_invoice);
          if (!is_array($result_invoice) || val($result_invoice, 's') === false) {
            $msg = is_array($result_invoice) && isset($result_invoice['d'][0]) ? $result_invoice['d'][0] . " - Response dari Accurate" : "Gagal memanggil API Accurate (invoice detail)";
            respond_and_exit($con, $msg, 502, true);
          }
  
          $status_invoice = val(val($result_invoice, 'd', []), 'statusName');
          $is_lunas = ($status_invoice === "Lunas") ? 1 : 0;
  
          if ($tgl_kirim_inv == null) {
            // Hapus SR di Accurate & akhiri dengan pesan khusus
            $url_delete = 'https://zeus.accurate.id/accurate/api/sales-receipt/delete.do';
            $id_sales_receipt = ['id' => $salesReceiptId];
            $result_delete = curl_delete($url_delete, json_encode($id_sales_receipt));
  
            if (is_array($result_delete) && val($result_delete, 's') === true) {
              respond_and_exit($con, "Terdapat invoice yang belum ada tanggal kirim.", 200, true);
            } else {
              $msg = (is_array($result_delete) && isset($result_delete['d'][0]))
                ? $result_delete['d'][0] . " - Response dari Accurate"
                : "Gagal menghapus Sales Receipt di Accurate";
              respond_and_exit($con, $msg, 502, true);
            }
          } else {
            if ($is_lunas === 1) {
              $bayar = $total_payment + $total_bayar_syop;
  
              $sql_id_bayar = "
                  SELECT MAX(id_invoice_bayar) AS max_bayar
                    FROM pro_invoice_admin_detail_bayar
                   WHERE id_invoice = '" . addslashes($res_invoice['id_invoice']) . "'";
              $res_max_bayar = $con->getRecord($sql_id_bayar);
              $next_id_invoice_bayar = (isset($res_max_bayar['max_bayar']) && $res_max_bayar['max_bayar'] !== null)
                ? (intval($res_max_bayar['max_bayar']) + 1)
                : 1;
  
              $insert_invoice_detail = "
                  INSERT INTO pro_invoice_admin_detail_bayar
                    (id_invoice_bayar, id_invoice, id_accurate_sales_receipt, tgl_bayar, jumlah_bayar)
                  VALUES
                    ('" . intval($next_id_invoice_bayar) . "',
                     '" . addslashes($res_invoice['id_invoice']) . "',
                     '" . addslashes($salesReceiptId) . "',
                     '" . addslashes($date_payment) . "',
                     '" . floatval($total_payment) . "')";
              $con->setQuery($insert_invoice_detail);
              $oke = $oke && !$con->hasError();
  
              $update = "UPDATE pro_invoice_admin
                              SET total_bayar = '" . floatval($bayar) . "',
                                  is_lunas = '" . intval($is_lunas) . "'
                            WHERE id_invoice = '" . addslashes($res_invoice['id_invoice']) . "'";
              $con->setQuery($update);
              $oke = $oke && !$con->hasError();
  
              // detailDiscount
              $detailDiscount = as_array(val($value, 'detailDiscount'));
              if (!empty($detailDiscount)) {
                foreach ($detailDiscount as $dd) {
                  $kategori = val(val($dd, 'account', []), 'name', '');
                  $nominal  = floatval(val($dd, 'amount', 0));
                  $insert_potongan = "
                      INSERT INTO pro_invoice_bukti_potong
                        (id_invoice, id_accurate_sales_receipt, kategori, nominal, created_at)
                      VALUES
                        ('" . addslashes($res_invoice['id_invoice']) . "',
                         '" . addslashes($salesReceiptId) . "',
                         '" . addslashes($kategori) . "',
                         '" . $nominal . "',
                         NOW())";
                  $con->setQuery($insert_potongan);
                  $oke  = $oke && !$con->hasError();
                }
              }
  
              $history_ar_customer = "
                  INSERT INTO pro_history_ar_customer
                    (id_invoice, id_accurate_sales_receipt, kategori, keterangan, nominal, created_time, created_by)
                  VALUES
                    ('" . addslashes($res_invoice['id_invoice']) . "',
                     '" . addslashes($salesReceiptId) . "',
                     '3', 'Invoice Payment', round(" . floatval($bayar) . "), NOW(), 'Accurate Online')";
              $con->setQuery($history_ar_customer);
              $oke = $oke && !$con->hasError();
  
              // UPDATE AR bucket
              $credit_limit_temp_to_reduce = 0;
              $credit_limit_used_to_reduce = 0;
  
              if ($credit_limit_temp >= floatval($bayar)) {
                // Potong dari temp dulu
                $credit_limit_temp_to_reduce = floatval($bayar);
                $credit_limit_used_to_reduce = 0;
              } else {
                // Habiskan temp, sisanya potong dari used
                $credit_limit_temp_to_reduce = $credit_limit_temp;
                $credit_limit_used_to_reduce = $amount_paid - $credit_limit_temp;
              }
  
              $update_cl = "
                UPDATE pro_customer SET
                  credit_limit_temp = credit_limit_temp - " . floatval($credit_limit_temp_to_reduce) . ",
                  credit_limit_used = credit_limit_used - " . floatval($credit_limit_used_to_reduce) . "
                WHERE id_customer = '" . addslashes($res_invoice['id_customer']) . "'
              ";
              $con->setQuery($update_cl);
              $oke  = $oke && !$con->hasError();
  
              $status_ar = val($res_invoice, 'status_ar');
              if ($status_ar === 'notyet') {
                $sql4 = "UPDATE pro_customer_admin_arnya
                              SET not_yet = ((not_yet - '" . $credit_limit_used_to_reduce . "') + '" . floatval($res_invoice['total_bayar']) . "')
                            WHERE id_customer = '" . addslashes($res_invoice['id_customer']) . "'";
              } else if ($status_ar === 'ov_up_07') {
                $sql4 = "UPDATE pro_customer_admin_arnya
                              SET ov_up_07 = ((ov_up_07 - '" . $credit_limit_used_to_reduce . "') + '" . floatval($res_invoice['total_bayar']) . "')
                            WHERE id_customer = '" . addslashes($res_invoice['id_customer']) . "'";
              } else if ($status_ar === 'ov_under_30') {
                $sql4 = "UPDATE pro_customer_admin_arnya
                              SET ov_under_30 = ((ov_under_30 - '" . $credit_limit_used_to_reduce . "') + '" . floatval($res_invoice['total_bayar']) . "')
                            WHERE id_customer = '" . addslashes($res_invoice['id_customer']) . "'";
              } else if ($status_ar === 'ov_under_60') {
                $sql4 = "UPDATE pro_customer_admin_arnya
                              SET ov_under_60 = ((ov_under_60 - '" . $credit_limit_used_to_reduce . "') + '" . floatval($res_invoice['total_bayar']) . "')
                            WHERE id_customer = '" . addslashes($res_invoice['id_customer']) . "'";
              } else if ($status_ar === 'ov_under_90') {
                $sql4 = "UPDATE pro_customer_admin_arnya
                              SET ov_under_90 = ((ov_under_90 - '" . $credit_limit_used_to_reduce . "') + '" . floatval($res_invoice['total_bayar']) . "')
                            WHERE id_customer = '" . addslashes($res_invoice['id_customer']) . "'";
              } else if ($status_ar === 'ov_up_90') {
                $sql4 = "UPDATE pro_customer_admin_arnya
                              SET ov_up_90 = ((ov_up_90 - '" . $credit_limit_used_to_reduce . "') + '" . floatval($res_invoice['total_bayar']) . "')
                            WHERE id_customer = '" . addslashes($res_invoice['id_customer']) . "'";
              } else {
                $sql4 = null;
              }
              if ($sql4) {
                $con->setQuery($sql4);
                $oke  = $oke && !$con->hasError();
              }
  
              // ====== Cek semua invoice dalam satu grup HSD sudah lunas? ======
              $ambil_dsd = "SELECT id_dsd FROM pro_invoice_admin_detail WHERE id_invoice='" . addslashes($res_invoice['id_invoice']) . "'";
              $row_dsd   = $con->getRecord($ambil_dsd);
  
              $update = "UPDATE pro_invoice_admin
                            SET total_bayar = '" . floatval($bayar) . "',
                                is_lunas = '" . intval($is_lunas) . "'
                          WHERE id_invoice = '" . addslashes($res_invoice['id_invoice']) . "'";
              $con->setQuery($update);
              $oke = $oke && !$con->hasError();
  
              if ($row_dsd && val($row_dsd, 'id_dsd')) {
                $ambil_invoice = "SELECT * FROM pro_invoice_admin_detail WHERE id_dsd='" . addslashes($row_dsd['id_dsd']) . "'";
                $result_invoice_group = $con->getResult($ambil_invoice);
  
                $status_lunas = true;
                $id_invoice_hsd = "";
                foreach (as_array($result_invoice_group) as $ris) {
                  $invoice = "SELECT * FROM pro_invoice_admin WHERE id_invoice = '" . addslashes($ris['id_invoice']) . "'";
                  $row_invoice = $con->getRecord($invoice);
                  if (!$row_invoice) {
                    continue;
                  }
  
                  if (intval(val($row_invoice, 'is_lunas', 0)) !== 1) {
                    $status_lunas = false;
                  }
  
                  $jenis = val($row_invoice, 'jenis', '');
                  if (in_array($jenis, ["all_in", "harga_dasar", "harga_dasar_oa", "harga_dasar_pbbkb"], true)) {
                    $id_invoice_hsd = $row_invoice['id_invoice'];
                  }
                }
  
                if ($status_lunas === true && $id_invoice_hsd !== "") {
                  // ==== UPDATE REFUND ====
                  $sql_refund = "UPDATE pro_refund SET disposisi = 1 WHERE id_invoice = '" . addslashes($id_invoice_hsd) . "'";
                  $con->setQuery($sql_refund);
                  $oke = $oke && !$con->hasError();
  
                  // ==== UPDATE INCENTIVE (ambil data ringkas) ====
                  $sql_incentive = "
                  SELECT a.id_dsd as id_dsdnya, a.id_invoice as id_invoicenya, a.total_incentive, a.disposisi as statusnya,
                         i.nama_customer, i.kode_pelanggan, i.jenis_payment, i.top_payment, i.id_customer as id_customernya,
                         e.alamat_survey, f.nama_prov, g.nama_kab, j.id_user, j.fullname, j.id_role,
                         h.nomor_poc, h.tanggal_poc, h.id_poc as id_pocnya, h.produk_poc, b.volume_po, k.refund_tawar, l.nama_area, l.id_master as id_areanya,
                         m.wilayah_angkut, k.id_penawaran, k.harga_asli as harga_dasarnya, k.harga_tier, k.tier, ppdd.tanggal_delivered,
                         n.no_invoice, n.tgl_invoice_dikirim, n.tgl_invoice, k.masa_awal, k.masa_akhir,
                         CONCAT(o.jenis_produk,' - ', o.merk_dagang) as nama_produk,
                         p.vol_kirim as volume_invoice, n.is_lunas, q.nama_cabang,
                         (SELECT SUM(vol_kirim) FROM pro_invoice_admin_detail WHERE id_invoice=a.id_invoice) as total_vol_invoice
                    FROM pro_incentive a
                    JOIN pro_po_ds_detail ppdd ON ppdd.id_dsd = a.id_dsd
                    JOIN pro_po_detail b ON ppdd.id_pod = b.id_pod
                    JOIN pro_pr_detail c ON ppdd.id_prd = c.id_prd
                    JOIN pro_po_customer_plan d ON ppdd.id_plan = d.id_plan
                    JOIN pro_customer_lcr e ON d.id_lcr = e.id_lcr
                    JOIN pro_master_provinsi f ON e.prov_survey = f.id_prov
                    JOIN pro_master_kabupaten g ON e.kab_survey = g.id_kab
                    JOIN pro_po_customer h ON d.id_poc = h.id_poc
                    JOIN pro_customer i ON h.id_customer = i.id_customer
                    JOIN acl_user j ON a.id_marketing = j.id_user
                    JOIN pro_penawaran k ON h.id_penawaran = k.id_penawaran
                    JOIN pro_master_area l ON k.id_area = l.id_master
                    JOIN pro_master_wilayah_angkut m ON e.id_wil_oa = m.id_master AND e.prov_survey = m.id_prov AND e.kab_survey = m.id_kab
                    JOIN pro_invoice_admin n ON a.id_invoice = n.id_invoice
                    JOIN pro_master_produk o ON o.id_master = h.produk_poc
                    JOIN pro_invoice_admin_detail p ON a.id_invoice = p.id_invoice
                    JOIN pro_master_cabang q ON q.id_master = i.id_wilayah
                   WHERE k.created_time > '2025-03-01' AND n.id_invoice='" . addslashes($id_invoice_hsd) . "'
                   LIMIT 1";
                  $row_incentive = $con->getRecord($sql_incentive);
  
                  if ($row_incentive) {
                    // hitung tier/point
                    if (floatval(val($row_incentive, "harga_tier", 0)) == 0) {
                      $tiernya = "Harga Tier 0";
                      $harganya = "";
                    } else {
                      $tiernya = "Tier " . val($row_incentive, 'tier', '');
                      $harganya = number_format(floatval(val($row_incentive, "harga_tier", 0)));
                    }
  
                    $sql_bayar_1 = "SELECT MAX(tgl_bayar) as tanggal_bayar
                                    FROM pro_invoice_admin_detail_bayar
                                   WHERE id_invoice='" . addslashes($id_invoice_hsd) . "'";
                    $row_bayar_1 = $con->getRecord($sql_bayar_1);
                    $tanggal_bayar = val($row_bayar_1, 'tanggal_bayar');
  
                    $top_payment = intval(val($row_incentive, 'top_payment', 0));
                    $tgl_kirim = val($row_incentive, 'tgl_invoice_dikirim');
                    $due_date = $tgl_kirim ? date('Y-m-d', strtotime($tgl_kirim . " +{$top_payment} days")) : null;
  
                    // Load TOP mapping (aman)
                    $cek_top = "SELECT * FROM pro_top_incentive ORDER BY id ASC";
                    $res_top = as_array($con->getResult($cek_top));
  
                    $top1 = $top2 = $top3 = $top4 = $top5 = 0;
                    foreach ($res_top as $rt) {
                      $t = strval(val($rt, 'top', ''));
                      if ($t === "14") {
                        $top1 = 14;
                      } elseif ($t === "35") {
                        $top2 = 21;
                      } elseif ($t === "54") {
                        $top3 = 19;
                      } elseif ($t === "75") {
                        $top4 = 21;
                      } elseif ($t === "76") {
                        $top5 = 1;
                      }
                    }
  
                    $due_date_week2 = $tgl_kirim ? date('Y-m-d', strtotime($tgl_kirim . " +{$top1} days")) : null;
                    $due_date_week3 = $due_date_week2 ? date('Y-m-d', strtotime($due_date_week2 . " +{$top2} days")) : null;
                    $due_date_week4 = $due_date_week3 ? date('Y-m-d', strtotime($due_date_week3 . " +{$top3} days")) : null;
                    $due_date_week5 = $due_date_week4 ? date('Y-m-d', strtotime($due_date_week4 . " +{$top4} days")) : null;
                    $due_date_week6 = $due_date_week5 ? date('Y-m-d', strtotime($due_date_week5 . " +{$top5} days")) : null;
  
                    $cek_non_penerima = "SELECT id_user FROM pro_non_penerima_incentive WHERE id_user = '" . addslashes(val($row_incentive, 'id_user', '')) . "'";
                    $res_non_penerima = $con->getRecord($cek_non_penerima);
  
                    $week1 = $week2 = $week3 = $week4 = $week5 = $week6 = 0;
                    $total_incentive_fix = 0;
                    $total_point = 0; // default
  
                    $total_vol_invoice = floatval(val($row_incentive, 'total_vol_invoice', 0));
                    $id_role = val($row_incentive, 'id_role');
  
                    if (val($row_incentive, 'jenis_payment') === "CBD") {
                      $cek_point = "SELECT * FROM pro_point_incentive WHERE id_role='" . addslashes($id_role) . "' AND id_top='1' AND tier='" . addslashes($tiernya) . "' LIMIT 1";
                      $res_point = $con->getRecord($cek_point);
                      $total_point = $res_non_penerima ? 0 : intval(val($res_point, 'point', 0));
                      $week1 += $total_vol_invoice * $total_point;
                      $total_incentive_fix = $week1;
                    } else {
                      if ($tanggal_bayar && $due_date_week2 && $tanggal_bayar <= $due_date_week2) {
                        $cek_point = "SELECT * FROM pro_point_incentive WHERE id_role='" . addslashes($id_role) . "' AND id_top='2' AND tier='" . addslashes($tiernya) . "' LIMIT 1";
                        $res_point = $con->getRecord($cek_point);
                        $total_point = $res_non_penerima ? 0 : intval(val($res_point, 'point', 0));
                        $week2 += $total_vol_invoice * $total_point;
                        $total_incentive_fix = $week2;
                      } elseif ($tanggal_bayar && $due_date_week2 && $due_date_week3 && ($tanggal_bayar > $due_date_week2 && $tanggal_bayar <= $due_date_week3)) {
                        $cek_point = "SELECT * FROM pro_point_incentive WHERE id_role='" . addslashes($id_role) . "' AND id_top='3' AND tier='" . addslashes($tiernya) . "' LIMIT 1";
                        $res_point = $con->getRecord($cek_point);
                        $total_point = $res_non_penerima ? 0 : intval(val($res_point, 'point', 0));
                        $week3 += $total_vol_invoice * $total_point;
                        $total_incentive_fix = $week3;
                      } elseif ($tanggal_bayar && $due_date_week3 && $due_date_week4 && ($tanggal_bayar > $due_date_week3 && $tanggal_bayar <= $due_date_week4)) {
                        $cek_point = "SELECT * FROM pro_point_incentive WHERE id_role='" . addslashes($id_role) . "' AND id_top='4' AND tier='" . addslashes($tiernya) . "' LIMIT 1";
                        $res_point = $con->getRecord($cek_point);
                        $total_point = $res_non_penerima ? 0 : intval(val($res_point, 'point', 0));
                        $week4 += $total_vol_invoice * $total_point;
                        $total_incentive_fix = $week4;
                      } elseif ($tanggal_bayar && $due_date_week4 && $due_date_week5 && ($tanggal_bayar > $due_date_week4 && $tanggal_bayar <= $due_date_week5)) {
                        $cek_point = "SELECT * FROM pro_point_incentive WHERE id_role='" . addslashes($id_role) . "' AND id_top='5' AND tier='" . addslashes($tiernya) . "' LIMIT 1";
                        $res_point = $con->getRecord($cek_point);
                        $total_point = $res_non_penerima ? 0 : intval(val($res_point, 'point', 0));
                        $week5 += $total_vol_invoice * $total_point;
                        $total_incentive_fix = $week5;
                      } elseif ($tanggal_bayar && $due_date_week6 && ($tanggal_bayar >= $due_date_week6)) {
                        $total_point = 0;
                        $total_incentive_fix = $week6; // 0
                      }
                    }
  
                    $sql_update_incentive = "
                    UPDATE pro_incentive
                       SET volume = " . floatval($total_vol_invoice) . ",
                           harga_dasar = " . floatval(val($row_incentive, 'harga_dasarnya', 0)) . ",
                           point_incentive = " . intval($total_point) . ",
                           tier = " . ($tiernya ? "'" . addslashes($tiernya) . "'" : "NULL") . ",
                           total_incentive = " . round(floatval($total_incentive_fix)) . ",
                           disposisi = 1,
                           updated_at = NOW()
                     WHERE id_invoice = '" . addslashes($id_invoice_hsd) . "'";
                    $con->setQuery($sql_update_incentive);
                    $oke  = $oke && !$con->hasError();
                  }
                }
              }
              if (!$oke) {
                // Revert SR di Accurate kalau proses lokal gagal
                $url_delete = 'https://zeus.accurate.id/accurate/api/sales-receipt/delete.do';
                $id_sales_receipt = ['id' => $salesReceiptId];
                $result_delete = curl_delete($url_delete, json_encode($id_sales_receipt));
                $msg = (is_array($result_delete) && val($result_delete, 's') === true)
                  ? "gagal"
                  : ((is_array($result_delete) && isset($result_delete['d'][0])) ? $result_delete['d'][0] . " - Response dari Accurate" : "Gagal rollback SR Accurate");
                respond_and_exit($con, $msg, 500, true);
              }
            } else {
              // ====== Jika total_bayar sebelumnya 0 (belum ada bayar tercatat di SYOP) ======
              if (floatval(val($res_invoice, 'total_bayar', 0)) == 0) {
                // catat pembayaran pertama
                $bayar = $total_payment + $total_bayar_syop;
  
                $sql_id_bayar = "
                  SELECT MAX(id_invoice_bayar) AS max_bayar
                    FROM pro_invoice_admin_detail_bayar
                   WHERE id_invoice = '" . addslashes($res_invoice['id_invoice']) . "'";
                $res_max_bayar = $con->getRecord($sql_id_bayar);
                $next_id_invoice_bayar = (isset($res_max_bayar['max_bayar']) && $res_max_bayar['max_bayar'] !== null)
                  ? (intval($res_max_bayar['max_bayar']) + 1)
                  : 1;
  
                $insert_invoice_detail = "
                  INSERT INTO pro_invoice_admin_detail_bayar
                    (id_invoice_bayar, id_invoice, id_accurate_sales_receipt, tgl_bayar, jumlah_bayar)
                  VALUES
                    ('" . intval($next_id_invoice_bayar) . "',
                     '" . addslashes($res_invoice['id_invoice']) . "',
                     '" . addslashes($salesReceiptId) . "',
                     '" . addslashes($date_payment) . "',
                     '" . floatval($total_payment) . "')";
                $con->setQuery($insert_invoice_detail);
                $oke = $oke && !$con->hasError();
  
                $update = "UPDATE pro_invoice_admin
                              SET total_bayar = '" . floatval($bayar) . "',
                                  is_lunas = '" . intval($is_lunas) . "'
                            WHERE id_invoice = '" . addslashes($res_invoice['id_invoice']) . "'";
                $con->setQuery($update);
                $oke = $oke && !$con->hasError();
  
                // detailDiscount
                $detailDiscount = as_array(val($value, 'detailDiscount'));
                if (!empty($detailDiscount)) {
                  foreach ($detailDiscount as $dd) {
                    $kategori = val(val($dd, 'account', []), 'name', '');
                    $nominal  = floatval(val($dd, 'amount', 0));
                    $insert_potongan = "
                      INSERT INTO pro_invoice_bukti_potong
                        (id_invoice, id_accurate_sales_receipt, kategori, nominal, created_at)
                      VALUES
                        ('" . addslashes($res_invoice['id_invoice']) . "',
                         '" . addslashes($salesReceiptId) . "',
                         '" . addslashes($kategori) . "',
                         '" . $nominal . "',
                         NOW())";
                    $con->setQuery($insert_potongan);
                    $oke  = $oke && !$con->hasError();
                  }
                }
  
                $update_cl = "UPDATE pro_customer
                                 SET credit_limit_used = credit_limit_used - " . floatval($bayar) . "
                               WHERE id_customer = '" . addslashes($res_invoice['id_customer']) . "'";
                $con->setQuery($update_cl);
                $oke  = $oke && !$con->hasError();
  
                $history_ar_customer = "
                  INSERT INTO pro_history_ar_customer
                    (id_invoice, id_accurate_sales_receipt, kategori, keterangan, nominal, created_time, created_by)
                  VALUES
                    ('" . addslashes($res_invoice['id_invoice']) . "',
                     '" . addslashes($salesReceiptId) . "',
                     '3', 'Invoice Payment', round(" . floatval($bayar) . "), NOW(), 'Accurate Online')";
                $con->setQuery($history_ar_customer);
                $oke = $oke && !$con->hasError();
  
                // UPDATE AR bucket
                $credit_limit_temp_to_reduce = 0;
                $credit_limit_used_to_reduce = 0;
  
                if ($credit_limit_temp >= floatval($bayar)) {
                  // Potong dari temp dulu
                  $credit_limit_temp_to_reduce = floatval($bayar);
                  $credit_limit_used_to_reduce = 0;
                } else {
                  // Habiskan temp, sisanya potong dari used
                  $credit_limit_temp_to_reduce = $credit_limit_temp;
                  $credit_limit_used_to_reduce = $amount_paid - $credit_limit_temp;
                }
  
                $update_cl = "
                UPDATE pro_customer SET
                  credit_limit_temp = credit_limit_temp - " . floatval($credit_limit_temp_to_reduce) . ",
                  credit_limit_used = credit_limit_used - " . floatval($credit_limit_used_to_reduce) . "
                WHERE id_customer = '" . addslashes($res_invoice['id_customer']) . "'
              ";
                $con->setQuery($update_cl);
                $oke  = $oke && !$con->hasError();
  
                // UPDATE AR bucket
                $status_ar = val($res_invoice, 'status_ar');
                if ($status_ar === 'notyet') {
                  $sql4 = "UPDATE pro_customer_admin_arnya
                              SET not_yet = ((not_yet - '" . floatval($credit_limit_used_to_reduce) . "') + '" . floatval($res_invoice['total_bayar']) . "')
                            WHERE id_customer = '" . addslashes($res_invoice['id_customer']) . "'";
                } else if ($status_ar === 'ov_up_07') {
                  $sql4 = "UPDATE pro_customer_admin_arnya
                              SET ov_up_07 = ((ov_up_07 - '" . floatval($credit_limit_used_to_reduce) . "') + '" . floatval($res_invoice['total_bayar']) . "')
                            WHERE id_customer = '" . addslashes($res_invoice['id_customer']) . "'";
                } else if ($status_ar === 'ov_under_30') {
                  $sql4 = "UPDATE pro_customer_admin_arnya
                              SET ov_under_30 = ((ov_under_30 - '" . floatval($credit_limit_used_to_reduce) . "') + '" . floatval($res_invoice['total_bayar']) . "')
                            WHERE id_customer = '" . addslashes($res_invoice['id_customer']) . "'";
                } else if ($status_ar === 'ov_under_60') {
                  $sql4 = "UPDATE pro_customer_admin_arnya
                              SET ov_under_60 = ((ov_under_60 - '" . floatval($credit_limit_used_to_reduce) . "') + '" . floatval($res_invoice['total_bayar']) . "')
                            WHERE id_customer = '" . addslashes($res_invoice['id_customer']) . "'";
                } else if ($status_ar === 'ov_under_90') {
                  $sql4 = "UPDATE pro_customer_admin_arnya
                              SET ov_under_90 = ((ov_under_90 - '" . floatval($credit_limit_used_to_reduce) . "') + '" . floatval($res_invoice['total_bayar']) . "')
                            WHERE id_customer = '" . addslashes($res_invoice['id_customer']) . "'";
                } else if ($status_ar === 'ov_up_90') {
                  $sql4 = "UPDATE pro_customer_admin_arnya
                              SET ov_up_90 = ((ov_up_90 - '" . floatval($credit_limit_used_to_reduce) . "') + '" . floatval($res_invoice['total_bayar']) . "')
                            WHERE id_customer = '" . addslashes($res_invoice['id_customer']) . "'";
                } else {
                  $sql4 = null;
                }
                if ($sql4) {
                  $con->setQuery($sql4);
                  $oke  = $oke && !$con->hasError();
                }
  
                if (!$oke) {
                  // Revert SR di Accurate kalau proses lokal gagal
                  $url_delete = 'https://zeus.accurate.id/accurate/api/sales-receipt/delete.do';
                  $id_sales_receipt = ['id' => $salesReceiptId];
                  $result_delete = curl_delete($url_delete, json_encode($id_sales_receipt));
                  $msg = (is_array($result_delete) && val($result_delete, 's') === true)
                    ? "gagal"
                    : ((is_array($result_delete) && isset($result_delete['d'][0])) ? $result_delete['d'][0] . " - Response dari Accurate" : "Gagal rollback SR Accurate");
                  respond_and_exit($con, $msg, 500, true);
                }
              } else {
                // ====== Sudah ada pembayaran → update ulang berdasarkan SR terkini ======
                $data_pembayaran = "
                DELETE FROM pro_invoice_admin_detail_bayar
                 WHERE id_invoice ='" . addslashes($res_invoice['id_invoice']) . "'
                   AND id_accurate_sales_receipt = '" . addslashes($salesReceiptId) . "'";
                $con->setQuery($data_pembayaran);
                $oke  = $oke && !$con->hasError();
  
                $data_bupot = "
                DELETE FROM pro_invoice_bukti_potong
                 WHERE id_invoice ='" . addslashes($res_invoice['id_invoice']) . "'
                   AND id_accurate_sales_receipt = '" . addslashes($salesReceiptId) . "'";
                $con->setQuery($data_bupot);
                $oke  = $oke && !$con->hasError();
  
                $sql_id_bayar = "
                SELECT MAX(id_invoice_bayar) AS max_bayar
                  FROM pro_invoice_admin_detail_bayar
                 WHERE id_invoice = '" . addslashes($res_invoice['id_invoice']) . "'";
                $res_max_bayar = $con->getRecord($sql_id_bayar);
                $next_id_invoice_bayar = (isset($res_max_bayar['max_bayar']) && $res_max_bayar['max_bayar'] !== null)
                  ? (intval($res_max_bayar['max_bayar']) + 1)
                  : 1;
  
                $insert_invoice_detail = "
                INSERT INTO pro_invoice_admin_detail_bayar
                  (id_invoice_bayar, id_invoice, id_accurate_sales_receipt, tgl_bayar, jumlah_bayar)
                VALUES
                  ('" . intval($next_id_invoice_bayar) . "',
                   '" . addslashes($res_invoice['id_invoice']) . "',
                   '" . addslashes($salesReceiptId) . "',
                   '" . addslashes($date_payment) . "',
                   '" . floatval($total_payment) . "')";
                $con->setQuery($insert_invoice_detail);
                $oke  = $oke && !$con->hasError();
  
                $data_pembayaran2 = "
                SELECT SUM(jumlah_bayar) as total_bayar
                  FROM pro_invoice_admin_detail_bayar
                 WHERE id_invoice ='" . addslashes($res_invoice['id_invoice']) . "'";
                $res_pembayaran2 = $con->getRecord($data_pembayaran2);
                $sum_bayar = floatval(val($res_pembayaran2, 'total_bayar', 0));
  
                $update = "
                UPDATE pro_invoice_admin
                   SET total_bayar = '" . $sum_bayar . "',
                       is_lunas = '" . intval($is_lunas) . "'
                 WHERE id_invoice = '" . addslashes($res_invoice['id_invoice']) . "'";
                $con->setQuery($update);
                $oke  = $oke && !$con->hasError();
  
                $update_cl = "
                UPDATE pro_customer
                   SET credit_limit_used = (credit_limit_used + " . floatval($res_invoice['total_bayar']) . ") - " . $sum_bayar . "
                 WHERE id_customer = '" . addslashes($res_invoice['id_customer']) . "'";
                $con->setQuery($update_cl);
                $oke  = $oke && !$con->hasError();
  
                $update_history = "
                UPDATE pro_history_ar_customer
                   SET nominal = " . $sum_bayar . ", created_time = NOW()
                 WHERE id_accurate_sales_receipt = '" . addslashes($salesReceiptId) . "'";
                $con->setQuery($update_history);
                $oke  = $oke && !$con->hasError();
  
                // UPDATE AR bucket
                $status_ar = val($res_invoice, 'status_ar');
                if ($status_ar === 'notyet') {
                  $sql4 = "UPDATE pro_customer_admin_arnya
                            SET not_yet = ((not_yet - '" . $sum_bayar . "') + '" . floatval($res_invoice['total_bayar']) . "')
                          WHERE id_customer = '" . addslashes($res_invoice['id_customer']) . "'";
                } else if ($status_ar === 'ov_up_07') {
                  $sql4 = "UPDATE pro_customer_admin_arnya
                            SET ov_up_07 = ((ov_up_07 - '" . $sum_bayar . "') + '" . floatval($res_invoice['total_bayar']) . "')
                          WHERE id_customer = '" . addslashes($res_invoice['id_customer']) . "'";
                } else if ($status_ar === 'ov_under_30') {
                  $sql4 = "UPDATE pro_customer_admin_arnya
                            SET ov_under_30 = ((ov_under_30 - '" . $sum_bayar . "') + '" . floatval($res_invoice['total_bayar']) . "')
                          WHERE id_customer = '" . addslashes($res_invoice['id_customer']) . "'";
                } else if ($status_ar === 'ov_under_60') {
                  $sql4 = "UPDATE pro_customer_admin_arnya
                            SET ov_under_60 = ((ov_under_60 - '" . $sum_bayar . "') + '" . floatval($res_invoice['total_bayar']) . "')
                          WHERE id_customer = '" . addslashes($res_invoice['id_customer']) . "'";
                } else if ($status_ar === 'ov_under_90') {
                  $sql4 = "UPDATE pro_customer_admin_arnya
                            SET ov_under_90 = ((ov_under_90 - '" . $sum_bayar . "') + '" . floatval($res_invoice['total_bayar']) . "')
                          WHERE id_customer = '" . addslashes($res_invoice['id_customer']) . "'";
                } else if ($status_ar === 'ov_up_90') {
                  $sql4 = "UPDATE pro_customer_admin_arnya
                            SET ov_up_90 = ((ov_up_90 - '" . $sum_bayar . "') + '" . floatval($res_invoice['total_bayar']) . "')
                          WHERE id_customer = '" . addslashes($res_invoice['id_customer']) . "'";
                } else {
                  $sql4 = null;
                }
                if ($sql4) {
                  $con->setQuery($sql4);
                  $oke  = $oke && !$con->hasError();
                }
  
                // detailDiscount
                $detailDiscount = as_array(val($value, 'detailDiscount'));
                if (!empty($detailDiscount)) {
                  foreach ($detailDiscount as $dd) {
                    $kategori = val(val($dd, 'account', []), 'name', '');
                    $nominal  = floatval(val($dd, 'amount', 0));
                    $insert_potongan = "
                    INSERT INTO pro_invoice_bukti_potong
                      (id_invoice, id_accurate_sales_receipt, kategori, nominal, created_at)
                    VALUES
                      ('" . addslashes($res_invoice['id_invoice']) . "',
                       '" . addslashes($salesReceiptId) . "',
                       '" . addslashes($kategori) . "',
                       '" . $nominal . "',
                       NOW())";
                    $con->setQuery($insert_potongan);
                    $oke  = $oke && !$con->hasError();
                  }
                }
              }
            }
          }
        } // end foreach detail_invoice_accurate
  
        // Sukses jalur WRITE
        if ($oke) {
          try {
            $con->commit();
          } catch (\Throwable $e) { /* ignore */
          }
          try {
            $con->close();
          } catch (\Throwable $e) { /* ignore */
          }
          echo json_encode("berhasil", JSON_UNESCAPED_UNICODE);
          exit;
        } else {
          respond_and_exit($con, "gagal", 500, true);
        }
      } elseif ($action === "DELETE") { // DELETE SALES RECEIPT
        $salesReceiptId = val($key2, 'salesReceiptId');
  
        $data_pembayaran = "SELECT * FROM pro_invoice_admin_detail_bayar WHERE id_accurate_sales_receipt ='" . addslashes($salesReceiptId) . "'";
        $res_pembayaran  = as_array($con->getResult($data_pembayaran));
  
        if (count($res_pembayaran) > 0) {
          foreach ($res_pembayaran as $ri) {
            $data_invoice = "SELECT * FROM pro_invoice_admin WHERE id_invoice ='" . addslashes($ri['id_invoice']) . "'";
            $res_invoice  = $con->getRecord($data_invoice);
            if (!$res_invoice) {
              continue;
            }
  
            $sisa_tagihan_awal = floatval(val($ri, 'jumlah_bayar', 0));
            $status_ar         = val($res_invoice, 'status_ar');
            $status_invoice    = 0; // fallback
  
            $update = 'UPDATE pro_invoice_admin
                          SET total_bayar = total_bayar - ' . $sisa_tagihan_awal . ',
                              is_lunas = "' . intval($status_invoice) . '"
                        WHERE id_invoice = "' . addslashes($ri['id_invoice']) . '"';
            $con->setQuery($update);
            $oke  = $oke && !$con->hasError();
  
            $update_incentive = 'UPDATE pro_incentive
                                    SET volume = 0, harga_dasar = 0, point_incentive = 0, tier = NULL, total_incentive = 0, disposisi = 0
                                  WHERE id_invoice = "' . addslashes($ri['id_invoice']) . '"';
            $con->setQuery($update_incentive);
            $oke  = $oke && !$con->hasError();
  
            $update_refund = "UPDATE pro_refund SET disposisi = 0 WHERE id_invoice = '" . addslashes($ri['id_invoice']) . "'";
            $con->setQuery($update_refund);
            $oke  = $oke && !$con->hasError();
  
            $del_pembayaran = "DELETE FROM pro_invoice_admin_detail_bayar
                                WHERE id_invoice ='" . addslashes($ri['id_invoice']) . "'
                                  AND id_accurate_sales_receipt ='" . addslashes($salesReceiptId) . "'";
            $con->setQuery($del_pembayaran);
            $oke  = $oke && !$con->hasError();
  
            $del_bupot = "DELETE FROM pro_invoice_bukti_potong
                           WHERE id_invoice ='" . addslashes($ri['id_invoice']) . "'
                             AND id_accurate_sales_receipt ='" . addslashes($salesReceiptId) . "'";
            $con->setQuery($del_bupot);
            $oke  = $oke && !$con->hasError();
  
            $delete_history_ar = "DELETE FROM pro_history_ar_customer
                                   WHERE id_invoice = '" . addslashes($ri['id_invoice']) . "'
                                     AND id_accurate_sales_receipt = '" . addslashes($salesReceiptId) . "'";
            $con->setQuery($delete_history_ar);
            $oke  = $oke && !$con->hasError();
  
            // Fokus kembalikan ke credit_limit_temp dulu
            $sisa_restore = floatval($sisa_tagihan_awal);
  
            // Ambil data customer
            $sql = "SELECT credit_limit_temp, credit_limit_used 
            FROM pro_customer 
            WHERE id_customer = '" . addslashes($res_invoice['id_customer']) . "'";
            $row = $con->getRecord($sql);
  
            $current_temp = floatval($row['credit_limit_temp']);
            $current_used = floatval($row['credit_limit_used']);
  
            // 1. Kembalikan ke credit_limit_temp dulu
            $restore_temp = min($sisa_restore, $sisa_restore); // no upper bound
            $sisa_restore -= $restore_temp;
  
            // 2. Sisanya ke used
            $restore_used = $sisa_restore;
  
            // 3. Update customer
            $update_cl = "UPDATE pro_customer SET
            credit_limit_temp = credit_limit_temp + " . floatval($restore_temp) . ",
            credit_limit_used = credit_limit_used + " . floatval($restore_used) . "
            WHERE id_customer = '" . addslashes($res_invoice['id_customer']) . "'";
            $con->setQuery($update_cl);
            $oke = $oke && !$con->hasError();
  
            // UPDATE AR bucket
            if ($status_ar === 'notyet') {
              $sql4 = "UPDATE pro_customer_admin_arnya
                          SET not_yet = not_yet + " . floatval($restore_used) . "
                        WHERE id_customer = " . intval($res_invoice['id_customer']);
            } elseif ($status_ar === 'ov_up_07') {
              $sql4 = "UPDATE pro_customer_admin_arnya
                          SET ov_up_07 = ov_up_07 + " . floatval($restore_used) . "
                        WHERE id_customer = " . intval($res_invoice['id_customer']);
            } elseif ($status_ar === 'ov_under_30') {
              $sql4 = "UPDATE pro_customer_admin_arnya
                          SET ov_under_30 = ov_under_30 + " . floatval($restore_used) . "
                        WHERE id_customer = " . intval($res_invoice['id_customer']);
            } elseif ($status_ar === 'ov_under_60') {
              $sql4 = "UPDATE pro_customer_admin_arnya
                          SET ov_under_60 = ov_under_60 + " . floatval($restore_used) . "
                        WHERE id_customer = " . intval($res_invoice['id_customer']);
            } elseif ($status_ar === 'ov_under_90') {
              $sql4 = "UPDATE pro_customer_admin_arnya
                          SET ov_under_90 = ov_under_90 + " . floatval($restore_used) . "
                        WHERE id_customer = " . intval($res_invoice['id_customer']);
            } elseif ($status_ar === 'ov_up_90') {
              $sql4 = "UPDATE pro_customer_admin_arnya
                          SET ov_up_90 = ov_up_90 + " . floatval($restore_used) . "
                        WHERE id_customer = " . intval($res_invoice['id_customer']);
            } else {
              $sql4 = null;
            }
            if ($sql4) {
              $con->setQuery($sql4);
              $oke  = $oke && !$con->hasError();
            }
          }
  
          if ($oke) {
            try {
              $con->commit();
            } catch (\Throwable $e) { /* ignore */
            }
            try {
              $con->close();
            } catch (\Throwable $e) { /* ignore */
            }
            echo json_encode("berhasil", JSON_UNESCAPED_UNICODE);
            exit;
          } else {
            respond_and_exit($con, "gagal", 500, true);
          }
        } else {
          // Tidak ada data untuk SR tersebut → anggap sukses idempotent
          try {
            $con->commit();
          } catch (\Throwable $e) { /* ignore */
          }
          try {
            $con->close();
          } catch (\Throwable $e) { /* ignore */
          }
          echo json_encode("berhasil", JSON_UNESCAPED_UNICODE);
          exit;
        }
      }
    }
  }else if($type == "CUSTOMER"){
    
  $dataList = as_array(val($key, 'data'));
  foreach ($dataList as $key2) {
    $action = val($key2, 'action');
    if ($action === "WRITE") { // ADD SALES RECEIPT
      $customerId = val($key2, 'customerId');
      $customerNo = val($key2, 'customerNo');
      

      $update = "UPDATE pro_customer
                SET kode_pelanggan = '" . $customerNo . "' WHERE id_accurate = '" .$customerId . "'";
      $con->setQuery($update);
      $oke = $oke && !$con->hasError();


      // Sukses jalur WRITE
      if ($oke) {
        try {
          $con->commit();
        } catch (\Throwable $e) { /* ignore */
        }
        try {
          $con->close();
        } catch (\Throwable $e) { /* ignore */
        }
        echo json_encode("berhasil", JSON_UNESCAPED_UNICODE);
        exit;
      } else {
        respond_and_exit($con, "gagal", 500, true);
      }
    } 
  }
  }

}

// Jika tidak ada aksi yang dieksekusi, tetap commit kosong
try {
  $con->commit();
} catch (\Throwable $e) { /* ignore */
}
try {
  $con->close();
} catch (\Throwable $e) { /* ignore */
}
echo json_encode("berhasil", JSON_UNESCAPED_UNICODE);

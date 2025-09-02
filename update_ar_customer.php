<?php
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "htmlawed");

$con = new Connection();
$oke = true;
$con->beginTransaction();
$con->clearError();

// $data_customer = "SELECT id_customer, kode_pelanggan, nama_customer, credit_limit, credit_limit_used, credit_limit_reserved FROM pro_customer WHERE kode_pelanggan IS NOT NULL AND id_customer = 65";
// $res_customer = $con->getResult($data_customer);

// foreach ($res_customer as $rc) {
//   $data_invoice = "SELECT id_invoice, id_customer, id_accurate, no_invoice, tgl_invoice, tgl_invoice_dikirim, total_invoice, total_bayar, status_ar FROM pro_invoice_admin WHERE id_customer = '" . $rc['id_customer'] . "' AND (is_lunas = 0 OR is_lunas IS NULL OR total_bayar = 0) AND tgl_invoice >= '2024-08-08' ORDER BY id_invoice DESC";
//   $res_invoice = $con->getResult($data_invoice);
//   // echo json_encode($res_invoice);
//   // exit();
// }

$today = DateTime::createFromFormat('Y-m-d', date('Y-m-d'));

$data_customer = "SELECT id_customer, top_payment FROM pro_customer WHERE kode_pelanggan IS NOT NULL AND id_customer = 4903";
$res_customer = $con->getResult($data_customer);

foreach ($res_customer as $rc) {
  $id_customer = $rc['id_customer'];

  $data_invoice = "SELECT id_invoice, tgl_invoice, total_invoice, total_bayar, tgl_invoice_dikirim FROM pro_invoice_admin WHERE id_customer = '$id_customer' AND (is_lunas = 0 OR is_lunas IS NULL OR total_bayar = 0) AND tgl_invoice >= '2024-08-08' AND tgl_invoice_dikirim IS NOT NULL";
  $res_invoice = $con->getResult($data_invoice);

  // Inisialisasi aging
  $not_yet = $ov_up_07 = $ov_under_30 = $ov_under_60 = $ov_under_90 = $ov_up_90 = 0;

  if (count($res_invoice) > 0) {

    foreach ($res_invoice as $inv) {
      $tgl_invoice_dikirim = new DateTime($inv['tgl_invoice_dikirim']);
      $topDays = (int) $rc['top_payment']; // pastikan bertipe int

      // Tambahkan TOP (misalnya 30 hari)
      $tgl_jatuh_tempo = clone $tgl_invoice_dikirim; // supaya tidak merusak original
      $tgl_jatuh_tempo->add(new DateInterval('P' . $topDays . 'D'));

      // Hitung selisih dari hari ini ke tanggal jatuh tempo
      $interval = $tgl_jatuh_tempo->diff($today);
      $selisih = (int) $interval->format('%a');

      // Hitung sisa tagihan
      $sisa_tagihan = (float)$inv['total_invoice'] - (float)$inv['total_bayar'];
      $status_ar = "";

      // echo json_encode($selisih);
      // Jika tgl_invoice di masa depan, skip aging
      if ($interval->invert === 1) {
        // Invoice belum jatuh tempo, bisa abaikan atau masuk ke kategori khusus
        $status_ar = 'notyet';
        $not_yet += $sisa_tagihan;
      } else {
        // Aging seperti biasa
        if ($selisih <= 7) {
          $not_yet += $sisa_tagihan;
          $status_ar = 'notyet';
        } elseif ($selisih <= 30) {
          $ov_up_07 += $sisa_tagihan;
          $status_ar = 'ov_up_07';
        } elseif ($selisih <= 60) {
          $ov_under_30 += $sisa_tagihan;
          $status_ar = 'ov_under_30';
        } elseif ($selisih <= 90) {
          $ov_under_60 += $sisa_tagihan;
          $status_ar = 'ov_under_60';
        } elseif ($selisih <= 120) {
          $ov_under_90 += $sisa_tagihan;
          $status_ar = 'ov_under_90';
        } else {
          $ov_up_90 += $sisa_tagihan;
          $status_ar = 'ov_up_90';
        }
      }

      // Update status_ar per invoice
      $id_invoice = $inv['id_invoice'];
      $update_status = "UPDATE pro_invoice_admin SET status_ar = '$status_ar' WHERE id_invoice = '$id_invoice'";
      $con->setQuery($update_status);
      $oke  = $oke && !$con->hasError();
    }

    // Update aging summary ke pro_customer_admin_arnya
    $cek = "SELECT id_arnya FROM pro_customer_admin_arnya WHERE id_customer = '$id_customer'";
    $res_cek = $con->getRecord($cek);
    if ($res_cek) {
      $update_summary = "UPDATE pro_customer_admin_arnya SET 
      not_yet = '$not_yet',
      ov_up_07 = '$ov_up_07',
      ov_under_30 = '$ov_under_30',
      ov_under_60 = '$ov_under_60',
      ov_under_90 = '$ov_under_90',
      ov_up_90 = '$ov_up_90'
      WHERE id_customer = '$id_customer'";
      $con->setQuery($update_summary);
      $oke  = $oke && !$con->hasError();
    }

    $msg = "Update AR berhasil";
  } else {
    $msg = "Tidak ada data update";
  }
}

if ($oke) {
  $con->commit();
  echo $msg;
} else {
  $con->rollback();
  echo "Update AR gagal. Transaksi dibatalkan.";
}

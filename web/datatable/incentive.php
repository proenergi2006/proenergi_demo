<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth    = new MyOtentikasi();
$con     = new Connection();
$draw     = isset($_POST["element"]) ? htmlspecialchars($_POST["element"], ENT_QUOTES) : 0;
$start     = isset($_POST["start"]) ? htmlspecialchars($_POST["start"], ENT_QUOTES) : 0;
$length    = isset($_POST['length']) ? htmlspecialchars($_POST["length"], ENT_QUOTES) : 10;
$q1    = isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';
$q2    = isset($_POST["q2"]) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : '';
$q3    = isset($_POST["q3"]) ? htmlspecialchars($_POST["q3"], ENT_QUOTES) : '';
$cabang    = isset($_POST["cabang"]) ? htmlspecialchars($_POST["cabang"], ENT_QUOTES) : '';
$id_role = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$id_user = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);
$id_wilayah = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

$arrTermPayment = array("CREDIT" => "CREDIT", "CBD" => "CBD (Cash Before Delivery)", "COD" => "COD (Cash On Delivery)");

if ($cabang) {
    $wilayah = " AND i.id_wilayah = '" . $cabang . "'";
} else {
    $wilayah = "";
}

if ($id_role == '23' ||  $id_role ==  '21') {
    $user = "";
} else {
    $user = " AND j.id_user = '" . $id_user . "'";
}

if ($q3 != "") {
    $status = " AND a.disposisi = '" . $q3 . "'";
} else {
    $status = "";
}

if ($q1) {
    $keywords = " AND (upper(j.fullname) like '%" . strtoupper($q1) . "%' or upper(i.nama_customer) like '%" . strtoupper($q1) . "%' or upper(n.no_invoice) like '%" . strtoupper($q1) . "%')";
} else {
    $keywords = "";
}

$p = new paging;
$sql = "SELECT DISTINCT 
    a.id AS id_incentivenya, 
    a.id_dsd AS id_dsdnya, 
    a.id_invoice AS id_invoicenya, 
    a.total_incentive, 
    a.disposisi AS statusnya, 
    a.point_incentive, 
    i.nama_customer, 
    i.kode_pelanggan, 
    i.jenis_payment, 
    i.top_payment, 
    i.id_customer AS id_customernya, 
    e.alamat_survey, 
    f.nama_prov, 
    g.nama_kab, 
    j.fullname, 
    j.id_role, 
    j.id_user, 
    h.nomor_poc, 
    h.tanggal_poc, 
    h.id_poc AS id_pocnya, 
    h.produk_poc, 
    b.volume_po, 
    k.refund_tawar, 
    l.nama_area, 
    l.id_master AS id_areanya, 
    m.wilayah_angkut, 
    k.id_penawaran, 
    k.harga_asli AS harga_dasarnya, 
    k.harga_tier, 
    k.tier, 
    ppdd.tanggal_delivered, 
    n.no_invoice, 
    n.tgl_invoice_dikirim, 
    n.tgl_invoice, 
    k.masa_awal, 
    k.masa_akhir, 
    CONCAT(o.jenis_produk, ' - ', o.merk_dagang) AS nama_produk, 
    (SELECT SUM(vol_kirim) FROM pro_invoice_admin_detail WHERE id_invoice = a.id_invoice) AS volume_invoice, 
    n.is_lunas, 
    q.nama_cabang
FROM 
    pro_incentive a
JOIN 
    pro_po_ds_detail ppdd ON ppdd.id_dsd = a.id_dsd
JOIN 
    pro_po_detail b ON ppdd.id_pod = b.id_pod
JOIN 
    pro_pr_detail c ON ppdd.id_prd = c.id_prd
JOIN 
    pro_po_customer_plan d ON ppdd.id_plan = d.id_plan
JOIN 
    pro_customer_lcr e ON d.id_lcr = e.id_lcr
JOIN 
    pro_master_provinsi f ON e.prov_survey = f.id_prov
JOIN 
    pro_master_kabupaten g ON e.kab_survey = g.id_kab
JOIN 
    pro_po_customer h ON d.id_poc = h.id_poc
JOIN 
    pro_customer i ON h.id_customer = i.id_customer
JOIN 
    acl_user j ON a.id_marketing = j.id_user
JOIN 
    pro_penawaran k ON h.id_penawaran = k.id_penawaran
JOIN 
    pro_master_area l ON k.id_area = l.id_master
JOIN 
    pro_master_wilayah_angkut m ON e.id_wil_oa = m.id_master 
    AND e.prov_survey = m.id_prov 
    AND e.kab_survey = m.id_kab
JOIN 
    pro_invoice_admin n ON a.id_invoice = n.id_invoice
JOIN 
    pro_master_produk o ON o.id_master = h.produk_poc
JOIN 
    pro_invoice_admin_detail p ON a.id_invoice = p.id_invoice AND p.id_dsd=ppdd.id_dsd AND p.jenisnya='truck'
JOIN 
    pro_master_cabang q ON q.id_master = i.id_wilayah
WHERE  1 = 1
	" . $wilayah . "
	" . $user . "
	" . $status . "
	" . $keywords . "
UNION ALL
SELECT DISTINCT 
    a.id AS id_incentivenya, 
    a.id_dsd AS id_dsdnya, 
    a.id_invoice AS id_invoicenya, 
    a.total_incentive, 
    a.disposisi AS statusnya,
	a.point_incentive,  
    i.nama_customer, 
    i.kode_pelanggan, 
    i.jenis_payment, 
    i.top_payment, 
    i.id_customer AS id_customernya, 
    e.alamat_survey, 
    f.nama_prov, 
    g.nama_kab, 
    j.fullname, 
    j.id_role, 
	j.id_user,
    h.nomor_poc, 
    h.tanggal_poc, 
    h.id_poc AS id_pocnya, 
    h.produk_poc, 
	NULL AS volume_po, 
    k.refund_tawar, 
    l.nama_area, 
    l.id_master AS id_areanya, 
    m.wilayah_angkut, 
    k.id_penawaran, 
    k.harga_asli AS harga_dasarnya, 
	k.harga_tier, 
    k.tier, 
    ppdd.tanggal_delivered, 
    n.no_invoice, 
    n.tgl_invoice_dikirim, 
    n.tgl_invoice, 
    k.masa_awal, 
    k.masa_akhir, 
    CONCAT(o.jenis_produk, ' - ', o.merk_dagang) AS nama_produk, 
    (SELECT SUM(vol_kirim) FROM pro_invoice_admin_detail WHERE id_invoice = a.id_invoice) AS volume_invoice, 
    n.is_lunas, 
    q.nama_cabang
FROM 
    pro_incentive a
JOIN 
    pro_po_ds_kapal ppdd ON ppdd.id_dsk = a.id_dsd
JOIN 
    pro_pr_detail c ON ppdd.id_prd = c.id_prd
JOIN 
    pro_po_customer_plan d ON ppdd.id_plan = d.id_plan
JOIN 
    pro_customer_lcr e ON d.id_lcr = e.id_lcr
JOIN 
    pro_master_provinsi f ON e.prov_survey = f.id_prov
JOIN 
    pro_master_kabupaten g ON e.kab_survey = g.id_kab
JOIN 
    pro_po_customer h ON d.id_poc = h.id_poc
JOIN 
    pro_customer i ON h.id_customer = i.id_customer
JOIN 
    acl_user j ON a.id_marketing = j.id_user
JOIN 
    pro_penawaran k ON h.id_penawaran = k.id_penawaran
JOIN 
    pro_master_area l ON k.id_area = l.id_master
JOIN 
    pro_master_wilayah_angkut m ON e.id_wil_oa = m.id_master 
    AND e.prov_survey = m.id_prov 
    AND e.kab_survey = m.id_kab
JOIN 
    pro_invoice_admin n ON a.id_invoice = n.id_invoice
JOIN 
    pro_master_produk o ON o.id_master = h.produk_poc
JOIN 
    pro_invoice_admin_detail p ON a.id_invoice = p.id_invoice AND p.id_dsd=ppdd.id_dsk AND p.jenisnya='kapal'
JOIN 
    pro_master_cabang q ON q.id_master = i.id_wilayah
WHERE  1 = 1
	" . $wilayah . "
	" . $user . "
	" . $status . "
	" . $keywords . "";


$tot_record = $con->num_rows($sql);
$tot_page     = ceil($tot_record / $length);
$page        = ($start > $tot_page) ? $start - 1 : $start;
$position     = $p->findPosition($length, $tot_record, $page);
$sql .= " ORDER BY 1 DESC limit " . $position . ", " . $length;

$content = "";
$count = 0;
if ($tot_record <= 0) {
    $content .= '<tr><td colspan="12" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
    $count         = $position;
    $tot_page     = ceil($tot_record / $length);
    $result     = $con->getResult($sql);
    $total_incentif = 0;

    $cek_top = "SELECT * FROM pro_top_incentive ORDER BY id ASC";
    $res_top = $con->getResult($cek_top);
    $tr_top = "";

    foreach ($result as $data) {
        $count++;

        $cek_oc = "SELECT * FROM pro_other_cost_detail where id_penawaran = '" . $data['id_penawaran'] . "'";
        $res_oc = $con->getResult($cek_oc);

        $cek_non_penerima = "SELECT id_user FROM pro_non_penerima_incentive WHERE id_user = '" . $data['id_user'] . "'";
        $res_non_penerima = $con->getRecord($cek_non_penerima);

        if (count($res_oc) > 0) {
            $ket_oc = "<i>(Harga Dasar - Other Cost - Refund)</i>";
        } else {
            $ket_oc = "";
        }

        if ($arrTermPayment[$data['jenis_payment']] == "CREDIT") {
            $top_payment = $data['top_payment'];
        } else {
            $top_payment = "-";
        }

        if ($data["harga_tier"] == 0) {
            $tiernya = "Harga Tier 0";
            $harganya = "";
        } else {
            $tiernya = "Tier " . $data['tier'];
            $harganya = number_format($data["harga_tier"]);
        }

        $idp         = $data["id_dsdnya"];
        $linkList     = paramEncrypt($data['id_dsd'] . "|#|1");
        $tempal     = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
        $alamat        = $data['alamat_survey'] . " " . ucwords($tempal) . " " . $data['nama_prov'];
        $datenow = date("Y-m-d");
        $linkDetail    = BASE_URL_CLIENT . '/detail_incentif.php?' . paramEncrypt('id_dsd=' . $idp . '&id_penawaran=' . $data['id_penawaran']);
        $tgl_invoice = $data['tgl_invoice'];
        $nomor_invoice = $data['no_invoice'];
        $statusnya = $data['statusnya'];

        if ($data['id_role'] == 7) {
            $role = "Branch Manager";
        } elseif ($data['id_role'] == 11) {
            $role = "Marketing";
        } elseif ($data['id_role'] == 17) {
            $role = "Key Account Executive";
        } elseif ($data['id_role'] == 20) {
            $role = "SPV Marketing";
        }

        $sql_1 = "SELECT * FROM pro_invoice_admin WHERE id_invoice = '" . $data['id_invoicenya'] . "'";
        $row_1 = $con->getRecord($sql_1);
        if (($row_1['total_invoice'] == $row_1['total_bayar']) || $row_1['is_lunas'] == '1') {
            $sql_bayar_1 = "SELECT MAX(tgl_bayar) as tanggal_bayar FROM pro_invoice_admin_detail_bayar WHERE id_invoice='" . $data['id_invoicenya'] . "'";
            $row_bayar_1 = $con->getRecord($sql_bayar_1);
        }

        if ($data['tgl_invoice_dikirim'] == NULL) {
            $tgl_invoice_dikirim = "-";
        } else {
            $tgl_invoice_dikirim = tgl_indo($data['tgl_invoice_dikirim']);
        }

        $due_date_indo = tgl_indo(date('Y-m-d', strtotime($data['tgl_invoice_dikirim'] . "+" . $data['top_payment'] . " days")));
        $due_date = date('Y-m-d', strtotime($data['tgl_invoice_dikirim'] . "+" . $data['top_payment'] . " days"));

        $week1 = 0;
        $week2 = 0;
        $week3 = 0;
        $week4 = 0;
        $week5 = 0;
        $week6 = 0;
        $total_incentive_fix = 0;

        if ($data['is_lunas'] == 1) {
            if ($data['statusnya'] == 0) {
                $date_payment = "-";
                $status_payment = "<div class='badge badge-warning'>UNPAID</div>";
                $daysDiff = "-";
            } else {
                $date_payment = tgl_indo($row_bayar_1['tanggal_bayar']);

                $startDate = new DateTime($data['tgl_invoice_dikirim']);
                $endDate = new DateTime($row_bayar_1['tanggal_bayar']);

                // Menghitung selisih
                $interval = $startDate->diff($endDate);
                $daysDiff = $interval->days; // Selisih hari

                $status_payment = "<div class='badge badge-success'>PAID</div>";
            }

            foreach ($res_top as $rt) {
                if ($rt['top'] == "0") {
                    $term = "CBD";
                    $keterangan1 = $rt['keterangan'];
                    $top = 0;
                } elseif ($rt['top'] == "14") {
                    $term = "14";
                    $keterangan2 = $rt['keterangan'];
                    $top1 = 14;
                } elseif ($rt['top'] == "35") {
                    $term = "35";
                    $keterangan3 = $rt['keterangan'];
                    $top2 = 21;
                } elseif ($rt['top'] == "54") {
                    $term = "54";
                    $keterangan4 = $rt['keterangan'];
                    $top3 = 19;
                } elseif ($rt['top'] == "75") {
                    $term = "75";
                    $keterangan5 = $rt['keterangan'];
                    $top4 = 21;
                } elseif ($rt['top'] == "76") {
                    $term = "76";
                    $keterangan6 = $rt['keterangan'];
                    $top5 = 1;
                }
            }

            $due_date_week2 = date('Y-m-d', strtotime($data['tgl_invoice_dikirim'] . "+" . $top1 . " days"));
            $due_date_week3 = date('Y-m-d', strtotime($due_date_week2 . "+" . $top2 . " days"));
            $due_date_week4 = date('Y-m-d', strtotime($due_date_week3 . "+" . $top3 . " days"));
            $due_date_week5 = date('Y-m-d', strtotime($due_date_week4 . "+" . $top4 . " days"));
            $due_date_week6 = date('Y-m-d', strtotime($due_date_week5 . "+" . $top5 . " days"));

            if ($data['jenis_payment'] == "CBD") {

                if ($res_non_penerima) {
                    $point = 0;
                } else {
                    if ($tiernya == "Harga Tier 0") {
                        $point = 0;
                    } else {
                        if ($statusnya == 0) {
                            $cek_point = "SELECT * FROM pro_point_incentive WHERE id_role='" . $data['id_role'] . "' AND id_top ='1' AND tier='" . $tiernya . "'";
                            $res_point = $con->getRecord($cek_point);
                            $point = $res_point['point'];
                        } else {
                            $point = $data['point_incentive'];
                        }
                    }
                }

                $week1 += $data['volume_invoice'] * $point;
                $week2 += 0;
                $week3 += 0;
                $week4 += 0;
                $week5 += 0;
                $week6 += 0;
                $total_incentive_fix = $week1;
            } else {
                if ($row_bayar_1['tanggal_bayar'] <= $due_date_week2) {

                    if ($res_non_penerima) {
                        $point = 0;
                    } else {
                        if ($tiernya == "Harga Tier 0") {
                            $point = 0;
                        } else {
                            if ($statusnya == 0) {
                                $cek_point = "SELECT * FROM pro_point_incentive WHERE id_role='" . $data['id_role'] . "' AND id_top ='2' AND tier='" . $tiernya . "'";
                                $res_point = $con->getRecord($cek_point);
                                $point = $res_point['point'];
                            } else {
                                $point = $data['point_incentive'];
                            }
                        }
                    }

                    $week1 += 0;
                    $week2 += $data['volume_invoice'] * $point;
                    $week3 += 0;
                    $week4 += 0;
                    $week5 += 0;
                    $week6 += 0;
                    $total_incentive_fix = $week2;
                } elseif ($row_bayar_1['tanggal_bayar'] > $due_date_week2 && $row_bayar_1['tanggal_bayar'] <= $due_date_week3) {

                    if ($res_non_penerima) {
                        $point = 0;
                    } else {
                        if ($tiernya == "Harga Tier 0") {
                            $point = 0;
                        } else {
                            if ($statusnya == 0) {
                                $cek_point = "SELECT * FROM pro_point_incentive WHERE id_role='" . $data['id_role'] . "' AND id_top ='3' AND tier='" . $tiernya . "'";
                                $res_point = $con->getRecord($cek_point);
                                $point = $res_point['point'];
                            } else {
                                $point = $data['point_incentive'];
                            }
                        }
                    }

                    $week1 += 0;
                    $week2 += 0;
                    $week3 += $data['volume_invoice'] * $point;
                    $week4 += 0;
                    $week5 += 0;
                    $week6 += 0;
                    $total_incentive_fix = $week3;
                } elseif ($row_bayar_1['tanggal_bayar'] > $due_date_week3 && $row_bayar_1['tanggal_bayar'] <= $due_date_week4) {

                    if ($res_non_penerima) {
                        $point = 0;
                    } else {
                        if ($tiernya == "Harga Tier 0") {
                            $point = 0;
                        } else {
                            if ($statusnya == 0) {
                                $cek_point = "SELECT * FROM pro_point_incentive WHERE id_role='" . $data['id_role'] . "' AND id_top ='4' AND tier='" . $tiernya . "'";
                                $res_point = $con->getRecord($cek_point);
                                $point = $res_point['point'];
                            } else {
                                $point = $data['point_incentive'];
                            }
                        }
                    }

                    $week1 += 0;
                    $week2 += 0;
                    $week3 += 0;
                    $week4 += $data['volume_invoice'] * $point;
                    $week5 += 0;
                    $week6 += 0;
                    $total_incentive_fix = $week4;
                } elseif ($row_bayar_1['tanggal_bayar'] > $due_date_week4 && $row_bayar_1['tanggal_bayar'] <= $due_date_week5) {

                    if ($res_non_penerima) {
                        $point = 0;
                    } else {
                        if ($tiernya == "Harga Tier 0") {
                            $point = 0;
                        } else {
                            if ($statusnya == 0) {
                                $cek_point = "SELECT * FROM pro_point_incentive WHERE id_role='" . $data['id_role'] . "' AND id_top ='5' AND tier='" . $tiernya . "'";
                                $res_point = $con->getRecord($cek_point);
                                $point = $res_point['point'];
                            } else {
                                $point = $data['point_incentive'];
                            }
                        }
                    }

                    $week1 += 0;
                    $week2 += 0;
                    $week3 += 0;
                    $week4 += 0;
                    $week5 += $data['volume_invoice'] * $point;
                    $week6 += 0;
                    $total_incentive_fix = $week5;
                } elseif ($row_bayar_1['tanggal_bayar'] >= $due_date_week6) {
                    $point = 0;

                    $week1 += 0;
                    $week2 += 0;
                    $week3 += 0;
                    $week4 += 0;
                    $week5 += 0;
                    $week6 += 0;
                    $total_incentive_fix = $week6;
                }
            }

            $tr_top = "
				<tr>
					<td width='60%'>
						" . $keterangan1 . " days
					</td>
					<td width='2%'>:</td>
					<td align='right'>
						" . number_format($week1) . "
					</td>
				</tr>
				<tr>
					<td>
						" . $keterangan2 . ' days | ' . tgl_indo($due_date_week2) . "
					</td>
					<td>:</td>
					<td align='right'>
						" . number_format($week2) . "
					</td>
				</tr>
				<tr>
					<td>
						" . $keterangan3 . ' days | ' . tgl_indo($due_date_week3) . "
					</td>
					<td>:</td>
					<td align='right'>
						" . number_format($week3) . "
					</td>
				</tr>
				<tr>
					<td>
						" . $keterangan4 . ' days | ' . tgl_indo($due_date_week4) . "
					</td>
					<td>:</td>
					<td align='right'>
						" . number_format($week4) . "
					</td>
				</tr>
				<tr>
					<td>
						" . $keterangan5 . ' days | ' . tgl_indo($due_date_week5) . "
					</td>
					<td>:</td>
					<td align='right'>
						" . number_format($week5) . "
					</td>
				</tr>
				<tr>
					<td>
						" . $keterangan6 . ' days | ' . tgl_indo($due_date_week6) . "
					</td>
					<td>:</td>
					<td align='right'>
						" . number_format($week6) . "
					</td>
				</tr>";
        } else {
            $date_payment = "-";
            $status_payment = "<div class='badge badge-warning'>UNPAID</div>";
            $daysDiff = "-";

            foreach ($res_top as $rt) {
                if ($rt['top'] == "0") {
                    $term = "CBD";
                    $keterangan1 = $rt['keterangan'];
                    $top = 0;
                } elseif ($rt['top'] == "14") {
                    $term = "14";
                    $keterangan2 = $rt['keterangan'];
                    $top1 = 14;
                } elseif ($rt['top'] == "35") {
                    $term = "35";
                    $keterangan3 = $rt['keterangan'];
                    $top2 = 21;
                } elseif ($rt['top'] == "54") {
                    $term = "54";
                    $keterangan4 = $rt['keterangan'];
                    $top3 = 19;
                } elseif ($rt['top'] == "75") {
                    $term = "75";
                    $keterangan5 = $rt['keterangan'];
                    $top4 = 21;
                } elseif ($rt['top'] == "76") {
                    $term = "76";
                    $keterangan6 = $rt['keterangan'];
                    $top5 = 1;
                }
            }

            $due_date_week2 = date('Y-m-d', strtotime($data['tgl_invoice_dikirim'] . "+" . $top1 . " days"));
            $due_date_week3 = date('Y-m-d', strtotime($due_date_week2 . "+" . $top2 . " days"));
            $due_date_week4 = date('Y-m-d', strtotime($due_date_week3 . "+" . $top3 . " days"));
            $due_date_week5 = date('Y-m-d', strtotime($due_date_week4 . "+" . $top4 . " days"));
            $due_date_week6 = date('Y-m-d', strtotime($due_date_week5 . "+" . $top5 . " days"));

            if ($data['jenis_payment'] == "CBD") {

                if ($res_non_penerima) {
                    $point = 0;
                } else {
                    if ($tiernya == "Harga Tier 0") {
                        $point = 0;
                    } else {
                        if ($statusnya == 0) {
                            $cek_point = "SELECT * FROM pro_point_incentive WHERE id_role='" . $data['id_role'] . "' AND id_top ='1' AND tier='" . $tiernya . "'";
                            $res_point = $con->getRecord($cek_point);
                            $point = $res_point['point'];
                        } else {
                            $point = $data['point_incentive'];
                        }
                    }
                }

                $week1 += $data['volume_invoice'] * $point;
                $week2 += 0;
                $week3 += 0;
                $week4 += 0;
                $week5 += 0;
                $week6 += 0;
                $total_incentive_fix = $week1;
            } else {
                if ($datenow <= $due_date_week2) {

                    if ($res_non_penerima) {
                        $point = 0;
                    } else {
                        if ($tiernya == "Harga SM masih 0" || $tiernya == "Harga OM masih 0" || $tiernya == "Harga COO masih 0" || $tiernya == "Harga CEO masih 0") {
                            $point = 0;
                        } else {
                            if ($tiernya == "Tier III Dasar") {
                                $cek_point = "SELECT * FROM pro_point_incentive WHERE id_role='" . $data['id_role'] . "' AND id_top ='5' AND tier='Tier III'";
                                $res_point = $con->getRecord($cek_point);
                                $point = $res_point['point'];
                            } else {
                                if ($statusnya == 0) {
                                    $cek_point = "SELECT * FROM pro_point_incentive WHERE id_role='" . $data['id_role'] . "' AND id_top ='2' AND tier='" . $tiernya . "'";
                                    $res_point = $con->getRecord($cek_point);
                                    $point = $res_point['point'];
                                } else {
                                    $point = $data['point_incentive'];
                                }
                            }
                        }
                    }

                    $week1 += 0;
                    $week2 += $data['volume_invoice'] * $point;
                    $week3 += 0;
                    $week4 += 0;
                    $week5 += 0;
                    $week6 += 0;
                    $total_incentive_fix = $week2;
                } elseif ($datenow > $due_date_week2 && $datenow <= $due_date_week3) {

                    if ($res_non_penerima) {
                        $point = 0;
                    } else {
                        if ($tiernya == "Harga SM masih 0" || $tiernya == "Harga OM masih 0" || $tiernya == "Harga COO masih 0" || $tiernya == "Harga CEO masih 0") {
                            $point = 0;
                        } else {
                            if ($tiernya == "Tier III Dasar") {
                                $cek_point = "SELECT * FROM pro_point_incentive WHERE id_role='" . $data['id_role'] . "' AND id_top ='5' AND tier='Tier III'";
                                $res_point = $con->getRecord($cek_point);
                                $point = $res_point['point'];
                            } else {
                                if ($statusnya == 0) {
                                    $cek_point = "SELECT * FROM pro_point_incentive WHERE id_role='" . $data['id_role'] . "' AND id_top ='3' AND tier='" . $tiernya . "'";
                                    $res_point = $con->getRecord($cek_point);
                                    $point = $res_point['point'];
                                } else {
                                    $point = $data['point_incentive'];
                                }
                            }
                        }
                    }

                    $week1 += 0;
                    $week2 += 0;
                    $week3 += $data['volume_invoice'] * $point;
                    $week4 += 0;
                    $week5 += 0;
                    $week6 += 0;
                    $total_incentive_fix = $week3;
                } elseif ($datenow > $due_date_week3 && $datenow <= $due_date_week4) {

                    if ($res_non_penerima) {
                        $point = 0;
                    } else {
                        if ($tiernya == "Harga SM masih 0" || $tiernya == "Harga OM masih 0" || $tiernya == "Harga COO masih 0" || $tiernya == "Harga CEO masih 0") {
                            $point = 0;
                        } else {
                            if ($tiernya == "Tier III Dasar") {
                                $cek_point = "SELECT * FROM pro_point_incentive WHERE id_role='" . $data['id_role'] . "' AND id_top ='5' AND tier='Tier III'";
                                $res_point = $con->getRecord($cek_point);
                                $point = $res_point['point'];
                            } else {
                                if ($statusnya == 0) {
                                    $cek_point = "SELECT * FROM pro_point_incentive WHERE id_role='" . $data['id_role'] . "' AND id_top ='4' AND tier='" . $tiernya . "'";
                                    $res_point = $con->getRecord($cek_point);
                                    $point = $res_point['point'];
                                } else {
                                    $point = $data['point_incentive'];
                                }
                            }
                        }
                    }

                    $week1 += 0;
                    $week2 += 0;
                    $week3 += 0;
                    $week4 += $data['volume_invoice'] * $point;
                    $week5 += 0;
                    $week6 += 0;
                    $total_incentive_fix = $week4;
                } elseif ($datenow > $due_date_week4 && $datenow <= $due_date_week5) {

                    if ($res_non_penerima) {
                        $point = 0;
                    } else {
                        if ($tiernya == "Harga SM masih 0" || $tiernya == "Harga OM masih 0" || $tiernya == "Harga COO masih 0" || $tiernya == "Harga CEO masih 0") {
                            $point = 0;
                        } else {
                            if ($tiernya == "Tier III Dasar") {
                                $cek_point = "SELECT * FROM pro_point_incentive WHERE id_role='" . $data['id_role'] . "' AND id_top ='5' AND tier='Tier III'";
                                $res_point = $con->getRecord($cek_point);
                                $point = $res_point['point'];
                            } else {
                                if ($statusnya == 0) {
                                    $cek_point = "SELECT * FROM pro_point_incentive WHERE id_role='" . $data['id_role'] . "' AND id_top ='5' AND tier='" . $tiernya . "'";
                                    $res_point = $con->getRecord($cek_point);
                                    $point = $res_point['point'];
                                } else {
                                    $point = $data['point_incentive'];
                                }
                            }
                        }
                    }

                    $week1 += 0;
                    $week2 += 0;
                    $week3 += 0;
                    $week4 += 0;
                    $week5 += $data['volume_invoice'] * $point;
                    $week6 += 0;
                    $total_incentive_fix = $week5;
                } elseif ($datenow >= $due_date_week6) {

                    $point = 0;

                    $week1 += 0;
                    $week2 += 0;
                    $week3 += 0;
                    $week4 += 0;
                    $week5 += 0;
                    $week6 += 0;
                    $total_incentive_fix = $week6;
                }
            }

            $tr_top = "
				<tr>
					<td width='60%'>
						" . $keterangan1 . " days
					</td>
					<td width='2%'>:</td>
					<td align='right'>
						" . number_format($week1) . "
					</td>
				</tr>
				<tr>
					<td>
						" . $keterangan2 . ' days | ' . tgl_indo($due_date_week2) . "
					</td>
					<td>:</td>
					<td align='right'>
						" . number_format($week2) . "
					</td>
				</tr>
				<tr>
					<td>
						" . $keterangan3 . ' days | ' . tgl_indo($due_date_week3) . "
					</td>
					<td>:</td>
					<td align='right'>
						" . number_format($week3) . "
					</td>
				</tr>
				<tr>
					<td>
						" . $keterangan4 . ' days | ' . tgl_indo($due_date_week4) . "
					</td>
					<td>:</td>
					<td align='right'>
						" . number_format($week4) . "
					</td>
				</tr>
				<tr>
					<td>
						" . $keterangan5 . ' days | ' . tgl_indo($due_date_week5) . "
					</td>
					<td>:</td>
					<td align='right'>
						" . number_format($week5) . "
					</td>
				</tr>
				<tr>
					<td>
						" . $keterangan6 . ' days | ' . tgl_indo($due_date_week6) . "
					</td>
					<td>:</td>
					<td align='right'>
						" . number_format($week6) . "
					</td>
				</tr>";
        }

        if ($data['statusnya'] == 0) {
            $status = "<div class='badge badge-warning'>INVOICE UNPAID</div>";
        } elseif ($data['statusnya'] == 1) {
            $status = "<div class='badge badge-success'>INVOICE PAID</div>";
        } elseif ($data['statusnya'] == 2) {
            $status = "<div class='badge badge-secondary'>PROSES PENGAJUAN PERSETUJUAN CEO</div>";
        } elseif ($data['statusnya'] == 3) {
            $status = "<div class='badge badge-info'>APPROVED BY CEO</div>";
        }

        if ($point == 0) {
            $status_point = "<div class='badge badge-error'>POINT 0 TIDAK DAPAT INCENTIVE</div>";
        } else {
            $status_point = "";
        }


        $content .= '
				<tr ' . $background . '>
					<td class="text-center">
					' . $count . '
					</td>
					<td class="text-center">
						<p style="margin-bottom:0px">' . $nomor_invoice . '</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>' . $data['kode_pelanggan'] . '</b></p>
						<p style="margin-bottom:0px">' . $data['nama_customer'] . '</p>
						<p style="margin-bottom:0px">' . $alamat . '</p>
						<br>
						<p style="margin-bottom:0px"> Cabang : ' . $data['nama_cabang'] . '</p>
						<p style="margin-bottom:0px"> Area Penawaran : ' . $data['nama_area'] . '</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>' . strtoupper($data['fullname']) . '</b></p>
						<p style="margin-bottom:0px"><i>' . $role . '</i></p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>' . $data['nomor_poc'] . '</b></p>
						<p style="margin-bottom:0px">' . date("d/m/Y H:i", strtotime($data['tanggal_delivered'])) . '</p>
						<p style="margin-bottom:0px">' . number_format($data['volume_invoice']) . ' Liter</p>
						<br>
						<p style="margin-bottom:0px">' . $data['nama_produk'] . '</p>
					</td>
					<td class="text-center">
					<p style="margin-bottom:0px"><b>Rp. ' . number_format($data['harga_dasarnya']) . '</b></p>
					</td>
					<td class="text-center">
						<p style="margin-bottom:0px"><b>Rp. ' . number_format($data['harga_tier']) . '</b></p>
						' . $ket_oc . '
					</td>
					<td class="text-center">
						<p style="margin-bottom:0px"><b>' . $tiernya . '</b></p>
					</td>
					<td class="text-left">
						<b>Invoice Date : ' . tgl_indo($tgl_invoice) . '</b>
						<br><br>
						<p>Invoice Send : ' . $tgl_invoice_dikirim . '</p>
						<p>Payment : ' . $arrTermPayment[$data['jenis_payment']] . '</p>
						<p>TOP : ' . $top_payment . '</p>
						<p>Invoice Payment : ' . $date_payment . '</p>
						<p>Total Days : ' . $daysDiff . '</p>
						' . $status_payment . '
					</td>
					<td class="text-center">
						<p style="margin-bottom:0px"><b>' . $point . '</b></p>
					</td>
					<td class="text-left">
						<table width="100%" border="0">
							' . $tr_top . '
						</table>
						<br>
						' . $status_point . '
					</td>
					<td class="text-right">
					' . number_format($total_incentive_fix) . '
					</td>
					<td class="text-center">
					' . $status . '
					</td>
				</tr>';
        $grandtotal_incentive += $total_incentive_fix;
    }
    $content .= '
	<tr>
		<td class="text-center" colspan="10"><b>TOTAL</b></td>
		<td class="text-right">' . number_format($grandtotal_incentive) . '</td>
		</td>
	</tr>';
}

$json_data = array(
    "items"        => $content,
    "pages"        => $tot_page,
    "page"        => $page,
    "totalData"    => $tot_record,
    "infoData"    => "Showing " . ($position + 1) . " to " . $count . " of " . $tot_record . " entries",
);
echo json_encode($json_data);

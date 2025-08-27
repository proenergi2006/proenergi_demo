<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "pdfgen");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;
$enk  	= decode($_SERVER['REQUEST_URI']);
$id_dsd 	= isset($enk["id_dsd"]) ? htmlspecialchars($enk["id_dsd"], ENT_QUOTES) : '';
$status 	= isset($enk["status"]) ? htmlspecialchars($enk["status"], ENT_QUOTES) : '';
$sess_wil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$id_role = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);

$refund = 0;
$total_refund = 0;
$sql = "SELECT a.id_dsd as id_dsdnya, a.id_invoice as id_invoicenya, a.total_refund, a.paid_by, a.tgl_bayar, a.disposisi, i.nama_customer, i.kode_pelanggan, i.jenis_payment, i.top_payment, i.id_customer as id_customernya, e.alamat_survey, f.nama_prov, g.nama_kab, j.fullname, h.nomor_poc, h.tanggal_poc, h.id_poc as id_pocnya, h.created_time as tgl_buat_po, b.volume_po, k.refund_tawar, l.nama_area, m.wilayah_angkut, k.id_penawaran, ppdd.tanggal_delivered, n.no_invoice, n.tgl_invoice, n.tgl_invoice_dikirim, o.vol_kirim, (SELECT SUM(vol_kirim) FROM pro_invoice_admin_detail WHERE id_invoice=a.id_invoice) as total_vol_invoice
from pro_refund a 
join pro_po_ds_detail ppdd on ppdd.id_dsd = a.id_dsd
join pro_po_detail b on ppdd.id_pod = b.id_pod
join pro_pr_detail c on ppdd.id_prd = c.id_prd 
join pro_po_customer_plan d on ppdd.id_plan = d.id_plan 
join pro_customer_lcr e on d.id_lcr = e.id_lcr
join pro_master_provinsi f on e.prov_survey = f.id_prov 
join pro_master_kabupaten g on e.kab_survey = g.id_kab
join pro_po_customer h on d.id_poc = h.id_poc 
join pro_customer i on h.id_customer = i.id_customer 
join acl_user j on i.id_marketing = j.id_user 
join pro_penawaran k on h.id_penawaran = k.id_penawaran	
join pro_master_area l on k.id_area = l.id_master 
join pro_master_wilayah_angkut m on e.id_wil_oa = m.id_master and e.prov_survey = m.id_prov and e.kab_survey = m.id_kab
join pro_invoice_admin n on a.id_invoice = n.id_invoice
join pro_invoice_admin_detail o on a.id_invoice = o.id_invoice 
where a.id_dsd='" . $id_dsd . "'";
$result 	= $con->getRecord($sql);

// $arrid_invoice = json_decode($result['id_invoicenya'], true);
$total_refund = $result['refund_tawar'] * $result['total_vol_invoice'];
$tgl_invoice = $result['tgl_invoice'];
$nomor_invoice = $result['no_invoice'];
$tgl_invoice_dikirim = tgl_indo($result['tgl_invoice_dikirim']);

$sql_1 = "SELECT * FROM pro_invoice_admin WHERE id_invoice = '" . $result['id_invoicenya'] . "'";
$row_1 = $con->getRecord($sql_1);
if (($row_1['total_invoice'] == $row_1['total_bayar']) || $row_1['is_lunas'] == '1') {
	$sql_bayar_1 = "SELECT MAX(tgl_bayar) as tanggal_bayar FROM pro_invoice_admin_detail_bayar WHERE id_invoice='" . $result['id_invoicenya'] . "'";
	$row_bayar_1 = $con->getRecord($sql_bayar_1);
	$status_invoice_1 = "Lunas";
	$date_payment = tgl_indo($row_bayar_1['tanggal_bayar']);
} else {
	$sql_bayar_1 = "SELECT MAX(tgl_bayar) as tanggal_bayar FROM pro_invoice_admin_detail_bayar WHERE id_invoice='" . $result['id_invoicenya'] . "'";
	$row_bayar_1 = $con->getRecord($sql_bayar_1);
	$date_payment = tgl_indo($row_bayar_1['tanggal_bayar']);
	$status_invoice_1 = "Not Yet";
}

$due_date_indo = tgl_indo(date('Y-m-d', strtotime($result['tgl_invoice_dikirim'] . "+" . $result['top_payment'] . " days")));
$due_date = date('Y-m-d', strtotime($result['tgl_invoice_dikirim'] . "+" . $result['top_payment'] . " days"));

$sql_penerima_refund = "SELECT a.*, b.* FROM pro_poc_penerima_refund a JOIN pro_master_penerima_refund b ON a.penerima_refund=b.id WHERE a.id_poc='" . $result['id_pocnya'] . "'";
$res_penerima_refund = $con->getResult($sql_penerima_refund);

$nama = "";
$divisi = "";
$bank = "";
$no_rekening = "";
$atas_nama = "";
$persentase_refund = 0;
$terima_refund_fix = 0;

$nama2 = "";
$divisi2 = "";
$bank2 = "";
$no_rekening2 = "";
$atas_nama2 = "";
$persentase_refund2 = 0;
$terima_refund_fix2 = 0;

$nama3 = "";
$divisi3 = "";
$bank3 = "";
$no_rekening3 = "";
$atas_nama3 = "";
$persentase_refund3 = 0;
$terima_refund_fix3 = 0;

foreach ($res_penerima_refund as $i => $key) {
	if ($i == 0) {
		$nama = $key['nama'];
		$divisi = $key['divisi'];
		$bank = $key['bank'];
		$no_rekening = $key['no_rekening'];
		$atas_nama = $key['atas_nama'];
		$persentase_refund = $key['persentasi_refund'];
		$terima_refund_fix = $key['terima_refund'];
	}
	if ($i == 1) {
		$nama2 = $key['nama'];
		$divisi2 = $key['divisi'];
		$bank2 = $key['bank'];
		$no_rekening2 = $key['no_rekening'];
		$atas_nama2 = $key['atas_nama'];
		$persentase_refund2 = $key['persentasi_refund'];
		$terima_refund_fix2 = $key['terima_refund'];
	}
	if ($i == 2) {
		$nama3 = $key['nama'];
		$divisi3 = $key['divisi'];
		$bank3 = $key['bank'];
		$no_rekening3 = $key['no_rekening'];
		$atas_nama3 = $key['atas_nama'];
		$persentase_refund3 = $key['persentasi_refund'];
		$terima_refund_fix3 = $key['terima_refund'];
	}
}

// echo json_encode($res_penerima_refund);

$week1 = 0;
$week2 = 0;
$week3 = 0;
$week4 = 0;
$week5 = 0;
$week6 = 0;
$week7 = 0;

$due_date_week2 = date('Y-m-d', strtotime($due_date . "+" . "14 days"));
$due_date_week3 = date('Y-m-d', strtotime($due_date_week2 . "+" . "7 days"));
$due_date_week4 = date('Y-m-d', strtotime($due_date_week3 . "+" . "10 days"));
$due_date_week5 = date('Y-m-d', strtotime($due_date_week4 . "+" . "14 days"));
$due_date_week6 = date('Y-m-d', strtotime($due_date_week5 . "+" . "15 days"));
// $due_date_week7 = date('Y-m-d', strtotime($due_date_week6 . "+" . "1 days"));

if ($row_bayar_1['tanggal_bayar'] <= $due_date) {
	$week1 += ($total_refund * 100) / 100;
	$week2 += 0;
	$week3 += 0;
	$week4 += 0;
	$week5 += 0;
	$week6 += 0;
	$week7 += 0;
	$total_refund_fix = $week1;
	$persen = 100;
} elseif ($row_bayar_1['tanggal_bayar'] <= $due_date_week2) {
	$week1 += 0;
	$week2 += ($total_refund * 95) / 100;
	$week3 += 0;
	$week4 += 0;
	$week5 += 0;
	$week6 += 0;
	$week7 += 0;
	$total_refund_fix = $week2;
	$persen = 95;
} elseif ($row_bayar_1['tanggal_bayar'] > $due_date_week2 && $row_bayar_1['tanggal_bayar'] <= $due_date_week3) {
	$week1 += 0;
	$week2 += 0;
	$week3 += ($total_refund * 85) / 100;
	$week4 += 0;
	$week5 += 0;
	$week6 += 0;
	$week7 += 0;
	$total_refund_fix = $week3;
	$persen = 85;
} elseif ($row_bayar_1['tanggal_bayar'] > $due_date_week3 && $row_bayar_1['tanggal_bayar'] <= $due_date_week4) {
	$week1 += 0;
	$week2 += 0;
	$week3 += 0;
	$week4 += ($total_refund * 75) / 100;
	$week5 += 0;
	$week6 += 0;
	$week7 += 0;
	$total_refund_fix = $week4;
	$persen = 75;
} elseif ($row_bayar_1['tanggal_bayar'] > $due_date_week4 && $row_bayar_1['tanggal_bayar'] <= $due_date_week5) {
	$week1 += 0;
	$week2 += 0;
	$week3 += 0;
	$week4 += 0;
	$week5 += ($total_refund * 65) / 100;
	$week6 += 0;
	$week7 += 0;
	$total_refund_fix = $week5;
	$persen = 65;
} elseif ($row_bayar_1['tanggal_bayar'] > $due_date_week5 && $row_bayar_1['tanggal_bayar'] <= $due_date_week6) {
	$week1 += 0;
	$week2 += 0;
	$week3 += 0;
	$week4 += 0;
	$week5 += 0;
	$week6 += ($total_refund * 50) / 100;
	$week7 += 0;
	$total_refund_fix = $week6;
	$persen = 50;
} elseif ($row_bayar_1['tanggal_bayar'] > $due_date_week6) {
	$week1 += 0;
	$week2 += 0;
	$week3 += 0;
	$week4 += 0;
	$week5 += 0;
	$week6 += 0;
	$week7 += ($total_refund * 0) / 100;
	$total_refund_fix = $week7;
	$persen = 0;
}

if ($total_refund_fix == 0) {
	$status_refund = "HANGUS";
} else {
	if ($result['total_refund'] != 0) {
		$status_refund = "PAID By " . ucwords($result['paid_by']) . " " . tgl_indo($result['tgl_bayar']);
	} else {
		$status_refund = "PROGRESS";
	}
}
// echo json_encode($result);

$printe = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]) . " " . date("d/m/Y H:i:s") . " WIB";
// if ($result['disposisi'] == 2) {
// }
$barcod = $result['no_invoice'];
// echo json_encode($approval);

ob_start();
require_once(realpath("./template/refund-customer.php"));
$content = ob_get_clean();
ob_end_flush();
$con->close();

$mpdf = null;
if (PHP_VERSION >= 5.6) {
	$mpdf = new \Mpdf\Mpdf(['format' => 'Letter', 'default_font' => 'Arial']);
}
$mpdf->AddPage('P', '', '', '', '', 12, 12, 12, 12, 12, 12);
$mpdf->SetDisplayMode('fullpage');
$mpdf->use_kwt = true;
$mpdf->autoPageBreak = false;
$mpdf->setAutoTopMargin = 'stretch';
$mpdf->setAutoBottomMargin = 'stretch';
$mpdf->shrink_tables_to_fit = 1;
$mpdf->WriteHTML($content);
$filename = "Refund_";
$mpdf->Output($filename . '_' . date('dmyHis') . '.pdf', 'I');
exit;

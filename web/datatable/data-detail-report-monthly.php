<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$con 	= new Connection();

$cabang		= isset($_POST["cabang"]) ? htmlspecialchars($_POST["cabang"], ENT_QUOTES) : '';
$tgl_awal	= isset($_POST["tgl_awal"]) ? htmlspecialchars($_POST["tgl_awal"], ENT_QUOTES) : '';
$tgl_akhir	= isset($_POST["tgl_akhir"]) ? htmlspecialchars($_POST["tgl_akhir"], ENT_QUOTES) : '';
$kategori 	= isset($_POST["kategori"]) ? htmlspecialchars($_POST["kategori"], ENT_QUOTES) : '';

if ($kategori == "POC") {
	$sql = "SELECT a.nomor_poc, a.tanggal_poc, CONCAT(b.kode_pelanggan, ' ', b.nama_customer) as nama_customer, a.harga_poc, a.volume_poc, a.disposisi_poc, a.poc_approved, a.sm_pic, a.sm_result FROM pro_po_customer a
	JOIN pro_customer b ON a.id_customer=b.id_customer
	WHERE b.id_wilayah = " . $cabang . "
	AND a.disposisi_poc = 1
	AND a.poc_approved = 1
	AND (a.tanggal_poc BETWEEN '" . date("Y-m-d", strtotime($tgl_awal)) . "' AND '" . date("Y-m-d", strtotime($tgl_akhir)) . "')
	ORDER BY a.tanggal_poc ASC";
	$result = $con->getResult($sql);
} else if ($kategori == "DO") {
	$sql = "SELECT a.nomor_pr, a.tanggal_pr, CONCAT(f.kode_pelanggan, ' ', f.nama_customer) as nama_customer, b.no_do_syop, b.volume, e.harga_dasar FROM pro_pr a
	JOIN pro_pr_detail b ON a.id_pr=b.id_pr
	JOIN pro_po_customer_plan c ON b.id_plan=c.id_plan
	JOIN pro_po_customer d ON c.id_poc=d.id_poc
	JOIN pro_penawaran e ON d.id_penawaran=e.id_penawaran
	JOIN pro_customer f ON d.id_customer=f.id_customer
	WHERE a.id_wilayah = " . $cabang . "
	AND a.disposisi_pr = 7
	AND b.is_approved = 1
	AND (a.tanggal_pr BETWEEN '" . date("Y-m-d", strtotime($tgl_awal)) . "' AND '" . date("Y-m-d", strtotime($tgl_akhir)) . "')
	ORDER BY a.tanggal_pr ASC";
	$result = $con->getResult($sql);
} else if ($kategori == "Loaded") {
	$sql = "SELECT CONCAT(f.kode_pelanggan, ' ', f.nama_customer) as nama_customer, a.nomor_ds, CONCAT(b.tanggal_loaded, ' ', b.jam_loaded) as tanggal_loaded, b.nomor_do, c.no_do_syop, c.volume FROM pro_po_ds a
	JOIN pro_po_ds_detail b ON a.id_ds=b.id_ds
	JOIN pro_pr_detail c ON b.id_prd=c.id_prd
	JOIN pro_pr d ON c.id_pr=d.id_pr
	JOIN pro_po_customer e ON b.id_poc=e.id_poc
	JOIN pro_customer f ON e.id_customer=f.id_customer
	WHERE a.id_wilayah = " . $cabang . "
	AND a.is_submitted = 1
	AND (d.tanggal_pr BETWEEN '" . date("Y-m-d", strtotime($tgl_awal)) . "' AND '" . date("Y-m-d", strtotime($tgl_akhir)) . "')
	AND (b.is_loaded = 1 AND b.is_cancel = 0)
	ORDER BY a.tanggal_ds ASC";
	$result = $con->getResult($sql);
} else if ($kategori == "Delivered") {
	$sql = "SELECT CONCAT(f.kode_pelanggan, ' ', f.nama_customer) as nama_customer, a.nomor_ds, b.nomor_do, c.no_do_syop, CONCAT(b.tanggal_loaded, ' ', b.jam_loaded) as tanggal_loaded, b.tanggal_delivered, c.volume FROM pro_po_ds a
	JOIN pro_po_ds_detail b ON a.id_ds=b.id_ds
	JOIN pro_pr_detail c ON b.id_prd=c.id_prd
	JOIN pro_pr d ON c.id_pr=d.id_pr
	JOIN pro_po_customer e ON b.id_poc=e.id_poc
	JOIN pro_customer f ON e.id_customer=f.id_customer
	WHERE a.id_wilayah = " . $cabang . "
	AND a.is_submitted = 1
	AND (d.tanggal_pr BETWEEN '" . date("Y-m-d", strtotime($tgl_awal)) . "' AND '" . date("Y-m-d", strtotime($tgl_akhir)) . "')
	AND b.is_delivered = 1";
	$result = $con->getResult($sql);
} else if ($kategori == "Realisasi") {
	$sql = "SELECT CONCAT(f.kode_pelanggan, ' ', f.nama_customer) as nama_customer, a.nomor_ds, b.nomor_do, c.no_do_syop, c.nomor_lo_pr, CONCAT(b.tanggal_loaded, ' ', b.jam_loaded) as tanggal_loaded, b.tanggal_delivered, c.volume, b.realisasi_volume FROM pro_po_ds a
	JOIN pro_po_ds_detail b ON a.id_ds=b.id_ds
	JOIN pro_pr_detail c ON b.id_prd=c.id_prd
	JOIN pro_pr d ON c.id_pr=d.id_pr
	JOIN pro_po_customer e ON b.id_poc=e.id_poc
	JOIN pro_customer f ON e.id_customer=f.id_customer
	WHERE a.id_wilayah = " . $cabang . "
	AND a.is_submitted = 1
	AND (d.tanggal_pr BETWEEN '" . date("Y-m-d", strtotime($tgl_awal)) . "' AND '" . date("Y-m-d", strtotime($tgl_akhir)) . "')
	AND b.realisasi_volume != 0";
	$result = $con->getResult($sql);
} else if ($kategori == "Invoice") {
	$sql = "SELECT CONCAT(b.kode_pelanggan, ' ', b.nama_customer) as nama_customer, a.no_invoice, a.tgl_invoice, a.total_invoice
	FROM pro_invoice_admin a 
	JOIN pro_customer b ON a.id_customer = b.id_customer 
	JOIN pro_master_cabang c ON b.id_wilayah = c.id_master
	WHERE 1=1 AND c.id_master = " . $cabang . " AND (a.tgl_invoice BETWEEN '" . date("Y-m-d", strtotime($tgl_awal)) . "' AND '" . date("Y-m-d", strtotime($tgl_akhir)) . "')
	ORDER BY a.tgl_invoice ASC";
	$result = $con->getResult($sql);
} else if ($kategori == "Volume Invoice") {
	$sql = "SELECT CONCAT(b.kode_pelanggan,' ', b.nama_customer) as nama_customer, a.no_invoice, a.tgl_invoice, d.vol_kirim FROM pro_invoice_admin a 
	JOIN pro_customer b ON a.id_customer = b.id_customer 
	JOIN pro_invoice_admin_detail d ON a.id_invoice=d.id_invoice
	WHERE 1=1 AND b.id_wilayah= " . $cabang . " 
	AND (a.tgl_invoice BETWEEN '" . date("Y-m-d", strtotime($tgl_awal)) . "' AND '" . date("Y-m-d", strtotime($tgl_akhir)) . "')
	AND a.jenis IN('all_in','harga_dasar','harga_dasar_oa','harga_dasar_pbbkb')";
	$result = $con->getResult($sql);
}

$data = [
	"data" 		=> $result,
	"kategori"	=> $kategori
];

echo json_encode($data);

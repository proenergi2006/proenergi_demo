<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;
$enk  	= decode($_SERVER['REQUEST_URI']);
$sess_wil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

$kategori  = isset($_POST["kategori"]) ? htmlspecialchars($_POST["kategori"], ENT_QUOTES) : NULL;
$cabang  = isset($_POST["cabang"]) ? htmlspecialchars($_POST["cabang"], ENT_QUOTES) : NULL;
$tgl_awal  = isset($_POST["tgl_awal"]) ? htmlspecialchars($_POST["tgl_awal"], ENT_QUOTES) : NULL;
$tgl_akhir  = isset($_POST["tgl_akhir"]) ? htmlspecialchars($_POST["tgl_akhir"], ENT_QUOTES) : NULL;
$datenow = date("Y-m-d");

if ($kategori == "po_supplier") {
	$nama_kategori = "PO Supplier";
} elseif ($kategori == "invoice") {
	$nama_kategori = "Invoice";
} elseif ($kategori == "po_customer") {
	$nama_kategori = "PO Customer";
} elseif ($kategori == "bpuj") {
	$nama_kategori = "BPUJ";
}

$sql_cabang = "SELECT * FROM pro_master_cabang WHERE id_master='" . $cabang . "'";
$row_cabang = $con->getRecord($sql_cabang);

// echo json_encode();
if ($kategori == "po_supplier") {
	$sql = "SELECT a.nomor_po, b.id_po_receive, b.id_po_supplier, a.tanggal_inven as tanggal_po, a.volume_po, b.volume_terima, b.tgl_terima,
	(
	SELECT SUM(ab.out_inven_virtual)
	FROM new_pro_inventory_depot ab 
	JOIN pro_pr ac ON ab.id_pr=ac.id_pr
	AND ab.id_jenis = '6' 
	AND ab.id_po_supplier=b.id_po_supplier
	AND ab.id_po_receive=b.id_po_receive
	) as yang_terpakai,
	(
	SELECT SUM(d.sisa_inven) FROM vw_terminal_inventory_receive d 
	WHERE d.id_po_supplier=b.id_po_supplier
	AND d.id_po_receive=b.id_po_receive
	AND d.id_terminal=a.id_terminal
	) as sisa_stock,
	(
	SELECT SUM(d.adj_inven) FROM vw_terminal_inventory_receive d 
	WHERE d.id_po_supplier=b.id_po_supplier
	AND d.id_po_receive=b.id_po_receive
	AND d.id_terminal=a.id_terminal
	) as adj_inven
	FROM new_pro_inventory_vendor_po a
	JOIN new_pro_inventory_vendor_po_receive b ON a.id_master=b.id_po_supplier
	JOIN pro_master_terminal c ON a.id_terminal=c.id_master
	WHERE c.id_cabang = '" . $cabang . "' AND (a.tanggal_inven BETWEEN '" . tgl_db($tgl_awal) . "' AND '" . tgl_db($tgl_akhir) . "')";

	$sql .= " order by a.tanggal_inven desc";
	$res = $con->getResult($sql);

	$data = [
		"status" => 200,
		"data" => $res,
		"kategori" => $nama_kategori,
		"cabang" => $row_cabang['nama_cabang'],
		"periode" => tgl_indo(tgl_db($tgl_awal)) . " s/d " . tgl_indo(tgl_db($tgl_akhir)),
	];
	echo json_encode($data);
} elseif ($kategori == "invoice") {
} elseif ($kategori == "po_customer") {
} elseif ($kategori == "bpuj") {
}

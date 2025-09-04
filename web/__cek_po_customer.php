<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$conSub = new Connection();
$valid	= true;
$answer	= array();
$dt1 	= htmlspecialchars($_POST["penawaran"], ENT_QUOTES);
$dt2 	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["harga_liter"]), ENT_QUOTES);
$dt3 	= htmlspecialchars($_POST["produk"], ENT_QUOTES);
$dt4 	= htmlspecialchars(str_replace(array(","), array(""), $_POST["harga_liter2"]), ENT_QUOTES);

$sql = "select a.*, b.nama_cabang, c.jenis_produk from pro_penawaran a 
			join pro_master_cabang b on a.id_cabang = b.id_master join pro_master_produk c on a.produk_tawar = c.id_master where a.id_penawaran = '" . $dt1 . "'";
$rsm = $conSub->getRecord($sql);
if ($rsm['perhitungan'] == 1) {
	if ($rsm['harga_dasar'] > $dt4) {
		$valid = false;
		$pesan = "Harga yang diinput tidak sesuai dengan harga di penawaran...";
	} else if ($rsm['produk_tawar'] != $dt3) {
		$valid = false;
		$pesan = "Produk yang diinput tidak sesuai dengan produk di penawaran...";
	}
} else if ($rsm['perhitungan'] == 2) {
	if ($rsm['produk_tawar'] != $dt3) {
		$valid = false;
		$pesan = "Produk yang diinput tidak sesuai dengan produk di penawaran...";
	}
} else {
	$valid = false;
	$pesan = "Data penawaran tidak ditemukan...";
}

$answer["error"] = ($valid) ? "" : $pesan;
echo json_encode($answer);
$conSub->close();

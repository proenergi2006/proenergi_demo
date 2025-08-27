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
$act	= ($enk['act'] == "") ? htmlspecialchars($_POST["act"], ENT_QUOTES) : $enk['act'];

$jenis	= htmlspecialchars($_POST["jenis"], ENT_QUOTES);
$merk	= htmlspecialchars($_POST["merk"], ENT_QUOTES);
$active = isset($_POST["active"]) ? htmlspecialchars($_POST["active"], ENT_QUOTES) : '0';
if ($active == "") {
	$actived = 0;
} else {
	$actived = 1;
}
$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
$note 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["note"], ENT_QUOTES));

if ($jenis == "" || $merk == "") {
	$con->close();
	$flash->add("error", "KOSONG", BASE_REFERER);
} else {
	if ($act == 'add') {
		$sql = "insert into pro_master_produk(jenis_produk, catatan_produk, merk_dagang, is_active, created_time, created_ip, created_by) values ('" . $jenis . "', '" . $note . "', '" . $merk . "', '" . $active . "', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "')";
		$msg = "GAGAL_MASUK";
	} else if ($act == 'update') {
		$sql = "update pro_master_produk set catatan_produk = '" . $note . "', jenis_produk = '" . $jenis . "', merk_dagang = '" . $merk . "', is_active = '" . $actived . "', lastupdate_time = NOW(), lastupdate_ip = '" . $_SERVER['REMOTE_ADDR'] . "', lastupdate_by = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "' where id_master = " . $idr;
		$msg = "GAGAL_UBAH";
	}

	$con->setQuery($sql);
	if (!$con->hasError()) {
		$con->close();
		header("location: " . BASE_URL_CLIENT . "/master-produk.php");
		exit();
	} else {
		$con->clearError();
		$con->close();
		$flash->add("error", $msg, BASE_REFERER);
	}
}

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

$volume	= htmlspecialchars(str_replace(array(",", "."), array("", ""), $_POST["volume"]), ENT_QUOTES);
$active = isset($_POST["active"]) ? htmlspecialchars($_POST["active"], ENT_QUOTES) : '0';
$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);

if ($volume == "") {
	$con->close();
	$flash->add("error", "KOSONG", BASE_REFERER);
} else {
	if ($act == 'add') {
		$sql = "insert into pro_master_volume_angkut(volume_angkut, is_active, created_time, created_ip, created_by) values ('" . $volume . "', '" . $active . "', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "')";
		$msg = "GAGAL_MASUK";
	} else if ($act == 'update') {
		$sql = "update pro_master_volume_angkut set volume_angkut = '" . $volume . "', is_active = '" . $active . "', lastupdate_time = NOW(), lastupdate_ip = '" . $_SERVER['REMOTE_ADDR'] . "', lastupdate_by = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "' where id_master = " . $idr;
		$msg = "GAGAL_UBAH";
	}

	$con->setQuery($sql);
	if (!$con->hasError()) {
		$con->close();
		header("location: " . BASE_URL_CLIENT . "/master-volume-angkut.php");
		exit();
	} else {
		$con->clearError();
		$con->close();
		$flash->add("error", $msg, BASE_REFERER);
	}
}

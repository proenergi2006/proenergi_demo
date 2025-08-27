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
$idr	= htmlspecialchars($_POST["idr"], ENT_QUOTES);

$oke = true;
$con->beginTransaction();
$con->clearError();

foreach ($_POST['link_gps'] as $idx => $val) {
	$link_gps = htmlspecialchars(str_replace(array(" ", ","), array("", ""), $_POST["link_gps"][$idx]), ENT_QUOTES);
	$user_gps = htmlspecialchars($_POST["user_gps"][$idx], ENT_QUOTES);
	$pass_gps = htmlspecialchars($_POST["pass_gps"][$idx], ENT_QUOTES);
	$membercode_gps = htmlspecialchars($_POST["membercode_gps"][$idx], ENT_QUOTES);
	$sql = "update pro_master_transportir_mobil set link_gps = '" . $link_gps . "', user_gps = '" . $user_gps . "', pass_gps = '" . $pass_gps . "', membercode_gps = '" . $membercode_gps . "' where id_master = " . $idx;
	$con->setQuery($sql);
	$oke = $oke && !$con->hasError();
}

if ($oke) {
	$con->commit();
	$con->close();
	$flash->add("success", "Data berhasil diubah", BASE_URL_CLIENT . "/gps-truck.php?" . paramEncrypt("q1=" . $idr));
} else {
	$con->rollBack();
	$con->clearError();
	$con->close();
	$flash->add("error", $msg, BASE_REFERER);
}

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

$nama_penerima	= htmlspecialchars($_POST["nama_penerima"], ENT_QUOTES);
$jabatan		= htmlspecialchars($_POST["jabatan"], ENT_QUOTES);
$cabang			= htmlspecialchars($_POST["cabang"], ENT_QUOTES);
$persen			= htmlspecialchars($_POST["persen"], ENT_QUOTES);
$active 		= htmlspecialchars($_POST["active"], ENT_QUOTES);
$idr 			= htmlspecialchars($_POST["idr"], ENT_QUOTES);

$oke = true;
$con->beginTransaction();
$con->clearError();

if ($act == 'add') {
	$query = "SELECT * FROM pro_master_cabang WHERE id_master='" . $cabang . "'";
	$res_cabang = $con->getRecord($query);

	$sql_cek_bm = "SELECT * FROM pro_penerima_incentive WHERE cabang = '" . $cabang . "' AND status = '1' AND jabatan = '" . $jabatan . "'";
	$res_cek_bm = $con->getRecord($sql_cek_bm);

	if ($res_cek_bm) {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "Data BM atau SM dengan cabang " . $res_cabang['nama_cabang'] . " dan masih berstatus aktif sudah ada", BASE_REFERER);
	}

	$sql = "INSERT into pro_penerima_incentive(nama, jabatan, cabang, status, persentase, created_at) values ('" . $nama_penerima . "', '" . $jabatan . "', '" . $cabang . "','" . $active . "', '" . $persen . "', NOW())";
	$msg = "GAGAL_MASUK";
	$con->setQuery($sql);
	$oke  = $oke && !$con->hasError();
} else if ($act == 'update') {
	$query = "SELECT * FROM pro_master_cabang WHERE id_master='" . $cabang . "'";
	$res_cabang = $con->getRecord($query);

	// Cek apakah BM lain di cabang sama dan status aktif sudah ada (kecuali data yang sedang diupdate)
	$sql_cek_bm = "SELECT * FROM pro_penerima_incentive WHERE cabang = '" . $cabang . "' AND status = '1' AND jabatan = '" . $jabatan . "' AND id != '" . $idr . "'";

	if ($res_cek_bm) {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "Data BM atau SM dengan cabang " . $res_cabang['nama_cabang'] . " dan masih berstatus aktif sudah ada", BASE_REFERER);
	}

	$sql = "UPDATE pro_penerima_incentive set nama = '" . $nama_penerima . "', jabatan = '" . $jabatan . "', cabang = '" . $cabang . "', status = '" . $active . "', persentase = '" . $persen . "', created_at = NOW() where id = " . $idr;
	$msg = "GAGAL_UBAH";
	$con->setQuery($sql);
	$oke  = $oke && !$con->hasError();
}

if ($oke) {
	$con->commit();
	$con->close();
	header("location: " . BASE_URL_CLIENT . "/penerima_incentive.php");
	exit();
} else {
	$con->rollBack();
	$con->clearError();
	$con->close();
	$flash->add("error", $msg, BASE_REFERER);
}

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

$vendor	= htmlspecialchars($_POST["vendor"], ENT_QUOTES);
$kode	= htmlspecialchars($_POST["kode_vendor"], ENT_QUOTES);
$inisial_vendor	= htmlspecialchars($_POST["inisial_vendor"], ENT_QUOTES);
$kode_vendor	= 'V-' . $kode;
$active = isset($_POST["active"]) ? htmlspecialchars($_POST["active"], ENT_QUOTES) : '0';
$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
$note 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["note"], ENT_QUOTES));
$oke = true;
$con->beginTransaction();
$con->clearError();

if ($vendor == "") {
	$con->close();
	$flash->add("error", "KOSONG", BASE_REFERER);
} else {
	if ($act == 'add') {
		$sql = "insert into pro_master_vendor(nama_vendor, kode_vendor, is_active, created_time, created_ip, created_by, inisial_vendor) values ('" . $vendor . "', '" . $kode_vendor . "', '" . $active . "', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "','" . $inisial_vendor . "')";
		$msg = "GAGAL_MASUK";
		$lastid = $con->setQuery($sql);
		$oke = $oke && !$con->hasError();
	} else if ($act == 'update') {
		$sql = "update pro_master_vendor set nama_vendor = '" . $vendor . "', is_active = '" . $active . "', inisial_vendor = '" . $inisial_vendor . "', lastupdate_time = NOW(), lastupdate_ip = '" . $_SERVER['REMOTE_ADDR'] . "', lastupdate_by = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "' where id_master = " . $idr;
		$msg = "GAGAL_UBAH";
		$con->setQuery($sql);
		$oke = $oke && !$con->hasError();
	}

	if ($oke) {
		if ($act == 'add') {

			$urlnya = 'https://zeus.accurate.id/accurate/api/vendor/save.do';
			// Data yang akan dikirim dalam format JSON
			$data = array(
				'name'        	=> $vendor,
				'transDate'     => date("d/m/Y"),
				'currencyCode'  => "IDR",
				'vendorNo'        => $kode_vendor,
			);

			$jsonData = json_encode($data);


			$result = curl_post($urlnya, $jsonData);

			if ($result['s'] == true) {
				$ambil_id_accurate = "UPDATE pro_master_vendor set id_accurate = '" . $result['r']['id'] . "', kode_vendor = '" . $kode_vendor . "' WHERE id_master = " . $lastid;
				$con->setQuery($ambil_id_accurate);
				if (!$con->hasError()) {
					$con->commit();
					$con->close();
					header("location: " . BASE_URL_CLIENT . "/master-vendor.php");
					exit();
				} else {
					$con->clearError();
					$con->close();
					$flash->add("error", $msg, BASE_REFERER);
				}
			} else {
				$con->clearError();
				$con->close();
				$flash->add("error", $result['d'][0] . " - Response dari Accurate", BASE_REFERER);
			}
		}
	}
}

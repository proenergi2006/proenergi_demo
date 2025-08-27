<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "htmlawed");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$oke 	= true;
$flash	= new FlashAlerts;
$enk  	= decode($_SERVER['REQUEST_URI']);
$act	= ($enk['act'] == "") ? htmlspecialchars($_POST["act"], ENT_QUOTES) : $enk['act'];
$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
$idk	= htmlspecialchars($_POST["idk"], ENT_QUOTES);

$alamat_kirim	= htmlspecialchars($_POST["alamat_kirim"], ENT_QUOTES);
$tanggal_kirim	= htmlspecialchars($_POST["tanggal_kirim"], ENT_QUOTES);
$volume_kirim	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["vol_kir"]), ENT_QUOTES);
$is_urgent		= isset($_POST["is_urgent"]) ? htmlspecialchars($_POST["is_urgent"], ENT_QUOTES) : 0;
$status			= htmlspecialchars($_POST["catatan"], ENT_QUOTES);
$tanggal_buat	= date("Y/m/d H:i:s");
$sesname 		= paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]);

if ($act == "delete") {
	$idr = isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
	$idk = htmlspecialchars($enk["idk"], ENT_QUOTES);
	$idp = htmlspecialchars($enk["idp"], ENT_QUOTES);
	$sql1 = "delete from pro_po_customer_plan where id_plan = '" . $idp . "'";
	$con->setQuery($sql1);
	$oke = $oke && !$con->hasError();

	$qvolume = "select volume_poc from pro_po_customer where id_poc = " . $idk;
	$volume = $con->getRecord($qvolume);

	$qvolume_plan = "select sum(volume_kirim) as jum_kirim from pro_po_customer_plan where id_poc = " . $idk;
	$volume_plan = $con->getRecord($qvolume_plan);

	if ($volume['volume_poc'] > $volume_plan['jum_kirim']) {
		$sql2 = "update pro_po_customer set po_notif = 1 where id_poc = '" . $idk . "'";
		$con->setQuery($sql2);
		$oke = $oke && !$con->hasError();
	}

	$url  = BASE_URL_CLIENT . "/po-customer-plan.php?" . paramEncrypt("idr=" . $idr . "&idk=" . $idk);
	if ($oke) {
		$con->close();
		header("location: " . $url);
		exit();
	} else {
		$con->clearError();
		$con->close();
		$flash->add("error", "GAGAL_HAPUS", BASE_REFERER);
	}
} else {
	$datetime1 	= new DateTime(date("Y/m/d", strtotime($tanggal_buat)));
	$datetime2 	= new DateTime(tgl_db($tanggal_kirim));
	$interval 	= $datetime1->diff($datetime2);
	$cekHari	= $interval->format("%r%a");

	if ($idr == "" || $idk == "" || $alamat_kirim == "" || $tanggal_kirim == "" || $volume_kirim == "") {
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else if ($cekHari < 0) {
		$con->close();
		$flash->add("error", "Tanggal pengiriman tidak benar....", BASE_REFERER);
	} else {
		// if($cekHari == 0)
		// 	$status .= "<br /><i>Urgent</i>";
		// else if($cekHari == 1 && date("Hi", strtotime($tanggal_buat)) > '1300')
		// 	$status .= "<br /><i>Lewat Jam 13:00 WIB</i>";
		// else $status = $status;

		$sql = "insert into pro_po_customer_plan(id_poc, id_lcr, tanggal_kirim, volume_kirim, is_urgent, status_jadwal, created_time, created_ip, created_by) values ('" . $idk . "', '" . $alamat_kirim . "', '" . tgl_db($tanggal_kirim) . "', '" . $volume_kirim . "', '" . $is_urgent . "', '" . $status . "', '" . $tanggal_buat . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $sesname . "')";
		$con->setQuery($sql);
		$oke = $oke && !$con->hasError();

		$qvolume = "select volume_poc from pro_po_customer where id_poc = " . $idk;
		$volume = $con->getRecord($qvolume);

		$qvolume_plan = "select sum(volume_kirim) as jum_kirim from pro_po_customer_plan where id_poc = " . $idk;
		$volume_plan = $con->getRecord($qvolume_plan);

		if ($volume['volume_poc'] > $volume_plan['jum_kirim'])
			$sql2 = "update pro_po_customer set po_notif = 1 where id_poc = " . $idk;
		else
			$sql2 = "update pro_po_customer set po_notif = 0 where id_poc = " . $idk;

		$con->setQuery($sql2);
		$oke = $oke && !$con->hasError();

		$url  = BASE_URL_CLIENT . "/po-customer-plan.php?" . paramEncrypt("idr=" . $idr . "&idk=" . $idk);

		if ($oke) {
			$con->close();
			header("location: " . $url);
			exit();
		} else {
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
	}
}

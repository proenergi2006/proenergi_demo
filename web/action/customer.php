<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "htmlawed");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;
$enk  	= decode($_SERVER['REQUEST_URI']);
$act	= (isset($enk['act']) and $enk['act'] != "") ? $enk['act'] : htmlspecialchars($_POST["act"], ENT_QUOTES);
$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$seswil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$sesgroup = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);

$nama 		= htmlspecialchars($_POST["nama_customer"], ENT_QUOTES);
$email		= htmlspecialchars($_POST["email_customer"], ENT_QUOTES);
$alamat		= htmlspecialchars($_POST["alamat_customer"], ENT_QUOTES);
$jenis_customer		= htmlspecialchars($_POST["jenis_customer"], ENT_QUOTES);
$propinsi	= htmlspecialchars($_POST["prov_customer"], ENT_QUOTES);
$kabupaten	= htmlspecialchars($_POST["kab_customer"], ENT_QUOTES);
$telepon	= htmlspecialchars($_POST["telp_customer"], ENT_QUOTES);
$fax		= htmlspecialchars($_POST["fax_customer"], ENT_QUOTES);
$marketing	= htmlspecialchars($_POST["marketing"], ENT_QUOTES);
$postalcode_customer	= htmlspecialchars($_POST["postalcode_customer"], ENT_QUOTES);

$sqlValNama = "SELECT count(1) JML FROM pro_customer
			 		WHERE UPPER(NAMA_CUSTOMER) = UPPER('" . $nama . "')
			 		and prov_customer = '" . $propinsi . "'";
$resVal = $con->getRecord($sqlValNama);
$jmlCustomer = intval($resVal['JML']);
// echo json_encode($jmlCustomer); die();

if ($marketing == "" || $nama == "" || $email == "" || $alamat == "" || $propinsi == "" || $kabupaten == "" || $telepon == "") {
	$con->close();
	$flash->add("error", "KOSONG", BASE_REFERER);
} else if ($email != "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
	$con->close();
	$flash->add("error", "Alamat email tidak benar", BASE_REFERER);
} else if ($jmlCustomer > 0) {
	$con->close();
	$flash->add("error", "Nama Customer sudah ada di Database", BASE_REFERER);
} else {
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	$sql1 = "insert ignore into pro_customer(id_marketing, nama_customer, alamat_customer, prov_customer, kab_customer, postalcode_customer, telp_customer, fax_customer, email_customer, created_time, created_ip, created_by, jenis_customer) values ('" . $marketing . "', '" . $nama . "', '" . $alamat . "', '" . $propinsi . "', '" . $kabupaten . "', '" . $postalcode_customer . "', '" . $telepon . "', '" . $fax . "', '" . $email . "', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "', '" . $jenis_customer . "')";
	if ($sesrol == 18) {
		$sql = "select * from acl_user where id_user = '" . $marketing . "'";
		$rsm = $con->getRecord($sql);
		$sql1 = "insert ignore into pro_customer(id_marketing, nama_customer, alamat_customer, prov_customer, kab_customer, postalcode_customer, telp_customer, fax_customer, email_customer, created_time, created_ip, created_by, jenis_customer) values ('" . $marketing . "', '" . $nama . "', '" . $alamat . "', '" . $propinsi . "', '" . $kabupaten . "', '" . $postalcode_customer . "', '" . $telepon . "', '" . $fax . "', '" . $email . "', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "', '" . $jenis_customer . "')";
		if ($seswil and $sesgroup) {
			$sql1 = "insert ignore into pro_customer(id_marketing, id_wilayah, id_group, nama_customer, alamat_customer, prov_customer, kab_customer, postalcode_customer, telp_customer, fax_customer, email_customer, created_time, created_ip, created_by, jenis_customer) values ('" . $marketing . "', '" . $rsm['id_wilayah'] . "', '" . $rsm['id_group'] . "', '" . $nama . "', '" . $alamat . "', '" . $propinsi . "', '" . $kabupaten . "', '" . $postalcode_customer . "', '" . $telepon . "', '" . $fax . "', '" . $email . "', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "', '" . $jenis_customer . "')";
		} else if (!$seswil and $sesgroup) {
			$sql1 = "insert ignore into pro_customer(id_marketing, id_group, nama_customer, alamat_customer, prov_customer, kab_customer, postalcode_customer, telp_customer, fax_customer, email_customer, created_time, created_ip, created_by, jenis_customer) values ('" . $marketing . "', '" . $rsm['id_group'] . "', '" . $nama . "', '" . $alamat . "', '" . $propinsi . "', '" . $kabupaten . "', '" . $postalcode_customer . "', '" . $telepon . "', '" . $fax . "', '" . $email . "', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "', '" . $jenis_customer . "')";
		}
	}
	$res1 = $con->setQuery($sql1);
	$oke  = $oke && !$con->hasError();

	$sql2 = "insert ignore into pro_customer_contact(id_customer) values ('" . $res1 . "')";
	$con->setQuery($sql2);
	$oke  = $oke && !$con->hasError();

	$sql3 = "insert ignore into pro_customer_logistik(id_customer) values ('" . $res1 . "')";
	$con->setQuery($sql3);
	$oke  = $oke && !$con->hasError();

	$sql4 = "insert ignore into pro_customer_payment(id_customer) values ('" . $res1 . "')";
	$con->setQuery($sql4);
	$oke  = $oke && !$con->hasError();

	$sql5 = "insert ignore into pro_customer_admin_arnya(id_customer) values ('" . $res1 . "')";
	$con->setQuery($sql5);
	$oke  = $oke && !$con->hasError();

	$url  = BASE_URL_CLIENT . "/customer.php";

	if ($oke) {
		$con->commit();
		$con->close();
		header("location: " . $url);
		exit();
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}
}

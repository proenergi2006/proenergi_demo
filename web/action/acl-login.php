<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
require_once($public_base_directory . "/libraries/helper/passwordHash.php");
load_helper();

if (isset($_SESSION["sinori" . SESSIONID])) {
	header("location: " . BASE_URL_CLIENT . "/home.php");
	exit();
}

$con 	= new Connection();
$flash	= new FlashAlerts;
$hasher = new PasswordHash(8, false);
$salt	= "&%ApaKabar*(@!";
$enk  	= decode($_SERVER['REQUEST_URI']);

$username	= htmlspecialchars($_POST["username"], ENT_QUOTES);
$password 	= htmlspecialchars($_POST["sandi"], ENT_QUOTES);
$latitude	= htmlspecialchars($_POST["latitude"], ENT_QUOTES);
$longitude 	= htmlspecialchars($_POST["longitude"], ENT_QUOTES);

if ($username == "" || $password == "") {
	$flash->add("error", "KOSONG", BASE_REFERER);
} else if (strlen($password . $salt) > 72) {
	$flash->add("error", "Password anda terlalu panjang", BASE_REFERER);
} else {
	$sql1 = "select a.id_user, a.id_role, a.id_wilayah, a.id_group, a.username, a.password, a.fullname, a.id_transportir, a.id_terminal, a.id_customer, a.department,  a.foto,
				 b.is_fleet, b.tipe_angkutan from acl_user a left join pro_master_transportir b on a.id_transportir = b.id_master 
				 where a.username = '" . $username . "' and a.is_active = 1";
	$res1 = $con->getRecord($sql1);
	$cek1 = isset($res1['id_user']) ? count($res1['id_user']) : 0;
	$cek2 = $hasher->CheckPassword($password . $salt, $res1['password']);
	if ($cek1 > 0 && $cek2 === true) {
		$last_login = date("Y-m-d H:i:s");
		$created_ip	= $_SERVER['REMOTE_ADDR'];
		$fleet = ($res1['is_fleet']) ? $res1['is_fleet'] : 0;
		$_SESSION["sinori" . SESSIONID]["id_user"] 	= paramEncrypt($res1['id_user']);
		$_SESSION["sinori" . SESSIONID]["department"] 	= paramEncrypt($res1['department']);
		$_SESSION["sinori" . SESSIONID]["foto"] = paramEncrypt($res1['foto']);
		$_SESSION["sinori" . SESSIONID]["id_role"] 	= paramEncrypt($res1['id_role']);
		$_SESSION["sinori" . SESSIONID]["id_wilayah"] = paramEncrypt($res1['id_wilayah']);
		$_SESSION["sinori" . SESSIONID]["id_group"] = paramEncrypt($res1['id_group']);
		$_SESSION["sinori" . SESSIONID]["username"] = paramEncrypt($res1['username']);
		$_SESSION["sinori" . SESSIONID]["fullname"] = paramEncrypt($res1['fullname']);
		$_SESSION["sinori" . SESSIONID]["suplier"] 	= paramEncrypt($res1['id_transportir']);
		$_SESSION["sinori" . SESSIONID]["terminal"] = paramEncrypt($res1['id_terminal']);
		$_SESSION["sinori" . SESSIONID]["customer"] = paramEncrypt($res1['id_customer']);
		$_SESSION["sinori" . SESSIONID]["fleet"] 	= paramEncrypt($fleet);
		$_SESSION["sinori" . SESSIONID]["angkutan"] = paramEncrypt($res1['tipe_angkutan']);
		$_SESSION["sinori" . SESSIONID]["checksum"] = paramEncrypt($res1['id_user'] . $res1['username']);
		$_SESSION["sinori" . SESSIONID]["timeout"] = time();
		$_SESSION["sinori" . SESSIONID]["last_login"] = paramEncrypt($last_login);

		$sql1 = "update acl_user set last_login_time = '" . $last_login . "', latitude = '$latitude', longitude = '$longitude', lastupdate_ip = '" . $created_ip . "' where id_user = '" . $res1['id_user'] . "'";
		$con->setQuery($sql1);

		$con->close();
		header("location: " . BASE_URL_CLIENT . "/home.php");
		exit();
	} else {
		$con->close();
		$flash->add("error", "username dan password anda tidak ditemukan", BASE_REFERER);
	}
}

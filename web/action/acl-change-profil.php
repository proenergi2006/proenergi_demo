<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;

$username 	= htmlspecialchars($_POST["username"], ENT_QUOTES);
$fullname 	= htmlspecialchars($_POST["fullname"], ENT_QUOTES);
$email 		= htmlspecialchars($_POST["email"], ENT_QUOTES);
$telepon 	= htmlspecialchars($_POST["telepon"], ENT_QUOTES);

if ($username == "" || $fullname == "" || $email == "" || $telepon == "") {
	$con->close();
	$flash->add("error", "KOSONG", BASE_REFERER);
} else {
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	$cek = "select * from acl_user where id_user <> '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_user"]) . "' and username = '" . $username . "' for update";
	$res = $con->getResult($cek);
	if (count($res) > 0) {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "Maaf username telah digunakan oleh user lain", BASE_REFERER);
	} else {
		$sql = "update acl_user set username = '" . $username . "', fullname = '" . $fullname . "', email_user = '" . $email . "', mobile_user = '" . $telepon . "' 
					where id_user = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_user"]) . "'";
		$con->setQuery($sql);
		$oke  = $oke && !$con->hasError();

		if ($oke) {
			$_SESSION["sinori" . SESSIONID]["username"] = paramEncrypt($username);
			$_SESSION["sinori" . SESSIONID]["fullname"] = paramEncrypt($fullname);
			$_SESSION["sinori" . SESSIONID]["checksum"] = paramEncrypt($res1['id_user'] . $username);
			$con->commit();
			$con->close();
			$flash->add("success", "profil telah berhasil diubah", BASE_URL_CLIENT . "/acl-change-profil.php");
		} else {
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "Maaf sistem mengalami kendala, silahkan coba lagi", BASE_REFERER);
		}
	}
}

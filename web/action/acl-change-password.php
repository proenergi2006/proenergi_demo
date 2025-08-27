<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	require_once ($public_base_directory."/libraries/helper/passwordHash.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$hasher = new PasswordHash(8, false);
	$salt	= "&%ApaKabar*(@!";

	$old_pass = '';
	if (!isset($_POST['idr']))
		$old_pass 	= htmlspecialchars($_POST["old_pass"], ENT_QUOTES);
		
	$new_pass 	= htmlspecialchars($_POST["new_pass"], ENT_QUOTES);
	$c_new_pass = htmlspecialchars($_POST["confirm_new_pass"], ENT_QUOTES);
	$idr = paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"]);
	
	$manual = false;
	if (isset($_POST['idr'])) {
		if (!is_numeric($_POST['idr'])) {
			$idr = paramDecrypt($_POST['idr']);
			$idr = explode('=', $idr);
			$idr = $idr[0]; // $idr[1]
		} else {
			$idr = $_POST['idr'];
			$manual = true;
		}
	}
	
	$hashPass 	= $hasher->HashPassword($new_pass.$salt);

	if ($new_pass == "" || $c_new_pass == "") {
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else if ($new_pass != $c_new_pass) {
		$con->close();
		$flash->add("error", "Konfirmasi password dan password baru harus sama", BASE_REFERER);
	} else if(strlen($new_pass.$salt) > 72) {
		$con->close();
		$flash->add("error", "Password anda terlalu panjang", BASE_REFERER);
	} else if(strlen($hashPass) < 20) {
		$con->close();
		$flash->add("error", "Maaf sistem mengalami kendala, silahkan coba lagi", BASE_REFERER);
	} else {
		if ($manual==true) {
			$sql = "update acl_user set password = '".$hashPass."' where id_user = '".$idr."'";
			$con->setQuery($sql);
			if(!$con->hasError()){
				$con->close();
				$url = BASE_URL_CLIENT."/acl-change-password.php";
				$url .= '?' . paramEncrypt('idr='.$idr);
				$flash->add("success", "Password telah berhasil diubah", $url);
			} else{
				$con->clearError();
				$con->close();
				$flash->add("error", "Maaf sistem mengalami kendala, silahkan coba lagi", BASE_REFERER);
			}
		} else {
			$query = "select password from acl_user where id_user = '".$idr."'";
			$pass = $con->getOne($query);
			
			$cek1 = $hasher->CheckPassword($old_pass.$salt, $pass);

			if($pass != "" && $cek1 === true) {
				$sql = "update acl_user set password = '".$hashPass."' where id_user = '".$idr."'";
				$con->setQuery($sql);
				if(!$con->hasError()){
					$con->close();
					$url = BASE_URL_CLIENT."/acl-change-password.php";

					if (isset($_POST['idr']))
						$url .= '?' . paramEncrypt('idr='.$idr);

					$flash->add("success", "Password telah berhasil diubah", $url);
				} else{
					$con->clearError();
					$con->close();
					$flash->add("error", "Maaf sistem mengalami kendala, silahkan coba lagi", BASE_REFERER);
				}
			} else{
				$con->close();
				$flash->add("error", "Password lama tidak sesuai", BASE_REFERER);		
			}
		}
	}
?>

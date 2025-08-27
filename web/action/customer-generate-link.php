<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$idr 	= isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
	$url 	= BASE_URL_CLIENT."/customer-generate-link.php";
	
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	$sql1 = "update pro_customer set need_update = 1, count_update = 0, is_verified = 0, is_generated_link = 1 where id_customer = '".$idr."'";
	$con->setQuery($sql1);
	$oke  = $oke && !$con->hasError();

	$sql2 = "update pro_customer_verification set is_active = 0 where id_customer = '".$idr."'";
	$con->setQuery($sql2);
	$oke  = $oke && !$con->hasError();

	$sql3 = "insert ignore into pro_customer_verification(id_customer, token_verification) values ('".$idr."', '".mt_rand(100, 999).date('dmYHis')."')";
	$con->setQuery($sql3);
	$oke  = $oke && !$con->hasError();

	if ($oke){
		$con->commit();
		$con->close();
		header("location: ".$url);	
		exit();
	} else{
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}
?>

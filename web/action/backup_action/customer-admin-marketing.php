<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	require_once ($public_base_directory."/libraries/helper/passwordHash.php");
	load_helper("autoload", "mailgen", "htmlawed");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);	
	$market = htmlspecialchars($_POST["market"], ENT_QUOTES);	

	if($market == ""){
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else{
		$sql = "update pro_customer set id_marketing = '".$market."' where id_customer = '".$idr."'";
		$con->setQuery($sql);
		
		if (!$con->hasError()){
			$con->close();
			$flash->add("success", "Marketing Customer telah berhasil diubah", BASE_URL_CLIENT."/customer-admin-detail.php?".paramEncrypt("idr=".$idr));
		} else{
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
	}
?>

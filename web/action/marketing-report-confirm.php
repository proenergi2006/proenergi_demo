<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload", "htmlawed");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$idr	= isset($enk['idr'])?$enk['idr']:null;

	$id_marketing_report = $idr;
	
	if ($id_marketing_report) {
		$oke = true;
		$con->beginTransaction();
		$con->clearError();
		$sql1 = "
		update pro_marketing_report set 
			technical_support_status = 1,
			technical_support_date = NOW()
		where 
			id_marketing_report = '".$id_marketing_report."'";
		$res1 = $con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();

		if ($oke){
			$con->commit();
			$con->close();
			$flash->add("success", "Data berhasil di confirm", BASE_REFERER);
			exit();
		} else{
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
	}
?>

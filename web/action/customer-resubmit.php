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
	$idk 	= htmlspecialchars($enk["idk"], ENT_QUOTES);
	$url 	= BASE_URL_CLIENT."/customer-generate-link.php";
	
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	$sql1 = "update pro_customer_verification set is_evaluated = 0, legal_result = 0, finance_result = 0, logistik_result = 0 where id_verification = '".$idk."'";
	$con->setQuery($sql1);
	$oke  = $oke && !$con->hasError();

	$sql3 = "select count_update from pro_customer where id_customer = '".$idr."'";
	$row  = $con->getRecord($sql3);
	$oke  = $oke && !$con->hasError();

	$count_update = $row['count_update'];
	
	if($row['count_update'] == 2)
		$count_update = 1; // need edit

	$sql2 = "update pro_customer set need_update = 1, count_update = '".$count_update."' where id_customer = '".$idr."'";
	$con->setQuery($sql2);
	$oke  = $oke && !$con->hasError();

	if ($oke){
		$con->commit();
		$con->close();
		$flash->add("success", "Form update data dengan kode link LC-".$idk." telah dibuka untuk direvisi", $url);
	} else{
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}
?>

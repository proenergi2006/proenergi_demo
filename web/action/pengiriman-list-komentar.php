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
	$act	= ($enk['act'] == "")?htmlspecialchars($_POST["act"], ENT_QUOTES):$enk['act'];
	$pic 	= paramDecrypt($_SESSION["sinori".SESSIONID]["fullname"]);
	$term 	= paramDecrypt($_SESSION["sinori".SESSIONID]["terminal"]);
	$answer	= array();
	
	$komentar 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["komentar"], ENT_QUOTES));	
	$rating 	= htmlspecialchars($_POST["rating"], ENT_QUOTES);	
	$idnya		= htmlspecialchars($_POST["idnya"], ENT_QUOTES);	
	$tipe 		= htmlspecialchars($_POST["tipe"], ENT_QUOTES);	

	$oke = true;
	$con->beginTransaction();
	$con->clearError();		

	$arrSql = array(1=>array("table"=>"pro_po_ds_detail", "key"=>"id_dsd"), array("table"=>"pro_po_ds_kapal", "key"=>"id_dsk"));
	$sql1 = "update ".$arrSql[$tipe]["table"]." set rating = '".$rating."', komentar = '".$komentar."' where ".$arrSql[$tipe]["key"]." = '".$idnya."'";
	$con->setQuery($sql1);
	$oke  = $oke && !$con->hasError();

	if($oke){
		$con->commit();
		$con->close();
	} else{
		$con->rollBack();
		$con->clearError();
		$con->close();
	}
?>

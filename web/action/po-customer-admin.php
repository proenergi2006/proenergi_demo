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
	$act	= ($enk['act'] == "")?htmlspecialchars($_POST["act"], ENT_QUOTES):$enk['act'];
	$idq1 	= htmlspecialchars($_POST["idq1"], ENT_QUOTES);	
	$idq2 	= htmlspecialchars($_POST["idq2"], ENT_QUOTES);	
	$url 	= BASE_URL_CLIENT."/po-customer-admin.php?".paramEncrypt("q1=".$idq1."&q2=".$idq2);

	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	foreach($_POST['cek'] as $idx=>$val){
		$idr = htmlspecialchars($_POST['idr'][$idx], ENT_QUOTES);
		$dt1 = htmlspecialchars($_POST['dt1'][$idx], ENT_QUOTES);
		$dt2 = htmlspecialchars($_POST['dt2'][$idx], ENT_QUOTES);
		$dt3 = htmlspecialchars(str_replace(array(",","."), array("",""), $_POST['dt3'][$idx]), ENT_QUOTES);
		$dt4 = htmlspecialchars(str_replace(array(",","."), array("",""), $_POST['dt4'][$idx]), ENT_QUOTES);
		$dt5 = htmlspecialchars(str_replace(array(",","."), array("",""), $_POST['dt5'][$idx]), ENT_QUOTES);
		$dt6 = htmlspecialchars(str_replace(array(",","."), array("",""), $_POST['dt6'][$idx]), ENT_QUOTES);
		$dt7 = htmlspecialchars($_POST['dt7'][$idx], ENT_QUOTES);
		
		$sql1 = "update pro_customer set kode_pelanggan = '".$dt2."' where id_customer = '".$idr."'";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();
		
		$sql2 = "update pro_po_customer_plan set top_plan = '".$dt1."', pelanggan_plan = '".$dt2."', ar_notyet = '".$dt3."', ar_satu = '".$dt4."', ar_dua = '".$dt5."', 
				 kredit_limit = '".$dt6."', actual_top_plan = '".$dt7."' where id_plan = '".$idx."'";
		$con->setQuery($sql2);
		$oke  = $oke && !$con->hasError();
	}
	if ($oke){
		$con->commit();
		$con->close();
		$flash->add("success", "Data telah disimpan", $url);
	} else{
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "GAGAL_MASUK", $url);
	}
?>

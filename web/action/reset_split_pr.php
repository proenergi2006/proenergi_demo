<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload", "htmlawed");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	
	$idnya 	= explode("|#|", paramDecrypt($_GET['idnya']));
	

	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 9){
		$url 	= BASE_URL_CLIENT."/purchase-request-detail.php?".paramEncrypt("idr=".$idnya[2]);
		
		$cek01 	= "select splitted_from from pro_pr_detail where id_plan = '".$idnya[0]."' and id_prd = '".$idnya[1]."'";
		$res01 	= $con->getRecord($cek01);

		$cek02 	= "
			select sum(volume) as volumenya  
			from pro_pr_detail 
			where id_plan = '".$idnya[0]."' and id_prd != '".$res01['splitted_from']."' and splitted_from = '".$res01['splitted_from']."'
		";
		$res02 = $con->getRecord($cek02);

		$sql1 = "delete from pro_pr_detail where id_plan = '".$idnya[0]."' and id_prd != '".$res01['splitted_from']."' and splitted_from = '".$res01['splitted_from']."'";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();

		$sql2 = "update pro_pr_detail set volume = volume + ".$res02['volumenya'].", splitted_from = NULL where id_prd = '".$res01['splitted_from']."'";
		$con->setQuery($sql2);
		$oke  = $oke && !$con->hasError();
	}

	if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 5){
		$url 	= BASE_URL_CLIENT."/purchase-request-detail.php?".paramEncrypt("idr=".$idnya[2]);
		$sql1 = "delete from pro_pr_detail where 1=1 and splitted_from_pr = '".$idnya[0]."'";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();

		$sql2 = "update pro_pr_detail set volume = vol_ori_pr, splitted_from_pr = NULL where id_prd = '".$idnya[0]."'";
		$con->setQuery($sql2);
		$oke  = $oke && !$con->hasError();
	}
	// exit();

	if ($oke){
		$con->commit();
		$con->close();
		$flash->add("success", "Data DR telah berhasil disimpan", $url);
	} else{
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}
?>

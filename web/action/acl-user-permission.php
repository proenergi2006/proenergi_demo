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
	$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
	
	if($idr == ""){
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else{
		$icn  = "success";
		$msg  = "SUKSES_MASUK";
		$oke = true;
		$con->beginTransaction();
		$con->clearError();


		$sql1 = "select * from acl_role_permission where id_user = '".$idr."'";
		$res1 = $con->getResult($sql1);
		foreach($res1 as $data1){
			$idmEncrypt = paramEncrypt($data1['id_menu']);
			$resP = $con->getResult("select permission from acl_permission");
			$arrP = array();
			foreach($resP as $val){
				$arrP[$val['permission']] = htmlspecialchars($_POST[$val['permission']][$idmEncrypt], ENT_QUOTES);
			}
			$permission = json_encode($arrP);
			$sql2 = "update acl_role_permission set permission = '".$permission."' where id_user = '".$idr."' and id_menu = '".$data1['id_menu']."'";
			$con->setQuery($sql2);
			$oke  = $oke && !$con->hasError();
		}

		if ($oke){
			$con->commit();
			$con->close();
			$flash->add($icn, $msg, BASE_REFERER);
		} else{
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
	}	
?>

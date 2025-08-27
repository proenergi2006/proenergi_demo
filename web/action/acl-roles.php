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
	
	$role_name	= htmlspecialchars($_POST["role_name"], ENT_QUOTES);	
	$role_desc 	= htmlspecialchars($_POST["role_desc"], ENT_QUOTES);	
	
	$active = htmlspecialchars($_POST["active"], ENT_QUOTES);
	$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);	
	
	if($role_name == "" || $role_desc == ""){
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else{
		if($act == 'add'){
			$sql = "insert into acl_role(role_name, role_desc, is_active, created_time, created_ip, created_by) values ('".$role_name."', '".$role_desc."', '".$active."', NOW(), '".$_SERVER['REMOTE_ADDR']."', '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."')";
			$msg = "GAGAL_MASUK";
		} else if($act == 'update'){
			$sql = "update acl_role set role_name = '".$role_name."', role_desc = '".$role_desc."', is_active = '".$active."', lastupdate_time = NOW(), lastupdate_ip = '".$_SERVER['REMOTE_ADDR']."', lastupdate_by = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."' where id_role = ".$idr;
			$msg = "GAGAL_UBAH";
		}
		
		$con->setQuery($sql);
		if(!$con->hasError()){
			$con->close();
			header("location: ".BASE_URL_CLIENT."/acl-roles.php");	
			exit();				
		} else{
			$con->clearError();
			$con->close();
			$flash->add("error", $msg, BASE_REFERER);
		}
	}
?>

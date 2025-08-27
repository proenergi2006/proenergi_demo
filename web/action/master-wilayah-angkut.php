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
	
	$propinsi	= htmlspecialchars($_POST["propinsi"], ENT_QUOTES);	
	$kabupaten	= htmlspecialchars($_POST["kabupaten"], ENT_QUOTES);	
	$destinasi	= htmlspecialchars($_POST["destinasi"], ENT_QUOTES);	
	$active 	= htmlspecialchars($_POST["active"], ENT_QUOTES);
	$idr 		= htmlspecialchars($_POST["idr"], ENT_QUOTES);	
	
	if($propinsi == "" && $kabupaten == "" && $destinasi == ""){
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else{
		if($act == 'add'){
			$sql = "insert into pro_master_wilayah_angkut(id_prov, id_kab, wilayah_angkut, is_active, created_time, created_ip, created_by) values ('".$propinsi."', '".$kabupaten."', '".strtoupper($destinasi)."', '".$active."', NOW(), '".$_SERVER['REMOTE_ADDR']."', '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."')";
			$msg = "GAGAL_MASUK";
		} else if($act == 'update'){
			$sql = "update pro_master_wilayah_angkut set id_prov = '".$propinsi."', id_kab = '".$kabupaten."', wilayah_angkut = '".strtoupper($destinasi)."', is_active = '".$active."', lastupdate_time = NOW(), lastupdate_ip = '".$_SERVER['REMOTE_ADDR']."', lastupdate_by = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."' where id_master = ".$idr;
			$msg = "GAGAL_UBAH";
		}
		
		$con->setQuery($sql);
		if(!$con->hasError()){
			$con->close();
			header("location: ".BASE_URL_CLIENT."/master-wilayah-angkut.php");
			exit();				
		} else{
			$con->clearError();
			$con->close();
			$flash->add("error", $msg, BASE_REFERER);
		}
	}
?>

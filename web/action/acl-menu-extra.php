<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$act	= ($enk['act'] == "")?htmlspecialchars($_POST["act"], ENT_QUOTES):$enk['act'];
	$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
	
	$menu_name		= htmlspecialchars($_POST["menu_name"], ENT_QUOTES);
	$menu_link 		= htmlspecialchars($_POST["menu_link"], ENT_QUOTES);
	$menu_parent 	= htmlspecialchars($_POST["menu_parent"], ENT_QUOTES);
	
	if($act == 'delete'){
		$idr = $enk['idr'];
		$sql = "delete from acl_menu_extra where id_menu_extra = '".$idr."'";
		$con->setQuery($sql);
		if (!$con->hasError()){
			$con->close();
			header("location: ".BASE_URL_CLIENT."/acl-menu-extra.php?".paramEncrypt("pesan=Data telah dihapus"));	
			exit();				
		} else{
			$con->clearError();
			$con->close();
			header("location: ".BASE_URL_CLIENT."/acl-menu-extra.php?".paramEncrypt("pesan=Data gagal dihapus"));	
			exit();				
		}
	} 
	else{
		if($menu_name != "" && $menu_link != "" && $menu_parent != ""){
			if($act == 'add'){
				$sql = "insert into acl_menu_extra(menu_extra_name, menu_extra_parent, menu_extra_link, menu_extra_level, created_time, created_ip, created_by) values ('".$menu_name."', '".$menu_parent."', '".$menu_link."', '0', NOW(), '".$_SERVER['REMOTE_ADDR']."', '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."')";
				$msg = "GAGAL_MASUK";
			} else if($act == 'update'){
				$sql = "update acl_menu_extra set menu_extra_name = '".$menu_name."', menu_extra_link = '".$menu_link."', lastupdate_time = NOW(), lastupdate_ip = '".$_SERVER['REMOTE_ADDR']."', lastupdate_by = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."' where id_menu_extra = ".$idr;
				$msg = "GAGAL_UBAH";
			}
			
			$con->setQuery($sql);
			if(!$con->hasError()){
				$con->close();
				header("location: ".BASE_URL_CLIENT."/acl-menu-extra.php?".paramEncrypt("pesan=Data telah disimpan"));	
				exit();				
			} else{
				$con->clearError();
				$con->close();
				header("location: ".BASE_URL_CLIENT."/acl-menu-extra.php?".paramEncrypt("pesan=Data gagal disimpan"));	
				exit();				
			}
		}
	}
?>

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
	
	$menu_name		= htmlspecialchars($_POST["menu_name"], ENT_QUOTES);	
	$menu_link 		= htmlspecialchars($_POST["menu_link"], ENT_QUOTES);	
	$menu_parent 	= htmlspecialchars($_POST["menu_parent"], ENT_QUOTES);	
	
	$active = htmlspecialchars($_POST["active"], ENT_QUOTES);
	$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);	
	
	if($act == 'delete'){
		$idr = $enk['idr'];
		unset($enk['idr'], $enk['act']);
		foreach($enk as $key => $value){
			if(!empty($value))
				$ref .= '&'.$key.'='.$value;
		}
		$sql = "delete from acl_menu where menu_tree = '".$idr."' or menu_tree like '".$idr.".%'";
		$idrm = $con->setQuery($sql);
		if (!$con->hasError()){
			$con->close();
			header("location: ".REFERER."?".paramEncrypt('1=1'.$ref));
			exit();				
		} else{
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_HAPUS", BASE_REFERER);
		}
	} 
	else{
		if($menu_name == "" || $menu_parent == ""){
			$flash->add("error", "KOSONG", BASE_REFERER);
		} else{
			if($act == 'add'){
				$con->beginTransaction();
				$move = true;

				$sql1 = "insert into acl_seq_menu values(NULL)";
				$res1 = $con->setQuery($sql1);
				$move = $move && !$con->hasError();
				$con->clearError();
				
				$sql2 = "delete from acl_seq_menu where id_seq <> '".$res1."'";
				$res2 = $con->setQuery($sql2);
				$move = $move && !$con->hasError();
				$con->clearError();

				str_replace(".", "-", $menu_parent, $nomor);
				$cek_order	= $con->getOne("select menu_order from acl_menu where menu_tree = '".$menu_parent."'");
				$menu_tree 	= ($menu_parent == '-')?str_pad($res1,3,'0',STR_PAD_LEFT):$menu_parent.".".str_pad($res1,3,'0',STR_PAD_LEFT);
				$menu_level	= ($menu_parent == '-')?1:$nomor + 2;
				$menu_order	= ($menu_parent == '-')?$menu_tree:$cek_order.".".str_pad($res1,3,'0',STR_PAD_LEFT);

				$sql3 = "insert into acl_menu(menu_tree, menu_name, menu_parent, menu_link, menu_level, menu_order, is_active, created_time, created_ip, created_by) values ('".$menu_tree."', '".$menu_name."', '".$menu_parent."', '".$menu_link."', '".$menu_level."', '".$menu_order."', '".$active."', NOW(), '".$_SERVER['REMOTE_ADDR']."', '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."')";
				$res3 = $con->setQuery($sql3);
				$move = $move && !$con->hasError();
				$con->clearError();
				
				$msg = "GAGAL_MASUK";
			} else if($act == 'update'){
				$con->beginTransaction();
				$move = true;

				$sql1 = "update acl_menu set menu_name = '".$menu_name."', menu_link = '".$menu_link."', is_active = '".$active."', lastupdate_time = NOW(), lastupdate_ip = '".$_SERVER['REMOTE_ADDR']."', lastupdate_by = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."' where id_menu = ".$idr;
				$res1 = $con->setQuery($sql1);
				$move = $move && !$con->hasError();
				$con->clearError();

				$msg = "GAGAL_UBAH";
			}
		
			if($move){
				$con->commit();
				$con->close();
				header("location: ".BASE_URL_CLIENT."/acl-menu.php");	
				exit();				
			} else{
				$con->rollBack();
				$con->close();
				$flash->add("error", $msg, BASE_REFERER);
			}
		}
	}
?>

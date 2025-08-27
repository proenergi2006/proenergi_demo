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
	$idu 	= htmlspecialchars($_POST["idu"], ENT_QUOTES);
	$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
	$role 	= htmlspecialchars($_POST["role"], ENT_QUOTES);
	
	if($idr == "" || $idu == "" || $role == ""){
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else if($idr == $role){
		$flash->add("warning", "Sistem tidak merubah data, karena role baru masih sama dengan role sebelumnya", BASE_REFERER);
	} else{
		$icn  = "success";
		$msg  = "SUKSES_MASUK";
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		$sql1 = "update acl_user set id_role = '".$role."' where id_user = '".$idu."'";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();
	
		$sql2 = "delete from acl_role_permission where id_user = '".$idu."'";
		$con->setQuery($sql2);
		$oke  = $oke && !$con->hasError();
	
		$resP = $con->getResult("select permission from acl_permission");
		$arrP = array();
		foreach($resP as $val){
			$arrP[$val['permission']] = 1;
		}
		$permission = json_encode($arrP);
		$sqlInsert = "
			insert into acl_role_permission(id_user, id_role, id_menu, permission)
			(
				select '".$idu."' as id_user, '".$role."' as id_role, a.id_menu, '".$permission."' as permission
				from acl_menu a join acl_role_menu b on a.id_menu = b.id_menu and b.id_role  = '".$role."'
				where a.menu_level <> 0 and a.is_active = 1
			)";
		$con->setQuery($sqlInsert);
		$oke = $oke && !$con->hasError();

		if ($oke){
			$con->commit();
			$con->close();
			$flash->add($icn, $msg, BASE_URL_CLIENT.'/acl-user-roles.php?'.paramEncrypt('idu='.$idu.'&idr='.$role));
		} else{
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
	}	
?>

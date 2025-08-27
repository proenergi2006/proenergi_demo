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
	
	$oke = true;
	$con->beginTransaction();
	$con->clearError();
	$sql 	= "delete from acl_role_menu where id_role = '".$idr."'";
	$res 	= $con->setQuery($sql);
	$oke  	= $oke && !$con->hasError();
	foreach($_POST["menu"] as $idx=>$val){
		$idm 	= htmlspecialchars($val, ENT_QUOTES);
		if($idm != ""){
			$sql1 	= "insert into acl_role_menu(id_role, id_menu) values ('".$idr."', '".$idm."')";
			$res1 	= $con->setQuery($sql1);
			$oke  	= $oke && !$con->hasError();
		}
	}
	$build 	= getRebuildUserMenu($oke, $con, $idr);
	$oke  	= $oke && $build;

	if ($oke){
		$con->commit();
		$con->close();
		$flash->add("success", "SUKSES_MASUK", BASE_REFERER);
	} else{
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}
	
	function getRebuildUserMenu($oke, $con, $id_role){
		$sql1 = "select id_user from acl_user where id_role = '".$id_role."'";
		$res1 = $con->getResult($sql1);
		$jum1 = count($res1);
		if($jum1 > 0){
			$resP = $con->getResult("select permission from acl_permission");
			$arrP = array();
			foreach($resP as $val){
				$arrP[$val['permission']] = 1;
			}
			$permission = json_encode($arrP);

			$sql2 = "select id_menu from acl_role_menu where id_role = '".$id_role."'";
			$res2 = $con->getResult($sql2);
			
			$sql3 = "delete from acl_role_permission where id_role = '".$id_role."' and id_menu not in(".$sql2.")";
			$con->setQuery($sql3);
			$oke  = $oke && !$con->hasError();

			$sql4 = "select distinct id_menu from acl_role_permission where id_role = '".$id_role."'";
			$res4 = $con->getResult($sql4);
			foreach($res2 as $data2){
				if(!in_array(array("id_menu"=>$data2['id_menu'], "0"=>$data2['id_menu']), $res4)){
					$sqlInsert = "
						insert into acl_role_permission(id_user, id_role, id_menu, permission)
						(
							select id_user, '".$id_role."' as id_role, '".$data2['id_menu']."' as id_menu, '".$permission."' as permission
							from acl_user where id_role  = '".$id_role."'
						)";
					$con->setQuery($sqlInsert);
					$oke = $oke && !$con->hasError();
				}
			}
		}
		return $oke;
	}
?>

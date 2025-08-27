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
	$act	= isset($enk['act'])?$enk['act']:'';
	if ($act=='') $act = htmlspecialchars($_POST["act"], ENT_QUOTES);
	$idr 	= isset($_POST["idr"])?htmlspecialchars($_POST["idr"], ENT_QUOTES):null;	
	$active = isset($_POST["active"])?htmlspecialchars($_POST["active"], ENT_QUOTES):null;
	
	$wilayah_angkut	= htmlspecialchars($_POST["wilayah"], ENT_QUOTES);	
	$id_transportir = htmlspecialchars($_POST["transportir"], ENT_QUOTES);

	if($wilayah_angkut == "" || $id_transportir == ""){
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else if(is_array_empty($_POST["ongkos"])){
		$con->close();
		$flash->add("error", "Ongkos angkut belum diisi", BASE_REFERER);
	} else{
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		if($act == 'add'){
			$msg = "GAGAL_MASUK";
			foreach($_POST["ongkos"] as $idx=>$nilai){
				if ($nilai=='') continue;
				$ongkos_angkut	= htmlspecialchars(str_replace(array(".",","), array("",""), $nilai), ENT_QUOTES);
				$volume_angkut	= htmlspecialchars($idx, ENT_QUOTES);

				$sql1 = "insert into pro_master_ongkos_angkut(id_transportir, id_wil_angkut, id_prov_angkut, id_kab_angkut, ongkos_angkut, id_vol_angkut, created_time, created_ip, created_by) (select '".$id_transportir."', '".$wilayah_angkut."', id_prov, id_kab, '".$ongkos_angkut."', '".$volume_angkut."', NOW(), '".$_SERVER['REMOTE_ADDR']."', '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."' from pro_master_wilayah_angkut where id_master = '".$wilayah_angkut."')";
				$con->setQuery($sql1);
				$oke  = $oke && !$con->hasError();
			}
		} else if($act == 'update'){
			$msg = "GAGAL_UBAH";
			$sql1 = "delete from pro_master_ongkos_angkut where id_transportir = '".$id_transportir."' and id_wil_angkut = '".$wilayah_angkut."'";
			$con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();
			foreach($_POST["ongkos"] as $idx=>$nilai){
				if ($nilai=='') continue;
				$ongkos_angkut	= htmlspecialchars(str_replace(array(".",","), array("",""), $nilai), ENT_QUOTES);
				$volume_angkut	= htmlspecialchars($idx, ENT_QUOTES);

				$sql2 = "insert into pro_master_ongkos_angkut(id_transportir, id_wil_angkut, id_prov_angkut, id_kab_angkut, ongkos_angkut, id_vol_angkut, created_time, created_ip, created_by) (select '".$id_transportir."', '".$wilayah_angkut."', id_prov, id_kab, '".$ongkos_angkut."', '".$volume_angkut."', NOW(), '".$_SERVER['REMOTE_ADDR']."', '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."' from pro_master_wilayah_angkut where id_master = '".$wilayah_angkut."')";
				$con->setQuery($sql2);
				$oke  = $oke && !$con->hasError();
			}
		}
		
		if ($oke){
			$con->commit();
			$con->close();
			header("location: ".BASE_URL_CLIENT."/master-ongkos-angkut.php");
			exit();				
		} else{
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", $msg, BASE_REFERER);
		}
	}
?>

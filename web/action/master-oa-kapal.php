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
	$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);	
	
	if($act == "add"){
		if(is_array_empty($_POST["dt1"])){
			$con->close();
			$flash->add("error", "KOSONG", BASE_REFERER);
		} else{
			$oke = true;
			$con->beginTransaction();
			$con->clearError();

			foreach($_POST["dt1"] as $idx=>$val){
				$dt1 = htmlspecialchars($_POST["dt1"][$idx], ENT_QUOTES);
				$dt2 = htmlspecialchars($_POST["dt2"][$idx], ENT_QUOTES);
				$dt3 = htmlspecialchars($_POST["dt3"][$idx], ENT_QUOTES);
				$dt4 = htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["dt4"][$idx]), ENT_QUOTES);
				$dt5 = htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["dt5"][$idx]), ENT_QUOTES);
				$dt6 = htmlspecialchars($_POST["dt6"][$idx], ENT_QUOTES);
				$dt7 = htmlspecialchars($_POST["dt7"][$idx], ENT_QUOTES);
				$dt8 = htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["dt8"][$idx]), ENT_QUOTES);
				
				$sql = "insert into pro_master_oa_kapal(id_transportir, asal_angkut, tujuan_angkut, volume_angkut, harga_angkut, nama_kapal, tipe_kapal, max_kapal, created_time, created_ip, created_by) values ('".$dt1."', '".$dt2."', '".$dt3."', '".$dt4."', '".$dt5."', '".$dt6."', '".$dt7."', '".$dt8."', NOW(), '".$_SERVER['REMOTE_ADDR']."', '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."')";
				$con->setQuery($sql);
				$oke  = $oke && !$con->hasError();
			}
			if ($oke){
				$con->commit();
				$con->close();
				header("location: ".BASE_URL_CLIENT."/master-oa-kapal.php");	
				exit();
			} else{
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
			}
		}
	}
	
	else if($act == "update"){
		$dt1 = htmlspecialchars($_POST["dt1"], ENT_QUOTES);	
		$dt2 = htmlspecialchars($_POST["dt2"], ENT_QUOTES);	
		$dt3 = htmlspecialchars($_POST["dt3"], ENT_QUOTES);	
		$dt4 = htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["dt4"]), ENT_QUOTES);
		$dt5 = htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["dt5"]), ENT_QUOTES);
		$dt6 = htmlspecialchars($_POST["dt6"], ENT_QUOTES);
		$dt7 = htmlspecialchars($_POST["dt7"], ENT_QUOTES);
		$dt8 = htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["dt8"]), ENT_QUOTES);
		if($dt1 == "" || $dt2 == "" || $dt3 == "" || $dt4 == "" || $dt5 == ""){
			$con->close();
			$flash->add("error", "KOSONG", BASE_REFERER);
		} else{
			$oke = true;
			$con->beginTransaction();
			$con->clearError();

			$sql = "update pro_master_oa_kapal set id_transportir = '".$dt1."', asal_angkut = '".$dt2."', tujuan_angkut = '".$dt3."', volume_angkut = '".$dt4."', harga_angkut = '".$dt5."', nama_kapal = '".$dt6."', tipe_kapal = '".$dt7."', max_kapal = '".$dt8."', lastupdate_time = NOW(), lastupdate_ip = '".$_SERVER['REMOTE_ADDR']."', lastupdate_by = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."' where id_master = ".$idr;
			$con->setQuery($sql);
			$oke  = $oke && !$con->hasError();

			if ($oke){
				$con->commit();
				$con->close();
				header("location: ".BASE_URL_CLIENT."/master-oa-kapal.php");	
				exit();
			} else{
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", "GAGAL_UBAH", BASE_REFERER);
			}
		}
	}

?>

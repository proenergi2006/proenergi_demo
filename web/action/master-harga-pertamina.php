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

	$periode_awal	= htmlspecialchars($_POST["periode_awal"], ENT_QUOTES);	
	$periode_akhir 	= htmlspecialchars($_POST["periode_akhir"], ENT_QUOTES);
	$user_ip		= $_SERVER['REMOTE_ADDR'];
	$user_pic		= paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname']);
	$back_link 		= BASE_URL_CLIENT."/master-harga-pertamina.php";

	if($act == "add"){
		if($periode_awal == "" || $periode_akhir == ""){
			$con->close();
			$flash->add("error", "KOSONG", BASE_REFERER);
		} else{
			$oke = true;
			$con->beginTransaction();
			$con->clearError();

			$msg = "GAGAL_MASUK";
			foreach($_POST["area"] as $idx=>$nilai){
				$area	= htmlspecialchars($_POST["area"][$idx], ENT_QUOTES);	
				$produk	= htmlspecialchars($_POST["produk"][$idx], ENT_QUOTES);
				$harga 	= htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["harga"][$idx]), ENT_QUOTES);
				if($area && $produk && $harga){
					$sql = "insert into pro_master_harga_pertamina(periode_awal, periode_akhir, id_area, id_produk, harga_minyak, created_time, created_ip, created_by) 
							values ('".tgl_db($periode_awal)."', '".tgl_db($periode_akhir)."', '".$area."', '".$produk."', '".$harga."', NOW(), '".$user_ip."', '".$user_pic."')";
					$con->setQuery($sql);
					$oke  = $oke && !$con->hasError();
				}
			}
		}
	} 
	
	else if($act == "update"){
		$area	= htmlspecialchars($_POST["area"], ENT_QUOTES);	
		$produk	= htmlspecialchars($_POST["produk"], ENT_QUOTES);
		$harga 	= htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["harga"]), ENT_QUOTES);
		
		if($periode_awal == "" || $periode_akhir == "" || $area == "" || $produk == "" || $harga == ""){
			$con->close();
			$flash->add("error", "KOSONG", BASE_REFERER);
		} else{
			$oke = true;
			$con->beginTransaction();
			$con->clearError();

			$msg = "GAGAL_UBAH";
			$sql = "update pro_master_harga_pertamina set harga_minyak = '".$harga."', lastupdate_time = NOW(), lastupdate_ip = '".$user_ip."', lastupdate_by = '".$user_pic."' 
					where periode_awal = '".tgl_db($periode_awal)."' and periode_akhir = '".tgl_db($periode_akhir)."' and id_area = '".$area."' and id_produk = '".$produk."'";
			$con->setQuery($sql);
			$oke  = $oke && !$con->hasError();
		}
	}

	if ($oke){
		$con->commit();
		$con->close();
		header("location: ".$back_link);
		exit();				
	} else{
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", $msg, BASE_REFERER);
	}
?>

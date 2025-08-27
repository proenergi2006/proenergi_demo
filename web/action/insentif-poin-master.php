<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload", "htmlawed");
	

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$enk  	= decode($_SERVER['REQUEST_URI']);
	
	$act	= isset($enk['act'])?$enk['act']:htmlspecialchars($_POST["act"], ENT_QUOTES);
	$idr	= isset($enk['idr'])?null:htmlspecialchars($_POST["idr"], ENT_QUOTES);
	$idMaster	= isset($enk['idmaster'])?null:htmlspecialchars($_POST["idmaster"], ENT_QUOTES);

	$id_ruangan = $idr;
	$id_user = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);


	$tier = htmlspecialchars($_POST["tier"], ENT_QUOTES);
	$rangeAwal = htmlspecialchars($_POST["range_awal"], ENT_QUOTES);
	$rangeAkhir = htmlspecialchars($_POST["range_akhir"], ENT_QUOTES);
	$jenisPelunasan = htmlspecialchars($_POST["jenis_pelunasan"], ENT_QUOTES);
	$poin = htmlspecialchars($_POST["poin"], ENT_QUOTES);
	// $customer_date = date("Y-m-d", strtotime(str_replace('/', '-', $_POST["customer_date"])));
	if($act == "add"){
		$con->beginTransaction();
        
        
		$sql1 = "
					INSERT INTO pro_master_poin_insentif
							(JENIS_PELUNASAN,
							RANGE_AWAL,
							RANGE_AKHIR,
							TIER,
							POIN,
							PETUGAS_REKAM,
							TGL_REKAM)
					VALUES
							('".$jenisPelunasan."',
							'".$rangeAwal."',
							'".$rangeAkhir."',
							'".$tier."',
							'".$poin."',
							'".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."',
							now())";

		
		
		$res1 = $con->setQuery($sql1);
		$oke  = !$con->hasError();
		$url = BASE_URL_CLIENT."/insentif-poin-master.php";
		$msg = "Data behasil disimpan";
		


		if ($oke){
			$con->commit();
			$con->close();
			$flash->add("success", $msg, $url);
		} else{
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
		
	}

	if($act == "update"){
		
		$con->beginTransaction();
		$con->clearError();
		$sql1 = "
			UPDATE pro_master_poin_insentif
			SET
				JENIS_PELUNASAN = '".$jenisPelunasan."',
				RANGE_AWAL = '".$rangeAwal."',
				RANGE_AKHIR = '".$rangeAkhir."',
				TIER = '".$tier."',
				POIN = '".$poin."',
				PETUGAS_UBAH = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."',
				TGL_UBAH = now()
			WHERE id_master = '".$idMaster."'
		";
		
		$res1 = $con->setQuery($sql1);
		$oke  = !$con->hasError();
		$url = BASE_URL_CLIENT."/insentif-poin-master.php";

		if ($oke){
			$con->commit();
			$con->close();
			header("location: ".$url);	
			exit();
		} else{
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
		
	}
?>

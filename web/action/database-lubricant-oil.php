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

	$id_database_lubricant_oil = $idr;
	$nama_customer = htmlspecialchars($_POST['nama_customer'], ENT_QUOTES);
	$jenis_oil = htmlspecialchars($_POST['jenis_oil'], ENT_QUOTES);
	$spesifikasi = htmlspecialchars($_POST['spesifikasi'], ENT_QUOTES);
	$konsumsi_volume = $_POST['konsumsi_volume'];
	$konsumsi_volume = str_replace(',', '', $konsumsi_volume);
	$konsumsi_unit = htmlspecialchars($_POST['konsumsi_unit'], ENT_QUOTES);
	$kompetitor = htmlspecialchars($_POST['kompetitor'], ENT_QUOTES);
	$harga_kompetitor = $_POST['harga_kompetitor'];
	$harga_kompetitor = str_replace(',', '', $harga_kompetitor);
	$top = htmlspecialchars($_POST['top'], ENT_QUOTES);
	$pic = htmlspecialchars($_POST['pic'], ENT_QUOTES);
	$kontak_email = htmlspecialchars($_POST['kontak_email'], ENT_QUOTES);
	$kontak_phone = htmlspecialchars($_POST['kontak_phone'], ENT_QUOTES);
	$keterangan = htmlspecialchars($_POST['keterangan'], ENT_QUOTES);

	if($act == "add"){
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		$sql1 = "
		insert into pro_database_lubricant_oil(
			nama_customer,
			jenis_oil,
			spesifikasi,
			konsumsi_volume,
			konsumsi_unit,
			kompetitor,
			harga_kompetitor,
			top,
			pic,
			kontak_email,
			kontak_phone,
			keterangan,
			created_time,
			created_by,
			deleted_time
		) values (
			'".$nama_customer."',
			'".$jenis_oil."',
			'".$spesifikasi."',
			'".$konsumsi_volume."',
			'".$konsumsi_unit."',
			'".$kompetitor."',
			'".$harga_kompetitor."',
			'".$top."',
			'".$pic."',
			'".$kontak_email."',
			'".$kontak_phone."',
			'".$keterangan."',
			NOW(), 
			'".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."',
			NULL
		)";
		$res1 = $con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();
		$url = BASE_URL_CLIENT."/database-lubricant-oil.php";
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
	
	else if($act == "update"){
		$oke = true;
		$con->beginTransaction();
		$con->clearError();
		
		$sql1 = "
		update pro_database_lubricant_oil set 
			nama_customer = '".$nama_customer."',
			jenis_oil = '".$jenis_oil."',
			spesifikasi = '".$spesifikasi."',
			konsumsi_volume = '".$konsumsi_volume."',
			konsumsi_unit = '".$konsumsi_unit."',
			kompetitor = '".$kompetitor."',
			harga_kompetitor = '".$harga_kompetitor."',
			top = '".$top."',
			pic = '".$pic."',
			kontak_email = '".$kontak_email."',
			kontak_phone = '".$kontak_phone."',
			keterangan = '".$keterangan."',
			created_time = NOW()
		where 
			id_database_lubricant_oil = '".$id_database_lubricant_oil."'";
		$res1 = $con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();
		$url = BASE_URL_CLIENT."/database-lubricant-oil.php";
		$msg = "Data behasil diupdate";

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
?>

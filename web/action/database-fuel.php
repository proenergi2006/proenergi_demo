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

	$id_database_fuel = $idr;
	$nama_customer = htmlspecialchars($_POST['nama_customer'], ENT_QUOTES);
	$potensi_volume = $_POST['potensi_volume'];
	$potensi_volume = str_replace(',', '', $potensi_volume);
	$potensi_waktu = htmlspecialchars($_POST['potensi_waktu'], ENT_QUOTES);
	$tersuplai_jumlah_pengiriman = htmlspecialchars($_POST['tersuplai_jumlah_pengiriman'], ENT_QUOTES);
	$tersuplai_waktu = htmlspecialchars($_POST['tersuplai_waktu'], ENT_QUOTES);
	$tersuplai_volume = $_POST['tersuplai_volume'];
	$tersuplai_volume = str_replace(',', '', $tersuplai_volume);
	$sisa_potensi = htmlspecialchars($_POST['sisa_potensi'], ENT_QUOTES);
	$kompetitor = htmlspecialchars($_POST['kompetitor'], ENT_QUOTES);
	$harga_kompetitor = $_POST['harga_kompetitor'];
	$harga_kompetitor = str_replace(',', '', $harga_kompetitor);
	$top = htmlspecialchars($_POST['top'], ENT_QUOTES);
	$pic = htmlspecialchars($_POST['pic'], ENT_QUOTES);
	$kontak_email = htmlspecialchars($_POST['kontak_email'], ENT_QUOTES);
	$kontak_phone = htmlspecialchars($_POST['kontak_phone'], ENT_QUOTES);
	$catatan = htmlspecialchars($_POST['catatan'], ENT_QUOTES);

	if($act == "add"){
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		$sql1 = "
		insert into pro_database_fuel(
			nama_customer,
			potensi_volume,
			potensi_waktu,
			tersuplai_jumlah_pengiriman,
			tersuplai_waktu,
			tersuplai_volume,
			sisa_potensi,
			kompetitor,
			harga_kompetitor,
			top,
			pic,
			kontak_email,
			kontak_phone,
			catatan,
			created_time,
			created_by,
			deleted_time
		) values (
			'".$nama_customer."',
			'".$potensi_volume."',
			'".$potensi_waktu."',
			'".$tersuplai_jumlah_pengiriman."',
			'".$tersuplai_waktu."',
			'".$tersuplai_volume."',
			'".$sisa_potensi."',
			'".$kompetitor."',
			'".$harga_kompetitor."',
			'".$top."',
			'".$pic."',
			'".$kontak_email."',
			'".$kontak_phone."',
			'".$catatan."',
			NOW(), 
			'".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."',
			NULL
		)";
		$res1 = $con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();
		$url = BASE_URL_CLIENT."/database-fuel.php";
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
		update pro_database_fuel set 
			nama_customer = '".$nama_customer."',
			potensi_volume = '".$potensi_volume."',
			potensi_waktu = '".$potensi_waktu."',
			tersuplai_jumlah_pengiriman = '".$tersuplai_jumlah_pengiriman."',
			tersuplai_waktu = '".$tersuplai_waktu."',
			tersuplai_volume = '".$tersuplai_volume."',
			sisa_potensi = '".$sisa_potensi."',
			kompetitor = '".$kompetitor."',
			harga_kompetitor = '".$harga_kompetitor."',
			top = '".$top."',
			pic = '".$pic."',
			kontak_email = '".$kontak_email."',
			kontak_phone = '".$kontak_phone."',
			catatan = '".$catatan."',
			created_time = NOW()
		where 
			id_database_fuel = '".$id_database_fuel."'";
		$res1 = $con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();
		$url = BASE_URL_CLIENT."/database-fuel.php";
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

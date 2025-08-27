<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload", "htmlawed");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$flash	= new FlashAlerts;

	$id_user = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);

	if (!$_POST['id_insentif'])
		$flash->add("warning", "Silakan checklist data yang akan dikirim.", BASE_REFERER);

	$submit = $_POST['submit'];
	$id_insentif = $_POST['id_insentif'];
	$id_insentif = implode(',', $id_insentif);
	$id_insentif = explode(',', $id_insentif);

	if($submit == "hrd"){
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		$harga_jual = $_POST['harga_jual'];
		$jumlah_hari_dispensasi = $_POST['jumlah_hari_dispensasi'];
		$jumlah_hari_netto = $_POST['jumlah_hari_netto'];
		$jumlah_hari_gol_inc = $_POST['jumlah_hari_gol_inc'];
		$incentive = $_POST['incentive'];
		for ($i=0; $i < count($id_insentif); $i++) {
			$sql1 = "
			update pro_insentif set
				harga_jual = ". str_replace(',', '', $harga_jual[$i]) .",
	            jumlah_hari_dispensasi = ". str_replace(',', '', $jumlah_hari_dispensasi[$i]) .",
	            jumlah_hari_netto = ". str_replace(',', '', $jumlah_hari_netto[$i]) .",
	            jumlah_hari_gol_inc = ". str_replace(',', '', $jumlah_hari_gol_inc[$i]) .",
	            incentive = ". str_replace(',', '', $incentive[$i]) .",
				approve_hrd = 1
			where 
				id = ".$id_insentif[$i]."
			";
			$con->setQuery($sql1);
			if (!$oke || $con->hasError()) {
				$oke = false;
				continue;
			}
		}

		$url = BASE_URL_CLIENT."/perhitungan-insentif-".$submit.".php";
		$msg = "Data behasil diproses";

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

	if($submit == "ceo"){
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		$harga_jual = $_POST['harga_jual'];
		$jumlah_hari_dispensasi = $_POST['jumlah_hari_dispensasi'];
		$jumlah_hari_netto = $_POST['jumlah_hari_netto'];
		$jumlah_hari_gol_inc = $_POST['jumlah_hari_gol_inc'];
		$incentive = $_POST['incentive'];
		for ($i=0; $i < count($id_insentif); $i++) {
			$sql1 = "
			update pro_insentif set
				harga_jual = ". str_replace(',', '', $harga_jual[$i]) .",
	            jumlah_hari_dispensasi = ". str_replace(',', '', $jumlah_hari_dispensasi[$i]) .",
	            jumlah_hari_netto = ". str_replace(',', '', $jumlah_hari_netto[$i]) .",
	            jumlah_hari_gol_inc = ". str_replace(',', '', $jumlah_hari_gol_inc[$i]) .",
	            incentive = ". str_replace(',', '', $incentive[$i]) .",
				approve_ceo = 1
			where 
				id = ".$id_insentif[$i]."
			";
			$con->setQuery($sql1);
			if (!$oke || $con->hasError()) {
				$oke = false;
				continue;
			}
		}

		$url = BASE_URL_CLIENT."/perhitungan-insentif-".$submit.".php";
		$msg = "Data behasil diproses";

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

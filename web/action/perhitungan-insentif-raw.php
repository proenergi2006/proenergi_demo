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

	$enk_act = isset($enk['act'])?$enk['act']:null;
	$post_act = isset($_POST['act'])?$_POST['act']:null;
	$enk_id	= isset($enk['id'])?$enk['id']:null;
	$post_id	= isset($_POST['id'])?$_POST['id']:null;
	$id_user = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);
	$id_insentif_raw = $enk_id?$enk_id:$post_id;
	$act = $enk_act?$enk_act:$post_act;

	if($act == "send"){
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		$harga_jual = $_POST['harga_jual'];
		$jumlah_hari_dispensasi = $_POST['jumlah_hari_dispensasi'];
		$jumlah_hari_netto = $_POST['jumlah_hari_netto'];
		$jumlah_hari_gol_inc = $_POST['jumlah_hari_gol_inc'];
		$incentive = $_POST['incentive'];
		for ($i=0; $i < count($id_insentif_raw); $i++) { 
			$sql1 = "
			INSERT INTO pro_insentif ( 
			    form_no,
	            recv_date,
	            customer_name,
	            inv_no,
	            inv_date,
	            quantity,
	            harga_jual,
	            jumlah_hari_lunas,
	            jumlah_hari_dispensasi,
	            jumlah_hari_netto,
	            jumlah_hari_gol_inc,
	            incentive,
	            created_time,
	            id_cabang,
	            id_marketing,
	            periode,
	            approve_hrd,
	            approve_ceo,
	            created_by,
	            deleted_time
			) 
			SELECT 
				form_no,
	            recv_date,
	            customer_name,
	            inv_no,
	            inv_date,
	            quantity,
	            ". str_replace(',', '', $harga_jual[$i]) ." AS harga_jual,
	            jumlah_hari_lunas,
	            ". str_replace(',', '', $jumlah_hari_dispensasi[$i]) ." AS jumlah_hari_dispensasi,
	            ". str_replace(',', '', $jumlah_hari_netto[$i]) ." AS jumlah_hari_netto,
	            ". str_replace(',', '', $jumlah_hari_gol_inc[$i]) ." AS jumlah_hari_gol_inc,
	            ". str_replace(',', '', $incentive[$i]) ." AS incentive,
	            NOW() AS created_time,
	            id_cabang,
	            id_marketing,
	            periode,
	            0 AS approve_hrd,
	            0 AS approve_ceo,
	            ".$id_user." AS created_by,
	            NULL AS deleted_time
			FROM pro_insentif_raw
			WHERE id=".$id_insentif_raw[$i]."
			";
			$con->setQuery($sql1);
			if (!$oke || $con->hasError()) {
				$oke = false;
				continue;
			}

			$sql1 = "
			update pro_insentif_raw set
				has_send_hrd = 1
			where 
				id in (".$id_insentif_raw[$i].")
			";
			$con->setQuery($sql1);
			if (!$oke || $con->hasError()) {
				$oke = false;
				continue;
			}
		}

		$url = BASE_URL_CLIENT."/perhitungan-insentif.php";
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

	else if($act == 'delete') {
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		$sql1 = "
		update pro_insentif_raw set
			deleted_time = NOW()
		where 
			id in (".$id_insentif_raw.")";
		$con->setQuery($sql1);

		$oke  = $oke && !$con->hasError();
		$url = BASE_URL_CLIENT."/perhitungan-insentif.php";
		$msg = "Data behasil dihapus";

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

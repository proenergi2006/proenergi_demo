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
	// echo json_encode($_POST); die();
	$id_marketing_reimbursement = $idr;
	$marketing_reimbursement_date = $_POST['marketing_reimbursement_date'];
	$marketing_reimbursement_date_format = explode('/', $marketing_reimbursement_date);
	$marketing_reimbursement_date_format = $marketing_reimbursement_date_format[2].'-'.$marketing_reimbursement_date_format[1].'-'.$marketing_reimbursement_date_format[0];
	$no_polisi = $_POST['no_polisi'];
	$user = $_POST['user'];
	$km_awal = $_POST['km_awal'];
	$km_akhir = $_POST['km_akhir'];
	$total = $_POST['total'];

	$reimbursement_item = $_POST['reimbursement_item'];
	$id_reimbursement_item = $_POST['id_reimbursement_item'];
	$item = $_POST['item'];
	$jumlah = $_POST['jumlah'];

	$marketing_reimbursement_item_delete = $_POST['marketing_reimbursement_item_delete'];
	$marketing_reimbursement_keterangan_delete = $_POST['marketing_reimbursement_keterangan_delete'];

	if($act == "add"){
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		$sql1 = "
		insert into pro_marketing_reimbursement(
			marketing_reimbursement_date,
			no_polisi,
			user,
			km_awal,
			km_akhir,
			total,
			created_time,
			created_by,
			deleted_time
		) values (
			'".$marketing_reimbursement_date_format."',
			'".$no_polisi."',
			'".$user."',
			'".$km_awal."',
			'".$km_akhir."',
			'".str_replace(',', '', $total)."',
			NOW(), 
			'".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."',
			NULL
		)";
		$res1 = $con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();

		$sqlget = "select id_marketing_reimbursement from pro_marketing_reimbursement where deleted_time is null order by id_marketing_reimbursement desc limit 1";
        $marketing_reimbursement = $con->getRecord($sqlget);

        for ($i=0; $i < count($reimbursement_item); $i++) {
			$sql1 = "
			insert into pro_marketing_reimbursement_item(
				id_marketing_reimbursement,
				item,
				jumlah,
				created_time,
				deleted_time
			) values (
				'".$marketing_reimbursement['id_marketing_reimbursement']."',
				'".$item[$i]."',
				'".str_replace(',', '', $jumlah[$i])."',
				NOW(),
				NULL
			)";
			$res1 = $con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();

			if (isset($_POST['keterangan_'.$i])) {
				$sqlget = "select id_marketing_reimbursement_item from pro_marketing_reimbursement_item where deleted_time is null order by id_marketing_reimbursement_item desc limit 1";
        		$marketing_reimbursement_item = $con->getRecord($sqlget);
				for ($j=0; $j < count($_POST['keterangan_'.$i]); $j++) { 
					$sql1 = "
					insert into pro_marketing_reimbursement_keterangan(
						id_marketing_reimbursement_item,
						keterangan,
						nilai,
						created_time,
						deleted_time
					) values (
						'".$marketing_reimbursement_item['id_marketing_reimbursement_item']."',
						'".$_POST['keterangan_'.$i][$j]."',
						'".str_replace(',', '', $_POST['nilai_'.$i][$j])."',
						NOW(),
						NULL
					)";
					$res1 = $con->setQuery($sql1);
					$oke  = $oke && !$con->hasError();
				}
			}
		}

		$url = BASE_URL_CLIENT."/marketing-reimbursement.php";
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
		update pro_marketing_reimbursement set 
			marketing_reimbursement_date = '".$marketing_reimbursement_date_format."',
			no_polisi = '".$no_polisi."',
			user = '".$user."',
			km_awal = '".$km_awal."',
			km_akhir = '".$km_akhir."',
			total = '".str_replace(',', '', $total)."',
			created_time = NOW()
		where 
			id_marketing_reimbursement = '".$id_marketing_reimbursement."'";
		$res1 = $con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();

		if (isset($marketing_reimbursement_item_delete)) {
			foreach ($marketing_reimbursement_item_delete as $row) {
				$sql1 = "
				update pro_marketing_reimbursement_item set 
					deleted_time = NOW()
				where 
					id_marketing_reimbursement_item = '".$row."'";
				$res1 = $con->setQuery($sql1);
				$oke  = $oke && !$con->hasError();
			}
		}
		if (isset($marketing_reimbursement_keterangan_delete)) {
			foreach ($marketing_reimbursement_keterangan_delete as $row) {
				$sql1 = "
				update pro_marketing_reimbursement_keterangan set 
					deleted_time = NOW()
				where 
					id_marketing_reimbursement_keterangan = '".$row."'";
				$res1 = $con->setQuery($sql1);
				$oke  = $oke && !$con->hasError();
			}
		}

		for ($i=0; $i < count($reimbursement_item); $i++) {
			if ($id_reimbursement_item[$i]) {
				$sql1 = "
				update pro_marketing_reimbursement_item set
					item = '".$item[$i]."',
					jumlah = '".str_replace(',', '', $jumlah[$i])."',
					created_time = NOW()
				where 
					id_marketing_reimbursement_item = '".$id_reimbursement_item[$i]."'";
				$res1 = $con->setQuery($sql1);
				$oke  = $oke && !$con->hasError();
			} else {
				$sql1 = "
				insert into pro_marketing_reimbursement_item(
					id_marketing_reimbursement,
					item,
					jumlah,
					created_time,
					deleted_time
				) values (
					'".$idr."',
					'".$item[$i]."',
					'".str_replace(',', '', $jumlah[$i])."',
					NOW(),
					NULL
				)";
				$res1 = $con->setQuery($sql1);
				$oke  = $oke && !$con->hasError();

				$sqlget = "select id_marketing_reimbursement_item from pro_marketing_reimbursement_item where deleted_time is null order by id_marketing_reimbursement_item desc limit 1";
        		$marketing_reimbursement_item = $con->getRecord($sqlget);
        		$id_reimbursement_item[$i] = $marketing_reimbursement_item['id_marketing_reimbursement_item'];
			}

			if (isset($_POST['reimbursement_keterangan_'.$i])) {
				for ($j=0; $j < count($_POST['reimbursement_keterangan_'.$i]); $j++) { 
					if ($_POST['id_reimbursement_keterangan_'.$i][$j]) {
						$sql1 = "
						update pro_marketing_reimbursement_keterangan set
							keterangan = '".$_POST['keterangan_'.$i][$j]."',
							nilai = '".str_replace(',', '', $_POST['nilai_'.$i][$j])."',
							created_time = NOW()
						where 
							id_marketing_reimbursement_keterangan = '".$_POST['id_reimbursement_keterangan_'.$i][$j]."'";
						$res1 = $con->setQuery($sql1);
						$oke  = $oke && !$con->hasError();
					} else {
						$sql1 = "
						insert into pro_marketing_reimbursement_keterangan(
							id_marketing_reimbursement_item,
							keterangan,
							nilai,
							created_time,
							deleted_time
						) values (
							'".$id_reimbursement_item[$i]."',
							'".$_POST['keterangan_'.$i][$j]."',
							'".str_replace(',', '', $_POST['nilai_'.$i][$j])."',
							NOW(),
							NULL
						)";
						$res1 = $con->setQuery($sql1);
						$oke  = $oke && !$con->hasError();
					}
				}
			}
		}

		$url = BASE_URL_CLIENT."/marketing-reimbursement.php";
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

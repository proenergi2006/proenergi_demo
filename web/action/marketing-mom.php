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

	// Marketing MoM
	$id_marketing_mom = $idr;
	$date = htmlspecialchars($_POST['date'], ENT_QUOTES);
	$date_format = explode('/', $date);
	$date_format = $date_format[2].'-'.$date_format[1].'-'.$date_format[0];
	$id_customer = htmlspecialchars($_POST['id_customer'], ENT_QUOTES);
	$customer = htmlspecialchars($_POST['customer'], ENT_QUOTES);
	$place = htmlspecialchars($_POST['place'], ENT_QUOTES);
	$title = htmlspecialchars($_POST['title'], ENT_QUOTES);
	$hasil_rapat = htmlspecialchars($_POST['hasil_rapat'], ENT_QUOTES);

	// Marketing MoM Participant
	$marketing_mom_participant = $_POST['marketing_mom_participant'];
	$id_marketing_mom_participant = $_POST['id_marketing_mom_participant'];
	$name = $_POST['name'];
	$position = $_POST['position'];
	$marketing_mom_participant_delete = $_POST['marketing_mom_participant_delete'] ?? null;

	// Database Fuel
	$id_database_fuel = $_POST['id_database_fuel'];
	$database_fuel_nama_customer = $_POST['database_fuel_nama_customer'];
	$database_fuel_potensi_volume = $_POST['database_fuel_potensi_volume'];
	$database_fuel_potensi_volume = str_replace(',', '', $database_fuel_potensi_volume);
	$database_fuel_potensi_waktu = $_POST['database_fuel_potensi_waktu'];
	$database_fuel_tersuplai_jumlah_pengiriman = $_POST['database_fuel_tersuplai_jumlah_pengiriman'];
	$database_fuel_tersuplai_waktu = $_POST['database_fuel_tersuplai_waktu'];
	$database_fuel_tersuplai_volume = $_POST['database_fuel_tersuplai_volume'];
	$database_fuel_tersuplai_volume = str_replace(',', '', $database_fuel_tersuplai_volume);
	$database_fuel_sisa_potensi = $_POST['database_fuel_sisa_potensi'];
	$database_fuel_kompetitor = $_POST['database_fuel_kompetitor'];
	$database_fuel_harga_kompetitor = $_POST['database_fuel_harga_kompetitor'];
	$database_fuel_harga_kompetitor = str_replace(',', '', $database_fuel_harga_kompetitor);
	$database_fuel_top = $_POST['database_fuel_top'];
	$database_fuel_pic = $_POST['database_fuel_pic'];
	$database_fuel_kontak_email = $_POST['database_fuel_kontak_email'];
	$database_fuel_kontak_phone = $_POST['database_fuel_kontak_phone'];
	$database_fuel_catatan = $_POST['database_fuel_catatan'];

	// Database Lubricant Oil
	$id_database_lubricant_oil = $_POST['id_database_lubricant_oil'];
	$database_lubricant_oil_nama_customer = $_POST['database_lubricant_oil_nama_customer'];
	$database_lubricant_oil_jenis_oil = $_POST['database_lubricant_oil_jenis_oil'];
	$database_lubricant_oil_spesifikasi = $_POST['database_lubricant_oil_spesifikasi'];
	$database_lubricant_oil_konsumsi_volume = $_POST['database_lubricant_oil_konsumsi_volume'];
	$database_lubricant_oil_konsumsi_volume = str_replace(',', '', $database_lubricant_oil_konsumsi_volume);
	$database_lubricant_oil_konsumsi_unit = $_POST['database_lubricant_oil_konsumsi_unit'];
	$database_lubricant_oil_kompetitor = $_POST['database_lubricant_oil_kompetitor'];
	$database_lubricant_oil_harga_kompetitor = $_POST['database_lubricant_oil_harga_kompetitor'];
	$database_lubricant_oil_harga_kompetitor = str_replace(',', '', $database_lubricant_oil_harga_kompetitor);
	$database_lubricant_oil_top = $_POST['database_lubricant_oil_top'];
	$database_lubricant_oil_pic = $_POST['database_lubricant_oil_pic'];
	$database_lubricant_oil_kontak_email = $_POST['database_lubricant_oil_kontak_email'];
	$database_lubricant_oil_kontak_phone = $_POST['database_lubricant_oil_kontak_phone'];
	$database_lubricant_oil_keterangan = $_POST['database_lubricant_oil_keterangan'];

	// Dokumentasi
	$max_size	= 2 * 1024 * 1024;
	$allow_type	= array(".jpg", ".jpeg", ".JPG", ".png", ".pdf", ".rar", ".zip");
	$pathfile	= $public_base_directory.'/files/uploaded_user/lampiran';
	$file_odometer_pergi 	= htmlspecialchars($_FILES['odometer_pergi']['name'],ENT_QUOTES);
	$size_odometer_pergi 	= htmlspecialchars($_FILES['odometer_pergi']['size'],ENT_QUOTES);
	$temp_odometer_pergi 	= htmlspecialchars($_FILES['odometer_pergi']['tmp_name'],ENT_QUOTES);
	$ext_odometer_pergi 	= substr($file_odometer_pergi,strrpos($file_odometer_pergi,'.'));
	if ($file_odometer_pergi) {
		if($size_odometer_pergi > $max_size) {
			$con->close();
			$flash->add("error", "Ukuran file terlalu besar, melebihi 2MB...", BASE_REFERER);
		}
		if(!in_array($ext_odometer_pergi, $allow_type)) {
			$con->close();
			$flash->add("error", "Tipe file tidak diperbolehkan...", BASE_REFERER);
		}
	}
	$file_odometer_pulang 	= htmlspecialchars($_FILES['odometer_pulang']['name'],ENT_QUOTES);
	$size_odometer_pulang 	= htmlspecialchars($_FILES['odometer_pulang']['size'],ENT_QUOTES);
	$temp_odometer_pulang 	= htmlspecialchars($_FILES['odometer_pulang']['tmp_name'],ENT_QUOTES);
	$ext_odometer_pulang 	= substr($file_odometer_pulang,strrpos($file_odometer_pulang,'.'));
	if ($file_odometer_pulang) {
		if($size_odometer_pulang > $max_size) {
			$con->close();
			$flash->add("error", "Ukuran file terlalu besar, melebihi 2MB...", BASE_REFERER);
		}
		if(!in_array($ext_odometer_pulang, $allow_type)) {
			$con->close();
			$flash->add("error", "Tipe file tidak diperbolehkan...", BASE_REFERER);
		}
	}
	$file_meeting_customer 	= htmlspecialchars($_FILES['meeting_customer']['name'],ENT_QUOTES);
	$size_meeting_customer 	= htmlspecialchars($_FILES['meeting_customer']['size'],ENT_QUOTES);
	$temp_meeting_customer 	= htmlspecialchars($_FILES['meeting_customer']['tmp_name'],ENT_QUOTES);
	$ext_meeting_customer 	= substr($file_meeting_customer,strrpos($file_meeting_customer,'.'));
	if ($file_meeting_customer) {
		if($size_meeting_customer > $max_size) {
			$con->close();
			$flash->add("error", "Ukuran file terlalu besar, melebihi 2MB...", BASE_REFERER);
		}
		if(!in_array($ext_meeting_customer, $allow_type)) {
			$con->close();
			$flash->add("error", "Tipe file tidak diperbolehkan...", BASE_REFERER);
		}
	}
	$file_tambahan 	= htmlspecialchars($_FILES['tambahan']['name'],ENT_QUOTES);
	$size_tambahan 	= htmlspecialchars($_FILES['tambahan']['size'],ENT_QUOTES);
	$temp_tambahan 	= htmlspecialchars($_FILES['tambahan']['tmp_name'],ENT_QUOTES);
	$ext_tambahan 	= substr($file_tambahan,strrpos($file_tambahan,'.'));
	if ($file_tambahan) {
		if($size_tambahan > $max_size) {
			$con->close();
			$flash->add("error", "Ukuran file terlalu besar, melebihi 2MB...", BASE_REFERER);
		}
		if(!in_array($ext_tambahan, $allow_type)) {
			$con->close();
			$flash->add("error", "Tipe file tidak diperbolehkan...", BASE_REFERER);
		}
	}

	if($act == "add"){
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		// Marketing MoM
		$sql1 = "
		insert into pro_marketing_mom(
			id_customer,
			customer,
			date,
			place,
			title,
			hasil_rapat,
			created_time,
			created_by,
			deleted_time
		) values (
			'".$id_customer."',
			'".$customer."',
			'".$date_format."',
			'".$place."',
			'".$title."',
			'".$hasil_rapat."',
			NOW(),
			'".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."',
			NULL
		)";
		$res1 = $con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();

		// Marketing MoM Participant
		$sqlget = "select id_marketing_mom from pro_marketing_mom where deleted_time is null order by id_marketing_mom desc limit 1";
        $marketing_mom = $con->getRecord($sqlget);
        $id_marketing_mom = $marketing_mom['id_marketing_mom'];

        for ($i=0; $i < count($marketing_mom_participant); $i++) {
			$sql1 = "
			insert into pro_marketing_mom_participant(
				id_marketing_mom,
				name,
				position,
				created_time,
				created_by,
				deleted_time
			) values (
				'".$marketing_mom['id_marketing_mom']."',
				'".$name[$i]."',
				'".$position[$i]."',
				NOW(),
				'".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."',
				NULL
			)";
			$res1 = $con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();
		}

		if(!is_array_empty($_POST["newdok1"])){
			foreach($_POST["newdok1"] as $idx1=>$val1){
				$newdok1 = htmlspecialchars($_POST["newdok1"][$idx1], ENT_QUOTES);
				$newdok2 = htmlspecialchars($_FILES['newdok2']['name'][$idx1],ENT_QUOTES);     	
				if($newdok1){
					$sql2 = "insert into pro_marketing_mom_file(id_marketing_mom,keterangan,file_ori) values ('".$marketing_mom['id_marketing_mom']."', '".$newdok1."', '".sanitize_filename($newdok2)."')";
					$idk = $con->setQuery($sql2);
					$oke = $oke && !$con->hasError();
					
					if($newdok2){
						$lampiran = 'mkt_mom_'.$marketing_mom['id_marketing_mom'].'_'.$idk.'_'.sanitize_filename($newdok2);
						$upload[$idx1] = $lampiran;
						
						$sql3 = "update pro_marketing_mom_file set file_upload = '".$lampiran."' where id_marketing_mom_file = '".$idk."'";
						$con->setQuery($sql3);
						$oke = $oke && !$con->hasError();
					}
				}
			}
		}

		// // Database Fuel
		// $sql1 = "
		// insert into pro_database_fuel(
		// 	id_marketing_mom,
		// 	is_mom,
		// 	nama_customer,
		// 	potensi_volume,
		// 	potensi_waktu,
		// 	tersuplai_jumlah_pengiriman,
		// 	tersuplai_waktu,
		// 	tersuplai_volume,
		// 	sisa_potensi,
		// 	kompetitor,
		// 	harga_kompetitor,
		// 	top,
		// 	pic,
		// 	kontak_email,
		// 	kontak_phone,
		// 	catatan,
		// 	created_time,
		// 	created_by,
		// 	deleted_time
		// ) values (
		// 	'".$marketing_mom['id_marketing_mom']."',
		// 	1,
		// 	'".$database_fuel_nama_customer."',
		// 	'".$database_fuel_potensi_volume."',
		// 	'".$database_fuel_potensi_waktu."',
		// 	'".$database_fuel_tersuplai_jumlah_pengiriman."',
		// 	'".$database_fuel_tersuplai_waktu."',
		// 	'".$database_fuel_tersuplai_volume."',
		// 	'".$database_fuel_sisa_potensi."',
		// 	'".$database_fuel_kompetitor."',
		// 	'".$database_fuel_harga_kompetitor."',
		// 	'".$database_fuel_top."',
		// 	'".$database_fuel_pic."',
		// 	'".$database_fuel_kontak_email."',
		// 	'".$database_fuel_kontak_phone."',
		// 	'".$database_fuel_catatan."',
		// 	NOW(), 
		// 	'".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."',
		// 	NULL
		// )";
		// $res1 = $con->setQuery($sql1);
		// $oke  = $oke && !$con->hasError();

		// // Database Lubricant Oil
		// $sql1 = "
		// insert into pro_database_lubricant_oil(
		// 	id_marketing_mom,
		// 	is_mom,
		// 	nama_customer,
		// 	jenis_oil,
		// 	spesifikasi,
		// 	konsumsi_volume,
		// 	konsumsi_unit,
		// 	kompetitor,
		// 	harga_kompetitor,
		// 	top,
		// 	pic,
		// 	kontak_email,
		// 	kontak_phone,
		// 	keterangan,
		// 	created_time,
		// 	created_by,
		// 	deleted_time
		// ) values (
		// 	'".$marketing_mom['id_marketing_mom']."',
		// 	1,
		// 	'".$database_lubricant_oil_nama_customer."',
		// 	'".$database_lubricant_oil_jenis_oil."',
		// 	'".$database_lubricant_oil_spesifikasi."',
		// 	'".$database_lubricant_oil_konsumsi_volume."',
		// 	'".$database_lubricant_oil_konsumsi_unit."',
		// 	'".$database_lubricant_oil_kompetitor."',
		// 	'".$database_lubricant_oil_harga_kompetitor."',
		// 	'".$database_lubricant_oil_top."',
		// 	'".$database_lubricant_oil_pic."',
		// 	'".$database_lubricant_oil_kontak_email."',
		// 	'".$database_lubricant_oil_kontak_phone."',
		// 	'".$database_lubricant_oil_keterangan."',
		// 	NOW(), 
		// 	'".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."',
		// 	NULL
		// )";
		// $res1 = $con->setQuery($sql1);
		// $oke  = $oke && !$con->hasError();

		$url = BASE_URL_CLIENT."/marketing-mom.php";
		$msg = "Data behasil disimpan";

		if ($oke){
			$mantab  = true;
			if(!is_array_empty($upload)){
				foreach($_FILES['newdok2']['name'] as $idx5=>$val5){
					$filetmp = htmlspecialchars($_FILES['newdok2']["tmp_name"][$idx5],ENT_QUOTES); 
					$tujuan  = $pathfile."/".$upload[$idx5];
					$mantab  = $mantab && move_uploaded_file($filetmp, $tujuan);
					if(file_exists($filetmp)) unlink($filetmp);
				}
			}

			if($mantab){
				$con->commit();
				$con->close();
				$flash->add("success", $msg, $url);
			} else{
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
			}
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

		// Marketing MoM
		$sql1 = "
		update pro_marketing_mom set 
			date = '".$date_format."',
			customer = '".$customer."',
			place = '".$place."',
			created_time = NOW()
		where 
			id_marketing_mom = '".$id_marketing_mom."'";
		$res1 = $con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();

		// Marketing MoM Participant
		if (isset($marketing_mom_participant_delete)) {
			foreach ($marketing_mom_participant_delete as $row) {
				$sql1 = "
				update pro_marketing_mom_participant set 
					deleted_time = NOW()
				where 
					id_marketing_mom_participant = '".$row."'";
				$res1 = $con->setQuery($sql1);
				$oke  = $oke && !$con->hasError();
			}
		}
		for ($i=0; $i < count($marketing_mom_participant); $i++) {
			if ($id_marketing_mom_participant[$i]) {
				$sql1 = "
				update pro_marketing_mom_participant set
					name = '".$name[$i]."',
					position = '".$position[$i]."',
					created_time = NOW()
				where 
					id_marketing_mom_participant = '".$id_marketing_mom_participant[$i]."'";
				$res1 = $con->setQuery($sql1);
				$oke  = $oke && !$con->hasError();
			} else {
				$sql1 = "
				insert into pro_marketing_mom_participant(
					id_marketing_mom,
					name,
					position,
					created_time,
					created_by,
					deleted_time
				) values (
					'".$idr."',
					'".$name[$i]."',
					'".$position[$i]."',
					NOW(),
					'".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."',
					NULL
				)";
				$res1 = $con->setQuery($sql1);
				$oke  = $oke && !$con->hasError();
			}
		}

		if(!is_array_empty($_POST["newdok1"])){
			foreach($_POST["newdok1"] as $idx1=>$val1){
				$newdok1 = htmlspecialchars($_POST["newdok1"][$idx1], ENT_QUOTES);
				// $newdok2 = htmlspecialchars($_POST["newdok2"][$idx1], ENT_QUOTES);
				$newdok2 = htmlspecialchars($_FILES['newdok2']['name'][$idx1],ENT_QUOTES);     	
				if($newdok1){
					$sql2 = "insert into pro_marketing_mom_file(id_marketing_mom,keterangan,file_ori) values ('".$id_marketing_mom."', '".$newdok1."', '".sanitize_filename($newdok2)."')";
					$idk = $con->setQuery($sql2);
					$oke = $oke && !$con->hasError();
					
					if($newdok2){
						$lampiran = 'mkt_report_'.$id_marketing_mom.'_'.$idk.'_'.sanitize_filename($newdok2);
						$upload[$idx1] = $lampiran;
						
						$sql3 = "update pro_marketing_mom_file set file_upload = '".$lampiran."' where id_marketing_mom_file = '".$idk."'";
						$con->setQuery($sql3);
						$oke = $oke && !$con->hasError();
					}
				}
			}
		}
				
		if(!is_array_empty($_POST["doksup"])){
			foreach($_POST["doksup"] as $idx2=>$val2){
				if(!$_POST["doknya"][$idx2]){
					$sql4 = "delete from pro_marketing_mom_file where id_marketing_mom_file = '".$idx2."'";
					$con->setQuery($sql4);
					$oke = $oke && !$con->hasError();

					$tmpPic = glob($pathfile."/mkt_report_".$id_marketing_mom."_".$idx2."_*.{pdf,docx}", GLOB_BRACE);
					if(count($tmpPic) > 0){
						foreach($tmpPic as $datx)
							$delPic[$idx2] = $datx;
					}
				}
			}
		}

		// Database Fuel
		// $sql1 = "
		// update pro_database_fuel set 
		// 	nama_customer = '".$database_fuel_nama_customer."',
		// 	potensi_volume = '".$database_fuel_potensi_volume."',
		// 	potensi_waktu = '".$database_fuel_potensi_waktu."',
		// 	tersuplai_jumlah_pengiriman = '".$database_fuel_tersuplai_jumlah_pengiriman."',
		// 	tersuplai_waktu = '".$database_fuel_tersuplai_waktu."',
		// 	tersuplai_volume = '".$database_fuel_tersuplai_volume."',
		// 	sisa_potensi = '".$database_fuel_sisa_potensi."',
		// 	kompetitor = '".$database_fuel_kompetitor."',
		// 	harga_kompetitor = '".$database_fuel_harga_kompetitor."',
		// 	top = '".$database_fuel_top."',
		// 	pic = '".$database_fuel_pic."',
		// 	kontak_email = '".$database_fuel_kontak_email."',
		// 	kontak_phone = '".$database_fuel_kontak_phone."',
		// 	catatan = '".$database_fuel_catatan."',
		// 	created_time = NOW()
		// where 
		// 	id_database_fuel = '".$id_database_fuel."'";
		// $res1 = $con->setQuery($sql1);
		// $oke  = $oke && !$con->hasError();

		// // Database Lubricant Oil
		// $sql1 = "
		// update pro_database_lubricant_oil set 
		// 	nama_customer = '".$database_lubricant_oil_nama_customer."',
		// 	jenis_oil = '".$database_lubricant_oil_jenis_oil."',
		// 	spesifikasi = '".$database_lubricant_oil_spesifikasi."',
		// 	konsumsi_volume = '".$database_lubricant_oil_konsumsi_volume."',
		// 	konsumsi_unit = '".$database_lubricant_oil_konsumsi_unit."',
		// 	kompetitor = '".$database_lubricant_oil_kompetitor."',
		// 	harga_kompetitor = '".$database_lubricant_oil_harga_kompetitor."',
		// 	top = '".$database_lubricant_oil_top."',
		// 	pic = '".$database_lubricant_oil_pic."',
		// 	kontak_email = '".$database_lubricant_oil_kontak_email."',
		// 	kontak_phone = '".$database_lubricant_oil_kontak_phone."',
		// 	keterangan = '".$database_lubricant_oil_keterangan."',
		// 	created_time = NOW()
		// where 
		// 	id_database_lubricant_oil = '".$id_database_lubricant_oil."'";
		// $res1 = $con->setQuery($sql1);
		// $oke  = $oke && !$con->hasError();

		$url = BASE_URL_CLIENT."/marketing-mom.php";
		$msg = "Data behasil diupdate";

		if ($oke){
			// $isUpload = false;
			// $mantab = true;
			// $sqlUpload = "update pro_marketing_mom set ";
			// if($file_odometer_pergi){
			// 	$tmpPot = glob($pathfile."/mom_odometer_pergi_".$id_marketing_mom."_*.{jpg,jpeg,gif,png,pdf,rar,zip}", GLOB_BRACE);
				
			// 	if(count($tmpPot) > 0){
			// 		foreach($tmpPot as $datj)
			// 			if(file_exists($datj)) unlink($datj);
			// 	}
			// 	$file_odometer_pergi = 'mom_odometer_pergi_'.$id_marketing_mom.'_'.sanitize_filename($file_odometer_pergi);
			// 	$tujuan  = $pathfile."/".$file_odometer_pergi;
			// 	$mantab  = $mantab && move_uploaded_file($temp_odometer_pergi, $tujuan);
			// 	if(file_exists($temp_odometer_pergi)) unlink($temp_odometer_pergi);
			// 	$isUpload = true;
			// 	$sqlUpload .= "odometer_pergi = ".($file_odometer_pergi?"'".$file_odometer_pergi."'":"NULL").",";
			// }
			// if($file_odometer_pulang){
			// 	$tmpPot = glob($pathfile."/mom_odometer_pulang_".$id_marketing_mom."_*.{jpg,jpeg,gif,png,pdf,rar,zip}", GLOB_BRACE);
				
			// 	if(count($tmpPot) > 0){
			// 		foreach($tmpPot as $datj)
			// 			if(file_exists($datj)) unlink($datj);
			// 	}
			// 	$file_odometer_pulang = 'mom_odometer_pulang_'.$id_marketing_mom.'_'.sanitize_filename($file_odometer_pulang);
			// 	$tujuan  = $pathfile."/".$file_odometer_pulang;
			// 	$mantab  = $mantab && move_uploaded_file($temp_odometer_pulang, $tujuan);
			// 	if(file_exists($temp_odometer_pulang)) unlink($temp_odometer_pulang);
			// 	$isUpload = true;
			// 	$sqlUpload .= "odometer_pulang = ".($file_odometer_pulang?"'".$file_odometer_pulang."'":"NULL").",";
			// }
			// if($file_meeting_customer){
			// 	$tmpPot = glob($pathfile."/mom_meeting_customer_".$id_marketing_mom."_*.{jpg,jpeg,gif,png,pdf,rar,zip}", GLOB_BRACE);
				
			// 	if(count($tmpPot) > 0){
			// 		foreach($tmpPot as $datj)
			// 			if(file_exists($datj)) unlink($datj);
			// 	}
			// 	$file_meeting_customer = 'mom_meeting_customer_'.$id_marketing_mom.'_'.sanitize_filename($file_meeting_customer);
			// 	$tujuan  = $pathfile."/".$file_meeting_customer;
			// 	$mantab  = $mantab && move_uploaded_file($temp_meeting_customer, $tujuan);
			// 	if(file_exists($temp_meeting_customer)) unlink($temp_meeting_customer);
			// 	$isUpload = true;
			// 	$sqlUpload .= "meeting_customer = ".($file_meeting_customer?"'".$file_meeting_customer."'":"NULL").",";
			// }
			// if($file_tambahan){
			// 	$tmpPot = glob($pathfile."/mom_tambahan_".$id_marketing_mom."_*.{jpg,jpeg,gif,png,pdf,rar,zip}", GLOB_BRACE);
				
			// 	if(count($tmpPot) > 0){
			// 		foreach($tmpPot as $datj)
			// 			if(file_exists($datj)) unlink($datj);
			// 	}
			// 	$file_tambahan = 'mom_tambahan_'.$id_marketing_mom.'_'.sanitize_filename($file_tambahan);
			// 	$tujuan  = $pathfile."/".$file_tambahan;
			// 	$mantab  = $mantab && move_uploaded_file($temp_tambahan, $tujuan);
			// 	if(file_exists($temp_tambahan)) unlink($temp_tambahan);
			// 	$isUpload = true;
			// 	$sqlUpload .= "tambahan = ".($file_tambahan?"'".$file_tambahan."'":"NULL").",";
			// }
			// if ($isUpload) {
			// 	$sqlUpload .= "created_by = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."' ";
			// 	$sqlUpload .= "where id_marketing_mom = '".$id_marketing_mom."'";
			// 	$res1 = $con->setQuery($sqlUpload);
			// 	$mantab  = $mantab && !$con->hasError();
			// }
			$mantab  = true;
			if(!is_array_empty($upload)){
				foreach($_FILES['newdok2']['name'] as $idx5=>$val5){
					$filetmp = htmlspecialchars($_FILES['newdok2']["tmp_name"][$idx5],ENT_QUOTES); 
					$tujuan  = $pathfile."/".$upload[$idx5];
					$mantab  = $mantab && move_uploaded_file($filetmp, $tujuan);
					if(file_exists($filetmp)) unlink($filetmp);
				}
			}
			if($mantab){
				$con->commit();
				$con->close();
				$flash->add("success", $msg, $url);
			} else{
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
			}
		} else{
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
	}
?>

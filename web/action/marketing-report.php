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

	$id_marketing_report = $idr;
	$marketing_report_date = htmlspecialchars($_POST['marketing_report_date'], ENT_QUOTES);
	$marketing_report_date_format = explode('/', $marketing_report_date);
	$marketing_report_date_format = $marketing_report_date_format[2].'-'.$marketing_report_date_format[1].'-'.$marketing_report_date_format[0];
	$profile_customer_nama_customer = htmlspecialchars($_POST['profile_customer_nama_customer'], ENT_QUOTES);
	$profile_customer_alamat = htmlspecialchars($_POST['profile_customer_alamat'], ENT_QUOTES);
	$profile_customer_status = htmlspecialchars($_POST['profile_customer_status'], ENT_QUOTES);
	$marketing_activity_activity = htmlspecialchars($_POST['marketing_activity_activity'], ENT_QUOTES);
	$marketing_activity_purpose = htmlspecialchars($_POST['marketing_activity_purpose'], ENT_QUOTES);
	$pic = htmlspecialchars($_POST['pic'], ENT_QUOTES);
	$kontak_email = htmlspecialchars($_POST['kontak_email'], ENT_QUOTES);
	$kontak_phone = htmlspecialchars($_POST['kontak_phone'], ENT_QUOTES);
	$file_upload = htmlspecialchars($_POST['file_upload'], ENT_QUOTES);

	$fileUpload 		= htmlspecialchars($_FILES['file_upload']['name'],ENT_QUOTES);
	$sizeUpload 		= htmlspecialchars($_FILES['file_upload']['size'],ENT_QUOTES);
	$tempUpload 		= htmlspecialchars($_FILES['file_upload']['tmp_name'],ENT_QUOTES);
	$extUpload 			= substr($fileUpload,strrpos($fileUpload,'.'));
		

	$upload 	= array();
	$delPic 	= array();
	$max_size	= 2 * 1024 * 1024;
	$allow_type	= array(".pdf", ".docx");
	$pathfile	= $public_base_directory.'/files/uploaded_user/lampiran';
	$arNamaFile = array();

	$sesrol 	= paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]);
	$sesuser 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);
	$seswil 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);

	$technical_support_status = 0;


	if($fileUpload != "" && $sizeUpload > $max_size){
		$con->close();
		$flash->add("error", "Ukuran file upload terlalu besar, melebihi 2MB...", BASE_REFERER);
	} else if($fileUpload != "" && !in_array($extUpload, $allow_type)){
		$con->close();
		$flash->add("error", "Tipe file upload yang diperbolehkan hanya .pdf dan .docx", BASE_REFERER);
	} else{

		if($act == "add"){
			$oke = true;
			$con->beginTransaction();
			$con->clearError();

			$sql1 = "
			insert into pro_marketing_report_master(
				id_customer,
				pic_customer,
				tanggal,
				kegiatan,
				hasil_kegiatan,
				create_date,
				create_by,
				create_ip
			) values (
				'".$profile_customer_nama_customer."',
				'".$pic."',
				'".$marketing_report_date_format."',
				'".$marketing_activity_activity."',
				'".$marketing_activity_purpose."',
				NOW(), 
				'".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."',
				NULL
			)";

			
			$res1 = $con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();


			if(!is_array_empty($_POST["newdok1"])){
				foreach($_POST["newdok1"] as $idx1=>$val1){
					$newdok1 = htmlspecialchars($_POST["newdok1"][$idx1], ENT_QUOTES);
					// $newdok2 = htmlspecialchars($_POST["newdok2"][$idx1], ENT_QUOTES);
					$newdok2 = htmlspecialchars($_FILES['newdok2']['name'][$idx1],ENT_QUOTES);     	
					if($newdok1){
						$sql2 = "insert into pro_marketing_report_master_file(id_mkt_report,keterangan,file_ori) values ('".$res1."', '".$newdok1."', '".sanitize_filename($newdok2)."')";
						$idk = $con->setQuery($sql2);
						$oke = $oke && !$con->hasError();
						
						if($newdok2){
							$lampiran = 'mkt_report_'.$res1.'_'.$idk.'_'.sanitize_filename($newdok2);
							$upload[$idx1] = $lampiran;
							
							$sql3 = "update pro_marketing_report_master_file set file_upload = '".$lampiran."' where id_file_upload = '".$idk."'";
							$con->setQuery($sql3);
							$oke = $oke && !$con->hasError();
						}
					}
				}
			}

			$url = BASE_URL_CLIENT."/marketing-report.php";
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
		}else if($act == "update"){
			$oke = true;
			$con->beginTransaction();
			$con->clearError();
			$sql1 = "
			update pro_marketing_report_master set 
				id_customer= '".$profile_customer_nama_customer."',
				pic_customer= '".$pic."',
				tanggal= '".$marketing_report_date_format."',
				kegiatan= '".$marketing_activity_activity."',
				hasil_kegiatan= '".$marketing_activity_purpose."',
				update_date=NOW(),
				update_by='".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."',
				update_ip=NULL
			where 
				id_mkt_report = '".$id_marketing_report."'";
			$res1 = $con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();

			// $nqu = 'file_'.$res1.'_'.sanitize_filename($fileUpload);
			// $que = "update pro_marketing_report_master set file_upload = '".$nqu."' where id_mkt_report = '".$id_marketing_report."'";
			// $con->setQuery($que);
			// $oke = $oke && !$con->hasError();

			if(!is_array_empty($_POST["newdok1"])){
				foreach($_POST["newdok1"] as $idx1=>$val1){
					$newdok1 = htmlspecialchars($_POST["newdok1"][$idx1], ENT_QUOTES);
					// $newdok2 = htmlspecialchars($_POST["newdok2"][$idx1], ENT_QUOTES);
					$newdok2 = htmlspecialchars($_FILES['newdok2']['name'][$idx1],ENT_QUOTES);     	
					if($newdok1){
						$sql2 = "insert into pro_marketing_report_master_file(id_mkt_report,keterangan,file_ori) values ('".$id_marketing_report."', '".$newdok1."', '".sanitize_filename($newdok2)."')";
						$idk = $con->setQuery($sql2);
						$oke = $oke && !$con->hasError();
						
						if($newdok2){
							$lampiran = 'mkt_report_'.$id_marketing_report.'_'.$idk.'_'.sanitize_filename($newdok2);
							$upload[$idx1] = $lampiran;
							
							$sql3 = "update pro_marketing_report_master_file set file_upload = '".$lampiran."' where id_file_upload = '".$idk."'";
							$con->setQuery($sql3);
							$oke = $oke && !$con->hasError();
						}
					}
				}
			}
					
			if(!is_array_empty($_POST["doksup"])){
				foreach($_POST["doksup"] as $idx2=>$val2){
					if(!$_POST["doknya"][$idx2]){
						$sql4 = "delete from pro_marketing_report_master_file where id_file_upload = '".$idx2."'";
						$con->setQuery($sql4);
						$oke = $oke && !$con->hasError();

						$tmpPic = glob($pathfile."/mkt_report_".$id_mkt_report."_".$idx2."_*.{pdf,docx}", GLOB_BRACE);
						if(count($tmpPic) > 0){
							foreach($tmpPic as $datx)
								$delPic[$idx2] = $datx;
						}
					}
				}
			}

			$url = BASE_URL_CLIENT."/marketing-report.php";
			$msg = "Data behasil diupdate";

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
		}else if($act == "ajukan"){
				$oke = true;
				$con->beginTransaction();
				$con->clearError();
				$cek1 = "select id_wilayah from pro_customer where id_customer = '".$profile_customer_nama_customer."'";
				$row1 = $con->getRecord($cek1);

				$sqlcek01 = "select * from pro_mapping_spv where id_mkt = '".$sesuser."'";
				$rescek01 = $con->getResult($sqlcek01);

				$maxId=1;
				$sqlmax = "select max(id_disposisi) as last_id from pro_marketing_report_master_disposisi where id_mkt_report = '".$id_marketing_report."'";
				$resmax = $con->getRecord($sqlmax);
				$maxId=$maxId+ (int) $resmax['last_id'];
				if(count($rescek01) > 0){

					$id_spv = "";
					foreach($rescek01 as $idx1=>$val1){
						$id_spv .= ", ".$val1['id_spv'];
					}
					$id_spv = substr($id_spv, 2);
					/*insert disposisi dulu kemudian update*/
					$insertDispo = "insert into pro_marketing_report_master_disposisi(
									id_disposisi,
									id_mkt_report,
									disposisi
								) values (
									'".$maxId."',
									'".$id_marketing_report."',
									'1'
								)";
					$resDispo = $con->setQuery($insertDispo);
					/*(ada spv nya)*/
					$sql1 = "update pro_marketing_report_master set 
						status= '1'
					where 
						id_mkt_report = '".$id_marketing_report."'";
					$res1 = $con->setQuery($sql1);/*update status*/
				}else{
					if($row1['id_wilayah'] != $seswil){
						/*insert disposisi dulu kemudian update*/
						$insertDispo = "insert into pro_marketing_report_master_disposisi(
							id_disposisi,
							id_mkt_report,
							disposisi
						) values (
							'".$maxId."',
							'".$id_marketing_report."'.
							'3'
						)";

						$resDispo = $con->setQuery($insertDispo);
						/*(tidak ada spv nya menyebrang BM)*/
						$sql1 = "update pro_marketing_report_master set 
							status= '3'
						where 
							id_mkt_report = '".$id_marketing_report."'";
						$res1 = $con->setQuery($sql1);/*update status*/
					}else{
						$insertDispo = "insert into pro_marketing_report_master_disposisi(
							id_disposisi,
							id_mkt_report,
							disposisi
						) values (
							'".$maxId."',
							'".$id_marketing_report."',
							'2'
						)";
						$resDispo = $con->setQuery($insertDispo);
						/*(tidak ada spv nya tidak menyebrang BM)*/
						$sql1 = "update pro_marketing_report_master set 
							status= '2'
						where 
							id_mkt_report = '".$id_marketing_report."'";
						$res1 = $con->setQuery($sql1);/*update status*/
					}
				}
			
			
				$oke  = $oke && !$con->hasError();
				$url = BASE_URL_CLIENT."/marketing-report.php";
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
		}else if($act == "approve"){
			$oke = true;
			$con->beginTransaction();
			$con->clearError();
			$catatan = htmlspecialchars($_POST['catatan'], ENT_QUOTES);

			$maxId=1;
			$sqlmax = "select max(id_disposisi) as last_id from pro_marketing_report_master_disposisi where id_mkt_report = '".$id_marketing_report."'";
			$resmax = $con->getRecord($sqlmax);
			$maxId=$maxId+ (int) $resmax['last_id'];

			if ($sesrol=='20') {
				$disp=1;
				$insertDispo = "insert into pro_marketing_report_master_disposisi(
							id_disposisi,
							id_mkt_report,
							disposisi
						) values (
							'".$maxId."',
							'".$id_marketing_report."',
							'2'
						)";
				$resDispo = $con->setQuery($insertDispo);
				$sqlreport = "
				update pro_marketing_report_master set 
					status='2'
				where 
					id_mkt_report = '".$id_marketing_report."'";
				$resReport = $con->setQuery($sqlreport);

			}else if ($sesrol=='7') {
				$cek1 = "select id_wilayah from pro_customer where id_customer = '".$profile_customer_nama_customer."'";
				$row1 = $con->getRecord($cek1);
				if($row1['id_wilayah'] != $seswil){
					$disp=2;
					$insertDispo = "insert into pro_marketing_report_master_disposisi(
							id_disposisi,
							id_mkt_report,
							disposisi
						) values (
							'".$maxId."',
							'".$id_marketing_report."',
							'3'
						)";
					$resDispo = $con->setQuery($insertDispo);
					$sqlreport = "
					update pro_marketing_report_master set 
						status='3'
					where 
						id_mkt_report = '".$id_marketing_report."'";
					$resReport = $con->setQuery($sqlreport);
				}else{
					$disp=3;
				}
			}
			
			$sql1 = "
			update pro_marketing_report_master_disposisi set 
				catatan= '".$catatan."',
				result= '1',
				tanggal= NOW(),
				pic= '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."',
				create_date=NOW(),
				create_by='".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."',
				create_ip=NULL
			where 
				id_mkt_report = '".$id_marketing_report."' and disposisi='".$disp."'";
			
			$res1 = $con->setQuery($sql1);

			

			$oke  = $oke && !$con->hasError();
			$url = BASE_URL_CLIENT."/marketing-report.php";
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
	}
?>

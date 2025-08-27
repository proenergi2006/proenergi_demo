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

	$id_ruangan 	= $idr;
	$id_user 		= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);
	$id_cabang 		= htmlspecialchars($_POST["id_cabang"], ENT_QUOTES);
	$nama_ruangan 	= htmlspecialchars($_POST["nama_ruangan"], ENT_QUOTES);
	
	$filePhoto1 = htmlspecialchars($_FILES['attach_foto']['name'], ENT_QUOTES);
	$sizePhoto1 = htmlspecialchars($_FILES['attach_foto']['size'], ENT_QUOTES);
	$tempPhoto1 = htmlspecialchars($_FILES['attach_foto']['tmp_name'], ENT_QUOTES);
	$tipePhoto1 = htmlspecialchars($_FILES['attach_foto']['type'], ENT_QUOTES);
	$extPhoto1 	= substr($filePhoto1,strrpos($filePhoto1,'.'));


	if($act == "add"){
		if($id_cabang == "" || $nama_ruangan == ""){
			$con->close();
			$flash->add("error", "KOSONG", BASE_REFERER);
		} else{
			$oke = true;
			$con->beginTransaction();
			$con->clearError();

			$sql1 = "
				insert into pro_master_ruangan(id_cabang, nama_ruangan, created_time, created_by, created_ip) 
				values ('".$id_cabang."', '".$nama_ruangan."',  NOW(), '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."', '".$_SERVER['REMOTE_ADDR']."')
			";
			$idr = $con->setQuery($sql1);
			$oke = $oke && !$con->hasError();

			if($filePhoto1){
				$pathnya = $public_base_directory.'/files/uploaded_user/lampiran';
				if(!file_exists($pathnya.'/ruangan/')) mkdir($pathnya.'/ruangan', 0777);
				$fileUploadName = '/ruangan/PICRM_'.$idr.'_'.md5($idr.'_'.basename($filePhoto1, $extPhoto1)).$extPhoto1;
				$fileOriginName = sanitize_filename($filePhoto1);
				$fileUploadNya 	= $pathnya.$fileUploadName;

				$sql2 = "update pro_master_ruangan set attach_foto = '".$fileUploadName."', attach_foto_ori = '".$fileOriginName."' where id_ruangan = '".$idr."'";
				$con->setQuery($sql2);
				$oke = $oke && !$con->hasError();
			}

			$url = BASE_URL_CLIENT."/reservasi-ruangan-master.php";
			$msg = "Data behasil disimpan";

			if ($oke){
				$con->commit();
				$con->close();
				
				if($filePhoto1){
					$arrFiles = glob($public_base_directory."/files/uploaded_user/lampiran/ruangan/PICRM_".$idr."_*.{jpg,jpeg,gif,png,pdf,zip,rar}", GLOB_BRACE);
					if(count($arrFiles) > 0){
						foreach($arrFiles as $data){
							unlink($data);
						}
					}

					$tujuan  = $fileUploadNya;
					$mantab  = move_uploaded_file($tempPhoto1, $tujuan);
					if(file_exists($tempPhoto1)) unlink($tempPhoto1);
				}

				$flash->add("success", $msg, $url);
			} else{
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
			}
		}
	}

	if($act == "update"){
		if($id_cabang == "" || $nama_ruangan == ""){
			$con->close();
			$flash->add("error", "KOSONG", BASE_REFERER);
		} else{
			$oke = true;
			$con->beginTransaction();
			$con->clearError();
			
			$sql1 = "
				update pro_master_ruangan set id_cabang = '".$id_cabang."', nama_ruangan = '".$nama_ruangan."', 
				lastupdate_time = NOW(), lastupdate_ip = '".$_SERVER['REMOTE_ADDR']."', lastupdate_by = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."' 
				where id_ruangan = '".$id_ruangan."'
			";
			$con->setQuery($sql1);
			$oke = $oke && !$con->hasError();
			$url = BASE_URL_CLIENT."/reservasi-ruangan-master.php";

			if($filePhoto1){
				$pathnya = $public_base_directory.'/files/uploaded_user/lampiran';
				if(!file_exists($pathnya.'/ruangan/')) mkdir($pathnya.'/ruangan', 0777);
				$fileUploadName = '/ruangan/PICRM_'.$id_ruangan.'_'.md5($id_ruangan.'_'.basename($filePhoto1, $extPhoto1)).$extPhoto1;
				$fileOriginName = sanitize_filename($filePhoto1);
				$fileUploadNya 	= $pathnya.$fileUploadName;

				$sql2 = "update pro_master_ruangan set attach_foto = '".$fileUploadName."', attach_foto_ori = '".$fileOriginName."' where id_ruangan = '".$id_ruangan."'";
				$con->setQuery($sql2);
				$oke = $oke && !$con->hasError();
			}

			if ($oke){
				$con->commit();
				$con->close();

				if($filePhoto1){
					$arrFiles = glob($public_base_directory."/files/uploaded_user/lampiran/ruangan/PICRM_".$id_ruangan."_*.{jpg,jpeg,gif,png,pdf,zip,rar}", GLOB_BRACE);
					if(count($arrFiles) > 0){
						foreach($arrFiles as $data){
							unlink($data);
						}
					}

					$tujuan  = $fileUploadNya;
					$mantab  = move_uploaded_file($tempPhoto1, $tujuan);
					if(file_exists($tempPhoto1)) unlink($tempPhoto1);
				}

				header("location: ".$url);	
				exit();
			} else{
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
			}
		}
	}
?>

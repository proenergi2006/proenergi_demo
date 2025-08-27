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
	$act	= ($enk['act'] == "")?htmlspecialchars($_POST["act"], ENT_QUOTES):$enk['act'];

	$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);	
	$idk 	= htmlspecialchars($_POST["idk"], ENT_QUOTES);	
	$judul	= htmlspecialchars($_POST["judul"], ENT_QUOTES);	
	$pesan 	= htmLawed($_POST["pesan"], array('safe'=>1));

	$kategori	= htmlspecialchars($_POST["kategori"], ENT_QUOTES);	

	$filePhoto 	= htmlspecialchars($_FILES['attachment_order']['name'],ENT_QUOTES);
	$sizePhoto 	= htmlspecialchars($_FILES['attachment_order']['size'],ENT_QUOTES);
	$tempPhoto 	= htmlspecialchars($_FILES['attachment_order']['tmp_name'],ENT_QUOTES);
	$extPhoto 	= substr($filePhoto,strrpos($filePhoto,'.'));
	$max_size	= 2 * 1024 * 1024;
	$allow_type	= array(".jpg", ".jpeg", ".JPG", ".png", ".pdf", ".rar", ".zip");
	$pathfile	= $public_base_directory.'/files/uploaded_user/lampiran';

	if($idr == "" || $judul == "" || $pesan == ""){
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else if($filePhoto != "" && $sizePhoto > $max_size){
		$con->close();
		$flash->add("error", "Ukuran file terlalu besar, melebihi 2MB...", BASE_REFERER);
	} else if($filePhoto != "" && !in_array($extPhoto, $allow_type)){
		$con->close();
		$flash->add("error", "Tipe file tidak diperbolehkan...", BASE_REFERER);
	} else{
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		if($act == "add"){
			$msg = "GAGAL_MASUK";
			if($filePhoto != ""){
				$upl = true;
				$sql = "
					insert into pro_customer_update(id_customer, judul, kategori, pesan, attachment_order_ori, created_time, created_ip, created_by) values ('".$idr."', '".$judul."', 
					'".$kategori."', '".$pesan."', '".sanitize_filename($filePhoto)."', NOW(), '".$_SERVER['REMOTE_ADDR']."', '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."')
				";
				$idk = $con->setQuery($sql);
				$oke  = $oke && !$con->hasError();

				$nqu = 'PUD_'.$idk.'_'.sanitize_filename($filePhoto);
				$que = "update pro_customer_update set attachment_order = '".$nqu."' where id_cu = '".$idk."'";
				$con->setQuery($que);
				$oke = $oke && !$con->hasError();
			} else{
				$upl = false;
				$nqu = '';
				$sql = "
					insert into pro_customer_update(id_customer, judul, kategori, pesan, created_time, created_ip, created_by) values ('".$idr."', '".$judul."', '".$kategori."', 
					'".$pesan."', NOW(), '".$_SERVER['REMOTE_ADDR']."', '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."')
				";
				$idk = $con->setQuery($sql);
				$oke  = $oke && !$con->hasError();
			}
		} 

		else if($act == 'update'){
			$msg = "GAGAL_UBAH";
			if($filePhoto != ""){
				$upl = true;
				$nqu = 'PUD_'.$idk.'_'.sanitize_filename($filePhoto);
				$sql = "
					update pro_customer_update set judul = '".$judul."', kategori = '".$kategori."', pesan = '".$pesan."', attachment_order = '".$nqu."', 
					attachment_order_ori = '".sanitize_filename($filePhoto)."', lastupdate_time = NOW(), lastupdate_ip = '".$_SERVER['REMOTE_ADDR']."', 
					lastupdate_by = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."' where id_customer = '".$idr."' and id_cu = '".$idk."'
				";
				$con->setQuery($sql);
				$oke  = $oke && !$con->hasError();
			} else{
				$upl = false;
				$nqu = '';
				$sql = "
					update pro_customer_update set judul = '".$judul."', kategori = '".$kategori."', pesan = '".$pesan."', 
					lastupdate_time = NOW(), lastupdate_ip = '".$_SERVER['REMOTE_ADDR']."', lastupdate_by = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."' 
					where id_customer = '".$idr."' and id_cu = '".$idk."'
				";
				$con->setQuery($sql);
				$oke  = $oke && !$con->hasError();
			}
		}
		$url  = BASE_URL_CLIENT."/customer-permohonan-update-detail.php?".paramEncrypt("idr=".$idr."&idk=".$idk);

		if ($oke){
			$mantab  = true;
			if($upl){
				$tmpPot = glob($pathfile."/PUD_".$idk."_*.{jpg,jpeg,gif,png,pdf,rar,zip}", GLOB_BRACE);
				if(count($tmpPot) > 0){
					foreach($tmpPot as $datj)
						if(file_exists($datj)) unlink($datj);
				}
				$tujuan  = $pathfile."/".$nqu;
				$mantab  = $mantab && move_uploaded_file($tempPhoto, $tujuan);
				if(file_exists($tempPhoto)) unlink($tempPhoto);
			}
			if($mantab){
				$con->commit();
				$con->close();
				header("location: ".$url);	
				exit();
			} else{
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", $msg, BASE_REFERER);
			}
		} else{
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", $msg, BASE_REFERER);
		}
	}
?>

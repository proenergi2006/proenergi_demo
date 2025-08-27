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

	$periode_awal	= htmlspecialchars($_POST["periode_awal"], ENT_QUOTES);	
	$periode_akhir 	= htmlspecialchars($_POST["periode_akhir"], ENT_QUOTES);
	$fileAttach 	= htmlspecialchars($_FILES['attach_harga']['name'],ENT_QUOTES);
	$sizeAttach 	= htmlspecialchars($_FILES['attach_harga']['size'],ENT_QUOTES);
	$tempAttach 	= htmlspecialchars($_FILES['attach_harga']['tmp_name'],ENT_QUOTES);
	$extAttach 		= substr($fileAttach,strrpos($fileAttach,'.'));
	$note			= htmLawed($_POST["note"], array('safe'=>1));

	$user_ip		= $_SERVER['REMOTE_ADDR'];
	$user_pic		= paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname']);
	$user_role		= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	$url_back 		= BASE_URL_CLIENT."/attach-harga-minyak.php";

	$allow_type	= array(".jpg", ".jpeg", ".JPG", ".png", ".pdf", ".xls", ".xlsx");
	$pathfile	= $public_base_directory.'/files/uploaded_user/lampiran';

	if($periode_awal == "" || $periode_akhir == ""){
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else if($act == "add" && $fileAttach == ""){
		$con->close();
		$flash->add("error", "Lampiran masih kosong", BASE_REFERER);
	} else if($fileAttach != "" && !in_array($extAttach, $allow_type)){
		$con->close();
		$flash->add("error", "Tipe file lampiran yang diperbolehkan hanya .jpg, .png, .pdf, .xlsx, .xls", BASE_REFERER);
	} else{
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		if($act == 'add'){
			$msg = "GAGAL_MASUK";
			$sql = "insert into pro_attach_harga_minyak(periode_awal, periode_akhir, note_attach, attach_harga_ori, created_time, created_ip, created_by) 
					values ('".tgl_db($periode_awal)."', '".tgl_db($periode_akhir)."', '".$note."', '".sanitize_filename($fileAttach)."', NOW(), '".$user_ip."', '".$user_pic."')";
			$idr = $con->setQuery($sql);
			$oke = $oke && !$con->hasError();

			$nqu = 'aPrice_'.$idr.'_'.sanitize_filename($fileAttach);
			$que = "update pro_attach_harga_minyak set attach_harga = '".$nqu."' where id_master = '".$idr."'";
			$con->setQuery($que);
			$oke = $oke && !$con->hasError();
		} 
		
		else if($act == 'update'){
			$msg = "GAGAL_UBAH";
			if($fileAttach){
				$nqu = 'aPrice_'.$idr.'_'.sanitize_filename($fileAttach);
				$sql = "update pro_attach_harga_minyak set note_attach = '".$note."', attach_harga = '".$nqu."', attach_harga_ori = '".sanitize_filename($fileAttach)."', 
						lastupdate_time = NOW(), lastupdate_ip = '".$user_ip."', lastupdate_by = '".$user_pic."' where id_master = '".$idr."'";
			} else{
				$sql = "update pro_attach_harga_minyak set note_attach = '".$note."', lastupdate_time = NOW(), lastupdate_ip = '".$user_ip."', lastupdate_by = '".$user_pic."' 
						where id_master = '".$idr."'";
			}
			$con->setQuery($sql);
			$oke  = $oke && !$con->hasError();
		}

		if ($oke){
			$mantab  = true;
			if($fileAttach){
				$tmpPot = glob($pathfile."/aPrice_".$idr."_*.{jpg,jpeg,gif,png,pdf,xls,xlsx}", GLOB_BRACE);
				if(count($tmpPot) > 0){
					foreach($tmpPot as $datj)
						if(file_exists($datj)) unlink($datj);
				}
				$tujuan  = $pathfile."/".$nqu;
				$mantab  = $mantab && move_uploaded_file($tempAttach, $tujuan);
				if(file_exists($tempAttach)) unlink($tempAttach);
			}
			if($mantab){
				$con->commit();
				$con->close();
				header("location: ".$url_back);
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

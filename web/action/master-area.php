<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$act	= ($enk['act'] == "")?htmlspecialchars($_POST["act"], ENT_QUOTES):$enk['act'];
	$url_back 		= BASE_URL_CLIENT."/master-area.php";
	
	$area	= htmlspecialchars($_POST["nama_area"], ENT_QUOTES);	
	$active = htmlspecialchars($_POST["active"], ENT_QUOTES);
	$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
	$note 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["note"], ENT_QUOTES));
	$wapu 	= htmlspecialchars($_POST["cb_wapu"], ENT_QUOTES);
	$fileAttach 	= htmlspecialchars($_FILES['attach_lampiran']['name'],ENT_QUOTES);
	$sizeAttach 	= htmlspecialchars($_FILES['attach_lampiran']['size'],ENT_QUOTES);
	$tempAttach 	= htmlspecialchars($_FILES['attach_lampiran']['tmp_name'],ENT_QUOTES);
	$extAttach 		= substr($fileAttach,strrpos($fileAttach,'.'));
	
	$allow_type	= array(".jpg", ".jpeg", ".JPG", ".png", ".pdf", ".xls", ".xlsx");
	$pathfile	= $public_base_directory.'/files/uploaded_user/lampiran';
	
	if($area == ""){
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else if($fileAttach != "" && !in_array($extAttach, $allow_type)){
		$con->close();
		$flash->add("error", "Tipe file lampiran yang diperbolehkan hanya .jpg, .png, .pdf, .xlsx, .xls", BASE_REFERER);
	} else{
		$oke = true;
		$con->beginTransaction();
		$con->clearError();
		
		if($act == 'add'){
			$fileAttach = ($fileAttach ? $fileAttach : '');
			$sql = "
				insert into pro_master_area(nama_area, lampiran_ori, wapu,  is_active, created_time, created_ip, created_by) 
				values ('".$area."', '".sanitize_filename($fileAttach)."', '".$wapu."' , '".$active."', NOW(), '".$_SERVER['REMOTE_ADDR']."', 
				'".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."')
			";
			$idr = $con->setQuery($sql);
			$oke = $oke && !$con->hasError();
			
			$nqu = 'areaLamp_'.$idr.'_'.sanitize_filename($fileAttach);
			$que = "update pro_master_area set lampiran = '".$nqu."' where id_master = '".$idr."'";
			$con->setQuery($que);
			$oke = $oke && !$con->hasError();
		} else if($act == 'update'){
			$msg = "GAGAL_UBAH";
			if($fileAttach){
				$nqu = 'areaLamp_'.$idr.'_'.sanitize_filename($fileAttach);
				$sql = "
					update pro_master_area set nama_area = '".$area."', lampiran = '".$nqu."', lampiran_ori = '".sanitize_filename($fileAttach)."', wapu = '".$wapu."', is_active = '".$active."', 
					lastupdate_time = NOW(), lastupdate_ip = '".$_SERVER['REMOTE_ADDR']."', lastupdate_by = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."' 
					where id_master = ".$idr;
			} else{
				$sql = "
					update pro_master_area set nama_area = '".$area."', wapu = '".$wapu."', is_active = '".$active."', 
					lastupdate_time = NOW(), lastupdate_ip = '".$_SERVER['REMOTE_ADDR']."', lastupdate_by = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."' 
					where id_master = ".$idr;
			}
			
			$con->setQuery($sql);
			$oke  = $oke && !$con->hasError();
		}
		
		if ($oke){
			$mantab  = true;
			if($fileAttach){
				$tmpPot = glob($pathfile."/areaLamp_".$idr."_*.{jpg,jpeg,gif,png,pdf,xls,xlsx}", GLOB_BRACE);
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

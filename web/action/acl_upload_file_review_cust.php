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
	
	$idr	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
	$idk	= htmlspecialchars($_POST["idk"], ENT_QUOTES);
	$urut   = htmlspecialchars($_POST["urut"], ENT_QUOTES);
	$role 	= htmlspecialchars($_POST["role"], ENT_QUOTES);

	$fileAttach = htmlspecialchars($_FILES['file']['name'],ENT_QUOTES);
	$sizeAttach = htmlspecialchars($_FILES['file']['size'],ENT_QUOTES);
	$tempAttach = htmlspecialchars($_FILES['file']['tmp_name'],ENT_QUOTES);
	$extAttach  = substr($fileAttach,strrpos($fileAttach,'.'));


	$max_size 	= 2 * 1024 * 1024;
	$allow_type	= array(".jpg", ".jpeg", ".JPG", ".png", ".pdf", ".zip", ".rar");
	$pathfile	= $public_base_directory.'/files/uploaded_user/lampiran';
	$user_ip	= $_SERVER['REMOTE_ADDR'];
	$user_pic	= paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname']);
	
	if($idk == "" || $idr==""){
		$flash->add("error", "KOSONG", BASE_REFERER);
	}else if($fileAttach != "" && $sizeAttach > $max_size){
		$con->close();
		// $flash->add("error", "Ukuran file lampiran terlalu besar", BASE_REFERER);
		$msg=['type'=>'error','pesan'=>'Ukuran file lampiran terlalu besar'];
		echo json_encode($msg);
	} else if($fileAttach != "" && !in_array($extAttach, $allow_type)){
		$con->close();
		// $flash->add("error", "Tipe file lampiran yang diperbolehkan hanya .jpg, .png, .pdf, .zip, .rar", BASE_REFERER);
		$msg=['type'=>'error','pesan'=>'Tipe file lampiran yang diperbolehkan hanya .jpg, .png, .pdf, .zip, .rar'];
		echo json_encode($msg);
	} else{
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		$nqu = 'RA_'.$idk.'_'.$urut.'_'.sanitize_filename($fileAttach);
		$que = "insert into pro_customer_review_attchment (id_review,id_verification,no_urut,review_attach,review_attach_ori) values(".$idk.",".$idr.",'".$urut."','".$nqu."','".sanitize_filename($fileAttach)."')";
		$con->setQuery($que);
		$oke = $oke && !$con->hasError();

		if ($oke){
			$mantab  = true;
			if($fileAttach){
				$tmpPot = glob($pathfile."/RA_".$idk.'_'.$urut."_*.{jpg,jpeg,gif,png,pdf,zip,rar}", GLOB_BRACE);
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
				// header("location: ".$url);	
				$msg=['type'=>'success','pesan'=>'Berhasil simpan'];
				echo json_encode($msg);
				// exit();
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

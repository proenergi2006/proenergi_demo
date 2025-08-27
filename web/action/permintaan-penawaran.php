<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload", "mailgen", "htmlawed");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$act	= (isset($enk['act']) && $enk['act'] == "") ? htmlspecialchars($_POST["act"], ENT_QUOTES) : $enk['act'];
	$idr	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
	$reply	= $con->getOne("select email_user from acl_user where id_user = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."'");
	$sescus = paramDecrypt($_SESSION['sinori' . SESSIONID]['customer']);
	if ($sescus==0)
		$sescus = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);

	$judul_pmnt	= htmlspecialchars($_POST["judul_pmnt"], ENT_QUOTES);	
	$pesan_pmnt = htmLawed($_POST["pesan_pmnt"], array('safe'=>1));
	$filePhoto 	= htmlspecialchars($_FILES['attachment_order']['name'],ENT_QUOTES);
	$sizePhoto 	= htmlspecialchars($_FILES['attachment_order']['size'],ENT_QUOTES);
	$tempPhoto 	= htmlspecialchars($_FILES['attachment_order']['tmp_name'],ENT_QUOTES);
	$extPhoto 	= substr($filePhoto,strrpos($filePhoto,'.'));

	$max_size	= 2 * 1024 * 1024;
	$allow_type	= array(".jpg", ".jpeg", ".JPG", ".png", ".pdf", ".rar", ".zip");
	$pathfile	= $public_base_directory.'/files/uploaded_user/lampiran';

	if($judul_pmnt == "" || $pesan_pmnt == ""){
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else if($filePhoto != "" && $sizePhoto > $max_size){
		$con->close();
		$flash->add("error", "Ukuran file terlalu besar, melebihi 2MB...", BASE_REFERER);
	} else if($filePhoto != "" && !in_array($extPhoto, $allow_type)){
		$con->close();
		$flash->add("error", "Tipe file tidak diperbolehkan...", BASE_REFERER);
	} else{
		$cek = "select id_user, email_user, mobile_user, fullname from acl_user where id_user = (select id_marketing from pro_customer where id_customer = '".$sescus."')";
		$row = $con->getRecord($cek);
		$pic_user 	= $row['id_user'];
		$pic_name 	= $row['fullname'];
		$pic_email	= $row['email_user'];
		$pic_telp	= $row['mobile_user'];

		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		$msg = "GAGAL_MASUK";
		if($filePhoto != ""){
			$upl = true;
			$sql = "insert into pro_permintaan_penawaran(pic_user, pic_name, pic_email, pic_telp, id_customer, judul_pmnt, pesan_pmnt, attachment_order_ori, created_time, 
					created_ip, created_by) values ('".$pic_user."', '".$pic_name."', '".$pic_email."', '".$pic_telp."', '".$sescus."', '".$judul_pmnt."', '".$pesan_pmnt."', 
					'".sanitize_filename($filePhoto)."', NOW(), '".$_SERVER['REMOTE_ADDR']."', '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."')";
			$idr = $con->setQuery($sql);
			$oke = $oke && !$con->hasError();

			$nqu = 'tawar_'.$idr.'_'.sanitize_filename($filePhoto);
			$que = "update pro_permintaan_penawaran set attachment_order = '".$nqu."' where id_pmnt = '".$idr."'";
			$con->setQuery($que);
			$oke = $oke && !$con->hasError();
		} else{
			$upl = false;
			$nqu = '';
			$sql = "insert into pro_permintaan_penawaran(pic_user, pic_name, pic_email, pic_telp, id_customer, judul_pmnt, pesan_pmnt, created_time, created_ip, created_by) 
					values ('".$pic_user."', '".$pic_name."', '".$pic_email."', '".$pic_telp."', '".$sescus."', '".$judul_pmnt."', '".$pesan_pmnt."', NOW(), 
					'".$_SERVER['REMOTE_ADDR']."', '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."')";
			$idr = $con->setQuery($sql);
			$oke = $oke && !$con->hasError();
		}
		$url = BASE_URL_CLIENT."/permintaan-penawaran-detail.php?".paramEncrypt("idr=".$idr);
		
		if($oke){
			$mantab  = true;
			if($upl){
				$tmpPot = glob($pathfile."/tawar_".$idr."_*.{jpg,jpeg,gif,png,pdf,rar,zip}", GLOB_BRACE);
				if(count($tmpPot) > 0){
					foreach($tmpPot as $datj)
						if(file_exists($datj)) unlink($datj);
				}
				$tujuan  = $pathfile."/".$nqu;
				$mantab  = $mantab && move_uploaded_file($tempPhoto, $tujuan);
				if(file_exists($tempPhoto)) unlink($tempPhoto);
			}
			if($mantab){
				$mail = new PHPMailer;
				$mail->isSMTP();
				$mail->Host = 'smtp.gmail.com';
				$mail->Port = 465;
				$mail->SMTPSecure = 'ssl';
				$mail->SMTPAuth = true;
				$mail->SMTPKeepAlive = true;
				$mail->Username = USR_EMAIL_PROENERGI202389;
				$mail->Password = PWD_EMAIL_PROENERGI202389;
				
				$mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');
				$mail->addReplyTo($reply);
				$mail->addAddress($pic_email);		
				$mail->Subject = $judul_pmnt;
				$mail->msgHTML($pesan_pmnt);
				if($upl){
					$mail->addAttachment($tujuan, sanitize_filename($filePhoto));
				}
				if($mail->send()){
					$con->commit();
					$con->close();
					header("location: ".$url);	
					exit();
				} else{
					if(file_exists($tujuan)) unlink($tujuan);
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
		} else{
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
	}	
?>

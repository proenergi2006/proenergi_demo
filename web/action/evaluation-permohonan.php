<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload", "htmlawed", "mailgen");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
	$idk 	= htmlspecialchars($_POST["idk"], ENT_QUOTES);
	$role 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	$isis 	= "";
	
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	if($role == 10){
		$summary 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["finance_summary"], ENT_QUOTES));
		$approval	= htmlspecialchars($_POST["finance_result"], ENT_QUOTES);
		$pic		= paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname']);
		$sql1 = "update pro_customer_update set finance_summary = '".$summary."', finance_result = '".$approval."', finance_tanggal = NOW(), finance_pic = '".$pic."'";
		if($approval == 1){
			$sql1 .= ", flag_disposisi = 2";
			$ems1 = "select email_user from acl_user where id_role = 7 and id_group = (select id_group from pro_customer where id_customer = '".$idr."')";
		} else{
			$sql1 .= ", flag_approval = '".$approval."', tgl_approval = NOW()";
			$ems1 = "";
		}
		$sql1 .= " where id_cu = '".$idk."' and id_customer = '".$idr."'";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();
	}

	else if($role == 7){
		$summary 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["summary"], ENT_QUOTES));
		$approval	= htmlspecialchars($_POST["approval"], ENT_QUOTES);
		$extend		= htmlspecialchars($_POST["extend"], ENT_QUOTES);
		$pic		= paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname']);
		$sql1 = "update pro_customer_update set om_summary = '".$summary."', om_result = '".$approval."', om_tanggal = NOW(), om_pic = '".$pic."'";
		if($extend == 1){
			$sql1 .= ", flag_disposisi = 3";
			$ems1 = "select email_user from acl_user where id_role = 4";
		} else if($extend == 2){
			$sql1 .= ", flag_approval = '".$approval."', tgl_approval = NOW()";
			$ems1 = ($approval == 1)?"select email_user from acl_user where id_role = 10 and id_wilayah = (select id_wilayah from pro_customer where id_customer = '".$idr."')":"";
			$isis = ($approval == 1)?1:0;
		}
		$sql1 .= " where id_cu = '".$idk."' and id_customer = '".$idr."'";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();
	}

	else if($role == 4){
		$summary 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["summary"], ENT_QUOTES));
		$approval	= htmlspecialchars($_POST["approval"], ENT_QUOTES);
		$extend		= 2;
		$pic		= paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname']);
		$sql1 = "update pro_customer_update set cfo_summary = '".$summary."', cfo_result = '".$approval."', cfo_tanggal = NOW(), cfo_pic = '".$pic."'";
		if($extend == 1){
			$sql1 .= ", flag_disposisi = 4";
		} else if($extend == 2){
			$sql1 .= ", flag_approval = '".$approval."', tgl_approval = NOW()";
			$ems1 = ($approval == 1)?"select email_user from acl_user where id_role = 10 and id_wilayah = (select id_wilayah from pro_customer where id_customer = '".$idr."')":"";
			$isis = ($approval == 1)?1:0;
		}
		$sql1 .= " where id_cu = '".$idk."' and id_customer = '".$idr."'";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();
	}
	
	else if($role == 3){
		$summary 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["summary"], ENT_QUOTES));
		$approval	= htmlspecialchars($_POST["approval"], ENT_QUOTES);
		$pic		= paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname']);
		$sql1 = "update pro_customer_update set ceo_summary = '".$summary."', ceo_result = '".$approval."', ceo_tanggal = NOW(), ceo_pic = '".$pic."', flag_approval = '".$approval."' where id_cu = '".$idk."' and id_customer = '".$idr."'";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();
	}

	if ($oke){
		if($ems1){
			$rms1 = $con->getResult($ems1);
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
			foreach($rms1 as $datms){
				$mail->addAddress($datms['email_user']);
			}
			if(!$isis){
				$mail->Subject = "Persetujuan Permohonan Update Data [".date('d/m/Y H:i:s')."]";
				$mail->msgHTML($pic." meminta persetujuan untuk permohonan update data customer <p>".BASE_SERVER."</p>");
			} else{
				$mail->Subject = "Pemutakhiran Data [".date('d/m/Y H:i:s')."]";
				$mail->msgHTML("Persetujuan untuk permohonan update data customer telah disetujui, silahkan data dimutakhirkan <p>".BASE_SERVER."</p>");
			}
			$mail->send();
		}
		$con->commit();
		$con->close();
		header("location: ".BASE_URL_CLIENT."/verifikasi-permohonan.php");	
		exit();				
	} else{
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}
?>

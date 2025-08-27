<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload", "mailgen");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
	$idk 	= htmlspecialchars($_POST["idk"], ENT_QUOTES);
	$role 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	$bali 	= false;
	
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	if($role == 7){
		$summary 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["sm_summary"], ENT_QUOTES));
		$approval	= htmlspecialchars($_POST["sm_result"], ENT_QUOTES);
		$pic		= paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname']);
		if($approval){
			$ems1 = "";
			$sql1 = "update pro_customer_lcr set sm_summary = '".$summary."', sm_result = '".$approval."', sm_tanggal = NOW(), sm_pic = '".$pic."', 
					 flag_approval = '".$approval."', tgl_approval = NOW() where id_lcr = '".$idk."' and id_customer = '".$idr."'";
			$con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();
		} else{
			$ems1 = "";
			$sql1 = "update pro_customer_lcr set sm_summary = '".$summary."' where id_lcr = '".$idk."' and id_customer = '".$idr."'";
			$con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();
		}
	}
	
	else if($role == 9){
		$summary 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["logistik_summary"], ENT_QUOTES));
		$approval	= htmlspecialchars($_POST["logistik_result"], ENT_QUOTES);
		$balik		= htmlspecialchars($_POST["balik"], ENT_QUOTES);
		$pic		= paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname']);
		if($balik){
			$bali = true;
			$ems1 = "select email_user from acl_user where id_role in(11,17) and id_user = (select id_marketing from pro_customer where id_customer = '".$idr."')";
			$sql1 = "update pro_customer_lcr set flag_disposisi = -1, flag_approval = 0, logistik_result = 0, sm_result = 0 where id_lcr = '".$idk."' and id_customer = '".$idr."'";
			$con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();
		} else{
			if($approval){
				$ems1 = "select email_user from acl_user where id_role = 7 and id_wilayah = (select id_wilayah from pro_customer where id_customer = '".$idr."')";
				$sql1 = "update pro_customer_lcr set logistik_summary = '".$summary."', logistik_result = '".$approval."', logistik_tanggal = NOW(), logistik_pic = '".$pic."', 
						 flag_disposisi = '2' where id_lcr = '".$idk."' and id_customer = '".$idr."'";
				$con->setQuery($sql1);
				$oke  = $oke && !$con->hasError();
			} else{
				$ems1 = "";
				$sql1 = "update pro_customer_lcr set logistik_summary = '".$summary."' where id_lcr = '".$idk."' and id_customer = '".$idr."'";
				$con->setQuery($sql1);
				$oke  = $oke && !$con->hasError();
			}
		}
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
			if($bali){
				$mail->Subject = "Penolakan LCR [".date('d/m/Y H:i:s')."]";
				$mail->msgHTML(paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])." menolak lcr anda <p>".BASE_SERVER."</p>");
			} else{
				$mail->Subject = "Persetujuan LCR [".date('d/m/Y H:i:s')."]";
				$mail->msgHTML(paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])." meminta persetujuan lcr <p>".BASE_SERVER."</p>");
			}
			$mail->send();
		}
		$con->commit();
		$con->close();
		header("location: ".BASE_URL_CLIENT."/verifikasi-lcr.php");	
		exit();				
	} else{
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}
?>

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
	$idr 	= isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
	$idk 	= htmlspecialchars($enk["idk"], ENT_QUOTES);
	$url 	= BASE_URL_CLIENT."/customer-permohonan-update-detail.php?".paramEncrypt("idr=".$idr."&idk=".$idk);
	
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	$ems = "select email_user from acl_user where id_role = 10 and id_wilayah = (select id_wilayah from pro_customer where id_customer = '".$idr."')";
	$sql = "update pro_customer_update set flag_disposisi = 1, finance_result = 0, om_result = 0, cfo_result = 0, flag_approval = 0 
			where id_customer = '".$idr."' and id_cu = '".$idk."'";
	$con->setQuery($sql);
	$oke  = $oke && !$con->hasError();

	if ($oke){
		$rms1 = $con->getResult($ems);
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
		$mail->Subject = "Persetujuan Permohonan Updata Data [".date('d/m/Y H:i:s')."]";
		$mail->msgHTML(paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])." meminta persetujuan permohonan updata data <p>".BASE_SERVER."</p>");
		$mail->send();

		$con->commit();
		$con->close();
		$flash->add("success", "Persetujuan untuk permohonan update data sudah diajukan", $url);
	} else{
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}
?>

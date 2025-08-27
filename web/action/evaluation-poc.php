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
	$note 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["summary"], ENT_QUOTES));
	$cabang = htmlspecialchars($_POST["cabang"], ENT_QUOTES);
	$setuju = htmlspecialchars($_POST["setuju"], ENT_QUOTES);
	$role 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	$pic	= paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname']);
	$ems1 	= null;
	$ems2 	= null;
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	if($role == -1){
		$sql = "update pro_po_customer set om_summary = '".$note."', om_result = 1, om_tanggal = NOW(), om_pic = '".$pic."', poc_approved = 1 
				where id_poc = '".$idk."' and id_customer = '".$idr."'";
		$con->setQuery($sql);
		$oke  = $oke && !$con->hasError();
	}

	else if($role == 7 || $role == 6){
		$sql = "update pro_po_customer set poc_approved = '".$setuju."', tgl_approved = NOW(), sm_summary = '".$note."', sm_result = 1, sm_tanggal = NOW(), sm_pic = '".$pic."' 
				where id_poc = '".$idk."' and id_customer = '".$idr."'";
		$con->setQuery($sql);
		$oke  = $oke && !$con->hasError();
		$ems1 = "select email_user from acl_user where id_role in(11,17) and id_user = (select id_marketing from pro_customer where id_customer = '".$idr."')";
		
		//insert to sales confirmation
		if($setuju == 1)
		{
			$sql = "insert into pro_sales_confirmation(id_customer, id_poc, id_wilayah, created_date, period_date) values (".$idr.", ".$idk.", ".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']).", NOW(), NOW());";
			$id = $con->setQuery($sql);
			$oke  = $oke && !$con->hasError();

			$sql2 = "insert into pro_sales_confirmation_approval(id_sales) values (".$id.");";
			$con->setQuery($sql2);
			$oke  = $oke && !$con->hasError();

			$ems2 = "select email_user from acl_user where id_role in(10) and id_wilayah = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."')";
		}
	}

	if ($oke)
	{
		$mail = new PHPMailer;
		$mail->isSMTP();
		$mail->Host = 'smtp.gmail.com';
		$mail->Port = 465;
		$mail->SMTPSecure = 'ssl';
		$mail->SMTPAuth = true;
		$mail->SMTPKeepAlive = true;
		$mail->Username = USR_EMAIL_PROENERGI202389;
		$mail->Password = PWD_EMAIL_PROENERGI202389;
	
		if($ems1)
		{
			$rms1 = $con->getResult($ems1);
			
			$mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');
			foreach($rms1 as $datms){
				$mail->addAddress($datms['email_user']);
			}
			if($setuju == 2){
				$mail->Subject = "Penolakan PO [".date('d/m/Y H:i:s')."]";
				$mail->msgHTML(paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])." menolak PO Customer anda <p>".BASE_SERVER."</p>");
			} else if($setuju == 1){
				$mail->Subject = "Persetujuan PO[".date('d/m/Y H:i:s')."]";
				$mail->msgHTML(paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])." Menyetujui PO Customer anda, silahkan jadwalkan pengiriman <p>".BASE_SERVER."</p>");
			}

			$mail->send();
		}

		if($ems2)
		{
			$rms2 = $con->getResult($ems2);
			
			$mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');
			foreach($rms2 as $data){
				$mail->addAddress($data['email_user']);
			}

			$mail->Subject = "Persetujuan Sales Confirmation [".date('d/m/Y H:i:s')."]";
			$mail->msgHTML(paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])." Meminta Persetujuan anda <p>".BASE_SERVER."</p>");
			$mail->send();
		}

		$con->commit();
		$con->close();
		header("location: ".BASE_URL_CLIENT."/verifikasi-poc.php");	
		exit();

	} else{
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}
?>

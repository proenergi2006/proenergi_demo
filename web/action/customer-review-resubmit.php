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
	$idr 	= isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
	$idk 	= htmlspecialchars($enk["idk"], ENT_QUOTES);
	$flag 	= htmlspecialchars($enk["flag"], ENT_QUOTES);
	$url 	= BASE_URL_CLIENT."/customer-review-detail.php?".paramEncrypt("idr=".$idr."&idk=".$idk);
	
	/*$oke = true;
	$con->beginTransaction();
	$con->clearError();

	$sql1 = "update pro_customer_verification set disposisi_result = 1, is_approved = 0, role_approve = NULL, tanggal_approved = NULL, 
			legal_result = 0, finance_result = 0, logistik_result = 0, sm_result = 0, om_result = 0, cfo_result = 0, ceo_result = 0, is_active = 1,
			legal_summary = '', finance_summary = '', logistik_summary = '', sm_summary = '', om_summary = '', cfo_summary = '', ceo_summary = '' 
			where id_verification = '".$idr."'";
	$con->setQuery($sql1);
	$oke  = $oke && !$con->hasError();

	$sql2 = "update pro_customer set need_update = 1, ajukan = 1 where id_customer = '".$idk."'";
	$con->setQuery($sql2);
	$oke  = $oke && !$con->hasError();

	if ($oke){
		$con->commit();
		$con->close();
		$flash->add("success", "Form update data dengan kode link LC-".$idr." telah dibuka untuk direvisi", $url);
	} else{
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}*/
	
	
	if($flag == 'persetujuan'){
		$user_pic = paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname']);

		$oke = true;
		$con->beginTransaction();
		$con->clearError();
	
		$ems1 = "
			select a.id_wilayah, c.id_role, c.fullname, c.username, c.email_user  
			from pro_customer a
			join pro_customer_verification b on a.id_customer = b.id_customer 
			join acl_user c on a.id_wilayah = c.id_wilayah and c.id_role in (15,9,10)
			where b.id_verification = '".$idr."'
		";
		$rms1 = $con->getResult($ems1);
		
		$sql1 = "
			update pro_customer_verification set 
			legal_data = '', legal_summary = '', legal_result = 0, legal_tgl_proses = NULL, legal_pic = '', 
			finance_data = '', finance_summary = '', finance_result = 0, finance_tgl_proses = NULL, finance_pic = '', 
			logistik_data = '', logistik_summary = '', logistik_result = 0, logistik_tgl_proses = NULL, logistik_pic = '', 
			sm_summary = '', sm_result = 0, sm_tgl_proses = NULL, sm_pic = '', 
			om_summary = '', om_result = 0, om_tgl_proses = NULL, om_pic = '', 
			cfo_summary = '', cfo_result = 0, cfo_tgl_proses = NULL, cfo_pic = '', 
			ceo_summary = '', ceo_result = 0, ceo_tgl_proses = NULL, ceo_pic = '', 
			is_reviewed = 1, is_active = 1, disposisi_result = 1, is_approved = 0, role_approve = NULL, tanggal_approved = NULL		
			where id_verification = '".$idr."'
		";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();
	
		if ($oke){
			if(count($rms1) > 0){
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
				$mail->Subject = "Verifikasi data customer [".date('d/m/Y H:i:s')."]";
				$mail->msgHTML($user_pic." meminta anda untuk melakukan verifikasi data customer <p>".BASE_SERVER."</p>");
				$mail->send();
			}

			$con->commit();
			$con->close();
			$flash->add("success", "Persetujuan untuk Customer Review Form sudah diajukan", $url);
		} else{
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
	}
?>

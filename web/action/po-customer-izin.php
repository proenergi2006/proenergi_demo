<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload","mailgen");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$idr 	= isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
	$idk 	= htmlspecialchars($enk["idk"], ENT_QUOTES);
	$url  	= BASE_URL_CLIENT."/po-customer-detail.php?".paramEncrypt("idr=".$idr."&idk=".$idk);
	// $sesrol = paramDecrypt($_SESSION['sinori']['id_role']);
	$sesrol = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	if(in_array($sesrol, array(11, 17, 18))){
		// $ems1 = "select email_user from acl_user where id_role = 7 and id_wilayah = (select id_wilayah from pro_customer where id_customer = '".$idr."')";
		$penawaran = "select pn.id_cabang from pro_penawaran pn join pro_po_customer pr on pr.id_penawaran = pn.id_penawaran where pr.id_poc = ".$idk;
		$pn = $con->getRecord($penawaran);

		$qsc 	= "select * from pro_sales_confirmation where id_poc = ".$idk." and id_customer = ".$idr." and id_wilayah = ".$pn['id_cabang'];
		$sc 	= $con->getRecord($qsc);
		
		if(!$sc){
			$sql = "insert into pro_sales_confirmation(id_customer, id_poc, id_wilayah, created_date, period_date) values (".$idr.", ".$idk.", ".$pn['id_cabang'].", NOW(), NOW());";
			$id = $con->setQuery($sql);
			$oke  = $oke && !$con->hasError();

			$sql2 = "insert into pro_sales_confirmation_approval(id_sales) values (".$id.");";
			$con->setQuery($sql2);
			$oke  = $oke && !$con->hasError();
		} else{
			$sql = "update pro_sales_confirmation set created_date = NOW(), period_date = NOW(), flag_approval = 0, role_approved = NULL, tgl_approved = NULL, disposisi = 1  where id = ".$sc['id'];
			$id = $con->setQuery($sql);
			$oke  = $oke && !$con->hasError();

			$sql3 = "delete from pro_sales_confirmation_approval where id_sales = ".$sc['id'];
			$con->setQuery($sql3);
			$oke  = $oke && !$con->hasError();

			$sql2 = "insert into pro_sales_confirmation_approval(id_sales) values (".$sc['id'].");";
			$con->setQuery($sql2);
			$oke  = $oke && !$con->hasError();
		}

		$id_wilayah = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
		$ems1 		= "select email_user from acl_user where id_role in(10) and id_wilayah = '".$pn['id_cabang']."'";
	}

	$sql1 = "update pro_po_customer set disposisi_poc = 1, poc_approved = 0, sm_result = 0 where id_customer = '".$idr."' and id_poc = '".$idk."'";
	$con->setQuery($sql1);
	$oke  = $oke && !$con->hasError();

	if ($oke){
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
		$mail->Subject = "Persetujuan PO Customer [".date('d/m/Y H:i:s')."]";
		$mail->msgHTML(paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])." meminta persetujuan untuk po customer <p>".BASE_SERVER."</p>");
		$mail->send();

		$con->commit();
		$con->close();
		$flash->add("success", "Persetujuan untuk dokumen ini sudah diajukan", $url);
	} else{
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}
?>

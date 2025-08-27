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
	$act	= ($enk['act'] == "")?htmlspecialchars($_POST["act"], ENT_QUOTES):$enk['act'];
	$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);	
	$url 	= BASE_URL_CLIENT."/po-customer-om-detail.php?".paramEncrypt("idr=".$idr);
	$pic	= paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname']);

	$btnSbmt1 	= htmlspecialchars($_POST["btnSbmt1"], ENT_QUOTES);	
	$btnSbmt2	= htmlspecialchars($_POST["btnSbmt2"], ENT_QUOTES);	
	
	if(is_array_empty($_POST["cek"])){
		$con->close();
		$flash->add("error", "Data belum dipilih...", BASE_REFERER);
	} else{
		$oke = true;
		$con->beginTransaction();
		$con->clearError();
	
		if($btnSbmt1 == 1){
			$status	= 0;
			$result	= 1;
			$setuju = 1;
		} else if($btnSbmt2 == 1){
			$status	= 2;
			$result	= 2;
			$setuju = 0;
		}
	
		$ems1 = "select email_user from acl_user where id_role = 9 and id_wilayah = (select id_wilayah from pro_po_customer_om where id_ppco = '".$idr."')";
		$sql1 = "update pro_po_customer_om set is_executed = 1 where id_ppco = '".$idr."'";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();
	
		foreach($_POST['cek'] as $idx=>$val){
			$sql2 = "update pro_po_customer_om_detail set om_pic = '".$pic."', om_result = '".$result."', om_result_tgl = NOW() where id_plan = '".$idx."'";
			$con->setQuery($sql2);
			$oke  = $oke && !$con->hasError();
	
			$sql3 = "update pro_po_customer_plan set status_plan = '".$status."', is_approved = '".$setuju."' where id_plan = '".$idx."'";
			$con->setQuery($sql3);
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
				$mail->Subject = "Persetujuan PO Customer BM [".date('d/m/Y H:i:s')."]";
				$mail->msgHTML(paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])." telah mendisposisikan kembali po customer <p>".BASE_SERVER."</p>");
				$mail->send();
			}
			$con->commit();
			$con->close();
			header("location: ".$url);	
			exit();
		} else{
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
	}
?>

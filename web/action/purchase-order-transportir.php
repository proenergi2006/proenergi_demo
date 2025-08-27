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
	$act	= ($enk['act'] == "")?htmlspecialchars($_POST["act"], ENT_QUOTES):$enk['act'];
	$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);	
	$url 	= BASE_URL_CLIENT."/purchase-order-transportir.php";
	$summary= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["summary"], ENT_QUOTES));
	
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	$sql1 = "update pro_po set disposisi_po = 1, po_approved = 0, is_new = 1, catatan_transportir = '".$summary."' where id_po = '".$idr."'";
	$con->setQuery($sql1);
	$oke  = $oke && !$con->hasError();

	foreach($_POST["dt1"] as $idx=>$val){
		$dt1 = htmlspecialchars($_POST["dt1"][$idx], ENT_QUOTES);
		$dt2 = htmlspecialchars($_POST["dt2"][$idx], ENT_QUOTES);
		$dt3 = htmlspecialchars($_POST["dt3"][$idx], ENT_QUOTES);

		$sql2 = "update pro_po_detail set mobil_po = '".$dt2."', sopir_po = '".$dt3."' where id_pod = '".$idx."'";
		$con->setQuery($sql2);
		$oke  = $oke && !$con->hasError();
	}

	if ($oke){
		$ems1 = "select email_user from acl_user where id_role = 9 and id_wilayah = (select id_wilayah from pro_po where id_po = '".$idr."')";
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
		$mail->Subject = "Pengembalian Verifikasi PO dari Transportir  [".date('d/m/Y H:i:s')."]";
		$mail->msgHTML(paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])." telah melakukan verifikasi PO Transportir<p>".BASE_SERVER."</p>");
		$mail->send();

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
?>

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
	$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
	$url 	= BASE_URL_CLIENT."/customer-generate-link.php";
	$kepada = htmlspecialchars($_POST["to"], ENT_QUOTES);
	$cc 	= htmlspecialchars($_POST["cc"], ENT_QUOTES);
	$judul 	= htmlspecialchars($_POST["judul"], ENT_QUOTES);
	$pesan 	= htmLawed($_POST["pesan"], array('safe'=>1));
	$arr1 	= explode(",", $kepada);
	$arr2 	= explode(",", $cc);
	$reply	= $con->getOne("select email_user from acl_user where id_user = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."'");

	$mail = new PHPMailer;
	$mail->isSMTP();
	$mail->Host = 'smtp.gmail.com';
	$mail->Port = 587;
	$mail->SMTPSecure = 'tls';
	$mail->SMTPAuth = true;
	$mail->Username = USR_EMAIL_PROENERGI202389;
	$mail->Password = PWD_EMAIL_PROENERGI202389;
	
	$mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');
	$mail->addReplyTo($reply);
	foreach($arr1 as $data1){
		$mail->addAddress($data1);
	}
	if(!is_array_empty($arr2)){
		foreach($arr2 as $data2){
			$mail->addCC($data2);
		}
	}
	
	$mail->Subject = $judul;
	$mail->msgHTML($pesan);
	
	if (!$mail->send()){
		$con->close();
		$flash->add("error", "Maaf email tidak dapat dikirim", BASE_REFERER);
	} else{
		$con->close();
		$flash->add("success", "Email telah dikirim", $url);
	}
?>

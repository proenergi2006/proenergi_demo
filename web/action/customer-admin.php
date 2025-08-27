<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	require_once ($public_base_directory."/libraries/helper/passwordHash.php");
	load_helper("autoload", "mailgen", "htmlawed");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$act	= ($enk['act'] == "")?htmlspecialchars($_POST["act"], ENT_QUOTES):$enk['act'];
	
	if($act == "add"){
		$username	= htmlspecialchars($_POST["username"], ENT_QUOTES);	
		$telepon	= htmlspecialchars($_POST["telp"], ENT_QUOTES);
		$email		= htmlspecialchars($_POST["email"], ENT_QUOTES);
		$idr 		= htmlspecialchars($_POST["idr"], ENT_QUOTES);	
		$password	= random_password(8);
		$id_role 	= 19;
		$id_wilayah = 1;
		$id_group 	= 1;
		$active 	= 1;

		$salt		= "&%ApaKabar*(@!";
		$hasher 	= new PasswordHash(8, false);
		$hashPass 	= $hasher->HashPassword($password.$salt);
		$reply		= $con->getOne("select email_user from acl_user where id_user = (select id_marketing from pro_customer where id_customer = '".$idr."')");
		$cek_user 	= $con->getOne("select username from acl_user where username = '".$username."'");
		$customer	= $con->getOne("select nama_customer from pro_customer where id_customer = '".$idr."'");
		
		if($username == "" || $telepon == "" || $email == "" || $idr == ""){
			$con->close();
			$flash->add("error", "KOSONG", BASE_REFERER);
		} else if($email != "" && !filter_var($email, FILTER_VALIDATE_EMAIL)){
			$con->close();
			$flash->add("error", "Penulisan email tidak benar", BASE_REFERER);
		} else if($cek_user != ""){
			$valid = false;
			$pesan = "Username telah dipakai, silahkan gunakan username yang lain";
		} else{
			$oke = true;
			$con->beginTransaction();
			$con->clearError();
	
			$sql1 = "insert into acl_user(username, password, fullname, mobile_user, email_user, id_wilayah, id_group, id_role, id_customer, is_active, created_time, created_ip, created_by) values ('".$username."', '".$hashPass."', '".$username."', '".$telepon."', '".$email."', '".$id_wilayah."', '".$id_group."', '".$id_role."', '".$idr."', '".$active."', NOW(), '".$_SERVER['REMOTE_ADDR']."', '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."')";
			$res1 = $con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();
	
			$pesan = '<h3>Dear '.$customer.'</h3>
					 <p>You have a new account at Pro Energi application, detail  as below :</p><br />
					 <p style="margin-bottom:0px;">Username : '.$username.'</p>
					 <p style="margin-bottom:0px;">Password : '.$password.'</p>
					 <p>Url : '.BASE_SERVER.'</p><br />
					 <p style="margin-bottom:0px;">Thanks you</p>
					 <p style="margin-bottom:0px;">Best regards</p>
					 <p>Admin Pro Energi</p><br />
					 <p style="margin-bottom:0px;"><b><u>Note:</b></u></p>
					 <p style="margin-bottom:0px;">1. For your secure change your password after login</p>';
	
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
			$mail->addAddress($email);
			$mail->Subject = "Username dan Password Aplikasi Pro Energi";
			$mail->msgHTML($pesan);
		
			if ($oke && $mail->send()){
				$con->commit();
				$con->close();
				header("location: ".BASE_URL_CLIENT."/customer-admin-detail.php?".paramEncrypt("idr=".$idr));	
				exit();
			} else{
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
			}
		}
	}
	
	else if($act == "reset"){
		$idr = isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
		$idc = htmlspecialchars($enk["idc"], ENT_QUOTES);

		$password 	= random_password(8);
		$salt		= "&%ApaKabar*(@!";
		$hasher 	= new PasswordHash(8, false);
		$hashPass 	= $hasher->HashPassword($password.$salt);
		$reply		= $con->getOne("select email_user from acl_user where id_user = (select id_marketing from pro_customer where id_customer = '".$idr."')");
		$email_user = $con->getOne("select email_user from acl_user where id_user = '".$idc."'");

		$oke = true;
		$con->beginTransaction();
		$con->clearError();
		
		$sql = "update acl_user set password = '".$hashPass."' where id_user = '".$idc."'";
		$con->setQuery($sql);
		$oke  = $oke && !$con->hasError();

		$pesan = '<p>Password : '.$password.'</p>';
		$pesan .= '<p>'.BASE_SERVER.'</p>';

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
		$mail->addAddress($email_user);
		$mail->Subject = "Reset Password Aplikasi Pro Energi";
		$mail->msgHTML($pesan);
	
		if ($oke && $mail->send()){
			$con->commit();
			$con->close();
			header("location: ".BASE_URL_CLIENT."/customer-admin-detail.php?".paramEncrypt("idr=".$idr));	
			exit();
		} else{
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
	}
	
	else if($act == "aktif"){
		$idr = isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
		$idc = htmlspecialchars($enk["idc"], ENT_QUOTES);
		$sql = "update acl_user set is_active = 1 where id_user = '".$idc."'";
		$con->setQuery($sql);
		if(!$con->hasError()){
			$con->close();
			header("location: ".BASE_URL_CLIENT."/customer-admin-detail.php?".paramEncrypt("idr=".$idr));	
			exit();
		} else{
			$con->clearError();
			$con->close();
			$flash->add("error", "Maaf, sistem mengalami kendala teknis.", BASE_REFERER);
		}		
	}

	else if($act == "nonef"){
		$idr = isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
		$idc = htmlspecialchars($enk["idc"], ENT_QUOTES);
		$sql = "update acl_user set is_active = 0 where id_user = '".$idc."'";
		$con->setQuery($sql);
		if(!$con->hasError()){
			$con->close();
			header("location: ".BASE_URL_CLIENT."/customer-admin-detail.php?".paramEncrypt("idr=".$idr));	
			exit();
		} else{
			$con->clearError();
			$con->close();
			$flash->add("error", "Maaf, sistem mengalami kendala teknis.", BASE_REFERER);
		}		
	}

	else if($act == "hapus"){
		$idr = isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
		$idc = htmlspecialchars($enk["idc"], ENT_QUOTES);
		$sql = "delete from acl_user where id_user = '".$idc."'";
		$con->setQuery($sql);
		if(!$con->hasError()){
			$con->close();
			header("location: ".BASE_URL_CLIENT."/customer-admin-detail.php?".paramEncrypt("idr=".$idr));	
			exit();
		} else{
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_HAPUS", BASE_REFERER);
		}		
	}
?>

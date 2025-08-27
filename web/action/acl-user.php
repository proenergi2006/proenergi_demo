<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	require_once ($public_base_directory."/libraries/helper/passwordHash.php");
	load_helper("autoload", "mailgen");
	// load_helper("autoload");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$enk  	= decode($_SERVER['REQUEST_URI']);
	// $act	= isset($enk['act'])?($enk['act'] == "")?htmlspecialchars($_POST["act"], ENT_QUOTES):$enk['act']:'';
	$act 	= (isset($enk['act']) and $enk['act'] !== "") ? $enk["act"] : $_POST["act"];
	
	if($act == "reset"){
		$idr = isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
		$password 	= random_password(8);
		$salt		= "&%ApaKabar*(@!";
		$hasher 	= new PasswordHash(8, false);
		$hashPass 	= $hasher->HashPassword($password.$salt);

		$rsm = $con->getRecord("select username, email_user from acl_user where id_user = '".$idr."'");
		$oke = true;
		$con->beginTransaction();
		$con->clearError();
		
		$sql = "update acl_user set password = '".$hashPass."' where id_user = '".$idr."'";
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
		$mail->addAddress($rsm['email_user']);
		$mail->Subject = "Reset Password Aplikasi Pro Energi";
		$mail->msgHTML($pesan);
	
		if ($oke && $mail->send()){
			$con->commit();
			$con->close();
			$flash->add("success", "Password ".$rsm['username']." telah berhasil direset", BASE_URL_CLIENT."/acl-user.php");
		} else{
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
	} else if($act == "update"){
		$idr 		= htmlspecialchars($_POST["idr"], ENT_QUOTES);	
		$active 	= htmlspecialchars($_POST["active"], ENT_QUOTES);
		$omnya 		= isset($_POST["id_om"]) ? htmlspecialchars($_POST["id_om"], ENT_QUOTES) : null;
		$id_role 	= htmlspecialchars($_POST["id_role"], ENT_QUOTES);
		
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		$ext1 = ($id_role == 17)?"id_om = '".$omnya."', ":"";
		$sql1 = "update acl_user set ".$ext1." is_active = '".$active."' where id_user = '".$idr."'";
		$res1 = $con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();
		
		if ($oke){
			$con->commit();
			$con->close();
			$flash->add("success", "User has been updated", BASE_REFERER);
		} else{
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
	} else if($act == "add"){
		$username	= htmlspecialchars($_POST["username"], ENT_QUOTES);	
		$fullname 	= htmlspecialchars($_POST["fullname"], ENT_QUOTES);	
		$telepon	= htmlspecialchars($_POST["telepon"], ENT_QUOTES);
		$email		= htmlspecialchars($_POST["email"], ENT_QUOTES);
		$password	= htmlspecialchars($_POST["pass"], ENT_QUOTES);
		$cpassword 	= htmlspecialchars($_POST["cpass"], ENT_QUOTES);	
		$id_role 	= htmlspecialchars($_POST["id_role"], ENT_QUOTES);
		$id_wilayah = isset($_POST["id_wilayah"])?htmlspecialchars($_POST["id_wilayah"], ENT_QUOTES):null;
		$active 	= htmlspecialchars($_POST["active"], ENT_QUOTES);
		$idr 		= htmlspecialchars($_POST["idr"], ENT_QUOTES);	
		$transportir= htmlspecialchars($_POST["id_transportir"], ENT_QUOTES);
		$terminal 	= htmlspecialchars($_POST["id_terminal"], ENT_QUOTES);
		$omnya 		= htmlspecialchars($_POST["id_om"], ENT_QUOTES);
		
		$salt		= "&%ApaKabar*(@!";
		$hasher 	= new PasswordHash(8, false);
		$hashPass 	= $hasher->HashPassword($password.$salt);
		
		if($username == "" || $fullname == "" || $password == "" || $id_role == ""){
			$con->close();
			$flash->add("error", "KOSONG", BASE_REFERER);
		} else if($password != "" && $password != $cpassword){
			$con->close();
			$flash->add("error", "Konfirmasi password tidak sesuai", BASE_REFERER);
		} else if(strlen($password.$salt) > 72){ 
			$con->close();
			$flash->add("error", "Password anda terlalu panjang", BASE_REFERER);
		} else if(strlen($hashPass) < 20){ 
			$con->close();
			$flash->add("error", "Maaf sistem mengalami kendala, silahkan coba lagi", BASE_REFERER);
		} else if(strlen($hashPass) < 20){ 
			$con->close();
			$flash->add("error", "Maaf sistem mengalami kendala, silahkan coba lagi", BASE_REFERER);
		} else if($id_role == 12 && $transportir == ""){ 
			$con->close();
			$flash->add("error", "Transportir belum diisi", BASE_REFERER);
		} else if($id_role == 13 && $terminal == ""){ 
			$con->close();
			$flash->add("error", "Terminal belum diisi", BASE_REFERER);
		} else{
			$oke = true;
			$con->beginTransaction();
			$con->clearError();
			
			if($id_role == '6'){
				$id_group 	= $id_wilayah;
				$id_wilayah = $con->getOne("select id_master from pro_master_cabang where id_group_cabang = '".$id_group."'");
			} else if($id_role == '18') {
				$inpchkcs = htmlspecialchars($_POST["inp-chk-cs"], ENT_QUOTES);
				if ($inpchkcs=='1') {
					$id_group = $con->getOne("select id_group_cabang from pro_master_cabang where id_master = '".$id_wilayah."'");
				} else if ($inpchkcs=='2') {
					$id_group = $id_wilayah;
					$id_wilayah = 0;
				} else if ($inpchkcs=='3') {
					$id_group = 0;
					$id_wilayah = 0;
				}
			} else{
				$id_group = $con->getOne("select id_group_cabang from pro_master_cabang where id_master = '".$id_wilayah."'");
			}

			$transportir = $transportir==''?0:$transportir;
			$terminal = $terminal==''?0:$terminal;
			$omnya = $omnya==''?0:$omnya;

			$sql1 = '
				insert into 
					acl_user
				(
					username,
					password,
					fullname,
					mobile_user,
					email_user,
					id_wilayah,
					id_group,
					id_role,
					id_transportir,
					id_terminal,
					id_om,
					is_active,
					created_time,
					created_ip,
					created_by
				) values (
					"'.$username.'",
					"'.$hashPass.'",
					"'.$fullname.'",
					"'.$telepon.'",
					"'.$email.'",
					"'.$id_wilayah.'",
					"'.$id_group.'",
					"'.$id_role.'",
					"'.$transportir.'",
					"'.$terminal.'",
					"'.$omnya.'",
					"'.$active.'",
					"'.date('Y-m-d H:i:s').'",
					"'.$_SERVER['REMOTE_ADDR'].'",
					"'.paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname']).'"
				)'
			;

			$res1 = $con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();
			if ($oke){
				$con->commit();
				$con->close();
				$flash->add("success", "User has been created", BASE_URL_CLIENT."/acl-user.php");
			} else{
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
			}
		}
	}
?>

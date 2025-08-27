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
	$url 	= BASE_URL_CLIENT."/purchase-request-ar.php";
	$abis	= true;

	$note 		= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["summary"], ENT_QUOTES));
	$pic		= paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname']);
	$wilayah	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
	$group		= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']);

	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 15){
		$ems1 = "";
		$sql1 = "update pro_pr_ar set ar_approved = 1, mgr_summary = '".$note."', mgr_result = 1, mgr_pic = '".$pic."', mgr_tanggal = NOW() where id_par = '".$idr."'";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();		

		$cek1 = "select id_wilayah, id_pr from pro_pr_ar where id_par = '".$idr."'";
		$row1 = $con->getRecord($cek1);

		$cek2 = "
			select count(a.id_par) as jumlah_pr, count(b.id_par) as jumlah_approve, a.id_pr 
			from pro_pr_ar a 
			left join pro_pr_ar b on a.id_par = b.id_par and b.ar_approved = 1
			where a.id_pr = '".$row1['id_pr']."' group by a.id_pr";
		$row2 = $con->getRecord($cek2);

		if($row2['jumlah_pr'] == $row2['jumlah_approve']){
			$ems1 = "select email_user from acl_user where id_role = 7 and id_wilayah = '".$row1['id_wilayah']."'";
			$sbjk = "Persetujuan PR [".date('d/m/Y H:i:s')."]";
			$pesn = $pic." telah melakukan verifikasi AR, lanjutkan persetujuan PR";
			
			$sql2 = "update pro_pr set ar_approved = 1, disposisi_pr = 2 where id_pr = '".$row1['id_pr']."'";
			$con->setQuery($sql2);
			$oke  = $oke && !$con->hasError();
		}			
	}
	
	else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 6){
		$toMgr = false;
		foreach($_POST['cek'] as $idx=>$val){
			$dt3 	= htmlspecialchars($_POST['dt3'][$idx], ENT_QUOTES);
			$dt4 	= htmlspecialchars($_POST['dt4'][$idx], ENT_QUOTES);
			$dt5 	= htmlspecialchars($_POST['dt5'][$idx], ENT_QUOTES);
			$dt6 	= htmlspecialchars($_POST['dt6'][$idx], ENT_QUOTES);
			$tot 	= $dt3 + $dt4 + $dt5;
			$toMgr 	= $toMgr || ($dt4 > 250000000 || $dt5 > 0 || $tot > $dt6);
		}
			
		if($toMgr){
			$ems1 = "select email_user from acl_user where id_role = 15";
			$sbjk = "Persetujuan AR [".date('d/m/Y H:i:s')."]";
			$pesn = $pic." meminta persetujuan untuk PR";
			
			$sql1 = "update pro_pr_ar set disposisi_ar = 3, om_summary = '".$note."', om_result = 1, om_pic = '".$pic."', om_tanggal = NOW() where id_par = '".$idr."'";
			$con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();
		} else{
			$ems1 = "";
			$sql1 = "update pro_pr_ar set ar_approved = 1, om_summary = '".$note."', om_result = 1, om_pic = '".$pic."', om_tanggal = NOW() where id_par = '".$idr."'";
			$con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();		
	
			$cek1 = "select id_wilayah, id_pr from pro_pr_ar where id_par = '".$idr."'";
			$row1 = $con->getRecord($cek1);

			$cek2 = "
				select count(a.id_par) as jumlah_pr, count(b.id_par) as jumlah_approve, a.id_pr 
				from pro_pr_ar a 
				left join pro_pr_ar b on a.id_par = b.id_par and b.ar_approved = 1
				where a.id_pr = '".$row1['id_pr']."' group by a.id_pr";
			$row2 = $con->getRecord($cek2);
			
			if($row2['jumlah_pr'] == $row2['jumlah_approve']){
				$ems1 = "select email_user from acl_user where id_role = 7 and id_wilayah = '".$row1['id_wilayah']."'";
				$sbjk = "Persetujuan PR [".date('d/m/Y H:i:s')."]";
				$pesn = $pic." telah melakukan verifikasi AR, lanjutkan persetujuan PR";
				
				$sql2 = "update pro_pr set ar_approved = 1, disposisi_pr = 2 where id_pr = '".$row1['id_pr']."'";
				$con->setQuery($sql2);
				$oke  = $oke && !$con->hasError();
			}			
		}
	}
	
	else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 7){
		$ems1 = "select email_user from acl_user where id_role = 6 and id_group = '".$group."'";
		$sbjk = "Persetujuan AR [".date('d/m/Y H:i:s')."]";
		$pesn = $pic." meminta persetujuan untuk AR";
		
		$sql1 = "update pro_pr_ar set disposisi_ar = 2, sm_summary = '".$note."', sm_result = 1, sm_pic = '".$pic."', sm_tanggal = NOW() where id_par = '".$idr."'";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();
	}
	
	else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 10){
		$ems1 = "select email_user from acl_user where id_role = 7 and id_wilayah = '".$wilayah."'";
		$sbjk = "Persetujuan AR [".date('d/m/Y H:i:s')."]";
		$pesn = $pic." meminta persetujuan untuk AR";
		
		$sql1 = "update pro_pr_ar set disposisi_ar = 1, finance_summary = '".$note."', finance_result = 1, finance_pic = '".$pic."', finance_tanggal = NOW() 
				 where id_par = '".$idr."'";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();
		foreach($_POST["spy"] as $idx=>$val){
			$spy = htmlspecialchars($val, ENT_QUOTES);
			$sql2 = "update pro_pr_detail set schedule_payment = '".$spy."' where id_prd = '".$idx."'";
			$con->setQuery($sql2);
			$oke  = $oke && !$con->hasError();			
		}
	}
	
	$pesn .= "<p>".BASE_SERVER."</p>";

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
			$mail->Subject = $sbjk;
			$mail->msgHTML($pesn);
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
?>

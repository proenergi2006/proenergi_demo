<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload", "htmlawed", "mailgen");

	$conDr1 = new Connection();
	$sql01 	= "
		select a.*, time_to_sec(a.date_difference) as detiknya 
		from (
			select id_pr, purchasing_tanggal, 
			TIMEDIFF(NOW(), purchasing_tanggal) AS date_difference 
			from pro_pr 
			where purchasing_result > 0 and (ceo_result = 0 and coo_result = 0) 
			and year(tanggal_pr) >= 2023 
		) a 
		where 1=1 and time_to_sec(date_difference) > 900
	";
	$res01 = $conDr1->getResult($sql01);

	$ems1 = ""; $oke = true;
	if(count($res01) > 0){
		$ems1 = "select email_user from acl_user where id_role = 21";
		$sbjk = "Persetujuan DR [".date('d/m/Y H:i:s')."]";
		$pesn = "Sistem SYOP meminta persetujuan untuk DR, dikarenakan dalam jangka waktu 15 menit, COO belum menyetujui DR ini";
		
		foreach($res01 as $data){
			$idpr 	= $data['id_pr'];
			$sql02 	= "
				update pro_pr set coo_summary = 'Approved By System Automatically', coo_result = 1, coo_pic = 'Syop System', coo_tanggal = NOW(), 
				is_ceo = 1, disposisi_pr = 5 
				where id_pr = '".$idpr."'
			";
			$conDr1->setQuery($sql02);
			$oke  = $oke && !$conDr1->hasError();
		}
	}

	if($ems1){
		$rms1 = $conDr1->getResult($ems1);
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

	$conDr1->close();
?>

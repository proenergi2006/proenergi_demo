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
	$url 	= BASE_URL_CLIENT."/master-approval-harga.php";

	if(is_array_empty($_POST["cek"])){
		$con->close();
		$flash->add("error", "Data harga belum pilih", BASE_REFERER);
	} else{
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		$i = 0;
		foreach($_POST["cek"] as $idx1=>$val1){
			$tmpx = paramDecrypt($idx1);
			list($periode_awal, $periode_akhir, $area, $produk) = explode("#|#", $tmpx);
			foreach($_POST["hrgN"][$idx1] as $idx2=>$val2){
				$hrgN = htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["hrgN"][$idx1][$idx2]), ENT_QUOTES);
				$hrgS = htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["hrgS"][$idx1][$idx2]), ENT_QUOTES);
				$hrgO = htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["hrgO"][$idx1][$idx2]), ENT_QUOTES);
				$sql = "update pro_master_harga_minyak set harga_normal = '".$hrgN."', harga_sm = '".$hrgS."', harga_om = '".$hrgO."', is_approved = 1, 
						tanggal_persetujuan = NOW(), lastupdate_time = NOW(), lastupdate_ip = '".$_SERVER['REMOTE_ADDR']."', 
						lastupdate_by = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."' where periode_awal = '".$periode_awal."' and periode_akhir = '".$periode_akhir."' 
						and id_area = '".$area."' and produk = '".$produk."' and pajak = '".$idx2."'";
				$con->setQuery($sql);
				$oke = $oke && !$con->hasError();
			}
			// Send Email
			$mail = new PHPMailer;
			$mail->isSMTP();
			$mail->Host = 'smtp.gmail.com';
			$mail->Port = 587;
			$mail->SMTPSecure = 'tls';
			$mail->SMTPAuth = true;
			$mail->Username = USR_EMAIL_PROENERGI202389;
			$mail->Password = PWD_EMAIL_PROENERGI202389;
			
			$mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');
			$kepada = htmlspecialchars($_POST["to"][$i], ENT_QUOTES);
			$arr1 	= explode(",", $kepada);
			foreach($arr1 as $data1){
				$mail->addAddress($data1);
			}
			$cc 	= htmlspecialchars($_POST["cc"][$i], ENT_QUOTES);
			$arr2 	= explode(",", $cc);
			if(!is_array_empty($arr2)){
				foreach($arr2 as $data2){
					$mail->addCC($data2);
				}
			}
			
			$judul 	= htmlspecialchars($_POST["judul"][$i], ENT_QUOTES);
			$mail->Subject = $judul;
			$pesan 	= htmLawed($_POST["pesan"][$i], array('safe'=>1));
			$mail->msgHTML($pesan);
			$mail->send();
			// Send Mail
			$i ++;
		}
		if ($oke){
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

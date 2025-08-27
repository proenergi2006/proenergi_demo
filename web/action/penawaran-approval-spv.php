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
	$idr	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
	$idk	= htmlspecialchars($_POST["idk"], ENT_QUOTES);
	$url 	= BASE_URL_CLIENT."/penawaran-approval-spv.php";

	$catatan_spv	= htmlspecialchars($_POST["spv_mkt_summary"], ENT_QUOTES);
	$approval		= htmlspecialchars($_POST["approval"], ENT_QUOTES);	
	$extend			= htmlspecialchars($_POST["extend"], ENT_QUOTES);
	$is_mkt 		= htmlspecialchars($_POST["is_mkt"], ENT_QUOTES);
	$tmp_cabang		= htmlspecialchars($_POST["tmp_cabang"], ENT_QUOTES);

	$approval_pic	= paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname']);
	$user_group 	= paramDecrypt($_SESSION["sinori".SESSIONID]["id_group"]);
	$user_id 		= paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"]);
	$seswil 		= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);

	$ls_harga_dasar				= htmlspecialchars($_POST["harga_dasar"], ENT_QUOTES);
	$ls_oa_kirim				= htmlspecialchars($_POST["oa_kirim"], ENT_QUOTES);
	$ls_ppn						= htmlspecialchars($_POST["ppn"], ENT_QUOTES);
	$ls_pbbkb					= htmlspecialchars($_POST["pbbkb"], ENT_QUOTES);
	$ls_volume					= htmlspecialchars($_POST["volume"], ENT_QUOTES);
	$ls_keterangan_pengajuan	= htmlspecialchars($_POST["keterangan_pengajuan"], ENT_QUOTES);

	$cek_persetujuan = "0";

	if($approval == ""){
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else{
		$oke = true;
		$con->beginTransaction();
		$con->clearError();
		$email = true;

		$sqlcek01 = "select id_cabang, nomor_surat from pro_penawaran where id_customer = '".$idr."' and id_penawaran = '".$idk."'";
		$rescek01 = $con->getRecord($sqlcek01);
		if($rescek01['id_cabang'] != $seswil){
			$ems1 = "select email_user from acl_user where id_role = 7 and (id_wilayah = '".$rescek01['id_cabang']."' or id_wilayah = '".$seswil."')";
			$flag = 3;
			$sm_mkt_result = 1;
			$sm_mkt_tanggal = "'".date("Y/m/d H:i:s")."'";
		} else{
			$ems1 = "select email_user from acl_user where id_role = 7 and id_wilayah = '".$seswil."'";
			$flag = 3;
			$sm_mkt_result = 0;
			$sm_mkt_tanggal = 'NULL';
		}

		if($extend != "" && $extend == '1'){
			$sqlcek01 = "select id_cabang, nomor_surat from pro_penawaran where id_customer = '".$idr."' and id_penawaran = '".$idk."'";
			$rescek01 = $con->getRecord($sqlcek01);
			if($rescek01['id_cabang'] != $seswil){
				$ems1 = "select email_user from acl_user where id_role = 7 and (id_wilayah = '".$rescek01['id_cabang']."' or id_wilayah = '".$seswil."')";
				$flag = 3;
				$sm_mkt_result = 1;
				$sm_mkt_tanggal = "'".date("Y/m/d H:i:s")."'";
			} else{
				$ems1 = "select email_user from acl_user where id_role = 7 and id_wilayah = '".$seswil."'";
				$flag = 3;
				$sm_mkt_result = 0;
				$sm_mkt_tanggal = 'NULL';
			}
	
			$sql1 = "
				update pro_penawaran set 
				spv_mkt_summary = '".$catatan_spv."', spv_mkt_result = '".$approval."', spv_mkt_tanggal = NOW(), spv_mkt_pic = '".$approval_pic."', 
				sm_mkt_summary = '', sm_mkt_result = '".$sm_mkt_result."', sm_mkt_tanggal = ".$sm_mkt_tanggal.", sm_mkt_pic = '', 
				flag_disposisi = '".$flag."' 
				where id_customer = '".$idr."' and id_penawaran = '".$idk."'
			";
			$con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();

		} else if($extend != "" && $extend == '2'){
			$sql1 = "
				update pro_penawaran set 
				spv_mkt_summary = '".$catatan_spv."', spv_mkt_result = '".$approval."', spv_mkt_tanggal = NOW(), spv_mkt_pic = '".$approval_pic."', 
				flag_approval = '".$approval."', tgl_approval = NOW(), pic_approval = '".$user_id."' 
				where id_customer = '".$idr."' and id_penawaran = '".$idk."'
			";
			$con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();

			$ems1 = "
			select c.email_user 
			from pro_penawaran a 
			join pro_customer b on a.id_customer = b.id_customer 
			join acl_user c on b.id_marketing = c.id_user 
			where a.id_penawaran = '".$idk."'";
			
			$cek_persetujuan = $approval;
		}

		$sql2 = "
			insert into pro_approval_hist (kd_approval, result, summary, id_user, tgl_approval, id_customer, id_penawaran, id_role, harga_dasar, oa_kirim, pbbkb, ppn, keterangan_pengajuan, volume)
			values ('P001', '".$approval."', '".$catatan_spv."', '".$user_id."', NOW(), '".$idr."', '".$idk."', '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role'])."', 
			".$ls_harga_dasar.", ".$ls_oa_kirim.", ".$ls_pbbkb.", ".$ls_ppn.", '".$ls_keterangan_pengajuan."', ".$ls_volume.");";
		$con->setQuery($sql2);
		$oke  = $oke && !$con->hasError();

		if ($oke){
			if($email){
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
				if($cek_persetujuan == '1'){ // Ya
					$mail->Subject = "Persetujuan Penawaran [".date('d/m/Y H:i:s')."]";
					$mail->msgHTML($approval_pic." menyetujui penawaran <p>".BASE_SERVER."</p>");
				}else if($cek_persetujuan == '2'){ // Tidak
					$mail->Subject = "Penolakan Penawaran [".date('d/m/Y H:i:s')."]";
					$mail->msgHTML($approval_pic." menolak penawaran <p>".BASE_SERVER."</p>");
				}else{ // Jika Disposisi
					$mail->Subject = "Persetujuan Penawaran [".date('d/m/Y H:i:s')."]";
					$mail->msgHTML($approval_pic." meminta persetujuan penawaran <p>".BASE_SERVER."</p>");
				}
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

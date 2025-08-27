<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "mailgen");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;
$enk  	= decode($_SERVER['REQUEST_URI']);
$idr 	= isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
$idk 	= htmlspecialchars($enk["idk"], ENT_QUOTES);
$url 	= BASE_URL_CLIENT . "/penawaran-detail.php?" . paramEncrypt("idr=" . $idr . "&idk=" . $idk);

$sesrol 	= paramDecrypt($_SESSION["sinori" . SESSIONID]["id_role"]);
$sesuser 	= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);
$seswil 	= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);


$sql1 = "select * from pro_penawaran where id_customer = '" . $idr . "' and id_penawaran = '" . $idk . "'";
$row4 = $con->getRecord($sql1);

$refund = $row4['refund_tawar'];
$volume = $row4['volume_tawar'];
$other_cost = $row4['other_cost'];
$oa_kirim = $row4['oa_kirim'];
$tier = $row4['tier'];

$rincian = json_decode($row4['detail_rincian'], true);

$ongkosAngkut = 0;

foreach ($rincian as $item) {
	if ($item['rincian'] === "Ongkos Angkut") {
		$ongkosAngkut = $item['biaya'];
	}
}


$totalrefund = $refund * $volume;
$totalothercost = $other_cost * $volume;

$selisih =  $oa_kirim - $ongkosAngkut;
$totalselisih = $selisih * $volume;

if ($refund > 70 || $totalrefund > 1000000 || $other_cost > 50 || $totalothercost > 1000000 || $selisih > 50 || $totalselisih > 1000000) {
	$flag = 4;
	$id_role = 6;
	$flag_approval = 0;
} elseif ((($refund <= 70 || $totalrefund <= 1000000) || ($other_cost <= 50 || $totalothercost <= 1000000)) && $tier == 'I') {
	$flag = 3;
	$id_role = 7;
	$flag_approval = 0;
} elseif ((($refund <= 70 || $totalrefund <= 1000000) || ($other_cost <= 50 || $totalothercost <= 1000000)) && $tier == 'II') {
	$flag = 4;
	$id_role = 6;
	$flag_approval = 0;
} else {
	$flag = 4;
	$id_role = 6;
	$flag_approval  =  0;
}



if (($sesrol == 18 || $sesrol == 11) && ($refund > 70 || $totalrefund > 1000000 || $other_cost > 50 || $totalothercost > 1000000 || $selisih > 50 || $totalselisih > 1000000)) {
	$oke = true;
	$con->beginTransaction();
	$con->clearError();
	$cek2 = "select nomor_surat from pro_penawaran where id_customer = '" . $idr . "' and id_penawaran = '" . $idk . "'";
	$row2 = $con->getOne($cek2);
	$ems1 = "select email_user from acl_user where id_role = '" . $id_role . "'";

	$sql = "update pro_penawaran set sm_mkt_summary = '', sm_mkt_pic = '', sm_mkt_result = 0, sm_wil_summary = '', sm_wil_pic = '', sm_wil_result = 0, om_summary = '', 
				om_pic = '', om_result = 0, coo_summary = '', coo_pic = '', coo_result = 0, ceo_summary = '', ceo_pic = '', ceo_result = 0, flag_disposisi = '" . $flag . "', flag_approval = 0 
				where id_customer = '" . $idr . "' and id_penawaran = '" . $idk . "'";
	$con->setQuery($sql);
	$oke  = $oke && !$con->hasError();

	if ($oke) {
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
		foreach ($rms1 as $datms) {
			$mail->addAddress($datms['email_user']);
		}
		$mail->Subject = "Persetujuan Penawaran [" . date('d/m/Y H:i:s') . "]";
		$mail->msgHTML(paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " meminta persetujuan penawaran <p>" . BASE_SERVER . "</p>");
		$mail->send();

		$con->commit();
		$con->close();
		$flash->add("success", "Persetujuan untuk penawaran " . $row2 . " sudah diajukan", $url);
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}
} else if ($sesrol == 11 || $sesrol == 18) {
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	$cek1 = "select id_cabang, nomor_surat from pro_penawaran where id_customer = '" . $idr . "' and id_penawaran = '" . $idk . "'";
	$row1 = $con->getRecord($cek1);


	$sqlcek02 = "select id_marketing from pro_customer where id_customer = '" . $idr . "'";
	$rescek02 = $con->getRecord($sqlcek02);

	$sqlcek01 = "select * from pro_mapping_spv where id_mkt = '" . $rescek02['id_marketing'] . "'";
	$rescek01 = $con->getResult($sqlcek01);

	if (count($rescek01) > 0) {
		$id_spv = "";
		foreach ($rescek01 as $idx1 => $val1) {
			$id_spv .= ", " . $val1['id_spv'];
		}
		$id_spv = substr($id_spv, 2);

		$ems1 = "select email_user from acl_user where id_role = '" . $id_role . "' and id_wilayah = '" . $seswil . "'";
		//$flag = 1;
		$sm_mkt_result = 0;
		$sm_mkt_tanggal = 'NULL';
	} else {
		if ($row1['id_cabang'] != $seswil) {
			$ems1 = "select email_user from acl_user where id_role = '" . $id_role . "' and (id_wilayah = '" . $row1['id_cabang'] . "' or id_wilayah = '" . $seswil . "')";
			//$flag = 3;
			$sm_mkt_result = 1;
			$sm_mkt_tanggal = "'" . date("Y/m/d H:i:s") . "'";
		} else {
			$ems1 = "select email_user from acl_user where id_role = '" . $id_role . "' and id_wilayah = '" . $seswil . "'";
			//$flag = 3;
			$sm_mkt_result = 0;
			$sm_mkt_tanggal = 'NULL';
		}
	}
	$sql = "
			update pro_penawaran set 
			spv_mkt_summary = '', spv_mkt_pic = '', spv_mkt_result = 0, spv_mkt_tanggal = NULL, 
			sm_mkt_summary = '', sm_mkt_pic = '', sm_mkt_result = '" . $sm_mkt_result . "', sm_mkt_tanggal = " . $sm_mkt_tanggal . ", 
			sm_wil_summary = '', sm_wil_pic = '', sm_wil_result = 0, sm_wil_tanggal = NULL, 
			om_summary = '', om_pic = '', om_result = 0, om_tanggal = NULL, 
			coo_summary = '', coo_pic = '', coo_result = 0, coo_tanggal = NULL, 
			ceo_summary = '', ceo_pic = '', ceo_result = 0, ceo_tanggal = NULL, 
			flag_disposisi = '" . $flag . "', flag_approval = 0, tgl_approval = NULL 
			where id_customer = '" . $idr . "' and id_penawaran = '" . $idk . "'
		";
	$con->setQuery($sql);
	$oke  = $oke && !$con->hasError();

	if ($oke) {
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
		foreach ($rms1 as $datms) {
			$mail->addAddress($datms['email_user']);
		}
		$mail->Subject = "Persetujuan Penawaran [" . date('d/m/Y H:i:s') . "]";
		$mail->msgHTML(paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " meminta persetujuan penawaran <p>" . BASE_SERVER . "</p>");
		$mail->send();

		$con->commit();
		$con->close();
		$flash->add("success", "Persetujuan untuk penawaran " . $row1['nomor_surat'] . " sudah diajukan", $url);
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}
} else if ($sesrol == 17) {
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	// $cek1 = "select id_om from acl_user where id_user = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_user"]) . "'";
	// $row1 = $con->getOne($cek1);
	$cek2 = "select nomor_surat from pro_penawaran where id_customer = '" . $idr . "' and id_penawaran = '" . $idk . "'";
	$row2 = $con->getOne($cek2);
	$ems1 = "select email_user from acl_user where id_role = '" . $id_role . "'";

	$sql = "update pro_penawaran set sm_mkt_summary = '', sm_mkt_pic = '', sm_mkt_result = 0, sm_wil_summary = '', sm_wil_pic = '', sm_wil_result = 0, om_summary = '', 
				om_pic = '', om_result = 0, coo_summary = '', coo_pic = '', coo_result = 0, ceo_summary = '', ceo_pic = '', ceo_result = 0, flag_disposisi = '" . $flag . "', flag_approval = 0 
				where id_customer = '" . $idr . "' and id_penawaran = '" . $idk . "'";
	$con->setQuery($sql);
	$oke  = $oke && !$con->hasError();

	if ($oke) {
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
		foreach ($rms1 as $datms) {
			$mail->addAddress($datms['email_user']);
		}
		$mail->Subject = "Persetujuan Penawaran [" . date('d/m/Y H:i:s') . "]";
		$mail->msgHTML(paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " meminta persetujuan penawaran <p>" . BASE_SERVER . "</p>");
		$mail->send();

		$con->commit();
		$con->close();
		$flash->add("success", "Persetujuan untuk penawaran " . $row2 . " sudah diajukan", $url);
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}
}

<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "htmlawed", "mailgen");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;
$enk  	= decode($_SERVER['REQUEST_URI']);
$idr	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
$idk	= htmlspecialchars($_POST["idk"], ENT_QUOTES);
$url 	= BASE_URL_CLIENT . "/penawaran-approval.php";

$catatan_sm_mkt	= htmlspecialchars($_POST["sm_mkt_summary"], ENT_QUOTES);
$catatan_sm_cab	= htmlspecialchars($_POST["sm_wil_summary"], ENT_QUOTES);
$catatan_om		= htmlspecialchars($_POST["om_summary"], ENT_QUOTES);
$catatan_coo	= htmlspecialchars($_POST["coo_summary"], ENT_QUOTES);
$catatan_ceo	= htmlspecialchars($_POST["ceo_summary"], ENT_QUOTES);

$approval		= htmlspecialchars($_POST["approval"], ENT_QUOTES);
$extend			= htmlspecialchars($_POST["extend"], ENT_QUOTES);
$is_mkt 		= htmlspecialchars($_POST["is_mkt"], ENT_QUOTES);
$tmp_cabang		= htmlspecialchars($_POST["tmp_cabang"], ENT_QUOTES);

$approval_pic	= paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']);
$user_group 	= paramDecrypt($_SESSION["sinori" . SESSIONID]["id_group"]);
$user_id 		= paramDecrypt($_SESSION["sinori" . SESSIONID]["id_user"]);

$ls_harga_dasar				= htmlspecialchars($_POST["harga_dasar"], ENT_QUOTES);
$ls_oa_kirim				= htmlspecialchars($_POST["oa_kirim"], ENT_QUOTES);
$ls_ppn						= htmlspecialchars($_POST["ppn"], ENT_QUOTES);
$ls_pbbkb					= htmlspecialchars($_POST["pbbkb"], ENT_QUOTES);
$ls_volume					= htmlspecialchars($_POST["volume"], ENT_QUOTES);
$ls_keterangan_pengajuan	= htmlspecialchars($_POST["keterangan_pengajuan"], ENT_QUOTES);

$ls_harga_dasar				= ($ls_harga_dasar ? $ls_harga_dasar : 0);
$ls_oa_kirim				= ($ls_oa_kirim ? $ls_oa_kirim : 0);
$ls_ppn						= ($ls_ppn ? $ls_ppn : 0);
$ls_pbbkb					= ($ls_pbbkb ? $ls_pbbkb : 0);
$ls_volume					= ($ls_volume ? $ls_volume : 0);

$cek_persetujuan = "0";

if ($approval == "") {
	$con->close();
	$flash->add("error", "KOSONG", BASE_REFERER);
} else {
	$oke = true;
	$con->beginTransaction();
	$con->clearError();
	$email = true;

	if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 7 && $is_mkt) {
		$ems1 = "select email_user from acl_user where id_role = 7 and id_wilayah = '" . $tmp_cabang . "'";

		$sql1 = "
				update pro_penawaran set 
				sm_mkt_summary = '" . $catatan_sm_mkt . "', sm_mkt_result = '" . $approval . "', sm_mkt_tanggal = NOW(), sm_mkt_pic = '" . $approval_pic . "', 
				flag_disposisi = 3 
				where id_customer = '" . $idr . "' and id_penawaran = '" . $idk . "'
			";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();

		$sql2 = "
				insert into pro_approval_hist (kd_approval, result, summary, id_user, tgl_approval, id_customer, id_penawaran, id_role, harga_dasar, oa_kirim, pbbkb, ppn, keterangan_pengajuan, volume)
				values ('P001', '" . $approval . "', '" . $catatan_sm_mkt . "', '" . $user_id . "', NOW(), '" . $idr . "', '" . $idk . "', '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) . "', 
				" . $ls_harga_dasar . ", " . $ls_oa_kirim . ", " . $ls_pbbkb . ", " . $ls_ppn . ", '" . $ls_keterangan_pengajuan . "', " . $ls_volume . ");";
		$con->setQuery($sql2);
		$oke  = $oke && !$con->hasError();
	} else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 7 && !$is_mkt) {
		$sql1 = "update pro_penawaran set sm_wil_summary = '" . $catatan_sm_cab . "', sm_wil_result = '" . $approval . "', sm_wil_tanggal = NOW(), sm_wil_pic = '" . $approval_pic . "'";
		if ($extend != "" && $extend == '1') {
			$sql1 .= ", flag_disposisi = 4";
			$ems1 = "select email_user from acl_user where id_role = 6 and id_group = '" . $user_group . "'";
		} else if ($extend != "" && $extend == '2') {
			$sql1 .= ", flag_approval = '" . $approval . "', tgl_approval = NOW(), pic_approval = '" . $user_id . "'";
			$ems1 = "
				select c.email_user 
				from pro_penawaran a 
				join pro_customer b on a.id_customer = b.id_customer 
				join acl_user c on b.id_marketing = c.id_user 
				where a.id_penawaran = '" . $idk . "'";

			$cek_persetujuan = $approval;
		}
		$sql1 .= " where id_customer = '" . $idr . "' and id_penawaran = '" . $idk . "'";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();

		$sql2 = "
				insert into pro_approval_hist (kd_approval, result, summary, id_user, tgl_approval, id_customer, id_penawaran, id_role, harga_dasar, oa_kirim, pbbkb, ppn, keterangan_pengajuan, volume)
				values ('P001', '" . $approval . "', '" . $catatan_sm_cab . "', '" . $user_id . "', NOW(), '" . $idr . "', '" . $idk . "', '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) . "', 
				" . $ls_harga_dasar . ", " . $ls_oa_kirim . ", " . $ls_pbbkb . ", " . $ls_ppn . ", '" . $ls_keterangan_pengajuan . "', " . $ls_volume . ");";
		$con->setQuery($sql2);
		$oke  = $oke && !$con->hasError();
	} else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 6) {
		$sql1 = "update pro_penawaran set om_summary = '" . $catatan_om . "', om_result = '" . $approval . "', om_tanggal = NOW(), om_pic = '" . $approval_pic . "'";
		if ($extend != "" && $extend == 1) {
			$sql1 .= ", flag_disposisi = 6";
			$ems1 = "select email_user from acl_user where id_role = 21";
		} else if ($extend != "" && $extend == 2) {
			$sql1 .= ", flag_approval = '" . $approval . "', tgl_approval = NOW(), pic_approval = '" . $user_id . "'";
			$ems1 = "
				select c.email_user 
				from pro_penawaran a 
				join pro_customer b on a.id_customer = b.id_customer 
				join acl_user c on b.id_marketing = c.id_user 
				where a.id_penawaran = '" . $idk . "'";

			$cek_persetujuan = $approval;
		}
		$sql1 .= " where id_customer = '" . $idr . "' and id_penawaran = '" . $idk . "'";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();

		$sql2 = "
				insert into pro_approval_hist (kd_approval, result, summary, id_user, tgl_approval, id_customer, id_penawaran, id_role, harga_dasar, oa_kirim, pbbkb, ppn, keterangan_pengajuan, volume)
				values ('P001', '" . $approval . "', '" . $catatan_om . "', '" . $user_id . "', NOW(), '" . $idr . "', '" . $idk . "', '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) . "', 
				" . $ls_harga_dasar . ", " . $ls_oa_kirim . ", " . $ls_pbbkb . ", " . $ls_ppn . ", '" . $ls_keterangan_pengajuan . "', " . $ls_volume . ");";
		$con->setQuery($sql2);
		$oke  = $oke && !$con->hasError();
	} else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 3) {
		$sql1 = "update pro_penawaran set coo_summary = '" . $catatan_coo . "', coo_result = '" . $approval . "', coo_tanggal = NOW(), coo_pic = '" . $approval_pic . "'";
		if ($extend != "" && $extend == 1) {
			$sql1 .= ", flag_disposisi = 6";
			$ems1 = "select email_user from acl_user where id_role = 21";
		} else if ($extend != "" && $extend == 2) {
			$sql1 .= ", flag_approval = '" . $approval . "', tgl_approval = NOW(), pic_approval = '" . $user_id . "'";
			$ems1 = "
				select c.email_user 
				from pro_penawaran a 
				join pro_customer b on a.id_customer = b.id_customer 
				join acl_user c on b.id_marketing = c.id_user 
				where a.id_penawaran = '" . $idk . "'";

			$cek_persetujuan = $approval;
		}
		$sql1 .= " where id_customer = '" . $idr . "' and id_penawaran = '" . $idk . "'";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();

		$sql2 = "
				insert into pro_approval_hist (kd_approval, result, summary, id_user, tgl_approval, id_customer, id_penawaran, id_role, harga_dasar, oa_kirim, pbbkb, ppn, keterangan_pengajuan, volume)
				values ('P001', '" . $approval . "', '" . $catatan_coo . "', '" . $user_id . "', NOW(), '" . $idr . "', '" . $idk . "', '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) . "', 
				" . $ls_harga_dasar . ", " . $ls_oa_kirim . ", " . $ls_pbbkb . ", " . $ls_ppn . ", '" . $ls_keterangan_pengajuan . "', " . $ls_volume . ");";
		$con->setQuery($sql2);
		$oke  = $oke && !$con->hasError();
	} else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 21) {
		$email = true;
		$sql1 = "update pro_penawaran set ceo_summary = '" . $catatan_ceo . "', ceo_result = '" . $approval . "', ceo_tanggal = NOW(), ceo_pic = '" . $approval_pic . "', 
					flag_approval = '" . $approval . "', tgl_approval = NOW(), pic_approval = '" . $user_id . "' where id_customer = '" . $idr . "' and id_penawaran = '" . $idk . "'";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();

		$sql2 = "
				insert into pro_approval_hist (kd_approval, result, summary, id_user, tgl_approval, id_customer, id_penawaran, id_role, harga_dasar, oa_kirim, pbbkb, ppn, keterangan_pengajuan, volume)
				values ('P001', '" . $approval . "', '" . $catatan_ceo . "', '" . $user_id . "', NOW(), '" . $idr . "', '" . $idk . "', '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) . "', 
				" . $ls_harga_dasar . ", " . $ls_oa_kirim . ", " . $ls_pbbkb . ", " . $ls_ppn . ", '" . $ls_keterangan_pengajuan . "', " . $ls_volume . ");";
		$con->setQuery($sql2);
		$oke  = $oke && !$con->hasError();

		$ems1 = "
			select c.email_user 
			from pro_penawaran a 
			join pro_customer b on a.id_customer = b.id_customer 
			join acl_user c on b.id_marketing = c.id_user 
			where a.id_penawaran = '" . $idk . "'";

		$cek_persetujuan = $approval;
	}

	if ($approval == '2') {
		$sql5 = "update pro_penawaran set view = 'No' where id_penawaran = '" . $idk . "'";
		$con->setQuery($sql5);
	}

	if ($oke) {
		if ($email) {
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
			if ($cek_persetujuan == '1') { // Ya
				$mail->Subject = "Persetujuan Penawaran [" . date('d/m/Y H:i:s') . "]";
				$mail->msgHTML($approval_pic . " menyetujui penawaran <p>" . BASE_SERVER . "</p>");
			} else if ($cek_persetujuan == '2') { // Tidak
				$mail->Subject = "Penolakan Penawaran [" . date('d/m/Y H:i:s') . "]";
				$mail->msgHTML($approval_pic . " menolak penawaran <p>" . BASE_SERVER . "</p>");
			} else { // Jika Disposisi
				$mail->Subject = "Persetujuan Penawaran [" . date('d/m/Y H:i:s') . "]";
				$mail->msgHTML($approval_pic . " meminta persetujuan penawaran <p>" . BASE_SERVER . "</p>");
			}

			$mail->send();
		}

		$con->commit();
		$con->close();
		header("location: " . $url);
		exit();
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}
}

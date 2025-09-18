<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "mailgen");

$auth   = new MyOtentikasi();
$con    = new Connection();
$flash  = new FlashAlerts;
$enk    = decode($_SERVER['REQUEST_URI']);
$id     = paramDecrypt(htmlspecialchars($_POST["id"], ENT_QUOTES));
$idc    = paramDecrypt(htmlspecialchars($_POST["idc"], ENT_QUOTES));

$role   = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$pic    = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']);
$id_wil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

$kode_pelanggan = htmlspecialchars($_POST["kode_pelanggan"], ENT_QUOTES);

$cl 		= str_replace(",", "", htmlspecialchars($_POST["cl"], ENT_QUOTES));
$cl_temp 	= str_replace(",", "", htmlspecialchars($_POST["cl_temp"], ENT_QUOTES));
$po_not_yet = str_replace(",", "", htmlspecialchars($_POST["po_not_yet"], ENT_QUOTES));
$up_07 		= str_replace(",", "", htmlspecialchars($_POST["ov_up_07"], ENT_QUOTES));
$ov_30 		= str_replace(",", "", htmlspecialchars($_POST["ov_under_30"], ENT_QUOTES));
$ov_60 		= str_replace(",", "", htmlspecialchars($_POST["ov_under_60"], ENT_QUOTES));
$ov_90 		= str_replace(",", "", htmlspecialchars($_POST["ov_under_90"], ENT_QUOTES));
$up_90 		= str_replace(",", "", htmlspecialchars($_POST["ov_up_90"], ENT_QUOTES));
$not_yet 	= str_replace(",", "", htmlspecialchars($_POST["not_yet"], ENT_QUOTES));
$reminding 	= str_replace(",", "", htmlspecialchars($_POST["reminding"], ENT_QUOTES));

$status_po 	= htmlspecialchars($_POST["status_po"], ENT_QUOTES);
$volume_po 	= str_replace(",", "", htmlspecialchars($_POST["volume_po"], ENT_QUOTES));
$amount_po 	= str_replace(",", "", htmlspecialchars($_POST["amount_po"], ENT_QUOTES));

$proposed  	= htmlspecialchars($_POST["proposed"], ENT_QUOTES);
$add_top   	= str_replace(",", "", htmlspecialchars($_POST["add_top"], ENT_QUOTES));
$add_cl    	= str_replace(",", "", htmlspecialchars($_POST["add_cl"], ENT_QUOTES));

$type_customer 		= htmlspecialchars($_POST["type_customer"], ENT_QUOTES);
$customer_date  	= htmlspecialchars($_POST["customer_date"], ENT_QUOTES);
$customer_amount 	= str_replace(",", "", htmlspecialchars($_POST["customer_amount"], ENT_QUOTES));

$credit_limit = ($cl ? $cl : 0);
$credit_limit_temp = ($cl_temp ? $cl_temp : 0);
$po_not_yet = ($po_not_yet ? $po_not_yet : 0);
$not_yet 	= ($not_yet ? $not_yet : 0);
$up_07 		= ($up_07 ? $up_07 : 0);
$ov_30 		= ($ov_30 ? $ov_30 : 0);
$ov_60 		= ($ov_60 ? $ov_60 : 0);
$ov_90 		= ($ov_90 ? $ov_90 : 0);
$up_90 		= ($up_90 ? $up_90 : 0);
$reminding 	= ($reminding ? $reminding : 0);
$volume_po 	= ($volume_po ? $volume_po : 0);
$amount_po 	= ($amount_po ? $amount_po : 0);
$add_top   	= ($add_top ? $add_top : 0);
$add_cl    	= ($add_cl ? $add_cl : 0);
$customer_amount = ($customer_amount ? $customer_amount : 0);

$ems2 			= null;
$email_jadwal 	= null;
$terima 		= null;

$filePhoto 	= htmlspecialchars($_FILES['attachment_unblock']['name'], ENT_QUOTES);
$sizePhoto 	= htmlspecialchars($_FILES['attachment_unblock']['size'], ENT_QUOTES);
$tempPhoto 	= htmlspecialchars($_FILES['attachment_unblock']['tmp_name'], ENT_QUOTES);
$extPhoto 	= substr($filePhoto, strrpos($filePhoto, '.'));
$max_size	= 2 * 1024 * 1024;
$allow_type	= array(".jpg", ".jpeg", ".JPG", ".png", ".pdf", ".rar", ".zip");
$pathfile	= $public_base_directory . '/files/uploaded_user/lampiran/unblock';
$uploadnya 	= false;
// 	$waktu_sekarang = date("H:i:s");
// $waktu_tutup = date("16:00:00");
// $waktu_buka = date("07:00:00");

if ($role == 10) {
	if ($proposed == '1' && $filePhoto == "") {
		$con->close();
		$flash->add("error", "File Attachment Unblock Belum Diupload", BASE_REFERER);
	} else if ($filePhoto != "" && $sizePhoto > $max_size) {
		$con->close();
		$flash->add("error", "Ukuran file terlalu besar, melebihi 2MB...", BASE_REFERER);
	} else if ($filePhoto != "" && !in_array($extPhoto, $allow_type)) {
		$con->close();
		$flash->add("error", "Tipe file tidak diperbolehkan...", BASE_REFERER);
	} else {
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		if ($proposed == '1') {
			if ($filePhoto != "") {
				$uploadnya = true;
				$lampirannya 		= $id . '_' . md5($filePhoto) . $extPhoto;
				$lampirannya_ori 	= sanitize_filename($filePhoto);
				// $lampirannya_ori 	= $id . '_ORI' . $extPhoto;
			}

			if ($type_customer == '1') {
				$customer_amount 	= ($customer_amount ? $customer_amount : 0);
				$customer_date 		= ($customer_date ? "'" . tgl_db($customer_date) . "'" : NULL);
				$extranya = "type_customer = '" . $type_customer . "', customer_amount = '" . $customer_amount . "', customer_date = " . $customer_date . "";
			} else if ($type_customer == '2') {
				$extranya = "type_customer = '" . $type_customer . "', customer_amount = '0', customer_date = NULL";
			} else {
				$extranya = "type_customer = NULL, customer_amount = '0', customer_date = NULL";
			}

			$sql = "
					update pro_sales_confirmation set 
					credit_limit = '" . $credit_limit . "', credit_limit_temp = '" . $credit_limit_temp . "', po_not_yet = '" . $po_not_yet . "', not_yet = '" . $not_yet . "', ov_up_07 = '" . $up_07 . "', 
					ov_under_30 = '" . $ov_30 . "', ov_under_60 = '" . $ov_60 . "', ov_under_90 = '" . $ov_90 . "', ov_up_90 = '" . $up_90 . "', reminding = '" . $reminding . "', 
					po_status = '" . $status_po . "', po_volume = '" . $volume_po . "', po_amount = '" . $amount_po . "', 
					proposed_status = '" . $proposed . "', add_top = '" . $add_top . "', add_cl = '" . $add_cl . "', 
					disposisi = 2, flag_approval = 0, role_approved = NULL, tgl_approved = NULL, 
					lampiran_unblock = '" . $lampirannya . "', lampiran_unblock_ori = '" . $lampirannya_ori . "', " . $extranya . " 
					where id = '" . $id . "' 
				";
			$con->setQuery($sql);
			$oke  = $oke && !$con->hasError();

			if ($type_customer == '2') {
				$sql_delete = "delete from pro_sales_colleteral where sales_id = '" . $id . "'";
				$con->setQuery($sql_delete);
				$oke  = $oke && !$con->hasError();

				if (count($_POST['customer_amount_coll']) > 0) {
					$nom = 0;
					foreach ($_POST['customer_amount_coll'] as $idx => $val) {
						$tgl_colat = htmlspecialchars($_POST["customer_date_coll"][$idx], ENT_QUOTES);
						$jml_colat = str_replace(",", "", htmlspecialchars($_POST["customer_amount_coll"][$idx], ENT_QUOTES));
						$itm_colat = htmlspecialchars($_POST["item_coll"][$idx], ENT_QUOTES);
						$jml_colat = ($jml_colat ? $jml_colat : 0);

						if ($tgl_colat) {
							$nom++;
							$sql_coll = "
									insert into pro_sales_colleteral(id, sales_id, date, amount, item) 
									values ('" . $nom . "', '" . $id . "', '" . tgl_db($tgl_colat) . "', '" . $jml_colat . "', '" . $itm_colat . "')
								";
							$con->setQuery($sql_coll);
							$oke  = $oke && !$con->hasError();
						}
					}
				}
			}
		} else {
			$sql = "
					update pro_sales_confirmation set 
					credit_limit = '" . $credit_limit . "', credit_limit_temp = '" . $credit_limit_temp . "', po_not_yet = '" . $po_not_yet . "', not_yet = '" . $not_yet . "', ov_up_07 = '" . $up_07 . "', 
					ov_under_30 = '" . $ov_30 . "', ov_under_60 = '" . $ov_60 . "', ov_under_90 = '" . $ov_90 . "', ov_up_90 = '" . $up_90 . "', reminding = '" . $reminding . "', 
					po_status = '" . $status_po . "', po_volume = '" . $volume_po . "', po_amount = '" . $amount_po . "', 
					proposed_status = '" . $proposed . "', add_top = '0', add_cl = '0', 
					disposisi = 2, flag_approval = 0, role_approved = NULL, tgl_approved = NULL  
					where id = '" . $id . "' 
				";
			$con->setQuery($sql);
			$oke  = $oke && !$con->hasError();
		}

		$sql3 = "
				update pro_customer_admin_arnya set 
				not_yet = '" . $not_yet . "', ov_up_07 = '" . $up_07 . "', 
				ov_under_30 = '" . $ov_30 . "', ov_under_60 = '" . $ov_60 . "', ov_under_90 = '" . $ov_90 . "', ov_up_90 = '" . $up_90 . "'  
				where id_customer = '" . $idc . "' 
			";
		$con->setQuery($sql3);
		$oke  = $oke && !$con->hasError();

		$approval 	= htmlspecialchars($_POST["approval"], ENT_QUOTES);
		$note   	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["admin_summary"], ENT_QUOTES));

		$sql2 = "
				update pro_sales_confirmation_approval set 
				adm_result = '" . $approval . "', adm_summary = '" . $note . "', adm_result_date = NOW(), adm_pic = '" . $pic . "',  
				bm_result = 0, bm_summary = NULL, bm_result_date = NULL, bm_pic = NULL,  
				om_result = 0, om_summary = NULL, om_result_date = NULL, om_pic = NULL,  
				mgr_result = 0, mgr_summary = NULL, mgr_result_date = NULL, mgr_pic = NULL,  
				cfo_result = 0, cfo_summary = NULL, cfo_result_date = NULL, cfo_pic = NULL   
				where id_sales = '" . $id . "'
			";
		$con->setQuery($sql2);
		$oke  = $oke && !$con->hasError();

		// if ($kode_pelanggan) {
		// 	$sql3 = "update pro_customer set kode_pelanggan = '" . $kode_pelanggan . "' where id_customer = " . $idc;
		// 	$con->setQuery($sql3);
		// 	$oke  = $oke && !$con->hasError();
		// }

		if ($approval == 1) $ems1 = "select email_user from acl_user where id_role in(7) and id_wilayah = '" . $id_wil . "'";
		//$email_jadwal = "select email_user from acl_user where id_role in(11) and id_user = (select id_marketing from pro_customer where id_customer = '".$idc."')";
	}
} else if ($role == 7) {
	$approval 	= htmlspecialchars($_POST["approval"], ENT_QUOTES);
	$note   	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["bm_summary"], ENT_QUOTES));

	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	if ($approval == '1') {
		$sql2 = "
				update pro_sales_confirmation_approval set 
				bm_result = '" . $approval . "', bm_summary = '" . $note . "', bm_result_date = NOW(), bm_pic = '" . $pic . "' 
				where id_sales = '" . $id . "'
			";
		$con->setQuery($sql2);
		$oke  = $oke && !$con->hasError();

		$terima = 1;
		$con->setQuery("update pro_sales_confirmation set flag_approval = " . $approval . ", role_approved = 7, tgl_approved = NOW() where id = " . $id);
		$oke  = $oke && !$con->hasError();

		$sql3 = "SELECT id_customer FROM pro_sales_confirmation WHERE id = '" . $id . "'";
		$ambil_cust = $con->getRecord($sql3);

		$sql_cust = "UPDATE pro_customer set credit_limit_temp = '" . $add_cl . "' where id_customer = '" . $ambil_cust['id_customer'] . "'";
		$con->setQuery($sql_cust);
		$oke  = $oke && !$con->hasError();
	} else if ($approval == '2') {
		$sql2 = "
				update pro_sales_confirmation_approval set 
				bm_result = '" . $approval . "', bm_summary = '" . $note . "', bm_result_date = NOW(), bm_pic = '" . $pic . "' 
				where id_sales = '" . $id . "'
			";
		$con->setQuery($sql2);
		$oke  = $oke && !$con->hasError();

		$email_jadwal = "select email_user from acl_user where id_role in(11) and id_user = (select id_marketing from pro_customer where id_customer = '" . $idc . "')";
		$con->setQuery("update pro_sales_confirmation set flag_approval = " . $approval . ", role_approved = 7, tgl_approved = NOW(), disposisi = 1 where id = " . $id);
		$oke  = $oke && !$con->hasError();
	}

	$oke = $oke && history($id, $con);
}

if ($terima == '1') {
	$row 	= $con->getRecord('select * from pro_sales_confirmation sc join pro_sales_confirmation_approval sca on sca.id_sales = sc.id where sc.id = ' . $id);
	$notif 	= ($approval == '1' ? 1 : 0);

	$sql = "
			update pro_po_customer set poc_approved = '" . $approval . "', tgl_approved = NOW(), 
			sm_summary = '" . $note . "', sm_result = 1, sm_tanggal = NOW(), sm_pic = '" . $pic . "', po_notif = " . $notif . " 
			where id_poc = '" . $row['id_poc'] . "' and id_customer = '" . $idc . "'
		";

	$con->setQuery($sql);
	$oke  = $oke && !$con->hasError();
	$ems2 = "select email_user from acl_user where id_role in(11, 17) and id_user = (select id_marketing from pro_customer where id_customer = '" . $idr . "')";
}

if ($oke) {
	$mail = new PHPMailer;
	$mail->isSMTP();
	$mail->Host = 'smtp.gmail.com';
	$mail->Port = 465;
	$mail->SMTPSecure = 'ssl';
	$mail->SMTPAuth = true;
	$mail->SMTPKeepAlive = true;
	$mail->Username = USR_EMAIL_PROENERGI202389;
	$mail->Password = PWD_EMAIL_PROENERGI202389;

	if ($ems1) {
		$rms1 = $con->getResult($ems1);

		$mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');
		foreach ($rms1 as $datms) {
			$mail->addAddress($datms['email_user']);
		}
		$mail->Subject = "Persetujuan Sales Confirmation [" . date('d/m/Y H:i:s') . "]";
		$mail->msgHTML(paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " Meminta Persetujuan anda <p>" . BASE_SERVER . "</p>");
		$mail->send();
	}

	if ($ems2) {
		$rms1 = $con->getResult($ems2);

		$mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');
		foreach ($rms1 as $datms) {
			$mail->addAddress($datms['email_user']);
		}

		$mail->Subject = "Persetujuan PO[" . date('d/m/Y H:i:s') . "]";
		$mail->msgHTML(paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " Menyetujui PO Customer anda <p>" . BASE_SERVER . "</p>");

		$mail->send();
	}

	if ($email_jadwal) {
		$rms1 = $con->getResult($email_jadwal);

		$mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');
		foreach ($rms1 as $datms) {
			$mail->addAddress($datms['email_user']);
		}

		$mail->Subject = "Silahkan jadwalkan pengiriman[" . date('d/m/Y H:i:s') . "]";
		$mail->msgHTML(paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " Menyetujui PO Customer anda <p>" . BASE_SERVER . "</p>");

		$mail->send();
	}

	if ($uploadnya) {
		$tmpPot = glob($pathfile . "/" . $id . "_*.{jpg,jpeg,gif,png,pdf,rar,zip}", GLOB_BRACE);

		if (count($tmpPot) > 0) {
			foreach ($tmpPot as $datj)
				if (file_exists($datj)) unlink($datj);
		}
		$tujuan  = $pathfile . "/" . $lampirannya;
		$mantab  = move_uploaded_file($tempPhoto, $tujuan);
		if (file_exists($tempPhoto)) unlink($tempPhoto);
	}

	$con->commit();
	$con->close();
	header("location: " . BASE_URL_CLIENT . "/pro_sales_confirmation.php");
	exit();
} else {
	$con->rollBack();
	$con->clearError();
	$con->close();
	$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
}

function history($id, $con)
{

	$row = $con->getRecord('select * from pro_sales_confirmation sc join pro_sales_confirmation_approval sca on sca.id_sales = sc.id where sc.id = ' . $id);
	$con->setQuery('DELETE FROM pro_sales_confirmation_log WHERE id_sales = ' . $row['id_sales']);
	$type_customer = ($row['type_customer'] ? "'" . $row['type_customer'] . "'" : 'NULL');
	$sql = "
			INSERT INTO pro_sales_confirmation_log (
			id_sales, id_customer, id_poc, id_wilayah, 
			not_yet, ov_under_30, ov_under_60, ov_under_90, ov_up_90, reminding,
			type_customer, customer_items, customer_date, customer_amount,
			po_status, po_volume, po_amount, proposed_status,
			add_top, add_cl, supply_date, period_date,
			adm_result, adm_pic, adm_summary, adm_result_date, bm_result, bm_pic, bm_summary, bm_result_date) 
			VALUES (
				'" . $row['id_sales'] . "', '" . $row['id_customer'] . "', '" . $row['id_poc'] . "', '" . $row['id_wilayah'] . "', 
				'" . $row['not_yet'] . "', '" . $row['ov_under_30'] . "', '" . $row['ov_under_60'] . "', '" . $row['ov_under_90'] . "', '" . $row['ov_up_90'] . "', '" . $row['reminding'] . "', 
				" . $type_customer . ", '" . $row['customer_items'] . "', 
				" . ($row['customer_date'] ? "'" . $row['customer_date'] . "'" : 'NULL') . ", '" . $row['customer_amount'] . "', 
				'" . $row['po_status'] . "', '" . $row['po_volume'] . "', '" . $row['po_amount'] . "', '" . $row['proposed_status'] . "', 
				'" . $row['add_top'] . "', '" . $row['add_cl'] . "', 
				" . ($row['supply_date'] ? "'" . $row['supply_date'] . "'" : 'NULL') . ", " . ($row['period_date'] ? "'" . $row['period_date'] . "'" : 'NULL') . ", 
				'" . $row['adm_result'] . "', '" . $row['adm_pic'] . "', '" . $row['adm_summary'] . "', " . ($row['adm_result_date'] ? "'" . $row['adm_result_date'] . "'" : 'NULL') . ", 
				'" . $row['bm_result'] . "', '" . $row['bm_pic'] . "', '" . $row['bm_summary'] . "', " . ($row['bm_result_date'] ? "'" . $row['bm_result_date'] . "'" : 'NULL') . " 
        	)
		";
	$con->setQuery($sql);
	return !$con->hasError();
}

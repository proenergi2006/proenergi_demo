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
$act	= isset($enk['act']) ? $enk['act'] : (isset($_POST['act']) ? htmlspecialchars($_POST["act"], ENT_QUOTES) : null);
$idq1 	= htmlspecialchars($_POST["idq1"], ENT_QUOTES);
$idq2 	= htmlspecialchars($_POST["idq2"], ENT_QUOTES);
$url 	= BASE_URL_CLIENT . "/po-customer-logistik.php?" . paramEncrypt("q1=" . $idq1 . "&q2=" . $idq2);
$tombol = htmlspecialchars($_POST["tombol_klik"], ENT_QUOTES);

$oke = true;
$con->beginTransaction();
$con->clearError();

$arrExtraData = array();
if (isset($_POST["newdt4"])) {
	foreach ($_POST['newdt4'] as $idx1 => $val1) {
		foreach ($_POST['newdt4'][$idx1] as $idx2 => $val2) {
			$cek01 		= htmlspecialchars($_POST['newcek'][$idx1][$idx2], ENT_QUOTES);
			$dtx01 		= htmlspecialchars($_POST['newdt1'][$idx1][$idx2], ENT_QUOTES);
			$tgl_loading = htmlspecialchars($_POST['newtgl'][$idx1][$idx2], ENT_QUOTES);
			$dtx03 		= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['newdt3'][$idx1][$idx2]), ENT_QUOTES);
			$volume 	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['newdt4'][$idx1][$idx2]), ENT_QUOTES);
			$volplan 	= htmlspecialchars($_POST['volplan' . $idx1], ENT_QUOTES);

			$sql_get_nomor_so = "SELECT no_so FROM pro_po_customer_plan WHERE id_plan = '" . $idx1 . "'";
			$row_nomor_so = $con->getRecord($sql_get_nomor_so);
			$nomor_so = $row_nomor_so['no_so'] ?? '';

			$nomor_so_detail = $nomor_so . "-S" . $no;

			$sqlExtra 	= "
					insert into pro_po_customer_plan (id_poc, id_lcr, no_so, tanggal_kirim, tanggal_loading, volume_kirim, realisasi_kirim, is_urgent, top_plan, actual_top_plan, pelanggan_plan, 
					ar_notyet, ar_satu, ar_dua, kredit_limit, status_plan, status_jadwal, ask_approval, catatan_reschedule, 
					is_approved, created_time, created_ip, created_by, splitted_from_plan, vol_ori_plan) (
						select id_poc, id_lcr, '" . $nomor_so_detail . "', tanggal_kirim, '" . tgl_db($tgl_loading) . "', '" . $volume . "', realisasi_kirim, is_urgent, top_plan, actual_top_plan, pelanggan_plan, 
						ar_notyet, ar_satu, ar_dua, kredit_limit, status_plan, status_jadwal, ask_approval, catatan_reschedule, is_approved, 
						created_time, created_ip, created_by, '" . $idx1 . "', '" . $volplan . "' 
						from pro_po_customer_plan
						where id_plan = '" . $idx1 . "'
					)
				";
			$row1 = $con->setQuery($sqlExtra);
			$oke  = $oke && !$con->hasError();

			if ($cek01) {
				$arrExtraData[] = array('id_plan' => $row1, 'produk' => $dtx01, 'volume' => $volume, 'oanya' => $dtx03);
			}
		}
	}
}

if ($tombol == 1) {
	$wilayah	= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
	$group		= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);
	$tgl_create = date("Y/m/d");

	if (!is_array_empty($_POST["cek"]) || count($arrExtraData) > 0) {
		$cek1 = "select inisial_cabang, urut_pr from pro_master_cabang where id_master = '" . $wilayah . "' for update";
		$row1 = $con->getRecord($cek1);
		$tmp1 = $row1['urut_pr'] + 1;
		$tmp2 = array("1" => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
		$noms = str_pad($tmp1, 4, '0', STR_PAD_LEFT) . '/PE/DR/' . $row1['inisial_cabang'] . '/' . $tmp2[intval(date("m"))] . '/' . date("Y");

		//$ems1 = "select email_user from acl_user where id_role = 10 and id_wilayah = '" . $wilayah . "'";

		$ems1 = "select email_user from acl_user where id_role = 5";
		$sbjk = "Persetujuan DR [" . date('d/m/Y H:i:s') . "]";
		$pesn = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " meminta persetujuan untuk DR";
		$sql1 = "insert into pro_pr (id_wilayah, id_group, nomor_pr, tanggal_pr, logistik_result, logistik_pic, logistik_tanggal, disposisi_pr, finance_result, sm_result, is_ceo) values ('" . $wilayah . "', 
					'" . $group . "', '" . $noms . "', '" . $tgl_create . "', 1, '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "', NOW(), 3, 1, 1, 1)";
		$res1 = $con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();


		foreach ($_POST['cek'] as $idx1 => $val1) {
			$dt1 = htmlspecialchars($_POST['dt1'][$idx1], ENT_QUOTES);
			$dt4 = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['dt4'][$idx1]), ENT_QUOTES);
			$dt3 = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['dt3'][$idx1]), ENT_QUOTES);
			$tgl_loading = htmlspecialchars($_POST["tgl_loading"][$idx1], ENT_QUOTES);

			$sql2 = "update pro_po_customer_plan set tanggal_loading = '" . tgl_db($tgl_loading) . "',volume_kirim = '" . $dt4 . "', status_plan = 1 where id_plan = '" . $idx1 . "'";
			$con->setQuery($sql2);
			$oke  = $oke && !$con->hasError();

			$sql3 = "insert into pro_pr_detail(id_pr, id_plan, produk, volume, transport, is_approved) values ('" . $res1 . "', '" . $idx1 . "', '" . $dt1 . "', '" . $dt4 . "', '" . $dt3 . "','1')";
			$con->setQuery($sql3);
			$oke  = $oke && !$con->hasError();
		}

		if (count($arrExtraData) > 0) {
			foreach ($arrExtraData as $idx01 => $data01) {
				$id_plan 	= $data01['id_plan'];
				$produk 	= $data01['produk'];
				$tgl_loading = $data01['tgl_loading'];
				$volume 	= $data01['volume'];
				$oanya 		= $data01['oanya'];

				$sql2 = "update pro_po_customer_plan set volume_kirim = '" . $volume . "', status_plan = 1 where id_plan = '" . $id_plan . "'";
				$con->setQuery($sql2);
				$oke  = $oke && !$con->hasError();

				$sql3 = "insert into pro_pr_detail(id_pr, id_plan, produk, volume, transport, is_approved) values ('" . $res1 . "', '" . $id_plan . "', '" . $produk . "', '" . $volume . "', '" . $oanya . "','1')";
				$con->setQuery($sql3);
				$oke  = $oke && !$con->hasError();
			}
		}

		$sql4 = "update pro_master_cabang set urut_pr = '" . $tmp1 . "' where id_master = '" . $wilayah . "'";
		$con->setQuery($sql4);
		$oke  = $oke && !$con->hasError();
	}
} else if ($tombol == 2) {
	$wilayah	= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
	$group		= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);
	$catatan 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["catatan_logistik"], ENT_QUOTES));

	//$ems1 = "select email_user from acl_user where id_role = 7 and id_wilayah = '" . $wilayah . "'";
	$ems1 = "select email_user from acl_user where id_role = 7 and id_wilayah = '" . $wilayah . "'";
	$sbjk = "Persetujuan PO Customer [" . date('d/m/Y H:i:s') . "]";
	$pesn = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " meminta persetujuan untuk PO";
	$sql1 = "insert into pro_po_customer_om (id_wilayah, id_group, tanggal_issued, catatan_logistik) values ('" . $wilayah . "', '" . $group . "', NOW(), '" . $catatan . "')";
	$res1 = $con->setQuery($sql1);
	$oke  = $oke && !$con->hasError();
	foreach ($_POST['cek'] as $idx => $val) {
		$sql2 = "insert into pro_po_customer_om_detail (id_ppco, id_plan) values ('" . $res1 . "', '" . $idx . "')";
		$con->setQuery($sql2);
		$oke  = $oke && !$con->hasError();

		$sql3 = "update pro_po_customer_plan set ask_approval = 1, is_approved = 0 where id_plan = '" . $idx . "'";
		$con->setQuery($sql3);
		$oke  = $oke && !$con->hasError();
	}
} else if ($tombol == 3) {
	$catatan 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["catatan_logistik"], ENT_QUOTES));
	foreach ($_POST['cek'] as $idx => $val) {
		$sql1 = "update pro_po_customer_plan set status_plan = '2', is_approved = 0, catatan_reschedule = '" . $catatan . "' where id_plan = '" . $idx . "'";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();
	}
}

if ($oke) {
	if ($ems1) {
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

		$mail->Subject = $sbjk;
		$mail->msgHTML($pesn);
		$mail->send();

		// $mail->Subject = "Persetujuan PO Customer [" . date('d/m/Y H:i:s') . "]";
		// $mail->msgHTML(paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " meminta persetujuan untuk po customer <p>" . BASE_SERVER . "</p>");
		// $mail->send();
	}
	$con->commit();
	$con->close();
	$flash->add("success", "Data berhasil diproses", BASE_REFERER);
	// header("location: ".$url);	
	exit();
} else {
	$con->rollBack();
	$con->clearError();
	$con->close();
	$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
}

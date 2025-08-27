<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;
$enk  	= decode($_SERVER['REQUEST_URI']);
$act	= ($enk['act'] == "") ? htmlspecialchars($_POST["act"], ENT_QUOTES) : $enk['act'];
$idr 	= isset($_POST["idr"]) ? htmlspecialchars($_POST["idr"], ENT_QUOTES) : 0;

$nomor_plat		= htmlspecialchars($_POST["nomor_plat"], ENT_QUOTES);
$transportir	= htmlspecialchars($_POST["transportir"], ENT_QUOTES);
$max_kap 		= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["max_kap"]), ENT_QUOTES);
$link_gps 		= htmlspecialchars($_POST["link_gps"], ENT_QUOTES);
$user_gps 		= htmlspecialchars($_POST["user_gps"], ENT_QUOTES);
$pass_gps 		= htmlspecialchars($_POST["pass_gps"], ENT_QUOTES);
$membercode_gps = htmlspecialchars($_POST["membercode_gps"], ENT_QUOTES);
$active         = isset($_POST['active']) ? (int)$_POST['active'] : 0;
$filePhoto 		= htmlspecialchars($_FILES['photo']['name'], ENT_QUOTES);
$sizePhoto 		= htmlspecialchars($_FILES['photo']['size'], ENT_QUOTES);
$tempPhoto 		= htmlspecialchars($_FILES['photo']['tmp_name'], ENT_QUOTES);
$extPhoto 		= substr($filePhoto, strrpos($filePhoto, '.'));

$kompart 	= array();
$upload 	= array();
$delPic 	= array();
$max_size	= 2 * 1024 * 1024;
$allow_type	= array(".jpg", ".jpeg", ".JPG", ".png");
$pathfile	= $public_base_directory . '/files/uploaded_user/lampiran';
$arNamaFile = array();
$user_pic 	= paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']);

foreach ($_POST['fkomp'] as $idxa => $vala) {
	$fkomp	= htmlspecialchars($_POST["fkomp"][$idxa], ENT_QUOTES);
	$ftank	= htmlspecialchars($_POST["ftank"][$idxa], ENT_QUOTES);
	if ($fkomp && $ftank) {
		array_push($kompart, array("kompart" => $fkomp, "tanki" => $ftank));
	}
}

if ($nomor_plat == "" || $transportir == "") {
	$con->close();
	$flash->add("error", "KOSONG", BASE_REFERER);
} else if ($filePhoto != "" && $sizePhoto > $max_size) {
	$con->close();
	$flash->add("error", "Ukuran file photo terlalu besar, melebihi 2MB...", BASE_REFERER);
} else if ($filePhoto != "" && !in_array($extPhoto, $allow_type)) {
	$con->close();
	$flash->add("error", "Tipe file photo yang diperbolehkan hanya .jpg dan .png", BASE_REFERER);
} else {
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	if ($act == 'add') {
		$msg = "GAGAL_MASUK";
		if ($filePhoto != "") {
			$upl = true;
			$sql = "insert into pro_master_transportir_mobil(id_transportir, nomor_plat, max_kap, komp_tanki, link_gps, user_gps, pass_gps, membercode_gps, photo_ori, is_active, created_time, 
						created_ip, created_by) values ('" . $transportir . "', '" . $nomor_plat . "', '" . $max_kap . "', '" . json_encode($kompart) . "', '" . $link_gps . "', '" . $user_gps . "', '" . $pass_gps . "', '" . $membercode_gps . "', '" . sanitize_filename($filePhoto) . "', '" . $active . "', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . $user_pic . "')";
			$idr = $con->setQuery($sql);
			$oke = $oke && !$con->hasError();

			$nqu = 'pics_' . $idr . '_' . sanitize_filename($filePhoto);
			$que = "update pro_master_transportir_mobil set photo = '" . $nqu . "' where id_master = '" . $idr . "'";
			$con->setQuery($que);
			$oke = $oke && !$con->hasError();
		} else {
			$upl = false;
			$nqu = '';
			$sql = "insert into pro_master_transportir_mobil(id_transportir, nomor_plat, max_kap, komp_tanki, link_gps, user_gps, pass_gps, membercode_gps, is_active, created_time, 
						created_ip, created_by) values ('" . $transportir . "', '" . $nomor_plat . "', '" . $max_kap . "', '" . json_encode($kompart) . "', '" . $link_gps . "', '" . $user_gps . "', '" . $pass_gps . "', '" . $membercode_gps . "', '" . $active . "', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . $user_pic . "')";
			$idr = $con->setQuery($sql);
			$oke = $oke && !$con->hasError();
		}
	} else if ($act == 'update') {
		$msg = "GAGAL_UBAH";
		if ($filePhoto != "") {
			$upl = true;
			$nqu = 'pics_' . $idr . '_' . sanitize_filename($filePhoto);
			$sql = "update pro_master_transportir_mobil set nomor_plat = '" . $nomor_plat . "', photo = '" . $nqu . "', photo_ori = '" . sanitize_filename($filePhoto) . "', 
						max_kap = '" . $max_kap . "', komp_tanki = '" . json_encode($kompart) . "', link_gps = '" . $link_gps . "', user_gps = '" . $user_gps . "', pass_gps = '" . $pass_gps . "', membercode_gps = '" . $membercode_gps . "', 
						is_active = '" . $active . "', lastupdate_time = NOW(), lastupdate_ip = '" . $_SERVER['REMOTE_ADDR'] . "', lastupdate_by = '" . $user_pic . "' where id_master = " . $idr;
			$con->setQuery($sql);
			$oke = $oke && !$con->hasError();
		} else {
			$upl = false;
			$nqu = '';
			$sql = "update pro_master_transportir_mobil set nomor_plat = '" . $nomor_plat . "', max_kap = '" . $max_kap . "', komp_tanki = '" . json_encode($kompart) . "', 
						link_gps = '" . $link_gps . "', user_gps = '" . $user_gps . "', pass_gps = '" . $pass_gps . "', membercode_gps = '" . $membercode_gps . "', is_active = '" . $active . "', lastupdate_time = NOW(), 
						lastupdate_ip = '" . $_SERVER['REMOTE_ADDR'] . "', lastupdate_by = '" . $user_pic . "' where id_master = " . $idr;
			$con->setQuery($sql);
			$oke = $oke && !$con->hasError();
		}
	}

	if (!is_array_empty($_POST["newdok1"])) {
		foreach ($_POST["newdok1"] as $idx1 => $val1) {
			$newdok1 = htmlspecialchars($_POST["newdok1"][$idx1], ENT_QUOTES);
			$newdok2 = htmlspecialchars($_POST["newdok2"][$idx1], ENT_QUOTES);
			$newdok3 = htmlspecialchars($_FILES['newdok3']['name'][$idx1], ENT_QUOTES);
			if ($newdok1) {
				$sql2 = "insert into pro_master_transportir_mobil_detail(id_transportir_mobil, dokumen, masa_berlaku, lampiran_ori) 
							 values ('" . $idr . "', '" . $newdok1 . "', '" . tgl_db($newdok2) . "', '" . sanitize_filename($newdok3) . "')";
				$idk = $con->setQuery($sql2);
				$oke = $oke && !$con->hasError();

				if ($newdok3) {
					$lampiran = 'mobil_' . $idr . '_' . $idk . '_' . sanitize_filename($newdok3);
					$upload[$idx1] = $lampiran;

					$sql3 = "update pro_master_transportir_mobil_detail set lampiran = '" . $lampiran . "' where id_tmd = '" . $idk . "'";
					$con->setQuery($sql3);
					$oke = $oke && !$con->hasError();
				}
			}
		}
	}

	if (!is_array_empty($_POST["doksup"])) {
		foreach ($_POST["doksup"] as $idx2 => $val2) {
			if (!$_POST["doknya"][$idx2]) {
				$sql4 = "delete from pro_master_transportir_mobil_detail where id_tmd = '" . $idx2 . "'";
				$con->setQuery($sql4);
				$oke = $oke && !$con->hasError();

				$tmpPic = glob($pathfile . "/mobil_" . $idr . "_" . $idx2 . "_*.{jpg,jpeg,gif,png,pdf,zip,rar}", GLOB_BRACE);
				if (count($tmpPic) > 0) {
					foreach ($tmpPic as $datx)
						$delPic[$idx2] = $datx;
				}
			}
		}
	}

	if ($oke) {
		$mantab  = true;
		if (!is_array_empty($upload)) {
			foreach ($_FILES['newdok3']['name'] as $idx5 => $val5) {
				$filetmp = htmlspecialchars($_FILES['newdok3']["tmp_name"][$idx5], ENT_QUOTES);
				$tujuan  = $pathfile . "/" . $upload[$idx5];
				$mantab  = $mantab && move_uploaded_file($filetmp, $tujuan);
				if (file_exists($filetmp)) unlink($filetmp);
			}
		}
		if (!is_array_empty($delPic)) {
			foreach ($delPic as $idx6 => $val6) {
				if (file_exists($val6)) unlink($val6);
			}
		}
		if ($upl) {
			$tmpPot = glob($pathfile . "/pics_" . $idr . "_*.{jpg,jpeg,gif,png}", GLOB_BRACE);
			if (count($tmpPot) > 0) {
				foreach ($tmpPot as $datj)
					if (file_exists($datj)) unlink($datj);
			}
			$tujuan  = $pathfile . "/" . $nqu;
			$mantab  = $mantab && move_uploaded_file($tempPhoto, $tujuan);
			if (file_exists($tempPhoto)) unlink($tempPhoto);
		}
		if ($mantab) {
			$con->commit();
			$con->close();
			header("location: " . BASE_URL_CLIENT . "/master-transportir-mobil.php");
			exit();
		} else {
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", $msg, BASE_REFERER);
		}
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", $msg, BASE_REFERER);
	}
}

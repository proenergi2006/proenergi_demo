<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "htmlawed");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;
$enk  	= decode($_SERVER['REQUEST_URI']);
$act	= ($enk['act'] == "") ? htmlspecialchars($_POST["act"], ENT_QUOTES) : $enk['act'];
$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
$active = isset($_POST["active"]) ? htmlspecialchars($_POST["active"], ENT_QUOTES) : '0';
$fleet 	= isset($_POST["fleet"]) ? htmlspecialchars($_POST["fleet"], ENT_QUOTES) : 0;
if ($fleet == '') $fleet = 0;

$nama_transportir 	= htmlspecialchars($_POST["nama"], ENT_QUOTES);
$nama_suplier 		= htmlspecialchars($_POST["nama_sup"], ENT_QUOTES);
$lokasi_suplier 	= htmlspecialchars($_POST["lok_sup"], ENT_QUOTES);
$telp_suplier 		= htmlspecialchars($_POST["telp_sup"], ENT_QUOTES);
$fax_suplier 		= htmlspecialchars($_POST["fax_sup"], ENT_QUOTES);
$terms_suplier 		= htmlspecialchars($_POST["terms_sup"], ENT_QUOTES);
$tipe_angkutan 		= htmlspecialchars($_POST["tipe"], ENT_QUOTES);
$alamat_suplier 	= htmlspecialchars($_POST["almt_sup"], ENT_QUOTES);
$owner_suplier 		= htmlspecialchars($_POST["owner_suplier"], ENT_QUOTES);
$catatan 			= htmLawed($_POST["catatan"], array('safe' => 1));
$owner_suplier 		= ($owner_suplier ? $owner_suplier : '0');

$attention 	= array();
$upload 	= array();
$delPic 	= array();
$max_size	= 2 * 1024 * 1024;
$allow_type	= array(".jpg", ".jpeg", ".JPG", ".png", ".pdf", ".rar");
$pathfile	= $public_base_directory . '/files/uploaded_user/lampiran';
$arNamaFile = array();

foreach ($_POST['att1'] as $idxa => $vala) {
	$att1 = htmlspecialchars($_POST["att1"][$idxa], ENT_QUOTES);
	$att2 = htmlspecialchars($_POST["att2"][$idxa], ENT_QUOTES);
	$att3 = htmlspecialchars($_POST["att3"][$idxa], ENT_QUOTES);
	$att4 = htmlspecialchars($_POST["att4"][$idxa], ENT_QUOTES);
	if ($att1) {
		array_push($attention, array("nama" => $att1, "posisi" => $att2, "hp" => $att3, "email" => $att4));
	}
}

if ($nama_transportir == "" || $nama_suplier == "" || $lokasi_suplier == "" || $telp_suplier == "" || $fax_suplier == "" || $tipe_angkutan == "" || $terms_suplier == "") {
	$con->close();
	$flash->add("error", "KOSONG", BASE_REFERER);
} else {
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	if ($act == 'add') {
		$msg = "GAGAL_MASUK";
		$sql = "insert into pro_master_transportir(nama_transportir, nama_suplier, lokasi_suplier, owner_suplier, alamat_suplier, terms_suplier, telp_suplier, fax_suplier, att_suplier, tipe_angkutan, is_fleet, is_active, catatan, created_time, created_ip, created_by) values ('" . $nama_transportir . "', '" . $nama_suplier . "', '" . $lokasi_suplier . "', '" . $owner_suplier . "', '" . $alamat_suplier . "',  '" . $terms_suplier . "', '" . $telp_suplier . "', '" . $fax_suplier . "', '" . json_encode($attention) . "', '" . $tipe_angkutan . "', '" . $fleet . "', '" . $active . "', '" . $catatan . "', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "')";
		$idr = $con->setQuery($sql);
		$oke = $oke && !$con->hasError();
	} else if ($act == 'update') {
		$msg = "GAGAL_UBAH";
		$sql = "update pro_master_transportir set nama_transportir = '" . $nama_transportir . "', owner_suplier = '" . $owner_suplier . "', nama_suplier = '" . $nama_suplier . "', lokasi_suplier = '" . $lokasi_suplier . "', terms_suplier = '" . $terms_suplier . "', telp_suplier = '" . $telp_suplier . "', fax_suplier = '" . $fax_suplier . "', att_suplier = '" . json_encode($attention) . "', tipe_angkutan = '" . $tipe_angkutan . "', is_fleet = '" . $fleet . "', is_active = '" . $active . "', catatan = '" . $catatan . "', lastupdate_time = NOW(), lastupdate_ip = '" . $_SERVER['REMOTE_ADDR'] . "', lastupdate_by = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "' where id_master = " . $idr;
		$con->setQuery($sql);
		$oke = $oke && !$con->hasError();
	}

	if (!is_array_empty($_POST["newdok1"])) {
		foreach ($_POST["newdok1"] as $idx1 => $val1) {
			$newdok1 = htmlspecialchars($_POST["newdok1"][$idx1], ENT_QUOTES);
			$newdok2 = htmlspecialchars($_POST["newdok2"][$idx1], ENT_QUOTES);
			$newdok3 = htmlspecialchars($_FILES['newdok3']['name'][$idx1], ENT_QUOTES);
			if ($newdok1) {
				$sql2 = "insert into pro_master_transportir_detail(id_transportir, dokumen, masa_berlaku, lampiran_ori) values ('" . $idr . "', '" . $newdok1 . "', 
							'" . tgl_db($newdok2) . "', '" . sanitize_filename($newdok3) . "')";
				$idk = $con->setQuery($sql2);
				$oke = $oke && !$con->hasError();

				if ($newdok3) {
					$lampiran = 'sup_' . $idr . '_' . $idk . '_' . sanitize_filename($newdok3);
					$upload[$idx1] = $lampiran;

					$sql3 = "update pro_master_transportir_detail set lampiran = '" . $lampiran . "' where id_td = '" . $idk . "'";
					$con->setQuery($sql3);
					$oke = $oke && !$con->hasError();
				}
			}
		}
	}

	if (!is_array_empty($_POST["doksup"])) {
		foreach ($_POST["doksup"] as $idx2 => $val2) {
			if (!$_POST["doknya"][$idx2]) {
				$sql4 = "delete from pro_master_transportir_detail where id_td = '" . $idx2 . "'";
				$con->setQuery($sql4);
				$oke = $oke && !$con->hasError();

				$tmpPic = glob($pathfile . "/sup_" . $idr . "_" . $idx2 . "_*.{jpg,jpeg,gif,png,pdf,zip,rar}", GLOB_BRACE);
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
		if ($mantab) {
			$con->commit();
			$con->close();
			header("location: " . BASE_URL_CLIENT . "/master-transportir.php");
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

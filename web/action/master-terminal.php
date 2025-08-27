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

$nama	= htmlspecialchars($_POST["nama"], ENT_QUOTES);
$telp	= htmlspecialchars($_POST["telp"], ENT_QUOTES);
$fax	= htmlspecialchars($_POST["fax"], ENT_QUOTES);
$cc		= htmlspecialchars($_POST["cc"], ENT_QUOTES);
$lokasi	= htmlspecialchars($_POST["lokasi"], ENT_QUOTES);
$tanki	= htmlspecialchars($_POST["tanki"], ENT_QUOTES);
$alamat	= htmlspecialchars($_POST["alamat"], ENT_QUOTES);
$active = isset($_POST["active"]) ? htmlspecialchars($_POST["active"], ENT_QUOTES) : '0';
$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
$initial 		= htmlspecialchars($_POST["initial"], ENT_QUOTES);
$kategori 		= htmlspecialchars($_POST["kategori"], ENT_QUOTES);
$batas_atas 	= htmlspecialchars($_POST["batas_atas"], ENT_QUOTES);
$batas_bawah 	= htmlspecialchars($_POST["batas_bawah"], ENT_QUOTES);
$latitude 	= htmlspecialchars($_POST["latitude"], ENT_QUOTES);
$longitude 	= htmlspecialchars($_POST["longitude"], ENT_QUOTES);

$id_cabang	= htmlspecialchars($_POST["id_cabang"], ENT_QUOTES);
$id_area	= htmlspecialchars($_POST["id_area"], ENT_QUOTES);
$id_area	= ($id_area ? $id_area : 0);

$note 		= htmLawed($_POST["note"], array('safe' => 1));
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

if ($nama == "" || $telp == "" || $fax == "" || $cc == "" || $lokasi == "") {
	$con->close();
	$flash->add("error", "KOSONG", BASE_REFERER);
} else {
	$oke = true;
	$con->beginTransaction();
	$con->clearError();
	if ($act == 'add') {
		$sql = "
			insert into pro_master_terminal(
				nama_terminal, att_terminal, catatan_terminal, tanki_terminal, lokasi_terminal, kategori_terminal, batas_atas, batas_bawah, latitude, longitude, alamat_terminal, telp_terminal, fax_terminal, cc_terminal, is_active, 
				created_time, created_ip, created_by, initial, id_cabang, id_area
			) values (
				'" . $nama . "', '" . json_encode($attention) . "', '" . $note . "', '" . $tanki . "', '" . $lokasi . "', '" . $kategori . "', '" . $batas_atas . "', '" . $batas_bawah . "',  '" . $latitude . "', '" . $longitude . "', '" . $alamat . "', '" . $telp . "', '" . $fax . "', '" . $cc . "', '" . $active . "', 
				NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "', '" . $initial . "', '" . $id_cabang . "', '" . $id_area . "'
			)";
		$msg = "GAGAL_MASUK";
		$idr = $con->setQuery($sql);
		$oke = $oke && !$con->hasError();
	} else if ($act == 'update') {
		$sql = "
			update pro_master_terminal set catatan_terminal = '" . $note . "', att_terminal = '" . json_encode($attention) . "', tanki_terminal = '" . $tanki . "', lokasi_terminal = '" . $lokasi . "', kategori_terminal = '" . $kategori . "', batas_atas = '" . $batas_atas . "', batas_bawah = '" . $batas_bawah . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "',
			alamat_terminal = '" . $alamat . "', nama_terminal = '" . $nama . "', telp_terminal = '" . $telp . "', fax_terminal = '" . $fax . "', cc_terminal = '" . $cc . "', is_active = '" . $active . "', 
			lastupdate_time = NOW(), lastupdate_ip = '" . $_SERVER['REMOTE_ADDR'] . "', lastupdate_by = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "', 
			initial = '" . $initial . "', id_cabang = '" . $id_cabang . "', id_area = '" . $id_area . "' where id_master = " . $idr;
		$msg = "GAGAL_UBAH";
		$con->setQuery($sql);
		$oke = $oke && !$con->hasError();
	}

	if (!is_array_empty($_POST["newdok1"])) {
		foreach ($_POST["newdok1"] as $idx1 => $val1) {
			$newdok1 = htmlspecialchars($_POST["newdok1"][$idx1], ENT_QUOTES);
			$newdok2 = htmlspecialchars($_POST["newdok2"][$idx1], ENT_QUOTES);
			$newdok3 = htmlspecialchars($_FILES['newdok3']['name'][$idx1], ENT_QUOTES);
			if ($newdok1) {
				$sql2 = "insert into pro_master_terminal_detail(id_terminal, dokumen, masa_berlaku, lampiran_ori) values ('" . $idr . "', '" . $newdok1 . "', 
							'" . tgl_db($newdok2) . "', '" . sanitize_filename($newdok3) . "')";
				$idk = $con->setQuery($sql2);
				$oke = $oke && !$con->hasError();

				if ($newdok3) {
					$lampiran = 'dokTerminal_' . $idr . '_' . $idk . '_' . sanitize_filename($newdok3);
					$upload[$idx1] = $lampiran;

					$sql3 = "update pro_master_terminal_detail set lampiran = '" . $lampiran . "' where id_td = '" . $idk . "'";
					$con->setQuery($sql3);
					$oke = $oke && !$con->hasError();
				}
			}
		}
	}

	if (!is_array_empty($_POST["doksup"])) {
		foreach ($_POST["doksup"] as $idx2 => $val2) {
			if (!$_POST["doknya"][$idx2]) {
				$sql4 = "delete from pro_master_terminal_detail where id_td = '" . $idx2 . "'";
				$con->setQuery($sql4);
				$oke = $oke && !$con->hasError();

				$tmpPic = glob($pathfile . "/dokTerminal_" . $idr . "_" . $idx2 . "_*.{jpg,jpeg,gif,png,pdf,zip,rar}", GLOB_BRACE);
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
			header("location: " . BASE_URL_CLIENT . "/master-terminal.php");
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

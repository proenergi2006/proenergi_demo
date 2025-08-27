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
$act	= isset($enk['act']) ? $enk['act'] : htmlspecialchars($_POST["act"], ENT_QUOTES);
$idr	= isset($enk['idr']) ? null : htmlspecialchars($_POST["idr"], ENT_QUOTES);
$idk	= isset($enk['idk']) ? $enk['idk'] : htmlspecialchars($_POST["idk"], ENT_QUOTES);

$id_peminjaman 	= $idk;
$id_mobil 		= $idr;
$id_user 		= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);
$tgl_reservasi 	= htmlspecialchars($_POST["tanggal_reservasi"], ENT_QUOTES);
$tgl_reservasi 	= ($tgl_reservasi ? tgl_db($tgl_reservasi) : '');
$jam_mulai 		= htmlspecialchars($_POST["jam_mulai"], ENT_QUOTES);
$jam_selesai 	= htmlspecialchars($_POST["jam_selesai"], ENT_QUOTES);
$jam_reservasi 	= $jam_mulai . "-" . $jam_selesai;
$keperluan 		= $_POST["keperluan"];
$id_cabang 		= htmlspecialchars($_POST["idc"], ENT_QUOTES);
$id_cabang 		= ($id_cabang ? $id_cabang : paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']));

$last_km 		= htmlspecialchars($_POST["last_km"], ENT_QUOTES);
$bensin 		= htmlspecialchars($_POST["bensin"], ENT_QUOTES);
$last_km 		= ($last_km ? str_replace(array(".", ","), array("", ""), $last_km) : 0);
$bensin 		= ($bensin ? $bensin : 0);

$answer = array();
if ($act == "add") {
	$sqlCek01 = "
			select a.* from (
				select b.fullname as nama_user, a.id_peminjaman,  
				cast(concat(a.tanggal_peminjaman, ' ', a.start_jam_peminjaman) as datetime) as jam_mulai, 
				cast(concat(a.tanggal_peminjaman, ' ', a.end_jam_peminjaman) as datetime) as jam_selesai 
				from pro_peminjaman_mobil a 
				join acl_user b on a.id_user = b.id_user 
				where id_mobil = '" . $idr . "'
			) a 
			where 1=1 
				and ('" . $tgl_reservasi . " " . $jam_mulai . "' between jam_mulai and DATE_ADD(jam_selesai, INTERVAL -1 SECOND) 
				or '" . $tgl_reservasi . " " . $jam_selesai . "' between jam_mulai and DATE_ADD(jam_selesai, INTERVAL -1 SECOND))
		";
	$resCek01 = $con->getRecord($sqlCek01);

	if ($id_mobil == "" || $tgl_reservasi == "" || $jam_mulai == "" || $jam_selesai == "" || $keperluan == "") {
		$con->close();
		$answer = array("error" => true, "pesan" => "Harap isi data dengan benar");
	} else if ($resCek01['id_peminjaman']) {
		$con->close();
		$answer = array("error" => true, "pesan" => "Telah direservasi oleh " . $resCek01['nama_user']);
	} else {
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		$sql1 = "
				insert into pro_peminjaman_mobil(id_mobil, id_user, id_cabang, tanggal_peminjaman, start_jam_peminjaman, end_jam_peminjaman, keperluan, created_time, created_by, deleted_time) values 
				('" . $id_mobil . "', '" . $id_user . "', '" . $id_cabang . "', '" . $tgl_reservasi . "', '" . $jam_mulai . ":00', '" . $jam_selesai . ":00', '" . $keperluan . "', NOW(), 
				'" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "', NULL)
			";
		$res1 = $con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();

		if ($oke) {
			$con->commit();
			$con->close();
			$answer = array("error" => false, "pesan" => "");
		} else {
			$con->rollBack();
			$con->clearError();
			$con->close();
			$answer = array("error" => true, "pesan" => "Maaf data gagal disimpan");
		}
	}
	echo json_encode($answer);
} else if ($act == "update") {
	$sqlCek01 = "
			select a.* from (
				select b.fullname as nama_user, a.id_peminjaman,  
				cast(concat(a.tanggal_peminjaman, ' ', a.start_jam_peminjaman) as datetime) as jam_mulai, 
				cast(concat(a.tanggal_peminjaman, ' ', a.end_jam_peminjaman) as datetime) as jam_selesai 
				from pro_peminjaman_mobil a 
				join acl_user b on a.id_user = b.id_user 
				where id_mobil = '" . $idr . "'
			) a 
			where 1=1 
				and ('" . $tgl_reservasi . " " . $jam_mulai . "' between jam_mulai and DATE_ADD(jam_selesai, INTERVAL -1 SECOND) 
				or '" . $tgl_reservasi . " " . $jam_selesai . "' between jam_mulai and DATE_ADD(jam_selesai, INTERVAL -1 SECOND))
				and id_peminjaman != " . $id_peminjaman . "
		";
	$resCek01 = $con->getRecord($sqlCek01);

	if ($id_mobil == "" || $tgl_reservasi == "" || $jam_mulai == "" || $jam_selesai == "" || $keperluan == "") {
		$con->close();
		$answer = array("error" => true, "pesan" => "Harap isi data dengan benar");
	} else if ($resCek01['id_peminjaman']) {
		$con->close();
		$answer = array("error" => true, "pesan" => "Telah direservasi oleh " . $resCek01['nama_user']);
	} else {
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		$sql1 = "
				update pro_peminjaman_mobil set tanggal_peminjaman = '" . $tgl_reservasi . "', start_jam_peminjaman = '" . $jam_mulai . ":00', end_jam_peminjaman = '" . $jam_selesai . ":00', 
				keperluan = '" . $keperluan . "', bensin = '" . $bensin . "', last_km = '" . $last_km . "',
				created_time = NOW() where id_peminjaman = '" . $id_peminjaman . "'
			";
		$res1 = $con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();

		if ($oke) {
			$con->commit();
			$con->close();
			$answer = array("error" => false, "pesan" => "");
		} else {
			$con->rollBack();
			$con->clearError();
			$con->close();
			$answer = array("error" => true, "pesan" => "Maaf data gagal disimpan");
		}
	}
	echo json_encode($answer);
} else if ($act == 'delete') {
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	$sql1 = "delete from pro_peminjaman_mobil where id_peminjaman = '" . paramDecrypt($id_peminjaman) . "'";
	$res1 = $con->setQuery($sql1);
	$oke  = $oke && !$con->hasError();

	if ($oke) {
		$con->commit();
		$con->close();
		$answer = array("error" => false, "pesan" => "");
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$answer = array("error" => true, "pesan" => "Maaf data gagal dihapus");
	}
	echo json_encode($answer);
}

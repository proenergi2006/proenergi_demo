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

$id_reservasi 	= $idk;
$id_ruangan 	= $idr;
$id_user 		= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);
$tgl_reservasi 	= htmlspecialchars($_POST["tanggal_reservasi"], ENT_QUOTES);
$jam_mulai 		= htmlspecialchars($_POST["jam_mulai"], ENT_QUOTES);
$jam_selesai 	= htmlspecialchars($_POST["jam_selesai"], ENT_QUOTES);
$jam_reservasi 	= $jam_mulai . "-" . $jam_selesai;
$personel 		= $_POST["personel"];
$keperluan 		= $_POST["keperluan"];



$answer = array();
if ($act == "add") {
	$sqlCek01 = "
			select a.* from (
				select b.fullname as nama_user, a.id_reservasi,  
				cast(concat(a.tanggal_reservasi, ' ', substring(a.jam_reservasi, 1, 5)) as datetime) as jam_mulai, 
				cast(concat(a.tanggal_reservasi, ' ', substring(a.jam_reservasi, 7, 5)) as datetime) as jam_selesai 
				from pro_reservasi_ruangan a 
				join acl_user b on a.id_user = b.id_user 
				where id_ruangan = '" . $idr . "'
			) a 
			where 1=1 
				and ('" . tgl_db($tgl_reservasi) . " " . $jam_mulai . "' between jam_mulai and DATE_ADD(jam_selesai, INTERVAL -1 SECOND) 
				or '" . tgl_db($tgl_reservasi) . " " . $jam_selesai . "' between jam_mulai and DATE_ADD(jam_selesai, INTERVAL -1 SECOND))
		";
	$resCek01 = $con->getRecord($sqlCek01);

	if ($id_ruangan == "" || $tgl_reservasi == "" || $jam_mulai == "" || $jam_selesai == "" || $personel == "" || $keperluan == "") {
		$con->close();
		$answer = array("error" => true, "pesan" => "Harap isi data dengan benar");
	} else if ($resCek01['id_reservasi']) {
		$con->close();
		$answer = array("error" => true, "pesan" => "Telah direservasi oleh " . $resCek01['nama_user']);
	} else {
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		$sql1 = "
				insert into pro_reservasi_ruangan(id_ruangan, id_user, tanggal_reservasi, jam_reservasi, personel, keperluan, created_time, created_by, deleted_time) values 
				('" . $id_ruangan . "', '" . $id_user . "', '" . tgl_db($tgl_reservasi) . "', '" . $jam_reservasi . "', '" . $personel . "', '" . $keperluan . "', NOW(), 
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
				select b.fullname as nama_user, a.id_reservasi,  
				cast(concat(a.tanggal_reservasi, ' ', substring(a.jam_reservasi, 1, 5)) as datetime) as jam_mulai, 
				cast(concat(a.tanggal_reservasi, ' ', substring(a.jam_reservasi, 7, 5)) as datetime) as jam_selesai 
				from pro_reservasi_ruangan a 
				join acl_user b on a.id_user = b.id_user 
				where id_ruangan = '" . $idr . "'
			) a 
			where 1=1 
				and ('" . tgl_db($tgl_reservasi) . " " . $jam_mulai . "' between jam_mulai and DATE_ADD(jam_selesai, INTERVAL -1 SECOND) 
				or '" . tgl_db($tgl_reservasi) . " " . $jam_selesai . "' between jam_mulai and DATE_ADD(jam_selesai, INTERVAL -1 SECOND))
				and id_reservasi != " . $id_reservasi . "
		";
	$resCek01 = $con->getRecord($sqlCek01);

	if ($id_ruangan == "" || $tgl_reservasi == "" || $jam_mulai == "" || $jam_selesai == "" || $personel == "" || $keperluan == "") {
		$con->close();
		$answer = array("error" => true, "pesan" => "Harap isi data dengan benar");
	} else if ($resCek01['id_reservasi']) {
		$con->close();
		$answer = array("error" => true, "pesan" => "Telah direservasi oleh " . $resCek01['nama_user']);
	} else {
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		$sql1 = "
				update pro_reservasi_ruangan set tanggal_reservasi = '" . tgl_db($tgl_reservasi) . "', jam_reservasi = '" . $jam_reservasi . "', personel = '" . $personel . "', keperluan = '" . $keperluan . "',
				created_time = NOW() where id_reservasi = '" . $id_reservasi . "'
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

	$sql1 = "delete from pro_reservasi_ruangan where id_reservasi = '" . paramDecrypt($id_reservasi) . "'";
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

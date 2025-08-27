<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$conSub = new Connection();
$q1 	= htmlspecialchars($_POST["q1"], ENT_QUOTES);

$sqlnya01 = "select id_mobil, plat_mobil, concat(nama_mobil, ' ', plat_mobil) as nama_mobil, id_cabang, attach_foto from pro_master_mobil where is_active = 1";
if ($q1 == '1' || $q1 == '2') {
	$sqlnya01 .= " and (id_cabang = '1' or id_cabang = '2')";
} else {
	$sqlnya01 .= " and id_cabang = '" . $q1 . "'";
}
$resnya01 = $conSub->getResult($sqlnya01);
$conSub->close();

$hasilnya = '<option></option>';
$pathFile = $public_base_directory . '/files/uploaded_user/lampiran';

if (count($resnya01) > 0) {
	foreach ($resnya01 as $idx01 => $data01) {
		$gambarnya = "";
		if ($data01['attach_foto'] && file_exists($pathFile . $data01['attach_foto'])) {
			$gambarnya = $data01['attach_foto'];
		}

		$hasilnya .= '<option value="' . $data01['id_mobil'] . '" data-cabang="' . $data01['id_cabang'] . '" data-mobilnya="' . $gambarnya . '" data-nopol="' . $data01['plat_mobil'] . '">' . $data01['nama_mobil'] . '</option>';
	}
}
echo $hasilnya;

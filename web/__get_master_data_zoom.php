<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$conSub = new Connection();
$q1 	= htmlspecialchars($_POST["q1"], ENT_QUOTES);

$sqlnya01 = "select id_zoom, nama_zoom, id_cabang from pro_master_zoom where is_active = 1";

$sqlnya01 .= " and id_cabang = '" . $q1 . "'";

$resnya01 = $conSub->getResult($sqlnya01);
$conSub->close();

$hasilnya = '<option></option>';

if (count($resnya01) > 0) {
	foreach ($resnya01 as $idx01 => $data01) {

		echo '<option value="' . $data01['id_zoom'] . '" data-cabang="' . $data01['id_cabang'] . '">' . $data01['nama_zoom'] . '</option>';
	}
}
echo $hasilnya;

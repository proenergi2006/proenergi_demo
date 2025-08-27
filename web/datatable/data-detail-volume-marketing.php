<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$con 	= new Connection();

$id_mkt	= isset($_POST["id_mkt"]) ? htmlspecialchars($_POST["id_mkt"], ENT_QUOTES) : '';
$bulan	= isset($_POST["bulan"]) ? htmlspecialchars($_POST["bulan"], ENT_QUOTES) : '';
$tahun	= isset($_POST["tahun"]) ? htmlspecialchars($_POST["tahun"], ENT_QUOTES) : '';
$wilayah	= isset($_POST["wilayah"]) ? htmlspecialchars($_POST["wilayah"], ENT_QUOTES) : '';
$q1 = isset($_POST["q1"]) ? $_POST["q1"] : '';

if (!empty($q1)) {
	$q1_array = explode(',', $q1);
	// Jika $q1 adalah array, lakukan pemrosesan
	$q1_imploded = implode("','", array_map('htmlspecialchars', $q1_array));
	$filter = "AND a.id_customer IN ('" . $q1_imploded . "')";
} else {
	// Jika $q1 kosong atau tidak valid
	$filter = "";  // Atau bisa kosongkan sesuai kebutuhan
}
// if ($q1) {
// 	$filter = "AND upper(b.nama_customer) LIKE '%" . strtoupper($q1) . "%'";
// } else {
// 	$filter = "";
// }

$sql = "SELECT a.id_poc, a.id_customer, b.id_marketing, b.nama_customer, c.realisasi_kirim, c.volume_kirim, a.tanggal_poc, c.tanggal_kirim, a.nomor_poc, d.pr_mobil
FROM pro_po_customer as a 
JOIN pro_customer as b ON a.id_customer=b.id_customer 
JOIN pro_po_customer_plan as c ON a.id_poc=c.id_poc 
JOIN (SELECT id_plan, pr_mobil FROM pro_pr_detail GROUP BY id_plan) as d ON c.id_plan=d.id_plan
JOIN pro_penawaran e ON e.id_penawaran=a.id_penawaran
WHERE a.poc_approved = 1 AND c.status_plan = 1 AND b.id_marketing = '" . $id_mkt . "' AND MONTH(c.tanggal_kirim) = '" . $bulan . "' AND YEAR(c.tanggal_kirim) = '" . $tahun . "' " . $filter . " ORDER BY c.tanggal_kirim DESC";
$result = $con->getResult($sql);

$sql02 = "SELECT fullname FROM acl_user WHERE id_user='" . $id_mkt . "'";
$result02 = $con->getRecord($sql02);

$sql03 = "SELECT a.id_customer, b.nama_customer
FROM pro_po_customer as a 
JOIN pro_customer as b ON a.id_customer=b.id_customer 
JOIN pro_po_customer_plan as c ON a.id_poc=c.id_poc 
JOIN (SELECT id_plan, pr_mobil FROM pro_pr_detail GROUP BY id_plan) as d ON c.id_plan=d.id_plan
JOIN pro_penawaran e ON e.id_penawaran=a.id_penawaran
WHERE a.poc_approved = 1 AND c.status_plan = 1 AND b.id_marketing = '" . $id_mkt . "' AND MONTH(c.tanggal_kirim) = '" . $bulan . "' AND YEAR(c.tanggal_kirim) = '" . $tahun . "' GROUP BY a.id_customer ORDER BY c.tanggal_kirim DESC";
$result03 = $con->getResult($sql03);

switch ($bulan) {
	case "01":
		$bulan = "Januari" . " " . $tahun;
		break;
	case "02":
		$bulan = "Februari" . " " . $tahun;
		break;
	case "03":
		$bulan = "Maret" . " " . $tahun;
		break;
	case "04":
		$bulan = "April" . " " . $tahun;
		break;
	case "05":
		$bulan = "Mei" . " " . $tahun;
		break;
	case "06":
		$bulan = "Juni" . " " . $tahun;
		break;
	case "07":
		$bulan = "Juli" . " " . $tahun;
		break;
	case "08":
		$bulan = "Agustus" . " " . $tahun;
		break;
	case "09":
		$bulan = "September" . " " . $tahun;
		break;
	case "10":
		$bulan = "Oktober" . " " . $tahun;
		break;
	case "11":
		$bulan = "November" . " " . $tahun;
		break;
	case "12":
		$bulan = "Desember" . " " . $tahun;
		break;
}

$data = [
	"data" 		=> $result,
	"nama_mkt" 	=> $result02['fullname'],
	"bulan" 	=> $bulan,
	"customer" 	=> $result03,
];

echo json_encode($data);

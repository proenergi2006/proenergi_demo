<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$con 	= new Connection();

$id_mkt	= isset($_POST["id_mkt"]) ? htmlspecialchars($_POST["id_mkt"], ENT_QUOTES) : '';
$id_wilayah	= isset($_POST["wilayah"]) ? htmlspecialchars($_POST["wilayah"], ENT_QUOTES) : '';
$bulan	= isset($_POST["bulan"]) ? htmlspecialchars($_POST["bulan"], ENT_QUOTES) : '';
$tahun	= isset($_POST["tahun"]) ? htmlspecialchars($_POST["tahun"], ENT_QUOTES) : '';

$sql = "SELECT pc.id_customer, pc.nama_customer, ppc.tanggal_poc, pc.id_marketing, ppc.id_poc, ppcp.id_plan,
(
	SELECT COUNT(DISTINCT a.id_customer) FROM pro_customer a
	JOIN pro_po_customer b ON a.id_customer=b.id_customer
	JOIN pro_po_customer_plan c ON b.id_poc=c.id_poc
	JOIN pro_penawaran d ON d.id_penawaran=b.id_penawaran
	JOIN pro_master_cabang e on d.id_cabang = e.id_master
	WHERE d.id_cabang='" . $id_wilayah . "' AND a.id_marketing=pc.id_marketing AND ppc.tanggal_poc = b.tanggal_poc AND a.id_customer=pc.id_customer AND
	(
		CASE
		WHEN MONTH(ppc.tanggal_poc) = '" . $bulan . "' AND YEAR(ppc.tanggal_poc) = '" . $tahun . "' THEN 1
		ELSE 0
		END
	)
) AS total
FROM pro_customer pc 
JOIN pro_po_customer ppc ON pc.id_customer=ppc.id_customer 
JOIN pro_po_customer_plan ppcp ON ppcp.id_poc=ppc.id_poc
JOIN pro_penawaran pp ON pp.id_penawaran=ppc.id_penawaran
JOIN pro_master_cabang pmc on pp.id_cabang = pmc.id_master
WHERE pp.id_cabang = '" . $id_wilayah . "' AND pc.id_marketing='" . $id_mkt . "' AND ppcp.realisasi_kirim != 0 AND pc.is_verified = '1' AND (pc.status_customer = '2' OR pc.status_customer = '3') GROUP BY pc.id_customer;";
$result = $con->getResult($sql);

$arrayIdPlan = [];
if (!empty($result)) {
	foreach ($result as $key) {
		if ($key['total'] == 1) {
			array_push($arrayIdPlan, $key['id_plan']);
		}
	}
}

$arrayCust = [];
if (!empty($arrayIdPlan)) {
	foreach ($arrayIdPlan as $val) {
		$sql02 = "SELECT a.id_plan, b.tanggal_poc, c.id_customer, c.nama_customer, a.realisasi_kirim FROM pro_po_customer_plan a
			JOIN pro_po_customer b ON a.id_poc=b.id_poc
			JOIN pro_customer c ON b.id_customer=c.id_customer
			WHERE a.id_plan='" . $val . "'";
		$res = $con->getRecord($sql02);
		// array_push($arrayCust, $res['nama_customer'], $res['tanggal_poc']);
		array_push($arrayCust, (object)[
			"nama_customer" => $res['nama_customer'],
			"tgl_poc" => $res['tanggal_poc'],
			"volume" => $res['realisasi_kirim'],
		]);
	}
}

$sql03 = "SELECT fullname FROM acl_user WHERE id_user='" . $id_mkt . "'";
$result03 = $con->getRecord($sql03);

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
	"data" 		=> $arrayCust,
	"nama_mkt" 	=> $result03['fullname'],
	"bulan" 	=> $bulan,
];

echo json_encode($data);

<?php
session_start();

$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth = new MyOtentikasi();
$con = new Connection();


$q4   = isset($_GET["q4"]) ? htmlspecialchars($_GET["q4"], ENT_QUOTES) : '';
$selectBulan   = isset($_GET["selectBulan"]) ? htmlspecialchars($_GET["selectBulan"], ENT_QUOTES) : '';
$selectTahun   = isset($_GET["selectTahun"]) ? htmlspecialchars($_GET["selectTahun"], ENT_QUOTES) : '';

$year = date('Y');
$month = date('m');


// Pastikan format tanggal valid (opsional untuk keamanan)
// $start = date('Y-m-d', strtotime($start));
// $end   = date('Y-m-d', strtotime($end));

$sql = "
    SELECT
        DATE_FORMAT(a.tgl_approved, '%Y-%m') AS bulan,
        SUM(CASE WHEN a.id_transportir IN (1,2,3,4,5,28,63,105,180,99) THEN 1 ELSE 0 END) AS pro_energi,
        SUM(CASE WHEN a.id_transportir NOT IN (1,2,3,4,5,28,63,105,180,99) THEN 1 ELSE 0 END) AS thirdparty,
        SUM(CASE WHEN a.id_transportir IN (1,2,3,4,5,28,63,105,180,99) THEN b.volume_po ELSE 0 END) AS pro_energi_volume,
        SUM(CASE WHEN a.id_transportir NOT IN (1,2,3,4,5,28,63,105,180,99) THEN b.volume_po ELSE 0 END) AS thirdparty_volume
    FROM pro_po a
    JOIN pro_po_detail b on a.id_po = b.id_po
    JOIN pro_master_cabang c ON a.id_wilayah = c.id_master
    WHERE 1=1
";

if ($q4 != '') {
    $sql .= " and c.id_master = '" . $q4 . "'";
}

// if ($selectTahun != ""){
//     $sql .= " and YEAR(a.tgl_approved) = '" .$selectTahun. "'";
// }
// if ($selectBulan != "" ){
//     $sql .= " and MONTH(a.tgl_approved) = '".$selectBulan."'";
// }

if ($selectTahun != "" && $selectBulan != "") {
    $selectedDate = date('Y-m-01', strtotime("$selectTahun-$selectBulan-01"));

    // 3 bulan sebelum dan 3 bulan sesudah
    $startDate = date('Y-m-01', strtotime('-3 months', strtotime($selectedDate)));
    $endDate   = date('Y-m-t', strtotime('+3 months', strtotime($selectedDate)));

    $sql .= " AND a.tgl_approved BETWEEN '$startDate' AND '$endDate'";
} else {
    // Jika tidak ada pilihan, pakai 6 bulan ke belakang default
    $startDate = date('Y-m-01', strtotime('-3 months'));
    $endDate   = date('Y-m-t');
    $sql .= " AND a.tgl_approved BETWEEN '$startDate' AND '$endDate'";
}


$sql .= "   GROUP BY DATE_FORMAT(a.tgl_approved, '%Y-%m')
    ORDER BY DATE_FORMAT(a.tgl_approved, '%Y-%m')";

$result = $con->getResult($sql);

$data = [
    'labels'       => [],
    'proEnergi'    => [],
    'thirdParty'   => [],
    'pro_energi_volume' => [],
    'thirdparty_volume' => []

];

foreach ($result as $row) {
    $label = date('F Y', strtotime($row['bulan']));
    $data['labels'][]     = $label;
    $data['proEnergi'][]  = (int)$row['pro_energi'];
    $data['thirdParty'][] = (int)$row['thirdparty'];
    $data['pro_energi_volume'][] = (int)$row['pro_energi_volume'];
    $data['thirdparty_volume'][] = (int)$row['thirdparty_volume'];
}

$data_result[] = $data;
// header('Content-Type: application/json');
echo json_encode($data_result);

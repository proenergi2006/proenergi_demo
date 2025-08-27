<?php
// header('Content-Type: application/json');
session_start();

$privat_base_directory  = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory  = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth = new MyOtentikasi();
$con = new Connection();

$q4   = isset($_GET["q4"]) ? htmlspecialchars($_GET["q4"], ENT_QUOTES) : '';
$selectBulan   = isset($_GET["selectBulan"]) ? htmlspecialchars($_GET["selectBulan"], ENT_QUOTES) : '';
$selectTahun   = isset($_GET["selectTahun"]) ? htmlspecialchars($_GET["selectTahun"], ENT_QUOTES) : '';

$year = date('Y');
$month = date('m');

// Query per cabang
$sql = "
 SELECT
  c.nama_cabang AS branch,
  SUM(CASE WHEN a.id_transportir IN (1,2,3,4,5,28,63,105,180,99) THEN 1 ELSE 0 END) AS pro_energi,
  SUM(CASE WHEN a.id_transportir NOT IN (1,2,3,4,5,28,63,105,180,99) THEN 1 ELSE 0 END) AS thirdparty
FROM pro_po a
JOIN pro_po_detail b on a.id_po = b.id_po
JOIN pro_master_cabang c ON a.id_wilayah = c.id_master
WHERE 1=1
";

if ($q4 != '') {
    $sql .= " and c.id_master = '" . $q4 . "'";
}

if ($selectTahun != "") {
    $sql .= " and YEAR(a.tgl_approved) = '" . $selectTahun . "'";
}
if ($selectBulan != "") {
    $sql .= " and MONTH(a.tgl_approved) = '" . $selectBulan . "'";
}

$sql .= " GROUP BY c.nama_cabang
        ORDER BY c.nama_cabang";

$result = $con->getResult($sql);

$labels = [];
$dataSets = [];

foreach ($result as $row) {
    if (!in_array($row['branch'], $labels)) {
        $labels[] = $row['branch'];
    }
}

$cabangKategori = []; // untuk simpan sementara

foreach ($result as $row) {
    $key1 = 'Pro Energi';
    $key2 = 'Third Party';

    $cabangKategori[$key1][$row['branch']] = (int)$row['pro_energi'];
    $cabangKategori[$key2][$row['branch']] = (int)$row['thirdparty'];
}

// Bangun dataset untuk Chart.js
foreach ($cabangKategori as $label => $dataPerTgl) {
    $data = [];
    foreach ($labels as $tgl) {
        $data[] = isset($dataPerTgl[$tgl]) ? $dataPerTgl[$tgl] : 0;
    }
    $dataSets[] = [
        'label' => $label,
        'data' => $data
    ];
}

$output[] = [
    'labels' => $labels,
    'datasets' => $dataSets
];

echo json_encode($output);

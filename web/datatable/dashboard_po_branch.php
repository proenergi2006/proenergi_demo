<?php
header('Content-Type: application/json');
session_start();

$privat_base_directory  = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory  = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth = new MyOtentikasi();
$con = new Connection();

// Ambil parameter tanggal dari GET
$dateFrom = isset($_GET['start']) && !empty($_GET['start'])
  ? date('Y-m-d', strtotime($_GET['start']))
  : date('Y-m-d', strtotime('-6 days'));

$dateTo = isset($_GET['end']) && !empty($_GET['end'])
  ? date('Y-m-d', strtotime($_GET['end']))
  : date('Y-m-d');

$branches = isset($_GET['branch']) && !empty($_GET['branch'])
  ? $_GET['branch']
  : [4]; // default Samarinda

// Sanitasi format tanggal
$dateFrom = date('Y-m-d', strtotime($dateFrom));
$dateTo   = date('Y-m-d', strtotime($dateTo));

$whereCabang = '';
if (!empty($branches)) {
  // pastikan array dan hanya angka
  $escaped = array_map('intval', $branches);
  $inList = implode(',', $escaped);
  $whereCabang = " AND b.id_master IN ($inList)";
}


// Query per cabang
$sql = "
 SELECT
  DATE(a.tgl_approved) AS tgl,
  DATE_FORMAT(a.tgl_approved, '%e %M %Y') AS tanggal,
  b.nama_cabang AS branch,
  SUM(CASE WHEN a.id_transportir IN (1,2,3,4,5,28,63,105,180,99) THEN 1 ELSE 0 END) AS pro_energi,
  SUM(CASE WHEN a.id_transportir NOT IN (1,2,3,4,5,28,63,105,180,99) THEN 1 ELSE 0 END) AS thirdparty
FROM pro_po a
JOIN pro_master_cabang b ON a.id_wilayah = b.id_master
WHERE DATE(a.tgl_approved) BETWEEN '$dateFrom' AND '$dateTo'
$whereCabang
GROUP BY tgl, b.nama_cabang
ORDER BY tgl, b.nama_cabang
";

$result = $con->getResult($sql);

$labels = [];
$dataSets = [];

foreach ($result as $row) {
  if (!in_array($row['tanggal'], $labels)) {
    $labels[] = $row['tanggal'];
  }
}

$cabangKategori = []; // untuk simpan sementara

foreach ($result as $row) {
  $tgl = $row['tanggal'];

  $key1 = $row['branch'] . ' - Pro Energi';
  $key2 = $row['branch'] . ' - Third Party';

  $cabangKategori[$key1][$tgl] = (int)$row['pro_energi'];
  $cabangKategori[$key2][$tgl] = (int)$row['thirdparty'];
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

$output = [
  'labels' => $labels,
  'datasets' => $dataSets
];

echo json_encode($output);

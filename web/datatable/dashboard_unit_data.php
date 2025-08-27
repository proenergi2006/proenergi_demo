<?php
session_start();

$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth = new MyOtentikasi();
$con = new Connection();

// Ambil parameter tanggal dari GET
$start = isset($_GET['start']) && !empty($_GET['start'])
    ? $_GET['start']
    : date('Y-m-d', strtotime('-6 days'));

$end = isset($_GET['end']) && !empty($_GET['end'])
    ? $_GET['end']
    : date('Y-m-d');


// Pastikan format tanggal valid (opsional untuk keamanan)
$start = date('Y-m-d', strtotime($start));
$end   = date('Y-m-d', strtotime($end));

$sql = "
    SELECT
        DATE_FORMAT(a.tgl_approved, '%e %M %Y') AS tanggal,
        SUM(CASE WHEN a.id_transportir IN (1,2,3,4,5,28,63,105,180,99) THEN 1 ELSE 0 END) AS pro_energi,
        SUM(CASE WHEN a.id_transportir NOT IN (1,2,3,4,5,28,63,105,180,99) THEN 1 ELSE 0 END) AS thirdparty,
        SUM(CASE WHEN a.id_transportir IN (1,2,3,4,5,28,63,105,180,99) THEN b.volume_po ELSE 0 END) AS pro_energi_volume,
        SUM(CASE WHEN a.id_transportir NOT IN (1,2,3,4,5,28,63,105,180,99) THEN b.volume_po ELSE 0 END) AS thirdparty_volume
    FROM pro_po a
    JOIN pro_po_detail b on a.id_po = b.id_po
    WHERE DATE(a.tgl_approved) BETWEEN '$start' AND '$end'
    GROUP BY DATE(a.tgl_approved)
    ORDER BY DATE(a.tgl_approved)
";

$result = $con->getResult($sql);

$data = [
    'labels'       => [],
    'proEnergi'    => [],
    'thirdParty'   => [],
    'pro_energi_volume' => [],
    'thirdparty_volume' => []

];

foreach ($result as $row) {
    $data['labels'][]     = $row['tanggal'];
    $data['proEnergi'][]  = (int)$row['pro_energi'];
    $data['thirdParty'][] = (int)$row['thirdparty'];
    $data['pro_energi_volume'][] = (int)$row['pro_energi_volume'];
    $data['thirdparty_volume'][] = (int)$row['thirdparty_volume'];
}

header('Content-Type: application/json');
echo json_encode($data);

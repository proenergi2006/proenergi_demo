<?php

$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$con = new Connection();


$cabang = isset($_POST['cabang']) ? addslashes($_POST['cabang']) : '';
$day    = isset($_POST['day'])    ? (int)$_POST['day']        : 0;
$month  = isset($_POST['month'])  ? $_POST['month']   : date('Y-m');

$startDate = $month . '-01';
$endDate   = date('Y-m-t', strtotime($startDate));

$sql = "
  SELECT
    b.nomor_pr,
    DATE(a.tanggal_loaded)      AS tanggal,
    DATE(g.masa_awal)        AS tanggal_awal,
    DATE(g.masa_akhir)      AS tanggal_akhir,
    c.nomor_lo_pr               AS nomor_lo,
    c.volume                    AS volume,
    i.nama_terminal AS terminal
FROM pro_po_ds_detail a
JOIN pro_pr             b ON a.id_pr         = b.id_pr
JOIN pro_pr_detail      c ON b.id_pr         = c.id_pr
JOIN pro_master_cabang  d ON b.id_wilayah    = d.id_master
JOIN pro_po_customer_plan e ON a.id_plan     = e.id_plan
JOIN pro_po_customer f ON a.id_poc     = f.id_poc
JOIN pro_penawaran g ON f.id_penawaran     = g.id_penawaran
JOIN pro_po_ds h ON a.id_ds = h.id_ds
JOIN pro_master_terminal i ON h.id_terminal = i.id_master
WHERE d.nama_cabang            = '$cabang'
  AND DAY(a.tanggal_loaded)    = $day
  AND a.tanggal_loaded BETWEEN '$startDate' AND '$endDate'
  AND a.is_loaded = 1
  AND a.is_cancel != 1
ORDER BY a.tanggal_loaded, b.nomor_pr
";

// 4) Jalankan query
$rows = $con->getResult($sql);  // mengembalikan array associative

// 5) Keluarkan JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($rows);
exit;

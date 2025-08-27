<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth    = new MyOtentikasi();
$con     = new Connection();
// Dekripsi session untuk mendapatkan id_wilayah
$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$where = " c.id_wilayah = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]) . "'";
// $q2    = isset($_GET["q2"]) ? htmlspecialchars($_GET["q2"], ENT_QUOTES) : '';
// $q3    = isset($_GET["q3"]) ? htmlspecialchars($_GET["q3"], ENT_QUOTES) : '';
$q4   = isset($_GET["q4"]) ? htmlspecialchars($_GET["q4"], ENT_QUOTES) : '';
$selectBulan   = isset($_GET["selectBulan"]) ? htmlspecialchars($_GET["selectBulan"], ENT_QUOTES) : '';
$selectTahun   = isset($_GET["selectTahun"]) ? htmlspecialchars($_GET["selectTahun"], ENT_QUOTES) : '';

$year = date('Y');
$month = date('m');
$bulan = '';
$tahun = '';
if ($selectBulan != "") {
    $bulan = $selectBulan;
} else {
    $bulan = $month;
}

if ($selectTahun != "") {
    $tahun = $selectTahun;
} else {
    $tahun = $year;
}

// Query untuk mengambil data volume dan tanggal_loaded
$sql_cust = "SELECT 
                mc.nama_cabang,

                -- Total active customer (yang realisasi_kirim di bulan & tahun tertentu)
                COUNT(DISTINCT c.id_customer) AS total_active_customer,

                -- Total new customer (yang pertama kali PO-nya di bulan & tahun yang difilter)
                COUNT(DISTINCT CASE 
                    WHEN MONTH(first_po.tanggal_poc) = '$bulan' AND YEAR(first_po.tanggal_poc) = '$tahun'
                    THEN c.id_customer
                END) AS total_new_customer

                FROM pro_customer c
                JOIN pro_po_customer poc ON c.id_customer = poc.id_customer
                JOIN pro_po_customer_plan plan ON poc.id_poc = plan.id_poc
                JOIN pro_penawaran p ON p.id_penawaran = poc.id_penawaran
                JOIN pro_master_cabang mc ON p.id_cabang = mc.id_master

                -- Subquery tanggal PO pertama
                LEFT JOIN (
                SELECT id_customer, MIN(tanggal_poc) AS tanggal_poc
                FROM pro_po_customer
                GROUP BY id_customer
                ) AS first_po ON first_po.id_customer = c.id_customer

                WHERE 
                c.status_customer IN ('2','3') AND
                c.is_verified = '1' AND
                plan.realisasi_kirim != 0 
                ";


if ($q4 != '') {
    $sql_cust .= " and a.id_cabang = '" . $q4 . "'";
}

if ($selectBulan != "") {
    $sql_cust .= " and MONTH(poc.tanggal_poc) = '" . $selectBulan . "'";
}

if ($selectTahun != "") {
    $sql_cust .= " and YEAR(poc.tanggal_poc) = '" . $selectTahun . "'";
}


$sql_cust .= "   GROUP BY mc.id_master
                ORDER BY mc.nama_cabang";

// Menjalankan query dan mendapatkan hasilnya
$result = $con->getResult($sql_cust);


$labels = [];
$dataSets = [];

foreach ($result as $row) {
    if (!in_array($row['nama_cabang'], $labels)) {
        $labels[] = $row['nama_cabang'];
    }
}

$cabangKategori = []; // untuk simpan sementara

foreach ($result as $row) {
    $key1 = 'Active Customer';
    $key2 = 'New Customer';

    $cabangKategori[$key1][$row['nama_cabang']] = (int)$row['total_active_customer'];
    $cabangKategori[$key2][$row['nama_cabang']] = (int)$row['total_new_customer'];
}

// Bangun dataset untuk Chart.js
foreach ($cabangKategori as $label => $dataperbulan) {
    $data = [];
    foreach ($labels as $cbg) {
        $data[] = isset($dataperbulan[$cbg]) ? $dataperbulan[$cbg] : 0;
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

// Mengirimkan data dalam format JSON
echo json_encode($output);

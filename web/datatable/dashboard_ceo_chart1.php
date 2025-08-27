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

$year = date('Y');
$date = time(); // current date
$week = date("W", $date);
// Query untuk mengambil data volume dan tanggal_loaded
$sql = "select 
    YEAR(a.tanggal_kirim) AS tahun,
    MONTHNAME(a.tanggal_kirim) AS bulan,
    WEEK(a.tanggal_kirim, 1) AS minggu,  -- Menggunakan WEEK() untuk mendapatkan nomor minggu
    d.nama_cabang,
    SUM(a.volume_kirim) AS total_volume
FROM 
    pro_po_customer_plan a
JOIN 
    pro_po_customer b ON a.id_poc = b.id_poc
JOIN 
    pro_customer c ON b.id_customer = c.id_customer
JOIN 
    pro_master_cabang d ON c.id_wilayah = d.id_master
WHERE 
    YEAR(a.tanggal_kirim) = '" . $year . "'  -- Tahun saat ini
    AND WEEK(a.tanggal_kirim, 1) = '" . $week . "' -- Minggu yang sedang berjalan
    AND a.status_plan = 1  -- Filter status plan
GROUP BY 
    YEAR(a.tanggal_kirim), MONTH(a.tanggal_kirim), WEEK(a.tanggal_kirim, 1), d.nama_cabang  -- Kelompokkan berdasarkan tahun, bulan, minggu, dan cabang
ORDER BY 
    d.nama_cabang

		";

// Menjalankan query dan mendapatkan hasilnya
$result = $con->getResult($sql);

// Siapkan data dalam format array untuk JSON
$data = [];
foreach ($result as $row) {
    $data[] = [
        'volume' => $row['total_volume'],
        'cabang' => $row['nama_cabang'],
        'minggu' => $row['minggu']
    ];
}

// Mengirimkan data dalam format JSON
echo json_encode($data);

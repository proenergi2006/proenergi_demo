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
// $where = " c.id_wilayah = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]) . "'";
$q4   = isset($_GET["q4"]) ? htmlspecialchars($_GET["q4"], ENT_QUOTES) : '';
$selectBulan   = isset($_GET["selectBulan"]) ? htmlspecialchars($_GET["selectBulan"], ENT_QUOTES) : '';
$selectTahun   = isset($_GET["selectTahun"]) ? htmlspecialchars($_GET["selectTahun"], ENT_QUOTES) : '';

$year = date('Y');
$month = date('m');
// Query untuk mengambil data volume dan tanggal_loaded
// $sql = "select 
//     MONTHNAME(a.tanggal_kirim) AS bulan, 
//     d.nama_cabang,
//     SUM(a.volume_kirim) AS total_volume
//         FROM 
//             pro_po_customer_plan a
//         JOIN 
//             pro_po_customer b ON a.id_poc = b.id_poc
//         JOIN 
//             pro_customer c ON b.id_customer = c.id_customer
//         JOIN 
//             pro_master_cabang d ON c.id_wilayah = d.id_master
//         WHERE 
//             YEAR(a.tanggal_kirim) = '".$year."'  -- Tahun yang diinginkan
//             AND MONTH(a.tanggal_kirim) ='".$month."' -- Bulan September
//             AND a.status_plan = 1  -- Filter status plan
//         GROUP BY 
//             d.nama_cabang, MONTH(a.tanggal_kirim)  -- Pastikan mengelompokkan berdasarkan bulan dan cabang
//         ORDER BY 
//             d.nama_cabang
// 		";
$sql = "select 
    MONTHNAME(a.tanggal_kirim) AS bulan, 
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
        JOIN 
            pro_po_ds_detail e ON a.id_plan = e.id_plan
        WHERE a.status_plan = 1 AND e.is_delivered = 1
		";

if ($q4 != '') {
    $sql .= " and c.id_wilayah = '" . $q4 . "'";
}

if ($selectBulan != "") {
    $sql .= " and MONTH(a.tanggal_kirim) = '" . $selectBulan . "'";
}

if ($selectTahun != "") {
    $sql .= " and YEAR(a.tanggal_kirim) = '" . $selectTahun . "'";
}

$sql .= "  GROUP BY 
            d.nama_cabang, MONTH(a.tanggal_kirim)  -- Pastikan mengelompokkan berdasarkan bulan dan cabang
        ORDER BY 
            d.nama_cabang";
// Menjalankan query dan mendapatkan hasilnya
$result = $con->getResult($sql);

// Siapkan data dalam format array untuk JSON
$data = [];
foreach ($result as $row) {
    $data[] = [
        'volume' => $row['total_volume'],
        'cabang' => $row['nama_cabang'],
        'bulan' => $row['bulan']
    ];
}



// Mengirimkan data dalam format JSON
echo json_encode($data);

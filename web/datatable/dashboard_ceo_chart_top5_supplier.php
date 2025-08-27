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
// Query untuk mengambil data volume dan tanggal_loaded
$sql = "select
    CONCAT(
        LEFT(SUBSTRING_INDEX(c.nama_vendor, ' ', 1), 2), 
        UPPER(
            CONCAT(
                LEFT(SUBSTRING_INDEX(SUBSTRING_INDEX(c.nama_vendor, ' ', 2), ' ', -1), 1), -- Inisial kata kedua
                LEFT(SUBSTRING_INDEX(SUBSTRING_INDEX(c.nama_vendor, ' ', 3), ' ', -1), 1), -- Inisial kata ketiga
                LEFT(SUBSTRING_INDEX(SUBSTRING_INDEX(c.nama_vendor, ' ', 4), ' ', -1), 1) -- Inisial kata keempat
            )
        )
            ) AS inisial_vendor,
             c.nama_vendor,
    MONTHNAME(a.tgl_terima) AS bulan, 
    SUM(a.volume_terima) AS total_volume
FROM 
   new_pro_inventory_vendor_po_receive a
JOIN 
    new_pro_inventory_vendor_po b ON a.id_po_supplier = b.id_master
JOIN 
    pro_master_vendor c ON b.id_vendor = c.id_master
JOIN 
    pro_master_terminal d ON b.id_terminal = d.id_master
WHERE 1=1 
";

if ($q4 != '') {
    $sql .= " and d.id_cabang = '" . $q4 . "'";
}

if ($selectTahun != "") {
    $sql .= " and YEAR(a.tgl_terima) = '" . $selectTahun . "'";
}
if ($selectBulan != "") {
    $sql .= " and MONTH(a.tgl_terima) = '" . $selectBulan . "'";
}

// if ($q2 != "" && $q3 != "" ){
//     $sql .= " and DATE(a.tgl_terima) between '" . tgl_db($q2) . "' and '" . tgl_db($q3) . "'";
// }else{
//     $sql .= "and YEAR(a.tgl_terima) = '" .$year. "' AND MONTH(a.tgl_terima) = '".$month."'";
// }

$sql .= "GROUP BY 
            c.nama_vendor, MONTH(a.tgl_terima)
        ORDER BY 
            total_volume DESC
        LIMIT 5";

// Menjalankan query dan mendapatkan hasilnya
$result = $con->getResult($sql);

// Siapkan data dalam format array untuk JSON
$data = [];
foreach ($result as $row) {
    $data[] = [
        'vendor_det' => $row['nama_vendor'],
        'vendor' => $row['inisial_vendor'],
        'volume' => $row['total_volume'],
        'bulan' => $row['bulan']
    ];
}



// Mengirimkan data dalam format JSON
echo json_encode($data);

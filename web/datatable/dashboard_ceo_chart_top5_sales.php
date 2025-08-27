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
						e.fullname,
            MONTHNAME(a.tanggal_kirim) AS bulan, 
            SUM(a.volume_kirim) AS total_volume
        FROM 
            pro_po_customer_plan a
        JOIN 
            pro_po_customer b ON a.id_poc = b.id_poc
        JOIN 
            pro_penawaran c ON b.id_penawaran = c.id_penawaran
        JOIN 
            pro_customer  d ON c.id_customer = d.id_customer
				JOIN 
            acl_user  e ON d.id_marketing = e.id_user
        WHERE 
            a.status_plan = 1
				";

if ($q4 != '') {
    $sql .= " and d.id_wilayah = '" . $q4 . "'";
}

if ($selectBulan != "") {
    $sql .= " and MONTH(a.tanggal_kirim) = '" . $selectBulan . "'";
}

if ($selectTahun != "") {
    $sql .= " and YEAR(a.tanggal_kirim) = '" . $selectTahun . "'";
}
// if ($q2 != "" && $q3 != "" ){
//     $sql .= " and a.tanggal_kirim between '" . tgl_db($q2) . "' and '" . tgl_db($q3) . "'";
// }else{
//     $sql .= "and YEAR(a.tanggal_kirim) = '" .$year. "' AND MONTH(a.tanggal_kirim) = '".$month."'";
// }

$sql .= "GROUP BY 
            e.fullname, MONTH(a.tanggal_kirim)
        ORDER BY 
            total_volume DESC
        LIMIT 5";

// Menjalankan query dan mendapatkan hasilnya
$result = $con->getResult($sql);

// Siapkan data dalam format array untuk JSON
$data = [];
foreach ($result as $row) {
    $data[] = [
        'sales' => $row['fullname'],
        'volume' => $row['total_volume'],
        'bulan' => $row['bulan']
    ];
}



// Mengirimkan data dalam format JSON
echo json_encode($data);

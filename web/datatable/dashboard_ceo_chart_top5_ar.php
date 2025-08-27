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
$sql = "
		SELECT 
        CONCAT(
            LEFT(SUBSTRING_INDEX(a.nama_customer, ' ', 1), 2), 
            UPPER(
                CONCAT(
                    LEFT(SUBSTRING_INDEX(SUBSTRING_INDEX(a.nama_customer, ' ', 2), ' ', -1), 1), -- Inisial kata kedua
                    LEFT(SUBSTRING_INDEX(SUBSTRING_INDEX(a.nama_customer, ' ', 3), ' ', -1), 1), -- Inisial kata ketiga
                    LEFT(SUBSTRING_INDEX(SUBSTRING_INDEX(a.nama_customer, ' ', 4), ' ', -1), 1) -- Inisial kata keempat
                )
            )
            ) AS inisial_customer,
             a.nama_customer, d.nama_cabang,
		b.not_yet, b.ov_up_07, b.ov_under_30, b.ov_under_60, b.ov_under_90, b.ov_up_90,
		(b.ov_up_07 + b.ov_under_30 + b.ov_under_60 + b.ov_under_90 + b.ov_up_90) AS utangnya 
		from pro_customer a 
		join pro_customer_admin_arnya b on a.id_customer = b.id_customer 
		join acl_user c on a.id_marketing = c.id_user 
		join pro_master_cabang d on a.id_wilayah = d.id_master 
		where 1=1
	";

if ($q4 != '') {
    $sql .= " and a.id_wilayah = '" . $q4 . "'";
}

// if ($selectBulan != ""){
//     $sql .= " and MONTH(a.tanggal_kirim) = '".$selectBulan."'";
// }

// if ( $selectTahun != "" ){
//     $sql .= " and YEAR(a.tanggal_kirim) = '" .$selectTahun. "'";
// }

$sql .= " GROUP BY 
		    inisial_customer
		ORDER BY 
		    utangnya DESC
		LIMIT 5";

// Menjalankan query dan mendapatkan hasilnya
$result = $con->getResult($sql);



// Siapkan data dalam format array untuk JSON
$data = [];
foreach ($result as $row) {
    if ($q4 == '') {
        $row['nama_cabang'] = " Nasional";
    }

    $data[] = [
        'customer_det'  => $row['nama_customer'],
        'customer'      => $row['inisial_customer'],
        'ar'            => $row['utangnya'],
        'cabang'        => $row['nama_cabang']
    ];
}



// Mengirimkan data dalam format JSON
echo json_encode($data);

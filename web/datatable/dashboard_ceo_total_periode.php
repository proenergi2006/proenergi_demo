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

// $year = date('Y');
// $month = date('m');
$firstPeriode = date('Y-m-01', strtotime("$selectTahun-$selectBulan-01"));
$lastPeriode = date('Y-m-15', strtotime("$selectTahun-$selectBulan-15"));
// Query untuk mengambil data volume dan tanggal_loaded
if ($q4 != '') {
    $filterCabang = " and id_cabang = '" . $q4 . "'";
}

$sql_periode_awal = "
  WITH
distinct_penawaran AS (
    SELECT id_penawaran, volume_tawar
    FROM pro_penawaran
    WHERE flag_approval = '1'
      AND DATE(masa_awal) = '$firstPeriode' $filterCabang
),
data_po AS (
    SELECT
    a.id_penawaran,
    b.id_poc,
    COALESCE((CASE WHEN b.poc_approved = '1' THEN b.volume_poc ELSE 0 END), 0) AS volume_poc,
    SUM(CASE WHEN d.is_approved = '1' THEN 1 ELSE 0 END) AS po_cust,
    COALESCE(SUM(CASE WHEN d.is_approved = '1' THEN d.volume_kirim ELSE 0 END), 0) AS vol_cust,
    COALESCE((SELECT COALESCE(volume_close, 0)
            FROM pro_po_customer_close
            WHERE id_poc = b.id_poc AND st_Aktif = 'Y'),0) AS volume_close_po,

          -- Delivered
    SUM(CASE WHEN d.status_plan = '1' AND e.is_delivered = '1' THEN 1 ELSE 0 END) AS po_delivered,
    SUM(CASE WHEN d.status_plan = '1' AND e.is_delivered = '1' THEN d.volume_kirim ELSE 0 END) AS vol_po_delivered,

    -- Pending Delivery
    SUM(CASE WHEN d.is_approved = '1' AND (e.is_delivered != '1' OR e.is_delivered IS NULL) AND (e.is_cancel != '1' OR e.is_cancel IS NULL) THEN 1 ELSE 0 END) AS pend_delivered,
    SUM(CASE WHEN d.is_approved = '1' AND (e.is_delivered != '1' OR e.is_delivered IS NULL) AND (e.is_cancel != '1' OR e.is_cancel IS NULL) THEN d.volume_kirim ELSE 0 END) AS vol_pend_delivered
    FROM pro_penawaran a
    JOIN pro_po_customer b ON a.id_penawaran = b.id_penawaran
    LEFT JOIN pro_po_customer_plan d ON b.id_poc = d.id_poc
    LEFT JOIN pro_po_ds_detail e ON d.id_plan = e.id_plan
    WHERE DATE(a.masa_awal) = '$firstPeriode' AND b.poc_approved = '1' AND d.status_plan = '1' $filterCabang
    GROUP BY a.id_penawaran, b.id_poc, b.volume_poc
)

SELECT
    -- Penawaran
    (SELECT COUNT(*) FROM distinct_penawaran) AS po_penawaran,
     COALESCE((SELECT SUM(volume_tawar) FROM distinct_penawaran),0) AS vol_penawaran,

    -- PO Customer
     COALESCE(SUM(vol_cust), 0) AS vol_cust,
     COALESCE(SUM(po_cust), 0) AS po_cust,

    -- PO Close
    SUM(volume_close_po) AS po_close,

     COALESCE(SUM(po_delivered), 0) AS po_delivered,
     COALESCE(SUM(vol_po_delivered), 0) vol_po_delivered
    -- COALESCE(SUM(pend_delivered), 0) pend_delivered,
    -- COALESCE(SUM(vol_pend_delivered), 0) vol_pend_delivered,
   
    -- Pending Plan
    -- COALESCE(SUM(volume_poc) - SUM(vol_cust) - SUM(volume_close_po),0) AS vol_pend_plan

FROM data_po a
                ";

$sql_periode_akhir = "
WITH
distinct_penawaran AS (
    SELECT id_penawaran, volume_tawar
    FROM pro_penawaran
    WHERE flag_approval = '1'
      AND DATE(masa_awal) = '$lastPeriode' $filterCabang
),
data_po AS (
     SELECT
    a.id_penawaran,
    b.id_poc,
    COALESCE((CASE WHEN b.poc_approved = '1' THEN b.volume_poc ELSE 0 END), 0) AS volume_poc,
    SUM(CASE WHEN d.is_approved = '1' THEN 1 ELSE 0 END) AS po_cust,
    COALESCE(SUM(CASE WHEN d.is_approved = '1' THEN d.volume_kirim ELSE 0 END), 0) AS vol_cust,
    COALESCE((SELECT COALESCE(volume_close, 0)
            FROM pro_po_customer_close
            WHERE id_poc = b.id_poc AND st_Aktif = 'Y'),0) AS volume_close_po,

          -- Delivered
    SUM(CASE WHEN d.status_plan = '1' AND e.is_delivered = '1' THEN 1 ELSE 0 END) AS po_delivered,
    SUM(CASE WHEN d.status_plan = '1' AND e.is_delivered = '1' THEN d.volume_kirim ELSE 0 END) AS vol_po_delivered,

    -- Pending Delivery
    SUM(CASE WHEN d.is_approved = '1' AND (e.is_delivered != '1' OR e.is_delivered IS NULL) AND (e.is_cancel != '1' OR e.is_cancel IS NULL) THEN 1 ELSE 0 END) AS pend_delivered,
    SUM(CASE WHEN d.is_approved = '1' AND (e.is_delivered != '1' OR e.is_delivered IS NULL) AND (e.is_cancel != '1' OR e.is_cancel IS NULL) THEN d.volume_kirim ELSE 0 END) AS vol_pend_delivered
    FROM pro_penawaran a
    JOIN pro_po_customer b ON a.id_penawaran = b.id_penawaran
    LEFT JOIN pro_po_customer_plan d ON b.id_poc = d.id_poc
    LEFT JOIN pro_po_ds_detail e ON d.id_plan = e.id_plan
    WHERE DATE(a.masa_awal) = '$lastPeriode' AND b.poc_approved = '1' AND d.status_plan = '1' $filterCabang
    GROUP BY a.id_penawaran, b.id_poc, b.volume_poc
)

SELECT
  -- Penawaran
    (SELECT COUNT(*) FROM distinct_penawaran) AS po_penawaran,
     COALESCE((SELECT SUM(volume_tawar) FROM distinct_penawaran),0) AS vol_penawaran,

    -- PO Customer
     COALESCE(SUM(vol_cust), 0) AS vol_cust,
     COALESCE(SUM(po_cust), 0) AS po_cust,

    -- PO Close
    SUM(volume_close_po) AS po_close,

     COALESCE(SUM(po_delivered), 0) AS po_delivered,
     COALESCE(SUM(vol_po_delivered), 0) vol_po_delivered
    -- COALESCE(SUM(pend_delivered), 0) pend_delivered,
    -- COALESCE(SUM(vol_pend_delivered), 0) vol_pend_delivered,
   
    -- Pending Plan
    -- COALESCE(SUM(volume_poc) - SUM(vol_cust) - SUM(volume_close_po),0) AS vol_pend_plan

FROM data_po a
";

// if ($selectTahun != ""){
//     $sql_periode_awal .= " and YEAR(tgl_realisasi) = '" .$selectTahun. "'";
//     $$sql_periode_akhir .= " and YEAR(a.tanggal_delivered) = '" .$selectTahun. "'";
// }
// if ($selectBulan != "" ){
//     $sql_periode_awal .= " and MONTH(tgl_realisasi) = '".$selectBulan."'";
//     $sql_periode_akhir .= " and MONTH(a.tanggal_delivered) = '".$selectBulan."'";

// }


// Menjalankan query dan mendapatkan hasilnya
$sql_periode_awal = $con->getRecord($sql_periode_awal);
// Menjalankan query dan mendapatkan hasilnya
$sql_periode_akhir = $con->getRecord($sql_periode_akhir);


// Isi default jika kosong
if (!$sql_periode_awal) {
    $sql_periode_awal = [
        'po_penawaran' => 0,
        'vol_penawaran' => 0,
        'po_cust' => 0,
        'vol_cust' => 0,
        'po_close' => 0,
        'po_delivered' => 0,
        'vol_po_delivered' => 0,
        'pend_delivered' => 0,
        'vol_pend_delivered' => 0

    ];
}

if (!$sql_periode_akhir) {
    $sql_periode_akhir = [
        'po_penawaran' => 0,
        'vol_penawaran' => 0,
        'po_cust' => 0,
        'vol_cust' => 0,
        'po_close' => 0,
        'po_delivered' => 0,
        'vol_po_delivered' => 0,
        'pend_delivered' => 0,
        'vol_pend_delivered' => 0
    ];
}

$data[] = [
    'periode_awal' => $sql_periode_awal,
    'periode_akhir' => $sql_periode_akhir,
];

// Mengirimkan data dalam format JSON
echo json_encode($data);

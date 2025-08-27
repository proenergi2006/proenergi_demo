<?php
// ini_set('memory_limit', '256M');
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$con = new Connection();
$id_wilayah  = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$q1 = isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';
$q2 = isset($_POST["q2"]) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : '';
$q3 = isset($_POST["q3"]) ? htmlspecialchars($_POST["q3"], ENT_QUOTES) : '';

if ($q1) {
    $year = $q1;
} else {
    $year = date("Y");
}

if ($q3) {
    $id_wilayah = $q3;
}

$sql = "SELECT
    a.fullname AS nama_marketing,
    a.id_user AS id_mkt,
    COALESCE(SUM(CASE WHEN MONTH(b.tanggal_kirim) = 1 THEN b.volume_kirim ELSE 0 END), 0) AS total_realisasi_jan,
    COALESCE(SUM(CASE WHEN MONTH(b.tanggal_kirim) = 2 THEN b.volume_kirim ELSE 0 END), 0) AS total_realisasi_feb,
    COALESCE(SUM(CASE WHEN MONTH(b.tanggal_kirim) = 3 THEN b.volume_kirim ELSE 0 END), 0) AS total_realisasi_mar,
    COALESCE(SUM(CASE WHEN MONTH(b.tanggal_kirim) = 4 THEN b.volume_kirim ELSE 0 END), 0) AS total_realisasi_apr,
    COALESCE(SUM(CASE WHEN MONTH(b.tanggal_kirim) = 5 THEN b.volume_kirim ELSE 0 END), 0) AS total_realisasi_mei,
    COALESCE(SUM(CASE WHEN MONTH(b.tanggal_kirim) = 6 THEN b.volume_kirim ELSE 0 END), 0) AS total_realisasi_jun,
    COALESCE(SUM(CASE WHEN MONTH(b.tanggal_kirim) = 7 THEN b.volume_kirim ELSE 0 END), 0) AS total_realisasi_jul,
    COALESCE(SUM(CASE WHEN MONTH(b.tanggal_kirim) = 8 THEN b.volume_kirim ELSE 0 END), 0) AS total_realisasi_ags,
    COALESCE(SUM(CASE WHEN MONTH(b.tanggal_kirim) = 9 THEN b.volume_kirim ELSE 0 END), 0) AS total_realisasi_sep,
    COALESCE(SUM(CASE WHEN MONTH(b.tanggal_kirim) = 10 THEN b.volume_kirim ELSE 0 END), 0) AS total_realisasi_okt,
    COALESCE(SUM(CASE WHEN MONTH(b.tanggal_kirim) = 11 THEN b.volume_kirim ELSE 0 END), 0) AS total_realisasi_nov,
    COALESCE(SUM(CASE WHEN MONTH(b.tanggal_kirim) = 12 THEN b.volume_kirim ELSE 0 END), 0) AS total_realisasi_des
	FROM
		acl_user a
	JOIN pro_customer pc ON a.id_user = pc.id_marketing
	JOIN pro_po_customer ppc ON pc.id_customer = ppc.id_customer
	JOIN pro_po_customer_plan b ON b.id_poc = ppc.id_poc
	JOIN pro_penawaran pp ON pp.id_penawaran = ppc.id_penawaran
	WHERE
		pp.id_cabang = '" . $id_wilayah . "'
		AND (a.id_role = '11' OR a.id_role = '17')
		AND ppc.poc_approved = 1
		AND b.status_plan = 1
		AND YEAR(b.tanggal_kirim) = '" . $year . "'";

if ($q2) {
    $sql .= "AND a.id_user = '" . $q2 . "'";
}

$sql .= "GROUP BY a.id_user order by a.fullname asc";
$tot_record = $con->num_rows($sql);

if ($tot_record == 0) {
    $data = [];
} else {
    $result = $con->getResult($sql);
    $total_volume_jan = 0;
    $total_volume_feb = 0;
    $total_volume_mar = 0;
    $total_volume_apr = 0;
    $total_volume_mei = 0;
    $total_volume_jun = 0;
    $total_volume_jul = 0;
    $total_volume_ags = 0;
    $total_volume_sep = 0;
    $total_volume_okt = 0;
    $total_volume_nov = 0;
    $total_volume_des = 0;

    foreach ($result as $key) {

        $total_volume_jan += $key['total_realisasi_jan'];
        $total_volume_feb += $key['total_realisasi_feb'];
        $total_volume_mar += $key['total_realisasi_mar'];
        $total_volume_apr += $key['total_realisasi_apr'];
        $total_volume_mei += $key['total_realisasi_mei'];
        $total_volume_jun += $key['total_realisasi_jun'];
        $total_volume_jul += $key['total_realisasi_jul'];
        $total_volume_ags += $key['total_realisasi_ags'];
        $total_volume_sep += $key['total_realisasi_sep'];
        $total_volume_okt += $key['total_realisasi_okt'];
        $total_volume_nov += $key['total_realisasi_nov'];
        $total_volume_des += $key['total_realisasi_des'];

        $data = [
            "nama_marketing" => $key['nama_marketing'],
            "januari" => $total_volume_jan,
            "februari" => $total_volume_feb,
            "maret" => $total_volume_mar,
            "april" => $total_volume_apr,
            "mei" => $total_volume_mei,
            "juni" => $total_volume_jun,
            "juli" => $total_volume_jul,
            "agustus" => $total_volume_ags,
            "september" => $total_volume_sep,
            "oktober" => $total_volume_okt,
            "november" => $total_volume_nov,
            "desember" => $total_volume_des
        ];
    }
}
// echo json_encode($data);
// exit();
if ($q1 != "" || $q2 != "" || $q3 != "") {
    echo json_encode($data);
} else {
    $volumeJSON = json_encode($data);
}
// unset($data);

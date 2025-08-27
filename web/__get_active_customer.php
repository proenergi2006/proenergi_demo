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

$sql = "SELECT a.fullname as nama_marketing, 
( SELECT count(DISTINCT d.id_customer) FROM pro_po_customer as d 
JOIN pro_customer as b ON d.id_customer=b.id_customer 
JOIN pro_po_customer_plan as c ON d.id_poc=c.id_poc
JOIN pro_penawaran e ON e.id_penawaran=d.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master 
WHERE e.id_cabang='" . $id_wilayah . "' AND b.id_marketing = a.id_user AND c.realisasi_kirim != 0 AND MONTH(d.tanggal_poc) = '01' AND YEAR(d.tanggal_poc) = '" . $year . "') as total_customer_jan,

( SELECT count(DISTINCT d.id_customer) FROM pro_po_customer as d 
JOIN pro_customer as b ON d.id_customer=b.id_customer 
JOIN pro_po_customer_plan as c ON d.id_poc=c.id_poc 
JOIN pro_penawaran e ON e.id_penawaran=d.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master 
WHERE e.id_cabang='" . $id_wilayah . "' AND b.id_marketing = a.id_user AND c.realisasi_kirim != 0 AND MONTH(d.tanggal_poc) = '02' AND YEAR(d.tanggal_poc) = '" . $year . "') as total_customer_feb,

( SELECT count(DISTINCT d.id_customer) FROM pro_po_customer as d 
JOIN pro_customer as b ON d.id_customer=b.id_customer 
JOIN pro_po_customer_plan as c ON d.id_poc=c.id_poc 
JOIN pro_penawaran e ON e.id_penawaran=d.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master 
WHERE e.id_cabang='" . $id_wilayah . "' AND b.id_marketing = a.id_user AND c.realisasi_kirim != 0 AND MONTH(d.tanggal_poc) = '03' AND YEAR(d.tanggal_poc) = '" . $year . "') as total_customer_mar,

( SELECT count(DISTINCT d.id_customer) FROM pro_po_customer as d 
JOIN pro_customer as b ON d.id_customer=b.id_customer 
JOIN pro_po_customer_plan as c ON d.id_poc=c.id_poc 
JOIN pro_penawaran e ON e.id_penawaran=d.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master 
WHERE e.id_cabang='" . $id_wilayah . "' AND b.id_marketing = a.id_user AND c.realisasi_kirim != 0 AND MONTH(d.tanggal_poc) = '04' AND YEAR(d.tanggal_poc) = '" . $year . "') as total_customer_apr,

( SELECT count(DISTINCT d.id_customer) FROM pro_po_customer as d 
JOIN pro_customer as b ON d.id_customer=b.id_customer 
JOIN pro_po_customer_plan as c ON d.id_poc=c.id_poc 
JOIN pro_penawaran e ON e.id_penawaran=d.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master 
WHERE e.id_cabang='" . $id_wilayah . "' AND b.id_marketing = a.id_user AND c.realisasi_kirim != 0 AND MONTH(d.tanggal_poc) = '05' AND YEAR(d.tanggal_poc) = '" . $year . "') as total_customer_mei,

( SELECT count(DISTINCT d.id_customer) FROM pro_po_customer as d 
JOIN pro_customer as b ON d.id_customer=b.id_customer 
JOIN pro_po_customer_plan as c ON d.id_poc=c.id_poc 
JOIN pro_penawaran e ON e.id_penawaran=d.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master 
WHERE e.id_cabang='" . $id_wilayah . "' AND b.id_marketing = a.id_user AND c.realisasi_kirim != 0 AND MONTH(d.tanggal_poc) = '06' AND YEAR(d.tanggal_poc) = '" . $year . "') as total_customer_jun,

( SELECT count(DISTINCT d.id_customer) FROM pro_po_customer as d 
JOIN pro_customer as b ON d.id_customer=b.id_customer 
JOIN pro_po_customer_plan as c ON d.id_poc=c.id_poc 
JOIN pro_penawaran e ON e.id_penawaran=d.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master 
WHERE e.id_cabang='" . $id_wilayah . "' AND b.id_marketing = a.id_user AND c.realisasi_kirim != 0 AND MONTH(d.tanggal_poc) = '07' AND YEAR(d.tanggal_poc) = '" . $year . "') as total_customer_jul,

( SELECT count(DISTINCT d.id_customer) FROM pro_po_customer as d 
JOIN pro_customer as b ON d.id_customer=b.id_customer 
JOIN pro_po_customer_plan as c ON d.id_poc=c.id_poc 
JOIN pro_penawaran e ON e.id_penawaran=d.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master 
WHERE e.id_cabang='" . $id_wilayah . "' AND b.id_marketing = a.id_user AND c.realisasi_kirim != 0 AND MONTH(d.tanggal_poc) = '08' AND YEAR(d.tanggal_poc) = '" . $year . "') as total_customer_ags,

( SELECT count(DISTINCT d.id_customer) FROM pro_po_customer as d 
JOIN pro_customer as b ON d.id_customer=b.id_customer 
JOIN pro_po_customer_plan as c ON d.id_poc=c.id_poc 
JOIN pro_penawaran e ON e.id_penawaran=d.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master 
WHERE e.id_cabang='" . $id_wilayah . "' AND b.id_marketing = a.id_user AND c.realisasi_kirim != 0 AND MONTH(d.tanggal_poc) = '09' AND YEAR(d.tanggal_poc) = '" . $year . "') as total_customer_sep,

( SELECT count(DISTINCT d.id_customer) FROM pro_po_customer as d 
JOIN pro_customer as b ON d.id_customer=b.id_customer 
JOIN pro_po_customer_plan as c ON d.id_poc=c.id_poc 
JOIN pro_penawaran e ON e.id_penawaran=d.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master 
WHERE e.id_cabang='" . $id_wilayah . "' AND b.id_marketing = a.id_user AND c.realisasi_kirim != 0 AND MONTH(d.tanggal_poc) = '10' AND YEAR(d.tanggal_poc) = '" . $year . "') as total_customer_okt,

( SELECT count(DISTINCT d.id_customer) FROM pro_po_customer as d 
JOIN pro_customer as b ON d.id_customer=b.id_customer 
JOIN pro_po_customer_plan as c ON d.id_poc=c.id_poc 
JOIN pro_penawaran e ON e.id_penawaran=d.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master 
WHERE e.id_cabang='" . $id_wilayah . "' AND b.id_marketing = a.id_user AND c.realisasi_kirim != 0 AND MONTH(d.tanggal_poc) = '11' AND YEAR(d.tanggal_poc) = '" . $year . "') as total_customer_nov,

( SELECT count(DISTINCT d.id_customer) FROM pro_po_customer as d 
JOIN pro_customer as b ON d.id_customer=b.id_customer 
JOIN pro_po_customer_plan as c ON d.id_poc=c.id_poc 
JOIN pro_penawaran e ON e.id_penawaran=d.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master 
WHERE e.id_cabang='" . $id_wilayah . "' AND b.id_marketing = a.id_user AND c.realisasi_kirim != 0 AND MONTH(d.tanggal_poc) = '12' AND YEAR(d.tanggal_poc) = '" . $year . "') as total_customer_des

FROM acl_user a 
JOIN pro_customer pc ON a.id_user=pc.id_marketing
JOIN pro_po_customer ppc ON pc.id_customer=ppc.id_customer
JOIN pro_po_customer_plan ppcp ON ppcp.id_poc=ppc.id_poc
JOIN pro_penawaran pp ON pp.id_penawaran=ppc.id_penawaran
JOIN pro_master_cabang pmc on pp.id_cabang = pmc.id_master
WHERE pp.id_cabang = '" . $id_wilayah . "' AND (a.id_role = '11' OR a.id_role = '17') AND ppcp.realisasi_kirim != 0 AND YEAR(ppc.tanggal_poc) = '" . $year . "'";

if ($q2) {
    $sql .= "AND a.id_user = '" . $q2 . "'";
}

$sql .= "GROUP BY a.id_user order by a.fullname asc";
$tot_record = $con->num_rows($sql);

if ($tot_record == 0) {
    $data = [];
} else {
    $result = $con->getResult($sql);
    $total_customer_jan = 0;
    $total_customer_feb = 0;
    $total_customer_mar = 0;
    $total_customer_apr = 0;
    $total_customer_mei = 0;
    $total_customer_jun = 0;
    $total_customer_jul = 0;
    $total_customer_ags = 0;
    $total_customer_sep = 0;
    $total_customer_okt = 0;
    $total_customer_nov = 0;
    $total_customer_des = 0;

    foreach ($result as $key) {

        $total_customer_jan += $key['total_customer_jan'];
        $total_customer_feb += $key['total_customer_feb'];
        $total_customer_mar += $key['total_customer_mar'];
        $total_customer_apr += $key['total_customer_apr'];
        $total_customer_mei += $key['total_customer_mei'];
        $total_customer_jun += $key['total_customer_jun'];
        $total_customer_jul += $key['total_customer_jul'];
        $total_customer_ags += $key['total_customer_ags'];
        $total_customer_sep += $key['total_customer_sep'];
        $total_customer_okt += $key['total_customer_okt'];
        $total_customer_nov += $key['total_customer_nov'];
        $total_customer_des += $key['total_customer_des'];

        $data = [
            "nama_marketing" => $key['nama_marketing'],
            "januari" => $total_customer_jan,
            "februari" => $total_customer_feb,
            "maret" => $total_customer_mar,
            "april" => $total_customer_apr,
            "mei" => $total_customer_mei,
            "juni" => $total_customer_jun,
            "juli" => $total_customer_jul,
            "agustus" => $total_customer_ags,
            "september" => $total_customer_sep,
            "oktober" => $total_customer_okt,
            "november" => $total_customer_nov,
            "desember" => $total_customer_des
        ];
    }
}
// echo json_encode($data);
// exit();
if ($q1 != "" || $q2 != "" || $q3 != "") {
    echo json_encode($data);
} else {
    $customerJSON = json_encode($data);
}
// unset($data);

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

$sql = "SELECT au.fullname as nama_marketing, au.id_user
FROM pro_po_customer ppc 
JOIN pro_customer pc ON ppc.id_customer=pc.id_customer 
JOIN acl_user au ON pc.id_marketing=au.id_user 
JOIN pro_po_customer_plan ppcp ON ppcp.id_poc=ppc.id_poc 
WHERE au.id_wilayah = '" . $id_wilayah . "' AND (au.id_role = '11' OR au.id_role = '17') AND ppcp.realisasi_kirim != 0 AND pc.is_verified = '1' AND (pc.status_customer = '2' OR pc.status_customer = '3') AND YEAR(ppc.tanggal_poc) = '" . $year . "'";

if ($q2) {
    $sql .= "AND au.id_user = '" . $q2 . "'";
}

$sql .= "GROUP BY au.id_user order by au.fullname asc";
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

        $sql02 = "SELECT pc.id_customer, pc.nama_customer, ppc.tanggal_poc, pc.id_marketing,
		(
			SELECT COUNT(DISTINCT a.id_customer) FROM pro_customer a
			JOIN pro_po_customer b ON a.id_customer=b.id_customer
			JOIN pro_po_customer_plan c ON b.id_poc=c.id_poc
			WHERE a.id_marketing=pc.id_marketing AND ppc.tanggal_poc = b.tanggal_poc AND a.id_customer=pc.id_customer AND
			(
				CASE
				WHEN MONTH(ppc.tanggal_poc) = '01' AND YEAR(ppc.tanggal_poc) = '" . $year . "' THEN 1
				ELSE 0
				END
			)
		) AS total_jan,
		(
			SELECT COUNT(DISTINCT a.id_customer) FROM pro_customer a
			JOIN pro_po_customer b ON a.id_customer=b.id_customer
			JOIN pro_po_customer_plan c ON b.id_poc=c.id_poc
			WHERE a.id_marketing=pc.id_marketing AND ppc.tanggal_poc = b.tanggal_poc AND a.id_customer=pc.id_customer AND
			(
				CASE
				WHEN MONTH(ppc.tanggal_poc) = '02' AND YEAR(ppc.tanggal_poc) = '" . $year . "' THEN 1
				ELSE 0
				END
			)
		) AS total_feb,
		(
			SELECT COUNT(DISTINCT a.id_customer) FROM pro_customer a
			JOIN pro_po_customer b ON a.id_customer=b.id_customer
			JOIN pro_po_customer_plan c ON b.id_poc=c.id_poc
			WHERE a.id_marketing=pc.id_marketing AND ppc.tanggal_poc = b.tanggal_poc AND a.id_customer=pc.id_customer AND
			(
				CASE
				WHEN MONTH(ppc.tanggal_poc) = '03' AND YEAR(ppc.tanggal_poc) = '" . $year . "' THEN 1
				ELSE 0
				END
			)
		) AS total_mar,
		(
			SELECT COUNT(DISTINCT a.id_customer) FROM pro_customer a
			JOIN pro_po_customer b ON a.id_customer=b.id_customer
			JOIN pro_po_customer_plan c ON b.id_poc=c.id_poc
			WHERE a.id_marketing=pc.id_marketing AND ppc.tanggal_poc = b.tanggal_poc AND a.id_customer=pc.id_customer AND
			(
				CASE
				WHEN MONTH(ppc.tanggal_poc) = '04' AND YEAR(ppc.tanggal_poc) = '" . $year . "' THEN 1
				ELSE 0
				END
			)
		) AS total_apr,
		(
			SELECT COUNT(DISTINCT a.id_customer) FROM pro_customer a
			JOIN pro_po_customer b ON a.id_customer=b.id_customer
			JOIN pro_po_customer_plan c ON b.id_poc=c.id_poc
			WHERE a.id_marketing=pc.id_marketing AND ppc.tanggal_poc = b.tanggal_poc AND a.id_customer=pc.id_customer AND
			(
				CASE
				WHEN MONTH(ppc.tanggal_poc) = '05' AND YEAR(ppc.tanggal_poc) = '" . $year . "' THEN 1
				ELSE 0
				END
			)
		) AS total_mei,
		(
			SELECT COUNT(DISTINCT a.id_customer) FROM pro_customer a
			JOIN pro_po_customer b ON a.id_customer=b.id_customer
			JOIN pro_po_customer_plan c ON b.id_poc=c.id_poc
			WHERE a.id_marketing=pc.id_marketing AND ppc.tanggal_poc = b.tanggal_poc AND a.id_customer=pc.id_customer AND
			(
				CASE
				WHEN MONTH(ppc.tanggal_poc) = '06' AND YEAR(ppc.tanggal_poc) = '" . $year . "' THEN 1
				ELSE 0
				END
			)
		) AS total_jun,
		(
			SELECT COUNT(DISTINCT a.id_customer) FROM pro_customer a
			JOIN pro_po_customer b ON a.id_customer=b.id_customer
			JOIN pro_po_customer_plan c ON b.id_poc=c.id_poc
			WHERE a.id_marketing=pc.id_marketing AND ppc.tanggal_poc = b.tanggal_poc AND a.id_customer=pc.id_customer AND
			(
				CASE
				WHEN MONTH(ppc.tanggal_poc) = '07' AND YEAR(ppc.tanggal_poc) = '" . $year . "' THEN 1
				ELSE 0
				END
			)
		) AS total_jul,
		(
			SELECT COUNT(DISTINCT a.id_customer) FROM pro_customer a
			JOIN pro_po_customer b ON a.id_customer=b.id_customer
			JOIN pro_po_customer_plan c ON b.id_poc=c.id_poc
			WHERE a.id_marketing=pc.id_marketing AND ppc.tanggal_poc = b.tanggal_poc AND a.id_customer=pc.id_customer AND
			(
				CASE
				WHEN MONTH(ppc.tanggal_poc) = '08' AND YEAR(ppc.tanggal_poc) = '" . $year . "' THEN 1
				ELSE 0
				END
			)
		) AS total_ags,
		(
			SELECT COUNT(DISTINCT a.id_customer) FROM pro_customer a
			JOIN pro_po_customer b ON a.id_customer=b.id_customer
			JOIN pro_po_customer_plan c ON b.id_poc=c.id_poc
			WHERE a.id_marketing=pc.id_marketing AND ppc.tanggal_poc = b.tanggal_poc AND a.id_customer=pc.id_customer AND
			(
				CASE
				WHEN MONTH(ppc.tanggal_poc) = '09' AND YEAR(ppc.tanggal_poc) = '" . $year . "' THEN 1
				ELSE 0
				END
			)
		) AS total_sep,
		(
			SELECT COUNT(DISTINCT a.id_customer) FROM pro_customer a
			JOIN pro_po_customer b ON a.id_customer=b.id_customer
			JOIN pro_po_customer_plan c ON b.id_poc=c.id_poc
			WHERE a.id_marketing=pc.id_marketing AND ppc.tanggal_poc = b.tanggal_poc AND a.id_customer=pc.id_customer AND
			(
				CASE
				WHEN MONTH(ppc.tanggal_poc) = '10' AND YEAR(ppc.tanggal_poc) = '" . $year . "' THEN 1
				ELSE 0
				END
			)
		) AS total_okt,
		(
			SELECT COUNT(DISTINCT a.id_customer) FROM pro_customer a
			JOIN pro_po_customer b ON a.id_customer=b.id_customer
			JOIN pro_po_customer_plan c ON b.id_poc=c.id_poc
			WHERE a.id_marketing=pc.id_marketing AND ppc.tanggal_poc = b.tanggal_poc AND a.id_customer=pc.id_customer AND
			(
				CASE
				WHEN MONTH(ppc.tanggal_poc) = '11' AND YEAR(ppc.tanggal_poc) = '" . $year . "' THEN 1
				ELSE 0
				END
			)
		) AS total_nov,
		(
			SELECT COUNT(DISTINCT a.id_customer) FROM pro_customer a
			JOIN pro_po_customer b ON a.id_customer=b.id_customer
			JOIN pro_po_customer_plan c ON b.id_poc=c.id_poc
			WHERE a.id_marketing=pc.id_marketing AND ppc.tanggal_poc = b.tanggal_poc AND a.id_customer=pc.id_customer AND
			(
				CASE
				WHEN MONTH(ppc.tanggal_poc) = '12' AND YEAR(ppc.tanggal_poc) = '" . $year . "' THEN 1
				ELSE 0
				END
			)
		) AS total_des
		FROM pro_customer pc JOIN pro_po_customer ppc ON pc.id_customer=ppc.id_customer JOIN pro_po_customer_plan ppcp ON ppcp.id_poc=ppc.id_poc WHERE pc.id_marketing='" . $key['id_user'] . "' AND ppcp.realisasi_kirim != 0 AND pc.is_verified = '1' AND (pc.status_customer = '2' OR pc.status_customer = '3') GROUP BY pc.id_customer";

        $tot_record02 = $con->num_rows($sql02);

        $customer_jan         = 0;
        $customer_feb         = 0;
        $customer_mar         = 0;
        $customer_apr         = 0;
        $customer_mei         = 0;
        $customer_jun         = 0;
        $customer_jul         = 0;
        $customer_ags         = 0;
        $customer_sep         = 0;
        $customer_okt         = 0;
        $customer_nov         = 0;
        $customer_des         = 0;

        if ($tot_record02 > 0) {
            $result02 = $con->getResult($sql02);
            foreach ($result02 as $key) {
                $customer_jan += $key['total_jan'];
                $customer_feb += $key['total_feb'];
                $customer_mar += $key['total_mar'];
                $customer_apr += $key['total_apr'];
                $customer_mei += $key['total_mei'];
                $customer_jun += $key['total_jun'];
                $customer_jul += $key['total_jul'];
                $customer_ags += $key['total_ags'];
                $customer_sep += $key['total_sep'];
                $customer_okt += $key['total_okt'];
                $customer_nov += $key['total_nov'];
                $customer_des += $key['total_des'];
            }
        }

        $total_customer_jan += $customer_jan;
        $total_customer_feb += $customer_feb;
        $total_customer_mar += $customer_mar;
        $total_customer_apr += $customer_apr;
        $total_customer_mei += $customer_mei;
        $total_customer_jun += $customer_jun;
        $total_customer_jul += $customer_jul;
        $total_customer_ags += $customer_ags;
        $total_customer_sep += $customer_sep;
        $total_customer_okt += $customer_okt;
        $total_customer_nov += $customer_nov;
        $total_customer_des += $customer_des;

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
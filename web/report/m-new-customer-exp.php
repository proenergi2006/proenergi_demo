<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
require_once($public_base_directory . "/libraries/helper/class.xlsxwriter.php");
load_helper("autoload");

$auth    = new MyOtentikasi();
$con     = new Connection();
$flash   = new FlashAlerts;
$enk     = decode($_SERVER['REQUEST_URI']);
$sheet   = 'Sheet1';
$id_wilayah  = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$q1    = htmlspecialchars($enk["q1"], ENT_QUOTES);
$q2    = htmlspecialchars($enk["q2"], ENT_QUOTES);
$q3    = htmlspecialchars($enk["q3"], ENT_QUOTES);

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

$sql .= "GROUP BY au.id_user order by au.fullname ASC";

$res = $con->getResult($sql);

$getWilayah = "SELECT nama_cabang FROM pro_master_cabang WHERE id_master='" . $id_wilayah . "'";
$resWilayah = $con->getRecord($getWilayah);
// echo json_encode($q1);
// exit();

$filename     = "New-Customer-" . date('dmYHis') . ".xlsx";
$arrOp         = array(1 => "=", ">=", "<=");
header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
ob_end_clean();
header('Content-type: application/vnd.ms-excel');
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');

$writer = new XLSXWriter();
$writer->writeSheetHeader($sheet, array('NEW CUSTOMER' => 'string'));
$writer->writeSheetHeaderExt($sheet, array("Tahun : " . $year => "string"));
$writer->writeSheetHeaderExt($sheet, array("Cabang : " . $resWilayah['nama_cabang'] => "string"));
$writer->newMergeCell($sheet, "A1", "N1");
$start = 4;
$patok = 1;
$writer->writeSheetHeaderExt($sheet, array("" => "string"));
$patok++;
$start++;
$writer->setColumnIndex($patok);
$header = array(
	"NO"                        => 'string',
	"NAMA MARKETING"            => 'string',
	"JANUARI"                   => 'string',
	"FEBRUARI"                  => 'string',
	"MARET"                     => 'string',
	"APRIL"                     => 'string',
	"MEI"                       => 'string',
	"JUNI"                      => 'string',
	"JULI"                      => 'string',
	"AGUSTUS"                   => 'string',
	"SEPTEMBER"                 => 'string',
	"OKTOBER"                   => 'string',
	"NOVEMBER"                  => 'string',
	"DESEMBER"                  => 'string',
	"TOTAL CUSTOMER"            => 'string',
);
$writer->writeSheetHeaderExt($sheet, $header);
$start++;

if (count($res) > 0) {
	$tot1 = 0;
	$last = $start - 1;
	$no = 1;
	foreach ($res as $data) {
		$sql02 = "SELECT pc.id_customer, pc.nama_customer, ppc.tanggal_poc, pc.id_marketing,
		(
			SELECT COUNT(DISTINCT a.id_customer) FROM pro_customer a
			JOIN pro_po_customer b ON a.id_customer=b.id_customer
			JOIN pro_po_customer_plan c ON b.id_poc=c.id_poc
			JOIN pro_penawaran d ON d.id_penawaran=b.id_penawaran
			JOIN pro_master_cabang e on d.id_cabang = e.id_master
			WHERE d.id_cabang='" . $id_wilayah . "' AND a.id_marketing=pc.id_marketing AND ppc.tanggal_poc = b.tanggal_poc AND a.id_customer=pc.id_customer AND
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
			JOIN pro_penawaran d ON d.id_penawaran=b.id_penawaran
			JOIN pro_master_cabang e on d.id_cabang = e.id_master
			WHERE d.id_cabang='" . $id_wilayah . "' AND a.id_marketing=pc.id_marketing AND ppc.tanggal_poc = b.tanggal_poc AND a.id_customer=pc.id_customer AND
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
			JOIN pro_penawaran d ON d.id_penawaran=b.id_penawaran
			JOIN pro_master_cabang e on d.id_cabang = e.id_master
			WHERE d.id_cabang='" . $id_wilayah . "' AND a.id_marketing=pc.id_marketing AND ppc.tanggal_poc = b.tanggal_poc AND a.id_customer=pc.id_customer AND
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
			JOIN pro_penawaran d ON d.id_penawaran=b.id_penawaran
			JOIN pro_master_cabang e on d.id_cabang = e.id_master
			WHERE d.id_cabang='" . $id_wilayah . "' AND a.id_marketing=pc.id_marketing AND ppc.tanggal_poc = b.tanggal_poc AND a.id_customer=pc.id_customer AND
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
			JOIN pro_penawaran d ON d.id_penawaran=b.id_penawaran
			JOIN pro_master_cabang e on d.id_cabang = e.id_master
			WHERE d.id_cabang='" . $id_wilayah . "' AND a.id_marketing=pc.id_marketing AND ppc.tanggal_poc = b.tanggal_poc AND a.id_customer=pc.id_customer AND
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
			JOIN pro_penawaran d ON d.id_penawaran=b.id_penawaran
			JOIN pro_master_cabang e on d.id_cabang = e.id_master
			WHERE d.id_cabang='" . $id_wilayah . "' AND a.id_marketing=pc.id_marketing AND ppc.tanggal_poc = b.tanggal_poc AND a.id_customer=pc.id_customer AND
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
			JOIN pro_penawaran d ON d.id_penawaran=b.id_penawaran
			JOIN pro_master_cabang e on d.id_cabang = e.id_master
			WHERE d.id_cabang='" . $id_wilayah . "' AND a.id_marketing=pc.id_marketing AND ppc.tanggal_poc = b.tanggal_poc AND a.id_customer=pc.id_customer AND
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
			JOIN pro_penawaran d ON d.id_penawaran=b.id_penawaran
			JOIN pro_master_cabang e on d.id_cabang = e.id_master
			WHERE d.id_cabang='" . $id_wilayah . "' AND a.id_marketing=pc.id_marketing AND ppc.tanggal_poc = b.tanggal_poc AND a.id_customer=pc.id_customer AND
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
			JOIN pro_penawaran d ON d.id_penawaran=b.id_penawaran
			JOIN pro_master_cabang e on d.id_cabang = e.id_master
			WHERE d.id_cabang='" . $id_wilayah . "' AND a.id_marketing=pc.id_marketing AND ppc.tanggal_poc = b.tanggal_poc AND a.id_customer=pc.id_customer AND
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
			JOIN pro_penawaran d ON d.id_penawaran=b.id_penawaran
			JOIN pro_master_cabang e on d.id_cabang = e.id_master
			WHERE d.id_cabang='" . $id_wilayah . "' AND a.id_marketing=pc.id_marketing AND ppc.tanggal_poc = b.tanggal_poc AND a.id_customer=pc.id_customer AND
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
			JOIN pro_penawaran d ON d.id_penawaran=b.id_penawaran
			JOIN pro_master_cabang e on d.id_cabang = e.id_master
			WHERE d.id_cabang='" . $id_wilayah . "' AND a.id_marketing=pc.id_marketing AND ppc.tanggal_poc = b.tanggal_poc AND a.id_customer=pc.id_customer AND
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
			JOIN pro_penawaran d ON d.id_penawaran=b.id_penawaran
			JOIN pro_master_cabang e on d.id_cabang = e.id_master
			WHERE d.id_cabang='" . $id_wilayah . "' AND a.id_marketing=pc.id_marketing AND ppc.tanggal_poc = b.tanggal_poc AND a.id_customer=pc.id_customer AND
			(
				CASE
				WHEN MONTH(ppc.tanggal_poc) = '12' AND YEAR(ppc.tanggal_poc) = '" . $year . "' THEN 1
				ELSE 0
				END
			)
		) AS total_des
		FROM pro_customer pc 
		JOIN pro_po_customer ppc ON pc.id_customer=ppc.id_customer 
		JOIN pro_po_customer_plan ppcp ON ppcp.id_poc=ppc.id_poc
		JOIN pro_penawaran pp ON pp.id_penawaran=ppc.id_penawaran
		JOIN pro_master_cabang pmc on pp.id_cabang = pmc.id_master
		WHERE pp.id_cabang = '" . $id_wilayah . "' AND pc.id_marketing='" . $data['id_user'] . "' AND ppcp.realisasi_kirim != 0 AND pc.is_verified = '1' AND (pc.status_customer = '2' OR pc.status_customer = '3') GROUP BY pc.id_customer";

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

		$total_customer = $customer_jan + $customer_feb + $customer_mar + $customer_apr + $customer_mei + $customer_jun + $customer_jul + $customer_ags + $customer_sep + $customer_okt + $customer_nov + $customer_des;

		$grand_total += $total_customer;

		$last++;

		$writer->writeSheetRow($sheet, array(
			$no++, $data['nama_marketing'], number_format($customer_jan), number_format($customer_feb), number_format($customer_mar), number_format($customer_apr), number_format($customer_mei), number_format($customer_jun), number_format($customer_jul), number_format($customer_ags), number_format($customer_sep), number_format($customer_okt), number_format($customer_nov), number_format($customer_des), number_format($total_customer)
		));
	}
	$writer->writeSheetRow($sheet, array("TOTAL", "", "" . number_format($total_customer_jan) . "", "" . number_format($total_customer_feb) . "", "" . number_format($total_customer_mar) . "", "" . number_format($total_customer_apr) . "", "" . number_format($total_customer_mei) . "", "" . number_format($total_customer_jun) . "", "" . number_format($total_customer_jul) . "", "" . number_format($total_customer_ags) . "", "" . number_format($total_customer_sep) . "", "" . number_format($total_customer_okt) . "", "" . number_format($total_customer_nov) . "", "" . number_format($total_customer_des) . "", "" . number_format($grand_total) . ""));
	$last++;
	$writer->newMergeCell($sheet, "A" . $last, "B" . $last);
} else {
	$writer->writeSheetRow($sheet, array("Data tidak ada"));
	$writer->newMergeCell($sheet, "A" . $start, "N" . $start);
	$start++;
}

$con->close();
$writer->writeToStdOut();
exit(0);

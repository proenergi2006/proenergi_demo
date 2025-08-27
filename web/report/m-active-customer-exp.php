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
WHERE pp.id_cabang = '" . $id_wilayah . "' AND a.id_role = '11' AND ppcp.realisasi_kirim != 0 AND YEAR(ppc.tanggal_poc) = '" . $year . "'";


if ($q2) {
    $sql .= "AND a.id_user = '" . $q2 . "'";
}

$sql .= "GROUP BY a.id_user order by a.fullname ASC";

$res = $con->getResult($sql);

$getWilayah = "SELECT nama_cabang FROM pro_master_cabang WHERE id_master='" . $id_wilayah . "'";
$resWilayah = $con->getRecord($getWilayah);
// echo json_encode($q1);
// exit();

$filename     = "Active-Customer-" . date('dmYHis') . ".xlsx";
$arrOp         = array(1 => "=", ">=", "<=");
header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
ob_end_clean();
header('Content-type: application/vnd.ms-excel');
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');

$writer = new XLSXWriter();
$writer->writeSheetHeader($sheet, array('ACTIVE CUSTOMER' => 'string'));
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
    $total_jan = 0;
    $total_feb = 0;
    $total_mar = 0;
    $total_apr = 0;
    $total_mei = 0;
    $total_jun = 0;
    $total_jul = 0;
    $total_ags = 0;
    $total_sep = 0;
    $total_okt = 0;
    $total_nov = 0;
    $total_des = 0;
    $total_volume = 0;
    $grand_total = 0;
    foreach ($res as $data) {
        $last++;
        $total_jan += $data['total_customer_jan'];
        $total_feb += $data['total_customer_feb'];
        $total_mar += $data['total_customer_mar'];
        $total_apr += $data['total_customer_apr'];
        $total_mei += $data['total_customer_mei'];
        $total_jun += $data['total_customer_jun'];
        $total_jul += $data['total_customer_jul'];
        $total_ags += $data['total_customer_ags'];
        $total_sep += $data['total_customer_sep'];
        $total_okt += $data['total_customer_okt'];
        $total_nov += $data['total_customer_nov'];
        $total_des += $data['total_customer_des'];

        $total_volume = $data['total_customer_jan'] + $data['total_customer_feb'] + $data['total_customer_mar'] + $data['total_customer_apr'] + $data['total_customer_mei'] + $data['total_customer_jun'] + $data['total_customer_jul'] + $data['total_customer_ags'] + $data['total_customer_sep'] + $data['total_customer_okt'] + $data['total_customer_nov'] + $data['total_customer_des'];

        $grand_total += $total_volume;

        $writer->writeSheetRow($sheet, array(
            $no++, $data['nama_marketing'], number_format($data['total_customer_jan']), number_format($data['total_customer_feb']), number_format($data['total_customer_mar']), number_format($data['total_customer_apr']), number_format($data['total_customer_mei']), number_format($data['total_customer_jun']), number_format($data['total_customer_jul']), number_format($data['total_customer_ags']), number_format($data['total_customer_sep']), number_format($data['total_customer_okt']), number_format($data['total_customer_nov']), number_format($data['total_customer_des']), number_format($total_volume)
        ));
    }
    $writer->writeSheetRow($sheet, array("TOTAL", "", "" . number_format($total_jan) . "", "" . number_format($total_feb) . "", "" . number_format($total_mar) . "", "" . number_format($total_apr) . "", "" . number_format($total_mei) . "", "" . number_format($total_jun) . "", "" . number_format($total_jul) . "", "" . number_format($total_ags) . "", "" . number_format($total_sep) . "", "" . number_format($total_okt) . "", "" . number_format($total_nov) . "", "" . number_format($total_des) . "", "" . number_format($grand_total) . ""));
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

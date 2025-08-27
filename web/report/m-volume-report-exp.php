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
( SELECT IFNULL(sum(realisasi_kirim),0) FROM pro_po_customer_plan b 
JOIN pro_po_customer c ON b.id_poc=c.id_poc 
JOIN pro_customer d ON c.id_customer=d.id_customer AND d.id_marketing=a.id_user 
JOIN pro_penawaran e ON e.id_penawaran=c.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master
WHERE e.id_cabang='" . $id_wilayah . "' AND MONTH(c.tanggal_poc) = '01' AND YEAR(c.tanggal_poc) = '" . $year . "' AND c.poc_approved = 1 AND d.id_marketing=a.id_user) as total_realisasi_jan,

( SELECT IFNULL(sum(realisasi_kirim),0) FROM pro_po_customer_plan b 
JOIN pro_po_customer c ON b.id_poc=c.id_poc 
JOIN pro_customer d ON c.id_customer=d.id_customer AND d.id_marketing=a.id_user 
JOIN pro_penawaran e ON e.id_penawaran=c.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master
WHERE e.id_cabang='" . $id_wilayah . "' AND MONTH(c.tanggal_poc) = '02' AND YEAR(c.tanggal_poc) = '" . $year . "' AND c.poc_approved = 1 AND d.id_marketing=a.id_user) as total_realisasi_feb,

( SELECT IFNULL(sum(realisasi_kirim),0) FROM pro_po_customer_plan b 
JOIN pro_po_customer c ON b.id_poc=c.id_poc 
JOIN pro_customer d ON c.id_customer=d.id_customer AND d.id_marketing=a.id_user 
JOIN pro_penawaran e ON e.id_penawaran=c.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master
WHERE e.id_cabang='" . $id_wilayah . "' AND MONTH(c.tanggal_poc) = '03' AND YEAR(c.tanggal_poc) = '" . $year . "' AND c.poc_approved = 1 AND d.id_marketing=a.id_user) as total_realisasi_mar,

( SELECT IFNULL(sum(realisasi_kirim),0) FROM pro_po_customer_plan b 
JOIN pro_po_customer c ON b.id_poc=c.id_poc 
JOIN pro_customer d ON c.id_customer=d.id_customer AND d.id_marketing=a.id_user 
JOIN pro_penawaran e ON e.id_penawaran=c.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master
WHERE e.id_cabang='" . $id_wilayah . "' AND MONTH(c.tanggal_poc) = '04' AND YEAR(c.tanggal_poc) = '" . $year . "' AND c.poc_approved = 1 AND d.id_marketing=a.id_user) as total_realisasi_apr,

( SELECT IFNULL(sum(realisasi_kirim),0) FROM pro_po_customer_plan b 
JOIN pro_po_customer c ON b.id_poc=c.id_poc 
JOIN pro_customer d ON c.id_customer=d.id_customer AND d.id_marketing=a.id_user 
JOIN pro_penawaran e ON e.id_penawaran=c.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master
WHERE e.id_cabang='" . $id_wilayah . "' AND MONTH(c.tanggal_poc) = '05' AND YEAR(c.tanggal_poc) = '" . $year . "' AND c.poc_approved = 1 AND d.id_marketing=a.id_user) as total_realisasi_mei,

( SELECT IFNULL(sum(realisasi_kirim),0) FROM pro_po_customer_plan b 
JOIN pro_po_customer c ON b.id_poc=c.id_poc 
JOIN pro_customer d ON c.id_customer=d.id_customer AND d.id_marketing=a.id_user 
JOIN pro_penawaran e ON e.id_penawaran=c.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master
WHERE e.id_cabang='" . $id_wilayah . "' AND MONTH(c.tanggal_poc) = '06' AND YEAR(c.tanggal_poc) = '" . $year . "' AND c.poc_approved = 1 AND d.id_marketing=a.id_user) as total_realisasi_jun,

( SELECT IFNULL(sum(realisasi_kirim),0) FROM pro_po_customer_plan b 
JOIN pro_po_customer c ON b.id_poc=c.id_poc 
JOIN pro_customer d ON c.id_customer=d.id_customer AND d.id_marketing=a.id_user 
JOIN pro_penawaran e ON e.id_penawaran=c.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master
WHERE e.id_cabang='" . $id_wilayah . "' AND MONTH(c.tanggal_poc) = '07' AND YEAR(c.tanggal_poc) = '" . $year . "' AND c.poc_approved = 1 AND d.id_marketing=a.id_user) as total_realisasi_jul,

( SELECT IFNULL(sum(realisasi_kirim),0) FROM pro_po_customer_plan b 
JOIN pro_po_customer c ON b.id_poc=c.id_poc 
JOIN pro_customer d ON c.id_customer=d.id_customer AND d.id_marketing=a.id_user 
JOIN pro_penawaran e ON e.id_penawaran=c.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master
WHERE e.id_cabang='" . $id_wilayah . "' AND MONTH(c.tanggal_poc) = '08' AND YEAR(c.tanggal_poc) = '" . $year . "' AND c.poc_approved = 1 AND d.id_marketing=a.id_user) as total_realisasi_ags,

( SELECT IFNULL(sum(realisasi_kirim),0) FROM pro_po_customer_plan b 
JOIN pro_po_customer c ON b.id_poc=c.id_poc 
JOIN pro_customer d ON c.id_customer=d.id_customer AND d.id_marketing=a.id_user 
JOIN pro_penawaran e ON e.id_penawaran=c.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master
WHERE e.id_cabang='" . $id_wilayah . "' AND MONTH(c.tanggal_poc) = '09' AND YEAR(c.tanggal_poc) = '" . $year . "' AND c.poc_approved = 1 AND d.id_marketing=a.id_user) as total_realisasi_sep,

( SELECT IFNULL(sum(realisasi_kirim),0) FROM pro_po_customer_plan b 
JOIN pro_po_customer c ON b.id_poc=c.id_poc 
JOIN pro_customer d ON c.id_customer=d.id_customer AND d.id_marketing=a.id_user 
JOIN pro_penawaran e ON e.id_penawaran=c.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master
WHERE e.id_cabang='" . $id_wilayah . "' AND MONTH(c.tanggal_poc) = '10' AND YEAR(c.tanggal_poc) = '" . $year . "' AND c.poc_approved = 1 AND d.id_marketing=a.id_user) as total_realisasi_okt,

( SELECT IFNULL(sum(realisasi_kirim),0) FROM pro_po_customer_plan b 
JOIN pro_po_customer c ON b.id_poc=c.id_poc 
JOIN pro_customer d ON c.id_customer=d.id_customer AND d.id_marketing=a.id_user 
JOIN pro_penawaran e ON e.id_penawaran=c.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master
WHERE e.id_cabang='" . $id_wilayah . "' AND MONTH(c.tanggal_poc) = '11' AND YEAR(c.tanggal_poc) = '" . $year . "' AND c.poc_approved = 1 AND d.id_marketing=a.id_user) as total_realisasi_nov,

( SELECT IFNULL(sum(realisasi_kirim),0) FROM pro_po_customer_plan b 
JOIN pro_po_customer c ON b.id_poc=c.id_poc 
JOIN pro_customer d ON c.id_customer=d.id_customer AND d.id_marketing=a.id_user 
JOIN pro_penawaran e ON e.id_penawaran=c.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master
WHERE e.id_cabang='" . $id_wilayah . "' AND MONTH(c.tanggal_poc) = '12' AND YEAR(c.tanggal_poc) = '" . $year . "' AND c.poc_approved = 1 AND d.id_marketing=a.id_user) as total_realisasi_des

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

$sql .= "GROUP BY a.id_user order by a.fullname ASC";

$res = $con->getResult($sql);

$getWilayah = "SELECT nama_cabang FROM pro_master_cabang WHERE id_master='" . $id_wilayah . "'";
$resWilayah = $con->getRecord($getWilayah);
// echo json_encode($q1);
// exit();

$filename     = "Laporan-Volume-Marketing-" . date('dmYHis') . ".xlsx";
$arrOp         = array(1 => "=", ">=", "<=");
header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
ob_end_clean();
header('Content-type: application/vnd.ms-excel');
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');

$writer = new XLSXWriter();
$writer->writeSheetHeader($sheet, array('LAPORAN VOLUME MARKETING' => 'string'));
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
    "TOTAL VOLUME"              => 'string',
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
        $total_jan += $data['total_realisasi_jan'];
        $total_feb += $data['total_realisasi_feb'];
        $total_mar += $data['total_realisasi_mar'];
        $total_apr += $data['total_realisasi_apr'];
        $total_mei += $data['total_realisasi_mei'];
        $total_jun += $data['total_realisasi_jun'];
        $total_jul += $data['total_realisasi_jul'];
        $total_ags += $data['total_realisasi_ags'];
        $total_sep += $data['total_realisasi_sep'];
        $total_okt += $data['total_realisasi_okt'];
        $total_nov += $data['total_realisasi_nov'];
        $total_des += $data['total_realisasi_des'];

        $total_volume = $data['total_realisasi_jan'] + $data['total_realisasi_feb'] + $data['total_realisasi_mar'] + $data['total_realisasi_apr'] + $data['total_realisasi_mei'] + $data['total_realisasi_jun'] + $data['total_realisasi_jul'] + $data['total_realisasi_ags'] + $data['total_realisasi_sep'] + $data['total_realisasi_okt'] + $data['total_realisasi_nov'] + $data['total_realisasi_des'];

        $grand_total += $total_volume;

        $writer->writeSheetRow($sheet, array(
            $no++, $data['nama_marketing'], number_format($data['total_realisasi_jan']), number_format($data['total_realisasi_feb']), number_format($data['total_realisasi_mar']), number_format($data['total_realisasi_apr']), number_format($data['total_realisasi_mei']), number_format($data['total_realisasi_jun']), number_format($data['total_realisasi_jul']), number_format($data['total_realisasi_ags']), number_format($data['total_realisasi_sep']), number_format($data['total_realisasi_okt']), number_format($data['total_realisasi_nov']), number_format($data['total_realisasi_des']), number_format($total_volume)
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

<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
require_once($public_base_directory . "/libraries/helper/class.xlsxwriter.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;
$enk  	= decode($_SERVER['REQUEST_URI']);
$sheet 	= 'Sheet1';

$q1	= htmlspecialchars($enk["q1"], ENT_QUOTES);
$q2	= htmlspecialchars($enk["q2"], ENT_QUOTES);
$q3	= htmlspecialchars($enk["q3"], ENT_QUOTES);
$q4	= htmlspecialchars($enk["q4"], ENT_QUOTES);
$q5	= htmlspecialchars($enk["q5"], ENT_QUOTES);
$q6	= htmlspecialchars($enk["q6"], ENT_QUOTES);
$q7	= htmlspecialchars($enk["q7"], ENT_QUOTES);
$q8	= htmlspecialchars($enk["q8"], ENT_QUOTES);

if (paramDecrypt($_SESSION["sinori" . SESSIONID]["id_role"]) == 11 || paramDecrypt($_SESSION["sinori" . SESSIONID]["id_role"]) == 17)
	$where .= " and f.id_marketing = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_user"]) . "'";
else if (paramDecrypt($_SESSION["sinori" . SESSIONID]["id_role"]) == 7 || paramDecrypt($_SESSION["sinori" . SESSIONID]["id_role"]) == 10 || paramDecrypt($_SESSION["sinori" . SESSIONID]["id_role"]) == 18)
	$where .= " and f.id_wilayah = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]) . "'";
else if (paramDecrypt($_SESSION["sinori" . SESSIONID]["id_role"]) == 6)
	$where .= " and (f.id_group = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_group"]) . "' or g.id_om = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_user"]) . "')";

if ($q1 && !$q2) {
	$where .= " and a.tanggal_delivered between '" . tgl_db($q1) . " 00:00:00' and '" . tgl_db($q1) . " 23:59:59'";
	$period = $q1;
} else if ($q1 && $q2) {
	$where .= " and a.tanggal_delivered between '" . tgl_db($q1) . " 00:00:00' and '" . tgl_db($q2) . " 23:59:59'";
	$period = $q1 . " s/d " . $q2;
}
if ($q3) $where .= " and upper(f.nama_customer) like '%" . strtoupper($q3) . "%'";
if ($q4) $where .= " and f.status_customer = '" . $q4 . "'";
if ($q5) $where .= " and f.id_wilayah = '" . $q5 . "'";
if ($q6) $where .= " and f.id_marketing = '" . $q6 . "'";
if ($q7) $where .= " and d.id_area = '" . $q7 . "'";

$sql = "
		select sum(jum_vol) as volume, tanggal_delivered, id_customer, nama_customer, status_customer, fullname, nama_cabang, id_area, nama_area 
		from (
			select date(a.tanggal_delivered) as tanggal_delivered, b.volume_po as jum_vol, f.id_customer, f.nama_customer, f.status_customer, f.id_wilayah, h.nama_cabang, 
			d.id_area, e.nama_area, f.id_marketing, g.fullname 
			from pro_po_ds_detail a
			join pro_po_detail b on a.id_pod = b.id_pod 
			join pro_po_customer c on a.id_poc = c.id_poc 
			join pro_penawaran d on c.id_penawaran = d.id_penawaran 
			join pro_master_area e on d.id_area = e.id_master 
			join pro_customer f on c.id_customer = f.id_customer 
			join acl_user g on f.id_marketing = g.id_user 
			join pro_master_cabang h on f.id_wilayah = h.id_master 
			where a.is_delivered = 1 " . $where . " 
			UNION ALL
			select date(a.tanggal_delivered) as tanggal_delivered, a.bl_lo_jumlah as jum_vol, f.id_customer, f.nama_customer, f.status_customer, f.id_wilayah, h.nama_cabang, 
			d.id_area, e.nama_area, f.id_marketing, g.fullname 
			from pro_po_ds_kapal a 
			join pro_po_customer b on a.id_poc = b.id_poc 
			join pro_penawaran d on b.id_penawaran = d.id_penawaran 
			join pro_master_area e on d.id_area = e.id_master 
			join pro_customer f on b.id_customer = f.id_customer 
			join acl_user g on f.id_marketing = g.id_user 
			join pro_master_cabang h on f.id_wilayah = h.id_master 
			where a.is_delivered = 1 " . $where . " 
		) a 
		group by a.tanggal_delivered, id_customer, id_customer, nama_customer, status_customer, fullname, nama_cabang, id_area, nama_area 
		order by a.tanggal_delivered desc";
$res = $con->getResult($sql);


$filename 	= "Laporan-Customer-" . date('dmYHis') . ".xlsx";
$arrSts 	= array(1 => "Prospek", "Evaluasi", "Tetap");
header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
ob_end_clean();
header('Content-type: application/vnd.ms-excel');
// header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');

$writer = new XLSXWriter();
$writer->writeSheetHeader($sheet, array('Laporan Customer' => 'string'));
$writer->newMergeCell($sheet, "A1", "G1");
$start = 2;
$patok = 1;
if ($q1 && !$q2) {
	$writer->writeSheetHeaderExt($sheet, array("Periode : " . $q1 => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "G" . $start);
	$patok++;
	$start++;
} else if ($q1 && $q2) {
	$writer->writeSheetHeaderExt($sheet, array("Periode : " . $q1 . " s/d " . $q2 => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "G" . $start);
	$patok++;
	$start++;
}
if ($q3) {
	$writer->writeSheetHeaderExt($sheet, array("Customer : " . $q3 => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "G" . $start);
	$patok++;
	$start++;
}
if ($q4) {
	$writer->writeSheetHeaderExt($sheet, array("Status Customer : " . $arrSts[$q4] => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "G" . $start);
	$patok++;
	$start++;
}
if ($q7) {
	$q7Txt = $con->getOne("select nama_area from pro_master_area where id_master = '" . $q7 . "'");
	$writer->writeSheetHeaderExt($sheet, array("Area : " . $q7Txt => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "G" . $start);
	$patok++;
	$start++;
}
if ($q5) {
	$start++;
	$q5Txt = $con->getOne("select nama_cabang from pro_master_cabang where id_master = '" . $q5 . "'");
	$writer->writeSheetHeaderExt($sheet, array("Cabang Invoice : " . $q5Txt => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "G" . $start);
	$patok++;
	$start++;
}
if ($q6) {
	$start++;
	$q6Txt = $con->getOne("select fullname from acl_user where id_user = '" . $q6 . "'");
	$writer->writeSheetHeaderExt($sheet, array("Marketing : " . $q6Txt => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "G" . $start);
	$patok++;
	$start++;
}
$writer->writeSheetHeaderExt($sheet, array("" => "string"));
$patok++;
$start++;
$writer->setColumnIndex($patok);

$header = array(
	"Periode" => 'string',
	"Customer" => 'string',
	"Marketing" => 'string',
	"Cabang Invoice" => 'string',
	"Area" => 'string',
	"Status" => 'string',
	"Volume Terkirim (Liter)" => 'string',
);
$writer->writeSheetHeaderExt($sheet, $header);
$start++;

if (count($res) > 0) {
	$tot1 = 0;
	$last = $start - 1;
	foreach ($res as $data) {
		$last++;
		$writer->writeSheetRow($sheet, array(
			date("d/m/Y", strtotime($data['tanggal_delivered'])), $data['nama_customer'], $data['fullname'], $data['nama_cabang'], $data['nama_area'],
			$arrSts[$data['status_customer']], $data['volume']
		));
	}
	$writer->writeSheetRow($sheet, array("TOTAL", "", "", "", "", "", "=SUM(G" . $start . ":G" . $last . ")"));
	$last++;
	$writer->newMergeCell($sheet, "A" . $last, "F" . $last);
} else {
	$writer->writeSheetRow($sheet, array("Data tidak ada"));
	$writer->newMergeCell($sheet, "A" . $start, "G" . $start);
	$start++;
}

$con->close();
$writer->writeToStdOut();
exit(0);

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


if (paramDecrypt($_SESSION["sinori" . SESSIONID]["id_role"]) == 11 || paramDecrypt($_SESSION["sinori" . SESSIONID]["id_role"]) == 17) {
	$where1 .= " and a.id_marketing = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_user"]) . "'";
	$where2 .= " and a.id_marketing = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_user"]) . "'";
	$where3 .= " and a.id_marketing = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_user"]) . "'";
} else if (paramDecrypt($_SESSION["sinori" . SESSIONID]["id_role"]) == 7 || paramDecrypt($_SESSION["sinori" . SESSIONID]["id_role"]) == 10) {
	$where1 .= " and a.id_wilayah = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]) . "'";
	$where2 .= " and a.id_wilayah = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]) . "'";
	$where3 .= " and a.id_wilayah = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]) . "'";
} else if (paramDecrypt($_SESSION["sinori" . SESSIONID]["id_role"]) == 6) {
	$where1 .= " and (a.id_group = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_group"]) . "' or b.id_om = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_user"]) . "')";
	$where2 .= " and (a.id_group = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_group"]) . "' or b.id_om = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_user"]) . "')";
	$where3 .= " and (a.id_group = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_group"]) . "' or b.id_om = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_user"]) . "')";
}

if ($q1 && !$q2) {
	$where1 .= " and a.created_time between '" . tgl_db($q1) . " 00:00:00' and '" . tgl_db($q1) . " 23:59:59'";
	$where2 .= " and a.prospect_customer_date = '" . tgl_db($q1) . "'";
	$where3 .= " and a.fix_customer_since = '" . tgl_db($q1) . "'";
	$period = $q1;
} else if ($q1 && $q2) {
	$where1 .= " and a.created_time between '" . tgl_db($q1) . " 00:00:00' and '" . tgl_db($q2) . " 23:59:59'";
	$where2 .= " and a.prospect_customer_date between '" . tgl_db($q1) . "' and '" . tgl_db($q2) . "'";
	$where3 .= " and a.fix_customer_since between '" . tgl_db($q1) . "' and '" . tgl_db($q2) . "'";
	$period = $q1 . " s/d " . $q2;
}
if ($q4) {
	$where1 .= " and a.id_wilayah = '" . $q4 . "'";
	$where2 .= " and a.id_wilayah = '" . $q4 . "'";
	$where3 .= " and a.id_wilayah = '" . $q4 . "'";
}
if ($q5) {
	$where1 .= " and a.id_marketing = '" . $q5 . "'";
	$where2 .= " and a.id_marketing = '" . $q5 . "'";
	$where3 .= " and a.id_marketing = '" . $q5 . "'";
}
$sql = "select * from (
				select 'Prospek' as statusnya, count(a.id_customer) as jumlah from pro_customer a join acl_user b on a.id_marketing = b.id_user 
				where a.status_customer = 1 " . $where1 . " 
				union select 'Evaluasi' as statusnya, count(a.id_customer) as jumlah from pro_customer a join acl_user b on a.id_marketing = b.id_user  
				where a.status_customer = 2 " . $where2 . " 
				union select 'Tetap' as statusnya, count(a.id_customer) as jumlah from pro_customer a join acl_user b on a.id_marketing = b.id_user  
				where a.status_customer = 3 " . $where3 . " 
			) a";
if ($q3) {
	$arrSts = array(1 => "Prospek", "Evaluasi", "Tetap");
	$sql .= " where statusnya = '" . $arrSts[$q3] . "'";
}

$res = $con->getResult($sql);


$filename 	= "Laporan-Total-Customer-" . date('dmYHis') . ".xlsx";
$arrOp 		= array(1 => "=", ">=", "<=");
header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
ob_end_clean();
header('Content-type: application/vnd.ms-excel');
// header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');

$writer = new XLSXWriter();
$writer->writeSheetHeader($sheet, array('Laporan Total Customer' => 'string'));
$writer->newMergeCell($sheet, "A1", "I1");
$start = 2;
$patok = 1;
if ($q1 && !$q2) {
	$writer->writeSheetHeaderExt($sheet, array("Periode  : " . $q1 => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "I" . $start);
	$patok++;
	$start++;
} else if ($q1 && $q2) {
	$writer->writeSheetHeaderExt($sheet, array("Periode  : " . $q1 . " s/d " . $q2 => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "I" . $start);
	$patok++;
	$start++;
}
if ($q3) {
	$writer->writeSheetHeaderExt($sheet, array("Status " . $arrSts[$q3] . " " . $q3 => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "I" . $start);
	$patok++;
	$start++;
}
if ($q4) {
	$q7Txt = $con->getOne("select nama_cabang from pro_master_cabang where id_master = '" . $q4 . "'");
	$writer->writeSheetHeaderExt($sheet, array("Cabang Invoice : " . $q7Txt => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "I" . $start);
	$patok++;
	$start++;
}
if ($q5) {

	$q7Txt = $con->getOne("select fullname from acl_user where id_user = '" . $q5 . "'");
	$writer->writeSheetHeaderExt($sheet, array("Marketing : " . $q7Txt => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "I" . $start);
	$patok++;
	$start++;
}

$writer->writeSheetHeaderExt($sheet, array("" => "string"));
$patok++;
$start++;
$writer->setColumnIndex($patok);

$header = array(
	"Status" => 'string',
	"Jumlah" => 'string',
);
$writer->writeSheetHeaderExt($sheet, $header);
$start++;

if (count($res) > 0) {
	$tot1 = 0;
	$last = $start - 1;
	foreach ($res as $data) {
		$last++;
		$writer->writeSheetRow($sheet, array(
			$data['statusnya'], $data['jumlah']
		));
	}
	$last++;
} else {
	$writer->writeSheetRow($sheet, array("Data tidak ada"));
	$writer->newMergeCell($sheet, "A" . $start, "I" . $start);
	$start++;
}

$con->close();
$writer->writeToStdOut();
exit(0);

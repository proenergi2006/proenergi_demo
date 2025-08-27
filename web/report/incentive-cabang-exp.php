<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
require_once($public_base_directory . "/libraries/helper/class.xlsxwriter.php");
load_helper("autoload");

$auth    = new MyOtentikasi();
$con     = new Connection();
$flash    = new FlashAlerts;
$enk      = decode($_SERVER['REQUEST_URI']);
$sheet     = 'Sheet1';
$sess_wil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

$periode    = htmlspecialchars($enk["periode"], ENT_QUOTES);
$filter_cabang    = htmlspecialchars($enk["filter_cabang"], ENT_QUOTES);
$datenow     = date("Y-m-d");
$id_user    = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);
$sesrol     = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);

$exp = explode("-", $periode);
$bulan = $exp[1];
$tahun = $exp[0];

$filter_bulan = " AND a.periode_bulan = '" . $bulan . "'";
$filter_tahun = " AND a.periode_tahun = '" . $tahun . "'";

if ($filter_cabang) {
    $sql = "SELECT nama_cabang FROM pro_master_cabang WHERE id_master = '" . $filter_cabang . "'";
    $row = $con->getRecord($sql);
    $file_cabang = $row['nama_cabang'];
    $sql_cabang = " AND a.wilayah = '" . $filter_cabang . "'";
} else {
    $sql_cabang = "";
    $file_cabang = "ALL Cabang";
}

switch ($bulan) {
    case '01':
        $nama_bulan = "Januari";
        break;
    case '02':
        $nama_bulan = "Februari";
        break;
    case '03':
        $nama_bulan = "Maret";
        break;
    case '04':
        $nama_bulan = "April";
        break;
    case '05':
        $nama_bulan = "Mei";
        break;
    case '06':
        $nama_bulan = "Juni";
        break;
    case '07':
        $nama_bulan = "Juli";
        break;
    case '08':
        $nama_bulan = "Agustus";
        break;
    case '09':
        $nama_bulan = "September";
        break;
    case '10':
        $nama_bulan = "Oktober";
        break;
    case '11':
        $nama_bulan = "November";
        break;
    case '12':
        $nama_bulan = "Desember";
        break;
}

$p = new paging;
$sql = "SELECT pmc.id_master as id_cabang, pmc.nama_cabang as cabang,
			SUM(
				CASE
					WHEN pi.disposisi = 2 THEN pi.total_incentive
					ELSE 0
				END
			) AS total_incentive,
			SUM(
				CASE
					WHEN pi.disposisi = 2 THEN pi.volume
					ELSE 0
				END
			) AS total_volume
		FROM 
			pro_pengajuan_incentive a
		LEFT JOIN 
			pro_bundle_incentive as pbi ON pbi.id_pengajuan = a.id
		LEFT JOIN 
			pro_incentive as pi ON pbi.id_incentive = pi.id
		JOIN
			pro_master_cabang as pmc ON a.wilayah=pmc.id_master
		WHERE 
			1=1
		" . $filter_bulan . "
		" . $filter_tahun . "
		" . $sql_cabang . "
		GROUP BY 
			id_cabang";
$res = $con->getResult($sql);
echo json_encode($res);

$filename     = "Incentive per Cabang Periode " . $nama_bulan . " " . $tahun . " - " . $file_cabang . ".xlsx";
header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
ob_end_clean();
header('Content-type: application/vnd.ms-excel');
// header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');

$writer = new XLSXWriter();
$writer->writeSheetHeader($sheet, array('TOTAL INCENTIVE PER CABANG PERIODE ' . strtoupper($nama_bulan) . ' ' . $tahun  => 'string'));
$writer->newMergeCell($sheet, "A1", "D1");
$start = 2;
$patok = 1;

$writer->writeSheetHeaderExt($sheet, array("" => "string"));
$patok++;
$start++;
$writer->setColumnIndex($patok);

$header = array(
    "No" => 'string',
    "Cabang" => 'string',
    "Total Volume (Liter)" => 'string',
    "Total Incentive (Rp)" => 'string',
);
$writer->writeSheetHeaderExt($sheet, $header);
$start++;

if (count($res) > 0) {
    $tot1 = 0;
    $last = $start - 1;
    foreach ($res as $data) {
        $tot1++;
        $writer->writeSheetRow($sheet, array(
            $tot1,
            $data['cabang'],
            number_format($data['total_volume'], 0),
            number_format($data['total_incentive'], 0),
        ), $row_options = array('height' => 30, 'wrap_text' => true));
    }
    $last++;
} else {
    $writer->writeSheetRow($sheet, array("Data tidak ada"));
    $writer->newMergeCell($sheet, "A" . $start, "D" . $start);
    $start++;
}

$con->close();
$writer->writeToStdOut();
exit(0);

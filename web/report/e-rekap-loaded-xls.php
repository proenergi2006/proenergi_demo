<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
// require_once ($public_base_directory."/libraries/helper/excelgen/PHPExcel/IOFactory.php");
require_once($public_base_directory . "/libraries/helper/class.xlsxwriter.php");
load_helper("autoload");

error_reporting(E_ALL ^ E_DEPRECATED);
// error_reporting(E_ALL ^ (E_NOTICE | E_WARNING | E_DEPRECATED));

$auth   = new MyOtentikasi();
$con    = new Connection();
$arrTgl = array(1 => "a.tanggal_loaded");
$q1 = isset($_REQUEST['q1']) ? $_REQUEST['q1'] : '';
$q2 = isset($_REQUEST['q2']) ? $_REQUEST['q2'] : '';
$q3 = isset($_REQUEST['q3']) ? $_REQUEST['q3'] : '';
$q4 = isset($_REQUEST['q4']) ? $_REQUEST['q4'] : '';
$q5 = isset($_REQUEST['q5']) ? $_REQUEST['q5'] : '';
$q6 = isset($_REQUEST['q6']) ? $_REQUEST['q6'] : '';
$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$seswil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

$sql = "
select 
a.*,
b.nomor_pr,
c.nomor_lo_pr,
b.tanggal_pr,
c.volume,
h.nama_terminal,
h.tanki_terminal,
i.nama_cabang,
k.nama_customer,
c.nomor_po_supplier,
g.no_spj,
l.nama_suplier


from pro_po_ds_detail a
join pro_pr b on a.id_pr = b.id_pr
join pro_pr_detail c on a.id_prd = c.id_prd
join pro_po_ds d on a.id_ds = d.id_ds
join pro_po_ds_detail e on a.id_dsd = e.id_dsd
join pro_po f on a.id_po = f.id_po
join pro_po_detail g on a.id_pod = g.id_pod
join pro_master_terminal h on c.pr_terminal = h.id_master
join pro_master_cabang i on f.id_wilayah = i.id_master
join pro_po_customer j on a.id_poc = j.id_poc
join pro_customer k on j.id_customer = k.id_customer
join pro_master_transportir l ON  f.id_transportir = l.id_master
where b.tanggal_pr > '2024-01-01'
			
	";


// $res = $con->getResult($sql);
// echo json_encode($res);

// Tambahkan kondisi pencarian jika ada




if ($q1 != "") {
    $sql .= " AND (
            UPPER(b.nomor_pr) LIKE '%" . strtoupper($q1) . "%' 
            OR UPPER(c.nomor_lo_pr) LIKE '%" . strtoupper($q1) . "%' 
            OR UPPER(h.nama_terminal) LIKE '%" . strtoupper($q1) . "%' 
            OR UPPER(h.tanki_terminal) LIKE '%" . strtoupper($q1) . "%' 
        )";
}
if ($q2 != "" && $q3 != "" && $q4 == "")
    $sql .= " and " . $arrTgl[$q2] . " = '" . tgl_db($q3) . "'";
else if ($q2 != "" && $q3 != "" && $q4 != "")
    $sql .= " and " . $arrTgl[$q2] . " between '" . tgl_db($q3) . "' and '" . tgl_db($q4) . "'";

if ($q5 != "") {
    $sql .= " and f.id_wilayah = '" . $q5 . "'";
}
if ($q6 != "") {
    $sql .= " and d.id_terminal = '" . $q6 . "'";
}

$sql .= "  order by a.tanggal_loaded desc";

$data_ = [];
$arrTgl = array(1 => "a.tanggal_loaded");
$result = $con->getResult($sql);
// echo json_encode($result);
foreach ($result as $data) {
    $data = (object) $data;
    $customer = $data->nama_customer;
    $nama_cabang = $data->nama_cabang;
    $nomor_pr = $data->nomor_pr;
    $nomor_lo_pr = $data->nomor_lo_pr;
    $nama_suplier = $data->nama_suplier;
    $no_spj = $data->no_spj;
    $nomor_po_supplier = $data->nomor_po_supplier;
    $volume = $data->volume;
    $terminal = $data->nama_terminal . ' ' . $data->tanki_terminal . ' ';
    $data_[] = $data;
}
$content = [];
foreach ($data_ as $i => $row) {
    $customer = $row->nama_customer;
    $nama_cabang = $row->nama_cabang;
    $nomor_pr = $row->nomor_pr;
    $nomor_lo_pr = $row->nomor_lo_pr;
    $nama_suplier = $row->nama_suplier;
    $no_spj = $row->no_spj;
    $nomor_po_supplier = $row->nomor_po_supplier;
    $volume = $row->volume;
    $terminal = $row->nama_terminal . ' ' . $row->tanki_terminal . ' ';
    $status = "";
    if ($row->is_loaded == 0 && $row->is_delivered == 0 && $row->is_cancel == 0) {
        $status = 'Belum Loading';
    } elseif ($row->is_loaded == 1 && $row->is_delivered == 0 && $row->is_cancel == 0) {
        $status = 'Loading' . ' ' .
            'Tgl Loading ' . tgl_indo($row->tanggal_loaded) . ' ' .
            'Jam Loading ' . ($row->jam_loaded) . ' ';
    } elseif ($row->is_loaded == 1 && $row->is_delivered == 1 && $row->is_cancel == 0) {
        $status =
            'Delivered' . ' ' .
            'Tgl Loading ' . tgl_indo($row->tanggal_loaded) . ' ' .
            'Jam Loading ' . ($row->jam_loaded) . ' ';
    } elseif ($row->is_loaded == 1 && $row->is_delivered == 0 && $row->is_cancel == 1) {
        $status = 'Cancel' . '' .
            'Tgl Cancel ' . tgl_indo($row->tanggal_cancel) . '';
    }


    $content[] = array(
        ($i + 1),
        $nama_cabang,
        $customer,
        $nomor_pr,
        $nomor_lo_pr,
        $nama_suplier,
        $no_spj,
        $nomor_po_supplier,
        number_format($volume),
        date('d-m-Y', strtotime($row->tanggal_pr)),
        date('d-m-Y', strtotime($row->tanggal_loaded)),
        $terminal,
        $status,
    );
}

$filename = "Rekap-Loaded-" . date('dmYHis') . '.xlsx';
$arrOp         = array(1 => "=", ">=", "<=");
header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
ob_end_clean();
header('Content-type: application/vnd.ms-excel');
// header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');

$sheet  = 'Rekap Loaded';
$writer = new XLSXWriter();
$writer->writeSheetHeader($sheet, array('Rekap Loaded' => 'string'));
$writer->newMergeCell($sheet, 'A1', 'V1');
$writer->writeSheetHeaderExt($sheet, array("" => "string"));
// $writer->setColumnIndex(2);

$header = array(
    'No' => 'string',
    "Cabang" => 'string',
    "Customer" => 'string',
    "Nomor DR" => 'string',
    "Nomor LO" => 'string',
    "Transportir" => 'string',
    "Spj" => 'string',
    "Nomor PO" => 'string',
    "QTY" => 'string',
    "Tanggal DR" => 'string',
    "Tanggal Loaded" => 'string',
    "Terminal" => 'string',
    "Status" => 'string',

);
$writer->writeSheetHeaderExt($sheet, $header);

if (count($data_) > 0) {
    foreach ($content as $row) {
        $writer->writeSheetRow($sheet, $row);
    }
} else {
    $writer->writeSheetRow($sheet, array('Data tidak ada'));
    $writer->newMergeCell($sheet, 'A4', 'V4');
}

$con->close();
$writer->writeToStdOut();
exit(0);

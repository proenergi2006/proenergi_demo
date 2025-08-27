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
$arrTgl = array(1 => "m.tanggal_po", "b.tgl_kirim_po", "a.tanggal_loading");
$q1 = isset($_REQUEST['q1']) ? $_REQUEST['q1'] : '';
$q2 = isset($_REQUEST['q2']) ? $_REQUEST['q2'] : '';
$q3 = isset($_REQUEST['q3']) ? $_REQUEST['q3'] : '';
$q4 = isset($_REQUEST['q4']) ? $_REQUEST['q4'] : '';
$q5 = isset($_REQUEST['q5']) ? $_REQUEST['q5'] : '';

$sql = "
        SELECT
            JSON_EXTRACT(a.status_pengiriman, CONCAT('$[', num.n - 1, '].status')) AS _status_pengiriman,
            JSON_EXTRACT(a.status_pengiriman, CONCAT('$[', num.n - 1, '].tanggal')) AS _tanggal_pengiriman,
            a.*, 
            c.pr_pelanggan, 
            i.nama_customer, 
            e.alamat_survey, 
            f.nama_prov, 
            g.nama_kab, 
            j.fullname, 
            n.nama_transportir, 
            n.nama_suplier, 
            b.no_spj, 
            k.nomor_plat, 
            l.nama_sopir, 
            b.volume_po, 
            h.produk_poc, 
            p.id_area, 
            c.pr_vendor, 
            r.nama_terminal, 
            r.tanki_terminal, 
            r.lokasi_terminal, 
            s.wilayah_angkut, 
            m.nomor_po, 
            m.tanggal_po, 
            c.produk, 
            b.tgl_kirim_po, 
            b.mobil_po 
        FROM 
            (
                SELECT @row := @row + 1 AS n FROM 
                (SELECT 0 UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) t2,
                (SELECT 0 UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) t1,
                (SELECT @row:=0) T0
            ) num
            join pro_po_ds_detail a ON num.n <= JSON_LENGTH(a.status_pengiriman)
            join pro_po_ds o on a.id_ds = o.id_ds 
            join pro_po_detail b on a.id_pod = b.id_pod 
            join pro_po m on a.id_po = m.id_po 
            join pro_pr_detail c on a.id_prd = c.id_prd 
            join pro_po_customer_plan d on a.id_plan = d.id_plan 
            join pro_po_customer h on d.id_poc = h.id_poc 
            join pro_customer_lcr e on d.id_lcr = e.id_lcr 
            join pro_customer i on h.id_customer = i.id_customer 
            join acl_user j on i.id_marketing = j.id_user 
            join pro_master_provinsi f on e.prov_survey = f.id_prov 
            join pro_master_kabupaten g on e.kab_survey = g.id_kab 
            join pro_penawaran p on h.id_penawaran = p.id_penawaran 
            join pro_master_area q on p.id_area = q.id_master 
            join pro_master_transportir_mobil k on b.mobil_po = k.id_master 
            join pro_master_transportir_sopir l on b.sopir_po = l.id_master 
            join pro_master_transportir n on m.id_transportir = n.id_master 
            join pro_master_terminal r on o.id_terminal = r.id_master 
            join pro_master_wilayah_angkut s on e.id_wil_oa = s.id_master and e.prov_survey = s.id_prov and e.kab_survey = s.id_kab 
        where 
            a.is_loaded = 1 
            and o.id_wilayah = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]) . "'
    ";

// $res = $con->getResult($sql);
// echo json_encode($res);

if ($q1 != "") {
    $sql .= " and (upper(a.nomor_do) like '" . strtoupper($q1) . "%' or upper(b.no_spj) = '" . strtoupper($q1) . "' or upper(k.nomor_plat) = '" . strtoupper($q1) . "' 
            or upper(l.nama_sopir) like '%" . strtoupper($q1) . "%' or upper(i.nama_customer) like '%" . strtoupper($q1) . "%')";
}
if ($q2 != "" && $q3 != "" && $q4 == "")
    $sql .= " and " . $arrTgl[$q2] . " = '" . tgl_db($q3) . "'";
else if ($q2 != "" && $q3 != "" && $q4 != "")
    $sql .= " and " . $arrTgl[$q2] . " between '" . tgl_db($q3) . "' and '" . tgl_db($q4) . "'";

$sql .= "  order by a.tanggal_loading desc, a.jam_loading, a.nomor_urut_ds, a.id_dsd";

$data_ = [];
$arrTgl = array(1 => "m.tanggal_po", "b.tgl_kirim_po", "a.tanggal_loading");
$result = $con->getResult($sql);
// echo json_encode($result);
foreach ($result as $data) {
    $data = (object) $data;
    $terminal1 = $data->nama_terminal;
    $terminal2 = ($data->tanki_terminal) ? ' - ' . $data->tanki_terminal : '';
    $terminal3 = ($data->lokasi_terminal) ? ' [' . $data->lokasi_terminal . ']' : '';
    $terminal = $terminal1 . $terminal2 . $terminal3;
    $seg_aw     = ($data->nomor_segel_awal) ? str_pad($data->nomor_segel_awal, 4, '0', STR_PAD_LEFT) : '';
    $seg_ak     = ($data->nomor_segel_akhir) ? str_pad($data->nomor_segel_akhir, 4, '0', STR_PAD_LEFT) : '';
    if ($data->jumlah_segel == 1) {
        $nomor_segel = $data->pre_segel . "-" . $seg_aw;
    } else 
        if ($data->jumlah_segel == 2) {
        $nomor_segel = $data->pre_segel . "-" . $seg_aw . " - " . $data->pre_segel . "-" . $seg_ak;
    } else 
        if ($data->jumlah_segel > 2) {
        $nomor_segel = $data->pre_segel . "-" . $seg_aw . " s/d " . $data->pre_segel . "-" . $seg_ak;
    } else
        $nomor_segel = '';
    $data_[] = $data;
}
$content = [];
foreach ($data_ as $i => $row) {
    $content[] = array(
        ($i + 1),
        date('d-m-Y', strtotime($row->tanggal_loading)),
        $row->nama_customer,
        $row->alamat_survey,
        $row->nomor_plat,
        $row->nama_sopir,
        $row->volume_po,
        $row->fullname,
        $row->nomor_po,
        $row->produk,
        $row->no_spj,
        $nomor_segel,
        $row->nama_suplier,
        $terminal,
        $row->nomor_do,
        '-',
        '-',
        '-',
        '-',
        $row->nomor_order,
        '-',
        str_replace('"', '', $row->_status_pengiriman),
    );
}

$filename = "Rekap-Pengiriman-" . date('dmYHis') . '.xlsx';
$arrOp         = array(1 => "=", ">=", "<=");
header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
ob_end_clean();
header('Content-type: application/vnd.ms-excel');
// header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');

$sheet  = 'Sheet1';
$writer = new XLSXWriter();
$writer->writeSheetHeader($sheet, array('List Pengiriman' => 'string'));
$writer->newMergeCell($sheet, 'A1', 'V1');
$writer->writeSheetHeaderExt($sheet, array("" => "string"));
// $writer->setColumnIndex(2);

$header = array(
    'No' => 'string',
    'Tanggal Loading' => 'string',
    'Nama Customer' => 'string',
    'Alamat' => 'string',
    'Plat Nomor' => 'string',
    'Driver' => 'string',
    'Jumlah/Liter' => 'string',
    'MKT' => 'string',
    'No. PO' => 'string',
    'Product' => 'string',
    'No. SPJ' => 'string',
    'No. Segel' => 'string',
    'No. DO Admin' => 'string',
    'Hauler' => 'string',
    'Nomor DN' => 'string',
    'Kendala' => 'string',
    'Dispatcher' => 'string',
    'Status' => 'string',
    'No. Trip' => 'string',
    'No. Order' => 'string',
    'Logistik' => 'string',
    'Keterangan' => 'string'
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

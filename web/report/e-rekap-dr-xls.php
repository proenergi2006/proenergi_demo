<?php
session_start();
ini_set('memory_limit', '512M');
set_time_limit(300);
ob_start();
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
b.tanggal_pr,
b.nomor_pr,
d.nomor_poc,
e.nama_customer,
a.volume,
a.vol_ori,
a.vol_potongan,
h.volume as vol_split,
a.nomor_po_supplier,
h.nomor_po_supplier as nomor_split,
g.nama_terminal,
a.no_do_syop,
a.nomor_lo_pr,
a.is_split,
g.tanki_terminal,
c.tanggal_kirim,
f.nama_cabang,
i.is_loaded,
i.is_delivered,
i.is_cancel,
i.tanggal_loaded,
i.jam_loaded,
i.tanggal_cancel,
j.is_loaded as is_loaded_kapal,
j.is_delivered as is_delivered_kapal,
j.is_cancel as is_cancel_kapal,
j.tanggal_loaded as tanggal_loaded_kapal,
j.jam_loaded as jam_loaded_kapal, 
j.tanggal_cancel as tanggal_cancel_kapal

from pro_pr_detail a
join pro_pr b on a.id_pr = b.id_pr
join pro_po_customer_plan c on a.id_plan = c.id_plan 
join pro_po_customer d on c.id_poc = d.id_poc
join pro_customer e on d.id_customer = e.id_customer
join pro_master_cabang f on b.id_wilayah = f.id_master
join pro_master_terminal g on a.pr_terminal = g.id_master
LEFT join new_pro_inventory_potongan_stock h on a.id_prd = h.id_prd
left join pro_po_ds_detail i on a.id_prd = i.id_prd
left join pro_po_ds_kapal j on a.id_prd = j.id_prd
where 1=1";




if ($q1 != "") {
    $sql .= " and (
            UPPER(b.nomor_pr) LIKE '%" . strtoupper($q1) . "%' 
            OR UPPER(a.nomor_lo_pr) LIKE '%" . strtoupper($q1) . "%' 
            OR UPPER(g.nama_terminal) LIKE '%" . strtoupper($q1) . "%' 
            OR UPPER(g.tanki_terminal) LIKE '%" . strtoupper($q1) . "%' 
        )";
}
if ($q4 != "" && $q5 == "") {
    $sql .= " and b.tanggal_pr = '" . tgl_db($q4) . "'";
} else if ($q4 != "" && $q5 != "") {
    $sql .= " and b.tanggal_pr BETWEEN '" . tgl_db($q4) . "' AND '" . tgl_db($q5) . "'";
}

if ($q2 != "") {
    $sql .= " and b.id_wilayah = '" . $q2 . "'";
}

$sql .= "  ORDER BY b.tanggal_pr DESC";

$data_ = [];
$result = $con->getResult($sql);
// echo json_encode($result);
foreach ($result as $data) {
    $data = (object) $data;
    $purchasing_tanggal = $data->purchasing_tanggal;
    $tanggal_pr = $data->tanggal_pr;
    $nomor_pr = $data->nomor_pr;
    $nomor_poc = trim($data->nomor_poc);
    $nama_customer = $data->nama_customer;
    $volume = $data->volume;
    $vol_ori = $data->vol_ori;
    $vol_potongan = $data->vol_potongan;
    $vol_split = $data->vol_split;
    $nomor_po_supplier = $data->nomor_po_supplier;
    $nomor_po_split = $data->nomor_split;
    $nomor_lo_pr = $data->nomor_lo_pr;
    $no_do_syop = $data->no_do_syop;
    $terminal = $data->nama_terminal . ' ' . $data->tanki_terminal . ' ';
    $tanggal_kirim = $data->tanggal_kirim;
    $nama_cabang = $data->nama_cabang;
    $split = $data->is_split;
    $status = '';
    $status_kapal = '';



    $data_[] = $data;
}
$content = [];
foreach ($data_ as $i => $row) {
    $purchasing_tanggal = $row->purchasing_tanggal;
    $tanggal_pr = $row->tanggal_pr;
    $nomor_pr = $row->nomor_pr;
    $nomor_poc = trim($row->nomor_poc);
    $nama_customer = $row->nama_customer;
    $volume = $row->volume;
    $vol_ori = $row->vol_ori;
    $vol_potongan = $row->vol_potongan;
    $vol_split = $row->vol_split;
    $nomor_po_supplier = $row->nomor_po_supplier;
    $nomor_po_split = $row->nomor_split;
    $nomor_lo_pr = $row->nomor_lo_pr;
    $no_do_syop = $row->no_do_syop;
    $terminal = $row->nama_terminal . ' ' . $row->tanki_terminal . ' ';
    $tanggal_kirim = $row->tanggal_kirim;
    $nama_cabang = $row->nama_cabang;
    $split = "";
    if ($row->is_split == 0) {
        $split = 'Tidak';
    } elseif ($row->is_split == 1) {
        $split = 'Ya';
    }
    $status = "";
    $status_kapal = "";

    // Cek apakah ini trip dari truck atau kapal
    $is_trip_truck = isset($row->is_loaded) || isset($row->is_delivered) || isset($row->is_cancel);
    $is_trip_kapal = isset($row->is_loaded_kapal) || isset($row->is_delivered_kapal) || isset($row->is_cancel_kapal);

    if ($is_trip_truck) {
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
        } elseif ($row->is_loaded == 0 && $row->is_delivered == 0 && $row->is_cancel == 1) {
            $status = 'Cancel' . '' .
                'Tgl Cancel ' . tgl_indo($row->tanggal_cancel) . '';
        } else {
            $status = 'Belum Loading';
        }
    }

    if ($is_trip_kapal) {
        if ($row->is_loaded_kapal == 0 && $row->is_delivered_kapal == 0 && $row->is_cancel_kapal == 0) {
            $status_kapal = 'Belum Loading';
        } elseif ($row->is_loaded_kapal == 1 && $row->is_delivered_kapal == 0 && $row->is_cancel_kapal == 0) {
            $status_kapal = 'Loading' . ' ' .
                'Tgl Loading ' . tgl_indo($row->tanggal_loaded_kapal) . ' ' .
                'Jam Loading ' . ($row->jam_loaded_kapal) . ' ';
        } elseif ($row->is_loaded_kapal == 1 && $row->is_delivered_kapal == 1 && $row->is_cancel_kapal == 0) {
            $status_kapal =
                'Delivered' . ' ' .
                'Tgl Loading ' . tgl_indo($row->tanggal_loaded_kapal) . ' ' .
                'Jam Loading ' . ($row->jam_loaded_kapal) . ' ';
        } elseif ($row->is_loaded_kapal == 1 && $row->is_delivered_kapal == 0 && $row->is_cancel_kapal == 1) {
            $status_kapal = 'Cancel' . '' .
                'Tgl Cancel ' . tgl_indo($row->tanggal_cancel_kapal) . '';
        } elseif ($row->is_loaded_kapal == 0 && $row->is_delivered_kapal == 0 && $row->is_cancel_kapal == 1) {
            $status_kapal = 'Cancel' . '' .
                'Tgl Cancel ' . tgl_indo($row->tanggal_cancel_kapal) . '';
        } else {
            $status_kapal = 'Belum Loading';
        }
    }


    // Jika trip berasal dari kapal, kosongkan status truck
    if ($is_trip_kapal) {
        $status = "";
    }

    // Jika trip berasal dari truck, kosongkan status kapal
    if ($is_trip_truck) {
        $status_kapal = "";
    }

    $content[] = array(
        ($i + 1),
        date('d-m-Y', strtotime($row->purchasing_tanggal)),
        date('d-m-Y', strtotime($row->tanggal_pr)),
        $nomor_pr,
        $nomor_poc,
        $nama_customer,
        number_format($volume),
        number_format($vol_ori),
        number_format($vol_potongan),
        number_format($vol_split),
        $nomor_po_supplier,
        $nomor_po_split,
        $nomor_lo_pr,
        $no_do_syop,
        $terminal,
        date('d-m-Y', strtotime($row->tanggal_kirim)),
        $nama_cabang,
        $split,
        $status,
        $status_kapal,
    );
}

$filename = "Rekap-DR-" . date('dmYHis') . '.xlsx';
$arrOp         = array(1 => "=", ">=", "<=");


header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
ob_end_clean();
header('Content-type: application/vnd.ms-excel');
// header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');

$sheet  = 'Rekap DR';
$writer = new XLSXWriter();
$writer->writeSheetHeader($sheet, array('List Pengiriman' => 'string'));
$writer->newMergeCell($sheet, 'A1', 'V1');
$writer->writeSheetHeaderExt($sheet, array("" => "string"));
// $writer->setColumnIndex(2);

$header = array(
    'No' => 'string',
    "Tanggal Realese" => 'string',
    "Tanggal DR" => 'string',
    "Nomor DR" => 'string',
    "Nomor PO" => 'string',
    "Customer" => 'string',
    "Volume" => 'string',
    "Volume Ori" => 'string',
    "Volume Potongan" => 'string',
    "Volume Split" => 'string',
    "Nomor PO Supplier" => 'string',
    "Nomor PO Split" => 'string',
    "Nomor LO" => 'string',
    "Nomor DO" => 'string',
    "Terminal" => 'string',
    "Tanggal Kirim" => 'string',
    "Cabang" => 'string',
    "Split" => 'string',
    "Status DR Truck" => 'string',
    "Status DR Kapal" => 'string',



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

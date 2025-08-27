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
$q1 = isset($_REQUEST['q1']) ? $_REQUEST['q1'] : '';
// $q2 = isset($_REQUEST['q2']) ? $_REQUEST['q2'] : '';
$q3 = isset($_REQUEST['q3']) ? $_REQUEST['q3'] : '';
$q4 = isset($_REQUEST['q4']) ? $_REQUEST['q4'] : '';
$q5 = isset($_REQUEST['q5']) ? $_REQUEST['q5'] : '';
// $q6 = isset($_REQUEST['q6']) ? $_REQUEST['q6'] : '';

$seswil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);

$sql = "SELECT a.*,cp.volume_kirim, c. nama_customer, f.inisial_cabang, g.top_payment,h.fullname AS marketing, i.nama_prov, j.nama_kab, b.harga_asli, b.detail_rincian, b.oa_kirim,
b.other_cost, d.realisasi_volume,d.tanggal_loading, d.tanggal_loaded,d.jam_loading,d.tanggal_loaded, d.jam_loaded, d.tanggal_request, d.tanggal_delivered,
b.tol_susut, l.nama_transportir,k.nomor_plat,m.nama_sopir,l.nama_suplier, e.tgl_eta_po, e.jam_eta_po,n.no_do_syop, n.volume as volume_dr,
CASE WHEN COUNT(a.id_customer) OVER (PARTITION BY a.id_customer) = 1 THEN 'New' ELSE 'Existing' END AS status_customer, cp.tanggal_kirim
FROM pro_po_customer_plan cp
JOIN pro_po_customer a ON cp.id_poc = a.id_poc
JOIN pro_penawaran b ON a.id_penawaran = b.id_penawaran
JOIN pro_customer c ON a.id_customer=c.id_customer
JOIN pro_po_ds_detail d ON cp.id_plan=d.id_plan
JOIN pro_po_detail e ON d.id_prd = e.id_prd
JOIN pro_master_cabang f ON c.id_wilayah=f.id_master
JOIN pro_customer g ON a.id_customer=g.id_customer
JOIN acl_user h ON g.id_marketing=h.id_user
JOIN pro_master_provinsi i ON c.prov_customer = i.id_prov
JOIN pro_master_kabupaten j ON c.kab_customer= j.id_kab
JOIN pro_master_transportir_mobil k ON e.mobil_po=k.id_master
JOIN pro_master_transportir l ON k.id_transportir=l.id_master
JOIN pro_master_transportir_sopir m ON e.sopir_po=m.id_master
JOIN pro_pr_detail n ON e.id_plan=n.id_plan
JOIN pro_po o ON e.id_po = o.id_po
WHERE 1=1";

// Tambahkan kondisi pencarian jika ada
if ($q1 != "") {
    $sql .= " AND (
            UPPER(b.nomor_pr) LIKE '%" . strtoupper($q1) . "%' 
            OR UPPER(c.nomor_lo_pr) LIKE '%" . strtoupper($q1) . "%' 
            OR UPPER(h.nama_terminal) LIKE '%" . strtoupper($q1) . "%' 
            OR UPPER(h.tanki_terminal) LIKE '%" . strtoupper($q1) . "%' 
        )";
}
if ($q3 != "" && $q4 != "")
    $sql .= " and a.tanggal_poc between '" . tgl_db($q3) . "' and '" . tgl_db($q4) . "'";

if ($q5 != "") {
    $sql .= " and o.id_wilayah = '" . $q5 . "'";
}

if ($sesrol == 10) {
    $sql .= " and f.id_master = '$seswil'";
}

// if ($q6 != "") {
//     $sql .= " and d.id_terminal = '" . $q6 . "'";
// }
$sql .= " order by a.tanggal_poc desc";

$data_ = [];
$result = $con->getResult($sql);
// echo json_encode($result);
// var_dump($result);
// exit;
foreach ($result as $data) {
    $data = (object) $data;
    $customer = $data->nama_customer;
    $nama_cabang = $data->inisial_cabang;
    $nomor_po = $data->nomor_poc;
    $volume = $data->volume_kirim;
    $top_poc = $data->top_poc;
    $marketing = $data->marketing;
    $tanggal_po = $data->tanggal_poc;
    $month = date('M', strtotime($data->tanggal_poc));
    $week = weekOfMonth($data->tanggal_poc);
    $nama_prov = $data->nama_prov;
    $nama_kab = $data->nama_kab;
    $harga_asli = $data->harga_asli;
    $detail_rincian = $data->detail_rincian;

    $decode = json_decode($data->detail_rincian, true);

    $data_[] = $data;
}
$content = [];
foreach ($data_ as $i => $row) {
    $customer = $row->nama_customer;
    $nama_cabang = $row->inisial_cabang;
    $nomor_po = $row->nomor_poc;
    $volume = $row->volume_poc;
    $top_poc = $row->top_poc;
    $marketing = $row->marketing;
    $tanggal_po = $row->tanggal_poc;
    $month = date('M', strtotime($row->tanggal_poc));
    $week = weekOfMonth($row->tanggal_poc);
    $nama_prov = $row->nama_prov;
    $nama_kab = $row->nama_kab;
    $other_cost = (float) $row->other_cost;
    $harga_asli = (float) $row->harga_asli;
    $tgl_loading = $row->tanggal_loading ? date('d/M/y', strtotime($row->tanggal_loading)) . ' ' . $row->jam_loading : '';
    $tgl_loaded = $row->jam_loaded ? date('d/M/y', strtotime($row->tanggal_loaded)) . ' ' . $row->jam_loaded : '';
    $tanggal_request = $row->tanggal_request;
    $tanggal_delivered = $row->tanggal_delivered;
    $volume_dr = $row->volume_dr;
    $realisasi_volume = $row->realisasi_volume;
    $losses = $volume_dr - $realisasi_volume;
    $status = "";
    $tol_susut = (float)$row->tol_susut;
    $nama_transportir = $row->nama_transportir;
    $nomor_plat = $row->nomor_plat;
    $nama_sopir = $row->nama_sopir;
    $nama_suplier = $row->nama_suplier;
    $status_customer = $row->status_customer;
    $tgl_eta_po = $row->tgl_eta_po;
    $jam_eta_po = $row->jam_eta_po;
    $no_do_syop = $row->no_do_syop;
    $volume_kirim = $row->volume_kirim;

    // if ($row->is_loaded == 0 && $row->is_delivered == 0 && $row->is_cancel == 0) {
    //     $status = 'Belum Loading';
    // } elseif ($row->is_loaded == 1 && $row->is_delivered == 0 && $row->is_cancel == 0) {
    //     $status = 'Loading' . ' ' .
    //         'Tgl Loading ' . tgl_indo($row->tanggal_loaded) . ' ' .
    //         'Jam Loading ' . ($row->jam_loaded) . ' ';
    // } elseif ($row->is_loaded == 1 && $row->is_delivered == 1 && $row->is_cancel == 0) {
    //     $status =
    //         'Delivered' . ' ' .
    //         'Tgl Loading ' . tgl_indo($row->tanggal_loaded) . ' ' .
    //         'Jam Loading ' . ($row->jam_loaded) . ' ';
    // } elseif ($row->is_loaded == 1 && $row->is_delivered == 0 && $row->is_cancel == 1) {
    //     $status = 'Cancel' . '' .
    //         'Tgl Cancel ' . tgl_indo($row->tanggal_cancel) . '';
    // }

    $harga_pbbkb = 0;
    $harga_oa = 0;
    $harga_dasar = 0;
    $harga_ppn = 0;
    $nilai_ppn = 0;
    $nilai_pbbkb = 0;

    foreach ($decode as $d) {
        $rincian = $d['rincian'];
        if ($rincian == "Harga Dasar") {
            $harga_dasar = ($d['biaya']) ? $d['biaya'] : 0;
        }
        if ($rincian == "Ongkos Angkut") {
            $harga_oa = ($d['biaya']) ? $d['biaya'] : 0;
        }

        if ($rincian == "PPN") {
            $nilai_ppn = $d['nilai'];
            $harga_ppn = ($d['biaya']) ? $d['biaya'] : 0;
        }
        if ($rincian == "PBBKB") {
            $harga_pbbkb = ($d['biaya']) ? $d['biaya'] : 0;
            $nilai_pbbkb = $d['nilai'];
        }
    }

    $selling_price = $harga_asli + ($harga_asli * ((float)$nilai_ppn / 100)) + ($harga_asli * ((float)$nilai_pbbkb / 100)) + ($harga_oa + ($harga_oa * (float)$nilai_ppn / 100));
    $basic_price = $harga_asli - $other_cost;
    $losses_after = $losses - ($volume_dr * ($tol_susut / 100));
    $harga_losses = $losses_after * ($selling_price / $volume_dr);
    $total_revenue = $selling_price * $realisasi_volume;
    $nett_revenue = $basic_price * $realisasi_volume;

    // Gabungkan tanggal dan waktu
    $datetime_string = $tgl_eta_po . ' ' . $jam_eta_po;
    // Gunakan strtotime untuk mengubah string menjadi timestamp
    $timestamp = strtotime($datetime_string);
    // Format tanggal dan waktu sesuai format yang diinginkan
    $request_date = date('d-m-Y H:i:s', $timestamp);

    //menghitung diff time
    $request_timestamp = strtotime($request_date);
    $delivered_timestamp = strtotime($tanggal_delivered);

    // Hitung selisih waktu dalam detik
    $diff_time =  $delivered_timestamp - $request_timestamp;

    $content[] = array(
        ($i + 1),
        $month,
        $tanggal_po,
        $week,
        number_format($volume),
        trim($nomor_po),
        $customer,
        $top_poc,
        $marketing,
        $nama_cabang,
        $status_customer,
        $status,
        $nama_prov,
        $nama_kab,
        $no_do_syop,
        number_format($selling_price),
        $harga_oa,
        '-',
        $other_cost,
        $nilai_pbbkb . '%',
        number_format($harga_asli),
        number_format($basic_price),
        number_format($total_revenue),
        number_format($nett_revenue),
        '',
        '',
        $tgl_loaded,
        $request_date ? date('d/M/y H:i', strtotime($request_date)) : '',
        $tanggal_delivered ? date('d/M/y H:i', strtotime($tanggal_delivered)) : '',
        $diff_time,
        $diff_time > 0 ? 'Late' : 'On Time',
        '',
        '',
        number_format($volume_dr),
        number_format($realisasi_volume),
        $losses,
        $tol_susut . '%',
        $losses_after < 0 ? '-' : $losses_after,
        $harga_losses < 0 ? '-' : $harga_losses,
        $nama_suplier == 'Pro Energi' ? 'PE' : '3rd Party',
        $nama_transportir,
        $nama_sopir,
        $nomor_plat
    );
}
$filename = "Rekap-performance-" . date('dmYHis') . '.xlsx';
$arrOp         = array(1 => "=", ">=", "<=");
ob_end_clean();
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
// header('Content-type: application/vnd.ms-excel');
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');

$sheet  = 'Rekap performance';
$writer = new XLSXWriter();
$writer->writeSheetHeader($sheet, array('Rekap performance' => 'string'));
$writer->newMergeCell($sheet, 'A1', 'V1');
$writer->writeSheetHeaderExt($sheet, array("" => "string"));
$start = 2;
$patok = 1;
$writer->setColumnIndex($patok);

$header = array(
    "No" => 'string',
    "MONTH" => 'string',
    "PO DATE" => 'string',
    "WEEK" => 'string',
    "QTY PO" => 'string',
    "PO NUMBER" => 'string',
    "CUSTOMER" => 'string',
    "TOP" => 'string',
    "MKT" => 'string',
    "BRANCH" => 'string',
    "STATUS CUSTOMER" => 'string',
    "STATUS ORDER" => 'string',
    "PROVINSI" => 'string',
    "KABUPATEN/KOTA" => 'string',
    "DO NUMBER" => 'string',
    // "SECTOR" => 'string',
    "SELLING PRICE" => 'string',
    "OAT (excl PPN)" => 'string',
    "FEE" => 'string',
    "OTHER COST" => 'string',
    "PBBKB RATE" => 'string',
    "SELLING BASIC PRICE" => 'string',
    "BASIC PRICE(After Fee + Other Cost)" => 'string',
    "TOTAL REVENUE" => 'string',
    "NETT REVENUE" => 'string',
    "STATUS DELIVER" => 'string',
    "START LOADING TIME" => 'string',
    "FINISH LOADING TIME" => 'string',
    "REQUEST DATE DELIVER" => 'string',
    "ACTUAL DATE DELIVER" => 'string',
    "DIFF TIME(Minute)" => 'string',
    "ONTIME/LATE" => 'string',
    "LATE ISSUES" => 'string',
    "DETAIL ISSUE" => 'string',
    "DO QTY" => 'string',
    "ACTUAL RECEIVE QTY" => 'string',
    "LOSSES BEFORE TOLERANCE" => 'string',
    "TOLERANCE" => 'string',
    "LOSSES AFTER TOLERANCE" => 'string',
    "NOMINAL LOSSES PER LTR" => 'string',
    "VENDOR" => 'string',
    "VENDOR NAME" => 'string',
    "DRVER" => 'string',
    "PLATE NUMBER" => 'string',

);
$writer->writeSheetHeaderExt($sheet, $header);
$start++;

if (count($data_) > 0) {
    $tot1 = 0;
    $last = $start - 1;
    foreach ($content as $row) {
        $tot1++;
        $writer->writeSheetRow($sheet, $row);
        // $writer->writeSheetRow($sheet, array(
        // 	$tot1,
        //     date('M',strtotime($row->tanggal_poc)),
        //     $row->tanggal_poc,
        //     "W".weekOfMonth($row->tanggal_poc),
        //     $row->volume_poc,
        //     trim($row->nomor_poc),
        //     $row->nama_customer,
        //     $row->top_poc,
        //     $row->marketing,
        //     $row->inisial_cabang,
        //     '',
        //     $row->nama_prov,
        //     $row->nama_kab,
        //     '',
        //     ''
        // ));
    }
    $last++;
} else {
    $writer->writeSheetRow($sheet, array('Data tidak ada'));
    $writer->newMergeCell($sheet, 'A4', 'V4');
}

$con->close();
$writer->writeToStdOut();
exit(0);

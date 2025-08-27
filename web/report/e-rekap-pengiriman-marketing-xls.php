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
$arrTgl = array(1 => "e.tanggal_kirim");
$q1 = isset($_REQUEST['q1']) ? $_REQUEST['q1'] : '';
$q2 = isset($_REQUEST['q2']) ? $_REQUEST['q2'] : '';
$q3 = isset($_REQUEST['q3']) ? $_REQUEST['q3'] : '';
$q4 = isset($_REQUEST['q4']) ? $_REQUEST['q4'] : '';
$q5 = isset($_REQUEST['q5']) ? $_REQUEST['q5'] : '';
$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$seswil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

$sql = "

			 SELECT a.*,
       e.tanggal_kirim,
       c.no_spj,
       c.tgl_eta_po,
       c.jam_eta_po,
       j.nomor_plat,
       g.nama_sopir,
       i.nama_transportir,
       h.nama_customer,
       c.volume_po,
       k.yang_dibayarkan,
       k.jarak_real,
       l.nama_terminal,
       l.tanki_terminal,
       m.nama_cabang,
       o.nama_kab,
       p.pr_mobil
       
      
        FROM pro_po_ds_detail a
        JOIN pro_po_ds b ON a.id_ds = b.id_ds
        JOIN pro_po_detail c ON a.id_pod = c.id_pod
        JOIN pro_po d ON a.id_po = d.id_po
        JOIN pro_po_customer_plan e ON a.id_plan = e.id_plan
        JOIN pro_po_customer f ON a.id_poc = f.id_poc
        JOIN pro_master_transportir_sopir g ON c.sopir_po = g.id_master 
        JOIN pro_customer h ON f.id_customer = h.id_customer 
        JOIN pro_master_transportir i ON d.id_transportir = i.id_master 
        JOIN pro_master_transportir_mobil j ON c.mobil_po = j.id_master 
        left JOIN pro_bpuj k ON a.id_dsd = k.id_dsd
        JOIN pro_master_terminal l ON b.id_terminal = l.id_master 
        JOIN pro_master_cabang m ON b.id_wilayah = m.id_master
        JOIN pro_customer_lcr n on e.id_lcr = n.id_lcr
        JOIN pro_master_kabupaten o on n.kab_survey = o.id_kab
        JOIN pro_pr_detail p on a.id_prd = p.id_prd
		where 1=1
			
	";


// $res = $con->getResult($sql);
// echo json_encode($res);

// Tambahkan kondisi pencarian jika ada


if ($q5 != "") {
    $sql .= " and d.id_wilayah = '" . $q5 . "'";
} else if ($sesrol == 18) {
    $sql .= " and d.id_wilayah = '" . $seswil . "'";
}
if ($q1 != "") {
    $sql .= " AND (UPPER(h.nama_customer) LIKE '" . strtoupper($q1) . "%' 
                 OR UPPER(c.no_spj) = '" . strtoupper($q1) . "' 
                 OR UPPER(j.nomor_plat) = '" . strtoupper($q1) . "' 
                 OR UPPER(g.nama_sopir) LIKE '%" . strtoupper($q1) . "%' 
                 OR UPPER(i.nama_transportir) LIKE '%" . strtoupper($q1) . "%')";
}
if ($q2 != "" && $q3 != "" && $q4 == "")
    $sql .= " and " . $arrTgl[$q2] . " = '" . tgl_db($q3) . "'";
else if ($q2 != "" && $q3 != "" && $q4 != "")
    $sql .= " and " . $arrTgl[$q2] . " between '" . tgl_db($q3) . "' and '" . tgl_db($q4) . "'";

$sql .= "  order by a.tanggal_loading desc, a.jam_loading, a.nomor_urut_ds, a.id_dsd";

$data_ = [];
$arrTgl = array(1 => "e.tanggal_kirim");
$result = $con->getResult($sql);
// echo json_encode($result);
foreach ($result as $data) {
    $data = (object) $data;

    $tgl_loading = tgl_indo($data->tanggal_loaded) && $data->jam_loaded;
    $tgl_delivered = tgl_indo($data->tanggal_delivered) ?: '';
    $losses = ($data->realisasi_volume - $data->volume_po);
    $tgl1 = strtotime($data->tanggal_loaded . " " . $data->jam_loaded);

    // $terminal1 = $data->nama_terminal;
    // $terminal2 = ($data->tanki_terminal) ? ' - ' . $data->tanki_terminal : '';
    // $terminal3 = ($data->lokasi_terminal) ? ' [' . $data->lokasi_terminal . ']' : '';
    // $terminal = $terminal1 . $terminal2 . $terminal3;
    // $seg_aw     = ($data->nomor_segel_awal) ? str_pad($data->nomor_segel_awal, 4, '0', STR_PAD_LEFT) : '';
    // $seg_ak     = ($data->nomor_segel_akhir) ? str_pad($data->nomor_segel_akhir, 4, '0', STR_PAD_LEFT) : '';
    // if ($data->jumlah_segel == 1) {
    //     $nomor_segel = $data->pre_segel . "-" . $seg_aw;
    // } else 
    //     if ($data->jumlah_segel == 2) {
    //     $nomor_segel = $data->pre_segel . "-" . $seg_aw . " - " . $data->pre_segel . "-" . $seg_ak;
    // } else 
    //     if ($data->jumlah_segel > 2) {
    //     $nomor_segel = $data->pre_segel . "-" . $seg_aw . " s/d " . $data->pre_segel . "-" . $seg_ak;
    // } else
    //     $nomor_segel = '';
    $data_[] = $data;
}
$content = [];
foreach ($data_ as $i => $row) {

    $tanggal_loaded = !empty($row->tanggal_loaded) ? date('d-m-Y', strtotime($row->tanggal_loaded)) : '';
    $jam_loaded = !empty($row->jam_loaded) ? date('H:i', strtotime($row->jam_loaded)) : '';
    $tanggal_eta =  !empty($row->tgl_eta_po) ? date('d-m-Y', strtotime($row->tgl_eta_po)) : '';
    $jam_eta = !empty($row->jam_eta_po) ? date('H:i', strtotime($row->jam_eta_po)) : '';
    $tanggal_jam_loaded = ($tanggal_loaded && $jam_loaded) ? $tanggal_loaded . ' ' . $jam_loaded : '';
    $tanggal_jam_eta = ($tanggal_eta && $jam_eta) ? $tanggal_eta . ' ' . $jam_eta : '';
    $losses = ($row->realisasi_volume > 0) ? $row->realisasi_volume - $row->volume_po : 0;
    $gain   = ($row->realisasi_volume > $row->volume_po) ? $row->realisasi_volume - $row->volume_po : 0;
    $persen_losses = ($row->volume_po != 0) ? ($losses / $row->volume_po) * 100 : 0;
    // Pastikan tanggal_loaded dan jam_loaded tidak kosong sebelum melakukan perhitungan strtotime
    if (!empty($row->tanggal_loaded) && !empty($row->jam_loaded)) {
        $tgl1 = strtotime($row->tanggal_loaded . " " . $row->jam_loaded);
    } else {
        $tgl1 = null;
    }

    if (empty($row->tanggal_delivered) || $tgl1 === null) {
        $leadtm = null; // Atau bisa diganti dengan nilai lain seperti '' atau 0 tergantung kebutuhan
    } else {
        $tgl2 = strtotime($row->tanggal_delivered);
        $leadtm = ($tgl2 - $tgl1);
    }

    $arrMobil = array(1 => "Truck", "Kapal", "Loco");

    $content[] = array(
        ($i + 1),
        date('d-m-Y', strtotime($row->tanggal_kirim)),
        $row->nama_cabang,
        $row->nama_transportir,
        $row->nama_customer,
        number_format($row->volume_po),
        $tanggal_jam_eta,
        $tanggal_jam_loaded,
        !empty($row->tanggal_delivered) ? date('d-m-Y H:i', strtotime($row->tanggal_delivered)) : '',
        timeManHours($leadtm),

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
    "Date" => 'string',
    "Area" => 'string',
    "Transporter" => 'string',
    "Customer" => 'string',
    "Qty Delivery (L)" => 'string',
    "ETA" => 'string',
    "At Terminal" => 'string',
    "At Customer" => 'string',
    "Delivery Time" => 'string',

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

<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "pdfgen");

$auth    = new MyOtentikasi();
$con     = new Connection();
$flash    = new FlashAlerts;
$enk      = decode($_SERVER['REQUEST_URI']);
$idr     = isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';

$cek = "select a.*, b.inisial_segel, b.kode_barcode, c.nama_terminal, c.lokasi_terminal, c.initial, d.nama_suplier, e.volume, g.nama_customer, h.alamat_survey, a.bl_lo_jumlah, i.nama_suplier, i.alamat_suplier 
			from pro_po_ds_kapal a 
			join pro_master_cabang b on a.id_wilayah = b.id_master 
			join pro_master_terminal c on a.terminal = c.id_master join pro_master_transportir d on a.transportir = d.id_master 
			join pro_pr_detail e on a.id_prd = e.id_prd 
			join pro_po_customer f on a.id_poc = f.id_poc
			join pro_customer g on f.id_customer = g.id_customer
            join pro_customer_lcr h on g.id_customer = h.id_customer
            join pro_master_transportir i on a.transportir = i.id_master
			where a.id_dsk = '" . $idr . "'";
$row = $con->getRecord($cek);
$note     = ($row['keterangan']) ? str_replace("<br />", PHP_EOL, $row['keterangan']) : '&nbsp;';
$tank     = json_decode($row['tank_seal'], true);
$mani     = json_decode($row['manifold_seal'], true);
$pump     = json_decode($row['pump_seal'], true);
$other     = json_decode($row['other_seal'], true);
$printe = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]) . " " . date("d/m/Y H:i:s") . " WIB";
$barcod = $row['kode_barcode'] . '07' . str_pad($row['id_dsk'], 6, '0', STR_PAD_LEFT);

$mani_kiri_awal  = ($mani['mani_kiri_awal']) ? str_pad($mani['mani_kiri_awal'], 4, '0', STR_PAD_LEFT) : '';
$mani_kiri_akhir = ($mani['mani_kiri_akhir']) ? str_pad($mani['mani_kiri_akhir'], 4, '0', STR_PAD_LEFT) : '';
if ($mani['jumlah_kiri'] == 1)
    $mani_kiri = $row['inisial_segel'] . "-" . $mani_kiri_awal;
else if ($mani['jumlah_kiri'] == 2)
    $mani_kiri = $row['inisial_segel'] . "-" . $mani_kiri_awal . " &amp; " . $row['inisial_segel'] . "-" . $mani_kiri_akhir;
else if ($mani['jumlah_kiri'] > 2)
    $mani_kiri = $row['inisial_segel'] . "-" . $mani_kiri_awal . " s/d " . $row['inisial_segel'] . "-" . $mani_kiri_akhir;
else $mani_kiri = '';

$mani_kanan_awal  = ($mani['mani_kanan_awal']) ? str_pad($mani['mani_kanan_awal'], 4, '0', STR_PAD_LEFT) : '';
$mani_kanan_akhir = ($mani['mani_kanan_akhir']) ? str_pad($mani['mani_kanan_akhir'], 4, '0', STR_PAD_LEFT) : '';
if ($mani['jumlah_kanan'] == 1)
    $mani_kanan = $row['inisial_segel'] . "-" . $mani_kanan_awal;
else if ($mani['jumlah_kanan'] == 2)
    $mani_kanan = $row['inisial_segel'] . "-" . $mani_kanan_awal . " &amp; " . $row['inisial_segel'] . "-" . $mani_kanan_akhir;
else if ($mani['jumlah_kanan'] > 2)
    $mani_kanan = $row['inisial_segel'] . "-" . $mani_kanan_awal . " s/d " . $row['inisial_segel'] . "-" . $mani_kanan_akhir;
else $mani_kanan = '';

$pump_kiri_awal  = ($pump['pump_kiri_awal']) ? str_pad($pump['pump_kiri_awal'], 4, '0', STR_PAD_LEFT) : '';
$pump_kiri_akhir = ($pump['pump_kiri_akhir']) ? str_pad($pump['pump_kiri_akhir'], 4, '0', STR_PAD_LEFT) : '';
if ($pump['jumlah_kiri'] == 1)
    $pump_kiri = $row['inisial_segel'] . "-" . $pump_kiri_awal;
else if ($pump['jumlah_kiri'] == 2)
    $pump_kiri = $row['inisial_segel'] . "-" . $pump_kiri_awal . " &amp; " . $row['inisial_segel'] . "-" . $pump_kiri_akhir;
else if ($pump['jumlah_kiri'] > 2)
    $pump_kiri = $row['inisial_segel'] . "-" . $pump_kiri_awal . " s/d " . $row['inisial_segel'] . "-" . $pump_kiri_akhir;
else $pump_kiri = '';

$pump_kanan_awal  = ($pump['pump_kanan_awal']) ? str_pad($pump['pump_kanan_awal'], 4, '0', STR_PAD_LEFT) : '';
$pump_kanan_akhir = ($pump['pump_kanan_akhir']) ? str_pad($pump['pump_kanan_akhir'], 4, '0', STR_PAD_LEFT) : '';
if ($pump['jumlah_kanan'] == 1)
    $pump_kanan = $row['inisial_segel'] . "-" . $pump_kanan_awal;
else if ($pump['jumlah_kanan'] == 2)
    $pump_kanan = $row['inisial_segel'] . "-" . $pump_kanan_awal . " &amp; " . $row['inisial_segel'] . "-" . $pump_kanan_akhir;
else if ($pump['jumlah_kanan'] > 2)
    $pump_kanan = $row['inisial_segel'] . "-" . $pump_kanan_awal . " s/d " . $row['inisial_segel'] . "-" . $pump_kanan_akhir;
else $pump_kanan = '';

ob_start();
require_once(realpath("./template/po-kapal.php"));
$content = ob_get_clean();
ob_end_flush();
$con->close();

$mpdf = null;
if (PHP_VERSION >= 5.6) {
    $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
} else
    $mpdf = new mPDF('c', 'A4', 9, 'arial', 10, 10, 10, 10, 0, 5);
$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($content);
$filename = "PO_KAPAL_" . sanitize_filename($idr);
$mpdf->Output($filename . '_' . date('dmyHis') . '.pdf', 'I');
exit;

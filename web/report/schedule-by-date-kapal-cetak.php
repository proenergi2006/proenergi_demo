<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "pdfgen");


$auth    = new MyOtentikasi();
$con     = new Connection();
$flash    = new FlashAlerts;
$enk   = decode($_SERVER['REQUEST_URI']);
$q1    = htmlspecialchars($enk["q1"], ENT_QUOTES);
$q2    = htmlspecialchars($enk["q2"], ENT_QUOTES);
$q3    = htmlspecialchars($enk["q3"], ENT_QUOTES);
$q4    = htmlspecialchars($enk["q4"], ENT_QUOTES);
$q5    = htmlspecialchars($enk["q5"], ENT_QUOTES);
$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

$whereadd = '';
if ($sesrol > 1) {
    $whereadd = " and i.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
}
if ($q1) {
    $where1 .= " and a.tanggal_loading between '" . tgl_db($q1) . " 00:00:00' and '" . tgl_db($q1) . " 23:59:59'";
}

if ($q2 && !$q3) {
    $where1 .= " and a.tanggal_loading between '" . tgl_db($q2) . " 00:00:00' and '" . tgl_db($q2) . " 23:59:59'";
} else if ($q2 && $q3) {
    $where1 .= " and a.tanggal_loading between '" . tgl_db($q2) . " 00:00:00' and '" . tgl_db($q3) . " 23:59:59'";
}

if ($q4) {
    $where1 .= "  and b.pr_terminal = '" . $q4 . "'";
}


if ($q5) {
    $q5 = strtoupper($q5); // Ubah menjadi uppercase di awal untuk efisiensi
    $where1 .= " and (UPPER(j.nama_terminal) LIKE '" . $q5 . "%' 
                    OR UPPER(j.tanki_terminal) = '" . $q5 . "' 
                    OR UPPER(i.nomor_pr) = '" . $q5 . "' 
                    OR UPPER(b.nomor_lo_pr) LIKE '%" . $q5 . "%' 
                    OR UPPER(a.captain) LIKE '%" . $q5 . "%')";
}



$sql = " select a.*,
            i.nomor_pr, b.nomor_lo_pr, j.nama_terminal, j.tanki_terminal, k.nama_suplier, b.volume
            from pro_po_ds_kapal a 
            join pro_pr_detail b on a.id_prd = b.id_prd 
            join pro_po_customer_plan c on a.id_plan = c.id_plan 
            join pro_po_customer d on c.id_poc = d.id_poc 
            join pro_pr i on a.id_pr = i.id_pr 
            join pro_master_terminal j on b.pr_terminal = j.id_master 
            join pro_master_transportir k on a.transportir = k.id_master
            where a.is_cancel != 1" . $whereadd . $where1;



$res = $con->getResult($sql);
$printe = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]) . " " . date("d/m/Y H:i:s") . " WIB";

$barcod = $res[0]['kode_barcode'] . '05' . str_pad($idr, 6, '0', STR_PAD_LEFT);


ob_start();
require_once(realpath("schedule-by-date-kapal.php"));
$content = ob_get_clean();
ob_end_flush();
$con->close();

$mpdf = null;
if (PHP_VERSION >= 5.6) {
    $mpdf = new \Mpdf\Mpdf(['format' => 'A4', 'default_font' => 'Arial']);
} else
    $mpdf = new mPDF('c', 'A4', 9, 'arial', 10, 10, 10, 10, 0, 5);
// Memecah HTML menjadi potongan-potongan kecil dan menuliskannya ke mPDF
$mpdf->AddPage('L');
$mpdf->SetDisplayMode('fullpage');
$mpdf->use_kwt = true;
$mpdf->autoPageBreak = true;
$mpdf->setAutoTopMargin = 'stretch';
$mpdf->setAutoBottomMargin = 'stretch';
$mpdf->WriteHTML($content);

$filename = "DS_" . sanitize_filename($idr);
$mpdf->Output($filename . '_' . date('dmyHis') . '.pdf', 'I');
exit;

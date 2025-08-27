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
    $whereadd = " and a.is_cancel = 0 and c.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
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
    $where1 .= "  and b.terminal_po = '" . $q4 . "'";
}

if ($q5) {
    $where1 .= " and (upper(r.nama_terminal) like '" . strtoupper($q5) . "%' or upper(r.tanki_terminal) = '" . strtoupper($q5) . "' or upper(c.nomor_po) = '" . strtoupper($q5) . "' 
                    or upper(n.nama_sopir) like '%" . strtoupper($q5) . "%' or upper(m.nomor_plat) like '%" . strtoupper($q5) . "%')";
}


$sql = "select a.*, h.kode_pelanggan, h.id_customer, h.nama_customer, g.id_lcr, g.alamat_survey, i.nama_prov, j.nama_kab, q.fullname, l.nama_area, 
            o.nama_transportir, o.nama_suplier, b.no_spj, b.mobil_po, m.nomor_plat, n.nama_sopir, b.volume_po, p.wilayah_angkut, d.produk,  d.is_split, d.vol_potongan,
            c.nomor_po, b.multidrop_po, b.trip_po, b.nomor_oslog, d.no_do_acurate, d.nomor_lo_pr, f.nomor_poc, e.tanggal_kirim, e.volume_kirim, c.id_wilayah,  r.nama_terminal, r.tanki_terminal,
            t.volume as volume_potong,pt.nama_terminal AS terminal_potong,pt.tanki_terminal AS tanki_potong
            from pro_po_ds_detail a 
            join pro_po_detail b on a.id_pod = b.id_pod 
            join pro_po c on a.id_po = c.id_po 
            join pro_pr_detail d on a.id_prd = d.id_prd 
            join pro_po_customer_plan e on a.id_plan = e.id_plan 
            join pro_po_customer f on e.id_poc = f.id_poc 
            join pro_customer_lcr g on e.id_lcr = g.id_lcr
            join pro_customer h on f.id_customer = h.id_customer 
            join pro_master_provinsi i on g.prov_survey = i.id_prov 
            join pro_master_kabupaten j on g.kab_survey = j.id_kab
            join pro_penawaran k on f.id_penawaran = k.id_penawaran  
            join pro_master_area l on k.id_area = l.id_master 
            join pro_master_transportir_mobil m on b.mobil_po = m.id_master 
            join pro_master_transportir_sopir n on b.sopir_po = n.id_master 
            join pro_master_transportir o on c.id_transportir = o.id_master 
            join pro_master_wilayah_angkut p on g.id_wil_oa = p.id_master and g.prov_survey = p.id_prov and g.kab_survey = p.id_kab 
            join acl_user q on h.id_marketing = q.id_user 
            join pro_master_terminal r on b.terminal_po = r.id_master 
            join pro_pr s on a.id_pr = s.id_pr 
            LEFT JOIN new_pro_inventory_potongan_stock t ON d.id_prd = t.id_prd
            LEFT JOIN pro_master_terminal pt ON pt.id_master = t.pr_terminal
            where 1=1" . $whereadd . $where1;



$res = $con->getResult($sql);
$printe = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]) . " " . date("d/m/Y H:i:s") . " WIB";

$barcod = $res[0]['kode_barcode'] . '05' . str_pad($idr, 6, '0', STR_PAD_LEFT);


ob_start();
require_once(realpath("schedule-by-date.php"));
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

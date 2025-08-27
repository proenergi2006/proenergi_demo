<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "pdfgen");

$auth    = new MyOtentikasi();
$con     = new Connection();
$flash    = new FlashAlerts;
$enk     = decode($_SERVER['REQUEST_URI']);
$id_dsd    = htmlspecialchars($enk['id_dsd'], ENT_QUOTES);

$sql = "select a.*, i.nama_customer, e.alamat_survey, f.nama_prov, g.nama_kab, o.nama_terminal, o.tanki_terminal, o.lokasi_terminal, b.no_spj, k.nomor_plat, q.kode_barcode, 
			l.nama_sopir, b.volume_po, j.jenis_produk, j.merk_dagang, n.nama_transportir, n.nama_suplier, p.created_by, p.nomor_ds, o.telp_terminal, o.fax_terminal, o.cc_terminal 
			from pro_po_ds_detail a join pro_po_detail b on a.id_pod = b.id_pod 
			join pro_pr_detail c on a.id_prd = c.id_prd 
			join pro_po_customer_plan d on a.id_plan = d.id_plan 
			join pro_customer_lcr e on d.id_lcr = e.id_lcr
			join pro_master_provinsi f on e.prov_survey = f.id_prov 
			join pro_master_kabupaten g on e.kab_survey = g.id_kab
			join pro_po_customer h on d.id_poc = h.id_poc 
			join pro_customer i on h.id_customer = i.id_customer 
			join pro_master_produk j on h.produk_poc = j.id_master 
			join pro_master_transportir_mobil k on b.mobil_po = k.id_master 
			join pro_master_transportir_sopir l on b.sopir_po = l.id_master
			join pro_po m on a.id_po = m.id_po 
			join pro_master_transportir n on m.id_transportir = n.id_master 
			join pro_master_terminal o on b.terminal_po = o.id_master  
			join pro_po_ds p on a.id_ds = p.id_ds 
			join pro_master_cabang q on p.id_wilayah = q.id_master 
			where a.id_dsd = '" . $id_dsd . "' order by a.is_cancel, a.nomor_urut_ds, a.tanggal_loading, a.jam_loading, a.id_dsd";
$res = $con->getResult($sql);
$att = json_decode($res[0]['att_suplier'], true);
$printe = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]) . " " . date("d/m/Y H:i:s") . " WIB";

$code =  $id_dsd;
// $barcod = BASE_URL . "/barcode_result.php?idr=" . paramEncrypt($code);

$barcod = 'http://system.proenergi.com/customer/barcode/' . paramEncrypt($code);
// $barcod = $res[0]['kode_barcode'] . '03' . str_pad($idr, 6, '0', STR_PAD_LEFT);
// echo $barcod;

ob_start();
require_once(realpath("./template/delivery-order-qr.php"));
$content = ob_get_clean();
ob_end_flush();
$con->close();

if (PHP_VERSION >= 5.6) {
    $mpdf = new \Mpdf\Mpdf([
        'mode'          => 'utf-8',
        'format'        => [50, 50],    // [width(mm), height(mm)]
        'margin_left'   => 0,
        'margin_right'  => 0,
        'margin_top'    => 0,
        'margin_bottom' => 0,
    ]);
} else {
    $mpdf = new mPDF(
        '',           // mode
        [50, 50],     // custom page size dalam mm
        10,           // default font size
        'arial',      // default font family
        0,
        0,
        0,
        0,
        0,
        0 // margin kiri, kanan, atas, bawah, header, footer
    );
}

// Tampilkan penuh halaman dan tulis HTML
$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($content);

// Output PDF langsung ke browser
$filename = "QR_" . sanitize_filename($id_dsd) . '_' . date('dmyHis') . '.pdf';
$mpdf->Output($filename, 'I');
exit;

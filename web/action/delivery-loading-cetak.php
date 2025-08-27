<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "pdfgen");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;
$enk  	= decode($_SERVER['REQUEST_URI']);
$idr 	= isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
$code 	= isset($enk["code"]) ? htmlspecialchars($enk["code"], ENT_QUOTES) : '';

$sql = "select a.*, i.nama_customer, e.alamat_survey, f.nama_prov, g.nama_kab, o.nama_terminal, o.tanki_terminal, o.lokasi_terminal,o.initial, b.no_spj, k.nomor_plat, c.nomor_lo_pr, c.no_do_syop,
			l.nama_sopir, b.volume_po, j.jenis_produk, j.merk_dagang, n.nama_transportir, n.nama_suplier, p.created_by, q.kode_barcode, i.kode_pelanggan, b.tgl_eta_po,  m.id_wilayah, p.id_terminal, i.print_product, h.nomor_poc
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
			
			where a.id_ds = '" . $idr . "' and a.is_cancel = 0 order by a.nomor_urut_ds, a.tanggal_loading, a.jam_loading, a.id_ds";
$res = $con->getResult($sql);
$created = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]);
$printe = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]) . " " . date("d/m/Y H:i:s") . " WIB";
$barcod = $res[0]['kode_barcode'] . '06' . str_pad($idr, 6, '0', STR_PAD_LEFT);
ob_start();
require_once(realpath("./template/delivery-loading-dn.php"));
$content = ob_get_clean();
ob_end_flush();
$con->close();

$mpdf = null;
if (PHP_VERSION >= 5.6) {
	$mpdf = new \Mpdf\Mpdf(['format' => 'Letter']);
} else
	$mpdf = new mPDF('c', 'Letter');
$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($content);
$filename = "DN_" . sanitize_filename($idr);
$mpdf->Output($filename . '_' . date('dmyHis') . '.pdf', 'I');
exit;

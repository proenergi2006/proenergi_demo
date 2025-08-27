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

$sql = "select a.*, b.nomor_po, b.tanggal_po, k.nama_suplier, k.att_suplier, k.fax_suplier, k.telp_suplier, l.nomor_plat, m.nama_sopir, n.nama_terminal, c.is_approved, c.nomor_lo_pr, c.no_do_syop,
			h.nomor_poc, i.nama_customer, j.fullname, e.alamat_survey, e.picustomer, f.nama_prov, g.nama_kab, o.nama_cabang, c.produk, b.created_by, b.tgl_approved, i.print_product,
			p.is_cancel, o.kode_barcode, d.status_jadwal, d.tanggal_kirim
			from pro_po_detail a join pro_po b on a.id_po = b.id_po 
			join pro_pr_detail c on a.id_prd = c.id_prd
			join pro_po_customer_plan d on a.id_plan = d.id_plan 
			join pro_customer_lcr e on d.id_lcr = e.id_lcr
			join pro_master_provinsi f on e.prov_survey = f.id_prov 
			join pro_master_kabupaten g on e.kab_survey = g.id_kab
			join pro_po_customer h on d.id_poc = h.id_poc 
			join pro_customer i on h.id_customer = i.id_customer 
			join acl_user j on i.id_marketing = j.id_user 
			join pro_master_transportir k on b.id_transportir = k.id_master 
			join pro_master_transportir_mobil l on a.mobil_po = l.id_master 
			join pro_master_transportir_sopir m on a.sopir_po = m.id_master 
			join pro_master_terminal n on a.terminal_po = n.id_master 
			join pro_master_cabang o on b.id_wilayah = o.id_master 
			left join pro_po_ds_detail p on a.id_pod = p.id_pod
			where a.id_po = '" . $idr . "' and a.pod_approved = 1 and (p.is_cancel is not null or p.is_cancel = 0) order by a.no_urut_po";
$res = $con->getResult($sql);
$code   = $res[0]['kode_barcode'] . '-' . '04' . '-' . $idr;
$bar    = BASE_URL . "/barcode_result.php?idr=" . paramEncrypt($code);
$printe = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]) . " " . date("d/m/Y H:i:s") . " WIB";
$created = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]);
ob_start();
require_once(realpath("./template/surat-jalan.php"));
$content = ob_get_clean();
ob_end_flush();
$con->close();

$mpdfOpt1 	= array(
	'mode' => 'c',
	'format' => 'A4',
	'default_font_size' => '10',
	'default_font' => 'arial',
	'margin_left' => '15',
	'margin_right' => '15',
	'margin_top' => '15',
	'margin_bottom' => '15',
	'margin_header' => '5',
	'margin_footer' => '5'
);

$mpdf = null;
if (PHP_VERSION >= 5.6) {
	$mpdf = new \Mpdf\Mpdf($mpdfOpt1);
} else {
	$mpdf = new mPDF('c', 'A4', 10, 'arial', 15, 15, 15, 15, 5, 5);
}

/*$mpdf = null;
	if (PHP_VERSION >= 5.6) {
		$mpdf = new \Mpdf\Mpdf(['format' => [216, 139]]);
	} else {
		$mpdf = new mPDF('c',array(215,139),10,'arial',10,10,10,10,0,5); 
		$mpdf->AddPage('P');
	}*/
$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($content);
$filename = "SPJ_" . sanitize_filename($idr);
$mpdf->Output($filename . '_' . date('dmyHis') . '.pdf', 'I');
exit;

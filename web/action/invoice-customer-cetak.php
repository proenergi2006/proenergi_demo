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
$tipe 	= isset($enk["tipe"]) ? htmlspecialchars($enk["tipe"], ENT_QUOTES) : '';
$sess_wil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

$sql = "
	select a.*, b.nama_customer as nm_customer, c.nama_cabang, c.id_master as id_cabang, d.fullname as marketing, b.alamat_customer, e.nama_prov, f.nama_kab, b.postalcode_customer as kode_pos
	from pro_invoice_admin a 
	join pro_customer b on a.id_customer = b.id_customer 
	join pro_master_cabang c on b.id_wilayah = c.id_master
	join acl_user d ON d.id_user=b.id_marketing
	join pro_master_provinsi e on e.id_prov=b.prov_customer
	join pro_master_kabupaten f on f.id_kab=b.kab_customer
	where 1=1 and a.id_invoice = '" . $idr . "'
";
$res = $con->getRecord($sql);

if ($res['id_approval'] == NULL) {
	$approval = "PT Pro Energi";
	$jabatan = "";
} else {
	$sql_approval = "SELECT * FROM pro_master_approval_invoice WHERE id_master='" . $res['id_approval'] . "'";
	$res_approval = $con->getRecord($sql_approval);
	$approval = $res_approval['nama'];
	$jabatan = $res_approval['jabatan'];
}


$sql02 = "select 
a.*, b.nomor_do as no_dn, k1.nomor_plat as angkutan, l1.nama_sopir as sopir, d.nomor_poc, b.realisasi_volume, d.top_poc, c.tanggal_kirim, e.produk, g.wilayah_angkut, h.nama_prov as provinsi_angkut, i.nama_kab as kab_angkut, f.alamat_survey, j.gabung_oa, j.all_in, j.gabung_pbbkb, j.gabung_pbbkboa, j.detail_rincian, j.pembulatan
from pro_invoice_admin_detail a 
join pro_po_ds_detail b on a.id_dsd = b.id_dsd and a.jenisnya = 'truck' 
join pro_po_customer_plan c on b.id_plan = c.id_plan 
join pro_po_customer d on c.id_poc = d.id_poc 
join pro_pr_detail e on b.id_prd = e.id_prd
join pro_customer_lcr f on c.id_lcr = f.id_lcr
left join pro_master_wilayah_angkut g on f.id_wil_oa = g.id_master and f.prov_survey = g.id_prov and f.kab_survey = g.id_kab
join pro_master_provinsi h on h.id_prov=g.id_prov
join pro_master_kabupaten i on i.id_kab=g.id_kab
join pro_penawaran j on d.id_penawaran=j.id_penawaran
join pro_po_detail b1 on b.id_pod = b1.id_pod 
join pro_master_transportir_mobil k1 on b1.mobil_po = k1.id_master 
join pro_master_transportir_sopir l1 on b1.sopir_po = l1.id_master
where 1=1 and a.id_invoice = '" . $idr . "'
UNION ALL 
select 
a.*, b.nomor_dn_kapal as no_dn, b.vessel_name as angkutan, b.kapten_name as sopir, e.nomor_poc, b.realisasi_volume, e.top_poc, d.tanggal_kirim, c.produk, g.wilayah_angkut, h.nama_prov as provinsi_angkut, i.nama_kab as kab_angkut, f.alamat_survey, j.gabung_oa, j.all_in, j.gabung_pbbkb, j.gabung_pbbkboa, j.detail_rincian, j.pembulatan
from pro_invoice_admin_detail a 
join pro_po_ds_kapal b on a.id_dsd = b.id_dsk and a.jenisnya = 'kapal' 
join pro_pr_detail c on b.id_prd = c.id_prd 
join pro_po_customer_plan d on c.id_plan = d.id_plan 
join pro_po_customer e on d.id_poc = e.id_poc 
join pro_customer_lcr f on d.id_lcr = f.id_lcr
left join pro_master_wilayah_angkut g on f.id_wil_oa = g.id_master and f.prov_survey = g.id_prov and f.kab_survey = g.id_kab
join pro_master_provinsi h on h.id_prov=g.id_prov
join pro_master_kabupaten i on i.id_kab=g.id_kab
join pro_penawaran j on e.id_penawaran=j.id_penawaran
where 1=1 and a.id_invoice = '" . $idr . "' 
order by id_invoice_detail ";

$res02 = $con->getResult($sql02);

$sql03 = "
		select 
		a.*, b.nomor_do as no_dn, k1.nomor_plat as angkutan, l1.nama_sopir as sopir, d.nomor_poc, b.realisasi_volume, d.top_poc, c.tanggal_kirim, e.produk, g.wilayah_angkut, h.nama_prov as provinsi_angkut, i.nama_kab as kab_angkut, f.alamat_survey, j.gabung_oa, j.all_in, j.gabung_pbbkb, j.gabung_pbbkboa, j.id_penawaran, j.pembulatan
		from pro_invoice_admin_detail a 
		join pro_po_ds_detail b on a.id_dsd = b.id_dsd and a.jenisnya = 'truck' 
		join pro_po_customer_plan c on b.id_plan = c.id_plan 
		join pro_po_customer d on c.id_poc = d.id_poc 
		join pro_pr_detail e on b.id_prd = e.id_prd
		join pro_customer_lcr f on c.id_lcr = f.id_lcr
		left join pro_master_wilayah_angkut g on f.id_wil_oa = g.id_master and f.prov_survey = g.id_prov and f.kab_survey = g.id_kab
		join pro_master_provinsi h on h.id_prov=g.id_prov
		join pro_master_kabupaten i on i.id_kab=g.id_kab
		join pro_penawaran j on d.id_penawaran=j.id_penawaran
		join pro_po_detail b1 on b.id_pod = b1.id_pod 
		join pro_master_transportir_mobil k1 on b1.mobil_po = k1.id_master 
		join pro_master_transportir_sopir l1 on b1.sopir_po = l1.id_master
		where 1=1 and a.id_invoice = '" . $idr . "'
		UNION ALL 
		select 
		a.*, b.nomor_dn_kapal as no_dn, b.vessel_name as angkutan, b.kapten_name as sopir, e.nomor_poc, b.realisasi_volume, e.top_poc, d.tanggal_kirim, c.produk, g.wilayah_angkut, h.nama_prov as provinsi_angkut, i.nama_kab as kab_angkut, f.alamat_survey, j.gabung_oa, j.all_in, j.gabung_pbbkb, j.gabung_pbbkboa, j.id_penawaran, j.pembulatan
		from pro_invoice_admin_detail a 
		join pro_po_ds_kapal b on a.id_dsd = b.id_dsk and a.jenisnya = 'kapal' 
		join pro_pr_detail c on b.id_prd = c.id_prd 
		join pro_po_customer_plan d on c.id_plan = d.id_plan 
		join pro_po_customer e on d.id_poc = e.id_poc 
		join pro_customer_lcr f on d.id_lcr = f.id_lcr
		left join pro_master_wilayah_angkut g on f.id_wil_oa = g.id_master and f.prov_survey = g.id_prov and f.kab_survey = g.id_kab
		join pro_master_provinsi h on h.id_prov=g.id_prov
		join pro_master_kabupaten i on i.id_kab=g.id_kab
		join pro_penawaran j on e.id_penawaran=j.id_penawaran
		where 1=1 and a.id_invoice = '" . $idr . "' 
		order by id_invoice_detail 
";

$res03 = $con->getRecord($sql03);

$printe = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]) . " " . date("d/m/Y H:i:s") . " WIB";
// $barcod = $res[0]['kode_barcode'] . '05' . str_pad($idr, 6, '0', STR_PAD_LEFT);
// echo json_encode($approval);

ob_start();
if ($tipe != "default" && $tipe != "pbbkb") {
	require_once(realpath("./template/invoice-customer-split.php"));
} else {
	require_once(realpath("./template/invoice-customer2.php"));
}
$content = ob_get_clean();
ob_end_flush();
$con->close();

$mpdf = null;
if (PHP_VERSION >= 5.6) {
	$mpdf = new \Mpdf\Mpdf(['format' => 'A4', 'default_font' => 'Arial']);
} else
	$mpdf = new mPDF('c', 'A4', 9, 'arial', 10, 10, 10, 10, 0, 5);
$mpdf->AddPage('P', '', '', '', '', 12, 12, 12, 12, 12, 12);
$mpdf->SetDisplayMode('fullpage');
$mpdf->use_kwt = true;
$mpdf->autoPageBreak = false;
$mpdf->setAutoTopMargin = 'stretch';
$mpdf->setAutoBottomMargin = 'stretch';
$mpdf->shrink_tables_to_fit = 1;
$mpdf->WriteHTML($content);
$filename = "SI_" . sanitize_filename($idr);
$mpdf->Output($filename . '_' . date('dmyHis') . '.pdf', 'I');
exit;

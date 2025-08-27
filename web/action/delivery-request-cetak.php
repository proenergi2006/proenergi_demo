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

$sql = "select a.*, b.sm_result, b.sm_summary, b.sm_pic, b.sm_tanggal, b.purchasing_result, b.purchasing_summary, b.purchasing_pic, b.purchasing_tanggal, 
b.cfo_result, b.cfo_summary, b.cfo_pic, b.cfo_tanggal, b.is_ceo, 
b.ceo_result, b.ceo_summary, b.ceo_pic, b.ceo_tanggal, 
b.coo_result, b.coo_summary, b.coo_pic, b.coo_tanggal, 
c.tanggal_kirim, e.alamat_survey, e.id_wil_oa, f.nama_prov, g.nama_kab, n.nilai_pbbkb, 
k.id_penawaran, k.masa_awal, k.masa_akhir, k.id_area, k.flag_approval, 
k.refund_tawar, k.other_cost, k.perhitungan, k.detail_rincian, k.harga_dasar,
o1.harga_normal, 
h.nama_customer, h.id_customer, i.fullname, l.nama_area, d.harga_poc, k.refund_tawar, k.other_cost, m.jenis_produk, e.jenis_usaha, d.nomor_poc, d.produk_poc, 
p.nama_terminal, p.tanki_terminal, p.lokasi_terminal, q.nama_vendor, r.wilayah_angkut, m.merk_dagang, d.lampiran_poc, d.lampiran_poc_ori, d.id_poc, 
h.kode_pelanggan, c.status_jadwal, s.id_pod, t.id_dsd, u.id_dsk 
from pro_pr_detail a 
join pro_pr b on a.id_pr = b.id_pr 
join pro_po_customer_plan c on a.id_plan = c.id_plan 
join pro_po_customer d on c.id_poc = d.id_poc 
join pro_customer_lcr e on c.id_lcr = e.id_lcr
join pro_master_provinsi f on e.prov_survey = f.id_prov 
join pro_master_kabupaten g on e.kab_survey = g.id_kab
join pro_customer h on d.id_customer = h.id_customer 
join acl_user i on h.id_marketing = i.id_user 
join pro_master_cabang j on h.id_wilayah = j.id_master 
join pro_penawaran k on d.id_penawaran = k.id_penawaran  
join pro_master_area l on k.id_area = l.id_master 
join pro_master_produk m on d.produk_poc = m.id_master 
join pro_master_pbbkb n on k.pbbkb_tawar = n.id_master 
left join pro_master_harga_minyak o1 on k.masa_awal = o1.periode_awal and k.masa_akhir = o1.periode_akhir and k.id_area = o1.id_area 
	and k.pbbkb_tawar = o1.pajak and o1.is_approved = 1 
left join pro_master_terminal p on a.pr_terminal = p.id_master 
left join pro_master_vendor q on a.pr_vendor = q.id_master 
left join pro_master_wilayah_angkut r on e.id_wil_oa = r.id_master and e.prov_survey = r.id_prov and e.kab_survey = r.id_kab 
left join pro_po_detail s on a.id_prd = s.id_prd 
left join pro_po_ds_detail t on a.id_prd = t.id_prd 
left join pro_po_ds_kapal u on a.id_prd = u.id_prd 
where a.id_pr = '" . $idr . "' and ((b.disposisi_pr < 3) or (b.disposisi_pr > 2 and a.is_approved = 1))
order by a.is_approved desc, c.tanggal_kirim, k.id_cabang, k.id_area, a.id_plan, a.id_prd
";
$res = $con->getResult($sql);

$cek = "select a.id_pr, a.nomor_pr, a.tanggal_pr, a.jam_submit, a.disposisi_pr, a.is_edited, a.id_wilayah, a.id_group, b.nama_cabang, c.id_par, c.tanggal_buat 
            from pro_pr a join pro_master_cabang b on a.id_wilayah = b.id_master left join pro_pr_ar c on a.id_pr = c.id_pr and c.ar_approved = 1 
            where a.id_pr = '" . $idr . "'";
$row = $con->getResult($cek);

$printe = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]) . " " . date("d/m/Y H:i:s") . " WIB";
// $code   = $res[0]['kode_barcode'] . '-' . '11' . '-' . $idr;
// $barcod = BASE_URL . "/barcode_result.php?idr=" . paramEncrypt($code);
$barcod = $res[0]['kode_barcode'] . '05' . str_pad($idr, 6, '0', STR_PAD_LEFT);

ob_start();
require_once(realpath("./template/delivery-request.php"));
$content = ob_get_clean();
ob_end_flush();
$con->close();

$mpdf = null;
if (PHP_VERSION >= 5.6) {
	$mpdf = new \Mpdf\Mpdf(['format' => 'A4', 'default_font' => 'Arial']);
} else
	$mpdf = new mPDF('c', 'A4', 9, 'arial', 10, 10, 10, 10, 0, 5);
$mpdf->AddPage('L');
$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($content);
$filename = "DR_" . sanitize_filename($idr);
$mpdf->Output($filename . '_' . date('dmyHis') . '.pdf', 'I');
exit;

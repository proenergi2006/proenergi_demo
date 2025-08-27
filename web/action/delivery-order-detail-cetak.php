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
$idp     = isset($enk["idp"]) ? htmlspecialchars($enk["idp"], ENT_QUOTES) : '';

$sql = "select a.*, 
b.sm_result, b.sm_summary, b.sm_pic, b.sm_tanggal, 
b.purchasing_result, b.purchasing_summary, b.purchasing_pic, b.purchasing_tanggal,
b.is_ceo, b.disposisi_pr,
c.tanggal_kirim, c.status_plan, c.catatan_reschedule, c.status_jadwal, 
e.alamat_survey, e.id_wil_oa, 
f.nama_prov, g.nama_kab, 
n.nilai_pbbkb, 
k.id_penawaran, k.masa_awal, k.masa_akhir, k.id_area, k.flag_approval, 
k.refund_tawar, k.other_cost, k.perhitungan, k.detail_rincian, k.harga_dasar, k.gabung_oa,  
o1.harga_normal, o2.harga_normal as harga_normal_new, s.kode_barcode,
h.nama_customer, h.id_customer, 
i.fullname, l.nama_area, d.harga_poc, 
m.jenis_produk, e.jenis_usaha, 
d.nomor_poc, d.produk_poc, 
p.nama_terminal, p.tanki_terminal, p.lokasi_terminal, 
q.nama_vendor, r.wilayah_angkut, m.merk_dagang, 
d.lampiran_poc, d.lampiran_poc_ori, d.id_poc, h.kode_pelanggan, 
b.revert_cfo, b.revert_cfo_summary, b.revert_ceo, b.revert_ceo_summary,
b.submit_bm, b.pr_con
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
left join pro_master_harga_minyak o2 on k.masa_awal = o2.periode_awal and k.masa_akhir = o2.periode_akhir and k.id_area = o2.id_area 
	and o2.pajak = 1 and o2.is_approved = 1 
left join pro_master_terminal p on a.pr_terminal = p.id_master 
left join pro_master_vendor q on a.pr_vendor = q.id_master 
left join pro_master_wilayah_angkut r on e.id_wil_oa = r.id_master 
	and e.prov_survey = r.id_prov 
	and e.kab_survey = r.id_kab
join pro_master_cabang s on h.id_wilayah = s.id_master 
where 
	a.id_prd = '" . $idp . "' and 
	a.is_approved = 1
order by a.is_approved desc, c.tanggal_kirim, k.id_cabang, k.id_area, a.id_plan, a.id_prd";
$res = $con->getRecord($sql);

$printe = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]) . " " . date("d/m/Y H:i:s") . " WIB";
$barcod = $res['kode_barcode'] . '05' . str_pad($idp, 6, '0', STR_PAD_LEFT);
$tempal = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $res['nama_kab']));
$alamat    = $res['alamat_survey'] . " " . ucwords($tempal) . " " . $res['nama_prov'];
// echo json_encode($res);
ob_start();
require_once(realpath("./template/delivery-order-detail.php"));
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
$filename = "DO_" . sanitize_filename($idr);
$mpdf->Output($filename . '_' . date('dmyHis') . '.pdf', 'I');
exit;

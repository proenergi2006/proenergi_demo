<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth    = new MyOtentikasi();
$con     = new Connection();

$q2        = htmlspecialchars($_POST["periode"], ENT_QUOTES);
$q3        = htmlspecialchars($_POST["cabang"], ENT_QUOTES);
$seswil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

$exp = explode("-", $q2);
$bulan = $exp[1];
$tahun = $exp[0];

$p = new paging;
$sql = "SELECT a.*, a.id_dsd as id_dsdnya, a.id_invoice as id_invoicenya, a.id as id_incentive, a.total_incentive, a.disposisi as statusnya, i.nama_customer, i.kode_pelanggan, i.jenis_payment, i.top_payment, i.id_customer as id_customernya, e.alamat_survey, f.nama_prov, g.nama_kab, j.fullname, j.id_role, h.nomor_poc, h.tanggal_poc, h.id_poc as id_pocnya, h.produk_poc, k.refund_tawar, l.nama_area, l.id_master as id_areanya, k.id_penawaran, k.harga_asli as harga_dasarnya, ppdd.tanggal_delivered, n.no_invoice, n.tgl_invoice_dikirim, n.tgl_invoice, k.masa_awal, k.masa_akhir, CONCAT(o.jenis_produk,' - ', o.merk_dagang) as nama_produk, p.vol_kirim as volume_invoice, n.is_lunas,
(SELECT MAX(tgl_bayar) FROM pro_invoice_admin_detail_bayar WHERE id_invoice=a.id_invoice) as tanggal_bayar
from pro_incentive a 
join pro_po_ds_detail ppdd on ppdd.id_dsd = a.id_dsd
join pro_po_customer_plan d on ppdd.id_plan = d.id_plan 
join pro_customer_lcr e on d.id_lcr = e.id_lcr
join pro_master_provinsi f on e.prov_survey = f.id_prov 
join pro_master_kabupaten g on e.kab_survey = g.id_kab
join pro_po_customer h on d.id_poc = h.id_poc 
join pro_customer i on h.id_customer = i.id_customer 
join acl_user j on i.id_marketing = j.id_user 
join pro_penawaran k on h.id_penawaran = k.id_penawaran	
join pro_master_area l on k.id_area = l.id_master
join pro_invoice_admin n on a.id_invoice = n.id_invoice
join pro_master_produk o on o.id_master=h.produk_poc
join pro_invoice_admin_detail p on a.id_invoice=p.id_invoice
WHERE a.disposisi = '1' AND k.created_time > '2025-03-01'
AND MONTH((SELECT MAX(tgl_bayar) FROM pro_invoice_admin_detail_bayar WHERE id_invoice = a.id_invoice)) = '" . $bulan . "' AND YEAR((SELECT MAX(tgl_bayar) FROM pro_invoice_admin_detail_bayar WHERE id_invoice = a.id_invoice)) = '" . $tahun . "' AND i.id_wilayah = '" . $q3 . "' GROUP BY a.id ORDER BY id DESC";

$result = $con->getResult($sql);

$content = (count($result) > 0) ? $result : array();
$json_data = array("items" => $result);
echo json_encode($json_data);

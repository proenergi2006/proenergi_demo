<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$con 	= new Connection();

$q1		= htmlspecialchars($_POST["q1"], ENT_QUOTES);
$q2		= htmlspecialchars($_POST["q2"], ENT_QUOTES);
$q3		= htmlspecialchars($_POST["q3"], ENT_QUOTES);
$q4		= htmlspecialchars($_POST["q4"], ENT_QUOTES);
$seswil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

if ($q4 == "kirim") {
	$jenis_tanggal = "and d.tanggal_kirim between '" . tgl_db($q2) . "' and '" . tgl_db($q3) . "'";
	$jenis_tanggal_kapal = "and c.tanggal_kirim between '" . tgl_db($q2) . "' and '" . tgl_db($q3) . "'";
} else {
	$jenis_tanggal = "and a.tanggal_delivered between '" . tgl_db($q2) . " 00:00:00' and '" . tgl_db($q3) . " 23:59:59'";
	$jenis_tanggal_kapal = "and a.tanggal_delivered between '" . tgl_db($q2) . " 00:00:00' and '" . tgl_db($q3) . " 23:59:59'";
}

$p = new paging;
$sql = "
		select 
		i.kode_pelanggan, i.id_customer, i.nama_customer, k1.id_invoice, n.detail_rincian, n.pembulatan,
		h.nomor_poc, b.volume_po, h.produk_poc, c.produk, h.harga_poc, a.realisasi_volume,   
		a.id_dsd, a.tanggal_delivered, DATE_FORMAT(a.tanggal_delivered, '%d/%m/%Y') AS tgl_delivered, DATE_FORMAT(d.tanggal_kirim, '%d/%m/%Y') AS tgl_kirim, 
		'truck' as jenisnya, a.nomor_do as no_dn, k.nomor_plat as angkutan, l.nama_sopir as sopir, c.no_do_syop, c.no_do_acurate, c.nomor_lo_pr, n.refund_tawar, n.nomor_surat, b.no_spj
		from pro_po_ds_detail a 
		join pro_po_ds o on a.id_ds = o.id_ds 
		join pro_po_detail b on a.id_pod = b.id_pod 
		join pro_po m on a.id_po = m.id_po 
		join pro_pr_detail c on a.id_prd = c.id_prd 
		join pro_po_customer_plan d on a.id_plan = d.id_plan 
		join pro_po_customer h on d.id_poc = h.id_poc 
		join pro_customer i on h.id_customer = i.id_customer 
		join acl_user j on i.id_marketing = j.id_user 
		join pro_master_transportir_mobil k on b.mobil_po = k.id_master 
		join pro_master_transportir_sopir l on b.sopir_po = l.id_master
		join pro_penawaran n ON h.id_penawaran=n.id_penawaran
		left join pro_invoice_admin_detail k1 on a.id_dsd = k1.id_dsd and k1.jenisnya = 'truck'
		where 1=1 
			and a.is_loaded = 1 and a.is_delivered = 1 and k1.id_dsd is null 
			and o.id_wilayah = '" . $seswil . "' 
			and i.id_customer = '" . $q1 . "' 
			" . $jenis_tanggal . "
		UNION ALL 
		select 
		e.kode_pelanggan, e.id_customer, e.nama_customer, k1.id_invoice, f.detail_rincian, f.pembulatan,
		d.nomor_poc, a.bl_lo_jumlah as volume_po, d.produk_poc, b.produk, d.harga_poc, a.realisasi_volume,  
		a.id_dsk as id_dsd, a.tanggal_delivered, DATE_FORMAT(a.tanggal_delivered, '%d/%m/%Y') AS tgl_delivered, DATE_FORMAT(c.tanggal_kirim, '%d/%m/%Y') AS tgl_kirim, 
		'kapal' as jenisnya, a.nomor_dn_kapal as no_dn, a.vessel_name as angkutan, a.kapten_name as sopir, b.no_do_syop, b.no_do_acurate, b.nomor_lo_pr, f.refund_tawar, f.nomor_surat, NULL as no_spj
		from pro_po_ds_kapal a 
		join pro_pr_detail b on a.id_prd = b.id_prd 
		join pro_po_customer_plan c on b.id_plan = c.id_plan 
		join pro_po_customer d on c.id_poc = d.id_poc 
		join pro_customer e on d.id_customer = e.id_customer
		join pro_penawaran f ON f.id_penawaran=d.id_penawaran
		left join pro_invoice_admin_detail k1 on a.id_dsk = k1.id_dsd and k1.jenisnya = 'kapal' 
		where 1=1 
			and a.is_loaded = 1 and a.is_delivered = 1 and k1.id_dsd is null 
			and a.id_wilayah = '" . $seswil . "' 
			and e.id_customer = '" . $q1 . "' 
			" . $jenis_tanggal_kapal . "
		order by tanggal_delivered
	";
/*
	where 1=1 
		and a.is_loaded = 1 and a.is_delivered = 1 and k1.id_dsd is null 
		and a.id_wilayah = '".$seswil."' 
		and e.id_customer = '".$q1."' 
		and tanggal_delivered between '".tgl_db($q2)." 00:00:00' and '".tgl_db($q3)." 23:59:59' 
	*/

$result = $con->getResult($sql);

$content = (count($result) > 0) ? $result : array();
$json_data = array("items" => $result);
echo json_encode($json_data);

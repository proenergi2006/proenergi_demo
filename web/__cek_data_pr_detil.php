<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$conSub = new Connection();
$q1 	= htmlspecialchars($_POST["q1"], ENT_QUOTES);
$answer = array();

$sql = "select a.id_prd, a.volume, d.alamat_survey, e.nama_prov, f.nama_kab, h.nama_customer, h.alamat_customer, i.nama_prov as prov_cust, j.nama_kab as kab_cust, 
			k.jenis_produk, k.merk_dagang, l.nama_terminal, l.tanki_terminal, a.pr_terminal, a.volume, m.oa_kirim, m.detail_rincian, c.tanggal_kirim
			from pro_pr_detail a 
			join pro_pr b on a.id_pr = b.id_pr 
			join pro_po_customer_plan c on a.id_plan = c.id_plan
		    join pro_customer_lcr d on c.id_lcr = d.id_lcr
			join pro_master_provinsi e on d.prov_survey = e.id_prov
			join pro_master_kabupaten f on d.kab_survey = f.id_kab
			join pro_po_customer g on c.id_poc = g.id_poc
			join pro_customer h on g.id_customer = h.id_customer 
			join pro_master_provinsi i on h.prov_customer = i.id_prov
			join pro_master_kabupaten j on h.kab_customer = j.id_kab 
			join pro_master_produk k on g.produk_poc = k.id_master 
			join pro_master_terminal l on a.pr_terminal = l.id_master
		    join pro_penawaran m on g.id_penawaran = m.id_penawaran 
			
			where a.id_prd = '" . $q1 . "'";
$res = $conSub->getRecord($sql);
$tempal1 = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $res['kab_cust']));
$alamat1 = $res['alamat_customer'] . " " . ucwords($tempal1) . " " . $res['prov_cust'];
$tempal2 = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $res['nama_kab']));
$alamat2 = $res['alamat_survey'] . " " . ucwords($tempal2) . " " . $res['nama_prov'];
$kirim    = date("d/m/Y", strtotime($res['tanggal_kirim']));
//$alamat_customer= $res['alamat_customer']." ".str_replace(array("KABUPATEN ","KOTA "), array("",""), $res['kab_cust'])." ".$res['prov_cust'];
//$alamat_survey 	= $res['alamat_survey']." ".str_replace(array("KABUPATEN ","KOTA "), array("",""), $res['nama_kab'])." ".$res['nama_prov'];
foreach ($res as $data) {
	$rincian = json_decode($res['detail_rincian'], true);
	foreach ($rincian as $idx1 => $arr1) {
		if ($idx1 == 1) {
			$res['ongkos_angkut'] = ($arr1['biaya'] ? $arr1['biaya'] : '0');
		}
	}
}

if (count($res) > 0) {
	$answer['customer'] 		= $res['nama_customer'];
	$answer['terminal'] 	    = $res['nama_terminal'] . '-' . $res['tanki_terminal'];
	$answer['id_terminal'] 	    = $res['pr_terminal'];
	$answer['bl1'] 	            = $res['volume'];
	$answer['oa_kirim'] 	    = $res['oa_kirim'];
	$answer['oa_disetujui'] 	= $res['ongkos_angkut'];
	$answer['tgl_etl'] 			= $kirim;
	$answer['tgl_eta'] 			= $kirim;
	$answer['alamat_customer'] 	= $alamat1;
	$answer['alamat_survey'] 	= $alamat2;
	$answer['produk'] 			= $res['jenis_produk'] . " (" . $res['merk_dagang'] . ")";
	$answer['volume'] 			= number_format($res['volume']);
}
echo json_encode($answer);
$conSub->close();

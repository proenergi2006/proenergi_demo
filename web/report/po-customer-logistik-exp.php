<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
require_once($public_base_directory . "/libraries/helper/class.xlsxwriter.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;
$enk  	= decode($_SERVER['REQUEST_URI']);
$sheet 	= 'Sheet1';

$q1	= htmlspecialchars($enk["q1"], ENT_QUOTES);
$q2	= htmlspecialchars($enk["q2"], ENT_QUOTES);

$sql = "select a.*, b.id_customer, c.alamat_survey, c.id_wil_oa, c.jenis_usaha, d.nama_prov, e.nama_kab, i.refund_tawar, 
			f.nama_customer, f.kode_pelanggan, f.top_payment, g.fullname, j.nama_area, k.jenis_produk, b.nomor_poc, b.harga_poc, 
			b.lampiran_poc, k.merk_dagang, h.nama_cabang, l.wilayah_angkut, i.oa_kirim, m.nilai_pbbkb, f.jenis_payment, 
			f.top_payment, f.jenis_net, b.lampiran_poc_ori, i.other_cost      
			from pro_po_customer_plan a 
			join pro_po_customer b on a.id_poc = b.id_poc 
			join pro_customer_lcr c on a.id_lcr = c.id_lcr
			join pro_master_provinsi d on c.prov_survey = d.id_prov 
			join pro_master_kabupaten e on c.kab_survey = e.id_kab
			join pro_customer f on b.id_customer = f.id_customer 
			join acl_user g on f.id_marketing = g.id_user 
			join pro_master_cabang h on f.id_wilayah = h.id_master 
			join pro_penawaran i on b.id_penawaran = i.id_penawaran  
			join pro_master_area j on i.id_area = j.id_master 
			join pro_master_produk k on b.produk_poc = k.id_master 
			join pro_master_wilayah_angkut l on c.id_wil_oa = l.id_master and c.prov_survey = l.id_prov and c.kab_survey = l.id_kab
			join pro_master_pbbkb m on i.pbbkb_tawar = m.id_master
			where 1=1 and f.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "' and (a.status_plan = 0 or a.status_plan = 1) and a.is_approved = 1";

if ($q1 && !$q2)
	$sql .= " and a.tanggal_kirim = '" . tgl_db($q1) . "'";
else if ($q1 && $q2)
	$sql .= " and a.tanggal_kirim between '" . tgl_db($q1) . "' and '" . tgl_db($q2) . "'";
$sql .= " order by a.tanggal_kirim, a.id_plan";
$res = $con->getResult($sql);

$filename 	= "Laporan-po-customer-logstik-" . date('dmYHis') . ".xlsx";
header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
ob_end_clean();
header('Content-type: application/vnd.ms-excel');
// header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');

$writer = new XLSXWriter();
$writer->writeSheetHeader($sheet, array('Laporan PO Customer Logistik' => 'string'));
$writer->newMergeCell($sheet, "A1", "R1");
$start = 2;
$patok = 1;

if ($q1 && $q2) {
	$writer->writeSheetHeaderExt($sheet, array("Periode Tanggal Kirim : " . $q1 . " s/d " . $q2 => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "R" . $start);
	$patok++;
	$start++;
} else if ($q1 && !$q2) {
	$writer->writeSheetHeaderExt($sheet, array(" Tanggal Kirim : " . $q1 => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "R" . $start);
	$patok++;
	$start++;
}

$q5Txt = $con->getOne("select nama_cabang from pro_master_cabang where id_master = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'");
$writer->writeSheetHeaderExt($sheet, array("Cabang : " . $q5Txt => "string"));
$writer->newMergeCell($sheet, "A" . $start, "R" . $start);
$patok++;
$start++;

$writer->writeSheetHeaderExt($sheet, array("" => "string"));
$patok++;
$start++;
$writer->setColumnIndex($patok);

$header1 = array(
	"No" => 'string',
	"Kode Customer" => 'string',
	"Customer" => 'string',
	"Alamat Kirim" => 'string',
	"Produk" => 'string',
	"Volume (KL)" => 'string',
	"PO Customer" => 'string',
	"Bidang Usaha" => 'string',
	"Harga Jual" => 'string',
	"Ongkos Angkut" => 'string',
	"Refund" => 'string',
	"Oil Due" => 'string',
	"PBBKB" => 'string',
	"Other Cost" => 'string',
	"Harga Jual (Nett)" => 'string',
	"Term of Payment" => 'string',
	"Catatan" => 'string',
	"Tanggal Issued" => 'string',
);
$writer->writeSheetHeaderExt($sheet, $header1);
$start++;

if (count($res) > 0) {
	$nom  = 0;
	$last = $start - 1;
	foreach ($res as $data) {
		$last++;
		$nom++;
		$jns_payment = $data['jenis_payment'];
		$top_payment = $data['top_payment'];
		$jenisCredit = $data['jenis_net'];
		$arr_payment = array("CREDIT" => "NET " . $top_payment, "COD" => "COD", "CDB" => "CBD");
		$termPayment = $arr_payment[$jns_payment];

		$idp 	= $data['id_plan'];
		$tempal = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
		$alamat	= $data['alamat_survey'] . " " . ucwords($tempal) . " " . $data['nama_prov'];
		$vkirim	= $data['volume_kirim'] / 1000;
		$pbbkbT = ($data['nilai_pbbkb'] / 100) + 1.11;
		$oildus = $data['harga_poc'] / $pbbkbT * 0.003;
		$pbbkbN = $data['harga_poc'] / $pbbkbT * ($data['nilai_pbbkb'] / 100);
		$tmphrg = $data['refund_tawar'] + $oildus + $data['oa_kirim'] + $pbbkbN + $data['other_cost'];
		$nethrg = $data['harga_poc'] - $tmphrg;
		$writer->writeSheetRow($sheet, array(
			$nom,
			$data['kode_pelanggan'],
			$data['nama_customer'],
			$alamat,
			$data['merk_dagang'],
			$vkirim,
			$data['nomor_poc'],
			$data['jenis_usaha'],
			round($data['harga_poc']),
			round($data['oa_kirim']),
			round($data['refund_tawar']),
			round($oildus),
			round($pbbkbN),
			round($data['other_cost']),
			round($nethrg),
			$termPayment,
			$data['status_jadwal'],
			date("d/m/Y H:i:s", strtotime($data['created_time'])) . " WIB",

		));
	}
} else {
	$writer->writeSheetRow($sheet, array("Data tidak ada"));
	$writer->newMergeCell($sheet, "A" . $start, "R" . $start);
	$start++;
}

$con->close();
$writer->writeToStdOut();
exit(0);

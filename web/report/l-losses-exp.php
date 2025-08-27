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
$where1 = "";
$where2 = "";

$q1	= htmlspecialchars($enk["q1"], ENT_QUOTES);
$q2	= htmlspecialchars($enk["q2"], ENT_QUOTES);
$q3	= htmlspecialchars($enk["q3"], ENT_QUOTES);
$q4	= htmlspecialchars($enk["q4"], ENT_QUOTES);
$q5	= htmlspecialchars($enk["q5"], ENT_QUOTES);
$q6	= htmlspecialchars($enk["q6"], ENT_QUOTES);
$q7	= htmlspecialchars($enk["q7"], ENT_QUOTES);
$q8	= htmlspecialchars($enk["q8"], ENT_QUOTES);
$q9	= htmlspecialchars($enk["q9"], ENT_QUOTES);
$q10 = htmlspecialchars($enk["q10"], ENT_QUOTES);

if (paramDecrypt($_SESSION["sinori" . SESSIONID]["id_role"]) == 13) {
	$where1 .= " and a.id_terminal = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["terminal"]) . "'";
	$where2 .= " and a.terminal = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["terminal"]) . "'";
} else if (paramDecrypt($_SESSION["sinori" . SESSIONID]["id_role"]) == 9 || paramDecrypt($_SESSION["sinori" . SESSIONID]["id_role"]) == 10) {
	$where1 .= " and n.id_wilayah = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]) . "'";
	$where2 .= " and n.id_wilayah = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]) . "'";
}

if ($q1 && !$q2) {
	$where1 .= " and g.tanggal_kirim = '" . tgl_db($q1) . "'";
	$where2 .= " and g.tanggal_kirim = '" . tgl_db($q1) . "'";
} else if ($q1 && $q2) {
	$where1 .= " and g.tanggal_kirim between '" . tgl_db($q1) . "' and '" . tgl_db($q2) . "'";
	$where2 .= " and g.tanggal_kirim between '" . tgl_db($q1) . "' and '" . tgl_db($q1) . "'";
}
if ($q3) {
	$where1 .= " and upper(n.nama_customer) like '%" . strtoupper($q3) . "%'";
	$where2 .= " and upper(n.nama_customer) like '%" . strtoupper($q3) . "%'";
}
if ($q4) {
	$where1 .= " and upper(d.no_spj) = '" . strtoupper($q4) . "'";
	$where2 .= " and 1=2";
}
if ($q5) {
	$where1 .= " and i.kab_survey = '" . $q5 . "'";
	$where2 .= " and i.kab_survey = '" . $q5 . "'";
}
if ($q6) {
	$where1 .= " and c.id_transportir = '" . $q6 . "'";
	$where2 .= " and a.transportir = '" . $q6 . "'";
}
if ($q7) {
	$where1 .= " and d.mobil_po = '" . $q7 . "'";
	$where2 .= " and 1=2";
}
if ($q8) {
	$where1 .= " and d.sopir_po = '" . $q8 . "'";
	$where2 .= " and 1=2";
}
if ($q9) {
	$where1 .= " and l.id_area = '" . $q9 . "'";
	$where2 .= " and l.id_area = '" . $q9 . "'";
}
if ($q10) {
	$where1 .= " and n.id_wilayah = '" . $q10 . "'";
	$where2 .= " and n.id_wilayah = '" . $q10 . "'";
}

$sql = "
		select * from (
			select g.tanggal_kirim, d.volume_po as jum_vol, n.nama_customer, n.id_wilayah, o.nama_cabang, i.kab_survey, k.nama_kab, l.id_area, m.nama_area, 
			h.nomor_poc, d.no_spj, q.nama_suplier, q.nama_transportir, q.lokasi_suplier, r.nomor_plat, s.nama_sopir, t.nama_terminal, t.tanki_terminal, t.lokasi_terminal, 
			b.rating, b.realisasi_volume, i.alat_ukur 
			from pro_po_ds a
			join pro_po_ds_detail b on a.id_ds = b.id_ds 
			join pro_po_detail d on b.id_pod = d.id_pod 
			join pro_po c on d.id_po = c.id_po 
			join pro_pr_detail e on d.id_prd = e.id_prd 
			join pro_pr f on e.id_pr = f.id_pr 
			join pro_po_customer_plan g on e.id_plan = g.id_plan 
			join pro_po_customer h on g.id_poc = h.id_poc 
			join pro_customer_lcr i on g.id_lcr = i.id_lcr 
			join pro_master_provinsi j on i.prov_survey = j.id_prov 
			join pro_master_kabupaten k on i.kab_survey = k.id_kab 
			join pro_penawaran l on h.id_penawaran = l.id_penawaran 
			join pro_master_area m on l.id_area = m.id_master 
			join pro_customer n on h.id_customer = n.id_customer 
			join pro_master_cabang o on n.id_wilayah = o.id_master 
			join acl_user p on n.id_marketing = p.id_user 
			join pro_master_transportir q on c.id_transportir = q.id_master 
			join pro_master_transportir_mobil r on d.mobil_po = r.id_master 
			join pro_master_transportir_sopir s on d.sopir_po = s.id_master 
			join pro_master_terminal t on a.id_terminal = t.id_master 
			where b.is_delivered = 1 " . $where1 . " 
			UNION ALL
			select g.tanggal_kirim, a.bl_lo_jumlah as jum_vol, n.nama_customer, n.id_wilayah, o.nama_cabang, i.kab_survey, k.nama_kab, l.id_area, m.nama_area, 
			h.nomor_poc, '' as no_spj, q.nama_suplier, q.nama_transportir, q.lokasi_suplier, a.vessel_name as nomor_plat, a.kapten_name as nama_sopir, 
			t.nama_terminal, t.tanki_terminal, t.lokasi_terminal, a.rating, a.realisasi_volume, i.alat_ukur 
			from pro_po_ds_kapal a 
			join pro_pr_detail e on a.id_prd = e.id_prd 
			join pro_pr f on e.id_pr = f.id_pr 
			join pro_po_customer_plan g on e.id_plan = g.id_plan 
			join pro_po_customer h on g.id_poc = h.id_poc 
			join pro_customer_lcr i on g.id_lcr = i.id_lcr 
			join pro_master_provinsi j on i.prov_survey = j.id_prov 
			join pro_master_kabupaten k on i.kab_survey = k.id_kab 
			join pro_penawaran l on h.id_penawaran = l.id_penawaran 
			join pro_master_area m on l.id_area = m.id_master 
			join pro_customer n on h.id_customer = n.id_customer 
			join pro_master_cabang o on n.id_wilayah = o.id_master 
			join acl_user p on n.id_marketing = p.id_user 
			join pro_master_transportir q on a.transportir = q.id_master 
			join pro_master_terminal t on a.terminal = t.id_master 
			where a.is_delivered = 1 " . $where2 . " 
		) a ";

$sql .= " order by tanggal_kirim desc";
$res = $con->getResult($sql);


$filename 	= "Laporan-losses-" . date('dmYHis') . ".xlsx";
$arrOp 		= array(1 => "=", ">=", "<=");
header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
ob_end_clean();
header('Content-type: application/vnd.ms-excel');
// header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');

$writer = new XLSXWriter();
$writer->writeSheetHeader($sheet, array('Laporan Losses' => 'string'));
$writer->newMergeCell($sheet, "A1", "O1");
$start = 2;
$patok = 1;
if ($q1 && !$q2) {
	$writer->writeSheetHeaderExt($sheet, array("Tanggal Kirim : " . $q1 => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "O" . $start);
	$patok++;
	$start++;
} else if ($q1 && $q2) {
	$writer->writeSheetHeaderExt($sheet, array("Tanggal Kirim : " . $q1 . " s/d " . $q2 => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "O" . $start);
	$patok++;
	$start++;
}
if ($q3) {
	$writer->writeSheetHeaderExt($sheet, array("Customer : " . $q3 => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "O" . $start);
	$patok++;
	$start++;
}
if ($q5) {
	$q5Txt = $con->getOne("select nama_kab from pro_master_kabupaten where id_kab = '" . $q5 . "'");
	$q5Tmp = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $q5Txt));
	$writer->writeSheetHeaderExt($sheet, array("Wilayah Kirim : " . ucwords($q5Tmp) => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "O" . $start);
	$patok++;
	$start++;
}
if ($q4) {
	$writer->writeSheetHeaderExt($sheet, array("Surat Jalan : " . $q4 => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "O" . $start);
	$patok++;
	$start++;
}
if ($q6) {
	$q6Txt = $con->getOne("select concat(nama_suplier,' - ',nama_transportir,', ',lokasi_suplier) as transportir from pro_master_transportir where id_master = '" . $q6 . "'");
	$writer->writeSheetHeaderExt($sheet, array("Transportir : " . $q6Txt => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "O" . $start);
	$patok++;
	$start++;
}
if ($q7) {
	$q7Txt = $con->getOne("select nomor_plat from pro_master_transportir_mobil where id_master = '" . $q7 . "'");
	$writer->writeSheetHeaderExt($sheet, array("No. Plat : " . $q7Txt => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "O" . $start);
	$patok++;
	$start++;
}
if ($q8) {
	$q8Txt = $con->getOne("select nama_sopir from pro_master_transportir_sopir where id_master = '" . $q8 . "'");
	$writer->writeSheetHeaderExt($sheet, array("Sopir : " . $q8Txt => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "O" . $start);
	$patok++;
	$start++;
}
if ($q9) {
	$q9Txt = $con->getOne("select nama_area from pro_master_area where id_master = '" . $q9 . "'");
	$writer->writeSheetHeaderExt($sheet, array("Area : " . $q9Txt => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "O" . $start);
	$patok++;
	$start++;
}
if ($q10) {
	$q10Txt = $con->getOne("select nama_cabang from pro_master_cabang where id_master = '" . $q10 . "'");
	$writer->writeSheetHeaderExt($sheet, array("Cabang Invoice : " . $q10Txt => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "O" . $start);
	$patok++;
	$start++;
}

$writer->writeSheetHeaderExt($sheet, array("" => "string"));
$patok++;
$start++;
$writer->setColumnIndex($patok);

$header = array(
	"Tanggal Kirim" => 'string',
	"Customer" => 'string',
	"Wilayah Kirim" => 'string',
	"No. PO" => 'string',
	"SJ" => 'string',
	"Transportir" => 'string',
	"No. Plat" => 'string',
	"Driver" => 'string',
	"Volume SJ" => 'string',
	"Volume Terkirim" => 'string',
	"Losses" => 'string',
	"Rating" => 'string',
	"Area" => 'string',
	"Cabang" => 'string',
	"Alat Ukur (LCR)" => 'string',
);
$writer->writeSheetHeaderExt($sheet, $header);
$start++;

if (count($res) > 0) {
	$tot1 = 0;
	$last = $start - 1;
	foreach ($res as $data) {
		$last++;
		$tempal = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
		$transp = $data['nama_suplier'] . ' - ' . $data['nama_transportir'] . ', ' . $data['lokasi_suplier'];
		$rating = ($data['rating'] ? $data['rating'] . '/5' : '');
		$alatUk = ($data['alat_ukur'] ? $data['alat_ukur'] : '');

		$writer->writeSheetRow($sheet, array(
			date("d/m/Y", strtotime($data['tanggal_kirim'])), $data['nama_customer'], ucwords($tempal), $data['nomor_poc'], $data['no_spj'],
			$transp, $data['nomor_plat'], $data['nama_sopir'], $data['jum_vol'], $data['realisasi_volume'],
			'=J' . $last . '-I' . $last . '', $rating, $data['nama_area'], $data['nama_cabang'], $alatUk,
		));
	}
	$writer->writeSheetRow($sheet, array(
		"", "", "", "", "", "", "", "TOTAL",
		"=SUM(I" . $start . ":I" . $last . ")", "=SUM(J" . $start . ":J" . $last . ")", "=SUM(K" . $start . ":K" . $last . ")", "", "", "", ""
	));
	$last++;
	$writer->newMergeCell($sheet, "A" . $last, "G" . $last);
	$writer->newMergeCell($sheet, "L" . $last, "O" . $last);
} else {
	$writer->writeSheetRow($sheet, array("Data tidak ada"));
	$writer->newMergeCell($sheet, "A" . $start, "O" . $start);
	$start++;
}

$con->close();
$writer->writeToStdOut();
exit(0);

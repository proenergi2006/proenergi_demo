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
$sess_wil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

$q1	= htmlspecialchars($enk["q1"], ENT_QUOTES);
$q2	= htmlspecialchars($enk["q2"], ENT_QUOTES);
$q3	= htmlspecialchars($enk["q3"], ENT_QUOTES);
$q4	= htmlspecialchars($enk["q4"], ENT_QUOTES);
$q5	= htmlspecialchars($enk["q5"], ENT_QUOTES);
$cabang	= htmlspecialchars($enk["cabang"], ENT_QUOTES);
$datenow = date("Y-m-d");
$id_user = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);
$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);

if ($sesrol == '25') {
	if ($cabang) {
		$filter_cabang = " and b.id_wilayah = '" . $cabang . "'";
	} else {
		$filter_cabang = "";
	}
} else {
	$filter_cabang = " and b.id_wilayah = '" . $sess_wil . "'";
}

$p = new paging;
$sql = "SELECT a.*, b.nama_customer, b.jenis_payment, b.top_payment, c.nama_cabang
		from pro_invoice_admin a 
		join pro_customer b on a.id_customer = b.id_customer 
		join pro_master_cabang c on b.id_wilayah = c.id_master
		where 1=1 " . $filter_cabang . "";

// echo json_encode($con->getResult($sql));

if ($q1 != "")
	$sql .= " and (upper(b.nama_customer) like '%" . strtoupper($q1) . "%' or upper(a.no_invoice) like '%" . strtoupper($q1) . "%')";

if ($q2 != "" && $q3 != "") {
	$sql .= " and (DATE(a.tgl_invoice) between '" . tgl_db($q2) . "' and '" . tgl_db($q3) . "')";
} else {
	if ($q2 != "") $sql .= " and (DATE(a.tgl_invoice) = '" . tgl_db($q2) . "')";
	if ($q3 != "") $sql .= " and (DATE(a.tgl_invoice) = '" . tgl_db($q3) . "')";
}
$sql .= " order by a.tgl_invoice desc";
$res 	= $con->getResult($sql);

$filename 	= "Invoice-" . date('dmYHis') . ".xlsx";
$arrOp 		= array(1 => "=", ">=", "<=");
header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
ob_end_clean();
header('Content-type: application/vnd.ms-excel');
// header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');

$writer = new XLSXWriter();
$writer->writeSheetHeader($sheet, array('PT PRO ENERGI' => 'string'));
$writer->newMergeCell($sheet, "A1", "M1");
$start = 2;
$patok = 1;

$writer->writeSheetHeaderExt($sheet, array("SALES BY ITEM DETAIL" => "string"));
$writer->newMergeCell($sheet, "A" . $start, "M" . $start);
$patok++;
$start++;

if ($q1) {
	$writer->writeSheetHeaderExt($sheet, array("Keywords : " . $q1 => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "M" . $start);
	$patok++;
	$start++;
}
if ($q2 != "" && $q3 != "") {
	$writer->writeSheetHeaderExt($sheet, array("From : " . $q2 . " To " . $q3 => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "M" . $start);
	$patok++;
	$start++;
} elseif ($q2 != "" && $q3 == "") {
	$writer->writeSheetHeaderExt($sheet, array("From : " . $q2 . " To " . $q2 => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "M" . $start);
	$patok++;
	$start++;
} elseif ($q2 == "" && $q3 != "") {
	$writer->writeSheetHeaderExt($sheet, array("From : " . $q3 . " To " . $q3 => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "M" . $start);
	$patok++;
	$start++;
} else {
	$writer->writeSheetHeaderExt($sheet, array("ALL" => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "M" . $start);
	$patok++;
	$start++;
}

$writer->writeSheetHeaderExt($sheet, array("" => "string"));
$patok++;
$start++;
$writer->setColumnIndex($patok);

$header = array(
	"No" => 'string',
	"Delivery Order No." => 'string',
	"SJ No." => 'string',
	"Invoice No." => 'string',
	"Customer Name" => 'string',
	"Quantity" => 'string',
	"Invoice Amount" => 'string',
	"Invoice Date" => 'string',
	"Invoice Terms Net Days" => 'string',
	"Due Date" => 'string',
	// "Tgl Terima SJ" => 'string',
	"Tgl Kirim Invoice" => 'string',
	"Tgl Delivered" => 'string'
);
$writer->writeSheetHeaderExt($sheet, $header);
$start++;

if (count($res) > 0) {
	$tot1 = 0;
	$last = $start - 1;
	foreach ($res as $data) {
		$sql02 = "
		select 
		a.*, f.*, b.nomor_do as no_dn, k1.nomor_plat as angkutan, l1.nama_sopir as sopir, d.nomor_poc, b.realisasi_volume, ppd.no_do_acurate, ppd.no_do_syop, ppd.nomor_lo_pr, b1.no_spj, b.status_pengiriman, b.tgl_realisasi
		from pro_invoice_admin_detail a 
		join pro_po_ds_detail b on a.id_dsd = b.id_dsd and a.jenisnya = 'truck' 
		join pro_pr_detail ppd on b.id_prd = ppd.id_prd 
		join pro_po_customer_plan c on b.id_plan = c.id_plan 
		join pro_po_customer d on c.id_poc = d.id_poc 
		join pro_po_detail b1 on b.id_pod = b1.id_pod 
		join pro_master_transportir_mobil k1 on b1.mobil_po = k1.id_master 
		join pro_master_transportir_sopir l1 on b1.sopir_po = l1.id_master
		join pro_invoice_admin f on a.id_invoice=f.id_invoice
		where 1=1 and a.id_invoice = '" . $data['id_invoice'] . "'
		UNION ALL 
		select 
		a.*, f.*, b.nomor_dn_kapal as no_dn, b.vessel_name as angkutan, b.kapten_name as sopir, e.nomor_poc, b.realisasi_volume, c.no_do_acurate, c.no_do_syop, c.nomor_lo_pr, NULL as no_spj, NULL as status_pengiriman, NULL as tgl_realisasi
		from pro_invoice_admin_detail a 
		join pro_po_ds_kapal b on a.id_dsd = b.id_dsk and a.jenisnya = 'kapal' 
		join pro_pr_detail c on b.id_prd = c.id_prd 
		join pro_po_customer_plan d on c.id_plan = d.id_plan 
		join pro_po_customer e on d.id_poc = e.id_poc
		join pro_invoice_admin f on a.id_invoice=f.id_invoice
		where 1=1 and a.id_invoice = '" . $data['id_invoice'] . "' 
		order by id_invoice_detail";

		$listData1 	= $con->getResult($sql02);

		$arrPengeluaran = (count($listData1) > 0) ? $listData1 : array();
		if (count($arrPengeluaran) > 0) {
			$total_invoice = 0;
			$arrDO = array();
			$arrSJ = array();
			$arrVolume = array();
			foreach ($arrPengeluaran as $data1) {

				if ($datenow > "2024-07-16") {
					if ($data1['realisasi_volume'] != 0) {
						$tgl_realisasi = date("d-M-Y", strtotime($data1['tgl_realisasi']));
					} else {
						$tgl_realisasi = "";
					}
				} else {
					$decode = json_decode($data1['status_pengiriman'], true);
					if ($data1['realisasi_volume'] != 0) {
						foreach ($decode as $dec) {
							if ($dec['status'] == "Entry data terima surat jalan dan realisasi volume") {
								$tgl_db = tgl_db($dec['tanggal']);
								$tgl_realisasi = date("d-M-Y", strtotime($tgl_db));
							}
						}
					} else {
						$tgl_realisasi = "";
					}
				}

				$tgl_delivered 	= ($data1['tgl_delivered']) ? date('d-M-Y', strtotime($data1['tgl_delivered'])) : '';

				$realisasi_volume = ($data1['realisasi_volume']) ? number_format($data1['realisasi_volume']) : '';

				$vol_kirim = ($data1['vol_kirim']) ? number_format($data1['vol_kirim']) : '';

				array_push($arrVolume, $vol_kirim);

				if ($data1['no_do_acurate'] == NULL) {
					$no_do = $data1['no_do_syop'];
				} else {
					$no_do = $data1['no_do_acurate'];
				}
				array_push($arrDO, $no_do);

				$jumlah_harga = $data1['vol_kirim'] * $data1['harga_kirim'];
				$total_invoice 	+= +$jumlah_harga;
				$no_spj = $data1['no_spj'];
				array_push($arrSJ, $no_spj);

				$sql_penawaran 	= "SELECT a.*, d.harga_dasar, d.detail_rincian, d.pembulatan FROM pro_invoice_admin_detail a JOIN pro_po_ds_detail b ON a.id_dsd=b.id_dsd and a.jenisnya = 'truck' JOIN pro_po_customer c ON b.id_poc=c.id_poc JOIN pro_penawaran d ON c.id_penawaran=d.id_penawaran WHERE a.id_invoice='" . $data1['id_invoice'] . "' LIMIT 1";
				$result02 	= $con->getRecord($sql_penawaran);

				if ($result02['pembulatan'] == 2) {
					$harga_kirim = ($result02['harga_kirim']) ? number_format($result02['harga_kirim'], 4) : '';
					$jumlah_harga_fix = number_format($jumlah_harga, 4);
					$total_invoice_fix = number_format($total_invoice, 4);
				} elseif ($result02['pembulatan'] == 0) {
					$harga_kirim = ($result02['harga_kirim']) ? number_format($result02['harga_kirim'], 2) : '';
					$jumlah_harga_fix = number_format($jumlah_harga, 2);
					$total_invoice_fix = number_format($total_invoice, 2);
				} else {
					$harga_kirim = ($result02['harga_kirim']) ? number_format($result02['harga_kirim'], 0) : '';
					$jumlah_harga_fix = number_format($jumlah_harga, 0);
					$total_invoice_fix = number_format($total_invoice, 0);
				}
			}
		}

		if ($data['tgl_invoice_dikirim'] == NULL) {
			$tgl_inv_send = "";
		} else {
			$tgl_inv_send = date("d-M-Y", strtotime($data['tgl_invoice_dikirim']));
		}

		$tot1++;
		$writer->writeSheetRow($sheet, array(
			$tot1,
			implode(" \n", $arrDO),
			implode(" \n", $arrSJ),
			$data['no_invoice'],
			$data['nama_customer'],
			implode(" \n", $arrVolume),
			$total_invoice_fix,
			date('d-M-Y', strtotime($data1['tgl_invoice'])),
			$data['top_payment'],
			date('d-M-Y', strtotime($data['tgl_invoice'] . "+" . $data['top_payment'] . " days")),
			// $tgl_realisasi,
			$tgl_inv_send,
			$tgl_delivered
		), $row_options = array('height' => 30, 'wrap_text' => true));
	}
	$last++;
} else {
	$writer->writeSheetRow($sheet, array("Data tidak ada"));
	$writer->newMergeCell($sheet, "A" . $start, "M" . $start);
	$start++;
}

$con->close();
$writer->writeToStdOut();
exit(0);

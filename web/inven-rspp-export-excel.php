<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");
require_once($public_base_directory . "/libraries/helper/excelwriter/XlsxWriterModif.php");

$auth	= new MyOtentikasi();
$enk  	= decode($_SERVER['REQUEST_URI']);
$con 	= new Connection();
$flash	= new FlashAlerts;

$arrBulan01 = array(1 => "JANUARI", "FEBRUARI", "MARET", "APRIL", "MEI", "JUNI", "JULI", "AGUSTUS", "SEPTEMBER", "OKTOBER", "NOVEMBER", "DESEMBER");
$arrBulan02 = array(1 => "JAN", "FEB", "MAR", "APR", "MEI", "JUN", "JUL", "AGU", "SEP", "OKT", "NOV", "DES");

$q1 = (isset($enk['q1']) && $enk['q1'] ? htmlspecialchars($enk['q1'], ENT_QUOTES) : NULL);
$q2 = (isset($enk['q2']) && $enk['q2'] ? htmlspecialchars($enk['q2'], ENT_QUOTES) : date('m'));
$q3 = (isset($enk['q3']) && $enk['q3'] ? htmlspecialchars($enk['q3'], ENT_QUOTES) : date('Y'));
$q4 = (isset($enk['q4']) && $enk['q4'] ? htmlspecialchars($enk['q4'], ENT_QUOTES) : NULL);
$q5 = (isset($enk['q5']) && $enk['q5'] ? htmlspecialchars($enk['q5'], ENT_QUOTES) : NULL);
$idproduk = (isset($enk['idproduk']) && $enk['idproduk'] ? htmlspecialchars($enk['idproduk'], ENT_QUOTES) : NULL);

$display = (isset($enk['display']) && $enk['display'] ? 1 : 0);
$sesRole = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$sesGrup = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);
$sesCbng = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);


$filename = "LAPORAN RSSP";
$txtJudul01 = "";
if ($q1 == '1') {
	$sql00a = "select id_master, concat(nama_terminal,' ',tanki_terminal) as nama_terminal from pro_master_terminal where id_master = '" . $q4 . "'";
	$res00a = $con->getRecord($sql00a);
	$filename .= " " . strtoupper($res00a['nama_terminal']) . " " . strtoupper($arrBulan01[$q2]) . " " . $q3;
	$txtJudul01 = strtoupper($res00a['nama_terminal']);
} else if ($q1 == '2') {
	$sql00a = "select id_master, nama_cabang from pro_master_cabang where id_master = '" . $q5 . "'";
	$res00a = $con->getRecord($sql00a);
	$filename .= " CABANG " . strtoupper($res00a['nama_cabang']) . " " . strtoupper($arrBulan01[$q2]) . " " . $q3;
	$txtJudul01 = "CABANG " . strtoupper($res00a['nama_cabang']);
} else if ($q1 == '3') {
	$filename .= " NASIONAL " . strtoupper($arrBulan01[$q2]) . " " . $q3;
	$txtJudul01 = "NASIONAL";
}
$filename .= ".xlsx";

if (!$is_cron) {
	header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
}

function getNameFromNumber($num)
{
	$numnya = ($num - 1) % 26;
	$letter = chr(65 + $numnya);
	$num2 	= intval(($num - 1) / 26);
	return ($num2 > 0) ? getNameFromNumber($num2) . $letter : $letter;
}

$nextMonth 	= date("Y-m-d", strtotime('+1 month', strtotime($q3 . '-' . $q2 . '-01')));
$tglakhir01 = date('t', strtotime($q3 . '-' . $q2 . '-01'));

$cek = "select * from pro_master_produk where is_active = 1 order by id_master";
$row = $con->getResult($cek);
$rowProduk = (count($row) > 0) ? $row : array();

$urut = 0;
foreach ($rowProduk as $idxProduk => $dataProduk) {
	$urut++;
	$arrWidth[$urut] 	= array("29");
	$arrColType[$urut] 	= array("string");
	$arrJudul[$urut] 	= array("LAPORAN RSSP " . $txtJudul01, "BULAN " . strtoupper($arrBulan01[$q2]) . " " . $q3, "Produk : " . strtoupper($dataProduk['jenis_produk'] . ' - ' . $dataProduk['merk_dagang']));
	$arrHead1[$urut] 	= array("DEPOT TERMINAL");
	$arrIsiDataKosong[$urut] 	= array("Data Awal untuk terminal dan produk ini belum diset");
	$arrIsiDataInvalid[$urut] 	= array("Nilai Data Awal Bukan Pada Bulan " . ucwords(strtolower($arrBulan01[$q2])) . " Tahun " . $q3 . "");

	for ($tglnya = 1; $tglnya <= $tglakhir01; $tglnya++) {
		$lastCol = ($i == intval($strBln)) ? '01 S.D ' . $strTgl . ' ' . $arrBulan01[$i] . ' ' . $strThn : $arrBulan01[$i];
		$arrWidth[$urut][] 		= "16";
		$arrColType[$urut][] 	= "price_no_dec";
		$arrHead1[$urut][] 		= str_pad($tglnya, 2, '0', STR_PAD_LEFT) . ' ' . $arrBulan02[$q2];
		$arrIsiDataKosong[$urut][] 	= "";
		$arrIsiDataInvalid[$urut][] = "";
	}
}
/*echo '<pre>'; 
	print_r($arrWidth); 
	print_r($arrColType); 
	print_r($arrHead1); 
	print_r($arrHead2); 
	exit;*/

$writer = new XLSXWriterModif();
$index 	= 0;
foreach ($rowProduk as $idxProduk => $dataProduk) {
	$index++;
	$idproduk = $dataProduk['id_master'];
	$nmproduk = strtoupper($dataProduk['jenis_produk'] . ' - ' . $dataProduk['merk_dagang']);

	$sheet 	= $nmproduk;
	$style1 = array('height' => 18, 'halign' => 'left', 'valign' => 'center', 'font-style' => 'bold', 'font-size' => '11');

	//$writer->setSheetAndColOption(Nama Sheet, ['widths'=>[10,20,30,40], 'freeze_rows'=>4, 'freeze_columns'=>3]);
	$writer->setSheetAndColOption($sheet, ['widths' => $arrWidth[$index]]);

	$writer->writeSheetHeaderAndRow($sheet, array($arrJudul[$index][0]), $style1);
	$writer->newMergeCell($sheet, "A1", getNameFromNumber(count($arrWidth[$index])) . "1");

	$writer->writeSheetHeaderAndRow($sheet, array($arrJudul[$index][1]), $style1);
	$writer->newMergeCell($sheet, "A2", getNameFromNumber(count($arrWidth[$index])) . "2");

	$writer->writeSheetHeaderAndRow($sheet, array(""));
	$writer->newMergeCell($sheet, "A3", getNameFromNumber(count($arrWidth[$index])) . "3");

	$writer->writeSheetHeaderAndRow($sheet, array($arrJudul[$index][2]), $style1);
	$writer->newMergeCell($sheet, "A4", getNameFromNumber(count($arrWidth[$index])) . "4");

	$start = 5;
	$rowHead1 = $start;

	$style2 = array(
		'height' => 25, 'halign' => 'center', 'valign' => 'center', 'font-style' => 'bold', 'font-size' => '9', 'wrap_text' => true,
		'border' => 'left:thin,right:thin,top:thin,bottom:thin', 'fill' => '#c2d1ec'
	);

	$writer->writeSheetHeaderAndRow($sheet, $arrHead1[$index], $style2);
	$start++;

	$arrStyle3 = array(
		'default' => ['valign' => 'top', 'border' => 'left:thin,right:thin,top:thin,bottom:thin', 'wrap_text' => true, 'font-size' => '9'],
	);

	$sqlcek01 = "
			select a.id_datanya, a.id_produk, a.id_terminal 
			from new_pro_inventory_depot a 
			join pro_master_terminal b on a.id_terminal = b.id_master 
			where a.id_jenis = 1 and a.id_produk = '" . $idproduk . "' 
		";

	if ($q1 == '1' || $q1 == '2') {
		if ($q4) {
			$where01 = " and a.id_terminal = '" . $q4 . "'";
			$where02 = " and a.id_master = '" . $q4 . "'";
			$sqlcek01 .= " and a.id_terminal = '" . $q4 . "'";
		}

		if ($q5) {
			$where01 = " and b.id_cabang = '" . $q5 . "'";
			$where02 = " and a.id_cabang = '" . $q5 . "'";
			$sqlcek01 .= " and b.id_cabang = '" . $q5 . "'";
		}
	} else {
		$where01 = "";
		$where02 = "";
		$sqlcek01 .= "";
	}
	$rescek01 = $con->getResult($sqlcek01);
	if (count($rescek01) > 0) {
		require_once($public_base_directory . "/web/models/inven-rspp-data-awal.php");
		$resutama01 = $con->getResult($sqlutama01);

		require_once($public_base_directory . "/web/models/inven-rspp-input.php");
		$resutama02 = $con->getResult($sqlutama02);

		require_once($public_base_directory . "/web/models/inven-rspp-input-adj.php");
		$resutama03 = $con->getResult($sqlutama03);

		require_once($public_base_directory . "/web/models/inven-rspp-output.php");
		$resutama04 = $con->getResult($sqlutama04);

		require_once($public_base_directory . "/web/models/inven-rspp-output-adj.php");
		$resutama05 = $con->getResult($sqlutama05);

		$arrDataUtama = array();
		foreach ($resutama01 as $idx => $data) {
			$arrIsi = array();
			foreach (array_keys($data) as $data12) {
				if (intval($data12) == '0' && $data12 != '0')
					$arrIsi[$data12] = $data[$data12];
			}
			$arrDataUtama[$data['id_terminal']] = $arrIsi;
		}

		foreach ($resutama02 as $idx => $data) {
			if ($idx == 0) {
				$arrDataUtama['supplyTxt']['id_terminal'] 	= NULL;
				$arrDataUtama['supplyTxt']['ket_terminal'] 	= 'SUPPLY';
			}

			$arrDataUtama[$data['id_terminal'] . '.supply']['id_terminal'] 	= $data['id_terminal'];
			$arrDataUtama[$data['id_terminal'] . '.supply']['ket_terminal'] 	= $data['ket_terminal'];

			for ($tglnya = 1; $tglnya <= $tglakhir01; $tglnya++) {
				$txtTglnya = str_pad($tglnya, 2, '0', STR_PAD_LEFT);
				$arrDataUtama[$data['id_terminal'] . '.supply']['col' . $txtTglnya] = $data['col' . $txtTglnya];
			}
		}

		foreach ($resutama03 as $idx => $data) {
			if ($idx == 0) {
				$arrDataUtama['supplyAdjTxt']['id_terminal'] 	= NULL;
				$arrDataUtama['supplyAdjTxt']['ket_terminal'] 	= 'ADJUSTMENT (+)';
			}

			$arrDataUtama[$data['id_terminal'] . '.supplyAdj']['id_terminal'] 	= $data['id_terminal'];
			$arrDataUtama[$data['id_terminal'] . '.supplyAdj']['ket_terminal'] 	= $data['ket_terminal'];

			for ($tglnya = 1; $tglnya <= $tglakhir01; $tglnya++) {
				$txtTglnya = str_pad($tglnya, 2, '0', STR_PAD_LEFT);
				$arrDataUtama[$data['id_terminal'] . '.supplyAdj']['col' . $txtTglnya] = $data['col' . $txtTglnya];
			}
		}

		foreach ($resutama04 as $idx => $data) {
			if ($idx == 0) {
				$arrDataUtama['outputTxt']['id_terminal'] 	= NULL;
				$arrDataUtama['outputTxt']['ket_terminal'] 	= 'SALES';
			}
			$arrDataUtama[$data['id_terminal'] . '.output']['id_terminal'] 	= $data['id_terminal'];
			$arrDataUtama[$data['id_terminal'] . '.output']['ket_terminal'] 	= $data['ket_terminal'];

			for ($tglnya = 1; $tglnya <= $tglakhir01; $tglnya++) {
				$txtTglnya = str_pad($tglnya, 2, '0', STR_PAD_LEFT);
				$arrDataUtama[$data['id_terminal'] . '.output']['col' . $txtTglnya] = $data['col' . $txtTglnya];
			}
		}

		foreach ($resutama05 as $idx => $data) {
			if ($idx == 0) {
				$arrDataUtama['outputAdjTxt']['id_terminal'] 	= NULL;
				$arrDataUtama['outputAdjTxt']['ket_terminal'] 	= 'ADJUSTMENT (-)';
			}

			$arrDataUtama[$data['id_terminal'] . '.outputAdj']['id_terminal'] 	= $data['id_terminal'];
			$arrDataUtama[$data['id_terminal'] . '.outputAdj']['ket_terminal'] 	= $data['ket_terminal'];

			for ($tglnya = 1; $tglnya <= $tglakhir01; $tglnya++) {
				$txtTglnya = str_pad($tglnya, 2, '0', STR_PAD_LEFT);
				$arrDataUtama[$data['id_terminal'] . '.outputAdj']['col' . $txtTglnya] = $data['col' . $txtTglnya] * -1;
			}
		}

		$arrGrandTotal = array("ket_terminal" => "TOTAL ENDING");
		for ($tglnya = 1; $tglnya <= $tglakhir01; $tglnya++) {
			$txtTglnya = str_pad($tglnya, 2, '0', STR_PAD_LEFT);
			$arrGrandTotal['col' . $txtTglnya] = 0;
		}

		$grandTot01 = 0;
		$grandTot02 = 0;
		$grandTot03 = 0;
		$grandTot04 = 0;
		$grandTot05 = 0;
		$grandTot06 = 0;
		$grandTot07 = 0;
		$grandTot08 = 0;
		$grandTot09 = 0;
		$grandTot10 = 0;
		$grandTot11 = 0;
		$grandTot12 = 0;
		$grandTot13 = 0;
		$grandTot14 = 0;
		$grandTot15 = 0;
		$grandTot16 = 0;
		$grandTot17 = 0;
		$grandTot18 = 0;
		$grandTot19 = 0;
		$grandTot20 = 0;
		$grandTot21 = 0;
		$grandTot22 = 0;
		$grandTot23 = 0;
		$grandTot24 = 0;
		$grandTot25 = 0;
		$grandTot26 = 0;
		$grandTot27 = 0;
		$grandTot28 = 0;
		$grandTot29 = 0;
		$grandTot30 = 0;
		$grandTot31 = 0;

		foreach ($resutama01 as $idx => $data) {
			if ($idx == 0) {
				$arrDataUtama['totalTxt']['id_terminal'] 	= NULL;
				$arrDataUtama['totalTxt']['ket_terminal'] 	= 'ENDING';
			}
			$arrDataUtama[$data['id_terminal'] . '.total']['id_terminal'] 	= $data['id_terminal'];
			$arrDataUtama[$data['id_terminal'] . '.total']['ket_terminal'] 	= $data['ket_terminal'];

			for ($tglnya = 1; $tglnya <= $tglakhir01; $tglnya++) {
				if ($tglnya == '1') {
					$txtTglnya = str_pad($tglnya, 2, '0', STR_PAD_LEFT);
					$arrDataUtama[$data['id_terminal'] . '.total']['col' . $txtTglnya] = $data['col' . $txtTglnya] +
						($arrDataUtama[$data['id_terminal'] . '.supply']['col' . $txtTglnya] + $arrDataUtama[$data['id_terminal'] . '.supplyAdj']['col' . $txtTglnya]) -
						($arrDataUtama[$data['id_terminal'] . '.output']['col' . $txtTglnya] + $arrDataUtama[$data['id_terminal'] . '.outputAdj']['col' . $txtTglnya]);
				} else {
					$txtTglnya = str_pad($tglnya, 2, '0', STR_PAD_LEFT);
					$txtTglBef = str_pad(($tglnya - 1), 2, '0', STR_PAD_LEFT);

					$arrDataUtama[$data['id_terminal'] . '.total']['col' . $txtTglnya] = $arrDataUtama[$data['id_terminal'] . '.total']['col' . $txtTglBef] +
						($arrDataUtama[$data['id_terminal'] . '.supply']['col' . $txtTglnya] + $arrDataUtama[$data['id_terminal'] . '.supplyAdj']['col' . $txtTglnya]) -
						($arrDataUtama[$data['id_terminal'] . '.output']['col' . $txtTglnya] + $arrDataUtama[$data['id_terminal'] . '.outputAdj']['col' . $txtTglnya]);
				}

				$arrGrandTotal['col' . $txtTglnya] = $arrGrandTotal['col' . $txtTglnya] + $arrDataUtama[$data['id_terminal'] . '.total']['col' . $txtTglnya];
			}
		}

		foreach ($resutama01 as $idx => $data) {
			for ($tglnya = 2; $tglnya <= $tglakhir01; $tglnya++) {
				$txtTglnya = str_pad($tglnya, 2, '0', STR_PAD_LEFT);
				$txtTglBef = str_pad(($tglnya - 1), 2, '0', STR_PAD_LEFT);
				$arrDataUtama[$data['id_terminal']]['col' . $txtTglnya] = $arrDataUtama[$data['id_terminal'] . '.total']['col' . $txtTglBef];
			}
		}

		//echo '<pre>'; print_r($arrDataUtama); echo '</pre>';

		if (count($arrDataUtama) > 0) {
			$arrDataMain = array("supplyTxt", "supplyAdjTxt", "outputTxt", "outputAdjTxt", "totalTxt");
			$nomsnya = 0;
			foreach ($arrDataUtama as $idxnya => $datanya) {
				$nomsnya++;
				if (in_array($idxnya, $arrDataMain)) {
					$arrIsiData = array(
						$datanya['ket_terminal'],
					);
					for ($tglnya = 1; $tglnya <= $tglakhir01; $tglnya++) {
						$arrIsiData[] = "";
					}
					$style3a = array('height' => 20, 'border' => 'left:thin,right:thin,top:thin,bottom:thin', 'valign' => 'center', 'font-style' => 'bold', 'font-size' => '9', 'fill' => '#f4f4f4');
					$writer->writeSheetHeaderAndRow($sheet, $arrIsiData, $style3a, ($nomsnya == 1 ? $arrColType[$index] : array()));
					$writer->newMergeCell($sheet, "A" . $start, getNameFromNumber(count($arrWidth[$index])) . $start);
					$start++;
				} else {
					$arrIsiData = array($datanya['ket_terminal']);
					for ($tglnya = 1; $tglnya <= $tglakhir01; $tglnya++) {
						$txtTglnya = str_pad($tglnya, 2, '0', STR_PAD_LEFT);
						$arrIsiData[] = ($datanya['col' . $txtTglnya] ? $datanya['col' . $txtTglnya] : 0);
					}
					$writer->writeSheetHeaderAndRow($sheet, $arrIsiData, $arrStyle3, ($nomsnya == 1 ? $arrColType[$index] : array()));
					$start++;
				}
			}

			$arrColTypeTotal = array("string");
			$arrIsiData = array($arrGrandTotal['ket_terminal']);
			for ($tglnya = 1; $tglnya <= $tglakhir01; $tglnya++) {
				$arrColTypeTotal[] = "price_no_dec";

				$txtTglnya = str_pad($tglnya, 2, '0', STR_PAD_LEFT);
				$arrIsiData[] = ($arrGrandTotal['col' . $txtTglnya] ? $arrGrandTotal['col' . $txtTglnya] : 0);
			}
			$style3a = array('height' => 20, 'border' => 'left:thin,right:thin,top:thin,bottom:thin', 'valign' => 'center', 'font-style' => 'bold', 'font-size' => '9', 'fill' => '#f4f4f4');
			$writer->writeSheetHeaderAndRow($sheet, $arrIsiData, $style3a, $arrColTypeTotal);
			$start++;
		} else {
			$style3a = array('height' => 20, 'border' => 'left:thin,right:thin,top:thin,bottom:thin', 'halign' => 'left', 'valign' => 'center', 'font-size' => '9');
			$writer->writeSheetHeaderAndRow($sheet, $arrIsiDataInvalid[$index], $style3a);
			$writer->newMergeCell($sheet, "A" . $start, getNameFromNumber(count($arrWidth[$index])) . $start);
			$start++;
		}
	} else {
		$style3a = array('height' => 20, 'border' => 'left:thin,right:thin,top:thin,bottom:thin', 'halign' => 'left', 'valign' => 'center', 'font-size' => '9');
		$writer->writeSheetHeaderAndRow($sheet, $arrIsiDataKosong[$index], $style3a);
		$writer->newMergeCell($sheet, "A" . $start, getNameFromNumber(count($arrWidth[$index])) . $start);
		$start++;
	}
}

$writer->writeToStdOutExt();
exit(0);

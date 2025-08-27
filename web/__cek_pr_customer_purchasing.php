<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$conSub = new Connection();
$valid	= true;
$answer	= array();
$prnya 	= htmlspecialchars($_POST['prnya'], ENT_QUOTES);

if ($prnya == 'bm') {
	foreach ($_POST['cek'] as $idx => $val) {
		$chk = htmlspecialchars($_POST['cek'][$idx], ENT_QUOTES);
		$vol = htmlspecialchars($_POST['vol'][$idx], ENT_QUOTES);
		$ket = ($_POST['ket'][$idx]) ? htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['ket'][$idx]), ENT_QUOTES) : 0;
		if ($ket > $vol) {
			$valid = false;
			$pesan = "Perubahan volume pengiriman lebih besar daripada yang dijadwalkan";
		}
	}
} else if ($prnya == 'purchasing') {
	$arrVolumeAwal = [];
	$total_parent = 0;
	$arrTempStok 	= array();
	$arrTotalVolume = array();
	$arrTrip = array();
	$depotUsed = array();
	foreach ($_POST['dp8'] as $idx => $val) {
		$chk 	= htmlspecialchars($_POST['cek'][$idx], ENT_QUOTES);
		$split 	= htmlspecialchars($_POST['form_split_pr'][$idx], ENT_QUOTES);
		// $dt1 	= htmlspecialchars($_POST['dp1'][$idx], ENT_QUOTES);
		// $dt2 	= htmlspecialchars($_POST['dp2'][$idx], ENT_QUOTES);
		$dt2 	= htmlspecialchars(str_replace(',', '', $_POST['dp2'][$idx]), ENT_QUOTES);
		$dt8 	= htmlspecialchars($_POST['dp8'][$idx], ENT_QUOTES);
		$ips 	= htmlspecialchars($_POST['ps1'][$idx], ENT_QUOTES);
		$ipr 	= htmlspecialchars($_POST['pr1'][$idx], ENT_QUOTES);
		$idv 	= htmlspecialchars($_POST['dv1'][$idx], ENT_QUOTES);
		$np 	= htmlspecialchars($_POST['np1'][$idx], ENT_QUOTES);
		$si 	= htmlspecialchars($_POST['si1'][$idx], ENT_QUOTES);
		$vol 	= htmlspecialchars(str_replace(',', '', $_POST['dp10'][$idx]), ENT_QUOTES);
		$volume = htmlspecialchars(str_replace(',', '', $_POST['volume'][$idx]), ENT_QUOTES);
		$urgent = htmlspecialchars(str_replace(',', '', $_POST['urgent'][$idx]), ENT_QUOTES);
		$attachment_condition = htmlspecialchars(str_replace(',', '', $_POST['attachment_condition'][$idx]), ENT_QUOTES);
		$total_parent 		= $volume;

		if ($chk && (!$idv || !$dt2 || !$dt8 || !$ips || !$ipr || !$np)) {
			$valid = false;
			$pesan = "Depot, PO Supplier dan Harga Beli harus diisi semua";
		}

		if ($chk && ($urgent == 1)) {
			$valid = false;
			$pesan = "Lampiran Wajib di isi";
			// Jika attachment_condition kosong atau tidak diisi, munculkan pesan dan hentikan proses lebih lanjut
		}



		$arrTempStok[$dt8] 		= $si;
		$arrTotalVolume[$dt8] 	= $arrTotalVolume[$dt8] + $volume;

		// $arrTotalVolume2[$idx] 	= $arrTotalVolume2[$idx] + $volume;
		$arrVolumeAwal[$idx] 	= $vol;
		$arrTrip[$idx] 			= $volume;

		if ($split != '') {
			$volume02 		= htmlspecialchars(str_replace(',', '', $_POST['volume'][$split]), ENT_QUOTES);
			$splitDepot = htmlspecialchars($_POST['dp8'][$split], ENT_QUOTES);
			// Validasi depot yang sama
			if ($dt8 != $splitDepot) {
				$valid = false;
				$pesan = "Depot harus sama antara yang di-split dan yang tidak di-split";
				break;
			}
			$total_parent 	= $total_parent + $volume02;
			if ($total_parent > $vol) {
				$valid = false;
				$pesan = "Perubahan volume delivery lebih besar daripada voume awal" . $total_parent . '_' . $volume02;
				break;
			}
		}
	}

	if (isset($_POST['newIdx'])) {
		foreach ($_POST['newIdx'] as $idx => $val) {
			$vol01 		= htmlspecialchars(str_replace(',', '', $_POST['newVolume'][$idx]), ENT_QUOTES);
			$newnp 		= htmlspecialchars($_POST['newnp1'][$idx], ENT_QUOTES);
			$newidv 	= htmlspecialchars($_POST['newdv1'][$idx], ENT_QUOTES);
			$newipr 	= htmlspecialchars($_POST['newpr1'][$idx], ENT_QUOTES);
			$newnop 	= htmlspecialchars($_POST['newnp1'][$idx], ENT_QUOTES);
			$newips 	= htmlspecialchars($_POST['newps1'][$idx], ENT_QUOTES);
			$newidpo	= htmlspecialchars($_POST['newidpro'][$idx], ENT_QUOTES);
			$newdt8 	= htmlspecialchars($_POST['newdp8'][$idx], ENT_QUOTES);
			$dt8 		= htmlspecialchars($_POST['dp8'][$_POST['newIdx'][$idx]], ENT_QUOTES);
			$newsi 		= htmlspecialchars($_POST['newsi1'][$idx], ENT_QUOTES);
			$vol02 		= htmlspecialchars(str_replace(',', '', $_POST['volume'][$_POST['newIdx'][$idx]]), ENT_QUOTES);
			$newdp10 	= htmlspecialchars(str_replace(',', '', $_POST['dp10'][$_POST['newIdx'][$idx]]), ENT_QUOTES);

			if ($newnp == "" || $newdt8 == "") {
				$valid = false;
				$pesan = "Depot, PO Supplier dan Harga Beli harus diisi semua";
			}



			$arrTempStok[$newdt8] = $newsi;
			$arrTotalVolume[$newdt8] = $arrTotalVolume[$newdt8] + $vol01;

			$arrVolumeAwal[$val] 	= $newdp10;
			$arrTrip[$val] 			= $vol01 + $vol02;

			// Validasi depot yang sama
			if ($newdt8 != $dt8) {
				$valid = false;
				$pesan = "Depot harus sama antara yang di-split dan yang tidak di-split";
				break;
			}

			// if ($vol01 == '0' || $vol02 == '0') {
			// 	$valid = false;
			// 	$pesan = "Kolom [Volume] harus diisi semua " . $vol01 . " - " . $vol02 . "";
			// } else if ($jml_vol > $total_vol[$_POST['newIdx'][$idx]]) {
			// 	$valid = false;
			// 	$pesan = "Perubahan volume delivery [" . $jml_vol . "] lebih besar daripada volume awal [" . $total_vol[$_POST['newIdx'][$idx]] . "]";
			// } else if ($jml_vol < $total_vol[$_POST['newIdx'][$idx]]) {
			// 	$valid = false;
			// 	$pesan = "Perubahan volume delivery [" . $jml_vol . "] lebih kecil daripada volume awal [" . $total_vol[$_POST['newIdx'][$idx]] . "]";
			// } else if ($jml_vol > $newsi) {
			// 	$valid = false;
			// 	$pesan = "Volume delivery [" . $jml_vol . "] lebih besar dari sisa stock [" . $newsi . "]";
			// }
		}
	}

	foreach ($arrTrip as $idx => $val) {
		if ($arrTrip[$idx] > $arrVolumeAwal[$idx]) {
			$valid = false;
			$pesan = "Perubahan volume delivery [" . number_format($arrTrip[$idx]) . "] lebih besar daripada volume awal [" . number_format($arrVolumeAwal[$idx]) . "]";
		} else if ($arrTrip[$idx] < $arrVolumeAwal[$idx]) {
			$valid = false;
			$pesan = "Perubahan volume delivery [" . number_format($arrTrip[$idx]) . "] lebih kecil daripada volume awal [" . number_format($arrVolumeAwal[$idx]) . "]";
		}
	}
	foreach ($arrTempStok as $id => $nilai) {
		if ($arrTempStok[$id] == "") {
			$valid = false;
			$pesan = "Ada PO Supplier yang belum di pilih";
		} else {
			// if ($arrTotalVolume[$id] > $arrTempStok[$id]) {
			// 	$valid = false;
			// 	$pesan = "Perubahan volume delivery [" . number_format($arrTotalVolume[$id]) . "] lebih besar daripada stock terminal [" . number_format($arrTempStok[$id]) . "]";
			// }
		}
	}
} else if ($prnya == 'cfo') {
	foreach ($_POST['cek'] as $idx => $val) {
		$chk = htmlspecialchars($_POST['cek'][$idx], ENT_QUOTES);
		$vol = htmlspecialchars($_POST['vol'][$idx], ENT_QUOTES);
		$dp2 = ($_POST['dp2'][$idx]) ? htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['dp2'][$idx]), ENT_QUOTES) : 0;
		$ket = ($_POST['ket'][$idx]) ? htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['ket'][$idx]), ENT_QUOTES) : 0;
		if ($ket > $vol) {
			$valid = false;
			$pesan = "Perubahan volume pengiriman lebih besar daripada yang dijadwalkan";
		} else if (!$dp2) {
			$valid = false;
			$pesan = "Harga beli belum diisi";
		}
	}
} else if ($prnya == 'cfoall') {
	foreach ($_POST['cek'] as $idx1 => $val1) {
		foreach ($_POST['cek'][$idx1] as $idx2 => $val2) {
			$chk = htmlspecialchars($_POST['cek'][$idx1][$idx2], ENT_QUOTES);
			$vol = htmlspecialchars($_POST['vol'][$idx1][$idx2], ENT_QUOTES);
			$dp2 = ($_POST['dp2'][$idx1][$idx2]) ? htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['dp2'][$idx1][$idx2]), ENT_QUOTES) : 0;
			$ket = ($_POST['ket'][$idx1][$idx2]) ? htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['ket'][$idx1][$idx2]), ENT_QUOTES) : 0;
			if ($ket > $vol) {
				$valid = false;
				$pesan = "Perubahan volume pengiriman lebih besar daripada yang dijadwalkan";
			} else if (!$dp2) {
				$valid = false;
				$pesan = "Harga beli belum diisi";
			}
		}
	}
}

$answer["error"] = ($valid) ? "" : $pesan;
echo json_encode($answer);
$conSub->close();

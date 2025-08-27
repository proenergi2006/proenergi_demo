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
$arrExt = array();

$nom = 0;
$valid = true;
$pesan = "";

if (isset($_POST["newdt4"])) {
	$arrExt = []; // Inisialisasi array untuk volume eksternal
	foreach ($_POST['newdt4'] as $idx1 => $val1) {
		$arrExt[$idx1] = 0;  // Set awal volume per index

		foreach ($_POST['newdt4'][$idx1] as $idx2 => $val2) {
			// Menghapus karakter yang tidak diperlukan dan membersihkan input
			$dt4 = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['newdt4'][$idx1][$idx2]), ENT_QUOTES);

			// Validasi jika volume tidak diisi
			if (!$dt4) {
				$valid = false;
				$pesan = "Kolom volume harus diisi semua.";
				break 2;  // Keluar dari kedua loop
			} else {
				// Menambahkan nilai volume untuk index tersebut
				$arrExt[$idx1] += $dt4;
			}
		}
	}
}

if (isset($_POST["dt4"]) && $valid) {  // Pastikan validasi masih true sebelum melanjutkan
	foreach ($_POST['dt4'] as $idx01 => $val01) {
		// Ambil nilai volume plan yang dijadwalkan dan bersihkan input
		$volplan = htmlspecialchars($_POST['volplan' . $idx01], ENT_QUOTES);

		// Menghapus karakter yang tidak diperlukan dan membersihkan input volume
		$dt4 = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['dt4'][$idx01]), ENT_QUOTES);

		// Validasi jika volume yang akan dikirimkan melebihi volume yang dijadwalkan
		$totalVolume = $arrExt[$idx01] + $dt4;
		if ($totalVolume > $volplan) {
			$valid = false;
			$selisih = $totalVolume - $volplan;
			$pesan = "Volume pengiriman lebih " . number_format($selisih, 0, ',', '.') . " Liter dari yang sudah dijadwalkan.";
			break;  // Keluar dari loop jika volume melebihi
		} elseif ($totalVolume < $volplan) {
			$valid = false;
			$selisih = $volplan - $totalVolume;
			$pesan = "Volume pengiriman kurang " . number_format($selisih, 0, ',', '.') . " Liter dari yang sudah dijadwalkan.";
			break;  // Keluar dari loop jika volume melebihi
		}
	}
}


/*foreach($_POST['cek'] as $idx1=>$val1){
		$volplan = htmlspecialchars($_POST['volplan'.$idx1], ENT_QUOTES);
		$vol_tmp = 0;
		foreach($val1 as $idx2=>$val2){
			$nom++;
			$dt1 = htmlspecialchars($_POST['dt1'][$idx1][$idx2], ENT_QUOTES);
			$dt2 = htmlspecialchars($_POST['dt2'][$idx1][$idx2], ENT_QUOTES);
			if($dt1 && $dt2){
				$vol_tmp = $vol_tmp + $dt2;
				if($vol_tmp > $volplan){
					$valid = false;
					$pesan = "Volume pengiriman tidak boleh melebihi dari yang sudah dijadwalkan";
					break 2;
				}
			} else{
				$valid = false;
				$pesan = "Produk dan Volume harus diisi semua";
				break 2;
			}
		}
	}*/

$answer["error"] = ($valid) ? "" : $pesan;
echo json_encode($answer);
$conSub->close();

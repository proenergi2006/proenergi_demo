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
$tombol = htmlspecialchars($_POST["tombol_klik"], ENT_QUOTES);

if ($valid) {
	if (isset($_POST["dt1"])) {
		foreach ($_POST['dt1'] as $idx1 => $val1) {
			$no_urut_po 	= isset($_POST['dt1'][$idx1]) ? htmlspecialchars($_POST['dt1'][$idx1], ENT_QUOTES) : null;
			$oa_flag 		= isset($_POST['dt2'][$idx1]) ? htmlspecialchars($_POST['dt2'][$idx1], ENT_QUOTES) : null;
			$ongkos_po 		= isset($_POST['dt3'][$idx1]) ? htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['dt3'][$idx1]), ENT_QUOTES) : null;
			$mobil_po 		= isset($_POST['dt4'][$idx1]) ? htmlspecialchars($_POST['dt4'][$idx1], ENT_QUOTES) : null;
			$sopir_po 		= isset($_POST['dt5'][$idx1]) ? htmlspecialchars($_POST['dt5'][$idx1], ENT_QUOTES) : null;
			$tgl_eta_po 	= isset($_POST['dt6'][$idx1]) ? htmlspecialchars($_POST['dt6'][$idx1], ENT_QUOTES) : null;
			$jam_eta_po 	= isset($_POST['dt7'][$idx1]) ? htmlspecialchars($_POST['dt7'][$idx1], ENT_QUOTES) : null;
			$tgl_etl_po 	= isset($_POST['dt8'][$idx1]) ? htmlspecialchars($_POST['dt8'][$idx1], ENT_QUOTES) : null;
			$jam_etl_po 	= isset($_POST['dt9'][$idx1]) ? htmlspecialchars($_POST['dt9'][$idx1], ENT_QUOTES) : null;
			$terminal_po 	= isset($_POST['dt10'][$idx1]) ? htmlspecialchars($_POST['dt10'][$idx1], ENT_QUOTES) : null;
			$trip_po 		= isset($_POST['dt11'][$idx1]) ? htmlspecialchars($_POST['dt11'][$idx1], ENT_QUOTES) : null;
			$multidrop_po 	= isset($_POST['dt12'][$idx1]) ? htmlspecialchars($_POST['dt12'][$idx1], ENT_QUOTES) : null;
			$ket_po 		= isset($_POST['dt13'][$idx1]) ? htmlspecialchars($_POST['dt13'][$idx1], ENT_QUOTES) : null;
			$tgl_kirim_po 	= isset($_POST['dt14'][$idx1]) ? htmlspecialchars($_POST['dt14'][$idx1], ENT_QUOTES) : null;
			$volume_po 		= isset($_POST['dt15'][$idx1]) ? htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['dt15'][$idx1]), ENT_QUOTES) : null;
			if ($tombol == 1) {
				if (!$no_urut_po || !$ongkos_po || !$mobil_po || !$sopir_po || !$tgl_eta_po || !$tgl_etl_po || !$jam_etl_po || !$terminal_po || !$trip_po || !$volume_po) {
					$valid = false;
					$pesan = "Kolom<br />[OA Disetujui], [Plat No.], [Driver],<br />[Tanggal dan Jam ETL],<br />[Tanggal ETA], dan [Trip]<br />harus diisi semua";
					break;
				}
			} else if ($tombol == 2) {
				if (!$no_urut_po || !$ongkos_po || !$tgl_eta_po || !$tgl_etl_po || !$jam_etl_po || !$terminal_po || !$trip_po || !$volume_po) {
					$valid = false;
					$pesan = "Kolom selain nomor plat, driver, keterangan dan multidrop harus diisi semua";
					break;
				}
				// } else if($tombol == 3){
				// 	if(!$ongkos_po || !$mobil_po || !$sopir_po || !$tgl_eta_po){
				// 		$valid = false;
				// 		$pesan = "Kolom<br />[OA Disetujui], [Plat No.], [Driver],<br />dan [Tanggal ETA]<br />harus diisi semua";
				// 		break;
				// 	}
			}
		}
	} else if (isset($_POST["dt4"]) && isset($_POST["dt5"])) {
		foreach ($_POST['dt4'] as $idx1 => $val1) {
			$ongkos_po 	= htmlspecialchars($_POST['dt3'][$idx1], ENT_QUOTES);
			$mobil_po 	= htmlspecialchars($_POST['dt4'][$idx1], ENT_QUOTES);
			$sopir_po 	= htmlspecialchars($_POST['dt5'][$idx1], ENT_QUOTES);
			$tgl_eta_po = htmlspecialchars($_POST['dt6'][$idx1], ENT_QUOTES);
		}
	} else {
		$valid = false;
		$pesan = "Data tidak dapat disimpan karena sedang diverifikasi transportir";
	}
}

$answer["error"] = ($valid) ? "" : $pesan;
echo json_encode($answer);
$conSub->close();

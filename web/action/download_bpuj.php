<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload", "pdfgen");

	$enk  	= decode($_SERVER['REQUEST_URI']);
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$printe = paramDecrypt($_SESSION["sinori".SESSIONID]["fullname"])." ".date("d/m/Y H:i:s")." WIB";
    $id 	= isset($enk["id"])?htmlspecialchars($enk["id"], ENT_QUOTES):'';
	// $idp 	= isset($enk["idp"])?htmlspecialchars($enk["idp"], ENT_QUOTES):'';
	// $idc 	= isset($enk["idc"])?htmlspecialchars($enk["idc"], ENT_QUOTES):'';
    $role 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
    
    $tanggal_bpuj = $_POST['tanggal_bpuj'];
    $nama_customer = $_POST['nama_customer'];
    $no_unit = $_POST['no_unit'];
    $nama_driver = $_POST['nama_driver'];
    $jarak_km = $_POST['jarak_km'];
    $driver_harian = $_POST['driver_harian'];
    $no_bukti = $_POST['no_bukti'];
    $jenis_tangki = $_POST['jenis_tangki'];
    $jasa_driver = $_POST['jasa_driver'];
    $jasa_trip = $_POST['jasa_trip'];
    $dexlite_bbm = $_POST['dexlite_bbm'];
    $dexlite_uang = $_POST['dexlite_uang'];
    $tol = $_POST['tol'];
    $uang_makan = $_POST['uang_makan'];
    $lain_lain = $_POST['lain_lain'];
    $kenek = $_POST['kenek'];
    $koordinasi = $_POST['koordinasi'];
    $parkir = $_POST['parkir'];
    $min_plus = $_POST['min_plus'];
    $multidrop = $_POST['multidrop'];
    $total = $_POST['total'];
    $yang_dibayar = $_POST['yang_dibayar'];
    $keterangan = $_POST['keterangan'];
    $keterangan_jenis_tangki = $_POST['keterangan_jenis_tangki'];
    $keterangan_jasa_driver = $_POST['keterangan_jasa_driver'];
    $keterangan_jasa_trip = $_POST['keterangan_jasa_trip'];
    $keterangan_tol = $_POST['keterangan_tol'];
    $keterangan_uang_makan = $_POST['keterangan_uang_makan'];
    $keterangan_lain_lain = $_POST['keterangan_lain_lain'];
    $keterangan_kenek = $_POST['keterangan_kenek'];
    $keterangan_koordinasi = $_POST['keterangan_koordinasi'];
    $keterangan_parkir = $_POST['keterangan_parkir'];
    $keterangan_min_plus = $_POST['keterangan_min_plus'];
    $keterangan_multidrop = $_POST['keterangan_multidrop'];
    $keterangan_total = $_POST['keterangan_total'];
    $keterangan_yang_dibayar = $_POST['keterangan_yang_dibayar'];
    $keterangan_keterangan = $_POST['keterangan_keterangan'];
	$set_keterangan = $_POST['set_keterangan'];
	
	$oke = true;
	$query = '
		INSERT INTO bpuj_history 
		SET 
			`tanggal_bpuj` = "' . $tanggal_bpuj . '",
			`nama_customer` = "' . $nama_customer . '",
			`no_unit` = "' . $no_unit . '",
			`nama_driver` = "' . $nama_driver . '",
			`jarak_km` = ' . empty($jarak_km) ?? 0 . ',
			`driver_harian` = ' . empty($driver_harian) ?? 0 . ',
			`jenis_tangki` = "' . $jenis_tangki . '",
			`jasa_driver` = "' . $jasa_driver . '",
			`jasa_trip` = "' . $jasa_trip . '",
			`dexlite_bbm` = "' . $dexlite_bbm . '",
			`dexlite_uang` = "' . $dexlite_uang . '",
			`tol` = "' . $tol . '",
			`uang_makan` = "' . $uang_makan . '",
			`lain_lain` = "' . $lain_lain . '",
			`kenek` = "' . $kenek . '",
			`koordinasi` = "' . $koordinasi . '",
			`parkir` = "' . $parkir . '",
			`min_plus` = "' . $min_plus . '",
			`multidrop` = "' . $multidrop . '",
			`total` = "' . $total . '",
			`yang_dibayar` = "' . $yang_dibayar . '",
			`keterangan` = "' . $keterangan . '",
			`keterangan_jenis_tangki` = "' . $keterangan_jenis_tangki . '",
			`keterangan_jasa_driver` = "' . $keterangan_jasa_driver . '",
			`keterangan_jasa_trip` = "' . $keterangan_jasa_trip . '",
			`keterangan_tol` = "' . $keterangan_tol . '",
			`keterangan_uang_makan` = "' . $keterangan_uang_makan . '",
			`keterangan_lain_lain` = "' . $keterangan_lain_lain . '",
			`keterangan_kenek` = "' . $keterangan_kenek . '",
			`keterangan_koordinasi` = "' . $keterangan_koordinasi . '",
			`keterangan_parkir` = "' . $keterangan_parkir . '",
			`keterangan_min_plus` = "' . $keterangan_min_plus . '",
			`keterangan_multidrop` = "' . $keterangan_multidrop . '",
			`keterangan_total` = "' . $keterangan_total . '",
			`keterangan_yang_dibayar` = "' . $keterangan_yang_dibayar . '",
			`keterangan_keterangan` = "' . $keterangan_keterangan . '",
			`set_keterangan` = "' . $set_keterangan . '"';

	$con->beginTransaction();
	$con->clearError();
	$con->setQuery($query);
	
	$oke = $oke && !$con->hasError();

	if ($oke) {
		$con->commit();
		$con->close();
		$flash->add("success", 'Berhasil download');
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();

		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}
	
	ob_start();
	require_once(realpath("./template/cetak_bpuj.php"));
	$content = ob_get_clean();
	ob_end_flush();
	$con->close();

	$mpdf = null;
	if (PHP_VERSION >= 5.6) {
		$mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
	} else
		$mpdf = new mPDF('c','A4'); 
	$mpdf->AddPage('P');
	$mpdf->SetDisplayMode('fullpage');
	$mpdf->WriteHTML($content);
	$filename = "BPUJ_";
	$mpdf->Output($filename.'_'.date('dmyHis').'.pdf', 'I');
	exit;
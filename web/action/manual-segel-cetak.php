<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload", "pdfgen");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$idr 	= isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';

	$sql = "select a.*, b.inisial_segel, b.kode_barcode from pro_manual_segel a join pro_master_cabang b on a.id_wilayah = b.id_master where a.id_master = '".$idr."'";
	$rsm = $con->getRecord($sql);
	$seg_aw = ($rsm['segel_awal'])?str_pad($rsm['segel_awal'],4,'0',STR_PAD_LEFT):'';
	$seg_ak = ($rsm['segel_akhir'])?str_pad($rsm['segel_akhir'],4,'0',STR_PAD_LEFT):'';
	if($rsm['jumlah_segel'] == 1)
		$nomor_segel = $rsm['inisial_segel']."-".$seg_aw;
	else if($rsm['jumlah_segel'] == 2)
		$nomor_segel = $rsm['inisial_segel']."-".$seg_aw." &amp; ".$rsm['inisial_segel']."-".$seg_ak;
	else if($rsm['jumlah_segel'] > 2)
		$nomor_segel = $rsm['inisial_segel']."-".$seg_aw." s/d ".$rsm['inisial_segel']."-".$seg_ak;
	else $nomor_segel = '';
	$printe = paramDecrypt($_SESSION["sinori".SESSIONID]["fullname"])." ".date("d/m/Y H:i:s")." WIB";
	$barcod = $rsm['kode_barcode'].'08'.str_pad($rsm['id_master'],6,'0',STR_PAD_LEFT);

	ob_start();
	require_once(realpath("./template/manual-segel.php"));
	$content = ob_get_clean();
	ob_end_flush();
	$con->close();

	$mpdf = null;
	if (PHP_VERSION >= 5.6) {
		$mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
	} else 
		$mpdf = new mPDF('c','A4',10,'arial',10,10,23,22,5,4); 
	$mpdf->AddPage('P');
	$mpdf->SetDisplayMode('fullpage');
	$mpdf->WriteHTML($content);
	$filename = "Manual-Segel-".sanitize_filename($rsm['nama_customer']);
	$mpdf->Output($filename.'_'.date('dmyHis').'.pdf', 'I');
	exit;
?>

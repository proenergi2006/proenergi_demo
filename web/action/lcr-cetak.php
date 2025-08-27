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
	$idk 	= htmlspecialchars($enk["idk"], ENT_QUOTES);

	$sql = "
		select a.*, b.nama_prov, c.nama_kab, d.nama_customer, d.kode_pelanggan, e.kode_barcode 
		from pro_customer_lcr a 
		join pro_master_provinsi b on a.prov_survey = b.id_prov 
		join pro_master_kabupaten c on a.kab_survey = c.id_kab 
		join pro_customer d on a.id_customer = d.id_customer 
		join pro_master_cabang e on a.id_wilayah = e.id_master 
		where a.id_lcr = '".$idk."' and a.id_customer = '".$idr."'
	";
	$rsm = $con->getRecord($sql);
	
	$arrTgl = array(1=>"I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
	$upload_dir	= $public_base_directory."/files/uploaded_user/files/";
	$upload_url	= BASE_URL."/files/uploaded_user/files/";
	$almtlokasi = $rsm['alamat_survey']." ".str_replace(array("KABUPATEN ","KOTA "), array("",""), $rsm['nama_kab'])." ".$rsm['nama_prov'];
	$surveyor 	= (json_decode($rsm['nama_surveyor'], true) === NULL)?array(""):json_decode($rsm['nama_surveyor'], true);
	$hasilsurv 	= (json_decode($rsm['hasilsurv'], true) === NULL)?array(""):json_decode($rsm['hasilsurv'], true);
	$kompetitor = (json_decode($rsm['kompetitor'], true) === NULL)?array(""):json_decode($rsm['kompetitor'], true);
	$produkvol 	= (json_decode($rsm['produkvol'], true) === NULL)?array(1):json_decode($rsm['produkvol'], true);
	$picustomer = (json_decode($rsm['picustomer'], true) === NULL)?array(1):json_decode($rsm['picustomer'], true);
	$jamOperasi = (json_decode($rsm['jam_operasional'], true) === NULL)?array(""):json_decode($rsm['jam_operasional'], true);
	$tangki 	= (json_decode($rsm['tangki'], true) === NULL)?array(1):json_decode($rsm['tangki'], true);
	$pendukung 	= (json_decode($rsm['pendukung'], true) === NULL)?array(1):json_decode($rsm['pendukung'], true);
	$kuantitas1 = (json_decode($rsm['quantity_tangki'], true) === NULL)?array(1):json_decode($rsm['quantity_tangki'], true);
	$kualitas1 	= (json_decode($rsm['quality_tangki'], true) === NULL)?array(1):json_decode($rsm['quality_tangki'], true);
	$kapal 		= (json_decode($rsm['kapal'], true) === NULL)?array(1):json_decode($rsm['kapal'], true);
	$jetty 		= (json_decode($rsm['jetty'], true) === NULL)?array(1):json_decode($rsm['jetty'], true);
	$kuantitas2 = (json_decode($rsm['quantity_kapal'], true) === NULL)?array(1):json_decode($rsm['quantity_kapal'], true);
	$kualitas2 	= (json_decode($rsm['quality_kapal'], true) === NULL)?array(1):json_decode($rsm['quality_kapal'], true);

	$file_jalan	= (json_decode($rsm['kondisi_jalan'], true) === NULL)?array(1):json_decode($rsm['kondisi_jalan'], true);
	$file_kntr 	= (json_decode($rsm['kantor_perusahaan'], true) === NULL)?array(1):json_decode($rsm['kantor_perusahaan'], true);
	$file_strg 	= (json_decode($rsm['fasilitas_storage'], true) === NULL)?array(1):json_decode($rsm['fasilitas_storage'], true);
	$file_inlet = (json_decode($rsm['inlet_pipa'], true) === NULL)?array(1):json_decode($rsm['inlet_pipa'], true);
	$file_ukur 	= (json_decode($rsm['alat_ukur_gambar'], true) === NULL)?array(1):json_decode($rsm['alat_ukur_gambar'], true);
	$file_media = (json_decode($rsm['media_datar'], true) === NULL)?array(1):json_decode($rsm['media_datar'], true);
	$file_ket 	= (json_decode($rsm['keterangan_lain'], true) === NULL)?array(1):json_decode($rsm['keterangan_lain'], true);
	$printe 	= paramDecrypt($_SESSION["sinori".SESSIONID]["fullname"])." ".date("d/m/Y H:i:s")." WIB";
	$barcod 	= $rsm['kode_barcode'].'02'.str_pad($rsm['id_lcr'],6,'0',STR_PAD_LEFT);

	ob_start();
	require_once(realpath("./template/form-lcr.php"));
	$content = ob_get_clean();
	ob_end_flush();
	$con->close();

	$mpdf = null;
	if (PHP_VERSION >= 5.6) {
		$mpdf = new \Mpdf\Mpdf(['format' => 'A4', 'setAutoTopMargin' => 'stretch', 'margin_left'=>'10', 'margin_right'=>'10', 
			'margin_top'=>'30', 'margin_bottom'=>'16', 'margin_header'=>'10', 'margin_footer'=>'10']);
	} else
		$mpdf = new mPDF('c','A4',10,'arial',10,10,30,16,5,4); 
	$mpdf->AddPage('P');
	$mpdf->SetDisplayMode('fullpage');
	$mpdf->shrink_tables_to_fit = 1;
	$mpdf->WriteHTML($content);
	
	$filename 	= "LCR_".str_pad($idk,4,'0',STR_PAD_LEFT);
	$mpdf->Output($filename.'_'.date('dmyHis').'.pdf', 'I');
	exit;

?>

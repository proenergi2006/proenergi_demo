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

	$cek = "select a.id_pr, a.nomor_pr, a.tanggal_pr, a.disposisi_pr, a.is_edited, a.id_wilayah, a.id_group, b.nama_cabang, c.id_par, c.tanggal_buat 
			from pro_pr a join pro_master_cabang b on a.id_wilayah = b.id_master left join pro_pr_ar c on a.id_pr = c.id_pr and c.ar_approved = 1 
			where a.id_pr = '".$idr."'";
    $row = $con->getResult($cek);

	$sql = "select a.*, b.sm_result, b.nomor_pr, b.sm_summary, b.sm_pic, b.sm_tanggal, c.tanggal_kirim, e.alamat_survey, e.id_wil_oa, f.nama_prov, g.nama_kab, h.nama_customer, h.id_customer, h.kode_pelanggan, i.fullname, l.nama_area, d.harga_poc, k.refund_tawar, m.jenis_produk, m.merk_dagang, e.jenis_usaha, d.nomor_poc, d.lampiran_poc, d.lampiran_poc_ori, d.id_poc, n.wilayah_angkut, o.nilai_pbbkb, j.kode_barcode
	        from pro_pr_detail a 
			join pro_pr b on a.id_pr = b.id_pr 
			join pro_po_customer_plan c on a.id_plan = c.id_plan 
			join pro_po_customer d on c.id_poc = d.id_poc 
			join pro_customer_lcr e on c.id_lcr = e.id_lcr
			join pro_master_provinsi f on e.prov_survey = f.id_prov 
			join pro_master_kabupaten g on e.kab_survey = g.id_kab
			join pro_customer h on d.id_customer = h.id_customer 
			join acl_user i on h.id_marketing = i.id_user 
			join pro_master_cabang j on h.id_wilayah = j.id_master 
			join pro_penawaran k on d.id_penawaran = k.id_penawaran  
			join pro_master_area l on k.id_area = l.id_master 
			join pro_master_produk m on d.produk_poc = m.id_master 
			join pro_master_wilayah_angkut n on e.id_wil_oa = n.id_master and e.prov_survey = n.id_prov and e.kab_survey = n.id_kab 
			join pro_master_pbbkb o on k.pbbkb_tawar = o.id_master 
	        where a.id_pr = '".$idr."' order by a.is_approved desc, c.tanggal_kirim, k.id_cabang, k.id_area, a.id_plan, a.id_prd";
	$res = $con->getResult($sql);
	$printe = paramDecrypt($_SESSION["sinori".SESSIONID]["fullname"])." ".date("d/m/Y H:i:s")." WIB";
	// created by Alvin
	$barcod  = isset($res[0]['kode_barcode'])?$res[0]['kode_barcode']:'07';
	if (strlen($barcod)==1)
		$barcod = '0'.$barcod;
	// $barcod = $barcod."10".substr(rand(), 0, 6);
	$nomor_pr = explode('/', $row[0]['nomor_pr'])[0];
	$barcod = $barcod."10".sprintf("%06d", $nomor_pr);
	// echo json_encode($res); die();

	ob_start();
	require_once(realpath("./template/purchase-request-detail.php"));
	$content = ob_get_clean();
	ob_end_flush();
	$con->close();

	$mpdf = null;
	if (PHP_VERSION >= 5.6) {
		$mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
	} else
		$mpdf = new mPDF('c','A4',9,'arial',10,10,10,10,0,5); 
	$mpdf->AddPage('L');
	$mpdf->SetDisplayMode('fullpage');
	$mpdf->WriteHTML($content);
	$filename = "Purchase_Request_".sanitize_filename($idr);
	$mpdf->Output($filename.'_'.date('dmyHis').'.pdf', 'I');
	exit;
?>

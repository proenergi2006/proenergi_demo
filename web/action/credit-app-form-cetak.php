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
		select 
		a.id_customer, a.id_marketing, a.id_wilayah, a.id_group, a.kode_pelanggan, a.nama_customer, a.alamat_customer, a.prov_customer, a.kab_customer, a.postalcode_customer, 
		a.telp_customer, a.fax_customer, a.email_customer, a.website_customer, a.tipe_bisnis, a.tipe_bisnis_lain, a.ownership, a.ownership_lain, 
		a.nomor_sertifikat, a.nomor_sertifikat_file, a.nomor_npwp, a.nomor_npwp_file, a.nomor_siup, a.nomor_siup_file, 
		a.nomor_tdp, a.nomor_tdp_file, a.dokumen_lainnya, a.dokumen_lainnya_file, 
		a.need_update, a.is_generated_link, a.count_update, a.is_verified, a.status_customer, 
		a.prospect_customer_date, a.prospect_evaluated, a.fix_customer_since, a.fix_customer_redate, 
		a.jenis_payment, a.top_payment, a.jenis_net, a.credit_limit, a.credit_limit_diajukan, 
		a.id_verification, a.ajukan, a.jenis_customer, a.induk_perusahaan, a.kecamatan_customer, a.kelurahan_customer, 
		a.lastupdate_time, a.lastupdate_ip, a.lastupdate_by, 		
				
		b.pic_decision_name, b.pic_decision_position, b.pic_decision_telp, b.pic_decision_mobile, b.pic_decision_email, 
		b.pic_ordering_name, b.pic_ordering_position, b.pic_ordering_telp, b.pic_ordering_mobile, b.pic_ordering_email, 
		b.pic_billing_name, b.pic_billing_position, b.pic_billing_telp, b.pic_billing_mobile, b.pic_billing_email, 
		b.pic_invoice_name, b.pic_invoice_position, b.pic_invoice_telp, b.pic_invoice_mobile, b.pic_invoice_email, 
		b.product_delivery_address, b.invoice_delivery_addr_primary, b.invoice_delivery_addr_secondary, 
		b.pic_fuelman_name, b.pic_fuelman_position, b.pic_fuelman_telp, b.pic_fuelman_mobile, b.pic_fuelman_email, 
				
		d.email_billing, d.alamat_billing, d.prov_billing, d.kab_billing, d.postalcode_billing, d.telp_billing, d.fax_billing, 
		d.payment_schedule, d.payment_schedule_other, d.payment_method, d.payment_method_other, d.invoice, d.ket_extra, 
		d.kecamatan_billing, d.kelurahan_billing, d.calculate_method, d.bank_name, d.curency, d.bank_address, d.account_number, 
		d.credit_facility, d.creditor, 
		
		e.logistik_area, e.logistik_bisnis, e.logistik_env, e.logistik_env_other, e.logistik_storage, e.logistik_storage_other, e.logistik_hour, e.logistik_hour_other, 
		e.logistik_volume, e.logistik_volume_other, e.logistik_quality, e.logistik_quality_other, e.logistik_truck, e.logistik_truck_other, 
		e.desc_stor_fac, e.desc_condition, e.supply_shceme, e.specify_product, e.volume_per_month, e.operational_hour_from, e.operational_hour_to, e.nico, 

		f.nama_prov as propinsi_customer, 
		g.nama_kab as kabupaten_customer, 
		h.nama_prov as propinsi_payment, 
		i.nama_kab as kabupaten_payment, 
		
		j.token_verification, j.is_evaluated, j.is_reviewed, j.is_active, 
		j.legal_data, j.legal_summary, j.legal_result, j.legal_tgl_proses, j.legal_pic, 
		j.finance_data, j.finance_summary, j.finance_result, j.finance_tgl_proses, j.finance_pic, 
		j.logistik_data, j.logistik_summary, j.logistik_result, j.logistik_tgl_proses, j.logistik_pic, 
		j.sm_summary, j.sm_result, j.sm_tgl_proses, j.sm_pic, 
		j.om_summary, j.om_result, j.om_tgl_proses, j.om_pic, 
		j.cfo_summary, j.cfo_result, j.cfo_tgl_proses, j.cfo_pic, 
		j.ceo_summary, j.ceo_result, j.ceo_tgl_proses, j.ceo_pic, 
		j.disposisi_result, j.is_approved, j.role_approve, j.tanggal_approved, 
		
		k.nama_cabang as wilayah, 
		
		l.id_review, l.review1, l.review2, l.review3, l.review4, l.review5, l.review6, l.review7, l.review8, l.review9, l.review10, 
		l.review11, l.review12, l.review13, l.review14, l.review15, l.review16, 
		l.review_result, l.review_pic, l.review_tanggal, l.review_summary, l.review_attach, l.review_attach_ori, 
		l.jenis_asset, l.kelengkapan_dok_tagihan, l.alur_proses_periksaan, 
		l.jadwal_penerimaan, l.background_bisnis, l.lokasi_depo, l.opportunity_bisnis, 

		'' as testajabos 

		from pro_customer a 
		left join pro_customer_contact b on a.id_customer = b.id_customer 
		left join pro_customer_payment d on a.id_customer = d.id_customer 
		left join pro_customer_logistik e on a.id_customer = e.id_customer 
		left join pro_master_provinsi f on a.prov_customer = f.id_prov 
		left join pro_master_kabupaten g on a.kab_customer = g.id_kab 
		left join pro_master_provinsi h on d.prov_billing = h.id_prov 
		left join pro_master_kabupaten i on d.kab_billing = i.id_kab 
		left join pro_customer_verification j on a.id_customer = j.id_customer 
		left join pro_customer_review l on j.id_verification = l.id_verification 
		left join pro_master_cabang k on a.id_wilayah = k.id_master and a.id_group = k.id_group_cabang 
		where j.id_verification = '".$idr."' and l.id_review = '".$idk."'
	";
	$rsm = $con->getRecord($sql);
	
	$tmp1 	= isset($rsm['kabupaten_customer'])?strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $rsm['kabupaten_customer'])):null;
	$alamat = isset($rsm['propinsi_customer'])?$rsm['alamat_customer']." ".ucwords($tmp1)." ".$rsm['propinsi_customer']:null;

	$arrTipeBisnis 	= array(
		1=>"Agriculture & Forestry / Horticulture", "Business & Information", "Construction/Utilities/Contracting", "Education", 
		"Finance & Insurance", "Food & hospitally", "Gaming", "Health Services", 
		"Motor Vehicle", $rsm['tipe_bisnis_lain'], "Natural Resources / Environmental", "Personal Service", "Manufacture"
	);
	$tipebisnis 	= ($arrTipeBisnis[$rsm['tipe_bisnis']] ? $arrTipeBisnis[$rsm['tipe_bisnis']] : '-');
	
	$arrOwnership 	= array(1=>"Affiliation", "National Private", "Foreign Private", "Joint Venture", "BUMN / BUMD", "Foundation", "Personal", $rsm['ownership_lain']);
	$ownership 		= ($arrOwnership[$rsm['ownership']] ? $arrOwnership[$rsm['ownership']] : '-');

	ob_start();
	require_once(realpath("./template/credit-application-form.php"));
	$content = ob_get_clean();
	ob_end_flush();
	$con->close();

	$mpdf = null;
	if (PHP_VERSION >= 5.6) {
		$mpdf = new \Mpdf\Mpdf(['format' => 'A4', 'setAutoTopMargin' => 'stretch']);
	} else
		$mpdf = new mPDF('c','A4',10,'arial',10,10,30,16,5,4); 
	$mpdf->AddPage('P');
	$mpdf->SetDisplayMode('fullpage');
	$mpdf->shrink_tables_to_fit = 1;
	$mpdf->WriteHTML($content);
	
	$kode_review 	= "RC".str_pad($idk,4,'0',STR_PAD_LEFT);
	$filename 		= "KYC_Review_Customer_".$kode_review;
	$mpdf->Output($filename.'_'.date('dmyHis').'.pdf', 'I');
	exit;

?>
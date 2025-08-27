<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload", "pdfgen");

	$enk  	= decode($_SERVER['REQUEST_URI']);
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$idr 	= isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
	$printe = paramDecrypt($_SESSION["sinori".SESSIONID]["fullname"])." ".date("d/m/Y H:i:s")." WIB";
	
	$sql = "select a.id_customer, a.id_marketing, a.nama_customer, a.alamat_customer, a.prov_customer, a.kab_customer, a.telp_customer, a.fax_customer, a.email_customer, 
			a.website_customer, a.tipe_bisnis, a.tipe_bisnis_lain, a.ownership, a.ownership_lain, a.need_update, a.count_update, a.status_customer, a.fix_customer_since,  a.induk_perusahaan, a.kecamatan_customer, a.kelurahan_customer, d.kecamatan_billing, d.kelurahan_billing, b.pic_fuelman_name, b.pic_fuelman_position, 
			b.pic_fuelman_telp, b.pic_fuelman_mobile, b.pic_fuelman_email, b.invoice_delivery_addr_primary, e.operational_hour_from, e.operational_hour_to, b.invoice_delivery_addr_secondary, product_delivery_address,d.calculate_method, e.supply_shceme, e.specify_product, e.volume_per_month, e.nico, d.bank_name, d.bank_address, d.curency, d.account_number, d.credit_facility, d.creditor,
			a.fix_customer_redate, a.top_payment, a.lastupdate_time, a.lastupdate_ip, a.lastupdate_by, b.pic_decision_name, b.pic_decision_position, b.pic_decision_telp, 
			b.pic_decision_mobile, b.pic_decision_email, b.pic_ordering_name, b.pic_ordering_position, b.pic_ordering_telp, b.pic_ordering_mobile, b.pic_ordering_email, 
			b.pic_billing_name, b.pic_billing_position, b.pic_billing_telp, b.pic_billing_mobile, b.pic_billing_email, b.pic_invoice_name, b.pic_invoice_position, 
			b.pic_invoice_telp, b.pic_invoice_mobile, b.pic_invoice_email, a.nomor_sertifikat, a.nomor_sertifikat_file, a.nomor_npwp, a.nomor_npwp_file, a.nomor_siup, 
			a.nomor_siup_file, a.nomor_tdp, a.nomor_tdp_file, d.email_billing, d.alamat_billing, d.prov_billing, d.kab_billing, d.telp_billing, d.fax_billing, 
			d.payment_schedule, d.payment_schedule_other, d.payment_method, d.payment_method_other, d.invoice, d.ket_extra, e.logistik_area, e.logistik_bisnis, e.logistik_env, 
			e.logistik_env_other, e.logistik_storage, e.logistik_storage_other, e.logistik_hour, e.logistik_hour_other, e.logistik_volume, e.logistik_volume_other, 
			e.logistik_quality, e.logistik_quality_other, e.logistik_truck, e.logistik_truck_other, f.nama_prov as propinsi_customer, g.nama_kab as kabupaten_customer, 
			h.nama_prov as propinsi_payment, i.nama_kab as kabupaten_payment, j.token_verification, j.is_evaluated, j.is_approved, a.jenis_payment, a.jenis_net, 
			a.postalcode_customer, d.postalcode_billing, a.credit_limit_diajukan, a.credit_limit, e.desc_stor_fac, e.desc_condition, a.id_verification, a.id_wilayah 
			from pro_customer a left join pro_customer_contact b on a.id_customer = b.id_customer 
			left join pro_customer_payment d on a.id_customer = d.id_customer left join pro_customer_logistik e on a.id_customer = e.id_customer 
			left join pro_master_provinsi f on a.prov_customer = f.id_prov left join pro_master_kabupaten g on a.kab_customer = g.id_kab 
			left join pro_master_provinsi h on d.prov_billing = h.id_prov left join pro_master_kabupaten i on d.kab_billing = i.id_kab 
			left join pro_customer_verification j on a.id_customer = j.id_customer and j.is_active = 1 where a.id_customer = '".$idr."'";
	$rsm = $con->getRecord($sql);
	
	$cek = "select inisial_cabang, kode_barcode from pro_master_cabang where id_master = '".$rsm['id_wilayah']."'";
	$row = $con->getRecord($cek);
	$barcod 		= $row['kode_barcode'].'09'.str_pad($rsm['id_customer'],6,'0',STR_PAD_LEFT);
	$inisial_cabang = $row['inisial_cabang'];

	$alamat_customer 	= $rsm['alamat_customer'];
	$alamat_payment 	= $rsm['alamat_billing'];
	$arrTipeBisnis 		= array(1=>"Agriculture & Forestry / Horticulture", "Business & Information", "Construction/Utilities/Contracting", "Education", "Finance & Insurance", "Food & hospitally", "Gaming", "Health Services", "Motor Vehicle", $rsm['tipe_bisnis_lain'],"Natural Resources / Environmental","Personal Service","Manufacture");
	$arrOwnership 	 	= array(1=>"Affiliation", "National Private", "Foreign Private", "Joint Venture", "BUMN / BUMD", "Foundation", "Personal", $rsm['ownership_lain']);
	$arrPaymentJadwal 	= array(1=>"Every Day", $rsm['payment_schedule_other']);
	$arrPaymentMethod 	= array(1=>"Cash", "Transfer", "Cheque / Giro", "Bank Guarantee", $rsm['payment_method_other']);
	$arrBuktiPotPPN 	= array("_________________", "Bukti Pot. PPn");
	$arrLogistikEnv 	= array(1=>"Industri", "Pemukiman", $rsm['logistik_env_other']);
	$arrLogistikStorage = array(1=>"Indoor", "Outdoor", $rsm['logistik_storage_other']);
	$arrLogistikHour 	= array(1=>"08.00 - 17.00", "24 Hours", $rsm['logistik_hour_other']);
	$arrLogistikVolume 	= array(1=>"Flowmeter", "Stick", $rsm['logistik_volume_other']);
	$arrLogistikQuality = array(1=>"BJ", $rsm['logistik_quality_other']);
	$arrLogistikTruck 	= array(1=>"5 KL", "8 KL", "10 KL", "16 KL", $rsm['logistik_truck_other']);
	$tmp_addr1 			= ucwords(strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $rsm['kabupaten_customer'])));
	$tmp_addr2 			= ucwords(strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $rsm['kabupaten_payment'])));
	$kecamatan_billing  = $rsm['kecamatan_billing'];
    $kelurahan_billing  = $rsm['kelurahan_billing'];
    $kecamatan_customer = $rsm['kecamatan_customer'];
    $kelurahan_customer = $rsm['kelurahan_customer'];
    $invoice_delivery_addr_primary  = '(1.3) '.$rsm['invoice_delivery_addr_primary'];
    $invoice_delivery_addr_secondary= '(1.4) '.$rsm['invoice_delivery_addr_secondary'];
    if ($rsm['specify_product']=='1'){
        $specify_product    = 'Prodiesel Bio (Bio Diesel)';
    }else if($rsm['specify_product']=='2'){
        $specify_product    = 'Promarine (MFO)';
    }else if($rsm['specify_product']=='3'){
        $specify_product    = 'Eneos (Lubricant)';
    }else{
        $specify_product    = '';
    }

    if($rsm['product_delivery_address']!=''){
        $product_delivery=json_decode($rsm['product_delivery_address'],TRUE);
    }else{
        $product_delivery['product_delivery_address']=[];
    }

	$arrTermPayment 	= array("CREDIT"=>"CREDIT", "CBD"=>"CBD (Cash Before Delivery)", "COD"=>"COD (Cash On Delivery)");
	$arrConditionInd 	= array(1=>"Setelah Invoice diterima", "Setelah Pengiriman", "Setelah Loading");
	$arrConditionEng 	= array(1=>"After Invoice Receive", "After Delivery", "After Loading");

	ob_start();
	require_once(realpath("./template/customer-cetak.php"));
	$content = ob_get_clean();
	ob_end_flush();
	$con->close();

	$mpdf = null;
	if (PHP_VERSION >= 5.6) {
		$mpdf = new \Mpdf\Mpdf(['format' => 'A4', 'setAutoTopMargin' => 'stretch']);
	} else
		$mpdf = new mPDF('c','A4',10,'arial',10,10,30,16,5,4); 
	$mpdf->AddPage('P');
	// $mpdf->setAutoTopMargin('stretch');
	$mpdf->SetDisplayMode('fullpage');
	$mpdf->WriteHTML($content);
	$filename = "KYC_Customer_".sanitize_filename($rsm['nama_customer']);
	$mpdf->Output($filename.'_'.date('dmyHis').'.pdf', 'I');
	exit;
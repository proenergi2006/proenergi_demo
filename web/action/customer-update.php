<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "htmlawed");

$con 	= new Connection();
$flash	= new FlashAlerts;
$enk  	= decode(BASE_REFERER);
$idr	= htmlspecialchars(paramDecrypt($_POST["idr"]), ENT_QUOTES);
$idk	= $con->getOne("select token_verification from pro_customer_verification where id_verification = '" . $enk['idk'] . "'");
if (($idr == $enk['idr'] && $enk['token'] == $idk)) {
	$agreement 			= htmlspecialchars($_POST["agreement"], ENT_QUOTES);
	$nama_customer 		= htmlspecialchars($_POST["nama_customer"], ENT_QUOTES);
	$print_product		= htmlspecialchars($_POST["print_product"], ENT_QUOTES);
	$alamat_customer	= htmlspecialchars($_POST["alamat_customer"], ENT_QUOTES);
	$prov_customer		= htmlspecialchars($_POST["prov_customer"], ENT_QUOTES);
	$kab_customer		= htmlspecialchars($_POST["kab_customer"], ENT_QUOTES);
	$telp_customer		= htmlspecialchars($_POST["telp_customer"], ENT_QUOTES);
	$fax_customer 		= htmlspecialchars($_POST["fax_customer"], ENT_QUOTES);
	$email_customer		= htmlspecialchars($_POST["email_customer"], ENT_QUOTES);
	$postalcode_customer = isset($_POST["postalcode_customer"]) ? htmlspecialchars($_POST["postalcode_customer"], ENT_QUOTES) : '';
	$website_customer	= htmlspecialchars($_POST["website_customer"], ENT_QUOTES);
	$tipe_bisnis		= htmlspecialchars($_POST["tipe_bisnis"], ENT_QUOTES);
	$tipe_bisnis 		= $tipe_bisnis ? $tipe_bisnis : 0;
	$tipe_bisnis_lain	= htmlspecialchars($_POST["tipe_bisnis_lain"], ENT_QUOTES);
	$ownership 			= htmlspecialchars($_POST["ownership"], ENT_QUOTES);
	$ownership 			= $ownership ? $ownership : 0;
	$ownership_lain		= htmlspecialchars($_POST["ownership_lain"], ENT_QUOTES);
	$update_by 			= htmlspecialchars($_POST["update_by"], ENT_QUOTES);
	$top_payment		= htmlspecialchars($_POST["top_payment"], ENT_QUOTES);
	$jenis_payment		= htmlspecialchars($_POST["jenis_payment"], ENT_QUOTES);
	$jenis_net			= isset($_POST["jenis_net"]) ? htmlspecialchars($_POST["jenis_net"], ENT_QUOTES) : 0;

	$induk_perusahaan 		= htmlspecialchars($_POST["induk_perusahaan"], ENT_QUOTES);
	$kecamatan_customer 	= htmlspecialchars($_POST["kecamatan_customer"], ENT_QUOTES);
	$kelurahan_customer 	= htmlspecialchars($_POST["kelurahan_customer"], ENT_QUOTES);
	$bank_name				= htmlspecialchars($_POST["bank_name"], ENT_QUOTES);
	$curency				= htmlspecialchars($_POST["curency"], ENT_QUOTES);
	$bank_address			= htmlspecialchars($_POST["bank_address"], ENT_QUOTES);
	$account_number			= htmlspecialchars($_POST["account_number"], ENT_QUOTES);
	$credit_facility		= htmlspecialchars($_POST["credit_facility"], ENT_QUOTES);
	$creditor				= ($credit_facility == 1 ? htmlspecialchars($_POST["creditor"], ENT_QUOTES) : '');

	if ($jenis_net == '') $jenis_net = 0;
	$top_payment		= ($jenis_payment == "CREDIT") ? $top_payment : "14";

	$nomor_sertifikat		= htmlspecialchars($_POST["nomor_sertifikat"], ENT_QUOTES);
	$nomor_npwp				= htmlspecialchars($_POST["nomor_npwp"], ENT_QUOTES);
	$nomor_siup				= htmlspecialchars($_POST["nomor_siup"], ENT_QUOTES);
	$nomor_tdp				= htmlspecialchars($_POST["nomor_tdp"], ENT_QUOTES);
	$dokumen_lainnya		= htmlspecialchars($_POST["dokumen_lainnya"], ENT_QUOTES);
	$credit_limit			= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["credit_limit"]), ENT_QUOTES);
	$credit_limit_diajukan	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["credit_limit_diajukan"]), ENT_QUOTES);
	if (!$credit_limit_diajukan)
		$credit_limit_diajukan = 0;

	$pic_decision_name 		= htmlspecialchars($_POST["pic_decision_name"], ENT_QUOTES);
	$pic_decision_position	= htmlspecialchars($_POST["pic_decision_position"], ENT_QUOTES);
	$pic_decision_telp		= htmlspecialchars($_POST["pic_decision_telp"], ENT_QUOTES);
	$pic_decision_mobile	= htmlspecialchars($_POST["pic_decision_mobile"], ENT_QUOTES);
	$pic_decision_email		= htmlspecialchars($_POST["pic_decision_email"], ENT_QUOTES);
	$pic_ordering_name 		= htmlspecialchars($_POST["pic_ordering_name"], ENT_QUOTES);
	$pic_ordering_position	= htmlspecialchars($_POST["pic_ordering_position"], ENT_QUOTES);
	$pic_ordering_telp		= htmlspecialchars($_POST["pic_ordering_telp"], ENT_QUOTES);
	$pic_ordering_mobile	= htmlspecialchars($_POST["pic_ordering_mobile"], ENT_QUOTES);
	$pic_ordering_email		= htmlspecialchars($_POST["pic_ordering_email"], ENT_QUOTES);
	$pic_billing_name 		= htmlspecialchars($_POST["pic_billing_name"], ENT_QUOTES);
	$pic_billing_position	= htmlspecialchars($_POST["pic_billing_position"], ENT_QUOTES);
	$pic_billing_telp		= htmlspecialchars($_POST["pic_billing_telp"], ENT_QUOTES);
	$pic_billing_mobile		= htmlspecialchars($_POST["pic_billing_mobile"], ENT_QUOTES);
	$pic_billing_email		= htmlspecialchars($_POST["pic_billing_email"], ENT_QUOTES);
	$pic_invoice_name 		= htmlspecialchars($_POST["pic_invoice_name"], ENT_QUOTES);
	$pic_invoice_position	= htmlspecialchars($_POST["pic_invoice_position"], ENT_QUOTES);
	$pic_invoice_telp		= htmlspecialchars($_POST["pic_invoice_telp"], ENT_QUOTES);
	$pic_invoice_mobile		= htmlspecialchars($_POST["pic_invoice_mobile"], ENT_QUOTES);
	$pic_invoice_email		= htmlspecialchars($_POST["pic_invoice_email"], ENT_QUOTES);

	$pic_fuelman_name 		= htmlspecialchars($_POST["pic_fuelman_name"], ENT_QUOTES);
	$pic_fuelman_position	= htmlspecialchars($_POST["pic_fuelman_position"], ENT_QUOTES);
	$pic_fuelman_telp		= htmlspecialchars($_POST["pic_fuelman_telp"], ENT_QUOTES);
	$pic_fuelman_mobile		= htmlspecialchars($_POST["pic_fuelman_mobile"], ENT_QUOTES);
	$pic_fuelman_email		= htmlspecialchars($_POST["pic_fuelman_email"], ENT_QUOTES);

	$invoice_delivery_addr_primary 		= htmlspecialchars($_POST["invoice_delivery_addr_primary"], ENT_QUOTES);
	$invoice_delivery_addr_secondary 	= htmlspecialchars($_POST["invoice_delivery_addr_secondary"], ENT_QUOTES);

	$product_delivery_address = [];
	for ($i = 0; $i < count($_POST["product_delivery_address"]); $i++) {
		$product_delivery_address['product_delivery_address'][] = str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["product_delivery_address"][$i], ENT_QUOTES));
	}

	$email_billing 			= htmlspecialchars($_POST["email_billing"], ENT_QUOTES);
	$alamat_billing			= htmlspecialchars($_POST["alamat_billing"], ENT_QUOTES);
	$prov_billing			= htmlspecialchars($_POST["prov_billing"], ENT_QUOTES);
	$kab_billing			= htmlspecialchars($_POST["kab_billing"], ENT_QUOTES);
	$postalcode_billing		= htmlspecialchars($_POST["postalcode_billing"], ENT_QUOTES);
	$telp_billing			= htmlspecialchars($_POST["telp_billing"], ENT_QUOTES);
	$fax_billing 			= htmlspecialchars($_POST["fax_billing"], ENT_QUOTES);
	$kecamatan_billing		= htmlspecialchars($_POST["kecamatan_billing"], ENT_QUOTES);
	$kelurahan_billing		= htmlspecialchars($_POST["kelurahan_billing"], ENT_QUOTES);
	$calculate_method		= htmlspecialchars($_POST["calculate_method"], ENT_QUOTES);

	$payment_schedule		= htmlspecialchars($_POST["payment_schedule"], ENT_QUOTES);
	$payment_schedule 		= $payment_schedule ? $payment_schedule : 0;
	$payment_schedule_other	= htmlspecialchars($_POST["payment_schedule_other"], ENT_QUOTES);

	$payment_method			= htmlspecialchars($_POST["payment_method"], ENT_QUOTES);
	$payment_method 		= $payment_method ? $payment_method : 0;
	$payment_method_other 	= htmlspecialchars($_POST["payment_method_other"], ENT_QUOTES);

	$invoice 				= isset($_POST["invoice"]) ? htmlspecialchars($_POST["invoice"], ENT_QUOTES) : 0;
	$ket_extra				= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["ket_extra"], ENT_QUOTES));

	$logistik_area 			= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["logistik_area"], ENT_QUOTES));
	$logistik_bisnis		= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["logistik_bisnis"], ENT_QUOTES));

	$logistik_env			= htmlspecialchars($_POST["logistik_env"], ENT_QUOTES);
	$logistik_env 			= $logistik_env ? $logistik_env : 0;
	$logistik_env_other		= htmlspecialchars($_POST["logistik_env_other"], ENT_QUOTES);

	$logistik_storage 		= htmlspecialchars($_POST["logistik_storage"], ENT_QUOTES);
	$logistik_storage 		= $logistik_storage ? $logistik_storage : 0;
	$logistik_storage_other = htmlspecialchars($_POST["logistik_storage_other"], ENT_QUOTES);

	$logistik_hour			= htmlspecialchars($_POST["logistik_hour"], ENT_QUOTES);
	$logistik_hour 			= $logistik_hour ? $logistik_hour : 0;
	$logistik_hour_other	= htmlspecialchars($_POST["logistik_hour_other"], ENT_QUOTES);

	$logistik_volume 		= htmlspecialchars($_POST["logistik_volume"], ENT_QUOTES);
	$logistik_volume 		= $logistik_volume ? $logistik_volume : 0;
	$logistik_volume_other 	= htmlspecialchars($_POST["logistik_volume_other"], ENT_QUOTES);

	$logistik_quality 		= htmlspecialchars($_POST["logistik_quality"], ENT_QUOTES);
	$logistik_quality 		= $logistik_quality ? $logistik_quality : 0;
	$logistik_quality_other	= htmlspecialchars($_POST["logistik_quality_other"], ENT_QUOTES);

	$logistik_truck			= htmlspecialchars($_POST["logistik_truck"], ENT_QUOTES);
	$logistik_truck 		= $logistik_truck ? $logistik_truck : 0;
	$logistik_truck_other	= htmlspecialchars($_POST["logistik_truck_other"], ENT_QUOTES);

	$desc_condition			= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["desc_condition"], ENT_QUOTES));
	$desc_stor_fac			= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["desc_stor_fac"], ENT_QUOTES));

	$supply_shceme			= htmlspecialchars($_POST["supply_shceme"], ENT_QUOTES);
	$specify_product		= htmlspecialchars($_POST["specify_product"], ENT_QUOTES);
	$volume_per_month		= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["volume_per_month"]), ENT_QUOTES);
	$operational_hour_from 	= htmlspecialchars($_POST["operational_hour_from"], ENT_QUOTES);
	$operational_hour_to 	= htmlspecialchars($_POST["operational_hour_to"], ENT_QUOTES);
	$nico					= htmlspecialchars($_POST["nico"], ENT_QUOTES);
	$volume_per_month		= ($volume_per_month ? $volume_per_month : 0);


	$sql = "select count_update, need_update, finance_result from pro_customer a join pro_customer_verification b on b.id_customer = a.id_customer where a.id_customer = '" . $idr . "'";
	$res = $con->getRecord($sql);

	$count_update = $res['count_update'] < 2 ? $res['count_update'] + 1 : 2;
	$need_update  = $res['count_update'] == 1 ? 1 : 0;

	if ($nama_customer == "" || $alamat_customer == "" || $prov_customer == "" || $kab_customer == "" || $telp_customer == "" || $email_customer == "" || $update_by == "") {
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else if ($top_payment && !is_numeric($top_payment)) {
		$con->close();
		$flash->add("error", "Jangka waktu pembayaran diisi dengan angka", BASE_REFERER);
	} else if ($email_customer != "" && !filter_var($email_customer, FILTER_VALIDATE_EMAIL)) {
		$con->close();
		$flash->add("error", "Alamat email tidak benar", BASE_REFERER);
	} else if ($tipe_bisnis == "10" && $tipe_bisnis_lain == "") {
		$con->close();
		$flash->add("error", "Tipe Bisnis lain belum disebutkan", BASE_REFERER);
	} else if ($ownership == "8" && $ownership_lain == "") {
		$con->close();
		$flash->add("error", "Kepemilikan lain belum disebutkan", BASE_REFERER);
	} else if ($nomor_npwp == "" || $nomor_siup == "" || $nomor_tdp == "") {
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else if ($pic_decision_name == "" || $pic_decision_position == "" || $pic_decision_mobile == "" || $pic_decision_telp == "") {
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else if ($pic_decision_email != "" && !filter_var($pic_decision_email, FILTER_VALIDATE_EMAIL)) {
		$con->close();
		$flash->add("error", "Alamat email tidak benar", BASE_REFERER);
	} else if ($pic_ordering_name == "" || $pic_ordering_position == "" || $pic_ordering_mobile == "" || $pic_ordering_telp == "") {
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else if ($pic_ordering_email != "" && !filter_var($pic_ordering_email, FILTER_VALIDATE_EMAIL)) {
		$con->close();
		$flash->add("error", "Alamat email tidak benar", BASE_REFERER);
	} else if ($pic_billing_name == "" || $pic_billing_position == "" || $pic_billing_mobile == "" || $pic_billing_telp == "") {
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else if ($pic_billing_email != "" && !filter_var($pic_billing_email, FILTER_VALIDATE_EMAIL)) {
		$con->close();
		$flash->add("error", "Alamat email tidak benar", BASE_REFERER);
	} else if ($pic_invoice_name == "" || $pic_invoice_position == "" || $pic_invoice_mobile == "" || $pic_invoice_telp == "") {
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else if ($pic_invoice_email != "" && !filter_var($pic_invoice_email, FILTER_VALIDATE_EMAIL)) {
		$con->close();
		$flash->add("error", "Alamat email tidak benar", BASE_REFERER);
	} else if ($email_billing == "" || $alamat_billing == "" || $prov_billing == "" || $kab_billing == "" || $telp_billing == "") {
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else if ($email_billing != "" && !filter_var($email_billing, FILTER_VALIDATE_EMAIL)) {
		$con->close();
		$flash->add("error", "Alamat email tidak benar", BASE_REFERER);
	} else if ($payment_schedule == "2" && $payment_schedule_other == "") {
		$con->close();
		$flash->add("error", "Jadwal pembayaran lain belum disebutkan", BASE_REFERER);
	} else if ($payment_method == "5" && $payment_method_other == "") {
		$con->close();
		$flash->add("error", "Cara pembayaran lain belum disebutkan", BASE_REFERER);
	} else if ($agreement != "1") {
		$con->close();
		$flash->add("error", "Anda belum menyatakan setuju untuk mengirim data dibawah ini", BASE_REFERER);
	} else {
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		$sql1 = "update pro_customer set nama_customer = '" . $nama_customer . "', print_product = '" . $print_product . "', alamat_customer = '" . $alamat_customer . "', prov_customer = '" . $prov_customer . "', kab_customer = '" . $kab_customer . "', postalcode_customer = '" . $postalcode_customer . "', telp_customer = '" . $telp_customer . "', fax_customer = '" . $fax_customer . "', email_customer = '" . $email_customer . "', website_customer = '" . $website_customer . "', tipe_bisnis = '" . $tipe_bisnis . "', tipe_bisnis_lain = '" . $tipe_bisnis_lain . "', ownership = '" . $ownership . "', ownership_lain = '" . $ownership_lain . "', jenis_payment = '" . $jenis_payment . "', jenis_net = '" . $jenis_net . "', top_payment = '" . $top_payment . "', nomor_sertifikat = '" . $nomor_sertifikat . "', nomor_npwp = '" . $nomor_npwp . "', nomor_siup = '" . $nomor_siup . "', nomor_tdp = '" . $nomor_tdp . "',  dokumen_lainnya = '" . $dokumen_lainnya . "', credit_limit_diajukan = '" . $credit_limit_diajukan . "', lastupdate_time = NOW(), lastupdate_ip = '" . $_SERVER['REMOTE_ADDR'] . "', lastupdate_by = '" . $update_by . "', need_update = '" . $need_update . "', count_update = '" . $count_update . "', induk_perusahaan = '" . $induk_perusahaan . "', kecamatan_customer = '" . $kecamatan_customer . "', kelurahan_customer = '" . $kelurahan_customer . "' where id_customer = '" . $idr . "'";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();

		$sql2 = "update pro_customer_contact set pic_decision_name = '" . $pic_decision_name . "', pic_decision_position = '" . $pic_decision_position . "', pic_decision_telp = '" . $pic_decision_telp . "', pic_decision_mobile = '" . $pic_decision_mobile . "', pic_decision_email = '" . $pic_decision_email . "', pic_ordering_name = '" . $pic_ordering_name . "', pic_ordering_position = '" . $pic_ordering_position . "', pic_ordering_telp = '" . $pic_ordering_telp . "', pic_ordering_mobile = '" . $pic_ordering_mobile . "', pic_ordering_email = '" . $pic_ordering_email . "', pic_billing_name = '" . $pic_billing_name . "', pic_billing_position = '" . $pic_billing_position . "', pic_billing_telp = '" . $pic_billing_telp . "', pic_billing_mobile = '" . $pic_billing_mobile . "', pic_billing_email = '" . $pic_billing_email . "', pic_invoice_name = '" . $pic_invoice_name . "', pic_invoice_position = '" . $pic_invoice_position . "', pic_invoice_telp = '" . $pic_invoice_telp . "', pic_invoice_mobile = '" . $pic_invoice_mobile . "', pic_invoice_email = '" . $pic_invoice_email . "', pic_fuelman_name = '" . $pic_fuelman_name . "', pic_fuelman_position = '" . $pic_fuelman_position . "', pic_fuelman_telp = '" . $pic_fuelman_telp . "', pic_fuelman_mobile = '" . $pic_fuelman_mobile . "', pic_fuelman_email = '" . $pic_fuelman_email . "', invoice_delivery_addr_primary = '" . $invoice_delivery_addr_primary . "', invoice_delivery_addr_secondary = '" . $invoice_delivery_addr_secondary . "', product_delivery_address = '" . json_encode($product_delivery_address, TRUE) . "' where id_customer = '" . $idr . "'";
		$con->setQuery($sql2);
		$oke  = $oke && !$con->hasError();

		$sql3 = "update pro_customer_payment set email_billing = '" . $email_billing . "', alamat_billing = '" . $alamat_billing . "', prov_billing = '" . $prov_billing . "', kab_billing = '" . $kab_billing . "', postalcode_billing = '" . $postalcode_billing . "', telp_billing = '" . $telp_billing . "', fax_billing = '" . $fax_billing . "', payment_schedule = '" . $payment_schedule . "', payment_schedule_other = '" . $payment_schedule_other . "', payment_method = '" . $payment_method . "', payment_method_other = '" . $payment_method_other . "', invoice = '" . $invoice . "', ket_extra = '" . $ket_extra . "', kecamatan_billing = '" . $kecamatan_billing . "', kelurahan_billing = '" . $kelurahan_billing . "', calculate_method = '" . $calculate_method . "', bank_name = '" . $bank_name . "', curency = '" . $curency . "', bank_address = '" . $bank_address . "', account_number = '" . $account_number . "', credit_facility = '" . $credit_facility . "', creditor = '" . $creditor . "' where id_customer = '" . $idr . "'";
		$con->setQuery($sql3);
		$oke  = $oke && !$con->hasError();

		$sql4 = "update pro_customer_logistik set logistik_area = '" . $logistik_area . "', logistik_bisnis = '" . $logistik_bisnis . "', logistik_env = '" . $logistik_env . "', logistik_env_other = '" . $logistik_env_other . "', logistik_storage = '" . $logistik_storage . "', logistik_storage_other = '" . $logistik_storage_other . "', logistik_hour = '" . $logistik_hour . "', logistik_hour_other = '" . $logistik_hour_other . "', logistik_volume = '" . $logistik_volume . "', logistik_volume_other = '" . $logistik_volume_other . "', logistik_quality = '" . $logistik_quality . "', logistik_quality_other = '" . $logistik_quality_other . "', logistik_truck = '" . $logistik_truck . "', logistik_truck_other = '" . $logistik_truck_other . "', desc_condition = '" . $desc_condition . "', desc_stor_fac = '" . $desc_stor_fac . "', supply_shceme = '" . $supply_shceme . "', specify_product = '" . $specify_product . "', volume_per_month = '" . $volume_per_month . "',  nico = '" . $nico . "', operational_hour_from = '" . $operational_hour_from . "', operational_hour_to = '" . $operational_hour_to . "' where id_customer = '" . $idr . "'";
		//operational_hour = '".$operational_hour."',
		$con->setQuery($sql4);
		$oke  = $oke && !$con->hasError();

		$sql5 = "update pro_customer_verification set is_evaluated = 1 where id_verification = '" . $enk['idk'] . "'";
		$con->setQuery($sql5);
		$oke  = $oke && !$con->hasError();

		if ($oke) {
			$con->commit();
			$con->close();
			unset($_SESSION['post'][$idr]);
			$linkCus = BASE_URL . '/customer/update-customer.php?' . paramEncrypt('idr=' . $enk['idr'] . '&idk=' . $enk['idk'] . '&token=' . $enk['token']);
			$flash->add("success", "Data telah disimpan", $linkCus);
		} else {
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
	}
} else {
	$con->close();
	$flash->add("error", "Maaf data tidak ditemukan...", BASE_REFERER);
}

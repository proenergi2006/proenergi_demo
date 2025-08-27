<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "pdfgen");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;
$enk  	= decode($_SERVER['REQUEST_URI']);
$idr 	= isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
$idk 	= htmlspecialchars($enk["idk"], ENT_QUOTES);
$bhs 	= htmlspecialchars($enk["bhs"], ENT_QUOTES);

$sql = "
		select
			a.*,
			b.nama_customer,
			b.alamat_customer,
			b.telp_customer,
			b.fax_customer,
			c.fullname,
			c.mobile_user,
			c.email_user,
			d.nama_cabang,
			e.jenis_produk,
			e.merk_dagang,
			f.nama_prov,
			g.nama_kab,
			h.fullname as picname,
			i.role_name,
			d.kode_barcode
		from
			pro_penawaran a
		join pro_customer b on
			a.id_customer = b.id_customer
		join acl_user c on
			b.id_marketing = c.id_user
		join pro_master_cabang d on
			a.id_cabang = d.id_master
		join pro_master_produk e on
			a.produk_tawar = e.id_master
		join pro_master_provinsi f on
			b.prov_customer = f.id_prov
		join pro_master_kabupaten g on
			b.kab_customer = g.id_kab
		left join acl_user h on
			a.pic_approval = h.id_user
		left join acl_role i on
			h.id_role = i.id_role
		where
			a.id_customer = '" . $idr . "' 
			and a.id_penawaran = '" . $idk . "'";
//echo $sql; exit;

$rsm = $con->getRecord($sql);
$jabat 	= str_replace("Role ", "", $rsm['role_name']);
$printe = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]) . " " . date("d/m/Y H:i:s") . " WIB";
$barcod = $rsm['kode_barcode'] . '01' . str_pad($rsm['id_penawaran'], 6, '0', STR_PAD_LEFT);
$arrTgl = array(1 => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
$alamat = $rsm['alamat_customer'] . " " . str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $rsm['nama_kab']) . " " . $rsm['nama_prov'];
$pembulatan = $rsm['pembulatan'];

$arrKondInd	= array(0 => '', 1 => "Setelah Invoice diterima", "Setelah pengiriman", "Setelah Loading");
$arrKondEng = array(0 => '', 1 => "After Invoice Receive", "After Delivery", "After Loading");
$jenis_net	= $rsm['jenis_net'];

if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == '11') {
	$nama_role 	= "Marketing";
	if ($rsm['tier'] == 'I') {
		$wil_jbt = "Branch Manager";
		$wil_pic = $rsm['sm_wil_pic'];
	} else if ($rsm['tier'] == 'II') {
		$wil_jbt = "Operation Manager";
		$wil_pic = $rsm['om_pic'];
	} else if ($rsm['tier'] == 'III') {
		$wil_jbt = "Operation Manager";
		$wil_pic = 'Sony Hartono';
	} else {
		$wil_jbt = "Branch Manager"; // Default jika tier tidak sesuai
		$wil_pic = "";
	}
} else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == '17') {
	$nama_role = "Key Account Executive";

	if ($rsm['tier'] == 'I') {
		$wil_jbt = "Branch Manager";
		$wil_pic = $rsm['sm_wil_pic'];
	} else if ($rsm['tier'] == 'II') {
		$wil_jbt = "Operation Manager";
		$wil_pic = $rsm['om_pic'];
	} else if ($rsm['tier'] == 'III') {
		$wil_jbt = "Operation Manager";
		$wil_pic = 'Sony Hartono';
	} else {
		$wil_jbt = "Branch Manager"; // Default jika tier tidak sesuai
		$wil_pic = "";
	}
} else {
	$nama_role = "";
	$wil_pic = "";
	$wil_jbt = "Branch Manager";
}

$rincian = json_decode($rsm['detail_rincian'], true);
if ($rsm['perhitungan'] == 1) {
	$breakdown = false;
	foreach ($rincian as $temp) {
		$breakdown = $breakdown || $temp["rinci"];
	}
}
if ($rsm['perhitungan'] == 1 && !$breakdown) {
	$mpdfOpt1 	= array(
		'mode' => 'c',
		'format' => 'A4',
		'default_font_size' => '10',
		'default_font' => 'arial',
		'margin_left' => '15',
		'margin_right' => '15',
		'margin_top' => '30',
		'margin_bottom' => '30',
		'margin_header' => '5',
		'margin_footer' => '5'
	);
} else {
	$mpdfOpt1 	= array(
		'mode' => 'c',
		'format' => 'A4',
		'default_font_size' => '10',
		'default_font' => 'arial',
		'margin_left' => '15',
		'margin_right' => '15',
		'margin_top' => '30',
		'margin_bottom' => '30',
		'margin_header' => '5',
		'margin_footer' => '5'
	);
}

if ($rsm['flag_approval'] == 1) {

	ob_start();
	if ($bhs == 'ind') {
		$arrPayment = array("CREDIT" => "CREDIT " . $rsm['jangka_waktu'] . " Hari " . $arrKondInd[$jenis_net], "CBD" => "CBD (Cash Before Delivery)", "COD" => "COD (Cash On Delivery)");
		require_once(realpath("./template/surat-penawaran.php"));
	} else {
		$arrPayment = array("CREDIT" => "CREDIT " . $rsm['jangka_waktu'] . " days " . $arrKondEng[$jenis_net], "CBD" => "CBD (Cash Before Delivery)", "COD" => "COD (Cash On Delivery)");
		require_once(realpath("./template/surat-penawaran-eng.php"));
	}

	$content = ob_get_clean();
	ob_end_flush();
	$con->close();

	$mpdf = null;
	if (PHP_VERSION >= 5.6) {
		$mpdf = new \Mpdf\Mpdf($mpdfOpt1);
	} else {
		$mpdf = new mPDF('c', 'A4', 10, 'arial', 10, 10, 30, 15, 5, 5);
	}
	$mpdf->AddPage('P');
	$mpdf->SetDisplayMode('fullpage');
	// $mpdf->SetWatermarkImage(BASE_IMAGE."/watermark-penawaran.png", 0.2, "P", array(0,0));
	$mpdf->showWatermarkImage = true;
	$mpdf->WriteHTML($content);
	if ($rsm['term_condition'] != '' or $rsm['term_condition'] != null) {
		$mpdf->AddPage('P');
		$mpdf->WriteHTML('<div style="margin-left:0px; padding-top:80px;">
						    <table width="100%" border="0" cellpadding="0" cellspacing="0">
						        <tr>
						            <td width="70%"><span style="font-size:9pt;">Syarat Dan Ketentuan :</span></td>
						            <td width="30%"><span style="font-size:9pt;"></span></td>
						        </tr>
						    </table><br><p style="font-size:9pt;">' . $rsm['term_condition'] . '</p>');
	}

	$filename = "Surat_Penawaran_" . sanitize_filename($rsm['nama_customer']);
	$mpdf->Output($filename . '_' . date('dmyHis') . '.pdf', 'I');
	exit;
} else {
	$flash->add("warning", "Penawaran belum dapat dicetak", BASE_REFERER);
	$con->close();
}

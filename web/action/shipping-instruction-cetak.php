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
$tipe 	= isset($enk["tipe"]) ? htmlspecialchars($enk["tipe"], ENT_QUOTES) : '';

$sql = "SELECT a.*,a.created_by as sr_created,a.created_at as sr_createdat,c.*,concat(nama_terminal,' - ',tanki_terminal,' - ',lokasi_terminal) as nama_terminal,b.nomor_po, e.nama_vendor, f.nama_suplier,
		f.alamat_suplier, f.att_suplier
		FROM new_pro_inventory_vendor_po_ship_req a 
		JOIN new_pro_inventory_vendor_po b ON a.id_vendor_po = b.id_master
		JOIN pro_master_oa_kapal c ON a.id_vessel = c.id_master
		JOIN pro_master_terminal d ON a.id_terminal_discharging=d.id_master
		JOIN pro_master_vendor e ON b.id_vendor=e.id_master
		JOIN pro_master_transportir f ON c.id_transportir = f.id_master
		WHERE a.id_master =  '" . $idr . "'";
$res = $con->getResult($sql);
$printe = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]) . " " . date("d/m/Y H:i:s") . " WIB";

$barcod = "http://localhost/proenergi_demo/" . paramEncrypt($idr);
$loading_date='';
if(date("m",strtotime($res[0]['etl_date_first']) == date("m",strtotime($res[0]['etl_date_last'])))){
    if(date("d",strtotime($res[0]['etl_date_first'])) == date("d",strtotime($res[0]['etl_date_last']))){
        $loading_date=tgl_indo($res[0]['etl_date_first']);
    }else{
        $loading_date=date("d",strtotime($res[0]['etl_date_first']))."-".tgl_indo(($res[0]['etl_date_last']));
    }
}else{
    $loading_date=($res[0]['etl_date_first'])."-".tgl_indo($res[0]['etl_date_last']);
}

ob_start();

if($tipe == 'shipping_instruction'){
	require_once(realpath("./template/shipping-instruction.php"));
}elseif($tipe == 'LO'){
	require_once(realpath("./template/delivery-order-ship.php"));
}else{
	require_once(realpath("./template/spal-ship.php"));
}
$content = ob_get_clean();
ob_end_flush();
$con->close();

$mpdf = null;
if (PHP_VERSION >= 5.6) {
	$mpdf = new \Mpdf\Mpdf(['format' => 'A4', 'default_font' => 'Arial']);
} else
	$mpdf = new mPDF('c', 'A4', 10, 'arial', 8, 8, 33, 25, 5, 5);

	$mpdf->SetDisplayMode('fullpage');
if($tipe == 'LO'){
	$mpdf->AddPage('P', '', '', '', '', 5, 5, 5, 5, 5, 5);
	$mpdf->use_kwt = true;
	$mpdf->autoPageBreak = false;
	$mpdf->setAutoTopMargin = 'stretch';
	$mpdf->setAutoBottomMargin = 'stretch';
	$mpdf->shrink_tables_to_fit = 1;
}
$mpdf->WriteHTML($content);
// $filename = "Shipping_Request_" . sanitize_filename($idr);
$filename = "Shipping_Instruction";
if($tipe == 'LO'){
	$mpdf->Output('LO_' . date('dmyHis') . '.pdf', 'I');
}else{
	$mpdf->Output($filename . '_' . date('dmyHis') . '.pdf', 'I');
}
exit;

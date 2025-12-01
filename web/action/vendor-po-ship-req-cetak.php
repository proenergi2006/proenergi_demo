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
$idsr 	= isset($enk["idsr"]) ? htmlspecialchars($enk["idsr"], ENT_QUOTES) : '';

$sql = "select a.*, sr.*, a1.id_po_supplier, b.jenis_produk, b.merk_dagang, d.nama_vendor, e.nama_terminal, e.tanki_terminal, e.lokasi_terminal 
			from new_pro_inventory_vendor_po a 
			join pro_master_produk b on a.id_produk = b.id_master 
			join pro_master_vendor d on a.id_vendor = d.id_master 
			join pro_master_terminal e on a.id_terminal = e.id_master 
			left join new_pro_inventory_vendor_po_receive a1 on a.id_master = a1.id_po_supplier
            left JOIN new_pro_inventory_vendor_po_ship_req sr ON a.id_master = sr.id_vendor_po
			where a.id_master = '" . $idr . "'";
$res = $con->getResult($sql);
$printe = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]) . " " . date("d/m/Y H:i:s") . " WIB";

$barcod = "http://barcode.proenergi.com/customer/barcode/po/" . paramEncrypt($idsr);
$loading_date='';
if(date("m",strtotime($res[0]['etl_date_first']) == date("m",strtotime($res[0]['etl_date_last'])))){
    $loading_date=date("d",strtotime($res[0]['etl_date_first']))."-".date("d F Y",strtotime($res[0]['etl_date_last']));
}else{
    $loading_date=date("d F Y",strtotime($res[0]['etl_date_first']))."-".date("d F Y",strtotime($res[0]['etl_date_last']));
}

ob_start();
require_once(realpath("./template/shipping-request.php"));
$content = ob_get_clean();
ob_end_flush();
$con->close();

$mpdf = null;
if (PHP_VERSION >= 5.6) {
	$mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
} else
	$mpdf = new mPDF('c', 'A4', 10, 'arial', 8, 8, 33, 25, 5, 5);
$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($content);
// $filename = "Shipping_Request_" . sanitize_filename($idr);
$filename = "Shipping_Request";
$mpdf->Output($filename . '_' . date('dmyHis') . '.pdf', 'I');
exit;

<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "pdfgen");

$auth    = new MyOtentikasi();
$con     = new Connection();
$flash    = new FlashAlerts;
$enk      = decode($_SERVER['REQUEST_URI']);
$idr = isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';

$sql = "select a.*, a1.id_po_supplier, b.jenis_produk, b.merk_dagang, d.nama_vendor, e.nama_terminal, e.tanki_terminal, e.lokasi_terminal 
from new_pro_inventory_vendor_po a 
join pro_master_produk b on a.id_produk = b.id_master 
join pro_master_vendor d on a.id_vendor = d.id_master 
join pro_master_terminal e on a.id_terminal = e.id_master 
left join new_pro_inventory_vendor_po_receive a1 on a.id_master = a1.id_po_supplier 
where a.id_master = '" . $idr . "'";

$data = $con->getRecord($sql);
$printe = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]) . " " . date("d/m/Y H:i:s") . " WIB";
$barcod =   '00' . str_pad($idr, 6, '0', STR_PAD_LEFT);

ob_start();
require_once(realpath("./action/template/po-supplier-vendor-syop.php"));
$content = ob_get_clean();
ob_end_flush();
$con->close();

// $mpdf = new mPDF('c','A4',10,'arial',10,10,20,10,0,5); 
// $mpdf->SetDisplayMode('fullpage');
// $mpdf->WriteHTML($content);
// $filename = "DO_TRUCK_";
// $mpdf->Output($filename.'_'.date('dmyHis').'.pdf', 'D');
// exit;

$mpdf = null;
if (PHP_VERSION >= 5.6) {
    $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
} else
    $mpdf = new mPDF('c', 'A4', 10, 'arial', 10, 10, 20, 10, 0, 5);
$mpdf->AddPage('P');
$mpdf->SetDisplayMode('fullpage');
// $mpdf->SetWatermarkImage(BASE_IMAGE."/watermark-penawaran.png", 0.2, "P", array(0,0));
// $mpdf->showWatermarkImage = true;
$mpdf->WriteHTML($content);
$filename = "PO_SUPP_";
$mpdf->Output($filename . '_' . date('dmyHis') . '.pdf', 'I');
exit;

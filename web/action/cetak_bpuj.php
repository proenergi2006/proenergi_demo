<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "pdfgen");

$auth    = new MyOtentikasi();
$con     = new Connection();
$flash   = new FlashAlerts;
$enk     = decode($_SERVER['REQUEST_URI']);
$idr     = isset($enk["id_bpuj"]) ? htmlspecialchars($enk["id_bpuj"], ENT_QUOTES) : '';
$sql     = "SELECT a.*, b.nomor_do FROM pro_bpuj a JOIN pro_po_ds_detail b ON a.id_dsd=b.id_dsd WHERE a.id_bpuj='" . $idr . "'";
$res     = $con->getRecord($sql);
$printe  = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]) . " " . date("d/m/Y H:i:s") . " WIB";
ob_start();
require_once(realpath("./template/cetak_bpuj.php"));
$content = ob_get_clean();
ob_end_flush();
$con->close();

$mpdf = null;
$mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($content);
$filename = "BPUJ-" . $res['nomor_do'];
$mpdf->Output($filename . '.pdf', 'I');
exit;

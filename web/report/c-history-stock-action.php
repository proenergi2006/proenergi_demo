<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "pdfgen", "mailgen");

$auth    = new MyOtentikasi();
$con     = new Connection();
$flash    = new FlashAlerts;
$enk      = decode($_SERVER['REQUEST_URI']);

$tgl     = isset($_POST["tgl"]) ? htmlspecialchars($_POST["tgl"], ENT_QUOTES) : null;
$id      = isset($_POST["id"]) ? htmlspecialchars($_POST["id"], ENT_QUOTES) : null;

$oke = true;
$sql1 = "update new_pro_inventory_depot SET tanggal_inven = '" . tgl_db($tgl) . "' WHERE id_master = '" . $id . "'";
$con->setQuery($sql1);
$oke  = $oke && !$con->hasError();

// Cek apakah query berhasil
if ($oke) {
    // Kirim response success dalam format JSON
    echo json_encode([
        "success" => true,
        "message" => "Data berhasil diedit!"
    ]);
} else {
    // Kirim response error dalam format JSON
    echo json_encode([
        "success" => false,
        "message" => "Terjadi kesalahan saat mengedit data!"
    ]);
}
exit();

<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "pdfgen");

$enk    = decode($_SERVER['REQUEST_URI']);
$con    = new Connection();
$fullname = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]);
$datenow = date("Y-m-d H:i:s");
$id = paramDecrypt(isset($_POST["id"]) ? htmlspecialchars($_POST["id"], ENT_QUOTES) : NULL);
$jenis = paramDecrypt(isset($_POST["jenis"]) ? htmlspecialchars($_POST["jenis"], ENT_QUOTES) : NULL);
$keterangan  = isset($_POST["keterangan"]) ? htmlspecialchars($_POST["keterangan"], ENT_QUOTES) : NULL;

$oke = true;
$con->beginTransaction();
$con->clearError();
// echo json_encode($keterangan);
if ($jenis == "open") {
    $query = "UPDATE pro_button_control SET status='0', keterangan='".$keterangan."', updated_by='".$fullname."', updated_at='".$datenow."' WHERE id='" . $id . "'";
    $con->setQuery($query);
    $oke  = $oke && !$con->hasError();

    if ($oke) {
        $con->commit();
        $con->close();
        $data = [
            "status" => true,
            "pesan"  => "Button Berhasil di buka",
        ];
        echo json_encode($data);
    } else {
        $con->rollBack();
        $con->clearError();
        $con->close();
        $data = [
            "status" => false,
            "pesan"  => "Button Gagal di buka",
        ];
        echo json_encode($data);
    }
} elseif ($jenis == "close") {
    $query = "UPDATE pro_button_control SET status='1', keterangan='".$keterangan."', updated_by='".$fullname."', updated_at='".$datenow."' WHERE id='" . $id . "'";
    $con->setQuery($query);
    $oke  = $oke && !$con->hasError();

    if ($oke) {
        $con->commit();
        $con->close();
        $data = [
            "status" => true,
            "pesan"  => "Button Berhasil di tutup",
        ];
        echo json_encode($data);
    } else {
        $con->rollBack();
        $con->clearError();
        $con->close();
        $data = [
            "status" => false,
            "pesan"  => "Button Gagal di tutup",
        ];
        echo json_encode($data);
    }
}
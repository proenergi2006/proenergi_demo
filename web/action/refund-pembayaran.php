<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "pdfgen");

$enk    = decode($_SERVER['REQUEST_URI']);
$con    = new Connection();
$printe = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]) . " " . date("d/m/Y H:i:s") . " WIB";
$role   = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$wilayah = paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]);
$fullname = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]);
$id_refund = paramDecrypt(isset($_POST["id_refund"]) ? htmlspecialchars($_POST["id_refund"], ENT_QUOTES) : NULL);
$id_poc = paramDecrypt(isset($_POST["id_poc"]) ? htmlspecialchars($_POST["id_poc"], ENT_QUOTES) : NULL);
$persen = paramDecrypt(isset($_POST["persen"]) ? htmlspecialchars($_POST["persen"], ENT_QUOTES) : NULL);
$bayar_refund = isset($_POST["bayar_refund"]) ? htmlspecialchars($_POST["bayar_refund"], ENT_QUOTES) : NULL;
$jenis = $_POST['jenis'];

$oke = true;
$con->beginTransaction();
$con->clearError();

// echo json_encode("asdasdasd");
if ($jenis == 'pembayaran') {
    // echo json_encode($bayar_refund);
    $query = '
    UPDATE pro_refund
    SET 
        `total_refund`  = "' . $bayar_refund . '",
        `tgl_bayar`  = "' . date("Y-m-d") . '",
        `paid_by`  = "' . $fullname . '",
        `disposisi`  = 2,
        `updated_at`  = "' . date("Y-m-d H:i:s") . '"
    WHERE 
        `id_refund` = "' . $id_refund . '"';
    $con->setQuery($query);
    $oke  = $oke && !$con->hasError();

    $sql_penerima_refund = "SELECT * FROM pro_poc_penerima_refund WHERE id_poc = '" . $id_poc . "'";
    $penerima_refund = $con->getResult($sql_penerima_refund);

    foreach ($penerima_refund as $key) {
        $terima_refund = ($key['persentasi_refund'] * $persen) / 100;

        $query2 = '
        UPDATE pro_poc_penerima_refund
        SET 
            `terima_refund`  = "' . $terima_refund . '"
        WHERE 
        `id` = "' . $key['id'] . '"';
        $con->setQuery($query2);
        $oke  = $oke && !$con->hasError();
    }

    // echo json_encode($penerima_refund);
    if ($oke) {
        $con->commit();
        $con->close();
        $data = [
            "status" => true,
            "pesan"  => "Refund berhasil di Bayar",
        ];
        echo json_encode($data);
    } else {
        $con->rollBack();
        $con->clearError();
        $con->close();
        $data = [
            "status" => false,
            "pesan"  => "Refund gagal di Bayar",
        ];
        echo json_encode($data);
    }
} else {
    $query = '
    UPDATE pro_refund
    SET 
        `closed_by`  = "' . $fullname . '",
        `closed_date`  = "' . date("Y-m-d") . '",
        `disposisi`  = 3,
        `updated_at`  = "' . date("Y-m-d H:i:s") . '"
    WHERE 
        `id_refund` = "' . $id_refund . '"';
    $con->setQuery($query);
    $oke  = $oke && !$con->hasError();

    if ($oke) {
        $con->commit();
        $con->close();
        $data = [
            "status" => true,
            "pesan"  => "Refund berhasil di Close",
        ];
        echo json_encode($data);
    } else {
        $con->rollBack();
        $con->clearError();
        $con->close();
        $data = [
            "status" => false,
            "pesan"  => "Refund gagal di Close",
        ];
        echo json_encode($data);
    }
}

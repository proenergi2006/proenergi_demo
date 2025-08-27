<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth    = new MyOtentikasi();
$con     = new Connection();
$flash    = new FlashAlerts;
$enk      = decode($_SERVER['REQUEST_URI']);
$act    = !isset($enk['act']) ? htmlspecialchars($_POST["act"], ENT_QUOTES) : $enk['act'];

$akun    = htmlspecialchars($_POST["akun"], ENT_QUOTES);
$fullname = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]);

$oke = true;
$con->beginTransaction();
$con->clearError();

if ($act == "add") {
    $sql_cek = "SELECT * FROM pro_non_penerima_incentive WHERE id_user = '" . $akun . "'";
    $res_cek = $con->getRecord($sql_cek);

    if ($res_cek) {
        $con->rollBack();
        $con->clearError();
        $con->close();
        $flash->add("error", "Data Akun tersebut sudah ada dalam list", BASE_REFERER);
    }

    $sql = "INSERT into pro_non_penerima_incentive(id_user, created_by, created_at) values ('" . $akun . "', '" . $fullname . "', NOW())";
    $msg = "GAGAL_MASUK";
    $con->setQuery($sql);
    $oke  = $oke && !$con->hasError();

    if ($oke) {
        $con->commit();
        $con->close();
        header("location: " . BASE_URL_CLIENT . "/non_penerima_incentive.php");
        exit();
    } else {
        $con->rollBack();
        $con->clearError();
        $con->close();
        $flash->add("error", $msg, BASE_REFERER);
    }
} else {
    $param     = htmlspecialchars(paramDecrypt($_POST["param"]), ENT_QUOTES);
    $post     = explode("#|#", $param);
    $id        = htmlspecialchars($post[1], ENT_QUOTES);

    $sql2 = "DELETE from pro_non_penerima_incentive where id = '" . $id . "'";
    $con->setQuery($sql2);
    $oke  = $oke && !$con->hasError();

    if ($oke) {
        $con->commit();
        $con->close();
        $result = [
            "status"     => true,
            "pesan"     => "Berhasil di hapus",
        ];
    } else {
        $con->rollBack();
        $con->clearError();
        $con->close();
        $result = [
            "status"     => false,
            "pesan"     => "Gagal di hapus",
        ];
    }
    echo json_encode($result);
}

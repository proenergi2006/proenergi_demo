<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth    = new MyOtentikasi();
$con     = new Connection();
$flash    = new FlashAlerts;

$idr     = htmlspecialchars($_POST["idr"], ENT_QUOTES);

$filePhoto     = htmlspecialchars($_FILES['uploadfoto']['name'], ENT_QUOTES);
$sizePhoto     = htmlspecialchars($_FILES['uploadfoto']['size'], ENT_QUOTES);
$tempPhoto     = htmlspecialchars($_FILES['uploadfoto']['tmp_name'], ENT_QUOTES);
$extPhoto     = strtolower(pathinfo($filePhoto, PATHINFO_EXTENSION)); // Ubah ekstensi menjadi huruf kecil
$max_size    = 2 * 1024 * 1024;
$allow_type    = array("jpg", "jpeg", "png"); // Hanya perlu ekstensi dalam huruf kecil
$pathfile    = $public_base_directory . '/images';

$oke = true;
$url     = BASE_URL_CLIENT . "/acl-change-foto.php";
$manual = false;


if (isset($_POST['idr'])) {
    if (!is_numeric($_POST['idr'])) {
        $idr = paramDecrypt($_POST['idr']);
        $idr = explode('=', $idr);
        $idr = $idr[1]; // Perubahan di sini
    } else {
        $idr = $_POST['idr'];
        $manual = true;
    }
}

if ($manual == true) {
    $uploadnya = true;
    $nqu = 'USR_' . $idr . '_' . sanitize_filename($filePhoto);

    $sql = "UPDATE acl_user SET foto = '" . $nqu . "' WHERE id_user = '" . $idr . "'";
    $con->beginTransaction(); // Mulai transaksi

    $con->setQuery($sql);
    $oke  = $oke && !$con->hasError();

    if ($oke) {
        $mantab  = true;
        if ($uploadnya) {
            $tmpPot = glob($pathfile . "/USR_" . $idr . "_*.{jpg,jpeg,gif,png,pdf}", GLOB_BRACE);

            if (count($tmpPot) > 0) {
                foreach ($tmpPot as $datj)
                    if (file_exists($datj)) unlink($datj);
            }
            $tujuan  = $pathfile . "/" . $nqu;
            $mantab  = $mantab && move_uploaded_file($tempPhoto, $tujuan);
            if (file_exists($tempPhoto)) unlink($tempPhoto);
        }
        $con->commit(); // Commit transaksi
        $con->close();
        $url = BASE_URL_CLIENT . "/acl-user.php";
        $url .= '?' . paramEncrypt('idr=' . $idr);
        $flash->add("success", "Foto  telah berhasil diubah", $url);
    } else {
        $con->rollBack(); // Rollback jika terjadi kesalahan
        $con->clearError();
        $con->close();
        $flash->add("error", "Maaf sistem mengalami kendala, silahkan coba lagi", BASE_REFERER);
    }
}

<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "mailgen");

$auth    = new MyOtentikasi();
$con     = new Connection();
$flash    = new FlashAlerts;
$enk      = decode($_SERVER['REQUEST_URI']);
$idr     = isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
$url     = BASE_URL_CLIENT . "/vendor-po-new-crushed-stone-detail.php?" . paramEncrypt("idr=" . $idr);

$sesrol     = paramDecrypt($_SESSION["sinori" . SESSIONID]["id_role"]);
$sesuser     = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);
$seswil     = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);


$oke = true;
$con->beginTransaction();
$con->clearError();

$cek2 = "select nomor_po from new_pro_inventory_vendor_po_crushed_stone where id_master = '" . $idr . "'";
$row2 = $con->getOne($cek2);
$ems1 = "select email_user from acl_user where id_role = 4";

$sql = "update new_pro_inventory_vendor_po_crushed_stone set disposisi_po = '1' 
		where id_master = '" . $idr . "'";

$con->setQuery($sql);
$oke  = $oke && !$con->hasError();

if ($oke) {
    $rms1 = $con->getResult($ems1);
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 465;
    $mail->SMTPSecure = 'ssl';
    $mail->SMTPAuth = true;
    $mail->SMTPKeepAlive = true;
    $mail->Username = USR_EMAIL_PROENERGI202389;
    $mail->Password = PWD_EMAIL_PROENERGI202389;

    $mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');
    foreach ($rms1 as $datms) {
        $mail->addAddress($datms['email_user']);
    }
    $mail->Subject = "Persetujuan PO crushed stone [" . date('d/m/Y H:i:s') . "]";
    $mail->msgHTML(paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " Meminta Persetujuan Po Supplier Crushed Stone <p>" . BASE_SERVER . "</p>");
    $mail->send();

    $con->commit();
    $con->close();
    $flash->add("success", "Persetujuan untuk PO crushed stone " . $row2 . " sudah diajukan", $url);
} else {
    $con->rollBack();
    $con->clearError();
    $con->close();
    $flash->add("error", "GAGAL_MASUK", BASE_REFERER);
}

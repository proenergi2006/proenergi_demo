<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "htmlawed", "mailgen");

$auth    = new MyOtentikasi();
$con     = new Connection();
$flash    = new FlashAlerts;
$enk      = decode($_SERVER['REQUEST_URI']);
$act     = null;
if (isset($enk['act'])) $act = $enk['act'];
if (isset($_POST['act'])) $act = htmlspecialchars($_POST["act"], ENT_QUOTES);
$idr     = htmlspecialchars($_POST["idr"], ENT_QUOTES);
$idw     = htmlspecialchars($_POST["idw"], ENT_QUOTES);
$revert = isset($_POST["revert"]) ? htmlspecialchars($_POST["revert"], ENT_QUOTES) : null;

$summary = '';
if (isset($_POST["summary"]))
    $summary = str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["summary"], ENT_QUOTES));
$backdis = isset($_POST["summary_revert"]) ? str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["summary_revert"], ENT_QUOTES)) : '';

$pic    = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']);

$oke = true;
$con->beginTransaction();
$con->clearError();

$oke = true;
if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 4) {
    if ($revert == 1) {
        $sql2 = "update new_pro_inventory_vendor_po set cfo_result = 1, cfo_pic = '" . $pic . "', revert_cfo = 1, revert_cfo_summary = '" . $backdis . "', cfo_tanggal = NOW(),  disposisi_po = 3 where id_master = '" . $idr . "'";
        $con->setQuery($sql2);
        $oke  = $oke && !$con->hasError();

        $ems1 = "select email_user from acl_user where id_role = 5";
        $sbjk = "Pengembalian PO Supplier[" . date('d/m/Y H:i:s') . "]";
        $pesn = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " meminta anda untuk merevisi ulang PO";
        $url  = BASE_URL_CLIENT . "/verifikasi-po.php";
    } else if ($revert == 2) {
        $sql2 = "update new_pro_inventory_vendor_po set cfo_summary = '" . $summary . "', cfo_result = 1, cfo_pic = '" . $pic . "', cfo_tanggal = NOW(), disposisi_po = 2 where id_master = '" . $idr . "'";
        $con->setQuery($sql2);
        $oke  = $oke && !$con->hasError();

        $ems1 = "select email_user from acl_user where id_role = 5";
        $sbjk = "Persetujuan PO Supplier [" . date('d/m/Y H:i:s') . "]";
        $pesn = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " telah melakukan verifikasi PO Supplier";
    }
} else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 21) {
    if ($revert == 1) {
        $sql2 = "update new_pro_inventory_vendor_po set ceo_result = 1, ceo_pic = '" . $pic . "', ceo_pic = '" . $pic . "', ceo_tanggal = NOW(), revert_ceo = 1, revert_ceo_summary = '" . $backdis . "', disposisi_po = 5 where id_master = '" . $idr . "'";
        $con->setQuery($sql2);
        $oke  = $oke && !$con->hasError();

        $sql3 = "update new_pro_inventory_gain_loss set ceo_result = 1, ceo_summary = '" . $backdis . "', disposisi_gain_loss = 3 where id_po_supplier = '" . $idr . "'";
        $con->setQuery($sql3);
        $oke  = $oke && !$con->hasError();


        $ems1 = "select email_user from acl_user where id_role = 5";
        $sbjk = "Pengembalian PO Supplier [" . date('d/m/Y H:i:s') . "]";
        $pesn = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " meminta anda untuk merevisi ulang PO";
        $url  = BASE_URL_CLIENT . "/verifikasi-po.php";
    } else if ($revert == 2) {
        $sql2 = "update new_pro_inventory_vendor_po set ceo_summary = '" . $summary . "', ceo_result = 1, ceo_pic = '" . $pic . "', ceo_tanggal = NOW(), disposisi_po = 4 where id_master = '" . $idr . "'";
        $con->setQuery($sql2);
        $oke  = $oke && !$con->hasError();

        $sql3 = "update new_pro_inventory_gain_loss set ceo_result = 1, ceo_pic = '" . $pic . "', ceo_summary = '" . $summary . "', ceo_tanggal = NOW(), disposisi_gain_loss = 2 where id_po_supplier = '" . $idr . "'";
        $con->setQuery($sql3);
        $oke  = $oke && !$con->hasError();


        $ems1 = "select email_user from acl_user where id_role = 5";
        $sbjk = "Persetujuan PO Supplier[" . date('d/m/Y H:i:s') . "]";
        $pesn = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " telah melakukan verifikasi PO Supplier";
    }
}

$pesn .= "<p>" . BASE_SERVER . "</p>";

if ($oke) {

    if ($ems1) {
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
        $mail->Subject = $sbjk;
        $mail->msgHTML($pesn);
        $mail->send();
    }

    $con->commit();
    $con->close();
    header("location: " . BASE_URL_CLIENT . "/verifikasi-po.php");
    exit();
} else {
    $con->rollBack();
    $con->clearError();
    $con->close();
    $flash->add("error", $msg, BASE_REFERER);
}

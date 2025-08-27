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
$id_pr     = htmlspecialchars($_POST["id_pr"], ENT_QUOTES);
$id_prd     = htmlspecialchars($_POST["id_prd"], ENT_QUOTES);
$id_pod     = htmlspecialchars($_POST["id_pod"], ENT_QUOTES);
$id_ds     = htmlspecialchars($_POST["id_ds"], ENT_QUOTES);
$idw     = htmlspecialchars($_POST["idw"], ENT_QUOTES);
$id_plan     = htmlspecialchars($_POST["id_plan"], ENT_QUOTES);
$is_loaded    = htmlspecialchars($_POST["is_loaded"], ENT_QUOTES);
$id_vendor     = htmlspecialchars($_POST["id_vendor"], ENT_QUOTES);
$id_produk     = htmlspecialchars($_POST["id_produk"], ENT_QUOTES);
$id_po_supplier    = isset($_POST["id_po_supplier"]) ? htmlspecialchars($_POST["id_po_supplier"], ENT_QUOTES) : null;
$id_po_receive     = isset($_POST["id_po_receive"]) ? htmlspecialchars($_POST["id_po_receive"], ENT_QUOTES) : null;
$volume     = htmlspecialchars($_POST["volume"], ENT_QUOTES);
$extend = isset($_POST["extend"]) ? htmlspecialchars($_POST["extend"], ENT_QUOTES) : null;
$revert = isset($_POST["revert"]) ? htmlspecialchars($_POST["revert"], ENT_QUOTES) : null;
$is_request = isset($_POST["is_request"]) ? htmlspecialchars($_POST["is_request"], ENT_QUOTES) : null;
$tgl_kirim = isset($_POST["tgl_kirim"]) ? htmlspecialchars($_POST["tgl_kirim"], ENT_QUOTES) : null;
$dis_lo = isset($_POST["dis_lo"]) ? htmlspecialchars($_POST["dis_lo"], ENT_QUOTES) : null;
$summary = '';
if (isset($_POST["summary"]))
    $summary = str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["summary"], ENT_QUOTES));
$backdis = isset($_POST["summary_revert"]) ? str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["summary_revert"], ENT_QUOTES)) : '';
$url     = BASE_URL_CLIENT . "/verifikasi-losses.php?" . paramEncrypt("idr=" . $idr);
$pic    = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']);

$oke = true;
$con->beginTransaction();
$con->clearError();

$wilayah    = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 7) {
    $sql3 = "update pro_po_ds_detail set flag_approval = 1, bm_result = 1 ,bm_tanggal = NOW(), bm_pic = '" . $pic . "',  disposisi_losses = 4 where id_dsd = '" . $idr . "'";
    $con->setQuery($sql3);
    $oke = $oke && !$con->hasError();

    if ($oke) {

        $ems1 = "select distinct email_user FROM acl_user WHERE id_role = 9 and id_wilayah = '" . $wilayah . "'";

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
            $mail->Subject = "Verifikasi Pengajuan  Losses Berhasil Disetujui,  [" . date('d/m/Y H:i:s') . "]";
            $mail->msgHTML(paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " melakukan verifikasi Losses Pengiriman");
            $mail->send();
        }



        if ($oke) {
            $con->commit();
            $con->close();
            $flash->add("success", "Data Losses telah berhasil disimpan", $url);
            header("location: " . $url);
            exit();
        } else {
            $con->rollBack();
            $con->clearError();
            $con->close();
            $flash->add("error", "GAGAL_MASUK", BASE_REFERER);
        }
    }
} elseif (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 6) {
    $sql3 = "update pro_po_ds_detail set flag_approval = 0, om_result = 1 ,om_tanggal = NOW(), om_pic = '" . $pic . "',  disposisi_losses = 3 where id_dsd = '" . $idr . "'";
    $con->setQuery($sql3);
    $oke = $oke && !$con->hasError();

    if ($oke) {

        $ems1 = "select distinct email_user FROM acl_user WHERE id_role = 15";

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
            $mail->Subject = "Verifikasi Pengajuan  Losses Berhasil Disetujui,  [" . date('d/m/Y H:i:s') . "]";
            $mail->msgHTML(paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " OM melakukan verifikasi Losses Pengiriman dan melanjutkan ke manager finance");
            $mail->send();
        }



        if ($oke) {
            $con->commit();
            $con->close();
            $flash->add("success", "Data Losses telah berhasil disimpan", $url);
            header("location: " . $url);
            exit();
        } else {
            $con->rollBack();
            $con->clearError();
            $con->close();
            $flash->add("error", "GAGAL_MASUK", BASE_REFERER);
        }
    }
} elseif (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 15) {
    $sql3 = "update pro_po_ds_detail set flag_approval = 1, fin_result = 1 , fin_tanggal = NOW(), fin_pic = '" . $pic . "',  disposisi_losses = 4 where id_dsd = '" . $idr . "'";
    $con->setQuery($sql3);
    $oke = $oke && !$con->hasError();

    if ($oke) {

        $ems1 = "select distinct email_user FROM acl_user WHERE id_role = 9 and id_wilayah = '" . $wilayah . "'";

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
            $mail->Subject = "Verifikasi Pengajuan  Losses Berhasil Disetujui,  [" . date('d/m/Y H:i:s') . "]";
            $mail->msgHTML(paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " melakukan verifikasi Losses Pengiriman");
            $mail->send();
        }



        if ($oke) {
            $con->commit();
            $con->close();
            $flash->add("success", "Data Losses telah berhasil disimpan", $url);
            header("location: " . $url);
            exit();
        } else {
            $con->rollBack();
            $con->clearError();
            $con->close();
            $flash->add("error", "GAGAL_MASUK", BASE_REFERER);
        }
    }
}

<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "htmlawed", "mailgen");

$auth    = new MyOtentikasi();
$con     = new Connection();
$flash   = new FlashAlerts;
$revisi_dr = isset($_POST["revisiDRPR"]) ? htmlspecialchars($_POST["revisiDRPR"], ENT_QUOTES) : null;
$idr       = isset($_POST["idr"]) ? htmlspecialchars($_POST["idr"], ENT_QUOTES) : null;
$idw       = isset($_POST["idw"]) ? htmlspecialchars($_POST["idw"], ENT_QUOTES) : null;
$id_plan   = isset($_POST["id_plan"]) ? $_POST["id_plan"] : [];
$id_prd    = isset($_POST["id_prd"]) ? $_POST["id_prd"] : [];

header('Content-Type: application/json');

if ($revisi_dr == 1 && $idr !== null) { // Memastikan $idr tidak null
    // Menghapus detail PR
    foreach ($id_prd as $prd) {
        $sqlUpdate = 'delete FROM pro_pr_detail WHERE id_prd = "' . htmlspecialchars($prd, ENT_QUOTES) . '"';
        $con->setQuery($sqlUpdate); // Pastikan setQuery mengelola kesalahan dengan baik
        if ($con->hasError()) {
            echo json_encode(['status' => 'error', 'message' => 'Kesalahan saat menghapus detail PR.']);
            exit();
        }
    }

    // Menghapus PR
    $sqlUpdate1 = 'delete FROM pro_pr WHERE id_pr = "' . $idr . '"';
    $con->setQuery($sqlUpdate1);
    if ($con->hasError()) {
        echo json_encode(['status' => 'error', 'message' => 'Kesalahan saat menghapus PR.']);
        exit();
    }

    // Memperbarui status_plan untuk setiap id_plan
    foreach ($id_plan as $plan) {
        $sqlUpdate2 = 'update pro_po_customer_plan SET status_plan = 0 WHERE id_plan = "' . htmlspecialchars($plan, ENT_QUOTES) . '"';
        $con->setQuery($sqlUpdate2);
        if ($con->hasError()) {
            echo json_encode(['status' => 'error', 'message' => 'Kesalahan saat memperbarui status plan.']);
            exit();
        }
    }

    $ems1 = "select distinct email_user FROM acl_user WHERE id_role = 5 and id_wilayah = '" . $idw . "' ";

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
        $mail->Subject = "Revisi DR ke DP  [" . date('d/m/Y H:i:s') . "]";
        $mail->msgHTML(paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " kembalikan status DR ke delivery plan logistik");
        $mail->send();
    }


    // Jika semua query berhasil
    echo json_encode([
        'status' => 'success',
        'message' => 'Revisi DR berhasil disimpan.'
    ]);
    exit();
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Input tidak valid.'
    ]);
    exit();
}

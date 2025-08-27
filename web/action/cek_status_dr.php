<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "htmlawed", "mailgen");

$auth    = new MyOtentikasi();
$con     = new Connection();
$flash    = new FlashAlerts;
$revisi_dr = htmlspecialchars($_POST["revisiDR"], ENT_QUOTES);
$idr       = htmlspecialchars($_POST["idr"], ENT_QUOTES);
$wilayah    = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

header('Content-Type: application/json');

if ($revisi_dr == 1) {
    $sqlCheck = 'select COUNT(*) as total FROM pro_po WHERE id_pr = "' . $idr . '"';
    $result   = $con->getResult($sqlCheck);

    if ($result && $result[0]['total'] > 0) {
        // Jika id_pr sudah ada
        echo json_encode([
            'status' => 'error',
            'message' => 'Nomor DR sudah ada di Proses di PO Transportir. Proses update dibatalkan.'
        ]);
        exit();
    } else {
        // Proses update jika id_pr belum ada
        $sqlUpdate = 'update pro_pr SET disposisi_pr = 6 WHERE id_pr = "' . $idr . '"';
        $con->setQuery($sqlUpdate);


        $ems1 = "select distinct email_user FROM acl_user WHERE id_role = 5";

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
            $mail->Subject = "Revisi Status DR   [" . date('d/m/Y H:i:s') . "]";
            $mail->msgHTML(paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " kembalikan status DR ke terverifikasi");
            $mail->send();
        }

        if (!$con->hasError()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Revisi DR berhasil disimpan.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan revisi DR.'
            ]);
        }
        exit();
    }
}

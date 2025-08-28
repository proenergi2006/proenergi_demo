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

    // if ($ems1) {
    //     $rms1 = $con->getResult($ems1);
    //     $mail = new PHPMailer;
    //     $mail->isSMTP();
    //     $mail->Host = 'smtp.gmail.com';
    //     $mail->Port = 465;
    //     $mail->SMTPSecure = 'ssl';
    //     $mail->SMTPAuth = true;
    //     $mail->SMTPKeepAlive = true;
    //     $mail->Username = USR_EMAIL_PROENERGI202389;
    //     $mail->Password = PWD_EMAIL_PROENERGI202389;

    //     $mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');
    //     foreach ($rms1 as $datms) {
    //         $mail->addAddress($datms['email_user']);
    //     }
    //     $mail->Subject = $sbjk;
    //     $mail->msgHTML($pesn);
    //     $mail->send();
    // }

    //Approve CEO untuk Accurate agar membuka PO yang ditutup sebelum di verifikasi CEO
    if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 21) {
        if ($revert == 2) {
            $queryget = "SELECT * FROM new_pro_inventory_vendor_po WHERE id_master ='" . $idr . "'";
            $rowgetid = $con->getRecord($queryget);

            $id_cabang = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

            $queryget_cabang = "SELECT * FROM pro_master_cabang WHERE id_master = '" . $id_cabang . "'";
            $rowget_cabang = $con->getRecord($queryget_cabang);
            if ($rowgetid['id_accurate'] != null) {

                $data_id = http_build_query([
                    'id' => $rowgetid['id_accurate'],
                ]);

                $url_getpo = 'https://zeus.accurate.id/accurate/api/purchase-order/detail.do?' . $data_id;

                $result_detail = curl_get($url_getpo);

                if ($result_detail['s'] == true) {
                    $urlnya = 'https://zeus.accurate.id/accurate/api/purchase-order/save.do';
                    // Data yang akan dikirim dalam format JSON
                    $data = array(
                        'id'                => $rowgetid['id_accurate'],
                        'branchName'        => $rowget_cabang['nama_cabang'] == 'Kantor Pusat' ? 'Head Office' : $rowget_cabang['nama_cabang'],
                        'toAddress'         => $result_detail['d']['toAddress'],
                        'manualClosed'      => false,
                        // 'detailItem'       	=> [],
                    );

                    // // Menggunakan foreach untuk mengisi detailItem
                    foreach ($result_detail['d']['detailItem'] as $item) {
                        $data['detailItem'][] = [
                            'id'           => $item['id'],
                            'itemNo'       => $item['item']['no'],
                            'quantity'     => $item['quantity'],
                            'unitPrice'    => $item['unitPrice'],
                            'manualClosed' => false
                        ];
                    }

                    // Mengonversi data menjadi format JSON
                    $jsonData = json_encode($data);
                    $result = curl_post($urlnya, $jsonData);


                    if ($result['s'] == true) {
                        $con->commit();
                        $con->close();
                        header("location: " . BASE_URL_CLIENT . "/verifikasi-po.php");
                        exit();
                    } else {
                        $con->rollBack();
                        $con->clearError();
                        $con->close();
                        $flash->add("error", $result["d"][0] . " - Response dari Accurate", BASE_REFERER);
                    }
                } else {
                    $con->rollBack();
                    $con->clearError();
                    $con->close();
                    $flash->add("error", $$result_detail["d"][0] . " - Response dari Accurate", BASE_REFERER);
                }
            } else {
                $con->rollBack();
                $con->clearError();
                $con->close();
                $flash->add("error", "Id accurate tidak ditemukan", BASE_REFERER);
            }
        }
    } else {
        $con->commit();
        $con->close();
        header("location: " . BASE_URL_CLIENT . "/verifikasi-po.php");
        exit();
    }
    // $con->commit();
    // $con->close();
    // header("location: " . BASE_URL_CLIENT . "/verifikasi-po.php");
    // exit();
} else {
    $con->rollBack();
    $con->clearError();
    $con->close();
    $flash->add("error", $msg, BASE_REFERER);
}

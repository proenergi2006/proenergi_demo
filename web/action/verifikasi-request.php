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
$idr       = htmlspecialchars($_POST["idr"], ENT_QUOTES);
$id_pr     = htmlspecialchars($_POST["id_pr"], ENT_QUOTES);
$id_prd    = htmlspecialchars($_POST["id_prd"], ENT_QUOTES);
$id_pod    = htmlspecialchars($_POST["id_pod"], ENT_QUOTES);
$id_ds     = htmlspecialchars($_POST["id_ds"], ENT_QUOTES);
$idw       = htmlspecialchars($_POST["idw"], ENT_QUOTES);
$id_plan   = htmlspecialchars($_POST["id_plan"], ENT_QUOTES);
$is_loaded = htmlspecialchars($_POST["is_loaded"], ENT_QUOTES);
$id_vendor = htmlspecialchars($_POST["id_vendor"], ENT_QUOTES);
$id_produk = htmlspecialchars($_POST["id_produk"], ENT_QUOTES);
$wilayah = htmlspecialchars($_POST["wilayah"], ENT_QUOTES);
$id_po_supplier = isset($_POST["id_po_supplier"]) ? htmlspecialchars($_POST["id_po_supplier"], ENT_QUOTES) : null;
$id_po_receive  = isset($_POST["id_po_receive"]) ? htmlspecialchars($_POST["id_po_receive"], ENT_QUOTES) : null;
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
$url     = BASE_URL_CLIENT . "/verifikasi-request.php?" . paramEncrypt("idr=" . $idr);
$pic    = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']);

$oke = true;
$con->beginTransaction();
$con->clearError();



// $wilayah    = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

if ($revert == 1 && $is_request == 2) {
    $sql1 = "update pro_po_customer_plan set tanggal_kirim = '" . tgl_db($tgl_kirim) . "' where id_plan = '" . $id_plan . "'";
    $con->setQuery($sql1);
    $oke  = $oke && !$con->hasError();
    $sql2 = "update pro_po_detail set tgl_kirim_po = '" . tgl_db($tgl_kirim) . "' where id_pod = '" . $id_pod . "'";
    $con->setQuery($sql2);
    $oke  = $oke && !$con->hasError();
    $sql3 = "update pro_po_ds_detail set is_approved = 1, tanggal_approved = NOW(), disposisi_request = 2, is_revert = '" . $revert . "' where id_dsd = '" . $idr . "'";
    $con->setQuery($sql3);
    $oke = $oke && !$con->hasError();
} elseif ($revert == 1 && $is_request == 3 && $is_loaded == 0) {
    $sql3 = "update pro_po_ds_detail set is_approved = 1, is_cancel = 1, tanggal_cancel = NOW(), tanggal_approved = NOW(), disposisi_request = 2, is_revert = '" . $revert . "'  where id_dsd = '" . $idr . "'";
    $con->setQuery($sql3);
    $oke = $oke && !$con->hasError();
    $sql4 = 'delete from new_pro_inventory_depot where id_jenis = 6 and id_prd = "' . $id_prd . '"';
    $con->setQuery($sql4);
    $oke  = $oke && !$con->hasError();
    $sql5 = "update pro_po_customer_plan set status_plan = '2', is_approved = 0, catatan_reschedule = '" . $revert . "' where id_plan = '" . $id_plan . "'";
    $con->setQuery($sql5);
    $oke  = $oke && !$con->hasError();

    $cekoslog = "SELECT a.id_dsd, d.id_wilayah, c.link_gps FROM pro_po_ds_detail a
    JOIN pro_po_detail b ON a.id_pod=b.id_pod
    JOIN pro_master_transportir_mobil c ON c.id_master=b.mobil_po
    WHERE c.link_gps='OSLOG' AND a.id_dsd='" . $idr . "'";
    $rowoslog = $con->getRecord($cekoslog);

    if ($rowoslog) {
        $logFilePath = realpath(_DIR_ . '/../../post-data-api-oslog.log.txt');
        // Token Bearer
        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJJZCI6MTE1OSwiTmFtZSI6InByb2VuZXJnaSIsIlJvbGUiOiJhZG1fcHJvZW5lcmdpIiwiQ29tcGFueSI6NjA2LCJVc2VyUG9kSWQiOjAsImlzcyI6Ik9TTE9HIDUgQVBJIn0.H-ljfy7I0zVzpvXsar3FddpUT2RHChNaEP8uw50kmV8';

        // URL API yang akan diakses
        $url_api = "https://oslog.id/javaz-api/shipment-syop/edit-status-cancel/" . $idr;

        $data = [
            "remark" => $summary,
        ];

        // Mengonversi data ke format JSON
        $jsonData = json_encode($data);

        // Catat data POST ke file (append agar data baru ditambahkan ke bawah)
        $logEntry = "Timestamp: " . date("Y-m-d H:i:s") . "\n";
        $logEntry .= "Endpoint: " . $url_api . "\n";
        $logEntry .= "POST Data: " . $jsonData . "\n\n";

        // Menulis log ke file
        file_put_contents($logFilePath, $logEntry, FILE_APPEND);

        // Inisialisasi cURL
        $ch = curl_init($url_api);

        // Setel opsi cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token,
            'Content-Length: ' . strlen($jsonData)
        ]);

        // Eksekusi permintaan dan ambil respons
        $response = curl_exec($ch);

        // Cek jika terjadi kesalahan
        if (curl_errno($ch)) {
            $con->rollBack();
            $con->clearError();
            $con->close();
            $flash->add("error", "Server Error", BASE_REFERER);
        } else {
            $result = json_decode($response, true);
            curl_close($ch);
            if ($result['code'] != 200) {
                $con->rollBack();
                $con->clearError();
                $con->close();
                $flash->add("error", $result['message'], BASE_REFERER);
            }
        }
    }
} elseif ($revert == 1 && $is_request == 3 && $is_loaded == 1) {
    $sql3 = "update pro_po_ds_detail set is_approved = 1, is_cancel = 1, tanggal_cancel = NOW(), tanggal_approved = NOW(), disposisi_request = 2, is_revert = '" . $revert . "'  where id_dsd = '" . $idr . "'";
    $con->setQuery($sql3);
    $oke = $oke && !$con->hasError();
    $sql5 = "update pro_po_customer_plan set status_plan = '2', is_approved = 0, catatan_reschedule = '" . $revert . "' where id_plan = '" . $id_plan . "'";
    $con->setQuery($sql5);
    $oke  = $oke && !$con->hasError();

    $id_terminal = null;

    if ($wilayah == 4) {
        $id_terminal = 67;
    } elseif ($wilayah == 2) {
        $id_terminal = 80;
    } elseif ($wilayah == 6) {
        $id_terminal = 68;
    } elseif ($wilayah == 3) {
        $id_terminal = 78;
    } elseif ($wilayah == 5) {
        $id_terminal = 81;
    } elseif ($wilayah == 7) {
        $id_terminal = 79;
    } elseif ($wilayah == 11) {
        $id_terminal = 82;
    } else {
        $id_terminal = 0;
    }

    $cek_idprd = "select * from new_pro_inventory_depot where id_jenis = 3 and id_prd = '" . $id_prd . "'";
    $row_idprd = $con->getRecord($cek_idprd);

    if (!$row_idprd) {
        $sql6 = "insert into new_pro_inventory_depot (id_datanya, id_jenis, id_produk, id_terminal, id_vendor, id_po_supplier, id_po_receive, tanggal_inven, adj_inven, out_inven_virtual, keterangan, created_time, created_ip, created_by, id_dsd, id_pr, id_prd
                ) VALUES ('TF TRUCK', '3', '" . $id_produk . "', '" . $id_terminal . "', '" . $id_vendor . "', '" . $id_po_supplier . "', '" . $id_po_receive . "', NOW(), '" . $volume . "', '0', 'TRANSFER STOCK TRUCK', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . paramDecrypt($_SESSION['sinori" . SESSIONID . "']['fullname']) . "', '" . $idr . "', '" . $id_pr . "', '" . $id_prd . "'
                )";
        $con->setQuery($sql6);
        $oke = $oke && !$con->hasError();
    }

    // $sql6 = "insert into new_pro_inventory_depot (id_datanya, id_jenis, id_produk, id_terminal, id_vendor, id_po_supplier, id_po_receive, tanggal_inven, adj_inven, out_inven_virtual, keterangan, created_time, created_ip, created_by, id_dsd, id_pr, id_prd
    //             ) VALUES ('TF TRUCK', '3', '" . $id_produk . "', '" . $id_terminal . "', '" . $id_vendor . "', '" . $id_po_supplier . "', '" . $id_po_receive . "', NOW(), '" . $volume . "', '0', 'TRANSFER STOCK TRUCK', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . paramDecrypt($_SESSION['sinori" . SESSIONID . "']['fullname']) . "', '" . $idr . "', '" . $id_pr . "', '" . $id_prd . "'
    //             )";
    // $con->setQuery($sql6);
    // $oke = $oke && !$con->hasError();

    $cekoslog = "SELECT a.id_dsd, d.id_wilayah, c.link_gps FROM pro_po_ds_detail a
    JOIN pro_po_detail b ON a.id_pod=b.id_pod
    JOIN pro_master_transportir_mobil c ON c.id_master=b.mobil_po
    WHERE c.link_gps='OSLOG' AND a.id_dsd='" . $idr . "'";
    $rowoslog = $con->getRecord($cekoslog);

    $logFilePath = realpath(_DIR_ . '/../../post-data-api-oslog.log.txt');

    if ($rowoslog) {
        // API CANCEL
        // Token Bearer
        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJJZCI6MTE1OSwiTmFtZSI6InByb2VuZXJnaSIsIlJvbGUiOiJhZG1fcHJvZW5lcmdpIiwiQ29tcGFueSI6NjA2LCJVc2VyUG9kSWQiOjAsImlzcyI6Ik9TTE9HIDUgQVBJIn0.H-ljfy7I0zVzpvXsar3FddpUT2RHChNaEP8uw50kmV8';

        // URL API yang akan diakses
        $url_api = "https://oslog.id/javaz-api/shipment-syop/edit-status-cancel/" . $idr;

        $data = [
            "remark" => $summary,
        ];

        // Mengonversi data ke format JSON
        $jsonData = json_encode($data);

        // Catat data POST ke file (append agar data baru ditambahkan ke bawah)
        $logEntry = "Timestamp: " . date("Y-m-d H:i:s") . "\n";
        $logEntry .= "Endpoint: " . $url_api . "\n";
        $logEntry .= "POST Data: " . $jsonData . "\n\n";

        // Menulis log ke file
        file_put_contents($logFilePath, $logEntry, FILE_APPEND);

        // Inisialisasi cURL
        $ch = curl_init($url_api);

        // Setel opsi cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token,
            'Content-Length: ' . strlen($jsonData)
        ]);

        // Eksekusi permintaan dan ambil respons
        $response = curl_exec($ch);

        // Cek jika terjadi kesalahan
        if (curl_errno($ch)) {
            $con->rollBack();
            $con->clearError();
            $con->close();
            $flash->add("error", "Server Error", BASE_REFERER);
        } else {
            $result = json_decode($response, true);
            curl_close($ch);
            if ($result['code'] != 200) {
                $con->rollBack();
                $con->clearError();
                $con->close();
                $flash->add("error", $result['message'], BASE_REFERER);
            }
        }
    }
} elseif ($revert == 2 && $is_request == 2) {
    $sql1 = "update pro_po_ds_detail set is_approved = 1, tanggal_approved = NOW(), disposisi_request = 2, is_revert = '" . $revert . "', revert_summary = '" . $backdis . "', tanggal_revert = NOW() where id_dsd = '" . $idr . "'";
    $con->setQuery($sql1);
    $oke = $oke && !$con->hasError();
} elseif ($revert == 2 && $is_request == 3) {
    $sql1 = "update pro_po_ds_detail set is_approved = 1, tanggal_approved = NOW(), disposisi_request = 2, is_revert = '" . $revert . "', revert_summary = '" . $backdis . "', tanggal_revert = NOW() where id_dsd = '" . $idr . "'";
    $con->setQuery($sql1);
    $oke = $oke && !$con->hasError();
}



$dt5_text = '';
if ($is_request == 1) {
    $dt5_text = 'Change Depot';
} else if ($is_request == 2 && $revert == 1) {
    $dt5_text = 'Reschedule Berhasil Disetujui';
} else if ($is_request == 2 && $revert == 2) {
    $dt5_text = 'Reschedule Ditolak ';
} else if ($is_request == 3 && $revert == 1) {
    $dt5_text = 'Cancel Berhasil Disetujui';
} else if ($is_request == 3 && $revert == 2) {
    $dt5_text = 'Cancel Ditolak';
}

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
    $mail->Subject = "Verifikasi Pengajuan  " . $dt5_text . "  [" . date('d/m/Y H:i:s') . "]";
    $mail->msgHTML(paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " melakukan verifikasi Request Pengiriman");
    $mail->send();
}



if ($oke) {
    $con->commit();
    $con->close();
    $flash->add("success", "Data DR telah berhasil disimpan", $url);
    header("location: " . $url);
    exit();
} else {
    $con->rollBack();
    $con->clearError();
    $con->close();
    $flash->add("error", "GAGAL_MASUK", BASE_REFERER);
}

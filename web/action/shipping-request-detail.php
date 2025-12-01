<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "htmlawed", "mailgen");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;
$enk  	= decode($_SERVER['REQUEST_URI']);
$act	= ($enk['act'] ? $enk['act'] : htmlspecialchars($_POST["act"], ENT_QUOTES));
$idr 	= isset($_POST["idr"]) ? $_POST["idr"] : null;

$id_vessel  = htmlspecialchars($_POST["id_vessel"], ENT_QUOTES);
$ket_log    =htmlspecialchars($_POST["ket_log"], ENT_QUOTES);
$mgrfin_summary    =htmlspecialchars($_POST["mgrfin_summary"], ENT_QUOTES);
$ceo_summary    =htmlspecialchars($_POST["ceo_summary"], ENT_QUOTES);
$freight    =htmlspecialchars($_POST["freight"], ENT_QUOTES);
$loss	    = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["loss"]), ENT_QUOTES);
$approve     = isset($_POST["approve"]) ? htmlspecialchars($_POST["approve"], ENT_QUOTES) : null;

if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 16) {
        // if ($id_vessel == "" || $loss == 0) {
        //     $con->close();
        //     $flash->add("error", "KOSONG", BASE_REFERER);
        // } else {
        $oke = true;
        $con->beginTransaction();
        $con->clearError();
        $query_no_req = "SELECT * FROM new_pro_inventory_vendor_po_ship_req WHERE id_master = '".$idr."' ";
        $row2 = $con->getRecord($query_no_req);
        $no_req = $row2['nomor_req'];
        $explode = explode("/", $no_req);
        $year_req = $explode[4] ? $explode[4] : $year;
        $month_req = $explode[3];
        $sum = $explode[2]+1;

        $urut_req = $explode[0] ? $explode[0]+1 : 1;
        $no_req = sprintf("%03s", $urut_req);
        $noms_req = $explode[0] . '/PE/LOG-HO/250/' .  $month_req. '/' . $year_req;
        // var_dump($noms_req);
        // exit;
        $sql = "
                update new_pro_inventory_vendor_po_ship_req set nomor_si = '" . $noms_req . "', ket_log = '" . $ket_log . "',
                status = 1, log_tanggal = NOW(), log_pic = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "' 
                where id_master = '" . $idr . "'
            ";
        $con->setQuery($sql);
        $oke  = $oke && !$con->hasError();
        

        // $sbjk = "Shipping Request[" . date('d/m/Y H:i:s') . "]";
        // $pesn = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " meminta untuk Shipping instruction";
        // $pesn .= "<p>" . BASE_SERVER . "</p>";

        if ($oke) {

            // if ($ems1) {
            // 	$rms1 = $con->getResult($ems1);
            // 	$mail = new PHPMailer;
            // 	$mail->isSMTP();
            // 	$mail->Host = 'smtp.gmail.com';
            // 	$mail->Port = 465;
            // 	$mail->SMTPSecure = 'ssl';
            // 	$mail->SMTPAuth = true;
            // 	$mail->SMTPKeepAlive = true;
            // 	$mail->Username = USR_EMAIL_PROENERGI202389;
            // 	$mail->Password = PWD_EMAIL_PROENERGI202389;

            // 	$mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');
            // 	foreach ($rms1 as $datms) {
            // 		$mail->addAddress($datms['email_user']);
            // 	}
            // 	$mail->Subject = $sbjk;
            // 	$mail->msgHTML($pesn);
            // 	$mail->send();
            // }

            $con->commit();
            $con->close();
            $flash->add("success", "Data berhasil disimpan", BASE_URL_CLIENT . "/shipping-request-detail.php?" . paramEncrypt('idr=' . $idr));
            // header("location: " . BASE_URL_CLIENT . "/vendor-po-ship-req.php");
            exit();
        } else {
            $con->rollBack();
            $con->clearError();
            $con->close();
            $flash->add("error", 'Maaf Data Gagal Disimpan', BASE_REFERER);
        }
        // }
} else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 15) {
    $oke = true;
    $con->beginTransaction();
    $con->clearError();
   
    $sql = "
            update new_pro_inventory_vendor_po_ship_req set mgrfin_summary = '" . $mgrfin_summary . "',
            status = 2,  mgrfin_result= '" . $approve . "', mgrfin_tanggal = NOW(), mgrfin_pic = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "' 
            where id_master = '" . $idr . "'
        ";
    $con->setQuery($sql);
    $oke  = $oke && !$con->hasError();
    

    // $sbjk = "Shipping Request[" . date('d/m/Y H:i:s') . "]";
    // $pesn = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " meminta untuk Shipping instruction";
    // $pesn .= "<p>" . BASE_SERVER . "</p>";
    if ($oke) {

        // if ($ems1) {
        // 	$rms1 = $con->getResult($ems1);
        // 	$mail = new PHPMailer;
        // 	$mail->isSMTP();
        // 	$mail->Host = 'smtp.gmail.com';
        // 	$mail->Port = 465;
        // 	$mail->SMTPSecure = 'ssl';
        // 	$mail->SMTPAuth = true;
        // 	$mail->SMTPKeepAlive = true;
        // 	$mail->Username = USR_EMAIL_PROENERGI202389;
        // 	$mail->Password = PWD_EMAIL_PROENERGI202389;

        // 	$mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');
        // 	foreach ($rms1 as $datms) {
        // 		$mail->addAddress($datms['email_user']);
        // 	}
        // 	$mail->Subject = $sbjk;
        // 	$mail->msgHTML($pesn);
        // 	$mail->send();
        // }

        $con->commit();
        $con->close();
        $flash->add("success", "Data berhasil disimpan", BASE_URL_CLIENT . "/shipping-request-detail.php?" . paramEncrypt('idr=' . $idr));
        // header("location: " . BASE_URL_CLIENT . "/vendor-po-ship-req.php");
        exit();
    } else {
        $con->rollBack();
        $con->clearError();
        $con->close();
        $flash->add("error", 'Maaf Data Gagal Disimpan', BASE_REFERER);
    }
}else{
    $oke = true;
    $con->beginTransaction();
    $con->clearError();
    $query_no_req = "SELECT * FROM new_pro_inventory_vendor_po_ship_req WHERE id_master = '".$idr."' ";
    $row2 = $con->getRecord($query_no_req);
   
    $sql = "
            update new_pro_inventory_vendor_po_ship_req set ceo_summary = '" . $ceo_summary . "',
            status = 3,  ceo_result= '" . $approve . "', ceo_tanggal = NOW(), ceo_pic = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "' 
            where id_master = '" . $idr . "'
        ";
    $con->setQuery($sql);
    $oke  = $oke && !$con->hasError();
    

    // $sbjk = "Shipping Request[" . date('d/m/Y H:i:s') . "]";
    // $pesn = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " meminta untuk Shipping instruction";
    // $pesn .= "<p>" . BASE_SERVER . "</p>";

    if ($oke) {

        // if ($ems1) {
        // 	$rms1 = $con->getResult($ems1);
        // 	$mail = new PHPMailer;
        // 	$mail->isSMTP();
        // 	$mail->Host = 'smtp.gmail.com';
        // 	$mail->Port = 465;
        // 	$mail->SMTPSecure = 'ssl';
        // 	$mail->SMTPAuth = true;
        // 	$mail->SMTPKeepAlive = true;
        // 	$mail->Username = USR_EMAIL_PROENERGI202389;
        // 	$mail->Password = PWD_EMAIL_PROENERGI202389;

        // 	$mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');
        // 	foreach ($rms1 as $datms) {
        // 		$mail->addAddress($datms['email_user']);
        // 	}
        // 	$mail->Subject = $sbjk;
        // 	$mail->msgHTML($pesn);
        // 	$mail->send();
        // }

        $con->commit();
        $con->close();
        $flash->add("success", "Data berhasil disimpan", BASE_URL_CLIENT . "/shipping-request-detail.php?" . paramEncrypt('idr=' . $idr));
        // header("location: " . BASE_URL_CLIENT . "/vendor-po-ship-req.php");
        exit();
    } else {
        $con->rollBack();
        $con->clearError();
        $con->close();
        $flash->add("error", 'Maaf Data Gagal Disimpan', BASE_REFERER);
    }
}

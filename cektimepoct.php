<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "htmlawed", "mailgen");

$conDr1 = new Connection();
$sql01     = "
		select a.*, time_to_sec(a.date_difference) as detiknya 
		from (
			select id_master, created_time, 
			TIMEDIFF(NOW(), created_time) AS date_difference 
			from new_pro_inventory_vendor_po_crushed_stone
			where disposisi_po > 0 and (ceo_result = 0 and cfo_result = 0) 
			and year(tanggal_inven) >= 2024 
		) a 
		where 1=1 and time_to_sec(date_difference) > 1200
	";
$res01 = $conDr1->getResult($sql01);

$ems1 = "";
$oke = true;
if (count($res01) > 0) {
    $ems1 = "select email_user from acl_user where id_role = 21";
    $sbjk = "Persetujuan PO Supplier Crushed Stone[" . date('d/m/Y H:i:s') . "]";
    $pesn = "Sistem SYOP meminta persetujuan untuk PO Supplier Crushed Stone, dikarenakan dalam jangka waktu 20 menit, CFO belum menyetujui PO Supplier Crushed Stone ini";

    foreach ($res01 as $data) {
        $idmaster     = $data['id_master'];
        $sql02     = "
				update new_pro_inventory_vendor_po_crushed_stone set cfo_summary = 'Approved By System Automatically', cfo_result = 1, cfo_pic = 'Syop System', cfo_tanggal = NOW(), 
				disposisi_po = 2
				where id_master = '" . $idmaster . "'
			";
        $conDr1->setQuery($sql02);
        $oke  = $oke && !$conDr1->hasError();
    }
}

if ($ems1) {
    $rms1 = $conDr1->getResult($ems1);
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

$conDr1->close();

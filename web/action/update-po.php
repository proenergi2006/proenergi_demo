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
$act    = ($enk['act'] == "") ? htmlspecialchars($_POST["act"], ENT_QUOTES) : $enk['act'];
$idr     = htmlspecialchars($_POST["idr"], ENT_QUOTES);
$idk     = htmlspecialchars($_POST["idk"], ENT_QUOTES);
$total_inv    = htmlspecialchars($_POST["total_inv"], ENT_QUOTES);
$nomor_po_cust    = htmlspecialchars($_POST["nomor_po_cust"], ENT_QUOTES);
$filePhoto     = htmlspecialchars($_FILES['attachment_order']['name'], ENT_QUOTES);
$sizePhoto     = htmlspecialchars($_FILES['attachment_order']['size'], ENT_QUOTES);
$tempPhoto     = htmlspecialchars($_FILES['attachment_order']['tmp_name'], ENT_QUOTES);
$extPhoto     = substr($filePhoto, strrpos($filePhoto, '.'));
$max_size    = 2 * 1024 * 1024;
$allow_type    = array(".jpg", ".jpeg", ".JPG", ".png", ".pdf", ".rar", ".zip");
$pathfile    = $public_base_directory . '/files/uploaded_user/lampiran';
// var_dump($filePhoto);
// exit;
// echo json_encode([
//     'status' => 'error',
//     'message' =>$filePhoto
// ]);
// exit();
if ($filePhoto == "") {
    echo json_encode([
        'status' => 'error',
        'message' => "Lampiran harus diisi"
    ]);
    exit();
} else if ($nomor_po_cust == "") {
    echo json_encode([
        'status' => 'error',
        'message' => "Nomor PO harus diisi"
    ]);
    exit();
} else if ($filePhoto != "" && $sizePhoto > $max_size) {
    echo json_encode([
        'status' => 'error',
        'message' => "Ukuran file terlalu besar, melebihi 2MB"
    ]);
    exit();
} else if ($filePhoto != "" && !in_array($extPhoto, $allow_type)) {
    echo json_encode([
        'status' => 'error',
        'message' => "Tipe file tidak diperbolehkan"
    ]);
    exit();
} else {
    // if ($total_inv > 0) {
    //     echo json_encode([
    //         'status' => 'error',
    //         'message' => "Tidak dapat update data karena PO sudah terbentuk invoice"
    //     ]);
    //     exit();
    // } else {

    $oke = true;
    $upl = true;
    $con->beginTransaction();
    $con->clearError();

    $sql = "UPDATE pro_po_customer SET nomor_poc = '" . $nomor_po_cust . "',lampiran_poc_ori = '" . sanitize_filename($filePhoto) . "',is_edit = 1 where id_customer = '" . $idr . "' and id_poc = '" . $idk . "'";
    $con->setQuery($sql);

    $nqu = 'POC_' . $idk . '_' . sanitize_filename($filePhoto);
    $que = "update pro_po_customer set lampiran_poc = '" . $nqu . "' where id_poc = '" . $idk . "'";
    $con->setQuery($que);
    $oke  = $oke && !$con->hasError();
    // }


    if ($oke && !$con->hasError()) {
        $mantab  = true;
        if ($upl) {
            $tmpPot = glob($pathfile . "/POC_" . $idk . "_*.{jpg,jpeg,gif,png,pdf,rar,zip}", GLOB_BRACE);

            if (count($tmpPot) > 0) {
                foreach ($tmpPot as $datj)
                    if (file_exists($datj)) unlink($datj);
            }
            $tujuan  = $pathfile . "/" . $nqu;
            $mantab  = $mantab && move_uploaded_file($tempPhoto, $tujuan);
            if (file_exists($tempPhoto)) unlink($tempPhoto);
        }

        if ($mantab) {
            $con->commit();
            $con->close();
            echo json_encode([
                'status' => 'success',
                'message' => 'Update Nomor PO berhasil.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal menyimpan Nomor PO'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menyimpan Nomor PO'
        ]);
    }
    exit();
}

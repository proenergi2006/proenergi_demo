<?php
$fileSizeLimit = 5 * 1024 * 1024; // 5 MB dalam byte
ini_set('upload_max_filesize', $fileSizeLimit);
ini_set('post_max_size', $fileSizeLimit);
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "htmlawed", "mailgen");

$auth    = new MyOtentikasi();
$con     = new Connection();
$flash    = new FlashAlerts;
$enk      = decode($_SERVER['REQUEST_URI']);
$act    = ($enk['act'] ? $enk['act'] : htmlspecialchars($_POST["act"], ENT_QUOTES));
$idr    = isset($_POST["idnya01"]) ? $_POST["idnya01"] : null;

$dt1    = htmlspecialchars($_POST["dt1"], ENT_QUOTES);
$dt2    = htmlspecialchars($_POST["dt2"], ENT_QUOTES);
$dt3    = htmlspecialchars($_POST["dt3"], ENT_QUOTES);
$dt4    = htmlspecialchars($_POST["dt4"], ENT_QUOTES);
$dt5    = htmlspecialchars($_POST["dt5"], ENT_QUOTES);
$dt6    = htmlspecialchars($_POST["dt6"], ENT_QUOTES);
$dt7    = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["dt7"]), ENT_QUOTES);
$dt8    = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt8"]), ENT_QUOTES);
$dt16    = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt16"]), ENT_QUOTES);
$dt22   = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt22"]), ENT_QUOTES);

$subTotal    = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt9"]), ENT_QUOTES);
$dt10    = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt10"]), ENT_QUOTES);
$dt15    = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt15"]), ENT_QUOTES);
$dt19    = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt19"]), ENT_QUOTES);
$ppn_11    = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt11"]), ENT_QUOTES);
$pph_22    = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt12"]), ENT_QUOTES);
$pbbkb    = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt13"]), ENT_QUOTES);
$totalOrder    = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt14"]), ENT_QUOTES);

$kd_tax        = htmlspecialchars($_POST["kd_tax"], ENT_QUOTES);

$ket        = htmlspecialchars($_POST["ket"], ENT_QUOTES);

$volume_po_loss_gain    = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["volume_po_loss_gain"]), ENT_QUOTES);
$volume_terima_loss_gain   = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["volume_terima_loss_gain"]), ENT_QUOTES);
$jenis    = htmlspecialchars($_POST["jenis"], ENT_QUOTES);
$ket_loss_gain    = htmlspecialchars($_POST["ket_loss_gain"], ENT_QUOTES);
$volume_loss_gain   = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["volume_loss_gain"]), ENT_QUOTES);
$volume_bl_loss_gain   = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["volume_bl_loss_gain"]), ENT_QUOTES);



$filePhoto1     = htmlspecialchars($_FILES['file_template']['name'], ENT_QUOTES);
$sizePhoto1     = htmlspecialchars($_FILES['file_template']['size'], ENT_QUOTES);
$tempPhoto1     = htmlspecialchars($_FILES['file_template']['tmp_name'], ENT_QUOTES);
$tipePhoto1     = htmlspecialchars($_FILES['file_template']['type'], ENT_QUOTES);

$folder         = date("Ym");
$pathnya         = $public_base_directory . '/files/uploaded_user/lampiran';

//echo $act; exit;
if ($act == 'update') {
    if ($dt1 == "" || $dt8 == "" || $dt10 == "") {
        $con->close();
        $flash->add("error", "KOSONG", BASE_REFERER);
    } else {
        $id1nya = $idr;

        if ($id1nya) {

            if ($filePhoto1) {
                $fileExt         = strtolower(pathinfo($filePhoto1, PATHINFO_EXTENSION));
                $fileName         = $folder . '/BOL_' . $idnya01 . '_' . md5(basename($filePhoto1, $fileExt)) . '.' . $fileExt;
                $fileOriginName = sanitize_filename($filePhoto1);
                $isUpload         = true;
            } else {
                $fileName         = $arrget[$idx]['filenya'];
                $fileOriginName = $arrget[$idx]['file_upload_ori'];
                $isUpload         = false;
            }

            $oke = true;
            $con->beginTransaction();
            $con->clearError();

            $msg = "GAGAL_UBAH";

            if ($jenis == 1) {
                $sql = "
					update new_pro_inventory_vendor_po set harga_tebus = '" . $dt16 . "', harga_po = '" . $dt8 . "',  volume_ri = '" . $dt15 . "', disposisi_po = 1, cfo_result= 0, cfo_pic= '',  cfo_summary = '', ceo_result = 0, ceo_pic = '', ceo_summary = '', volume_po = '" . $dt10 . "', subtotal = '" . $subTotal . "',  ppn_11 = '" . $ppn_11 . "', pph_22 = '" . $pph_22 . "', pbbkb = '" . $pbbkb . "', pbbkb_po = '" . $dt22 . "', total_order = '" . $totalOrder . "',
					 keterangan =  '" . $ket . "',
					lastupdate_time = NOW(), lastupdate_ip = '" . $_SERVER['REMOTE_ADDR'] . "', lastupdate_by = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "' 
					where id_master = '" . $idr . "'
				";
                $con->setQuery($sql);
                $oke  = $oke && !$con->hasError();
            }






            $sql2 =
                "
                        insert into new_pro_inventory_gain_loss (id_po_supplier, volume_po,  volume_terima, jenis, volume, file_upload, file_upload_ori, ket, disposisi_gain_loss, created_time, created_ip, 
                         created_by) (
                            select '" . $idr . "', '" . $volume_po_loss_gain  . "', '" . $volume_terima_loss_gain  . "', '" . $jenis  . "', '" . $volume_loss_gain  . "', '" . $fileName . "', '" . $fileOriginName . "', '" . $ket_loss_gain . "', 1,NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "' 
                        )";
            $con->setQuery($sql2);
            $oke  = $oke && !$con->hasError();

            if ($oke) {
                $con->commit();
                $con->close();


                if ($isUpload) {
                    if (!file_exists($pathnya . '/' . $folder . '/')) mkdir($pathnya . '/' . $folder, 0777);

                    $tujuan  = $pathnya . '/' . $fileName;
                    $mantab  = move_uploaded_file($tempPhoto1, $tujuan);
                }

                header("location: " . BASE_URL_CLIENT . "/vendor-po-new.php");
                exit();
            } else {
                $con->rollBack();
                $con->clearError();
                $con->close();
                $flash->add("error", 'Maaf Data Gagal Disimpan...', BASE_REFERER);
            }
        }
    }
}

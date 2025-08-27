
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

$point                = htmlspecialchars($_POST["point"] == "" ? 0 : $_POST["point"], ENT_QUOTES);
$id_incentive        = htmlspecialchars($_POST["id_incentive"], ENT_QUOTES);
$volume_incentive    = htmlspecialchars($_POST["volume_incentive"], ENT_QUOTES);

$oke = true;
$con->beginTransaction();
$con->clearError();

$sql = "SELECT a.*, b.no_invoice, c.fullname as nama_marketing FROM pro_incentive a JOIN pro_invoice_admin b ON a.id_invoice=b.id_invoice JOIN acl_user c ON a.id_marketing=c.id_user WHERE a.id = '" . $id_incentive . "'";
$result = $con->getRecord($sql);

$point_lama = $result['point_incentive'];

$total_incentive = $point * $volume_incentive;

$sql = "UPDATE pro_incentive set point_incentive = '" . $point . "', total_incentive = '" . $total_incentive . "', is_edit = '1' WHERE id = '" . $id_incentive . "'";
$con->setQuery($sql);
$oke  = $oke && !$con->hasError();

if ($oke) {
    $ems1 = "SELECT email_user from acl_user where id_role = 23";
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
    $mail->Subject = "Perubahan Point Incentive  [" . date('d/m/Y H:i:s') . "]";
    $mail->msgHTML(paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " telah melakukan perubahan point pada incentive " . $result['nama_marketing'] . " dengan invoice " . $result['no_invoice'] . "<p>" . BASE_SERVER . "</p>" . "<p>" . "Point sebelum diubah : " . $point_lama . "" . "</p>" . "<p>" . "Point sesudah diubah : " . $point . "" . "</p>");
    $mail->send();

    $con->commit();
    $con->close();
    $data = [
        "pesan" => "Berhasil update point",
        "status" => 200
    ];
} else {
    $con->rollBack();
    $con->clearError();
    $con->close();
    $data = [
        "pesan" => "Gagal update point",
        "status" => 400
    ];
}
echo json_encode($data);

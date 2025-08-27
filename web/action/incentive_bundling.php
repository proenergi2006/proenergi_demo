
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

$tgl_pengajuan    = date('Y-m-d');
$year            = date('Y');
$periode        = htmlspecialchars($_POST["periode"], ENT_QUOTES);
$cabang            = htmlspecialchars($_POST["cabang"], ENT_QUOTES);
$jenis            = htmlspecialchars($_POST["jenis"], ENT_QUOTES);
$id_pengajuan    = paramDecrypt($_POST["id_pengajuan"]);
$id_incentive    = $_POST["id_incentive"];
$fullname         = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']);

$oke = true;
$con->beginTransaction();
$con->clearError();

if ($jenis == "hapus_pengajuan") {
    $ambil = "SELECT is_ceo FROM pro_pengajuan_incentive WHERE id = '" . $id_pengajuan . "'";
    $res_ambil = $con->getRecord($ambil);

    if ($res_ambil['is_ceo'] == 1) {
        $con->rollBack();
        $con->clearError();
        $con->close();
        $data = [
            "pesan" => "Pengajuan sudah di approve CEO, tidak bisa di hapus",
            "status" => false
        ];
        echo json_encode($data);
    } else {
        $ambil2 = "SELECT * FROM pro_bundle_incentive WHERE id_pengajuan = '" . $id_pengajuan . "'";
        $res_ambil2 = $con->getResult($ambil2);

        $sql_delete = "DELETE FROM pro_pengajuan_incentive WHERE id = '" . $id_pengajuan . "'";
        $con->setQuery($sql_delete);
        $oke  = $oke && !$con->hasError();

        $sql_delete2 = "DELETE FROM pro_bundle_incentive WHERE id_pengajuan = '" . $id_pengajuan . "'";
        $con->setQuery($sql_delete2);
        $oke  = $oke && !$con->hasError();

        foreach ($res_ambil2 as $key) {
            $sql_update = "UPDATE pro_incentive SET disposisi = '1' WHERE id = '" . $key['id_incentive'] . "' ";
            $con->setQuery($sql_update);
            $oke  = $oke && !$con->hasError();
        }

        if ($oke) {
            $con->commit();
            $con->close();
            $data = [
                "pesan" => "Pengajuan berhasil di hapus",
                "status" => true
            ];
            echo json_encode($data);
        } else {
            $con->rollBack();
            $con->clearError();
            $con->close();
            $data = [
                "pesan" => "Pengajuan gagal di hapus",
                "status" => false
            ];
            echo json_encode($data);
        }
    }
} elseif ($jenis == "approve_pengajuan") {
    // echo json_encode($id_pengajuan);
    $sql_update = "UPDATE pro_pengajuan_incentive SET is_ceo = 1, ceo_by = '" . $fullname . "', ceo_date = NOW() WHERE id = '" . $id_pengajuan . "'";
    $con->setQuery($sql_update);
    $oke = $oke && !$con->hasError();

    $ambil2 = "SELECT * FROM pro_bundle_incentive WHERE id_pengajuan = '" . $id_pengajuan . "'";
    $res_ambil2 = $con->getResult($ambil2);
    foreach ($res_ambil2 as $key) {
        $sql_update2 = "UPDATE pro_incentive SET disposisi = '3' WHERE id = '" . $key['id_incentive'] . "' ";
        $con->setQuery($sql_update2);
        $oke  = $oke && !$con->hasError();
    }

    if ($oke) {
        $con->commit();
        $con->close();
        $data = [
            "pesan" => "Pengajuan berhasil di approve",
            "status" => true
        ];
        echo json_encode($data);
    } else {
        $con->rollBack();
        $con->clearError();
        $con->close();
        $data = [
            "pesan" => "Pengajuan gagal di approve",
            "status" => false
        ];
        echo json_encode($data);
    }
} elseif ($jenis == "kirim_pengajuan") {
    // echo json_encode($id_pengajuan);
    $sql_update = "UPDATE pro_pengajuan_incentive SET disposisi = 1 WHERE id = '" . $id_pengajuan . "'";
    $con->setQuery($sql_update);
    $oke = $oke && !$con->hasError();

    $query = "SELECT * FROM pro_pengajuan_incentive WHERE id = '" . $id_pengajuan . "'";
    $row = $con->getRecord($query);

    $today = tgl_indo(date("Y-m-d")) . " " . date("H:i:s");

    switch ($row['periode_bulan']) {
        case '01':
            $nama_bulan = "Januari";
            break;
        case '02':
            $nama_bulan = "Februari";
            break;
        case '03':
            $nama_bulan = "Maret";
            break;
        case '04':
            $nama_bulan = "April";
            break;
        case '05':
            $nama_bulan = "Mei";
            break;
        case '06':
            $nama_bulan = "Juni";
            break;
        case '07':
            $nama_bulan = "Juli";
            break;
        case '08':
            $nama_bulan = "Agustus";
            break;
        case '09':
            $nama_bulan = "September";
            break;
        case '10':
            $nama_bulan = "Oktober";
            break;
        case '11':
            $nama_bulan = "November";
            break;
        case '12':
            $nama_bulan = "Desember";
            break;
    }

    if ($oke) {
        $ems1 = "SELECT distinct email_user FROM acl_user WHERE id_role='21' AND is_active = 1";
        $rms1 = $con->getResult($ems1);
        if ($rms1 && count($rms1) > 0) {
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
            $mail->Subject = "Pengajuan Incentive " . ', ' . $today . "";
            $mail->msgHTML($row['created_by'] . " telah mengajukan approval Incentive dengan nomor pengajuan : " . $row['nomor_pengajuan'] . " Periode " . $nama_bulan . " " . $row['periode_tahun']);
            $mail->send();
        }

        $con->commit();
        $con->close();
        $data = [
            "pesan" => "Pengajuan berhasil di kirim ke CEO",
            "status" => true
        ];
        echo json_encode($data);
    } else {
        $con->rollBack();
        $con->clearError();
        $con->close();
        $data = [
            "pesan" => "Pengajuan gagal di kirim ke CEO",
            "status" => false
        ];
        echo json_encode($data);
    }
} else {
    $query = "SELECT * FROM pro_pengajuan_incentive WHERE wilayah = '" . $cabang . "' AND periode_tahun = '" . $year . "' ORDER BY id DESC";
    $row = $con->getRecord($query);

    $query2 = "SELECT * FROM pro_master_cabang WHERE id_master='" . $cabang . "'";
    $res_cabang = $con->getRecord($query2);

    if ($row) {
        $incentive = $row['nomor_pengajuan'];
        $explode = explode("/", $incentive);
        $ambil_angka = intval($explode['4']);
        $angka = $ambil_angka + 1;
        $print = sprintf("%02s", $angka);
        $nomor_incentive = "PE/INC/" . $res_cabang['inisial_cabang'] . "/" . $year . "/" . $print;
    } else {
        $nomor_incentive = "PE/INC/" . $res_cabang['inisial_cabang'] . "/" . $year . "/01";
    }

    $exp = explode("-", $periode);
    $bulan = $exp[1];
    $tahun = $exp[0];

    $sql_cek = "SELECT * FROM pro_pengajuan_incentive WHERE periode_bulan = '" . $bulan . "' AND periode_tahun = '" . $tahun . "' AND wilayah = '" . $cabang . "'";
    $res_cek = $con->getRecord($sql_cek);

    if ($res_cek) {
        $con->rollBack();
        $con->clearError();
        $con->close();
        $flash->add("error", "Data Pengajuan pada Periode tersebut sudah ada.", BASE_REFERER);
    } else {
        $id_bm = 0;
        $id_sm = 0;
        $id_spv = 0;
        $persen_bm = 0;
        $persen_sm = 0;
        $persen_spv = 0;
        foreach ($res_cek2 as $data) {
            if ($data['jabatan'] == "BM") {
                $id_bm = $data['id'];
                $persen_bm = $data['persentase'];
            } elseif ($data['jabatan'] == "SM") {
                $id_sm = $data['id'];
                $persen_sm = $data['persentase'];
            } else {
                $id_spv = $data['id'];
                $persen_spv = $data['persentase'];
            }
        }

        $sql1 = "INSERT INTO pro_pengajuan_incentive(nomor_pengajuan, tgl_pengajuan, wilayah, id_bm, persen_bm, id_sm, persen_sm, id_spv, persen_spv, periode_bulan, periode_tahun, created_at, created_by) VALUES ('" . $nomor_incentive . "', '" . $tgl_pengajuan . "', '" . $cabang . "', '" . $id_bm . "', '" . $persen_bm . "', '" . $id_sm . "', '" . $persen_sm . "', '" . $id_spv . "', '" . $persen_spv . "', '" . $bulan . "', '" . $tahun . "', NOW(), '" . $fullname . "')";
        $last_id = $con->setQuery($sql1);
        $oke  = $oke && !$con->hasError();

        foreach ($id_incentive as $i => $key) {
            $sql2 = "INSERT INTO pro_bundle_incentive(id_pengajuan, id_incentive) VALUES ('" . $last_id . "', '" . $key . "')";
            $con->setQuery($sql2);
            $oke  = $oke && !$con->hasError();

            $sql_update = "UPDATE pro_incentive SET disposisi = '2' WHERE id = '" . $key . "'";
            $con->setQuery($sql_update);
            $oke  = $oke && !$con->hasError();
        }

        $url  = BASE_URL_CLIENT . "/list_pengajuan_incentive.php";
        if ($oke) {
            $con->commit();
            $con->close();
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

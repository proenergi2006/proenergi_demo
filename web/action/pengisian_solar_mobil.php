<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "mailgen", "htmlawed");

$auth    = new MyOtentikasi();
$con     = new Connection();
$flash    = new FlashAlerts;
$enk      = decode($_SERVER['REQUEST_URI']);
$act    = !isset($enk['act']) ? htmlspecialchars($_POST["act"], ENT_QUOTES) : $enk['act'];

$mobil = isset($_POST["mobil"]) && trim($_POST["mobil"]) !== '' ? htmlspecialchars($_POST["mobil"], ENT_QUOTES) : 0;
$truck = isset($_POST["truck"]) && trim($_POST["truck"]) !== '' ? htmlspecialchars($_POST["truck"], ENT_QUOTES) : 0;
$dispenser    = htmlspecialchars($_POST["dispenser"], ENT_QUOTES);
$liter_bbm    = htmlspecialchars(str_replace(array(","), array(""), $_POST["liter_bbm"]), ENT_QUOTES);
$keterangan    = htmlspecialchars($_POST["keterangan"], ENT_QUOTES);
$tujuan    = htmlspecialchars($_POST["tujuan"], ENT_QUOTES);
$driver    = htmlspecialchars($_POST["driver"], ENT_QUOTES);
$fullname     = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]);
$id_wilayah = paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]);
$max_size    = 2 * 1024 * 1024;
$allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'];
$pathfile    = $public_base_directory . '/files/uploaded_user/file_pengisian_solar_mobil_opr';
$today = date("Y-m-d");
$tahun_sekarang = date('Y');

$oke = true;
$con->beginTransaction();
$con->clearError();

if ($act == "add") {
    $sql_stok_total = "SELECT SUM(sisa_inven) AS total_stok FROM vw_terminal_inventory_receive WHERE id_terminal = '" . $dispenser . "' AND sisa_inven > 0";
    $row_total  = $con->getRecord($sql_stok_total);
    $total_stok = floatval($row_total['total_stok'] ?? 0);

    $sqlMobil = "SELECT CONCAT(nama_mobil,' - ', plat_mobil) as nama_mobil FROM pro_master_mobil WHERE id_mobil = '" . $mobil . "'";
    $getMobil = $con->getRecord($sqlMobil);

    $sqlTruck = "SELECT CONCAT(b.nama_transportir,' - ', a.nomor_plat) as nama_truck FROM pro_master_transportir_mobil a LEFT JOIN pro_master_transportir b ON a.id_transportir=b.id_master WHERE a.id_master = '" . $truck . "'";
    $getTruck = $con->getRecord($sqlTruck);

    if ($getMobil) {
        $nama_unit = $getMobil['nama_mobil'];
    } else {
        $nama_unit = $getTruck['nama_truck'];
    }

    if (floor($liter_bbm) == $liter_bbm) {
        $volume = number_format($liter_bbm, 0, '.', ','); // tampilkan tanpa desimal
    } else {
        $volume = number_format($liter_bbm, 4, '.', ','); // tampilkan 4 angka di belakang koma
    }

    if ($total_stok < $liter_bbm) {
        $con->rollBack();
        $con->clearError();
        $con->close();
        $flash->add("error", "Stock pada dispenser tidak cukup", BASE_REFERER);
    }

    if (isset($_FILES['lampiran']['name'][0])) {
        $originalName = $_FILES['lampiran']['name'];
        $tmpName = $_FILES['lampiran']['tmp_name'];
        $error = $_FILES['lampiran']['error'];
        $size = $_FILES['lampiran']['size'];
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        // Error upload
        if ($error !== UPLOAD_ERR_OK) {
            $con->rollBack();
            $con->clearError();
            $con->close();
            $flash->add("error", "Gagal upload file: $originalName (Error code: $error)<br>", BASE_REFERER);
        }

        // Validasi ekstensi
        if (!in_array($ext, $allowedTypes)) {
            $con->rollBack();
            $con->clearError();
            $con->close();
            $flash->add("error", "Tipe file tidak diperbolehkan: $originalName<br>", BASE_REFERER);
        }

        // Validasi ukuran file
        if ($size > $max_size) {
            $con->rollBack();
            $con->clearError();
            $con->close();
            $flash->add("error", "Ukuran file melebihi 2MB: $originalName<br>", BASE_REFERER);
        }

        $newFileName = "Lampiran-pengisian-solar-" . date("Ymd-His") . "-" . uniqid() . "." . $ext;
        $destination = $pathfile . '/' . $newFileName;

        if (!move_uploaded_file($tmpName, $destination)) {
            $con->rollBack();
            $con->clearError();
            $con->close();
            $flash->add("error", "Gagal memindahkan file: $originalName<br>", BASE_REFERER);
        }
    } else {
        $newFileName = NULL;
    }

    $sql = "INSERT into pro_pengisian_solar_mobil_opr(id_mobil, id_truck, id_terminal, id_wilayah, volume, driver, tujuan, keterangan, lampiran, disposisi, createdby, created_at) values ('" . $mobil . "', '" . $truck . "', '" . $dispenser . "', '" . $id_wilayah . "', '" . $liter_bbm . "', '" . $driver . "', '" . $tujuan . "', '" . $keterangan . "', '" . $newFileName . "', 0, '" . $fullname . "', NOW())";
    $id_pengisian = $con->setQuery($sql);
    $oke  = $oke && !$con->hasError();

    $sql_inven = "SELECT * FROM vw_terminal_inventory_receive WHERE id_terminal = '" . $dispenser . "' AND sisa_inven > 0 ORDER BY tgl_po_supplier ASC";
    $res_po = $con->getResult($sql_inven);

    $sisa_pemakaian = $liter_bbm;

    if (count($res_po) > 0) {

        foreach ($res_po as $key) {
            if ($sisa_pemakaian <= 0) break;

            $id_po_supplier = $key['id_po_supplier'];
            $id_po_receive = $key['id_po_receive'];
            $id_terminal = $key['id_terminal'];
            $qty_tersedia = floatval($key['sisa_inven']);
            $qty_potong = min($qty_tersedia, $sisa_pemakaian);

            $sql_insert_depot = "INSERT into new_pro_inventory_depot(id_datanya, id_jenis, id_produk, id_terminal, id_po_supplier, id_po_receive, tanggal_inven, out_inven_virtual, keterangan, created_time, created_ip, created_by, id_pengisian_solar) values ('pengisian_solar_mobil_opr', '11', '" . $key['id_produk'] . "', '" . $key['id_terminal'] . "', '" . $id_po_supplier . "', '" . $id_po_receive . "', '" . $today . "', $qty_potong, '" . $keterangan . "', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . $fullname . "', '" . $id_pengisian . "')";
            $con->setQuery($sql_insert_depot);
            $oke  = $oke && !$con->hasError();

            $sisa_pemakaian -= $qty_potong;
        }

        if ($oke) {
            $ems1 = "SELECT distinct email_user FROM acl_user WHERE id_wilayah ='" . $id_wilayah . "' AND id_role in(10) AND is_active=1";

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
                $mail->Subject = "Pengajuan Verifikasi Pengisian Solar [" . date("d/m/Y") . "]";
                $mail->msgHTML("" . $fullname . " mengajukan verifikasi pengisian solar unit " . $nama_unit . " dengan volume sebesar " . $volume . " Liter");
                $mail->send();
            }

            $con->commit();
            $con->close();
            header("location: " . BASE_URL_CLIENT . "/pengisian_solar_mobil.php");
            exit();
        } else {
            $con->rollBack();
            $con->clearError();
            $con->close();
            $flash->add("error", "Data gagal disimpan", BASE_REFERER);
        }
    } else {
        $con->rollBack();
        $con->clearError();
        $con->close();
        $flash->add("error", "Stock pada dispenser kosong", BASE_REFERER);
    }
} else if ($act == "hapus") {

    $param     = htmlspecialchars(paramDecrypt($_POST["param"]), ENT_QUOTES);
    $post     = explode("#|#", $param);
    $id        = htmlspecialchars($post[1], ENT_QUOTES);

    $sql = "SELECT * FROM pro_pengisian_solar_mobil_opr WHERE id = '" . $id . "'";
    $row = $con->getRecord($sql);

    if ($row['disposisi'] == 1) {
        $con->rollBack();
        $con->clearError();
        $con->close();
        $result = [
            "status"     => false,
            "pesan"     => "Pengajuan sudah di approve Admin Finance",
        ];
    } else {
        $pathDelete = $pathfile . "/" . $row['lampiran'];

        if (file_exists($pathDelete)) {
            if (is_file($pathDelete)) {
                if (unlink($pathDelete)) {
                    $oke = true;
                } else {
                    $con->rollBack();
                    $con->clearError();
                    $con->close();
                    $result = [
                        "status"     => false,
                        "pesan"     => "Gagal menghapus file",
                    ];
                }
            } else {
                $con->rollBack();
                $con->clearError();
                $con->close();
                $result = [
                    "status"     => false,
                    "pesan"     => "Path bukan file",
                ];
            }
        } else {
            $con->rollBack();
            $con->clearError();
            $con->close();
            $result = [
                "status"     => false,
                "pesan"     => "File tidak ditemukan untuk di hapus",
            ];
        }

        $query_delete = "DELETE FROM new_pro_inventory_depot WHERE id_pengisian_solar = '" . $id . "' AND id_jenis = '11'";
        $con->setQuery($query_delete);
        $oke  = $oke && !$con->hasError();

        $sql2 = "DELETE from pro_pengisian_solar_mobil_opr where id = '" . $id . "'";
        $con->setQuery($sql2);
        $oke  = $oke && !$con->hasError();

        if ($oke) {
            $con->commit();
            $con->close();
            $result = [
                "status"     => true,
                "pesan"     => "Berhasil di hapus",
            ];
        } else {
            $con->rollBack();
            $con->clearError();
            $con->close();
            $result = [
                "status"     => false,
                "pesan"     => "Gagal di hapus",
            ];
        }
    }
    echo json_encode($result);
} else if ($act == "approve") {

    $param     = htmlspecialchars(paramDecrypt($_POST["param"]), ENT_QUOTES);
    $post     = explode("#|#", $param);
    $id        = htmlspecialchars($post[1], ENT_QUOTES);

    $sql = "SELECT a.*, b.inisial_cabang FROM pro_pengisian_solar_mobil_opr a JOIN pro_master_cabang b ON a.id_wilayah=b.id_master WHERE a.id = '" . $id . "' ";
    $row = $con->getRecord($sql);
    $inisial_cabang = $row['inisial_cabang'];

    if ($row == "" || $row == NULL) {
        $con->rollBack();
        $con->clearError();
        $con->close();
        $result = [
            "status"     => false,
            "pesan"     => "Pengajuan tidak ditemukan",
        ];
    } else {
        $sql_stok_total = "SELECT SUM(sisa_inven) AS total_stok FROM vw_terminal_inventory_receive WHERE id_terminal = '" . $row['id_terminal'] . "' AND sisa_inven > 0";
        $row_total  = $con->getRecord($sql_stok_total);
        $total_stok = floatval($row_total['total_stok'] ?? 0);

        if ($total_stok < $row['volume']) {
            $con->rollBack();
            $con->clearError();
            $con->close();
            $result = [
                "status"     => false,
                "pesan"     => "Stock pada dispenser tidak cukup",
            ];
            echo json_encode($result);
        } else {
            $query_delete = "DELETE FROM new_pro_inventory_depot WHERE id_pengisian_solar = '" . $id . "' AND id_jenis = '11'";
            $con->setQuery($query_delete);
            $oke  = $oke && !$con->hasError();

            $sql_inven = "SELECT * FROM vw_terminal_inventory_receive WHERE id_terminal = '" . $row['id_terminal'] . "' AND sisa_inven > 0 ORDER BY tgl_po_supplier ASC";
            $res_po = $con->getResult($sql_inven);

            $sisa_pemakaian = $row['volume'];

            if (count($res_po) > 0) {

                foreach ($res_po as $key) {
                    if ($sisa_pemakaian <= 0) break;

                    $id_po_supplier = $key['id_po_supplier'];
                    $id_po_receive = $key['id_po_receive'];
                    $id_terminal = $key['id_terminal'];
                    $qty_tersedia = floatval($key['sisa_inven']);
                    $qty_potong = min($qty_tersedia, $sisa_pemakaian);

                    $sql_insert_depot = "INSERT into new_pro_inventory_depot(id_datanya, id_jenis, id_produk, id_terminal, id_po_supplier, id_po_receive, tanggal_inven, out_inven, keterangan, created_time, created_ip, created_by, id_pengisian_solar) values ('pengisian_solar_mobil_opr', '11', '" . $key['id_produk'] . "', '" . $key['id_terminal'] . "', '" . $id_po_supplier . "', '" . $id_po_receive . "', '" . $today . "', $qty_potong, '" . $row['keterangan'] . "', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . $fullname . "', '" . $row['id'] . "')";
                    $con->setQuery($sql_insert_depot);
                    $oke  = $oke && !$con->hasError();

                    $sisa_pemakaian -= $qty_potong;
                }

                $sql3 = "SELECT * FROM pro_pengisian_solar_mobil_opr WHERE id_wilayah = '" . $row['id_wilayah'] . "' AND nomor LIKE 'VCHR-BBM/$inisial_cabang/$tahun_sekarang/%' ORDER BY nomor DESC LIMIT 1";
                $row_nomor = $con->getRecord($sql3);

                if ($row_nomor) {
                    // Ambil angka urut terakhir
                    $last_number = (int)substr($row_nomor['nomor'], -3);
                    $new_number = str_pad($last_number + 1, 3, '0', STR_PAD_LEFT);
                } else {
                    // Tahun baru, mulai dari 001
                    $new_number = '001';
                }

                $no_vchr_baru = "VCHR-BBM/$inisial_cabang/$tahun_sekarang/$new_number";

                $sql2 = "UPDATE pro_pengisian_solar_mobil_opr SET nomor = '" . $no_vchr_baru . "', disposisi = 1, is_admin = 1, admin_pic = '" . $fullname . "', date_admin = NOW() WHERE id = '" . $id . "'";
                $con->setQuery($sql2);
                $oke  = $oke && !$con->hasError();

                if ($oke) {
                    $ems1 = "SELECT distinct email_user FROM acl_user WHERE id_wilayah ='" . $id_wilayah . "' AND id_role in(9,24) AND is_active=1";

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
                        $mail->Subject = "Verifikasi Pengisian Solar [" . date("d/m/Y") . "]";
                        $mail->msgHTML("" . $fullname . " telah melakukan verifikasi pengisian solar unit dengan nomor voucher [" . $no_vchr_baru . "]");
                        $mail->send();
                    }

                    $con->commit();
                    $con->close();
                    $result = [
                        "status"     => true,
                        "pesan"     => "Berhasil di approve",
                    ];
                } else {
                    $con->rollBack();
                    $con->clearError();
                    $con->close();
                    $result = [
                        "status"     => false,
                        "pesan"     => "Gagal di approve",
                    ];
                }
                echo json_encode($result);
            } else {
                $con->rollBack();
                $con->clearError();
                $con->close();
                $result = [
                    "status"     => false,
                    "pesan"     => "Stock pada dispenser kosong",
                ];
                echo json_encode($result);
            }
        }
    }
} else if ($act == "realisasi") {
    $id_pengisian = paramDecrypt($_POST["id_pengisian"]);
    $vol_realisasi    = htmlspecialchars(str_replace(array(","), array(""), $_POST["vol_realisasi"]), ENT_QUOTES);
    $tgl_realisasi    = htmlspecialchars($_POST["tgl_realisasi"], ENT_QUOTES);
    $driver_realisasi = htmlspecialchars($_POST["driver_realisasi"], ENT_QUOTES);
    $keterangan_realisasi = htmlspecialchars($_POST["keterangan_realisasi"], ENT_QUOTES);

    $sql = "SELECT * FROM pro_pengisian_solar_mobil_opr WHERE id = '" . $id_pengisian . "'";
    $data = $con->getRecord($sql);

    $sql_stok_total = "SELECT SUM(sisa_inven) AS total_stok FROM vw_terminal_inventory_receive WHERE id_terminal = '" . $data['id_terminal'] . "' AND sisa_inven > 0";
    $row_total  = $con->getRecord($sql_stok_total);
    $total_stok = floatval($row_total['total_stok'] ?? 0);

    if ($total_stok < $vol_realisasi) {
        $con->rollBack();
        $con->clearError();
        $con->close();
        $flash->add("error", "Stock pada dispenser tidak cukup", BASE_REFERER);
    }

    if (isset($_FILES['lampiran']['name'][0])) {
        $originalName = $_FILES['lampiran']['name'];
        $tmpName = $_FILES['lampiran']['tmp_name'];
        $error = $_FILES['lampiran']['error'];
        $size = $_FILES['lampiran']['size'];
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        // Error upload
        if ($error !== UPLOAD_ERR_OK) {
            $con->rollBack();
            $con->clearError();
            $con->close();
            $flash->add("error", "Gagal upload file: $originalName (Error code: $error)<br>", BASE_REFERER);
        }

        // Validasi ekstensi
        if (!in_array($ext, $allowedTypes)) {
            $con->rollBack();
            $con->clearError();
            $con->close();
            $flash->add("error", "Tipe file tidak diperbolehkan: $originalName<br>", BASE_REFERER);
        }

        // Validasi ukuran file
        if ($size > $max_size) {
            $con->rollBack();
            $con->clearError();
            $con->close();
            $flash->add("error", "Ukuran file melebihi 2MB: $originalName<br>", BASE_REFERER);
        }

        $newFileName = "Lampiran-realisasi-pengisian-solar-" . date("Ymd-His") . "-" . uniqid() . "." . $ext;
        $destination = $pathfile . '/' . $newFileName;

        if (!move_uploaded_file($tmpName, $destination)) {
            $con->rollBack();
            $con->clearError();
            $con->close();
            $flash->add("error", "Gagal memindahkan file: $originalName<br>", BASE_REFERER);
        }
    }

    $query_delete = "DELETE FROM new_pro_inventory_depot WHERE id_pengisian_solar = '" . $data['id'] . "' AND id_jenis = '11'";
    $con->setQuery($query_delete);
    $oke  = $oke && !$con->hasError();

    $sql_inven = "SELECT * FROM vw_terminal_inventory_receive WHERE id_terminal = '" . $data['id_terminal'] . "' AND sisa_inven > 0 ORDER BY tgl_po_supplier ASC";
    $res_po = $con->getResult($sql_inven);

    $sisa_pemakaian = $vol_realisasi;

    if (count($res_po) > 0) {

        foreach ($res_po as $key) {
            if ($sisa_pemakaian <= 0) break;

            $id_po_supplier = $key['id_po_supplier'];
            $id_po_receive = $key['id_po_receive'];
            $id_terminal = $key['id_terminal'];
            $qty_tersedia = floatval($key['sisa_inven']);
            $qty_potong = min($qty_tersedia, $sisa_pemakaian);

            $sql_insert_depot = "INSERT into new_pro_inventory_depot(id_datanya, id_jenis, id_produk, id_terminal, id_po_supplier, id_po_receive, tanggal_inven, out_inven_virtual, keterangan, created_time, created_ip, created_by, id_pengisian_solar) values ('pengisian_solar_mobil_opr', '11', '" . $key['id_produk'] . "', '" . $key['id_terminal'] . "', '" . $id_po_supplier . "', '" . $id_po_receive . "', '" . $today . "', $qty_potong, '" . $data['keterangan'] . "', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . $fullname . "', '" . $data['id'] . "')";
            $con->setQuery($sql_insert_depot);
            $oke  = $oke && !$con->hasError();

            $sisa_pemakaian -= $qty_potong;
        }

        $sql2 = "UPDATE pro_pengisian_solar_mobil_opr SET volume_realisasi = '" . $vol_realisasi . "', tgl_realisasi = '" . tgl_db($tgl_realisasi) . "', lampiran_realisasi = '" . $newFileName . "', driver_realisasi = '" . $driver_realisasi . "', keterangan_realisasi = '" . $keterangan_realisasi . "' WHERE id = '" . $data['id'] . "'";
        $con->setQuery($sql2);
        $oke  = $oke && !$con->hasError();

        if ($oke) {
            $ems1 = "SELECT distinct email_user FROM acl_user WHERE id_wilayah ='" . $id_wilayah . "' AND id_role in(10) AND is_active=1";

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
                $mail->Subject = "Pengajuan Verifikasi Realisasi Pengisian Solar [" . date("d/m/Y") . "]";
                $mail->msgHTML("" . $fullname . " mengajukan verifikasi realisasi pengisian solar unit dengan nomor voucher [" . $data['nomor'] . "]");
                $mail->send();
            }

            $con->commit();
            $con->close();
            header("location: " . BASE_URL_CLIENT . "/pengisian_solar_mobil.php");
            exit();
        } else {
            $con->rollBack();
            $con->clearError();
            $con->close();
            $flash->add("error", "Data realisasi gagal disimpan", BASE_REFERER);
        }
    } else {
        $con->rollBack();
        $con->clearError();
        $con->close();
        $flash->add("error", "Stock pada dispenser kosong", BASE_REFERER);
    }
} else if ($act == "cancel") {
    $param     = htmlspecialchars(paramDecrypt($_POST["param"]), ENT_QUOTES);
    $post     = explode("#|#", $param);
    $id        = htmlspecialchars($post[1], ENT_QUOTES);

    $sql = "UPDATE pro_pengisian_solar_mobil_opr SET disposisi = 2 WHERE id = '" . $id . "'";
    $con->setQuery($sql);

    $query_delete = "DELETE FROM new_pro_inventory_depot WHERE id_pengisian_solar = '" . $id . "' AND id_jenis = '11'";
    $con->setQuery($query_delete);
    $oke  = $oke && !$con->hasError();

    if ($oke) {
        $con->commit();
        $con->close();
        $result = [
            "status"     => true,
            "pesan"     => "Berhasil di cancel",
        ];
    } else {
        $con->rollBack();
        $con->clearError();
        $con->close();
        $result = [
            "status"     => false,
            "pesan"     => "Gagal di cancel",
        ];
    }
    echo json_encode($result);
} else if ($act == "update") {
    $id = paramDecrypt($_POST["idr"]);

    $sql = "SELECT * FROM pro_pengisian_solar_mobil_opr WHERE id = '" . $id . "'";
    $row = $con->getRecord($sql);

    if ($row['disposisi'] == 1) {
        $con->rollBack();
        $con->clearError();
        $con->close();
        $result = [
            "status"     => false,
            "pesan"     => "Pengajuan sudah di approve Admin Finance",
        ];
    } else {
        $query_delete = "DELETE FROM new_pro_inventory_depot WHERE id_pengisian_solar = '" . $id . "' AND id_jenis = '11'";
        $con->setQuery($query_delete);
        $oke  = $oke && !$con->hasError();

        $sql_stok_total = "SELECT SUM(sisa_inven) AS total_stok FROM vw_terminal_inventory_receive WHERE id_terminal = '" . $dispenser . "' AND sisa_inven > 0";
        $row_total  = $con->getRecord($sql_stok_total);
        $total_stok = floatval($row_total['total_stok'] ?? 0);

        if ($total_stok < $liter_bbm) {
            $con->rollBack();
            $con->clearError();
            $con->close();
            $flash->add("error", "Stock pada dispenser tidak cukup", BASE_REFERER);
        }

        $sql_inven = "SELECT * FROM vw_terminal_inventory_receive WHERE id_terminal = '" . $dispenser . "' AND sisa_inven > 0 ORDER BY tgl_po_supplier ASC";
        $res_po = $con->getResult($sql_inven);

        $sisa_pemakaian = $liter_bbm;

        if (count($res_po) > 0) {

            foreach ($res_po as $key) {
                if ($sisa_pemakaian <= 0) break;

                $id_po_supplier = $key['id_po_supplier'];
                $id_po_receive = $key['id_po_receive'];
                $id_terminal = $key['id_terminal'];
                $qty_tersedia = floatval($key['sisa_inven']);
                $qty_potong = min($qty_tersedia, $sisa_pemakaian);

                $sql_insert_depot = "INSERT into new_pro_inventory_depot(id_datanya, id_jenis, id_produk, id_terminal, id_po_supplier, id_po_receive, tanggal_inven, out_inven_virtual, keterangan, created_time, created_ip, created_by, id_pengisian_solar) values ('pengisian_solar_mobil_opr', '11', '" . $key['id_produk'] . "', '" . $key['id_terminal'] . "', '" . $id_po_supplier . "', '" . $id_po_receive . "', '" . $today . "', $qty_potong, '" . $data['keterangan'] . "', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . $fullname . "', '" . $id . "')";
                $con->setQuery($sql_insert_depot);
                $oke  = $oke && !$con->hasError();

                $sisa_pemakaian -= $qty_potong;
            }

            if (isset($_FILES['lampiran']['name'][0])) {
                $pathDelete = $pathfile . "/" . $row['lampiran'];

                if (file_exists($pathDelete)) {
                    if (is_file($pathDelete)) {
                        if (unlink($pathDelete)) {
                            $oke = true;
                        } else {
                            $con->rollBack();
                            $con->clearError();
                            $con->close();
                            $result = [
                                "status"     => false,
                                "pesan"     => "Gagal menghapus file",
                            ];
                        }
                    } else {
                        $con->rollBack();
                        $con->clearError();
                        $con->close();
                        $result = [
                            "status"     => false,
                            "pesan"     => "Path bukan file",
                        ];
                    }
                } else {
                    $con->rollBack();
                    $con->clearError();
                    $con->close();
                    $result = [
                        "status"     => false,
                        "pesan"     => "File tidak ditemukan untuk di hapus",
                    ];
                }

                $originalName = $_FILES['lampiran']['name'];
                $tmpName = $_FILES['lampiran']['tmp_name'];
                $error = $_FILES['lampiran']['error'];
                $size = $_FILES['lampiran']['size'];
                $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                // Error upload
                if ($error !== UPLOAD_ERR_OK) {
                    $con->rollBack();
                    $con->clearError();
                    $con->close();
                    $flash->add("error", "Gagal upload file: $originalName (Error code: $error)<br>", BASE_REFERER);
                }

                // Validasi ekstensi
                if (!in_array($ext, $allowedTypes)) {
                    $con->rollBack();
                    $con->clearError();
                    $con->close();
                    $flash->add("error", "Tipe file tidak diperbolehkan: $originalName<br>", BASE_REFERER);
                }

                // Validasi ukuran file
                if ($size > $max_size) {
                    $con->rollBack();
                    $con->clearError();
                    $con->close();
                    $flash->add("error", "Ukuran file melebihi 2MB: $originalName<br>", BASE_REFERER);
                }

                $newFileName = "Lampiran-pengisian-solar-" . date("Ymd-His") . "-" . uniqid() . "." . $ext;
                $destination = $pathfile . '/' . $newFileName;

                if (!move_uploaded_file($tmpName, $destination)) {
                    $con->rollBack();
                    $con->clearError();
                    $con->close();
                    $flash->add("error", "Gagal memindahkan file: $originalName<br>", BASE_REFERER);
                }

                $sql2 = "UPDATE pro_pengisian_solar_mobil_opr SET id_mobil = '" . $mobil . "', id_truck = '" . $truck . "', id_terminal = '" . $dispenser . "', volume = " . $liter_bbm . ", driver = '" . $driver . "', tujuan = '" . $tujuan . "', keterangan = '" . $keterangan . "', lampiran = '" . $newFileName . "', updatedby = '" . $fullname . "', updated_at = NOW() WHERE id = '" . $id . "'";
                $con->setQuery($sql2);
                $oke  = $oke && !$con->hasError();
            } else {
                $sql2 = "UPDATE pro_pengisian_solar_mobil_opr SET id_mobil = '" . $mobil . "', id_truck = '" . $truck . "', id_terminal = '" . $dispenser . "', volume = " . $liter_bbm . ", driver = '" . $driver . "', tujuan = '" . $tujuan . "', keterangan = '" . $keterangan . "', updatedby = '" . $fullname . "', updated_at = NOW() WHERE id = '" . $id . "'";
                $con->setQuery($sql2);
                $oke  = $oke && !$con->hasError();
            }


            if ($oke) {
                $con->commit();
                $con->close();
                header("location: " . BASE_URL_CLIENT . "/pengisian_solar_mobil.php");
                exit();
            } else {
                $con->rollBack();
                $con->clearError();
                $con->close();
                $flash->add("error", "Update gagal", BASE_REFERER);
            }
        } else {
            $con->rollBack();
            $con->clearError();
            $con->close();
            $flash->add("error", "Stock pada dispenser kosong", BASE_REFERER);
        }
    }
} else if ($act == "approve_realisasi") {
    $param     = htmlspecialchars(paramDecrypt($_POST["param"]), ENT_QUOTES);
    $post     = explode("#|#", $param);
    $id        = htmlspecialchars($post[1], ENT_QUOTES);

    $sql = "SELECT a.*, b.inisial_cabang FROM pro_pengisian_solar_mobil_opr a JOIN pro_master_cabang b ON a.id_wilayah=b.id_master WHERE a.id = '" . $id . "' ";
    $row = $con->getRecord($sql);
    $inisial_cabang = $row['inisial_cabang'];

    if ($row == "" || $row == NULL) {
        $con->rollBack();
        $con->clearError();
        $con->close();
        $result = [
            "status"     => false,
            "pesan"     => "Pengajuan tidak ditemukan",
        ];
    } else {
        $sql_stok_total = "SELECT SUM(sisa_inven) AS total_stok FROM vw_terminal_inventory_receive WHERE id_terminal = '" . $row['id_terminal'] . "' AND sisa_inven > 0";
        $row_total  = $con->getRecord($sql_stok_total);
        $total_stok = floatval($row_total['total_stok'] ?? 0);

        if ($total_stok < $row['volume']) {
            $con->rollBack();
            $con->clearError();
            $con->close();
            $result = [
                "status"     => false,
                "pesan"     => "Stock pada dispenser tidak cukup",
            ];
            echo json_encode($result);
        } else {
            $query_delete = "DELETE FROM new_pro_inventory_depot WHERE id_pengisian_solar = '" . $id . "' AND id_jenis = '11'";
            $con->setQuery($query_delete);
            $oke  = $oke && !$con->hasError();

            $sql_inven = "SELECT * FROM vw_terminal_inventory_receive WHERE id_terminal = '" . $row['id_terminal'] . "' AND sisa_inven > 0 ORDER BY tgl_po_supplier ASC";
            $res_po = $con->getResult($sql_inven);

            $sisa_pemakaian = $row['volume_realisasi'];

            if (count($res_po) > 0) {

                foreach ($res_po as $key) {
                    if ($sisa_pemakaian <= 0) break;

                    $id_po_supplier = $key['id_po_supplier'];
                    $id_po_receive = $key['id_po_receive'];
                    $id_terminal = $key['id_terminal'];
                    $qty_tersedia = floatval($key['sisa_inven']);
                    $qty_potong = min($qty_tersedia, $sisa_pemakaian);

                    $sql_insert_depot = "INSERT into new_pro_inventory_depot(id_datanya, id_jenis, id_produk, id_terminal, id_po_supplier, id_po_receive, tanggal_inven, out_inven, keterangan, created_time, created_ip, created_by, id_pengisian_solar) values ('pengisian_solar_mobil_opr', '11', '" . $key['id_produk'] . "', '" . $key['id_terminal'] . "', '" . $id_po_supplier . "', '" . $id_po_receive . "', '" . $today . "', $qty_potong, '" . $row['keterangan'] . "', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . $fullname . "', '" . $row['id'] . "')";
                    $con->setQuery($sql_insert_depot);
                    $oke  = $oke && !$con->hasError();

                    $sisa_pemakaian -= $qty_potong;
                }

                $sql2 = "UPDATE pro_pengisian_solar_mobil_opr SET is_admin_realisasi = 1, admin_pic_realisasi = '" . $fullname . "', date_admin_realisasi = NOW() WHERE id = '" . $id . "'";
                $con->setQuery($sql2);
                $oke  = $oke && !$con->hasError();

                if ($oke) {
                    $ems1 = "SELECT distinct email_user FROM acl_user WHERE id_wilayah ='" . $id_wilayah . "' AND id_role in(9,24) AND is_active=1";

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
                        $mail->Subject = "Verifikasi Realisasi Pengisian Solar [" . date("d/m/Y") . "]";
                        $mail->msgHTML("" . $fullname . " telah melakukan verifikasi realisasi pengisian solar unit dengan nomor voucher [" . $row['nomor'] . "]");
                        $mail->send();
                    }

                    $con->commit();
                    $con->close();
                    $result = [
                        "status"     => true,
                        "pesan"     => "Berhasil di approve",
                    ];
                } else {
                    $con->rollBack();
                    $con->clearError();
                    $con->close();
                    $result = [
                        "status"     => false,
                        "pesan"     => "Gagal di approve",
                    ];
                }
                echo json_encode($result);
            } else {
                $con->rollBack();
                $con->clearError();
                $con->close();
                $result = [
                    "status"     => false,
                    "pesan"     => "Stock pada dispenser kosong",
                ];
                echo json_encode($result);
            }
        }
    }
}

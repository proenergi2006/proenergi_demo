<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "pdfgen");

$enk    = decode($_SERVER['REQUEST_URI']);
$con    = new Connection();
$printe = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]) . " " . date("d/m/Y H:i:s") . " WIB";
$role   = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$wilayah = paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]);
$fullname = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]);
$id = paramDecrypt(isset($_POST["id"]) ? htmlspecialchars($_POST["id"], ENT_QUOTES) : NULL);
$id_realisasi = paramDecrypt(isset($_POST["id_realisasi"]) ? htmlspecialchars($_POST["id_realisasi"], ENT_QUOTES) : NULL);
$id_dsd = paramDecrypt(isset($_POST["id_dsd"]) ? htmlspecialchars($_POST["id_dsd"], ENT_QUOTES) : NULL);
$jenis = isset($_POST["jenis"]) ? htmlspecialchars($_POST["jenis"], ENT_QUOTES) : NULL;

$tanggal_bpuj       = date("Y-m-d H:i:s");
$tanggal            = htmlspecialchars($_POST["tgl_kirim_bpuj"], ENT_QUOTES);
$tgl_kirim_bpuj     = date("Y-m-d", strtotime(tgl_db($tanggal)));
$tanggal_realisasi  = htmlspecialchars($_POST["tanggal_realisasi"], ENT_QUOTES);
$tgl_kirim_realisasi = date("Y-m-d", strtotime(tgl_db($tanggal_realisasi)));

$tgl_pengisian      = htmlspecialchars($_POST["tgl_pengisian"], ENT_QUOTES);
if ($tgl_pengisian != "") {
    $tgl_pengisian_fix = '"' .  tgl_db($tgl_pengisian) . '"';
} else {
    $tgl_pengisian_fix = "NULL";
}

$tgl_pengisian_tambahan = htmlspecialchars($_POST["tgl_pengisian2"], ENT_QUOTES);
if ($tgl_pengisian_tambahan != "") {
    $tgl_pengisian_tambahan_fix = '"' .  tgl_db($tgl_pengisian_tambahan) . '"';
} else {
    $tgl_pengisian_tambahan_fix = "NULL";
}

$tgl_pengisian_tambahan3 = htmlspecialchars($_POST["tgl_pengisian3"], ENT_QUOTES);
if ($tgl_pengisian_tambahan3 != "") {
    $tgl_pengisian_tambahan_fix3 = '"' .  tgl_db($tgl_pengisian_tambahan3) . '"';
} else {
    $tgl_pengisian_tambahan_fix3 = "NULL";
}

$nama_customer      = isset($_POST["nama_customer"]) ? htmlspecialchars($_POST["nama_customer"], ENT_QUOTES) : NULL;
$no_unit            = isset($_POST["no_unit"]) ? htmlspecialchars($_POST["no_unit"], ENT_QUOTES) : NULL;
$nama_driver        = isset($_POST["nama_driver"]) ? htmlspecialchars($_POST["nama_driver"], ENT_QUOTES) : NULL;
$status_driver      = isset($_POST["status_driver"]) ? htmlspecialchars($_POST["status_driver"], ENT_QUOTES) : NULL;
$jarak_km_lcr       = isset($_POST["jarak_km_lcr"]) ? htmlspecialchars($_POST["jarak_km_lcr"], ENT_QUOTES) : NULL;
$jarak_real         = isset($_POST["jarak_real"]) ? htmlspecialchars($_POST["jarak_real"], ENT_QUOTES) : NULL;
$jasa               = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["jasa"]), ENT_QUOTES);
$jenis_tangki       = isset($_POST["max_cap"]) ? htmlspecialchars($_POST["max_cap"], ENT_QUOTES) : NULL;
$pengisian_bbm      = isset($_POST["pengisian_bbm"]) ? htmlspecialchars($_POST["pengisian_bbm"], ENT_QUOTES) : NULL;
$exp                = explode("||", $pengisian_bbm);
$dispenser          = $exp[1];
if ($dispenser == "NULL") {
    $dispenser_fix = 0;
} else {
    $dispenser_fix = $dispenser;
}

$pengisian_bbm2      = isset($_POST["pengisian_bbm2"]) ? htmlspecialchars($_POST["pengisian_bbm2"], ENT_QUOTES) : NULL;
if ($pengisian_bbm2 == "" || $pengisian_bbm2 == NULL) {
    $dispenser_fix2 = 0;
} else {
    $exp2                = explode("||", $pengisian_bbm2);
    $dispenser2          = $exp2[1];
    if ($dispenser2 == "NULL") {
        $dispenser_fix2 = 0;
    } else {
        $dispenser_fix2 = $dispenser2;
    }
}

$pengisian_bbm3      = isset($_POST["pengisian_bbm3"]) ? htmlspecialchars($_POST["pengisian_bbm3"], ENT_QUOTES) : NULL;
if ($pengisian_bbm3 == "" || $pengisian_bbm3 == NULL) {
    $dispenser_fix3 = 0;
} else {
    $exp3                = explode("||", $pengisian_bbm3);
    $dispenser3          = $exp3[1];
    if ($dispenser3 == "NULL") {
        $dispenser_fix3 = 0;
    } else {
        $dispenser_fix3 = $dispenser3;
    }
}
$liter_bbm          = isset($_POST["liter_bbm"]) ? htmlspecialchars(str_replace(array(","), array(""), $_POST["liter_bbm"])) : 0;
$liter_bbm2          = isset($_POST["liter_bbm2"]) ? htmlspecialchars(str_replace(array(","), array(""), $_POST["liter_bbm2"])) : NULL;
if ($liter_bbm2 == NULL) {
    $liter_bbm_fix2 = 0;
} else {
    $liter_bbm_fix2 = $liter_bbm2;
}
$liter_bbm3          = isset($_POST["liter_bbm3"]) ? htmlspecialchars(str_replace(array(","), array(""), $_POST["liter_bbm3"])) : NULL;
if ($liter_bbm3 == NULL) {
    $liter_bbm_fix3 = 0;
} else {
    $liter_bbm_fix3 = $liter_bbm3;
}
$perbandingan_bbm   = isset($_POST["perbandingan_bbm"]) ? htmlspecialchars($_POST["perbandingan_bbm"], ENT_QUOTES) : NULL;
$total_jasa         = isset($_POST["total_jasa2"]) ? htmlspecialchars($_POST["total_jasa2"], ENT_QUOTES) : NULL;
$total_bbm          = isset($_POST["total_bbm2"]) ? htmlspecialchars($_POST["total_bbm2"], ENT_QUOTES) : NULL;
$uang_makan         = isset($_POST["uang_makan2"]) ? htmlspecialchars($_POST["uang_makan2"], ENT_QUOTES) : NULL;
$kernet             = isset($_POST["kernet2"]) ? htmlspecialchars($_POST["kernet2"], ENT_QUOTES) : NULL;
$tol                = isset($_POST["tol2"]) ? htmlspecialchars($_POST["tol2"], ENT_QUOTES) : NULL;
$demmurade          = isset($_POST["demmurade2"]) ? htmlspecialchars($_POST["demmurade2"], ENT_QUOTES) : NULL;
$koordinasi         = isset($_POST["koordinasi2"]) ? htmlspecialchars($_POST["koordinasi2"], ENT_QUOTES) : NULL;
$multidrop          = isset($_POST["multidrop2"]) ? htmlspecialchars($_POST["multidrop2"], ENT_QUOTES) : NULL;
if ($multidrop == NULL) {
    $multidrop_fix = 0;
} else {
    $multidrop_fix = $multidrop;
}
$biaya_lain         = isset($_POST["biaya_lain2"]) ? htmlspecialchars($_POST["biaya_lain2"], ENT_QUOTES) : NULL;
$catatan            = isset($_POST["catatan"]) ? htmlspecialchars($_POST["catatan"], ENT_QUOTES) : NULL;
$catatan_realisasi  = isset($_POST["catatan_realisasi"]) ? htmlspecialchars($_POST["catatan_realisasi"], ENT_QUOTES) : NULL;
$catatan_edit  = isset($_POST["catatan_edit"]) ? htmlspecialchars($_POST["catatan_edit"], ENT_QUOTES) : NULL;
$biaya_penyebrangan = isset($_POST["biaya_penyebrangan2"]) ? htmlspecialchars($_POST["biaya_penyebrangan2"], ENT_QUOTES) : NULL;
// $biaya_perjalanan   = isset($_POST["biaya_perjalanan2"]) ? htmlspecialchars($_POST["biaya_perjalanan2"], ENT_QUOTES) : NULL;
$total_bpuj         = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["total"]), ENT_QUOTES);
$bayar              = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["bayar"]), ENT_QUOTES);

$uang_makan_tambahan  = $_POST["uang_makan_tambahan"];
$uang_kernet_tambahan  = $_POST["kernet_tambahan"];

$oke = true;
$con->beginTransaction();
$con->clearError();

$master_bpuj  = "SELECT * FROM pro_master_bpuj";
$row_master   = $con->getRecord($master_bpuj);

// $data = [
//     "status" => true,
//     "pesan"  => $id,
// ];
// echo json_encode($data);

if ($jenis == "Kirim") {
    // echo json_encode($id);
    $query = "UPDATE pro_bpuj SET disposisi_bpuj='1' WHERE id_bpuj='" . $id . "'";
    $con->setQuery($query);
    $oke  = $oke && !$con->hasError();

    if ($oke) {
        $con->commit();
        $con->close();
        $data = [
            "status" => true,
            "pesan"  => "BPUJ berhasil dikirim",
        ];
        echo json_encode($data);
    } else {
        $con->rollBack();
        $con->clearError();
        $con->close();
        $data = [
            "status" => false,
            "pesan"  => "BPUJ gagal dikirim",
        ];
        echo json_encode($data);
    }
} elseif ($jenis == "Kembalikan") {
    $query = '
        UPDATE pro_bpuj 
        SET 
            `disposisi_bpuj` = "0"
        WHERE 
            `id_bpuj` = "' . $id . '"
        ';
    $con->setQuery($query);
    $oke  = $oke && !$con->hasError();

    if ($oke) {
        $con->commit();
        $con->close();
        $data = [
            "status" => true,
            "pesan"  => "BPUJ berhasil dikembalikan",
        ];
        echo json_encode($data);
    } else {
        $con->rollBack();
        $con->clearError();
        $con->close();
        $data = [
            "status" => false,
            "pesan"  => "BPUJ gagal dikembalikan",
        ];
        echo json_encode($data);
    }
} elseif ($jenis == "Approve") {
    $query_realisasi   = "SELECT * FROM pro_bpuj_realisasi WHERE id = '" . $id_realisasi . "'";
    $row_realisasi     = $con->getRecord($query_realisasi);

    $query_bpuj   = "SELECT * FROM pro_bpuj WHERE id_bpuj = '" . $row_realisasi['id_bpuj'] . "'";
    $row          = $con->getRecord($query_bpuj);

    $query = '
        UPDATE pro_bpuj_realisasi
        SET 
            `disposisi_realisasi` = "1",
            `catatan`             = "' . $catatan_edit . '",
            `approved_by`         = "' . $fullname . '",
            `approved_date`       = "' . date("Y-m-d H:i:s") . '"
        WHERE 
            `id` = "' . $id_realisasi . '"';
    $con->setQuery($query);
    $oke  = $oke && !$con->hasError();

    $query_delete = "DELETE FROM new_pro_inventory_depot WHERE id_dsd = '" . $row['id_dsd'] . "' AND id_jenis = '10'";
    $con->setQuery($query_delete);
    $oke  = $oke && !$con->hasError();

    $pengisian = [
        ['dispenser_id' => $row_realisasi['dispenser'], 'volume' => floatval($row_realisasi['liter_bbm'])],
        ['dispenser_id' => $row_realisasi['dispenser_tambahan'], 'volume' => floatval($row_realisasi['liter_bbm_tambahan'])],
        ['dispenser_id' => $row_realisasi['dispenser_tambahan2'], 'volume' => floatval($row_realisasi['liter_bbm_tambahan2'])],
    ];

    foreach ($pengisian as $item) {
        $id_dispenser = $item['dispenser_id'];
        $volume_input = $item['volume'];

        // Skip jika tidak ada pengisian
        if (!$id_dispenser || $volume_input <= 0) continue;

        $sql_stok_total = "SELECT SUM(sisa_inven) AS total_stok FROM vw_terminal_inventory_receive WHERE id_terminal = '" . $id_dispenser . "' AND sisa_inven > 0";
        $row_total  = $con->getRecord($sql_stok_total);
        $total_stok = floatval($row_total['total_stok'] ?? 0);

        if ($total_stok < $volume_input) {
            $con->rollBack();
            $con->clearError();
            $con->close();
            $result = [
                "status"     => false,
                "pesan"     => "Stock pada dispenser tidak cukup",
            ];
            echo json_encode($result);
            exit();
        }

        $sisa_pemakaian = $volume_input;

        $sql_inven = "SELECT * FROM vw_terminal_inventory_receive WHERE id_terminal = '" . $id_dispenser . "' AND sisa_inven > 0 ORDER BY tgl_po_supplier ASC";
        $res_po = $con->getResult($sql_inven);

        if (count($res_po) > 0) {
            foreach ($res_po as $key) {

                if ($sisa_pemakaian <= 0) break;

                $id_po_supplier = $key['id_po_supplier'];
                $id_po_receive = $key['id_po_receive'];
                $qty_tersedia = floatval($key['sisa_inven']);
                $qty_potong = min($qty_tersedia, $sisa_pemakaian);

                $query_stock = '
                INSERT INTO new_pro_inventory_depot 
                SET 
                `id_datanya`           = "dispenser",
                `id_jenis`             = "10",
                `id_produk`            = "' . $key['id_produk'] . '",
                `id_terminal`          = "' . $id_dispenser . '",
                `id_po_supplier`       = "' . $id_po_supplier . '",
                `id_po_receive`        = "' . $id_po_receive . '",
                `tanggal_inven`        = "' . $row_realisasi['tanggal_realisasi'] . '",
                `out_inven`            = "' . $qty_potong . '",
                `keterangan`           = "Pengurangan Stock dari BPUJ",
                `created_time`         = "' . date("Y-m-d H:i:s") . '",
                `created_by`           = "' . $fullname . '",
                `created_ip`           = "' . $_SERVER['REMOTE_ADDR'] . '",
                `lastupdate_time`      = "' . date("Y-m-d H:i:s") . '",
                `lastupdate_by`        = "' . $fullname . '",
                `lastupdate_ip`        = "' . $_SERVER['REMOTE_ADDR'] . '",
                `id_dsd`               = "' . $row['id_dsd'] . '"';
                $con->setQuery($query_stock);
                $oke  = $oke && !$con->hasError();

                $sisa_pemakaian -= $qty_potong;
            }
        }
    }

    if ($oke) {
        $con->commit();
        $con->close();
        $data = [
            "status" => true,
            "pesan"  => "Realisasi berhasil di Approve",
        ];
        echo json_encode($data);
    } else {
        $con->rollBack();
        $con->clearError();
        $con->close();
        $data = [
            "status" => false,
            "pesan"  => "Realisasi gagal di Approve",
        ];
        echo json_encode($data);
    }
} elseif ($jenis == "realisasi") {
    // echo json_encode($jenis);
    $query_bpuj   = "SELECT * FROM pro_bpuj WHERE id_bpuj = '" . $id . "'";
    $row          = $con->getRecord($query_bpuj);

    $query = '
        INSERT INTO pro_bpuj_realisasi
        SET 
            `id_bpuj`                 = "' . $id . '",
            `no_unit`                 = "' . $no_unit . '",
            `nama_driver`             = "' . $nama_driver . '",
            `status_driver`           = "' . $status_driver . '",
            `tanggal_realisasi`       = "' . $tgl_kirim_realisasi . '",
            `pengisian_bbm`           = "' . $pengisian_bbm . '",
            `tgl_pengisian`           = ' . $tgl_pengisian_fix . ',
            `dispenser`               = "' . $dispenser_fix . '",
            `liter_bbm`               = "' . $liter_bbm . '",
            `pengisian_bbm_tambahan`  = "' . $pengisian_bbm2 . '",
            `tgl_pengisian_tambahan`  = ' . $tgl_pengisian_tambahan_fix . ',
            `dispenser_tambahan`      = "' . $dispenser_fix2 . '",
            `liter_bbm_tambahan`      = "' . $liter_bbm_fix2 . '",
            `pengisian_bbm_tambahan2`  = "' . $pengisian_bbm3 . '",
            `tgl_pengisian_tambahan2`  = ' . $tgl_pengisian_tambahan_fix3 . ',
            `dispenser_tambahan2`      = "' . $dispenser_fix3 . '",
            `liter_bbm_tambahan2`      = "' . $liter_bbm_fix3 . '",
            `jarak_real`              = "' . $jarak_real . '",
            `total_jasa`              = "' . $total_jasa . '",
            `total_bbm`               = "' . $total_bbm . '",
            `uang_makan`              = "' . $uang_makan . '",
            `uang_kernet`             = "' . $kernet . '",
            `uang_tol`                = "' . $tol . '",
            `uang_demmurade`          = "' . $demmurade . '",
            `uang_koordinasi`         = "' . $koordinasi . '",
            `uang_multidrop`          = "' . $multidrop_fix . '",
            `biaya_penyebrangan`      = "' . $biaya_penyebrangan . '",
            `biaya_lain`              = "' . $biaya_lain . '",
            `catatan_biaya_lain`      = "' . $catatan . '",
            `total_realisasi`         = "' . $total_bpuj . '",
            `catatan`                 = "' . $catatan_realisasi . '",
            `created_by`              = "' . $fullname . '",
            `created_at`              = "' . date("Y-m-d H:i:s") . '"';
    $last_id = $con->setQuery($query);

    if (!empty($uang_makan_tambahan)) {
        foreach ($uang_makan_tambahan as $key => $value) {
            # code...
            $query_tambahan = '
            INSERT INTO pro_bpuj_realisasi_tambahan_hari 
            SET 
                `id_bpuj`           = "' . $id . '",
                `id_realisasi`      = "' . $last_id . '",
                `uang_makan`        = "' . $value . '",
                `uang_kernet`       = "' . $uang_kernet_tambahan[$key] . '",
                `biaya_perjalanan`  = "' . $row['master_biaya_perjalanan'] . '"';
            $con->setQuery($query_tambahan);
            $oke  = $oke && !$con->hasError();
        }
    }

    if (!empty($_FILES['foto']['name'][0])) {
        $max_size      = 1 * 1024 * 1024;
        $allowed_extensions = ['.jpg', '.jpeg', '.png'];
        $pathfile      = $public_base_directory . '/files/uploaded_user/lampiran_realisasi_bpuj';

        foreach ($_FILES['foto']['name'] as $key => $filePhoto) {
            $rand = rand(10, 100);
            $sizePhoto     = $_FILES['foto']['size'][$key];
            $tempPhoto     = $_FILES['foto']['tmp_name'][$key];
            $extPhoto      = substr($filePhoto, strrpos($filePhoto, '.'));
            $keterangan    = isset($_POST["keterangan"][$key]) ? htmlspecialchars($_POST["keterangan"][$key], ENT_QUOTES) : NULL;

            // Validate file extension
            if (!in_array($extPhoto, $allowed_extensions)) {
                $con->rollBack();
                $con->clearError();
                $con->close();
                $data = [
                    "status" => false,
                    "pesan"  => "Invalid file type for $filePhoto",
                ];
                echo json_encode($data);
            }

            // Validate file size
            if ($sizePhoto > $max_size) {
                $con->rollBack();
                $con->clearError();
                $con->close();
                $data = [
                    "status" => false,
                    "pesan"  => "File size exceeds the maximum limit for $filePhoto",
                ];
                echo json_encode($data);
            }

            $query_foto = '
                INSERT INTO pro_foto_realisasi_bpuj 
                SET 
                    `id_realisasi`  = "' . $last_id . '",
                    `foto`          = "foto_' . $last_id . '_' . $rand . '_' . sanitize_filename($filePhoto) . '",
                    `keterangan`    = "' . $keterangan . '",
                    `created_at`    = "' . date("Y-m-d H:i:s") . '"';
            $con->setQuery($query_foto);
            $oke  = $oke && !$con->hasError();

            // Move the uploaded file
            if (move_uploaded_file($tempPhoto, $pathfile . '/' . 'foto_' . $last_id . '_' . $rand . '_' . sanitize_filename($filePhoto))) {
                // echo "File $filePhoto uploaded successfully.<br>";
            } else {
                // echo "Failed to upload file $filePhoto.<br>";
            }
        }
    }
    if ($oke) {
        $con->commit();
        $con->close();
        $data = [
            "status" => true,
            "pesan"  => "Realisasi BPUJ berhasil di simpan",
        ];
        echo json_encode($data);
    } else {
        $con->rollBack();
        $con->clearError();
        $con->close();
        $data = [
            "status" => false,
            "pesan"  => "Realisasi BPUJ gagal di simpan",
        ];
        echo json_encode($data);
    }
} elseif ($jenis == 'realisasi_update') {
    $query_bpuj   = "SELECT * FROM pro_bpuj WHERE id_bpuj = '" . $id . "'";
    $row          = $con->getRecord($query_bpuj);

    $query_realisasi   = "SELECT * FROM pro_bpuj_realisasi WHERE id_bpuj = '" . $id . "'";
    $row_realisasi     = $con->getRecord($query_realisasi);

    if ($row_realisasi['disposisi_realisasi'] == 1) {
        $data = [
            "status" => false,
            "pesan"  => "Gagal di update, Realisasi sudah di approve. Silahkan refresh halaman ini",
        ];
        echo json_encode($data);
    } else {
        $query = '
            UPDATE pro_bpuj_realisasi
            SET 
                `tanggal_realisasi`       = "' . $tgl_kirim_realisasi . '",
                `status_driver`           = "' . $status_driver . '",
                `jarak_real`              = "' . $jarak_real . '",
                `pengisian_bbm`           = "' . $pengisian_bbm . '",
                `tgl_pengisian`           = ' . $tgl_pengisian_fix . ',
                `pengisian_bbm_tambahan`  = "' . $pengisian_bbm2 . '",
                `tgl_pengisian_tambahan`  = ' . $tgl_pengisian_tambahan_fix . ',
                `dispenser`               = "' . $dispenser_fix . '",
                `dispenser_tambahan`      = "' . $dispenser_fix2 . '",
                `liter_bbm`               = "' . $liter_bbm . '",
                `liter_bbm_tambahan`      = "' . $liter_bbm_fix2 . '",
                `pengisian_bbm_tambahan2`  = "' . $pengisian_bbm3 . '",
                `tgl_pengisian_tambahan2`  = ' . $tgl_pengisian_tambahan_fix3 . ',
                `dispenser_tambahan2`      = "' . $dispenser_fix3 . '",
                `liter_bbm_tambahan2`      = "' . $liter_bbm_fix3 . '",
                `total_jasa`              = "' . $total_jasa . '",
                `total_bbm`               = "' . $total_bbm . '",
                `uang_makan`              = "' . $uang_makan . '",
                `uang_kernet`             = "' . $kernet . '",
                `uang_tol`                = "' . $tol . '",
                `uang_demmurade`          = "' . $demmurade . '",
                `uang_koordinasi`         = "' . $koordinasi . '",
                `uang_multidrop`          = "' . $multidrop_fix . '",
                `biaya_penyebrangan`      = "' . $biaya_penyebrangan . '",
                `biaya_lain`              = "' . $biaya_lain . '",
                `catatan_biaya_lain`      = "' . $catatan . '",
                `updated_by`              = "' . $fullname . '",
                `updated_at`              = "' . date("Y-m-d H:i:s") . '",
                `total_realisasi`         = "' . $total_bpuj . '",
                `catatan`                 = "' . $catatan_realisasi . '"
            WHERE 
                `id` = "' . $id_realisasi . '"
            ';

        $con->setQuery($query);
        $oke  = $oke && !$con->hasError();

        if (!empty($uang_makan_tambahan)) {
            $query_delete = "DELETE FROM pro_bpuj_realisasi_tambahan_hari WHERE id_bpuj = '" . $id . "' AND id_realisasi = '" . $id_realisasi . "'";
            $con->setQuery($query_delete);
            $oke  = $oke && !$con->hasError();

            foreach ($uang_makan_tambahan as $key => $value) {
                # code...
                $query_tambahan = '
                INSERT INTO pro_bpuj_realisasi_tambahan_hari 
                SET 
                    `id_bpuj`           = "' . $id . '",
                    `id_realisasi`      = "' . $id_realisasi . '",
                    `uang_makan`        = "' . $value . '",
                    `uang_kernet`       = "' . $uang_kernet_tambahan[$key] . '",
                    `biaya_perjalanan`  = "' . $row['master_biaya_perjalanan'] . '"';
                $con->setQuery($query_tambahan);
                $oke  = $oke && !$con->hasError();
            }
        } else {
            $query_delete = "DELETE FROM pro_bpuj_realisasi_tambahan_hari WHERE id_bpuj = '" . $id . "' AND id_realisasi = '" . $id_realisasi . "'";
            $con->setQuery($query_delete);
            $oke  = $oke && !$con->hasError();
        }

        if (!empty($_FILES['foto']['name'][0])) {

            $query_foto     = "SELECT * FROM pro_foto_realisasi_bpuj WHERE id_realisasi = '" . $id_realisasi . "'";
            $res_foto       = $con->getResult($query_foto);

            if ((count($_FILES['foto']['name']) + count($res_foto)) > 3) {
                $con->rollBack();
                $con->clearError();
                $con->close();
                $data = [
                    "status" => false,
                    "pesan"  => "Foto tidak boleh lebih dari 3",
                ];
                echo json_encode($data);
            } else {
                $max_size      = 1 * 1024 * 1024;
                $allowed_extensions = ['.jpg', '.jpeg', '.png'];
                $pathfile      = $public_base_directory . '/files/uploaded_user/lampiran_realisasi_bpuj';

                foreach ($_FILES['foto']['name'] as $key => $filePhoto) {
                    $rand = rand(10, 100);
                    $sizePhoto     = $_FILES['foto']['size'][$key];
                    $tempPhoto     = $_FILES['foto']['tmp_name'][$key];
                    $extPhoto      = substr($filePhoto, strrpos($filePhoto, '.'));
                    $keterangan    = isset($_POST["keterangan"][$key]) ? htmlspecialchars($_POST["keterangan"][$key], ENT_QUOTES) : NULL;

                    // Validate file extension
                    if (!in_array($extPhoto, $allowed_extensions)) {
                        $con->rollBack();
                        $con->clearError();
                        $con->close();
                        $data = [
                            "status" => false,
                            "pesan"  => "Invalid file type for $filePhoto",
                        ];
                        echo json_encode($data);
                    }

                    // Validate file size
                    if ($sizePhoto > $max_size) {
                        $con->rollBack();
                        $con->clearError();
                        $con->close();
                        $data = [
                            "status" => false,
                            "pesan"  => "File size exceeds the maximum limit for $filePhoto",
                        ];
                        echo json_encode($data);
                    }

                    $query_foto = '
                        INSERT INTO pro_foto_realisasi_bpuj 
                        SET 
                            `id_realisasi`  = "' . $id_realisasi . '",
                            `foto`          = "foto_' . $id_realisasi . '_' . $rand . '_' . sanitize_filename($filePhoto) . '",
                            `keterangan`    = "' . $keterangan . '",
                            `created_at`    = "' . date("Y-m-d H:i:s") . '"';
                    $con->setQuery($query_foto);
                    $oke  = $oke && !$con->hasError();

                    // Move the uploaded file
                    if (move_uploaded_file($tempPhoto, $pathfile . '/' . 'foto_' . $id_realisasi . '_' . $rand . '_' . sanitize_filename($filePhoto))) {
                        // echo "File $filePhoto uploaded successfully.<br>";
                    } else {
                        // echo "Failed to upload file $filePhoto.<br>";
                    }
                }
                if ($oke) {
                    $con->commit();
                    $con->close();
                    $data = [
                        "status" => true,
                        "pesan"  => "Realisasi berhasil simpan",
                    ];
                    echo json_encode($data);
                } else {
                    $con->rollBack();
                    $con->clearError();
                    $con->close();
                    $data = [
                        "status" => false,
                        "pesan"  => "Realisasi gagal di simpan",
                    ];
                    echo json_encode($data);
                }
            }
        } else {
            if ($oke) {
                $con->commit();
                $con->close();
                $data = [
                    "status" => true,
                    "pesan"  => "Realisasi berhasil simpan",
                ];
                echo json_encode($data);
            } else {
                $con->rollBack();
                $con->clearError();
                $con->close();
                $data = [
                    "status" => false,
                    "pesan"  => "Realisasi gagal di simpan",
                ];
                echo json_encode($data);
            }
        }
    }
} elseif ($jenis == 'hapus_foto') {
    $id_foto = $_POST["id_foto"];

    $ambil_foto = "SELECT * FROM pro_foto_realisasi_bpuj WHERE id='" . $id_foto . "'";
    $row = $con->getRecord($ambil_foto);

    $pathfile = $public_base_directory . '/files/uploaded_user/lampiran_realisasi_bpuj/' . $row['foto'];

    // Check if the file exists
    if (file_exists($pathfile)) {
        // Attempt to delete the file
        if (unlink($pathfile)) {
            $query_delete = "DELETE FROM pro_foto_realisasi_bpuj WHERE id = '" . $id_foto . "'";
            $con->setQuery($query_delete);
            $oke  = $oke && !$con->hasError();

            if ($oke) {
                $con->commit();
                $con->close();
                $data = [
                    "status" => true,
                    "pesan"  => "Realisasi berhasil simpan",
                ];
                echo json_encode($data);
            } else {
                $con->rollBack();
                $con->clearError();
                $con->close();
                $data = [
                    "status" => false,
                    "pesan"  => "Realisasi gagal di simpan",
                ];
                echo json_encode($data);
            }
        } else {
            $con->rollBack();
            $con->clearError();
            $con->close();
            $data = [
                "status" => false,
                "pesan"  => "Error delete foto",
            ];
            echo json_encode($data);
        }
    } else {
        $con->rollBack();
        $con->clearError();
        $con->close();
        $data = [
            "status" => false,
            "pesan"  => "Foto tidak ada",
        ];
        echo json_encode($data);
    }
} else {
    if ($id == NULL) {
        $query_bpuj   = "SELECT * FROM pro_bpuj WHERE cabang = '" . $wilayah . "' ORDER BY id_bpuj DESC";
        $row          = $con->getRecord($query_bpuj);
        $query_cabang = "SELECT inisial_cabang from pro_master_cabang where id_master = '" . $wilayah . "'";
        $inisial_cabang = $con->getRecord($query_cabang);
        $arrRomawi  = array("1" => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
        $year       = date("y");

        if ($row) {
            $exst_bpuj = $row['nomor_bpuj'];
            $explode = explode("/", $exst_bpuj);
            $year_bpuj = $explode[3];

            if ($year_bpuj == $year) {
                $urut_bpuj = $explode[5] + 1;
                $no_bpuj   = sprintf("%04s", $urut_bpuj);
                $noms_bpuj = 'BPUJ/' . 'PE/' . $inisial_cabang['inisial_cabang'] . '/' . $year . '/' . $arrRomawi[intval(date("m"))] . '/' . $no_bpuj;
            } else {
                $urut_bpuj = 0001;
                $no_bpuj   = sprintf("%04s", $urut_bpuj);
                $noms_bpuj = 'BPUJ/' . 'PE/' . $inisial_cabang['inisial_cabang'] . '/' . $year . '/' . $arrRomawi[intval(date("m"))] . '/' . $no_bpuj;
            }
        } else {
            $urut_bpuj = 0001;
            $no_bpuj   = sprintf("%04s", $urut_bpuj);
            $noms_bpuj = 'BPUJ/' . 'PE/' . $inisial_cabang['inisial_cabang'] . '/' . $year . '/' . $arrRomawi[intval(date("m"))] . '/' . $no_bpuj;
        }

        $query = '
            INSERT INTO pro_bpuj 
            SET 
                `id_dsd`                  = "' . $id_dsd . '",
                `nomor_bpuj`              = "' . $noms_bpuj . '",
                `cabang`                  = "' . $wilayah . '",
                `tanggal_bpuj`            = "' . $tgl_kirim_bpuj . '",
                `nama_customer`           = "' . $nama_customer . '",
                `no_unit`                 = "' . $no_unit . '",
                `nama_driver`             = "' . $nama_driver . '",
                `status_driver`           = "' . $status_driver . '",
                `jarak_real_lcr`          = "' . $jarak_km_lcr . '",
                `jarak_real`              = "' . $jarak_real . '",
                `jasa`                    = "' . $jasa . '",
                `jenis_tangki`            = "' . $jenis_tangki . '",
                `pengisian_bbm`           = "' . $pengisian_bbm . '",
                `tgl_pengisian`           = ' . $tgl_pengisian_fix . ',
                `pengisian_bbm_tambahan`  = "' . $pengisian_bbm2 . '",
                `tgl_pengisian_tambahan`  = ' . $tgl_pengisian_tambahan_fix . ',
                `dispenser`               = "' . $dispenser_fix . '",
                `dispenser_tambahan`      = "' . $dispenser_fix2 . '",
                `liter_bbm`               = "' . $liter_bbm . '",
                `liter_bbm_tambahan`      = "' . $liter_bbm_fix2 . '",
                `pengisian_bbm_tambahan2`  = "' . $pengisian_bbm3 . '",
                `tgl_pengisian_tambahan2`  = ' . $tgl_pengisian_tambahan_fix3 . ',
                `dispenser_tambahan2`      = "' . $dispenser_fix3 . '",
                `liter_bbm_tambahan2`      = "' . $liter_bbm_fix3 . '",
                `perbandingan_bbm`        = "' . $perbandingan_bbm . '",
                `total_jasa`              = "' . $total_jasa . '",
                `total_bbm`               = "' . $total_bbm . '",
                `uang_makan`              = "' . $uang_makan . '",
                `uang_kernet`             = "' . $kernet . '",
                `uang_tol`                = "' . $tol . '",
                `uang_demmurade`          = "' . $demmurade . '",
                `uang_koordinasi`         = "' . $koordinasi . '",
                `uang_multidrop`          = "' . $multidrop_fix . '",
                `biaya_penyebrangan`      = "' . $biaya_penyebrangan . '",
                `biaya_lain`              = "' . $biaya_lain . '",
                `catatan_biaya_lain`      = "' . $catatan . '",
                `total_uang_bpuj`         = "' . $total_bpuj . '",
                `created_by`              = "' . $fullname . '",
                `created_at`              = "' . date("Y-m-d H:i:s") . '",
                `master_multidrop`        = "' . $row_master['multidrop'] . '",
                `master_makan_kedua`      = "' . $row_master['makan_kedua'] . '",
                `master_kernet`           = "' . $row_master['kernet'] . '",
                `master_biaya_perjalanan` = "' . $row_master['perjalanan'] . '"';
        $last_id = $con->setQuery($query);
        $oke  = $oke && !$con->hasError();
        if (!empty($uang_makan_tambahan)) {
            foreach ($uang_makan_tambahan as $key => $value) {
                # code...
                $query_tambahan = '
                INSERT INTO pro_bpuj_tambahan_hari 
                SET 
                    `id_bpuj`           = "' . $last_id . '",
                    `uang_makan`        = "' . $value . '",
                    `uang_kernet`       = "' . $uang_kernet_tambahan[$key] . '",
                    `biaya_perjalanan`  = "' . $row_master['perjalanan'] . '"';
                $con->setQuery($query_tambahan);
                $oke  = $oke && !$con->hasError();
            }
        }
        if ($oke) {
            $con->commit();
            $con->close();
            $data = [
                "status" => true,
                "pesan"  => "BPUJ berhasil simpan",
            ];
            echo json_encode($data);
        } else {
            $con->rollBack();
            $con->clearError();
            $con->close();
            $data = [
                "status" => false,
                "pesan"  => "BPUJ gagal di simpan",
            ];
            echo json_encode($data);
        }
    } else {
        if ($role == 10) {
            $pengecekan = "SELECT * FROM pro_bpuj WHERE id_bpuj='" . $id . "'";
            $row = $con->getRecord($pengecekan);


            //Validasi edit masuk stock id_dsd (fadli 18/07/2025)
            $query_delete = "DELETE FROM new_pro_inventory_depot WHERE id_dsd = '" . $row['id_dsd'] . "' AND id_jenis = '10'";
            $con->setQuery($query_delete);
            $oke  = $oke && !$con->hasError();
            // end 

            if ($bayar < $row['total_uang_bpuj']) {
                $data = [
                    "status" => false,
                    "pesan"  => "Gagal, Nominal yang dibayarkan lebih kecil dari total BPUJ",
                ];
                echo json_encode($data);
            } else if ($bayar > $row['total_uang_bpuj']) {
                $data = [
                    "status" => false,
                    "pesan"  => "Gagal, Nominal yang dibayarkan lebih besar dari total BPUJ",
                ];
                echo json_encode($data);
            } else {
                if ($bayar < $total_bpuj) {
                    $data = [
                        "status" => false,
                        "pesan"  => "Gagal, Nominal yang dibayarkan lebih kecil dari total BPUJ",
                    ];
                    echo json_encode($data);
                } else if ($bayar > $total_bpuj) {
                    $data = [
                        "status" => false,
                        "pesan"  => "Gagal, Nominal yang dibayarkan lebih besar dari total BPUJ",
                    ];
                    echo json_encode($data);
                } else {
                    $query = '
                        UPDATE pro_bpuj 
                        SET 
                            `diberikan_oleh` = "' . $fullname . '",
                            `diberikan_tgl` = "' . date("Y-m-d H:i:s") . '",
                            `yang_dibayarkan` = "' . $bayar . '",
                            `disposisi_bpuj` = "2"
                        WHERE
                            `id_bpuj` = "' . $id . '"
                        ';
                    $con->setQuery($query);
                    $oke  = $oke && !$con->hasError();

                    $pengecekan = "SELECT * FROM pro_bpuj WHERE id_bpuj='" . $id . "'";
                    $row = $con->getRecord($pengecekan);

                    $pengisian = [
                        ['dispenser_id' => $row['dispenser'], 'volume' => floatval($row['liter_bbm'])],
                        ['dispenser_id' => $row['dispenser_tambahan'], 'volume' => floatval($row['liter_bbm_tambahan'])],
                        ['dispenser_id' => $row['dispenser_tambahan2'], 'volume' => floatval($row['liter_bbm_tambahan2'])],
                    ];

                    foreach ($pengisian as $item) {
                        $id_dispenser = $item['dispenser_id'];
                        $volume_input = $item['volume'];

                        // Skip jika tidak ada pengisian
                        if (!$id_dispenser || $volume_input <= 0) continue;

                        $sql_stok_total = "SELECT SUM(sisa_inven) AS total_stok FROM vw_terminal_inventory_receive WHERE id_terminal = '" . $id_dispenser . "' AND sisa_inven > 0";
                        $row_total  = $con->getRecord($sql_stok_total);
                        $total_stok = floatval($row_total['total_stok'] ?? 0);

                        if ($total_stok < $volume_input) {
                            $con->rollBack();
                            $con->clearError();
                            $con->close();
                            $result = [
                                "status"     => false,
                                "pesan"     => "Stock pada dispenser tidak cukup",
                            ];
                            echo json_encode($result);
                            exit();
                        }

                        $sisa_pemakaian = $volume_input;

                        $sql_inven = "SELECT * FROM vw_terminal_inventory_receive WHERE id_terminal = '" . $id_dispenser . "' AND sisa_inven > 0 ORDER BY tgl_po_supplier ASC";
                        $res_po = $con->getResult($sql_inven);

                        if (count($res_po) > 0) {
                            foreach ($res_po as $key) {

                                if ($sisa_pemakaian <= 0) break;

                                $id_po_supplier = $key['id_po_supplier'];
                                $id_po_receive = $key['id_po_receive'];
                                $qty_tersedia = floatval($key['sisa_inven']);
                                $qty_potong = min($qty_tersedia, $sisa_pemakaian);

                                $query_stock = '
                                        INSERT INTO new_pro_inventory_depot 
                                        SET 
                                        `id_datanya`           = "dispenser",
                                        `id_jenis`             = "10",
                                        `id_produk`            = "' . $key['id_produk'] . '",
                                        `id_terminal`          = "' . $id_dispenser . '",
                                        `id_po_supplier`       = "' . $id_po_supplier . '",
                                        `id_po_receive`        = "' . $id_po_receive . '",
                                        `tanggal_inven`        = "' . $row['tanggal_bpuj'] . '",
                                        `out_inven_virtual`    = "' . $qty_potong . '",
                                        `keterangan`           = "Pengurangan Stock dari BPUJ",
                                        `created_time`         = "' . date("Y-m-d H:i:s") . '",
                                        `created_by`           = "' . $fullname . '",
                                        `created_ip`           = "' . $_SERVER['REMOTE_ADDR'] . '",
                                        `lastupdate_time`      = "' . date("Y-m-d H:i:s") . '",
                                        `lastupdate_by`        = "' . $fullname . '",
                                        `lastupdate_ip`        = "' . $_SERVER['REMOTE_ADDR'] . '",
                                        `id_dsd`               = "' . $row['id_dsd'] . '"';
                                $con->setQuery($query_stock);
                                $oke  = $oke && !$con->hasError();

                                $sisa_pemakaian -= $qty_potong;
                            }
                        }
                    }

                    if ($oke) {
                        $con->commit();
                        $con->close();
                        $data = [
                            "status" => true,
                            "pesan"  => "BPUJ berhasil simpan",
                        ];
                        echo json_encode($data);
                    } else {
                        $con->rollBack();
                        $con->clearError();
                        $con->close();
                        $data = [
                            "status" => false,
                            "pesan"  => "BPUJ gagal di simpan",
                        ];
                        echo json_encode($data);
                    }
                }
            }
        } else {
            $pengecekan = "SELECT * FROM pro_bpuj WHERE id_bpuj='" . $id . "'";
            $row = $con->getRecord($pengecekan);

            if ($row['diberikan_oleh'] == NULL) {

                $query = '
                UPDATE pro_bpuj 
                SET 
                    `tanggal_bpuj`            = "' . $tgl_kirim_bpuj . '",
                    `status_driver`           = "' . $status_driver . '",
                    `jarak_real`              = "' . $jarak_real . '",
                    `pengisian_bbm`           = "' . $pengisian_bbm . '",
                    `tgl_pengisian`           = ' . $tgl_pengisian_fix . ',
                    `pengisian_bbm_tambahan`  = "' . $pengisian_bbm2 . '",
                    `pengisian_bbm_tambahan2`  = "' . $pengisian_bbm3 . '",
                    `tgl_pengisian_tambahan`  = ' . $tgl_pengisian_tambahan_fix . ',
                    `tgl_pengisian_tambahan2`  = ' . $tgl_pengisian_tambahan_fix3 . ',
                    `dispenser`               = "' . $dispenser_fix . '",
                    `dispenser_tambahan`      = "' . $dispenser_fix2 . '",
                    `dispenser_tambahan2`      = "' . $dispenser_fix3 . '",
                    `liter_bbm`               = "' . $liter_bbm . '",
                    `liter_bbm_tambahan`      = "' . $liter_bbm_fix2 . '",
                    `liter_bbm_tambahan2`      = "' . $liter_bbm_fix3 . '",
                    `total_jasa`              = "' . $total_jasa . '",
                    `total_bbm`               = "' . $total_bbm . '",
                    `uang_makan`              = "' . $uang_makan . '",
                    `uang_kernet`             = "' . $kernet . '",
                    `uang_tol`                = "' . $tol . '",
                    `uang_demmurade`          = "' . $demmurade . '",
                    `uang_koordinasi`         = "' . $koordinasi . '",
                    `biaya_penyebrangan`      = "' . $biaya_penyebrangan . '",
                    `biaya_lain`              = "' . $biaya_lain . '",
                    `catatan_biaya_lain`      = "' . $catatan . '",
                    `updated_by`              = "' . $fullname . '",
                    `updated_at`              = "' . date("Y-m-d H:i:s") . '",
                    `total_uang_bpuj`         = "' . $total_bpuj . '"
                WHERE 
                    `id_bpuj` = "' . $id . '"
                ';
                $con->setQuery($query);
                $oke  = $oke && !$con->hasError();

                if (!empty($uang_makan_tambahan)) {
                    $query_delete = "DELETE FROM pro_bpuj_tambahan_hari WHERE id_bpuj = '" . $id . "'";
                    $con->setQuery($query_delete);
                    $oke  = $oke && !$con->hasError();

                    foreach ($uang_makan_tambahan as $key => $value) {
                        # code...
                        $query_tambahan = '
                        INSERT INTO pro_bpuj_tambahan_hari 
                        SET 
                            `id_bpuj`           = "' . $id . '",
                            `uang_makan`        = "' . $value . '",
                            `uang_kernet`       = "' . $uang_kernet_tambahan[$key] . '",
                            `biaya_perjalanan`  = "' . $row['master_biaya_perjalanan'] . '"';
                        $con->setQuery($query_tambahan);
                        $oke  = $oke && !$con->hasError();
                    }
                } else {
                    $query_delete = "DELETE FROM pro_bpuj_tambahan_hari WHERE id_bpuj = '" . $id . "'";
                    $con->setQuery($query_delete);
                    $oke  = $oke && !$con->hasError();
                }

                if ($oke) {
                    $con->commit();
                    $con->close();
                    $data = [
                        "status" => true,
                        "pesan"  => "BPUJ berhasil simpan",
                    ];
                    echo json_encode($data);
                } else {
                    $con->rollBack();
                    $con->clearError();
                    $con->close();
                    $data = [
                        "status" => false,
                        "pesan"  => "BPUJ gagal di simpan",
                    ];
                    echo json_encode($data);
                }
                // echo json_encode($query);
            } else {
                $data = [
                    "status" => false,
                    "pesan"  => "Gagal, Pengajuan BPUJ Anda sudah di Approve " . $fullname . " Silahkan refresh halaman Anda",
                ];
                echo json_encode($data);
            }
        }
    }
}

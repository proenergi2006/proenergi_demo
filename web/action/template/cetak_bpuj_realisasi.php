<style>
    table {
        font-size: 8.5pt;
    }

    .tabel_header td {
        padding: 1px 3px;
        font-size: 9pt;
        height: 18px;
    }

    .tabel_rincian th {
        padding: 5px 3px;
        background-color: #ffcc99;
    }

    .tabel_rincian td {
        padding: 3px 2px;
    }

    .td-ket,
    .td-subisi {
        padding: 1px 0px 2px;
        vertical-align: top;
    }

    .td-subisi {
        font-size: 5pt;
    }

    .td-ket {
        padding: 1px 0px;
        font-size: 8pt;
    }

    p {
        margin: 0 0 10px;
        text-align: justify;
    }

    .b1 {
        border-top: 1px solid #000;
    }

    .b2 {
        border-right: 1px solid #000;
    }

    .b3 {
        border-bottom: 1px solid #000;
    }

    .b4 {
        border-left: 1px solid #000;
    }

    .b1d {
        border-top: 2px solid #000;
    }

    .b2d {
        border-right: 2px solid #000;
    }

    .b3d {
        border-bottom: 2px solid #000;
    }

    .b4d {
        border-left: 2px solid #000;
    }
</style>
<htmlpagefooter name="myHTMLFooter1">
    <p style="margin:0; text-align:right; font-size:7pt;"><i>(This form is valid with sign by computerized system)</i></p>
    <p style="margin:0; text-align:right; font-size:6pt;">Printed by <?php echo $printe; ?></p>
</htmlpagefooter>
<sethtmlpagefooter name="myHTMLFooter1" page="ALL" value="on" show-this-page="1" />
<?php
// $waktu_bpuj = date("H:i:s", strtotime($realisasi['tanggal_realisasi']));
// $waktu_diberikan = date("H:i:s", strtotime($res['diberikan_tgl']));

// Pengajuan BPUJ
$exp = explode("||", $res['pengisian_bbm']);
$pengisian_bbm = $exp[0];
$id_terminal = $exp[1];

$exp2 = explode("||", $res['pengisian_bbm_tambahan']);
$pengisian_bbm_tambahan = $exp2[0];
$id_terminal_tambahan = $exp2[1];

$exp3 = explode("||", $res['pengisian_bbm_tambahan2']);
$pengisian_bbm_tambahan3 = $exp3[0];
$id_terminal_tambahan3 = $exp3[1];

if ($res['dispenser'] != 0) {
    $query_dispenser = "SELECT * FROM pro_master_terminal WHERE id_master = '" . $id_terminal . "'";
    $dispenser = $con->getRecord($query_dispenser);
}

if ($res['dispenser_tambahan'] != 0) {
    $query_dispenser2 = "SELECT * FROM pro_master_terminal WHERE id_master = '" . $id_terminal_tambahan . "'";
    $dispenser2 = $con->getRecord($query_dispenser2);
}

if ($res['dispenser_tambahan2'] != 0) {
    $query_dispenser3 = "SELECT * FROM pro_master_terminal WHERE id_master = '" . $id_terminal_tambahan3 . "'";
    $dispenser3 = $con->getRecord($query_dispenser3);
}

$data_tambahan  = "SELECT * FROM pro_bpuj_tambahan_hari WHERE id_bpuj = '" . $idr . "'";
$res2  = $con->getResult($data_tambahan);

// ============================================

// Realisasi BPUJ
if ($realisasi) {

    if ($realisasi['pengisian_bbm'] != NULL) {
        $exp_realisasi = explode("||", $realisasi['pengisian_bbm']);
        $pengisian_bbm_realisasi = $exp_realisasi[0];
        $id_terminal_realisasi = $exp_realisasi[1];

        if ($realisasi['dispenser'] != 0) {
            $query_dispenser_realisasi = "SELECT * FROM pro_master_terminal WHERE id_master = '" . $id_terminal_realisasi . "'";
            $dispenser_realisasi = $con->getRecord($query_dispenser_realisasi);
        }
    } else {
        $pengisian_bbm_realisasi = $exp[0];
        $dispenser_realisasi = $dispenser;
    }

    if ($realisasi['pengisian_bbm_tambahan'] != NULL) {
        $exp_realisasi2 = explode("||", $realisasi['pengisian_bbm_tambahan']);
        $pengisian_bbm_tambahan_realisasi = $exp_realisasi2[0];
        $id_terminal_tambahan_realisasi = $exp_realisasi2[1];

        if ($realisasi['dispenser_tambahan'] != 0) {
            $query_dispenser_tambahan_realisasi = "SELECT * FROM pro_master_terminal WHERE id_master = '" . $id_terminal_tambahan_realisasi . "'";
            $dispenser_tambahan_realisasi = $con->getRecord($query_dispenser_tambahan_realisasi);
        }
    } else {
        $pengisian_bbm_tambahan_realisasi = $exp2[0];
        $dispenser_tambahan_realisasi = $dispenser2;
    }

    if ($realisasi['pengisian_bbm_tambahan2'] != NULL) {
        $exp_realisasi3 = explode("||", $realisasi['pengisian_bbm_tambahan2']);
        $pengisian_bbm_tambahan_realisasi3 = $exp_realisasi3[0];
        $id_terminal_tambahan_realisasi3 = $exp_realisasi3[1];

        if ($realisasi['dispenser_tambahan2'] != 0) {
            $query_dispenser_tambahan_realisasi3 = "SELECT * FROM pro_master_terminal WHERE id_master = '" . $id_terminal_tambahan_realisasi3 . "'";
            $dispenser_tambahan_realisasi3 = $con->getRecord($query_dispenser_tambahan_realisasi3);
        }
    } else {
        $pengisian_bbm_tambahan_realisasi3 = $exp3[0];
        $dispenser_tambahan_realisasi3 = $dispenser3;
    }

    $data_tambahan_realisasi  = "SELECT * FROM pro_bpuj_realisasi_tambahan_hari WHERE id_realisasi = '" . $realisasi['id'] . "'";
    $res_hari_realisasi  = $con->getResult($data_tambahan_realisasi);
}
?>
<div class="container">
    <table border="0" width="100%">
        <tr>
            <td width="30%">
                <div style="padding:0;"><img src="<?php echo BASE_IMAGE . "/logo-kiri-penawaran.png"; ?>" width="20%" /></div>
            </td>
            <td width="2%">

            </td>
            <td>
                <center>
                    <?= $res['nomor_bpuj'] ?>
                    <br><br>
                    <b>
                        <h3>BUKTI REALISASI UANG JALAN</h3>
                    </b>
                </center>
            </td>
        </tr>
    </table>
    <br>
    <table width="100%" border="0" cellpadding="5" cellspacing="0">
        <tr>
            <td width="20%">
                Tanggal Realisasi
            </td>
            <td width="2%">
                :
            </td>
            <td>
                <?= tgl_indo($realisasi['tanggal_realisasi']) ?>
            </td>
        </tr>
        <tr>
            <td>
                Nama Driver
            </td>
            <td>
                :
            </td>
            <td>
                <?= $realisasi['nama_driver'] ?>
            </td>
        </tr>
        <tr>
            <td>
                Status
            </td>
            <td>
                :
            </td>
            <td>
                <?= $realisasi['status_driver'] ?>
            </td>
        </tr>
        <tr>
            <td>
                No. Unit
            </td>
            <td>
                :
            </td>
            <td>
                <?= $realisasi['no_unit'] ?>
            </td>
        </tr>
        <tr>
            <td>
                No. SJ
            </td>
            <td>
                :
            </td>
            <td>
                <?= $res['no_spj'] ?>
            </td>
        </tr>
        <tr>
            <td>
                Nama Customer
            </td>
            <td>
                :
            </td>
            <td>
                <?= $res['nama_customer'] ?>
            </td>
        </tr>
        <tr>
            <td>
                Jarak Tempuh
            </td>
            <td>
                :
            </td>
            <td>
                <?= $realisasi['jarak_real'] ?> Km
            </td>
        </tr>
        <?php if ($res['created_at'] > '2024-06-18') : ?>
            <tr>
                <td>
                    Pengisian BBM
                </td>
                <td>
                    :
                </td>
                <td>
                    <?php
                    if ($realisasi['tgl_pengisian'] != NULL) {
                        $tgl_pengisian = " | " . tgl_indo($realisasi['tgl_pengisian']);
                    } else {
                        $tgl_pengisian = "";
                    }
                    ?>
                    <?php if ($realisasi['dispenser'] != 0) : ?>
                        <?= $dispenser_realisasi['nama_terminal'] . " - " . $dispenser_realisasi['tanki_terminal']; ?>

                        <?php if (fmod($realisasi['liter_bbm'], 1) !== 0.000) : ?>
                            (<?= number_format($realisasi['liter_bbm'], 3) ?> Liter) <?= $tgl_pengisian ?>
                        <?php else : ?>
                            (<?= number_format($realisasi['liter_bbm']) ?> Liter) <?= $tgl_pengisian ?>
                        <?php endif ?>

                    <?php else : ?>
                        <?= $pengisian_bbm_realisasi; ?>

                        <?php if (fmod($realisasi['liter_bbm'], 1) !== 0.000) : ?>
                            (<?= number_format($realisasi['liter_bbm'], 3) ?> Liter) <?= $tgl_pengisian ?>
                        <?php else : ?>
                            (<?= number_format($realisasi['liter_bbm']) ?> Liter) <?= $tgl_pengisian ?>
                        <?php endif ?>

                    <?php endif ?>
                </td>
            </tr>
            <?php if ($realisasi['pengisian_bbm_tambahan'] != "") : ?>
                <tr>
                    <td>
                        Pengisian BBM
                    </td>
                    <td>
                        :
                    </td>
                    <td>
                        <?php
                        if ($realisasi['tgl_pengisian_tambahan'] != NULL) {
                            $tgl_pengisian2 = " | " . tgl_indo($realisasi['tgl_pengisian_tambahan']);
                        } else {
                            $tgl_pengisian2 = "";
                        }
                        ?>
                        <?php if ($realisasi['dispenser_tambahan'] != 0) : ?>
                            <?= $dispenser_tambahan_realisasi['nama_terminal'] . " - " . $dispenser_tambahan_realisasi['tanki_terminal']; ?>

                            <?php if (fmod($realisasi['liter_bbm_tambahan'], 1) !== 0.000) : ?>
                                (<?= number_format($realisasi['liter_bbm_tambahan'], 3) ?> Liter) <?= $tgl_pengisian2 ?>
                            <?php else : ?>
                                (<?= number_format($realisasi['liter_bbm_tambahan']) ?> Liter) <?= $tgl_pengisian2 ?>
                            <?php endif ?>

                        <?php else : ?>
                            <?= $pengisian_bbm_tambahan_realisasi; ?>

                            <?php if (fmod($realisasi['liter_bbm_tambahan'], 1) !== 0.000) : ?>
                                (<?= number_format($realisasi['liter_bbm_tambahan'], 3) ?> Liter) <?= $tgl_pengisian2 ?>
                            <?php else : ?>
                                (<?= number_format($realisasi['liter_bbm_tambahan']) ?> Liter) <?= $tgl_pengisian2 ?>
                            <?php endif ?>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endif ?>
            <?php if ($realisasi['pengisian_bbm_tambahan2'] != "") : ?>
                <tr>
                    <td>
                        Pengisian BBM
                    </td>
                    <td>
                        :
                    </td>
                    <td>
                        <?php
                        if ($realisasi['tgl_pengisian_tambahan2'] != NULL) {
                            $tgl_pengisian3 = " | " . tgl_indo($realisasi['tgl_pengisian_tambahan2']);
                        } else {
                            $tgl_pengisian3 = "";
                        }
                        ?>
                        <?php if ($realisasi['dispenser_tambahan2'] != 0) : ?>
                            <?= $dispenser_tambahan_realisasi3['nama_terminal'] . " - " . $dispenser_tambahan_realisasi3['tanki_terminal']; ?>

                            <?php if (fmod($realisasi['liter_bbm_tambahan2'], 1) !== 0.000) : ?>
                                (<?= number_format($realisasi['liter_bbm_tambahan2'], 3) ?> Liter) <?= $tgl_pengisian3 ?>
                            <?php else : ?>
                                (<?= number_format($realisasi['liter_bbm_tambahan2']) ?> Liter) <?= $tgl_pengisian3 ?>
                            <?php endif ?>

                        <?php else : ?>
                            <?= $pengisian_bbm_tambahan_realisasi3; ?>

                            <?php if (fmod($realisasi['liter_bbm_tambahan2'], 1) !== 0.000) : ?>
                                (<?= number_format($realisasi['liter_bbm_tambahan2'], 3) ?> Liter) <?= $tgl_pengisian3 ?>
                            <?php else : ?>
                                (<?= number_format($realisasi['liter_bbm_tambahan2']) ?> Liter) <?= $tgl_pengisian3 ?>
                            <?php endif ?>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endif ?>
        <?php else : ?>
            <tr>
                <td>
                    Pengisian BBM
                </td>
                <td>
                    :
                </td>
                <td>
                    <?php if ($res['dispenser'] != 0) : ?>
                        <?= $dispenser['nama_terminal'] . " - " . $dispenser['tanki_terminal']; ?>

                        <?php if (fmod($realisasi['liter_bbm'], 1) !== 0.000) : ?>
                            (<?= number_format($realisasi['liter_bbm'], 3) ?> Liter)
                        <?php else : ?>
                            (<?= number_format($realisasi['liter_bbm']) ?> Liter)
                        <?php endif ?>


                    <?php else : ?>
                        <?= $pengisian_bbm; ?>

                        <?php if (fmod($realisasi['liter_bbm'], 1) !== 0.000) : ?>
                            (<?= number_format($realisasi['liter_bbm'], 3) ?> Liter)
                        <?php else : ?>
                            (<?= number_format($realisasi['liter_bbm']) ?> Liter)
                        <?php endif ?>
                    <?php endif ?>
                </td>
            </tr>
            <?php if ($res['pengisian_bbm_tambahan'] != "") : ?>
                <tr>
                    <td>
                        Pengisian BBM
                    </td>
                    <td>
                        :
                    </td>
                    <td>
                        <?php if ($res['dispenser_tambahan'] != 0) : ?>
                            <?= $dispenser2['nama_terminal'] . " - " . $dispenser2['tanki_terminal']; ?>

                            <?php if (fmod($realisasi['liter_bbm_tambahan'], 1) !== 0.000) : ?>
                                (<?= number_format($realisasi['liter_bbm_tambahan'], 3) ?> Liter)
                            <?php else : ?>
                                (<?= number_format($realisasi['liter_bbm_tambahan']) ?> Liter)
                            <?php endif ?>
                        <?php else : ?>
                            <?= $pengisian_bbm_tambahan; ?>

                            <?php if (fmod($realisasi['liter_bbm_tambahan'], 1) !== 0.000) : ?>
                                (<?= number_format($realisasi['liter_bbm_tambahan'], 3) ?> Liter)
                            <?php else : ?>
                                (<?= number_format($realisasi['liter_bbm_tambahan']) ?> Liter)
                            <?php endif ?>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endif ?>
        <?php endif ?>
    </table>
    <br>
    <table width="100%" border="1" style="border-collapse: collapse;" cellpadding="5" cellspacing="0">
        <tr style="background-color: #ffcc99;">
            <th width="5%">
                NO
            </th>
            <th width="75%">
                Keterangan
            </th>
            <th>
                Amount (Rp)
            </th>
        </tr>
        <tr>
            <td align="center">
                1
            </td>
            <td>
                Jasa
            </td>
            <td align="right">
                <span><?= number_format($realisasi['total_jasa']) ?></span>
            </td>
        </tr>
        <tr>
            <td align="center">
                2
            </td>
            <td>
                <?php
                $total_bahan_bakar = $realisasi['liter_bbm'] + $realisasi['liter_bbm_tambahan'] + $realisasi['liter_bbm_tambahan2'];
                ?>
                <?php if (fmod($total_bahan_bakar, 1) !== 0.000) : ?>
                    Bahan Bakar <?= number_format($total_bahan_bakar, 3, ",", ".") ?> Liter
                <?php else : ?>
                    Bahan Bakar <?= number_format($total_bahan_bakar) ?> Liter
                <?php endif ?>
            </td>
            <td align="right">
                <span>
                    <?php if ($realisasi['total_bbm'] == 0) : ?>
                        -
                    <?php else : ?>
                        <?= number_format($realisasi['total_bbm']) ?>
                    <?php endif ?>
                </span>
            </td>
        </tr>
        <tr>
            <td align="center">
                3
            </td>
            <td>
                Tol
            </td>
            <td align="right">
                <span>
                    <?php if ($realisasi['uang_tol'] == 0) : ?>
                        -
                    <?php else : ?>
                        <?= number_format($realisasi['uang_tol']) ?>
                    <?php endif ?>
                </span>
            </td>
        </tr>
        <tr>
            <td align="center">
                4
            </td>
            <td>
                Uang makan + parkir + meal
            </td>
            <td align="right">
                <?php if ($res['created_at'] > '2024-06-18') : ?>
                    <?= number_format($realisasi['uang_makan']) ?>
                <?php else : ?>
                    <?= number_format($res['uang_makan']) ?>
                <?php endif ?>
            </td>
        </tr>
        <?php if ($res['created_at'] > '2024-06-18') : ?>
            <?php if (!empty($res_hari_realisasi)) : ?>
                <?php $nomor = 2; ?>
                <?php foreach ($res_hari_realisasi as $key) : ?>
                    <tr>
                        <td align="center">

                        </td>
                        <td>
                            Uang makan + parkir + meal hari ke <?= $nomor++ ?>
                        </td>
                        <td align="right">
                            <?= number_format($key['uang_makan']) ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            <?php endif ?>
        <?php else : ?>
            <?php if (!empty($res2)) : ?>
                <?php $nomor = 2; ?>
                <?php foreach ($res2 as $key) : ?>
                    <tr>
                        <td align="center">

                        </td>
                        <td>
                            Uang makan + parkir + meal hari ke <?= $nomor++ ?>
                        </td>
                        <td align="right">
                            <?= number_format($key['uang_makan']) ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            <?php endif ?>
        <?php endif ?>

        <tr>
            <td align="center">
                5
            </td>
            <td>
                Uang kernet
            </td>
            <td align="right">
                <?php if ($res['created_at'] > '2024-06-18') : ?>
                    <?php if ($realisasi['uang_kernet'] == 0) : ?>
                        -
                    <?php else : ?>
                        <?= number_format($realisasi['uang_kernet']) ?>
                    <?php endif ?>
                <?php else : ?>
                    <?php if ($res['uang_kernet'] == 0) : ?>
                        -
                    <?php else : ?>
                        <?= number_format($res['uang_kernet']) ?>
                    <?php endif ?>
                <?php endif ?>

            </td>
        </tr>
        <?php if ($res['created_at'] > '2024-06-18') : ?>
            <?php if (!empty($res_hari_realisasi)) : ?>
                <?php $nomor = 2; ?>
                <?php foreach ($res_hari_realisasi as $key) : ?>
                    <tr>
                        <td align="center">

                        </td>
                        <td>
                            Uang kernet hari ke <?= $nomor++ ?>
                        </td>
                        <td align="right">
                            <?php if ($key['uang_kernet'] == 0) : ?>
                                -
                            <?php else : ?>
                                <?= number_format($key['uang_kernet']) ?>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            <?php endif ?>
        <?php else : ?>
            <?php if (!empty($res2)) : ?>
                <?php $nomor = 2; ?>
                <?php foreach ($res2 as $key) : ?>
                    <tr>
                        <td align="center">

                        </td>
                        <td>
                            Uang kernet hari ke <?= $nomor++ ?>
                        </td>
                        <td align="right">
                            <?php if ($key['uang_kernet'] == 0) : ?>
                                -
                            <?php else : ?>
                                <?= number_format($key['uang_kernet']) ?>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            <?php endif ?>
        <?php endif ?>
        <tr>
            <td align="center">
                6
            </td>
            <td>
                Biaya perjalanan
            </td>
            <td align="right">
                -
            </td>
        </tr>
        <?php if ($res['created_at'] > '2024-06-18') : ?>
            <?php if (!empty($res_hari_realisasi)) : ?>
                <?php $nomor = 2; ?>
                <?php foreach ($res_hari_realisasi as $key) : ?>
                    <tr>
                        <td align="center">

                        </td>
                        <td>
                            Biaya perjalanan hari ke <?= $nomor++ ?>
                        </td>
                        <td align="right">
                            <?php if ($key['biaya_perjalanan'] == 0) : ?>
                                -
                            <?php else : ?>
                                <?= number_format($key['biaya_perjalanan']) ?>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            <?php endif ?>
        <?php else : ?>
            <?php if (!empty($res2)) : ?>
                <?php $nomor = 2; ?>
                <?php foreach ($res2 as $key) : ?>
                    <tr>
                        <td align="center">

                        </td>
                        <td>
                            Biaya perjalanan hari ke <?= $nomor++ ?>
                        </td>
                        <td align="right">
                            <?php if ($key['biaya_perjalanan'] == 0) : ?>
                                -
                            <?php else : ?>
                                <?= number_format($key['biaya_perjalanan']) ?>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            <?php endif ?>
        <?php endif ?>
        <tr>
            <td align="center">
                7
            </td>
            <td>
                Demmurade
            </td>
            <td align="right">
                <?php if ($realisasi['uang_demmurade'] == 0) : ?>
                    -
                <?php else : ?>
                    <?= number_format($realisasi['uang_demmurade']) ?>
                <?php endif ?>
            </td>
        </tr>
        <tr>
            <td align="center">
                8
            </td>
            <td>
                Koordinasi
            </td>
            <td align="right">
                <?php if ($realisasi['uang_koordinasi'] == 0) : ?>
                    -
                <?php else : ?>
                    <?= number_format($realisasi['uang_koordinasi']) ?>
                <?php endif ?>
            </td>
        </tr>
        <tr>
            <td align="center">
                9
            </td>
            <td>
                Multidrop
            </td>
            <td align="right">
                <?php if ($res['uang_multidrop'] == 0) : ?>
                    -
                <?php else : ?>
                    <?= number_format($res['uang_multidrop']) ?>
                <?php endif ?>
            </td>
        </tr>
        <tr>
            <td align="center">
                10
            </td>
            <td>
                Biaya Penyebrangan
            </td>
            <td align="right">
                <?php if ($realisasi['biaya_penyebrangan'] == 0) : ?>
                    -
                <?php else : ?>
                    <?= number_format($realisasi['biaya_penyebrangan']) ?>
                <?php endif ?>
            </td>
        </tr>
        <tr>
            <td align="center">
                11
            </td>
            <td>
                Biaya Lain
                <br>
                <small>
                    Catatan :
                    <?php if ($realisasi['catatan_biaya_lain'] != NULL) : ?>
                        <p><?= $realisasi['catatan_biaya_lain'] ?></p>
                    <?php else : ?>
                        -
                    <?php endif ?>
                </small>
            </td>
            <td align="right">
                <?php if ($realisasi['biaya_lain'] == 0) : ?>
                    -
                <?php else : ?>
                    <?= number_format($realisasi['biaya_lain']) ?>
                <?php endif ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <b>TOTAL</b>
            </td>
            <td align="right">
                <b>
                    <?= number_format($realisasi['total_realisasi']) ?>
                </b>
            </td>
        </tr>
    </table>
    <br>
    <table border="1" width="50%" style="border-collapse: collapse; float:right;" cellpadding="5" cellspacing="0" align="right">
        <tr>
            <td width="60%">
                <b>
                    Yang dibayarkan
                </b>
            </td>
            <td align="right">
                <b>
                    <?= number_format($res['yang_dibayarkan']) ?>
                </b>
            </td>
        </tr>
        <tr>
            <td width="60%">
                <b>
                    Realisasi
                </b>
            </td>
            <td align="right">
                <b>
                    <?= number_format($realisasi['total_realisasi']) ?>
                </b>
            </td>
        </tr>
        <tr>
            <td width="60%">
                <b>
                    Selisih lebih / kurang
                </b>
            </td>
            <td align="right">
                <?php
                $dibayarkan = $res['yang_dibayarkan'];
                $total_realisasi  = $realisasi['total_realisasi'];

                if ($dibayarkan > $total_realisasi) {
                    $selisih = number_format($dibayarkan - $total_realisasi);
                } elseif ($total_realisasi > $dibayarkan) {
                    $selisih = number_format($dibayarkan - $total_realisasi);
                } elseif ($dibayarkan == $total_realisasi) {
                    $selisih = "-";
                }
                ?>
                <b>
                    <?= $selisih ?>
                </b>
            </td>
        </tr>
    </table>
    <br>
    <table border="0" width="50%" style="border-collapse: collapse; float:right;" cellpadding="5" cellspacing="0" align="right">
        <tr>
            <td colspan="2">
                <b>
                    Catatan
                </b>
                <br>
                <small>
                    <?php if ($realisasi['catatan'] != NULL) : ?>
                        <?= $realisasi['catatan'] ?>
                    <?php else : ?>
                        -
                    <?php endif ?>
                </small>
            </td>
        </tr>
    </table>
</div>
<br>
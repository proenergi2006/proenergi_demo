<style>
    table {
        font-size:10pt;
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
<?php
if ($res03['top_poc'] == "COD" || $res03['top_poc'] == "CBD") {
    $due_date = "-";
} else {
    $due_date = date("d M Y", strtotime("+" . $res03['top_poc'] . "days", strtotime($res['tgl_invoice'])));
}
?>
<htmlpagefooter name="myHTMLFooter1">
    <p style="margin:0; text-align:right;">
        <barcode code="<?php echo $barcod; ?>" type="C39" size="0.8" />
    </p>
    <p style="margin:0; text-align:right; font-size:6pt; padding-right:70px;"><?php echo $barcod; ?></p>
    <p style="margin:0; text-align:right; font-size:7pt;"><i>(This form is valid with sign by computerized system)</i></p>
    <p style="margin:0; text-align:right; font-size:6pt;">Printed by <?php echo $printe; ?></p>
</htmlpagefooter>
<sethtmlpagefooter name="myHTMLFooter1" page="ALL" value="on" show-this-page="1" />
<table border="0" width="100%">
    <tr>
        <td width="30%">
            <div style="padding:0;"><img src="<?php echo BASE_IMAGE . "/logo-kiri-penawaran.png"; ?>" width="15%" /></div>
        </td>
        <td align="right">
            <h2>SALES INVOICE</h2>
        </td>
    </tr>
    <tr>
        <td>
            &nbsp;
        </td>
    </tr>
    <tr>
        <td>
            <p>
                <b>
                    PT PRO ENERGI
                </b>
            </p>
            <br>
            <p>
                GRAHA IRAMA BUILDING LT.6 UNIT G
                JL. HR RASUNA SAID KAV 1-2
                KUNINGAN TIMUR JAKARTA SELATAN
            </p>
        </td>
    </tr>
</table>
<br>
<table width="100%" border="0">
    <tr>
        <td width="25%">
            <b>
                Bill To
            </b>
        </td>
        <td width="5%">

        </td>
        <td width="25%">
            <b>
                Ship To
            </b>
        </td>
        <td width="5%">

        </td>
        <td rowspan="2" valign="top">
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
                <?php if ($res['tgl_invoice'] >= "2024-11-18") : ?>
                    <tr>
                        <td align="right" width="50%">
                            <b>
                                Invoice
                            </b>
                        </td>
                        <td align="right" width="5%">
                            :
                        </td>
                        <td align="right">
                            <?php if ($res['is_cetakan'] == 1 || $res['is_cetakan'] == NULL) : ?>
                                <?= $res['no_invoice'] ?>
                            <?php else : ?>
                                <?= $res['no_invoice_customer'] ?>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php else : ?>
                    <tr>
                        <td align="right" width="50%">
                            <b>
                                Invoice
                            </b>
                        </td>
                        <td align="right" width="5%">
                            :
                        </td>
                        <td align="right">
                            <?= $res['no_invoice'] ?>
                        </td>
                    </tr>
                <?php endif ?>
                <tr>
                    <td align="right" width="50%">
                        <b>
                            Invoice Date
                        </b>
                    </td>
                    <td align="right" width="5%">
                        :
                    </td>
                    <td align="right">
                        <?= date("d M Y", strtotime($res['tgl_invoice'])) ?>
                    </td>
                </tr>
                <tr>
                    <td align="right" width="50%">
                        <b>
                            PO. NO
                        </b>
                    </td>
                    <td align="right" width="5%">
                        :
                    </td>
                    <td align="right">
                        <?= $res03['nomor_poc'] ?>
                    </td>
                </tr>
                <tr>
                    <td align="right" width="50%">
                        <b>
                            Due Date
                        </b>
                    </td>
                    <td align="right" width="5%">
                        :
                    </td>
                    <td align="right">
                        <?= $due_date ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td valign="top">
            <?= $res['nm_customer'] ?>
            <br>
            <?= $res['alamat_customer'] ?>
            <br>
            <?= $res['nama_prov'] ?>
            <?= $res['nama_kab'] ?>
            <?= $res['kode_pos'] ?>
        </td>
        <td></td>
        <td valign="top">
            <?= $res03['wilayah_angkut'] ?>
            <?= $res03['alamat_survey'] ?>
            <br>
            <?= $res03['provinsi_angkut'] ?>
            <?= $res03['kab_angkut'] ?>
        </td>
        <td></td>
    </tr>
</table>
<br>
<div class="container-fluid" style="border:1px solid black;">
    <table width="100%" cellspacing="0" style="padding: 5px;">
        <tr>
            <td width="25%" align="center" rowspan="2">
                <b>
                    Terms
                </b>
                <br>
                <?php if ($res03['top_poc'] == "COD" || $res03['top_poc'] == "CBD") : ?>
                    <?= $res03['top_poc'] ?>
                <?php else : ?>
                    NET <?= $res03['top_poc'] ?>
                <?php endif ?>
            </td>
            <td rowspan="2">
                &nbsp;
            </td>
            <td width="25%" align="center" rowspan="2">
                <b>
                    FOB
                </b>
                <br>
                -
            </td>
            <td rowspan="2">
                &nbsp;
            </td>
            <td width="25%" align="center" rowspan="2">
                <b>
                    Ship Via
                </b>
                <br>
                PT. Pro Energi
            </td>
            <td rowspan="2">
                &nbsp;
            </td>
            <td width="25%" align="center" rowspan="2">
                <b>
                    Ship Date
                </b>
                <br>
                <?= date("d M Y", strtotime($res03['tanggal_kirim'])) ?>
            </td>
        </tr>
    </table>
</div>
<table width="100%" style="border: 1px solid black; border-collapse: collapse; border-top:none;" cellspacing="0" cellpadding="5">
    <tr>
        <th align="center" class="b2 b3">
            <b>
                Item Description
            </b>
        </th>
        <th align="center" class="b2 b3">
            <b>
                Item Unit
            </b>
        </th>
        <th align="center" class="b2 b3">
            <b>
                Qty
            </b>
        </th>
        <th align="center" class="b2 b3">
            <b>
                Unit Price
            </b>
        </th>
        <th align="center" class="b2 b3">
            <b>
                Discount
            </b>
        </th>
        <th align="center" class="b2 b3">
            <b>
                Tax
            </b>
        </th>
        <th align="center" class="b2 b3">
            <b>
                Amount
            </b>
        </th>
    </tr>
    <?php
    $sub_total = 0;
    $total_vol_kirim = 0;
    ?>
    <tbody>
        <?php foreach ($res02 as $key) : ?>
            <?php
            $volume       = (int)$key['vol_kirim'];
            $total_vol_kirim += $volume;

            if ($tipe == 'pbbkb') {
                $decode = json_decode($key['detail_rincian'], true);
                foreach ($decode as $d) {
                    $rincian = $d['rincian'];
                    if ($rincian == "Harga Dasar") {
                        $harga_asli = $d['biaya'];
                    }
                    if ($rincian == "Ongkos Angkut") {
                        $ongkos_angkut = $d['biaya'];
                    }
                    if ($rincian == "PPN") {
                        $ppn = $d['biaya'];
                        $nilai_ppn = $d['nilai'];
                    }
                    if ($rincian == "PBBKB") {
                        $pbbkb = $d['biaya'];
                        $nilai_pbbkb = $d['nilai'];
                        if ($res03['gabung_pbbkb'] == 1 || $nilai_pbbkb == 0 || $res03['gabung_pbbkboa'] == 1) {
                            $ket_pbbkb = "PBBKB";
                        } else {
                            if ($pbbkb == 0) {
                                $ket_pbbkb = "PBBKB";
                            } else {
                                $ket_pbbkb = "PBBKB";
                                // $ket_pbbkb = "PBBKB " . $nilai_pbbkb . "%";
                            }
                        }
                    }

                    if ($res03['all_in'] == 1) {
                        $harga = $res03['harga_kirim'];
                        $total_harga  = $harga * $total_vol_kirim;
                        $total_harga_pbbkb  = $pbbkb * $total_vol_kirim;
                    } else {
                        if ($res03['gabung_oa'] == '1') {
                            $harga = $harga_asli + $ongkos_angkut;
                            $total_harga  = $harga * $total_vol_kirim;
                            $total_harga_pbbkb  = $pbbkb * $total_vol_kirim;
                        } elseif ($res03['gabung_pbbkb'] == '1') {
                            $harga = $harga_asli + $ongkos_angkut;
                            $total_harga  = $harga * $total_vol_kirim;
                            $total_harga_pbbkb  = $pbbkb * $total_vol_kirim;
                        } elseif ($res03['gabung_pbbkboa'] == '1') {
                            $harga = $harga_asli + $ongkos_angkut;
                            $total_harga  = $harga * $total_vol_kirim;
                            $total_harga_pbbkb  = $pbbkb * $total_vol_kirim;
                        } else {
                            $harga = $harga_asli + $ongkos_angkut;
                            $total_harga  = $harga * $total_vol_kirim;
                            $total_harga_pbbkb  = $pbbkb * $total_vol_kirim;
                        }
                    }
                }

                $sub_total = $total_harga + $total_harga_pbbkb;

                if ($res03['pembulatan'] == 1) {
                    if ($res03['all_in'] == 1) {
                        $total_ppn = (round(($harga_asli + $ongkos_angkut) * $nilai_ppn / 100)) * $total_vol_kirim;
                        $grand_total = $sub_total;
                    } elseif ($res03['gabung_pbbkb'] == 1 || $res03['gabung_pbbkboa'] == 1) {
                        $total_pbbkb = 0;
                        $total_ppn = (round(($harga_asli + $ongkos_angkut) * $nilai_ppn / 100)) * $total_vol_kirim;
                        if ($tipe == 'default') {
                            $grand_total = $sub_total + $total_ppn + $total_pbbkb;
                        } else {
                            $grand_total = $sub_total + $total_ppn;
                        }
                    } elseif ($res03['gabung_oa'] == 1) {
                        $total_pbbkb = $pbbkb * $total_vol_kirim;
                        $total_ppn = (round(($harga_asli + $ongkos_angkut) * $nilai_ppn / 100)) * $total_vol_kirim;
                        if ($tipe == 'default') {
                            $grand_total = $sub_total + $total_ppn + $total_pbbkb;
                        } else {
                            $grand_total = $sub_total + $total_ppn;
                        }
                    } else {
                        $total_pbbkb = round($harga_asli * $nilai_pbbkb / 100) * $total_vol_kirim;
                        $total_ppn = (round(($harga_asli + $ongkos_angkut) * $nilai_ppn / 100)) * $total_vol_kirim;
                        if ($tipe == 'default') {
                            $grand_total = $sub_total + $total_ppn + $total_pbbkb;
                        } else {
                            $grand_total = $sub_total + $total_ppn;
                        }
                    }
                } elseif ($res03['pembulatan'] == 0) {
                    if ($res03['all_in'] == 1) {
                        $total_ppn = round((($harga_asli + $ongkos_angkut) * $nilai_ppn / 100), 2) * $total_vol_kirim;
                        $grand_total = $sub_total;
                    } elseif ($res03['gabung_pbbkb'] == 1 || $res03['gabung_pbbkboa'] == 1) {
                        $total_pbbkb = 0;
                        $total_ppn = round((($harga_asli + $ongkos_angkut) * $nilai_ppn / 100), 2) * $total_vol_kirim;
                        if ($tipe == 'default') {
                            $grand_total = $sub_total + $total_ppn + $total_pbbkb;
                        } else {
                            $grand_total = $sub_total + $total_ppn;
                        }
                    } elseif ($res03['gabung_oa'] == 1) {
                        $total_pbbkb = $pbbkb * $total_vol_kirim;
                        $total_ppn = round((($harga_asli + $ongkos_angkut) * $nilai_ppn / 100), 2) * $total_vol_kirim;
                        if ($tipe == 'default') {
                            $grand_total = $sub_total + $total_ppn + $total_pbbkb;
                        } else {
                            $grand_total = $sub_total + $total_ppn;
                        }
                    } else {
                        $total_pbbkb = ($harga_asli * $nilai_pbbkb / 100) * $total_vol_kirim;
                        $total_ppn = round((($harga_asli + $ongkos_angkut) * $nilai_ppn / 100), 2) * $total_vol_kirim;
                        if ($tipe == 'default') {
                            $grand_total = $sub_total + $total_ppn + $total_pbbkb;
                        } else {
                            $grand_total = $sub_total + $total_ppn;
                        }
                    }
                } elseif ($res03['pembulatan'] == 2) {
                    if ($res03['all_in'] == 1) {
                        $total_ppn = round((($harga_asli + $ongkos_angkut) * $nilai_ppn / 100), 4) * $total_vol_kirim;
                        $grand_total = $sub_total;
                    } elseif ($res03['gabung_pbbkb'] == 1 || $res03['gabung_pbbkboa'] == 1) {
                        $total_pbbkb = 0;
                        $total_ppn = round((($harga_asli + $ongkos_angkut) * $nilai_ppn / 100), 4) * $total_vol_kirim;
                        if ($tipe == 'default') {
                            $grand_total = $sub_total + $total_ppn + $total_pbbkb;
                        } else {
                            $grand_total = $sub_total + $total_ppn;
                        }
                    } elseif ($res03['gabung_oa'] == 1) {
                        $total_pbbkb = $pbbkb * $total_vol_kirim;
                        $total_ppn = round((($harga_asli + $ongkos_angkut) * $nilai_ppn / 100), 4) * $total_vol_kirim;
                        if ($tipe == 'default') {
                            $grand_total = $sub_total + $total_ppn + $total_pbbkb;
                        } else {
                            $grand_total = $sub_total + $total_ppn;
                        }
                    } else {
                        $total_pbbkb = ($harga_asli * $nilai_pbbkb / 100) * $total_vol_kirim;
                        $total_ppn = round((($harga_asli + $ongkos_angkut) * $nilai_ppn / 100), 4) * $total_vol_kirim;
                        if ($tipe == 'default') {
                            $grand_total = $sub_total + $total_ppn + $total_pbbkb;
                        } else {
                            $grand_total = $sub_total + $total_ppn;
                        }
                    }
                }
            } else {
                $decode = json_decode($key['detail_rincian'], true);
                foreach ($decode as $d) {
                    $rincian = $d['rincian'];
                    if ($rincian == "Harga Dasar") {
                        $harga_asli = $d['biaya'];
                    }
                    if ($rincian == "Ongkos Angkut") {
                        $ongkos_angkut = $d['biaya'];
                    }
                    if ($rincian == "PPN") {
                        $ppn = $d['biaya'];
                        $nilai_ppn = $d['nilai'];
                    }
                    if ($rincian == "PBBKB") {
                        $pbbkb = $d['biaya'];
                        $nilai_pbbkb = $d['nilai'];
                        if ($res03['gabung_pbbkb'] == 1 || $nilai_pbbkb == 0 || $res03['gabung_pbbkboa'] == 1) {
                            $ket_pbbkb = "PBBKB";
                        } else {
                            if ($pbbkb == 0) {
                                $ket_pbbkb = "PBBKB";
                            } else {
                                $ket_pbbkb = "PBBKB";
                                // $ket_pbbkb = "PBBKB " . $nilai_pbbkb . "%";
                            }
                        }
                    }

                    if ($res03['all_in'] == 1) {
                        $harga = $res03['harga_kirim'];
                        $total_harga  = $harga * $total_vol_kirim;
                    } else {
                        if ($res03['gabung_oa'] == '1') {
                            $harga = $harga_asli + $ongkos_angkut;
                            $total_harga  = $harga * $total_vol_kirim;
                        } elseif ($res03['gabung_pbbkb'] == '1') {
                            $harga = $harga_asli + $pbbkb + $ongkos_angkut;
                            $total_harga  = $harga * $total_vol_kirim;
                        } elseif ($res03['gabung_pbbkboa'] == '1') {
                            $harga = $harga_asli + $pbbkb + $ongkos_angkut;
                            $total_harga  = $harga * $total_vol_kirim;
                        } else {
                            $harga = $harga_asli + $ongkos_angkut;
                            $total_harga  = $harga * $total_vol_kirim;
                        }
                    }
                }

                $sub_total = $total_harga;

                if ($res03['pembulatan'] == 1) {
                    if ($res03['all_in'] == 1) {
                        $total_ppn = $ppn * $total_vol_kirim;
                        $grand_total = $sub_total;
                    } elseif ($res03['gabung_pbbkb'] == 1 || $res03['gabung_pbbkboa'] == 1) {
                        $total_pbbkb = 0;
                        $total_ppn = $ppn * $total_vol_kirim;
                        if ($tipe == 'default') {
                            $grand_total = $sub_total + $total_ppn + $total_pbbkb;
                        } else {
                            $grand_total = $sub_total + $total_ppn;
                        }
                    } elseif ($res03['gabung_oa'] == 1) {
                        $total_pbbkb = $pbbkb * $total_vol_kirim;
                        $total_ppn = $ppn * $total_vol_kirim;
                        if ($tipe == 'default') {
                            $grand_total = $sub_total + $total_ppn + $total_pbbkb;
                        } else {
                            $grand_total = $sub_total + $total_ppn;
                        }
                    } else {
                        $total_pbbkb = round($harga_asli * $nilai_pbbkb / 100) * $total_vol_kirim;
                        $total_ppn = $ppn * $total_vol_kirim;
                        if ($tipe == 'default') {
                            $grand_total = $sub_total + $total_ppn + $total_pbbkb;
                        } else {
                            $grand_total = $sub_total + $total_ppn;
                        }
                    }
                } else {
                    if ($res03['all_in'] == 1) {
                        $total_ppn = $ppn * $total_vol_kirim;
                        $grand_total = $sub_total;
                    } elseif ($res03['gabung_pbbkb'] == 1 || $res03['gabung_pbbkboa'] == 1) {
                        $total_pbbkb = 0;
                        $total_ppn = $ppn * $total_vol_kirim;
                        if ($tipe == 'default') {
                            $grand_total = $sub_total + $total_ppn + $total_pbbkb;
                        } else {
                            $grand_total = $sub_total + $total_ppn;
                        }
                    } elseif ($res03['gabung_oa'] == 1) {
                        $total_pbbkb = $pbbkb * $total_vol_kirim;
                        $total_ppn = $ppn * $total_vol_kirim;
                        if ($tipe == 'default') {
                            $grand_total = $sub_total + $total_ppn + $total_pbbkb;
                        } else {
                            $grand_total = $sub_total + $total_ppn;
                        }
                    } else {
                        $total_pbbkb = ($harga_asli * $nilai_pbbkb / 100) * $total_vol_kirim;
                        $total_ppn = $ppn * $total_vol_kirim;
                        if ($tipe == 'default') {
                            $grand_total = $sub_total + $total_ppn + $total_pbbkb;
                        } else {
                            $grand_total = $sub_total + $total_ppn;
                        }
                    }
                }
            }
            ?>
        <?php endforeach ?>

        <?php if ($tipe == 'pbbkb') : ?>

            <?php if ($pbbkb > 0) : ?>
                <tr style="border: 1px solid black;">
                    <td height="10px" valign="top" align="center">
                        <?= $res03['produk'] ?>
                        <br>
                        PBBKB
                    </td>
                    <td valign="top" align="center">
                        Liter
                        <br>
                        Liter
                    </td>
                    <td valign="top" align="center">
                        <?= number_format($total_vol_kirim) ?>
                        <br>
                        <?= number_format($total_vol_kirim) ?>
                    </td>
                    <td valign="top" align="center">

                        <?php if ($res03['pembulatan'] == 2) : ?>
                            <?= number_format($harga, 4) ?>
                            <br>
                            <?= number_format($pbbkb, 4) ?>
                        <?php elseif ($res03['pembulatan'] == 0) : ?>
                            <?= number_format($harga, 2) ?>
                            <br>
                            <?= number_format($pbbkb, 2) ?>
                        <?php else : ?>
                            <?= (fmod($harga, 1) !== 0.0000) ? number_format($harga, 4, ".", ",") : number_format($harga) ?>
                            <br>
                            <?= (fmod($pbbkb, 1) !== 0.0000) ? number_format($pbbkb, 4, ".", ",") : number_format($pbbkb)  ?>
                        <?php endif ?>

                    </td>
                    <td valign="top" align="center">
                        0
                        <br>
                        0
                    </td>
                    <td valign="top" align="center">
                        -
                        <br>
                        -
                    </td>
                    <td valign="top" align="right">
                        <?= number_format($total_harga) ?>
                        <br>
                        <?= number_format($total_harga_pbbkb) ?>
                    </td>
                </tr>
            <?php else : ?>
                <tr style="border: 1px solid black;">
                    <td height="10px" valign="top" align="center">
                        <?= $res03['produk'] ?>
                    </td>
                    <td valign="top" align="center">
                        Liter
                    </td>
                    <td valign="top" align="center">
                        <?= number_format($total_vol_kirim) ?>
                    </td>
                    <td valign="top" align="center">

                        <?php if ($res03['pembulatan'] == 2) : ?>
                            <?= number_format($harga, 4) ?>
                        <?php elseif ($res03['pembulatan'] == 0) : ?>
                            <?= number_format($harga, 2) ?>
                        <?php else : ?>
                            <?= number_format($harga) ?>
                        <?php endif ?>

                    </td>
                    <td valign="top" align="center">
                        0
                    </td>
                    <td valign="top" align="center">
                        -
                    </td>
                    <td valign="top" align="right">
                        <?= number_format($total_harga) ?>
                    </td>
                </tr>
            <?php endif ?>

        <?php else : ?>

            <tr style="border: 1px solid black;">
                <td height="10px" valign="top" align="center">
                    <?= $res03['produk'] ?>
                </td>
                <td valign="top" align="center">
                    Liter
                </td>
                <td valign="top" align="center">
                    <?= number_format($total_vol_kirim) ?>
                </td>
                <td valign="top" align="center">
                    <?php if ($res03['pembulatan'] == 2) : ?>
                        <?= number_format($harga, 4) ?>
                    <?php elseif ($res03['pembulatan'] == 0) : ?>
                        <?= number_format($harga, 2) ?>
                    <?php else : ?>
                        <?= (fmod($harga, 1) !== 0.0000) ? number_format($harga, 4, ".", ",") : number_format($harga, 0) ?>
                    <?php endif ?>
                </td>
                <td valign="top" align="center">
                    <?= number_format($res03['total_disc']) ?>
                </td>
                <td valign="top" align="center">
                    -
                </td>
                <td valign="top" align="right">
                    <?= number_format($total_harga) ?>
                </td>
            </tr>
        <?php endif ?>
        <tr>
            <td colspan="6" class="b1 b2" align="right">
                <b>
                    Sub Total
                </b>
            </td>
            <td class="b1" align="right">
                <?php
                echo number_format($sub_total);
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" class="b2" align="right">
                <b>
                    Discount
                </b>
            </td>
            <td align="right">
                0
            </td>
        </tr>
         <tr>
            <td colspan="6" class="b2" align="right">
                <b>
                    DPP
                </b>
            </td>
            <td align="right">
                <?= number_format(($sub_total*11)/12) ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" class="b2" align="right">
                <b>
                    PPN
                </b>
            </td>
            <td align="right">
                <?php if ($res03['all_in'] == 1) : ?>
                    0
                <?php else : ?>
                    <?= number_format($total_ppn) ?>
                <?php endif ?>
            </td>
        </tr>
        <?php if ($tipe == 'default') : ?>
            <tr>
                <td colspan="6" class="b2" align="right">
                    <b>
                        <?= $ket_pbbkb ?>
                    </b>
                </td>
                <td align="right">
                    <?= number_format($total_pbbkb) ?>
                </td>
            </tr>
        <?php else : ?>
            <tr>
                <td colspan="6" class="b2" align="right">
                    <b>
                        PBBKB
                    </b>
                </td>
                <td align="right">
                    0
                </td>
            </tr>
        <?php endif ?>
        <tr>
            <td colspan="6" class="b2" align="right">
                <b>
                    Total Invoice
                </b>
            </td>
            <td align="right">
                <?= number_format($grand_total) ?>
            </td>
        </tr>
        <?php if ($res['total_bayar']!= 0): ?>
                          
            <tr>
                <td colspan="6" class="b2" align="right">
                    <b>
                        Payment
                    </b>
                </td>
                <td align="right">
                    <?= number_format($res['total_bayar']) ?>
                </td>
            </tr>
        <?php endif ?>
    </tbody>
</table>
<table width="100%" style="margin-top:5px;" cellspacing="0" cellpadding="5">
    <tr>
        <td width="10%">
            Say
        </td>
        <td align="left" class="b1 b2 b3 b4">
            <b>
                <?php echo terbilang($grand_total) ?>
            </b>
        </td>
    </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr>
        <td width="60%">Description :</td>
        <td rowspan="7" align="center">
            <?= ucwords($approval) ?>
            <br>
            <br>
            <hr style="height: 3px; border: 0px solid black; width:50%; margin:0 auto;">
            <span>
                <?= ucwords($jabatan) ?>
            </span>
        </td>
    </tr>
    <tr>
        <td>
            Pembayaran dengan BG/CEK harap diatas namakan PT. PRO ENERGI
            <br>
            atau transfer ke :
        </td>
    </tr>
    <tr>
        <td>
            <table width="100%" border="0" cellspacing="0" cellpadding="3">
                <tr>
                    <td width="35%">
                        Nama
                    </td>
                    <td width="5%">
                        :
                    </td>
                    <td>
                        PT. PRO ENERGI
                    </td>
                </tr>
                <tr>
                    <?php if ($sess_wil == '6') : ?>
                        <td>
                            Bank Jtrust Indonesia
                        </td>
                        <td>
                            :
                        </td>
                        <td>
                            Cab. Sudirman, Jakarta
                        </td>
                    <?php else : ?>
                        <td>
                            Bank Rakyat Indonesia
                        </td>
                        <td>
                            :
                        </td>
                        <td>
                            Cab. Veteran
                        </td>
                    <?php endif ?>
                </tr>
                <tr>
                    <td>
                        No. Rekening
                    </td>
                    <td>
                        :
                    </td>
                    <td>
                        <?php if ($sess_wil == '6') : ?>
                            100 2083 604
                        <?php else : ?>
                            0329-01-003694-305
                        <?php endif ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            Harap mencantumkan : Nomor Invoice ini
        </td>
    </tr>
    <tr>
        <td>
            Pembayaran dengan BG/CEK dianggap sah, setelah dana cair di rekening kami
        </td>
    </tr>
    <tr>
        <td>
            <b>
                <i>
                    Mohon dicek kembali informasi dan nilai yang tercantum di dalam invoice, koreksi
                    invoice dapat dilakukan maksimal 1 (satu) minggu setelah invoice diterima.
                </i>
            </b>
        </td>
    </tr>
    <tr>
        <td>
            <b>
                <i>
                    Keterlambatan atas pembayaran yang telah jatuh tempo dikenakan denda
                    keterlambatan sebesar 0,05% per hari
                </i>
            </b>
        </td>
    </tr>
</table>
<hr style="height: 3px; border: 0px solid #D6D6D6; border-top-width: 1px;">
<table>
    <tr>
        <td>
            <?= $res['no_invoice'] ?>
        </td>
    </tr>
    <tr>
        <td>
            <?= date("d/m/Y", strtotime($res['tgl_invoice'])) ?>
        </td>
    </tr>
    <tr>
        <td>
            <?= $res['nm_customer'] ?>
        </td>
    </tr>
    <tr>
        <td>
            <?= number_format($total_vol_kirim) ?>
        </td>
    </tr>
    <tr>
        <td>
            <?= $res03['nomor_poc'] ?>
        </td>
    </tr>
    <tr>
        <td>
            <?= number_format($grand_total) ?>
        </td>
    </tr>
    <tr>
        <td>
            <?= $res['marketing'] ?>
        </td>
    </tr>
</table>
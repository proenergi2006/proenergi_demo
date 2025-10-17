<style>
    table {
        font-size: 9pt;
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
            <h2>PROFORMA INVOICE</h2>
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
        <td width="30%">
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
                <tr>
                    <td align="right" width="36%">
                        <b>
                            Invoice
                        </b>
                    </td>
                    <td align="right" width="5%">
                        :
                    </td>
                    <td align="right">
                        XX/XX/XX/XXX
                    </td>
                </tr>
                <tr>
                    <td align="right" width="36%">
                        <b>
                            Invoice Date
                        </b>
                    </td>
                    <td align="right" width="5%">
                        :
                    </td>
                    <td align="right">
                        XX-XX-XXXX
                    </td>
                </tr>
                <tr>
                    <td align="right" width="36%">
                        <b>
                            PO. NO
                        </b>
                    </td>
                    <td align="right" width="5%">
                        :
                    </td>
                    <td align="right">
                        <?= $row_poc['nomor_poc'] ?>
                    </td>
                </tr>
                <tr>
                    <td align="right" width="36%">
                        <b>
                            Due Date
                        </b>
                    </td>
                    <td align="right" width="5%">
                        :
                    </td>
                    <td align="right">
                        XX-XX-XXXX
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td valign="top">
            <?php if ($res_poc['alamat_billing'] == NULL || $res_poc['alamat_billing'] == "") : ?>
                <b><?= strtoupper($row_poc['nama_customer']) ?></b>
                <br>
                <?= strtoupper($row_poc['alamat_customer']) ?>
                <br>
                <?= strtoupper($row_poc['prov_customer']) ?>
                <?= strtoupper($row_poc['kab_customer']) ?>
                <?= $row_poc['kode_pos'] ?>
            <?php else : ?>
                <b><?= strtoupper($row_poc['nama_customer']) ?></b>
                <br>
                <?= strtoupper($row_poc['alamat_billing']) ?>
                <br>
                <?= strtoupper($row_poc['nama_prov_billing']) ?>
                <?= strtoupper($row_poc['nama_kab_billing']) ?>
                <?= $row_poc['postalcode_billing'] ?>
            <?php endif ?>
        </td>
        <td></td>
        <td valign="top">
            -
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
                <?php if ($row_poc['top_poc'] == "COD" || $row_poc['top_poc'] == "CBD") : ?>
                    <?= $row_poc['top_poc'] ?>
                <?php else : ?>
                    NET <?= $row_poc['top_poc'] ?>
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
                -
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
        <th width="25%" align="center" class="b2 b3">
            <b>
                Amount
            </b>
        </th>
    </tr>
    <?php
    $rincian = json_decode($row_poc['detail_rincian'], true);
    foreach ($rincian as $key) {
        if ($key['rincian'] == "Harga Dasar") {
            $harga_dasar    = $key['biaya'];
            $nilai_hsd      = $key['nilai'];
        }
        if ($key['rincian'] == "Ongkos Angkut") {
            $ongkos_angkut  = $key['biaya'];
            $nilai_oa       = $key['nilai'];
        }
        if ($key['rincian'] == "PPN") {
            $ppn            = $key['biaya'];
            $nilai_ppn      = $key['nilai'];
        }
        if ($key['rincian'] == "PBBKB") {
            $pbbkb          = $key['biaya'];
            $nilai_pbbkb    = $key['nilai'];
        }
    }

    if ($res03['all_in'] == 1) {
        $harga_asli = $harga_dasar + $ongkos_angkut;
        $sub_total_hsd = $harga_asli * $row_poc['volume_poc'];

        $harga_asli_pbbkb = $pbbkb;
        $sub_total_pbbkb = $harga_asli_pbbkb * $row_poc['volume_poc'];
    } else {
        if ($row_poc['biaya_ppn'] == "gabung_oa") {
            $harga_asli = $harga_dasar;
            $sub_total_hsd = $harga_asli * $row_poc['volume_poc'];

            $harga_asli_oa = $ongkos_angkut;
            $sub_total_oa = $harga_asli_oa * $row_poc['volume_poc'];

            $harga_asli_pbbkb = $pbbkb;
            $sub_total_pbbkb = $harga_asli_pbbkb * $row_poc['volume_poc'];
        } elseif ($row_poc['biaya_ppn'] == "gabung_pbbkb") {
            $harga_asli = $harga_dasar + $pbbkb;
            $sub_total_hsd = $harga_asli * $row_poc['volume_poc'];

            $harga_asli_oa = $ongkos_angkut;
            $sub_total_oa = $harga_asli_oa * $row_poc['volume_poc'];

            $harga_asli_pbbkb = $pbbkb;
            $sub_total_pbbkb = 0;
        } elseif ($row_poc['biaya_ppn'] == "gabung_pbbkboa") {
            $harga_asli = $harga_dasar + $pbbkb;
            $sub_total_hsd = $harga_asli * $row_poc['volume_poc'];

            $harga_asli_oa = $ongkos_angkut;
            $sub_total_oa = $harga_asli_oa * $row_poc['volume_poc'];

            $harga_asli_pbbkb = $pbbkb;
            $sub_total_pbbkb = 0;
        } else {
            $harga_asli = $harga_dasar;
            $sub_total_hsd = $harga_asli * $row_poc['volume_poc'];

            $harga_asli_oa = $ongkos_angkut;
            $sub_total_oa = $harga_asli_oa * $row_poc['volume_poc'];

            $harga_asli_pbbkb = $pbbkb;
            $sub_total_pbbkb = $harga_asli_pbbkb * $row_poc['volume_poc'];
        }
    }

    $sub_total = $sub_total_hsd + $sub_total_oa;
    $total_ppn = round($ppn * $row_poc['volume_poc']);
    $grand_total = $sub_total + $total_ppn + $sub_total_pbbkb;
    ?>

    <tbody>
        <tr style="border: 1px solid black;">
            <td height="10px" valign="top" align="center">
                <?= $row_poc['produk'] ?>
                <br>
                <br>
                Freight Cost
            </td>
            <td valign="top" align="center">
                <?= number_format($row_poc['volume_poc']) ?>
                <br>
                <br>
                <?= number_format($row_poc['volume_poc']) ?>
            </td>
            <td valign="top" align="center">
                <?php if ($row_poc['pembulatan'] == 2) : ?>
                    <?= (fmod($harga_asli, 1) !== 0.0000) ? number_format($harga_asli, 4, ".", ",") : number_format($harga_asli, 0) ?>
                <?php elseif ($row_poc['pembulatan'] == 0) : ?>
                    <?= (fmod($harga_asli, 1) !== 0.0000) ? number_format($harga_asli, 2, ".", ",") : number_format($harga_asli, 0) ?>
                <?php else : ?>
                    <?= (fmod($harga_asli, 1) !== 0.0000) ? number_format($harga_asli, 4, ".", ",") : number_format($harga_asli, 0) ?>
                <?php endif ?>
                <br>
                <br>
                <?php if ($row_poc['pembulatan'] == 2) : ?>
                    <?= (fmod($harga_asli_oa, 1) !== 0.0000) ? number_format($harga_asli_oa, 4, ".", ",") : number_format($harga_asli_oa, 0) ?>
                <?php elseif ($row_poc['pembulatan'] == 0) : ?>
                    <?= (fmod($harga_asli_oa, 1) !== 0.0000) ? number_format($harga_asli_oa, 2, ".", ",") : number_format($harga_asli_oa, 0) ?>
                <?php else : ?>
                    <?= (fmod($harga_asli_oa, 1) !== 0.0000) ? number_format($harga_asli_oa, 4, ".", ",") : number_format($harga_asli_oa, 0) ?>
                <?php endif ?>
            </td>
            <td valign="top" align="center">
                0
                <br>
                <br>
                0
            </td>
            <td valign="top" align="right">
                <?= number_format($sub_total_hsd) ?>
                <br>
                <br>
                <?= number_format($sub_total_oa) ?>
            </td>
        </tr>
        <tr>
            <td colspan="4" class="b1 b2" align="right">
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
            <td colspan="4" class="b2" align="right">
                <b>
                    Discount
                </b>
            </td>
            <td align="right">
                0
            </td>
        </tr>
        <tr>
            <td colspan="4" class="b2" align="right">
                <b>
                    DPP
                </b>
            </td>
            <td align="right">
                <?= number_format(($sub_total_hsd * 11) / 12) ?>
            </td>
        </tr>
        <tr>
            <td colspan="4" class="b2" align="right">
                <b>
                    PPN
                </b>
            </td>
            <td align="right">
                <?= number_format($total_ppn) ?>
            </td>
        </tr>
        <?php
        if ($row_poc['biaya_ppn'] == "gabung_pbbkb"):
        ?>
            <!-- <tr>
                <td colspan="4" class="b2" align="right">
                    <b>
                        PBBKB
                    </b>
                </td>
                <td align="right">
                    0
                </td>
            </tr> -->
        <?php else : ?>
            <?php if ($sub_total_pbbkb > 0) : ?>
                <tr>
                    <td colspan="4" class="b2" align="right">
                        <b>
                            PBBKB
                        </b>
                    </td>
                    <td align="right">
                        <?= number_format($sub_total_pbbkb) ?>
                    </td>
                </tr>
            <?php endif ?>
        <?php endif ?>
        <tr>
            <td colspan="4" class="b2" align="right">
                <b>
                    Total Invoice
                </b>
            </td>
            <td align="right">
                <?= number_format($grand_total) ?>
            </td>
        </tr>
    </tbody>
</table>
<table width="100%" style="margin-top:5px;" cellspacing="0" cellpadding="5">
    <tr>
        <td width="10%">
            Terbilang
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
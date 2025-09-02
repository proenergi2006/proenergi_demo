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
    $total_discount = 0;
    ?>
    <tbody>
        <?php foreach ($res02 as $key) : ?>
            <?php
            $volume       = (int)$key['vol_kirim'];
            $total_vol_kirim += $volume;
            $total_discount += (int)$key['discount'];

            if ($tipe == "split_oa") {
                $decode = json_decode($key['detail_rincian'], true);
                foreach ($decode as $d) {
                    $rincian = $d['rincian'];
                    if ($rincian == "PPN") {
                        $ppn = $d['biaya'];
                        $nilai_ppn = $d['nilai'];
                    }
                    if ($rincian == "Ongkos Angkut") {
                        $oa = $d['biaya'];
                        $nilai_oa = $d['nilai'];
                    }
                }
                $ket_pbbkb = "PBBKB";
                $produk = "Freight Cost";
                $harga_kirim = $oa;
                $total_harga = $harga_kirim * $total_vol_kirim;
                $sub_total = $total_harga;
                // if ($key['pembulatan'] == 1) {
                //     // $total_ppn = round($total_harga * $nilai_ppn / 100);
                //     $total_ppn = (round(($oa * $nilai_ppn) / 100)) * $total_vol_kirim;
                // } else {
                //     $total_ppn = (($oa * $nilai_ppn) / 100) * $total_vol_kirim;
                // }
                $total_ppn = (($oa * $nilai_ppn) / 100) * $total_vol_kirim;
                $hitung_ppn = ($oa * $nilai_ppn) / 100;
                $total_ppn = $hitung_ppn * $total_vol_kirim;
                // $total_ppn = $total_harga * $nilai_ppn / 100;
                $grand_total = $sub_total + $total_ppn;
            } elseif ($tipe == "split_pbbkb") {
                $ket_pbbkb = "PBBKB";
                $produk = "PBBKB";
                $harga_kirim = $key['harga_kirim'];
                $total_harga = $harga_kirim * $total_vol_kirim;
                $sub_total = $total_harga;
                $total_ppn = 0;
                $grand_total = $sub_total;
                $nilai_ppn = "";
            } elseif ($tipe == "harga_dasar") {
                $decode = json_decode($key['detail_rincian'], true);
                foreach ($decode as $d) {
                    $rincian = $d['rincian'];
                    if ($rincian == "PPN") {
                        $ppn = $d['biaya'];
                        $nilai_ppn = $d['nilai'];
                    }
                    if ($rincian == "Harga Dasar") {
                        $hsd = $d['biaya'];
                        $nilai_hsd = $d['nilai'];
                    }
                }
                $ket_pbbkb = "PBBKB";
                $produk = $res03['produk'];
                $harga_kirim = $hsd;
                $total_harga = $harga_kirim * $total_vol_kirim;
                $sub_total = $total_harga;
                // $hitung_ppn = $harga_kirim * $nilai_ppn / 100;
                // if ($key['pembulatan'] == 1) {
                //     $total_ppn = round($total_harga * $nilai_ppn / 100);
                // } else {
                //     $total_ppn = $total_harga * $nilai_ppn / 100;
                // }
                // if ($key['pembulatan'] == 1) {
                //     // $total_ppn = round($total_harga * $nilai_ppn / 100);
                //     $total_ppn = (round(($hsd  * $nilai_ppn) / 100)) * $total_vol_kirim;
                // } else {
                //     $total_ppn = (($hsd * $nilai_ppn) / 100) * $total_vol_kirim;
                // }
                $total_ppn = (($hsd * $nilai_ppn) / 100) * $total_vol_kirim;

                $grand_total = $sub_total + $total_ppn;
            } elseif ($tipe == "harga_dasar_oa") {
                $decode = json_decode($key['detail_rincian'], true);
                foreach ($decode as $d) {
                    $rincian = $d['rincian'];
                    if ($rincian == "PPN") {
                        $ppn = $d['biaya'];
                        $nilai_ppn = $d['nilai'];
                    }
                    if ($rincian == "Harga Dasar") {
                        $hsd = $d['biaya'];
                        $nilai_hsd = $d['nilai'];
                    }
                    if ($rincian == "Ongkos Angkut") {
                        $oa = $d['biaya'];
                        $nilai_oa = $d['nilai'];
                    }
                    if ($rincian == "PBBKB") {
                        $pbbkb = $d['biaya'];
                        $nilai_pbbkb = $d['nilai'];
                    }
                }

                // if ($key['gabung_pbbkb'] == 1) {
                //     $ket_pbbkb = "PBBKB";
                //     // $ket_pbbkb = "PBBKB " . $nilai_pbbkb . "%";
                //     $harga_kirim = $hsd + $pbbkb;
                //     $total_pbbkb = 0;
                // } else {
                //     // $ket_pbbkb = "PBBKB";
                //     $ket_pbbkb = "PBBKB " . $nilai_pbbkb . "%";
                //     $harga_kirim = $hsd;
                //     $total_pbbkb = $pbbkb * $total_vol_kirim;
                // }
                $ket_pbbkb = "PBBKB";
                $total_pbbkb = 0;
                $produk = $res03['produk'];
                $harga_kirim = $hsd + $oa;

                if ($key['discount'] > 0) {
                    $jumlah_harga = ($harga_kirim * $total_vol_kirim) - $total_discount;
                    $total_ppn = ($jumlah_harga * $nilai_ppn) / 100;
                    $total_harga = $jumlah_harga;
                } else {
                    $total_harga = $harga_kirim * $total_vol_kirim;
                    $hitung_ppn = ($harga_kirim * $nilai_ppn) / 100;
                    $total_ppn = $hitung_ppn * $total_vol_kirim;
                }
                $sub_total = $total_harga;
                // if ($key['pembulatan'] == 1) {
                //     // $total_ppn = round($total_harga * $nilai_ppn / 100);
                //     $total_ppn = (round((($hsd + $oa) * $nilai_ppn) / 100)) * $total_vol_kirim;
                // } else {
                //     $total_ppn = ((($hsd + $oa) * $nilai_ppn) / 100) * $total_vol_kirim;
                // }
                // $total_ppn = ((($hsd + $oa) * $nilai_ppn) / 100) * $total_vol_kirim;
                $grand_total = $sub_total + $total_ppn;
            } elseif ($tipe == "harga_dasar_pbbkb") {
                $decode = json_decode($key['detail_rincian'], true);
                foreach ($decode as $d) {
                    $rincian = $d['rincian'];
                    if ($rincian == "PPN") {
                        $ppn = $d['biaya'];
                        $nilai_ppn = $d['nilai'];
                    }
                    if ($rincian == "Harga Dasar") {
                        $hsd = $d['biaya'];
                        $nilai_hsd = $d['nilai'];
                    }
                    if ($rincian == "PBBKB") {
                        $pbbkb = $d['biaya'];
                        $nilai_pbbkb = $d['nilai'];
                    }
                }
                if ($key['gabung_pbbkb'] == 1) {
                    $ket_pbbkb = "PBBKB";
                    // $ket_pbbkb = "PBBKB " . $nilai_pbbkb . "%";
                    $harga_kirim = $hsd + $pbbkb;
                    $total_pbbkb = 0;
                } else {
                    // $ket_pbbkb = "PBBKB";
                    $ket_pbbkb = "PBBKB " . $nilai_pbbkb . "%";
                    $harga_kirim = $hsd;
                    $total_pbbkb = $pbbkb * $total_vol_kirim;
                }



                if ($key['discount'] > 0) {
                    $jumlah_harga = ($harga_kirim * $total_vol_kirim) - $total_discount;
                    $total_ppn = ($jumlah_harga * $nilai_ppn) / 100;
                    $total_harga = $jumlah_harga;
                } else {
                    $total_harga = $harga_kirim * $total_vol_kirim;
                    $hitung_ppn = ($harga_kirim * $nilai_ppn) / 100;
                    $total_ppn = $hitung_ppn * $total_vol_kirim;
                }
                $produk = $res03['produk'];
                $sub_total = $total_harga;
                // if ($key['pembulatan'] == 1) {
                //     $hitung_ppn = round(($harga_kirim * $nilai_ppn) / 100);
                // } else {
                //     $hitung_ppn = ($harga_kirim * $nilai_ppn) / 100;
                // }


                // $total_ppn = $total_harga * $nilai_ppn / 100;
                $grand_total = $sub_total + $total_ppn;
            }

            ?>
        <?php endforeach ?>

        <tr style="border: 1px solid black;">
            <td height="10px" valign="top" align="center">
                <?= $produk ?>
            </td>
            <td valign="top" align="center">
                Ltr
            </td>
            <td valign="top" align="center">
                <?= number_format($total_vol_kirim) ?>
            </td>
            <td valign="top" align="center">
                <?php if ($res03['pembulatan'] == 2) : ?>
                    <?= number_format($harga_kirim, 4) ?>
                <?php elseif ($res03['pembulatan'] == 0) : ?>
                    <?= number_format($harga_kirim, 2) ?>
                <?php else : ?>
                    <?= (fmod($harga_kirim, 1) !== 0.0000) ? number_format($harga_kirim, 4, ".", ",") : number_format($harga_kirim, 0) ?>
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
                    PPN
                </b>
            </td>
            <td align="right">
                <?= number_format($total_ppn) ?>
            </td>
        </tr>
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
                    <td>
                        Bank Rakyat Indonesia
                    </td>
                    <td>
                        :
                    </td>
                    <td>
                        Cab. Veteran
                    </td>
                </tr>
                <tr>
                    <td>
                        No. Rekening
                    </td>
                    <td>
                        :
                    </td>
                    <td>
                        0329-01-003694-305
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
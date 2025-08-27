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
$volume = (int)$res02['vol_kirim'];
$harga  = (int)$res02['harga_kirim'];
$ppn    = 11;

$total_harga    = number_format($volume * $harga);
$total_harga02  = $volume * $harga;

$total_ppn = ($total_harga02 * $ppn) / 100;
$grand_total = $total_harga02 + $total_ppn;

if ($res02['top_poc'] == "COD" || $res02['top_poc'] == "CBD") {
    $due_date = "-";
} else {
    $due_date = date("d M Y", strtotime("+" . $res02['top_poc'] . "days", strtotime($res['tgl_invoice'])));
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
<div class="container">
    <table border="0" width="100%">
        <tr>
            <td width="20%">
                <div style="padding:0;"><img src="<?php echo BASE_IMAGE . "/logo-kiri-penawaran.png"; ?>" width="15%" /></div>
            </td>
            <td width="45%">
                <table border="1" cellspacing="0" cellpadding="5" width="100%">
                    <tr>
                        <td align="center">
                            PT PRO ENERGI
                        </td>
                    </tr>
                    <tr>
                        <td>
                            GRAHA IRAMA BUILDING LT.6 UNIT G
                            JL. HR RASUNA SAID KAV 1-2
                            KUNINGAN TIMUR JAKARTA SELATAN
                        </td>
                    </tr>
                </table>
            </td>
            <td>

            </td>
            <td align="center">
                <h2>
                    SALES INVOICE
                    <hr style="height: 1px; border: 1px solid black; width:100%; margin:3 auto;">
                </h2>
            </td>
        </tr>
    </table>
</div>
<br>
<div class="container">
    <table border="0" width="100%">
        <tr>
            <th width="30%" rowspan="3">
                <table border="1" width="100%" cellspacing="0" cellpadding="5">
                    <tr>
                        <td align="center">
                            Bill To
                        </td>
                    </tr>
                    <tr>
                        <td height="100px" valign="top" align="left">
                            <?= $res['nm_customer'] ?>
                            <br>
                            <?= $res['alamat_customer'] ?>
                            <br>
                            <?= $res['nama_prov'] ?>
                            <?= $res['nama_kab'] ?>
                            <?= $res['kode_pos'] ?>
                        </td>
                    </tr>
                </table>
            </th>
            <th width="30%" rowspan="3">
                <table border="1" width="100%" cellspacing="0" cellpadding="5">
                    <tr>
                        <td align="center">
                            Ship To
                        </td>
                    </tr>
                    <tr>
                        <td height="100px" valign="top" align="left">
                            <?= $res02['wilayah_angkut'] ?>
                            <br>
                            <?= $res02['alamat_survey'] ?>
                            <br>
                            <?= $res02['provinsi_angkut'] ?>
                            <?= $res02['kab_angkut'] ?>
                        </td>
                    </tr>
                </table>
            </th>
            <th rowspan="3" width="40%" valign="top">
                <table width="100%" border="1" cellspacing="0" cellpadding="5">
                    <tr>
                        <th align="center">
                            Invoice Date
                        </th>
                        <th align="center">
                            Invoice No
                        </th>
                    </tr>
                    <tr>
                        <td align="center">
                            <?= date("d M Y", strtotime($res['tgl_invoice'])) ?>
                        </td>
                        <td align="center">
                            <?= $res['no_invoice'] ?>
                        </td>
                    </tr>
                </table>
                <br>
                <table width="50%" border="1" cellspacing="0" cellpadding="5">
                    <tr>
                        <th align="center">
                            Due Date
                        </th>
                    </tr>
                    <tr>
                        <td align="center">
                            <?= $due_date ?>
                        </td>
                    </tr>
                </table>
            </th>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
    </table>
</div>
<br>
<table width="100%" border="0">
    <tr>
        <td width="20%">
            <table border="1" width="100%" cellspacing="0" cellpadding="5">
                <tr>
                    <th>
                        Terms
                    </th>
                </tr>
                <tr>
                    <td align="center">
                        <?php if ($res02['top_poc'] == "COD" || $res02['top_poc'] == "CBD") : ?>
                            <?= $res02['top_poc'] ?>
                        <?php else : ?>
                            NET <?= $res02['top_poc'] ?>
                        <?php endif ?>
                    </td>
                </tr>
            </table>
        </td>
        <td width="20%">
            <table border="1" width="100%" cellspacing="0" cellpadding="5">
                <tr>
                    <th>
                        FOB
                    </th>
                </tr>
                <tr>
                    <td align="center">
                        &nbsp;
                    </td>
                </tr>
            </table>
        </td>
        <td width="20%">
            <table border="1" width="100%" cellspacing="0" cellpadding="5">
                <tr>
                    <th>
                        Ship Via
                    </th>
                </tr>
                <tr>
                    <td align="center">
                        PT. Pro Energi
                    </td>
                </tr>
            </table>
        </td>
        <td width="20%">
            <table border="1" width="100%" cellspacing="0" cellpadding="5">
                <tr>
                    <th>
                        Ship Date
                    </th>
                </tr>
                <tr>
                    <td align="center">
                        <?= date("d M Y", strtotime($res02['tanggal_kirim'])) ?>
                    </td>
                </tr>
            </table>
        </td>
        <td width="20%">
            <table border="1" width="100%" cellspacing="0" cellpadding="5">
                <tr>
                    <th>
                        PO. No.
                    </th>
                </tr>
                <tr>
                    <td align="center">
                        <?= $res02['nomor_poc'] ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<div style="clear:both"></div>
<table border="1" cellpadding="5" cellspacing="0" width="100%">
    <tr>
        <th align="center" class="b1 b3 b4" width="20%">Item Description</th>
        <th align="center" class="b1 b3 b4" width="20%">Item Unit</th>
        <th align="center" class="b1 b3 b4" width="5%">Qty</th>
        <th align="center" class="b1 b3 b4" width="10%">Unit Price</th>
        <th align="center" class="b1 b3 b4" width="8%">Disc %</th>
        <th align="center" class="b1 b3 b4" width="8%">Tax</th>
        <th align="center" class="b1 b2 b3 b4" width="10%">Amount</th>
    </tr>
    <tr>
        <td height="100px" valign="top" align="center">
            <?= $res02['produk'] ?>
        </td>
        <td valign="top" align="center">Ltr</td>
        <td valign="top" align="center"><?= number_format($res02['vol_kirim']) ?></td>
        <td valign="top" align="center"><?= number_format($harga) ?></td>
        <td valign="top" align="center">0</td>
        <td valign="top" align="center">-</td>
        <td valign="top" align="center">
            <?= $total_harga ?>
        </td>
    </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr>
        <th width="60%"></th>
        <th align="right">
            <table width="100%" border="1" cellspacing="0" cellpadding="5">
                <tr>
                    <td>Sub Total :</td>
                    <td>
                        <?php
                        echo $total_harga;
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Discount :</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>PPN <?= $ppn ?>% :</td>
                    <td>
                        <?= number_format($total_ppn) ?>
                    </td>
                </tr>
                <tr>
                    <td>PBBKB :</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>Total Invoice :</td>
                    <td><?= number_format($grand_total) ?></td>
                </tr>
                <tr>
                    <td>Payment :</td>
                    <td>
                        <?= number_format($res['total_bayar']) ?>
                    </td>
                </tr>
            </table>
        </th>
    </tr>
</table>
<br>
<table width="100%" border="0">
    <tr>
        <td width="10%">
            Say
        </td>
        <td>
            <table width="100%" border="1" cellspacing="0" cellpadding="5">
                <tr>
                    <th width="100%" align="left">
                        <?php echo terbilang($grand_total) ?>
                    </th>
                </tr>
            </table>
        </td>
    </tr>
</table>
<br>
<table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr>
        <td width="60%">Description :</td>
        <td rowspan="7" align="center">
            Isyanto Broto
            <br>
            <br>
            <hr style="height: 3px; border: 0px solid black; width:50%; margin:0 auto;">
            <span>
                Assistant Sales Manager
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
            <?= number_format($res02['vol_kirim']) ?>
        </td>
    </tr>
    <tr>
        <td>
            <?= $res02['nomor_poc'] ?>
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
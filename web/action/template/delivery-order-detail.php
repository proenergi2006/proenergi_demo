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

    #main {
        width: 400px;
        height: 100px;
        border: 1px solid #c3c3c3;
        display: flex;
        justify-content: space-between;
    }

    #main div {
        width: 70px;
        height: 70px;
    }
</style>
<htmlpagefooter name="myHTMLFooter1">
    <p style="margin:0; text-align:right;">
        <barcode code="<?= $barcod ?>" type="C39" size="0.8" />
    </p>
    <p style="margin:0; text-align:right; font-size:6pt; padding-right:70px;"><?= $barcod ?></p>
    <p style="margin:0; text-align:right; font-size:7pt;"><i>(This form is valid with sign by computerized system)</i></p>
    <p style="margin:0; text-align:right; font-size:6pt;">Printed by <?php echo $printe; ?></p>
</htmlpagefooter>
<sethtmlpagefooter name="myHTMLFooter1" page="ALL" value="on" show-this-page="1" />
<div class="container-fluid">
    <table class="table-bordered" border="0" width="100%">
        <tr>
            <td width="60%">
                <h1>PT PRO ENERGI</h1>
            </td>
            <td width="40%">

            </td>
        </tr>
        <tr>
            <td>
                GRAHA IRAMA BUILDING LT.6 UNIT G
                <br>
                JL. HR RASUNA SAID KAV 1-2
                <br>
                KUNINGAN TIMUR JAKARTA SELATAN
            </td>
            <td>
                <h2>Delivery Order</h2>
            </td>
        </tr>
        <tr>
            <td>
                <img src="<?php echo BASE_IMAGE . "/logo-kiri-penawaran.png"; ?>" width="20%" />
            </td>
            <td valign="top">
                <table class="table-bordered" border="0" width="100%">
                    <tr>
                        <td width="30%">
                            Delivery NO
                        </td>
                        <td width="5%">
                            :
                        </td>
                        <td>
                            <?= $res['no_do_acurate'] ? $res['no_do_acurate'] : $res['no_do_syop'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Delivery Date
                        </td>
                        <td>
                            :
                        </td>
                        <td>
                            <?= tgl_indo($res['tanggal_kirim']) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Ship Via
                        </td>
                        <td>
                            :
                        </td>
                        <td>
                            PT. Pro Energi
                        </td>
                    </tr>
                    <tr>
                        <td>
                            PO No
                        </td>
                        <td>
                            :
                        </td>
                        <td>
                            <?= $res['nomor_poc'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            LO No
                        </td>
                        <td>
                            :
                        </td>
                        <td>
                            <?= $res['nomor_lo_pr'] ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br>
    <table class="table-bordered" border="0" width="100%">
        <tr>
            <td width="40%">
                <h3>Bill To</h3>
            </td>
            <td width="20%"></td>
            <td width="40%">
                <h3>Ship To</h3>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <?= strtoupper($res['nama_customer']) ?>
                <br>
                <?= $res['jenis_usaha']; ?>
                <br>
                <?= $res['fullname']; ?>
            </td>
            <td></td>
            <td valign="top">
                <?= strtoupper($res['nama_area']); ?>
                <br>
                <?= strtoupper($alamat); ?>
                <br>
                <?= 'Wilayah OA : ' . strtoupper($res['wilayah_angkut']); ?>
            </td>
        </tr>
    </table>
</div>
<br>
<div style="clear:both"></div>

<table border="1" cellpadding="0" cellspacing="0" width="100%" class="tabel_rincian" style="margin-bottom:10px;">
    <tr>
        <th align="center" class="b1 b3 b4" width="30%">Item</th>
        <th align="center" class="b1 b3 b4" width="40%">Item Description</th>
        <th align="center" class="b1 b3 b4" width="30%">QTY</th>
    </tr>
    <tr>
        <td align="center"><?= $res['jenis_produk'] ?></td>
        <td align="center"><?= $res['produk'] ?></td>
        <td align="center"><?= number_format($res['volume']) ?></td>
    </tr>
</table>
<br>
<table>
    <tr>
        <td>
            Description :
        </td>
    </tr>
    <tr>
        <td>
            <?= strtoupper($res['nama_customer']) . " PO NO. " . $res['nomor_poc'] . " " . number_format($res['volume']) ?>
        </td>
    </tr>
</table>
<br><br>
<table class="table-bordered" border="0" width="100%">
    <tr>
        <td align="center">
            Prepared By
        </td>
        <td align="center">
            Approved By
        </td>
        <td align="center">
            Shipped By
        </td>
    </tr>
    <tr>
        <td>
            <br><br><br><br><br>
        </td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td align="center">
            <hr style="width: 70%;">
        </td>
        <td align="center">
            <hr style="width: 70%;">
        </td>
        <td align="center">
            <hr style="width: 70%;">
        </td>
    </tr>
    <tr>
        <td>Date :</td>
        <td>Date :</td>
        <td>Date :</td>
    </tr>
</table>
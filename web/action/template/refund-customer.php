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

    .logo-unpaid {
        margin-top: 5%;
        width: 20%;
        border: 5px solid #ff4d4d;
        border-radius: 10px;
        padding: 20px 40px;
        transform: skewY(-0.06turn);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .unpaid {
        text-align: center;
        font-size: 35px;
        font-weight: bold;
        color: #ff4d4d;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .logo-paid {
        margin-top: 5%;
        width: 11%;
        border: 5px solid #13e800;
        border-radius: 10px;
        padding: 20px 40px;
        transform: skewY(-0.06turn);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .paid {
        font-size: 30px;
        font-weight: bold;
        color: #13e800;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }
</style>
<?php

?>
<htmlpagefooter name="myHTMLFooter1">
    <p style="margin:0; text-align:right;">
        <barcode code="<?php echo $barcod; ?>" type="QR" size="1" />
    </p>
    <p style="margin:0; text-align:right; font-size:6pt;"><?php echo $barcod; ?></p>
    <p style="margin:0; text-align:right; font-size:7pt;"><i>(This form is valid with sign by computerized system)</i></p>
    <p style="margin:0; text-align:right; font-size:6pt;">Printed by <?php echo $printe; ?></p>
</htmlpagefooter>
<sethtmlpagefooter name="myHTMLFooter1" page="ALL" value="on" show-this-page="1" />
<table border="0" width="100%">
    <tr>
        <td width="30%">
            <div style="padding:0;"><img src="<?php echo BASE_IMAGE . "/logo-kiri-penawaran.png"; ?>" width="20%" /></div>
        </td>
        <td align="right">
            <h1>REFUND</h1>
        </td>
    </tr>
    <tr>
        <td width="55%">
            &nbsp;
        </td>
        <td>
            <table width="100%" border="0" style="font-size: 12px;">
                <tr>
                    <td align="right">
                        No Invoice
                    </td>
                    <td align="center">
                        :
                    </td>
                    <td align="right">
                        <?= $nomor_invoice ?>
                    </td>
                </tr>
                <tr>
                    <td align="right">
                        No PO Customer
                    </td>
                    <td align="center">
                        :
                    </td>
                    <td align="right">
                        <?= $result['nomor_poc'] ?>
                    </td>
                </tr>
                <tr>
                    <td align="right">
                        Nama Customer
                    </td>
                    <td align="center">
                        :
                    </td>
                    <td align="right">
                        <?= $result['nama_customer'] ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<br>
<table width="100%" border="0" style="font-size: 12px;" cellpadding="8" cellspacing="0">
    <tr>
        <td style="background-color: #ffcc99;" align="center">
            <b>
                <h3>
                    FORM COMMISION PAID
                </h3>
            </b>
        </td>
    </tr>
</table>
<table width="100%" border="1" style="font-size: 12px;" cellpadding="8" cellspacing="0">
    <tr>
        <td width="50%" align="center">
            Receiver Commission
        </td>
        <td width="50%" align="center">
            Transfer
        </td>
    </tr>
</table>
<table width="100%" border="0" style="font-size: 12px;" cellpadding="8" cellspacing="0">
    <tr>
        <td width="50%">
            Name : <?= $nama ?>
        </td>
        <td width="50%">
            Bank : <?= $bank ?>
        </td>
    </tr>
    <tr>
        <td>
            Divisi : <?= $divisi ?>
        </td>
        <td>
            No. Rekening : <?= $no_rekening ?>
        </td>
    </tr>
    <tr>
        <td>
            &nbsp;
        </td>
        <td>
            On Behalf : <?= ucwords($atas_nama) ?>
        </td>
    </tr>
    <tr>
        <td>
            &nbsp;
        </td>
        <?php if ($result['tgl_buat_po'] >= "2024-10-28 06:00:00") : ?>
            <td>
                <?php
                $total_terima_refund = (($persentase_refund * $result['total_vol_invoice']) * $persen) / 100;
                ?>
                Terima Refund : Rp. <?= $persentase_refund ?> / Liter (Rp. <?= number_format($total_terima_refund) ?>)
            </td>
        <?php else : ?>
            <td>
                <?php
                if ($result['disposisi'] == '1') {
                    $total_terima_refund = ($persentase_refund * $persen) / 100;
                } else {
                    $total_terima_refund = $terima_refund_fix;
                }
                ?>
                Terima Refund : Rp. <?= number_format($total_terima_refund) ?>
            </td>
        <?php endif ?>
    </tr>
</table>
<hr>
<table width="100%" border="0" style="font-size: 12px;" cellpadding="8" cellspacing="0">
    <tr>
        <td width="50%">
            Name : <?= $nama2 ?>
        </td>
        <td width="50%">
            Bank : <?= $bank2 ?>
        </td>
    </tr>
    <tr>
        <td>
            Divisi : <?= $divisi2 ?>
        </td>
        <td>
            No. Rekening : <?= $no_rekening2 ?>
        </td>
    </tr>
    <tr>
        <td>
            &nbsp;
        </td>
        <td>
            On Behalf : <?= ucwords($atas_nama2) ?>
        </td>
    </tr>
    <tr>
        <td>
            &nbsp;
        </td>
        <?php if ($result['tgl_buat_po'] >= "2024-10-28 06:00:00") : ?>
            <td>
                <?php
                $total_terima_refund2 = (($persentase_refund2 * $result['total_vol_invoice']) * $persen) / 100;
                ?>
                Terima Refund : Rp. <?= $persentase_refund2 ?> / Liter (Rp. <?= number_format($total_terima_refund2) ?>)
            </td>
        <?php else : ?>
            <td>
                <?php
                if ($result['disposisi'] == '1') {
                    $total_terima_refund2 = ($persentase_refund2 * $persen) / 100;
                } else {
                    $total_terima_refund2 = $terima_refund_fix2;
                }
                ?>
                Terima Refund : Rp. <?= number_format($total_terima_refund2) ?>
            </td>
        <?php endif ?>
    </tr>
</table>
<hr>
<table width="100%" border="0" style="font-size: 12px;" cellpadding="8" cellspacing="0">
    <tr>
        <td width="50%">
            Name : <?= $nama3 ?>
        </td>
        <td width="50%">
            Bank : <?= $bank3 ?>
        </td>
    </tr>
    <tr>
        <td>
            Divisi : <?= $divisi3 ?>
        </td>
        <td>
            No. Rekening : <?= $no_rekening3 ?>
        </td>
    </tr>
    <tr>
        <td>
            &nbsp;
        </td>
        <td>
            On Behalf : <?= ucwords($atas_nama3) ?>
        </td>
    </tr>
    <tr>
        <td>
            &nbsp;
        </td>
        <?php if ($result['tgl_buat_po'] >= "2024-10-28 06:00:00") : ?>
            <td>
                <?php
                $total_terima_refund3 = (($persentase_refund3 * $result['total_vol_invoice']) * $persen) / 100;
                ?>
                Terima Refund : Rp. <?= $persentase_refund3 ?> / Liter (Rp. <?= number_format($total_terima_refund3) ?>)
            </td>
        <?php else : ?>
            <td>
                <?php
                if ($result['disposisi'] == '1') {
                    $total_terima_refund3 = ($persentase_refund3 * $persen) / 100;
                } else {
                    $total_terima_refund3 = $terima_refund_fix3;
                }
                ?>
                Terima Refund : Rp. <?= number_format($total_terima_refund3) ?>
            </td>
        <?php endif ?>
    </tr>
</table>
<table width="100%" style="font-size: 12px;" cellspacing="0" cellpadding="5">
    <tr>
        <td align="left" class="b1 b2 b3 b4">
            <?php if ($result['disposisi'] == '1') : ?>
                <strong>Amount : Rp. <?= number_format($total_refund_fix) ?></strong>
            <?php else : ?>
                <strong>Amount : Rp. <?= number_format($result['total_refund']) ?></strong>
            <?php endif ?>
        </td>
    </tr>
</table>
<br>
<div class="container-fluid" width="40%" style="border:1px solid black;">
    <table width="100%" style="font-size: 12px;" cellspacing="0" cellpadding="5">
        <tr>
            <td align="left" width="30%">
                Send Date
            </td>
            <td width="5%">
                :
            </td>
            <td>
                <?= $tgl_invoice_dikirim ?>
            </td>
        </tr>
        <tr>
            <td align="left">
                T.O.P
            </td>
            <td>
                :
            </td>
            <td>
                <?= $result['top_payment'] ?>
            </td>
        </tr>
        <tr>
            <td align="left">
                Due Date
            </td>
            <td>
                :
            </td>
            <td>
                <?= $due_date_indo ?>
            </td>
        </tr>
        <tr>
            <td align="left">
                Pay Date
            </td>
            <td>
                :
            </td>
            <td>
                <?= $date_payment ?>
            </td>
        </tr>
    </table>
</div>
<?php if ($result['disposisi'] == 1) : ?>
    <div class="logo-unpaid">
        <span class="unpaid">UNPAID</span>
    </div>
<?php else : ?>
    <div class="logo-paid">
        <span class="paid">PAID</span>
    </div>
<?php endif ?>
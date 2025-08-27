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
    <p style="margin:0; text-align:right;">
        <barcode code="<?php echo $barcod; ?>" type="C39" size="0.8" />
    </p>
    <p style="margin:0; text-align:right; font-size:6pt; padding-right:70px;"><?php echo $barcod; ?></p>
    <p style="margin:0; text-align:right; font-size:7pt;"><i>(This form is valid with sign by computerized system)</i></p>
    <p style="margin:0; text-align:right; font-size:6pt;">Printed by <?php echo $printe; ?></p>
</htmlpagefooter>
<sethtmlpagefooter name="myHTMLFooter1" page="ALL" value="on" show-this-page="2" />
<div style="width:100%;">
    <div style="width:33%; float:left;">
        <div style="padding:0;"><img src="<?php echo BASE_IMAGE . "/logo-kiri-penawaran.png"; ?>" width="30%" /></div>
    </div>
    <div style="width:33%; float:left;">
        <div style="padding:0;">
            <p style="margin:0 0 5px; text-align:center; font-size:14pt; font-family:times;"><b>Loading Schedule Kapal</b></p>

        </div>
    </div>
    <div style="width:33%; float:left;">
        <p style="margin:0; text-align:right;"><b>PT. Pro Energi</b></p>
        <p style="margin:0; text-align:right; font-size:8pt;">
            Gd. Graha Irama Lt.6 Unit G<br />Jl. HR. Rasuna Said Kuningan,<br /> Jakarta Selatan, 12710<br />Telp: (021)-52892321<br />Fax: (021)-52892310
        </p>
    </div>
</div>
<div style="clear:both"></div>

<table border="0" cellpadding="0" cellspacing="0" width="100%" class="tabel_rincian" style="margin-bottom:10px;">
    <tr>
        <th align="center" class="b1 b3 b4" width="3%">No</th>
        <th align="center" class="b1 b3 b4" width="7%">Date</th>
        <th align="center" class="b1 b3 b4" width="28%">Depot</th>
        <th align="center" class="b1 b3 b4" width="18%">Transporter</th>
        <th align="center" class="b1 b3 b4" width="10%">No Pol</th>
        <th align="center" class="b1 b3 b4" width="15%">Captain</th>
        <th align="center" class="b1 b3 b4" width="5%">Qty</th>
        <th align="center" class="b1 b2 b3 b4" width="15%">Ket</th>
    </tr>
    <?php
    $nom = 0;
    $total1 = 0;
    foreach ($res as $data) {
        $nom++;
        $total1 = $total1 + $data['volume'];
    ?>
        <tr>
            <td class="b3 b4" align="center"><?php echo $nom; ?></td>
            <td class="b3 b4" align="left"><?php echo date("d/m/Y", strtotime($data['tanggal_loading'])); ?></td>
            <td class="b3 b4" align="left"><?php echo $data['nama_terminal']; ?> - <?php echo $data['tanki_terminal']; ?>
            </td>
            <td class="b3 b4" align="left"><?php echo $data['nama_suplier']; ?></td>
            <td class="b3 b4" align="left"><?php echo $data['vessel_name']; ?></td>
            <td class="b3 b4" align="left"><?php echo $data['kapten_name']; ?></td>
            <td class="b3 b4" align="right"><?php echo number_format($data['volume']); ?></td>
            <td class="b2 b3 b4" align="center"><?php echo $data['keterangan']; ?></td>
        </tr>
    <?php } ?>
    <tr>
        <td colspan="6" align="center">&nbsp;</td>
        <td class="b3 b4" align="right"><?php echo number_format($total1); ?></td>
        <td class="b4" colspan="4" align="center">&nbsp;</td>
    </tr>
</table>


<p style="margin:0 0 10px; font-size:8pt;">Request By, </p>
<p style="margin:0; font-size:8pt;"><u><?php echo $res[0]['created_by']; ?></u><br />Logistik</p>
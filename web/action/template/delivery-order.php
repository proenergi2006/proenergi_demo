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
<sethtmlpagefooter name="myHTMLFooter1" page="ALL" value="on" show-this-page="1" />
<div style="width:100%;">
    <div style="width:33%; float:left;">
        <div style="padding:0;"><img src="<?php echo BASE_IMAGE . "/logo-kiri-penawaran.png"; ?>" width="30%" /></div>
    </div>
    <div style="width:33%; float:left;">
        <div style="padding:0;">
            <p style="margin:0 0 5px; text-align:center; font-size:12pt; font-family:times;"><b>Delivery Schedule Loading Request</b></p>
            <p style="margin:0; text-align:center; font-size:11pt;">No : <?php echo $res[0]['nomor_ds']; ?></p>
        </div>
    </div>
    <div style="width:33%; float:left;">
        <p style="margin:0; text-align:right;"><b>PT. Pro Energi</b></p>
        <p style="margin:0; text-align:right; font-size:8pt;">
            Gd. Graha Irama Lt.6 Unit G<br />Jl. HR. Rasuna Said Kuningan,<br /> Jakarta Selatan, 12710<br />Telp: (021)-52892321<br />
        </p>
    </div>
</div>
<div style="clear:both"></div>
<div style="width:100%;">
    <div style="float:left; width:80px;">
        <p style="margin:0; font-size:8pt;">Depot</p>
    </div>
    <div style="float:left;">
        <p style="margin:0; font-size:8pt;">: <?php echo $res[0]['nama_terminal'] . ' ' . $res[0]['tanki_terminal'] . ', ' . $res[0]['lokasi_terminal']; ?></p>
    </div>
</div>
<div style="width:100%;">
    <div style="float:left; width:80px;">
        <p style="margin:0; font-size:8pt;">Telp</p>
    </div>
    <div style="float:left;">
        <p style="margin:0; font-size:8pt;">: <?php echo $res[0]['telp_terminal']; ?></p>
    </div>
</div>

<div style="width:100%;">
    <div style="float:left; width:80px;">
        <p style="margin:0; font-size:8pt;">CC</p>
    </div>
    <div style="float:left;">
        <p style="margin:0; font-size:8pt;">: <?php echo $res[0]['cc_terminal']; ?></p>
    </div>
</div>
<p></p>
<div style="clear:both"></div>

<table border="0" cellpadding="0" cellspacing="0" width="100%" class="tabel_rincian" style="margin-bottom:10px;">
    <tr>
        <th align="center" class="b1 b3 b4" width="3%">No</th>
        <th align="center" class="b1 b3 b4" width="6%">Date</th>
        <th align="center" class="b1 b3 b4" width="5%">Loading Request</th>
        <th align="center" class="b1 b3 b4" width="12%">Transportir</th>
        <th align="center" class="b1 b3 b4" width="8%">SPJ No</th>
        <th align="center" class="b1 b3 b4" width="8%">Truck No</th>
        <th align="center" class="b1 b3 b4" width="10%">Driver</th>
        <th align="center" class="b1 b3 b4" width="6%">Volume (Liter)</th>
        <th align="center" class="b1 b3 b4" width="6%">No. Oc</th>
        <th align="center" class="b1 b3 b4" width="6%">No. Order</th>
        <th align="center" class="b1 b3 b4" width="14%">Seal</th>
        <th align="center" class="b1 b2 b3 b4" width="16%">Remarks</th>
    </tr>
    <tr>
        <?php
        if (count($res) > 0) {
            $nom = 0;
            $total1 = 0;
            foreach ($res as $data) {
                $nom++;
                $total1 = $total1 + $data['volume_po'];
                $tempal = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
                $alamat    = ucwords($tempal) . " " . $data['nama_prov'];
                $bar    = "06" . str_pad($data['id_dsd'], 6, '0', STR_PAD_LEFT);
                $seg_aw = ($data['nomor_segel_awal']) ? str_pad($data['nomor_segel_awal'], 4, '0', STR_PAD_LEFT) : '';
                $seg_ak = ($data['nomor_segel_akhir']) ? str_pad($data['nomor_segel_akhir'], 4, '0', STR_PAD_LEFT) : '';
                if ($data['jumlah_segel'] == 1)
                    $nomor_segel = $data['pre_segel'] . "-" . $seg_aw;
                else if ($data['jumlah_segel'] == 2)
                    $nomor_segel = $data['pre_segel'] . "-" . $seg_aw . " &amp; " . $data['pre_segel'] . "-" . $seg_ak;
                else if ($data['jumlah_segel'] > 2)
                    $nomor_segel = $data['pre_segel'] . "-" . $seg_aw . " s/d " . $data['pre_segel'] . "-" . $seg_ak;
                else $nomor_segel = '';
        ?>
                <tr<?php echo ($data['is_cancel'] ? ' style="background-color:#ddd;"' : ''); ?>>
                    <td class="b3 b4" align="center"><?php echo $nom; ?></td>
                    <td class="b3 b4" align="center"><?php echo date("d/m/Y", strtotime($data['tanggal_loading'])); ?></td>
                    <td class="b3 b4" align="center"><?php echo date("H:i", strtotime($data['jam_loading'])); ?></td>
                    <td class="b3 b4" align="left"><?php echo $data['nama_suplier']; ?></td>
                    <td class="b3 b4" align="center"><?php echo $data['no_spj']; ?></td>
                    <td class="b3 b4" align="center"><?php echo $data['nomor_plat']; ?></td>
                    <td class="b3 b4" align="center"><?php echo $data['nama_sopir']; ?></td>
                    <td class="b3 b4" align="right"><?php echo number_format($data['volume_po']); ?></td>
                    <td class="b3 b4" align="center"><?php echo $data['nomor_oc']; ?></td>
                    <td class="b3 b4" align="center"><?php echo $data['nomor_order']; ?></td>
                    <td class="b3 b4" align="center"><?php echo $nomor_segel; ?></td>
                    <td class="b2 b3 b4" align="left"><?php echo ($data['is_cancel'] ? 'CANCEL' : '&nbsp;'); ?></td>
    </tr>
<?php } ?>
<tr>

    <td colspan="7" align="center">&nbsp;</td>
    <td class="b3 b4" align="right"><?php echo number_format($total1); ?></td>
    <td class="b4" colspan="4" align="center">&nbsp;</td>
</tr>
<?php } ?>
</table>

<p style="margin:0 0 20px; font-size:8pt;">Request By, </p>
<p style="margin:0; font-size:8pt;"><u><?php echo $res[0]['created_by']; ?></u><br />Logistik</p>
<style>
    .tabel_header td {
        padding: 1px 3px;
        font-size: 9pt;
        height: 35px;
    }

    .tabel_rincian td {
        height: 35px;
        padding: 3px;
        font-size: 9pt;
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

    .coret {
        text-decoration: line-through;
    }

    .td-header,
    .td-isi {
        font-size: 5pt;
        padding: 2px;
    }

    .th-isi {
        font-size: 5pt;
        padding: 1px;
        background-color: #b8cce4;
    }

    .td-isi {
        text-align: center;
        font-weight: bold;
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

    .isi-spj {
        padding: 1px 0px 2px;
        vertical-align: top;
        font-size: 10pt;
        font-family: tahoma;
    }

    .isi-spj2 {
        padding: 1px;
        vertical-align: top;
        font-size: 9pt;
        font-family: tahoma;
    }
</style>
<htmlpagefooter name="myHTMLFooter1">
    <p style="font-size:6pt; text-align:right;">Printed by <?php echo $printe; ?></p>
</htmlpagefooter>
<sethtmlpagefooter name="myHTMLFooter1" page="ALL" value="on" show-this-page="1" />
<?php
if (count($res) > 0) {
    $jum = 0;
    foreach ($res as $data) {
        $jum++;
        $barcod = $data['kode_barcode'] . '07' . str_pad($data['id_dsk'], 6, '0', STR_PAD_LEFT);
        $note     = ($data['keterangan']) ? str_replace("<br />", PHP_EOL, $data['keterangan']) : '&nbsp;';
        $tank     = json_decode($data['tank_seal'], true);
        $mani     = json_decode($data['manifold_seal'], true);
        $pump     = json_decode($data['pump_seal'], true);
        $other     = json_decode($data['other_seal'], true);

        $mani_kiri_awal  = ($mani['mani_kiri_awal']) ? str_pad($mani['mani_kiri_awal'], 4, '0', STR_PAD_LEFT) : '';
        $mani_kiri_akhir = ($mani['mani_kiri_akhir']) ? str_pad($mani['mani_kiri_akhir'], 4, '0', STR_PAD_LEFT) : '';
        if ($mani['jumlah_kiri'] == 1)
            $mani_kiri = $data['inisial_segel'] . "-" . $mani_kiri_awal;
        else if ($mani['jumlah_kiri'] == 2)
            $mani_kiri = $data['inisial_segel'] . "-" . $mani_kiri_awal . " &amp; " . $data['inisial_segel'] . "-" . $mani_kiri_akhir;
        else if ($mani['jumlah_kiri'] > 2)
            $mani_kiri = $data['inisial_segel'] . "-" . $mani_kiri_awal . " s/d " . $data['inisial_segel'] . "-" . $mani_kiri_akhir;
        else $mani_kiri = '';

        $mani_kanan_awal  = ($mani['mani_kanan_awal']) ? str_pad($mani['mani_kanan_awal'], 4, '0', STR_PAD_LEFT) : '';
        $mani_kanan_akhir = ($mani['mani_kanan_akhir']) ? str_pad($mani['mani_kanan_akhir'], 4, '0', STR_PAD_LEFT) : '';
        if ($mani['jumlah_kanan'] == 1)
            $mani_kanan = $data['inisial_segel'] . "-" . $mani_kanan_awal;
        else if ($mani['jumlah_kanan'] == 2)
            $mani_kanan = $data['inisial_segel'] . "-" . $mani_kanan_awal . " &amp; " . $data['inisial_segel'] . "-" . $mani_kanan_akhir;
        else if ($mani['jumlah_kanan'] > 2)
            $mani_kanan = $data['inisial_segel'] . "-" . $mani_kanan_awal . " s/d " . $data['inisial_segel'] . "-" . $mani_kanan_akhir;
        else $mani_kanan = '';

        $pump_kiri_awal  = ($pump['pump_kiri_awal']) ? str_pad($pump['pump_kiri_awal'], 4, '0', STR_PAD_LEFT) : '';
        $pump_kiri_akhir = ($pump['pump_kiri_akhir']) ? str_pad($pump['pump_kiri_akhir'], 4, '0', STR_PAD_LEFT) : '';
        if ($pump['jumlah_kiri'] == 1)
            $pump_kiri = $data['inisial_segel'] . "-" . $pump_kiri_awal;
        else if ($pump['jumlah_kiri'] == 2)
            $pump_kiri = $data['inisial_segel'] . "-" . $pump_kiri_awal . " &amp; " . $data['inisial_segel'] . "-" . $pump_kiri_akhir;
        else if ($pump['jumlah_kiri'] > 2)
            $pump_kiri = $data['inisial_segel'] . "-" . $pump_kiri_awal . " s/d " . $data['inisial_segel'] . "-" . $pump_kiri_akhir;
        else $pump_kiri = '';

        $pump_kanan_awal  = ($pump['pump_kanan_awal']) ? str_pad($pump['pump_kanan_awal'], 4, '0', STR_PAD_LEFT) : '';
        $pump_kanan_akhir = ($pump['pump_kanan_akhir']) ? str_pad($pump['pump_kanan_akhir'], 4, '0', STR_PAD_LEFT) : '';
        if ($pump['jumlah_kanan'] == 1)
            $pump_kanan = $data['inisial_segel'] . "-" . $pump_kanan_awal;
        else if ($pump['jumlah_kanan'] == 2)
            $pump_kanan = $data['inisial_segel'] . "-" . $pump_kanan_awal . " &amp; " . $data['inisial_segel'] . "-" . $pump_kanan_akhir;
        else if ($pump['jumlah_kanan'] > 2)
            $pump_kanan = $data['inisial_segel'] . "-" . $pump_kanan_awal . " s/d " . $data['inisial_segel'] . "-" . $pump_kanan_akhir;
        else $pump_kanan = '';
?>
        <p style="margin-bottom:0px; text-align:center;"><u>DELIVERY NOTE</u></p>
        <p style="margin-bottom:0px; text-align:center;"><b>NO : <?php echo $data['nomor_dn_kapal']; ?></b></p>
        <hr />
        <div style="width:100%">
            <div style="width:50%; float:left;">
                <div style="padding:0 5px 5px 0;">
                    <p style="margin:0px; font-size:12pt;"><u>PT. Pro Energi</u></p>
                    <p style="margin:0px;">Head Office: Graha Irama Building lt.6 Unit G</p>
                    <p style="margin:0px;">Jl. HR. Rasuna Said Blok X-1 Kav. 1-2 12950 DKI Jakarta - Indonesia</p>
                    <p style="margin:0px;">Phone: +62-21-52892321</p>
                    <p style="margin:0px;">Email: info@proenergi.com</p>
                </div>
            </div>
            <div style="width:50%; float:left;">
                <div style="padding:0 5px 5px 0;">
                    <div style="text-align:right">
                        <barcode code="<?php echo $barcod; ?>" type="QR" size="1" />
                    </div>
                    <p style="margin:0; padding-left:125px; text-align:right; font-size:6pt;"><?php echo $barcod; ?></p>
                    <p style="margin:0 0 10px; text-align:right; font-size:7pt;"><i>(This form is valid with sign by computerized system)</i></p>
                    <p style="margin:0px; font-size:8pt; text-align:right;">Date : <?php echo tgl_indo($data['tanggal_loading']); ?></p>
                </div>
            </div>
        </div>
        <div style="clear:both"></div>

        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="tabel_rincian" style="margin-bottom:15px;">
            <tr>
                <td width="33%" class="b1 b4"><b>Shipper</b></td>
                <td width="33%" class="b1 b4"><b>Consignee</b></td>
                <td width="34%" class="b1 b2 b4"><b>Notify Party</b></td>
            </tr>
            <tr>
                <td class="b3 b4"><?php echo $data['consignor_nama']; ?><br /><?php echo $data['consignor_alamat']; ?></td>
                <td class="b3 b4"><?php echo $data['consignee_nama']; ?><br /><?php echo $data['consignee_alamat']; ?></td>
                <td class="b2 b3 b4"><?php echo $data['notify_nama']; ?><br /><?php echo $data['notify_alamat']; ?></td>
            </tr>
        </table>

        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="tabel_rincian">
            <tr>
                <th rowspan="2" style="text-align:center;" width="5%" class="b1 b3 b4">No</th>
                <th rowspan="2" style="text-align:center;" width="30%" class="b1 b3 b4">Description</th>
                <th style="text-align:center;" width="15%" class="b1 b3 b4">Quantity (BL)</th>
                <th rowspan="2" style="text-align:center;" width="35%" class="b1 b2 b3 b4">Unit</th>
            </tr>
            <tr>
                <th style="text-align:center;" width="15%" class="b3 b4">BL</th>
            </tr>
            <tr>
                <td rowspan="3" style="text-align:center;" class="b3 b4">1</td>
                <td rowspan="3" style="text-align:center;" class="b3 b4"><?php echo $data['produk_dn']; ?></td>
                <td class="b3 b4" style="text-align:right;"><?php echo ($data['bl_lo_jumlah']) ? number_format($data['bl_lo_jumlah'], 0, '', '.') : ''; ?></td>
                <td class="b2 b3 b4">Litres Observe</td>
            </tr>
            <tr>
                <td class="b3 b4" style="text-align:right;"><?php echo ($data['bl_lc_jumlah']) ? number_format($data['bl_lc_jumlah'], 0, '', '.') : ''; ?></td>
                <td class="b2 b3 b4">Litres 15<sup>o</sup>C (GSV)</td>
            </tr>
            <tr>
                <td class="b3 b4" style="text-align:right;"><?php echo ($data['bl_mt_jumlah']) ? number_format($data['bl_mt_jumlah'], 0, '', '.') : ''; ?></td>
                <td class="b2 b3 b4">MT</td>
            </tr>
        </table>
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="tabel_rincian">
            <tr>
                <td class="b4" width="20%">Loading Port</td>
                <td width="2%" style="text-align:center">:</td>
                <td class="b2" width="78%"><?php echo $data['nama_terminal']; ?></td>
            </tr>
            <tr>
                <td class="b3 b4">Port of Discharge</td>
                <td class="b3" style="text-align:center">:</td>
                <td class="b2 b3"><?php echo $data['port_discharge']; ?></td>
            </tr>
            <tr>
                <td class="b4">Shipping Line</td>
                <td style="text-align:center">:</td>
                <td class="b2"><?php echo $data['nama_suplier']; ?></td>
            </tr>
            <tr>
                <td class="b4">Master (Captain)</td>
                <td style="text-align:center">:</td>
                <td class="b2"><?php echo $data['kapten_name']; ?></td>
            </tr>
            <tr>
                <td class="b4">Vessel Name</td>
                <td style="text-align:center">:</td>
                <td class="b2"><?php echo $data['vessel_name']; ?></td>
            </tr>
            <tr>
                <td class="b4">Shipment</td>
                <td style="text-align:center">:</td>
                <td class="b2"><?php echo $data['shipment']; ?></td>
            </tr>

            <?php
            $nom = 0;
            $tank_kiri = [];  // Array untuk menyimpan nomor tank kiri
            $tank_kanan = []; // Array untuk menyimpan nomor tank kanan

            foreach ($tank as $idx1 => $data1) {
                $nom++;

                // Proses untuk tank kiri
                if (!empty($data1['tank_kiri_awal']) && !empty($data1['tank_kiri_akhir'])) {
                    // Loop untuk menambahkan semua nomor antara tank_kiri_awal dan tank_kiri_akhir
                    for ($i = $data1['tank_kiri_awal']; $i <= $data1['tank_kiri_akhir']; $i++) {
                        $tank_kiri[] = $data['inisial_segel'] . "-" . str_pad($i, 5, '0', STR_PAD_LEFT);
                    }
                }

                // Proses untuk tank kanan (jika ada datanya)
                if (!empty($data1['tank_kanan_awal']) && !empty($data1['tank_kanan_akhir'])) {
                    // Loop untuk menambahkan semua nomor antara tank_kanan_awal dan tank_kanan_akhir
                    for ($i = $data1['tank_kanan_awal']; $i <= $data1['tank_kanan_akhir']; $i++) {
                        $tank_kanan[] = $data['inisial_segel'] . "-" . str_pad($i, 5, '0', STR_PAD_LEFT);
                    }
                }
            }

            // Menggabungkan semua nomor yang telah diambil
            $all_tank = array_merge($tank_kiri, $tank_kanan);

            // Menghapus duplikat jika ada
            $all_tank = array_unique($all_tank);

            // Menyiapkan output
            $output = implode(", ", $all_tank);
            ?>
            <tr>
                <td class="b3 b4">Seal Number</td>
                <td class="b3" style="text-align:center">:</td>
                <td class="b2 b3"> <?php echo $output; ?></td>
            </tr>
        </table>

        <!-- <table width="100%" border="0" cellpadding="0" cellspacing="0" class="tabel_rincian">



            <tr>
                <td class="b3 b4" colspan="2">Manifold</td>
                <td class="b3 b4" style="text-align:center"><?php echo $mani_kiri; ?></td>
                <td class="b3 b4" colspan="2">Manifold</td>
                <td class="b2 b3 b4" style="text-align:center"><?php echo $mani_kanan; ?></td>
            </tr>
            <tr>
                <td class="b3 b4" colspan="2">Pump Room</td>
                <td class="b3 b4" style="text-align:center"><?php echo $pump_kiri; ?></td>
                <td class="b3 b4" colspan="2">&nbsp;</td>
                <td class="b2 b3 b4" style="text-align:center">&nbsp;</td>
            </tr>
        </table> -->

        <?php if (count($other) > 0) { ?>
            <table width="100%" border="0" cellpadding="0" cellspacing="0" class="tabel_rincian">
                <tr>
                    <td class="b3 b4">Other Seal Number</td>
                    <td class="b2 b3" colspan="3">:</td>
                </tr>
                <?php
                foreach ($other as $data5) {
                    $sgl_kiri_awal     = ($data5['sgl_kiri_awal']) ? str_pad($data5['sgl_kiri_awal'], 4, '0', STR_PAD_LEFT) : '';
                    $sgl_kiri_akhir = ($data5['sgl_kiri_akhir']) ? str_pad($data5['sgl_kiri_akhir'], 4, '0', STR_PAD_LEFT) : '';
                    if ($data5['jumlah_kiri'] == 1)
                        $nomor_lain_kiri = $data['inisial_segel'] . "-" . $sgl_kiri_awal;
                    else if ($data5['jumlah_kiri'] == 2)
                        $nomor_lain_kiri = $data['inisial_segel'] . "-" . $sgl_kiri_awal . " &amp; " . $data['inisial_segel'] . "-" . $sgl_kiri_akhir;
                    else if ($data5['jumlah_kiri'] > 2)
                        $nomor_lain_kiri = $data['inisial_segel'] . "-" . $sgl_kiri_awal . " s/d " . $data['inisial_segel'] . "-" . $sgl_kiri_akhir;
                    else $nomor_lain_kiri = '';

                    $sgl_kanan_awal  = ($data5['sgl_kanan_awal']) ? str_pad($data5['sgl_kanan_awal'], 4, '0', STR_PAD_LEFT) : '';
                    $sgl_kanan_akhir = ($data5['sgl_kanan_akhir']) ? str_pad($data5['sgl_kanan_akhir'], 4, '0', STR_PAD_LEFT) : '';
                    if ($data5['jumlah_kanan'] == 1)
                        $nomor_lain_kanan = $data['inisial_segel'] . "-" . $sgl_kanan_awal;
                    else if ($data5['jumlah_kanan'] == 2)
                        $nomor_lain_kanan = $data['inisial_segel'] . "-" . $sgl_kanan_awal . " &amp; " . $data['inisial_segel'] . "-" . $sgl_kanan_akhir;
                    else if ($data5['jumlah_kanan'] > 2)
                        $nomor_lain_kanan = $data['inisial_segel'] . "-" . $sgl_kanan_awal . " s/d " . $data['inisial_segel'] . "-" . $sgl_kanan_akhir;
                    else $nomor_lain_kanan = '';
                ?>
                    <tr>
                        <td width="20%" class="b3 b4"><?php echo $data5['jns_kiri']; ?></td>
                        <td width="30%" class="b3 b4" style="text-align:center"><?php echo $nomor_lain_kiri; ?></td>
                        <td width="20%" class="b3 b4"><?php echo $data5['jns_kanan']; ?></td>
                        <td width="30%" class="b2 b3 b4" style="text-align:center"><?php echo $nomor_lain_kanan; ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>

        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="tabel_rincian" style="margin-bottom:10px;">
            <tr>
                <td colspan="6" class="b2 b4">Remarks :</td>
            </tr>
            <tr>
                <td colspan="6" class="b2 b3 b4"><?php echo $note; ?></td>
            </tr>
            <tr>
                <td colspan="6" class="b2 b4">Acknowledge :</td>
            </tr>
            <tr>
                <td style="text-align:left;" class="b4" width="30%">Shipper</td>
                <td style="text-align:center;" width="5%">&nbsp;</td>
                <td style="text-align:center;" width="30%">Master</td>
                <td style="text-align:center;" width="5%">&nbsp;</td>
                <td style="text-align:center;" width="28%">Customer</td>
                <td style="text-align:center;" class="b2" width="2%">&nbsp;</td>
            </tr>
            <tr>
                <td style="text-align:center; height:30px;" class="b4">&nbsp;</td>
                <td style="text-align:center;">&nbsp;</td>
                <td style="text-align:center;">&nbsp;</td>
                <td style="text-align:center;">&nbsp;</td>
                <td style="text-align:center;">&nbsp;</td>
                <td style="text-align:center;" class="b2">&nbsp;</td>
            </tr>
            <tr>
                <td style="text-align:left;" class="b4"><?php echo $data['created_by']; ?></td>
                <td style="text-align:center;">&nbsp;</td>
                <td style="text-align:center;">&nbsp;</td>
                <td style="text-align:center;">&nbsp;</td>
                <td style="text-align:center;">&nbsp;</td>
                <td style="text-align:center;" class="b2">&nbsp;</td>
            </tr>
            <tr>
                <td style="text-align:left;" class="b1 b3 b4">PT. Pro Energi</td>
                <td style="text-align:center;" class="b3">&nbsp;</td>
                <td style="text-align:center;" class="b1 b3"><?php echo $data['vessel_name']; ?></td>
                <td style="text-align:center;" class="b3">&nbsp;</td>
                <td style="text-align:center;" class="b1 b3"><?php echo $data['nama_customer']; ?></td>
                <td style="text-align:center;" class="b2 b3">&nbsp;</td>
            </tr>
        </table>


<?php
        if ($jum < count($res))
            echo '<pagebreak sheet-size="A4" margin-left="10mm" margin-right="10mm" margin-top="10mm" margin-bottom="10mm" margin-header="0mm" margin-footer="5mm" />';
    }
}
?>
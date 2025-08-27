<style>
    .tabel_header td {
        padding: 1px 3px;
        font-size: 8pt;
        height: 18px;
    }

    .tabel_rincian td {
        padding: 2px;
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
    <p style="font-size:6pt; text-align:right;">Printed by <?php echo $printe; ?></p>
</htmlpagefooter>
<sethtmlpagefooter name="myHTMLFooter1" page="ALL" value="on" show-this-page="1" />
<?php
if (count($res) > 0) {
    $nom = 0;
    foreach ($res as $data) {
        $nom++;
        $volume_po = $data['volume_po'];
        $tempal = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
        $alamat    = ucwords($tempal) . " " . $data['nama_prov'];
        $bar    = $data['kode_barcode'] . "06" . str_pad($data['id_dsd'], 6, '0', STR_PAD_LEFT);
        $seg_aw = ($data['nomor_segel_awal']) ? str_pad($data['nomor_segel_awal'], 4, '0', STR_PAD_LEFT) : '';
        $seg_ak = ($data['nomor_segel_akhir']) ? str_pad($data['nomor_segel_akhir'], 4, '0', STR_PAD_LEFT) : '';
        // METODE SEGEL LAMA
        if ($data['jumlah_segel'] == 1) {
            $nomor_segel = $data['pre_segel'] . "-" . str_pad($seg_aw, 7, '0', STR_PAD_LEFT);
        } elseif ($data['jumlah_segel'] == 2) {
            $nomor_segel = $data['pre_segel'] . "-" . str_pad($seg_aw, 7, '0', STR_PAD_LEFT) . ", " . $data['pre_segel'] . "-" . str_pad($seg_ak, 7, '0', STR_PAD_LEFT);
        } else {
            // Inisialisasi array untuk menampung daftar nomor segel
            $daftar_nomor_segel = array();

            // Iterasi melalui setiap nomor segel dalam rentang dan tambahkan ke dalam daftar
            for ($i = $seg_aw; $i <= $seg_ak; $i++) {
                $daftar_nomor_segel[] = $data['pre_segel'] . "-" . str_pad($i, 7, '0', STR_PAD_LEFT);
            }

            // Gabungkan daftar nomor segel menjadi string terpisah dengan koma
            $nomor_segel = implode(", ", $daftar_nomor_segel);
        }
?>
        <p style="margin-bottom:0px; text-align:center;"><u>DELIVERY NOTE</u></p>
        <p style="margin-bottom:0px; text-align:center;"><b>NO : <?php echo $data['nomor_do']; ?></b></p>
        <hr />
        <div style="width:100%; margin-bottom:30px; border-top:1px solid; border-bottom:1px solid;">
            <div style="width:50%; float:left;">
                <div style="padding:0;">
                    <p style="margin:0px; font-size:14pt;"><b><u>PT. Pro Energi</u></b></p>
                    <p style="margin:0px; font-size:9pt;">Head Office: Graha Irama Building (Indorama) lt.6 Unit G</p>
                    <p style="margin:0px; font-size:9pt;">Jl. HR. Rasuna Said Blok X-1 Kav. 1-2 Jakarta Selatan, 12950</p>
                    <p style="margin:0px; font-size:9pt;">Telp: (+6221) 52892321, Fax: (+6221) 52892310</p>
                    <p style="margin:0px; font-size:9pt;">Web: www.proenergi.com</p>
                </div>
            </div>
            <div style="width:50%; float:left;">
                <div style="padding:10px 0;">
                    <div style="text-align:right">
                        <barcode code="<?php echo $bar; ?>" type="C39" />
                    </div>
                    <p style="margin:0; padding-left:125px; text-align:center; font-size:6pt;"><?php echo $bar; ?></p>
                    <p style="margin:0 0 10px; text-align:right; font-size:7pt;"><i>(This form is valid with sign by computerized system)</i></p>
                </div>
            </div>
        </div>
        <div style="clear:both"></div>

        <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tabel_rincian" style="margin-bottom:30px; font-size: 12px;">
            <tr>
                <td class="b1 b3 b4" width="10%" style="vertical-align:top;">LOADING</td>
                <td class="b1 b3" width="2%" style="vertical-align:top; text-align:center">:</td>
                <td class="b1 b2 b3" width="40%" style="vertical-align:top;"><?php echo ($sbmt1 == '1' ? $data['initial'] : $data['nama_terminal'] . ' ' . $data['tanki_terminal']) . ', ' . $data['lokasi_terminal']; ?></td>
                <td class="" width="5%" rowspan="3">&nbsp;</td>
                <td class="b1 b2 b3 b4" width="43%" rowspan="3" style="vertical-align:middle;">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td width="20%">DATE</td>
                            <td width="5%" style="text-align:center">:</td>
                            <td width="75%"><?php echo tgl_indo($data['tanggal_loading']); ?></td>
                        </tr>
                        <tr>
                            <td>SPJ</td>
                            <td style="text-align:center">:</td>
                            <td><?php echo $data['no_spj']; ?></td>
                        </tr>
                        <tr>
                            <td>SEAL</td>
                            <td style="text-align:center">:</td>
                            <td><?php echo $nomor_segel; ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="b4" style="vertical-align:top;">SOLD TO</td>
                <td class="" style="vertical-align:top; text-align:center">:</td>
                <td class="b2" style="vertical-align:top;"><?php echo !isset($data['is_loco']) ? $data['sold_to'] : $data['nama_customer']; ?></td>
            </tr>
            <tr>
                <td class="b3 b4" style="vertical-align:top;">SHIP TO</td>
                <td class="b3" style="vertical-align:top; text-align:center">:</td>
                <td class="b2 b3" style="vertical-align:top;"><?php echo !isset($data['is_loco']) ? $data['nama_customer'] . "<br>" . $alamat : $data['alamat_survey'] . ' ' . $alamat; ?></td>
            </tr>
        </table>

        <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tabel_rincian" style="font-size: 12px;">
            <tr>
                <td class="b1 b2 b3 b4" colspan="3" style="text-align:center;"><b>DESCRIPTION</b></td>
            </tr>
            <tr>
                <td class="b3 b4" style="text-align:center;"><b>PRODUCT</b></td>
                <td class="b2 b3 b4" colspan="2" style="text-align:center;"><b>QUANTITY</b></td>
            </tr>
            <tr>
                <td class="b3 b4" width="50%" style="text-align:center; height:70px;"><?php echo $data['jenis_produk'] . " (" . $data['merk_dagang'] . ")"; ?></td>
                <td class="b3 b4" width="44%" style="text-align:center;"><?php echo number_format($volume_po); ?> </td>
                <td class="b2 b3" width="6%" style="text-align:center;">Liter</td>
            </tr>
        </table>

        <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tabel_rincian" style="margin-bottom:20px; font-size: 11px;">
            <tr>
                <td class="b3 b4" width="30%%">TRANSPOTER</td>
                <td class="b2 b3 b4" width="70%"><?php echo $data['nama_suplier']; ?></td>
            </tr>
            <tr>
                <td class="b3 b4">DRIVER</td>
                <td class="b2 b3 b4"><?php echo $data['nama_sopir']; ?></td>
            </tr>
            <tr>
                <td class="b3 b4">LORRY TANK</td>
                <td class="b2 b3 b4"><?php echo $data['nomor_plat']; ?></td>
            </tr>
        </table>

        <p style="margin-bottom:0px;"><b>Measurement</b></p>
        <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tabel_rincian" style="margin-bottom:15px; font-size: 11px;">
            <tr>
                <td width="30%" style="text-align:center;" class="b1 b3 b4"><b>Compartment</b></td>
                <td width="30%" style="text-align:center;" class="b1 b2 b3 b4"><b>Sounding Level (mm)</b></td>
                <td width="40%">&nbsp;</td>
            </tr>
            <tr>
                <td style="text-align:center; height:35px;" class="b3 b4">Compartement 1 (Front)</td>
                <td style="text-align:center;" class="b2 b3 b4">&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td style="text-align:center; height:35px;" class="b3 b4">Compartement 2 (Back)</td>
                <td style="text-align:center;" class="b2 b3 b4">&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td style="text-align:center; height:35px;" class="b3 b4">Density</td>
                <td style="text-align:center;" class="b2 b3 b4">&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td style="text-align:center; height:35px;" class="b3 b4">Suhu (celcius)</td>
                <td style="text-align:center;" class="b2 b3 b4">&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>

        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="tabel_rincian" style="margin-bottom:20px; font-size: 11px;">
            <tr>
                <td width="30%" style="text-align:center;">Acknowledge,</td>
                <td style="text-align:center;" colspan="5">&nbsp;</td>
            </tr>
            <tr>
                <td style="text-align:center; height:50px;">&nbsp;</td>
                <td style="text-align:center;">&nbsp;</td>
                <td style="text-align:center; vertical-align:bottom;">&nbsp;</td>
                <td style="text-align:center;">&nbsp;</td>
                <td style="text-align:center;">&nbsp;</td>
                <td style="text-align:center;">&nbsp;</td>
            </tr>
            <tr>
                <td style="text-align:center;"><?php echo $res[0]['created_by']; ?></td>
                <td style="text-align:center;" width="5%">&nbsp;</td>
                <td style="text-align:center; vertical-align:bottom;" width="30%"><?php echo $data['nama_sopir'] . " (Driver)"; ?></td>
                <td style="text-align:center;" width="5%">&nbsp;</td>
                <td style="text-align:center; vertical-align:bottom;" width="30%"><?php echo !isset($data['is_loco']) ? $data['sold_to'] : $data['nama_customer']; ?></td>
                <td style="text-align:center;" width="2%">&nbsp;</td>
            </tr>
            <tr>
                <td style="text-align:center; border-top:1px dotted #000;">PT. Pro Energi</td>
                <td style="text-align:center;">&nbsp;</td>
                <td style="text-align:center; border-top:1px dotted #000;">Transporter</td>
                <td style="text-align:center;">&nbsp;</td>
                <!-- <td style="text-align:center;">&nbsp;</td> -->
                <td style="text-align:center; border-top:1px dotted #000;">Customer</td>
                <td style="text-align:center;">&nbsp;</td>
            </tr>
        </table>
        <div style="page-break-inside:avoid">
            <div class="td-ket">Representative Office: </div>
            <div class="td-ket">Gedung Graha Irama lt. 6 unit 6G, Jl. HR. Rasuna Said Blok XI, Jakarta Selatan, DKI Jakarta, 021-52892321</div>
            <div class="td-ket">Jl. Raya Alaya, Block LE-21, Samarinda, Kalimantan Timur, 0541-4100195</div>
            <div class="td-ket">Jl. Ketintang Madya Cempaka RK-02 Jambangan Surabaya, Jawa Timur, Telp. 031-8296463</div>
            <div class="td-ket">Komplek Ruko Golden Boulevard Blok D 01 No. 01 Citra Grand City, Sumatera Selatan, Telp. 0711-5645549</div>
            <div class="td-ket">Jl. Purnama, Komplek Purnama Agung 5 No. 10FF, Pontianak, Kalimantan Barat - Indonesia, Telp. 0561-8100273</div>
        </div>
<?php if ($nom < count($res)) echo '<pagebreak sheet-size="Letter" margin-left="10mm" margin-right="10mm" margin-top="20mm" margin-bottom="10mm" />';
    }
} ?>
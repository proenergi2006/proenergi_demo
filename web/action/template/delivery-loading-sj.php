<style>
    /* sisi kiri/kanan 0 */
    @media print {

        html,
        body {
            margin: 0;
            padding: 0;
        }
    }

    table {
        font-size: 10pt;
        font-family: 'Arial';

    }

    .tabel_header td {
        padding: 1px 3px;
        font-size: 10pt;
        height: 18px;
        font-family: 'Arial';
    }

    .tabel_rincian th {
        padding: 5px 3px;
        background-color: #ffcc99;
        font-family: 'Arial';
    }

    .tabel_rincian td {
        padding: 3px 2px;
        font-family: 'Arial';
    }

    .td-ket,
    .td-subisi {
        padding: 1px 0px 2px;
        vertical-align: top;
        font-family: 'Arial';
        font-size: 10pt;
    }

    .td-subisi {
        font-size: 10pt;
        font-family: 'Arial';
    }

    .td-ket {
        padding: 1px 0px;
        font-size: 10pt;
        font-family: 'Arial';
    }

    p {
        margin: 0 0 10px;
        text-align: justify;
        font-family: 'Arial';
    }

    .b1 {
        border-top: 0.5px solid #000;
        font-family: 'Arial';
        font-size: 8pt;
    }

    .b2 {
        border-right: 0.5px solid #000;
        font-family: 'Arial';
        font-size: 8pt;
    }

    .b3 {
        border-bottom: 0.5px solid #000;
        font-family: 'Arial';
        font-size: 8pt;
    }

    .b4 {
        border-left: 0.5px solid #000;
        font-family: 'Arial';
        font-size: 8pt;
    }

    .b1d {
        border-top: 0.5px solid #000;
        font-family: 'Arial';
    }

    .b2d {
        border-right: 0.5px solid #000;
        font-family: 'Arial';
    }

    .b3d {
        border-bottom: 0.5px solid #000;
        font-family: 'Arial';
    }

    .b4d {
        border-left: 0.5px solid #000;
        font-family: 'Arial';
    }

    .div-table {
        padding: 0px;
        margin: 0px;
        display: table;
        width: 100%;
        border: none;
        font-family: 'Arial';
    }

    .div-table-row {
        padding: 0px;
        margin: 0px;
        display: table-row;
        width: 100%;
        clear: both;
        font-family: 'Arial';
    }

    .div-table-cell {
        padding: 0px;
        margin: 0px;
        display: table-cell;
        float: right;
        font-size: 12px;
        font-family: 'Arial';
    }

    .p {
        font-family: 'Arial';
    }

    .td {
        font-family: Arial;
        font-size: 10pt;
    }

    .section {
        margin: 8px 0 12px;
    }

    /* jarak atas-bawah */
    /* Judul section: full-width + garis atas & bawah */
    .hr-mpdf {
        width: 100%;
        border-collapse: collapse;
        margin: 4pt 0;
    }

    .hr-mpdf td {
        border-top: 2pt solid #000;
        /* tebal & pekat */
        height: 1;
        line-height: 1;
        padding: 0;
        font-size: 0;
    }

    .section-heading {
        font-weight: 700;
        text-transform: uppercase;
        padding: 2pt 0;
    }

    .kv {
        width: 100%;
        table-layout: fixed;
    }

    /* kunci lebar kolom */
    .kv td {
        vertical-align: top;
        padding: 2px 0;
    }

    .kv .value {
        white-space: normal;
        word-break: break-word;
    }

    .rule {
        /* garis solid yang stabil di print/pdf */
        border: 0;
        height: 0;
        border-top: 1pt solid #000;
        /* 1pt lebih konsisten dari 1px */
        margin: 4px auto;
    }

    .rule.w100 {
        width: 100%;
    }

    .rule.w93 {
        width: 93%;
    }

    /* Garis aman untuk dot-matrix */
    .rule-dot {
        border: 0;
        /* jangan pakai border hairline */
        height: 1.0mm;
        /* balok padat, ~2–3 dot printer */
        background: #000;
        /* hitam pekat */
        margin: 3pt 0;
    }

    @media print {
        .rule-dot {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>
<?php
if (count($res) > 0) {
    $nom = 0;
    foreach ($res as $data2) {
        $nom++;
        $volume_po = $data2['volume_po'];
        $ongkos_po = $data2['ongkos_po'];
        $id_wilayah = $data2['id_wilayah'];
        $id_terminal = $data2['id_terminal'];
        $manual_segel = $data2['manual_segel'];
        $jumlah_po = $volume_po * $ongkos_po;
        $tempal = str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data2['nama_kab']);
        $alamat    = $data2['alamat_survey'] . " " . $tempal . " " . $data2['nama_prov'];
        $picust    = json_decode($data2['picustomer'], true);

        $seg_aw = ($data2['nomor_segel_awal']) ? str_pad($data2['nomor_segel_awal'], 4, '0', STR_PAD_LEFT) : '';
        $seg_ak = ($data2['nomor_segel_akhir']) ? str_pad($data2['nomor_segel_akhir'], 4, '0', STR_PAD_LEFT) : '';

        // METODE SEGEL LAMA
        if ($data2['jumlah_segel'] == 1) {
            $nomor_segel = $data2['pre_segel'] . "-" . $seg_aw;
        } elseif ($data2['jumlah_segel'] == 2) {
            $nomor_segel = $data2['pre_segel'] . "-" . $seg_aw . ", " . $data2['pre_segel'] . "-" . $seg_ak;
        } else {
            // Inisialisasi array untuk menampung daftar nomor segel
            $daftar_nomor_segel = array();

            // Iterasi melalui setiap nomor segel dalam rentang dan tambahkan ke dalam daftar
            for ($i = $seg_aw; $i <= $seg_ak; $i++) {
                $daftar_nomor_segel[] = $data2['pre_segel'] . "-" . str_pad($i, 4, '0', STR_PAD_LEFT);
            }

            // Gabungkan daftar nomor segel menjadi string terpisah dengan koma
            $nomor_segel = implode(", ", $daftar_nomor_segel);
        }



        $id_dsd   = $data2['id_dsd'];
        $barcode    = 'https://barcode.proenergi.com/' . paramEncrypt($id_dsd);
        //if ($data2['is_approved']) {
?>

        <htmlpagefooter name="myHTMLFooter1">



            <br>

            <p style=" margin:0; text-align:center; font-size:7pt;"><i>(This form is valid with sign by computerized system)</i></p>
            <p style="margin:0; text-align:center; font-size:6pt;">Printed by <?php echo $printe; ?></p>
        </htmlpagefooter>
        <sethtmlpagefooter name="myHTMLFooter1" page="ALL" value="on" show-this-page="1" />
        <div class="container">
            <table border="0" width="100%" style="border-collapse:collapse;">
                <tr>
                    <td width="30%">
                        <div style="padding:0;"><img src="<?php echo BASE_IMAGE . "/logo-kiri-penawaran.png"; ?>" width="20%" /></div>
                    </td>
                    <td width="50%" align="center">
                        <h2><u border="2">DELIVERY & RECEIPT NOTE</u></h2>
                        <h2><b><?= $data2['no_spj']; ?></b></h2>
                    </td>
                    <td width="30%" align="right">
                    </td>

                </tr>
            </table>
            <table border="0" width="100%">
                <tr>
                    <td width="30%">
                        <div style="padding:0;"></div>
                    </td>
                    <td>
                    <td width="37% " align="center">
                    </td>
                    </td>
                    <td width="30%"></td>
                </tr>
            </table>

        </div>

        <table border="0" width="100%">
            <tr>
                <!-- ORIGIN -->
                <td width="40%" style="vertical-align:top;">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="tabel_rincian" style="margin-bottom:10px; font-size: 11pt;">
                        <tr>
                            <td border="1" style="text-align:left; border-top:1px solid #000;"><b>ORIGIN</b></td>

                            <td style="text-align:left; border-top:1px solid #000;"></td>

                            <td style="text-align:left; border-top:1px solid #000;"></td>

                        </tr>
                        <tr>
                            <td border="1" style="text-align:center; border-top:1px solid #000;"></td>

                            <td style="text-align:center; border-top:1px solid #000;"></td>

                            <td style="text-align:center; border-top:1px solid #000;"></td>

                        </tr>
                    </table>
                    <table class="kv" cellspacing="0" cellpadding="0">
                        <colgroup>
                            <col style="width:110px"> <!-- label -->
                            <col style="width:12px"> <!-- ":" -->
                            <col> <!-- nilai -->
                        </colgroup>

                        <tr>
                            <td>Loading</td>
                            <td>:</td>
                            <td class="value"><?php echo ($code == 'yes' && $data2['initial'] != '' ? $data2['initial'] : $data2['nama_terminal'] . ' ' . $data2['tanki_terminal']) . ', ' . $data2['lokasi_terminal']; ?></td>
                        </tr>
                        <tr>
                            <td>Date</td>
                            <td>:</td>
                            <td class="value"><?= $data2['nama_cabang'] . ', ' . tgl_indo($res[0]['tgl_approved']); ?></td>
                        </tr>
                        <tr>
                            <td>Transporter</td>
                            <td>:</td>
                            <td class="value"><?= $data2['nama_suplier']; ?></td>
                        </tr>
                        <tr>
                            <td>Driver</td>
                            <td>:</td>
                            <td class="value"><?= $data2['nama_sopir']; ?></td>
                        </tr>
                        <tr>
                            <td>Nopol</td>
                            <td>:</td>
                            <td class="value"><?= $data2['nomor_plat']; ?></td>
                        </tr>
                    </table>
                </td>

                <!-- SOLD TO -->
                <td width="70%" class="section" style="vertical-align:top;">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="tabel_rincian" style="margin-bottom:10px; font-size: 11pt;">



                        <tr>
                            <td border="1" style="text-align:left; border-top:1px solid #000;"><b>SHIP TO</b></td>

                            <td style="text-align:left; border-top:1px solid #000;"></td>

                            <td style="text-align:left; border-top:1px solid #000;"></td>

                        </tr>
                        <tr>
                            <td border="1" style="text-align:center; border-top:1px solid #000;"></td>

                            <td style="text-align:center; border-top:1px solid #000;"></td>

                            <td style="text-align:center; border-top:1px solid #000;"></td>

                        </tr>
                    </table>

                    <table class="kv" cellspacing="0" cellpadding="0">
                        <colgroup>
                            <col style="width:110px">
                            <col style="width:12px">
                            <col>
                        </colgroup>

                        <tr>
                            <td>No.Po</td>
                            <td>:</td>
                            <td class="value"><?= $data2['nomor_poc']; ?></td>
                        </tr>
                        <tr>
                            <td>Consignee</td>
                            <td>:</td>
                            <td class="value">
                                <?= nl2br(htmlspecialchars($data2['nama_customer'])) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Shipping Address</td>
                            <td>:</td>
                            <td class="value"><?= nl2br(htmlspecialchars($alamat)) ?></td>
                        </tr>
                        <tr>
                            <td>PIC</td>
                            <td>:</td>
                            <td class="value">
                                <?php if (!empty($picust)) {
                                    foreach ($picust as $row) {
                                        echo '<div><b>' . htmlspecialchars($row['nama']) . ' - ' . htmlspecialchars($row['telepon']) . '</b></div>';
                                    }
                                } else {
                                    echo '&nbsp;';
                                } ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <br>

        <div style="clear:both"></div>
        <table border="1" style="border: 1px solid #888; border-collapse: collapse;" cellpadding="5" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th colspan="3" align="center" class="b1 b3 b4">DESCRIPTION</th>
                </tr>
                <tr>
                    <th align="center" class="b1 b3 b4" width="20%">ITEM</th>
                    <th align="center" class="b1 b3 b4" width="30%">VOLUME (L)</th>
                    <th align="center" class="b1 b3 b4" width="20%">WRITTEN AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td valign="top" align="center" style="font-size: 11pt;">
                        <?php echo $data2['produk']; ?>
                    </td>
                    <td valign="top" align="center" style="font-size: 11pt;">
                        <?php echo number_format($volume_po, 0, '', '.'); ?>
                    </td>
                    <td align="center" height="30px" style="font-size: 11pt;" valign="top">
                        <?php echo terbilang($volume_po) . " Liter"; ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <br>
        <table width="100%" height="100px" border="0" cellspacing="0" cellpadding="5">
            <tr>
                <th width="50%">
                    <table width="100%" align="left" border="0">
                        <tr>
                            <td width="17%">
                                <table width="100%" border="1" style="border: 1px solid #888; border-collapse: collapse;" cellspacing=" 0" cellpadding="5">
                                    <tr>
                                        <td valign="top" height="80px" align="left" class="b1 b3 b4" style="font-size: 10pt;font-family:Arial;">
                                            <?php
                                            $seal = ($id_wilayah == 11 && $id_terminal == 73) ? $manual_segel : $nomor_segel;
                                            echo 'Seal : ' . $seal;
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </th>
                <th align=" right">
                    <table width="100%" cellspacing=" 0" cellpadding="5">
                        <tr>
                            <td align="left" style="font-size: 10pt;font-family:Arial;">Loading Date</td>
                            <td style="font-size: 10pt;font-family:Arial;">:</td>
                            <td align="left" style="font-size: 10pt;font-family:Arial;"><?= tgl_indo($data2['tanggal_loading']); ?></td>
                            <td style="font-size: 10pt;font-family:Arial;">Time :</td>
                            <td style="font-size: 10pt;font-family:Arial;"><?= $data2['jam_loading']; ?></td>
                        </tr>

                        <tr>
                            <td align="left" style="font-size: 10pt;font-family:Arial;"><strong>Receive Date </strong></td>
                            <td style="font-size: 10pt;font-family:Arial;">:</td>
                            <td></td>
                            <td style="font-size: 10pt;font-family:Arial;"><strong>Time :</strong></td>
                            <td></td>
                        </tr>
                    </table>
                </th>
            </tr>
        </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="5">
            <tr>
                <th width="100%">
                    <table width="100%" align="left" border="0">
                        <tr>
                            <td width="30%">
                                <table border="1" style="border: 1px solid #888; border-collapse: collapse;" width="100%" cellspacing="0" cellpadding="5">
                                    <tr>
                                        <th align="left"> REMARK </th>
                                    </tr>
                                    <tr>
                                        <td valign="top" height="100px" align="left" class="b1 b3 b4">
                                            <table width="100%">
                                                <tr>
                                                    <td align="left">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td height="80px" valign="top" align="left" cellspacing=" 0" cellpadding="5">
                                                        <table>
                                                            <tr>
                                                                <td> <strong>Quantity Received : __________________ Liter , &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Losses : __________________ Liter</strong></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td> </td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td> </td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Saat serah terima, telah diperiksa semua segel dalam keadaan baik, tanpa cacat dan BBM diterima sesuai dengan spesifikasi dan volume tersebut diatas.</td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>

                                                            <tr>
                                                                <td></td>
                                                                <td> </td>
                                                                <td></td>
                                                            </tr>



                                                        </table>
                                                    </td>


                                                </tr>

                                                <tr>

                                                    <td width="100%" style="border:1px solid #000; padding:10px;">
                                                        <table width="100%" cellpadding="5" cellspacing="0" style="border-collapse:collapse;">
                                                            <tr>
                                                                <td> <strong>Unloading Note :</strong></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td> </td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td> </td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>




                                                        </table>
                                                    </td>


                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>




                        </tr>
                    </table>
                </th>

            </tr>
        </table>

        <table width="100%" cellpadding="0" cellspacing="0" class="tabel_rincian" style="margin-bottom:10px; font-size:11px;">
            <!-- Baris label di atas garis tanda tangan -->
            <tr>
                <td width="30%" style="text-align:center;">Prepared By</td>
                <td width="5%"></td>
                <td width="30%" style="text-align:center;">Delivered By</td>
                <td width="5%"></td>
                <td width="30%" style="text-align:center;">Received By</td>
                <td width="2%"></td>
            </tr>

            <!-- Ruang untuk tanda tangan -->
            <tr>
                <td style="text-align:center; height:60px;">&nbsp;</td>
                <td>&nbsp;</td>
                <td style="text-align:center; height:60px;">&nbsp;</td>
                <td>&nbsp;</td>
                <td style="text-align:center; height:60px;">&nbsp;</td>
                <td>&nbsp;</td>
            </tr>

            <!-- Nama di atas garis -->
            <tr>
                <td style="text-align:center;"><?php echo htmlspecialchars($res[0]['created_by'] ?? ''); ?></td>
                <td>&nbsp;</td>
                <td style="text-align:center;"><?php echo htmlspecialchars($data2['nama_sopir'] ?? ''); ?></td>
                <td>&nbsp;</td>
                <td style="text-align:center;"><?php /* biasanya dikosongkan agar diisi penerima */ echo '&nbsp;'; ?></td>
                <td>&nbsp;</td>
            </tr>

            <!-- Garis dan jabatan/instansi di bawahnya -->
            <tr>
                <td style="text-align:center; border-top:1px solid #000;">PT. Pro Energi</td>
                <td>&nbsp;</td>
                <td style="text-align:center; border-top:1px solid #000;">Driver</td>
                <td>&nbsp;</td>
                <td style="text-align:center; border-top:1px solid #000;"><?php echo htmlspecialchars($data2['nama_customer']); ?></td>
                <td>&nbsp;</td>
            </tr>
        </table>



        <br>
        <div style="margin:0; text-align:right;">
            <p style="margin:0; text-align:left; font-size:7pt;"> <i><strong> Note*: Mohon diisi pada kolom receive date, quantity received & unloading note jika diperlukan oleh pihak customer. </strong></i></p>
            <barcode code="<?php echo $barcode; ?>" type="QR" size="0.75" />
        </div>









<?php if ($nom < count($res)) echo '<pagebreak sheet-size="A4" margin-left="10mm" margin-right="10mm" margin-top="20mm" margin-bottom="10mm" />';
    }
} ?>
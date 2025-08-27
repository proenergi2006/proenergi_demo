<style>
    .tabel_header td {
        padding: 1px 3px;
        font-size: 9pt;
        height: 18px;
    }

    .tabel_rincian td {
        height: 18px;
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
        font-weight: bold;
        vertical-align: top;
    }

    .td-subisi {
        font-size: 5pt;
    }

    .td-ket {
        font-size: 6pt;
    }

    .isi-spj {
        padding: 1px 0px 2px;
        vertical-align: top;
        font-size: 8pt;
        font-family: arial;
    }

    .isi-spj2 {
        padding: 1px;
        vertical-align: top;
        font-size: 8pt;
        font-family: arial;
    }
</style>

<?php
if (count($res) > 0) {
    $nom = 0;
    foreach ($res as $data2) {
        $nom++;
        $tgl_eta = $data2['tgl_eta_po'];
        $volume_po = $data2['volume_po'];
        $ongkos_po = $data2['ongkos_po'];
        $jumlah_po = $volume_po * $ongkos_po;
        $tempal = str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data2['nama_kab']);
        $alamat    = $data2['alamat_survey'] . " " . $tempal . " " . $data2['nama_prov'];
        $picust    = json_decode($data2['picustomer'], true);
        $code   = $data2['kode_barcode'] . '-' . '04' . '-' . $data2['id_pod'];
        $bar    = BASE_URL . "/barcode_result.php?idr=" . paramEncrypt($code);
        if ($data2['is_approved']) {
?>
            <p style="margin-bottom:0px; text-align:center; font-size: 12px;"><u>SURAT JALAN/TANDA TERIMA</u></p>
            <p style="margin-bottom:0px; text-align:center;"><b>NO : <?php echo $data2['no_spj']; ?></b></p>
            <p style="margin-bottom:0px; text-align:center; font-size: 9px;">Ref.DO :
                <?php echo $data2['no_do_syop'] ? $data2['no_do_syop'] : '-' ?>

            </p>
            <div style="margin-left:75%; text-align:center; margin-top: -60px;">
                <barcode size="0.7" code="<?php echo $bar; ?>" type="QR" disableborder="1" />
                <p></p>
                <p style="margin-bottom:0px; text-align:center; font-size: 9px;">Ref.LO:
                    <?php echo $data2['nomor_lo_pr']; ?>
                </p>
            </div>


            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td class="isi-spj" width="15%">No. Polisi</td>
                    <td class="isi-spj" width="2%" align="center">:</td>
                    <td class="isi-spj" width="33%"><b><?php echo $data2['nomor_plat']; ?></b></td>
                    <td class="isi-spj" width="15%">Nama Pengemudi</td>
                    <td class="isi-spj" width="2%" align="center">:</td>
                    <td class="isi-spj" width="33%"><b><?php echo $data2['nama_sopir']; ?></b></td>
                </tr>
                <tr>
                    <td class="isi-spj" colspan="6">&nbsp;</td>
                </tr>
                <tr>
                    <td class="isi-spj" colspan="6"><u>Data Tujuan Pengiriman Barang</u></td>
                </tr>
                <tr>
                    <td class="isi-spj">Nama</td>
                    <td class="isi-spj" align="center">:</td>
                    <td class="isi-spj" colspan="4"><b><?php echo $data2['nama_customer']; ?></b></td>
                </tr>
                <tr>
                    <td class="isi-spj">Alamat</td>
                    <td class="isi-spj" align="center">:</td>
                    <td class="isi-spj" colspan="4"><b><?php echo $alamat; ?></b></td>
                </tr>
                <tr>
                    <td class="isi-spj">PIC</td>
                    <td class="isi-spj" align="center">:</td>
                    <td class="isi-spj" colspan="4">
                        <?php
                        if (count($picust) > 0) {
                            // Inisialisasi string untuk menyimpan semua data
                            $output = '';

                            foreach ($picust as $index => $row) {
                                // Tambahkan data ke dalam string output
                                $output .= '<b>' . $row['nama'] . ' - ' . html_entity_decode($row['telepon']) . '</b>';

                                // Tambahkan koma jika bukan item terakhir
                                if ($index < count($picust) - 1) {
                                    $output .= ', ';
                                }
                            }

                            // Tampilkan semua data secara horizontal
                            echo $output;
                        } else {
                            echo '&nbsp;';
                        }
                        ?></td>
                </tr>
                <tr>
                    <td class="isi-spj" colspan="6">&nbsp;</td>
                </tr>
                <tr>
                    <td class="isi-spj" colspan="6">Sesuai dengan PO nomor <b><?php echo $data2['nomor_poc']; ?></b> kami kirimkan BBM dgn spesifikasi sebagai berikut</td>
                </tr>
                <tr>
                    <td class="isi-spj">Jenis</td>
                    <td class="isi-spj" align="center">:</td>
                    <td class="isi-spj">
                        <?php
                        // ambil nilai full
                        $fullProduk = $data2['print_product'] ?? '';

                        // potong pada koma pertama (atau line-break)
                        // explode pertama by comma
                        $parts = explode(',', $fullProduk);
                        // bagian pertama
                        $shortProduk = trim($parts[0]);
                        ?>
                        <b>
                            <?php if (!empty($data2['print_product'])): ?>
                                <?php echo $data2['produk']; ?> - <?php echo $shortProduk; ?>
                            <?php else: ?>
                                <?php echo $data2['produk']; ?>
                            <?php endif; ?>
                        </b>
                    </td>
                    <td class="isi-spj" colspan="3"> ETA : <b><?php echo tgl_indo($tgl_eta); ?></b></td>
                </tr>
                <tr>
                    <td class="isi-spj">Volume</td>
                    <td class="isi-spj" align="center">:</td>
                    <td class="isi-spj"><b><?php echo number_format($volume_po, 0, '', '.'); ?></b></td>
                    <td class="isi-spj" colspan="3">Terbilang : <b><?php echo terbilang($volume_po) . " Liter"; ?></b></td>
                </tr>
                <tr>
                    <td class="isi-spj">Catatan</td>
                    <td class="isi-spj" align="center">:</td>
                    <td class="isi-spj" colspan="4"><b><?php echo $data2['status_jadwal']; ?></b></td>
                </tr>
            </table>

            <p style="font-size:8pt; font-family:arial;">Saat serah terima, telah diperiksa semua segel dalam keadaan baik, tanpa cacat dan BBM diterima sesuai dengan spesifikasi dan volume tersebut diatas.</p>
            <p style="font-size:8pt; font-family:arial;"><?php echo $data2['nama_cabang'] . ", " . tgl_indo($res[0]['tanggal_kirim']); ?></p>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td class="isi-spj2 b1 b3 b4" align="center" width="21%">Logistik</td>
                    <td class="isi-spj2 b1 b2 b3 b4" align="center" width="21%">Diserahkan oleh</td>
                    <td class="isi-spj2" width="6%">&nbsp;</td>
                    <td class="isi-spj2 b1 b2 b3 b4" colspan="2" align="center">Diterima oleh</td>
                </tr>
                <tr>
                    <td width="21%" rowspan="5" class="isi-spj2 b3 b4">&nbsp;</td>
                    <td width="21%" rowspan="5" class="isi-spj2 b2 b3 b4">&nbsp;</td>
                    <td class="isi-spj2">&nbsp;</td>
                    <td class="isi-spj2 b4" width="21%">Nama :</td>
                    <td class="isi-spj2 b2 b4" width="21%">Jabatan :</td>
                </tr>
                <tr>
                    <td class="isi-spj2">&nbsp;</td>
                    <td class="isi-spj2 b3 b4">Tanggal :</td>
                    <td class="isi-spj2 b2 b3 b4">Jam :</td>
                </tr>
                <tr>
                    <td class="isi-spj2">&nbsp;</td>
                    <td class="isi-spj2 b4">Tanda Tangan</td>
                    <td class="isi-spj2 b2 b4">Stempel</td>
                </tr>
                <tr>
                    <td class="isi-spj2">&nbsp;</td>
                    <td rowspan="4" class="isi-spj2 b3 b4">&nbsp;</td>
                    <td rowspan="4" class="isi-spj2 b2 b3 b4">&nbsp;</td>
                </tr>
                <tr>
                    <td class="isi-spj2">&nbsp;</td>
                </tr>
                <tr>
                    <td class="isi-spj2 b4">Nama : <?php echo $res[0]['created_by']; ?></td>
                    <td class="isi-spj2 b2 b4">Nama :</td>
                    <td class="isi-spj2">&nbsp;</td>
                </tr>
                <tr>
                    <td class="isi-spj2 b3 b4">Tanggal : <?php echo date("d/m/Y", strtotime($res[0]['tanggal_kirim'])); ?></td>
                    <td class="isi-spj2 b2 b3 b4">Tanggal :</td>
                    <td class="isi-spj2">&nbsp;</td>
                </tr>
            </table>
            <p style="margin:0; text-align:right; font-size:6pt; font-family:arial;">Created by <?php echo $created; ?></p>
            <p style="margin:0; text-align:right; font-size:6pt; font-family:arial;">Printed by <?php echo $printe; ?></p>
        <?php } ?>
        <?php
        if (($nom % 2) != 0)
            echo '<div style="border-top:4px double #333; margin:3px 0px 35px;">&nbsp;</div>';
        else if (($nom % 2) == 0 && $nom < count($res)) echo '<pagebreak />';
        ?>
<?php }
} ?>
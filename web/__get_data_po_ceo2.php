<?php
$sql = "select a.*,b.nama_vendor, c.merk_dagang, d.nama_terminal 
        from new_pro_inventory_vendor_po a 
        join pro_master_vendor b on a.id_vendor = b.id_master 
        join pro_master_produk c on a.id_produk = c.id_master 
        join pro_master_terminal d on a.id_terminal = d.id_master  
        where a.id_master = '" . $idr . "'";
$res = $con->getResult($sql);
$fnr = ($res[0]['ceo_result']);

$fnr = ($fnr ? $fnr : null);
if (count($res) == 0) {
    echo '<tr><td colspan="16" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
    $nom = 0;
    foreach ($res as $data) {
        $nom++;

?>
        <div class="form-group row">
            <div class="col-sm-6">
                <div class="table-responsive">
                    <table class="table no-border table-detail">
                        <tr>
                            <td width="100">Vendor</td>
                            <td width="10">:</td>
                            <td><?php echo $data['nama_vendor']; ?></td>
                        </tr>

                        <tr>
                            <td>Produk</td>
                            <td>:</td>
                            <td><?php echo $data['merk_dagang']; ?></td>
                        </tr>
                        <tr>
                            <td width="100">Terminal</td>
                            <td width="10">:</td>
                            <td><?php echo $data['nama_terminal']; ?></td>
                        </tr>
                        <tr>
                            <?php
                            $harga_tebus = $data['harga_tebus'];

                            // Menggunakan fmod untuk memeriksa apakah ada nilai desimal yang signifikan
                            if (fmod($harga_tebus, 1) == 0) {
                                // Jika tidak ada nilai desimal yang signifikan, tampilkan sebagai angka bulat
                                $formatted_harga_tebus = number_format($harga_tebus, 0, '.', ',');
                            } else {
                                // Jika ada nilai desimal yang signifikan, tampilkan dengan format desimal
                                $formatted_harga_tebus = number_format($harga_tebus, 2, '.', ',');
                            }
                            ?>
                            <td width="100">Harga Dasar</td>
                            <td width="10">:</td>
                            <td> Rp. <?php echo $formatted_harga_tebus ?> </td>
                        </tr>
                        <?php if ($data['kategori_oa'] == 2) : ?>
                            <tr>
                                <?php
                                $ongkos_angkut = $data['ongkos_angkut'];

                                // Menggunakan fmod untuk memeriksa apakah ada nilai desimal yang signifikan
                                if (fmod($ongkos_angkut, 1) == 0) {
                                    // Jika tidak ada nilai desimal yang signifikan, tampilkan sebagai angka bulat
                                    $formatted_ongkos_angkut = number_format($ongkos_angkut, 0, '.', ',');
                                } else {
                                    // Jika ada nilai desimal yang signifikan, tampilkan dengan format desimal
                                    $formatted_ongkos_angkut = number_format($ongkos_angkut, 2, '.', ',');
                                }
                                ?>
                                <td width="100">Ongkos Angkut</td>
                                <td width="10">:</td>
                                <td> Rp. <?php echo $formatted_ongkos_angkut ?> </td>
                            </tr>
                            <tr>
                                <td width="100">Kategori Plat</td>
                                <td width="10">:</td>
                                <td> <?= $data['kategori_plat'] ?> </td>
                            </tr>
                        <?php endif ?>
                        <tr>
                            <?php
                            $subtotal = $data['subtotal'];

                            // Menggunakan fmod untuk memeriksa apakah ada nilai desimal yang signifikan
                            if (fmod($subtotal, 1) == 0) {
                                // Jika tidak ada nilai desimal yang signifikan, tampilkan sebagai angka bulat
                                $formatted_subtotal = number_format($subtotal, 0, '.', ',');
                            } else {
                                // Jika ada nilai desimal yang signifikan, tampilkan dengan format desimal
                                $formatted_subtotal = number_format($subtotal, 2, '.', ',');
                            }
                            ?>
                            <td width="100">Subtotal</td>
                            <td width="10">:</td>
                            <td>Rp. <?php echo $formatted_subtotal ?></td>
                        </tr>

                        <tr>
                            <?php
                            $ppn11 = $data['ppn_11'];

                            // Menggunakan fmod untuk memeriksa apakah ada nilai desimal yang signifikan
                            if (fmod($ppn11, 1) == 0) {
                                // Jika tidak ada nilai desimal yang signifikan, tampilkan sebagai angka bulat
                                $formatted_ppn11 = number_format($ppn11, 0, '.', ',');
                            } else {
                                // Jika ada nilai desimal yang signifikan, tampilkan dengan format desimal
                                $formatted_ppn11 = number_format($ppn11, 2, '.', ',');
                            }
                            ?>
                            <td width="100">PPN 11%</td>
                            <td width="10">:</td>
                            <td>Rp. <?php echo $formatted_ppn11 ?></td>
                        </tr>

                        <tr>
                            <?php
                            $pph22 = $data['pph_22'];

                            // Menggunakan fmod untuk memeriksa apakah ada nilai desimal yang signifikan
                            if (fmod($pph22, 1) == 0) {
                                // Jika tidak ada nilai desimal yang signifikan, tampilkan sebagai angka bulat
                                $formatted_pph22 = number_format($pph22, 0, '.', ',');
                            } else {
                                // Jika ada nilai desimal yang signifikan, tampilkan dengan format desimal
                                $formatted_pph22 = number_format($pph22, 2, '.', ',');
                            }
                            ?>
                            <td width="100">PPH 22</td>
                            <td width="10">:</td>
                            <td>Rp. <?php echo  $formatted_pph22 ?></td>
                        </tr>

                        <tr>
                            <?php
                            $pbbkb = $data['pbbkb'];

                            // Menggunakan fmod untuk memeriksa apakah ada nilai desimal yang signifikan
                            if (fmod($pbbkb, 1) == 0) {
                                // Jika tidak ada nilai desimal yang signifikan, tampilkan sebagai angka bulat
                                $formatted_pbbkb = number_format($pbbkb, 0, '.', ',');
                            } else {
                                // Jika ada nilai desimal yang signifikan, tampilkan dengan format desimal
                                $formatted_pbbkb = number_format($pbbkb, 2, '.', ',');
                            }
                            ?>
                            <td width="100">PBBKB <?= $data['nilai_pbbkb'] ?>%</td>
                            <td width="10">:</td>
                            <td>Rp. <?php echo  $formatted_pbbkb ?></td>
                        </tr>

                        <tr>
                            <?php
                            $iuran_migas = $data['nominal_migas'];

                            // Menggunakan fmod untuk memeriksa apakah ada nilai desimal yang signifikan
                            if (fmod($iuran_migas, 1) == 0) {
                                // Jika tidak ada nilai desimal yang signifikan, tampilkan sebagai angka bulat
                                $formatted_iuran = number_format($iuran_migas, 0, '.', ',');
                            } else {
                                // Jika ada nilai desimal yang signifikan, tampilkan dengan format desimal
                                $formatted_iuran = number_format($iuran_migas, 2, '.', ',');
                            }
                            ?>
                            <td width="100">Iuran Migas</td>
                            <td width="10">:</td>
                            <td>Rp. <?php echo  $formatted_iuran ?></td>
                        </tr>

                        <tr>
                            <?php
                            $total_order = $data['total_order'];

                            // Menggunakan fmod untuk memeriksa apakah ada nilai desimal yang signifikan
                            if (fmod($total_order, 1) == 0) {
                                // Jika tidak ada nilai desimal yang signifikan, tampilkan sebagai angka bulat
                                $formatted_total_order = number_format($total_order, 0, '.', ',');
                            } else {
                                // Jika ada nilai desimal yang signifikan, tampilkan dengan format desimal
                                $formatted_total_order = number_format($total_order, 2, '.', ',');
                            }
                            ?>
                            <td width="100">Total Order</td>
                            <td width="10">:</td>
                            <td>Rp. <?php echo  $formatted_total_order ?></td>
                        </tr>

                        <tr>
                            <td width="100">Kode Tax</td>
                            <td width="10">:</td>
                            <td><?php echo $data['kd_tax']; ?></td>
                        </tr>

                        <tr>
                            <td width="100">Terms</td>
                            <td width="10">:</td>
                            <td><?php echo $data['terms']; ?></td>
                        </tr>

                        <tr>
                            <td width="100">Terms Day</td>
                            <td width="10">:</td>
                            <td><?php echo $data['terms_day']; ?></td>
                        </tr>

                        <tr>
                            <td width="100">Catatan PO </td>
                            <td width="10">:</td>
                            <td><?php echo $data['keterangan']; ?></td>
                        </tr>



                <?php }
        } ?>
                    </table>
                </div>
            </div>
        </div>

        <hr>



        <?php if ($data['disposisi_po'] == 2) { ?>
            <div class="form-group row">
                <div class="col-sm-4">
                    <label>Dikembalikan ke Purchasing ?*</label>
                    <div class="radio clearfix" style="margin:0px;">
                        <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="revert" id="revert1" class="validate[required]" value="1" /> Ya</label>
                        <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="revert" id="revert2" class="validate[required]" value="2" /> Tidak</label>
                    </div>
                </div>
                <div class="col-sm-4 col-sm-top kembalikan-cfo" <?php echo (!$fnr) ? 'disabled' : ''; ?> <label>Catatan Di kembalikan</label>
                    <textarea name="summary_revert" id="summary_revert" class="form-control"></textarea>
                </div>

            </div>
        <?php } ?>


        <div class="form-group row persetujuan-ceo" <?php echo (!$fnr) ? 'hide' : ''; ?>>

            <div class="col-sm-6">
                <?php if ($data['ceo_result'] == 0 && $data['revert_cfo'] == 0) { ?>
                    <label>Catatan CEO</label>


                    <textarea name="summary" id="summary" class="form-control"></textarea>
                <?php } elseif ($data['ceo_result'] == 1 && $data['revert_ceo'] == 0) { ?>
                    <label>Catatan CEO</label>

                    <div class="form-control" style="height:auto">
                        <?php echo ($res[0]['ceo_summary'] ? $res[0]['ceo_summary'] : $res[0]['ceo_summary']); ?>
                        <?php
                        $picnya1 = ($res[0]['ceo_pic'] ? $res[0]['ceo_pic'] : $res[0]['ceo_pic']);
                        $tglnya1 = ($res[0]['ceo_tanggal'] ? $res[0]['ceo_tanggal'] : $res[0]['ceo_tanggal']);
                        ?>
                        <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $picnya1 . " - " . date("d/m/Y H:i:s", strtotime($tglnya1)) . " WIB"; ?></i></p>
                    </div>
                <?php } ?>
            </div>
        </div>


        <div class="row">
            <?php if ($res && $res[0]['revert_cfo']) { ?>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Catatan Pengembalian CFO</label>
                        <div class="form-control" style="height:auto"><?php echo ($res[0]['revert_cfo_summary']); ?></div>
                    </div>
                </div>
            <?php }
            if ($res && $res[0]['revert_ceo']) { ?>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Catatan Pengembalian CEO</label>
                        <div class="form-control" style="height:auto"><?php echo ($res[0]['revert_ceo_summary'] ? $res[0]['revert_ceo_summary'] : '&nbsp;'); ?></div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div class="form-group row ">
            <div class="col-sm-6">
                <?php if ($data['cfo_result'] == 1 && $data['revert_cfo'] == 0) { ?>
                    <label>Catatan CFO</label>

                    <div class="form-control" style="height:auto">
                        <?php echo ($res[0]['cfo_summary'] ? $res[0]['cfo_summary'] : $res[0]['ceo_summary']); ?>
                        <?php
                        $picnya = ($res[0]['cfo_pic'] ? $res[0]['cfo_pic'] : $res[0]['cfo_pic']);
                        $tglnya = ($res[0]['cfo_tanggal'] ? $res[0]['cfo_tanggal'] : $res[0]['cfo_tanggal']);
                        ?>
                        <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $picnya . " - " . date("d/m/Y H:i:s", strtotime($tglnya)) . " WIB"; ?></i></p>
                    </div>
                <?php } ?>
            </div>
        </div>











        <?php if (count($res) > 0) { ?>
            <p>&nbsp;</p>
            <div class="row">
                <div class="col-sm-6">
                    <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                    <input type="hidden" name="idw" value="<?php echo $row['id_wilayah']; ?>" />
                    <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT . "/verifikasi-po.php"; ?>">Kembali</a>
                    <?php if ($data['ceo_result'] == 0 && $data['revert_cfo'] == 0 && $data['cfo_result'] == 1) { ?>
                        <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt">Simpan</button>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
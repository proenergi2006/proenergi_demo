<div style="overflow-x: scroll" id="table-long">
    <div style="width:1965px; height:auto;">
        <div class="table-responsive-satu">
            <table class="table table-bordered" id="table-grid3">
                <thead>
                    <tr>
                        <th class="text-center" width="50">No.</th>
                        <th class="text-center" width="60">Ref DN</th>
                        <th class="text-center" width="170">Customer</th>
                        <th class="text-center" width="200">Area/ Alamat Kirim/ Wilayah OA</th>
                        <th class="text-center" width="200">PO Customer</th>
                        <th class="text-center" width="200">PO Transportir</th>
                        <th class="text-center" width="200">Keterangan Lain</th>
                        <th class="text-center" width="85">Tgl Jam Loading</th>
                        <th class="text-center" width="100">No Order</th>
                        <th class="text-center" width="130">Segel</th>
                        <th class="text-center" width="150">Sold To</th>
                        <th class="text-center" width="150">Remark To Depo</th>
                        <th class="text-center" width="100">SPJ<br /> Truck/ Driver</th>
                        <th class="text-center" width="130">Nomor DO</th>
                        <th class="text-center" width="130">Trip</th>
                        <th class="text-center" width="130">No Pengiriman</th>
                        <th class="text-center" width="130">Status Pengiriman</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select a.*, h.kode_pelanggan, h.id_customer, h.nama_customer, g.id_lcr, g.alamat_survey, i.nama_prov, j.nama_kab, q.fullname, l.nama_area, 
                            o.nama_transportir, o.nama_suplier, b.no_spj, b.mobil_po, m.nomor_plat, n.nama_sopir, b.volume_po, p.wilayah_angkut, d.produk, 
                            c.nomor_po, b.multidrop_po, b.trip_po, b.nomor_oslog, d.no_do_syop, d.nomor_lo_pr, f.nomor_poc, e.tanggal_kirim, e.volume_kirim, c.id_wilayah,ai.id_terminal
                            from pro_po_ds_detail a 
                            join pro_po_detail b on a.id_pod = b.id_pod 
                            join pro_po c on a.id_po = c.id_po 
                            join pro_pr_detail d on a.id_prd = d.id_prd 
                            join pro_po_customer_plan e on a.id_plan = e.id_plan 
                            join pro_po_customer f on e.id_poc = f.id_poc 
                            join pro_customer_lcr g on e.id_lcr = g.id_lcr
                            join pro_customer h on f.id_customer = h.id_customer 
                            join pro_master_provinsi i on g.prov_survey = i.id_prov 
                            join pro_master_kabupaten j on g.kab_survey = j.id_kab
                            join pro_penawaran k on f.id_penawaran = k.id_penawaran  
                            join pro_master_area l on k.id_area = l.id_master 
                            join pro_master_transportir_mobil m on b.mobil_po = m.id_master 
                            join pro_master_transportir_sopir n on b.sopir_po = n.id_master 
                            join pro_master_transportir o on c.id_transportir = o.id_master 
                            join pro_master_wilayah_angkut p on g.id_wil_oa = p.id_master and g.prov_survey = p.id_prov and g.kab_survey = p.id_kab 
                            join acl_user q on h.id_marketing = q.id_user 
                              join pro_po_ds ai on a.id_ds = ai.id_ds
                            where a.id_ds = '" . $idr . "' 
                            order by a.nomor_urut_ds, a.tanggal_loading, a.jam_loading, c.id_po, m.nomor_plat, b.trip_po, b.multidrop_po";
                    $res = $con->getResult($sql);
                    if (count($res) == 0) {
                        echo '<tr><td colspan="14" style="text-align:center">Data tidak ditemukan </td></tr>';
                    } else {
                        $nom = 0;
                        foreach ($res as $data) {
                            $nom++;
                            $idp = $data['id_dsd'];
                            $tempal = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
                            $alamat    = $data['alamat_survey'] . " " . ucwords($tempal) . " " . $data['nama_prov'];
                            $jamLoa = ($data['jam_loading']) ? date("H:i", strtotime($data['jam_loading'])) : "04:00";
                            $nom_do = ($data['nomor_do']) ? $data['nomor_do'] : '';
                            $ref_dn = ($data['nomor_ref_dn']) ? $data['nomor_ref_dn'] : $nom;
                            $tgload = date("d/m/Y", strtotime($data['tanggal_loading']));
                            $nom_oc = $data['nomor_oc'];
                            $nom_or = $data['nomor_order'];
                            $source = $data['source'];
                            $soldto = $data['sold_to'];
                            $remark_to_depo = $data['remark_depo'];
                            $id_customer = $data['id_customer'];
                            $nomor_segel_manual = $data['manual_segel'];

                            $class1 = 'form-control input-po text-center';
                            $class2 = 'form-control input-po datepicker';
                            $class3 = 'form-control input-po timepicker';
                            $class4 = 'form-control input-po';
                            $class5 = 'form-control input-po text-right hitung';

                            $seg_aw = ($data['nomor_segel_awal']) ? str_pad($data['nomor_segel_awal'], 4, '0', STR_PAD_LEFT) : '';
                            $seg_ak = ($data['nomor_segel_akhir']) ? str_pad($data['nomor_segel_akhir'], 4, '0', STR_PAD_LEFT) : '';
                            if ($data['jumlah_segel'] == 1)
                                $nomor_segel = $data['pre_segel'] . "-" . $seg_aw;
                            else if ($data['jumlah_segel'] == 2)
                                $nomor_segel = $data['pre_segel'] . "-" . $seg_aw . " &amp; " . $data['pre_segel'] . "-" . $seg_ak;
                            else if ($data['jumlah_segel'] > 2)
                                $nomor_segel = $data['pre_segel'] . "-" . $seg_aw . " s/d " . $data['pre_segel'] . "-" . $seg_ak;
                            else $nomor_segel = '';

                            $linkCtkBBM = ACTION_CLIENT . '/cetak_bbm.php?' . paramEncrypt('id_dsd=' . $idp);
                            $linkCtkQr = ACTION_CLIENT . '/delivery-loading-cetak-qr.php?' . paramEncrypt('id_dsd=' . $idp);
                    ?>
                            <tr<?php echo ($data['is_cancel'] ? ' style="color:#999;"' : ''); ?>>
                                <td class="text-center">
                                    <?php echo '<input type="hidden" name="ck1[' . $idp . ']" id="ck1' . $nom . '" value="' . $nom . '" />' . $nom; ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    if (!$data['is_cancel'] && !$data['is_delivered'])
                                        echo '<input type="text" name="dt1[' . $idp . ']" id="dt1' . $nom . '" class="' . $class1 . '" value="' . $ref_dn . '" readonly/>';
                                    else echo $ref_dn;
                                    ?></td>
                                <td>
                                    <p style="margin-bottom:0px"><b>
                                            <?php echo ($data['kode_pelanggan'] ? $data['kode_pelanggan'] . '<br/>' : '') . $data['nama_customer']; ?></b>
                                    </p>
                                    <p style="margin-bottom:0px"><i><?php echo $data['fullname']; ?></i></p>
                                    <p style="margin-bottom:0px"><a style="cursor:pointer" class="detLcr" data-idnya="<?php echo $data['id_lcr']; ?>">Detail LCR</a></p>
                                    <br>
                                    <?php if ($data['nomor_do']) : ?>
                                        <?php
                                        $querycek = "SELECT * FROM pro_bpuj WHERE is_active='1' AND id_dsd='" . $idp . "'";
                                        $row_cek = $con->getRecord($querycek);
                                        ?>
                                        <?php if ($row_cek) : ?>
                                            <p style="margin-bottom:0px; color:green;">BPUJ sudah dibuat</p>
                                            <p style="margin-bottom:0px">
                                                <a target="_blank" style="cursor:pointer" href="<?php echo BASE_URL_CLIENT ?>/_get_form_bpuj.php?<?php echo paramEncrypt('id_cust=' . $id_customer . '&id_dsd=' . $idp) ?>" data-idnya="<?php echo $idp; ?>">Lihat BPUJ</a>
                                            </p>
                                            <?php if ($data['id_wilayah'] == 11): ?>
                                                <p style="margin-bottom:0px">
                                                    <a target="_blank"
                                                        style="cursor:pointer;color:red;"
                                                        title="Cetak BBM"
                                                        href="<?= $linkCtkBBM ?>">
                                                        Cetak BBM
                                                    </a>
                                                </p>
                                            <?php endif; ?>
                                        <?php else : ?>
                                            <p style="margin-bottom:0px; color:orange;">BPUJ belum dibuat</p>
                                            <p style="margin-bottom:0px">
                                                <a target="_blank" style="cursor:pointer" href="<?php echo BASE_URL_CLIENT ?>/_get_form_bpuj.php?<?php echo paramEncrypt('id_cust=' . $id_customer . '&id_dsd=' . $idp) ?>" data-idnya="<?php echo $idp; ?>">Buat BPUJ</a>
                                            </p>
                                        <?php endif ?>
                                    <?php endif ?>
                                </td>
                                <td>
                                    <p style="margin-bottom:0px"><b><?php echo $data['nama_area']; ?></b></p>
                                    <p style="margin-bottom:0px"><?php echo $alamat; ?></p>
                                    <p style="margin-bottom:0px"><?php echo 'Wilayah OA : ' . $data['wilayah_angkut']; ?></p>
                                </td>
                                <td>
                                    <p style="margin-bottom:0px"><b><?php echo $data['nomor_poc']; ?></b></p>
                                    <p style="margin-bottom:0px"><?php echo number_format($data['volume_kirim']) . ' Liter ' . $data['produk']; ?></p>
                                    <p style="margin-bottom:0px"><?php echo 'Tgl Kirim ' . tgl_indo($data['tanggal_kirim']); ?></p>
                                </td>
                                <td>
                                    <p style="margin-bottom:0px"><b><?php echo $data['nomor_po']; ?></b></p>
                                    <p style="margin-bottom:0px"><?php echo number_format($data['volume_po']) . ' Liter ' . $data['produk']; ?></p>
                                    <p style="margin-bottom:0px"><?php echo $data['nama_suplier']; ?></p>
                                </td>
                                <td class="text-left">
                                    <p style="margin-bottom:0px"><b>NO DO Syop : </b></p>
                                    <p style="margin-bottom:5px"><?php echo ($data['no_do_syop'] ? $data['no_do_syop'] : 'N/A'); ?></p>
                                    <p style="margin-bottom:0px"><b>Loading Order : </b></p>
                                    <p style="margin-bottom:0px"><?php echo ($data['nomor_lo_pr'] ? $data['nomor_lo_pr'] : 'N/A'); ?></p>
                                </td>
                                <td class="text-center">
                                    <?php
                                    echo '<div style="margin-bottom:5px;">';
                                    if (!$data['is_cancel'] && !$data['is_delivered'])
                                        echo '<input type="text" name="dt2[' . $idp . ']" id="dt2' . $nom . '" class="' . $class2 . '" value="' . $tgload . '" />';
                                    else echo $tgload;
                                    echo '</div>';

                                    echo '<div style="margin-bottom:5px;">';
                                    if (!$data['is_cancel'] && !$data['is_delivered'])
                                        echo '<input type="text" name="dt3[' . $idp . ']" id="dt3' . $nom . '" class="' . $class3 . '" value="' . $jamLoa . '" />';
                                    else echo $jamLoa;
                                    echo '</div>';
                                    ?></td>
                                <td><?php
                                    if (!$data['is_cancel'] && !$data['is_delivered'])
                                        echo '<input type="text" name="dt5[' . $idp . ']" id="dt5' . $nom . '" class="' . $class4 . '" value="' . $nom_or . '" />';
                                    else echo $nom_or;
                                    ?></td>
                                <td><?php
                                    if ($data['id_wilayah'] == 11 && $data['id_terminal'] == 73) {
                                        if (!$data['is_cancel'] && !$data['is_delivered'])
                                            echo '<input type="text" name="dtsegel[' . $idp . ']" id="dtsegel' . $nom . '" class="' . $class4 . '" value="" />';
                                        echo ($nomor_segel_manual) ? '<p class="text-center" style="margin:5px 0px;">' . $nomor_segel_manual . '</p>' : '';
                                    } else {
                                        if (!$data['is_cancel'] && !$data['is_delivered'])
                                            echo '<input type="text" name="dt6[' . $idp . ']" id="dt6' . $nom . '" class="' . $class5 . '" value="" />';
                                        echo ($nomor_segel) ? '<p class="text-center" style="margin:5px 0px;">' . $nomor_segel . '</p>' : '';
                                        echo  '<p  class="text-center" style="margin-bottom:0px">
                                                <a target="_blank" style="cursor:pointer;color:orange;" title="Cetak Qr" href=' . $linkCtkQr . '> Cetak QR</a>
                                            </p>';
                                    }
                                    ?></td>
                                <td><?php
                                    if (!$data['is_cancel'] && !$data['is_delivered'])
                                        echo '<input type="text" name="dt8[' . $idp . ']" id="dt8' . $nom . '" class="' . $class4 . '" value="' . $data['nama_customer'] . '" />';
                                    else echo '<p class="text-center">' . $data['nama_area'] . '</p>';
                                    ?></td>
                                <td><?php
                                    if (!$data['is_cancel'] && !$data['is_delivered'])
                                        echo '<input type="text" name="dt18[' . $idp . ']" id="dt18' . $nom . '" class="' . $class4 . '" value="' . $remark_to_depo . '" />';
                                    else echo $remark_to_depo;
                                    ?></td>
                                <td>
                                    <p style="margin-bottom:0px"><b><?php echo $data['no_spj']; ?></b></p>
                                    <p style="margin-bottom:0px"><?php echo $data['nomor_plat']; ?></p>
                                    <p style="margin-bottom:0px"><?php echo $data['nama_sopir']; ?></p>
                                    <input type="hidden" name="<?php echo 'ext_id_lcr[' . $idp . ']'; ?>" value="<?php echo $data['id_lcr']; ?>" />
                                    <!-- <input type="hidden" name="<?php //echo 'dt11['.$idp.']';
                                                                    ?>" value="<?php //echo $data['mobil_po'];
                                                                                ?>" /> -->
                                    <p style="margin-bottom:0px"><a style="cursor:pointer" class="detTruck" data-idnya="<?php echo $idp; ?>">Detil Truck</a></p>
                                    <!-- <p style="margin-bottom:0px">
                                        <a style="cursor:pointer" href="<?php echo BASE_URL_CLIENT ?>/_get_form_bpuj.php?<?php echo paramEncrypt('id_cust=' . $id_customer . '&id_dsd=' . $idp) ?>" data-idnya="<?php echo $idp; ?>">BPUJ</a>
                                    </p> -->
                                </td>
                                <td class="text-center">
                                    <?php echo '<input type="hidden" name="dt9[' . $idp . ']" id="dt9' . $nom . '" value="' . $nom_do . '" />' . $nom_do; ?>
                                </td>
                                <td class="text-left">
                                    <?php echo 'Trip : ' . $data['trip_po'] . '<br />Multidrop : ' . $data['multidrop_po']; ?>
                                </td>
                                <td class="text-center">
                                    <?php echo '<p class="text-center">' . $data['nomor_oslog'] . '</p>'; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($data['is_loaded'] == 0 && $data['is_delivered'] == 0 && $data['is_cancel'] == 0) { ?>
                                        <p style="margin-bottom:0px"><b>Belum Loading</b></p>
                                    <?php } elseif ($data['is_loaded'] == 1 && $data['is_delivered'] == 0 && $data['is_cancel'] == 0) { ?>
                                        <p style="margin-bottom:0px"><b>Loading</b></p>
                                        <p style="margin-bottom:0px"><?php echo 'Tgl Loading ' . tgl_indo($data['tanggal_loaded']); ?> </p>
                                        <p style="margin-bottom:0px"><?php echo 'Jam Loading ' . ($data['jam_loaded']); ?> </p>
                                    <?php } elseif ($data['is_loaded'] == 1 && $data['is_delivered'] == 1 && $data['is_cancel'] == 0) { ?>
                                        <p style="margin-bottom:0px"><?php echo 'Tgl Loading ' . tgl_indo($data['tanggal_loaded']); ?> </p>
                                        <p style="margin-bottom:0px"><?php echo 'Jam Loading ' . ($data['jam_loaded']); ?> </p>
                                        <p style="margin-bottom:0px"><b>Delivered</b></p>
                                    <?php } elseif ($data['is_loaded'] == 0 && $data['is_delivered'] == 0 && $data['is_cancel'] == 1) { ?>
                                        <p style="margin-bottom:0px"><b>Cancel</b></p>
                                        <p style="margin-bottom:0px"><?php echo 'Tgl Cancel ' . tgl_indo($data['tanggal_cancel']); ?> </p>
                                    <?php } elseif ($data['is_loaded'] == 1 && $data['is_delivered'] == 0 && $data['is_cancel'] == 1) { ?>
                                        <p style="margin-bottom:0px"><b>Cancel</b></p>
                                        <p style="margin-bottom:0px"><?php echo 'Tgl Cancel ' . tgl_indo($data['tanggal_cancel']); ?> </p>

                                    <?php } ?>
                                </td>
                                </tr>
                        <?php }
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="form-group row">
    <div class="col-sm-6">
        <label>Catatan</label>
        <?php if ($row['is_submitted']) { ?>
            <div class="form-control" style="height:auto"><?php echo $catatan; ?></div>
        <?php } else { ?>
            <textarea name="catatan" id="catatan" class="form-control"></textarea>
        <?php } ?>
    </div>
</div>

<?php if (count($res) > 0) { ?>
    <p>&nbsp;</p>
    <div class="row">
        <div class="col-sm-6">
            <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
            <input type="hidden" name="loco" value="<?php echo $row['is_loco']; ?>" />
            <a class="btn btn-default jarak-kanan" style="width:80px;" href="<?php echo BASE_URL_CLIENT . "/delivery-loading.php"; ?>">Kembali</a>
            <?php if (!$row['is_submitted']) { ?>
                <input type="hidden" name="baru" value="1" />
                <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt1" id="btnSbmt1" value="1" style="width:80px;">Save</button>
            <?php } else { ?>
                <?php if ($id_role != 24) : ?>
                    <input type="hidden" name="baru" value="0" />
                    <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt1" id="btnSbmt1" value="1">Ubah Data</button>
                <?php endif ?>
            <?php } ?>
        </div>
        <div class="col-sm-6 col-sm-top">
            <div class="text-right">
                <?php if ($row['is_submitted']) { ?>
                    <div class="btn-group jarak-kanan">
                        <button type="button" class="btn btn-success">Cetak DN</button>
                        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a target="_blank" href="<?php echo $linkCtk2; ?>">Dengan Inisial</a></li>
                            <li><a target="_blank" href="<?php echo $linkCtk4; ?>">Tanpa Inisial</a></li>
                        </ul>
                    </div>
                    <a class="btn btn-success jarak-kanan" target="_blank" href="<?php echo $linkCtk3; ?>">Cetak BA</a>
                    <a class="btn btn-success jarak-kanan" target="_blank" href="<?php echo $linkCtk1; ?>">Cetak DS</a>
                    <!-- <a class="btn btn-success" target="_blank" href="<?php echo $linkCtk2; ?>">Cetak DN</a>  -->
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>
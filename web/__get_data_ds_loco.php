<div style="overflow-x: scroll" id="table-long">
    <div style="width:1200px; height:auto;">
        <div class="table-responsive-satu">
            <table class="table table-bordered" id="table-grid3">
                <thead>
                    <tr>
                        <th class="text-center" width="50">Aksi</th>
                        <th class="text-center" width="50">No</th>
                        <th class="text-center" width="60">Ref DN</th>
                        <th class="text-center" width="170">Customer</th>
                        <th class="text-center" width="200">Area/ Alamat Kirim/ Wilayah OA</th>
                        <th class="text-center" width="200">PO Customer</th>
                        <th class="text-center" width="225">PO Transportir</th>
                        <th class="text-center" width="225">Keterangan Lain</th>
                        <th class="text-center" width="85">Tgl Jam Loading</th>
                        <th class="text-center" width="100">No Order</th>
                        <th class="text-center" width="130">Segel</th>
                        <th class="text-center" width="130">Nomor DO</th>
                        <th class="text-center" width="130">Status Pengiriman</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select a.*, h.kode_pelanggan, h.nama_customer, g.id_lcr, g.alamat_survey, i.nama_prov, j.nama_kab, q.fullname, l.nama_area, 
                            o.nama_transportir, o.nama_suplier, o.lokasi_suplier, m.nomor_plat, n.nama_sopir, b.volume_po, d.volume, p.wilayah_angkut, d.produk, 
                            b.no_spj, b.mobil_po, b.sopir_po, c.nomor_po, c.id_transportir, d.no_do_syop, d.nomor_lo_pr,  f.nomor_poc, b.tgl_kirim_po
                            from pro_po_ds_detail a 
                            join pro_pr_detail d on a.id_prd = d.id_prd 
                            join pro_po_customer_plan e on a.id_plan = e.id_plan 
                            join pro_po_customer f on e.id_poc = f.id_poc 
                            join pro_customer_lcr g on e.id_lcr = g.id_lcr
                            join pro_customer h on f.id_customer = h.id_customer 
                            join pro_master_provinsi i on g.prov_survey = i.id_prov 
                            join pro_master_kabupaten j on g.kab_survey = j.id_kab
                            join pro_penawaran k on f.id_penawaran = k.id_penawaran  
                            join pro_master_area l on k.id_area = l.id_master 
                            join pro_master_wilayah_angkut p on g.id_wil_oa = p.id_master and g.prov_survey = p.id_prov and g.kab_survey = p.id_kab 
                            join acl_user q on h.id_marketing = q.id_user 
                            left join pro_po_detail b on a.id_pod = b.id_pod 
                            left join pro_po c on a.id_po = c.id_po 
                            left join pro_master_transportir_mobil m on b.mobil_po = m.id_master 
                            left join pro_master_transportir_sopir n on b.sopir_po = n.id_master 
                            left join pro_master_transportir o on c.id_transportir = o.id_master 
                            where a.id_ds = '" . $idr . "' order by a.nomor_urut_ds, a.tanggal_loading, a.jam_loading, a.id_ds";
                    $res = $con->getResult($sql);
                    if (count($res) == 0) {
                        echo '<tr><td colspan="12" style="text-align:center">Data tidak ditemukan </td></tr>';
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
                            $volume = ($data['volume_po']) ? $data['volume_po'] : $data['volume'];
                            $nom_oc = $data['nomor_oc'];
                            $nom_or = $data['nomor_order'];
                            $source = $data['source'];
                            $soldto = $data['sold_to'];
                            $angkut = $data['id_transportir'];
                            $mobil     = $data['mobil_po'];
                            $sopir     = $data['sopir_po'];

                            $class1 = 'form-control input-po text-center';
                            $class2 = 'form-control input-po datepicker';
                            $class3 = 'form-control input-po timepicker';
                            $class4 = 'form-control input-po';
                            $class4 = 'form-control input-po text-right hitung';

                            $kolom1 = "concat(nama_suplier,' - ',nama_transportir,', ',lokasi_suplier)";
                            $where1 = "where is_active = 1 and tipe_angkutan in (1,3)";
                            "where is_active = 1 and id_transportir = '" . $row['id_transportir'] . "'";
                            $where2 = "where is_active = 1 and id_transportir = '" . $angkut . "'";

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
                            <tr<?php echo ($data['is_cancel'] ? ' style="color:#999;"' : ''); ?>>
                                <td class="text-center">

                                    <?php
                                    //if ((!$data['is_cancel'] && !$data['is_delivered']) && !$data['is_loaded'])
                                    //echo '<button type="button" class="btn btn-danger btn-action dRow" value="' . $idp . '"><i class="fa fa-trash"></i></button>';
                                    //else echo '&nbsp;';
                                    ?>

                                </td>
                                <td class="text-center">
                                    <?php echo (!$data['is_cancel'] && !$data['is_delivered'] ? '<input type="hidden" name="ck1[' . $idp . ']" id="ck1' . $nom . '" value="' . $nom . '" />' : '') . $nom; ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    if (!$data['is_cancel'] && !$data['is_delivered'])
                                        echo '<input type="text" name="dt1[' . $idp . ']" id="dt1' . $nom . '" class="' . $class1 . '" value="' . $ref_dn . '" />';
                                    else echo $ref_dn;
                                    ?></td>
                                <td>
                                    <p style="margin-bottom:0px"><b>
                                            <?php echo ($data['kode_pelanggan'] ? $data['kode_pelanggan'] . '<br/>' : '') . $data['nama_customer']; ?></b>
                                    </p>
                                    <p style="margin-bottom:0px"><?php echo number_format($volume) . ' Liter ' . $data['produk']; ?></p>
                                    <p style="margin-bottom:0px"><i><?php echo $data['fullname']; ?></i></p>
                                    <p style="margin-bottom:0px"><a style="cursor:pointer" class="detLcr" data-idnya="<?php echo $data['id_lcr']; ?>">Detil LCR</a></p>
                                </td>
                                <td>
                                    <p style="margin-bottom:0px"><b><?php echo $data['nama_area']; ?></b></p>
                                    <p style="margin-bottom:0px"><?php echo $alamat; ?></p>
                                    <p style="margin-bottom:0px"><?php echo 'Wilayah OA : ' . $data['wilayah_angkut']; ?></p>
                                </td>
                                <td>
                                    <p style="margin-bottom:0px"><b><?php echo $data['nomor_poc']; ?></b></p>
                                    <p style="margin-bottom:0px"><?php echo number_format($data['volume_po']) . ' Liter ' . $data['produk']; ?></p>
                                    <p style="margin-bottom:0px"><?php echo 'Tgl Kirim ' . tgl_indo($data['tgl_kirim_po']); ?></p>
                                </td>
                                <td class="text-left">
                                    <?php
                                    echo '<div style="margin-bottom:5px;">';
                                    if (!$data['is_cancel'] && !$data['is_delivered']) {
                                        echo '<select name="dt10[' . $idp . ']" id="dt10' . $nom . '" class="dt10 ' . $class4 . '"><option></option>';
                                        $con->fill_select("id_master", $kolom1, "pro_master_transportir", $angkut, $where1, "id_master", false);
                                        echo '</select>';
                                    } else echo $data['nama_suplier'] . ' - ' . $data['nama_transportir'] . '<br />' . $data['lokasi_suplier'];
                                    echo '</div>';

                                    echo '<div style="margin-bottom:5px;">';
                                    if (!$data['is_cancel'] && !$data['is_delivered']) {
                                        echo '<select name="dt11[' . $idp . ']" id="dt11' . $nom . '" class="dt11 ' . $class4 . '"><option></option>';
                                        $con->fill_select("id_master", "nomor_plat", "pro_master_transportir_mobil", $mobil, $where2, "", false);
                                        echo '</select>';
                                    } else echo '<input type="hidden" name="dt11[' . $idp . ']" value="' . $mobil . '" />' . $data['nomor_plat'];
                                    echo '</div>';

                                    echo '<div style="margin-bottom:5px;">';
                                    if (!$data['is_cancel'] && !$data['is_delivered']) {
                                        echo '<select name="dt12[' . $idp . ']" id="dt12' . $nom . '" class="dt12 ' . $class4 . '"><option></option>';
                                        $con->fill_select("id_master", "nama_sopir", "pro_master_transportir_sopir", $sopir, $where2, "", false);
                                        echo '</select>';
                                    } else echo $data['nama_sopir'];
                                    echo '</div><p style="margin-bottom:0px;"><a style="cursor:pointer" class="detTruck" data-idnya="' . $idp . '">Detil Truck</a></p>';

                                    echo '<input type="hidden" name="ext_id_lcr[' . $idp . ']" value="' . $data['id_lcr'] . '" />';
                                    echo '<input type="hidden" name="ext_id_po[' . $idp . ']" value="' . $data['id_po'] . '" />';
                                    echo '<input type="hidden" name="ext_id_pod[' . $idp . ']" value="' . $data['id_pod'] . '" />';
                                    echo '<input type="hidden" name="ext_id_pr[' . $idp . ']" value="' . $data['id_pr'] . '" />';
                                    echo '<input type="hidden" name="ext_id_prd[' . $idp . ']" value="' . $data['id_prd'] . '" />';
                                    ?></td>
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
                                    if (!$data['is_cancel'] && !$data['is_delivered'])
                                        echo '<input type="text" name="dt6[' . $idp . ']" id="dt6' . $nom . '" class="' . $class5 . '" value="" />';
                                    echo ($nomor_segel) ? '<p class="text-center" style="margin:5px 0px;">' . $nomor_segel . '</p>' : '';
                                    ?></td>
                                <td class="text-center">
                                    <?php echo '<input type="hidden" name="dt9[' . $idp . ']" id="dt9' . $nom . '" value="' . $nom_do . '" />' . $nom_do; ?>
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
            <input type="hidden" name="tgl" value="<?php echo $row['tanggal_ds']; ?>" />
            <input type="hidden" name="dpt" value="<?php echo $row['id_terminal']; ?>" />
            <input type="hidden" name="nods" value="<?php echo $row['nomor_ds']; ?>" />
            <input type="hidden" name="loco" value="<?php echo $row['is_loco']; ?>" />
            <a class="btn btn-default jarak-kanan" style="width:80px;" href="<?php echo BASE_URL_CLIENT . "/delivery-loading.php"; ?>">Kembali</a>
            <?php if (!$row['is_submitted']) { ?>
                <input type="hidden" name="baru" value="1" />
                <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt1" id="btnSbmt1" value="1" style="width:80px;" <?= $row['tanggal_ds'] < '2023-01-01' ? 'disabled' : '' ?>>Save</button>
            <?php } else { ?>
                <input type="hidden" name="baru" value="0" />
                <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt1" id="btnSbmt1" value="1">Ubah Data</button>
            <?php } ?>
        </div>
        <div class="col-sm-6 col-sm-top">
            <div class="text-right">
                <?php if ($row['is_submitted']) { ?>
                    <!-- <a class="btn btn-success jarak-kanan" target="_blank" href="<?php echo $linkCtk3; ?>">Cetak BA</a> -->
                    <div class="btn-group jarak-kanan">
                        <button type="button" class="btn btn-success">Cetak DN</button>
                        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a target="_blank" href="<?php echo $linkCtk2; ?>">Dengan Inisial.</a></li>
                            <li><a target="_blank" href="<?php echo $linkCtk4; ?>">Tanpa Inisial.</a></li>
                        </ul>
                    </div>
                    <a class="btn btn-success jarak-kanan" target="_blank" href="<?php echo $linkCtk1; ?>">Cetak DS</a>
                    <!-- <a class="btn btn-success" target="_blank" href="<?php echo $linkCtk2; ?>">Cetak DN</a>  -->
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>

<script>
    $(document).ready(function() {
        $(".dt10").select2({
            placeholder: "Transportir",
            allowClear: true
        });
        $(".dt11").select2({
            placeholder: "Truck",
            allowClear: true
        });
        $(".dt12").select2({
            placeholder: "Driver",
            allowClear: true
        });
        $("select.dt10").change(function() {
            var idnya = $(this).attr("id").replace("dt10", "");
            var nilai = $(this).val();
            $("select#dt11" + idnya).val("").trigger('change').select2('close');
            $("select#dt11" + idnya + " option").remove();
            $("select#dt12" + idnya).val("").trigger('change').select2('close');
            $("select#dt12" + idnya + " option").remove();
            $.ajax({
                type: "POST",
                url: "./__get_truk_transportir.php",
                dataType: 'json',
                data: {
                    q1: nilai
                },
                cache: false,
                success: function(data) {
                    if (data.items1 != "") $("select#dt11" + idnya).select2({
                        placeholder: "Truck",
                        allowClear: true,
                        data: data.items1
                    });
                    if (data.items2 != "") $("select#dt12" + idnya).select2({
                        placeholder: "Driver",
                        allowClear: true,
                        data: data.items2
                    });
                    return false;
                }
            });
        });
    });
</script>
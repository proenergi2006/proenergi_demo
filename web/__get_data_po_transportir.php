<div style="overflow-x: scroll" id="table-long">
    <div style="width:2780px; height:auto;">
        <div class="table-responsive-satu">
            <table class="table table-bordered" id="table-grid3">
                <thead>
                    <tr>
                        <th class="text-center" width="70">Aksi</th>
                        <th class="text-center" width="90">Sort</th>
                        <th class="text-center" width="70">No</th>
                        <th class="text-center" width="250">Customer</th>
                        <th class="text-center" width="250">Area/ Alamat Kirim/ Wilayah OA</th>
                        <th class="text-center" width="200">PO Customer</th>
                        <th class="text-center" width="200">Keterangan Lain </th>
                        <th class="text-center" width="90">OA<br />Price List</th>
                        <th class="text-center" width="90">OA<br />Penawaran</th>
                        <th class="text-center" width="90">OA<br />Transportir</th>
                        <th class="text-center" width="180">Plat No.</th>
                        <th class="text-center" width="180">Driver</th>
                        <th class="text-center" width="100">Estimasi Loading</th>
                        <th class="text-center" width="100">Estimasi Tiba Customer</th>
                        <th class="text-center" width="100">No. SPJ</th>
                        <th class="text-center" width="200">Depot</th>
                        <th class="text-center" width="70">Trip</th>
                        <th class="text-center" width="70">Multi Drop</th>
                        <th class="text-center" width="200">Keterangan</th>
                        <th class="text-center" width="180">Catatan Marketing</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select a.*, c.pr_pelanggan, c.produk, c.transport, e.status_jadwal, e.tanggal_kirim, e.tanggal_loading as tgl_loading_plan, f.harga_poc, f.nomor_poc, 
                    g.alamat_survey, g.id_wil_oa, g.jenis_usaha, g.id_lcr, h.nama_prov, i.nama_kab, j.nama_customer, j.id_customer, j.kode_pelanggan, 
                    k.fullname, n.nama_area, o.nama_terminal, o.tanki_terminal, o.lokasi_terminal, p.nama_vendor, q.nomor_plat, r.nama_sopir, 
					s.wilayah_angkut, t.ongkos_angkut, u.is_cancel, u.is_delivered, u.is_loaded, m.detail_rincian, m.oa_kirim, c.no_do_syop, c.nomor_lo_pr
                    from pro_po_detail a
                    join pro_po b on a.id_po = b.id_po
                    join pro_pr_detail c on a.id_prd = c.id_prd 
                    join pro_pr d on c.id_pr = d.id_pr 
                    join pro_po_customer_plan e on c.id_plan = e.id_plan 
                    join pro_po_customer f on e.id_poc = f.id_poc 
                    join pro_customer_lcr g on e.id_lcr = g.id_lcr
                    join pro_master_provinsi h on g.prov_survey = h.id_prov 
                    join pro_master_kabupaten i on g.kab_survey = i.id_kab
                    join pro_customer j on f.id_customer = j.id_customer 
                    join acl_user k on j.id_marketing = k.id_user 
                    join pro_master_cabang l on j.id_wilayah = l.id_master 
                    join pro_penawaran m on f.id_penawaran = m.id_penawaran  
                    join pro_master_area n on m.id_area = n.id_master 
                    left join pro_master_terminal o on a.terminal_po = o.id_master 
                    left join pro_master_vendor p on c.pr_vendor = p.id_master 
                    left join pro_master_transportir_mobil q on a.mobil_po = q.id_master 
                    left join pro_master_transportir_sopir r on a.sopir_po = r.id_master 
					left join pro_master_wilayah_angkut s on g.id_wil_oa = s.id_master and g.prov_survey = s.id_prov and g.kab_survey = s.id_kab 
                    left join (
                        select a.id_transportir, a.id_wil_angkut, a.ongkos_angkut, b.volume_angkut 
                        from pro_master_ongkos_angkut a join pro_master_volume_angkut b on a.id_vol_angkut = b.id_master 
                    ) t on t.id_wil_angkut = g.id_wil_oa and t.volume_angkut = a.volume_po and t.id_transportir = '" . $row['id_transportir'] . "' 
					left join pro_po_ds_detail u on a.id_pod = u.id_pod 
                    where a.id_po = '" . $idr . "' order by a.pod_approved desc, a.no_urut_po";
                    $res = $con->getResult($sql);
                    if (count($res) == 0) {
                        echo '<tr><td colspan="20" style="text-align:center">Data tidak ditemukan </td></tr>';
                    } else {
                        $nom = 0;
                        foreach ($res as $data) {
                            $nom++;
                            $rincian = json_decode($data['detail_rincian'], true);
                            foreach ($rincian as $idx1 => $arr1) {
                                if ($idx1 == 1) {
                                    $data['ongkos_angkut'] = ($arr1['biaya'] ? $arr1['biaya'] : '0');
                                }
                            }

                            $idp = $data['id_pod'];
                            $tempal = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
                            $alamat    = $data['alamat_survey'] . " " . ucwords($tempal) . " " . $data['nama_prov'];
                            $kirim    = date("d/m/Y", strtotime($data['tgl_kirim_po']));
                            $tgl_loading_plan = date("d/m/Y", strtotime($data['tgl_loading_plan']));
                            $ongkos = ($data['ongkos_po']) ? $data['ongkos_po'] : $data['ongkos_angkut'];
                            $tgleta    = (!$data['tgl_eta_po'] or $data['tgl_eta_po'] == '0000-00-00') ? $kirim : date("d/m/Y", strtotime($data['tgl_eta_po']));
                            $tgletl    = (!$data['tgl_eta_po'] or $data['tgl_etl_po'] == '0000-00-00') ? $tgl_loading_plan : date("d/m/Y", strtotime($data['tgl_etl_po']));
                            $jameta = $data['jam_eta_po'];
                            $jametl = $data['jam_etl_po'];
                            $mobil     = $data['mobil_po'];
                            $sopir     = $data['sopir_po'];
                            $sng_po = $data['trip_po'];
                            $dbl_po = $data['multidrop_po'];
                            $ket_po = $data['ket_po'];

                            $class1 = "form-control input-po noFormula text-center";
                            $class2 = "form-control input-po text-right";
                            $class3 = "form-control input-po datepicker";
                            $class4 = "form-control input-po timepicker";
                            $class5 = "form-control input-po";
                            $where1 = "where is_active = 1 and id_transportir = '" . $row['id_transportir'] . "'";

                            $tmn1     = ($data['nama_terminal']) ? $data['nama_terminal'] : '';
                            $tmn2     = ($data['tanki_terminal']) ? '<br />' . $data['tanki_terminal'] : '';
                            $tmn3     = ($data['lokasi_terminal']) ? ', ' . $data['lokasi_terminal'] : '';
                            $depot     = $tmn1 . $tmn2 . $tmn3;

                            $value_ongkos = ($data['ongkos_po_real'] == 0) ? $ongkos : $data['ongkos_po_real'];
                    ?>
                            <tr<?php echo (!$data['pod_approved'] ? ' style="color:#999;"' : ''); ?>>
                                <td class="text-center">
                                    <?php
                                    echo '<input type="hidden" name="ext_id_lcr[' . $idp . ']" value="' . $data['id_lcr'] . '" />';
                                    if ($data['pod_approved'] && $row['disposisi_po'] != 2) {
                                        $disabled1 = ($data['is_cancel'] || $data['is_delivered'] || $data['is_loaded']) ? 'disabled ' : '';
                                        echo '<button type="button" class="' . $disabled1 . 'btn btn-danger btn-action dRow" value="' . $idp . '"><i class="fa fa-trash"></i></button>';
                                    }
                                    ?></td>
                                <td class="text-center">
                                    <?php if ($data['pod_approved'] && !$row['po_approved'] && $row['disposisi_po'] != 2) { ?>
                                        <button type="button" class="btn btn-action btn-default upRow jarak-kanan"><i class="fa fa fa-arrow-up"></i></button>
                                        <button type="button" class="btn btn-action btn-default downRow"><i class="fa fa fa-arrow-down"></i></button>
                                    <?php } else echo '&nbsp;'; ?>
                                </td>
                                <td><?php echo '<input type="text" name="dt1[' . $idp . ']" id="dt1' . $nom . '" class="' . $class1 . '" value="' . $nom . '" readonly />'; ?></td>
                                <td>
                                    <p style="margin-bottom:0px"><b><?php echo ($data['kode_pelanggan'] ? $data['kode_pelanggan'] . '<br/>' : '') . $data['nama_customer']; ?></b></p>
                                    <p style="margin-bottom:0px"><i><?php echo $data['fullname']; ?></i></p>
                                    <p style="margin:5px 0 0;"><a style="cursor:pointer" class="detLcr" data-idnya="<?php echo $data['id_lcr']; ?>">Detil</a></p>
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
                                    <p style="margin-bottom:0px"><b>NO DO Syop : </b></p>
                                    <p style="margin-bottom:5px"><?php echo ($data['no_do_syop'] ? $data['no_do_syop'] : 'N/A'); ?></p>
                                    <p style="margin-bottom:0px"><b>Loading Order : </b></p>
                                    <p style="margin-bottom:0px"><?php echo ($data['nomor_lo_pr'] ? $data['nomor_lo_pr'] : 'N/A'); ?></p>
                                </td>
                                <td class="text-right"><?php echo number_format($data['oa_kirim']); ?></td>
                                <td class="text-right">
                                    <?php
                                    if ($data['pod_approved'] && $row['disposisi_po'] != 2 && !$data['is_cancel'] && !$data['is_delivered']) {
                                        echo '<input type="hidden" name="ext_oa_dr[' . $idp . ']" id="ext_oa_dr' . $nom . '" value="' . $data['transport'] . '" />';
                                        echo '<input type="hidden" name="dt14[' . $idp . ']" id="dt14' . $nom . '" value="' . $data['tgl_kirim_po'] . '" />';
                                        echo '<input type="hidden" name="dt15[' . $idp . ']" id="dt15' . $nom . '" value="' . $data['volume_po'] . '" />';
                                        echo '<input type="hidden" name="dt2[' . $idp . ']" id="dt2' . $nom . '" value="' . $data['oa_flag'] . '" />';
                                        echo '<input type="text" name="dt3[' . $idp . ']" id="dt3' . $nom . '" class="' . $class2 . '" readonly value="' . number_format($ongkos) . '" />';
                                    } else {
                                        echo number_format($data['ongkos_po']);
                                        echo '<input type="hidden" name="dt3[' . $idp . ']" id="dt3' . $nom . '" class="' . $class2 . '" readonly value="' . number_format($data['ongkos_po']) . '" />';
                                    }
                                    ?>
                                </td>
                                <td class="text-right">
                                    <?php if ($data['pod_approved'] && $row['disposisi_po'] != 2 && !$data['is_cancel'] && !$data['is_delivered']) : ?>
                                        <input type="text" name="dt16[<?= $idp ?>]" id="dt16<?= $nom ?>" class="<?= $class2 ?> hitung" value="<?= $value_ongkos ?>" />
                                    <?php else : ?>
                                        <span><?= number_format($data['ongkos_po_real']) ?></span>
                                        <input type="hidden" name="dt16[<?= $idp ?>]" id="dt16<?= $nom ?>" class="<?= $class2 ?> hitung" value="<?= $data['ongkos_po_real'] ?>" />
                                    <?php endif ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    if ($data['pod_approved'] && $row['disposisi_po'] != 2 && !$data['is_cancel'] && !$data['is_delivered']) {
                                        echo '<select name="dt4[' . $idp . ']" id="dt4' . $nom . '" class="input-po form-control select2"><option></option>';
                                        $con->fill_select("id_master", "nomor_plat", "pro_master_transportir_mobil", $mobil, $where1, "", false);
                                        echo '</select>';
                                    } else {
                                        echo '<input type="hidden" name="dt4[' . $idp . ']" value="' . $mobil . '" />' . $data['nomor_plat'];
                                    }
                                    echo '<p style="margin:5px 0 0;"><a style="cursor:pointer" class="detTruck" data-idnya="' . $idp . '">Detil</a></p>';
                                    ?></td>
                                <td class="text-center">
                                    <?php
                                    if ($data['pod_approved'] && $row['disposisi_po'] != 2 && !$data['is_cancel'] && !$data['is_delivered']) {
                                        echo '<select name="dt5[' . $idp . ']" id="dt5' . $nom . '" class="input-po form-control select2"><option></option>';
                                        $con->fill_select("id_master", "nama_sopir", "pro_master_transportir_sopir", $sopir, $where1, "", false);
                                        echo '</select>';
                                    } else {
                                        echo '<input type="hidden" name="dt5[' . $idp . ']" value="' . $sopir . '" />' . $data['nama_sopir'];
                                    }
                                    ?></td>
                                <td class="text-center">
                                    <?php
                                    if ($data['pod_approved'] && !$row['po_approved'] && $row['disposisi_po'] != 2) {
                                        echo '<div style="margin-bottom:5px;"><input type="text" name="dt8[' . $idp . ']" id="dt8' . $nom . '" class="' . $class3 . '" value="' . $tgletl . '" autocomplete="off" /></div>';
                                    } else echo '<p style="margin-bottom:3px;">' . $tgletl . '</p>';

                                    if ($data['pod_approved'] && !$row['po_approved'] && $row['disposisi_po'] != 2) {
                                        echo '<div style="margin-bottom:0px;"><input type="text" name="dt9[' . $idp . ']" id="dt9' . $nom . '" class="' . $class4 . '" value="' . $jametl . '" autocomplete="off" /></div>';
                                    } else echo '<p style="margin-bottom:3px;">' . $jametl . '</p>';
                                    ?></td>
                                <td class="text-center">
                                    <?php
                                    if ($data['pod_approved'] && $row['disposisi_po'] != 2 && !$data['is_cancel'] && !$data['is_delivered']) {
                                        echo '<div style="margin-bottom:5px;"><input type="text" name="dt6[' . $idp . ']" id="dt6' . $nom . '" class="' . $class3 . '" value="' . $tgleta . '" autocomplete="off" /></div>';
                                    } else echo '<p style="margin-bottom:3px;">' . $tgleta . '</p>';

                                    if ($data['pod_approved'] && !$row['po_approved'] && $row['disposisi_po'] != 2) {
                                        echo '<div style="margin-bottom:0px;"><input type="text" name="dt7[' . $idp . ']" id="dt7' . $nom . '" class="' . $class4 . '" value="' . $jameta . '" autocomplete="off" /></div>';
                                    } else echo '<p style="margin-bottom:3px;">' . $jameta . '</p>';
                                    ?></td>
                                <td class="text-center"><?php echo $data['no_spj']; ?></td>
                                <td><?php echo '<input type="hidden" name="dt10[' . $idp . ']" value="' . $data['terminal_po'] . '" />' . $depot; ?></td>
                                <td class="text-center">
                                    <?php
                                    if ($data['pod_approved'] && !$row['po_approved'] && $row['disposisi_po'] != 2) {
                                        echo '<input type="text" name="dt11[' . $idp . ']" id="dt11' . $nom . '" class="' . $class5 . '" value="' . $sng_po . '" autocomplete="off" />';
                                    } else echo $sng_po;
                                    ?></td>
                                <td class="text-center">
                                    <?php
                                    if ($data['pod_approved'] && !$row['po_approved'] && $row['disposisi_po'] != 2) {
                                        echo '<input type="text" name="dt12[' . $idp . ']" id="dt12' . $nom . '" class="' . $class5 . '" value="' . $dbl_po . '" autocomplete="off" />';
                                    } else echo $dbl_po;
                                    ?></td>
                                <td class="text-center">
                                    <?php
                                    if ($data['pod_approved'] && !$row['po_approved'] && $row['disposisi_po'] != 2) {
                                        echo '<input type="text" name="dt13[' . $idp . ']" id="dt13' . $nom . '" class="' . $class5 . '" value="' . $ket_po . '" autocomplete="off" />';
                                    } else echo $ket_po;
                                    ?></td>
                                <td><?php echo $data['status_jadwal']; ?></td>
                                </tr>
                        <?php }
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($row['catatan_transportir'] || $row['disposisi_po'] == 1) { ?>
    <div class="form-group row">
        <div class="col-sm-6">
            <label>Catatan Transportir</label>
            <div class="form-control" style="height:auto"><?php echo $catatan; ?></div>
        </div>
    </div>
<?php } ?>

<?php if (count($res) > 0) { ?>
    <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

    <div class="row">
        <div class="col-sm-6">
            <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
            <input type="hidden" name="transportir" id="transportir" value="<?php echo $row['id_transportir']; ?>" />
            <input type="hidden" name="tombol_klik" id="tombol_klik" value="" />
            <a href="<?php echo BASE_URL_CLIENT . '/purchase-order.php'; ?>" class="btn btn-default jarak-kanan" style="min-width:90px;">Kembali</a>
            <?php if (!$row['po_approved'] && $row['disposisi_po'] != 2) { ?>
                <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt1" id="btnSbmt1" value="1" style="min-width:90px;">Simpan</button>
                <button type="submit" class="btn btn-success jarak-kanan" name="btnSbmt2" id="btnSbmt2" value="1" style="min-width:90px;">Send to Transportir</button>
            <?php }
            if ($row['po_approved']) { ?>
                <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt1" id="btnSbmt3" value="1" style="min-width:90px;">Ubah Data</button>
            <?php } ?>
        </div>
        <div class="col-sm-6 col-sm-top">
            <div class="text-right">
                <?php
                if (($row['po_approved'] && !$row['ada_selisih']) || ($row['po_approved'] && $row['ada_selisih'] && $row['f_proses_selisih'])) {
                    echo ' <div class="btn-group jarak-kanan">
                    <button type="button" class="btn btn-success">Cetak PO</button>
                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a target="_blank" href="' . $linkCetak1 . '">Dengan PPN</a></li>
                        <li><a target="_blank"  href="' . $linkCetak3 . '">Tanpa PPN</a></li>
                    </ul>
                </div>';

                    echo '<a class="btn btn-success jarak-kanan" target="_blank" title="Cetak" href="' . $linkCetak2 . '" style="min-width:90px;">Cetak SPJ</a> ';
                } else if (($row['po_approved'] && $row['ada_selisih'] && !$row['f_proses_selisih'])) {
                    echo '<p style="margin-bottom:0px; font-size:12px;"><i>Cetak PO dan SPJ menunggu proses selisih OA</i></p>';
                } else echo '&nbsp;';
                ?>
            </div>
        </div>
    </div>
<?php } ?>
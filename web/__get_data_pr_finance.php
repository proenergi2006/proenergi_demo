<div style="overflow-x: auto" id="table-long">
    <div style="width:2080px; height:auto;">
        <div class="table-responsive-satu">
            <table class="table table-bordered" id="table-grid3">
                <thead>
                    <tr>
                        <th class="text-center" rowspan="2" width="50">No</th>
                        <th class="text-center" rowspan="2" width="180">Customer</th>
                        <th class="text-center" rowspan="2" width="200">Area/ Alamat Kirim/ Wilayah OA</th>
                        <th class="text-center" rowspan="2" width="150">PO Customer</th>
                        <th class="text-center" rowspan="2" width="150">No DO SYOP</th>
                        <th class="text-center" rowspan="2" width="150">No PO Supplier</th>
                        <th class="text-center" rowspan="2" width="100">Volume</th>
                        <th class="text-center" rowspan="2" width="100">Volume Potongan</th>
                        <th class="text-center" rowspan="2" width="150">Depot</th>
                        <th class="text-center" rowspan="2" width="100">TOP</th>
                        <th class="text-center" rowspan="2" width="120">Credit Limit</th>
                        <th class="text-center" colspan="4">Harga (Rp/Liter)</th>
                        <th class="text-center" rowspan="2" width="150">Loading Order</th>
                        <th class="text-center" rowspan="2" width="150">Status</th>
                    </tr>
                    <tr>
                        <th class="text-center" width="90">Harga</th>
                        <th class="text-center" width="200">Rincian Harga</th>
                        <th class="text-center" width="80">Refund</th>
                        <th class="text-center" width="80">Other Cost</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select a.*, b.finance_result, b.revert_ceo, b.revert_cfo, b.revert_cfo_summary, b.revert_ceo_summary, c.tanggal_kirim, c.volume_kirim, c.top_plan, 
							c.pelanggan_plan, c.ar_notyet, c.ar_satu, c.ar_dua, c.kredit_limit, 
							e.alamat_survey, e.id_wil_oa, f.nama_prov, g.nama_kab,
							h.nama_customer, h.top_payment, h.id_customer, h.jenis_payment, h.kode_pelanggan, h.tipe_bisnis, h.tipe_bisnis_lain,
							i.fullname, l.nama_area, d.harga_poc, o.wilayah_angkut, c.actual_top_plan, p.nilai_pbbkb, m.merk_dagang, k.flag_approval, 
							d.lampiran_poc, d.nomor_poc, d.lampiran_poc_ori, d.id_poc, i.id_role as ext_id_role, i.id_om as ext_id_om, h.credit_limit, 
							k.refund_tawar, k.other_cost, k.perhitungan, k.detail_rincian, k.harga_dasar, k.gabung_oa, k.id_penawaran,
                            q.pr_harga_beli as harga_potong,

                            q.volume as volume_potong, q.nomor_po_supplier as nomor_potong,
						    pt.nama_terminal AS terminal_potong,pt.tanki_terminal AS tanki_potong, pt.lokasi_terminal AS lokasi_potong,
                            r.nama_terminal, r.tanki_terminal, r.lokasi_terminal,
                            s.is_loaded, s.is_delivered, s.is_cancel, s.tanggal_loaded, s.jam_loaded, s.tanggal_cancel
                            from pro_pr_detail a 
							join pro_pr b on a.id_pr = b.id_pr 
							join pro_po_customer_plan c on a.id_plan = c.id_plan 
							join pro_po_customer d on c.id_poc = d.id_poc 
							join pro_customer_lcr e on c.id_lcr = e.id_lcr
							join pro_master_provinsi f on e.prov_survey = f.id_prov 
							join pro_master_kabupaten g on e.kab_survey = g.id_kab
							join pro_customer h on d.id_customer = h.id_customer 
							join acl_user i on h.id_marketing = i.id_user 
							join pro_master_cabang j on h.id_wilayah = j.id_master 
							join pro_penawaran k on d.id_penawaran = k.id_penawaran  
							join pro_master_area l on k.id_area = l.id_master 
							join pro_master_produk m on d.produk_poc = m.id_master 
							left join pro_master_wilayah_angkut o on e.id_wil_oa = o.id_master and e.prov_survey = o.id_prov and e.kab_survey = o.id_kab 
							join pro_master_pbbkb p on k.pbbkb_tawar = p.id_master 
                            LEFT JOIN new_pro_inventory_potongan_stock q ON a.id_prd = q.id_prd
						    LEFT JOIN pro_master_terminal pt ON pt.id_master = q.pr_terminal
                            left join pro_master_terminal r on a.pr_terminal = r.id_master 
                            left join pro_po_ds_detail s on a.id_prd = s.id_prd 
                            where a.id_pr = '" . $idr . "' order by a.is_approved desc, c.tanggal_kirim, k.id_cabang, k.id_area, a.id_plan, a.id_prd";
                    $res = $con->getResult($sql);
                    $fnr = $res ? $res[0]['finance_result'] : null;

                    if (count($res) == 0) {
                        echo '<tr><td colspan="12" style="text-align:center">Data tidak ditemukan </td></tr>';
                    } else {
                        $nom = 0;
                        foreach ($res as $data) {
                            $arrTipeBisnis         = array(1 => "Agriculture & Forestry / Horticulture", "Business & Information", "Construction/Utilities/Contracting", "Education", "Finance & Insurance", "Food & hospitally", "Gaming", "Health Services", "Motor Vehicle", $data['tipe_bisnis_lain'], "Natural Resources / Environmental", "Personal Service", "Manufacture");

                            $id_poc_sc[] = $data['id_poc'];

                            $nom++;
                            $idk     = $data['id_prd'];
                            $linkCtk1     = ACTION_CLIENT . "/delivery-order-detail-cetak.php?" . paramEncrypt("idp=" . $idk);
                            $tempal = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
                            $alamat    = $data['alamat_survey'] . " " . ucwords($tempal) . " " . $data['nama_prov'];
                            $kirim    = tgl_indo($data['tanggal_kirim']);
                            $dt2     = ($data['pelanggan_plan']) ? $data['pelanggan_plan'] : $data['kode_pelanggan'];
                            $dt3     = ($data['ar_notyet']) ? number_format($data['ar_notyet']) : '';
                            $dt4     = ($data['ar_satu']) ? number_format($data['ar_satu']) : '';
                            $dt5     = ($data['ar_dua']) ? number_format($data['ar_dua']) : '';
                            $dt6     = ($data['kredit_limit']) ? number_format($data['kredit_limit']) : number_format($data['credit_limit']);
                            $dt7     = ($data['pr_actual_top']) ? $data['pr_actual_top'] : '';
                            $no_do_syop = ($data['no_do_syop']) ? $data['no_do_syop'] : '';

                            $pbbkbT = ($data['nilai_pbbkb'] / 100) + 1.11;
                            $oildus = $data['harga_poc'] / $pbbkbT * 0.003;
                            $pbbkbN = $data['harga_poc'] / $pbbkbT * ($data['nilai_pbbkb'] / 100);
                            $tmphrg = $data['refund_tawar'] + $oildus + $data['transport'] + $pbbkbN + $data['other_cost'];
                            $nethrg = $data['harga_poc'] - $tmphrg;

                            $pathPt = $public_base_directory . '/files/uploaded_user/lampiran/' . $data['lampiran_poc'];
                            $lampPt = $data['lampiran_poc_ori'];

                            $link_cetak = ACTION_CLIENT . '/penawaran-cetak.php?' . paramEncrypt('idr=' . $data['id_customer'] . '&idk=' . $data['id_penawaran'] . '&bhs=ind');

                            if ($data['lampiran_poc'] && file_exists($pathPt) && $data['flag_approval'] == 1) {
                                $linkPt = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=2&ktg=POC_" . $data['id_poc'] . "_&file=" . $lampPt);
                                $attach = '
								<a href="' . $linkPt . '" target ="_blank"><i class="fa fa-file-alt" title="' . $lampPt . '"></i> PO Customer</a><br />
								<a href="' . $link_cetak . '" target ="_blank"><i class="fa fa-file-alt" title="' . $lampPt . '"></i> Penawaran' . '</a><br />
								<a href="#" class="history_approve" attr_idc="' . $data['id_customer'] . '" attr_idk="' . $data['id_penawaran'] . '">
									<i class="fa fa-cog" title="History Approval"></i> History Approve
								</a>';
                            } else {
                                $attach = '
                                <a href="' . $link_cetak . '" target ="_blank"><i class="fa fa-file-alt" title="' . $lampPt . '"></i> Penawaran' . '</a><br />
                                <a href="#" class="history_approve" attr_idc="' . $data['id_customer'] . '" attr_idk="' . $data['id_penawaran'] . '">
                                	<i class="fa fa-cog" title="History Approval"></i> History Approve
                                </a>';
                            }

                            $jns_payment = $data['jenis_payment'];
                            $top_payment = $data['top_payment'];
                            $arr_payment = array("CREDIT" => "NET " . $top_payment, "COD" => "COD", "CBD" => "CBD");
                            $termPayment = $arr_payment[$jns_payment];
                            $topCustomer = ($data['top_plan']) ? $data['top_plan'] : $termPayment;
                            $dt1Customer = 'value="' . $topCustomer . '" readonly';
                            $dt2Customer = 'value="' . $dt2 . '"' . ($dt2 ? ' readonly' : '');


                            $rincian = json_decode($data['detail_rincian'], true);
                            $tabel_harga = '<table border="0" cellpadding="" cellspacing="0" width="200">';
                            foreach ($rincian as $arr1) {
                                $cetak = 1;
                                $nilai = $arr1['nilai'];
                                $biaya = ($arr1['biaya']) ? $arr1['biaya'] : '';
                                $biaya = ($rsm['pembulatan']) ? number_format($arr1['biaya']) : number_format($arr1['biaya'], 2);
                                $jenis = $arr1['rincian'];
                                $tabel_harga .= '
								<tr>
									<td align="left" witdh="110">' . $jenis . ($nilai ? " " . $nilai . "%" : "") . '</td>
									<td align="right">' . $biaya . '</td>
								</tr>';
                            }
                            $tabel_harga .= '
							<tr>
								<td align="left" colspan="2">' . ($data['gabung_oa'] ? '<p style="margin:5px 0px 0px;"><i>* Harga Dasar Inc. OA</i></p>' : '') . '</td>
							</tr>';
                            $tabel_harga .= '</table>';

                            if (!$fnr) {
                    ?>
                                <tr>
                                    <td class="text-center"><?php echo $nom; ?></td>
                                    <td>
                                        <p style="margin-bottom:0px"><?php echo ($data['kode_pelanggan'] ? $data['kode_pelanggan'] : '------'); ?></p>
                                        <p style="margin-bottom:0px"><?php echo $data['nama_customer']; ?></b></p>
                                        <p style="margin-bottom:0px"><i><?php echo $data['fullname']; ?></i></p>
                                    </td>
                                    <td>
                                        <p style="margin-bottom:0px"><b><?php echo $data['nama_area']; ?></b></p>
                                        <p style="margin-bottom:0px"><?php echo $alamat; ?></p>
                                        <p style="margin-bottom:0px"><?php echo 'Wilayah OA : ' . $data['wilayah_angkut']; ?></p>
                                        <input type="hidden" name="<?php echo "idc[" . $idk . "]"; ?>" value="<?php echo $data['id_customer']; ?>" />
                                        <input type="hidden" name="<?php echo "idg[" . $idk . "]"; ?>" value="<?php echo $data['id_group_cabang']; ?>" />
                                        <input type="hidden" name="<?php echo "ext_id_role[" . $idk . "]"; ?>" value="<?php echo $data['ext_id_role']; ?>" />
                                        <input type="hidden" name="<?php echo "ext_id_om[" . $idk . "]"; ?>" value="<?php echo $data['ext_id_om']; ?>" />
                                    </td>
                                    <td>
                                        <p style="margin-bottom:0px"><b><?php echo $data['nomor_poc']; ?></b></p>
                                        <p style="margin-bottom:0px"><?php echo $data['merk_dagang']; ?></p>
                                        <p style="margin-bottom:0px"><?php echo $kirim; ?></p>
                                        <p style="margin-bottom:0px"><?php echo number_format($data['volume']) . " Liter"; ?></p>
                                        <p style="margin-bottom:0px"><?php echo $attach; ?></p>
                                    </td>
                                    <td>
                                        <p style="margin-bottom:0px"><?php echo $data['no_do_syop']; ?></p>
                                    </td>
                                    <td><input type="text" name="<?php echo "dt1[" . $idk . "]"; ?>" id="<?php echo "dt1" . $nom; ?>" class="form-control input-po" <?php echo $dt1Customer; ?> /></td>
                                    <!-- <td><input type="text" name="<?php echo "dt7[" . $idk . "]"; ?>" id="<?php echo "dt7" . $nom; ?>" class="form-control i-po cps" value="<?php echo $dt7; ?>" /></td>
                        <td><input type="text" name="<?php echo "dt3[" . $idk . "]"; ?>" id="<?php echo "dt3" . $nom; ?>" class="form-control h-po cps" value="<?php echo $dt3; ?>" /></td>
                        <td><input type="text" name="<?php echo "dt4[" . $idk . "]"; ?>" id="<?php echo "dt4" . $nom; ?>" class="form-control h-po cps" value="<?php echo $dt4; ?>" /></td>
                        <td><input type="text" name="<?php echo "dt5[" . $idk . "]"; ?>" id="<?php echo "dt5" . $nom; ?>" class="form-control h-po cps" value="<?php echo $dt5; ?>" /></td> -->
                                    <td><input type="text" name="<?php echo "dt6[" . $idk . "]"; ?>" id="<?php echo "dt6" . $nom; ?>" class="form-control h-po cps" value="<?php echo $dt6; ?>" readonly="" /></td>
                                    <td class="text-right"><?php echo number_format($data['harga_dasar']); ?></td>
                                    <td class="text-left"><?php echo $tabel_harga; ?></td>
                                    <td class="text-right"><?php echo number_format($data['refund_tawar']); ?></td>
                                    <td class="text-right"><?php echo number_format($data['other_cost']); ?></td>
                                    <td><?php echo $data['nomor_lo_pr']; ?></td>
                                </tr>
                            <?php
                            } else if ($fnr) {
                                $dt1 = $data['pr_top'];
                                $dt2 = $data['pr_pelanggan'];
                                $dt7 = $data['pr_actual_top'];
                                $dt3 = ($data['pr_ar_notyet']) ? number_format($data['pr_ar_notyet']) : '';
                                $dt4 = ($data['pr_ar_satu']) ? number_format($data['pr_ar_satu']) : '';
                                $dt5 = ($data['pr_ar_dua']) ? number_format($data['pr_ar_dua']) : '';
                                $dt6 = ($data['pr_kredit_limit']) ? number_format($data['pr_kredit_limit']) : number_format($data['credit_limit']);
                                $no_do_syop = $data['no_do_syop'];

                            ?>
                                <tr>
                                    <td class="text-center"><?php echo $nom; ?></td>
                                    <td>
                                        <p style="margin-bottom:0px"><b><?php echo $data['kode_pelanggan'] . ' - ' . $data['nama_customer']; ?></b></p>
                                        <p style="margin-bottom:0px"><i><?php echo $data['fullname']; ?></i></p>
                                        <p>
                                            <?php
                                            echo '<a target="_blank" href="' . $linkCtk1 . '" class="btn btn-primary btn-sm ' . ($fnr == '1' ? '' : 'hide') . '">Cetak DO</a>';
                                            ?>
                                        </p>
                                    </td>
                                    <td>
                                        <p style="margin-bottom:0px"><b><?php echo $data['nama_area']; ?></b></p>
                                        <p style="margin-bottom:0px"><?php echo $alamat; ?></p>
                                        <p style="margin-bottom:0px"><?php echo 'Wilayah OA : ' . $data['wilayah_angkut']; ?></p>
                                    </td>
                                    <td>
                                        <p style="margin-bottom:0px"><b><?php echo $data['nomor_poc']; ?></b></p>
                                        <p style="margin-bottom:0px"><?php echo $data['merk_dagang']; ?></p>
                                        <p style="margin-bottom:0px"><?php echo $kirim; ?></p>
                                        <p style="margin-bottom:0px"><?php echo number_format($data['volume']) . " Liter"; ?></p>
                                        <p style="margin-bottom:0px"><?php echo $attach; ?></p>
                                    </td>
                                    <td><?php echo $data['no_do_syop']; ?></td>
                                    <td>
                                        <p style="margin-bottom:0px"><?php echo $data['nomor_po_supplier']; ?></p>
                                        <p style="margin-bottom:0px"><?php echo $data['nomor_potong']; ?></p>
                                    </td>

                                    <td>
                                        <p style="margin-bottom:0px"><?php echo number_format($data['volume']); ?></p>

                                    </td>
                                    <td>
                                        <p style="margin-bottom:0px"><?php echo number_format($data['vol_potongan']); ?></p>
                                        <p style="margin-bottom:0px"><?php echo number_format($data['volume_potong']) && $data['volume_potong'] != '' ? number_format($data['volume_potong']) : ''; ?></p>
                                    </td>
                                    <td>
                                        <?php
                                        $tmn1 = ($data['nama_terminal']) ? $data['nama_terminal'] : '';
                                        $tmn2 = ($data['tanki_terminal']) ? ' - ' . $data['tanki_terminal'] : '';
                                        $tmn3 = ($data['lokasi_terminal']) ? ', ' . $data['lokasi_terminal'] : '';
                                        $tmn4 = ($data['terminal_potong']) ? $data['terminal_potong'] : '';
                                        $tmn5 = ($data['tanki_potong']) ? ' - ' . $data['tanki_potong'] : '';
                                        $tmn6 = ($data['lokasi_potong']) ? ', ' . $data['lokasi_potong'] : '';
                                        ?>
                                        <p style="margin-bottom:0px"><?php echo $tmn1 . $tmn2 . $tmn3 ?></p>
                                        <p style="margin-bottom:0px"><?php echo $tmn4 . $tmn5 . $tmn6 ?></p>
                                    </td>


                                    <td><?php echo $dt1; ?></td>
                                    <td class="text-right"><?php echo $dt6; ?></td>
                                    <td class="text-right"><?php echo number_format($data['harga_dasar']); ?></td>
                                    <td class="text-left"><?php echo $tabel_harga; ?></td>
                                    <td class="text-right"><?php echo number_format($data['refund_tawar']); ?></td>
                                    <td class="text-right"><?php echo number_format($data['other_cost']); ?></td>
                                    <td><?php echo $data['nomor_lo_pr']; ?></td>

                                    <td class="text-center">

                                        <?php if ($data['is_loaded'] == 1 && $data['is_delivered'] == 1 && $data['is_cancel'] == 0) { ?>

                                            <p style="margin-bottom:0px;color:green"><b>Delivered</b></p>
                                        <?php } elseif ($data['is_loaded'] == 1 && $data['is_delivered'] == 0 && $data['is_cancel'] == 1) { ?>
                                            <p style="margin-bottom:0px;color:red"><b>Cancel</b></p>
                                            <p style="margin-bottom:0px"><?php echo 'Tgl Cancel ' . tgl_indo($data['tanggal_cancel']); ?> </p>
                                        <?php } elseif ($data['is_loaded'] == 0 && $data['is_delivered'] == 0 && $data['is_cancel'] == 1) { ?>
                                            <p style="margin-bottom:0px;color:red"><b>Cancel</b></p>
                                            <p style="margin-bottom:0px"><?php echo 'Tgl Cancel ' . tgl_indo($data['tanggal_cancel']); ?> </p>
                                        <?php } ?>
                                    </td>
                                </tr>
                    <?php }
                        }
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <?php if ($res) { ?>
        <?php if ($res[0]['revert_cfo']) { ?>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Catatan Pengembalian CFO</label>
                    <div class="form-control" style="height:auto"><?php echo ($res[0]['revert_cfo_summary']); ?></div>
                </div>
            </div>
        <?php }
        if ($res[0]['revert_ceo']) { ?>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Catatan Pengembalian CEO</label>
                    <div class="form-control" style="height:auto"><?php echo ($res[0]['revert_ceo_summary']); ?></div>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
</div>

<?php if (count($res) > 0) { ?>
    <hr style="margin:0 0 10px" />
    <div class="row">
        <div class="col-sm-12">
            <div class="pad bg-gray">
                <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT . "/purchase-request.php"; ?>">Kembali</a>
                <?php if (!$fnr) { ?><button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Submit</button>
                    <button type="submit" class="btn btn-success backlog" name="backlog" id="backlog"><i class="fa fa-floppy-o jarak-kanan"></i>Kembalikan Ke DP Logistik</button>
                    <input type="hidden" name="backlog" value="0" />
                    <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                <?php } ?>
                <?php $linkCtk1 = ACTION_CLIENT . "/purchase-request-detail-cetak.php?" . paramEncrypt("idr=" . $idr); ?>


                <a class="btn btn-primary" target="_blank" href="<?php echo $linkCtk1; ?>">Cetak</a>
            </div>
        </div>
    </div>
<?php } ?>

<style type="text/css">
    .input-po,
    .h-po,
    .i-po {
        padding: 5px;
        height: auto;
        font-size: 11px;
        font-family: arial;
    }

    .h-po {
        text-align: right;
    }
</style>
<script>
    $(document).ready(function() {

        $(".h-po").number(true, 0, ".", ",");
        // Tombol "Kembalikan ke Logistik"
        $("form#gform").on("click", "#backlog", function() {
            if (confirm("Apakah anda yakin?")) {
                $("#loading_modal").modal({
                    backdrop: "static"
                });
                $('input[name="backlog"]').val(1);
                // Menghapus validasi untuk kolom no_do_accurate
                $("form#gform").validationEngine('detach');
                $("form#gform").submit();
            } else {
                return false;
            }
        });

        // Tombol "Simpan" diaktifkan hanya jika no_do_accurate telah diisi
        $("form#gform").validationEngine('attach', {
            onValidationComplete: function(form, status) {
                var submitButton = form.find('[type="submit"]');

                if (status && submitButton.attr('name') === 'btnSbmt') {
                    var noDoAccurateInputs = $(".no_do_acurate");

                    for (var i = 0; i < noDoAccurateInputs.length; i++) {
                        var input = noDoAccurateInputs[i];

                        if ($(input).val().trim() === "") {
                            alert("No Do Accurate harus diisi.");
                            return false;
                        }
                    }
                }

                if (status) {
                    if (confirm("Apakah anda yakin?")) {
                        $('#loading_modal').modal({
                            backdrop: "static"
                        });
                        form.validationEngine('detach');
                        form.submit();
                    } else {
                        return false;
                    }
                }
            }
        });

        $(".cpsKolom").on("input", function(e) {
            var data = $(this).val();
            var rows = data.split("\n");
            var elem = $(this).parent().next().find("input.cps").first();
            for (var y in rows) {
                if (rows[y] != "") {
                    var cells = rows[y].split("\t");
                    for (var x in cells) {
                        elem.val(cells[x]);
                        elem = elem.parent().next().find("input.cps").first();
                    }
                }
            }
            $(this).val("");
        });



        $('.posisi_edit').click(function() {
            if ($(this).attr('attr_edit') == '0') {
                $(this).closest('.input-group').find('.input-sm').attr('readonly', false);
                $(this).attr('attr_edit', '1');
            } else {
                $(this).closest('.input-group').find('.input-sm').attr('readonly', true);
                $(this).attr('attr_edit', '0');
                var idpr = $(this).closest('.input-group').find('.input-sm').attr('attr_idpr');
                var nilai = $(this).closest('.input-group').find('.input-sm').val();
                $.ajax({
                    type: "POST",
                    url: './__save_no_dr_accurate.php',
                    dataType: "json",
                    data: {
                        'id_pr': idpr,
                        'nilai': nilai,
                        'set_no_do_accurate': '1'
                    },
                    error: function(err, xhr, status) {
                        alert(xhr);
                    },
                    success: function(data) {
                        alert('No Do Accurate Berhasil Disimpan!');
                    }
                });
            }
        });
    });
</script>
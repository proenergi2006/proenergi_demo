<div style="overflow-y:scroll; overflow:scroll;" id="table-long">
    <div style="width:1320px; height:200px;">
        <div class="table-responsive-satu">
            <table class="table table-bordered table-grid3" id="table-grid3">
                <thead>
                    <tr>

                        <th class="text-center" rowspan="2" width="10">No</th>
                        <th class="text-center" rowspan="2" width="100">Customer</th>
                        <th class="text-center" rowspan="2" width="250">Alamat</th>
                        <th class="text-center" rowspan="2" width="100">PO Customer</th>
                        <th class="text-center" rowspan="2" width="20">Volume DO</th>
                        <th class="text-center" rowspan="2" width="20">Realisasi</th>
                        <th class="text-center" rowspan="2" width="20">Jumlah Losses</th>
                        <th class="text-center" rowspan="2" width="30">Persen (%)</th>
                        <th class="text-center" rowspan="2" width="20">Volume</th>
                        <th class="text-center" rowspan="2" width="100" style="color: white; background-color: green;">Losses Ditanggung Customer</th>
                        <th class="text-center" rowspan="2" width="20">Harga</th>
                        <th class="text-center" rowspan="2" width="100" style="color: white; background-color: red;">Losses Ditanggung PE</th>
                        <th class="text-center" rowspan="2" width="100" style="color: white; background-color: red;">Tanggungan PE</th>
                        <!-- <th class="text-center" rowspan="2" width="20">Batas Toleransi Transportir (Liter)</th>
                        <th class="text-center" rowspan="2" width="20">Toleransi Transportir</th>
                        <th class="text-center" rowspan="2" width="100">Harga Losses Transportir (Rp)</th>
                        <th class="text-center" rowspan="2" width="100">Losses Real Transportir (Liter)</th>
                        <th class="text-center" rowspan="2" width="100">Harga Losses Real Transportir (Rp)</th> -->
                        \
                        <th class="text-center" rowspan="2" width="150">Transportir</th>
                        <th class="text-center" rowspan="2" width="150">Lampiran Losses</th>
                        <th class="text-center" rowspan="2" width="150">Catatan Losses</th>
                        <th class="text-center" rowspan="2" width="250">Catatan</th>

                    </tr>
                    <tr>
                        <th style="display:none"></th>
                        <th style="display:none"></th>
                        <th style="display:none"></th>
                        <th style="display:none"></th>
                        <th style="display:none"></th>
                        <th style="display:none"></th>
                        <th style="display:none"></th>
                        <th style="display:none"></th>
                        <th style="display:none"></th>
                        <th style="display:none"></th>
                        <th style="display:none"></th>
                        <th style="display:none"></th>
                        <th style="display:none"></th>
                        <th style="display:none"></th>
                        <th style="display:none"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "
					select a.*,
                    f.nomor_pr,
                    f.tanggal_pr,
                    i.nama_customer,
                    h.nomor_poc,
                    h.harga_poc,
                    e.volume,
                    d.nomor_po,
                    j.nama_cabang,
                    i.kode_pelanggan, l.jenis_usaha, k.fullname,n.nama_area, l.alamat_survey,o.nama_prov, p.nama_kab,  r.wilayah_angkut, m.tol_susut, h.harga_poc,
                    s.merk_dagang, g.tanggal_kirim, e.vol_ori_pr, e.volume, e.nomor_po_supplier, e.vol_potongan, m.harga_dasar,m.refund_tawar,m.other_cost,e.pr_harga_beli, e.splitted_from_pr, i.jenis_payment, i.top_payment,
                    g.top_plan, i.credit_limit, o1.harga_normal, h.harga_poc, m.detail_rincian, g.status_plan, g.catatan_reschedule,g.status_jadwal,
                    e.no_do_syop, e.nomor_lo_pr, f.purchasing_summary,s.id_master, u.nama_terminal, u.tanki_terminal, u.lokasi_terminal, v.volume as volume_potong, v.nomor_po_supplier as nomor_potong, v.pr_harga_beli as harga_potong,
                    pt.nama_terminal AS terminal_potong,pt.tanki_terminal AS tanki_potong, pt.lokasi_terminal AS lokasi_potong, z.nama_suplier, c.no_spj, x.nama_sopir, w.nomor_plat, s.id_master, e.pr_vendor, e.id_po_supplier, e.id_po_receive, c.id_pod

                    from pro_po_ds_detail a 
                    join pro_po_ds b on a.id_ds = b.id_ds 
                    join pro_po_detail c on a.id_pod = c.id_pod 
                    join pro_po d on a.id_po = d.id_po
                    join pro_pr_detail e on a.id_prd = e.id_prd
                    join pro_pr f on a.id_pr = f.id_pr
                    join pro_po_customer_plan g on a.id_plan = g.id_plan 
                    join pro_po_customer h on g.id_poc =  h.id_poc
                    join pro_customer i on h.id_customer = i.id_customer
                    join pro_master_cabang j on b.id_wilayah = j.id_master
                    join acl_user k on i.id_marketing = k.id_user 
                    join pro_customer_lcr l on g.id_lcr = l.id_lcr 
                    join pro_penawaran m on h.id_penawaran = m.id_penawaran 
                    join pro_master_area n on m.id_area = n.id_master 
                    join pro_master_provinsi o on l.prov_survey = o.id_prov 
                    join pro_master_kabupaten p on l.kab_survey = p.id_kab 
                    join pro_master_produk s on h.produk_poc = s.id_master 
                    join pro_master_pbbkb t on m.pbbkb_tawar = t.id_master 
                    join pro_master_terminal u on e.pr_terminal = u.id_master 
                    join pro_master_transportir_mobil w on c.mobil_po = w.id_master 
			        join pro_master_transportir_sopir x on c.sopir_po = x.id_master
                    join pro_master_transportir z on d.id_transportir = z.id_master 
                    left join pro_master_wilayah_angkut r on l.id_wil_oa = r.id_master 
                    left join new_pro_inventory_potongan_stock v ON e.id_prd = v.id_prd
                    left join pro_master_harga_minyak o1 on m.masa_awal = o1.periode_awal and m.masa_akhir = o1.periode_akhir and m.id_area = o1.id_area 
                        and m.pbbkb_tawar = o1.pajak and o1.is_approved = 1 
                        
                    LEFT JOIN pro_master_terminal pt ON pt.id_master = v.pr_terminal
							and l.prov_survey = r.id_prov 
							and l.kab_survey = r.id_kab 
                

						where 
							a.id_dsd= '" . $idr . "' 
						order by a.is_request desc";

                    $res = $con->getResult($sql);


                    if (count($res) == 0) {
                        echo '<tr><td colspan="19" style="text-align: center">Data tidak ditemukan </td></tr>';
                    } else {
                        $nom = 0;
                        $total1 = 0;
                        $total2 = 0;
                        $total3 = 0;
                        $total4 = 0;

                        foreach ($res as $data) {
                            $id_poc_sc[] = $data['id_poc'];
                            $nom++;
                            $idp     = $data['id_prd'];
                            $idsp     = $data['splitted_from_pr'];
                            $linkCtk1     = ACTION_CLIENT . "/delivery-order-detail-cetak.php?" . paramEncrypt("idp=" . $idp);
                            $tempal = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
                            $alamat    = $data['alamat_survey'] . " " . ucwords($tempal) . " " . $data['nama_prov'];

                            $jns_payment = $data['jenis_payment'];
                            $top_payment = $data['top_payment'];
                            $arr_payment = array("CREDIT" => "NET " . $top_payment, "COD" => "COD", "CDB" => "CBD");
                            $termPayment = $arr_payment[$jns_payment];
                            $topCustomer = ($data['top_plan']) ? $data['top_plan'] : $termPayment;
                            $dt1Customer = $topCustomer;
                            $credit_limit = $data['credit_limit'];
                            $id_pro = $data['id_master'];

                            $data['harga_normal'] = ($data['harga_normal'] ? $data['harga_normal'] : $data['harga_normal_new']);
                            $pbbkbT = ($data['nilai_pbbkb'] / 100) + 1.11;
                            $oildus = $data['harga_poc'] / $pbbkbT * 0.003;
                            $pbbkbN = $data['harga_poc'] / $pbbkbT * ($data['nilai_pbbkb'] / 100);
                            $volume = $data['volume'];
                            $vol_potongan_pr = $data['vol_potongan'];
                            $volume_potong_split = $data['volume_potong'];
                            $harga_potong = $data['harga_potong'];
                            $volori = ($data['vol_ori_pr'] ? $data['vol_ori_pr'] : $data['volume']);
                            $tmphrg = $data['refund_tawar'] + $data['other_cost'];
                            $nethrg = $data['harga_poc'] - $tmphrg;
                            $pr_harga_beli =  $data['pr_harga_beli'];
                            $pr_harga_beli_potong =  $data['harga_potong'];
                            $netgnl = ($nethrg - $data['harga_normal']) * $volume;
                            //$netprt = ($nethrg - $data['pr_harga_beli']) * $volume;
                            $form_split_pr = $data['splitted_from_pr'];




                            $disposisi_losses = $data['disposisi_losses'];

                            $losses = $data['losses'];
                            $batas = $data['batas_toleransi'];
                            $harga_poc = $data['harga_poc'];


                            $ditanggung_customer = ($data['losses'] > $data['batas_toleransi']) ? ($data['losses'] - $data['batas_toleransi']) : $data['losses'];

                            $hasil = ($losses > $batas) ? ($losses - $batas) : 0;

                            $totallossesreal = $harga_poc * $hasil;

                            $batas_toleransi_transportir = $data['batas_toleransi_transportir'];

                            $hasil_transportir = ($losses >  $batas_toleransi_transportir) ? ($losses - $batas_toleransi_transportir) : 0;

                            $totallossesrealtransportir = $harga_poc * $hasil_transportir;

                            //menghitung harga transportir selisih dari toleransi customer 
                            if ($losses > $batas) {
                                $selisih = $losses -  $batas_toleransi_transportir;
                                $hrgtransportir = $selisih * $harga_poc;
                            }

                            $lampPt   = $data['lampiran_losses'];
                            $lampName = $data['lampiran_losses_ori'];
                            $pathPt   = $public_base_directory . '/files/uploaded_user/lampiran/' . $lampPt;

                            if ($lampPt && file_exists($pathPt)) {
                                $linkPt = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=7&ktg=&file=" . $lampPt);
                                $attach1 = '<a href="' . $linkPt . '" target="_blank"><i class="fa fa-file-alt" title="' . $lampName . '"></i></a>';
                            } else {
                                $attach1 = '-';
                            }








                            // $pathPt = $public_base_directory . '/files/uploaded_user/lampiran/' . $data['lampiran_poc'];
                            // $lampPt = $data['lampiran_poc_ori'];

                            $link_cetak     = ACTION_CLIENT . '/penawaran-cetak.php?' . paramEncrypt('idr=' . $data['id_customer'] . '&idk=' . $data['id_penawaran'] . '&bhs=ind');

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
                            $rincian = json_decode($data['detail_rincian'], true);
                            $tabel_harga = '<table border="0" cellpadding="" cellspacing="0" width="200">';
                            $harga_dasar_new = 0;
                            foreach ($rincian as $idx23 => $arr1) {
                                $cetak = 1;
                                $nilai = $arr1['nilai'];
                                $biaya = ($arr1['biaya']) ? $arr1['biaya'] : '';
                                $biaya = ($rsm['pembulatan']) ? number_format($arr1['biaya']) : number_format($arr1['biaya'], 2);
                                $jenis = $arr1['rincian'];
                                if ($idx23 == 0) {
                                    $harga_dasar_new = str_replace(",", "", $biaya);
                                }

                                $tabel_harga .= '
								<tr>
									<td align="left" witdh="110">' . $jenis . ($nilai ? " " . $nilai . "%" : "") . '</td>
									<td align="right">' . $biaya . '</td>
								</tr>
								';
                            }
                            $tabel_harga .= '
							<tr>
								<td align="left" colspan="2">' . ($data['gabung_oa'] ? '<p style="margin:5px 0px 0px;"><i>* Harga Dasar Inc. OA</i></p>' : '') . '</td>
							</tr>';
                            $tabel_harga .= '</table>';

                            // $tmphrg = $data['refund_tawar'] + $oildus + $data['transport'] + $pbbkbN + $data['other_cost'];
                            // $nethrg = $data['harga_poc'] - $tmphrg;
                            // $netprt = ($harga_dasar_new - $tmphrg - $data['pr_harga_beli']) * $volume;
                            $total1 = $total1 + $volume;
                            $total2 = $total2 + $data['vol_ket'];
                            $total4 = $total4 + $netgnl;

                            if ($vol_potongan_pr == 0) {
                                $total_harga_dasar_nett = $harga_dasar_new - $tmphrg;
                                $netprt = ($total_harga_dasar_nett - $pr_harga_beli) * $volume;
                            } else {
                                $total_harga_dasar_nett = $harga_dasar_new - $tmphrg;
                                $netprt = ($total_harga_dasar_nett - $pr_harga_beli) * $vol_potongan_pr;
                            }
                            // Memeriksa jika harga_potong lebih besar dari 0 sebelum menghitung $netprt1
                            if ($harga_potong > 0) {
                                $total_harga_dasar_nett1 = $harga_dasar_new - $tmphrg;
                                $netprt1 = ($total_harga_dasar_nett1 - $pr_harga_beli_potong) * $volume_potong_split;
                            } else {
                                $netprt1 = 0;
                            }

                            // Menambahkan $netprt1 ke total hanya jika harga_potong lebih besar dari 0
                            $total3 = $total3 + $netprt + $netprt1;
                    ?>
                            <tr>

                                <td class="text-center"><span class="noFormula"><?php echo $nom; ?></span></td>
                                <td>
                                    <p style="margin-bottom:0px"><b><?php echo ($data['kode_pelanggan'] ? $data['kode_pelanggan'] . ' - ' : '') . $data['nama_customer']; ?></b></p>


                                </td>
                                <td>
                                    <p style="margin-bottom:0px"><b><?php echo $data['nama_area']; ?></b></p>
                                    <p style="margin-bottom:0px"><?php echo $alamat; ?></p>

                                </td>
                                <td>
                                    <p style="margin-bottom:0px"><b><?php echo $data['nomor_poc']; ?></b></p>


                                </td>

                                <td class="text-right volume_oil">

                                    <?php
                                    echo number_format($data['volume']);
                                    echo '<input type="hidden" name="is_request" id="" class="form-control" value="' . $data['is_request'] . '"/>';
                                    echo '<input type="hidden" name="id_plan" id="" class="form-control" value="' . $data['id_plan'] . '"/>';
                                    echo '<input type="hidden" name="id_pr" id="" class="form-control" value="' . $data['id_pr'] . '"/>';
                                    echo '<input type="hidden" name="id_prd" id="" class="form-control" value="' . $data['id_prd'] . '"/>';
                                    echo '<input type="hidden" name="id_pod" id="" class="form-control" value="' . $data['id_pod'] . '"/>';
                                    echo '<input type="hidden" name="is_loaded" id="" class="form-control" value="' . $data['is_loaded'] . '"/>';
                                    echo '<input type="hidden" name="id_produk" id="" class="form-control" value="' . $data['id_master'] . '"/>';
                                    echo '<input type="hidden" name="id_vendor" id="" class="form-control" value="' . $data['pr_vendor'] . '"/>';
                                    echo '<input type="hidden" name="id_po_supplier" id="" class="form-control" value="' . $data['id_po_supplier'] . '"/>';
                                    echo '<input type="hidden" name="id_po_receive" id="" class="form-control" value="' . $data['id_po_receive'] . '"/>';
                                    echo '<input type="hidden" name="volume" id="" class="form-control" value="' . $data['volume'] . '"/>';

                                    ?>
                                </td>

                                <td class="text-right volume_oil">
                                    <?php
                                    echo number_format($data['realisasi_volume']);
                                    ?>
                                </td>

                                <td class="text-right volume_oil">
                                    <?php
                                    echo number_format($data['losses']);
                                    ?>
                                </td>

                                <td class="text-right volume_oil">
                                    <?php
                                    echo $data['tol_susut'];
                                    ?> %
                                </td>

                                <td class="text-right volume_oil">
                                    <?php
                                    echo number_format($data['batas_toleransi']);
                                    ?>
                                </td>


                                <td class="text-right volume_oil">
                                    <?php
                                    echo number_format($data['batas_toleransi']);
                                    ?>
                                </td>


                                <td class="text-right volume_oil">
                                    <?php
                                    echo number_format($data['harga_poc']);
                                    ?>
                                </td>



                                <td class="text-right volume_oil">
                                    <?php
                                    echo number_format($hasil);
                                    ?>
                                </td>


                                <td class="text-right volume_oil">
                                    <?php
                                    echo number_format($totallossesreal);
                                    ?>
                                </td>

                                <!-- <td class="text-right volume_oil">
                                    <?php
                                    echo number_format($data['batas_toleransi_transportir']);
                                    ?>
                                </td> -->

                                <!-- <td class="text-right volume_oil">
                                    <?php
                                    echo $data['toleransi_susut'];
                                    ?> %
                                </td> -->

                                <!-- <td class="text-right volume_oil">
                                    <?php
                                    echo number_format($hrgtransportir);
                                    ?>
                                </td> -->

                                <!-- <td class="text-right volume_oil">
                                    <?php
                                    echo number_format($hasil_transportir);
                                    ?>
                                </td>


                                <td class="text-right volume_oil">
                                    <?php
                                    echo number_format($totallossesrealtransportir);
                                    ?>
                                </td> -->



                                <td style="display: none;">
                                    <?php
                                    echo '<input class="ps1" type="text" id="ps1' . $nom . '" name="ps1[' . $idp . ']" value="" />';
                                    ?>
                                </td>
                                <td style="display: none;">
                                    <?php
                                    echo '<input class="pr1" type="text" id="pr1' . $nom . '" name="pr1[' . $idp . ']" value="" />';
                                    echo '<input type="text" name="top_plan[' . $idp . ']" id="top_plan' . $nom . '" class="form-control input-po" value="' . $dt1Customer  . '" />';
                                    echo '<input type="text" name="credit_limit[' . $idp . ']" id="top_plan' . $nom . '" class="form-control input-po" value="' . $credit_limit  . '" />';
                                    echo '<input type="text" name="id_pro[' . $idp . ']" id="id_pro' . $nom . '" class="form-control input-po" value="' . $id_pro  . '" />';

                                    ?>
                                </td>
                                <td style="display: none;">
                                    <?php
                                    echo '<input class="dv1" type="text" id="dv1' . $nom . '" name="dv1[' . $idp . ']" value="" />';
                                    ?>
                                </td>
                                <td style="display: none;">
                                    <?php
                                    echo '<input class="si1" type="text" id="si1' . $nom . '" name="si1[' . $idp . ']" value="" />';
                                    ?>
                                </td>


                                <td class="text-left">
                                    <p style="margin-bottom:0px"><b><?php echo $data['nama_suplier'] ?></b></p>
                                    <p style="margin-bottom:0px"><?php echo $data['no_spj'] ?></p>
                                    <p style="margin-bottom:0px">Truck &nbsp;: <?php echo $data['nomor_plat'] ?></p>
                                    <p style="margin-bottom:0px">Driver : <?php echo $data['nama_sopir'] ?></p>
                                </td>

                                <td class="text-center volume_oil">
                                    <?php
                                    echo $attach1;
                                    echo '<p>' . $data['lampiran_losses_ori'] . '</p>';
                                    ?>
                                </td>
                                <td class="text-left">
                                    <p style="margin-bottom:0px"><b><?php echo $data['catatan_losses'] ?></b></p>

                                </td>
                                <td class="text-left">
                                    <p style="margin-bottom:0px"><b>NO DO SYOP : </b></p>
                                    <p style="margin-bottom:5px"><?php echo ($data['no_do_syop'] ? $data['no_do_syop'] : 'N/A'); ?></p>

                                    <p style="margin-bottom:0px"><b>Loading Order : </b></p>
                                    <p style="margin-bottom:0px"><?php echo ($data['nomor_lo_pr'] ? $data['nomor_lo_pr'] : 'N/A'); ?></p>
                                </td>
                            </tr>
                    <?php }
                    } ?>
                </tbody>

            </table>
        </div>
    </div>
</div>



<div class="modal fade" id="modalPO" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">List PO Supplier</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" class="form-control input-sm" id="searchInput" onkeyup="searchTerminal()" placeholder="Keywords">
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-hover" id="data-po">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Nama Terminal</th>
                                <th class="text-center">Stock Inventory</th>
                                <th class="text-center">Nomor PO</th>
                                <th class="text-center">Vol PO Supplier</th>
                                <th class="text-center">Vol Terima Barang</th>
                                <th class="text-center">Harga Tebus</th>
                                <th class="text-center">Nama Vendor</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="bodyResult">
                            <!-- Data tabel akan dimasukkan di sini -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if ($data['disposisi_losses'] == 3 && $data['om_result'] == 1 && $sesrole == 6) { ?>
    <div class="row">
        <div class="col-md-4">
            <label>Terverifikasi OM</label>
            <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                <p style="margin:10px 0 0; font-size:12px;">
                    <i>Terverifikasi</i>
                <p></p>
                <i>
                    <?php
                    echo ($data['om_pic'] ? $data['om_pic'] . ' - ' : '&nbsp;') .
                        ($data['om_tanggal'] ? date("d/m/Y H:i:s", strtotime($data['om_tanggal'])) . ' WIB' : '');
                    ?> </i>
                </p>
            </div>
        </div>
    </div>
    <P></P>

<?php }  ?>


<?php if ($data['disposisi_losses'] == 4 && $data['bm_result'] == 1 && $sesrole == 6) { ?>
    <div class="row">
        <div class="col-md-4">
            <label>Terverifikasi BM</label>
            <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                <p style="margin:10px 0 0; font-size:12px;">
                    <i>Terverifikasi</i>
                <p></p>
                <i>
                    <?php
                    echo ($data['bm_pic'] ? $data['bm_pic'] . ' - ' : '&nbsp;') .
                        ($data['bm_tanggal'] ? date("d/m/Y H:i:s", strtotime($data['bm_tanggal'])) . ' WIB' : '');
                    ?> </i>
                </p>
            </div>
        </div>
    </div>
    <P></P>

<?php }  ?>

<?php if ($data['disposisi_losses'] == 4 && $data['bm_result'] == 1 && $sesrole == 7) { ?>
    <div class="row">
        <div class="col-md-4">
            <label>Terverifikasi BM</label>
            <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                <p style="margin:10px 0 0; font-size:12px;">
                    <i>Terverifikasi</i>
                <p></p>
                <i>
                    <?php
                    echo ($data['bm_pic'] ? $data['bm_pic'] . ' - ' : '&nbsp;') .
                        ($data['bm_tanggal'] ? date("d/m/Y H:i:s", strtotime($data['bm_tanggal'])) . ' WIB' : '');
                    ?> </i>
                </p>
            </div>
        </div>
    </div>
    <P></P>

<?php }  ?>

<?php if ($data['disposisi_losses'] == 4 && $data['bm_result'] == 1 && $sesrole == 15) { ?>
    <div class="row">
        <div class="col-md-4">
            <label>Terverifikasi BM</label>
            <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                <p style="margin:10px 0 0; font-size:12px;">
                    <i>Terverifikasi</i>
                <p></p>
                <i>
                    <?php
                    echo ($data['bm_pic'] ? $data['bm_pic'] . ' - ' : '&nbsp;') .
                        ($data['bm_tanggal'] ? date("d/m/Y H:i:s", strtotime($data['bm_tanggal'])) . ' WIB' : '');
                    ?> </i>
                </p>
            </div>
        </div>
    </div>
    <P></P>

<?php }  ?>




<?php if ($data['disposisi_losses'] == 4 && $data['fin_result'] == 1 && $sesrole == 6) { ?>
    <div class="row">

        <div class="col-md-4">
            <label>Terverifikasi OM</label>
            <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                <p style="margin:10px 0 0; font-size:12px;">
                    <i>Terverifikasi</i>
                <p></p>
                <i>
                    <?php
                    echo ($data['om_pic'] ? $data['om_pic'] . ' - ' : '&nbsp;') .
                        ($data['om_tanggal'] ? date("d/m/Y H:i:s", strtotime($data['om_tanggal'])) . ' WIB' : '');
                    ?> </i>
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <label>Terverifikasi Mgr Finance</label>
            <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                <p style="margin:10px 0 0; font-size:12px;">
                    <i>Terverifikasi</i>
                <p></p>
                <i>
                    <?php
                    echo ($data['fin_pic'] ? $data['fin_pic'] . ' - ' : '&nbsp;') .
                        ($data['fin_tanggal'] ? date("d/m/Y H:i:s", strtotime($data['fin_tanggal'])) . ' WIB' : '');
                    ?> </i>
                </p>
            </div>
        </div>
    </div>
    <P></P>

<?php }  ?>


<?php if ($data['disposisi_losses'] == 4 && $data['fin_result'] == 1 && $sesrole == 15) { ?>
    <div class="row">

        <div class="col-md-4">
            <label>Terverifikasi OM</label>
            <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                <p style="margin:10px 0 0; font-size:12px;">
                    <i>Terverifikasi</i>
                <p></p>
                <i>
                    <?php
                    echo ($data['om_pic'] ? $data['om_pic'] . ' - ' : '&nbsp;') .
                        ($data['om_tanggal'] ? date("d/m/Y H:i:s", strtotime($data['om_tanggal'])) . ' WIB' : '');
                    ?> </i>
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <label>Terverifikasi Mgr Finance</label>
            <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                <p style="margin:10px 0 0; font-size:12px;">
                    <i>Terverifikasi</i>
                <p></p>
                <i>
                    <?php
                    echo ($data['fin_pic'] ? $data['fin_pic'] . ' - ' : '&nbsp;') .
                        ($data['fin_tanggal'] ? date("d/m/Y H:i:s", strtotime($data['fin_tanggal'])) . ' WIB' : '');
                    ?> </i>
                </p>
            </div>
        </div>
    </div>
    <P></P>

<?php }  ?>

<?php if ($data['disposisi_losses'] == 2 && $data['om_result'] == 0 && $sesrole == 6) { ?>
    <div class="form-group row">

        <div class="col-sm-3">
            <label>Verifikasi </label>
            <div class="radio clearfix" style="margin:0px;">
                <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="revert" id="revert1" class="validate[required]" value="1" /> Ya</label>
            </div>
        </div>
    </div>
    </div>
    </div>
<?php }  ?>




<?php if ($data['disposisi_losses'] == 3 && $data['om_result'] == 1 && $sesrole == 15) { ?>
    <div class="form-group row">
        <div class="col-sm-3">
            <label>Verifikasi </label>
            <div class="radio clearfix" style="margin:0px;">
                <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="revert" id="revert1" class="validate[required]" value="1" /> Ya</label>
            </div>
        </div>
    </div>
    </div>
    </div>
<?php }  ?>

<?php if ($data['disposisi_losses'] == 1 && $data['bm_result'] == 0 && $sesrole == 7) { ?>
    <div class="form-group row">
        <div class="col-sm-3">
            <label>Verifikasi </label>
            <div class="radio clearfix" style="margin:0px;">
                <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="revert" id="revert1" class="validate[required]" value="1" /> Ya</label>
            </div>
        </div>
    </div>
    </div>
    </div>
<?php }  ?>

<hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

<div style="margin-bottom:0px;">
    <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
    <input type="hidden" name="idw" value="<?php echo $row[0]['id_wilayah']; ?>" />
    <input type="hidden" name="idg" value="<?php echo $row[0]['id_group']; ?>" />
    <input type="hidden" name="prnya" value="purchasing" />
    <input type="hidden" name="backadmin" value="0" />
    <input type="hidden" name="is_ceo" value="<?php echo $res[0]['is_ceo']; ?>" />
    <a href="<?php echo BASE_URL_CLIENT . '/verifikasi-losses.php'; ?>" class="btn btn-default jarak-kanan" style="min-width:90px;">Kembali</a>

    <?php if ($data['disposisi_losses'] == 2 && $data['om_result'] == 0 && $sesrole == 6) { ?>
        <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">Simpan</button>
    <?php } elseif ($data['disposisi_losses'] == 3 && $data['om_result'] == 1 && $sesrole == 15) { ?>
        <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">Simpan</button>
    <?php } elseif ($data['disposisi_losses'] == 1 && $data['bm_result'] == 0 && $sesrole == 7) { ?>
        <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">Simpan</button>
    <?php } ?>


    <button type="button" class="btn btn-danger jarak-kanan" name="btnCancel" id="btnCancel" style="min-width:90px; display:none;">Batal</button>






</div>



<div class="modal fade" id="user_modal" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="width:1000px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Inventory By Depot </h4>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>


<style type="text/css">
    .input-po {
        padding: 3px 5px;
        height: auto;
        font-size: 11px;
        font-family: arial;
    }

    .select2-search--dropdown .select2-search__field {
        font-family: arial;
        font-size: 11px;
        padding: 4px 3px;
    }

    .select2-results__option {
        font-family: arial;
        font-size: 11px;
    }

    #table-grid3 {
        border-collapse: separate;
    }

    #modalPO .modal-dialog {
        max-width: 90%;
        width: 90%;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {

        $("#btnSbmt").on("click", function(e) {
            e.preventDefault(); // Mencegah tombol langsung submit

            Swal.fire({
                title: "Konfirmasi Simpan",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Simpan!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#gform").submit(); // Submit form jika user konfirmasi
                }
            });
        });



        $("input[name='revert']").on("ifChecked", function() {
            var nilai = $(this).val();
            if (nilai == 1) {
                $(".persetujuan-pr").addClass("hide");
            } else if (nilai == 2) {
                $(".persetujuan-pr").removeClass("hide");

            }
        });
    });



    $(".hitung").number(true, 0, ".", ",");

    $("#gform").find(".dp2").each(function() {
        if ($(this).val() != "") {
            var elm = $(this);
            hitungNettProfit(elm);
        }
    });
</script>
<div style="overflow-y:scroll; overflow:scroll;" id="table-long">
    <div style="width:1500px; height:350px;">
        <div class="table-responsive-satu">
            <table class="table table-bordered table-grid3" id="table-grid3">
                <thead>
                    <tr>

                        <th class="text-center" rowspan="2" width="10">No</th>
                        <th class="text-center" rowspan="2" width="50">Customer/ Bidang Usaha</th>
                        <th class="text-center" rowspan="2" width="250">Area/ Alamat Kirim/ Wilayah OA</th>
                        <th class="text-center" rowspan="2" width="100">PO Customer</th>
                        <th class="text-center" rowspan="2" width="20">Volume Awal (Liter)</th>
                        <th class="text-center" rowspan="2" width="20">Volume (Liter)</th>
                        <th class="text-center" rowspan="2" width="200">Depot</th>
                        <th class="text-center" rowspan="2" width="150">Transportir</th>
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
                    b.id_wilayah,
                    f.nomor_pr,
                    f.tanggal_pr,
                    i.nama_customer,
                    h.nomor_poc,
                    e.volume,
                    d.nomor_po,
                    j.nama_cabang,
                    i.kode_pelanggan, l.jenis_usaha, k.fullname,n.nama_area, l.alamat_survey,o.nama_prov, p.nama_kab,  r.wilayah_angkut,
                    s.merk_dagang, g.tanggal_kirim, e.vol_ori_pr, e.volume, e.nomor_po_supplier, e.vol_potongan, m.harga_dasar,m.refund_tawar,m.other_cost,e.pr_harga_beli, e.splitted_from_pr, i.jenis_payment, i.top_payment,
                    g.top_plan, i.credit_limit, o1.harga_normal, h.harga_poc, m.detail_rincian, g.status_plan, g.catatan_reschedule,g.status_jadwal,
                    e.no_do_syop, e.nomor_lo_pr, f.purchasing_summary,s.id_master, u.nama_terminal, u.tanki_terminal, u.lokasi_terminal, 
                  z.nama_suplier, c.no_spj, x.nama_sopir, w.nomor_plat, s.id_master, e.pr_vendor, e.id_po_supplier, e.id_po_receive, c.id_pod,g.tanggal_loading,e.id_do_accurate

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
                    left join pro_master_harga_minyak o1 on m.masa_awal = o1.periode_awal and m.masa_akhir = o1.periode_akhir and m.id_area = o1.id_area 
                        and m.pbbkb_tawar = o1.pajak and o1.is_approved = 1 
                        
                    
							
                

						where 
							a.id_dsd= '" . $idr . "' and 
							(a.is_request = 2 OR a.is_request = 3)
						order by a.is_request desc";

                    $res = $con->getResult($sql);
                    $fnr = $res[0]['is_approved'] ?? null;


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

                            $pathPt = $public_base_directory . '/files/uploaded_user/lampiran/' . $data['lampiran_poc'];
                            $lampPt = $data['lampiran_poc_ori'];

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
                                    <p style="margin-bottom:0px"><?php echo $data['jenis_usaha']; ?></p>
                                    <p style="margin-bottom:0px"><i><?php echo $data['fullname']; ?></i></p>
                                </td>
                                <td>
                                    <p style="margin-bottom:0px"><b><?php echo $data['nama_area']; ?></b></p>
                                    <p style="margin-bottom:0px"><?php echo $alamat; ?></p>
                                    <p style="margin-bottom:0px"><?php echo 'Wilayah OA : ' . $data['wilayah_angkut']; ?></p>
                                </td>
                                <td>
                                    <p style="margin-bottom:0px"><b><?php echo $data['nomor_poc']; ?></b></p>
                                    <p style="margin-bottom:0px"><?php echo $data['merk_dagang']; ?></p>
                                    <p style="margin-bottom:0px"><?php echo 'Tgl Kirim ' . tgl_indo($data['tanggal_kirim']); ?></p>

                                </td>
                                <td class="text-right">
                                    <?php if (!$fnr) {
                                        echo number_format($volume);
                                    } else {
                                        echo number_format($data['vol_ori_pr']);
                                    }
                                    ?>
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
                                    echo '<input type="hidden" name="wilayah" id="" class="form-control" value="' . $data['id_wilayah'] . '"/>';

                                    ?>
                                </td>


                                <td>
                                    <?php

                                    echo '<div class="divText">';
                                    $tmn1 = ($data['nama_terminal']) ? $data['nama_terminal'] : '';
                                    $tmn2 = ($data['tanki_terminal']) ? ' - ' . $data['tanki_terminal'] : '';
                                    $tmn3 = ($data['lokasi_terminal']) ? ', ' . $data['lokasi_terminal'] : '';

                                    echo '<input type="hidden" name="dp8[' . $idp . ']" id="dp8' . $nom . '" value="' . $data['pr_terminal'] . '" />';
                                    echo $tmn1 . $tmn2 . $tmn3;
                                    echo '<br>';
                                    echo ' <p style="margin-bottom:0px">ETL : ' . tgl_indo($data['tanggal_loading'], 'short') . ' ' . date("H:i", strtotime($data['jam_loading'])) . '</p>';
                                    echo '<p style="margin-bottom:0px">ETA : ' . tgl_indo($data['tgl_eta_po'], 'short') . ' ' . date("H:i", strtotime($data['jam_eta_po'])) . '</p>';


                                    // echo '<p style="margin:5px 0 0;"><a style="cursor:pointer" class="detInven" data-idnya="' . $nom . '">Detil Inventory</a></p>';
                                    ?>
                                    <span id="nt1<?= $nom ?>" class="nt1" style="display: none;"></span><br>
                                    <span id="sc1<?= $nom ?>" class="sc1" style="display: none;"></span>
                                </td>
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

<!-- <div class="modal fade" id="modalPO" role="dialog" tabindex="-1" aria-hidden="true">
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
                           
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> -->

<?php if (!$fnr) { ?>
    <div class="form-group row">
        <div class="col-sm-6">
            <label>Catatan Request Logistik</label>

            <textarea name="summary" id="summary" class="form-control"><?php if ($res) {
                                                                            echo str_replace("<br />", PHP_EOL, $res[0]['request']);
                                                                        } ?></textarea>


        </div>
        <div class="col-sm-3">

            <?php if (!$fnr && $data['is_request'] == 2) {
                echo '<label>Tanggal Kirim</label>';
                echo '<input type="text" name="tgl_kirim" id="" class="form-control input-po datepicker" value="' . date('d/m/Y',strtotime($data['tanggal_loading']))  . '" autocomplete="off" width="50%"/>';
            } else {
            } ?>
        </div>
        <div class="col-sm-3">
            <label>Verifikasi ?*</label>
            <div class="radio clearfix" style="margin:0px;">
                <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="revert" id="revert1" class="validate[required]" value="1" /> Ya</label>
                <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="revert" id="revert2" class="validate[required]" value="2" /> Tidak</label>
            </div>



        </div>
         <input type="hidden" id="id_do_accurate" name="id_do_accurate" value="<?php echo $data['id_do_accurate'] ?>" />
    </div>

    <div class="form-group row">
        <div class="col-sm-6">
            <label class="persetujuan-pr">Catatan Dikembalikan</label>

            <textarea name="summary_revert" id="summary_revert" class="form-control persetujuan-pr"></textarea>
        </div>




    </div>

    </div>
<?php } else if ($fnr) { ?>
    <div class="form-group row">
        <div class="col-sm-6">
            <label>Catatan Request Logistik</label>

            <textarea name="summary" id="summary" class="form-control"><?php if ($res) {
                                                                            echo str_replace("<br />", PHP_EOL, $res[0]['request']);
                                                                        } ?></textarea>


        </div>


    </div>
    <div class="form-group row">

        <?php if ($data['is_revert'] == 2) { ?>
            <div class="col-sm-6">
                <label class="persetujuan-pr">Catatan Dikembalikan</label>

                <textarea name="summary_revert" id="summary_revert" class="form-control persetujuan-pr"><?php if ($res) {
                                                                                                            echo str_replace("<br />", PHP_EOL, $res[0]['revert_summary']);
                                                                                                        } ?></textarea>

            </div>
        <?php } ?>
    </div>

    </div>
<?php } ?>

<?php if (!$fnr) { ?>
    <div class="form-group row">



    </div>
<?php } ?>


<hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

<div style="margin-bottom:0px;">
    <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
    <input type="hidden" name="idw" value="<?php echo $row[0]['id_wilayah']; ?>" />
    <input type="hidden" name="idg" value="<?php echo $row[0]['id_group']; ?>" />
    <input type="hidden" name="prnya" value="purchasing" />
    <input type="hidden" name="backadmin" value="0" />
    <input type="hidden" name="is_ceo" value="<?php echo $res[0]['is_ceo']; ?>" />
    <a href="<?php echo BASE_URL_CLIENT . '/verifikasi-request.php'; ?>" class="btn btn-default jarak-kanan" style="min-width:90px;">Kembali</a>

    <?php if (!$fnr) { ?>
        <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">Simpan</button>
    <?php } ?>
    <button type="button" class="btn btn-danger jarak-kanan" name="btnCancel" id="btnCancel" style="min-width:90px; display:none;">Batal</button>






</div>


<!-- 
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
</div> -->


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





    /* .table-grid3>thead>tr>th:nth-child(1) {
		position: sticky;
		left: 0px;
		z-index: 2;
	}

	.table-grid3>thead>tr>th:nth-child(2) {
		position: sticky;
		left: 100px;
		z-index: 2;
	}

	.table-grid3>thead>tr>th:nth-child(3) {
		position: sticky;
		left: 150px;
		z-index: 2;
	}

	.table-grid3>tbody>tr>td:nth-child(1) {
		background-color: #f4f4f4;
		position: sticky;
		left: 0px;
		z-index: 2;
	}

	.table-grid3>tbody>tr>td:nth-child(2) {
		background-color: #f4f4f4;
		position: sticky;
		left: 100px;
		z-index: 2;
	}

	.table-grid3>tbody>tr>td:nth-child(3) {
		background-color: #f4f4f4;
		position: sticky;
		left: 150px;
		z-index: 2;
	} */
</style>
<script>
    $(document).ready(function() {
        $("input[name='revert']").on("ifChecked", function() {
            var nilai = $(this).val();
            if (nilai == 1) {
                $(".persetujuan-pr").addClass("hide");
            } else if (nilai == 2) {
                $(".persetujuan-pr").removeClass("hide");

            }
        });
    });

    // function searchTerminal() {
    //     var input, filter, table, tr, td, i, txtValue;
    //     input = document.getElementById("searchInput");
    //     filter = input.value.toUpperCase();
    //     table = document.getElementById("data-po");
    //     tr = table.getElementsByTagName("tr");

    //     for (i = 0; i < tr.length; i++) {
    //         tdNamaTerminal = tr[i].getElementsByTagName("td")[1];
    //         tdNomorPO = tr[i].getElementsByTagName("td")[3];
    //         if (tdNamaTerminal || tdNomorPO) {
    //             txtValueNamaTerminal = tdNamaTerminal.textContent || tdNamaTerminal.innerText;
    //             txtValueNomorPO = tdNomorPO.textContent || tdNomorPO.innerText;
    //             if (txtValueNamaTerminal.toUpperCase().indexOf(filter) > -1 || txtValueNomorPO.toUpperCase().indexOf(filter) > -1) {
    //                 tr[i].style.display = "";
    //             } else {
    //                 tr[i].style.display = "none";
    //             }
    //         }
    //     }
    // }

    // function myFunction(nom) {
    //     $.ajax({
    //         type: "POST",
    //         url: `<?= BASE_URL . "/web/__get_po_by_terminal.php" ?>`,
    //         dataType: "json",
    //         success: function(result) {
    //             $('#modalPO').modal({
    //                 show: true
    //             })
    //             // console.log(result)
    //             var html = "";
    //             for (var i = 0; i < result.length; i++) {

    //                 var no = i + 1;
    //                 html += "<tr>";
    //                 html += "<td>" + no + "</td>";
    //                 html += "<td>" + result[i]['nama_terminal'] + " " + result[i]['tanki_terminal'] + " " + result[i]['lokasi_terminal'] + "</td>";
    //                 html += "<td align='center' nowrap>" + new Intl.NumberFormat().format(result[i]['sisa_inven']) + "</td>";
    //                 html += "<td align='center' nowrap>" + result[i]['nomor_po_supplier'] + "</td>";
    //                 html += "<td align='center' nowrap>" + new Intl.NumberFormat().format(result[i]['vol_po_supplier']) + "</td>";
    //                 html += "<td align='center' nowrap>" + new Intl.NumberFormat().format(result[i]['vol_terima_barang']) + "</td>";
    //                 html += "<td align='center' nowrap>Rp. " + new Intl.NumberFormat().format(result[i]['harga_tebus']) + "</td>";
    //                 html += "<td align='center' nowrap>" + result[i]['nama_vendor'] + "</td>";
    //                 html += "<td align='center' nowrap><button type='button' class='btn btn-success btn-md btn-pilih' data-detail='" + result[i]['nomor_po_supplier'] + "|#|" + result[i]['id_po_supplier'] + "|#|" + result[i]['id_po_receive'] + "|#|" + result[i]['harga_tebus'] + "|#|" + result[i]['id_vendor'] + "|#|" + result[i]['sisa_inven'] + "|#|" + result[i]['id_terminal'] + "|#|" + result[i]['nama_terminal'] + "|#|" + result[i]['tanki_terminal'] + "|#|" + result[i]['lokasi_terminal'] + "' data-nom='" + nom + "'>PILIH</button></td>";
    //                 html += "</tr>";
    //             }
    //             $('#bodyResult').html(html);
    //         }
    //     });
    // }

    // $(document).ready(function() {

    //     $("#data-po").on("click", ".btn-pilih", function() {
    //         let index = $(this).data('detail');
    //         let nom = $(this).data('nom');
    //         let param = index.toString().split('|#|');
    //         let sisa_inven = parseInt(decodeURIComponent(param[5]));

    //         let nama_terminal = decodeURIComponent(param[7])
    //         let tanki_terminal = decodeURIComponent(param[8])
    //         let lokasi_terminal = decodeURIComponent(param[9])

    //         var volume = $("#volume" + nom).val();

    //         if (volume == "") {
    //             Swal.fire({
    //                 title: "Oppss..",
    //                 text: "Silahkan isi volume terlebih dahulu",
    //                 icon: "warning"
    //             });
    //             $('#nt1' + nom).css('display', 'none');
    //             $('#sc1' + nom).css('display', 'none');
    //             $('#tx1' + nom).css('display', 'inline');
    //         } else {
    //             if (sisa_inven < volume) {
    //                 Swal.fire({
    //                     title: "Oppss..",
    //                     text: "Sisa stock kurang",
    //                     icon: "warning"
    //                 });
    //                 $('#nt1' + nom).css('display', 'none');
    //                 $('#sc1' + nom).css('display', 'none');
    //                 $('#tx1' + nom).css('display', 'inline');
    //             } else {
    //                 $('#nt1' + nom).css('display', 'inline');
    //                 $('#sc1' + nom).css('display', 'inline');
    //                 $('#tx1' + nom).css('display', 'none');
    //                 $("#np1" + nom).val(decodeURIComponent(param[0]));
    //                 $("#ps1" + nom).val(parseInt(decodeURIComponent(param[1])));
    //                 $("#pr1" + nom).val(parseInt(decodeURIComponent(param[2])));
    //                 $("#dp2" + nom).val(parseInt(decodeURIComponent(param[3])));
    //                 $("#dv1" + nom).val(parseInt(decodeURIComponent(param[4])));
    //                 $("#si1" + nom).val(parseInt(sisa_inven));
    //                 $("#dp8" + nom).val(parseInt(decodeURIComponent(param[6])));
    //                 $("#nt1" + nom).html("Nama Terminal : " + nama_terminal + " " + tanki_terminal + " " + lokasi_terminal)
    //                 $("#sc1" + nom).html("Sisa Stock : " + new Intl.NumberFormat().format(sisa_inven));
    //                 $("#modalPO").modal("hide");


    //                 // Setelah modal ditutup, panggil hitungNettProfit untuk dp2 yang terisi
    //                 hitungNettProfit($("#dp2" + nom));

    //             }

    //         }

    //     });

    //     $("#user_modal").on('show.bs.modal', function(e) {
    //         $("#loading_modal").modal({
    //             keyboard: false,
    //             backdrop: 'static'
    //         });
    //     }).on('shown.bs.modal', function(e) {
    //         $("#loading_modal").modal("hide");
    //     }).on('click', '#idBataluser_modal', function() {
    //         $("#user_modal").modal("hide");
    //     });

    //     $(".table-grid3").floatThead({
    //         position: 'fixed',
    //         zIndex: 799,
    //         scrollContainer: function($table) {
    //             return $table.closest("#table-long");
    //         },
    //         responsiveContainer: function($table) {
    //             return $table.closest("#table-long");
    //         },
    //         top: function pageTop() {
    //             return $(".main-header").height() + $(".content-header").height();
    //         },
    //     });

    //     $("#urgent").on('ifChanged', function() {
    //         if ($(this).is(':checked')) {
    //             $(".urgent_condition").show(); // Tampilkan tombol "Simpan"
    //         } else {
    //             $(".urgent_condition").hide(); // Sembunyikan tombol "Simpan"
    //         }
    //     });

    //     $(".hitung").number(true, 0, ".", ",");
    //     // $(".dp8").select2({
    //     // 	placeholder: "Pilih salah satu",
    //     // 	allowClear: true
    //     // });
    //     $("#gform").find(".dp2").each(function() {
    //         if ($(this).val() != "") {
    //             var elm = $(this);
    //             hitungNettProfit(elm);
    //         }
    //     });




    //     $("form#gform").on("click", "#btnSbmt", function() {
    //         if (confirm("Apakah anda yakin?")) {
    //             $.ajax({
    //                 type: 'POST',
    //                 url: "./__cek_pr_customer_purchasing.php",
    //                 dataType: "json",
    //                 data: $("#gform").serializeArray(),
    //                 cache: false,
    //                 success: function(data) {
    //                     console.log(data.error);
    //                     if (data.error) {
    //                         swal.fire({
    //                             icon: "warning",
    //                             width: '350px',
    //                             allowOutsideClick: false,
    //                             html: '<p style="font-size:14px; font-family:arial;">' + data.error + '</p>'
    //                         });
    //                         return false;
    //                     } else {
    //                         $("#loading_modal").modal({
    //                             backdrop: "static"
    //                         });
    //                         $("form#gform").submit();
    //                     }
    //                 }
    //             });
    //             return false;
    //         } else return false;
    //     });

    //     $("#gform").on("click", "#table-grid3 button.addRow", function() {
    //         var count = parseInt($(this).attr('data-idp'));
    //         count++;
    //         var row = $(this).closest('tr');
    //         var idl = $(this).val();
    //         var urut = $(this).attr('data-idp', count);

    //         // $("#table-grid3").find(".dp1").each(function(i, v) {
    //         // 	$(v).select2("destroy");
    //         // });
    //         // $("#table-grid3").find(".dp8").each(function(i, v) {
    //         // 	$(v).select2("destroy");
    //         // });

    //         var cloning = row.clone();
    //         cloning.find('td').each(function(i, v) {
    //             var el = $(this).find(":first-child");
    //             var id = el.attr("id") || null;
    //             switch (i) {
    //                 case 0:
    //                     $(v).html('<button type="button" class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></button>');
    //                     break;
    //             }

    //             // console.log(id)

    //             if (id && i != 0) {
    //                 var elName = "new" + el.attr("name").substr(0, 3);
    //                 var elId = el.attr("id");
    //                 el.attr("id", elId + '' + idl + '' + count);
    //                 el.attr('name', elName + '[]');
    //             }
    //         });

    //         let elemen = '<input class="text-right volume hitung" type="text" name="newVolume[]" value="" style="width:100%;" />' +
    //             '<input class="idx"type="hidden" name="newIdx[]" value="' + idl + '" />' +
    //             '<input class="cek" type="hidden" name="newCek[]" value="1" />';

    //         cloning.find(".volume_oil").html(elemen);
    //         row.find("td:last-child");
    //         row.after(cloning);

    //         // $("#table-grid3").find(".dp1").each(function(i, v) {
    //         // 	$(v).select2({
    //         // 		placeholder: "Pilih salah satu",
    //         // 		allowClear: true
    //         // 	});
    //         // });
    //         // $("#table-grid3").find(".dp8").each(function(i, v) {
    //         // 	$(v).select2({
    //         // 		placeholder: "Pilih salah satu",
    //         // 		allowClear: true
    //         // 	});
    //         // });
    //         $(".hitung").number(true, 0, ".", ",");

    //         $("#table-grid3").find(".noFormula").each(function(i, v) {
    //             $(this).text(i + 1);
    //             // $(this).closest('tr').find('.dp1').attr('id', 'dp1' + (i + 1));
    //             $(this).closest('tr').find('.dp8').attr('id', 'dp8' + (i + 1));
    //             $(this).closest('tr').find('.dp2').attr('id', 'dp2' + (i + 1));
    //             $(this).closest('tr').find('.dp9').attr('id', 'dp9' + (i + 1));
    //             $(this).closest('tr').find('.dp3').attr('id', 'dp3' + (i + 1));
    //             $(this).closest('tr').find('.dp4').attr('id', 'dp4' + (i + 1));
    //             $(this).closest('tr').find('.dp5').attr('id', 'dp5' + (i + 1));
    //             $(this).closest('tr').find('.dp6').attr('id', 'dp6' + (i + 1));
    //             $(this).closest('tr').find('.dp7').attr('id', 'dp7' + (i + 1));
    //             $(this).closest('tr').find('.dp10').attr('id', 'dp10' + (i + 1));
    //             $(this).closest('tr').find('.dp11').attr('id', 'dp11' + (i + 1));
    //             $(this).closest('tr').find('.dp12').attr('id', 'dp12' + (i + 1));
    //             $(this).closest('tr').find('.cek').attr('id', 'cek' + (i + 1));
    //             $(this).closest('tr').find('.idx').attr('id', 'idx' + (i + 1));
    //             $(this).closest('tr').find('.np1').attr('onclick', 'myFunction(' + (i + 1) + ')');
    //             $(this).closest('tr').find('.ps1').attr('id', 'ps1' + (i + 1));
    //             $(this).closest('tr').find('.pr1').attr('id', 'pr1' + (i + 1));
    //             $(this).closest('tr').find('.dv1').attr('id', 'dv1' + (i + 1));
    //             $(this).closest('tr').find('.si1').attr('id', 'si1' + (i + 1));
    //             $(this).closest('tr').find('.np1').attr('id', 'np1' + (i + 1));
    //             $(this).closest('tr').find('.nt1').attr('id', 'nt1' + (i + 1));
    //             $(this).closest('tr').find('.sc1').attr('id', 'sc1' + (i + 1));
    //             $(this).closest('tr').find('.tx1').attr('id', 'tx1' + (i + 1));
    //             $(this).closest('tr').find('.volume').attr('id', 'volume' + (i + 1));
    //         });


    //     }).on("click", "#table-grid3 button.resetRow", function() {
    //         if (confirm("Apakah anda yakin?")) {
    //             $("#loading_modal").modal({
    //                 backdrop: "static"
    //             });
    //             var row = $(this).closest('tr');
    //             var idl = $(this).val();
    //             window.location.href = $base_url + "/web/action/reset_split_pr.php?idnya=" + idl;
    //         } else return false;
    //     });

    //     $("#gform").on("click", "#table-grid3 button.hRow", function() {
    //         var cRow = $(this).closest('tr');
    //         cRow.remove();
    //         $("#table-grid3").find(".noFormula").each(function(i, v) {
    //             $(this).text(i + 1);
    //         });
    //     });

    //     $("form#gform").on("click", "#backadmin", function() {
    //         if (confirm("Apakah anda yakin?")) {
    //             $("#loading_modal").modal({
    //                 backdrop: "static"
    //             });
    //             $('input[name="backadmin"]').val(1);
    //             $("form#gform").submit();
    //         } else return false;
    //     });

    //     $("#gform").on("change", "select.dp8", function() {
    //         var idnya = $(this).attr("id").substr(3);
    //         $("#np1" + idnya).val("");
    //         getHargaBeli(idnya);
    //     }).on("keyup", ".dp2", function() {
    //         var elm = $(this);
    //         hitungNettProfit(elm);
    //     });

    //     function getHargaBeli(newId) {
    //         var vendor = $("#dv1" + newId).val();
    //         var awal = $("#dp3" + newId).val();
    //         var akhir = $("#dp4" + newId).val();
    //         var area = $("#dp5" + newId).val();
    //         var produk = $("#dp6" + newId).val();
    //         var depot = $("#dp8" + newId).val();

    //         if (vendor != "" && awal != "" && akhir != "" && area != "" && produk != "" && depot != "") {
    //             $('#loading_modal').modal({
    //                 backdrop: "static"
    //             });
    //             $.ajax({
    //                 type: 'POST',
    //                 url: "./__get_harga_tebus.php",
    //                 data: {
    //                     q1: awal,
    //                     q2: akhir,
    //                     q3: produk,
    //                     q4: area,
    //                     q5: vendor,
    //                     q6: depot
    //                 },
    //                 cache: false,
    //                 success: function(data) {
    //                     $("#dp2" + newId).val(data);
    //                     hitungNettProfit($("#dp2" + newId));
    //                 }
    //             });
    //             $("#loading_modal").modal("hide");
    //         } else {
    //             $("#dp2" + newId).val("");

    //             hitungNettProfit($("#dp2" + newId));
    //         }
    //     }

    //     // function hitungNettProfit(elm) {
    //     // 	var idx = elm.attr("id").split('dp2');
    //     // 	var dt1 = $("#dp2" + idx[1]).val() * 1;
    //     // 	var dt2 = $("#dp10" + idx[1]).val() * 1;
    //     // 	var dt3 = $("#dp11" + idx[1]).val() * 1;
    //     // 	var dtx = (dt3 - dt1) * dt2;
    //     // 	$("#dp9" + idx[1]).val(dtx);
    //     // }


    //     function hitungNettProfit(elm) {
    //         var idx = elm.attr("id").split('dp2');
    //         var dt1 = $("#dp2" + idx[1]).val() * 1;
    //         var dt2 = $("#volume" + idx[1]).val() * 1;
    //         var dt3 = $("#dp12" + idx[1]).val() * 1;
    //         var dtx = (dt3 - dt1) * dt2;
    //         $("#dp9" + idx[1]).val(dtx);
    //     }
    // });
    // $('#btnEdit').on('click', function() {
    //     $('#btnSbmt').css('display', '');
    //     $('#btnCancel').css('display', '');
    //     $('#btnEdit').css('display', 'none');
    //     $('.divText').css('display', 'none');
    //     $('.divEdit').css('display', '');
    //     $(".tombol-addnya").addClass("hide");
    // });
    // $('#btnCancel').on('click', function() {
    //     $('#btnSbmt').css('display', 'none');
    //     $('#btnCancel').css('display', 'none');
    //     $('#btnEdit').css('display', '');
    //     $('.divText').css('display', '');
    //     $('.divEdit').css('display', 'none');
    //     $(".tombol-addnya").addClass("hide");
    // });
</script>
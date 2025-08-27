<?php
$_param = 1;
foreach ($rsms as $key => $rsm) {
    $rsm = (array) $rsm;
    $rincian = json_decode($rsm['detail_rincian'], true);
    $formula = json_decode($rsm['detail_formula'], true);
    $sesrole = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
    $seswil  = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

    $rsm['harga_normal'] = ($rsm['harga_normal'] ? $rsm['harga_normal'] : $rsm['harga_normal_new']);
    $rsm['harga_sm'] = ($rsm['harga_sm'] ? $rsm['harga_sm'] : $rsm['harga_sm_new']);
    $rsm['harga_om'] = ($rsm['harga_om'] ? $rsm['harga_om'] : $rsm['harga_om_new']);
    $rsm['harga_coo'] = ($rsm['harga_coo'] ? $rsm['harga_coo'] : $rsm['harga_coo_new']);
    $rsm['harga_ceo'] = ($rsm['harga_ceo'] ? $rsm['harga_ceo'] : $rsm['harga_ceo_new']);

    $arrKondInd    = array(0 => '', 1 => "Setelah Invoice diterima", "Setelah pengiriman", "Setelah loading");
    $arrKondEng = array(0 => '', 1 => "After Invoice Receive", "After Delivery", "After Loading");
    $jenis_net  = $rsm['jenis_net'];
    $arrPayment = array("CREDIT" => "CREDIT " . $rsm['jangka_waktu'] . " hari " . $arrKondInd[$jenis_net], "CBD" => "CBD (Cash Before Delivery)", "COD" => "COD (Cash On Delivery)");
    if ($rsm['id_penawaran'] <= $idk && $_param <= 3) {
?>
        <div class="box box-primary">
            <div class="box-header with-border">
                Data Penawaran di tanggal : <?php echo date('d/m/Y', strtotime($rsm['created_time'])); ?> &nbsp; &nbsp; &nbsp; -
                Status PO : <span style="color: <?php echo ($rsm['penawaran_status'] == 'YA' ? 'green' : 'red'); ?>;">
                    <?php echo $rsm['penawaran_status']; ?>
                </span>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table no-border">
                        <tr>
                            <td width="180">Nama Customer</td>
                            <td width="10" class="text-center">:</td>
                            <td><?php echo $rsm['nama_customer']; ?></td>
                        </tr>
                        <tr>
                            <td>Cabang Invoice</td>
                            <td class="text-center">:</td>
                            <td><?php echo $rsm['nama_cabang']; ?></td>
                        </tr>
                        <tr>
                            <td>Marketing</td>
                            <td class="text-center">:</td>
                            <td><?php echo $rsm['fullname']; ?></td>
                        </tr>
                        <tr>
                            <td>PIC Customer</td>
                            <td class="text-center">:</td>
                            <td><?php echo $rsm['gelar'] . ' ' . $rsm['nama_up'];
                                echo ($rsm['jabatan_up']) ? " (<i>" . $rsm['jabatan_up'] . "</i>)" : ""; ?></td>
                        </tr>
                        <tr>
                            <td>Alamat Korespondensi</td>
                            <td class="text-center">:</td>
                            <td><?php echo $rsm['alamat_up']; ?></td>
                        </tr>
                        <tr>
                            <td>Telepon</td>
                            <td class="text-center">:</td>
                            <td><?php echo $rsm['telp_up']; ?></td>
                        </tr>
                        <tr>
                            <td>Fax</td>
                            <td class="text-center">:</td>
                            <td><?php echo $rsm['fax_up']; ?></td>
                        </tr>
                        <tr>
                            <td>TOP Customer</td>
                            <td class="text-center">:</td>
                            <td><?php echo $arrPayment[$rsm['jenis_payment']]; ?></td>
                        </tr>
                    </table>

                    <hr style="margin:10px 0px; border-top:4px double #ddd;" />

                    <table class="table no-border">
                        <tr>
                            <td width="180">Masa berlaku harga</td>
                            <td width="10" class="text-center">:</td>
                            <td><?php echo tgl_indo($rsm['masa_awal']) . " - " . tgl_indo($rsm["masa_akhir"]); ?></td>
                        </tr>
                        <tr>
                            <td>Harga Jual</td>
                            <td class="text-center">:</td>
                            <td><?php echo number_format($rsm['harga_normal']); ?></td>
                        </tr>
                        <?php if ($sesrole == 21) { ?>
                            <tr>
                                <td>Harga Terendah CEO</td>
                                <td class="text-center">:</td>
                                <td><?php echo number_format($rsm['harga_ceo']); ?></td>
                            </tr>
                        <?php }
                        if ($sesrole == 3 || $sesrole == 21) { ?>
                            <tr>
                                <td>Harga Terendah COO</td>
                                <td class="text-center">:</td>
                                <td><?php echo number_format($rsm['harga_coo']); ?></td>
                            </tr>
                        <?php }
                        if ($sesrole == 6 || $sesrole == 3 || $sesrole == 21) { ?>
                            <tr>
                                <td>Harga Terendah OM</td>
                                <td class="text-center">:</td>
                                <td><?php echo number_format($rsm['harga_om']); ?></td>
                            </tr>
                        <?php }
                        if ($sesrole == 7 || $sesrole == 6 || $sesrole == 3 || $sesrole == 21) { ?>
                            <tr>
                                <td>Harga Terendah BM</td>
                                <td class="text-center">:</td>
                                <td><?php echo number_format($rsm['harga_sm']); ?></td>
                            </tr>
                        <?php } ?>
                    </table>

                    <hr style="margin:10px 0px; border-top:4px double #ddd;" />

                    <table class="table no-border">
                        <tr>
                            <td width="180">Nomor Referensi</td>
                            <td width="10" class="text-center">:</td>
                            <td><?php echo $rsm['nomor_surat']; ?></td>
                        </tr>
                        <tr>
                            <td>Area</td>
                            <td class="text-center">:</td>
                            <td><?php echo $rsm['nama_area']; ?></td>
                        </tr>
                        <tr>
                            <td>Produk</td>
                            <td class="text-center">:</td>
                            <td><?php echo $rsm['merk_dagang']; ?></td>
                        </tr>
                        <tr>
                            <td>Volume</td>
                            <td class="text-center">:</td>
                            <td><?php echo number_format($rsm['volume_tawar']) . " Liter"; ?></td>
                        </tr>
                        <tr>
                            <td>Order Method</td>
                            <td class="text-center">:</td>
                            <td><?php echo $rsm['method_order'] . " hari sebelum pickup"; ?></td>
                        </tr>
                        <?php if ($rsm['perhitungan'] == 1) { ?>
                            <tr>
                                <td>Harga perliter</td>
                                <td class="text-center">:</td>
                                <td><?php echo number_format($rsm['harga_dasar']); ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td>Refund</td>
                            <td class="text-center">:</td>
                            <td><?php echo ($rsm['refund_tawar']) ? number_format($rsm['refund_tawar']) : '-'; ?></td>
                        </tr>
                        <tr>
                            <td>Keterangan Harga</td>
                            <td class="text-center">:</td>
                            <td><?php echo ($rsm['ket_harga']) ? $rsm['ket_harga'] : '-'; ?></td>
                        </tr>
                    </table>
                </div>
                <?php
                $breakdown = true;
                if ($breakdown) {
                    $nom = 0;
                ?>
                    <p style="margin:0px 5px 5px;">Dengan rincian sebagai berikut:</p>
                    <div class="clearfix">
                        <div class="col-sm-10 col-md-8">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <th class="text-center" width="10%">NO</th>
                                        <th class="text-center" width="40%">RINCIAN</th>
                                        <th class="text-center" width="10%">NILAI</th>
                                        <th class="text-center" width="40%">HARGA</th>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($rincian as $arr1) {
                                            $nom++;
                                            $cetak = $arr1['rinci'] || true;
                                            $nilai = $arr1['nilai'];
                                            $biaya = ($arr1['biaya']) ? number_format($arr1['biaya']) : '';
                                            $jenis = $arr1['rincian'];
                                            if ($cetak) {
                                        ?>
                                                <tr>
                                                    <td class="text-center"><?php echo $nom; ?></td>
                                                    <td class="text-left"><?php echo $jenis; ?></td>
                                                    <td class="text-right"><?php echo ($nilai ? $nilai . " %" : ""); ?></td>
                                                    <td class="text-right"><?php echo $biaya; ?></td>
                                                </tr>
                                        <?php }
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php } else if ($rsm['perhitungan'] == 2) { ?>
                    <p style="margin:0px 5px 5px;">Perhitungan menggunakan formula</p>
                    <?php if (count($formula) > 0) {
                        $nom = 0; ?>
                        <div class="clearfix">
                            <div class="col-sm-8">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <?php foreach ($formula as $arr1) {
                                            $nom++; ?>
                                            <tr>
                                                <td width="10%" class="text-center"><?php echo $nom; ?></td>
                                                <td width="90%"><?php echo $arr1; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                <?php }
                } ?>
                <hr style="margin:0px 0px 10px; color:#ccc;" />
                <div class="form-group clearfix">
                    <div class="col-sm-8">
                        <label>Status Approval</label>
                        <div class="form-control" style="min-height:30px; height:auto; font-size:12px;">
                            <?php
                            $status = '';
                            $arrPosisi  = array(1 => "SPV", "BM", "BM", "OM", "COO", "CEO");
                            $arrSetuju  = array(1 => "Disetujui", "Ditolak");
                            $arrAlasan    = array(1 => "spv_mkt_summary", "sm_mkt_summary", "sm_wil_summary", "om_summary", "coo_summary", "ceo_summary");

                            if ($rsm['flag_approval'] == 0 && $rsm['flag_disposisi'] == 0) {
                                $status = "Terdaftar";
                            } else if ($rsm['flag_approval'] == 0 && $rsm['flag_disposisi']) {
                                if ($rsm['flag_disposisi'] > 1 && $rsm['flag_disposisi'] < 4) {
                                    $status = "Verifikasi " . $arrPosisi[$rsm['flag_disposisi']] . " " . $rsm['nama_cabang'];
                                } else {
                                    $status = "Verifikasi " . $arrPosisi[$rsm['flag_disposisi']];
                                }
                            } else if ($rsm['flag_approval']) {
                                $picApproval = "";
                                if ($rsm['flag_disposisi'] == '3') $picApproval = $rsm['sm_wil_pic'];
                                else if ($rsm['flag_disposisi'] == '4') $picApproval = $rsm['om_pic'];
                                else if ($rsm['flag_disposisi'] == '5') $picApproval = $rsm['coo_pic'];
                                else if ($rsm['flag_disposisi'] == '6') $picApproval = $rsm['ceo_pic'];

                                $alasanDitolak = "";
                                if ($rsm['flag_approval'] == '2') {
                                    $alasanDitolak = "<br /><br /><b><u>Alasan Penolakan</u></b>";
                                    $alasanDitolak .= "<br />" . ($rsm[$arrAlasan[$rsm['flag_disposisi']]] ? nl2br($rsm[$arrAlasan[$rsm['flag_disposisi']]]) : "-") . "<br />";
                                }

                                if ($rsm['flag_disposisi'] > 1 && $rsm['flag_disposisi'] < 4) {
                                    $status = $arrSetuju[$rsm['flag_approval']] . " " . $arrPosisi[$rsm['flag_disposisi']] . " " . $rsm['nama_cabang'];
                                    $status .= $alasanDitolak;
                                    $status .= "<br /><i>" . ($picApproval ? $picApproval . " - " : "");
                                    $status .= ($rsm['tgl_approval'] ? date("d/m/Y H:i:s", strtotime($rsm['tgl_approval'])) . " WIB" : "") . "</i>";
                                } else {
                                    $status = $arrSetuju[$rsm['flag_approval']] . " " . $arrPosisi[$rsm['flag_disposisi']];
                                    $status .= $alasanDitolak;
                                    $status .= "<br /><i>" . ($picApproval ? $picApproval . " - " : "");
                                    $status .= ($rsm['tgl_approval'] ? date("d/m/Y H:i:s", strtotime($rsm['tgl_approval'])) . " WIB" : "") . "</i>";
                                }
                            }
                            echo $status;
                            ?>
                        </div>
                    </div>
                </div>
                <div class="form-group clearfix">
                    <div class="col-sm-8">
                        <label>Catatan Marketing/Key Account</label>
                        <div class="form-control" style="min-height:90px; height:auto; font-size:12px;"><?php echo ($rsm['catatan'] ? nl2br($rsm['catatan']) : '&nbsp;'); ?></div>
                    </div>
                </div>
            </div>
        </div>
<?php $_param++;
    }
} ?>
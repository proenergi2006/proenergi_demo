<?php
$simpan  = false;
foreach ($rsms as $key => $rsm) {
    if ($rsm['id_penawaran'] == $idk) {
        $sesrole = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
        $rincian = json_decode($rsm['detail_rincian'], true);
        $formula = json_decode($rsm['detail_formula'], true);
        $sesrole = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
        $seswil  = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
        $notespv = ($rsm['spv_mkt_summary']);
        $notesm1 = ($rsm['sm_mkt_summary']);
        $notesm2 = ($rsm['sm_wil_summary']);
        $noteopm = ($rsm['om_summary']);
        $noteceo = ($rsm['ceo_summary']);
        $arrStat = array(1 => "Disetujui", "Ditolak");

        $rsm['harga_normal'] = ($rsm['harga_normal'] ? $rsm['harga_normal'] : $rsm['harga_normal_new']);
        $rsm['harga_sm'] = ($rsm['harga_sm'] ? $rsm['harga_sm'] : $rsm['harga_sm_new']);
        $rsm['harga_om'] = ($rsm['harga_om'] ? $rsm['harga_om'] : $rsm['harga_om_new']);
        $rsm['harga_coo'] = ($rsm['harga_coo'] ? $rsm['harga_coo'] : $rsm['harga_coo_new']);
        $rsm['harga_ceo'] = ($rsm['harga_ceo'] ? $rsm['harga_ceo'] : $rsm['harga_ceo_new']);

        $arrKondInd    = array(0 => '', 1 => "Setelah Invoice diterima", "Setelah pengiriman", "Setelah loading");
        $arrKondEng = array(0 => '', 1 => "After Invoice Receive", "After Delivery", "After Loading");
        $jenis_net  = $rsm['jenis_net'];
        $arrPayment = array("CREDIT" => "CREDIT " . $rsm['jangka_waktu'] . " hari " . $arrKondInd[$jenis_net], "CBD" => "CBD (Cash Before Delivery)", "COD" => "COD (Cash On Delivery)");

        $tmp_calc     = (json_decode($rsm['kalkulasi_oa'], true) === NULL) ? array(1) : json_decode($rsm['kalkulasi_oa'], true);
        $calcoa1     = ($tmp_calc[0]['transportir'] ? $tmp_calc[0]['transportir'] : '');
        $calcoa2     = ($tmp_calc[0]['wiloa_po'] ? $tmp_calc[0]['wiloa_po'] : '');
        $calcoa3     = ($tmp_calc[0]['voloa_po'] ? $tmp_calc[0]['voloa_po'] : 'N/A');
        $calcoa4     = ($tmp_calc[0]['ongoa_po'] ? $tmp_calc[0]['ongoa_po'] : 'N/A');
        $hargadasar = 0;

?>
        <form action="<?php echo ACTION_CLIENT . '/penawaran-approval.php'; ?>" id="gform" name="gform" class="form-horizontal" method="post" role="form">
            <div class="form-group row">
                <div class="col-sm-8">
                    <div class="table-responsive">
                        <input type="hidden" name="keterangan_pengajuan" id="keterangan_pengajuan" value="<?php echo $rsm['catatan']; ?>" />
                        <input type="hidden" name="volume" id="volume" value="<?php echo $rsm['volume_tawar']; ?>" />

                        <table class="table table-bordered table-summary">
                            <tbody>
                                <tr>
                                    <td colspan="2" style="background-color:#f4f4f4; vertical-align:middle; padding:8px 5px;">
                                        <b>PRICELIST</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Masa Berlaku Harga</td>
                                    <td><?php echo tgl_indo($rsm['masa_awal']) . " - " . tgl_indo($rsm["masa_akhir"]); ?></td>
                                </tr>
                                <tr>
                                    <td>Rekomendasi Harga Dasar</td>
                                    <td><?php echo number_format($rsm['harga_normal']); ?></td>
                                </tr>
                                <tr>
                                    <td>Rekomendasi Ongkos Angkut</td>
                                    <td><?php echo number_format($rsm['oa_kirim']); ?></td>
                                </tr>
                                <?php if ($sesrole == 21) { ?>
                                    <tr>
                                        <td>Harga Tier III CEO</td>
                                        <td><?php echo number_format($rsm['harga_ceo']); ?></td>
                                    </tr>
                                <?php }
                                if ($sesrole == 3 || $sesrole == 21) { ?>
                                    <tr>
                                        <td>Harga Tier III COO</td>
                                        <td><?php echo number_format($rsm['harga_coo']); ?></td>
                                    </tr>
                                <?php }
                                if ($sesrole == 6 || $sesrole == 3 || $sesrole == 21) { ?>
                                    <tr>
                                        <td>Harga Tier II OM</td>
                                        <td><?php echo number_format($rsm['harga_om']); ?></td>
                                    </tr>
                                <?php }
                                if ($sesrole == 7 || $sesrole == 6 || $sesrole == 3 || $sesrole == 21) { ?>
                                    <tr>
                                        <td>Harga Tier I BM</td>
                                        <td><?php echo number_format($rsm['harga_sm']); ?></td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td colspan="2" style="background-color:#f4f4f4; vertical-align:middle; padding:8px 5px;">
                                        <b>SUMMARY</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="200">Nama Customer</td>
                                    <td><?php echo $rsm['nama_customer']; ?></td>
                                </tr>
                                <tr>
                                    <td>Volume</td>
                                    <td><?php echo number_format($rsm['volume_tawar']) . " Liter"; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Refund</td>
                                    <td><?php echo ($rsm['refund_tawar']) ? number_format($rsm['refund_tawar']) : '-'; ?></td>
                                </tr>
                                <tr>
                                    <td>Other Cost</td>
                                    <td><?php echo ($rsm['other_cost']) ? number_format($rsm['other_cost']) : '-'; ?></td>
                                </tr>

                                <?php
                                $cnt_rincian = 0;
                                foreach ($rincian as $arr1) {
                                    $cnt_rincian++;
                                    $biaya = ($arr1['biaya']) ? $arr1['biaya'] : '';

                                    if ($cnt_rincian == '1') {
                                        $hargadasar = $arr1['biaya'];
                                        echo '<input type="hidden" name="harga_dasar" id="harga_dasar" value="' . $arr1['biaya'] . '" />';
                                    } else if ($cnt_rincian == '2') {
                                        echo '<input type="hidden" name="oa_kirim" id="oa_kirim" value="' . $arr1['biaya'] . '" />';
                                    } else if ($cnt_rincian == '3') {
                                        echo '<input type="hidden" name="ppn" id="ppn" value="' . $arr1['biaya'] . '" />';
                                    } else if ($cnt_rincian == '4') {
                                        echo '<input type="hidden" name="pbbkb" id="pbbkb" value="' . $arr1['biaya'] . '" />';
                                    }
                                }
                                ?>


                                <?php if ($rsm['perhitungan'] == 1) { ?>
                                    <tr>
                                        <td>Harga Penawaran</td>
                                        <td><?php echo number_format($rsm['harga_dasar']); ?>
                                        </td>
                                    </tr>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="2">Perhitungan menggunakan formula</td>
                                    </tr>
                                <?php } ?>


                                <tr>
                                    <td>Metode Pengiriman</td>
                                    <td><?php echo $rsm['metode']; ?></td>
                                </tr>
                                <tr>
                                    <td>Lokasi Pengiriman</td>
                                    <td><?php echo $rsm['lok_kirim']; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <?php
                    $breakdown = false;
                    foreach ($rincian as $temp) {
                        $breakdown = $breakdown || 1;
                    }
                    if ($breakdown && $rsm['perhitungan'] == 1) {
                        $nom = 0;
                        $oa_penawaran = 0;
                    ?>
                        <p style="margin:10px 0px;">Dengan rincian sebagai berikut:</p>

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <th class="text-center" width="50">NO</th>
                                            <th class="text-center" width="">RINCIAN</th>
                                            <th class="text-center" width="100">NILAI</th>
                                            <th class="text-center" width="130">HARGA</th>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($rincian as $arr1) {
                                                $nom++;
                                                $cetak = 1;
                                                $nilai = $arr1['nilai'];
                                                $biaya = ($arr1['biaya']) ? $arr1['biaya'] : '';
                                                $biaya = ($rsm['pembulatan']) ? number_format($arr1['biaya']) : number_format($arr1['biaya'], 2);
                                                $jenis = $arr1['rincian'];
                                                if ($cetak) {
                                                    if ($nom == '2') $oa_penawaran = $arr1['biaya'];

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
                                    <?php if ($rsm['pembulatan']) { ?>
                                        <p style="margin:0px 0px 5px;"><i>*) Perhitungan menggunakan pembulatan</i></p>
                                    <?php } else { ?>
                                        <p style="margin:0px 0px 5px;"><i>*) Perhitungan tidak menggunakan pembulatan</i></p>
                                    <?php } ?>

                                    <?php if ($rsm['gabung_oa']) { ?>
                                        <p style="margin:0px 0px 15px;"><i>*) Cetakan Harga Dasar Termasuk Ongkos Angkut</i></p>
                                    <?php } else { ?>
                                        <p style="margin:0px 0px 15px;"><i>*) Cetakan Harga Dasar Tidak Termasuk Ongkos Angkut</i></p>
                                    <?php } ?>



                                </div>
                            </div>

                        </div>

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <th class="text-center" width="50">NO</th>
                                            <th class="text-center" width="">RINCIAN</th>
                                            <th class="text-center" width="100">NILAI</th>
                                            <th class="text-center" width="130">HARGA</th>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center">1</td>
                                                <td class="text-left">Harga Tier</td>
                                                <td class="text-right">-</td>
                                                <td class="text-right"><b><?php echo number_format($rsm['harga_tier']); ?></b></td>
                                            </tr>

                                            <tr>
                                                <td class="text-center">2</td>
                                                <td class="text-left">Tier</td>
                                                <td class="text-right"><b><?php echo $rsm['tier']; ?></b></td>
                                                <td class="text-right"></td>
                                            </tr>

                                            <?php
                                            $totalrefund = $rsm['refund_tawar'] * $rsm['volume_tawar'];
                                            $totalother = $rsm['other_cost'] * $rsm['volume_tawar'];
                                            ?>

                                            <tr>
                                                <td class="text-center">3</td>
                                                <td class="text-left">Total Refund</td>
                                                <td class="text-right"><?php echo number_format($rsm['refund_tawar']); ?></td>
                                                <td class="text-right <?php echo ($totalrefund > 1000000) ? 'text-danger' : 'text-success'; ?>">
                                                    <?php echo number_format($totalrefund); ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="text-center">4</td>
                                                <td class="text-left">Total Other Cost</td>
                                                <td class="text-right"><?php echo number_format($rsm['other_cost']); ?></td>
                                                <td class="text-right <?php echo ($totalother > 1000000) ? 'text-danger' : 'text-success'; ?>">
                                                    <?php echo number_format($totalother); ?>
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>


                                </div>
                            </div>

                        </div>





                        <?php if (!empty($rsmOtherCost) && is_array($rsmOtherCost)) : ?>
                            <tr>
                                <td>Keterangan Other Cost</td>
                                <td class="text-center">:</td>
                                <td>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>KETERANGAN</th>
                                                <th>NOMINAL</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($rsmOtherCost as $detail) : ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($detail['keterangan'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?php echo number_format($detail['nominal']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        <?php else : ?>
                        <?php endif; ?>

                    <?php } ?>

                    <?php
                    $need_ceo = 0;
                    if ($oa_penawaran < $rsm['oa_kirim'] && $rsm['metode'] == 'Franco') {
                        $need_ceo = 1;
                        echo '<p class="text-red" style="font-size:14px;"><b>OA PENAWARAN LEBIH KECIL DARI OA YANG DIREKOMENDASIKAN</b></p>';
                    }
                    if ($rsm['other_cost'] > 50) {
                        $need_ceo = 1;
                        echo '<p class="text-red" style="font-size:14px;"><b>OTHER COST MELEBIHI DARI BATAS OTHER COST YANG DI TETAPKAN</b></p>';
                    }
                    if ($rsm['refund_tawar'] > 70 || $totalrefund > 1000000) {
                        $need_ceo = 1;
                        echo '<p class="text-red" style="font-size:14px;"><b>REFUND MELEBIHI DARI BATAS REFUND YANG DI TETAPKAN</b></p>';
                    }
                    ?>

                </div>
            </div>

            <?php
            if ($sesrole == 7) {
                if ($rsm['spv_mkt_result'] && $rsm['spv_mkt_pic'] && $rsm['spv_mkt_tanggal']) {
                    echo '
                <div class="form-group row">
                    <div class="col-sm-8">
                        <label>Catatan Supervisor Marketing</label>
                        <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                            ' . nl2br($notespv) . '
                            <p style="margin:10px 0 0; font-size:12px;">
                                <i>' . ($rsm['spv_mkt_pic'] ? $rsm['spv_mkt_pic'] . ' - ' : '&nbsp;') .
                        ($rsm['spv_mkt_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['spv_mkt_tanggal'])) . ' WIB' : '') . '</i>
                            </p>
                        </div>
                    </div>
                </div>';
                }

                /*if($rsm['id_cabang'] != $seswil && !$rsm['sm_mkt_result'] && $rsm['flag_disposisi'] <= 2){
                $simpan = true; 
                echo '
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group form-group-sm">
                            <label class="control-label col-md-12">Catatan Branch Manager Marketing</label>
                            <div class="col-md-12">
                                <textarea name="sm_mkt_summary" id="sm_mkt_summary" class="form-control" style="height:90px;" required></textarea>
                                <input type="hidden" name="approval" id="approval" value="1" />
                                <input type="hidden" name="is_mkt" id="is_mkt" value="1" />
                                <input type="hidden" name="tmp_cabang" id="tmp_cabang" value="'.$rsm['id_cabang'].'" />
                            </div>
                        </div>
                    </div>
                </div>';
            } else if($rsm['sm_mkt_result'] && $rsm['sm_mkt_pic'] && $rsm['sm_mkt_tanggal']){
                echo '
                <div class="row">
                    <div class="col-md-8">
                        <label>Catatan Branch Manager Marketing</label>
                        <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                            '.nl2br($notesm1).'
                            <p style="margin:10px 0 0; font-size:12px;">
                                <i>'.($rsm['sm_mkt_pic'] ? $rsm['sm_mkt_pic'].' - ' : '&nbsp;').
                                ($rsm['sm_mkt_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['sm_mkt_tanggal'])).' WIB' : '').'</i>
                            </p>
                        </div>
                    </div>
                </div>';
            }*/

                if ($rsm['flag_disposisi'] == 3 && ($rsm['id_cabang'] == $seswil) && !$rsm['sm_wil_result']) {
                    $simpan = true;
                    $elm_approval =
                        '<div class="radio">
					<label class="rtl"><input type="radio" name="extend" id="extend1" value="1"   /> Ya</label>
				</div>
				<div class="radio">
					<label class="rtl"><input type="radio"  name="extend" id="extend2" value="2" checked required  /> Tidak</label>
				</div>';
                    if ($need_ceo) {
                        $elm_approval =
                            '<div class="radio">
						<label class="rtl"><input type="radio" name="extend" id="extend1" value="1" checked required /> Ya</label>
					</div>';
                    }

                    echo '
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group form-group-sm">
                            <label class="control-label col-md-12">Catatan Branch Manager</label>
                            <div class="col-md-12">
                                <textarea name="sm_wil_summary" id="sm_wil_summary" class="form-control" style="height:90px;" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group form-group-sm">
                            <label class="control-label col-md-12">Persetujuan</label>
                            <div class="col-md-12">
                                <div class="radio">
                                    <label class="rtl"><input type="radio" name="approval" id="approval1" value="1" required /> Ya</label>
                                </div>
                                <div class="radio">
                                    <label class="rtl"><input type="radio" name="approval" id="approval2" value="2" required /> Tidak</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" hidden>
                        <div class="form-group form-group-sm">
                            <label class="control-label col-md-12">Diteruskan ke OM</label>
                            <div class="col-md-12">
                                ' . $elm_approval . '
                            </div>
                        </div>
                    </div>
                </div>';
                } else if ($rsm['sm_wil_result'] && $rsm['sm_wil_pic'] && $rsm['sm_wil_tanggal']) {
                    echo '
                <div class="form-group row">
                    <div class="col-md-8">
                        <label>Catatan Branch Manager</label>
                        <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                            ' . nl2br($notesm2) . '
                            <p style="margin:10px 0 0; font-size:12px;">
                                <i>' . ($rsm['sm_wil_pic'] ? $rsm['sm_wil_pic'] . ' - ' : '&nbsp;') .
                        ($rsm['sm_wil_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['sm_wil_tanggal'])) . ' WIB' : '') . '</i>
                            </p>
                        </div>
                    </div>
                </div>';
                }

                if ($rsm['om_result'] && $rsm['om_pic'] && $rsm['om_tanggal']) {
                    echo '
                <div class="form-group row">
                    <div class="col-sm-8">
                        <label>Catatan Operation Manager</label>
                        <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                            ' . nl2br($noteopm) . '
                            <p style="margin:10px 0 0; font-size:12px;">
                                <i>' . ($rsm['om_pic'] ? $rsm['om_pic'] . ' - ' : '&nbsp;') .
                        ($rsm['om_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['om_tanggal'])) . ' WIB' : '') . '</i>
                            </p>
                        </div>
                    </div>
                </div>';
                }

                if ($rsm['coo_result'] && $rsm['coo_pic'] && $rsm['coo_tanggal']) {
                    echo '
                <div class="form-group row">
                    <div class="col-sm-8">
                        <label>Catatan COO</label>
                        <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                            ' . nl2br($rsm['coo_summary']) . '
                            <p style="margin:10px 0 0; font-size:12px;">
                                <i>' . ($rsm['coo_pic'] ? $rsm['coo_pic'] . ' - ' : '&nbsp;') .
                        ($rsm['coo_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['coo_tanggal'])) . ' WIB' : '') . '</i>
                            </p>
                        </div>
                    </div>
                </div>';
                }

                if ($rsm['ceo_result'] && $rsm['ceo_pic'] && $rsm['ceo_tanggal']) {
                    echo '
                <div class="form-group row">
                    <div class="col-sm-8">
                        <label>Catatan CEO</label>
                        <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                            ' . nl2br($noteceo) . '
                            <p style="margin:10px 0 0; font-size:12px;">
                                <i>' . ($rsm['ceo_pic'] ? $rsm['ceo_pic'] . ' - ' : '&nbsp;') .
                        ($rsm['ceo_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['ceo_tanggal'])) . ' WIB' : '') . '</i>
                            </p>
                        </div>
                    </div>
                </div>';
                }
            }

            /* BUAT APPROVAL OM */
            if ($sesrole == 6) {
                if ($rsm['spv_mkt_result'] && $rsm['spv_mkt_pic'] && $rsm['spv_mkt_tanggal']) {
                    echo '
                <div class="form-group row">
                    <div class="col-sm-8">
                        <label>Catatan Supervisor Marketing</label>
                        <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                            ' . nl2br($notespv) . '
                            <p style="margin:10px 0 0; font-size:12px;">
                                <i>' . ($rsm['spv_mkt_pic'] ? $rsm['spv_mkt_pic'] . ' - ' : '&nbsp;') .
                        ($rsm['spv_mkt_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['spv_mkt_tanggal'])) . ' WIB' : '') . '</i>
                            </p>
                        </div>
                    </div>
                </div>';
                }

                if ($rsm['sm_mkt_result'] && $rsm['sm_mkt_pic'] && $rsm['sm_mkt_tanggal']) {
                    echo '
                <div class="form-group row">
                    <div class="col-sm-8">
                        <label>Catatan Branch Manager Marketing</label>
                        <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                            ' . nl2br($notesm1) . '
                            <p style="margin:10px 0 0; font-size:12px;">
                                <i>' . ($rsm['sm_mkt_pic'] ? $rsm['sm_mkt_pic'] . ' - ' : '&nbsp;') .
                        ($rsm['sm_mkt_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['sm_mkt_tanggal'])) . ' WIB' : '') . '</i>
                            </p>
                        </div>
                    </div>
                </div>';
                }

                if ($rsm['sm_wil_result'] && $rsm['sm_wil_pic'] && $rsm['sm_wil_tanggal']) {
                    echo '
                <div class="form-group row">
                    <div class="col-sm-8">
                        <label>Catatan Branch Manager</label>
                        <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                            ' . nl2br($notesm2) . '
                            <p style="margin:10px 0 0; font-size:12px;">
                                <i>' . ($rsm['sm_wil_pic'] ? $rsm['sm_wil_pic'] . ' - ' : '&nbsp;') .
                        ($rsm['sm_wil_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['sm_wil_tanggal'])) . ' WIB' : '') . '</i>
                            </p>
                        </div>
                    </div>
                </div>';
                }

                if ($rsm['flag_disposisi'] == 4 && !$rsm['om_result'] && $rsm['flag_approval'] == 0) {
                    $simpan = true;
                    // Jika tier III, tambahkan elemen radio khusus
                    if ($rsm['tier'] == 'III') {
                        $elm_approval =   '<div class="radio">
		        <label class="rtl"><input type="radio" name="extend" id="extend1" value="1" required /> Ya</label>
	        </div>
            <div class="radio">
		        <label class="rtl"><input type="radio" name="extend" id="extend2" value="2" required /> Tidak</label>
	        </div>
            
            ';
                    } else {
                        // Jika bukan tier III, gunakan kondisi default
                        $elm_approval =
                            '<div class="radio">
	            <label class="rtl"><input type="radio" name="extend" id="extend1" value="1" ' . ($hargadasar < $rsm['harga_om'] ? 'checked' : '') . ' required /> Ya</label>
            </div>
            <div class="radio">
                <label class="rtl"><input type="radio" name="extend" id="extend2" value="2" ' . ($hargadasar >= $rsm['harga_om'] ? 'checked' : '') . ' required /> Tidak</label>
            </div>';
                    }

                    //         // Jika membutuhkan approval CEO, maka nilai sebelumnya harus ditimpa
                    //         if ($need_ceo) {
                    //             $elm_approval =
                    //                 '<div class="radio">
                    //     <label class="rtl"><input type="radio" name="extend" id="extend1" value="1" checked required /> Ya</label>
                    // </div>';
                    //         }

                    echo '
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group form-group-sm">
                            <label class="control-label col-md-12">Catatan Operation Manager</label>
                            <div class="col-md-12">
                                <textarea name="om_summary" id="om_summary" class="form-control" style="height:90px;" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group form-group-sm">
                            <label class="control-label col-md-12">Persetujuan</label>
                            <div class="col-md-12">
                                <div class="radio">
                                    <label class="rtl"><input type="radio" name="approval" id="approval1" value="1" required /> Ya</label>
                                </div>
                                <div class="radio">
                                    <label class="rtl"><input type="radio" name="approval" id="approval2" value="2" required /> Tidak</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group form-group-sm">
                            <label class="control-label col-md-12">Diteruskan ke CEO</label>
                            <div class="col-md-12">
                                ' . $elm_approval . '
                            </div>
                        </div>
                    </div>
                </div>';
                } else if ($rsm['om_result'] && $rsm['om_pic'] && $rsm['om_tanggal']) {
                    echo '
                <div class="form-group row">
                    <div class="col-md-8">
                        <label>Catatan Operation Manager</label>
                        <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                            ' . nl2br($noteopm) . '
                            <p style="margin:10px 0 0; font-size:12px;">
                                <i>' . ($rsm['om_pic'] ? $rsm['om_pic'] . ' - ' : '&nbsp;') .
                        ($rsm['om_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['om_tanggal'])) . ' WIB' : '') . '</i>
                            </p>
                        </div>
                    </div>
                </div>';
                }

                if ($rsm['coo_result'] && $rsm['coo_pic'] && $rsm['coo_tanggal']) {
                    echo '
                <div class="form-group row">
                    <div class="col-sm-8">
                        <label>Catatan COO</label>
                        <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                            ' . nl2br($rsm['coo_summary']) . '
                            <p style="margin:10px 0 0; font-size:12px;">
                                <i>' . ($rsm['coo_pic'] ? $rsm['coo_pic'] . ' - ' : '&nbsp;') .
                        ($rsm['coo_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['coo_tanggal'])) . ' WIB' : '') . '</i>
                            </p>
                        </div>
                    </div>
                </div>';
                }

                if ($rsm['ceo_result'] && $rsm['ceo_pic'] && $rsm['ceo_tanggal']) {
                    echo '
                <div class="form-group row">
                    <div class="col-sm-8">
                        <label>Catatan CEO</label>
                        <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                            ' . nl2br($noteceo) . '
                            <p style="margin:10px 0 0; font-size:12px;">
                                <i>' . ($rsm['ceo_pic'] ? $rsm['ceo_pic'] . ' - ' : '&nbsp;') .
                        ($rsm['ceo_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['ceo_tanggal'])) . ' WIB' : '') . '</i>
                            </p>
                        </div>
                    </div>
                </div>';
                }
            }

            /* BUAT APPROVAL COO */
            if ($sesrole == 3) {
                if ($rsm['spv_mkt_result'] && $rsm['spv_mkt_pic'] && $rsm['spv_mkt_tanggal']) {
                    echo '
                <div class="form-group row">
                    <div class="col-sm-8">
                        <label>Catatan Supervisor Marketing</label>
                        <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                            ' . nl2br($notespv) . '
                            <p style="margin:10px 0 0; font-size:12px;">
                                <i>' . ($rsm['spv_mkt_pic'] ? $rsm['spv_mkt_pic'] . ' - ' : '&nbsp;') .
                        ($rsm['spv_mkt_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['spv_mkt_tanggal'])) . ' WIB' : '') . '</i>
                            </p>
                        </div>
                    </div>
                </div>';
                }

                if ($rsm['sm_mkt_result'] && $rsm['sm_mkt_pic'] && $rsm['sm_mkt_tanggal']) {
                    echo '
                <div class="form-group row">
                    <div class="col-sm-8">
                        <label>Catatan Branch Manager Marketing</label>
                        <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                            ' . nl2br($notesm1) . '
                            <p style="margin:10px 0 0; font-size:12px;">
                                <i>' . ($rsm['sm_mkt_pic'] ? $rsm['sm_mkt_pic'] . ' - ' : '&nbsp;') .
                        ($rsm['sm_mkt_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['sm_mkt_tanggal'])) . ' WIB' : '') . '</i>
                            </p>
                        </div>
                    </div>
                </div>';
                }

                if ($rsm['sm_wil_result'] && $rsm['sm_wil_pic'] && $rsm['sm_wil_tanggal']) {
                    echo '
                <div class="form-group row">
                    <div class="col-sm-8">
                        <label>Catatan Branch Manager</label>
                        <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                            ' . nl2br($notesm2) . '
                            <p style="margin:10px 0 0; font-size:12px;">
                                <i>' . ($rsm['sm_wil_pic'] ? $rsm['sm_wil_pic'] . ' - ' : '&nbsp;') .
                        ($rsm['sm_wil_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['sm_wil_tanggal'])) . ' WIB' : '') . '</i>
                            </p>
                        </div>
                    </div>
                </div>';
                }

                if ($rsm['om_result'] && $rsm['om_pic'] && $rsm['om_tanggal']) {
                    echo '
                <div class="form-group row">
                    <div class="col-sm-8">
                        <label>Catatan Operation Manager</label>
                        <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                            ' . nl2br($noteopm) . '
                            <p style="margin:10px 0 0; font-size:12px;">
                                <i>' . ($rsm['om_pic'] ? $rsm['om_pic'] . ' - ' : '&nbsp;') .
                        ($rsm['om_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['om_tanggal'])) . ' WIB' : '') . '</i>
                            </p>
                        </div>
                    </div>
                </div>';
                }

                if ($rsm['flag_disposisi'] == 5 && !$rsm['coo_result'] && $rsm['flag_approval'] == 0) {
                    $simpan = true;
                    echo '
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group form-group-sm">
                            <label class="control-label col-md-12">Catatan COO</label>
                            <div class="col-md-12">
                                <textarea name="coo_summary" id="coo_summary" class="form-control" style="height:90px;" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group form-group-sm">
                            <label class="control-label col-md-12">Persetujuan</label>
                            <div class="col-md-12">
                                <div class="radio">
                                    <label class="rtl"><input type="radio" name="approval" id="approval1" value="1" required /> Ya</label>
                                </div>
                                <div class="radio">
                                    <label class="rtl"><input type="radio" name="approval" id="approval2" value="2" required /> Tidak</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group form-group-sm">
                            <label class="control-label col-md-12">Diteruskan ke CEO</label>
                            <div class="col-md-12">
                                <div class="radio">
                                    <label class="rtl"><input type="radio" name="extend" id="extend1" value="1" ' . ($hargadasar < $rsm['harga_ceo'] ? 'checked' : '') . ' required /> Ya</label>
                                </div>
                                <div class="radio">
                                    <label class="rtl"><input type="radio" name="extend" id="extend2" value="2" ' . ($hargadasar >= $rsm['harga_ceo'] ? 'checked' : '') . ' required /> Tidak</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
                } else if ($rsm['coo_result'] && $rsm['coo_pic'] && $rsm['coo_tanggal']) {
                    echo '
                <div class="form-group row">
                    <div class="col-md-8">
                        <label>Catatan COO</label>
                        <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                            ' . nl2br($rsm['coo_summary']) . '
                            <p style="margin:10px 0 0; font-size:12px;">
                                <i>' . ($rsm['coo_pic'] ? $rsm['coo_pic'] . ' - ' : '&nbsp;') .
                        ($rsm['coo_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['coo_tanggal'])) . ' WIB' : '') . '</i>
                            </p>
                        </div>
                    </div>
                </div>';
                }

                if ($rsm['ceo_result'] && $rsm['ceo_pic'] && $rsm['ceo_tanggal']) {
                    echo '
                <div class="form-group row">
                    <div class="col-sm-8">
                        <label>Catatan CEO</label>
                        <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                            ' . nl2br($noteceo) . '
                            <p style="margin:10px 0 0; font-size:12px;">
                                <i>' . ($rsm['ceo_pic'] ? $rsm['ceo_pic'] . ' - ' : '&nbsp;') .
                        ($rsm['ceo_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['ceo_tanggal'])) . ' WIB' : '') . '</i>
                            </p>
                        </div>
                    </div>
                </div>';
                }
            }


            /* BUAT APPROVAL CEO */
            if ($sesrole == 21) {
                if ($rsm['spv_mkt_result'] && $rsm['spv_mkt_pic'] && $rsm['spv_mkt_tanggal']) {
                    echo '
                <div class="form-group row">
                    <div class="col-sm-8">
                        <label>Catatan Supervisor Marketing</label>
                        <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                            ' . nl2br($notespv) . '
                            <p style="margin:10px 0 0; font-size:12px;">
                                <i>' . ($rsm['spv_mkt_pic'] ? $rsm['spv_mkt_pic'] . ' - ' : '&nbsp;') .
                        ($rsm['spv_mkt_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['spv_mkt_tanggal'])) . ' WIB' : '') . '</i>
                            </p>
                        </div>
                    </div>
                </div>';
                }

                if ($rsm['sm_mkt_result'] && $rsm['sm_mkt_pic'] && $rsm['sm_mkt_tanggal']) {
                    echo '
                <div class="form-group row">
                    <div class="col-sm-8">
                        <label>Catatan Branch Manager Marketing</label>
                        <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                            ' . nl2br($notesm1) . '
                            <p style="margin:10px 0 0; font-size:12px;">
                                <i>' . ($rsm['sm_mkt_pic'] ? $rsm['sm_mkt_pic'] . ' - ' : '&nbsp;') .
                        ($rsm['sm_mkt_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['sm_mkt_tanggal'])) . ' WIB' : '') . '</i>
                            </p>
                        </div>
                    </div>
                </div>';
                }

                if ($rsm['sm_wil_result'] && $rsm['sm_wil_pic'] && $rsm['sm_wil_tanggal']) {
                    echo '
                <div class="form-group row">
                    <div class="col-sm-8">
                        <label>Catatan Branch Manager</label>
                        <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                            ' . nl2br($notesm2) . '
                            <p style="margin:10px 0 0; font-size:12px;">
                                <i>' . ($rsm['sm_wil_pic'] ? $rsm['sm_wil_pic'] . ' - ' : '&nbsp;') .
                        ($rsm['sm_wil_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['sm_wil_tanggal'])) . ' WIB' : '') . '</i>
                            </p>
                        </div>
                    </div>
                </div>';
                }

                if ($rsm['om_result'] && $rsm['om_pic'] && $rsm['om_tanggal']) {
                    echo '
                <div class="form-group row">
                    <div class="col-sm-8">
                        <label>Catatan Operation Manager</label>
                        <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                            ' . nl2br($noteopm) . '
                            <p style="margin:10px 0 0; font-size:12px;">
                                <i>' . ($rsm['om_pic'] ? $rsm['om_pic'] . ' - ' : '&nbsp;') .
                        ($rsm['om_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['om_tanggal'])) . ' WIB' : '') . '</i>
                            </p>
                        </div>
                    </div>
                </div>';
                }

                if ($rsm['coo_result'] && $rsm['coo_pic'] && $rsm['coo_tanggal']) {
                    echo '
                <div class="form-group row">
                    <div class="col-sm-8">
                        <label>Catatan COO</label>
                        <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                            ' . nl2br($rsm['coo_summary']) . '
                            <p style="margin:10px 0 0; font-size:12px;">
                                <i>' . ($rsm['coo_pic'] ? $rsm['coo_pic'] . ' - ' : '&nbsp;') .
                        ($rsm['coo_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['coo_tanggal'])) . ' WIB' : '') . '</i>
                            </p>
                        </div>
                    </div>
                </div>';
                }

                if ($rsm['flag_disposisi'] == 6 && !$rsm['ceo_result'] && $rsm['flag_approval'] == 0) {
                    $simpan = true;
                    echo '
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group form-group-sm">
                            <label class="control-label col-md-12">Catatan CEO</label>
                            <div class="col-md-12">
                                <textarea name="ceo_summary" id="ceo_summary" class="form-control" style="height:90px;" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group form-group-sm">
                            <label class="control-label col-md-12">Persetujuan</label>
                            <div class="col-md-12">
                                <div class="radio">
                                    <label class="rtl"><input type="radio" name="approval" id="approval1" value="1" required /> Ya</label>
                                </div>
                                <div class="radio">
                                    <label class="rtl"><input type="radio" name="approval" id="approval2" value="2" required /> Tidak</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
                } else if ($rsm['ceo_result'] && $rsm['ceo_pic'] && $rsm['ceo_tanggal']) {
                    echo '
                <div class="row">
                    <div class="col-md-8">
                        <label>Catatan CEO</label>
                        <div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
                            ' . nl2br($noteceo) . '
                            <p style="margin:10px 0 0; font-size:12px;">
                                <i>' . ($rsm['ceo_pic'] ? $rsm['ceo_pic'] . ' - ' : '&nbsp;') .
                        ($rsm['ceo_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['ceo_tanggal'])) . ' WIB' : '') . '</i>
                            </p>
                        </div>
                    </div>
                </div>';
                }
            }
            ?>


            <hr style="margin:15px 0px; border-top:4px double #ddd;" />

            <div style="margin-bottom:0px;">
                <input type="hidden" name="act" value="<?php echo $action; ?>" />
                <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                <input type="hidden" name="idk" value="<?php echo $idk; ?>" />
                <input type="hidden" id="tier_value" value="<?= $rsm['tier']; ?>">
                <?php if ($simpan === true) { ?>
                    <button type="submit" name="btnSbmt" id="btnSbmt" class="btn btn-primary jarak-kanan" style="min-width:90px;">
                        <i class="fa fa-save jarak-kanan"></i> Simpan</button>
                <?php } ?>


                <a class="btn btn-default" style="min-width:90px;" href="<?php echo BASE_URL_CLIENT . "/penawaran-approval.php"; ?>">
                    <i class="fa fa-reply jarak-kanan"></i> Batal</a>
            </div>
        </form>

        <!-- Tombol Mengambang -->
        <!-- <button id="open-widget" class="floating-circle">
            <span class="icon">📜</span>
            <span class="text">Data Penawaran</span>
        </button>

        <button id="open1-widget" class="floating-circle">
            <span class="icon">✔</span>
            <span class="text">History Penawaran</span>
        </button> -->




        <!-- 
        <div id="penawaran-data" class="floating-widget" style="display: none;">
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

                    <div class="box-header with-border">
                        Data Penawaran di tanggal : <?php echo date('d/m/Y', strtotime($rsm['created_time'])); ?> &nbsp; &nbsp; -
                        Status PO : <span style="color: <?php echo ($rsm['penawaran_status'] == 'YA' ? 'green' : 'red'); ?>;">
                            <?php echo $rsm['penawaran_status']; ?>
                        </span>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table no-border">
                                <tr>
                                    <td width="">Nama Customer</td>
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
                                    <td width="">Masa berlaku harga</td>
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
                                    <td width="">Nomor Referensi</td>
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
                                <div class="col-sm-12 col-md-12">
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
                                    <div class="col-sm-12">
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
                            <div class="col-sm-12">
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
                            <div class="col-sm-12">
                                <label>Catatan Marketing/Key Account</label>
                                <div class="form-control" style="min-height:90px; height:auto; font-size:12px;"><?php echo ($rsm['catatan'] ? nl2br($rsm['catatan']) : '&nbsp;'); ?></div>
                            </div>
                        </div>
                    </div>

            <?php $_param++;
                }
            } ?>
            <button id="close-widget" class="btn btn-primary btn-sm">
                <i class="fa fa-close"></i> Tutup
            </button>
        </div> -->





        <!-- <div id="history-data" class="floating-widget" style="display: none;">

            <div class="box box-primary">
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center" width="60">No</th>
                                    <th class="text-center" width="200">User Approval</th>
                                    <th class="text-center" width="">Detil Penawaran</th>
                                    <th class="text-center" width="100">Tgl Approval</th>
                                    <th class="text-center" width="100">Status</th>
                                    <th class="text-center" width="250">Catatan Disposisi</th>
                                    <th class="text-center" width="250">Catatan Marketing / Key Account</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sqlHist = "
                        select a.kd_approval,
                        case 
                            when a.result = '1' then 'Disetujui'
                            when a.result = '2' then 'Ditolak'
                            else ''
                        end as result, 
                        a.summary, a.id_user, DATE_FORMAT(a.tgl_approval, '%d-%m-%Y') tgl_approval, b.fullname, c.role_name,
                        harga_dasar, oa_kirim, ppn, pbbkb, keterangan_pengajuan, volume, a.tgl_approval as ordernya
                        from 
                        pro_approval_hist a 
                        join acl_user b on a.id_user = b.id_user 
                        join acl_role c on a.id_role = c.id_role 
                        where 1=1 and a.kd_approval = 'P001' and a.id_customer = '" . $idr . "' and a.id_penawaran = '" . $idk . "'
                        order by ordernya desc
                    ";
                                $resHist = $con->getResult($sqlHist);
                                $nomor = 0;
                                if (count($resHist) > 0) {
                                    foreach ($resHist as $arr1) {
                                        $nomor++;
                                        echo '
							<tr>
								<td class="text-right">' . $nomor . '</td>
								<td class="text-left"><p style="margin-bottom:5px;">' . $arr1['fullname'] . '</p><i>' . $arr1['role_name'] . '</i></td>
								<td class="text-left">
									<div style="display:table; width:100%;">
										<div style="display:table-row">
											<div style="display:table-cell; width:100px;">Volume (Liter)</div>
											<div style="display:table-cell; text-align:right;">
												<p style="margin-bottom:3px;">' . number_format($arr1['volume']) . '</p>
											</div>
										</div>
										<div style="display:table-row">
											<div style="display:table-cell; width:100px;">Harga Dasar</div>
											<div style="display:table-cell; text-align:right;">
												<p style="margin-bottom:3px;">' . number_format($arr1['harga_dasar']) . '</p>
											</div>
										</div>
										<div style="display:table-row">
											<div style="display:table-cell; width:100px;">Ongkos Angkut</div>
											<div style="display:table-cell; text-align:right;">
												<p style="margin-bottom:3px;">' . number_format($arr1['oa_kirim']) . '</p>
											</div>
										</div>
										<div style="display:table-row">
											<div style="display:table-cell; width:100px;">PPN</div>
											<div style="display:table-cell; text-align:right;">
												<p style="margin-bottom:3px;">' . number_format($arr1['ppn']) . '</p>
											</div>
										</div>
										<div style="display:table-row">
											<div style="display:table-cell; width:100px;">PBBKB</div>
											<div style="display:table-cell; text-align:right;">
												<p style="margin-bottom:3px;">' . number_format($arr1['pbbkb']) . '</p>
											</div>
										</div>
									</div>
								</td>
								<td class="text-center">' . $arr1['tgl_approval'] . '</td>
								<td class="text-left">' . $arr1['result'] . '</td>
								<td class="text-left">' . nl2br($arr1['summary']) . '</td>
								<td class="text-left">' . nl2br($arr1['keterangan_pengajuan']) . '</td>
							</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="7">Histori approval penawaran belum ada</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <button id="close-widget1" class="btn btn-primary btn-sm">
                <i class="fa fa-close"></i> Tutup
            </button>
        </div> -->


        <!-- <script>
            document.getElementById('open-widget').addEventListener('click', function() {
                var dataPenawaran = document.getElementById('penawaran-data');
                if (dataPenawaran.style.display === 'none') {
                    dataPenawaran.style.display = 'block'; // Menampilkan data penawaran
                } else {
                    dataPenawaran.style.display = 'none'; // Menyembunyikan data penawaran
                }
            });

            document.getElementById('close-widget').addEventListener('click', function() {
                document.getElementById('penawaran-data').style.display = 'none'; // Menyembunyikan widget saat tombol tutup ditekan
            });

            document.getElementById('open1-widget').addEventListener('click', function() {
                var dataHistory = document.getElementById('history-data');
                if (dataHistory.style.display === 'none') {
                    dataHistory.style.display = 'block'; // Menampilkan data History
                } else {
                    dataHistory.style.display = 'none'; // Menyembunyikan data History
                }
            });

            document.getElementById('close-widget1').addEventListener('click', function() {
                document.getElementById('history-data').style.display = 'none'; // Menyembunyikan widget saat tombol tutup ditekan
            });
        </script>





        <style>
            /* Ukuran teks lebih kecil */
            .floating-circle .text {
                font-size: 12px;
                opacity: 1;
                transition: opacity 0.3s ease-in-out;
            }

            /* Tombol dengan Ujung Kiri Lancip */
            .floating-circle {
                position: fixed;
                right: 20px;
                width: 180px;
                height: 60px;
                background-color: #007bff;
                color: white;
                border: none;
                border-radius: 10px 30px 30px 10px;
                /* Ujung kiri lancip */
                font-size: 16px;
                text-align: center;
                cursor: pointer;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                z-index: 1000;
                display: flex;
                align-items: center;
                justify-content: start;
                padding: 0 15px;
                white-space: nowrap;
                gap: 10px;
            }

            /* Tombol "Data Penawaran" */
            #open-widget {
                top: 40%;
            }

            /* Tombol "History Penawaran" */
            #open1-widget {
                top: 55%;
            }

            /* Ikon Selalu Tampil */
            .floating-circle .icon {
                flex-shrink: 0;
            }

            /* Tidak Ada Efek Hover */
            .floating-circle:hover {
                width: 180px;
            }

            @media (max-width: 768px) {
                .floating-circle {
                    width: 60px;
                    height: 60px;
                    justify-content: center;
                    padding: 0;
                    border-radius: 50%;
                }

                .floating-circle .text {
                    display: none;
                }
            }

            .floating-widget {
                position: fixed;
                right: 90px;
                top: 50%;
                transform: translateY(-50%);
                width: 350px;
                /* Ukuran lebih besar */
                max-height: 300px;
                /* Tinggi maksimum */
                background: white;
                border-radius: 12px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
                padding: 20px;
                /* Padding lebih besar */
                display: none;
                z-index: 1001;
                overflow-y: auto;
                /* Tambahkan scroll jika konten melebihi max-height */
            }

            /* Jika Mobile, Widget Digeser Lebih Dekat */
            @media (max-width: 768px) {
                .floating-widget {
                    right: 70px;
                    width: 350px;
                }
            }

            /* Header Widget */
            /* Header Widget */
            .widget-header {
                display: flex;
                justify-content: space-between;
                font-size: 18px;
                /* Perbesar teks */
                font-weight: bold;
                margin-bottom: 15px;
            }



            /* Tombol Approval */
            .widget-content button {
                width: 100%;
                padding: 10px;
                margin-top: 10px;
                font-size: 16px;
                /* Perbesar teks tombol */
                border: none;
                cursor: pointer;
                border-radius: 5px;
            }

            /* Tombol Approve */
            #approve-btn {
                background-color: #28a745;
                color: white;
            }

            /* Tombol Reject */
            #reject-btn {
                background-color: #dc3545;
                color: white;
            }
        </style> -->
<?php }
} /* END FOREACH */ ?>

<script>
    $(document).ready(function() {
        $("#btnSbmt").on("click", function(e) {
            e.preventDefault(); // Mencegah tombol langsung submit

            // Ambil nilai approval dan extend
            const approval = $("input[name=\'approval\']:checked").val();
            const extend = $("input[name=\'extend\']:checked").val();
            // Ambil tier dari hidden input
            const tier = $("#tier_value").val().trim();

            // Debugging (opsional)
            console.log("Tier:", tier);
            console.log("Approval:", approval);
            console.log("Extend:", extend);

            // Validasi Tier III - harus diteruskan ke CEO jika disetujui
            if (tier === "III" && approval === "1" && extend === "2") {
                Swal.fire({
                    icon: "error",
                    title: "Tidak Valid",
                    text: "Untuk tier III, jika disetujui maka wajib diteruskan ke CEO.",
                    confirmButtonText: "OK"
                });
                return;
            }


            if (approval === "2" && extend === "1") {
                Swal.fire({
                    icon: "error",
                    title: "Tidak Valid",
                    text: "Jika persetujuan dipilih 'Tidak', maka tidak boleh diteruskan ke CEO.",
                    confirmButtonText: "OK"
                });
                return;
            }

            // Ambil data dari modal untuk ditampilkan di SweetAlert konfirmasi
            let hargaDasar = $("td:contains(\'Harga Dasar\')").closest("tr").find("td:eq(3)").first().text().trim();
            let hargaTier = $(".text-right b:eq(0)").text().trim();
            let totalRefund = $("td:contains(\'Total Refund\')").next().next().text().trim();
            let totalOtherCost = $("td:contains(\'Total Other Cost\')").next().next().text().trim();

            // Tampilkan konfirmasi SweetAlert jika lolos validasi
            Swal.fire({
                title: "Konfirmasi Simpan",
                html: `
                            <table class="table table-bordered text-left">
                                <tr><td><b>Harga Dasar:</b></td><td>${hargaDasar}</td></tr>
                                <tr><td><b>Harga Tier:</b></td><td>${hargaTier}</td></tr>
                                <tr><td><b>Tier:</b></td><td>${tier}</td></tr>
                            </table>
                            <p>Apakah anda yakin ingin simpan penawaran ini?</p>
                        `,
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
    });
</script>
';
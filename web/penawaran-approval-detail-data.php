<?php
$simpan  = false;
function formatAngka($nilai, $pembulatan = 0)
{
    if (!isset($nilai) || !is_numeric($nilai)) return '';

    if (fmod($nilai, 1) != 0) {
        if ($pembulatan == 0) {
            return number_format($nilai, 2, '.', ',');
        } elseif ($pembulatan == 1) {
            return number_format($nilai, 0, '.', ',');
        } else {
            return number_format($nilai, 4, '.', ',');
        }
    } else {
        return number_format($nilai, 0, '.', ',');
    }
}

function getHargaKey($row)
{
    return md5(
        $row['volume'] . '|' .
            $row['harga_dasar'] . '|' .
            $row['oa_kirim'] . '|' .
            $row['ppn'] . '|' .
            $row['pbbkb'] . '|' .
            $row['harga_tier'] . '|' .
            $row['tier'] . '|' .
            $row['refund'] . '|' .
            $row['other_cost']
    );
}

function getDeltaClass($old, $new)
{
    if (!is_numeric($old) || !is_numeric($new)) return '';
    if ($old == $new) return '';
    return ($new > $old) ? 'color: green;' : 'color: red;';
}

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
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <input type="hidden" name="keterangan_pengajuan" id="keterangan_pengajuan" value="<?php echo $rsm['catatan']; ?>" />
                    </div>
                    <div class="box-body" style="margin-top: -25px;">
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
                                    <td>TOP Customer</td>
                                    <td class="text-center">:</td>
                                    <td><?php echo $arrPayment[$rsm['jenis_payment']]; ?></td>
                                </tr>
                            </table>
                            <hr style="margin:10px 0px; border-top:4px double #ddd;" />
                        </div>
                    </div>
                </div>
                <style>
                    .panel-heading {
                        font-size: 12px;
                    }

                    .panel-title a {
                        font-size: 12px;
                        display: block;
                    }

                    .panel-body {
                        font-size: 12px;
                    }
                </style>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <h4>Penawaran</h4>
                                <div class="panel-group" id="accordion">
                                    <?php foreach ($rsms as $key => $data) : ?>
                                        <?php
                                        $data = (array) $data;
                                        $rincian_penawaran = json_decode($data['detail_rincian'], true);
                                        // ... kode lainnya ...
                                        $data['harga_normal'] = ($data['harga_normal'] ? $data['harga_normal'] : $data['harga_normal_new']);
                                        $data['harga_sm'] = ($data['harga_sm'] ? $data['harga_sm'] : $data['harga_sm_new']);
                                        $data['harga_om'] = ($data['harga_om'] ? $data['harga_om'] : $data['harga_om_new']);
                                        $data['harga_coo'] = ($data['harga_coo'] ? $data['harga_coo'] : $data['harga_coo_new']);
                                        $data['harga_ceo'] = ($data['harga_ceo'] ? $data['harga_ceo'] : $data['harga_ceo_new']);

                                        // Tambahkan class untuk menyembunyikan history (data ke-2 dan seterusnya)
                                        $hiddenClass = ($key > 0) ? 'history-penawaran hide' : '';

                                        $sqlOtherCost = "SELECT keterangan, nominal FROM pro_other_cost_detail 
                                        WHERE id_penawaran = '" . $data['id_penawaran'] . "'";
                                        $rsmOtherCost = $con->getResult($sqlOtherCost);
                                        ?>
                                        <div class="panel panel-default <?= $hiddenClass ?>">
                                            <div class="panel-heading" style="cursor: pointer;">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" href="#collapse<?= $key ?>">
                                                        Data Penawaran di tanggal : <?php echo tgl_indo($data['created_time']); ?>
                                                        <strong style="float: right;">Status PO :
                                                            <span style="color: <?php echo ($data['penawaran_status'] == 'YA' ? 'green' : 'red'); ?>;"><?php echo $data['penawaran_status']; ?></span>
                                                        </strong>
                                                        <i class="toggle-icon fa fa-chevron-down ml-2"></i>
                                                        <?php if ($data['is_edited'] == 1) : ?>
                                                            <span class="badge-info">Edited</span>
                                                        <?php endif ?>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapse<?= $key ?>" class="panel-collapse collapse <?= $key == 0 ? 'in' : '' ?>">
                                                <div class="panel-body">
                                                    <table class="table table-bordered table-summary">
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="2" style="background-color:#f4f4f4; vertical-align:middle; padding:8px 5px;">
                                                                    <b>PRICELIST</b>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Masa Berlaku Harga</td>
                                                                <td><?php echo tgl_indo($data['masa_awal']) . " - " . tgl_indo($data["masa_akhir"]); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Rekomendasi Harga Dasar</td>
                                                                <td><?php echo number_format($data['harga_normal']); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Rekomendasi Ongkos Angkut</td>
                                                                <td><?php echo number_format($data['oa_kirim']); ?></td>
                                                            </tr>
                                                            <?php if ($sesrole == 7 || $sesrole == 6 || $sesrole == 3 || $sesrole == 21) { ?>
                                                                <tr>
                                                                    <td>Harga Tier I BM</td>
                                                                    <td><?php echo number_format($data['harga_sm']); ?></td>
                                                                </tr>
                                                            <?php }
                                                            if ($sesrole == 6 || $sesrole == 3 || $sesrole == 21) { ?>
                                                                <tr>
                                                                    <td>Harga Tier II OM</td>
                                                                    <td><?php echo number_format($data['harga_om']); ?></td>
                                                                </tr>
                                                            <?php }
                                                            if ($sesrole == 3 || $sesrole == 21) { ?>
                                                                <tr>
                                                                    <td>Harga Tier III COO</td>
                                                                    <td><?php echo number_format($data['harga_coo']); ?></td>
                                                                </tr>
                                                            <?php }
                                                            if ($sesrole == 21) { ?>
                                                                <tr>
                                                                    <td>Harga Tier III CEO</td>
                                                                    <td><?php echo number_format($data['harga_ceo']); ?></td>
                                                                </tr>
                                                            <?php } ?>
                                                            <tr>
                                                                <td colspan="2" style="background-color:#f4f4f4; vertical-align:middle; padding:8px 5px;">
                                                                    <b>PROPOSE</b>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="200">Nomor Referensi</td>
                                                                <td><?php echo $data['nomor_surat']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="200">Produk</td>
                                                                <td><?php echo $data['merk_dagang']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="200">Volume</td>
                                                                <td><?php echo number_format($data['volume_tawar']) . " Liter"; ?>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Refund</td>
                                                                <td><?php echo ($data['refund_tawar']) ? number_format($data['refund_tawar']) : '-'; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Other Cost</td>
                                                                <td><?php echo ($data['other_cost']) ? number_format($data['other_cost']) : '-'; ?></td>
                                                            </tr>
                                                            <?php if (!empty($rsmOtherCost) && is_array($rsmOtherCost)) : ?>
                                                                <tr>
                                                                    <td>Keterangan Other Cost</td>
                                                                    <td>
                                                                        <table class="table table-bordered">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>Keterangan</th>
                                                                                    <th>Nominal</th>
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
                                                                <tr>
                                                                    <td>Keterangan Other Cost</td>
                                                                    <td>Tidak ada data Other Cost.</td>
                                                                </tr>
                                                            <?php endif; ?>
                                                            <tr>
                                                                <td width="200">Order Method</td>
                                                                <td><?php echo $data['method_order'] . " hari sebelum pickup"; ?></td>
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

                                                            <?php if ($key == 0) : ?>
                                                                <input type="hidden" value="<?= $data['harga_tier'] ?>" name="harga_tier" id="harga_tier">
                                                                <input type="hidden" value="<?= $data['tier'] ?>" name="tier" id="tier">
                                                                <input type="hidden" value="<?= $data['refund_tawar'] ?>" name="refund_tawar" id="refund_tawar">
                                                                <input type="hidden" value="<?= $data['other_cost'] ?>" name="other_cost" id="other_cost">
                                                                <input type="hidden" value="<?= $data['volume_tawar']; ?>" name="volume" id="volume" />
                                                            <?php endif ?>

                                                            <?php if ($data['perhitungan'] == 1) { ?>
                                                                <tr>
                                                                    <td>Harga Penawaran</td>
                                                                    <td><?php echo formatAngka($data['harga_dasar'], $data['pembulatan']); ?>
                                                                    </td>
                                                                </tr>
                                                            <?php } else { ?>
                                                                <tr>
                                                                    <td colspan="2">Perhitungan menggunakan formula</td>
                                                                </tr>
                                                            <?php } ?>


                                                            <tr>
                                                                <td>Metode Pengiriman</td>
                                                                <td><?php echo $data['metode']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Lokasi Pengiriman</td>
                                                                <td><?php echo $data['lok_kirim']; ?></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <?php
                                                    $breakdown = true;
                                                    if ($breakdown) {
                                                        $nom = 0;
                                                        $oa_penawaran = 0;
                                                    ?>

                                                        <div class="row" style="display: flex; align-items: flex-start; margin-top: 0;">
                                                            <div class="col-md-6" style="padding-top: 0;">
                                                                <div style="margin-bottom: 10px;">
                                                                    <p style="margin: 0;"><strong>Rincian Harga:</strong></p>
                                                                </div>
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
                                                                            foreach ($rincian_penawaran as $arr1) {
                                                                                $nom++;
                                                                                $cetak = $arr1['rinci'] || true;
                                                                                $nilai = $arr1['nilai'];
                                                                                $biaya = "";
                                                                                if (isset($arr1['biaya']) && is_numeric($arr1['biaya'])) {
                                                                                    if (fmod($arr1['biaya'], 1) != 0) {
                                                                                        if ($data['pembulatan'] == 0) {
                                                                                            $biaya = number_format($arr1['biaya'], 2, '.', ',');
                                                                                        } elseif ($data['pembulatan'] == 1) {
                                                                                            $biaya = number_format($arr1['biaya'], 0, '.', ',');
                                                                                        } else {
                                                                                            $biaya = number_format($arr1['biaya'], 4, '.', ',');
                                                                                        }
                                                                                    } else {
                                                                                        $biaya = number_format($arr1['biaya'], 0, '.', ',');
                                                                                    }
                                                                                }
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
                                                                    <?php if ($data['pembulatan']) { ?>
                                                                        <p style="margin:0px 0px 5px;"><i>*) Perhitungan menggunakan pembulatan</i></p>
                                                                    <?php } else { ?>
                                                                        <p style="margin:0px 0px 5px;"><i>*) Perhitungan tidak menggunakan pembulatan</i></p>
                                                                    <?php } ?>

                                                                    <?php if ($data['gabung_oa']) { ?>
                                                                        <p style="margin:0px 0px 15px;"><i>*) Cetakan Harga Dasar Termasuk Ongkos Angkut</i></p>
                                                                    <?php } else { ?>
                                                                        <p style="margin:0px 0px 15px;"><i>*) Cetakan Harga Dasar Tidak Termasuk Ongkos Angkut</i></p>
                                                                    <?php } ?>
                                                                </div>
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
                                                                                <td class="text-right"><b><?php echo number_format($data['harga_tier']); ?></b></td>
                                                                            </tr>

                                                                            <tr>
                                                                                <td class="text-center">2</td>
                                                                                <td class="text-left">Tier</td>
                                                                                <td class="text-right"><b><?php echo $data['tier']; ?></b></td>
                                                                                <td class="text-right"></td>
                                                                            </tr>

                                                                            <?php
                                                                            $totalrefund = $data['refund_tawar'] * $data['volume_tawar'];
                                                                            $totalother = $data['other_cost'] * $data['volume_tawar'];
                                                                            ?>

                                                                            <tr>
                                                                                <td class="text-center">3</td>
                                                                                <td class="text-left">Total Refund</td>
                                                                                <td class="text-right"><?php echo number_format($data['refund_tawar']); ?></td>
                                                                                <td class="text-right <?php echo ($totalrefund > 1000000) ? 'text-danger' : 'text-success'; ?>">
                                                                                    <?php echo number_format($totalrefund); ?>
                                                                                </td>
                                                                            </tr>

                                                                            <tr>
                                                                                <td class="text-center">4</td>
                                                                                <td class="text-left">Total Other Cost</td>
                                                                                <td class="text-right"><?php echo number_format($data['other_cost']); ?></td>
                                                                                <td class="text-right <?php echo ($totalother > 1000000) ? 'text-danger' : 'text-success'; ?>">
                                                                                    <?php echo number_format($totalother); ?>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6" style="padding-top: 0;">
                                                                <?php
                                                                $sqlHist = "
                                                                        SELECT 
                                                                            a.kd_approval,
                                                                            CASE 
                                                                                WHEN a.result = '1' THEN 'Disetujui'
                                                                                WHEN a.result = '2' THEN 'Ditolak'
                                                                                ELSE ''
                                                                            END AS result, 
                                                                            a.summary, a.keterangan_pengajuan,
                                                                            a.id_user, a.tgl_approval,
                                                                            b.fullname, c.role_name,
                                                                            a.harga_dasar, a.oa_kirim, a.ppn, a.pbbkb, a.volume, a.harga_tier, a.tier, a.refund, a.other_cost,
                                                                            a.tgl_approval AS ordernya
                                                                        FROM pro_approval_hist a 
                                                                        JOIN acl_user b ON a.id_user = b.id_user 
                                                                        JOIN acl_role c ON a.id_role = c.id_role 
                                                                        WHERE a.kd_approval = 'P001' 
                                                                            AND a.id_customer = '" . $idr . "' 
                                                                            AND a.id_penawaran = '" . $data['id_penawaran'] . "'
                                                                        ORDER BY a.tgl_approval ASC";

                                                                $resHist = $con->getResult($sqlHist);

                                                                $grouped = [];

                                                                foreach ($resHist as $hist) {
                                                                    $i = getHargaKey($hist);
                                                                    if (!isset($grouped[$i])) {
                                                                        $grouped[$i] = [
                                                                            'harga' => [
                                                                                'volume' => $hist['volume'],
                                                                                'harga_dasar' => $hist['harga_dasar'],
                                                                                'oa_kirim' => $hist['oa_kirim'],
                                                                                'ppn' => $hist['ppn'],
                                                                                'pbbkb' => $hist['pbbkb'],
                                                                                'harga_tier' => $hist['harga_tier'],
                                                                                'tier' => $hist['tier'],
                                                                                'refund' => $hist['refund'],
                                                                                'other_cost' => $hist['other_cost'],
                                                                            ],
                                                                            'approvals' => []
                                                                        ];
                                                                    }

                                                                    $grouped[$i]['approvals'][] = $hist;
                                                                }
                                                                ?>

                                                                <?php if (count($resHist) > 0): ?>
                                                                    <div style="margin-bottom: 10px;">
                                                                        <p style="margin: 0;"><strong>History Approval:</strong></p>
                                                                    </div>
                                                                    <div class="box box-primary">
                                                                        <div class="box-body" style="max-height: 500px; overflow-y: auto; overflow-x: hidden;">

                                                                            <?php
                                                                            $prevHarga = null;
                                                                            $index = 1;
                                                                            foreach ($grouped as $group):
                                                                                $curr = $group['harga'];
                                                                            ?>
                                                                                <p style="font-weight: bold; margin-top: 20px;">Pengajuan ke-<?= $index ?></p>

                                                                                <!-- Blok Harga -->
                                                                                <table class="table table-bordered" style="width: 100%; max-width: 600px;">
                                                                                    <tr>
                                                                                        <td>Volume (Liter)</td>
                                                                                        <td class="text-right" style="<?= $prevHarga ? getDeltaClass($prevHarga['volume'], $curr['volume']) : '' ?>">
                                                                                            <?= formatAngka($curr['volume'], $data['pembulatan']) ?>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Harga Dasar</td>
                                                                                        <td class="text-right" style="<?= $prevHarga ? getDeltaClass($prevHarga['harga_dasar'], $curr['harga_dasar']) : '' ?>">
                                                                                            <?= formatAngka($curr['harga_dasar'], $data['pembulatan']) ?>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Ongkos Angkut</td>
                                                                                        <td class="text-right" style="<?= $prevHarga ? getDeltaClass($prevHarga['oa_kirim'], $curr['oa_kirim']) : '' ?>">
                                                                                            <?= formatAngka($curr['oa_kirim'], $data['pembulatan']) ?>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>PPN</td>
                                                                                        <td class="text-right" style="<?= $prevHarga ? getDeltaClass($prevHarga['ppn'], $curr['ppn']) : '' ?>">
                                                                                            <?= formatAngka($curr['ppn'], $data['pembulatan']) ?>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>PBBKB</td>
                                                                                        <td class="text-right" style="<?= $prevHarga ? getDeltaClass($prevHarga['pbbkb'], $curr['pbbkb']) : '' ?>">
                                                                                            <?= formatAngka($curr['pbbkb'], $data['pembulatan']) ?>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Harga Tier</td>
                                                                                        <td class="text-right" style="<?= $prevHarga ? getDeltaClass($prevHarga['harga_tier'], $curr['harga_tier']) : '' ?>">
                                                                                            <?= formatAngka($curr['harga_tier'], $data['pembulatan']) ?>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Tier</td>
                                                                                        <td class="text-right" style="<?= $prevHarga ? getDeltaClass($prevHarga['tier'], $curr['tier']) : '' ?>">
                                                                                            <?= $curr['tier'] ? $curr['tier'] : '-' ?>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Refund</td>
                                                                                        <td class="text-right" style="<?= $prevHarga ? getDeltaClass($prevHarga['refund'], $curr['refund']) : '' ?>">
                                                                                            <?= formatAngka($curr['refund'], $data['pembulatan']) ?>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Other Cost</td>
                                                                                        <td class="text-right" style="<?= $prevHarga ? getDeltaClass($prevHarga['other_cost'], $curr['other_cost']) : '' ?>">
                                                                                            <?= formatAngka($curr['other_cost'], $data['pembulatan']) ?>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>

                                                                                <?php foreach ($group['approvals'] as $hist): ?>
                                                                                    <div class="approval-block" style="margin-bottom: 20px; padding: 10px; border: 1px solid #ccc; border-radius: 6px; background-color: #fdfdfd;">
                                                                                        <?php if ($hist['result'] == 'Disetujui') : ?>
                                                                                            <p><b style="color: blue;">Disetujui oleh : </b> <?= $hist['fullname'] ?> <i>(<?= str_replace('Role ', '', $hist['role_name']) ?>)</i></p>
                                                                                        <?php else : ?>
                                                                                            <p><b style="color: red;">Ditolak oleh : </b> <?= $hist['fullname'] ?> <i>(<?= str_replace('Role ', '', $hist['role_name']) ?>)</i></p>
                                                                                        <?php endif ?>
                                                                                        <p><b>Tanggal : </b> <?= tgl_indo($hist['tgl_approval']) . " " . date("H:i", strtotime($hist['tgl_approval'])) ?></p>
                                                                                        <p><b>Catatan : </b><br><?= $hist['summary'] ? nl2br($hist['summary']) : '–' ?></p>
                                                                                    </div>
                                                                                <?php endforeach; ?>
                                                                                <!-- Tampilkan Catatan Marketing satu kali -->
                                                                                <?php
                                                                                $firstApproval = reset($group['approvals']);
                                                                                if (!$isSame && !empty($firstApproval['keterangan_pengajuan'])):
                                                                                ?>
                                                                                    <div style="margin-top: -10px; margin-bottom: 15px;">
                                                                                        <p><b>Catatan Marketing:</b></p>
                                                                                        <?= html_entity_decode($firstApproval['keterangan_pengajuan']) ?>
                                                                                    </div>
                                                                                <?php endif; ?>
                                                                                <hr style="border-top: 3px dashed #bbb; margin: 20px 0;">
                                                                            <?php
                                                                                $prevHarga = $curr;
                                                                                $index++;
                                                                            endforeach;
                                                                            ?>
                                                                        </div>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <center>
                                                                        <p><b>History approval penawaran belum ada.</b></p>
                                                                    </center>
                                                                <?php endif; ?>

                                                            </div>
                                                        </div>
                                                    <?php } else if ($data['perhitungan'] == 2) { ?>
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

                                                                if ($data['flag_approval'] == 0 && $data['flag_disposisi'] == 0) {
                                                                    $status = "Terdaftar";
                                                                } else if ($data['flag_approval'] == 0 && $data['flag_disposisi']) {
                                                                    if ($data['flag_disposisi'] > 1 && $data['flag_disposisi'] < 4) {
                                                                        $status = "Verifikasi " . $arrPosisi[$data['flag_disposisi']] . " " . $data['nama_cabang'];
                                                                    } else {
                                                                        $status = "Verifikasi " . $arrPosisi[$data['flag_disposisi']];
                                                                    }
                                                                } else if ($data['flag_approval']) {
                                                                    $picApproval = "";
                                                                    if ($data['flag_disposisi'] == '3') $picApproval = $data['sm_wil_pic'];
                                                                    else if ($data['flag_disposisi'] == '4') $picApproval = $data['om_pic'];
                                                                    else if ($data['flag_disposisi'] == '5') $picApproval = $data['coo_pic'];
                                                                    else if ($data['flag_disposisi'] == '6') $picApproval = $data['ceo_pic'];

                                                                    $alasanDitolak = "";
                                                                    if ($data['flag_approval'] == '2') {
                                                                        $alasanDitolak = "<br /><br /><b><u>Alasan Penolakan</u></b>";
                                                                        $alasanDitolak .= "<br />" . ($data[$arrAlasan[$data['flag_disposisi']]] ? nl2br($data[$arrAlasan[$data['flag_disposisi']]]) : "-") . "<br />";
                                                                    }

                                                                    if ($data['flag_disposisi'] > 1 && $data['flag_disposisi'] < 4) {
                                                                        $status = $arrSetuju[$data['flag_approval']] . " " . $arrPosisi[$data['flag_disposisi']] . " " . $data['nama_cabang'];
                                                                        $status .= $alasanDitolak;
                                                                        $status .= "<br /><i>" . ($picApproval ? $picApproval . " - " : "");
                                                                        $status .= ($data['tgl_approval'] ? date("d/m/Y H:i:s", strtotime($data['tgl_approval'])) . " WIB" : "") . "</i>";
                                                                    } else {
                                                                        $status = $arrSetuju[$data['flag_approval']] . " " . $arrPosisi[$data['flag_disposisi']];
                                                                        $status .= $alasanDitolak;
                                                                        $status .= "<br /><i>" . ($picApproval ? $picApproval . " - " : "");
                                                                        $status .= ($data['tgl_approval'] ? date("d/m/Y H:i:s", strtotime($data['tgl_approval'])) . " WIB" : "") . "</i>";
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
                                                            <div class="form-control" style="min-height:90px; height:auto; font-size:12px;"><?php echo ($data['catatan'] ? nl2br($data['catatan']) : '&nbsp;'); ?></div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                    $need_ceo = 0;
                                                    if ($oa_penawaran < $data['oa_kirim'] && $data['metode'] == 'Franco') {
                                                        $need_ceo = 1;
                                                        echo '<p class="text-red" style="font-size:14px;"><b>OA PENAWARAN LEBIH KECIL DARI OA YANG DIREKOMENDASIKAN</b></p>';
                                                    }
                                                    if ($data['other_cost'] > 50) {
                                                        $need_ceo = 1;
                                                        echo '<p class="text-red" style="font-size:14px;"><b>OTHER COST MELEBIHI DARI BATAS OTHER COST YANG DI TETAPKAN</b></p>';
                                                    }
                                                    if ($data['refund_tawar'] > 70 || $totalrefund > 1000000) {
                                                        $need_ceo = 1;
                                                        echo '<p class="text-red" style="font-size:14px;"><b>REFUND MELEBIHI DARI BATAS REFUND YANG DI TETAPKAN</b></p>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($key == 0): ?>
                                            <?php

                                            if ($sesrole == 7) {
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
                                                }
                                            }

                                            /* BUAT APPROVAL OM */
                                            if ($sesrole == 6) {

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
                                                }
                                            }

                                            /* BUAT APPROVAL COO */
                                            if ($sesrole == 3) {

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
                                                }
                                            }

                                            /* BUAT APPROVAL CEO */
                                            if ($sesrole == 21) {

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
                                                }
                                            }
                                            ?>
                                            <br>
                                            <div style="margin-bottom:0px;">
                                                <input type="hidden" name="act" value="<?php echo $action; ?>" />
                                                <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                                                <input type="hidden" name="idk" value="<?php echo $idk; ?>" />
                                                <input type="hidden" id="tier_value" value="<?= $rsm['tier']; ?>">
                                                <?php if ($simpan === true) { ?>
                                                    <button type="submit" name="btnSbmt" id="btnSbmt" class="btn btn-primary jarak-kanan" style="min-width:90px;">
                                                        <i class="fa fa-save jarak-kanan"></i> Simpan</button>
                                                <?php } ?>
                                            </div>
                                            <hr style="border-top: 1px dashed #bbb; margin: 20px 0;">
                                            <div style="margin-top:10px; margin-bottom:15px;">
                                                <button class="btn btn-primary btn-sm" type="button" id="toggleHistory">Daftar History Penawaran</button>
                                            </div>
                                        <?php endif ?>
                                    <?php endforeach ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- <hr style="margin:15px 0px; border-top:4px double #ddd;" /> -->


        </form>
        <hr style="margin:10px 0px; border-top:4px double #ddd;" />
        <a class="btn btn-default" style="min-width:90px;" href="<?php echo BASE_URL_CLIENT . "/penawaran-approval.php"; ?>">
            <i class="fa fa-reply jarak-kanan"></i> Batal
        </a>
<?php }
} /* END FOREACH */ ?>

<script>
    $(document).ready(function() {

        document.getElementById('toggleHistory').addEventListener('click', function() {
            var elements = document.querySelectorAll('.history-penawaran');
            elements.forEach(function(el) {
                el.classList.toggle('hide');
            });

            // Optional: toggle button text
            this.textContent = (this.textContent === "Daftar History Penawaran") ? "Sembunyikan History Penawaran" : "Daftar History Penawaran";
        });

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
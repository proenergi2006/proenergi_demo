<div style="overflow-y: scroll" id="table-long">
    <div style="width:1660px; height:auto;">
        <div class="table-responsive-satu">
            <table class="table table-bordered" id="table-grid3">
                <thead>
                    <tr>
                        <th class="text-center" width="50">No</th>

                        <th class="text-center" width="100">Volume PO</th>
                        <th class="text-center" width="100">Volume Terima</th>
                        <th class="text-center" width="100">Jenis </th>
                        <th class="text-center" width="200">Volume</th>
                        <th class="text-center" width="150">Attachment</th>
                        <th class="text-center" width="150">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select a.*,b.nama_vendor, c.merk_dagang, d.nama_terminal 
                                                                    from new_pro_inventory_gain_loss a 
                                                                    join new_pro_inventory_vendor_po e on a.id_po_supplier = e.id_master 
                                                                    join pro_master_vendor b on e.id_vendor = b.id_master 
                                                                    join pro_master_produk c on e.id_produk = c.id_master 
                                                                    join pro_master_terminal d on e.id_terminal = d.id_master  
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

                            if ($data['jenis'] == 1)
                                $jenis = 'Gain';
                            else if ($data['jenis'] == 2)
                                $jenis = 'Loss';

                            $dataIcons     = "";
                            $pathnya     = $public_base_directory . '/files/uploaded_user/lampiran';
                            if ($data['file_upload_ori'] && file_exists($pathnya . '/' . $data['file_upload'])) {
                                $labelFile     = 'Ubah File';
                                $linkPt     = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=108900&ktg=" . $data['file_upload'] . "&file=" . $data['file_upload_ori']);
                                $dataIcons     = '
                                <a href="' . $linkPt . '" target="_blank" title="download file"> 
                                <i class="far fa-file-alt jarak-kanan" style="font-size:14px;"></i> Download</a>';
                            }
                    ?>
                            <tr>

                                <td class="text-center"><?php echo $nom; ?></td>

                                <td class="text-center">
                                    <p style="margin-bottom:0px">
                                        <?php echo number_format($data['volume_po']); ?>
                                    </p>

                                </td>
                                <td class="text-center">
                                    <p style="margin-bottom:0px">
                                        <?php echo number_format($data['volume_terima']); ?>
                                    </p>

                                </td>
                                <td class="text-center">
                                    <p style="margin-bottom:0px"><?php echo $jenis; ?></p>

                                </td>
                                <td class="text-center">
                                    <p style="margin-bottom:0px">
                                        <?php echo number_format($data['volume']); ?> Ltr
                                    </p>

                                </td>
                                <td class="text-center">
                                    <p style="margin-bottom:0px"><?php echo $dataIcons; ?></p>

                                </td>

                                <td class="text-center"><?php echo $data['ket']; ?></td>



                            </tr>
                    <?php }
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- <?php if (count($res) > 0) { ?>
    <?php if (!$fnr) { ?>
        <div class="form-group row">
            <div class="col-sm-4">
                <label>Persetujuan Gain & Loss ?*</label>
                <div class="radio clearfix" style="margin:0px;">
                    <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="revert" id="revert1" class="validate[required]" value="1" /> Disetujui</label>
                    <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="revert" id="revert2" class="validate[required]" value="2" /> Ditolak</label>
                </div>
            </div>


        </div>
    <?php } ?>
<?php } ?> -->
<div class="form-group row persetujuan-ceo" <?php echo (!$fnr) ? 'hide' : ''; ?>>
    <div class="col-sm-6">
        <?php if (!$fnr) { ?>
            <!-- <textarea name="summary" id="summary" class="form-control"></textarea> -->
        <?php } else { ?>
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














<?php if (count($res) > 0) { ?>
    <p>&nbsp;</p>
    <div class="row">
        <div class="col-sm-6">
            <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
            <input type="hidden" name="idw" value="<?php echo $row['id_wilayah']; ?>" />
            <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT . "/verifikasi-gain-loss.php"; ?>">Kembali</a>
            <?php if (!$fnr) { ?>
                <!--  <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt">Simpan</button> -->
            <?php } ?>
        </div>
    </div>
<?php } ?>
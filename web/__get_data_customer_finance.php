<?php
    $arrResult  = array(1=>"Supply Delivery", "Supply Delivery With Note", "Revised and Resubmitted","Yes","No");
    $arrSetuju  = array(1=>"Yes", "No");    

    $arrTipeBisnis  = array(
        1=>"Agriculture & Forestry / Horticulture", "Business & Information", "Construction/Utilities/Contracting", "Education", 
        "Finance & Insurance", "Food & hospitally", "Gaming", "Health Services", 
        "Motor Vehicle", $rsm['tipe_bisnis_lain'], "Natural Resources / Environmental", "Personal Service", "Manufacture"
    );
    $tipebisnis     = ($arrTipeBisnis[$rsm['tipe_bisnis']] ? $arrTipeBisnis[$rsm['tipe_bisnis']] : '-');

    $eval       = json_decode($rsm['finance_data'], true);
    $arrD       = $eval?explode(",", $eval[1]["nomor"]):[];
    
    $jenis_net  = $rsm['jenis_net'];
    $arrKondInd = array(1=>"Setelah Invoice diterima", "Setelah pengiriman", "Setelah loading");
    $arrKondEng = array(1=>"After Invoice Receive", "After Delivery", "After Loading");
    $arrPayment = array("CREDIT"=>"CREDIT ".$rsm['top_payment']." days ".($rsm['jenis_net']>0?$arrKondEng[$jenis_net]:''), "CBD"=>"CBD (Cash Before Delivery)", "COD"=>"COD (Cash On Delivery)");
    $linkCetak  = ACTION_CLIENT."/credit-app-form-cetak.php?".paramEncrypt("idr=".$idr."&idk=".$rsm['id_review']);

?>
<div class="row">
    <div class="col-md-8">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-3">Customer Name</label>
            <div class="col-md-9">
                <input type="text" name="getData0" id="getData0" class="form-control" value="<?php echo $rsm['nama_customer']; ?>" />
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-3">Business Type</label>
            <div class="col-md-9">
                <input type="text" name="getData1" id="getData1" class="form-control" readonly value="<?php echo $tipebisnis; ?>" />
            </div>
        </div>
    </div>
</div>

<hr style="margin:15px 0px; border-top:4px double #ddd;" />

<p style="margin:20px 0px;"><b><u>PENGAJUAN</u></b></p>
<div class="row">
    <div class="col-md-8">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-3">TOP</label>
            <div class="col-md-9">
                <input type="text" name="getData2" id="getData2" class="form-control" readonly value="<?php echo $arrPayment[$rsm['jenis_payment']]; ?>" />
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-3">Potensial Volume</label>
            <div class="col-md-5">
                <div class="input-group">
                    <input type="text" name="getData3" id="getData3" class="form-control hitung" readonly value="<?php echo $rsm['review9'];?>" />
                    <span class="input-group-addon">Liter</span>
                </div> 
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-3">Pengajuan Kredit Limit</label>
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-addon">Rp.</span>
                    <input type="text" name="getData4" id="getData4" class="form-control hitung" readonly value="<?php echo $rsm['credit_limit_diajukan'];?>" />
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(!$rsm['finance_result']){ ?>
<hr style="margin:15px 0px; border-top:4px double #ddd;" />

<div class="row">
    <div class="col-md-8">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-3">Jenis Data</label>
            <div class="col-md-5">
                <select name="jenis_datanya" id="jenis_datanya" class="form-control select2" required>
                    <option value="1" <?php echo ($rsm['jenis_datanya'] == '1')?' selected':''; ?>>Sebelum Persetujuan Komite</option>
                    <option value="2" <?php echo ($rsm['jenis_datanya'] == '2')?' selected':''; ?>>Setelah Persetujuan Komite</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-3">Financial Review *</label>
            <div class="col-md-9">
                <textarea name="finance_summary" id="finance_summary" class="form-control" style="height:90px;" required><?php echo ($rsm['finance_summary'] ? str_replace("<br />", PHP_EOL, $rsm['finance_summary']) : ''); ?></textarea>
            </div>
        </div>
    </div>
</div>

<?php } ?>

<div id="form_ce_fin_01" <?php echo ($rsm['jenis_datanya'] != '2' ? 'hidden' : '');?>>
    
    <hr style="margin:15px 0px; border-top:4px double #ddd;" />
    
    <p style="margin:20px 0px;"><b><u>PERSETUJUAN</u></b></p>
    <div class="row">
        <div class="col-md-8">
            <div class="form-group form-group-sm">
                <label class="control-label col-md-3">Persetujuan Kredit Limit</label>
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-addon">Rp.</span>
                        <input type="text" name="credit_limit" id="credit_limit" class="form-control hitung" required value="<?php echo $rsm['credit_limit'];?>" <?php echo ($rsm['finance_result']) ? "readonly": "";?> />
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="form-group form-group-sm">
                <label class="control-label col-md-3">Payment Type</label>
                <div class="col-md-5">
                    <select name="jenis_payment" id="jenis_payment" class="form-control select2" required>
                        <option></option>
                        <option value="CBD" <?php echo ($rsm['jenis_payment'] == 'CBD')?' selected':''; ?>>CBD (Cash Before Delivery)</option>
                        <option value="COD" <?php echo ($rsm['jenis_payment'] == 'COD')?' selected':''; ?>>COD (Cash On Delivery)</option>
                        <option value="CREDIT" <?php echo ($rsm['jenis_payment'] == 'CREDIT')?' selected':''; ?>>CREDIT</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    <?php $span_top_val = ($rsm['jenis_payment'] == 'CREDIT') ? '' : 'hidden';?>
    <div id="span_top" <?php echo $span_top_val;?>>
        <div class="row">
            <div class="col-md-8">
                <div class="form-group form-group-sm">
                    <label class="control-label col-md-3">TOP (Top of Payment) (Days)</label>
                    <div class="col-md-5">
                        <input type="text" name="top_payment" id="top_payment" class="form-control hitung" required value="<?php echo($rsm['top_payment'])?>" />
                    </div>
                </div>
            </div>
        </div>
    
        <div class="row">
            <div class="col-md-8">
                <div class="form-group form-group-sm">
                    <label class="control-label col-md-3">&nbsp;</label>
                    <div class="col-md-5">
                        <select name="jenis_net" id="jenis_net" class="form-control select2" required>
                            <option></option>
                            <option value="3" <?php echo ($rsm['jenis_net'] == '3')?' selected':''; ?>>After Loading</option>
                            <option value="2" <?php echo ($rsm['jenis_net'] == '2')?' selected':''; ?>>After Delivery</option>
                            <option value="1" <?php echo ($rsm['jenis_net'] == '1')?' selected':''; ?>>After Invoice Receive</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    
    </div>
    
    <hr style="margin:15px 0px; border-top:4px double #ddd;" />

</div>

<?php if(!$rsm['finance_result']){ ?>
<div id="form_ce_fin_02" <?php echo ($rsm['jenis_datanya'] != '2' ? 'hidden' : '');?>>
    <div class="row">
        <div class="col-md-8">
            <div class="form-group form-group-sm">
                <label class="control-label col-md-3">Group of Company *</label>
                <div class="col-md-9">
                    <input type="text" name="evaluation_number[]" id="evaluation_number1" class="form-control" required />
                </div>
            </div>
        </div>
    </div>
    
    <p style="margin:0px 0px 5px;"><b>Verification Document *</b></p>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group form-group-sm">
                <div class="col-md-12">
                    <div class="radio clearfix" style="margin:0px;">
                        <label class="col-xs-12" style="margin-bottom:5px;">
                            <input type="checkbox" name="dokumen[]" id="dokumen1" value="1"<?php echo (in_array("1", $arrD))?' checked':'';?> /> Customer Data Base
                        </label>
                        <label class="col-xs-12" style="margin-bottom:5px;">
                            <input type="checkbox" name="dokumen[]" id="dokumen2" value="2"<?php echo (in_array("2", $arrD))?' checked':'';?> /> SIUP
                        </label>
                        <label class="col-xs-12" style="margin-bottom:5px;">
                            <input type="checkbox" name="dokumen[]" id="dokumen3" value="3"<?php echo (in_array("3", $arrD))?' checked':'';?> /> Notarial Deed
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group form-group-sm">
                <div class="col-md-12">
                    <div class="radio clearfix" style="margin:0px;">
                        <label class="col-xs-12" style="margin-bottom:5px;">
                            <input type="checkbox" name="dokumen[]" id="dokumen4" value="4"<?php echo (in_array("4", $arrD))?' checked':'';?> /> LCR
                        </label>
                        <label class="col-xs-12" style="margin-bottom:5px;">
                            <input type="checkbox" name="dokumen[]" id="dokumen5" value="5"<?php echo (in_array("5", $arrD))?' checked':'';?> /> NPWP
                        </label>
                        <label class="col-xs-12" style="margin-bottom:5px;">
                            <input type="checkbox" name="dokumen[]" id="dokumen6" value="6"<?php echo (in_array("6", $arrD))?' checked':'';?> /> Financial Statement
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group form-group-sm">
                <div class="col-md-12">
                    <div class="radio clearfix" style="margin:0px;">
                        <label class="col-xs-12" style="margin-bottom:5px;">
                            <input type="checkbox" name="dokumen[]" id="dokumen7" value="7"<?php echo (in_array("7", $arrD))?' checked':'';?> /> Customer Review
                        </label>
                        <label class="col-xs-12" style="margin-bottom:5px;">
                            <input type="checkbox" name="dokumen[]" id="dokumen8" value="8"<?php echo (in_array("8", $arrD))?' checked':'';?> /> TOP
                        </label>
                        <label class="col-xs-12" style="margin-bottom:5px;">
                            <input type="checkbox" name="dokumen[]" id="dokumen9" value="9"<?php echo (in_array("9", $arrD))?' checked':'';?> /> Others
                        </label>
                    </div>            
                    <input type="text" name="dok_lain" id="dok_lain" class="form-control" required value="<?php echo $eval[2];?>" <?php echo(!$eval[2])?'disabled':'';?> />
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="form-group form-group-sm">
                <label class="control-label col-md-3">Dokumen Lainnya</label>
                <div class="col-md-9">
                    <input type="text" name="dokumen_lainnya" id="dokumen_lainnya" class="form-control" value="<?php echo $rsm['dokumen_lainnya']; ?>" />
                </div>
            </div>
        </div>
    </div>
    
    <?php
        if($rsm['dokumen_lainnya_file']){
            $linknya = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=1&ktg=dokumen_lainnya_file".$rsm['id_customer']."_&file=".$rsm['dokumen_lainnya_file']);
            echo '
            <div id="tmp_file">
                <div class="row wrapper_file_pendukung">
                    <div class="col-md-8 num_file">
                        <div class="form-group">
                            <div class="col-md-3">&nbsp;</div>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <input type="text" class="form-control" readonly value="'.$rsm['dokumen_lainnya_file'].'" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" />
                                    <span class="input-group-btn">
                                        <a href="'.$linknya.'" class="btn btn-primary" target="_blank">
                                        &nbsp;<i class="fa fa-download"></i>&nbsp;</a>
                                        <button type="button" class="btn btn-danger ubah_file_pendukung">
                                        &nbsp;<i class="fa fa-times"></i>&nbsp;</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
        } else{
            echo '
            <div id="tmp_file">
                <div class="row wrapper_file_pendukung">
                    <div class="col-md-8 num_file">
                        <div class="form-group">
                            <div class="col-md-3">&nbsp;</div>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <input type="file" id="dokumen_lainnya_file_1" name="dokumen_lainnya_file[]" class="form-control" />
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-danger delete_file_pendukung">
                                        &nbsp;<i class="fa fa-times"></i>&nbsp;</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
        }
    ?>
    
    <hr style="margin:15px 0px; border-top:4px double #ddd;" />
    
    <p style="margin:20px 0px;"><b><u>LAMPIRAN DOKUMEN KYC</u></b></p>

    <?php
        echo '
        <div class="table-responsive">
            <table id="tabel_lampiran_kyc" class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center" width="80">No Urut</th>
                        <th class="text-center" width="350">Nama Lampiran</th>
                        <th class="text-center" width="">File Lampiran</th>
                        <th class="text-center" width="100">
                            <a class="btn btn-primary btn-sm add_volume"><i class="fa fa-plus"></i></a>
                        </th>
                    </tr>
                </thead>
                <tbody>
        ';
        $rowTerima  = json_decode($rsm['finance_data_kyc'], true);
        $arrTerima  = (is_array($rowTerima) && count($rowTerima) > 0) ? $rowTerima : array(array(""));
        $no_urut = 0;
        foreach($arrTerima as $idx=>$value){ 
            $no_urut++;
            $nom        = ($value['id_detail']) ? $value['id_detail'] : $no_urut;
            $pathFile   = $value['filenya'];
            $labelFile  = 'Unggah File';
            $dataIcons  = '<div style="width:45px; float:left;">&nbsp;</div>';
            
            if($value['file_upload_ori'] && file_exists($pathFile)){
                $labelFile  = 'Ubah File';
                $linkPt     = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=3&ktg=".$value['filenya']."&file=".$value['file_upload_ori']);
                $dataIcons  = '
                <div style="width:45px; float:left;">
                    <a href="'.$linkPt.'" target="_blank" class="btn btn-sm btn-success" title="download file" style="color: #fff;"> 
                    <i class="fa fa-download"></i></a>
                </div>';
            }

            echo '
            <tr data-id="'.$nom.'">
                <td class="text-center"><span class="frmnodasar" data-row-count="'.$nom.'">'.$no_urut.'</span></td>
                <td class="text-left">
                    <input type="text" id="nama_file_kyc'.$nom.'" name="nama_file_kyc['.$nom.']" class="form-control input-sm pic" value="'.$value['pic'].'" />
                </td>
                <td class="text-left">
                    <div class="rowuploadnya">
                        '.$dataIcons.'
                        <div class="simple-fileupload" style="margin-left:45px;">
                            <input type="file" name="attach_file_kyc['.$nom.']" id="attach_file_kyc'.$nom.'" class="form-inputfile" />
                            <label for="attach_file_kyc'.$nom.'" class="label-inputfile">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-addon btn-primary"><i class="fa fa-upload"></i></div>
                                    <input type="text" class="form-control" placeholder="'.$labelFile.'" readonly />
                                </div>
                            </label>
                        </div>
                    </div>
                </td>
                <td class="text-center">
                    <a class="btn btn-danger btn-sm del_volume"><span class="fa fa-trash"></span></a>
                </td>
            </tr>';
        }
        echo '</tbody></table></div>';
    ?>

    <p style="font-size:12px; margin:10px 0px 10px;">* Max size 2MB</p>
    <p style="font-size:12px; margin:0;">** Allowed file extension .jpg, .jpeg, .png, .zip, .pdf, .rar</p>
    
    <hr style="margin:15px 0px; border-top:4px double #ddd;" />
    
    <?php if($rsm['logistik_result']){ ?>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group form-group-sm">
                <label class="control-label col-md-4">Logistik Summary</label>
                <div class="col-md-8">
                    <div class="form-control" style="height:auto;">
                        <?php echo ($rsm['logistik_summary']); ?>
                        <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $rsm['logistik_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['logistik_tgl_proses']))." WIB";?></i></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group form-group-sm">
                <label class="control-label col-md-4">Logistik Result</label>
                <div class="col-md-8">
                    <div class="form-control" style="height:auto;"><?php echo $arrResult[$rsm['logistik_result']]; ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
    
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group form-group-sm">
                <label class="control-label col-md-4">Assessment Result *</label>
                <div class="col-md-8">
                    <div class="radio clearfix" style="margin:0px;">
                        <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="finance_result" id="finance_result1" required value="1" /> Supply Delivery</label>
                        <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="finance_result" id="finance_result2" required value="2" /> Supply Delivery With Note</label>
                        <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="finance_result" id="finance_result3" required value="3" /> Revised and Resubmitted</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } else{ ?>

<div class="row">
    <div class="col-md-8">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-3">Group of Company *</label>
            <div class="col-md-9">
                <div class="form-control" style="height:auto"><?php echo $eval[0]['nomor']; ?></div>
            </div>
        </div>
    </div>
</div>

<p style="margin:0px 0px 5px;"><b>Verification Document *</b></p>
<div class="row">
    <div class="col-md-4">
        <div class="form-group form-group-sm">
            <div class="col-md-12">
                <div class="radio clearfix" style="margin:0px;">
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="checkbox" name="dokumen[]" id="dokumen1" value="1"<?php echo (in_array("1", $arrD))?' checked disabled':' disabled';?> /> Customer Data Base
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="checkbox" name="dokumen[]" id="dokumen2" value="2"<?php echo (in_array("2", $arrD))?' checked disabled':' disabled';?> /> SIUP
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="checkbox" name="dokumen[]" id="dokumen3" value="3"<?php echo (in_array("3", $arrD))?' checked disabled':' disabled';?> /> Notarial Deed
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group form-group-sm">
            <div class="col-md-12">
                <div class="radio clearfix" style="margin:0px;">
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="checkbox" name="dokumen[]" id="dokumen4" value="4"<?php echo (in_array("4", $arrD))?' checked disabled':' disabled';?> /> LCR
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="checkbox" name="dokumen[]" id="dokumen5" value="5"<?php echo (in_array("5", $arrD))?' checked disabled':' disabled';?> /> NPWP
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="checkbox" name="dokumen[]" id="dokumen6" value="6"<?php echo (in_array("6", $arrD))?' checked disabled':' disabled';?> /> Financial Statement
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group form-group-sm">
            <div class="col-md-12">
                <div class="radio clearfix" style="margin:0px;">
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="checkbox" name="dokumen[]" id="dokumen7" value="7"<?php echo (in_array("7", $arrD))?' checked disabled':' disabled';?> /> Customer Review
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="checkbox" name="dokumen[]" id="dokumen8" value="8"<?php echo (in_array("8", $arrD))?' checked disabled':' disabled';?> /> TOP
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="checkbox" name="dokumen[]" id="dokumen9" value="9"<?php echo (in_array("9", $arrD))?' checked disabled':' disabled';?> /> Others
                    </label>
                </div>            
                <?php echo ($eval[2])?'<div class="form-control" style="height:auto">'.$eval[2].'</div>':''; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-3">Dokumen Lainnya</label>
            <div class="col-md-9">
                <?php echo '<div class="form-control" style="height:auto">'.($rsm['dokumen_lainnya'] ? $rsm['dokumen_lainnya'] : '&nbsp;').'</div>'; ?>
            </div>
        </div>
    </div>
</div>

<?php
    if($rsm['dokumen_lainnya_file']){
        $linknya = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=1&ktg=dokumen_lainnya_file".$rsm['id_customer']."_&file=".$rsm['dokumen_lainnya_file']);
        echo '
        <div id="tmp_file">
            <div class="row wrapper_file_pendukung">
                <div class="col-md-8 num_file">
                    <div class="form-group">
                        <div class="col-md-3">&nbsp;</div>
                        <div class="col-md-9">
                            <div class="input-group">
                                <input type="text" class="form-control" readonly value="'.$rsm['dokumen_lainnya_file'].'" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" />
                                <span class="input-group-btn">
                                    <a href="'.$linknya.'" class="btn btn-primary" target="_blank">
                                    &nbsp;<i class="fa fa-download"></i>&nbsp;</a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    } else{
        echo '
        <div id="tmp_file">
            <div class="row wrapper_file_pendukung">
                <div class="col-md-8 num_file">
                    <div class="form-group">
                        <div class="col-md-3">&nbsp;</div>
                        <div class="col-md-9">
                            <input type="text" id="dokumen_lainnya_file_1" name="dokumen_lainnya_file[]" class="form-control" readonly />
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }
?>

<hr style="margin:15px 0px; border-top:4px double #ddd;" />

<p style="margin:20px 0px;"><b><u>LAMPIRAN DOKUMEN KYC</u></b></p>

<?php
    echo '<div class="row"><div class="col-md-8">
    <div class="table-responsive">
        <table id="tabel_lampiran_kyc" class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-center" width="80">No Urut</th>
                    <th class="text-center" width="">Nama Lampiran</th>
                    <th class="text-center" width="100">File</th>
                </tr>
            </thead>
            <tbody>
    ';
    $rowTerima  = json_decode($rsm['finance_data_kyc'], true);
    $arrTerima  = (is_array($rowTerima) && count($rowTerima) > 0) ? $rowTerima : array(array(""));
    $no_urut = 0;
    foreach($arrTerima as $idx=>$value){ 
        $no_urut++;
        $nom        = ($value['id_detail']) ? $value['id_detail'] : $no_urut;
        $pathFile   = $value['filenya'];
        $labelFile  = 'Unggah File';
        $dataIcons  = '<div style="width:45px; float:left;">&nbsp;</div>';
        
        if($value['file_upload_ori'] && file_exists($pathFile)){
            $labelFile  = 'Ubah File';
            $linkPt     = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=3&ktg=".$value['filenya']."&file=".$value['file_upload_ori']);
            $dataIcons  = '
            <div>
                <a href="'.$linkPt.'" target="_blank" class="btn btn-sm btn-success" title="download file" style="color: #fff;"> 
                <i class="fa fa-download"></i></a>
            </div>';
        }

        echo '
        <tr data-id="'.$nom.'">
            <td class="text-center"><span class="frmnodasar" data-row-count="'.$nom.'">'.$no_urut.'</span></td>
            <td class="text-left">
                <input type="text" id="nama_file_kyc'.$nom.'" name="nama_file_kyc['.$nom.']" class="form-control input-sm pic" readonly value="'.$value['nama_file'].'" />
            </td>
            <td class="text-center"><div class="rowuploadnya">'.$dataIcons.'</div>
            </td>
        </tr>';
    }
    echo '</tbody></table></div></div></div>';
?>

<hr style="margin:15px 0px; border-top:4px double #ddd;" />

<?php if($rsm['logistik_result']){ ?>
<div class="row">
    <div class="col-md-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Logistik Summary</label>
            <div class="col-md-8">
                <div class="form-control" style="height:auto;">
                    <?php echo ($rsm['logistik_summary']); ?>
                    <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $rsm['logistik_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['logistik_tgl_proses']))." WIB";?></i></p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Logistik Result</label>
            <div class="col-md-8">
                <div class="form-control" style="height:auto;"><?php echo $arrResult[$rsm['logistik_result']]; ?></div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<div class="row">
    <div class="col-md-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Admin Finance Summary</label>
            <div class="col-md-8">
                <div class="form-control" style="height:auto;">
                    <?php echo ($rsm['finance_summary']); ?>
                    <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $rsm['finance_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['finance_tgl_proses']))." WIB";?></i></p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Admin Finance Result</label>
            <div class="col-md-8">
                <div class="form-control" style="height:auto;"><?php echo $arrResult[$rsm['finance_result']]; ?></div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<?php if($rsm['sm_result']) {?>
<div class="row">
    <div class="col-md-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Logistik Summary</label>
            <div class="col-md-8">
                <div class="form-control" style="height:auto;">
                    <?php echo ($rsm['sm_summary']); ?>
                    <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $rsm['sm_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['sm_tgl_proses']))." WIB";?></i></p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Logistik Result</label>
            <div class="col-md-8">
                <div class="form-control" style="height:auto;"><?php echo $arrSetuju[$rsm['sm_result']]; ?></div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<?php if($rsm['om_result']) {?>
<div class="row">
    <div class="col-md-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Operation Manager Summary</label>
            <div class="col-md-8">
                <div class="form-control" style="height:auto;">
                    <?php echo ($rsm['om_summary']); ?>
                    <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $rsm['om_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['om_tgl_proses']))." WIB";?></i></p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Operation Manager Result</label>
            <div class="col-md-8">
                <div class="form-control" style="height:auto;"><?php echo $arrSetuju[$rsm['om_result']]; ?></div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<hr style="margin:15px 0px; border-top:4px double #ddd;" />

<div style="margin-bottom:0px;">
    <input type="hidden" name="idr" value="<?php echo $idr;?>" />
    <?php if(!$rsm['finance_result']){ ?>
        <button type="submit" name="btnSbmt" id="btnSbmt" class="btn btn-primary jarak-kanan" style="min-width:90px;">
        <i class="fa fa-save jarak-kanan"></i> Simpan</button> 
    <?php } ?>

    <a class="btn btn-default jarak-kanan" style="min-width:90px;" href="<?php echo BASE_URL_CLIENT."/verifikasi-data-customer.php";?>">
    <i class="fa fa-reply jarak-kanan"></i> Batal</a>

    <?php //if($rsm['finance_result']){ ?>
    <a class="btn btn-info jarak-kanan" target="_blank" style="min-width:90px;" href="<?php echo $linkCetak;?>">
    <i class="fa fa-print jarak-kanan"></i> Cetak Credit Application</a> 
    <?php //} ?>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $("#credit_limit, #getData4, #getData3").number(true, 0, ".", ",");
    $('#jenis_payment').change(function(e) {
        if ($('#jenis_payment').val()=='CREDIT') {
            $('#span_top').show();
        }else{
            $('#span_top').hide();
        }
    });

    $("#jenis_datanya").on("change", function(e){
        let nilai = $(this).val();
        if(nilai == '2'){
            $("#form_ce_fin_01").show();
            $("#form_ce_fin_02").show();
        } else{
            $("#form_ce_fin_01").hide();
            $("#form_ce_fin_02").hide();
        }
    });

    $("#tmp_file").on("click", ".delete_file_pendukung", function(){
        $("#dokumen_lainnya_file_1").val("");
    }).on("click", ".ubah_file_pendukung", function(){
        var tabel   = $(this).parents(".wrapper_file_pendukung").first();
        var arrId   = tabel.find(".frmnodasar").data("urut");
        var rwNom   = parseInt(arrId);
        var newId   = rwNom;
        
        var isiannya = 
        '<div class="col-md-8 num_file">'+
            '<div class="form-group">'+
                '<div class="col-md-3">&nbsp;</div>'+
                '<div class="col-md-9">'+
                    '<div class="input-group">'+
                        '<input type="file" id="dokumen_lainnya_file_1" name="dokumen_lainnya_file[]" class="form-control" />'+
                        '<span class="input-group-btn">'+
                            '<button type="button" class="btn btn-danger delete_file_pendukung">'+
                            '&nbsp;<i class="fa fa-times"></i>&nbsp;</button>'+
                        '</span>'+
                    '</div>'+ 
                '</div>'+
            '</div>'+
        '</div>';
        tabel.html(isiannya);
    });

    $("#tabel_lampiran_kyc").on("click", ".add_volume", function(){
        var tabel   = $("#tabel_lampiran_kyc");
        var arrId   = tabel.find("tbody > tr").map(function(){ 
            return parseFloat($(this).data("id")) || 0; 
        }).toArray();
        var rwNom   = Math.max.apply(Math, arrId);
        var newId   = (rwNom == 0) ? 1 : (rwNom+1);

        var isiHtml = 
        '<tr data-id="'+newId+'">'+
            '<td class="text-center"><span class="frmnodasar" data-row-count="'+newId+'"></span></td>'+
            '<td class="text-left">'+
                '<input type="text" id="nama_file_kyc'+newId+'" name="nama_file_kyc['+newId+']" class="form-control input-sm pic" />'+
            '</td>'+
            '<td class="text-left">'+
                '<div class="rowuploadnya">'+
                    '<div style="width:45px; float:left;">&nbsp;</div>'+
                    '<div class="simple-fileupload" style="margin-left:45px;">'+
                        '<input type="file" name="attach_file_kyc['+newId+']" id="attach_file_kyc'+newId+'" class="form-inputfile" />'+
                        '<label for="attach_file_kyc'+newId+'" class="label-inputfile">'+
                            '<div class="input-group input-group-sm">'+
                                '<div class="input-group-addon btn-primary"><i class="fa fa-upload"></i></div>'+
                                '<input type="text" class="form-control" placeholder="Unggah File" readonly />'+
                            '</div>'+
                        '</label>'+
                    '</div>'+
                '</div>'+
            '</td>'+
            '<td class="text-center">'+
                '<a class="btn btn-danger btn-sm del_volume"><span class="fa fa-trash"></span></a>'+
            '</td>'+
        '</tr>';
        if(rwNom == 0){
            tabel.find('tbody').html(isiHtml);
        } else{
            tabel.find('tbody > tr:last').after(isiHtml);
        }
        tabel.find("span.frmnodasar").each(function(i,v){$(v).text(i+1);});
    }).on("click", ".del_volume", function(){
        var tabel   = $("#tabel_lampiran_kyc");
        var jTbl    = tabel.find('tbody > tr').length;
        if(jTbl > 1){
            var cRow = $(this).closest('tr');
            cRow.remove();
            tabel.find("span.frmnodasar").each(function(i,v){$(v).text(i+1);});
            calculate_volterima();
        }
    });

});
</script>

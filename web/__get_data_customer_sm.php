<?php 
	$arrResult 	= array(1=>"Supply Delivery", "Supply Delivery With Note", "Revised and Resubmitted","Yes","No");
	$arrSetuju 	= array(1=>"Yes", "No");	
	$arrTipeBisnis 	= array(
		1=>"Agriculture & Forestry / Horticulture", "Business & Information", "Construction/Utilities/Contracting", "Education", 
		"Finance & Insurance", "Food & hospitally", "Gaming", "Health Services", 
		"Motor Vehicle", $rsm['tipe_bisnis_lain'], "Natural Resources / Environmental", "Personal Service", "Manufacture"
	);
	$tipebisnis 	= ($arrTipeBisnis[$rsm['tipe_bisnis']] ? $arrTipeBisnis[$rsm['tipe_bisnis']] : '-');

    $jenis_net	= $rsm['jenis_net'];
    $eval 		= json_decode($rsm['legal_data'], true);
	$sert 		= isset($eval[0])?$eval[0]['nomor']:$rsm['nomor_sertifikat'];
	$npwp 		= isset($eval[1])?$eval[1]['nomor']:$rsm['nomor_npwp'];
	$siup 		= isset($eval[2])?$eval[2]['nomor']:$rsm['nomor_siup'];
	$tdpn 		= isset($eval[3])?$eval[3]['nomor']:$rsm['nomor_tdp'];
	$arrKondInd	= array(1=>"Setelah Invoice diterima", "Setelah pengiriman", "Setelah loading");
	$arrKondEng = array(1=>"After Invoice Receive", "After Delivery", "After Loading");
	$arrPayment = array("CREDIT"=>"CREDIT ".$rsm['top_payment']." days ".$arrKondEng[$jenis_net], "CBD"=>"CBD (Cash Before Delivery)", "COD"=>"COD (Cash On Delivery)");

	$arrTipeBisnis 	= array(
		1=>"Agriculture & Forestry / Horticulture", "Business & Information", "Construction/Utilities/Contracting", "Education", 
		"Finance & Insurance", "Food & hospitally", "Gaming", "Health Services", 
		"Motor Vehicle", $rsm['tipe_bisnis_lain'], "Natural Resources / Environmental", "Personal Service", "Manufacture"
	);
	$tipebisnis 	= ($arrTipeBisnis[$rsm['tipe_bisnis']] ? $arrTipeBisnis[$rsm['tipe_bisnis']] : '-');

	$eval 		= json_decode($rsm['finance_data'], true);
	$arrD 		= $eval?explode(",", $eval[1]["nomor"]):[];
	
	$jenis_net	= $rsm['jenis_net'];
	$arrKondInd	= array(1=>"Setelah Invoice diterima", "Setelah pengiriman", "Setelah loading");
	$arrKondEng = array(1=>"After Invoice Receive", "After Delivery", "After Loading");
	$arrPayment = array("CREDIT"=>"CREDIT ".$rsm['top_payment']." days ".($rsm['jenis_net']>0?$arrKondEng[$jenis_net]:''), "CBD"=>"CBD (Cash Before Delivery)", "COD"=>"COD (Cash On Delivery)");
	$linkCetak 	= ACTION_CLIENT."/credit-app-form-cetak.php?".paramEncrypt("idr=".$idr."&idk=".$rsm['id_review']);
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
			<?php 
                if($rsm['jenis_payment'] == 'CBD') $currval = 'CBD (Cash Before Delivery)';
                else if($rsm['jenis_payment'] == 'COD') $currval = 'COD (Cash On Delivery)';
                else if($rsm['jenis_payment'] == 'CREDIT') $currval = 'CREDIT';
                echo '<input type="text" class="form-control" value="'.$currval.'" readonly />';
            ?>
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
                    <input type="text" class="form-control hitung" readonly value="<?php echo($rsm['top_payment'])?>" />
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="form-group form-group-sm">
                <label class="control-label col-md-3">&nbsp;</label>
                <div class="col-md-5">
				<?php 
                    if($rsm['jenis_net'] == '1') $currval = 'After Invoice Receive';
                    else if($rsm['jenis_net'] == '2') $currval = 'After Delivery';
                    else if($rsm['jenis_net'] == '3') $currval = 'After Loading';
                    echo '<input type="text" class="form-control" value="'.$currval.'" readonly />';
                ?>
                </div>
            </div>
        </div>
    </div>

</div>

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
    $rowTerima 	= json_decode($rsm['finance_data_kyc'], true);
    $arrTerima 	= (is_array($rowTerima) && count($rowTerima) > 0) ? $rowTerima : array(array(""));
    $no_urut = 0;
    foreach($arrTerima as $idx=>$value){ 
        $no_urut++;
        $nom 		= ($value['id_detail']) ? $value['id_detail'] : $no_urut;
        $pathFile 	= $value['filenya'];
        $labelFile 	= 'Unggah File';
        $dataIcons 	= '<div style="width:45px; float:left;">&nbsp;</div>';
        
        if($value['file_upload_ori'] && file_exists($pathFile)){
            $labelFile 	= 'Ubah File';
            $linkPt 	= ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=3&ktg=".$value['filenya']."&file=".$value['file_upload_ori']);
            $dataIcons 	= '
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

<?php if(!$rsm['sm_result']){ ?>
<div class="row">
    <div class="col-md-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Branch Manager Summary *</label>
            <div class="col-md-8">
                <textarea name="sm_summary" id="sm_summary" class="form-control" style="height:90px;" required></textarea>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Branch Manager Result *</label>
            <div class="col-md-8">
                <div class="radio clearfix" style="margin:0px;">
                    <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="sm_result" id="sm_result1" value="1" required /> Yes</label>
                    <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="sm_result" id="sm_result2" value="2" required /> No</label>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } else{ ?>
<div class="row">
    <div class="col-md-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Branch Manager Summary</label>
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
            <label class="control-label col-md-4">Branch Manager Result</label>
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
    <?php if(!$rsm['sm_result']){ ?>
        <button type="submit" name="btnSbmt" id="btnSbmt" class="btn btn-primary jarak-kanan" style="min-width:90px;">
        <i class="fa fa-save jarak-kanan"></i> Simpan</button> 
    <?php } ?>
    <a class="btn btn-default" style="min-width:90px;" href="<?php echo BASE_URL_CLIENT."/verifikasi-data-customer.php";?>">
    <i class="fa fa-reply jarak-kanan"></i> Batal</a>
</div>

<script type="text/javascript">
$(document).ready(function(){
	$("#credit_limit, #getData4, #getData3").number(true, 0, ".", ",");
});
</script>

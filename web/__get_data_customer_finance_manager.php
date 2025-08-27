<?php
	$arrResult 	= array(1=>"Supply Delivery", "Supply Delivery With Note", "Revised and Resubmitted","Yes","No");
	$arrSetuju 	= array(1=>"Yes", "No");	
	$arrT 		= array(1=>"Agriculture & Forestry / Horticulture", "Business & Information", "Construction/Utilities/Contracting", "Education", "Finance & Insurance", 
					"Food & hospitally", "Gaming", "Health Services", "Motor Vehicle", $rsm['tipe_bisnis_lain'],"Natural Resources / Environmental","Personal Service");
	$eval 		= json_decode($rsm['finance_data'], true);
	$arrD 		= explode(",", $eval[1]["nomor"]);
	
	$jenis_net	= $rsm['jenis_net'];
	$arrKondInd	= array(1=>"Setelah Invoice diterima", "Setelah pengiriman", "Setelah loading");
	$arrKondEng = array(1=>"After Invoice Receive", "After Delivery", "After Loading");
	$arrPayment = array("CREDIT"=>"CREDIT ".$rsm['top_payment']." days ".$arrKondEng[$jenis_net], "CBD"=>"CBD (Cash Before Delivery)", "COD"=>"COD (Cash On Delivery)");
?>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label>Customer Name</label>
            <input type="text" name="getData0" id="getData0" class="form-control" readonly value="<?php echo $rsm['nama_customer']; ?>" />
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Business Type</label>
            <input type="text" name="getData1" id="getData1" class="form-control" readonly value="<?php echo $arrT[$rsm['tipe_bisnis']]; ?>" />
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
        <div class="form-group">
            <label>TOP</label>
            <input type="text" name="getData2" id="getData2" class="form-control" readonly value="<?php echo $arrPayment[$rsm['jenis_payment']]; ?>" />
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
            <label>Potensial Volume</label>
             <div class="input-group">
				<input type="text" name="getData3" id="getData3" class="form-control hitung" readonly value="<?php echo $rsm['review9'];?>" />
				<span class="input-group-addon">Liter</span>
			</div> 
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
            <label>Pengajuan Kredit Limit</label>
			<div class="input-group">
				<span class="input-group-addon">Rp.</span>
				<input type="text" name="getData4" id="getData4" class="form-control hitung" readonly value="<?php echo $rsm['credit_limit_diajukan'];?>" />
			</div>
        </div>
    </div>
	<div class="col-sm-3">
        <div class="form-group">
            <label>Persetujuan Kredit Limit</label>
			<div class="input-group">
				<span class="input-group-addon">Rp.</span>
				<input type="text" name="credit_limit" id="credit_limit" class="form-control validate[required] hitung" readonly value="<?php echo $rsm['credit_limit'];?>" /><!-- readonly -->
			</div>
        </div>
    </div>
</div>
<hr style="margin:10px 0px 20px; border-top:4px double #ddd;" />

<div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label>Group of Company *</label>
                <div class="form-control" style="height:auto"><?php echo $eval[0]['nomor']; ?></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <label>Verification Document *</label>
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
        <div class="col-sm-4">
            <div class="form-group">
                <label>&nbsp;</label>
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
        <div class="col-sm-4">
            <div class="form-group">
                <label>&nbsp;</label>
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
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label>Dokumen Lainnya</label>
                <input type="text" name="dokumen_lainnya" id="dokumen_lainnya" disabled class="form-control" value="<?php echo $rsm['dokumen_lainnya']; ?>" />
            </div>
            <div id="dokumen_lainnya_file_wrap" class="file-wrap">
            <?php 
                for($i = 0; $i < count($alldokumen); $i++) {
                    $allddokumen[] = $base_directory."/dokumen_lainnya_file".$rsm["id_customer"]."_".$alldokumenraw[$i];
                    if($rsm['dokumen_lainnya_file'] && file_exists($alldokumen_link[$i])){
                        if($alldokumen[$i]) {
                            $linkDokumenLainnya = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=1&ktg=dokumen_lainnya_file".$rsm["id_customer"]."_&file=".$alldokumen[$i]);
                            echo '<br><div class="preview-file"><a href="'.$linkDokumenLainnya.'">'.str_replace("_", "", $alldokumen[$i]).'</a>

                                    </div>';
                        }
                    }
                }
            ?>
            <br>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label>Summary Assessment *</label>
                <div class="form-control" style="height:auto">
                    <?php echo ($rsm['finance_summary']); ?>
                    <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $rsm['finance_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['finance_tgl_proses']))." WIB";?></i></p>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>Assessment Result *</label>
                <p><?php echo $arrResult[$rsm['finance_result']];?></p>
            </div>
        </div>
    </div>

<script type="text/javascript">
$(document).ready(function(){
    $("#credit_limit, #getData4, #getData3").number(true, 0, ".", ",");
});
</script>

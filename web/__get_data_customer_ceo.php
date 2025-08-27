<?php
	$arrResult 	= array(1=>"Supply Delivery", "Supply Delivery With Note", "Revised and Resubmitted","Yes","No");
	$arrSetuju 	= array(1=>"Yes", "No");	
	$arrT 		= array(1=>"Agriculture & Forestry / Horticulture", "Business & Information", "Construction/Utilities/Contracting", "Education", "Finance & Insurance", 
					"Food & hospitally", "Gaming", "Health Services", "Motor Vehicle", $rsm['tipe_bisnis_lain'],"Natural Resources / Environmental","Personal Service","Manufacture");
	$arrKondInd	= array(1=>"Setelah Invoice diterima", "Setelah pengiriman", "Setelah loading");
	$arrKondEng = array(1=>"After Invoice Receive", "After Delivery", "After Loading");
	$jenis_net	= $rsm['jenis_net'];
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
            <label>Persetujuan Kredit Limit *</label>
			<div class="input-group">
				<span class="input-group-addon">Rp.</span>
				<input type="text" name="credit_limit" id="credit_limit" class="form-control validate[required] hitung" <?php echo ($rsm['ceo_result'] ||  $rsm['is_approved'] > 0)?"readonly":""; ?> value="<?php echo $rsm['credit_limit'];?>" />
			</div>
        </div>
    </div>
</div>
<hr style="margin:10px 0px 20px; border-top:4px double #ddd;" />

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label>Manager Finance Summary</label>
            <div class="form-control" style="height:auto">
				<?php echo ($rsm['legal_summary']); ?>
                <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $rsm['legal_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['legal_tgl_proses']))." WIB";?></i></p>
			</div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Manager Finance Result</label>
            <p><?php echo $arrResult[$rsm['legal_result']]; ?></p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label>Admin Finance Summary</label>
            <div class="form-control" style="height:auto">
				<?php echo ($rsm['finance_summary']); ?>
                <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $rsm['finance_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['finance_tgl_proses']))." WIB";?></i></p>
			</div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Admin Finance Result</label>
            <p><?php echo $arrResult[$rsm['finance_result']]; ?></p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label>Logistik Summary</label>
            <div class="form-control" style="height:auto">
				<?php echo ($rsm['logistik_summary']); ?>
                <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $rsm['logistik_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['logistik_tgl_proses']))." WIB";?></i></p>
			</div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Logistik Result</label>
            <p><?php echo $arrResult[$rsm['logistik_result']]; ?></p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label>Branch Manager Summary</label>
            <div class="form-control" style="height:auto">
				<?php echo ($rsm['sm_summary']); ?>
                <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $rsm['sm_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['sm_tgl_proses']))." WIB";?></i></p>
			</div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Branch Manager Result</label>
            <p><?php echo $arrSetuju[$rsm['sm_result']]; ?></p>
        </div>
    </div>
</div>

<?php if($rsm['om_result']){?>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label>Operation Manager Summary</label>
                <div class="form-control" style="height:auto">
                    <?php echo ($rsm['om_summary']); ?>
                    <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $rsm['om_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['om_tgl_proses']))." WIB";?></i></p>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>Operation Manager Result</label>
                <p><?php echo $arrSetuju[$rsm['om_result']]; ?></p>
            </div>
        </div>
    </div>
<?php }?>

<?php if($rsm['cfo_result']){?>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label>CFO Summary</label>
                <div class="form-control" style="height:auto">
                    <?php echo ($rsm['cfo_summary']); ?>
                    <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $rsm['cfo_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['cfo_tgl_proses']))." WIB";?></i></p>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>CFO Approval</label>
                <p><?php echo $arrSetuju[$rsm['cfo_result']]; ?></p>
            </div>
        </div>
    </div>
<?php }?>

<?php if(!$rsm['ceo_result'] && $rsm['disposisi_result'] == 5) {?>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label>CEO Summary *</label>
                <textarea name="ceo_summary" id="ceo_summary" class="form-control validate[required]"></textarea>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>CEO Approval *</label>
                <div class="radio clearfix" style="margin:0px;">
                    <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="ceo_result" id="ceo_result1" class="validate[required]" value="1" /> Yes</label>
                    <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="ceo_result" id="ceo_result2" class="validate[required]" value="2" /> No</label>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="pad bg-gray">
                <input type="hidden" name="idr" value="<?php echo $idr;?>" />
                <a href="<?php echo BASE_URL_CLIENT."/verifikasi-data-customer.php"; ?>" class="btn btn-default jarak-kanan">
                <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                <button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Submit</button>
            </div>
        </div>
    </div>
    <hr style="margin:5px 0" /><div class="row"><div class="col-sm-12"><small>* Wajib Diisi</small></div></div>
<?php } else { ?>
    <?php if($rsm['ceo_pic']) { ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label>CEO Summary *</label>
                    <div class="form-control" style="height:auto">
                        <?php echo ($rsm['ceo_summary']); ?>
                        <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $rsm['ceo_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['ceo_tgl_proses']))." WIB";?></i></p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>CEO Approval *</label>
                    <p><?php echo $arrSetuju[$rsm['ceo_result']]; ?></p>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) != '11' && paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) != '17'){ ?>
        <div class="row">
            <div class="col-sm-12">
                <div class="pad bg-gray">
                    <a href="<?php echo BASE_URL_CLIENT."/verifikasi-data-customer.php"; ?>" class="btn btn-default jarak-kanan">
                    <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } ?>

<script type="text/javascript">
$(document).ready(function(){
	$("#credit_limit, #getData4, #getData3").number(true, 0, ".", ",");
});
</script>

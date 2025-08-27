<?php
	$arrResult 	= array(1=>"Supply Delivery", "Supply Delivery With Note", "Revised and Resubmitted","Yes","No");
	$arrSetuju 	= array(1=>"Ya", "Tidak");
	$dt1 		= str_replace("<br />", PHP_EOL, $rsm['sm_evaluasi1']);
	$dt2 		= str_replace("<br />", PHP_EOL, $rsm['sm_evaluasi2']);
	$dt3 		= str_replace("<br />", PHP_EOL, $rsm['sm_evaluasi3']);
	$tmp 		= json_decode($rsm['sm_summary'], true);
	$summary 	= str_replace("<br />", PHP_EOL, $tmp['summary']);
	$smp1 = json_decode($rsm['marketing_summary'], true);
	$smp2 = json_decode($rsm['finance_summary'], true);
	$smp3 = json_decode($rsm['logistik_summary'], true);
?>
<h3 class="form-title bg-light-blue">MARKETING</h3>
<div class="bungkus-title">
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Bulan ke-1</label>
                <div class="form-control" style="height:auto"><?php echo str_replace("<br />", PHP_EOL, $rsm['marketing_evaluasi1']); ?></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Bulan ke-2</label>
                <div class="form-control" style="height:auto"><?php echo str_replace("<br />", PHP_EOL, $rsm['marketing_evaluasi2']); ?></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Bulan ke-3</label>
                <div class="form-control" style="height:auto"><?php echo str_replace("<br />", PHP_EOL, $rsm['marketing_evaluasi3']); ?></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label>Marketing Summary</label>
                <div class="form-control" style="height:auto">
                    <?php echo str_replace("<br />", PHP_EOL, $smp1['summary']); ?>
                    <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $smp1['pic']." - ".$smp1['tanggal']." WIB";?></i></p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-sm-top">
            <div class="form-group">
                <label>Marketing Result</label>
                <p><?php echo $arrResult[$rsm['marketing_result']]; ?></p>
            </div>
        </div>
    </div>
</div>

<h3 class="form-title bg-light-blue">FINANCE</h3>
<div class="bungkus-title">
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Bulan ke-1</label>
                <div class="form-control" style="height:auto"><?php echo str_replace("<br />", PHP_EOL, $rsm['finance_evaluasi1']); ?></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Bulan ke-2</label>
                <div class="form-control" style="height:auto"><?php echo str_replace("<br />", PHP_EOL, $rsm['finance_evaluasi2']); ?></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Bulan ke-3</label>
                <div class="form-control" style="height:auto"><?php echo str_replace("<br />", PHP_EOL, $rsm['finance_evaluasi3']); ?></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label>Finance Summary</label>
                <div class="form-control" style="height:auto">
                    <?php echo str_replace("<br />", PHP_EOL, $smp2['summary']); ?>
                    <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $smp2['pic']." - ".$smp2['tanggal']." WIB";?></i></p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-sm-top">
            <div class="form-group">
                <label>Finance Result</label>
                <p><?php echo $arrResult[$rsm['finance_result']]; ?></p>
            </div>
        </div>
    </div>
</div>

<h3 class="form-title bg-light-blue">LOGISTIK</h3>
<div class="bungkus-title">
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Bulan ke-1</label>
                <div class="form-control" style="height:auto"><?php echo str_replace("<br />", PHP_EOL, $rsm['logistik_evaluasi1']); ?></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Bulan ke-2</label>
                <div class="form-control" style="height:auto"><?php echo str_replace("<br />", PHP_EOL, $rsm['logistik_evaluasi2']); ?></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Bulan ke-3</label>
                <div class="form-control" style="height:auto"><?php echo str_replace("<br />", PHP_EOL, $rsm['logistik_evaluasi3']); ?></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label>Logistik Summary</label>
                <div class="form-control" style="height:auto">
                    <?php echo str_replace("<br />", PHP_EOL, $smp3['summary']); ?>
                    <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $smp3['pic']." - ".$smp3['tanggal']." WIB";?></i></p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-sm-top">
            <div class="form-group">
                <label>Logistik Result</label>
                <p><?php echo $arrResult[$rsm['logistik_result']]; ?></p>
            </div>
        </div>
    </div>
</div>

<?php if(!$rsm['sm_result']){ ?>
<div class="form-group row">
    <div class="col-sm-6">
        <label>Catatan BM</label>
        <textarea name="summary" id="summary" class="form-control"><?php echo $summary;?></textarea>
    </div>
    <div class="col-sm-6 col-sm-top">
        <label>Persetujuan BM</label>
        <div class="radio clearfix" style="margin:0px;">
            <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="result" id="result1" value="1" /> Ya</label>
            <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="result" id="result2" value="2" /> Tidak</label>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="pad bg-gray">
            <input type="hidden" name="act" value="<?php echo $action;?>" />
            <input type="hidden" name="idr" value="<?php echo $idr;?>" />
            <input type="hidden" name="idk" value="<?php echo $idk;?>" />
            <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT."/evaluasi-data-customer.php";?>">
            <i class="fa fa-reply jarak-kanan"></i>Kembali</a>
            <button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
        </div>
    </div>
</div>
<?php } else{ ?>
	<div class="form-group row">
		<div class="col-sm-6">
			<label>Catatan BM</label>
			<div class="form-control" style="height:auto">
				<?php echo $tmp['summary']; ?><p style="margin:10px 0 0; font-size:12px;"><i><?php echo $tmp['pic']." - ".$tmp['tanggal']." WIB";?></i></p>
            </div>
		</div>
		<div class="col-sm-6 col-sm-top">
			<label>Persetujuan BM</label>
			<div class="radio clearfix" style="margin:0px;">
				<label class="col-xs-12" style="margin-bottom:5px;">
					<input type="radio" name="result" id="result" value="1" checked /><?php echo $arrSetuju[$rsm['sm_result']];?>
				</label>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="pad bg-gray">
				<a href="<?php echo BASE_URL_CLIENT."/evaluasi-data-customer.php"; ?>" class="btn btn-default jarak-kanan">
				<i class="fa fa-reply jarak-kanan"></i> Kembali</a>
			</div>
		</div>
	</div>
<?php } ?>
<style type="text/css">
	h3.form-title {
		 font-size: 18px;
		 margin: 0px;
		 font-weight: 700;
		 padding: 10px;
	}
	.bungkus-title{
		padding:10px 20px 0px;
		border:1px solid #ddd;
		border-top:none;
		margin-bottom:15px;
	}
</style>
<script>
	$(document).ready(function(){
		$("form#gform").validationEngine('attach',{
			onValidationComplete: function(form, status){
				if(status == true){
					$('#loading_modal').modal({backdrop:"static"});
					form.validationEngine('detach');
					form.submit();
				}
			}
		});
	});		
</script>

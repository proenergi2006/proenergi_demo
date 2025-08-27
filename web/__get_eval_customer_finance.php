<?php
	$arrResult 	= array(1=>"Supply Delivery", "Supply Delivery With Note", "Revised and Resubmitted","Yes","No");
	$arrSetuju 	= array(1=>"Yes", "No");
	$dt1 		= str_replace("<br />", PHP_EOL, $rsm['finance_evaluasi1']);
	$dt2 		= str_replace("<br />", PHP_EOL, $rsm['finance_evaluasi2']);
	$dt3 		= str_replace("<br />", PHP_EOL, $rsm['finance_evaluasi3']);
	$tmp 		= json_decode($rsm['finance_summary'], true);
	$summary 	= str_replace("<br />", PHP_EOL, $tmp['summary']);
	$smp1 = json_decode($rsm['marketing_summary'], true);
?>
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
<?php if(!$rsm['finance_result']){ ?>
<div class="form-group row">
    <div class="col-sm-10">
        <label>Bulan ke-1</label>
        <textarea name="dt1" id="dt1" class="form-control"><?php echo $dt1;?></textarea>
    </div>
</div>
<div class="form-group row">
    <div class="col-sm-10">
        <label>Bulan ke-2</label>
        <textarea name="dt2" id="dt2" class="form-control"><?php echo $dt2;?></textarea>
    </div>
</div>
<div class="form-group row">
    <div class="col-sm-10">
        <label>Bulan ke-3</label>
        <textarea name="dt3" id="dt3" class="form-control"><?php echo $dt3;?></textarea>
    </div>
</div>
<div class="form-group row">
    <div class="col-sm-6">
        <label>Summary</label>
        <textarea name="summary" id="summary" class="form-control"><?php echo $summary;?></textarea>
    </div>
    <div class="col-sm-6 col-sm-top">
        <label>Result</label>
        <div class="radio clearfix" style="margin:0px;">
            <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="result" id="result1" value="1" /> Supply Delivery</label>
            <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="result" id="result2" value="2" /> Supply Delivery With Note</label>
            <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="result" id="result3" value="3" /> Revised and Resubmitted</label>
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
		<div class="col-sm-10">
			<label>Bulan ke-1</label>
			<div class="form-control" style="height:auto"><?php echo $rsm['finance_evaluasi1'];?></div>
		</div>
	</div>
	<div class="form-group row">
		<div class="col-sm-10">
			<label>Bulan ke-2</label>
			<div class="form-control" style="height:auto"><?php echo $rsm['finance_evaluasi2'];?></div>
		</div>
	</div>
	<div class="form-group row">
		<div class="col-sm-10">
			<label>Bulan ke-3</label>
			<div class="form-control" style="height:auto"><?php echo $rsm['finance_evaluasi3'];?></div>
		</div>
	</div>
	<div class="form-group row">
		<div class="col-sm-6">
			<label>Summary</label>
			<div class="form-control" style="height:auto">
				<?php echo $tmp['summary']; ?>
				<p style="margin:10px 0 0; font-size:12px;"><i><?php echo $tmp['pic']." - ".$tmp['tanggal']." WIB";?></i></p>
			</div>
		</div>
		<div class="col-sm-6 col-sm-top">
			<label>Result</label>
			<div class="radio clearfix" style="margin:0px;">
				<label class="col-xs-12">
					<input type="radio" name="result" id="result" value="1" checked /><?php echo $arrResult[$rsm['finance_result']];?>
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

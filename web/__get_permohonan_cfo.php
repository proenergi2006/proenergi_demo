<?php $arrStat = array(1=>"Disetujui", "Ditolak"); ?>
<div class="form-group row">
	<div class="col-sm-10">
    	<label>Catatan Finance</label>
        <div class="form-control" style="height:auto">
			<?php echo ($rsm['finance_summary']); ?>
            <p style="margin:10px 0 0; font-size:12px;"><i>
                <?php echo $rsm['finance_pic']." ".date("d/m/Y H:m:s", strtotime($rsm['finance_tanggal']))." WIB"; ?>
            </i></p>
    	</div>
    </div>
</div>
<div class="form-group row">
	<div class="col-sm-10">
    	<label>Catatan OM</label>
        <div class="form-control" style="height:auto">
			<?php echo ($rsm['om_summary']); ?>
            <p style="margin:10px 0 0; font-size:12px;"><i>
                <?php echo $rsm['om_pic']." ".date("d/m/Y H:m:s", strtotime($rsm['om_tanggal']))." WIB"; ?>
            </i></p>
    	</div>
    </div>
</div>

<?php if(!$rsm['cfo_result']){ ?>
<div class="form-group row">
    <div class="col-sm-10">
        <label>Catatan CFO *</label>
        <textarea name="summary" id="summary" class="form-control validate[required]"></textarea>
    </div>
</div>
<div class="form-group row">
    <div class="col-sm-12">
        <label>Persetujuan</label>
        <div class="radio">
            <label class="rtl"><input type="radio" name="approval" id="approval1" class="validate[required]" value="1" /></label> Ya
        </div>
        <div class="radio">
            <label class="rtl"><input type="radio" name="approval" id="approval2" class="validate[required]" value="2" /></label> Tidak
        </div>
    </div>
</div>
<div class="form-group row">
	<div class="col-sm-12">
    	<div class="pad bg-gray">
            <input type="hidden" name="idr" value="<?php echo $idr;?>" />
            <input type="hidden" name="idk" value="<?php echo $idk;?>" />
            <a href="<?php echo BASE_REFERER; ?>" class="btn btn-default jarak-kanan"><i class="fa fa-reply jarak-kanan"></i> Kembali</a>
            <button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
		</div>
	</div>
</div>
<hr style="margin:5px 0" /><div class="row"><div class="col-sm-12"><small>* Wajib Diisi</small></div></div>
<?php } else{ ?>
<div class="form-group row">
    <div class="col-sm-10">
        <label>Catatan CFO</label>
        <div class="form-control" style="height:auto">
			<?php echo ($rsm['cfo_summary']); ?>
            <p style="margin:10px 0 0; font-size:12px;"><i>
                <?php echo $rsm['cfo_pic']." ".date("d/m/Y H:m:s", strtotime($rsm['cfo_tanggal']))." WIB"; ?>
            </i></p>
    	</div>
    </div>
</div>
<div class="form-group row">
	<div class="col-sm-12">
    	<div class="pad bg-gray">
            <a href="<?php echo BASE_REFERER; ?>" class="btn btn-default"><i class="fa fa-reply jarak-kanan"></i> Kembali</a>
		</div>
	</div>
</div>
<?php } ?>
<script>
	$(document).ready(function(){
		$("form#gform").validationEngine('attach',{
			onValidationComplete: function(form, status){
				if(status == true){
					$('#preview_modal').modal('hide');
					$('#loading_modal').modal({backdrop:"static"});
					form.validationEngine('detach');
					form.submit();
				}
			}
		});
	});		
</script>

<?php if(!$rsm['finance_result']){ ?>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label>Catatan *</label>
            <textarea name="finance_summary" id="finance_summary" class="form-control validate[required]"><?php echo str_replace("<br />", PHP_EOL, $rsm['finance_summary']); ?></textarea>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Persetujuan</label>
            <div class="radio clearfix" style="margin:0px;">
                <p><label><input type="radio" name="finance_result" id="finance_result1" class="validate[required]" value="1" /></label>Ya</p>
                <p><label><input type="radio" name="finance_result" id="finance_result2" class="validate[required]" value="2" /></label>Tidak</p>
            </div>
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
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label>Catatan *</label>
            <div class="form-control" style="height:auto">
                <?php echo ($rsm['finance_summary']); ?>
                <p style="margin:10px 0 0; font-size:12px;"><i>
                    <?php echo $rsm['finance_pic']." ".date("d/m/Y H:m:s", strtotime($rsm['finance_tanggal']))." WIB"; ?>
                </i></p>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Persetujuan</label>
            <div class="radio clearfix" style="margin:0px;">
                <label class="col-xs-12" style="margin-bottom:5px;">
                    <input type="radio" name="finance_result" id="finance_result1" value="1"<?php echo ($rsm['finance_result'] == 1)?' checked':' disabled';?> /> Ya
                </label>
                <label class="col-xs-12" style="margin-bottom:5px;">
                    <input type="radio" name="finance_result" id="finance_result2" value="2"<?php echo ($rsm['finance_result'] == 2)?' checked':' disabled';?> /> Tidak
                </label>
            </div>
        </div>
    </div>
</div>
<div class="row">
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
				$('#preview_modal').modal('hide');
				$('#preview_modal').find('#preview_alert').html("");
				$('#preview_modal').find('.modal-footer').on("click", "#cfOke", function(){
					$('#preview_modal').modal('hide');
					$('#loading_modal').modal({backdrop:"static"});
					form.validationEngine('detach');
					form.submit();
				});
				$('#preview_modal').find('.modal-footer').addClass("hide");
				if(status == true){
					var text = ($('input[name="finance_result"]:checked').val() == 1?'Disposisi persetujuan untuk permohonan update data akan dilanjutkan':'Permohonan update data ditolak, disposisi tidak dilanjutkan');
					$('#preview_modal').find('#preview_alert').html('<div>'+text+'.</div>Apakah anda yakin ?');
					$('#preview_modal').find('.modal-footer').removeClass("hide");
					$('#preview_modal').modal();
				}
			}
		});
	});		
</script>


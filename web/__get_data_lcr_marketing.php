<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label>Catatan *</label>
            <?php if($rsm['marketing_result']){ ?><div class="form-control static-text"><?php echo $rsm['marketing_summary']; ?></div><?php } else{ ?>
            <textarea name="marketing_summary" id="marketing_summary" class="form-control validate[required]"><?php echo $rsm['marketing_summary'];?></textarea><?php } ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Data Terverifikasi ? </label>
			<?php if($rsm['marketing_result']){ ?>
            <div class="radio clearfix" style="margin:0px;">
                <label class="col-xs-12" style="margin-bottom:5px;">
                    <input type="radio" name="marketing_result" id="marketing_result1" value="1"<?php echo ($rsm['marketing_result'] == 1)?' checked':' disabled';?> /> Ya
                </label>
                <label class="col-xs-12" style="margin-bottom:5px;">
                    <input type="radio" name="marketing_result" id="marketing_result2" value="2"<?php echo ($rsm['marketing_result'] == 2)?' checked':' disabled';?> /> Tidak
                </label>
            </div>
            <?php } else{ ?>
            <div class="radio clearfix" style="margin:0px;">
                <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="marketing_result" id="marketing_result1" value="1" /> Ya</label>
                <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="marketing_result" id="marketing_result2" value="2" /> Tidak</label>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php if(!$rsm['marketing_result']){ ?>
<div class="form-group row">
	<div class="col-sm-6">
		<input type="hidden" name="idr" value="<?php echo $idr;?>" />
		<input type="hidden" name="idk" value="<?php echo $idk;?>" />
		<a href="<?php echo BASE_REFERER; ?>" class="btn btn-primary jarak-kanan"><i class="fa fa-reply jarak-kanan"></i> Kembali</a>
		<button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
	</div>
</div>
<hr style="margin:5px 0" /><div class="row"><div class="col-sm-12"><small>* Wajib Diisi</small></div></div>
<?php } else{ ?>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label>PIC</label>
            <input type="text" name="pic" id="pic" class="form-control" value="<?php echo $rsm['marketing_pic'];?>" readonly />
        </div>
    </div>
    <div class="col-sm-3 col-md-2">
        <div class="form-group">
            <label>Tanggal</label>
            <input type="text" name="tgl" id="tgl" class="form-control" value="<?php echo date("d/m/Y", strtotime($rsm['marketing_tanggal']));?>" readonly />
        </div>
    </div>
</div>
<div class="form-group row">
	<div class="col-sm-6">
		<a href="<?php echo BASE_REFERER; ?>" class="btn btn-primary jarak-kanan"><i class="fa fa-reply jarak-kanan"></i> Kembali</a>
	</div>
</div>
<?php } ?>

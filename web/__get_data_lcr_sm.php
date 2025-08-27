<?php $arrSetuju = array(1=>"Ya", "Tidak"); ?>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label>Catatan Logistik</label>
            <div class="form-control" style="height:auto">
				<?php echo ($rsm['logistik_summary']); ?>
                <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $rsm['logistik_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['logistik_tanggal']))." WIB";?></i></p>
			</div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Persetujuan Logistik</label>
            <p><?php echo $arrSetuju[$rsm['logistik_result']]; ?></p>
        </div>
    </div>
</div>    
<?php if(!$rsm['sm_result']){ ?>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label>Catatan BM *</label>
            <textarea name="sm_summary" id="sm_summary" class="form-control validate[required]"><?php echo str_replace("<br />", PHP_EOL, $rsm['sm_summary']); ?></textarea>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Persetujuan BM ? </label>
            <div class="radio clearfix" style="margin:0px;">
                <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="sm_result" id="sm_result1" value="1" /> Ya</label>
                <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="sm_result" id="sm_result2" value="2" /> Tidak</label>
            </div>
        </div>
    </div>
</div>
<div class="form-group row">
	<div class="col-sm-12">
    	<div class="pad bg-gray">
            <input type="hidden" name="idr" value="<?php echo $idr;?>" />
            <input type="hidden" name="idk" value="<?php echo $idk;?>" />
			<a href="<?php echo BASE_URL_CLIENT."/verifikasi-lcr.php"; ?>" class="btn btn-default jarak-kanan"><i class="fa fa-reply jarak-kanan"></i> Kembali</a>
            <button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
		</div>
	</div>
</div>
<hr style="margin:5px 0" /><div class="row"><div class="col-sm-12"><small>* Wajib Diisi</small></div></div>
<?php } else{ ?>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label>Catatan BM *</label>
            <div class="form-control" style="height:auto">
				<?php echo ($rsm['sm_summary']); ?>
                <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $rsm['sm_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['sm_tanggal']))." WIB";?></i></p>
			</div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Persetujuan BM ? </label>
            <p><?php echo $arrSetuju[$rsm['sm_result']]; ?></p>
        </div>
    </div>
</div>    
<div class="form-group row">
	<div class="col-sm-12">
    	<div class="pad bg-gray">
			<a href="<?php echo BASE_URL_CLIENT."/verifikasi-lcr.php"; ?>" class="btn btn-default jarak-kanan"><i class="fa fa-reply jarak-kanan"></i> Kembali</a>
		</div>
	</div>
</div>
<?php } ?>

<?php 
	$arrSetuju 	= array(1=>"Ya","Tidak"); 
	$catatan	= str_replace("<br />", PHP_EOL, $rsm['logistik_summary']);
	$disabled 	= (!$rsm['id_wil_oa'])?'disabled':'';
	if(!$rsm['logistik_result']){ 
?>
<div class="form-group row">
    <div class="col-sm-6">
        <label>Catatan *</label>
        <textarea name="logistik_summary" id="logistik_summary" class="form-control validate[required]" <?php echo $disabled;?>><?php echo $catatan; ?></textarea>
    </div>
    <div class="col-sm-6 col-sm-top">
        <label>Data Terverifikasi ? </label>
        <?php if($rsm['id_wil_oa']){ ?>
        <div class="radio clearfix" style="margin:0px;">
            <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="logistik_result" id="logistik_result1" value="1" /> Ya</label>
            <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="logistik_result" id="logistik_result2" value="2" /> Tidak</label>
        </div>
        <?php } else{ ?>
        <p style="margin:0;" class="text-red"><b><i>Harap lengkapi data LCR terlebih dahulu</i></b></p>
        <?php } ?>
    </div>
</div>

<?php if(!$rsm['id_wil_oa']){ ?>
<div class="form-group row">
    <div class="col-sm-6">
        <div class="radio clearfix" style="margin:0px;">
            <label class="col-xs-12" style="margin-bottom:5px;"><input type="checkbox" name="balik" id="balik" value="1" /> Kembalikan ke marketing</label>
        </div>
    </div>
</div>
<?php } ?>

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
<div class="form-group row">
    <div class="col-sm-6">
        <label>Catatan *</label>
        <div class="form-control" style="height:auto">
            <?php echo $rsm['logistik_summary']; ?>
            <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $rsm['logistik_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['logistik_tanggal']))." WIB";?></i></p>
        </div>
    </div>
    <div class="col-sm-6 col-sm-top">
        <label>Data Terverifikasi ? </label>
        <p><?php echo $arrSetuju[$rsm['logistik_result']];?></p>
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

<?php $arrStat = array(1=>"Disetujui", "Ditolak"); ?>
<div class="form-group row">
	<div class="col-sm-12">
    	<label>Catatan Finance</label>
        <div class="form-control" style="height:auto">
			<?php echo str_replace("<br />", PHP_EOL, $rsm['finance_summary']); ?>
            <p style="margin:10px 0 0; font-size:12px;"><i>
                <?php echo $rsm['finance_pic']." ".date("d/m/Y H:m:s", strtotime($rsm['finance_tanggal']))." WIB"; ?>
            </i></p>
    	</div>
    </div>
</div>
<div class="form-group row">
	<div class="col-sm-12">
    	<label>Catatan OM</label>
        <div class="form-control" style="height:auto">
			<?php echo str_replace("<br />", PHP_EOL, $rsm['om_summary']); ?>
            <p style="margin:10px 0 0; font-size:12px;"><i>
                <?php echo $rsm['om_pic']." ".date("d/m/Y H:m:s", strtotime($rsm['om_tanggal']))." WIB"; ?>
            </i></p>
    	</div>
    </div>
</div>
<div class="form-group row">
    <div class="col-sm-12">
        <label>Catatan CFO</label>
        <div class="form-control" style="height:auto">
			<?php echo str_replace("<br />", PHP_EOL, $rsm['cfo_summary']); ?>
            <p style="margin:10px 0 0; font-size:12px;"><i>
                <?php echo $rsm['cfo_pic']." ".date("d/m/Y H:m:s", strtotime($rsm['cfo_tanggal']))." WIB"; ?>
            </i></p>
    	</div>
    </div>
</div>

<?php if(!$rsm['ceo_result']){ ?>
<div class="form-group row">
    <div class="col-sm-8">
        <label>Catatan CEO *</label>
        <textarea name="summary" id="summary" class="form-control validate[required]"></textarea>
    </div>
</div>
<div class="form-group row">
    <div class="col-sm-12">
        <label>Data Terverifikasi ? </label>
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
    <div class="col-sm-12">
        <label>Catatan CEO</label>
        <div class="form-control" style="height:auto">
			<?php echo str_replace("<br />", PHP_EOL, $rsm['ceo_summary']); ?>
            <p style="margin:10px 0 0; font-size:12px;"><i>
                <?php echo $rsm['ceo_pic']." ".date("d/m/Y H:m:s", strtotime($rsm['ceo_tanggal']))." WIB"; ?>
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

<?php
	if(count($res1) > 0){
		foreach($res1 as $datax1){
?>
<div class="box box-primary">
    <div class="box-header with-border">
    	<h3 class="box-title" style="font-size:14px;"><?php echo '['.strtoupper($datax1['nama_cabang']).'] '.$datax1['nomor_pr']; ?></h3>
    </div>
    <div class="box-body">
        <div class="form-group row">
            <div class="col-sm-6">
                <label>Catatan BM</label>
                <div class="form-control" style="height:auto">
                    <?php echo ($datax1['sm_summary'])?$datax1['sm_summary']:'&nbsp;'; ?>
                    <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $datax1['sm_pic']." - ".date("d/m/Y H:i:s", strtotime($datax1['sm_tanggal']))." WIB"; ?></i></p>
                </div>
            </div>
            <div class="col-sm-6 col-sm-top">
                <label>Catatan Purchasing</label>
                <div class="form-control" style="height:auto">
                    <?php echo ($datax1['purchasing_summary'])?$datax1['purchasing_summary']:'&nbsp;'; ?>
                    <p style="margin:10px 0 0; font-size:12px;"><i>
                        <?php echo $datax1['purchasing_pic']." - ".date("d/m/Y H:i:s", strtotime($datax1['purchasing_tanggal']))." WIB"; ?>
                    </i></p>
                </div>
            </div>
        </div>
        <?php if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 4){ ?>

		<!-- <?php if($datax1['revert_cfo']){ ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Catatan Pengembalian CFO</label>
                    <div class="form-control" style="height:auto"><?php echo ($datax1['revert_cfo_summary'])?$datax1['revert_cfo_summary']:'&nbsp;'; ?></div>
                </div>
            </div>
        </div>
        <hr style="border-top:4px double #ddd; margin:5px 0px 20px;" />
		<?php } ?>

        <div class="form-group row">
            <div class="col-sm-6">
                <label>Dikembalikan ke Purchasing ?*</label>
                <div class="radio clearfix" style="margin:0px;">	
                    <label class="col-xs-12" style="margin-bottom:5px;">
                    	<input type="radio" name="<?php echo 'revert['.$datax1['id_pr'].']';?>" id="<?php echo 'revert1['.$datax1['id_pr'].']';?>" class="revert" value="1" /> Ya
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                    <input type="radio" name="<?php echo 'revert['.$datax1['id_pr'].']';?>" id="<?php echo 'revert2['.$datax1['id_pr'].']';?>" class="revert" checked value="2" /> 
                    Tidak</label>
                </div>
            </div>
            <div class="col-sm-6 col-sm-top">
                <label>Catatan Pengembalian</label>
                <textarea name="<?php echo 'summary_revert['.$datax1['id_pr'].']';?>" id="<?php echo 'summary_revert['.$datax1['id_pr'].']';?>" class="form-control" readonly></textarea>
            </div>
        </div>
        
        <div class="form-group row">
            <div class="col-sm-6">
                <label>Diteruskan ke CEO ?*</label>
                <div class="radio clearfix" style="margin:0px;">	
                    <label class="col-xs-12" style="margin-bottom:5px;">
                    	<input type="radio" name="<?php echo 'extend['.$datax1['id_pr'].']';?>" id="<?php echo 'extend1['.$datax1['id_pr'].']';?>" value="1" checked /> Ya
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                    	<input type="radio" name="<?php echo 'extend['.$datax1['id_pr'].']';?>" id="<?php echo 'extend2['.$datax1['id_pr'].']';?>" value="2" /> Tidak
                    </label>
                </div>
            </div>
            <div class="col-sm-6 col-sm-top">
                <label>Catatan CFO</label>
                <textarea name="<?php echo 'summary['.$datax1['id_pr'].']';?>" id="<?php echo 'summary['.$datax1['id_pr'].']';?>" class="form-control"></textarea>
                <input type="hidden" name="<?php echo 'idw['.$datax1['id_pr'].']';?>" id="<?php echo 'idw['.$datax1['id_pr'].']';?>" value="<?php echo $datax1['id_wilayah'];?>" />
            </div>
        </div> -->
        <?php } else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 3){ ?>
        <!-- <div class="form-group row">
            <div class="col-sm-6">
                <label>Catatan CFO</label>
                <div class="form-control" style="height:auto">
                    <?php echo ($datax1['cfo_summary'])?$datax1['cfo_summary']:'&nbsp;'; ?>
                    <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $datax1['cfo_pic']." - ".date("d/m/Y H:i:s", strtotime($datax1['cfo_tanggal']))." WIB"; ?></i></p>
                </div>
			</div>
		</div> -->

		<?php if($datax1['revert_ceo']){ ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Catatan Pengembalian CEO</label>
                    <div class="form-control" style="height:auto"><?php echo ($datax1['revert_ceo_summary'])?$datax1['revert_ceo_summary']:'&nbsp;'; ?></div>
                </div>
            </div>
        </div>
        <hr style="border-top:4px double #ddd; margin:5px 0px 20px;" />
		<?php } ?>

        <div class="form-group row">
            <div class="col-sm-6">
                <label>Catatan Pengembalian</label>
                <textarea name="<?php echo 'summary_revert['.$datax1['id_pr'].']';?>" id="<?php echo 'summary_revert['.$datax1['id_pr'].']';?>" class="form-control" readonly></textarea>
            </div>
            <div class="col-sm-6 col-sm-top">
                <label>Dikembalikan ke Purchasing ?*</label>
                <div class="radio clearfix" style="margin:0px;">	
                    <label class="col-xs-12" style="margin-bottom:5px;">
                    	<input type="radio" name="<?php echo 'revert['.$datax1['id_pr'].']';?>" id="<?php echo 'revert1['.$datax1['id_pr'].']';?>" class="revert" value="1" /> Ya
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                    <input type="radio" name="<?php echo 'revert['.$datax1['id_pr'].']';?>" id="<?php echo 'revert2['.$datax1['id_pr'].']';?>" class="revert" checked value="2" /> 
                    Tidak</label>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-6">
                <label>Catatan CEO</label>
                <textarea name="<?php echo 'summary['.$datax1['id_pr'].']';?>" id="<?php echo 'summary['.$datax1['id_pr'].']';?>" class="form-control"></textarea>
                <input type="hidden" name="<?php echo 'idw['.$datax1['id_pr'].']';?>" id="<?php echo 'idw['.$datax1['id_pr'].']';?>" value="<?php echo $datax1['id_wilayah'];?>" />
            </div>
        </div>
        <?php } ?>
        
    </div>
</div>
<?php } ?>

<hr style="margin:0 0 10px" />
<div class="form-group row">
    <div class="col-sm-12">
        <div class="pad bg-gray">
			<input type="hidden" name="prnya" id="prnya" value="cfoall" />
            <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT."/purchase-request.php";?>">Kembali</a> 
            <button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Submit</button>
        </div>
    </div>
</div>
<?php } ?>
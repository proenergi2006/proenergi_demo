<?php
	$arrResult 	= array(1=>"Supply Delivery", "Supply Delivery With Note", "Revised and Resubmitted");
	$arrSetuju 	= array(1=>"Yes", "No");	
	$eval = json_decode($rsm['logistik_data'], true);
	$eval[0]['nomor'] = ($eval[0]['nomor'])?$eval[0]['nomor']:str_replace("<br />", PHP_EOL, $rsm['logistik_area']);
	$eval[1]['nomor'] = ($eval[1]['nomor'])?$eval[1]['nomor']:$rsm['logistik_env'];
	$eval[2]['nomor'] = ($eval[2]['nomor'])?$eval[2]['nomor']:$rsm['logistik_storage'];
	$eval[3]['nomor'] = ($eval[3]['nomor'])?$eval[3]['nomor']:str_replace("<br />", PHP_EOL, $rsm['logistik_bisnis']);
	$eval[4]['nomor'] = ($eval[4]['nomor'])?$eval[4]['nomor']:$rsm['logistik_hour'];
	$eval[5]['nomor'] = ($eval[5]['nomor'])?$eval[5]['nomor']:$rsm['logistik_volume'];
	$eval[6]['nomor'] = ($eval[6]['nomor'])?$eval[6]['nomor']:$rsm['logistik_quality'];
	$eval[7]['nomor'] = ($eval[7]['nomor'])?$eval[7]['nomor']:$rsm['logistik_truck'];
	$eval[8]['nomor'] = ($eval[8]['nomor'])?$eval[8]['nomor']:str_replace("<br />", PHP_EOL, $rsm['desc_condition']);
	$eval[9]['nomor'] = ($eval[9]['nomor'])?$eval[9]['nomor']:str_replace("<br />", PHP_EOL, $rsm['desc_stor_fac']);

	$lain1 = ($eval[1]['lain'])?$eval[1]['lain']:$rsm['logistik_env_other'];
	$lain2 = ($eval[2]['lain'])?$eval[2]['lain']:$rsm['logistik_storage_other'];
	$lain3 = ($eval[4]['lain'])?$eval[4]['lain']:$rsm['logistik_hour_other'];
	$lain4 = ($eval[5]['lain'])?$eval[5]['lain']:$rsm['logistik_volume_other'];
	$lain5 = ($eval[6]['lain'])?$eval[6]['lain']:$rsm['logistik_bisnis_other'];
	$lain6 = ($eval[7]['lain'])?$eval[7]['lain']:$rsm['logistik_truck_other'];
	
?>
<p><b><u>1. LOCATION DETAIL</u></b></p>

<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <label>Area *</label>
            <div class="form-control" style="min-height:80px; height:auto;"><?php echo ($eval[0]['nomor'])?$eval[0]['nomor']:$rsm['logistik_area']; ?></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label>Conditions Around Locations *</label>
            <div class="radio clearfix" style="margin:0px;">
                <label class="col-xs-12" style="margin-bottom:5px;">
                    <input type="radio" name="evaluationA" id="evaluationA1" value="1"<?php echo ($eval[1]['nomor'] == 1)?' checked':' disabled';?> /> Industri
                </label>
                <label class="col-xs-12" style="margin-bottom:5px;">
                    <input type="radio" name="evaluationA" id="evaluationA2" value="2"<?php echo ($eval[1]['nomor'] == 2)?' checked':' disabled';?> /> Pemukiman
                </label>
                <label class="col-xs-12" style="margin-bottom:5px;">
                    <input type="radio" name="evaluationA" id="evaluationA3" value="3"<?php echo ($eval[1]['nomor'] == 3)?' checked':' disabled';?> /> 
                    Other (<i>Specify</i>)
                </label>                
            </div>
            <?php echo ($eval[1]['nomor'] == 3 && $lain1)?'<div class="form-control" style="height:auto">'.$lain1.'</div>':''; ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Storage Facility *</label>
            <div class="radio clearfix" style="margin:0px;">
                <label class="col-xs-12" style="margin-bottom:5px;">
                    <input type="radio" name="evaluationB" id="evaluationB1" value="1"<?php echo ($eval[2]['nomor'] == 1)?' checked':' disabled';?> /> Indoor
                </label>
                <label class="col-xs-12" style="margin-bottom:5px;">
                    <input type="radio" name="evaluationB" id="evaluationB2" value="2"<?php echo ($eval[2]['nomor'] == 2)?' checked':' disabled';?> /> Outdoor
                </label>
                <label class="col-xs-12" style="margin-bottom:5px;">

                    <input type="radio" name="evaluationB" id="evaluationB3" value="3"<?php echo ($eval[2]['nomor'] == 3)?' checked':' disabled';?> /> 
                    Other (<i>Specify</i>)
                </label>
            </div>
            <?php echo ($eval[2]['nomor'] == 3 && $lain2)?'<div class="form-control" style="height:auto">'.$lain2.'</div>':''; ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label>Description Of Condition</label>
            <div class="form-control" style="height:auto; min-height:80px;"><?php echo $eval[8]['nomor']; ?></div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Description Of Storage Facility</label>
            <div class="form-control" style="height:auto; min-height:80px;"><?php echo $eval[9]['nomor']; ?></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <label>Security Environment / Business Area *</label>
            <div class="form-control" style="height:auto; min-height:80px;"><?php echo ($eval[3]['nomor'])?$eval[3]['nomor']:$rsm['logistik_bisnis']; ?></div>
        </div>
    </div>
</div>

<p style="margin-top:20px;"><b><u>2. DELIVERY DETAIL</u></b></p>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label>Operating Hours *</label>
            <div class="radio clearfix" style="margin:0px;">
                <label class="col-xs-12" style="margin-bottom:5px;">
                    <input type="radio" name="evaluationC" id="evaluationC1" value="1"<?php echo ($eval[4]['nomor'] == 1)?' checked':' disabled';?> /> 08.00-17.00
                </label>
                <label class="col-xs-12" style="margin-bottom:5px;">
                    <input type="radio" name="evaluationC" id="evaluationC2" value="2"<?php echo ($eval[4]['nomor'] == 2)?' checked':' disabled';?> /> 24 Hours
                </label>
                <label class="col-xs-12" style="margin-bottom:5px;">
                    <input type="radio" name="evaluationC" id="evaluationC3" value="3"<?php echo ($eval[4]['nomor'] == 3)?' checked':' disabled';?> /> 
                    Other (<i>Specify</i>)
                </label>                
            </div>
            <?php echo ($eval[4]['nomor'] == 3 && $lain3)?'<div class="form-control" style="height:auto">'.$lain3.'</div>':''; ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Volume Measurement *</label>
            <div class="radio clearfix" style="margin:0px;">
                <label class="col-xs-12" style="margin-bottom:5px;">
                    <input type="radio" name="evaluationD" id="evaluationD1" value="1"<?php echo ($eval[5]['nomor'] == 1)?' checked':' disabled';?> /> Pro Energy's Tank Lorry
                </label>
                <label class="col-xs-12" style="margin-bottom:5px;">
                    <input type="radio" name="evaluationD" id="evaluationD2" value="2"<?php echo ($eval[5]['nomor'] == 2)?' checked':' disabled';?> /> Flowmeter
                </label>
                <label class="col-xs-12" style="margin-bottom:5px;">
                    <input type="radio" name="evaluationD" id="evaluationD3" value="3"<?php echo ($eval[5]['nomor'] == 3)?' checked':' disabled';?> /> 
                    Other (<i>Specify</i>)
                </label>
            </div>
            <?php echo ($eval[5]['nomor'] == 3 && $lain4)?'<div class="form-control" style="height:auto">'.$lain4.'</div>':''; ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label>Quality Checking *</label>
            <div class="radio clearfix" style="margin:0px;">
                <label class="col-xs-12" style="margin-bottom:5px;">
                    <input type="radio" name="evaluationE" id="evaluationE1" value="1"<?php echo ($eval[6]['nomor'] == 1)?' checked':' disabled';?> /> Density
                </label>
                <label class="col-xs-12" style="margin-bottom:5px;">
                    <input type="radio" name="evaluationE" id="evaluationE2" value="2"<?php echo ($eval[6]['nomor'] == 2)?' checked':' disabled';?> /> 
                    Other (<i>Specify</i>)
                </label>                
            </div>
            <?php echo ($eval[6]['nomor'] == 3 && $lain5)?'<div class="form-control" style="height:auto">'.$lain5.'</div>':''; ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Max. Truck Capacity *</label>
            <div class="radio clearfix" style="margin:0px;">
                <label class="col-xs-12" style="margin-bottom:5px;">
                    <input type="radio" name="evaluationF" id="evaluationF1" value="1"<?php echo ($eval[7]['nomor'] == 1)?' checked':' disabled';?> /> 5 KL
                </label>
                <label class="col-xs-12" style="margin-bottom:5px;">
                    <input type="radio" name="evaluationF" id="evaluationF2" value="2"<?php echo ($eval[7]['nomor'] == 2)?' checked':' disabled';?> /> 8 KL
                </label>
                <label class="col-xs-12" style="margin-bottom:5px;">
                    <input type="radio" name="evaluationF" id="evaluationF3" value="3"<?php echo ($eval[7]['nomor'] == 3)?' checked':' disabled';?> /> 10 KL
                </label>
                <label class="col-xs-12" style="margin-bottom:5px;">
                    <input type="radio" name="evaluationF" id="evaluationF4" value="4"<?php echo ($eval[7]['nomor'] == 4)?' checked':' disabled';?> /> 16 KL
                </label>
                <label class="col-xs-12" style="margin-bottom:5px;">
                    <input type="radio" name="evaluationF" id="evaluationF5" value="5"<?php echo ($eval[7]['nomor'] == 5)?' checked':' disabled';?> /> Other, 
                </label>
            </div>
            <?php echo ($eval[7]['nomor'] == 5 && $lain6)?'<div class="form-control" style="height:auto">'.$lain6.'</div>':''; ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label>Summary Assessment *</label>
            <div class="form-control" style="height:auto">
                <?php echo ($rsm['logistik_summary']); ?>
                <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $rsm['logistik_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['logistik_tgl_proses']))." WIB";?></i></p>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Assessment Result *</label>
            <p><?php echo $arrResult[$rsm['logistik_result']];?></p>
        </div>
    </div>
</div>

<?php if($rsm['legal_summary']) {?>
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
<?php } ?>

<?php if($rsm['finance_summary']) {?>
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
<?php } ?>

<?php if($rsm['sm_summary']) {?>
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
<?php } ?>

<?php if($rsm['om_summary']) {?>
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
<?php } ?>

<?php if($rsm['cfo_summary']) {?>
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
<?php } ?>

<?php if($rsm['ceo_summary']) {?>
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

<?php if(!$rsm['logistik_result']){ ?>

    <div class="row">
        <div class="col-sm-12">
            <div class="pad bg-gray">
                <input type="hidden" name="idr" value="<?php echo $idr;?>" />
                <a href="<?php echo BASE_URL_CLIENT."/verifikasi-data-customer.php"; ?>" class="btn btn-default jarak-kanan">
                <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                <?php //<button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Submit</button> ?>
            </div>
        </div>
    </div>

    <hr style="margin:5px 0" /><div class="row"><div class="col-sm-12"><small>* Wajib Diisi</small></div></div>
<?php } else {?>

    <div class="row">
        <div class="col-sm-12">
            <div class="pad bg-gray">
                <a href="<?php echo BASE_URL_CLIENT."/verifikasi-data-customer.php"; ?>" class="btn btn-default jarak-kanan">
                <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
            </div>
        </div>
    </div>

<?php } ?>
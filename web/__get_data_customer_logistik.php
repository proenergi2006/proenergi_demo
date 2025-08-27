<?php
	$arrResult 	= array(1=>"Supply Delivery", "Supply Delivery With Note", "Revised and Resubmitted","Yes","No");
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
	$lain5 = ($eval[6]['lain'])?$eval[6]['lain']:$rsm['logistik_quality_other'];
	$lain6 = ($eval[7]['lain'])?$eval[7]['lain']:$rsm['logistik_truck_other'];
	
?>
<?php if(!$rsm['logistik_result']){ ?>
<p><b><u>1. LOCATION DETAIL</u></b></p>

<div class="row">
    <div class="col-md-8">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-3">Area *</label>
            <div class="col-md-9">
                <textarea name="evaluation_number[]" id="evaluation_number1" class="form-control" style="height:90px;" required><?php echo $eval[0]['nomor'];?></textarea>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Conditions Around Locations *</label>
            <div class="col-md-8">
                <div class="radio" style="margin:0px;">
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationA" id="evaluationA1" value="1"<?php echo ($eval[1]['nomor'] == 1)?' checked':'';?> /> Industri
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationA" id="evaluationA2" value="2"<?php echo ($eval[1]['nomor'] == 2)?' checked':'';?> /> Pemukiman
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationA" id="evaluationA3" value="3"<?php echo ($eval[1]['nomor'] == 3)?' checked':'';?> /> Other (<i>Specify</i>)
                    </label>
                </div>
                <input type="text" name="a1" id="a1" class="form-control" required <?php echo 'value="'.$lain1.'" '.($eval[1]['nomor'] != 3 ? 'disabled' : '');?> />
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Storage Facility *</label>
            <div class="col-md-8">
                <div class="radio clearfix" style="margin:0px;">
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationB" id="evaluationB1" value="1"<?php echo ($eval[2]['nomor'] == 1)?' checked':'';?> /> Indoor
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationB" id="evaluationB2" value="2"<?php echo ($eval[2]['nomor'] == 2)?' checked':'';?> /> Outdoor
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationB" id="evaluationB3" value="3"<?php echo ($eval[2]['nomor'] == 3)?' checked':'';?> /> Other (<i>Specify</i>)
                    </label>
                </div>
                <input type="text" name="a2" id="a2" class="form-control" required <?php echo 'value="'.$lain2.'" '.($eval[2]['nomor'] != 3?'disabled':''); ?> />
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-3">Description Of Condition *</label>
            <div class="col-md-9">
                <textarea name="desc_condition" id="desc_condition" class="form-control" style="height:90px;" required><?php echo $eval[8]['nomor'];?></textarea>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-3">Description Of Storage Facility *</label>
            <div class="col-md-9">
                <textarea name="desc_stor_fac" id="desc_stor_fac" class="form-control" style="height:90px;" required><?php echo $eval[9]['nomor'];?></textarea>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-3">Security Environment / Business Area *</label>
            <div class="col-md-9">
                <textarea name="evaluation_number[]" id="evaluation_number2" class="form-control" style="height:90px;" required><?php echo $eval[3]['nomor'];?></textarea>
            </div>
        </div>
    </div>
</div>

<hr style="margin:15px 0px; border-top:4px double #ddd;" />

<p style="margin:20px 0px;"><b><u>2. DELIVERY DETAIL</u></b></p>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Operating Hours *</label>
            <div class="col-md-8">
                <div class="radio clearfix" style="margin:0px;">
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationC" id="evaluationC1" value="1"<?php echo ($eval[4]['nomor'] == 1)?' checked':'';?> /> 08.00-17.00
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationC" id="evaluationC2" value="2"<?php echo ($eval[4]['nomor'] == 2)?' checked':'';?> /> 24 Hours
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationC" id="evaluationC3" value="3"<?php echo ($eval[4]['nomor'] == 3)?' checked':'';?> /> Other (<i>Specify</i>)
                    </label>
                </div>
                <input type="text" name="a3" id="a3" class="form-control" required <?php echo 'value="'.$lain3.'" '.($eval[4]['nomor'] != 3?'disabled':''); ?> />
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Volume Measurement *</label>
            <div class="col-md-8">
                <div class="radio clearfix" style="margin:0px;">
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationD" id="evaluationD1" value="1"<?php echo ($eval[5]['nomor'] == 1)?' checked':'';?> /> Pro Energy's Tank Lorry
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationD" id="evaluationD2" value="2"<?php echo ($eval[5]['nomor'] == 2)?' checked':'';?> /> Flowmeter
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationD" id="evaluationD3" value="3"<?php echo ($eval[5]['nomor'] == 3)?' checked':'';?> /> Other (<i>Specify</i>)
                    </label>
                </div>
                <input type="text" name="a4" id="a4" class="form-control" required <?php echo 'value="'.$lain4.'" '.($eval[5]['nomor'] != 3?'disabled':''); ?> />
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Quality Checking *</label>
            <div class="col-md-8">
                <div class="radio clearfix" style="margin:0px;">
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationE" id="evaluationE1" value="1"<?php echo ($eval[6]['nomor'] == 1)?' checked':'';?> /> Density
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationE" id="evaluationE2" value="2"<?php echo ($eval[6]['nomor'] == 2)?' checked':'';?> /> Other (<i>Specify</i>)
                    </label>
                </div>
                <input type="text" name="a5" id="a5" class="form-control" required <?php echo 'value="'.$lain5.'" '.($eval[6]['nomor'] != 2?'disabled':''); ?> />
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Truck Capacity *</label>
            <div class="col-md-8">
                <div class="radio clearfix" style="margin:0px;">
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationF" id="evaluationF1" value="1"<?php echo ($eval[7]['nomor'] == 1)?' checked':'';?> /> 5 KL
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationF" id="evaluationF2" value="2"<?php echo ($eval[7]['nomor'] == 2)?' checked':'';?> /> 8 KL
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationF" id="evaluationF3" value="3"<?php echo ($eval[7]['nomor'] == 3)?' checked':'';?> /> 10 KL
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationF" id="evaluationF4" value="4"<?php echo ($eval[7]['nomor'] == 4)?' checked':'';?> /> 16 KL
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationF" id="evaluationF5" value="5"<?php echo ($eval[7]['nomor'] == 5)?' checked':'';?> /> Other, 
                    </label>
                </div>
                <input type="text" name="a6" id="a6" class="form-control" required <?php echo 'value="'.$lain6.'" '.($eval[7]['nomor'] != 5?'disabled':''); ?> />
            </div>
        </div>
    </div>
</div>

<hr style="margin:15px 0px; border-top:4px double #ddd;" />

<?php if($rsm['finance_result']) {?>
<div class="row">
    <div class="col-md-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Admin Finance Summary</label>
            <div class="col-md-8">
                <div class="form-control" style="height:auto;">
					<?php echo ($rsm['finance_summary']); ?>
                    <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $rsm['finance_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['finance_tgl_proses']))." WIB";?></i></p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Admin Finance Result</label>
            <div class="col-md-8">
                <div class="form-control" style="height:auto;"><?php echo $arrResult[$rsm['finance_result']]; ?></div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<div class="row">
    <div class="col-md-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Summary Assessment *</label>
            <div class="col-md-8">
                <textarea name="logistik_summary" id="logistik_summary" class="form-control" style="height:90px;" required></textarea>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Assessment Result *</label>
            <div class="col-md-8">
                <div class="radio clearfix" style="margin:0px;">
                    <label class="col-xs-12" style="margin-bottom:5px;">
                    <input type="radio" name="logistik_result" id="logistik_result1" required value="1" /> Supply Delivery</label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                    <input type="radio" name="logistik_result" id="logistik_result2" required value="2" /> Supply Delivery With Note</label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                    <input type="radio" name="logistik_result" id="logistik_result3" required value="3" /> Revised and Resubmitted</label>
                </div>
            </div>
        </div>
    </div>
</div>

<?php } else{ ?>

<p><b><u>1. LOCATION DETAIL</u></b></p>

<div class="row">
    <div class="col-md-8">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-3">Area *</label>
            <div class="col-md-9">
                <div class="form-control" style="min-height:90px; height:auto;"><?php echo ($eval[0]['nomor'])?$eval[0]['nomor']:$rsm['logistik_area']; ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Conditions Around Locations *</label>
            <div class="col-md-8">
                <div class="radio" style="margin:0px;">
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationA" id="evaluationA1" value="1"<?php echo ($eval[1]['nomor'] == 1)?' checked':' disabled';?> /> Industri
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationA" id="evaluationA2" value="2"<?php echo ($eval[1]['nomor'] == 2)?' checked':' disabled';?> /> Pemukiman
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationA" id="evaluationA3" value="3"<?php echo ($eval[1]['nomor'] == 3)?' checked':' disabled';?> /> Other (<i>Specify</i>)
                    </label>
                </div>
                <?php echo ($eval[1]['nomor'] == 3 && $lain1) ? '<div class="form-control" style="height:auto;">'.$lain1.'</div>' : ''; ?>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Storage Facility *</label>
            <div class="col-md-8">
                <div class="radio clearfix" style="margin:0px;">
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationB" id="evaluationB1" value="1"<?php echo ($eval[2]['nomor'] == 1)?' checked':' disabled';?> /> Indoor
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationB" id="evaluationB2" value="2"<?php echo ($eval[2]['nomor'] == 2)?' checked':' disabled';?> /> Outdoor
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationB" id="evaluationB3" value="3"<?php echo ($eval[2]['nomor'] == 3)?' checked':' disabled';?> /> Other (<i>Specify</i>)
                    </label>
                </div>
                <?php echo ($eval[2]['nomor'] == 3 && $lain2) ? '<div class="form-control" style="height:auto;">'.$lain2.'</div>' : ''; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-3">Description Of Condition *</label>
            <div class="col-md-9">
                <div class="form-control" style="min-height:90px; height:auto;"><?php echo ($eval[8]['nomor'])?$eval[8]['nomor']:''; ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-3">Description Of Storage Facility *</label>
            <div class="col-md-9">
                <div class="form-control" style="min-height:90px; height:auto;"><?php echo ($eval[9]['nomor'])?$eval[9]['nomor']:''; ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-3">Security Environment / Business Area *</label>
            <div class="col-md-9">
                <div class="form-control" style="min-height:90px; height:auto;"><?php echo ($eval[3]['nomor'])?$eval[3]['nomor']:$rsm['logistik_bisnis']; ?></div>
            </div>
        </div>
    </div>
</div>

<hr style="margin:15px 0px; border-top:4px double #ddd;" />

<p style="margin:20px 0px;"><b><u>2. DELIVERY DETAIL</u></b></p>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Operating Hours *</label>
            <div class="col-md-8">
                <div class="radio clearfix" style="margin:0px;">
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationC" id="evaluationC1" value="1"<?php echo ($eval[4]['nomor'] == 1)?' checked':' disabled';?> /> 08.00-17.00
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationC" id="evaluationC2" value="2"<?php echo ($eval[4]['nomor'] == 2)?' checked':' disabled';?> /> 24 Hours
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationC" id="evaluationC3" value="3"<?php echo ($eval[4]['nomor'] == 3)?' checked':' disabled';?> /> Other (<i>Specify</i>)
                    </label>
                </div>
                <?php echo ($eval[4]['nomor'] == 3 && $lain3)?'<div class="form-control" style="height:auto">'.$lain3.'</div>':''; ?>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Volume Measurement *</label>
            <div class="col-md-8">
                <div class="radio clearfix" style="margin:0px;">
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationD" id="evaluationD1" value="1"<?php echo ($eval[5]['nomor'] == 1)?' checked':' disabled';?> /> Pro Energy's Tank Lorry
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationD" id="evaluationD2" value="2"<?php echo ($eval[5]['nomor'] == 2)?' checked':' disabled';?> /> Flowmeter
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationD" id="evaluationD3" value="3"<?php echo ($eval[5]['nomor'] == 3)?' checked':' disabled';?> /> Other (<i>Specify</i>)
                    </label>
                </div>
                <?php echo ($eval[5]['nomor'] == 3 && $lain4)?'<div class="form-control" style="height:auto">'.$lain4.'</div>':''; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Quality Checking *</label>
            <div class="col-md-8">
                <div class="radio clearfix" style="margin:0px;">
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationE" id="evaluationE1" value="1"<?php echo ($eval[6]['nomor'] == 1)?' checked':' disabled';?> /> Density
                    </label>
                    <label class="col-xs-12" style="margin-bottom:5px;">
                        <input type="radio" name="evaluationE" id="evaluationE2" value="2"<?php echo ($eval[6]['nomor'] == 2)?' checked':' disabled';?> /> Other (<i>Specify</i>)
                    </label>
                </div>
                <?php echo ($eval[6]['nomor'] == 2 && $lain5)?'<div class="form-control" style="height:auto">'.$lain5.'</div>':''; ?>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Truck Capacity *</label>
            <div class="col-md-8">
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
</div>

<hr style="margin:15px 0px; border-top:4px double #ddd;" />

<?php if($rsm['finance_result']) {?>
<div class="row">
    <div class="col-md-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Admin Finance Summary</label>
            <div class="col-md-8">
                <div class="form-control" style="height:auto;">
					<?php echo ($rsm['finance_summary']); ?>
                    <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $rsm['finance_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['finance_tgl_proses']))." WIB";?></i></p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Admin Finance Result</label>
            <div class="col-md-8">
                <div class="form-control" style="height:auto;"><?php echo $arrResult[$rsm['finance_result']]; ?></div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<div class="row">
    <div class="col-md-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Logistik Summary</label>
            <div class="col-md-8">
                <div class="form-control" style="height:auto;">
                    <?php echo ($rsm['logistik_summary']); ?>
                    <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $rsm['logistik_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['logistik_tgl_proses']))." WIB";?></i></p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Logistik Result</label>
            <div class="col-md-8">
                <div class="form-control" style="height:auto;"><?php echo $arrResult[$rsm['logistik_result']]; ?></div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<?php if($rsm['sm_result']) {?>
<div class="row">
    <div class="col-md-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Logistik Summary</label>
            <div class="col-md-8">
                <div class="form-control" style="height:auto;">
                    <?php echo ($rsm['sm_summary']); ?>
                    <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $rsm['sm_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['sm_tgl_proses']))." WIB";?></i></p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Logistik Result</label>
            <div class="col-md-8">
                <div class="form-control" style="height:auto;"><?php echo $arrSetuju[$rsm['sm_result']]; ?></div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<?php if($rsm['om_result']) {?>
<div class="row">
    <div class="col-md-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Operation Manager Summary</label>
            <div class="col-md-8">
                <div class="form-control" style="height:auto;">
                    <?php echo ($rsm['om_summary']); ?>
                    <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $rsm['om_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['om_tgl_proses']))." WIB";?></i></p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group form-group-sm">
            <label class="control-label col-md-4">Operation Manager Result</label>
            <div class="col-md-8">
                <div class="form-control" style="height:auto;"><?php echo $arrSetuju[$rsm['om_result']]; ?></div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<hr style="margin:15px 0px; border-top:4px double #ddd;" />

<div style="margin-bottom:0px;">
    <input type="hidden" name="idr" value="<?php echo $idr;?>" />
    <?php if(!$rsm['logistik_result']){ ?>
        <button type="submit" name="btnSbmt" id="btnSbmt" class="btn btn-primary jarak-kanan" style="min-width:90px;">
        <i class="fa fa-save jarak-kanan"></i> Simpan</button> 
    <?php } ?>
    <a class="btn btn-default" style="min-width:90px;" href="<?php echo BASE_URL_CLIENT."/verifikasi-data-customer.php";?>">
    <i class="fa fa-reply jarak-kanan"></i> Batal</a>
</div>


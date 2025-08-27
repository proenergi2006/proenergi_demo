<?php 
	$logistik_env 		= isset($rsm['logistik_env'])?$rsm['logistik_env']:$_SESSION['post'][$idr]['logistik_env']; 
	$logistik_storage 	= isset($rsm['logistik_storage'])?$rsm['logistik_storage']:$_SESSION['post'][$idr]['logistik_storage']; 
	$logistik_hour 		= isset($rsm['logistik_hour'])?$rsm['logistik_hour']:$_SESSION['post'][$idr]['logistik_hour']; 
	$logistik_volume 	= isset($rsm['logistik_volume'])?$rsm['logistik_volume']:$_SESSION['post'][$idr]['logistik_volume']; 
	$logistik_quality 	= isset($rsm['logistik_quality'])?$rsm['logistik_quality']:$_SESSION['post'][$idr]['logistik_quality']; 
	$logistik_truck 	= isset($rsm['logistik_truck'])?$rsm['logistik_truck']:$_SESSION['post'][$idr]['logistik_truck']; 
?>

<fieldset id="set-logistic"> 
    <div class="form-title">
    	<h3>LOGISTIC</h3><small>Step 5/5</small>
	</div>
    <div class="form-main">
    	<h3 class="form-main-title"><i class="fa fa-list jarak-kanan"></i> DESCRIPTION AND CAPACITY OF FACILITIES BUSINESS LOCATION</h3>
        <div style="margin:0px 30px;">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Area (Luas Lokasi)</label>
                        <textarea name="logistik_area" id="logistik_area" class="form-control"><?php 
                        $logistik_area = changeValue($rsm, 'logistik_area');
                        echo str_replace("<br />", PHP_EOL, $logistik_area); ?></textarea>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Security Environment / Business Area</label>
                        <textarea name="logistik_bisnis" id="logistik_bisnis" class="form-control"><?php 
                        $logistik_bisnis = changeValue($rsm, 'logistik_bisnis');
                        echo str_replace("<br />", PHP_EOL, $logistik_bisnis); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <label>Conditions Around Locations</label>
                    <div class="form-group">
                        <div class="radio clearfix">
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="radio" name="logistik_env" id="logistik_env1" value="1" class="form-control"<?php echo($logistik_env == 1)?' checked="checked"':''; ?> /> 
                                Industri
                            </label>
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="radio" name="logistik_env" id="logistik_env2" value="2" class="form-control"<?php echo($logistik_env == 2)?' checked="checked"':''; ?> /> 
                                Pemukiman
                            </label>
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="radio" name="logistik_env" id="logistik_env3" value="3" class="form-control"<?php echo($logistik_env == 3)?' checked="checked"':''; ?> /> 
                                Other, 
                            </label>
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="text" name="logistik_env_other" id="logistik_env_other" class="form-control" placeholder="Specify" <?php echo $logistik_env_other; ?> /> 
                            </label>
						</div>
                    </div>
                </div>
    
                <div class="col-sm-6">
                    <label>Storage Facility</label>
                    <div class="form-group">
                        <div class="radio clearfix">
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="radio" name="logistik_storage" id="logistik_storage1" value="1" class="form-control"<?php echo($logistik_storage == 1)?' checked="checked"':''; ?> /> 
                                Indoor
                            </label>
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="radio" name="logistik_storage" id="logistik_storage2" value="2" class="form-control"<?php echo($logistik_storage == 2)?' checked="checked"':''; ?> /> 
                                Outdoor
                            </label>
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="radio" name="logistik_storage" id="logistik_storage3" value="3" class="form-control"<?php echo($logistik_storage == 3)?' checked="checked"':''; ?> /> 
                                Other, 
                            </label>
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="text" name="logistik_storage_other" id="logistik_storage_other" class="form-control" placeholder="Specify" <?php echo ($logistik_storage_other)?$logistik_storage_other:$_SESSION['post'][$idr]['logistik_storage_other']; ?> /> 
                            </label>
                        </div>
                    </div>
                </div>
            </div>		

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Description Of Condition</label>
                        <textarea name="desc_condition" id="desc_condition" class="form-control"><?php 
                        $desc_condition = changeValue($rsm, 'desc_condition');
                        echo str_replace("<br />", PHP_EOL, $desc_condition); ?></textarea>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Description Of Storage Facility</label>
                        <textarea name="desc_stor_fac" id="desc_stor_fac" class="form-control"><?php 
                        $desc_stor_fac = changeValue($rsm, 'desc_stor_fac');
                        echo str_replace("<br />", PHP_EOL, $desc_stor_fac); ?></textarea>
                    </div>
                </div>
            </div>
		</div>

    	<h3 class="form-main-title"><i class="fa fa-list jarak-kanan"></i> LOGISTICS DETAIL</h3>
        <div style="margin:0px 30px;">
            <div class="row">
                <div class="col-sm-6">
                    <label>Operating Hours</label>
                    <div class="form-group">
                        <div class="radio clearfix">
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="radio" name="logistik_hour" id="logistik_hour1" value="1" class="form-control"<?php echo($logistik_hour == 1)?' checked="checked"':''; ?> /> 
                                08.00 - 17.00
                            </label>
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="radio" name="logistik_hour" id="logistik_hour2" value="2" class="form-control"<?php echo($logistik_hour == 2)?' checked="checked"':''; ?> /> 
                                24 Hours
                            </label>
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="radio" name="logistik_hour" id="logistik_hour3" value="3" class="form-control"<?php echo($logistik_hour == 3)?' checked="checked"':''; ?> /> 
                                Other, 
                            </label>
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="text" name="logistik_hour_other" id="logistik_hour_other" class="form-control" placeholder="Specify" <?php echo ($logistik_hour_other)?$logistik_hour_other:$_SESSION['post'][$idr]['logistik_hour_other']; ?> />
                            </label>
                        </div>
                    </div>
                </div>
    
                <div class="col-sm-6">
                    <label>Volume Measurement</label>
                    <div class="form-group">
                        <div class="radio clearfix">
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="radio" name="logistik_volume" id="logistik_volume1" value="1" class="form-control"<?php echo($logistik_volume == 1)?' checked="checked"':''; ?> /> 
                                PRO ENERGY'S TANK LORRY
                            </label>
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="radio" name="logistik_volume" id="logistik_volume2" value="2" class="form-control"<?php echo($logistik_volume == 2)?' checked="checked"':''; ?> /> 
                                Flowmeter
                            </label>
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="radio" name="logistik_volume" id="logistik_volume3" value="3" class="form-control"<?php echo($logistik_volume == 3)?' checked="checked"':''; ?> /> 
                                Other, 
                            </label>
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="text" name="logistik_volume_other" id="logistik_volume_other" class="form-control" placeholder="Specify" <?php 
                                echo ($logistik_volume_other)?$logistik_volume_other:$_SESSION['post'][$idr]['logistik_volume_other']; ?> />
                            </label>
                        </div>
                    </div>
                </div>
			</div>

            <div class="row">
                <div class="col-sm-6">
                    <label>Quality Checking</label>
                    <div class="form-group">
                        <div class="radio clearfix">
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="radio" name="logistik_quality" id="logistik_quality1" value="1" class="form-control"<?php echo($logistik_quality == 1)?' checked="checked"':''; ?> /> 
                                DENSITY
                            </label>
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="radio" name="logistik_quality" id="logistik_quality2" value="2" class="form-control"<?php echo($logistik_quality == 2)?' checked="checked"':''; ?> /> 
                                Other, 
                            </label>
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="text" name="logistik_quality_other" id="logistik_quality_other" class="form-control" placeholder="Specify" <?php 
                                echo ($logistik_quality_other)?$logistik_quality_other:$_SESSION['post'][$idr]['logistik_quality_other']; ?> />
                            </label>
                        </div>
                    </div>
                </div>
    
                <div class="col-sm-6">
                    <label>Max. Truck Capacity</label>
                    <div class="form-group">
                        <div class="radio clearfix">
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="radio" name="logistik_truck" id="logistik_truck1" value="1" class="form-control"<?php echo($logistik_truck == 1)?' checked="checked"':''; ?> /> 
                                5 KL
                            </label>
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="radio" name="logistik_truck" id="logistik_truck2" value="2" class="form-control"<?php echo($logistik_truck == 2)?' checked="checked"':''; ?> /> 
                                8 KL
                            </label>
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="radio" name="logistik_truck" id="logistik_truck3" value="3" class="form-control"<?php echo($logistik_truck == 3)?' checked="checked"':''; ?> /> 
                                10 KL
                            </label>
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="radio" name="logistik_truck" id="logistik_truck4" value="4" class="form-control"<?php echo($logistik_truck == 4)?' checked="checked"':''; ?> /> 
                                16 KL
                            </label>
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="radio" name="logistik_truck" id="logistik_truck5" value="5" class="form-control"<?php echo($logistik_truck == 5)?' checked="checked"':''; ?> /> 
                                Other, 
                            </label>
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="text" name="logistik_truck_other" id="logistik_truck_other" class="form-control" placeholder="Specify" <?php 
                                echo ($logistik_truck_other)?$logistik_truck_other:$_SESSION['post'][$idr]['logistik_truck_other']; ?> />
                            </label>
                        </div>
                    </div>
                </div>

            </div>
		</div>

    </div>
</fieldset>

<fieldset id="set-agreement"> 
    <div class="form-title">
    	<h3>AGREEMENT</h3>
	</div>
    <div class="form-main">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Updated By</label>
                    <input type="text" name="update_by" id="update_by" class="form-control" required value="<?php echo changeValue($rsm, 'lastupdate_by') ?>" />
                </div>
            </div>
            <div class="col-sm-6">
                <label>Captcha *</label>
                <div id="CaptchaDIV"><?php echo $Captcha; ?></div>
                <input name="CaptchaCode" id="CaptchaCode" type="text" class="form-control" required style="width:200px; margin:12px 0px 0px 30px;" />
            </div>
		</div>

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <div class="radio clearfix">
                        <label class="col-sm-12">
                        	<input type="checkbox" name="agreement" id="agreement" class="form-control" required value="1" />
                        	I declare that the above data is true (Dengan ini saya menyatakan bahwa data diatas benar adanya)
                        </label>
                    </div>
                </div>
            </div>
		</div>

        <hr style="border-top:4px double #ddd; margin:20px 0px;" />
        
        <div style="margin-bottom:0px;">
            <input type="hidden" name="idr" value="<?php echo paramEncrypt($idr);?>" />
            <button type="submit" class="btn btn-primary" name="btnSubmit" id="btnSubmit" style="min-width:90px;">
            <i class="fa fa-save jarak-kanan"></i> Simpan</button>
        </div>

    </div>
</fieldset>

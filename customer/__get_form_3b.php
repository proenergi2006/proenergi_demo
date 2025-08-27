<fieldset id="set-logistic">
    <div class="form-title"><h3>Logistic</h3><small>Step 3/3</small></div>
    <div class="form-main">
    	<h3 class="form-main-title">I. DESCRIPTION AND CAPACITY OF FACILITIES BUSINESS LOCATION</h3>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Area</label>
                    <textarea name="logistik_area" id="logistik_area" class="form-control validate[required]"><?php echo $rsm['logistik_area']; ?></textarea>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Security Environment / Business Area</label>
                    <textarea name="logistik_bisnis" id="logistik_bisnis" class="form-control validate[required]"><?php echo $rsm['logistik_bisnis']; ?></textarea>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
            	<label>Description Around The Environment</label>
                <div class="form-group">
                    <div class="radio clearfix" style="margin-top: 0px">
                        <label class="col-xs-12" style="margin-bottom:5px;">
                            <input type="radio" name="logistik_env" id="logistik_env1" value="1" class="validate[required]"<?php echo($rsm['logistik_env'] == 1)?' checked="checked"':''; ?>/> Industri
                        </label>
                        <label class="col-xs-12" style="margin-bottom:5px;">
                            <input type="radio" name="logistik_env" id="logistik_env2" value="2" class="validate[required]"<?php echo($rsm['logistik_env'] == 2)?' checked="checked"':''; ?> /> Pemukiman
                        </label>
                        <label class="col-xs-12" style="margin-bottom:5px;">
                            <input type="radio" name="logistik_env" id="logistik_env3" value="3" class="validate[required]"<?php echo($rsm['logistik_env'] == 3)?' checked="checked"':''; ?> /> Other, 
                        </label>
                        <div class="row">
                            <div class="col-sm-10 col-md-8">
                                <input type="text" name="logistik_env_other" id="logistik_env_other" class="form-control validate[required]" placeholder="Specify" <?php echo $logistik_env_other; ?> />
                            </div>
						</div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
            	<label>Storage</label>
                <div class="form-group">
                    <div class="radio clearfix" style="margin-top: 0px">
                        <label class="col-xs-12" style="margin-bottom:5px;">
                            <input type="radio" name="logistik_storage" id="logistik_storage1" value="1" class="validate[required]"<?php echo($rsm['logistik_storage'] == 1)?' checked="checked"':''; ?> /> Indoor
                        </label>
                        <label class="col-xs-12" style="margin-bottom:5px;">
                            <input type="radio" name="logistik_storage" id="logistik_storage2" value="2" class="validate[required]"<?php echo($rsm['logistik_storage'] == 2)?' checked="checked"':''; ?> /> Outdoor
                        </label>
                        <label class="col-xs-12" style="margin-bottom:5px;">
                            <input type="radio" name="logistik_storage" id="logistik_storage3" value="3" class="validate[required]"<?php echo($rsm['logistik_storage'] == 3)?' checked="checked"':''; ?> /> Other, 
                        </label>
                        <div class="row">
                            <div class="col-sm-10 col-md-8">
                                <input type="text" name="logistik_storage_other" id="logistik_storage_other" class="form-control validate[required]" placeholder="Specify" <?php echo $logistik_storage_other; ?> />
                            </div>
						</div>
                    </div>
                </div>
            </div>

        </div>

    	<h3 class="form-main-title">II. LOGISTICS DETAIL</h3>
        <div class="row">
            <div class="col-sm-6">
            	<label>Operating Hours</label>
                <div class="form-group">
                    <div class="radio clearfix" style="margin-top: 0px">
                        <label class="col-xs-12" style="margin-bottom:5px;">
                            <input type="radio" name="logistik_hour" id="logistik_hour1" value="1" class="validate[required]"<?php echo($rsm['logistik_hour'] == 1)?' checked="checked"':''; ?> /> 08.00 - 17.00
                        </label>
                        <label class="col-xs-12" style="margin-bottom:5px;">
                            <input type="radio" name="logistik_hour" id="logistik_hour2" value="2" class="validate[required]"<?php echo($rsm['logistik_hour'] == 2)?' checked="checked"':''; ?> /> 24 Hours
                        </label>
                        <label class="col-xs-12" style="margin-bottom:5px;">
                            <input type="radio" name="logistik_hour" id="logistik_hour3" value="3" class="validate[required]"<?php echo($rsm['logistik_hour'] == 3)?' checked="checked"':''; ?> /> Other, 
                        </label>
                        <div class="row">
                            <div class="col-sm-10 col-md-8">
                                <input type="text" name="logistik_hour_other" id="logistik_hour_other" class="form-control validate[required]" placeholder="Specify" <?php echo $logistik_hour_other; ?> />
                            </div>
						</div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
            	<label>Volume Measurement</label>
                <div class="form-group">
                    <div class="radio clearfix" style="margin-top: 0px">
                        <label class="col-xs-12" style="margin-bottom:5px;">
                            <input type="radio" name="logistik_volume" id="logistik_volume1" value="1" class="validate[required]"<?php echo($rsm['logistik_volume'] == 1)?' checked="checked"':''; ?> /> Flowmeter
                        </label>
                        <label class="col-xs-12" style="margin-bottom:5px;">
                            <input type="radio" name="logistik_volume" id="logistik_volume2" value="2" class="validate[required]"<?php echo($rsm['logistik_volume'] == 2)?' checked="checked"':''; ?> /> Stick
                        </label>
                        <label class="col-xs-12" style="margin-bottom:5px;">
                            <input type="radio" name="logistik_volume" id="logistik_volume3" value="3" class="validate[required]"<?php echo($rsm['logistik_volume'] == 3)?' checked="checked"':''; ?> /> Other, 
                        </label>
                        <div class="row">
                            <div class="col-sm-10 col-md-8">
                                <input type="text" name="logistik_volume_other" id="logistik_volume_other" class="form-control validate[required]" placeholder="Specify" <?php echo $logistik_volume_other; ?> />
                            </div>
						</div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-sm-6">
            	<label>Quality Checking</label>
                <div class="form-group">
                    <div class="radio clearfix" style="margin-top: 0px">
                        <label class="col-xs-12" style="margin-bottom:5px;">
                            <input type="radio" name="logistik_quality" id="logistik_quality1" value="1" class="validate[required]"<?php echo($rsm['logistik_quality'] == 1)?' checked="checked"':''; ?> /> BJ
                        </label>
                        <label class="col-xs-12" style="margin-bottom:5px;">
                            <input type="radio" name="logistik_quality" id="logistik_quality2" value="2" class="validate[required]"<?php echo($rsm['logistik_quality'] == 2)?' checked="checked"':''; ?> /> Other, 
                        </label>
                        <div class="row">
                            <div class="col-sm-10 col-md-8">
                                <input type="text" name="logistik_quality_other" id="logistik_quality_other" class="form-control validate[required]" placeholder="Specify" <?php echo $logistik_quality_other; ?> />
                            </div>
						</div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
            	<label>Truck Capacity</label>
                <div class="form-group">
                    <div class="radio clearfix" style="margin-top: 0px">
                        <label class="col-xs-12" style="margin-bottom:5px;">
                            <input type="radio" name="logistik_truck" id="logistik_truck1" value="1" class="validate[required]"<?php echo($rsm['logistik_truck'] == 1)?' checked="checked"':''; ?> /> 5 KL
                        </label>
                        <label class="col-xs-12" style="margin-bottom:5px;">
                            <input type="radio" name="logistik_truck" id="logistik_truck2" value="2" class="validate[required]"<?php echo($rsm['logistik_truck'] == 2)?' checked="checked"':''; ?> /> 8 KL
                        </label>
                        <label class="col-xs-12" style="margin-bottom:5px;">
                            <input type="radio" name="logistik_truck" id="logistik_truck3" value="3" class="validate[required]"<?php echo($rsm['logistik_truck'] == 3)?' checked="checked"':''; ?> /> 10 KL
                        </label>
                        <label class="col-xs-12" style="margin-bottom:5px;">
                            <input type="radio" name="logistik_truck" id="logistik_truck4" value="4" class="validate[required]"<?php echo($rsm['logistik_truck'] == 4)?' checked="checked"':''; ?> /> 16 KL
                        </label>
                        <label class="col-xs-12" style="margin-bottom:5px;">
                            <input type="radio" name="logistik_truck" id="logistik_truck5" value="5" class="validate[required]"<?php echo($rsm['logistik_truck'] == 5)?' checked="checked"':''; ?> /> Other, 
                        </label>
                        <div class="row">
                            <div class="col-sm-10 col-md-8">
                                <input type="text" name="logistik_truck_other" id="logistik_truck_other" class="form-control validate[required]" placeholder="Specify" <?php echo $logistik_truck_other; ?> />
                            </div>
						</div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="pad bg-gray">
                    <input type="hidden" name="idr" value="<?php echo $idr;?>" />
                    <input type="hidden" name="idk" value="<?php echo $idk;?>" />
                    <a href="<?php echo BASE_REFERER; ?>" class="btn btn-default jarak-kanan"><i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                    <button type="submit" class="btn btn-primary" name="btnSubmit" id="btnSubmit"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
                </div>
            </div>
        </div>
    </div>
</fieldset>

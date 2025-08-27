<fieldset id="set-company4">
    <div class="form-title">
        <h3>4. SUPPLY SCHEME</h3><small>Step 4/5</small>
    </div>
    <div class="form-main">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="form-main-title"><i class="fa fa-list jarak-kanan"></i> SUPPLY SCHEME DETAILS *</h3>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <?php $supply_shceme = isset($rsm['supply_shceme']) ? $rsm['supply_shceme'] : $_SESSION['post'][$idr]['supply_shceme']; ?>
                            <select name="supply_shceme" id="supply_shceme" class="form-control select2" required>
                                <option></option>
                                <option value="1" <?php echo ($supply_shceme == 1) ? ' selected' : ''; ?>>Trucking</option>
                                <option value="2" <?php echo ($supply_shceme == 2) ? ' selected' : ''; ?>>SPOB/Vassel</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <h3 class="form-main-title"><i class="fa fa-list jarak-kanan"></i> Print Product *</h3>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <input type="text" name="print_product" id="print_product" class="form-control" value="<?php echo changeValue($rsm, 'print_product') ?>" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div>&nbsp;</div>
        <div class="row">
            <div class="col-sm-6">
                <h3 class="form-main-title"><i class="fa fa-list jarak-kanan"></i> SPECIFY PRODUCT *</h3>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <?php $specify_product = isset($rsm['specify_product']) ? $rsm['specify_product'] : $_SESSION['post'][$idr]['specify_product']; ?>
                            <select name="specify_product" id="specify_product" class="form-control select2" required>
                                <option></option>
                                <option value="1" <?php echo ($specify_product == 1) ? ' selected' : ''; ?>>Prodiesel Bio (Bio Diesel)</option>
                                <option value="2" <?php echo ($specify_product == 2) ? ' selected' : ''; ?>>Promarine (MFO)</option>
                                <option value="3" <?php echo ($specify_product == 3) ? ' selected' : ''; ?>>Eneos (Lubricant)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <h3 class="form-main-title"><i class="fa fa-list jarak-kanan"></i> VOLUME PER MONTH *</h3>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <input type="text" name="volume_per_month" id="volume_per_month" class="form-control angkabiasa" required value="<?php echo changeValue($rsm, 'volume_per_month') ?>" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div>&nbsp;</div>
        <div class="row">
            <div class="col-sm-6">
                <h3 class="form-main-title"><i class="fa fa-list jarak-kanan"></i> OPERATIONAL HOUR *</h3>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">FROM</span>
                                <input type="text" name="operational_hour_from" id="operational_hour_from" class="form-control" value="<?php echo changeValue($rsm, 'operational_hour_from') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <span class="input-group-addon">TO</span>
                            <input type="text" name="operational_hour_to" id="operational_hour_to" class="form-control" value="<?php echo changeValue($rsm, 'operational_hour_to') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <h3 class="form-main-title"><i class="fa fa-list jarak-kanan"></i> INCO TERMS *</h3>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <?php $nico = isset($rsm['nico']) ? $rsm['nico'] : $_SESSION['post'][$idr]['nico']; ?>
                            <select name="nico" id="nico" class="form-control select2" required>
                                <option></option>
                                <option value="1" <?php echo ($nico == 1) ? ' selected' : ''; ?>>Loco</option>
                                <option value="2" <?php echo ($nico == 2) ? ' selected' : ''; ?>>Delivered</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</fieldset>
<fieldset id="set-company2"> 
    <div class="form-title">
    	<h3>2. PERSON IN CHARGE DETAILS</h3><small>Step 2/5</small>
	</div>
    <div class="form-main">
        <div class="row">
            <div class="col-sm-6">
                <p><b><span style="width:20px; display:inline-block;">1.</span> <u>Decision Makers</u></b></p>
                <div class="form-horizontal" style="padding:0px 25px">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Name *</label>
                        <div class="col-sm-9">
                        	<input type="text" name="pic_decision_name" id="pic_decision_name" class="form-control" required value="<?php echo changeValue($rsm, 'pic_decision_name') ?>" />
						</div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Position *</label>
                        <div class="col-sm-9">
                        	<input type="text" name="pic_decision_position" id="pic_decision_position" class="form-control" required value="<?php echo changeValue($rsm, 'pic_decision_position') ?>" />
						</div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Telephone *</label>
                        <div class="col-sm-9">
                        	<input type="text" name="pic_decision_telp" id="pic_decision_telp" class="form-control phone-number" required value="<?php echo changeValue($rsm, 'pic_decision_telp') ?>" />
						</div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Mobile *</label>
                        <div class="col-sm-9">
                        	<input type="text" name="pic_decision_mobile" id="pic_decision_mobile" class="form-control phone-number" required value="<?php echo changeValue($rsm, 'pic_decision_mobile') ?>" />
						</div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Email</label>
                        <div class="col-sm-9">
                        	<input type="text" name="pic_decision_email" id="pic_decision_email" class="form-control" data-rule-email="1" value="<?php echo changeValue($rsm, 'pic_decision_email') ?>" />
						</div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
            	<p><b><span style="width:20px; display:inline-block;">1.</span> <u>Ordering Goods</u></b></p>
                <div class="form-horizontal" style="padding:0px 25px">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Name *</label>
                        <div class="col-sm-9">
                        	<input type="text" name="pic_ordering_name" id="pic_ordering_name" class="form-control" required value="<?php echo changeValue($rsm, 'pic_ordering_name') ?>" />
						</div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Position *</label>
                        <div class="col-sm-9">
                        	<input type="text" name="pic_ordering_position" id="pic_ordering_position" class="form-control" required value="<?php echo changeValue($rsm, 'pic_ordering_position') ?>" />
						</div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Telephone *</label>
                        <div class="col-sm-9">
                        	<input type="text" name="pic_ordering_telp" id="pic_ordering_telp" class="form-control phone-number" required value="<?php echo changeValue($rsm, 'pic_ordering_telp') ?>" />
						</div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Mobile *</label>
                        <div class="col-sm-9">
                        	<input type="text" name="pic_ordering_mobile" id="pic_ordering_mobile" class="form-control phone-number" required value="<?php echo changeValue($rsm, 'pic_ordering_mobile') ?>" />
						</div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Email</label>
                        <div class="col-sm-9">
                        	<input type="text" name="pic_ordering_email" id="pic_ordering_email" class="form-control" data-rule-email="1" value="<?php echo changeValue($rsm, 'pic_ordering_email') ?>" />
						</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <p><b><span style="width:20px; display:inline-block;">3.</span> <u>Billing Receiver</u></b></p>
                <div class="form-horizontal" style="padding:0px 25px">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Name *</label>
                        <div class="col-sm-9">
                        	<input type="text" name="pic_billing_name" id="pic_billing_name" class="form-control" required value="<?php echo changeValue($rsm, 'pic_billing_name') ?>" />
						</div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Position *</label>
                        <div class="col-sm-9">
                        	<input type="text" name="pic_billing_position" id="pic_billing_position" class="form-control" required value="<?php echo changeValue($rsm, 'pic_billing_position') ?>" />
						</div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Telephone *</label>
                        <div class="col-sm-9">
                        	<input type="text" name="pic_billing_telp" id="pic_billing_telp" class="form-control phone-number" required value="<?php echo changeValue($rsm, 'pic_billing_telp') ?>" />
						</div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Mobile *</label>
                        <div class="col-sm-9">
                        	<input type="text" name="pic_billing_mobile" id="pic_billing_mobile" class="form-control phone-number" required value="<?php echo changeValue($rsm, 'pic_billing_mobile') ?>" />
						</div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Email</label>
                        <div class="col-sm-9">
                        	<input type="text" name="pic_billing_email" id="pic_billing_email" class="form-control" data-rule-email="1" value="<?php echo changeValue($rsm, 'pic_billing_email') ?>" />
						</div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
            	<p><b><span style="width:20px; display:inline-block;">4.</span> <u>Site / Fuelman PIC</u></b></p>
                <div class="form-horizontal" style="padding:0px 25px">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Name *</label>
                        <div class="col-sm-9">
                        	<input type="text" name="pic_fuelman_name" id="pic_fuelman_name" class="form-control" required value="<?php echo changeValue($rsm, 'pic_fuelman_name') ?>" />
						</div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Position *</label>
                        <div class="col-sm-9">
                        	<input type="text" name="pic_fuelman_position" id="pic_fuelman_position" class="form-control" required value="<?php echo changeValue($rsm, 'pic_fuelman_position') ?>" />
						</div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Telephone *</label>
                        <div class="col-sm-9">
                        	<input type="text" name="pic_fuelman_telp" id="pic_fuelman_telp" class="form-control phone-number" required value="<?php echo changeValue($rsm, 'pic_fuelman_telp') ?>" />
						</div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Mobile *</label>
                        <div class="col-sm-9">
                        	<input type="text" name="pic_fuelman_mobile" id="pic_fuelman_mobile" class="form-control phone-number" required value="<?php echo changeValue($rsm, 'pic_fuelman_mobile') ?>" />
						</div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Email</label>
                        <div class="col-sm-9">
                        	<input type="text" name="pic_fuelman_email" id="pic_fuelman_email" class="form-control" data-rule-email="1" value="<?php echo changeValue($rsm, 'pic_fuelman_email') ?>" />
						</div>
                    </div>
                </div>
            </div>
        </div>

	</div>
</fieldset>
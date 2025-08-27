<?php 
	$calculate_method 	= isset($rsm['calculate_method'])?$rsm['calculate_method']:$_SESSION['post'][$idr]['calculate_method']; 
	$payment_method 	= isset($rsm['payment_method'])?$rsm['payment_method']:$_SESSION['post'][$idr]['payment_method']; 
	$payment_schedule 	= isset($rsm['payment_schedule'])?$rsm['payment_schedule']:$_SESSION['post'][$idr]['payment_schedule']; 
	$invoice 			= isset($rsm['invoice'])?$rsm['invoice']:$_SESSION['post'][$idr]['invoice']; 
?>

<fieldset id="set-payment"> 
    <div class="form-title">
    	<h3>3. PAYMENT TERM &amp; BANKING DETAIL</h3><small>Step 3/5</small>
	</div>
    <div class="form-main">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="form-main-title"><i class="fa fa-list jarak-kanan"></i> PRICING METHOD CALCULATION *</h3>
                <div style="margin:0px 15px;">
                    <div class="form-group">
                        <div class="radio clearfix">
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="radio" name="calculate_method" id="calculate_method1" value="1" class="form-control" required <?php echo($calculate_method == 1)?'checked':''; ?> /> 
                                Discount Pricelist
                            </label>
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="radio" name="calculate_method" id="calculate_method2" value="2" class="form-control" required <?php echo($calculate_method == 2)?'checked':''; ?> /> 
                                Formula MOPS
                            </label>
                        </div>
                    </div>
				</div>
            </div>

            <div class="col-sm-6">
                <h3 class="form-main-title"><i class="fa fa-list jarak-kanan"></i> PAYMENT METODE *</h3>
                <div style="margin:0px 15px;">
                    <div class="form-group">
                        <div class="radio clearfix">
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="radio" name="payment_method" id="payment_method1" value="1" class="form-control" required <?php echo($payment_method == 1)?'checked':''; ?> /> 
                                Cash
                            </label>
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="radio" name="payment_method" id="payment_method2" value="2" class="form-control" required <?php echo($payment_method == 2)?'checked':''; ?> /> 
                                Transfer
                            </label>
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="radio" name="payment_method" id="payment_method3" value="3" class="form-control" required <?php echo($payment_method == 3)?'checked':''; ?> /> 
                                Cheque / Giro
                            </label>
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="radio" name="payment_method" id="payment_method4" value="4" class="form-control" required <?php echo($payment_method == 4)?'checked':''; ?> /> 
                                Bank Guarantee
                            </label>
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="radio" name="payment_method" id="payment_method5" value="5" class="form-control" required <?php echo($payment_method == 5)?'checked':''; ?> /> 
                                Other, 
                            </label>
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="text" name="payment_method_other" id="payment_method_other" class="form-control" required placeholder="Specify" <?php echo ($payment_method_other)?$payment_method_other:$_SESSION['post'][$idr]['payment_method_other']; ?> />
                            </label>
                        </div>
                    </div>
				</div>
            </div>
        </div>

        <h3 class="form-main-title"><i class="fa fa-list jarak-kanan"></i> PAYMENT TERM</h3>
        <div style="margin:0px 30px;">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                      <label>Payment Type</label>
                        <?php $jenis_payment = $rsm['jenis_payment'] ?? null; ?>
                        <select name="jenis_payment" id="jenis_payment" class="form-control select2" required>
                            <option></option>
                            <option value="CBD" <?php echo ($jenis_payment == 'CBD')?' selected':''; ?>>CBD (Cash Before Delivery)</option>
                            <option value="COD" <?php echo ($jenis_payment == 'COD')?' selected':''; ?>>COD (Cash On Delivery)</option>
                            <option value="CREDIT" <?php echo ($jenis_payment == 'CREDIT')?' selected':''; ?>>CREDIT</option>
                        </select>
                    </div>
                </div>
                <div id="jwp" class="col-sm-4 <?php echo ($jenis_payment != 'CREDIT')?' hide':''; ?>">
                    <div class="form-group">
                        <label>TOP (Top of Payment)</label>
                        <div class="input-group">
                            <input type="text" name="top_payment" id="top_payment" class="form-control text-right" required value="<?php echo changeValue($rsm, 'top_payment') ?>" />
                            <span class="input-group-addon">Days</span>
                        </div>
                    </div>
                </div>
                <div id="jwp2" class="col-sm-4 <?php echo ($jenis_payment == "CREDIT" || (isset($rsm['jenis_waktu']) && $rsm['jenis_waktu'] == "CREDIT") ? '' : ' hide') ?>">
                    <label>&nbsp;</label>
                    <?php $jenis_net = $rsm['jenis_net']; ?>
                    <select name="jenis_net" id="jenis_net" class="form-control select2" required>
                        <option></option>
                        <option value="3" <?php echo ($jenis_net == '3')?' selected':''; ?>>After Loading</option>
                        <option value="1" <?php echo ($jenis_net == '1')?' selected':''; ?>>After Invoice Receive</option>
                        <option value="2" <?php echo ($jenis_net == '2')?' selected':''; ?>>After Delivery</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Bank Name / Nama Bank</label>
                        <input type="text" name="bank_name" id="bank_name" class="form-control" required value="<?php echo changeValue($rsm, 'bank_name') ?>" />
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Curency / Mata Uang</label>
                        <input type="text" name="curency" id="curency" class="form-control" required value="<?php echo changeValue($rsm, 'curency') ?>" />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Bank Address / Alamat Bank</label>
                        <textarea name="bank_address" id="bank_address" class="form-control"><?php echo changeValue($rsm, 'bank_address') ?></textarea>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Account Number / Nomor Rekening</label>
                        <input type="text" name="account_number" id="account_number" class="form-control" required value="<?php echo changeValue($rsm, 'account_number') ?>" />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <?php $credit_facility = isset($rsm['credit_facility'])?$rsm['credit_facility']:$_SESSION['post'][$idr]['credit_facility']; ?>
                        <label>Have Credit Facility or Bank Loan? / Punya Fasilitas Kredit atau Pinjaman Bank ?</label>
                        <div class="radio clearfix">
                            <label class="radio-inline" style="margin-bottom:5px;">
                                <input type="radio" name="credit_facility" id="credit_facility1" value="1" class="form-control" required <?php echo($credit_facility == 1)?'checked':''; ?> /> Yes
                            </label>
                            <label class="radio-inline" style="margin-bottom:5px;">
                                <input type="radio" name="credit_facility" id="credit_facility2" value="0" class="form-control" required <?php echo($credit_facility == 0)?'checked':''; ?> /> No
                            </label>
						</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Nama Penyedia Kredit atau Pinjaman</label>
                        <input type="text" name="creditor" id="creditor" class="form-control " placeholder="Specify" <?php echo ($creditor)?$creditor:$_SESSION['post'][$idr]['creditor']; ?> />
                    </div>
                </div>
            </div>
		</div>

        <div>&nbsp;</div>
        <div>&nbsp;</div>
        <div class="row">
            <div class="col-sm-6">
                <h3 class="form-main-title"><i class="fa fa-list jarak-kanan"></i> CASHIER AND PAYMENT SCHEDULE *</h3>
                <div style="margin:0px 15px;">
                    <div class="form-group">
                        <div class="radio clearfix">
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="radio" name="payment_schedule" id="payment_schedule1" value="1" class="form-control" <?php echo($payment_schedule == 1)?'checked':''; ?> /> 
                                Every Day
                            </label>
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="radio" name="payment_schedule" id="payment_schedule2" value="2" class="form-control" <?php echo($payment_schedule == 2)?'checked':''; ?> /> 
                                Other, 
                            </label>
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="text" name="payment_schedule_other" id="payment_schedule_other" class="form-control" required placeholder="Specify" <?php echo ($payment_schedule_other)?$payment_schedule_other:$_SESSION['post'][$idr]['payment_schedule_other']; ?> />
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <h3 class="form-main-title"><i class="fa fa-list jarak-kanan"></i> INVOICES</h3>
                <div style="margin:0px 15px;">
                    <div class="form-group">
                        <div class="radio clearfix">
                            <label class="col-sm-12" style="margin-bottom:5px;">
                                <input type="checkbox" name="invoice" id="invoice" value="1" <?php echo($invoice == 1)?'checked':''; ?> /> Tax Invoice (Faktur Pajak)
                            </label>
                        </div>
                    </div>
                </div>
            </div>
		</div>
        
        <div>&nbsp;</div>
        <div class="row">
            <div class="col-sm-8">
                <h3 class="form-main-title"><i class="fa fa-list jarak-kanan"></i> KETERANGAN (Jika Ada)</h3>
                <div style="margin:0px 15px;">
                    <div class="form-group">
                        <textarea name="ket_extra" id="ket_extra" class="form-control"><?php 
                        $ket_extra = changeValue($rsm, 'ket_extra');
                        echo str_replace("<br />", PHP_EOL, $ket_extra); ?></textarea>
                    </div>
                </div>
            </div>
		</div>

    </div>
</fieldset>
<style type="text/css">
.inputRupiah { 
	text-align: right;
}
</style>
<script type="text/javascript">
$(".inputRupiah").number(true, 0, ".", ",");
$(".registration-form").on("ifChecked", "input[name='credit_facility']", function(){
     var nilai = $(this).val();
     if(nilai == 1){
         $("#creditor").removeAttr("disabled");
     } else{
         $("#creditor").attr("disabled", "disabled").val("");
     }
    });
</script>
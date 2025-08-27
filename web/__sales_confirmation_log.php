<?php
$arrSetuju = array(1 => "Yes", "No");
$arrSuplay = array(1 => "Supply", "Not Supply");
$role = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$disabled = "readonly='true'";
$jns_payment = $row['jenis_payment'];
$top_payment = $row['top_payment'];
$arr_payment = array("CREDIT" => "NET " . $top_payment, "COD" => "COD", "CDB" => "CDB", "CBD" => "CBD");
$termPayment = $arr_payment[$jns_payment];
?>
<div id="table-long">
    <div style="height:auto;">
        <div class="table-responsive-satu">
            <table class="table table-bordered" id="table-grid2">
                <thead>
                    <tr>
                        <th class="text-center" width="20">Customer Code</th>
                        <th class="text-center" width="100">Customer Name</th>
                        <th class="text-center" width="50">TOP</th>
                        <th class="text-center" width="70">Credit Limit</th>
                        <th class="text-center" width="70">Business</th>
                        <th class="text-center" width="70">Marketing</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center" width="20"><?php echo $row['kode_pelanggan']; ?></td>
                        <td class="text-center" width="100"><?php echo $row['nama_customer']; ?></td>
                        <td width="50" class="text-center"><?php echo $termPayment; ?></td>
                        <td width="70" class="text-right">
                            <?php echo number_format($row['credit_limit']); ?>
                            <input type="hidden" name="cl" value="<?php echo $row['credit_limit']; ?>" />
                        </td>
                        <td class="text-center" width="70"><?php echo ($row['tipe_bisnis']) ? $arrT[$row['tipe_bisnis']] : ''; ?></td>
                        <td class="text-center" width="70"><?php echo $row['marketing']; ?></td>
                    </tr>
                </tbody>
            </table>
            <br />
            <table class="table table-bordered" id="table-grid2">
                <thead>
                    <tr>
                        <th class="text-center" colspan="6">Balance AR</th>
                    </tr>
                    <tr>
                        <th class="text-center" width="75">NOT YET</th>
                        <th class="text-center" width="60">Overdue 1-30 Days</th>
                        <th class="text-center" width="60">Overdue 31-60 Days</th>
                        <th class="text-center" width="60">Overdue 61-90 Days</th>
                        <th class="text-center" width="60">Overdue > 90 Days</th>
                        <th class="text-center" width="60">Reminding</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td width="75">
                            <div class="input-group">
                                <span class="input-group-addon">Rp.</span>
                                <input autocomplete="off" type="text" name="not_yet" class="form-control input-po text-right hitung" required="" value="<?php echo $row2['not_yet']; ?>" <?php echo $disabled; ?> />
                            </div>
                        </td>
                        <td width="60">
                            <div class="input-group">
                                <span class="input-group-addon">Rp.</span>
                                <input autocomplete="off" type="text" name="ov_under_30" class="form-control input-po text-right hitung" value="<?php echo $row2['ov_under_30']; ?>" <?php echo $disabled; ?> />
                            </div>
                        </td>
                        <td width="60">
                            <div class="input-group">
                                <span class="input-group-addon">Rp.</span>
                                <input autocomplete="off" type="text" name="ov_under_60" class="form-control input-po text-right hitung" value="<?php echo $row2['ov_under_60']; ?>" <?php echo $disabled; ?> />
                            </div>
                        </td>
                        <td width="60">
                            <div class="input-group">
                                <span class="input-group-addon">Rp.</span>
                                <input autocomplete="off" type="text" name="ov_under_90" class="form-control input-po text-right hitung" value="<?php echo $row2['ov_under_90']; ?>" <?php echo $disabled; ?> />
                            </div>
                        </td>
                        <td width="60">
                            <div class="input-group">
                                <span class="input-group-addon">Rp.</span>
                                <input autocomplete="off" type="text" name="ov_up_90" class="form-control input-po text-right hitung" value="<?php echo $row2['ov_up_90']; ?>" <?php echo $disabled; ?> />
                            </div>
                        </td>
                        <td width="60">
                            <div class="input-group">
                                <span class="input-group-addon">Rp.</span>
                                <input autocomplete="off" type="text" name="reminding" class="form-control input-po text-right hitung" value="<?php echo $row2['reminding']; ?>" <?php echo $disabled; ?> />
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <hr style="margin:10px 0px 20px; border-top:4px double #ddd;">
            <br />
            <div class="row">

                <div class="col-sm-6">
                    <label style="padding-left: 30px; padding-bottom: 10px">Customer Type *</label>
                    <div class="form-group">
                        <div class="radio clearfix" style="margin:0px; padding-bottom: 10px">
                            <label class="col-xs-4 type" style="margin-bottom:5px; float: left; padding-left: 30px">
                                <div style="position: relative; float: left">
                                    <input <?php echo $disabled; ?> type="radio" name="type_customer" class="type_customer" value="1" <?php echo ($row2['type_customer'] == 'Customer Commitment') ? 'checked' : ''; ?> style="position: absolute; opacity: 0;" required="">
                                </div> Customer Commitment
                            </label>
                            <label class="col-xs-6 type" style="margin-bottom:5px; padding-left: 25px">
                                <div style="position: relative; float: left">
                                    <input <?php echo $disabled; ?> type="radio" name="type_customer" class="type_customer" value="2" <?php echo ($row2['type_customer'] == 'Customer Collateral') ? 'checked' : ''; ?> style="position: absolute; opacity: 0;" required="">
                                </div> Customer Collateral
                            </label>
                        </div>
                        <div>
                            <div style="padding-left: 30px;" class="col-sm-4">Date
                                <input <?php echo $disabled; ?> type="text" name="customer_date" id="customer_date_log" class="form-control datepicker" value="<?php echo ($row2['customer_date']) ? date('d/m/Y', strtotime($row2['customer_date'])) : ''; ?>">
                            </div>
                            <div style="padding-left: 30px;" class="col-sm-6">Amount
                                <div class="input-group">
                                    <span class="input-group-addon">Rp.</span>
                                    <input <?php echo $disabled; ?> autocomplete="off" type="text" name="customer_amount" class="form-control input-po text-right hitung" value="<?php echo $row2['customer_amount']; ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="item <?php echo ($row2['type_customer'] == 'Customer Collateral') ? '' : 'hidden'; ?>">
                            <div style="padding-left: 30px;" class="col-sm-10" id="item">Item <input <?php echo $disabled; ?> autocomplete="off" type="text" name="item" class="form-control input-po" /></div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <label style="padding-left: 30px; padding-bottom: 10px">PO Type *</label>
                    <div class="form-group">
                        <div class="radio clearfix" style="margin:0px; padding-bottom: 10px">
                            <label class="col-xs-4" style="margin-bottom:5px; float: left; padding-left: 30px">
                                <div style="position: relative; float: left">
                                    <input <?php echo $disabled; ?> type="radio" name="status_po" value="1" <?php echo ($row2['po_status'] == '1') ? 'checked' : ''; ?> style="position: absolute; opacity: 0;" required="">
                                    <ins class="iCheck-helper"></ins>
                                </div> New PO
                            </label>
                            <label class="col-xs-6" style="margin-bottom:5px; padding-left: 25px">
                                <div style="position: relative; float: left">
                                    <input <?php echo $disabled; ?> type="radio" name="status_po" value="2" <?php echo ($row2['po_status'] == '2') ? 'checked' : ''; ?> style="position: absolute; opacity: 0;" required="">
                                    <ins class="iCheck-helper"></ins>
                                </div> Parcial PO / Contract
                            </label>
                        </div>
                        <div>
                            <div style="padding-left: 30px;" class="col-sm-6">Volume
                                <div class="input-group">
                                    <input <?php echo $disabled; ?> autocomplete="off" type="text" name="volume_po" class="form-control input-po text-right hitung" value="<?php echo $row2['po_volume']; ?>" required="" />
                                    <span class="input-group-addon">Liter</span>
                                </div>
                            </div>
                            <div style="padding-left: 30px;" class="col-sm-6">Amount
                                <div class="input-group">
                                    <span class="input-group-addon">Rp.</span>
                                    <input <?php echo $disabled; ?> autocomplete="off" type="text" name="amount_po" value="<?php echo ($row2['po_amount']);  ?>" class="form-control input-po text-right hitung" required="" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <br>
            <div class="row">
                <div class="col-sm-6">
                    <label style="padding-left: 30px; padding-bottom: 10px">Proposed *</label>
                    <div class="form-group">
                        <div class="radio clearfix" style="margin:0px; padding-bottom: 10px">
                            <label class="col-xs-4 proposed" style="margin-bottom:5px; float: left; padding-left: 30px">
                                <div style="position: relative; float: left">
                                    <ins class="iCheck-helper">
                                        <input type="radio" name="proposed" value="0" <?php echo ($row2['proposed_status'] == '0') ? 'checked' : ''; ?> style="position: absolute; opacity: 0;" required="">
                                    </ins>
                                </div> Not Proposed
                            </label>
                            <label class="col-xs-6 proposed" style="margin-bottom:5px; padding-left: 25px">
                                <div style="position: relative; float: left">
                                    <input type="radio" name="proposed" value="1" <?php echo ($row2['proposed_status'] == '1') ? 'checked' : ''; ?> style="position: absolute; opacity: 0;" required="">
                                    <ins class="iCheck-helper"></ins>
                                </div> Proposed
                            </label>
                        </div>

                        <div class="_proposed <?php echo ($row2['proposed_status'] == '1') ? '' : 'hidden'; ?>">
                            <div style="padding-left: 30px;" class="col-sm-6">ADD TOP
                                <input <?php echo $disabled; ?> autocomplete="off" type="text" name="add_top" value="<?php echo $row2['add_top']; ?>" class="form-control input-po text-right hitung" />
                            </div>
                            <div style="padding-left: 30px;" class="col-sm-6">ADD CL
                                <div class="input-group">
                                    <span class="input-group-addon">Rp.</span>
                                    <input <?php echo $disabled; ?> autocomplete="off" type="text" name="add_cl" value="<?php echo $row2['add_cl']; ?>" class="form-control input-po text-right hitung" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="row" style="padding-left: 30px">
    <?php if ($row2['adm_result'] >= '1') { ?>
        <div class="col-sm-6">
            <label>Admin Finance Summary</label>
            <div class="form-control" style="height:auto">
                <?php echo $row2['adm_summary']; ?>
                <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $row2['adm_pic'] . " - " . date("d/m/Y H:i:s", strtotime($row2['adm_result_date'])) . " WIB"; ?></i></p>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>Admin Finance Result *</label>
                <p><?php echo $arrSuplay[$row2['adm_result']]; ?></p>
            </div>
        </div>
    <?php } ?>
</div>
<br>

<div class="row" style="padding-left: 30px">
    <?php if ($row2['bm_result'] >= '1') { ?>
        <div class="col-sm-6">
            <label>Branch Manager Summary</label>
            <div class="form-control" style="height:auto">
                <?php echo $row2['bm_summary']; ?>
                <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $row2['bm_pic'] . " - " . date("d/m/Y H:i:s", strtotime($row2['bm_result_date'])) . " WIB"; ?></i></p>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>Branch Manager Result *</label>
                <p><?php echo $arrSuplay[$row2['bm_result']]; ?></p>
            </div>
        </div>
    <?php } ?>
</div>
<br>


<div class="row" style="padding-left: 30px">
    <?php if ($row2['om_result'] >= '1') { ?>
        <div class="col-sm-6">
            <label>Operator Manager Summary</label>
            <div class="form-control" style="height:auto">
                <?php echo $row2['om_summary']; ?>
                <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $row2['om_pic'] . " - " . date("d/m/Y H:i:s", strtotime($row2['om_result_date'])) . " WIB"; ?></i></p>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>Operator Manager Result *</label>
                <p><?php echo $arrSuplay[$row2['om_result']]; ?></p>
            </div>
        </div>
    <?php } ?>
</div>
<br>

<div class="row" style="padding-left: 30px">
    <?php if ($row2['mgr_result'] >= '1') { ?>
        <div class="col-sm-6">
            <label>Manager Summary</label>
            <div class="form-control" style="height:auto">
                <?php echo $row2['mgr_summary']; ?>
                <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $row2['mgr_pic'] . " - " . date("d/m/Y H:i:s", strtotime($row2['mgr_result_date'])) . " WIB"; ?></i></p>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>Manager Result *</label>
                <p><?php echo $arrSetuju[$row2['mgr_result']]; ?></p>
            </div>
        </div>
    <?php } ?>
</div>
<br>

<div class="row" style="padding-left: 30px">
    <?php if ($row2['cfo_result'] >= '1') { ?>
        <div class="col-sm-6">
            <label>CFO Summary</label>
            <div class="form-control" style="height:auto">
                <?php echo $row2['cfo_summary']; ?>
                <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $row2['cfo_pic'] . " - " . date("d/m/Y H:i:s", strtotime($row2['cfo_result_date'])) . " WIB"; ?></i></p>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>CFO Result *</label>
                <p><?php echo $arrSetuju[$row2['cfo_result']]; ?></p>
            </div>
        </div>
    <?php } ?>
</div>
<br>

<script>
    $(document).ready(function() {
        $("#customer_date_log").datepicker("option", "disabled", true);
    });
</script>
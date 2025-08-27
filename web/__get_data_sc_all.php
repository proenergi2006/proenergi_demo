<?php

foreach ($id_poc_sc as $id) {
    $role     = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);

    $cek = "select n.*, s.*, p.supply_date, p.nomor_poc, c.kode_pelanggan, c.nama_customer, c.credit_limit, c.tipe_bisnis, c.tipe_bisnis_lain, p.volume_poc, p.harga_poc, e.fullname as marketing, c.jenis_payment, c.top_payment 
            from pro_sales_confirmation n 
            left join pro_sales_confirmation_approval s on n.id = s.id_sales
            join pro_customer c on n.id_customer = c.id_customer 
            join acl_user e on e.id_user = c.id_marketing
            join pro_po_customer p on p.id_poc = n.id_poc 
            where n.id_poc = " . $id;

    $row = $con->getRecord($cek);

    $arrT = array(
        1 => "Agriculture & Forestry / Horticulture", "Business & Information", "Construction/Utilities/Contracting", "Education", "Finance & Insurance",
        "Food & hospitally", "Gaming", "Health Services", "Motor Vehicle", $row['tipe_bisnis_lain'], "Natural Resources / Environmental", "Personal Service", "Manufacture"
    );

    $arrSetuju = array(1 => "Yes", "No");
    $arrSuplay = array(1 => "Supply", "Not Supply");
    $role = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);

    $disabled = "readonly='true'";

    $jns_payment = $row['jenis_payment'];
    $top_payment = $row['top_payment'];
    $arr_payment = array("CREDIT" => "NET " . $top_payment, "COD" => "COD", "CDB" => "CBD");
    $termPayment = $arr_payment[$jns_payment];
?>

    <?php if ($row) { ?>
        <div class="box box-primary">
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
                                            <input autocomplete="off" type="text" name="not_yet" class="form-control input-po text-right hitung rimender" value="<?php echo number_format($row['not_yet']); ?>" <?php echo $disabled; ?> />
                                        </div>
                                    </td>
                                    <td width="60">
                                        <div class="input-group">
                                            <span class="input-group-addon">Rp.</span>
                                            <input autocomplete="off" type="text" name="ov_under_30" class="form-control input-po text-right hitung rimender" value="<?php echo number_format($row['ov_under_30']); ?>" <?php echo $disabled; ?> />
                                        </div>
                                    </td>
                                    <td width="60">
                                        <div class="input-group">
                                            <span class="input-group-addon">Rp.</span>
                                            <input autocomplete="off" type="text" name="ov_under_60" class="form-control input-po text-right hitung rimender" value="<?php echo number_format($row['ov_under_60']); ?>" <?php echo $disabled; ?> />
                                        </div>
                                    </td>
                                    <td width="60">
                                        <div class="input-group">
                                            <span class="input-group-addon">Rp.</span>
                                            <input autocomplete="off" type="text" name="ov_under_90" class="form-control input-po text-right hitung rimender" value="<?php echo number_format($row['ov_under_90']); ?>" <?php echo $disabled; ?> />
                                        </div>
                                    </td>
                                    <td width="60">
                                        <div class="input-group">
                                            <span class="input-group-addon">Rp.</span>
                                            <input autocomplete="off" type="text" name="ov_up_90" class="form-control input-po text-right hitung rimender" value="<?php echo number_format($row['ov_up_90']); ?>" <?php echo $disabled; ?> />
                                        </div>
                                    </td>
                                    <td width="60">
                                        <div class="input-group">
                                            <span class="input-group-addon">Rp.</span>
                                            <input autocomplete="off" type="text" name="reminding" class="form-control input-po text-right hitung" value="<?php echo number_format($row['reminding']); ?>" readonly="" />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <hr style="margin:10px 0px 20px; border-top:4px double #ddd;">
                        <br />
                        <div class="row">

                            <div class="col-sm-6">
                                <label style="padding-left: 30px; padding-bottom: 10px">Customer Type <span style="color:red;">*</span></label>
                                <div class="form-group">
                                    <div class="radio clearfix" style="margin:0px; padding-bottom: 10px">
                                        <label class="col-xs-4 type" style="margin-bottom:5px; float: left; padding-left: 30px">
                                            <div style="position: relative; float: left">
                                                <input <?php echo $disabled; ?> type="radio" name="type_customer" class="type_customer" value="1" <?php echo ($row['type_customer'] == 'Customer Commitment') ? 'checked' : ''; ?> style="position: absolute; opacity: 0;" required="">
                                            </div> Customer Commitment
                                        </label>
                                        <label class="col-xs-6 type" style="margin-bottom:5px; padding-left: 25px">
                                            <div style="position: relative; float: left">
                                                <input <?php echo $disabled; ?> type="radio" name="type_customer" class="type_customer" value="2" <?php echo ($row['type_customer'] == 'Customer Collateral') ? 'checked' : ''; ?> style="position: absolute; opacity: 0;" required="">
                                            </div> Customer Collateral
                                        </label>
                                    </div>

                                    <div class="item1 <?php echo ($row['type_customer'] == 'Customer Collateral') ? 'hidden' : ''; ?>" style="padding-left: 30px;">
                                        <table width="500px">
                                            <tr>
                                                <td width="40%">
                                                    <div>Date
                                                        <input <?php echo $disabled; ?> autocomplete="off" type="text" name="customer_date" id="customer_date" class="form-control input-po" value="<?php echo ($row['customer_date']) ? date('d/m/Y', strtotime($row['customer_date'])) : ''; ?>">
                                                    </div>
                                                </td>
                                                <td style="padding-left: 15px">
                                                    Amount
                                                    <div class="input-group col-sm-12">
                                                        <span class="input-group-addon">Rp.</span>
                                                        <input <?php echo $disabled; ?> autocomplete="off" type="text" name="customer_amount" class="form-control input-po text-right hitung" value="<?php echo number_format($row['customer_amount']); ?>" />
                                                    </div>
                                                </td>
                                            </tr>

                                        </table>
                                    </div>

                                    <div class="item2 <?php echo ($row['type_customer'] == 'Customer Collateral') ? '' : 'hidden'; ?>">
                                        <!-- <div style="padding-top:22px">
                                            <button class="btn btn-action btn-primary addRow " type="button"><i class="fa fa-plus"></i></button>
                                        </div> -->
                                        <div class="copy" style="padding-left: 30px;" id="copy">
                                            <table width="500px">
                                                <tr>
                                                    <td width="40%">
                                                        <div>Date
                                                            <input <?php echo $disabled; ?> autocomplete="off" type="text" name="customer_date_coll[]" id="customer_date2" class="form-control datepicker" value="<?php echo ($row3[0]['date']) ? date('d/m/Y', strtotime($row3[0]['date'])) : ''; ?>">
                                                        </div>
                                                    </td>
                                                    <td style="padding-left: 15px">
                                                        <div>Amount
                                                            <div class="input-group col-sm-12">
                                                                <span class="input-group-addon">Rp.</span>
                                                                <input <?php echo $disabled; ?> autocomplete="off" type="text" name="customer_amount_coll[]" class="form-control input-po text-right hitung" value="<?php echo ($row3[0]['amount']) ? number_format($row3[0]['amount']) : ''; ?>" />
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <?php if (!$disabled) { ?>
                                                        <td style="padding-top:12px; padding-left: 10px" class="btn_action"><button class="btn btn-action btn-primary addRow jarak-kanan" type="button"><i class="fa fa-plus"></i></button></td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        Item <br>
                                                        <input <?php echo $disabled; ?> autocomplete="off" type="text" name="item_coll[]" class="form-control input-po" value="<?php echo ($row3[0]['item']) ? $row3[0]['item'] : ''; ?>" />
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            </table>
                                        </div>


                                        <div class="paste">

                                            <?php if (count($row3) > 1) {
                                                foreach ($row3 as $i => $data) {
                                                    if ($i > 0) {
                                            ?>
                                                        <div class="copy1" style="padding-left: 30px;" id="copy">
                                                            <table width="500px">
                                                                <tr>
                                                                    <td width="40%">
                                                                        <div>Date
                                                                            <input <?php echo $disabled; ?> autocomplete="off" type="text" name="customer_date_coll[]" id="customer_date2" class="form-control datepicker" value="<?php echo ($data['date']) ? date('d/m/Y', strtotime($data['date'])) : ''; ?>">
                                                                        </div>
                                                                    </td>
                                                                    <td style="padding-left: 15px">
                                                                        <div>Amount
                                                                            <div class="input-group col-sm-12">
                                                                                <span class="input-group-addon">Rp.</span>
                                                                                <input <?php echo $disabled; ?> autocomplete="off" type="text" name="customer_amount_coll[]" class="form-control input-po text-right hitung" value="<?php echo ($data['amount']) ? number_format($data['amount']) : ''; ?>" />
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="2">
                                                                        Item <br>
                                                                        <input <?php echo $disabled; ?> autocomplete="off" type="text" name="item_coll[]" class="form-control input-po" value="<?php echo ($data['item']) ? $data['item'] : ''; ?>" />
                                                                    </td>
                                                                    <td></td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                            <?php
                                                    }
                                                }
                                            } ?>

                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="col-sm-6">
                                <label style="padding-left: 30px; padding-bottom: 10px">PO Type <span style="color:red;">*</span></label>
                                <div class="form-group">
                                    <div class="radio clearfix" style="margin:0px; padding-bottom: 10px">
                                        <label class="col-xs-4" style="margin-bottom:5px; float: left; padding-left: 30px">
                                            <div style="position: relative; float: left">
                                                <input <?php echo $disabled; ?> type="radio" name="status_po" value="1" <?php echo ($row['po_status'] == '1') ? 'checked' : ''; ?> style="position: absolute; opacity: 0;" required="">
                                                <ins class="iCheck-helper"></ins>
                                            </div> New PO
                                        </label>
                                        <label class="col-xs-6" style="margin-bottom:5px; padding-left: 25px">
                                            <div style="position: relative; float: left">
                                                <input <?php echo $disabled; ?> type="radio" name="status_po" value="2" <?php echo ($row['po_status'] == '2') ? 'checked' : ''; ?> style="position: absolute; opacity: 0;" required="">
                                                <ins class="iCheck-helper"></ins>
                                            </div> Parcial PO / Contract
                                        </label>
                                    </div>
                                    <div>
                                        <div style="padding-left: 30px;" class="col-sm-6">Volume
                                            <div class="input-group">
                                                <input readonly="" autocomplete="off" type="text" name="volume_po" class="form-control input-po text-right hitung" value="<?php echo number_format($row['volume_poc']); ?>" required="" />
                                                <span class="input-group-addon">Liter</span>
                                            </div>
                                        </div>
                                        <div style="padding-left: 30px;" class="col-sm-6">Amount
                                            <div class="input-group">
                                                <span class="input-group-addon">Rp.</span>
                                                <input readonly="" autocomplete="off" type="text" name="amount_po" value="<?php echo number_format(($row['harga_poc'] * $row['volume_poc']));  ?>" class="form-control input-po text-right hitung" required="" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <br>
                        <div class="row">
                            <div class="col-sm-6">
                                <label style="padding-left: 30px; padding-bottom: 10px">Proposed <span style="color:red;">*</span></label>
                                <div class="form-group">
                                    <div class="radio clearfix" style="margin:0px; padding-bottom: 10px">
                                        <label class="col-xs-4 proposed" style="margin-bottom:5px; float: left; padding-left: 30px">
                                            <div style="position: relative; float: left">
                                                <ins class="iCheck-helper">
                                                    <input type="radio" name="proposed" value="0" <?php echo ($row['proposed_status'] == '0') ? 'checked' : ''; ?> style="position: absolute; opacity: 0;" required="">
                                                </ins>
                                            </div> Not Proposed
                                        </label>
                                        <label class="col-xs-6 proposed" style="margin-bottom:5px; padding-left: 25px">
                                            <div style="position: relative; float: left" class="div_proposed">
                                                <input type="radio" name="proposed" id="proposed" value="1" <?php echo ($row['proposed_status'] == '1') ? 'checked' : ''; ?> style="position: absolute; opacity: 0;" required="">
                                                <ins class="iCheck-helper"></ins>
                                            </div> Proposed
                                        </label>
                                    </div>

                                    <div class="_proposed <?php echo ($row['proposed_status'] == '1') ? '' : 'hidden'; ?>">
                                        <div style="padding-left: 30px;" class="col-sm-6">ADD TOP
                                            <input <?php echo $disabled; ?> autocomplete="off" type="text" name="add_top" value="<?php echo $row['add_top']; ?>" class="form-control input-po text-right hitung" />
                                        </div>
                                        <div style="padding-left: 30px;" class="col-sm-6">ADD CL
                                            <div class="input-group">
                                                <span class="input-group-addon">Rp.</span>
                                                <input <?php echo $disabled; ?> autocomplete="off" type="text" name="add_cl" value="<?php echo ($row['add_cl']) ? $row['add_cl'] : (($row['harga_poc'] * $row['volume_poc']) - $row['reminding']); ?>" class="form-control input-po text-right hitung" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <hr style="margin:0 0 10px" />
    <?php } ?>

<?php } ?>
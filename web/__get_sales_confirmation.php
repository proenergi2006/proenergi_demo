<?php
$disabled = '';
$arrSetuju = array(1 => "Yes", "No");
$arrSuplay = array(1 => "Supply", "Not Supply");
$role = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);

if ($role != 10 && ($row['adm_result'] == 1 || $row['flag_approval'] == 2))
	$disabled = "readonly";

$jns_payment = $row['jenis_payment'];

$top_payment = $row['top_payment'];
$arr_payment = array("CREDIT" => "NET " . $top_payment, "COD" => "COD", "CDB" => "CBD", "CBD" => "CBD");
$termPayment = $arr_payment[$jns_payment];

?>

<style type="text/css">
	.inp-table {
		min-width: 150px;
	}
</style>

<div class="table-responsive">
	<table class="table table-bordered" id="table-grid2">
		<thead>
			<tr>
				<th class="text-center" width="">Customer Code</th>
				<th class="text-center" width="250">Customer Name</th>
				<th class="text-center" width="150">TOP</th>
				<th class="text-center" width="150">Credit Limit</th>
				<th class="text-center" width="200">Business</th>
				<th class="text-center" width="200">Marketing</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="text-center">
					<?php
					if ($row['kode_pelanggan']) {
						echo $row['kode_pelanggan'];
					} else {
						echo '<input type="text" name="kode_pelanggan" class="form-control input-sm input-po text-center" required value="' . $row['kode_pelanggan'] . '" />';
					}
					?>
				</td>
				<td class="text-center"><?php echo $row['nama_customer']; ?></td>
				<td class="text-center"><?php echo $termPayment; ?></td>
				<td class="text-right">
					<?php echo number_format($row['credit_limit']); ?>
					<input type="hidden" id="cl" name="cl" value="<?php echo $row['credit_limit']; ?>" />
				</td>
				<td class="text-center"><?php echo ($row['tipe_bisnis']) ? $arrT[$row['tipe_bisnis']] : ''; ?></td>
				<td class="text-center"><?php echo $row['marketing']; ?></td>
			</tr>
		</tbody>
	</table>
</div>





<div class="table-responsive" style="width: 100%; margin-bottom: 15px; overflow-x: auto; overflow-y: hidden;">
	<table class="table table-bordered" id="table-grid2">
		<thead>
			<tr>
				<th class="text-center" colspan="6">Balance AR</th>
			</tr>
			<tr>
				<th class="text-center" width="190">Not Yet</th>
				<th class="text-center" width="190">Overdue 1-7 Days</th>
				<th class="text-center" width="190">Overdue 8-30 Days</th>
				<th class="text-center" width="190">Overdue 31-60 Days</th>
				<th class="text-center" width="190">Overdue 61-90 Days</th>
				<th class="text-center" width="190">Overdue > 90 Days</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="text-center">
					<div class="form-group from-group-sm" style="margin-bottom:0px;">
						<div class="input-group input-group-sm">
							<span class="input-group-addon">Rp.</span>
							<input type="text" name="not_yet" class="form-control input-po inp-table text-right hitung rimender" value="<?php echo $row['not_yet'] ?? $row12['not_yet']; ?>" <?php echo $disabled; ?> />
						</div>
					</div>
				</td>
				<td class="text-center">
					<div class="form-group from-group-sm" style="margin-bottom:0px;">
						<div class="input-group input-group-sm">
							<span class="input-group-addon">Rp.</span>
							<input type="text" name="ov_up_07" class="form-control input-po inp-table text-right hitung" value="<?php echo $row['ov_up_07'] ?? $row12['ov_up_07']; ?>" <?php echo $disabled; ?> />
						</div>
					</div>
				</td>

				<td class="text-center">

					<div class="form-group from-group-sm" style="margin-bottom:0px;">
						<div class="input-group input-group-sm">
							<span class="input-group-addon">Rp.</span>
							<input type="text" name="ov_under_30" class="form-control input-po inp-table text-right hitung rimender" value="<?php echo $row['ov_under_30'] ?? $row12['ov_under_30']; ?>" <?php echo $disabled; ?> />
						</div>
					</div>
				</td>
				<td class="text-center">
					<div class="form-group from-group-sm" style="margin-bottom:0px;">
						<div class="input-group input-group-sm">
							<span class="input-group-addon">Rp.</span>
							<input type="text" name="ov_under_60" class="form-control input-po inp-table text-right hitung rimender" value="<?php echo $row['ov_under_60'] ?? $row12['ov_under_60']; ?>" <?php echo $disabled; ?> />
						</div>
					</div>
				</td>
				<td class="text-center">


					<div class="form-group from-group-sm" style="margin-bottom:0px;">
						<div class="input-group input-group-sm">
							<span class="input-group-addon">Rp.</span>
							<input type="text" name="ov_under_90" class="form-control input-po inp-table text-right hitung rimender" value="<?php echo $row['ov_under_90'] ?? $row12['ov_under_90']; ?>" <?php echo $disabled; ?> />
						</div>
					</div>
				</td>
				<td class="text-center">

					<div class="form-group from-group-sm">
						<div class="input-group input-group-sm" style="margin-bottom:0px;">
							<span class="input-group-addon">Rp.</span>
							<input type="text" name="ov_up_90" class="form-control input-po inp-table text-right hitung rimender" value="<?php echo $row['ov_up_90'] ?? $row12['ov_up_90']; ?>" <?php echo $disabled; ?> />
						</div>
					</div>
				</td>
			</tr>

			<tr>
				<td class="text-right" colspan="5" style="vertical-align:middle;"><b>Remaining</b></td>
				<td class="text-center">
					<div class="form-group from-group-sm">
						<div class="input-group input-group-sm">
							<span class="input-group-addon">Rp.</span>
							<input type="text" name="reminding" class="form-control input-po text-right hitung" value="<?php echo $row['reminding'] ?? $row12['reminding']; ?>" readonly />
						</div>
					</div>
				</td>
			</tr>

		</tbody>
	</table>
</div>

<hr style="margin:10px 0px 20px; border-top:4px double #ddd;">

<div class="form-horizontal">

	<div class="row">
		<div class="col-md-6">
			<div class="form-group form-group-sm">
				<label class="control-label col-md-4">PO Type *</label>
				<div class="col-md-8">
					<div class="radio">
						<label class="col-md-6" style="padding-left:0px; <?php echo $row['bm_result'] >= '1' ? 'pointer-events: none;' : '' ?>">
							<input type="radio" id="status_po1" name="status_po" value="1" <?php echo ($row['po_status'] == '1') ? 'checked' : ''; ?> required /> New PO
						</label>
						<label class="col-md-6" style="padding-left:0px; <?php echo $row['bm_result'] >= '1' ? 'pointer-events: none;' : '' ?>">
							<input type="radio" id="status_po2" name="status_po" value="2" <?php echo ($row['po_status'] == '2') ? 'checked' : ''; ?> required /> Parcial PO / Contract
						</label>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<div class="form-group form-group-sm">
				<label class="control-label col-md-4">Volume</label>
				<div class="col-md-5">
					<div class="input-group">
						<input type="text" id="volume_po" name="volume_po" class="form-control input-po text-right hitung" value="<?php echo $row['volume_poc']; ?>" required readonly />
						<span class="input-group-addon">Liter</span>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<div class="form-group form-group-sm">
				<label class="control-label col-md-4">Amount</label>
				<div class="col-md-5">
					<div class="input-group">
						<span class="input-group-addon">Rp.</span>
						<input type="text" id="amount_po" name="amount_po" value="<?php echo ($row['harga_poc'] * $row['volume_poc']); ?>" class="form-control input-po text-right hitung" required readonly />
					</div>
				</div>
			</div>
		</div>
	</div>

	<hr style="margin:10px 0px 20px; border-top:4px double #ddd;">

	<div class="row">
		<div class="col-md-6">
			<div class="form-group form-group-sm">
				<label class="control-label col-md-4">Payment Type</label>
				<div class="col-md-8">
					<div class="radio">
						<label class="col-md-6" style="padding-left:0px; <?php echo $row['bm_result'] >= '1' ? 'pointer-events: none;' : '' ?>">
							<input type="radio" name="type_customer" class="type_customer" value="1" <?php echo $disabled; ?> <?php echo ($row['type_customer'] == 'Customer Commitment') ? 'checked' : ''; ?> /> Customer Commitment
						</label>
						<label class="col-md-6" style="padding-left:0px; <?php echo $row['bm_result'] >= '1' ? 'pointer-events: none;' : '' ?>">
							<input type="radio" name="type_customer" class="type_customer" value="2" <?php echo $disabled; ?> <?php echo ($row['type_customer'] == 'Customer Collateral') ? 'checked' : ''; ?> /> Customer Collateral
						</label>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="item1 <?php echo ($row['type_customer'] != 'Customer Commitment') ? 'hidden' : ''; ?>">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group form-group-sm">
					<label class="control-label col-md-4">Date</label>
					<div class="col-md-5">
						<input type="text" name="customer_date" id="customer_date" class="form-control datepicker" required value="<?php echo ($row['customer_date']) ? date('d/m/Y', strtotime($row['customer_date'])) : ''; ?>" <?php echo $row['bm_result'] >= '1' ? 'readonly style="pointer-events: none;"' : '' ?> <?php echo $disabled; ?> />
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="form-group form-group-sm">
					<label class="control-label col-md-4">Amount</label>
					<div class="col-md-5">
						<div class="input-group">
							<span class="input-group-addon">Rp.</span>
							<input type="text" name="customer_amount" id="customer_amount" class="form-control input-po text-right hitung" required value="<?php echo $row['customer_amount']; ?>" <?php echo $row['bm_result'] >= '1' ? 'readonly' : '' ?> <?php echo $disabled; ?> />
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="item2 <?php echo ($row['type_customer'] != 'Customer Collateral') ? 'hidden' : ''; ?>">
		<div class="row">
			<div class="col-md-8">
				<div class="table-responsive">
					<table class="table table-bordered" id="table_collateral">
						<thead>
							<tr>
								<th class="text-center" width="180">Date</th>
								<th class="text-center" width="200">Amount</th>
								<th class="text-center" width="">Item</th>
								<th class="text-center" width="80">Aksi</th>
							</tr>
						</thead>
						<tbody>
							<tr data-id="1">
								<td class="text-center">
									<div class="col-md-12">
										<div class="form-group form-group-sm" style="margin-bottom:0px;">
											<input type="text" name="customer_date_coll[]" id="customer_date_coll1" class="form-control datepicker" value="<?php echo (isset($row3[0])) ? date('d/m/Y', strtotime($row3[0]['date'])) : ''; ?>" <?php echo $disabled; ?> />
										</div>
									</div>
								</td>
								<td class="text-center">
									<div class="col-md-12">
										<div class="form-group form-group-sm" style="margin-bottom:0px;">
											<div class="input-group input-group-sm">
												<span class="input-group-addon">Rp.</span>
												<input type="text" name="customer_amount_coll[]" id="customer_amount_coll1" class="form-control input-po text-right hitung" value="<?php echo (isset($row3[0])) ? $row3[0]['amount'] : ''; ?>" <?php echo $disabled; ?> />
											</div>
										</div>
									</div>
								</td>
								<td class="text-center">
									<div class="col-md-12">
										<div class="form-group form-group-sm" style="margin-bottom:0px;">
											<input type="text" name="item_coll[]" id="item_coll1" class="form-control input-po" value="<?php echo (isset($row3[0])) ? $row3[0]['item'] : ''; ?>" <?php echo $disabled; ?> />
										</div>
									</div>
								</td>
								<td class="text-center">
									<?php
									if (!$disabled) {
										echo '<button type="button" class="btn btn-action btn-primary addRow jarak-kanan"><i class="fa fa-plus"></i></button>';
									}
									?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<hr style="margin:10px 0px 20px; border-top:4px double #ddd;">

	<div class="row">
		<div class="col-md-6">
			<div class="form-group form-group-sm">
				<label class="control-label col-md-4">Schedule Payment</label>
				<div class="col-md-8">
					<div class="radio">
						<label class="col-md-6 proposed" style="padding-left:0px; <?php echo $row['bm_result'] >= '1' ? 'pointer-events: none;' : '' ?>">
							<input type="radio" id="proposed1" name="proposed" value="0" <?php echo ($row['proposed_status'] == '0') ? 'checked' : ''; ?> required /> Not Proposed
						</label>
						<label class="col-md-6 proposed" style="padding-left:0px; <?php echo $row['bm_result'] >= '1' ? 'pointer-events: none;' : '' ?>">
							<input type="radio" id="proposed2" name="proposed" value="1" <?php echo ($row['proposed_status'] == '1') ? 'checked' : ''; ?> required /> Proposed
						</label>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="_proposed <?php echo ($row['proposed_status'] == '1') ? '' : 'hidden'; ?>">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group form-group-sm">
					<label class="control-label col-md-4">ADD TOP</label>
					<div class="col-md-5">
						<input type="text" name="add_top" id="add_top" class="form-control input-po text-right hitung" value="<?php echo $row['add_top']; ?>" <?php echo $disabled; ?> <?php echo $row['bm_result'] >= '1' ? 'readonly' : ''; ?> />
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="form-group form-group-sm">
					<label class="control-label col-md-4">ADD CL</label>
					<div class="col-md-5">
						<div class="input-group">
							<span class="input-group-addon">Rp.</span>
							<input type="text" name="add_cl" id="add_cl" class="form-control input-po text-right hitung" value="<?php echo $row['add_cl']; ?>" <?php echo $disabled; ?> required <?php echo $row['bm_result'] >= '1' ? 'readonly' : ''; ?> />
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php if ($role == 10) { ?>
			<div class="form-group row">
				<div class="col-md-8">
					<?php
					$pathPt = $public_base_directory . '/files/uploaded_user/lampiran/unblock/' . $row['lampiran_unblock'];
					$lampPt = $row['lampiran_unblock_ori'];
					$lamp 	= $row['lampiran_unblock'] ? $row['lampiran_unblock'] : '';
					if ($lamp && file_exists($pathPt)) {
						$linkPt 	= ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=unblock&ktg=" . $lamp . "&file=" . $lampPt);
						$label01 	= 'File Persetujuan Unblock';
						$attr01 	= '';
						$filenya01 	= '<p style="margin:15px 0px;"><a href="' . $linkPt . '" target="_blank"><i class="fa fa-file-alt jarak-kanan"></i>' . $lampPt . '</a></p>';
					} else {
						$label01 	= 'File Persetujuan Unblock';
						$attr01 	= 'required';
						$filenya01 	= '';
					}
					?>
					<div class="form-group form-group-sm">
						<label class="control-label col-md-3">File Persetujuan Unblock</label>
						<div class="col-md-9">
							<input type="file" id="attachment_unblock" name="attachment_unblock" class="form-control" <?php echo $attr01; ?> />
							<?php echo $filenya01; ?>
						</div>
					</div>
				</div>
			</div>
		<?php } else { ?>
			<div class="form-group row">
				<div class="col-md-8">
					<?php
					$pathPt = $public_base_directory . '/files/uploaded_user/lampiran/unblock/' . $row['lampiran_unblock'];
					$lampPt = $row['lampiran_unblock_ori'];
					$lamp 	= $row['lampiran_unblock'] ? $row['lampiran_unblock'] : '';
					if ($lamp && file_exists($pathPt)) {
						$linkPt 	= ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=unblock&ktg=" . $lamp . "&file=" . $lampPt);
						$label01 	= 'File Persetujuan Unblock';
						$attr01 	= '';
						$filenya01 	= '<p style="margin:0px;"><a href="' . $linkPt . '" target="_blank"><i class="fa fa-file-alt jarak-kanan"></i>' . $lampPt . '</a></p>';
					} else {
						$label01 	= 'File Persetujuan Unblock';
						$attr01 	= 'required';
						$filenya01 	= '';
					}
					?>
					<div class="form-group form-group-sm">
						<label class="control-label col-md-3">File Persetujuan Unblock</label>
						<label class="control-label col-md-9"><?php echo $filenya01; ?></label>
					</div>
				</div>
			</div>
		<?php } ?>


	</div>

</div>

<hr style="margin:10px 0px 20px; border-top:4px double #ddd;">

<div class="form-horizontal">
	<?php
	$sesrole = $role;
	if ($sesrole == 10) {
		if (!$row['adm_result']) {
			echo '
			<div class="row">
				<div class="col-md-6">
					<div class="form-group form-group-sm">
						<label class="control-label col-md-12">Catatan Admin Finance</label>
						<div class="col-md-12">
							<textarea name="admin_summary" id="admin_summary" class="form-control" style="height:90px;" required></textarea>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group form-group-sm">
						<label class="control-label col-md-12">Persetujuan</label>
						<div class="col-md-12">
							<div class="radio">
								<label class="rtl"><input type="radio" name="approval" id="approval1" value="1" required /> Ya</label>
							</div>
							<div class="radio">
								<label class="rtl"><input type="radio" name="approval" id="approval2" value="2" required /> Tidak</label>
							</div>
						</div>
					</div>
				</div>
			</div>';
		} else {
			echo '
			<div class="row">
				<div class="col-md-6">
					<div class="form-group form-group-sm">
						<label class="control-label col-md-12">Catatan Admin Finance</label>
						<div class="col-md-12">
							<div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
								' . nl2br($row['adm_summary']) . '
								<p style="margin:10px 0 0; font-size:12px;">
									<i>' . ($row['adm_pic'] ? $row['adm_pic'] . ' - ' : '&nbsp;') .
				($row['adm_result_date'] ? date("d/m/Y H:i:s", strtotime($row['adm_result_date'])) . ' WIB' : '') . '</i>
								</p>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group form-group-sm">
						<label class="control-label col-md-12">Persetujuan Admin Finance</label>
						<div class="col-md-12">
							<div class="form-control" style="height:auto; font-size:12px;">' . $arrSuplay[$row['adm_result']] . '</div>
						</div>
					</div>
				</div>
			</div>
			<p>&nbsp;</p>';
		}

		if ($row['bm_result']) {
			echo '
			<div class="row">
				<div class="col-md-6">
					<div class="form-group form-group-sm">
						<label class="control-label col-md-12">Catatan Branch Manager</label>
						<div class="col-md-12">
							<div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
								' . nl2br($row['bm_summary']) . '
								<p style="margin:10px 0 0; font-size:12px;">
									<i>' . ($row['bm_pic'] ? $row['bm_pic'] . ' - ' : '&nbsp;') .
				($row['bm_result_date'] ? date("d/m/Y H:i:s", strtotime($row['bm_result_date'])) . ' WIB' : '') . '</i>
								</p>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group form-group-sm">
						<label class="control-label col-md-12">Persetujuan Branch Manager</label>
						<div class="col-md-12">
							<div class="form-control" style="height:auto; font-size:12px;">' . $arrSuplay[$row['bm_result']] . '</div>
						</div>
					</div>
				</div>
			</div>
			<p>&nbsp;</p>';
		}
	}

	/* BUAT APPROVAL OM */
	if ($sesrole == 7) {
		if ($row['adm_result']) {
			echo '
			<div class="row">
				<div class="col-md-6">
					<div class="form-group form-group-sm">
						<label class="control-label col-md-12">Catatan Admin Finance</label>
						<div class="col-md-12">
							<div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
								' . nl2br($row['adm_summary']) . '
								<p style="margin:10px 0 0; font-size:12px;">
									<i>' . ($row['adm_pic'] ? $row['adm_pic'] . ' - ' : '&nbsp;') .
				($row['adm_result_date'] ? date("d/m/Y H:i:s", strtotime($row['adm_result_date'])) . ' WIB' : '') . '</i>
								</p>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group form-group-sm">
						<label class="control-label col-md-12">Persetujuan Admin Finance</label>
						<div class="col-md-12">
							<div class="form-control" style="height:auto; font-size:12px;">' . $arrSuplay[$row['adm_result']] . '</div>
						</div>
					</div>
				</div>
			</div>
			<p>&nbsp;</p>';
		}

		if (!$row['bm_result']) {
			echo '
			<div class="row">
				<div class="col-md-6">
					<div class="form-group form-group-sm">
						<label class="control-label col-md-12">Catatan Branch Manager</label>
						<div class="col-md-12">
							<textarea name="bm_summary" id="bm_summary" class="form-control" style="height:90px;" required></textarea>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group form-group-sm">
						<label class="control-label col-md-12">Persetujuan</label>
						<div class="col-md-12">
							<div class="radio">
								<label class="rtl"><input type="radio" name="approval" id="approval1" value="1" required /> Ya</label>
							</div>
							<div class="radio">
								<label class="rtl"><input type="radio" name="approval" id="approval2" value="2" required /> Tidak</label>
							</div>
						</div>
					</div>
				</div>
			</div>';
		} else {
			echo '
			<div class="row">
				<div class="col-md-6">
					<div class="form-group form-group-sm">
						<label class="control-label col-md-12">Catatan Branch Manager</label>
						<div class="col-md-12">
							<div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
								' . nl2br($row['bm_summary']) . '
								<p style="margin:10px 0 0; font-size:12px;">
									<i>' . ($row['bm_pic'] ? $row['bm_pic'] . ' - ' : '&nbsp;') .
				($row['bm_result_date'] ? date("d/m/Y H:i:s", strtotime($row['bm_result_date'])) . ' WIB' : '') . '</i>
								</p>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group form-group-sm">
						<label class="control-label col-md-12">Persetujuan Branch Manager</label>
						<div class="col-md-12">
							<div class="form-control" style="height:auto; font-size:12px;">' . $arrSuplay[$row['bm_result']] . '</div>
						</div>
					</div>
				</div>
			</div>
			<p>&nbsp;</p>';
		}
	}
	?>
</div>

<hr style="margin:15px 0px; border-top:4px double #ddd;" />

<div style="margin-bottom:0px;">
	<input type="hidden" name="id" value="<?php echo paramEncrypt($id); ?>" />
	<input type="hidden" name="idc" value="<?php echo paramEncrypt($idc); ?>" />
	<input type="hidden" name="id_poc" value="<?php echo paramEncrypt($id_poc); ?>" />
	<?php
	$simpan = false;
	$status_disabled = "";
	$tgl_sekarang = strtotime(date("Y-m-d H:i:s"));
	$wilayah = paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]);
	$query = "SELECT * FROM pro_button_control WHERE button = 'SC'";
	$row_button = $con->getRecord($query);
	if ($wilayah == '4' || $wilayah == '7') {
		// Samarinda, Banjarmasin, zona WITA +1 dari WIB
		$waktu_sekarang = date("H:i:s", strtotime("+1 hour"));
		// $waktu_sekarang = date("H:i:s");
		$waktu_tutup = date("16:01:00");
		$zona_waktu = "WITA";
		$waktu_buka = date("06:59:00");
		$tgl_buka = date("Y-m-d 06:59:00", strtotime("+15 hour", $tgl_sekarang));
	} else {
		$waktu_sekarang = date("H:i:s");
		$waktu_tutup = date("15:01:00");
		$zona_waktu = "WIB";
		$waktu_buka = date("06:59:00");
		$tgl_buka = date("Y-m-d 06:59:00", strtotime("+16 hour", $tgl_sekarang));
	}

	if ($waktu_sekarang >= $waktu_buka && $waktu_sekarang <= $waktu_tutup) {
		$status_disabled = "";
	} elseif ($waktu_sekarang >= $waktu_tutup || $waktu_sekarang < $waktu_buka) {
		$status_disabled = "disabled";
	}

	if ($role == 7 && $row['bm_result'] == 0) $simpan = true;
	else if ($role == 10 && $row['adm_result'] == 0) $simpan = true;
	?>
	<!-- BUKA SC PAKE BUTTON -->
	<?php if ($row_button['status'] == 1) : ?>
		<?php if ($simpan === true) : ?>
			<?php if ($status_disabled == "disabled") : ?>
				<span style="color: red;"><b>SC sudah melewati jam 16:00:00, Akan dibuka kembali pada : <?= $tgl_buka ?></b>
				</span>
			<?php endif ?>
			<br>
			<button type="submit" name="btnSbmt" id="btnSbmt" class="btn btn-primary jarak-kanan " style="min-width:90px; " <?= $status_disabled ?>><i class="fa fa-save jarak-kanan"></i> Simpan</button>
		<?php endif ?>
	<?php else : ?>
		<?php if ($simpan === true) : ?>
			<button type="submit" name="btnSbmt" id="btnSbmt" class="btn btn-primary jarak-kanan " style="min-width:90px; "><i class="fa fa-save jarak-kanan"></i> Simpan</button>
		<?php endif ?>
	<?php endif ?>

	<!-- <?php if ($simpan === true) : ?>
		<?php if ($status_disabled == "disabled") : ?>
			<span style="color: red;"><b>SC sudah ditutup, Akan dibuka kembali pada : <?= $tgl_buka . ' ' . $zona_waktu ?></b>
			</span>
		<?php endif ?>
		<br>
		<button type="submit" name="btnSbmt" id="btnSbmt" class="btn btn-primary jarak-kanan " style="min-width:90px;" <?= $status_disabled ?>><i class="fa fa-save jarak-kanan"></i> Simpan</button>
	<?php endif ?> -->

	<a class="btn btn-default" style="min-width:90px;" href="<?php echo BASE_URL_CLIENT . "/pro_sales_confirmation.php"; ?>">
		<i class="fa fa-reply jarak-kanan"></i> Batal</a>
</div>

<?php /*<?php if($row['flag_approval'] > 0){ ?>
<?php
$_hidden = '';
if ($role==10 && $row['flag_approval']==2)
$_hidden = 'style="display: none;"';
?>
<a class="btn btn-success jarak-kanan" <?php echo $_hidden; ?> href="<?php echo ACTION_CLIENT."/sc_cetak.php?".paramEncrypt('id='.$id); ?>" target="_blank">Print</a> 
<?php } ?> 
*/ ?>

<script>
	$(document).ready(function() {

		var objSettingDate = {
			dateFormat: 'dd/mm/yy',
			changeMonth: true,
			changeYear: true,
			yearRange: "c-80:c+10",
			dayNamesMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
			monthNamesShort: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
		};
		hitung_arnya();

		$(".hitung").number(true, 0, ".", ",");

		$('.datepicker').change(function() {
			var id = $(this).attr('id');
			var val = $(this).val();
			var sub = val.split("/");

			if ((id.indexOf('customer_date') < 0)) {
				$(this).val(sub[1] + '/' + sub[0] + '/' + sub[2]);
			}
		});

		<?php if ($disabled) { ?>
			$(':radio:not(:checked)').attr('disabled', true);
			$(':radio[name=approval]:not(:checked)').attr('disabled', false);
			$("#customer_date").datepicker("option", "disabled", true);
		<?php } else { ?>
		<?php } ?>

		$("input[name='type_customer']").on('ifChecked', function(e) {
			let nilai = $(this).val();
			if (nilai == 1) {
				$('.item1').removeClass('hidden');
				$('.item2').addClass('hidden');
				$('#customer_date').attr('required', true)
				$('#customer_amount').attr('required', true)
			} else if (nilai == 2) {
				$('.item2').removeClass('hidden');
				$('.item1').addClass('hidden');
				$('#customer_date').attr('required', false)
				$('#customer_amount').attr('required', false)
			}
		});

		$("input[name='proposed']").on('ifChecked', function(e) {
			let nilai = $(this).val();
			if (nilai == 0) {
				$('._proposed').addClass('hidden');
			} else if (nilai == 1) {
				$('._proposed').removeClass('hidden');
			}
		});

		$('.rimender').on('keyup change blur', hitung_arnya);

		function hitung_arnya() {
			var creditlimit = parseFloat($('input[name="cl"]').val().replace(",", "")) || 0;
			var not_yet = parseFloat($('input[name="not_yet"]').val().replace(",", "")) || 0;
			var ov_up_07 = parseFloat($('input[name="ov_up_07"]').val().replace(",", "")) || 0;
			var ov_under_30 = parseFloat($('input[name="ov_under_30"]').val().replace(",", "")) || 0;
			var ov_under_60 = parseFloat($('input[name="ov_under_60"]').val().replace(",", "")) || 0;
			var ov_under_90 = parseFloat($('input[name="ov_under_90"]').val().replace(",", "")) || 0;
			var ov_up_90 = parseFloat($('input[name="ov_up_90"]').val().replace(",", "")) || 0;
			var amount_po = parseFloat($('input[name="amount_po"]').val().replace(",", "")) || 0;

			var nilai_ar = not_yet + ov_up_07 + ov_under_30 + ov_under_60 + ov_under_90 + ov_up_90;
			var nilai_cl = (creditlimit ? creditlimit - nilai_ar : 0);
			var nilai_sl = (amount_po > nilai_cl ? (amount_po - nilai_cl) : 0);
			var unblock = false;

			$("input[name='reminding']").val(nilai_cl);

			if (amount_po > nilai_cl) unblock = true;
			if (ov_up_07 > 0 || ov_under_30 > 0 || ov_under_60 > 0 || ov_under_90 > 0 || ov_up_90 > 0) unblock = true;

			if (unblock) {
				$("#proposed2").iCheck('check');
				$("input[name='add_cl']").val(nilai_sl);
				$('._proposed').removeClass('hidden');
			} else {
				$("#proposed1").iCheck('check');
				$("input[name='add_cl']").val('');
				$('._proposed').addClass('hidden');
			}
		}


		var maxRows = 5; // Jumlah maksimum baris yang diizinkan

		$("#table_collateral").on("click", ".addRow", function() {
			var tabel = $("#table_collateral");
			var arrId = tabel.find("tbody > tr").map(function() {
				return parseFloat($(this).data("id")) || 0;
			}).toArray();
			var rwNom = Math.max.apply(Math, arrId);
			var newId = (rwNom == 0) ? 1 : (rwNom + 1);

			if (tabel.find("tbody > tr").length >= maxRows) {
				alert("Anda tidak dapat menambahkan lebih dari " + maxRows + " baris.");
				return;
			}


			var isiHtml =
				'<tr data-id="' + newId + '">' +
				'<td class="text-center">' +
				'<div class="col-md-12"><div class="form-group form-group-sm" style="margin-bottom:0px;">' +
				'<input type="text" name="customer_date_coll[]" id="customer_date_coll' + newId + '" class="form-control datepicker" value="" />' +
				'</div></div>' +
				'</td>' +
				'<td class="text-center">' +
				'<div class="col-md-12"><div class="form-group form-group-sm" style="margin-bottom:0px;">' +
				'<div class="input-group input-group-sm">' +
				'<span class="input-group-addon">Rp.</span>' +
				'<input type="text" name="customer_amount_coll[]" id="customer_amount_coll' + newId + '" class="form-control input-po text-right" value="" />' +
				'</div>' +
				'</div></div>' +
				'</td>' +
				'<td class="text-center">' +
				'<div class="col-md-12"><div class="form-group form-group-sm" style="margin-bottom:0px;">' +
				'<input type="text" name="item_coll[]" id="item_coll' + newId + '" class="form-control input-po" value="" />' +
				'</div></div>' +
				'</td>' +
				'<td class="text-center">' +
				'<button type="button" class="btn btn-action btn-danger hRow jarak-kanan"><i class="fa fa-times"></i></button>' +
				'</td>' +
				'</tr>';
			if (rwNom == 0) {
				tabel.find('tbody').html(isiHtml);
			} else {
				tabel.find('tbody > tr:last').after(isiHtml);
			}

			$("#customer_date_coll" + newId).datepicker(objSettingDate);
			$("#customer_amount_coll" + newId).number(true, 0, ".", ",");
		}).on("click", ".hRow", function() {
			var tabel = $("#table_collateral");
			var jTbl = tabel.find('tbody > tr').length;
			var cRow = $(this).closest('tr');
			cRow.remove();
		});

	});
</script>
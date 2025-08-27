<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$enk  	= decode($_SERVER['REQUEST_URI']);
$con 	= new Connection();
$flash	= new FlashAlerts;

$idr 		= isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
$sesuser 	= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);
$seswil 	= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$sesgroup 	= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);

if ($idr != "") {
	$sql = "
			select a.*, b.nama_customer as nm_customer, c.nama_cabang 
			from pro_invoice_admin a 
			join pro_customer b on a.id_customer = b.id_customer 
			join pro_master_cabang c on b.id_wilayah = c.id_master 
			where 1=1 and a.id_invoice = '" . $idr . "'
		";
	$model 	= $con->getRecord($sql);
	$action = "bayar";
} else {
	$model 		= array();
	$action 	= "add";
}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("myGrid", "formatNumber", "jqueryUI"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory . "/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
		<aside class="right-side">
			<section class="content-header">
				<h1>Pembayaran Invoice Customer</h1>
			</section>
			<section class="content">

				<?php $flash->display(); ?>
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
					</div>
					<div class="box-body">
						<form action="<?php echo ACTION_CLIENT . '/invoice_customer.php'; ?>" id="gform" name="gform" method="post" class="form-validasi form-horizontal" role="form">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">Nama Customer *</label>
										<div class="col-md-8">
											<?php if ($action == 'add') { ?>
												<div class="input-group">
													<input type="hidden" id="id_customer" name="id_customer" value="<?php echo $model['id_customer']; ?>" />
													<input type="text" id="nm_customer" name="nm_customer" class="form-control" value="<?php echo $model['nm_customer']; ?>" required readonly />
													<span class="input-group-btn">
														<button type="button" class="btn btn-sm btn-primary picker-user"><i class="fa fa-search"></i></button>
													</span>
												</div>
											<?php } else { ?>
												<input type="hidden" id="id_customer" name="id_customer" value="<?php echo $model['id_customer']; ?>" />
												<input type="text" id="nm_customer" name="nm_customer" class="form-control" value="<?php echo $model['nm_customer']; ?>" required readonly />
											<?php } ?>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">No Invoice *</label>
										<div class="col-md-8">
											<input type="text" id="no_invoice" name="no_invoice" class="form-control" value="<?php echo $model['no_invoice']; ?>" required readonly />
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">Tgl Invoice *</label>
										<div class="col-md-4">
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
												<?php $currval = ($model['tgl_invoice'] ? date("d/m/Y", strtotime($model['tgl_invoice'])) : ''); ?>
												<input type="text" id="tgl_invoice" name="tgl_invoice" class="form-control" value="<?php echo $currval; ?>" required readonly />
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">Total Tagihan *</label>
										<div class="col-md-6">
											<div class="input-group">
												<span class="input-group-addon">Rp.</span>
												<?php $currval = ($model['total_invoice'] ? number_format($model['total_invoice']) : ''); ?>
												<input type="text" id="total_invoice" name="total_invoice" class="form-control text-right" value="<?php echo $currval; ?>" required readonly />
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-8">
									<div class="table-responsive">
										<table class="table table-bordered table-dasar">
											<thead>
												<tr>
													<th class="text-center" width="80">No</th>
													<th class="text-center" width="180">Tanggal Bayar</th>
													<th class="text-center" width="">Jumlah Bayar</th>
													<th class="text-center" width="100">Aksi</th>
												</tr>
											</thead>
											<tbody>
												<?php
												$sql02 = "select a.* from pro_invoice_admin_detail_bayar a where 1=1 and a.id_invoice = '" . $idr . "' order by id_invoice_bayar";
												$listData1 	= $con->getResult($sql02);

												$arrPengeluaran = (count($listData1) > 0) ? $listData1 : array(array(""));
												if (count($arrPengeluaran) > 0) {
													$no_urut = 0;
													$total_bayar = 0;
													foreach ($arrPengeluaran as $data1) {
														$no_urut++;
														$tgl_bayar 		= ($data1['tgl_bayar']) ? date('d/m/Y', strtotime($data1['tgl_bayar'])) : '';
														$jml_bayar 		= ($data1['jumlah_bayar']) ? number_format($data1['jumlah_bayar']) : '';
														$total_bayar 	= $total_bayar + $data1['jumlah_bayar'];

														echo '
                                                    <tr data-id="' . $no_urut . '">
                                                        <td class="text-center"><span class="frmnodasar" data-row-count="' . $no_urut . '">' . $no_urut . '</span></td>
                                                        <td class="text-left">
                                                            <input type="text" id="tgl_bayar' . $no_urut . '" name="tgl_bayar[]" class="form-control input-sm datepicker" required value="' . $tgl_bayar . '" data-rule-dateNL="true" autocomplete="off"/>
                                                        </td>
                                                        <td class="text-left">
                                                            <input type="text" id="jml_bayar' . $no_urut . '" name="jml_bayar[]" class="form-control input-sm text-right harganya" required value="' . $jml_bayar . '" autocomplete="off"/>
                                                        </td>
                                                        <td class="text-center">
                                                            <a class="btn btn-action btn-primary addRow jarak-kanan">&nbsp;<i class="fa fa-plus"></i>&nbsp;</a> 
                                                            <a class="btn btn-action btn-danger hRow">&nbsp;<i class="fa fa-times"></i>&nbsp;</a>
                                                        </td>
                                                    </tr>';
													}
												} else {
													echo '<tr><td class="text-left" colspan="4">Tidak Ada Data</td></tr>';
												}
												?>
												<tr>
													<td class="text-center" colspan="2"><b>T O T A L</b></td>
													<td class="text-left">
														<input type="text" id="total_bayar" name="total_bayar" class="form-control input-sm text-right hitung" value="<?php echo $total_bayar; ?>" readonly />
													</td>
													<td class="text-center">
														<input type="checkbox" id="lunas" name="lunas" value="1">
														<label for="lunas"> LUNAS</label><br>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>

							<div class="row hide" id="row-bukti-potongan">
								<div class="col-md-8">
									<strong>
										<small>Sistem mendeteksi bahwa jumlah pembayaran lebih kecil dari jumlah tagihan dan di anggap sudah LUNAS.</small>
										<br>
										<small>
											Silahkan isi bukti potongan dibawah ini.
										</small>
									</strong>
									<div class="table-responsive">
										<table class="table table-bordered table-kategori-potongan">
											<thead>
												<tr>
													<th class="text-center" width="260">Kategori Potongan</th>
													<th class="text-center" width="">Jumlah Potongan</th>
													<th class="text-center" width="100">Aksi</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td class="text-left">
														<select class="form-control select2 kategori_potongan" name="kategori_potongan[]">
															<option value=""></option>
															<option value="Biaya Admin">Biaya Admin</option>
															<option value="PPH 23">PPH 23</option>
															<option value="Perbedaan Koma Accurate">Perbedaan Koma Accurate</option>
															<option value="Wapu">Wapu</option>
															<option value="HSD Losses">HSD Losses</option>
														</select>
													</td>
													<td class="text-left">
														<input type="text" name="jml_bayar_potongan[]" class="form-control input-sm text-right harganya-potongan" autocomplete="off" />
													</td>
													<td class="text-center">
														<a class="btn btn-action btn-primary addRowPot jarak-kanan">&nbsp;<i class="fa fa-plus"></i>&nbsp;</a>
														<a class="btn btn-action btn-danger hRowPot">&nbsp;<i class="fa fa-times"></i>&nbsp;</a>
													</td>
												</tr>
												<tr>
													<td class="text-center"><b>T O T A L</b></td>
													<td class="text-left">
														<input type="text" id="total_bayar_potongan" name="total_bayar_potongan" class="form-control input-sm text-right hitung" value="0" readonly />
													</td>
													<td class="text-center">
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>


							<hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

							<div style="margin-bottom:15px;">
								<input type="hidden" name="act" value="<?php echo $action; ?>" />
								<input type="hidden" name="idr" value="<?php echo $idr; ?>" />
								<button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">
									<i class="fa fa-save jarak-kanan"></i> Simpan</button>
								<a href="<?php echo BASE_URL_CLIENT . '/invoice_customer.php'; ?>" class="btn btn-default" style="min-width:90px;">
									<i class="fa fa-reply jarak-kanan"></i> Kembali</a>
							</div>

							<p style="margin:0px;"><small>* Wajib Diisi</small></p>
						</form>
					</div>
				</div>

				<?php $con->close(); ?>
			</section>
			<?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
		</aside>
	</div>

	<div class="modal fade" id="loading_modal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-blue">
					<h4 class="modal-title">Loading Data ...</h4>
				</div>
				<div class="modal-body text-center modal-loading"></div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="user_modal" role="dialog" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog" style="width:1000px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">List Customer</h4>
				</div>
				<div class="modal-body"></div>
			</div>
		</div>
	</div>

	<style type="text/css">
		#table-grid3 {
			margin-bottom: 15px;
		}

		#table-grid3 td,
		#table-grid3 th {
			font-size: 11px;
			font-family: arial;
		}
	</style>
	<script>
		$(document).ready(function() {

			$(".harganya, .hitung, .harganya-potongan, #total_invoice").number(true, 0, ".", ",");

			var objSettingDate = {
				dateFormat: 'dd/mm/yy',
				changeMonth: true,
				changeYear: true,
				yearRange: "c-80:c+10",
				dayNamesMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
				monthNamesShort: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
			};

			var formValidasiCfg = {
				submitHandler: function(form) {
					if ($("#cekkolnup").is(":checked") && $("#nup_fee").val() == "") {
						$("#loading_modal").modal("hide");
						$.validator.showErrorField('nup_fee', "Kolom ini belum diisi atau dipilih");
						setErrorFocus($("#nup_fee"), $("form#gform"), false);
					} else {
						Swal.fire({
							title: "Anda yakin simpan?",
							showDenyButton: true,
							confirmButtonText: "YA",
							denyButtonText: "Tidak"
						}).then((result) => {
							var total_tagihan = parseFloat($("#total_invoice").val()) || 0;
							var total_bayar = parseFloat($("#total_bayar").val()) || 0;
							var total_bayar_potongan = parseFloat($("#total_bayar_potongan").val()) || 0;
							// $("#loading_modal").modal({
							// 	keyboard: false,
							// 	backdrop: 'static'
							// });
							/* Read more about isConfirmed, isDenied below */
							if (result.isConfirmed) {
								if ((total_bayar + total_bayar_potongan) < total_tagihan) {
									var selisih = total_tagihan - (total_bayar + total_bayar_potongan);
									if (total_bayar_potongan === 0) {
										Swal.fire({
											title: "Total bayar masih kurang " + new Intl.NumberFormat("ja-JP").format(selisih) + " dari total tagihan, anda yakin lanjut?",
											showDenyButton: true,
											confirmButtonText: "YA",
											denyButtonText: "Batal"
										}).then((result) => {
											/* Read more about isConfirmed, isDenied below */
											if (result.isConfirmed) {
												form.submit();
											} else if (result.isDenied) {
												Swal.fire("Data batal disimpan", "", "info");
											}
										});
									} else {
										Swal.fire({
											icon: "error",
											title: "Oops...",
											text: "Total bayar masih kurang " + new Intl.NumberFormat("ja-JP").format(selisih) + " dari total tagihan"
										});
									}
								} else {
									form.submit();
								}
							}
						});
					}
				}
			};
			$("form#gform").validate($.extend(true, {}, config.validation, formValidasiCfg));

			$(".picker-user").on("click", function(e) {
				$("#loading_modal").modal({
					keyboard: false,
					backdrop: 'static'
				});
				$.post(base_url + "/web/invoice_customer_picker.php", {
					prm: $(this).data("param")
				}, function(data) {
					$("#user_modal").find(".modal-body").html(data);
					$("#user_modal").modal({
						backdrop: "static"
					});
				});
			});

			$("#user_modal").on('show.bs.modal', function(e) {
				$("#loading_modal").modal({
					keyboard: false,
					backdrop: 'static'
				});
			}).on('shown.bs.modal', function(e) {
				$("#loading_modal").modal("hide");
			}).on('click', '#idBataluser_modal', function() {
				$("#user_modal").modal("hide");
			});

			$(".table-dasar").on("click", "a.addRow", function() {
				var tabel = $(".table-dasar");
				var arrId = tabel.find("tbody > tr").map(function() {
					return parseFloat($(this).data("id")) || 0;
				}).toArray();
				var rwNom = Math.max.apply(Math, arrId);
				var newId = (rwNom == 0) ? 1 : (rwNom + 1);

				var isiHtml =
					'<tr data-id="' + newId + '">' +
					'<td class="text-center"><span class="frmnodasar" data-row-count="' + newId + '"></span></td>' +
					'<td class="text-left">' +
					'<input type="text" id="tgl_bayar' + newId + '" name="tgl_bayar[]" class="form-control input-sm" required data-rule-dateNL="true" autocomplete="off" />' +
					'</td>' +
					'<td class="text-left">' +
					'<input type="text" id="jml_bayar' + newId + '" name="jml_bayar[]" class="form-control input-sm text-right harganya" required autocomplete="off"/>' +
					'</td>' +
					'<td class="text-center">' +
					'<a class="btn btn-action btn-primary addRow jarak-kanan">&nbsp;<i class="fa fa-plus"></i>&nbsp;</a> ' +
					'<a class="btn btn-action btn-danger hRow">&nbsp;<i class="fa fa-times"></i>&nbsp;</a>' +
					'</td>' +
					'</tr>';
				if (rwNom == 0) {
					tabel.find('tbody').html(isiHtml);
				} else {
					$(this).closest('tr').after(isiHtml);
				}
				$("#tgl_bayar" + newId).datepicker(objSettingDate);
				$("#jml_bayar" + newId).number(true, 0, ".", ",");
				tabel.find("span.frmnodasar").each(function(i, v) {
					$(v).text(i + 1);
				});
			}).on("click", ".hRow", function() {
				var tabel = $(".table-dasar");
				var jTbl = tabel.find('tbody > tr').length;
				if (jTbl > 2) {
					var cRow = $(this).closest('tr');
					cRow.remove();
					tabel.find("span.frmnodasar").each(function(i, v) {
						$(v).text(i + 1);
					});
					calculate_volterima();
				}
				$('#lunas').iCheck('uncheck'); // Uncheck the checkbox
			}).on("keyup", ".harganya", function() {
				$('#lunas').iCheck('uncheck'); // Uncheck the checkbox
				calculate_volterima();
			});

			function calculate_volterima() {
				let grandTotal = 0;
				const total_tagihan = Number($("#total_invoice").val()) || 0;

				$(".harganya").each(function() {
					const value = Number($(this).val()) || 0; // Caching the jQuery object
					grandTotal += value;
				});

				const isPaid = grandTotal === total_tagihan;
				$('#lunas').prop("checked", isPaid);
				$('.icheckbox_square-blue').toggleClass("checked", isPaid);

				$('#total_bayar').val(grandTotal);
				//$('#vol_total_cek').val(grandTotal);
				//$("#vol_total_cek").number(true, 0, ".", ",");
			}

			// Function to add a new row
			$('.table-kategori-potongan').on('click', '.addRowPot', function() {
				var newRow = `
				<tr>
					<td class="text-left">
						<select class="form-control select2 kategori_potongan" name="kategori_potongan[]">
							<option value=""></option>
							<option value="Biaya Admin">Biaya Admin</option>
							<option value="PPH 23">PPH 23</option>
							<option value="Perbedaan Koma Accurate">Perbedaan Koma Accurate</option>
							<option value="Wapu">Wapu</option>
						</select>
					</td>
					<td class="text-left">
						<input type="text" name="jml_bayar_potongan[]" class="form-control input-sm text-right harganya-potongan" autocomplete="off"/>
					</td>
					<td class="text-center">
						<a class="btn btn-action btn-danger hRowPot">&nbsp;<i class="fa fa-times"></i>&nbsp;</a>
					</td>
				</tr>`;
				// Insert new row before the total row
				$('table.table-kategori-potongan tbody tr:last-child').before(newRow);
				$('.select2').select2({
					placeholder: "Pilih salah satu",
				});
				$(".harganya-potongan").number(true, 0, ".", ",");
			}).on("keyup blur", ".harganya-potongan", function() {
				calculate_potongan();
			});

			// Function to remove a row
			$(document).on('click', '.hRowPot', function() {
				var row = $(this).closest('tr');
				var rowCount = $('table.table-kategori-potongan tbody tr').length;

				// Check if the row is the first row (not the total row)
				if (rowCount > 2) {
					row.remove();
				}
				calculate_potongan();
			});

			function calculate_potongan() {
				let grandTotal = 0;
				$(".harganya-potongan").each(function(i, v) {
					grandTotal = grandTotal + ($(v).val() * 1);
				});
				$('#total_bayar_potongan').val(grandTotal);
				//$('#vol_total_cek').val(grandTotal);
				//$("#vol_total_cek").number(true, 0, ".", ",");
			}
			// Initialize iCheck for the lunas checkbox
			// $("#lunas").iCheck({
			// 	checkboxClass: 'icheckbox_square-blue',
			// });

			// Handling the 'check' event
			$("#lunas").on("ifChecked", function() {
				var total_tagihan = parseFloat($("#total_invoice").val()) || 0;
				var total_bayar = parseFloat($("#total_bayar").val()) || 0;

				// Check if total_bayar is 0
				if (total_bayar === 0) {
					Swal.fire({
						icon: "warning",
						title: "Oops...",
						text: "Total bayar masih 0"
					}).then(function() {
						$('#lunas').iCheck('uncheck'); // Uncheck the checkbox
					});;
				} else {
					// If total bayar is less than total tagihan, show the relevant row
					if (total_bayar < total_tagihan) {
						$("#row-bukti-potongan").removeClass("hide");
						$(".kategori_potongan").attr("required", true);
						$(".harganya-potongan").attr("required", true);
					}
				}
			}).on("ifUnchecked", function() {
				// Hide the relevant row when unchecked
				$("#row-bukti-potongan").addClass("hide");
				$(".kategori_potongan").val("").trigger("change");
				$(".harganya-potongan").val("");
			});

		});
	</script>
</body>

</html>
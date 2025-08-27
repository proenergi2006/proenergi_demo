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
$arrBln = array(1 => "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
$linkEx = BASE_URL_CLIENT . '/report/m-po-customer-exp.php';
$sesUser = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);
$sesRole = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$sesGrup = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);
$sesCbng = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

// Cek peran pengguna
$required_role = ['1', '2', '7', '21', '4', '3', '11', '17', '18', '6', '5'];
// Misalnya halaman ini hanya untuk superadmin
if (!in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), $required_role)) {
	// Pengguna tidak memiliki peran yang tepat, redirect ke halaman lain atau tampilkan pesan akses ditolak
	$flash->add("warning", "Akses ditolak.", BASE_URL_CLIENT . "/home.php");
	// exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("formatNumber", "jqueryUI", "myGrid"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory . "/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
		<aside class="right-side">
			<section class="content-header">
				<h1>Laporan PO Customer</h1>
			</section>
			<section class="content">

				<?php $flash->display(); ?>
				<div class="row">
					<div class="col-sm-12">
						<div class="box box-info">
							<div class="box-header with-border">
								<div class="row">
									<div class="col-sm-6">
										<div style="font-size:16px;"><b>PENCARIAN</b></div>
									</div>
									<div class="col-sm-6">
										<div class="text-right">
											<a href="<?php echo $linkEx; ?>" class="btn btn-success btn-sm" target="_blank" id="expData">Export Data</a>
										</div>
									</div>
								</div>
							</div>
							<div class="box-body">
								<form name="sFrm" id="sFrm" method="post">
									<div class="table-responsive">
										<table border="0" cellpadding="0" cellspacing="0" class="table no-border col-sm-top table-pencarian" style="margin-bottom:0px;">
											<tr>
												<td width="130">Periode PO</td>
												<td width="38%">
													<input type="text" name="q1" id="q1" class="datepicker input-cr-sm" value="<?php echo $q1; ?>" autocomplete="off" /> s/d
													<input type="text" name="q2" id="q2" class="datepicker input-cr-sm" value="<?php echo $q2; ?>" autocomplete="off" />
												</td>
												<td width="130">Customer</td>
												<td width="38%"><input type="text" name="q3" id="q3" class="input-cr-lg" value="<?php echo $q3; ?>" /></td>
											</tr>
											<?php
											if (in_array($sesRole, array("5", "6", "7", "16"))) {
												if ($sesRole == "6") {
													$agm = " and ((id_role = 11 and id_group = '" . $sesGrup . "') or (id_role = 17 and id_om = '" . $sesUser . "'))";
													$agc = " and id_master != 1 and id_group_cabang = '" . $sesGrup . "'";
												} else if ($sesRole == "7") {
													$agm = " and ((id_role = 11 and id_wilayah = '" . $sesCbng . "') or id_role = 17)";
													$agc = "";
												} else {
													$agm = " and id_role in(11,17)";
													$agc = " and id_master != 1";
												}
											?>
												<tr>
													<td>Marketing</td>
													<td>
														<div style="width:300px; float:left; margin-right:10px;">
															<select name="q5" id="q5" class="select2">
																<option></option>
																<?php $con->fill_select("id_user", "fullname", "acl_user", $q5, "where is_active=1" . $agm, "fullname", false); ?>
															</select>
														</div>
													</td>
													<?php
													// if(in_array($sesRole, array("5", "6", "16"))){ 
													if (!in_array($sesRole, array("7"))) {
													?>
														<td>Cabang Invoice</td>
														<td>
															<div style="width:300px; float:left; margin-right:10px;">
																<select name="q4" id="q4" class="select2">
																	<option></option>
																	<?php $con->fill_select("id_master", "nama_cabang", "pro_master_cabang", $q4, "where is_active=1" . $agc, "", false); ?>
																</select>
															</div>
														</td>
													<?php } else echo '<td colspan="2">&nbsp;</td>'; ?>
												</tr>
											<?php } ?>
											<?php if (in_array($sesRole, array("1", "2", "3", "4", "6"))) { ?>
												<tr>
													<td>Area</td>
													<td>
														<select name="q6" id="q6" class="select2">
															<option></option>
															<?php $con->fill_select("id_gu", "group_wilayah", "pro_master_group_cabang", "", "", "group_wilayah", false); ?>
														</select>
													</td>
												</tr>
											<?php } ?>
											<tr>
												<td colspan="4">
													<button type="submit" class="btn btn-info btn-sm" name="btnSc" id="btnSc"><i class="fa fa-search jarak-kanan"></i>Search</button>
												</td>
											</tr>
										</table>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-12">
						<div class="box box-info">
							<div class="box-header with-border">
								<div class="row">
									<div class="col-sm-12">
										<div class="text-right" style="margin-top: 10px">Show
											<select name="tableGridLength" id="tableGridLength">
												<option value="10" selected>10</option>
												<option value="25">25</option>
												<option value="50">50</option>
												<option value="100">100</option>
												<option value="all">All</option>
											</select> Data
										</div>
									</div>
								</div>
							</div>
							<div class="box-body table-responsive">
								<table class="table table-bordered col-sm-top table-isi" id="table-grid">
									<thead>
										<tr>
											<th class="text-center" width="10%">Periode PO</th>
											<th class="text-center" width="23%">Customer</th>
											<th class="text-center" width="22%">No. PO</th>
											<th class="text-center" width="15%">Volume (Liter)</th>
											<th class="text-center" width="15%">Realisasi (Liter)</th>
											<th class="text-center" width="15%">Pending (Liter)</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>


				<?php $con->close(); ?>
			</section>
			<?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
		</aside>
	</div>

	<style type="text/css">
		.table-isi>thead>tr>th,
		.table-isi>tbody>tr>td {
			font-size: 11px;
			font-family: arial;
		}

		.table-pencarian>tbody>tr>td {
			padding: 5px;
			font-size: 11px;
			font-family: arial;
			vertical-align: top;
		}

		select.input-cr,
		input.input-cr-sm,
		input.input-cr-lg,
		input.input-cr {
			padding: 3px 5px;
			border: 1px solid #ccc;
			font-family: arial;
			font-size: 11px;
			height: 26px;
			line-height: 26px;
		}

		input.input-cr-sm {
			width: 100px;
		}

		input.input-cr-lg {
			width: 300px;
		}

		.btn-sm,
		.btn-group-sm>.btn {
			font-size: 11px;
		}

		.select2-container .select2-selection--single {
			height: 26px;
		}

		.select2-container--default .select2-selection--single .select2-selection__rendered {
			line-height: 26px;
		}

		.select2-container--default .select2-selection--single .select2-selection__clear {
			height: 26px;
		}

		.select2-search--dropdown .select2-search__field {
			font-family: arial;
			font-size: 11px;
			padding: 4px 3px;
		}

		.select2-results__option {
			font-family: arial;
			font-size: 11px;
		}
	</style>
	<script>
		$(document).ready(function() {
			$(".hitung").number(true, 0, ".", ",");
			$("#table-grid").ajaxGrid({
				url: "./m-po-customer-data.php",
				data: {
					q1: $("#q1").val(),
					q2: $("#q2").val(),
					q3: $("#q3").val(),
					q4: $("#q4").val(),
					q5: $("#q5").val(),
					q6: $("#q6").val()
				},
			});
			$('#btnSc').on('click', function() {
				$("#table-grid").ajaxGrid("draw", {
					data: {
						q1: $("#q1").val(),
						q2: $("#q2").val(),
						q3: $("#q3").val(),
						q4: $("#q4").val(),
						q5: $("#q5").val(),
						q6: $("#q6").val()
					}
				});
				return false;
			});
			$('#tableGridLength').on('change', function() {
				$("#table-grid").ajaxGrid("pageLen", $(this).val());
			});
			$('#expData').on('click', function() {
				$(this).prop("href", $("#uriExp").val());
			});

			/*	$("select#q1").select2({placeholder:"Vendor/Suplier", allowClear:true });
				$("select#q2").select2({placeholder:"Produk", allowClear:true });
				$("select#q3").select2({placeholder:"Area", allowClear:true });
				$("select#q4").select2({placeholder:"Terminal/Depot", allowClear:true });
				$("select#q5").select2({placeholder:"Bulan", allowClear:true });
				$("select#q6").select2({placeholder:"Tahun", allowClear:true });
			*/
		});
	</script>
</body>

</html>
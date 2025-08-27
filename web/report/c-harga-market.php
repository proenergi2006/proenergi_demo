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
$linkEx = BASE_URL_CLIENT . '/report/c-harga-market-exp.php';

// Cek peran pengguna
$required_role = ['1', '2', '21', '4', '3', '11', '5'];
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
				<h1>Laporan Harga Market</h1>
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
												<td width="130">Periode</td>
												<td width="38%">
													<input type="text" name="q1" id="q1" class="datepicker input-cr-sm" value="<?php echo $q1; ?>" autocomplete="off" /> s/d
													<input type="text" name="q2" id="q2" class="datepicker input-cr-sm" value="<?php echo $q2; ?>" autocomplete="off" />
												</td>
												<td width="130">Customer</td>
												<td width="38%"><input type="text" name="q3" id="q3" class="input-cr-lg" value="<?php echo $q3; ?>" /></td>
											</tr>
											<tr>
												<td>Volume Delivery</td>
												<td>
													<div style="width:100px; float:left; margin-right:10px;">
														<select name="q4" id="q4" class="select2">
															<option></option>
															<option value="1" <?php echo ($q4 == 1 ? ' selected' : ''); ?>>Sama dengan</option>
															<option value="2" <?php echo ($q4 == 2 ? ' selected' : ''); ?>>Lebih besar</option>
															<option value="3" <?php echo ($q4 == 3 ? ' selected' : ''); ?>>Lebih kecil</option>
														</select>
													</div>
													<input type="text" name="q5" id="q5" class="input-cr hitung" value="<?php echo $q5; ?>" />
												</td>
												<td>Wilayah Kirim</td>
												<td>
													<div style="width:300px; float:left; margin-right:10px;">
														<select name="q6" id="q6" class="select2">
															<option></option>
															<?php $con->fill_select("id_kab", "nama_kab", "pro_master_kabupaten", $q6, "", "id_kab", false); ?>
														</select>
													</div>
												</td>
											</tr>
											<tr>
												<td>Area</td>
												<td>
													<div style="width:300px; float:left; margin-right:10px;">
														<select name="q9" id="q9" class="select2">
															<option value="">Semua Area</option>
															<?php $con->fill_select("id_master", "nama_area", "pro_master_area", $q9, "where is_active=1", "nama_area", false); ?>
														</select>
													</div>
												</td>
												<td>Cabang Invoice</td>
												<td>
													<div style="width:300px; float:left; margin-right:10px;">
														<select name="q7" id="q7" class="select2">
															<option value="">Semua Cabang</option>
															<?php $con->fill_select("id_master", "nama_cabang", "pro_master_cabang", $q7, "where is_active=1 and id_master != 1", "", false); ?>
														</select>
													</div>
												</td>
											</tr>
											<tr>
												<td>Marketing</td>
												<td>
													<div style="width:300px; float:left; margin-right:10px;">
														<select name="q8" id="q8" class="select2">
															<option></option>
															<?php $con->fill_select("id_user", "fullname", "acl_user", $q8, "where is_active=1 and id_role = 11", "fullname", false); ?>
														</select>
													</div>
												</td>
												<td colspan="2">&nbsp;</td>
											</tr>
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
											<th class="text-center" width="8%">Periode Delivery</th>
											<th class="text-center" width="20%">Customer</th>
											<th class="text-center" width="11%">Cabang Invoice</th>
											<th class="text-center" width="11%">Area</th>
											<th class="text-center" width="16%">Wilayah Kirim</th>
											<th class="text-center" width="10%">Volume Terkirim (Liter)</th>
											<th class="text-center" width="8%">Harga Dasar Pertamina</th>
											<th class="text-center" width="8%">Harga Jual (Dasar)</th>
											<th class="text-center" width="8%">Harga Jual (Inc. Tax)</th>
											<th class="text-center" width="6%">Ongkos Angkut</th>
											<th class="text-center" width="8%">% Harga Jual vs Harga Dasar Pertamina</th>
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
				url: "./c-harga-market-data.php",
				data: {
					q1: $("#q1").val(),
					q2: $("#q2").val(),
					q3: $("#q3").val(),
					q4: $("#q4").val(),
					q5: $("#q5").val(),
					q6: $("#q6").val(),
					q7: $("#q7").val(),
					q8: $("#q8").val(),
					q9: $("#q9").val()
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
						q6: $("#q6").val(),
						q7: $("#q7").val(),
						q8: $("#q8").val(),
						q9: $("#q9").val()
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
				
			*/
			$("select#q7").select2();
			$("select#q9").select2();
		});
	</script>
</body>

</html>
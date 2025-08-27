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
$sesgr	= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);

$linkEx1 = BASE_URL_CLIENT . '/report/bpuj-exp.php';
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("myGrid", "jqueryUI"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory . "/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
		<aside class="right-side">
			<section class="content-header">
				<h1>Rekapitulasi Laporan Pemberian Uang Jalan</h1>
			</section>
			<section class="content">

				<?php $flash->display(); ?>
				<div class="alert alert-danger alert-dismissible" style="display:none">
					<div class="box-tools">
						<button data-alert="remove" class="btn btn-box-tool close" type="button"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<form name="searchForm" id="searchForm" role="form" class="form-horizontal">
					<div class="form-group row">
						<div class="col-sm-3">
							<input type="text" class="form-control input-sm" placeholder="Keywords" name="q1" id="q1" />
						</div>
						<div class="col-sm-3">
							<div class="input-group">
								<span class="input-group-addon">Periode</span>
								<input type="text" name="q2" id="q2" class="form-control input-sm validate[required,custom[date]] datepicker" autocomplete='off' />
							</div>
						</div>
						<div class="col-sm-3">
							<div class="input-group">
								<span class="input-group-addon">S/D</span>
								<input type="text" name="q3" id="q3" class="form-control input-sm validate[required,custom[date]] datepicker" autocomplete='off' />
							</div>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-3">
							<select name="q4" id="q4" class="form-control">
								<option value="">Pilih Status BPUJ</option>
								<option value="1">Menunggu Verifikasi</option>
								<option value="2">Approved</option>
							</select>
						</div>
						<div class="col-sm-3">
							<select name="q5" id="q5" class="form-control">
								<option value="">Pilih Status Realisasi</option>
								<option value="NULL">Belum Realisasi</option>
								<option value="0">Menunggu Approval</option>
								<option value="1">Approved</option>
							</select>
						</div>
						<div class="col-sm-1 col-sm-top">
							<button type="submit" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch"><i class="fa fa-search jarak-kanan"></i> Search</button>
						</div>
						<div class="col-sm-1 col-sm-top">
							<a href="<?php echo $linkEx1; ?>" class="btn btn-success btn-sm" target="_blank" id="expData1">Export Data</a>
						</div>
					</div>
				</form>
				<div class="row">
					<div class="col-sm-12">
						<div class="box box-info">
							<div class="box-header with-border">
								<div class="row">
									<div class="col-sm-6">
									</div>
									<div class="col-sm-6">
										<div class="text-right" style="margin-top: 10px">Show
											<select name="tableGridLength" id="tableGridLength">
												<option value="10">10</option>
												<option value="25" selected>25</option>
												<option value="50">50</option>
												<option value="100">100</option>
											</select> Data
										</div>
									</div>
								</div>
							</div>
							<div class="box-body table-responsive">
								<table class="table table-bordered" id="table-grid">
									<thead>
										<tr>
											<th class="text-center" width="60">No</th>
											<th class="text-center" width="200">Tanggal Kirim BPUJ</th>
											<th class="text-center" width="150">Nomor BPUJ</th>
											<th class="text-center" width="150">Nomor DN</th>
											<th class="text-center" width="150">Kode DS</th>
											<th class="text-center" width="200">Total BPUJ (Rp)</th>
											<th class="text-center" width="300">Yang dibayarkan (Rp)</th>
											<th class="text-center" width="250">Realisasi (Rp)</th>
											<th class="text-center" width="250">Status BPUJ</th>
											<th class="text-center" width="250">Status Realisasi</th>
											<th class="text-center" width="600">Aksi</th>
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

	<script>
		$(document).ready(function() {
			$("select#q2").select2({
				placeholder: "Status",
				allowClear: true
			});
			$("#table-grid").ajaxGrid({
				url: "./datatable/list_bpuj_log.php",
				data: {
					q1: $("#q1").val(),
					q2: $("#q2").val(),
					q3: $("#q3").val(),
					q4: $("#q4").val(),
					q5: $("#q5").val()
				},
			});
			$('#btnSearch').on('click', function() {
				$("#table-grid").ajaxGrid("draw", {
					data: {
						q1: $("#q1").val(),
						q2: $("#q2").val(),
						q3: $("#q3").val(),
						q4: $("#q4").val(),
						q5: $("#q5").val()
					}
				});
				return false;
			});
			$('#tableGridLength').on('change', function() {
				$("#table-grid").ajaxGrid("pageLen", $(this).val());
			});

			$('#expData1').on('click', function() {
				$(this).prop("href", $("#uriExp").val());
			});
		});
	</script>
</body>

</html>
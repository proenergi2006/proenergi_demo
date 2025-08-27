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
$linkEx = BASE_URL_CLIENT . '/report/master-ongkos-angkut-exp.php';

// Cek peran pengguna
$required_role = ['1', '2', '21', '4', '3', '9', '11', '16', '5'];
// Misalnya halaman ini hanya untuk superadmin
if (!in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), $required_role)) {
	// Pengguna tidak memiliki peran yang tepat, redirect ke halaman lain atau tampilkan pesan akses ditolak
	$flash->add("warning", "Akses ditolak.", BASE_URL_CLIENT . "/home.php");
	// exit();
}
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
				<h1>Ongkos Angkut</h1>
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
							<input type="text" name="q1" id="q1" class="form-control" placeholder="Tujuan" />
						</div>
						<div class="col-sm-3 col-sm-top">
							<select name="q2" id="q2" class="form-control select2">
								<option></option>
								<?php $con->fill_select("id_master", "concat(nama_suplier,' - ',nama_transportir,', ',lokasi_suplier)", "pro_master_transportir", "", "where is_active=1 and tipe_angkutan in(1,3)", "nama_suplier", false); ?>
							</select>
						</div>
						<div class="col-sm-3">
							<select id="q3" name="q3" class="form-control validate[required] select2">
								<option></option>
								<?php $con->fill_select("id_prov", "nama_prov", "pro_master_provinsi", '', "", "nama_prov", false); ?>
							</select>
						</div>
						<div class="col-sm-3">
							<select id="q4" name="q4" class="form-control validate[required] select2">

							</select>
						</div>

					</div>
					<div class="form-group row">
						<div class="col-sm-4 col-sm-top">
							<input type="hidden" name="q5" id="q5" value="<?php echo $enk['is_report'] ?? null; ?>" />
							<button type="submit" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch"><i class="fa fa-search jarak-kanan"></i>Search</button>
						</div>
					</div>
				</form>

				<div class="row">
					<div class="col-sm-12">
						<div class="box box-info">
							<div class="box-header with-border">
								<div class="row">
									<div class="col-sm-6">
										<?php if (!isset($enk['is_report'])) { ?>
											<a href="<?php echo BASE_URL_CLIENT . '/add-master-ongkos-angkut.php'; ?>" class="btn btn-primary jarak-kanan">
												<i class="fa fa-plus jarak-kanan"></i>Add Data</a>
										<?php } ?>
										<a href="<?php echo $linkEx; ?>" class="btn btn-success" target="_blank" id="expData">Export Data</a>
									</div>
									<div class="col-sm-6">
										<div class="text-right" style="margin-top: 10px">Show
											<select name="tableGridLength" id="tableGridLength">
												<option value="10" selected>10</option>
												<option value="25">25</option>
												<option value="50">50</option>
												<option value="100">100</option>
											</select> Data
										</div>
									</div>
								</div>
							</div>
							<div class="box-body table-responsive">
								<table class="table table-bordered table-hover" id="table-grid">
									<thead>
										<tr>
											<th class="text-center" width="8%">NO</th>
											<th class="text-center" width="10%">TRANSPORTIR</th>
											<th class="text-center" width="22%">TUJUAN</th>
											<?php
											$cek = "select * from pro_master_volume_angkut where is_active = 1";
											$row = $con->getResult($cek);
											if (count($row) > 0) {
												foreach ($row as $res) {
													echo '<th class="text-center">' . ($res['volume_angkut'] / 1000) . ' KL</th>';
												}
											}
											echo (!isset($enk['is_report'])) ? '<th class="text-center" width="8%">AKSI</th>' : '';
											?>
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

	<style>
		#table-grid td,
		#table-grid th {
			font-size: 12px;
		}
	</style>
	<script>
		$(document).ready(function() {
			$('#expData').on('click', function() {
				$(this).prop("href", $("#uriExp").val());
			});

			$("select#q3").select2({
				placeholder: "Provinsi",
				allowClear: true
			});
			$("select#q4").select2({
				placeholder: "Kabupaten",
				allowClear: true
			});

			$("#table-grid").ajaxGrid({
				url: "./datatable/master-ongkos-angkut.php",
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
			$('#table-grid tbody').on('click', '[data-action="deleteGrid"]', function(e) {
				e.preventDefault();
				if (confirm("Apakah anda yakin ?")) {
					var param = $(this).data("param-idx");
					var handler = function(data) {
						if (data.error == "") {
							$(".alert").slideUp();
							$("#table-grid").ajaxGrid("draw");
						} else {
							$(".alert").slideUp();
							var a = $(".alert > .box-tools");
							a.next().remove();
							a.after("<p>" + data.error + "</p>");
							$(".alert").slideDown();
						}
					};
					$.post("./datatable/deleteTable.php", {
						param: param
					}, handler, "json");
				}
			});


			$("select#q3").change(function() {
				$("select#q4").val("").trigger('change').select2('close');
				$("select#q4 option").remove();
				$.ajax({
					type: "POST",
					url: "./__get_kabupaten.php",
					dataType: 'json',
					data: {
						q1: $("select#q3").val()
					},
					cache: false,
					success: function(data) {
						if (data.items != "") {
							$("select#q4").select2({
								data: data.items,
								placeholder: "Pilih Kabupaten",
								allowClear: true,
							});
							return false;
						}
					}
				});
			});


		});
	</script>
</body>

</html>
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

// Cek peran pengguna
$required_role = ['1', '2', '7', '21', '4', '3', '15', '11', '16', '6', '17', '18'];
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
				<h1>Location Customer Review</h1>
			</section>
			<section class="content">

				<?php $flash->display(); ?>
				<form name="searchForm" id="searchForm" role="form" class="form-horizontal">
					<div class="form-group row">
						<div class="col-sm-4">
							<input type="text" class="form-control input-sm" name="q1" id="q1" placeholder="Keywords..." />
						</div>
						<div class="col-sm-4 col-sm-top">
							<select id="q2" name="q2" class="form-control">
								<option></option>
								<option value="1">Disetujui</option>
								<option value="2">Ditolak</option>
							</select>
						</div>
						<div class="col-sm-4 col-sm-top">
							<select id="q5" name="q5" class="form-control select2">
								<option></option>
								<?php $con->fill_select("id_master", "nama_cabang", "pro_master_cabang", '', "where is_active=1 and id_master <> 1", "nama_cabang", false); ?>
							</select>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-4">
							<select id="q3" name="q3" class="form-control validate[required] select2">
								<option></option>
								<?php $con->fill_select("id_prov", "nama_prov", "pro_master_provinsi", '', "", "nama_prov", false); ?>
							</select>
						</div>
						<div class="col-sm-4 col-sm-top">
							<select id="q4" name="q4" class="form-control validate[required] select2">
							</select>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-3">
							<div class="input-group">
								<span class="input-group-addon">Periode</span>
								<input type="text" name="q6" id="q6" class="form-control input-sm validate[required,custom[date]] datepicker" autocomplete='off' />
							</div>
						</div>
						<div class="col-sm-3 col-sm-top">
							<div class="input-group">
								<span class="input-group-addon">S/D</span>
								<input type="text" name="q7" id="q7" class="form-control input-sm validate[required,custom[date]] datepicker" autocomplete='off' />
							</div>
						</div>
						<div class="col-sm-6 col-sm-top">
							<button type="submit" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch"><i class="fa fa-search jarak-kanan"></i> Search</button>
						</div>
					</div>
					<p style="font-size:12px;"><i>* Keywords berdasarkan nama dan kode customer</i></p>
				</form>

				<div class="row">
					<div class="col-sm-12">
						<div class="box box-info">
							<div class="box-header with-border">
								<div class="row">
									<div class="col-sm-6">
										<?php if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 11 || paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 17 || paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 18) { ?>
											<a href="<?php echo BASE_URL_CLIENT . '/lcr-add.php'; ?>" class="btn btn-primary">
												<i class="fa fa-plus jarak-kanan"></i>Add Data
											</a>
										<?php } ?>
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
								<table class="table table-bordered table-hover" id="data-lcr-table">
									<thead>
										<tr>
											<th class="text-center" width="7%">No</th>
											<th class="text-center" width="8%">Kode LCR</th>
											<th class="text-center" width="15%">Customer</th>
											<th class="text-center" width="15%">Surveyor</th>
											<th class="text-center" width="20%">Lokasi</th>
											<th class="text-center" width="12%">Keterangan</th>
											<th class="text-center" width="12%">Status</th>
											<th class="text-center" width="4%"><i class="fa fa-paperclip"></i></th>
											<th class="text-center" width="7%">Aksi</th>
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
		#data-lcr-table td,
		#data-lcr-table th {
			font-size: 12px;
		}
	</style>
	<script>
		$(document).ready(function() {
			$("select#q2").select2({
				placeholder: "Persetujuan",
				allowClear: true
			});
			$("select#q3").select2({
				placeholder: "Provinsi",
				allowClear: true
			});
			$("select#q4").select2({
				placeholder: "Kabupaten",
				allowClear: true
			});
			$("select#q5").select2({
				placeholder: "Cabang",
				allowClear: true
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


			$("#data-lcr-table").ajaxGrid({
				url: "./datatable/lcr.php",
				data: {
					q1: $("#q1").val(),
					q2: $("#q2").val(),
					q3: $("#q3").val(),
					q4: $("#q4").val(),
					q5: $("#q5").val(),
					q6: $("#q6").val(),
					q7: $("#q7").val()
				},
			});
			$('#btnSearch').on('click', function() {
				var param = {
					q1: $("#q1").val(),
					q2: $("#q2").val(),
					q3: $("#q3").val(),
					q4: $("#q4").val(),
					q5: $("#q5").val(),
					q6: $("#q6").val(),
					q7: $("#q7").val()
				};
				$("#data-lcr-table").ajaxGrid("draw", {
					data: param
				});
				return false;
			});
			$('#tableGridLength').on('change', function() {
				$("#data-lcr-table").ajaxGrid("pageLen", $(this).val());
			});
			$('#data-lcr-table').on('click', '[data-action="deleteGrid"]', function(e) {
				e.preventDefault();
				if (confirm("Apakah anda yakin ?")) {
					var param = $(this).data("param-idx");
					var handler = function(data) {
						if (data.error == "") {
							$(".alert").slideUp();
							$("#data-lcr-table").ajaxGrid("draw");
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
		});
	</script>
</body>

</html>
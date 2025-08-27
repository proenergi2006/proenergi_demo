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
$required_role = ['1', '21', '4', '7', '3', '15', '9', '16', '6', '10'];
// Misalnya halaman ini hanya untuk superadmin
if (!in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), $required_role)) {
	// Pengguna tidak memiliki peran yang tepat, redirect ke halaman lain atau tampilkan pesan akses ditolak
	$flash->add("warning", "Akses ditolak.", BASE_URL_CLIENT . "/home.php");
	// exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("myGrid"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory . "/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
		<aside class="right-side">
			<section class="content-header">
				<h1>Verifikasi Data Customer</h1>
			</section>
			<section class="content">

				<?php $flash->display(); ?>
				<div class="alert alert-danger alert-dismissible" style="display:none">
					<div class="box-tools">
						<button data-alert="remove" class="btn btn-box-tool close" type="button"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<form name="searchForm" id="searchForm" role="form" class="form-horizontal">
					<div class="form-group">
						<div class="col-sm-6 col-md-4">
							<div class="input-group">
								<input type="text" class="form-control input-sm" name="q1" id="q1" placeholder="Keywords..." />
								<div class="input-group-btn">
									<button type="submit" class="btn btn-sm btn-info" name="btnSearch" id="btnSearch"><i class="fa fa-search"></i></button>
								</div>
							</div>
						</div>
					</div>
				</form>

				<div class="row">
					<div class="col-sm-12">
						<div class="box box-info">
							<div class="box-header with-border">
								<div class="row">
									<div class="col-sm-12">
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
											<th class="text-center" width="80">No</th>
											<th class="text-center" width="120">Kode Verifikasi</th>
											<th class="text-center" width="150">Kode Pelanggan</th>
											<th class="text-center" width="280">Nama Customer</th>
											<th class="text-center" width="">Alamat</th>
											<th class="text-center" width="200">Status</th>
											<th class="text-center" width="60">Aksi</th>
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
		.table>thead>tr>th,
		.table>tbody>tr>td {
			font-size: 12px;
		}
	</style>
	<script>
		$(document).ready(function() {
			$("#table-grid").ajaxGrid({
				url: "./datatable/verifikasi-data-customer.php",
				data: {
					q1: $("#q1").val()
				},
			});
			$('#btnSearch').on('click', function() {
				$("#table-grid").ajaxGrid("draw", {
					data: {
						q1: $("#q1").val()
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

		});
	</script>
</body>

</html>
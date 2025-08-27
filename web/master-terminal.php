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
$required_role = ['1', '2', '21', '3', '5'];
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
				<h1>Master Terminal</h1>
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
						<div class="col-sm-4">
							<input type="text" class="form-control input-sm" name="q1" id="q1" placeholder="Keywords..." />
						</div>
						<div class="col-sm-2 col-sm-top">
							<select id="q2" name="q2" class="form-control select2">
								<option value="1" selected>Active</option>
								<option value="0">Not Active</option>
								<option value="2">All</option>
							</select>
						</div>
						<div class="col-sm-4">
							<select name="q3" id="q3" class="select2">
								<option value="">Semua Cabang</option>
								<?php $con->fill_select("id_master", "nama_cabang", "pro_master_cabang", $q3, "where is_active=1", "", false); ?>
							</select>
						</div>
						<div class="col-sm-2 col-sm-top">
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
										<a href="<?php echo BASE_URL_CLIENT . '/add-master-terminal.php'; ?>" class="btn btn-primary">
											<i class="fa fa-plus jarak-kanan"></i>Add Data
										</a>
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
								<table class="table table-bordered table-hover" id="table-grid">
									<thead>
										<tr>
											<th class="text-center" width="80">NO</th>
											<th class="text-center" width="230">TERMINAL</th>
											<th class="text-center" width="150">TANKI</th>
											<th class="text-center" width="100">CABANG</th>
											<th class="text-center" width="">LOKASI</th>
											<th class="text-center" width="130">KATEGORI</th>
											<th class="text-center" width="150">BATAS ATAS TANKI</th>
											<th class="text-center" width="150">BATAS BAWAH TANKI</th>
											<th class="text-center" width="100">STATUS</th>
											<th class="text-center" width="130">AKSI</th>
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
		.table>thead>tr>th,
		.table>tbody>tr>td {
			font-size: 12px;
		}
	</style>
	<script>
		$(document).ready(function() {
			$("#table-grid").ajaxGrid({
				url: "./datatable/master-terminal.php",
				data: {
					q1: $("#q1").val(),
					q2: $("#q2").val(),
					q3: $("#q3").val()
				},
			});
			$('#btnSearch').on('click', function() {
				$("#table-grid").ajaxGrid("draw", {
					data: {
						q1: $("#q1").val(),
						q2: $("#q2").val(),
						q3: $("#q3").val()
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
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
				<h1>Purchase Order Truck</h1>
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
							<input type="text" class="form-control input-sm" placeholder="Keywords" name="q1" id="q1" />
						</div>
						<div class="col-sm-4 col-md-3 col-sm-top">
							<select id="q2" name="q2" class="form-control">
								<option></option>
								<option value="1">Terdaftar</option>
								<option value="2">Verifikasi Transportir</option>
								<option value="3">Konfirmasi Logistik</option>
								<option value="4">Terverifikasi</option>
								<option value="5">Tagihan Diterima</option>
							</select>
						</div>
						<div class="col-sm-4 col-md-5 col-sm-top">
							<button type="submit" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch"><i class="fa fa-search jarak-kanan"></i> Search</button>
						</div>
					</div>
				</form>

				<div class="row">
					<div class="col-sm-12">
						<div class="box box-info">
							<div class="box-header with-border">
								<div class="row">
									<div class="col-sm-6">
										<a href="<?php echo BASE_URL_CLIENT . '/purchase-order-add.php'; ?>" class="btn btn-primary">
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
								<table class="table table-bordered table-hover1" id="table-grid">
									<thead>
										<tr>
											<th class="text-center" width="50">No</th>
											<th class="text-center" width="120">Tanggal PO</th>
											<th class="text-center" width="200">Kode PO</th>
											<th class="text-center" width="200">Kode DR</th>
											<th class="text-center" width="300">Customer</th>
											<th class="text-center" width="120">Volume</th>
											<th class="text-center" width="120">Nomor SPJ</th>
											<th class="text-center" width="120">Tanggal Kirim</th>
											<th class="text-center" width="">Status</th>
											<th class="text-center" width="100">Aksi</th>
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
				url: "./datatable/purchase-order.php",
				data: {
					q1: $("#q1").val(),
					q2: $("#q2").val()
				},
			});
			$('#btnSearch').on('click', function() {
				$("#table-grid").ajaxGrid("draw", {
					data: {
						q1: $("#q1").val(),
						q2: $("#q2").val()
					}
				});
				return false;
			});
			$('#tableGridLength').on('change', function() {
				$("#table-grid").ajaxGrid("pageLen", $(this).val());
			});
			$('#table-grid tbody').on('click', '[data-action="terimaTagihan"]', function(e) {
				e.preventDefault();
				if (confirm("Tagihan sudah diterima.\nApakah anda yakin ?")) {
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
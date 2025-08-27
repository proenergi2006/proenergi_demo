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
				<h1>Daftar Penerima Refund</h1>
			</section>
			<section class="content">

				<?php $flash->display(); ?>
				<div class="alert alert-danger alert-dismissible" style="display:none">
					<div class="box-tools">
						<button data-alert="remove" class="btn btn-box-tool close" type="button"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="box box-info">
							<div class="box-header with-border">
								<div class="row">
									<div class="col-sm-6">
										<div style="font-size:16px;"><b>PENCARIAN</b></div>
									</div>
									<div class="col-sm-6">
										<!-- <div class="text-right">
											<a href="<?php echo $linkEx; ?>" class="btn btn-success btn-sm" target="_blank" id="expData">Export Data</a>
										</div> -->
									</div>
								</div>
							</div>
							<div class="box-body">
								<?php
								if (in_array($sesRole, array("3", "4", "6", "7", "17", "20", "21"))) {
									if ($sesRole == '6') { //OM1 dan OM2
										$agc = " and id_master != 1 and id_group_cabang = '" . $sesGrup . "'";
										$agm = " and id_group = '" . $sesGrup . "'";
									} elseif ($sesRole == '3' || $sesRole == '4' || $sesRole == '21') { //CEO & CFO
										$agc = "";
										$agm = "";
									} elseif ($sesRole == '7') { //BM
										$agm = " and id_wilayah = '" . $sesCbng . "'";
									} else { //SPV
										$agm = " and id_wilayah = '" . $sesCbng . "'";
									}
								}
								?>
								<form name="sFrm" id="sFrm" method="post">
									<div class="table-responsive">
										<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table no-border col-sm-top table-pencarian" style="margin-bottom:0px;">
											<tr>
												<td>Keywords</td>
												<td>Status</td>
												<td></td>
											</tr>
											<tr>
												<td>
													<input type="text" class="form-control" placeholder="Masukkan keywords..." name="q1" id="q1">
												</td>
												<td style="text-transform:uppercase">
													<select name="q2" id="q2" class="form-control select2">
														<option></option>
														<option value="1">Aktif</option>
														<option value="2">Non Aktif</option>
													</select>
												</td>
												<td align="center">
													<button type="submit" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch"><i class="fa fa-search jarak-kanan"></i>Search</button>
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
									<div class="col-sm-6">
										<a href="<?php echo BASE_URL_CLIENT . '/add-master-penerima-refund.php'; ?>" class="btn btn-primary">
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
								<table class="table table-bordered" id="table-grid">
									<thead>
										<tr>
											<th class="text-center" width="50">No</th>
											<th class="text-center" width="250">Nama Customer</th>
											<th class="text-center" width="200">Nama Penerima</th>
											<th class="text-center" width="150">Divisi</th>
											<th class="text-center" width="100">Status</th>
											<th class="text-center" width="150">Status Approve</th>
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

	<style>
		#table-grid td,
		#table-grid th {
			font-size: 12px;
		}

		.table>tbody>tr>td {
			padding: 5px;
		}
	</style>
	<script>
		$(document).ready(function() {
			// $("select#q2").select2({
			// 	placeholder: "Nama Area",
			// 	allowClear: true
			// });

			$("#table-grid").ajaxGrid({
				url: "./datatable/master-penerima-refund.php",
				data: {},
			});
			$('#btnSearch').on('click', function() {
				var valq1 = $('#q1').val();
				var valq2 = $('#q2').val();
				$("#table-grid").ajaxGrid("draw", {
					data: {
						q1: valq1,
						q2: valq2,
					}
				});
				return false;
			});
			$('#tableGridLength').on('change', function() {
				$("#table-grid").ajaxGrid("pageLen", $(this).val());
			});
		});
	</script>
</body>

</html>
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
$sesRole = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$sesGrup = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);
$sesCbng = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

$month_now = date("m");
$year_now = date("Y");

// Mendapatkan tanggal awal bulan
$awalBulan = date('01/m/Y', strtotime("$year_now-$month_now-01"));

// Mendapatkan tanggal akhir bulan
$akhirBulan = date('t/m/Y', strtotime("$year_now-$month_now-01"));

// Cek peran pengguna
$required_role = ['1', '2'];
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

<style>
	th.sticky,
	td.sticky {
		position: -webkit-sticky;
		/* For Safari */
		position: sticky;
		left: 0;
		background-color: #f4f4f4;
		z-index: 2;
		/* Ensures the sticky column is on top of other columns */
	}

	/* th {
		background-color: #f4f4f4;
	} */

	thead th {
		position: sticky;
		top: 0;
		/* background-color: #ddd; */
		z-index: 1;
		/* Ensure the header row is on top of other content */
	}
</style>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory . "/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
		<aside class="right-side">
			<section class="content-header">
				<h1>Report Monthly </h1>
			</section>
			<section class="content">

				<?php $flash->display(); ?>

				<div class="row">
					<div class="col-sm-12">
						<div class="box box-info">
							<div class="container-fluid">
								<br>
								<form name="sFrm" id="sFrm" method="post">
									<div class="form-group row">
										<div class="col-sm-4">
											<div class="input-group">
												<span class="input-group-addon" id="tgl-addon">Periode</span>
												<input type="text" name="q1" id="q1" class="form-control input-sm datepicker" autocomplete="off" required value="<?= $awalBulan ?>" />
											</div>
										</div>
										<div class="col-sm-4">
											<div class="input-group">
												<span class="input-group-addon">S/D</span>
												<input type="text" name="q2" id="q2" class="form-control input-sm datepicker" autocomplete="off" required max="<?= date("Y-m-d") ?>" value="<?= $akhirBulan ?>" />
											</div>
										</div>
										<div class="col-sm-2">
											<button type="button" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch" style="width:80px;" value="1">Filter</button>
										</div>
										<div class="col-sm-2">
											<!-- <button type="submit" class="btn btn-success btn-sm" name="btnSubmit" id="btnExp" style="width:80px;" value="2">Export</button> -->
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<div class="box-body table-responsive">
					<div style="width:2000px; height:auto;">
						<table class="table table-bordered table-hover" id="data-report-table">
							<thead>
								<tr>
									<th class="text-center" rowspan="2" width="150px">Cabang</th>
									<th class="text-center" colspan="3" style="background-color: lightgreen;">PO Customer</th>
									<th class="text-center" colspan="3" style="background-color: coral;">DO</th>
									<th class="text-center" colspan="2" style="background-color: lightblue;">Loaded</th>
									<th class="text-center" colspan="2" style="background-color: orange;">Delivered</th>
									<th class="text-center" colspan="2" style="background-color: lightseagreen;">Realisasi</th>
									<th class="text-center" colspan="3" style="background-color: lavender;">Invoice</th>
								</tr>
								<tr>
									<!-- PO Customer -->
									<th class="text-center" style="background-color: lightgreen;">Qty</th>
									<th class="text-center" width="100px" style="background-color: lightgreen;">Vol</th>
									<th class="text-center" width="150px" style="background-color: lightgreen;">Nominal</th>
									<!-- DO -->
									<th class="text-center" style="background-color: coral;">Qty</th>
									<th class="text-center" width="100px" style="background-color: coral;">Vol</th>
									<th class="text-center" width="150px" style="background-color: coral;">Nominal</th>
									<!-- Loaded -->
									<th class="text-center" style="background-color: lightblue;">Qty</th>
									<th class="text-center" width="100px" style="background-color: lightblue;">Vol</th>
									<!-- Delivered -->
									<th class="text-center" style="background-color: orange;">Qty</th>
									<th class="text-center" width="100px" style="background-color: orange;">Vol</th>
									<!-- Realisasi -->
									<th class="text-center" style="background-color: lightseagreen;">Qty</th>
									<th class="text-center" width="100px" style="background-color: lightseagreen;">Vol</th>
									<!-- Invoice -->
									<th class="text-center" style="background-color: lavender;">Qty</th>
									<th class="text-center" width="100px" style="background-color: lavender;">Vol</th>
									<th class="text-center" width="150px" style="background-color: lavender;">Nominal</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>

				<!-- Modal Detail PO Customer -->
				<div class="modal fade" id="modalDetailPoc" role="dialog" tabindex="-1" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header bg-blue">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">Detail PO Customer</h4>
							</div>
							<div class="modal-body">
								<div class="box-body table-responsive">
									<table width="100%" class="table table-bordered table-hover" id="data-detail-poc">
										<thead>
											<tr>
												<th width="5%" class="text-center">
													No
												</th>
												<th width="20%" class="text-center">
													Nama Customer
												</th>
												<th width="20%" class="text-center">
													Nomor POC
												</th>
												<th width="20%" class="text-center">
													Tanggal POC
												</th>
												<th width="10%" class="text-center">
													Harga POC
												</th>
												<th width="10%" class="text-center">
													Volume POC
												</th>
											</tr>
										</thead>

										<tbody id="bodyResultPOC">

										</tbody>

									</table>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Modal Detail DO -->
				<div class="modal fade" id="modalDetailDo" role="dialog" tabindex="-1" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header bg-blue">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">Detail DO</h4>
							</div>
							<div class="modal-body">
								<div class="box-body table-responsive">
									<table width="100%" class="table table-bordered table-hover" id="data-detail-do">
										<thead>
											<tr>
												<th width="5%" class="text-center">
													No
												</th>
												<th width="20%" class="text-center">
													Nama Customer
												</th>
												<th width="20%" class="text-center">
													Nomor DO
												</th>
												<th width="20%" class="text-center">
													Nomor DR
												</th>
												<th width="10%" class="text-center">
													Tanggal DR
												</th>
												<th width="10%" class="text-center">
													Harga
												</th>
												<th width="10%" class="text-center">
													Volume
												</th>
											</tr>
										</thead>

										<tbody id="bodyResultDO">

										</tbody>

									</table>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Modal Detail Loaded -->
				<div class="modal fade" id="modalDetailLoaded" role="dialog" tabindex="-1" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header bg-blue">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">Detail Loaded</h4>
							</div>
							<div class="modal-body">
								<div class="box-body table-responsive">
									<table width="100%" class="table table-bordered table-hover" id="data-detail-loaded">
										<thead>
											<tr>
												<th width="5%" class="text-center">
													No
												</th>
												<th width="20%" class="text-center">
													Nama Customer
												</th>
												<th width="20%" class="text-center">
													Nomor DS
												</th>
												<th width="10%" class="text-center">
													Nomor DN
												</th>
												<th width="10%" class="text-center">
													Nomor DO
												</th>
												<th width="20%" class="text-center">
													Tanggal Loaded
												</th>
												<th width="10%" class="text-center">
													Volume
												</th>
											</tr>
										</thead>

										<tbody id="bodyResultLoaded">

										</tbody>

									</table>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Modal Detail Delivered -->
				<div class="modal fade" id="modalDetailDelivered" role="dialog" tabindex="-1" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header bg-blue">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">Detail Delivered</h4>
							</div>
							<div class="modal-body">
								<div class="box-body table-responsive">
									<table width="100%" class="table table-bordered table-hover" id="data-detail-delivered">
										<thead>
											<tr>
												<th width="5%" class="text-center">
													No
												</th>
												<th width="20%" class="text-center">
													Nama Customer
												</th>
												<th width="20%" class="text-center">
													Nomor DS
												</th>
												<th width="10%" class="text-center">
													Nomor DN
												</th>
												<th width="10%" class="text-center">
													Nomor DO
												</th>
												<th width="20%" class="text-center">
													Tanggal Loaded
												</th>
												<th width="20%" class="text-center">
													Tanggal Delivered
												</th>
												<th width="10%" class="text-center">
													Volume
												</th>
											</tr>
										</thead>

										<tbody id="bodyResultDelivered">

										</tbody>

									</table>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Modal Detail Realisasi -->
				<div class="modal fade" id="modalDetailRealisasi" role="dialog" tabindex="-1" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header bg-blue">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">Detail Realiasi</h4>
							</div>
							<div class="modal-body">
								<div class="box-body table-responsive">
									<table width="100%" class="table table-bordered table-hover" id="data-detail-realisasi">
										<thead>
											<tr>
												<th width="5%" class="text-center">
													No
												</th>
												<th width="20%" class="text-center">
													Nama Customer
												</th>
												<th width="20%" class="text-center">
													Nomor DS
												</th>
												<th width="10%" class="text-center">
													Nomor DN
												</th>
												<th width="10%" class="text-center">
													Nomor DO
												</th>
												<th width="20%" class="text-center">
													Tanggal Loaded
												</th>
												<th width="20%" class="text-center">
													Tanggal Delivered
												</th>
												<th width="10%" class="text-center">
													Volume
												</th>
												<th width="10%" class="text-center">
													Volume Realisasi
												</th>
											</tr>
										</thead>

										<tbody id="bodyResultRealisasi">

										</tbody>

									</table>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Modal Detail Invoice -->
				<div class="modal fade" id="modalDetailInvoice" role="dialog" tabindex="-1" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header bg-blue">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">Detail Invoice</h4>
							</div>
							<div class="modal-body">
								<div class="box-body table-responsive">
									<table width="100%" class="table table-bordered table-hover" id="data-detail-invoice">
										<thead>
											<tr>
												<th width="5%" class="text-center">
													No
												</th>
												<th width="20%" class="text-center">
													Nama Customer
												</th>
												<th width="20%" class="text-center">
													Nomor Invoice
												</th>
												<th width="10%" class="text-center">
													Tanggal Invoice
												</th>
												<th width="10%" class="text-center">
													Total Invoice
												</th>
											</tr>
										</thead>

										<tbody id="bodyResultInvoice">

										</tbody>

									</table>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Modal Detail Invoice Volume -->
				<div class="modal fade" id="modalDetailVolInvoice" role="dialog" tabindex="-1" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header bg-blue">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">Detail Volume Invoice</h4>
							</div>
							<div class="modal-body">
								<div class="box-body table-responsive">
									<table width="100%" class="table table-bordered table-hover" id="data-detail-volume-invoice">
										<thead>
											<tr>
												<th width="5%" class="text-center">
													No
												</th>
												<th width="20%" class="text-center">
													Nama Customer
												</th>
												<th width="20%" class="text-center">
													Nomor Invoice
												</th>
												<th width="10%" class="text-center">
													Tanggal Invoice
												</th>
												<th width="10%" class="text-center">
													Volume
												</th>
											</tr>
										</thead>

										<tbody id="bodyResultVolInvoice">

										</tbody>

									</table>
								</div>
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
			function formatRupiah(amount) {
				// Cek jika amount bukan angka atau kurang dari 0
				if (isNaN(amount) || amount < 0) {
					return 'Invalid amount';
				}

				// Format angka dengan dua desimal dan gunakan locale 'id-ID' untuk format Rupiah
				return amount.toLocaleString('id-ID', {
					style: 'currency',
					currency: 'IDR',
					minimumFractionDigits: 2,
					maximumFractionDigits: 2
				});
			}

			$(".hitung").number(true, 0, ".", ",");

			$("#data-report-table").ajaxGrid({
				url: "../datatable/data-report-monthly.php",
				data: {
					q1: $("#q1").val(),
					q2: $("#q2").val()
				},
			});

			$('#btnSearch').on('click', function() {
				$("#data-report-table").ajaxGrid("draw", {
					data: {
						q1: $("#q1").val(),
						q2: $("#q2").val(),
					}
				});
				return false;
			});
			$('#tableGridLength').on('change', function() {
				$("#data-report-table").ajaxGrid("pageLen", $(this).val());
			});
			$('#expData').on('click', function() {
				$(this).prop("href", $("#uriExp").val());
			});

			$("#q1, #q2").on('keydown', function(e) {
				e.preventDefault();
			})

			$('#data-report-table').on('click', '.openDetail', function() {
				var cabang = $(this).attr('data-cabang');
				var tgl_awal = $(this).attr('data-date-start');
				var tgl_akhir = $(this).attr('data-date-end');
				var kategori = $(this).attr('data-kategori');
				if (kategori == "POC") {
					$('#modalDetailPoc').modal({
						show: true,
						keyboard: false,
					})
				} else if (kategori == "DO") {
					$('#modalDetailDo').modal({
						show: true,
						keyboard: false,
					})
				} else if (kategori == "Loaded") {
					$('#modalDetailLoaded').modal({
						show: true,
						keyboard: false,
					})
				} else if (kategori == "Delivered") {
					$('#modalDetailDelivered').modal({
						show: true,
						keyboard: false,
					})
				} else if (kategori == "Realisasi") {
					$('#modalDetailRealisasi').modal({
						show: true,
						keyboard: false,
					})
				} else if (kategori == "Invoice") {
					$('#modalDetailInvoice').modal({
						show: true,
						keyboard: false,
					})
				} else if (kategori == "Volume Invoice") {
					$('#modalDetailVolInvoice').modal({
						show: true,
						keyboard: false,
					})
				}
				$.ajax({
					type: "POST",
					url: `<?= BASE_URL . "/web/datatable/data-detail-report-monthly.php" ?>`,
					dataType: "json",
					data: {
						"cabang": cabang,
						"tgl_awal": tgl_awal,
						"tgl_akhir": tgl_akhir,
						"kategori": kategori
					},
					success: function(result) {
						if (result.kategori == 'POC') {
							// console.log(result);
							var html = "";
							var total = 0;

							for (var i = 0; i < result.data.length; i++) {

								total += parseInt(result.data[i]['volume_poc'])

								var no = i + 1;
								html += "<tr>";
								html += "<td>" + no + "</td>";
								html += "<td>" + result.data[i]['nama_customer'] + "</td>";
								html += "<td align='center'>" + result.data[i]['nomor_poc'] + "</td>";
								html += "<td align='center'>" + result.data[i]['tanggal_poc'] + "</td>";
								html += "<td align='right'>" + new Intl.NumberFormat().format(result.data[i]['harga_poc']) + "</td>";
								html += "<td align='right'>" + new Intl.NumberFormat().format(result.data[i]['volume_poc']) + "</td>";
								html += "</tr>";
							}
							html += "<td align='center' colspan='5'><b>TOTAL</b></td>";
							html += "<td align='center'><b>" + new Intl.NumberFormat().format(total) + "</b></td>";
							$('#bodyResultPOC').html(html);
						} else if (result.kategori == 'DO') {
							var html = "";
							var total = 0;

							for (var i = 0; i < result.data.length; i++) {

								total += parseInt(result.data[i]['volume'])

								var no = i + 1;
								html += "<tr>";
								html += "<td>" + no + "</td>";
								html += "<td>" + result.data[i]['nama_customer'] + "</td>";
								html += "<td align='center'>" + result.data[i]['no_do_syop'] + "</td>";
								html += "<td align='center'>" + result.data[i]['nomor_pr'] + "</td>";
								html += "<td align='center' nowrap>" + result.data[i]['tanggal_pr'] + "</td>";
								html += "<td align='right'>" + new Intl.NumberFormat().format(result.data[i]['harga_dasar']) + "</td>";
								html += "<td align='right'>" + new Intl.NumberFormat().format(result.data[i]['volume']) + "</td>";
								html += "</tr>";
							}
							html += "<td align='center' colspan='6'><b>TOTAL</b></td>";
							html += "<td align='center'><b>" + new Intl.NumberFormat().format(total) + "</b></td>";
							$('#bodyResultDO').html(html);
						} else if (result.kategori == 'Loaded') {
							var html = "";
							var total = 0;

							for (var i = 0; i < result.data.length; i++) {

								total += parseInt(result.data[i]['volume'])

								var no = i + 1;
								html += "<tr>";
								html += "<td>" + no + "</td>";
								html += "<td>" + result.data[i]['nama_customer'] + "</td>";
								html += "<td align='center'>" + result.data[i]['nomor_ds'] + "</td>";
								html += "<td align='center' nowrap>" + result.data[i]['nomor_do'] + "</td>";
								html += "<td align='center' nowrap>" + result.data[i]['no_do_syop'] + "</td>";
								html += "<td align='center' nowrap>" + result.data[i]['tanggal_loaded'] + "</td>";
								html += "<td align='right'>" + new Intl.NumberFormat().format(result.data[i]['volume']) + "</td>";
								html += "</tr>";
							}
							html += "<td align='center' colspan='6'><b>TOTAL</b></td>";
							html += "<td align='center'><b>" + new Intl.NumberFormat().format(total) + "</b></td>";
							$('#bodyResultLoaded').html(html);
						} else if (result.kategori == 'Delivered') {
							var html = "";
							var total = 0;

							for (var i = 0; i < result.data.length; i++) {

								total += parseInt(result.data[i]['volume']);

								var no = i + 1;
								html += "<tr>";
								html += "<td>" + no + "</td>";
								html += "<td>" + result.data[i]['nama_customer'] + "</td>";
								html += "<td align='center'>" + result.data[i]['nomor_ds'] + "</td>";
								html += "<td align='center' nowrap>" + result.data[i]['nomor_do'] + "</td>";
								html += "<td align='center' nowrap>" + result.data[i]['no_do_syop'] + "</td>";
								html += "<td align='center' nowrap>" + result.data[i]['tanggal_loaded'] + "</td>";
								html += "<td align='center' nowrap>" + result.data[i]['tanggal_delivered'] + "</td>";
								html += "<td align='right'>" + new Intl.NumberFormat().format(result.data[i]['volume']) + "</td>";
								html += "</tr>";
							}
							html += "<td align='center' colspan='7'><b>TOTAL</b></td>";
							html += "<td align='center'><b>" + new Intl.NumberFormat().format(total) + "</b></td>";
							$('#bodyResultDelivered').html(html);
						} else if (result.kategori == 'Realisasi') {
							var html = "";
							var total_vol = 0;
							var total_realisasi = 0;

							for (var i = 0; i < result.data.length; i++) {

								total_vol += parseInt(result.data[i]['volume']);
								total_realisasi += parseInt(result.data[i]['realisasi_volume']);

								var no = i + 1;
								html += "<tr>";
								html += "<td>" + no + "</td>";
								html += "<td>" + result.data[i]['nama_customer'] + "</td>";
								html += "<td align='center'>" + result.data[i]['nomor_ds'] + "</td>";
								html += "<td align='center' nowrap>" + result.data[i]['nomor_do'] + "</td>";
								html += "<td align='center' nowrap>" + result.data[i]['no_do_syop'] + "</td>";
								html += "<td align='center' nowrap>" + result.data[i]['tanggal_loaded'] + "</td>";
								html += "<td align='center' nowrap>" + result.data[i]['tanggal_delivered'] + "</td>";
								html += "<td align='right'>" + new Intl.NumberFormat().format(result.data[i]['volume']) + "</td>";
								html += "<td align='right'>" + new Intl.NumberFormat().format(result.data[i]['realisasi_volume']) + "</td>";
								html += "</tr>";
							}
							html += "<td align='center' colspan='7'><b>TOTAL</b></td>";
							html += "<td align='center'><b>" + new Intl.NumberFormat().format(total_vol) + "</b></td>";
							html += "<td align='center'><b>" + new Intl.NumberFormat().format(total_realisasi) + "</b></td>";
							$('#bodyResultRealisasi').html(html);
						} else if (result.kategori == 'Invoice') {
							var html = "";
							var total = 0;

							for (var i = 0; i < result.data.length; i++) {

								total += parseInt(result.data[i]['total_invoice']);

								var no = i + 1;
								html += "<tr>";
								html += "<td>" + no + "</td>";
								html += "<td>" + result.data[i]['nama_customer'] + "</td>";
								html += "<td align='center' nowrap>" + result.data[i]['no_invoice'] + "</td>";
								html += "<td align='center' nowrap>" + result.data[i]['tgl_invoice'] + "</td>";
								html += "<td align='center' nowrap>" + formatRupiah(parseFloat(result.data[i]['total_invoice'])) + "</td>";
								html += "</tr>";
							}
							html += "<td align='center' colspan='4'><b>TOTAL</b></td>";
							html += "<td align='center'><b>" + formatRupiah(total) + "</b></td>";
							$('#bodyResultInvoice').html(html);
						} else if (result.kategori == 'Volume Invoice') {
							var html = "";
							var total = 0;

							for (var i = 0; i < result.data.length; i++) {

								total += parseInt(result.data[i]['vol_kirim']);

								var no = i + 1;
								html += "<tr>";
								html += "<td>" + no + "</td>";
								html += "<td>" + result.data[i]['nama_customer'] + "</td>";
								html += "<td align='center' nowrap>" + result.data[i]['no_invoice'] + "</td>";
								html += "<td align='center' nowrap>" + result.data[i]['tgl_invoice'] + "</td>";
								html += "<td align='center' nowrap>" + new Intl.NumberFormat().format(result.data[i]['vol_kirim']) + "</td>";
								html += "</tr>";
							}
							html += "<td align='center' colspan='4'><b>TOTAL</b></td>";
							html += "<td align='center'><b>" + new Intl.NumberFormat().format(total) + "</b></td>";
							$('#bodyResultVolInvoice').html(html);
						}
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {
						alert("Error");
					}
				});
			});
		});
	</script>
</body>

</html>
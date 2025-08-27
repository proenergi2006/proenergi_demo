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
$required_role = ['1', '7'];
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

<style>
	th,
	td {
		padding-top: 5px;
		padding-bottom: 5px;
		padding-left: 5px;
	}
</style>

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

				<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header bg-blue">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">Detail Penerima Refund</h4>
							</div>
							<div class="modal-body">
								<input type="hidden" name="id_refund" id="id_refund" readonly>
								<table width="100%" border="0" cellpadding="5">
									<tr>
										<td width="20%">
											Nama Customer
										</td>
										<td width="3%">
											:
										</td>
										<td id="nama_customer">

										</td>
									</tr>
									<tr>
										<td>
											Nama Penerima
										</td>
										<td>
											:
										</td>
										<td id="nama_penerima">

										</td>
									</tr>
									<tr>
										<td>
											Divisi
										</td>
										<td>
											:
										</td>
										<td id="divisi">

										</td>
									</tr>
									<tr>
										<td>
											Nomor KTP
										</td>
										<td>
											:
										</td>
										<td id="no_ktp">

										</td>
									</tr>
									<tr>
										<td>
											Foto KTP
										</td>
										<td>
											:
										</td>
										<td id="myImage">
										</td>
									</tr>
									<tr>
										<td>
											Foto NPWP
										</td>
										<td>
											:
										</td>
										<td id="myImageNpwp">
										</td>
									</tr>
									<tr>
										<td>
											Nama Bank
										</td>
										<td>
											:
										</td>
										<td id="bank">

										</td>
									</tr>
									<tr>
										<td>
											Nomor Rekening
										</td>
										<td>
											:
										</td>
										<td id="no_rekening">

										</td>
									</tr>
									<tr>
										<td>
											Atas Nama
										</td>
										<td>
											:
										</td>
										<td id="atas_nama">

										</td>
									</tr>
								</table>
								<hr>
								<table width="100%" border="0" cellpadding="5">
									<tr>
										<td width="20%">
											<b>Status BM</b>
										</td>
										<td width="3%">
											:
										</td>
										<td id="approved_bm_by">

										</td>
									</tr>
									<tr>
										<td width="20%">
											<b>Catatan BM</b>
										</td>
										<td width="3%">
											:
										</td>
										<td id="catatan_bm_td">
											<input type="text" class="form-control" id="catatan_bm" name="catatan_bm" placeholder="Masukan catatan disini">
										</td>
									</tr>
									<tr>
										<td>
											<b>Status CEO</b>
										</td>
										<td>
											:
										</td>
										<td id="approved_ceo_by">

										</td>
									</tr>
									<tr id="catatan_ceo_tr">
										<td width="20%">
											<b>Catatan CEO</b>
										</td>
										<td width="3%">
											:
										</td>
										<td id="catatan_ceo_td">

										</td>
									</tr>
								</table>
								<br>
								<div class="modal-footer">
									<!-- <button id="btnReject" type="button" class="btn btn-danger btn-md"><i class="fa fa-times-circle"></i> Tolak</button> -->
									<button id="btnApprove" type="button" class="btn btn-success btn-md"><i class="fa fa-thumbs-up"></i> Approve</button>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="modal fade" id="loading_modal" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header bg-blue">
								<h4 class="modal-title">Loading Data ...</h4>
							</div>
							<div class="modal-body text-center modal-loading"></div>
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
				url: "./datatable/master-penerima-refund-bm.php",
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

			$('#table-grid').on('click', '.openDetail', function() {
				var id_refund = $(this).attr('data-id');
				var param = $(this).attr('data-param');
				const myArray = param.split("|");
				var customer = myArray[0];
				var nama_penerima = myArray[1];
				var divisi = myArray[2];
				var bank = myArray[3];
				var no_rekening = myArray[4];
				var atas_nama = myArray[5];
				var is_bm = myArray[6];
				var approved_bm_by = myArray[7];
				var approved_bm_date = myArray[8];
				var catatan_bm = myArray[9];
				var catatan_ceo = myArray[10];
				var is_ceo = myArray[11];
				var approved_ceo_by = myArray[12];
				var approved_ceo_date = myArray[13];
				var no_ktp = myArray[14];
				var url_ktp = myArray[15];
				var url_npwp = myArray[16];

				$('#modalDetail').modal({
					show: true
				})
				$("#id_refund").val(id_refund);
				$("#nama_customer").html(customer);
				$("#nama_penerima").html(nama_penerima);
				$("#divisi").html(divisi);
				$("#bank").html(bank);
				$("#no_rekening").html(no_rekening);
				$("#atas_nama").html(atas_nama);
				$("#no_ktp").html(no_ktp);

				$('#myImage').html(`<a href="` + url_ktp + `"  target="_blank">Preview File</a>`);
				if (url_npwp == "") {
					$('#myImageNpwp').html(`<span>Tidak ada foto</span>`);
				} else {
					$('#myImageNpwp').html(`<a href="` + url_npwp + `"  target="_blank">Preview File</a>`);
				}
				if (is_bm == 1) {
					$("#approved_bm_by").html("Approved by " + approved_bm_by + " | " + approved_bm_date);
					$("#catatan_bm_td").html(catatan_bm);
					$("#btnApprove").addClass("hide");
					$("#btnReject").addClass("hide");
				} else if (is_bm == 2) {
					$("#approved_bm_by").html("Rejected by " + approved_bm_by + " | " + approved_bm_date);
					$("#catatan_bm_td").html(catatan_bm);
					$("#btnApprove").addClass("hide");
					$("#btnReject").addClass("hide");
				} else {
					$("#approved_bm_by").html("Verifikasi BM");
					$("#btnApprove").removeClass("hide");
					$("#btnReject").removeClass("hide");
				}

				if (is_ceo == 1) {
					$("#approved_ceo_by").html("Approved by " + approved_ceo_by + " | " + approved_ceo_date);
					$("#catatan_ceo_td").html(catatan_ceo);
					$("#catatan_ceo_tr").removeClass("hide");
					$("#btnApprove").addClass("hide");
					$("#btnReject").addClass("hide");
				} else if (is_ceo == 2) {
					$("#approved_ceo_by").html("Rejected by " + approved_ceo_by + " | " + approved_ceo_date);
					$("#catatan_ceo_td").html(catatan_ceo);
					$("#catatan_ceo_tr").removeClass("hide");
					$("#btnApprove").addClass("hide");
					$("#btnReject").addClass("hide");
				} else {
					$("#approved_ceo_by").html("Verifikasi CEO");
					$("#catatan_ceo_tr").addClass("hide");
				}
			});

			$("#btnApprove").click(function() {
				var id_refund = $("#id_refund").val();
				var catatan_bm = $("#catatan_bm").val();
				Swal.fire({
					title: "Anda yakin Approve?",
					showCancelButton: true,
					confirmButtonText: "YA",
				}).then((result) => {
					if (result.isConfirmed) {
						$("#loading_modal").modal({
							keyboard: false,
							backdrop: 'static'
						});
						$.ajax({
							method: 'post',
							url: '<?php echo ACTION_CLIENT ?>/master-penerima-refund.php',
							data: {
								"act": "approve",
								"id_refund": id_refund,
								"catatan_bm": catatan_bm
							},
							dataType: 'json',
							success: function(result) {
								if (result.status == false) {
									setTimeout(function() {
										$("#modalDetail").modal("hide");
										Swal.fire({
											title: "Ooppss",
											text: result.pesan,
											icon: "warning"
										}).then((result) => {
											// Reload the Page
											location.reload();
										});
									}, 2000);
								} else {
									setTimeout(function() {
										$("#modalDetail").modal("hide");
										Swal.fire({
											title: "Berhasil",
											text: result.pesan,
											icon: "success"
										}).then((result) => {
											location.reload();
										});
									}, 2000);
								}
							},
							error: function(XMLHttpRequest, textStatus, errorThrown) {
								alert("Error");
								// console.log(errorThrown)
							}
						})
					}
				});
			})
			$("#btnReject").click(function() {
				var id_refund = $("#id_refund").val();
				var catatan_bm = $("#catatan_bm").val();
				Swal.fire({
					title: "Anda yakin Tolak?",
					showCancelButton: true,
					confirmButtonText: "YA",
				}).then((result) => {
					if (result.isConfirmed) {
						$("#loading_modal").modal({
							keyboard: false,
							backdrop: 'static'
						});
						$.ajax({
							method: 'post',
							url: '<?php echo ACTION_CLIENT ?>/master-penerima-refund.php',
							data: {
								"act": "reject",
								"id_refund": id_refund,
								"catatan_bm": catatan_bm
							},
							dataType: 'json',
							success: function(result) {
								if (result.status == false) {
									setTimeout(function() {
										$("#modalDetail").modal("hide");
										Swal.fire({
											title: "Ooppss",
											text: result.pesan,
											icon: "warning"
										}).then((result) => {
											// Reload the Page
											location.reload();
										});
									}, 2000);
								} else {
									setTimeout(function() {
										$("#modalDetail").modal("hide");
										Swal.fire({
											title: "Berhasil",
											text: result.pesan,
											icon: "success"
										}).then((result) => {
											location.reload();
										});
									}, 2000);
								}
							},
							error: function(XMLHttpRequest, textStatus, errorThrown) {
								alert("Error");
								// console.log(errorThrown)
							}
						})
					}
				});
			})
		});
	</script>
</body>

</html>
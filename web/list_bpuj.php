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

// Cek peran pengguna
$required_role = ['1', '10', '15', '25'];
// Misalnya halaman ini hanya untuk superadmin
if (!in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), $required_role)) {
	// Pengguna tidak memiliki peran yang tepat, redirect ke halaman lain atau tampilkan pesan akses ditolak
	$flash->add("warning", "Akses ditolak.", BASE_URL_CLIENT . "/home.php");
	// exit();
}

$query = "SELECT * FROM pro_master_cabang WHERE is_active = '1' AND id_master NOT IN('1','10') ORDER BY nama_cabang ASC";
$cabang = $con->getResult($query);
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
						<div class="col-sm-3">
							<select name="cabang" id="cabang" class="form-control">
								<option value="">Semua Cabang</option>
								<?php foreach ($cabang as $key) : ?>
									<option <?= $key['id_master'] == '2' ? 'selected' : '' ?> value="<?= $key['id_master'] ?>"><?= ucwords($key['nama_cabang']) ?></option>
								<?php endforeach ?>
							</select>
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
						<div class="col-sm-3 col-sm-top">
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
								<table class="table table-bordered" id="table-bpuj">
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
											<th class="text-center" width="250">Aksi</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
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

				<div class="modal fade" id="modalRealisasi" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header bg-blue">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">Realisasi BPUJ</h4>
							</div>
							<div class="modal-body">
								<input type="hidden" class="hide" name="id_bpuj" id="id_bpuj" readonly>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="">Realisasi (Rp)</label>
											<input type="text" class="form-control text-right" name="realisasi" id="realisasi">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="" style="color: white;">Realisasi</label>
											<br>
											<button type="button" class="btn btn-info btn-sm" name="btnRealisasi" id="btnRealisasi"><i class="fa fa-save"></i> Simpan</button>
										</div>
									</div>
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
			$("#realisasi").keyup(function() {
				$(this).val(format($(this).val()));
			});

			var format = function(num) {
				var str = num.toString().replace("", ""),
					parts = false,
					output = [],
					i = 1,
					formatted = null;
				if (str.indexOf(".") > 0) {
					parts = str.split(".");
					str = parts[0];
				}
				str = str.split("").reverse();
				for (var j = 0, len = str.length; j < len; j++) {
					if (str[j] != ",") {
						output.push(str[j]);
						if (i % 3 == 0 && j < (len - 1)) {
							output.push(",");
						}
						i++;
					}
				}
				formatted = output.reverse().join("");
				return ("" + formatted + ((parts) ? "." + parts[1].substr(0, 2) : ""));
			};

			$('#table-bpuj tbody').on('click', '.realisasiBpuj', function(e) {
				var param = $(this).data("param");
				var nominal = $(this).data("nominal");
				$("#realisasi").val(new Intl.NumberFormat("ja-JP").format(nominal));
				$("#id_bpuj").val(param);
				$('#modalRealisasi').modal({
					show: true
				})
			});

			$("#btnRealisasi").click(function() {
				var realisasi = $("#realisasi").val();
				var id_bpuj = $("#id_bpuj").val();
				Swal.fire({
					title: "Anda yakin simpan?",
					showCancelButton: true,
					confirmButtonText: "Simpan",
				}).then((result) => {
					if (result.isConfirmed) {
						if (realisasi == "") {
							Swal.fire({
								title: "Oopss",
								text: "Masukan nominal realisasi",
								icon: "warning"
							});
						} else {
							$("#modalRealisasi").modal("hide");
							$("#loading_modal").modal({
								keyboard: false,
								backdrop: 'static'
							});
							$.ajax({
								method: 'post',
								url: '<?php echo ACTION_CLIENT ?>/realisasi_bpuj.php',
								data: {
									"realisasi": realisasi,
									"id_bpuj": id_bpuj,
								},
								dataType: 'json',
								success: function(result) {
									console.log(result)
									if (result.status == false) {
										setTimeout(function() {
											$("#loading_modal").modal("hide");
											Swal.fire({
												title: "Ooppss",
												text: result.pesan,
												icon: "warning"
											}).then((result) => {
												location.reload();
											});
										}, 2000);
									} else {
										setTimeout(function() {
											$("#loading_modal").modal("hide");
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
					}
				});
			});

			$("select#q2").select2({
				placeholder: "Status",
				allowClear: true
			});
			$("#table-bpuj").ajaxGrid({
				url: "./datatable/list_bpuj.php",
				data: {
					q1: $("#q1").val(),
					q2: $("#q2").val(),
					q3: $("#q3").val(),
					q4: $("#q4").val(),
					q5: $("#q5").val(),
					cabang: $("#cabang").val()
				},
			});
			$('#btnSearch').on('click', function() {
				$("#table-bpuj").ajaxGrid("draw", {
					data: {
						q1: $("#q1").val(),
						q2: $("#q2").val(),
						q3: $("#q3").val(),
						q4: $("#q4").val(),
						q5: $("#q5").val(),
						cabang: $("#cabang").val()
					}
				});
				return false;
			});
			$('#tableGridLength').on('change', function() {
				$("#table-bpuj").ajaxGrid("pageLen", $(this).val());
			});
		});
	</script>
</body>

</html>
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
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("myGrid"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory . "/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
		<aside class="right-side">
			<section class="content-header">
				<h1>List Unblock Customer</h1>
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
						<div class="col-sm-2">
							<select name="q2" id="q2">
								<option></option>
								<option value="0">Verifikasi Admin Finance</option>
								<option value="1">Verifikasi Finance</option>
								<option value="2">Verifikasi Manager Finance</option>
								<option value="3">Verifikasi CFO</option>
								<option value="4">Verifikasi CEO</option>
								<option value="5">Approved</option>
								<option value="6">Ditolak</option>
							</select>
						</div>
						<div class="col-sm-4">
							<button type="submit" disabled class="btn btn-sm btn-info" name="btnSearch" id="btnSearch">Cari</button>
						</div>
					</div>
				</form>

				<div class="row">
					<div class="col-sm-12">
						<div class="box box-info">
							<div class="box-header with-border">
								<div class="row">
									<div class="col-sm-6">
										<!-- <a href="<?php echo BASE_URL_CLIENT . '/form-unblock-add.php'; ?>" class="btn btn-primary">
											<i class="fa fa-plus jarak-kanan"></i>Add Data
										</a> -->
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
											<th class="text-center" width="3%">No</th>
											<th class="text-center" width="18%">Nomor Dokumen</th>
											<th class="text-center" width="13%">Nama Customer</th>
											<th class="text-center" width="15%">Total PO</th>
											<th class="text-center" width="5%">TOP Temporary</th>
											<th class="text-center" width="10%">Tanggal</th>
											<th class="text-center" width="16%">Status</th>
											<th class="text-center" width="4%"><i class="fa fa-paperclip"></i></th>
											<th class="text-center" width="10%">Aksi</th>
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

	<!-- Modal File -->
	<div class="modal fade" id="fileModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">

				<div class="modal-header">
					<h5 class="modal-title">Daftar Lampiran</h5>
				</div>

				<div class="modal-body">
					<ul class="list-group" id="fileList">
						<!-- File list generated here -->
					</ul>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
				</div>

			</div>
		</div>
	</div>


	<style>
		.timeline {
			position: relative;
			padding-left: 30px;
			margin-top: 20px;
		}

		.timeline::before {
			content: '';
			position: absolute;
			top: 0;
			left: 12px;
			width: 2px;
			height: 100%;
			background: #dee2e6;
			/* abu-abu garis */
		}

		.timeline-item {
			position: relative;
			margin-bottom: 20px;
		}

		.timeline-icon {
			position: absolute;
			left: 0;
			top: 0;
			width: 24px;
			height: 24px;
			border-radius: 50%;
		}

		.timeline-content {
			padding-left: 40px;
		}
	</style>
	<!-- Modal History-->
	<div class="modal fade" id="historyApproval" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">

				<div class="modal-header">
					<h5 class="modal-title">History Approval</h5>
				</div>

				<div class="modal-body" id="historyContent">
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
				</div>

			</div>
		</div>
	</div>

	<style>
		#table-grid td,
		#table-grid th {
			font-size: 12px;
		}
	</style>
	<script>
		$(document).ready(function() {
			$("#q2").select2({
				placeholder: "Status",
				allowClear: true
			});
			$("#table-grid").ajaxGrid({
				url: "./datatable/form-unblock-marketing.php",
				data: {
					q1: $("#q1").val(),
					q2: $("#q2").val()
				},
			});
			// $('#btnSearch').on('click', function() {
			// 	$("#table-grid").ajaxGrid("draw", {
			// 		data: {
			// 			q1: $("#q1").val(),
			// 			q2: $("#q2").val()
			// 		}
			// 	});
			// 	return false;
			// });
			$('#tableGridLength').on('change', function() {
				$("#table-grid").ajaxGrid("pageLen", $(this).val());
			});
			$('#table-grid tbody').on('click', '[data-action="deleteGrid"]', function(e) {
				e.preventDefault();
				const that = this; // simpan this agar tidak hilang di dalam then
				Swal.fire({
					title: "Anda yakin hapus?",
					showCancelButton: true,
					confirmButtonText: "Hapus",
				}).then((result) => {
					if (result.isConfirmed) {
						$("#loading_modal").modal({
							keyboard: false,
							backdrop: 'static'
						});
						var param = $(that).data("param-idx");
						var handler = function(data) {
							if (data.error == "") {
								Swal.fire({
									title: "Berhasil",
									text: "Berhasil Hapus",
									icon: "success"
								}).then(() => {
									location.reload();
								});
							} else {
								Swal.fire({
									title: "Gagal",
									text: "Gagal di hapus",
									icon: "error"
								});
							}
						};
						$.post("./datatable/deleteTable.php", {
							param: param
						}, handler, "json");
					}
					// jika Cancel ditekan, tidak ada aksi tambahan
				});
			});

			$('#table-grid tbody').on('click', '.btnShowFiles', function() {
				const files = $(this).data("param-idx");
				// Clear list
				$('#fileList').empty();

				// Generate list
				files.forEach(file => {
					const url = "../files/uploaded_user/file_unblock/" + file.nama_file;
					const listItem = `<li class="list-group-item d-flex justify-content-between align-items-center">
					<a href="${url}" target="_blank">${file.nama_file_ori}</a>
					</li>`;
					$('#fileList').append(listItem);
				});

				// Tampilkan modal
				$("#fileModal").modal("show");
			});

			$('#table-grid tbody').on('click', '.btnShowHistory', function(e) {
				e.preventDefault();

				const param = $(this).data('param-idx');
				const parts = param.split('#|#');

				var disposisi = parts[0];

				if (disposisi == 0) {
					var status_disposisi = "Proses Verifikasi";
				} else if (disposisi == 1) {
					var status_disposisi = "Terverifikasi";
				} else {
					var status_disposisi = "Rejected";
				}

				var is_admin = parts[1];
				var date_admin = parts[2];
				var pic_admin = parts[3];

				if (is_admin == 0) {
					var status_admin = "Not Yet";
				} else if (is_admin == 1) {
					var status_admin = "Approved" + " | " + formatTanggal(date_admin) + " | " + pic_admin;
				} else {
					var status_admin = "Rejected" + " | " + formatTanggal(date_admin) + " | " + pic_admin;
				}

				var is_finance = parts[4];
				var date_finance = parts[5];
				var pic_finance = parts[6];

				if (is_finance == 0) {
					var status_finance = "Not Yet";
				} else if (is_finance == 1) {
					var status_finance = "Approved" + " | " + formatTanggal(date_finance) + " | " + pic_finance;
				} else {
					var status_finance = "Rejected" + " | " + formatTanggal(date_finance) + " | " + pic_finance;
				}

				var is_mgr_fin = parts[7];
				var date_mgr_fin = parts[8];
				var pic_mgr_fin = parts[9];

				if (is_mgr_fin == 0) {
					var status_mgr_fin = "Not Yet";
				} else if (is_mgr_fin == 1) {
					var status_mgr_fin = "Approved" + " | " + formatTanggal(date_mgr_fin) + " | " + pic_mgr_fin;
				} else {
					var status_mgr_fin = "Rejected" + " | " + formatTanggal(date_mgr_fin) + " | " + pic_mgr_fin;
				}

				var is_cfo = parts[10];
				var date_cfo = parts[11];
				var pic_cfo = parts[12];

				if (is_cfo == 0) {
					var status_cfo = "Not Yet";
				} else if (is_cfo == 1) {
					var status_cfo = "Approved" + " | " + formatTanggal(date_cfo) + " | " + pic_cfo;
				} else {
					var status_cfo = "Rejected" + " | " + formatTanggal(date_cfo) + " | " + pic_cfo;
				}

				var is_ceo = parts[13];
				var date_ceo = parts[14];
				var pic_ceo = parts[15];

				if (is_ceo == 0) {
					var status_ceo = "Not Yet";
				} else if (is_ceo == 1) {
					var status_ceo = "Approved" + " | " + formatTanggal(date_ceo) + " | " + pic_ceo;
				} else {
					var status_ceo = "Rejected" + " | " + formatTanggal(date_ceo) + " | " + pic_ceo;
				}

				let html = '<div class="timeline">';

				html += `<div class="timeline-item">
					<h2>${status_disposisi}<h2>
				</div>`;

				html += `<div class="timeline-item">
					<div class="timeline-icon bg-primary"></div>
					<div class="timeline-content">
					<h6>Admin</h6>
					<p>Status : ${status_admin}</p>
					</div>
				</div>`;

				html += `<div class="timeline-item">
					<div class="timeline-icon bg-danger"></div>
					<div class="timeline-content">
					<h6>Finance</h6>
					<p>Status : ${status_finance}</p>
					</div>
				</div>`;

				html += `<div class="timeline-item">
					<div class="timeline-icon bg-warning"></div>
					<div class="timeline-content">
					<h6>Manager Finance</h6>
					<p>Status : ${status_mgr_fin}</p>
					</div>
				</div>`;

				html += `<div class="timeline-item">
					<div class="timeline-icon bg-info"></div>
					<div class="timeline-content">
					<h6>CFO</h6>
					<p>Status : ${status_cfo}</p>
					</div>
				</div>`;

				html += `<div class="timeline-item">
					<div class="timeline-icon bg-success"></div>
					<div class="timeline-content">
					<h6>CEO</h6>
					<p>Status : ${status_ceo}</p>
					</div>
				</div>`;

				// Tambah item lain sesuai parts

				html += '</div>';

				$('#historyContent').html(html);
				$('#historyApproval').modal('show');
			});

			function formatTanggal(datetimeStr) {
				// Pisahkan tanggal dan jam
				const [datePart, timePart] = datetimeStr.split(' ');

				// Pisahkan tahun, bulan, hari
				const [year, month, day] = datePart.split('-');

				// Susun ulang
				return `${day}-${month}-${year} ${timePart}`;
			}
		});
	</script>
</body>

</html>
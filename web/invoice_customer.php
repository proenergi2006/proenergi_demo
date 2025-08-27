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
$sesrole = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$linkEx1 = BASE_URL_CLIENT . '/report/invoice-exp.php';

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
				<h1>Invoice</h1>
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
						<?php if ($sesrole == '25') : ?>
							<div class="col-sm-3">
								<select name="cabang" id="cabang" class="form-control input-sm">
									<option value="">Semua Cabang</option>
									<?php foreach ($cabang as $key) : ?>
										<option <?= $key['id_master'] == '2' ? 'selected' : '' ?> value="<?= $key['id_master'] ?>"><?= ucwords($key['nama_cabang']) ?></option>
									<?php endforeach ?>
								</select>
							</div>
						<?php endif ?>
					</div>
					<div class="row">
						<div class="col-sm-1 col-sm-top">
							<button type="submit" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch"><i class="fa fa-search jarak-kanan"></i> Search</button>
						</div>
						<div class="col-sm-1 col-sm-top">
							<a href="<?php echo $linkEx1; ?>" class="btn btn-success btn-sm" target="_blank" id="expData1">Export Data</a>
						</div>
					</div>
				</form>
				<br>
				<div class="row">
					<div class="col-sm-12">
						<div class="box box-info">
							<div class="box-header with-border">
								<div class="row">
									<div class="col-sm-6">
										<?php if ($sesrole != '25') : ?>
											<a href="<?php echo BASE_URL_CLIENT . '/invoice_customer_add.php'; ?>" class="btn btn-primary">
												<i class="fa fa-plus jarak-kanan"></i>Add Data
											</a>
										<?php endif ?>
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
											<th class="text-center" width="150">Nama Customer</th>
											<th class="text-center" width="100">No Invoice</th>
											<th class="text-center" width="400">Tgl Invoice</th>
											<th class="text-center" width="150">Invoice Dikirim</th>
											<th class="text-center" width="150">Harga</th>
											<th class="text-center" width="100">Total Volume</th>
											<th class="text-center" width="150">Tagihan</th>
											<th class="text-center" width="180">Total Bayar</th>
											<th class="text-center" width="200">Aksi</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>

				<div class="modal fade" id="modalTglInvoice" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header bg-blue">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							</div>
							<div class="modal-body">
								<form action="<?php echo ACTION_CLIENT . '/invoice_customer.php'; ?>" id="gform" name="gform" method="post" class="form-horizontal" role="form" enctype="multipart/form-data">
									<input type="hidden" name="act" id="act" value="update_tanggal" readonly>
									<input type="hidden" name="id_invoice_encrypt" id="id_invoice_encrypt" readonly>
									<input type="hidden" name="refund" id="refund" readonly>
									<div class="form-group row">
										<div class="col-sm-6">
											<input type="text" name="tgl_invoice_dikirim" id="tgl_invoice_dikirim" class="form-control input-sm datepicker" autocomplete="off" placeholder="tanggal kirim invoice" required />
										</div>
										<div class="col-sm-6 col-sm-top">
											<button type="submit" class="btn btn-info btn-sm jarak-kanan" name="btnSimpan" id="btnSimpan" style="width:80px;">Simpan</button>
										</div>
									</div>
								</form>
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

	<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-blue">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Detail Pembayaran</h4>
				</div>
				<div class="modal-body">
					<div class="container-fluid">
						<table width="100%" border="1" style="border-collapse: collapse;">
							<thead>
								<tr class="text-center">
									<td width="50%" style="padding: 5px; border-spacing: 5px;">
										<b>Tanggal Bayar</b>
									</td>
									<td>
										<b>
											Jumlah Bayar
										</b>
									</td>
								</tr>
							</thead>
							<tbody id="tbody-pembayaran">
								<!-- <tr>
									<td class="text-center" style="padding: 5px; border-spacing: 5px;">
										1 Januari 1997
									</td>
									<td class="text-right" style="padding: 5px; border-spacing: 5px;">
										15.0000
									</td>
								</tr> -->
							</tbody>
						</table>
					</div>
					<br>
					<div class="container-fluid" id="row-bukti-potongan">
						<hr>
						<span>
							<b>
								Bukti Potongan Pembayaran
							</b>
						</span>
						<table width="100%" border="1" style="border-collapse: collapse;">
							<thead>
								<tr class="text-center">
									<td width="50%" style="padding: 5px; border-spacing: 5px;">
										<b>
											Kategori Potongan
										</b>
									</td>
									<td>
										<b>
											Jumlah Bayar
										</b>
									</td>
								</tr>
							</thead>
							<tbody id="tbody-potongan">
								<!-- <tr>
									<td class="text-center">
										PPH 23
									</td>
									<td class="text-right" style="padding: 5px; border-spacing: 5px;">
										15.0000
									</td>
								</tr> -->
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script>
		$(document).ready(function() {
			function formatUang(angka) {
				// Memastikan angka adalah number
				if (typeof angka !== 'number') {
					angka = Number(angka);
				}

				// Menggunakan toLocaleString untuk format angka
				return `${angka.toLocaleString('id-ID')}`;
			}

			function formatTanggalIndonesia(tanggal) {
				// Pastikan tanggal adalah objek Date
				if (!(tanggal instanceof Date)) {
					tanggal = new Date(tanggal);
				}

				// Mendapatkan komponen tanggal
				let day = String(tanggal.getDate()).padStart(2, '0'); // Menambah 0 di depan jika diperlukan
				let monthIndex = tanggal.getMonth(); // Mendapatkan bulan (0-11)
				let year = tanggal.getFullYear();

				// Nama bulan dalam bahasa Indonesia
				const bulan = [
					"Januari", "Februari", "Maret", "April", "Mei", "Juni",
					"Juli", "Agustus", "September", "Oktober", "November", "Desember"
				];

				// Mengembalikan format tanggal Indonesia
				return `${day} ${bulan[monthIndex]} ${year}`;
			}

			$("select#q2").select2({
				placeholder: "Status",
				allowClear: true
			});
			$("#table-grid").ajaxGrid({
				url: "./datatable/invoice_customer.php",
				data: {
					q1: $("#q1").val(),
					q2: $("#q2").val(),
					q3: $("#q3").val(),
					cabang: $("#cabang").val()
				},
			});
			$('#btnSearch').on('click', function() {
				$("#table-grid").ajaxGrid("draw", {
					data: {
						q1: $("#q1").val(),
						q2: $("#q2").val(),
						q3: $("#q3").val(),
						cabang: $("#cabang").val()
					}
				});
				return false;
			});
			$('#tableGridLength').on('change', function() {
				$("#table-grid").ajaxGrid("pageLen", $(this).val());
			});

			$('#table-grid tbody').on('click', '.detail_pembayaran', function(e) {
				var param = $(this).data("param");
				$.ajax({
					method: 'post',
					url: '<?php echo ACTION_CLIENT ?>/invoice_customer.php',
					data: {
						"param": param,
						"act": "detail_pembayaran",
					},
					dataType: 'json',
					success: function(result) {
						console.log(result)
						$('#tbody-pembayaran').empty();
						$('#tbody-potongan').empty();

						if (result.status == 100) {
							Swal.fire({
								title: "Ooppss",
								text: result.pesan,
								icon: "warning"
							})
						} else {
							$('#modalDetail').modal({
								show: true
							})

							var total_pembayaran = 0;
							// Looping untuk menambahkan data ke tbody
							$.each(result.data_invoice_pembayaran, function(index, item) {
								$('#tbody-pembayaran').append(
									`<tr>
										<td class="text-center" style="padding: 5px; border-spacing: 5px;">${formatTanggalIndonesia(item.tgl_bayar)}</td>
										<td class="text-right" style="padding: 5px; border-spacing: 5px;">${formatUang(item.jumlah_bayar)}</td>
									</tr>`
								);
								total_pembayaran += parseInt(item.jumlah_bayar);
							});
							$('#tbody-pembayaran').append(
								`<tr>
									<td class="text-center"><b>TOTAL</b></td>
									<td class="text-right" style="padding: 5px; border-spacing: 5px;"><b>${formatUang(total_pembayaran)}</b></td>
								</tr>`
							);

							if (result.data_invoice_potongan.length > 0) {
								$("#row-bukti-potongan").removeClass("hide");
								console.log(result.data_invoice_potongan)
								var total_potongan = 0;
								// Looping untuk menambahkan data ke tbody
								$.each(result.data_invoice_potongan, function(index, item) {
									$('#tbody-potongan').append(
										`<tr>
											<td class="text-center">${item.kategori}</td>
											<td class="text-right" style="padding: 5px; border-spacing: 5px;">${formatUang(item.nominal)}</td>
										</tr>`
									);
									total_potongan += parseInt(item.nominal);
								});
								$('#tbody-potongan').append(
									`<tr>
										<td class="text-center"><b>TOTAL</b></td>
										<td class="text-right" style="padding: 5px; border-spacing: 5px;"><b>${formatUang(total_potongan)}</b></td>
									</tr>`
								);
							} else {
								$("#row-bukti-potongan").addClass("hide");
							}

						}
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {
						alert("Error");
						// console.log(errorThrown)
					}
				})
			});

			$('#table-grid tbody').on('click', '[data-action="deleteGrid"]', function(e) {
				e.preventDefault();
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
						var param = $(this).data("param-idx");
						$.ajax({
							method: 'post',
							url: '<?php echo ACTION_CLIENT ?>/invoice_customer.php',
							data: {
								"param": param,
								"act": "hapus",
							},
							dataType: 'json',
							success: function(result) {
								// console.log(result)
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
				});
				// if (confirm("Apakah anda yakin ?")) {
				// 	var param = $(this).data("param-idx");
				// 	var handler = function(data) {
				// 		if (data.error == "") {
				// 			$(".alert").slideUp();
				// 			$("#table-grid").ajaxGrid("draw");
				// 		} else {
				// 			$(".alert").slideUp();
				// 			var a = $(".alert > .box-tools");
				// 			a.next().remove();
				// 			a.after("<p>" + data.error + "</p>");
				// 			$(".alert").slideDown();
				// 		}
				// 	};
				// 	$.post("./action/invoice_customer.php", {
				// 		param: param,
				// 		act: 'hapus'
				// 	}, handler, "json");
				// }
			});

			$('#table-grid').on('click', '.tgl_invoice', function() {
				var id_invoice = $(this).attr('data-id');
				var refund = $(this).attr('data-refund');
				$('#id_invoice_encrypt').val(id_invoice);
				$('#refund').val(refund);
				// alert(id_invoice);
				$('#modalTglInvoice').modal({
					show: true
				})
			});

			$('#expData1').on('click', function() {
				$(this).prop("href", $("#uriExp").val());
			});
		});
	</script>
</body>

</html>
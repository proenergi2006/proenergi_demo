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
$idk 	= isset($enk["idk"]) ? htmlspecialchars($enk["idk"], ENT_QUOTES) : '';
$sesid 	= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);
$seswil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$sesgroup = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);


$sql = "SELECT a.*, b.kode_pelanggan, b.nama_customer FROM pro_po_customer a JOIN pro_customer b ON a.id_customer=b.id_customer WHERE id_poc='" . $idk . "'";
$rsm = $con->getRecord($sql);

$total_po = $rsm['harga_poc'] * $rsm['volume_poc'];
?>

<style>
	.file-warning {
		font-size: 12px;
		color: #d9534f;
		margin-top: 4px;
	}

	.file-preview-list {
		margin-top: 8px;
		list-style-type: none;
		padding-left: 0;
	}

	.file-preview-list li {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 4px 8px;
		background: #f9f9f9;
		border: 1px solid #ddd;
		margin-bottom: 5px;
		border-radius: 3px;
		font-size: 13px;
	}

	.file-remove-btn {
		background: #d9534f;
		color: #fff;
		border: none;
		padding: 2px 8px;
		cursor: pointer;
		font-size: 12px;
		border-radius: 3px;
	}
</style>

<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("formatNumber", "jqueryUI"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory . "/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
		<aside class="right-side">
			<section class="content-header">
				<h1><?php echo $section . " Unblock Customer"; ?></h1>
			</section>
			<section class="content">

				<?php $flash->display(); ?>
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
					</div>
					<div class="box-body">
						<form action="<?php echo ACTION_CLIENT . '/form-unblock.php'; ?>" id="gform" name="gform" class="form-horizontal" method="post" role="form" enctype="multipart/form-data">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">Nama Customer</label>
										<div class="col-md-8">
											<input type="hidden" name="id_cust" id="id_cust" value="<?= paramEncrypt($rsm['id_customer']) ?>" readonly />
											<input type="hidden" name="idk" id="idk" value="<?= paramEncrypt($idk) ?>" readonly />
											<input type="text" name="customer" id="customer" class="form-control" value="<?= $rsm['nama_customer'] ?>" readonly />
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-5">Nomor POC</label>
										<div class="col-md-7">
											<input type="text" name="nomor_poc" id="nomor_poc" class="form-control text-left" value="<?= $rsm['nomor_poc'] ?>" readonly>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">Jumlah CL Temporary</label>
										<div class="col-md-6">
											<div class="input-group">
												<span class="input-group-addon">Rp</span>
												<input type="text" name="cl_temp" id="cl_temp" class="form-control text-right hitung" value="<?= number_format($total_po) ?>" readonly>
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-5">Jumlah TOP Temporary</label>
										<div class="col-md-4">
											<input type="text" name="top_temp" id="top_temp" class="form-control text-right" value="0" disabled>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-2">Keterangan *</label>
										<div class="col-md-10">
											<textarea name="keterangan" id="keterangan" class="form-control" required></textarea>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">Lampiran</label>
										<div class="col-md-8">
											<input type="file" id="lampiran" class="form-control" multiple accept=".pdf,image/*" name="lampiran[]">
											<div class="file-warning">* Hanya file gambar dan PDF, maksimal 2MB per file</div>
											<ul id="file-preview" class="file-preview-list"></ul>
										</div>
									</div>
								</div>
							</div>

							<hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

							<div style="margin-bottom:15px;">
								<button type="button" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">
									<i class="fa fa-save jarak-kanan"></i> Simpan</button>
								<button type="button" class="btn btn-default jarak-kanan" onClick="history.back()" style="min-width:90px;">
									<i class="fa fa-reply jarak-kanan"></i> Kembali</button>
							</div>
							<p><small>* Wajib Diisi</small></p>
						</form>
					</div>
				</div>
				<div id="selectedValues" style="margin-top: 20px;"></div>

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

	<style type="text/css">
		.table>tr>td {
			font-size: 12px;
			padding: 5px;
		}
	</style>

	<script>
		$(document).ready(function() {

			document.getElementById("btnSbmt").addEventListener("click", function(e) {
				Swal.fire({
					title: 'Konfirmasi Simpan',
					text: "Anda yakin ingin menyimpan data ini?",
					icon: 'question',
					showCancelButton: true,
					confirmButtonText: 'Ya, Simpan',
					cancelButtonText: 'Batal',
				}).then((result) => {
					if (result.isConfirmed) {
						$("#loading_modal").modal({
							keyboard: false,
							backdrop: 'static'
						});
						document.getElementById("gform").submit(); // Submit form jika dikonfirmasi
					}
				});
			});

			function formatNumber(value) {
				const formatter = new Intl.NumberFormat('id-ID', { // Using 'id-ID' for Indonesian locale (you can change it to your desired locale)
					// style: 'decimal',
					// maximumFractionDigits: 2,
					// minimumFractionDigits: 2
				});

				return formatter.format(value);
			}

			$(".hitung").number(true, 0, ".", ",");

			const inputFile = document.getElementById('lampiran');
			const filePreview = document.getElementById('file-preview');
			let selectedFiles = [];

			function renderPreview() {
				filePreview.innerHTML = '';
				selectedFiles.forEach((file, index) => {
					const li = document.createElement('li');

					const sizeKB = Math.round(file.size / 1024);
					const fileName = `${file.name} (${sizeKB} KB${file.size > 2 * 1024 * 1024 ? ' ⚠️' : ''})`;

					const nameSpan = document.createElement('span');
					nameSpan.textContent = fileName;

					const deleteBtn = document.createElement('button');
					deleteBtn.textContent = 'Hapus';
					deleteBtn.type = 'button';
					deleteBtn.className = 'file-remove-btn';

					// ✅ Tambahkan event listener ke tombol hapus
					deleteBtn.addEventListener('click', () => {
						selectedFiles.splice(index, 1);
						renderPreview();
					});

					li.appendChild(nameSpan);
					li.appendChild(deleteBtn);
					filePreview.appendChild(li);
				});

				updateInputFiles();
			}


			function updateInputFiles() {
				const dataTransfer = new DataTransfer();
				selectedFiles.forEach(file => dataTransfer.items.add(file));
				inputFile.files = dataTransfer.files;
			}

			inputFile.addEventListener('change', function() {
				selectedFiles = Array.from(inputFile.files);
				renderPreview();
			});
		});
	</script>
</body>

</html>
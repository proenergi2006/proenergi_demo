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

$section 	= "PO Suplier Receive";
$idnya01 	= ($enk["idr"] ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '');
$idnya02 	= ($enk["idnya02"] ? htmlspecialchars($enk["idnya02"], ENT_QUOTES) : '');

if (!$idnya02) {
	$sql = "
			select a.*, b.jenis_produk, b.merk_dagang, d.nama_vendor, e.nama_terminal, e.tanki_terminal, e.lokasi_terminal 
			from new_pro_inventory_vendor_po a 
			join pro_master_produk b on a.id_produk = b.id_master 
			join pro_master_vendor d on a.id_vendor = d.id_master 
			join pro_master_terminal e on a.id_terminal = e.id_master 
			where a.id_master = '" . $idnya01 . "'
		";
	$rsm 	= $con->getRecord($sql);
	$dt1 	= date("d/m/Y", strtotime($rsm['tanggal_inven']));
	$dt8 	= ($rsm['harga_tebus'] ? 'Rp. ' . number_format($rsm['harga_tebus'], 0, ',', '.') : '');
	$dt10 	= ($rsm['volume_po'] ? number_format($rsm['volume_po'], 0, ',', '.') . ' Liter' : '');

	$action 		= "add";
	$tgl_terima 	= "";
	$harga_tebus 	= $rsm['harga_tebus'];
	$volume_terima 	= $rsm['volume_po'];
	$volume_bol 	= $rsm['volume_po'];
	$nama_pic 		= "";

	//Buka apabila ingin penomoran otomatis
	//get vendor
	// $data = http_build_query([
	// 	'fields' => 'id,receiveNumber,no',
	// 	'sp.sort' => 'id|desc',
	// 	'sp.pageSizefields' => 1,
	// ]);

	// $urlnya = 'https://zeus.accurate.id/accurate/api/receive-item/list.do?' . $data;

	// $result = curl_get($urlnya);

	// if ($result['s'] == true) {
	// 	$no_terima = $result['d'][0]['receiveNumber'];
	// } else {
	// 	echo "get list receiveNumber accurate not found";
	// }
	$no_terima = '';
} else {
	$sql = "
			select a.nomor_po, a.tanggal_inven, a.harga_tebus, a.volume_po, a.id_terminal, b.jenis_produk, b.merk_dagang, 
			d.nama_vendor, e.nama_terminal, e.tanki_terminal, e.lokasi_terminal, 
			a1.tgl_terima, a1.harga_tebus as harga_tebus_receive, a1.volume_bol, a1.volume_terima, a1.nama_pic, 
			a1.file_upload, a1.file_upload_ori 
			from new_pro_inventory_vendor_po a 
			join new_pro_inventory_vendor_po_receive a1 on a.id_master = a1.id_po_supplier 
			join pro_master_produk b on a.id_produk = b.id_master 
			join pro_master_vendor d on a.id_vendor = d.id_master 
			join pro_master_terminal e on a.id_terminal = e.id_master 
			where a1.id_po_supplier = '" . $idnya01 . "' and a1.id_po_receive = '" . $idnya02 . "'
		";
	$rsm 	= $con->getRecord($sql);
	$dt1 	= date("d/m/Y", strtotime($rsm['tanggal_inven']));
	$dt8 	= ($rsm['harga_tebus'] ? 'Rp. ' . number_format($rsm['harga_tebus'], 0, ',', '.') : '');
	$dt10 	= ($rsm['volume_po'] ? number_format($rsm['volume_po'], 0, ',', '.') . ' Liter' : '');

	$action 		= "update";
	$tgl_terima 	= date("d/m/Y", strtotime($rsm['tgl_terima']));
	$harga_tebus 	= ($rsm['harga_tebus_receive'] ? $rsm['harga_tebus_receive'] : '');
	$volume_bol 	= ($rsm['volume_bol'] ? $rsm['volume_bol'] : '');

	$volume_terima 	= ($rsm['volume_terima'] ? $rsm['volume_terima'] : '');
	$nama_pic 		= $rsm['nama_pic'];

	$dataIcons 	= "";
	$pathnya 	= $public_base_directory . '/files/uploaded_user/lampiran';
	if ($rsm['file_upload_ori'] && file_exists($pathnya . '/' . $rsm['file_upload'])) {
		$labelFile 	= 'Ubah File';
		$linkPt 	= ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=108900&ktg=" . $rsm['file_upload'] . "&file=" . $rsm['file_upload_ori']);
		$dataIcons 	= '
			<div style="margin:6px 0px 10px;">
				<a href="' . $linkPt . '" target="_blank" title="download file">
				<i class="fas fa-paperclip jarak-kanan"></i> Download File</a>
			</div>';
	}
}

//GET DETAIL PO ACCURATE
$query = http_build_query([
	'id' => $rsm['id_accurate'],
]);

$urlnya = 'https://zeus.accurate.id/accurate/api/purchase-order/detail.do?' . $query;

$result = curl_get($urlnya);

$kode_item_accurate = $result['d']['detailItem'][0]['item']['no'];
$unitPrice = $result['d']['detailItem'][0]['unitPrice'];

$sql = "select * FROM pro_master_cabang WHERE id_master='" . $rsm['id_cabang'] . "'";
$row = $con->getRecord($sql);

?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("formatNumber", "jqueryUI", "ckeditor"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory . "/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
		<aside class="right-side">
			<section class="content-header">
				<h1><?php echo $section; ?></h1>
			</section>
			<section class="content">

				<?php $flash->display(); ?>
				<table class="table no-border tablea-bordered" style="width:100%;">
					<tr>
						<td class="text-left" width="150">Nomor PO</td>
						<td class="text-center" width="20">:</td>
						<td class="text-left" width=""><?php echo $rsm['nomor_po']; ?></td>
					</tr>
					<tr>
						<td class="text-left">Tanggal PO</td>
						<td class="text-center">:</td>
						<td class="text-left"><?php echo $dt1; ?></td>
					</tr>
					<tr>
						<td class="text-left">Produk</td>
						<td class="text-center">:</td>
						<td class="text-left"><?php echo $rsm['jenis_produk'] . ' - ' . $rsm['merk_dagang']; ?></td>
					</tr>
					<tr>
						<td class="text-left">Terminal / Depot</td>
						<td class="text-center">:</td>
						<td class="text-left">
							<?php
							$terminal1 	= $rsm['nama_terminal'];
							$terminal2 	= ($rsm['tanki_terminal'] ? ' - ' . $rsm['tanki_terminal'] : '');
							$terminal3 	= ($rsm['lokasi_terminal'] ? ', ' . $rsm['lokasi_terminal'] : '');
							echo $terminal1 . $terminal2 . $terminal3;
							?>
						</td>
					</tr>
					<tr>
						<td class="text-left">Vendor</td>
						<td class="text-center">:</td>
						<td class="text-left"><?php echo $rsm['nama_vendor']; ?></td>
					</tr>
					<tr>
						<td class="text-left">Volume PO</td>
						<td class="text-center">:</td>
						<td class="text-left"><?php echo $dt10; ?></td>
					</tr>
					<tr>
						<td class="text-left">Harga Dasar</td>
						<td class="text-center">:</td>
						<td class="text-left"><?php echo $dt8; ?></td>
					</tr>
				</table>

				<hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

				<form action="<?php echo ACTION_CLIENT . '/vendor-po-new-terima.php'; ?>" id="gform" name="gform" method="post" class="form-horizontal" role="form" enctype="multipart/form-data">
					<div class="row">
						<div class="col-md-8">
							<div class="form-group form-group-sm">
								<label class="control-label col-md-3">Tanggal Terima *</label>
								<div class="col-md-4">
									<input type="text" name="tgl_terima" id="tgl_terima" class="form-control datepicker" required data-rule-dateNL="1" value="<?php echo $tgl_terima; ?> " autocomplete="off" />
								</div>
							</div>
						</div>
					</div>

					<!-- Parameter yang dibutuhkan untuk Accurate -->
					<div class="row">
						<div class="col-md-8">
							<div class="form-group form-group-sm">
								<label class="control-label col-md-3">Nomor Terima *</label>
								<div class="col-md-4">
									<input type="text" name="no_terima" id="no_terima" class="form-control" value="<?php echo $no_terima; ?>" required />
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-8">
							<div class="form-group form-group-sm">
								<label class="control-label col-md-3">Harga Tebus *</label>
								<div class="col-md-4">
									<div class="input-group">
										<span class="input-group-addon">Rp.</span>
										<input type="text" id="harga_tebus" name="harga_tebus" class="form-control hitung" required value="<?php echo $harga_tebus; ?>" readonly />
									</div>
								</div>
							</div>
						</div>
					</div>


					<div class="row">
						<div class="col-md-8">
							<div class="form-group form-group-sm">
								<label class="control-label col-md-3">Volume BL *</label>
								<div class="col-md-4">
									<div class="input-group">
										<input type="text" id="volume_bol" name="volume_bol" class="form-control hitung" value="<?php echo $volume_bol; ?>" required />
										<span class="input-group-addon">Liter</span>
									</div>
								</div>
							</div>
						</div>
					</div>


					<div class="row">
						<div class="col-md-8">
							<div class="form-group form-group-sm">
								<label class="control-label col-md-3">Volume Terima *</label>
								<div class="col-md-4">
									<div class="input-group">
										<input type="text" id="volume_terima" name="volume_terima" class="form-control hitung" value="<?php echo $volume_terima; ?>" required />
										<span class="input-group-addon">Liter</span>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Parameter untuk ke Accurate -->
					<div class="row">
						<div class="col-md-8">
							<div class="form-group form-group-sm">
								<input type="hidden" name="gudang" value="<?= paramEncrypt($row['inisial_cabang']) ?>" readonly>
								<input type="hidden" name="kode_item_accurate" value="<?= paramEncrypt($kode_item_accurate) ?>" readonly>
								<input type="hidden" name="unit_price" value="<?= paramEncrypt($unitPrice) ?>" readonly>
								<input type="hidden" name="nomor_po" value="<?= paramEncrypt($rsm['nomor_po']) ?>" readonly>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-8">
							<div class="form-group form-group-sm">
								<label class="control-label col-md-3">Nama PIC *</label>
								<div class="col-md-8">
									<input type="text" name="nama_pic" id="nama_pic" class="form-control" value="<?php echo $nama_pic; ?>" required />
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-8">
							<div class="form-group form-group-sm">
								<label class="control-label col-md-3">File Upload *</label>
								<div class="col-md-8">
									<?php
									echo '
								' . $dataIcons . '
								<div class="rowuploadnya">
									<div class="simple-fileupload">
										<input type="file" name="file_template" id="file_template" class="form-inputfile" />
										<label for="file_template" class="label-inputfile">
											<div class="input-group input-group-sm">
												<div class="input-group-addon btn-primary"><i class="fa fa-upload"></i></div>
												<input type="text" class="form-control" placeholder="' . $labelFile . '" readonly />
											</div>
										</label>
									</div>
								</div>';
									?>
									<p style="font-size:12px;" class="help-block">* Max size 2Mb | .jpg, .png, .rar, .pdf</p>
								</div>
							</div>
						</div>
					</div>

					<hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

					<div style="margin-bottom:15px;">
						<input type="hidden" name="act" value="<?php echo $action; ?>" />
						<input type="hidden" name="idnya01" value="<?php echo $idnya01; ?>" />
						<input type="hidden" name="idnya02" value="<?php echo $idnya02; ?>" />
						<input type="hidden" name="id_terminal_po" value="<?php echo $rsm['id_terminal']; ?>" />
						<input type="hidden" name="harga_tebus_po" value="<?php echo $rsm['harga_tebus']; ?>" />
						<?php if (!$rsm['is_selesai']) { ?>
							<?php if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']), array("365"))) { ?>
								<button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">
									<i class="fa fa-save jarak-kanan"></i> Simpan
								</button>
							<?php } ?>
						<?php } ?>
						<a href="<?php echo BASE_URL_CLIENT . "/vendor-po-new-terima.php?" . paramEncrypt('idr=' . $idnya01); ?>" class="btn btn-default" style="min-width:90px;">
							<i class="fa fa-reply jarak-kanan"></i> Kembali
						</a>
					</div>

					<p><small>* Wajib Diisi</small></p>
				</form>

				<?php $con->close(); ?>
			</section>
			<?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
		</aside>
	</div>

	<script>
		$(document).ready(function() {
			$(".hitung").number(true, 0, ".", ",");



			var formValidasiCfg = {
				submitHandler: function(form) {
					// var volume_terima = parseInt($("#volume_terima").val().replace(/,/g, ''), 10);
					// var volume_po = parseInt("<?php echo $dt10; ?>".replace(/,/g, '').replace(/\./g, ''), 10);

					// console.log('Volume Terima:', volume_terima);
					// console.log('Volume PO:', volume_po);

					if ($("#cekkolnup").is(":checked") && $("#nup_fee").val() == "") {
						swal.fire({
							icon: "warning",
							width: '350px',
							allowOutsideClick: false,
							html: '<p style="font-size:14px; font-family:arial;">Kolom [Ongkos Angkut] pada tabel rincian harga belum diisi</p>'
						});

						//} else if ($("input[name='act']").val() == "add" && $("#file_template").get(0).files.length === 0) {
						//swal.fire({
						//	icon: "warning",
						//	width: '350px',
						//allowOutsideClick: false,
						//html: '<p style="font-size:14px; font-family:arial;">Kolom [File Upload] Belum Dipilih...</p>'
						//});

					} else {
						$("body").addClass("loading");
						var paramnya = $(form).serializeArray();
						paramnya.push({
							name: 'formnya',
							value: 'vendor-po-new-terima-add'
						});
						$.ajax({
							type: 'POST',
							url: base_url + "/web/action/cek-validasi.php",
							data: paramnya,
							cache: false,
							dataType: 'json',
							success: function(data) {
								if (!data.hasil) {
									$("body").removeClass("loading");
									swal.fire({
										icon: "warning",
										width: '350px',
										allowOutsideClick: false,
										html: '<p style="font-size:14px; font-family:arial;">' + data.pesan + '</p>'
									});
								} else {
									form.submit();
								}
							}
						});
					}
				}
			};
			$("form#gform").validate($.extend(true, {}, config.validation, formValidasiCfg));

			$("#tb_vol_terima").on("click", ".add_volume", function() {
				var tabel = $("#tb_vol_terima");
				var arrId = tabel.find("tbody > tr").map(function() {
					return parseFloat($(this).data("id")) || 0;
				}).toArray();
				var rwNom = Math.max.apply(Math, arrId);
				var newId = (rwNom == 0) ? 1 : (rwNom + 1);

				var isiHtml =
					'<tr data-id="' + newId + '">' +
					'<td class="text-center"><span class="frmnodasar" data-row-count="' + newId + '"></span></td>' +
					'<td class="text-left">' +
					'<input type="text" id="tgl_terima' + newId + '" name="tgl_terima[' + newId + ']" class="form-control tgl_terima" />' +
					'</td>' +
					'<td class="text-left">' +
					'<input type="text" id="pic' + newId + '" name="pic[' + newId + ']" class="form-control pic" />' +
					'</td>' +
					'<td class="text-left">' +
					'<input type="text" id="vol_terima' + newId + '" name="vol_terima[' + newId + ']" class="form-control vol_terima text-right" />' +
					'</td>' +
					'<td class="text-left">' +
					'<div class="rowuploadnya">' +
					'<div style="width:45px; float:left;">&nbsp;</div>' +
					'<div class="simple-fileupload" style="margin-left:45px;">' +
					'<input type="file" name="file_template[' + newId + ']" id="file_template' + newId + '" class="form-inputfile" />' +
					'<label for="file_template' + newId + '" class="label-inputfile">' +
					'<div class="input-group input-group-sm">' +
					'<div class="input-group-addon btn-primary"><i class="fa fa-upload"></i></div>' +
					'<input type="text" class="form-control" placeholder="Unggah File" readonly />' +
					'</div>' +
					'</label>' +
					'</div>' +
					'</div>' +
					'</td>' +
					'<td class="text-center">' +
					'<a class="btn btn-danger btn-sm del_volume"><span class="fa fa-trash"></span></a>' +
					'</td>' +
					'</tr>';
				if (rwNom == 0) {
					tabel.find('tbody').html(isiHtml);
				} else {
					tabel.find('tbody > tr:last').after(isiHtml);
				}

				$("#tgl_terima" + newId).datepicker(objSettingDate);
				$("#vol_terima" + newId).number(true, 0, ".", ",");
				tabel.find("span.frmnodasar").each(function(i, v) {
					$(v).text(i + 1);
				});
			}).on("click", ".del_volume", function() {
				var tabel = $("#tb_vol_terima");
				var jTbl = tabel.find('tbody > tr').length;
				if (jTbl > 1) {
					var cRow = $(this).closest('tr');
					cRow.remove();
					tabel.find("span.frmnodasar").each(function(i, v) {
						$(v).text(i + 1);
					});
					calculate_volterima();
				}
			}).on("keyup blur", ".volume_terima", function() {
				calculate_volterima();
			});

			function calculate_volterima() {
				let grandTotal = 0;
				$(".volume_terima").each(function(i, v) {
					grandTotal = grandTotal + ($(v).val() * 1);
				});
				$('#vol_total').val(grandTotal);
				$('#vol_total_cek').val(grandTotal);
				$("#vol_total_cek").number(true, 0, ".", ",");
			}

		});
	</script>
</body>

</html>
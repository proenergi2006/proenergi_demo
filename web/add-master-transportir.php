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

if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 7) {
	$queries = "select * from pro_master_cabang where id_master = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "';";
	$rs  = $con->getRecord($queries);
}

if (isset($enk['idr']) && $enk['idr'] !== '') {
	$action 	= "update";
	$section 	= "Edit Data";
	$idr = isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
	$sql = "select * from pro_master_transportir where id_master = '" . $idr . "';";
	$rsm = $con->getRecord($sql);
	$attention = json_decode($rsm['att_suplier'], true);
	$chk1 = ($rsm['is_active']) ? "checked" : "";
	$chk2 = ($rsm['is_fleet']) ? "checked" : "";
} else {
	$idr = 0;
	$rsm = null;
	$attention = [];
	$action 	= "add";
	$section 	= "Tambah Data";
	$chk1		= "checked";
	$chk2		= "";
	$dokumen	= array(1);
}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("ckeditor", "jqueryUI"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory . "/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
		<aside class="right-side">
			<section class="content-header">
				<h1><?php echo $section . " Transportir"; ?></h1>
			</section>
			<section class="content">

				<?php $flash->display(); ?>
				<div class="row">
					<div class="col-sm-12">
						<div class="box box-info">
							<div class="box-header with-border">
								<h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
							</div>
							<div class="box-body">
								<form action="<?php echo ACTION_CLIENT . '/master-transportir.php'; ?>" id="gform" name="gform" method="post" enctype="multipart/form-data">
									<div class="form-group row">
										<div class="col-sm-6">
											<label>Nama Perusahaan *</label>
											<input type="text" id="nama_sup" name="nama_sup" class="form-control validate[required]" value="<?php echo $rsm['nama_suplier'] ?? null; ?>" />
										</div>
										<div class="col-sm-6 col-sm-top">
											<label>Singkatan *</label>
											<input type="text" id="nama" name="nama" class="form-control validate[required]" value="<?php echo $rsm['nama_transportir'] ?? null; ?>" />
										</div>
									</div>
									<div class="form-group row">
										<div class="col-sm-6">
											<label>Kepemilikan *</label>
											<select id="owner_suplier" name="owner_suplier" class="form-control vaidate[required] select2">
												<option></option>
												<option value="1" <?php echo ($rsm && $rsm['owner_suplier'] == 1) ? 'selected' : ''; ?>>Milik Sendiri</option>
												<option value="2" <?php echo ($rsm && $rsm['owner_suplier'] == 2) ? 'selected' : ''; ?>>Third Party</option>
											</select>
										</div>
										<div class="col-sm-6">
											<label>Terms *</label>
											<input type="text" id="terms_sup" name="terms_sup" class="form-control validate[required]" value="<?php echo $rsm['terms_suplier'] ?? null; ?>" />
										</div>
									</div>

									<div class="form-group row">
										<div class="col-sm-6">
											<label>Lokasi *</label>
											<?php if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 7) { ?>
												<input type="text" id="lok_sup" name="lok_sup" class="form-control validate[required]" value="<?php echo $rs['nama_cabang']; ?>" readonly="" />
											<?php } else { ?>
												<select id="lok_sup" name="lok_sup" class="form-control validate[required] select2">
													<option></option>
													<?php $con->fill_select("nama_cabang", "nama_cabang", "pro_master_cabang", $rsm['lokasi_suplier'], "where id_master != 1", "nama_cabang", false); ?>
												</select>
											<?php } ?>
										</div>
										<div class="col-sm-6">
											<label>Alamat *</label>
											<input type="text" id="almt_sup" name="almt_sup" class="form-control validate[required]" value="<?php echo $rsm['alamat_suplier'] ?? null; ?>" />
										</div>
									</div>
									<div class="form-group row">
										<div class="col-sm-6">
											<label>Telepon *</label>
											<input type="text" id="telp_sup" name="telp_sup" class="form-control validate[required]" value="<?php echo $rsm['telp_suplier']; ?>" />
										</div>
										<div class="col-sm-6 col-sm-top">
											<label>Fax *</label>
											<input type="text" id="fax_sup" name="fax_sup" class="form-control validate[required]" value="<?php echo $rsm['fax_suplier']; ?>" />
										</div>
									</div>
									<div class="form-group row">
										<div class="col-sm-6">
											<label>Angkutan Kirim *</label>
											<select id="tipe" name="tipe" class="form-control vaidate[required] select2">
												<option></option>
												<option value="1" <?php echo ($rsm && $rsm['tipe_angkutan'] == 1) ? 'selected' : ''; ?>>Truck</option>
												<option value="2" <?php echo ($rsm && $rsm['tipe_angkutan'] == 2) ? 'selected' : ''; ?>>Kapal</option>
												<option value="3" <?php echo ($rsm && $rsm['tipe_angkutan'] == 3) ? 'selected' : ''; ?>>Keduanya</option>
											</select>
										</div>
										<div class="col-sm-3 col-sm-top">
											<div class="checkbox">
												<label class="rtl">
													<input type="checkbox" name="fleet" id="fleet" value="1" class="form-control" <?php echo $chk2; ?> /> Fleet
												</label>
											</div>
										</div>
										<div class="col-sm-3 col-sm-top">
											<div class="checkbox">
												<label class="rtl">
													<input type="checkbox" name="active" id="active" value="1" class="form-control" <?php echo $chk1; ?> /> Active
												</label>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-12">
											<div class="table-responsive">
												<table class="table table-bordered table-attention" style="margin-top:10px;">
													<thead>
														<tr>
															<th class="text-center" width="25%">Attention</th>
															<th class="text-center" width="23%">Posisi</th>
															<th class="text-center" width="22%">No. HP</th>
															<th class="text-center" width="25%">Email</th>
															<th class="text-center" width="5%">
																<button class="btn btn-action btn-primary addRow" type="button"><i class="fa fa-plus"></i></button>
															</th>
														</tr>
													</thead>
													<tbody>
														<?php
														if ($attention && count($attention) == 0) {
															echo '<tr><td colspan="5" class="text-center">Tidak ada Attention</td></tr>';
														} else {
															$d = 0;
															foreach ($attention as $dat3) {
																$d++;
																$att1 = $dat3['nama'];
																$att2 = $dat3['posisi'];
																$att3 = $dat3['hp'];
																$att4 = $dat3['email'];
														?>
																<tr>
																	<td><input type="text" name="att1[]" id="<?php echo 'att1_' . $d; ?>" class="form-control" value="<?php echo $att1; ?>" /></td>
																	<td><input type="text" name="att2[]" id="<?php echo 'att2_' . $d; ?>" class="form-control" value="<?php echo $att2; ?>" /></td>
																	<td><input type="text" name="att3[]" id="<?php echo 'att3_' . $d; ?>" class="form-control" value="<?php echo $att3; ?>" /></td>
																	<td><input type="text" name="att4[]" id="<?php echo 'att4_' . $d; ?>" class="form-control" value="<?php echo $att4; ?>" /></td>
																	<td class="text-center">
																		<span class="frmid" data-row-count="<?php echo $d; ?>"></span>
																		<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>
																	</td>
																</tr>
														<?php }
														} ?>
													</tbody>
												</table>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-sm-12">
											<div class="table-responsive">
												<table class="table table-bordered table-dokumen" style="margin-top:10px;">
													<thead>
														<tr>
															<th class="text-center" width="35%">Perizinan</th>
															<th class="text-center" width="15%">Masa Berlaku</th>
															<th class="text-center" width="45%">Lampiran</th>
															<th class="text-center" width="5%">
																<button class="btn btn-action btn-primary addRow" type="button"><i class="fa fa-plus"></i></button>
															</th>
														</tr>
													</thead>
													<tbody>
														<?php
														$cek1 = "select * from pro_master_transportir_detail where id_transportir = '" . $idr . "' order by id_td";
														$row1 = $con->getResult($cek1);
														if (count($row1) == 0) {
															echo '<tr><td colspan="4" class="text-center">Tidak ada dokumen</td></tr>';
														} else {
															$d = 0;
															foreach ($row1 as $dat1) {
																$d++;
																$idd 	= $dat1['id_td'];
																$linkAt = "";
																$textAt = "";
																$pathAt = $public_base_directory . '/files/uploaded_user/lampiran/' . $dat1['lampiran'];
																$nameAt = $dat1['lampiran_ori'];
																if ($dat1['lampiran'] && file_exists($pathAt)) {
																	$linkAt = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=2&ktg=sup_" . $idr . "_" . $idd . "_&file=" . $nameAt);
																	$textAt = '<a href="' . $linkAt . '"><i class="fa fa-paperclip jarak-kanan"></i>' . $nameAt . '</a>';
																}
														?>
																<tr>
																	<td><?php echo $dat1['dokumen']; ?></td>
																	<td><?php echo tgl_indo($dat1['masa_berlaku'], 'normal', 'db', '/'); ?></td>
																	<td><?php echo $textAt; ?></td>
																	<td class="text-center">
																		<input type="hidden" name="<?php echo 'doknya[' . $idd . ']'; ?>" value="1" />
																		<span class="frmid" data-row-count="<?php echo $d; ?>"></span>
																		<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>
																	</td>
																</tr>
														<?php }
														} ?>
													</tbody>
												</table>
											</div>
										</div>
									</div>
									<?php if (count($row1) > 0) {
										foreach ($row1 as $dat2) {
											echo '<input type="hidden" name="doksup[' . $dat2['id_td'] . ']" value="1" />';
										}
									} ?>

									<div class="form-group row">
										<div class="col-sm-8">
											<label>Catatan</label>
											<textarea name="catatan" id="catatan" class="form-control wysiwyg"><?php echo $rsm['catatan'] ?? null; ?></textarea>
										</div>
									</div>

									<div class="row">
										<div class="col-sm-12">
											<div class="pad bg-gray">
												<input type="hidden" name="act" value="<?php echo $action; ?>" />
												<input type="hidden" name="idr" value="<?php echo $idr; ?>" />
												<a href="<?php echo BASE_URL_CLIENT . "/master-transportir.php"; ?>" class="btn btn-default jarak-kanan">
													<i class="fa fa-reply jarak-kanan"></i> Kembali</a>
												<button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
											</div>
										</div>
									</div>
									<hr style="margin:5px 0" />
									<div class="row">
										<div class="col-sm-12"><small>* Wajib Diisi</small></div>
									</div>
								</form>
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
	<script>
		$(document).ready(function() {
			$(".wysiwyg").ckeditor();
			var objSettingDate = {
				dateFormat: 'dd/mm/yy',
				changeMonth: true,
				changeYear: true,
				yearRange: "c-80:c+10",
				dayNamesMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
				monthNamesShort: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
			};
			var objAttach = {
				onValidationComplete: function(form, status) {
					if (status == true) {
						$('#loading_modal').modal({
							backdrop: "static"
						});
						for (instance in CKEDITOR.instances) {
							CKEDITOR.instances[instance].updateElement();
						}
						form.validationEngine('detach');
						form.submit();
					}
				}
			};
			$("form#gform").validationEngine('attach', objAttach);



			$(".table-dokumen").on("click", "button.addRow", function() {
				$("form#gform").validationEngine('detach');
				var tabel = $(this).parents(".table-dokumen");
				var rwTbl = tabel.find('tbody > tr:last');
				var rwNom = parseInt(rwTbl.find("span.frmid").data('rowCount'));
				var newId = (isNaN(rwNom)) ? 1 : parseInt(rwNom + 1);

				var objTr = $("<tr>");
				var objTd1 = $("<td>", {
					class: "text-left"
				}).appendTo(objTr);
				var objTd2 = $("<td>", {
					class: "text-left"
				}).appendTo(objTr);
				var objTd3 = $("<td>", {
					class: "text-left"
				}).appendTo(objTr);
				var objTd4 = $("<td>", {
					class: "text-center"
				}).appendTo(objTr);
				objTd1.html('<input type="text" name="newdok1[' + newId + ']" id="newdok1_' + newId + '" class="form-control" autocomplete="off" />');
				objTd2.html('<input type="text" name="newdok2[' + newId + ']" id="newdok2_' + newId + '" class="form-control" autocomplete="off" />');
				objTd3.html('<input type="file" name="newdok3[' + newId + ']" id="newdok3_' + newId + '" class="validate[funcCall[fileCheck]]" autocomplete="off" />');
				objTd4.html('<span class="frmid" data-row-count="' + newId + '"></span><a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>');
				if (isNaN(rwNom)) {
					rwTbl.remove();
					rwTbl = $(".table-dokumen > tbody");
					rwTbl.append(objTr);
				} else {
					rwTbl.after(objTr);
				}
				$("#newdok2_" + newId).datepicker(objSettingDate);
				$("form#gform").validationEngine('attach', objAttach);
			});
			$(".table-dokumen").on("click", "a.hRow", function() {
				var tabel = $(this).parents(".table-dokumen");
				var jTbl = tabel.find("tr").length;
				if (jTbl > 1) {
					var cRow = $(this).closest('tr');
					cRow.remove();
				}
				if (jTbl == 2) {
					var nRow = $(".table-dokumen > tbody");
					nRow.append('<tr><td colspan="4" class="text-center">Tidak ada dokumen</td></tr>');
				}
			});

			$(".table-attention").on("click", "button.addRow", function() {
				var tabel = $(this).parents(".table-attention");
				var rwTbl = tabel.find('tbody > tr:last');
				var rwNom = parseInt(rwTbl.find("span.frmid").data('rowCount'));
				var newId = (isNaN(rwNom)) ? 1 : parseInt(rwNom + 1);

				var objTr = $("<tr>");
				var objTd1 = $("<td>", {
					class: "text-left"
				}).appendTo(objTr);
				var objTd2 = $("<td>", {
					class: "text-left"
				}).appendTo(objTr);
				var objTd3 = $("<td>", {
					class: "text-left"
				}).appendTo(objTr);
				var objTd4 = $("<td>", {
					class: "text-left"
				}).appendTo(objTr);
				var objTd5 = $("<td>", {
					class: "text-center"
				}).appendTo(objTr);
				objTd1.html('<input type="text" name="att1[]" id="att1_' + newId + '" class="form-control" autocomplete="off" />');
				objTd2.html('<input type="text" name="att2[]" id="att2_' + newId + '" class="form-control" autocomplete="off" />');
				objTd3.html('<input type="text" name="att3[]" id="att3_' + newId + '" class="form-control" autocomplete="off" />');
				objTd4.html('<input type="text" name="att4[]" id="att4_' + newId + '" class="form-control" autocomplete="off" />');
				objTd5.html('<span class="frmid" data-row-count="' + newId + '"></span><a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>');
				if (isNaN(rwNom)) {
					rwTbl.remove();
					rwTbl = $(".table-attention > tbody");
					rwTbl.append(objTr);
				} else {
					rwTbl.after(objTr);
				}
			});
			$(".table-attention").on("click", "a.hRow", function() {
				var tabel = $(this).parents(".table-attention");
				var jTbl = tabel.find("tr").length;
				if (jTbl > 1) {
					var cRow = $(this).closest('tr');
					cRow.remove();
				}
				if (jTbl == 2) {
					var nRow = $(".table-attention > tbody");
					nRow.append('<tr><td colspan="5" class="text-center">Tidak ada kompartemen</td></tr>');
				}
			});
		});
	</script>
</body>

</html>
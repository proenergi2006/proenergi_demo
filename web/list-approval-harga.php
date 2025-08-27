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
$link1 	= BASE_URL_CLIENT . '/master-approval-harga.php';
$cek = "select distinct periode_awal, periode_akhir, id_area, produk, note_jual from pro_master_harga_minyak where is_evaluated = 1 and is_approved = 0 order by periode_awal, id_area, produk ";
$rec = $con->getResult($cek);
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("formatUang", "jqueryUI", "ckeditor"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory . "/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
		<aside class="right-side">
			<section class="content-header">
				<h1>Persetujuan Harga Jual</h1>
			</section>
			<section class="content">

				<?php $flash->display(); ?>
				<div class="row">
					<div class="col-sm-12">
						<div class="box box-info">
							<div class="box-body">
								<form action="<?php echo ACTION_CLIENT . '/master-approval-harga.php'; ?>" id="gform" name="gform" method="post" class="form-validasi" role="form">
									<div class="table-responsive">
										<table class="table table-bordered" id="table-grid">
											<thead>
												<tr>
													<th class="text-center" width="25%">DETAIL HARGA JUAL</th>
													<th class="text-center" width="25%">KETERANGAN</th>
													<?php
													$ces = "select * from pro_master_pbbkb where id_master = 1 and is_active = 1";
													$res = $con->getResult($ces);
													$tmp = array();
													if (count($res) > 0) {
														foreach ($res as $data) {
															array_push($tmp, array($data['id_master'], $data['nilai_pbbkb']));
															echo '<th class="text-center">Nominal & Catatan </th>';
														}
													}
													?>
													<th class="text-center" width="5%"><input type="checkbox" name="cekAll" id="cekAll" value="1" /></th>
												</tr>
											</thead>
											<tbody>
												<?php
												$sql = "select a.periode_awal, a.periode_akhir, a.id_area, a.produk, a.is_approved, 
											a.is_evaluated, a.note_jual, a.nama_area, a.jenis_produk, a.merk_dagang";
												foreach ($tmp as $que) {
													$sql .= ", coalesce(sum(a.nm" . $que[0] . "), 0) as 'nm" . $que[0] . "'";
													$sql .= ", coalesce(sum(a.sm" . $que[0] . "), 0) as 'sm" . $que[0] . "'";
													$sql .= ", coalesce(sum(a.om" . $que[0] . "), 0) as 'om" . $que[0] . "'";
												}
												$sql .= " from (select a.periode_awal, a.periode_akhir, a.id_area, a.produk, a.is_approved, 
											a.is_evaluated, a.note_jual, b.nama_area, c.jenis_produk, c.merk_dagang";
												foreach ($tmp as $que) {
													$sql .= ", case when a.pajak = " . $que[0] . " then a.harga_normal end as 'nm" . $que[0] . "'";
													$sql .= ", case when a.pajak = " . $que[0] . " then a.harga_sm end as 'sm" . $que[0] . "'";
													$sql .= ", case when a.pajak = " . $que[0] . " then a.harga_om end as 'om" . $que[0] . "'";
												}
												$sql .= " from pro_master_harga_minyak a join pro_master_area b on a.id_area = b.id_master 
											join pro_master_produk c on a.produk = c.id_master) a where a.is_evaluated = 1 and a.is_approved = 0
											group by a.periode_awal, a.periode_akhir, a.id_area, a.produk, a.is_approved, a.is_evaluated, a.note_jual, a.nama_area, a.jenis_produk, a.merk_dagang
											order by a.periode_awal, a.id_area, a.produk";
												$nom = 0;
												$result = $con->getResult($sql);
												if (count($result) > 0) {
													foreach ($result as $index => $data) {
														$nom++;
														$idm = paramEncrypt($data['periode_awal'] . "#|#" . $data['periode_akhir'] . "#|#" . $data['id_area'] . "#|#" . $data['produk']);
														$periode = date("d/m/Y", strtotime($data['periode_awal'])) . ' - ' . date("d/m/Y", strtotime($data['periode_akhir']));
												?>
														<tr>
															<td class="text-left" rowspan="4">
																<p style="margin-bottom:3px"><b><?php echo $data['jenis_produk'] . ' - ' . $data['merk_dagang']; ?></b></p>
																<p style="margin-bottom:3px"><?php echo "Area " . $data['nama_area']; ?></p>
																<p style="margin-bottom:0px"><?php echo $periode; ?></p>
															</td>
															<td class="text-left thin-border-bottom">Harga Jual</td>
															<?php
															foreach ($tmp as $que) {
																echo '<td class="text-right thin-border-bottom">
														<input type="text" name="hrgN[' . $idm . '][' . $que[0] . ']" id="hrgN' . $idm . $que[0] . '" class="input-sm form-control hitung" value="' . number_format($data['nm' . $que[0]], 0, '', '.') . '" />
													  </td>';
															}
															?>
															<td class="text-center" rowspan="4">
																<input type="checkbox" name="<?php echo "cek[" . $idm . "][cek]"; ?>" id="<?php echo "cek" . $idm . $que[0]; ?>" class="chkp" value="1" />
															</td>
														</tr>
														<tr>
															<td class="text-left thin-border-bottom">Harga Tier I (BM)</td>
															<?php
															foreach ($tmp as $que) {
																echo '<td class="text-right thin-border-bottom">
														<input type="text" name="hrgS[' . $idm . '][' . $que[0] . ']" id="hrgS' . $idm . $que[0] . '" class="input-sm form-control hitung" value="' . number_format($data['sm' . $que[0]], 0, '', '.') . '" />
													  </td>';
															}
															?>
														</tr>
														<tr>
															<td class="text-left thin-border-bottom">Harga Tier II (OM)</td>
															<?php
															foreach ($tmp as $que) {
																echo '<td class="text-right thin-border-bottom">
														<input type="text" name="hrgO[' . $idm . '][' . $que[0] . ']" id="hrgO' . $idm . $que[0] . '" class="input-sm form-control hitung" value="' . number_format($data['om' . $que[0]], 0, '', '.') . '" />
													  </td>';
															}
															?>
														</tr>
														<tr>
															<td class="text-left">Catatan</td>
															<td colspan="<?php echo count($res); ?>">
																<?php $notenya = $rec[$index]['note_jual']; ?>
																<div class="form-control catat" style="height:auto"><?php echo ($notenya) ? $notenya : '&nbsp;'; ?></div>
															</td>
														</tr>
														<tr style="display: none;">
															<?php
															$to = '';
															$sqlUser = "select * from acl_user where id_role = 6";
															$resUser = $con->getResult($sqlUser);
															foreach ($resUser as $key => $value) {
																$to .= $value['email_user'];
																if ($key < count($resUser) - 1)
																	$to .= ',';
															}
															?>
															<td colspan="8">
																<div class="row">
																	<div class="col-sm-12">
																		<div class="box-body">
																			<div class="row">
																				<div class="col-sm-10 col-md-8">
																					<div class="form-group">
																						<label>Kepada</label>
																						<input type="text" name="to[<?= $index ?>]" id="to" class="form-control validate[required]" value="<?php echo $to; ?>" />
																					</div>
																				</div>
																			</div>
																			<div class="row">
																				<div class="col-sm-10 col-md-8">
																					<div class="form-group">
																						<label>CC</label>
																						<input type="text" name="cc[<?= $index ?>]" id="cc" class="form-control" />
																					</div>
																				</div>
																			</div>
																			<div class="row">
																				<div class="col-sm-10 col-md-8">
																					<div class="form-group">
																						<label>Judul</label>
																						<input type="text" name="judul[<?= $index ?>]" id="judul" class="form-control" value="Notifikasi Persetujuan Harga Jual" />
																					</div>
																				</div>
																			</div>
																			<div class="row">
																				<div class="col-sm-10">
																					<div class="form-group">
																						<label>Pesan</label>
																						<textarea name="pesan[<?= $index ?>]" id="pesan" class="form-control wysiwyg">Notifikasi Persetujuan Harga Jual Area <?php echo $data['nama_area']; ?></textarea>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
															</td>
														</tr>
												<?php }
												} ?>
											</tbody>
										</table>
									</div>
									<?php if (count($result) > 0) { ?>
										<hr style="margin:0 0 10px" />
										<div class="form-group row">
											<div class="col-sm-12">
												<a href="<?php echo $link1; ?>" class="btn btn-default jarak-kanan">Kembali</a>
												<button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
											</div>
										</div>
									<?php } ?>
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

	<style>
		#table-grid td,
		#table-grid th {
			font-size: 12px;
		}

		.table>tbody>tr>td {
			padding: 5px;
			border-bottom: 4px solid #ccc;
		}

		.thin-border-bottom {
			border-bottom: 1px solid #ddd !important;
		}

		.catat {
			font-size: 12px;
		}

		.catat p {
			margin-bottom: 3px;
		}
	</style>
	<script>
		$(document).ready(function() {

			$("#btnSbmt").on("click", function(e) {
				e.preventDefault(); // Cegah submit default

				Swal.fire({
					title: "Konfirmasi",
					text: "Apakah Anda yakin ingin Menyetujui data ini?",
					icon: "warning",
					showCancelButton: true,
					confirmButtonText: "Ya, Simpan",
					cancelButtonText: "Batal",
					reverseButtons: true,
				}).then((result) => {
					if (result.isConfirmed) {
						// Jika konfirmasi OK, submit form
						$("#gform").submit();
					}
				});
			});

			$(".hitung").priceFormat({
				prefix: '',
				thousandsSeparator: ','
			});
			$("#cekAll").on("ifChecked", function() {
				$(".chkp").iCheck("check");
			}).on("ifUnchecked", function() {
				$(".chkp").iCheck("uncheck");
			});
		});
		$(".wysiwyg").ckeditor();
	</script>
</body>

</html>
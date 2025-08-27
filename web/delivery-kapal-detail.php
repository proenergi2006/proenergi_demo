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
$idr 	= isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
$cek = "select a.*, b.inisial_segel, c.nama_terminal, c.tanki_terminal, d.nama_suplier, e.volume 
			from pro_po_ds_kapal a join pro_master_cabang b on a.id_wilayah = b.id_master 
			join pro_master_terminal c on a.terminal = c.id_master join pro_master_transportir d on a.transportir = d.id_master 
			join pro_pr_detail e on a.id_prd = e.id_prd where a.id_dsk = '" . $idr . "'";
$row = $con->getRecord($cek);
$link1 	= BASE_URL_CLIENT . '/delivery-kapal.php';
$link2 	= ACTION_CLIENT . '/delivery-kapal-cetak.php?' . paramEncrypt('idr=' . $idr);
$note 	= ($row['keterangan']) ? ($row['keterangan']) : '&nbsp;';
$tank 	= json_decode($row['tank_seal'], true);
$mani 	= json_decode($row['manifold_seal'], true);
$pump 	= json_decode($row['pump_seal'], true);
$other 	= json_decode($row['other_seal'], true);

$mani_kiri_awal  = ($mani['mani_kiri_awal']) ? str_pad($mani['mani_kiri_awal'], 4, '0', STR_PAD_LEFT) : '';
$mani_kiri_akhir = ($mani['mani_kiri_akhir']) ? str_pad($mani['mani_kiri_akhir'], 4, '0', STR_PAD_LEFT) : '';
if ($mani['jumlah_kiri'] == 1)
	$mani_kiri = $row['inisial_segel'] . "-" . $mani_kiri_awal;
else if ($mani['jumlah_kiri'] == 2)
	$mani_kiri = $row['inisial_segel'] . "-" . $mani_kiri_awal . " &amp; " . $row['inisial_segel'] . "-" . $mani_kiri_akhir;
else if ($mani['jumlah_kiri'] > 2)
	$mani_kiri = $row['inisial_segel'] . "-" . $mani_kiri_awal . " s/d " . $row['inisial_segel'] . "-" . $mani_kiri_akhir;
else $mani_kiri = '';

$mani_kanan_awal  = ($mani['mani_kanan_awal']) ? str_pad($mani['mani_kanan_awal'], 4, '0', STR_PAD_LEFT) : '';
$mani_kanan_akhir = ($mani['mani_kanan_akhir']) ? str_pad($mani['mani_kanan_akhir'], 4, '0', STR_PAD_LEFT) : '';
if ($mani['jumlah_kanan'] == 1)
	$mani_kanan = $row['inisial_segel'] . "-" . $mani_kanan_awal;
else if ($mani['jumlah_kanan'] == 2)
	$mani_kanan = $row['inisial_segel'] . "-" . $mani_kanan_awal . " &amp; " . $row['inisial_segel'] . "-" . $mani_kanan_akhir;
else if ($mani['jumlah_kanan'] > 2)
	$mani_kanan = $row['inisial_segel'] . "-" . $mani_kanan_awal . " s/d " . $row['inisial_segel'] . "-" . $mani_kanan_akhir;
else $mani_kanan = '';

$pump_kiri_awal  = ($pump['pump_kiri_awal']) ? str_pad($pump['pump_kiri_awal'], 4, '0', STR_PAD_LEFT) : '';
$pump_kiri_akhir = ($pump['pump_kiri_akhir']) ? str_pad($pump['pump_kiri_akhir'], 4, '0', STR_PAD_LEFT) : '';
if ($pump['jumlah_kiri'] == 1)
	$pump_kiri = $row['inisial_segel'] . "-" . $pump_kiri_awal;
else if ($pump['jumlah_kiri'] == 2)
	$pump_kiri = $row['inisial_segel'] . "-" . $pump_kiri_awal . " &amp; " . $row['inisial_segel'] . "-" . $pump_kiri_akhir;
else if ($pump['jumlah_kiri'] > 2)
	$pump_kiri = $row['inisial_segel'] . "-" . $pump_kiri_awal . " s/d " . $row['inisial_segel'] . "-" . $pump_kiri_akhir;
else $pump_kiri = '';

$pump_kanan_awal  = ($pump['pump_kanan_awal']) ? str_pad($pump['pump_kanan_awal'], 4, '0', STR_PAD_LEFT) : '';
$pump_kanan_akhir = ($pump['pump_kanan_akhir']) ? str_pad($pump['pump_kanan_akhir'], 4, '0', STR_PAD_LEFT) : '';
if ($pump['jumlah_kanan'] == 1)
	$pump_kanan = $row['inisial_segel'] . "-" . $pump_kanan_awal;
else if ($pump['jumlah_kanan'] == 2)
	$pump_kanan = $row['inisial_segel'] . "-" . $pump_kanan_awal . " &amp; " . $row['inisial_segel'] . "-" . $pump_kanan_akhir;
else if ($pump['jumlah_kanan'] > 2)
	$pump_kanan = $row['inisial_segel'] . "-" . $pump_kanan_awal . " s/d " . $row['inisial_segel'] . "-" . $pump_kanan_akhir;
else $pump_kanan = '';
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
				<h1>Delivery Kapal Detail</h1>
			</section>
			<section class="content">

				<?php if ($enk['idr'] !== '' && isset($enk['idr'])) { ?>
					<?php $flash->display(); ?>
					<div class="row">
						<div class="col-sm-12">
							<div class="box box-primary">
								<div class="box-body">

									<p class="text-center" style="margin-bottom:0px;"><b>DELIVERY NOTE</b></p>
									<p class="text-center" style="margin-bottom:0px;"><b>NO : <?php echo $row['nomor_dn_kapal']; ?></b></p>
									<p>Date : <?php echo tgl_indo($row['tanggal_loading']); ?></p>
									<div class="table-responsive">
										<table class="table table-bordered">
											<tr>
												<td width="33%">
													<p style="margin-bottom:0px;"><b>Shipper</b></p>
													<p style="margin-bottom:0px;"><?php echo $row['consignor_nama']; ?></p>
													<p style="margin-bottom:0px;"><?php echo $row['consignor_alamat']; ?></p>
												</td>
												<td width="33%">
													<p style="margin-bottom:0px;"><b>Consignee</b></p>
													<p style="margin-bottom:0px;"><?php echo $row['consignee_nama']; ?></p>
													<p style="margin-bottom:0px;"><?php echo $row['consignee_alamat']; ?></p>
												</td>
												<td width="33%">
													<p style="margin-bottom:0px;"><b>Notify Party</b></p>
													<p style="margin-bottom:0px;"><?php echo $row['notify_nama']; ?></p>
													<p style="margin-bottom:0px;"><?php echo $row['notify_alamat']; ?></p>
												</td>
											</tr>
										</table>
									</div>
									<div class="table-responsive">
										<table class="table table-bordered table-grid1">
											<thead>
												<tr>
													<th rowspan="2" class="text-center" width="5%">No</th>
													<th rowspan="2" class="text-center" width="25%">Description</th>
													<th colspan="2" class="text-center">Quantity</th>
													<th rowspan="2" class="text-center" width="25%">Unit</th>
												</tr>
												<tr>
													<th class="text-center" width="15%">SYSTEM</th>
													<th class="text-center" width="15%">BL</th>

												</tr>
											</thead>
											<tbody>
												<tr>
													<td rowspan="3" class="text-center">1</td>
													<td rowspan="3" class="text-center"><?php echo $row['produk_dn']; ?></td>
													<td rowspan="3" class="text-right"><?php echo number_format($row['volume']); ?></td>
													<td class="text-right"><?php echo ($row['bl_lo_jumlah']) ? number_format($row['bl_lo_jumlah']) : ''; ?></td>

													<td>Litres Observe</td>
												</tr>
												<tr>
													<td class="text-right"><?php echo ($row['bl_lc_jumlah']) ? number_format($row['bl_lc_jumlah']) : ''; ?></td>

													<td>Litres 15<sup>o</sup>C (GSV)</td>
												</tr>
												<tr>
													<td class="text-right"><?php echo ($row['bl_mt_jumlah']) ? number_format($row['bl_mt_jumlah']) : ''; ?></td>

													<td>MT</td>
												</tr>
												<tr>
													<td colspan="2">Loading Port</td>
													<td colspan="4"><?php echo $row['nama_terminal']; ?> - <?php echo $row['tanki_terminal']; ?></td>
												</tr>
												<tr>
													<td colspan="2">Port of Discharge</td>
													<td colspan="4"><?php echo $row['port_discharge']; ?></td>
												</tr>
												<tr>
													<td colspan="2">Shipping Line</td>
													<td colspan="4"><?php echo $row['nama_suplier']; ?></td>
												</tr>
												<tr>
													<td colspan="2">Master (Captain)</td>
													<td colspan="4"><?php echo $row['kapten_name']; ?></td>
												</tr>
												<tr>
													<td colspan="2">Vessel Name</td>
													<td colspan="4"><?php echo $row['vessel_name']; ?></td>
												</tr>
												<tr>
													<td colspan="2">Shipment</td>
													<td colspan="4"><?php echo $row['shipment']; ?></td>
												</tr>
											</tbody>
										</table>
									</div>

									<?php
									$nom = 0;
									$tank_kiri = [];  // Array untuk menyimpan nomor tank kiri
									$tank_kanan = []; // Array untuk menyimpan nomor tank kanan

									foreach ($tank as $idx1 => $data1) {
										$nom++;

										// Proses untuk tank kiri
										if (!empty($data1['tank_kiri_awal']) && !empty($data1['tank_kiri_akhir'])) {
											// Loop untuk menambahkan semua nomor antara tank_kiri_awal dan tank_kiri_akhir
											for ($i = $data1['tank_kiri_awal']; $i <= $data1['tank_kiri_akhir']; $i++) {
												$tank_kiri[] = $row['inisial_segel'] . "-" . str_pad($i, 5, '0', STR_PAD_LEFT);
											}
										}

										// Proses untuk tank kanan (jika ada datanya)
										if (!empty($data1['tank_kanan_awal']) && !empty($data1['tank_kanan_akhir'])) {
											// Loop untuk menambahkan semua nomor antara tank_kanan_awal dan tank_kanan_akhir
											for ($i = $data1['tank_kanan_awal']; $i <= $data1['tank_kanan_akhir']; $i++) {
												$tank_kanan[] = $row['inisial_segel'] . "-" . str_pad($i, 5, '0', STR_PAD_LEFT);
											}
										}
									}

									// Menggabungkan semua nomor yang telah diambil
									$all_tank = array_merge($tank_kiri, $tank_kanan);

									// Menghapus duplikat jika ada
									$all_tank = array_unique($all_tank);

									// Menyiapkan output
									$output = implode(", ", $all_tank);
									?>


									<div class="form-group row">
										<div class="col-sm-8">
											<p style="font-size:18px;"><b><u>Seal Number</u></b></p>
											<div class="form-control" style="height:auto"> <?php echo $output; ?></div>
										</div>
									</div>
									<!-- <div class="table-responsive">
										<table class="table table-bordered table-grid1" id="seal-nomor">
											<thead>
												<tr>
													<th class="text-center" width="25%">ITEMS</th>
													<th class="text-center" width="25%">SEGEL</th>
													<th class="text-center" width="25%">ITEMS</th>
													<th class="text-center" width="25%">SEGEL</th>
												</tr>
											</thead>
											<tbody>
												<?php
												$nom = 0;
												$tank_kiri_awal_total = '';  // Variabel untuk menyimpan nomor awal kiri yang paling kecil
												$tank_kanan_akhir_total = ''; // Variabel untuk menyimpan nomor akhir kanan yang paling besar

												foreach ($tank as $idx1 => $data1) {
													$nom++;

													// Proses untuk tank kiri
													$tank_kiri_awal  = ($data1['tank_kiri_awal']) ? str_pad($data1['tank_kiri_awal'], 4, '0', STR_PAD_LEFT) : '';
													$tank_kiri_akhir = ($data1['tank_kiri_akhir']) ? str_pad($data1['tank_kiri_akhir'], 4, '0', STR_PAD_LEFT) : '';

													// Proses untuk tank kanan
													$tank_kanan_awal  = ($data1['tank_kanan_awal']) ? str_pad($data1['tank_kanan_awal'], 4, '0', STR_PAD_LEFT) : '';
													$tank_kanan_akhir = ($data1['tank_kanan_akhir']) ? str_pad($data1['tank_kanan_akhir'], 4, '0', STR_PAD_LEFT) : '';

													// Set nomor segel awal (kiri paling kecil)
													if ($tank_kiri_awal_total == '' || $tank_kiri_awal < $tank_kiri_awal_total) {
														$tank_kiri_awal_total = $tank_kiri_awal;
													}

													// Set nomor segel akhir (kanan paling besar)
													if ($tank_kanan_akhir_total == '' || $tank_kanan_akhir > $tank_kanan_akhir_total) {
														$tank_kanan_akhir_total = $tank_kanan_akhir;
													}
												}
												?>
												<tr class="tank">
													<td><?php echo $nom . "S"; ?></td>
													<td class="text-center"><?php echo 	$tank_kiri_awal_total; ?></td>

												</tr>

												<tr>
													<td>Manifold</td>
													<td class="text-center"><?php echo $mani_kiri; ?></td>
													<td>Manifold</td>
													<td class="text-center"><?php echo $mani_kanan; ?></td>
												</tr>
												<tr>
													<td>Pump Room</td>
													<td class="text-center"><?php echo $pump_kiri; ?></td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
												</tr>
											</tbody>
										</table>
									</div> -->

									<?php if (count($other) > 0) { ?>
										<p style="font-size:18px;"><b><u>Seal Number Others</u></b></p>
										<div class="table-responsive">
											<table class="table table-bordered table-grid1" id="seal-lain">
												<thead>
													<tr>
														<th class="text-center" width="25%">ITEMS</th>
														<th class="text-center" width="25%">SEGEL</th>
														<th class="text-center" width="25%">ITEMS</th>
														<th class="text-center" width="25%">SEGEL</th>
													</tr>
												</thead>
												<tbody>
													<?php
													foreach ($other as $data5) {
														$sgl_kiri_awal 	= ($data5['sgl_kiri_awal']) ? str_pad($data5['sgl_kiri_awal'], 4, '0', STR_PAD_LEFT) : '';
														$sgl_kiri_akhir = ($data5['sgl_kiri_akhir']) ? str_pad($data5['sgl_kiri_akhir'], 4, '0', STR_PAD_LEFT) : '';
														if ($data5['jumlah_kiri'] == 1)
															$nomor_lain_kiri = $row['inisial_segel'] . "-" . $sgl_kiri_awal;
														else if ($data5['jumlah_kiri'] == 2)
															$nomor_lain_kiri = $row['inisial_segel'] . "-" . $sgl_kiri_awal . " &amp; " . $row['inisial_segel'] . "-" . $sgl_kiri_akhir;
														else if ($data5['jumlah_kiri'] > 2)
															$nomor_lain_kiri = $row['inisial_segel'] . "-" . $sgl_kiri_awal . " s/d " . $row['inisial_segel'] . "-" . $sgl_kiri_akhir;
														else $nomor_lain_kiri = '';

														$sgl_kanan_awal  = ($data5['sgl_kanan_awal']) ? str_pad($data5['sgl_kanan_awal'], 4, '0', STR_PAD_LEFT) : '';
														$sgl_kanan_akhir = ($data5['sgl_kanan_akhir']) ? str_pad($data5['sgl_kanan_akhir'], 4, '0', STR_PAD_LEFT) : '';
														if ($data5['jumlah_kanan'] == 1)
															$nomor_lain_kanan = $row['inisial_segel'] . "-" . $sgl_kanan_awal;
														else if ($data5['jumlah_kanan'] == 2)
															$nomor_lain_kanan = $row['inisial_segel'] . "-" . $sgl_kanan_awal . " &amp; " . $row['inisial_segel'] . "-" . $sgl_kanan_akhir;
														else if ($data5['jumlah_kanan'] > 2)
															$nomor_lain_kanan = $row['inisial_segel'] . "-" . $sgl_kanan_awal . " s/d " . $row['inisial_segel'] . "-" . $sgl_kanan_akhir;
														else $nomor_lain_kanan = '';
													?>
														<tr>
															<td><?php echo $data5['jns_kiri']; ?></td>
															<td class="text-center"><?php echo $nomor_lain_kiri; ?></td>
															<td><?php echo $data5['jns_kanan']; ?></td>
															<td class="text-center"><?php echo $nomor_lain_kanan; ?></td>
														</tr>
													<?php } ?>
												</tbody>
											</table>
										</div>
									<?php } ?>


									<div class="form-group row">
										<div class="col-sm-8">
											<label>Keterangan</label>
											<div class="form-control" style="height:auto"><?php echo $note; ?></div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-12">
											<div class="pad bg-gray">
												<a class="btn btn-default jarak-kanan" style="width:80px;" href="<?php echo $link1; ?>">Kembali</a>
												<?php if (!$row['is_cancel']) { ?>
													<a class="btn btn-success" target="_blank" href="<?php echo $link2; ?>">Cetak Data</a>
												<?php } ?>
											</div>
										</div>
									</div>

								</div>
							</div>
						</div>
					</div>

				<?php } ?>
				<div class="modal fade" id="preview_modal" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header bg-blue">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">Peringatan</h4>
							</div>
							<div class="modal-body">
								<div id="preview_alert" class="text-center"></div>
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

	<style type="text/css">
		#table-grid3 td,
		#table-grid3 th {
			font-size: 12px;
			padding: 5px
		}

		.table-grid1 td,
		.table-grid1 th {
			font-size: 14px;
			padding: 5px
		}
	</style>
</body>

</html>
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

$idr 		= isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
$sesuser 	= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);
$seswil 	= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$sesgroup 	= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);

if ($idr != "") {
	$sql = "
			select a.*, b.nama_customer as nm_customer, c.nama_cabang 
			from pro_invoice_admin a 
			join pro_customer b on a.id_customer = b.id_customer 
			join pro_master_cabang c on b.id_wilayah = c.id_master 
			where 1=1 and a.id_invoice = '" . $idr . "'
		";
	$model 	= $con->getRecord($sql);
	$action = "update";
} else {
	$model 		= array();
	$action 	= "add";
}

if ($model['jenis'] == "split_pbbkb") {
	$linkCetak = ACTION_CLIENT . '/invoice-customer-cetak.php?' . paramEncrypt('idr=' . $idr . '&tipe=split_pbbkb');
} elseif ($model['jenis'] == "split_oa") {
	$linkCetak = ACTION_CLIENT . '/invoice-customer-cetak.php?' . paramEncrypt('idr=' . $idr . '&tipe=split_oa');
} elseif ($model['jenis'] == "harga_dasar") {
	$linkCetak = ACTION_CLIENT . '/invoice-customer-cetak.php?' . paramEncrypt('idr=' . $idr . '&tipe=harga_dasar');
} elseif ($model['jenis'] == "harga_dasar_oa") {
	$linkCetak = ACTION_CLIENT . '/invoice-customer-cetak.php?' . paramEncrypt('idr=' . $idr . '&tipe=harga_dasar_oa');
} elseif ($model['jenis'] == "harga_dasar_pbbkb") {
	$linkCetak = ACTION_CLIENT . '/invoice-customer-cetak.php?' . paramEncrypt('idr=' . $idr . '&tipe=harga_dasar_pbbkb');
} else {
	$linkCetak = ACTION_CLIENT . '/invoice-customer-cetak.php?' . paramEncrypt('idr=' . $idr . '&tipe=default');
	$linkCetak_pbbkb = ACTION_CLIENT . '/invoice-customer-cetak.php?' . paramEncrypt('idr=' . $idr . '&tipe=pbbkb');
}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("myGrid", "formatNumber", "jqueryUI"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory . "/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
		<aside class="right-side">
			<section class="content-header">
				<h1>Invoice Customer</h1>
			</section>
			<section class="content">

				<?php $flash->display(); ?>
				<div class="box box-primary">
					<div class="box-header with-border">
					</div>
					<div class="box-body">
						<form action="<?php echo ACTION_CLIENT . '/invoice_customer.php'; ?>" id="gform" name="gform" method="post" class="form-validasi form-horizontal" role="form">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">Nama Customer</label>
										<div class="col-md-8">
											<?php if ($action == 'add') { ?>
												<div class="input-group">
													<input type="hidden" id="id_customer" name="id_customer" value="<?php echo $model['id_customer']; ?>" />
													<input type="text" id="nm_customer" name="nm_customer" class="form-control" value="<?php echo $model['nm_customer']; ?>" required readonly />
													<span class="input-group-btn">
														<button type="button" class="btn btn-sm btn-primary picker-user"><i class="fa fa-search"></i></button>
													</span>
												</div>
											<?php } else { ?>
												<input type="hidden" id="id_customer" name="id_customer" value="<?php echo $model['id_customer']; ?>" />
												<input type="text" id="nm_customer" name="nm_customer" class="form-control" value="<?php echo $model['nm_customer']; ?>" required readonly />
											<?php } ?>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">No Invoice</label>
										<div class="col-md-8">
											<input type="text" id="no_invoice" name="no_invoice" class="form-control" value="<?php echo $model['no_invoice']; ?>" required disabled />
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">Tgl Invoice</label>
										<div class="col-md-4">
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
												<?php $currval = ($model['tgl_invoice'] ? date("d/m/Y", strtotime($model['tgl_invoice'])) : ''); ?>
												<input type="text" id="tgl_invoice" name="tgl_invoice" class="form-control datepicker" value="<?php echo $currval; ?>" autocomplete="off" required disabled />
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="row" id="row-periode">
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">Periode Awal</label>
										<div class="col-md-4">
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
												<?php $currval = ($model['tgl_kirim_awal'] ? date("d/m/Y", strtotime($model['tgl_kirim_awal'])) : ''); ?>
												<input type="text" id="tgl_kirim_awal" name="tgl_kirim_awal" class="form-control datepicker" value="<?php echo $currval; ?>" autocomplete="off" required disabled />
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">Periode Akhir</label>
										<div class="col-md-4">
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
												<?php $currval = ($model['tgl_kirim_akhir'] ? date("d/m/Y", strtotime($model['tgl_kirim_akhir'])) : ''); ?>
												<input type="text" id="tgl_kirim_akhir" name="tgl_kirim_akhir" class="form-control datepicker" value="<?php echo $currval; ?>" autocomplete="off" required disabled />
											</div>
										</div>
									</div>
								</div>
							</div>

							<?php if ($action == 'add') { ?>
								<div class="row">
									<div class="col-md-12">
										<div class="form-group form-group-sm">
											<div class="col-md-12">
												<button type="button" name="btn-generate" id="btn-generate" class="btn btn-sm btn-info">Generate</button>
											</div>
										</div>
									</div>
								</div>
							<?php } ?>

							<div class="table-responsive">
								<table class="table table-bordered table-dasar">
									<thead>
										<tr>
											<th class="text-center" width="50">No</th>
											<th class="text-center" width="150">No PO Customer</th>
											<th class="text-center" width="120">Referensi</th>
											<th class="text-center" width="150">Tgl Delivered</th>
											<th class="text-center" width="200">Volume Realisasi</th>
											<th class="text-center" width="180">Harga</th>
											<th class="text-center" width="180">Jumlah</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$sql02 = "
										select 
										a.*, f.*, b.nomor_do as no_dn, k1.nomor_plat as angkutan, l1.nama_sopir as sopir, d.nomor_poc, b.realisasi_volume, ppd.no_do_acurate, ppd.no_do_syop, ppd.nomor_lo_pr
										from pro_invoice_admin_detail a 
										join pro_po_ds_detail b on a.id_dsd = b.id_dsd and a.jenisnya = 'truck' 
										join pro_pr_detail ppd on b.id_prd = ppd.id_prd 
										join pro_po_customer_plan c on b.id_plan = c.id_plan 
										join pro_po_customer d on c.id_poc = d.id_poc 
										join pro_po_detail b1 on b.id_pod = b1.id_pod 
										join pro_master_transportir_mobil k1 on b1.mobil_po = k1.id_master 
										join pro_master_transportir_sopir l1 on b1.sopir_po = l1.id_master
										join pro_invoice_admin f on a.id_invoice=f.id_invoice
										where 1=1 and a.id_invoice = '" . $idr . "'
										UNION ALL 
										select 
										a.*, f.*, b.nomor_dn_kapal as no_dn, b.vessel_name as angkutan, b.kapten_name as sopir, e.nomor_poc, b.realisasi_volume, c.no_do_acurate, c.no_do_syop, c.nomor_lo_pr  
										from pro_invoice_admin_detail a 
										join pro_po_ds_kapal b on a.id_dsd = b.id_dsk and a.jenisnya = 'kapal' 
										join pro_pr_detail c on b.id_prd = c.id_prd 
										join pro_po_customer_plan d on c.id_plan = d.id_plan 
										join pro_po_customer e on d.id_poc = e.id_poc
										join pro_invoice_admin f on a.id_invoice=f.id_invoice
										where 1=1 and a.id_invoice = '" . $idr . "' 
										order by id_invoice_detail";

										$listData1 	= $con->getResult($sql02);

										$arrPengeluaran = (count($listData1) > 0) ? $listData1 : array();
										if (count($arrPengeluaran) > 0) {
											$no_urut = 0;
											$total_realisasi = 0;


											foreach ($arrPengeluaran as $data1) {
												$no_urut++;

												$tgl_delivered 	= ($data1['tgl_delivered']) ? date('d/m/Y', strtotime($data1['tgl_delivered'])) : '';

												$vol_kirim 		= ($data1['vol_kirim']) ? number_format($data1['vol_kirim']) : '';

												$realisasi_volume = ($data1['realisasi_volume']) ? number_format($data1['realisasi_volume']) : '';

												$jumlah_harga = $data1['vol_kirim'] * $data1['harga_kirim'];

												$total_invoice 	= $total_invoice + $jumlah_harga;

												if ($data1['no_do_acurate'] == NULL) {
													$no_do = $data1['no_do_syop'];
												} else {
													$no_do = $data1['no_do_acurate'];
												}

												$sql02 	= "SELECT a.*, d.harga_dasar, d.detail_rincian, d.pembulatan FROM pro_invoice_admin_detail a JOIN pro_po_ds_detail b ON a.id_dsd=b.id_dsd and a.jenisnya = 'truck' JOIN pro_po_customer c ON b.id_poc=c.id_poc JOIN pro_penawaran d ON c.id_penawaran=d.id_penawaran WHERE a.id_invoice='" . $data1['id_invoice'] . "' LIMIT 1";
												$result02 	= $con->getRecord($sql02);

												if ($result02['pembulatan'] == 2) {
													$harga_kirim = ($result02['harga_kirim']) ? number_format($result02['harga_kirim'], 4) : '';
													$jumlah_harga_fix = number_format($jumlah_harga, 4);
													$total_invoice_fix = number_format($total_invoice, 4);
												} elseif ($result02['pembulatan'] == 0) {
													$harga_kirim = ($result02['harga_kirim']) ? number_format($result02['harga_kirim'], 2) : '';
													$jumlah_harga_fix = number_format($jumlah_harga, 2);
													$total_invoice_fix = number_format($total_invoice, 2);
												} else {
													$harga_kirim = ($result02['harga_kirim']) ? number_format($result02['harga_kirim'], 0) : '';
													$jumlah_harga_fix = number_format($jumlah_harga, 0);
													$total_invoice_fix = number_format($total_invoice, 0);
												}

												$decode = json_decode($result02['detail_rincian'], true);
												$jenis  = "";
												$total_volume = 0;
												foreach ($decode as $arr1) {
													if ($arr1['rincian'] == "Harga Dasar") {
														$harga_dasar_penawaran = ($arr1['biaya']) ? $arr1['biaya'] : 0;
														$nilai_harga_dasar = $arr1['nilai'];
													}

													if ($arr1['rincian'] == "Ongkos Angkut") {
														$ongkos_angkut_penawaran = ($arr1['biaya']) ? $arr1['biaya'] : 0;
														$nilai_ongkos_angkut = $arr1['nilai'];
													}

													if ($arr1['rincian'] == "PBBKB") {
														$pbbkb_penawaran = ($arr1['biaya']) ? $arr1['biaya'] : 0;
														$nilai_pbbkb = $arr1['nilai'];
													}

													if ($arr1['rincian'] == "PPN") {
														$ppn_penawaran = ($arr1['biaya']) ? $arr1['biaya'] : 0;
														$nilai_ppn = $arr1['nilai'];
													}

													if ($data1['jenis'] == "all_in") {
														$nilai = $arr1['nilai'];
														$biaya = ($arr1['biaya']) ? $arr1['biaya'] : 0;
														if ($arr1['rincian'] == "PPN") {
															if ($result02['pembulatan'] == 1) {
																$jenis .= "<p>" . $arr1['rincian'] . " " . $arr1['nilai'] . "% : " . number_format($biaya) . "</p>";
															} elseif ($result02['pembulatan'] == 0) {
																$jenis .= "<p>" . $arr1['rincian'] . " " . $arr1['nilai'] . "% : " . number_format($biaya, 2, ".", ",") . "</p>";
															} else {
																$jenis .= "<p>" . $arr1['rincian'] . " " . $arr1['nilai'] . "% : " . number_format($biaya, 4, ".", ",") . "</p>";
															}
														} else {
															if ($result02['pembulatan'] == 1) {
																$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya) . "</p>";
															} elseif ($result02['pembulatan'] == 0) {
																$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya, 2, ".", ",") . "</p>";
															} else {
																$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya, 4, ".", ",") . "</p>";
															}
														}
													} elseif ($data1['jenis'] == "split_oa") {
														if ($arr1['rincian'] == "Ongkos Angkut" || $arr1['rincian'] == "PPN") {
															$nilai = $arr1['nilai'];
															$biaya = ($arr1['biaya']) ? $arr1['biaya'] : 0;
															if ($arr1['rincian'] == "PPN") {
																$total_oa_ppn = $ongkos_angkut_penawaran * $nilai_ppn / 100;
																if ($result02['pembulatan'] == 1) {
																	$jenis .= "<p>" . $arr1['rincian'] . " " . $arr1['nilai'] . "% : " . number_format($total_oa_ppn) . "</p>";
																} elseif ($result02['pembulatan'] == 0) {
																	$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($total_oa_ppn, 2, ".", ",") . "</p>";
																} else {
																	$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($total_oa_ppn, 4, ".", ",") . "</p>";
																}
															} else {
																if ($result02['pembulatan'] == 1) {
																	$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya) . "</p>";
																} elseif ($result02['pembulatan'] == 0) {
																	$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya, 2, ".", ",") . "</p>";
																} else {
																	$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya, 4, ".", ",") . "</p>";
																}
															}
														}
													} elseif ($data1['jenis'] == "harga_dasar") {
														if ($arr1['rincian'] == "Harga Dasar" || $arr1['rincian'] == "PPN") {
															$nilai = $arr1['nilai'];
															$biaya = ($arr1['biaya']) ? $arr1['biaya'] : 0;
															if ($arr1['rincian'] == "PPN") {
																$total_hsd_ppn = $harga_dasar_penawaran * $nilai_ppn / 100;
																if ($result02['pembulatan'] == 1) {
																	$jenis .= "<p>" . $arr1['rincian'] . " " . $arr1['nilai'] . "% : " . number_format($total_hsd_ppn) . "</p>";
																} elseif ($result02['pembulatan'] == 0) {
																	$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($total_hsd_ppn, 2, ".", ",") . "</p>";
																} else {
																	$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($total_hsd_ppn, 4, ".", ",") . "</p>";
																}
															} else {
																if ($result02['pembulatan'] == 1) {
																	$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya) . "</p>";
																} elseif ($result02['pembulatan'] == 0) {
																	$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya, 2, ".", ",") . "</p>";
																} else {
																	$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya, 4, ".", ",") . "</p>";
																}
															}
														}
													} elseif ($data1['jenis'] == "split_pbbkb") {
														if ($arr1['rincian'] == "PBBKB") {
															$nilai = $arr1['nilai'];
															$biaya = ($arr1['biaya']) ? $arr1['biaya'] : 0;
															if ($result02['pembulatan'] == 1) {
																$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya) . "</p>";
															} elseif ($result02['pembulatan'] == 0) {
																$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya, 2, ".", ",") . "</p>";
															} else {
																$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya, 4, ".", ",") . "</p>";
															}
														}
													} elseif ($data1['jenis'] == "harga_dasar_oa") {
														if ($arr1['rincian'] == "Harga Dasar" || $arr1['rincian'] == "Ongkos Angkut" || $arr1['rincian'] == "PPN") {
															$nilai = $arr1['nilai'];
															$biaya = ($arr1['biaya']) ? $arr1['biaya'] : 0;
															if ($arr1['rincian'] == "PPN") {
																if ($result02['pembulatan'] == 1) {
																	$jenis .= "<p>" . $arr1['rincian'] . " " . $arr1['nilai'] . "% : " . number_format($biaya) . "</p>";
																} elseif ($result02['pembulatan'] == 0) {
																	$jenis .= "<p>" . $arr1['rincian'] . " " . $arr1['nilai'] . "% : " . number_format($biaya, 2, ".", ",") . "</p>";
																} else {
																	$jenis .= "<p>" . $arr1['rincian'] . " " . $arr1['nilai'] . "% : " . number_format($biaya, 4, ".", ",") . "</p>";
																}
															} else {
																if ($result02['pembulatan'] == 1) {
																	$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya) . "</p>";
																} elseif ($result02['pembulatan'] == 0) {
																	$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya, 2, ".", ",") . "</p>";
																} else {
																	$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya, 4, ".", ",") . "</p>";
																}
															}
														}
													}
												}

												echo '
                                            <tr data-id="' . $no_urut . '">
                                                <td class="text-center">
													<span class="frmnodasar" data-row-count="' . $no_urut . '">' . $no_urut . '</span>
												</td>
												<td class="text-left">
													<p style="margin-bottom:3px;">No PO Customer : ' . $data1['nomor_poc'] . '</p>
													<p style="margin-bottom:3px;">No DN : ' . $data1['no_dn'] . '</p>
													<p style="margin-bottom:3px;">Jenis Angkutan : ' . strtoupper($data1['jenisnya']) . '</p>
													<p style="margin-bottom:0px;">' . ($data1['jenisnya'] == 'truck' ? 'No Plat : ' . $data1['angkutan'] . ' (' . $data1['sopir'] . ')' : 'Vessel : ' . $data1['angkutan'] . ' (' . $data1['sopir'] . ')') . '</p>

												</td> 
												<td class="text-left">
													<p style="margin-bottom:3px;">Nomor DO : ' . $no_do . '</p>
													<p style="margin-bottom:3px;">Nomor LO : ' . $data1['nomor_lo_pr'] . '</p>
												</td>
												<td class="text-left">
													<input type="text" id="tgl_delivered' . $no_urut . '" name="tgl_delivered[]" class="form-control input-sm" value="' . $tgl_delivered . '" data-rule-dateNL="true" readonly />
												</td>
												<td class="text-left">
													<input type="text" id="vol_kirim' . $no_urut . '" name="vol_kirim[]" class="form-control input-sm  text-right volumenya" value="' . $vol_kirim . '" disabled/>
													<p> Volume PO : ' . $vol_kirim . '</p>
													<p> Realisasi : ' . $realisasi_volume  . '</p>
												</td>
												<td class="text-left">
													<input type="text" id="harga_kirim' . $no_urut . '" name="harga_kirim[]" class="form-control input-sm text-right harganya" value="' . $harga_kirim . '" readonly/>
													<br>
													' . $jenis . '
												</td>
												<td class="text-left">
													<input type="hidden" name="id_dsd[]" value="' . $data1['id_dsd'] . '" />
													<input type="hidden" name="jenisnya[]" value="' . $data1['jenisnya'] . '" />
													<input type="text" id="jumlah_harga' . $no_urut . '" name="jumlah_harga[]" class="form-control input-sm text-right jumlahnya" value="' . $jumlah_harga_fix . '" readonly />
												</td>
                                            </tr>';
											}
										} else {
											echo '<tr><td class="text-left" colspan="7">Tidak Ada Data</td></tr>';
										}
										?>
										<tr>
											<td class="text-center" colspan="6"><b>T O T A L</b></td>
											<td class="text-left">
												<input type="text" id="total_invoice" name="total_invoice" class="form-control input-sm text-right hitung" value="<?php echo $total_invoice_fix; ?>" readonly />
											</td>
											<td class="text-center">&nbsp;</td>
										</tr>
									</tbody>
								</table>
							</div>


							<hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

							<div style="margin-bottom:15px;">
								<a href="<?php echo BASE_URL_CLIENT . '/invoice_customer.php'; ?>" class="btn btn-default" style="min-width:90px;">
									<i class="fa fa-reply jarak-kanan"></i> Kembali
								</a>
								<?php if ($model['jenis'] == "all_in") : ?>
									<div class="btn-group text-left">
										<button type="button" class="btn btn-primary btn-md"><i class="fas fa-print"></i> Cetak</button>
										<button type="button" class="btn btn-primary btn-md dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
											<span class="caret"></span>
											<span class="sr-only">Toggle Dropdown</span>
										</button>
										<ul class="dropdown-menu" role="menu">
											<li>
												<a target="_blank" href="<?= $linkCetak ?>">Default</a>
											</li>
											<li>
												<a target="_blank" href="<?= $linkCetak_pbbkb ?>">Pisah PBBKB</a>
											</li>
										</ul>
									</div>
								<?php else : ?>
									<a target="_blank" class="btn btn-primary" href="<?= $linkCetak ?>">Cetak</a>
								<?php endif ?>
							</div>
						</form>
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

	<div class="modal fade" id="user_modal" role="dialog" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog" style="width:1000px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">List Customer</h4>
				</div>
				<div class="modal-body"></div>
			</div>
		</div>
	</div>

	<style type="text/css">
		#table-grid3 {
			margin-bottom: 15px;
		}

		#table-grid3 td,
		#table-grid3 th {
			font-size: 11px;
			font-family: arial;
		}
	</style>
	<script>
		$(document).ready(function() {
			var action = `<?= $action ?>`;
			if (action == 'add') {
				$("#row-tanggal").hide();
				$("#row-periode").hide();
			} else {
				var tgl_kirim_awal = `<?= $model['tgl_kirim_awal'] ?>`;
				var tgl_kirim_akhir = `<?= $model['tgl_kirim_akhir'] ?>`;
				if (tgl_kirim_awal == tgl_kirim_akhir) {
					$("#row-tanggal").show();
					$("#row-periode").hide();
				} else {
					$("#row-tanggal").hide();
					$("#row-periode").show();
				}
			}
		});
	</script>
</body>

</html>
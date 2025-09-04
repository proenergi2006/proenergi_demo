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
$idk 	= isset($enk["idk"]) ? htmlspecialchars($enk["idk"], ENT_QUOTES) : '';
$idc 	= isset($enk["idc"]) ? htmlspecialchars($enk["idc"], ENT_QUOTES) : '';
$sesid 	= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);
$seswil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$sesgroup = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);

$lsClosePo    = isset($enk["parClosePo"]) ? htmlspecialchars($enk["parClosePo"], ENT_QUOTES) : '';
$lsAttachment    = isset($enk["parAttachment"]) ? htmlspecialchars($enk["parAttachment"], ENT_QUOTES) : '';

if ($lsClosePo) {
	$cekClosePo = "
            SELECT id_poc,
                tgl_close,
                volume_close,
                realisasi_close,
                created_time,
                created_ip,
                created_by,
                id_user,
                id_role,
                keterangan,
                lampiran_close_po,
                lampiran_close_po_ori
            FROM pro_po_customer_close
            WHERE ST_AKTIF = 'Y'
            AND ID_POC = '" . $idk . "'
        ";

	$rowClosePo = $con->getRecord($cekClosePo);
	$pathPtClose     = $public_base_directory . '/files/uploaded_user/lampiran/' . $rowClosePo['lampiran_close_po'];
	$lampPtClose     = $rowClosePo['lampiran_close_po_ori'];

	$cekPlan = "
            select 
                a.id_poc,
                lpad(a.id_poc,4,'0') as kode_po,
                b.nama_customer,
                a.volume_poc,
                c.vol_plan,
                c.realisasi 
            from pro_po_customer a 
            join pro_customer b on a.id_customer = b.id_customer 
            left join (
                select 
                    id_poc, 
                    sum(if(realisasi_kirim = 0, volume_kirim, realisasi_kirim)) as vol_plan,
                    sum(realisasi_kirim) as realisasi 
                from pro_po_customer_plan 
                where 
                    id_poc = '" . $idk . "' 
                    and status_plan not in (2,3) 
                group by id_poc
            ) c on a.id_poc = c.id_poc 
            where 
                a.poc_approved = 1
                and a.id_customer = '" . $idr . "' 
                and a.id_poc = '" . $idk . "'
        ";

	$rowPlan = $con->getRecord($cekPlan);
}

if ($idr != "" && $idk != "") {
	$sql = "
			select a.*, b.nama_customer, b.credit_limit, b.top_payment, c.nomor_surat, c.masa_awal, c.masa_akhir, d.nama_cabang, f.nama_area, e.jenis_produk, e.merk_dagang, 
			c.oa_kirim, c.volume_tawar, c.refund_tawar,
			c.detail_formula, c.perhitungan, c.harga_dasar, c.pembulatan, c.detail_rincian,
			g.not_yet as not_yet,
			g.ov_up_07 as ov_up_07, 
			g.ov_under_30 as ov_under_30,
			g.ov_under_60 as ov_under_60,
			g.ov_under_90 as ov_under_90,
			g.ov_up_90 as ov_up_90 
			  
			from pro_po_customer a 
			join pro_customer b on a.id_customer = b.id_customer 
			join pro_penawaran c on a.id_penawaran = c.id_penawaran 
			join pro_master_cabang d on c.id_cabang = d.id_master 
			join pro_master_produk e on c.produk_tawar = e.id_master 
			join pro_master_area f on c.id_area = f.id_master 
			join pro_customer_admin_arnya g on a.id_customer = g.id_customer 
			where a.id_customer = '" . $idr . "' and a.id_poc = '" . $idk . "'
		";
	$rsm = $con->getRecord($sql);
	//print_r($rsm); exit;
	$rincian = json_decode($rsm['detail_rincian'], true);
	$formula = json_decode($rsm['detail_formula'], true);
	if ($rsm['perhitungan'] == 1) {
		if ($rsm['pembulatan'] == 0) {
			$harganya = number_format($rsm['harga_dasar'], 2);
			$ket_pembulatan = "TIDAK - 2 Angka dibelakang koma";
		} elseif ($rsm['pembulatan'] == 1) {
			$harganya = number_format($rsm['harga_dasar'], 0);
			$ket_pembulatan = "YA";
		} elseif ($rsm['pembulatan'] == 2) {
			$harganya = number_format($rsm['harga_dasar'], 4);
			$ket_pembulatan = "TIDAK - 4 Angka dibelakang koma";
		}
		$nilainya = $rsm['harga_dasar'];
	} else {
		$harganya = '';
		$nilainya = '';
		foreach ($formula as $jenis) {
			$harganya .= '<p style="margin-bottom:0px">' . $jenis . '</p>';
		}
	}
	$reminding = ($rsm['credit_limit'] ? $rsm['credit_limit'] - ($rsm['not_yet'] + $rsm['ov_up_07'] + $rsm['ov_under_30'] + $rsm['ov_under_60'] + $rsm['ov_under_90'] + $rsm['ov_up_90']) : 0);
	$rsm['reminding'] = $reminding;

	$action 	= "update";
	$section 	= "Ubah";
	$pathPt 	= $public_base_directory . '/files/uploaded_user/lampiran/' . $rsm['lampiran_poc'];
	$lampPt 	= $rsm['lampiran_poc_ori'];
	$linkAddPenerima = BASE_URL_CLIENT . '/add-master-penerima-refund.php?' . paramEncrypt('idcust=' . $rsm['id_customer']);

	$poc_penerima_refund = "SELECT * FROM pro_poc_penerima_refund WHERE id_poc = '" . $idk . "'";
	$result_poc_penerima_refund = $con->getResult($poc_penerima_refund);

	$sql_penerima_refund = "SELECT * FROM pro_master_penerima_refund WHERE is_active = '1' AND id_customer='" . $idr . "'";
	$rsm_penerima_refund = $con->getResult($sql_penerima_refund);
} else {
	$action 	= "add";
	$section 	= "Tambah";
	$linkAddPenerima = "#";
}

?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("formatNumber", "jqueryUI"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory . "/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
		<aside class="right-side">
			<section class="content-header">
				<h1><?php echo $section . " PO Customer"; ?></h1>
			</section>
			<section class="content">

				<?php $flash->display(); ?>
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
					</div>
					<div class="box-body">
						<form action="<?php echo ACTION_CLIENT . '/po-customer.php'; ?>" id="gform" name="gform" class="form-horizontal" method="post" role="form" enctype="multipart/form-data">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">Nama Customer *</label>
										<div class="col-md-8">
											<?php
											if ($action == "add") {
												$where = "id_marketing = '" . $sesid . "'";
												if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 18) {
													$where = "1=1";
													if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']))
														$where = "(id_wilayah = '" . $seswil . "' or id_marketing = '" . $sesid . "')";
													else if (!paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']))
														$where = "(id_group = '" . $sesgroup . "' or id_marketing = '" . $sesid . "')";
												}
												echo '<select id="customer" name="customer" class="form-control select2" required>';
												echo '<option></option>';
												$con->fill_select("id_customer", "if(kode_pelanggan = '', nama_customer, concat(kode_pelanggan,' - ',nama_customer))", "pro_customer", $idc, "where " . $where . " and is_verified = 1", "id_customer desc, nama", false);
												echo '</select>';
											} else {
												echo '<input type="hidden" name="customer" id="customer" value="' . $rsm['id_customer'] . '" />';
												echo '<input type="text" name="custNama" id="custNama" class="form-control" value="' . $rsm['nama_customer'] . '" readonly />';
											}
											?>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">TOP Payment *</label>
										<div class="col-md-4">
											<div class="input-group">
												<input type="text" name="top" id="top" class="form-control" value="<?php echo ($result['top_payment'] ? $result['top_payment'] : $rsm['top_poc']); ?>" readonly />
												<span class="input-group-addon">Hari</span>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-offset-2 col-md-6">
									<div id="keterangan_limit">
										<?php //if($result){ 
										?>
										<table border="1" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin-bottom:15px;">
											<tr>
												<td width="150" style="padding:3px 5px; background-color: #ddd;">Credit Limit</td>
												<td style="padding:3px 5px;"><?php echo 'Rp ' . ($rsm['credit_limit'] ? number_format($rsm['credit_limit']) : 0); ?></td>
											</tr>
											<tr>
												<td style="padding:3px 5px; background-color: #ddd;">Invoice not issued yet</td>
												<td style="padding:3px 5px;"><?php echo 'Rp ' . ($credit_limit_reserved ? number_format($credit_limit_reserved) : 0); ?></td>
											</tr>
											<tr>
												<td style="padding:3px 5px; background-color: #ddd;">AR Not yet</td>
												<td style="padding:3px 5px;"><?php echo 'Rp ' . ($rsm['not_yet'] ? number_format($rsm['not_yet']) : 0); ?></td>
											</tr>
											<tr>
												<td style="padding:3px 5px; background-color: #ddd;">AR Overdue 1-7 days</td>
												<td style="padding:3px 5px;"><?php echo 'Rp ' . ($rsm['ov_up_07'] ? number_format($rsm['ov_up_07']) : 0); ?></td>
											</tr>
											<tr>
												<td style="padding:3px 5px; background-color: #ddd;">AR Overdue 8-30 days</td>
												<td style="padding:3px 5px;"><?php echo 'Rp ' . ($rsm['ov_under_30'] ? number_format($rsm['ov_under_30']) : 0); ?></td>
											</tr>
											<tr>
												<td style="padding:3px 5px; background-color: #ddd;">AR Overdue 31-60 days</td>
												<td style="padding:3px 5px;"><?php echo 'Rp ' . ($rsm['ov_under_60'] ? number_format($rsm['ov_under_60']) : 0); ?></td>
											</tr>
											<tr>
												<td style="padding:3px 5px; background-color: #ddd;">AR Overdue 61-90 days</td>
												<td style="padding:3px 5px;"><?php echo 'Rp ' . ($rsm['ov_under_90'] ? number_format($rsm['ov_under_90']) : 0); ?></td>
											</tr>
											<tr>
												<td style="padding:3px 5px; background-color: #ddd;">AR Overdue > 90 days</td>
												<td style="padding:3px 5px;"><?php echo 'Rp ' . ($rsm['ov_up_90'] ? number_format($rsm['ov_up_90']) : 0); ?></td>
											</tr>
											<tr>
												<td style="padding:3px 5px; background-color: #ddd;">Credit Limit Remaining</td>
												<td style="padding:3px 5px;"><?php echo 'Rp ' . ($rsm['reminding'] ? number_format($rsm['reminding']) : 0); ?></td>
											</tr>
										</table>
										<?php //} 
										?>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">Nomor Penawaran *</label>
										<div class="col-md-8">
											<?php
											if ($action == "add") {
												echo '<select id="penawaran" name="penawaran" class="form-control select2" required>';
												echo '<option></option>';
												if ($idc && count($row1) > 0) {
													foreach ($row1 as $optx) {
														echo '<option value="' . $optx['id_penawaran'] . '">' . $optx['kode_penawaran'] . '</option>';
													}
												}
												echo '</select>';
											} else {
												echo '<input type="hidden" name="penawaran" id="penawaran" value="' . $rsm['id_penawaran'] . '" />';
												echo '<input type="text" name="a8" id="a8" class="form-control" value="' . $rsm['nomor_surat'] . '" readonly />';
											}
											?>
										</div>
									</div>
								</div>
							</div>

							<div id="ket-penawaran">
								<?php if ($action == "update") { ?>
									<div class="row">
										<div class="col-md-offset-2 col-md-6">
											<div class="table-responsive">
												<table class="table table-bordered">
													<tr>
														<td colspan="2" class="text-center bg-gray"><b>KETERANGAN</b></td>
													</tr>
													<tr>
														<td width="160">Masa berlaku harga</td>
														<td><?php echo tgl_indo($rsm['masa_awal']) . " - " . tgl_indo($rsm["masa_akhir"]); ?></td>
													</tr>
													<tr>
														<td>Cabang</td>
														<td><?php echo $rsm['nama_cabang']; ?></td>
													</tr>
													<tr>
														<td>Area</td>
														<td><?php echo $rsm['nama_area']; ?></td>
													</tr>
													<tr>
														<td>Produk</td>
														<td><?php echo $rsm['jenis_produk'] . ' - ' . $rsm['merk_dagang']; ?></td>
													</tr>
													<tr>
														<td>Ongkos Angkut</td>
														<td><?php echo number_format($rsm['oa_kirim']); ?></td>
													</tr>
													<tr>
														<td>Volume</td>
														<td><?php echo number_format($rsm['volume_tawar']) . ' Liter'; ?></td>
													</tr>
													<tr>
														<td>Harga</td>
														<td><?php echo $harganya; ?></td>
													</tr>
													<tr>
														<td>Pembulatan</td>
														<td><?php echo $ket_pembulatan; ?></td>
													</tr>
													<?php if ($rsm['refund_tawar'] != 0) : ?>
														<tr>
															<td>Refund</td>
															<td><?php echo number_format($rsm['refund_tawar']); ?></td>
														</tr>
														<input type="hidden" id="refund_tawar" value="<?= $rsm['refund_tawar'] ?>">
													<?php endif ?>
												</table>
											</div>
										</div>
									</div>
								<?php } ?>
							</div>

							<div id="rincian-harga">
								<?php if ($action == "update") { ?>
									<?php
									$rincian_harga = '';
									$no = 1;

									foreach ($rincian as $arr1) {
										$nilai = $arr1['nilai'] ? $arr1['nilai'] . " %" : '';
										$biaya = (float)$arr1['biaya']; // pastikan berupa float
										$jenis = $arr1['rincian'];

										// Periksa apakah ada desimal (4 digit) yang tidak semuanya nol
										$biaya_parts = explode('.', number_format($biaya, 4, '.', ''));
										if (isset($biaya_parts[1]) && (int)$biaya_parts[1] > 0) {
											// Tampilkan dengan 4 angka desimal dan koma sebagai pemisah desimal
											$formatted_biaya = number_format($biaya, 4, '.', ',');
										} else {
											// Tampilkan tanpa koma desimal
											$formatted_biaya = number_format($biaya, 0, '.', ',');
										}

										$rincian_harga .= '
									<tr>
										<td class="text-center">' . $no++ . '</td>
										<td>' . htmlspecialchars($jenis) . '</td>
										<td class="text-right">' . $nilai . '</td>
										<td class="text-right"><span style="float:left;">Rp.</span>' . $formatted_biaya . '</td>
									</tr>';
									}
									?>
									<div class="row">
										<div class="col-md-offset-2 col-sm-6">
											<div class="table-responsive">
												<table class="table table-bordered">
													<thead>
														<th class="text-center" width="10%">NO</th>
														<th class="text-center" width="20%">RINCIAN</th>
														<th class="text-center" width="10%">NILAI</th>
														<th class="text-center" width="30%">HARGA</th>
													</thead>
													<tbody>
														<?= $rincian_harga ?>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								<?php } ?>
							</div>

							<input type="hidden" id="refund_tawar" value="">

							<?php if ($action == "update") : ?>
								<?php if (date("Y-m-d") >= '2024-10-07') : ?>
									<div id="div-penerima-refund" class="<?= $rsm['refund_tawar'] != 0 ? '' : 'hide' ?>">
										<div class="row">
											<div class="col-md-12">
												<div class="form-group form-group-md">
													<label class="control-label col-md-2">Penerima Refund *</label>
													<div class="col-md-6">
														<div class="table-responsive">
															<table class="table table-penerima-refund">
																<thead>
																	<tr>
																		<th class="text-center" width="20%">Penerima Refund</th>
																		<th class="text-center" width="10%">Terima refund (per Liter)</th>
																		<th class="text-center" width="10%">Total</th>
																		<th class="text-center" width="5%">
																			<button class="btn btn-action btn-primary addRowEdit" type="button"><i class="fa fa-plus"></i></button>
																		</th>
																	</tr>
																</thead>
																<tbody>
																	<?php foreach ($result_poc_penerima_refund as $i => $key) : ?>
																		<tr>
																			<td>
																				<select name="penerima_refund[]" id="penerima_refund" class="form-control penerima_refund" required>
																					<option value="">Pilih Salah Satu</option>
																					<?php foreach ($rsm_penerima_refund as $key2) : ?>
																						<option <?= $key['penerima_refund'] == $key2['id'] ? 'selected' : '' ?> value="<?= $key2['id'] ?>"><?= $key2['nama'] ?></option>
																					<?php endforeach ?>
																				</select>
																			</td>
																			<td>
																				<input type="text" name="terima_refund[]" id="terima_refund1" class="form-control terima_refund text-right" value="<?= $key['persentasi_refund'] ?>" required maxlength="3">
																			</td>
																			<td>
																				<input type="text" name="total_terima_refund[]" id="total_terima_refund<?= $i + 1 ?>" class="form-control total_terima_refund text-right" value="<?= number_format($key['persentasi_refund'] * $rsm['volume_poc']) ?>" readonly>
																			</td>
																			<td align="center" class="<?= $i == 0 ? 'hide' : '' ?>">
																				<button class="btn btn-action btn-danger delRow" type="button"><i class="fa fa-minus"></i></button>
																			</td>
																		</tr>
																	<?php endforeach ?>
																</tbody>
															</table>
															<span><b>Note : Ada perubahan pada input penerima refund pada PO, sekarang jika ingin memasukan penerima refund pada kolom terima refund, masukkan angka terima Per liter nya untuk masing-masing penerima. (Pastikan isi jumlah volume terlebih dahulu, agar total bisa muncul)</b></span>
														</div>
													</div>
												</div>
											</div>
											<div class="col-md-12">
												<div class="form-group form-group-md">
													<label class="control-label col-md-2"></label>
													<div class="col-md-6">
														<a href="<?= $linkAddPenerima ?>" id="BtnAddPenerima" target="_blank" class="btn btn-primary btn-sm">Add Penerima</a>
													</div>
												</div>
											</div>
										</div>
									</div>
								<?php endif ?>
							<?php else : ?>
								<?php if (date("Y-m-d") >= '2024-10-07') : ?>
									<div id="div-penerima-refund" class="<?= $rsm['refund_tawar'] != 0 ? '' : 'hide' ?>">
										<div class="row">
											<div class="col-md-12">
												<div class="form-group form-group-md">
													<label class="control-label col-md-2">Penerima Refund *</label>
													<div class="col-md-8">
														<div class="table-responsive">
															<table class="table table-penerima-refund">
																<thead>
																	<tr>
																		<th class="text-center" width="20%">Penerima Refund</th>
																		<th class="text-center" width="10%">Terima refund (per Liter)</th>
																		<th class="text-center" width="10%">Total</th>
																		<th class="text-center" width="5%">
																			<button class="btn btn-action btn-primary addRow" type="button"><i class="fa fa-plus"></i></button>
																		</th>
																	</tr>
																</thead>
																<tbody>
																	<tr>
																		<td>
																			<select name="penerima_refund[]" id="penerima_refund" class="form-control penerima_refund" required>
																				<option value="">Pilih Salah Satu</option>
																			</select>
																		</td>
																		<td>
																			<input type="text" name="terima_refund[]" id="terima_refund1" class="form-control terima_refund text-right" required maxlength="3">
																		</td>
																		<td>
																			<input type="text" name="total_terima_refund[]" id="total_terima_refund1" class="form-control total_terima_refund text-right" readonly>
																		</td>
																	</tr>
																</tbody>
															</table>
															<span><b>Note : Ada perubahan pada input penerima refund pada PO, sekarang jika ingin memasukan penerima refund pada kolom terima refund, masukkan angka terima Per liter nya untuk masing-masing penerima. (Pastikan isi jumlah volume terlebih dahulu, agar total bisa muncul)</b></span>
														</div>
													</div>
												</div>
											</div>
											<div class="col-md-12">
												<div class="form-group form-group-md">
													<label class="control-label col-md-2"></label>
													<div class="col-md-6">
														<a href="<?= $linkAddPenerima ?>" id="BtnAddPenerima" target="_blank" class="btn btn-primary btn-sm">Add Penerima</a>
													</div>
												</div>
											</div>
										</div>
										<!-- <div class="row">
											<div class="col-md-6">
												<div class="form-group form-group-md">
													<label class="control-label col-md-4">Penerima Refund *</label>
													<div class="col-md-5">
														<select name="penerima_refund[]" id="penerima_refund" class="form-control select2" multiple="multiple" required>
														</select>
														<span>
															Bisa pilih lebih dari 1 (Max 3)
														</span>
													</div>
													<div class="col-md-2">
														<a href="<?= $linkAddPenerima ?>" id="BtnAddPenerima" target="_blank" class="btn btn-primary btn-sm">Add Penerima</a>
													</div>
												</div>
											</div>
										</div> -->
									</div>
								<?php endif ?>
							<?php endif ?>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">Nomor PO *</label>
										<div class="col-md-8">
											<?php
											if (isset($rsm['nomor_poc'])) {
												$readonly = (!$rsm['disposisi_poc'] || $rsm['poc_approved'] == 2 ? '' : 'readonly');
												echo '<input type="text" id="nomor_po" name="nomor_po" class="form-control" required value="' . $rsm['nomor_poc'] . '" ' . $readonly . ' />';
											} else {
												echo '<input type="text" id="nomor_po" name="nomor_po" class="form-control" required />';
											}
											?>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">Tanggal PO *</label>
										<div class="col-md-4">
											<?php
											if (isset($rsm['tanggal_poc'])) {
												$readonly = (!$rsm['disposisi_poc'] || $rsm['poc_approved'] == 2 ? '' : 'readonly');
												$nilainya = tgl_indo($rsm['tanggal_poc'], 'normal', 'db', '/');
												echo '<input type="text" id="tanggal_po" name="tanggal_po" class="form-control datepicker" autocomplete="off" required value="' . $nilainya . '" ' . $readonly . ' />';
											} else {
												$readonly = ($idr != '' && $idk != '' ? 'readonly' : '');
												echo '<input type="text" id="tanggal_po" name="tanggal_po" class="form-control datepicker" autocomplete="off" required data-rule-dateNL="1" ' . $readonly . ' />';
											}
											?>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">Tgl Pengiriman *</label>
										<div class="col-md-4">
											<?php
											if (isset($rsm['supply_date'])) {
												$readonly = (!$rsm['disposisi_poc'] || $rsm['poc_approved'] == 2 ? '' : 'readonly');
												$nilainya = tgl_indo($rsm['supply_date'], 'normal', 'db', '/');
												echo '<input type="text" id="supply_date" name="supply_date" class="form-control datepicker" autocomplete="off" required value="' . $nilainya . '" ' . $readonly . ' />';
											} else {
												$readonly = ($idr != '' && $idk != '' ? 'readonly' : '');
												echo '<input type="text" id="supply_date" name="supply_date" class="form-control datepicker" autocomplete="off" required data-rule-dateNL="1" ' . $readonly . ' />';
											}
											?>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">Produk *</label>
										<div class="col-md-8">
											<?php
											$cek_poc = isset($rsm['disposisi_poc']) ? $rsm['disposisi_poc'] : '';
											$cek_app = isset($rsm['poc_approved']) ? $rsm['poc_approved'] : '';
											if (!$cek_poc || $cek_app == 2) {
												echo '<select id="produk" name="produk" class="form-control select2" required>';
												echo '<option></option>';
												$con->fill_select("id_master", "concat(jenis_produk,' - ',merk_dagang)", "pro_master_produk", $rsm['produk_poc'], "where is_active =1", "id_master", false);
												echo '</select>';
											} else {
												echo '<input type="hidden" name="produk" id="produk" value="' . $rsm['produk_poc'] . '" />';
												echo '<input type="text" name="produkTxt" id="produkTxt" class="form-control" readonly value="' . $rsm['jenis_produk'] . ' - ' . $rsm['merk_dagang'] . '" />';
											}
											?>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">Harga/Liter *</label>
										<div class="col-md-6">
											<?php
											if (isset($rsm['harga_poc'])) {
												$readonly = (!$rsm['disposisi_poc'] || $rsm['poc_approved'] == 2 ? '' : 'readonly');

												$nilainya = ($rsm['harga_poc'] ? $rsm['harga_poc'] : "");

												if ($rsm['pembulatan'] == 0) {
													$formated_nilai = number_format($nilainya, 2);
												} elseif ($rsm['pembulatan'] == 1) {
													$formated_nilai = number_format($nilainya, 0);
												} else {
													$formated_nilai = number_format($nilainya, 4);
												}

												echo '<input type="text" id="harga_liter" name="harga_liter" class="form-control text-right" required value="' . $formated_nilai . '" ' . $readonly . ' readonly/>';
												echo '<input type="hidden" id="harga_liter2" name="harga_liter2" class="form-control text-right" required value="' . $rsm['harga_poc'] . '" ' . $readonly . ' readonly/>';
											} else {
												$readonly = ($idr != '' && $idk != '' ? 'readonly' : '');
												echo '<input type="text" id="harga_liter" name="harga_liter" class="form-control text-right" required ' . $readonly . ' readonly/>';
											}
											?>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">Jumlah Volume *</label>
										<div class="col-md-6">
											<div class="input-group">
												<?php
												if (isset($rsm['volume_poc'])) {
													$readonly = (!$rsm['disposisi_poc'] || $rsm['poc_approved'] == 2 ? '' : 'readonly');
													$nilainya = ($rsm['volume_poc'] ? $rsm['volume_poc'] : "");
													echo '<input type="text" id="total_volume" name="total_volume" class="form-control hitung" required value="' . $nilainya . '" ' . $readonly . ' />';
												} else {
													$readonly = ($idr != '' && $idk != '' ? 'readonly' : '');
													echo '<input type="text" id="total_volume" name="total_volume" class="form-control hitung" required ' . $readonly . ' />';
												}
												?>
												<span class="input-group-addon">Liter</span>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">Total Order</label>
										<div class="col-md-6">
											<?php
											if (isset($rsm['volume_poc'])) {
												$nilainya = ($rsm['volume_poc'] && $rsm['harga_poc'] ? $rsm['volume_poc'] * $rsm['harga_poc'] : "");
												echo '<input type="text" id="total_order" name="total_order" class="form-control hitung" value="' . $nilainya . '" readonly />';
											} else {
												echo '<input type="text" id="total_order" name="total_order" class="form-control hitung" readonly />';
											}
											?>
										</div>
									</div>
								</div>
							</div>

							<?php if (!$lsClosePo) { ?>
								<div class="form-group row">
									<div class="col-sm-12">
										<?php
										$lamp = $rsm['lampiran_poc'] ? $rsm['lampiran_poc'] : '';
										if ($lamp && file_exists($pathPt)) {
											$linkPt = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=2&ktg=POC_" . $idk . "_&file=" . $lampPt);
											$attr01 = '';
											echo '<label>Ubah Lampiran</label>';
											echo '<p><a href="' . $linkPt . '"><i class="fa fa-file-alt jarak-kanan"></i>' . $lampPt . '</a></p>';
										} else {
											$attr01 = 'required';
											echo '<label>Lampiran</label>';
										}
										?>
										<input type="file" name="attachment_order" id="attachment_order" class="" <?php echo $attr01; ?> /></td>
										<p style="font-size:12px;" class="help-block">* Max size 2Mb | .jpg, .png, .rar, .pdf</p>
									</div>
								</div>
							<?php } ?>

							<?php if ($lsClosePo) { ?>
								<div class="form-group row">
									<div class="col-sm-3">
										<label>Kode Dokumen : <?php echo "PO-" . $rowPlan['kode_po']; ?></label>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-sm-3">
										<label>Terkirim : <?php echo number_format($rowPlan['realisasi']) . " Liter"; ?></label>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-sm-3">
										<label>Sisa Buku : <?php echo number_format(($rowPlan['volume_poc'] - $rowPlan['vol_plan'])) . " Liter"; ?></label>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-sm-3">
										<label>Tanggal Close PO *</label>
										<input type="text" id="tanggal_close" name="tanggal_close" class="form-control datepicker validate[required,custom[date]]" value="<?php echo tgl_indo($rowClosePo['tgl_close'], 'normal', 'db', '/'); ?>" autocomplete='off' />
									</div>
									<div class="col-sm-3 col-sm-top">
										<label>Volume *</label>
										<div class="input-group">
											<input type="text" id="volume_close" name="volume_close" class="form-control hitung validate[required,funcCall[maxnya[<?php echo $rowClosePo['volume_poc']; ?>]]]" readonly value="<?php echo number_format(($rowPlan['volume_poc'] - $rowPlan['vol_plan'])) . " Liter"; ?>" />
											<span class="input-group-addon">Liter</span>
										</div>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-sm-8">
										<label>Catatan</label>
										<input type="text" id="catatan_close" name="catatan_close" class="form-control" value="<?php echo $rowClosePo['keterangan']; ?>" />
									</div>
								</div>
								<div class="form-group row">
									<div class="col-sm-12">
										<?php
										$lamp = isset($rowClosePo['lampiran_close_po']) ? $rowClosePo['lampiran_close_po'] : '';
										if ($lamp && file_exists($pathPtClose)) {
											$linkPt = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=2&ktg=POC_" . $idk . "_&file=" . $lampPtClose);
											echo '<label>Ubah Lampiran</label>';
											echo '<p><a href="' . $linkPt . '"><i class="fa fa-file-alt jarak-kanan"></i>' . $lampPtClose  . '</a></p>';
										} else {
											echo '<label>Lampiran Close PO</label>';
										}
										?>
										<input type="file" name="attachment_order" id="attachment_order" class="validate[funcCall[fileCheck]]" /></td>
										<p style="font-size:12px;" class="help-block">* Max size 2Mb | .jpg, .png, .rar, .pdf</p>
									</div>
								</div>
							<?php } ?>

							<hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

							<div style="margin-bottom:15px;">
								<input type="hidden" id="not_yet" name="not_yet" value="<?php echo $rsm['not_yet']; ?>" />
								<input type="hidden" id="ov_up_07" name="ov_up_07" value="<?php echo $rsm['ov_up_07']; ?>" />
								<input type="hidden" id="ov_under_30" name="ov_under_30" value="<?php echo $rsm['ov_under_30']; ?>" />
								<input type="hidden" id="ov_under_60" name="ov_under_60" value="<?php echo $rsm['ov_under_60']; ?>" />
								<input type="hidden" id="ov_under_90" name="ov_under_90" value="<?php echo $rsm['ov_under_90']; ?>" />
								<input type="hidden" id="ov_up_90" name="ov_up_90" value="<?php echo $rsm['ov_up_90']; ?>" />
								<input type="hidden" id="reminding" name="reminding" value="<?php echo $rsm['reminding']; ?>" />

								<input type="hidden" name="act" value="<?php echo $action; ?>" />
								<input type="hidden" name="closepo" value="<?php echo $lsClosePo; ?>" />
								<input type="hidden" name="attachment" value="<?php echo $lsAttachment; ?>" />
								<input type="hidden" name="idr" value="<?php echo $idr; ?>" />
								<input type="hidden" name="idk" value="<?php echo $idk; ?>" />

								<?php
								$status_disabled = "";
								$tgl_sekarang = strtotime(date("Y-m-d H:i:s"));
								$wilayah = paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]);
								if ($wilayah == '4' || $wilayah == '7') {
									// Samarinda, Banjarmasin, zona WITA +1 dari WIB
									$waktu_sekarang = date("H:i:s", strtotime("+1 hour"));
									// $waktu_sekarang = date("H:i:s");
									$waktu_tutup = date("16:01:00");
									$zona_waktu = "WITA";
									$waktu_buka = date("06:59:00");
									$tgl_buka = date("Y-m-d 06:59:00", strtotime("+15 hour", $tgl_sekarang));
								} else {
									$waktu_sekarang = date("H:i:s");
									$waktu_tutup = date("15:01:00");
									$zona_waktu = "WIB";
									$waktu_buka = date("06:59:00");
									$tgl_buka = date("Y-m-d 06:59:00", strtotime("+16 hour", $tgl_sekarang));
								}

								if ($waktu_sekarang >= $waktu_buka && $waktu_sekarang <= $waktu_tutup) {
									$status_disabled = "";
								} elseif ($waktu_sekarang >= $waktu_tutup || $waktu_sekarang < $waktu_buka) {
									$status_disabled = "Jika akan dilanjutkan ke SC, maka baru bisa dilanjutkan di tanggal " . $tgl_buka;
								}
								?>

								<button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">
									<i class="fa fa-save jarak-kanan"></i> Simpan</button>
								<button type="button" class="btn btn-default jarak-kanan" onClick="history.back()" style="min-width:90px;">
									<i class="fa fa-reply jarak-kanan"></i> Kembali</button>
							</div>
							<p><?= $status_disabled ?></p>
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

	<style type="text/css">
		.table>tr>td {
			font-size: 12px;
			padding: 5px;
		}
	</style>

	<script>
		$(document).ready(function() {

			function formatNumber(value) {
				const formatter = new Intl.NumberFormat('id-ID', { // Using 'id-ID' for Indonesian locale (you can change it to your desired locale)
					// style: 'decimal',
					// maximumFractionDigits: 2,
					// minimumFractionDigits: 2
				});

				return formatter.format(value);
			}

			$(".hitung, .terima_refund").number(true, 0, ".", ",");

			// Global variable to store the options for the select
			let selectOptions = [];

			$("select#penawaran").change(function() {
				var id_customer = $("#customer").val();
				if ($(this).val() != "" && $(this).val() != null) {
					$("#loading_modal").modal({
						keyboard: false,
						backdrop: 'static'
					});
					$.ajax({
						type: 'POST',
						url: "./__get_data_penawaran.php",
						dataType: "json",
						data: {
							q1: $(this).val(),
							id_customer: id_customer,
						},
						cache: false,
						success: function(data) {
							// console.log(data)
							$("#ket-penawaran").html(data.items);
							$("#rincian-harga").html(data.rincian);
							$("#produk").val(data.produk).trigger('change');
							$("#refund_tawar").val(data.refund).trigger('change');
							$("#harga_liter").val(data.harga);
							if (data.refund != 0) {
								$("#div-penerima-refund").removeClass("hide");
								// $(".penerima_refund").attr("required");
								$("#BtnAddPenerima").attr("href", data.addPenerimRefund);

								// if (data.penerima_refund != "") {
								// 	$("select#penerima_refund").select2({
								// 		data: data.penerima_refund,
								// 		placeholder: "Pilih salah satu",
								// 		allowClear: true
								// 	});
								// 	return false;
								// }
								// Menambahkan data array ke Select2
								// $.each(data.penerima_refund, function(index, item) {
								// 	const newOption = new Option(item.nama, item.id, false, false);
								// 	$('.penerima_refund').append(newOption);
								// });

								// // Initialize Select2 after options are added
								// $('.penerima_refund').select2({
								// 	placeholder: "Pilih salah satu",
								// 	allowClear: true
								// });

								// Store select options globally
								selectOptions = data.penerima_refund.map(item => ({
									id: item.id,
									name: item.nama
								}));

								// Initialize Select2 for existing rows
								populateSelectOptions();
							} else {
								// $(".penerima_refund").removeAttr("required");
								$("#BtnAddPenerima").removeClass("href", "#");
								$("#div-penerima-refund").addClass("hide");
							}
							calculate_order();
						}
					});
					$("#loading_modal").modal("hide");
				} else {
					$("#ket-penawaran").html("");
					$("#rincian-harga").html("");
					$("#produk").val("").trigger('change');
					$("#harga_liter").val("");
					calculate_order();
				}
			});

			// Function to validate unique selection in all .penerima_refund selects
			function validateUniqueSelects() {
				const selectedValues = [];

				// Collect all selected values from .penerima_refund selects
				$('.penerima_refund').each(function() {
					const selectedValue = $(this).val();
					if (selectedValue) {
						selectedValues.push(selectedValue);
					}
				});

				// Check for duplicates
				$('.penerima_refund').each(function() {
					const currentSelect = $(this);
					const selectedValue = currentSelect.val();

					// If the current value is duplicated in the selected values
					if (selectedValue && selectedValues.filter(val => val === selectedValue).length > 1) {
						// Alert user or reset the select
						alert("Penerima Refund ini telah dipilih. Silakan pilih yang lain.");
						currentSelect.val("").trigger('change'); // Reset the current select
					}
				});
			}

			// Attach the change event listener to all .penerima_refund selects
			$('.table-penerima-refund').on('change', '.penerima_refund', function() {
				validateUniqueSelects();
			});

			// Function to add a new row
			$('.table-penerima-refund').on('click', '.addRow', function() {
				// Count the current number of rows
				var currentRowCount = $('.table-penerima-refund tbody tr').length;
				var row = currentRowCount + 1;
				// Check if the current row count is less than 3
				if (currentRowCount < 3) {
					// Create new row HTML
					var newRow = `
					<tr>
						<td>
							<select name="penerima_refund[]" class="form-control penerima_refund" required>
								<option value="">Pilih Salah Satu</option>
							</select>
						</td>
						<td>
							<input type="text" name="terima_refund[]" class="form-control terima_refund text-right" id="terima_refund` + row + `" required maxlength="3">
						</td>
						<td>
							<input type="text" name="total_terima_refund[]" id="total_terima_refund` + row + `" class="form-control total_terima_refund text-right" value="0" readonly>
						</td>
						<td align="center">
							<button class="btn btn-action btn-danger delRow" type="button"><i class="fa fa-minus"></i></button>
						</td>
					</tr>`;

					// Append the new row to the table body
					$('.table-penerima-refund tbody').append(newRow);

					// Initialize select2 on the new select element
					// $('.select2').select2({
					// 	placeholder: "Pilih salah satu",
					// });

					// Populate select options in the new row
					populateSelectOptions();
				} else {
					Swal.fire({
						title: "Ooppss",
						text: "Maksimal hanya 3 penerima refund",
						icon: "warning"
					});
				}
			}).on('click', '.addRowEdit', function() {
				var q1 = $("#penawaran").val();
				var id_customer = $("#customer").val();
				// Count the current number of rows
				var currentRowCount = $('.table-penerima-refund tbody tr').length;
				var row = currentRowCount + 1;
				// Check if the current row count is less than 3
				if (currentRowCount < 3) {
					// Create new row HTML
					var newRow = `
					<tr>
						<td>
							<select name="penerima_refund[]" class="form-control penerima_refund" required>
								<option value="">Pilih Salah Satu</option>
							</select>
						</td>
						<td>
							<input type="text" name="terima_refund[]" class="form-control terima_refund text-right" id="terima_refund` + row + `" required maxlength="3">
						</td>
						<td>
							<input type="text" name="total_terima_refund[]" id="total_terima_refund` + row + `" class="form-control total_terima_refund text-right" value="0" readonly>
						</td>
						<td align="center">
							<button class="btn btn-action btn-danger delRow" type="button"><i class="fa fa-minus"></i></button>
						</td>
					</tr>`;

					// Append the new row to the table body
					$('.table-penerima-refund tbody').append(newRow);

					$.ajax({
						type: 'POST',
						url: "./__get_data_penawaran.php",
						dataType: "json",
						data: {
							q1: q1,
							id_customer: id_customer,
						},
						cache: false,
						success: function(data) {
							console.log(q1)
							if (data.refund != 0) {
								$(".penerima_refund").last().empty().append('<option selected="selected" value="">Pilih Salah Satu</option>');
								$.each(data.penerima_refund, function(index, item) {
									const newOption = new Option(item.nama, item.id, false, false);
									$('.penerima_refund').last().append(newOption);
								});
							}
						}
					});

					$(".total_terima_refund").val("");
				} else {
					Swal.fire({
						title: "Ooppss",
						text: "Maksimal hanya 3 penerima refund",
						icon: "warning"
					});
				}
			}).on('click', '.delRow', function() {
				var row = $(this).closest('tr');
				var rowCount = $('table.table-penerima-refund tbody tr').length;
				row.remove();
			}).on('input blur', '#terima_refund1', function() {
				var val = $(this).val();
				var volume = $("#total_volume").val();

				if (volume == "") {
					var volume_fix = 0;
				} else {
					var volume_fix = parseFloat($("#total_volume").val());
				}

				var total = val * volume_fix;

				$("#total_terima_refund1").val(formatNumber(total))

				calculateTotalRefund(val);
				// var refund_tawar = parseFloat($("#refund_tawar").val()); // Ambil nilai refund_tawar
				// var total_refund = calculateTotalRefund(); // Hitung total terima refund

				// // Validasi jika total terima refund melebihi refund tawar
				// if (total_refund > refund_tawar) {
				// 	$(".terima_refund").val(""); // Kosongkan semua input terima_refund
				// 	$(".total_terima_refund").val(""); // Kosongkan semua input terima_refund
				// 	Swal.fire({
				// 		title: "Ooppss",
				// 		text: "Terima refund per liter tidak boleh melebihi refund tawar pada penawaran",
				// 		icon: "warning"
				// 	});
				// }

				if (!/[0-9]/.test(String.fromCharCode(event.which)) && event.which !== 8) {
					event.preventDefault();
				}
			}).on('input blur', '#terima_refund2', function() {
				var val = $(this).val();
				var volume = $("#total_volume").val();

				if (volume == "") {
					var volume_fix = 0;
				} else {
					var volume_fix = parseFloat($("#total_volume").val());
				}

				var total = val * volume_fix;

				$("#total_terima_refund2").val(formatNumber(total))

				if (!/[0-9]/.test(String.fromCharCode(event.which)) && event.which !== 8) {
					event.preventDefault();
				}

				calculateTotalRefund(val);
			}).on('input blur', '#terima_refund3', function() {
				var val = $(this).val();
				var volume = $("#total_volume").val();

				if (volume == "") {
					var volume_fix = 0;
				} else {
					var volume_fix = parseFloat($("#total_volume").val());
				}

				var total = val * volume_fix;

				$("#total_terima_refund3").val(formatNumber(total))
				if (!/[0-9]/.test(String.fromCharCode(event.which)) && event.which !== 8) {
					event.preventDefault();
				}

				calculateTotalRefund(val);
			})

			// Fungsi untuk menghitung total refund
			function calculateTotalRefund(refund) {
				let total = 0;
				var refund_tawar = parseFloat($("#refund_tawar").val());
				$(".terima_refund").each(function() {
					let value = parseFloat($(this).val()) || 0; // Pastikan nilai yang diambil adalah angka
					total += value;
				});

				if (total > refund_tawar) {
					$(".terima_refund").val(""); // Kosongkan semua input terima_refund
					$(".total_terima_refund").val(""); // Kosongkan semua input terima_refund
					Swal.fire({
						title: "Ooppss",
						text: "Terima refund per liter tidak boleh melebihi refund tawar pada penawaran",
						icon: "warning"
					});
				}
			}

			// // Fungsi untuk menghitung total terima refund
			// function calculateTotalTerimaRefund() {
			// 	var volume = parseFloat($("#total_volume").val());
			// 	let total_terima = 0; // Mulai total dengan 0
			// 	$(".terima_refund").each(function() {
			// 		let value = parseFloat($(this).val()) || 0; // Pastikan nilai yang diambil adalah angka
			// 		total_terima += value * volume; // Tambahkan hasil perkalian ke total_terima
			// 		$(".total_terima_refund").val(total_terima);
			// 	});
			// 	return total_terima; // Kembalikan total yang telah dihitung
			// }

			// Function to populate select options
			function populateSelectOptions() {
				// Find all select elements with the class .penerima_refund
				$('.penerima_refund').each(function() {
					// Clear existing options
					$(".penerima_refund").last().empty().append('<option selected="selected" value="">Pilih Salah Satu</option>');

					// Append options from selectOptions
					selectOptions.forEach(option => {
						const newOption = new Option(option.name, option.id, false, false);
						$('.penerima_refund').last().append(newOption);
					});
				});
				$('.terima_refund').each(function() {
					// Clear existing options
					$(this).val("");
					$(".terima_refund").number(true, 0, ".", ",");
				});
				$("#total_terima_refund1").val("");
				$("#total_terima_refund2").val("");
				$("#total_terima_refund3").val("");
			}

			// $(".terima_refund")

			// $('#penerima_refund').select2({
			// 	maximumSelectionLength: 3, // Batasan maksimal 3 pilihan
			// 	placeholder: "Pilih item",
			// 	allowClear: true
			// });
			// $("#penerima_refund").change(function() {
			// 	const selectedValues = $(this).val(); // Mendapatkan nilai yang dipilih
			// 	$('#selectedValues').empty(); // Mengosongkan sebelumnya
			// 	if (selectedValues) {
			// 		$('#selectedValues').append('<strong>Nilai yang dipilih:</strong> ' + selectedValues.join(', '));
			// 	} else {
			// 		$('#selectedValues').append('<strong>Nilai yang dipilih:</strong> Tidak ada');
			// 	}
			// });

			<?php if ($idc) { ?>
				setKreditLimit('<?php echo $idc; ?>');
			<?php } ?>

			var formValidasiCfg = {
				submitHandler: function(form) {
					var total_order = $("#total_order").val();
					var not_yet = $("#not_yet").val();
					var ov_up_07 = $("#ov_up_07").val();
					var ov_under_30 = $("#ov_under_30").val();
					var ov_under_60 = $("#ov_under_60").val();
					var ov_under_90 = $("#ov_under_90").val();
					var ov_up_90 = $("#ov_up_90").val();
					var reminding = $("#reminding").val();

					Swal.fire({
						title: "Anda yakin simpan?",
						showCancelButton: true,
						confirmButtonText: "Ya",
					}).then((result) => {
						if (result.isConfirmed) {
							$("#loading_modal").modal({
								keyboard: false,
								backdrop: 'static'
							});
							if ($("#cekkolnup").is(":checked") && $("#nup_fee").val() == "") {
								$("#loading_modal").modal("hide");
								$.validator.showErrorField('nup_fee', "Kolom ini belum diisi atau dipilih");
								setErrorFocus($("#nup_fee"), $("form#gform"), false);
							} else {
								$.ajax({
									type: "POST",
									url: "./__cek_po_customer.php",
									dataType: "json",
									data: $(form).serializeArray(),
									cache: false,
									success: function(data) {
										if (data.error) {
											$("#preview_modal").find("#preview_alert").html(data.error);
											$("#preview_modal").modal();
											$("#loading_modal").modal("hide");
											return false;
										} else {
											form.submit();
											// var unblock = false;
											// if (total_order > reminding) unblock = true;
											// if (ov_up_07 > 0 || ov_under_30 > 0 || ov_under_60 > 0 || ov_under_90 > 0 || ov_up_90 > 0) unblock = true;
											// if (unblock) {
											// 	$("#loading_modal").modal("hide");
											// 	swal.fire({
											// 		title: '<div style="font-weight:400; font-size:16px; line-height:25px;">Terdapat Proses Unblock pada PO untuk cutomer ini. Apakah anda yakin tetap menyimpan data?</div>',
											// 		icon: 'warning',
											// 		showCancelButton: true,
											// 		confirmButtonColor: '#3085d6',
											// 		cancelButtonColor: '#d33',
											// 		confirmButtonText: 'Ya',
											// 		cancelButtonText: 'Tidak',
											// 	}).then((result) => {
											// 		if (result.isConfirmed) {
											// 			$("#loading_modal").modal({
											// 				keyboard: false,
											// 				backdrop: 'static'
											// 			});
											// 			form.submit();
											// 		}
											// 	});
											// } else {
											// 	form.submit();
											// }
										}
									}
								});
							}
						}
					});
				}
			};
			$("form#gform").validate($.extend(true, {}, config.validation, formValidasiCfg));

			$("select#customer").change(function() {
				$("#loading_modal").modal({
					keyboard: false,
					backdrop: 'static'
				});
				$("select#penawaran").val("").trigger('change').select2('close');
				// $("select#penerima_refund").val("").trigger('change').select2('close');
				$("select#penawaran option").remove();
				// $("select#penerima_refund option").remove();
				$("#div-penerima-refund").addClass("hide");
				$("#top").val("");
				if ($(this).val() != "") {
					$.ajax({
						type: "POST",
						url: "./__get_top_customer.php",
						dataType: "json",
						data: {
							q1: $(this).val()
						},
						cache: false,
						success: function(data) {
							$("#top").val(data.top_payment)
							$('#credit_limit').val(data.credit_limit)
							let html =
								'<table border="1" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin-bottom:15px;">' +
								'<tr>' +
								'<td width="150" style="padding:3px 5px; background-color: #ddd;">Credit Limit</td>' +
								'<td style="padding:3px 5px;">' + data.credit_limit + '</td>' +
								'<tr>' +
								'<td style="padding:3px 5px; background-color: #ddd;">Invoice not issued yet</td>' +
								'<td style="padding:3px 5px;">' + data.credit_limit_reserved + '</td>' +
								'</tr>' +
								'</tr>' +
								'<tr>' +
								'<td style="padding:3px 5px; background-color: #ddd;">AR Not yet</td>' +
								'<td style="padding:3px 5px;">' + data.not_yet + '</td>' +
								'</tr>' +
								'<tr>' +
								'<td style="padding:3px 5px; background-color: #ddd;">AR Overdue 1-7 days</td>' +
								'<td style="padding:3px 5px;">' + data.ov_up_07 + '</td>' +
								'</tr>' +
								'<tr>' +
								'<td style="padding:3px 5px; background-color: #ddd;">AR Overdue 8-30 days</td>' +
								'<td style="padding:3px 5px;">' + data.ov_under_30 + '</td>' +
								'</tr>' +
								'<tr>' +
								'<td style="padding:3px 5px; background-color: #ddd;">AR Overdue 31-60 days</td>' +
								'<td style="padding:3px 5px;">' + data.ov_under_60 + '</td>' +
								'</tr>' +
								'<tr>' +
								'<td style="padding:3px 5px; background-color: #ddd;">AR Overdue 61-90 days</td>' +
								'<td style="padding:3px 5px;">' + data.ov_under_90 + '</td>' +
								'</tr>' +
								'<tr>' +
								'<td style="padding:3px 5px; background-color: #ddd;">AR Overdue > 90 days</td>' +
								'<td style="padding:3px 5px;">' + data.ov_up_90 + '</td>' +
								'</tr>' +
								'<tr>' +
								'<td style="padding:3px 5px; background-color: #ddd;">Credit Limit Remaining</td>' +
								'<td style="padding:3px 5px;">' + data.reminding + '</td>' +
								'</tr>' +
								'</table>';
							if (data.items.length > 0)
								$('#keterangan_limit').html(html);

							$("#not_yet").val(data.nilai_not_yet);
							$("#ov_up_07").val(data.nilai_ov_up_07);
							$("#ov_under_30").val(data.nilai_ov_under_30);
							$("#ov_under_60").val(data.nilai_ov_under_60);
							$("#ov_under_90").val(data.nilai_ov_under_90);
							$("#ov_up_90").val(data.nilai_ov_up_90);
							$("#reminding").val(data.nilai_reminding);

							if (data.items != "") {
								$("select#penawaran").select2({
									data: data.items,
									placeholder: "Pilih salah satu",
									allowClear: true
								});
								return false;
							}
						}
					});
				}
				$("#loading_modal").modal("hide");
			});

			$("#total_volume, #harga_liter").on("change keyup blur", function() {
				calculate_order();
			});

			function calculate_order() {
				let nilai01 = $("#total_volume").val() * 1;
				let nilai02 = $("#harga_liter").val().replace(/,/g, '');
				nilai02 = parseFloat(nilai02) * 1;
				let nilai03 = nilai01 * nilai02;
				nilai03 = (nilai03 > 0) ? nilai03 : '';
				$("#total_order").val(nilai03);
			}

			function setKreditLimit(nilai) {
				$("#loading_modal").modal({
					keyboard: false,
					backdrop: 'static'
				});
				$("select#penawaran").val("").trigger('change').select2('close');
				$("select#penawaran option").remove();
				$("#top").val("");
				if (nilai != "") {
					$.ajax({
						type: "POST",
						url: "./__get_top_customer.php",
						dataType: "json",
						data: {
							q1: nilai
						},
						cache: false,
						success: function(data) {
							$("#top").val(data.top_payment)
							$('#credit_limit').val(data.credit_limit)
							let html =
								'<table border="1" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin-bottom:15px;">' +
								'<tr>' +
								'<td width="150" style="padding:3px 5px; background-color: #ddd;">Credit Limit</td>' +
								'<td style="padding:3px 5px;">' + data.credit_limit + '</td>' +
								'<tr>' +
								'<td style="padding:3px 5px; background-color: #ddd;">Invoice not issued yet</td>' +
								'<td style="padding:3px 5px;">' + data.credit_limit_reserved + '</td>' +
								'</tr>' +
								'</tr>' +
								'<tr>' +
								'<td style="padding:3px 5px; background-color: #ddd;">AR Not yet</td>' +
								'<td style="padding:3px 5px;">' + data.not_yet + '</td>' +
								'</tr>' +
								'<tr>' +
								'<td style="padding:3px 5px; background-color: #ddd;">AR Overdue 1-7 days</td>' +
								'<td style="padding:3px 5px;">' + data.ov_up_07 + '</td>' +
								'</tr>' +
								'<tr>' +
								'<td style="padding:3px 5px; background-color: #ddd;">AR Overdue 8-30 days</td>' +
								'<td style="padding:3px 5px;">' + data.ov_under_30 + '</td>' +
								'</tr>' +
								'<tr>' +
								'<td style="padding:3px 5px; background-color: #ddd;">AR Overdue 31-60 days</td>' +
								'<td style="padding:3px 5px;">' + data.ov_under_60 + '</td>' +
								'</tr>' +
								'<tr>' +
								'<td style="padding:3px 5px; background-color: #ddd;">AR Overdue 61-90 days</td>' +
								'<td style="padding:3px 5px;">' + data.ov_under_90 + '</td>' +
								'</tr>' +
								'<tr>' +
								'<td style="padding:3px 5px; background-color: #ddd;">AR Overdue > 90 days</td>' +
								'<td style="padding:3px 5px;">' + data.ov_up_90 + '</td>' +
								'</tr>' +
								'<tr>' +
								'<td style="padding:3px 5px; background-color: #ddd;">Credit Limit Remaining</td>' +
								'<td style="padding:3px 5px;">' + data.reminding + '</td>' +
								'</tr>' +
								'</table>';
							if (data.items.length > 0)
								$('#keterangan_limit').html(html);

							$("#not_yet").val(data.nilai_not_yet);
							$("#ov_up_07").val(data.nilai_ov_up_07);
							$("#ov_under_30").val(data.nilai_ov_under_30);
							$("#ov_under_60").val(data.nilai_ov_under_60);
							$("#ov_under_90").val(data.nilai_ov_under_90);
							$("#ov_up_90").val(data.nilai_ov_up_90);
							$("#reminding").val(data.nilai_reminding);

							if (data.items != "") {
								$("select#penawaran").select2({
									data: data.items,
									placeholder: "Pilih salah satu",
									allowClear: true
								});
								return false;
							}
						}
					});
				}
				$("#loading_modal").modal("hide");
			}

		});
	</script>
</body>

</html>
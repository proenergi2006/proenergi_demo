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
$sesrole 	= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);

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


// if ($model['total_bayar'] != 0) {
// 	$readonly = "readonly";
// } else {
// 	$readonly = "";
// }
$readonly = "readonly";

$sqlGetWil = "SELECT * FROM pro_master_cabang WHERE id_master='" . $seswil . "'";
$row = $con->getRecord($sqlGetWil);

$query_no_inv = "SELECT * FROM pro_invoice_admin WHERE no_invoice LIKE '%PE/" . $row['inisial_cabang'] . "%' ORDER BY no_invoice DESC";
$row2 = $con->getRecord($query_no_inv);

$arrRomawi 	= array("1" => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
$year 		= date("y");
$month 		= date("m");

if ($row2) {
	$no_invoice = $row2['no_invoice'];
	$explode = explode("/", $no_invoice);
	$year_inv = $explode[3];
	$month_inv = $explode[4];

	switch ($month_inv) {
		case "I":
			$bulan = '01';
			break;
		case "II":
			$bulan = '02';
			break;
		case "III":
			$bulan = '03';
			break;
		case "IV":
			$bulan = '04';
			break;
		case "V":
			$bulan = '05';
			break;
		case "VI":
			$bulan = '06';
			break;
		case "VII":
			$bulan = '07';
			break;
		case "VIII":
			$bulan = '08';
			break;
		case "IX":
			$bulan = '09';
			break;
		case "X":
			$bulan = '10';
			break;
		case "XI":
			$bulan = '11';
			break;
		case "XII":
			$bulan = '12';
			break;
	}

	if ($bulan == $month && $year_inv == $year) {
		$urut_inv = $explode[5] + 1;
		$no_inv = sprintf("%03s", $urut_inv);
		$noms_inv = 'SI/' . 'PE/' . $row['inisial_cabang'] . '/' . $year_inv . '/' . $arrRomawi[intval($bulan)] . '/' . $no_inv;
	} else {
		$urut_inv = 1;
		$no_inv = sprintf("%03s", $urut_inv);
		$noms_inv = 'SI/' . 'PE/' . $row['inisial_cabang'] . '/' . $year . '/' . $arrRomawi[intval(date("m"))] . '/' . $no_inv;
	}
} else {
	$urut_inv = 1;
	$no_inv = sprintf("%03s", $urut_inv);
	$noms_inv = 'SI/' . 'PE/' . $row['inisial_cabang'] . '/' . $year . '/' . $arrRomawi[intval(date("m"))] . '/' . $no_inv;
}

// Cek peran pengguna
$required_role = ['1', '2', '10', '25'];
// Misalnya halaman ini hanya untuk superadmin
if (!in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), $required_role)) {
	// Pengguna tidak memiliki peran yang tepat, redirect ke halaman lain atau tampilkan pesan akses ditolak
	$flash->add("warning", "Akses ditolak.", BASE_URL_CLIENT . "/home.php");
	// exit();
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
						<h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
					</div>
					<div class="box-body">
						<form action="<?php echo ACTION_CLIENT . '/invoice_customer.php'; ?>" id="gform" name="gform" method="post" class="form-validasi form-horizontal" role="form">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">Nama Customer *</label>
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
										<label class="control-label col-md-4">No Invoice *</label>
										<div class="col-md-8">
											<?php if ($seswil == '4' || $seswil == '5' || $seswil == '7') : ?>
												<input type="text" id="no_invoice" name="no_invoice" class="form-control" <?= $action == 'add' ? '' : 'readonly' ?> value="<?= $action == 'add' ? '' : $model['no_invoice'] ?>" />
											<?php else : ?>
												<input type="text" id="no_invoice" name="no_invoice" class="form-control" value="<?= $model['no_invoice'] ? $model['no_invoice'] : $noms_inv ?>" readonly />
											<?php endif ?>
											<?php if ($action == 'add') : ?>
												<span><i>* No. Invoice auto generate</i></span>
												<br>
												<input type="checkbox" id="next_month" name="next_month" value="next_month">
												<label for="next_month"> Next Month</label> <small><strong>(Nomor Invoice untuk bulan berikutnya)</strong></small>
											<?php endif ?>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">Tgl Invoice *</label>
										<div class="col-md-4">
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
												<?php $currval = ($model['tgl_invoice'] ? date("d/m/Y", strtotime($model['tgl_invoice'])) : ''); ?>
												<input type="text" id="tgl_invoice" name="tgl_invoice" class="form-control datepicker" value="<?php echo $currval; ?>" autocomplete="off" required />
											</div>
										</div>
									</div>
								</div>
							</div>
							<?php if ($action == 'add') : ?>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group form-group-sm">
											<label class="control-label col-md-4">Cetakan Invoice</label>
											<div class="col-md-6">
												<select name="cetakan_invoice" id="cetakan_invoice" class="form-control" <?= $action == 'add' ? '' : '' ?>>
													<option <?= $action == 'add' ? 'hide' : ($model['is_cetakan'] == 1 ? 'selected' : '') ?> value="1">Cetakan by Nomor Invoice SYOP</option>
													<option <?= $action == 'add' ? 'hide' : ($model['is_cetakan'] == 2 ? 'selected' : '') ?> value="2">Cetakan by Nomor Invoice Customer</option>
												</select>
											</div>
										</div>
									</div>
								</div>
							<?php endif ?>
							<div class="row <?= $action == 'add' ? 'hide' : ($model['is_cetakan'] == 1 || $model['is_cetakan'] == null ? 'hide' : '') ?>" id="row-inv-customer">
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">No Invoice Customer</label>
										<div class="col-md-8">
											<input type="text" id="no_invoice_customer" name="no_invoice_customer" class="form-control" value="<?= $action == 'add' ? '' : $model['no_invoice_customer'] ?>" />
										</div>
									</div>
								</div>
							</div>
							<?php if ($action == 'add') : ?>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group form-group-sm">
											<label class="control-label col-md-4">Split Invoice</label>
											<div class="col-md-6">
												<select name="split_invoice" id="split_invoice" class="form-control" required>
													<option value="all_in">Tidak di split</option>
													<option value="split_oa">Split OA</option>
													<option value="split_pbbkb">Split PBBKB</option>
													<option value="split_all">Split OA & PBBKB</option>
												</select>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group form-group-sm">
											<label class="control-label col-md-4">Invoice by *</label>
											<div class="col-md-4">
												<select name="tipe" id="tipe" class="form-control">
													<option value="">--PILIH--</option>
													<option value="tanggal">Tanggal</option>
													<option value="periode">Periode</option>
												</select>
											</div>
										</div>
									</div>
								</div>
								<div class="row" id="row-jenis-tanggal">
									<div class="col-md-6">
										<div class="form-group form-group-sm">
											<label class="control-label col-md-4">Periode by *</label>
											<div class="col-md-4">
												<input type="radio" id="delivered" name="jenis_tanggal" value="delivered" class="form-control"><label for="delivered">Tanggal Delivered</label>
											</div>
											<div class="col-md-4">
												<input type="radio" id="kirim" name="jenis_tanggal" value="kirim" class="form-control"><label for="kirim">Tanggal Kirim</label>
											</div>
										</div>
									</div>
								</div>
								<div class="row" id="row-tanggal">
									<div class="col-md-6">
										<div class="form-group form-group-sm">
											<label class="control-label col-md-4">Tanggal Delivered *</label>
											<div class="col-md-4">
												<div class="input-group">
													<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
													<?php $currval = ($model['tgl_kirim_awal'] ? date("d/m/Y", strtotime($model['tgl_kirim_awal'])) : ''); ?>
													<input type="text" id="tanggal" name="tanggal" class="form-control datepicker" value="<?php echo $currval; ?>" autocomplete="off" required />
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="row" id="row-periode">
									<div class="col-md-6">
										<div class="form-group form-group-sm">
											<label class="control-label col-md-4">Periode Awal *</label>
											<div class="col-md-4">
												<div class="input-group">
													<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
													<?php $currval = ($model['tgl_kirim_awal'] ? date("d/m/Y", strtotime($model['tgl_kirim_awal'])) : ''); ?>
													<input type="text" id="tgl_kirim_awal" name="tgl_kirim_awal" class="form-control datepicker" value="<?php echo $currval; ?>" autocomplete="off" required />
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group form-group-sm">
											<label class="control-label col-md-4">Periode Akhir *</label>
											<div class="col-md-4">
												<div class="input-group">
													<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
													<?php $currval = ($model['tgl_kirim_akhir'] ? date("d/m/Y", strtotime($model['tgl_kirim_akhir'])) : ''); ?>
													<input type="text" id="tgl_kirim_akhir" name="tgl_kirim_akhir" class="form-control datepicker" value="<?php echo $currval; ?>" autocomplete="off" required />
												</div>
											</div>
										</div>
									</div>
								</div>
							<?php endif ?>



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
											<th class="text-center" width="300">No PO Customer</th>
											<th class="text-center" width="200">Referensi</th>
											<th class="text-center" width="120">Tanggal</th>
											<th class="text-center" width="150">Volume Realisasi</th>
											<th class="text-center" width="200">Harga</th>
											<th class="text-center" width="180">Jumlah</th>
											<th class="text-center" width="70">Aksi</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$sql02 = "
										select 
										a.*, f.*, b.nomor_do as no_dn, k1.nomor_plat as angkutan, l1.nama_sopir as sopir, d.nomor_poc, b.realisasi_volume, ppd.no_do_acurate, ppd.no_do_syop, ppd.nomor_lo_pr, ppd.volume as volume_pr, g.pembulatan, b1.no_spj
										from pro_invoice_admin_detail a 
										join pro_po_ds_detail b on a.id_dsd = b.id_dsd and a.jenisnya = 'truck' 
										join pro_pr_detail ppd on b.id_prd = ppd.id_prd 
										join pro_po_customer_plan c on b.id_plan = c.id_plan 
										join pro_po_customer d on c.id_poc = d.id_poc 
										join pro_po_detail b1 on b.id_pod = b1.id_pod 
										join pro_master_transportir_mobil k1 on b1.mobil_po = k1.id_master 
										join pro_master_transportir_sopir l1 on b1.sopir_po = l1.id_master
										join pro_invoice_admin f on a.id_invoice=f.id_invoice
										join pro_penawaran g ON d.id_penawaran=g.id_penawaran
										where 1=1 and a.id_invoice = '" . $idr . "'
										UNION ALL 
										select 
										a.*, f.*, b.nomor_dn_kapal as no_dn, b.vessel_name as angkutan, b.kapten_name as sopir, e.nomor_poc, b.realisasi_volume, c.no_do_acurate, c.no_do_syop, c.nomor_lo_pr, c.volume as volume_pr, g.pembulatan, NULL as no_spj
										from pro_invoice_admin_detail a 
										join pro_po_ds_kapal b on a.id_dsd = b.id_dsk and a.jenisnya = 'kapal' 
										join pro_pr_detail c on b.id_prd = c.id_prd 
										join pro_po_customer_plan d on c.id_plan = d.id_plan 
										join pro_po_customer e on d.id_poc = e.id_poc
										join pro_invoice_admin f on a.id_invoice=f.id_invoice
										join pro_penawaran g ON e.id_penawaran=g.id_penawaran
										where 1=1 and a.id_invoice = '" . $idr . "' 
										order by id_invoice_detail  
									";
										$listData1 	= $con->getResult($sql02);

										$arrPengeluaran = (count($listData1) > 0) ? $listData1 : array();
										if (count($arrPengeluaran) > 0) {
											$no_urut = 0;
											$total_realisasi = 0;
											$total_invoice = 0;
											$jenis = "";
											foreach ($arrPengeluaran as $data1) {
												$no_urut++;

												$tgl_delivered 	= ($data1['tgl_delivered']) ? date('d/m/Y', strtotime($data1['tgl_delivered'])) : '';

												if (fmod($data1['vol_kirim'], 1) !== 0.0000) {
													$vol_kirim = ($data1['vol_kirim']) ? number_format((float)$data1['vol_kirim'], 4, '.', '') : '';
												} else {
													$vol_kirim = ($data1['vol_kirim']) ? number_format($data1['vol_kirim']) : '';
												}

												$realisasi_volume = ($data1['realisasi_volume']) ? number_format($data1['realisasi_volume']) : '';

												if ($data1['no_do_acurate'] == NULL) {
													$no_do = $data1['no_do_syop'];
												} else {
													$no_do = $data1['no_do_acurate'];
												}

												$sql02 	= "SELECT 
															a.*, 
															d.harga_dasar, 
															d.detail_rincian, 
															d.pembulatan, 
															d.refund_tawar, 
															d.id_penawaran,
															'truck' AS jenisnya
														FROM 
															pro_invoice_admin_detail a
														JOIN 
															pro_po_ds_detail b ON a.id_dsd = b.id_dsd
														JOIN 
															pro_po_customer c ON b.id_poc = c.id_poc
														JOIN 
															pro_penawaran d ON c.id_penawaran = d.id_penawaran
														WHERE 
															a.id_invoice = '" . $data1['id_invoice'] . "' 
															AND a.jenisnya = 'truck'

														UNION ALL

														SELECT 
															a.*, 
															d.harga_dasar, 
															d.detail_rincian, 
															d.pembulatan, 
															d.refund_tawar, 
															d.id_penawaran,
															'kapal' AS jenisnya
														FROM 
															pro_invoice_admin_detail a
														JOIN 
															pro_po_ds_kapal e ON a.id_dsd = e.id_dsk
														JOIN 
															pro_po_customer c ON e.id_poc = c.id_poc
														JOIN 
															pro_penawaran d ON c.id_penawaran = d.id_penawaran
														WHERE 
															a.id_invoice = '" . $data1['id_invoice'] . "' 
															AND a.jenisnya = 'kapal'
														LIMIT 1";
												$result02 = $con->getRecord($sql02);
												$decode = json_decode($result02['detail_rincian'], true);

												$jenis  = "";
												$total_penawaran = 0;
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

													$total_penawaran = (float)$harga_dasar_penawaran + (float)$ongkos_angkut_penawaran + (float)$pbbkb_penawaran + (float)$ppn_penawaran;
												}

												if ($data1['jenis'] == "all_in") {
													if (fmod($harga_dasar_penawaran, 1) !== 0.0000) {
														$jenis .= "<p>" . 'Harga Dasar' . " : " . number_format($harga_dasar_penawaran, 4, ".", ",") . "</p>";
													} else {
														$jenis .= "<p>" . 'Harga Dasar' . " : " . number_format($harga_dasar_penawaran, 0, ".", ",") . "</p>";
													}

													if (fmod($ongkos_angkut_penawaran, 1) !== 0.0000) {
														$jenis .= "<p>" . 'Ongkos Angkut' . " : " . number_format($ongkos_angkut_penawaran, 4, ".", ",") . "</p>";
													} else {
														$jenis .= "<p>" . 'Ongkos Angkut' . " : " . number_format($ongkos_angkut_penawaran, 0, ".", ",") . "</p>";
													}

													if (fmod($ppn_penawaran, 1) !== 0.0000) {
														$jenis .= "<p>" . 'PPN' . " : " . number_format($ppn_penawaran, 4, ".", ",") . "</p>";
													} else {
														$jenis .= "<p>" . 'PPN' . " : " . number_format($ppn_penawaran, 0, ".", ",") . "</p>";
													}

													if (fmod($pbbkb_penawaran, 1) !== 0.0000) {
														$jenis .= "<p>" . 'PBBKB' . " " . $nilai_pbbkb . "% : " . number_format($pbbkb_penawaran, 4, ".", ",") . "</p>";
													} else {
														$jenis .= "<p>" . 'PBBKB' . " " . $nilai_pbbkb . "% : " . number_format($pbbkb_penawaran, 0, ".", ",") . "</p>";
													}
													// if ($arr1['rincian'] == "Ongkos Angkut" || $arr1['rincian'] == "PPN") {
													// 	$nilai = $arr1['nilai'];
													// 	$biaya = ($arr1['biaya']) ? $arr1['biaya'] : 0;
													// 	if ($result02['pembulatan'] == 2) {
													// 		if ($arr1['rincian'] == "PPN") {
													// 			$total_oa_ppn = $ongkos_angkut_penawaran * $nilai_ppn / 100;
													// 			$jenis .= "<p>" . $arr1['rincian'] . " " . $arr1['nilai'] . "% : " . $total_oa_ppn . "</p>";
													// 		} else {
													// 			if (fmod($biaya, 1) !== 0.0000) {
													// 				$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya, 4, ".", ",") . "</p>";
													// 			} else {
													// 				$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya, 0) . "</p>";
													// 			}
													// 		}
													// 	} elseif ($result02['pembulatan'] == 0) {
													// 		if ($arr1['rincian'] == "PPN") {
													// 			$total_oa_ppn = $ongkos_angkut_penawaran * $nilai_ppn / 100;
													// 			$jenis .= "<p>" . $arr1['rincian'] . " " . $arr1['nilai'] . "% : " . $total_oa_ppn . "</p>";
													// 		} else {
													// 			if (fmod($biaya, 1) !== 0.0000) {
													// 				$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya, 4, ".", ",") . "</p>";
													// 			} else {
													// 				$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya, 0) . "</p>";
													// 			}
													// 		}
													// 	} else {
													// 		if ($arr1['rincian'] == "PPN") {
													// 			$total_oa_ppn = $ongkos_angkut_penawaran * $nilai_ppn / 100;
													// 			$jenis .= "<p>" . $arr1['rincian'] . " " . $arr1['nilai'] . "% : " . $total_oa_ppn . "</p>";
													// 		} else {
													// 			if (fmod($biaya, 1) !== 0.0000) {
													// 				$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya, 4, ".", ",") . "</p>";
													// 			} else {
													// 				$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya, 0) . "</p>";
													// 			}
													// 		}
													// 	}
													// }
												} elseif ($data1['jenis'] == "harga_dasar") {
													$total_hsd_ppn = ($harga_dasar_penawaran * $nilai_ppn) / 100;

													if (fmod($harga_dasar_penawaran, 1) !== 0.0000) {
														$jenis .= "<p>" . 'Harga Dasar' . " : " . number_format($harga_dasar_penawaran, 4, ".", ",") . "</p>";
													} else {
														$jenis .= "<p>" . 'Harga Dasar' . " : " . number_format($harga_dasar_penawaran, 0, ".", ",") . "</p>";
													}

													if (fmod($total_hsd_ppn, 1) !== 0.0000) {
														$jenis .= "<p>" . 'PPN' . " : " . number_format($total_hsd_ppn, 4, ".", ",") . "</p>";
													} else {
														$jenis .= "<p>" . 'PPN' . " : " . number_format($total_hsd_ppn, 0, ".", ",") . "</p>";
													}

													// if ($arr1['rincian'] == "PBBKB") {
													// 	$nilai = $arr1['nilai'];
													// 	$biaya = ($arr1['biaya']) ? $arr1['biaya'] : 0;

													// 	if (fmod($biaya, 1) !== 0.0000) {
													// 		$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya, 4, ".", ",") . "</p>";
													// 	} else {
													// 		$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya) . "</p>";
													// 	}
													// }
												} elseif ($data1['jenis'] == "harga_dasar_oa") {
													$total_hsd_oa_ppn = ($harga_dasar_penawaran + $ongkos_angkut_penawaran) * $nilai_ppn / 100;

													if (fmod($harga_dasar_penawaran, 1) !== 0.0000) {
														$jenis .= "<p>" . 'Harga Dasar' . " : " . number_format($harga_dasar_penawaran, 4, ".", ",") . "</p>";
													} else {
														$jenis .= "<p>" . 'Harga Dasar' . " : " . number_format($harga_dasar_penawaran, 0, ".", ",") . "</p>";
													}

													if (fmod($ongkos_angkut_penawaran, 1) !== 0.0000) {
														$jenis .= "<p>" . 'Ongkos Angkut' . " : " . number_format($ongkos_angkut_penawaran, 4, ".", ",") . "</p>";
													} else {
														$jenis .= "<p>" . 'Ongkos Angkut' . " : " . number_format($ongkos_angkut_penawaran, 0, ".", ",") . "</p>";
													}

													if (fmod($total_hsd_oa_ppn, 1) !== 0.0000) {
														$jenis .= "<p>" . 'PPN' . " : " . number_format($total_hsd_oa_ppn, 4, ".", ",") . "</p>";
													} else {
														$jenis .= "<p>" . 'PPN' . " : " . number_format($total_hsd_oa_ppn, 0, ".", ",") . "</p>";
													}

													// if ($arr1['rincian'] == "Harga Dasar" || $arr1['rincian'] == "Ongkos Angkut" || $arr1['rincian'] == "PPN") {
													// 	$nilai = $arr1['nilai'];
													// 	$biaya = ($arr1['biaya']) ? $arr1['biaya'] : 0;
													// 	if ($arr1['rincian'] == "PPN") {
													// 		$total_hsd_oa_ppn = ($harga_dasar_penawaran + $ongkos_angkut_penawaran) * $nilai_ppn / 100;
													// 		if (fmod($total_hsd_oa_ppn, 1) !== 0.0000) {
													// 			$jenis .= "<p>" . $arr1['rincian'] . " " . $arr1['nilai'] . "% : " . number_format($total_hsd_oa_ppn, 4, ".", ",") . "</p>";
													// 		} else {
													// 			$jenis .= "<p>" . $arr1['rincian'] . " " . $arr1['nilai'] . "% : " . number_format($total_hsd_oa_ppn) . "</p>";
													// 		}
													// 	} else {
													// 		if (fmod($biaya, 1) !== 0.0000) {
													// 			$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya, 4, ".", ",") . "</p>";
													// 		} else {
													// 			$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya) . "</p>";
													// 		}
													// 	}
													// }
												} elseif ($data1['jenis'] == "harga_dasar_pbbkb") {
													$total_hsd_ppn = ($harga_dasar_penawaran + $pbbkb_penawaran) * $nilai_ppn / 100;

													if (fmod($harga_dasar_penawaran, 1) !== 0.0000) {
														$jenis .= "<p>" . 'Harga Dasar' . " : " . number_format($harga_dasar_penawaran, 4, ".", ",") . "</p>";
													} else {
														$jenis .= "<p>" . 'Harga Dasar' . " : " . number_format($harga_dasar_penawaran, 0, ".", ",") . "</p>";
													}

													if (fmod($total_hsd_ppn, 1) !== 0.0000) {
														$jenis .= "<p>" . 'PPN' . " : " . number_format($total_hsd_ppn, 4, ".", ",") . "</p>";
													} else {
														$jenis .= "<p>" . 'PPN' . " : " . number_format($total_hsd_ppn, 0, ".", ",") . "</p>";
													}

													if (fmod($pbbkb_penawaran, 1) !== 0.0000) {
														$jenis .= "<p>" . 'PBBKB' . " " . $nilai_pbbkb . "% : " . number_format($pbbkb_penawaran, 4, ".", ",") . "</p>";
													} else {
														$jenis .= "<p>" . 'PBBKB' . " " . $nilai_pbbkb . "% : " . number_format($pbbkb_penawaran, 0, ".", ",") . "</p>";
													}
													// if ($arr1['rincian'] == "PPN") {
													// 	$total_hsd_ppn = ($harga_dasar_penawaran + $pbbkb_penawaran) * $nilai_ppn / 100;

													// 	$jenis .= "<p>" . $arr1['rincian'] . " " . $arr1['nilai'] . "% : " . $total_hsd_ppn . "</p>";
													// } else {
													// 	if (fmod($biaya, 1) !== 0.0000) {
													// 		$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya, 4, ".", ",") . "</p>";
													// 	} else {
													// 		$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya) . "</p>";
													// 	}
													// }
												} elseif ($data1['jenis'] == "split_oa") {
													$total_oa_ppn = $ongkos_angkut_penawaran * $nilai_ppn / 100;

													if (fmod($ongkos_angkut_penawaran, 1) !== 0.0000) {
														$jenis .= "<p>" . 'Ongkos Angkut' . " : " . number_format($ongkos_angkut_penawaran, 4, ".", ",") . "</p>";
													} else {
														$jenis .= "<p>" . 'Ongkos Angkut' . " : " . number_format($ongkos_angkut_penawaran, 0, ".", ",") . "</p>";
													}

													if (fmod($total_oa_ppn, 1) !== 0.0000) {
														$jenis .= "<p>" . 'PPN' . " : " . number_format($total_oa_ppn, 4, ".", ",") . "</p>";
													} else {
														$jenis .= "<p>" . 'PPN' . " : " . number_format($total_oa_ppn, 0, ".", ",") . "</p>";
													}
												}
												$harga_kirim = $data1['harga_kirim'] ? number_format($data1['harga_kirim']) : 0;

												$jumlah_harga = (float)$data1['vol_kirim'] * (float)$data1['harga_kirim'];
												$total_invoice 	= $total_invoice + $jumlah_harga;
												$jumlah_harga_fix = number_format(round($jumlah_harga));
												$total_invoice_fix = number_format(round($total_invoice));

												echo '
                                            <tr data-id="' . $no_urut . '">
                                                <td class="text-center"><span class="frmnodasar" data-row-count="' . $no_urut . '">' . $no_urut . '</span></td>
												<td class="text-left">
													<p style="margin-bottom:3px;">No PO Customer : ' . $data1['nomor_poc'] . '</p>
													<p style="margin-bottom:3px;">No DN : ' . $data1['no_dn'] . '</p>
													<p style="margin-bottom:3px;">Jenis Angkutan : ' . strtoupper($data1['jenisnya']) . '</p>
													<p style="margin-bottom:0px;">' . ($data1['jenisnya'] == 'truck' ? 'No Plat : ' . $data1['angkutan'] . ' (' . $data1['sopir'] . ')' : 'Vessel : ' . $data1['angkutan'] . ' (' . $data1['sopir'] . ')') . '</p>

												</td> 
												<td class="text-left">
													<p style="margin-bottom:3px;">Nomor DO : ' . $no_do . '</p>
													<p style="margin-bottom:3px;">Nomor LO : ' . $data1['nomor_lo_pr'] . '</p>
													<p style="margin-bottom:3px;">Nomor SPJ : ' . $data1['no_spj'] . '</p>
												</td>
												<td class="text-left">
													<input type="text" id="tgl_delivered' . $no_urut . '" name="tgl_delivered[]" class="form-control input-sm" value="' . $tgl_delivered . '" data-rule-dateNL="true" readonly />
												</td>
												<td class="text-left">
													<input type="text" id="vol_kirim' . $no_urut . '" name="vol_kirim[]" class="form-control input-sm  text-right volumenya" value="' . $vol_kirim . '" />
													<p> Volume PO : ' . number_format($data1['volume_pr']) . '</p>
													<p> Realisasi : ' . $realisasi_volume  . '</p>
												</td>
												<td class="text-left">
													<input type="text" id="harga_kirim' . $no_urut . '" name="harga_kirim[]" class="form-control input-sm text-right harganya" value="' . $harga_kirim . '" readonly/>

													<input type="hidden" id="harga_kirim' . $no_urut . '" name="harga_kirim_fix[]" class="form-control input-sm text-right harganya_luar" value="' . $data1['harga_kirim'] . '" readonly/>
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
											echo '<tr><td class="text-center" colspan="8">Tidak Ada Data</td></tr>';
										}
										?>
										<tr>
											<td class="text-center" colspan="6"><b>T O T A L</b></td>
											<td class="text-left">
												<input type="text" id="total_invoice_edit" name="total_invoice" class="form-control input-sm text-right hitung" value="<?php echo $total_invoice_fix; ?>" readonly />
											</td>
											<td class="text-center">&nbsp;</td>
										</tr>
									</tbody>
								</table>
							</div>


							<hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

							<div style="margin-bottom:15px;">
								<input type="hidden" name="act" value="<?php echo $action; ?>" />
								<input type="hidden" name="idr" value="<?php echo $idr; ?>" />
								<a href="<?php echo BASE_URL_CLIENT . '/invoice_customer.php'; ?>" class="btn btn-default" style="min-width:90px;">
									<i class="fa fa-reply jarak-kanan"></i> Kembali</a>
								<?php if (!$model['total_bayar'] || $model['total_bayar'] == 0) { ?>
									<?php if ($sesrole != '25') : ?>
										<button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" <?= $action == 'add' ? 'disabled' : '' ?> style="min-width:90px;">
											<i class="fa fa-save jarak-kanan"></i> Simpan</button>
									<?php endif ?>
								<?php } ?>
							</div>
							<p style="margin:0px;"><small>* Wajib Diisi</small></p>
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
			$("#split_invoice").change(function() {
				var value = $(this).val();
				if (value == "split_oa") {
					$(".row-kode").removeClass("hide");
					$("#kode_oa").attr("required", true);
					$(".row-akun-pbbkb").removeClass("hide");
					$("#akun_pbbkb").attr("required", true);
					$(".row-kode2").addClass("hide");
					$("#kode_pbbkb").removeAttr("required", true);
				} else if (value == "split_pbbkb") {
					$(".row-kode").addClass("hide");
					$("#kode_oa").removeAttr("required", true);
					$(".row-akun-pbbkb").addClass("hide");
					$("#akun_pbbkb").removeAttr("required", true);
					$(".row-kode2").removeClass("hide");
					$('.row-kode2').animate({
						opacity: 1
					}, 400, "swing");
					$("#kode_pbbkb").attr("required", true);
				} else if (value == "split_all") {
					$(".row-kode").removeClass("hide");
					$("#kode_oa").removeAttr("required", true);
					$(".row-akun-pbbkb").addClass("hide");
					$("#akun_pbbkb").removeAttr("required", true);
					$(".row-kode2").removeClass("hide");
					$("#kode_pbbkb").attr("required", true);
				} else {
					$(".row-kode").addClass("hide");
					$("#kode_oa").removeAttr("required", true);
					$(".row-akun-pbbkb").removeClass("hide");
					$("#akun_pbbkb").attr("required", true);
					$(".row-kode2").addClass("hide");
					$("#kode_pbbkb").removeAttr("required", true);
				}
			})

			$("#cetakan_invoice").change(function() {
				var value = $(this).val();
				if (value == 1) {
					$("#row-inv-customer").addClass("hide");
					$("#no_invoice_customer").removeAttr("required", true);
					$("#no_invoice_customer").val("");
				} else {
					$("#row-inv-customer").removeClass("hide");
					$("#no_invoice_customer").attr("required", true);
				}
			})

			var action = `<?= $action ?>`;
			if (action == 'add') {
				$("#row-tanggal").hide();
				$("#row-jenis-tanggal").hide();
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

			$(".volumenya").number(true, 4, ".", ",");
			$(".jumlahnya, #total_invoice_edit").number(true, 0, ".", ",");


			var formValidasiCfg = {
				submitHandler: function(form) {


					var rowCount = $(".table-dasar tbody tr").length;

					if (rowCount === 0) {
						// Tampilkan pesan kesalahan jika tidak ada data
						alert("Tabel tidak memiliki data. Silakan tambahkan data terlebih dahulu.");
					} else {

						$("#loading_modal").modal({
							keyboard: false,
							backdrop: 'static'
						});

						if ($("#cekkolnup").is(":checked") && $("#nup_fee").val() == "") {
							$("#loading_modal").modal("hide");
							$.validator.showErrorField('nup_fee', "Kolom ini belum diisi atau dipilih");
							setErrorFocus($("#nup_fee"), $("form#gform"), false);
						} else {
							form.submit();
						}
					}
				}
			};
			$("form#gform").validate($.extend(true, {}, config.validation, formValidasiCfg));

			$(".picker-user").on("click", function(e) {
				$("#loading_modal").modal({
					keyboard: false,
					backdrop: 'static'
				});
				$.post(base_url + "/web/invoice_customer_picker.php", {
					prm: $(this).data("param")
				}, function(data) {
					$("#user_modal").find(".modal-body").html(data);
					$("#user_modal").modal({
						backdrop: "static"
					});
				});
			});

			$("#user_modal").on('show.bs.modal', function(e) {
				$("#loading_modal").modal({
					keyboard: false,
					backdrop: 'static'
				});
			}).on('shown.bs.modal', function(e) {
				$("#loading_modal").modal("hide");
			}).on('click', '#idBataluser_modal', function() {
				$("#user_modal").modal("hide");
			});


			$(".table-dasar").on("input", "input.volumenya, input.harganya_luar", function() {
				var row = $(this).closest("tr");
				var volumeKirim = parseFloat(row.find("input.volumenya").val()) || 0;
				var hargaKirim = parseFloat(row.find("input.harganya_luar").val().replace(/,/g, '')) || 0;
				// console.log(hargaKirim)
				var jumlahHarga = Math.round((volumeKirim * hargaKirim));
				row.find("input.jumlahnya").val(jumlahHarga);

				recalculateTotal();
			});

			$("#tipe").change(function() {
				var val = $(this).val();
				if (val == "periode") {
					$("#tanggal").val("");
					$("#row-periode").show();
					$("#row-tanggal").hide();
					$("#row-jenis-tanggal").show();
				} else if (val == "tanggal") {
					$("#tgl_kirim_awal").val("");
					$("#tgl_kirim_akhir").val("");
					$("#row-periode").hide();
					$("#row-tanggal").show();
					$("#row-jenis-tanggal").hide();
				} else {
					$("#tanggal").val("");
					$("#tgl_kirim_awal").val("");
					$("#tgl_kirim_akhir").val("");
					$("#row-periode").hide();
					$("#row-tanggal").hide();
					$("#row-jenis-tanggal").hide();
				}
			})

			$("#btn-generate").on("click", function(e) {
				let tipe = $("#tipe").val();
				let nilai01 = $("#id_customer").val();
				let nilai02 = $("#tgl_kirim_awal").val();
				let nilai03 = $("#tgl_kirim_akhir").val();
				let nilai04 = $("#tanggal").val();
				let selected_value = $("input[name='jenis_tanggal']:checked").val();

				if (tipe) {

					if (tipe == "periode") {
						if (selected_value == undefined) {
							swal.fire("Harap mengisi periode by");
						} else {
							if (nilai01 && nilai02 && nilai03) {
								$("#loading_modal").modal({
									keyboard: false,
									backdrop: 'static'
								});
								$.ajax({
									type: 'POST',
									url: "./invoice_customer_list_generate.php",
									data: {
										q1: nilai01,
										q2: nilai02,
										q3: nilai03,
										q4: selected_value
									},
									cache: false,
									dataType: "json",
									success: function(data) {
										if (data.items.length > 0) {
											$("#btnSbmt").removeAttr("disabled");
											var tabel = $(".table-dasar");
											var arrId = tabel.find("tbody > tr").map(function() {
												return parseFloat($(this).data("id")) || 0;
											}).toArray();
											var rwNom = Math.max.apply(Math, arrId);
											var newId = (rwNom == 0) ? 1 : (rwNom + 1);
											var newId = 1;
											var total01 = 0;
											var total02 = 0;
											var total03 = 0;
											var total04 = 0;
											var total05 = 0;
											var total06 = 0;
											var biaya_ppn = '';

											var total_penawaran_luar = 0;
											var nilai_ppn_luar = 0;

											$.each(data.items, function(idx, row) {
												// var jumlahnya = row.volume_po * row.harga_poc;
												// total01 = total01 + jumlahnya;
												var penawaran_detail = JSON.parse(row.detail_rincian);
												var penawaran = "";
												var harga_dasar = 0;
												var ongkos_angkut = 0;
												var ppn = 0;
												var nilai_ppn = 0;
												var pbbkb = 0;
												let biaya = 0;
												var total_penawaran = 0;
												var total_penawaran_2 = 0;
												biaya_ppn = row.biaya_ppn
												$.each(penawaran_detail, function(i, res) {
													if (res.rincian == "Harga Dasar") {
														harga_dasar = res.biaya;
													}
													if (res.rincian == "Ongkos Angkut") {
														ongkos_angkut = res.biaya;
													}
													if (res.rincian == "PPN") {
														ppn = res.biaya;
														nilai_ppn = res.nilai;
													}
													if (res.rincian == "PBBKB") {
														pbbkb = res.biaya;
													}

													total_penawaran_luar = parseFloat(harga_dasar) + parseFloat(ongkos_angkut) + parseFloat(pbbkb) + parseFloat(ppn);

													if (row.pembulatan == 2) {
														biaya = parseFloat(res.biaya).toFixed(4).replace(/\d(?=(\d{3})+\.)/g, '$&,');

														if (biaya_ppn == "gabung_pbbkb" || biaya_ppn == "gabung_pbbkboa") {
															total_penawaran = parseFloat(harga_dasar) + parseFloat(ongkos_angkut) + parseFloat(pbbkb) + parseFloat(ppn);

															total_penawaran_2 = parseFloat(harga_dasar) + parseFloat(ongkos_angkut) + parseFloat(ppn) + parseFloat(pbbkb);
														} else {
															total_penawaran = parseFloat(harga_dasar) + parseFloat(ongkos_angkut);

															total_penawaran_2 = parseFloat(harga_dasar) + parseFloat(ongkos_angkut) + parseFloat(ppn);
														}

													} else if (row.pembulatan == 0) {
														biaya = parseFloat(res.biaya).toFixed(4).replace(/\d(?=(\d{3})+\.)/g, '$&,');

														if (biaya_ppn == "gabung_pbbkb" || biaya_ppn == "gabung_pbbkboa") {
															total_penawaran = parseFloat(harga_dasar) + parseFloat(ongkos_angkut) + parseFloat(pbbkb);

															total_penawaran_2 = parseFloat(harga_dasar) + parseFloat(ongkos_angkut) + parseFloat(ppn) + parseFloat(pbbkb);
														} else {
															total_penawaran = parseFloat(harga_dasar) + parseFloat(ongkos_angkut);

															total_penawaran_2 = parseFloat(harga_dasar) + parseFloat(ongkos_angkut) + parseFloat(ppn);
														}


													} else {
														biaya = parseFloat(res.biaya).toFixed(4).replace(/\d(?=(\d{3})+\.)/g, '$&,');
														if (biaya_ppn == "gabung_pbbkb" || biaya_ppn == "gabung_pbbkboa") {
															total_penawaran = parseFloat(harga_dasar) + parseFloat(ongkos_angkut) + parseFloat(pbbkb);

															total_penawaran_2 = parseFloat(harga_dasar) + parseFloat(ongkos_angkut) + parseFloat(ppn) + parseFloat(pbbkb);
														} else {
															total_penawaran = parseFloat(harga_dasar) + parseFloat(ongkos_angkut);

															total_penawaran_2 = parseFloat(harga_dasar) + parseFloat(ongkos_angkut) + parseFloat(ppn);
														}
													}

													penawaran += "<p>" + res.rincian + " : " + biaya + "</p>";
												})

												if (biaya_ppn == "gabung_pbbkb" || biaya_ppn == "gabung_pbbkboa") {
													var jumlahnya = Math.round(((row.volume_po * total_penawaran) * nilai_ppn / 100)) + (row.volume_po * total_penawaran);
												} else {
													var jumlahnya = Math.round(((row.volume_po * total_penawaran) * nilai_ppn / 100)) + (row.volume_po * total_penawaran) + (row.volume_po * pbbkb);

												}
												// var jumlahnya = row.volume_po * total_penawaran;
												total01 = total01 + jumlahnya;

												if (row.pembulatan == 2 || row.pembulatan == 0) {

													var jumlah_harga_dasar = (parseFloat(harga_dasar) * row.volume_po) * parseFloat(nilai_ppn) / 100 + (parseFloat(harga_dasar) * row.volume_po);

													var jumlah_ongkos_angkut = (parseFloat(ongkos_angkut) * row.volume_po) + (parseFloat(ongkos_angkut) * parseFloat(nilai_ppn) / 100) * row.volume_po;

													var jumlah_pbbkb = parseFloat(pbbkb) * row.volume_po;

													var jumlah_harga_dasar_oa = ((parseFloat(harga_dasar) + parseFloat(ongkos_angkut)) * row.volume_po) + ((parseFloat(harga_dasar) + parseFloat(ongkos_angkut)) * parseFloat(nilai_ppn) / 100) * row.volume_po;

													if (biaya_ppn == "gabung_pbbkb" || biaya_ppn == "gabung_pbbkboa") {
														var jumlah_harga_dasar_pbbkb = ((parseFloat(harga_dasar) + parseFloat(pbbkb)) * row.volume_po) + ((parseFloat(harga_dasar) + parseFloat(pbbkb)) * parseFloat(nilai_ppn) / 100) * row.volume_po;
													} else {
														var jumlah_harga_dasar_pbbkb = ((parseFloat(harga_dasar)) * row.volume_po) + ((parseFloat(pbbkb)) * row.volume_po) + ((parseFloat(harga_dasar))) * parseFloat(nilai_ppn) / 100 * row.volume_po;
													}

													total02 = total02 + parseFloat(jumlah_harga_dasar);
													total03 = total03 + parseFloat(jumlah_ongkos_angkut);
													total04 = total04 + parseFloat(jumlah_pbbkb);
													total05 = total05 + parseFloat(jumlah_harga_dasar_oa);
													total06 = total06 + parseFloat(jumlah_harga_dasar_pbbkb);

												} else {
													var jumlah_harga_dasar = (parseFloat(harga_dasar) * row.volume_po) * parseFloat(nilai_ppn) / 100 + (parseFloat(harga_dasar) * row.volume_po);

													var jumlah_ongkos_angkut = (parseFloat(ongkos_angkut) + (parseFloat(ongkos_angkut) * parseFloat(nilai_ppn) / 100)) * row.volume_po;

													var jumlah_pbbkb = parseFloat(pbbkb) * row.volume_po;

													var jumlah_harga_dasar_oa = (parseFloat(harga_dasar) + parseFloat(ongkos_angkut) + (parseFloat(harga_dasar) + parseFloat(ongkos_angkut)) * parseFloat(nilai_ppn) / 100) * row.volume_po;

													if (biaya_ppn == "gabung_pbbkb" || biaya_ppn == "gabung_pbbkboa") {
														var jumlah_harga_dasar_pbbkb = ((parseFloat(harga_dasar) + parseFloat(pbbkb)) * row.volume_po) + ((parseFloat(harga_dasar) + parseFloat(pbbkb))) * parseFloat(nilai_ppn) / 100 * row.volume_po;
													} else {
														var jumlah_harga_dasar_pbbkb = ((parseFloat(harga_dasar)) * row.volume_po) + ((parseFloat(pbbkb)) * row.volume_po) + ((parseFloat(harga_dasar))) * parseFloat(nilai_ppn) / 100 * row.volume_po;
													}

													total02 = total02 + parseFloat(jumlah_harga_dasar);
													total03 = total03 + parseFloat(jumlah_ongkos_angkut);
													total04 = total04 + parseFloat(jumlah_pbbkb);
													total05 = total05 + parseFloat(jumlah_harga_dasar_oa);
													total06 = total06 + parseFloat(jumlah_harga_dasar_pbbkb);
												}

												if (row.no_do_acurate == null) {
													var no_do = row.no_do_syop;
												} else {
													var no_do = row.no_do_acurate;
												}
												var isiHtml =
													'<tr data-id="' + newId + '">' +
													'<td class="text-center"><span class="frmnodasar" data-row-count="' + newId + '"></span></td>' +
													'<td class="text-left">' +
													'<p style="margin-bottom:3px;">No PO Customer : ' + row.nomor_poc + '</p>' +
													'<p style="margin-bottom:3px;">No DN : ' + row.no_dn + '</p>' +
													'<p style="margin-bottom:3px;">No SPJ : ' + row.no_spj + '</p>' +
													'<p style="margin-bottom:3px;">No Penawaran : ' + row.nomor_surat + '</p>' +
													'<p style="margin-bottom:3px;">Jenis Angkutan : ' + row.jenisnya.toUpperCase() + '</p>' +
													'<p style="margin-bottom:0px;">' + (row.jenisnya == 'truck' ? 'No Plat : ' + row.angkutan + ' (' + row.sopir + ')' : 'Vessel : ' + row.angkutan + ' (' + row.sopir + ')') + '</p>' +
													'</td>' +
													'<td class="text-left">' +
													'<p style="margin-bottom:3px;">No DO : ' + no_do + '</p>' +
													'<p style="margin-bottom:3px;">No LO : ' + row.nomor_lo_pr + '</p>' +
													'<p style="margin-bottom:3px;">Refund : ' + row.refund_tawar + '</p>' +
													'</td>' +
													'<td class="text-left">' +
													'<input type="text" id="tgl_delivered' + newId + '" name="tgl_delivered[]" class="form-control input-sm" value="' + row.tgl_delivered + '" data-rule-dateNL="true" readonly />' +
													'</td>' +
													'<td class="text-left">' +
													'<input type="text" id="vol_kirim' + newId + '" name="vol_kirim[]" class="form-control input-sm text-right volumenya" value="' + row.volume_po + '"/>' +
													'<p style="margin-bottom:3px;">Volume PO : ' + new Intl.NumberFormat("ja-JP").format(row.volume_po) + '</p>' +
													'<p style="margin-bottom:3px;">Realisasi : ' + new Intl.NumberFormat("ja-JP").format(row.realisasi_volume) + '</p>' +
													'<td class="text-left" width="200">' +
													'<input type="text" id="harga_kirim' + newId + '" name="harga_kirim[]" class="form-control input-sm text-right harganya" value="' + total_penawaran_2 + '"  readonly />' +
													'<input type="hidden" id="harga_kirim' + newId + '" name="harga_kirim_fix[]" class="form-control input-sm text-right harganya_luar" value="' + total_penawaran_luar + '"  readonly />' +
													'<input type="hidden" id="pembulatan' + newId + '" name="pembulatan[]" class="form-control input-sm text-right pembulatannya" value="' + row.pembulatan + '"  readonly />' +
													'<input type="hidden" id="harga_dasar' + newId + '" name="harga_dasar[]" class="form-control input-sm text-right harga_dasarnya" value="' + harga_dasar + '" readonly />' +
													'<input type="hidden" id="ongkos_angkut' + newId + '" name="ongkos_angkut[]" class="form-control input-sm text-right oanya" value="' + ongkos_angkut + '"  readonly />' +
													'<input type="hidden" id="ppn' + newId + '" name="ppn[]" class="form-control input-sm text-right total_ppn_nya" value="' + ppn + '"  readonly />' +
													'<input type="hidden" id="nilai_ppn' + newId + '" name="nilai_ppn[]" class="form-control input-sm text-right ppn_nya" value="' + nilai_ppn + '"  readonly />' +
													'<input type="hidden" id="pbbkb' + newId + '" name="pbbkb[]" class="form-control input-sm text-right pbbkbnya" value="' + pbbkb + '"  readonly />' + penawaran +
													'</td>' +
													'<td class="text-left">' +
													'<input type="hidden" name="id_dsd[]" value="' + row.id_dsd + '" />' +
													'<input type="hidden" name="jenisnya[]" value="' + row.jenisnya + '" />' +
													'<input type="hidden" name="kategori[]" value="' + row.biaya_ppn + '" />' +
													'<input type="hidden" name="refund_tawar[]" value="' + row.refund_tawar + '" />' +
													'<input type="text" id="jumlah_harga' + newId + '" name="jumlah_harga[]" class="form-control input-sm text-right jumlahnya" value="' + jumlahnya + '" readonly />' +
													'<input type="hidden" id="jumlah_harga_dasar' + newId + '" name="jumlah_harga_dasar[]" class="form-control input-sm text-right jumlahnya_harga_dasar" value="' + jumlah_harga_dasar + '" readonly />' +
													'<input type="hidden" id="jumlah_pbbkb' + newId + '" name="jumlah_pbbkb[]" class="form-control input-sm text-right jumlahnya_pbbkb" value="' + jumlah_pbbkb + '" readonly />' +
													'<input type="hidden" id="jumlah_harga_dasar_oa' + newId + '" name="jumlah_harga_dasar_oa[]" class="form-control input-sm text-right jumlahnya_harga_dasar_oa" value="' + jumlah_harga_dasar_oa + '" readonly />' +
													'<input type="hidden" id="jumlah_ongkos_angkut' + newId + '" name="jumlah_ongkos_angkut[]" class="form-control input-sm text-right jumlahnya_ongkos_angkut" value="' + jumlah_ongkos_angkut + '" readonly />' +
													'<input type="hidden" id="jumlah_harga_dasar_pbbkb' + newId + '" name="jumlah_harga_dasar_pbbkb[]" class="form-control input-sm text-right jumlahnya_harga_dasar_pbbkb" value="' + jumlah_harga_dasar_pbbkb + '" readonly />' +
													'</td>' +
													'<td class="text-center">' +
													'<a class="btn btn-action btn-danger hRow jarak-kanan">&nbsp;<i class="fa fa-times"></i>&nbsp;</a>' +
													'' +
													'</td>' +
													'</tr>';
												if (newId == 1) {
													tabel.find('tbody').html(isiHtml);
												} else {
													tabel.find('tbody > tr:last').after(isiHtml);
												}
												$("#tgl_delivered" + newId).datepicker(config.datepicker);
												$("#vol_kirim" + newId).number(true, 4, '.', ',');
												if (row.pembulatan == 2) {
													$("#jumlah_harga" + newId).number(true, 0, '.', ',');
													$("#harga_kirim" + newId).number(true, 4, '.', ',');
												} else if (row.pembulatan == 0) {
													$("#jumlah_harga" + newId).number(true, 0, '.', ',');
													$("#harga_kirim" + newId).number(true, 2, '.', ',');
												} else {
													$("#jumlah_harga" + newId).number(true, 0, '.', ',');
													$("#harga_kirim" + newId).number(true, 0, '.', ',');
												}
												newId++;
											});
											tabel.find("span.frmnodasar").each(function(i, v) {
												$(v).text(i + 1);
											});
											var isiHtml =
												'<tr>' +
												'<td class="text-center" colspan="6"><b>T O T A L</b></td>' +
												'<td class="text-left">' +
												'<input type="text" id="total_invoice" name="total_invoice" class="form-control input-sm text-right" value="' + Math.round(total01) + '" readonly />' +
												'<input type="hidden" id="total_invoice_harga_dasar" name="total_invoice_harga_dasar" class="form-control input-sm text-right" value="' + Math.round(total02) + '" readonly />' +
												'<input type="hidden" id="total_invoice_ongkos_angkut" name="total_invoice_ongkos_angkut" class="form-control input-sm text-right" value="' + Math.round(total03) + '" readonly />' +
												'<input type="hidden" id="total_invoice_pbbkb" name="total_invoice_pbbkb" class="form-control input-sm text-right" value="' + Math.round(total04) + '" readonly />' +
												'<input type="hidden" id="total_invoice_harga_dasar_oa" name="total_invoice_harga_dasar_oa" class="form-control input-sm text-right" value="' + Math.round(total05) + '" readonly />' +
												'<input type="hidden" id="total_invoice_harga_dasar_pbbkb" name="total_invoice_harga_dasar_pbbkb" class="form-control input-sm text-right" value="' + Math.round(total06) + '" readonly />' +
												'</td>' +
												'<td class="text-center">&nbsp;</td>' +
												'</tr>';
											tabel.find('tbody > tr:last').after(isiHtml);

											if (data.items[0].pembulatan == 2) {
												$("#total_invoice").number(true, 0, '.', ',');
												$("#total_invoice_harga_dasar").number(true, 4, '.', ',');
												$("#total_invoice_ongkos_angkut").number(true, 4, '.', ',');
												$("#total_invoice_pbbkb").number(true, 4, '.', ',');
												$("#total_invoice_harga_dasar_oa").number(true, 4, '.', ',');
												$("#total_invoice_harga_dasar_pbbkb").number(true, 4, '.', ',');
											} else if (data.items[0].pembulatan == 0) {
												$("#total_invoice").number(true, 0, '.', ',');
												$("#total_invoice_harga_dasar").number(true, 2, '.', ',');
												$("#total_invoice_ongkos_angkut").number(true, 2, '.', ',');
												$("#total_invoice_pbbkb").number(true, 2, '.', ',');
												$("#total_invoice_harga_dasar_oa").number(true, 2, '.', ',');
												$("#total_invoice_harga_dasar_pbbkb").number(true, 4, '.', ',');
											} else {
												$("#total_invoice").number(true, 0, '.', ',');
												$("#total_invoice_harga_dasar").number(true, 0, '.', ',');
												$("#total_invoice_ongkos_angkut").number(true, 0, '.', ',');
												$("#total_invoice_pbbkb").number(true, 0, '.', ',');
												$("#total_invoice_harga_dasar_oa").number(true, 0, '.', ',');
												$("#total_invoice_harga_dasar_pbbkb").number(true, 0, '.', ',');
											}

											// if (biaya_ppn == 'gabung_pbbkb' || biaya_ppn == 'gabung_pbbkboa' || biaya_ppn == 'all_in') {
											// 	$("#group_items").show(400, "swing");
											// 	$("#group_akun").hide(400, "swing");
											// } else {
											// 	$("#group_akun").show(400, "swing");
											// 	$("#group_items").hide(400, "swing");
											// }

										} else {
											var isiHtml =
												'<tr><td class="text-center" colspan="8">Tidak Ada Data</td></tr>' +
												'<tr>' +
												'<td class="text-center" colspan="6"><b>T O T A L</b></td>' +
												'<td class="text-left">' +
												'<input type="text" id="total_invoice" name="total_invoice" class="form-control input-sm text-right" value="" readonly />' +
												'</td>' +
												'<td class="text-center">&nbsp;</td>' +
												'</tr>';
											$(".table-dasar").find('tbody').html(isiHtml);
											$("#total_invoice").number(true, 0, ',', '.');
										}
									}
								});
								$("#loading_modal").modal("hide");
							} else {
								swal.fire("Harap Mengisi Kolom Customer, Periode Awal dan Periode Akhir Terlebih Dahulu");
							}
						}

					} else {

						if (nilai04) {
							$("#loading_modal").modal({
								keyboard: false,
								backdrop: 'static'
							});
							$.ajax({
								type: 'POST',
								url: "./invoice_customer_list_generate.php",
								data: {
									q1: nilai01,
									q2: nilai04,
									q3: nilai04
								},
								cache: false,
								dataType: "json",
								success: function(data) {
									if (data.items.length > 0) {
										$("#btnSbmt").removeAttr("disabled");
										var tabel = $(".table-dasar");
										var arrId = tabel.find("tbody > tr").map(function() {
											return parseFloat($(this).data("id")) || 0;
										}).toArray();
										var rwNom = Math.max.apply(Math, arrId);
										var newId = (rwNom == 0) ? 1 : (rwNom + 1);
										var newId = 1;
										var total01 = 0;
										var total02 = 0;
										var total03 = 0;
										var total04 = 0;
										var total05 = 0;
										var total06 = 0;
										var biaya_ppn = '';

										var total_penawaran_luar = 0;
										var nilai_ppn_luar = 0;

										$.each(data.items, function(idx, row) {
											// var jumlahnya = row.volume_po * row.harga_poc;
											// total01 = total01 + jumlahnya;
											var penawaran_detail = JSON.parse(row.detail_rincian);
											var penawaran = "";
											var harga_dasar = 0;
											var ongkos_angkut = 0;
											var ppn = 0;
											var nilai_ppn = 0;
											var pbbkb = 0;
											let biaya = 0;
											var total_penawaran = 0;
											var total_penawaran_2 = 0;
											biaya_ppn = row.biaya_ppn
											$.each(penawaran_detail, function(i, res) {
												if (res.rincian == "Harga Dasar") {
													harga_dasar = res.biaya;
												}
												if (res.rincian == "Ongkos Angkut") {
													ongkos_angkut = res.biaya;
												}
												if (res.rincian == "PPN") {
													ppn = res.biaya;
													nilai_ppn = res.nilai;
												}
												if (res.rincian == "PBBKB") {
													pbbkb = res.biaya;
												}

												total_penawaran_luar = parseFloat(harga_dasar) + parseFloat(ongkos_angkut) + parseFloat(pbbkb) + parseFloat(ppn);

												total_penawaran_2 = parseFloat(harga_dasar) + parseFloat(ongkos_angkut) + parseFloat(ppn) + parseFloat(pbbkb);

												if (row.pembulatan == 2) {
													biaya = parseFloat(res.biaya).toFixed(4).replace(/\d(?=(\d{3})+\.)/g, '$&,');

													if (biaya_ppn == "gabung_pbbkb" || biaya_ppn == "gabung_pbbkboa") {
														total_penawaran = parseFloat(harga_dasar) + parseFloat(ongkos_angkut) + parseFloat(pbbkb) + parseFloat(ppn);
													} else {
														total_penawaran = parseFloat(harga_dasar) + parseFloat(ongkos_angkut);
													}

												} else if (row.pembulatan == 0) {
													biaya = parseFloat(res.biaya).toFixed(4).replace(/\d(?=(\d{3})+\.)/g, '$&,');

													if (biaya_ppn == "gabung_pbbkb" || biaya_ppn == "gabung_pbbkboa") {
														total_penawaran = parseFloat(harga_dasar) + parseFloat(ongkos_angkut) + parseFloat(pbbkb);
													} else {
														total_penawaran = parseFloat(harga_dasar) + parseFloat(ongkos_angkut);
													}

												} else {
													biaya = parseFloat(res.biaya).toFixed(4).replace(/\d(?=(\d{3})+\.)/g, '$&,');
													if (biaya_ppn == "gabung_pbbkb" || biaya_ppn == "gabung_pbbkboa") {
														total_penawaran = parseFloat(harga_dasar) + parseFloat(ongkos_angkut) + parseFloat(pbbkb);

														total_penawaran_2 = parseFloat(harga_dasar) + parseFloat(ongkos_angkut) + parseFloat(ppn) + parseFloat(pbbkb);
													} else {
														total_penawaran = parseFloat(harga_dasar) + parseFloat(ongkos_angkut);

														// total_penawaran_2 = parseFloat(harga_dasar) + parseFloat(ongkos_angkut) + parseFloat(ppn);
													}
												}

												penawaran += "<p>" + res.rincian + " : " + biaya + "</p>";
											})

											if (biaya_ppn == "gabung_pbbkb" || biaya_ppn == "gabung_pbbkboa") {
												var jumlahnya = Math.round(((row.volume_po * total_penawaran) * nilai_ppn / 100)) + (row.volume_po * total_penawaran);
											} else {
												var jumlahnya = Math.round(((row.volume_po * total_penawaran) * nilai_ppn / 100)) + (row.volume_po * total_penawaran) + (row.volume_po * pbbkb);

											}
											// var jumlahnya = row.volume_po * total_penawaran;
											total01 = total01 + jumlahnya;

											if (row.pembulatan == 2 || row.pembulatan == 0) {

												var jumlah_harga_dasar = (parseFloat(harga_dasar) * row.volume_po) * parseFloat(nilai_ppn) / 100 + (parseFloat(harga_dasar) * row.volume_po);

												var jumlah_ongkos_angkut = (parseFloat(ongkos_angkut) * row.volume_po) + (parseFloat(ongkos_angkut) * parseFloat(nilai_ppn) / 100) * row.volume_po;

												var jumlah_pbbkb = parseFloat(pbbkb) * row.volume_po;

												var jumlah_harga_dasar_oa = ((parseFloat(harga_dasar) + parseFloat(ongkos_angkut)) * row.volume_po) + ((parseFloat(harga_dasar) + parseFloat(ongkos_angkut)) * parseFloat(nilai_ppn) / 100) * row.volume_po;

												if (biaya_ppn == "gabung_pbbkb" || biaya_ppn == "gabung_pbbkboa") {
													var jumlah_harga_dasar_pbbkb = ((parseFloat(harga_dasar) + parseFloat(pbbkb)) * row.volume_po) + ((parseFloat(harga_dasar) + parseFloat(pbbkb)) * parseFloat(nilai_ppn) / 100) * row.volume_po;
												} else {
													var jumlah_harga_dasar_pbbkb = ((parseFloat(harga_dasar)) * row.volume_po) + ((parseFloat(pbbkb)) * row.volume_po) + ((parseFloat(harga_dasar)) * parseFloat(nilai_ppn) / 100) * row.volume_po;
												}

												total02 = total02 + parseFloat(jumlah_harga_dasar);
												total03 = total03 + parseFloat(jumlah_ongkos_angkut);
												total04 = total04 + parseFloat(jumlah_pbbkb);
												total05 = total05 + parseFloat(jumlah_harga_dasar_oa);
												total06 = total06 + parseFloat(jumlah_harga_dasar_pbbkb);

											} else {
												var jumlah_harga_dasar = (parseFloat(harga_dasar) * row.volume_po) * parseFloat(nilai_ppn) / 100 + (parseFloat(harga_dasar) * row.volume_po);

												var jumlah_ongkos_angkut = (parseFloat(ongkos_angkut) + (parseFloat(ongkos_angkut) * parseFloat(nilai_ppn) / 100)) * row.volume_po;

												var jumlah_pbbkb = parseFloat(pbbkb) * row.volume_po;

												var jumlah_harga_dasar_oa = (parseFloat(harga_dasar) + parseFloat(ongkos_angkut) + (parseFloat(harga_dasar) + parseFloat(ongkos_angkut)) * parseFloat(nilai_ppn) / 100) * row.volume_po;

												if (biaya_ppn == "gabung_pbbkb" || biaya_ppn == "gabung_pbbkboa") {
													var jumlah_harga_dasar_pbbkb = ((parseFloat(harga_dasar) + parseFloat(pbbkb)) * row.volume_po) + ((parseFloat(harga_dasar) + parseFloat(pbbkb))) * parseFloat(nilai_ppn) / 100 * row.volume_po;
												} else {
													var jumlah_harga_dasar_pbbkb = ((parseFloat(harga_dasar)) * row.volume_po) + ((parseFloat(pbbkb)) * row.volume_po) + ((parseFloat(harga_dasar))) * parseFloat(nilai_ppn) / 100 * row.volume_po;
												}

												total02 = total02 + parseFloat(jumlah_harga_dasar);
												total03 = total03 + parseFloat(jumlah_ongkos_angkut);
												total04 = total04 + parseFloat(jumlah_pbbkb);
												total05 = total05 + parseFloat(jumlah_harga_dasar_oa);
												total06 = total06 + parseFloat(jumlah_harga_dasar_pbbkb);
											}

											if (row.no_do_acurate == null) {
												var no_do = row.no_do_syop;
											} else {
												var no_do = row.no_do_acurate;
											}
											var isiHtml =
												'<tr data-id="' + newId + '">' +
												'<td class="text-center"><span class="frmnodasar" data-row-count="' + newId + '"></span></td>' +
												'<td class="text-left">' +
												'<p style="margin-bottom:3px;">No PO Customer : ' + row.nomor_poc + '</p>' +
												'<p style="margin-bottom:3px;">No DN : ' + row.no_dn + '</p>' +
												'<p style="margin-bottom:3px;">No SPJ : ' + row.no_spj + '</p>' +
												'<p style="margin-bottom:3px;">No Penawaran : ' + row.nomor_surat + '</p>' +
												'<p style="margin-bottom:3px;">Jenis Angkutan : ' + row.jenisnya.toUpperCase() + '</p>' +
												'<p style="margin-bottom:0px;">' + (row.jenisnya == 'truck' ? 'No Plat : ' + row.angkutan + ' (' + row.sopir + ')' : 'Vessel : ' + row.angkutan + ' (' + row.sopir + ')') + '</p>' +
												'</td>' +
												'<td class="text-left">' +
												'<p style="margin-bottom:3px;">No DO : ' + no_do + '</p>' +
												'<p style="margin-bottom:3px;">No LO : ' + row.nomor_lo_pr + '</p>' +
												'<p style="margin-bottom:3px;">Refund : ' + row.refund_tawar + '</p>' +
												'</td>' +
												'<td class="text-left">' +
												'<input type="text" id="tgl_delivered' + newId + '" name="tgl_delivered[]" class="form-control input-sm" value="' + row.tgl_delivered + '" data-rule-dateNL="true" readonly />' +
												'</td>' +
												'<td class="text-left">' +
												'<input type="text" id="vol_kirim' + newId + '" name="vol_kirim[]" class="form-control input-sm text-right volumenya" value="' + row.volume_po + '"/>' +
												'<p style="margin-bottom:3px;">Volume PO : ' + new Intl.NumberFormat("ja-JP").format(row.volume_po) + '</p>' +
												'<p style="margin-bottom:3px;">Realisasi : ' + new Intl.NumberFormat("ja-JP").format(row.realisasi_volume) + '</p>' +
												'<td class="text-left" width="200">' +
												'<input type="text" id="harga_kirim' + newId + '" name="harga_kirim[]" class="form-control input-sm text-right harganya" value="' + total_penawaran_2 + '"  readonly />' +
												'<input type="hidden" id="harga_kirim' + newId + '" name="harga_kirim_fix[]" class="form-control input-sm text-right harganya_luar" value="' + total_penawaran_luar + '"  readonly />' +
												'<input type="hidden" id="pembulatan' + newId + '" name="pembulatan[]" class="form-control input-sm text-right pembulatannya" value="' + row.pembulatan + '"  readonly />' +
												'<input type="hidden" id="harga_dasar' + newId + '" name="harga_dasar[]" class="form-control input-sm text-right harga_dasarnya" value="' + harga_dasar + '" readonly />' +
												'<input type="hidden" id="ongkos_angkut' + newId + '" name="ongkos_angkut[]" class="form-control input-sm text-right oanya" value="' + ongkos_angkut + '"  readonly />' +
												'<input type="hidden" id="ppn' + newId + '" name="ppn[]" class="form-control input-sm text-right total_ppn_nya" value="' + ppn + '"  readonly />' +
												'<input type="hidden" id="nilai_ppn' + newId + '" name="nilai_ppn[]" class="form-control input-sm text-right ppn_nya" value="' + nilai_ppn + '"  readonly />' +
												'<input type="hidden" id="pbbkb' + newId + '" name="pbbkb[]" class="form-control input-sm text-right pbbkbnya" value="' + pbbkb + '"  readonly />' + penawaran +
												'</td>' +
												'<td class="text-left">' +
												'<input type="hidden" name="id_dsd[]" value="' + row.id_dsd + '" />' +
												'<input type="hidden" name="jenisnya[]" value="' + row.jenisnya + '" />' +
												'<input type="hidden" name="kategori[]" value="' + row.biaya_ppn + '" />' +
												'<input type="hidden" name="refund_tawar[]" value="' + row.refund_tawar + '" />' +
												'<input type="text" id="jumlah_harga' + newId + '" name="jumlah_harga[]" class="form-control input-sm text-right jumlahnya" value="' + jumlahnya + '" readonly />' +
												'<input type="hidden" id="jumlah_harga_dasar' + newId + '" name="jumlah_harga_dasar[]" class="form-control input-sm text-right jumlahnya_harga_dasar" value="' + jumlah_harga_dasar + '" readonly />' +
												'<input type="hidden" id="jumlah_pbbkb' + newId + '" name="jumlah_pbbkb[]" class="form-control input-sm text-right jumlahnya_pbbkb" value="' + jumlah_pbbkb + '" readonly />' +
												'<input type="hidden" id="jumlah_harga_dasar_oa' + newId + '" name="jumlah_harga_dasar_oa[]" class="form-control input-sm text-right jumlahnya_harga_dasar_oa" value="' + jumlah_harga_dasar_oa + '" readonly />' +
												'<input type="hidden" id="jumlah_ongkos_angkut' + newId + '" name="jumlah_ongkos_angkut[]" class="form-control input-sm text-right jumlahnya_ongkos_angkut" value="' + jumlah_ongkos_angkut + '" readonly />' +
												'<input type="hidden" id="jumlah_harga_dasar_pbbkb' + newId + '" name="jumlah_harga_dasar_pbbkb[]" class="form-control input-sm text-right jumlahnya_harga_dasar_pbbkb" value="' + jumlah_harga_dasar_pbbkb + '" readonly />' +
												'</td>' +
												'<td class="text-center">' +
												'<a class="btn btn-action btn-danger hRow jarak-kanan">&nbsp;<i class="fa fa-times"></i>&nbsp;</a>' +
												'' +
												'</td>' +
												'</tr>';
											if (newId == 1) {
												tabel.find('tbody').html(isiHtml);
											} else {
												tabel.find('tbody > tr:last').after(isiHtml);
											}
											$("#tgl_delivered" + newId).datepicker(config.datepicker);
											$("#vol_kirim" + newId).number(true, 4, '.', ',');
											if (row.pembulatan == 2) {
												$("#jumlah_harga" + newId).number(true, 0, '.', ',');
												$("#harga_kirim" + newId).number(true, 4, '.', ',');
											} else if (row.pembulatan == 0) {
												$("#jumlah_harga" + newId).number(true, 0, '.', ',');
												$("#harga_kirim" + newId).number(true, 2, '.', ',');
											} else {
												$("#jumlah_harga" + newId).number(true, 0, '.', ',');
												$("#harga_kirim" + newId).number(true, 0, '.', ',');
											}
											newId++;
										});
										tabel.find("span.frmnodasar").each(function(i, v) {
											$(v).text(i + 1);
										});
										var isiHtml =
											'<tr>' +
											'<td class="text-center" colspan="6"><b>T O T A L</b></td>' +
											'<td class="text-left">' +
											'<input type="text" id="total_invoice" name="total_invoice" class="form-control input-sm text-right" value="' + Math.round(total01) + '" readonly />' +
											'<input type="hidden" id="total_invoice_harga_dasar" name="total_invoice_harga_dasar" class="form-control input-sm text-right" value="' + Math.round(total02) + '" readonly />' +
											'<input type="hidden" id="total_invoice_ongkos_angkut" name="total_invoice_ongkos_angkut" class="form-control input-sm text-right" value="' + Math.round(total03) + '" readonly />' +
											'<input type="hidden" id="total_invoice_pbbkb" name="total_invoice_pbbkb" class="form-control input-sm text-right" value="' + Math.round(total04) + '" readonly />' +
											'<input type="hidden" id="total_invoice_harga_dasar_oa" name="total_invoice_harga_dasar_oa" class="form-control input-sm text-right" value="' + Math.round(total05) + '" readonly />' +
											'<input type="hidden" id="total_invoice_harga_dasar_pbbkb" name="total_invoice_harga_dasar_pbbkb" class="form-control input-sm text-right" value="' + Math.round(total06) + '" readonly />' +
											'</td>' +
											'<td class="text-center">&nbsp;</td>' +
											'</tr>';
										tabel.find('tbody > tr:last').after(isiHtml);

										if (data.items[0].pembulatan == 2) {
											$("#total_invoice").number(true, 0, '.', ',');
											$("#total_invoice_harga_dasar").number(true, 4, '.', ',');
											$("#total_invoice_ongkos_angkut").number(true, 4, '.', ',');
											$("#total_invoice_pbbkb").number(true, 4, '.', ',');
											$("#total_invoice_harga_dasar_oa").number(true, 4, '.', ',');
											$("#total_invoice_harga_dasar_pbbkb").number(true, 4, '.', ',');
										} else if (data.items[0].pembulatan == 0) {
											$("#total_invoice").number(true, 0, '.', ',');
											$("#total_invoice_harga_dasar").number(true, 2, '.', ',');
											$("#total_invoice_ongkos_angkut").number(true, 2, '.', ',');
											$("#total_invoice_pbbkb").number(true, 2, '.', ',');
											$("#total_invoice_harga_dasar_oa").number(true, 2, '.', ',');
											$("#total_invoice_harga_dasar_pbbkb").number(true, 4, '.', ',');
										} else {
											$("#total_invoice").number(true, 0, '.', ',');
											$("#total_invoice_harga_dasar").number(true, 0, '.', ',');
											$("#total_invoice_ongkos_angkut").number(true, 0, '.', ',');
											$("#total_invoice_pbbkb").number(true, 0, '.', ',');
											$("#total_invoice_harga_dasar_oa").number(true, 0, '.', ',');
											$("#total_invoice_harga_dasar_pbbkb").number(true, 0, '.', ',');
										}

										// if (biaya_ppn == 'gabung_pbbkb' || biaya_ppn == 'gabung_pbbkboa' || biaya_ppn == 'all_in') {
										// 	$("#group_items").show(400, "swing");
										// 	$("#group_akun").hide(400, "swing");
										// } else {
										// 	$("#group_akun").show(400, "swing");
										// 	$("#group_items").hide(400, "swing");
										// }

									} else {
										var isiHtml =
											'<tr><td class="text-center" colspan="8">Tidak Ada Data</td></tr>' +
											'<tr>' +
											'<td class="text-center" colspan="6"><b>T O T A L</b></td>' +
											'<td class="text-left">' +
											'<input type="text" id="total_invoice" name="total_invoice" class="form-control input-sm text-right" value="" readonly />' +
											'</td>' +
											'<td class="text-center">&nbsp;</td>' +
											'</tr>';
										$(".table-dasar").find('tbody').html(isiHtml);
										$("#total_invoice").number(true, 0, ',', '.');
									}
								}
							});
							$("#loading_modal").modal("hide");
						} else {
							swal.fire("Harap Mengisi Kolom Customer dan Tanggal");
						}

					}
				} else {
					swal.fire("Silahkan Pilih Invoice By");
				}
			});

			function recalculateTotal() {
				var total = 0;
				var total_harga_dasar = 0;
				var total_HargaDasarPbbkb = 0;
				var total_OA = 0;
				var total_Hsd_OA = 0;
				var total_pbbkb = 0;
				$(".table-dasar tbody tr").each(function() {
					var jumlahHarga = parseFloat($(this).find("input.jumlahnya").val()) || 0;
					var jumlahHargaDasarPbbkb = parseFloat($(this).find("input.jumlahnya_harga_dasar_pbbkb").val()) || 0;
					var jumlahHsdOA = parseFloat($(this).find("input.jumlahnya_harga_dasar_oa").val()) || 0;
					var jumlahPbbkb = parseFloat($(this).find("input.jumlahnya_pbbkb").val()) || 0;
					var jumlahOA = parseFloat($(this).find("input.jumlahnya_ongkos_angkut").val()) || 0;
					var jumlahHargaDasar = parseFloat($(this).find("input.jumlahnya_harga_dasar").val()) || 0;

					total += jumlahHarga;
					total_harga_dasar += isNaN(jumlahHargaDasar) ? 0 : jumlahHargaDasar;
					total_HargaDasarPbbkb += isNaN(jumlahHargaDasarPbbkb) ? 0 : jumlahHargaDasarPbbkb;
					total_OA += isNaN(jumlahOA) ? 0 : jumlahOA;
					total_Hsd_OA += isNaN(jumlahHsdOA) ? 0 : jumlahHsdOA;
					total_pbbkb += isNaN(jumlahPbbkb) ? 0 : jumlahPbbkb;
				});

				$("#total_invoice").val(total);
				$("#total_invoice_edit").val(total);
				$("#total_invoice_harga_dasar").val(total_harga_dasar);
				$("#total_invoice_harga_dasar_pbbkb").val(total_HargaDasarPbbkb);
				$("#total_invoice_ongkos_angkut").val(total_OA);
				$("#total_invoice_harga_dasar_oa").val(total_Hsd_OA);
				$("#total_invoice_pbbkb").val(total_pbbkb);

				// ... Tambahkan logika atau tindakan lain yang diperlukan setelah menghitung total ...
			}

			$(".table-dasar").on("click", "a.addRow1", function() {
				var tabel = $(".table-dasar");
				var arrId = tabel.find("tbody > tr").map(function() {
					return parseFloat($(this).data("id")) || 0;
				}).toArray();
				var rwNom = Math.max.apply(Math, arrId);
				var newId = (rwNom == 0) ? 1 : (rwNom + 1);

				var isiHtml =
					'<tr data-id="' + newId + '">' +
					'<td class="text-center">' +
					'<a class="btn btn-action btn-danger hRow jarak-kanan">&nbsp;<i class="fa fa-times"></i>&nbsp;</a>' +
					'<a class="btn btn-action btn-primary addRow">&nbsp;<i class="fa fa-plus"></i>&nbsp;</a> ' +
					'</td>' +
					'<td class="text-center"><span class="frmnodasar" data-row-count="' + newId + '"></span></td>' +
					'<td class="text-left">' +
					'<input type="text" id="tgl_realisasi' + newId + '" name="tgl_realisasi[' + newId + ']" class="form-control" data-rule-dateNL="true" />' +
					'</td>' +
					'<td class="text-left">' +
					'<select id="triwulan' + newId + '" name="triwulan[' + newId + ']" class="form-control" style="width:100%">' +
					'<option></option>' +
					'<option value="1">TW I</option>' +
					'<option value="2">TW II</option>' +
					'<option value="3">TW III</option>' +
					'<option value="4">TW IV</option>' +
					'</select>' +
					'</td>' +
					'<td class="text-left">' +
					'<input type="text" id="jml_realisasi' + newId + '" name="jml_realisasi[' + newId + ']" class="form-control text-right harganya" />' +
					'</td>' +
					'<td class="text-left">' +
					'<textarea id="catatan' + newId + '" name="catatan[' + newId + ']" class="form-control"></textarea>' +
					'</td>' +
					'</tr>';
				if (rwNom == 0) {
					tabel.find('tbody').html(isiHtml);
				} else {
					$(this).closest('tr').after(isiHtml);
				}
				$("#triwulan" + newId).select2(config.select2);
				$("#tgl_realisasi" + newId).datepicker(config.datepicker);
				$("#jml_realisasi" + newId).number(true, 0, '.', ',');
				tabel.find("span.frmnodasar").each(function(i, v) {
					$(v).text(i + 1);
				});
			}).on("click", "a.hRow1", function() {
				var tabel = $(".table-dasar");
				var jTbl = tabel.find('tbody > tr').length;
				if (jTbl > 1) {
					var cRow = $(this).closest('tr');
					cRow.remove();
					tabel.find("span.frmnodasar").each(function(i, v) {
						$(v).text(i + 1);
					});
					calculate_pengeluaran('00');
				}
			}).on("keyup blur", ".harganya1", function() {
				let idnya = $(this).attr('id').replace('harganya', '');
				calculate_pengeluaran(idnya);
			});

			function calculate_pengeluaran(idnya) {
				let total = 0;
				$(".harganya").each(function(i, v) {
					total = total + ($(v).val() * 1);
				});
				$("#total_realisasi").val(total);
			}

			// Event handler untuk menghapus baris
			$(".table-dasar").on("click", "a.btn-danger", function() {
				// Temukan baris terdekat yang berisi tombol yang diklik
				var row = $(this).closest("tr");

				// Hapus baris tersebut dari tabel
				row.remove();

				// Hitung ulang total setelah menghapus baris
				calculateTotal();
			});

			// Fungsi untuk menghitung ulang total
			function calculateTotal() {
				var total = 0;
				var total_harga_dasar = 0;
				var total_HargaDasarPbbkb = 0;
				var total_OA = 0;
				var total_Hsd_OA = 0;
				var total_pbbkb = 0;

				// Loop melalui semua baris data dalam tabel
				$(".table-dasar tbody tr").each(function() {
					// Ambil nilai jumlah harga dari kolom input
					var jumlahHarga = parseFloat($(this).find("input[name='jumlah_harga[]']").val());
					var jumlahHargaDasar = parseFloat($(this).find("input[name='jumlah_harga_dasar[]']").val());
					var jumlahHargaDasarPbbkb = parseFloat($(this).find("input[name='jumlah_harga_dasar_pbbkb[]']").val());
					var jumlahOA = parseFloat($(this).find("input[name='jumlah_ongkos_angkut[]']").val());
					var jumlahHsdOA = parseFloat($(this).find("input[name='jumlah_harga_dasar_oa[]']").val());
					var jumlahPbbkb = parseFloat($(this).find("input[name='jumlah_pbbkb[]']").val());

					// Tambahkan nilai jumlah harga ke total
					total += isNaN(jumlahHarga) ? 0 : jumlahHarga;
					total_harga_dasar += isNaN(jumlahHargaDasar) ? 0 : jumlahHargaDasar;
					total_HargaDasarPbbkb += isNaN(jumlahHargaDasarPbbkb) ? 0 : jumlahHargaDasarPbbkb;
					total_OA += isNaN(jumlahOA) ? 0 : jumlahOA;
					total_Hsd_OA += isNaN(jumlahHsdOA) ? 0 : jumlahHsdOA;
					total_pbbkb += isNaN(jumlahPbbkb) ? 0 : jumlahPbbkb;
				});

				if (total == 0) {
					$("#btnSbmt").attr("disabled", true);
				}

				// Setel nilai total_invoice ke total yang dihitung ulang
				$("#total_invoice").val(total);
				$("#total_invoice_harga_dasar").val(total_harga_dasar);
				$("#total_invoice_harga_dasar_pbbkb").val(total_HargaDasarPbbkb);
				$("#total_invoice_ongkos_angkut").val(total_OA);
				$("#total_invoice_harga_dasar_oa").val(total_Hsd_OA);
				$("#total_invoice_pbbkb").val(total_pbbkb);
			}

			// Panggil fungsi calculateTotal saat dokumen dimuat
			$(document).ready(function() {
				calculateTotal();
			});

		});
	</script>
</body>

</html>
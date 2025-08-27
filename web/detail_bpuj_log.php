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
$idr 	= isset($enk["id_bpuj"]) ? htmlspecialchars($enk["id_bpuj"], ENT_QUOTES) : '';
$data 	= "SELECT a.*, b.nomor_do FROM pro_bpuj a JOIN pro_po_ds_detail b ON a.id_dsd=b.id_dsd WHERE a.id_bpuj='" . $idr . "' AND a.is_active='1'";
$row 	= $con->getRecord($data);

$query2 = "SELECT a.*, c.pr_pelanggan, i.nama_customer, e.alamat_survey, e.jarak_depot as jarak_lcr, e.lsm_portal, f.nama_prov, g.nama_kab, j.fullname, n.nama_transportir, n.nama_suplier, b.no_spj, k.nomor_plat, k.link_gps, l.nama_sopir, b.volume_po, h.produk_poc, p.id_area, c.pr_vendor, r.nama_terminal, r.tanki_terminal, r.lokasi_terminal, s.wilayah_angkut, m.nomor_po, m.tanggal_po, 
        c.produk, b.tgl_kirim_po, b.mobil_po, c.no_do_acurate, c.no_do_syop, c.nomor_lo_pr, h.nomor_poc, d.tanggal_kirim, d.volume_kirim, m.id_wilayah as id_wilayah_po, b.multidrop_po,
        d.realisasi_kirim,
        i.id_customer,
        m.created_by as pic_logistik,
        d.created_by as pic_cs,
        j.id_user as pic_marketing, o.id_terminal,
        k.max_kap
        from pro_po_ds_detail a 
        join pro_po_ds o on a.id_ds = o.id_ds 
        join pro_po_detail b on a.id_pod = b.id_pod 
        join pro_po m on a.id_po = m.id_po 
        join pro_pr_detail c on a.id_prd = c.id_prd 
        join pro_po_customer_plan d on a.id_plan = d.id_plan 
        join pro_po_customer h on a.id_poc = h.id_poc 
        join pro_customer_lcr e on d.id_lcr = e.id_lcr
        join pro_customer i on h.id_customer = i.id_customer 
        join acl_user j on i.id_marketing = j.id_user 
        join pro_master_provinsi f on e.prov_survey = f.id_prov 
        join pro_master_kabupaten g on e.kab_survey = g.id_kab
        join pro_penawaran p on h.id_penawaran = p.id_penawaran  
        join pro_master_area q on p.id_area = q.id_master 
        join pro_master_transportir_mobil k on b.mobil_po = k.id_master 
        join pro_master_transportir_sopir l on b.sopir_po = l.id_master
        join pro_master_transportir n on m.id_transportir = n.id_master 
        join pro_master_terminal r on o.id_terminal = r.id_master 
        join pro_master_wilayah_angkut s on e.id_wil_oa = s.id_master and e.prov_survey = s.id_prov and e.kab_survey = s.id_kab
        where a.id_dsd = '" . $row['id_dsd'] . "'";

$data_dsd = $con->getRecord($query2);
if ($data_dsd == false) $data_dsd = null;

// $created_time_bpuj = date("H:i:s", strtotime($row['created_at']));
// $diberikan_time_bpuj = date("H:i:s", strtotime($row['diberikan_tgl']));

$data_tambahan 	= "SELECT * FROM pro_bpuj_tambahan_hari WHERE id_bpuj = '" . $idr . "'";
$row2 	= $con->getResult($data_tambahan);

$exp = explode("||", $row['pengisian_bbm']);
$pengisian_bbm = $exp[0];
$id_terminal = $exp[1];
if ($row['dispenser'] != 0) {
	$query_dispenser = "SELECT * FROM pro_master_terminal WHERE id_master = '" . $id_terminal . "'";
	$dispenser = $con->getRecord($query_dispenser);
}

$exp2 = explode("||", $row['pengisian_bbm_tambahan']);
$pengisian_bbm2 = $exp2[0];
$id_terminal2 = $exp2[1];
if ($row['dispenser_tambahan'] != 0) {
	$query_dispenser2 = "SELECT * FROM pro_master_terminal WHERE id_master = '" . $id_terminal2 . "'";
	$dispenser2 = $con->getRecord($query_dispenser2);
}

$exp3 = explode("||", $row['pengisian_bbm_tambahan2']);
$pengisian_bbm3 = $exp3[0];
$id_terminal3 = $exp3[1];
if ($row['dispenser_tambahan2'] != 0) {
	$query_dispenser3 = "SELECT * FROM pro_master_terminal WHERE id_master = '" . $id_terminal3 . "'";
	$dispenser3 = $con->getRecord($query_dispenser3);
}

$queryRealisasi = "SELECT * FROM pro_bpuj_realisasi WHERE id_bpuj='" . $row['id_bpuj'] . "'";
$realisasi = $con->getRecord($queryRealisasi);

if ($realisasi) {
	$data_tambahan_realisasi = "SELECT * FROM pro_bpuj_realisasi_tambahan_hari WHERE id_realisasi = '" . $realisasi['id'] . "'";
	$row3 = $con->getResult($data_tambahan_realisasi);

	if ($realisasi['pengisian_bbm'] != NULL) {
		$exp_realisasi = explode("||", $realisasi['pengisian_bbm']);
		$pengisian_bbm_realisasi = $exp_realisasi[0];
		$id_terminal_realisasi = $exp_realisasi[1];
		if ($realisasi['dispenser'] != 0) {
			$query_dispenser_realisasi = "SELECT * FROM pro_master_terminal WHERE id_master = '" . $id_terminal_realisasi . "'";
			$dispenser_realisasi = $con->getRecord($query_dispenser_realisasi);
		}
	} else {
		$pengisian_bbm_realisasi = $exp[0];
		$dispenser_realisasi = $dispenser;
	}

	if ($realisasi['pengisian_bbm_tambahan'] != NULL) {
		$exp_realisasi2 = explode("||", $realisasi['pengisian_bbm_tambahan']);
		$pengisian_bbm_realisasi2 = $exp_realisasi2[0];
		$id_terminal_realisasi2 = $exp_realisasi2[1];
		if ($realisasi['dispenser_tambahan'] != 0) {
			$query_dispenser_realisasi2 = "SELECT * FROM pro_master_terminal WHERE id_master = '" . $id_terminal_realisasi2 . "'";
			$dispenser_realisasi2 = $con->getRecord($query_dispenser_realisasi2);
		}
	} else {
		$pengisian_bbm_realisasi2 = $exp2[0];
		$dispenser_realisasi2 = $dispenser2;
	}
}

$linkCtkBpuj = ACTION_CLIENT . '/cetak_bpuj.php?' . paramEncrypt('id_bpuj=' . $idr);
$linkCtkRealisasi = ACTION_CLIENT . '/cetak_bpuj_realisasi.php?' . paramEncrypt('id_bpuj=' . $idr);
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("myGrid", "jqueryUI", "formatNumber"), "css" => array("jqueryUI"))); ?>

<style>
	th,
	td {
		padding: 10px;
	}
</style>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory . "/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
		<aside class="right-side">
			<section class="content-header">
				<h1>BPUJ Detail </h1>
			</section>
			<section class="content">
				<?php if ($row) : ?>
					<div class="row">
						<div class="col-md-12">
							<table width="100%" border="0">
								<?php if ($row['diberikan_oleh'] != NULL) : ?>
									<tr>
										<td width="13%">
											<b>Diberikan Tanggal</b>
										</td>
										<td width="1%">
											:
										</td>
										<td>
											<?= tgl_indo($row['diberikan_tgl']) ?>
										</td>
										<td width="15%">
											<b>Diberikan Oleh</b>
										</td>
										<td width="1%">
											:
										</td>
										<td>
											<?= $row['diberikan_oleh'] ?>
										</td>
									</tr>
								<?php endif ?>
								<tr>
									<td>
										<b>Tanggal Kirim BPUJ</b>
									</td>
									<td>
										:
									</td>
									<td>
										<?= tgl_indo($row['tanggal_bpuj']) ?>
									</td>
									<td>
										<b>Tanggal Realisasi</b>
									</td>
									<td>
										:
									</td>
									<td>
										<?php if ($realisasi) : ?>
											<?= tgl_indo($realisasi['tanggal_realisasi']) ?>
										<?php else : ?>
											-
										<?php endif ?>
									</td>
								</tr>
								<tr>
									<td>
										<b>PO Customer</b>
									</td>
									<td>
										:
									</td>
									<td>
										<?= $data_dsd['nomor_poc'] ?>
									</td>
									<td>
										<b>Nomor DN</b>
									</td>
									<td>
										:
									</td>
									<td>
										<?= $row['nomor_do'] ?>
									</td>
								</tr>
								<tr>
									<td>
										<b>Nomor BPUJ</b>
									</td>
									<td>
										:
									</td>
									<td>
										<?= $row['nomor_bpuj'] ?>
									</td>
									<td>
										<b>Nama Customer</b>
									</td>
									<td>
										:
									</td>
									<td>
										<?= $row['nama_customer'] ?>
									</td>
									<td>
										<b>Jasa per Km</b>
									</td>
									<td>
										:
									</td>
									<td>
										Rp. <?= number_format($row['jasa']) ?>
									</td>
								</tr>
								<tr>
									<td>
										<hr style="border: 1px solid black;">
									</td>
								</tr>
								<tr>
									<td>
										<b>Nama Driver</b>
									</td>
									<td>
										:
									</td>
									<td>
										<?= $row['nama_driver'] ?>
									</td>
									<td>
										<b>Nama Driver Realisasi</b>
									</td>
									<td>
										:
									</td>
									<td>
										<?php if ($realisasi) : ?>
											<?= $realisasi['nama_driver'] ?>
										<?php else : ?>
											-
										<?php endif ?>
									</td>
								</tr>
								<tr>
									<td>
										<b>No Unit</b>
									</td>
									<td>
										:
									</td>
									<td>
										<?= $row['no_unit'] ?>
									</td>
									<td>
										<b>No Unit Realisasi</b>
									</td>
									<td>
										:
									</td>
									<td>
										<?php if ($realisasi) : ?>
											<?= $realisasi['no_unit'] ?>
										<?php else : ?>
											-
										<?php endif ?>
									</td>
								</tr>
								<tr>
									<td>
										<b>Status Driver</b>
									</td>
									<td>
										:
									</td>
									<td>
										<?= ucwords($row['status_driver']) ?>
									</td>
									<td>
										<b>Status Driver Realisasi</b>
									</td>
									<td>
										:
									</td>
									<td>
										<?php if ($realisasi) : ?>
											<?= ucwords($realisasi['status_driver']) ?>
										<?php else : ?>
											-
										<?php endif ?>
									</td>
								</tr>
								<tr>
									<td>
										<hr style="border: 1px solid black;">
									</td>
								</tr>
								<tr>
									<td>
										<b>Jarak LCR</b>
									</td>
									<td>
										:
									</td>
									<td>
										<?= $row['jarak_real_lcr'] ?> Km
									</td>
								</tr>
								<tr>
									<td>
										<b>Jarak Real</b>
									</td>
									<td>
										:
									</td>
									<td>
										<?= $row['jarak_real'] ?> Km
									</td>

									<td>
										<b>Jarak Real Realisasi</b>
									</td>
									<td>
										:
									</td>
									<td>
										<?php if ($realisasi) : ?>
											<?= $realisasi['jarak_real'] ?> Km
										<?php else : ?>
											-
										<?php endif ?>
									</td>
								</tr>
								<tr>
									<td>
										<hr style="border: 1px solid black;">
									</td>
								</tr>
								<tr>
									<td>
										<b>Jenis Tangki</b>
									</td>
									<td>
										:
									</td>
									<td>
										<?= $row['jenis_tangki'] ?> KL
									</td>
								</tr>
								<tr>
									<td>
										<b>Pengisian BBM</b>
									</td>
									<td>
										:
									</td>
									<td>
										<?php
										if ($row['dispenser'] != 0) {
											echo $dispenser['nama_terminal'] . " - " . $dispenser['tanki_terminal'];
										} else {
											echo $pengisian_bbm;
										}
										?>
									</td>
									<td>
										<b>BBM</b>
									</td>
									<td>
										:
									</td>
									<td>
										<?php if (fmod($row['liter_bbm'], 1) !== 0.000) : ?>
											<?= number_format($row['liter_bbm'], 3, ",", ".") ?> Liter
										<?php else : ?>
											<?= number_format($row['liter_bbm']) ?> Liter
										<?php endif ?>
									</td>
									<?php if ($row['tgl_pengisian'] != NULL) : ?>
										<td>
											<b>Tgl Pengisian</b>
										</td>
										<td>
											:
										</td>
										<td>
											<?= tgl_indo($row['tgl_pengisian']) ?>
										</td>
									<?php endif ?>
								</tr>
								<?php if ($row['pengisian_bbm_tambahan'] != NULL) : ?>
									<tr>
										<td>
											<b>Pengisian BBM</b>
										</td>
										<td>
											:
										</td>
										<td>
											<?php
											if ($row['dispenser_tambahan'] != 0) {
												echo $dispenser2['nama_terminal'] . " - " . $dispenser2['tanki_terminal'];
											} else {
												echo $pengisian_bbm2;
											}
											?>
										</td>
										<td>
											<b>BBM</b>
										</td>
										<td>
											:
										</td>
										<td>
											<?php if (fmod($row['liter_bbm_tambahan'], 1) !== 0.000) : ?>
												<?= number_format($row['liter_bbm_tambahan'], 3, ",", ".") ?> Liter
											<?php else : ?>
												<?= number_format($row['liter_bbm_tambahan']) ?> Liter
											<?php endif ?>
										</td>
										<?php if ($row['tgl_pengisian_tambahan'] != NULL) : ?>
											<td>
												<b>Tgl Pengisian</b>
											</td>
											<td>
												:
											</td>
											<td>
												<?= tgl_indo($row['tgl_pengisian_tambahan']) ?>
											</td>
										<?php endif ?>
									</tr>
								<?php endif ?>
								<?php if ($row['pengisian_bbm_tambahan2'] != NULL) : ?>
									<tr>
										<td>
											<b>Pengisian BBM</b>
										</td>
										<td>
											:
										</td>
										<td>
											<?php
											if ($row['dispenser_tambahan2'] != 0) {
												echo $dispenser3['nama_terminal'] . " - " . $dispenser3['tanki_terminal'];
											} else {
												echo $pengisian_bbm3;
											}
											?>
										</td>
										<td>
											<b>BBM</b>
										</td>
										<td>
											:
										</td>
										<td>
											<?php if (fmod($row['liter_bbm_tambahan2'], 1) !== 0.000) : ?>
												<?= number_format($row['liter_bbm_tambahan2'], 3, ",", ".") ?> Liter
											<?php else : ?>
												<?= number_format($row['liter_bbm_tambahan2']) ?> Liter
											<?php endif ?>
										</td>
										<?php if ($row['tgl_pengisian_tambahan2'] != NULL) : ?>
											<td>
												<b>Tgl Pengisian</b>
											</td>
											<td>
												:
											</td>
											<td>
												<?= tgl_indo($row['tgl_pengisian_tambahan2']) ?>
											</td>
										<?php endif ?>
									</tr>
								<?php endif ?>
								<tr>
									<td>
										<hr style="border: 1px solid black;">
									</td>
								</tr>
								<?php if ($realisasi) : ?>
									<tr>
										<td>
											<b>Pengisian BBM Realisasi</b>
										</td>
										<td>
											:
										</td>
										<td>
											<?php
											if ($realisasi['dispenser'] != 0) {
												echo $dispenser_realisasi['nama_terminal'] . " - " . $dispenser_realisasi['tanki_terminal'];
											} else {
												echo $pengisian_bbm_realisasi;
											}
											?>
										</td>
										<td>
											<b>Realisasi BBM</b>
										</td>
										<td>
											:
										</td>
										<td>
											<?php if (fmod($realisasi['liter_bbm'], 1) !== 0.000) : ?>
												<?= number_format($realisasi['liter_bbm'], 3, ",", ".") ?> Liter
											<?php else : ?>
												<?= number_format($realisasi['liter_bbm']) ?> Liter
											<?php endif ?>
											<input type="hidden" name="liter_bbm" id="liter_bbm" value="<?= $realisasi['liter_bbm'] ?>">
										</td>
										<?php if ($realisasi['tgl_pengisian'] != NULL) : ?>
											<td>
												<b>Tgl Pengisian</b>
											</td>
											<td>
												:
											</td>
											<td>
												<?= tgl_indo($realisasi['tgl_pengisian']) ?>
											</td>
										<?php endif ?>
									</tr>
									<?php if ($realisasi['pengisian_bbm_tambahan'] != NULL) : ?>
										<tr>
											<td>
												<b>Pengisian BBM Realisasi</b>
											</td>
											<td>
												:
											</td>
											<td>
												<?php
												if ($realisasi['dispenser_tambahan'] != 0) {
													echo $dispenser_realisasi2['nama_terminal'] . " - " . $dispenser_realisasi2['tanki_terminal'];
												} else {
													echo $pengisian_bbm_realisasi2;
												}
												?>
											</td>
											<td>
												<b>Realisasi BBM</b>
											</td>
											<td>
												:
											</td>
											<td>
												<?php if (fmod($realisasi['liter_bbm_tambahan'], 1) !== 0.000) : ?>
													<?= number_format($realisasi['liter_bbm_tambahan'], 3, ",", ".") ?> Liter
												<?php else : ?>
													<?= number_format($realisasi['liter_bbm_tambahan']) ?> Liter
												<?php endif ?>
												<input type="hidden" name="liter_bbm2" id="liter_bbm2" value="<?= $realisasi['liter_bbm_tambahan'] ?>">
											</td>
											<?php if ($realisasi['tgl_pengisian_tambahan'] != NULL) : ?>
												<td>
													<b>Tgl Pengisian</b>
												</td>
												<td>
													:
												</td>
												<td>
													<?= tgl_indo($realisasi['tgl_pengisian_tambahan']) ?>
												</td>
											<?php endif ?>
										</tr>
									<?php endif ?>
								<?php endif ?>
							</table>
							<hr>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<?php if ($row['disposisi_bpuj'] == 2) : ?>
								<div class="row">
									<div class="col-md-12">
										<center>
											<a target="_blank" class="margin-sm btn btn-success btn-md" title="Cetak BPUJ" href="<?= $linkCtkBpuj ?>"><i class="fa fa-file"></i> Cetak PDF</a>
										</center>
									</div>
								</div>
								<br>
							<?php endif ?>
							<center>
								<h4>Pengajuan Awal</h4>
							</center>
							<table width="100%" border="1">
								<tr>
									<th class="text-center">
										Keterangan
									</th>
									<th class="text-center">
										Amount (Rp)
									</th>
								</tr>
								<tr>
									<td>
										<b>Jasa</b>
									</td>
									<td class="text-right">
										<?= number_format($row['total_jasa']) ?>
									</td>
								</tr>
								<tr>
									<td>
										<b>BBM</b>
									</td>
									<td class="text-right">
										<?= number_format($row['total_bbm']) ?>
									</td>
								</tr>
								<tr>
									<td>
										<b>Tol</b>
									</td>
									<td class="text-right">
										<?= number_format($row['uang_tol']) ?>
									</td>
								</tr>
								<tr>
									<td>
										<b>Uang Makan + Parkir + Meal</b>
									</td>
									<td class="text-right">
										<?= number_format($row['uang_makan']) ?>
									</td>
								</tr>
								<?php if (!empty($row2)) : ?>
									<?php $hari = 2; ?>
									<?php foreach ($row2 as $key) : ?>
										<tr>
											<td>
												<b>Uang Makan + Parkir + Meal Hari ke <?= $hari++ ?></b>
											</td>
											<td class="text-right">
												<?= number_format($key['uang_makan']) ?>
											</td>
										</tr>
									<?php endforeach ?>
								<?php endif ?>
								<tr style="background-color: #ffcc99;">
									<td colspan="2" class="text-center"><b>OTHER COST</b></td>
								</tr>
								<tr>
									<td>
										<b>Kernet</b>
									</td>
									<td class="text-right">
										<?= number_format($row['uang_kernet']) ?>
									</td>
								</tr>
								<?php if (!empty($row2)) : ?>
									<?php $hari = 2; ?>
									<?php foreach ($row2 as $key) : ?>
										<tr>
											<td>
												<b>Uang Kernet Hari ke <?= $hari++ ?></b>
											</td>
											<td class="text-right">
												<?= number_format($key['uang_kernet']) ?>
											</td>
										</tr>
									<?php endforeach ?>
								<?php endif ?>
								<tr>
									<td>
										<b>Biaya Perjalanan</b>
									</td>
									<td class="text-right">
										<?= number_format(0) ?>
									</td>
								</tr>
								<?php if (!empty($row2)) : ?>
									<?php $hari = 2; ?>
									<?php foreach ($row2 as $key) : ?>
										<tr>
											<td>
												<b>Biaya Perjalanan Hari ke <?= $hari++ ?></b>
											</td>
											<td class="text-right">
												<?= number_format($key['biaya_perjalanan']) ?>
											</td>
										</tr>
									<?php endforeach ?>
								<?php endif ?>
								<tr>
									<td>
										<b>Demmurade</b>
									</td>
									<td class="text-right">
										<?= number_format($row['uang_demmurade']) ?>
									</td>
								</tr>
								<tr>
									<td>
										<b>Koordinasi</b>
									</td>
									<td class="text-right">
										<?= number_format($row['uang_koordinasi']) ?>
									</td>
								</tr>
								<tr>
									<td>
										<b>Multidrop</b>
									</td>
									<td class="text-right">
										<?= number_format($row['uang_multidrop']) ?>
									</td>
								</tr>
								<tr>
									<td>
										<b>Biaya Penyebrangan</b>
									</td>
									<td class="text-right">
										<?= number_format($row['biaya_penyebrangan']) ?>
									</td>
								</tr>
								<tr>
									<td>
										<b>Biaya Lain</b>
										<br>
										<small>
											Catatan :
											<?php if ($row['catatan_biaya_lain'] != NULL) : ?>
												<p><?= $row['catatan_biaya_lain'] ?></p>
											<?php else : ?>
												-
											<?php endif ?>
										</small>
									</td>
									<td class="text-right">
										<?= number_format($row['biaya_lain']) ?>
									</td>
								</tr>
								<tr>
									<td>
										<b>Total</b>
									</td>
									<td class="text-right">
										<b><?= number_format($row['total_uang_bpuj']) ?></b>
									</td>
								</tr>
								<tr>
									<td>
										<b>Yang dibayarkan</b>
									</td>
									<td class="text-right">
										<b><?= number_format($row['yang_dibayarkan']) ?></b>
									</td>
								</tr>
							</table>
						</div>
						<div class="col-md-6">
							<?php if ($realisasi) : ?>
								<?php if ($realisasi['created_at'] > '2024-06-18') : ?>
									<?php if ($realisasi['disposisi_realisasi'] == 1) : ?>
										<div class="row">
											<div class="col-md-12">
												<center>
													<a target="_blank" class="margin-sm btn btn-success btn-md" title="Cetak BPUJ" href="<?= $linkCtkRealisasi ?>"><i class="fa fa-file"></i> Cetak Realisasi PDF</a>
												</center>
											</div>
										</div>
										<br>
									<?php else : ?>
										<div class="row">
											<div class="col-md-12">
												<center>
													<h4>Realisasi Belum di Approve</h4>
												</center>
											</div>
										</div>
										<br>
									<?php endif ?>
								<?php else : ?>
									<div class="row">
										<div class="col-md-12">
											<center>
												<a target="_blank" class="margin-sm btn btn-success btn-md" title="Cetak BPUJ" href="<?= $linkCtkRealisasi ?>"><i class="fa fa-file"></i> Cetak Realisasi PDF</a>
											</center>
										</div>
									</div>
									<br>
								<?php endif ?>
							<?php endif ?>

							<center>
								<h4>Realisasi</h4>
							</center>
							<?php if ($realisasi) : ?>
								<table width="100%" border="1">
									<tr>
										<th class="text-center">
											Keterangan
										</th>
										<th class="text-center">
											Amount (Rp)
										</th>
									</tr>
									<tr>
										<td>
											<b>Jasa</b>
										</td>
										<td class="text-right">
											<?= number_format($realisasi['total_jasa']) ?>
										</td>
									</tr>
									<tr>
										<td>
											<b>BBM</b>
										</td>
										<td class="text-right">
											<?= number_format($realisasi['total_bbm']) ?>
										</td>
									</tr>
									<tr>
										<td>
											<b>Tol</b>
										</td>
										<td class="text-right">
											<?= number_format($realisasi['uang_tol']) ?>
										</td>
									</tr>
									<tr>
										<td>
											<b>Uang Makan + Parkir + Meal</b>
										</td>
										<td class="text-right">
											<?php if ($row['created_at'] > '2024-06-18') : ?>
												<?= number_format($realisasi['uang_makan']) ?>
											<?php else : ?>
												<?= number_format($row['uang_makan']) ?>
											<?php endif ?>
										</td>
									</tr>
									<?php if ($realisasi) : ?>
										<?php if (!empty($row3) || $row['created_at'] > '2024-06-18') : ?>
											<?php $hari = 2; ?>
											<?php foreach ($row3 as $key) : ?>
												<tr>
													<td>
														<b>Uang Makan + Parkir + Meal Hari ke <?= $hari++ ?></b>
													</td>
													<td class="text-right">
														<?= number_format($key['uang_makan']) ?>
													</td>
												</tr>
											<?php endforeach ?>
										<?php else : ?>
											<?php if (!empty($row2)) : ?>
												<?php $hari = 2; ?>
												<?php foreach ($row2 as $key) : ?>
													<tr>
														<td>
															<b>Uang Makan + Parkir + Meal Hari ke <?= $hari++ ?></b>
														</td>
														<td class="text-right">
															<?= number_format($key['uang_makan']) ?>
														</td>
													</tr>
												<?php endforeach ?>
											<?php endif ?>
										<?php endif ?>
									<?php else : ?>
										<?php if (!empty($row2)) : ?>
											<?php $hari = 2; ?>
											<?php foreach ($row2 as $key) : ?>
												<tr>
													<td>
														<b>Uang Makan + Parkir + Meal Hari ke <?= $hari++ ?></b>
													</td>
													<td class="text-right">
														<?= number_format($key['uang_makan']) ?>
													</td>
												</tr>
											<?php endforeach ?>
										<?php endif ?>
									<?php endif ?>
									<tr style="background-color: #ffcc99;">
										<td colspan="2" class="text-center"><b>OTHER COST</b></td>
									</tr>
									<tr>
										<td>
											<b>Kernet</b>
										</td>
										<td class="text-right">
											<?php if ($row['created_at'] > '2024-06-18') : ?>
												<?= number_format($realisasi['uang_kernet']) ?>
											<?php else : ?>
												<?= number_format($row['uang_kernet']) ?>
											<?php endif ?>
										</td>
									</tr>
									<?php if ($realisasi) : ?>
										<?php if (!empty($row3) || $row['created_at'] > '2024-06-18') : ?>
											<?php $hari = 2; ?>
											<?php foreach ($row3 as $key) : ?>
												<tr>
													<td>
														<b>Uang Kernet Hari ke <?= $hari++ ?></b>
													</td>
													<td class="text-right">
														<?= number_format($key['uang_kernet']) ?>
													</td>
												</tr>
											<?php endforeach ?>
										<?php else : ?>
											<?php if (!empty($row2)) : ?>
												<?php $hari = 2; ?>
												<?php foreach ($row2 as $key) : ?>
													<tr>
														<td>
															<b>Uang Kernet Hari ke <?= $hari++ ?></b>
														</td>
														<td class="text-right">
															<?= number_format($key['uang_kernet']) ?>
														</td>
													</tr>
												<?php endforeach ?>
											<?php endif ?>
										<?php endif ?>
									<?php else : ?>
										<?php if (!empty($row2)) : ?>
											<?php $hari = 2; ?>
											<?php foreach ($row2 as $key) : ?>
												<tr>
													<td>
														<b>Uang Kernet Hari ke <?= $hari++ ?></b>
													</td>
													<td class="text-right">
														<?= number_format($key['uang_kernet']) ?>
													</td>
												</tr>
											<?php endforeach ?>
										<?php endif ?>
									<?php endif ?>
									<tr>
										<td>
											<b>Biaya Perjalanan</b>
										</td>
										<td class="text-right">
											<?= number_format(0) ?>
										</td>
									</tr>
									<?php if ($realisasi) : ?>
										<?php if (!empty($row3) || $row['created_at'] > '2024-06-18') : ?>
											<?php $hari = 2; ?>
											<?php foreach ($row3 as $key) : ?>
												<tr>
													<td>
														<b>Biaya Perjalanan Hari ke <?= $hari++ ?></b>
													</td>
													<td class="text-right">
														<?= number_format($key['biaya_perjalanan']) ?>
													</td>
												</tr>
											<?php endforeach ?>
										<?php else : ?>
											<?php if (!empty($row2)) : ?>
												<?php $hari = 2; ?>
												<?php foreach ($row2 as $key) : ?>
													<tr>
														<td>
															<b>Biaya Perjalanan Hari ke <?= $hari++ ?></b>
														</td>
														<td class="text-right">
															<?= number_format($key['biaya_perjalanan']) ?>
														</td>
													</tr>
												<?php endforeach ?>
											<?php endif ?>
										<?php endif ?>
									<?php else : ?>
										<?php if (!empty($row2)) : ?>
											<?php $hari = 2; ?>
											<?php foreach ($row2 as $key) : ?>
												<tr>
													<td>
														<b>Biaya Perjalanan Hari ke <?= $hari++ ?></b>
													</td>
													<td class="text-right">
														<?= number_format($key['biaya_perjalanan']) ?>
													</td>
												</tr>
											<?php endforeach ?>
										<?php endif ?>
									<?php endif ?>
									<tr>
										<td>
											<b>Demmurade</b>
										</td>
										<td class="text-right">
											<?= number_format($realisasi['uang_demmurade']) ?>
										</td>
									</tr>
									<tr>
										<td>
											<b>Koordinasi</b>
										</td>
										<td class="text-right">
											<?= number_format($realisasi['uang_koordinasi']) ?>
										</td>
									</tr>
									<tr>
										<td>
											<b>Multidrop</b>
										</td>
										<td class="text-right">
											<?= number_format($realisasi['uang_multidrop']) ?>
										</td>
									</tr>
									<tr>
										<td>
											<b>Biaya Lain</b>
											<br>
											<small>
												Catatan :
												<?php if ($realisasi['catatan_biaya_lain'] != NULL) : ?>
													<p><?= $realisasi['catatan_biaya_lain'] ?></p>
												<?php else : ?>
													-
												<?php endif ?>
											</small>
										</td>
										<td class="text-right">
											<?= number_format($realisasi['biaya_lain']) ?>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<b>Catatan</b>
											<br>
											<small>
												<?php if ($realisasi['catatan'] != NULL) : ?>
													<textarea style="border: none; outline: none; resize:none;" readonly><?= $realisasi['catatan'] ?></textarea>
												<?php else : ?>
													-
												<?php endif ?>
											</small>
										</td>
									</tr>
									<tr>
										<td>
											<b>Total</b>
										</td>
										<td class="text-right">
											<b><?= number_format($realisasi['total_realisasi']) ?></b>
										</td>
									</tr>
								</table>
							<?php else : ?>
								<center>
									Belum Realisasi
								</center>
							<?php endif ?>
						</div>
					</div>
				<?php else : ?>
					<center>
						<h2>DATA TIDAK DITEMUKAN</h2>
					</center>
				<?php endif ?>
			</section>
			<?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
		</aside>
	</div>
</body>

</html>
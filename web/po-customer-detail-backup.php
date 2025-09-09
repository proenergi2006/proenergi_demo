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
$idk 	= htmlspecialchars($enk["idk"], ENT_QUOTES);

$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$arrRol = array(11 => "BM", 17 => "OM", 18 => "BM");
// $arrRol = array(7=>"BM", 6=>"OM", 4=>"CFO", 15=>"MGR Finance");
$arrPosisi  = array(1 => "Adm Finance", 2 => "BM", 3 => "OM", 4 => "MGR Finance", 5 => "CFO");

$stbatalpo    = htmlspecialchars(isset($enk["stbatalpo"]) ? $enk["stbatalpo"] : '', ENT_QUOTES);

if (isset($stbatalpo)  and $stbatalpo == 1) {
	$oke = true;
	$sql = "UPDATE pro_po_customer_close
                SET
                st_aktif = 'T'
                WHERE id_poc = '" . $idk . "'
                and st_aktif = 'Y'";

	$con->setQuery($sql);
	$oke  = $oke && !$con->hasError();
	if ($oke) {
		$flash->add("success", 'Close PO berhasil dibatalkan', BASE_REFERER);
	}
}


$cekClosePo = "SELECT count(1) cekpo
                FROM pro_po_customer_close
                WHERE ST_AKTIF='Y'
                AND ID_POC='" . $idk . "'";


$rowClosePo = $con->getRecord($cekClosePo);
$cek_po     = $rowClosePo['cekpo'];

$sql = "select a.*, b.nama_customer, c.jenis_produk, c.merk_dagang, d.nomor_surat, d.perhitungan, d.harga_dasar, d.detail_formula, d.volume_tawar, d.sm_wil_summary, d.sm_wil_pic, d.sm_wil_tanggal, e.nama_area, d.refund_tawar, d.pembulatan,
    (select role_approved from pro_sales_confirmation where id_customer=a.id_customer and id_poc=a.id_poc)
        role_approved,
        (select disposisi from pro_sales_confirmation where id_customer=a.id_customer and id_poc=a.id_poc)
        disposisi    
			from pro_po_customer a 
			join pro_customer b on a.id_customer = b.id_customer 
			join pro_master_produk c on a.produk_poc = c.id_master 
			join (pro_penawaran d JOIN pro_master_area e ON e.id_master = d.id_area) on a.id_penawaran = d.id_penawaran 
			where a.id_customer = '" . $idr . "' and a.id_poc = '" . $idk . "'";
$rsm = $con->getRecord($sql);

$sqlRealisasiVol = "select SUM(realisasi_kirim) as jum_vol_realisasi from pro_po_customer_plan where id_poc='" . $idk . "'";
$resVolume = $con->getRecord($sqlRealisasiVol);


$formula = json_decode($rsm['detail_formula'], true);
if ($rsm['perhitungan'] == 1) {
	if ($rsm['pembulatan'] == 0) {
		$harganya = number_format($rsm['harga_dasar'], 2);
		$harga_pocnya = number_format($rsm['harga_poc'], 2);
	} elseif ($rsm['pembulatan'] == 1) {
		$harganya = number_format($rsm['harga_dasar'], 0);
		$harga_pocnya = number_format($rsm['harga_poc'], 0);
	} elseif ($rsm['pembulatan'] == 2) {
		$harganya = number_format($rsm['harga_dasar'], 4);
		$harga_pocnya = number_format($rsm['harga_poc'], 4);
	}
	$nilainya = $rsm['harga_dasar'];
} else {
	$harganya = '';
	$nilainya = '';
	foreach ($formula as $jenis) {
		$harganya .= '<p style="margin-bottom:0px">' . $jenis . '</p>';
	}
}

$link1 = BASE_URL_CLIENT . '/po-customer.php';
$link2 = BASE_URL_CLIENT . '/po-customer-add.php?' . paramEncrypt('idr=' . $idr . '&idk=' . $idk . '&parAttachment=1');
$link5 = BASE_URL_CLIENT . '/po-customer-add.php?' . paramEncrypt('idr=' . $idr . '&idk=' . $idk . '&parClosePo=1');
$link3 = ACTION_CLIENT . '/po-customer-izin.php?' . paramEncrypt('idr=' . $idr . '&idk=' . $idk);
$link4 = BASE_URL_CLIENT . '/po-customer-plan.php?' . paramEncrypt('idr=' . $idr . '&idk=' . $idk);
$pathPt = $public_base_directory . '/files/uploaded_user/lampiran/' . $rsm['lampiran_poc'];
$lampPt = $rsm['lampiran_poc_ori'];

$linkBatalClosePo = BASE_URL_CLIENT . '/po-customer-detail.php?' . paramEncrypt('idr=' . $idr . '&idk=' . $idk . '&stbatalpo=1');

// if($rsm['poc_approved'] == 1)
// 	$disposisi = 'Terverifikasi '.$arrRol[$sesrol].' '.date("d/m/Y H:i:s", strtotime($rsm['tgl_approved'])).' WIB';
// else if($rsm['poc_approved'] == 2)
// 	$disposisi = 'Ditolak '.$arrRol[$sesrol];
// else if($rsm['disposisi_poc'] == 0)
// 	$disposisi = 'Terdaftar';
// else if($rsm['disposisi_poc'] == 1)
// 	$disposisi = 'Verifikasi '.$arrRol[$sesrol];
// else if($rsm['disposisi_poc'] == 2)
// 	$disposisi = 'Verifikasi OM';
// else $disposisi = '';

if ($rsm['poc_approved'] == 1)
	$disposisi = 'Terverifikasi ' . $arrPosisi[$rsm['disposisi']] . '<br/><i>' . date("d/m/Y H:i:s", strtotime($rsm['tgl_approved'])) . '</i> WIB';
else if ($rsm['poc_approved'] == 2) {
	$disposisi = 'Ditolak ' . $arrPosisi[$rsm['disposisi']];
} else if ($rsm['disposisi_poc'] == 0)
	if ($rsm['is_draft'] == 1) {
		$disposisi = 'Draft';
	} else {
		$disposisi = 'Terdaftar';
	}
else if ($rsm['disposisi_poc'] == 1)
	$disposisi = 'Verifikasi ' . $arrPosisi[$rsm['disposisi']];
else $disposisi = '';

$arr_payment = array("COD" => "COD (Cash On Delivery)", "CBD" => "CBD (Cash Before Delivery)");

$sql_penerima_refund = "SELECT a.*, b.nama, b.divisi FROM pro_poc_penerima_refund a JOIN pro_master_penerima_refund b ON a.penerima_refund=b.id WHERE a.id_poc='" . $rsm['id_poc'] . "'";
$res_penerima = $con->getResult($sql_penerima_refund);


$total_invoice = "SELECT * FROM pro_invoice_admin_detail WHERE id_dsd IN (SELECT pd.id_dsd FROM pro_po_customer pc JOIN pro_po_ds_detail pd ON pc.id_poc=pd.id_poc WHERE pc.id_poc = $idk)";
$rowget = $con->num_rows($total_invoice);
$tanggal_sekarang = date("Y-m-d");

// Mengubah tanggal menjadi format timestamp
$tgl_poc = strtotime($data['tanggal_poc']);
$timestamp_sekarang = strtotime($tanggal_sekarang);

// Menghitung selisih hari
$selisih_hari = ($timestamp_sekarang - $tgl_poc) / (60 * 60 * 24);

?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory . "/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
		<aside class="right-side">
			<section class="content-header">
				<h1>Detail PO Customer</h1>
			</section>
			<section class="content">

				<?php if ($enk['idr'] !== '' && isset($enk['idr'])) { ?>
					<?php $flash->display(); ?>
					<div class="row">
						<div class="col-sm-12">
							<div class="box box-primary">
								<div class="box-header with-border">
									<h3 class="box-title">Data PO Customer</h3>
								</div>
								<div class="box-body">
									<div class="table-responsive">
										<table class="table">
											<thead>
												<tr>
													<th colspan="3"><?php echo "Kode Dokumen PO-" . str_pad($rsm['id_poc'], 4, '0', STR_PAD_LEFT); ?></th>
												</tr>
											</thead>
											<tr>
												<td width="180">Nama Customer</td>
												<td width="10">:</td>
												<td><?php echo $rsm['nama_customer']; ?></td>
											</tr>
											<tr>
												<td>TOP Customer</td>
												<td>:</td>
												<td><?php echo (is_numeric($rsm['top_poc'])) ? $rsm['top_poc'] . " Hari" : $arr_payment[$rsm['top_poc']]; ?></td>
											</tr>
											<tr>
												<td>Penawaran</td>
												<td>:</td>
												<td><?php echo $rsm['nomor_surat']; ?></td>
											</tr>
											<tr>
												<td>Volume Penawaran</td>
												<td>:</td>
												<td><?php echo number_format($rsm['volume_tawar']) . ' Liter'; ?></td>
											</tr>
											<tr>
												<td>Harga Penawaran</td>
												<td>:</td>
												<td><?php echo $harganya; ?></td>
											</tr>
											<tr>
												<td colspan="3">&nbsp;</td>
											</tr>
											<tr>
												<td>Nomor PO</td>
												<td>:</td>
												<td><?php echo $rsm['nomor_poc']; ?></td>
											</tr>
											<tr>
												<td>Tanggal PO</td>
												<td>:</td>
												<td><?php echo tgl_indo($rsm['tanggal_poc']); ?></td>
											</tr>
											<tr>
												<td>Tgl Pengiriman</td>
												<td>:</td>
												<td><?php echo tgl_indo($rsm['supply_date']); ?></td>
											</tr>
											<tr>
												<td>Produk</td>
												<td>:</td>
												<td><?php echo $rsm['jenis_produk'] . " " . $rsm['merk_dagang']; ?></td>
											</tr>
											<tr>
												<td>Harga/Liter</td>
												<td>:</td>
												<td><?php echo $harga_pocnya; ?></td>
											</tr>
											<tr>
												<td>Jumlah Volume</td>
												<td>:</td>
												<td><?php echo number_format($rsm['volume_poc']) . " Liter"; ?></td>
											</tr>
											<tr>
												<td>Total Order</td>
												<td>:</td>
												<td><?php echo number_format(($rsm['volume_poc'] * $rsm['harga_poc'])); ?></td>
											</tr>
											<tr class="<?= $rsm['refund_tawar'] == 0 ? 'hide' : '' ?>">
												<td>Refund</td>
												<td>:</td>
												<td><?php echo number_format($rsm['refund_tawar']); ?></td>
											</tr>
											<tr class="<?= $rsm['refund_tawar'] == 0 ? 'hide' : '' ?>">
												<td>Penerima Refund</td>
												<td>:</td>
												<td>
													<?php if ($rsm['tanggal_poc'] >= '2024-11-12') : ?>
														<table width="100%" border="1">
															<thead>
																<tr>
																	<th width="40%" style="padding: 10px;" class="text-center">
																		Nama penerima
																	</th>
																	<th width="20%" class="text-center">
																		Divisi
																	</th>
																	<th width="20%" class="text-center">
																		Terima Refund (per liter)
																	</th>
																</tr>
															</thead>
															<tbody>
																<?php foreach ($res_penerima as $key) : ?>
																	<tr>
																		<td class="text-center">
																			<?= ucwords($key['nama']) ?>
																		</td>
																		<td class="text-center">
																			<?= ucwords($key['divisi']) ?>
																		</td>
																		<td style="padding: 10px;" align="right"> Rp.
																			<?= number_format($key['persentasi_refund']) ?>
																		</td>
																	</tr>
																<?php endforeach ?>
															</tbody>
														</table>
													<?php else : ?>
														<table width="100%" border="1">
															<thead>
																<tr>
																	<th width="40%" style="padding: 10px;" class="text-center">
																		Nama penerima
																	</th>
																	<th width="20%" class="text-center">
																		Divisi
																	</th>
																	<th width="20%" class="text-center">
																		Terima Refund Awal
																	</th>
																	<th width="20%" class="text-center">
																		Terima Refund Akhir
																	</th>
																</tr>
															</thead>
															<tbody>
																<?php foreach ($res_penerima as $key) : ?>
																	<tr>
																		<td class="text-center">
																			<?= ucwords($key['nama']) ?>
																		</td>
																		<td class="text-center">
																			<?= ucwords($key['divisi']) ?>
																		</td>
																		<td style="padding: 10px;" align="right"> Rp.
																			<?= number_format($key['persentasi_refund']) ?>
																		</td>
																		<?php if ($key['terima_refund'] == 0) : ?>
																			<td style="padding: 10px;" align="right">
																				<span>-</span>
																			</td>
																		<?php else : ?>
																			<td style="padding: 10px;" align="right"> Rp.
																				<?= number_format($key['terima_refund']) ?>
																			</td>
																		<?php endif ?>
																	</tr>
																<?php endforeach ?>
															</tbody>
														</table>
													<?php endif ?>
												</td>
											</tr>
											<tr>
												<td>Lampiran</td>
												<td>:</td>
												<td>
													<?php
													if ($rsm['lampiran_poc'] && file_exists($pathPt)) {
														$linkPt = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=2&ktg=POC_" . $idk . "_&file=" . $lampPt);
														echo '<a href="' . $linkPt . '"><i class="fa fa-file-alt jarak-kanan"></i>' . $lampPt . '</a>';
													} else echo '-';
													?></td>
											</tr>
											<tr>
												<td>Disposisi</td>
												<td>:</td>
												<td>
													<?php echo $disposisi; ?>
													<?php if ($rsm['poc_approved'] == 1) { ?>
														<div class="form-group row">
															<div class="col-sm-6">
																<label>Catatan BM</label>
																<div class="form-control" style="height:auto">
																	<?php echo ($rsm['sm_summary']); ?>
																	<p style="margin:10px 0 0; font-size:12px;"><i>
																			<?php echo $rsm['sm_pic'] . " - " . date("d/m/Y H:i:s", strtotime($rsm['sm_tanggal'])) . " WIB"; ?></i>
																	</p>
																</div>
															</div>
														</div>
													<?php } ?>
												</td>
											</tr>
											<?php if ($rsm['poc_approved'] == 2) { ?>
												<tr>
													<td>Alasan Penolakan</td>
													<td>:</td>
													<td><?php echo $rsm['sm_summary']; ?></td>
												</tr>
											<?php } ?>
											<tr>
												<td>
													STATUS
												</td>
												<td>:</td>
												<td>
													<?php if (intval($cek_po) == 0) : ?>
														<?php if ($rsm['is_draft'] == 1) : ?>
															<b>OPEN (Draft)</b>
														<?php else : ?>
															<b>OPEN</b>
														<?php endif ?>
													<?php else : ?>
														<b>CLOSED</b>
													<?php endif ?>
												</td>
											</tr>
										</table>
									</div>

									<div class="row">
										<div class="col-sm-12">
											<div class="pad bg-gray">
												<a class="btn btn-default jarak-kanan" style="width:80px;" href="<?php echo $link1; ?>">Kembali</a>
												<?php if ($selisih_hari < 14 || $rsm['is_edit'] == 0) { ?>
													<a class="btn btn-warning jarak-kanan edit-po" href="javascript:;">Edit Nomor PO</a>
												<?php } ?>
												<?php if ($rsm['disposisi_poc'] == 0 || $rsm['poc_approved'] == 2) : ?>
													<a class="btn btn-primary jarak-kanan" style="width:80px;" href="<?php echo $link2; ?>">Edit</a>
													<?php if ($rsm['is_draft'] == 0) : ?>
														<a class="btn btn-info jarak-kanan izin-pd" style="width:100px;" href="<?php echo $link3; ?>">Persetujuan</a>
													<?php endif ?>
													<a class="btn btn-primary jarak-kanan" href="<?php echo $link2; ?>">Attachment</a>
												<?php else : ?>
													<?php if ($resVolume['jum_vol_realisasi'] < $rsm['volume_poc']) : ?>
														<?php if ($rsm['poc_approved'] == 1) : ?>
															<a class="btn btn-success jarak-kanan" href="<?php echo $link4; ?>">Jadwal Kirim</a>
															<?php if (intval($cek_po) > 0) : ?>
																<a class="btn btn-info jarak-kanan batal-po" href="<?php echo $linkBatalClosePo; ?>">Batal Close PO</a>
															<?php else : ?>
																<a class="btn btn-success jarak-kanan" href="<?php echo $link5; ?>">Close PO</a>
															<?php endif ?>
														<?php endif ?>
													<?php endif ?>
												<?php endif ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

				<!-- Modal edit PO -->
				<div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog modal-sm" style="width:30%;">
						<div class="modal-content">
							<div class="modal-header bg-blue">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">Edit PO Customer</h4>
							</div>
							<div class="modal-body">
								<form action="javascript:;" id="gform" name="gform" method="post" enctype="multipart/form-data" class="form-validasi" role="form">
									<div class="col-sm-12">
										<input type="hidden" name="act" value="update_no_po" />
										<input type="hidden" name="idk" id="idk" value="<?php echo $rsm['id_poc']; ?>" />
										<input type="hidden" name="idr" id="idr" value="<?php echo $rsm['id_customer']; ?>" />
										<input type="hidden" name="total_inv" id="total_inv" value="<?php echo $rowget; ?>" />
										<div class="form-group">
											<label>Nomor PO*</label>
											<input type="text" id="nomor_po_cust" name="nomor_po_cust" class="form-control" value="<?php echo $rsm['nomor_poc']; ?>" />
										</div>
										<div class="form-group">
											<label>Attachment*</label>
											<input type="file" name="attachment_order" id="attachment_order" class="form-control validate[funcCall[fileCheck]]" /></td>
											<p style="font-size:12px;" class="help-block">* Max size 2Mb | .jpg, .png, .rar, .pdf</p>
											<label>Attachment Sebelumnya :</label>
											<div id="link-lampiran">
												<?php
												if ($rsm['lampiran_poc'] && file_exists($pathPt)) {
													$linkPt = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=2&ktg=POC_" . $idk . "_&file=" . $lampPt);
													echo '<a href="' . $linkPt . '"><i class="fa fa-file-alt jarak-kanan"></i>' . $lampPt . '</a>';
												} else echo '-';
												?>
											</div>
										</div>
									</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
							</div>
							</form>
						</div>
					</div>



					<div class="modal fade" id="loading_modal" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-blue">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
			$(window).on("load resize", function() {
				if ($(this).width() < 977) {
					$(".vertical-tab").addClass("collapsed-box");
					$(".vertical-tab").find(".box-tools").show();
					$(".vertical-tab > .vertical-tab-body").hide();
				} else {
					$(".vertical-tab").removeClass("collapsed-box");
					$(".vertical-tab").find(".box-tools").hide();
					$(".vertical-tab > .vertical-tab-body").show();
				}
			});
			$(".izin-pd").click(function() {
				if (confirm("Apakah anda yakin?")) {
					$('#loading_modal').modal({
						backdrop: "static"
					});
					return true;
				} else {
					return false;
				}
			});
			$(".batal-po").click(function() {
				if (confirm("Apakah anda yakin membatalkan Close PO?")) {
					$('#loading_modal').modal({
						backdrop: "static"
					});
					return true;
				} else {
					return false;
				}
			});




			//edit Nomor PO
			//edit Nomor PO
			$(".edit-po").click(function(e) {
				e.preventDefault();

				$('#edit_modal').modal({
					show: true,
					keyboard: false,
				})
			});

			$('#gform').submit(function(e) {
				e.preventDefault();
				var formData = new FormData(this);

				console.log(formData)
				var nomor_po_cust = $("#nomor_po_cust").val();
				var id_cust = $("#idr").val();
				var id_poc = $("#idk").val();
				var total_inv = $("#total_inv").val();
				// Konfirmasi sebelum melanjutkan
				Swal.fire({
					title: "Apakah Anda yakin?",
					text: "Anda hanya dapat melakukan edit sebanyak 1 kali selama 14 hari dari tanggal PO dibuat, pastikan nomor PO sudah sesuai",
					showCancelButton: true,
					confirmButtonText: 'Ya, lanjutkan!',
					cancelButtonText: 'Batal'
				}).then((result) => {
					if (result.isConfirmed) {
						// console.log("Data yang akan dikirim: ", dataToSend);

						// Kirim AJAX request
						$.ajax({
							type: 'POST',
							url: base_url + "/web/action/update-po.php",
							data: formData,
							processData: false,
							cache: false,
							contentType: false,
							dataType: "json",
							success: function(response) {
								console.log(response)
								// Cek respons dari server
								if (response.status === 'success') {
									Swal.fire({
										icon: 'success',
										title: 'Berhasil',
										text: response.message
									}).then(() => {
										// Redirect setelah berhasil
										window.location.href = "<?php echo BASE_URL_CLIENT . '/po-customer.php'; ?>"; // Pastikan URL ini sesuai
									});
								} else if (response.status === 'error') {
									Swal.fire({
										icon: 'error',
										title: 'Error',
										text: response.message
									});
								}
							},
							error: function(xhr, status, error) {
								Swal.fire({
									icon: 'error',
									title: 'Kesalahan',
									text: 'Terjadi kesalahan: ' + error
								});
							}
						});
					} else {
						Swal.fire({
							icon: 'info',
							title: 'Dibatalkan',
							text: 'Proses telah dibatalkan.'
						});
					}
				});
			});


		});
	</script>
</body>

</html>
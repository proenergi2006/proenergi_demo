<div style="overflow-x: scroll" id="table-long">
	<div style="width:1360px; height:auto;">
		<div class="table-responsive-satu">
			<table class="table table-bordered" id="table-grid3">
				<thead>
					<tr>
						<th class="text-center" rowspan="2" width="100">Split</th>
						<th class="text-center" rowspan="2" width="50">No</th>
						<th class="text-center" rowspan="2" width="200">Nama Customer</th>
						<th class="text-center" rowspan="2" width="230">Area/ Alamat Kirim/ Wilayah OA</th>
						<th class="text-center" rowspan="2" width="160">PO Customer</th>
						<th class="text-center" rowspan="2" width="125">Catatan</th>
						<th class="text-center" rowspan="2" width="80">Angkutan</th>
						<th class="text-center" colspan="2">Quantity</th>
						<th class="text-center" rowspan="2" width="120">Depot</th>
						<th class="text-center" rowspan="2" width="150">Keterangan Lain</th>
					</tr>
					<tr>
						<th class="text-center" width="65">Volume (Liter)</th>
						<th class="text-center" width="80">Edit (Liter)</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$sql = "
						select a.*, b.sm_result, b.sm_summary, b.sm_pic, b.sm_tanggal, b.purchasing_result, b.purchasing_summary, b.purchasing_pic, b.purchasing_tanggal, 
						b.cfo_result, b.cfo_summary, b.cfo_pic, b.cfo_tanggal, b.is_ceo, 
						b.ceo_result, b.ceo_summary, b.ceo_pic, b.ceo_tanggal, 
						b.coo_result, b.coo_summary, b.coo_pic, b.coo_tanggal, 
						c.tanggal_kirim, e.alamat_survey, e.id_wil_oa, f.nama_prov, g.nama_kab, n.nilai_pbbkb, 
						k.id_penawaran, k.masa_awal, k.masa_akhir, k.id_area, k.flag_approval, 
						k.refund_tawar, k.other_cost, k.perhitungan, k.detail_rincian, k.harga_dasar,
						o1.harga_normal, o2.harga_normal as harga_normal_new, 
						h.nama_customer, h.id_customer, i.fullname, l.nama_area, d.harga_poc, k.refund_tawar, k.other_cost, m.jenis_produk, e.jenis_usaha, d.nomor_poc, d.produk_poc, 
						p.nama_terminal, p.tanki_terminal, p.lokasi_terminal, q.nama_vendor, r.wilayah_angkut, m.merk_dagang, d.lampiran_poc, d.lampiran_poc_ori, d.id_poc, 
						h.kode_pelanggan, c.status_jadwal, t.id_pod, t.id_dsd, u.id_dsk 
						from pro_pr_detail a 
						join pro_pr b on a.id_pr = b.id_pr 
						join pro_po_customer_plan c on a.id_plan = c.id_plan 
						join pro_po_customer d on c.id_poc = d.id_poc 
						join pro_customer_lcr e on c.id_lcr = e.id_lcr
						join pro_master_provinsi f on e.prov_survey = f.id_prov 
						join pro_master_kabupaten g on e.kab_survey = g.id_kab
						join pro_customer h on d.id_customer = h.id_customer 
						join acl_user i on h.id_marketing = i.id_user 
						join pro_master_cabang j on h.id_wilayah = j.id_master 
						join pro_penawaran k on d.id_penawaran = k.id_penawaran  
						join pro_master_area l on k.id_area = l.id_master 
						join pro_master_produk m on d.produk_poc = m.id_master 
						join pro_master_pbbkb n on k.pbbkb_tawar = n.id_master 
						left join pro_master_harga_minyak o1 on k.masa_awal = o1.periode_awal and k.masa_akhir = o1.periode_akhir and k.id_area = o1.id_area  and k.produk_tawar = o1.produk
							and k.pbbkb_tawar = o1.pajak and o1.is_approved = 1 
						left join pro_master_harga_minyak o2 on k.masa_awal = o2.periode_awal and k.masa_akhir = o2.periode_akhir and k.id_area = o2.id_area  and k.produk_tawar = o2.produk
							and o2.pajak = 1 and o2.is_approved = 1 
						left join pro_master_terminal p on a.pr_terminal = p.id_master 
						left join pro_master_vendor q on a.pr_vendor = q.id_master 
						left join pro_master_wilayah_angkut r on e.id_wil_oa = r.id_master and e.prov_survey = r.id_prov and e.kab_survey = r.id_kab 
						left join pro_po_ds_detail t on a.id_prd = t.id_prd 
						left join pro_po_ds_kapal u on a.id_prd = u.id_prd 
						where a.id_pr = '" . $idr . "' and ((b.disposisi_pr < 3) or (b.disposisi_pr > 2 and a.is_approved = 1))
						order by a.is_approved desc, c.tanggal_kirim, k.id_cabang, k.id_area, a.id_plan, a.id_prd
					";
					$res = $con->getResult($sql);
					$fnr = $row[0]['disposisi_pr'];
					$edt = $row[0]['is_edited'];
					if (count($res) == 0) {
						echo '<tr><td colspan="10" style="text-align:center">Data tidak ditemukan ' . $idr . ' </td></tr>';
					} else {
						$nom = 0;
						$jum = 0;
						foreach ($res as $data) {

							$id_poc_sc[] = $data['id_poc'];

							$nom++;
							$idp 	= $data['id_prd'];
							$idl 	= $data['id_plan'];
							$linkCtk3	= ACTION_CLIENT . "/delivery-order-detail-cetak.php?" . paramEncrypt("idp=" . $idp);

							$tempal = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
							$alamat	= $data['alamat_survey'] . " " . ucwords($tempal) . " " . $data['nama_prov'];

							$pbbkbT = ($data['nilai_pbbkb'] / 100) + 1.11;
							$oildus = $data['harga_poc'] / $pbbkbT * 0.003;
							$pbbkbN = $data['harga_poc'] / $pbbkbT * ($data['nilai_pbbkb'] / 100);
							$tmphrg = $data['refund_tawar'] + $oildus + $data['transport'] + $pbbkbN + $data['other_cost'];
							$nethrg = $data['harga_poc'] - $tmphrg;
							$volume = $data['volume'];

							$volori 	= ($data['vol_ori']) ? $data['vol_ori'] : $data['volume'];
							$voloripr 	= ($data['vol_ori_pr']) ? $data['vol_ori_pr'] : $data['volume'];

							$netgnl = ($nethrg - $data['harga_normal']) * $volume;
							$netprt = ($nethrg - $data['pr_harga_beli']) * $volume;
							$total1 = 0;
							$total2 = 0;
							$total3 = 0;
							$total4 = 0;
							$total1 = $total1 + $volume;
							$total2 = $total2 + $data['vol_ket'];
							$total3 = $total3 + $netprt;
							$total4 = $total4 + $netgnl;
							$checked = ($data['is_approved']) ? ' checked' : '';
							$flagEd = !$data['id_pod'] && !$data['id_dsd'] && !$data['id_dsk'];
							$class1 = "form-control input-po hitung toa";

							$tmn0 	= $data['pr_terminal'];
							$tmn1 	= ($data['nama_terminal']) ? $data['nama_terminal'] : '';
							$tmn2 	= ($data['tanki_terminal']) ? '<br />' . $data['tanki_terminal'] : '';
							$tmn3 	= ($data['lokasi_terminal']) ? ', ' . $data['lokasi_terminal'] : '';
							$depot 	= $tmn1 . $tmn2 . $tmn3;

							$pathPt = $public_base_directory . '/files/uploaded_user/lampiran/' . $data['lampiran_poc'];
							$lampPt = $data['lampiran_poc_ori'];
							if ($data['lampiran_poc'] && file_exists($pathPt)) {
								$linkPt = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=2&ktg=POC_" . $data['id_poc'] . "_&file=" . $lampPt);
								// $attach = '<a href="'.$linkPt.'"><i class="fa fa-paperclip" title="'.$lampPt.'"></i> PO Customer</a>';
							} else {
								$attach = '';
							}

					?>
							<tr>
								<td class="text-center">
									<?php
									if ($fnr > 5 && $flagEd) {
										$jum++;
										echo '<input type="hidden" name="cek[' . $idl . '][' . $idp . ']" id="cek' . $nom . '" value="1" />';
										if ($data['splitted_from']) {
											echo '<button type="button" id="ery' . $nom . '" class="btn btn-action btn-warning resetRow" data-cnt="1" value="' . paramEncrypt($idl . '|#|' . $idp . '|#|' . $idr) . '">
									<i class="fa fa-undo"></i></button>&nbsp;&nbsp;';
										}
										// 		echo '<button type="button" id="ert' . $nom . '" class="btn btn-action btn-primary addRow" data-cnt="1" value="' . $idl . '">
										// <i class="fa fa-plus"></i></button>';
									} else echo '<input type="hidden" name="cek[' . $idl . '][' . $idp . ']" id="cek' . $nom . '" value="0" />&nbsp;';
									echo '<a target="_blank" href="' . $linkCtk3 . '" class="btn btn-primary btn-sm ' . ($fnr == '7' ? '' : 'hide') . '">Cetak DO</a>'
									?></td>
								<td class="text-center"><span class="noFormula"><?php echo $nom; ?></span></td>
								<td>
									<p style="margin-bottom:0px"><?php echo ($data['kode_pelanggan'] ? $data['kode_pelanggan'] : '------'); ?></p>
									<p style="margin-bottom:0px"><?php echo $data['nama_customer']; ?></b></p>
									<p style="margin-bottom:0px"><i><?php echo $data['fullname']; ?></i></p>
								</td>
								<td>
									<p style="margin-bottom:0px"><b><?php echo $data['nama_area']; ?></b></p>
									<p style="margin-bottom:0px"><?php echo $alamat; ?></p>
									<p style="margin-bottom:0px"><?php echo 'Wilayah OA : ' . $data['wilayah_angkut']; ?></p>
								</td>
								<td>
									<p style="margin-bottom:0px"><b><?php echo $data['nomor_poc']; ?></b></p>
									<p style="margin-bottom:0px"><?php echo $data['merk_dagang']; ?></p>
									<p style="margin-bottom:0px"><?php echo 'Tgl Kirim ' . tgl_indo($data['tanggal_kirim']); ?></p>
									<p style="margin-bottom:0px"><?php echo $attach; ?></p>
								</td>
								<td><?php echo $data['status_jadwal']; ?></td>
								<td><?php
									$arrMobil = array(1 => "Truck", "Kapal", "Loco");
									if ($fnr > 5 && $flagEd)
										echo '<select name="dt4[' . $idl . '][' . $idp . ']" id="dt4' . $nom . '" class="form-control input-po">
										<option value="1"' . ($data['pr_mobil'] == 1 ? ' selected' : '') . '>Truck</option>
										<option value="2"' . ($data['pr_mobil'] == 2 ? ' selected' : '') . '>Kapal</option>
										<option value="3"' . ($data['pr_mobil'] == 3 ? ' selected' : '') . '>Loco</option>
									  </select>';
									else echo $arrMobil[$data['pr_mobil']];
									?></td>
								<td class="text-right"><?php echo number_format($data['volume']); ?></td>
								<td class="text-right">
									<?php
									if ($fnr > 5 && $flagEd) echo '<input type="text" name="dt3[' . $idl . '][' . $idp . ']" id="dt3' . $nom . '" class="' . $class1 . '" value="' . $data['volume'] . '" />';
									else echo '<input type="hidden" name="dt3[' . $idl . '][' . $idp . ']" id="dt3' . $nom . '" value="' . $data['volume'] . '" />';
									?></td>
								<td>
									<?php echo '<input type="hidden" name="volori[' . $idl . '][' . $idp . ']" class="hdn" value="' . $volori . '" />'; ?>
									<?php echo '<input type="hidden" name="voloripr[' . $idl . ']" class="hdn" value="' . $voloripr . '" />'; ?>
									<?php echo '<input type="hidden" name="dt5[' . $idl . '][' . $idp . ']" id="dt5' . $nom . '" value="' . $tmn0 . '" />'; ?>

									<p style="margin-bottom:0px"><?php echo $depot; ?></p>
								</td>
								<td class="text-left">
									<p style="margin-bottom:0px"><b>NO DO Syop : </b></p>
									<p style="margin-bottom:5px"><?php echo ($data['no_do_syop'] ? $data['no_do_syop'] : 'N/A'); ?></p>
									<p style="margin-bottom:0px"><b>Loading Order : </b></p>
									<p style="margin-bottom:0px"><?php echo ($data['nomor_lo_pr'] ? $data['nomor_lo_pr'] : 'N/A'); ?></p>
								</td>
							</tr>
					<?php }
					} ?>
				</tbody>
			</table>
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

<div class="form-group row">
	<?php if ($res[0]['sm_result']) { ?>
		<!-- <div class="col-sm-6">
			<label>Catatan BM</label>
			<div class="form-control" style="height:auto">
				<?php echo ($res[0]['sm_summary']); ?>
				<p style="margin:10px 0 0; font-size:12px;"><i><?php echo $res[0]['sm_pic'] . " - " . date("d/m/Y H:i:s", strtotime($res[0]['sm_tanggal'])) . " WIB"; ?></i></p>
			</div>
		</div> -->
	<?php }
	if ($res[0]['purchasing_result']) { ?>
		<div class="col-sm-6 col-sm-top">
			<label>Catatan Purchasing</label>
			<div class="form-control" style="height:auto">
				<?php echo ($res[0]['purchasing_summary']); ?>
				<p style="margin:10px 0 0; font-size:12px;"><i>
						<?php echo $res[0]['purchasing_pic'] . " - " . date("d/m/Y H:i:s", strtotime($res[0]['purchasing_tanggal'])) . " WIB"; ?>
					</i></p>
			</div>
		</div>
	<?php } ?>
</div>
<!-- <div class="form-group row">
	<?php if ($res[0]['coo_result']) { ?>
		<div class="col-sm-6">
			<label>Catatan COO</label>
			<div class="form-control" style="height:auto">
				<?php echo ($res[0]['coo_summary']); ?>
				<p style="margin:10px 0 0; font-size:12px;"><i><?php echo $res[0]['coo_pic'] . " - " . date("d/m/Y H:i:s", strtotime($res[0]['coo_tanggal'])) . " WIB"; ?></i></p>
			</div>
		</div>
	<?php }
	if ($res[0]['ceo_result']) { ?>
		<div class="col-sm-6 col-sm-top">
			<label>Catatan CEO</label>
			<div class="form-control" style="height:auto">
				<?php echo ($res[0]['ceo_summary']); ?>
				<p style="margin:10px 0 0; font-size:12px;"><i><?php echo $res[0]['ceo_pic'] . " - " . date("d/m/Y H:i:s", strtotime($res[0]['ceo_tanggal'])) . " WIB"; ?></i></p>
			</div>
		</div>
	<?php } ?>
</div> -->

<?php if (count($res) > 0) { ?>
	<?php
	// $status_disabled = "";
	// $tgl_sekarang = strtotime(date("Y-m-d H:i:s"));
	// $wilayah = paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]);

	// if ($wilayah == '4' || $wilayah == '5' || $wilayah == '7' || $wilayah == '8' || $wilayah == '10') {
	// 	// Samarinda, Pontianak, Banjarmasin, Palangkaraya dan Bali zona WITA +1 dari WIB
	// 	$waktu_sekarang = date("H:i:s", strtotime("+1 hour"));
	// 	$waktu_tutup = date("17:00:00");
	// } else {
	// 	$waktu_sekarang = date("H:i:s");
	// 	$waktu_tutup = date("16:00:00");
	// }

	// $waktu_buka = date("07:00:00");
	// $tgl_buka = date("Y-m-d 07:00:00", strtotime("+15 hour", $tgl_sekarang));

	// if ($waktu_sekarang >= $waktu_buka && $waktu_sekarang <= $waktu_tutup) {
	// 	$status_disabled = "";
	// } elseif ($waktu_sekarang >= $waktu_tutup || $waktu_sekarang < $waktu_buka) {
	// 	$status_disabled = "disabled";
	// }
	?>

	<?php
	// echo "Wilayah : " . $wilayah;
	// echo "<br>";
	// echo "Waktu Sekarang : " . $waktu_sekarang;
	// echo "<br>";
	// echo "Waktu Buka : " . $waktu_buka;
	// echo "<br>";
	// echo "Waktu Tutup : " . $waktu_tutup;
	// echo "<br>";
	// echo "Tanggal Buka : " . $tgl_buka;
	// echo "<br>";
	?>

	<hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

	<div style="margin-bottom:0px;">
		<!-- <?php if ($status_disabled == "disabled") : ?>
			<span style="color: red;"><b>DR sudah melewati jam 17:00:00, Akan dibuka kembali pada : <?= $tgl_buka ?></b>
			</span>
			<br><br>
		<?php endif ?> -->
		<input type="hidden" name="idr" id="idr" value="<?php echo $idr; ?>" />
		<a href="<?php echo BASE_URL_CLIENT . '/purchase-request.php'; ?>" class="btn btn-default jarak-kanan" style="min-width:90px;">Kembali</a>
		<?php if ($fnr > 5 && $jum > 0) : ?>
			<?php if ($fnr == 7) : ?>
				<a class="btn btn-success jarak-kanan" target="_blank" href="<?php echo $linkCtk1; ?>">Export PDF</a>
				<button type="button" class="btn btn-success jarak-kanan" id="revisiDR" style="min-width:90px;">Kembalikan Ke Terverifikasi</button>
			<?php else : ?>
				<button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">Simpan</button>
			<?php endif ?>
		<?php else : ?>
			<a class="btn btn-success jarak-kanan" target="_blank" href="<?php echo $linkCtk1; ?>">Export PDF</a>
			<?php if ($fnr == 7) : ?>
				<button type="button" class="btn btn-success jarak-kanan" id="revisiDR" style="min-width:90px;">Kembalikan Ke Terverifikasi</button>
			<?php endif ?>
		<?php endif ?>
	</div>
<?php } ?>

<style type="text/css">
	.input-po {
		padding: 3px 5px;
		height: auto;
		font-size: 11px;
		font-family: arial;
	}
</style>
<script>
	$(document).ready(function() {
		$(".hitung").number(true, 0, ".", ",");
		$("form#gform").on("click", "button:submit", function() {
			if (confirm("Apakah anda yakin?")) {
				$("#loading_modal").modal({
					backdrop: "static"
				});
				$.ajax({
					type: 'POST',
					url: "./__cek_pr_customer_logistik.php",
					dataType: "json",
					data: $("#gform").serializeArray(),
					cache: false,
					success: function(data) {
						if (data.error) {
							$("#loading_modal").modal("hide");
							$("#loading_modal").on("hidden.bs.modal", function() {
								$("#preview_modal").find("#preview_alert").html(data.error);
								$("#preview_modal").modal();
								return false;
							});
						} else {
							$("button[type='submit']").addClass("disabled");
							$("#gform").submit();
						}
					}
				});
				return false;
			} else return false;
		});


		$('#revisiDR').click(function() {
			// Konfirmasi sebelum melanjutkan
			Swal.fire({
				title: 'Konfirmasi',
				text: 'Apakah Anda yakin ingin mengembalikan ke status terverifikasi?',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Ya, lanjutkan!',
				cancelButtonText: 'Batal'
			}).then((result) => {
				if (result.isConfirmed) {
					$("#loading_modal").modal({
						backdrop: 'static'
					});
					var idrValue = $('#idr').val();
					// Data yang akan dikirim
					var dataToSend = {
						revisiDR: 1,
						idr: idrValue
					};

					// Kirim AJAX request
					$.ajax({
						type: 'POST',
						url: "./action/cek_status_dr.php",
						data: dataToSend,
						success: function(response) {
							// Cek respons dari server
							if (response.status === 'success') {
								Swal.fire({
									icon: 'success',
									title: 'Berhasil',
									text: 'Revisi DR berhasil!'
								}).then(() => {
									// Redirect setelah berhasil
									window.location.href = "<?php echo BASE_URL_CLIENT . '/purchase-request.php'; ?>"; // Pastikan URL ini sesuai
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
							// Tangani kesalahan
							Swal.fire({
								icon: 'error',
								title: 'Kesalahan',
								text: 'Terjadi kesalahan: ' + error
							});
						}
					});
				} else {
					// Jika pengguna membatalkan
					Swal.fire({
						icon: 'info',
						title: 'Dibatalkan',
						text: 'Proses telah dibatalkan.'
					});
				}
			});
		});


		$("#gform").on("click", "#table-grid3 button.addRow", function() {
			var row = $(this).closest('tr');
			var idl = $(this).val();
			var gfd = $(this).data("cnt");
			$(this).data("cnt", (gfd + 1));
			var tmpId = row.find("input.toa").attr("id");
			var newId = tmpId.substr(4) + "a" + gfd;

			var cloning = row.clone();
			cloning.find('td').each(function(i, v) {
				var el = $(this).find(":first-child");
				var id = el.attr("id") || null;
				switch (i) {
					case 0:
						$(v).html('<button type="button" class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></button>');
						break;
				}
				if (id && i != 0) {
					var elName = "new" + el.attr("name").substr(0, 3) + "[" + idl + "][]";
					var elId = el.attr("id");
					el.attr("id", elId + newId);
					el.attr('name', elName);
				}
			});
			cloning.find('input:text').val("");
			cloning.find(".hdn").remove();
			let tmp_elm = row.find("input[name^='dt3']");
			let elName = "newSplit" + tmp_elm.attr("name");
			let elemen = '<input type="hidden" name="' + elName + '" value="1" />';

			cloning.find("td:last-child").append(elemen);
			row.find("td:last-child").append(elemen);
			row.after(cloning);

			$(".hitung").number(true, 0, ".", ",");
			$("#table-grid3").find(".noFormula").each(function(i, v) {
				$(this).text(i + 1);
			});
		}).on("click", "#table-grid3 button.resetRow", function() {
			if (confirm("Apakah anda yakin?")) {
				$("#loading_modal").modal({
					backdrop: "static"
				});
				var row = $(this).closest('tr');
				var idl = $(this).val();
				window.location.href = $base_url + "/web/action/reset_split_pr.php?idnya=" + idl;
			} else return false;
		});

		$("#gform").on("click", "#table-grid3 button.hRow", function() {
			var cRow = $(this).closest('tr');
			cRow.remove();
			$("#table-grid3").find(".noFormula").each(function(i, v) {
				$(this).text(i + 1);
			});
		});
	});
</script>
<div style="overflow-x: scroll" id="table-long">
	<div style="width:2050px; height:auto;">
		<div class="table-responsive-satu">
			<table class="table table-bordered" id="table-grid3">
				<thead>
					<tr>
						<th class="text-center" rowspan="2" width="50"><input type="checkbox" name="cekAll" id="cekAll" value="1" /></th>
						<th class="text-center" rowspan="2" width="50">No</th>
						<th class="text-center" rowspan="2" width="200">Customer/ Bidang Usaha</th>
						<th class="text-center" rowspan="2" width="230">Area/ Alamat Kirim/ Wilayah OA</th>
						<th class="text-center" rowspan="2" width="200">PO Customer</th>
						<th class="text-center" colspan="2">Quantity</th>
						<th class="text-center" rowspan="2" width="200">Suplier/ Terminal/ Harga Beli</th>
						<th class="text-center" colspan="5">Harga (Rp/Liter)</th>
						<th class="text-center" rowspan="2" width="140">Nett Profit</th>
						<th class="text-center" rowspan="2" width="100">Price List (Harga Dasar)</th>
						<th class="text-center" rowspan="2" width="150">Loading Order</th>
					</tr>
					<tr>
						<th class="text-center" width="100">Volume (Liter)</th>
						<th class="text-center" width="100">Edit (Liter)</th>
						<th class="text-center" width="90">Harga Jual (Gross)</th>
						<th class="text-center" width="200">Rincian Harga</th>
						<th class="text-center" width="80">Harga Dasar (Nett)</th>
						<th class="text-center" width="80">Refund</th>
						<th class="text-center" width="80">Other Cost</th>
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
						k.refund_tawar, k.other_cost, k.perhitungan, k.detail_rincian, k.harga_dasar, k.gabung_oa,
						o1.harga_normal, o2.harga_normal as harga_normal_new, 
						h.nama_customer, h.id_customer, i.fullname, l.nama_area, d.harga_poc, k.refund_tawar, k.other_cost, m.jenis_produk, e.jenis_usaha, d.nomor_poc, d.produk_poc, 
						p.nama_terminal, p.tanki_terminal, p.lokasi_terminal, q.nama_vendor, r.wilayah_angkut, m.merk_dagang, d.lampiran_poc, d.lampiran_poc_ori, d.id_poc, 
						h.kode_pelanggan, b.revert_ceo, b.revert_ceo_summary , b.lampiran_con, a.id_pr, b.lampiran_con_ori
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
						left join pro_master_harga_minyak o1 on k.masa_awal = o1.periode_awal and k.masa_akhir = o1.periode_akhir and k.id_area = o1.id_area 
							and k.pbbkb_tawar = o1.pajak and o1.is_approved = 1 
						left join pro_master_harga_minyak o2 on k.masa_awal = o2.periode_awal and k.masa_akhir = o2.periode_akhir and k.id_area = o2.id_area 
							and o2.pajak = 1 and o2.is_approved = 1 
						left join pro_master_terminal p on a.pr_terminal = p.id_master 
						left join pro_master_vendor q on a.pr_vendor = q.id_master 
						left join pro_master_wilayah_angkut r on e.id_wil_oa = r.id_master 
							and e.prov_survey = r.id_prov and e.kab_survey = r.id_kab 
						where a.id_pr = '" . $idr . "' and a.is_approved = 1 
						order by a.is_approved desc, c.tanggal_kirim, k.id_cabang, k.id_area, a.id_plan, a.id_prd
					";
					$res = $con->getResult($sql);
					$fnr = ($res[0]['ceo_result'] && $res[0]['is_ceo']);
					$fnr = ($fnr ? $fnr : null);
					$arrResult = array("Tidak", "Ya");
					if (count($res) == 0) {
						echo '<tr><td colspan="16" style="text-align:center">Data tidak ditemukan </td></tr>';
					} else {
						$nom = 0;
						$total1 = 0;
						$total2 = 0;
						$total3 = 0;
						$total4 = 0;
						$total5 = 0;
						foreach ($res as $data) {
							$id_poc_sc[] = $data['id_poc'];
							$nom++;
							$idp 	= $data['id_prd'];
							$tempal = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
							$alamat	= $data['alamat_survey'] . " " . ucwords($tempal) . " " . $data['nama_prov'];

							$pbbkbT = ($data['nilai_pbbkb'] / 100) + 1.11;
							$oildus = $data['harga_poc'] / $pbbkbT * 0.003;
							$pbbkbN = $data['harga_poc'] / $pbbkbT * ($data['nilai_pbbkb'] / 100);
							$tmphrg = $data['refund_tawar'] + $oildus + $data['transport'] + $pbbkbN + $data['other_cost'];
							$nethrg = $data['harga_poc'] - $tmphrg;
							$volume = $data['volume'];
							$netgnl = ($nethrg - $data['harga_normal']) * $volume;
							$netprt = ($nethrg - $data['pr_harga_beli']) * $volume;
							$othercost = $data['other_cost'];
							$total5 = $total5 + $othercost;
							$checked = ($data['is_approved']) ? ' checked' : '';

							$tmn1 	= ($data['nama_terminal']) ? $data['nama_terminal'] : '';
							$tmn2 	= ($data['tanki_terminal']) ? '<br />' . $data['tanki_terminal'] : '';
							$tmn3 	= ($data['lokasi_terminal']) ? ', ' . $data['lokasi_terminal'] : '';
							$depot 	= $tmn1 . $tmn2 . $tmn3;

							$pathPt 	= $public_base_directory . '/files/uploaded_user/urgent/' . $data['lampiran_con'];
							$lampPt 	= $data['lampiran_con_ori'];

							if ($data['lampiran_con'] && file_exists($pathPt)) {
								$linkPt = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=6&ktg=URG_" . $data['id_pr'] . "_&file=" . $lampPt);
								$attach = '<a href="' . $linkPt . '"><i class="fa fa-file-alt" title="' . $lampPt . '"></i> ' . $lampPt . '</a>';
							} else {
								$attach = '-';
							}

							$rincian = json_decode($data['detail_rincian'], true);
							$tabel_harga = '<table border="0" cellpadding="" cellspacing="0" width="200">';
							$harga_dasar_new = 0;
							foreach ($rincian as $idx23 => $arr1) {
								$cetak = 1;
								$nilai = $arr1['nilai'];
								$biaya = ($arr1['biaya']) ? $arr1['biaya'] : '';
								$biaya = ($rsm['pembulatan']) ? number_format($arr1['biaya']) : number_format($arr1['biaya'], 2);
								$jenis = $arr1['rincian'];
								if ($idx23 == 0) {
									$harga_dasar_new = str_replace(",", "", $biaya);
								}

								$tabel_harga .= '
								<tr>
									<td align="left" witdh="110">' . $jenis . ($nilai ? " " . $nilai . "%" : "") . '</td>
									<td align="right">' . $biaya . '</td>
								</tr>
								';
							}
							$tabel_harga .= '
							<tr>
								<td align="left" colspan="2">' . ($data['gabung_oa'] ? '<p style="margin:5px 0px 0px;"><i>* Harga Dasar Inc. OA</i></p>' : '') . '</td>
							</tr>';
							$tabel_harga .= '</table>';

							$tmphrg = $data['refund_tawar'] + $data['other_cost'];
							$nethrg = $harga_dasar_new - $tmphrg;
							$netprt = ($harga_dasar_new - $tmphrg - $data['pr_harga_beli']) * $volume;
							$total1 = $total1 + $volume;
							$total2 = $total2 + $data['vol_ket'];
							$total4 = $total4 + $netgnl;
							$total3 = $total3 + $netprt;

					?>
							<tr>
								<td class="text-center">
									<?php
									if (!$fnr) {
										echo '<input type="checkbox" name="cek[' . $idp . ']" id="cek' . $nom . '" class="chkp" value="1"' . $checked . ' />';
										echo '<input type="hidden" name="vol[' . $idp . ']" id="vol' . $nom . '" value="' . $data['volume'] . '" />';
									} else {
										echo ($data['is_approved']) ? '<i class="fa fa-check"></i>' : '&nbsp;';
									}
									?></td>
								<td class="text-center"><?php echo $nom; ?></td>
								<td>
									<p style="margin-bottom:0px"><b><?php echo ($data['kode_pelanggan'] ? $data['kode_pelanggan'] . ' - ' : '') . $data['nama_customer']; ?></b></p>
									<p style="margin-bottom:0px"><?php echo $data['jenis_usaha']; ?></p>
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

								</td>
								<td class="text-right"><?php echo number_format($volume); ?></td>
								<td class="text-right">
									<?php
									if (!$fnr) echo '<input type="text" name="ket[' . $idp . ']" id="ket' . $nom . '" class="form-control input-po hitung" value="' . $data['vol_ket'] . '" />';
									else echo ($data['vol_ket']) ? number_format($data['vol_ket']) : '&nbsp;';
									?>
								</td>
								<td>
									<p style="margin-bottom:0px"><b><?php echo $data['nama_vendor']; ?></b></p>
									<p style="margin-bottom:0px"><?php echo $depot; ?></p>
									<?php
									if (!$fnr) echo '<input type="text" name="dp2[' . $idp . ']" id="dp2' . $nom . '" class="form-control input-po hitung" value="' . $data['pr_harga_beli'] . '" />';
									else echo '<p style="margin-bottom:0px">' . number_format($data['pr_harga_beli']) . '</p>';
									?>
								</td>
								<td class="text-right"><?php echo number_format($data['harga_poc']); ?></td>
								<td class="text-left"><?php echo $tabel_harga; ?></td>
								<td class="text-right"><?php echo number_format($harga_dasar_new); ?></td>
								<td class="text-right"><?php echo number_format($data['refund_tawar']); ?></td>
								<td class="text-right"><?php echo number_format($data['other_cost']); ?></td>
								<td class="text-right"><?php echo number_format($netprt); ?></td>
								<td class="text-right"><?php echo number_format($data['pr_price_list']); ?></td>
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
				<tfoot>
					<tr>
						<th colspan="5" class="text-center"><b>TOTAL</b></th>
						<th class="text-right"><?php echo number_format($total1 ?? 0); ?></th>
						<th colspan="7" class="text-center">&nbsp;</th>
						<th class="text-right"><?php echo number_format($total3 ?? 0); ?></th>
						<th class="text-right">&nbsp;</th>
						<th class="text-right">&nbsp;</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>

<?php if ($res) { ?>
	<div class="form-group row">
		<!-- <div class="col-sm-4">
			<label>Catatan BM</label>
			<div class="form-control" style="height:auto">
				<?php echo $res[0]['sm_summary'] ?? ''; ?>
				<p style="margin:10px 0 0; font-size:12px;"><i><?php echo $res ? $res[0]['sm_pic'] . " - " . date("d/m/Y H:i:s", strtotime($res[0]['sm_tanggal'])) . " WIB" : ''; ?></i></p>
			</div>
		</div> -->
		<div class="col-sm-4 col-sm-top">
			<label>Catatan Purchasing</label>
			<div class="form-control" style="height:auto">
				<?php echo $res[0]['purchasing_summary'] ?? ''; ?>
				<p style="margin:10px 0 0; font-size:12px;"><i>
						<?php echo $res ? $res[0]['purchasing_pic'] . " - " . date("d/m/Y H:i:s", strtotime($res[0]['purchasing_tanggal'])) . " WIB" : ''; ?>
					</i></p>
			</div>
		</div>

		<!-- <div class="col-sm-4 col-sm-top">
			<label>Lampiran Urgen Conditional</label>
			<div class="form-control" style="height:auto">
				<p style="margin:10px 0 0; font-size:12px;"><i>
						<?php echo $attach; ?>
					</i></p>
			</div>
		</div> -->

	</div>
<?php } ?>
<!-- <?php if ($res) { ?>
	<?php if ($res[0]['coo_result']) { ?>
		<div class="form-group row">
			<div class="col-sm-6">
				<label>Catatan COO</label>
				<div class="form-control" style="height:auto">
					<?php echo ($res[0]['coo_summary']); ?>
					<p style="margin:10px 0 0; font-size:12px;"><i><?php echo $res[0]['coo_pic'] . " - " . date("d/m/Y H:i:s", strtotime($res[0]['coo_tanggal'])) . " WIB"; ?></i></p>
				</div>
			</div>
		</div>
	<?php } ?>
	<div class="row">
		<?php if ($res[0]['revert_ceo']) { ?>
			<div class="col-sm-6">
				<div class="form-group">
					<label>Catatan Pengembalian COO/CEO</label>
					<div class="form-control" style="height:auto"><?php echo ($res[0]['revert_ceo_summary'] ? $res[0]['revert_ceo_summary'] : '&nbsp;'); ?></div>
				</div>
			</div>
		<?php } ?>
	</div>
	<?php if ($res[0]['revert_ceo']) echo '<hr style="border-top:4px double #ddd; margin:5px 0px 20px;" />'; ?>
<?php } ?> -->

<!-- <?php if (count($res) > 0) { ?>
	<?php if (!$fnr) { ?>
		<div class="form-group row">
			<div class="col-sm-6">
				<label>Dikembalikan ke Purchasing ?*</label>
				<div class="radio clearfix" style="margin:0px;">
					<label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="revert" id="revert1" class="validate[required]" value="1" /> Ya</label>
					<label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="revert" id="revert2" class="validate[required]" value="2" /> Tidak</label>
				</div>
			</div>
			<div class="col-sm-6 col-sm-top">
				<label>Catatan Pengembalian</label>
				<textarea name="summary_revert" id="summary_revert" class="form-control"></textarea>
			</div>
		</div>
	<?php } ?>
<?php } ?> -->

<!-- <div class="form-group row persetujuan-ceo <?php echo (!$fnr) ? 'hide' : ''; ?>">
	<div class="col-sm-6">
		<label>Catatan CEO</label>
		<?php if (!$fnr) { ?>
			<textarea name="summary" id="summary" class="form-control"></textarea>
		<?php } else { ?>
			<div class="form-control" style="height:auto">
				<?php echo ($res[0]['ceo_summary'] ? $res[0]['ceo_summary'] : $res[0]['ceo_summary']); ?>
				<?php
				$picnya = ($res[0]['ceo_pic'] ? $res[0]['ceo_pic'] : $res[0]['ceo_pic']);
				$tglnya = ($res[0]['ceo_tanggal'] ? $res[0]['ceo_tanggal'] : $res[0]['ceo_tanggal']);
				?>
				<p style="margin:10px 0 0; font-size:12px;"><i><?php echo $picnya . " - " . date("d/m/Y H:i:s", strtotime($tglnya)) . " WIB"; ?></i></p>
			</div>
		<?php } ?>
	</div>
</div> -->


<hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

<?php if (count($res) > 0) { ?>
	<div style="margin-bottom:0px;">
		<input type="hidden" name="prnya" id="prnya" value="cfo" />
		<input type="hidden" name="idr" value="<?php echo $idr; ?>" />
		<input type="hidden" name="idw" value="<?php echo $row[0]['id_wilayah']; ?>" />
		<input type="hidden" name="idg" value="<?php echo $row[0]['id_group']; ?>" />
		<a href="<?php echo BASE_URL_CLIENT . '/purchase-request.php'; ?>" class="btn btn-default jarak-kanan" style="min-width:90px;">Kembali</a>
		<?php if (!$fnr) { ?>
			<button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">Simpan</button>
		<?php } ?>
		<a href="<?php echo BASE_URL_CLIENT . '/purchase-request-detail-exp.php?' . paramEncrypt('idr=' . $idr); ?>" class="btn btn-success jarak-kanan" target="_blank" style="min-width:90px;">Export</a>
	</div>
<?php } else { ?>
	<div style="margin-bottom:0px;">
		<a href="<?php echo BASE_URL_CLIENT . '/purchase-request.php'; ?>" class="btn btn-default jarak-kanan" style="min-width:90px;">Kembali</a>
	</div>
<?php } ?>

<style type="text/css">
	.input-po {
		padding: 3px 5px;
		height: auto;
		font-size: 11px;
		font-family: arial;
	}

	/*.table > thead > tr > th, 
	.table > tbody > tr > th, 
	.table > tfoot > tr > th{
		background-color: #fff;
		border: 1px solid #ddd;
	}*/
</style>
<script>
	$(document).ready(function() {
		$(".hitung").number(true, 0, ".", ",");

		$("input[name='revert']").on("ifChecked", function() {
			var nilai = $(this).val();
			if (nilai == 1) {
				$(".persetujuan-ceo").addClass("hide");
			} else if (nilai == 2) {
				$(".persetujuan-ceo").removeClass("hide");
			}
		});

		$("form#gform").on("click", "#btnSbmt", function() {
			if (!$("input[name='revert']:checked").validationEngine('validate')) {
				$("#preview_modal").find("#preview_alert").text("Pengembalian data belum dipilih..");
				$("#preview_modal").modal();
				return false;
			} else {
				if (confirm("Apakah anda yakin?")) {
					if ($("#gform").find("input:checked").length > 0) {
						$("#loading_modal").modal({
							backdrop: "static"
						});
						$.ajax({
							type: 'POST',
							url: "./__cek_pr_customer_purchasing.php",
							dataType: "json",
							data: $("#gform").serializeArray(),
							cache: false,
							success: function(data) {
								if (data.error) {
									$("#preview_modal").find("#preview_alert").html(data.error);
									$("#preview_modal").modal();
									$("#loading_modal").modal("hide");
									return false;
								} else {
									$("form#gform").submit();
								}
							}
						});
						return false;
					} else {
						$("#preview_modal").find("#preview_alert").text("Data DR Belum dipilih..");
						$("#preview_modal").modal();
						return false;
					}
				} else return false;
			}
		});
	});
</script>
<div style="overflow-x: scroll" id="table-long">
	<div style="width:1700px; height:auto;">
		<div class="table-responsive-satu">
			<table class="table table-bordered" id="table-grid3">
				<thead>
					<tr>
						<th class="text-center" rowspan="2" width="50"><input type="checkbox" name="cekAll" id="cekAll" value="1" /></th>
						<th class="text-center" rowspan="2" width="50">No</th>
						<th class="text-center" rowspan="2" width="200">Customer/ Bidang Usaha</th>
						<th class="text-center" rowspan="2" width="230">Area/ Alamat Kirim/ Wilayah OA</th>
						<th class="text-center" rowspan="2" width="190">PO Customer</th>
						<th class="text-center" colspan="2">Quantity</th>
						<th class="text-center" rowspan="2" width="100">TOP</th>
						<th class="text-center" rowspan="2" width="120">Credit Limit</th>
						<th class="text-center" colspan="4">Harga (Rp/Liter)</th>
						<th class="text-center" rowspan="2" width="150">Keterangan Lain</th>
						<th class="text-center" rowspan="2" width="150">Status Pengiriman</th>
					</tr>
					<tr>
						<th class="text-center" width="80">Volume (Liter)</th>
						<th class="text-center" width="80">Edit (Liter)</th>
						<th class="text-center" width="90">Harga</th>
						<th class="text-center" width="200">Rincian Harga</th>
						<th class="text-center" width="80">Refund</th>
						<th class="text-center" width="80">Other Cost</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$sql = "select a.*, b.sm_result, b.sm_summary, b.sm_pic, b.sm_tanggal, c.tanggal_kirim, e.alamat_survey, e.id_wil_oa, f.nama_prov, g.nama_kab, 
							h.nama_customer, h.id_customer, h.kode_pelanggan, i.fullname, l.nama_area, d.harga_poc, m.jenis_produk, m.merk_dagang, 
							e.jenis_usaha, d.nomor_poc, d.lampiran_poc, d.lampiran_poc_ori, d.id_poc, n.wilayah_angkut, o.nilai_pbbkb, 
							k.refund_tawar, k.other_cost, k.perhitungan, k.detail_rincian, k.harga_dasar, k.gabung_oa, c.top_plan, c.kredit_limit,
							p.is_loaded, p.is_delivered, p.is_cancel, p.tanggal_loaded, p.jam_loaded, p.tanggal_cancel 
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
							join pro_master_wilayah_angkut n on e.id_wil_oa = n.id_master and e.prov_survey = n.id_prov and e.kab_survey = n.id_kab 
							join pro_master_pbbkb o on k.pbbkb_tawar = o.id_master 
							left join pro_po_ds_detail p on a.id_prd = p.id_prd 
                            where a.id_pr = '" . $idr . "' order by a.is_approved desc, c.tanggal_kirim, k.id_cabang, k.id_area, a.id_plan, a.id_prd";
					$res = $con->getResult($sql);
					$fnr = $res[0]['sm_result'];
					if (count($res) == 0) {
						echo '<tr><td colspan="13" style="text-align:center">Data tidak ditemukan </td></tr>';
					} else {
						$nom = 0;
						foreach ($res as $data) {

							$id_poc_sc[] = $data['id_poc'];

							$nom++;
							$idp 	= $data['id_prd'];
							$tempal = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
							$alamat	= $data['alamat_survey'] . " " . ucwords($tempal) . " " . $data['nama_prov'];
							$kirim	= date("d/m/Y", strtotime($data['tanggal_kirim']));
							$tot_ar = ($data['pr_ar_notyet'] + $data['pr_ar_satu'] + $data['pr_ar_dua']);

							$pbbkbT = ($data['nilai_pbbkb'] / 100) + 1.11;
							$oildus = $data['harga_poc'] / $pbbkbT * 0.003;
							$pbbkbN = $data['harga_poc'] / $pbbkbT * ($data['nilai_pbbkb'] / 100);
							$tmphrg = $data['refund_tawar'] + $oildus + $data['transport'] + $pbbkbN + (isset($data['other_cost']) ? $data['other_cost'] : 0);
							$nethrg = $data['harga_poc'] - $tmphrg;

							$pathPt = $public_base_directory . '/files/uploaded_user/lampiran/' . $data['lampiran_poc'];
							$lampPt = $data['lampiran_poc_ori'];
							if ($data['lampiran_poc'] && file_exists($pathPt)) {
								$linkPt = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=2&ktg=POC_" . $data['id_poc'] . "_&file=" . $lampPt);
								$attach = '<a href="' . $linkPt . '"><i class="fa fa-file-alt" title="' . $lampPt . '"></i> PO Customer</a>';
							} else {
								$attach = '';
							}

							$rincian = json_decode($data['detail_rincian'], true);
							$tabel_harga = '<table border="0" cellpadding="" cellspacing="0" width="200">';
							foreach ($rincian as $arr1) {
								$cetak = 1;
								$nilai = $arr1['nilai'];
								$biaya = ($arr1['biaya']) ? $arr1['biaya'] : '';
								$biaya = ($rsm['pembulatan']) ? number_format($arr1['biaya']) : number_format($arr1['biaya'], 2);
								$jenis = $arr1['rincian'];
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

					?>
							<tr>
								<td class="text-center">
									<?php
									if (!$fnr) {
										echo '<input type="checkbox" name="cek[' . $idp . ']" id="cek' . $nom . '" class="chkp" value="1" />';
										echo '<input type="hidden" name="vol[' . $idp . ']" id="vol' . $nom . '" value="' . $data['volume'] . '" />';
									} else {
										echo ($data['is_approved']) ? '<i class="fa fa-check"></i>' : '&nbsp;';
									}
									?>
								</td>
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
									<p style="margin-bottom:0px"><?php echo $attach; ?></p>
								</td>
								<td class="text-right"><?php echo number_format($data['volume']); ?></td>
								<td class="text-right"><?php
														if (!$fnr) echo '<input type="text" name="ket[' . $idp . ']" id="ket' . $nom . '" class="form-control input-po hitung" />';
														else echo ($data['vol_ket']) ? number_format($data['vol_ket']) : '&nbsp;';
														?></td>
								<td class="text-center"><?php echo $data['pr_top']; ?></td>
								<td class="text-right"><?php echo number_format($data['pr_kredit_limit']); ?></td>
								<td class="text-right"><?php echo number_format($data['harga_dasar']); ?></td>
								<td class="text-left"><?php echo $tabel_harga; ?></td>
								<td class="text-right"><?php echo number_format($data['refund_tawar']); ?></td>
								<td class="text-right"><?php echo number_format($data['other_cost']); ?></td>
								<td class="text-left">
									<p style="margin-bottom:0px"><b>NO DO Syop : </b></p>
									<p style="margin-bottom:5px"><?php echo ($data['no_do_syop'] ? $data['no_do_syop'] : 'N/A'); ?></p>
									<p style="margin-bottom:0px"><b>Loading Order : </b></p>
									<p style="margin-bottom:0px"><?php echo ($data['nomor_lo_pr'] ? $data['nomor_lo_pr'] : 'N/A'); ?></p>
								</td>
								<td class="text-center">

									<?php if ($data['is_loaded'] == 1 && $data['is_delivered'] == 1 && $data['is_cancel'] == 0) { ?>

										<p style="margin-bottom:0px;color:green"><b>Delivered</b></p>
									<?php } elseif ($data['is_loaded'] == 1 && $data['is_delivered'] == 0 && $data['is_cancel'] == 1) { ?>
										<p style="margin-bottom:0px;color:red"><b>Cancel</b></p>
										<p style="margin-bottom:0px"><?php echo 'Tgl Cancel ' . tgl_indo($data['tanggal_cancel']); ?> </p>
									<?php } elseif ($data['is_loaded'] == 0 && $data['is_delivered'] == 0 && $data['is_cancel'] == 1) { ?>
										<p style="margin-bottom:0px;color:red"><b>Cancel</b></p>
										<p style="margin-bottom:0px"><?php echo 'Tgl Cancel ' . tgl_indo($data['tanggal_cancel']); ?> </p>

									<?php } ?>
								</td>
							</tr>
					<?php }
					} ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<!-- <div class="form-group row">
	<div class="col-sm-6">
		<label>Catatan BM</label>
		<?php if (!$fnr) { ?>
			<input type="hidden" name="prnya" id="prnya" value="bm" />
			<textarea name="summary" id="summary" class="form-control"></textarea>
		<?php } else { ?>
			<div class="form-control" style="height:auto">
				<?php echo ($res[0]['sm_summary']); ?>
				<p style="margin:10px 0 0; font-size:12px;"><i><?php echo $res[0]['sm_pic'] . " - " . date("d/m/Y H:i:s", strtotime($res[0]['sm_tanggal'])) . " WIB"; ?></i></p>
			</div>
		<?php } ?>
	</div>
</div> -->

<?php if (count($res) > 0) { ?>
	<div class="row">
		<div class="col-sm-12">
			<div class="pad bg-gray">
				<input type="hidden" name="idr" value="<?php echo $idr; ?>" />
				<a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT . "/purchase-request.php"; ?>">Kembali</a>
				<?php if (!$fnr) { ?><button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Submit</button><?php } ?>
				<?php $linkCtk1 = ACTION_CLIENT . "/purchase-request-detail-cetak.php?" . paramEncrypt("idr=" . $idr); ?>
				<a class="btn btn-primary" target="_blank" href="<?php echo $linkCtk1; ?>">Cetak</a>
			</div>
		</div>
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
		$("form#gform").on("click", "#btnSbmt", function() {
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
		});
	});
</script>
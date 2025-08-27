<div style="overflow-y:scroll; overflow:scroll;" id="table-long">
	<div style="width:2725px; height:350px;">
		<div class="table-responsive-satu">
			<table class="table table-bordered table-grid3" id="table-grid3">
				<thead>
					<tr>
						<th class="text-center" rowspan="2" width="100">Split</th>
						<th class="text-center" rowspan="2" width="50">No</th>
						<th class="text-center" rowspan="2" width="200">Customer/ Bidang Usaha</th>
						<th class="text-center" rowspan="2" width="230">Area/ Alamat Kirim/ Wilayah OA</th>
						<th class="text-center" rowspan="2" width="200">PO Customer</th>
						<th class="text-center" rowspan="2" width="75">Volume Awal (Liter)</th>
						<th class="text-center" rowspan="2" width="75">Volume (Liter)</th>
						<th class="text-center" rowspan="2" width="75">Volume Potongan</th>
						<th class="text-center" rowspan="2" width="200">PO Supplier</th>
						<th class="text-center" rowspan="2" width="200">Depot</th>
						<th class="text-center" rowspan="2" width="150">Harga Beli</th>
						<th class="text-center" colspan="5" width="75">Harga (Rp/Liter)</th>
						<th class="text-center" rowspan="2" width="140">Nett Profit</th>
						<th class="text-center" rowspan="2" width="100">Price List (Harga Dasar)</th>
						<th class="text-center" rowspan="2" width="250">Catatan</th>
						<th class="text-center" rowspan="2" width="150">Keterangan Lain</th>
						<th class="text-center" rowspan="2" width="150">Status</th>
					</tr>
					<tr>
						<th style="display:none"></th>
						<th style="display:none"></th>
						<th style="display:none"></th>
						<th style="display:none"></th>
						<th style="display:none"></th>
						<th style="display:none"></th>
						<th style="display:none"></th>
						<th style="display:none"></th>
						<th style="display:none"></th>
						<th style="display:none"></th>
						<th class="text-center" width="90">Harga Jual (Gross)</th>
						<th class="text-center" width="200">Rincian Harga</th>
						<th class="text-center" width="80">Harga Dasar (Nett)</th>
						<th class="text-center" width="80">Refund</th>
						<th class="text-center" width="80">Other Cost</th>
						<th style="display:none"></th>
						<th style="display:none"></th>
						<th style="display:none"></th>
						<th style="display:none"></th>
						<th style="display:none"></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$sql = "
						select a.*, 
						b.sm_result, b.sm_summary, b.sm_pic, b.sm_tanggal, 
						b.purchasing_result, b.ceo_result, b.coo_result, b.purchasing_summary, b.purchasing_pic, b.purchasing_tanggal,
						b.is_ceo, b.disposisi_pr,
						c.tanggal_kirim, c.status_plan, c.catatan_reschedule, c.status_jadwal, 
						e.alamat_survey, e.id_wil_oa, 
						f.nama_prov, g.nama_kab, 
						n.nilai_pbbkb, 
						k.id_penawaran, k.masa_awal, k.masa_akhir, k.id_area, k.flag_approval, 
						k.refund_tawar, k.other_cost, k.perhitungan, k.detail_rincian, k.harga_dasar, k.gabung_oa, k.pembulatan,
						o1.harga_normal, o2.harga_normal as harga_normal_new, 
						h.nama_customer, h.id_customer, 
						i.fullname, l.nama_area, d.harga_poc, 
						m.jenis_produk, e.jenis_usaha, 
						d.nomor_poc, d.produk_poc, 
						p.nama_terminal, p.tanki_terminal, p.lokasi_terminal, 
						q.nama_vendor, r.wilayah_angkut, m.merk_dagang, m.id_master,
						d.lampiran_poc, d.lampiran_poc_ori, d.id_poc, h.kode_pelanggan, 
						b.revert_cfo, b.revert_cfo_summary, b.revert_ceo, b.revert_ceo_summary,
						b.submit_bm, b.pr_con, h.jenis_payment, h.top_payment, h.credit_limit, c.top_plan,
						s.is_loaded, s.is_delivered, s.is_cancel, s.tanggal_loaded, s.jam_loaded, tanggal_cancel,
						t.volume as volume_potong, t.nomor_po_supplier as nomor_potong,  t.pr_harga_beli as harga_potong,
						pt.nama_terminal AS terminal_potong,pt.tanki_terminal AS tanki_potong, pt.lokasi_terminal AS lokasi_potong
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
						left join pro_master_wilayah_angkut r on e.id_wil_oa = r.id_master 
						left join pro_po_ds_detail s on a.id_prd = s.id_prd 
						LEFT JOIN new_pro_inventory_potongan_stock t ON a.id_prd = t.id_prd
						LEFT JOIN pro_master_terminal pt ON pt.id_master = t.pr_terminal
							and e.prov_survey = r.id_prov 
							and e.kab_survey = r.id_kab 
						where 
							a.id_pr = '" . $idr . "' and 
							a.is_approved = 1
						order by a.is_approved desc, c.tanggal_kirim, k.id_cabang, k.id_area, a.id_plan, a.id_prd";

					$res = $con->getResult($sql);
					$fnr = $res[0]['purchasing_result'] ?? null;
					$fnr_ceo = $res[0]['ceo_result'] ?? null;
					$fnr_coo = $res[0]['coo_result'] ?? null;

					if (count($res) == 0) {
						echo '<tr><td colspan="19" style="text-align: center">Data tidak ditemukan </td></tr>';
					} else {
						$nom = 0;
						$total1 = 0;
						$total2 = 0;
						$total3 = 0;
						$total4 = 0;

						foreach ($res as $data) {
							$id_poc_sc[] = $data['id_poc'];
							$nom++;
							$idp 	= $data['id_prd'];
							$id_plan 	= $data['id_plan'];
							$idsp 	= $data['splitted_from_pr'];
							$linkCtk1 	= ACTION_CLIENT . "/delivery-order-detail-cetak.php?" . paramEncrypt("idp=" . $idp);
							$tempal = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
							$alamat	= $data['alamat_survey'] . " " . ucwords($tempal) . " " . $data['nama_prov'];

							$jns_payment = $data['jenis_payment'];
							$top_payment = $data['top_payment'];
							$arr_payment = array("CREDIT" => "NET " . $top_payment, "COD" => "COD", "CDB" => "CBD");
							$termPayment = $arr_payment[$jns_payment];
							$topCustomer = ($data['top_plan']) ? $data['top_plan'] : $termPayment;
							$dt1Customer = $topCustomer;
							$credit_limit = $data['credit_limit'];
							$id_pro = $data['id_master'];

							$data['harga_normal'] = ($data['harga_normal'] ? $data['harga_normal'] : $data['harga_normal_new']);
							$pbbkbT = ($data['nilai_pbbkb'] / 100) + 1.11;
							$oildus = $data['harga_poc'] / $pbbkbT * 0.003;
							$pbbkbN = $data['harga_poc'] / $pbbkbT * ($data['nilai_pbbkb'] / 100);
							$volume = $data['volume'];
							$vol_potongan_pr = $data['vol_potongan'];
							$volume_potong_split = $data['volume_potong'];
							$harga_potong = $data['harga_potong'];
							$volori = ($data['vol_ori_pr'] ? $data['vol_ori_pr'] : $data['volume']);
							$tmphrg = $data['refund_tawar'] + $data['other_cost'];
							$nethrg = $data['harga_poc'] - $tmphrg;
							$pr_harga_beli =  $data['pr_harga_beli'];
							$pr_harga_beli_potong =  $data['harga_potong'];
							$netgnl = ($nethrg - $data['harga_normal']) * $volume;
							//$netprt = ($nethrg - $data['pr_harga_beli']) * $volume;
							$form_split_pr = $data['splitted_from_pr'];

							$pathPt = $public_base_directory . '/files/uploaded_user/lampiran/' . $data['lampiran_poc'];
							$lampPt = $data['lampiran_poc_ori'];

							$link_cetak	 = ACTION_CLIENT . '/penawaran-cetak.php?' . paramEncrypt('idr=' . $data['id_customer'] . '&idk=' . $data['id_penawaran'] . '&bhs=ind');

							if ($data['pembulatan'] == 0) {
								$harga_dasarnya = number_format($data['harga_dasar'], 2);
							} elseif ($data['pembulatan'] == 1) {
								$harga_dasarnya = number_format($data['harga_dasar']);
							} elseif ($data['pembulatan'] == 2) {
								$harga_dasarnya = number_format($data['harga_dasar'], 4);
							}

							if ($data['lampiran_poc'] && file_exists($pathPt) && $data['flag_approval'] == 1) {
								$linkPt = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=2&ktg=POC_" . $data['id_poc'] . "_&file=" . $lampPt);
								$attach = '
								<a href="' . $linkPt . '" target ="_blank"><i class="fa fa-file-alt" title="' . $lampPt . '"></i> PO Customer</a><br />
								<a href="' . $link_cetak . '" target ="_blank"><i class="fa fa-file-alt" title="' . $lampPt . '"></i> Penawaran' . '</a><br />
								<a href="#" class="history_approve" attr_idc="' . $data['id_customer'] . '" attr_idk="' . $data['id_penawaran'] . '">
									<i class="fa fa-cog" title="History Approval"></i> History Approve
								</a>';
							} else {
								$attach = '
								<a href="' . $link_cetak . '" target ="_blank"><i class="fa fa-file-alt" title="' . $lampPt . '"></i> Penawaran' . '</a><br />
								<a href="#" class="history_approve" attr_idc="' . $data['id_customer'] . '" attr_idk="' . $data['id_penawaran'] . '">
									<i class="fa fa-cog" title="History Approval"></i> History Approve
								</a>';
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

							// $tmphrg = $data['refund_tawar'] + $oildus + $data['transport'] + $pbbkbN + $data['other_cost'];
							// $nethrg = $data['harga_poc'] - $tmphrg;
							// $netprt = ($harga_dasar_new - $tmphrg - $data['pr_harga_beli']) * $volume;
							$total1 = $total1  + $volume;
							$total2 = $total2 + $data['vol_ket'];
							$total4 = $total4 + $netgnl;

							if ($vol_potongan_pr == 0) {
								$total_harga_dasar_nett = $harga_dasar_new - $tmphrg;
								$netprt = ($total_harga_dasar_nett - $pr_harga_beli) * $volume;
							} else {
								$total_harga_dasar_nett = $harga_dasar_new - $tmphrg;
								$netprt = ($total_harga_dasar_nett - $pr_harga_beli) * $vol_potongan_pr;
							}
							// Memeriksa jika harga_potong lebih besar dari 0 sebelum menghitung $netprt1
							if ($harga_potong > 0) {
								$total_harga_dasar_nett1 = $harga_dasar_new - $tmphrg;
								$netprt1 = ($total_harga_dasar_nett1 - $pr_harga_beli_potong) * $volume_potong_split;
							} else {
								$netprt1 = 0;
							}

							// Menambahkan $netprt1 ke total hanya jika harga_potong lebih besar dari 0
							$total3 = $total3 + $netprt + $netprt1;

					?>
							<tr>
								<td class="text-center">
									<?php
									$tombolAddnya = "";
									if ($data['splitted_from_pr']) {
										$tombolAddnya .= '
										<button type="button" class="btn btn-action btn-warning resetRow" data-cnt="1" value="' . paramEncrypt($idsp . '|#|' . $idp . '|#|' . $idr) . '">
											<i class="fa fa-undo"></i>
										</button>';
									}
									$tombolAddnya .= '
									<button type="button" class="btn btn-action btn-primary addRow" data-idp="' . $nom . '" value="' . $idp . '">
									<i class="fa fa-plus"></i>
									</button>';

									echo '<div class="tombol-addnya' . ($fnr ? ' hide' : '') . '">' . $tombolAddnya . '</div>';

									echo '<a target="_blank" href="' . $linkCtk1 . '" class="btn btn-primary btn-sm ' . ($fnr == '1' ? '' : 'hide') . '">Cetak DO</a>'

									?>
								</td>
								<td class="text-center"><span class="noFormula"><?php echo $nom; ?></span></td>
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
									<input type="hidden" name="tgl_kirim" value=<?php echo $data['tanggal_kirim'] ?>>

									<p style="margin-bottom:0px"><?php echo $attach; ?></p>
								</td>
								<td class="text-right">
									<?php if (!$fnr) {
										echo number_format($volume);
									} else {
										echo number_format($data['vol_ori_pr']);
									}
									?>
								</td>
								<td class="text-right volume_oil">

									<?php if (!$fnr) {  ?>
										<input class="text-right volume hitung" type="text" name="<?php echo 'volume[' . $idp . ']'; ?>" id="<?php echo 'volume' . $nom; ?>" value="<?php echo number_format($data['volume']); ?>" style="width:100%;" />
									<?php } else {
										echo number_format($data['volume']);
									} ?>
								</td>
								<td class="text-right">
									<?php if (!$fnr) {
										echo number_format($data['vol_potongan']);
									} else {
										echo number_format($data['vol_potongan']);
										echo '<br>';
										echo ($data['volume_potong']) && $data['volume_potong'] != '' ? number_format($data['volume_potong']) : '';
									}
									?>
								</td>
								<td>
									<?php
									$divEdit = '
									<input type="hidden" name="cek[' . $idp . ']" id="cek' . $nom . '" value="1" />
									<input class="dp3" type="hidden" name="dp3[' . $idp . ']" id="dp3' . $nom . '" value="' . $data['masa_awal'] . '" />
									<input class="dp4" type="hidden" name="dp4[' . $idp . ']" id="dp4' . $nom . '" value="' . $data['masa_akhir'] . '" />
									<input class="dp5" type="hidden" name="dp5[' . $idp . ']" id="dp5' . $nom . '" value="' . $data['id_area'] . '" />
									<input class="dp6" type="hidden" name="dp6[' . $idp . ']" id="dp6' . $nom . '" value="' . $data['produk_poc'] . '" />
									<input class="dp7" type="hidden" name="dp7[' . $idp . ']" id="dp7' . $nom . '" value="' . $data['harga_normal'] . '" />
									<input class="dp10" type="hidden" name="dp10[' . $idp . ']" id="dp10' . $nom . '" value="' . $volori . '" />
									<input class="dp11" type="hidden" name="dp11[' . $idp . ']" id="dp11' . $nom . '" value="' . $nethrg . '" />
									<input class="dp12" type="hidden" name="dp12[' . $idp . ']" id="dp12' . $nom . '" value="' . $harga_dasar_new . '" />
									<input class="dp13" type="hidden" name="dp13[' . $idp . ']" id="dp13' . $nom . '" value="' . $harga_dasar_new_potong . '" />
									<input class="form_split_pr" type="hidden" name="form_split_pr[' . $idp . ']" id="form_split_pr' . $nom . '" value="' . $form_split_pr . '" />';

									$divText = '
									<input type="hidden" name="np1[' . $idp . ']" id="np1' . $nom . '" value="' . $data['nomor_po_supplier'] . '" />
									<input type="hidden" name="dv1[' . $idp . ']" id="dv1' . $nom . '" value="' . $data['pr_vendor'] . '" />
									<input type="hidden" name="ps1[' . $idp . ']" id="ps1' . $nom . '" value="' . $data['id_po_supplier'] . '" />
									<input type="hidden" name="pr1[' . $idp . ']" id="pr1' . $nom . '" value="' . $data['id_po_receive'] . '" />';
									if (!$fnr) {

										echo '
										<input class="form-control np1" type="text" id="np1' . $nom . '" name="np1[' . $idp . ']" value="" placeholder="Klik untuk pilih PO" readonly onclick="myFunction(' . $nom . ')" style="cursor:pointer;" />';
										echo $divEdit;
									} else {
										echo '<center><div class="divText">';
										$nop = ($data['nomor_po_supplier']) ? $data['nomor_po_supplier'] : '';
										$nop2 = ($data['nomor_potong']) ? $data['nomor_potong'] : '';
										echo $nop;
										echo '<br>';
										echo $nop2;
										echo $divText;
										echo '</div></center>';
										echo '<div class="divEdit" style="display: none;">';
										echo '<div class="input-group">
										<input class="form-control np1" type="text" id="np1' . $nom . '" name="np1[' . $idp . ']" value="' . $data['nomor_po_supplier'] . '" placeholder="Klik untuk pilih PO" readonly onclick="myFunction(' . $nom . ')" style="cursor:pointer;" />';
										echo $divEdit;
									} ?>
								</td>
								<td>
									<?php
									if (!$fnr) {
										echo '<input class="form-control dp8" type="hidden" id="dp8' . $nom . '" name="dp8[' . $idp . ']" value="" readonly />';
										echo '<h5 id="tx1' . $nom . '" class="tx1">Silahkan pilih PO Supplier</h5>';
									} else {
										echo '<div class="divText">';
										$tmn1 = ($data['nama_terminal']) ? $data['nama_terminal'] : '';
										$tmn2 = ($data['tanki_terminal']) ? ' - ' . $data['tanki_terminal'] : '';
										$tmn3 = ($data['lokasi_terminal']) ? ', ' . $data['lokasi_terminal'] : '';
										$tmn4 = ($data['terminal_potong']) ? $data['terminal_potong'] : '';
										$tmn5 = ($data['tanki_potong']) ? ' - ' . $data['tanki_potong'] : '';
										$tmn6 = ($data['lokasi_potong']) ? ', ' . $data['lokasi_potong'] : '';
										echo '<input type="hidden" name="dp8[' . $idp . ']" id="dp8' . $nom . '" value="' . $data['pr_terminal'] . '" />';
										echo $tmn1 . $tmn2 . $tmn3;
										echo '<br>';

										echo $tmn4 . $tmn5 . $tmn6;
										echo '</div>';
										echo '<div class="divEdit" style="display: none;">';
										echo '<input class="form-control dp8" type="hidden" id="dp8' . $nom . '" name="dp8[' . $idp . ']" value="" readonly />';
										echo '</div>';
									}
									// echo '<p style="margin:5px 0 0;"><a style="cursor:pointer" class="detInven" data-idnya="' . $nom . '">Detil Inventory</a></p>';
									?>
									<span id="nt1<?= $nom ?>" class="nt1" style="display: none;"></span><br>
									<span id="sc1<?= $nom ?>" class="sc1" style="display: none;"></span>
								</td>
								<td style="display: none;">
									<?php
									echo '<input class="ps1" type="text" id="ps1' . $nom . '" name="ps1[' . $idp . ']" value="" />';
									?>
								</td>
								<td style="display: none;">
									<?php
									echo '<input class="pr1" type="text" id="pr1' . $nom . '" name="pr1[' . $idp . ']" value="" />';
									echo '<input type="text" name="top_plan[' . $idp . ']" id="top_plan' . $nom . '" class="form-control input-po" value="' . $dt1Customer  . '" />';
									echo '<input type="text" name="credit_limit[' . $idp . ']" id="top_plan' . $nom . '" class="form-control input-po" value="' . $credit_limit  . '" />';
									echo '<input type="text" name="id_pro[' . $idp . ']" id="id_pro' . $nom . '" class="form-control input-po" value="' . $id_pro  . '" />';

									?>
								</td>
								<td style="display: none;">
									<?php
									echo '<input class="dv1" type="text" id="dv1' . $nom . '" name="dv1[' . $idp . ']" value="" />';
									?>
								</td>
								<td style="display: none;">
									<?php
									echo '<input class="si1" type="text" id="si1' . $nom . '" name="si1[' . $idp . ']" value="" />';
									?>
								</td>
								<td class="text-left">
									<?php
									if (!$fnr) {
										echo '<input type="text" name="dp2[' . $idp . ']" id="dp2' . $nom . '" class="form-control input-po hitung dp2" value="0" style="width:100%;" readonly/>';
										echo '</div>';
									} else {
										echo '<div class="divText">';
										echo ($data['pr_harga_beli']) ? '<p style="margin-bottom:0px;" class="text-right">' . number_format($data['pr_harga_beli']) . '</p>' : '&nbsp;';

										echo ($data['harga_potong']) ? '<p style="margin-bottom:0px;" class="text-right">' . number_format($data['harga_potong']) . '</p>' : '&nbsp;';
										echo '</div>';
										echo '<div class="divEdit" style="display: none;">';
										echo '<input type="text" name="dp2[' . $idp . ']" id="dp2' . $nom . '" class="form-control input-po hitung dp2" value="' . $data['pr_harga_beli'] . '" style="width:100%;" readonly/>';
										echo '</div>';
									}
									?></td>
								<td class="text-right"><?php echo $harga_dasarnya; ?></td>
								<td class="text-left"><?php echo $tabel_harga; ?></td>
								<td class="text-right"><?php echo number_format($harga_dasar_new); ?></td>
								<td class="text-right"><?php echo number_format($data['refund_tawar']); ?></td>
								<td class="text-right"><?php echo number_format($data['other_cost']); ?>
									<input type="hidden" name="id_plan[]" value="<?php echo $id_plan; ?>" />
									<input type="hidden" name="id_prd[]" value="<?php echo $idp; ?>" />
								</td>
								<td class="text-right">
									<?php
									if (!$fnr) {
										echo '<input type="text" name="dp9[' . $idp . ']" id="dp9' . $nom . '" class="form-control input-po hitung dp9" readonly style="width:100%;" />';
									} else {
										echo number_format($netprt);
										echo '<br>';

										if (isset($data['harga_potong']) && $data['harga_potong'] > 0) {
											echo isset($netprt1) && $netprt1 !== '' ? number_format($netprt1) : '';
										}
									}
									?></td>
								<td class="text-right"><?php echo number_format($data['harga_normal']); ?></td>
								<td class="text-left"><?php echo $data['status_plan'] == 2 ? $data['catatan_reschedule'] : $data['status_jadwal']; ?></td>
								<td class="text-left">
									<p style="margin-bottom:0px"><b>NO DO SYOP : </b></p>
									<p style="margin-bottom:5px"><?php echo ($data['no_do_syop'] ? $data['no_do_syop'] : 'N/A'); ?></p>

									<p style="margin-bottom:0px"><b>Loading Order : </b></p>
									<p style="margin-bottom:0px"><?php echo ($data['nomor_lo_pr'] ? $data['nomor_lo_pr'] : 'N/A'); ?></p>
								</td>
								<td class="text-left">
									<?php if ($data['is_loaded'] == 0 && $data['is_delivered'] == 0 && $data['is_cancel'] == 0) { ?>
										<p style="margin-bottom:0px"><b>Belum Loading</b></p>
									<?php } elseif ($data['is_loaded'] == 0 && $data['is_delivered'] == 0 && $data['is_cancel'] == 1) { ?>
										<p style="margin-bottom:0px"><b>Cancel</b></p>
										<p style="margin-bottom:0px"><?php echo 'Tgl Cancel ' . tgl_indo($data['tanggal_cancel']); ?> </p>
									<?php } elseif ($data['is_loaded'] == 1 && $data['is_delivered'] == 0 && $data['is_cancel'] == 0) { ?>
										<p style="margin-bottom:0px"><b>Loading</b></p>
										<p style="margin-bottom:0px"><?php echo 'Tgl Loading ' . tgl_indo($data['tanggal_loaded']); ?> </p>
										<p style="margin-bottom:0px"><?php echo 'Jam Loading ' . ($data['jam_loaded']); ?> </p>
									<?php } elseif ($data['is_loaded'] == 1 && $data['is_delivered'] == 1 && $data['is_cancel'] == 0) { ?>
										<p style="margin-bottom:0px"><?php echo 'Tgl Loading ' . tgl_indo($data['tanggal_loaded']); ?> </p>
										<p style="margin-bottom:0px"><?php echo 'Jam Loading ' . ($data['jam_loaded']); ?> </p>
										<p style="margin-bottom:0px"><b>Delivered</b></p>
									<?php } elseif ($data['is_loaded'] == 1 && $data['is_delivered'] == 0 && $data['is_cancel'] == 1) { ?>
										<p style="margin-bottom:0px"><b>Cancel</b></p>
										<p style="margin-bottom:0px"><?php echo 'Tgl Cancel ' . tgl_indo($data['tanggal_cancel']); ?> </p>

									<?php } ?>


								</td>
							</tr>
					<?php }
					} ?>
				</tbody>
				<?php if ($fnr) { ?>
					<tfoot>
						<tr>
							<th colspan="7" class="text-center"><b>TOTAL</b></th>
							<th class="text-right"><?php echo number_format($total1); ?></th>
							<th colspan="8" class="text-center">&nbsp;</th>
							<th class="text-right"><?php echo number_format($total3); ?></th>
							<th class="text-right">&nbsp;</th>
							<th class="text-right">&nbsp;</th>
							<th class="text-right">&nbsp;</th>
							<th class="text-right">&nbsp;</th>
						</tr>
					</tfoot>
				<?php } ?>
			</table>
		</div>
	</div>
</div>

<div class="modal fade" id="modalPO" role="dialog" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 90%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">List PO Supplier</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-6">
						<input type="text" class="form-control input-sm" id="searchInput" onkeyup="searchTerminal()" placeholder="Keywords">
					</div>
				</div>
				<div class="box-body table-responsive">
					<table class="table table-bordered table-hover" id="data-po">
						<thead>
							<tr>
								<th class="text-center">No</th>
								<th class="text-center">Nama Terminal</th>
								<th class="text-center">Stock Inventory</th>
								<th class="text-center">Nomor PO</th>
								<th class="text-center">Vol PO Supplier</th>
								<th class="text-center">Vol Terima Barang</th>
								<th class="text-center">Harga Tebus</th>
								<th class="text-center">Nama Vendor</th>
								<th class="text-center">Aksi</th>
							</tr>
						</thead>
						<tbody id="bodyResult">
							<!-- Data tabel akan dimasukkan di sini -->
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<?php if ($res && $res[0]['revert_cfo']) { ?>
		<div class="col-sm-6">
			<div class="form-group">
				<label>Catatan Pengembalian CFO</label>
				<div class="form-control" style="height:auto"><?php echo ($res[0]['revert_cfo_summary']); ?></div>
			</div>
		</div>
	<?php }
	if ($res && $res[0]['revert_ceo']) { ?>
		<div class="col-sm-6">
			<div class="form-group">
				<label>Catatan Pengembalian COO/CEO</label>
				<div class="form-control" style="height:auto"><?php echo ($res[0]['revert_ceo_summary'] ? $res[0]['revert_ceo_summary'] : '&nbsp;'); ?></div>
			</div>
		</div>
	<?php } ?>
</div>
<?php
if ($res && ($res[0]['revert_cfo'] || $res[0]['revert_ceo'])) {
	echo '<hr style="border-top:4px double #ddd; margin:5px 0px 20px;" />';
	echo '<input type="hidden" name="dis_lo" value="1" />';
}
?>


<?php /*
<div class="form-group row">
	<div class="col-sm-6">
		<label>Catatan BM</label>
		<div class="form-control" style="height:auto">
			<?php echo ($res[0]['sm_summary'] ?? ''); ?>
			<p style="margin:10px 0 0; font-size:12px;"><i><?php if ($res) {
																echo $res[0]['sm_pic'] . " - " . date("d/m/Y H:i:s", strtotime($res[0]['sm_tanggal'])) . " WIB";
															} ?></i></p>
		</div>
	</div>
</div>
*/ ?>

<div class="form-group row">
	<div class="col-sm-6">
		<label>Catatan Purchasing</label>
		<?php if (!$fnr) { ?>
			<textarea name="summary" id="summary" class="form-control"><?php if ($res) {
																			echo str_replace("<br />", PHP_EOL, $res[0]['purchasing_summary']);
																		} ?></textarea>
		<?php } else { ?>
			<div class="form-control" style="height:auto">
				<?php echo ($res[0]['purchasing_summary'] ?? ''); ?>
				<p style="margin:10px 0 0; font-size:12px;"><i>
						<?php if ($res) {
							echo $res[0]['purchasing_pic'] . " - " . date("d/m/Y H:i:s", strtotime($res[0]['purchasing_tanggal'])) . " WIB";
						} ?>
					</i></p>
			</div>
		<?php } ?>
	</div>
</div>
<!-- <?php if (!$fnr) { ?>
	<div class="form-group row">
		<div class="col-sm-6">
			<label>
				<input type="checkbox" id="urgent" name="urgent" />
				Urgent Conditional
			</label>
		</div>
	</div>


	<div class="form-group row">
		<div class="col-sm-6">
			<input type="file" style="display: none;" name="attachment_condition" id="attachment_condition" class="urgent_condition" <?php echo $attr01; ?> /></td>
			<p style="font-size:12px; display: none;" class="help-block urgent_condition">* Max size 2Mb | .jpg, .png, .pdf</p>
		</div>
	</div>
	<div class="form-group row">
		<div class="col-sm-6">
			<div>
				<button type="submit" class="btn btn-primary jarak-kanan urgent_condition" name="btnSbmt" id="btnSbmt" style="min-width:90px; display: none;">Simpan</button>
			</div>
		</div>
	</div>

<?php } ?> -->

<?php if (count($res) > 0) { ?>
	<hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

	<div style="margin-bottom:0px;">
		<input type="hidden" name="idr" id="idr" value="<?php echo $idr; ?>" />
		<input type="hidden" name="idw" id="idw" value="<?php echo $row[0]['id_wilayah']; ?>" />
		<input type="hidden" name="idg" value="<?php echo $row[0]['id_group']; ?>" />
		<input type="hidden" name="prnya" value="purchasing" />
		<input type="hidden" name="backadmin" value="0" />
		<input type="hidden" name="is_ceo" value="<?php echo $res[0]['is_ceo']; ?>" />
		<a href="<?php echo BASE_URL_CLIENT . '/purchase-request.php'; ?>" class="btn btn-default jarak-kanan" style="min-width:90px;">Kembali</a>
		<?php if ($fnr && $res[0]['sm_result'] == 1) { ?>
			<?php if ($row[0]['disposisi_pr'] != 6) { ?>
				<button type="button" class="btn btn-warning jarak-kanan" name="btnEdit" id="btnEdit" style="min-width:90px;">Edit</button>
			<?php } ?>
			<button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px; display:none;">Simpan</button>
			<button type="button" class="btn btn-danger jarak-kanan" name="btnCancel" id="btnCancel" style="min-width:90px; display:none;">Batal</button>
			<a href="<?php echo BASE_URL_CLIENT . '/purchase-request-detail-exp.php?' . paramEncrypt('idr=' . $idr); ?>" class="btn btn-success jarak-kanan" target="_blank" style="min-width:90px;">Export</a>
		<?php } ?>
		<!-- <?php if (!$fnr && $res[0]['sm_result'] == 1) { ?>
			<button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">Simpan</button>
		<?php } ?> -->

		<?php
		if (!$fnr && $res[0]['sm_result'] == 1) {
			// Ambil waktu sekarang
			$now = time();

			// Ambil waktu submit_bm
			$submitBmTime = strtotime($row[0]['submit_bm']);

			// Tentukan waktu batas nonaktif (16:15 hari ini setelah submit_bm)
			$disableTimeToday = strtotime(date('Y-m-d', $submitBmTime) . ' 16:31:00');

			// Tentukan waktu batas aktif (08:00 hari berikutnya setelah submit_bm)
			$enableTimeNextDay = strtotime(date('Y-m-d', strtotime('+1 day', $submitBmTime)) . ' 08:00:00');

			// Jika waktu sekarang lebih dari atau sama dengan batas waktu nonaktif (16:15) dan waktu sekarang kurang dari batas waktu aktif (08:00 hari berikutnya)
			if ($now >= $disableTimeToday && $now < $enableTimeNextDay) {
				echo '<button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;" disabled>Simpan</button>';
			} else {
				echo '<button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">Simpan</button>';
				echo '<button type="button" class="btn btn-success jarak-kanan" id="revisiDRPR" style="min-width:90px;">Kembalikan Ke DP</button>';
			}
		}
		?>
		<?php if ($res[0]['disposisi_pr'] == 6) { ?>
			<button type="submit" class="btn btn-success jarak-kanan" name="revisiDR" id="revisiDR" value="1" style="min-width:90px;">Revisi DR</button>
		<?php } ?>
	</div>
<?php } ?>


<div class="modal fade" id="user_modal" role="dialog" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog" style="width:1000px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Inventory By Depot </h4>
			</div>
			<div class="modal-body"></div>
		</div>
	</div>
</div>


<style type="text/css">
	.input-po {
		padding: 3px 5px;
		height: auto;
		font-size: 11px;
		font-family: arial;
	}

	.select2-search--dropdown .select2-search__field {
		font-family: arial;
		font-size: 11px;
		padding: 4px 3px;
	}

	.select2-results__option {
		font-family: arial;
		font-size: 11px;
	}

	#table-grid3 {
		border-collapse: separate;
	}

	/* .table-grid3>thead>tr>th:nth-child(1) {
		position: sticky;
		left: 0px;
		z-index: 2;
	}

	.table-grid3>thead>tr>th:nth-child(2) {
		position: sticky;
		left: 100px;
		z-index: 2;
	}

	.table-grid3>thead>tr>th:nth-child(3) {
		position: sticky;
		left: 150px;
		z-index: 2;
	}

	.table-grid3>tbody>tr>td:nth-child(1) {
		background-color: #f4f4f4;
		position: sticky;
		left: 0px;
		z-index: 2;
	}

	.table-grid3>tbody>tr>td:nth-child(2) {
		background-color: #f4f4f4;
		position: sticky;
		left: 100px;
		z-index: 2;
	}

	.table-grid3>tbody>tr>td:nth-child(3) {
		background-color: #f4f4f4;
		position: sticky;
		left: 150px;
		z-index: 2;
	} */
</style>
<script>
	$('#revisiDRPR').click(function() {
		// Konfirmasi sebelum melanjutkan

		// Mengumpulkan semua id_plan dan id_prd dari input tersembunyi
		var id_planValues = [];
		var id_prdValues = [];

		// Mendapatkan semua elemen input tersembunyi dalam table
		$('input[name="id_plan[]"]').each(function() {
			id_planValues.push($(this).val()); // Mengambil value dari id_plan
		});

		$('input[name="id_prd[]"]').each(function() {
			id_prdValues.push($(this).val()); // Mengambil value dari id_prd
		});

		var idrValue = $('#idr').val();
		var idwValue = $('#idw').val();
		// Pastikan ada id_plan dan id_prd yang terisi
		if (id_planValues.length === 0 || id_prdValues.length === 0) {
			Swal.fire({
				icon: 'warning',
				title: 'Peringatan',
				text: 'Tidak ada data yang ditemukan untuk diproses.'
			});
			return; // Menghentikan eksekusi jika tidak ada data
		}

		Swal.fire({
			title: 'Konfirmasi',
			text: 'Apakah Anda yakin ingin mengembalikan ke delivery plan logistik?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Ya, lanjutkan!',
			cancelButtonText: 'Batal'
		}).then((result) => {
			if (result.isConfirmed) {

				// Mengumpulkan data yang akan dikirim
				var dataToSend = {
					revisiDRPR: 1,
					id_plan: id_planValues, // Menggunakan array id_plan
					id_prd: id_prdValues, // Menggunakan array id_prd
					idr: idrValue,
					idw: idwValue // Jika diperlukan
				};

				//console.log("Data yang akan dikirim: ", dataToSend);

				// Kirim AJAX request
				$.ajax({
					type: 'POST',
					url: "./action/cek_status_dr_pr.php",
					data: dataToSend,
					success: function(response) {
						// Cek respons dari server
						if (response.status === 'success') {
							Swal.fire({
								icon: 'success',
								title: 'Berhasil',
								text: 'Revisi Pengembalian DR berhasil!'
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
						console.log(response); // Melihat respons di konsol
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


	function searchTerminal() {
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("searchInput");
		filter = input.value.toUpperCase();
		table = document.getElementById("data-po");
		tr = table.getElementsByTagName("tr");

		for (i = 0; i < tr.length; i++) {
			tdNamaTerminal = tr[i].getElementsByTagName("td")[1];
			tdNomorPO = tr[i].getElementsByTagName("td")[3];
			if (tdNamaTerminal || tdNomorPO) {
				txtValueNamaTerminal = tdNamaTerminal.textContent || tdNamaTerminal.innerText;
				txtValueNomorPO = tdNomorPO.textContent || tdNomorPO.innerText;
				if (txtValueNamaTerminal.toUpperCase().indexOf(filter) > -1 || txtValueNomorPO.toUpperCase().indexOf(filter) > -1) {
					tr[i].style.display = "";
				} else {
					tr[i].style.display = "none";
				}
			}
		}
	}

	function myFunction(nom) {
		var idwValue = $('#idw').val();
		$.ajax({
			type: "POST",
			url: `<?= BASE_URL . "/web/__get_po_by_terminal.php" ?>`,
			data: {
				idw: idwValue
			},
			dataType: "json",
			success: function(result) {
				$('#modalPO').modal({
					show: true
				})
				// console.log(result)
				var html = "";
				for (var i = 0; i < result.length; i++) {

					var no = i + 1;
					html += "<tr>";
					html += "<td>" + no + "</td>";
					html += "<td>" + result[i]['nama_terminal'] + " " + result[i]['tanki_terminal'] + "</td>";
					html += "<td align='center' nowrap>" + new Intl.NumberFormat().format(result[i]['sisa_inven']) + "</td>";
					html += "<td align='center' nowrap>" + result[i]['nomor_po_supplier'] + "</td>";
					html += "<td align='center' nowrap>" + new Intl.NumberFormat().format(result[i]['vol_po_supplier']) + "</td>";
					html += "<td align='center' nowrap>" + new Intl.NumberFormat().format(result[i]['vol_terima_barang']) + "</td>";
					html += "<td align='center' nowrap>Rp. " + new Intl.NumberFormat().format(result[i]['harga_tebus']) + "</td>";
					html += "<td align='center' nowrap>" + result[i]['nama_vendor'] + "</td>";
					html += "<td align='center' nowrap><button type='button' class='btn btn-success btn-md btn-pilih' data-detail='" + result[i]['nomor_po_supplier'] + "|#|" + result[i]['id_po_supplier'] + "|#|" + result[i]['id_po_receive'] + "|#|" + result[i]['harga_tebus'] + "|#|" + result[i]['id_vendor'] + "|#|" + result[i]['sisa_inven'] + "|#|" + result[i]['id_terminal'] + "|#|" + result[i]['nama_terminal'] + "|#|" + result[i]['tanki_terminal'] + "|#|" + result[i]['lokasi_terminal'] + "' data-nom='" + nom + "'>PILIH</button></td>";
					html += "</tr>";
				}
				$('#bodyResult').html(html);
			}
		});
	}

	// function myFunction(nom) {
	// 	var idwValue = $('#idw').val();

	// 	$.ajax({
	// 		type: "POST",
	// 		url: <?= BASE_URL . "/web/__get_po_by_terminal.php" ?>,
	// 		data: {
	// 			idw: idwValue
	// 		},
	// 		dataType: "json",
	// 		success: function(result) {
	// 			console.log(result)
	// 			$('#modalPO').modal({
	// 				show: true
	// 			})
	// 			// console.log(result)
	// 			var html = "";
	// 			for (var i = 0; i < result.length; i++) {

	// 				var no = i + 1;
	// 				html += "<tr>";
	// 				html += "<td>" + no + "</td>";
	// 				html += "<td>" + result[i]['nama_terminal'] + " " + result[i]['tanki_terminal'] + "</td>";
	// 				html += "<td align='center' nowrap>" + new Intl.NumberFormat().format(result[i]['sisa_inven']) + "</td>";
	// 				html += "<td align='center' nowrap>" + result[i]['nomor_po_supplier'] + "</td>";
	// 				html += "<td align='center' nowrap>" + new Intl.NumberFormat().format(result[i]['vol_po_supplier']) + "</td>";
	// 				html += "<td align='center' nowrap>" + new Intl.NumberFormat().format(result[i]['vol_terima_barang']) + "</td>";
	// 				html += "<td align='center' nowrap>Rp. " + new Intl.NumberFormat().format(result[i]['harga_tebus']) + "</td>";
	// 				html += "<td align='center' nowrap>" + result[i]['nama_vendor'] + "</td>";
	// 				html += "<td align='center' nowrap><button type='button' class='btn btn-success btn-md btn-pilih' data-detail='" + result[i]['nomor_po_supplier'] + "|#|" + result[i]['id_po_supplier'] + "|#|" + result[i]['id_po_receive'] + "|#|" + result[i]['harga_tebus'] + "|#|" + result[i]['id_vendor'] + "|#|" + result[i]['sisa_inven'] + "|#|" + result[i]['id_terminal'] + "|#|" + result[i]['nama_terminal'] + "|#|" + result[i]['tanki_terminal'] + "|#|" + result[i]['lokasi_terminal'] + "' data-nom='" + nom + "'>PILIH</button></td>";
	// 				html += "</tr>";
	// 			}
	// 			$('#bodyResult').html(html);
	// 		}
	// 	});
	// }

	$(document).ready(function() {

		$("#data-po").on("click", ".btn-pilih", function() {
			let index = $(this).data('detail');
			let nom = $(this).data('nom');
			let param = index.toString().split('|#|');
			let sisa_inven = parseInt(decodeURIComponent(param[5]));

			let nama_terminal = decodeURIComponent(param[7])
			let tanki_terminal = decodeURIComponent(param[8])
			let lokasi_terminal = decodeURIComponent(param[9])

			var volume = $("#volume" + nom).val();

			if (volume == "") {
				Swal.fire({
					title: "Oppss..",
					text: "Silahkan isi volume terlebih dahulu",
					icon: "warning"
				});
				$('#nt1' + nom).css('display', 'none');
				$('#sc1' + nom).css('display', 'none');
				$('#tx1' + nom).css('display', 'inline');
			} else {
				if (sisa_inven < volume) {
					Swal.fire({
						title: "Oppss..",
						text: "Sisa stock kurang",
						icon: "warning"
					});
					$('#nt1' + nom).css('display', 'none');
					$('#sc1' + nom).css('display', 'none');
					$('#tx1' + nom).css('display', 'inline');
				} else {
					$('#nt1' + nom).css('display', 'inline');
					$('#sc1' + nom).css('display', 'inline');
					$('#tx1' + nom).css('display', 'none');
					$("#np1" + nom).val(decodeURIComponent(param[0]));
					$("#ps1" + nom).val(parseInt(decodeURIComponent(param[1])));
					$("#pr1" + nom).val(parseInt(decodeURIComponent(param[2])));
					$("#dp2" + nom).val(parseInt(decodeURIComponent(param[3])));
					$("#dv1" + nom).val(parseInt(decodeURIComponent(param[4])));
					$("#si1" + nom).val(parseInt(sisa_inven));
					$("#dp8" + nom).val(parseInt(decodeURIComponent(param[6])));
					$("#nt1" + nom).html("Nama Terminal : " + nama_terminal + " " + tanki_terminal + " " + lokasi_terminal)
					$("#sc1" + nom).html("Sisa Stock : " + new Intl.NumberFormat().format(sisa_inven));
					$("#modalPO").modal("hide");


					// Setelah modal ditutup, panggil hitungNettProfit untuk dp2 yang terisi
					hitungNettProfit($("#dp2" + nom));

				}

			}

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

		$(".table-grid3").floatThead({
			position: 'fixed',
			zIndex: 799,
			scrollContainer: function($table) {
				return $table.closest("#table-long");
			},
			responsiveContainer: function($table) {
				return $table.closest("#table-long");
			},
			top: function pageTop() {
				return $(".main-header").height() + $(".content-header").height();
			},
		});

		$("#urgent").on('ifChanged', function() {
			if ($(this).is(':checked')) {
				$(".urgent_condition").show(); // Tampilkan tombol "Simpan"
			} else {
				$(".urgent_condition").hide(); // Sembunyikan tombol "Simpan"
			}
		});

		$(".hitung").number(true, 0, ".", ",");
		// $(".dp8").select2({
		// 	placeholder: "Pilih salah satu",
		// 	allowClear: true
		// });
		$("#gform").find(".dp2").each(function() {
			if ($(this).val() != "") {
				var elm = $(this);
				hitungNettProfit(elm);
			}
		});




		$("form#gform").on("click", "#btnSbmt", function() {

			// Cek apakah checkbox "Urgent Conditional" dicentang
			if ($("#urgent").is(':checked')) {
				// Jika dicentang, periksa apakah attachment_condition diisi
				if ($("#attachment_condition").val() === "") {
					// Jika attachment_condition kosong, tampilkan pesan SweetAlert
					Swal.fire({
						icon: 'error',
						title: 'Lampiran Wajib diisi',
						text: 'Mohon isi Attachment Condition.',
					});
					return false;
				}
			}
			if (confirm("Apakah anda yakin?")) {
				$.ajax({
					type: 'POST',
					url: "./__cek_pr_customer_purchasing.php",
					dataType: "json",
					data: $("#gform").serializeArray(),
					cache: false,
					success: function(data) {
						console.log(data.error);
						if (data.error) {
							swal.fire({
								icon: "warning",
								width: '350px',
								allowOutsideClick: false,
								html: '<p style="font-size:14px; font-family:arial;">' + data.error + '</p>'
							});
							return false;
						} else {
							$("#loading_modal").modal({
								backdrop: "static"
							});
							$("form#gform").submit();
						}
					}
				});
				return false;
			} else return false;
		});

		$("#gform").on("click", "#table-grid3 button.addRow", function() {
			var count = parseInt($(this).attr('data-idp'));
			count++;
			var row = $(this).closest('tr');
			var idl = $(this).val();
			var urut = $(this).attr('data-idp', count);

			// $("#table-grid3").find(".dp1").each(function(i, v) {
			// 	$(v).select2("destroy");
			// });
			// $("#table-grid3").find(".dp8").each(function(i, v) {
			// 	$(v).select2("destroy");
			// });

			var cloning = row.clone();
			cloning.find('td').each(function(i, v) {
				var el = $(this).find(":first-child");
				var id = el.attr("id") || null;
				switch (i) {
					case 0:
						$(v).html('<button type="button" class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></button>');
						break;
				}

				// console.log(id)

				if (id && i != 0) {
					var elName = "new" + el.attr("name").substr(0, 3);
					var elId = el.attr("id");
					el.attr("id", elId + '' + idl + '' + count);
					el.attr('name', elName + '[]');
				}
			});

			let elemen = '<input class="text-right volume hitung" type="text" name="newVolume[]" value="" style="width:100%;" />' +
				'<input class="idx"type="hidden" name="newIdx[]" value="' + idl + '" />' +
				'<input class="cek" type="hidden" name="newCek[]" value="1" />';

			cloning.find(".volume_oil").html(elemen);
			row.find("td:last-child");
			row.after(cloning);

			// $("#table-grid3").find(".dp1").each(function(i, v) {
			// 	$(v).select2({
			// 		placeholder: "Pilih salah satu",
			// 		allowClear: true
			// 	});
			// });
			// $("#table-grid3").find(".dp8").each(function(i, v) {
			// 	$(v).select2({
			// 		placeholder: "Pilih salah satu",
			// 		allowClear: true
			// 	});
			// });
			$(".hitung").number(true, 0, ".", ",");

			$("#table-grid3").find(".noFormula").each(function(i, v) {
				$(this).text(i + 1);
				// $(this).closest('tr').find('.dp1').attr('id', 'dp1' + (i + 1));
				$(this).closest('tr').find('.dp8').attr('id', 'dp8' + (i + 1));
				$(this).closest('tr').find('.dp2').attr('id', 'dp2' + (i + 1));
				$(this).closest('tr').find('.dp9').attr('id', 'dp9' + (i + 1));
				$(this).closest('tr').find('.dp3').attr('id', 'dp3' + (i + 1));
				$(this).closest('tr').find('.dp4').attr('id', 'dp4' + (i + 1));
				$(this).closest('tr').find('.dp5').attr('id', 'dp5' + (i + 1));
				$(this).closest('tr').find('.dp6').attr('id', 'dp6' + (i + 1));
				$(this).closest('tr').find('.dp7').attr('id', 'dp7' + (i + 1));
				$(this).closest('tr').find('.dp10').attr('id', 'dp10' + (i + 1));
				$(this).closest('tr').find('.dp11').attr('id', 'dp11' + (i + 1));
				$(this).closest('tr').find('.dp12').attr('id', 'dp12' + (i + 1));
				$(this).closest('tr').find('.cek').attr('id', 'cek' + (i + 1));
				$(this).closest('tr').find('.idx').attr('id', 'idx' + (i + 1));
				$(this).closest('tr').find('.np1').attr('onclick', 'myFunction(' + (i + 1) + ')');
				$(this).closest('tr').find('.ps1').attr('id', 'ps1' + (i + 1));
				$(this).closest('tr').find('.pr1').attr('id', 'pr1' + (i + 1));
				$(this).closest('tr').find('.dv1').attr('id', 'dv1' + (i + 1));
				$(this).closest('tr').find('.si1').attr('id', 'si1' + (i + 1));
				$(this).closest('tr').find('.np1').attr('id', 'np1' + (i + 1));
				$(this).closest('tr').find('.nt1').attr('id', 'nt1' + (i + 1));
				$(this).closest('tr').find('.sc1').attr('id', 'sc1' + (i + 1));
				$(this).closest('tr').find('.tx1').attr('id', 'tx1' + (i + 1));
				$(this).closest('tr').find('.volume').attr('id', 'volume' + (i + 1));
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

		$("form#gform").on("click", "#backadmin", function() {
			if (confirm("Apakah anda yakin?")) {
				$("#loading_modal").modal({
					backdrop: "static"
				});
				$('input[name="backadmin"]').val(1);
				$("form#gform").submit();
			} else return false;
		});

		$("#gform").on("change", "select.dp8", function() {
			var idnya = $(this).attr("id").substr(3);
			$("#np1" + idnya).val("");
			getHargaBeli(idnya);
		}).on("keyup", ".dp2", function() {
			var elm = $(this);
			hitungNettProfit(elm);
		});

		function getHargaBeli(newId) {
			var vendor = $("#dv1" + newId).val();
			var awal = $("#dp3" + newId).val();
			var akhir = $("#dp4" + newId).val();
			var area = $("#dp5" + newId).val();
			var produk = $("#dp6" + newId).val();
			var depot = $("#dp8" + newId).val();

			if (vendor != "" && awal != "" && akhir != "" && area != "" && produk != "" && depot != "") {
				$('#loading_modal').modal({
					backdrop: "static"
				});
				$.ajax({
					type: 'POST',
					url: "./__get_harga_tebus.php",
					data: {
						q1: awal,
						q2: akhir,
						q3: produk,
						q4: area,
						q5: vendor,
						q6: depot
					},
					cache: false,
					success: function(data) {
						$("#dp2" + newId).val(data);
						hitungNettProfit($("#dp2" + newId));
					}
				});
				$("#loading_modal").modal("hide");
			} else {
				$("#dp2" + newId).val("");

				hitungNettProfit($("#dp2" + newId));
			}
		}

		// function hitungNettProfit(elm) {
		// 	var idx = elm.attr("id").split('dp2');
		// 	var dt1 = $("#dp2" + idx[1]).val() * 1;
		// 	var dt2 = $("#dp10" + idx[1]).val() * 1;
		// 	var dt3 = $("#dp11" + idx[1]).val() * 1;
		// 	var dtx = (dt3 - dt1) * dt2;
		// 	$("#dp9" + idx[1]).val(dtx);
		// }


		function hitungNettProfit(elm) {
			var idx = elm.attr("id").split('dp2');
			var dt1 = $("#dp2" + idx[1]).val() * 1;
			var dt2 = $("#volume" + idx[1]).val() * 1;
			var dt3 = $("#dp12" + idx[1]).val() * 1;
			var dtx = (dt3 - dt1) * dt2;
			$("#dp9" + idx[1]).val(dtx);
		}
	});
	$('#btnEdit').on('click', function() {
		$('#btnSbmt').css('display', '');
		$('#btnCancel').css('display', '');
		$('#btnEdit').css('display', 'none');
		$('.divText').css('display', 'none');
		$('.divEdit').css('display', '');
		$(".tombol-addnya").addClass("hide");
	});
	$('#btnCancel').on('click', function() {
		$('#btnSbmt').css('display', 'none');
		$('#btnCancel').css('display', 'none');
		$('#btnEdit').css('display', '');
		$('.divText').css('display', '');
		$('.divEdit').css('display', 'none');
		$(".tombol-addnya").addClass("hide");
	});
</script>
<div style="overflow-x: scroll" id="table-long">
    <div style="width:2060px; height:auto;">
        <div class="table-responsive-satu">
            <table class="table table-bordered" id="table-grid3" style="margin-bottom:0px;">
                <thead>
                    <tr>
                        <th class="text-center" rowspan="2" width="50"><input type="checkbox" name="cekAll" id="cekAll" value="1" /></th>
                        <th class="text-center" rowspan="2" width="50">No</th>
                        <th class="text-center" rowspan="2" width="200">Customer/ Bidang Usaha</th>
                        <th class="text-center" rowspan="2" width="230">Area/ Alamat Kirim/ Wilayah OA</th>
                        <th class="text-center" rowspan="2" width="190">PO Customer</th>
                        <th class="text-center" colspan="2">Quantity</th>
                        <th class="text-center" rowspan="2" width="120">Suplier/ Terminal/ Harga Beli</th>
                        <th class="text-center" rowspan="2" width="80">PBBKB (%)</th>
                        <th class="text-center" colspan="7">Harga (Rp/Liter)</th>
                        <th class="text-center" rowspan="2" width="110">Nett Profit</th>
                        <th class="text-center" rowspan="2" width="60">Price List</th>
                        <th class="text-center" rowspan="2" width="100">Gain/Loss</th>
                        <th class="text-center" rowspan="2" width="100">Loading Order</th>
                    </tr>
                    <tr>
                        <th class="text-center" width="65">Volume (Liter)</th>
                        <th class="text-center" width="80">Edit (Liter)</th>
                        <th class="text-center" width="75">Harga Jual (Gross)</th>
                        <th class="text-center" width="60">Ongkos Angkut</th>
                        <th class="text-center" width="60">Refund</th>
                        <th class="text-center" width="60">Oil Dues</th>
                        <th class="text-center" width="60">PBBKB</th>
                        <th class="text-center" width="60">Other Cost</th>
                        <th class="text-center" width="75">Harga Jual (Nett)</th>
                    </tr>
                </thead>
                <tbody>
				<?php
                    $sql1 = "select a.id_pr, a.nomor_pr, a.tanggal_pr, a.sm_summary, a.sm_pic, a.sm_tanggal, a.purchasing_summary, a.purchasing_pic, a.purchasing_tanggal, 
							 a.cfo_result, a.cfo_summary, a.cfo_pic, a.cfo_tanggal, a.ceo_result, a.ceo_summary, a.ceo_pic, a.ceo_tanggal, a.is_ceo, a.id_wilayah, 
							 b.nama_cabang, a.disposisi_pr, a.revert_cfo, a.revert_cfo_summary, a.revert_ceo, a.revert_ceo_summary 
							 from pro_pr a join pro_master_cabang b on a.id_wilayah = b.id_master where 1=1";
					if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 4)
						$sql1 .= " and a.disposisi_pr > 3 and a.cfo_result = 0";
					else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 3)
						$sql1 .= " and a.disposisi_pr > 3 and a.is_ceo = 1 and a.ceo_result = 0";
                    $sql1 .= " order by a.id_pr";
                    $res1 = $con->getResult($sql1);
                    if(count($res1) > 0){
                        $nom = 0;
                        foreach($res1 as $data1){
                            $nom++;
							$idr = $data1['id_pr'];
							echo '<tr><td colspan="20" style="background-color:#eee;"><b>['.strtoupper($data1['nama_cabang']).'] '.$data1['nomor_pr'].'</b></td></tr>';							
							$sql2 = "select a.*, b.sm_result, b.sm_summary, b.sm_pic, b.sm_tanggal, b.purchasing_result, b.purchasing_summary, b.purchasing_pic, 
									b.purchasing_tanggal, b.cfo_result, b.cfo_summary, b.cfo_pic, b.cfo_tanggal, b.is_ceo, b.ceo_result, b.ceo_summary, b.ceo_pic, b.ceo_tanggal, 
									b.revert_cfo, b.revert_cfo_summary, b.revert_ceo, b.revert_ceo_summary, 
									c.tanggal_kirim, e.alamat_survey, e.id_wil_oa, f.nama_prov, g.nama_kab,
									n.nilai_pbbkb, k.masa_awal, k.masa_akhir, k.id_area, o.harga_normal, k.id_penawaran, h.nama_customer, h.id_customer, i.fullname, l.nama_area, d.harga_poc, 
									k.refund_tawar, m.jenis_produk, e.jenis_usaha, d.nomor_poc, d.produk_poc, p.nama_terminal, p.tanki_terminal, p.lokasi_terminal, q.nama_vendor, 
									r.wilayah_angkut, m.merk_dagang, d.lampiran_poc, d.lampiran_poc_ori, d.id_poc, h.kode_pelanggan, k.other_cost 
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
									left join pro_master_harga_minyak o on o.periode_awal = k.masa_awal and o.periode_akhir = k.masa_akhir and o.id_area = k.id_area 
									and o.produk = k.produk_tawar and o.pajak = k.pbbkb_tawar 
									left join pro_master_terminal p on a.pr_terminal = p.id_master 
									left join pro_master_vendor q on a.pr_vendor = q.id_master 
									left join pro_master_wilayah_angkut r on e.id_wil_oa = r.id_master and e.prov_survey = r.id_prov and e.kab_survey = r.id_kab 
									where a.id_pr = '".$idr."' and a.is_approved = 1 
									order by a.is_approved desc, c.tanggal_kirim, k.id_cabang, k.id_area, a.id_plan, a.id_prd";
							$res2 = $con->getResult($sql2);
							if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 4)
								$disp = $data1['cfo_result'];
							else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 3)
								$disp = $data1['ceo_result'];

							if(count($res2) == 0){
								echo '<tr><td colspan="20" style="text-align:center">Data tidak ditemukan </td></tr>';
							} else{
								$nom2 = 0;
								$total1 = 0; $total2 = 0; $total3 = 0; $total4 = 0;
								foreach($res2 as $data2){
									$id_poc_sc[] = $data2['id_poc'];
									$nom2++;
									$idp 	= $data2['id_prd'];
									$idk 	= $data2['id_pr'];
									$tempal = strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data2['nama_kab']));
									$alamat	= $data2['alamat_survey']." ".ucwords($tempal)." ".$data2['nama_prov'];

									$pbbkbT = ($data2['nilai_pbbkb']/100) + 1.11;
									$oildus = $data2['harga_poc'] / $pbbkbT * 0.003;
									$pbbkbN = $data2['harga_poc'] / $pbbkbT * ($data2['nilai_pbbkb']/100);
									$tmphrg = $data2['refund_tawar'] + $oildus + $data2['transport'] + $pbbkbN + $data2['other_cost'];
									$nethrg = $data2['harga_poc'] - $tmphrg;
									$volume = $data2['volume'];
									$netgnl = ($nethrg - $data2['harga_normal']) * $volume;
									$netprt = ($nethrg - $data2['pr_harga_beli']) * $volume;
									$total1 = $total1 + $volume;
									$total2 = $total2 + $data2['vol_ket'];
									$total3 = $total3 + $netprt;
									$total4 = $total4 + $netgnl;
									$checked= ($data2['is_approved'])?' checked':'';

									$tmn1 	= ($data2['nama_terminal'])?$data2['nama_terminal']:'';
									$tmn2 	= ($data2['tanki_terminal'])?'<br />'.$data2['tanki_terminal']:'';
									$tmn3 	= ($data2['lokasi_terminal'])?', '.$data2['lokasi_terminal']:'';
									$depot 	= $tmn1.$tmn2.$tmn3;
									
									$pathPt = $public_base_directory.'/files/uploaded_user/lampiran/'.$data2['lampiran_poc'];
									$lampPt = $data2['lampiran_poc_ori'];
									if($data2['lampiran_poc'] && file_exists($pathPt)){
										$linkPt = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=2&ktg=POC_".$data2['id_poc']."_&file=".$lampPt);
										$attach = '<a href="'.$linkPt.'"><i class="fa fa-file-alt" title="'.$lampPt.'"></i> PO Customer</a>';
									} else {$attach = '';}

                ?>
                    <tr>
                        <td class="text-center">
						<?php 
							if(!$disp){
								echo '<input type="checkbox" name="cek['.$idk.']['.$idp.']" id="cek_'.$nom.'_'.$nom2.'" class="chkp" value="1"'.$checked.' />';
								echo '<input type="hidden" name="vol['.$idk.']['.$idp.']" id="vol_'.$nom.'_'.$nom2.'" value="'.$data2['volume'].'" />';
							} else{ echo ($data2['is_approved'])?'<i class="fa fa-check"></i>':'&nbsp;';}
						?></td>
                        <td class="text-center"><?php echo $nom2; ?></td>
                        <td>
                            <p style="margin-bottom:0px"><b><?php echo ($data2['kode_pelanggan'] ? $data2['kode_pelanggan'].' - ':'').$data2['nama_customer'];?></b></p>
                            <p style="margin-bottom:0px"><?php echo $data2['jenis_usaha'];?></p>
                            <p style="margin-bottom:0px"><i><?php echo $data2['fullname'];?></i></p>
                        </td>
                        <td>
                            <p style="margin-bottom:0px"><b><?php echo $data2['nama_area'];?></b></p>
                            <p style="margin-bottom:0px"><?php echo $alamat;?></p>
                            <p style="margin-bottom:0px"><?php echo 'Wilayah OA : '.$data2['wilayah_angkut'];?></p>
                        </td>
                        <td>
                            <p style="margin-bottom:0px"><b><?php echo $data2['nomor_poc'];?></b></p>
                            <p style="margin-bottom:0px"><?php echo $data2['merk_dagang'];?></p>
                            <p style="margin-bottom:0px"><?php echo 'Tgl Kirim '.tgl_indo($data2['tanggal_kirim']);?></p>
                            <p style="margin-bottom:0px"><?php echo $attach;?></p>
						</td>
                        <td class="text-right"><?php echo number_format($volume); ?></td>
                        <td class="text-right">
						<?php 
							$volket = $data2['vol_ket'];
							if(!$disp) 
								echo '<input type="text" name="ket['.$idk.']['.$idp.']" id="ket_'.$nom.'_'.$nom2.'" class="form-control input-po hitung" value="'.$volket.'" />';
							else echo ($volket)?number_format($volket):'&nbsp;';
						?>
                        </td>
                        <td>
                            <p style="margin-bottom:0px"><b><?php echo $data2['nama_vendor'];?></b></p>
                            <p style="margin-bottom:0px"><?php echo $depot;?></p>
						<?php 
							$harga_beli = $data2['pr_harga_beli'];
							if(!$disp) 
							echo '<input type="text" name="dp2['.$idk.']['.$idp.']" id="dp2_'.$nom.'_'.$nom2.'" class="form-control input-po hitung" value="'.$harga_beli.'" />';
							else echo '<p style="margin-bottom:0px">'.number_format($harga_beli).'</p>';
						?></td>
                        <td class="text-right"><?php echo $data2['nilai_pbbkb']." %"; ?></td>
                        <td class="text-right"><?php echo number_format($data2['harga_poc']);?></td>
                        <td class="text-right"><?php echo number_format($data2['transport']);?></td>
                        <td class="text-right"><?php echo number_format($data2['refund_tawar']);?></td>
                        <td class="text-right"><?php echo number_format($oildus);?></td>
                        <td class="text-right"><?php echo number_format($pbbkbN);?></td>
                        <td class="text-right"><?php echo number_format($data2['other_cost']);?></td>
                        <td class="text-right"><?php echo number_format($nethrg);?></td>
                        <td class="text-right"><?php echo number_format($netprt);?></td>
                        <td class="text-right"><?php echo number_format($data2['pr_price_list']); ?></td>
                        <td class="text-right"><?php echo number_format($netgnl); ?></td>
                        <td><?php echo $data2['nomor_lo_pr'];?></td>
                    </tr>
				<?php } ?>
                	<tr>
                    	<td style="background-color:#f4f4f4;" class="text-center" colspan="5"><b>TOTAL</b></td>
                    	<td style="background-color:#f4f4f4;" class="text-right"><b><?php echo number_format($total1); ?></b></td>
                    	<td style="background-color:#f4f4f4;" class="text-center" colspan="10">&nbsp;</td>
                    	<td style="background-color:#f4f4f4;" class="text-right"><b><?php echo number_format($total3); ?></b></td>
                    	<td style="background-color:#f4f4f4;" class="text-right">&nbsp;</td>
                    	<td style="background-color:#f4f4f4;" class="text-right"><b><?php echo number_format($total4); ?></b></td>
                    	<td style="background-color:#f4f4f4;" class="text-right">&nbsp;</td>
                    </tr>
				<?php } } } else echo '<tr><td colspan="20" style="text-align:center">Data tidak ditemukan </td></tr>'; ?>
                </tbody>
			</table>
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
</style>

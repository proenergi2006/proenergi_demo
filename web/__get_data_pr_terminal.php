<div style="overflow-x: scroll" id="table-long">
    <div style="width:1120px; height:auto;">
        <div class="table-responsive-satu">
			<table class="table table-bordered" id="table-grid3">
                <thead>
                    <tr>
                        <th class="text-center" width="50">No</th>
                        <th class="text-center" width="200">Customer/ Bidang Usaha</th>
                        <th class="text-center" width="230">Area/ Alamat Kirim/ Wilayah OA</th>
                        <th class="text-center" width="160">PO Customer</th>
                        <th class="text-center" width="125">Catatan</th>
                        <th class="text-center" width="80">Angkutan</th>
                        <th class="text-center" width="65">Volume (Liter)</th>
                        <th class="text-center" width="120">Suplier/ Depot</th>
                        <th class="text-center" width="90">Tanggal Issued</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                    $sql = "select a.*, b.sm_result, b.sm_summary, b.sm_pic, b.sm_tanggal, b.purchasing_result, b.purchasing_summary, b.purchasing_pic, b.purchasing_tanggal, 
							b.cfo_result, b.cfo_summary, b.cfo_pic, b.cfo_tanggal, b.is_ceo, b.ceo_result, b.ceo_summary, b.ceo_pic, b.ceo_tanggal, c.created_time, 
							c.tanggal_kirim, e.alamat_survey, e.id_wil_oa, f.nama_prov, g.nama_kab, n.nilai_pbbkb, k.masa_awal, k.masa_akhir, k.id_area, o.harga_normal, 
							h.nama_customer, h.id_customer, i.fullname, l.nama_area, d.harga_poc, k.refund_tawar, m.jenis_produk, e.jenis_usaha, d.nomor_poc, d.produk_poc, 
							p.nama_terminal, p.tanki_terminal, p.lokasi_terminal, q.nama_vendor, c.status_jadwal, h.kode_pelanggan, m.merk_dagang, d.id_poc, 
							d.lampiran_poc, d.lampiran_poc_ori, r.wilayah_angkut, s.id_pod, t.id_dsd, u.id_dsk 
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
							join pro_master_harga_minyak o on o.periode_awal = k.masa_awal and o.periode_akhir = k.masa_akhir and o.id_area = k.id_area 
							and o.produk = k.produk_tawar and o.pajak = k.pbbkb_tawar 
							left join pro_master_terminal p on a.pr_terminal = p.id_master 
							left join pro_master_vendor q on a.pr_vendor = q.id_master 
							left join pro_master_wilayah_angkut r on e.id_wil_oa = r.id_master and e.prov_survey = r.id_prov and e.kab_survey = r.id_kab 
							left join pro_po_detail s on a.id_prd = s.id_prd 
							left join pro_po_ds_detail t on a.id_prd = t.id_prd 
							left join pro_po_ds_kapal u on a.id_prd = u.id_prd 
                            where a.id_pr = '".$idr."' and a.is_approved = 1
							order by a.is_approved desc, c.tanggal_kirim, k.id_cabang, k.id_area, a.id_plan, a.id_prd";
                	$res = $con->getResult($sql);
					$fnr = $row[0]['disposisi_pr'];
					$edt = $row[0]['is_edited'];
                    if(count($res) == 0){
                        echo '<tr><td colspan="9" style="text-align:center">Data tidak ditemukan </td></tr>';
                    } else{
                        $nom = 0;
						$jum = 0;
                        foreach($res as $data){
                            $nom++;
                            $idp 	= $data['id_prd'];
                            $idl 	= $data['id_plan'];
							$tempal = strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
                            $alamat	= $data['alamat_survey']." ".ucwords($tempal)." ".$data['nama_prov'];

							$pbbkbT = ($data['nilai_pbbkb']/100) + 1.11;
							$oildus = $data['harga_poc'] / $pbbkbT * 0.003;
							$pbbkbN = $data['harga_poc'] / $pbbkbT * ($data['nilai_pbbkb']/100);
							$tmphrg = $data['refund_tawar'] + $oildus + $data['transport'] + $pbbkbN + $data['other_cost'];
							$nethrg = $data['harga_poc'] - $tmphrg;
							$volume = $data['volume'];
							$volori = ($data['vol_ori'])?$data['vol_ori']:$data['volume'];
							$netgnl = ($nethrg - $data['harga_normal']) * $volume;
							$netprt = ($nethrg - $data['pr_harga_beli']) * $volume;
							$total1 = $total1 + $volume;
							$total2 = $total2 + $data['vol_ket'];
							$total3 = $total3 + $netprt;
							$total4 = $total4 + $netgnl;
							$checked= ($data['is_approved'])?' checked':'';
							$flagEd = !$data['id_pod'] && !$data['id_dsd'] && !$data['id_dsk'];
							$class1 = "form-control input-po hitung toa";
							$arrMobil = array(1=>"Truck", "Kapal", "Loco"); 
							$tmn0 	= $data['pr_terminal'];
							$tmn1 	= ($data['nama_terminal'])?$data['nama_terminal']:'';
							$tmn2 	= ($data['tanki_terminal'])?'<br />'.$data['tanki_terminal']:'';
							$tmn3 	= ($data['lokasi_terminal'])?', '.$data['lokasi_terminal']:'';
							$depot 	= $tmn1.$tmn2.$tmn3;
							
							$pathPt = $public_base_directory.'/files/uploaded_user/lampiran/'.$data['lampiran_poc'];
							$lampPt = $data['lampiran_poc_ori'];
							if($data['lampiran_poc'] && file_exists($pathPt)){
								$linkPt = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=2&ktg=POC_".$data['id_poc']."_&file=".$lampPt);
								// $attach = '<a href="'.$linkPt.'"><i class="fa fa-file-alt" title="'.$lampPt.'"></i> PO Customer</a>';
							} else {$attach = '';}
							
                ?>
                    <tr>
                        <td class="text-center"><span class="noFormula"><?php echo $nom; ?></span></td>
                        <td>
                            <p style="margin-bottom:0px"><b><?php echo ($data['kode_pelanggan'] ? $data['kode_pelanggan'].' - ':'').$data['nama_customer'];?></b></p>
                            <p style="margin-bottom:0px"><?php echo $data['jenis_usaha'];?></p>
                            <p style="margin-bottom:0px"><i><?php echo $data['fullname'];?></i></p>
                        </td>
                        <td>
                            <p style="margin-bottom:0px"><b><?php echo $data['nama_area'];?></b></p>
                            <p style="margin-bottom:0px"><?php echo $alamat;?></p>
                            <p style="margin-bottom:0px"><?php echo 'Wilayah OA : '.$data['wilayah_angkut'];?></p>
                        </td>
                        <td>
                            <p style="margin-bottom:0px"><b><?php echo $data['nomor_poc'];?></b></p>
                            <p style="margin-bottom:0px"><?php echo $data['merk_dagang'];?></p>
                            <p style="margin-bottom:0px"><?php echo 'Tgl Kirim '.tgl_indo($data['tanggal_kirim']);?></p>
                            <p style="margin-bottom:0px"><?php echo $attach;?></p>
						</td>
                        <td><?php echo $data['status_jadwal'];?></td>
						<td><?php echo $arrMobil[$data['pr_mobil']]; ?></td>
                        <td class="text-right"><?php echo number_format($data['volume']);?></td>
                        <td>
                            <p style="margin-bottom:0px"><b><?php echo $data['nama_vendor'];?></b></p>
                            <p style="margin-bottom:0px"><?php echo $depot;?></p>
						</td>
                        <td><?php echo date("d/m/Y H:i:s", strtotime($data['created_time']))." WIB";?></td>
                    </tr>
                <?php } } ?>
                </tbody>
			</table>
      	</div>
    </div>
</div>

<div class="form-group row">
	<?php if($res[0]['sm_result']){ ?>
    <div class="col-sm-6">
        <label>Catatan BM</label>
        <div class="form-control" style="height:auto">
            <?php echo ($res[0]['sm_summary']); ?>
            <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $res[0]['sm_pic']." - ".date("d/m/Y H:i:s", strtotime($res[0]['sm_tanggal']))." WIB"; ?></i></p>
        </div>
    </div>
	<?php } if($res[0]['purchasing_result']){ ?>
    <div class="col-sm-6 col-sm-top">
        <label>Catatan Purchasing</label>
        <div class="form-control" style="height:auto">
            <?php echo ($res[0]['purchasing_summary']); ?>
            <p style="margin:10px 0 0; font-size:12px;"><i>
				<?php echo $res[0]['purchasing_pic']." - ".date("d/m/Y H:i:s", strtotime($res[0]['purchasing_tanggal']))." WIB"; ?>
			</i></p>
        </div>
    </div>
    <?php } ?>
</div>
<div class="form-group row">
	<?php if($res[0]['cfo_result']){ ?>
    <div class="col-sm-6">
        <label>Catatan CFO</label>
        <div class="form-control" style="height:auto">
            <?php echo ($res[0]['cfo_summary']); ?>
            <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $res[0]['cfo_pic']." - ".date("d/m/Y H:i:s", strtotime($res[0]['cfo_tanggal']))." WIB"; ?></i></p>
        </div>
    </div>
	<?php } if($res[0]['ceo_result']){ ?>
    <div class="col-sm-6 col-sm-top">
        <label>Catatan CEO</label>
        <div class="form-control" style="height:auto">
            <?php echo ($res[0]['ceo_summary']); ?>
            <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $res[0]['ceo_pic']." - ".date("d/m/Y H:i:s", strtotime($res[0]['ceo_tanggal']))." WIB"; ?></i></p>
        </div>
    </div>
    <?php } ?>
</div>

<?php if(count($res) > 0){ ?>
<hr style="margin:0 0 10px" />
<div class="form-group row">
    <div class="col-sm-12">
        <div class="pad bg-gray">
            <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT."/purchase-request.php";?>">Kembali</a> 
            <a class="btn btn-success" title="Cetak" target="_blank" href="<?php echo $linkCetak;?>">Cetak</a> 
		</div>
    </div>
</div>
<?php } ?>

<style type="text/css">
	.input-po {
		padding: 3px 5px;
		height: auto;
		font-size: 11px;
		font-family:arial;
	}
</style>
<script>
	$(document).ready(function(){
		$(".hitung").number(true, 0, ".", ",");
	});
</script>


<div style="overflow-x: scroll" id="table-long">
    <div style="width:2060px; height:auto;">
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
                    $sql = "select a.*, b.sm_result, b.sm_summary, b.sm_pic, b.sm_tanggal, b.purchasing_result, b.purchasing_summary, b.purchasing_pic, b.purchasing_tanggal, 
							b.cfo_result, b.cfo_summary, b.cfo_pic, b.cfo_tanggal, b.is_ceo, c.tanggal_kirim, e.alamat_survey, e.id_wil_oa, f.nama_prov, g.nama_kab, 
							n.nilai_pbbkb, k.masa_awal, k.masa_akhir, k.id_area, o.harga_normal, h.nama_customer, h.id_customer, i.fullname, l.nama_area, d.harga_poc, 
							k.refund_tawar, k.other_cost, m.jenis_produk, e.jenis_usaha, d.nomor_poc, d.produk_poc, p.nama_terminal, p.tanki_terminal, p.lokasi_terminal, q.nama_vendor, 
							r.wilayah_angkut, m.merk_dagang, d.lampiran_poc, d.lampiran_poc_ori, d.id_poc, h.kode_pelanggan, b.revert_cfo, b.revert_cfo_summary 
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
                            where a.id_pr = '".$idr."' and a.is_approved = 1 
							order by a.is_approved desc, c.tanggal_kirim, k.id_cabang, k.id_area, a.id_plan, a.id_prd";
							// die($sql);
                    $res = $con->getResult($sql);
                    $fnr = $res[0]['cfo_result'];
					$arrResult = array("Tidak","Ya");
                    if(count($res) == 0){
                        echo '<tr><td colspan="20" style="text-align:center">Data tidak ditemukan </td></tr>';
                    } else{
                        $nom = 0;
						$total1 = 0; $total2 = 0; $total3 = 0; $total4 = 0;
                        foreach($res as $data){
							
							$id_poc_sc[] = $data['id_poc'];

                            $nom++;
                            $idp 	= $data['id_prd'];
							$tempal = strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
                            $alamat	= $data['alamat_survey']." ".ucwords($tempal)." ".$data['nama_prov'];

							$pbbkbT = ($data['nilai_pbbkb']/100) + 1.11;
							$oildus = $data['harga_poc'] / $pbbkbT * 0.003;
							$pbbkbN = $data['harga_poc'] / $pbbkbT * ($data['nilai_pbbkb']/100);
							$tmphrg = $data['refund_tawar'] + $oildus + $data['transport'] + $pbbkbN + $data['other_cost'];
							$nethrg = $data['harga_poc'] - $tmphrg;
							$volume = $data['volume'];
							$netgnl = ($nethrg - $data['harga_normal']) * $volume;
							$netprt = ($nethrg - $data['pr_harga_beli']) * $volume;
							$total1 = $total1 + $volume;
							$total2 = $total2 + $data['vol_ket'];
							$total3 = $total3 + $netprt;
							$total4 = $total4 + $netgnl;
							$checked= ($data['is_approved'])?' checked':'';

							$tmn1 	= ($data['nama_terminal'])?$data['nama_terminal']:'';
							$tmn2 	= ($data['tanki_terminal'])?'<br />'.$data['tanki_terminal']:'';
							$tmn3 	= ($data['lokasi_terminal'])?', '.$data['lokasi_terminal']:'';
							$depot 	= $tmn1.$tmn2.$tmn3;
							
							$pathPt = $public_base_directory.'/files/uploaded_user/lampiran/'.$data['lampiran_poc'];
							$lampPt = $data['lampiran_poc_ori'];
							if($data['lampiran_poc'] && file_exists($pathPt)){
								$linkPt = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=2&ktg=POC_".$data['id_poc']."_&file=".$lampPt);
								$attach = '<a href="'.$linkPt.'"><i class="fa fa-file-alt" title="'.$lampPt.'"></i> PO Customer</a>';
							} else {$attach = '';}
                ?>
                    <tr>
                        <td class="text-center">
						<?php 
							if(!$fnr){
								echo '<input type="checkbox" name="cek['.$idp.']" id="cek'.$nom.'" class="chkp" value="1"'.$checked.' />';
								echo '<input type="hidden" name="vol['.$idp.']" id="vol'.$nom.'" value="'.$data['volume'].'" />';
							} else{ echo ($data['is_approved'])?'<i class="fa fa-check"></i>':'&nbsp;';}
						?></td>
                        <td class="text-center"><?php echo $nom; ?></td>
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
                        <td class="text-right"><?php echo number_format($volume); ?></td>
                        <td class="text-right">
						<?php 
							if(!$fnr) 
								echo '<input type="text" name="ket['.$idp.']" id="ket'.$nom.'" class="form-control input-po hitung" value="'.$data['vol_ket'].'" />';
							else 
								echo ($data['vol_ket'])?number_format($data['vol_ket']):'&nbsp;';
						?>
                        </td>
                        <td>
                            <p style="margin-bottom:0px"><b><?php echo $data['nama_vendor'];?></b></p>
                            <p style="margin-bottom:0px"><?php echo $depot;?></p>
						<?php 
							if(!$fnr) echo '<input type="text" name="dp2['.$idp.']" id="dp2'.$nom.'" class="form-control input-po hitung" value="'.$data['pr_harga_beli'].'" />';
							else echo '<p style="margin-bottom:0px">'.number_format($data['pr_harga_beli']).'</p>';
						?></td>
                        <td class="text-right"><?php echo $data['nilai_pbbkb']." %"; ?></td>
                        <td class="text-right"><?php echo number_format($data['harga_poc']);?></td>
                        <td class="text-right"><?php echo number_format($data['transport']);?></td>
                        <td class="text-right"><?php echo number_format($data['refund_tawar']);?></td>
                        <td class="text-right"><?php echo number_format($oildus);?></td>
                        <td class="text-right"><?php echo number_format($pbbkbN);?></td>
                        <td class="text-right"><?php echo number_format($data['other_cost']);?></td>
                        <td class="text-right"><?php echo number_format($nethrg);?></td>
                        <td class="text-right"><?php echo number_format($netprt);?></td>
                        <td class="text-right"><?php echo number_format($data['pr_price_list']); ?></td>
                        <td class="text-right"><?php echo number_format($netgnl); ?></td>
                        <td><?php echo $data['nomor_lo_pr'];?></td>
                    </tr>
				<?php } } ?>
                </tbody>
                <tfoot>
                	<tr>
                    	<th colspan="5" class="text-center"><b>TOTAL</b></th>
                    	<th class="text-right"><?php echo number_format($total1); ?></th>
                    	<th colspan="10" class="text-center">&nbsp;</th>
                    	<th class="text-right"><?php echo number_format($total3); ?></th>
                    	<th class="text-right">&nbsp;</th>
                    	<th class="text-right"><?php echo number_format($total4); ?></th>
                    	<th class="text-right">&nbsp;</th>
                    </tr>
                </tfoot>
			</table>
      	</div>
    </div>
</div>

<div class="form-group row">
    <div class="col-sm-6">
        <label>Catatan BM</label>
        <div class="form-control" style="height:auto">
            <?php echo ($res[0]['sm_summary']); ?>
            <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $res[0]['sm_pic']." - ".date("d/m/Y H:i:s", strtotime($res[0]['sm_tanggal']))." WIB"; ?></i></p>
        </div>
    </div>
    <div class="col-sm-6 col-sm-top">
        <label>Catatan Purchasing</label>
        <div class="form-control" style="height:auto">
            <?php echo ($res[0]['purchasing_summary']); ?>
            <p style="margin:10px 0 0; font-size:12px;"><i>
				<?php echo $res[0]['purchasing_pic']." - ".date("d/m/Y H:i:s", strtotime($res[0]['purchasing_tanggal']))." WIB"; ?>
			</i></p>
        </div>
    </div>
</div>

<div class="row">
	<?php if($res[0]['revert_cfo']){ ?>
    <div class="col-sm-6">
    	<div class="form-group">
            <label>Catatan Pengembalian CFO</label>
            <div class="form-control" style="height:auto"><?php echo ($res[0]['revert_cfo_summary']); ?></div>
		</div>
    </div>
	<?php } ?>
</div>
<?php if($res[0]['revert_cfo']) echo '<hr style="border-top:4px double #ddd; margin:5px 0px 20px;" />';?>

<?php if(!$fnr){ ?>
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

<div class="form-group row persetujuan-cfo <?php echo 'hide';?>">
    <div class="col-sm-6">
        <label>Diteruskan ke CEO ?*</label>
        <div class="radio clearfix" style="margin:0px;">	
        	<?php if(!$fnr){ ?>
            <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="extend" id="extend1" class="validate[required]" value="1" /> Ya</label>
            <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="extend" id="extend2" class="validate[required]" value="2" /> Tidak</label>
			<?php } else { ?>
            <label class="col-xs-12" style="margin-bottom:5px;">
            	<input type="radio" name="extend" id="extend1" value="1" checked /> <?php echo $arrResult[$res[0]['is_ceo']]; ?>
			</label>
        	<?php } ?>
        </div>
    </div>
	<div class="col-sm-6 col-sm-top">
		<label>Catatan CFO</label>
		<?php if(!$fnr){ ?>
		<textarea name="summary" id="summary" class="form-control"></textarea>
		<?php } else if($res[0]['cfo_summary']){ ?>
		<div class="form-control" style="height:auto">
			<?php echo ($res[0]['cfo_summary']); ?>
			<p style="margin:10px 0 0; font-size:12px;"><i><?php echo $res[0]['cfo_pic']." - ".date("d/m/Y H:i:s", strtotime($res[0]['cfo_tanggal']))." WIB"; ?></i></p>
		</div>
		<?php } ?>
	</div>
</div>

<?php if(count($res) > 0){ ?>
<hr style="margin:0 0 10px" />
<div class="form-group row">
    <div class="col-sm-12">
        <div class="pad bg-gray">
			<input type="hidden" name="prnya" id="prnya" value="cfo" />
            <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
            <input type="hidden" name="idw" value="<?php echo $row[0]['id_wilayah']; ?>" />
            <input type="hidden" name="idg" value="<?php echo $row[0]['id_group']; ?>" />
            <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT."/purchase-request.php";?>">Kembali</a> 
            <?php if(!$fnr){ ?><button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Submit</button><?php } ?>
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
	$(document).ready(function(){
		$(".hitung").number(true, 0, ".", ",");

		$("input[name='revert']").on("ifChecked", function(){
			var nilai = $(this).val();
			if(nilai == 1){
				$(".persetujuan-cfo").addClass("hide");
			} else if(nilai == 2){
				$(".persetujuan-cfo").removeClass("hide");
			}
		});

		$("form#gform").on("click", "#btnSbmt", function(){
			if(!$("input[name='revert']:checked").validationEngine('validate')){
				$("#preview_modal").find("#preview_alert").text("Pengembalian data belum dipilih..");
				$("#preview_modal").modal();					
				return false;
			} else if($("input[name='revert']:checked").val() == "2" && !$("input[name='extend']:checked").validationEngine('validate')){
				$("#preview_modal").find("#preview_alert").text("Disposisi data belum dipilih..");
				$("#preview_modal").modal();					
				return false;
			} else{
				if(confirm("Apakah anda yakin?")){
					if($("#gform").find("input:checked").length > 0){
						$("#loading_modal").modal({backdrop:"static"});
						$.ajax({
							type	: 'POST',
							url		: "./__cek_pr_customer_purchasing.php",
							dataType: "json",
							data	: $("#gform").serializeArray(),
							cache	: false,
							success : function(data){
								if(data.error){
									$("#preview_modal").find("#preview_alert").html(data.error);
									$("#preview_modal").modal();
									$("#loading_modal").modal("hide");					
									return false;
								} else{
									$("form#gform").submit();
								}
							}
						});
						return false;
					} else{
						$("#preview_modal").find("#preview_alert").text("Data DR Belum dipilih..");
						$("#preview_modal").modal();					
						return false;
					}
				} else return false;
			}
		});
	});		
</script>

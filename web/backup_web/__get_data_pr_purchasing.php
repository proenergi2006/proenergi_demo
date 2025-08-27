<div style="overflow-x: scroll" id="table-long">
    <div style="width:2110px; height:auto;">
        <div class="table-responsive-satu">
            <table class="table table-bordered" id="table-grid3">
                <thead>
                    <tr>
                        <th class="text-center" rowspan="2" width="50">No</th>
                        <th class="text-center" rowspan="2" width="200">Customer/ Bidang Usaha</th>
                        <th class="text-center" rowspan="2" width="230">Area/ Alamat Kirim/ Wilayah OA</th>
                        <th class="text-center" rowspan="2" width="190">PO Customer</th>
                        <th class="text-center" rowspan="2" width="65">Volume (Liter)</th>
                        <th class="text-center" rowspan="2" width="80">PBBKB</th>
                        <th class="text-center" rowspan="2" width="130">Suplier</th>
                        <th class="text-center" rowspan="2" width="195">Depot</th>
                        <th class="text-center" rowspan="2" width="150">Harga Beli</th>
                        <th class="text-center" colspan="7">Harga (Rp/Liter)</th>
                        <th class="text-center" rowspan="2" width="110">Nett Profit</th>
                        <th class="text-center" rowspan="2" width="60">Price List</th>
                        <th class="text-center" rowspan="2" width="100">Gain/Loss</th>
                        <th class="text-center" rowspan="2" width="100">Catatan</th>
                        <th class="text-center" rowspan="2" width="100">Loading Order</th>
                    </tr>
                    <tr>
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
					$sql = "
						select 
							a.*, 
							b.sm_result, 
							b.sm_summary, 
							b.sm_pic, 
							b.sm_tanggal, 
							b.purchasing_result, 
							b.purchasing_summary, 
							b.purchasing_pic, 
							b.purchasing_tanggal,
							b.is_ceo, 
							c.tanggal_kirim, 
							c.status_plan, 
							c.catatan_reschedule, 
							c.status_jadwal, 
							e.alamat_survey, 
							e.id_wil_oa, 
							f.nama_prov, 
							g.nama_kab, 
							n.nilai_pbbkb, 
							k.masa_awal, 
							k.masa_akhir, 
							k.id_area, 
							o.harga_normal, 
							h.nama_customer, 
							h.id_customer, 
							i.fullname, 
							l.nama_area, 
							d.harga_poc, 
							k.refund_tawar, 
							k.other_cost, 
							m.jenis_produk, 
							e.jenis_usaha, 
							d.nomor_poc, 
							d.produk_poc, 
							p.nama_terminal, 
							p.tanki_terminal, 
							p.lokasi_terminal, 
							q.nama_vendor, 
							r.wilayah_angkut, 
							m.merk_dagang, 
							d.lampiran_poc, 
							d.lampiran_poc_ori, 
							d.id_poc, 
							h.kode_pelanggan, 
							b.revert_cfo, 
							b.revert_cfo_summary, 
							b.revert_ceo, 
							b.revert_ceo_summary 
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
						join pro_master_harga_minyak o on o.periode_awal = k.masa_awal 
							and o.periode_akhir = k.masa_akhir 
							and o.id_area = k.id_area 
							and o.produk = k.produk_tawar 
							and o.pajak = k.pbbkb_tawar 
						left join pro_master_terminal p on a.pr_terminal = p.id_master 
						left join pro_master_vendor q on a.pr_vendor = q.id_master 
						left join pro_master_wilayah_angkut r on e.id_wil_oa = r.id_master 
							and e.prov_survey = r.id_prov 
							and e.kab_survey = r.id_kab 
						where 
							a.id_pr = '" . $idr . "' and 
							a.is_approved = 1 
						order by 
							a.is_approved desc, 
							c.tanggal_kirim, 
							k.id_cabang, 
							k.id_area, 
							a.id_plan, 
							a.id_prd";

                    $res = $con->getResult($sql);
					$fnr = $res[0]['purchasing_result'] ?? null;
					
                    if (count($res) == 0) {
                        echo '<tr><td colspan="20" style="text-align: center">Data tidak ditemukan </td></tr>';
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
							$tempal = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
                            $alamat	= $data['alamat_survey'] . " " . ucwords($tempal) . " " . $data['nama_prov'];

							$pbbkbT = ($data['nilai_pbbkb'] / 100) + 1.1;
							$oildus = $data['harga_poc'] / $pbbkbT * 0.003;
							$pbbkbN = $data['harga_poc'] / $pbbkbT * ($data['nilai_pbbkb'] / 100);
							$tmphrg = $data['refund_tawar'] + $oildus + $data['transport'] + $pbbkbN + $data['other_cost'];
							$nethrg = $data['harga_poc'] - $tmphrg;
							$volume = $data['volume'];
							$netgnl = ($nethrg - $data['harga_normal']) * $volume;
							$netprt = ($nethrg - $data['pr_harga_beli']) * $volume;
							$total1 = $total1 + $volume;
							$total2 = $total2 + $data['vol_ket'];
							$total3 = $total3 + $netprt;
							$total4 = $total4 + $netgnl;
							
							$pathPt = $public_base_directory . '/files/uploaded_user/lampiran/' . $data['lampiran_poc'];
							$lampPt = $data['lampiran_poc_ori'];

							if ($data['lampiran_poc'] && file_exists($pathPt)) {
								$linkPt = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=2&ktg=POC_" . $data['id_poc'] . "_&file=" . $lampPt);
								$attach = '<a href="' . $linkPt . '"><i class="fa fa-file-alt" title="' . $lampPt . '"></i> PO Customer</a>';
							} else 
								$attach = '';
                ?>
                    <tr>
                        <td class="text-center"><?php echo $nom; ?></td>
                        <td>
                            <p style="margin-bottom:0px"><b><?php echo ($data['kode_pelanggan'] ? $data['kode_pelanggan'] . ' - ' : '') . $data['nama_customer'];?></b></p>
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
                        <td class="text-right"><?php echo $data['nilai_pbbkb']." %"; ?></td>
                        <td><?php 
                            $divEdit = '
                                <input type="hidden" name="cek['.$idp.']" id="cek'.$nom.'" value="1" />
                                <input type="hidden" name="dp3['.$idp.']" id="dp3'.$nom.'" value="'.$data['masa_awal'].'" />
                                <input type="hidden" name="dp4['.$idp.']" id="dp4'.$nom.'" value="'.$data['masa_akhir'].'" />
                                <input type="hidden" name="dp5['.$idp.']" id="dp5'.$nom.'" value="'.$data['id_area'].'" />
                                <input type="hidden" name="dp6['.$idp.']" id="dp6'.$nom.'" value="'.$data['produk_poc'].'" />
                                <input type="hidden" name="dp7['.$idp.']" id="dp7'.$nom.'" value="'.$data['harga_normal'].'" />
                                <input type="hidden" name="dp10['.$idp.']" id="dp10'.$nom.'" value="'.$volume.'" />
                                <input type="hidden" name="dp11['.$idp.']" id="dp11'.$nom.'" value="'.$nethrg.'" />
                            ';
                            $divText = '
                                <input type="hidden" name="dp1['.$idp.']" id="dp1'.$nom.'" value="'.$data['pr_vendor'].'" />
                                <input type="hidden" name="dp5['.$idp.']" id="dp5'.$nom.'" value="'.$data['id_area'].'" />
                                <input type="hidden" name="dp6['.$idp.']" id="dp6'.$nom.'" value="'.$data['produk_poc'].'" />
                                '.(($data['nama_vendor'])?$data['nama_vendor']:'&nbsp;').'
                            ';
							if(!$fnr){ 
								echo '<select name="dp1['.$idp.']" id="dp1'.$nom.'" class="form-control select2 dp1"><option></option>';
                                $con->fill_select("id_master","nama_vendor","pro_master_vendor",$data['pr_vendor'],"where is_active=1","id_master",false);
								echo $divEdit;
							} else {
                                echo '<div class="divText">';
                                echo $divText;
                                echo '</div>';
                                echo '<div class="divEdit" style="display: none;">';
                                echo '<select name="dp1['.$idp.']" id="dp1'.$nom.'" class="form-control select2 dp1"><option></option>';
                                $con->fill_select("id_master","nama_vendor","pro_master_vendor",$data['pr_vendor'],"where is_active=1","id_master",false);
                                echo $divEdit;
                                echo '</div>';
							}
						?></td>
                        <td><?php 
							if(!$fnr){ 
								$tmpDp8 = "concat(nama_terminal,'#',tanki_terminal,'#',lokasi_terminal)";
								echo '<select name="dp8['.$idp.']" id="dp8'.$nom.'" class="form-control dp8"><option></option>';
                                $con->fill_select("id_master",$tmpDp8,"pro_master_terminal",$data['pr_terminal'],"where is_active=1","id_master",false);
								echo '</select>';
							} else {
                                echo '<div class="divText">';
								$tmn1 = ($data['nama_terminal'])?$data['nama_terminal']:'';
								$tmn2 = ($data['tanki_terminal'])?' - '.$data['tanki_terminal']:'';
								$tmn3 = ($data['lokasi_terminal'])?', '.$data['lokasi_terminal']:'';
								echo '<input type="hidden" name="dp8['.$idp.']" id="dp8'.$nom.'" value="'.$data['pr_terminal'].'" />';
								echo $tmn1.$tmn2.$tmn3;
                                echo '</div>';
                                echo '<div class="divEdit" style="display: none;">';
                                $tmpDp8 = "concat(nama_terminal,'#',tanki_terminal,'#',lokasi_terminal)";
                                echo '<select name="dp8['.$idp.']" id="dp8'.$nom.'" class="form-control dp8"><option></option>';
                                $con->fill_select("id_master",$tmpDp8,"pro_master_terminal",$data['pr_terminal'],"where is_active=1","id_master",false);
                                echo '</select>';
                                echo '</div>';
							}
							echo '<p style="margin:5px 0 0;"><a style="cursor:pointer" class="detInven" data-idnya="'.$nom.'">Detil Inventory</a></p>';
						?></td>
                        <td class="text-left">
						<?php 
							if(!$fnr){ 
                                echo '<input type="text" name="dp2['.$idp.']" id="dp2'.$nom.'" class="form-control input-po hitung dp2" value="'.$data['pr_harga_beli'].'"/>';
                                echo '<p style="margin:5px 0px 0px;"><b>Masa Berlaku</b></p>';
                                echo '<p style="margin-bottom:0px;">'.date("d/m/Y",strtotime($data['masa_awal'])).' - '.date("d/m/Y",strtotime($data['masa_akhir'])).'</p>';
                                echo '</div>';
                                // echo '<div class="divEdit" style="display: none;">';
                                // echo '<input type="text" name="dp2['.$idp.']" id="dp2'.$nom.'" class="form-control input-po hitung dp2" value="'.$data['pr_harga_beli'].'"/>';
                                // echo '</div>';
							}else{ 
                                echo '<div class="divText">';
                                echo ($data['pr_harga_beli'])?'<p style="margin-bottom:0px;" class="text-right">'.number_format($data['pr_harga_beli']).'</p>':'&nbsp;';
    							echo '<p style="margin:5px 0px 0px;"><b>Masa Berlaku</b></p>';
    							echo '<p style="margin-bottom:0px;">'.date("d/m/Y",strtotime($data['masa_awal'])).' - '.date("d/m/Y",strtotime($data['masa_akhir'])).'</p>';
                                echo '</div>';
                                echo '<div class="divEdit" style="display: none;">';
                                echo '<input type="text" name="dp2['.$idp.']" id="dp2'.$nom.'" class="form-control input-po hitung dp2" value="'.$data['pr_harga_beli'].'"/>';
                                echo '</div>';
                            }
						?></td>
                        <td class="text-right"><?php echo number_format($data['harga_poc']);?></td>
                        <td class="text-right"><?php echo number_format($data['transport']);?></td>
                        <td class="text-right"><?php echo number_format($data['refund_tawar']);?></td>
                        <td class="text-right"><?php echo number_format($oildus);?></td>
                        <td class="text-right"><?php echo number_format($pbbkbN);?></td>
                        <td class="text-right"><?php echo number_format($data['other_cost']);?></td>
                        <td class="text-right"><?php echo number_format($nethrg);?></td>
                        <td class="text-right">
						<?php 
							if(!$fnr) echo '<input type="text" name="dp9['.$idp.']" id="dp9'.$nom.'" class="form-control input-po hitung dp9" readonly />';
							else echo number_format($netprt);
						?></td>
                        <td class="text-right"><?php echo number_format($data['harga_normal']); ?></td>
                        <td class="text-right"><?php echo number_format($netgnl); ?></td>
                        <td class="text-right"><?php echo $data['status_plan']==2?$data['catatan_reschedule']:$data['status_jadwal']; ?></td>
                        <td><?php echo $data['nomor_lo_pr'];?></td>
                    </tr>
				<?php } } ?>
                </tbody>
                <?php if($fnr){ ?>
                <tfoot>
                	<tr>
                    	<th colspan="4" class="text-center"><b>TOTAL</b></th>
                    	<th class="text-right"><?php echo number_format($total1); ?></th>
                    	<th colspan="11" class="text-center">&nbsp;</th><!-- <th colspan="11" class="text-center">&nbsp;</th> -->
                    	<th class="text-right"><?php echo number_format($total3); ?></th>
                    	<th class="text-right">&nbsp;</th>
                    	<th class="text-right"><?php echo number_format($total4); ?></th>
                        <th class="text-right">&nbsp;</th>
                    	<th class="text-right">&nbsp;</th>
                    </tr>
                </tfoot>
                <?php } ?>
			</table>
      	</div>
    </div>
</div>


<div class="row">
	<?php if($res && $res[0]['revert_cfo']){ ?>
    <div class="col-sm-6">
    	<div class="form-group">
            <label>Catatan Pengembalian CFO</label>
            <div class="form-control" style="height:auto"><?php echo ($res[0]['revert_cfo_summary']); ?></div>
		</div>
    </div>
	<?php } if($res && $res[0]['revert_ceo']){ ?>
    <div class="col-sm-6">
    	<div class="form-group">
            <label>Catatan Pengembalian CEO</label>
            <div class="form-control" style="height:auto"><?php echo ($res[0]['revert_ceo_summary']); ?></div>
		</div>
    </div>
	<?php } ?>
</div>
<?php 
	if($res && ($res[0]['revert_cfo'] || $res[0]['revert_ceo'])){
		echo '<hr style="border-top:4px double #ddd; margin:5px 0px 20px;" />'; 
		echo '<input type="hidden" name="dis_lo" value="1" />'; 
	}
?>

<div class="form-group row">
    <div class="col-sm-6">
        <label>Catatan BM</label>
        <div class="form-control" style="height:auto">
            <?php echo ($res[0]['sm_summary'] ?? ''); ?>
            <p style="margin:10px 0 0; font-size:12px;"><i><?php if ($res) { echo $res[0]['sm_pic']." - ".date("d/m/Y H:i:s", strtotime($res[0]['sm_tanggal']))." WIB"; } ?></i></p>
        </div>
    </div>
</div>

<div class="form-group row">
    <div class="col-sm-6">
        <label>Catatan Purchasing</label>
        <?php if(!$fnr){ ?>
        <textarea name="summary" id="summary" class="form-control"><?php if ($res) { echo str_replace("<br />", PHP_EOL, $res[0]['purchasing_summary']); } ?></textarea>
        <?php } else{ ?>
        <div class="form-control" style="height:auto">
            <?php echo ($res[0]['purchasing_summary'] ?? ''); ?>
            <p style="margin:10px 0 0; font-size:12px;"><i>
				<?php if ($res) { echo $res[0]['purchasing_pic']." - ".date("d/m/Y H:i:s", strtotime($res[0]['purchasing_tanggal']))." WIB"; } ?>
			</i></p>
        </div>
        <?php } ?>
    </div>
</div>

<?php if(count($res) > 0){ ?>
<div class="row">
    <div class="col-sm-12">
        <div class="pad bg-gray">
            <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
            <input type="hidden" name="idw" value="<?php echo $row[0]['id_wilayah']; ?>" />
            <input type="hidden" name="idg" value="<?php echo $row[0]['id_group']; ?>" />
            <input type="hidden" name="prnya" value="purchasing" />
            <input type="hidden" name="backadmin" value="0" />
            <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT."/purchase-request.php";?>">Kembali</a> 
            <?php if($fnr && $res[0]['sm_result'] == 1){ ?>
                <a class="btn btn-success" target="_blank" href="<?php echo BASE_URL_CLIENT.'/purchase-request-detail-exp.php?'.paramEncrypt('idr='.$idr); ?>" style="width:80px;">Export</a>
                <?php if ($row[0]['disposisi_pr'] != 5 and $row[0]['disposisi_pr'] != 6) { ?>
                <?php // if ($row[0]['disposisi_pr'] != 5) { ?>
                	<?php
                		// $isEdit = true;
                		// if($row[0]['disposisi_pr'] == 4 && $res[0]['is_ceo'])
                		// 	$isEdit = false;
                		// if ($isEdit) {
                		// if (!isset($enk['detail'])) {
                	?>
                		<button type="button" class="btn btn-warning" id="btnEdit"><i class="fa fa-edit jarak-kanan"></i>Edit</button>
                	<?php // } ?>
            	<?php } ?>
                <button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt" style="display: none;"><i class="fa fa-floppy-o jarak-kanan"></i>Submit</button>
                <button type="button" class="btn btn-default" id="btnCancel" style="display: none;"><i class="fa fa-times jarak-kanan"></i>Cancel</button>
            <?php } ?>
            <?php if(!$fnr && $res[0]['sm_result'] == 1){ ?>
            	<button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Submit</button>
            <?php } ?>
            <?php if( ($res[0]['revert_ceo'] || $res[0]['revert_cfo']) && $res[0]['sm_result'] == 1 ){ ?><button type="submit" class="btn btn-success" id="backadmin">Kembalikan ke Admin</button><?php } ?>
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
	.select2-search--dropdown .select2-search__field{
		font-family: arial;
		font-size: 11px;
		padding: 4px 3px;
	}
	.select2-results__option{
		font-family: arial;
		font-size: 11px;
	}
</style>
<script>
	$(document).ready(function(){
		$(".hitung").number(true, 0, ".", ",");
		$("#gform").find(".dp2").each(function(){
			if($(this).val() != ""){
				var elm = $(this);
				hitungNettProfit(elm);
			}
		});
		$("select.dp8").select2({
			placeholder	: "Pilih salah satu",
			allowClear	: true,
			templateResult : function(repo){ 
				if(repo.loading) return repo.text;
				var text1 = repo.text.split("#");
				var $returnString = $('<span>'+text1[0]+(text1[1]?' - '+text1[1]:'')+(text1[2]?'<br />'+text1[2]:'')+'</span>');
				return $returnString;
			},
			templateSelection : function(repo){ 
				var text1 = repo.text.split("#");
				var $returnString = $('<span>'+text1[0]+(text1[1]?' - '+text1[1]:'')+(text1[2]?', '+text1[2]:'')+'</span>');
				return $returnString;
			},
		});
		
		$("form#gform").on("click", "#btnSbmt", function(){
			if(confirm("Apakah anda yakin?")){
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
			} else return false;
		});

		$("form#gform").on("click", "#backadmin", function(){
			if(confirm("Apakah anda yakin?"))
			{
				$("#loading_modal").modal({backdrop:"static"});
				$('input[name="backadmin"]').val(1);
				$("form#gform").submit();
			} else return false;
		});

		$("#gform").on("change", "select.dp1, select.dp8", function(){
			var idnya = $(this).attr("id").substr(3);
			getHargaBeli(idnya);
		}).on("keyup", ".dp2", function(){
			var elm = $(this);
			hitungNettProfit(elm);
		});

		function getHargaBeli(newId) {
			var vendor 	= $("#dp1" + newId).val();
			var awal 	= $("#dp3" + newId).val();
			var akhir 	= $("#dp4" + newId).val();
			var area 	= $("#dp5" + newId).val();
			var produk 	= $("#dp6" + newId).val();
			var depot 	= $("#dp8" + newId).val();
			
			if (vendor != "" && awal != "" && akhir != "" && area != "" && produk != "" && depot != "") {
				$('#loading_modal').modal({backdrop: "static"});
				$.ajax({
					type	: 'POST',
					url		: "./__get_harga_tebus.php",
					data	: {q1: awal, q2: akhir, q3: produk, q4: area, q5: vendor, q6: depot},
					cache	: false,
					success : function(data) {
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

		function hitungNettProfit(elm) {
			var idx = elm.attr("id").split('dp2');
			var dt1 = $("#dp2"+idx[1]).val() * 1;
			var dt2 = $("#dp10"+idx[1]).val() * 1;
			var dt3 = $("#dp11"+idx[1]).val() * 1;
			var dtx = (dt3 - dt1) * dt2;
			$("#dp9"+idx[1]).val(dtx);
		}
	});		
    $('#btnEdit').on('click', function() {
        $('#btnSbmt').css('display', '')
        $('#btnCancel').css('display', '')
        $('#btnEdit').css('display', 'none')
        $('.divText').css('display', 'none')
        $('.divEdit').css('display', '')
    })
    $('#btnCancel').on('click', function() {
        $('#btnSbmt').css('display', 'none')
        $('#btnCancel').css('display', 'none')
        $('#btnEdit').css('display', '')
        $('.divText').css('display', '')
        $('.divEdit').css('display', 'none')
    })
</script>

<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$idr 	= isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
	$cek = "
	select a.id_po, a.id_pr, a.tanggal_po, a.id_transportir, a.catatan_transportir, b.nama_transportir, b.nama_suplier, c.nama_cabang, a.disposisi_po, a.po_approved, a.nomor_po, 
	d.nomor_pr, b.lokasi_suplier, a.ada_selisih, a.f_proses_selisih, a.catatan_selisih, a.catatan_selisih_mgrlog, a.selisih_approved, a.selisih_approved_mgrlog, a.id_wilayah 
	from pro_po a 
	join pro_master_transportir b on a.id_transportir = b.id_master 
	join pro_master_cabang c on a.id_wilayah = c.id_master 
	join pro_pr d on a.id_pr = d.id_pr 
	where a.id_po = '".$idr."'";
	$row = $con->getRecord($cek);

	$catatan 	= ($row['catatan_transportir'])?$row['catatan_transportir']:'&nbsp;';
	$linkCetak1	= ACTION_CLIENT.'/purchase-order-cetak.php?'.paramEncrypt('idr='.$idr);
	$linkCetak2	= ACTION_CLIENT.'/purchase-order-cetak-spj.php?'.paramEncrypt('idr='.$idr);
	$sesrol 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	$simpan 	= false;
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("myGrid", "jqueryUI", "formatNumber"), "css"=>array("jqueryUI"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1>Verifikasi Selisih OA Detail</h1>
        	</section>
			<section class="content">

				<?php if($enk['idr'] !== '' && isset($enk['idr'])){ ?>
				<?php $flash->display(); ?>
                <div class="row">                
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-body">
        
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                    	<div class="table-responsive">
                                            <table class="table no-border table-detail">
                                                <tr>
                                                    <td width="70">Kode PO</td>
                                                    <td width="10">:</td>
                                                    <td><?php echo $row['nomor_po']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td width="70">Kode PR</td>
                                                    <td width="10">:</td>
                                                    <td><?php echo $row['nomor_pr']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Tanggal</td>
                                                    <td>:</td>
                                                    <td><?php echo tgl_indo($row['tanggal_po']); ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Transportir</td>
                                                    <td>:</td>
                                                    <td><?php echo $row['nama_suplier'].
														($row['nama_transportir']?' - '.$row['nama_transportir']:'').
														($row['lokasi_suplier']?', '.$row['lokasi_suplier']:'');
													?></td>
                                                </tr>
                                            </table>
										</div>
									</div> 
                                </div> 
                
                                <div class="row">
                                    <div class="col-sm-12">
                                        <form action="<?php echo ACTION_CLIENT.'/evaluation-oa.php'; ?>" id="gform" name="gform" method="post" role="form">
                                            <div style="overflow-x: scroll" id="table-long">
                                                <div style="width:1640px; height:auto;">
                                                    <div class="table-responsive-satu">
                                                        <table class="table table-bordered" id="table-grid3">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center" width="100">Aksi</th>
                                                                    <th class="text-center" width="50">No</th>
                                                                    <th class="text-center" width="170">Customer</th>
                                                                    <th class="text-center" width="200">Area/ Alamat Kirim/ Wilayah OA</th>
                                                                    <th class="text-center" width="150">PO Customer</th>
                                                                    <th class="text-center" width="55">OA DR</th>
                                                                    <th class="text-center" width="55">OA</th>
                                                                    <th class="text-center" width="100">Plat No.</th>
                                                                    <th class="text-center" width="100">Driver</th>
                                                                    <th class="text-center" width="90">Tanggal Jam (ETA)</th>
                                                                    <th class="text-center" width="90">Tanggal Jam (ETL)</th>
                                                                    <th class="text-center" width="80">No. SPJ</th>
                                                                    <th class="text-center" width="120">Depot</th>
                                                                    <th class="text-center" width="50">Trip</th>
                                                                    <th class="text-center" width="50">Multi Drop</th>
                                                                    <th class="text-center" width="180">Keterangan</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php 
                                                                $sql = "select a.*, 
                                                                c.pr_pelanggan, c.produk, c.transport, e.status_jadwal, e.tanggal_kirim, 
                                                                f.harga_poc, f.nomor_poc, 
                                                                g.alamat_survey, g.id_wil_oa, g.jenis_usaha, g.id_lcr, 
                                                                h.nama_prov, 
                                                                i.nama_kab, 
                                                                j.nama_customer, j.id_customer, j.kode_pelanggan, 
                                                                k.fullname, 
                                                                n.nama_area, 
                                                                o.nama_terminal, o.tanki_terminal, o.lokasi_terminal, 
                                                                p.nama_vendor, 
                                                                q.nomor_plat, 
                                                                r.nama_sopir 
                                                                from pro_po_detail a
                                                                join pro_po b on a.id_po = b.id_po
                                                                join pro_pr_detail c on a.id_prd = c.id_prd 
                                                                join pro_pr d on c.id_pr = d.id_pr 
                                                                join pro_po_customer_plan e on c.id_plan = e.id_plan 
                                                                join pro_po_customer f on e.id_poc = f.id_poc 
                                                                join pro_customer_lcr g on e.id_lcr = g.id_lcr
                                                                join pro_master_provinsi h on g.prov_survey = h.id_prov 
                                                                join pro_master_kabupaten i on g.kab_survey = i.id_kab
                                                                join pro_customer j on f.id_customer = j.id_customer 
                                                                join acl_user k on j.id_marketing = k.id_user 
                                                                join pro_master_cabang l on j.id_wilayah = l.id_master 
                                                                join pro_penawaran m on f.id_penawaran = m.id_penawaran  
                                                                join pro_master_area n on m.id_area = n.id_master 
                                                                left join pro_master_terminal o on a.terminal_po = o.id_master 
                                                                left join pro_master_vendor p on c.pr_vendor = p.id_master 
                                                                left join pro_master_transportir_mobil q on a.mobil_po = q.id_master 
                                                                left join pro_master_transportir_sopir r on a.sopir_po = r.id_master 
                                                                where 
                                                                    a.id_po = '".$idr."' 
                                                                    -- and a.oa_flag = 1 
                                                                order by 
                                                                    a.pod_approved desc, 
                                                                    a.no_urut_po";
                                                                $res = $con->getResult($sql);
                                                                if(count($res) == 0){
                                                                    echo '<tr><td colspan="16" style="text-align:center">Data tidak ditemukan </td></tr>';
                                                                } else{
                                                                    $nom = 0;
                                                                    foreach($res as $data){
                                                                        $nom++;
                                                                        $idp = $data['id_pod'];
                                                                        $tempal = strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
                                                                        $alamat	= $data['alamat_survey']." ".ucwords($tempal)." ".$data['nama_prov'];
                                                                        $kirim	= date("d/m/Y", strtotime($data['tgl_kirim_po']));
                                                                        $tgleta	= date("d/m/Y", strtotime($data['tgl_eta_po']));
                                                                        $tgletl	= date("d/m/Y", strtotime($data['tgl_etl_po']));
                                                                        $jameta = $data['jam_eta_po'];
                                                                        $jametl = $data['jam_etl_po'];
                                                    
                                                                        $class1 = "form-control input-po noFormula text-center";
                                                                        $tmn1 	= ($data['nama_terminal'])?$data['nama_terminal']:'';
                                                                        $tmn2 	= ($data['tanki_terminal'])?'<br />'.$data['tanki_terminal']:'';
                                                                        $tmn3 	= ($data['lokasi_terminal'])?', '.$data['lokasi_terminal']:'';
                                                                        $depot 	= $tmn1.$tmn2.$tmn3;
                                                            ?>
                                                                <tr>
                                                                    <td>
                                                                    <?php 
                                                                        if($sesrol == '16'){
																			if(!$row['f_proses_selisih'] && $row['ada_selisih'] == '2'){
																				echo '<select name="cek['.$idp.']" id="cek'.$nom.'" class="cekAksi"><option></option>
																						<option value="1">Disetujui</option>
																						<option value="2">Ditolak</option>
																					  </select>';
																				echo '<input type="hidden" name="dt1['.$idp.']" value="'.$nom.'" />';
																				echo '<input type="hidden" name="dt2['.$idp.']" value="'.$data['terminal_po'].'" />';
																			} else if(!$row['f_proses_selisih'] && $row['ada_selisih'] == '1'){
																				if($data['oa_result_mgrlog'] == 0) 
																					echo '<p class="text-left">Disetujui Manager Logistik</p>';
																				else if($data['oa_result_mgrlog'] == '1') 
																					echo '<p class="text-left">Disetujui Manager Logistik</p>';
																				else if($data['oa_result_mgrlog'] == '2') 
																					echo '<p class="text-left">Ditolak Manager Logistik</p>';
																			} 
																			else{ 
																				echo ($data['oa_result'] == 1)?'<p class="text-center"><i class="fa fa-check"></i></p>':'&nbsp;';
																			}
																		}
																		
																		else if($sesrol == '3'){
																			if(!$row['f_proses_selisih']){
																				/*if($data['oa_result_mgrlog'] == '1') 
																					echo '<p class="text-left">Disetujui Manager Logistik</p>';
																				else if($data['oa_result_mgrlog'] == '2') 
																					echo '<p class="text-left">Ditolak Manager Logistik</p>';*/

																				echo '<select name="cek['.$idp.']" id="cek'.$nom.'" class="cekAksi"><option></option>
																						<option value="1" '.($data['oa_result_mgrlog'] == '1' ? 'selected' : '').'>Disetujui</option>
																						<option value="2" '.($data['oa_result_mgrlog'] == '2' ? 'selected' : '').'>Ditolak</option>
																					  </select>';
																				echo '<input type="hidden" name="dt1['.$idp.']" value="'.$nom.'" />';
																				echo '<input type="hidden" name="dt2['.$idp.']" value="'.$data['terminal_po'].'" />';
																			} 
																			else{ 
																				echo ($data['oa_result'] == 1)?'<p class="text-center"><i class="fa fa-check"></i></p>':'&nbsp;';
																			}
																		}
                                                                    ?></td>
                                                                    <td class="text-center"><?php echo $nom;?></td>
                                                                    <td>
                                                                        <p style="margin-bottom:0px"><b>
																			<?php echo ($data['kode_pelanggan'] ? $data['kode_pelanggan'].'<br/>':'').$data['nama_customer'];?>
																		</b></p>
                                                                        <p style="margin-bottom:0px"><i><?php echo $data['fullname'];?></i></p>
                                                                    </td>
                                                                    <td>
                                                                        <p style="margin-bottom:0px"><b><?php echo $data['nama_area'];?></b></p>
                                                                        <p style="margin-bottom:0px"><?php echo $alamat;?></p>
                                                                    </td>
                                                                    <td>
                                                                        <p style="margin-bottom:0px"><b><?php echo $data['nomor_poc'];?></b></p>
                                                                        <p style="margin-bottom:0px"><?php echo number_format($data['volume_po']).' Liter '.$data['produk'];?></p>
                                                                        <p style="margin-bottom:0px"><?php echo 'Tgl Kirim '.tgl_indo($data['tgl_kirim_po']);?></p>
                                                                    </td>
                                                                    <td class="text-right"><?php echo number_format($data['transport']);?></td>
                                                                    <td class="text-right"><?php echo number_format($data['ongkos_po']);?></td>
                                                                    <td class="text-center"><?php echo $data['nomor_plat'];?></td>
                                                                    <td class="text-center"><?php echo $data['nama_sopir'];?></td>
                                                                    <td class="text-center">
                                                                        <p style="margin-bottom:0px"><?php echo $tgleta;?></p>
                                                                        <p style="margin-bottom:0px"><?php echo $jameta;?></p>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <p style="margin-bottom:0px"><?php echo $tgletl;?></p>
                                                                        <p style="margin-bottom:0px"><?php echo $jametl;?></p>
                                                                    </td>
                                                                    <td class="text-center"><?php echo $data['no_spj']; ?></td>
                                                                    <td><?php echo $depot;?></td>
                                                                    <td class="text-center"><?php echo $data['trip_po']; ?></td>
                                                                    <td class="text-center"><?php echo $data['multidrop_po']; ?></td>
                                                                    <td class="text-center"><?php echo $data['ket_po']; ?></td>
                                                                </tr>
                                                            <?php } } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if($sesrol == 16){ $simpan = ($row['ada_selisih'] == '2') ? true : false; ?>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Catatan Manager Logistik</label>
                                                        <?php if(!$row['f_proses_selisih'] && $row['ada_selisih'] == '2'){ ?>
                                                        <textarea name="summary" id="summary" class="form-control"></textarea>
                                                        <?php } else{ ?>
                                                        <div class="form-control" style="height:auto">
                                                            <?php echo ($row['catatan_selisih_mgrlog']); ?>
                                                            <p style="margin:10px 0 0; font-size:12px;"><i>
															<?php echo date("d/m/Y H:i:s", strtotime($row['selisih_approved_mgrlog']))." WIB"; ?></i></p>
                                                        </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php } else if($sesrol == 3){ $simpan = (!$row['f_proses_selisih']) ? true : false; ?>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Catatan Manager Logistik</label>
                                                        <div class="form-control" style="height:auto">
                                                            <?php echo ($row['catatan_selisih_mgrlog'] ? $row['catatan_selisih_mgrlog'] : '&nbsp;'); ?>
                                                            <p style="margin:10px 0 0; font-size:12px;"><i>
															<?php echo ($row['selisih_approved_mgrlog']) ? date("d/m/Y H:i:s", strtotime($row['selisih_approved_mgrlog']))." WIB" : '&nbsp;'; ?></i></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Catatan CEO</label>
                                                        <?php if(!$row['f_proses_selisih']){ ?>
                                                        <textarea name="summary" id="summary" class="form-control"></textarea>
                                                        <?php } else{ ?>
                                                        <div class="form-control" style="height:auto">
                                                            <?php echo ($row['catatan_selisih']); ?>
                                                            <p style="margin:10px 0 0; font-size:12px;"><i>
															<?php echo date("d/m/Y H:i:s", strtotime($row['selisih_approved']))." WIB"; ?></i></p>
                                                        </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php } ?>
                                            
                                            <?php if(count($res) > 0){ ?>
                                            <p>&nbsp;</p>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                                                    <input type="hidden" name="idw" value="<?php echo $row['id_wilayah']; ?>" />
                                                    <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT."/verifikasi-oa.php";?>">Kembali</a> 
                                                    <?php if(!$row['f_proses_selisih'] && $simpan == true){ ?>
                                                    	<button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt">Submit</button>
													<?php } ?>
                                                </div>
                                            </div>
                                            <?php } ?>


                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            <?php } ?>
            
            <div class="modal fade" id="preview_modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-blue">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Peringatan</h4>
                        </div>
                        <div class="modal-body">
                        	<div id="preview_alert" class="text-center"></div>
						</div>
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
			<?php $con->close(); ?>
			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
	</div>

<style type="text/css">
	h3.form-title {
		 font-size: 18px;
		 margin: 0 0 10px;
		 font-weight: 700;
	}
	#table-long, #table-grid3, .table-detail {margin-bottom: 15px;}
	#table-grid3 > tbody > tr > td, 
	#table-grid3 > thead > tr > th {
		font-size: 11px; 
		font-family: arial;
	}
	.table-detail > thead > tr > th, 
	.table-detail > tbody > tr > td { 
		padding:5px; 
		font-size: 12px;
	}
	.input-po {
		padding: 5px;
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
		$(".cekAksi").select2({placeholder:"Aksi", allowClear:true});

		$("form#gform").on("click", "button:submit", function(){
			var terus = true;
			if(confirm("Apakah anda yakin?")){
				$("#loading_modal").modal({backdrop:"static"});
				$(".cekAksi").each(function(){
					if($(this).val() == "") terus = false;
				});
				if(!terus){
					$("#preview_modal").find("#preview_alert").html('Kolom aksi masih ada yang kosong');
					$("#preview_modal").modal();
					$("#loading_modal").modal("hide");					
					return false;
				} else{
					$("button[type='submit']").addClass("disabled");
					$("#gform").submit();
				}
				return false;
			} else return false;
		});

		var x,y,top,left,down;
		$("#table-long").mousedown(function(e){
			if(e.target.nodeName != "INPUT" && e.target.nodeName != "SELECT"){
				down = true;
				x = e.pageX;
				y = e.pageY;
				top = $(this).scrollTop();
				left = $(this).scrollLeft();
			}
		});			
		$("body").mousemove(function(e){
			if(down){
				var newX = e.pageX;
				var newY = e.pageY;
				$("#table-long").scrollTop(top-newY+y);    
				$("#table-long").scrollLeft(left-newX+x);    
			}
		});
		$("body").mouseup(function(e){down=false;});
	});		
</script>
</body>
</html>      

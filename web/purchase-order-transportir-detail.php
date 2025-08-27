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
	$cek = "select a.*, b.nama_transportir, b.nama_suplier, b.lokasi_suplier, c.nama_cabang from pro_po a join pro_master_transportir b on a.id_transportir = b.id_master 
			join pro_master_cabang c on a.id_wilayah = c.id_master where a.id_po = '".$idr."'";
	$row = $con->getRecord($cek);
	$catatan = ($row['catatan_transportir'])?str_replace("<br />", PHP_EOL, $row['catatan_transportir']):'';
	if($row['po_approved'] && $row['is_new']){
		$sqlCek = "update pro_po set is_new = 0 where id_po = '".$idr."'";
		$con->setQuery($sqlCek);
		$con->clearError();
	}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("myGrid"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1>Purchase Order Detail</h1>
        	</section>
			<section class="content">

				<?php if($enk['idr'] !== '' && isset($enk['idr'])){ ?>
				<?php $flash->display(); ?>
                <div class="row">                
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-body">
        
                                <table border="0" cellpadding="0" cellspacing="0" id="table-detail">
                                    <tr>
                                        <td width="70">Kode PO</td>
                                        <td width="10">:</td>
                                        <td><?php echo $row['nomor_po']; ?></td>
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
                                <div class="row">
                                    <div class="col-sm-12">
                                        <form action="<?php echo ACTION_CLIENT.'/purchase-order-transportir.php'; ?>" id="gform" name="gform" method="post" role="form">
                                        <div style="overflow-x: scroll" id="table-long"><div style="width:1725px; height:auto; min-height:280px;">
                                        <div class="table-responsive-satu">
                                            <table class="table table-bordered" id="table-grid">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" width="50">No</th>
                                                        <th class="text-center" width="170">Customer</th>
                                                        <th class="text-center" width="200">Area/ Alamat Kirim/ Wilayah OA</th>
                                                        <th class="text-center" width="150">PO Customer</th>
                        								<th class="text-center" width="55">OA</th>
                                                        <th class="text-center" width="140">Plat No.</th>
                                                        <th class="text-center" width="150">Driver</th>
                                                        <th class="text-center" width="90">Tanggal Jam (ETA)</th>
                                                        <th class="text-center" width="90">Tanggal Jam (ETL)</th>
                                                        <th class="text-center" width="80">No. SPJ</th>
                                                        <th class="text-center" width="120">Depot</th>
                                                        <th class="text-center" width="50">Trip</th>
                                                        <th class="text-center" width="50">Multi Drop</th>
                                                        <th class="text-center" width="180">Keterangan</th>
                                                        <th class="text-center" width="150">Catatan Marketing</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php 
													$sql = "select a.*, 
													c.pr_pelanggan, c.produk, e.status_jadwal, 
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
													r.nama_sopir, s.wilayah_angkut 
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
													left join pro_master_wilayah_angkut s on g.id_wil_oa = s.id_master and g.prov_survey = s.id_prov and g.kab_survey = s.id_kab 
													where a.id_po = '".$idr."' order by a.pod_approved desc, a.no_urut_po";
                                                    $res = $con->getResult($sql);
                                                    if(count($res) == 0){
                                                        echo '<tr><td colspan="15" style="text-align:center">Data tidak ditemukan </td></tr>';
                                                    } else{
                                                        $nom = 0;
                                                        foreach($res as $data){
                                                            $nom++;
                                                            $idp = $data['id_pod'];
															$tempal = strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
															$alamat	= $data['alamat_survey']." ".ucwords($tempal)." ".$data['nama_prov'];
                                                            $tmpeta = (tgl_db($data['tgl_eta_po']))?date("d/m/Y", strtotime($data['tgl_eta_po'])):'';
															$tgleta	= $tmpeta." ".$data['jam_eta_po'];
                                                            $tgletl = date("d/m/Y", strtotime($data['tgl_etl_po']))." ".$data['jam_etl_po'];

															$mobil 	= $data['mobil_po'];
															$sopir 	= $data['sopir_po'];
										
															$where1 = "where is_active = 1 and id_transportir = '".$row['id_transportir']."'";
								
															$tmn1 	= ($data['nama_terminal'])?$data['nama_terminal']:'';
															$tmn2 	= ($data['tanki_terminal'])?'<br />'.$data['tanki_terminal']:'';
															$tmn3 	= ($data['lokasi_terminal'])?', '.$data['lokasi_terminal']:'';
															$depot 	= $tmn1.$tmn2.$tmn3;
                                                ?>
                                                    <tr>
                                                        <td class="text-center">
														<?php 
															echo '<input type="hidden" name="ext_id_lcr['.$idp.']" value="'.$data['id_lcr'].'" />';
															echo '<input type="hidden" name="dt1['.$idp.']" id="dt1'.$nom.'" value="'.$nom.'" />'.$nom;
														?></td>
                                                        <td>
                                                            <p style="margin-bottom:0px">
                                                            <b><?php echo ($data['kode_pelanggan'] ? $data['kode_pelanggan'].'<br/>':'').$data['nama_customer'];?></b></p>
                                                            <p style="margin-bottom:0px"><i><?php echo $data['fullname'];?></i></p>
                                                            <p style="margin-bottom:0px">
                                                            <a style="cursor:pointer" class="detLcr" data-idnya="<?php echo $data['id_lcr'];?>">Detil</a></p>
                                                        </td>
                                                        <td>
                                                            <p style="margin-bottom:0px"><b><?php echo $data['nama_area'];?></b></p>
                                                            <p style="margin-bottom:0px"><?php echo $alamat;?></p>
                                                            <p style="margin-bottom:0px"><?php echo 'Wilayah OA : '.$data['wilayah_angkut'];?></p>
                                                        </td>
                                                        <td>
                                                            <p style="margin-bottom:0px"><b><?php echo $data['nomor_poc'];?></b></p>
                                                            <p style="margin-bottom:0px"><?php echo number_format($data['volume_po']).' Liter '.$data['produk'];?></p>
                                                            <p style="margin-bottom:0px"><?php echo 'Tgl Kirim '.tgl_indo($data['tgl_kirim_po']);?></p>
                                                        </td>
                                                        <td class="text-right"><?php echo number_format($data['ongkos_po']); ?></td>
                                                        <td class="text-center">
                                                        <?php 
                                                            if(!$row['po_approved'] && $row['disposisi_po'] == 2){
                                                                echo '<select name="dt2['.$idp.']" id="dt2'.$nom.'" class="input-po form-control select2"><option></option>';
                                                                $con->fill_select("id_master", "nomor_plat", "pro_master_transportir_mobil", $mobil, $where1, "", false);
                                                                echo '</select>';
                                                            } else{
                                                                echo '<input type="hidden" name="dt2['.$idp.']" value="'.$mobil.'" />'.$data['nomor_plat'];
                                                            }
                                                            echo '<p style="margin:5px 0 0;"><a style="cursor:pointer" class="detTruck" data-idnya="'.$idp.'">Detil</a></p>';
                                                        ?></td>
                                                        <td class="text-center">
                                                        <?php 
                                                            if(!$row['po_approved'] && $row['disposisi_po'] == 2){
                                                                echo '<select name="dt3['.$idp.']" id="dt3'.$nom.'" class="input-po form-control select2"><option></option>';
                                                                $con->fill_select("id_master", "nama_sopir", "pro_master_transportir_sopir", $sopir, $where1, "", false);
                                                                echo '</select>';
                                                            } else{
                                                                echo '<input type="hidden" name="dt3['.$idp.']" value="'.$sopir.'" />'.$data['nama_sopir'];
                                                            }
                                                        ?></td>
                                                        <td class="text-center"><?php echo $tgleta;?></td>
                                                        <td class="text-center"><?php echo $tgletl;?></td>
                                                        <td class="text-center"><?php echo $data['no_spj'];?></td>
                                                        <td><?php echo $depot; ?></td>
                                                        <td class="text-center"><?php echo $data['trip_po']; ?></td>
                                                        <td class="text-center"><?php echo $data['multidrop_po']; ?></td>
                                                        <td><?php echo $data['ket_po']; ?></td>
                                                        <td><?php echo $data['status_jadwal']; ?></td>
                                                    </tr>
                                                <?php } } ?>
                                                </tbody>
                                            </table>
                                        </div></div></div>

                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>Catatan</label>
                                                <?php if(!$row['po_approved'] && $row['disposisi_po'] == 2){ ?>
                                                <textarea name="summary" id="summary" class="form-control"><?php echo $catatan; ?></textarea>
                                                <?php } else{ ?>
                                                <div class="form-control" style="height:auto"><?php echo ($catatan)?$row['catatan_transportir']:'&nbsp;'; ?></div>
                                                <?php } ?>
                                            </div>
                                        </div>

										<?php if(count($res) > 0){ ?>
                                        <hr style="margin:0 0 10px" />
                                        <div class="form-group row">
                                            <div class="col-sm-12">
                                                <div class="pad bg-gray">
                                                    <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                                                    <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT."/purchase-order-transportir.php";?>">Kembali</a> 
                                                    <?php if(!$row['po_approved'] && $row['disposisi_po'] == 2){ ?>
                                                    <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt1" id="btnSbmt1" value="1" data-click="0">
                                                    <i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
													<?php } ?>
                                                </div>
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
            <div class="modal fade" id="lcr_modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-blue">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Informasi</h4>
                        </div>
                        <div class="modal-body"></div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="truck_modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-blue">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Informasi</h4>
                        </div>
                        <div class="modal-body"></div>
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
	#table-long, #table-grid, #table-detail {margin-bottom: 15px;}
	#table-grid > tbody > tr > td, 
	#table-grid > thead > tr > th {
		font-size: 11px; 
		font-family: arial;
	}
	#table-detail > tbody > tr > td { 
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
		$("form#gform").on("click", "button:submit", function(){
			if(confirm("Apakah anda yakin?")){
				$("#loading_modal").modal({backdrop:"static"});
				$("button[type='submit']").addClass("disabled");
				$("#gform").submit();
			} else return false;
		});

		$("#gform").on("click", "a.detLcr", function(){
			var cRow = $(this).data('idnya');
			$.ajax({
				type	: 'POST',
				url		: "./__get_info_lcr_customer.php",
				data	: {q1:cRow},
				cache	: false,
				success : function(data){
					$("#lcr_modal").find(".modal-body").html(data);
					$("#lcr_modal").modal();
				}
			});
		});

		$("#gform").on("click", "a.detTruck", function(){
			var cRow = $(this).data('idnya');
			$.ajax({
				type	: 'POST',
				url		: "./__get_info_truck_transportir.php",
				data	: {q1:$("select[name='dt2["+cRow+"]']").val(), q2:$("input[name='dt2["+cRow+"]']").val(), q3:$("input[name='ext_id_lcr["+cRow+"]']").val()},
				cache	: false,
				success : function(data){
					$("#truck_modal").find(".modal-body").html(data);
					$("#truck_modal").modal();
				}
			});
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

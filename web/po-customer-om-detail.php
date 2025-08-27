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
        		<h1>Persetujuan PO ke PR</h1>
        	</section>
			<section class="content">

				<?php if($enk['idr'] !== '' && isset($enk['idr'])){ ?>
				<?php $flash->display(); ?>
                <div class="row">                
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?php echo "POPR".str_pad($idr,4,'0',STR_PAD_LEFT); ?></h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <form action="<?php echo ACTION_CLIENT.'/po-customer-om.php'; ?>" id="gform" name="gform" method="post" role="form">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="table-grid2">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" width="5%">No</th>
                                                        <th class="text-center" width="20%">Kode dan Nama Customer</th>
                                                        <th class="text-center" width="30%">Area/ Alamat Kirim/ Wilayah OA</th>
                                                        <th class="text-center" width="17%">Nomor PO Customer</th>
                                                        <th class="text-center" width="13%">Tgl dan Volume Kirim</th>
                                                        <th class="text-center" width="10%">Status</th>
                                                        <th class="text-center" width="5%"><input type="checkbox" name="cekAll" id="cekAll" value="1" /></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php 
													$sql = "select a.*, c.tanggal_kirim, c.volume_kirim, c.is_urgent, g.id_customer, d.alamat_survey, e.nama_prov, 
															f.nama_kab, h.nama_customer, i.fullname, c.id_plan, b.catatan_logistik, l.nama_area, h.kode_pelanggan, 
															m.jenis_produk, m.merk_dagang, n.wilayah_angkut, g.nomor_poc, g.lampiran_poc, g.lampiran_poc_ori, g.id_poc 
															from pro_po_customer_om_detail a 
															join pro_po_customer_om b on a.id_ppco = b.id_ppco 
															join pro_po_customer_plan c on a.id_plan = c.id_plan 
															join pro_customer_lcr d on c.id_lcr = d.id_lcr
															join pro_master_provinsi e on d.prov_survey = e.id_prov 
															join pro_master_kabupaten f on d.kab_survey = f.id_kab
															join pro_po_customer g on c.id_poc = g.id_poc 
															join pro_customer h on g.id_customer = h.id_customer 
															join acl_user i on h.id_marketing = i.id_user 
															join pro_master_cabang j on h.id_wilayah = j.id_master 
															join pro_penawaran k on g.id_penawaran = k.id_penawaran  
															join pro_master_area l on k.id_area = l.id_master 
															join pro_master_produk m on g.produk_poc = m.id_master 
															join pro_master_wilayah_angkut n on d.id_wil_oa = n.id_master and d.prov_survey = n.id_prov and d.kab_survey = n.id_kab
															where a.id_ppco = '".$idr."'";
													$res = $con->getResult($sql);
													if(count($res) == 0){
														echo '<tr><td colspan="7" style="text-align:center">Data tidak ditemukan </td></tr>';
													} else{
                                                        $buttonApprove = false;
														$nom = 0;
														foreach($res as $data){
															$nom++;
															$idp	= $data['id_plan'];
															$tempal = strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
															$alamat	= $data['alamat_survey']." ".ucwords($tempal)." ".$data['nama_prov'];
															$arPenting = array("normal", "utama");
															$arApprove = array(0=>"", 1=>"Approved", 2=>"Re-Schedule");

															$pathPt = $public_base_directory.'/files/uploaded_user/lampiran/'.$data['lampiran_poc'];
															$lampPt = $data['lampiran_poc_ori'];
															if($data['lampiran_poc'] && file_exists($pathPt)){
																$linkPt = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=2&ktg=POC_".$data['id_poc']."_&file=".$lampPt);
																$attach = '<a href="'.$linkPt.'"><i class="fa fa-file-alt" title="'.$lampPt.'"></i></a>';
															} else {$attach = '';}
                                                            if ($data['om_result']!=1 and $data['om_result']!=2) $buttonApprove = true

												?>
                                                	<tr>
                                                    	<td class="text-center"><?php echo $nom; ?></td>
                                                    	<td>
                                                            <p style="margin-bottom:0px">
                                                            	<b><?php echo ($data['kode_pelanggan'] ? $data['kode_pelanggan'].' - ':'').$data['nama_customer'];?></b>                                                            </p>
                                                            <p style="margin-bottom:0px;"><i><?php echo $data['fullname']; ?></i></p>
                                                        </td>
                                                        <td>
                                                            <p style="margin-bottom:0px"><b><?php echo $data['nama_area'];?></b></p>
                                                            <p style="margin-bottom:0px"><?php echo $alamat;?></p>
                                                            <p style="margin-bottom:0px"><?php echo 'Wilayah OA : '.$data['wilayah_angkut'];?></p>                                                        </td>
                                                    	<td>
															<p style="margin-bottom:0px;"><b><?php echo $data['nomor_poc']; ?></b></p>
                                                            <p style="margin-bottom:0px;"><?php echo $data['merk_dagang']; ?></p>
                                                            <p style="margin-bottom:0px;"><?php echo $attach; ?></p>
														</td>
                                                    	<td>
															<p style="margin-bottom:0px;"><?php echo tgl_indo($data['tanggal_kirim']); ?></p>
                                                            <p style="margin-bottom:0px;"><?php echo number_format($data['volume_kirim'])." Liter"; ?></p>
														</td>
                                                    	<td class="text-center"><?php echo $arApprove[$data['om_result']]; ?></td>
                                                    	<td class="text-center">
                                                        	<?php if(!$data['om_result']){ ?>
                                                            <input type="checkbox" name="<?php echo "cek[".$idp."]"; ?>" id="<?php echo "cek".$nom;?>" class="chkp" value="1" />
                                                            <?php } ?>
                                                        </td>
													</tr>
                                                <?php } } ?>
                                                </tbody>
                                            </table>
                                        </div>
										<?php if(count($res) > 0){ ?>
                                        <hr style="margin:0 0 10px" />
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>Catatan Logistik</label>
                                                <div class="form-control" style="<?php echo $data['catatan_logistik']?'height:auto;':''; ?>"><?php echo $data['catatan_logistik']; ?></div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="pad bg-gray">
                                                    <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                                                    <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT."/po-customer-om.php";?>">Kembali</a>
                                                    <?php if ($buttonApprove) { ?>
                                                    <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt1" id="btnSbmt1" value="1">Approve</button>
                                                    <button type="submit" class="btn btn-danger jarak-kanan" name="btnSbmt2" id="btnSbmt2" value="1">Re-schedule</button>
                                                    <?php }?>
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
            <div class="modal fade" id="preview_modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-blue">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Peringatan</h4>
                        </div>
                        <div class="modal-body"><div id="preview_alert" class="text-center"></div></div>
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
	#table-grid2 {margin-bottom: 15px;}
	#table-grid2 td, #table-grid2 th {font-size: 12px;}
</style>
<script>
	$(document).ready(function(){
		$("form#gform").on("click", "button:submit", function(){
			if(confirm("Apakah anda yakin?")){
				if($("#gform").find("input:checked").length > 0){
					$("#loading_modal").modal({backdrop:"static"});
					$("#gform").submit();
				} else{
					$("#preview_modal").find("#preview_alert").text("Data Po Belum dipilih..");
					$("#preview_modal").modal();					
					return false;
				}
			} else return false;
		});
		$("#cekAll").on("ifChecked", function(){
			$(".chkp").iCheck("check");
		}).on("ifUnchecked", function(){
			$(".chkp").iCheck("uncheck");
		});
	});		
</script>
</body>
</html>      

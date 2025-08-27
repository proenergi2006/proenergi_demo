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
	$idk 	= htmlspecialchars($enk["idk"], ENT_QUOTES);
	$sesrol = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	$arrRol = array(7=>"BM", 6=>"OM", 17=>"Key Account");

	$sql = "select a.*, b.nama_customer, b.id_wilayah, c.nomor_surat, c.perhitungan, c.harga_dasar, c.detail_formula, c.volume_tawar, e.jenis_produk, e.merk_dagang 
			from pro_po_customer a 
			join pro_customer b on a.id_customer = b.id_customer 
			join pro_penawaran c on a.id_penawaran = c.id_penawaran 
			join pro_master_produk e on a.produk_poc = e.id_master 
			where a.id_customer = '".$idr."' and a.id_poc = '".$idk."'";
	$rsm = $con->getRecord($sql);
	$formula = json_decode($rsm['detail_formula'], true);
	if($rsm['perhitungan'] == 1){
		$harganya = number_format($rsm['harga_dasar']);
		$nilainya = $rsm['harga_dasar'];
	} else{
		$harganya = '';
		$nilainya = '';
		foreach($formula as $jenis){
			$harganya .= '<p style="margin-bottom:0px">'.$jenis.'</p>';
		}
	} 

	$link1 = BASE_URL_CLIENT.'/verifikasi-poc.php';
	$pathPt = $public_base_directory.'/files/uploaded_user/lampiran/'.$rsm['lampiran_poc'];
	$lampPt = $rsm['lampiran_poc_ori'];

	if($rsm['poc_approved'] == 1)
		$disposisi = 'Terverifikasi '.$arrRol[$sesrol].' '.date("d/m/Y H:i:s", strtotime($rsm['tgl_approved'])).' WIB';
	else if($rsm['poc_approved'] == 2)
		$disposisi = 'Ditolak '.$arrRol[$sesrol];
	else if($rsm['disposisi_poc'] == 0)
		$disposisi = 'Terdaftar';
	else if($rsm['disposisi_poc'] == 1)
		$disposisi = 'Verifikasi '.$arrRol[$sesrol];
	else if($rsm['disposisi_poc'] == 2)
		$disposisi = 'Verifikasi OM';
	else $disposisi = '';

	$arr_payment = array("COD"=>"COD (Cash On Delivery)","CBD"=>"CBD (Cash Before Delivery)");
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1>Detil PO Customer</h1>
        	</section>
			<section class="content">

				<?php if($enk['idr'] !== '' && isset($enk['idr'])){ ?>
				<?php $flash->display(); ?>
                <div class="row">                
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Data PO Customer</h3>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                	<table class="table">
                                        <thead>
                                        	<tr>
                                            	<th colspan="3"><?php echo "Kode Dokumen PO-".str_pad($rsm['id_poc'],4,'0',STR_PAD_LEFT);?></th>
											</tr>
                                        </thead>
                                        <tr>
                                        	<td width="180">Nama Customer</td>
                                        	<td width="10">:</td>
                                        	<td><?php echo $rsm['nama_customer'];?></td>
                                        </tr>
                                    	<tr>
                                        	<td>TOP Customer</td>
                                        	<td>:</td>
                                        	<td><?php echo (is_numeric($rsm['top_poc']))?$rsm['top_poc']." Hari":$arr_payment[$rsm['top_poc']];?></td>
                                        </tr>
                                    	<tr>
                                        	<td>Penawaran</td>
                                        	<td>:</td>
                                        	<td><?php echo $rsm['nomor_surat'];?></td>
                                        </tr>
                                    	<tr>
                                        	<td>Volume Penawaran</td>
                                        	<td>:</td>
                                        	<td><?php echo number_format($rsm['volume_tawar']).' Liter';?></td>
                                        </tr>
                                    	<tr>
                                        	<td>Harga Penawaran</td>
                                        	<td>:</td>
                                        	<td><?php echo $harganya;?></td>
                                        </tr>
                                    	<tr>
                                        	<td colspan="3">&nbsp;</td>
                                        </tr>
                                    	<tr>
                                        	<td>Nomor PO</td>
                                        	<td>:</td>
                                        	<td><?php echo $rsm['nomor_poc'];?></td>
                                        </tr>
                                    	<tr>
                                        	<td>Tanggal PO</td>
                                        	<td>:</td>
                                        	<td><?php echo tgl_indo($rsm['tanggal_poc']);?></td>
                                        </tr>
                                    	<tr>
                                        	<td>Produk</td>
                                        	<td>:</td>
                                        	<td><?php echo $rsm['jenis_produk']." - ".$rsm['merk_dagang'];?></td>
                                        </tr>
                                    	<tr>
                                        	<td>Total Order</td>
                                        	<td>:</td>
                                        	<td><?php echo number_format($rsm['volume_poc'])." Liter";?></td>
                                        </tr>
                                    	<tr>
                                        	<td>Harga/Liter</td>
                                        	<td>:</td>
                                        	<td><?php echo number_format($rsm['harga_poc']);?></td>
                                        </tr>
                                    	<tr>
                                        	<td>Lampiran</td>
                                        	<td>:</td>
                                        	<td>
											<?php
                                    			if($rsm['lampiran_poc'] && file_exists($pathPt)){
                                        			$linkPt = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=2&ktg=POC_".$idk."_&file=".$lampPt);
                                        			echo '<a href="'.$linkPt.'"><i class="fa fa-file-alt jarak-kanan"></i>'.$lampPt.'</a>';
												} else echo '-';
											?></td>
                                        </tr>
                                    	<tr>
                                        	<td>Disposisi</td>
                                        	<td>:</td>
                                        	<td><?php echo $disposisi;?></td>
                                        </tr>
                                    </table>
								</div>
                                
                                <form action="<?php echo ACTION_CLIENT.'/evaluation-poc.php'; ?>" id="gform" name="gform" method="post" role="form">
                                    <?php if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == -1){ ?>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label>Catatan BM</label>
                                            <div class="form-control" style="height:auto">
                                                <?php echo ($rsm['sm_summary']); ?>
                                                <p style="margin:10px 0 0; font-size:12px;"><i>
													<?php echo $rsm['sm_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['sm_tanggal']))." WIB"; ?></i>
												</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label>Catatan OM</label>
                                            <?php if(!$rsm['om_result']){ ?>
                                            <textarea name="summary" id="summary" class="form-control"></textarea>
                                            <?php } else{ ?>
                                            <div class="form-control" style="height:auto">
                                                <?php echo ($rsm['om_summary']); ?>
                                                <p style="margin:10px 0 0; font-size:12px;"><i>
													<?php echo $rsm['om_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['om_tanggal']))." WIB"; ?></i>
												</p>
                                            </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php } else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 7 || paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 6){ ?>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label><?php echo 'Catatan '.$arrRol[$sesrol];?></label>
                                            <?php if(!$rsm['sm_result']){ ?>
                                            <textarea name="summary" id="summary" class="form-control"></textarea>
                                            <?php } else{ ?>
                                            <div class="form-control" style="height:auto">
                                                <?php echo ($rsm['sm_summary']); ?>
                                                <p style="margin:10px 0 0; font-size:12px;"><i>
													<?php echo $rsm['sm_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['sm_tanggal']))." WIB"; ?></i>
												</p>
                                            </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php } ?> 
											 
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="pad bg-gray">
                                                <input type="hidden" name="idr" value="<?php echo $idr;?>" />
                                                <input type="hidden" name="idk" value="<?php echo $idk;?>" />
                                                <input type="hidden" name="cabang" value="<?php echo $rsm['id_wilayah'];?>" />
                                                <a href="<?php echo $link1; ?>" class="btn btn-default jarak-kanan"><i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                                                <?php if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == -1 && !$rsm['om_result']){ ?>
                                                <button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Submit</button>
                                                <?php } else if(in_array(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']), array("6","7")) && !$rsm['sm_result']){ ?>
                                                <input type="hidden" name="setuju" id="setuju" value="" />
                                                <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt1" id="btnSbmt1" value="1">
                                                <i class="fa fa-floppy-o jarak-kanan"></i>Submit</button>
                                                <button type="submit" class="btn btn-danger jarak-kanan" name="btnSbmt2" id="btnSbmt2" value="2">
                                                <i class="fa fa-times jarak-kanan"></i>Tolak</button>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                
                                
                            </div>
                        </div>
                    </div>
                </div>




            <?php } ?>
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

<script>
	$(document).ready(function(){
		$("button:submit").on("click", function(){
			$("#setuju").val($(this).val());
		});
		$("form#gform").validationEngine('attach',{
			onValidationComplete: function(form, status){
				if(status == true){
					if(confirm("Apakah anda yakin?")){
						$('#loading_modal').modal({backdrop:"static"});
						form.validationEngine('detach');
						form.submit();
					} else return false;
				}
			}
		});
	});		
</script>
</body>
</html>      

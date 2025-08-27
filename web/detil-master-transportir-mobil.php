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
	$idr = isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
	$sql = "select a.*, b.nama_suplier, b.nama_transportir, b.lokasi_suplier from pro_master_transportir_mobil a join pro_master_transportir b on a.id_transportir = b.id_master 
			where a.id_master = '".$idr."';";
	$rsm = $con->getRecord($sql);
	$link1 	= BASE_URL_CLIENT.'/master-transportir-mobil.php';
	$link2 	= BASE_URL_CLIENT.'/add-master-transportir-mobil.php?'.paramEncrypt('idr='.$idr);
	$pathPt = $public_base_directory.'/files/uploaded_user/lampiran/'.$rsm['photo'];
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("ckeditor"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1>Detil Truck Transportir</h1>
        	</section>
			<section class="content">

				<?php if($enk['idr'] !== '' && isset($enk['idr'])){ $flash->display(); ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-info">
                            <div class="box-body">
                            	<?php
									if($rsm['photo'] && file_exists($pathPt)){
										$urliPt = BASE_URL.'/files/uploaded_user/lampiran/'.$rsm['photo'];
										$linkPt = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=2&ktg=pics_".$idr."_&file=".$rsm['photo_ori']);
										echo '<div class="text-center"><a href="'.$linkPt.'"><img src="'.$urliPt.'" title="'.$rsm['photo_ori'].'" style="width:25%" /></a></div>';
									}
								?>
                                <div class="table-responsive">
                                	<table class="table no-border">
                                    	<tr>
                                        	<td width="150">Nomor Plat</td>
                                        	<td width="10">:</td>
                                        	<td><?php echo $rsm['nomor_plat'];?></td>
                                        </tr>
                                    	<tr>
                                        	<td>Transportir</td>
                                        	<td>:</td>
                                        	<td><?php echo $rsm['nama_suplier'].' - '.$rsm['nama_transportir'].', '.$rsm['lokasi_suplier'];?></td>
                                        </tr>
                                    	<tr>
                                        	<td>Kapasitas Max</td>
                                        	<td>:</td>
                                        	<td><?php echo ($rsm['max_kap']?$rsm['max_kap'].' KL':'-');?></td>
                                        </tr>
                                    </table>
                                </div>

                                <h3 class="form-title">KOMPARTEMEN</h3>
                                <div class="row">
                                	<div class="col-sm-10 col-md-8">
                                		<div class="table-responsive">
                                            <table class="table table-bordered" style="margin-bottom:30px;">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" width="50%">Kompartemen</th>
                                                        <th class="text-center" width="50%">Tanki</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php 
                                                        $kompar = json_decode($rsm['komp_tanki'], true);
                                                        if(count($kompar) == 0){
                                                            echo '<tr><td colspan="2" class="text-center">Tidak ada kompartemen</td></tr>';
                                                        } else{
                                                            foreach($kompar as $dat3){
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $dat3['kompart'];?></td>
                                                        <td><?php echo $dat3['tanki'];?></td>
                                                    </tr>
                                                    <?php } } ?>
                                        		</tbody>
											</table>
                                        </div>
                                    </div>
                                </div>

                                <h3 class="form-title">GPS</h3>
                                <div class="table-responsive">
                                	<table class="table table-bordered" style="margin-bottom:30px;">
                                        <thead>
                                            <tr>
                                                <th class="text-center" width="45%">Link GPS</th>
                                                <th class="text-center" width="20%">Username</th>
                                                <th class="text-center" width="20%">Password</th>
                                                <th class="text-center" width="15%">Member Code</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><?php echo ($rsm['link_gps']?$rsm['link_gps']:'&nbsp;'); ?></td>
                                                <td><?php echo ($rsm['user_gps']?$rsm['user_gps']:'&nbsp;'); ?></td>
                                                <td><?php echo ($rsm['pass_gps']?$rsm['pass_gps']:'&nbsp;'); ?></td>
                                                <td><?php echo ($rsm['membercode_gps']?$rsm['membercode_gps']:'&nbsp;'); ?></td>
                                            </tr>
                                        </tbody>
									</table>
                                </div>

                                <h3 class="form-title">DOKUMEN</h3>
                                <div class="table-responsive">
                                	<table class="table table-bordered">
                                    	<thead>
                                            <tr>
                                                <th class="text-center" width="35%">Dokumen</th>
                                                <th class="text-center" width="15%">Masa Berlaku</th>
                                                <th class="text-center" width="50%">Lampiran</th>
                                            </tr>
                                        </thead>
                                        <tbody>
										<?php 
                                            $cek1 = "select * from pro_master_transportir_mobil_detail where id_transportir_mobil = '".$idr."' order by id_tmd";
                                            $row1 = $con->getResult($cek1);
                                            if(count($row1) == 0){
                                                echo '<tr><td colspan="3" class="text-center">Tidak ada dokumen</td></tr>';
                                            } else{
                                                $d=0;
                                                foreach($row1 as $dat1){
                                                    $d++;
                                                    $idd 	= $dat1['id_tmd'];
													$linkAt = "";
													$textAt = "";
													$pathAt = $public_base_directory.'/files/uploaded_user/lampiran/'.$dat1['lampiran'];
													$nameAt = $dat1['lampiran_ori'];
													if($dat1['lampiran'] && file_exists($pathAt)){
														$linkAt = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=2&ktg=mobil_".$idr."_".$idd."_&file=".$nameAt);
														$textAt = '<a href="'.$linkAt.'"><i class="fa fa-paperclip jarak-kanan"></i>'.$nameAt.'</a>';
													}
                                        ?>
                                            <tr>
                                                <td><?php echo $dat1['dokumen']; ?></td>
                                                <td class="text-center"><?php echo tgl_indo($dat1['masa_berlaku'],'normal','db','/'); ?></td>
                                                <td><?php echo $textAt; ?></td>
                                            </tr>
                                        <?php } } ?>
                                        </tbody>
									</table>
                                </div>

                                <?php 
									echo '<div style="font-size:12px;">';
									echo '<p style="margin-bottom:5px;">- Dibuat oleh '.$rsm['created_by'].' <i>('.date("d/m/Y H:i:s", strtotime($rsm['created_time'])).')</i></p>';
                                	if($rsm['lastupdate_by'])
										echo '<p>- Terakhir diupdate oleh '.$rsm['lastupdate_by'].' <i>('.date("d/m/Y H:i:s", strtotime($rsm['lastupdate_time'])).')</i></p>';
									echo '</div>';
								?>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <a href="<?php echo $link1; ?>" class="btn btn-default jarak-kanan"><i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                                            <a href="<?php echo $link2; ?>" class="btn btn-primary"><i class="fa fa-edit jarak-kanan"></i> Edit Data</a>
										</div>
                                    </div>
                                </div>
                            </div>
						</div>
					</div>
				</div>

			<?php } $con->close(); ?>
			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
	</div>

<style type="text/css">
	.table > tbody > tr > td{
		padding: 5px;
	}
	h3.form-title {
		 font-size: 18px;
		 margin: 0 0 10px;
		 font-weight: 700;
	}
</style>

</body>
</html>      

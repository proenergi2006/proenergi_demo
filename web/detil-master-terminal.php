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
	$sql 	= "select * from pro_master_terminal where id_master = '".$idr."';";
	$rsm 	= $con->getRecord($sql);
	$lastup = $rsm['lastupdate_by']." <i>(".date("d/m/Y H:i:s", strtotime($rsm['lastupdate_time'])).")</i>";
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("formatNumber"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1><?php echo "Detil Master Terminal"; ?></h1>
        	</section>
			<section class="content">

				<?php if($enk['idr'] !== '' && isset($enk['idr'])){ ?>
				<?php $flash->display(); ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Detil Data Terminal</h3>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table no-border">
                                        <tr>
                                            <td width="180">Nama Terminal</td>
                                            <td width="10" class="text-center">:</td>
                                            <td><?php echo $rsm['nama_terminal'];?></td>
                                        </tr>
                                        <tr>
                                            <td>Tanki</td>
                                            <td class="text-center">:</td>
                                            <td><?php echo $rsm['tanki_terminal'];?></td>
                                        </tr>
                                        <tr>
                                            <td>Lokasi</td>
                                            <td class="text-center">:</td>
                                            <td><?php echo $rsm['lokasi_terminal'];?></td>
                                        </tr>
										<tr>
                                            <td>Alamat</td>
                                            <td class="text-center">:</td>
                                            <td><?php echo $rsm['alamat_terminal'];?></td>
                                        </tr>
                                        <tr>
                                            <td>Telpon</td>
                                            <td class="text-center">:</td>
                                            <td><?php echo $rsm['telp_terminal'];?></td>
                                        </tr>
                                        <tr>
                                            <td>Fax</td>
                                            <td class="text-center">:</td>
                                            <td><?php echo $rsm['fax_terminal'];?></td>
                                        </tr>
                                        <tr>
                                            <td>PIC</td>
                                            <td class="text-center">:</td>
                                            <td><?php echo $rsm['cc_terminal'];?></td>
                                        </tr>
                                        <tr>
                                            <td>Status</td>
                                            <td class="text-center">:</td>
                                            <td><?php echo ($rsm["is_active"] == 1)?"Active":"Not Active";?></td>
                                        </tr>
									</table>
								</div>

                                <h3 class="form-title">ATTENTION</h3>
                                <div class="table-responsive">
                                	<table class="table table-bordered" style="margin-bottom:30px;">
                                        <thead>
                                            <tr>
                                                <th class="text-center" width="27%">Attention</th>
                                                <th class="text-center" width="25%">Posisi</th>
                                                <th class="text-center" width="20%">No. HP</th>
                                                <th class="text-center" width="28%">Email</th>
                                            </tr>
                                        </thead>
                                        <tbody>
											<?php 
                                                $attention = json_decode($rsm['att_terminal'], true);
                                                if(count($attention) == 0){
                                                    echo '<tr><td colspan="4" class="text-center">Tidak ada attention</td></tr>';
                                                } else{
                                                    foreach($attention as $dat3){
                                            ?>
                                            <tr>
                                                <td><?php echo $dat3['nama'];?></td>
                                                <td><?php echo $dat3['posisi'];?></td>
                                                <td><?php echo $dat3['hp'];?></td>
                                                <td><?php echo $dat3['email'];?></td>
                                            </tr>
                                            <?php } } ?>
                                        </tbody>
									</table>
                                </div>

                                <h3 class="form-title">PERIZINAN</h3>
                                <div class="table-responsive">
                                	<table class="table table-bordered">
                                    	<thead>
                                            <tr>
                                                <th class="text-center" width="35%">Perizinan</th>
                                                <th class="text-center" width="15%">Masa Berlaku</th>
                                                <th class="text-center" width="50%">Lampiran</th>
                                            </tr>
                                        </thead>
                                        <tbody>
										<?php 
                                            $cek1 = "select * from pro_master_terminal_detail where id_terminal = '".$idr."' order by id_td";
                                            $row1 = $con->getResult($cek1);
                                            if(count($row1) == 0){
                                                echo '<tr><td colspan="3" class="text-center">Tidak ada dokumen</td></tr>';
                                            } else{
                                                $d=0;
                                                foreach($row1 as $dat1){
                                                    $d++;
                                                    $idd 	= $dat1['id_td'];
													$linkAt = "";
													$textAt = "";
													$pathAt = $public_base_directory.'/files/uploaded_user/lampiran/'.$dat1['lampiran'];
													$nameAt = $dat1['lampiran_ori'];
													if($dat1['lampiran'] && file_exists($pathAt)){
														$linkAt = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=2&ktg=dokTerminal_".$idr."_".$idd."_&file=".$nameAt);
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

                                <div class="form-group row">
                                    <div class="col-sm-8">
                                        <label>Catatan</label>
                                        <div class="form-control" style="height:auto"><?php echo ($rsm['catatan_terminal'])?$rsm['catatan_terminal']:'&nbsp;';?></div>
                                    </div>
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
                                            <a href="<?php echo BASE_URL_CLIENT."/master-terminal.php"; ?>" class="btn btn-default jarak-kanan">
                                            <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                                            <a href="<?php echo BASE_URL_CLIENT.'/add-master-terminal.php?'.paramEncrypt('idr='.$rsm['id_master']);?>" class="btn btn-primary">
                                            <i class="fa fa-edit jarak-kanan"></i> Edit Data</a>
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

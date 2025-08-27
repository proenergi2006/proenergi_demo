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
	$sql 	= "select a.*, b.group_wilayah from pro_master_cabang a join pro_master_group_cabang b on a.id_group_cabang = b.id_gu where a.id_master = '".$idr."'";
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
        		<h1><?php echo "Detil Master Cabang"; ?></h1>
        	</section>
			<section class="content">

				<?php if($enk['idr'] !== '' && isset($enk['idr'])){ ?>
				<?php $flash->display(); ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Detil Data Cabang</h3>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table no-border">
                                        <tr>
                                            <td width="180">Nama Cabang</td>
                                            <td width="10" class="text-center">:</td>
                                            <td><?php echo $rsm['nama_cabang'];?></td>
                                        </tr>
                                        <tr>
                                            <td>Wilayah</td>
                                            <td class="text-center">:</td>
                                            <td><?php echo $rsm['group_wilayah'];?></td>
                                        </tr>
                                        <tr>
                                            <td>Kode Barcode</td>
                                            <td class="text-center">:</td>
                                            <td><?php echo $rsm['kode_barcode'];?></td>
                                        </tr>
                                        <tr>
                                            <td>Format Nomor</td>
                                            <td class="text-center">:</td>
                                            <td><?php echo $rsm['inisial_cabang'];?></td>
                                        </tr>
                                        <tr>
                                            <td>Format Segel</td>
                                            <td class="text-center">:</td>
                                            <td><?php echo $rsm['inisial_segel'];?></td>
                                        </tr>
                                        <tr>
                                            <td>Stok Segel</td>
                                            <td class="text-center">:</td>
                                            <td><?php echo $rsm['stok_segel'];?></td>
                                        </tr>
                                        <tr>
                                            <td>Nomor Segel Terakhir</td>
                                            <td class="text-center">:</td>
                                            <td><?php echo $rsm['inisial_segel']."-".$rsm['urut_segel'];?></td>
                                        </tr>
                                        <tr>
                                            <td>Status</td>
                                            <td class="text-center">:</td>
                                            <td><?php echo ($rsm["is_active"] == 1)?"Active":"Not Active";?></td>
                                        </tr>
									</table>
								</div>
                                <div class="form-group row">
                                    <div class="col-sm-8">
                                        <label>Catatan</label>
                                        <div class="form-control" style="height:auto"><?php echo ($rsm['catatan_cabang'])?$rsm['catatan_cabang']:'&nbsp;';?></div>
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
                                            <a href="<?php echo BASE_URL_CLIENT."/master-cabang.php"; ?>" class="btn btn-default jarak-kanan">
                                            <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                                            <a href="<?php echo BASE_URL_CLIENT.'/add-master-cabang.php?'.paramEncrypt('idr='.$rsm['id_master']);?>" class="btn btn-primary">
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
</style>
</body>
</html>      

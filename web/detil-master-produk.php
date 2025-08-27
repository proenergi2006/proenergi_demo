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
	$sql 	= "select * from pro_master_produk where id_master = '".$idr."';";
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
        		<h1><?php echo "Detil Master Produk"; ?></h1>
        	</section>
			<section class="content">

				<?php if($enk['idr'] !== '' && isset($enk['idr'])){ ?>
				<?php $flash->display(); ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Detil Data Produk</h3>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table no-border">
                                        <tr>
                                            <td width="180">Jenis Produk</td>
                                            <td width="10" class="text-center">:</td>
                                            <td><?php echo $rsm['jenis_produk'];?></td>
                                        </tr>
                                        <tr>
                                            <td>Merk Dagang</td>
                                            <td class="text-center">:</td>
                                            <td><?php echo $rsm['merk_dagang'];?></td>
                                        </tr>
                                        <tr>
                                            <td>Catatan</td>
                                            <td class="text-center">:</td>
                                            <td><?php echo $rsm['catatan_produk'];?></td>
                                        </tr>
                                        <tr>
                                            <td>Status</td>
                                            <td class="text-center">:</td>
                                            <td><?php echo ($rsm["is_active"] == 1)?"Active":"Not Active";?></td>
                                        </tr>
                                        <tr>
                                            <td>Dibuat oleh</td>
                                            <td class="text-center">:</td>
                                            <td><?php echo $rsm['created_by']." <i>(".date("d/m/Y H:i:s", strtotime($rsm['created_time'])).")</i>";?></td>
                                        </tr>
                                        <tr>
                                            <td>Diupdate terakhir</td>
                                            <td class="text-center">:</td>
                                            <td><?php echo ($rsm['lastupdate_time'])?$lastup:'-';?></td>
                                        </tr>
									</table>
								</div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <a href="<?php echo BASE_URL_CLIENT."/master-produk.php"; ?>" class="btn btn-default jarak-kanan">
                                            <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                                            <a href="<?php echo BASE_URL_CLIENT.'/add-master-produk.php?'.paramEncrypt('idr='.$rsm['id_master']);?>" class="btn btn-primary">
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

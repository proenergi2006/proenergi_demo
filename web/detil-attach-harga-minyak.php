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
	$sql = "select * from pro_attach_harga_minyak where id_master = '".$idr."'";
	$rsm = $con->getRecord($sql);
	$pathAt = $public_base_directory.'/files/uploaded_user/lampiran/'.$rsm['attach_harga'];
	$period = date("d/m/Y", strtotime($rsm['periode_awal'])).' - '.date("d/m/Y", strtotime($rsm['periode_akhir']));
	$link1 	= BASE_URL_CLIENT.'/attach-harga-minyak.php';
	$link2 	= BASE_URL_CLIENT.'/add-attach-harga-minyak.php?'.paramEncrypt("idr=".$idr);
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
        		<h1>Detil Attachment Harga Jual</h1>
        	</section>
			<section class="content">

				<?php if($enk['idr'] !== '' && isset($enk['idr'])){ ?>
				<?php $flash->display(); ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-info">
                            <div class="box-body">
                                <div class="form-group row">
                                    <div class="col-sm-10 col-md-8">
                                        <label>Periode</label>
                                        <div class="form-control" style="height:auto">
											<?php echo tgl_indo($rsm['periode_awal']); ?> - <?php echo tgl_indo($rsm['periode_akhir']); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-10 col-md-8">
                                        <label>Catatan</label>
                                        <div class="form-control" style="height:auto"><?php echo ($rsm['note_attach'])?$rsm['note_attach']:'&nbsp;';?></div>
                                    </div>
                                </div>
								<?php
                                    if($rsm['attach_harga'] && file_exists($pathAt)){
                                        $linkAt = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=2&ktg=aPrice_".$idr."_&file=".$rsm['attach_harga_ori']);
                                        echo '<p><a href="'.$linkAt.'"><i class="fa fa-file-alt jarak-kanan"></i>'.$rsm['attach_harga_ori'].'</a></p>';
                                    }
                                ?>

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
                                            <a href="<?php echo $link1; ?>" class="btn btn-default jarak-kanan">Kembali</a>
                                        	<?php if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 3){ ?>
                                            <a href="<?php echo $link2; ?>" class="btn btn-primary jarak-kanan">Edit Data</a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>

                            </div>
						</div>
					</div>
				</div>

            <?php } ?>
			<?php $con->close(); ?>
			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
	</div>

</body>
</html>      

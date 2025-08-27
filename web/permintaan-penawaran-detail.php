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
	$sql = "select a.* ,role_name
from pro_permintaan_penawaran a
inner join acl_user b on a.pic_user = b.id_user
inner join acl_role c on b.id_role = c.id_role where id_pmnt = '".$idr."'";
	$rsm = $con->getRecord($sql);
	$link1 	= BASE_URL_CLIENT.'/permintaan-penawaran.php';
	$link2 	= BASE_URL_CLIENT.'/permintaan-penawaran-add.php?'.paramEncrypt('idr='.$idr);
	$pathPt = $public_base_directory.'/files/uploaded_user/lampiran/'.$rsm['attachment_order'];
	$lampPt = $rsm['attachment_order_ori'];
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
        		<h1>Detil Permintaan Penawaran</h1>
        	</section>
			<section class="content">

				<?php $flash->display(); ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                            	<h3 class="box-title"><?php echo 'INQ-'.str_pad($rsm['id_pmnt'],4,'0',STR_PAD_LEFT);?></h3>
                            </div>
                            <div class="box-body">
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <p class="text-right" style="margin-bottom:0px;"><?php echo tgl_indo($rsm['created_time']);?></p>
                                        <p class="text-center" style="font-weight:bold; font-size:16px;"><?php echo $rsm['judul_pmnt'];?></p>
                                        <div class="form-control" style="height:auto"><?php echo $rsm['pesan_pmnt'];?></div>
                                    </div>
                                </div>

                            	<?php
                                    if($rsm['attachment_order'] && file_exists($pathPt)){
                                        $linkPt = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=2&ktg=tawar_".$idr."_&file=".$lampPt);
                                        echo '<p><a href="'.$linkPt.'"><i class="fa fa-file-alt jarak-kanan"></i>'.$lampPt.'</a></p>';
                                    }
								?>
                                <div style="margin-bottom:10px;">&nbsp;</div>

                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <label>Contact Person</label>
                                        <p style="margin-bottom:0px"><?php echo $rsm['pic_name'];?></p>
										<p style="margin-bottom:0px"><?php echo ucwords(str_replace('ROLE ','',strtoupper($rsm['role_name'])));?></p>
                                        <p style="margin-bottom:0px"><?php echo $rsm['pic_telp'];?></p>
                                        <p style="margin-bottom:0px"><?php echo $rsm['pic_email'];?></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <a class="btn btn-default jarak-kanan" href="<?php echo $link1;?>"><i class="fa fa-reply jarak-kanan"></i>Kembali</a>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>

            <div class="modal fade" id="loading_modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-blue">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
<style>
	.table > tbody > tr > td{
		padding: 5px;
	}
	.form-control p{
		margin-bottom: 5px;
	}
</style>
</body>
</html>      

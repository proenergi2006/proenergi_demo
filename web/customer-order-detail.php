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
from pro_permintaan_order a
inner join acl_user b on a.pic_user = b.id_user
inner join acl_role c on b.id_role = c.id_role where id_pmnt_order = '".$idr."'";
	$rsm = $con->getRecord($sql);
	$link1 	= BASE_URL_CLIENT.'/customer-order.php';
	$pathPt = $public_base_directory.'/files/uploaded_user/lampiran/'.$rsm['attachment_order'];
	$lampPt = $rsm['attachment_order_ori'];
	if($rsm['id_pmnt_order']){
		$sqlAksi1 = "update pro_permintaan_order set is_delivered = 1 where id_pmnt_order = '".$idr."'";
		$con->setQuery($sqlAksi1); 
		$con->clearError();	
	}
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
        		<h1>Detil Customer Order</h1>
        	</section>
			<section class="content">

				<?php if($enk['idr'] !== '' && isset($enk['idr'])){ ?>
				<?php $flash->display(); ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-body">
                            	<div class="table-responsive">
                                	<table class="table no-border">
                                    	<tr>
                                        	<td width="100">Nomor PO</td>
                                        	<td width="10">:</td>
                                        	<td><?php echo $rsm['nomor_order'];?></td>
                                        </tr>
                                    	<tr>
                                        	<td>Tanggal PO</td>
                                        	<td>:</td>
                                        	<td><?php echo date("d/m/Y", strtotime($rsm['tanggal_order']));?></td>
                                        </tr>
                                    	<tr>
                                        	<td>Volume</td>
                                        	<td>:</td>
                                        	<td><?php echo number_format($rsm['volume_order']);?> Liter</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-8">
                                        <label>Catatan</label>
										<div class="form-control" style="height:auto"><?php echo ($rsm['catatan'])?$rsm['catatan']:'&nbsp;';?></div>
                                    </div>
                                </div>
                                
                            	<?php
                                    if($rsm['attachment_order'] && file_exists($pathPt)){
                                        $linkPt = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=2&ktg=order_".$idr."_&file=".$lampPt);
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
            <?php } ?>
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
	.preview-file{
		background-color: #f4f4f4;
		font-weight: 400;
		padding: 5px 25px 5px 10px;
		border: 1px solid #ddd;
		position: relative;
	}
	.preview-file > span{
		font-weight: 700;
		color: #3c8dbc;
		cursor: pointer;
		position: absolute;
		top: 5px;
		right: 5px;
	}
</style>
</body>
</html>      

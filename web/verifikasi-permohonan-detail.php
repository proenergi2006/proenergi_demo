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

	$sql = "select a.*, b.nama_customer, c.fullname from pro_customer_update a join pro_customer b on a.id_customer = b.id_customer 
			join acl_user c on b.id_marketing = c.id_user where a.id_cu = '".$idk."' and a.id_customer = '".$idr."'";
	$rsm = $con->getRecord($sql);
	$pathPt 	= $public_base_directory.'/files/uploaded_user/lampiran/'.$rsm['attachment_order'];
	$lampPt 	= $rsm['attachment_order_ori'];
	$arrFlag 	= array(1=>"Admin Finance", "BM", "CFO");
	if($rsm['flag_disposisi'] == 0)
		$status = "Terdaftar";
	else if($rsm['flag_edited'] == 1)
		$status = "Telah Dimutakhirkan";
	else if($rsm['flag_approval'] == 1)
		$status = "Permohonan Disetujui";
	else if($rsm['flag_approval'] == 2)
		$status = "Permohonan Ditolak ".$arrFlag[$rsm['flag_disposisi']];
	else if($rsm['flag_disposisi'] == 1)
		$status = "Diverifikasi Admin Finance";
	else if($rsm['flag_disposisi'] == 2)
		$status = "Diverifikasi BM";
	else if($rsm['flag_disposisi'] == 3)
		$status = "Diverifikasi CFO";
	else if($rsm['flag_disposisi'] == 4)
		$status = "Diverifikasi CEO";
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
        		<h1>Verifikasi Permohonan</h1>
        	</section>
			<section class="content">

				<?php $flash->display(); ?>
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                    	<a href="#form-evaluation" aria-controls="form-evaluation" role="tab" data-toggle="tab">Verifikasi Permohonan</a>
					</li>
                    <li role="presentation" class="">
                    	<a href="#data-evaluation" aria-controls="data-evaluation" role="tab" data-toggle="tab">Data Permohonan</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="form-evaluation">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Form Verifikasi</h3>
                                    </div>
                                    <div class="box-body">
                                        <form action="<?php echo ACTION_CLIENT.'/evaluation-permohonan.php'; ?>" id="gform" name="gform" method="post">
                                        <?php
                                            if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 10)
                                                require_once($public_base_directory."/web/__get_permohonan_finance.php");
                                            else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 7)
                                                require_once($public_base_directory."/web/__get_permohonan_om.php");
                                            else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 4)
                                                require_once($public_base_directory."/web/__get_permohonan_cfo.php");
                                            else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 3)
                                                require_once($public_base_directory."/web/__get_permohonan_ceo.php");
                                        ?>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="data-evaluation">
                        <div class="box box-primary">
                            <div class="box-body">
                                <div class="form-horizontal">
                                    <h3 style="font-size:20px; margin:0px 0px 20px;"><b><?php echo $rsm['nama_customer']; ?></b></h3>
        
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="form-group form-group-sm">
                                                <label class="control-label col-md-3">Kategori</label>
                                                <div class="col-md-9">
                                                    <?php 
                                                        $arrKategoriPerubahan = array(
                                                            1=>"Perubahan Credit Limit",
                                                            "Perubahan TOP",
                                                            "Perubahan Data",
                                                            "Perubahan Credit Limit & Data Customer",
                                                            "Perubahan TOP & Data Customer",
                                                            "Perubahan Credit Limit & TOP",
                                                            "Perubahan Credit Limit & TOP & Data Customer",
                                                        );
                                                    ?>
                                                    <div class="form-control" style="height:auto;"><?php echo ($rsm['kategori'] ? $arrKategoriPerubahan[$rsm['kategori']] : '&nbsp;'); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
            
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="form-group form-group-sm">
                                                <label class="control-label col-md-3">Judul</label>
                                                <div class="col-md-9">
                                                    <div class="form-control" style="height:auto;"><?php echo $rsm['judul']; ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
        
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="form-group form-group-sm">
                                                <label class="control-label col-md-3">Pesan</label>
                                                <div class="col-md-9">
                                                    <div class="form-control" style="height:auto;"><?php echo $rsm['pesan']; ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
        
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="form-group form-group-sm">
                                                <label class="control-label col-md-3">Lampiran</label>
                                                <div class="col-md-9">
                                                    <div class="form-control" style="height:auto;">
                                                        <?php
                                                            if($rsm['attachment_order'] && file_exists($pathPt)){
                                                                $linkPt = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=2&ktg=PUD_".$idk."_&file=".$lampPt);
                                                                echo '<p><a href="'.$linkPt.'"><i class="fa fa-file-alt jarak-kanan"></i>'.$lampPt.'</a></p>';
                                                            } else echo '&nbsp;';
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
        
                                </div>
                            </div>
                        
                        </div>
                    
                    </div>
                </div>

            <div class="modal fade" id="preview_modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-blue">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Peringatan</h4>
                        </div>
                        <div class="modal-body"><div id="preview_alert"></div></div>
                        <div class="modal-footer">
                            <div class="text-right">
                                <button type="button" name="cfCancel" id="cfCancel" class="btn btn-default jarak-kanan" data-dismiss="modal">Cancel</button>
                                <button type="button" name="cfOke" id="cfOke" class="btn btn-primary">Confirm</button>
                            </div>
                        </div>
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
	.form-control > p{
		margin-bottom:3px;
	}
</style>
</body>
</html>      

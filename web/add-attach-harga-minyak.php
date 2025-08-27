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
	$uRole 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	$link1 	= BASE_URL_CLIENT.'/attach-harga-minyak.php';

    if ($idr['idr'] !== '' && isset($enk['idr'])){
        $action 	= "update"; 
		$section 	= "Edit Attchment Harga";
        $idr = isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
		$sql = "select * from pro_attach_harga_minyak where id_master = '".$idr."'";
        $rsm = $con->getRecord($sql);
		$pathAt = $public_base_directory.'/files/uploaded_user/lampiran/'.$rsm['attach_harga'];
    } else{ 
		$action 	= "add";
		$section 	= "Tambah Attchment Harga";
        $rsm 		= array();
		$pathPt 	= "";
	}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("formatNumber","jqueryUI","ckeditor"), "css"=>array("jqueryUI"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1><?php echo $section; ?></h1>
        	</section>
			<section class="content">

				<?php $flash->display(); ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-info">
                        	<div class="box-header with-border">
                            	<h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
							</div>
                            <div class="box-body">
                                <form action="<?php echo ACTION_CLIENT.'/attach-harga-minyak.php'; ?>" id="gform" name="gform" method="post" enctype="multipart/form-data">
                                <div class="form-group row">
                                    <div class="col-sm-4 col-md-3">
                                        <label>Periode Awal *</label>
                                        <?php if($action == "add"){ ?>
                                        <input type="text" name="periode_awal" id="periode_awal" class="form-control validate[required,custom[date]] datepicker" autocomplete = 'off'/>
                                        <?php } else{ ?>
                                        <input type="hidden" name="periode_awal" id="periode_awal" value="<?php echo date("d/m/Y", strtotime($rsm['periode_awal'])); ?>" />
                                        <div class="form-control"><?php echo date("d/m/Y", strtotime($rsm['periode_awal'])); ?></div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-sm-4 col-md-3 col-sm-top">
                                        <label>Periode Akhir *</label>
                                        <?php if($action == "add"){ ?>
                                        <input type="text" name="periode_akhir" id="periode_akhir" class="form-control validate[required,custom[date]] datepicker" autocomplete = 'off'/>
                                        <?php } else{ ?>
                                        <input type="hidden" name="periode_akhir" id="periode_akhir" value="<?php echo date("d/m/Y", strtotime($rsm['periode_akhir'])); ?>" />
                                        <div class="form-control"><?php echo date("d/m/Y", strtotime($rsm['periode_akhir'])); ?></div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-8">
                                        <label>Catatan</label>
                                        <textarea name="note" id="note" class="form-control wysiwyg"><?php echo $rsm['note_attach'];?></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12">
										<?php
                                            if($rsm['attach_harga'] && file_exists($pathAt)){
                                                $linkAt = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=2&ktg=aPrice_".$idr."_&file=".$rsm['attach_harga_ori']);
                                                echo '<label>Ganti Lampiran</label>';
												echo '<p><a href="'.$linkAt.'"><i class="fa fa-file-alt jarak-kanan"></i>'.$rsm['attach_harga_ori'].'</a></p>';
												echo '<input type="file" name="attach_harga" id="attach_harga" class="" />';
                                            } else{
												echo '<label>Lampiran *</label>';
												echo '<input type="file" name="attach_harga" id="attach_harga" class="validate[required]" />';
											}
                                        ?>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <input type="hidden" name="act" value="<?php echo $action;?>" />
                                            <input type="hidden" name="idr" value="<?php echo $idr;?>" />
                                            <a href="<?php echo $link1; ?>" class="btn btn-default jarak-kanan"><i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                                            <button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
										</div>
                                    </div>
                                </div>
                                <hr style="margin:5px 0" />
                                <div class="clearfix">
                                    <div class="col-sm-12"><small>* Wajib Diisi</small></div>
                                </div>
                                </form>
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
	.table-jual{
		border:1px solid #ddd;
	}
	.table-jual > p{
		 margin:0px;
		 padding-right:35px;
	}
	p.frmid{
		margin-bottom:0px;
		margin-top:-5px;
	}
	.jual-detil{
		padding:15px 15px 0px;
		border:1px solid #ddd;
		border-bottom-width:5px;
	}
</style>
<script>
	$(document).ready(function(){
		var objAttach = {
			onValidationComplete: function(form, status){
				if(status == true){
					$('#loading_modal').modal({backdrop:"static"});
					for (instance in CKEDITOR.instances){
						CKEDITOR.instances[instance].updateElement();
					}
					form.validationEngine('detach');
					form.submit();
				}
			}
		}
		$(".wysiwyg").ckeditor();
		$("form#gform").validationEngine('attach',objAttach);
	});
</script>
</body>
</html>      

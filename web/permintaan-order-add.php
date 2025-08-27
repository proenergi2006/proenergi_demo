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
	$sescus = paramDecrypt($_SESSION['sinori'.SESSIONID]['customer']);

	if($idr != ""){
		$action 	= "update";
		$titleAct 	= "Ubah Permintaan Penawaran";
		$sql = "select a.* ,role_name
from pro_permintaan_penawaran a
inner join acl_user b on a.pic_user = b.id_user
inner join acl_role c on b.id_role = c.id_role where id_pmnt = '".$idr."'";
		$rsm = $con->getRecord($sql);
		$pic_name = $rsm['pic_name'];
		$pic_mail = $rsm['pic_email'];
		$pic_telp = $rsm['pic_telp'];
	} else{
		$rsm = array();
		$action 	= "add";
		$titleAct 	= "Tambah Order";
		$cek = "select email_user, mobile_user, fullname from acl_user where id_user = (select id_marketing from pro_customer where id_customer = '".$sescus."')";
		$row = $con->getRecord($cek);
		$pic_name = $row['fullname'];
		$pic_mail = $row['email_user'];
		$pic_telp = $row['mobile_user'];
	}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("formatNumber", "jqueryUI", "ckeditor"), "css"=>array("jqueryUI"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1><?php echo $titleAct; ?></h1>
        	</section>
			<section class="content">

				<?php $flash->display(); ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                        	<div class="box-header with-border">
                            	<h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
							</div>
                            <div class="box-body">
                                <form action="<?php echo ACTION_CLIENT.'/permintaan-order.php'; ?>" id="gform" name="gform" method="post" enctype="multipart/form-data">
                                <div class="form-group row">
                                    <div class="col-sm-6">
										<label>Nomor PO *</label>
                                        <input type="text" name="nomor_order" id="nomor_order" class="form-control validate[required]" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
										<label>Tanggal PO *</label>
                                        <input type="text" name="tanggal_order" id="tanggal_order" class="form-control datepicker validate[required,custom[date]]" autocomplete = 'off' />
                                    </div>
                                    <div class="col-sm-3 col-sm-top">
                                        <label>Volume Pemesanan</label>
                                        <div class="input-group">
                                            <input type="text" name="volume_order" id="volume_order" class="form-control hitung validate[required]" />
                                            <span class="input-group-addon">Liter</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-8">
                                        <label>Catatan</label>
                                        <textarea name="catatan" id="catatan" class="form-control wysiwyg"><?php echo $rsm['catatan'];?></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <label>Attachment</label>
										<input type="file" name="attachment_order" id="attachment_order" /></td>
                                        <p style="font-size:12px;" class="help-block">* Max size 2Mb | .jpg, .png, .rar, .pdf, .zip</p>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <label>Contact Person</label>
                                        <p style="margin-bottom:0px"><?php echo $pic_name;?></p>
										<?php if($idr != ""){ ?>
										<p style="margin-bottom:0px"><?php echo ucwords(str_replace('ROLE ','',strtoupper($rsm['role_name'])));?></p>
										<?php } ?>
                                        <p style="margin-bottom:0px"><?php echo $pic_telp;?></p>
                                        <p style="margin-bottom:0px"><?php echo $pic_mail;?></p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <input type="hidden" name="act" value="<?php echo $action;?>" />
                                            <input type="hidden" name="idr" value="<?php echo $idr;?>" />
                                            <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT."/permintaan-order.php";?>">
                                            <i class="fa fa-reply jarak-kanan"></i>Kembali</a>
                                            <button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
                                        </div>
                                    </div>
                                </div>
                                </form>
                                
                                
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

<script>
	$(document).ready(function(){
		$(".hitung").number(true, 0, ".", ",");
		$(".wysiwyg").ckeditor();
		$("form#gform").validationEngine('attach',{
			onValidationComplete: function(form, status){
				$('#preview_modal').modal('hide');
				$('#preview_modal').find('#preview_alert').html("");
				$('#preview_modal').find('.modal-footer').on("click", "#cfOke", function(){
					$('#preview_modal').modal('hide');
					$('#loading_modal').modal({backdrop:"static"});
					form.validationEngine('detach');
					form.submit();
				});
				$('#preview_modal').find('.modal-footer').addClass("hide");
				if(status == true){
					CKEDITOR.instances.catatan.updateElement();
					$('#preview_modal').find('#preview_alert').html('<p class="text-center">Data tidak dapat diubah, apakah anda yakin ?</p>');
					$('#preview_modal').find('.modal-footer').removeClass("hide");
					$('#preview_modal').modal();
				}
			}
		});
	});
</script>
</body>
</html>      

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
	$cek = "select telp_customer, email_customer from pro_customer where id_customer = '".$idr."'";
	$row = $con->getRecord($cek);
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
        		<h1>Tambah User Customer</h1>
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
                                <form action="<?php echo ACTION_CLIENT.'/customer-admin.php'; ?>" id="gform" name="gform" method="post" role="form">
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Username *</label>
                                        <input type="text" id="username" name="username" class="form-control" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Email *</label>
                                        <input type="text" id="email" name="email" class="form-control" value="<?php echo $row['email_customer'];?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Telepon *</label>
                                        <input type="text" id="telp" name="telp" class="form-control" value="<?php echo $row['telp_customer'];?>" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <input type="hidden" name="act" value="add" />
                                            <input type="hidden" name="idr" value="<?php echo $idr;?>" />
                                            <a href="<?php echo BASE_URL_CLIENT."/customer-admin-detail.php?".paramEncrypt("idr=".$idr);?>" class="btn btn-default jarak-kanan">
                                            <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
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

            <div class="modal fade" id="preview_modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-blue">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Peringatan</h4>
                        </div>
                        <div class="modal-body">
                        	<div id="preview_alert" class="text-center"></div>
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
		$("form#gform").on("click", "button:submit", function(){
			$("#loading_modal").modal({backdrop:"static"});
			$.ajax({
				type	: 'POST',
				url		: "./__cek_customer_admin.php",
				dataType: "json",
				data	: $("#gform").serializeArray(),
				cache	: false,
				success : function(data){
					if(data.error){
						$("#preview_modal").find("#preview_alert").html(data.error);
						$("#preview_modal").modal();
						$("#loading_modal").modal("hide");					
						return false;
					} else{
						$("button[type='submit']").addClass("disabled");
						$("#gform").submit();
					}
				}
			});
			return false;
		});
	});
</script>
</body>
</html>      

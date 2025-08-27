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
	$cek 	= "select urut_segel, inisial_segel, stok_segel from pro_master_cabang where id_master = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_wilayah"])."'";
	$row 	= $con->getRecord($cek);
	$sg1 	= ($row['urut_segel'])?$row['inisial_segel']."-".str_pad($row['urut_segel'],4,'0',STR_PAD_LEFT):'';
	$sg2 	= ($row['stok_segel'])?number_format($row['stok_segel'],0,'','.'):'Tidak ada';
	
	if($idr != ""){
		$sql = "select * from pro_manual_segel where id_master = '".$idr."'";
		$rsm = $con->getRecord($sql);
		$action 	= "update";
		$titleAct 	= "Ubah Segel Manual";
	} else{
		$rsm 		= array();
		$action 	= "add";
		$titleAct 	= "Tambah Segel Manual";
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
        		<h1><?php echo $titleAct;?></h1>
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
                                <form action="<?php echo ACTION_CLIENT.'/manual-segel.php'; ?>" id="gform" name="gform" method="post" role="form">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label>Nomor Segel Terakhir</label>
                                        <input type="text" id="nomor_akhir" name="nomor_akhir" class="form-control" value="<?php echo $sg1;?>" readonly />
                                    </div>
                                    <div class="col-sm-4 col-sm-top">
                                        <label>Stock Segel</label>
                                        <input type="text" id="nomor_stock" name="nomor_stock" class="form-control" value="<?php echo $sg2;?>" readonly />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 col-md-4">
                                        <label>Kategori *</label>
                                        <?php if($action == "add"){ ?>
                                        <select name="kategori" id="kategori" class="select2 validate[required]">
                                        	<option></option>
                                            <option value="1">Segel</option>
                                            <option value="2">Lain-lain</option>
                                        </select>
                                        <?php } else if($action == "update"){ ?>
                                        <input type="hidden" name="kategori" id="kategori" value="<?php echo $rsm['kategori'];?>" />
                                        <input type="text" class="form-control" value="<?php echo ($rsm['kategori'] == 1)?'Segel':'Lain-lain';?>" readonly />
                                        <?php } ?>
                                    </div>
                                </div>
								
								<?php if($action == "add" || ($action == "update" && $rsm['kategori'] == 0)){ ?>
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label>Jumlah *</label>
                                        <input type="text" id="jumlah" name="jumlah" class="form-control validate[required]" />
                                    </div>
                                    <div class="col-sm-4 col-sm-top">
                                        <label>Tanggal *</label>
                                        <input type="text" id="tanggal" name="tanggal" class="form-control datepicker validate[required,custom[date]]" autocomplete = 'off' />
                                    </div>
                                </div>
								<?php } ?>
                                
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <label>Keperluan *</label>
                                        <textarea id="keperluan" name="keperluan" class="form-control wysiwyg" style="height:400px;"><?php echo $rsm['keperluan'];?></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <input type="hidden" name="act" value="<?php echo $action;?>" />
                                            <input type="hidden" name="idr" value="<?php echo $idr;?>" />
                                            <a href="<?php echo BASE_URL_CLIENT."/manual-segel.php"; ?>" class="btn btn-default jarak-kanan">
                                            <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                                            <button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
                                    	</div>
                                    </div>
                                </div>
                                <hr style="margin:5px 0" />
                                <div class="row"><div class="col-sm-12"><small>* Wajib Diisi</small></div></div>
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
		$(".wysiwyg").ckeditor();
		$("#kategori").on("change", function(){
			var nilai = $(this).val();
			if(nilai == 1){
				$("#jumlah, #tanggal").removeProp("disabled");
			} else if(nilai == 2){
				$("#jumlah, #tanggal").val("").prop("disabled", "disabled");
			} else{
				$("#jumlah, #tanggal").removeProp("disabled");
			}
		});
		$("form#gform").validationEngine('attach',{
			onValidationComplete: function(form, status){
				$('#preview_modal').find('#preview_alert').html("");
				$('#preview_modal').modal('hide');
				if(status == true){
					if(confirm("Apakah anda yakin?")){
						CKEDITOR.instances.keperluan.updateElement();
						if($("#keperluan").val() == ""){
							$('#preview_modal').find('#preview_alert').html('<p class="text-center">Keperluan belum diisi</p>');
							$('#preview_modal').modal();
						}else {
							$('#preview_modal').modal('hide');
							$('#loading_modal').modal({backdrop:"static"});
							form.validationEngine('detach');
							form.submit();
						}
					} 
				}
			}
		});
	});		
</script>
</body>
</html>      

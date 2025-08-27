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
	$idk 	= isset($enk["idk"])?htmlspecialchars($enk["idk"], ENT_QUOTES):'';
    $sesid  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);
    $seswil = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
    $sesgroup = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']);

	$arrKategoriPerubahan = array(
		1=>"Perubahan Credit Limit",
		"Perubahan TOP",
		"Perubahan Data",
		"Perubahan Credit Limit & Data Customer",
		"Perubahan TOP & Data Customer",
		"Perubahan Credit Limit & TOP",
		"Perubahan Credit Limit & TOP & Data Customer",
	);

	if($idk != ""){
		$sql = "select a.*, b.nama_customer from pro_customer_update a join pro_customer b on a.id_customer = b.id_customer 
				where a.id_cu = '".$idk."' and a.id_customer = '".$idr."'";
		$rsm = $con->getRecord($sql);
		$action 	= "update";
		$titleAct 	= "Ubah Permohonan";
		$pesan 		= $rsm['pesan'];
		$pathPt 	= $public_base_directory.'/files/uploaded_user/lampiran/'.$rsm['attachment_order'];
		$lampPt 	= $rsm['attachment_order_ori'];
	} else{
		$rsm 		= array();
		$action 	= "add";
		$titleAct 	= "Tambah Permohonan";
		$pesan 		= '<p>Kepada Yth.</p><p>Manajemen Pro Energi</p><p>Di Tempat</p>';
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
        		<h1><?php echo $titleAct; ?></h1>
        	</section>
			<section class="content">

				<?php $flash->display(); ?>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
                    </div>
                    <div class="box-body">
                        <form action="<?php echo ACTION_CLIENT.'/customer-permohonan-update.php'; ?>" id="gform" name="gform" method="post" class="form-horizontal" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-3">Customer *</label>
                                    <div class="col-md-6">
                                    <?php 
										if($action == "add"){ 
											$where = "id_marketing = '".$sesid."'";
											if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 18) {
												$where = "1=1";
												if (paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
													$where = "(id_wilayah = '".$seswil."' or id_marketing = '".$sesid."')";
												else if (!paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
													$where = "(id_group = '".$sesgroup."' or id_marketing = '".$sesid."')";
											}
											echo '<select name="idr" id="idr" class="form-control select2" required style="width:100%"><option></option>';
                                        	$con->fill_select("id_customer", "if(kode_pelanggan = '', nama_customer, concat(kode_pelanggan,' - ',nama_customer))", "pro_customer", $rsm['id_customer'], "where ".$where." and is_verified = 1 and status_customer > 1", "nama", false);
											echo '</select>';
										} else if($action == "update"){
											echo '
											<input type="hidden" name="idr" value="'.$idr.'" />
											<input type="text" name="idrNama" id="idrNama" class="form-control" value="'.$rsm['nama_customer'].'" readonly />';
										}
                                    ?>
                                    </div>
                                </div>
                            </div>
						</div>

                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-3">Kategori *</label>
                                    <div class="col-md-6">
                                        <select name="kategori" id="kategori" class="form-control select2" required style="width:100%">
                                        	<option></option>
                                            <?php 
												foreach($arrKategoriPerubahan as $idx=>$val){
													$selected = ($rsm['kategori'] == $idx ? 'selected' : '');
													echo '<option value="'.$idx.'" '.$selected.'>'.$val.'</option>';
												}
											?>
										</select>
                                    </div>
                                </div>
                            </div>
						</div>

                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-3">Judul *</label>
                                    <div class="col-md-9">
										<input type="text" name="judul" id="judul" class="form-control" required value="<?php echo $rsm['judul'] ?? null; ?>" />
                                    </div>
                                </div>
                            </div>
						</div>

                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-3">Pesan *</label>
                                    <div class="col-md-9">
										<textarea name="pesan" id="pesan" class="form-control wysiwyg"><?php echo $pesan; ?></textarea>
                                    </div>
                                </div>
                            </div>
						</div>

                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group form-group-sm">
									<?php
                                        if(isset($rsm['attachment_order']) && $rsm['attachment_order'] && file_exists($pathPt)){
                                            $linkPt = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=2&ktg=PUD_".$idk."_&file=".$lampPt);
                                            $txtLamp01A = 'Ubah Lampiran';
                                            $txtLamp01B = '<p><a href="'.$linkPt.'"><i class="fa fa-file-alt jarak-kanan"></i>'.$lampPt.'</a></p>';
                                        } else{
                                            $txtLamp01A = 'Lampiran';
                                            $txtLamp01B = '';
                                        }
                                    ?>
                                    <label class="control-label col-md-3"><?php echo $txtLamp01A;?></label>
                                    <div class="col-md-9">
										<?php echo $txtLamp01B;?>
                                        <input type="file" name="attachment_order" id="attachment_order" class="form-control" />
                                        <p style="font-size:12px;" class="help-block">* Max size 2Mb | .jpg, .png, .rar, .pdf, .zip</p>
                                    </div>
                                </div>
                            </div>
						</div>

                        <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

                        <div style="margin-bottom:15px;">
                            <input type="hidden" name="act" value="<?php echo $action;?>" />
                            <input type="hidden" name="idk" value="<?php echo $idk;?>" />
                            <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">
                            <i class="fa fa-save jarak-kanan"></i> Simpan</button>
                            <a href="<?php echo BASE_URL_CLIENT.'/customer-permohonan-update.php'; ?>" class="btn btn-default" style="min-width:90px;">
                            <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                        </div>

                        <p><small>* Wajib Diisi</small></p>
                        </form>
                        
                        
                    </div>
                </div>

			<?php $con->close(); ?>
			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
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

<script>
$(document).ready(function(){
	$(".wysiwyg").ckeditor();
	var formValidasiCfg = {
		submitHandler: function(form) {
			$("#loading_modal").modal({keyboard:false, backdrop:'static'});

			for(instance in CKEDITOR.instances){
				CKEDITOR.instances[instance].updateElement();
			}

			if($("#cekkolnup").is(":checked") && $("#nup_fee").val() == ""){
				$("#loading_modal").modal("hide");
				$.validator.showErrorField('nup_fee', "Kolom ini belum diisi atau dipilih");
				setErrorFocus($("#nup_fee"), $("form#gform"), false);
			} else if($("#pesan").val() == ""){
				$("#loading_modal").modal("hide");
				swal.fire({
					allowOutsideClick: false, icon: "warning", width: '350px',
					html:'<p style="font-size:14px; font-family:arial;">Kolom Pesan belum diisi</p>'
				});
			} else{
				form.submit();
			}
		}	
	};
	$("form#gform").validate($.extend(true,{},config.validation,formValidasiCfg));
});		
</script>
</body>
</html>      

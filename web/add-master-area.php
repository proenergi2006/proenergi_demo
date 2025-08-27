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

    if (isset($enk['idr']) && $enk['idr']!== ''){
        $action 	= "update"; 
		$section 	= "Edit Area";
        $idr = isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
        $sql = "select * from pro_master_area where id_master = '".$idr."'";
        $rsm = $con->getRecord($sql);
		$chk = ($rsm['is_active'])?"checked":"";
		$pathAt = $public_base_directory.'/files/uploaded_user/lampiran/'.$rsm['lampiran'];
    } else{ 
		$action 	= "add";
		$section 	= "Tambah Area";
		$chk		= "checked";
	}
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
        		<h1><?php echo $section; ?></h1>
        	</section>
			<section class="content">

				<?php $flash->display(); ?>
                <div class="box box-primary">
                    <div class="box-header with-border bg-light-blue">
                        <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
                    </div>
                    <div class="box-body">
						<form action="<?php echo ACTION_CLIENT.'/master-area.php'; ?>" id="gform" name="gform" method="post" class="form-horizontal" role="form" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-4">Nama Area *</label>
                                    <div class="col-md-8">
										<input type="text" id="nama_area" name="nama_area" class="form-control" required value="<?php echo $rsm['nama_area'];?>" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-4">WAPU *</label>
                                    <div class="col-md-4">
										<select id="cb_wapu" name="cb_wapu" class="form-control select2" required>
                                            <option value=""></option>
                                            <option value="Ya" <?php if($rsm['wapu']=='Ya') echo "selected"; ?> >Ya</option>
                                            <option value="Tidak" <?php if($rsm['wapu']=='Tidak') echo "selected"; ?>>Tidak</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <?php
                                    if($rsm['lampiran'] && file_exists($pathAt)){
                                        $linkAt = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=2&ktg=areaLamp_".$idr."_&file=".$rsm['lampiran_ori']);
                                        echo '<label>Ganti Lampiran</label>';
                                        echo '<p><a href="'.$linkAt.'"><i class="fa fa-file-alt jarak-kanan"></i>'.$rsm['lampiran_ori'].'</a></p>';
                                        echo '<input type="file" name="attach_lampiran" id="attach_lampiran" class="validate[funcCall[fileCheck]]" />';
                                    } else{
                                        echo '<label>Lampiran *</label>';
                                        echo '<input type="file" name="attach_lampiran" id="attach_lampiran" class="validate[funcCall[fileCheck]]" />';
                                    }
                                ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-group-sm">
                                    <div class="col-md-12">
                                        <div class="checkbox">
                                            <label class="rtl">
                                                <input type="checkbox" name="active" id="active" value="1" class="form-control" <?php echo $chk; ?> /> Active
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

                        <div style="margin-bottom:15px;">
                            <input type="hidden" name="act" value="<?php echo $action;?>" />
                            <input type="hidden" name="idr" value="<?php echo $idr;?>" />

                            <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">
                            <i class="fa fa-save jarak-kanan"></i> Simpan</button>
                            <a href="<?php echo BASE_URL_CLIENT.'/master-area.php'; ?>" class="btn btn-default" style="min-width:90px;">
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
	$(".hitung").number(true, 0, ".", ",");
	var formValidasiCfg = {
		submitHandler: function(form) {
			$("#loading_modal").modal({keyboard:false, backdrop:'static'});

			if($("#cekkolnup").is(":checked") && $("#nup_fee").val() == ""){
				$("#loading_modal").modal("hide");
				$.validator.showErrorField('nup_fee', "Kolom ini belum diisi atau dipilih");
				setErrorFocus($("#nup_fee"), $("form#gform"), false);
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

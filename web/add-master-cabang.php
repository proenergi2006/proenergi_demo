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
		$section 	= "Edit Cabang Penagihan";
        $idr = isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
        $sql = "select a.*, b.group_wilayah from pro_master_cabang a join pro_master_group_cabang b on a.id_group_cabang = b.id_gu where a.id_master = '".$idr."'";
        $rsm = $con->getRecord($sql);
		$chk = ($rsm['is_active'])?"checked":"";
    } else{ 
		$action 	= "add";
		$section 	= "Tambah Cabang Penagihan";
		$chk		= "checked";
	}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("formatNumber","ckeditor"))); ?>

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
						<form action="<?php echo ACTION_CLIENT.'/master-cabang.php'; ?>" id="gform" name="gform" method="post" class="form-horizontal" role="form">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-4">Nama Cabang *</label>
                                    <div class="col-md-8">
										<input type="text" id="nama_cabang" name="nama_cabang" class="form-control" required value="<?php echo $rsm['nama_cabang'];?>" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-4">Wilayah *</label>
                                    <div class="col-md-8">
										<?php if($action == "add"){ ?>
                                        <select name="wilayah" id="wilayah" class="form-control select2" required>
                                            <option></option>
                                            <?php $con->fill_select("id_gu","group_wilayah","pro_master_group_cabang","","where is_active=1","id_gu",false); ?>
                                        </select>
                                        <?php } else if($action == "update"){ ?>
                                        <input type="hidden" name="wilayah" id="wilayah" value="<?php echo $rsm['id_group_cabang'];?>" />
                                        <input type="text" id="wilName" name="wilName" class="form-control" value="<?php echo $rsm['group_wilayah'];?>" readonly />
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-4">Kode Barcode *</label>
                                    <div class="col-md-4">
										<input type="text" id="kode_barcode" name="kode_barcode" class="form-control" required value="<?php echo $rsm['kode_barcode'];?>" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-4">Format Nomor *</label>
                                    <div class="col-md-4">
										<input type="text" id="inisial" name="inisial" class="form-control" required value="<?php echo $rsm['inisial_cabang'];?>" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-4">Format Segel *</label>
                                    <div class="col-md-4">
										<input type="text" id="segel" name="segel" class="form-control" required value="<?php echo $rsm['inisial_segel'];?>" />
                                    </div>
                                </div>
                            </div>
                        </div>

						<?php if($action == "add"){ ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-4">Stok Segel *</label>
                                    <div class="col-md-4">
										<input type="text" id="stok" name="stok" class="form-control hitung" required />
                                    </div>
                                </div>
                            </div>
                        </div>
						<?php } else{ ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-4">Stok Segel *</label>
                                    <div class="col-md-4">
                                        <input type="text" id="stok" name="stok" class="form-control hitung" required value="<?php echo $rsm['stok_segel'];?>" readonly />
                                        <p class="help-block" style="margin-bottom:0px; font-size:12px;">
                                        <i>Nomor Segel Terakhir : <?php echo $rsm['inisial_segel']."-".$rsm['urut_segel'];?></i></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-4">Tambah Segel *</label>
                                    <div class="col-md-4">
										<input type="text" id="stokA" name="stokA" class="form-control hitung" />
                                    </div>
                                </div>
                            </div>
                        </div>
						<?php } ?>

                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-12">Catatan</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group form-group-sm">
                                    <div class="col-md-12">
										<textarea id="note" name="note" class="form-control wysiwyg"><?php echo $rsm['catatan_cabang'];?></textarea>
                                    </div>
                                </div>
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
                            <a href="<?php echo BASE_URL_CLIENT.'/master-cabang.php'; ?>" class="btn btn-default" style="min-width:90px;">
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
	$(".wysiwyg").ckeditor();
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

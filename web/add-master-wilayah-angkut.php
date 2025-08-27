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
		$section 	= "Edit Wilayah Angkut";
        $idr = isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
        $sql = "select * from pro_master_wilayah_angkut where id_master = '".$idr."';";
        $rsm = $con->getRecord($sql);
		$chk = ($rsm['is_active'])?"checked":"";
    } else{ 
        $idr = null;
        $rsm = null;
		$action 	= "add";
		$section 	= "Tambah Wilayah Angkut";
		$chk		= "checked";
	}
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
        		<h1><?php echo $section; ?></h1>
        	</section>
			<section class="content">

				<?php $flash->display(); ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                        	<div class="box-header with-border bg-light-blue">
                            	<h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
							</div>
                            <div class="box-body">
                                <form action="<?php echo ACTION_CLIENT.'/master-wilayah-angkut.php'; ?>" id="gform" name="gform" method="post" class="form-validasi" role="form">
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Propinsi *</label>
                                        <select id="propinsi" name="propinsi" class="form-control validate[required] select2">
                                        	<option></option>
                                            <?php $con->fill_select("id_prov","nama_prov","pro_master_provinsi",$rsm['id_prov'],"","nama_prov",false); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Kabupaten *</label>
                                        <select id="kabupaten" name="kabupaten" class="form-control validate[required] select2">
                                        <?php ($action == 'update')?$con->fill_select("id_kab","nama_kab","pro_master_kabupaten",$rsm['id_kab'],"","nama_kab",false):''; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Destinasi *</label>
                                        <input type="text" id="destinasi" name="destinasi" class="form-control validate[required]" value="<?php echo $rsm['wilayah_angkut'] ?? null;?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <div class="checkbox">
                                            <label class="rtl">
                                                <input type="checkbox" name="active" id="active" value="1" class="form-control" <?php echo $chk; ?> /> Active
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <input type="hidden" name="act" value="<?php echo $action;?>" />
                                            <input type="hidden" name="idr" value="<?php echo $idr;?>" />
                                            <a href="<?php echo BASE_URL_CLIENT."/master-wilayah-angkut.php"; ?>" class="btn btn-default jarak-kanan">
                                            <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                                            <button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
										</div>
                                    </div>
                                </div>
                                <hr style="margin:5px 0" />
                                <div class="row">
                                    <div class="col-sm-12"><small>* Wajib Diisi</small></div>
                                </div>
                                </form>
                            </div>
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
		$("select#propinsi").change(function(){
			$("select#kabupaten").val("").trigger('change').select2('close');
			$("select#kabupaten option").remove();
			$("#destinasi").val('');
			$.ajax({
				type	: "POST",
				url		: "./__get_kabupaten.php",
				dataType: 'json',
				data	: { q1 : $("select#propinsi").val() },
				cache	: false,
				success : function(data){ 
					if(data.items != ""){
						$("select#kabupaten").select2({ 
							data 		: data.items, 
							placeholder : "Pilih salah satu", 
							allowClear 	: true, 
						});
						return false;
					}
				}
			});
		});
		$("select#kabupaten").change(function(){
			$("#destinasi").val('');
		});
	});
</script>
</body>
</html>      

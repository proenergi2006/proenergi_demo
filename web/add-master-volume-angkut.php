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
		$section 	= "Edit Master Volume Angkut";
        $idr = isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
        $sql = "select * from pro_master_volume_angkut where id_master = '".$idr."';";
        $rsm = $con->getRecord($sql);
		$chk = ($rsm['is_active'])?"checked":"";
		$vol = $rsm['volume_angkut'];
    } else{ 
		$action 	= "add";
		$section 	= "Tambah Master Volume Angkut";
		$chk		= "checked";
		$vol 		= "";
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
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                        	<div class="box-header with-border bg-light-blue">
                            	<h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
							</div>
                            <div class="box-body">
                                <form action="<?php echo ACTION_CLIENT.'/master-volume-angkut.php'; ?>" id="gform" name="gform" method="post" class="form-validasi" role="form">
                                <div class="form-group row">
                                    <div class="col-sm-4 col-md-3">
                                        <label>Jenis Volume *</label>
                                        <div class="input-group">
                                        	<input type="text" id="volume" name="volume" class="form-control validate[required] hitung" value="<?php echo $vol;?>" />
                                        	<span class="input-group-addon">Liter</span>
                                        </div>
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
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                    	<div class="pad bg-gray">
                                            <input type="hidden" name="act" value="<?php echo $action;?>" />
                                            <input type="hidden" name="idr" value="<?php echo $idr;?>" />
                                            <a href="<?php echo BASE_URL_CLIENT."/master-volume-angkut.php"; ?>" class="btn btn-default jarak-kanan">
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

			<?php $con->close(); ?>
			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
	</div>
<script>
	$(document).ready(function(){
		$(".hitung").number(true, 0, ".", ",");
	});
</script>
</body>
</html>      

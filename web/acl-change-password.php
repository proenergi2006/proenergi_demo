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
        		<h1>Ubah Password</h1>
        	</section>
			<section class="content">

			<?php $flash->display(); ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Silahkan isi form dibawah ini</h3>
                            <?php if (!isset($enk['idr'])): ?>
                            <a href="<?php echo BASE_URL_CLIENT."/acl-change-profil.php"; ?>" class="pull-right"><i class="fa fa-user jarak-kanan"></i>Ubah Profil</a>
                            <?php endif ?>
                        </div>
                        <div class="box-body">
                            <form action="<?php echo ACTION_CLIENT.'/acl-change-password.php'; ?>" id="gform" name="gform" method="post" role="form" class="form-validasi">
                                <?php if (isset($enk['idr'])): ?>
                                <input type="hidden" name="idr" value="<?php echo $enk['idr'] ?>">
                                <?php endif ?>

                                <?php if (!isset($enk['idr'])): ?>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Password Lama *</label>
                                        <input type="password" id="old_pass" name="old_pass" class="form-control validate[required]" />
                                    </div>
                                </div>
                                <?php endif ?>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Password Baru *</label>
                                        <input type="password" id="new_pass" name="new_pass" class="form-control validate[required]" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Konfirmasi Password Baru *</label>
                                        <input type="password" id="confirm_new_pass" name="confirm_new_pass" class="form-control validate[required, funcCall[confirmPassCheck[new_pass]]]" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <a href="<?php echo BASE_URL_CLIENT."/home.php"; ?>" class="btn btn-default jarak-kanan"><i class="fa fa-reply jarak-kanan"></i> Batal</a>
                                            <button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
                                        </div>
                                    </div>
                                </div>
                                <hr style="margin:5px 0" />
                                <div class="clearfix"><div class="col-sm-12"><small>* Wajib Diisi</small></div></div>
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

</body>
</html>      

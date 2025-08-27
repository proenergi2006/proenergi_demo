<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth    = new MyOtentikasi();
$enk      = decode($_SERVER['REQUEST_URI']);
$con     = new Connection();
$flash    = new FlashAlerts;

$akun = "SELECT CONCAT(a.fullname , ' - ', b.inisial_cabang) as fullname, REPLACE(c.role_name, 'Role', '') AS role_name, a.id_user FROM acl_user a JOIN pro_master_cabang b ON a.id_wilayah=b.id_master JOIN acl_role c ON a.id_role=c.id_role WHERE a.id_role IN ('11','17','7','6') AND a.is_active = '1' AND a.id_user NOT IN (
    SELECT id_user FROM pro_non_penerima_incentive
    )";
$res_akun = $con->getResult($akun);

?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS); ?>

<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
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
                                <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan pilih akun</h3>
                            </div>
                            <div class="box-body">
                                <form action="<?php echo ACTION_CLIENT . '/non_penerima_incentive.php'; ?>" id="gform" name="gform" method="post" class="form-validasi" role="form">
                                    <div class="form-group row">
                                        <div class="col-sm-4">
                                            <label>Akun *</label>
                                            <select name="akun" id="akun" class="form-control select2 validate[required]">
                                                <option value=""></option>
                                                <?php foreach ($res_akun as $key) : ?>
                                                    <option value="<?= $key['id_user'] ?>"><?= $key['fullname'] . " (" . $key['role_name'] . " )" ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="pad bg-gray">
                                                <input type="hidden" id="act" name="act" class="form-control" value="add" />
                                                <a href="<?php echo BASE_URL_CLIENT . "/non_penerima_incentive.php"; ?>" class="btn btn-default jarak-kanan">
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
            <?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
        </aside>
    </div>
</body>

</html>

<script>
    $(document).ready(function() {
        $('#persen').on('keypress', function(e) {
            var charCode = e.which ? e.which : e.keyCode;

            // Izinkan hanya angka 0–9 (kode ASCII 48–57)
            if (charCode < 48 || charCode > 57) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>
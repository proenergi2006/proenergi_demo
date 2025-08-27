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
$param     = ($_POST['q1']) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : htmlspecialchars($enk["q1"], ENT_QUOTES);

// Cek peran pengguna
$required_role = ['1', '2', '21', '3', '16'];
// Misalnya halaman ini hanya untuk superadmin
if (!in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), $required_role)) {
    // Pengguna tidak memiliki peran yang tepat, redirect ke halaman lain atau tampilkan pesan akses ditolak
    $flash->add("warning", "Akses ditolak.", BASE_URL_CLIENT . "/home.php");
    // exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("myGrid"))); ?>

<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1>GPS Truck</h1>
            </section>
            <section class="content">

                <form name="searchForm" id="searchForm" method="post" role="form" class="form-horizontal">
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <select id="q1" name="q1" class="form-control">
                                <option></option>
                                <?php $con->fill_select("id_master", "concat(nama_suplier,' - ',nama_transportir,', ',lokasi_suplier)", "pro_master_transportir", $param, "where is_active=1 and tipe_angkutan in(1,3)", "nama_suplier", false); ?>
                            </select>
                        </div>
                        <div class="col-sm-3 col-sm-top">
                            <button type="submit" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch"><i class="fa fa-search jarak-kanan"></i>Search</button>
                        </div>
                    </div>
                </form>

                <?php $flash->display(); ?>
                <form action="<?php echo ACTION_CLIENT . '/gps-truck.php'; ?>" id="gform" name="gform" method="post" role="form">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="box box-info">
                                <div class="box-body table-responsive">
                                    <table class="table table-bordered table-hover" id="table-grid">
                                        <thead>
                                            <tr>
                                                <th class="text-center" width="8%">NO</th>
                                                <th class="text-center" width="17%">NOMOR PLAT</th>
                                                <th class="text-center" width="35%">LINK GPS</th>
                                                <th class="text-center" width="15%">MEMBER CODE</th>
                                                <th class="text-center" width="15%">USERNAME</th>
                                                <th class="text-center" width="10%">PASSWORD</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "select * from pro_master_transportir_mobil where id_transportir = '" . $param . "'";
                                            $res = $con->getResult($sql);
                                            if (count($res) == 0) {
                                                echo '<tr><td class="text-center" colspan="5">Tidak ada data</td></tr>';
                                            } else {
                                                $nom = 0;
                                                foreach ($res as $data) {
                                                    $nom++;
                                                    $idt = $data['id_master'];
                                            ?>
                                                    <tr>
                                                        <td class="text-center"><?php echo $nom; ?></td>
                                                        <td class="text-center"><?php echo $data['nomor_plat']; ?></td>
                                                        <td><input type="text" name="<?php echo 'link_gps[' . $idt . ']'; ?>" id="<?php echo 'link_gps' . $idt; ?>" class="form-control input-sm" value="<?php echo $data['link_gps']; ?>" /></td>
                                                        <td><input type="text" name="<?php echo 'membercode_gps[' . $idt . ']'; ?>" id="<?php echo 'membercode_gps' . $idt; ?>" class="form-control input-sm" value=" <?php echo $data['membercode_gps']; ?>" /></td>
                                                        <td><input type="text" name="<?php echo 'user_gps[' . $idt . ']'; ?>" id="<?php echo 'user_gps' . $idt; ?>" class="form-control input-sm" value=" <?php echo $data['user_gps']; ?>" /></td>
                                                        <td><input type="text" name="<?php echo 'pass_gps[' . $idt . ']'; ?>" id="<?php echo 'pass_gps' . $idt; ?>" class="form-control input-sm" value="<?php echo $data['pass_gps']; ?>" /></td>
                                                    </tr>
                                            <?php }
                                            } ?>
                                        </tbody>
                                    </table>
                                    <?php if (count($res) > 0) { ?>
                                        <div class="pad bg-gray">
                                            <input type="hidden" name="idr" value="<?php echo $param; ?>" />
                                            <button type="reset" class="btn btn-default jarak-kanan" name="btnReset" id="btnReset"><i class="fa fa-retweet jarak-kanan"></i>Reset</button>
                                            <button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

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
            <?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
        </aside>
    </div>

    <script>
        $(document).ready(function() {
            $("select#q1").select2({
                placeholder: "Transportir",
                allowClear: true
            });
            var objAttach = {
                onValidationComplete: function(form, status) {
                    if (status == true) {
                        $('#loading_modal').modal({
                            backdrop: "static"
                        });
                        form.validationEngine('detach');
                        form.submit();
                    }
                }
            };
            $("form#gform").validationEngine('attach', objAttach);
            $("form#searchForm").validationEngine('attach', objAttach);
        });
    </script>
</body>

</html>
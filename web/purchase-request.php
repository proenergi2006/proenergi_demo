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
$sesgr    = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);
if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 3) {
    $sqljum    = "select count(id_pr) as jum from pro_pr where ceo_result = 0 and disposisi_pr = 4 and is_ceo = 1";
    $jumlah = $con->getOne($sqljum);
} else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 4) {
    $sqljum    = "select count(id_pr) as jum from pro_pr where cfo_result = 0 and disposisi_pr = 4";
    $jumlah = $con->getOne($sqljum);
}

// Cek peran pengguna
$required_role = ['1', '2', '7', '21', '3', '10', '9', '16', '6', '5'];
// Misalnya halaman ini hanya untuk superadmin
if (!in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), $required_role)) {
    // Pengguna tidak memiliki peran yang tepat, redirect ke halaman lain atau tampilkan pesan akses ditolak
    $flash->add("warning", "Akses ditolak.", BASE_URL_CLIENT . "/home.php");
    // exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("myGrid", "jqueryUI"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1>Delivery Request</h1>
            </section>
            <section class="content">

                <?php $flash->display(); ?>
                <div class="alert alert-danger alert-dismissible" style="display:none">
                    <div class="box-tools">
                        <button data-alert="remove" class="btn btn-box-tool close" type="button"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <form name="searchForm" id="searchForm" role="form" class="form-horizontal">
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <input type="text" class="form-control input-sm" placeholder="Keywords" name="q1" id="q1" />
                        </div>
                        <div class="col-sm-4 col-sm-top">
                            <select id="q3" name="q3" class="form-control">
                                <option></option>
                                <option value="1">Admin Finance</option>
                                <option value="1.5">Pending Due AR</option>
                                <option value="2">Verifikasi BM</option>
                                <option value="3">Verifikasi Purchasing</option>
                                <option value="4">Verifikasi COO</option>
                                <option value="5">Verifikasi CEO</option>
                                <option value="6">Terverifikasi</option>
                                <option value="7">Purchase Order</option>
                                <option value="8">Closed</option>
                            </select>
                        </div>
                        <div class="col-sm-4 col-sm-top">
                            <select id="q2" name="q2" class="form-control">
                                <option></option>
                                <?php $con->fill_select("id_master", "nama_cabang", "pro_master_cabang", '', "where is_active=1 and id_master <> 1", "nama_cabang", false); ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">Periode</span>
                                <input type="text" name="q4" id="q4" class="form-control input-sm validate[required,custom[date]] datepicker" autocomplete='off' />
                            </div>
                        </div>
                        <div class="col-sm-3 col-sm-top">
                            <div class="input-group">
                                <span class="input-group-addon">S/D</span>
                                <input type="text" name="q5" id="q5" class="form-control input-sm validate[required,custom[date]] datepicker" autocomplete='off' />
                            </div>
                        </div>
                        <div class="col-sm-6 col-sm-top">
                            <button type="submit" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch"><i class="fa fa-search jarak-kanan"></i> Search</button>
                            <?php if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("5", "10"))) { ?>
                                <a href="javascript:;" id="btnExport" class="btn btn-success btn-sm jarak-kanan">Export Data</a>
                            <?php } ?>
                            <!-- <a class="btn btn-action btn-info input-sm" title="Jadwal" href="<?php echo BASE_URL_CLIENT; ?>/calender_admin.php" style="margin-left: 25px;"><i class="fa fa-info-circle"></i> Lihat Jadwal</a> -->
                        </div>
                    </div>
                </form>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <div class="row">
                                    <?php if (!in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array(3, 4))) { ?>
                                        <div class="col-sm-12">
                                            <div class="text-right" style="margin-top: 10px">Show
                                                <select name="tableGridLength" id="tableGridLength">
                                                    <option value="10">10</option>
                                                    <option value="25" selected>25</option>
                                                    <option value="50">50</option>
                                                    <option value="100">100</option>
                                                </select> Data
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <div class="col-sm-6">
                                            <a href="<?php echo BASE_URL_CLIENT . '/purchase-request-detail-all.php'; ?>" class="btn btn-primary">Unverified Data
                                                <?php echo ($jumlah > 0) ? '<span class="label">' . $jumlah . '</span>' : ''; ?></a>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="text-right" style="margin-top: 10px">Show
                                                <select name="tableGridLength" id="tableGridLength">
                                                    <option value="10" selected>10</option>
                                                    <option value="25">25</option>
                                                    <option value="50">50</option>
                                                    <option value="100">100</option>
                                                </select> Data
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="box-body table-responsive">
                                <table class="table table-bordered" id="table-grid">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="5%">No</th>
                                            <th class="text-center" width="12%">Tanggal DR</th>
                                            <th class="text-center" width="18%">Kode DR</th>
                                            <th class="text-center" width="25%">Customer</th>
                                            <th class="text-center" width="15%">Nomor PO</th>
                                            <th class="text-center" width="10%">Volume</th>
                                            <th class="text-center" width="12%">Tanggal Kirim</th>
                                            <th class="text-center" width="18%">Cabang Penagih</th>
                                            <th class="text-center" width="18%">Disposisi</th>
                                            <th class="text-center" width="6%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
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
            $("select#q2").select2({
                placeholder: "Cabang Penagih",
                allowClear: true
            });
            $("select#q3").select2({
                placeholder: "Status",
                allowClear: true
            });
            $("#table-grid").ajaxGrid({
                url: "./datatable/purchase-request.php",
                data: {
                    q1: $("#q1").val(),
                    q2: $("#q2").val(),
                    q3: $("#q3").val(),
                    q4: $("#q4").val(),
                    q5: $("#q5").val(),
                    q6: $("#q6").val()
                },
            });

            $('#btnExport').on('click', function() {
                let url = '<?php echo BASE_URL_CLIENT ?>/report/e-rekap-dr-xls.php?name=rekap-dr&' + $('#searchForm').serialize();

                window.open(url, '_blank');
            });

            $('#btnSearch').on('click', function() {
                $("#table-grid").ajaxGrid("draw", {
                    data: {
                        q1: $("#q1").val(),
                        q2: $("#q2").val(),
                        q3: $("#q3").val(),
                        q4: $("#q4").val(),
                        q5: $("#q5").val(),
                        q6: $("#q6").val()
                    }
                });
                return false;
            });
            $('#tableGridLength').on('change', function() {
                $("#table-grid").ajaxGrid("pageLen", $(this).val());
            });
        });
    </script>
</body>

</html>
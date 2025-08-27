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
                <h1>Delivery Schedule</h1>
            </section>
            <section class="content">

                <?php $flash->display(); ?>
                <form name="searchForm" id="searchForm" role="form" class="form-horizontal">
                    <div class="form-group row">
                        <div class="col-sm-4 col-md-2">
                            <select name="q2" id="q2">
                                <option></option>
                                <option value="0">PO</option>
                                <option value="1">LOCO</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-4 col-sm-top">
                            <div class="input-group">
                                <input type="text" class="form-control input-sm" placeholder="Keywords" name="q1" id="q1" />
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </div>
                        <a class="btn btn-action btn-info input-sm" title="Schedule By Date" href="<?php echo BASE_URL_CLIENT; ?>/report/l-schedule-by-date.php"><i class="fa fa-info-circle"></i> Schedule By Date</a>
                    </div>
                </form>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <div class="row">
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
                                </div>
                            </div>
                            <div class="box-body table-responsive">
                                <table class="table table-bordered table-hover" id="table-grid">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="3%">No</th>
                                            <th class="text-center" width="10%">Tanggal DS</th>
                                            <th class="text-center" width="20%">Kode DS</th>
                                            <th class="text-center" width="15%">Nomor PO</th>
                                            <th class="text-center" width="25%">Customer</th>
                                            <th class="text-center" width="15%">Tanggal Kirim</th>
                                            <th class="text-center" width="10%">Volume PO</th>
                                            <th class="text-center" width="10%">Nomor DO</th>
                                            <th class="text-center" width="10%">Cabang</th>
                                            <th class="text-center" width="24%">Terminal</th>
                                            <th class="text-center" width="5%">Aksi</th>
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
                placeholder: "Jenis DS",
                allowClear: true
            });
            $("#table-grid").ajaxGrid({
                url: "./datatable/delivery-loading.php",
                data: {
                    q1: $("#q1").val(),
                    q2: $("#q2").val()
                },
            });
            $('#btnSearch').on('click', function() {
                $("#table-grid").ajaxGrid("draw", {
                    data: {
                        q1: $("#q1").val(),
                        q2: $("#q2").val()
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
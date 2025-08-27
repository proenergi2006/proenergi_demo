<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth   = new MyOtentikasi();
$enk    = decode($_SERVER['REQUEST_URI']);
$con    = new Connection();
$flash  = new FlashAlerts;
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
                <h1>Peminjaman Mobil Master</h1>
            </section>
            <section class="content">

                <?php $flash->display(); ?>
                <form name="searchForm" id="searchForm" role="form" class="form-horizontal">
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <input type="text" class="form-control input-sm" name="q1" id="q1" placeholder="Keywords..." />
                        </div>
                        <div class="col-sm-6 col-sm-top">
                            <button type="submit" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch"><i class="fa fa-search jarak-kanan"></i> Search</button>
                        </div>
                    </div>
                    <p style="font-size:12px;"><i>* Keywords berdasarkan plat mobil</i></p>
                </form>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <a href="<?php echo BASE_URL_CLIENT . '/peminjaman-mobil-master-add.php'; ?>" class="btn btn-primary"><i class="fa fa-plus jarak-kanan"></i> Add Data</a>
                                    </div>
                                    <div class="col-sm-6">
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
                                <table class="table table-bordered table-hover" id="data-peminjaman-mobil-master-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="70">No</th>
                                            <th class="text-center" width="250">Nama Cabang</th>
                                            <th class="text-center" width="">Nama Mobil</th>
                                            <th class="text-center" width="200">Plat Mobil</th>
                                            <th class="text-center" width="100">Status</th>
                                            <th class="text-center" width="100">Aksi</th>
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

    <style>
        #data-peminjaman-mobil-master-table td,
        #data-peminjaman-mobil-master-table th {
            font-size: 14px;
        }
    </style>
    <script>
        $(document).ready(function() {
            $("#data-peminjaman-mobil-master-table").ajaxGrid({
                url: "./datatable/peminjaman-mobil-master.php",
                data: {
                    q1: $("#q1").val(),
                    q2: $("#q2").val()
                },
            });
            $('#btnSearch').on('click', function() {
                var param = {
                    q1: $("#q1").val(),
                    q2: $("#q2").val()
                };
                $("#data-peminjaman-mobil-master-table").ajaxGrid("draw", {
                    data: param
                });
                return false;
            });
            $('#tableGridLength').on('change', function() {
                $("#data-peminjaman-mobil-master-table").ajaxGrid("pageLen", $(this).val());
            });
            $('#data-peminjaman-mobil-master-table').on('click', '[data-action="deleteGrid"]', function(e) {
                e.preventDefault();
                if (confirm("Apakah anda yakin ?")) {
                    var param = $(this).data("param-idx");
                    var handler = function(data) {
                        if (data.error == "") {
                            $(".alert").slideUp();
                            $("#data-peminjaman-mobil-master-table").ajaxGrid("draw");
                        } else {
                            $(".alert").slideUp();
                            var a = $(".alert > .box-tools");
                            a.next().remove();
                            a.after("<p>" + data.error + "</p>");
                            $(".alert").slideDown();
                        }
                    };
                    $.post("./datatable/deleteTable.php", {
                        param: param
                    }, handler, "json");
                }
            });
        });
    </script>
</body>

</html>
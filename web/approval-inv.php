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
                <h1>Master Approval Invoice</h1>
            </section>
            <section class="content">

                <?php $flash->display(); ?>
                <div class="alert alert-danger alert-dismissible" style="display:none">
                    <div class="box-tools">
                        <button data-alert="remove" class="btn btn-box-tool close" type="button"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <form name="searchForm" id="searchForm" role="form">
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <label class="sr-only"></label>
                            <input type="text" class="form-control input-sm" name="q1" id="q1" placeholder="Keywords" />
                        </div>
                        <div class="col-sm-8 col-sm-top">
                            <button type="submit" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch"><i class="fa fa-search jarak-kanan"></i>Search</button>
                        </div>
                    </div>
                    <p style="font-size:12px;"><i>* Keywords berdasarkan nama approval</i></p>
                </form>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <a href="<?php echo BASE_URL_CLIENT . '/add-approval-invoice.php'; ?>" class="btn btn-primary">
                                            <i class="fa fa-plus jarak-kanan"></i>Add Approval
                                        </a>
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
                                <table class="table table-bordered table-hover" id="table-grid">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="80">No</th>
                                            <th class="text-center" width="200">Nama</th>
                                            <th class="text-center" width="150">Jabatan</th>
                                            <th class="text-center" width="150">Cabang</th>
                                            <th class="text-center" width="150">Status</th>
                                            <th class="text-center" width="150">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="loading_modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
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

    <style>
        #table-grid td,
        #table-grid th {
            font-size: 12px;
        }
    </style>
    <script>
        $(document).ready(function() {
            $("select#q2").select2({
                placeholder: "Status",
                allowClear: true
            });
            $("select#q3").select2({
                placeholder: "Nama Cabang",
                allowClear: true
            });
            $("select#q4").select2({
                placeholder: "Role",
                allowClear: true
            });
            $("#table-grid").ajaxGrid({
                url: "./datatable/approval-inv.php",
                data: {
                    q1: $("#q1").val(),
                    q2: $("#q2").val(),
                    q3: $("#q3").val(),
                    q4: $("#q4").val()
                },
            });
            $('#btnSearch').on('click', function() {
                $("#table-grid").ajaxGrid("draw", {
                    data: {
                        q1: $("#q1").val(),
                        q2: $("#q2").val(),
                        q3: $("#q3").val(),
                        q4: $("#q4").val()
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
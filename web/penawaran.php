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
                <h1>Penawaran</h1>
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
                        <div class="col-sm-3">
                            <input type="text" class="form-control input-sm" name="q1" id="q1" placeholder="Keywords..." />
                        </div>
                        <div class="col-sm-3 col-sm-top">
                            <select id="q2" name="q2" class="form-control">
                                <option></option>
                                <?php $con->fill_select("id_master", "nama_cabang", "pro_master_cabang", $rsm['id_cabang'], "where is_active=1 and id_master <> 1", "", false); ?>
                            </select>
                        </div>
                        <div class="col-sm-2 col-sm-top">
                            <select id="q3" name="q3" class="form-control">
                                <option></option>
                                <?php $con->fill_select("id_master", "nama_area", "pro_master_area", "", "where is_active=1", "", false); ?>
                            </select>
                        </div>
                        <div class="col-sm-2 col-sm-top">
                            <select id="q4" name="q4" class="form-control">
                                <option value=""> All Status</option>
                                <option value="1">Disetujui</option>
                                <option value="2">Ditolak</option>
                                <option value="3">Terdaftar</option>
                                <option value="4">Verifikasi</option>
                            </select>
                        </div>
                        <div class="col-sm-1 col-sm-top">
                            <button type="submit" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch"><i class="fa fa-search jarak-kanan"></i>Search</button>
                        </div>
                    </div>
                </form>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <a href="<?php echo BASE_URL_CLIENT . '/penawaran-add.php'; ?>" class="btn btn-primary">
                                            <i class="fa fa-plus jarak-kanan"></i>Add Data
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
                                            <th class="text-center" width="70">No</th>
                                            <th class="text-center" width="200">No. Ref</th>
                                            <th class="text-center" width="250">Customer</th>
                                            <th class="text-center" width="100">Cabang Invoice</th>
                                            <th class="text-center" width="80">Area</th>
                                            <th class="text-center" width="80">Volume</th>
                                            <th class="text-center" width="180">Disposisi</th>
                                            <th class="text-center" width="80">Status PO</th>
                                            <th class="text-center" width="130">Aksi</th>
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
        #table-grid td,
        #table-grid th {
            font-size: 12px;
        }
    </style>
    <script>
        $(document).ready(function() {
            $("select#q2").select2({
                placeholder: "Cabang Invoice",
                allowClear: true
            });
            $("select#q3").select2({
                placeholder: "Area",
                allowClear: true
            });
            $("select#q4").select2({
                placeholder: "Status",
                allowClear: true
            });

            $("#table-grid").ajaxGrid({
                url: "./datatable/penawaran.php",
                data: {
                    q1: $("#q1").val(),
                    q2: $("#q2").val(),
                    q3: $("#q3").val()
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
            $('#table-grid tbody').on('click', '[data-action="deleteGrid"]', function(e) {
                e.preventDefault();
                if (confirm("Apakah anda yakin ?")) {
                    var param = $(this).data("param-idx");
                    var handler = function(data) {
                        if (data.error == "") {
                            $(".alert").slideUp();
                            $("#table-grid").ajaxGrid("draw");
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
        // $(document).on('click','#trigger_noanim').flyout({
        //         //title: 'My Flyout without animation',
        //         title: '',
        //         content: '<i>My flyout contents!</i> It works! kljsdkfljkls dfks djfkl sjdkfj skd fksdfjks d',
        //         html: true,
        //         animation: false
        // });
    </script>
</body>

</html>
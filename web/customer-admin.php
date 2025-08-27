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

// Cek peran pengguna
$required_role = ['1', '2', '7', '21', '4', '3', '6'];
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
                <h1>Customer</h1>
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
                            <input type="text" class="form-control input-sm" name="q1" id="q1" placeholder="Keywords..." />
                        </div>
                        <div class="col-sm-4 col-sm-top">
                            <select id="q2" name="q2" class="form-control">
                                <option></option>
                                <option value="1">Prospek</option>
                                <option value="2">Tetap</option>
                            </select>
                        </div>
                        <div class="col-sm-4 col-sm-top">
                            <select id="q3" name="q3" class="form-control">
                                <option></option>
                                <?php $con->fill_select("id_user", "fullname", "acl_user", '', "where is_active=1 and id_role in (11,17)", "fullname", false); ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <select id="q4" name="q4" class="form-control">
                                <option></option>
                                <?php $con->fill_select("id_master", "nama_cabang", "pro_master_cabang", '', "where is_active=1 and id_master <> 1", "nama_cabang", false); ?>
                            </select>
                        </div>


                        <div class="col-sm-3">
                            <select id="q6" name="q6" class="form-control">
                                <option></option>
                                <option value="1">> 1 Bulan</option>
                                <option value="2">
                                    < 1 Bulan</option>
                            </select>
                        </div>

                        <div class="col-sm-3">
                            <select id="q5" name="q5" class="form-control">
                                <option></option>
                                <option value="1">> 6 Bulan</option>
                                <option value="2">
                                    < 6 Bulan</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <button type="submit" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch"><i class="fa fa-search jarak-kanan"></i>Search</button>
                        </div>
                    </div>
                    <p style="font-size:12px;"><i>* Keywords berdasarkan nama dan kode customer</i></p>
                </form>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="text-right" style="margin-top: 10px">Show
                                            <select name="tableGridLength" id="tableGridLength">
                                                <option value="10" selected>10</option>
                                                <option value="25">25</option>
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
                                            <th class="text-center" width="150">Nama Customer</th>
                                            <th class="text-center" width="">Alamat Customer</th>
                                            <th class="text-center" width="150">Cabang Penagihan</th>
                                            <th class="text-center" width="180">Status</th>
                                            <th class="text-center" width="100">User</th>

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
                placeholder: "Status Customer",
                allowClear: true
            });
            $("select#q3").select2({
                placeholder: "Nama Marketing",
                allowClear: true
            });
            /* Lasamba */
            $("select#q4").select2({
                placeholder: "Cabang Penagih",
                allowClear: true
            });
            $("select#q5").select2({
                placeholder: "Last Order",
                allowClear: true
            });
            $("select#q6").select2({
                placeholder: "Last Quotation",
                allowClear: true
            });

            $("#table-grid").ajaxGrid({
                url: "./datatable/customer-admin.php",
                data: {
                    q1: $("#q1").val(),
                    q2: $("#q2").val(),
                    q3: $("#q3").val(),
                    q4: $("#q4").val(),
                    q5: $("#q5").val(),
                    q6: $("#q6").val()
                },
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
    </script>
</body>

</html>
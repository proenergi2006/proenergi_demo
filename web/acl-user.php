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
                <h1>User Management</h1>
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
                        <div class="col-sm-4 col-sm-top">
                            <label class="sr-only"></label>
                            <select id="q2" name="q2" class="form-control">
                                <option value="1" selected>Active</option>
                                <option value="0">Not Active</option>
                                <option value="2">All</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="sr-only"></label>
                            <select id="q4" name="q4" class="form-control">
                                <option value=""></option>
                                <?php $con->fill_select("id_role", "role_name", "acl_role", "", "where is_active = 1", "no_urut", false); ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4 col-sm-top">
                            <label class="sr-only"></label>
                            <select id="q3" name="q3" class="form-control">
                                <option value=""></option>
                                <?php $con->fill_select("id_master", "nama_cabang", "pro_master_cabang", "", "where is_active = 1", "id_master", false); ?>
                            </select>
                        </div>
                        <div class="col-sm-8 col-sm-top">
                            <button type="submit" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch"><i class="fa fa-search jarak-kanan"></i>Search</button>
                        </div>
                    </div>
                    <p style="font-size:12px;"><i>* Keywords berdasarkan username dan fullname</i></p>
                </form>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <a href="<?php echo BASE_URL_CLIENT . '/add-acl-user.php'; ?>" class="btn btn-primary">
                                            <i class="fa fa-plus jarak-kanan"></i>Add User
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
                                            <th class="text-center" width="80">NO</th>
                                            <th class="text-center" width="200">USERNAME</th>
                                            <th class="text-center" width="">FULL NAME</th>
                                            <th class="text-center" width="150">LAST LOGIN</th>
                                            <th class="text-center" width="150">LAST IP</th>
                                            <th class="text-center" width="200">ROLE NAME</th>
                                            <th class="text-center" width="150">STATUS</th>
                                            <th class="text-center" width="150">IMAGE</th>
                                            <th class="text-center" width="250">LAST LOCATION</th>
                                            <th class="text-center" width="150">ACTIONS</th>
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

                <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-blue">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Users who get this role</h4>
                            </div>
                            <div class="modal-body"></div>
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
                url: "./datatable/user.php",
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
            $(document).on("click", ".konfirmasi", function() {
                if (confirm("Apakah anda yakin untuk me-reset password user ini ?\npassword akan dikirim ke email user")) {
                    $("#loading_modal").modal();
                } else {
                    $("#loading_modal").modal("hide");
                    return false;
                }
            });
            $('#table-grid tbody').on('click', '[data-action="deleteGrid"], [data-action="staAcc"]', function(e) {
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
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
$required_role = ['1', '11', '18', '17'];
// Misalnya halaman ini hanya untuk superadmin
if (!in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), $required_role)) {
    // Pengguna tidak memiliki peran yang tepat, redirect ke halaman lain atau tampilkan pesan akses ditolak
    $flash->add("warning", "Akses ditolak.", BASE_URL_CLIENT . "/home.php");
    // exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("formatNumber", "jqueryUI", "myGrid"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1>Refund</h1>
            </section>
            <section class="content">

                <?php $flash->display(); ?>
                <div class="alert alert-danger alert-dismissible" style="display:none">
                    <div class="box-tools">
                        <button data-alert="remove" class="btn btn-box-tool close" type="button"><i class="fa fa-times"></i></button>
                    </div>
                </div>

                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#data-truck" aria-controls="data-truck" role="tab" data-toggle="tab">Truck</a></li>
                    <li role="presentation" class=""><a href="#data-kapal" aria-controls="data-kapal" role="tab" data-toggle="tab">Kapal</a></li>
                </ul>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="data-truck">
                        <form name="searchForm" id="searchForm" role="form" class="form-horizontal">
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <input type="text" class="form-control input-sm" name="q1" id="q1" placeholder="Keywords..." />
                                </div>
                                <div class="col-sm-3 col-sm-top">
                                    <select class="form-control select2" name="q3" id="q3">
                                        <option></option>
                                        <option value="0">PROGRESS</option>
                                        <option value="1">PAID</option>
                                    </select>
                                </div>
                                <div class="col-sm-4 col-sm-top">
                                    <button type="submit" class="btn btn-info btn-sm" name="btnSearch1" id="btnSearch1"><i class="fa fa-search jarak-kanan"></i>Search</button>
                                </div>
                            </div>
                            <p style="font-size:12px; margin-top:-10px;"><i>Keywords berdasarkan customer, Nomor PO dan Nomor Invoice</i></p>
                        </form>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="box box-info">
                                    <div class="box-header with-border">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="text-right" style="margin-top: 10px">Show
                                                    <select name="tableGridLength1" id="tableGridLength1">
                                                        <option value="10" selected>10</option>
                                                        <option value="25">25</option>
                                                        <option value="50">50</option>
                                                        <option value="100">100</option>
                                                    </select> Data
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="overflow-x: auto" id="table-long">
                                        <div style="width:1600px; height:auto;">
                                            <div class="table-responsive-satu">
                                                <table class="table table-bordered" id="data-truck-table" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" width="2%">No</th>
                                                            <th class="text-center" width="5%">No Invoice</th>
                                                            <th class="text-center" width="13%">Customer</th>
                                                            <th class="text-center" width="10%">No. PO Customer<br />Tgl, Jam dan Vol Terkirim</th>
                                                            <th class="text-center" width="8%">Tanggal Terbit Invoice</th>
                                                            <th class="text-center" width="15%">TOP dan Tgl Pembayaran Invoice</th>
                                                            <th class="text-center" width="3%">Refund</th>
                                                            <th class="text-center" width="20%">Overdue</th>
                                                            <th class="text-center" width="5%">Total Refund</th>
                                                            <th class="text-center" width="5%">Total Bayar</th>
                                                            <th class="text-center" width="7%">Status</th>
                                                            <th class="text-center" width="10%">&nbsp;</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="data-kapal">
                        <form name="searchForm2" id="searchForm2" role="form" class="form-horizontal">
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <input type="text" class="form-control input-sm" name="q4" id="q4" placeholder="Keywords..." />
                                </div>
                                <div class="col-sm-2 col-sm-top">
                                    <input type="text" class="form-control input-sm datepicker" name="q5" id="q5" placeholder="Tanggal Terkirim" autocomplete="off" />
                                </div>
                                <div class="col-sm-3 col-sm-top">
                                    <select class="form-control select2" name="q6" id="q6">
                                        <option></option>
                                        <option value="0">Diproses</option>
                                        <option value="1">Terbayar</option>
                                    </select>
                                </div>
                                <div class="col-sm-4 col-sm-top">
                                    <button type="submit" class="btn btn-info btn-sm" name="btnSearch2" id="btnSearch2"><i class="fa fa-search jarak-kanan"></i>Search</button>
                                </div>
                            </div>
                            <p style="font-size:12px; margin-top:-10px;"><i>Keywords berdasarkan customer dan Nomor PO</i></p>
                        </form>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="box box-info">
                                    <div class="box-header with-border">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="text-right" style="margin-top: 10px">Show
                                                    <select name="tableGridLength2" id="tableGridLength2">
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
                                        <table class="table table-bordered" id="data-kapal-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" width="8%">No</th>
                                                    <th class="text-center" width="16%">Customer</th>
                                                    <th class="text-center" width="22%">Cabang/ Alamat Kirim/ Wilayah OA</th>
                                                    <th class="text-center" width="16%">No. PO Customer<br />Tgl, Jam dan Vol Terkirim</th>
                                                    <th class="text-center" width="6%">Refund</th>
                                                    <th class="text-center" width="10%">Total Refund</th>
                                                    <th class="text-center" width="17%">Status</th>
                                                    <th class="text-center" width="5%">&nbsp;</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
    <style type="text/css">
        .badge {
            display: inline-block;
            padding: 0.5em 1em;
            border-radius: 0.5em;
            color: #fff;
            font-size: 0.9em;
            margin: 0.5em;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-warning {
            background-color: #ffc107;
        }

        .badge-info {
            background-color: #34aeeb;
        }

        .badge-info {
            background-color: #050df2;
        }

        .badge-error {
            background-color: #dc3545;
        }

        .badge:hover {
            opacity: 0.9;
        }
    </style>
    <script>
        $(document).ready(function() {
            $("#data-truck-table").ajaxGrid({
                url: "./datatable/refund-truck-mkt.php",
                data: {
                    q1: $("#q1").val(),
                    q2: $("#q2").val(),
                    q3: $("#q3").val()
                },
            });
            $("#btnSearch1").on("click", function() {
                $("#data-truck-table").ajaxGrid("draw", {
                    data: {
                        q1: $("#q1").val(),
                        q2: $("#q2").val(),
                        q3: $("#q3").val()
                    }
                });
                return false;
            });
            $('#tableGridLength1').on('change', function() {
                $("#data-truck-table").ajaxGrid("pageLen", $(this).val());
            });

            $("[data-toggle='tab']").click(function() {
                var $this = $(this);
                var idnya = $this.attr('href');
                var urlnya = (idnya == "#data-truck") ? "./datatable/refund-truck-mkt.php" : "./datatable/refund-kapal.php";
                $(idnya + "-table").ajaxGrid({
                    url: urlnya,
                    data: {
                        q1: "",
                        q2: "",
                        q3: ""
                    },
                });
                $this.tab('show');
                return false;
            });

            $("#btnSearch2").on("click", function() {
                $("#data-kapal-table").ajaxGrid("draw", {
                    data: {
                        q1: $("#q4").val(),
                        q2: $("#q5").val(),
                        q3: $("#q6").val()
                    }
                });
                return false;
            });
            $('#tableGridLength2').on('change', function() {
                $("#data-kapal-table").ajaxGrid("pageLen", $(this).val());
            });
        });
    </script>
</body>

</html>
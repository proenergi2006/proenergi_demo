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

$id_role = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);

// Cek peran pengguna
$required_role = ['1', '2', '23', '21', '11', '17'];
// Misalnya halaman ini hanya untuk superadmin
if (!in_array($id_role, $required_role)) {
    // Pengguna tidak memiliki peran yang tepat, redirect ke halaman lain atau tampilkan pesan akses ditolak
    $flash->add("warning", "Akses ditolak.", BASE_URL_CLIENT . "/home.php");
    // exit();
}

$query = "SELECT * FROM pro_master_cabang WHERE is_active = '1' AND id_master NOT IN('1','10') ORDER BY nama_cabang ASC";
$cabang = $con->getResult($query);
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
                <h1>Incentive</h1>
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
                        <!-- <div class="col-sm-2 col-sm-top">
                                    <input type="text" class="form-control input-sm datepicker" name="q2" id="q2" placeholder="Tanggal Terkirim" autocomplete="off" />
                                </div> -->
                        <div class="col-sm-3 col-sm-top">
                            <select class="form-control select2" name="q3" id="q3">
                                <option></option>
                                <option value="0">INVOICE NOT YET</option>
                                <option value="1">INVOICE PAID</option>
                                <option value="2">PROSES PENGAJUAN PERSETUJUAN CEO</option>
                                <option value="3">APPROVED BY CEO</option>
                            </select>
                        </div>
                        <?php if ($id_role == '23') : ?>
                            <div class="col-sm-3 col-sm-top">
                                <select name="cabang" id="cabang" class="form-control input-sm">
                                    <option value="">Semua Cabang</option>
                                    <?php foreach ($cabang as $key) : ?>
                                        <option value="<?= $key['id_master'] ?>"><?= ucwords($key['nama_cabang']) ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        <?php endif ?>
                        <div class="col-sm-1 col-sm-top">
                            <button type="submit" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch"><i class="fa fa-search jarak-kanan"></i>Search</button>
                        </div>
                    </div>
                    <?php if ($id_role == '23') : ?>
                        <p style="font-size:12px; margin-top:-10px;"><i>Keywords berdasarkan Nama Customer, Nomor Invoice dan Nama Marketing / KAE</i></p>
                    <?php else: ?>
                        <p style="font-size:12px; margin-top:-10px;"><i>Keywords berdasarkan Nama Customer, Nomor Invoice</i></p>
                    <?php endif ?>
                </form>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <?php if ($id_role == '23') : ?>
                                            <a href="<?php echo BASE_URL_CLIENT . '/incentive_add.php'; ?>" class="btn btn-primary">
                                                <i class="fas fa-folder-open jarak-kanan"></i>Ajukan Persetujuan
                                            </a>
                                        <?php endif ?>
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
                                </div>
                            </div>
                            <div style="overflow-x: auto" id="table-long">
                                <div style="width:2000px; height:auto;">
                                    <div class="table-responsive-satu">
                                        <table class="table table-bordered" id="data-incentive" width="100%">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" width="30px">No</th>
                                                    <th class="text-center" width="50px">No Invoice</th>
                                                    <th class="text-center" width="200px">Customer</th>
                                                    <th class="text-center" width="120px">Marketing</th>
                                                    <th class="text-center" width="150px">No. PO Customer<br />Tgl, Jam dan Vol Terkirim</th>
                                                    <th class="text-center" width="80px">Harga Dasar</th>
                                                    <th class="text-center" width="100px">Harga Tier</th>
                                                    <th class="text-center" width="80px">Tier</th>
                                                    <th class="text-center" width="170px">Tanggal Invoice, TOP, </br> Volume Invoice</th>
                                                    <th class="text-center" width="50px">Point</th>
                                                    <th class="text-center" width="250px">Overdue</th>
                                                    <th class="text-center" width="100px">Total Incentive</th>
                                                    <th class="text-center" width="100px">Status</th>
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
            $("#data-incentive").ajaxGrid({
                url: "./datatable/incentive.php",
                data: {
                    q1: $("#q1").val(),
                    q2: $("#q2").val(),
                    q3: $("#q3").val(),
                    cabang: $("#cabang").val()
                },
            });
            $("#btnSearch").on("click", function() {
                $("#data-incentive").ajaxGrid("draw", {
                    data: {
                        q1: $("#q1").val(),
                        q2: $("#q2").val(),
                        q3: $("#q3").val(),
                        cabang: $("#cabang").val()
                    }
                });
                return false;
            });
            $('#tableGridLength').on('change', function() {
                $("#data-incentive").ajaxGrid("pageLen", $(this).val());
            });
        });
    </script>
</body>

</html>
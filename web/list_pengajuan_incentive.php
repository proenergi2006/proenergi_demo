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
$required_role = ['1', '23', '21'];
// Misalnya halaman ini hanya untuk superadmin
if (!in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), $required_role)) {
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
                            <label for="">Keywords Pencarian</label>
                            <input type="text" class="form-control input-sm" name="q1" id="q1" placeholder="Keywords..." />
                        </div>
                        <div class="col-sm-3 col-sm-top">
                            <label for="">Status</label>
                            <select class="form-control select2" name="q2" id="q2">
                                <option></option>
                                <option value="0">Verifikasi CEO</option>
                                <option value="1">Approved by CEO</option>
                            </select>
                        </div>
                        <div class="col-sm-3 col-sm-top">
                            <label for="">Periode</label>
                            <input type="month" name="q3" id="q3" class="form-control" value="<?= date("Y-m") ?>">
                        </div>
                        <div class="col-sm-3 col-sm-top">
                            <label for="">Cabang</label>
                            <select class="form-control" name="q4" id="q4">
                                <option value="">Semua Cabang</option>
                                <?php foreach ($cabang as $key) : ?>
                                    <option value="<?= $key['id_master'] ?>"><?= ucwords($key['nama_cabang']) ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-1 col-sm-top" style="margin-right: 10px;">
                            <button type="submit" class="btn btn-info btn-md" name="btnSearch" id="btnSearch"><i class="fa fa-search jarak-kanan"></i>Search</button>
                        </div>
                        <div class="col-sm-2 col-sm-top">
                            <?php if ($id_role != '21') : ?>
                                <div class="btn-group jarak-kanan">
                                    <button type="button" class="btn btn-success"><i class="fa fa-print"></i> Export</button>
                                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a target="_blank" id="expData1" href="#">Rekap Claim</a></li>
                                        <li><a target="_blank" id="expData2" href="#">Rekap Biaya Incentive</a></li>
                                    </ul>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>
                </form>
                <br>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <?php if ($id_role != '21') : ?>
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
                                <div class="table-responsive-satu">
                                    <table class="table table-bordered" id="data-incentive" width="100%">
                                        <thead>
                                            <tr>
                                                <th class="text-center" width="5%">No</th>
                                                <th class="text-center" width="10%">No Pengajuan</th>
                                                <th class="text-center" width="15%">Tanggal Pengajuan</th>
                                                <th class="text-center" width="15%">Periode Bulan</th>
                                                <th class="text-center" width="15%">Periode Tahun</th>
                                                <th class="text-center" width="15%">Status</th>
                                                <th class="text-center" width="15%">Aksi</th>
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
    </style>
    <script>
        $(document).ready(function() {
            $('#expData1').on('click', function() {
                $(this).prop("href", $("#uriExp").val());
            });
            $('#expData2').on('click', function() {
                $(this).prop("href", $("#uriExp2").val());
            });

            $("#data-incentive").ajaxGrid({
                url: "./datatable/list_pengajuan_incentive.php",
                data: {
                    q1: $("#q1").val(),
                    q2: $("#q2").val(),
                    q3: $("#q3").val(),
                    q4: $("#q4").val()
                },
            });
            $("#btnSearch").on("click", function() {
                $("#data-incentive").ajaxGrid("draw", {
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
                $("#data-incentive").ajaxGrid("pageLen", $(this).val());
            });

            $('#data-incentive tbody').on('click', '.btnHapus', function(e) {
                var param = $(this).data("param");
                var jenis = "hapus_pengajuan";
                Swal.fire({
                    title: "Hapus Pengajuan?",
                    showCancelButton: true,
                    confirmButtonText: "YA",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#loading_modal").modal({
                            keyboard: false,
                            backdrop: 'static'
                        });
                        $.ajax({
                            method: 'post',
                            url: '<?php echo ACTION_CLIENT ?>/incentive_bundling.php',
                            data: {
                                "jenis": jenis,
                                "id_pengajuan": param
                            },
                            dataType: 'json',
                            success: function(result) {
                                if (result.status == false) {
                                    setTimeout(function() {
                                        $("#loading_modal").modal("hide");
                                        Swal.fire({
                                            title: "Ooppss",
                                            text: result.pesan,
                                            icon: "warning"
                                        }).then((result) => {
                                            // Reload the Page
                                            location.reload();
                                        });
                                    }, 2000);
                                } else {
                                    setTimeout(function() {
                                        $("#loading_modal").modal("hide");
                                        Swal.fire({
                                            title: "Berhasil",
                                            text: result.pesan,
                                            icon: "success"
                                        }).then((result) => {
                                            location.reload();
                                        });
                                    }, 2000);
                                }
                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown) {
                                alert("Error");
                                // console.log(errorThrown)
                            }
                        })
                    }
                });
            });
        });
    </script>
</body>

</html>
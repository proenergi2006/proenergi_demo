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
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("myGrid", "jqueryUI"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1>Setting Non Penerima Incentive</h1>
            </section>
            <section class="content">

                <?php $flash->display(); ?>
                <div class="alert alert-danger alert-dismissible" style="display:none">
                    <div class="box-tools">
                        <button data-alert="remove" class="btn btn-box-tool close" type="button"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <form name="searchForm" id="searchForm" role="form" class="form-horizontal">
                    <!-- untuk filter -->
                </form>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <?php if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 23) { ?>
                                            <a href="<?php echo BASE_URL_CLIENT . '/add_non_penerima_incentive.php'; ?>" class="btn btn-primary">
                                                <i class="fa fa-plus jarak-kanan"></i>Add Data
                                            </a>
                                        <?php } ?>
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
                            <div class="box-body table-responsive">
                                <table class="table table-bordered" id="table-grid">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="50">No</th>
                                            <th class="text-center" width="250">Nama Akun</th>
                                            <th class="text-center" width="200">Cabang</th>
                                            <th class="text-center" width="200">Role</th>
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

    <style>
        #table-grid td,
        #table-grid th {
            font-size: 12px;
        }

        .table>tbody>tr>td {
            padding: 5px;
        }
    </style>
    <script>
        $(document).ready(function() {
            $("#table-grid").ajaxGrid({
                url: "./datatable/non_penerima_incentive.php",
                data: {},
            });
            // 	$('#btnSearch').on('click', function() {
            // 		$("#table-grid").ajaxGrid("draw", {
            // 			data: {
            // 				q1: $("#q1").val(),
            // 				q2: $("#q2").val(),
            // 				q3: $("#q3").val(),
            // 				q4: $("#q4").val()
            // 			}
            // 		});
            // 		return false;
            // 	});
            // 	$('#tableGridLength').on('change', function() {
            // 		$("#table-grid").ajaxGrid("pageLen", $(this).val());
            // 	});
            $('#table-grid tbody').on('click', '[data-action="deleteGrid"]', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: "Anda yakin hapus?",
                    showCancelButton: true,
                    confirmButtonText: "Hapus",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#loading_modal").modal({
                            keyboard: false,
                            backdrop: 'static'
                        });
                        var param = $(this).data("param-idx");
                        $.ajax({
                            method: 'post',
                            url: '<?php echo ACTION_CLIENT ?>/non_penerima_incentive.php',
                            data: {
                                "param": param,
                                "act": "hapus",
                            },
                            dataType: 'json',
                            success: function(result) {
                                // console.log(result)
                                if (result.status == false) {
                                    setTimeout(function() {
                                        Swal.fire({
                                            title: "Ooppss",
                                            text: result.pesan,
                                            icon: "warning"
                                        }).then((result) => {
                                            location.reload();
                                        });
                                    }, 2000);
                                } else {
                                    setTimeout(function() {
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
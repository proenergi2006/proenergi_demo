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
$required_role = ['1', '2'];
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
                <h1>Button Control</h1>
            </section>
            <section class="content">

                <?php $flash->display(); ?>
                <div class="alert alert-danger alert-dismissible" style="display:none">
                    <div class="box-tools">
                        <button data-alert="remove" class="btn btn-box-tool close" type="button"><i class="fa fa-times"></i></button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <div class="row">
                                    <div class="col-sm-6">
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
                                            <th class="text-center" width="30">No</th>
                                            <th class="text-center" width="100">Button</th>
                                            <th class="text-center" width="150">Keterangan</th>
                                            <th class="text-center" width="150">Status Button</th>
                                            <th class="text-center" width="150">Updated By</th>
                                            <th class="text-center" width="150">Updated At</th>
                                            <th class="text-center" width="150">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="modalOpen" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-blue">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Keterangan</label>
                                            <input type="hidden" name="jenis" id="jenis">
                                            <input type="hidden" name="id" id="id">
                                            <input type="text" name="keterangan" id="keterangan" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" id="btnSimpan">Simpan</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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

            $('#table-grid').on('click', '.openModal', function() {
                var param = $(this).attr('data-param');
                var id = $(this).attr('data-id');
                $('#modalOpen').modal({
                    show: true
                })
                $('#jenis').val(param);
                $('#id').val(id);
            });

            $("#btnSimpan").click(function() {
                var jenis = $("#jenis").val();
                var keterangan = $("#keterangan").val();
                var id = $("#id").val();
                Swal.fire({
                    title: "Anda yakin simpan?",
                    showCancelButton: true,
                    confirmButtonText: "Simpan",
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (keterangan == "") {
                            Swal.fire({
                                title: "Oopss",
                                text: "Harap masukan keterangan terlebih dahulu",
                                icon: "warning"
                            });
                        } else {
                            $("#loading_modal").modal({
                                keyboard: false,
                                backdrop: 'static'
                            });
                            $.ajax({
                                method: 'post',
                                url: `<?php echo ACTION_CLIENT ?>/button-control.php`,
                                data: {
                                    "jenis": jenis,
                                    "keterangan": keterangan,
                                    "id": id
                                },
                                dataType: 'json',
                                success: function(result) {
                                    // console.log(result)
                                    if (result.status == false) {
                                        setTimeout(function() {
                                            $("#modalOpen").modal("hide");
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
                                            $("#modalOpen").modal("hide");
                                            Swal.fire({
                                                title: "Berhasil",
                                                text: result.pesan,
                                                icon: "success"
                                            }).then((result) => {
                                                // Reload the Page
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
                    }
                });
            })

            $("#table-grid").ajaxGrid({
                url: "./datatable/button-control.php",
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
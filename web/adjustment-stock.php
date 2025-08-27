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

$sesRole = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$sesGrup = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);
$sesCbng = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

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
                <h1>Adjustment Stock </h1>
            </section>
            <section class="content">

                <?php $flash->display(); ?>
                <div class="box box-info">
                    <div class="box-header with-border">
                        <p style="font-size:18px; margin-bottom:0px;"><b>PENCARIAN</b></p>
                    </div>
                    <div class="box-body">
                        <form name="searchForm" id="searchForm" role="form" class="form-horizontal">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Kata Kunci</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control input-sm" name="q1" id="q1" placeholder="Keywords..." />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr style="border-top:2px solid #ddd; margin:10px 0 15px;" />
                            <button type="submit" class="btn btn-info btn-sm jarak-kanan" name="btnSearch" id="btnSearch" style="min-width:100px;">
                                <i class="fa fa-search jarak-kanan"></i> Search
                            </button>
                        </form>
                    </div>
                </div>

                <hr style="border-top:4px double #ddd; margin:10px 0 15px;" />

                <div style="margin:15px 0px;">
                    <?php if ($sesRole == 5) { ?>
                        <a href="<?php echo BASE_URL_CLIENT . '/adjustment-stock-add.php'; ?>" class="btn btn-primary jarak-kanan">
                            <i class="fa fa-plus jarak-kanan"></i> Add Data
                        </a>
                    <?php } ?>
                </div>

                <div class="box-tabel-headernya">
                    <div class="row">
                        <div class="col-md-6">
                            <div id="table-grid-infonya"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-right">Show
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

                <div class="table-responsive">
                    <table class="table table-bordered table-hovera" id="table-grid">
                        <thead>
                            <tr>
                                <th class="text-center" width="80" style="height:40px;">No</th>
                                <th class="text-center" width="150">Jenis </th>
                                <th class="text-center" width="100">Tanggal</th>
                                <th class="text-center" width="150">Produk</th>
                                <th class="text-center" width="250">Terminal</th>
                                <th class="text-center" width="150">Nilai</th>
                                <th class="text-center" width="">Keterangan</th>

                                <th class="text-center" width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                <div id="table-grid-linknya"></div>

                <?php $con->close(); ?>

            </section>
            <?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
        </aside>
    </div>

    <script>
        $(document).ready(function() {
            $("#table-grid").ajaxGridNew({
                url: "./datatable/adjustment-stock.php",
                data: {
                    q1: $("#q1").val()
                },
                infoPage: true,
                infoPageClass: "#table-grid-infonya",
                linkPage: true,
                linkPageClass: "#table-grid-linknya",
            });
            $("#table-grid").on("sukses:beforeLoad", function() {
                $("body").addClass("loading");
            }).on("sukses:diload", function() {
                $("body").removeClass("loading");
            });

            $('#btnSearch').on('click', function() {
                $("#table-grid").ajaxGridNew("draw", {
                    data: {
                        q1: $("#q1").val()
                    }
                });
                return false;
            });
            $('#tableGridLength').on('change', function() {
                $("#table-grid").ajaxGridNew("pageLen", $(this).val());
            });

            $('#table-grid tbody').on('click', '[data-action="deleteGrid"]', function(e) {
                swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes...!!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("body").addClass("loading");
                        var param = $(this).data("param-idx");
                        var handler = function(data) {
                            if (data.error == "") {
                                $("body").removeClass("loading");
                                swal.fire({
                                    title: "Information",
                                    icon: "success",
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    html: '<p style="font-size:14px; font-family:arial;">Data Berhasil Dihapus...</p>',
                                    position: "center",
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then((result) => {
                                    if (result.isDismissed) {
                                        $("#table-grid").ajaxGridNew("draw");
                                    }
                                });
                            } else {
                                $("body").removeClass("loading");
                                swal.fire({
                                    icon: "warning",
                                    width: '350px',
                                    allowOutsideClick: false,
                                    html: '<p style="font-size:14px; font-family:arial;">' + data.error + '</p>'
                                });
                            }
                        };
                        $.post(base_url + "/web/action/vendor-inven-terminal-new.php", {
                            act: "hapus",
                            param: param
                        }, handler, "json");
                    }
                });
            });

        });
    </script>
</body>

</html>
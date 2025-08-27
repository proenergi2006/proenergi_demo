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

$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("formatNumber", "jqueryUI", "formatNumber", "myGrid"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1>History Stock </h1>
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
                            <select id="q5" name="q5" class="form-control" <?php echo ($sesrol != 5) ? 'disabled' : ''; ?>>
                                <option></option>
                                <?php $con->fill_select("id_master", "nama_cabang", "pro_master_cabang", $q7, "where is_active=1 and id_master != 1", "", false); ?>
                            </select>
                        </div>

                        <div class="col-sm-4 col-sm-top">
                            <select id="q6" name="q6" class="form-control">
                                <option></option>
                                <?php $con->fill_select("id_master", "concat(nama_terminal,' ',tanki_terminal)", "pro_master_terminal", $q1, "where is_active=1", "id_master", false); ?>
                            </select>

                        </div>


                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3 col-sm-top">
                            <select id="q2" name="q2" class="form-control">
                                <option></option>
                                <option value="1">Tanggal Potong </option>
                                <!-- <option value="2">Tanggal Kirim</option>
                                <option value="3">Tanggal ETL</option> -->
                            </select>
                        </div>
                        <div class="col-sm-3 col-sm-top">
                            <div class="input-group">
                                <span class="input-group-addon">Periode</span>
                                <input type="text" name="q3" id="q3" class="form-control input-sm datepicker" disabled autocomplete="off" />
                            </div>
                        </div>
                        <div class="col-sm-3 col-sm-top">
                            <div class="input-group">
                                <span class="input-group-addon">S/D</span>
                                <input type="text" name="q4" id="q4" class="form-control input-sm datepicker" disabled autocomplete="off" />
                            </div>
                        </div>
                        <div class="col-sm-3 col-sm-top">
                            <button type="submit" class="btn btn-info btn-sm jarak-kanan" name="btnSearch1" id="btnSearch1" style="width:80px;">Cari</button>


                        </div>

                    </div>
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
                            <div class="box-body table-responsive" style="width: 100%; margin-bottom: 15px; overflow-x: auto; overflow-y: hidden;">
                                <table class="table table-bordered" id="data-rekap-pengiriman-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="2%">No</th>
                                            <th class="text-center" width="5%">Cabang</th>
                                            <th class="text-center" width="10%">Customer</th>
                                            <th class="text-center" width="8%">Nomor DR</th>
                                            <th class="text-center" width="8%">Nomor PO</th>
                                            <th class="text-center" width="8%">Nomor LO</th>
                                            <th class="text-center" width="5%">Qty</th>
                                            <th class="text-center" width="8%">Tgl DR</th>
                                            <th class="text-center" width="8%">Tgl Potong</th>
                                            <th class="text-center" width="8%">Jenis</th>
                                            <th class="text-center" width="10%">Terminal</th>
                                            <th class="text-center" width="8%">Action</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="modal fade" id="edit_load_modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-blue">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title"><i class="fas fa-calendar-alt"></i> Edit Tanggal Potong</h4>
                            </div>
                            <div class="modal-body">
                                <div id="infoStatLP"></div>
                                <div id="errStatLP"></div>
                                <table class="table no-border">
                                    <tbody>
                                        <div class="row">

                                            <div class="col-sm-6">
                                                <p style="font-weight:bold; margin-bottom:5px;">Tanggal Potong</p>
                                                <input type="text" name="tglinv" id="tglinv" class="form-control input-sm datepicker" autocomplete="off">
                                            </div>
                                        </div>
                                        <p>


                                    </tbody>
                                </table>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <input type="hidden" name="id" id="id" value="" />
                                            <button type="button" class="btn btn-default jarak-kanan" data-dismiss="modal">Batal</button>
                                            <button type="button" class="btn btn-primary" name="btnAction" id="btnAction" value="1">Simpan</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="error_modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-blue">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Informasi</h4>
                            </div>
                            <div class="modal-body">
                                <p class="text-center" id="error-preview"></p>
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

    <script>
        $(document).ready(function() {
            $(".hitung").number(true, 0, ".", ",");
            $("select#q2").select2({
                placeholder: "Pilih Tanggal",
                allowClear: true
            });
            $("select#q5, select#q4k").select2({
                placeholder: "Pilih Cabang",
                allowClear: true
            });
            $("select#q6, select#q4k").select2({
                placeholder: "Pilih Depot",
                allowClear: true
            });
            $("select#q2").on("change", function() {
                if ($(this).val() == "") $("#q3, #q4").val("").prop("disabled", "disabled");
                else $("#q3, #q4").removeProp("disabled");
            });

            $('#expData1').on('click', function() {
                $(this).prop("href", $("#uriExp1").val());
            });
            $('#expData2').on('click', function() {
                $(this).prop("href", $("#uriExp2").val());
            });
            $("#data-rekap-pengiriman-table").ajaxGrid({
                url: "./c-history-stock-data.php",
                data: {
                    q1: $("#q1").val(),
                    q2: $("#q2").val(),
                    q3: $("#q3").val(),
                    q4: $("#q4").val(),
                    q5: $("#q5").val(),
                    q6: $("#q6").val()
                },
            });

            $(document).on('click', '.editStsAction', function(e) {
                var id = $(this).data("id");
                var tgl = $(this).data("tgl");
                var jenis = $(this).data('jenis'); // Ambil data jenis

                function formatDate(date) {
                    var parts = date.split('-'); // Pisah berdasarkan "-"
                    return parts[2] + '/' + parts[1] + '/' + parts[0]; // Urutkan ulang jadi DD/MM/YYYY
                }


                var formattedDate = formatDate(tgl);

                // Pastikan jenis 'action' atau lainnya sesuai dengan kondisi yang dimaksud
                if (jenis == 'edit') {
                    $("#edit_load_modal").find("#ctt").removeClass('hidden');
                    $("#edit_load_modal").find("#dt4").removeClass('hidden');
                } else {
                    $("#edit_load_modal").find("#ctt").addClass('hidden');
                    $("#edit_load_modal").find("#dt4").addClass('hidden');
                }

                $("#edit_load_modal").find("#tglinv").val(formattedDate);
                $("#edit_load_modal").find("#id").val(id);

                // Menampilkan modal
                $("#edit_load_modal").modal({
                    keyboard: false,
                    backdrop: 'static'
                });
            });


            $('#btnAction').on('click', function() {
                if ($("#tglinv").val() === "") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Tanggal belum diisi!'
                    });
                } else {
                    Swal.fire({
                        title: 'Apakah kamu yakin?',
                        text: "Data akan disimpan setelah kamu menyetujui!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, simpan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Jika user menekan "Yes", lanjutkan proses simpan
                            $("#loading_modal").modal({
                                keyboard: false,
                                backdrop: 'static'
                            });
                            $("#edit_load_modal").modal("hide");

                            // Data yang akan dikirim
                            var dataToSend = {
                                "id": $("#id").val(), // Sertakan id
                                "tgl": $("#tglinv").val()
                            };

                            $.ajax({
                                type: 'POST',
                                url: "./c-history-stock-action.php",
                                data: dataToSend,
                                dataType: "json", // Pastikan respon dari server berupa JSON
                                success: function(data) {
                                    $("#loading_modal").modal("hide");

                                    if (data.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil!',
                                            text: data.message,
                                            showConfirmButton: true
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = './c-history-stock.php'; // Redirect ke halaman c-history-stock
                                            }
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal!',
                                            text: data.message,
                                            showConfirmButton: true
                                        });
                                    }
                                },
                                error: function(xhr, status, error) {
                                    $("#loading_modal").modal("hide");
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal!',
                                        text: 'Terjadi kesalahan tidak terduga.',
                                        showConfirmButton: true
                                    });
                                }
                            });
                        }
                    });
                }
            });







            $("#q2").change(function() {
                var val = $(this).val();

                if (val != "") {
                    $("#q3").removeAttr("disabled", true)
                    $("#q4").removeAttr("disabled", true)
                } else {
                    $("#q3").attr("disabled", true)
                    $("#q4").attr("disabled", true)
                }
            })
            $("#btnSearch1").on("click", function() {
                $("#data-rekap-pengiriman-table").ajaxGrid("draw", {
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
            $('#tableGridLength1').on('change', function() {
                $("#data-rekap-pengiriman-table").ajaxGrid("pageLen", $(this).val());
            });
        });
    </script>
</body>

</html>
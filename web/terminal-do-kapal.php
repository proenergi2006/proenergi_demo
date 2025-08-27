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
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("formatNumber", "jqueryUI", "myGrid"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1>Loading Status Kapal</h1>
            </section>
            <section class="content">

                <?php $flash->display(); ?>
                <div class="alert alert-danger alert-dismissible" style="display:none">
                    <div class="box-tools">
                        <button data-alert="remove" class="btn btn-box-tool close" type="button"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="tab-content">
                    <form name="searchForm2" id="searchForm2" role="form" class="form-horizontal">
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-sm" name="q1k" id="q1k" placeholder="Keywords..." />
                            </div>
                            <div class="col-sm-3 col-sm-top">
                                <div class="input-group">
                                    <span class="input-group-addon">Tgl Kirim</span>
                                    <input type="text" name="q2k" id="q2k" class="form-control input-sm datepicker" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-sm-3 col-sm-top">
                                <div class="input-group">
                                    <span class="input-group-addon">S/D</span>
                                    <input type="text" name="q3k" id="q3k" class="form-control input-sm datepicker" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <select id="q4k" name="q4k" class="form-control">
                                    <option></option>
                                    <option value="1">Registered</option>
                                    <option value="2">Loaded</option>
                                    <option value="3">Delivered</option>
                                    <option value="4">Cancel</option>
                                </select>
                            </div>
                            <div class="col-sm-9 col-sm-top">
                                <button type="submit" class="btn btn-info btn-sm" name="btnSearch2" id="btnSearch2" style="width:80px;">Cari</button>
                            </div>
                        </div>
                    </form>

                    <form action="<?php echo ACTION_CLIENT . '/terminal-do.php'; ?>" id="gform" name="gform" method="post" role="form" target="_blank">
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
                                                    <th class="text-center" width="5%">No</th>
                                                    <th class="text-center" width="24%">Nomor DN</th>
                                                    <th class="text-center" width="16%">Notify Party</th>
                                                    <th class="text-center" width="16%">Transportir</th>
                                                    <th class="text-center" width="15%">Depot</th>
                                                    <th class="text-center" width="15%">Status</th>
                                                    <th class="text-center" width="9%"><input type="checkbox" name="cekAll2" id="cekAll2" value="1" /></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="pad bg-gray">
                                    <input type="hidden" name="param" id="param1" value="<?php echo paramEncrypt("do_kapal#|#cetak"); ?>" />
                                    <button type="submit" class="btn btn-success" name="btnSbmt" id="btnSbmt" value="1"><i class="fa fa-print jarak-kanan"></i>Cetak DN</button>
                                </div>
                            </div>
                        </div>
                    </form>


                </div>

                <div class="modal fade" id="revert_load_modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-blue">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Reset Loading</h4>
                            </div>
                            <div class="modal-body">
                                <div id="infoStatLP"></div>
                                <p>Anda akan mereset status loading customer diatas, hal ini akan membuat sistem menjadikan status loading menjadi belum loading,
                                    menghapus data histori pengiriman dan mengembalikan volume inventory.</p>
                                <p>Apakah anda yakin ?</p>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="text-right" style="border-top:4px double #ddd; padding-top:10px;">
                                            <input type="hidden" name="paramLP2" id="paramLP2" value="" />
                                            <input type="hidden" name="jenisLP2" id="jenisLP2" value="" />
                                            <button type="button" class="btn btn-default jarak-kanan" data-dismiss="modal">Batal</button>
                                            <button type="button" class="btn btn-primary" name="btnLP2" id="btnLP2" value="1">Confirm</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="status_load_modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-blue">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title"></h4>
                            </div>
                            <div class="modal-body">
                                <div id="infoStatLP"></div>
                                <div id="errStatLP"></div>
                                <table class="table no-border">
                                    <tbody>
                                        <tr>
                                            <td style="padding:0px 5px;" width="100" hidden>
                                                <p style="font-weight:bold; margin-bottom:5px;"></p>
                                                <input type="text" name="" id="etl_val" class="input-sm datepicker form-control" autocomplete="off" />
                                            </td>
                                            <td style="padding:0px 5px;" width="100">
                                                <p id="lb1" style="font-weight:bold; margin-bottom:5px;"></p>
                                                <input type="text" name="dt1" id="dt1" class="input-sm datepicker form-control" autocomplete="off" />
                                            </td>
                                            <td width="10">&nbsp;</td>
                                            <td style="padding:0px 5px;">
                                                <p id="lb2" style="font-weight:bold; margin-bottom:5px;"></p>
                                                <select name="dt2" id="dt2" style="height:30px; line-height:1.5; width:50px;">
                                                    <option></option>
                                                    <?php for ($i = 0; $i < 24; $i++) echo '<option>' . str_pad($i, 2, '0', STR_PAD_LEFT) . '</option>'; ?>
                                                </select>
                                                <span style="font-size:14px; padding:0px 2px;">:</span>
                                                <select name="dt3" id="dt3" style="height:30px; line-height:1.5; width:50px;">
                                                    <option></option>
                                                    <?php for ($j = 0; $j < 60; $j++) echo '<option>' . str_pad($j, 2, '0', STR_PAD_LEFT) . '</option>'; ?>
                                                </select>
                                                <span style="font-size:14px; padding:0px 5px;">&nbsp;</span>
                                                <a class="btn btn-sm btn-info" id="load_now_modal">NOW</a>
                                            </td>
                                            <td style="padding:0px 5px;">
                                                <p id="ctt" style="font-weight:bold; margin-bottom:5px;"></p>
                                                <textarea name="dt4" id="dt4" class="input-sm form-control" autocomplete="off"></textarea>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <input type="hidden" name="terminal" id="terminal" value="" />
                                            <input type="hidden" name="customer" id="customer" value="" />
                                            <input type="hidden" name="paramLP" id="paramLP" value="" />
                                            <input type="hidden" name="jenisLP" id="jenisLP" value="" />
                                            <button type="button" class="btn btn-default jarak-kanan" data-dismiss="modal">Batal</button>
                                            <button type="button" class="btn btn-primary" name="btnLP1" id="btnLP1" value="1">Simpan</button>
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

    <style type="text/css">
        .table {
            border: 1px solid #ddd;
            margin-bottom: 15px;
            border-collapse: collapse;
            border-spacing: 0px;
        }

        .table>thead>tr>th,
        .table>tbody>tr>td {
            border: 1px solid #ddd;
            padding: 5px;
            font-size: 11px;
            font-family: arial;
            vertical-align: top;
        }

        .table>thead>tr>th {
            background-color: #f4f4f4;
            vertical-align: middle;
            padding: 8px 5px;
        }
    </style>
    <script>
        $(document).ready(function() {

            $("select#q2").select2({
                placeholder: "Pilih Tanggal",
                allowClear: true
            });
            $("select#q5, select#q4k").select2({
                placeholder: "Pilih Status",
                allowClear: true
            });
            $("select#q2").on("change", function() {
                if ($(this).val() == "") $("#q3, #q4").val("").prop("disabled", "disabled");
                else $("#q3, #q4").removeProp("disabled");
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

            $("#data-kapal-table").ajaxGrid({
                url: "./datatable/terminal-do-kapal.php",
                data: {
                    q1: $("#q1").val(),
                    q2: $("#q2").val(),
                    q3: $("#q3").val(),
                    q4: $("#q4").val(),
                    q5: $("#q5").val()
                },
            });
            $("#btnSearch").on("click", function() {
                $("#data-kapal-table").ajaxGrid("draw", {
                    data: {
                        q1: $("#q1").val(),
                        q2: $("#q2").val(),
                        q3: $("#q3").val(),
                        q4: $("#q4").val(),
                        q5: $("#q5").val()
                    }
                });
                return false;
            });
            $('#tableGridLength1').on('change', function() {
                $("#data-kapal-table").ajaxGrid("pageLen", $(this).val());
            });
            $("#data-kapal-table").on("ifChecked", "#cekAll", function() {
                $(".chkp").iCheck("check");
            }).on("ifUnchecked", "#cekAll", function() {
                $(".chkp").iCheck("uncheck");
            });

            $('#data-kapal-table tbody').on('click', '.editStsT', function(e) {
                var param = $(this).data("param");
                var jenis = $(this).data("jenis");
                var infor = $(this).data("info");
                var etl = $(this).data("etl");
                var etl_val = $(this).data("etl_val");
                var terminal = $(this).data("terminal");
                var customer = $(this).data("customer");
                var angku = $(this).parents("table").first().attr("id");
                var tkuLP = (angku == "data-kapal-table") ? 1 : 2;
                var arrMt = (jenis == "loading") ? 'Status Loading' : 'Penolakan Pengiriman';

                $("#status_load_modal").find("#ctt").addClass('hidden');
                $("#status_load_modal").find("#dt4").addClass('hidden');
                if (jenis == 'loading') {
                    $("#status_load_modal").find("#ctt").removeClass('hidden');
                    $("#status_load_modal").find("#dt4").removeClass('hidden');
                }


                function formatDate(date) {
                    var parts = date.split('-'); // Pisah berdasarkan "-"
                    return parts[2] + '/' + parts[1] + '/' + parts[0]; // Urutkan ulang jadi DD/MM/YYYY
                }


                var formattedDate = formatDate(etl);
                var formattedDateval = formatDate(etl_val);

                $.post("./datatable/get_info_loading.php", {
                    param: infor
                }, function(data) {
                    $("#status_load_modal").find(".modal-title").html(arrMt);
                    $("#status_load_modal").find("#infoStatLP").html(data);
                    $("#status_load_modal").find("#lb1").html("Tanggal " + jenis);
                    $("#status_load_modal").find("#lb2").html("Jam " + jenis);
                    $("#status_load_modal").find("#ctt").html("Catatan " + jenis);
                    $("#status_load_modal").find("#dt1").val(formattedDate);
                    $("#status_load_modal").find("#etl_val").val(formattedDateval);
                    $("#status_load_modal").find("#terminal").val(terminal);
                    $("#status_load_modal").find("#customer").val(customer);
                    $("#status_load_modal").find("#paramLP").val(param);
                    $("#status_load_modal").find("#jenisLP").val(tkuLP);
                    $("#status_load_modal").modal();
                });
            });

            $("#status_load_modal").on("hidden.bs.modal", function() {
                $("#status_load_modal").find("#dt1, #dt2, #dt3, #paramLP, #jenisLP").val("");
                $("#status_load_modal").find("#errStatLP, #infoStatLP, #lb1, #lb2, .modal-title").html("");
            });

            $('#load_now_modal').on('click', function(e) {
                var handler = function(data) {
                    $("#status_load_modal").find("#dt1").val(data.tanggal);
                    $("#status_load_modal").find("#dt2").val(data.jam);
                    $("#status_load_modal").find("#dt3").val(data.menit);
                };
                $.post("./datatable/get_tanggal.php", {}, handler, "json");
            });

            $('#data-kapal-table tbody').on('click', '.resetSts', function(e) {
                var param = $(this).data("param");
                var jenis = $(this).data("jenis");
                var infor = $(this).data("info");
                var angku = $(this).parents("table").first().attr("id");
                var tkuLP = (angku == "data-kapal-table") ? 1 : 2;

                $.post("./datatable/get_info_loading.php", {
                    param: infor
                }, function(data) {
                    $("#revert_load_modal").find("#infoStatLP").html(data);
                    $("#revert_load_modal").find("#paramLP2").val(param);
                    $("#revert_load_modal").find("#jenisLP2").val(tkuLP);
                    $("#revert_load_modal").modal();
                });
            });

            $("#status_load_modal").on("hidden.bs.modal", function() {
                $("#status_load_modal").find("#dt1, #dt2, #dt3, #dt4,  #paramLP, #jenisLP").val("");
                $("#status_load_modal").find("#errStatLP, #infoStatLP, #lb1, #lb2, .modal-title").html("");
            });


            $(document).ajaxComplete(function() {
                $("input[type='checkbox']").iCheck({
                    checkboxClass: 'icheckbox_square-blue'
                });
            });

            $("#btnSearch2").on("click", function() {
                $("#data-kapal-table").ajaxGrid("draw", {
                    data: {
                        q1: $("#q1k").val(),
                        q2: $("#q2k").val(),
                        q3: $("#q3k").val(),
                        q4: $("#q4k").val()
                    }
                });
                return false;
            });
            $('#tableGridLength2').on('change', function() {
                $("#data-kapal-table").ajaxGrid("pageLen", $(this).val());
            });
            $("#data-kapal-table").on("ifChecked", "#cekAll2", function() {
                $(".chkp2").iCheck("check");
            }).on("ifUnchecked", "#cekAll2", function() {
                $(".chkp2").iCheck("uncheck");
            });

            $("#status_load_modal").on("click", "#btnLP1", function() {

                var dt1 = $("#dt1").val();
                var etl_val = $("#etl_val").val();

                var inputDate = new Date(dt1.split("/").reverse().join("-"));
                var etlDate = new Date(etl_val.split("/").reverse().join("-")); // format input "dd/mm/yyyy" dibalik jadi "yyyy-mm-dd"

                if ($("#dt1").val() == "" || $("#dt2").val() == "" || $("#dt3").val() == "") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Tanggal dan jam belum diisi!'
                    });
                } else if (inputDate.getMonth() !== etlDate.getMonth() || inputDate.getFullYear() !== etlDate.getFullYear()) {
                    // Jika bulan atau tahun dari inputDate tidak sama dengan etlDate, tampilkan pesan error
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Bulan dan tahun harus sama dengan ETL ' + etl_val + '. Hanya tanggal yang boleh berbeda.'
                    });
                } else {
                    Swal.fire({
                        title: 'Apakah Anda yakin ingin melanjutkan?',
                        text: "Pastikan data sudah benar sebelum melanjutkan.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, lanjutkan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $("#loading_modal").modal({
                                keyboard: false,
                                backdrop: 'static'
                            });

                            $("#status_load_modal").modal("hide");
                            $.ajax({
                                type: 'POST',
                                url: "./action/terminal-do.php",
                                data: {
                                    "param": $("#paramLP").val(),
                                    "dt1": $("#dt1").val(),
                                    "dt2": $("#dt2").val(),
                                    "dt3": $("#dt3").val(),
                                    "dt4": $("#dt4").val(),
                                    "terminal": $("#terminal").val(),
                                    "customer": $("#customer").val(),
                                },
                                cache: false,
                                dataType: "json",
                                success: function(data) {
                                    if ($("#jenisLP").val() == 2) $("#data-kapal-table").ajaxGrid("draw");
                                    if (data.badge > 0) $("#menubadge21").html(data.badge);
                                    $("#loading_modal").modal("hide");

                                    if (data.error) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: data.error
                                        });
                                    } else {
                                        // Tampilkan SweetAlert jika sukses, lalu reload halaman
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil',
                                            text: 'Data berhasil disimpan!'
                                        }).then(function() {
                                            location.reload(); // Reload halaman setelah SweetAlert
                                        });
                                    }
                                }
                            });
                        }
                    });
                }
            });

            $("#revert_load_modal").on("click", "#btnLP2", function() {
                $("#loading_modal").modal({
                    backdrop: "static"
                });
                $("#revert_load_modal").modal("hide");
                $.ajax({
                    type: 'POST',
                    url: "./action/terminal-do.php",
                    data: {
                        "param": $("#paramLP2").val()
                    },
                    cache: false,
                    dataType: "json",
                    success: function(data) {
                        if ($("#jenisLP2").val() == 1) $("#data-kapal-table").ajaxGrid("draw");
                        if (data.badge > 0) $("#menubadge21").html(data.badge);
                        $("#loading_modal").modal("hide");
                        if (data.error) {
                            $("#error_modal").find("#error-preview").html(data.error);
                            $("#error_modal").modal();
                        }
                    }
                });
            });

        });
    </script>
</body>

</html>
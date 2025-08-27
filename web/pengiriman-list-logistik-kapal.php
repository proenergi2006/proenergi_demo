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
$linkEx2 = BASE_URL_CLIENT . '/report/pengiriman-logistik-kapal-exp.php';

// include_once($public_base_directory . "/web/update_otomatis_list_pengiriman.php");
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
                <h1>List Pengiriman Kapal</h1>
            </section>
            <section class="content">
                <?php $flash->display(); ?>
                <div class="alert alert-danger alert-dismissible" style="display:none">
                    <div class="box-tools">
                        <button data-alert="remove" class="btn btn-box-tool close" type="button"><i class="fa fa-times"></i></button>
                    </div>
                </div>






                <form name="searchForm2" id="searchForm2" role="form" class="form-horizontal">
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <input type="text" class="form-control input-sm" name="q1" id="q1" placeholder="Keywords..." />
                        </div>
                        <div class="col-sm-3 col-sm-top">
                            <div class="input-group">
                                <span class="input-group-addon">Tgl Kirim</span>
                                <input type="text" name="q2" id="q2" class="form-control input-sm datepicker" autocomplete="off" />
                            </div>
                        </div>
                        <div class="col-sm-3 col-sm-top">
                            <div class="input-group">
                                <span class="input-group-addon">S/D</span>
                                <input type="text" name="q3" id="q3" class="form-control input-sm datepicker" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <select id="q4" name="q4" class="form-control select2">
                                <option></option>
                                <option value="5">Belum Loading</option>
                                <option value="2">Loaded</option>
                                <option value="3">Delivered</option>
                                <option value="4">Cancel</option>
                            </select>
                        </div>
                        <div class="col-sm-9 col-sm-top">
                            <button type="submit" class="btn btn-info btn-sm jarak-kanan" name="btnSearch1" id="btnSearch1" style="width:80px;">Cari</button>
                            <a href="<?php echo $linkEx2; ?>" class="btn btn-success btn-sm" target="_blank" id="expData2">Export Data</a>
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
                                <table class="table table-bordered" id="data-kapal-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="3%">No</th>
                                            <th class="text-center" width="20%">Nomor DN</th>

                                            <th class="text-center" width="16%">PO Customer</th>
                                            <th class="text-center" width="15%">Transportir</th>
                                            <th class="text-center" width="13%">Keterangan Lain</th>
                                            <th class="text-center" width="16%">Depot</th>
                                            <th class="text-center" width="">Status</th>
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



                <div class="modal fade" id="status_kirim_modal" tabindex="-1" role="dialog" aria-hidden="false">

                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-blue">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title"></h4>
                            </div>
                            <div class="modal-body">
                                <div id="infoStatLP1"></div>
                                <div id="errStatLP"></div>
                                <table class="table no-border" style="margin-bottom:10px;">
                                    <tbody>
                                        <tr>
                                            <td style="padding:0px 5px;" width="100">
                                                <p style="font-weight:bold; margin-bottom:5px;">Tanggal</p>
                                                <input type="text" name="dt1" id="dt1" class="input-sm datepicker form-control" />
                                            </td>
                                            <td width="10">&nbsp;</td>
                                            <td style="padding:0px 5px;" width="50">
                                                <p style="font-weight:bold; margin-bottom:5px;">Jam</p>
                                                <select name="dt2" id="dt2" style="height:30px; line-height:1.5; width:50px;">
                                                    <option></option>
                                                    <?php for ($i = 0; $i < 24; $i++) echo '<option>' . str_pad($i, 2, '0', STR_PAD_LEFT) . '</option>'; ?>
                                                </select>
                                            </td>
                                            <td style="padding:0px 5px 0px 0px;">
                                                <p style="font-weight:bold; margin-bottom:5px;">&nbsp;</p>
                                                <span style="font-size:14px; padding:0px 2px;">:</span>
                                                <select name="dt3" id="dt3" style="height:30px; line-height:1.5; width:50px;">
                                                    <option></option>
                                                    <?php for ($j = 0; $j < 60; $j++) echo '<option>' . str_pad($j, 2, '0', STR_PAD_LEFT) . '</option>'; ?>
                                                </select>
                                                <span style="font-size:14px; padding:0px 5px;">&nbsp;</span>
                                                <a class="btn btn-sm btn-info" id="load_now_modal">NOW</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="padding:10px 5px;" class="stasi">
                                                <p style="font-weight:bold; margin-bottom:5px;">Status</p>
                                                <input type="text" name="stat_kirim" id="stat_kirim" class="form-control stasi" />
                                                <input type="hidden" name="customer_kapal" id="customer" class="form-control stasi" />
                                                <input type="hidden" name="customer_alamat_dr" id="customer_alamat_dr" class="form-control stasi" />
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="3" style="padding:10px 5px;" class="reali">
                                                <p style="font-weight:bold; margin-bottom:5px;">Realisasi Volume</p>
                                                <div class="input-group">
                                                    <input type="number" name="real_kirim" id="real_kirim" class="form-control hitung reali" />
                                                    <span class="input-group-addon">Liter</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" style="padding:10px 5px;" class="reali">
                                                <p style="font-weight:bold; margin-bottom:5px;">Terima Surat Jalan</p>
                                                <input type="text" name="staj_kirim" id="staj_kirim" class="form-control reali" />
                                            </td>
                                            <td style="padding:10px 5px;" class="reali">

                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <hr style="border-top: 4px double #ccc; margin: 10px 0px 15px;">
                                <div class="text-left">
                                    <input type="hidden" name="idLP" id="idLP" value="" />
                                    <input type="hidden" name="tipeLP" id="tipeLP" value="" />
                                    <button type="button" class="btn btn-default jarak-kanan" data-dismiss="modal">Batal</button>
                                    <button type="button" class="btn btn-primary jarak-kanan stasi" name="btnLP1" id="btnLP1" value="1">Update Status</button>
                                    <button type="button" class="btn btn-success jarak-kanan stasi" name="btnLP2" id="btnLP2" value="1">Pengiriman Selesai</button>
                                    <button type="button" class="btn btn-primary jarak-kanan reali" name="btnLP3" id="btnLP3" value="1">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="status_history_modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-histori">
                        <div class="modal-content">
                            <div class="modal-header bg-blue">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Histori Status Pengiriman</h4>
                            </div>
                            <div class="modal-body">
                                <p id="jdlKirim"></p>
                                <div class="table-responsive">
                                    <form name="modal-form-histori" id="modal-form-histori">
                                        <table class="table table-bordered" id="listHistoriLP">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" width="50">No</th>
                                                    <th class="text-center" width="180">Tanggal</th>
                                                    <th class="text-center" width="">Status</th>
                                                    <th class="text-center" width="">Catatan</th>
                                                    <th class="text-center" width="45"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </form>
                                </div>
                                <div id="detilHistoriLp"></div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="show_maptracking_modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" style="width:1000px;">
                        <div class="modal-content">
                            <div class="modal-header bg-blue">
                                <button type="button" class="close btnBatal_show_maptracking_modal"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Tracking</h4>
                            </div>
                            <div class="modal-body">
                                <div class="text-left infonya"></div>
                                <div id="map_track_view" style="border: 4px double #ddd;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="modalLiveTracking" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog-fullscreen">
                        <div class="modal-content-fullscreen">
                            <div class="modal-header bg-blue">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <iframe id="myiframeMaps" width="100%" height="600vh" frameborder="0"></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="modalDispatch" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog-fullscreen">
                        <div class="modal-content-fullscreen">
                            <div class="modal-header bg-blue">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <iframe id="myiframeDispatch" width="100%" height="600vh" frameborder="0"></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <?php $con->close(); ?>
            </section>
            <?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
        </aside>
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
                                    <input type="text" name="dt1_load" id="dt1_load" class="input-sm datepicker form-control" autocomplete="off" disabled />
                                </td>

                                <td width="10">&nbsp;</td>
                                <td style="padding:0px 5px;">
                                    <p id="lb2" style="font-weight:bold; margin-bottom:5px;"></p>
                                    <select name="dt2_load" id="dt2_load" style="height:30px; line-height:1.5; width:50px;">
                                        <option></option>
                                        <?php for ($i = 0; $i < 24; $i++) echo '<option>' . str_pad($i, 2, '0', STR_PAD_LEFT) . '</option>'; ?>
                                    </select>
                                    <span style="font-size:14px; padding:0px 2px;">:</span>
                                    <select name="dt3_load" id="dt3_load" style="height:30px; line-height:1.5; width:50px;">
                                        <option></option>
                                        <?php for ($j = 0; $j < 60; $j++) echo '<option>' . str_pad($j, 2, '0', STR_PAD_LEFT) . '</option>'; ?>
                                    </select>
                                    <span style="font-size:14px; padding:0px 5px;">&nbsp;</span>
                                    <a class="btn btn-sm btn-info" id="load_now_status_load_modal">NOW</a>
                                </td>
                                <td style="padding:0px 5px;">
                                    <p id="ctt" style="font-weight:bold; margin-bottom:5px;"></p>
                                    <textarea name="dt4_load" id="dt4_load" class="input-sm form-control" autocomplete="off"></textarea>
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
                                <button type="button" class="btn btn-primary" name="btnLPLoading" id="btnLPLoading" value="1">Simpan</button>
                            </div>
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

    <style>
        .getlokasimobil {
            cursor: pointer;
        }

        #map_track_view {
            min-height: 420px;
        }

        .openModalTracking {
            cursor: pointer;
        }

        .modal {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            overflow: hidden;
        }

        .modal-dialog-fullscreen {
            position: fixed;
            margin: 0;
            width: 100%;
            height: 100%;
            padding: 0;
        }

        .modal-content-fullscreen {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            border: 2px solid #3c7dcf;
            border-radius: 0;
            box-shadow: none;
        }
    </style>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js" crossorigin=""></script>

    <script>
        function onlyNumberKey(evt) {
            let ASCIICode = (evt.which) ? evt.which : evt.keyCode
            if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
                return false;
            return true;
        }
        $(document).ready(function() {
            var map_track_view;

            if (!window.console) {
                console = {};
                console.log = function() {};
            }
            $("#data-kapal-table").ajaxGrid({
                url: "./datatable/pengiriman-list-logistik-kapal.php",
                data: {
                    q1: $("#q1").val(),
                    q2: $("#q2").val(),
                    q3: $("#q3").val(),
                    q4: $("#q4").val(),
                    q5: $("#q5").val()
                },
            });

            $("#btnSearch1").on("click", function() {
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

            $('#data-kapal-table tbody').on('click', '.editStsT', function(e) {
                var param = $(this).data("param");
                var infor = $(this).data("info");
                var reali = $(this).data("realisasi");
                var status = $(this).data("status");
                var customer = $(this).data("customer");
                var customer_alamat_dr = $(this).data("customer_alamat_dr");
                var angku = $(this).parents("table").first().attr("id");
                var tkuLP = (angku == "data-kapal-table") ? 1 : 2;



                $.post("./datatable/get_info_loading.php", {
                    param: infor
                }, function(data) {
                    if (reali) {
                        $("#status_kirim_modal").find(".reali").removeClass("hide");
                        $("#status_kirim_modal").find(".stasi").addClass("hide");
                        $("#status_kirim_modal").find(".modal-title").html("Realisasi Volume dan Terima Surat Jalan");
                    } else {
                        $("#status_kirim_modal").find(".stasi").removeClass("hide");
                        $("#status_kirim_modal").find(".reali").addClass("hide");
                        $("#status_kirim_modal").find("#customer").val(customer);
                        $("#status_kirim_modal").find("#customer_alamat_dr").val(customer_alamat_dr);
                        $("#status_kirim_modal").find(".modal-title").html("Status Pengiriman");
                    }
                    $("#status_kirim_modal").find("#infoStatLP1").html(data);
                    $("#status_kirim_modal").find("#idLP").val(param);
                    $("#status_kirim_modal").find("#tipeLP").val(tkuLP);
                    $("#status_kirim_modal").modal({
                        keyboard: false,
                        backdrop: 'static'
                    });
                });
            })

            $('#data-kapal-table tbody').on('click', '.listStsT', function(e) {
                var param = $(this).data("param");
                $("#loading_modal").modal({
                    keyboard: false,
                    backdrop: 'static'
                });
                $.ajax({
                    type: 'POST',
                    url: "./__get_pengiriman_list.php",
                    data: {
                        "file": "logistik",
                        "aksi": param
                    },
                    cache: false,
                    dataType: "json",
                    success: function(data) {
                        $("#loading_modal").modal("hide");
                        $("#status_history_modal").find("#listHistoriLP > tbody").html(data.items);
                        $("#status_history_modal").find("#jdlKirim").html(data.judul);
                        $("#status_history_modal").find("#detilHistoriLp").html(data.extras);
                        $(".input-date").datepicker({
                            dateFormat: 'dd/mm/yy',
                            changeMonth: true,
                            changeYear: true,
                            yearRange: "c-80:c+10",
                            dayNamesMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                            monthNamesShort: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
                        });
                    }
                });
                $("#status_history_modal").modal({
                    keyboard: false,
                    backdrop: 'static'
                });
            });
            $("#status_history_modal").on("shown.bs.modal", function() {
                $("#status_kirim_modal").modal("hide");
            }).on("hidden.bs.modal", function() {
                $("#status_history_modal").find("#listHistoriLP > tbody, #jdlKirim").html("");
                $("body").css("padding-right", "0px");
            }).on("click", ".fa-ubah-sts", function() {
                var idx = $(this).data("idx");
                $(".histori-form" + idx).removeClass("hide");
                $(".histori-text" + idx).addClass("hide");
            }).on("click", ".fa-simpan-sts", function() {
                var jns = $(this).data("jns"),
                    idnya = $(this).data("ids");
                var prm = $("#modal-form-histori").serializeArray();
                prm.push({
                    name: 'file',
                    value: 'logtrans'
                }, {
                    name: 'aksi',
                    value: 'ubah'
                }, {
                    name: 'param',
                    value: idnya
                }, {
                    name: 'tipe',
                    value: jns
                });

                $("#loading_modal").modal({
                    keyboard: false,
                    backdrop: 'static'
                });
                $.ajax({
                    type: 'POST',
                    url: "./action/pengiriman-list.php",
                    data: prm,
                    cache: false,
                    success: function(data) {
                        if (data) {
                            if (jns == 1) $("#data-kapal-table").ajaxGrid("draw");
                            else $("#data-kapal-table").ajaxGrid("draw");
                            $("#status_history_modal").find("#listHistoriLP > tbody").html(data);
                            $(".input-date").datepicker({
                                dateFormat: 'dd/mm/yy',
                                changeMonth: true,
                                changeYear: true,
                                yearRange: "c-80:c+10",
                                dayNamesMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                                monthNamesShort: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
                            });
                        }
                        $("#loading_modal").modal("hide");
                    }
                });
            });

            $("#status_load_modal").on("hidden.bs.modal", function() {
                $("#status_load_modal").find("#dt1, #dt2, #dt3, #paramLP, #jenisLP").val("");
                $("#status_load_modal").find("#errStatLP, #infoStatLP, #lb1, #lb2, .modal-title").html("");
            }).on('click', '#load_now_status_load_modal', function(e) {
                var handler = function(data) {
                    $("#status_load_modal").find("#dt1_load").val(data.tanggal);
                    $("#status_load_modal").find("#dt2_load").val(data.jam);
                    $("#status_load_modal").find("#dt3_load").val(data.menit);
                };
                $.post("./datatable/get_tanggal.php", {}, handler, "json");
            }).on("click", "#btnLPLoading", function() {

                var dt1_load = $("#dt1_load").val();
                var etl_val = $("#etl_val").val();

                var inputDate = new Date(dt1_load.split("/").reverse().join("-"));
                var etlDate = new Date(etl_val.split("/").reverse().join("-")); // format input "dd/mm/yyyy" dibalik jadi "yyyy-mm-dd"
                // Cek apakah tanggal input memiliki bulan dan tahun yang sama dengan etl_val
                if ($("#dt1_load").val() == "" || $("#dt2_load").val() == "" || $("#dt3_load").val() == "") {
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
                        text: 'Bulan dan tahun harus sama dengan ETL' + etl_val + '. Hanya tanggal yang boleh berbeda.'
                    });
                } else {
                    // Jika validasi lolos, lanjutkan dengan menyimpan data
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
                            "dt1": $("#dt1_load").val(),
                            "dt2": $("#dt2_load").val(),
                            "dt3": $("#dt3_load").val(),
                            "dt4": $("#dt4_load").val(),
                            "terminal": $("#terminal").val(),
                            "customer": $("#customer").val(),

                        },
                        cache: false,
                        dataType: "json",
                        success: function(data) {
                            if ($("#jenisLP").val() == 2) $("#data-kapal-table").ajaxGrid("draw");
                            else $("#data-kapal-table").ajaxGrid("draw");
                            $("#loading_modal").modal("hide");
                            if (data.error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.error
                                });
                            }
                        }
                    });
                }
            });


            //pembaruan is_request *iwanhermawan develop 27/09/2024


            $("#request_load_modal").on("hidden.bs.modal", function() {
                // Reset form saat modal ditutup
                $("#request_load_modal").find("#paramRQ, #jenisLP").val("");
                $("#request_load_modal").find("#errStatRP, #infoStatLP, #lb5, .modal-title").html("");
            }).on("click", "#btnAction", function(e) {
                e.preventDefault(); // Mencegah tindakan default

                var jenisRequest = $("#dt5_load").val();
                var catatan = $("#dt6_load").val();
                var paramRQ = $("#paramRQ").val(); // Mendapatkan nilai paramRQ
                var loaded = $("#is_loaded").val(); // Mendapatkan nilai loaded
                var loco = $("#loco").val(); // Mendapatkan nilai loaded
                var masaAkhir = $("#masa_akhir").val(); // Mendapatkan nilai masa akhir

                var today = new Date();
                var endDate = new Date(masaAkhir);

                if (jenisRequest === "" || catatan === "") {
                    $("#errStatRP").html('<p class="text-red">Jenis Request dan Catatan Belum Di isi...</p>');
                } else {
                    console.log("Param (paramRQ): " + paramRQ);
                    console.log("Jenis Request (dt5): " + jenisRequest);
                    console.log("Catatan (dt6): " + catatan);

                    // Cek jika nilai loaded adalah 1 dan jenisRequest adalah 'Change Depot'
                    if (loaded === "1" && jenisRequest === "1") {
                        Swal.fire({
                            icon: 'error',
                            title: 'Informasi',
                            text: 'Tidak Bisa Request Change Depot Karena Anda Sudah Loading.',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $("#request_load_modal").modal("show");
                            }
                        });
                    } else if (jenisRequest === "2" && loaded === "1") {
                        Swal.fire({
                            icon: 'error',
                            title: 'Informasi',
                            text: 'Tidak Bisa Request Reschedule Harus Memilih Cancel Karena Anda Sudah Loading.',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $("#request_load_modal").modal("show");
                            }
                        });
                        // Cek jika hari ini sudah melewati masa akhir
                    } else if (jenisRequest === "2" && today > endDate) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Informasi',
                            text: 'Tidak Bisa Request Karena Tgl Penawaran Periode Masa Akhir Sudah Lewat.',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $("#request_load_modal").modal("show");
                            }
                        });

                    } else if (jenisRequest === "3" && loaded === "1" && loco === "1") {
                        Swal.fire({
                            icon: 'error',
                            title: 'Informasi',
                            text: 'Tidak Bisa Cancel Karena Status Loco Dan Anda Sudah Loading.',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $("#request_load_modal").modal("show");
                            }
                        });
                    } else {
                        $("#loading_modal").modal({
                            keyboard: false,
                            backdrop: 'static'
                        });
                        $("#request_load_modal").modal("hide");
                        $.ajax({
                            type: 'POST',
                            url: "./action/terminal-do.php",
                            data: {
                                "param": paramRQ,
                                "dt5": jenisRequest,
                                "dt6": catatan
                            },
                            cache: false,
                            dataType: "json",
                            success: function(data) {
                                console.log("Data yang dikirim:");
                                console.log("paramRQ (Param):", paramRQ);
                                console.log("dt5 (Jenis Request):", jenisRequest);
                                console.log("dt6 (Catatan):", catatan);

                                if ($("#jenisLP").val() == 2) {
                                    $("#data-kapal-table").ajaxGrid("draw");
                                } else {
                                    $("#data-truck-table").ajaxGrid("draw");
                                }
                                $("#loading_modal").modal("hide");

                                if (data.error) {
                                    $("#error_modal").find("#error-preview").html(data.error);
                                    $("#error_modal").modal({
                                        keyboard: false,
                                        backdrop: 'static'
                                    });
                                }
                            }
                        });
                    }
                }
            });

            //end 

            $('#data-truck-table tbody, #data-kapal-table tbody').on('click', '.getlokasimobil', function(e) {
                let nilai = $(this).data("mobil");
                if (nilai) {
                    $("#loading_modal").modal({
                        backdrop: "static"
                    });
                    $.ajax({
                        type: 'POST',
                        url: "./tracking-view.php",
                        data: {
                            "id1": nilai
                        },
                        cache: false,
                        dataType: "json",
                        success: function(data) {
                            if (data.hasil) {
                                const dataMap = data.items;
                                if (dataMap.length > 0) {
                                    if (map_track_view) {
                                        map_track_view.off();
                                        map_track_view.remove();
                                    }

                                    map_track_view = L.map('map_track_view').setView([dataMap[0].data['lat'], dataMap[0].data['lon']], 8);

                                    mapLink = '<a href="http://openstreetmap.org">OpenStreetMap</a>';
                                    L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                        attribution: '&copy; ' + mapLink + ' Contributors',
                                        maxZoom: 18,
                                    }).addTo(map_track_view);

                                    let i = 0;
                                    while (i < dataMap.length) {
                                        marker = new L.marker([dataMap[i].data['lat'], dataMap[i].data['lon']])
                                            .bindPopup(dataMap[i].data['vehicle_name'])
                                            .openPopup()
                                            .addTo(map_track_view);
                                        i++;
                                    }
                                }
                                $("#show_maptracking_modal").find(".infonya").addClass("hide").html("");
                                $("#show_maptracking_modal").find("#map_track_view").removeClass("hide");
                                $("#show_maptracking_modal").modal({
                                    backdrop: "static"
                                });
                            } else {
                                $("#show_maptracking_modal").find(".infonya").removeClass("hide").html(data.items);
                                $("#show_maptracking_modal").find("#map_track_view").addClass("hide");
                                $("#show_maptracking_modal").modal({
                                    backdrop: "static"
                                });
                            }
                        }
                    });
                }
            });

            $("#status_kirim_modal").on("shown.bs.modal", function() {
                $("#status_history_modal").modal("hide");
            }).on("hidden.bs.modal", function() {
                $("#status_kirim_modal").find(".stasi").removeClass("hide");
                $("#status_kirim_modal").find(".reali").removeClass("hide");
                $("#status_kirim_modal").find("#dt1, #dt2, #dt3, #idLP, #tipeLP, #stat_kirim, #real_kirim, #staj_kirim").val("");
                $("#status_kirim_modal").find("#errStatLP, #infoStatLP1, .modal-title").html("");
            }).on("click", "#load_now_modal", function() {
                var handler = function(data) {
                    $("#status_kirim_modal").find("#dt1").val(data.tanggal);
                    $("#status_kirim_modal").find("#dt2").val(data.jam);
                    $("#status_kirim_modal").find("#dt3").val(data.menit);
                };
                $.post("./datatable/get_tanggal.php", {}, handler, "json");
            }).on("click", "#btnLP1", function() {
                if ($("#stat_kirim").val() == "" || $("#dt1").val() == "" || $("#dt2").val() == "" || $("#dt3").val() == "") {
                    $("#errStatLP").html('<p class="text-red">Status, tanggal dan jam harus diisi...</p>');
                } else {
                    var tipe = $("#tipeLP").val(),
                        idnya = $("#idLP").val(),
                        status = $("#stat_kirim").val(),
                        dt1 = $("#dt1").val(),
                        dt2 = $("#dt2").val(),
                        dt3 = $("#dt3").val();

                    $("#loading_modal").modal({
                        keyboard: false,
                        backdrop: 'static'
                    });
                    $("#status_kirim_modal").modal("hide");
                    $.ajax({
                        type: 'POST',
                        url: "./action/pengiriman-list.php",
                        data: {
                            "file": "logistik",
                            "aksi": "ubah-kapal",
                            "status": status,
                            "dt1": dt1,
                            "dt2": dt2,
                            "dt3": dt3,
                            "param": idnya,
                            "tipe": tipe,


                        },
                        cache: false,
                        dataType: "json",
                        success: function(data) {
                            $("#loading_modal").modal("hide");
                            if (tipe == 1) $("#data-kapal-table").ajaxGrid("draw");
                            else $("#data-kapal-table").ajaxGrid("draw");
                            if (data.error) {
                                $("#error_modal").find("#error-preview").html(data.error);
                                $("#error_modal").modal({
                                    keyboard: false,
                                    backdrop: 'static'
                                });
                            }
                        }
                    });
                }
            }).on("click", "#btnLP2", function() {
                if (confirm("Produk telah diterima customer.\nApakah anda yakin ?")) {
                    if ($("#dt1").val() == "" || $("#dt2").val() == "" || $("#dt3").val() == "") {
                        $("#errStatLP").html('<p class="text-red">Tanggal dan jam harus diisi...</p>');
                    } else {
                        var tipe = $("#tipeLP").val(),
                            idnya = $("#idLP").val(),
                            dt1 = $("#dt1").val(),
                            dt2 = $("#dt2").val(),
                            customer_kapal = $("#customer").val();
                        customer_alamat_dr = $("#customer_alamat_dr").val();
                        dt3 = $("#dt3").val();
                        $("#loading_modal").modal({
                            keyboard: false,
                            backdrop: 'static'
                        });
                        $("#status_kirim_modal").modal("hide");
                        $.ajax({
                            type: 'POST',
                            url: "./action/pengiriman-list.php",
                            data: {
                                "file": "logistik",
                                "aksi": "selesai-kapal",
                                "dt1": dt1,
                                "dt2": dt2,
                                "dt3": dt3,
                                "param": idnya,
                                "tipe": tipe,
                                "customer_kapal": customer_kapal,
                                "customer_alamat_dr": customer_alamat_dr,
                            },
                            cache: false,
                            dataType: "json",
                            success: function(data) {
                                $("#loading_modal").modal("hide");
                                if (tipe == 1) $("#data-kapal-table").ajaxGrid("draw");
                                else $("#data-kapal-table").ajaxGrid("draw");
                                if (data.error) {
                                    $("#error_modal").find("#error-preview").html(data.error);
                                    $("#error_modal").modal({
                                        keyboard: false,
                                        backdrop: 'static'
                                    });
                                }
                            }
                        });
                    }
                }
            }).on("click", "#btnLP3", function() {
                if (confirm("Apakah anda yakin ?")) {
                    if ($("#dt1").val() == "" || $("#dt2").val() == "" || $("#dt3").val() == "" || $("#real_kirim").val() == "" || $("#staj_kirim").val() == "") {
                        $("#errStatLP").html('<p class="text-red">Seluruh field harus diisi...</p>');
                    } else {
                        var tipe = $("#tipeLP").val(),
                            idnya = $("#idLP").val(),
                            dt1 = $("#dt1").val(),
                            dt2 = $("#dt2").val(),
                            dt3 = $("#dt3").val();
                        var real = $("#real_kirim").val(),
                            staj = $("#staj_kirim").val();


                        console.log("Data yang dikirim:", {
                            tipe: tipe,
                            idnya: idnya,
                            dt1: dt1,
                            dt2: dt2,
                            dt3: dt3,
                            real: real,
                            staj: staj
                        });


                        $("#loading_modal").modal({
                            keyboard: false,
                            backdrop: 'static'
                        });
                        $("#status_kirim_modal").modal("hide");
                        $.ajax({
                            type: 'POST',
                            url: "./action/pengiriman-list.php",
                            data: {
                                "file": "logistik",
                                "aksi": "realisasi-kapal",
                                "dt1": dt1,
                                "dt2": dt2,
                                "dt3": dt3,
                                "dt4": real,
                                "dt5": staj,
                                "param": idnya,
                                "tipe": tipe

                            },
                            cache: false,
                            dataType: "json",
                            success: function(data) {
                                $("#loading_modal").modal("hide");
                                if (tipe == 1) $("#data-kapal-table").ajaxGrid("draw");
                                else $("#data-kapal-table").ajaxGrid("draw");
                                if (data.error) {
                                    $("#error_modal").find("#error-preview").html(data.error);
                                    $("#error_modal").modal({
                                        keyboard: false,
                                        backdrop: 'static'
                                    });
                                }
                            }
                        });
                    }
                }
            });

            $('#data-kapal-table tbody').on('click', '.editStsLoading', function(e) {
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
                    $("#status_load_modal").find("#paramLP").val(param);
                    $("#status_load_modal").find("#dt1_load").val(formattedDate);
                    $("#status_load_modal").find("#etl_val").val(formattedDateval);
                    $("#status_load_modal").find("#jenisLP").val(tkuLP);
                    $("#status_load_modal").find("#terminal").val(terminal);
                    $("#status_load_modal").find("#customer").val(customer);
                    $("#status_load_modal").modal({
                        keyboard: false,
                        backdrop: 'static'
                    });
                });
            });

            //pembaruan is_request develop *iwanhermawan 27/09/2024
            $('#data-truck-table tbody, #data-kapal-table tbody').on('click', '.editStsRequest', function(e) {
                var param = $(this).data("param");
                var jenis = $(this).data("jenis");
                var infor = $(this).data("info");
                var is_loaded = $(this).data("loaded");
                var loco = $(this).data("loco");
                var masa_akhir = $(this).data("masa");
                var angku = $(this).parents("table").first().attr("id");
                var tkuLP = (angku == "data-truck-table") ? 1 : 2;
                var arrMt = (jenis == "request") ?
                    'Request' : '';

                $("#request_load_modal").find("#ctt").addClass('hidden');
                $("#request_load_modal").find("#dt6").addClass('hidden');
                if (jenis == 'request') {
                    $("#request_load_modal").find("#ctt").removeClass('hidden');
                    $("#request_load_modal").find("#dt6").removeClass('hidden');
                }

                $.post("./datatable/get_info_loading.php", {
                    param: infor
                }, function(data) {
                    $("#request_load_modal").find(".modal-title").html(arrMt);
                    $("#request_load_modal").find("#infoStatLP").html(data);
                    $("#request_load_modal").find("#lb5").html("Jenis " + jenis);
                    $("#request_load_modal").find("#ctt").html("Catatan " + jenis);
                    $("#request_load_modal").find("#paramRQ").val(param);
                    $("#request_load_modal").find("#jenisLP").val(tkuLP);
                    $("#request_load_modal").find("#is_loaded").val(is_loaded); // Update elemen input
                    $("#request_load_modal").find("#loco").val(loco); // Update elemen input
                    $("#request_load_modal").find("#masa_akhir").val(masa_akhir); // Update elemen input
                    $("#request_load_modal").modal({
                        keyboard: false,
                        backdrop: 'static'
                    });
                });
            });

            //end

            $("#show_maptracking_modal").on("show.bs.modal", function() {
                $("#loading_modal").modal("hide");
            }).on("shown.bs.modal", function() {
                setTimeout(function() {
                    map_track_view.invalidateSize();
                }, 10);
            }).on("hidden.bs.modal", function() {
                $("body").css("padding-right", "0px");
            }).on("click", ".btnBatal_show_maptracking_modal", function() {
                $("#show_maptracking_modal").modal("hide");
            });


        });
    </script>
</body>

</html>
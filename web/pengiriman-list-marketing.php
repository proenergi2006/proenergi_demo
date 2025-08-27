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
                <h1>List Pengiriman</h1>
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
                                    <select id="q2" name="q2" class="form-control">
                                        <option></option>
                                        <option value="1">Tanggal PO Customer</option>
                                        <option value="2">Tanggal Kirim</option>
                                        <option value="3">Tanggal ETL</option>
                                        <option value="4">Tanggal ETA</option>
                                    </select>
                                </div>
                                <div class="col-sm-3 col-sm-top">
                                    <div class="input-group">
                                        <span class="input-group-addon">Periode</span>
                                        <input type="text" name="q3" id="q3" class="form-control input-sm datepicker" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-sm-3 col-sm-top">
                                    <div class="input-group">
                                        <span class="input-group-addon">S/D</span>
                                        <input type="text" name="q4" id="q4" class="form-control input-sm datepicker" autocomplete="off" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <select id="q5" name="q5" class="form-control">
                                        <option></option>
                                        <option value="5">Belum Loading</option>
                                        <option value="2">Loaded</option>
                                        <option value="3">Delivered</option>
                                        <option value="4">Cancel</option>
                                    </select>
                                </div>
                                <div class="col-sm-9 col-sm-top">
                                    <button type="submit" class="btn btn-info btn-sm" name="btnSearch1" id="btnSearch1" style="width:80px;">Cari</button>
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
                                    <div class="box-body table-responsive">
                                        <table class="table table-bordered" id="data-truck-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" width="7%">No</th>
                                                    <th class="text-center" width="24%">Customer</th>
                                                    <th class="text-center" width="16%">PO Customer</th>
                                                    <th class="text-center" width="15%">Transportir</th>
                                                    <th class="text-center" width="15%">Keterangan Lain</th>
                                                    <th class="text-center" width="16%">Depot/ Seal</th>
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

                    <div role="tabpanel" class="tab-pane" id="data-kapal">
                        <form name="searchForm2" id="searchForm2" role="form" class="form-horizontal">
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <input type="text" class="form-control input-sm" name="q1k" id="q1k" placeholder="Keywords..." />
                                </div>
                                <div class="col-sm-3 col-sm-top">
                                    <div class="input-group">
                                        <span class="input-group-addon">Tgl Kirim</span>
                                        <input type="text" name="q2k" id="q2k" class="form-control input-sm datepicker" />
                                    </div>
                                </div>
                                <div class="col-sm-3 col-sm-top">
                                    <div class="input-group">
                                        <span class="input-group-addon">S/D</span>
                                        <input type="text" name="q3k" id="q3k" class="form-control input-sm datepicker" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <select id="q4k" name="q4k" class="form-control">
                                        <option></option>
                                        <option value="5">Belum Loading</option>
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
                                                    <th class="text-center" width="7%">No</th>
                                                    <th class="text-center" width="24%">Customer</th>
                                                    <th class="text-center" width="16%">PO Customer</th>
                                                    <th class="text-center" width="16%">Transportir</th>
                                                    <th class="text-center" width="15%">Keterangan Lain</th>
                                                    <th class="text-center" width="15%">Depot/ Notify Party</th>
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
                                    <table class="table table-bordered table-hover" id="listHistoriLP">
                                        <thead>
                                            <tr>
                                                <th class="text-center" width="6%">No</th>
                                                <th class="text-center" width="16%">Tanggal</th>
                                                <th class="text-center" width="78%">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div id="detilHistoriLp"></div>
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

        @media screen and (min-width: 992px) {
            .modal-dialog-histori {
                width: 70%;
            }
        }

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
        $(document).ready(function() {
            var map_track_view;

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

            $("#data-truck-table").ajaxGrid({
                url: "./datatable/pengiriman-list-marketing-truck.php",
                data: {
                    q1: $("#q1").val(),
                    q2: $("#q2").val(),
                    q3: $("#q3").val(),
                    q4: $("#q4").val(),
                    q5: $("#q5").val()
                },
            });
            $("#btnSearch1").on("click", function() {
                $("#data-truck-table").ajaxGrid("draw", {
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
                $("#data-truck-table").ajaxGrid("pageLen", $(this).val());
            });
            $('#data-truck-table').on('click', '.openModalTracking', function() {
                var plat_nomor = $(this).attr('data-mobil');
                window.open("https://oslog.id/embedd-monitoring-vehicle/index.html?apikey_jyoti=3549af8b-2607-4415-8d0d-09130e2e4c29&licensePlate=" + plat_nomor)
                // var iframe = $("#myiframeMaps");
                // // iframe.attr("src", "https://oslog.id/embedd-monitoring-vehicle/index.html?apiKey=3549af8b-2607-4415-8d0d-09130e2e4c29&licensePlate=" + plat_nomor);
                // $('#modalLiveTracking').modal({
                // 	show: true
                // })
            });
            $('#data-truck-table').on('click', '.openMonitoringDispatch', function() {
                var id_dsd = $(this).attr('data-param');
                var iframe = $("#myiframeDispatch");
                iframe.attr("src", "https://oslog.id/embedd-monitoring-dispatch?apiKey=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJJZCI6MTE1OSwiTmFtZSI6InByb2VuZXJnaSIsIlJvbGUiOiJhZG1fcHJvZW5lcmdpIiwiQ29tcGFueSI6NjA2LCJVc2VyUG9kSWQiOjAsImlzcyI6Ik9TTE9HIDUgQVBJIn0.H-ljfy7I0zVzpvXsar3FddpUT2RHChNaEP8uw50kmV8&shipmentIdInternal=" + id_dsd);
                $('#modalDispatch').modal({
                    show: true
                })
            });
            $('#data-truck-table').on('click', '.historyTracking', function() {
                var id_dsd = $(this).attr('data-param');
                var plat_nomor = $(this).attr('data-plate');
                window.open("https://oslog.id/embedd-monitoring-vehicle/index.html?apiKey=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJJZCI6MTE1OSwiTmFtZSI6InByb2VuZXJnaSIsIlJvbGUiOiJhZG1fcHJvZW5lcmdpIiwiQ29tcGFueSI6NjA2LCJVc2VyUG9kSWQiOjAsImlzcyI6Ik9TTE9HIDUgQVBJIn0.H-ljfy7I0zVzpvXsar3FddpUT2RHChNaEP8uw50kmV8&apikey_jyoti=3549af8b-2607-4415-8d0d-09130e2e4c29&licensePlate=" + plat_nomor + "&shipmentIdInternal=" + id_dsd)
            });
            $('#data-truck-table tbody, #data-kapal-table tbody').on('click', '.listStsT', function(e) {
                var param = $(this).data("param");
                $("#status_kirim_modal").modal("hide");
                $("#status_history_modal").find("#listHistoriLP > tbody, #jdlKirim").html("");
                $("#loading_modal").modal({
                    backdrop: "static"
                });
                $.ajax({
                    type: 'POST',
                    url: "./__get_pengiriman_list.php",
                    data: {
                        "file": "marketing",
                        "aksi": param
                    },
                    cache: false,
                    dataType: "json",
                    success: function(data) {
                        $("#loading_modal").modal("hide");
                        $("#status_history_modal").find("#listHistoriLP > tbody").html(data.items);
                        $("#status_history_modal").find("#jdlKirim").html(data.judul);
                        $("#status_history_modal").find("#detilHistoriLp").html(data.extras);
                    }
                });
                $("#status_history_modal").modal();
            }).on('click', '.getlokasimobil', function(e) {
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

            $("#status_history_modal").on("shown.bs.modal", function() {}).on("hidden.bs.modal", function() {
                $("body").css("padding-right", "0px");
            });

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

            $("[data-toggle='tab']").click(function() {
                var $this = $(this);
                var idnya = $this.attr('href');
                var urlnya = (idnya == "#data-truck") ? "./datatable/pengiriman-list-marketing-truck.php" : "./datatable/pengiriman-list-marketing-kapal.php";
                $(idnya + "-table").ajaxGrid({
                    url: urlnya,
                    data: {
                        q1: "",
                        q2: "",
                        q3: "",
                        q4: "",
                        q5: ""
                    },
                });
                $this.tab('show');
                return false;
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
        });
    </script>
</body>

</html>
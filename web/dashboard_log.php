<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#data-maps" aria-controls="data-maps" role="tab" data-toggle="tab">Maps</a></li>
    <li role="presentation" class=""><a href="#data-task-pengiriman" aria-controls="data-task-pengiriman" role="tab" data-toggle="tab">Task Pengiriman</a></li>
    <li role="presentation" class=""><a href="#data-lead-time" aria-controls="data-lead-time" role="tab" data-toggle="tab">Lead Time</a></li>
    <li role="presentation" class=""><a href="#data-losses" aria-controls="data-losses" role="tab" data-toggle="tab">Losses</a></li>
</ul>

<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="data-maps">
        <div class="row">
            <div class="col-sm-12">
                <div class="container-fluid">
                    <iframe src="https://oslog.id/embedd-maps-monitoring/?apiKey=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJJZCI6MTE1OSwiTmFtZSI6InByb2VuZXJnaSIsIlJvbGUiOiJhZG1fcHJvZW5lcmdpIiwiQ29tcGFueSI6NjA2LCJVc2VyUG9kSWQiOjAsImlzcyI6Ik9TTE9HIDUgQVBJIn0.H-ljfy7I0zVzpvXsar3FddpUT2RHChNaEP8uw50kmV8&apikey_jyoti=3549af8b-2607-4415-8d0d-09130e2e4c29&c=606&uid=1159" id="myiframe" width="100%" height="600vh" frameborder="0"></iframe>
                </div>
            </div>
        </div>
    </div>

    <div role="tabpanel" class="tab-pane" id="data-task-pengiriman">
        <div class="row">
            <div class="col-sm-12">
                <div class="box box-info">
                    <div class="container-fluid">
                        <h4>Task Pengiriman</h4>
                        <br>
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
                                <div class="col-sm-3 col-sm-top">
                                    <button type="submit" class="btn btn-info btn-sm jarak-kanan" name="btnSearch" id="btnSearch" style="width:80px;">Cari</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="text-left" style="margin-top: 10px">
                                    <span>Lihat semuanya di <a href="<?php echo BASE_URL_CLIENT . "/pengiriman-list-logistik.php"; ?>">List Pengiriman</a></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-right" style="margin-top: 10px">Show
                                    <select name="tableGridLength" id="tableGridLength">
                                        <option value="5" selected>5</option>
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                    </select> Data
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-body table-responsive">
                        <table class="table table-bordered" id="table-grid">
                            <thead>
                                <tr>
                                    <th class="text-center" width="5%">No</th>
                                    <th class="text-center" width="20%">Nomor DN</th>
                                    <th class="text-center" width="16%">PO Customer</th>
                                    <th class="text-center" width="16%">PO Transportir</th>
                                    <th class="text-center" width="10%">Transportir</th>
                                    <th class="text-center" width="15%">Keterangan Lain</th>
                                    <th class="text-center" width="25%">Depot/Seal</th>
                                    <th class="text-center" width="">Status</th>
                                    <th class="text-center" width="7%">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
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
                                    <td style="padding:0px 5px;" width="100">
                                        <p id="lb1" style="font-weight:bold; margin-bottom:5px;"></p>
                                        <input type="text" name="dt1_load" id="dt1_load" class="input-sm datepicker form-control" autocomplete="off" />
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

    <div class="modal fade" id="status_kirim_modal" tabindex="-1" role="dialog" aria-hidden="true">
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
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" style="padding:10px 5px;" class="reali">
                                    <p style="font-weight:bold; margin-bottom:5px;">Realisasi Volume</p>
                                    <div class="input-group">
                                        <input type="text" name="real_kirim" id="real_kirim" class="form-control hitung reali" />
                                        <span class="input-group-addon">Liter</span>
                                    </div>
                                </td>
                                <td style="padding:10px 5px;" class="teradepo">
                                    <p style="font-weight:bold; margin-bottom:5px;">Tera Depo</p>
                                    <input type="text" name="tera_depo" id="tera_depo" class="form-control teradepo" maxlength="6" onkeypress="return onlyNumberKey(event)" />
                                </td>
                                <td style="padding:10px 5px;" class="terasite">
                                    <p style="font-weight:bold; margin-bottom:5px;">Tera Site</p>
                                    <input type="text" name="tera_site" id="tera_site" class="form-control terasite" maxlength="6" onkeypress="return onlyNumberKey(event)" />
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div role="tabpanel" class="tab-pane" id="data-lead-time">
        <div class="row">
            <div class="col-sm-12">
                <div class="box box-info">
                    <div class="container-fluid">
                        <h4>Lead Time</h4>
                        <br>
                        <form name="searchForm2" id="searchForm2" role="form" class="form-horizontal">
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <input type="text" class="form-control input-sm" name="q1lt" id="q1lt" placeholder="Keywords..." />
                                </div>
                                <div class="col-sm-3 col-sm-top">
                                    <div class="input-group">
                                        <span class="input-group-addon">Tgl Kirim</span>
                                        <input type="text" name="q2lt" id="q2lt" class="form-control input-sm datepicker" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-sm-3 col-sm-top">
                                    <div class="input-group">
                                        <span class="input-group-addon">S/D</span>
                                        <input type="text" name="q3lt" id="q3lt" class="form-control input-sm datepicker" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-sm-3 col-sm-top">
                                    <button type="submit" class="btn btn-info btn-sm jarak-kanan" name="btnSearch2" id="btnSearch2" style="width:80px;">Cari</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="text-left" style="margin-top: 10px">
                                    <span>Lihat semuanya di <a href="<?php echo BASE_URL_CLIENT . "/report/l-lead-time.php"; ?>">Lead Time</a></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-right" style="margin-top: 10px">Show
                                    <select name="tableGridLength2" id="tableGridLength2">
                                        <option value="5" selected>5</option>
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                    </select> Data
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-body table-responsive">
                        <table class="table table-bordered" id="table-grid2">
                            <thead>
                                <tr>
                                    <th class="text-center" width="5%">No</th>
                                    <th class="text-center" width="20%">No.PO/Customer/Cabang Invoice</th>
                                    <th class="text-center" width="16%">Area/Alamat Kirim/Depot</th>
                                    <th class="text-center" width="16%">Transportir/Sopir/No.Pol</th>
                                    <th class="text-center" width="15%">SPJ/Volume SJ</th>
                                    <th class="text-center" width="10%">Tanggal Loading</th>
                                    <th class="text-center" width="10%">Tanggal Terkirim</th>
                                    <th class="text-center" width="20%">Total Waktu Pengiriman</th>
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

    <div role="tabpanel" class="tab-pane" id="data-losses">
        <div class="row">
            <div class="col-sm-12">
                <div class="box box-info">
                    <div class="container-fluid">
                        <h4>Losses</h4>
                        <br>
                        <form name="searchForm2" id="searchForm2" role="form" class="form-horizontal">
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <input type="text" class="form-control input-sm" name="ql1" id="ql1" placeholder="Keywords..." />
                                </div>
                                <div class="col-sm-3 col-sm-top">
                                    <div class="input-group">
                                        <span class="input-group-addon">Tgl Kirim</span>
                                        <input type="text" name="ql2" id="ql2" class="form-control input-sm datepicker" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-sm-3 col-sm-top">
                                    <div class="input-group">
                                        <span class="input-group-addon">S/D</span>
                                        <input type="text" name="ql3" id="ql3" class="form-control input-sm datepicker" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-sm-3 col-sm-top">
                                    <button type="submit" class="btn btn-info btn-sm jarak-kanan" name="btnSearch3" id="btnSearch3" style="width:80px;">Cari</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="text-left" style="margin-top: 10px">
                                    <span>Lihat semuanya di <a href="<?php echo BASE_URL_CLIENT . "/report/l-losses.php"; ?>">Losess</a></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-right" style="margin-top: 10px">Show
                                    <select name="tableGridLength3" id="tableGridLength3">
                                        <option value="5" selected>5</option>
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                    </select> Data
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-body table-responsive">
                        <table class="table table-bordered" id="table-grid3">
                            <thead>
                                <tr>
                                    <th class="text-center" width="6%">Tgl Kirim</th>
                                    <th class="text-center" width="19%">No. PO/ Customer/<br>Cabang Invoice</th>
                                    <th class="text-center" width="19%">Area/ Wilayah Kirim/ Depot</th>
                                    <th class="text-center" width="19%">Transportir/ Sopir/ No. Pol</th>
                                    <th class="text-center" width="8%">Volume SJ</th>
                                    <th class="text-center" width="8%">Volume Terkirim</th>
                                    <th class="text-center" width="8%">Losses</th>
                                    <th class="text-center" width="13%">No. SPJ &amp; Keterangan</th>
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

<style>
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

<script>
    $(document).ready(function() {
        $("#table-grid").ajaxGrid({
            url: "./datatable/dashboard_log.php",
            data: {
                tipe: "task_pengiriman",
                q1: $("#q1").val(),
                q2: $("#q2").val(),
                q3: $("#q3").val()
            },
        });
        $('#btnSearch').on('click', function() {
            $("#table-grid").ajaxGrid("draw", {
                data: {
                    q1: $("#q1").val(),
                    q2: $("#q2").val(),
                    q3: $("#q3").val()
                }
            });
            return false;
        });
        $('#tableGridLength').on('change', function() {
            $("#table-grid").ajaxGrid("pageLen", $(this).val());
        });

        $('#table-grid tbody').on('click', '.editStsT', function(e) {
            var param = $(this).data("param");
            var infor = $(this).data("info");
            var reali = $(this).data("realisasi");
            var status = $(this).data("status");
            var angku = $(this).parents("table").first().attr("id");
            var tkuLP = (angku == "table-grid") ? 1 : 2;

            if (status == 'loaded') {
                $(".teradepo").hide();
                $(".terasite").hide();
            } else {
                $(".teradepo").show();
                $(".terasite").show();
            }

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
                        "aksi": "ubah",
                        "status": status,
                        "dt1": dt1,
                        "dt2": dt2,
                        "dt3": dt3,
                        "param": idnya,
                        "tipe": tipe
                    },
                    cache: false,
                    dataType: "json",
                    success: function(data) {
                        $("#loading_modal").modal("hide");
                        if (tipe == 1) $("#table-grid").ajaxGrid("draw");
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
                            "aksi": "selesai",
                            "dt1": dt1,
                            "dt2": dt2,
                            "dt3": dt3,
                            "param": idnya,
                            "tipe": tipe
                        },
                        cache: false,
                        dataType: "json",
                        success: function(data) {
                            $("#loading_modal").modal("hide");
                            if (tipe == 1) $("#table-grid").ajaxGrid("draw");
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
        })

        $('#table-grid').on('click', '.openModalTracking', function() {
            var plat_nomor = $(this).attr('data-mobil');
            window.open("https://oslog.id/embedd-monitoring-vehicle/index.html?apikey_jyoti=3549af8b-2607-4415-8d0d-09130e2e4c29&licensePlate=" + plat_nomor)
            // var iframe = $("#myiframeMaps");
            // // iframe.attr("src", "https://oslog.id/embedd-monitoring-vehicle/index.html?apiKey=3549af8b-2607-4415-8d0d-09130e2e4c29&licensePlate=" + plat_nomor);
            // $('#modalLiveTracking').modal({
            // 	show: true
            // })
        });

        $('#table-grid').on('click', '.openMonitoringDispatch', function() {
            var id_dsd = $(this).attr('data-param');
            var iframe = $("#myiframeDispatch");
            iframe.attr("src", "https://oslog.id/embedd-monitoring-dispatch?apiKey=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJJZCI6MTE1OSwiTmFtZSI6InByb2VuZXJnaSIsIlJvbGUiOiJhZG1fcHJvZW5lcmdpIiwiQ29tcGFueSI6NjA2LCJVc2VyUG9kSWQiOjAsImlzcyI6Ik9TTE9HIDUgQVBJIn0.H-ljfy7I0zVzpvXsar3FddpUT2RHChNaEP8uw50kmV8&shipmentIdInternal=" + id_dsd);
            $('#modalDispatch').modal({
                show: true
            })
        });

        $("#table-grid2").ajaxGrid({
            url: "./datatable/dashboard_log.php",
            data: {
                tipe: "lead_time",
                q1lt: $("#q1lt").val(),
                q2lt: $("#q2lt").val(),
                q3lt: $("#q3lt").val()
            },
        });
        $('#btnSearch2').on('click', function() {
            $("#table-grid2").ajaxGrid("draw", {
                data: {
                    q1lt: $("#q1lt").val(),
                    q2lt: $("#q2lt").val(),
                    q3lt: $("#q3lt").val()
                }
            });
            return false;
        });
        $('#tableGridLength2').on('change', function() {
            $("#table-grid2").ajaxGrid("pageLen", $(this).val());
        });

        $('#table-grid tbody').on('click', '.editStsLoading', function(e) {
            var param = $(this).data("param");
            var jenis = $(this).data("jenis");
            var infor = $(this).data("info");
            var angku = $(this).parents("table").first().attr("id");
            var tkuLP = (angku == "table-grid") ? 1 : 2;
            var arrMt = (jenis == "loading") ? 'Status Loading' : 'Penolakan Pengiriman';

            $("#status_load_modal").find("#ctt").addClass('hidden');
            $("#status_load_modal").find("#dt4").addClass('hidden');
            if (jenis == 'loading') {
                $("#status_load_modal").find("#ctt").removeClass('hidden');
                $("#status_load_modal").find("#dt4").removeClass('hidden');
            }

            $.post("./datatable/get_info_loading.php", {
                param: infor
            }, function(data) {
                $("#status_load_modal").find(".modal-title").html(arrMt);
                $("#status_load_modal").find("#infoStatLP").html(data);
                $("#status_load_modal").find("#lb1").html("Tanggal " + jenis);
                $("#status_load_modal").find("#lb2").html("Jam " + jenis);
                $("#status_load_modal").find("#ctt").html("Catatan " + jenis);
                $("#status_load_modal").find("#paramLP").val(param);
                $("#status_load_modal").find("#jenisLP").val(tkuLP);
                $("#status_load_modal").modal({
                    keyboard: false,
                    backdrop: 'static'
                });
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
            Swal.fire({
                title: "Anda yakin simpan?",
                showCancelButton: true,
                confirmButtonText: "Simpan",
            }).then((result) => {
                if (result.isConfirmed) {
                    if ($("#dt1_load").val() == "" || $("#dt2_load").val() == "" || $("#dt3_load").val() == "") {
                        $("#errStatLP").html('<p class="text-red">Tanggal dan jam belum diisi...</p>');
                    } else {
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
                                "dt4": $("#dt4_load").val()
                            },
                            cache: false,
                            dataType: "json",
                            success: function(data) {
                                $("#table-grid").ajaxGrid("draw");
                                //if(data.badge > 0) $("#menubadge21").html(data.badge);
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
        });

        $("#table-grid3").ajaxGrid({
            url: "./datatable/dashboard_log.php",
            data: {
                tipe: "losess",
                ql1: $("#ql1").val(),
                ql2: $("#ql2").val(),
                ql3: $("#ql3").val()
            },
        });
        $('#btnSearch3').on('click', function() {
            $("#table-grid3").ajaxGrid("draw", {
                data: {
                    ql1: $("#ql1").val(),
                    ql2: $("#ql2").val(),
                    ql3: $("#ql3").val()
                }
            });
            return false;
        });
        $('#tableGridLength3').on('change', function() {
            $("#table-grid3").ajaxGrid("pageLen", $(this).val());
        });
    })
</script>
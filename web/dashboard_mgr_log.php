<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#data-maps" aria-controls="data-maps" role="tab" data-toggle="tab">Maps</a></li>
    <li role="presentation" class=""><a href="#data-task-pengiriman" aria-controls="data-task-pengiriman" role="tab" data-toggle="tab">Task Pengiriman</a></li>
    <li role="presentation" class=""><a href="#data-lead-time" aria-controls="data-lead-time" role="tab" data-toggle="tab">Lead Time</a></li>
    <li role="presentation" class=""><a href="#data-losess" aria-controls="data-losess" role="tab" data-toggle="tab">Losses</a></li>
    <li role="presentation" class=""><a href="#data-monitoring" aria-controls="data-monitoring" role="tab" data-toggle="tab">monitoring</a></li>
    <li role="presentation" class=""><a href="#data-unit" aria-controls="data-unit" role="tab" data-toggle="tab">Unit</a></li>
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
                                    <span>Lihat semuanya di <a href="<?php echo BASE_URL_CLIENT . "/pengiriman-list-view.php"; ?>">List Pengiriman</a></span>
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

    <div role="tabpanel" class="tab-pane" id="data-losess">
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

    <div role="tabpanel" class="tab-pane" id="data-monitoring">
        <div class="row">
            <div class="col-sm-12">
                <div class="box box-info">
                    <div class="container-fluid">
                        <h4>Monitoring</h4>
                        <br>
                    </div>
                    <div class="box-body table-responsive">
                        <table class="table table-bordered" id="table-grid3">
                            <thead>
                                <tr>
                                    <th class="text-center" width="8%">Cabang</th>
                                    <th class="text-center" width="8%">DR</th>
                                    <th class="text-center" width="8%">PO</th>
                                    <th class="text-center" width="8%">Backlog</th>
                                    <th class="text-center" width="8%">Loading</th>
                                    <th class="text-center" width="8%">Delivered</th>
                                    <th class="text-center" width="8%">Realisasi</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Query untuk mendapatkan data yang diperlukan
                                $sql1 = "select
                                c.nama_cabang,
                                COALESCE(SUM(CASE WHEN b.disposisi_pr = 6 THEN 1 ELSE 0 END), 0) AS dr_count,
                                COALESCE(SUM(CASE WHEN d.po_approved = 0 THEN 1 ELSE 0 END), 0) AS po_count,
                                COALESCE(SUM(CASE WHEN f.is_submitted = 0 THEN 1 ELSE 0 END), 0) AS backlog_count,
                                COALESCE(SUM(CASE WHEN e.is_loaded = 1 AND e.is_cancel != 1 THEN 1 ELSE 0 END), 0) AS loading_count,
                                COALESCE(SUM(CASE WHEN e.is_delivered = 0 AND e.is_cancel != 1 AND e.is_loaded = 1 THEN 1 ELSE 0 END), 0) AS delivered_count,
                                COALESCE(SUM(CASE WHEN e.realisasi_volume = 0 AND e.is_cancel != 1 AND e.is_loaded = 1 AND e.is_delivered = 1 THEN 1 ELSE 0 END), 0) AS realisasi_count
                                FROM 
                                    pro_master_cabang c
                                LEFT JOIN 
                                    pro_pr b ON c.id_master = b.id_wilayah AND b.tanggal_pr = CURDATE()
                                LEFT JOIN 
                                    pro_pr_detail a ON a.id_pr = b.id_pr
                                LEFT JOIN 
                                    pro_po d ON b.id_pr = d.id_pr
                                LEFT JOIN 
                                    pro_po_ds_detail e ON b.id_pr = e.id_pr
								LEFT JOIN 
								    pro_po_ds f on e.id_ds = f.id_ds
                                WHERE 
                                    c.id_master NOT IN (1, 8, 10)
                                GROUP BY 
                                    c.nama_cabang
                                            ";

                                // Mendapatkan hasil query
                                $result1 = $con->getResult($sql1);

                                foreach ($result1 as $data) {
                                    echo '<tr>';
                                    echo '<td><strong>' . $data['nama_cabang'] . '</strong></td>';
                                    echo '<td>' . $data['dr_count'] . '</td>';
                                    echo '<td>' . $data['po_count'] . '</td>';
                                    echo '<td>' . $data['backlog_count'] . '</td>';
                                    echo '<td>' . $data['loading_count'] . '</td>';
                                    echo '<td>' . $data['delivered_count'] . '</td>';
                                    echo '<td>' . $data['realisasi_count'] . '</td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div role="tabpanel" class="tab-pane" id="data-unit">

        <!-- Filter Periode -->
        <div class="form-group row">
            <div class="col-sm-4">
                <label>Periode</label>
                <input type="date" id="startDate" class="form-control">
            </div>
            <div class="col-sm-4">
                <label>S/D</label>
                <input type="date" id="endDate" class="form-control">
            </div>
            <div class="col-sm-4">
                <label>Cabang</label>
                <select id="branchFilter" class="form-control" multiple="multiple">
                    <?php $con->fill_select("id_master", "nama_cabang", "pro_master_cabang", $q7, "where is_active=1 and id_master != 1", "", false); ?>
                </select>
            </div>

        </div>
        <div class="form-group row">
            <div class="col-sm-4">
                <button id="applyFilter" class="btn btn-primary">Search</button>
            </div>
        </div>





        <!-- Grafik -->
        <div class="row">
            <div class="col-sm-6">
                <div class="box box-info">
                    <div class="container-fluid">
                        <h4>Unit Nasional</h4>
                        <canvas id="compareChart" width="600" height="300"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="box box-info">
                    <div class="container-fluid">
                        <h4>Unit Cabang</h4>
                        <canvas id="branchCompareChart" width="600" height="300"></canvas>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
    $(document).ready(function() {
        $("#table-grid").ajaxGrid({
            url: "./datatable/dashboard_mgr_log.php",
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

        $("#table-grid2").ajaxGrid({
            url: "./datatable/dashboard_mgr_log.php",
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

        $("#table-grid3").ajaxGrid({
            url: "./datatable/dashboard_mgr_log.php",
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

<script>
    $(document).ready(function() {
        $('#branchFilter').select2({
            placeholder: 'Pilih Cabang',
            allowClear: true
        });
    });
    const bulan = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    let compareChartInstance = null;
    let branchChartInstance = null;

    function loadCharts(startDate = '', endDate = '', branch = '') {
        // Chart Nasional
        $.getJSON('./datatable/dashboard_unit_data.php', {
            start: startDate,
            end: endDate

        }, function(res) {
            const labelsFormatted = res.labels.map(dstr => {
                const d = new Date(dstr);
                return `${d.getDate()} ${bulan[d.getMonth()]} ${d.getFullYear()}`;
            });

            if (compareChartInstance) compareChartInstance.destroy();

            const ctx1 = document.getElementById('compareChart').getContext('2d');
            compareChartInstance = new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: labelsFormatted,
                    datasets: [{
                            label: 'Pro Energi',
                            data: res.proEnergi,
                            fill: false,
                            tension: 0.1,
                            volume: res.pro_energi_volume
                        },
                        {
                            label: 'Third Party',
                            data: res.thirdParty,
                            fill: false,
                            tension: 0.1,
                            volume: res.thirdparty_volume

                        }
                    ]
                },
                options: {
                    plugins: {
                        datalabels: {
                            color: '#000',
                            align: 'top',
                            anchor: 'end',
                            formatter: v => v
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const index = context.dataIndex;
                                    const dataset = context.dataset;
                                    const value = dataset.data[index];
                                    const volume = dataset.volume ? dataset.volume[index] : 0;

                                    return `${dataset.label}: ${value} PO, Volume: ${volume.toLocaleString()}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Tanggal'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Jumlah PO'
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        });

        // Chart Cabang
        $.getJSON('./datatable/dashboard_po_branch.php', {
            start: startDate,
            end: endDate,
            branch: branch
        }, function(res) {
            if (branchChartInstance) branchChartInstance.destroy();

            branchChartInstance = new Chart(document.getElementById('branchCompareChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: res.labels,
                    datasets: res.datasets
                },
                options: {
                    plugins: {
                        datalabels: {
                            color: '#000',
                            align: 'top',
                            anchor: 'end',
                            formatter: v => v
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Tanggal'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Jumlah PO'
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        });
    }

    // Event tombol Terapkan
    $('#applyFilter').on('click', function() {
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();
        const branch = $('#branchFilter').val();
        loadCharts(startDate, endDate, branch);
    });

    // Load awal tanpa filter
    loadCharts();
</script>
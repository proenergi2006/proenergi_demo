<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth    = new MyOtentikasi();
$enk     = decode($_SERVER['REQUEST_URI']);
$con     = new Connection();
$flash   = new FlashAlerts;

include_once($public_base_directory . "/web/__get_inventory_stock_pr_super.php");
//include_once($public_base_directory . "/web/__sc_inventory_stock_pr.php");
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<style>
    .hover-info {
        position: absolute;
        bottom: 10px;
        right: 10px;
        display: none;
        /* Hidden initially */
        background-color: rgba(255, 255, 255, 0.8);
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 12px;
        color: #333;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
    }

    .box:hover .hover-info {
        display: inline-flex;
        /* Show on hover */
        align-items: center;
    }

    .hover-info i {
        margin-right: 5px;
        /* Space between icon and text */
    }

    .info-box {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        border-radius: 5px;
        /* Opsional, untuk membuat sudut lebih lembut */
    }

    .info-box:hover {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        /* Shadow lebih besar saat hover */
    }

    .branch-filter {
        margin-top: 30px;
        /* Atur nilai sesuai kebutuhan */
    }

    @import "https://code.highcharts.com/css/highcharts.css";

    .highcharts-pie-series .highcharts-point {
        stroke: #ede;
        stroke-width: 2px;
    }

    .highcharts-pie-series .highcharts-data-label-connector {
        stroke: silver;
        stroke-dasharray: 2, 2;
        stroke-width: 2px;
    }

    .highcharts-figure,
    .highcharts-data-table table {
        min-width: 320px;
        max-width: 600px;
        margin: 1em auto;
    }

    .highcharts-data-table table {
        font-family: Verdana, sans-serif;
        border-collapse: collapse;
        border: 1px solid #ebebeb;
        margin: 10px auto;
        text-align: center;
        width: 100%;
        max-width: 500px;
    }

    .highcharts-data-table caption {
        padding: 1em 0;
        font-size: 1.2em;
        color: #555;
    }

    .highcharts-data-table th {
        font-weight: 600;
        padding: 0.5em;
    }

    .highcharts-data-table td,
    .highcharts-data-table th,
    .highcharts-data-table caption {
        padding: 0.5em;
    }

    .highcharts-data-table thead tr,
    .highcharts-data-table tr:nth-child(even) {
        background: #f8f8f8;
    }

    .highcharts-data-table tr:hover {
        background: #f1f7ff;
    }

    .amcharts-chart-div a[href="https://www.amcharts.com/"] {
        display: none !important;
    }

    /* Sembunyikan teks "JavaScript chart by amCharts" */
    .amcharts-chart-div div[aria-label="JavaScript chart by amCharts"] {
        display: none !important;
    }

    #chartdiv {
        width: 100%;
        height: 500px;

    }

    .chart-container {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        /* Menunjukkan hanya 4 kolom di baris pertama */
        grid-gap: 15px;
    }

    .chart-item {
        border: 1px solid #ccc;
        border-radius: 5px;
        overflow: hidden;
        width: 100%;
    }

    .main-chart {
        grid-column: span 2;
        /* Menggunakan dua kolom untuk grafik pertama */
    }

    .hidden-chart {
        display: none;
        /* Menyembunyikan grafik sisanya */
    }

    /* Style untuk modal */
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(0, 0, 0);
        background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 10% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    #table-grid3 {
        border-collapse: separate;
    }
</style>

<div class="row">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-10">
                <div style="font-size:12px;"><b> Branch</b></div>
            </div>
            <div class="col-sm-2">
                <div style="font-size:12px;"> <b>Date</b> : <span id="current-date"></span></div>

            </div>

        </div>

        <div class="row" style="margin-top: 8px;">
            <div class="col-sm-4">

                <select name="q4" id="q4" class="form-control select2">
                    <option></option>
                    <?php $con->fill_select("id_master", "nama_cabang", "pro_master_cabang", '', "where is_active=1 and id_master <> 1", "nama_cabang", false); ?>

                </select>
                <p></p>
            </div>
            <!-- <div class="col-sm-4 ">
                <div class="input-group">
                    <span class="input-group-addon">Periode</span>
                    <input type="text" name="q2" id="q2" class="form-control input-sm datepicker" autocomplete="off" />
                </div>
            </div>
            <div class="col-sm-4">
                <div class="input-group">
                    <span class="input-group-addon">S/D</span>
                    <input type="text" name="q3" id="q3" class="form-control input-sm datepicker" autocomplete="off" />
                </div>
            </div> -->
            <div class="col-sm-8">
                <div class="row">
                    <div class="col-sm-1">
                        <button class="btn btn-sm btn-secondary" id="prevBtn">« Prev</button>
                    </div>
                    <div class="col-sm-3">

                        <select id="selectBulan" name="selectBulan" class="form-control ">
                            <option value="">All</option>
                            <option value="1">Januari</option>
                            <option value="2">Februari</option>
                            <option value="3">Maret</option>
                            <option value="4">April</option>
                            <option value="5">Mei</option>
                            <option value="6">Juni</option>
                            <option value="7">Juli</option>
                            <option value="8">Agustus</option>
                            <option value="9">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>

                    </div>
                    <div class="col-sm-3">
                        <select id="selectTahun" name="selectBulan" class="form-control ">
                            <!-- Tahun bisa kamu isi dinamis kalau mau -->
                            <option value="">All</option>
                            <option value="2021">2021</option>
                            <option value="2022">2022</option>
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <button class="btn btn-sm btn-secondary" id="nextBtn">Next »</button>
                        <span id="currentFilter" style="margin-left: 10px; font-weight: bold;"></span>
                    </div>


                </div>

            </div>
        </div>


        <div class="row branch-filter">
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class=" info-box-icon bg-green"><i class="fas fa-wallet"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">TOTAL MARGIN</span>
                        <span class="info-box-number total-revenue">Rp 0</span>
                        <sub>(Harga dasar jual - Refund - Other Cost) - Harga beli</sub>
                    </div>

                </div>
                <!-- /.info-box -->
            </div>

            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class=" info-box-icon bg-yellow"><i class="fas fa-money-bill-wave"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">TOTAL REFUND</span>
                        <span class="info-box-number total-refund">Rp 0</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>

            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class=" info-box-icon bg-red"><i class="fas fa-hand-holding-usd"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">TOTAL LOSSES</span>
                        <span class="info-box-number total-losses">Rp. 0</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            Sales Per Branch Month</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>

                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="chart">
                                    <canvas id="myChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="hover-info">
                        <i class="fa fa-clock"></i>
                        <?php
                        $currentTime = date("H:i:s");
                        $currentHour = date("H:i");
                        echo "<i>Data per jam " . $currentHour . " </i>";
                        ?>
                    </div>
                </div>
            </div>
            <!-- <div class="col-md-6">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            Sales Per Branch Week</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>

                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="chart">
                                    <canvas id="myChart1"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="hover-info">
                        <i class="fa fa-clock"></i>
                        <?php
                        $currentTime = date("H:i:s");
                        $currentHour = date("H:i");
                        echo "<i>Data per jam " . $currentHour . " </i>";
                        ?>
                    </div>
                </div>
            </div> -->
            <div class="col-md-3">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            PO Periode 1 - 14 <span class="getFilter" style="font-weight: bold;"></span>
                        </h3>
                    </div>
                    <div class="box-body" style="height:190px">
                        <div class="table-responsive">
                            <table width="100%" class="">
                                <tr>
                                    <th width="35%" height="35px" style="font-size:small;">Penawaran</th>
                                    <th>:&nbsp;</th>
                                    <td class="penawaran_awal" style="font-size:small;">0</td>
                                </tr>
                                <tr>
                                    <th width="35%" height="35px" style="font-size:small;">PO Customer</th>
                                    <th>:&nbsp;</th>
                                    <td class="po_awal" style="font-size:small;">0</td>
                                </tr>
                                <tr>
                                    <th width="35%" height="35px" style="font-size:small;">Delivered</th>
                                    <th>:&nbsp;</th>
                                    <td class="delivered_awal" style="font-size:small;">0</td>
                                </tr>
                                <!-- <tr>
                                <th width="35%" height="35px" style="font-size:small;">Pending Delivered</th>
                                <th>:&nbsp;</th>
                                <td class="pd_awal" style="font-size:small;">0</td>
                            </tr>
                            <tr>
                                <th width="35%" height="25px" style="font-size:small;">Pending Plan</th>
                                <th>:&nbsp;</th>
                                <td class="pp_awal" style="font-size:small;">0</td>
                            </tr> -->
                                <tr>
                                    <th width="35%" height="25px" style="font-size:small;">Close</th>
                                    <th>:&nbsp;</th>
                                    <td class="close_awal" style="font-size:small;">0</td>
                                </tr>
                            </table>
                        </div>
                        <!-- <div class="row">
                            <div class="col-sm-4">
                              <h5>Penawaran</h5>
                            </div>
                            <div class="col-sm-1">
                              <p><b>:</b></p>
                            </div>
                            <div class="col-sm-6">
                              <p class="penawaran_awal">0</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <h5>PO</h5>
                            </div>
                            <div class="col-sm-1">
                              <h5><b>:</b></h5>
                            </div>
                            <div class="col-sm-6">
                              <p class="po_awal text-justify" style="margin:0;">0</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                              <h5>Close</h5>
                            </div>
                            <div class="col-sm-1">
                              <p class="text-center"><b>:</b></p>
                            </div>
                            <div class="col-sm-6">
                              <p class="close_awal">0</p>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            PO Periode 15 - 31 <span class="getFilter" style="font-weight: bold;"></span>
                        </h3>
                    </div>
                    <div class="box-body" style="height:190px">
                        <div class="table-responsive">
                            <table width="100%" class="">
                                <tr>
                                    <th width="35%" height="35px" style="font-size:small;">Penawaran</th>
                                    <th>:&nbsp;</th>
                                    <td class="penawaran_akhir" style="font-size:small;">0</td>
                                </tr>
                                <tr>
                                    <th width="35%" height="35px" style="font-size:small;">PO Customer</th>
                                    <th>:&nbsp;</th>
                                    <td class="po_akhir" style="font-size:small;">0</td>
                                </tr>
                                <tr>
                                    <th width="35%" height="35px" style="font-size:small;">Delivered</th>
                                    <th>:&nbsp;</th>
                                    <td class="delivered_akhir" style="font-size:small;">0</td>
                                </tr>
                                <!-- <tr>
                                        <th width="35%" height="35px" style="font-size:small;">Pending Delivered</th>
                                        <th>:&nbsp;</th>
                                        <td class="pd_akhir" style="font-size:small;">0</td>
                                    </tr>
                                    <tr>
                                        <th width="35%" height="35px" style="font-size:small;">Pending Plan</th>
                                        <th>:&nbsp;</th>
                                        <td class="pp_akhir" style="font-size:small;">0</td>
                                    </tr> -->
                                <tr>
                                    <th width="35%" height="35px" style="font-size:small;">Close</th>
                                    <th>:&nbsp;</th>
                                    <td class="close_akhir" style="font-size:small;">0</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">

            <!-- total AR -->
            <!-- <div class="col-md-6">
                <div class="box" >
                    <div class="box-header" style="background:darkslateblue">
                        <h3 class="box-title text-center" style="color:white">
                         TOTAL AR
                        </h3>
                    </div>
                   <div class="box-body" style="height: 187px;">
                        <h2 class="total-ar text-center">0</h2>
                    </div>
                  </div>
            </div> -->

            <div class="col-md-6">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            Customer per Branch Month</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>

                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="chart">
                                    <canvas id="myChart8"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="hover-info">
                        <i class="fa fa-clock"></i>
                        <?php
                        $currentTime = date("H:i:s");
                        $currentHour = date("H:i");
                        echo "<i>Data per jam " . $currentHour . " </i>";
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Top 5 AR Overdue Customer</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>

                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="chart">
                                    <canvas id="myChart7"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="hover-info">
                        <i class="fa fa-clock"></i>
                        <?php
                        $currentTime = date("H:i:s");
                        $currentHour = date("H:i");
                        echo "<i>Data per jam " . $currentHour . " </i>";
                        ?>
                    </div>
                </div>
            </div>
        </div>




        <div class="row">
            <div class="col-md-4">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            Top 5 Customers</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>

                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="chart">
                                    <canvas id="myChart2"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="hover-info">
                        <i class="fa fa-clock"></i>
                        <?php
                        $currentTime = date("H:i:s");
                        $currentHour = date("H:i");
                        echo "<i>Data per jam " . $currentHour . " </i>";
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            Top 5 Suppliers</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>

                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="chart">
                                    <canvas id="myChart3"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="hover-info">
                        <i class="fa fa-clock"></i>
                        <?php
                        $currentTime = date("H:i:s");
                        $currentHour = date("H:i");
                        echo "<i>Data per jam " . $currentHour . " </i>";
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            Top 5 Sales</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>

                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="chart">
                                    <canvas id="myChart4"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="hover-info">
                        <i class="fa fa-clock"></i>
                        <?php
                        $currentTime = date("H:i:s");
                        $currentHour = date("H:i");
                        echo "<i>Data per jam " . $currentHour . " </i>";
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            Unit Nasional per Month</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>

                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="chart" style="height: 300px; position: relative;">
                                    <canvas id="myChart6"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="hover-info">
                        <i class="fa fa-clock"></i>
                        <?php
                        $currentTime = date("H:i:s");
                        $currentHour = date("H:i");
                        echo "<i>Data per jam " . $currentHour . " </i>";
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="box">
                    <div class="container-fluid">


                        <div class="box-header with-border">
                            <h3 class="box-title">
                                Unit Branch per Month
                            </h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>

                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>

                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="chart">
                                        <canvas id="myChart5" style="height: 300px; position: relative;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="hover-info">
                            <i class="fa fa-clock"></i>
                            <?php
                            $currentTime = date("H:i:s");
                            $currentHour = date("H:i");
                            echo "<i>Data per jam " . $currentHour . " </i>";
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            Inventory Stock All Terminal</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>

                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div style="overflow-x: auto; overflow-y: hidden; white-space: nowrap; max-height: 400px;">
                                    <?php foreach ($charts as $chartData) : ?>
                                        <div style="display: inline-block; vertical-align: top; margin-right: 20px;">
                                            <div id="<?php echo $chartData['containerId']; ?>" style="width: 400px; height: 370px;"></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="hover-info">
                        <i class="fa fa-clock"></i>
                        <?php
                        $currentTime = date("H:i:s");
                        $currentHour = date("H:i");
                        echo "<i>Data per jam " . $currentHour . " </i>";
                        ?>
                    </div>
                </div>
            </div>
        </div>



    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
<script src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>


<script>
    const today = new Date();
    const formattedDate = today.getDate() + '/' + (today.getMonth() + 1) + '/' + today.getFullYear();
    document.getElementById('current-date').textContent = formattedDate;

    let q2 = '';
    let q3 = '';
    let q4 = '';
    $("#q4").on("change", function() {
        q4 = $(this).val();
        loadTotalData();
        loadTotalDataPeriode();
        loadChartData();
        loadChartData3();
        loadChartData4();
        loadChartData5();
        loadChartData6();
        loadChartData7();
        loadChartData8();
        loadChartData9();
    });

    // $("#q3").on("change", function() {
    //      q3 = $(this).val();
    //      q2 = $('#q2').val();

    //      if(q2== ''){
    //         // $('#q2').focus();
    //         // $('#q2').datepicker("show");
    //         return;
    //      }
    //      loadTotalData();
    //      loadChartData3();
    //      loadChartData4();
    //      loadChartData5();
    // });

    // $("#q2").on("change", function() {
    //      q2 = $(this).val();
    //      q3 = $('#q3').val();

    //      if(q3== ''){
    //         // $('#q3').focus();
    //         // $('#q3').datepicker("show");
    //         return;
    //      }
    //      loadTotalData();
    //      loadChartData3();
    //      loadChartData4();
    //      loadChartData5();
    // });
    let currentMonth = today.getMonth() + 1;
    let currentYear = today.getFullYear();

    function getNamaBulan(bulan) {
        const namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        return namaBulan[bulan - 1];
    }

    function updateUIAndData() {
        $('#selectBulan').val(currentMonth);
        $('#selectTahun').val(currentYear);

        $('#currentFilter').text(`${getNamaBulan(currentMonth)} ${currentYear}`);
        $('.getFilter').text(`${getNamaBulan(currentMonth)} ${currentYear}`);
        // loadChartData();
    }

    $('#prevBtn').click(function() {
        currentMonth--;
        if (currentMonth < 1) {
            currentMonth = 12;
            currentYear--;
        }
        loadTotalData();
        loadTotalDataPeriode();
        loadChartData();
        loadChartData3();
        loadChartData4();
        loadChartData5();
        loadChartData6();
        loadChartData7();
        loadChartData9()
        updateUIAndData();
    });

    $('#nextBtn').click(function() {
        currentMonth++;
        if (currentMonth > 12) {
            currentMonth = 1;
            currentYear++;
        }
        loadTotalData();
        loadTotalDataPeriode();
        loadChartData();
        loadChartData3();
        loadChartData4();
        loadChartData5();
        loadChartData6();
        loadChartData7();
        loadChartData9();
        updateUIAndData();
    });

    $('#selectBulan, #selectTahun').change(function() {
        currentMonth = ($('#selectBulan').val() == '') ? '' : parseInt($('#selectBulan').val());
        currentYear = parseInt($('#selectTahun').val());
        updateUIAndData();
        loadTotalData();
        loadTotalDataPeriode();
        loadChartData();
        loadChartData3();
        loadChartData4();
        loadChartData5();
        loadChartData6();
        loadChartData7();
        loadChartData9();
    });

    function loadChartData() {
        $.ajax({
            url: "./datatable/dashboard_ceo_chart.php", // URL to the PHP file
            method: 'GET',
            data: {
                // q2: q2,
                // q3: q3,
                selectBulan: currentMonth,
                selectTahun: currentYear,
                q4: q4
            },
            success: function(response) {
                // Log the response to ensure the data is correct
                // console.log(response);

                // Parse JSON data
                const parsedData = JSON.parse(response);

                // Ubah tanggal menjadi nama hari

                if (Chart.getChart("myChart")) {
                    Chart.getChart("myChart")?.destroy()
                }
                // Ambil volume sebagai data
                const data = parsedData.map(item => item.volume);
                const cabang = parsedData.map(item => item.cabang);
                const labels = parsedData.map(item => item.bulan);

                // Balik urutan data untuk menampilkan yang lama di kiri, yang baru di kanan


                // Update the chart with new data
                updateChart(labels, data, cabang);
            },
            error: function(error) {
                console.error("Failed to retrieve data:", error);
            }
        });
    }

    // Function to update the chart with new data
    function updateChart(labels, data, cabang) {
        const ctx = document.getElementById('myChart').getContext('2d');

        // Menentukan warna untuk setiap cabang secara dinamis
        const colors = cabang.map(() => getRandomColor()); // Membuat array warna acak

        // Membuat datasets berdasarkan bulan
        const datasets = labels.map((label, index) => {
            return {
                label: label, // Menampilkan bulan di legend
                data: data[index], // Data untuk bulan tersebut
                backgroundColor: colors[index], // Set warna bar berdasarkan cabang
                borderColor: colors[index].replace('0.5', '1'), // Border dengan warna yang lebih gelap
                borderWidth: 1
            };
        });
        // Gabungkan data berdasarkan bulan
        const finalDatasets = [];
        labels.forEach((label, i) => {
            let dataset = finalDatasets.find(item => item.label === label);
            if (!dataset) {
                dataset = {
                    label: label, // Label bulan
                    data: Array(cabang.length).fill(0), // Inisialisasi data array kosong
                    backgroundColor: getRandomColor(), // Warna acak untuk setiap bulan
                    borderColor: getRandomColor().replace('0.5', '1'), // Border dengan warna yang lebih gelap
                    borderWidth: 1
                };
                finalDatasets.push(dataset);
            }
            // Isi data untuk bulan tersebut
            dataset.data[i] = data[i];
        });

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: cabang, // Data untuk X axis (menampilkan cabang) 
                datasets: finalDatasets // Menampilkan dataset yang berisi data untuk setiap bulan
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    datalabels: {
                        display: true, // Tampilkan label
                        align: 'end',
                        anchor: 'end',
                        color: '#000',
                        formatter: function(value) {
                            return parseFloat(value).toLocaleString('en-US'); // Format angka
                        },
                        font: {
                            size: 12,
                            weight: 'bold'
                        }
                    },
                    legend: {
                        display: true, // Menampilkan legend
                        position: 'top', // Letakkan legend di atas grafik
                        labels: {
                            boxWidth: 20, // Lebar kotak legend
                            padding: 15, // Padding antar legend
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    },
                    x: {
                        ticks: {
                            display: true // Menampilkan cabang di sumbu X
                        }
                    }
                }
            },
            plugins: [ChartDataLabels] // Tambahkan plugin
        });
    }

    // Fungsi untuk menghasilkan warna acak
    function getRandomColor() {
        const letters = '0123456789ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }

    $(document).ready(function() {
        loadChartData();
        updateUIAndData();
    });

    function loadChartData1() {
        $.ajax({
            url: "./datatable/dashboard_ceo_chart1.php", // URL to the PHP file
            method: 'GET',
            success: function(response) {
                // Log the response to ensure the data is correct
                // console.log(response);

                // Parse JSON data
                const parsedData = JSON.parse(response);

                // Ubah tanggal menjadi nama hari


                // Ambil volume sebagai data
                const data = parsedData.map(item => item.volume);
                const cabang = parsedData.map(item => item.cabang);
                const labels = parsedData.map(item => item.minggu);

                // Balik urutan data untuk menampilkan yang lama di kiri, yang baru di kanan


                // Update the chart with new data
                updateChart1(labels, data, cabang);
            },
            error: function(error) {
                console.error("Failed to retrieve data:", error);
            }
        });
    }

    // Function to update the chart with new data
    function updateChart1(labels, data, cabang) {
        const ctx = document.getElementById('myChart1').getContext('2d');

        // Menentukan warna untuk setiap cabang secara dinamis
        const colors = cabang.map(() => getRandomColor()); // Membuat array warna acak

        // Membuat datasets berdasarkan bulan
        const datasets = labels.map((label, index) => {
            return {
                label: label, // Menampilkan bulan di legend
                data: data[index], // Data untuk bulan tersebut
                backgroundColor: colors[index], // Set warna bar berdasarkan cabang
                borderColor: colors[index].replace('0.5', '1'), // Border dengan warna yang lebih gelap
                borderWidth: 1
            };
        });

        // Gabungkan data berdasarkan bulan
        const finalDatasets = [];
        labels.forEach((label, i) => {
            let dataset = finalDatasets.find(item => item.label === label);
            if (!dataset) {
                dataset = {
                    label: label, // Label bulan
                    data: Array(cabang.length).fill(0), // Inisialisasi data array kosong
                    backgroundColor: getRandomColor(), // Warna acak untuk setiap bulan
                    borderColor: getRandomColor().replace('0.5', '1'), // Border dengan warna yang lebih gelap
                    borderWidth: 1
                };
                finalDatasets.push(dataset);
            }
            // Isi data untuk bulan tersebut
            dataset.data[i] = data[i];
        });

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: cabang, // Data untuk X axis (menampilkan cabang) 
                datasets: finalDatasets // Menampilkan dataset yang berisi data untuk setiap bulan
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    datalabels: {
                        display: true, // Tampilkan label
                        align: 'end',
                        anchor: 'end',
                        color: '#000',
                        formatter: function(value) {
                            return parseFloat(value).toLocaleString('en-US'); // Format angka
                        },
                        font: {
                            size: 12,
                            weight: 'bold'
                        }
                    },
                    legend: {
                        display: true, // Menampilkan legend
                        position: 'top', // Letakkan legend di atas grafik
                        labels: {
                            boxWidth: 20, // Lebar kotak legend
                            padding: 15, // Padding antar legend
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    },
                    x: {
                        ticks: {
                            display: true // Menampilkan cabang di sumbu X
                        }
                    }
                }
            },
            plugins: [ChartDataLabels] // Tambahkan plugin
        });
    }

    // Fungsi untuk menghasilkan warna acak
    function getRandomColor() {
        const letters = '0123456789ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }

    $(document).ready(function() {
        loadChartData1();
    });



    //chart 3

    function loadChartData3() {
        $.ajax({
            url: "./datatable/dashboard_ceo_chart_top5.php", // URL to the PHP file
            method: 'GET',
            data: {
                // q2: q2,
                // q3: q3,
                selectBulan: currentMonth,
                selectTahun: currentYear,
                q4: q4
            },
            success: function(response) {
                // Log the response to ensure the data is correct
                // console.log(response);

                // Parse JSON data
                const parsedData = JSON.parse(response);

                // Ubah tanggal menjadi nama hari


                // Ambil volume sebagai data
                const customer_det = parsedData.map(item => item.customer_det);
                const customer = parsedData.map(item => item.customer);
                const data = parsedData.map(item => item.volume);
                const cabang = parsedData.map(item => item.cabang);
                const labels = parsedData.map(item => item.bulan);

                // Balik urutan data untuk menampilkan yang lama di kiri, yang baru di kanan


                if (Chart.getChart("myChart2")) {
                    Chart.getChart("myChart2")?.destroy()
                }
                // Update the chart with new data
                updateChart3(labels, data, cabang, customer, customer_det);
            },
            error: function(error) {
                console.error("Failed to retrieve data:", error);
            }
        });
    }

    // Function to update the chart with new data
    function updateChart3(labels, data, cabang, customer, customer_det) {
        const ctx = document.getElementById('myChart2').getContext('2d');

        // Menentukan warna untuk setiap cabang secara dinamis
        const colors = customer.map(() => getRandomColor()); // Membuat array warna acak

        // Membuat datasets berdasarkan bulan
        const datasets = labels.map((label, index) => {
            return {
                label: label, // Menampilkan bulan di legend
                data: data[index], // Data untuk bulan tersebut
                backgroundColor: colors[index], // Set warna bar berdasarkan cabang
                borderColor: colors[index].replace('0.5', '1'), // Border dengan warna yang lebih gelap
                borderWidth: 1
            };
        });

        // Gabungkan data berdasarkan bulan
        const finalDatasets = [];
        labels.forEach((label, i) => {
            let dataset = finalDatasets.find(item => item.label === label);
            if (!dataset) {
                dataset = {
                    label: label, // Label bulan
                    data: Array(customer.length).fill(0), // Inisialisasi data array kosong
                    backgroundColor: getRandomColor(), // Warna acak untuk setiap bulan
                    borderColor: getRandomColor().replace('0.5', '1'), // Border dengan warna yang lebih gelap
                    borderWidth: 1
                };
                finalDatasets.push(dataset);
            }
            // Isi data untuk bulan tersebut
            dataset.data[i] = data[i];
        });

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: customer, // Data untuk X axis (menampilkan cabang) 
                datasets: finalDatasets // Menampilkan dataset yang berisi data untuk setiap bulan
            },
            options: {
                indexAxis: 'y', // Membuat chart menjadi horizontal
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const i = context.dataIndex;
                                return `${customer_det[i]}: ${parseFloat(data[i]).toLocaleString('en-US')}`;
                            }
                        }
                    },
                    datalabels: {
                        display: true, // Tampilkan label
                        align: 'end',
                        anchor: 'end',
                        color: '#000',
                        formatter: function(value) {
                            return parseFloat(value).toLocaleString('en-US'); // Format angka
                        },
                        font: {
                            size: 10,
                            weight: 'bold'
                        }
                    },
                    legend: {
                        display: true, // Menampilkan legend
                        position: 'top', // Letakkan legend di atas grafik
                        labels: {
                            boxWidth: 20, // Lebar kotak legend
                            padding: 15, // Padding antar legend
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: {
                                size: 10 // Menampilkan cabang di sumbu X
                            }
                        }
                    },
                    x: {
                        ticks: {
                            display: true,
                            font: {
                                size: 10 // Atur ukuran font untuk sumbu X jika dibutuhkan
                            } // Menampilkan cabang di sumbu X
                        }
                    }
                }
            },
            plugins: [ChartDataLabels] // Tambahkan plugin
        });
    }

    // Fungsi untuk menghasilkan warna acak
    function getRandomColor() {
        const letters = '0123456789ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }

    $(document).ready(function() {
        loadChartData3();
    });



    //chart 4

    function loadChartData4() {
        $.ajax({
            url: "./datatable/dashboard_ceo_chart_top5_supplier.php", // URL to the PHP file
            method: 'GET',
            data: {
                // q2: q2,
                // q3: q3,
                selectBulan: currentMonth,
                selectTahun: currentYear,
                q4: q4
            },
            success: function(response) {
                // Log the response to ensure the data is correct
                console.log("chart3" + response);

                // Parse JSON data
                const parsedData = JSON.parse(response);

                // Ubah tanggal menjadi nama hari


                // Ambil volume sebagai data
                const vendor_det = parsedData.map(item => item.vendor_det);
                const vendor = parsedData.map(item => item.vendor);
                const data = parsedData.map(item => item.volume);
                const labels = parsedData.map(item => item.bulan);

                // Balik urutan data untuk menampilkan yang lama di kiri, yang baru di kanan

                if (Chart.getChart("myChart3")) {
                    Chart.getChart("myChart3")?.destroy()
                }

                // Update the chart with new data
                updateChart4(labels, data, vendor, vendor_det);
            },
            error: function(error) {
                console.error("Failed to retrieve data:", error);
            }
        });
    }

    // Function to update the chart with new data
    function updateChart4(labels, data, vendor, vendor_det) {
        const ctx = document.getElementById('myChart3').getContext('2d');

        // Menentukan warna untuk setiap cabang secara dinamis
        const colors = vendor.map(() => getRandomColor()); // Membuat array warna acak

        // Membuat datasets berdasarkan bulan
        const datasets = labels.map((label, index) => {
            return {
                label: label, // Menampilkan bulan di legend
                data: data[index], // Data untuk bulan tersebut
                backgroundColor: colors[index], // Set warna bar berdasarkan cabang
                borderColor: colors[index].replace('0.5', '1'), // Border dengan warna yang lebih gelap
                borderWidth: 1
            };
        });

        // Gabungkan data berdasarkan bulan
        const finalDatasets = [];
        labels.forEach((label, i) => {
            let dataset = finalDatasets.find(item => item.label === label);
            if (!dataset) {
                dataset = {
                    label: label, // Label bulan
                    data: Array(vendor.length).fill(0), // Inisialisasi data array kosong
                    backgroundColor: getRandomColor(), // Warna acak untuk setiap bulan
                    borderColor: getRandomColor().replace('0.5', '1'), // Border dengan warna yang lebih gelap
                    borderWidth: 1
                };
                finalDatasets.push(dataset);
            }
            // Isi data untuk bulan tersebut
            dataset.data[i] = data[i];
        });

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: vendor, // Data untuk X axis (menampilkan cabang) 
                datasets: finalDatasets // Menampilkan dataset yang berisi data untuk setiap bulan
            },
            options: {
                indexAxis: 'y', // Membuat chart menjadi horizontal
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const i = context.dataIndex;
                                return `${vendor_det[i]}: ${parseFloat(data[i]).toLocaleString('en-US')}`;
                            }
                        }
                    },
                    datalabels: {
                        display: true, // Tampilkan label
                        align: 'end',
                        anchor: 'end',
                        color: '#000',
                        formatter: function(value) {
                            return parseFloat(value).toLocaleString('en-US'); // Format angka
                        },
                        font: {
                            size: 10,
                            weight: 'bold'
                        }
                    },
                    legend: {
                        display: true, // Menampilkan legend
                        position: 'top', // Letakkan legend di atas grafik
                        labels: {
                            boxWidth: 20, // Lebar kotak legend
                            padding: 15, // Padding antar legend
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: {
                                size: 8 // Menampilkan cabang di sumbu X
                            }
                        }
                    },
                    x: {
                        ticks: {
                            display: true,
                            font: {
                                size: 8 // Atur ukuran font untuk sumbu X jika dibutuhkan
                            } // Menampilkan cabang di sumbu X
                        }
                    }
                }
            },
            plugins: [ChartDataLabels] // Tambahkan plugin
        });
    }

    // Fungsi untuk menghasilkan warna acak
    function getRandomColor() {
        const letters = '0123456789ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }

    $(document).ready(function() {
        loadChartData4();
    });

    //chart 5
    //chart 4

    function loadChartData5() {
        $.ajax({
            url: "./datatable/dashboard_ceo_chart_top5_sales.php", // URL to the PHP file
            method: 'GET',
            data: {
                // q2: q2,
                // q3: q3,
                selectBulan: currentMonth,
                selectTahun: currentYear,
                q4: q4
            },
            success: function(response) {
                // Log the response to ensure the data is correct
                // console.log(response);

                // Parse JSON data
                const parsedData = JSON.parse(response);

                // Ubah tanggal menjadi nama hari


                // Ambil volume sebagai data
                const sales = parsedData.map(item => item.sales);
                const data = parsedData.map(item => item.volume);
                const labels = parsedData.map(item => item.bulan);

                // Balik urutan data untuk menampilkan yang lama di kiri, yang baru di kanan
                if (Chart.getChart("myChart4")) {
                    Chart.getChart("myChart4")?.destroy()
                }

                // Update the chart with new data
                updateChart5(labels, data, sales);
            },
            error: function(error) {
                console.error("Failed to retrieve data:", error);
            }
        });
    }
    // Function to update the chart with new data
    function updateChart5(labels, data, sales) {
        const ctx = document.getElementById('myChart4').getContext('2d');

        // Menentukan warna untuk setiap cabang secara dinamis
        const colors = sales.map(() => getRandomColor()); // Membuat array warna acak

        // Membuat datasets berdasarkan bulan
        const datasets = labels.map((label, index) => {
            return {
                label: label, // Menampilkan bulan di legend
                data: data[index], // Data untuk bulan tersebut
                backgroundColor: colors[index], // Set warna bar berdasarkan cabang
                borderColor: colors[index].replace('0.5', '1'), // Border dengan warna yang lebih gelap
                borderWidth: 1
            };
        });

        // Gabungkan data berdasarkan bulan
        const finalDatasets = [];
        labels.forEach((label, i) => {
            let dataset = finalDatasets.find(item => item.label === label);
            if (!dataset) {
                dataset = {
                    label: label, // Label bulan
                    data: Array(sales.length).fill(0), // Inisialisasi data array kosong
                    backgroundColor: getRandomColor(), // Warna acak untuk setiap bulan
                    borderColor: getRandomColor().replace('0.5', '1'), // Border dengan warna yang lebih gelap
                    borderWidth: 1
                };
                finalDatasets.push(dataset);
            }
            // Isi data untuk bulan tersebut
            dataset.data[i] = data[i];
        });

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: sales, // Data untuk X axis (menampilkan cabang) 
                datasets: finalDatasets // Menampilkan dataset yang berisi data untuk setiap bulan
            },
            options: {
                indexAxis: 'y', // Membuat chart menjadi horizontal
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    datalabels: {
                        display: true, // Tampilkan label
                        align: 'end',
                        anchor: 'end',
                        color: '#000',
                        formatter: function(value) {
                            return parseFloat(value).toLocaleString('en-US'); // Format angka
                        },
                        font: {
                            size: 10,
                            weight: 'bold'
                        }
                    },
                    legend: {
                        display: true, // Menampilkan legend
                        position: 'top', // Letakkan legend di atas grafik
                        labels: {
                            boxWidth: 20, // Lebar kotak legend
                            padding: 15, // Padding antar legend
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: {
                                size: 8 // Menampilkan cabang di sumbu X
                            }
                        }
                    },
                    x: {
                        ticks: {
                            display: true,
                            font: {
                                size: 8 // Atur ukuran font untuk sumbu X jika dibutuhkan
                            } // Menampilkan cabang di sumbu X
                        }
                    }
                }
            },
            plugins: [ChartDataLabels] // Tambahkan plugin
        });
    }

    // Fungsi untuk menghasilkan warna acak
    function getRandomColor() {
        const letters = '0123456789ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }

    $(document).ready(function() {
        loadChartData5();
    });

    function loadTotalData() {
        $.ajax({
            url: "./datatable/dashboard_ceo_total.php", // URL to the PHP file
            method: 'GET',
            data: {
                // q2: q2,
                // q3: q3,
                selectBulan: currentMonth,
                selectTahun: currentYear,
                q4: q4
            },
            success: function(response) {
                // Log the response to ensure the data is correct
                // console.log(q4);
                console.log(response);

                // Parse JSON data
                const parsedData = JSON.parse(response);

                $('.total-refund').text('Rp ' + formatCogs(parsedData[0].total_refund))
                $('.total-losses').text('Rp ' + formatCogs(parsedData[0].total_losses))
                $('.total-revenue').text('Rp ' + formatCogs(parsedData[0].total_revenue))
                $('.total-ar').text('Rp ' + formatCogs(parsedData[0].total_ar))
            },
            error: function(error) {
                console.error("Failed to retrieve data:", error);
            }
        });
    }

    function loadTotalDataPeriode() {
        $.ajax({
            url: "./datatable/dashboard_ceo_total_periode.php", // URL to the PHP file
            method: 'GET',
            data: {
                // q2: q2,
                // q3: q3,
                selectBulan: currentMonth,
                selectTahun: currentYear,
                q4: q4
            },
            success: function(response) {
                // Log the response to ensure the data is correct
                console.log(response);

                // Parse JSON data
                const parsedData = JSON.parse(response);
                var periode_awal = parsedData[0].periode_awal;
                var periode_akhir = parsedData[0].periode_akhir;

                $('.penawaran_awal').text(periode_awal.po_penawaran + ' (' + formatCogs(periode_awal.vol_penawaran) + ' L)')
                $('.po_awal').text(periode_awal.po_cust + ' (' + formatCogs(periode_awal.vol_cust) + ' L)')
                $('.close_awal').text(formatCogs(periode_awal.po_close) + ' L')
                $('.delivered_awal').text(periode_awal.po_delivered + ' (' + formatCogs(periode_awal.vol_po_delivered) + ' L)')
                // $('.pd_awal').text( periode_awal.pend_delivered +' ('+ formatCogs(periode_awal.vol_pend_delivered)+ ' L)')
                // $('.pp_awal').text(formatCogs(periode_awal.vol_pend_plan)+ ' L')

                $('.penawaran_akhir').text(periode_akhir.po_penawaran + ' (' + formatCogs(periode_akhir.vol_penawaran) + ' L)')
                $('.po_akhir').text(periode_akhir.po_cust + ' (' + formatCogs(periode_akhir.vol_cust) + ' L)')
                $('.close_akhir').text(formatCogs(periode_akhir.po_close) + ' L')
                $('.delivered_akhir').text(periode_akhir.po_delivered + ' (' + formatCogs(periode_akhir.vol_po_delivered) + ' L)')
                // $('.pd_akhir').text( periode_akhir.pend_delivered +' ('+ formatCogs(periode_akhir.vol_pend_delivered)+ ' L)')
                // $('.pp_akhir').text(formatCogs(periode_akhir.vol_pend_plan)+ ' L')
            },
            error: function(error) {
                console.error("Failed to retrieve data:", error);
            }
        });
    }

    $(document).ready(function() {
        loadTotalDataPeriode();
    });

    $(document).ready(function() {
        loadTotalData();
    });

    function loadChartData6() {
        $.ajax({
            url: "./datatable/dashboard_ceo_chart_2.php", // URL to the PHP file
            method: 'GET',
            data: {
                // q2: q2,
                // q3: q3,
                selectBulan: currentMonth,
                selectTahun: currentYear,
                q4: q4
            },
            success: function(response) {
                // Log the response to ensure the data is correct
                // console.log(response);

                // Parse JSON data
                const parsedData = JSON.parse(response);

                // Ubah tanggal menjadi nama hari

                if (Chart.getChart("myChart5")) {
                    Chart.getChart("myChart5")?.destroy()
                }
                // Ambil volume sebagai data
                // const data = parsedData.map(item => item.datasets);
                // const labels = parsedData.map(item => item.labels);
                const dataObject = parsedData[0];

                const labels = dataObject.labels;
                const datasets = dataObject.datasets.map(ds => {
                    let color = '#000'; // Default warna
                    if (ds.label.includes('Pro Energi')) {
                        color = 'rgba(255, 99, 132, 0.5)'; // Merah
                    } else if (ds.label.includes('Third Party')) {
                        color = 'rgba(54, 162, 235, 0.5)'; // Biru
                    }

                    return {
                        label: ds.label,
                        data: ds.data,
                        backgroundColor: color,
                        borderColor: color.replace('0.5', '1'),
                        borderWidth: 1
                    };
                });
                // Update the chart with new data
                updateChart6(labels, datasets);
            },
            error: function(error) {
                console.error("Failed to retrieve data:", error);
            }
        });
    }

    // Function to update the chart with new data
    function updateChart6(labels, datasets) {
        const ctx = document.getElementById('myChart5').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels, // Sumbu X (Jakarta, Bandung, dst.)
                datasets: datasets // Multi dataset: Pro Energi, Third Party, dll
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    layout: {
                        padding: {
                            top: 50,
                            bottom: 20
                        }
                    }
                },
                plugins: {
                    datalabels: {
                        display: true,
                        align: 'end',
                        anchor: 'end',
                        color: '#000',
                        formatter: value => parseInt(value).toLocaleString('en-US'),
                        font: {
                            size: 12,
                            weight: 'bold'
                        }
                    },
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            boxWidth: 20,
                            padding: 15
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: Math.ceil(Math.max(...datasets.flatMap(ds => ds.data)) * 1.1),
                        ticks: {
                            stepSize: 1, // ⬅️ Paksa interval angka bulat
                            callback: function(value) {
                                return value;
                            }
                        }
                    },
                    x: {
                        ticks: {
                            display: true
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    }

    $(document).ready(function() {
        loadChartData6();
    });

    function loadChartData7() {
        $.ajax({
            url: "./datatable/dashboard_ceo_chart_3.php", // URL to the PHP file
            method: 'GET',
            data: {
                // q2: q2,
                // q3: q3,
                selectBulan: currentMonth,
                selectTahun: currentYear,
                q4: q4
            },
            success: function(response) {
                // Log the response to ensure the data is correct
                // console.log(response);
                const parsedData = JSON.parse(response);

                const dataObject = parsedData[0];

                if (Chart.getChart("myChart6")) {
                    Chart.getChart("myChart6")?.destroy()
                }
                const labels = dataObject.labels;
                const datasets = [{
                        label: "Pro Energi",
                        data: dataObject.proEnergi,
                        volume: dataObject.pro_energi_volume,
                        backgroundColor: "rgba(255, 99, 132, 0.5)",
                        borderColor: "rgba(255, 99, 132, 1)",
                        borderWidth: 1
                    },
                    {
                        label: "Third Party",
                        data: dataObject.thirdParty,
                        volume: dataObject.thirdparty_volume,
                        backgroundColor: "rgba(54, 162, 235, 0.5)",
                        borderColor: "rgba(54, 162, 235, 1)",
                        borderWidth: 1
                    }
                ];
                // // Update the chart with new data
                updateChart7(labels, datasets);
            },
            error: function(error) {
                console.error("Failed to retrieve data:", error);
            }
        });
    }

    // Function to update the chart with new data
    function updateChart7(labels, datasets) {
        const ctx = document.getElementById('myChart6').getContext('2d');
        // Cari nilai maksimum dari semua data di datasets
        const maxData = Math.max(...datasets.flatMap(ds => ds.data));
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels, // Sumbu X (Jakarta, Bandung, dst.)
                datasets: datasets // Multi dataset: Pro Energi, Third Party, dll
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    layout: {
                        padding: {
                            top: 50,
                            bottom: 20
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.dataset.label || '';
                                const value = context.raw;
                                const volume = context.dataset.volume?.[context.dataIndex] || 0;

                                return `${label}: PO ${value} | volume(${volume.toLocaleString()})`;
                            }
                        }
                    },
                    datalabels: {
                        display: true,
                        align: 'end',
                        anchor: 'end',
                        color: '#000',
                        formatter: value => parseInt(value).toLocaleString('en-US'),
                        font: {
                            size: 12,
                            weight: 'bold'
                        }
                    },
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            boxWidth: 20,
                            padding: 15
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: Math.ceil(maxData * 1.2), // Tambah rongga 20%
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return Number.isInteger(value) ? value : null;
                            }
                        }
                    },
                    x: {
                        ticks: {
                            display: true
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    }

    $(document).ready(function() {
        loadChartData7();
    });

    function loadChartData8() {
        $.ajax({
            url: "./datatable/dashboard_ceo_chart_top5_ar.php", // URL to the PHP file
            method: 'GET',
            data: {
                // q2: q2,
                // q3: q3,
                selectBulan: currentMonth,
                selectTahun: currentYear,
                q4: q4
            },
            success: function(response) {
                // Log the response to ensure the data is correct
                // console.log(response);

                // Parse JSON data
                const parsedData = JSON.parse(response);

                // Ubah tanggal menjadi nama hari


                // Ambil volume sebagai data
                const customer_det = parsedData.map(item => item.customer_det);
                const customer = parsedData.map(item => item.customer);
                const data = parsedData.map(item => item.ar);
                const cabang = parsedData.map(item => item.cabang);
                const labels = parsedData.map(item => item.bulan);

                // Balik urutan data untuk menampilkan yang lama di kiri, yang baru di kanan


                if (Chart.getChart("myChart7")) {
                    Chart.getChart("myChart7")?.destroy()
                }
                // Update the chart with new data
                updateChart8(data, cabang, customer, customer_det);
            },
            error: function(error) {
                console.error("Failed to retrieve data:", error);
            }
        });
    }

    // Function to update the chart with new data
    function updateChart8(data, cabang, customer, customer_det) {
        const ctx = document.getElementById('myChart7').getContext('2d');

        // Menentukan warna untuk setiap cabang secara dinamis
        const colors = customer.map(() => getRandomColor()); // Membuat array warna acak

        // // Membuat datasets berdasarkan bulan
        // const datasets = cabang.map((label, index) => {
        //     return {
        //         label: label, // Menampilkan bulan di legend
        //         data: data[index], // Data untuk bulan tersebut
        //         backgroundColor: colors[index], // Set warna bar berdasarkan cabang
        //         borderColor: colors[index].replace('0.5', '1'), // Border dengan warna yang lebih gelap
        //         borderWidth: 1
        //     };
        // });

        // Gabungkan data berdasarkan bulan
        const finalDatasets = [];
        cabang.forEach((label, i) => {
            let dataset = finalDatasets.find(item => item.label === label);
            if (!dataset) {
                dataset = {
                    label: label, // Label bulan
                    data: Array(customer.length).fill(0), // Inisialisasi data array kosong
                    backgroundColor: getRandomColor(), // Warna acak untuk setiap bulan
                    borderColor: getRandomColor().replace('0.5', '1'), // Border dengan warna yang lebih gelap
                    borderWidth: 1
                };
                finalDatasets.push(dataset);
            }
            // Isi data untuk bulan tersebut
            dataset.data[i] = data[i];
        });

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: customer, // Data untuk Y axis (menampilkan cabang) 
                datasets: finalDatasets // Menampilkan dataset yang berisi data untuk setiap bulan
            },
            options: {
                indexAxis: 'y', // Membuat chart menjadi horizontal
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const i = context.dataIndex;
                                return `${customer_det[i]}: ${parseFloat(data[i]).toLocaleString('en-US')}`;
                            }
                        }
                    },
                    datalabels: {
                        display: true, // Tampilkan label
                        align: 'end',
                        anchor: 'end',
                        color: '#000',
                        formatter: function(value) {
                            return parseFloat(value).toLocaleString('en-US'); // Format angka
                        },
                        font: {
                            size: 10,
                            weight: 'bold'
                        }
                    },
                    legend: {
                        display: true, // Menampilkan legend
                        position: 'top', // Letakkan legend di atas grafik
                        labels: {
                            boxWidth: 20, // Lebar kotak legend
                            padding: 15, // Padding antar legend
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: {
                                size: 10 // Menampilkan cabang di sumbu X
                            }
                        }
                    },
                    x: {
                        ticks: {
                            display: true,
                            font: {
                                size: 10 // Atur ukuran font untuk sumbu X jika dibutuhkan
                            } // Menampilkan cabang di sumbu X
                        }
                    }
                }
            },
            plugins: [ChartDataLabels] // Tambahkan plugin
        });
    }

    // Fungsi untuk menghasilkan warna acak
    function getRandomColor() {
        const letters = '0123456789ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }

    $(document).ready(function() {
        loadChartData8();
    });


    function loadChartData9() {
        $.ajax({
            url: "./datatable/dashboard_ceo_total_customer.php", // URL to the PHP file
            method: 'GET',
            data: {
                // q2: q2,
                // q3: q3,
                selectBulan: currentMonth,
                selectTahun: currentYear,
                q4: q4
            },
            success: function(response) {
                // Log the response to ensure the data is correct
                const parsedData = JSON.parse(response);

                const dataObject = parsedData[0];
                // console.log(dataObject);

                if (Chart.getChart("myChart8")) {
                    Chart.getChart("myChart8")?.destroy()
                }

                const labels = dataObject.labels;
                const datasets = dataObject.datasets.map(ds => {
                    let color = '#000'; // Default warna
                    if (ds.label.includes('Active Customer')) {
                        color = 'rgba(99, 195, 255, 0.5)'; // Merah
                    } else if (ds.label.includes('New Customer')) {
                        color = 'rgba(235, 163, 54, 0.5)'; // Biru
                    }

                    return {
                        label: ds.label,
                        data: ds.data,
                        backgroundColor: color,
                        borderColor: color.replace('0.5', '1'),
                        borderWidth: 1
                    };
                });
                // // Update the chart with new data
                updateChart9(labels, datasets);
            },
            error: function(error) {
                console.error("Failed to retrieve data:", error);
            }
        });
    }

    // Function to update the chart with new data
    function updateChart9(labels, datasets) {
        const ctx = document.getElementById('myChart8').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels, // Sumbu X (Jakarta, Bandung, dst.)
                datasets: datasets // Multi dataset: Pro Energi, Third Party, dll
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    layout: {
                        padding: {
                            top: 50,
                            bottom: 20
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.dataset.label || '';
                                const value = context.raw;
                                const volume = context.dataset.volume?.[context.dataIndex] || 0;

                                return `${label}: PO ${value} | volume(${volume.toLocaleString()})`;
                            }
                        }
                    },
                    datalabels: {
                        display: true,
                        align: 'end',
                        anchor: 'end',
                        color: '#000',
                        formatter: value => parseInt(value).toLocaleString('en-US'),
                        font: {
                            size: 12,
                            weight: 'bold'
                        }
                    },
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            boxWidth: 20,
                            padding: 15
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        // suggestedMax: adjustedMax,
                        max: Math.ceil(Math.max(...datasets.flatMap(ds => ds.data)) * 1.2),
                        ticks: {
                            stepSize: 1, // ⬅️ Paksa interval angka bulat
                            callback: function(value) {
                                return value;
                            }
                        }
                    },
                    x: {
                        ticks: {
                            display: true
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    }

    $(document).ready(function() {
        loadChartData9();
    });
</script>

<script>
    FusionCharts.ready(function() {
        // Mendapatkan semua data chart dari PHP
        var allCharts = <?php echo json_encode($charts); ?>;

        // Iterasi melalui setiap data chart
        allCharts.forEach(function(chartData) {
            // Mendefinisikan objek grafik FusionCharts untuk setiap data chart
            var chartObj = new FusionCharts({
                type: 'cylinder',
                dataFormat: 'json',
                renderAt: chartData.containerId,
                width: '100%',
                height: '400',
                dataSource: {
                    "chart": {
                        "theme": "fusion",
                        "caption": chartData.title + chartData.tankiTerminal,
                        "subcaption": 'COGS :' + formatCogs(chartData.cogs) + '\n' + '\n',
                        "xAxisName": "Terminal",
                        "yAxisName": "Volume Minyak (ltr)",
                        "upperlimitdisplay": formatCogs(chartData.batasAtas) + " ltr",
                        "upperlimit": chartData.batasAtas,
                        "numberSuffix": " ltr",
                        "showLabels": "1",
                        "showValues": "1",
                        "paletteColors": getColor(chartData.oilLevel, chartData.batasAtas, chartData.batasBawah),
                        "bgColor": "#ffffff",
                        "showBorder": "0",
                        "showCanvasBorder": "0",
                        "plotBorderAlpha": "10",
                        "usePlotGradientColor": "0",
                        "plotFillAlpha": "50",
                        "showPlotBorder": "0",
                        "toolTipColor": "#ffffff",
                        "toolTipBorderThickness": "0",
                        "toolTipBgColor": "#000000",
                        "toolTipBgAlpha": "80",
                        "toolTipBorderRadius": "2",
                        "toolTipPadding": "10",
                        "cylFillColor": getColor(chartData.oilLevel, chartData.batasAtas, chartData.batasBawah),
                        "cylradius": "100",
                        "cylheight": "230",
                        "animation": "1",
                        "yAxisMaxValue": chartData.batasAtas,
                        "yAxisMinValue": 0,
                        "showTickMarks": "1",
                        "showTickValues": "1",
                    },
                    "value": chartData.oilLevel
                }
            });

            // Render grafik untuk setiap data chart
            chartObj.render();

            // Hide the watermark
            chartObj.addEventListener('renderComplete', function() {
                document.querySelectorAll('.fusioncharts-container text[text-anchor="middle"]').forEach(function(element) {
                    element.setAttribute('y', parseInt(element.getAttribute('y')) + 0.5);
                });
            });
        });
    });

    function formatCogs(number) {
        return Math.round(number).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function getColor(oilLevel, batasAtas, batasBawah) {
        //console.log("oilLevel: " + oilLevel + ", batasAtas: " + batasAtas + ", batasBawah: " + batasBawah);
        if (oilLevel < batasBawah) {
            return "#FF0000"; // Merah untuk di bawah batas bawah
        } else if (oilLevel >= batasAtas) {
            return "#FFD700"; // Kuning untuk di atas batas atas
        } else {
            return "#FFD700"; // Hijau untuk di antara batas bawah dan atas
        }
    }
</script>
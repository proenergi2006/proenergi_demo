<?php
// session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth    = new MyOtentikasi();
$enk     = decode($_SERVER['REQUEST_URI']);
$con     = new Connection();
$flash   = new FlashAlerts;
$arrBln = array(1 => "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

$q1 = (isset($enk['q1']) && $enk['q1'] ? htmlspecialchars($enk['q1'], ENT_QUOTES) : NULL);
$q2 = (isset($enk['q2']) && $enk['q2'] ? htmlspecialchars($enk['q2'], ENT_QUOTES) : date('m'));
$q3 = (isset($enk['q3']) && $enk['q3'] ? htmlspecialchars($enk['q3'], ENT_QUOTES) : date('Y'));
$q4 = (isset($enk['q4']) && $enk['q4'] ? htmlspecialchars($enk['q4'], ENT_QUOTES) : NULL);
$q5 = (isset($enk['display']) && $enk['display'] ? 1 : 0);

include_once($public_base_directory . "/web/__get_inventory_stock.php");

?>
<!-- <script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script> -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/solid-gauge.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>


<style>
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
</style>




<h3>PO VS Realisasi</h3>
<div class="row">
    <div class="col-sm-12">
        <form method="post">
            <div class="box box-info">
                <div class="box-header with-border">
                    <div class="row">
                        <div class="col-sm-6">
                            <div style="font-size:12px;"><b>Cabang</b></div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-sm-4">

                            <select name="q4" id="q4" class="form-control select2">
                                <option></option>
                                <?php $con->fill_select("id_master", "nama_cabang", "pro_master_cabang", '', "where is_active=1 and id_master <> 1", "nama_cabang", false); ?>

                            </select>
                            <p></p>
                            <?php
                            // Menangkap nilai tanggal yang telah dipilih setelah pencarian
                            $selectedBranchId  = isset($_POST['q4']) ? $_POST['q4'] : '';


                            if (!empty($selectedBranchId)) {
                                // Mengambil data cabang dari database
                                $query = "Select nama_cabang FROM pro_master_cabang WHERE id_master = $selectedBranchId";
                                $result = $con->getRecord($query);
                                $selectedBranchName = $result['nama_cabang'];
                            }
                            $startDate = isset($_POST['q5']) ? $_POST['q5'] : '';
                            $endDate = isset($_POST['q6']) ? $_POST['q6'] : '';
                            ?>
                            <p style="font-size:12px;"><i>(* Silahkan Pilih Cabang Dan Periode Dahulu)</i></p>
                            <p style="font-size:12px;"><b>Cabang : <?php echo  $selectedBranchName; ?> </b></p>
                            <p style="font-size:12px;"><b>Periode : <?php echo $startDate; ?> - <?php echo $endDate; ?> </b></p>
                        </div>
                        <div class="col-sm-5">
                            <label>Periode</label>


                            <input type="text" name="q5" id="q5" class="datepicker input-cr-sm" autocomplete='off' /> <label>S/D</label>
                            <input type="text" name="q6" id="q6" class="datepicker input-cr-sm" autocomplete='off' />
                        </div>


                        <div class="col-sm-3">


                            <button type="submit" class="btn btn-success btn-sm"> Search</button>

                        </div>

                    </div>

                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="box box-info content-data-search">
            <div class="box-header with-border">
                <div class="row">
                    <div class="col-md-6">

                        <label style="font-size:large;">Marketing PO</label>



                        <div id="container"></div>

                    </div>
                    <div class="col-md-6">

                        <label style="font-size:large;">Customer PO</label>

                        <div id="container2"></div>

                    </div>
                </div>
                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <label style="font-size:large;">Realisasi Marketing</label>
                        <div id="container3"></div>

                    </div>
                    <div class="col-md-6">
                        <label style="font-size:large;">Realisasi Customer</label>
                        <div id="container4"></div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("1"))) { ?>
    <h3>Inventory Stock Depot Terminal</h3>

    <div class="box box-info">

        <div class="box-body">
            <form name="searchForm" id="searchForm" role="form" class="form-horizontal" method="POST">
                <div class="row">
                    <div class="col-md-8">
                        <!-- <div class="form-group form-group-sm">
                        <label class="control-label col-md-1"><strong>Cabang</strong></label>
                        <div class="col-md-5">
                            <select name="q4" id="q4" class="select2">
                                <option value="">Semua Cabang</option>
                                <?php $con->fill_select("id_master", "nama_cabang", "pro_master_cabang", $q1, "where is_active=1" . $agc, "", false); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-info btn-sm jarak-kanan" name="btnSearch" id="btnSearch" style="min-width:100px;">
                                <i class="fa fa-search jarak-kanan"></i> Search
                            </button>
                        </div>

                        <div>
                        </div>
                    </div> -->
                    </div>
                </div>


            </form>
            <!-- <div class="row">
            <div class="col-md-5">

            </div>
            <div class="col-md-5">

            </div>
            <div class="col-md-2">
                <div class="box-header with-border">
                    <h4 class="box-title">Parameter</h4>
                </div>
                <div class="box-body">
                    <div id="external-events" class="text-center">
                        <div class="external-event bg-light-blue">80 - 100 %</div>
                        <div class="external-event bg-green">50 - 70 %</div>
                        <div class="external-event bg-red">
                            < 30 %</div>
                        </div>
                    </div>
                </div>
            </div> -->








            <div style="overflow-x: auto; overflow-y: hidden; white-space: nowrap; max-height: 400px;">
                <?php foreach ($charts as $chartData) : ?>
                    <div style="display: inline-block; vertical-align: top; margin-right: 20px;">
                        <div id="<?php echo $chartData['containerId']; ?>" style="width: 400px; height: 370px;"></div>
                    </div>
                <?php endforeach; ?>
            </div>



        </div>


    </div>
<?php } ?>






<!-- Dev Iwan AR Customer -->
<h3>AR Customer</h3>
<form name="searchForm" id="searchForm" role="form" class="form-horizontal">
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <div class="row">
                        <div class="col-sm-4">
                            <input type="text" class="form-control input-sm" name="q1" id="q1" placeholder="Keywords Customer Name, Marketing" />
                        </div>

                        <div class="col-sm-4">
                            <button type="submit" class="btn btn-sm btn-info" name="btnSearch" id="btnSearch"> <i class="fa fa-search jarak-kanan"></i> Search</button>
                            <a href="<?php echo BASE_URL_CLIENT . '/export-bod.php'; ?>" class="btn btn-info btn-sm">
                                <i class="fa fa-plus jarak-kanan"></i>More
                            </a>
                        </div>
                    </div>
                </div>
</form>
<div class="box-body table-responsive">
    <table class="table table-bordered" id="table-grid1">
        <thead>
            <tr>
                <th class="text-center" width="30">No</th>
                <th class="text-center" width="250">Customer</th>
                <th class="text-center" width="50">TOP</th>
                <th class="text-center" width="100">Credit Limit</th>
                <th class="text-center" width="100">Not Yet</th>
                <th class="text-center" width="200">Overdue</th>
                <th class="text-center" width="150">Reminding</th>
                <th class="text-center" width="150">Total AR</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>
</div>
</div>
</div>




<?php include_once($public_base_directory . "/web/__sc_inventory_stock.php"); ?>
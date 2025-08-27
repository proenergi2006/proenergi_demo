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



include_once($public_base_directory . "/web/__get_inventory_stock_pr_new.php");

?>
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>




<h3>Inventory Stock Depot Terminal</h3>

<div class="box box-info">

    <div class="box-body">
        <form name="searchForm" id="searchForm" role="form" class="form-horizontal" method="POST">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group form-group-sm">
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
                    </div>
                </div>
            </div>


        </form>
        <br>
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





        <br>


        <div style="overflow-x: scroll; white-space: nowrap;">
            <?php foreach ($charts as $chartData) : ?>
                <div style="display: inline-block; vertical-align: top; margin-right: 20px;">
                    <div id="<?php echo $chartData['containerId']; ?>" style="width: 400px; height: 370px;"></div>
                </div>
            <?php endforeach; ?>
        </div>



    </div>


</div>
</div>
<?php include_once($public_base_directory . "/web/__sc_inventory_stock_pr_new.php"); ?>
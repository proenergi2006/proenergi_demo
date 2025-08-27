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
$arrBln = array(1 => "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
$linkEx1 = BASE_URL_CLIENT . '/report/schedule-by-date-kapal-cetak.php';
$sesRole = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$sesGrup = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);
$sesCbng = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
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
                <h1>Schedule By Date Kapal</h1>
            </section>
            <section class="content">

                <?php $flash->display(); ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div style="font-size:16px;"><b>SEARCH</b></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="text-right">
                                            <a href="<?php echo $linkEx1; ?>" class="btn btn-success btn-sm" target="_blank" id="expData1">Export Data</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-body">
                                <form name="sFrm" id="sFrm" method="post">
                                    <div class="table-responsive">
                                        <table border="0" cellpadding="0" cellspacing="0" class="table no-border col-sm-top table-pencarian" style="margin-bottom:0px;">
                                            <tr>



                                                <td width="130">Date</td>
                                                <td width="15%">
                                                    <input type="text" name="q1" id="q1" class="datepicker input-cr-sm" value="<?php echo $q1; ?>" autocomplete="off" />

                                                </td>
                                                <td width="130">Periode</td>
                                                <td width="30%">
                                                    <input type="text" name="q2" id="q2" class="datepicker input-cr-sm" value="<?php echo $q2; ?>" autocomplete="off" /> s/d
                                                    <input type="text" name="q3" id="q3" class="datepicker input-cr-sm" value="<?php echo $q3; ?>" autocomplete="off" />
                                                </td>
                                                <td width="130">Depot</td>
                                                <td width="30%">
                                                    <select id="q4" name="q4" class="form-control select2">
                                                        <option></option>
                                                        <?php $con->fill_select("id_master", "concat(nama_terminal,' ',tanki_terminal)", "pro_master_terminal", $q1, "where is_active=1", "id_master", false); ?>
                                                    </select>

                                                </td>

                                                <td width="20"></td>
                                                <td width="30%">
                                                    <input type="text" class="form-control input-sm" name="q5" id="q5" placeholder="Keywords..." />
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan="4">
                                                    <button type="submit" class="btn btn-info btn-sm" name="btnSc" id="btnSc"><i class="fa fa-search jarak-kanan"></i>Search</button>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="text-right" style="margin-top: 10px">Show
                                            <select name="tableGridLength" id="tableGridLength">
                                                <option value="10" selected>10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                                <option value="all">All</option>
                                            </select> Data
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered col-sm-top table-isi" id="table-grid">
                                        <thead>
                                            <tr>
                                                <th class="text-center" width="2%">No.</th>

                                                <th class="text-center" width="7%">Tgl Loading</th>
                                                <th class="text-center" width="15%">Kode DR</th>
                                                <th class="text-center" width="12%">Loading Order</th>
                                                <th class="text-center" width="17%">Depot</th>
                                                <th class="text-center" width="10%">Transportir</th>
                                                <th class="text-center" width="8%">No Pol</th>
                                                <th class="text-center" width="8%">Captain </th>
                                                <th class="text-center" width="6%">Qty</th>


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


                <?php $con->close(); ?>
            </section>
            <?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
        </aside>
    </div>

    <style type="text/css">
        .table-isi>thead>tr>th,
        .table-isi>tbody>tr>td {
            font-size: 11px;
            font-family: arial;
        }

        .table-pencarian>tbody>tr>td {
            padding: 5px;
            font-size: 11px;
            font-family: arial;
            vertical-align: top;
        }

        select.input-cr,
        input.input-cr-sm,
        input.input-cr-lg,
        input.input-cr {
            padding: 3px 5px;
            border: 1px solid #ccc;
            font-family: arial;
            font-size: 11px;
            height: 26px;
            line-height: 26px;
        }

        input.input-cr-sm {
            width: 100px;
        }

        input.input-cr-lg {
            width: 300px;
        }

        .btn-sm,
        .btn-group-sm>.btn {
            font-size: 11px;
        }

        .select2-container .select2-selection--single {
            height: 26px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px;
        }

        .select2-container--default .select2-selection--single .select2-selection__clear {
            height: 26px;
        }

        .select2-search--dropdown .select2-search__field {
            font-family: arial;
            font-size: 11px;
            padding: 4px 3px;
        }

        .select2-results__option {
            font-family: arial;
            font-size: 11px;
        }
    </style>
    <script>
        $(document).ready(function() {
            $(".hitung").number(true, 0, ".", ",");
            $("#table-grid").ajaxGrid({
                url: "./l-schedule-by-date-data-kapal.php",
                data: {
                    q1: $("#q1").val(),
                    q2: $("#q2").val(),
                    q3: $("#q3").val(),
                    q4: $("#q4").val(),
                    q5: $("#q5").val(),

                },
            });
            $('#btnSc').on('click', function() {
                $("#table-grid").ajaxGrid("draw", {
                    data: {
                        q1: $("#q1").val(),
                        q2: $("#q2").val(),
                        q3: $("#q3").val(),
                        q4: $("#q4").val(),
                        q5: $("#q5").val(),

                    }
                });
                return false;
            });
            $('#tableGridLength').on('change', function() {
                $("#table-grid").ajaxGrid("pageLen", $(this).val());
            });
            $('#expData1').on('click', function() {
                $(this).prop("href", $("#uriExp").val());
            });

        });
    </script>
</body>

</html>
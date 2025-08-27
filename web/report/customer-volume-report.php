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
$linkEx = BASE_URL_CLIENT . '/report/m-volume-report-exp.php';
$sesUser = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);
$sesRole = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$sesGrup = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);
$sesCbng = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

include_once($public_base_directory . "/web/__get_volume_customer.php");

$customer = "SELECT * FROM pro_customer WHERE status_customer=2 AND is_verified=1";
$res_cust = $con->getResult($customer);

$cabang = "SELECT id_master, nama_cabang, inisial_cabang FROM pro_master_cabang WHERE is_active=1 ORDER BY id_master ASC";
$res_cabang = $con->getResult($cabang);

// Cek peran pengguna
$required_role = ['1', '2', '16'];
// Misalnya halaman ini hanya untuk superadmin
if (!in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), $required_role)) {
    // Pengguna tidak memiliki peran yang tepat, redirect ke halaman lain atau tampilkan pesan akses ditolak
    $flash->add("warning", "Akses ditolak.", BASE_URL_CLIENT . "/home.php");
    // exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("myGrid", "jqueryUI"), "css" => array("jqueryUI"))); ?>

<style>
    th.sticky,
    td.sticky {
        position: -webkit-sticky;
        /* For Safari */
        position: sticky;
        left: 0;
        background-color: #f4f4f4;
        z-index: 2;
        /* Ensures the sticky column is on top of other columns */
    }

    th {
        background-color: #f4f4f4;
    }

    thead th {
        position: sticky;
        top: 0;
        background-color: #ddd;
        z-index: 1;
        /* Ensure the header row is on top of other content */
    }
</style>

<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1>Customer Volume Report</h1>
            </section>
            <section class="content">
                <?php $flash->display(); ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div style="font-size:16px;"><b>PENCARIAN</b></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="text-right">
                                            <a href="<?php echo $linkEx; ?>" class="btn btn-success btn-sm" target="_blank" id="expData">Export Data</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-body">
                                <form name="sFrm" id="sFrm" method="post">
                                    <div class="container-fluid">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table no-border col-sm-top table-pencarian" style="margin-bottom:0px;">
                                            <tr>
                                                <td>Customer</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <select name="q2[]" id="q2" class="form-control select2" multiple>
                                                        <?php foreach ($res_cust as $key) : ?>
                                                            <option value="<?= $key['id_customer'] ?>"><?= $key['nama_customer'] ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table no-border col-sm-top table-pencarian" style="margin-bottom:0px;">
                                            <tr>
                                                <td>Tahun</td>
                                                <td>Cabang</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <select name="q1" id="q1" class="form-control">
                                                        <?php for ($i = 2019; $i <= date("Y"); $i++) : ?>
                                                            <option <?= $i == date("Y") ? 'selected' : '' ?> value="<?= $i ?>"><?= $i ?></option>
                                                        <?php endfor ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="q3" id="q3" class="form-control select2">
                                                        <option></option>
                                                        <?php foreach ($res_cabang as $key) : ?>
                                                            <option cabang="<?= $key['nama_cabang'] ?>" value="<?= $key['id_master'] ?>"><?= $key['nama_cabang'] . " - " . $key['inisial_cabang']  ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </td>
                                                <td align="center">
                                                    <button type="submit" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch"><i class="fa fa-search jarak-kanan"></i>Search</button>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#data" aria-controls="data" role="tab" data-toggle="tab">Data</a></li>
                    <li role="presentation" class=""><a href="#diagram" aria-controls="diagram" role="tab" data-toggle="tab">Diagram</a></li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="data">
                        <div class="row">
                            <div class="col-sm-12">
                                <center>
                                    <h4 id="tahun">TAHUN <?= date("Y"); ?></h4>
                                </center>
                            </div>
                            <div class="col-sm-12">
                                <span id="nama_cabang">Cabang : Samarinda</span>
                            </div>
                            <div class="col-sm-12">
                                <span>Satuan volume : Liter</span>
                            </div>
                            <br>
                            <div class="col-sm-12">
                                <div class="box box-info">
                                    <div class="box-header with-border">
                                        <div class="row">
                                            <div class="col-sm-6">
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="text-right" style="margin-top: 10px">Show
                                                    <select name="tableGridLength" id="tableGridLength">
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
                                        <div style="width:3500px; height:auto;">
                                            <table class="table table-bordered table-hover" id="data-customer-volume-report-table">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" style="background: white;" rowspan="2">No</th>
                                                        <th class="text-center sticky" style="background: white;" rowspan="2">Nama Customer</th>
                                                        <th class="text-center" style="background: white;" colspan="4">Januari</th>
                                                        <th class="text-center" style="background: white;" colspan="4">Februari</th>
                                                        <th class="text-center" style="background: white;" colspan="4">Maret</th>
                                                        <th class="text-center" style="background: white;" colspan="4">April</th>
                                                        <th class="text-center" style="background: white;" colspan="4">Mei</th>
                                                        <th class="text-center" style="background: white;" colspan="4">Juni</th>
                                                        <th class="text-center" style="background: white;" colspan="4">Juli</th>
                                                        <th class="text-center" style="background: white;" colspan="4">Agustus</th>
                                                        <th class="text-center" style="background: white;" colspan="4">September</th>
                                                        <th class="text-center" style="background: white;" colspan="4">Oktober</th>
                                                        <th class="text-center" style="background: white;" colspan="4">November</th>
                                                        <th class="text-center" style="background: white;" colspan="4">Desember</th>
                                                    </tr>
                                                    <tr>
                                                        <!-- Januari -->
                                                        <th class="text-center" style="background: white;">Vol SJ</th>
                                                        <th class="text-center" style="background: white;">Vol Realisasi</th>
                                                        <th class="text-center" style="background: white;">Loss</th>
                                                        <th class="text-center" style="background: white;">%</th>
                                                        <!-- Februari -->
                                                        <th class="text-center" style="background: white;">Vol SJ</th>
                                                        <th class="text-center" style="background: white;">Vol Realisasi</th>
                                                        <th class="text-center" style="background: white;">Loss</th>
                                                        <th class="text-center" style="background: white;">%</th>
                                                        <!-- Maret -->
                                                        <th class="text-center" style="background: white;">Vol SJ</th>
                                                        <th class="text-center" style="background: white;">Vol Realisasi</th>
                                                        <th class="text-center" style="background: white;">Loss</th>
                                                        <th class="text-center" style="background: white;">%</th>
                                                        <!-- April -->
                                                        <th class="text-center" style="background: white;">Vol SJ</th>
                                                        <th class="text-center" style="background: white;">Vol Realisasi</th>
                                                        <th class="text-center" style="background: white;">Loss</th>
                                                        <th class="text-center" style="background: white;">%</th>
                                                        <!-- Mei -->
                                                        <th class="text-center" style="background: white;">Vol SJ</th>
                                                        <th class="text-center" style="background: white;">Vol Realisasi</th>
                                                        <th class="text-center" style="background: white;">Loss</th>
                                                        <th class="text-center" style="background: white;">%</th>
                                                        <!-- Juni -->
                                                        <th class="text-center" style="background: white;">Vol SJ</th>
                                                        <th class="text-center" style="background: white;">Vol Realisasi</th>
                                                        <th class="text-center" style="background: white;">Loss</th>
                                                        <th class="text-center" style="background: white;">%</th>
                                                        <!-- Juli -->
                                                        <th class="text-center" style="background: white;">Vol SJ</th>
                                                        <th class="text-center" style="background: white;">Vol Realisasi</th>
                                                        <th class="text-center" style="background: white;">Loss</th>
                                                        <th class="text-center" style="background: white;">%</th>
                                                        <!-- Agustus -->
                                                        <th class="text-center" style="background: white;">Vol SJ</th>
                                                        <th class="text-center" style="background: white;">Vol Realisasi</th>
                                                        <th class="text-center" style="background: white;">Loss</th>
                                                        <th class="text-center" style="background: white;">%</th>
                                                        <!-- September -->
                                                        <th class="text-center" style="background: white;">Vol SJ</th>
                                                        <th class="text-center" style="background: white;">Vol Realisasi</th>
                                                        <th class="text-center" style="background: white;">Loss</th>
                                                        <th class="text-center" style="background: white;">%</th>
                                                        <!-- Oktober -->
                                                        <th class="text-center" style="background: white;">Vol SJ</th>
                                                        <th class="text-center" style="background: white;">Vol Realisasi</th>
                                                        <th class="text-center" style="background: white;">Loss</th>
                                                        <th class="text-center" style="background: white;">%</th>
                                                        <!-- November -->
                                                        <th class="text-center" style="background: white;">Vol SJ</th>
                                                        <th class="text-center" style="background: white;">Vol Realisasi</th>
                                                        <th class="text-center" style="background: white;">Loss</th>
                                                        <th class="text-center" style="background: white;">%</th>
                                                        <!-- Desember -->
                                                        <th class="text-center" style="background: white;">Vol SJ</th>
                                                        <th class="text-center" style="background: white;">Vol Realisasi</th>
                                                        <th class="text-center" style="background: white;">Loss</th>
                                                        <th class="text-center" style="background: white;">%</th>
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

                    <div class="modal fade" id="modalDetail" role="dialog" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-blue">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="title-detail">Detail</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="box-body table-responsive">
                                        <table class="table table-bordered table-hover" id="data-marketing-volume-detail">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        No
                                                    </th>
                                                    <th nowrap>
                                                        Nama Customer
                                                    </th>
                                                    <!-- <th nowrap>
                                                        Cabang Penagih
                                                    </th> -->
                                                    <th nowrap>
                                                        Angkutan
                                                    </th>
                                                    <th nowrap>
                                                        Tanggal PO
                                                    </th>
                                                    <th nowrap>
                                                        Tanggal Kirim
                                                    </th>
                                                    <th nowrap class="text-center">
                                                        PO Customer
                                                    </th>
                                                    <th class="text-center">
                                                        Realisasi Volume
                                                    </th>
                                                </tr>
                                            </thead>

                                            <tbody id="bodyResult">

                                            </tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="diagram">
                        <div class="row table-responsive">
                            <div class="col-sm-12">
                                <div id="diagram-volume">

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

    <style>
        #data-marketing-volume-report-table td,
        #data-marketing-volume-report-table th {
            font-size: 12px;
        }

        @import "https://code.highcharts.com/css/highcharts.css";
    </style>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/solid-gauge.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script>
        $(document).ready(function() {
            $("#q2").select2({
                placeholder: "Pilih Customer"
            })

            var volume = <?php echo $volumeJSON; ?>;
            // Create the chart
            Highcharts.chart('diagram-volume', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Total Volume Customer Tahun ' + `<?= date("Y") ?>`
                },
                // subtitle: {
                //     text: 'Resize the frame to see the legend position change'
                // },
                legend: {
                    align: 'center',
                    verticalAlign: 'bottom',
                    layout: 'vertical'
                },
                xAxis: {
                    categories: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
                },
                yAxis: {
                    title: {
                        text: 'Amount'
                    }
                },
                series: [{
                    name: 'Volume SJ',
                    data: [
                        volume.sj_januari,
                        volume.sj_februari,
                        volume.sj_maret,
                        volume.sj_april,
                        volume.sj_mei,
                        volume.sj_juni,
                        volume.sj_juli,
                        volume.sj_agustus,
                        volume.sj_september,
                        volume.sj_oktober,
                        volume.sj_november,
                        volume.sj_desember
                    ]
                }, {
                    name: 'Volume Realisasi',
                    data: [
                        volume.realisasi_januari,
                        volume.realisasi_februari,
                        volume.realisasi_maret,
                        volume.realisasi_april,
                        volume.realisasi_mei,
                        volume.realisasi_juni,
                        volume.realisasi_juli,
                        volume.realisasi_agustus,
                        volume.realisasi_september,
                        volume.realisasi_oktober,
                        volume.realisasi_november,
                        volume.realisasi_desember
                    ]
                }, {
                    name: 'Losses',
                    data: [
                        volume.losses_januari,
                        volume.losses_februari,
                        volume.losses_maret,
                        volume.losses_april,
                        volume.losses_mei,
                        volume.losses_juni,
                        volume.losses_juli,
                        volume.losses_agustus,
                        volume.losses_september,
                        volume.losses_oktober,
                        volume.losses_november,
                        volume.losses_desember
                    ]
                }],
                responsive: {
                    rules: [{
                        condition: {
                            maxWidth: 500
                        },
                        chartOptions: {
                            legend: {
                                align: 'center',
                                verticalAlign: 'bottom',
                                layout: 'horizontal'
                            }
                        }
                    }]
                }
            });

            $("#data-customer-volume-report-table").ajaxGrid({
                url: "../datatable/customer-volume-report.php",
                data: {
                    q1: $("#q1").val(),
                    q2: $("#q2").val(),
                    q3: $("#q3").val()
                },
            });

            // Flag to prevent infinite loop
            let isChanging = false;

            $('#q2').change(function() {
                if (!isChanging) {
                    isChanging = true;
                    $("#q3").val('').trigger('change'); // Clear value and trigger change event
                    isChanging = false;
                }
            });

            $('#q3').change(function() {
                if (!isChanging) {
                    isChanging = true;
                    $("#q2").val('').trigger('change'); // Clear value and trigger change event
                    isChanging = false;
                }
            });

            // $('#btnSearch').on('click', function() {
            //     var tahun = $("#q1").val();
            //     var cabang = $('option:selected', '#q3').attr('cabang');
            //     if (cabang == undefined) {
            //         var nama_cabang = "ALL";
            //     } else {
            //         var nama_cabang = cabang;
            //     }
            //     $("#tahun").html("Tahun " + tahun);
            //     $("#nama_cabang").html("Cabang : " + nama_cabang);
            //     $("#data-customer-volume-report-table").ajaxGrid("draw", {
            //         data: {
            //             q1: $("#q1").val(),
            //             q2: $("#q2").val(),
            //             q3: $("#q3").val()
            //         }
            //     });
            //     return false;
            // });
            $('#btnSearch').on('click', function() {
                var valq1 = $('#q1').val();
                var valq2 = $('#q2').val();
                var valq3 = $("#q3").val();
                var cabang = $('option:selected', '#q3').attr('cabang');
                if (cabang == undefined) {
                    var nama_cabang = "ALL";
                } else {
                    var nama_cabang = cabang;
                }
                $("#tahun").html("Tahun " + valq1);
                $("#nama_cabang").html("Cabang : " + nama_cabang);

                var param = {
                    q1: valq1,
                    q2: valq2,
                    q3: valq3
                };
                $.ajax({
                    type: "POST",
                    url: `<?= BASE_URL . "/web/__get_volume_customer.php" ?>`,
                    data: {
                        "q1": valq1,
                        "q2": valq2,
                        "q3": valq3
                    },
                    dataType: "json",
                    success: function(data) {
                        // console.log(data.realisasi_januari);
                        if (data && Object.keys(data).length > 0) {
                            Highcharts.charts.forEach(chart => {
                                if (chart) {
                                    const volumeSJData = [
                                        data.sj_januari || 0,
                                        data.sj_februari || 0,
                                        data.sj_maret || 0,
                                        data.sj_april || 0,
                                        data.sj_mei || 0,
                                        data.sj_juni || 0,
                                        data.sj_juli || 0,
                                        data.sj_agustus || 0,
                                        data.sj_september || 0,
                                        data.sj_oktober || 0,
                                        data.sj_november || 0,
                                        data.sj_desember || 0
                                    ];

                                    const volumeRealisasiData = [
                                        data.realisasi_januari || 0,
                                        data.realisasi_februari || 0,
                                        data.realisasi_maret || 0,
                                        data.realisasi_april || 0,
                                        data.realisasi_mei || 0,
                                        data.realisasi_juni || 0,
                                        data.realisasi_juli || 0,
                                        data.realisasi_agustus || 0,
                                        data.realisasi_september || 0,
                                        data.realisasi_oktober || 0,
                                        data.realisasi_november || 0,
                                        data.realisasi_desember || 0
                                    ];

                                    const lossesData = [
                                        data.losses_januari || 0,
                                        data.losses_februari || 0,
                                        data.losses_maret || 0,
                                        data.losses_april || 0,
                                        data.losses_mei || 0,
                                        data.losses_juni || 0,
                                        data.losses_juli || 0,
                                        data.losses_agustus || 0,
                                        data.losses_september || 0,
                                        data.losses_oktober || 0,
                                        data.losses_november || 0,
                                        data.losses_desember || 0
                                    ];

                                    // Update each series
                                    if (chart.series[0]) {
                                        chart.series[0].setData(volumeSJData, false); // 'false' to prevent re-rendering after each update
                                    }
                                    if (chart.series[1]) {
                                        chart.series[1].setData(volumeRealisasiData, false);
                                    }
                                    if (chart.series[2]) {
                                        chart.series[2].setData(lossesData, false);
                                    }

                                    // Update chart title
                                    chart.setTitle({
                                        text: 'Tahun ' + valq1,
                                        align: 'center'
                                    });

                                    // Redraw the chart after all series updates
                                    chart.redraw();
                                }
                            });
                        } else {
                            Highcharts.charts.forEach(chart => {
                                if (chart && chart.series && chart.series.length > 0) {
                                    chart.series[0].setData([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]);
                                    chart.setTitle({
                                        text: 'Tidak ada data',
                                        align: 'center'
                                    });
                                }
                            });
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX request failed:', textStatus, errorThrown);
                        Highcharts.charts.forEach(chart => {
                            if (chart && chart.series && chart.series.length > 0) {
                                chart.series[0].setData([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]);
                                chart.setTitle({
                                    text: 'Tidak ada data',
                                    align: 'center'
                                });
                            }
                        });
                    }
                });

                $("#data-customer-volume-report-table").ajaxGrid("draw", {
                    data: param
                });

                return false;
            });

            $('#tableGridLength').on('change', function() {
                $("#data-customer-volume-report-table").ajaxGrid("pageLen", $(this).val());
            });

            $('#expData').on('click', function() {
                $(this).prop("href", $("#uriExp").val());
            });
        });
    </script>
</body>

</html>
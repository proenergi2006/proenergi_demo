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
$linkEx = BASE_URL_CLIENT . '/report/m-active-customer-exp.php';
$sesUser = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);
$sesRole = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$sesGrup = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);
$sesCbng = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

include_once($public_base_directory . "/web/__get_active_customer.php");

// Cek peran pengguna
$required_role = ['1', '7', '21', '4', '3', '11', '6'];
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

<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1>Active Customer</h1>
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
                                <?php
                                if (in_array($sesRole, array("3", "4", "6", "7", "17", "20", "21"))) {
                                    if ($sesRole == '6') { //OM1 dan OM2
                                        $agc = " and id_master != 1 and id_group_cabang = '" . $sesGrup . "'";
                                        $agm = " and id_group = '" . $sesGrup . "'";
                                    } elseif ($sesRole == '3' || $sesRole == '4' || $sesRole == '21') { //CEO & CFO
                                        $agc = "";
                                        $agm = "";
                                    } elseif ($sesRole == '7') { //BM
                                        $agm = " and id_wilayah = '" . $sesCbng . "'";
                                    } else { //SPV
                                        $agm = " and id_wilayah = '" . $sesCbng . "'";
                                    }
                                }
                                ?>
                                <form name="sFrm" id="sFrm" method="post">
                                    <div class="table-responsive">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table no-border col-sm-top table-pencarian" style="margin-bottom:0px;">
                                            <tr>
                                                <td>Tahun</td>
                                                <td>Marketing</td>
                                                <?php if ($sesRole == '6' || $sesRole == '3' || $sesRole == '4' || $sesRole == '21') : ?>
                                                    <td>Cabang</td>
                                                <?php endif ?>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <select name="q1" id="q1" class="form-control">
                                                        <option></option>
                                                        <?php for ($i = 2019; $i <= date("Y"); $i++) : ?>
                                                            <option value="<?= $i ?>"><?= $i ?></option>
                                                        <?php endfor ?>
                                                    </select>
                                                </td>
                                                <td style="text-transform:uppercase">
                                                    <select name="q2" id="q2" class="form-control select2">
                                                        <option></option>
                                                        <?php $con->fill_select("id_user", "UPPER(fullname)", "acl_user", $q2, "where id_role='11'" . $agm, "fullname", false); ?>
                                                    </select>
                                                </td>
                                                <?php if ($sesRole == '6' || $sesRole == '3' || $sesRole == '4' || $sesRole == '21') : ?>
                                                    <td>
                                                        <select name="q3" id="q3" class="form-control select2">
                                                            <option></option>
                                                            <?php $con->fill_select("id_master", "nama_cabang", "pro_master_cabang", $q3, "where is_active=1" . $agc, "", false); ?>
                                                        </select>
                                                    </td>
                                                <?php endif ?>
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
                <br>
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
                                        <table class="table table-bordered table-hover" id="data-active-customer">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" width="3%" style="background: white;">No</th>
                                                    <th class="text-center" width="10%" style="background: white;">Marketing</th>
                                                    <th class="text-center" width="10%" style="background: white;">Januari</th>
                                                    <th class="text-center" width="10%" style="background: white;">Februari</th>
                                                    <th class="text-center" width="10%" style="background: white;">Maret</th>
                                                    <th class="text-center" width="10%" style="background: white;">April</th>
                                                    <th class="text-center" width="10%" style="background: white;">Mei</th>
                                                    <th class="text-center" width="10%" style="background: white;">Juni</th>
                                                    <th class="text-center" width="10%" style="background: white;">Juli</th>
                                                    <th class="text-center" width="10%" style="background: white;">Agustus</th>
                                                    <th class="text-center" width="10%" style="background: white;">September</th>
                                                    <th class="text-center" width="10%" style="background: white;">Oktober</th>
                                                    <th class="text-center" width="10%" style="background: white;">November</th>
                                                    <th class="text-center" width="10%" style="background: white;">Desember</th>
                                                    <th class="text-center" width="10%" style="background: white;">Total Customer</th>
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
                                                        <center>
                                                            NO
                                                        </center>
                                                    </th>
                                                    <th nowrap>
                                                        <center>
                                                            Nama Customer
                                                        </center>
                                                    </th>
                                                    <!-- <th nowrap>
                                                        <center>
                                                            Cabang Penagih
                                                        </center>
                                                    </th> -->
                                                    <th nowrap>
                                                        <center>
                                                            Provinsi
                                                        </center>
                                                    </th>
                                                    <th nowrap>
                                                        <center>
                                                            Volume Realisasi
                                                        </center>
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
                        <div class="row">
                            <div class="col-sm-12">
                                <div id="diagram-customer">

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
        #data-active-customer td,
        #data-active-customer th {
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
            var customer = <?php echo $customerJSON; ?>;
            // Create the chart
            Highcharts.chart('diagram-customer', {
                chart: {
                    type: 'column'
                },
                credits: {
                    enabled: false
                },
                title: {
                    align: 'center',
                    text: 'Tahun ' + `<?= date("Y") ?>`
                },
                accessibility: {
                    announceNewData: {
                        enabled: true
                    }
                },
                xAxis: {
                    type: 'category'
                },
                yAxis: {
                    title: {
                        text: 'Range Total Customer'
                    }
                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y:,.0f} Customer'
                        }
                    }
                },

                tooltip: {
                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> of total<br/>'
                },

                series: [{
                    name: 'Customers',
                    colorByPoint: true,
                    data: [{
                            name: 'Januari',
                            y: customer.januari,
                            drilldown: 'Januari'
                        },
                        {
                            name: 'Februari',
                            y: customer.februari,
                            drilldown: 'Februari'
                        },
                        {
                            name: 'Maret',
                            y: customer.maret,
                            drilldown: 'Maret'
                        },
                        {
                            name: 'April',
                            y: customer.april,
                            drilldown: 'April'
                        },
                        {
                            name: 'Mei',
                            y: customer.mei,
                            drilldown: 'Mei'
                        },
                        {
                            name: 'Juni',
                            y: customer.juni,
                            drilldown: 'Juni'
                        },
                        {
                            name: 'Juli',
                            y: customer.juli,
                            drilldown: 'Juli'
                        },
                        {
                            name: 'Agustus',
                            y: customer.agustus,
                            drilldown: 'Agustus'
                        },
                        {
                            name: 'September',
                            y: customer.september,
                            drilldown: 'September'
                        },
                        {
                            name: 'Oktober',
                            y: customer.oktober,
                            drilldown: 'Oktober'
                        },
                        {
                            name: 'November',
                            y: customer.november,
                            drilldown: 'November'
                        },
                        {
                            name: 'Desember',
                            y: customer.desember,
                            drilldown: 'Desember'
                        },
                    ]
                }],
                drilldown: {
                    breadcrumbs: {
                        position: {
                            align: 'right'
                        }
                    },
                }
            });

            $("#data-active-customer").ajaxGrid({
                url: "../datatable/marketing-active-customer.php",
                data: {
                    q1: $("#q1").val(),
                    q2: $("#q2").val(),
                    q3: $("#q3").val(),
                },
            });
            $('#btnSearch').on('click', function() {
                // var param = {
                //     q1: $("#q1").val()
                // };
                var valq1 = $('#q1').val();
                var valq2 = $('#q2').val();
                var valq3 = $('#q3').val();
                if (valq1 != "") {
                    $("#tahun").html("Tahun " + valq1);
                } else {
                    valq1 = `<?= date("Y") ?>`;
                }

                $("#data-active-customer").ajaxGrid("draw", {
                    data: {
                        q1: $("#q1").val(),
                        q2: $("#q2").val(),
                        q3: $("#q3").val()
                    }
                });

                $.ajax({
                    type: "POST",
                    url: `<?= BASE_URL . "/web/__get_active_customer.php" ?>`,
                    data: {
                        "q1": valq1,
                        "q2": valq2,
                        "q3": valq3
                    },
                    dataType: "json",
                    success: function(data) {
                        console.log(data);
                        if (data.length != 0) {
                            Highcharts.charts.forEach(chart => {
                                chart.series[0].setData([data.januari, data.februari, data.maret, data.april, data.mei, data.juni, data.juli, data.agustus, data.september, data.oktober, data.november, data.desember]);
                                if (valq2 != "") {
                                    chart.setTitle({
                                        text: data.nama_marketing + '<br>' + ' Tahun ' + valq1,
                                        align: 'center'
                                    });
                                } else {
                                    chart.setTitle({
                                        text: 'Tahun ' + valq1,
                                        align: 'center'
                                    });
                                }
                            })
                        } else {
                            Highcharts.charts.forEach(chart => {
                                chart.series[0].setData([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]);
                                chart.setTitle({
                                    text: 'Tidak ada data',
                                    align: 'center'
                                });
                            })

                        }
                    },
                });

                return false;
            });

            $('#tableGridLength').on('change', function() {
                $("#data-active-customer").ajaxGrid("pageLen", $(this).val());
            });

            $('#expData').on('click', function() {
                $(this).prop("href", $("#uriExp").val());
            });

            $('#data-active-customer').on('click', '.openDetail', function() {
                var id_mkt = $(this).attr('data-idMkt');
                var bulan = $(this).attr('data-bulan');
                var tahun = $(this).attr('data-tahun');
                var wilayah = $(this).attr('data-wilayah');
                $('#modalDetail').modal({
                    show: true,
                    keyboard: false,
                    backdrop: 'static'
                })
                $.ajax({
                    type: "POST",
                    url: `<?= BASE_URL . "/web/datatable/data-detail-active-customer.php" ?>`,
                    dataType: "json",
                    data: {
                        "id_mkt": id_mkt,
                        "bulan": bulan,
                        "tahun": tahun,
                        "wilayah": wilayah
                    },
                    success: function(result) {
                        // console.log(result)
                        var html = "";
                        var total = 0;
                        $("#title-detail").html(result.nama_mkt + "</br>" + " Bulan " + result.bulan)
                        for (var i = 0; i < result.data.length; i++) {
                            total += parseInt(result.data[i]['total'])
                            var no = i + 1;
                            html += "<tr>";
                            html += "<td align='center'>" + no + "</td>";
                            html += "<td>" + result.data[i]['nama_customer'] + "</td>";
                            html += "<td nowrap>" + result.data[i]['nama_prov'] + "</td>";
                            html += "<td align='center' nowrap>" + new Intl.NumberFormat().format(result.data[i]['total']) + "</td>";
                            html += "</tr>";
                        }
                        html += "<td align='center' colspan='3'><b>TOTAL</b></td>";
                        html += "<td align='center'><b>" + new Intl.NumberFormat().format(total) + "</b></td>";
                        $('#bodyResult').html(html);
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert("Error");
                    }
                });
            });
        });
    </script>
</body>

</html>
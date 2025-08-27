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

include_once($public_base_directory . "/web/__get_volume_marketing.php");

// Cek peran pengguna
$required_role = ['1', '7', '21', '4', '3', '11', '6', '20'];
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
                <h1>Marketing Volume Report</h1>
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
                                        $agc = "";
                                        $agm = "";
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
                                        <table class="table table-bordered table-hover" id="data-marketing-volume-report-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" width="3%" style="background: white;">No</th>
                                                    <th class="text-center" width="20%" style="background: white;">Marketing</th>
                                                    <th class="text-center" width="20%" style="background: white;">Januari</th>
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
                                                    <th class="text-center" width="10%" style="background: white;">Total Volume</th>
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
                                        <input type="hidden" name="data-idMkt" id="data-idMkt">
                                        <input type="hidden" name="data-bulan" id="data-bulan">
                                        <input type="hidden" name="data-tahun" id="data-tahun">
                                        <input type="hidden" name="data-wilayah" id="data-wilayah">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="hidden" name="q1Modal[]" id="q1Modal" class="form-control">
                                                <select class="form-control select2" name="selectCustomer" id="selectCustomer" multiple>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <button type="button" id="btnModalSearch" class="btn btn-info btn-md">Search</button>
                                            </div>
                                        </div>
                                        <hr>
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
                        <div class="row">
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
            $('#selectCustomer').on('change', function() {
                var selectedValue = $(this).val(); // Mengambil nilai yang dipilih
                $('#q1Modal').val(selectedValue); // Menampilkan nilai ke konsol
            });

            $('#modalDetail').on('hidden.bs.modal', function() {
                $('#q1Modal').val("");
                $('#selectCustomer').val(null).trigger('change');
            })

            $("#btnModalSearch").on('click', function() {
                var q1Modal = $('#q1Modal').val();
                var id_mkt = $('#data-idMkt').val();
                var bulan = $('#data-bulan').val();
                var tahun = $('#data-tahun').val();
                var wilayah = $('#data-wilayah').val();

                $.ajax({
                    type: "POST",
                    url: `<?= BASE_URL . "/web/datatable/data-detail-volume-marketing.php" ?>`,
                    dataType: "json",
                    data: {
                        "q1": q1Modal,
                        "id_mkt": id_mkt,
                        "bulan": bulan,
                        "tahun": tahun,
                        "wilayah": wilayah
                    },
                    success: function(result) {
                        if (result.data.length > 0) {
                            var html = "";
                            var total = 0;
                            $("#title-detail").html(result.nama_mkt + "</br>" + " Bulan " + result.bulan)
                            for (var i = 0; i < result.data.length; i++) {
                                var angkutan = "";

                                if (result.data[i]['pr_mobil'] == '1') {
                                    angkutan = "Truck";
                                } else if (result.data[i]['pr_mobil'] == '2') {
                                    angkutan = "Kapal";
                                } else if (result.data[i]['pr_mobil'] == '3') {
                                    angkutan = "Loco";
                                } else {
                                    angkutan = "-";
                                }

                                total += parseInt(result.data[i]['volume_kirim'])

                                var no = i + 1;
                                html += "<tr>";
                                html += "<td>" + no + "</td>";
                                html += "<td>" + result.data[i]['nama_customer'] + "</td>";
                                html += "<td align='center' nowrap>" + angkutan + "</td>";
                                html += "<td align='center' nowrap>" + result.data[i]['tanggal_poc'] + "</td>";
                                html += "<td align='center' nowrap>" + result.data[i]['tanggal_kirim'] + "</td>";
                                html += "<td>" + result.data[i]['nomor_poc'] + "</td>";
                                html += "<td align='center'>" + new Intl.NumberFormat().format(result.data[i]['volume_kirim']) + "</td>";
                                html += "</tr>";
                            }
                            html += "<td align='center' colspan='6'><b>TOTAL</b></td>";
                            html += "<td align='center'><b>" + new Intl.NumberFormat().format(total) + "</b></td>";
                            $('#bodyResult').html(html);

                        } else {
                            var html = "";
                            html += "<td align='center' colspan='7'><b>Data tidak ditemukan</b></td>";
                            $('#bodyResult').html(html);
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        console.log(XMLHttpRequest.responseText); // Lihat error response
                        alert("Error");
                    }
                });
            });

            var volume = <?php echo $volumeJSON; ?>;
            // Create the chart
            Highcharts.chart('diagram-volume', {
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
                        text: 'Range Volume'
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
                            format: '{point.y:,.0f} Liter'
                        }
                    }
                },

                tooltip: {
                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> of total<br/>'
                },

                series: [{
                    name: 'Volume',
                    colorByPoint: true,
                    data: [{
                            name: 'Januari',
                            y: volume.januari,
                            drilldown: 'Januari'
                        },
                        {
                            name: 'Februari',
                            y: volume.februari,
                            drilldown: 'Februari'
                        },
                        {
                            name: 'Maret',
                            y: volume.maret,
                            drilldown: 'Maret'
                        },
                        {
                            name: 'April',
                            y: volume.april,
                            drilldown: 'April'
                        },
                        {
                            name: 'Mei',
                            y: volume.mei,
                            drilldown: 'Mei'
                        },
                        {
                            name: 'Juni',
                            y: volume.juni,
                            drilldown: 'Juni'
                        },
                        {
                            name: 'Juli',
                            y: volume.juli,
                            drilldown: 'Juli'
                        },
                        {
                            name: 'Agustus',
                            y: volume.agustus,
                            drilldown: 'Agustus'
                        },
                        {
                            name: 'September',
                            y: volume.september,
                            drilldown: 'September'
                        },
                        {
                            name: 'Oktober',
                            y: volume.oktober,
                            drilldown: 'Oktober'
                        },
                        {
                            name: 'November',
                            y: volume.november,
                            drilldown: 'November'
                        },
                        {
                            name: 'Desember',
                            y: volume.desember,
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

            $("#data-marketing-volume-report-table").ajaxGrid({
                url: "../datatable/marketing-volume-report.php",
                data: {
                    q1: $("#q1").val(),
                    q2: $("#q2").val(),
                    q3: $("#q3").val()
                },
            });
            $('#btnSearch').on('click', function() {
                var valq1 = $('#q1').val();
                var valq2 = $('#q2').val();
                var valq3 = $('#q3').val();
                if (valq1 != "") {
                    $("#tahun").html("Tahun " + valq1);
                } else {
                    valq1 = `<?= date("Y") ?>`;
                }
                var param = {
                    q1: valq1,
                    q2: valq2,
                    q3: valq3
                };
                $.ajax({
                    type: "POST",
                    url: `<?= BASE_URL . "/web/__get_volume_marketing.php" ?>`,
                    data: {
                        "q1": valq1,
                        "q2": valq2,
                        "q3": valq3
                    },
                    dataType: "json",
                    success: function(data) {
                        if (data.length != 0) {
                            // console.log(data);
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

                $("#data-marketing-volume-report-table").ajaxGrid("draw", {
                    data: param
                });

                return false;
            });

            $('#tableGridLength').on('change', function() {
                $("#data-marketing-volume-report-table").ajaxGrid("pageLen", $(this).val());
            });

            $('#expData').on('click', function() {
                $(this).prop("href", $("#uriExp").val());
            });

            $('#data-marketing-volume-report-table').on('click', '.openDetail', function() {
                var id_mkt = $(this).attr('data-idMkt');
                var bulan = $(this).attr('data-bulan');
                var tahun = $(this).attr('data-tahun');
                var wilayah = $(this).attr('data-wilayah');
                $('#modalDetail').modal({
                    show: true,
                    keyboard: false,
                })
                $.ajax({
                    type: "POST",
                    url: `<?= BASE_URL . "/web/datatable/data-detail-volume-marketing.php" ?>`,
                    dataType: "json",
                    data: {
                        "id_mkt": id_mkt,
                        "bulan": bulan,
                        "tahun": tahun,
                        "wilayah": wilayah
                    },
                    success: function(result) {
                        $('#data-idMkt').val(id_mkt);
                        $('#data-bulan').val(bulan);
                        $('#data-tahun').val(tahun);
                        $('#data-wilayah').val(wilayah);

                        var html = "";
                        var total = 0;
                        $("#title-detail").html(result.nama_mkt + "</br>" + " Bulan " + result.bulan)
                        for (var i = 0; i < result.data.length; i++) {
                            var angkutan = "";

                            if (result.data[i]['pr_mobil'] == '1') {
                                angkutan = "Truck";
                            } else if (result.data[i]['pr_mobil'] == '2') {
                                angkutan = "Kapal";
                            } else if (result.data[i]['pr_mobil'] == '3') {
                                angkutan = "Loco";
                            } else {
                                angkutan = "-";
                            }

                            total += parseInt(result.data[i]['volume_kirim'])

                            var no = i + 1;
                            html += "<tr>";
                            html += "<td>" + no + "</td>";
                            html += "<td>" + result.data[i]['nama_customer'] + "</td>";
                            html += "<td align='center' nowrap>" + angkutan + "</td>";
                            html += "<td align='center' nowrap>" + result.data[i]['tanggal_poc'] + "</td>";
                            html += "<td align='center' nowrap>" + result.data[i]['tanggal_kirim'] + "</td>";
                            html += "<td>" + result.data[i]['nomor_poc'] + "</td>";
                            html += "<td align='center'>" + new Intl.NumberFormat().format(result.data[i]['volume_kirim']) + "</td>";
                            html += "</tr>";
                        }
                        html += "<td align='center' colspan='6'><b>TOTAL</b></td>";
                        html += "<td align='center'><b>" + new Intl.NumberFormat().format(total) + "</b></td>";
                        $('#bodyResult').html(html);

                        $('#selectCustomer').empty();
                        for (var i = 0; i < result.customer.length; i++) {
                            $('#selectCustomer').append('<option value="' + result.customer[i].id_customer + '">' + result.customer[i].nama_customer + '</option>');
                        }
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
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

include_once($public_base_directory . "/web/__get_inventory_stock_pr.php");

?>

<!-- <script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/solid-gauge.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->

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


<?php
$selectedMonth = date('Y-m');
?>





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






        <br>


        <div style="overflow-x: auto; overflow-y: hidden; white-space: nowrap; max-height: 400px;">
            <?php foreach ($charts as $chartData) : ?>
                <div style="display: inline-block; vertical-align: top; margin-right: 20px;">
                    <div id="<?php echo $chartData['containerId']; ?>" style="width: 400px; height: 370px;"></div>
                </div>
            <?php endforeach; ?>
        </div>



    </div>

    <!-- <div class="row">
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
                                COALESCE(SUM(CASE WHEN e.is_loaded = 0 AND e.is_cancel != 1 THEN 1 ELSE 0 END), 0) AS backlog_count,
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
    </div> -->
</div>




<!-- <div class="row mb-3">
    <div class="col-md-4">
        <label for="monthPicker">Pilih Bulan:</label>
        <input
            type="month"
            id="monthPicker"
            class="form-control"
            value="<?= htmlspecialchars($selectedMonth) ?>">
    </div>
    <div class="col-md-4">
        <label for="q5">Depot Terminal:</label>
        <select id="q5" name="q5" class="form-control select2">
            <option value="">— Semua Terminal —</option>
            <?php
            $con->fill_select(
                "id_master",
                "concat(nama_terminal,' ',tanki_terminal)",
                "pro_master_terminal",
                $q5,
                "where is_active=1",
                "id_master",
                false
            );
            ?>
        </select>
    </div>
    <div class="col-md-4">
        <br>
        <button id="btnFilter" class="btn btn-primary w-100">Filter</button>
    </div>
</div> -->






<!-- MODAL DETAIL LOADED -->
<!-- <div
    class="modal fade"
    id="loadedModal"
    data-backdrop="false"
    tabindex="-1"
    role="dialog"
    aria-labelledby="loadedModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loadedModalLabel">Detail Loading</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p><strong>Cabang:</strong> <span id="lmCabang"></span></p>
                <p><strong>Tanggal:</strong> <span id="lmTanggal"></span></p>
                <p><strong>Total Loaded:</strong> <span id="lmVolume"></span></p>
                <hr>
                <ul id="lprList" class="list-group"></ul>
            </div>
        </div>
    </div>
</div> -->

<!-- <script>
    // formatter untuk tanggal & angka
    const dateFmt = new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });
    const numFmt = new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });



    // Load summary table via AJAX
    function loadLoaded(month, terminalId) {
        $('#loadedTableContainer').html('…spinner…');
        $.post('monitoring_summary_loaded.php', {
                month: month,
                terminal: terminalId
            },
            html => {
                $('#loadedTableContainer').html(html);
            }
        );
    }




    $(document).ready(function() {
        loadLoaded($('#monthPicker').val(), $('#q5').val());
    });

    $('#btnFilter').on('click', () => {
        loadLoaded($('#monthPicker').val(), $('#q5').val());
    });

    // delegasi klik tombol volume di dalam tabel yang nanti ter-load



    $(document).on('click', '.loaded-btn', function() {
        const cabang = $(this).data('cabang');
        const day = $(this).data('day');
        const volume = $(this).data('volume');
        const month = $('#monthPicker').val();

        $('#lmCabang').text(cabang);
        $('#lmTanggal').text(day + ' ' + dateFmt.format(new Date(month + '-01')).split(' ')[1]);
        $('#lmVolume').text(numFmt.format(volume) + ' Ltr');

        const $list = $('#lprList').empty();
        $.post('monitoring_loaded_detail.php', {
            cabang,
            day,
            month
        }, data => {
            if (!data.length) {
                $list.append('<li class="list-group-item">Tidak ada Loading</li>');
            } else {
                data.forEach(item => {
                    const tgl = dateFmt.format(new Date(item.tanggal));
                    const tgl_awal = dateFmt.format(new Date(item.tanggal_awal));
                    const tgl_akhir = dateFmt.format(new Date(item.tanggal_akhir));
                    const vol = numFmt.format(item.volume) + ' Ltr';
                    $list.append(`
            <li class="list-group-item">
             <strong> Periode Penawaran : <i>${tgl_awal} - ${tgl_akhir}</i></strong><br>
                <small class="text-muted">Nomor DR : ${item.nomor_pr}</small><br>
                <small class="text-muted">Nomor LO : ${item.nomor_lo}</small><br>
                <small class="text-muted">Terminal : ${item.terminal}</small><br>
              <small class="text-muted">Tanggal Loaded : ${tgl}</small><br>
              <small class="text-muted">Volume : ${vol}</small>
            </li>
          `);
                });
            }
            $('#loadedModal').modal('show');
        }, 'json');
    });
</script> -->


<?php include_once($public_base_directory . "/web/__sc_inventory_stock_pr.php"); ?>
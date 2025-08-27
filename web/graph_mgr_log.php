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

include_once($public_base_directory . "/web/__get_dashboard_mgr_log.php");

?>





<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<br>
<br>
<form name="searchForm" id="searchForm" role="form" class="form-horizontal" method="POST">
    <div class="form-group row">

        <div class="col-sm-3 col-sm-top">
            <select name="q9" id="q9" class="form-control select2">
                <option></option>
                <?php $con->fill_select("id_master", "nama_cabang", "pro_master_cabang", $q1, "where is_active=1" . $agc, "", false); ?>
            </select>
        </div>
        <div class="col-sm-3">
            <div class="input-group">
                <span class="input-group-addon">Periode</span>
                <input type="text" name="q10" id="q10" class="form-control input-sm validate[required,custom[date]] datepicker" autocomplete='off' />
            </div>
        </div>
        <div class="col-sm-3">
            <div class="input-group">
                <span class="input-group-addon">S/D</span>
                <input type="text" name="q11" id="q11" class="form-control input-sm validate[required,custom[date]] datepicker" autocomplete='off' />
            </div>
        </div>


    </div>
    <div class="form-group row">
        <div class="col-sm-4 col-sm-top">

            <button type="submit" class="btn btn-info btn-sm" name="btnSearch5" id="btnSearch5"><i class="fa fa-search jarak-kanan"></i>Search</button>
        </div>
    </div>
</form>


<div class="row">
    <div class="col-sm-12">
        <div class="box box-info">
            <div class="container-fluid">
                <h4 class="text-center">Total Supply & Losses</h4>

                <figure class="highcharts-figure">
                    <div id="container"></div>
                    <p class="highcharts-description">

                    </p>
                </figure>
            </div>
        </div>
    </div>

</div>

<div class="row">
    <div class="col-sm-6">
        <div class="box box-info">
            <div class="container-fluid">
                <h4 class="text-center"></h4>

                <figure class="highcharts-figure">
                    <div id="container1"></div>

                </figure>

            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="box box-info">
            <div class="container-fluid">
                <h4 class="text-center"></h4>

                <figure class="highcharts-figure">
                    <div id="container2"></div>

                </figure>

            </div>
        </div>
    </div>

</div>




<?php
// Mendapatkan tanggal 7 hari ke belakang dari tanggal saat ini
$today = date('Y-m-d');
$thirtyDaysAgo = date('Y-m-d', strtotime('-7 days', strtotime($today)));

// Array untuk menyimpan tanggal-tanggal yang akan ditampilkan di tabel
$dateArray = array();
for ($i = 0; $i < 7; $i++) {
    $dateArray[] = date('d M', strtotime("-$i days", strtotime($today)));
}
$dateArray = array_reverse($dateArray); // Membalikkan urutan array agar tanggal terbaru berada di sebelah kanan

// Query untuk mengambil data dari database
$sql = "SELECT  
                    c.nama_customer,
                    a.tanggal_delivered,
                    SUM(a.realisasi_volume) AS volume,
                    SUM(d.volume_po) - SUM(a.realisasi_volume) AS losses,
                    ROUND((SUM(d.volume_po) - SUM(a.realisasi_volume)) / SUM(a.realisasi_volume), 2) AS persen,
                    e.id_wilayah
                    FROM 
                    pro_po_ds_detail a 
                    JOIN 
                    pro_po_customer b ON a.id_poc = b.id_poc
                    JOIN 
                    pro_customer c ON b.id_customer = c.id_customer
                    JOIN 
                    pro_po_detail d ON a.id_pod = d.id_pod
                    JOIN 
                    pro_po e ON a.id_po = e.id_po
                    WHERE   
                    a.tanggal_delivered BETWEEN '$thirtyDaysAgo' AND '$today'
                    AND e.id_wilayah = '2'
                    GROUP BY 
                    c.nama_customer, a.tanggal_delivered
                    ORDER BY 
                    losses DESC
                    LIMIT 10";

// Mendapatkan hasil query
$result = $con->getResult($sql);


// Mengelompokkan hasil berdasarkan customer dan tanggal
$dataArray = array();
if (!empty($result) && is_array($result)) {
    foreach ($result as $row) {
        $customer = $row['nama_customer'];
        $date = date('d M', strtotime($row['tanggal_delivered']));
        $dataArray[$customer][$date] = $row;
    }
}

// Output HTML
?>
<div class="row">






    <div class="col-sm-12">
        <div class="box box-info">
            <div class="box-header with-border">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="text-center" style="margin-top: 10px">
                            SOURCE LAST MONTH
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-bordered col-sm-top table-isi" id="table-grid">
                    <thead>
                        <tr>

                            <th width="220px" class="text-center" style="background-color:#00008B;color:white;">Source Banjarmasin</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Jan</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Feb</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Mar</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Apr</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Mei</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Jun</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Jul</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Ags</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Sept</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Okt</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Nov</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Des</th>


                        </tr>

                    </thead>
                    <tbody>

                        <?php

                        $sql1 = " SELECT 
    c.nama_terminal,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '01' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 7
            AND c1.id_master = c.id_master
    ) AS source_januari,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '02' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 7
            AND c1.id_master = c.id_master
    ) AS source_februari,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '03' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 7
            AND c1.id_master = c.id_master
    ) AS source_maret,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '04' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 7
            AND c1.id_master = c.id_master
    ) AS source_april,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '05' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 7
            AND c1.id_master = c.id_master
    ) AS source_mei,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '06' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 7
            AND c1.id_master = c.id_master
    ) AS source_juni,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '07' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 7
            AND c1.id_master = c.id_master
    ) AS source_juli,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '08' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 7
            AND c1.id_master = c.id_master
    ) AS source_agustus,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '09' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 7
            AND c1.id_master = c.id_master
    ) AS source_september,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '10' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 7
            AND c1.id_master = c.id_master
    ) AS source_oktober,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '11' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 7
            AND c1.id_master = c.id_master
    ) AS source_november,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '12' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 7
            AND c1.id_master = c.id_master
    ) AS source_desember
FROM 
    pro_master_terminal c
LEFT JOIN 
    pro_po_ds b ON c.id_master = b.id_terminal
LEFT JOIN 
    pro_po_ds_detail a ON b.id_ds = a.id_ds
LEFT JOIN 
    pro_po_detail d ON a.id_pod = d.id_pod 
WHERE 
    b.id_wilayah = 7
GROUP BY 
    c.nama_terminal
    HAVING 
    source_januari IS NOT NULL OR 
    source_februari IS NOT NULL OR 
    source_maret IS NOT NULL OR 
    source_april IS NOT NULL OR 
    source_mei IS NOT NULL OR 
    source_juni IS NOT NULL OR 
    source_juli IS NOT NULL OR 
    source_agustus IS NOT NULL OR 
    source_september IS NOT NULL OR 
    source_oktober IS NOT NULL OR 
    source_november IS NOT NULL OR 
    source_desember IS NOT NULL;
                    
                        ";

                        // Mendapatkan hasil query
                        $result1 = $con->getResult($sql1);
                        $count = 0;
                        $total_volume1_jan = 0;
                        $total_volume1_feb = 0;
                        $total_volume1_mar = 0;
                        $total_volume1_apr = 0;
                        $total_volume1_mei = 0;
                        $total_volume1_jun = 0;
                        $total_volume1_jul = 0;
                        $total_volume1_ags = 0;
                        $total_volume1_sept = 0;
                        $total_volume1_okt = 0;
                        $total_volume1_nov = 0;
                        $total_volume1_des = 0;




                        foreach ($result1 as $data) {
                            $count++;
                            $total_volume1_jan += $data['source_januari'];
                            $total_volume1_feb += $data['source_februari'];
                            $total_volume1_mar += $data['source_maret'];
                            $total_volume1_apr += $data['source_april'];
                            $total_volume1_mei += $data['source_mei'];
                            $total_volume1_jun += $data['source_juni'];
                            $total_volume1_jul += $data['source_juli'];
                            $total_volume1_ags += $data['source_agustus'];
                            $total_volume1_sept += $data['source_september'];
                            $total_volume1_okt += $data['source_oktober'];
                            $total_volume1_nov += $data['source_november'];
                            $total_volume1_des += $data['source_desember'];


                            echo '<tr>';

                            echo '<td>' . $data['nama_terminal'] . '</td>'; // Menampilkan nama customer
                            echo '<td>' . number_format($data['source_januari']) . '</td>';
                            echo '<td>' . number_format($data['source_februari']) . '</td>';
                            echo '<td>' . number_format($data['source_maret']) . '</td>';
                            echo '<td>' . number_format($data['source_april']) . '</td>';
                            echo '<td>' . number_format($data['source_mei']) . '</td>';
                            echo '<td>' . number_format($data['source_juni']) . '</td>';
                            echo '<td>' . number_format($data['source_juli']) . '</td>';
                            echo '<td>' . number_format($data['source_agustus']) . '</td>';
                            echo '<td>' . number_format($data['source_september']) . '</td>';
                            echo '<td>' . number_format($data['source_oktober']) . '</td>';
                            echo '<td>' . number_format($data['source_november']) . '</td>';
                            echo '<td>' . number_format($data['source_desember']) . '</td>';




                            echo '</tr>';
                        }
                        ?>




                    </tbody>
                    <tfoot>
                        <tr style="font-weight: bold;">
                            <td>Grand Total</td>
                            <td><?php echo number_format($total_volume1_jan); ?></td>
                            <td><?php echo number_format($total_volume1_feb); ?></td>
                            <td><?php echo number_format($total_volume1_mar); ?></td>
                            <td><?php echo number_format($total_volume1_apr); ?></td>
                            <td><?php echo number_format($total_volume1_mei); ?></td>
                            <td><?php echo number_format($total_volume1_jun); ?></td>
                            <td><?php echo number_format($total_volume1_jul); ?></td>
                            <td><?php echo number_format($total_volume1_ags); ?></td>
                            <td><?php echo number_format($total_volume1_sept); ?></td>
                            <td><?php echo number_format($total_volume1_okt); ?></td>
                            <td><?php echo number_format($total_volume1_nov); ?></td>
                            <td><?php echo number_format($total_volume1_des); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <p></p>
            <div class="box-body table-responsive">
                <table class="table table-bordered col-sm-top table-isi" id="table-grid">
                    <thead>
                        <tr>

                            <th width="220px" class="text-center" style="background-color:#00008B;color:white;">Source Pontianak</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Jan</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Feb</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Mar</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Apr</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Mei</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Jun</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Jul</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Ags</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Sept</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Okt</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Nov</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Des</th>


                        </tr>

                    </thead>
                    <tbody>

                        <?php

                        $sql1 = " SELECT 
    c.nama_terminal,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '01' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 5
            AND c1.id_master = c.id_master
    ) AS source_januari,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '02' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 5
            AND c1.id_master = c.id_master
    ) AS source_februari,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '03' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 5
            AND c1.id_master = c.id_master
    ) AS source_maret,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '04' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 5
            AND c1.id_master = c.id_master
    ) AS source_april,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '05' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 5
            AND c1.id_master = c.id_master
    ) AS source_mei,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '06' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 5
            AND c1.id_master = c.id_master
    ) AS source_juni,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '07' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 5
            AND c1.id_master = c.id_master
    ) AS source_juli,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '08' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 5
            AND c1.id_master = c.id_master
    ) AS source_agustus,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '09' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 5
            AND c1.id_master = c.id_master
    ) AS source_september,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '10' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 5
            AND c1.id_master = c.id_master
    ) AS source_oktober,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '11' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 5
            AND c1.id_master = c.id_master
    ) AS source_november,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '12' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 5
            AND c1.id_master = c.id_master
    ) AS source_desember
FROM 
    pro_master_terminal c
LEFT JOIN 
    pro_po_ds b ON c.id_master = b.id_terminal
LEFT JOIN 
    pro_po_ds_detail a ON b.id_ds = a.id_ds
LEFT JOIN 
    pro_po_detail d ON a.id_pod = d.id_pod 
WHERE 
    b.id_wilayah = 5
GROUP BY 
    c.nama_terminal
    HAVING 
    source_januari IS NOT NULL OR 
    source_februari IS NOT NULL OR 
    source_maret IS NOT NULL OR 
    source_april IS NOT NULL OR 
    source_mei IS NOT NULL OR 
    source_juni IS NOT NULL OR 
    source_juli IS NOT NULL OR 
    source_agustus IS NOT NULL OR 
    source_september IS NOT NULL OR 
    source_oktober IS NOT NULL OR 
    source_november IS NOT NULL OR 
    source_desember IS NOT NULL;

";

                        // Mendapatkan hasil query
                        $result1 = $con->getResult($sql1);
                        $count = 0;
                        $total_volume2_jan = 0;
                        $total_volume2_feb = 0;
                        $total_volume2_mar = 0;
                        $total_volume2_apr = 0;
                        $total_volume2_mei = 0;
                        $total_volume2_jun = 0;
                        $total_volume2_jul = 0;
                        $total_volume2_ags = 0;
                        $total_volume2_sept = 0;
                        $total_volume2_okt = 0;
                        $total_volume2_nov = 0;
                        $total_volume2_des = 0;




                        foreach ($result1 as $data) {
                            $count++;
                            $total_volume2_jan += $data['source_januari'];
                            $total_volume2_feb += $data['source_februari'];
                            $total_volume2_mar += $data['source_maret'];
                            $total_volume2_apr += $data['source_april'];
                            $total_volume2_mei += $data['source_mei'];
                            $total_volume2_jun += $data['source_juni'];
                            $total_volume2_jul += $data['source_juli'];
                            $total_volume2_ags += $data['source_agustus'];
                            $total_volume2_sept += $data['source_september'];
                            $total_volume2_okt += $data['source_oktober'];
                            $total_volume2_nov += $data['source_november'];
                            $total_volume2_des += $data['source_desember'];


                            echo '<tr>';

                            echo '<td>' . $data['nama_terminal'] . '</td>'; // Menampilkan nama customer
                            echo '<td>' . number_format($data['source_januari']) . '</td>';
                            echo '<td>' . number_format($data['source_februari']) . '</td>';
                            echo '<td>' . number_format($data['source_maret']) . '</td>';
                            echo '<td>' . number_format($data['source_april']) . '</td>';
                            echo '<td>' . number_format($data['source_mei']) . '</td>';
                            echo '<td>' . number_format($data['source_juni']) . '</td>';
                            echo '<td>' . number_format($data['source_juli']) . '</td>';
                            echo '<td>' . number_format($data['source_agustus']) . '</td>';
                            echo '<td>' . number_format($data['source_september']) . '</td>';
                            echo '<td>' . number_format($data['source_oktober']) . '</td>';
                            echo '<td>' . number_format($data['source_november']) . '</td>';
                            echo '<td>' . number_format($data['source_desember']) . '</td>';




                            echo '</tr>';
                        }
                        ?>






                    </tbody>
                    <tfoot>
                        <tr style="font-weight: bold;">
                            <td>Grand Total</td>
                            <td><?php echo number_format($total_volume2_jan); ?></td>
                            <td><?php echo number_format($total_volume2_feb); ?></td>
                            <td><?php echo number_format($total_volume2_mar); ?></td>
                            <td><?php echo number_format($total_volume2_apr); ?></td>
                            <td><?php echo number_format($total_volume2_mei); ?></td>
                            <td><?php echo number_format($total_volume2_jun); ?></td>
                            <td><?php echo number_format($total_volume2_jul); ?></td>
                            <td><?php echo number_format($total_volume2_ags); ?></td>
                            <td><?php echo number_format($total_volume2_sept); ?></td>
                            <td><?php echo number_format($total_volume2_okt); ?></td>
                            <td><?php echo number_format($total_volume2_nov); ?></td>
                            <td><?php echo number_format($total_volume2_des); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="box-body table-responsive">
                <table class="table table-bordered col-sm-top table-isi" id="table-grid">
                    <thead>
                        <tr>

                            <th width="220px" class="text-center" style="background-color:#00008B;color:white;">Source Palembang</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Jan</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Feb</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Mar</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Apr</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Mei</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Jun</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Jul</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Ags</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Sept</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Okt</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Nov</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Des</th>


                        </tr>

                    </thead>
                    <tbody>

                        <?php

                        $sql1 = " SELECT 
    c.nama_terminal,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '01' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 3
            AND c1.id_master = c.id_master
    ) AS source_januari,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '02' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 3
            AND c1.id_master = c.id_master
    ) AS source_februari,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '03' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 3
            AND c1.id_master = c.id_master
    ) AS source_maret,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '04' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 3
            AND c1.id_master = c.id_master
    ) AS source_april,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '05' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 3
            AND c1.id_master = c.id_master
    ) AS source_mei,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '06' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 3
            AND c1.id_master = c.id_master
    ) AS source_juni,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '07' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 3
            AND c1.id_master = c.id_master
    ) AS source_juli,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '08' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 3
            AND c1.id_master = c.id_master
    ) AS source_agustus,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '09' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 3
            AND c1.id_master = c.id_master
    ) AS source_september,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '10' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 3
            AND c1.id_master = c.id_master
    ) AS source_oktober,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '11' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 3
            AND c1.id_master = c.id_master
    ) AS source_november,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '12' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 3
            AND c1.id_master = c.id_master
    ) AS source_desember
FROM 
    pro_master_terminal c
LEFT JOIN 
    pro_po_ds b ON c.id_master = b.id_terminal
LEFT JOIN 
    pro_po_ds_detail a ON b.id_ds = a.id_ds
LEFT JOIN 
    pro_po_detail d ON a.id_pod = d.id_pod 
WHERE 
    b.id_wilayah = 3
GROUP BY 
    c.nama_terminal
    HAVING 
    source_januari IS NOT NULL OR 
    source_februari IS NOT NULL OR 
    source_maret IS NOT NULL OR 
    source_april IS NOT NULL OR 
    source_mei IS NOT NULL OR 
    source_juni IS NOT NULL OR 
    source_juli IS NOT NULL OR 
    source_agustus IS NOT NULL OR 
    source_september IS NOT NULL OR 
    source_oktober IS NOT NULL OR 
    source_november IS NOT NULL OR 
    source_desember IS NOT NULL;

";

                        // Mendapatkan hasil query
                        $result1 = $con->getResult($sql1);
                        $count = 0;
                        $total_volume3_jan = 0;
                        $total_volume3_feb = 0;
                        $total_volume3_mar = 0;
                        $total_volume3_apr = 0;
                        $total_volume3_mei = 0;
                        $total_volume3_jun = 0;
                        $total_volume3_jul = 0;
                        $total_volume3_ags = 0;
                        $total_volume3_sept = 0;
                        $total_volume3_okt = 0;
                        $total_volume3_nov = 0;
                        $total_volume3_des = 0;




                        foreach ($result1 as $data) {
                            $count++;
                            $total_volume3_jan += $data['source_januari'];
                            $total_volume3_feb += $data['source_februari'];
                            $total_volume3_mar += $data['source_maret'];
                            $total_volume3_apr += $data['source_april'];
                            $total_volume3_mei += $data['source_mei'];
                            $total_volume3_jun += $data['source_juni'];
                            $total_volume3_jul += $data['source_juli'];
                            $total_volume3_ags += $data['source_agustus'];
                            $total_volume3_sept += $data['source_september'];
                            $total_volume3_okt += $data['source_oktober'];
                            $total_volume3_nov += $data['source_november'];
                            $total_volume3_des += $data['source_desember'];


                            echo '<tr>';

                            echo '<td>' . $data['nama_terminal'] . '</td>'; // Menampilkan nama customer
                            echo '<td>' . number_format($data['source_januari']) . '</td>';
                            echo '<td>' . number_format($data['source_februari']) . '</td>';
                            echo '<td>' . number_format($data['source_maret']) . '</td>';
                            echo '<td>' . number_format($data['source_april']) . '</td>';
                            echo '<td>' . number_format($data['source_mei']) . '</td>';
                            echo '<td>' . number_format($data['source_juni']) . '</td>';
                            echo '<td>' . number_format($data['source_juli']) . '</td>';
                            echo '<td>' . number_format($data['source_agustus']) . '</td>';
                            echo '<td>' . number_format($data['source_september']) . '</td>';
                            echo '<td>' . number_format($data['source_oktober']) . '</td>';
                            echo '<td>' . number_format($data['source_november']) . '</td>';
                            echo '<td>' . number_format($data['source_desember']) . '</td>';




                            echo '</tr>';
                        }
                        ?>






                    </tbody>
                    <tfoot>
                        <tr style="font-weight: bold;">
                            <td>Grand Total</td>
                            <td><?php echo number_format($total_volume3_jan); ?></td>
                            <td><?php echo number_format($total_volume3_feb); ?></td>
                            <td><?php echo number_format($total_volume3_mar); ?></td>
                            <td><?php echo number_format($total_volume3_apr); ?></td>
                            <td><?php echo number_format($total_volume3_mei); ?></td>
                            <td><?php echo number_format($total_volume3_jun); ?></td>
                            <td><?php echo number_format($total_volume3_jul); ?></td>
                            <td><?php echo number_format($total_volume3_ags); ?></td>
                            <td><?php echo number_format($total_volume3_sept); ?></td>
                            <td><?php echo number_format($total_volume3_okt); ?></td>
                            <td><?php echo number_format($total_volume3_nov); ?></td>
                            <td><?php echo number_format($total_volume3_des); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-bordered col-sm-top table-isi" id="table-grid">
                    <thead>
                        <tr>

                            <th width="220px" class="text-center" style="background-color:#00008B;color:white;">Source Surabaya</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Jan</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Feb</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Mar</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Apr</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Mei</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Jun</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Jul</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Ags</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Sept</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Okt</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Nov</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Des</th>


                        </tr>

                    </thead>
                    <tbody>

                        <?php

                        $sql1 = "SELECT 
    c.nama_terminal,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '01' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 6
            AND c1.id_master = c.id_master
            AND c1.id_master != 68
    ) AS source_januari,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '02' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 6
            AND c1.id_master = c.id_master
            AND c1.id_master != 68
    ) AS source_februari,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '03' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 6
            AND c1.id_master = c.id_master
            AND c1.id_master != 68
    ) AS source_maret,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '04' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 6
            AND c1.id_master = c.id_master
            AND c1.id_master != 68
    ) AS source_april,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '05' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 6
            AND c1.id_master = c.id_master
            AND c1.id_master != 68
    ) AS source_mei,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '06' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 6
            AND c1.id_master = c.id_master
            AND c1.id_master != 68
    ) AS source_juni,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '07' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 6
            AND c1.id_master = c.id_master
            AND c1.id_master != 68
    ) AS source_juli,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '08' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 6
            AND c1.id_master = c.id_master
            AND c1.id_master != 68
    ) AS source_agustus,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '09' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 6
            AND c1.id_master = c.id_master
            AND c1.id_master != 68
    ) AS source_september,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '10' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 6
            AND c1.id_master = c.id_master
            AND c1.id_master != 68
    ) AS source_oktober,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '11' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 6
            AND c1.id_master = c.id_master
            AND c1.id_master != 68
    ) AS source_november,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '12' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 6
            AND c1.id_master = c.id_master
            AND c1.id_master != 68
    ) AS source_desember
FROM 
    pro_master_terminal c
LEFT JOIN 
    pro_po_ds b ON c.id_master = b.id_terminal
LEFT JOIN 
    pro_po_ds_detail a ON b.id_ds = a.id_ds
LEFT JOIN 
    pro_po_detail d ON a.id_pod = d.id_pod 
WHERE 
    b.id_wilayah = 6
GROUP BY 
    c.nama_terminal
    HAVING 
    source_januari IS NOT NULL OR 
    source_februari IS NOT NULL OR 
    source_maret IS NOT NULL OR 
    source_april IS NOT NULL OR 
    source_mei IS NOT NULL OR 
    source_juni IS NOT NULL OR 
    source_juli IS NOT NULL OR 
    source_agustus IS NOT NULL OR 
    source_september IS NOT NULL OR 
    source_oktober IS NOT NULL OR 
    source_november IS NOT NULL OR 
    source_desember IS NOT NULL;
    


";

                        // Mendapatkan hasil query
                        $result1 = $con->getResult($sql1);
                        $count = 0;
                        $total_volume4_jan = 0;
                        $total_volume4_feb = 0;
                        $total_volume4_mar = 0;
                        $total_volume4_apr = 0;
                        $total_volume4_mei = 0;
                        $total_volume4_jun = 0;
                        $total_volume4_jul = 0;
                        $total_volume4_ags = 0;
                        $total_volume4_sept = 0;
                        $total_volume4_okt = 0;
                        $total_volume4_nov = 0;
                        $total_volume4_des = 0;




                        foreach ($result1 as $data) {
                            $count++;
                            $total_volume4_jan += $data['source_januari'];
                            $total_volume4_feb += $data['source_februari'];
                            $total_volume4_mar += $data['source_maret'];
                            $total_volume4_apr += $data['source_april'];
                            $total_volume4_mei += $data['source_mei'];
                            $total_volume4_jun += $data['source_juni'];
                            $total_volume4_jul += $data['source_juli'];
                            $total_volume4_ags += $data['source_agustus'];
                            $total_volume4_sept += $data['source_september'];
                            $total_volume4_okt += $data['source_oktober'];
                            $total_volume4_nov += $data['source_november'];
                            $total_volume4_des += $data['source_desember'];


                            echo '<tr>';

                            echo '<td>' . $data['nama_terminal'] . '</td>'; // Menampilkan nama customer
                            echo '<td>' . number_format($data['source_januari']) . '</td>';
                            echo '<td>' . number_format($data['source_februari']) . '</td>';
                            echo '<td>' . number_format($data['source_maret']) . '</td>';
                            echo '<td>' . number_format($data['source_april']) . '</td>';
                            echo '<td>' . number_format($data['source_mei']) . '</td>';
                            echo '<td>' . number_format($data['source_juni']) . '</td>';
                            echo '<td>' . number_format($data['source_juli']) . '</td>';
                            echo '<td>' . number_format($data['source_agustus']) . '</td>';
                            echo '<td>' . number_format($data['source_september']) . '</td>';
                            echo '<td>' . number_format($data['source_oktober']) . '</td>';
                            echo '<td>' . number_format($data['source_november']) . '</td>';
                            echo '<td>' . number_format($data['source_desember']) . '</td>';




                            echo '</tr>';
                        }
                        ?>






                    </tbody>
                    <tfoot>
                        <tr style="font-weight: bold;">
                            <td>Grand Total</td>
                            <td><?php echo number_format($total_volume4_jan); ?></td>
                            <td><?php echo number_format($total_volume4_feb); ?></td>
                            <td><?php echo number_format($total_volume4_mar); ?></td>
                            <td><?php echo number_format($total_volume4_apr); ?></td>
                            <td><?php echo number_format($total_volume4_mei); ?></td>
                            <td><?php echo number_format($total_volume4_jun); ?></td>
                            <td><?php echo number_format($total_volume4_jul); ?></td>
                            <td><?php echo number_format($total_volume4_ags); ?></td>
                            <td><?php echo number_format($total_volume4_sept); ?></td>
                            <td><?php echo number_format($total_volume4_okt); ?></td>
                            <td><?php echo number_format($total_volume4_nov); ?></td>
                            <td><?php echo number_format($total_volume4_des); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="box-body table-responsive">
                <table class="table table-bordered col-sm-top table-isi" id="table-grid">
                    <thead>
                        <tr>

                            <th width="220px" class="text-center" style="background-color:#00008B;color:white;">Source Samarinda</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Jan</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Feb</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Mar</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Apr</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Mei</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Jun</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Jul</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Ags</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Sept</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Okt</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Nov</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Des</th>


                        </tr>

                    </thead>
                    <tbody>

                        <?php

                        $sql1 = " SELECT 
    c.nama_terminal,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '01' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 4
            AND c1.id_master = c.id_master
            AND c1.id_master != 67
    ) AS source_januari,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '02' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 4
            AND c1.id_master = c.id_master
              AND c1.id_master != 67
    ) AS source_februari,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '03' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 4
            AND c1.id_master = c.id_master
              AND c1.id_master != 67
    ) AS source_maret,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '04' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 4
            AND c1.id_master = c.id_master
            AND c1.id_master != 67
    ) AS source_april,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '05' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 4
            AND c1.id_master = c.id_master
            AND c1.id_master != 67
    ) AS source_mei,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '06' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 4
            AND c1.id_master = c.id_master
            AND c1.id_master != 67
    ) AS source_juni,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '07' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 4
            AND c1.id_master = c.id_master
            AND c1.id_master != 67
    ) AS source_juli,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '08' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 4
            AND c1.id_master = c.id_master
            AND c1.id_master != 67
    ) AS source_agustus,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '09' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 4
            AND c1.id_master = c.id_master
            AND c1.id_master != 67
    ) AS source_september,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '10' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 4
            AND c1.id_master = c.id_master
            AND c1.id_master != 67
    ) AS source_oktober,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '11' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 4
            AND c1.id_master = c.id_master
            AND c1.id_master != 67
    ) AS source_november,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '12' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 4
            AND c1.id_master = c.id_master
            AND c1.id_master != 67
    ) AS source_desember
FROM 
    pro_master_terminal c
LEFT JOIN 
    pro_po_ds b ON c.id_master = b.id_terminal
LEFT JOIN 
    pro_po_ds_detail a ON b.id_ds = a.id_ds
LEFT JOIN 
    pro_po_detail d ON a.id_pod = d.id_pod 
WHERE 
    b.id_wilayah = 4
GROUP BY 
    c.nama_terminal
    HAVING 
    source_januari IS NOT NULL OR 
    source_februari IS NOT NULL OR 
    source_maret IS NOT NULL OR 
    source_april IS NOT NULL OR 
    source_mei IS NOT NULL OR 
    source_juni IS NOT NULL OR 
    source_juli IS NOT NULL OR 
    source_agustus IS NOT NULL OR 
    source_september IS NOT NULL OR 
    source_oktober IS NOT NULL OR 
    source_november IS NOT NULL OR 
    source_desember IS NOT NULL;


";

                        // Mendapatkan hasil query
                        $result1 = $con->getResult($sql1);
                        $count = 0;
                        $total_volume5_jan = 0;
                        $total_volume5_feb = 0;
                        $total_volume5_mar = 0;
                        $total_volume5_apr = 0;
                        $total_volume5_mei = 0;
                        $total_volume5_jun = 0;
                        $total_volume5_jul = 0;
                        $total_volume5_ags = 0;
                        $total_volume5_sept = 0;
                        $total_volume5_okt = 0;
                        $total_volume5_nov = 0;
                        $total_volume5_des = 0;




                        foreach ($result1 as $data) {
                            $count++;
                            $total_volume5_jan += $data['source_januari'];
                            $total_volume5_feb += $data['source_februari'];
                            $total_volume5_mar += $data['source_maret'];
                            $total_volume5_apr += $data['source_april'];
                            $total_volume5_mei += $data['source_mei'];
                            $total_volume5_jun += $data['source_juni'];
                            $total_volume5_jul += $data['source_juli'];
                            $total_volume5_ags += $data['source_agustus'];
                            $total_volume5_sept += $data['source_september'];
                            $total_volume5_okt += $data['source_oktober'];
                            $total_volume5_nov += $data['source_november'];
                            $total_volume5_des += $data['source_desember'];


                            echo '<tr>';

                            echo '<td>' . $data['nama_terminal'] . '</td>'; // Menampilkan nama customer
                            echo '<td>' . number_format($data['source_januari']) . '</td>';
                            echo '<td>' . number_format($data['source_februari']) . '</td>';
                            echo '<td>' . number_format($data['source_maret']) . '</td>';
                            echo '<td>' . number_format($data['source_april']) . '</td>';
                            echo '<td>' . number_format($data['source_mei']) . '</td>';
                            echo '<td>' . number_format($data['source_juni']) . '</td>';
                            echo '<td>' . number_format($data['source_juli']) . '</td>';
                            echo '<td>' . number_format($data['source_agustus']) . '</td>';
                            echo '<td>' . number_format($data['source_september']) . '</td>';
                            echo '<td>' . number_format($data['source_oktober']) . '</td>';
                            echo '<td>' . number_format($data['source_november']) . '</td>';
                            echo '<td>' . number_format($data['source_desember']) . '</td>';




                            echo '</tr>';
                        }
                        ?>






                    </tbody>
                    <tfoot>
                        <tr style="font-weight: bold;">
                            <td>Grand Total</td>
                            <td><?php echo number_format($total_volume5_jan); ?></td>
                            <td><?php echo number_format($total_volume5_feb); ?></td>
                            <td><?php echo number_format($total_volume5_mar); ?></td>
                            <td><?php echo number_format($total_volume5_apr); ?></td>
                            <td><?php echo number_format($total_volume5_mei); ?></td>
                            <td><?php echo number_format($total_volume5_jun); ?></td>
                            <td><?php echo number_format($total_volume5_jul); ?></td>
                            <td><?php echo number_format($total_volume5_ags); ?></td>
                            <td><?php echo number_format($total_volume5_sept); ?></td>
                            <td><?php echo number_format($total_volume5_okt); ?></td>
                            <td><?php echo number_format($total_volume5_nov); ?></td>
                            <td><?php echo number_format($total_volume5_des); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="box-body table-responsive">
                <table class="table table-bordered col-sm-top table-isi" id="table-grid">
                    <thead>
                        <tr>

                            <th width="220px" class="text-center" style="background-color:#00008B;color:white;">Source Jakarta</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Jan</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Feb</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Mar</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Apr</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Mei</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Jun</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Jul</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Ags</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Sept</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Okt</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Nov</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Des</th>


                        </tr>

                    </thead>

                    <tbody>

                        <?php

                        $sql1 = " SELECT 
    c.nama_terminal,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '01' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 2
            AND c1.id_master = c.id_master
    ) AS source_januari,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '02' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 2
            AND c1.id_master = c.id_master
    ) AS source_februari,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '03' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 2
            AND c1.id_master = c.id_master
    ) AS source_maret,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '04' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 2
            AND c1.id_master = c.id_master
    ) AS source_april,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '05' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 2
            AND c1.id_master = c.id_master
    ) AS source_mei,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '06' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 2
            AND c1.id_master = c.id_master
    ) AS source_juni,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '07' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 2
            AND c1.id_master = c.id_master
    ) AS source_juli,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '08' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 2
            AND c1.id_master = c.id_master
    ) AS source_agustus,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '09' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 2
            AND c1.id_master = c.id_master
    ) AS source_september,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '10' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 2
            AND c1.id_master = c.id_master
    ) AS source_oktober,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '11' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 2
            AND c1.id_master = c.id_master
    ) AS source_november,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '12' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 2
            AND c1.id_master = c.id_master
    ) AS source_desember
FROM 
    pro_master_terminal c
LEFT JOIN 
    pro_po_ds b ON c.id_master = b.id_terminal
LEFT JOIN 
    pro_po_ds_detail a ON b.id_ds = a.id_ds
LEFT JOIN 
    pro_po_detail d ON a.id_pod = d.id_pod 
WHERE 
    b.id_wilayah = 2
GROUP BY 
    c.nama_terminal
    HAVING 
    source_januari IS NOT NULL OR 
    source_februari IS NOT NULL OR 
    source_maret IS NOT NULL OR 
    source_april IS NOT NULL OR 
    source_mei IS NOT NULL OR 
    source_juni IS NOT NULL OR 
    source_juli IS NOT NULL OR 
    source_agustus IS NOT NULL OR 
    source_september IS NOT NULL OR 
    source_oktober IS NOT NULL OR 
    source_november IS NOT NULL OR 
    source_desember IS NOT NULL;


";

                        // Mendapatkan hasil query
                        $result1 = $con->getResult($sql1);
                        $count = 0;
                        $total_volume6_jan = 0;
                        $total_volume6_feb = 0;
                        $total_volume6_mar = 0;
                        $total_volume6_apr = 0;
                        $total_volume6_mei = 0;
                        $total_volume6_jun = 0;
                        $total_volume6_jul = 0;
                        $total_volume6_ags = 0;
                        $total_volume6_sept = 0;
                        $total_volume6_okt = 0;
                        $total_volume6_nov = 0;
                        $total_volume6_des = 0;




                        foreach ($result1 as $data) {
                            $count++;
                            $total_volume6_jan += $data['source_januari'];
                            $total_volume6_feb += $data['source_februari'];
                            $total_volume6_mar += $data['source_maret'];
                            $total_volume6_apr += $data['source_april'];
                            $total_volume6_mei += $data['source_mei'];
                            $total_volume6_jun += $data['source_juni'];
                            $total_volume6_jul += $data['source_juli'];
                            $total_volume6_ags += $data['source_agustus'];
                            $total_volume6_sept += $data['source_september'];
                            $total_volume6_okt += $data['source_oktober'];
                            $total_volume6_nov += $data['source_november'];
                            $total_volume6_des += $data['source_desember'];


                            echo '<tr>';

                            echo '<td>' . $data['nama_terminal'] . '</td>'; // Menampilkan nama customer
                            echo '<td>' . number_format($data['source_januari']) . '</td>';
                            echo '<td>' . number_format($data['source_februari']) . '</td>';
                            echo '<td>' . number_format($data['source_maret']) . '</td>';
                            echo '<td>' . number_format($data['source_april']) . '</td>';
                            echo '<td>' . number_format($data['source_mei']) . '</td>';
                            echo '<td>' . number_format($data['source_juni']) . '</td>';
                            echo '<td>' . number_format($data['source_juli']) . '</td>';
                            echo '<td>' . number_format($data['source_agustus']) . '</td>';
                            echo '<td>' . number_format($data['source_september']) . '</td>';
                            echo '<td>' . number_format($data['source_oktober']) . '</td>';
                            echo '<td>' . number_format($data['source_november']) . '</td>';
                            echo '<td>' . number_format($data['source_desember']) . '</td>';




                            echo '</tr>';
                        }
                        ?>






                    </tbody>
                    <tfoot>
                        <tr style="font-weight: bold;">
                            <td>Grand Total</td>
                            <td><?php echo number_format($total_volume6_jan); ?></td>
                            <td><?php echo number_format($total_volume6_feb); ?></td>
                            <td><?php echo number_format($total_volume6_mar); ?></td>
                            <td><?php echo number_format($total_volume6_apr); ?></td>
                            <td><?php echo number_format($total_volume6_mei); ?></td>
                            <td><?php echo number_format($total_volume6_jun); ?></td>
                            <td><?php echo number_format($total_volume6_jul); ?></td>
                            <td><?php echo number_format($total_volume6_ags); ?></td>
                            <td><?php echo number_format($total_volume6_sept); ?></td>
                            <td><?php echo number_format($total_volume6_okt); ?></td>
                            <td><?php echo number_format($total_volume6_nov); ?></td>
                            <td><?php echo number_format($total_volume6_des); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>



            <div class="box-body table-responsive">
                <table class="table table-bordered col-sm-top table-isi" id="table-grid">
                    <thead>
                        <tr>

                            <th width="220px" class="text-center" style="background-color:#00008B;color:white;">Source Sulawesi</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Jan</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Feb</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Mar</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Apr</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Mei</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Jun</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Jul</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Ags</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Sept</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Okt</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Nov</th>
                            <th class="text-center" style="background-color:#00008B;color:white;">Des</th>


                        </tr>

                    </thead>

                    <tbody>

                        <?php

                        $sql1 = " SELECT 
    c.nama_terminal,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '01' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 11
            AND c1.id_master = c.id_master
    ) AS source_januari,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '02' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 11
            AND c1.id_master = c.id_master
    ) AS source_februari,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '03' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 11
            AND c1.id_master = c.id_master
    ) AS source_maret,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '04' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 11
            AND c1.id_master = c.id_master
    ) AS source_april,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '05' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 11
            AND c1.id_master = c.id_master
    ) AS source_mei,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '06' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 11
            AND c1.id_master = c.id_master
    ) AS source_juni,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '07' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 11
            AND c1.id_master = c.id_master
    ) AS source_juli,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '08' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 11
            AND c1.id_master = c.id_master
    ) AS source_agustus,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '09' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 11
            AND c1.id_master = c.id_master
    ) AS source_september,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '10' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 11
            AND c1.id_master = c.id_master
    ) AS source_oktober,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '11' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 11
            AND c1.id_master = c.id_master
    ) AS source_november,
    (
        SELECT 
            SUM(d1.volume_po) 
        FROM 
            pro_po_ds_detail a1 
        JOIN 
            pro_po_ds b1 ON a1.id_ds = b1.id_ds
        JOIN 
            pro_master_terminal c1 ON b1.id_terminal = c1.id_master
        JOIN 
            pro_po_detail d1 ON a1.id_pod = d1.id_pod 
        WHERE 
            MONTH(a1.tanggal_loading) = '12' AND YEAR(a1.tanggal_loading) = '2025' AND b1.id_wilayah = 11
            AND c1.id_master = c.id_master
    ) AS source_desember
FROM 
    pro_master_terminal c
LEFT JOIN 
    pro_po_ds b ON c.id_master = b.id_terminal
LEFT JOIN 
    pro_po_ds_detail a ON b.id_ds = a.id_ds
LEFT JOIN 
    pro_po_detail d ON a.id_pod = d.id_pod 
WHERE 
    b.id_wilayah = 11
GROUP BY 
    c.nama_terminal
    HAVING 
    source_januari IS NOT NULL OR 
    source_februari IS NOT NULL OR 
    source_maret IS NOT NULL OR 
    source_april IS NOT NULL OR 
    source_mei IS NOT NULL OR 
    source_juni IS NOT NULL OR 
    source_juli IS NOT NULL OR 
    source_agustus IS NOT NULL OR 
    source_september IS NOT NULL OR 
    source_oktober IS NOT NULL OR 
    source_november IS NOT NULL OR 
    source_desember IS NOT NULL;


";

                        // Mendapatkan hasil query
                        $result1 = $con->getResult($sql1);
                        $count = 0;
                        $total_volume6_jan = 0;
                        $total_volume6_feb = 0;
                        $total_volume6_mar = 0;
                        $total_volume6_apr = 0;
                        $total_volume6_mei = 0;
                        $total_volume6_jun = 0;
                        $total_volume6_jul = 0;
                        $total_volume6_ags = 0;
                        $total_volume6_sept = 0;
                        $total_volume6_okt = 0;
                        $total_volume6_nov = 0;
                        $total_volume6_des = 0;




                        foreach ($result1 as $data) {
                            $count++;
                            $total_volume6_jan += $data['source_januari'];
                            $total_volume6_feb += $data['source_februari'];
                            $total_volume6_mar += $data['source_maret'];
                            $total_volume6_apr += $data['source_april'];
                            $total_volume6_mei += $data['source_mei'];
                            $total_volume6_jun += $data['source_juni'];
                            $total_volume6_jul += $data['source_juli'];
                            $total_volume6_ags += $data['source_agustus'];
                            $total_volume6_sept += $data['source_september'];
                            $total_volume6_okt += $data['source_oktober'];
                            $total_volume6_nov += $data['source_november'];
                            $total_volume6_des += $data['source_desember'];


                            echo '<tr>';

                            echo '<td>' . $data['nama_terminal'] . '</td>'; // Menampilkan nama customer
                            echo '<td>' . number_format($data['source_januari']) . '</td>';
                            echo '<td>' . number_format($data['source_februari']) . '</td>';
                            echo '<td>' . number_format($data['source_maret']) . '</td>';
                            echo '<td>' . number_format($data['source_april']) . '</td>';
                            echo '<td>' . number_format($data['source_mei']) . '</td>';
                            echo '<td>' . number_format($data['source_juni']) . '</td>';
                            echo '<td>' . number_format($data['source_juli']) . '</td>';
                            echo '<td>' . number_format($data['source_agustus']) . '</td>';
                            echo '<td>' . number_format($data['source_september']) . '</td>';
                            echo '<td>' . number_format($data['source_oktober']) . '</td>';
                            echo '<td>' . number_format($data['source_november']) . '</td>';
                            echo '<td>' . number_format($data['source_desember']) . '</td>';




                            echo '</tr>';
                        }
                        ?>






                    </tbody>
                    <tfoot>
                        <tr style="font-weight: bold;">
                            <td>Grand Total</td>
                            <td><?php echo number_format($total_volume6_jan); ?></td>
                            <td><?php echo number_format($total_volume6_feb); ?></td>
                            <td><?php echo number_format($total_volume6_mar); ?></td>
                            <td><?php echo number_format($total_volume6_apr); ?></td>
                            <td><?php echo number_format($total_volume6_mei); ?></td>
                            <td><?php echo number_format($total_volume6_jun); ?></td>
                            <td><?php echo number_format($total_volume6_jul); ?></td>
                            <td><?php echo number_format($total_volume6_ags); ?></td>
                            <td><?php echo number_format($total_volume6_sept); ?></td>
                            <td><?php echo number_format($total_volume6_okt); ?></td>
                            <td><?php echo number_format($total_volume6_nov); ?></td>
                            <td><?php echo number_format($total_volume6_des); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>


        </div>
    </div>
</div>




<style>
    .highcharts-figure,
    .highcharts-data-table table {
        min-width: 310px;
        max-width: auto;
        margin: 1em auto;
    }

    #container {
        height: 400px;
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
</style>




<?php include_once($public_base_directory . "/web/__sc_dashboard_mgr_log.php"); ?>
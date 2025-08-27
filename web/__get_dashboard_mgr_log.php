<?php

ini_set('memory_limit', '256M');
$cabang = isset($_POST["q9"]) ? htmlspecialchars($_POST["q9"], ENT_QUOTES) : '';
$periode = isset($_POST["q10"]) ? htmlspecialchars($_POST["q10"], ENT_QUOTES) : '';
$sd = isset($_POST["q11"]) ? htmlspecialchars($_POST["q11"], ENT_QUOTES) : '';

if (empty($cabang)) {
    $cabang_clause = ""; // Tidak termasuk kriteria cabang dalam kueri SQL
} else {
    $cabang_clause = "and b.id_wilayah = " . $cabang; // Termasuk kriteria cabang dalam kueri SQL
}



$sql = "select 
        a.tanggal_delivered
        AS tanggal,
        SUM(c.volume_po) AS volume_po,
        SUM(a.realisasi_volume) AS jum_vol,
        SUM(c.volume_po) - SUM(a.realisasi_volume) AS losess,
        FORMAT((SUM(c.volume_po - a.realisasi_volume) / SUM(a.realisasi_volume)), 2) AS persen,
        b.id_wilayah
        FROM 
        pro_po_ds_detail a
        JOIN 
        pro_po b ON a.id_po = b.id_po 
        JOIN 
        pro_po_detail c ON a.id_pod = c.id_pod
        WHERE 
        a.tanggal_delivered BETWEEN '" . tgl_db($periode) . "' AND '" . tgl_db($sd) . "'
        $cabang_clause
        GROUP BY 
        a.tanggal_delivered, b.id_wilayah
";

$rows = $con->getResult($sql);


$sql1 = "select
            a.tanggal_delivered AS tanggal,
            SUM(d.volume_po) AS volume_PO,
            SUM(a.realisasi_volume) AS volume_realisasi,
            SUM(d.volume_po) - SUM(a.realisasi_volume) AS losses,
            CASE
                WHEN SUM(a.realisasi_volume) > 0 THEN
                    ROUND(((SUM(d.volume_po) - SUM(a.realisasi_volume)) / SUM(a.realisasi_volume)*100), 2)
                ELSE
                    0.00
            END AS persen_cabang,
            e.id_wilayah,
            f.nama_cabang
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
            JOIN 
            pro_master_cabang f ON e.id_wilayah = f.id_master
            WHERE 
            a.tanggal_delivered >= CURDATE() - INTERVAL 7 DAY
            GROUP BY 
            a.tanggal_delivered, e.id_wilayah, f.nama_cabang;";

$rows1 = $con->getResult($sql1);


// Jika periode atau sd tidak diberikan, gunakan tanggal hari ini
if (empty($periode) || empty($sd)) {
    $periode = date('Y-m-d');
    $sd = date('Y-m-d');
}

$sql2 = "select 
                c1.nama_terminal as terminal,
                SUM(d1.volume_po) AS volume,
                a1.tanggal_loaded as tanggal,
                e1.id_wilayah
                FROM 
                pro_po_ds_detail a1 
                JOIN 
                pro_po_ds b1 ON a1.id_ds = b1.id_ds
                JOIN 
                pro_master_terminal c1 ON b1.id_terminal = c1.id_master
                JOIN 
                pro_po_detail d1 ON a1.id_pod = d1.id_pod 
                JOIN 
                pro_po e1 ON a1.id_po = e1.id_po 
                WHERE 
                a1.tanggal_loaded BETWEEN '" . tgl_db($periode) . "' AND '" . tgl_db($sd) . "'
                GROUP BY 
                e1.id_wilayah, a1.tanggal_loaded;";

$rows2 = $con->getResult($sql2);


$charts = [];
foreach ($rows as $row) {
    // Data untuk chart
    $chartData = [
        'tanggal' => $row['tanggal'],
        'jumlah' => (int)$row['jum_vol'],
        'persen' => $row['persen'],
    ];

    // Tambahkan chartData ke array charts
    $charts[] = $chartData;
}

// Konversi data chart menjadi format JSON
$charts_json = json_encode($charts);


// Mengelompokkan data berdasarkan cabang
$groupedData = [];
foreach ($rows1 as $row1) {
    $cabang = $row1['nama_cabang'];
    $tanggal = strtotime($row1['tanggal']) * 1000; // Ubah tanggal ke format timestamp JavaScript
    $persen = (float) $row1['persen_cabang'];

    if (!isset($groupedData[$cabang])) {
        $groupedData[$cabang] = [];
    }

    $groupedData[$cabang][] = [$tanggal, $persen];
}

// Konversi data yang dikelompokkan menjadi format JSON
$charts_json1 = json_encode($groupedData);




$charts2 = [];
$total_volume = 0;


// Calculate total volume for percentage calculation
foreach ($rows2 as $row2) {
    $total_volume += (int)$row2['volume'];
}

// Prepare data for the chart
foreach ($rows2 as $row2) {
    $chartData2 = [
        'name' => $row2['terminal'],
        'y' => ((int)$row2['volume'] / $total_volume) * 100
    ];

    // Add chartData to array charts
    $charts2[] = $chartData2;
}

// Convert chart data to JSON format
$charts_json2 = json_encode($charts2);

<?php

ini_set('memory_limit', '256M');

$where     = "";
$cabang = isset($_POST["q4"]) ? htmlspecialchars($_POST["q4"], ENT_QUOTES) : '';

// Query untuk kategori terminal 1
$sql1 = "select 
        a.nama_terminal,
        a.tanki_terminal,
        a.lokasi_terminal,
        a.id_cabang,
        a.id_terminal,
        SUM(a.harga_tebus * a.sisa_inven) / SUM(a.sisa_inven) as cogs,
        SUM(a.sisa_inven) as sisa_inven,
        b.batas_atas,
        b.batas_bawah
        FROM 
        vw_terminal_inventory_receive a
        JOIN pro_master_terminal b on a.id_terminal = b.id_master
        WHERE 
        a.sisa_inven > 0
        and b.kategori_terminal = 1
";

if (!empty($cabang)) {
    $where .= " and a.id_cabang = '$cabang'";
}

$sql1 .= $where . " GROUP BY  a.nama_terminal, a.tanki_terminal, a.lokasi_terminal, a.id_cabang, a.id_terminal";

// Query untuk kategori terminal 2
$sql2 = "select 
        a.nama_terminal,
        a.tanki_terminal,
        a.lokasi_terminal,
        a.id_cabang,
        a.id_terminal,
       
        SUM(a.sisa_inven) as sisa_inven,
        b.batas_atas,
        b.batas_bawah
        FROM 
        vw_terminal_inventory a
        JOIN pro_master_terminal b on a.id_terminal = b.id_master
        WHERE 
        a.sisa_inven > 0
        and b.kategori_terminal = 2
";

if (!empty($cabang)) {
    $sql2 .= " and a.id_cabang = '$cabang'";
}

$sql2 .= $where . " GROUP BY  a.nama_terminal, a.tanki_terminal, a.lokasi_terminal, a.id_cabang, a.id_terminal";

// Eksekusi kedua query
$rows1 = $con->getResult($sql1);
$rows2 = $con->getResult($sql2);

// Gabungkan hasil query ke dalam satu array
$rows = array_merge($rows1, $rows2);

// Buat array untuk menyimpan data chart
$charts = [];

foreach ($rows as $row) {
    // Generate unique containerId untuk setiap chart
    $containerId = 'container-oiltank-' . $row['id_terminal'];

    // Data untuk chart
    $chartData = [
        'containerId' => $containerId,
        'oilLevel' => (float)$row['sisa_inven'],
        'title' => $row['nama_terminal'],
        'tankiTerminal' => $row['tanki_terminal'],
        'lokasi' => $row['lokasi_terminal'],
        'cabang' => $row['id_cabang'],
        'cogs' => $row['cogs'],
        'batasAtas' => $row['batas_atas'],
        'batasBawah' => $row['batas_bawah'],
    ];

    // Tambahkan chartData ke array charts
    $charts[] = $chartData;
}

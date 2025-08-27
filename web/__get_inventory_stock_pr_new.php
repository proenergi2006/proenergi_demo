<?php

ini_set('memory_limit', '256M');

$where     = "";
$cabang = isset($_POST["q4"]) ? htmlspecialchars($_POST["q4"], ENT_QUOTES) : '';



$sql = "select a.*, 
        SUM(d.harga_tebus * d.sisa_inven) / SUM(d.sisa_inven) as cogs, e.batas_atas, e.batas_bawah
        from vw_terminal_inventory a
        join new_pro_inventory_depot b ON a.id_terminal = b.id_terminal
        join new_pro_inventory_vendor_po c ON  b.id_po_supplier = c.id_master
        join vw_terminal_inventory_receive d ON a.id_terminal = d.id_terminal
        join pro_master_terminal e ON a.id_terminal = e.id_master
        where d.sisa_inven > 0";

if (!empty($cabang)) {
    // Gunakan operator perbandingan yang sesuai (contoh: '=' atau 'LIKE') tergantung pada jenis data kolom 'id_cabang'
    $where .= " and a.id_cabang = '$cabang'";
}

$sql .= $where . " GROUP BY a.id_terminal";

$rows = $con->getResult($sql);

// Buat array untuk menyimpan data chart
$charts = [];

foreach ($rows as $row) {
    // Generate unique containerId untuk setiap chart
    $containerId = 'container-oiltank-' . $row['id_terminal'];

    // Data untuk chart
    $chartData = [
        'containerId' => $containerId,
        'oilLevel' => (float)$row['sisa_inven'], // Ganti dengan kolom yang sesuai
        'title' => $row['nama_terminal'], // Gunakan nama_terminal sebagai title
        'tankiTerminal' => $row['tanki_terminal'],
        'lokasi' => $row['lokasi_terminal'],
        'cabang' => $row['id_cabang'],
        'cogs' => $row['cogs'],
        'batasAtas' => (float)$row['batas_atas'], // Convert to float
        'batasBawah' => (float)$row['batas_bawah'], // Convert to float
    ];

    // Tambahkan chartData ke array charts
    $charts[] = $chartData;
}

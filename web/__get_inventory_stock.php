<?php

ini_set('memory_limit', '512M');

$where     = "";
$cabang = isset($_POST["q4"]) ? htmlspecialchars($_POST["q4"], ENT_QUOTES) : '';
$periodeAwal = isset($_POST["q5"]) ? htmlspecialchars($_POST["q5"], ENT_QUOTES) : '';
$periodeAkhir = isset($_POST["q6"]) ? htmlspecialchars($_POST["q6"], ENT_QUOTES) : '';


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





$query = "select a.*, 
            b.nama_customer, 
            c.tanggal_kirim,
            c.realisasi,
            d.fullname,
            e.nama_cabang,
            e.id_master,
            b.id_marketing
            from pro_po_customer a 
            join pro_customer b on a.id_customer = b.id_customer 
            join acl_user d on b.id_marketing = d.id_user 
            join pro_master_cabang e on b.id_wilayah = e.id_master 
            left join (select id_poc, sum(realisasi_kirim) as realisasi, tanggal_kirim from pro_po_customer_plan group by id_poc) c on a.id_poc = c.id_poc 
            where poc_approved = 1";

if (!empty($cabang)) {
    $query .= " AND e.id_master = '$cabang'";
}

if (!empty($periodeAwal) && !empty($periodeAkhir)) {
    $query .= " AND a.tanggal_poc BETWEEN'" . tgl_db($periodeAwal) . "' and '" . tgl_db($periodeAkhir) . "'";
}

$query .= " ORDER BY a.volume_poc DESC";

$rows1 = $con->getResult($query);

$marketing = array();

foreach ($rows1 as $r) {
    $key = $r['fullname'];
    if (isset($marketing[$key])) {
        // Jika kunci sudah ada dalam array, tambahkan volume_poc ke nilai yang ada
        $marketing[$key]['volume_poc'] += intval($r['volume_poc']);
    } else {
        // Jika kunci belum ada dalam array, tambahkan data baru
        $marketing[$key] = array(
            'fullname' => $r['fullname'],
            'volume_poc' => intval($r['volume_poc']),
        );
    }
}

$customer = array();

foreach ($rows1 as $r) {
    $key = $r['id_customer']; // Menggunakan id_customer sebagai kunci
    if (isset($customer[$key])) {
        // Jika kunci sudah ada dalam array, tambahkan volume_poc ke nilai yang ada
        $customer[$key]['volume_poc'] += intval($r['volume_poc']);
    } else {
        // Jika kunci belum ada dalam array, tambahkan data baru
        $customer[$key] = array(
            'nama_customer' => $r['nama_customer'],
            'volume_poc' => intval($r['volume_poc']),
        );
    }
}

$marketingRealisasi = array();

foreach ($rows1 as $r) {
    $key = $r['fullname'];
    if (isset($marketingRealisasi[$key])) {
        // Jika kunci sudah ada dalam array, tambahkan volume_poc ke nilai yang ada
        $marketingRealisasi[$key]['realisasi'] += intval($r['realisasi']);
    } else {
        // Jika kunci belum ada dalam array, tambahkan data baru
        $marketingRealisasi[$key] = array(
            'fullname' => $r['fullname'],
            'realisasi' => intval($r['realisasi']),
        );
    }
}

$customerRealisasi = array();

foreach ($rows1 as $r) {
    $key = $r['id_customer']; // Menggunakan id_customer sebagai kunci
    if (isset($customerRealisasi[$key])) {
        // Jika kunci sudah ada dalam array, tambahkan volume_poc ke nilai yang ada
        $customerRealisasi[$key]['realisasi'] += intval($r['realisasi']);
    } else {
        // Jika kunci belum ada dalam array, tambahkan data baru
        $customerRealisasi[$key] = array(
            'nama_customer' => $r['nama_customer'],
            'realisasi' => intval($r['realisasi']),
        );
    }
}


$chartData = array();
foreach ($customer as $r) {
    $chartData[] = array(
        $r['nama_customer'],
        intval($r['volume_poc']),
    );
}

$chartData1 = array_slice($chartData, 0, 10);

$customerDataJSON = json_encode($chartData1);


$chartData2 = array();
foreach ($marketing as $r) {
    $chartData2[] = array(
        $r['fullname'],
        intval($r['volume_poc']),
    );
}

$chartData3 = array_slice($chartData2, 0, 10);

$marketingDataJSON = json_encode($chartData3);

$chartData4 = array();
foreach ($marketingRealisasi as $r) {
    $chartData4[] = array(
        $r['fullname'],
        intval($r['realisasi']),
    );
}

$chartData5 = array_slice($chartData4, 0, 10);

$marketingDataRealisasiJSON = json_encode($chartData5);

$chartData6 = array();
foreach ($customerRealisasi as $r) {
    $chartData6[] = array(
        $r['nama_customer'],
        intval($r['realisasi']),
    );
}

$chartData7 = array_slice($chartData6, 0, 10);

$customerDataRealisasiJSON = json_encode($chartData7);

unset($marketing, $customer, $marketingRealisasi, $customerRealisasi, $chartData, $chartData1, $chartData2, $chartData3, $chartData4, $chartData5, $chartData6, $chartData7);

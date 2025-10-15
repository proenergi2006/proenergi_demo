<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");
header('Content-Type: application/json');

// Ambil parameter dari Select2
$q = isset($_POST['q']) ? trim($_POST['q']) : '';
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$pageSize = 30; // jumlah item per page

$query_params = [
    'fields' => 'id,no,name',
    'filter.itemType.val' => ['INVENTORY', 'NON_INVENTORY', 'SERVICE'],
    'sp.page' => $page,
    'sp.pageSize' => $pageSize
];

if ($q !== '') {
    $query_params['filter.keywords.op'] = 'CONTAIN';
    $query_params['filter.keywords.val'] = $q;
}

$query_string = http_build_query($query_params);

$urlnya = 'https://zeus.accurate.id/accurate/api/item/list.do?' . $query_string;

// 🔌 Kirim ke Accurate API
$result = curl_get($urlnya);

$item_details = [];
$total_count = 0;

if ($result['s'] === true) {
    $total_count = $result['sp']['rowCount'];
    foreach ($result['d'] as $item) {
        $item_details[] = [
            "id" => $item['no'],
            "text" => $item['no'] . " (" . $item['name'] . ")"
        ];
    }
}

echo json_encode([
    "items" => $item_details,
    "total_count" => $total_count,
    "pageSize" => $pageSize
]);

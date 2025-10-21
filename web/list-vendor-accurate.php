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
    'fields' => 'id,vendorNo,name',
    'sp.page' => $page,
    'sp.pageSize' => $pageSize
];

if ($q !== '') {
    $query_params['filter.keywords.op'] = 'CONTAIN';
    $query_params['filter.keywords.val'] = $q;
}

$query_string = http_build_query($query_params);

$vendor_kode = [];


// Make the request for the current page
$url_akun = 'https://zeus.accurate.id/accurate/api/vendor/list.do?' . $query_string;
$result_akun = curl_get($url_akun);

// If the request was successful, process the data
if ($result_akun['s'] == true) {
     $total_count = $result_akun['sp']['rowCount'];
    foreach ($result_akun['d'] as $key) {
        $vendor_kode[] = [
            'id' => $key['vendorNo'],
            "text" =>$key['vendorNo'] . " (" . $key['name']. ")"
        ];
    }
}

echo json_encode([
    "items" => $vendor_kode,
    "total_count" => $total_count,
    "pageSize" => $pageSize
]);

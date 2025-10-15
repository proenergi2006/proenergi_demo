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
    'fields' => 'id,no,nameWithIndent,accountTypeName,noWithIndent,name',
    'sp.page' => $page,
    'sp.pageSize' => $pageSize
];

if ($q !== '') {
    $query_params['filter.keywords.op'] = 'CONTAIN';
    $query_params['filter.keywords.val'] = $q;
}


$query_string = http_build_query($query_params);

// Make the request for the current page
$url_akun = 'https://zeus.accurate.id/accurate/api/glaccount/list.do?' . $query_string;
$result_akun = curl_get($url_akun);


$akun_details = [];
$total_count = 0;
// If the request was successful, process the data
if ($result_akun['s'] == true) {
    foreach ($result_akun['d'] as $key) {
    $total_count = $result_akun['sp']['rowCount'];
        $akun_details[] = [
            "id" => $key['no'] , // kode_barang jadi id
            "text" => html_entity_decode($key['noWithIndent'] .  $key['nameWithIndent'], ENT_QUOTES | ENT_HTML5)
        ];
        
        // $akun_details[] = [
        //     'id' => $key['id'],
        //     'no' => $key['no'],
        //     'name' => $key['name'],
        //     'accountTypeName' => $key['accountTypeName'],
        //     'nameWithIndent' => $key['nameWithIndent'],
        //     'noWithIndent' => $key['noWithIndent']
        // ];
    }
}


// Output JSON sesuai Select2
echo json_encode([
    "items" => $akun_details,
    "total_count" => $total_count,
    "pageSize" => $pageSize
]);

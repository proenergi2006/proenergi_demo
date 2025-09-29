<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth  = new MyOtentikasi();
$con   = new Connection();

$id_customer = (int)($_POST['id_customer'] ?? 0);
$from = $_POST['from'] ?? '';
$to   = $_POST['to'] ?? '';

$whereTo = empty($to)
  ? "p.created_time >= '" . addslashes($from) . "'"
  : "p.created_time >= '" . addslashes($from) . "' AND p.created_time < '" . addslashes($to) . "'";

$sql = "
SELECT p.*
FROM pro_penawaran p
WHERE p.id_customer = '{$id_customer}' AND $whereTo
ORDER BY p.created_time DESC, p.id_penawaran DESC
";
$result = $con->getResult($sql);

foreach ($result as &$r) {
  // sesuaikan nama kolom: id_customer & id_poc / id_penawaran
  $qid = 'idr=' . $r['id_customer'] . '&idk=' . $r['id_penawaran'];        // untuk PO
  // $qid = 'idr=' . $r['id_customer'] . '&idk=' . $r['id_penawaran']; // untuk Penawaran

  $r['link_detail_penawaran'] = BASE_URL_CLIENT . '/penawaran-detail.php?' . paramEncrypt($qid);
}
unset($r);

$data = [
  "data" => $result
];

echo json_encode($data);

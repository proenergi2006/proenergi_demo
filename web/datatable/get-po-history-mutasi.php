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
  ? "po.created_time >= '" . addslashes($from) . "'"
  : "po.created_time >= '" . addslashes($from) . "' AND po.created_time < '" . addslashes($to) . "'";

$sql = "
SELECT po.*, po.nomor_poc as nomor_surat
FROM pro_po_customer po
WHERE po.id_customer = '{$id_customer}' AND $whereTo
ORDER BY po.created_time DESC, po.id_poc DESC
";
$result = $con->getResult($sql);

foreach ($result as &$r) {
  // sesuaikan nama kolom: id_customer & id_poc / id_penawaran
  $qid = 'idr=' . $r['id_customer'] . '&idk=' . $r['id_poc'];        // untuk PO
  // $qid = 'idr=' . $r['id_customer'] . '&idk=' . $r['id_penawaran']; // untuk Penawaran

  $r['link_detail'] = BASE_URL_CLIENT . '/po-customer-detail.php?' . paramEncrypt($qid);
}
unset($r);

$data = [
  "data" => $result
];

echo json_encode($data);

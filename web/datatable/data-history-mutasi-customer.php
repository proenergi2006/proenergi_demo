<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$con 	= new Connection();

$id_lama	= isset($_POST["id_lama"]) ? htmlspecialchars($_POST["id_lama"], ENT_QUOTES) : '';
$id_cust	= isset($_POST["id_customer"]) ? htmlspecialchars($_POST["id_customer"], ENT_QUOTES) : '';

$sql = "SELECT a.nama_customer, a.kode_pelanggan, b.fullname FROM pro_customer a JOIN acl_user b ON a.id_marketing=b.id_user WHERE a.id_customer = '" . $id_cust . "'";
$result = $con->getRecord($sql);

$sql2 = "SELECT
  h.id,
  h.id_customer,
  h.id_marketing,
  h.reason,
  h.mutasi_by,
  m.fullname,
  h.effective_from,
  h.effective_to,
CONCAT(DAY(h.effective_from), ' ',
         ELT(MONTH(h.effective_from),
             'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'
         ),
         ' ', YEAR(h.effective_from)
  ) AS effective_from_fmt,
  IFNULL(
    CONCAT(DAY(h.effective_to), ' ',
           ELT(MONTH(h.effective_to),
               'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'
           ),
           ' ', YEAR(h.effective_to)
    ),
    'Sekarang'
  ) AS effective_to_fmt,
  (h.effective_to IS NULL) AS is_current,
  (
    SELECT COUNT(*)
    FROM pro_penawaran p
    WHERE p.id_customer = h.id_customer
      AND p.created_time >= h.effective_from
      AND (h.effective_to IS NULL OR p.created_time < h.effective_to)
  ) AS total_penawaran,
  (
    SELECT COUNT(*)
    FROM pro_po_customer po
    WHERE po.id_customer = h.id_customer
      AND po.created_time >= h.effective_from
      AND (h.effective_to IS NULL OR po.created_time < h.effective_to)
  ) AS total_po

FROM pro_customer_marketing_history h
LEFT JOIN acl_user m ON m.id_user = h.id_marketing
WHERE h.id_customer = '" . $id_cust . "'
ORDER BY h.effective_from DESC";
$result2 = $con->getResult($sql2);

$kode_pelanggan = $result['kode_pelanggan'] ? $result['kode_pelanggan'] : "N/A";

$data = [
	"nama_customer" => $result['nama_customer'] . " | " . $kode_pelanggan,
	"marketing_existing" => strtoupper($result['fullname']),
	"data" => $result2
];

echo json_encode($data);

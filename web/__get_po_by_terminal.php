<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$con 	= new Connection();

$searchKeyword = isset($_POST['q1']) ? $_POST['q1'] : '';

$sql = "SELECT * FROM vw_terminal_inventory_receive WHERE sisa_inven > 0";

// kondisi WHERE berdasarkan kata kunci pencarian
if (!empty($searchKeyword)) {
	$sql .= " AND (nama_terminal LIKE '%" . $searchKeyword . "%' OR nomor_po_supplier LIKE '%" . $searchKeyword . "%')";
}
$tot_record = $con->num_rows($sql);

if ($tot_record > 0) {
	$result = $con->getResult($sql);
}
echo json_encode($result);

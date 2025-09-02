<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$con 	= new Connection();

$kode_ri = isset($_POST["receive_number"]) ? htmlspecialchars($_POST["receive_number"], ENT_QUOTES) : 0;
$array_id =explode(',',$kode_ri);
$id_accurate =$array_id[0];

$sqlnya = "SELECT * FROM new_pro_inventory_vendor_po_receive a 
		  JOIN new_pro_inventory_vendor_po b ON a.`id_po_supplier`=b.`id_master`
		  JOIN pro_master_terminal c ON b.`id_terminal`=c.`id_master`
		  JOIN pro_master_cabang d ON c.`id_cabang`=d.`id_master`
		  WHERE a.`id_accurate` = $id_accurate";

$total = $con->getRecord($sqlnya);

echo json_encode(["status" => true, "data" => $total]);


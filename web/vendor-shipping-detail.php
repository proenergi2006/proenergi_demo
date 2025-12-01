<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$conSub = new Connection();
$id_vessel		= htmlspecialchars($_POST["id_vessel"], ENT_QUOTES);


if (isset($_POST['id_vessel'])) {
    $sql01 = "
                select *
                from pro_master_oa_kapal 
                where id_master = '".$id_vessel."'
            ";
    $res01 = $conSub->getRecord($sql01);

    echo json_encode(["status" => true, "data" => $res01]);
} else {
    echo json_encode(["status" => false, "message" => "data tidak ditemukan"]);
}

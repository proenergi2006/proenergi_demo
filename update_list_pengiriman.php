<?php
ini_set('memory_limit', '256M');
// set_time_limit(500);
ini_set('max_execution_time', 1200);
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "htmlawed", "mailgen");

$con = new Connection();

$oke = true;
$con->beginTransaction();
$con->clearError();

include_once($public_base_directory . "/sc_update_pengiriman_list.php");

include_once($public_base_directory . "/foot_sc_update_list_pengiriman.php");

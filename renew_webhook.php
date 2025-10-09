<?php
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$urlnya = 'https://account.accurate.id/api/webhook-renew.do';

$result = curl_get($urlnya);

echo json_encode($result);

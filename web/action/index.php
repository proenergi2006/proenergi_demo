<?php 
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
require_once ($public_base_directory."/libraries/helper/url.php");

header("location: ".BASE_URL);
exit();

?>
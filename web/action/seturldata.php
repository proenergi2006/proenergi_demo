<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$hasil = "display=1";
	if(count($_POST) > 0){
		foreach($_POST as $idx=>$data){
			$hasil .= "&".$idx."=".htmlspecialchars($data, ENT_QUOTES);
		}
	}	
	echo paramEncrypt($hasil);
?>

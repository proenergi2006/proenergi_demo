<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$conSub = new Connection();
	$q1 = htmlspecialchars($_POST["q1"], ENT_QUOTES);
	$q2 = htmlspecialchars($_POST["q2"], ENT_QUOTES);
	$q3 = htmlspecialchars($_POST["q3"], ENT_QUOTES);

	$sql = "select a.ongkos_angkut from pro_master_ongkos_angkut a join pro_master_volume_angkut b on a.id_vol_angkut = b.id_master 
			where a.id_transportir = '".$q1."' and a.id_wil_angkut = '".$q2."' and b.volume_angkut = '".$q3."'";
			
	$res = $conSub->getOne($sql);
	$conSub->close();
    echo $res;
?>

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
	$q4 = htmlspecialchars($_POST["q4"], ENT_QUOTES);
	$q5 = htmlspecialchars($_POST["q5"], ENT_QUOTES);
	$q6 = htmlspecialchars($_POST["q6"], ENT_QUOTES);

	$sql = "
		select harga_tebus 
		from pro_master_harga_tebus
		where 1=1 
			and periode_awal = (
				select max(periode_awal) as periode_awal 
				from pro_master_harga_tebus 
				where 1=1 
					and id_produk = '".$q3."' 
					and id_vendor = '".$q5."' 
					and id_terminal = '".$q6."' 
			)
			and id_produk = '".$q3."' 
			and id_vendor = '".$q5."' 
			and id_terminal = '".$q6."' 
	";
	/*
	select harga_tebus 
	from pro_master_harga_tebus 
	where
		periode_awal = '" . $q1 . "' and 
		periode_akhir = '" . $q2 . "' and 
		id_produk = '" . $q3 . "' and 
		id_area = '" . $q4 . "' and 
		id_vendor = '" . $q5 . "' and 
		id_terminal = '" . $q6 . "'";*/

	$res = $conSub->getOne($sql);
	$conSub->close();
    echo $res;
?>

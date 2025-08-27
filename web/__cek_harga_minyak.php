<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$conSub = new Connection();
	$q1 	= htmlspecialchars($_POST["q1"], ENT_QUOTES);
	$q2 	= htmlspecialchars($_POST["q2"], ENT_QUOTES);
	$q3 	= htmlspecialchars($_POST["q3"], ENT_QUOTES);
	$q4 	= htmlspecialchars($_POST["q4"], ENT_QUOTES);
	$q5 	= htmlspecialchars($_POST["q5"], ENT_QUOTES);

	$sql = "
		select * from pro_master_harga_minyak 
		where periode_awal = '".tgl_db($q1)."' and periode_akhir = '".tgl_db($q2)."' and id_area = '".$q3."' and pajak = '".$q4."' and produk = '".$q5."' and is_approved = 1
	";
	$rsm = $conSub->getRecord($sql);

	if(!$rsm["harga_normal"]){
		$sql = "
			select * from pro_master_harga_minyak 
			where periode_awal = '".tgl_db($q1)."' and periode_akhir = '".tgl_db($q2)."' and id_area = '".$q3."' and pajak = '1' and produk = '".$q5."' and is_approved = 1
		";
		$rsm = $conSub->getRecord($sql);
	}

    $answer	= array();
	if($rsm and $rsm["harga_normal"]){
		$answer["harga"] = $rsm['harga_normal'];
		$answer["items"] = "Harga Dasar Minyak Rp. ".number_format($rsm['harga_normal'],0);
		$answer["error"] = "";
    } else{
		$answer["harga"] = 0;
		$answer["items"] = "";
		$answer["error"] = "Maaf, Harga Dasar Minyak belum tersedia atau belum disetujui CEO";
	}
    echo json_encode($answer);
	$conSub->close();
?>

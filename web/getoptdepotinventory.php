<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$conSub = new Connection();
	$id_jenis 	= htmlspecialchars($_POST["id_jenis"], ENT_QUOTES);
	$id_produk 	= htmlspecialchars($_POST["id_produk"], ENT_QUOTES);

	if($id_jenis == '1'){
		$sql = "
			select distinct a.id_master, concat(a.nama_terminal, ' ', a.tanki_terminal, ', ', a.lokasi_terminal) as nama_terminal, b.id_terminal  
			from pro_master_terminal a 
			join pro_master_cabang a1 on a.id_cabang = a1.id_master
			left join new_pro_inventory_depot b on a.id_master = b.id_terminal and b.id_jenis = 1 and b.id_produk = '".$id_produk."'  
			where a.is_active = 1 and b.id_terminal is null 
			order by a.id_master 
		";
		$res = $conSub->getResult($sql);

		$conSub->close();
		echo json_encode($res);
	} else{
		$sql = "
			select distinct a.id_master, concat(a.nama_terminal, ' ', a.tanki_terminal, ', ', a.lokasi_terminal) as nama_terminal, b.id_terminal  
			from pro_master_terminal a 
			join pro_master_cabang a1 on a.id_cabang = a1.id_master
			left join new_pro_inventory_depot b on a.id_master = b.id_terminal and b.id_jenis = 1 and b.id_produk = '".$id_produk."'  
			where a.is_active = 1 and b.id_terminal is not null 
			order by a.id_master 
		";
		$res = $conSub->getResult($sql);

		$conSub->close();
		echo json_encode($res);
	}
?>

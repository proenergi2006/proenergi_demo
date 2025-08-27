<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$conSub = new Connection();
	$flash	= new FlashAlerts;

	$sesuser 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);
	$sesrole 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
    $seswil 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
    $sesgroup 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']);

	$prm = htmlspecialchars($_POST["prm"], ENT_QUOTES);	
	$tmp = explode("#", paramDecrypt($prm));
	$idk = $tmp[0];
	$arr = array();
	
	$sql = "
		select 
		a.id_peminjaman, a.id_mobil, b.id_cabang, d.nama_cabang, concat(b.nama_mobil, ' ', b.plat_mobil) as nama_mobil, 
		a.id_user, c.fullname, a.tanggal_peminjaman, 
		a.start_jam_peminjaman as jam_mulai, 
		a.end_jam_peminjaman as jam_selesai, 
		a.keperluan, a.last_km, a.bensin    
		from pro_peminjaman_mobil a 
		join pro_master_mobil b on a.id_mobil = b.id_mobil 
		join pro_master_cabang d on b.id_cabang = d.id_master 
		left join acl_user c on a.id_user = c.id_user 
		where 1=1 and a.id_peminjaman = '".$idk."' 
	";
	$row = $conSub->getRecord($sql);
	$conSub->close(); $conSub = NULL;

	$arr["nama_mobil"] 			= $row['nama_mobil'];
	$arr["tanggal_reservasi"] 	= date("d/m/Y", strtotime($row['tanggal_peminjaman']));
	$arr["keperluan"] 			= $row['keperluan'];
	$arr["jam_mulai"] 			= $row['jam_mulai'];
	$arr["jam_selesai"] 		= $row['jam_selesai'];
	$arr["id_peminjaman"] 		= $row['id_peminjaman'];
	$arr["id_mobil"] 			= $row['id_mobil'];
	$arr["id_cabang"] 			= $row['id_cabang'];
	$arr["last_km"] 			= ($row["last_km"] > 0 ? $row["last_km"] : '');
	$arr["bensin"] 				= ($row["bensin"] > 0 ? $row["bensin"] : '');

	echo json_encode($arr);
?>

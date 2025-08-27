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
		a.id_peminjaman, a.id_zoom, b.id_cabang, d.nama_cabang, b.nama_zoom, 
		a.id_user, c.fullname, a.tanggal_peminjaman, 
		a.start_jam_peminjaman as jam_mulai, 
		a.end_jam_peminjaman as jam_selesai, 
		a.keperluan, a.departmen 
		from pro_peminjaman_zoom a 
		join pro_master_zoom b on a.id_zoom = b.id_zoom 
		join pro_master_cabang d on b.id_cabang = d.id_master 
		left join acl_user c on a.id_user = c.id_user 
		where 1=1 and a.id_peminjaman = '".$idk."' 
	";
	$row = $conSub->getRecord($sql);
	$conSub->close(); $conSub = NULL;

	$arr["nama_zoom"] 			= $row['nama_zoom'];
	$arr["tanggal_reservasi"] 	= date("d/m/Y", strtotime($row['tanggal_peminjaman']));
	$arr["keperluan"] 			= $row['keperluan'];
    $arr["departmen"] 			= $row['departmen'];
	$arr["jam_mulai"] 			= $row['jam_mulai'];
	$arr["jam_selesai"] 		= $row['jam_selesai'];
	$arr["id_peminjaman"] 		= $row['id_peminjaman'];
	$arr["id_zoom"] 			= $row['id_zoom'];
	$arr["id_cabang"] 			= $row['id_cabang'];
	

	echo json_encode($arr);
?>

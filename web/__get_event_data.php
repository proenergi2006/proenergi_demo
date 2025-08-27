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
		a.id_reservasi, a.id_ruangan, b.id_cabang, d.nama_cabang, b.nama_ruangan, a.id_user, c.fullname, 
		a.tanggal_reservasi, a.jam_reservasi, 
		cast(substring(a.jam_reservasi, 1, 5) as time) as jam_mulai, 
		cast(substring(a.jam_reservasi, 7, 5) as time) as jam_selesai, 
		a.personel, a.keperluan  
		from pro_reservasi_ruangan a 
		join pro_master_ruangan b on a.id_ruangan = b.id_ruangan 
		join pro_master_cabang d on b.id_cabang = d.id_master 
		left join acl_user c on a.id_user = c.id_user 
		where 1=1 and a.id_reservasi = '".$idk."' 
	";
	$row = $conSub->getRecord($sql);
	$conSub->close(); $conSub = NULL;

	$arr["nama_ruangan"] 		= $row['nama_ruangan'];
	$arr["tanggal_reservasi"] 	= date("d/m/Y", strtotime($row['tanggal_reservasi']));
	$arr["personel"] 			= $row['personel'];
	$arr["keperluan"] 			= $row['keperluan'];
	$arr["jam_mulai"] 			= $row['jam_mulai'];
	$arr["jam_selesai"] 		= $row['jam_selesai'];
	$arr["id_reservasi"] 		= $row['id_reservasi'];
	$arr["id_ruangan"] 			= $row['id_ruangan'];

	echo json_encode($arr);
?>

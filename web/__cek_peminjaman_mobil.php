<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$conSub = new Connection();

	$idr 		= htmlspecialchars($_POST["idr"], ENT_QUOTES);
	$id_mobil 	= htmlspecialchars($_POST["id_mobil"], ENT_QUOTES);
	$tanggal_peminjaman 	= htmlspecialchars($_POST["tanggal_peminjaman"], ENT_QUOTES);
	$start_jam_peminjaman 	= htmlspecialchars($_POST["start_jam_peminjaman"], ENT_QUOTES);
	$end_jam_peminjaman 	= htmlspecialchars($_POST["end_jam_peminjaman"], ENT_QUOTES);

	$sql = "
		select b.fullname as nama_user, id_peminjaman 
		from pro_peminjaman_mobil a 
		join acl_user b on a.id_user = b.id_user 
		where 1=1 and id_mobil = '".$id_mobil."'
			and ('".tgl_db($tanggal_peminjaman)." ".date('H:i', strtotime($start_jam_peminjaman))."' between cast(concat(tanggal_peminjaman, ' ', start_jam_peminjaman) as datetime) 
				and cast(concat(tanggal_peminjaman, ' ', end_jam_peminjaman) as datetime) 
			or '".tgl_db($tanggal_peminjaman)." ".date('H:i', strtotime($end_jam_peminjaman))."' between cast(concat(tanggal_peminjaman, ' ', start_jam_peminjaman) as datetime) 
				and cast(concat(tanggal_peminjaman, ' ', end_jam_peminjaman) as datetime))

	";
	
	if($idr){
		$sql .= " and id_peminjaman != ".$idr."";
	}
	$rsm = $conSub->getRecord($sql);

	if($rsm['id_peminjaman']){
		$answer["success"] = false;
		$answer["pesan"]  = "Maaf, pada waktu tersebut mobil telah dibooking oleh ".$rsm['nama_user']."";
    } else{
		$answer["success"] = true;
		$answer["pesan"]  = "";
	}
    echo json_encode($answer);
	$conSub->close();
?>

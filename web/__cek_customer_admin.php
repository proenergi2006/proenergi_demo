<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$conSub = new Connection();
	$valid	= true;
	$answer	= array();
	
	$username	= htmlspecialchars($_POST["username"], ENT_QUOTES);	
	$telepon	= htmlspecialchars($_POST["telp"], ENT_QUOTES);
	$email		= htmlspecialchars($_POST["email"], ENT_QUOTES);
	$idr 		= htmlspecialchars($_POST["idr"], ENT_QUOTES);	
	$cek_user 	= $conSub->getOne("select username from acl_user where username = '".$username."'");

	if($username == "" || $telepon == "" || $email == "" || $idr == ""){
		$valid = false;
		$pesan = "Data dalam form harus diisi semua";
	} else if($email != "" && !filter_var($email, FILTER_VALIDATE_EMAIL)){
		$valid = false;
		$pesan = "Penulisan email tidak benar";
	} else if($cek_user != ""){
		$valid = false;
		$pesan = "Username telah dipakai, silahkan gunakan username yang lain";
	}
	$answer["error"] = ($valid)?"":$pesan;
    echo json_encode($answer);
	$conSub->close();
?>

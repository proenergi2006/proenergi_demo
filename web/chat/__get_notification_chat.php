<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$conSub = new Connection();
	$id_user = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);
	$response	= '';

	// Get Notif
	$sql = "
	SELECT count(id_chat) as total
	FROM pro_chat 
	WHERE 
	    1=1
	    AND id_receiver = '$id_user'
	    AND is_notif = 1
	    AND deleted_time IS NULL
    ";
	$rsm = $conSub->getRecord($sql);
	$response = $rsm['total'];
    echo $response;
	$conSub->close();
?>

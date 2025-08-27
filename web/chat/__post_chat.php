<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	// $flash	= new FlashAlerts;

	$id_sender = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);
	$id_receiver = $_POST['chat_to'];
	$message = $_POST['message'];

	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	$sql1 = "
		insert into pro_chat(
			id_sender, 
			id_receiver,
			message,
			created_time,
			is_notif,
			is_read
		) values (
			'".$id_sender."', 
			'".$id_receiver."',  
			'".$message."',  
			NOW(),
			1,
			1
		)
	";
	$res1 = $con->setQuery($sql1);
	$oke  = $oke && !$con->hasError();
	$url = BASE_URL_CLIENT."/chat.php";
	$msg = "Data behasil disimpan";

	if ($oke){
		$con->commit();
		$con->close();
		// $flash->add("success", $msg, $url);
	} else{
		$con->rollBack();
		$con->clearError();
		$con->close();
		// $flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}

	if ($oke){
	    $output = 1;
	}else{
	    $output = 0;
	}

	echo $output;

?>
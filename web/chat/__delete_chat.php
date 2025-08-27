<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();

	$id_sender = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);
	$id_receiver = $_POST['chat_to'];

	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	$sql1 = "
		update pro_chat set 
			deleted_time = NOW()
		where
			id_receiver = ".$id_receiver."
			and id_sender = ".$id_sender."
	";
	$res1 = $con->setQuery($sql1);
	$oke  = $oke && !$con->hasError();

	$sql1 = "
		update pro_chat set 
			deleted_time = NOW()
		where
			id_sender = ".$id_receiver."
			and id_receiver = ".$id_sender."
	";
	$res1 = $con->setQuery($sql1);
	$oke  = $oke && !$con->hasError();

	if ($oke){
		$con->commit();
		$con->close();
	} else{
		$con->rollBack();
		$con->clearError();
		$con->close();
	}

	if ($oke){
	    $output = 1;
	}else{
	    $output = 0;
	}

	echo $output;

?>
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
	$dt1 	= htmlspecialchars($_POST["username"], ENT_QUOTES);

	$sql = "select * from acl_user where username = '".$dt1."' and id_user <> '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."'";
	$rsm = $conSub->getResult($sql);
	if(count($rsm) > 0){
		$valid = false;
		$pesan = "Username \"".$dt1."\" sudah ada...";
	}
	$answer["error"] = ($valid)?"":$pesan;
    echo json_encode($answer);
	$conSub->close();
?>

<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$conSub = new Connection();
	$response	= array();
	$keyword = $_POST['keyword'];

	$sql = "
		select a.*, b.role_name
		from acl_user a 
		join acl_role b on b.id_role = a.id_role
		where a.id_user <> '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."'
		and a.fullname LIKE '%$keyword%'
	";
	$rsm = $conSub->getResult($sql);
	if(count($rsm) > 0){
		foreach ($rsm as $key => $value) {
			$response[$key]['id_user'] = $value['id_user'];
			$response[$key]['fullname'] = $value['fullname'];
			$response[$key]['role_name'] = str_replace('Role ', '', $value['role_name']);
		}
	}
    echo json_encode($response);
	$conSub->close();
?>

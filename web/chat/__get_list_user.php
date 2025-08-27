<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$conSub = new Connection();
	$response	= array();
	$paramx = array();
	$id_user = paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"]);

	// $sql = "
	// 	select a.*, b.role_name
	// 	from acl_user a 
	// 	join acl_role b on b.id_role = a.id_role
	// 	where a.id_user <> '".$id_user."'
	// ";
	$i = 0;
	// $sql = "
	// 	select b.id_user, b.fullname, c.role_name, a.created_time, a.is_notif, a.is_read, a.id_receiver
	// 	from pro_chat a 
	// 	join acl_user b on b.id_user = a.id_receiver
	// 	join acl_role c on c.id_role = b.id_role
	// 	where (a.id_sender = '$id_user' or a.id_receiver = '$id_user') and is_read = 1
	// 	order by a.created_time
	// ";
	$sql = "
	select z.* from (
	select b.id_user, b.fullname, c.role_name, a.created_time, a.is_notif, a.is_read, a.id_receiver
	from pro_chat a 
	join acl_user b on b.id_user = a.id_sender
	join acl_role c on c.id_role = b.id_role
	where (a.id_receiver = '$id_user') and is_read = 1 and deleted_time is null
	UNION
	select b.id_user, b.fullname, c.role_name, a.created_time, a.is_notif, a.is_read, a.id_receiver
	from pro_chat a 
	join acl_user b on b.id_user = a.id_receiver
	join acl_role c on c.id_role = b.id_role
	where (a.id_sender = '$id_user') and is_read = 1 and deleted_time is null
	) z
	order by z.created_time
	";
	$rsm = $conSub->getResult($sql);
	if($rsm){
		foreach ($rsm as $value) {
			if (!in_array($value['id_user'], $paramx)) {
				$paramx[] = $value['id_user'];
				$response[$i]['is_notif'] = $value['id_receiver']==$id_user?$value['is_notif']:0;
				$response[$i]['is_read'] = $value['id_receiver']==$id_user?$value['is_read']:0;
				$response[$i]['id_user'] = $value['id_user'];
				$response[$i]['fullname'] = $value['fullname'];
				$response[$i]['role_name'] = str_replace('Role ', '', $value['role_name']);
				$i ++;
			}
		}
	}
	// $sql = "
	// 	select b.id_user, b.fullname, c.role_name, a.created_time, a.is_notif, a.is_read
	// 	from pro_chat a 
	// 	join acl_user b  on b.id_user = a.id_sender
	// 	join acl_role c on c.id_role = b.id_role
	// 	where (a.id_sender = '$id_user' or a.id_receiver = '$id_user') and is_read = 0
	// 	order by a.created_time
	// ";
	$sql = "
	select z.* from (
	select b.id_user, b.fullname, c.role_name, a.created_time, a.is_notif, a.is_read, a.id_receiver
	from pro_chat a 
	join acl_user b on b.id_user = a.id_sender
	join acl_role c on c.id_role = b.id_role
	where (a.id_receiver = '$id_user') and is_read = 0 and deleted_time is null
	UNION
	select b.id_user, b.fullname, c.role_name, a.created_time, a.is_notif, a.is_read, a.id_receiver
	from pro_chat a 
	join acl_user b on b.id_user = a.id_receiver
	join acl_role c on c.id_role = b.id_role
	where (a.id_sender = '$id_user') and is_read = 0 and deleted_time is null
	) z
	order by z.created_time
	";
	$rsm = $conSub->getResult($sql);
	if($rsm){
		foreach ($rsm as $value) {
			if (!in_array($value['id_user'], $paramx)) {
				$paramx[] = $value['id_user'];
				$response[$i]['is_notif'] = $value['is_notif'];
				$response[$i]['is_read'] = $value['is_read'];
				$response[$i]['id_user'] = $value['id_user'];
				$response[$i]['fullname'] = $value['fullname'];
				$response[$i]['role_name'] = str_replace('Role ', '', $value['role_name']);
				$i ++;
			}
		}
	}
    echo json_encode($response);
	$conSub->close();
?>

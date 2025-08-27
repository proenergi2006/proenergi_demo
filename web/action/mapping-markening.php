<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload", "htmlawed");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$act	= ($enk['act'] == "")?htmlspecialchars($_POST["act"], ENT_QUOTES):$enk['act'];
	$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);	
	
	$jumlah 	= htmlspecialchars($_POST["jumlah"], ENT_QUOTES);	
	$tanggal 	= htmlspecialchars($_POST["tanggal"], ENT_QUOTES);	
	$nomor1 	= htmlspecialchars($_POST["nomor_akhir"], ENT_QUOTES);	
	$nomor2 	= htmlspecialchars($_POST["nomor_stock"], ENT_QUOTES);	
	$nomor 		= htmlspecialchars($_POST["nomor_acara"], ENT_QUOTES);	
	$kategori 	= htmlspecialchars($_POST["kategori"], ENT_QUOTES);	
	$keperluan 	= htmLawed($_POST["keperluan"], array('safe'=>1));
	$wilayah	= paramDecrypt($_SESSION["sinori".SESSIONID]["id_wilayah"]);
	$pic		= paramDecrypt($_SESSION["sinori".SESSIONID]["fullname"]);
	$spv		= paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"]);
	$id_user = join("','",$_POST['fullname']);


	

	if (array_search("", $_POST['fullname']) !== false){
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else{
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

			$query="insert into pro_mapping_marketing (id_user, id_member, full_name, created_date, created_ip, created_by) select '".$spv."', id_user, fullname,NOW(),'".$_SERVER['REMOTE_ADDR']."','".$pic."' from acl_user where id_wilayah=2 and is_active=1 and id_role=11 and id_user in('".$id_user."')";
			$con->setQuery($query);
			$oke  = $oke && !$con->hasError();
			$url  = BASE_URL_CLIENT."/mapping-marketing.php";


			
			if ($oke){
				$con->commit();
				$con->close();
				header("location: ".$url);	
				exit();
			} else{
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
			}
		
	}
?>
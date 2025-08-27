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
	$act	= !isset($enk['act'])?htmlspecialchars($_POST["act"], ENT_QUOTES):$enk['act'];
	$id1	= htmlspecialchars($_POST["id1"], ENT_QUOTES);

	if(!is_array_empty($_POST['id_mkt']) || !is_array_empty($_POST['id_spv'])){
		$no_urut = 0;

		$oke = true;
		$con->beginTransaction();
		$con->clearError();
	
		$sql1 = "delete from pro_mapping_spv where 1=1";
		$res1 = $con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();

		foreach($_POST['id_mkt'] as $idx=>$val){
			$no_urut++;

			$id_spv 	= htmlspecialchars($_POST['id_spv'][$idx], ENT_QUOTES);
			$id_mkt 	= htmlspecialchars($_POST['id_mkt'][$idx], ENT_QUOTES);
			
			$sql2 = "insert into pro_mapping_spv(no_urut, id_spv, id_mkt) values ('".$no_urut."', '".$id_spv."', '".$id_mkt."')";
			$res2 = $con->setQuery($sql2);
			$oke  = $oke && !$con->hasError();
		}

		$url = BASE_URL_CLIENT."/mapping-spv-mkt.php";
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
	} else{
		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}

?>

<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$act	= ($enk['act'] == "")?htmlspecialchars($_POST["act"], ENT_QUOTES):$enk['act'];
	$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
	$picnya = paramDecrypt($_SESSION["sinori".SESSIONID]["fullname"]);
	$ipnya 	= $_SERVER['REMOTE_ADDR'];
	
	$tgl	= htmlspecialchars($_POST["tgl"], ENT_QUOTES);	
	$vendor = htmlspecialchars($_POST["vendor"], ENT_QUOTES);	
	$produk = htmlspecialchars($_POST["produk"], ENT_QUOTES);	
	$area 	= htmlspecialchars($_POST["area"], ENT_QUOTES);	
	$depot 	= htmlspecialchars($_POST["terminal"], ENT_QUOTES);	
	$awal 	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["awal"]), ENT_QUOTES);	
	$adji 	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["adji"]), ENT_QUOTES);	

	if($tgl == "" || $vendor == "" || $produk == "" || $area == "" || $depot == "" || $awal == ""){
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else{
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		$cek1 = "select id_master from pro_inventory_vendor where tanggal_inven = '".tgl_db($tgl)."' and id_vendor = '".$vendor."' and id_produk = '".$produk."' 
				 and id_area = '".$area."' and id_terminal = '".$depot."'";
		$ada1 = $con->getOne($cek1);

		if($ada1){
			$sql1 = "update pro_inventory_vendor set awal_inven = '".$awal."', adj_inven = '".$adji."', lastupdate_time = NOW(), lastupdate_ip = '".$ipnya."', 
					 lastupdate_by = '".$picnya."' where id_master = '".$ada1."'";
			$con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();
		} else{
			$sql1 = "insert into pro_inventory_vendor(id_vendor, id_produk, id_area, id_terminal, tanggal_inven, awal_inven, adj_inven, created_time, created_ip, created_by) 
					 values ('".$vendor."', '".$produk."', '".$area."', '".$depot."', '".tgl_db($tgl)."', '".$awal."', '".$adji."', NOW(), '".$_SERVER['REMOTE_ADDR']."', 
					 '".$pic."')";
			$con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();
		}

		if($oke){
			$con->commit();
			$con->close();
			header("location: ".BASE_URL_CLIENT."/vendor-inven.php?".paramEncrypt("q1=".$vendor."&q2=".$produk."&q3=".$area."&q4=".$depot));	
			exit();
		} else{
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
	}
?>

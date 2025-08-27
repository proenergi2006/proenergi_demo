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
	
	$cabang			= htmlspecialchars($_POST["nama_cabang"], ENT_QUOTES);	
	$wilayah 		= htmlspecialchars($_POST["wilayah"], ENT_QUOTES);	
	$inisial 		= htmlspecialchars($_POST["inisial"], ENT_QUOTES);	
	$segel 			= htmlspecialchars($_POST["segel"], ENT_QUOTES);	
	$stok 			= htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["stok"]), ENT_QUOTES);
	$stokA 			= htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["stokA"]), ENT_QUOTES);
	$active 		= htmlspecialchars($_POST["active"], ENT_QUOTES);
	$idr 			= htmlspecialchars($_POST["idr"], ENT_QUOTES);
	$kode_barcode 	= htmlspecialchars($_POST["kode_barcode"], ENT_QUOTES);
	$note 			= htmLawed($_POST["note"], array('safe'=>1));

	$stokA			= ($stokA ? $stokA : 0);
	
	if($cabang == "" || $wilayah == "" || $inisial == "" || $segel == "" || $stok == ""){
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else{
		if($act == 'add'){
			$msg = "GAGAL_MASUK";
			$sql = "insert into pro_master_cabang(id_group_cabang, nama_cabang, inisial_cabang, inisial_segel, stok_segel, catatan_cabang, kode_barcode, is_active, created_time, created_ip, created_by) values ('".$wilayah."', '".$cabang."', '".$inisial."', '".$segel."', '".$stok."', '".$note."', '".$kode_barcode."', '".$active."', NOW(), '".$_SERVER['REMOTE_ADDR']."', '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."')";
		} else if($act == 'update'){
			$msg = "GAGAL_UBAH";
			$sql = "
				update pro_master_cabang set catatan_cabang = '".$note."', nama_cabang = '".$cabang."', inisial_cabang = '".$inisial."', inisial_segel = '".$segel."', 
				stok_segel = stok_segel+".$stokA.", kode_barcode = '".$kode_barcode."', is_active = '".$active."', 
				lastupdate_time = NOW(), lastupdate_ip = '".$_SERVER['REMOTE_ADDR']."', lastupdate_by = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."' 
				where id_master = ".$idr;
		}
		
		$con->setQuery($sql);
		if(!$con->hasError()){
			$con->close();
			header("location: ".BASE_URL_CLIENT."/master-cabang.php");
			exit();				
		} else{
			$con->clearError();
			$con->close();
			$flash->add("error", $msg, BASE_REFERER);
		}
	}
?>

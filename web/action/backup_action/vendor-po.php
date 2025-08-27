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
	$act	= isset($enk['act'])?$enk['act']:'';
	if ($act=='') $act = $_POST["act"];
	$idr 	= isset($_POST["idr"])?$_POST["idr"]:null;
	
	$dt1	= htmlspecialchars($_POST["dt1"], ENT_QUOTES);	
	$dt2	= htmlspecialchars($_POST["dt2"], ENT_QUOTES);	
	$dt3	= htmlspecialchars($_POST["dt3"], ENT_QUOTES);	
	$dt4	= htmlspecialchars($_POST["dt4"], ENT_QUOTES);	
	$dt5	= htmlspecialchars($_POST["dt5"], ENT_QUOTES);	
	$dt6	= htmlspecialchars($_POST["dt6"], ENT_QUOTES);	
	$dt7	= htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["dt7"]), ENT_QUOTES);	
	$dt8	= htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["dt8"]), ENT_QUOTES);	

	if($dt1 == "" || $dt3 == "" || $dt4 == "" || $dt5 == "" || $dt6 == ""){
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else{
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		$cek1 = "select * from pro_inventory_vendor where tanggal_inven = '".tgl_db($dt1)."' and id_produk = '".$dt3."' and id_area = '".$dt4."' and id_vendor = '".$dt5."' 
				 and id_terminal = '".$dt6."' for update";
		$row1 = $con->getRecord($cek1);
		if($row1) {
			$msg = "GAGAL_UBAH";
			$idr = $row1['id_master'];
			$sql = "update pro_inventory_vendor set nomor_po = '".$dt2."', in_inven = '".$dt7."', harga_tebus = '".$dt8."', lastupdate_time = NOW(), 
					lastupdate_ip = '".$_SERVER['REMOTE_ADDR']."', lastupdate_by = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."' where id_master = ".$idr;
			$con->setQuery($sql);
			$oke  = $oke && !$con->hasError();

			$cek = "update pro_master_harga_tebus set harga_tebus = '".$dt8."' where id_inven = ".$idr;
			$con->setQuery($cek);
			$oke  = $oke && !$con->hasError();
		} else{
			$msg = "GAGAL_MASUK";
			$sql = "insert into pro_inventory_vendor(tanggal_inven, nomor_po, id_produk, id_area, id_vendor, id_terminal, in_inven, harga_tebus, created_time, created_ip, 
					created_by) values ('".tgl_db($dt1)."', '".$dt2."', '".$dt3."', '".$dt4."', '".$dt5."', '".$dt6."', '".$dt7."', '".$dt8."', NOW(), 
					'".$_SERVER['REMOTE_ADDR']."', '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."')";
			$con->setQuery($sql);
			$oke  = $oke && !$con->hasError();
		}
		/*if($act == 'add'){
			$msg = "GAGAL_MASUK";
			$sql = "insert into pro_inventory_vendor(tanggal_inven, nomor_po, id_produk, id_area, id_vendor, id_terminal, in_inven, harga_tebus, created_time, created_ip, 
					created_by) values ('".tgl_db($dt1)."', '".$dt2."', '".$dt3."', '".$dt4."', '".$dt5."', '".$dt6."', '".$dt7."', '".$dt8."', NOW(), 
					'".$_SERVER['REMOTE_ADDR']."', '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."')";
			$con->setQuery($sql);
			$oke  = $oke && !$con->hasError();
		} else if($act == 'update'){
			$msg = "GAGAL_UBAH";
			$sql = "update pro_inventory_vendor set nomor_po = '".$dt2."', in_inven = '".$dt7."', harga_tebus = '".$dt8."', lastupdate_time = NOW(), 
					lastupdate_ip = '".$_SERVER['REMOTE_ADDR']."', lastupdate_by = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."' where id_master = ".$idr;
			$con->setQuery($sql);
			$oke  = $oke && !$con->hasError();

			$cek = "update pro_master_harga_tebus set harga_tebus = '".$dt8."' where id_inven = ".$idr;
			$con->setQuery($cek);
			$oke  = $oke && !$con->hasError();
		}*/
		
		if ($oke){
			$con->commit();
			$con->close();
			header("location: ".BASE_URL_CLIENT."/vendor-po.php");
			exit();
		} else{
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", $msg, BASE_REFERER);
		}
	}
?>

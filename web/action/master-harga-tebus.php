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
	$act	= isset($enk['act'])?$enk['act']:(isset($_POST['act'])?htmlspecialchars($_POST["act"], ENT_QUOTES):null);

	$user_ip 	= $_SERVER['REMOTE_ADDR'];
	$user_pic 	= paramDecrypt($_SESSION["sinori".SESSIONID]["fullname"]);

	if(!is_array_empty($_POST["prd"])){
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		$delete = [];
		foreach ($_POST["inp"][1] as $key => $value) {
			if (!isset($_POST["prd"][1][$key]))
				$delete[] = $value;
		}
		foreach ($_POST["inp"][2] as $key => $value) {
			if (!isset($_POST["prd"][2][$key]))
				$delete[] = $value;
		}

		if(count($_POST["prd"][1])){
			foreach($_POST["prd"][1] as $idx1=>$val1){
				$cek1 = "select id_master from pro_master_harga_tebus where (periode_awal, id_area, id_vendor, id_produk, id_terminal)in(select '::p1', id_area, id_vendor, 
						 id_produk, id_terminal from pro_inventory_vendor_po where id_master = '".$idx1."')";
				$var1 = htmlspecialchars($_POST["prd"][1][$idx1], ENT_QUOTES);
				$tVar = explode("#|#", $var1);
				$tgl1 = '01/'.$tVar[1].'/'.$tVar[2];
				$tgl2 = '14/'.$tVar[1].'/'.$tVar[2];
				$cek1 = str_replace('::p1', tgl_db($tgl1), $cek1);
				$idr1 = $con->getOne($cek1);
				if($idr1){
					$sql = "update pro_master_harga_tebus set harga_tebus = (select harga_tebus from pro_inventory_vendor_po where id_master = '".$idx1."'), id_inven = '".$idx1."', 
							lastupdate_time = NOW(), lastupdate_ip = '".$user_ip."', lastupdate_by = '".$user_pic."' where id_master = '".$idr1."'";
					$con->setQuery($sql);
					$oke  = $oke && !$con->hasError();
				} else{
					$sql = "insert into pro_master_harga_tebus (id_inven, id_produk, id_area, id_vendor, id_terminal, periode_awal, periode_akhir, harga_tebus, created_time, 
							created_ip, created_by)(select id_master, id_produk, id_area, id_vendor, id_terminal, '".tgl_db($tgl1)."', '".tgl_db($tgl2)."', harga_tebus, 
							NOW(), '".$user_ip."', '".$user_pic."' from pro_inventory_vendor_po where id_master = '".$idx1."')";
					$con->setQuery($sql);
					$oke  = $oke && !$con->hasError();
				}
			}
		}

		if(count($_POST["prd"][2])){
			foreach($_POST["prd"][2] as $idx2=>$val2){
				$cek1 = "select id_master from pro_master_harga_tebus where (periode_awal, id_area, id_vendor, id_produk, id_terminal)in(select '::p1', id_area, id_vendor, 
						 id_produk, id_terminal from pro_inventory_vendor_po where id_master = '".$idx2."')";
				$var1 = htmlspecialchars($_POST["prd"][2][$idx2], ENT_QUOTES);
				$tVar = explode("#|#", $var1);
				$tgl1 = '15/'.$tVar[1].'/'.$tVar[2];
				$tgl2 = cal_days_in_month(CAL_GREGORIAN, $tVar[1], $tVar[2]).'/'.$tVar[1].'/'.$tVar[2];
				$cek1 = str_replace('::p1', tgl_db($tgl1), $cek1);
				$idr1 = $con->getOne($cek1);
				if($idr1){
					$sql = "update pro_master_harga_tebus set harga_tebus = (select harga_tebus from pro_inventory_vendor_po where id_master = '".$idx2."'), id_inven = '".$idx2."', 
							lastupdate_time = NOW(), lastupdate_ip = '".$user_ip."', lastupdate_by = '".$user_pic."' where id_master = '".$idr1."'";
					$con->setQuery($sql);
					$oke  = $oke && !$con->hasError();
				} else{
					$sql = "insert into pro_master_harga_tebus (id_inven, id_produk, id_area, id_vendor, id_terminal, periode_awal, periode_akhir, harga_tebus, created_time, 
							created_ip, created_by)(select id_master, id_produk, id_area, id_vendor, id_terminal, '".tgl_db($tgl1)."', '".tgl_db($tgl2)."', harga_tebus, 
							NOW(), '".$user_ip."', '".$user_pic."' from pro_inventory_vendor_po where id_master = '".$idx2."')";
					$con->setQuery($sql);
					$oke  = $oke && !$con->hasError();
				}
			}
		}

		foreach ($delete as $row) {
			$sql = "delete from pro_master_harga_tebus where id_master = '".$row."'";
			$con->setQuery($sql);
			$oke  = $oke && !$con->hasError();
		}

		if ($oke){
			$con->commit();
			$con->close();
			$flash->add("success", "Data berhasil disimpan", BASE_URL_CLIENT."/vendor-po.php");				
		} else{
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "Maaf, ada duplikasi data untuk harga tebus", BASE_REFERER);
		}
	} else{
		$con->close();
		header("location: ".BASE_URL_CLIENT."/vendor-po.php");
		exit();
	}
?>

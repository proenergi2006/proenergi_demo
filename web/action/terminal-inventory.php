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
	$pic 	= paramDecrypt($_SESSION["sinori".SESSIONID]["fullname"]);
	$term 	= (paramDecrypt($_SESSION["sinori".SESSIONID]["terminal"]) == 13)?paramDecrypt($_SESSION["sinori".SESSIONID]["terminal"]):htmlspecialchars($_POST["terminal"], ENT_QUOTES);
	
	$tgl		= htmlspecialchars($_POST["tgl"], ENT_QUOTES);	
	$jam 		= htmlspecialchars($_POST["jam"], ENT_QUOTES);	
	$suhu 		= htmlspecialchars($_POST["suhu"], ENT_QUOTES);	
	$book_stok 	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["book_stok"]), ENT_QUOTES);	
	$awlm1 		= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["awlm1"]), ENT_QUOTES);	
	$awlm2 		= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["awlm2"]), ENT_QUOTES);	
	$voltbl 	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["voltbl"]), ENT_QUOTES);	
	$shrink 	= htmlspecialchars($_POST["shrink"], ENT_QUOTES);	
	$awlnet 	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["awlnet"]), ENT_QUOTES);	
	$density1	= htmlspecialchars($_POST["density1"], ENT_QUOTES);	
	$density2	= htmlspecialchars($_POST["density2"], ENT_QUOTES);	
	$vcf 		= htmlspecialchars($_POST["vcf"], ENT_QUOTES);	
	$produk 	= htmlspecialchars($_POST["prd"], ENT_QUOTES);	
	$ship 		= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["ship"]), ENT_QUOTES);	
	$truck		= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["truck"]), ENT_QUOTES);	
	$in_slop 	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["in_slop"]), ENT_QUOTES);	
	$tank_pipe 	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["tank_pipe"]), ENT_QUOTES);	
	$out_slop 	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["out_slop"]), ENT_QUOTES);

	$tgl_tmp 	= explode("/", $tgl);
	$yesterday 	= date("Y/m/d", mktime(0, 0, 0, $tgl_tmp[1], $tgl_tmp[0]-1, $tgl_tmp[2]));

	if($tgl == "" || $jam == "" || $suhu == "" || $awlm1 == "" || $voltbl == "" || $shrink == "" || $awlnet == "" || $density1 == "" || $density2 == "" || $vcf == ""){
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else{
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		if($act == 'add'){
			$cek1 = "select id_master from pro_master_inventory where tanggal_inv = '".$yesterday."' and id_terminal = '".$term."' and id_produk = '".$produk."'";
			$ada1 = $con->getOne($cek1);
			if($ada1){
				$sql1 = "update pro_master_inventory set gain_loss = '".$awlnet."' where id_master = '".$ada1."'";
				$con->setQuery($sql1);
				$oke  = $oke && !$con->hasError();
			} else{
				$sql1 = "insert into pro_master_inventory(id_terminal, id_produk, tanggal_inv, gain_loss, created_time, created_ip, created_by) values ('".$term."', '".$produk."', '".$yesterday."', '".$awlnet."', NOW(), '".$_SERVER['REMOTE_ADDR']."', '".$pic."')";
				$con->setQuery($sql1);
				$oke  = $oke && !$con->hasError();
			}
			
			$cek2 = "select id_master from pro_master_inventory where tanggal_inv = '".tgl_db($tgl)."' and id_terminal = '".$term."' and id_produk = '".$produk."'";
			$ada2 = $con->getOne($cek2);
			if($ada2){
				$sql2 = "update pro_master_inventory set awal_jam = '".$jam."', awal_level1 = '".$awlm1."', awal_level2 = '".$awlm2."', awal_volume_tabel = '".$voltbl."', awal_shrink = '".$shrink."', awal_nett = '".$awlnet."', awal_temp = '".$suhu."', awal_density1 = '".$density1."', awal_density2 = '".$density2."', awal_vcf = '".$vcf."', book_stok = '".$book_stok."', masuk_ship = '".$ship."', masuk_truck = '".$truck."', masuk_slop = '".$in_slop."', keluar_slop = '".$out_slop."', tank_pipe = '".$tank_pipe."', lastupdate_time = NOW(), lastupdate_ip = '".$_SERVER['REMOTE_ADDR']."', lastupdate_by = '".$pic."' where id_master = '".$ada2."'";
				$con->setQuery($sql2);
				$oke  = $oke && !$con->hasError();
			} else{
				$sql2 = "insert into pro_master_inventory(id_terminal, id_produk, tanggal_inv, awal_jam, awal_level1, awal_level2, awal_volume_tabel, awal_shrink, awal_nett, awal_temp, awal_density1, awal_density2, awal_vcf, book_stok, masuk_ship, masuk_truck, masuk_slop, keluar_slop, tank_pipe, created_time, created_ip, created_by) values ('".$term."', '".$produk."', '".tgl_db($tgl)."', '".$jam."', '".$awlm1."', '".$awlm2."', '".$voltbl."', '".$shrink."', '".$awlnet."', '".$suhu."', '".$density1."', '".$density2."', '".$vcf."', '".$book_stok."', '".$ship."', '".$truck."', '".$in_slop."', '".$out_slop."', '".$tank_pipe."', NOW(), '".$_SERVER['REMOTE_ADDR']."', '".$pic."')";
				$con->setQuery($sql2);
				$oke  = $oke && !$con->hasError();
			}
			$msg = "GAGAL_MASUK";
		} 
		
		else if($act == 'update'){
			$cek1 = "select id_master from pro_master_inventory where tanggal_inv = '".$yesterday."' and id_terminal = '".$term."' and id_produk = '".$produk."'";
			$ada1 = $con->getOne($cek1);
			if($ada1){
				$sql1 = "update pro_master_inventory set gain_loss = '".$awlnet."' where id_master = '".$ada1."'";
				$con->setQuery($sql1);
				$oke  = $oke && !$con->hasError();
			} else{
				$sql1 = "insert into pro_master_inventory(id_terminal, id_produk, tanggal_inv, gain_loss, created_time, created_ip, created_by) values ('".$term."', '".$produk."', '".$yesterday."', '".$awlnet."', NOW(), '".$_SERVER['REMOTE_ADDR']."', '".$pic."')";
				$con->setQuery($sql1);
				$oke  = $oke && !$con->hasError();
			}

			$sql2 = "update pro_master_inventory set awal_jam = '".$jam."', awal_level1 = '".$awlm1."', awal_level2 = '".$awlm2."', awal_volume_tabel = '".$voltbl."', awal_shrink = '".$shrink."', awal_nett = '".$awlnet."', awal_temp = '".$suhu."', awal_density1 = '".$density1."', awal_density2 = '".$density2."', awal_vcf = '".$vcf."', book_stok = '".$book_stok."', masuk_ship = '".$ship."', masuk_truck = '".$truck."', masuk_slop = '".$in_slop."', keluar_slop = '".$out_slop."', tank_pipe = '".$tank_pipe."', lastupdate_time = NOW(), lastupdate_ip = '".$_SERVER['REMOTE_ADDR']."', lastupdate_by = '".$pic."' where id_master = '".$idr."'";
			$con->setQuery($sql2);
			$oke  = $oke && !$con->hasError();
			$msg = "GAGAL_UBAH";
		}
		
		if($oke){
			$con->commit();
			$con->close();
			header("location: ".BASE_URL_CLIENT."/terminal-inventory.php?".paramEncrypt("prd=".$produk));	
			exit();
		} else{
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
	}
?>

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

	if($kategori == "" || $keperluan == ""){
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else{
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		if($kategori == 1){
			if($act == "add"){
				$cek1 = "select id_master, inisial_segel, stok_segel, urut_segel, inisial_cabang, urut_ba from pro_master_cabang where id_master = '".$wilayah."' for update";
				$row1 = $con->getRecord($cek1);
				$stok = $row1['stok_segel'];
				$seal = $row1['urut_segel'];
		
				if($stok < $jumlah){
					$con->rollBack();
					$con->clearError();
					$con->close();
					$flash->add("error", "Maaf stok segel tidak cukup...", BASE_REFERER);		
				} else{
					if($jumlah > 1){
						$segel_awal = $seal + 1;
						$segel_last = $seal + $jumlah;
						$seal = $seal + $jumlah;
					} else{
						$segel_awal = $seal + $jumlah;
						$segel_last = 0;
						$seal = $seal + $jumlah;
					}
					
					$tmp1 = $row1['urut_ba'] + 1;
					$tmp2 = array("1"=>"I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
					$noms = str_pad($tmp1,4,'0',STR_PAD_LEFT).'/BA/'.$row1['inisial_cabang'].'/'.$tmp2[intval(date("m"))].'/'.date("Y");
		
					$sql1 = "insert into pro_manual_segel(id_wilayah, kategori, jumlah_segel, tanggal_segel, segel_awal, segel_akhir, nomor_akhir, nomor_stock, nomor_acara, 
							 keperluan, created_time, created_ip, created_by) values ('".$wilayah."', '".$kategori."', '".$jumlah."', '".tgl_db($tanggal)."', '".$segel_awal."', 
							 '".$segel_last."', '".$nomor1."', '".$nomor2."', '".$noms."', '".$keperluan."', NOW(), '".$_SERVER['REMOTE_ADDR']."', '".$pic."')";
					$res1 = $con->setQuery($sql1);
					$oke  = $oke && !$con->hasError();
					$url  = BASE_URL_CLIENT."/manual-segel-detail.php?".paramEncrypt("idr=".$res1);
		
					$sql2 = "update pro_master_cabang set stok_segel = stok_segel - ".$jumlah.", urut_segel = '".$seal."', urut_ba = '".$tmp1."' where id_master = '".$wilayah."'";
					$con->setQuery($sql2);
					$oke  = $oke && !$con->hasError();
				}
			} else if($act == "update"){
				$sql2 = "update pro_manual_segel set keperluan = '".$keperluan."' where id_master = '".$idr."'";
				$con->setQuery($sql2);
				$oke  = $oke && !$con->hasError();					
				$url  = BASE_URL_CLIENT."/manual-segel-detail.php?".paramEncrypt("idr=".$idr);
			}
	
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
		} else if($kategori == 2){
			if($act == "add"){
				$cek1 = "select id_master, inisial_segel, stok_segel, urut_segel, inisial_cabang, urut_ba from pro_master_cabang where id_master = '".$wilayah."' for update";
				$row1 = $con->getRecord($cek1);
	
				$tmp1 = $row1['urut_ba'] + 1;
				$tmp2 = array("1"=>"I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
				$noms = str_pad($tmp1,4,'0',STR_PAD_LEFT).'/BA/'.$row1['inisial_cabang'].'/'.$tmp2[intval(date("m"))].'/'.date("Y");
	
				$sql1 = "insert into pro_manual_segel(id_wilayah, kategori, nomor_acara, keperluan, created_time, created_ip, created_by) values ('".$wilayah."', '".$kategori."', 
						'".$noms."', '".$keperluan."', NOW(), '".$_SERVER['REMOTE_ADDR']."', '".$pic."')";
				$res1 = $con->setQuery($sql1);
				$oke  = $oke && !$con->hasError();
				$url  = BASE_URL_CLIENT."/manual-segel-detail.php?".paramEncrypt("idr=".$res1);
	
				$sql2 = "update pro_master_cabang set urut_ba = '".$tmp1."' where id_master = '".$wilayah."'";
				$con->setQuery($sql2);
				$oke  = $oke && !$con->hasError();
			} else if($act == "update"){
				$sql2 = "update pro_manual_segel set keperluan = '".$keperluan."' where id_master = '".$idr."'";
				$con->setQuery($sql2);
				$oke  = $oke && !$con->hasError();					
				$url  = BASE_URL_CLIENT."/manual-segel-detail.php?".paramEncrypt("idr=".$idr);
			}
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
	}
?>
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
	$act	= isset($enk['act'])?$enk['act']:htmlspecialchars($_POST["act"], ENT_QUOTES);
	$idr	= isset($enk['idr'])?null:htmlspecialchars($_POST["idr"], ENT_QUOTES);
	$id_master	= isset($enk['id_master'])?null:htmlspecialchars($_POST["id_master"], ENT_QUOTES);

	$id_ruangan = $idr;
	$id_user = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);

	$tierX = htmlspecialchars($_POST["tier"], ENT_QUOTES);
	$tglAwal = $_POST["tgl_awal"];
	$tglAkhir = $_POST["tgl_akhir"];
	$hargaAwal = $_POST["harga_awal"];
	if ($hargaAwal) $hargaAwal = str_replace(',', '', $hargaAwal);
	$hargaAkhir = $_POST["harga_akhir"];
	if ($hargaAkhir) $hargaAkhir = str_replace(',', '', $hargaAkhir);
	// $customer_date = date("Y-m-d", strtotime(str_replace('/', '-', $_POST["customer_date"])));
	if($act == "add"){
		$con->beginTransaction();

		$sql = "
                select 
                        IF(count(1)=0,0,count(1)+1) X
                    FROM pro_master_pl_insentif a
                where 
                    DATE_FORMAT(a.TGL_AWAL, '%d/%m/%Y') = '".$tglAwal."' 
                    and DATE_FORMAT(a.TGL_AKHIR, '%d/%m/%Y') = '".$tglAkhir."'
            	"
        ;
        $row = $con->getRecord($sql);

        if ($row['X']>0) {
	        $next	= 'SUCCESS';
        }else{
        	$sql ="
	            	select 
						IF(STR_TO_DATE('".$tglAkhir."','%d/%m/%Y') >= STR_TO_DATE('".$tglAwal."','%d/%m/%Y'),'1','0') 'INPUTSTART_VS_INPUTEND', 
				        IF(STR_TO_DATE('".$tglAwal."','%d/%m/%Y') > ifnull(MAX(a.TGL_AKHIR),STR_TO_DATE('01/01/1000','%d/%m/%Y')) ,'1','0') 'INPUTSTART_VS_SYSEND'
					FROM pro_master_pl_insentif a;
	        	  "
	        ;
	        $row = $con->getRecord($sql);

	        if ($row['INPUTSTART_VS_SYSEND'] == '1' && $row['INPUTSTART_VS_INPUTEND'] == '1') {
	        	$next='SUCCESS';
	        }else{
	        	$next='ERROR';
	        }
        }

        
        
		if ($next == 'ERROR') {
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}else{
			$sql1 = "
						INSERT INTO pro_master_pl_insentif
								(TIER,
								TGL_AWAL,
								TGL_AKHIR,
								HARGA_AWAL,
								HARGA_AKHIR,
								PETUGAS_REKAM,
								TGL_REKAM)
						VALUES
								('".$tierX."',
								STR_TO_DATE('".tgl_db($tglAwal)."', '%Y/%m/%d'),
								STR_TO_DATE('".tgl_db($tglAkhir)."', '%Y/%m/%d'),
								'".(int)$hargaAwal."',
								'".(int)$hargaAkhir."',
								'".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."',
								now())";
			
			$res1 = $con->setQuery($sql1);
			$oke  = !$con->hasError();
			$url = BASE_URL_CLIENT."/insentif-pricelist-master.php";
			$msg = "Data berhasil disimpan";
		}


		if ($oke){
			$con->commit();
			$con->close();
			$flash->add("success", $msg, $url);
		} else{
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
		
	}

	if($act == "update"){
		
		$con->beginTransaction();
		$con->clearError();
		$sql1 = "
			UPDATE pro_master_pl_insentif
			SET
				HARGA_AWAL = '".$hargaAwal."',
				HARGA_AKHIR = '".$hargaAkhir."',
				PETUGAS_UBAH = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])."',
				TGL_UBAH = now()
			WHERE id_master='".$id_master."'
		";

		
		$res1 = $con->setQuery($sql1);
		$oke  = !$con->hasError();
		$url = BASE_URL_CLIENT."/insentif-pricelist-master.php";
		$msg = 'Data berhasil diupdate';

		if ($oke){
			$con->commit();
			$con->close();
			$flash->add("success", $msg, $url);
			exit();
		} else{
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
		
	}
?>

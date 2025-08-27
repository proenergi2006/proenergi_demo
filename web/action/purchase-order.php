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

	$transportir = htmlspecialchars($_POST["transportir"], ENT_QUOTES);	
	$code_pr 	= htmlspecialchars($_POST["code_pr"], ENT_QUOTES);
	$wilayah	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
	$pic		= paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname']);	
	
	if($transportir == "" || $code_pr == ""){
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else if(is_array_empty($_POST["cek"])){
		$con->close();
		$flash->add("error", "Anda belum memilih data PR", BASE_REFERER);
	} else{
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		$cek1 = "select inisial_cabang, urut_po from pro_master_cabang where id_master = '".$wilayah."' for update";
		$row1 = $con->getRecord($cek1);
		$tmp1 = $row1['urut_po'] + 1;
		$tmp2 = array("1"=>"I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
		$noms = str_pad($tmp1,4,'0',STR_PAD_LEFT).'/PE/PO/'.$row1['inisial_cabang'].'/'.$tmp2[intval(date("m"))].'/'.date("Y");

		$sql1 = "insert into pro_po(id_pr, id_wilayah, id_transportir, nomor_po, tanggal_po, created_by) 
				 values ('".$code_pr."', '".$wilayah."', '".$transportir."', '".$noms."', NOW(), '".$pic."')";
		$res1 = $con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();
		$url  = BASE_URL_CLIENT."/purchase-order-detail.php?".paramEncrypt("idr=".$res1);
		$urut = 0;
		foreach($_POST['cek'] as $idx=>$val){
			$urut++;
			$tgl1 = htmlspecialchars($_POST["dt1"][$idx], ENT_QUOTES);
			$sql2 = "insert into pro_po_detail(id_po, id_prd, id_plan, volume_po, no_urut_po, tgl_kirim_po, terminal_po)
					(select '".$res1."', '".$idx."', id_plan, volume, '".$urut."', '".$tgl1."', pr_terminal from pro_pr_detail where id_prd = '".$idx."')";
			$con->setQuery($sql2);
			$oke  = $oke && !$con->hasError();				
		}
		$sql4 = "update pro_master_cabang set urut_po = '".$tmp1."' where id_master = '".$wilayah."'";
		$con->setQuery($sql4);
		$oke  = $oke && !$con->hasError();

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

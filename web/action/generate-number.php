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

	$id_master = $idr;
	$id_user = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);
	$urut_spj = htmlspecialchars($_POST["urut_spj"], ENT_QUOTES)-1;
	$urut_dn = htmlspecialchars($_POST["urut_dn"], ENT_QUOTES)-1;
	$urut_po = htmlspecialchars($_POST["urut_po"], ENT_QUOTES)-1;

	// $sql1 = "select id_master from pro_master_cabang where is_active = 1 and urut_spj <= '".((int)$urut_spj)."' and urut_dn <= '".((int)$urut_dn)."' and urut_po <= '".((int)$urut_po)."' and id_master = '".$id_master."'";
	// $query = $con->getRecord($sql1);
	// if (!$query) {
	// 	$con->close();
	// 	$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	// }
	
	if($act == "update"){
		if($urut_spj == "" || $urut_dn == "" || $urut_po == ""){
			$con->close();
			$flash->add("error", "KOSONG", BASE_REFERER);
		} else{
			$oke = true;
			$con->beginTransaction();
			$con->clearError();
			$sql1 = "
			update pro_master_cabang set 
				urut_spj = '".$urut_spj."', 
				urut_dn = '".$urut_dn."', 
				urut_po = '".$urut_po."',
				lastupdate_time = NOW()
			where 
				id_master = '".$id_master."'";
			$res1 = $con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();
			$url = BASE_URL_CLIENT."/generate-number.php";

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

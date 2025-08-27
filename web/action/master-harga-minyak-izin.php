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
	$idr 	= isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
	list($id1, $id2, $id3, $id4) = explode("#*#", $idr);
	$url 	= BASE_URL_CLIENT."/add-master-harga-minyak-detail.php?".paramEncrypt('idr='.$idr);

	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	$sql = "update pro_master_harga_minyak set is_evaluated = 1 where periode_awal = '".$id1."' and periode_akhir = '".$id2."' and id_cabang = '".$id3."' and produk = '".$id4."'";
	$con->setQuery($sql);
	$oke  = $oke && !$con->hasError();

	if ($oke){
		$con->commit();
		$con->close();
		$flash->add("success", "Persetujuan harga minyak sudah diajukan", $url);
	} else{
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}
?>

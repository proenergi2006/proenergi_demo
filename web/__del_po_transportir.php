<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$arr	= array();
	$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
	$msg	= "";

	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	$cek1 = "select a.id_po, b.id_pr, c.id_ds 
			 from pro_po_detail a 
			 join pro_pr_detail b on a.id_prd = b.id_prd 
			 left join pro_po_ds_detail c on a.id_pod = c.id_pod
			 where a.id_pod = '".$idr."'";
	$row1 = $con->getRecord($cek1);

	$sql1 = "delete from pro_po_ds_detail where id_pod = '".$idr."'";
	$con->setQuery($sql1);
	$oke  = $oke && !$con->hasError();

	$sql2 = "delete from pro_po_detail where id_pod = '".$idr."'";
	$con->setQuery($sql2);
	$oke  = $oke && !$con->hasError();

	$sql3 = "update pro_pr set is_edited = 1 where id_pr = '".$row1['id_pr']."'";
	$con->setQuery($sql3);
	$oke  = $oke && !$con->hasError();

	$cek2 = "select count(*) from pro_po_detail where id_po = '".$row1['id_po']."'";
	$row2 = $con->getOne($cek2);

	$cek3 = "select count(*) from pro_po_ds_detail where id_ds = '".$row1['id_ds']."'";
	$row3 = $con->getOne($cek3);
	
	if($row3 == 0){
		$sql5 = "delete from pro_po_ds where id_ds = '".$row1['id_ds']."'";
		$con->setQuery($sql5);
		$oke  = $oke && !$con->hasError();
	}

	if($row2 > 0){
		$msg = 'reload';
	} else{
		$msg  = 'refresh';
		$sql4 = "delete from pro_po where id_po = '".$row1['id_po']."'";
		$con->setQuery($sql4);
		$oke  = $oke && !$con->hasError();
	}

	if ($oke){
		$con->commit();
		$con->close();
	} else{
		$con->rollBack();
		$con->clearError();
		$con->close();
		$msg = "error";
	}
	echo $msg;
?>

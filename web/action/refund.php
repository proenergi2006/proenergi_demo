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
	$pic 	= paramDecrypt($_SESSION["sinori".SESSIONID]["fullname"]);
	$term 	= paramDecrypt($_SESSION["sinori".SESSIONID]["terminal"]);
	$answer	= array();
	
	$tgl 	= htmlspecialchars($_POST["tgl_bayar"], ENT_QUOTES);	
	$ket 	= htmlspecialchars($_POST["keterangan"], ENT_QUOTES);	
	$tipe 	= htmlspecialchars($_POST["tipe"], ENT_QUOTES);
	$param 	= htmlspecialchars(paramDecrypt($_POST["param"]), ENT_QUOTES);	
	$param 	= explode("|#|", $param);
	$idnya 	= $param[0];
	$volume = $param[1];

	$oke = true;
	$con->beginTransaction();
	$con->clearError();		

	$arrSql = array(1=>array("table"=>"pro_po_ds_detail", "key"=>"id_dsd"), array("table"=>"pro_po_ds_kapal", "key"=>"id_dsk"));
	$sql1 = "update ".$arrSql[$tipe]["table"]." set is_bayar = 1, tanggal_bayar = '".tgl_db($tgl)."', ket_bayar = '".$ket."'  where ".$arrSql[$tipe]["key"]." = '".$idnya."'";
	$con->setQuery($sql1);
	$oke  = $oke && !$con->hasError();
	
	if($oke){
		$con->commit();
		$con->close();
		$answer["error"] = "";			
	} else{
		$con->rollBack();
		$con->clearError();
		$con->close();
		$answer["error"] = "Maaf, sistem mengalami kendala teknis. Silahkan coba lagi..";
	}
	echo json_encode($answer);
?>

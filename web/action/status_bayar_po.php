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
	// $act	= ($enk['act'] == "")?htmlspecialchars($_POST["act"], ENT_QUOTES):$enk['act'];
	$pic 	= paramDecrypt($_SESSION["sinori".SESSIONID]["fullname"]);
	$term 	= paramDecrypt($_SESSION["sinori".SESSIONID]["terminal"]);
	$answer	= array();
	
	$tgl_bayar 	= htmlspecialchars($_POST["tgl_bayar"], ENT_QUOTES);
	$tgl = explode('/', $tgl_bayar);
	$tgl = $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
	$ket 	= htmlspecialchars($_POST["keterangan"], ENT_QUOTES);	
	// $tipe 	= htmlspecialchars($_POST["tipe"], ENT_QUOTES);
	$param 	= htmlspecialchars(paramDecrypt($_POST["param"]), ENT_QUOTES);	

	$param 	= explode("|#|", $param);
	$id 	= $param[0];
	$id_poc = $param[1];
	$id_customer=$param[2];

	$oke = true;
	$con->beginTransaction();
	$con->clearError();		

	$sql1 = "update pro_po_customer
				set st_bayar_po='Y',
					tgl_bayar_po='".$tgl."',
					keterangan_bayar='".$ket."'
			where id_poc='".$id_poc."'
			and id_customer = '".$id_customer."'";
	
	$con->setQuery($sql1);
	$oke  = $oke && !$con->hasError();
	
	if($oke){
		$con->commit();
		$con->close();
		$answer["msg"] = "Data berhasil diupdate !!!";	
		$answer["status"] = "1";	
	} else{
		$con->rollBack();
		$con->clearError();	
		$con->close();
		$answer["msg"] = "Maaf, sistem mengalami kendala teknis. Silahkan coba lagi..";
		$answer["status"] = "-1";	

	}
	echo json_encode($answer);
?>

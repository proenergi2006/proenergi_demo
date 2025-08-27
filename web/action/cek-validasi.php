<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload", "htmlawed", "mailgen");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$enk  	= decode($_SERVER['REQUEST_URI']);
	
	$formnya = htmlspecialchars($_POST["formnya"], ENT_QUOTES);	
	
	if($formnya == 'vendor-po-new-terima-add'){
		$id_terminal 	= htmlspecialchars($_POST["id_terminal_po"], ENT_QUOTES);	
		$tgl_terima 	= htmlspecialchars($_POST["tgl_terima"], ENT_QUOTES);	
	
		$sql01 = "select tanggal_inven from new_pro_inventory_depot where id_terminal = '".$id_terminal."' and id_jenis = 1";
		$rsm01 = $con->getRecord($sql01);
		
		if($rsm01['tanggal_inven'] && strtotime(tgl_db($tgl_terima)) < strtotime($rsm01['tanggal_inven'])){
			echo json_encode(array('hasil'=>false, 'pesan'=>'Tanggal terima lebih kecil dari<br />data awal <i>( '.date('d/m/Y', strtotime($rsm01['tanggal_inven'])).' )</i> untuk terminal ini'));
			exit;
		} else{
			echo json_encode(array('hasil'=>true, 'pesan'=>''));
			exit;
		}

	}
	
?>

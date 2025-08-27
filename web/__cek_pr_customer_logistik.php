<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$conSub = new Connection();
	$valid	= true;
	$answer	= array();
	$arrExt = array();
	
	if(isset($_POST["newdt3"])){
		foreach($_POST['newdt3'] as $idx1=>$val1){
			$arrExt[$idx1] = 0;
			foreach($_POST['newdt3'][$idx1] as $idx2=>$val2){
				$dt3 = htmlspecialchars($_POST['newdt3'][$idx1][$idx2], ENT_QUOTES);
				//$arrExt[$idx1] = $arrExt[$idx1] + $dt3;
				if(!$dt3){
					$valid = false;
					$pesan = "Kolom volume harus diisi semua";
					break 2;			
				}else{
					$arrExt[$idx1] = $arrExt[$idx1] + $dt3;
				}
			}
		}
	}

	if($valid){
		foreach($_POST['dt3'] as $idx3=>$val3){
			$volext 	= $arrExt[$idx3];
			//$volori 	= htmlspecialchars(str_replace(array(".",","),array("",""),$_POST['volori'][$idx3]), ENT_QUOTES);
			$voloripr 	= htmlspecialchars(str_replace(array(".",","),array("",""),$_POST['voloripr'][$idx3]), ENT_QUOTES);
			$jumlah = 0;
			foreach($_POST['dt3'][$idx3] as $idx4=>$val4){
				$dt3 	= htmlspecialchars(str_replace(array(".",","),array("",""),$_POST['dt3'][$idx3][$idx4]), ENT_QUOTES);
				$jumlah = $jumlah + $dt3;
			}
			if(($jumlah + $volext) != $voloripr){
				$valid = false;
				$pesan = "Volume pengiriman tidak sama dengan yang sudah diverifikasi";
				break;
			}
		}
	}

	$answer["error"] = ($valid)?"":$pesan;
    echo json_encode($answer);
	$conSub->close();
?>

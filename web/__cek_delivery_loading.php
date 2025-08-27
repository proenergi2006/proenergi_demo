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
	$loco 	= htmlspecialchars($_POST['loco'], ENT_QUOTES);
	
	$nom = 0;
	if($loco == 0){
		if(!is_array_empty($_POST['dt1'])){
			foreach($_POST['dt1'] as $idx=>$val){
				$dt1 = htmlspecialchars($_POST['dt1'][$idx], ENT_QUOTES);
				$dt2 = htmlspecialchars($_POST['dt2'][$idx], ENT_QUOTES);
				$dt3 = htmlspecialchars($_POST['dt3'][$idx], ENT_QUOTES);
				$dt6 = htmlspecialchars($_POST['dt6'][$idx], ENT_QUOTES);
				if(!$dt1 || !$dt2 || !$dt3){
					$valid = false;
					$pesan = "Kolom Ref DN dan tanggal jam loading haris diisi semua";
					break;
				} else if($dt6 && !is_numeric($dt6)){
					$valid = false;
					$pesan = "Kolom segel diisi dengan jumlah segelnya saja";
					break;
				}
			}
		}
	} else if($loco == 1){
		if(!is_array_empty($_POST['dt1'])){
			foreach($_POST['dt1'] as $idx=>$val){
				$dt1 = htmlspecialchars($_POST['dt1'][$idx], ENT_QUOTES);
				$dt2 = htmlspecialchars($_POST['dt2'][$idx], ENT_QUOTES);
				$dt3 = htmlspecialchars($_POST['dt3'][$idx], ENT_QUOTES);
				$dt6 = htmlspecialchars($_POST['dt6'][$idx], ENT_QUOTES);
				$dt10 = htmlspecialchars($_POST['dt10'][$idx], ENT_QUOTES);
				$dt11 = htmlspecialchars($_POST['dt11'][$idx], ENT_QUOTES);
				$dt12 = htmlspecialchars($_POST['dt12'][$idx], ENT_QUOTES);
				if(!$dt1 || !$dt2 || !$dt3 || !$dt10 || !$dt11 || !$dt12){
					$valid = false;
					$pesan = "Kolom Ref DN, Transportir, Truck, Driver dan Tanggal jam loading haris diisi semua";
					break;
				} else if($dt6 && !is_numeric($dt6)){
					$valid = false;
					$pesan = "Kolom segel diisi dengan jumlah segelnya saja";
					break;
				}
			}
		}
	}

	$answer["error"] = ($valid)?"":$pesan;
    echo json_encode($answer);
	$conSub->close();
?>

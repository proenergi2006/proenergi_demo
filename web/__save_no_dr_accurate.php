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

	
	if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 10){
		$set=htmlspecialchars($_POST['set_no_do_accurate'], ENT_QUOTES);
		if(isset($set)){
		$idpr=htmlspecialchars($_POST['id_pr'], ENT_QUOTES);
		$nilai=htmlspecialchars($_POST['nilai'], ENT_QUOTES);
		$sql_accurete = "
				update pro_pr_detail set 
					no_do_acurate='".$nilai."'
				where id_prd = '".$idpr."'";
		$con->setQuery($sql_accurete);
		$oke  = $oke && !$con->hasError();
		if ($oke){
			$hasil=$con->commit();
			$con->close();
			echo json_encode($hasil,true);
		} else{
			$hasil=$con->rollBack();
			$con->clearError();
			$con->close();
			echo json_encode($hasil,true);
		}
		}

	}

	
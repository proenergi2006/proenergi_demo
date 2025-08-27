<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	if(isset($_POST) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
		$enk  	= decode(BASE_REFERER);
		$enk['idr'] = (paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 10 && isset($_SESSION['sinori'.SESSIONID]['id_customer']) ? $_SESSION['sinori'.SESSIONID]['id_customer'] : $enk['idr']);
		$idr  	= htmlspecialchars($enk["idr"],ENT_QUOTES); 
		$file  	= htmlspecialchars($_POST["file"],ENT_QUOTES); 
		$deleted_file = htmlspecialchars($_POST["source"],ENT_QUOTES); 
		$pref	= str_replace("_del","",$file).$idr."_".$deleted_file;
		$path	= $public_base_directory."/files/uploaded_user/images";
		$resp 	= array();

		$conFiles = new Connection();
		$arrImage = array("sert_file"=>"nomor_sertifikat_file","npwp_file"=>"nomor_npwp_file","siup_file"=>"nomor_siup_file","tdp_file"=>"nomor_tdp_file","dokumen_lainnya_file"=>"dokumen_lainnya_file");		
		$arrFiles = glob($path."/".$pref."*.{jpg,jpeg,gif,png,pdf,zip,rar}", GLOB_BRACE);

		$oke = true;
		$conFiles->beginTransaction();
		$conFiles->clearError();
		$sqlexdata = "SELECT * FROM `pro_customer` WHERE `id_customer` = '".$enk['idr']."'";
		$exdata = $conFiles->getRecord($sqlexdata);
		$newdata = "";
		if($exdata && $exdata['dokumen_lainnya_file'] && $deleted_file) {
			$newdata = str_replace(",".$deleted_file,"",$exdata['dokumen_lainnya_file']);
			$newdata = str_replace($deleted_file,"",$exdata['dokumen_lainnya_file']);
		}
		$sql1 = "update pro_customer set ".$arrImage[str_replace("_del","",$file)]." = '".$newdata."' where id_customer = '".$idr."'";
		$conFiles->setQuery($sql1);
		$oke  = $oke && !$conFiles->hasError();
		if(count($arrFiles) > 0){
			foreach($arrFiles as $data){
				$oke = unlink($data);
			}
		}
		if ($oke){
			$conFiles->commit();
			$conFiles->close();
			$resp["error"] = "";
		} else{
			$conFiles->rollBack();
			$conFiles->clearError();
			$conFiles->close();
			$resp["error"] = "File tidak dapat dihapus";
		}
	} else{
		$resp = array();
		$resp["error"] = "File is missing!";
	}
	echo json_encode($resp);
?>

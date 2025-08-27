<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	if(isset($_POST) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
		$enk  	= decode(BASE_REFERER);
		$enk['idr'] = (paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 10 && isset($_SESSION['sinori'.SESSIONID]['id_customer']) ? $_SESSION['sinori'.SESSIONID]['id_customer'] : $enk['idr']);
		$ktg  	= htmlspecialchars($_POST["kategori"],ENT_QUOTES);
		$prefix	= $ktg.$enk["idr"]."_";

		$file_name	= htmlspecialchars($_FILES["image_file"]["name"],ENT_QUOTES);
		$file_temp  = htmlspecialchars($_FILES["image_file"]["tmp_name"],ENT_QUOTES); 
		$file_size 	= htmlspecialchars($_FILES["image_file"]["size"],ENT_QUOTES);
		$file_type 	= htmlspecialchars($_FILES["image_file"]["type"],ENT_QUOTES);
		$file_error = htmlspecialchars($_FILES["image_file"]["error"],ENT_QUOTES);
		$file_path	= $public_base_directory."/files/uploaded_user/images";
		$max_size1	= 2 * 1024 * 1024;
		$max_size2	= 10 * 1024 * 1024;
		$allow_type	= array(".jpg", ".jpeg", ".png", ".gif", ".pdf", ".zip", ".rar");
		$ext 		= substr($file_name,strrpos($file_name,'.'));
		$response	= array();
		$conImage = new Connection();
		if($ktg == "dokumen_lainnya_file") {
			$sqlexdata = "SELECT * FROM `pro_customer` WHERE `id_customer` = '".$enk['idr']."'";
			$exdata = $conImage->getRecord($sqlexdata);
		}
		if($file_error){
			$response["error"] 	= $file_error;
			$response["answer"] = "";
		} if($file_name == "" || !is_uploaded_file($file_temp)){
			$response["error"] 	= $file_error;
			$response["answer"] = "";
		} else if($ktg != "sert_file" && $file_size > $max_size1){
			$response["error"] 	= "Maksimal ukuran file 2MB";
			$response["answer"] = "";
		} else if($ktg == "sert_file" && $file_size > $max_size2){
			$response["error"] 	= "Maksimal ukuran file 10MB";
			$response["answer"] = "";
		} else if(!in_array(strtolower($ext), $allow_type)){
			$response["error"] 	= "Extensi file tidak diperbolehkan";
			$response["answer"] = "";
		} else{
            $arrFiles = glob($public_base_directory."/files/uploaded_user/images/".$prefix."*.{jpg,jpeg,gif,png,pdf,zip,rar}", GLOB_BRACE);
			if(count($arrFiles) > 0){
				$compare = 0;
				$alldokumen_link = array();
				$alldokumenraw = array();
				if($ktg == "dokumen_lainnya_file") {
					if($exdata && $exdata['dokumen_lainnya_file']) {
						$alldokumenraw = explode(",", $exdata['dokumen_lainnya_file']);
						for($i = 0; $i < count($alldokumenraw); $i++) {
							$alldokumen_link[] = $public_base_directory."/files/uploaded_user/images/".$prefix.$alldokumenraw[$i];
						}
						$compare = 1;
					}
				}
				foreach($arrFiles as $data){
					if($compare === 0 || !in_array($data, $alldokumen_link)) {
						unlink($data);
					}
				}
			}
			switch(strtolower($file_type)){
				case 'image/png':
					$image 	= imagecreatefrompng($file_temp);
					$oke	= false;
					break;
				case 'image/gif':
					$image 	= imagecreatefromgif($file_temp);
					$oke	= false;
					break;			
				case 'image/jpeg': 
				case 'image/pjpeg':
					$image 	= imagecreatefromjpeg($file_temp);
					$oke	= false;
					break;
				default:
					$image 	= false;
					$oke	= false;
					$icon	= "";
					break;
			}
			if($image){
				$tujuan = $file_path."/".sanitize_filename($prefix.$file_name);
				$oke 	= save_image($image, $tujuan, $file_type);
				imagedestroy($image);
				if(file_exists($file_temp)) unlink($file_temp);
			} else{
				$tujuan = $file_path."/".sanitize_filename($prefix.$file_name);
				$oke 	= move_uploaded_file($file_temp, $tujuan);
				if(file_exists($file_temp)) unlink($file_temp);				
			}
			if($oke){
				if($ktg == "dokumen_lainnya_file") {
					if($exdata && $exdata['dokumen_lainnya_file']) {
						$filenamedb = $exdata['dokumen_lainnya_file'].','.sanitize_filename($file_name);
						$filename = sanitize_filename($file_name);
					}else{
						$filename = sanitize_filename($file_name);
						$filenamedb = $filename;
					}
				}else{
					$filename = sanitize_filename($file_name);
					$filenamedb = $filename;
				}
				$arrImage = array("sert_file"=>"nomor_sertifikat_file","npwp_file"=>"nomor_npwp_file","siup_file"=>"nomor_siup_file","tdp_file"=>"nomor_tdp_file","dokumen_lainnya_file"=>"dokumen_lainnya_file");
				$sqlImage = "update pro_customer set ".$arrImage[$ktg]." =  '".$filenamedb."' where id_customer = '".$enk['idr']."'";
				$conImage->setQuery($sqlImage);

				if(!$conImage->hasError()){
					$linkDownload = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=1&ktg=".$prefix."&file=".$filename);
					$response["error"] 	= "";
					$response["answer"] = '<a href="'.$linkDownload.'">'.str_replace("_", " ", $filename).'</a><span class="'.$ktg.'_del" '.($ktg == "dokumen_lainnya_file" ? 'data-source="'.$filename.'"' : '').'><i class="fa fa-times"></i></span>';
					$conImage->close();
				} else{
					$response["error"] 	= "Gagal Upload..";
					$response["answer"] = "";
					$conImage->clearError();
					$conImage->close();
					if(file_exists($tujuan)) unlink($tujuan);
				}
			} else{
				$response["error"] 	= "Gagal Upload..";
				$response["answer"] = "";
			}
		}
	} else{
		$response = array();
		$response["error"] = "File is missing!";
	}
	echo json_encode($response);
?>

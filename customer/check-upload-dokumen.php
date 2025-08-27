<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once($public_base_directory."/libraries/helper/load.php");
	require_once($public_base_directory."/customer/botdetect.php");
	load_helper("autoload");

	$ExampleCaptcha = new Captcha("ExampleCaptcha");
	$ExampleCaptcha->UserInputID = "CaptchaCode";
	$ExampleCaptcha->SoundEnabled = true; 
	$ExampleCaptcha->ReloadEnabled = true;
	$ExampleCaptcha->ImageWidth = 230;
	$Captcha = $ExampleCaptcha->Html();

	if(isset($_POST) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
		$enk  	= decode(BASE_REFERER);
		$idr  	= htmlspecialchars($enk["idr"],ENT_QUOTES); 
		$sert  	= isset($_POST['sert'])?htmlspecialchars($_POST["sert"],ENT_QUOTES):null; 
		$npwp  	= isset($_POST['npwp'])?htmlspecialchars($_POST["npwp"],ENT_QUOTES):null; 
		$siup  	= isset($_POST['siup'])?htmlspecialchars($_POST["siup"],ENT_QUOTES):null; 
		$tdpn  	= isset($_POST['tdpn'])?htmlspecialchars($_POST["tdpn"],ENT_QUOTES):null; 
		$dokumen_lainnya = isset($_POST['dokumen_lainnya'])?htmlspecialchars($_POST["dokumen_lainnya"],ENT_QUOTES):null; 
		
		$path	= $public_base_directory."/files/uploaded_user/images";
		$img1	= "/sert_file".$idr."_".sanitize_filename($sert);
		$img2	= "/npwp_file".$idr."_".sanitize_filename($npwp);
		$img3	= "/siup_file".$idr."_".sanitize_filename($siup);
		$img4	= "/tdp_file".$idr."_".sanitize_filename($tdpn);
		$img5	= "/dokumen_lainnya_file".$idr."_".sanitize_filename($dokumen_lainnya);
		$resp 	= array();

		/*} else if(isset($_POST["sert"]) && !file_exists($path.$img1)){
			$resp["error"] 	= "Dokumen sertifikat belum diupload...";
		} else if(isset($_POST["dokumen_lainnya"]) && !file_exists($path.$img5)){
			$resp["error"] 	= "Dokumen Dokumen Lainnya belum diupload...";*/

		if(isset($_POST["npwp"]) && !file_exists($path.$img2)){
			$resp["error"] 		= "Dokumen NPWP belum diupload...";
			$resp["captcha"] 	= $Captcha;
		} else if(isset($_POST["siup"]) && !file_exists($path.$img3)){
			$resp["error"] 		= "Dokumen SIUP belum diupload...";
			$resp["captcha"] 	= $Captcha;
		} else if(isset($_POST["tdpn"]) && !file_exists($path.$img4)){
			$resp["error"] 		= "Dokumen TDP belum diupload...";
			$resp["captcha"] 	= $Captcha;
		} else{
			$resp["error"] 		= "";
			$resp["captcha"] 	= "";
		}
	} else{
		$resp = array();
		$resp["error"] 		= "File is missing!";
		$resp["captcha"] 	= $Captcha;
	}

	echo json_encode($resp);
?>

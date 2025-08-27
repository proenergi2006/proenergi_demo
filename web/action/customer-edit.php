<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload", "htmlawed");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$act	= htmlspecialchars($_POST["act"], ENT_QUOTES);

	$nama 		= htmlspecialchars($_POST["nama_customer"], ENT_QUOTES);	
	$email		= htmlspecialchars($_POST["email_customer"], ENT_QUOTES);	
	$alamat		= htmlspecialchars($_POST["alamat_customer"], ENT_QUOTES);	
	$jenis_customer		= htmlspecialchars($_POST["jenis_customer"], ENT_QUOTES);	
	$propinsi	= htmlspecialchars($_POST["prov_customer"], ENT_QUOTES);	
	$kabupaten	= htmlspecialchars($_POST["kab_customer"], ENT_QUOTES);	
	$telepon	= htmlspecialchars($_POST["telp_customer"], ENT_QUOTES);	
	$fax		= htmlspecialchars($_POST["fax_customer"], ENT_QUOTES);	
	$marketing	= htmlspecialchars($_POST["marketing"], ENT_QUOTES);	
	$idr		= htmlspecialchars($_POST["idr"], ENT_QUOTES);	
	$server_uri	= htmlspecialchars($_POST["server_uri"], ENT_QUOTES);	
	$postalcode_customer	= htmlspecialchars($_POST["postalcode_customer"], ENT_QUOTES);	

	// echo json_encode($_POST); die();

	if($marketing == "" || $nama == "" || $email == "" || $alamat == "" || $propinsi == "" || $kabupaten == "" || $telepon == ""){
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else if($email != "" && !filter_var($email, FILTER_VALIDATE_EMAIL)){
		$con->close();
		$flash->add("error", "Alamat email tidak benar", BASE_REFERER);
	} else{
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		$sql1 = "update pro_customer set nama_customer = '".$nama."', alamat_customer = '".$alamat."', prov_customer = '".$propinsi."', kab_customer = '".$kabupaten."', jenis_customer = '".$jenis_customer."', postalcode_customer = '".$postalcode_customer."', telp_customer = '".$telepon."', fax_customer = '".$fax."', email_customer = '".$email."', lastupdate_time = NOW() where id_customer = '".$idr."'";
		$res1 = $con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();

		$url  = BASE_URL_CLIENT."/customer-detail.php?".$server_uri;

		if ($oke){
			$con->commit();
			$con->close();
			header("location: ".$url);	
			exit();
		} else{
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
	}
?>

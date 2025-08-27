<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$conSub = new Connection();
	$id1 	= htmlspecialchars($_POST["id1"], ENT_QUOTES);

	$response 		= null;
	$arrResponse 	= array();

    $sql01 = "select distinct nomor_plat from pro_master_transportir_mobil where 1=1 and link_gps = 'OSLOG' and nomor_plat = '".$id1."'";
	$res01 = $conSub->getResult($sql01);
	
	if(count($res01) > 0){
		$url01 = "https://oslog.id/apiv5/open-api/current-vehicle-status?apiKey=3549af8b-2607-4415-8d0d-09130e2e4c29&licensePlate=";	
		$tmp01 = file_get_contents($url01.urlencode($res01[0]['nomor_plat']));
		//echo $url01.urlencode($res01[0]['nomor_plat']); exit;
		if($tmp01){
			array_push($arrResponse, json_decode($tmp01, true));
		}
		if(count($arrResponse) > 0){
			$answer['hasil'] = true;
			$answer["items"] = $arrResponse;
			$response = json_encode($arrResponse);
		}
	} else{
		$answer['hasil'] = false;
		$answer["items"] = '
			<h1 style="font-size:16px; margin:0px 0px 15px; text-decoration:underline 2px; text-underline-offset:4px;">
				<b>Maaf kendaraan ini belum diregistrasi untuk dilakukan tracking</b>
			</h1>
			<p style="margin-bottom:5px;">Kendaraan yang dapat dilakukan tracking adalah kendaraan yang telah didaftarkan di OSLOG</p> 
			<p style="margin-bottom:5px;">Setelah terdaftar di OSLOG, lakukan penyesuaian data di aplikasi SYOP pada menu Referensi Data -> Transportir -> Mobil.</p>
			<p style="margin-bottom:5px;">Pada kolom [Link GPS], isi dengan kata "OSLOG" (Tanpa tanda petik).</p>
		';
	}

	$conSub->close();
	echo json_encode($answer);

?>

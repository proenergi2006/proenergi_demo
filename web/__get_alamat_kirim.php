<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$conSub = new Connection();
	$q1 	= htmlspecialchars($_POST["q1"], ENT_QUOTES);
	$q2 	= htmlspecialchars($_POST["q2"], ENT_QUOTES);

	$sql = "select a.id_lcr, a.alamat_survey, a.latitude_lokasi, a.longitude_lokasi, b.nama_prov, c.nama_kab 
			from pro_customer_lcr a join pro_master_provinsi b on a.prov_survey = b.id_prov join pro_master_kabupaten c on a.kab_survey = c.id_kab 
			where a.flag_approval = 1 and a.id_customer = '".$q1."' and a.id_wilayah = '".$q2."'";
	$res = $conSub->getResult($sql);
    $ans = array();
	if($res != null){
		foreach($res as $data){
            $answer["items"][] = array("id"=>$data['id_lcr'], 
										"alamat"=>$data['alamat_survey']." ".str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab'])." ".$data['nama_prov'], 
										"koordinat"=>$data['latitude_lokasi'].", ".$data['longitude_lokasi']
									);
        }
    } else {
        $answer["items"] = array();
    }
	$conSub->close();
    echo json_encode($answer);
?>

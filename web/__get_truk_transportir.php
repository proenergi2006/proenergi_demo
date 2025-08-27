<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$conSub = new Connection();
	$angkut = htmlspecialchars($_POST["q1"], ENT_QUOTES);
    $answer = array();

	$subSql1 = "select distinct id_master, nomor_plat from pro_master_transportir_mobil where is_active = 1 and id_transportir = '".$angkut."'";
	$subRes1 = $conSub->getResult($subSql1);
	$answer["items1"][] = array("id"=>'', "text"=>'');
	if(count($subRes1) > 0){
		foreach($subRes1 as $data1){
            $answer["items1"][] = array("id"=>$data1['id_master'], "text"=>$data1['nomor_plat']);
        }
    }

	$subSql2 = "select distinct id_master, nama_sopir from pro_master_transportir_sopir where is_active = 1 and id_transportir = '".$angkut."'";
	$subRes2 = $conSub->getResult($subSql2);
	$answer["items2"][] = array("id"=>'', "text"=>'');
	if(count($subRes2) > 0){
		foreach($subRes2 as $data2){
            $answer["items2"][] = array("id"=>$data2['id_master'], "text"=>$data2['nama_sopir']);
        }
    }

    echo json_encode($answer);
	$conSub->close();
?>

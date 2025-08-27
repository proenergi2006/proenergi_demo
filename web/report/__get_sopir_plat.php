<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$conSub = new Connection();
    $answer	= array();
	$q1 = isset($_POST["q1"])?htmlspecialchars($_POST["q1"], ENT_QUOTES):'';

	$sql1 = "select * from pro_master_transportir_mobil where id_transportir = '".$q1."'";
	$res1 = $conSub->getResult($sql1);
	$answer["plat"][] = array("id"=>'', "text"=>'');
	if(count($res1) > 0){
		foreach($res1 as $data1){
            $answer["plat"][] = array("id"=>$data1['id_master'], "text"=>$data1['nomor_plat']);
        }
    }

	$sql2 = "select * from pro_master_transportir_sopir where id_transportir = '".$q1."'";
	$res2 = $conSub->getResult($sql2);
	$answer["sopir"][] = array("id"=>'', "text"=>'');
	if(count($res2) > 0){
		foreach($res2 as $data2){
            $answer["sopir"][] = array("id"=>$data2['id_master'], "text"=>$data2['nama_sopir']);
        }
    }
    echo json_encode($answer);
	$conSub->close();
?>

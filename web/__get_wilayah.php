<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$conSub = new Connection();
	
	$q1 	= htmlspecialchars($_POST["q1"], ENT_QUOTES);
	
	if($q1=='6'){
		$sql	= "select id_gu, group_wilayah from pro_master_group_cabang where id_gu <> 1";
	}else{
		$sql	= "select id_master as id_gu, nama_cabang as group_wilayah from pro_master_cabang";
	}

	$result = $conSub->getResult($sql);
    $answer	= array();
	$answer["items"][] = array("id"=>'', "text"=>'');
	if(count($result) > 0){
		foreach($result as $data){
            $answer["items"][] = array("id"=>$data['id_gu'], "text"=>$data['group_wilayah']);
        }
    }
    echo json_encode($answer);
	$conSub->close();
?>

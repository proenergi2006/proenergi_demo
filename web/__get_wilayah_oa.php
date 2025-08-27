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

	$sql	= "select * from pro_master_wilayah_angkut where id_prov = '".$q1."' and id_kab = '".$q2."'";
	$result = $conSub->getResult($sql);
    $answer	= array();
	$answer["items"][] = array("id"=>'', "text"=>'');
	if(count($result) > 0){
		foreach($result as $data){
            $answer["items"][] = array("id"=>$data['id_master'], "text"=>$data['wilayah_angkut']);
        }
    }
    echo json_encode($answer);
	$conSub->close();
?>

<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$conSub = new Connection();
	$q1 	= isset($_POST["q1"])?htmlspecialchars($_POST["q1"], ENT_QUOTES):'';
	$q2 	= isset($_POST["q2"])?htmlspecialchars($_POST["q2"], ENT_QUOTES):'';

	$sql	= "select * from pro_master_kabupaten where id_prov = '".$q1."'";
	if($q2 != "")
		$sql .= " and upper(nama_kab) like '%".strtoupper($q2)."%'";

	$result = $conSub->getResult($sql);
    $answer	= array();
	$answer["items"][] = array("id"=>'', "text"=>'');
	if(count($result) > 0){
		foreach($result as $data){
            $answer["items"][] = array("id"=>$data['id_kab'], "text"=>$data['nama_kab']);
        }
    }
    echo json_encode($answer);
	$conSub->close();
?>

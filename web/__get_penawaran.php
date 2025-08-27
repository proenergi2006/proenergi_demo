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

	$sql = "select id_penawaran, lpad(id_penawaran,4,'0') as kode_penawaran, gelar, nama_up from pro_penawaran where id_customer = '".$q1."' and flag_approval = 1";
	if($q2 != "")
		$sql .= " and lpad(id_penawaran,4,'0') like '".$q2."%'";

	$result = $conSub->getResult($sql);
    $answer	= array();
	if($result != null){
		foreach($result as $data){
            $answer["items"][] = array("id"=>$data['id_penawaran'], "text"=>$data['kode_penawaran']);
        }
    } else {
        $answer["items"] = "";
    }
	$conSub->close();
    echo json_encode($answer);
?>

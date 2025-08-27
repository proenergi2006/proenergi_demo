<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$act 	= htmlspecialchars($_POST["act"], ENT_QUOTES);
	$data	= explode("#", htmlspecialchars($_POST["data"], ENT_QUOTES));
	$jum	= 0;
	
	$data_order	= $data[0];
	$data_level	= $data[1];
	$data_tree	= $data[2];
	$answer 	= array();
	
	if($data_level == 1){
		$sql1 	= "select menu_order from acl_menu where menu_level = ".$data_level." order by menu_order";
		$res1 	= $con->getResult($sql1);
		$arrData1 = array();
		foreach($res1 as $data1){
			array_push($arrData1, $data1['menu_order']);
		}
		
		if($act == "up"){
			$func1 	= "min";
			$pesan	= "Menu sudah berada ditingkat teratas";
		} else if($act == "down"){
			$func1 	= "max";
			$pesan	= "Menu sudah berada ditingkat terbawah";
		}
		
		$temp_sort1 = $func1($arrData1);
		if($data_order == $temp_sort1){
			$answer["error"][] = $pesan;
		} else{
			$tData 	= $data_order;
			$keys 	= array_search($tData, $arrData1);
			unset($arrData1[$keys]);
			
			if($act == "up"){
				$temp_sort2 = $arrData1[$keys - 1];
			} else if($act == "down"){
				$temp_sort2 = $arrData1[$keys + 1];
			}

			$con->beginTransaction();
			$move = true;
			$dTre = $con->getOne("select menu_tree from acl_menu where menu_order = '".$temp_sort2."'");

			$sql2 = "update acl_menu set menu_order = '".$temp_sort2."' where menu_tree = '".$data_tree."'";
			$res2 = $con->setQuery($sql2);
			$move = $move && !$con->hasError();
			$con->clearError();
			
			$sql3 = "update acl_menu set menu_order = '".$tData."' where menu_tree = '".$dTre."'";
			$res3 = $con->setQuery($sql3);
			$move = $move && !$con->hasError();
			$con->clearError();

			$sql4 = "
				update acl_menu a join acl_menu b on a.id_menu = b.id_menu 
				set a.menu_order = concat_ws('.', '".$temp_sort2."', substring_index(b.menu_order, '.', -(b.menu_level-1))) 
				where a.menu_tree in (select * from(select menu_tree from acl_menu where menu_tree like '".$data_tree.".%') tmp)";
			$res4 = $con->setQuery($sql4);
			$move = $move && !$con->hasError();
			$con->clearError();

			$sql5 = "
				update acl_menu a join acl_menu b on a.id_menu = b.id_menu 
				set a.menu_order = concat_ws('.', '".$tData."', substring_index(b.menu_order, '.', -(b.menu_level-1))) 
				where a.menu_tree in (select * from(select menu_tree from acl_menu where menu_tree like '".$dTre.".%') tmp)";
			$res5 = $con->setQuery($sql5);
			$move = $move && !$con->hasError();
			$con->clearError();

			if($move){
				$con->commit();
				$answer["error"] = "";
			} else{
				$con->rollBack();
				$answer["error"] = "Sistem mengalami gangguan teknis, hubungi administrator";
			}
		}
	} else if($data_level == 2){
		$vdi 	= explode(".", $data_order);
		$parent	= substr($data_tree,0,strrpos($data_tree,'.'));
		$dt_pnt = $vdi[0];
		$sql1 	= "select menu_order from acl_menu where menu_level = ".$data_level." and menu_parent = '".$parent."' order by menu_order";
		$res1 	= $con->getResult($sql1);
		$arrData1 = array();
		foreach($res1 as $data1){
			$temp = explode(".", $data1['menu_order']);
			array_push($arrData1, $temp[1]);
		}
		/*echo $sql1;
		print_r($arrData1); 
		$con->close();		
		exit;*/
		
		if($act == "up"){
			$func1 	= "min";
			$pesan	= "Menu sudah berada ditingkat teratas";
		} else if($act == "down"){
			$func1 	= "max";
			$pesan	= "Menu sudah berada ditingkat terbawah";
		}
		
		$temp_sort1 = $func1($arrData1);
		if($data_order == $dt_pnt.".".$temp_sort1){
			$answer["error"][] = $pesan;
		} else{
			$tData 	= explode(".", $data_order)[1];
			$keys 	= array_search($tData, $arrData1);
			unset($arrData1[$keys]);
			
			if($act == "up"){
				$temp_sort2 = $arrData1[$keys - 1];
			} else if($act == "down"){
				$temp_sort2 = $arrData1[$keys + 1];
			}

			$con->beginTransaction();
			$move = true;
			$dTre = $con->getOne("select menu_tree from acl_menu where menu_order = '".$dt_pnt.".".$temp_sort2."'");

			$sql2 = "update acl_menu set menu_order = '".$dt_pnt.".".$temp_sort2."' where menu_tree = '".$data_tree."'";
			$res2 = $con->setQuery($sql2);
			$move = $move && !$con->hasError();
			$con->clearError();
			
			$sql3 = "update acl_menu set menu_order = '".$dt_pnt.".".$tData."' where menu_tree = '".$dTre."'";
			$res3 = $con->setQuery($sql3);
			$move = $move && !$con->hasError();
			$con->clearError();

			$sql4 = "
				update acl_menu a join acl_menu b on a.id_menu = b.id_menu 
				set a.menu_order = concat_ws('.', '".$dt_pnt.".".$temp_sort2."', substring_index(b.menu_order, '.', -1)) 
				where a.menu_tree in (select * from(select menu_tree from acl_menu where menu_tree like '".$data_tree.".%') tmp)";
			$res4 = $con->setQuery($sql4);
			$move = $move && !$con->hasError();
			$con->clearError();

			$sql5 = "
				update acl_menu a join acl_menu b on a.id_menu = b.id_menu 
				set a.menu_order = concat_ws('.', '".$dt_pnt.".".$tData."', substring_index(b.menu_order, '.', -1)) 
				where a.menu_tree in (select * from(select menu_tree from acl_menu where menu_tree like '".$dTre.".%') tmp)";
			$res5 = $con->setQuery($sql5);
			$move = $move && !$con->hasError();
			$con->clearError();

			if($move){
				$con->commit();
				$answer["error"] = "";
			} else{
				$con->rollBack();
				$answer["error"] = "Sistem mengalami gangguan teknis, hubungi administrator";
			}
		}
	} else if($data_level == 3){
		$vdi 	= explode(".", $data_order);
		$dt_pnt = $vdi[0].".".$vdi[1];
		$sql1 	= "select menu_order from acl_menu where menu_level = ".$data_level." and menu_parent = '".$dt_pnt."' order by menu_order";
		$res1 	= $con->getResult($sql1);
		$arrData1 = array();
		foreach($res1 as $data1){
			$temp = explode(".", $data1['menu_order'])[2];
			array_push($arrData1, $temp);
		}
		
		if($act == "up"){
			$func1 	= "min";
			$pesan	= "Menu sudah berada ditingkat teratas";
		} else if($act == "down"){
			$func1 	= "max";
			$pesan	= "Menu sudah berada ditingkat terbawah";
		}
		
		$temp_sort1 = $func1($arrData1);
		if($data_order == $dt_pnt.".".$temp_sort1){
			$answer["error"][] = $pesan;
		} else{
			$tData 	= explode(".", $data_order)[2];
			$keys 	= array_search($tData, $arrData1);
			unset($arrData1[$keys]);
			
			if($act == "up"){
				$temp_sort2 = $arrData1[$keys - 1];
			} else if($act == "down"){
				$temp_sort2 = $arrData1[$keys + 1];
			}

			$con->beginTransaction();
			$move = true;

			$dTre = $con->getOne("select menu_tree from acl_menu where menu_order = '".$dt_pnt.".".$temp_sort2."'");
			$sql2 = "update acl_menu set menu_order = '".$dt_pnt.".".$temp_sort2."' where menu_tree = '".$data_tree."'";
			$res2 = $con->setQuery($sql2);
			$move = $move && !$con->hasError();
			$con->clearError();
			
			$sql3 = "update acl_menu set menu_order = '".$dt_pnt.".".$tData."' where menu_tree = '".$dTre."'";
			$res3 = $con->setQuery($sql3);
			$move = $move && !$con->hasError();
			$con->clearError();

			if($move){
				$con->commit();
				$answer["error"] = "";
			} else{
				$con->rollBack();
				$answer["error"] = "Sistem mengalami gangguan teknis, hubungi administrator";
			}
		}
	}

	$con->close();
	echo json_encode($answer);
?>
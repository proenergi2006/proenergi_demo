<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$draw 	= isset($_POST["element"])?htmlspecialchars($_POST["element"], ENT_QUOTES):0;
	$start 	= isset($_POST["start"])?htmlspecialchars($_POST["start"], ENT_QUOTES):0;
	$length	= isset($_POST['length'])?htmlspecialchars($_POST["length"], ENT_QUOTES):10;
	$q1	= isset($_POST["q1"])?htmlspecialchars($_POST["q1"], ENT_QUOTES):'';
	$q2	= isset($_POST["q2"])?htmlspecialchars($_POST["q2"], ENT_QUOTES):'';
	$q3	= isset($_POST["q3"])?htmlspecialchars($_POST["q3"], ENT_QUOTES):'';
	
	$p = new paging;
	$sql = "select * from pro_master_area where 1=1";
	
	if($q1 != "")
		$sql .= " and (upper(nama_area) like '%".strtoupper($q1)."%')";
	if($q2 != "" && $q2 != 2)
		$sql .= " and is_active = '".$q2."'";
	if($q3 != "")
		$sql .= " and wapu = '".$q3."'";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by is_active, id_master limit ".$position.", ".$length;

	$content = "";
	if($tot_record <= 0){
		$content .= '<tr><td colspan="4" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$linkEdit	= BASE_URL_CLIENT.'/add-master-area.php?'.paramEncrypt('idr='.$data['id_master']);
			$linkHapus	= paramEncrypt("master_area#|#".$data['id_master']);
			$active		= ($data["is_active"] == 1)?"Active":"Not Active";

			$pathPt 	= $public_base_directory.'/files/uploaded_user/lampiran/'.$data['lampiran'];
			$lampPt 	= $data['lampiran_ori'];

			if($data['lampiran'] && file_exists($pathPt)){
				$linkPt = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=2&ktg=areaLamp_".$data['id_master']."_&file=".$lampPt);
				$attach = '<a href="'.$linkPt.'"><i class="fa fa-file-alt" title="'.$lampPt.'"></i></a>';
			} else {$attach = '-';}

        	$content .= '
				<tr class="clickable-row" data-href="'.$linkEdit.'">
					<td class="text-center">'.$count.'</td>
					<td>'.$data['nama_area'].'</td>
					<td class="text-center">'.$data['wapu'].'</td>
					<td class="text-center">'.$attach.'</td>
					<td class="text-center">'.$active.'</td>
					<td class="text-center action">
						<a class="margin-sm btn btn-action btn-info" title="Edit" href="'.$linkEdit.'"><i class="fa fa-edit"></i></a>
						<a class="margin-sm delete btn btn-action btn-danger" title="Delete" data-param-idx="'.$linkHapus.'" data-action="deleteGrid">
						<i class="fa fa-trash"></i></a>
            		</td>
				</tr>';
		} 
	} 

	$json_data = array(
					"items"		=> $content,
					"pages"		=> $tot_page,
					"page"		=> $page,
					"totalData"	=> $tot_record,
					"infoData"	=> "Showing ".($position+1)." to ".$count." of ".$tot_record." entries",
				);
	echo json_encode($json_data);
?>

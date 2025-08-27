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
	
	$p = new paging;
	$sql = "select a.* from pro_master_cabang a where 1=1 and is_active = 1";

	if($q1 != "")
		$sql .= " and (a.nama_cabang like '%".$q1."%')";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by a.id_master asc limit ".$position.", ".$length;
	
	$content = "";
	$count = 0;
	if($tot_record ==  0){
		$content .= '<tr><td colspan="9" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$linkView 	= BASE_URL_CLIENT.'/generate-number-edit.php?'.paramEncrypt('idr='.$data['id_master']);

        	$content .= '
				<tr class="clickable-row" data-href="'.$linkView.'>
					<td class="text-center"></td>
					<td class="text-center">'.$count.'</td>
					<td>
						<p style="margin-bottom:0px">'.$data['nama_cabang'].'</p>
					</td>
					<td>
						<p style="margin-bottom:0px">'.((int)$data['urut_spj']+1).'</p>
					</td>
					<td>
						<p style="margin-bottom:0px">'.((int)$data['urut_dn']+1).'</p>
					</td>
					<td>
						<p style="margin-bottom:0px">'.((int)$data['urut_po']+1).'</p>
					</td>
					<td class="text-center">
						<a class="btn btn-action btn-warning" href="'.$linkView.'" style="margin-right:3px;"><i class="fa fa-edit"></i></a>
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

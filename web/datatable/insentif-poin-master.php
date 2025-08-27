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
	$sql = "
			SELECT a.JENIS_PELUNASAN,
				    a.RANGE_AWAL,
				    a.RANGE_AKHIR,
				    a.TIER,
				    a.POIN,
				    a.PETUGAS_REKAM,
				    a.TGL_REKAM,
				    a.id_master
				FROM pro_master_poin_insentif a
			where 
				1=1 
	";

	if($q1 != "")
		$sql .= " and (or a.JENIS_PELUNASAN LIKE '%".$q1."%'
					or a.RANGE_AWAL LIKE '%".$q1."%'
					or a.RANGE_AKHIR LIKE '%".$q1."%'
					or a.TIER LIKE '%".$q1."%'
					or a.POIN LIKE '%".$q1."%')";


	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by a.JENIS_PELUNASAN asc, RANGE_AWAL asc limit ".$position.", ".$length;
	
	
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
			$linkView 	= BASE_URL_CLIENT.'/insentif-poin-master-edit.php?'.paramEncrypt('id='.$data['id_master']);
			$linkHapus	= paramEncrypt("master_insentif_poin#|#".$data['id_master']);

        	$content .= '
				<tr class="clickable-row" data-href="'.$linkView.'>
					<td class="text-center"></td>
					<td class="text-center">'.$data['JENIS_PELUNASAN'].'</td>
					<td class="text-center">'.$data['RANGE_AWAL'].'</td>
					<td class="text-center">'.$data['RANGE_AKHIR'].'</p></td>
					<td class="text-center">'.$data['TIER'].'</td>
					<td class="text-center">'.$data['POIN'].'</td>
					<td class="text-center">
						<a class="btn btn-action btn-warning" href="'.$linkView.'" style="margin-right:3px;"><i class="fa fa-edit"></i></a>
						<a class="margin-sm btn btn-action btn-danger " title="Delete" data-param-idx="'.$linkHapus.'" data-action="deleteGrid">
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

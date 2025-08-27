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
	$sql = "select a.*, b.group_wilayah from pro_master_cabang a join pro_master_group_cabang b on a.id_group_cabang = b.id_gu where 1=1";
	
	if($q1 != "")
		$sql .= " and (upper(a.nama_cabang) like '%".strtoupper($q1)."%' or upper(a.inisial_cabang) like '%".strtoupper($q1)."%')";
	if($q2 != "" && $q2 != 2)
		$sql .= " and a.is_active = '".$q2."'";
	if($q3 != "")
		$sql .= " and a.id_group_cabang = '".$q3."'";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by a.is_active, a.id_master limit ".$position.", ".$length;

	$content = "";
	if($tot_record <= 0){
		$content .= '<tr><td colspan="7" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$linkDetail	= BASE_URL_CLIENT.'/detil-master-cabang.php?'.paramEncrypt('idr='.$data['id_master']);
			$linkEdit	= BASE_URL_CLIENT.'/add-master-cabang.php?'.paramEncrypt('idr='.$data['id_master']);
			$linkHapus	= paramEncrypt("master_cabang#|#".$data['id_master']);
			$active		= ($data["is_active"] == 1)?"Active":"Not Active";
        	$content .= '
				<tr class="clickable-row" data-href="'.$linkDetail.'">
					<td class="text-center">'.$count.'</td>
					<td>'.$data['nama_cabang'].'</td>
					<td class="text-center">'.$data['inisial_cabang'].'</td>
					<td>'.$data['group_wilayah'].'</td>
					<td class="text-center">'.$data['kode_barcode'].'</td>
					<td class="text-right">'.($data['stok_segel']?$data['stok_segel']:'').'</td>
					<td class="text-center">'.$active.'</td>
					<td class="text-center action">
						<a class="margin-sm btn btn-action btn-info" title="Detil" href="'.$linkDetail.'"><i class="fa fa-info-circle"></i></a>
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

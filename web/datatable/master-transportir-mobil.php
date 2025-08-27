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
	$q4	= isset($_POST["q3"])?htmlspecialchars($_POST["q4"], ENT_QUOTES):'';
	$q5	= isset($_POST["q3"])?htmlspecialchars($_POST["q5"], ENT_QUOTES):'';
	
	$p = new paging;
	$sql = "select a.*, b.nama_transportir, b.nama_suplier, b.lokasi_suplier from pro_master_transportir_mobil a join pro_master_transportir b on a.id_transportir = b.id_master ";
	
	if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 7)
	{
		$sql .= " join pro_master_cabang m on m.nama_cabang = b.lokasi_suplier ";
		$sql .=" where b.tipe_angkutan in (1,3) and m.id_master = ".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);	
	}
	else
		$sql .=" where b.tipe_angkutan in (1,3) ";
	
	if($q1 != "")
		$sql .= " and (upper(a.nomor_plat) like '%".strtoupper($q1)."%' OR a.max_kap = '".$q1."' )";
	if($q2 != "" && $q2 != 2)
		$sql .= " and a.is_active = '".$q2."'";
	if($q3 != "")
		$sql .= " and a.id_transportir = '".$q3."'";
	if($q4 != "")
		$sql .= " and b.lokasi_suplier = '".$q4."'";
	if($q5 != "")
		$sql .= " and a.max_kap = '".$q5."'";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by a.is_active, a.id_master desc limit ".$position.", ".$length;

	$content = "";
	if($tot_record <= 0){
		$content .= '<tr><td colspan="5" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$linkEdit	= BASE_URL_CLIENT.'/add-master-transportir-mobil.php?'.paramEncrypt('idr='.$data['id_master']);
			$linkDetail	= BASE_URL_CLIENT.'/detil-master-transportir-mobil.php?'.paramEncrypt('idr='.$data['id_master']);
			$linkHapus	= paramEncrypt("master_transportir_mobil#|#".$data['id_master']);
			$active		= ($data["is_active"] == 1)?"Active":"Not Active";

			$delete = '<a class="margin-sm delete btn btn-action btn-danger" title="Delete" data-param-idx="'.$linkHapus.'" data-action="deleteGrid"><i class="fa fa-trash"></i></a>';
			
			if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 7)
				$delete = '';

        	$content .= '
				<tr class="clickable-row" data-href="'.$linkDetail.'">
					<td class="text-center">'.$count.'</td>
					<td>'.$data['nomor_plat'].'</td>
					<td>'.$data['nama_suplier'].' - '.$data['nama_transportir'].', '.$data['lokasi_suplier'].'</td>
					<td class="text-center">'.($data['max_kap']?$data['max_kap'].' KL':'-').'</td>
					<td>'.$active.'</td>
					<td class="text-center action">
						<a class="margin-sm btn btn-action btn-info" title="Detail" href="'.$linkDetail.'"><i class="fa fa-info-circle"></i></a>
						<a class="margin-sm btn btn-action btn-info" title="Edit" href="'.$linkEdit.'"><i class="fa fa-edit"></i></a>
						'.$delete.'
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

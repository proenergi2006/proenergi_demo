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
	$length	= isset($_POST['length'])?htmlspecialchars($_POST["length"], ENT_QUOTES):25;
	$q1	= isset($_POST["q1"])?htmlspecialchars($_POST["q1"], ENT_QUOTES):'';
	$q2	= isset($_POST["q2"])?htmlspecialchars($_POST["q2"], ENT_QUOTES):'';
	$q3	= isset($_POST["q3"])?htmlspecialchars($_POST["q3"], ENT_QUOTES):'';
	
	$p = new paging;
	$sql = "select p.* from pro_master_transportir p";
	
	if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 7)
	{
		$sql .= " join pro_master_cabang m on m.nama_cabang = p.lokasi_suplier ";
		$sql .=" where  m.id_master = ".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);	
	}
	else
		$sql .=" where 1=1 ";

	if($q1 != "")
		$sql .= " and (upper(nama_transportir) like '%".strtoupper($q1)."%' or upper(nama_suplier) like '%".strtoupper($q1)."%')";
	if($q2 != "" && $q2 != 2)
		$sql .= " and p.is_active = '".$q2."'";
	if($q3 != ""){
		if($q3 == "1")
			$sql .= " and (tipe_angkutan = 1 or tipe_angkutan = 3)";
		else if($q3 == "2")
			$sql .= " and (tipe_angkutan = 2 or tipe_angkutan = 3)";
	}

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by p.is_active, p.id_master desc limit ".$position.", ".$length;
	// die($sql);
	$content = "";
	if($tot_record <= 0){
		$content .= '<tr><td colspan="8" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$attention 	= json_decode($data['att_suplier'], true);
			$linkEdit	= BASE_URL_CLIENT.'/add-master-transportir.php?'.paramEncrypt('idr='.$data['id_master']);
			$linkDetail	= BASE_URL_CLIENT.'/detil-master-transportir.php?'.paramEncrypt('idr='.$data['id_master']);
			$linkHapus	= paramEncrypt("master_transportir#|#".$data['id_master']);
			$active		= ($data["is_active"] == 1)?"Active":"Not Active";
			$fleet		= ($data["is_fleet"] == 1)?"Ya":"Tidak";
        	if($data["tipe_angkutan"] == 1)
				$angkutan = "Angkutan Pengiriman Truck";
        	else if($data["tipe_angkutan"] == 2)
				$angkutan = "Angkutan Pengiriman Kapal";
        	else if($data["tipe_angkutan"] == 3)
				$angkutan = "Angkutan Pengiriman Truck dan Kapal";

			$delete = '<a class="margin-sm delete btn btn-action btn-danger" title="Delete" data-param-idx="'.$linkHapus.'" data-action="deleteGrid"><i class="fa fa-trash"></i></a>';
			
			if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 7)
				$delete = '';

			$content .= '
				<tr class="clickable-row" data-href="'.$linkDetail.'">
					<td class="text-center">'.$count.'</td>
					<td>
						<p style="margin-bottom:0px;">'.$data['nama_suplier'].' - '.$data['nama_transportir'].'</p>
						<p style="margin-bottom:0px; font-size:12px;"><i>'.$angkutan.'</i></p>
					</td>
					<td>'.$data['lokasi_suplier'].'</td>
					<td>'.$data['telp_suplier'].'</td>
					<td>'.$data['fax_suplier'].'</td>
					<td>'.$fleet.'</td>
					<td>'.$active.'</td>
					<td class="text-center action">
						<a class="margin-sm btn btn-action btn-info" title="Detil" href="'.$linkDetail.'"><i class="fa fa-info-circle"></i></a>
						<a class="margin-sm btn btn-action btn-info" title="Detil" href="'.$linkEdit.'"><i class="fa fa-edit"></i></a>
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

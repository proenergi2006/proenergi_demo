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
    $cabang  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
	$q1	= isset($_POST["q1"])?htmlspecialchars($_POST["q1"], ENT_QUOTES):'';
	$id_user  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);
    $sesrole  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
    $seswilayah  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
    $sesgroup  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']);
	
	$p = new paging;
	$sql = "
		select 
			a.*,
			b.fullname as user_name,
			b.id_role as user_role
		from 
			pro_database_fuel a 
			join acl_user b on b.id_user = a.created_by
			join pro_master_area c on c.id_master = b.id_wilayah
		where 
			1=1 
			and a.is_mom = 0
			and a.deleted_time is null
	";

	if ($sesrole==6) 
		$sql .= " and b.id_group = ".$sesgroup;
	if ($sesrole==7) 
		$sql .= " and b.id_wilayah = ".$seswilayah;
	if ($sesrole==11)
		$sql .= " and b.id_user = ".$id_user;

	if($q1 != "") {
		$sql .= "
				 and
				(
					a.nama_customer like '%".$q1."%' or
					a.potensi_volume like '%".$q1."%' or
					a.potensi_waktu like '%".$q1."%' or
					a.tersuplai_jumlah_pengiriman like '%".$q1."%' or
					a.tersuplai_waktu like '%".$q1."%' or
					a.tersuplai_volume like '%".$q1."%' or
					a.sisa_potensi like '%".$q1."%' or
					a.kompetitor like '%".$q1."%' or
					a.harga_kompetitor like '%".$q1."%' or
					a.top like '%".$q1."%' or
					a.pic like '%".$q1."%' or
					a.kontak_email like '%".$q1."%' or
					a.kontak_phone like '%".$q1."%' or
					a.catatan like '%".$q1."%'
				)
				";
	}

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by a.created_time desc limit ".$position.", ".$length;
	
	$content = "";
	$count = 0;
	if($tot_record ==  0){
		$content .= '<tr><td colspan="16" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$linkView 	= BASE_URL_CLIENT.'/database-fuel-add.php?'.paramEncrypt('idr='.$data['id_database_fuel']);
			$linkDel	= paramEncrypt("database_fuel#|#".$data['id_database_fuel']);

        	$content .= '
				<tr class="clickable-row" data-href="'.$linkView.'">
					<td class="text-center">'.$count.'</td>
					<td>'.$data['nama_customer'].'</td>
					<td>'.number_format($data['potensi_volume']).'</td>
					<td>'.$data['potensi_waktu'].'</td>
					<td>'.$data['tersuplai_jumlah_pengiriman'].'</td>
					<td>'.$data['tersuplai_waktu'].'</td>
					<td>'.number_format($data['tersuplai_volume']).'</td>
					<td>'.$data['sisa_potensi'].'</td>
					<td>'.$data['kompetitor'].'</td>
					<td>'.number_format($data['harga_kompetitor']).'</td>
					<td>'.$data['top'].'</td>
					<td>'.$data['pic'].'</td>
					<td>'.$data['kontak_email'].'</td>
					<td>'.$data['kontak_phone'].'</td>
					<td>'.$data['catatan'].'</td>
				';
			if ($sesrole==11) {
				$content .= '
					<td class="text-center">
						<a class="btn btn-action btn-warning" href="'.$linkView.'" style="margin-right:3px;"><i class="fa fa-edit"></i></a>
					</td>
					<td class="text-center">
						<a class="btn btn-action btn-danger" data-param-idx="'.$linkDel.'" data-action="deleteGrid"><i class="fa fa-trash"></i></a>
            		</td>
				';
			}
			$content .= '</tr>';
		} 
	} 
	
	

	$json_data = array(
					"items"		=> $content,
					"pages"		=> $tot_page,
					"page"		=> $page,
					"totalData"	=> $tot_record,
					"infoData"	=> "Showing ".($position+1)." to ".$count." of ".$tot_record." entries",
				);
	//var_dump($json_data);exit;
	
	echo json_encode($json_data);
?>

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

	$sesuser 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);
    $seswil 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
    $sesgroup 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']);

	$q1	= isset($_POST["q1"])?htmlspecialchars($_POST["q1"], ENT_QUOTES):'';
	$q2	= isset($_POST["q2"])?htmlspecialchars($_POST["q2"], ENT_QUOTES):'';
	$q3	= isset($_POST["q3"])?htmlspecialchars($_POST["q3"], ENT_QUOTES):$seswil;

	$p = new paging;
	$sql = "
		select a.*, b.nama_cabang as nama_cabang, c.nama_mobil, c.plat_mobil, d.fullname as nama_user
		from pro_peminjaman_mobil a 
		join pro_master_cabang b on b.id_master = a.id_cabang 
		join pro_master_mobil c on c.id_mobil = a.id_mobil
		join acl_user d on a.id_user = d.id_user 
		where 1=1 and a.deleted_time is null
	";

	if($q1 != ""){
		$sql .= " and (upper(c.plat_mobil) like '%".strtoupper($q1)."%' or upper(c.nama_mobil) like '%".strtoupper($q1)."%' or 
			upper(d.fullname) like '%".strtoupper($q1)."%')";
	}
	if($q2 != "")
		$sql .= " and a.tanggal_peminjaman = '".tgl_db($q2)."'";
	if ($q3 != ""){
		if($q3 == '1' || $q3 == '2')
			$sql .= " and (a.id_cabang = 1 or a.id_cabang = 2)";
		else 
			$sql .= " and a.id_cabang = ".$q3;
	}

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by a.tanggal_peminjaman desc, start_jam_peminjaman desc limit ".$position.", ".$length; //echo $sql; exit;
	
	$content = "";
	$count = 0;
	if($tot_record ==  0){
		$content .= '<tr><td colspan="7" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$linkView 	= BASE_URL_CLIENT.'/peminjaman-mobil-add.php?'.paramEncrypt('idr='.$data['id_peminjaman']);
			$linkDel	= paramEncrypt("peminjaman_mobil#|#".$data['id_peminjaman']);
			
			$btnAksi = '';
			if(($data['id_user'] == $sesuser) || paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == '14'){
				$btnAksi = '
				<a class="btn btn-action btn-info jarak-kanan" href="'.$linkView.'"><i class="fa fa-edit"></i></a> 
				<a class="btn btn-action btn-danger" data-param-idx="'.$linkDel.'" data-action="deleteGrid"><i class="fa fa-trash"></i></a>';
			} 

        	$content .= '
				<tr class="clickable-row" data-href="'.$linkView.'">
					<td class="text-center">'.$count.'</td>
					<td>'.$data['nama_cabang'].'</td>
					<td>'.$data['nama_user'].'</td>
					<td>
						'.tgl_indo($data['tanggal_peminjaman']).'<br />
						'.date('H:i', strtotime($data['start_jam_peminjaman'])).' - '.date('H:i', strtotime($data['end_jam_peminjaman'])).'
					</td>
					<td>'.$data['nama_mobil'].'<br />'.$data['plat_mobil'].'</td>
					<td>'.$data['keperluan'].'</td>
					<td class="text-center">'.$btnAksi.'</td>
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

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
	$length	= isset($_POST['length'])?htmlspecialchars($_POST["length"], ENT_QUOTES):5;

	$q1		= htmlspecialchars($_POST["q1"], ENT_QUOTES);
	$seswil = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
	
	$p = new paging;
	$sql = "
		select a.id_customer, a.kode_pelanggan, a.nama_customer, a.alamat_customer, b.nama_kab, c.nama_prov, 
		d.fullname, a.id_wilayah, e.nama_cabang 
		from pro_customer a 
		join pro_master_kabupaten b on a.kab_customer = b.id_kab 
		join pro_master_provinsi c on a.prov_customer = c.id_prov 
		join acl_user d on a.id_marketing = d.id_user 
		join pro_master_cabang e on a.id_wilayah = e.id_master 
		where 1=1 and a.id_wilayah = '".$seswil."'
	";


	if($q1 != "")
		$sql .= " and (upper(a.nama_customer) like '%".strtoupper($q1)."%')";
	
	//echo $sql; exit;

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by a.id_customer desc limit ".$position.", ".$length;

	$content = "";
	$count = 0;
	if($tot_record <= 0){
		$content .= '<tr><td colspan="7" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$linkDetail	= $data['id_customer'].'|#|'.$data['nama_customer'];
			$status		= array(1=>"Prospek", "Evaluasi", "Tetap");
			$alamat		= $data['alamat_customer']." ".str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab'])." ".$data['nama_prov'];
			
			$content .= '
				<tr>
					<td class="text-center">'.$count.'</td>
					<td>
						<p style="margin-bottom: 0px">'.($data['kode_pelanggan']?'<b>'.$data['kode_pelanggan'].'</b>':'--------').'</b></p>
						<p style="margin-bottom: 0px"><b>'.$data['nama_customer'].'</b></p>
						<p style="margin-bottom: 0px"><i>'.$data['fullname'].'</i></p>
					</td>
					<td>
						<p style="margin-bottom: 0px">'.$alamat.'</p>
						<p style="margin-bottom: 0px">Telp : '.$data['telp_customer'].', Fax : '.$data['fax_customer'].'</p>
					</td>
					<td>'.$data['nama_cabang'].'</td>
					<td class="text-center"><button type="button" class="btn btn-success btn-pilih" data-detail="'.$linkDetail.'">Pilih</button></td>
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

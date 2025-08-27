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
	$q4	= isset($_POST["q4"])?htmlspecialchars($_POST["q4"], ENT_QUOTES):'';
	$q5	= isset($_POST["q5"])?htmlspecialchars($_POST["q5"], ENT_QUOTES):'';
	
	$p = new paging;
	$sql = "
		select a.*, b.nama_area, c.jenis_produk, c.merk_dagang, d.nama_vendor, e.nama_terminal, e.tanki_terminal, e.lokasi_terminal 
		from pro_master_harga_tebus a 
		join pro_master_area b on a.id_area = b.id_master 
		join pro_master_produk c on a.id_produk = c.id_master 
		join pro_master_vendor d on a.id_vendor = d.id_master 
		join pro_master_terminal e on a.id_terminal = e.id_master 
		where 1=1
	";
	if($q1 != "")
		$sql .= " and a.periode_awal = '".tgl_db($q1)."'";
	if($q2 != "")
		$sql .= " and a.id_produk = '".$q2."'";
	if($q3 != "")
		$sql .= " and a.id_area = '".$q3."'";
	if($q4 != "")
		$sql .= " and a.id_vendor = '".$q4."'";
	if($q5 != "")
		$sql .= " and a.id_terminal = '".$q5."'";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by a.periode_awal desc, a.id_area, a.id_vendor, a.id_produk, a.id_terminal limit ".$position.", ".$length;

	$content = "";
	if($tot_record <= 0){
		$content .= '<tr><td colspan="7" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$linkDetail	= BASE_URL_CLIENT.'/detil-master-harga-tebus.php?'.paramEncrypt('idr='.$data['id_master']);
			$linkEdit	= BASE_URL_CLIENT.'/add-master-harga-tebus.php?'.paramEncrypt('idr='.$data['id_master']);
			$linkHapus	= paramEncrypt("master_harga_tebus#|#".$data['id_master']);
        	$content .= '
				<tr>
					<td class="text-center">'.date("d/m/Y", strtotime($data['periode_awal'])).' - '.date("d/m/Y", strtotime($data['periode_akhir'])).'</td>
					<td>'.$data['jenis_produk'].' - '.$data['merk_dagang'].'</td>
					<td>'.$data['nama_area'].'</td>
					<td>'.$data['nama_vendor'].'</td>
					<td>'.$data['nama_terminal'].($data['tanki_terminal']?' - '.$data['tanki_terminal']:'').($data['lokasi_terminal']?', '.$data['lokasi_terminal']:'').'</td>
					<td class="text-right">'.number_format($data['harga_tebus']).'</td>
					<td class="text-center action">
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

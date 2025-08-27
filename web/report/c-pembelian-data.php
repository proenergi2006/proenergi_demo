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
	$arrSts = array(1=>"Prospek", "Evaluasi", "Tetap");
	$period = "";
	$where 	= "";

	$q1	= isset($_POST["q1"])?htmlspecialchars($_POST["q1"], ENT_QUOTES):'';
	$q2	= isset($_POST["q2"])?htmlspecialchars($_POST["q2"], ENT_QUOTES):'';
	$q3	= isset($_POST["q3"])?htmlspecialchars($_POST["q3"], ENT_QUOTES):'';
	$q4	= isset($_POST["q4"])?htmlspecialchars($_POST["q4"], ENT_QUOTES):'';
	$q5	= isset($_POST["q5"])?htmlspecialchars($_POST["q5"], ENT_QUOTES):'';
	
	if($q1 && !$q2){ 
		$where .= " and f.tanggal_inven = '".tgl_db($q1)."'";
	} else if($q1 && $q2){
		$where .= " and f.tanggal_inven between '".tgl_db($q1)."' and '".tgl_db($q2)."'";
	}
	if($q3) $where .= " and upper(f.nomor_po) like '%".strtoupper($q3)."%'";
	if($q4) $where .= " and a.id_vendor = '".$q4."'";
	if($q5) $where .= " and a.id_produk = '".$q5."'";
	
	$p = new paging;
	$sql = "
		select f.tanggal_inven, f.nomor_po, b.nama_vendor, c.jenis_produk, c.merk_dagang, f.in_inven, a.harga_tebus, g.harga_minyak as harga_pertamina, 
		d.nama_area, e.nama_terminal, e.tanki_terminal, e.lokasi_terminal
		from pro_master_harga_tebus a 
		join pro_master_vendor b on a.id_vendor = b.id_master 
		join pro_master_produk c on a.id_produk = c.id_master 
		join pro_master_area d on a.id_area = d.id_master 
		join pro_master_terminal e on a.id_terminal = e.id_master 
		join pro_inventory_vendor f on a.id_inven = f.id_master 
		left join pro_master_harga_pertamina g on a.periode_awal = g.periode_awal and a.periode_akhir = g.periode_akhir and a.id_area = g.id_area and a.id_produk = g.id_produk 
		where 1=1 ".$where;

	if(is_numeric($length)){
		$tot_record = $con->num_rows($sql);
		$tot_page 	= ceil($tot_record/$length);
		$page		= ($start > $tot_page)?$start-1:$start; 
		$position 	= $p->findPosition($length, $tot_record, $page);
		$sql .= " order by f.tanggal_inven desc limit ".$position.", ".$length;
	} else{
		$tot_record = $con->num_rows($sql);
		$page		= 1; 
		$position 	= 0;
		$sql .= " order by f.tanggal_inven desc";
	}
	$link = BASE_URL_CLIENT.'/report/c-pembelian-exp.php?'.paramEncrypt('q1='.$q1.'&q2='.$q2.'&q3='.$q3.'&q4='.$q4.'&q5='.$q5);

	$content = "";
	if($tot_record == 0){
		$content .= '<tr><td colspan="9" style="text-align:center"><input type="hidden" id="uriExp" value="'.$link.'" />Data tidak ditemukan </td></tr>';
	} else{
		$tot_vol    = 0;
		$count 		= $position;
		$tot_page 	= (is_numeric($length))?ceil($tot_record/$length):1;
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$tot_vol += $data['in_inven'];
        	$content .= '
				<tr>
					<td class="text-center">'.date("d/m/Y", strtotime($data['tanggal_inven'])).'</td>
					<td class="text-left">'.$data['nomor_po'].'</td>
					<td class="text-left">'.$data['nama_vendor'].'</td>
					<td class="text-left">'.$data['jenis_produk'].' - '.$data['merk_dagang'].'</td>
					<td class="text-right">'.number_format($data['in_inven']).'</td>
					<td class="text-right">'.number_format($data['harga_tebus']).'</td>
					<td class="text-right">'.number_format($data['harga_pertamina']).'</td>
					<td class="text-center">'.$data['nama_area'].'</td>
					<td class="text-left">'.$data['nama_terminal'].' '.$data['tanki_terminal'].', '.$data['lokasi_terminal'].'</td>
				</tr>';
		}
		$content .= '
			<tr>
				<td class="text-center bg-gray" colspan="4"><b>TOTAL</b></td>
				<td class="text-right bg-gray"><b>'.number_format($tot_vol).'</b></td>
				<td class="text-center bg-gray" colspan="4"></td>
			</tr>';
		$content .= '<tr class="hide"><td colspan="9"><input type="hidden" id="uriExp" value="'.$link.'" /></td></tr>';
	} 

	$json_data = array(
					"items"		=> $content,
					"pages"		=> $tot_page,
					"page"		=> $page,
					"totalData"	=> $tot_record,
					"infoData"	=> "Showing ".($position+1)." - ".$count." of ".$tot_record." entries",
				);
	echo json_encode($json_data);
?>

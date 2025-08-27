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
	
	$p = new paging;
	$sql = "
		select a.id_datanya, 'Data Awal' as jenis_penambahan, 
		a.id_produk, concat(c.jenis_produk, ' - ', c.merk_dagang) as ket_produk, 
		a.id_terminal, concat(b.nama_terminal, ' ', b.tanki_terminal) as ket_terminal, 
		a.tanggal_inven, sum(awal_inven) as nilai_jenis, a.keterangan, a.lastupdate_time 
		from pro_inventory_depot a 
		join pro_master_terminal b on a.id_terminal = b.id_master 
		join pro_master_produk c on a.id_produk = c.id_master 
		where id_jenis = 1
		group by a.id_datanya, a.id_produk, a.id_terminal, concat(b.nama_terminal, ' ', b.tanki_terminal), a.tanggal_inven 

		UNION ALL 

		select a.id_datanya, 'Adjustment' as jenis_penambahan, 
		a.id_produk, concat(c.jenis_produk, ' - ', c.merk_dagang) as ket_produk, 
		a.id_terminal, concat(b.nama_terminal, ' ', b.tanki_terminal) as ket_terminal, 
		a.tanggal_inven, adj_inven as nilai_jenis, a.keterangan, a.lastupdate_time 
		from pro_inventory_depot a 
		join pro_master_terminal b on a.id_terminal = b.id_master 
		join pro_master_produk c on a.id_produk = c.id_master 
		where id_jenis = 3

		UNION ALL 

		select a.id_datanya, a.jenis_penambahan, NULL as id_produk, a.ket_produk, 
		NULL as id_terminal, concat('Dari ', a.ket_terminal, '<br />Ke ', b.ket_terminal) as ket_terminal, 
		a.tanggal_inven, (a.nilai_jenis * -1) as nilai_jenis, a.keterangan, a.lastupdate_time 
		from (
			select a.id_datanya, 'Transfer Stock Tanki ke Tanki' as jenis_penambahan, 
			a.id_produk, concat(c.jenis_produk, ' - ', c.merk_dagang) as ket_produk, 
			a.id_terminal, concat(b.nama_terminal, ' ', b.tanki_terminal) as ket_terminal, 
			a.tanggal_inven, sum(adj_inven) as nilai_jenis, a.keterangan, a.lastupdate_time 
			from pro_inventory_depot a 
			join pro_master_terminal b on a.id_terminal = b.id_master 
			join pro_master_produk c on a.id_produk = c.id_master 
			where id_jenis = 4 and adj_inven < 0
			group by a.id_datanya, a.id_produk, a.tanggal_inven 
		) a
		join (
			select a.id_datanya, 'Transfer Stock Tanki ke Tanki' as jenis_penambahan, 
			a.id_produk, concat(c.jenis_produk, ' - ', c.merk_dagang) as ket_produk, 
			a.id_terminal, concat(b.nama_terminal, ' ', b.tanki_terminal) as ket_terminal, 
			a.tanggal_inven, sum(adj_inven) as nilai_jenis, a.keterangan, a.lastupdate_time 
			from pro_inventory_depot a 
			join pro_master_terminal b on a.id_terminal = b.id_master 
			join pro_master_produk c on a.id_produk = c.id_master 
			where id_jenis = 4 and adj_inven > 0
			group by a.id_datanya, a.id_produk, a.tanggal_inven 
		) b on a.id_datanya = b.id_datanya 
	";
	
	/*if($q1 != "")
		$sql .= " and upper(a.nama_terminal) like '%".strtoupper($q1)."%'";
	if($q2 != "" && $q2 != 2)
		$sql .= " and a.is_active = '".$q2."'";*/

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by lastupdate_time limit ".$position.", ".$length;

	$content = "";
	if($tot_record <= 0){
		$content .= '<tr><td colspan="8" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$linkDetail	= BASE_URL_CLIENT.'/detil-master-terminal.php?'.paramEncrypt('idr='.$data['id_master']);
			$linkEdit	= BASE_URL_CLIENT.'/add-master-terminal.php?'.paramEncrypt('idr='.$data['id_master']);
			$linkHapus	= paramEncrypt("vendor_inven_terminal#|#".$data['id_datanya']);
			$active		= ($data["is_active"] == 1)?"Active":"Not Active";
        	$content .= '
				<tr class="clickable-row12" data-href="'.$linkDetail.'">
					<td class="text-center">'.$count.'</td>
					<td>'.$data['jenis_penambahan'].'</td>
					<td class="text-center">'.date("d-m-Y", strtotime($data['tanggal_inven'])).'</td>
					<td>'.$data['ket_produk'].'</td>
					<td>'.$data['ket_terminal'].'</td>
					<td class="text-right">'.number_format($data['nilai_jenis'],0).'</td>
					<td>'.nl2br($data['keterangan']).'</td>
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

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
	$q4	= isset($_POST["q4"])?htmlspecialchars($_POST["q4"], ENT_QUOTES):'';
	$q5	= isset($_POST["q5"])?htmlspecialchars($_POST["q5"], ENT_QUOTES):'';
	
	$p = new paging;
	$sql = "
		select a.*, b.nama_cabang 
		from pro_pr a 
		join pro_master_cabang b on a.id_wilayah = b.id_master 
		join (
			select distinct pr_terminal, id_pr from pro_pr_detail
			where pr_terminal = '".paramDecrypt($_SESSION["sinori".SESSIONID]["terminal"])."' 
		) c on a.id_pr = c.id_pr
		where a.disposisi_pr = 6";
	
	if($q1 != "")
		$sql .= " and a.nomor_pr like '".$q1."%'";
	if($q2 != "")
		$sql .= " and a.id_wilayah = '".$q2."'";
	
	if($q4 != "" && $q5 != ""){
		$sql .= " and (a.tanggal_pr between '".tgl_db($q4)."' and '".tgl_db($q5)."')";
	} else{
		if($q4 != "") $sql .= " and (a.tanggal_pr = '".tgl_db($q4)."')";
		if($q5 != "") $sql .= " and (a.tanggal_pr = '".tgl_db($q5)."')";
	}

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by a.tanggal_pr desc, a.id_pr desc limit ".$position.", ".$length;

	$content = "";
	if($tot_record <= 0){
		$content .= '<tr><td colspan="5" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$idr = $data['id_pr'];
			$linkDetail	= BASE_URL_CLIENT.'/terminal-dr-detail.php?'.paramEncrypt('idr='.$idr);
			$linkCetak  = BASE_URL_CLIENT."/terminal-dr-cetak.php?".paramEncrypt("idr=".$idr."&nom=".$data['nomor_pr']."&tgl=".$data['tanggal_pr']."&cab=".$data['nama_cabang']);

        	$content .= '
				<tr class="clickable-row" data-href="'.$linkDetail.'">
					<td class="text-center">'.$count.'</td>
					<td>'.$data['nomor_pr'].'</td>
					<td>'.tgl_indo($data['tanggal_pr']).'</td>
					<td>'.$data['nama_cabang'].'</td>
					<td>Purchase Order</td>
					<td class="text-center action">
						<a class="margin-sm btn btn-action btn-info jarak-kanan" title="Detail" href="'.$linkDetail.'"><i class="fa fa-info-circle"></i></a>
						<a class="margin-sm btn btn-action btn-success" title="Cetak" href="'.$linkCetak.'" target="_blank"><i class="fa fa-print"></i></a>
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

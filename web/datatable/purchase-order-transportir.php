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
	$sessup = paramDecrypt($_SESSION['sinori'.SESSIONID]['suplier']);
	$seswil = paramDecrypt($_SESSION["sinori".SESSIONID]["id_wilayah"]);
	$q1	= isset($_POST["q1"])?htmlspecialchars($_POST["q1"], ENT_QUOTES):'';
	$q2	= isset($_POST["q2"])?htmlspecialchars($_POST["q2"], ENT_QUOTES):'';
	$q3	= isset($_POST["q3"])?htmlspecialchars($_POST["q3"], ENT_QUOTES):'';
	
	$p = new paging;
	$sql = "select a.*, b.nama_cabang from pro_po a join pro_master_cabang b on a.id_wilayah = b.id_master where a.id_transportir = '".$sessup."' and a.disposisi_po != -1";
	
	if($q1 != "")
		$sql .= " and (a.tanggal_po = '".tgl_db($q1)."' or upper(a.nomor_po) like '".strtoupper($q1)."%')";
	if($q2 != ""){
		if($q2 == 1)
			$sql .= " and a.disposisi_po = 2 and a.po_approved = 0";
		else if($q2 == 2)
			$sql .= " and a.disposisi_po = 1 and a.po_approved = 0";
		else if($q2 == 3)
			$sql .= " and a.po_approved = 1";
	}

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by a.is_new desc, a.disposisi_po desc, a.tanggal_po desc, a.id_po desc limit ".$position.", ".$length;

	$count = 0;
	$content = "";
	if($tot_record <= 0){
		$content .= '<tr><td colspan="6" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$linkDetail	= BASE_URL_CLIENT.'/purchase-order-transportir-detail.php?'.paramEncrypt('idr='.$data['id_po']);
			$status		= "";

			if(($data['disposisi_po'] == 2 || $data['po_approved']) && $data['is_new'])
				$background = ' style="background-color:#f5f5f5"';
			else $background = '';

			if($data['po_approved'])
				$status = 'Terverifikasi';
			else if($data['disposisi_po'] == 1)
				$status = 'Konfirmasi Logistik';
			else if($data['disposisi_po'] == 2)
				$status = 'Verifikasi Transportir';
			else $status = '';

        	$content .= '
				<tr class="clickable-row" data-href="'.$linkDetail.'"'.$background.'>
					<td class="text-center">'.$count.'</td>
					<td>'.$data['nomor_po'].'</td>
					<td class="text-center">'.tgl_indo($data['tanggal_po']).'</td>
					<td>'.$data['nama_cabang'].'</td>
					<td>'.$status.'</td>
					<td class="text-center action"><a class="margin-sm btn btn-action btn-info" title="Detail" href="'.$linkDetail.'"><i class="fa fa-info-circle"></i></a></td>
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

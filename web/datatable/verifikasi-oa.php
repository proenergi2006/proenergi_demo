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
	$sesrol = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	$q1	= isset($_POST["q1"])?htmlspecialchars($_POST["q1"], ENT_QUOTES):'';
	$q2	= isset($_POST["q2"])?htmlspecialchars($_POST["q2"], ENT_QUOTES):'';
	
	$p = new paging;
	$sql = "select a.*, b.nama_cabang, c.nomor_pr from pro_po a join pro_master_cabang b on a.id_wilayah = b.id_master join pro_pr c on a.id_pr = c.id_pr 
			where 1=1 and a.po_approved = 1";

	if($sesrol == '16'){
		$sql .= " and (a.ada_selisih >= 1)";
	} else{
		$sql .= " and (a.ada_selisih = 1)";
	}

	if($q1 != "")
		$sql .= " and (a.tanggal_po = '".tgl_db($q1)."' or a.nomor_po like '".strtoupper($q1)."%' or c.nomor_pr like '".strtoupper($q1)."%')";
	if($q2 != "")
		$sql .= " and a.id_wilayah = '".$q2."'";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by f_proses_selisih asc, a.id_po desc limit ".$position.", ".$length;

	$content = "";
	if($tot_record <= 0){
		$content .= '<tr><td colspan="6" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$linkDetail	= BASE_URL_CLIENT.'/verifikasi-oa-detail.php?'.paramEncrypt('idr='.$data['id_po']);

			if($sesrol == '16'){
				$background = ($data['ada_selisih'] == 2)?' style="background-color:#f5f5f5"':'';
			} else if($sesrol == '3'){
				$background = ($data['f_proses_selisih'] == 0)?' style="background-color:#f5f5f5"':'';
			}

			if($data['f_proses_selisih'] == 1)
				$status = 'Terverifikasi<br><i>'.date("d/m/Y H:i:s", strtotime($data['selisih_approved'])).' WIB</i>';
			else if($data['f_proses_selisih'] == 0 && $data['ada_selisih'] == 1)
				$status = 'Verifikasi CEO';
			else if($data['f_proses_selisih'] == 0 && $data['ada_selisih'] == 2)
				$status = 'Verifikasi Manager Logistik';
			else $status = '';

        	$content .= '
				<tr class="clickable-row" data-href="'.$linkDetail.'"'.$background.'>
					<td class="text-center">'.$count.'</td>
					<td>'.$data['nomor_po'].'</td>
					<td>'.$data['nomor_pr'].'</td>
					<td class="text-center">'.tgl_indo($data['tanggal_po']).'</td>
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

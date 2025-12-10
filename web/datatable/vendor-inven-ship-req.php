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
	$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
	
	$p = new paging;
	$sql = "
		SELECT a.*,c.nama_kapal,concat(nama_terminal,' - ',tanki_terminal,' - ',lokasi_terminal) as nama_terminal,b.nomor_po
		FROM new_pro_inventory_vendor_po_ship_req a 
		JOIN new_pro_inventory_vendor_po b ON a.id_vendor_po = b.id_master
		JOIN pro_master_oa_kapal c ON a.id_vessel = c.id_master
		JOIN pro_master_terminal d ON a.id_terminal_discharging=d.id_master
		WHERE a.is_cancel = 0
		ORDER BY a.id_master DESC
	";
	
	/*if($q1 != "")
		$sql .= " and upper(a.nama_terminal) like '%".strtoupper($q1)."%'";
	if($q2 != "" && $q2 != 2)
		$sql .= " and a.is_active = '".$q2."'";*/

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);

	$content = "";
	if($tot_record <= 0){
		$content .= '<tr><td colspan="8" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		$status = "";

		foreach($result as $data){
			if ($sesrol == '16') {
				$background = ($data['status'] == 0) ? ' style="background-color:#f5f5f5"' : '';
			}elseif ($sesrol == '4') {
				$background = ($data['status'] == 1) ? ' style="background-color:#f5f5f5"' : '';
			}elseif ($sesrol == '21') {
				$background = ($data['status'] == 2 && $data['cfo_result'] == 1) ? ' style="background-color:#f5f5f5"' : '';
			}

			if ($data['status'] == 0){
				$status = "Request Procurement";
			}else if($data['status'] == 1){
				$status = "Verifikasi CFO";
			}else if($data['status'] == 2){
				if( $data['cfo_result']==1){
					$status = "Verifikasi CEO";
				}else{
					$status = "Ditolak CFO <i>" . date("d/m/Y H:i:s", strtotime($data['cfo_tanggal'])) . ' WIB</i>';;
				}
			}else if($data['status'] == 3){
				if( $data['ceo_result']==1){
					$status = "Terverifikasi CEO <i>" . date("d/m/Y H:i:s", strtotime($data['ceo_tanggal'])) . ' WIB</i>';
				}else{
					$status = "Ditolak CEO <i>" . date("d/m/Y H:i:s", strtotime($data['ceo_tanggal'])) . ' WIB</i>';;
				}
			}

			$count++;
			$linkDetail	= BASE_URL_CLIENT.'/shipping-request-detail.php?'.paramEncrypt('idr='.$data['id_master']);
			$linkEdit	= BASE_URL_CLIENT.'/add-master-terminal.php?'.paramEncrypt('idr='.$data['id_master']);
			// $linkHapus	= paramEncrypt("vendor_inven_terminal#|#".$data['id_datanya']);
			$active		= ($data["is_active"] == 1)?"Active":"Not Active";
        	$content .= '
				<tr class="clickable-row12" data-href="'.$linkDetail.'"' . $background . '>
					<td class="text-center">'.$count.'</td>
					<td>'.$data['nomor_req'].'</td>
					<td>'.$data['nomor_po'].'</td>
					<td class="text-center">'.tgl_indo($data['created_at']).'</td>
					<td class="text-right">'.number_format($data['quantity'],0).'</td>
					<td>'.$data['nama_terminal'].'</td>
					<td>'.$status.'</td>
					<td class="text-center action">
						<a class="margin-sm btn btn-action btn-primary" title="Detail" href="' . $linkDetail . '">
						<i class="fa fa-info-circle"></i></a>
            		</td>
				</tr>';
		} 
	} 

	// <a class="margin-sm delete btn btn-action btn-danger" title="Delete" data-param-idx="'.$linkHapus.'" data-action="deleteGrid">
	// 					<i class="fa fa-trash"></i></a>
	$json_data = array(
					"items"		=> $content,
					"pages"		=> $tot_page,
					"page"		=> $page,
					"totalData"	=> $tot_record,
					"infoData"	=> "Showing ".($position+1)." to ".$count." of ".$tot_record." entries",
				);
	echo json_encode($json_data);
?>

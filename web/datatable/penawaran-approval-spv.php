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
	
	$s1 = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
	$s2 = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']);
	$s3 = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);
	
	$p = new paging;
	$sql = "
		select 
		case 
			when (a.flag_disposisi > 0 and a.flag_disposisi = 1 and a.spv_mkt_result = 0 and a.flag_approval = 0) then 1 
			when (a.flag_disposisi > 2 and a.flag_approval = 0) then 2 
			else 3 
		end as ordernya, b.id_marketing, f.id_spv, 
		a.*, b.nama_customer, b.kode_pelanggan, c.id_wilayah, c.fullname, d.nama_cabang, d.id_group_cabang, e.nama_area
		from pro_penawaran a 
		join pro_customer b on a.id_customer = b.id_customer 
		join acl_user c on b.id_marketing = c.id_user 
		join pro_master_cabang d on a.id_cabang = d.id_master 
		join pro_master_area e on a.id_area = e.id_master 
		join pro_mapping_spv f on b.id_marketing = f.id_mkt  
		where 1=1
		 and (a.flag_disposisi >= 1) and f.id_spv = '".$s3."'
	";
	
	if($q1 != ""){
		$sql .= " and (upper(b.nama_customer) like '%".strtoupper($q1)."%' or a.nomor_surat like '".strtoupper($q1)."%' or b.kode_pelanggan = '".$q1."')";
	}
	if($q2 != ""){
		$sql .= " and a.id_cabang = '".$q2."'";
	}
	if($q3 != ""){
		$sql .= " and a.id_area = '".$q3."'";
	}

	
	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by ordernya, a.id_penawaran desc limit ".$position.", ".$length;

	$arrPosisi	= array(1=>"SPV", "BM", "BM", "OM", "COO", "CEO");
	$arrSetuju	= array(1=>"Disetujui","Ditolak");

	$content = "";
	if($tot_record ==  0){
		$content .= '<tr><td colspan="8" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$linkDetail	= BASE_URL_CLIENT.'/penawaran-approval-spv-detail.php?'.paramEncrypt('idr='.$data['id_customer'].'&idk='.$data['id_penawaran']);
			$background	= "";
			$catatan_ceo = $data['sm_wil_summary'];
			
			if($data['ordernya'] == '1'){
				$background	= 'style="background-color:#f5f5f5"';
			}

			if($data['flag_approval'] == 0 && $data['flag_disposisi'] == 0){
				$status = "Terdaftar";
			} else if($data['flag_approval'] == 0 && $data['flag_disposisi']){
				if($data['flag_disposisi'] > 1 && $data['flag_disposisi'] < 4){
					$status = "Verifikasi ".$arrPosisi[$data['flag_disposisi']]." ".$data['nama_cabang'];
				} else{
					$status = "Verifikasi ".$arrPosisi[$data['flag_disposisi']];
				}
			} else if($data['flag_approval']){
				if($data['flag_disposisi'] > 1 && $data['flag_disposisi'] < 4){
					$status = $arrSetuju[$data['flag_approval']]." ".$arrPosisi[$data['flag_disposisi']]." ".$data['nama_cabang'];
					$status .= "<br /><i>".($data['tgl_approval'] ? date("d/m/Y H:i:s", strtotime($data['tgl_approval']))." WIB" : "")."</i>";
				} else{
					$status = $arrSetuju[$data['flag_approval']]." ".$arrPosisi[$data['flag_disposisi']];
					$status .= "<br /><i>".($data['tgl_approval'] ? date("d/m/Y H:i:s", strtotime($data['tgl_approval']))." WIB" : "")."</i>";
				}
			}

			$content .= '
				<tr class="clickable-row" data-href="'.$linkDetail.'"'.$background.'>
					<td class="text-center">'.$count.'</td>
					<td>
						<p style="margin-bottom: 0px"><b>'.$data['nomor_surat'].'</b></p>
						<p style="margin-bottom: 0px"><i>'.$data['fullname'].'</i></p>
					</td>
					<td>
						<p style="margin-bottom:0px;"><b>'.($data['kode_pelanggan']?$data['kode_pelanggan']:'-------').'</b></p>
						<p style="margin-bottom: 0px">'.$data['nama_customer'].'</p>
					</td>
					<td>'.$data['nama_cabang'].'</td>
					<td>'.$data['nama_area'].'</td>
					<td>'.number_format($data['volume_tawar'],0).' Liter</td>
					<td>'.$status.'</td>
					<td class="text-center action">
						<a class="margin-sm btn btn-action btn-info" title="Detail" href="'.$linkDetail.'"><i class="fa fa-table"></i></a>
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

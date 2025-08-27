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
	$s1 = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
	$s2 = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']);
	$s3 = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);
	
	$p = new paging;
	$sql = "select a.*, b.nama_customer, b.kode_pelanggan, c.id_wilayah, c.fullname, d.nama_cabang, d.id_group_cabang, e.nama_area 
			from pro_penawaran a join pro_customer b on a.id_customer = b.id_customer join acl_user c on b.id_marketing = c.id_user 
			join pro_master_cabang d on a.id_cabang = d.id_master join pro_master_area e on a.id_area = e.id_master where 1=1";
	
	$sql2 = $sql;
	if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 3){
		$sql .= " and (a.flag_disposisi > 3 or a.flag_approval > 0)";
		$orde = " a.id_penawaran desc";
		// $orde = "a.flag_approval asc, a.flag_disposisi desc, nomor_surat desc, a.tgl_approval asc, a.ceo_result, a.id_penawaran desc";
		// $orde = " a.ceo_result asc, a.flag_approval asc, a.id_penawaran desc";
		$sql2 .= " and a.flag_disposisi = 4 and a.flag_approval = 0";
		
	} else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 6){
		$sql .= " and (a.flag_disposisi > 2 or a.flag_approval > 0) and ((c.id_role in (11,18,17) and d.id_group_cabang = '".$s2."') or (c.id_role = 17 and c.id_om = '".$s3."'))";
		$orde = " a.id_penawaran desc";
		// $orde = "a.flag_approval asc, a.flag_disposisi desc, nomor_surat desc, a.tgl_approval asc, a.om_result, a.id_penawaran desc";
		// $orde = " a.om_result asc, a.flag_approval asc, a.id_penawaran desc";
		$sql2 .= " and a.flag_disposisi = 3 and a.flag_approval = 0 and ((c.id_role in (11,18,17) and d.id_group_cabang = '".$s2."') or (c.id_role = 17 and c.id_om = '".$s3."'))";
	} else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 7){
		$sql .= " and c.id_role in (11,18,17)";
		$sql .= " and (case when a.id_cabang <> c.id_wilayah then a.flag_disposisi > 0 else a.flag_disposisi > 1 end)";
		$sql .= " and (case when a.flag_disposisi > 1 then (a.id_cabang = '".$s1."' or c.id_wilayah = '".$s1."') else c.id_wilayah = '".$s1."' end)";
		$orde = " a.id_penawaran desc";
		// $orde = "a.flag_approval asc, a.flag_disposisi desc, nomor_surat desc, a.tgl_approval asc, a.sm_wil_result, a.id_penawaran desc";
		// $orde = " a.sm_mkt_result desc, a.sm_mkt_tanggal desc, a.sm_wil_result asc, a.id_penawaran desc";
		$sql2 .= " and c.id_role in (11,18)";
		$sql2 .= " and (case when a.flag_disposisi > 1 then (a.id_cabang = '".$s1."' or c.id_wilayah = '".$s1."') else c.id_wilayah = '".$s1."' end)";

		$sql2 .= " and (a.flag_disposisi = 1 or a.flag_disposisi = 2) and a.flag_approval = 0";
	}
	
	if($q1 != ""){
		$sql .= " and (upper(b.nama_customer) like '%".strtoupper($q1)."%' or a.nomor_surat like '".strtoupper($q1)."%' or b.kode_pelanggan = '".$q1."')";
		$sql2 .= " and (upper(b.nama_customer) like '%".strtoupper($q1)."%' or a.nomor_surat like '".strtoupper($q1)."%' or b.kode_pelanggan = '".$q1."')";
	}
	if($q2 != ""){
		$sql .= " and a.id_cabang = '".$q2."'";
		$sql2 .= " and a.id_cabang = '".$q2."'";
	}
	if($q3 != ""){
		$sql .= " and a.id_area = '".$q3."'";
		$sql2 .= " and a.id_area = '".$q3."'";
	}

	
	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);

	$arrPosisi	= array(1=>"BM","BM Cabang","OM","CEO");
	$arrSetuju	= array(1=>"Disetujui","Ditolak");

	$content = "";
	if($tot_record ==  0){
		$content .= '<tr><td colspan="8" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{

		$id = array();
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);

		$sql2 .= " order by a.id_penawaran limit ".$position.", ".$length;
		$result2 	= $con->getResult($sql2);
	
		foreach($result2 as $data){
			$count++;
			$length--;
			$linkDetail	= BASE_URL_CLIENT.'/penawaran-approval-detail.php?'.paramEncrypt('idr='.$data['id_customer'].'&idk='.$data['id_penawaran']);
			
			$status = "Verifikasi ".$arrPosisi[$data['flag_disposisi']];
			
			$background = ' style="background-color:#f5f5f5"';
			
			$id[] = $data['id_customer'];

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
						<a class="margin-sm btn btn-action btn-info" title="Detail" href="'.$linkDetail.'"><i class="fa fa-info-circle"></i></a>
            		</td>
				</tr>';
		} 

		if($id)
			$sql .= " and a.id_customer not in(".implode(",",$id).")";
		
		$sql .= " order by ".$orde." limit ".$position.", ".$length;
		$result 	= $con->getResult($sql);

		
		foreach($result as $data){
			$linkDetail	= BASE_URL_CLIENT.'/penawaran-approval-detail.php?'.paramEncrypt('idr='.$data['id_customer'].'&idk='.$data['id_penawaran']);
			$background	= "";

			if($data['flag_approval'] == 0 && $data['flag_disposisi'] == 0)
				$status = "Terdaftar";
			else if($data['flag_approval'] == 0 && $data['flag_disposisi'])
				$status = "Verifikasi ".$arrPosisi[$data['flag_disposisi']];
			else if($data['flag_approval'])
				$status = $arrSetuju[$data['flag_approval']]." ".$arrPosisi[$data['flag_disposisi']]."<br/><i>".date("d/m/Y H:i:s",strtotime($data['tgl_approval']))." WIB</i>";

			if($length > 0 )
			{
				$length --;
				$count++;
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
							<a class="margin-sm btn btn-action btn-info" title="Detail" href="'.$linkDetail.'"><i class="fa fa-info-circle"></i></a>
						</td>
					</tr>';
			}
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

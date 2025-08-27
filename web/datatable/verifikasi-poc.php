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
	$arrRol = array(11=>"BM", 17=>"OM");

	$q1	= isset($_POST["q1"])?htmlspecialchars($_POST["q1"], ENT_QUOTES):'';
	$q2	= isset($_POST["q2"])?htmlspecialchars($_POST["q2"], ENT_QUOTES):'';
	
	$p = new paging;
	$ar9 = array("6"=>"a.id_poc desc, a.sm_result", "7"=>"a.id_poc desc, a.sm_result", "17"=>"a.id_poc desc, a.sm_result");
	$sql = "select a.*, b.nama_customer, b.kode_pelanggan, b.alamat_customer, c.nama_kab, d.nama_prov, e.fullname, e.id_role, g.nama_cabang 
			from pro_po_customer a join pro_customer b on a.id_customer = b.id_customer 
			join pro_master_kabupaten c on b.kab_customer = c.id_kab join pro_master_provinsi d on b.prov_customer = d.id_prov 
			join acl_user e on b.id_marketing = e.id_user 
			join pro_penawaran f on a.id_penawaran = f.id_penawaran join pro_master_cabang g on f.id_cabang = g.id_master 
			where 1=1";
	
	if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 6)
		$sql .= " and a.disposisi_poc > 0 and a.disposisi_poc <> 3 and e.id_role = 17 and e.id_om = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."'";
	else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 7)
		$sql .= " and a.disposisi_poc > 0 and a.disposisi_poc <> 3 and e.id_role = 11 and b.id_wilayah = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."'";

	if($q1 != "")
		$sql .= " and (upper(b.nama_customer) like '%".strtoupper($q1)."%' or b.kode_pelanggan = '".$q1."' or a.nomor_poc = '".$q1."' or a.tanggal_poc = '".tgl_db($q1)."' or concat('PO-',lpad(a.id_poc,4,'0')) = '".strtoupper($q1)."')";
	if($q2 != "")
		$sql .= " and a.poc_approved = '".$q2."' and sm_result = 1";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by ".$ar9[paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role'])]." limit ".$position.", ".$length;
	
	$content = "";
	if($tot_record <= 0){
		$content .= '<tr><td colspan="8" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$linkDetail	= BASE_URL_CLIENT.'/verifikasi-poc-detail.php?'.paramEncrypt('idr='.$data['id_customer'].'&idk='.$data['id_poc']);
			$pathPt 	= $public_base_directory.'/files/uploaded_user/lampiran/'.$data['lampiran_poc'];
			$lampPt 	= $data['lampiran_poc_ori'];

			if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 7 && !$data['sm_result'])
				$background = ' style="background-color:#f5f5f5"';
			else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 6 && !$data['sm_result'])
				$background = ' style="background-color:#f5f5f5"';
			else $background = '';

			if($data['lampiran_poc'] && file_exists($pathPt)){
				$linkPt = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=2&ktg=POC_".$data['id_poc']."_&file=".$lampPt);
				$attach = '<a href="'.$linkPt.'"><i class="fa fa-file-alt" title="'.$lampPt.'"></i></a>';
			} else {$attach = '-';}

			if($data['poc_approved'] == 1)
				$disposisi = 'Terverifikasi '.$arrRol[$data['id_role']].'<br/><i>'.date("d/m/Y H:i:s", strtotime($data['tgl_approved'])).'</i> WIB';
			else if($data['poc_approved'] == 2)
				$disposisi = 'Ditolak '.$arrRol[$data['id_role']];
			else if($data['disposisi_poc'] == 0)
				$disposisi = 'Terdaftar';
			else if($data['disposisi_poc'] == 1)
				$disposisi = 'Verifikasi '.$arrRol[$data['id_role']];
			else if($data['disposisi_poc'] == 2)
				$disposisi = 'Verifikasi OM';
			else if($data['disposisi_poc'] == 3)
				$disposisi = 'Review Admin Finance';
			else $disposisi = '';
			
        	$content .= '
				<tr class="clickable-row" data-href="'.$linkDetail.'"'.$background.'>
					<td class="text-center">'.$count.'</td>
					<td>
						<p style="margin-bottom: 0px"><b>PO-'.str_pad($data['id_poc'],4,'0',STR_PAD_LEFT).'</b></p>
						<p style="margin-bottom: 0px"><i>'.$disposisi.'</i></p>
					</td>
					<td>
						<p style="margin-bottom: 0px"><b>'.$data['nama_customer'].'</b></p>
						<p style="margin-bottom: 0px"><i>'.$data['fullname'].'</i></p>
					</td>
					<td>
						<p style="margin-bottom: 0px"><b>'.$data['kode_pelanggan'].'</b></p>
						<p style="margin-bottom: 0px">'.$data['nama_cabang'].'</p>
					</td>
					<td>
						<p style="margin-bottom: 0px"><b>'.$data['nomor_poc'].'</b></p>
						<p style="margin-bottom: 0px">'.tgl_indo($data['tanggal_poc']).'</p>
					</td>
					<td>'.number_format($data['volume_poc']).' Liter (Rp. '.number_format($data['harga_poc']).'/liter)</td>
					<td class="text-center">'.$attach.'</td>
					<td class="text-center action">
						<a class="margin-sm btn btn-action btn-info" title="Detail" href="'.$linkDetail.'"><i class="fa fa-info-circle"></i></a>
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

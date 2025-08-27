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
	
	$p = new paging;
	$sql = "select a.*, b.nama_customer, b.alamat_customer, b.kode_pelanggan, c.nama_kab, d.nama_prov, e.fullname, g.nama_cabang, h.realisasi 
			from pro_po_customer a join pro_customer b on a.id_customer = b.id_customer 
			join pro_master_kabupaten c on b.kab_customer = c.id_kab join pro_master_provinsi d on b.prov_customer = d.id_prov 
			join acl_user e on b.id_marketing = e.id_user 
			join pro_penawaran f on a.id_penawaran = f.id_penawaran join pro_master_cabang g on f.id_cabang = g.id_master 
			left join (select id_poc, sum(realisasi_kirim) as realisasi from pro_po_customer_plan group by id_poc) h on a.id_poc = h.id_poc
			where b.id_customer = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['customer'])."' and poc_approved = 1";
	
	if($q1 != "")
		$sql .= " and upper(a.nomor_poc) like '%".strtoupper($q1)."%'";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by a.id_poc desc limit ".$position.", ".$length;
	$count = 0;

	$content = "";
	if($tot_record <= 0){
		$content .= '<tr><td colspan="9" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$linkPlan	= BASE_URL_CLIENT.'/permintaan-rekapitulasi-detail.php?'.paramEncrypt('idr='.$data['id_customer'].'&idk='.$data['id_poc']);
			$pathPt 	= $public_base_directory.'/files/uploaded_user/lampiran/'.$data['lampiran_poc'];
			$lampPt 	= $data['lampiran_poc_ori'];

			if($data['lampiran_poc'] && file_exists($pathPt)){
				$linkPt = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=2&ktg=POC_".$data['id_poc']."_&file=".$lampPt);
				$attach = '<a href="'.$linkPt.'"><i class="fa fa-file-alt" title="'.$lampPt.'"></i></a>';
			} else {$attach = '-';}
			
        	$content .= '
				<tr class="clickable-row" data-href="'.$linkPlan.'">
					<td class="text-center">'.$count.'</td>
					<td>'.$data['nomor_poc'].'</td>
					<td class="text-center">'.date("d/m/Y", strtotime($data['tanggal_poc'])).'</td>
					<td>'.$data['fullname'].'</td>
					<td class="text-right">'.number_format($data['volume_poc']).' Liter</td>
					<td class="text-right">'.number_format($data['harga_poc']).' /liter</td>
					<td class="text-right">'.number_format($data['realisasi']).' Liter</td>
					<td class="text-center">'.$attach.'</td>
					<td class="text-center action">
						<a class="margin-sm btn btn-action btn-info" title="Detail" href="'.$linkPlan.'"><i class="fa fa-info-circle"></i></a>
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

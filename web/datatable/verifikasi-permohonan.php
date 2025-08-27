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
	$ar9 = array(
		"3"=>"a.ceo_result, a.id_cu desc", 
		"4"=>"a.cfo_result, a.id_cu desc", 
		"6"=>"a.om_result, a.id_cu desc", 
		"7"=>"a.om_result, a.id_cu desc", 
		"10"=>"a.finance_result, a.id_cu desc"
	);
	$arrKategoriPerubahan = array(
		1=>"Perubahan Credit Limit",
		"Perubahan TOP",
		"Perubahan Data",
		"Perubahan Credit Limit & Data Customer",
		"Perubahan TOP & Data Customer",
		"Perubahan Credit Limit & TOP",
		"Perubahan Credit Limit & TOP & Data Customer",
	);

	$sql = "select a.*, b.nama_customer, b.kode_pelanggan, c.fullname from pro_customer_update a join pro_customer b on a.id_customer = b.id_customer 
			join acl_user c on b.id_marketing = c.id_user where 1=1";

	if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 10)
		$sql .= " and a.flag_disposisi > 0 and b.id_wilayah = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."'";
	else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 7)
		$sql .= " and a.flag_disposisi > 1 and b.id_wilayah = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."'";
	else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 4)
		$sql .= " and a.flag_disposisi > 2";
	else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 3)
		$sql .= " and a.flag_disposisi > 3";
	
	if($q1 != "")
		$sql .= " and (upper(b.nama_customer) like '%".strtoupper($q1)."%' or upper(a.judul) like '%".strtoupper($q1)."%' or b.kode_pelanggan = '".$q1."')";
	
	if($q2 != "")
		$sql .= " and (kategori = '".$q2."')";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by ".$ar9[paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role'])]." limit ".$position.", ".$length;
	
	$count = 6;
	$content = "";
	if($tot_record ==  0){
		$content .= '<tr><td colspan="5" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$linkDetail	= BASE_URL_CLIENT.'/verifikasi-permohonan-detail.php?'.paramEncrypt('idr='.$data['id_customer'].'&idk='.$data['id_cu']);
			$linkEdit	= BASE_URL_CLIENT.'/verifikasi-permohonan-data.php?'.paramEncrypt('idr='.$data['id_customer'].'&idk='.$data['id_cu']);
			$background	= "";
			$arrFlag 	= array(1=>"Admin Finance", "BM", "CFO");

			if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 10 && (!$data['finance_result'] || ($data['flag_approval'] == 1 && !$data['flag_edited'])))
				$background = ' style="background-color:#f5f5f5"';
			else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 7 && !$data['om_result'])
				$background = ' style="background-color:#f5f5f5"';
			else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 4 && !$data['cfo_result'])
				$background = ' style="background-color:#f5f5f5"';
			else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 3 && !$data['ceo_result'])
				$background = ' style="background-color:#f5f5f5"';
			

			if($data['flag_disposisi'] == 0)
				$status = "Terdaftar";
			else if($data['flag_edited'] == 1)
				$status = "Disetujui ".$arrFlag[$data['flag_disposisi']]." <i>".date("d/m/Y H:i:s", strtotime($data['tgl_approval']))." WIB</i><br/>Dimutakhirkan <i>".date("d/m/Y H:i:s", strtotime($data['tgl_edited']))." WIB</i>";
			else if($data['flag_approval'] == 1)
				$status = "Permohonan Disetujui ".$arrFlag[$data['flag_disposisi']]."<br /><i>".date("d/m/Y H:i:s", strtotime($data['tgl_approval']))." WIB</i>";
			else if($data['flag_approval'] == 2)
				$status = "Permohonan Ditolak ".$arrFlag[$data['flag_disposisi']]."<br /><i>".date("d/m/Y H:i:s", strtotime($data['tgl_approval']))." WIB</i>";
			else if($data['flag_disposisi'] == 1)
				$status = "Diverifikasi Admin Finance";
			else if($data['flag_disposisi'] == 2)
				$status = "Diverifikasi BM";
			else if($data['flag_disposisi'] == 3)
				$status = "Diverifikasi CFO";
			else if($data['flag_disposisi'] == 4)
				$status = "Diverifikasi CEO";

        	$content .= '
				<tr class="clickable-row" data-href="'.$linkDetail.'"'.$background.'>
					<td class="text-center">'.$count.'</td>
					<td class="text-center">'.date("d/m/Y", strtotime($data['created_time'])).'</td>
					<td>
						<p style="margin-bottom:5px"><b>'.$data['nama_customer'].'</b></p>
						<p style="margin-bottom:0px"><i>'.$data['fullname'].'</i></p>
					</td>
					<td>
						'.($data['kategori'] ? '<p style="margin-bottom:5px;"><b>'.strtoupper($arrKategoriPerubahan[$data['kategori']]).'</b></p>': '').'
						'.$data['judul'].'
					</td>
					<td>'.$status.'</td>
					<td class="text-center action">
						<a class="margin-sm btn btn-action btn-info" title="Detail" href="'.$linkDetail.'"><i class="fa fa-info-circle"></i></a>
						'.($data['flag_approval'] == 1 && !$data['flag_edited'] && paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 10 ? 
							'<a class="margin-sm btn btn-action btn-info" title="Edit Data" href="'.$linkEdit.'"><i class="fa fa-edit"></i></a>' : ''
						).'
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

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
	$sesrol = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	$iduser = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);

	$arrKategoriPerubahan = array(
		1=>"Perubahan Credit Limit",
		"Perubahan TOP",
		"Perubahan Data",
		"Perubahan Credit Limit & Data Customer",
		"Perubahan TOP & Data Customer",
		"Perubahan Credit Limit & TOP",
		"Perubahan Credit Limit & TOP & Data Customer",
	);

	$q1	= isset($_POST["q1"])?htmlspecialchars($_POST["q1"], ENT_QUOTES):'';
	$q2	= isset($_POST["q2"])?htmlspecialchars($_POST["q2"], ENT_QUOTES):'';
	
	$p = new paging;
	$sql = "select a.*, b.nama_customer, b.kode_pelanggan from pro_customer_update a join pro_customer b on a.id_customer = b.id_customer 
			where 1=1";
	if ($sesrol == 18) {
		if (paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
            $sql .= " and (b.id_wilayah = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."' or b.id_marketing = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."')";
        else if (!paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
            $sql .= " and (b.id_group = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group'])."' or b.id_marketing = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."')";
	} else if ($sesrol == 17 || $sesrol == 11) {
		$sql .= " and b.id_marketing = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."'";
	}
	
	if($q1 != "")
		$sql .= " and (upper(b.nama_customer) like '%".strtoupper($q1)."%' or upper(a.judul) like '%".strtoupper($q1)."%' or b.kode_pelanggan = '".$q1."')";

	if($q2 != "")
		$sql .= " and (kategori = '".$q2."')";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by a.id_cu desc limit ".$position.", ".$length;

	$content = "";
	$count = 0;
	if($tot_record <= 0){
		$content .= '<tr><td colspan="7" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$linkDetail	= BASE_URL_CLIENT.'/customer-permohonan-update-detail.php?'.paramEncrypt('idr='.$data['id_customer'].'&idk='.$data['id_cu']);
			$linkHapus	= paramEncrypt("customer_update#|#".$data['id_cu']);
			$pathPt 	= $public_base_directory.'/files/uploaded_user/lampiran/'.$data['attachment_order'];
			$lampPt 	= $data['attachment_order_ori'];
			$linkPt		= ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=2&ktg=PUD_".$data['id_cu']."_&file=".$lampPt);
			$filePt 	= '<a href="'.$linkPt.'" title="'.$lampPt.'"><i class="fa fa-file-alt jarak-kanan"></i></a>';
        	$status		= "";
			$arrFlag 	= array(1=>"Admin Finance", "BM", "CFO");
			
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
				<tr class="clickable-row" data-href="'.$linkDetail.'">
					<td class="text-center">'.$count.'</td>
					<td class="text-center">'.date("d/m/Y", strtotime($data['created_time'])).'</td>
					<td>
						<p style="margin-bottom:0px"><b>'.($data['kode_pelanggan']?$data['kode_pelanggan']:'-------').'</b></p>
						<p style="margin-bottom:0px"><i>'.$data['nama_customer'].'</i></p>
					</td>
					<td>
						'.($data['kategori'] ? '<p style="margin-bottom:5px;"><b>'.strtoupper($arrKategoriPerubahan[$data['kategori']]).'</b></p>': '').'
						'.$data['judul'].'
					</td>
					<td>'.$status.'</td>
					<td class="text-center">'.($data['attachment_order'] && file_exists($pathPt)?$filePt:'&nbsp;').'</td>
					<td class="text-center action">
						<a class="margin-sm btn btn-action btn-info" title="Detail" href="'.$linkDetail.'"><i class="fa fa-info-circle"></i></a>
						<a class="margin-sm btn btn-action btn-danger" data-param-idx="'.$linkHapus.'" data-action="deleteGrid"><i class="fa fa-trash"></i></a>
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

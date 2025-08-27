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
	$q3	= isset($_POST["q3"])?htmlspecialchars($_POST["q3"], ENT_QUOTES):'';
	$q4	= isset($_POST["q4"])?htmlspecialchars($_POST["q4"], ENT_QUOTES):'';
	
	$p = new paging;
	$sql = "select a.*, b.nama_customer, b.kode_pelanggan, c.nama_cabang, d.nama_area, if(a.flag_approval=0&&a.flag_disposisi>0, 1, 0) as position from pro_penawaran a join pro_customer b on a.id_customer = b.id_customer join pro_master_cabang c on a.id_cabang = c.id_master join pro_master_area d on a.id_area = d.id_master where 1=1";
	// $sql = "select a.*, b.nama_customer, b.kode_pelanggan, c.nama_cabang, d.nama_area, if(a.flag_approval=0&&a.flag_disposisi>0, 1, 0) as position from pro_penawaran a join pro_customer b on a.id_customer = b.id_customer join pro_master_cabang c on a.id_cabang = c.id_master join pro_master_area d on a.id_area = d.id_master where 1=1";
	if ($sesrol == 18)
		$sql .= " and b.id_wilayah = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."'";
	else
		$sql .= " and b.id_marketing = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."'";

	if($q1 != "")
		$sql .= " and (upper(b.nama_customer) like '%".strtoupper($q1)."%' or b.kode_pelanggan = '".$q1."' or a.nomor_surat like '".$q1."%')";
	if($q2 != "")
		$sql .= " and a.id_cabang = '".$q2."'";
	if($q3 != "")
		$sql .= " and a.id_area = '".$q3."'";
	
	if($q4)
	{
		if($q4 == 4)
			$sql .= " and a.flag_approval = '0' and flag_disposisi > 0";
		else if($q4 == 3)
			$sql .= "  and a.flag_disposisi = '0'";
		else
			$sql .= " and a.flag_approval = ".$q4;
	}

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by a.id_penawaran desc limit ".$position.", ".$length;

	$content = "";
	
	$sesrol = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);

	$count = 0;
	if($tot_record == 0){
		$content .= '<tr><td colspan="8" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$linkDetail	= BASE_URL_CLIENT.'/penawaran-detail.php?'.paramEncrypt('idr='.$data['id_customer'].'&idk='.$data['id_penawaran']);
			$linkHapus	= paramEncrypt("penawaran#|#".$data['id_penawaran']);
			//$disabled	= ($data['flag_approval'])?'disabled':'';
			$background = '';
			$arrPosisi	= array(1=>"BM","BM Cabang","OM","CEO");
			$arrSetuju	= array(1=>"Disetujui","Ditolak");

			if($data['flag_approval'] == 0 && $data['flag_disposisi'] == 0)
				$status = "Terdaftar";
			else if($data['flag_approval'] == 0 && $data['flag_disposisi'])
				$status = "Verifikasi ".$arrPosisi[$data['flag_disposisi']];
			else if($data['flag_approval'])
				$status = $arrSetuju[$data['flag_approval']]." ".$arrPosisi[$data['flag_disposisi']]."<br/><i>".date("d/m/Y H:i:s",strtotime($data['tgl_approval']))." WIB</i>";
			
			if($sesrol == 11 && $data['flag_approval'] == 2 && $data['view'] == 'No')
                $background = 'style="background-color:#f5f5f5"';
            
			/*if($data['perhitungan'] == 1)
				$harga = number_format($data['harga_dasar'],0,'','.');
			else if($data['perhitungan'] == 2){
				$harga = '';
				$temp = json_decode($data['detail_formula'], true);
				foreach($temp as $jenis){
					$harga .= '<p style="margin-bottom:0px">'.$jenis.'</p>';
				}
			}*/

        	$content .= '
				<tr class="clickable-row" data-href="'.$linkDetail.'" '.$background.'>
					<td class="text-center">'.$count.'</td>
					<td>'.$data['nomor_surat'].'</td>
					<td>
						<p style="margin-bottom:0px;"><b>'.($data['kode_pelanggan']?$data['kode_pelanggan']:'-------').'</b></p>
						<p style="margin-bottom:0px;">'.$data['nama_customer'].'</p>
					</td>
					<td>'.$data['nama_cabang'].'</td>
					<td>'.$data['nama_area'].'</td>
					<td>'.number_format($data['volume_tawar'],0).' Liter</td>
					<td>'.$status.'</td>
					<td class="text-center action">
						<a class="margin-sm btn btn-action btn-info" title="Detail" href="'.$linkDetail.'"><i class="fa fa-info-circle"></i></a>
						<a class="margin-sm btn btn-action btn-danger " title="Delete" data-param-idx="'.$linkHapus.'" data-action="deleteGrid">
						<i class="fa fa-trash"></i></a>
            		</td>
				</tr>';
		}
	} 

	$json_data = array(
					"items"		=> $content,
					"pages"		=> $tot_page,
					"page"		=> $page,
					"totalData"	=> $tot_record,
					"infoData"	=> "Showing ".($position+1)." - ".$count." of ".$tot_record." entries",
				);
	echo json_encode($json_data);
?>

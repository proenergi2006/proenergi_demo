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
	$sesrol = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
	
	$p = new paging;
	$whereadd = '';
	if ($sesrol>1) {
		$whereadd = " and a.id_wilayah = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_wilayah"])."'";
	}
	$sql = "select a.*, b.inisial_segel from pro_manual_segel a join pro_master_cabang b on a.id_wilayah = b.id_master 
			where 1=1 ".$whereadd;
	
	if($q1 != "")
		$sql .= " and (upper(a.nomor_acara) like '%".strtoupper($q1)."%' or upper(a.created_by) like '%".strtoupper($q1)."%' or 
				 (a.segel_awal <= '".intval($q1)."' and a.segel_akhir >= '".intval($q1)."'))";
	if($q2 != "" && $q3 == "")
		$sql .= " and a.tanggal_segel = '".tgl_db($q2)."'";
	else if($q2 != "" && $q3 != "")
		$sql .= " and a.tanggal_segel between '".tgl_db($q2)."' and '".tgl_db($q3)."'";
	
	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by tanggal_segel desc limit ".$position.", ".$length;

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
			$linkDetail	= BASE_URL_CLIENT.'/manual-segel-detail.php?'.paramEncrypt('idr='.$data['id_master']);
			$linkEdit	= BASE_URL_CLIENT.'/manual-segel-add.php?'.paramEncrypt('idr='.$data['id_master']);
			$linkCetak	= ACTION_CLIENT.'/manual-segel-cetak.php?'.paramEncrypt('idr='.$data['id_master']);
			$linkHapus	= paramEncrypt("manual_segel#|#".$data['id_master']);
			$disabled 	= ($data['kategori'] != 1)?'':'disabled';

			$seg_aw = ($data['segel_awal'])?str_pad($data['segel_awal'],4,'0',STR_PAD_LEFT):'';
			$seg_ak = ($data['segel_akhir'])?str_pad($data['segel_akhir'],4,'0',STR_PAD_LEFT):'';
			if($data['jumlah_segel'] == 1)
				$nomor_segel = $data['inisial_segel']."-".$seg_aw;
			else if($data['jumlah_segel'] == 2)
				$nomor_segel = $data['inisial_segel']."-".$seg_aw." &amp; ".$data['inisial_segel']."-".$seg_ak;
			else if($data['jumlah_segel'] > 2)
				$nomor_segel = $data['inisial_segel']."-".$seg_aw." s/d ".$data['inisial_segel']."-".$seg_ak;
			else $nomor_segel = '';
        	$content .= '
				<tr class="clickable-row" data-href="'.$linkDetail.'">
					<td class="text-center">'.$count.'</td>
					<td class="text-left">'.$data['nomor_acara'].'</td>
					<td class="text-center">'.tgl_indo($data['tanggal_segel'], 'normal', 'db', '/').'</td>
					<td class="text-center">'.$nomor_segel.'</td>
					<td class="text-left">'.$data['created_by'].'</td>
					<td class="text-center action">
						<a class="margin-sm btn btn-action btn-info" title="Detil" href="'.$linkDetail.'"><i class="fa fa-info-circle"></i></a> 
						<a class="margin-sm btn btn-action btn-info" title="Edit" href="'.$linkEdit.'"><i class="fa fa-edit"></i></a> 
						<a class="margin-sm btn btn-action btn-success" title="Cetak" href="'.$linkCetak.'" target="_blank"><i class="fa fa-print"></i></a>
						<a class="margin-sm btn btn-action btn-danger '.$disabled.'" title="Delete" data-param-idx="'.$linkHapus.'" data-action="deleteGrid">
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
					"infoData"	=> "Showing ".($position+1)." to ".$count." of ".$tot_record." entries",
				);
	echo json_encode($json_data);
?>

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
	$t1 = str_replace(array("AR","ar"),array("",""),$q1);
	
	$p = new paging;
	$sql = "select a.*, b.nama_cabang, c.nomor_pr from pro_pr_ar a join pro_master_cabang b on a.id_wilayah = b.id_master join pro_pr c on a.id_pr = c.id_pr where 1=1";
	
	if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 6)
		$sql .= " and a.disposisi_ar > 1 and ((a.is_ka = 0 and a.id_group = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group'])."') or (a.is_ka = 1 and a.ka_om = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."'))";
	else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 7)
		$sql .= " and a.disposisi_ar > 0 and a.id_wilayah = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."'";
	else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 10)
		$sql .= " and a.id_wilayah = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."'";
	else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 15)
		$sql .= " and a.disposisi_ar > 2";

	if($q1 != "")
		$sql .= " and (a.tanggal_buat = '".tgl_db($q1)."' or c.nomor_pr like '".strtoupper($q1)."%' or a.id_par = '".intval($t1)."')";
	if($q2 != "")
		$sql .= " and a.id_wilayah = '".$q2."'";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$arrSorting	= array("6"=>"a.om_result, a.tanggal_buat desc, a.id_par desc", "7"=>"a.sm_result, a.tanggal_buat desc, a.id_par desc", 
						"10"=>"a.finance_result, a.tanggal_buat desc, a.id_par desc", "15"=>"a.mgr_result, a.tanggal_buat desc, a.id_par desc");
	$sql .= " order by ".$arrSorting[paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role'])]." limit ".$position.", ".$length;//echo $sql; exit;

	$content = "";
	if($tot_record <= 0){
		$content .= '<tr><td colspan="7" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$linkDetail	= BASE_URL_CLIENT.'/purchase-request-ar-detail.php?'.paramEncrypt('idr='.$data['id_par']);
			$status		= "";

			if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 6 && !$data['om_result'])
				$background = ' style="background-color:#f5f5f5"';
			else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 7 && !$data['sm_result'])
				$background = ' style="background-color:#f5f5f5"';
			else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 10 && !$data['finance_result'])
				$background = ' style="background-color:#f5f5f5"';
			else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 15 && !$data['mgr_result'])
				$background = ' style="background-color:#f5f5f5"';
			else $background = '';

			if($data['ar_approved'] == 1)
				$status = 'Terverifikasi';
			else if($data['disposisi_ar'] == 0)
				$status = 'Verifikasi Finance';
			else if($data['disposisi_ar'] == 1)
				$status = 'Verifikasi BM';
			else if($data['disposisi_ar'] == 2)
				$status = 'Verifikasi OM';
			else if($data['disposisi_ar'] == 3)
				$status = 'Verifikasi Manager Finance';
			else $status = '';

        	$content .= '
				<tr class="clickable-row" data-href="'.$linkDetail.'"'.$background.'>
					<td class="text-center">'.$count.'</td>
					<td>AR'.str_pad($data['id_par'],4,'0',STR_PAD_LEFT).'</td>
					<td class="text-center">'.date("d/m/Y", strtotime($data['tanggal_buat'])).'</td>
					<td>'.$data['nomor_pr'].'</td>
					<td>'.$data['nama_cabang'].'</td>
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

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
	$sql = "select a.*, b.nama_customer, b.kode_pelanggan, c.fullname from pro_customer_evaluasi a join pro_customer b on a.id_customer = b.id_customer 
			join acl_user c on b.id_marketing = c.id_user where 1=1";
	
	if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 11 || paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 17) {
		$sql .= " and b.id_marketing = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."'";
	} else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 18) {
		if (paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
            $sql .= " and (b.id_wilayah = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."' or b.id_marketing = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."')";
        else if (!paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
            $sql .= " and (b.id_group = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group'])."' or b.id_marketing = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."')";
	}
	if($q1 != "")
		$sql .= " and (upper(b.nama_customer) like '%".strtoupper($q1)."%' or b.kode_pelanggan = '".$q1."' or concat('EC',lpad(a.id_evaluasi,4,'0')) = '".strtoupper($q1)."')";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$arrSorting	= array(
					"1"=>"a.ceo_result, a.id_evaluasi desc", 
					"3"=>"a.ceo_result, a.id_evaluasi desc", 
					"4"=>"a.cfo_result, a.id_evaluasi desc", 
					"6"=>"a.om_result, a.id_evaluasi desc", 
					"7"=>"a.sm_result, a.id_evaluasi desc", 
					"9"=>"a.logistik_result, a.id_evaluasi desc", 
					"10"=>"a.finance_result, a.id_evaluasi desc", 
					"11"=>"a.id_evaluasi desc, a.marketing_result",
					"18"=>"a.id_evaluasi desc, a.marketing_result"
				);
	$sql .= " order by ".$arrSorting[paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role'])]." limit ".$position.", ".$length;

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
			$idr 	 = $data['id_customer'];
			$idk 	 = $data['id_evaluasi'];
			$linkSur = BASE_URL_CLIENT.'/customer-evaluasi-add.php?'.paramEncrypt('idr='.$idr.'&idk='.$idk);

        	if($data['is_approved'] == 1)
				$status = "Terevaluasi (Customer Tetap)";
			else if($data['is_approved'] == 2)
				$status = "Terevaluasi (Customer Penawaran)";
			else if($data['disposisi_result'] == 0)
				$status = "Terdaftar";
			else if($data['disposisi_result'] == 1)
				$status = "Dievaluasi Finance, Logistik";
			else if($data['disposisi_result'] == 2)
				$status = "Dievaluasi BM";
			else if($data['disposisi_result'] == 3)
				$status = "Dievaluasi OM";
			else if($data['disposisi_result'] == 4)
				$status = "Dievaluasi CFO";
			else if($data['disposisi_result'] == 5)
				$status = "Dievaluasi CEO";

			$content .= '
				<tr class="clickable-row" data-href="'.$linkSur.'">
					<td class="text-center">'.$count.'</td>
					<td class="text-center">'.date("d/m/Y", strtotime($data['prospek_tanggal'])).'</td>
					<td>EC'.str_pad($idk,4,'0',STR_PAD_LEFT).'</td>
					<td>'.($data['kode_pelanggan']?$data['kode_pelanggan']:'-------').'</td>
					<td>'.$data['nama_customer'].'</td>
					<td>'.$status.'</td>
					<td class="text-center"><a class="btn btn-action btn-info" href="'.$linkSur.'"><i class="fa fa-info-circle"></i></a></td>
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

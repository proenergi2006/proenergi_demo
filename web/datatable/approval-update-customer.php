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
	$q1	= htmlspecialchars(paramDecrypt($_POST["q1"]), ENT_QUOTES);
	$q2	= isset($_POST["q2"])?htmlspecialchars($_POST["q2"], ENT_QUOTES):'';
	
	$p = new paging;
	$sql = "select * from pro_customer_verification where id_customer = '".$q1."'";
	
	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by id_verification desc limit ".$position.", ".$length;

	$content = "";
	if($tot_record <= 0){
		$content .= '<p class="text-center" style="padding: 100px;">Data tidak ditemukan...</p>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$active	 = ($data["is_active"] == 1)?"Status link aktif":"Masa berlaku link telah habis...";
			$linkRes = ACTION_CLIENT."/resubmit-customer.php?".paramEncrypt('idr='.$data['id_customer'].'&idk='.$data['id_verification']);
			$linkTag = '<a class="btn btn-primary konfirmasi" href="'.$linkRes.'">Revised</a>';
			$linkCus = BASE_URL.'/customer/update-customer.php?'.paramEncrypt('idr='.$data['id_customer'].'&token='.$data['token_verification'].'&idv='.$data['id_verification']);
        	$content .= '
				<div class="content-data">
					<div class="nomor-urut">UC-'.$data['id_verification'].'</div>
					<p style="margin-bottom:5px; color:#3c8dbc; word-wrap:break-word;">'.$linkCus.'</p>
					<p style="margin-bottom:0px;"><b>'.$active.'</b></p>';
			if($data['is_approved'] == 1)
				$content .= '<p style="margin-bottom:5px;">Data Terverifikasi</p>';
			else if($data['is_approved'] == 2)
				$content .= '<p style="margin-bottom:5px;">Data ditolak</p>';
			else if((!$data['legal_result'] || !$data['finance_result'] || !$data['logistik_result']) && $data['is_evaluated'])
				$content .= '<p style="margin-bottom:5px;"><i>Data dalam tahap verifikasi</i></p>';
			else if($data['legal_result'] && $data['finance_result'] && $data['logistik_result'] && !$data['om_result'])
				$content .= '<p style="margin-bottom:5px;">Data dalam tahap persetujuan OM</p>';
			else if($data['legal_result'] && $data['finance_result'] && $data['logistik_result'] && $data['om_result'])
				$content .= '<p style="margin-bottom:5px;">Data dalam tahap persetujuan CFO</p>';

			if((!$data['legal_result'] || !$data['finance_result'] || !$data['logistik_result']) && $data['is_evaluated'])
				$content .= $linkTag;
        	$content .= '</div>';
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

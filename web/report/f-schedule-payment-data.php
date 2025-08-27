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
	$arrSts = array(1=>"Prospek", "Evaluasi", "Tetap");
	$period = "";
	$where 	= "";

	$q1	= isset($_POST["q1"])?htmlspecialchars($_POST["q1"], ENT_QUOTES):'';
	$q2	= isset($_POST["q2"])?htmlspecialchars($_POST["q2"], ENT_QUOTES):'';
	$q3	= isset($_POST["q3"])?htmlspecialchars($_POST["q3"], ENT_QUOTES):'';
	$q4	= isset($_POST["q4"])?htmlspecialchars($_POST["q4"], ENT_QUOTES):'';
	$q5	= isset($_POST["q5"])?htmlspecialchars($_POST["q5"], ENT_QUOTES):'';
	$q6	= isset($_POST["q6"])?htmlspecialchars($_POST["q6"], ENT_QUOTES):'';
	
	if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 9){
		$where .= " and j.id_wilayah = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_wilayah"])."'";
	}

	if($q1 && !$q2){ 
		$where .= " and b.tanggal_pr = '".tgl_db($q1)."'";
	} else if($q1 && $q2){
		$where .= " and b.tanggal_pr between '".tgl_db($q1)."' and '".tgl_db($q2)."'";
	}
	if($q3) $where .= " and upper(j.nama_customer) like '%".strtoupper($q3)."%'";
	if($q4) $where .= " and upper(a.schedule_payment) like '%".strtoupper($q4)."%'";
	if($q5) $where .= " and j.id_wilayah = '".$q5."'";
	if($q6) $where .= " and h.id_area = '".$q6."'";

	// $where_not_used = "and (a.pr_ar_satu != 0 or a.pr_ar_dua != 0)";
	$where_not_used = "";
	
	$p = new paging;
	$sql = "
		select b.tanggal_pr, j.nama_customer, k.nama_cabang, i.nama_area, a.volume, a.schedule_payment 
		from pro_pr_detail a 
		join pro_pr b on a.id_pr = b.id_pr 
		join pro_po_customer_plan c on a.id_plan = c.id_plan 
		join pro_po_customer d on c.id_poc = d.id_poc 
		join pro_customer_lcr e on c.id_lcr = e.id_lcr 
		join pro_master_provinsi f on e.prov_survey = f.id_prov 
		join pro_master_kabupaten g on e.kab_survey = g.id_kab 
		join pro_penawaran h on d.id_penawaran = h.id_penawaran 
		join pro_master_area i on h.id_area = i.id_master 
		join pro_customer j on d.id_customer = j.id_customer 
		join pro_master_cabang k on j.id_wilayah = k.id_master 
		join acl_user l on j.id_marketing = l.id_user 
		where 1=1 ".$where_not_used." and a.is_approved = 1 ".$where;

	if(is_numeric($length)){
		$tot_record = $con->num_rows($sql);
		$tot_page 	= ceil($tot_record/$length);
		$page		= ($start > $tot_page)?$start-1:$start; 
		$position 	= $p->findPosition($length, $tot_record, $page);
		$sql .= " order by b.tanggal_pr desc limit ".$position.", ".$length;
	} else{
		$tot_record = $con->num_rows($sql);
		$page		= 1; 
		$position 	= 0;
		$sql .= " order by b.tanggal_pr desc";
	}
	$link = BASE_URL_CLIENT.'/report/f-schedule-payment-exp.php?'.paramEncrypt('q1='.$q1.'&q2='.$q2.'&q3='.$q3.'&q4='.$q4.'&q5='.$q5.'&q6='.$q6);

	$count = 0;
	$content = "";
	if($tot_record == 0){
		$content .= '<tr><td colspan="6" style="text-align:center"><input type="hidden" id="uriExp" value="'.$link.'" />Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= (is_numeric($length))?ceil($tot_record/$length):1;
		$result 	= $con->getResult($sql);
		$tot_vol	= 0;
		foreach($result as $data){
			$tot_vol += $data['volume'];
			$count++;

        	$content .= '
				<tr>
					<td class="text-center">'.date("d/m/Y", strtotime($data['tanggal_pr'])).'</td>
					<td class="text-left">'.$data['nama_customer'].'</td>
					<td class="text-center">'.$data['nama_cabang'].'</td>
					<td class="text-center">'.$data['nama_area'].'</td>
					<td class="text-right">'.number_format($data['volume']).'</td>
					<td class="text-left">'.$data['schedule_payment'].'</td>
				</tr>';
		}
		$content .= '
			<tr>
				<td class="text-center bg-gray" colspan="4"><b>TOTAL</b></td>
				<td class="text-right bg-gray"><b>'.number_format(($tot_vol)).'</b></td>
				<td class="text-center bg-gray"></td>
			</tr>';
		$content .= '<tr class="hide"><td colspan="6"><input type="hidden" id="uriExp" value="'.$link.'" /></td></tr>';
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

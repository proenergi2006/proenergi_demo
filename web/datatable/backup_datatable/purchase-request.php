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
	$q3	= isset($_POST["q3"])?htmlspecialchars($_POST["q3"], ENT_QUOTES):'';
	$q4	= isset($_POST["q4"])?htmlspecialchars($_POST["q4"], ENT_QUOTES):'';
	$q5	= isset($_POST["q5"])?htmlspecialchars($_POST["q5"], ENT_QUOTES):'';
	
	$p = new paging;
	// $sql = "select a.*, b.nama_cabang from pro_pr a join pro_master_cabang b on a.id_wilayah = b.id_master where 1=1";
	/*
	$sql = "
		select
			a.*, 
			b.nama_cabang,
			(
				select 
					f.nama_customer
				from
					pro_pr_detail c
					left join (
						pro_po_customer_plan d
						join (
							pro_po_customer e
							join pro_customer f on f.id_customer = e.id_customer
						) on e.id_poc = d.id_poc
					) on d.id_plan = c.id_plan 
				where c.id_pr = a.id_pr
				limit 1
			) as nama_customer
		from 
			pro_pr a 
			join pro_master_cabang b on a.id_wilayah = b.id_master 
		where 1=1
	";
	*/
	$seswil = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
	$sesrol = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	$disposisi_pr_sort = 0;
	if($sesrol == 10) $disposisi_pr_sort = 1;
	else if($sesrol == 7) $disposisi_pr_sort = 2;
	else if($sesrol == 5) $disposisi_pr_sort = 3;
	else if($sesrol == 3) $disposisi_pr_sort = 4;
	else if($sesrol == 4) $disposisi_pr_sort = 4;
	else if($sesrol == 9) $disposisi_pr_sort = 5;
	$sql = "
		select
		    a.*, 
		    b.nama_cabang,
		    (
		        select 
		            group_concat(f.nama_customer separator ', ')
		        from
		            pro_pr_detail c
		            left join (
		                pro_po_customer_plan d
		                join (
		                    pro_po_customer e
		                    join pro_customer f on f.id_customer = e.id_customer
		                ) on e.id_poc = d.id_poc
		            ) on d.id_plan = c.id_plan 
		        where c.id_pr = a.id_pr
		        -- limit 1
		    ) as nama_customer,
		    IF(a.disposisi_pr=".$disposisi_pr_sort.", 1, 0) as disposisi_pr_sort
		from 
		    pro_pr a 
		    join pro_master_cabang b on a.id_wilayah = b.id_master 
		where 1=1
		-- GROUP BY a.id_pr
	";
	
	if($sesrol == 3)
		$sql .= " and a.disposisi_pr > 3 and a.is_ceo = 1";
	else if($sesrol == 4)
		$sql .= " and a.disposisi_pr > 3";
	else if($sesrol == 5)
		$sql .= " and a.disposisi_pr > 2";
	else if($sesrol == 7)
		$sql .= " and a.disposisi_pr > 1 and a.id_wilayah = '".$seswil."'";
	else if($sesrol == 10)
		$sql .= " and a.disposisi_pr > 0 and a.id_wilayah = '".$seswil."'";
	else if($sesrol == 9)
		$sql .= " and a.id_wilayah = '".$seswil."'";

	if($q1 != "")
		$sql .= " and a.nomor_pr like '".$q1."%'";
	if($q2 != "")
		$sql .= " and a.id_wilayah = '".$q2."'";
	
	if($q3 != "" && $q3 == 1)
		$sql .= " and a.disposisi_pr = 1 and a.ada_ar = 0";
	else if($q3 != "" && $q3 == 1.5)
		$sql .= " and a.disposisi_pr = 1 and a.ada_ar = 1";
	else if($q3 != "" && $q3 == 4)
		$sql .= " and a.disposisi_pr = 4 and a.is_ceo = 0";
	else if($q3 != "" && $q3 == 4.5)
		$sql .= " and a.disposisi_pr = 4 and a.is_ceo = 1";
	else if($q3 != "")
		$sql .= " and a.disposisi_pr = '".$q3."'";

	if($q4 != "" && $q5 != ""){
		$sql .= " and (a.tanggal_pr between '".tgl_db($q4)."' and '".tgl_db($q5)."')";
	} else{
		if($q4 != "") $sql .= " and (a.tanggal_pr = '".tgl_db($q4)."')";
		if($q5 != "") $sql .= " and (a.tanggal_pr = '".tgl_db($q5)."')";
	}

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by disposisi_pr_sort desc, a.tanggal_pr desc, a.id_pr desc limit ".$position.", ".$length;

	$content = "";
	$count = 0;
	if($tot_record <= 0){
		$content .= '<tr><td colspan="5" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$linkDetail	= BASE_URL_CLIENT.'/purchase-request-detail.php?'.paramEncrypt('idr='.$data['id_pr'].'&detail=1');
			$status		= "";

			if($sesrol == 3 && !$data['ceo_result'] && $data['is_ceo'])
				$background = ' style="background-color:#f5f5f5"';
			else if($sesrol == 4 && !$data['cfo_result'])
				$background = ' style="background-color:#f5f5f5"';
			else if($sesrol == 5 && !$data['purchasing_result'])
				$background = ' style="background-color:#f5f5f5"';
			else if($sesrol == 7 && !$data['sm_result'])
				$background = ' style="background-color:#f5f5f5"';
			else if($sesrol == 9 && $data['logistik_result'] && $data['disposisi_pr'] == 5)
				$background = ' style="background-color:#f5f5f5"';
			else if($sesrol == 10 && !$data['finance_result'])
				$background = ' style="background-color:#f5f5f5"';
			else $background = '';

			if($data['disposisi_pr'] == 1 && !$data['ada_ar']){
				// if($data['revert_ceo'] != '' || $data['revert_cfo'] != '')
				// 	$status = 'Pengembalian dari Purchasing';
				// else
					$status = 'Admin Finance';
			}else if($data['disposisi_pr'] == 1 && $data['ada_ar'])
				$status = 'Pending Due AR';
			else if($data['disposisi_pr'] == 2)
				$status = 'Verifikasi BM';
			else if($data['disposisi_pr'] == 3)
				$status = 'Verifikasi Purchasing';
			else if($data['disposisi_pr'] == 4 && !$data['is_ceo'])
				$status = 'Verifikasi CFO';
			else if($data['disposisi_pr'] == 4 && $data['is_ceo'])
				$status = 'Verifikasi CEO';
			else if($data['disposisi_pr'] == 5)
				$status = 'Terverifikasi';
			else if($data['disposisi_pr'] == 6)
				$status = 'Purchase Order';
			else $status = '';

			$nama_customers = '';
			$nama_customer = explode(',', $data['nama_customer']);
			foreach ($nama_customer as $v) $nama_customers .= $v.'<br/>';
        	$content .= '
				<tr class="clickable-row" data-href="'.$linkDetail.'"'.$background.'>
					<td class="text-center">'.$count.'</td>
					<td>'.$data['nomor_pr'].'</td>
					<td>'./*$data['sm_pic']*/$nama_customers.'</td>
					<td>'.tgl_indo($data['tanggal_pr']).'</td>
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

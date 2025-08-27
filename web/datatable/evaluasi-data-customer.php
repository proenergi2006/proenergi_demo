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
	$sql = "
		select a.*, b.nama_customer, b.kode_pelanggan, b.alamat_customer, b.telp_customer, c.nama_prov, d.nama_kab, e.fullname 
		from pro_customer_evaluasi a 
		join pro_customer b on a.id_customer = b.id_customer 
		join pro_master_provinsi c on b.prov_customer = c.id_prov 
		join pro_master_kabupaten d on b.kab_customer = d.id_kab 
		join acl_user e on b.id_marketing = e.id_user 
		join pro_master_cabang f on b.id_group = f.id_master 
		where 1=1
	";
	
	if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 10)
		$sql .= " and a.disposisi_result > 0 and f.id_master = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."'";
	else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 9)
		$sql .= " and a.disposisi_result > 0  and f.id_master = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."'";
	else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 7)
		$sql .= " and a.disposisi_result > 1  and f.id_master = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."'";
	else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 6)
		$sql .= " and e.id_role = 11 and a.disposisi_result > 2  and f.id_group_cabang = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group'])."'";
	else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 4)
		$sql .= " and a.disposisi_result > 3";
	else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 3)
		$sql .= " and a.disposisi_result > 4";

	if($q1 != "")
		$sql .= " and (upper(b.nama_customer) like '%".strtoupper($q1)."%' or b.kode_pelanggan = '".$q1."' or concat('EC',lpad(a.id_evaluasi,4,'0')) = '".strtoupper($q1)."')";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$arrSorting	= array("3"=>"a.id_evaluasi desc, a.ceo_result", "4"=>"a.id_evaluasi desc, a.cfo_result", "6"=>"a.id_evaluasi desc, a.om_result", 
						"7"=>"a.id_evaluasi desc, a.sm_result", "9"=>"a.id_evaluasi desc, a.logistik_result", "10"=>"a.id_evaluasi desc, a.finance_result");
	$sql .= " order by ".$arrSorting[paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role'])]." limit ".$position.", ".$length;
	
	//$result 	= $con->getResult($sql); 
	//print_r($result); exit;
	//echo $sql; exit;

	$count = 0;
	$content = "";
	if($tot_record ==  0){
		$content .= '<tr><td colspan="6" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		$background	= '';
		foreach($result as $data){
			$count++;
			$temp1 		= strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
			$alamatCust = $data['alamat_customer']." ".ucwords($temp1)." ".$data['nama_prov'];
			$linkDetail	= BASE_URL_CLIENT.'/evaluasi-data-customer-detail.php?'.paramEncrypt('idr='.$data['id_customer'].'&idk='.$data['id_evaluasi']);
			$linkCetak	= ACTION_CLIENT.'/cetak-evaluasi-data-customer.php?'.paramEncrypt('idr='.$data['id_customer'].'&idk='.$data['id_evaluasi']);

			if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 7 && !$data['sm_result'])
				$background = ' style="background-color:#f5f5f5"';
			else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 9 && !$data['logistik_result'])
				$background = ' style="background-color:#f5f5f5"';
			else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 10 && !$data['finance_result'])
				$background = ' style="background-color:#f5f5f5"';
			else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 6 && !$data['om_result'])
				$background = ' style="background-color:#f5f5f5"';
			else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 4 && !$data['cfo_result'])
				$background = ' style="background-color:#f5f5f5"';
			else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 3 && !$data['ceo_result'])
				$background = ' style="background-color:#f5f5f5"';
			else $background = '';

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
				<tr class="clickable-row" data-href="'.$linkDetail.'"'.$background.'>
					<td class="text-center">'.$count.'</td>
					<td>EC'.str_pad($data['id_evaluasi'],4,'0',STR_PAD_LEFT).'</td>
					<td>'.$data['kode_pelanggan'].'</td>
					<td>
						<p style="margin-bottom:0px"><b>'.$data['nama_customer'].'</b></p>
						<p style="margin-bottom:0px"><i>'.$data['fullname'].'</i></p>
					</td>
					<td>'.$alamatCust.'</td>
					<td>'.$status.'</td>
					<td class="text-center action">
						<a class="margin-sm btn btn-action btn-info" title="Detail" href="'.$linkDetail.'"><i class="fa fa-info-circle"></i></a>
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

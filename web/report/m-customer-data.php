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
	$q7	= isset($_POST["q7"])?htmlspecialchars($_POST["q7"], ENT_QUOTES):'';
	
	if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 11 || paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 17)
		$where .= " and f.id_marketing = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."'";
	else if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 7 || paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 10)
		$where .= " and f.id_wilayah = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_wilayah"])."'";
	else if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 18) {
		if (paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
            $where .= " and (f.id_wilayah = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."' or f.id_marketing = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."')";
        else if (!paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
            $where .= " and (f.id_group = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group'])."' or f.id_marketing = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."')";
	}
	else if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 6)
		$where .= " and (f.id_group = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_group"])."' or g.id_om = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."')";

	if($q1 && !$q2){ 
		$where .= " and a.tanggal_delivered between '".tgl_db($q1)." 00:00:00' and '".tgl_db($q1)." 23:59:59'";
		$period = $q1;
	} else if($q1 && $q2){
		$where .= " and a.tanggal_delivered between '".tgl_db($q1)." 00:00:00' and '".tgl_db($q2)." 23:59:59'";
		$period = $q1." s/d ".$q2;
	}
	if($q3) $where .= " and upper(f.nama_customer) like '%".strtoupper($q3)."%'";
	if($q4) $where .= " and f.status_customer = '".$q4."'";
	if($q5) $where .= " and f.id_wilayah = '".$q5."'";
	if($q6) $where .= " and f.id_marketing = '".$q6."'";
	if($q7) $where .= " and d.id_area = '".$q7."'";
	
	$p = new paging;
	$sql = "
		select sum(jum_vol) as volume, tanggal_delivered, id_customer, nama_customer, status_customer, id_wilayah, nama_cabang, id_area, nama_area, id_marketing, fullname
		from (
			select date(a.tanggal_delivered) as tanggal_delivered, b.volume_po as jum_vol, f.id_customer, f.nama_customer, f.status_customer, f.id_wilayah, h.nama_cabang, 
			d.id_area, e.nama_area, f.id_marketing, g.fullname 
			from pro_po_ds_detail a
			join pro_po_detail b on a.id_pod = b.id_pod 
			join pro_po_customer c on a.id_poc = c.id_poc 
			join pro_penawaran d on c.id_penawaran = d.id_penawaran 
			join pro_master_area e on d.id_area = e.id_master 
			join pro_customer f on c.id_customer = f.id_customer 
			join acl_user g on f.id_marketing = g.id_user 
			join pro_master_cabang h on f.id_wilayah = h.id_master 
			where a.is_delivered = 1 ".$where." 
			UNION ALL
			select date(a.tanggal_delivered) as tanggal_delivered, a.bl_lo_jumlah as jum_vol, f.id_customer, f.nama_customer, f.status_customer, f.id_wilayah, h.nama_cabang, 
			d.id_area, e.nama_area, f.id_marketing, g.fullname 
			from pro_po_ds_kapal a 
			join pro_po_customer b on a.id_poc = b.id_poc 
			join pro_penawaran d on b.id_penawaran = d.id_penawaran 
			join pro_master_area e on d.id_area = e.id_master 
			join pro_customer f on b.id_customer = f.id_customer 
			join acl_user g on f.id_marketing = g.id_user 
			join pro_master_cabang h on f.id_wilayah = h.id_master 
			where a.is_delivered = 1 ".$where." 
		) a group by tanggal_delivered, id_customer, status_customer, id_wilayah, id_area, id_marketing";

	if(is_numeric($length)){
		$tot_record = $con->num_rows($sql);
		$tot_page 	= ceil($tot_record/$length);
		$page		= ($start > $tot_page)?$start-1:$start; 
		$position 	= $p->findPosition($length, $tot_record, $page);
		$sql .= " order by a.tanggal_delivered desc, a.id_customer limit ".$position.", ".$length;
	} else{
		$tot_record = $con->num_rows($sql);
		$page		= 1; 
		$position 	= 0;
		$sql .= " order by a.tanggal_delivered desc, a.id_customer";
	}
	$link = BASE_URL_CLIENT.'/report/m-customer-exp.php?'.paramEncrypt('q1='.$q1.'&q2='.$q2.'&q3='.$q3.'&q4='.$q4.'&q5='.$q5.'&q6='.$q6.'&q7='.$q7);

	$content = "";
	$count = 0;
	if($tot_record == 0){
		$content .= '<tr><td colspan="7" style="text-align:center"><input type="hidden" id="uriExp" value="'.$link.'" />Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= (is_numeric($length))?ceil($tot_record/$length):1;
		$result 	= $con->getResult($sql);
		$tot1 		= 0;
		$tot_vol    = 0;
		foreach($result as $data){
			$count++;
			//$volume = $data['jum_kapal'] + $data['jum_mobil'];
			$tot_vol += $data['volume'];
        	$content .= '
				<tr>
					<td class="text-center">'.date("d/m/Y", strtotime($data['tanggal_delivered'])).'</td>
					<td class="text-left">'.$data['nama_customer'].'</td>
					<td class="text-left">'.$data['fullname'].'</td>
					<td class="text-center">'.$data['nama_cabang'].'</td>
					<td class="text-center">'.$data['nama_area'].'</td>
					<td class="text-center">'.$arrSts[$data['status_customer']].'</td>
					<td class="text-right">'.number_format($data['volume']).'</td>
				</tr>';
		}
		$content .= '
			<tr>
				<td class="text-center bg-gray" colspan="6"><b>TOTAL</b></td>
				<td class="text-right bg-gray"><b>'.number_format($tot_vol).'</b></td>
			</tr>';
		$content .= '<tr class="hide"><td colspan="7"><input type="hidden" id="uriExp" value="'.$link.'" /></td></tr>';
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

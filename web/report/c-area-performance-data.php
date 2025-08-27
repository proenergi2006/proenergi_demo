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
	$where 	= "";

	$q1	= isset($_POST["q1"])?htmlspecialchars($_POST["q1"], ENT_QUOTES):'';
	$q2	= isset($_POST["q2"])?htmlspecialchars($_POST["q2"], ENT_QUOTES):'';
	$q3	= isset($_POST["q3"])?htmlspecialchars($_POST["q3"], ENT_QUOTES):'';
	
	if($q1 && !$q2){ 
		$t1 = explode("/",$q1);
		$m1 = $t1[1]."/".$t1[0]."/01";
		$t2 = explode("/",$q1);
		$m2 = date("Y/m/t", mktime(0, 0, 0, $t1[0], 01, $t1[1]));
		$where .= " and a.tanggal_delivered between '".$m1." 00:00:00' and '".$m2." 23:59:59'";
	} else if($q1 && $q2){
		$t1 = explode("/",$q1);
		$m1 = $t1[1]."/".$t1[0]."/01";
		$t2 = explode("/",$q2);
		$m2 = date("Y/m/t", mktime(0, 0, 0, $t2[0], 01, $t2[1]));
		$where .= " and a.tanggal_delivered between '".$m1." 00:00:00' and '".$m2." 23:59:59'";
	}
	if($q3) $where .= " and d.id_area = '".$q3."'";
	
	$p = new paging;
	$sql = "
		select sum(jum_vol) as volume, bulan_delivered, id_area, nama_area 
		from (
			select extract(year_month from a.tanggal_delivered) as bulan_delivered, b.volume_po as jum_vol, f.id_marketing, g.fullname, d.id_area, e.nama_area 
			from pro_po_ds_detail a
			join pro_po_detail b on a.id_pod = b.id_pod 
			join pro_po_customer c on a.id_poc = c.id_poc 
			join pro_penawaran d on c.id_penawaran = d.id_penawaran 
			join pro_master_area e on d.id_area = e.id_master 
			join pro_customer f on c.id_customer = f.id_customer 
			join acl_user g on f.id_marketing = g.id_user 
			where a.is_delivered = 1 ".$where." 
			UNION ALL
			select extract(year_month from a.tanggal_delivered) as bulan_delivered, a.bl_lo_jumlah as jum_vol, f.id_marketing, g.fullname, d.id_area, e.nama_area 
			from pro_po_ds_kapal a 
			join pro_po_customer b on a.id_poc = b.id_poc 
			join pro_penawaran d on b.id_penawaran = d.id_penawaran 
			join pro_master_area e on d.id_area = e.id_master 
			join pro_customer f on b.id_customer = f.id_customer 
			join acl_user g on f.id_marketing = g.id_user 
			where a.is_delivered = 1 ".$where." 
		) a group by bulan_delivered, id_area";
		
	if(isset($_POST['chart']) && $_POST['chart']=='1'){
		$sql = "
		select sum(jum_vol) as volume, nama_area 
		from (
			select extract(year_month from a.tanggal_delivered) as bulan_delivered, b.volume_po as jum_vol, f.id_marketing, g.fullname, d.id_area, e.nama_area 
			from pro_po_ds_detail a
			join pro_po_detail b on a.id_pod = b.id_pod 
			join pro_po_customer c on a.id_poc = c.id_poc 
			join pro_penawaran d on c.id_penawaran = d.id_penawaran 
			join pro_master_area e on d.id_area = e.id_master 
			join pro_customer f on c.id_customer = f.id_customer 
			join acl_user g on f.id_marketing = g.id_user 
			where a.is_delivered = 1 ".$where." 
			UNION ALL
			select extract(year_month from a.tanggal_delivered) as bulan_delivered, a.bl_lo_jumlah as jum_vol, f.id_marketing, g.fullname, d.id_area, e.nama_area 
			from pro_po_ds_kapal a 
			join pro_po_customer b on a.id_poc = b.id_poc 
			join pro_penawaran d on b.id_penawaran = d.id_penawaran 
			join pro_master_area e on d.id_area = e.id_master 
			join pro_customer f on b.id_customer = f.id_customer 
			join acl_user g on f.id_marketing = g.id_user 
			where a.is_delivered = 1 ".$where." 
		) a group by  id_area
		";
		$result 	= $con->getResult($sql);
		$chart	= array();
		foreach($result as $data){
			$chart['data'][] = (int)$data['volume'];
			$chart['name'][] = $data['nama_area'];
		}
		echo json_encode($chart);exit;
	}

	if(is_numeric($length)){
		$tot_record = $con->num_rows($sql);
		$tot_page 	= ceil($tot_record/$length);
		$page		= ($start > $tot_page)?$start-1:$start; 
		$position 	= $p->findPosition($length, $tot_record, $page);
		$sql .= " order by a.bulan_delivered desc, a.id_area limit ".$position.", ".$length;
	} else{
		$tot_record = $con->num_rows($sql);
		$page		= 1; 
		$position 	= 0;
		$sql .= " order by a.bulan_delivered desc, a.id_area";
	}
	$link = BASE_URL_CLIENT.'/report/c-area-performance-exp.php?'.paramEncrypt('q1='.$q1.'&q2='.$q2.'&q3='.$q3);

	$content = "";
	if($tot_record == 0){
		$content .= '<tr><td colspan="3" style="text-align:center"><input type="hidden" id="uriExp" value="'.$link.'" />Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= (is_numeric($length))?ceil($tot_record/$length):1;
		$result 	= $con->getResult($sql);
		$tot1 		= 0;
		$arrBln 	= array(1=>"Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
		foreach($result as $data){
			$count++;
        	$temp = $arrBln[intval(substr($data['bulan_delivered'],4,2))]." ".substr($data['bulan_delivered'],0,4);
			$tot1 = $tot1 + $data['volume'];
			$content .= '
				<tr>
					<td class="text-center">'.$temp.'</td>
					<td class="text-left">'.$data['nama_area'].'</td>
					<td class="text-right">'.number_format($data['volume']).'</td>
				</tr>';
		}
		$content .= '
			<tr>
				<td class="text-center bg-gray" colspan="2"><input type="hidden" id="uriExp" value="'.$link.'" /><b>TOTAL</b></td>
				<td class="text-right bg-gray"><b>'.number_format(($tot1)).'</b></td>
			</tr>';
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

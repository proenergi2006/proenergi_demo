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
	$where1 = "";
	$where2 = "";

	$q1	= isset($_POST["q1"])?htmlspecialchars($_POST["q1"], ENT_QUOTES):'';
	$q2	= isset($_POST["q2"])?htmlspecialchars($_POST["q2"], ENT_QUOTES):'';
	$q3	= isset($_POST["q3"])?htmlspecialchars($_POST["q3"], ENT_QUOTES):'';
	$q4	= isset($_POST["q4"])?htmlspecialchars($_POST["q4"], ENT_QUOTES):'';
	$q5	= isset($_POST["q5"])?htmlspecialchars($_POST["q5"], ENT_QUOTES):'';
	
	if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 9){
		$where1 .= " and n.id_wilayah = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_wilayah"])."'";
		$where2 .= " and n.id_wilayah = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_wilayah"])."'";
	}

	if($q1){ 
		$where1 .= " and h.tanggal_poc = '".tgl_db($q1)."'";
		$where2 .= " and h.tanggal_poc = '".tgl_db($q1)."'";
	}
	if($q2){
		 $where1 .= " and upper(n.nama_customer) like '%".strtoupper($q1)."%'";
		 $where2 .= " and upper(n.nama_customer) like '%".strtoupper($q2)."%'";
	}
	if($q3 != ""){
		 $where1 .= " and b.is_bayar = '".$q3."'";
		 $where2 .= " and a.is_bayar = '".$q3."'";
	}
	if($q4){
		 $where1 .= " and n.id_wilayah = '".$q4."'";
		 $where2 .= " and n.id_wilayah = '".$q4."'";
	}
	if($q5){
		 $where1 .= " and l.id_area = '".$q5."'";
		 $where2 .= " and l.id_area = '".$q5."'";
	}
	
	$p = new paging;
	$sql = "
		select * from (
			select f.tanggal_pr, n.nama_customer, n.kode_pelanggan, i.alamat_survey, j.nama_prov, k.nama_kab, h.nomor_poc, h.tanggal_poc, d.volume_po as jum_vol, 
			b.tanggal_delivered, l.refund_tawar, b.is_bayar, b.tanggal_bayar, b.ket_bayar, m.nama_area 
			from pro_po_ds a
			join pro_po_ds_detail b on a.id_ds = b.id_ds 
			join pro_po_detail d on b.id_pod = d.id_pod 
			join pro_po c on d.id_po = c.id_po 
			join pro_pr_detail e on d.id_prd = e.id_prd 
			join pro_pr f on e.id_pr = f.id_pr 
			join pro_po_customer_plan g on e.id_plan = g.id_plan 
			join pro_po_customer h on g.id_poc = h.id_poc 
			join pro_customer_lcr i on g.id_lcr = i.id_lcr 
			join pro_master_provinsi j on i.prov_survey = j.id_prov 
			join pro_master_kabupaten k on i.kab_survey = k.id_kab 
			join pro_penawaran l on h.id_penawaran = l.id_penawaran 
			join pro_master_area m on l.id_area = m.id_master 
			join pro_customer n on h.id_customer = n.id_customer 
			join pro_master_cabang o on n.id_wilayah = o.id_master 
			join acl_user p on n.id_marketing = p.id_user 
			where b.is_delivered = 1 and l.refund_tawar != 0 ".$where1." 
			UNION ALL
			select f.tanggal_pr, n.nama_customer, n.kode_pelanggan, i.alamat_survey, j.nama_prov, k.nama_kab, h.nomor_poc, h.tanggal_poc, a.bl_lo_jumlah as jum_vol, 
			a.tanggal_delivered, l.refund_tawar, a.is_bayar, a.tanggal_bayar, a.ket_bayar, m.nama_area 
			from pro_po_ds_kapal a 
			join pro_pr_detail e on a.id_prd = e.id_prd 
			join pro_pr f on e.id_pr = f.id_pr 
			join pro_po_customer_plan g on e.id_plan = g.id_plan 
			join pro_po_customer h on g.id_poc = h.id_poc 
			join pro_customer_lcr i on g.id_lcr = i.id_lcr 
			join pro_master_provinsi j on i.prov_survey = j.id_prov 
			join pro_master_kabupaten k on i.kab_survey = k.id_kab 
			join pro_penawaran l on h.id_penawaran = l.id_penawaran 
			join pro_master_area m on l.id_area = m.id_master 
			join pro_customer n on h.id_customer = n.id_customer 
			join pro_master_cabang o on n.id_wilayah = o.id_master 
			join acl_user p on n.id_marketing = p.id_user 
			join pro_master_transportir q on a.transportir = q.id_master 
			join pro_master_terminal t on a.terminal = t.id_master 
			where a.is_delivered = 1 and l.refund_tawar != 0 ".$where2." 
		) a";

	if(is_numeric($length)){
		$tot_record = $con->num_rows($sql);
		$tot_page 	= ceil($tot_record/$length);
		$page		= ($start > $tot_page)?$start-1:$start; 
		$position 	= $p->findPosition($length, $tot_record, $page);
		$sql .= " order by tanggal_delivered desc limit ".$position.", ".$length;
	} else{
		$tot_record = $con->num_rows($sql);
		$page		= 1; 
		$position 	= 0;
		$sql .= " order by tanggal_delivered desc";
	}
	$link = BASE_URL_CLIENT.'/report/f-refund-exp.php?'.paramEncrypt('q1='.$q1.'&q2='.$q2.'&q3='.$q3.'&q4='.$q4.'&q5='.$q5);

	$content = "";
	if($tot_record == 0){
		$content .= '<tr><td colspan="8" style="text-align:center"><input type="hidden" id="uriExp" value="'.$link.'" />Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= (is_numeric($length))?ceil($tot_record/$length):1;
		$result 	= $con->getResult($sql);
		$tot1 = 0; $tot2 = 0; $tot3 = 0;
		foreach($result as $data){
			$count++;
			$tempal = strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
			$alamat = $data['alamat_survey']." ".ucwords($tempal)." ".$data['nama_prov'];
			$totalF = $data['refund_tawar'] * $data['jum_vol'];
			$tot1 	= $tot1 + $data['jum_vol'];
			$tot2 	= $tot2 + $data['refund_tawar'];
			$tot3 	= $tot3 + $totalF;
			if($data['is_bayar']){
				$status = ' <p style="margin-bottom:0px;"><b>Terbayar</b></p>
							<p style="margin-bottom:0px;">'.date("d/m/Y", strtotime($data['tanggal_bayar'])).'</p>
							<p style="margin-bottom:0px;">'.$data['ket_bayar'].'</p>';
			} else{
				$status = '<p style="margin-bottom:0px;"><b>Diproses</b></p>';
			}

        	$content .= '
				<tr>
					<td class="text-center">'.date("d/m/Y", strtotime($data['tanggal_pr'])).'</td>
					<td class="text-left">
						<p style="margin-bottom:0px;"><b>'.$data['kode_pelanggan'].'</b></p>
						<p style="margin-bottom:0px;">'.$data['nama_customer'].'</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px;"><b>'.$data['nama_area'].'</b></p>
						<p style="margin-bottom:0px;">'.$alamat.'</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px;"><b>'.$data['nomor_poc'].'</b></p>
						<p style="margin-bottom:0px;">'.date("d/m/Y", strtotime($data['tanggal_poc'])).'</p>
						<p style="margin-bottom:0px;"> Tgl Terkirim : '.date("d/m/Y", strtotime($data['tanggal_delivered'])).'</p>
					</td>
					<td class="text-right">'.number_format($data['jum_vol']).'</td>
					<td class="text-right">'.number_format($data['refund_tawar']).'</td>
					<td class="text-right">'.number_format($totalF).'</td>
					<td class="text-left">'.$status.'</td>
				</tr>';
		}
		$content .= '
			<tr>
				<td class="text-center bg-gray" colspan="4"><b>TOTAL</b></td>
				<td class="text-right bg-gray"><b>'.number_format($tot1).'</b></td>
				<td class="text-center bg-gray"></td>
				<td class="text-right bg-gray"><b>'.number_format($tot3).'</b></td>
				<td class="text-right bg-gray"><input type="hidden" id="uriExp" value="'.$link.'" /></td>
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

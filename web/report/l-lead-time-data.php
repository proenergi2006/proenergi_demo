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
	$q6	= isset($_POST["q6"])?htmlspecialchars($_POST["q6"], ENT_QUOTES):'';
	$q7	= isset($_POST["q7"])?htmlspecialchars($_POST["q7"], ENT_QUOTES):'';
	$q8	= htmlspecialchars($_POST["q8"], ENT_QUOTES);
	$q9	= htmlspecialchars($_POST["q9"], ENT_QUOTES);
	$q10 = isset($_POST["q10"])?htmlspecialchars($_POST["q10"], ENT_QUOTES):null;
	
	if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 13){
		$where1 .= " and a.id_terminal = '".paramDecrypt($_SESSION["sinori".SESSIONID]["terminal"])."'";
		$where2 .= " and a.terminal = '".paramDecrypt($_SESSION["sinori".SESSIONID]["terminal"])."'";
	} else if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 9){
		$where1 .= " and n.id_wilayah = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_wilayah"])."'";
		$where2 .= " and n.id_wilayah = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_wilayah"])."'";
	}

	if($q1 && !$q2){ 
		$where1 .= " and b.tanggal_delivered between '".tgl_db($q1)." 00:00:00' and '".tgl_db($q1)." 23:59:59'";
		$where2 .= " and a.tanggal_delivered between '".tgl_db($q1)." 00:00:00' and '".tgl_db($q1)." 23:59:59'";
	} else if($q1 && $q2){
		$where1 .= " and b.tanggal_delivered between '".tgl_db($q1)." 00:00:00' and '".tgl_db($q2)." 23:59:59'";
		$where2 .= " and a.tanggal_delivered between '".tgl_db($q1)." 00:00:00' and '".tgl_db($q2)." 23:59:59'";
	}
	if($q3){
		 $where1 .= " and upper(n.nama_customer) like '%".strtoupper($q3)."%'";
		 $where2 .= " and upper(n.nama_customer) like '%".strtoupper($q3)."%'";
	}
	if($q4){
		 $where1 .= " and upper(d.no_spj) = '".strtoupper($q4)."'";
		 $where2 .= " and 1=2";
	}
	if($q5){
		 $where1 .= " and c.id_transportir = '".$q5."'";
		 $where2 .= " and a.transportir = '".$q5."'";
	}
	if($q6){
		 $where1 .= " and d.mobil_po = '".$q6."'";
		 $where2 .= " and 1=2";
	}
	if($q7){
		 $where1 .= " and d.sopir_po = '".$q7."'";
		 $where2 .= " and 1=2";
	}
	if($q8){
		 $where1 .= " and n.id_wilayah = '".$q8."'";
		 $where2 .= " and n.id_wilayah = '".$q8."'";
	}
	if($q9){
		 $where1 .= " and l.id_area = '".$q9."'";
		 $where2 .= " and l.id_area = '".$q9."'";
	}
	
	$p = new paging;
	$sql = "
		select * from (
			select date(b.tanggal_delivered) as periode_delivered, n.nama_customer, i.alamat_survey, j.nama_prov, k.nama_kab, h.nomor_poc, d.no_spj, q.nama_suplier, 
			q.nama_transportir, q.lokasi_suplier, r.nomor_plat, s.nama_sopir, t.nama_terminal, t.tanki_terminal, t.lokasi_terminal, d.volume_po as jum_vol, 
			b.tanggal_loaded, b.jam_loaded, b.tanggal_delivered, m.nama_area, o.nama_cabang 
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
			join pro_master_transportir q on c.id_transportir = q.id_master 
			join pro_master_transportir_mobil r on d.mobil_po = r.id_master 
			join pro_master_transportir_sopir s on d.sopir_po = s.id_master 
			join pro_master_terminal t on a.id_terminal = t.id_master 
			where b.is_delivered = 1 ".$where1." 
			UNION ALL
			select date(a.tanggal_delivered) as periode_delivered, n.nama_customer, i.alamat_survey, j.nama_prov, k.nama_kab, h.nomor_poc, '' as no_spj, q.nama_suplier, 
			q.nama_transportir, q.lokasi_suplier, a.vessel_name as nomor_plat, a.kapten_name as nama_sopir, t.nama_terminal, t.tanki_terminal, t.lokasi_terminal, 
			a.bl_lo_jumlah as jum_vol, a.tanggal_loaded, a.jam_loaded, a.tanggal_delivered, m.nama_area, o.nama_cabang 
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
			where a.is_delivered = 1 ".$where2." 
		) a ";

	if(is_numeric($length)){
		$tot_record = $con->num_rows($sql);
		$tot_page 	= ceil($tot_record/$length);
		$page		= ($start > $tot_page)?$start-1:$start; 
		$position 	= $p->findPosition($length, $tot_record, $page);
		$sql .= " order by periode_delivered desc limit ".$position.", ".$length;
	} else{
		$tot_record = $con->num_rows($sql);
		$page		= 1; 
		$position 	= 0;
		$sql .= " order by periode_delivered desc";
	}
	$link = BASE_URL_CLIENT.'/report/l-lead-time-exp.php?'.paramEncrypt('q1='.$q1.'&q2='.$q2.'&q3='.$q3.'&q4='.$q4.'&q5='.$q5.'&q6='.$q6.'&q7='.$q7.'&q8='.$q8.'&q9='.$q9);

	if ($q10) {
		echo $tot_record;
		exit;
	}

	$count = 0;
	$content = "";
	if($tot_record == 0){
		$content .= '<tr><td colspan="8" style="text-align:center"><input type="hidden" id="uriExp" value="'.$link.'" />Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= (is_numeric($length))?ceil($tot_record/$length):1;
		$result 	= $con->getResult($sql);
		$tot_vol    = 0;
		foreach($result as $data){
			$tot_vol += $data['jum_vol'];
			$count++;
			$tempal = strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
			$alamat = $data['alamat_survey']." ".ucwords($tempal)." ".$data['nama_prov'];
			$tgl1 	= strtotime($data['tanggal_loaded']." ".$data['jam_loaded']);
			$tgl2 	= strtotime($data['tanggal_delivered']);
			$leadtm = ($tgl2 - $tgl1);

        	$content .= '
				<tr>
					<td class="text-center">'.date("d/m/Y", strtotime($data['periode_delivered'])).'</td>
					<td class="text-left">
						<p style="margin-bottom:0px;"><b>'.$data['nomor_poc'].'</b></p>
						<p style="margin-bottom:0px;">'.$data['nama_customer'].'</p>
						<p style="margin-bottom:0px;">'.$data['nama_cabang'].'</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px;"><b>'.$data['nama_area'].'</b></p>
						<p style="margin-bottom:0px;">'.$alamat.'</p>
						<p style="margin-bottom:0px;">'.$data['nama_terminal'].' '.$data['tanki_terminal'].', '.$data['lokasi_terminal'].'</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px;"><b>'.$data['nama_suplier'].' - '.$data['nama_transportir'].', '.$data['lokasi_suplier'].'</b></p>
						<p style="margin-bottom:0px;">'.$data['nama_sopir'].'</p>
						<p style="margin-bottom:0px;">'.$data['nomor_plat'].'</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px;">'.($data['no_spj']?'<b>No. SJ '.$data['no_spj'].'</b>':'').'</p>
						<p style="margin-bottom:0px;">'.number_format($data['jum_vol']).' Liter</p>
					</td>
					<td class="text-left">'.date("d/m/Y", strtotime($data['tanggal_loaded'])).' '.$data['jam_loaded'].'</td>
					<td class="text-left">'.date("d/m/Y H:i", strtotime($data['tanggal_delivered'])).'</td>
					<td class="text-left">'.timeManHours($leadtm).'</td>
				</tr>';
		}
		$content .= '
			<tr>
				<td class="text-center bg-gray" colspan="4"><b>TOTAL</b></td>
				<td class="text-right bg-gray"><b>'.number_format(($tot_vol)).'</b></td>
				<td class="text-center bg-gray" colspan="3"></td>
			</tr>';
		$content .= '<tr class="hide"><td colspan="8"><input type="hidden" id="uriExp" value="'.$link.'" /></td></tr>';
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

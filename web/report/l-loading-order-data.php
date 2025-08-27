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
	
	if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 13){
		$where1 .= " and a.id_terminal = '".paramDecrypt($_SESSION["sinori".SESSIONID]["terminal"])."'";
		$where2 .= " and a.terminal = '".paramDecrypt($_SESSION["sinori".SESSIONID]["terminal"])."'";
	} else if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 9 || paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 10){
		$where1 .= " and n.id_wilayah = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_wilayah"])."'";
		$where2 .= " and n.id_wilayah = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_wilayah"])."'";
	}

	if($q1 && !$q2){ 
		$where1 .= " and g.tanggal_kirim = '".tgl_db($q1)."'";
		$where2 .= " and g.tanggal_kirim = '".tgl_db($q1)."'";
	} else if($q1 && $q2){
		$where1 .= " and g.tanggal_kirim between '".tgl_db($q1)."' and '".tgl_db($q2)."'";
		$where2 .= " and g.tanggal_kirim between '".tgl_db($q1)."' and '".tgl_db($q1)."'";
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
		 $where1 .= " and upper(e.nomor_lo_pr) = '".strtoupper($q5)."'";
		 $where2 .= " and upper(e.nomor_lo_pr) = '".strtoupper($q5)."'";
	}
	if($q6){
		 $where1 .= " and upper(b.nomor_order) = '".strtoupper($q6)."'";
		 $where2 .= " and 1=2";
	}
	if($q7){
		 $where1 .= " and n.id_wilayah = '".$q7."'";
		 $where2 .= " and n.id_wilayah = '".$q7."'";
	}
	if($q8){
		 $where1 .= " and l.id_area = '".$q8."'";
		 $where2 .= " and l.id_area = '".$q8."'";
	}
	
	$p = new paging;
	$sql = "
		select * from (
			select g.tanggal_kirim, e.nomor_lo_pr, e.no_do_acurate, b.nomor_order, n.nama_customer, i.alamat_survey, j.nama_prov, k.nama_kab, h.nomor_poc, d.no_spj, b.nomor_do, 
			q.nama_suplier, q.nama_transportir, q.lokasi_suplier, r.nomor_plat, s.nama_sopir, t.nama_terminal, t.tanki_terminal, t.lokasi_terminal, d.volume_po as jum_vol, 
			b.realisasi_volume, b.jumlah_segel, b.pre_segel, b.nomor_segel_awal, b.nomor_segel_akhir, m.nama_area, o.nama_cabang, f.nomor_pr 
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
			select g.tanggal_kirim, e.nomor_lo_pr, '' as nomor_order, n.nama_customer, i.alamat_survey, j.nama_prov, k.nama_kab, h.nomor_poc, '' as no_spj, 
			a.nomor_dn_kapal as nomor_do, q.nama_suplier, q.nama_transportir, q.lokasi_suplier, a.vessel_name as nomor_plat, a.kapten_name as nama_sopir, 
			t.nama_terminal, t.tanki_terminal, t.lokasi_terminal, a.bl_lo_jumlah as jum_vol, a.realisasi_volume, 
			'' as jumlah_segel, '' as pre_segel, '' as nomor_segel_awal, '' as nomor_segel_akhir, m.nama_area, o.nama_cabang, e.no_do_acurate, f.nomor_pr 
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
		$sql .= " order by tanggal_kirim desc limit ".$position.", ".$length;
	} else{
		$tot_record = $con->num_rows($sql);
		$page		= 1; 
		$position 	= 0;
		$sql .= " order by tanggal_kirim desc";
	}
	$link = BASE_URL_CLIENT.'/report/l-loading-order-exp.php?'.paramEncrypt('q1='.$q1.'&q2='.$q2.'&q3='.$q3.'&q4='.$q4.'&q5='.$q5.'&q6='.$q6.'&q7='.$q7.'&q8='.$q8);

	$content = "";
	if($tot_record == 0){
		$content .= '<tr><td colspan="8" style="text-align:center"><input type="hidden" id="uriExp" value="'.$link.'" />Data tidak ditemukan </td></tr>';
	} else{
		$count 				= $position;
		$tot_page 			= (is_numeric($length))?ceil($tot_record/$length):1;
		$result 			= $con->getResult($sql);
		$tot_vol_sj 		= 0;
		$tot_vol_realisasi 	= 0;
		foreach($result as $data){
			$tot_vol_sj 		+= $data['jum_vol'];
			$tot_vol_realisasi 	+= $data['realisasi_volume'];
			$count++;
			$tempal = strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
			$alamat = $data['alamat_survey']." ".ucwords($tempal)." ".$data['nama_prov'];
			$seg_aw = ($data['nomor_segel_awal'])?str_pad($data['nomor_segel_awal'],4,'0',STR_PAD_LEFT):'';
			$seg_ak = ($data['nomor_segel_akhir'])?str_pad($data['nomor_segel_akhir'],4,'0',STR_PAD_LEFT):'';
			if($data['jumlah_segel'] == 1)
				$nomor_segel = $data['pre_segel']."-".$seg_aw;
			else if($data['jumlah_segel'] == 2)
				$nomor_segel = $data['pre_segel']."-".$seg_aw." &amp; ".$data['pre_segel']."-".$seg_ak;
			else if($data['jumlah_segel'] > 2)
				$nomor_segel = $data['pre_segel']."-".$seg_aw." s/d ".$data['pre_segel']."-".$seg_ak;
			else $nomor_segel = '';

        	$content .= '
				<tr>
					<td class="text-center">'.date("d/m/Y", strtotime($data['tanggal_kirim'])).'</td>
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
						<p style="margin-bottom:0px;"><b>'.$data['nomor_lo_pr'].'</b></p>
						<p style="margin-bottom:0px;">'.$data['nomor_order'].'</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>NO DO Accurate : </b>'.($data['no_do_acurate'] ? $data['no_do_acurate'] : 'N/A').'</b></p>
						<p style="margin-bottom:0px"><b>NO DR : </b>'.$data['nomor_pr'] .'</b></p>
	
					</td>
					<td class="text-right">'.number_format($data['jum_vol']).'</td>
					<td class="text-right">'.number_format($data['realisasi_volume']).'</td>
					<td class="text-left">
						<p style="margin-bottom:0px;">'.($data['nomor_do']?'<b>'.$data['nomor_do'].'</b>':'').'</p>
						<p style="margin-bottom:0px;">'.($data['no_spj']?$data['no_spj']:'').'</p>
						<p style="margin-bottom:0px;">'.($nomor_segel?$nomor_segel:'').'</p>
					</td>
				</tr>';
		}
		$content .= '
			<tr>
				<td class="text-center bg-gray" colspan="5"><b>TOTAL</b></td>
				<td class="text-right bg-gray"><b>'.number_format(($tot_vol_sj)).'</b></td>
				<td class="text-right bg-gray"><b>'.number_format(($tot_vol_realisasi)).'</b></td>
				<td class="text-center bg-gray"></td>
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

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
	$q8	= htmlspecialchars($_POST["q8"], ENT_QUOTES);
	$q9	= htmlspecialchars($_POST["q9"], ENT_QUOTES);
	
	if($q1 && !$q2){ 
		$where .= " and b.tanggal_delivered between '".tgl_db($q1)." 00:00:00' and '".tgl_db($q1)." 23:59:59'";
	} else if($q1 && $q2){
		$where .= " and b.tanggal_delivered between '".tgl_db($q1)." 00:00:00' and '".tgl_db($q2)." 23:59:59'";
	}
	if($q3) $where .= " and upper(n.nama_customer) like '%".strtoupper($q3)."%'";
	if($q6) $where .= " and i.kab_survey = '".$q6."'";
	if($q7) $where .= " and n.id_wilayah = '".$q7."'";
	if($q8) $where .= " and n.id_marketing = '".$q8."'";
	if($q9) $where .= " and l.id_area = '".$q9."'";
	
	$p = new paging;
	// Lasamba => menambahkan kolom select untuk harga_dasar dan detail_rincian dari tabel pro penawaran
	$sql = "
		select sum(jum_vol) as volume, tanggal_delivered, id_customer, nama_customer, id_wilayah, nama_cabang, kab_survey, nama_kab, id_marketing, fullname, 
		id_area, nama_area, harga_asli, harga_minyak, pr_price_list , harga_dasar, detail_rincian, oa_kirim
		from (
			select date(b.tanggal_delivered) as tanggal_delivered, n.id_customer, n.nama_customer, n.id_marketing, p.fullname, n.id_wilayah, o.nama_cabang, l.id_area, m.nama_area, 
			i.kab_survey, k.nama_kab, d.volume_po as jum_vol, l.harga_asli, e.pr_price_list, q.harga_minyak , l.harga_dasar, l.detail_rincian, l.oa_kirim
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
			left join pro_master_harga_pertamina q on l.masa_awal = q.periode_awal and l.masa_akhir = q.periode_akhir and l.id_area = q.id_area and l.produk_tawar = q.id_produk 
			where b.is_delivered = 1 ".$where." 
			UNION ALL
			select date(b.tanggal_delivered) as tanggal_delivered, n.id_customer, n.nama_customer, n.id_marketing, p.fullname, n.id_wilayah, o.nama_cabang, l.id_area, m.nama_area, 
			i.kab_survey, k.nama_kab, b.bl_lo_jumlah as jum_vol, l.harga_asli, e.pr_price_list, q.harga_minyak , l.harga_dasar, l.detail_rincian, l.oa_kirim
			from pro_po_ds_kapal b 
			join pro_pr_detail e on b.id_prd = e.id_prd 
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
			left join pro_master_harga_pertamina q on l.masa_awal = q.periode_awal and l.masa_akhir = q.periode_akhir and l.id_area = q.id_area and l.produk_tawar = q.id_produk 
			where b.is_delivered = 1 ".$where2." 
		) a group by tanggal_delivered, id_customer, id_wilayah, kab_survey, id_marketing, id_area, harga_asli, harga_minyak, pr_price_list, harga_dasar, detail_rincian, oa_kirim";
	if($q4 && $q5){
		$arrOp = array(1=>"=", ">=", "<=");
		$sql .= " having sum(jum_vol) ".$arrOp[$q4]." '".str_replace(array(".",","),array("",""),$q5)."'";
	}

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
	
	$link = BASE_URL_CLIENT.'/report/c-harga-market-exp.php?'.paramEncrypt('q1='.$q1.'&q2='.$q2.'&q3='.$q3.'&q4='.$q4.'&q5='.$q5.'&q6='.$q6.'&q7='.$q7.'&q8='.$q8.'&q9='.$q9);

	$content = "";
	if($tot_record == 0){
		$content .= '<tr><td colspan="9" style="text-align:center"><input type="hidden" id="uriExp" value="'.$link.'" />Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= (is_numeric($length))?ceil($tot_record/$length):1;
		$result 	= $con->getResult($sql);
		$nom1 		= 0;
		$tot1 		= 0;
		$tot_vol 	= 0;
		foreach($result as $data){
			$tot_vol += $data['volume'];
			$count++;
			$tempal = strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
			$arHj = json_decode($data['detail_rincian'], true);
			/*if($data['harga_minyak']){
				$nom1++;
				$disc = (1-($data['harga_asli']/$data['harga_minyak'])) * 100;
				$tot1 = $tot1 + $disc;
				$dist = number_format($disc).'%';
			} else{
				$disc = 0;
				$tot1 = $tot1 + $disc;
				$dist = '';
			}*/
			//Lasamba
			if($data['harga_minyak'] && $data['harga_dasar']){
				if($data['harga_dasar'] > $data['harga_minyak']){ // Jika Harga Jual Dasar lebih besar dari harga pertamina
					$disc = (((abs($data['harga_minyak']-$data['harga_dasar'])) / $data['harga_minyak']) + 1) * 100;
				}else{
					$disc = (($data['harga_minyak']-$data['harga_dasar']) / $data['harga_minyak']) * 100;
				}
				$nom1++;
				$tot1 = $tot1 + $disc;
			}else{
				$disc = '';
			}
			$harga_ppn = $data['harga_dasar'] + $arHj[2]['biaya'];

        	$content .= '
				<tr>
					<td class="text-center">'.date("d/m/Y", strtotime($data['tanggal_delivered'])).'</td>
					<td class="text-left">
						<p style="margin-bottom:0px;"><b>'.$data['nama_customer'].'</b></p>
						<p style="margin-bottom:0px;">'.$data['fullname'].'</p>
					</td>
					<td class="text-center">'.$data['nama_cabang'].'</td>
					<td class="text-center">'.$data['nama_area'].'</td>
					<td class="text-center">'.ucwords($tempal).'</td>
					<td class="text-right">'.number_format($data['volume']).'</td>
					<td class="text-right">'.number_format($data['harga_minyak']).'</td>
					<td class="text-right">'.number_format($data['harga_dasar']).'</td>
					<td class="text-right">'.number_format($harga_ppn).'</td>
					<td class="text-right">'.number_format($data['oa_kirim']).'</td>
					<td class="text-right">'.($disc?number_format($disc).'%':'-').'</td>
				</tr>';
		}
		
		$avgDisc  = ($nom1)?$tot1/$nom1:0;
		$content .= '
			<tr>
				<td class="text-center bg-gray" colspan="5"><b>TOTAL</b></td>
				<td class="text-right bg-gray"><b>'.number_format($tot_vol).'</b></td>
				<td class="text-center bg-gray" colspan="4"><input type="hidden" id="uriExp" value="'.$link.'" /><b>AVERAGE</b></td>
				<td class="text-right bg-gray"><b>'.number_format($avgDisc).'%</b></td>
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

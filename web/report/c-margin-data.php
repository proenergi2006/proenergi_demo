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
		 $where1 .= " and upper(e.nomor_lo_pr) = '".strtoupper($q4)."'";
		 $where2 .= " and upper(e.nomor_lo_pr) = '".strtoupper($q4)."'";
	}
	if($q5){
		 $where1 .= " and h.produk_poc = '".$q5."'";
		 $where2 .= " and h.produk_poc = '".$q5."'";
	}
	if($q6){
		 $where1 .= " and e.pr_vendor = '".$q6."'";
		 $where2 .= " and e.pr_vendor = '".$q6."'";
	}
	
	$p = new paging;
	$sql = "
		select * from (
			select date(b.tanggal_delivered) as tanggal_delivered, n.kode_pelanggan, n.nama_customer, o.nama_cabang, m.nama_area, q.jenis_produk, q.merk_dagang, 
			b.realisasi_volume, r.nilai_pbbkb, s.nama_vendor, t.nama_terminal, t.tanki_terminal, t.lokasi_terminal, e.nomor_lo_pr, h.harga_poc, l.refund_tawar, 
			d.ongkos_po as transport, e.pr_harga_beli 
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
			join pro_master_produk q on h.produk_poc = q.id_master 
			join pro_master_pbbkb r on l.pbbkb_tawar = r.id_master 
			join pro_master_vendor s on e.pr_vendor = s.id_master 
			join pro_master_terminal t on a.id_terminal = t.id_master 
			where b.is_delivered = 1 ".$where1." 
			UNION ALL
			select date(a.tanggal_delivered) as tanggal_delivered, n.kode_pelanggan, n.nama_customer, o.nama_cabang, m.nama_area, q.jenis_produk, q.merk_dagang, 
			a.realisasi_volume, r.nilai_pbbkb, s.nama_vendor, t.nama_terminal, t.tanki_terminal, t.lokasi_terminal, e.nomor_lo_pr, h.harga_poc, l.refund_tawar, 
			e.transport as transport, e.pr_harga_beli 
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
			join pro_master_produk q on h.produk_poc = q.id_master 
			join pro_master_pbbkb r on l.pbbkb_tawar = r.id_master 
			join pro_master_vendor s on e.pr_vendor = s.id_master 
			join pro_master_terminal t on a.terminal = t.id_master 
			where a.is_delivered = 1 ".$where2." 
		) a ";

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
	$link = BASE_URL_CLIENT.'/report/c-margin-exp.php?'.paramEncrypt('q1='.$q1.'&q2='.$q2.'&q3='.$q3.'&q4='.$q4.'&q5='.$q5.'&q6='.$q6);

	$content = "";
	if($tot_record == 0){
		$content .= '<tr><td colspan="8" style="text-align:center"><input type="hidden" id="uriExp" value="'.$link.'" />Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= (is_numeric($length))?ceil($tot_record/$length):1;
		$result 	= $con->getResult($sql);
		$tot1 = 0;
		$tot2 = 0;
		foreach($result as $data){
			$count++;
			$pbbkbT = ($data['nilai_pbbkb']/100) + 1.11;
			$oildus = $data['harga_poc'] / $pbbkbT * 0.003;
			$pbbkbN = $data['harga_poc'] / $pbbkbT * ($data['nilai_pbbkb']/100);
			$nethrg = $data['harga_poc'] - $data['refund_tawar'] - $oildus - $data['transport'] - $pbbkbN;
			$netprt = ($nethrg - $data['pr_harga_beli']) * $data['realisasi_volume'];
			$tot1	= $tot1 + $data['realisasi_volume']; 
			$tot2	= $tot2 + $netprt; 

        	$content .= '
				<tr>
					<td class="text-center">'.date("d/m/Y", strtotime($data['tanggal_delivered'])).'</td>
					<td class="text-left">
						<p style="margin-bottom:0px;"><b>'.$data['kode_pelanggan'].'</b></p>
						<p style="margin-bottom:0px;">'.$data['nama_customer'].'</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px;"><b>'.$data['nama_area'].'</b></p>
						<p style="margin-bottom:0px;">'.$data['jenis_produk'].' - '.$data['merk_dagang'].'</p>
					</td>
					<td class="text-right">'.number_format($data['realisasi_volume']).'</td>
					<td class="text-right">'.$data['nilai_pbbkb'].'%</td>
					<td class="text-left">'.$data['nama_vendor'].'</td>
					<td class="text-right">'.number_format($netprt).'</td>
					<td class="text-left">
						<p style="margin-bottom:0px;"><b>'.$data['nomor_lo_pr'].'</b></p>
						<p style="margin-bottom:0px;">'.$data['nama_terminal'].' '.$data['tanki_terminal'].', '.$data['lokasi_terminal'].'</p>
					</td>
				</tr>';
		}
		$content .= '
			<tr>
				<td class="text-center bg-gray" colspan="3"><b>TOTAL</b></td>
				<td class="text-right bg-gray"><b>'.number_format($tot1).'</b></td>
				<td class="text-center bg-gray" colspan="2">&nbsp;</td>
				<td class="text-right bg-gray"><b>'.number_format($tot2).'</b></td>
				<td class="text-right bg-gray"><input type="hidden" id="uriExp" value="'.$link.'" /></td>
			</tr>
			<tr>
				<td class="text-right" colspan="6"><b>Average Margin/Liter</b></td>
				<td class="text-right"><b>'.number_format(($tot2/$tot1)).'</b></td>
				<td class="text-right">&nbsp;</td>
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

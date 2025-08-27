<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	require_once ($public_base_directory."/libraries/helper/class.xlsxwriter.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$sheet 	= 'Sheet1';
	$where 	= "";

	$q1	= htmlspecialchars($enk["q1"], ENT_QUOTES);
	$q2	= htmlspecialchars($enk["q2"], ENT_QUOTES);
	$q3	= htmlspecialchars($enk["q3"], ENT_QUOTES);
	$q4	= htmlspecialchars($enk["q4"], ENT_QUOTES);
	$q5	= htmlspecialchars($enk["q5"], ENT_QUOTES);
	$q6	= htmlspecialchars($enk["q6"], ENT_QUOTES);
	$q7	= htmlspecialchars($enk["q7"], ENT_QUOTES);
	$q8	= htmlspecialchars($enk["q8"], ENT_QUOTES);
	$q9	= htmlspecialchars($enk["q9"], ENT_QUOTES);

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
	
	$sql = "
		select sum(jum_vol) as volume, tanggal_delivered, id_customer, nama_customer, id_wilayah, nama_cabang, kab_survey, nama_kab, id_marketing, fullname, 
		id_area, nama_area, harga_asli, harga_minyak, pr_price_list  , harga_dasar, detail_rincian, oa_kirim
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
	$sql .= " order by a.tanggal_delivered desc, a.id_customer";
	$res = $con->getResult($sql);
	

	$filename 	= "Laporan-Harga-Market-".date('dmYHis').".xlsx";
	$arrOp 		= array(1=>"=", ">=", "<=");
	header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
	ob_end_clean();
	header('Content-type: application/vnd.ms-excel');
	// header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');

	$writer = new XLSXWriter();
	$writer->writeSheetHeader($sheet, array('Laporan Harga Trend Market'=>'string'));
	$writer->newMergeCell($sheet, "A1", "I1");
	$start = 2;
	$patok = 1;
	if($q1 && !$q2){
		$writer->writeSheetHeaderExt($sheet, array("Periode Delivery : ".$q1=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "I".$start);
		$patok++;
		$start++;
	} else if($q1 && $q2){
		$writer->writeSheetHeaderExt($sheet, array("Periode Delivery : ".$q1." s/d ".$q2=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "I".$start);
		$patok++;
		$start++;
	}
	if($q3){
		$writer->writeSheetHeaderExt($sheet, array("Customer : ".$q3=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "I".$start);
		$patok++;
		$start++;
	} 
	if($q4 && $q5){
		$writer->writeSheetHeaderExt($sheet, array("Volume Delivery ".$arrOp[$q4]." ".$q5=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "I".$start);
		$patok++;
		$start++;
	} 
	if($q6){
		$q6Txt = $con->getOne("select nama_kab from pro_master_kabupaten where id_kab = '".$q6."'");
		$q6Tmp = strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $q6Txt));
		$writer->writeSheetHeaderExt($sheet, array("Wilayah Kirim : ".ucwords($q6Tmp)=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "I".$start);
		$patok++;
		$start++;
	} 
	if($q9){
		$q9Txt = $con->getOne("select nama_area from pro_master_area where id_master = '".$q9."'");
		$writer->writeSheetHeaderExt($sheet, array("Area : ".$q9Txt=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "I".$start);
		$patok++;
		$start++;
	} 
	if($q7){
		$q7Txt = $con->getOne("select nama_cabang from pro_master_cabang where id_master = '".$q7."'");
		$writer->writeSheetHeaderExt($sheet, array("Cabang Invoice : ".$q7Txt=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "I".$start);
		$patok++;
		$start++;
	} 
	if($q8){
		$q8Txt = $con->getOne("select fullname from acl_user where id_user = '".$q8."'");
		$writer->writeSheetHeaderExt($sheet, array("Marketing : ".$q8Txt=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "I".$start);
		$patok++;
		$start++;
	} 
	$writer->writeSheetHeaderExt($sheet, array(""=>"string"));
	$patok++;
	$start++;
	$writer->setColumnIndex($patok);

	$header = array(
		"Tgl Delivery"=>'string',
		"Customer"=>'string',
		"Marketing"=>'string',
		"Area"=>'string',
		"Wilayah Kirim"=>'string',
		"Volume Terkirim (Liter)"=>'string',
		"Harga Dasar Pertamina"=>'string',
		"Harga Jual (Dasar)"=>'string',
		"Harga Jual (Inc. Tax)"=>'string',
		"Ongkos Kirim"=>'string',
		"% Harga Jual vs Harga Dasar Pertamina"=>'0%',
	);
	$writer->writeSheetHeaderExt($sheet, $header);
	$start++;

	if(count($res) > 0){
		$tot1 = 0;
		$last = $start-1;
		foreach($res as $data){
			$last++;
			$tempal = strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
			$arHj = json_decode($data['detail_rincian'], true);
			if($data['harga_minyak'] && $data['harga_dasar']){
				if($data['harga_dasar'] > $data['harga_minyak']){ // Jika Harga Jual Dasar lebih besar dari harga pertamina
					$disc = (((abs($data['harga_minyak']-$data['harga_dasar'])) / $data['harga_minyak']) + 1) ;
				}else{
					$disc = (($data['harga_minyak']-$data['harga_dasar']) / $data['harga_minyak']) ;
				}
				$nom1++;
				$tot1 = $tot1 + $disc;
			}else{
				$disc = '';
			}
			$harga_ppn = $data['harga_dasar'] + $arHj[2]['biaya'];

			$writer->writeSheetRow($sheet, array(
				date("d/m/Y", strtotime($data['tanggal_delivered'])), $data['nama_customer'], $data['fullname'], $data['nama_area'], ucwords($tempal), 
				$data['volume'], $data['harga_minyak'], $data['harga_dasar'], $harga_ppn, $data['oa_kirim'], $disc
			));
		}
		$writer->writeSheetRow($sheet, array("TOTAL", "", "", "", "","=SUM(F".$start.":F".$last.")", "AVERAGE", "", "", "", "=AVERAGE(K".$start.":K".$last.")"));
		$last++;
		$writer->newMergeCell($sheet, "A".$last, "E".$last);
		$writer->newMergeCell($sheet, "G".$last, "J".$last);
	} else{
		$writer->writeSheetRow($sheet, array("Data tidak ada"));
		$writer->newMergeCell($sheet, "A".$start, "I".$start);
		$start++;
	}
	
	$con->close();
	$writer->writeToStdOut();
	exit(0);

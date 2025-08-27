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
	$where	= "";

	$q1	= htmlspecialchars($enk["q1"], ENT_QUOTES);
	$q2	= htmlspecialchars($enk["q2"], ENT_QUOTES);
	$q3	= htmlspecialchars($enk["q3"], ENT_QUOTES);
	$q4	= htmlspecialchars($enk["q4"], ENT_QUOTES);
	$q5	= htmlspecialchars($enk["q5"], ENT_QUOTES);
	$q6	= htmlspecialchars($enk["q6"], ENT_QUOTES);
	$q7	= htmlspecialchars($enk["q7"], ENT_QUOTES);
	$q8	= htmlspecialchars($enk["q8"], ENT_QUOTES);
	$q9	= htmlspecialchars($enk["q9"], ENT_QUOTES);

	if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 11 || paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 17)
		$where .= " and f.id_marketing = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."'";
	else if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 7 || paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 9)
		$where .= " and f.id_wilayah = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_wilayah"])."'";
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
	if($q6) $where .= " and j.kab_survey = '".$q6."'";
	if($q7) $where .= " and f.id_wilayah = '".$q7."'";
	if($q8) $where .= " and f.id_marketing = '".$q8."'";
	if($q9) $where .= " and d.id_area = '".$q9."'";
	
	$p = new paging;
	$sql = "
		select sum(jum_vol) as volume, tanggal_delivered, id_customer, nama_customer, id_wilayah, nama_cabang, kab_survey, nama_kab, id_marketing, fullname, 
		id_area, nama_area, harga_asli, harga_minyak 
		from (
			select date(a.tanggal_delivered) as tanggal_delivered, b.volume_po as jum_vol, f.id_customer, f.nama_customer, f.id_wilayah, h.nama_cabang, j.kab_survey, 
			k.nama_kab, f.id_marketing, g.fullname, d.id_area, e.nama_area, d.harga_asli, l.harga_minyak 
			from pro_po_ds_detail a
			join pro_po_detail b on a.id_pod = b.id_pod 
			join pro_po_customer c on a.id_poc = c.id_poc 
			join pro_penawaran d on c.id_penawaran = d.id_penawaran 
			join pro_master_area e on d.id_area = e.id_master 
			join pro_customer f on c.id_customer = f.id_customer 
			join acl_user g on f.id_marketing = g.id_user 
			join pro_master_cabang h on f.id_wilayah = h.id_master 
			join pro_po_customer_plan i on a.id_plan = i.id_plan 
			join pro_customer_lcr j on i.id_lcr = j.id_lcr 
			join pro_master_kabupaten k on j.kab_survey = k.id_kab 
			left join pro_master_harga_pertamina l on d.masa_awal = l.periode_awal and d.masa_akhir = l.periode_akhir and d.id_area = l.id_area and d.produk_tawar = l.id_produk 
			where a.is_delivered = 1 ".$where." 
			UNION ALL
			select date(a.tanggal_delivered) as tanggal_delivered, a.bl_lo_jumlah as jum_vol, f.id_customer, f.nama_customer, f.id_wilayah, h.nama_cabang, j.kab_survey, 
			k.nama_kab, f.id_marketing, g.fullname, d.id_area, e.nama_area, d.harga_asli, l.harga_minyak  
			from pro_po_ds_kapal a 
			join pro_po_customer b on a.id_poc = b.id_poc 
			join pro_penawaran d on b.id_penawaran = d.id_penawaran 
			join pro_master_area e on d.id_area = e.id_master 
			join pro_customer f on b.id_customer = f.id_customer 
			join acl_user g on f.id_marketing = g.id_user 
			join pro_master_cabang h on f.id_wilayah = h.id_master 
			join pro_po_customer_plan i on a.id_plan = i.id_plan 
			join pro_customer_lcr j on i.id_lcr = j.id_lcr 
			join pro_master_kabupaten k on j.kab_survey = k.id_kab 
			left join pro_master_harga_pertamina l on d.masa_awal = l.periode_awal and d.masa_akhir = l.periode_akhir and d.id_area = l.id_area and d.produk_tawar = l.id_produk 
			where a.is_delivered = 1 ".$where." 
		) a group by tanggal_delivered, id_customer, id_wilayah, kab_survey, id_marketing, id_area, harga_asli, harga_minyak";
	if($q4 && $q5){
		$arrOp = array(1=>"=", ">=", "<=");
		$sql .= " having sum(jum_vol) ".$arrOp[$q4]." '".str_replace(array(".",","),array("",""),$q5)."'";
	}
	$sql .= " order by a.tanggal_delivered desc, a.id_customer";
	$res = $con->getResult($sql);
	

	$filename 	= "Laporan-realisasi-order-".date('dmYHis').".xlsx";
	$arrOp 		= array(1=>"=", ">=", "<=");
	header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
	ob_end_clean();
	header('Content-type: application/vnd.ms-excel');
	// header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');

	$writer = new XLSXWriter();
	$writer->writeSheetHeader($sheet, array('Laporan Realisasi Order'=>'string'));
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
		"Periode"=>'string',
		"Customer"=>'string',
		"Marketing"=>'string',
		"Cabang Invoice"=>'string',
		"Area"=>'string',
		"Wilayah Kirim"=>'string',
		"Volume Terkirim (Liter)"=>'string',
		"Harga Jual (Dasar)"=>'string',
		"% Disc Pertamina"=>'0%',
	);
	$writer->writeSheetHeaderExt($sheet, $header);
	$start++;

	if(count($res) > 0){
		$tot1 = 0;
		$last = $start-1;
		foreach($res as $data){
			$last++;
			$tempal = strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
			$disc 	= 0;
			if($data['harga_minyak'] && $data['harga_asli']){
				if($data['harga_asli'] > $data['harga_minyak']){ // Jika Harga Jual Dasar lebih besar dari harga pertamina
					$disc = (((abs($data['harga_minyak']-$data['harga_asli'])) / $data['harga_minyak']) + 1);
				}else{
					$disc = (($data['harga_minyak']-$data['harga_asli']) / $data['harga_minyak']);
					
				}
			}
			$dist = ($disc)?$disc:0;

			$writer->writeSheetRow($sheet, array(
				date("d/m/Y", strtotime($data['tanggal_delivered'])), $data['nama_customer'], $data['fullname'], $data['nama_cabang'], $data['nama_area'], 
				ucwords($tempal), $data['volume'], $data['harga_asli'], $dist
			));
		}
		$writer->writeSheetRow($sheet, array("", "", "", "", "", "TOTAL", "=SUM(G".$start.":G".$last.")", "AVERAGE", "=AVERAGE(I".$start.":I".$last.")"));
		$last++;
		$writer->newMergeCell($sheet, "A".$last, "E".$last);
	} else{
		$writer->writeSheetRow($sheet, array("Data tidak ada"));
		$writer->newMergeCell($sheet, "A".$start, "I".$start);
		$start++;
	}
	
	$con->close();
	$writer->writeToStdOut();
	exit(0);

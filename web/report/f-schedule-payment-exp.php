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
	$where	 = "";

	$q1	= htmlspecialchars($enk["q1"], ENT_QUOTES);
	$q2	= htmlspecialchars($enk["q2"], ENT_QUOTES);
	$q3	= htmlspecialchars($enk["q3"], ENT_QUOTES);
	$q4	= htmlspecialchars($enk["q4"], ENT_QUOTES);
	$q5	= htmlspecialchars($enk["q5"], ENT_QUOTES);
	$q6	= htmlspecialchars($enk["q6"], ENT_QUOTES);
	
	if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 9){
		$where .= " and j.id_wilayah = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_wilayah"])."'";
	}

	if($q1 && !$q2){ 
		$where .= " and b.tanggal_pr = '".tgl_db($q1)."'";
	} else if($q1 && $q2){
		$where .= " and b.tanggal_pr between '".tgl_db($q1)."' and '".tgl_db($q2)."'";
	}
	if($q3) $where .= " and upper(j.nama_customer) like '%".strtoupper($q3)."%'";
	if($q4) $where .= " and upper(a.schedule_payment) like '%".strtoupper($q4)."%'";
	if($q5) $where .= " and j.id_wilayah = '".$q5."'";
	if($q6) $where .= " and h.id_area = '".$q6."'";
	
	$sql = "
		select b.tanggal_pr, j.nama_customer, k.nama_cabang, i.nama_area, a.volume, a.schedule_payment 
		from pro_pr_detail a 
		join pro_pr b on a.id_pr = b.id_pr 
		join pro_po_customer_plan c on a.id_plan = c.id_plan 
		join pro_po_customer d on c.id_poc = d.id_poc 
		join pro_customer_lcr e on c.id_lcr = e.id_lcr 
		join pro_master_provinsi f on e.prov_survey = f.id_prov 
		join pro_master_kabupaten g on e.kab_survey = g.id_kab 
		join pro_penawaran h on d.id_penawaran = h.id_penawaran 
		join pro_master_area i on h.id_area = i.id_master 
		join pro_customer j on d.id_customer = j.id_customer 
		join pro_master_cabang k on j.id_wilayah = k.id_master 
		join acl_user l on j.id_marketing = l.id_user 
		where (a.pr_ar_satu != 0 or a.pr_ar_dua != 0) and a.is_approved = 1 ".$where;

	$sql .= " order by b.tanggal_pr desc";
	$res = $con->getResult($sql);
	

	$filename 	= "Laporan-Schedule-Payment-".date('dmYHis').".xlsx";
	$arrOp 		= array(1=>"=", ">=", "<=");
	header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
	ob_end_clean();
	header('Content-type: application/vnd.ms-excel');
	// header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');

	$writer = new XLSXWriter();
	$writer->writeSheetHeader($sheet, array('Laporan Schedule Payment'=>'string'));
	$writer->newMergeCell($sheet, "A1", "F1");
	$start = 2;
	$patok = 1;
	if($q1 && !$q2){
		$writer->writeSheetHeaderExt($sheet, array("Tanggal PR : ".$q1=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "F".$start);
		$patok++;
		$start++;
	} else if($q1 && $q2){
		$writer->writeSheetHeaderExt($sheet, array("Tanggal PR : ".$q1." s/d ".$q2=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "F".$start);
		$patok++;
		$start++;
	}
	if($q3){
		$writer->writeSheetHeaderExt($sheet, array("Customer : ".$q3=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "F".$start);
		$patok++;
		$start++;
	} 
	if($q4){
		$writer->writeSheetHeaderExt($sheet, array("Schedule Payment : ".$q4=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "F".$start);
		$patok++;
		$start++;
	}
	if($q5){
		$q5Txt = $con->getOne("select nama_cabang from pro_master_cabang where id_master = '".$q5."'");
		$writer->writeSheetHeaderExt($sheet, array("Cabang Invoice : ".$q5Txt=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "F".$start);
		$patok++;
		$start++;
	}
	if($q6){
		$q6Txt = $con->getOne("select nama_area from pro_master_area where id_master = '".$q6."'");
		$writer->writeSheetHeaderExt($sheet, array("Area : ".$q6Txt=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "F".$start);
		$patok++;
		$start++;
	}

	$writer->writeSheetHeaderExt($sheet, array(""=>"string"));
	$patok++;
	$start++;
	$writer->setColumnIndex($patok);

	$header = array(
		"Tanggal PR"=>'string',
		"Customer"=>'string',
		"Cabang Invoice"=>'string',
		"Area"=>'string',
		"Volume PR"=>'string',
		"Schedule Payment"=>'string',
	);
	$writer->writeSheetHeaderExt($sheet, $header);
	$start++;

	if(count($res) > 0){
		$tot1 = 0;
		$last = $start-1;
		foreach($res as $data){
			$last++;

			$writer->writeSheetRow($sheet, array(
				date("d/m/Y", strtotime($data['tanggal_pr'])), $data['nama_customer'], $data['nama_cabang'], $data['nama_area'], $data['volume'], $data['schedule_payment']
			));
		}
		$writer->writeSheetRow($sheet, array("", "", "", "TOTAL", "=SUM(E".$start.":E".$last.")", ""));
		$last++;
		$writer->newMergeCell($sheet, "A".$last, "C".$last);
	} else{
		$writer->writeSheetRow($sheet, array("Data tidak ada"));
		$writer->newMergeCell($sheet, "A".$start, "F".$start);
		$start++;
	}
	
	$con->close();
	$writer->writeToStdOut();
	exit(0);

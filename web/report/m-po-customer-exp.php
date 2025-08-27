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

	if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 11 || paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 17)
		$where .= " and b.id_marketing = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."'";
	else if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 7 || paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 18)
		$where .= " and b.id_wilayah = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_wilayah"])."'";
	else if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 6)
		$where .= " and (b.id_group = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_group"])."' or d.id_om = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."')";

	if($q1 && !$q2){ 
		$where .= " and a.tanggal_poc = '".tgl_db($q1)."'";
	} else if($q1 && $q2){
		$where .= " and a.tanggal_poc between '".tgl_db($q1)."' and '".tgl_db($q2)."'";
	}
	if($q3) $where .= " and upper(b.nama_customer) like '%".strtoupper($q3)."%'";
	if($q4) $where .= " and b.id_wilayah = '".$q4."'";
	if($q5) $where .= " and b.id_marketing = '".$q5."'";
	
	$sql = "select a.*, b.nama_customer, c.realisasi from pro_po_customer a join pro_customer b on a.id_customer = b.id_customer 
	left join (select id_poc, sum(realisasi_kirim) as realisasi from pro_po_customer_plan group by id_poc) c on a.id_poc = c.id_poc 
	where poc_approved = 1 ".$where." order by a.tanggal_poc desc, a.id_customer";
	$res = $con->getResult($sql);

	$filename 	= "Laporan-po-customer-".date('dmYHis').".xlsx";
	header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
	ob_end_clean();
	header('Content-type: application/vnd.ms-excel');
	// header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');

	$writer = new XLSXWriter();
	$writer->writeSheetHeader($sheet, array('Laporan PO Customer'=>'string'));
	$writer->newMergeCell($sheet, "A1", "F1");
	$start = 2;
	$patok = 1;
	if($q1 && !$q2){
		$writer->writeSheetHeaderExt($sheet, array("Periode PO : ".$q1=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "F".$start);
		$patok++;
		$start++;
	} else if($q1 && $q2){
		$writer->writeSheetHeaderExt($sheet, array("Periode PO : ".$q1." s/d ".$q2=>"string"));
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
		$start++;
		$q4Txt = $con->getOne("select nama_cabang from pro_master_cabang where id_master = '".$q4."'");
		$writer->writeSheetHeaderExt($sheet, array("Cabang Invoice : ".$q4Txt=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "F".$start);
		$patok++;
		$start++;
	} 
	if($q5){
		$start++;
		$q5Txt = $con->getOne("select fullname from acl_user where id_user = '".$q5."'");
		$writer->writeSheetHeaderExt($sheet, array("Marketing : ".$q5Txt=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "F".$start);
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
		"No. PO"=>'string',
		"Volume (Liter)"=>'string',
		"Realisasi (Liter)"=>'string',
		"Pending (Liter)"=>'string',
	);
	$writer->writeSheetHeaderExt($sheet, $header);
	$start++;

	if(count($res) > 0){
		$tot1 = 0;
		$last = $start-1;
		foreach($res as $data){
			$last++;
        	$pending = $data['volume_poc'] - $data['realisasi'];
			$writer->writeSheetRow($sheet, array(
				date("d/m/Y", strtotime($data['tanggal_poc'])), $data['nama_customer'], $data['nomor_poc'], $data['volume_poc'], $data['realisasi'], $pending
			));
		}
		$writer->writeSheetRow($sheet, array("", "", "TOTAL", "=SUM(D".$start.":D".$last.")", "", ""));
		$last++;
		$writer->newMergeCell($sheet, "A".$last, "B".$last);
	} else{
		$writer->writeSheetRow($sheet, array("Data tidak ada"));
		$writer->newMergeCell($sheet, "A".$start, "F".$start);
		$start++;
	}
	
	$con->close();
	$writer->writeToStdOut();
	exit(0);

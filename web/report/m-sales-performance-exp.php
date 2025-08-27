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

	if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 11 || paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 17)
		$where .= " and f.id_marketing = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."'";
	else if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 7 || paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 18)
		$where .= " and f.id_wilayah = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_wilayah"])."'";
	else if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 6)
		$where .= " and (f.id_group = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_group"])."' or g.id_om = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."')";

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
	if($q4) $where .= " and f.id_marketing = '".$q4."'";
	
	$sql = "
		select sum(jum_vol) as volume, bulan_delivered, id_marketing, fullname, id_area, nama_area 
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
		) a group by bulan_delivered, id_area, id_marketing order by a.bulan_delivered desc, a.id_area";
	$res = $con->getResult($sql);

	$filename 	= "Laporan-sales-performance-".date('dmYHis').".xlsx";
	header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
	ob_end_clean();
	header('Content-type: application/vnd.ms-excel');
	// header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');

	$arrBln = array(1=>"Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
	$writer = new XLSXWriter();
	$writer->writeSheetHeader($sheet, array('Laporan Sales Performance'=>'string'));
	$writer->newMergeCell($sheet, "A1", "D1");
	$start = 2;
	$patok = 1;
	if($q1 && !$q2){
		$tmp1 = explode("/", $q1);
		$bln1 = $arrBln[intval($tmp1[0])]." ".$tmp1[1];
		$writer->writeSheetHeaderExt($sheet, array("Bulan : ".$bln1=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "D".$start);
		$patok++;
		$start++;
	} else if($q1 && $q2){
		$tmp1 = explode("/",$q1);
		$bln1 = $arrBln[intval($tmp1[0])]." ".$tmp1[1];
		$tmp2 = explode("/",$q2);
		$bln2 = $arrBln[intval($tmp2[0])]." ".$tmp2[1];
		$writer->writeSheetHeaderExt($sheet, array("Bulan : ".$bln1." s/d ".$bln2=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "D".$start);
		$patok++;
		$start++;
	}
	if($q3){
		$start++;
		$q3Txt = $con->getOne("select nama_area from pro_master_area where id_master = '".$q3."'");
		$writer->writeSheetHeaderExt($sheet, array("Area : ".$q3Txt=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "D".$start);
		$patok++;
		$start++;
	} 
	if($q4){
		$start++;
		$q4Txt = $con->getOne("select fullname from acl_user where id_user = '".$q4."'");
		$writer->writeSheetHeaderExt($sheet, array("Marketing : ".$q4Txt=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "D".$start);
		$patok++;
		$start++;
	} 
	$writer->writeSheetHeaderExt($sheet, array(""=>"string"));
	$patok++;
	$start++;
	$writer->setColumnIndex($patok);

	$header = array(
		"Bulan"=>'string',
		"Area"=>'string',
		"Marketing"=>'string',
		"Volume Terkirim (Liter)"=>'string',
	);
	$writer->writeSheetHeaderExt($sheet, $header);
	$start++;

	if(count($res) > 0){
		$tot1 = 0;
		$last = $start-1;
		foreach($res as $data){
			$last++;
        	$temp = $arrBln[intval(substr($data['bulan_delivered'],4,2))]." ".substr($data['bulan_delivered'],0,4);
			$writer->writeSheetRow($sheet, array($temp, $data['nama_area'], $data['fullname'], $data['volume']));
		}
		$writer->writeSheetRow($sheet, array("", "", "TOTAL", "=SUM(D".$start.":D".$last.")"));
		$last++;
		$writer->newMergeCell($sheet, "A".$last, "B".$last);
	} else{
		$writer->writeSheetRow($sheet, array("Data tidak ada"));
		$writer->newMergeCell($sheet, "A".$start, "D".$start);
		$start++;
	}
	
	$con->close();
	$writer->writeToStdOut();
	exit(0);

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

	$q1	= htmlspecialchars($enk["q1"], ENT_QUOTES);
	$q2	= htmlspecialchars($enk["q2"], ENT_QUOTES);
	$q3	= htmlspecialchars($enk["q3"], ENT_QUOTES);
	$q4	= htmlspecialchars($enk["q4"], ENT_QUOTES);
	$q5	= htmlspecialchars($enk["q5"], ENT_QUOTES);
	$q6	= htmlspecialchars($enk["q6"], ENT_QUOTES);
	$q7	= htmlspecialchars($enk["q7"], ENT_QUOTES);
	$q8	= htmlspecialchars($enk["q8"], ENT_QUOTES);

	$sql = "select a.*, b.nama_customer, c.nama_cabang, d.nama_area, e.harga_minyak, f.fullname 
			from pro_penawaran a join pro_customer b on a.id_customer = b.id_customer join pro_master_cabang c on a.id_cabang = c.id_master 
			join pro_master_area d on a.id_area = d.id_master join acl_user f on b.id_marketing = f.id_user 
			left join pro_master_harga_pertamina e on a.masa_awal = e.periode_awal and a.masa_akhir = e.periode_akhir and a.id_area = e.id_area and a.produk_tawar = e.id_produk 
			where 1=1";
	if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 11 || paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 17)
		$sql .= " and b.id_marketing = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."'";
	else if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 7 || paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 18)
		$sql .= " and b.id_wilayah = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_wilayah"])."'";
	else if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 6)
		$sql .= " and (b.id_group = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_group"])."' or f.id_om = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."')";

	if($q1 && !$q2){
		$t1 = explode("/",$q1);
		$m1 = $t1[2]."/".$t1[1]."/01";
		$sql .= " and a.masa_awal = '".$m1."'";
	} else if($q1 && $q2){
		$t1 = explode("/",$q1);
		$m1 = $t1[2]."/".$t1[1]."/01";
		$t2 = explode("/",$q2);
		$m2 = $t2[2]."/".$t2[1]."/15";
		$sql .= " and a.masa_awal between '".$m1."' and '".$m2."'";
	}
	if($q3) $sql .= " and upper(b.nama_customer) like '%".strtoupper($q3)."%'";
	if($q4 && $q5){
		$arrOp = array(1=>"=", ">=", "<=");
		$sql .= " and a.volume_tawar ".$arrOp[$q4]." '".str_replace(array(".",","),array("",""),$q5)."'";
	}
	if($q6) $sql .= " and a.id_area = '".$q6."'";
	if($q7) $sql .= " and a.id_cabang = '".$q7."'";
	if($q8) $sql .= " and b.id_marketing = '".$q8."'";

	$sql .= " order by a.id_penawaran desc";
	$res = $con->getResult($sql);
	

	$filename 	= "Laporan-Penawaran-".date('dmYHis').".xlsx";
	$arrOp 		= array(1=>"=", ">=", "<=");
	header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
	ob_end_clean();
	header('Content-type: application/vnd.ms-excel');
	// header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');

	$writer = new XLSXWriter();
	$writer->writeSheetHeader($sheet, array('Laporan Penawaran'=>'string'));
	$writer->newMergeCell($sheet, "A1", "I1");
	$start = 2;
	$patok = 1;
	if($q1 && !$q2){
		$writer->writeSheetHeaderExt($sheet, array("Periode Penawaran : ".$q1=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "I".$start);
		$patok++;
		$start++;
	} else if($q1 && $q2){
		$writer->writeSheetHeaderExt($sheet, array("Periode Penawaran : ".$q1." s/d ".$q2=>"string"));
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
		$writer->writeSheetHeaderExt($sheet, array("Volume Penawaran ".$arrOp[$q4]." ".$q5=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "I".$start);
		$patok++;
		$start++;
	} 
	if($q6){
		$q6Txt = $con->getOne("select nama_area from pro_master_area where id_master = '".$q6."'");
		$writer->writeSheetHeaderExt($sheet, array("Area : ".$q6Txt=>"string"));
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
		"Volume Penawaran"=>'string',
		"Harga Jual (Dasar)"=>'string',
		"Harga Jual (Dasar Inc. Tax)"=>'string',
		"Ongkos Angkut"=>'string',
		"% Disc Pertamina"=>'0%',
		"Refund"=>'string',
	);
	$writer->writeSheetHeaderExt($sheet, $header);
	$start++;

	if(count($res) > 0){
		$tot1 = 0;
		$last = $start-1;
		foreach($res as $data){
			$last++;
			$masa = date("d/m/Y",strtotime($data['masa_awal']))." s/d ".date("d/m/Y",strtotime($data['masa_akhir']));
			//$tot1 = $tot1 + $data['volume_tawar'];
			//$disc = ($data['harga_minyak'] && $data['harga_asli'])?(1-($data['harga_asli']/$data['harga_minyak'])):'';
			//$dist = ($disc)?number_format($disc,2):'';
			$refu = ($data['refund_tawar'])?$data['refund_tawar']:'';
			
			$arHj = json_decode($data['detail_rincian'], true);
			if($data['harga_minyak'] && $data['harga_asli']){
				if($data['harga_asli'] > $data['harga_minyak']){ // Jika Harga Jual Dasar lebih besar dari harga pertamina
					$disc = (((abs($data['harga_minyak']-$data['harga_asli'])) / $data['harga_minyak']) + 1) ;
				}else{
					$disc = (($data['harga_minyak']-$data['harga_asli']) / $data['harga_minyak']);
					
				}
			}else{
				$disc = 0;
			}
			
			$harga_ppn = $data['harga_asli'] + $arHj[2]['biaya'];
			//Lasamba
			/*$writer->writeSheetRow($sheet, array(
				$masa, $data['nama_customer'], $data['fullname'], $data['nama_cabang'], 
				$data['nama_area'], $data['volume_tawar'], $data['harga_asli'], $dist, $refu
			));*/
			$writer->writeSheetRow($sheet, array(
				$masa, $data['nama_customer'], $data['fullname'], $data['nama_cabang'], 
				$data['nama_area'], $data['volume_tawar'], $data['harga_asli'],$harga_ppn,$data['oa_kirim'], $disc, $refu
			));
		}
		$writer->writeSheetRow($sheet, array("TOTAL", "", "", "", "", "=SUM(F".$start.":F".$last.")", "AVERAGE", "", "","=AVERAGE(J".$start.":J".$last.")", ""));
		$last++;
		$writer->newMergeCell($sheet, "A".$last, "E".$last);
		$writer->newMergeCell($sheet, "G".$last, "I".$last);
	} else{
		$writer->writeSheetRow($sheet, array("Data tidak ada"));
		$writer->newMergeCell($sheet, "A".$start, "I".$start);
		$start++;
	}
	
	$con->close();
	$writer->writeToStdOut();
	exit(0);

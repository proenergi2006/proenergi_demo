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
	$where1 = "";
	$where2 = "";

	$q1	= htmlspecialchars($enk["q1"], ENT_QUOTES);
	$q2	= htmlspecialchars($enk["q2"], ENT_QUOTES);
	$q3	= htmlspecialchars($enk["q3"], ENT_QUOTES);
	$q4	= htmlspecialchars($enk["q4"], ENT_QUOTES);
	$q5	= htmlspecialchars($enk["q5"], ENT_QUOTES);
	
	if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 9){
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
		 $where1 .= " and c.id_transportir = '".$q3."'";
		 $where2 .= " and a.transportir = '".$q3."'";
	}
	if($q4){
		 $where1 .= " and n.id_wilayah = '".$q4."'";
		 $where2 .= " and n.id_wilayah = '".$q4."'";
	}
	if($q5){
		 $where1 .= " and l.id_area = '".$q5."'";
		 $where2 .= " and l.id_area = '".$q5."'";
	}
	
	$sql = "
		select sum(jum_vol) as volume, tanggal_delivered, id_transportir, nama_suplier, nama_transportir, lokasi_suplier 
		from (
			select date(b.tanggal_delivered) as tanggal_delivered, d.volume_po as jum_vol, c.id_transportir, q.nama_suplier, q.nama_transportir, q.lokasi_suplier 
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
			select date(a.tanggal_delivered) as tanggal_delivered, a.bl_lo_jumlah as jum_vol, a.transportir as id_transportir, q.nama_suplier, q.nama_transportir, q.lokasi_suplier 
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
		) a group by tanggal_delivered, id_transportir";

	$sql .= " order by tanggal_delivered desc";
	$res = $con->getResult($sql);
	

	$filename 	= "Laporan-volume-angkut-".date('dmYHis').".xlsx";
	$arrOp 		= array(1=>"=", ">=", "<=");
	header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
	ob_end_clean();
	header('Content-type: application/vnd.ms-excel');
	// header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');

	$writer = new XLSXWriter();
	$writer->writeSheetHeader($sheet, array('Laporan Volume Angkut'=>'string'));
	$writer->newMergeCell($sheet, "A1", "C1");
	$start = 2;
	$patok = 1;
	if($q1 && !$q2){
		$writer->writeSheetHeaderExt($sheet, array("Tanggal Delivery : ".$q1=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "C".$start);
		$patok++;
		$start++;
	} else if($q1 && $q2){
		$writer->writeSheetHeaderExt($sheet, array("Tanggal Delivery : ".$q1." s/d ".$q2=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "C".$start);
		$patok++;
		$start++;
	}
	if($q3){
		$q3Txt = $con->getOne("select concat(nama_suplier,' - ',nama_transportir,', ',lokasi_suplier) as transportir from pro_master_transportir where id_master = '".$q3."'");
		$writer->writeSheetHeaderExt($sheet, array("Transportir : ".$q3Txt=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "C".$start);
		$patok++;
		$start++;
	} 
	if($q4){
		$q4Txt = $con->getOne("select nama_cabang from pro_master_cabang where id_master = '".$q4."'");
		$writer->writeSheetHeaderExt($sheet, array("Cabang Invoice : ".$q4Txt=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "C".$start);
		$patok++;
		$start++;
	}
	if($q5){
		$q5Txt = $con->getOne("select nama_area from pro_master_area where id_master = '".$q5."'");
		$writer->writeSheetHeaderExt($sheet, array("Area : ".$q5Txt=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "C".$start);
		$patok++;
		$start++;
	}

	$writer->writeSheetHeaderExt($sheet, array(""=>"string"));
	$patok++;
	$start++;
	$writer->setColumnIndex($patok);

	$header = array(
		"Tgl Delivery"=>'string',
		"Transportir"=>'string',
		"Volume Terkirim (Liter)"=>'string',
	);
	$writer->writeSheetHeaderExt($sheet, $header);
	$start++;

	if(count($res) > 0){
		$tot1 = 0;
		$last = $start-1;
		foreach($res as $data){
			$last++;
			$tempal = $data['nama_suplier'].' - '.$data['nama_transportir'].', '.$data['lokasi_suplier'];

			$writer->writeSheetRow($sheet, array(date("d/m/Y", strtotime($data['tanggal_delivered'])), $tempal, $data['volume']));
		}
		$writer->writeSheetRow($sheet, array("", "TOTAL", "=SUM(C".$start.":C".$last.")"));
		$last++;
	} else{
		$writer->writeSheetRow($sheet, array("Data tidak ada"));
		$writer->newMergeCell($sheet, "A".$start, "C".$start);
		$start++;
	}
	
	$con->close();
	$writer->writeToStdOut();
	exit(0);

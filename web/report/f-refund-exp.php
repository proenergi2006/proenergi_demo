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

	if($q1){ 
		$where1 .= " and h.tanggal_poc = '".tgl_db($q1)."'";
		$where2 .= " and h.tanggal_poc = '".tgl_db($q1)."'";
	}
	if($q2){
		 $where1 .= " and upper(n.nama_customer) like '%".strtoupper($q1)."%'";
		 $where2 .= " and upper(n.nama_customer) like '%".strtoupper($q2)."%'";
	}
	if($q3 != ""){
		 $where1 .= " and b.is_bayar = '".$q3."'";
		 $where2 .= " and a.is_bayar = '".$q3."'";
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
		select * from (
			select f.tanggal_pr, n.nama_customer, n.kode_pelanggan, i.alamat_survey, j.nama_prov, k.nama_kab, h.nomor_poc, h.tanggal_poc, d.volume_po as jum_vol, 
			b.tanggal_delivered, l.refund_tawar, b.is_bayar, b.tanggal_bayar, b.ket_bayar, m.nama_area 
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
			where b.is_delivered = 1 and l.refund_tawar != 0 ".$where1." 
			UNION ALL
			select f.tanggal_pr, n.nama_customer, n.kode_pelanggan, i.alamat_survey, j.nama_prov, k.nama_kab, h.nomor_poc, h.tanggal_poc, a.bl_lo_jumlah as jum_vol, 
			a.tanggal_delivered, l.refund_tawar, a.is_bayar, a.tanggal_bayar, a.ket_bayar, m.nama_area 
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
			where a.is_delivered = 1 and l.refund_tawar != 0 ".$where2." 
		) a";

	$sql .= " order by tanggal_delivered desc";
	$res = $con->getResult($sql);
	

	$filename 	= "Laporan-Refund-".date('dmYHis').".xlsx";
	$arrOp 		= array("Diproses", "Terbayar");
	header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
	ob_end_clean();
	header('Content-type: application/vnd.ms-excel');
	// header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');

	$writer = new XLSXWriter();
	$writer->writeSheetHeader($sheet, array('Laporan Refund'=>'string'));
	$writer->newMergeCell($sheet, "A1", "M1");
	$start = 2;
	$patok = 1;
	if($q1){
		$writer->writeSheetHeaderExt($sheet, array("Tanggal PO : ".$q1=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "M".$start);
		$patok++;
		$start++;
	}
	if($q2){
		$writer->writeSheetHeaderExt($sheet, array("Customer : ".$q2=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "M".$start);
		$patok++;
		$start++;
	} 
	if($q3){
		$writer->writeSheetHeaderExt($sheet, array("Status : ".$arrOp[$q3]=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "M".$start);
		$patok++;
		$start++;
	} 
	if($q4){
		$q4Txt = $con->getOne("select nama_cabang from pro_master_cabang where id_master = '".$q4."'");
		$writer->writeSheetHeaderExt($sheet, array("Cabang Invoice : ".$q4Txt=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "M".$start);
		$patok++;
		$start++;
	}
	if($q5){
		$q5Txt = $con->getOne("select nama_area from pro_master_area where id_master = '".$q5."'");
		$writer->writeSheetHeaderExt($sheet, array("Area : ".$q5Txt=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "M".$start);
		$patok++;
		$start++;
	}

	$writer->writeSheetHeaderExt($sheet, array(""=>"string"));
	$patok++;
	$start++;
	$writer->setColumnIndex($patok);

	$header = array(
		"Tanggal PR"=>'string',
		"Kode"=>'string',
		"Customer"=>'string',
		"Alamat Kirim"=>'string',
		"Tgl. PO"=>'string',
		"No. PO"=>'string',
		"Tgl. Terkirim"=>'string',
		"Volume (Liter)"=>'string',
		"Refund (Rp/liter)"=>'string',
		"Total (Rp)"=>'string',
		"Status"=>'string',
		"Tanggal Dibayar"=>'string',
		"Ket"=>'string',
	);
	$writer->writeSheetHeaderExt($sheet, $header);
	$start++;

	if(count($res) > 0){
		$last = $start-1;
		foreach($res as $data){
			$last++;
			$tempal = strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
			$alamat = $data['alamat_survey']." ".ucwords($tempal)." ".$data['nama_prov'];
			$totalF = $data['refund_tawar'] * $data['jum_vol'];
			$tglByr = tgl_indo($data['tanggal_bayar'], 'normal', 'db', '/');

			$writer->writeSheetRow($sheet, array(
				date("d/m/Y", strtotime($data['tanggal_pr'])), $data['kode_pelanggan'], $data['nama_customer'], $alamat, date("d/m/Y", strtotime($data['tanggal_poc'])), 
				$data['nomor_poc'], date("d/m/Y", strtotime($data['tanggal_delivered'])), $data['jum_vol'], $data['refund_tawar'], $totalF, $arrOp[$data['is_bayar']], 
				$tglByr, $data['ket_bayar'] 
			));
		}
		$writer->writeSheetRow($sheet, array("TOTAL", "", "", "", "", "", "", "=SUM(H".$start.":H".$last.")", "", "=SUM(J".$start.":J".$last.")", "", "", ""));
		$last++;
		$writer->newMergeCell($sheet, "A".$last, "G".$last);
		$writer->newMergeCell($sheet, "K".$last, "M".$last);
	} else{
		$writer->writeSheetRow($sheet, array("Data tidak ada"));
		$writer->newMergeCell($sheet, "A".$start, "M".$start);
		$start++;
	}
	
	$con->close();
	$writer->writeToStdOut();
	exit(0);

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
	$q6	= htmlspecialchars($enk["q6"], ENT_QUOTES);
	$q7	= htmlspecialchars($enk["q7"], ENT_QUOTES);
	$q8	= htmlspecialchars($enk["q8"], ENT_QUOTES);
	
	if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 13){
		$where1 .= " and a.id_terminal = '".paramDecrypt($_SESSION["sinori".SESSIONID]["terminal"])."'";
		$where2 .= " and a.terminal = '".paramDecrypt($_SESSION["sinori".SESSIONID]["terminal"])."'";
	} else if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 9 || paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 10){
		$where1 .= " and n.id_wilayah = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_wilayah"])."'";
		$where2 .= " and n.id_wilayah = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_wilayah"])."'";
	}

	if($q1 && !$q2){ 
		$where1 .= " and g.tanggal_kirim = '".tgl_db($q1)."'";
		$where2 .= " and g.tanggal_kirim = '".tgl_db($q1)."'";
	} else if($q1 && $q2){
		$where1 .= " and g.tanggal_kirim between '".tgl_db($q1)."' and '".tgl_db($q2)."'";
		$where2 .= " and g.tanggal_kirim between '".tgl_db($q1)."' and '".tgl_db($q1)."'";
	}
	if($q3){
		 $where1 .= " and upper(n.nama_customer) like '%".strtoupper($q3)."%'";
		 $where2 .= " and upper(n.nama_customer) like '%".strtoupper($q3)."%'";
	}
	if($q4){
		 $where1 .= " and upper(d.no_spj) = '".strtoupper($q4)."'";
		 $where2 .= " and 1=2";
	}
	if($q5){
		 $where1 .= " and upper(e.nomor_lo_pr) = '".strtoupper($q5)."'";
		 $where2 .= " and upper(e.nomor_lo_pr) = '".strtoupper($q5)."'";
	}
	if($q6){
		 $where1 .= " and upper(b.nomor_order) = '".strtoupper($q6)."'";
		 $where2 .= " and 1=2";
	}
	if($q7){
		 $where1 .= " and n.id_wilayah = '".$q7."'";
		 $where2 .= " and n.id_wilayah = '".$q7."'";
	}
	if($q8){
		 $where1 .= " and l.id_area = '".$q8."'";
		 $where2 .= " and l.id_area = '".$q8."'";
	}
	
	$sql = "
		select * from (
			select g.tanggal_kirim, e.nomor_lo_pr, b.nomor_order, n.nama_customer, i.alamat_survey, j.nama_prov, k.nama_kab, h.nomor_poc, d.no_spj, b.nomor_do, 
			q.nama_suplier, q.nama_transportir, q.lokasi_suplier, r.nomor_plat, s.nama_sopir, t.nama_terminal, t.tanki_terminal, t.lokasi_terminal, d.volume_po as jum_vol, 
			b.realisasi_volume, b.jumlah_segel, b.pre_segel, b.nomor_segel_awal, b.nomor_segel_akhir, m.nama_area, o.nama_cabang 
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
			select g.tanggal_kirim, e.nomor_lo_pr, '' as nomor_order, n.nama_customer, i.alamat_survey, j.nama_prov, k.nama_kab, h.nomor_poc, '' as no_spj, 
			a.nomor_dn_kapal as nomor_do, q.nama_suplier, q.nama_transportir, q.lokasi_suplier, a.vessel_name as nomor_plat, a.kapten_name as nama_sopir, 
			t.nama_terminal, t.tanki_terminal, t.lokasi_terminal, a.bl_lo_jumlah as jum_vol, a.realisasi_volume, 
			'' as jumlah_segel, '' as pre_segel, '' as nomor_segel_awal, '' as nomor_segel_akhir, m.nama_area, o.nama_cabang 
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
		) a ";

	$sql .= " order by tanggal_kirim desc";
	$res = $con->getResult($sql);
	

	$filename 	= "Laporan-Loading-Order-".date('dmYHis').".xlsx";
	$arrOp 		= array(1=>"=", ">=", "<=");
	header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
	ob_end_clean();
	header('Content-type: application/vnd.ms-excel');
	// header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');

	$writer = new XLSXWriter();
	$writer->writeSheetHeader($sheet, array('Laporan Loading Order'=>'string'));
	$writer->newMergeCell($sheet, "A1", "P1");
	$start = 2;
	$patok = 1;
	if($q1 && !$q2){
		$writer->writeSheetHeaderExt($sheet, array("Tanggal Kirim : ".$q1=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "P".$start);
		$patok++;
		$start++;
	} else if($q1 && $q2){
		$writer->writeSheetHeaderExt($sheet, array("Tanggal Kirim : ".$q1." s/d ".$q2=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "P".$start);
		$patok++;
		$start++;
	}
	if($q3){
		$writer->writeSheetHeaderExt($sheet, array("Customer : ".$q3=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "P".$start);
		$patok++;
		$start++;
	} 
	if($q4){
		$writer->writeSheetHeaderExt($sheet, array("Surat Jalan : ".$q4=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "P".$start);
		$patok++;
		$start++;
	}
	if($q5){
		$writer->writeSheetHeaderExt($sheet, array("Loading Order : ".$q5=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "P".$start);
		$patok++;
		$start++;
	}
	if($q6){
		$writer->writeSheetHeaderExt($sheet, array("No Order : ".$q6=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "P".$start);
		$patok++;
		$start++;
	}
	if($q7){
		$q7Txt = $con->getOne("select nama_cabang from pro_master_cabang where id_master = '".$q7."'");
		$writer->writeSheetHeaderExt($sheet, array("Cabang Invoice : ".$q7Txt=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "P".$start);
		$patok++;
		$start++;
	}
	if($q8){
		$q8Txt = $con->getOne("select nama_area from pro_master_area where id_master = '".$q8."'");
		$writer->writeSheetHeaderExt($sheet, array("Area : ".$q8Txt=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "P".$start);
		$patok++;
		$start++;
	}

	$writer->writeSheetHeaderExt($sheet, array(""=>"string"));
	$patok++;
	$start++;
	$writer->setColumnIndex($patok);

	$header = array(
		"Tanggal Kirim"=>'string',
		"Loading Order"=>'string',
		"No Order"=>'string',
		"Customer"=>'string',
		"Alamat Kirim"=>'string',
		"No. PO"=>'string',
		"SJ"=>'string',
		"DN"=>'string',
		"Transportir"=>'string',
		"No. Plat"=>'string',
		"Driver"=>'string',
		"Volume SJ"=>'string',
		"Volume Realisasi"=>'string',
		"Segel"=>'string',
		"Area"=>'string',
		"Depot"=>'string',
	);
	$writer->writeSheetHeaderExt($sheet, $header);
	$start++;

	if(count($res) > 0){
		$tot1 = 0;
		$last = $start-1;
		foreach($res as $data){
			$last++;
			$transp = $data['nama_suplier'].' - '.$data['nama_transportir'].', '.$data['lokasi_suplier'];
			$depot 	= $data['nama_terminal'].' '.$data['tanki_terminal'].', '.$data['lokasi_terminal'];
			$tempal = strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
			$alamat = $data['alamat_survey']." ".ucwords($tempal)." ".$data['nama_prov'];
			$seg_aw = ($data['nomor_segel_awal'])?str_pad($data['nomor_segel_awal'],4,'0',STR_PAD_LEFT):'';
			$seg_ak = ($data['nomor_segel_akhir'])?str_pad($data['nomor_segel_akhir'],4,'0',STR_PAD_LEFT):'';
			if($data['jumlah_segel'] == 1)
				$nomor_segel = $data['pre_segel']."-".$seg_aw;
			else if($data['jumlah_segel'] == 2)
				$nomor_segel = $data['pre_segel']."-".$seg_aw." & ".$data['pre_segel']."-".$seg_ak;
			else if($data['jumlah_segel'] > 2)
				$nomor_segel = $data['pre_segel']."-".$seg_aw." s/d ".$data['pre_segel']."-".$seg_ak;
			else $nomor_segel = '';

			$writer->writeSheetRow($sheet, array(
				date("d/m/Y", strtotime($data['tanggal_kirim'])), $data['nomor_lo_pr'], $data['nomor_order'], $data['nama_customer'], $alamat, $data['nomor_poc'], $data['no_spj'], 
				$data['nomor_do'], $transp, $data['nomor_plat'], $data['nama_sopir'], $data['jum_vol'], $data['realisasi_volume'], $nomor_segel, $data['nama_area'], $depot, 
			));
		}
		$writer->writeSheetRow($sheet, array("", "", "", "", "", "", "", "", "", "", "TOTAL", "=SUM(L".$start.":L".$last.")", "=SUM(M".$start.":M".$last.")", "", "", ""));
		$last++;
		$writer->newMergeCell($sheet, "A".$last, "J".$last);
	} else{
		$writer->writeSheetRow($sheet, array("Data tidak ada"));
		$writer->newMergeCell($sheet, "A".$start, "P".$start);
		$start++;
	}
	
	$con->close();
	$writer->writeToStdOut();
	exit(0);

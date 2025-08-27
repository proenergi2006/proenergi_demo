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

	$sql .= " order by tanggal_delivered desc";
	$res = $con->getResult($sql);
	

	$filename 	= "Laporan-Margin-".date('dmYHis').".xlsx";
	$arrOp 		= array(1=>"=", ">=", "<=");
	header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
	ob_end_clean();
	header('Content-type: application/vnd.ms-excel');
	// header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');

	$writer = new XLSXWriter();
	$writer->writeSheetHeader($sheet, array('Laporan Margin'=>'string'));
	$writer->newMergeCell($sheet, "A1", "K1");
	$start = 2;
	$patok = 1;
	if($q1 && !$q2){
		$writer->writeSheetHeaderExt($sheet, array("Tanggal Delivery : ".$q1=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "K".$start);
		$patok++;
		$start++;
	} else if($q1 && $q2){
		$writer->writeSheetHeaderExt($sheet, array("Tanggal Delivery : ".$q1." s/d ".$q2=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "K".$start);
		$patok++;
		$start++;
	}
	if($q3){
		$writer->writeSheetHeaderExt($sheet, array("Customer : ".$q3=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "K".$start);
		$patok++;
		$start++;
	} 
	if($q4){
		$writer->writeSheetHeaderExt($sheet, array("Loading Order : ".$q4=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "K".$start);
		$patok++;
		$start++;
	}
	if($q5){
		$q5Txt = $con->getOne("select concat(jenis_produk,' - ',merk_dagang) as produk from pro_master_produk where id_master = '".$q5."'");
		$writer->writeSheetHeaderExt($sheet, array("Produk : ".$q5Txt=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "K".$start);
		$patok++;
		$start++;
	}
	if($q6){
		$q6Txt = $con->getOne("select nama_vendor from pro_master_vendor where id_master = '".$q6."'");
		$writer->writeSheetHeaderExt($sheet, array("Suplier : ".$q6Txt=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "K".$start);
		$patok++;
		$start++;
	}

	$writer->writeSheetHeaderExt($sheet, array(""=>"string"));
	$patok++;
	$start++;
	$writer->setColumnIndex($patok);

	$header = array(
		"Tanggal Delivery"=>'string',
		"Kode"=>'string',
		"Customer"=>'string',
		"Produk"=>'string',
		"Volume Realisasi"=>'string',
		"PBBKB"=>'string',
		"Suplier"=>'string',
		"Nett Profit"=>'string',
		"Depot"=>'string',
		"Loading Order"=>'string',
		"Area"=>'string',
	);
	$writer->writeSheetHeaderExt($sheet, $header);
	$start++;

	if(count($res) > 0){
		$tot1 = 0;
		$tot2 = 0;
		$last = $start-1;
		foreach($res as $data){
			$last++;
			$produk = $data['jenis_produk'].' - '.$data['merk_dagang'];
			$depot 	= $data['nama_terminal'].' '.$data['tanki_terminal'].', '.$data['lokasi_terminal'];
			$pbbkbT = ($data['nilai_pbbkb']/100) + 1.1;
			$oildus = $data['harga_poc'] / $pbbkbT * 0.003;
			$pbbkbN = $data['harga_poc'] / $pbbkbT * ($data['nilai_pbbkb']/100);
			$nethrg = $data['harga_poc'] - $data['refund_tawar'] - $oildus - $data['transport'] - $pbbkbN;
			$netprt = ($nethrg - $data['pr_harga_beli']) * $data['realisasi_volume'];
			$tot1	= $tot1 + $data['realisasi_volume']; 
			$tot2	= $tot2 + $netprt; 

			$writer->writeSheetRow($sheet, array(
				date("d/m/Y", strtotime($data['tanggal_delivered'])), $data['kode_pelanggan'], $data['nama_customer'], $produk, $data['realisasi_volume'], 
				$data['nilai_pbbkb']."%", $data['nama_vendor'], round($netprt), $depot, $data['nomor_lo_pr'], $data['nama_area'] 
			));
		}
		$writer->writeSheetRow($sheet, array("TOTAL", "", "", "", $tot1, "", "", round($tot2), "", "", ""));
		$last++;
		$writer->newMergeCell($sheet, "A".$last, "D".$last);
		$writer->newMergeCell($sheet, "F".$last, "G".$last);
		$writer->newMergeCell($sheet, "I".$last, "K".$last);
	} else{
		$writer->writeSheetRow($sheet, array("Data tidak ada"));
		$writer->newMergeCell($sheet, "A".$start, "K".$start);
		$start++;
	}
	
	$con->close();
	$writer->writeToStdOut();
	exit(0);

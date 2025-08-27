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
	
	if($q1 && !$q2){ 
		$where .= " and f.tanggal_inven = '".tgl_db($q1)."'";
	} else if($q1 && $q2){
		$where .= " and f.tanggal_inven between '".tgl_db($q1)."' and '".tgl_db($q2)."'";
	}
	if($q3) $where .= " and upper(f.nomor_po) like '%".strtoupper($q3)."%'";
	if($q4) $where .= " and a.id_vendor = '".$q4."'";
	if($q5) $where .= " and a.id_produk = '".$q5."'";
	
	$sql = "
		select f.tanggal_inven, f.nomor_po, b.nama_vendor, c.jenis_produk, c.merk_dagang, f.in_inven, a.harga_tebus, g.harga_minyak as harga_pertamina, 
		d.nama_area, e.nama_terminal, e.tanki_terminal, e.lokasi_terminal
		from pro_master_harga_tebus a 
		join pro_master_vendor b on a.id_vendor = b.id_master 
		join pro_master_produk c on a.id_produk = c.id_master 
		join pro_master_area d on a.id_area = d.id_master 
		join pro_master_terminal e on a.id_terminal = e.id_master 
		join pro_inventory_vendor f on a.id_inven = f.id_master 
		left join pro_master_harga_pertamina g on a.periode_awal = g.periode_awal and a.periode_akhir = g.periode_akhir and a.id_area = g.id_area and a.id_produk = g.id_produk 
		where 1=1 ".$where;

	$sql .= " order by f.tanggal_inven desc";
	$res = $con->getResult($sql);
	

	$filename 	= "Laporan-Pembelian-".date('dmYHis').".xlsx";
	header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
	ob_end_clean();
	header('Content-type: application/vnd.ms-excel');
	// header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');

	$writer = new XLSXWriter();
	$writer->writeSheetHeader($sheet, array('Laporan Pembelian'=>'string'));
	$writer->newMergeCell($sheet, "A1", "I1");
	$start = 2;
	$patok = 1;
	if($q1 && !$q2){
		$writer->writeSheetHeaderExt($sheet, array("Tanggal Issued : ".$q1=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "I".$start);
		$patok++;
		$start++;
	} else if($q1 && $q2){
		$writer->writeSheetHeaderExt($sheet, array("Tanggal Issued : ".$q1." s/d ".$q2=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "I".$start);
		$patok++;
		$start++;
	}
	if($q3){
		$writer->writeSheetHeaderExt($sheet, array("No PO : ".$q3=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "I".$start);
		$patok++;
		$start++;
	} 
	if($q4){
		$q4Txt = $con->getOne("select nama_vendor from pro_master_vendor where id_master = '".$q4."'");
		$writer->writeSheetHeaderExt($sheet, array("Suplier : ".$q4Txt=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "I".$start);
		$patok++;
		$start++;
	}
	if($q5){
		$q5Txt = $con->getOne("select concat(jenis_produk,' - ',merk_dagang) as produk from pro_master_produk where id_master = '".$q5."'");
		$writer->writeSheetHeaderExt($sheet, array("Produk : ".$q5Txt=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "I".$start);
		$patok++;
		$start++;
	}

	$writer->writeSheetHeaderExt($sheet, array(""=>"string"));
	$patok++;
	$start++;
	$writer->setColumnIndex($patok);

	$header = array(
		"Tanggal Issued"=>'string',
		"Nomor PO"=>'string',
		"Suplier"=>'string',
		"Produk"=>'string',
		"Volume (Liter)"=>'string',
		"Harga Tebus"=>'string',
		"Harga Pertamina"=>'string',
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
			$produk = $data['jenis_produk']." - ".$data['merk_dagang'];
			$depot 	= $data['nama_terminal']." ".$data['tanki_terminal'].", ".$data['lokasi_terminal'];

			$writer->writeSheetRow($sheet, array(
				date("d/m/Y", strtotime($data['tanggal_inven'])), $data['nomor_po'], $data['nama_vendor'], $produk, $data['in_inven'], $data['harga_tebus'], 
				$data['harga_pertamina'], $data['nama_area'], $depot
			));
		}
		$writer->writeSheetRow($sheet, array("", "", "", "TOTAL",  "=SUM(E".$start.":E".$last.")", "", "", "", ""));
		$last++;
		$writer->newMergeCell($sheet, "A".$last, "C".$last);
		$writer->newMergeCell($sheet, "F".$last, "I".$last);
	} else{
		$writer->writeSheetRow($sheet, array("Data tidak ada"));
		$writer->newMergeCell($sheet, "A".$start, "I".$start);
		$start++;
	}
	
	$con->close();
	$writer->writeToStdOut();
	exit(0);

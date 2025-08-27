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
	$arrTgl = array(1=>"m.tanggal_po", "b.tgl_kirim_po", "a.tanggal_loading");
	$arrTxt = array(1=>"Tanggal PO", "Tanggal Kirim", "Tanggal Loading");

	$q1	= htmlspecialchars($enk["q1"], ENT_QUOTES);
	$q2	= htmlspecialchars($enk["q2"], ENT_QUOTES);
	$q3	= htmlspecialchars($enk["q3"], ENT_QUOTES);
	$q4	= htmlspecialchars($enk["q4"], ENT_QUOTES);
	$q5	= htmlspecialchars($enk["q5"], ENT_QUOTES);

	$sql = "select a.*, c.pr_pelanggan, i.nama_customer, e.alamat_survey, f.nama_prov, g.nama_kab, j.fullname, n.nama_transportir, n.nama_suplier, b.no_spj, k.nomor_plat, 
			l.nama_sopir, b.volume_po, h.produk_poc, p.id_area, c.pr_vendor, r.nama_terminal, r.tanki_terminal, r.lokasi_terminal, s.wilayah_angkut, m.nomor_po, m.tanggal_po, 
			c.produk, b.tgl_kirim_po, b.mobil_po 
			from pro_po_ds_detail a 
			join pro_po_ds o on a.id_ds = o.id_ds 
			join pro_po_detail b on a.id_pod = b.id_pod 
			join pro_po m on a.id_po = m.id_po 
			join pro_pr_detail c on a.id_prd = c.id_prd 
			join pro_po_customer_plan d on a.id_plan = d.id_plan 
			join pro_po_customer h on d.id_poc = h.id_poc 
			join pro_customer_lcr e on d.id_lcr = e.id_lcr
			join pro_customer i on h.id_customer = i.id_customer 
			join acl_user j on i.id_marketing = j.id_user 
			join pro_master_provinsi f on e.prov_survey = f.id_prov 
			join pro_master_kabupaten g on e.kab_survey = g.id_kab
			join pro_penawaran p on h.id_penawaran = p.id_penawaran  
			join pro_master_area q on p.id_area = q.id_master 
			join pro_master_transportir_mobil k on b.mobil_po = k.id_master 
			join pro_master_transportir_sopir l on b.sopir_po = l.id_master
			join pro_master_transportir n on m.id_transportir = n.id_master 
			join pro_master_terminal r on o.id_terminal = r.id_master 
			join pro_master_wilayah_angkut s on e.id_wil_oa = s.id_master and e.prov_survey = s.id_prov and e.kab_survey = s.id_kab
			where a.is_loaded = 1 and o.id_wilayah = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_wilayah"])."'";

	if($q1 != "")
		$sql .= " and (upper(a.nomor_do) like '".strtoupper($q1)."%' or upper(b.no_spj) = '".strtoupper($q1)."' or upper(k.nomor_plat) = '".strtoupper($q1)."' 
					or upper(l.nama_sopir) like '%".strtoupper($q1)."%' or upper(i.nama_customer) like '%".strtoupper($q1)."%')";
	if($q2 != "" && $q3 != "" && $q4 == "")
		$sql .= " and ".$arrTgl[$q2]." = '".tgl_db($q3)."'";
	else if($q2 != "" && $q3 != "" && $q4 != "")
		$sql .= " and ".$arrTgl[$q2]." between '".tgl_db($q3)."' and '".tgl_db($q4)."'";

	if($q5 != "" && $q5 == "1")
		$sql .= " and a.is_loaded = 0 and a.is_delivered = 0 and a.is_cancel = 0";
	else if($q5 != "" && $q5 == "2")
		$sql .= " and a.is_loaded = 1 and a.is_delivered = 0 and a.is_cancel = 0";
	else if($q5 != "" && $q5 == "3")
		$sql .= " and a.is_loaded = 1 and a.is_delivered = 1";
	else if($q5 != "" && $q5 == "4")
		$sql .= " and a.is_loaded = 1 and a.is_cancel = 1";
	
	$sql .= "  order by a.tanggal_loading desc, a.jam_loading, a.nomor_urut_ds, a.id_dsd";
	$res = $con->getResult($sql);
	

	$filename 	= "Laporan-Rekap-Delivery-Truck".date('dmYHis').".xlsx";
	$arrOp 		= array(1=>"=", ">=", "<=");
	header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
	ob_end_clean();
	header('Content-type: application/vnd.ms-excel');
	// header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');

	$writer = new XLSXWriter();
	$writer->writeSheetHeader($sheet, array('Laporan Rekap Delivery Truck'=>'string'));
	$writer->newMergeCell($sheet, "A1", "I1");
	$start = 2;
	$patok = 1;

	if($q1){
		$writer->writeSheetHeaderExt($sheet, array("Keywords : ".$q1=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "I".$start);
		$patok++;
		$start++;
	} 
	if($q2 != "" && $q3 != "" && $q4 == ""){
		$writer->writeSheetHeaderExt($sheet, array("Periode ".$arrTxt[$q2]." : ".$q3=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "I".$start);
		$patok++;
		$start++;
	} else if($q2 != "" && $q3 != "" && $q4 != ""){
		$writer->writeSheetHeaderExt($sheet, array("Periode ".$arrTxt[$q2]." : ".$q3." s/d ".$q4=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "I".$start);
		$patok++;
		$start++;
	}
	if($q5 != "" && $q5 == "1"){
		$writer->writeSheetHeaderExt($sheet, array("Status : Registered"=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "I".$start);
		$patok++;
		$start++;
	} else if($q5 != "" && $q5 == "2"){
		$writer->writeSheetHeaderExt($sheet, array("Status : Loaded"=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "I".$start);
		$patok++;
		$start++;
	} else if($q5 != "" && $q5 == "3"){
		$writer->writeSheetHeaderExt($sheet, array("Status : Delivered"=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "I".$start);
		$patok++;
		$start++;
	} else if($q5 != "" && $q5 == "4"){
		$writer->writeSheetHeaderExt($sheet, array("Status : Cancel"=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "I".$start);
		$patok++;
		$start++;
	}
	
	$writer->writeSheetHeaderExt($sheet, array(""=>"string"));
	$patok++;
	$start++;
	$writer->setColumnIndex($patok);

	$header = array(
		"Customer"=>'string',
		"Nomor DN"=>'string',
		"PO Transportir"=>'string',
		"Tanggal Kirim"=>'string',
		"Volume (Liter)"=>'string',
		"Alamat Kirim"=>'string',
		"Wilayah OA"=>'string',
		"Transportir"=>'string',
		"Depot"=>'string',
	);
	$writer->writeSheetHeaderExt($sheet, $header);
	$start++;

	if(count($res) > 0){
		$tot1 = 0;
		$last = $start-1;
		foreach($res as $data){
			$last++;
			$tempal 	= strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
			$alamat		= $data['alamat_survey']." ".ucwords($tempal)." ".$data['nama_prov'];
			$terminal1 	= $data['nama_terminal'];
			$terminal2 	= ($data['tanki_terminal'])?' - '.$data['tanki_terminal']:'';
			$terminal3 	= ($data['lokasi_terminal'])?', '.$data['lokasi_terminal']:'';
			$terminal 	= $terminal1.$terminal2.$terminal3;

			$writer->writeSheetRow($sheet, array(
				$data['nama_customer'], $data['nomor_do'], $data['nomor_po'], date("d/m/Y", strtotime($data['tgl_kirim_po'])), $data['volume_po'], $alamat, 
				$data['wilayah_angkut'], $data['nama_suplier'], $terminal
			));
		}
		$writer->writeSheetRow($sheet, array("", "", "", "TOTAL", "=SUM(E".$start.":E".$last.")", "", "", "", ""));
		$last++;
		$writer->newMergeCell($sheet, "A".$last, "C".$last);
	} else{
		$writer->writeSheetRow($sheet, array("Data tidak ada"));
		$writer->newMergeCell($sheet, "A".$start, "I".$start);
		$start++;
	}
	
	$con->close();
	$writer->writeToStdOut();
	exit(0);

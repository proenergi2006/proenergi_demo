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

	$q1	= isset($_POST["q1"])?htmlspecialchars($_POST["q1"], ENT_QUOTES):'';
	$q2	= isset($_POST["q2"])?htmlspecialchars($_POST["q2"], ENT_QUOTES):'';
	$q3	= isset($_POST["q3"])?htmlspecialchars($_POST["q3"], ENT_QUOTES):'';
	$q4	= isset($_POST["q4"])?htmlspecialchars($_POST["q4"], ENT_QUOTES):'';

	$sql = "select a.*, c.tanggal_kirim, d.produk_poc, e.nama_customer, f.nama_suplier, b.pr_terminal, g.id_area, h.alamat_survey, i.nama_prov, j.nama_kab, k.wilayah_angkut, 
			m.nama_terminal, m.tanki_terminal, m.lokasi_terminal, b.produk, b.pr_vendor 
			from pro_po_ds_kapal a 
			join pro_pr_detail b on a.id_prd = b.id_prd 
			join pro_po_customer_plan c on b.id_plan = c.id_plan 
			join pro_po_customer d on c.id_poc = d.id_poc 
			join pro_customer e on d.id_customer = e.id_customer 
			join pro_master_transportir f on a.transportir = f.id_master 
			join pro_penawaran g on d.id_penawaran = g.id_penawaran 
			join pro_customer_lcr h on c.id_lcr = h.id_lcr
			join pro_master_provinsi i on h.prov_survey = i.id_prov 
			join pro_master_kabupaten j on h.kab_survey = j.id_kab 
			join pro_master_wilayah_angkut k on h.id_wil_oa = k.id_master and h.prov_survey = k.id_prov and h.kab_survey = k.id_kab 
			join pro_master_area l on g.id_area = l.id_master 
			join pro_master_terminal m on a.terminal = m.id_master 
			where a.id_wilayah = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_wilayah"])."' and a.is_loaded = 1";

	if($q1 != "")
		$sql .= " and (upper(a.nomor_dn_kapal) like '".strtoupper($q1)."%' or upper(a.notify_nama) like '%".strtoupper($q1)."%' 
				or upper(a.vessel_name) like '%".strtoupper($q1)."%' or upper(a.kapten_name) like '%".strtoupper($q1)."%' or upper(e.nama_customer) like '%".strtoupper($q1)."%')";
	if($q2 != "" && $q3 == "")
		$sql .= " and c.tanggal_kirim = '".tgl_db($q2)."'";
	else if($q2 != "" && $q3 != "")
		$sql .= " and c.tanggal_kirim between '".tgl_db($q2)."' and '".tgl_db($q3)."'";

	if($q4 != "" && $q4 == "1")
		$sql .= " and a.is_loaded = 0 and a.is_delivered = 0 and a.is_cancel = 0";
	else if($q4 != "" && $q4 == "2")
		$sql .= " and a.is_loaded = 1 and a.is_delivered = 0 and a.is_cancel = 0";
	else if($q4 != "" && $q4 == "3")
		$sql .= " and a.is_loaded = 1 and a.is_delivered = 1";
	else if($q4 != "" && $q4 == "4")
		$sql .= " and a.is_loaded = 1 and a.is_cancel = 1";
	
	$sql .= " order by c.tanggal_kirim desc, a.id_dsk";
	$res = $con->getResult($sql);
	

	$filename 	= "Laporan-Rekap-Delivery-Kapal".date('dmYHis').".xlsx";
	$arrOp 		= array(1=>"=", ">=", "<=");
	header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
	ob_end_clean();
	header('Content-type: application/vnd.ms-excel');
	// header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');

	$writer = new XLSXWriter();
	$writer->writeSheetHeader($sheet, array('Laporan Rekap Delivery Kapal'=>'string'));
	$writer->newMergeCell($sheet, "A1", "H1");
	$start = 2;
	$patok = 1;

	if($q1){
		$writer->writeSheetHeaderExt($sheet, array("Keywords : ".$q1=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "H".$start);
		$patok++;
		$start++;
	} 
	if($q2 != "" && $q3 == ""){
		$writer->writeSheetHeaderExt($sheet, array("Periode Tgl Kirim : ".$q2=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "H".$start);
		$patok++;
		$start++;
	} else if($q2 != "" && $q3 != ""){
		$writer->writeSheetHeaderExt($sheet, array("Periode Tgl Kirim : ".$q2." s/d ".$q3=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "H".$start);
		$patok++;
		$start++;
	}
	if($q4 != "" && $q4 == "1"){
		$writer->writeSheetHeaderExt($sheet, array("Status : Registered"=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "H".$start);
		$patok++;
		$start++;
	} else if($q4 != "" && $q4 == "2"){
		$writer->writeSheetHeaderExt($sheet, array("Status : Loaded"=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "H".$start);
		$patok++;
		$start++;
	} else if($q4 != "" && $q4 == "3"){
		$writer->writeSheetHeaderExt($sheet, array("Status : Delivered"=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "H".$start);
		$patok++;
		$start++;
	} else if($q4 != "" && $q4 == "4"){
		$writer->writeSheetHeaderExt($sheet, array("Status : Cancel"=>"string"));
		$writer->newMergeCell($sheet, "A".$start, "H".$start);
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
				$data['nama_customer'], $data['nomor_dn_kapal'], date("d/m/Y", strtotime($data['tanggal_kirim'])), $data['bl_lo_jumlah'], $alamat, 
				$data['wilayah_angkut'], $data['nama_suplier'], $terminal
			));
		}
		$writer->writeSheetRow($sheet, array("", "", "TOTAL", "=SUM(D".$start.":D".$last.")", "", "", "", ""));
		$last++;
		$writer->newMergeCell($sheet, "A".$last, "B".$last);
	} else{
		$writer->writeSheetRow($sheet, array("Data tidak ada"));
		$writer->newMergeCell($sheet, "A".$start, "H".$start);
		$start++;
	}
	
	$con->close();
	$writer->writeToStdOut();
	exit(0);

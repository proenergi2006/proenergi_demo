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
	
	
	
	$cek = "select * from pro_master_volume_angkut where is_active = 1";
	$row = $con->getResult($cek);
	$tmp = array();
	if(count($row) > 0){
		foreach($row as $res){
			array_push($tmp, array($res['id_master'], $res['volume_angkut']));
		}
	}
	$sql = "select a.id_wil_angkut, a.id_transportir, a.nama_transportir, a.lokasi_suplier, a.wilayah_angkut, a.nama_kab, a.nama_prov";
	foreach($tmp as $que){
		$sql .= ", coalesce(sum(a.".$que[1]."), 0) as '".$que[1]."'";
	}
	$sql .= " from (select a.id_prov_angkut,a.id_kab_angkut,a.id_wil_angkut, a.id_transportir, b.nama_transportir, b.lokasi_suplier, c.wilayah_angkut, e.nama_kab, d.nama_prov";
	foreach($tmp as $que){
		$sql .= ", case when a.id_vol_angkut = ".$que[0]." then a.ongkos_angkut end as '".$que[1]."'";
	}
	$sql .= " from pro_master_ongkos_angkut a join pro_master_transportir b on a.id_transportir = b.id_master join pro_master_wilayah_angkut c on a.id_wil_angkut = c.id_master 
			join pro_master_provinsi d on c.id_prov = d.id_prov join pro_master_kabupaten e on c.id_kab = e.id_kab) a where 1=1";
	if($q1 != "")
		$sql .= " and a.wilayah_angkut like '%".strtoupper($q1)."%'";
	if($q2 != "")
		$sql .= " and a.id_transportir =  '".$q2."'";
	if($q3 != "")
		$sql .= " and a.id_prov_angkut =  '".$q3."'";
	if($q4 != "")
		$sql .= " and a.id_kab_angkut =  '".$q4."'";
	
	$sql .= " group by a.id_wil_angkut, a.id_transportir";
	$sql .= " order by a.id_transportir";

	$res = $con->getResult($sql);
	$arrAbc = array(1=>"A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U");
	$header = array(
		"Transportir"=>'string',
		"Tujuan"=>'string',
	);
	foreach($tmp as $que){
		$header[($que[1]/1000)." KL"] = "string";
	}
	$kolXls = $arrAbc[count($header)];


	$filename 	= "Laporan-Ongkos-Angkut-".date('dmYHis').".xlsx";
	$arrOp 		= array(1=>"=", ">=", "<=");
	header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
	ob_end_clean();
	header('Content-type: application/vnd.ms-excel');
	// header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');

	$writer = new XLSXWriter();
	$writer->writeSheetHeader($sheet, array('Laporan Ongkos Angkut'=>'string'));
	$writer->newMergeCell($sheet, "A1", $kolXls."1");
	$start = 2;
	$patok = 1;
	if($q1){
		$writer->writeSheetHeaderExt($sheet, array("Filter Berdasar  : ".$q1=>"string"));
		$writer->newMergeCell($sheet, "A".$start, $kolXls.$start);
		$patok++;
		$start++;
	} else if($q2){
		$sql7x = "select concat(nama_suplier,' - ',nama_transportir,', ',lokasi_suplier) from pro_master_transportir where id_master = '".$q2."'";
		$q7Txt = $con->getOne($sql7x);
		$writer->writeSheetHeaderExt($sheet, array("Transportir  : ".$q7Txt=>"string"));
		$writer->newMergeCell($sheet, "A".$start, $kolXls.$start);
		$patok++;
		$start++;
	}
	if($q3){
		$q7Txt = $con->getOne("select nama_prov from pro_master_provinsi where id_prov= '".$q3."'");
		$writer->writeSheetHeaderExt($sheet, array("Propinsi : ".$q7Txt=>"string"));
		$writer->newMergeCell($sheet, "A".$start, $kolXls.$start);
		$patok++;
		$start++;
	} 
	if($q4){
		$q7Txt = $con->getOne("select nama_kab from pro_master_kabupaten where id_kab= '".$q4."'");
		$writer->writeSheetHeaderExt($sheet, array("Kabupaten : ".$q7Txt=>"string"));
		$writer->newMergeCell($sheet, "A".$start, $kolXls.$start);
		$patok++;
		$start++;
	} 
	
	
	$writer->writeSheetHeaderExt($sheet, array(""=>"string"));
	$patok++;
	$start++;
	$writer->setColumnIndex($patok);

	$writer->writeSheetHeaderExt($sheet, $header);
	$start++;

	if(count($res) > 0){
		$tot1 = 0;
		$last = $start-1;
		foreach($res as $data){
			$transportir = $data['nama_transportir']."-".$data['lokasi_suplier'];
			$tujuan 	= $data['wilayah_angkut']."-".str_replace(array("KOTA","KABUPATEN"), array("",""), $data['nama_kab'])." ".$data['nama_prov'];
			$last++;
			$isinya = array($transportir, $tujuan);
			foreach($tmp as $que){
				$isinya[] = $data[$que[1]];
			}
			$writer->writeSheetRow($sheet, $isinya);
		}
		$last++;
	} else{
		$writer->writeSheetRow($sheet, array("Data tidak ada"));
		$writer->newMergeCell($sheet, "A".$start, $kolXls.$start);
		$start++;
	}
	
	$con->close();
	$writer->writeToStdOut();
	exit(0);

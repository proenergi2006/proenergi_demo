<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	require_once ($public_base_directory."/libraries/helper/excelgen/PHPExcel/IOFactory.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$arrBln = array(1=>"Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
	$arrBls = array(1=>"Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des");

	$q1	= htmlspecialchars($enk["q1"], ENT_QUOTES);
	$q2	= htmlspecialchars($enk["q2"], ENT_QUOTES);
	$q3	= htmlspecialchars($enk["q3"], ENT_QUOTES);
	$q4	= htmlspecialchars($enk["q4"], ENT_QUOTES);
	$q5	= htmlspecialchars($enk["q5"], ENT_QUOTES);
	$q6	= htmlspecialchars($enk["q6"], ENT_QUOTES);
	$q7	= htmlspecialchars($enk["q7"], ENT_QUOTES);
	$q8	= htmlspecialchars($enk["q8"], ENT_QUOTES);
	$q9	= htmlspecialchars($enk["q9"], ENT_QUOTES);
	$q9	= htmlspecialchars($enk["q9"], ENT_QUOTES);
	$q10= htmlspecialchars($enk["q10"], ENT_QUOTES);

	if($q1 == "1"){
		$tgl = ($q3 == 12)?($q4+1)."-01-01":$q4."-".($q3+1)."-01";
		$sql = "select a.*, b.out_pagi, b.out_malam, b.out_cancel from pro_master_inventory a 
				left join pro_master_inventory_out b on a.id_terminal = b.id_terminal and a.tanggal_inv = b.tanggal_inv and a.id_produk = b.id_produk  
				where a.id_produk = ".$q10." and a.id_terminal = '".$q9."' and month(a.tanggal_inv) = '".$q3."' and year(a.tanggal_inv) = '".$q4."'
				UNION
				select id_master, id_terminal, id_produk, tanggal_inv, awal_jam, awal_level1, awal_level2, awal_volume_tabel, awal_shrink, awal_nett, awal_temp, awal_density1, 		
				awal_density2, awal_vcf, book_stok, 0 as masuk_ship, 0 as masuk_truck, 0 as masuk_slop, 0 as keluar_slop, 0 as tank_pipe, 0 as gain_loss, created_time, created_ip, 
				created_by, lastupdate_time, lastupdate_ip, lastupdate_by , 0 as out_pagi, 0 as out_malam, 0 as out_cancel from pro_master_inventory
				where id_produk = ".$q10." and id_terminal = '".$q9."' and tanggal_inv = '".$tgl."' order by tanggal_inv";
		$res = $con->getResult($sql);
		$tot_record = count($res);
		$position 	= 0;
	} else if($q1 == "2"){
		$tgl1 = $q6."-".$q5."-01";
		$tgl2 = $q8."-".$q7."-31";
		$tgl3 = ($q7 == 12)?($q8+1)."-01-01":$q8."-".($q7+1)."-01";

		$sql1 = "
			select extract(year_month from a.tanggal_inv) as bulan_tahun, sum(a.masuk_ship) as in_ship, sum(a.masuk_truck) as in_truck, sum(a.masuk_slop) as in_slop, 
			sum(b.out_pagi) + sum(b.out_malam) as customer, sum(a.keluar_slop) as out_slop, 
			sum(a.masuk_ship * a.awal_vcf) as in_ship_gsv, sum(a.masuk_truck * a.awal_vcf) as in_truck_gsv, sum(a.masuk_slop * a.awal_vcf) as in_slop_gsv, 
			sum(b.out_pagi * a.awal_vcf) + sum(b.out_malam * a.awal_vcf) as customer_gsv, sum(a.keluar_slop * a.awal_vcf) as out_slop_gsv 
			from pro_master_inventory a left join pro_master_inventory_out b on a.tanggal_inv = b.tanggal_inv and a.id_terminal = b.id_terminal and a.id_produk = b.id_produk 
			where a.id_produk = ".$q10." and a.id_terminal = '".$q9."' and a.tanggal_inv between  '".$tgl1."' and '".$tgl2."' 
			group by extract(year_month from tanggal_inv) order by 1";
		$res1 = $con->getResult($sql1);
		$tot_record = count($res1);
		$position 	= 0;		

		$sql2 = "
			select * from 
			(
				select 1 as idnya, book_stok as end_book_stok_temp, awal_nett as end_actual_temp, book_stok * awal_vcf as end_book_stok_temp_gov, 
				awal_nett * awal_vcf as end_actual_temp_gov from pro_master_inventory where id_produk = ".$q10." and id_terminal = '".$q9."' 
				and tanggal_inv in(select max(tanggal_inv) from pro_master_inventory 
				where id_produk = ".$q10." and id_terminal = '".$q9."' and tanggal_inv between  '".$tgl1."' and '".$tgl2."')
			) a left join 
			(
				select 1 as idnya, tank_pipe as end_pipe, tank_pipe * awal_vcf as end_pipe_gov from pro_master_inventory where id_produk = ".$q10." and id_terminal = '".$q9."' 
				and tanggal_inv in (select max(tanggal_inv) from pro_master_inventory where id_produk = ".$q10." and id_terminal = '".$q9."' 
				and tanggal_inv between  '".$tgl1."' and '".$tgl2."')
			) b on a.idnya = b.idnya left join 
			(
				select 1 as idnya, book_stok as end_book_stok, awal_nett as end_actual, book_stok * awal_vcf as end_book_stok_gov, awal_nett * awal_vcf as end_actual_gov 
				from pro_master_inventory where id_produk = ".$q10." and id_terminal = '".$q9."' and tanggal_inv = '".$tgl3."'
			) c on a.idnya = c.idnya";
		$res2 = $con->getRecord($sql2);
	}
	

	$amg = array(1=>"Y","M");
	$obj = new PHPExcel();
	$obj->setActiveSheetIndex(0);
	$sheet = $obj->getActiveSheet();
	$sheet->getSheetView()->setZoomScale(80);
	$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LEGAL);
	$sheet->getDefaultStyle()->getFont()->setName('Arial');
	$sheet->getDefaultStyle()->getFont()->setSize('10');
	//$sheet->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

	$styleHeader = array(
						'font' => array(
							'bold' => true,
							'size' => 10
						),
						'alignment' => array(
							'horizontal' 	=> PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							'vertical' 		=> PHPExcel_Style_Alignment::VERTICAL_CENTER,
							'wrap'			=> true
						)
					);
	$styleHeadn1 = array(
						'alignment' => array(
							'horizontal' 	=> PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							'vertical' 		=> PHPExcel_Style_Alignment::VERTICAL_CENTER,
							'wrap'			=> true
						)
					);
	$styleBorder = array('borders'=> array('allborders'=> array('style'=> PHPExcel_Style_Border::BORDER_THIN)));
	$styleTabel  = array(
						'font' => array(
							'size' => 8
						),
						'alignment' => array(
							'vertical' 		=> PHPExcel_Style_Alignment::VERTICAL_TOP,
							'wrap'			=> true
						)
					);
	
	$sheet->setCellValue('A1', 'LAPORAN INVENTORY');
	$sheet->mergeCells('A1:'.$amg[$q1].'1');
	$q10Txt = $con->getOne("select concat(jenis_produk,' - ',merk_dagang) as produk from pro_master_produk where id_master = '".$q10."'");
	$sheet->setCellValue('A2', $q10Txt);
	$sheet->mergeCells('A2:'.$amg[$q1].'2');
	$q9Txt = $con->getOne("select concat(nama_terminal,' ',lokasi_terminal,', ',lokasi_terminal) as terminal from pro_master_terminal where id_master = '".$q9."'");
	$sheet->setCellValue('A3', $q9Txt);
	$sheet->mergeCells('A3:'.$amg[$q1].'3');

	if($q1 == 1){
		$sheet->setCellValue('A4', $arrBln[$q3]." ".$q4);
		$sheet->mergeCells('A4:'.$amg[$q1].'4');
		$sheet->setCellValue('A6', 'GOV');
		$sheet->mergeCells('A6:Y6');
		$sheet->setCellValue('AA6', 'GSV');
		$sheet->mergeCells('AA6:AM6');

		/*ROW 1*/
		$sheet->setCellValue('A7', 'Date');
		$sheet->mergeCells('A7:A8');
		$sheet->setCellValue('B7', 'Book Stock');
		$sheet->mergeCells('B7:B8');
		$sheet->setCellValue('C7', 'Opening Stock');
		$sheet->mergeCells('C7:C8');
		$sheet->setCellValue('D7', 'IN');
		$sheet->mergeCells('D7:F7');
		$sheet->setCellValue('G7', 'OUT');
		$sheet->mergeCells('G7:I7');
		$sheet->setCellValue('J7', 'Closing Stock');
		$sheet->setCellValue('K7', 'Tank Sounding');
		$sheet->mergeCells('K7:R7');
		$sheet->setCellValue('S7', 'Gain/Loss');
		$sheet->mergeCells('S7:T7');
		$sheet->setCellValue('U7', 'Remarks');
		$sheet->mergeCells('V7:Y7');

		$sheet->setCellValue('AA7', 'Date');
		$sheet->mergeCells('AA7:AA8');
		$sheet->setCellValue('AB7', 'Book Stock');
		$sheet->mergeCells('AB7:AB8');
		$sheet->setCellValue('AC7', 'Opening Stock');
		$sheet->mergeCells('AC7:AC8');
		$sheet->setCellValue('AD7', 'IN');
		$sheet->mergeCells('AD7:AF7');
		$sheet->setCellValue('AG7', 'OUT');
		$sheet->mergeCells('AG7:AI7');
		$sheet->setCellValue('AJ7', 'Closing Stock');
		$sheet->mergeCells('AJ7:AK7');
		$sheet->setCellValue('AL7', 'Gain/Loss');
		$sheet->mergeCells('AL7:AM7');

		/*ROW 2*/
		$sheet->setCellValue('D8', 'Ship');
		$sheet->setCellValue('E8', 'Truck');
		$sheet->setCellValue('F8', 'Slop');
		$sheet->setCellValue('G8', '7.00-24.00 (H)');
		$sheet->setCellValue('H8', '00.00 - 7.00 (H+1)');
		$sheet->setCellValue('I8', 'Slop');
		$sheet->setCellValue('J8', 'PE (7.00 H+1)');
		$sheet->setCellValue('K8', 'Time');
		$sheet->setCellValue('L8', 'Level (mm)');
		$sheet->setCellValue('M8', 'Temp');
		$sheet->setCellValue('N8', 'Volume Tabel');
		$sheet->setCellValue('O8', 'Shrinkage Correction');
		$sheet->setCellValue('P8', 'Nett Observed');
		$sheet->setCellValue('Q8', 'Tank Pipe to TLB');
		$sheet->setCellValue('R8', 'Actual Stock');
		$sheet->setCellValue('S8', 'Actual Vs Actual');
		$sheet->setCellValue('T8', 'Actual Vs Book Stock');
		$sheet->setCellValue('V8', 'temp');
		$sheet->setCellValue('W8', 'density (Oberved)');
		$sheet->setCellValue('X8', 'density (@15oC)');
		$sheet->setCellValue('Y8', 'vcf');

		$sheet->setCellValue('AD8', 'Ship');
		$sheet->setCellValue('AE8', 'Truck');
		$sheet->setCellValue('AF8', 'Slop');
		$sheet->setCellValue('AG8', '7.00-24.00 (H)');
		$sheet->setCellValue('AH8', '00.00 - 7.00 (H+1)');
		$sheet->setCellValue('AI8', 'Slop ');
		$sheet->setCellValue('AJ8', 'PE (7.00 H+1)');
		$sheet->setCellValue('AK8', 'Actual Stock');
		$sheet->setCellValue('AL8', 'Actual Vs Actual');
		$sheet->setCellValue('AM8', 'Actual Vs Book Stock');

		/*ROW 3*/
		$sheet->setCellValue('B9', 'Opening Stock(1)');
		$sheet->setCellValue('C9', '2');
		$sheet->setCellValue('D9', '3');
		$sheet->setCellValue('E9', '4');
		$sheet->setCellValue('F9', '5');
		$sheet->setCellValue('G9', '6');
		$sheet->setCellValue('H9', '7');
		$sheet->setCellValue('I9', '8');
		$sheet->setCellValue('J9', '9=(2+3+4+5)-(6+7+8)');
		$sheet->setCellValue('L9', 'Inc. Datum Plate');
		$sheet->setCellValue('P9', '10');
		$sheet->setCellValue('Q9', '11');
		$sheet->setCellValue('R9', '12=10');
		$sheet->setCellValue('S9', '13=12-9');
		$sheet->setCellValue('T9', '14=12-1');
		$sheet->setCellValue('AB9', 'Opening Stock(1)');
		$sheet->setCellValue('AC9', '2');
		$sheet->setCellValue('AD9', '3');
		$sheet->setCellValue('AE9', '4');
		$sheet->setCellValue('AF9', '5');
		$sheet->setCellValue('AG9', '6');
		$sheet->setCellValue('AH9', '7');
		$sheet->setCellValue('AI9', '8');
		$sheet->setCellValue('AJ9', '9=(2+3+4+5)-(6+7+8)');
		$sheet->setCellValue('AK9', '12');
		$sheet->setCellValue('AL9', '13=12-9');
		$sheet->setCellValue('AM9', '14=12-1');

		$sheet->getColumnDimension('A')->setWidth(13);
		$sheet->getColumnDimension('B')->setWidth(13);
		$sheet->getColumnDimension('C')->setWidth(13);
		$sheet->getColumnDimension('D')->setWidth(13);
		$sheet->getColumnDimension('E')->setWidth(13);
		$sheet->getColumnDimension('F')->setWidth(13);
		$sheet->getColumnDimension('G')->setWidth(13);
		$sheet->getColumnDimension('H')->setWidth(13);
		$sheet->getColumnDimension('I')->setWidth(13);
		$sheet->getColumnDimension('J')->setWidth(14);
		$sheet->getColumnDimension('K')->setWidth(13);
		$sheet->getColumnDimension('L')->setWidth(13);
		$sheet->getColumnDimension('M')->setWidth(13);
		$sheet->getColumnDimension('N')->setWidth(13);
		$sheet->getColumnDimension('O')->setWidth(13);
		$sheet->getColumnDimension('P')->setWidth(13);
		$sheet->getColumnDimension('Q')->setWidth(13);
		$sheet->getColumnDimension('R')->setWidth(13);
		$sheet->getColumnDimension('S')->setWidth(13);
		$sheet->getColumnDimension('T')->setWidth(13);
		$sheet->getColumnDimension('U')->setWidth(15);
		$sheet->getColumnDimension('V')->setWidth(8);
		$sheet->getColumnDimension('W')->setWidth(13);
		$sheet->getColumnDimension('X')->setWidth(13);
		$sheet->getColumnDimension('Y')->setWidth(13);
		$sheet->getColumnDimension('Z')->setWidth(11);
		$sheet->getColumnDimension('AA')->setWidth(13);
		$sheet->getColumnDimension('AB')->setWidth(13);
		$sheet->getColumnDimension('AC')->setWidth(13);
		$sheet->getColumnDimension('AD')->setWidth(15);
		$sheet->getColumnDimension('AE')->setWidth(13);
		$sheet->getColumnDimension('AF')->setWidth(13);
		$sheet->getColumnDimension('AG')->setWidth(13);
		$sheet->getColumnDimension('AH')->setWidth(13);
		$sheet->getColumnDimension('AI')->setWidth(13);
		$sheet->getColumnDimension('AJ')->setWidth(14);
		$sheet->getColumnDimension('AK')->setWidth(13);
		$sheet->getColumnDimension('AL')->setWidth(13);
		$sheet->getColumnDimension('AM')->setWidth(13);
		$sheet->getRowDimension(1)->setRowHeight(18);
		$sheet->getRowDimension(2)->setRowHeight(18);
		$sheet->getRowDimension(3)->setRowHeight(18);
		$sheet->getRowDimension(4)->setRowHeight(18);
		$sheet->getRowDimension(6)->setRowHeight(20);
		$sheet->getRowDimension(7)->setRowHeight(30);
		$sheet->getRowDimension(8)->setRowHeight(52);
		$sheet->getRowDimension(9)->setRowHeight(39);
		$sheet->getStyle('A1:AM8')->applyFromArray($styleHeader);
		$sheet->getStyle('A9:AM9')->applyFromArray($styleHeadn1);
		$sheet->getStyle('A7:Y9')->applyFromArray($styleBorder);
		$sheet->getStyle('AA7:AM9')->applyFromArray($styleBorder);
	
		if(count($res) > 0){
			$cellAwal = 10;
			$row = 0;
			$nom = 0;
			foreach($res as $data){
				$nom++;
				$level2 = ($data['awal_level1'])?$data['awal_level1']+$data['awal_level2']:0;
				$loss1 	= ($data['awal_nett'] && $data['gain_loss'])?'='.$data['gain_loss'].'-J'.$cellAwal:0;

				$sheet->setCellValue('A'.$cellAwal, date("d/m/Y", strtotime($data['tanggal_inv'])));
				$sheet->getStyle('A'.$cellAwal)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sheet->setCellValue('B'.$cellAwal, $data['book_stok']);
				$sheet->setCellValue('C'.$cellAwal, $data['awal_nett']);
				$sheet->setCellValue('D'.$cellAwal, $data['masuk_ship']);
				$sheet->setCellValue('E'.$cellAwal, $data['masuk_truck']);
				$sheet->setCellValue('F'.$cellAwal, $data['masuk_slop']);
				$sheet->setCellValue('G'.$cellAwal, $data['out_pagi']);
				$sheet->setCellValue('H'.$cellAwal, $data['out_malam']);
				$sheet->setCellValue('I'.$cellAwal, $data['keluar_slop']);
				$sheet->setCellValue('J'.$cellAwal, '=SUM(C'.$cellAwal.':F'.$cellAwal.')-SUM(G'.$cellAwal.':I'.$cellAwal.')');
				$sheet->setCellValue('K'.$cellAwal, $data['awal_jam']);
				$sheet->getStyle('K'.$cellAwal)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sheet->setCellValue('L'.$cellAwal, $level2);
				$sheet->setCellValue('M'.$cellAwal, $data['awal_temp']);
				$sheet->setCellValue('N'.$cellAwal, $data['awal_volume_tabel']);
				$sheet->setCellValue('O'.$cellAwal, $data['awal_shrink']);
				$sheet->setCellValue('P'.$cellAwal, $data['awal_nett']);
				$sheet->setCellValue('Q'.$cellAwal, $data['tank_pipe']);
				$sheet->setCellValue('R'.$cellAwal, $data['awal_nett']);
				$sheet->setCellValue('S'.$cellAwal, $loss1);
				$sheet->setCellValue('T'.$cellAwal, '=R'.$cellAwal.'-D'.$cellAwal);
				$sheet->setCellValue('V'.$cellAwal, $data['awal_temp']);
				$sheet->setCellValue('W'.$cellAwal, $data['awal_density1']);
				$sheet->setCellValue('X'.$cellAwal, $data['awal_density2']);
				$sheet->setCellValue('Y'.$cellAwal, $data['awal_vcf']);

				$sheet->setCellValue('AA'.$cellAwal, date("d/m/Y", strtotime($data['tanggal_inv'])));
				$sheet->getStyle('AA'.$cellAwal)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sheet->setCellValue('AB'.$cellAwal, '=B'.$cellAwal.'*Y'.$cellAwal);
				$sheet->setCellValue('AC'.$cellAwal, '=R'.$cellAwal.'*Y'.$cellAwal);
				$sheet->setCellValue('AD'.$cellAwal, '=D'.$cellAwal.'*Y'.$cellAwal);
				$sheet->setCellValue('AE'.$cellAwal, '=E'.$cellAwal.'*Y'.$cellAwal);
				$sheet->setCellValue('AF'.$cellAwal, '=F'.$cellAwal.'*Y'.$cellAwal);
				$sheet->setCellValue('AG'.$cellAwal, '=G'.$cellAwal.'*Y'.$cellAwal);
				$sheet->setCellValue('AH'.$cellAwal, '=H'.$cellAwal.'*Y'.$cellAwal);
				$sheet->setCellValue('AI'.$cellAwal, '=I'.$cellAwal.'*Y'.$cellAwal);
				$sheet->setCellValue('AJ'.$cellAwal, '=SUM(AC'.$cellAwal.':AF'.$cellAwal.')-SUM(AG'.$cellAwal.':AI'.$cellAwal.')');
				$sheet->setCellValue('AK'.$cellAwal, '=AC'.$cellAwal);
				$sheet->setCellValue('AL'.$cellAwal, '=S'.$cellAwal.'*Y'.$cellAwal);
				$sheet->setCellValue('AM'.$cellAwal, '=AC'.$cellAwal.'-AB'.$cellAwal);
				$cellAwal++;
			}
			$sheet->getStyle('B10:B'.($cellAwal-1))->getNumberFormat()->setFormatCode('#,##0');
			$sheet->getStyle('C10:L'.($cellAwal-1))->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
			$sheet->getStyle('M10:M'.($cellAwal-1))->getNumberFormat()->setFormatCode('0');
			$sheet->getStyle('N10:N'.($cellAwal-1))->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
			$sheet->getStyle('O10:O'.($cellAwal-1))->getNumberFormat()->setFormatCode('_(* #,##0.0000000_);_(* (#,##0.0000000);_(* "-"??_);_(@_)');
			$sheet->getStyle('P10:T'.($cellAwal-1))->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
			$sheet->getStyle('V10:V'.($cellAwal-1))->getNumberFormat()->setFormatCode('0');
			$sheet->getStyle('W10:Y'.($cellAwal-1))->getNumberFormat()->setFormatCode('0.0000');
			$sheet->getStyle('AB10:AB'.($cellAwal-1))->getNumberFormat()->setFormatCode('#,##0');
			$sheet->getStyle('AC10:AM'.($cellAwal-1))->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
			$sheet->getStyle('A10:Y'.($cellAwal-1))->applyFromArray($styleBorder);
			$sheet->getStyle('AA10:AM'.($cellAwal-1))->applyFromArray($styleBorder);
			
			$sheet->setCellValue('D'.$cellAwal, '=SUM(D10:D'.($cellAwal-1).')');
			$sheet->setCellValue('E'.$cellAwal, '=SUM(E10:E'.($cellAwal-1).')');
			$sheet->setCellValue('F'.$cellAwal, '=SUM(F10:F'.($cellAwal-1).')');
			$sheet->setCellValue('G'.$cellAwal, '=SUM(G10:G'.($cellAwal-1).')');
			$sheet->setCellValue('H'.$cellAwal, '=SUM(H10:H'.($cellAwal-1).')');
			$sheet->setCellValue('I'.$cellAwal, '=SUM(I10:I'.($cellAwal-1).')');
			$sheet->setCellValue('AD'.$cellAwal, '=SUM(AD10:AD'.($cellAwal-1).')');
			$sheet->setCellValue('AE'.$cellAwal, '=SUM(AE10:AE'.($cellAwal-1).')');
			$sheet->setCellValue('AF'.$cellAwal, '=SUM(AF10:AF'.($cellAwal-1).')');
			$sheet->setCellValue('AG'.$cellAwal, '=SUM(AG10:AG'.($cellAwal-1).')');
			$sheet->setCellValue('AH'.$cellAwal, '=SUM(AH10:AH'.($cellAwal-1).')');
			$sheet->setCellValue('AI'.$cellAwal, '=SUM(AI10:AI'.($cellAwal-1).')');
			$cellAwal++;
			$sheet->getStyle('D'.($cellAwal-1).':I'.($cellAwal-1))->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
			$sheet->getStyle('D'.($cellAwal-1).':I'.($cellAwal-1))->applyFromArray($styleBorder);
			$sheet->getStyle('AD'.($cellAwal-1).':AI'.($cellAwal-1))->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
			$sheet->getStyle('AD'.($cellAwal-1).':AI'.($cellAwal-1))->applyFromArray($styleBorder);

			$cellBaru = $cellAwal + 3;
			$sheet->setCellValue('A'.$cellBaru, 'SUMMARY');
			$sheet->mergeCells('A'.$cellBaru.':A'.($cellBaru+1));
			$sheet->setCellValue('B'.$cellBaru, 'OPENING STOCK');
			$sheet->mergeCells('B'.$cellBaru.':B'.($cellBaru+1));
			$sheet->setCellValue('C'.$cellBaru, 'IN');
			$sheet->mergeCells('C'.$cellBaru.':E'.$cellBaru);
			$sheet->setCellValue('F'.$cellBaru, 'OUT');
			$sheet->mergeCells('F'.$cellBaru.':G'.$cellBaru);
			$sheet->setCellValue('H'.$cellBaru, 'END STOCK');
			$sheet->mergeCells('H'.$cellBaru.':I'.$cellBaru);
			$sheet->setCellValue('J'.$cellBaru, 'GAIN/ LOSS');
			$sheet->mergeCells('J'.$cellBaru.':K'.$cellBaru);
			$sheet->setCellValue('L'.$cellBaru, 'PIPE TLB');
			$sheet->mergeCells('L'.$cellBaru.':L'.($cellBaru+1));

			$sheet->setCellValue('C'.($cellBaru+1), 'Ship');
			$sheet->setCellValue('D'.($cellBaru+1), 'Truck');
			$sheet->setCellValue('E'.($cellBaru+1), 'Slop');
			$sheet->setCellValue('F'.($cellBaru+1), 'Customer');
			$sheet->setCellValue('G'.($cellBaru+1), 'Slop');
			$sheet->setCellValue('H'.($cellBaru+1), 'Actual');
			$sheet->setCellValue('I'.($cellBaru+1), 'Book Stock');
			$sheet->setCellValue('J'.($cellBaru+1), 'Actual');
			$sheet->setCellValue('K'.($cellBaru+1), '%');
			$sheet->getRowDimension(($cellBaru))->setRowHeight(18);
			$sheet->getRowDimension(($cellBaru+1))->setRowHeight(18);

			$sheet->setCellValue('A'.($cellBaru+2), 'GOV');
			$sheet->setCellValue('B'.($cellBaru+2), '=C10');
			$sheet->setCellValue('C'.($cellBaru+2), '=D'.($cellAwal-1));
			$sheet->setCellValue('D'.($cellBaru+2), '=E'.($cellAwal-1));
			$sheet->setCellValue('E'.($cellBaru+2), '=F'.($cellAwal-1));
			$sheet->setCellValue('F'.($cellBaru+2), '=SUM(G'.($cellAwal-1).':H'.($cellAwal-1).')');
			$sheet->setCellValue('G'.($cellBaru+2), '=I'.($cellAwal-1));
			$sheet->setCellValue('H'.($cellBaru+2), '=C'.($cellAwal-2));
			$sheet->setCellValue('I'.($cellBaru+2), '=B'.($cellAwal-2));
			$sheet->setCellValue('J'.($cellBaru+2), '=ROUND(H'.($cellBaru+2).'-(SUM(B'.($cellBaru+2).':E'.($cellBaru+2).')-(SUM(F'.($cellBaru+2).':G'.($cellBaru+2).'))),0)');
			$sheet->setCellValue('K'.($cellBaru+2), '=J'.($cellBaru+2).'/(B'.($cellBaru+2).'+C'.($cellBaru+2).'+D'.($cellBaru+2).'+E'.($cellBaru+2).')');
			$sheet->setCellValue('L'.($cellBaru+2), '=Q'.($cellAwal-2));

			$sheet->setCellValue('A'.($cellBaru+3), 'GSV');
			$sheet->setCellValue('B'.($cellBaru+3), '=AC10');
			$sheet->setCellValue('C'.($cellBaru+3), '=AD'.($cellAwal-1));
			$sheet->setCellValue('D'.($cellBaru+3), '=AE'.($cellAwal-1));
			$sheet->setCellValue('E'.($cellBaru+3), '=AF'.($cellAwal-1));
			$sheet->setCellValue('F'.($cellBaru+3), '=SUM(AG'.($cellAwal-1).':AH'.($cellAwal-1).')');
			$sheet->setCellValue('G'.($cellBaru+3), '=AI'.($cellAwal-1));
			$sheet->setCellValue('H'.($cellBaru+3), '=AC'.($cellAwal-2));
			$sheet->setCellValue('I'.($cellBaru+3), '=AB'.($cellAwal-2));
			$sheet->setCellValue('J'.($cellBaru+3), '=ROUND(H'.($cellBaru+3).'-(SUM(B'.($cellBaru+3).':E'.($cellBaru+3).')-(SUM(F'.($cellBaru+3).':G'.($cellBaru+3).'))),0)');
			$sheet->setCellValue('K'.($cellBaru+3), '=J'.($cellBaru+3).'/(B'.($cellBaru+3).'+C'.($cellBaru+3).'+D'.($cellBaru+3).'+E'.($cellBaru+3).')');
			$sheet->setCellValue('L'.($cellBaru+3), '=Q'.($cellAwal-2).'*Y'.($cellAwal-2).'');

			$sheet->getStyle('B'.($cellBaru+2).':H'.($cellBaru+3))->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
			$sheet->getStyle('I'.($cellBaru+2).':I'.($cellBaru+3))->getNumberFormat()->setFormatCode('#,##0');
			$sheet->getStyle('J'.($cellBaru+2).':J'.($cellBaru+3))->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
			$sheet->getStyle('K'.($cellBaru+2).':K'.($cellBaru+3))->getNumberFormat()->setFormatCode('0.000%');
			$sheet->getStyle('L'.($cellBaru+2).':L'.($cellBaru+3))->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
			$sheet->getStyle('A'.($cellBaru).':L'.($cellBaru+1))->applyFromArray($styleHeader);
			$sheet->getStyle('A'.($cellBaru).':L'.($cellBaru+3))->applyFromArray($styleBorder);
		}
	} 
	
	else if($q1 == 2){
		$sheet->setCellValue('A4', $arrBln[$q5]." ".$q6." s/d ".$arrBln[$q7]." ".$q8);
		$sheet->mergeCells('A4:'.$amg[$q1].'4');
		$sheet->setCellValue('A6', 'GOV');
		$sheet->mergeCells('A6:F6');
		$sheet->setCellValue('H6', 'GSV');
		$sheet->mergeCells('H6:M6');

		/*ROW 1*/
		$sheet->setCellValue('A7', 'Bulan');
		$sheet->mergeCells('A7:A8');
		$sheet->setCellValue('B7', 'IN');
		$sheet->mergeCells('B7:D7');
		$sheet->setCellValue('E7', 'OUT');
		$sheet->mergeCells('E7:F7');
		$sheet->setCellValue('H7', 'Bulan');
		$sheet->mergeCells('H7:H8');
		$sheet->setCellValue('I7', 'IN');
		$sheet->mergeCells('I7:K7');
		$sheet->setCellValue('L7', 'OUT');
		$sheet->mergeCells('L7:M7');

		/*ROW 2*/
		$sheet->setCellValue('B8', 'Ship');
		$sheet->setCellValue('C8', 'Truck');
		$sheet->setCellValue('D8', 'Slop');
		$sheet->setCellValue('E8', 'Customer');
		$sheet->setCellValue('F8', 'Slop');
		$sheet->setCellValue('I8', 'Ship');
		$sheet->setCellValue('J8', 'Truck');
		$sheet->setCellValue('K8', 'Slop');
		$sheet->setCellValue('L8', 'Customer');
		$sheet->setCellValue('M8', 'Slop');

		$sheet->getColumnDimension('A')->setWidth(20);
		$sheet->getColumnDimension('B')->setWidth(13);
		$sheet->getColumnDimension('C')->setWidth(13);
		$sheet->getColumnDimension('D')->setWidth(13);
		$sheet->getColumnDimension('E')->setWidth(13);
		$sheet->getColumnDimension('F')->setWidth(13);
		$sheet->getColumnDimension('G')->setWidth(11);
		$sheet->getColumnDimension('H')->setWidth(20);
		$sheet->getColumnDimension('I')->setWidth(13);
		$sheet->getColumnDimension('J')->setWidth(13);
		$sheet->getColumnDimension('K')->setWidth(13);
		$sheet->getColumnDimension('L')->setWidth(13);
		$sheet->getColumnDimension('M')->setWidth(13);
		$sheet->getRowDimension(1)->setRowHeight(18);
		$sheet->getRowDimension(2)->setRowHeight(18);
		$sheet->getRowDimension(3)->setRowHeight(18);
		$sheet->getRowDimension(4)->setRowHeight(18);
		$sheet->getRowDimension(6)->setRowHeight(20);
		$sheet->getRowDimension(7)->setRowHeight(25);
		$sheet->getRowDimension(8)->setRowHeight(25);
		$sheet->getStyle('A1:M8')->applyFromArray($styleHeader);
		$sheet->getStyle('A7:F8')->applyFromArray($styleBorder);
		$sheet->getStyle('H7:M8')->applyFromArray($styleBorder);

		if(count($res1) > 0){
			$cellAwal = 9;
			$row = 0;
			$nom = 0;
			$tbs = $arrBls[intval($q5)]." ".$q6." - ".$arrBls[intval($q7)]." ".$q8;
			foreach($res1 as $data){
				$nom++;
				$textBln = $arrBln[intval(substr($data['bulan_tahun'],4))]." ".substr($data['bulan_tahun'],0,4);

				$sheet->setCellValue('A'.$cellAwal, $textBln);
				$sheet->setCellValue('B'.$cellAwal, $data['in_ship']);
				$sheet->setCellValue('C'.$cellAwal, $data['in_truck']);
				$sheet->setCellValue('D'.$cellAwal, $data['in_slop']);
				$sheet->setCellValue('E'.$cellAwal, $data['customer']);
				$sheet->setCellValue('F'.$cellAwal, $data['out_slop']);
				$sheet->setCellValue('H'.$cellAwal, $textBln);
				$sheet->setCellValue('I'.$cellAwal, $data['in_ship_gsv']);
				$sheet->setCellValue('J'.$cellAwal, $data['in_truck_gsv']);
				$sheet->setCellValue('K'.$cellAwal, $data['in_slop_gsv']);
				$sheet->setCellValue('L'.$cellAwal, $data['customer_gsv']);
				$sheet->setCellValue('M'.$cellAwal, $data['out_slop_gsv']);
				$cellAwal++;
			}
			$sheet->getStyle('B9:F'.($cellAwal-1))->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
			$sheet->getStyle('I9:M'.($cellAwal-1))->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
			
			$sheet->setCellValue('A'.$cellAwal, 'TOTAL');
			$sheet->getStyle('A'.$cellAwal)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->setCellValue('B'.$cellAwal, '=SUM(B9:B'.($cellAwal-1).')');
			$sheet->setCellValue('C'.$cellAwal, '=SUM(C9:C'.($cellAwal-1).')');
			$sheet->setCellValue('D'.$cellAwal, '=SUM(D9:D'.($cellAwal-1).')');
			$sheet->setCellValue('E'.$cellAwal, '=SUM(E9:E'.($cellAwal-1).')');
			$sheet->setCellValue('F'.$cellAwal, '=SUM(F9:F'.($cellAwal-1).')');
			$sheet->setCellValue('H'.$cellAwal, 'TOTAL');
			$sheet->getStyle('H'.$cellAwal)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->setCellValue('I'.$cellAwal, '=SUM(I9:I'.($cellAwal-1).')');
			$sheet->setCellValue('J'.$cellAwal, '=SUM(J9:J'.($cellAwal-1).')');
			$sheet->setCellValue('K'.$cellAwal, '=SUM(K9:K'.($cellAwal-1).')');
			$sheet->setCellValue('L'.$cellAwal, '=SUM(L9:L'.($cellAwal-1).')');
			$sheet->setCellValue('M'.$cellAwal, '=SUM(M9:M'.($cellAwal-1).')');
			$cellAwal++;
			$sheet->getStyle('B'.($cellAwal-1).':F'.($cellAwal-1))->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
			$sheet->getStyle('I'.($cellAwal-1).':M'.($cellAwal-1))->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
			$sheet->getStyle('A9:F'.($cellAwal-1))->applyFromArray($styleBorder);
			$sheet->getStyle('H9:M'.($cellAwal-1))->applyFromArray($styleBorder);

			$cellBaru = $cellAwal + 2;
			$sheet->setCellValue('A'.$cellBaru, 'SUMMARY');
			$sheet->mergeCells('A'.$cellBaru.':K'.$cellBaru);
			$sheet->getStyle('A'.$cellBaru)->applyFromArray($styleHeader);
			$sheet->getRowDimension($cellBaru)->setRowHeight(20);
			$cellBaru++;
			
			$sum_a1_gov = ($res2['end_actual'])?$res2['end_actual']:$res2['end_actual_temp'];
			$sum_a2_gov = ($res2['end_book_stok'])?$res2['end_book_stok']:$res2['end_book_stok_temp'];
			//$sum_a3_gov = $sum_a1_gov - (($masuk1+$masuk2+$masuk3) - ($keluar1+$keluar2));
			//$sum_a4_gov = ($sum_a3_gov / ($masuk1+$masuk2+$masuk3)) * 100;
			
			$sum_a1_gsv = ($res2['end_actual_gov'])?$res2['end_actual_gov']:$res2['end_actual_temp_gov'];
			$sum_a2_gsv = ($res2['end_book_stok_gov'])?$res2['end_book_stok_gov']:$res2['end_book_stok_temp_gov'];
			//$sum_a3_gsv = $sum_a1_gsv - (($masuk1g+$masuk2g+$masuk3g) - ($keluar1g+$keluar2g));
			//$sum_a4_gsv = ($sum_a3_gsv / ($masuk1g+$masuk2g+$masuk3g)) * 100;

			$sheet->setCellValue('A'.$cellBaru, $tbs);
			$sheet->mergeCells('A'.$cellBaru.':A'.($cellBaru+1));
			$sheet->setCellValue('B'.$cellBaru, 'IN');
			$sheet->mergeCells('B'.$cellBaru.':D'.$cellBaru);
			$sheet->setCellValue('E'.$cellBaru, 'OUT');
			$sheet->mergeCells('E'.$cellBaru.':F'.$cellBaru);
			$sheet->setCellValue('G'.$cellBaru, 'END STOCK');
			$sheet->mergeCells('G'.$cellBaru.':H'.$cellBaru);
			$sheet->setCellValue('I'.$cellBaru, 'GAIN/ LOSS');
			$sheet->mergeCells('I'.$cellBaru.':J'.$cellBaru);
			$sheet->setCellValue('K'.$cellBaru, 'PIPE TLB');
			$sheet->mergeCells('K'.$cellBaru.':K'.($cellBaru+1));

			$sheet->setCellValue('B'.($cellBaru+1), 'Ship');
			$sheet->setCellValue('C'.($cellBaru+1), 'Truck');
			$sheet->setCellValue('D'.($cellBaru+1), 'Slop');
			$sheet->setCellValue('E'.($cellBaru+1), 'Customer');
			$sheet->setCellValue('F'.($cellBaru+1), 'Slop');
			$sheet->setCellValue('G'.($cellBaru+1), 'Actual');
			$sheet->setCellValue('H'.($cellBaru+1), 'Book Stock');
			$sheet->setCellValue('I'.($cellBaru+1), 'Actual');
			$sheet->setCellValue('J'.($cellBaru+1), '%');
			$sheet->getRowDimension(($cellBaru))->setRowHeight(18);
			$sheet->getRowDimension(($cellBaru+1))->setRowHeight(18);

			$sheet->setCellValue('A'.($cellBaru+2), 'GOV');
			$sheet->setCellValue('B'.($cellBaru+2), '=B'.($cellAwal-1));
			$sheet->setCellValue('C'.($cellBaru+2), '=C'.($cellAwal-1));
			$sheet->setCellValue('D'.($cellBaru+2), '=D'.($cellAwal-1));
			$sheet->setCellValue('E'.($cellBaru+2), '=E'.($cellAwal-1));
			$sheet->setCellValue('F'.($cellBaru+2), '=F'.($cellAwal-1));
			$sheet->setCellValue('G'.($cellBaru+2), $sum_a1_gov);
			$sheet->setCellValue('H'.($cellBaru+2), $sum_a2_gov);
			$sheet->setCellValue('I'.($cellBaru+2), '=ROUND(G'.($cellBaru+2).'-(SUM(B'.($cellBaru+2).':D'.($cellBaru+2).')-(SUM(E'.($cellBaru+2).':F'.($cellBaru+2).'))),0)');
			$sheet->setCellValue('J'.($cellBaru+2), '=I'.($cellBaru+2).'/(B'.($cellBaru+2).'+C'.($cellBaru+2).'+D'.($cellBaru+2).')');
			$sheet->setCellValue('K'.($cellBaru+2), $res2['end_pipe']);

			$sheet->setCellValue('A'.($cellBaru+3), 'GSV');
			$sheet->setCellValue('B'.($cellBaru+3), '=I'.($cellAwal-1));
			$sheet->setCellValue('C'.($cellBaru+3), '=J'.($cellAwal-1));
			$sheet->setCellValue('D'.($cellBaru+3), '=K'.($cellAwal-1));
			$sheet->setCellValue('E'.($cellBaru+3), '=L'.($cellAwal-1));
			$sheet->setCellValue('F'.($cellBaru+3), '=M'.($cellAwal-1));
			$sheet->setCellValue('G'.($cellBaru+3), $sum_a1_gsv);
			$sheet->setCellValue('H'.($cellBaru+3), $sum_a2_gsv);
			$sheet->setCellValue('I'.($cellBaru+3), '=ROUND(G'.($cellBaru+3).'-(SUM(B'.($cellBaru+3).':D'.($cellBaru+3).')-(SUM(E'.($cellBaru+3).':F'.($cellBaru+3).'))),0)');
			$sheet->setCellValue('J'.($cellBaru+3), '=I'.($cellBaru+3).'/(B'.($cellBaru+3).'+C'.($cellBaru+3).'+D'.($cellBaru+3).')');
			$sheet->setCellValue('K'.($cellBaru+3), $res2['end_pipe_gov']);

			$sheet->getStyle('B'.($cellBaru+2).':G'.($cellBaru+3))->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
			$sheet->getStyle('H'.($cellBaru+2).':H'.($cellBaru+3))->getNumberFormat()->setFormatCode('#,##0');
			$sheet->getStyle('I'.($cellBaru+2).':I'.($cellBaru+3))->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
			$sheet->getStyle('J'.($cellBaru+2).':J'.($cellBaru+3))->getNumberFormat()->setFormatCode('0.000%');
			$sheet->getStyle('K'.($cellBaru+2).':K'.($cellBaru+3))->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
			$sheet->getStyle('A'.($cellBaru).':K'.($cellBaru+1))->applyFromArray($styleHeader);
			$sheet->getStyle('A'.($cellBaru).':K'.($cellBaru+3))->applyFromArray($styleBorder);
		}
	}

	$con->close();
	$filename = "Laporan-Inventory-".date('dmYHis').".xlsx";
	header('Content-disposition: attachment; filename="'.sanitize_filename($filename).'"');
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	$objWriter = PHPExcel_IOFactory::createWriter($obj, 'Excel2007');
	$objWriter->save('php://output');
	exit();
?>

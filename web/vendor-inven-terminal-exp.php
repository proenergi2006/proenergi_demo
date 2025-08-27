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

	// $sql = "select a.tanggal_inven, sum(a.awal_inven) as awal_inven, sum(a.in_inven) as in_inven, sum(a.out_inven) as out_inven, sum(a.adj_inven) as adj_inven 
	// 		from pro_inventory_vendor a where a.id_produk = '".$q4."' and a.id_terminal = '".$q1."' and month(a.tanggal_inven) = '".$q2."' and year(a.tanggal_inven) = '".$q3."' 
	// 		group by a.tanggal_inven order by a.tanggal_inven";
	// $res = $con->getResult($sql);
	$sql = "
        select b.id_master, b.nama_terminal, c.nama_area, d.nama_vendor, sum(a.awal_inven) as awal_inven, sum(a.in_inven) as in_inven, sum(a.out_inven) as out_inven, sum(a.adj_inven) as adj_inven
		from pro_inventory_vendor a 
        join pro_master_terminal b on b.id_master = a.id_terminal
        join pro_master_area c on c.id_master = a.id_area
        join pro_master_vendor d on d.id_master = a.id_vendor
        where a.id_produk = '".$q4."'
    ";
    if ($q1) $sql .= " and a.id_terminal = '".$q1."' ";
    if ($q2) $sql .= " and month(a.tanggal_inven) = '".$q2."' ";
    if ($q3) $sql .= " and year(a.tanggal_inven) = '".$q3."' ";
    $sql .= " group by b.id_master, c.nama_area, d.nama_vendor ";
    $res = $con->getResult($sql);

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
						'font' => array(
							'bold' => true,
							'size' => 10
						),
						'alignment' => array(
							'horizontal' 	=> PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
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
	
	$sheet->setCellValue('A1', 'LAPORAN INVENTORY VENDOR BY DEPOT');
	$sheet->mergeCells('A1:G1');
	$q4Txt = $con->getOne("select concat(jenis_produk,' - ',merk_dagang) as produk from pro_master_produk where id_master = '".$q4."'");
	$sheet->setCellValue('A2', $q4Txt);
	$sheet->mergeCells('A2:G2');
	if ($q1) {
		$q1Txt = $con->getOne("select concat(nama_terminal,' ',lokasi_terminal,', ',lokasi_terminal) as terminal from pro_master_terminal where id_master = '".$q1."'");
		$sheet->setCellValue('A3', $q1Txt);
	} else {
		$sheet->setCellValue('A3', 'Semua Terminal/Depot');
	}
	$sheet->mergeCells('A3:G3');
	if ($q2 and $q3) {
		$sheet->setCellValue('A4', $arrBln[$q2]." ".$q3);
		$sheet->mergeCells('A4:F4');
	}
	$sheet->getStyle('A1:G4')->applyFromArray($styleHeader);

	$sheet->getRowDimension(1)->setRowHeight(18);
	$sheet->getRowDimension(2)->setRowHeight(18);
	$sheet->getRowDimension(3)->setRowHeight(18);
	$sheet->getRowDimension(4)->setRowHeight(18);

	// /*ROW 1*/
	$sheet->setCellValue('A6', 'Terminal/Depot');
	$sheet->setCellValue('B6', 'Vendor/Suplier');
	$sheet->setCellValue('C6', 'Nama Area');
	$sheet->setCellValue('D6', 'Beginning');
	$sheet->setCellValue('E6', 'Input');
	$sheet->setCellValue('F6', 'Ouput');
	$sheet->setCellValue('G6', 'Adj Inv');
	$sheet->setCellValue('H6', 'Ending');

	$sheet->getColumnDimension('A')->setWidth(13);
	$sheet->getColumnDimension('B')->setWidth(13);
	$sheet->getColumnDimension('C')->setWidth(13);
	$sheet->getColumnDimension('D')->setWidth(13);
	$sheet->getColumnDimension('E')->setWidth(13);
	$sheet->getColumnDimension('F')->setWidth(13);
	$sheet->getColumnDimension('G')->setWidth(13);
	$sheet->getColumnDimension('H')->setWidth(13);
	$sheet->getRowDimension(6)->setRowHeight(30);
	$sheet->getStyle('A6:H6')->applyFromArray($styleHeader);
	$sheet->getStyle('A6:H6')->applyFromArray($styleBorder);
	
	if(count($res) > 0){
		$cellAwal = 7;
		$row = 0;
		$nom = 0;
		$awal_inven = 0;
		$in_inven = 0;
		$out_inven = 0;
		$adj_inven = 0;
		$ending = 0;
		foreach($res as $data){
			$nom++;
			$sheet->setCellValue('A'.$cellAwal, $data['nama_terminal']);
			$sheet->setCellValue('B'.$cellAwal, $data['nama_vendor']);
			$sheet->setCellValue('C'.$cellAwal, $data['nama_area']);
			$awal_inven += $data['awal_inven'];
			$sheet->setCellValue('D'.$cellAwal, $data['awal_inven']);
			$in_inven += $data['in_inven'];
			$sheet->setCellValue('E'.$cellAwal, $data['in_inven']);
			$out_inven += $data['out_inven'];
			$sheet->setCellValue('F'.$cellAwal, $data['out_inven']);
			$adj_inven += $data['adj_inven'];
			$sheet->setCellValue('G'.$cellAwal, $data['adj_inven']);
			$ending_ = ($data['awal_inven']+$data['in_inven']+$data['adj_inven'])-$data['out_inven'];
			$ending += $ending_;
			$sheet->setCellValue('H'.$cellAwal, (string)$ending_);
			$cellAwal++;
		}
		$sheet->setCellValue('A'.$cellAwal, 'TOTAL');
		$sheet->getStyle('A'.$cellAwal)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->mergeCells('A'.$cellAwal.':C'.$cellAwal);
		$sheet->setCellValue('D'.$cellAwal, (string)$awal_inven);
		$sheet->setCellValue('E'.$cellAwal, (string)$in_inven);
		$sheet->setCellValue('F'.$cellAwal, (string)$out_inven);
		$sheet->setCellValue('G'.$cellAwal, (string)$adj_inven);
		$sheet->setCellValue('H'.$cellAwal, (string)$ending);
		$sheet->getStyle('A'.$cellAwal.':H'.$cellAwal)->applyFromArray($styleHeadn1);
		$sheet->getRowDimension($cellAwal)->setRowHeight(20);
		$cellAwal++;
		$sheet->getStyle('D7:H'.($cellAwal-1))->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
		$sheet->getStyle('A7:H'.($cellAwal-1))->applyFromArray($styleBorder);
	}
	
	$con->close();
	$filename = "Laporan-Inventory-Vendor-Depot-".date('dmYHis').".xlsx";
	header('Content-disposition: attachment; filename="'.sanitize_filename($filename).'"');
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	$objWriter = PHPExcel_IOFactory::createWriter($obj, 'Excel2007');
	$objWriter->save('php://output');
	exit();
?>

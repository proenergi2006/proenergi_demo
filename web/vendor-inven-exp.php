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

	$sql = "select a.*, b.nama_terminal, c.nama_vendor
                    from pro_inventory_vendor a 
                    join pro_master_terminal b on b.id_master = a.id_terminal
                    join pro_master_vendor c on c.id_master = a.id_vendor
                    where 1=1 ";
	if($q1) $sql .= " and a.id_vendor = '".$q1."'";
	if($q2) $sql .= " and a.id_produk = '".$q2."'";
	if($q3) $sql .= " and a.id_area = '".$q3."'";
	if($q4) $sql .= " and a.id_terminal = '".$q4."'";
	if($q5 && $q6) $sql .= " and month(a.tanggal_inven) = '".$q5."' and year(a.tanggal_inven) = '".$q6."'";
    if(!$q5 && $q6) $sql .= " and year(a.tanggal_inven) = '".$q6."'";
    if($q6) $sql .= " order by a.tanggal_inven desc";
	else $sql .= " order by a.tanggal_inven desc"; 
	
	// $tot_record = $con->num_rows($sql);
	// $tot_page 	= ceil($tot_record/$limit);
 //    // $page       = 0;
	// // $page		= ($enk[$page] > $tot_page)?$enk[$page]-1:$enk[$page]; 
 //    $page       = ($start > $tot_page)?$start-1:$start;
	// $position 	= $p->findPosition($limit, $tot_record, $page);
	// $param_ref	= "&q1=".$q1."&q2=".$q2."&q3=".$q3."&q4=".$q4."&q5=".$q5."&q6=".$q6;
	// if($q5 && $q6) $sql .= " order by a.tanggal_inven desc";
    // if($q6) $sql .= " order by a.tanggal_inven desc";
	// else $sql .= " order by a.tanggal_inven desc";
	//eend

	// $sql = "select a.* from pro_inventory_vendor a where 1=1";
	// if($q1) $sql .= " and a.id_vendor = '".$q1."'";
	// if($q2) $sql .= " and a.id_produk = '".$q2."'";
	// if($q3) $sql .= " and a.id_area = '".$q3."'";
	// if($q4) $sql .= " and a.id_terminal = '".$q4."'";
	// if($q5 && $q6) $sql .= " and month(a.tanggal_inven) = '".$q5."' and year(a.tanggal_inven) = '".$q6."'";
	// if(!$q5 && $q6) $sql .= " and year(a.tanggal_inven) = '".$q6."'";
	// $sql .= " order by a.tanggal_inven asc";
	$res = $con->getResult($sql);
	if (!$q5 && !$q6) {
        $resTemp = [];
        $j = 0;
        // for ($i=(count($res)-1); $i >= 0; $i--) {
        for ($i=0; $i < count($res); $i++) {
            $resTemp[$j] = $res[$i];
            $j ++;
        }
        $res = $resTemp;
    }

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
							'horizontal' 	=> PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
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
	
	$sheet->setCellValue('A1', 'LAPORAN INVENTORY VENDOR');
	$sheet->mergeCells('A1:F1');
	$q2Txt = $con->getOne("select concat(jenis_produk,' - ',merk_dagang) as produk from pro_master_produk where id_master = '".$q2."'");
	$sheet->setCellValue('A2', $q2Txt);
	$sheet->mergeCells('A2:F2');
	$q3Txt = $con->getOne("select nama_area from pro_master_area where id_master = '".$q3."'");
	$sheet->setCellValue('A3', 'Area '.$q3Txt);
	$sheet->mergeCells('A3:F3');
	$q4Txt = $con->getOne("select concat(nama_terminal,' ',lokasi_terminal,', ',lokasi_terminal) as terminal from pro_master_terminal where id_master = '".$q4."'");
	$sheet->setCellValue('A4', $q4Txt);
	$sheet->mergeCells('A4:F4');
	$sheet->getStyle('A1:F4')->applyFromArray($styleHeader);

	$q1Txt = $con->getOne("select nama_vendor from pro_master_vendor where id_master = '".$q1."'");
	$sheet->setCellValue('A6', 'Suplier : '.$q1Txt);
	$sheet->mergeCells('A6:F6');
	$sheet->setCellValue('A7', 'Bulan : '.$arrBln[$q5]." ".$q6);
	$sheet->mergeCells('A7:F7');
	$sheet->getStyle('A6:F7')->applyFromArray($styleHeadn1);
	$sheet->getRowDimension(1)->setRowHeight(18);
	$sheet->getRowDimension(2)->setRowHeight(18);
	$sheet->getRowDimension(3)->setRowHeight(18);
	$sheet->getRowDimension(4)->setRowHeight(18);
	$sheet->getRowDimension(6)->setRowHeight(18);
	$sheet->getRowDimension(7)->setRowHeight(18);

	/*ROW 1*/
	$sheet->setCellValue('A8', 'Date');
	$sheet->setCellValue('B8', 'Beginning');
	$sheet->setCellValue('C8', 'Input');
	$sheet->setCellValue('D8', 'Ouput');
	$sheet->setCellValue('E8', 'Adj Inv');
	$sheet->setCellValue('F8', 'Ending');

	$sheet->getColumnDimension('A')->setWidth(13);
	$sheet->getColumnDimension('B')->setWidth(13);
	$sheet->getColumnDimension('C')->setWidth(13);
	$sheet->getColumnDimension('D')->setWidth(13);
	$sheet->getColumnDimension('E')->setWidth(13);
	$sheet->getColumnDimension('F')->setWidth(13);
	$sheet->getRowDimension(8)->setRowHeight(30);
	$sheet->getStyle('A8:F8')->applyFromArray($styleHeader);
	$sheet->getStyle('A8:F8')->applyFromArray($styleBorder);
	
	if(count($res) > 0){
		$cellAwal = 9;
		$row = 0;
		$nom = 0;
		$tot1 = 0;
        $tot2 = 0;
        $tot3 = 0;
        $tot4 = 0;
        $tot5 = 0;
        $totA = 0;
        $totTemp = 0;
        for ($i = count($res)-1; $i >= 0; $i--) {
			$data = $res[$i];
            $awal_inven = isset($data['awal_inven']) ? $data['awal_inven'] : 0;
            $out_inven = isset($data['out_inven']) ? $data['out_inven'] : 0;
            $in_inven = isset($data['in_inven']) ? $data['in_inven'] : 0;
            $adj_inven = isset($data['adj_inven']) ? $data['adj_inven'] : 0;
            $id_master = isset($data['id_master']) ? $data['id_master'] : null;
            $tanggal_inven = isset($data['tanggal_inven']) ? $data['tanggal_inven'] : null;

            $noms++;
            // $awal_inven = $totA;
            $awal_inven = $i==0?$awal_inven:$totA;
            $out_inven = str_replace('-', '', $out_inven);
            $totA = ($awal_inven + $in_inven + $adj_inven) - $out_inven;
            if ($i == 1) $totTemp = $totA;
            if ($i == 0) $awal_inven = $totTemp;
            /* Update new belum fix
            // if ($i == 0) {
            //     $awal_inven = $totTemp;
            //     $totA = ($awal_inven + $in_inven + $adj_inven) - $out_inven;
            // }
            */
            $tot1 	= $tot1 + $awal_inven;
            $tot2 	= $tot2 + $in_inven;
            $tot3 	= $tot3 + $out_inven;
            $tot4 	= $tot4 + $adj_inven;
            // $tot5 	= $tot5 + $totA;
            $tot5   = $tot2 - $tot3 - $tot4;
			
			$sheet->setCellValue('A'.$cellAwal, date("d/m/Y", strtotime($data['tanggal_inven'])));
			$sheet->getStyle('A'.$cellAwal)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->setCellValue('B'.$cellAwal, $awal_inven);
			$sheet->setCellValue('C'.$cellAwal, $in_inven);
			$sheet->setCellValue('D'.$cellAwal, $out_inven);
			$sheet->setCellValue('E'.$cellAwal, $adj_inven);
			$sheet->setCellValue('F'.$cellAwal, $totA);
			$cellAwal++;
			
		}
		$sheet->setCellValue('A'.$cellAwal, 'TOTAL');
		$sheet->getStyle('A'.$cellAwal)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->setCellValue('B'.$cellAwal, '-');
		$sheet->setCellValue('C'.$cellAwal, '=SUM(C9:C'.($cellAwal-1).')');
		$sheet->setCellValue('D'.$cellAwal, '=SUM(D9:D'.($cellAwal-1).')');
		$sheet->setCellValue('E'.$cellAwal, '=SUM(E9:E'.($cellAwal-1).')');
		$sheet->setCellValue('F'.$cellAwal, '=SUM(F9:F'.($cellAwal-1).')');
		$sheet->getStyle('A'.$cellAwal.':F'.$cellAwal)->applyFromArray($styleHeader);
		$sheet->getRowDimension($cellAwal)->setRowHeight(20);
		$cellAwal++;
		$sheet->getStyle('B9:F'.($cellAwal-1))->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
		$sheet->getStyle('A9:F'.($cellAwal-1))->applyFromArray($styleBorder);
	}
	
	$con->close();
	$filename = "Laporan-Inventory-Vendor-".date('dmYHis').".xlsx";
	header('Content-disposition: attachment; filename="'.sanitize_filename($filename).'"');
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	$objWriter = PHPExcel_IOFactory::createWriter($obj, 'Excel2007');
	$objWriter->save('php://output');
	exit();
?>

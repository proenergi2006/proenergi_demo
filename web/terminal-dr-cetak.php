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
	$idr 	= isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
	$nod 	= htmlspecialchars($enk["nom"], ENT_QUOTES);
	$tgl 	= htmlspecialchars($enk["tgl"], ENT_QUOTES);
	$cab 	= htmlspecialchars($enk["cab"], ENT_QUOTES);

	$sql = "select a.*, b.sm_result, b.sm_summary, b.sm_pic, b.sm_tanggal, b.purchasing_result, b.purchasing_summary, b.purchasing_pic, b.purchasing_tanggal, 
			b.cfo_result, b.cfo_summary, b.cfo_pic, b.cfo_tanggal, b.is_ceo, b.ceo_result, b.ceo_summary, b.ceo_pic, b.ceo_tanggal, c.created_time, 
			c.tanggal_kirim, e.alamat_survey, e.id_wil_oa, f.nama_prov, g.nama_kab, n.nilai_pbbkb, k.masa_awal, k.masa_akhir, k.id_area, o.harga_normal, 
			h.nama_customer, h.id_customer, i.fullname, l.nama_area, d.harga_poc, k.refund_tawar, m.jenis_produk, e.jenis_usaha, d.nomor_poc, d.produk_poc, 
			p.nama_terminal, p.tanki_terminal, p.lokasi_terminal, q.nama_vendor, c.status_jadwal, h.kode_pelanggan, m.merk_dagang, d.id_poc, 
			d.lampiran_poc, d.lampiran_poc_ori, r.wilayah_angkut, s.id_pod, t.id_dsd, u.id_dsk 
			from pro_pr_detail a 
			join pro_pr b on a.id_pr = b.id_pr 
			join pro_po_customer_plan c on a.id_plan = c.id_plan 
			join pro_po_customer d on c.id_poc = d.id_poc 
			join pro_customer_lcr e on c.id_lcr = e.id_lcr
			join pro_master_provinsi f on e.prov_survey = f.id_prov 
			join pro_master_kabupaten g on e.kab_survey = g.id_kab
			join pro_customer h on d.id_customer = h.id_customer 
			join acl_user i on h.id_marketing = i.id_user 
			join pro_master_cabang j on h.id_wilayah = j.id_master 
			join pro_penawaran k on d.id_penawaran = k.id_penawaran  
			join pro_master_area l on k.id_area = l.id_master 
			join pro_master_produk m on d.produk_poc = m.id_master 
			join pro_master_pbbkb n on k.pbbkb_tawar = n.id_master 
			join pro_master_harga_minyak o on o.periode_awal = k.masa_awal and o.periode_akhir = k.masa_akhir and o.id_area = k.id_area 
			and o.produk = k.produk_tawar and o.pajak = k.pbbkb_tawar 
			left join pro_master_terminal p on a.pr_terminal = p.id_master 
			left join pro_master_vendor q on a.pr_vendor = q.id_master 
			left join pro_master_wilayah_angkut r on e.id_wil_oa = r.id_master and e.prov_survey = r.id_prov and e.kab_survey = r.id_kab 
			left join pro_po_detail s on a.id_prd = s.id_prd 
			left join pro_po_ds_detail t on a.id_prd = t.id_prd 
			left join pro_po_ds_kapal u on a.id_prd = u.id_prd 
			where a.id_pr = '".$idr."' and a.is_approved = 1
			order by a.is_approved desc, c.tanggal_kirim, k.id_cabang, k.id_area, a.id_plan, a.id_prd";
	$res = $con->getResult($sql);

	$obj = new PHPExcel();
	$obj->setActiveSheetIndex(0);
	$sheet = $obj->getActiveSheet();
	$sheet->getSheetView()->setZoomScale(80);
	$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LEGAL);
	$sheet->getDefaultStyle()->getFont()->setName('Arial');
	$sheet->getDefaultStyle()->getFont()->setSize('11');
	//$sheet->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

	$styleHeader = array(
						'font' => array(
							'bold' => true,
							'size' => 11
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
							'size' => 11
						),
						'alignment' => array(
							'vertical' 		=> PHPExcel_Style_Alignment::VERTICAL_TOP,
							'wrap'			=> true
						)
					);
	
	$sheet->setCellValue('A1', 'DELIVERY REQUEST');
	$sheet->mergeCells('A1:F1');
	$sheet->getStyle('A1:F1')->applyFromArray($styleHeader);

	$sheet->setCellValue('A3', 'Nomor DR : '.$nod);
	$sheet->mergeCells('A3:F3');
	$sheet->setCellValue('A4', 'Tanggal DR : '.tgl_indo($tgl));
	$sheet->mergeCells('A4:F4');
	$sheet->setCellValue('A5', 'Cabang Invoice : '.$cab);
	$sheet->mergeCells('A5:F5');
	$sheet->getStyle('A3:F5')->applyFromArray($styleHeadn1);
	$sheet->getRowDimension(1)->setRowHeight(18);
	$sheet->getRowDimension(2)->setRowHeight(18);
	$sheet->getRowDimension(3)->setRowHeight(18);
	$sheet->getRowDimension(4)->setRowHeight(18);
	$sheet->getRowDimension(5)->setRowHeight(18);

	/*ROW 1*/
	$sheet->setCellValue('A7', 'Customer');
	$sheet->setCellValue('B7', 'Alamat Kirim');
	$sheet->setCellValue('C7', 'Produk');
	$sheet->setCellValue('D7', 'Angkutan');
	$sheet->setCellValue('E7', 'Volume');
	$sheet->setCellValue('F7', 'Depot');

	$sheet->getColumnDimension('A')->setWidth(25);
	$sheet->getColumnDimension('B')->setWidth(40);
	$sheet->getColumnDimension('C')->setWidth(15);
	$sheet->getColumnDimension('D')->setWidth(14);
	$sheet->getColumnDimension('E')->setWidth(14);
	$sheet->getColumnDimension('F')->setWidth(30);
	$sheet->getRowDimension(7)->setRowHeight(25);
	$sheet->getStyle('A7:F7')->applyFromArray($styleHeader);
	$sheet->getStyle('A7:F7')->applyFromArray($styleBorder);
	
	
	if(count($res) > 0){
		$arrMobil = array(1=>"Truck", "Kapal", "Loco");		
		$cellAwal = 8;
		$row = 0;
		$nom = 0;
		foreach($res as $data){
			$nom++;
			$tempal = strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
			$alamat	= $data['alamat_survey']." ".ucwords($tempal)." ".$data['nama_prov'];
			$tmn1 	= ($data['nama_terminal'])?$data['nama_terminal']:'';
			$tmn2 	= ($data['tanki_terminal'])?' - '.$data['tanki_terminal']:'';
			$tmn3 	= ($data['lokasi_terminal'])?', '.$data['lokasi_terminal']:'';
			$depot 	= $tmn1.$tmn2.$tmn3;

			$sheet->setCellValue('A'.$cellAwal, $data['nama_customer']);
			$sheet->setCellValue('B'.$cellAwal, $alamat);
			$sheet->setCellValue('C'.$cellAwal, $data['merk_dagang']);
			$sheet->setCellValue('D'.$cellAwal, $arrMobil[$data['pr_mobil']]);
			$sheet->setCellValue('E'.$cellAwal, $data['volume']);
			$sheet->setCellValue('F'.$cellAwal, $depot);
			$cellAwal++;
		}
		$sheet->getStyle('A8:F'.($cellAwal-1))->applyFromArray($styleBorder);
	}
	
	$con->close();
	$filename = "Delivery-Request-".date('dmYHis').".xlsx";
	header('Content-disposition: attachment; filename="'.sanitize_filename($filename).'"');
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	$objWriter = PHPExcel_IOFactory::createWriter($obj, 'Excel2007');
	$objWriter->save('php://output');
	exit();
?>

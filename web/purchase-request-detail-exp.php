<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
require_once($public_base_directory . "/libraries/helper/excelgen/PHPExcel/IOFactory.php");
load_helper("autoload");

error_reporting(E_ALL ^ E_DEPRECATED);
// error_reporting(E_ALL ^ (E_NOTICE | E_WARNING | E_DEPRECATED));

$auth   = new MyOtentikasi();
$con    = new Connection();
$flash  = new FlashAlerts;
$enk    = decode($_SERVER['REQUEST_URI']);

$idr = htmlspecialchars($enk["idr"], ENT_QUOTES);

$sql0 = "select a.id_pr, a.nomor_pr, a.tanggal_pr, a.jam_submit, a.disposisi_pr, a.is_edited, a.id_wilayah, a.id_group, b.nama_cabang, c.id_par, c.tanggal_buat 
            from pro_pr a join pro_master_cabang b on a.id_wilayah = b.id_master left join pro_pr_ar c on a.id_pr = c.id_pr and c.ar_approved = 1 
            where a.id_pr = '" . $idr . "'";
$res0 = $con->getResult($sql0);

$sql = "
        select a.*, 
            b.sm_result, 
            b.sm_summary, 
            b.sm_pic, 
            b.sm_tanggal, 
            b.purchasing_result, 
            b.purchasing_summary, 
            b.purchasing_pic, 
            b.purchasing_tanggal, 
            c.tanggal_kirim, 
            c.status_plan, 
            c.catatan_reschedule, 
            c.status_jadwal, 
            e.alamat_survey, 
            e.id_wil_oa, 
            f.nama_prov, 
            g.nama_kab, 
            n.nilai_pbbkb, 
            k.masa_awal, 
            k.masa_akhir, 
            k.id_area, 
            o.harga_normal, 
            h.nama_customer, 
            h.id_customer, 
            i.fullname, 
            l.nama_area, 
            d.harga_poc, 
            k.refund_tawar, 
            k.other_cost, 
            m.jenis_produk, 
            e.jenis_usaha, 
            d.nomor_poc, 
            d.produk_poc, 
            p.nama_terminal, 
            p.tanki_terminal, 
            p.lokasi_terminal, 
            q.nama_vendor, 
            r.wilayah_angkut, 
            m.merk_dagang, 
            d.lampiran_poc, 
            d.lampiran_poc_ori, 
            d.id_poc, 
            h.kode_pelanggan, 
            b.revert_cfo, 
            b.revert_cfo_summary, 
            b.revert_ceo, 
            b.revert_ceo_summary 
        from 
            pro_pr_detail a 
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
            left join pro_master_harga_minyak o on o.periode_awal = k.masa_awal and o.periode_akhir = k.masa_akhir and o.id_area = k.id_area 
                and o.produk = k.produk_tawar and o.pajak = k.pbbkb_tawar 
            left join pro_master_terminal p on a.pr_terminal = p.id_master 
            left join pro_master_vendor q on a.pr_vendor = q.id_master 
            left join pro_master_wilayah_angkut r on e.id_wil_oa = r.id_master and e.prov_survey = r.id_prov and e.kab_survey = r.id_kab 
        where 
            a.id_pr = '" . $idr . "' 
            and a.is_approved = 1 
        order by 
            a.is_approved desc, 
            c.tanggal_kirim, 
            k.id_cabang, 
            k.id_area, 
            a.id_plan, 
            a.id_prd
    ";
$res = $con->getResult($sql);
$tot_record = count($res);
$position   = 0;
// echo json_encode($res);
// die();

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
        'horizontal'    => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical'      => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        'wrap'          => true
    )
);
$styleHeadn1 = array(
    'alignment' => array(
        'horizontal'    => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical'      => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        'wrap'          => true
    )
);
$styleBorder = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
$styleTabel  = array(
    'font' => array(
        'size' => 8
    ),
    'alignment' => array(
        'vertical'      => PHPExcel_Style_Alignment::VERTICAL_TOP,
        'wrap'          => true
    )
);

$sheet->setCellValue('A1', 'DELIVERY REQUEST DETAIL');
$sheet->mergeCells('A1:' . 'U1');

/*HEADER*/
$sheet->setCellValue('A2', 'Kode DR:');
$sheet->setCellValue('B2', $res0[0]['nomor_pr']);
$sheet->setCellValue('A3', 'Tanggal:');
$sheet->setCellValue('B3', tgl_indo($res0[0]['tanggal_pr']));
$sheet->setCellValue('A4', 'Cabang:');
$sheet->setCellValue('B4', $res0[0]['nama_cabang']);
$sheet->setCellValue('A5', 'Jam Submit:');
$sheet->setCellValue('B5', date("H:i:s", strtotime($res0[0]['jam_submit'])));
$sheet->setCellValue('A6', 'NO');
$sheet->mergeCells('A6:A7');
$sheet->setCellValue('B6', 'Customer/ Bidang Usaha');
$sheet->mergeCells('B6:B7');
$sheet->setCellValue('C6', 'Area/ Alamat Kirim/ Wilayah OA');
$sheet->mergeCells('C6:C7');
$sheet->setCellValue('D6', 'PO Customer');
$sheet->mergeCells('D6:D7');
$sheet->setCellValue('E6', 'Volume (Liter)');
$sheet->mergeCells('E6:E7');
$sheet->setCellValue('F6', 'PBBKB');
$sheet->mergeCells('F6:F7');
$sheet->setCellValue('G6', 'Suplier');
$sheet->mergeCells('G6:G7');
$sheet->setCellValue('H6', 'Depot');
$sheet->mergeCells('H6:H7');
$sheet->setCellValue('I6', 'Harga Beli');
$sheet->mergeCells('I6:I7');
$sheet->setCellValue('J6', 'Harga (Rp/Liter)');
$sheet->mergeCells('J6:P6');
$sheet->setCellValue('J7', 'Harga Jual (Gross)');
$sheet->setCellValue('K7', 'Ongkos Angkut');
$sheet->setCellValue('L7', 'Refund');
$sheet->setCellValue('M7', 'Oil Dues');
$sheet->setCellValue('N7', 'PBBKB');
$sheet->setCellValue('O7', 'Other Cost');
$sheet->setCellValue('P7', 'Harga Jual (Nett)');
$sheet->setCellValue('Q6', 'Nett Profit');
$sheet->mergeCells('Q6:Q7');
$sheet->setCellValue('R6', 'Price List');
$sheet->mergeCells('R6:R7');
$sheet->setCellValue('S6', 'Gain/Loss');
$sheet->mergeCells('S6:S7');
$sheet->setCellValue('T6', 'Catatan');
$sheet->mergeCells('T6:T7');
$sheet->setCellValue('U6', 'Loading Order');
$sheet->mergeCells('U6:U7');

$sheet->getColumnDimension('A')->setWidth(13);
$sheet->getColumnDimension('B')->setWidth(13);
$sheet->getColumnDimension('C')->setWidth(13);
$sheet->getColumnDimension('D')->setWidth(13);
$sheet->getColumnDimension('E')->setWidth(13);
$sheet->getColumnDimension('F')->setWidth(13);
$sheet->getColumnDimension('G')->setWidth(13);
$sheet->getColumnDimension('H')->setWidth(13);
$sheet->getColumnDimension('I')->setWidth(13);
$sheet->getColumnDimension('J')->setWidth(13);
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
$sheet->getColumnDimension('U')->setWidth(13);
/*
    $sheet->getRowDimension(1)->setRowHeight(18);
    $sheet->getRowDimension(2)->setRowHeight(18);
    $sheet->getRowDimension(3)->setRowHeight(18);
    $sheet->getRowDimension(4)->setRowHeight(18);
    $sheet->getRowDimension(6)->setRowHeight(20);
    $sheet->getRowDimension(7)->setRowHeight(30);
    $sheet->getRowDimension(8)->setRowHeight(52);
    $sheet->getRowDimension(9)->setRowHeight(39);
    */

$sheet->getStyle('A1:U1')->applyFromArray($styleHeader);
$sheet->getStyle('A6:U7')->applyFromArray($styleHeader);

if (count($res) > 0) {
    $sheet->getStyle('A6:U' . ((count($res) * 3) + 7))->applyFromArray($styleBorder);
    $cellAwal = 8;
    $row = 0;
    $nom = 0;
    $total1 = 0;
    $total2 = 0;
    $total3 = 0;
    $total4 = 0;
    foreach ($res as $data) {
        $nom++;
        $id_poc_sc[] = $data['id_poc'];
        $idp    = $data['id_prd'];
        $tempal = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
        $alamat = $data['alamat_survey'] . " " . ucwords($tempal) . " " . $data['nama_prov'];

        $pbbkbT = ($data['nilai_pbbkb'] / 100) + 1.11;
        $oildus = $data['harga_poc'] / $pbbkbT * 0.003;
        $pbbkbN = $data['harga_poc'] / $pbbkbT * ($data['nilai_pbbkb'] / 100);
        $tmphrg = $data['refund_tawar'] + $oildus + $data['transport'] + $pbbkbN + $data['other_cost'];
        $nethrg = $data['harga_poc'] - $tmphrg;
        $volume = $data['volume'];
        $netgnl = ($nethrg - $data['harga_normal']) * $volume;
        $netprt = ($nethrg - $data['pr_harga_beli']) * $volume;
        $total1 = $total1 + $volume;
        $total2 = $total2 + $data['vol_ket'];
        $total3 = $total3 + $netprt;
        $total4 = $total4 + $netgnl;

        $pathPt = $public_base_directory . '/files/uploaded_user/lampiran/' . $data['lampiran_poc'];
        $lampPt = $data['lampiran_poc_ori'];
        if ($data['lampiran_poc'] && file_exists($pathPt)) {
            $linkPt = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=2&ktg=POC_" . $data['id_poc'] . "_&file=" . $lampPt);
            $attach = '<a href="' . $linkPt . '"><i class="fa fa-file-alt" title="' . $lampPt . '"></i> PO Customer</a>';
        } else {
            $attach = '';
        }

        $level2 = isset($data['awal_level1']) ? $data['awal_level1'] + $data['awal_level2'] : 0;
        $loss1  = (isset($data['awal_nett']) && isset($data['gain_loss'])) ? '=' . $data['gain_loss'] . '-J' . $cellAwal : 0;

        $sheet->setCellValue('A' . $cellAwal, $nom);
        $sheet->mergeCells('A' . $cellAwal . ':A' . ($cellAwal + 2));
        // $sheet->getStyle('A'.$cellAwal)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('B' . $cellAwal, ($data['kode_pelanggan'] ? $data['kode_pelanggan'] . ' - ' : '') . $data['nama_customer'] . "\r\n" . $data['jenis_usaha'] . "\r\n" . $data['fullname']);
        $sheet->mergeCells('B' . $cellAwal . ':B' . ($cellAwal + 2));
        $sheet->setCellValue('C' . $cellAwal, $data['nama_area'] . "\r\n" . $alamat . "\r\n" . 'Wilayah OA : ' . $data['wilayah_angkut']);
        $sheet->mergeCells('C' . $cellAwal . ':C' . ($cellAwal + 2));
        $sheet->setCellValue('D' . $cellAwal, $data['nomor_poc'] . "\r\n" . $data['merk_dagang'] . "\r\n" . 'Tgl Kirim ' . tgl_indo($data['tanggal_kirim']));
        $sheet->mergeCells('D' . $cellAwal . ':D' . ($cellAwal + 2));
        $sheet->setCellValue('E' . $cellAwal, number_format($volume));
        $sheet->mergeCells('E' . $cellAwal . ':E' . ($cellAwal + 2));
        $sheet->setCellValue('F' . $cellAwal, $data['nilai_pbbkb'] . " %");
        $sheet->mergeCells('F' . $cellAwal . ':F' . ($cellAwal + 2));
        $sheet->setCellValue('G' . $cellAwal, (($data['nama_vendor']) ? $data['nama_vendor'] : ''));
        $sheet->mergeCells('G' . $cellAwal . ':G' . ($cellAwal + 2));
        $tmn1 = ($data['nama_terminal']) ? $data['nama_terminal'] : '';
        $tmn2 = ($data['tanki_terminal']) ? ' - ' . $data['tanki_terminal'] : '';
        $tmn3 = ($data['lokasi_terminal']) ? ', ' . $data['lokasi_terminal'] : '';
        $sheet->setCellValue('H' . $cellAwal, $tmn1 . $tmn2 . $tmn3);
        $sheet->mergeCells('H' . $cellAwal . ':H' . ($cellAwal + 2));
        $sheet->setCellValue('I' . $cellAwal, (($data['pr_harga_beli']) ? number_format($data['pr_harga_beli']) : '') . "\r\n" . 'Masa Berlaku' . "\r\n" . date("d/m/Y", strtotime($data['masa_awal'])) . ' - ' . date("d/m/Y", strtotime($data['masa_akhir'])));
        $sheet->mergeCells('I' . $cellAwal . ':I' . ($cellAwal + 2));
        $sheet->setCellValue('J' . ($cellAwal), number_format($data['harga_poc']));
        $sheet->mergeCells('J' . $cellAwal . ':J' . ($cellAwal + 2));
        $sheet->setCellValue('K' . ($cellAwal), number_format($data['transport']));
        $sheet->mergeCells('K' . $cellAwal . ':K' . ($cellAwal + 2));
        $sheet->setCellValue('L' . ($cellAwal), number_format($data['refund_tawar']));
        $sheet->mergeCells('L' . $cellAwal . ':L' . ($cellAwal + 2));
        $sheet->setCellValue('M' . ($cellAwal), number_format($oildus));
        $sheet->mergeCells('M' . $cellAwal . ':M' . ($cellAwal + 2));
        $sheet->setCellValue('N' . ($cellAwal), number_format($pbbkbN));
        $sheet->mergeCells('N' . $cellAwal . ':N' . ($cellAwal + 2));
        $sheet->setCellValue('O' . ($cellAwal), number_format($data['other_cost']));
        $sheet->mergeCells('O' . $cellAwal . ':O' . ($cellAwal + 2));
        $sheet->setCellValue('P' . ($cellAwal), number_format($nethrg));
        $sheet->mergeCells('P' . $cellAwal . ':P' . ($cellAwal + 2));
        $sheet->setCellValue('Q' . ($cellAwal), number_format($netprt));
        $sheet->mergeCells('Q' . $cellAwal . ':Q' . ($cellAwal + 2));
        $sheet->setCellValue('R' . ($cellAwal), number_format($data['harga_normal']));
        $sheet->mergeCells('R' . $cellAwal . ':R' . ($cellAwal + 2));
        $sheet->setCellValue('S' . ($cellAwal), number_format($netgnl));
        $sheet->mergeCells('S' . $cellAwal . ':S' . ($cellAwal + 2));
        $sheet->setCellValue('T' . ($cellAwal), $data['status_plan'] == 2 ? $data['catatan_reschedule'] : $data['status_jadwal']);
        $sheet->mergeCells('T' . $cellAwal . ':T' . ($cellAwal + 2));
        $sheet->setCellValue('U' . ($cellAwal), $data['nomor_lo_pr']);
        $sheet->mergeCells('U' . $cellAwal . ':U' . ($cellAwal + 2));

        $cellAwal++;
        $cellAwal = $cellAwal + 2;
    }
    // Total
    $sheet->setCellValue('A' . ((count($res) * 3) + 8), 'Total');
    $sheet->mergeCells('A' . ((count($res) * 3) + 8) . ':D' . ((count($res) * 3) + 8));
    $sheet->setCellValue('E' . ((count($res) * 3) + 8), number_format($total1));
    $sheet->setCellValue('F' . ((count($res) * 3) + 8), '');
    $sheet->mergeCells('F' . ((count($res) * 3) + 8) . ':P' . ((count($res) * 3) + 8));
    $sheet->setCellValue('Q' . ((count($res) * 3) + 8), number_format($total3));
    $sheet->setCellValue('R' . ((count($res) * 3) + 8), '');
    $sheet->setCellValue('S' . ((count($res) * 3) + 8), number_format($total4));
    $sheet->setCellValue('T' . ((count($res) * 3) + 8), '');
    $sheet->setCellValue('U' . ((count($res) * 3) + 8), '');
    $sheet->getStyle('A' . ((count($res) * 3) + 8) . ':U' . ((count($res) * 3) + 8))->applyFromArray($styleHeader);
    $sheet->getStyle('A' . ((count($res) * 3) + 8) . ':U' . ((count($res) * 3) + 8))->applyFromArray($styleBorder);
    // Notes
    $sheet->setCellValue('A' . ((count($res) * 3) + 10), 'Catatan BM');
    $sheet->setCellValue('A' . ((count($res) * 3) + 11), $res[0]['sm_summary'] . "\r\n" . $res[0]['sm_pic'] . " - " . date("d/m/Y H:i:s", strtotime($res[0]['sm_tanggal'])) . " WIB");
    $sheet->mergeCells('A' . ((count($res) * 3) + 11) . ':E' . ((count($res) * 3) + 12));
    $sheet->getStyle('A' . ((count($res) * 3) + 11) . ':E' . ((count($res) * 3) + 12))->applyFromArray($styleBorder);

    $sheet->setCellValue('A' . ((count($res) * 3) + 14), 'Catatan Purchasing');
    $sheet->setCellValue('A' . ((count($res) * 3) + 15), $res[0]['purchasing_summary'] . "\r\n" . $res[0]['purchasing_pic'] . " - " . date("d/m/Y H:i:s", strtotime($res[0]['purchasing_tanggal'])) . " WIB");
    $sheet->mergeCells('A' . ((count($res) * 3) + 15) . ':E' . ((count($res) * 3) + 16));
    $sheet->getStyle('A' . ((count($res) * 3) + 15) . ':E' . ((count($res) * 3) + 16))->applyFromArray($styleBorder);
}

$con->close();
$filename = "Purchase-Request-Detail-" . date('dmYHis') . ".xlsx";
// header('Content-disposition: attachment; filename="' . sanitize_filename($filename) . '"');
// header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
// header('Content-Transfer-Encoding: binary');
// header('Cache-Control: must-revalidate');
// header('Pragma: public');
$objWriter = PHPExcel_IOFactory::createWriter($obj, 'Excel2007');
ob_end_clean();
header('Content-type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="' . sanitize_filename($filename) . '"');
$objWriter->save('php://output');

exit();

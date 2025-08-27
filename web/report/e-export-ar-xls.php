<?php
    session_start();
    $privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
    $public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
    require_once ($public_base_directory."/libraries/helper/load.php");
    // require_once ($public_base_directory."/libraries/helper/excelgen/PHPExcel.php");
    require_once ($public_base_directory."/libraries/helper/class.xlsxwriter.php");
    load_helper("autoload");

    error_reporting(E_ALL ^ E_DEPRECATED);
    // error_reporting(E_ALL ^ (E_NOTICE | E_WARNING | E_DEPRECATED));

    $auth   = new MyOtentikasi();
    $con    = new Connection();

    $seswil = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);

    $data_ = array();
    $sql = "
        select 
            a.id_customer, 
            a.nama_customer, 
            a.kode_pelanggan, 
            a.credit_limit,
            a.top_payment,
            b.fullname,
            c.nama_cabang,
            '' as sc
        from 
            pro_customer a 
            join acl_user b on b.id_user = a.id_marketing
            join pro_master_cabang c on c.id_master = a.id_wilayah
        where 
            1=1
            and a.id_wilayah = ".$seswil."
        order by 
            a.kode_pelanggan asc
    ";
    $result = $con->getResult($sql);
    foreach($result as $data) {
        $data = (object) $data;
        $sql1 = "
            select 
                not_yet,
                ov_under_30,
                ov_under_60,
                ov_under_90,
                ov_up_90,
                reminding
            from
                pro_sales_confirmation
            where 
                id_customer = ".$data->id_customer."
            order by 
                id desc
        ";
        $result1 = $con->getRecord($sql1);
        $data->sc = $result1 ? (object) $result1 : null;
        $data_[] = $data;
    }
    $content = [];
    foreach($data_ as $i=>$row) {
        $content[] = array(
            ($i+1),
            ($row->kode_pelanggan?$row->kode_pelanggan:'--------'),
            $row->nama_customer,
            $row->nama_cabang,
            (int) $row->credit_limit,
            (int) ($row->sc->not_yet ?? null),
            (int) ($row->sc->ov_under_30 ?? null),
            (int) ($row->sc->ov_under_60 ?? null),
            (int) ($row->sc->ov_under_90 ?? null),
            (int) ($row->sc->ov_up_90 ?? null),
            (int) ($row->sc->reminding ?? null),
            (int) $row->top_payment,
            $row->fullname,
        );
    }
    
    $filename = "Data-AR-".date('dmYHis').'.xlsx';

    // header('Content-type: application/vnd-ms-excel');
    header('Content-Disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
    ob_end_clean();
    // header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header('Content-type: application/vnd.ms-excel');
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    $sheet  = 'Sheet1';
    $writer = new XLSXWriter();
    $header = array(
            'Data diexport dari SYOP', '', '', '', '',
            'Data dimasukan manual oleh admin finance (data ada di Accurate)', '', '', '', '', '', 
            'Data diexport dari SYOP ', ''
    );
    $writer->writeSheetRow($sheet, $header);
    $writer->newMergeCell($sheet, 'A1', 'E1');
    $writer->newMergeCell($sheet, 'F1', 'K1');
    $writer->newMergeCell($sheet, 'L1', 'M1');

    $header = array(
            'data ini jangan dirubah', '', '', '', '',
            'Hanya data ini yang boleh diisi oleh Finance', '', '', '', '', '', 
            'data ini jangan dirubah ', ''
    );
    $writer->writeSheetRow($sheet, $header);
    $writer->newMergeCell($sheet, 'A2', 'E2');
    $writer->newMergeCell($sheet, 'F2', 'K2');
    $writer->newMergeCell($sheet, 'L2', 'M2');

    $header = array(
        'No' => 'string',
        'Kode Customer' => 'string',
        'Nama Customer' => 'string',
        'Cabang (SESUAI CABANG)' => 'string',
        'Credit Limit' => 'string',
        'Not Yet' => 'string',
        'Overdue 1-30 Days' => 'string',
        'Overdue 31-60 Days' => 'string',
        'Overdue 61-90 Days' => 'string',
        'Overdue > 90 Days' => 'string',
        'Reminding' => 'string',
        'TOP' => 'string',
        'Marketing' => 'string'
    );
    $writer->writeSheetHeaderExt($sheet, $header);

    if (count($data_) > 0) {
        foreach ($content as $row) {
            $writer->writeSheetRow($sheet, $row);
        }
    } else {
        $writer->writeSheetRow($sheet, array('Data tidak ada'));
        $writer->newMergeCell($sheet, 'A4', 'M4');
    }

    $con->close();
    $writer->writeToStdOut();
    exit(0);

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

    $id_user  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);
    $sesrole  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
    $seswilayah  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
    $sesgroup  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']);

    $data_ = array();
    $sql = "
        select 
            a.*,
            b.fullname as user_name,
            b.id_role as user_role
        from 
            pro_database_lubricant_oil a 
            join acl_user b on b.id_user = a.created_by
            join pro_master_area c on c.id_master = b.id_wilayah
        where 
            1=1 
            and a.is_mom = 0
            and a.deleted_time is null
    ";

    if ($sesrole==6) 
        $sql .= " and b.id_group = ".$sesgroup;
    if ($sesrole==7) 
        $sql .= " and b.id_wilayah = ".$seswilayah;
    if ($sesrole==11)
        $sql .= " and b.id_user = ".$id_user;

    $result = $con->getResult($sql);
    foreach($result as $data) {
        $data = (object) $data;
        $data_[] = $data;
    }
    $content = [];
    foreach($data_ as $i=>$row) {
        $content[] = array(
            ($i+1),
            $row->nama_customer,
            $row->jenis_oil,
            $row->spesifikasi,
            number_format($row->konsumsi_volume),
            $row->konsumsi_unit,
            $row->kompetitor,
            number_format($row->harga_kompetitor),
            $row->top,
            $row->pic,
            $row->kontak_email,
            $row->kontak_phone,
            $row->keterangan,
            $row->user_name
        );
    }
    
    $filename = "Data-Database-Lubricant-Oil-".date('dmYHis').'.xlsx';

    // header('Content-type: application/vnd-ms-excel');
    header('Content-Disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
    ob_end_clean();
	header('Content-type: application/vnd.ms-excel');
    // header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    $sheet  = 'Sheet1';
    $writer = new XLSXWriter();
    $writer->writeSheetHeader($sheet, array('Data Database Lubricant Oil'=>'string'));
    $writer->newMergeCell($sheet, 'A1', 'N1');
    $writer->writeSheetHeaderExt($sheet, array(""=>"string"));

    $header = array(
        'No' => 'string',
        'Nama Customer' => 'string',
        'Jenis Oil' => 'string',
        'Spesifikasi' => 'string',
        'Konsumsi Volume' => 'string',
        'Konsumsi Unit' => 'string',
        'kompetitor' => 'string',
        'kompetitor Harga' => 'string',
        'TOP' => 'string',
        'PIC' => 'string',
        'Kontak Email' => 'string',
        'Kontak HP/Tlpn' => 'string',
        'Keterangan' => 'string',
        'Marketing' => 'string'
    );
    $writer->writeSheetHeaderExt($sheet, $header);

    if (count($data_) > 0) {
        foreach ($content as $row) {
            $writer->writeSheetRow($sheet, $row);
        }
    } else {
        $writer->writeSheetRow($sheet, array('Data tidak ada'));
        $writer->newMergeCell($sheet, 'A4', 'N4');
    }

    $con->close();
    $writer->writeToStdOut();
    exit(0);

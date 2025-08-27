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
    $id_wilayah  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
    $id_group  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']);
    $id_role  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);

    $data_ = array();
    $sql = "
        select 
            a.*,
            b.fullname as user_name,
            b.id_role as user_role
        from 
            pro_marketing_report a 
            join acl_user b on b.id_user = a.created_by
            join pro_master_area c on c.id_master = b.id_wilayah
        where 
            1=1 
            and a.deleted_time is null
    ";
    if ($id_role=='11' || $id_role=='17') {
        $sql .= " and b.id_user = ".$id_user;
    } else
    if ($id_role=='7') {
        $sql .= " and b.id_wilayah = ".$id_wilayah;
        $sql .= " and b.id_role = 11";
    } else
    if ($id_role=='6') {
        $sql .= " and b.id_group = ".$id_group;
    }
    $result = $con->getResult($sql);
    foreach($result as $data) {
        $data = (object) $data;
        $data_[] = $data;
    }
    $content = [];
    foreach($data_ as $i=>$row) {
        $user_name = $row->user_name;
        $status = '';
        if ($id_role==11) {
            $status = ($row->technical_support_status==0?'Waiting Verified':'Confirmed '.date('d/m/Y', strtotime($row->technical_support_date)));
        } else
        if ($id_role==7) {
            $status = ($row->technical_support_status==0?'Waiting Verified':'Confirmed '.date('d/m/Y', strtotime($row->technical_support_date)));
        } else
        if ($id_role==6) {
            $status = ($row->technical_support_status==0?'Waiting Verified':'Confirmed by Tech. Support');
        }
        $content[] = array(
            ($i+1),
            date('d/m/Y', strtotime($row->marketing_report_date)),
            $row->profile_customer_nama_customer,
            $row->profile_customer_alamat,
            $row->profile_customer_status,
            $row->marketing_activity_activity,
            $row->marketing_activity_purpose,
            $row->pic,
            $row->kontak_email,
            $row->kontak_phone,
            $user_name,
            $status
        );
    }
    
    $filename = "Data-Marketing-Report-".date('dmYHis').'.xlsx';

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
    $writer->writeSheetHeader($sheet, array('Data Marketing Report'=>'string'));
    $writer->newMergeCell($sheet, 'A1', 'L1');
    $writer->writeSheetHeaderExt($sheet, array(""=>"string"));

    $header = array(
        'No' => 'string',
        'Date' => 'string',
        'Nama Customer' => 'string',
        'Alamat Customer' => 'string',
        'Status Customer' => 'string',
        'Kegiatan Marketing' => 'string',
        'Tujuan/Hasil Marketing' => 'string',
        'PIC' => 'string',
        'Kontak Email' => 'string',
        'Kontak HP/Tlpn' => 'string',
        'Marketing' => 'string',
        'Status' => 'string'
    );
    $writer->writeSheetHeaderExt($sheet, $header);

    if (count($data_) > 0) {
        foreach ($content as $row) {
            $writer->writeSheetRow($sheet, $row);
        }
    } else {
        $writer->writeSheetRow($sheet, array('Data tidak ada'));
        $writer->newMergeCell($sheet, 'A4', 'L4');
    }

    $con->close();
    $writer->writeToStdOut();
    exit(0);

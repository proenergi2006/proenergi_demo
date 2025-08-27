<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$con    = new Connection();
	$flash	= new FlashAlerts;
    $oke    = true;

    $id_wilayah = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
    $id_user = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);
    
	$con->beginTransaction();
	$con->clearError();

    require '../../vendor/autoload.php';

    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($_FILES['xls_file']['tmp_name']);

    $sheetData = $spreadsheet->getActiveSheet()->toArray();

    $oke = true;
    $has_export = 0;
    for ($i=0; $i < count($sheetData); $i++) { 
        if ($sheetData[$i]['0']=='Form No.') {
            continue;
        } else 
        if ($sheetData[$i]['0']=='') {
            continue;
        }

        $form_no = empty($sheetData[$i]['0']) ? null : $sheetData[$i]['0'];

        $periode = date('Y-m-d');

        $sql1 = "
        select id 
        from pro_insentif_raw 
        where 1=1 
        and form_no = '".$form_no."' 
        and id_cabang = '".$id_wilayah."'
        and periode = '".date('Y-m-d')."'
        and deleted_time is null
        ";
        $res1 = $con->getRecord($sql1);
        if ($res1) {
            continue;
        }

        $recv_date = empty($sheetData[$i]['1']) ? null : $sheetData[$i]['1'];
        if ($recv_date) $recv_date = date('Y-m-d', strtotime($recv_date));
        $customer_name = empty($sheetData[$i]['2']) ? null : $sheetData[$i]['2'];
        $inv_no = empty($sheetData[$i]['3']) ? null : $sheetData[$i]['3'];
        $inv_date = empty($sheetData[$i]['4']) ? null : $sheetData[$i]['4'];
        if ($inv_date) $inv_date = date('Y-m-d', strtotime($inv_date));
        $quantity = empty($sheetData[$i]['5']) ? null : (int) $sheetData[$i]['5'];
        $harga_jual = empty($sheetData[$i]['6']) ? null : (int) $sheetData[$i]['6'];
        $recv_date_str = strtotime($recv_date);
        $inv_date_str = strtotime($inv_date);
        $datediff = $recv_date_str - $inv_date_str;
        // $lunas = ceil(abs($recv_date - $inv_date) / 86400);
        $lunas = round($datediff / (60 * 60 * 24));
        $jumlah_hari_lunas = $lunas;
        $dispensasi = 7;
        $jumlah_hari_dispensasi = $dispensasi;
        $netto = 0;
        if ($lunas>0) $netto = $lunas - $dispensasi;
        $jumlah_hari_netto = (int) $netto;

        $sql1 = "
            SELECT 
                a.POIN AS poin
            FROM
                pro_master_poin_insentif a
                JOIN pro_master_pl_insentif b ON b.TIER = a.TIER
            WHERE
                1=1
                AND a.RANGE_AWAL <= ".$jumlah_hari_netto."
                AND a.RANGE_AKHIR >= ".$jumlah_hari_netto."
                AND b.TGL_AWAL <= '".$periode."'
                AND b.TGL_AKHIR >= '".$periode."'
                AND b.HARGA_AWAL <= ".$harga_jual."
                AND b.HARGA_AKHIR >= ".$harga_jual."
        ";
        $res1 = $con->getRecord($sql1);

        $poin = $res1 ? $res1['poin'] : 0;

        $jumlah_hari_gol_inc = $poin;
        $incentive = $poin * $quantity;

        $id_marketing = null;

        $sql1 = "
        select distinct id_marketing 
        from pro_customer 
        where 1=1 
        and nama_customer = '".$customer_name."' 
        and id_wilayah = '".$id_wilayah."'
        ";
        $res1 = $con->getRecord($sql1);
        if (!$res1) continue;
        
        $id_marketing = $res1['id_marketing'];

        $sql1 = "
        insert into pro_insentif_raw(
            form_no,
            recv_date,
            customer_name,
            inv_no,
            inv_date,
            quantity,
            harga_jual,
            jumlah_hari_lunas,
            jumlah_hari_dispensasi,
            jumlah_hari_netto,
            jumlah_hari_gol_inc,
            incentive,
            created_time,
            id_cabang,
            id_marketing,
            periode,
            created_by,
            deleted_time
        ) values (
            '".$form_no."',
            '".$recv_date."',
            '".$customer_name."',
            '".$inv_no."',
            '".$inv_date."',
            ".$quantity.",
            ".$harga_jual.",
            ".$jumlah_hari_lunas.",
            ".$jumlah_hari_dispensasi.",
            ".$jumlah_hari_netto.",
            ".$jumlah_hari_gol_inc.",
            ".$incentive.",
            NOW(), 
            '".$id_wilayah."',
            '".$id_marketing."',
            '".$periode."',
            '".$id_user."',
            NULL
        )";
        $res1 = $con->setQuery($sql1);
        $oke  = $oke && !$con->hasError();
        $has_export ++;
    }

    if ($oke) {
        $con->commit();
        $con->close();
        if ($has_export>0) {
            $flash->add("success", 'Data berhasil di export', BASE_REFERER);
        } else 
            $flash->add("error", 'Tidak ada data yang di export, semua data duplikat', BASE_REFERER);
    } else{
        $con->rollBack();
        $con->clearError();
        $con->close();
        $flash->add("error", 'Data tidak berhasil di export', BASE_REFERER);
    }
?>
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
    
	$con->beginTransaction();
	$con->clearError();

    require '../../vendor/autoload.php';

    /* Old Function
    $filePath   = $_FILES['xls_file']['tmp_name'];
    try {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
        $spreadSheet = $reader->load($filePath);
        $dataAsAssocArray = $spreadSheet->getActiveSheet()->toArray();
    } catch (\Exception $exception){
        //exception occurs
    }
    // Trying the same thing with the predecessor library PHPExcel produces no error at all. 
    $oldReader = PHPExcel_IOFactory::createReaderForFile($filePath);
    $oldSpreadSheet = $oldReader->load($filePath);
    $dataAsAssocArray = $oldSpreadSheet->getActiveSheet()->toArray();
    */

    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($_FILES['xls_file']['tmp_name']);

    $sheetData = $spreadsheet->getActiveSheet()->toArray();

    for ($i = 3; $i < count($sheetData); $i++) {
        $kode_customer = empty($sheetData[$i]['1']) ? 0 : $sheetData[$i]['1'];
        $nama_customer = empty($sheetData[$i]['2']) ? 0 : $sheetData[$i]['2'];
        $cabang = empty($sheetData[$i]['3']) ? 0 : $sheetData[$i]['3'];
        $credit_limit = empty($sheetData[$i]['4']) ? 0 : $sheetData[$i]['4'];
        $not_yet = empty($sheetData[$i]['5']) ? 0 : $sheetData[$i]['5'];
        $ov_under_30 = empty($sheetData[$i]['6']) ? 0 : $sheetData[$i]['6'];
        $ov_under_60 = empty($sheetData[$i]['7']) ? 0 : $sheetData[$i]['7'];
        $ov_under_90 = empty($sheetData[$i]['8']) ? 0 : $sheetData[$i]['8'];
        $ov_up_90 = empty($sheetData[$i]['9']) ? 0 : $sheetData[$i]['9'];
        $reminding = empty($sheetData[$i]['10']) ? 0 : $sheetData[$i]['10'];
        $top = empty($sheetData[$i]['11']) ? 0 : $sheetData[$i]['11'];
        $marketing = empty($sheetData[$i]['12']) ? 0 : $sheetData[$i]['12'];

        $sql = 'SELECT id_customer, id_wilayah FROM pro_customer WHERE kode_pelanggan = "'.$kode_customer.'"';
        $customer = $con->getRecord($sql);
        if ($customer) {
            $id_customer = $customer ? $customer['id_customer'] : null;
            // $id_wilayah = isset($customer['id_wilayah']) ? $customer['id_wilayah'] : 0;

            $sql = 'SELECT id FROM pro_sales_confirmation WHERE id_customer = '.$id_customer.' ORDER BY id DESC';
            $sales_confirmation = $con->getRecord($sql);
            if ($sales_confirmation) {
                $sql = "
                    UPDATE pro_sales_confirmation SET 
                    not_yet = ".$not_yet.",
                    ov_under_30 = ".$ov_under_30.",
                    ov_under_60 = ".$ov_under_60.",
                    ov_under_90 = ".$ov_under_90.",
                    ov_up_90 = ".$ov_up_90.",
                    reminding = ".$reminding."
                    WHERE id = ".$sales_confirmation['id']."
                ";
                $con->setQuery($sql);
                $oke  = $oke && !$con->hasError();
            }
        }
    }

    if ($oke) {
        $con->commit();
        $con->close();
        $flash->add("success", 'Data berhasil di export', BASE_REFERER);
    } else{
        $con->rollBack();
        $con->clearError();
        $con->close();
        $flash->add("error", 'Data tidak berhasil di export', BASE_REFERER);
    }
?>
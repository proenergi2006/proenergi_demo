<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload", "pdfgen");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$idr 	= isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
	$printe = paramDecrypt($_SESSION["sinori".SESSIONID]["fullname"])." ".date("d/m/Y H:i:s")." WIB";

	$path_odometer_pergi = null;
    $path_odometer_pulang = null;
    $path_meeting_customer = null;
    $path_tambahan = null;
    $sql = "select * from pro_marketing_mom where deleted_time is null and id_marketing_mom=".$idr;
    $marketing_mom = $con->getRecord($sql);
    if ($marketing_mom && $marketing_mom['odometer_pergi'])
        $path_odometer_pergi = getenv('APP_HOST').getenv('APP_NAME').'/files/uploaded_user/lampiran/'.$marketing_mom['odometer_pergi'];
    if ($marketing_mom && $marketing_mom['odometer_pulang'])
        $path_odometer_pulang = getenv('APP_HOST').getenv('APP_NAME').'/files/uploaded_user/lampiran/'.$marketing_mom['odometer_pulang'];
    if ($marketing_mom && $marketing_mom['meeting_customer'])
        $path_meeting_customer = getenv('APP_HOST').getenv('APP_NAME').'/files/uploaded_user/lampiran/'.$marketing_mom['meeting_customer'];
    if ($marketing_mom && $marketing_mom['tambahan'])
        $path_tambahan = getenv('APP_HOST').getenv('APP_NAME').'/files/uploaded_user/lampiran/'.$marketing_mom['tambahan'];
    $sql1 = "select * from pro_marketing_mom_participant where deleted_time is null and id_marketing_mom=".$idr;
    $marketing_mom_participant = $con->getResult($sql1);
    $sql1 = "select * from pro_database_fuel where deleted_time is null and is_mom = 1 and id_marketing_mom=".$idr;
    $database_fuel = $con->getResult($sql1);
    $sql1 = "select * from pro_database_lubricant_oil where deleted_time is null and is_mom = 1 and id_marketing_mom=".$idr;
    $database_lubricant_oil = $con->getResult($sql1);
    // echo json_encode($marketing_mom); die();
	ob_start();
	require_once(realpath("./template/marketing-mom.php"));
	$content = ob_get_clean();
	ob_end_flush();
	$con->close();

	$mpdf = null;
	if (PHP_VERSION >= 5.6) {
		$mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
	} else
		$mpdf = new mPDF('c','A4',10,'arial',10,10,30,16,5,4); 
	$mpdf->AddPage('P');
	$mpdf->SetDisplayMode('fullpage');
	// $mpdf->SetWatermarkImage(BASE_IMAGE."/watermark-penawaran.png", 0.2, "P", array(0,0));
	$mpdf->showWatermarkImage = true;
	$mpdf->WriteHTML($content);
	$filename = "mom_".date('d-m-Y', strtotime($marketing_mom['date']));
	$mpdf->Output($filename.'_'.date('dmyHis').'.pdf', 'I');
	exit;
?>

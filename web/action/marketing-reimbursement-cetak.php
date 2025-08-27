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

	$sql = "select * from pro_marketing_reimbursement where deleted_time is null and id_marketing_reimbursement=".$idr;
    $marketing_reimbursement = $con->getRecord($sql);
    $sql1 = "select * from pro_marketing_reimbursement_item where deleted_time is null and id_marketing_reimbursement=".$idr;
    $marketing_reimbursement_item = $con->getResult($sql1);
    if (count($marketing_reimbursement_item)) {
    	$item = [];
    	foreach($marketing_reimbursement_item as $i => $row) {
    		$sql2 = "select * from pro_marketing_reimbursement_keterangan where deleted_time is null and id_marketing_reimbursement_item=".$row['id_marketing_reimbursement_item'];
            $marketing_reimbursement_keterangan = $con->getResult($sql2);
            if (!$marketing_reimbursement_keterangan) 
            	$marketing_reimbursement_keterangan = [];
            $item[$i]['id_marketing_reimbursement_item'] = $row['id_marketing_reimbursement_item'];
            $item[$i]['id_marketing_reimbursement'] = $row['id_marketing_reimbursement'];
            $item[$i]['item'] = $row['item'];
            $item[$i]['jumlah'] = $row['jumlah'];
            $item[$i]['keterangan'] = $marketing_reimbursement_keterangan;
    	}
    	$marketing_reimbursement['item'] = $item;
    }
	ob_start();
	require_once(realpath("./template/marketing-reimbursement.php"));
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
	$filename = "Reimbursement_".date('d-m-Y', strtotime($marketing_reimbursement['marketing_reimbursement_date']));
	$mpdf->Output($filename.'_'.date('dmyHis').'.pdf', 'I');
	exit;
?>

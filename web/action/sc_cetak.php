<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload", "pdfgen");

	$enk  	= decode($_SERVER['REQUEST_URI']);
	$con 	= new Connection();
	$printe = paramDecrypt($_SESSION["sinori".SESSIONID]["fullname"])." ".date("d/m/Y H:i:s")." WIB";
    $id 	= isset($enk["id"])?htmlspecialchars($enk["id"], ENT_QUOTES):'';
	// $idp 	= isset($enk["idp"])?htmlspecialchars($enk["idp"], ENT_QUOTES):'';
	// $idc 	= isset($enk["idc"])?htmlspecialchars($enk["idc"], ENT_QUOTES):'';
	$role 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	
	$cek = "select n.*, s.*, p.supply_date, p.nomor_poc, c.kode_pelanggan, c.nama_customer, c.credit_limit, c.tipe_bisnis, c.tipe_bisnis_lain, p.volume_poc, p.harga_poc, e.fullname as marketing, c.jenis_payment, c.top_payment, q.kode_barcode
			from pro_sales_confirmation n 
			left join pro_sales_confirmation_approval s on n.id = s.id_sales
			join pro_customer c on n.id_customer = c.id_customer 
			join acl_user e on e.id_user = c.id_marketing
			join pro_po_customer p on p.id_poc = n.id_poc 
			LEFT JOIN pro_master_cabang q ON n.id_wilayah = q.id_master
			where n.id = '".$id."'";
	$row = $con->getRecord($cek);
	// created by Alvin
	$barcod  = ($row['kode_barcode']?$row['kode_barcode']:'07');
	if (strlen($barcod)==1)
		$barcod = '0'.$barcod;
	$nomor_poc = explode('/', $row['nomor_poc'])[0];
	$barcod = $barcod."09".sprintf("%06d", $nomor_poc);
	
	$cek3 = "select *
			from pro_sales_colleteral
			where sales_id = '".$id."'";
	$row3 = $con->getResult($cek3);
	
	$arrTipeBisnis 		= array(1=>"Agriculture & Forestry / Horticulture", "Business & Information", "Construction/Utilities/Contracting", "Education", "Finance & Insurance", "Food & hospitally", "Gaming", "Health Services", "Motor Vehicle", $row['tipe_bisnis_lain'],"Natural Resources / Environmental","Personal Service","Manufacture");
	
	ob_start();
	require_once(realpath("./template/sc-cetak.php"));
	$content = ob_get_clean();
	ob_end_flush();
	$con->close();

	$mpdf = null;
	if (PHP_VERSION >= 5.6) {
		$mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
	} else
		$mpdf = new mPDF('c','A4'); 
	$mpdf->AddPage('P');
	$mpdf->SetDisplayMode('fullpage');
	$mpdf->WriteHTML($content);
	$filename = "Surat_Penawaran_".sanitize_filename($rsm['nama_customer']);
	$mpdf->Output($filename.'_'.date('dmyHis').'.pdf', 'I');
	exit;
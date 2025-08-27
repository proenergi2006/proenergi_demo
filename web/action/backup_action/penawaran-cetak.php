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
	$idk 	= htmlspecialchars($enk["idk"], ENT_QUOTES);
	$bhs 	= htmlspecialchars($enk["bhs"], ENT_QUOTES);

	$sql = "
		select
			a.*,
			b.nama_customer,
			b.alamat_customer,
			b.telp_customer,
			b.fax_customer,
			c.fullname,
			c.mobile_user,
			c.email_user,
			d.nama_cabang,
			e.jenis_produk,
			e.merk_dagang,
			f.nama_prov,
			g.nama_kab,
			h.fullname as picname,
			i.role_name,
			d.kode_barcode
		from
			pro_penawaran a
		join pro_customer b on
			a.id_customer = b.id_customer
		join acl_user c on
			b.id_marketing = c.id_user
		join pro_master_cabang d on
			a.id_cabang = d.id_master
		join pro_master_produk e on
			a.produk_tawar = e.id_master
		join pro_master_provinsi f on
			b.prov_customer = f.id_prov
		join pro_master_kabupaten g on
			b.kab_customer = g.id_kab
		left join acl_user h on
			a.pic_approval = h.id_user
		left join acl_role i on
			h.id_role = i.id_role
		where
			a.id_customer = '" . $idr . "' 
			and a.id_penawaran = '" . $idk . "'";
	
	$rsm = $con->getRecord($sql);
	$jabat 	= str_replace("Role ", "", $rsm['role_name']);
	$printe = paramDecrypt($_SESSION["sinori".SESSIONID]["fullname"])." ".date("d/m/Y H:i:s")." WIB";
	$barcod = $rsm['kode_barcode'].'01'.str_pad($rsm['id_penawaran'],6,'0',STR_PAD_LEFT);
	$arrTgl = array(1=>"I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
	$alamat = $rsm['alamat_customer']." ".str_replace(array("KABUPATEN ","KOTA "), array("",""), $rsm['nama_kab'])." ".$rsm['nama_prov'];
	$arrKondInd	= array(1=>"Setelah Invoice diterima", "Setelah pengiriman", "Setelah Loading");
	$arrKondEng = array(1=>"After Invoice Receive", "After Delivery", "After Loading");
	$jenis_net	= $rsm['jenis_net'];

	if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role'])=='11'){
		$nama_role = "Marketing";
	}else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role'])=='17'){
		$nama_role = "Key Account Executive";
	}else{
		$nama_role = "";
	}
	if($rsm['flag_approval'] == 1){

		ob_start();
		if($bhs=='ind'){
			require_once(realpath("./template/surat-penawaran.php"));
		}else{
			require_once(realpath("./template/surat-penawaran-eng.php"));
		}
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
		$filename = "Surat_Penawaran_".sanitize_filename($rsm['nama_customer']);
		$mpdf->Output($filename.'_'.date('dmyHis').'.pdf', 'I');
		exit;
	} else{
		$flash->add("warning", "Penawaran belum dapat dicetak", BASE_REFERER);
		$con->close();
	}
?>

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
	$act	= ($enk['act'] == "")?htmlspecialchars($_POST["act"], ENT_QUOTES):$enk['act'];
	$pic 	= paramDecrypt($_SESSION["sinori".SESSIONID]["fullname"]);
	$term 	= paramDecrypt($_SESSION["sinori".SESSIONID]["terminal"]);
	
	$button1 = htmlspecialchars($_POST["btnSbmt1"], ENT_QUOTES);	
	$button2 = htmlspecialchars($_POST["btnSbmt2"], ENT_QUOTES);	

	if(is_array_empty($_POST["cek"])){
		$con->close();
		$flash->add("error", "Data belum dipilih....", BASE_REFERER);
	} else{
		if($button1){
			$oke = true;
			$con->beginTransaction();
			$con->clearError();

			foreach($_POST["cek"] as $idx=>$val){
				$dt1 = htmlspecialchars($_POST["dt1"][$idx], ENT_QUOTES);
				$dt2 = htmlspecialchars($_POST["dt2"][$idx], ENT_QUOTES);
				$dt3 = htmlspecialchars($_POST["dt3"][$idx], ENT_QUOTES);
				if($dt1 && $dt2){
					$tTgl = explode("/", $dt1);
					$tJam = explode(":", $dt2);
					if(intval($tJam[0]) < 7){
						$malam 	= true;
						$daten 	= date("Y/m/d", mktime(0, 0, 0, $tTgl[1], $tTgl[0]-1, $tTgl[2]));
					} else{
						$malam 	= false;
						$daten 	= tgl_db($dt1);
					}
					$cek1 = "select id_master from pro_master_inventory_out where tanggal_inv = '".$daten."' and id_terminal = '".$term."'";
					$ada1 = $con->getOne($cek1);
					if($ada1){
						$cols = ($malam)?"out_malam = out_malam + ".$dt3 :"out_pagi = out_pagi + ".$dt3;
						$sql1 = "update pro_master_inventory_out set lastupdate_time = NOW(), lastupdate_ip = '".$_SERVER['REMOTE_ADDR']."', lastupdate_by = '".$pic."', ".$cols." where id_master = '".$ada1."'";
						$con->setQuery($sql1);
						$oke  = $oke && !$con->hasError();
					} else{
						$cols = ($malam)?"out_malam" :"out_pagi";
						$sql1 = "insert into pro_master_inventory_out(id_terminal, tanggal_inv, ".$cols.", created_time, created_ip, created_by) values ('".$term."', '".$daten."', '".$dt3."', NOW(), '".$_SERVER['REMOTE_ADDR']."', '".$pic."')";
						$con->setQuery($sql1);
						$oke  = $oke && !$con->hasError();
					}
					$sql2 = "update pro_po_ds_detail set is_loaded = 1, tanggal_loaded = '".tgl_db($dt1)."', jam_loaded = '".$dt2."' where id_dsd = '".$idx."'";
					$con->setQuery($sql2);
					$oke  = $oke && !$con->hasError();
				}
			}

			if($oke){
				$con->commit();
				$con->close();
				header("location: ".BASE_URL_CLIENT."/terminal-do.php");	
				exit();
			} else{
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
			}
		}
		
		else if($button2){
			$idnya = implode(",", array_keys($_POST["cek"]));
			$sql = "select a.*, i.nama_customer, e.alamat_survey, f.nama_prov, g.nama_kab, o.nama_terminal, b.no_spj, k.nomor_plat, 
					l.nama_sopir, b.volume_po, j.jenis_produk, j.merk_dagang, n.nama_transportir, n.nama_suplier 
					from pro_po_ds_detail a join pro_po_detail b on a.id_pod = b.id_pod 
					join pro_pr_detail c on a.id_prd = c.id_prd 
					join pro_po_customer_plan d on a.id_plan = d.id_plan 
					join pro_customer_lcr e on d.id_lcr = e.id_lcr
					join pro_master_provinsi f on e.prov_survey = f.id_prov 
					join pro_master_kabupaten g on e.kab_survey = g.id_kab
					join pro_po_customer h on d.id_poc = h.id_poc 
					join pro_customer i on h.id_customer = i.id_customer 
					join pro_master_produk j on h.produk_poc = j.id_master 
					join pro_master_transportir_mobil k on b.mobil_po = k.id_master 
					join pro_master_transportir_sopir l on b.sopir_po = l.id_master
					join pro_po m on a.id_po = m.id_po 
					join pro_master_transportir n on m.id_transportir = n.id_master 
					join pro_master_terminal o on b.terminal_po = o.id_master  
					where a.id_dsd in (".$idnya.")";
			$res = $con->getResult($sql);
		
			ob_start();
			require_once(realpath("./template/delivery-order-truck.php"));
			$content = ob_get_clean();
			ob_end_flush();
			$con->close();
			
			$mpdf = null;
			if (PHP_VERSION >= 5.6) {
				$mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
			} else
				$mpdf = new mPDF('c','A4',10,'arial',10,10,20,20,0,0); 
			$mpdf->SetDisplayMode('fullpage');
			$mpdf->WriteHTML($content);
			$filename = "DO_TRUCK_";
			$mpdf->Output($filename.'_'.date('dmyHis').'.pdf', 'D');
			exit;
		}
	}
?>

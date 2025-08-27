<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload", "htmlawed");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$act	= ($enk['act'] == "")?htmlspecialchars($_POST["act"], ENT_QUOTES):$enk['act'];
	$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);	
	$url 	= BASE_URL_CLIENT."/perbaikan-data-detail.php?".paramEncrypt("idr=".$idr);
	$pic	= paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname']);
	
	if(count($_POST['dt6']) > 0){
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		foreach($_POST['dt6'] as $idx=>$val){
			$dt1 = htmlspecialchars(str_replace(array(",","."),array("",""),$_POST['dt1'][$idx]), ENT_QUOTES);
			$dt2 = htmlspecialchars(str_replace(array(",","."),array("",""),$_POST['dt2'][$idx]), ENT_QUOTES);
			$dt3 = htmlspecialchars(str_replace(array(",","."),array("",""),$_POST['dt3'][$idx]), ENT_QUOTES);
			$dt5 = htmlspecialchars(str_replace(array(",","."),array("",""),$_POST['dt5'][$idx]), ENT_QUOTES);
			$harga 	= htmlspecialchars(str_replace(array(",","."),array("",""),$_POST['harga_beli'][$idx]), ENT_QUOTES);
			$vendor	= htmlspecialchars($_POST['vendor'][$idx], ENT_QUOTES);
			$depot 	= htmlspecialchars($_POST['depot'][$idx], ENT_QUOTES);
			$dt6 	= htmlspecialchars($_POST['dt6'][$idx], ENT_QUOTES);
			$sql1 = "update pro_pr_detail set pr_harga_beli = '".$harga."', pr_vendor = '".$vendor."', pr_terminal = '".$depot."', pr_ar_notyet = '".$dt1."', 
					 pr_ar_satu = '".$dt2."', pr_ar_dua = '".$dt3."', pr_kredit_limit = '".$dt5."', is_approved = '".$dt6."' where id_prd = '".$idx."'";
			$con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();				
			
			if(!$dt6){
				$sql2 = "delete from pro_po_ds_detail where id_prd = '".$idx."'";
				$con->setQuery($sql2);
				$oke  = $oke && !$con->hasError();				
	
				$sql3 = "delete from pro_po_ds_kapal where id_prd = '".$idx."'";
				$con->setQuery($sql3);
				$oke  = $oke && !$con->hasError();				
	
				$sql4 = "update pro_po_detail set pod_approved = 0, ongkos_po = 0, pod_rejected_ket = 'Dibatalkan purchasing Pro Energi' where id_prd = '".$idx."'";
				$con->setQuery($sql4);
				$oke  = $oke && !$con->hasError();				
	
				$cek1 = "select count(*) as jumlah, id_plan from pro_pr_detail where is_approved = 1 and id_plan = (select id_plan from pro_pr_detail where id_prd = '".$idx."') 
						 group by id_plan";
				$row1 = $con->getRecord($cek1);
				if(!$row1['jumlah']){
					$sql5 = "update pro_po_customer_plan set status_plan = 2  where id_plan = (select id_plan from pro_pr_detail where id_prd = '".$idx."')";
					$con->setQuery($sql5);
					$oke  = $oke && !$con->hasError();
				}
			}
		}
		
		$cek1 = "
			select a.id_pr, a.id_plan, a.jumlah, b.jumlah as jumlah_approved
			from(
				select count(*) as jumlah, id_plan, id_pr 
				from pro_pr_detail 
				where 1=1 and id_pr = '".$idr."' 
				group by id_pr, id_plan
			) a left join(
				select count(*) as jumlah, id_plan, id_pr 
				from pro_pr_detail 
				where is_approved = 1 and id_pr = '".$idr."' 
				group by id_pr, id_plan
			) b on a.id_pr = b.id_pr and a.id_plan = b.id_plan";
		$res1 = $con->getResult($cek1);
		if(count($res1) > 0){
			foreach($res1 as $data1){
				if(!$data1['jumlah_approved']){
					$sql5 = "update pro_po_customer_plan set status_plan = 2  where id_plan = '".$data1['id_plan']."'";
					$con->setQuery($sql5);
					$oke  = $oke && !$con->hasError();
				}
			}
		}

		if ($oke){
			$con->commit();
			$con->close();
			$flash->add("success", "Perbaikan berhasil dilakukan", $url);
		} else{
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
	} else{
		$con->close();
		$flash->add("warning", "Tidak ada data yang dapat diubah", $url);	
	}

?>

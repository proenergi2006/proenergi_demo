<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload", "mailgen");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$act	= ($enk['act'] == "")?htmlspecialchars($_POST["act"], ENT_QUOTES):$enk['act'];
	$url 	= BASE_URL_CLIENT."/verifikasi-oa.php";
	$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);	
	$sesrol = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);

	$summary 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["summary"], ENT_QUOTES));
	$ip_user 	= $_SERVER['REMOTE_ADDR'];
	$pic_user 	= paramDecrypt($_SESSION["sinori".SESSIONID]["fullname"]);
	$wilayah 	= htmlspecialchars($_POST["idw"], ENT_QUOTES);	
	$tanggal_ds = date("Y/m/d");
	
	
	if($sesrol == '16'){
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		foreach($_POST["cek"] as $idx=>$val){
			$cek 		= htmlspecialchars($_POST['cek'][$idx], ENT_QUOTES);
			$no_urut_po = htmlspecialchars($_POST['dt1'][$idx], ENT_QUOTES);
			$terminal 	= htmlspecialchars($_POST['dt2'][$idx], ENT_QUOTES);
			if($val == 1){
				$sql2 = "update pro_po_detail set oa_result_mgrlog = '".$val."', oa_pic_mgrlog = '".$pic_user."', oa_tanggal_mgrlog = NOW() where id_pod = '".$idx."'";
				$con->setQuery($sql2);
				$oke  = $oke && !$con->hasError();
			}
		}

		$sql2 = "update pro_po set ada_selisih = 1, catatan_selisih_mgrlog = '".$summary."', selisih_approved_mgrlog = NOW() where id_po = '".$idr."'";
		$con->setQuery($sql2);
		$oke  = $oke && !$con->hasError();

		if ($oke){
			$ems1 = "select email_user from acl_user where id_role = 3";
			$rms1 = $con->getResult($ems1);
			$mail = new PHPMailer;
			$mail->isSMTP();
			$mail->Host = 'smtp.gmail.com';
			$mail->Port = 465;
			$mail->SMTPSecure = 'ssl';
			$mail->SMTPAuth = true;
			$mail->SMTPKeepAlive = true;
			$mail->Username = USR_EMAIL_PROENERGI202389;
			$mail->Password = PWD_EMAIL_PROENERGI202389;
			
			$mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');
			foreach($rms1 as $datms){
				$mail->addAddress($datms['email_user']);
			}
			$mail->Subject = "Verifikasi Selisih PO Transportir  [".date('d/m/Y H:i:s')."]";
			$mail->msgHTML(paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])." telah melakukan verifikasi selisih PO Transportir ".$rowA['nomor_po']."<p>".BASE_SERVER."</p>");
			//$mail->send();
	
			$con->commit();
			$con->close();
			header("location: ".$url);	
			exit();
		} else{
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
	} 
	
	else if($sesrol == '3'){
		$oke = true;
		$con->beginTransaction();
		$con->clearError();
	
		$arrTerima 	= array();
		$arrTolak 	= array();
		$arrDepot 	= array();
	
		$cekA = "select id_pr, nomor_po from pro_po where id_po = '".$idr."'";
		$rowA = $con->getRecord($cekA);
	
		foreach($_POST["cek"] as $idx=>$val){
			$cek 		= htmlspecialchars($_POST['cek'][$idx], ENT_QUOTES);
			$no_urut_po = htmlspecialchars($_POST['dt1'][$idx], ENT_QUOTES);
			$terminal 	= htmlspecialchars($_POST['dt2'][$idx], ENT_QUOTES);
			if($val == 1){
				$arrTerima[$no_urut_po] = $idx;
				if(!array_key_exists($terminal, $arrDepo)) $arrDepo[$terminal] = array();
				if(!in_array($idx, $arrDepo[$terminal])) array_push($arrDepo[$terminal], $idx);
	
				$sql2 = "update pro_po_detail set oa_result = '1', oa_pic = '".$pic_user."', oa_tanggal = NOW() where id_pod = '".$idx."'";
				$con->setQuery($sql2);
				$oke  = $oke && !$con->hasError();
			}
			
			else if($val == 2){
				$sqlA = "delete from pro_po_ds_detail where id_pod = '".$idx."'";
				$con->setQuery($cekA);
				$oke  = $oke && !$con->hasError();
			
				$sqlB = "delete from pro_po_detail where id_pod = '".$idx."'";
				$con->setQuery($sqlB);
				$oke  = $oke && !$con->hasError();
			
				$sqlC = "update pro_pr set is_edited = 1 where id_pr = '".$rowA['id_pr']."'";
				$con->setQuery($sqlC);
				$oke  = $oke && !$con->hasError();
			}
		}
			
		$cekB = "select count(*) from pro_po_detail where id_po = '".$idr."'";
		$rowB = $con->getOne($cekB);
		if($rowB == 0){
			$arrTerima = array();
			$sqlD = "delete from pro_po where id_po = '".$idr."'";
			$con->setQuery($sqlD);
			$oke  = $oke && !$con->hasError();
		} else{
			$sql1 = "update pro_po set po_approved = 1, f_proses_selisih = 1, catatan_selisih = '".$summary."', selisih_approved = NOW() where id_po = '".$idr."'";
			$con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();
		}
	
		ksort($arrTerima, SORT_NUMERIC);
		if(count($arrTerima) > 0){
			$cek2 = "select inisial_cabang, urut_spj, urut_ds from pro_master_cabang where id_master = '".$wilayah."' for update";
			$row2 = $con->getRecord($cek2);
			$arrRomawi 	= array("1"=>"I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
			$tmp_ds 	= $row2['urut_ds'];
			$nspj 		= $row2['urut_spj'];
	
			foreach($arrDepo as $idx=>$val){
				$cek1 = "select id_ds from pro_po_ds where id_wilayah = '".$wilayah."' and id_terminal = '".$idx."' and tanggal_ds = '".$tanggal_ds."' and is_submitted = 0 
						 and is_loco = 0";
				$row1 = $con->getRecord($cek1);
				if($row1['id_ds']){
					foreach($val as $idy=>$nilai){
						$sql5 = "insert into pro_po_ds_detail(id_ds, id_pod, id_po, id_prd, id_pr, id_plan, id_poc, tanggal_loading, jam_loading) 
								 (select '".$row1['id_ds']."', a.id_pod, a.id_po, a.id_prd, b.id_pr, a.id_plan, c.id_poc, a.tgl_etl_po, a.jam_etl_po from pro_po_detail a 
								 join pro_pr_detail b on a.id_prd = b.id_prd join pro_po_customer_plan c on a.id_plan = c.id_plan where a.id_pod = '".$nilai."')";
						$con->setQuery($sql5);
						$oke  = $oke && !$con->hasError();
					}
				} else{
					$tmp_ds = $tmp_ds + 1;
					$nom_ds = str_pad($tmp_ds,4,'0',STR_PAD_LEFT).'/LOG/'.$row2['inisial_cabang'].'/'.$arrRomawi[intval(date("m"))].'/'.date("Y");
	
					$sql5 = "insert into pro_po_ds(id_wilayah, id_terminal, nomor_ds, tanggal_ds, created_time, created_ip, created_by) values ('".$wilayah."', '".$idx."', 
							'".$nom_ds."', '".$tanggal_ds."', NOW(), '".$ip_user."', '".$pic_user."')";
					$res2 = $con->setQuery($sql5);
					$oke  = $oke && !$con->hasError();
	
					foreach($val as $idy=>$nilai){
						$sql6 = "insert into pro_po_ds_detail(id_ds, id_pod, id_po, id_prd, id_pr, id_plan, id_poc, tanggal_loading, jam_loading) 
								 (select '".$res2."', a.id_pod, a.id_po, a.id_prd, b.id_pr, a.id_plan, c.id_poc, a.tgl_etl_po, a.jam_etl_po from pro_po_detail a 
								  join pro_pr_detail b on a.id_prd = b.id_prd join pro_po_customer_plan c on a.id_plan = c.id_plan where a.id_pod = '".$nilai."')";
						$con->setQuery($sql6);
						$oke  = $oke && !$con->hasError();
					}
				}
			}
		
			foreach($arrTerima as $id_pod){
				$nspj++;
				$sql7 = "update pro_po_detail set no_spj = '".$row2['inisial_cabang']."-".str_pad($nspj,6,'0',STR_PAD_LEFT)."' where id_pod = '".$id_pod."'";
				$con->setQuery($sql7);
				$oke  = $oke && !$con->hasError();
			}
			$sql8 = "update pro_master_cabang set urut_spj = '".$nspj."', urut_ds = '".$tmp_ds."' where id_master = '".$wilayah."'";
			$con->setQuery($sql8);
			$oke  = $oke && !$con->hasError();
		}
	
		if ($oke){
			$ems1 = "select email_user from acl_user where id_role = 9 and id_wilayah = '".$wilayah."'";
			$rms1 = $con->getResult($ems1);
			$mail = new PHPMailer;
			$mail->isSMTP();
			$mail->Host = 'smtp.gmail.com';
			$mail->Port = 465;
			$mail->SMTPSecure = 'ssl';
			$mail->SMTPAuth = true;
			$mail->SMTPKeepAlive = true;
			$mail->Username = USR_EMAIL_PROENERGI202389;
			$mail->Password = PWD_EMAIL_PROENERGI202389;
			
			$mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');
			foreach($rms1 as $datms){
				$mail->addAddress($datms['email_user']);
			}
			$mail->Subject = "Verifikasi Selisih PO Transportir  [".date('d/m/Y H:i:s')."]";
			$mail->msgHTML(paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])." telah melakukan verifikasi selisih PO Transportir ".$rowA['nomor_po']."<p>".BASE_SERVER."</p>");
			//$mail->send();
	
			$con->commit();
			$con->close();
			header("location: ".$url);	
			exit();
		} else{
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
	}
?>

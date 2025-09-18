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
	$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
	$idc 	= htmlspecialchars($_POST["idc"], ENT_QUOTES);
	$role 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	$ems1 	= "";

	$credit_limit 	= htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["credit_limit"]), ENT_QUOTES);
	$nama_customer 	= htmlspecialchars($_POST["getData0"], ENT_QUOTES);
	
	$arrFilesnya = array();


	$oke = true;
	$con->beginTransaction();
	$con->clearError();
	
	if($role == 15){}

	//Admin Finance
	else if($role == 10){
		$credit_limit = htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["credit_limit"]), ENT_QUOTES);
		$summary 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["finance_summary"], ENT_QUOTES));
		$approval	= htmlspecialchars($_POST["finance_result"], ENT_QUOTES);
		$dokumen	= htmlspecialchars($_POST["dok_lain"], ENT_QUOTES);
		$dokumen_lainnya = htmlspecialchars($_POST["dokumen_lainnya"], ENT_QUOTES);
		$pic		= paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname']);
		$arrData	= array();
		$arrDoku	= array();
		foreach($_POST['evaluation_number'] as $idx=>$val){
			$sert = htmlspecialchars($_POST["evaluation_number"][$idx], ENT_QUOTES);
			array_push($arrData, array("nomor"=>$sert));
		}
		if(isset($_POST['dokumen']) && count($_POST['dokumen']) > 0){
			foreach($_POST['dokumen'] as $idy=>$va2){
				$desc = htmlspecialchars($_POST["dokumen"][$idy], ENT_QUOTES);
				$arrDoku[] = $desc;
			}
			
			array_push($arrData, array("nomor"=>implode(",", $arrDoku)));
			array_push($arrData, $dokumen);
		}

		if(count($_POST["nama_file_kyc"]) > 0){ 
			$no_urut 	= 0;
			$folder 	= date("Ym");
			$pathnya 	= $public_base_directory.'/files/uploaded_user/lampiran/'.$folder;
			$arrdel 	= array();
			$arrimg 	= array();
			if(!file_exists($pathnya.'/')) mkdir($pathnya, 0777);
	
			$sqlget = "select a.finance_data_kyc from pro_customer_verification a where a.id_verification = ".$idr;
			$rsmget = $con->getRecord($sqlget);
			$rowget = json_decode($rsmget['finance_data_kyc'], true);
			$arrget = (is_array($rowget) && count($rowget) > 0) ? $rowget : array();
	
			foreach($arrget as $idx=>$val){
				if(!array_key_exists($idx, $_POST["nama_file_kyc"])){
					array_push($arrdel, $arrget[$idx]['filenya']);
					unset($arrget[$idx]);
				}
			}
	
			foreach($_POST["nama_file_kyc"] as $idx=>$val){
				$id_detail 	= $idx;
				$nama_file 	= htmlspecialchars($_POST["nama_file_kyc"][$idx], ENT_QUOTES);
	
				$filePhoto1 = htmlspecialchars($_FILES['attach_file_kyc']['name'][$idx], ENT_QUOTES);
				$sizePhoto1 = htmlspecialchars($_FILES['attach_file_kyc']['size'][$idx], ENT_QUOTES);
				$tempPhoto1 = htmlspecialchars($_FILES['attach_file_kyc']['tmp_name'][$idx], ENT_QUOTES);
				$tipePhoto1 = htmlspecialchars($_FILES['attach_file_kyc']['type'][$idx], ENT_QUOTES);
	
				if($filePhoto1){
					$fileExt 		= strtolower(pathinfo($filePhoto1 ,PATHINFO_EXTENSION));
					$fileName 		= $pathnya.'/filekycfinance_'.$idr.'_'.md5($idx.'_'.basename($filePhoto1, $fileExt)).'.'.$fileExt;
					$fileOriginName = sanitize_filename($filePhoto1);
					array_push($arrimg, array('tmp_name'=>$tempPhoto1, 'filepath'=>$fileName));
				} else{
					$fileName 		= $arrget[$idx]['filenya'];
					$fileOriginName = $arrget[$idx]['file_upload_ori'];
				}
				
				$arrget[$idx] = array("id_detail"=>$id_detail, "nama_file"=>$nama_file, "filenya"=>$fileName, "file_upload_ori"=>$fileOriginName);
			}
		}

		/**************************************************************************************************/

		$paramCredit 	= "";
		$jenis_payment	= htmlspecialchars($_POST["jenis_payment"], ENT_QUOTES);
		$top_payment	= htmlspecialchars($_POST["top_payment"], ENT_QUOTES);
		$jenis_net		= htmlspecialchars($_POST["jenis_net"], ENT_QUOTES);

		/**************************************************************************************************/

		if($approval){
			
			$cek1 = $con->getRecord("select logistik_result, legal_result from pro_customer_verification where id_verification = '".$idr."' for update");
			$disp = ($cek1['logistik_result'] != 0 ) ? 2 : 1;
			$ems1 = ($disp == 2) ? "select email_user from acl_user where id_role = 7 and id_wilayah = (select id_wilayah from pro_customer where id_customer = '".$idc."')" : "";

			$jenisnya 	= htmlspecialchars($_POST["jenis_datanya"], ENT_QUOTES);
			
			$sql1 = "update pro_customer_verification set finance_data = '".json_encode($arrData)."', jenis_datanya = '".$jenisnya."', finance_summary = '".$summary."', finance_result = '".$approval."', 
					 finance_tgl_proses = NOW(), finance_pic = '".$pic."', finance_data_kyc = '".json_encode($arrget)."', disposisi_result = '".$disp."' where id_verification = '".$idr."'";
			$con->setQuery($sql1);

			if(count($_FILES['dokumen_lainnya_file']['name']) > 0){
				$urut = 0;
				foreach($_FILES['dokumen_lainnya_file']['name'] as $idx=>$val){
					$fileAttach = htmlspecialchars($_FILES['dokumen_lainnya_file']['name'][$idx],ENT_QUOTES);
					$sizeAttach = htmlspecialchars($_FILES['dokumen_lainnya_file']['size'][$idx],ENT_QUOTES);
					$tempAttach = htmlspecialchars($_FILES['dokumen_lainnya_file']['tmp_name'][$idx],ENT_QUOTES);
					$extAttach 	= substr($fileAttach,strrpos($fileAttach,'.'));
					
					if($fileAttach){
						$filenamedb = sanitize_filename($fileAttach);
						$kol1 = 'dokumen_lainnya_file'.$idc."_".$filenamedb;	
						array_push($arrFilesnya, array("urutnya"=>$urut, "filenya"=>$kol1, "tempnya"=>$tempAttach)); 
					}
				}
			}

			if(count($arrFilesnya) > 0){
				$paramCredit .= ", dokumen_lainnya_file = '".$filenamedb."'";
			}
			if($jenis_payment == 'CREDIT'){
				$paramCredit .= ", top_payment = '".$top_payment."', jenis_net = '".$jenis_net."'";
			}
			$sql_payment_type = "
				update pro_customer set dokumen_lainnya = '".$dokumen_lainnya."', credit_limit = '".$credit_limit."', 
				jenis_payment = '".$jenis_payment."' ".$paramCredit.", nama_customer = '".$nama_customer."' 
				where id_customer = '".$idc."'
			";
			$con->setQuery($sql_payment_type);
			$oke  = $oke && !$con->hasError();
		} else{
			$jenisnya 	= htmlspecialchars($_POST["jenis_datanya"], ENT_QUOTES);

			$ems1 = "";
			$sql1 = "
				update pro_customer_verification set finance_data = '".json_encode($arrData)."', jenis_datanya = '".$jenisnya."', finance_summary = '".$summary."' 
				where id_verification = '".$idr."'
			";
			$con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();
		}
	}

	//Logistics
	else if($role == 9){
		$summary 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["logistik_summary"], ENT_QUOTES));
		$approval	= htmlspecialchars($_POST["logistik_result"], ENT_QUOTES);
		$lain1		= htmlspecialchars($_POST["a1"], ENT_QUOTES);
		$lain2		= htmlspecialchars($_POST["a2"], ENT_QUOTES);
		$lain3		= htmlspecialchars($_POST["a3"], ENT_QUOTES);
		$lain4		= htmlspecialchars($_POST["a4"], ENT_QUOTES);
		$lain5		= htmlspecialchars($_POST["a5"], ENT_QUOTES);
		$lain6		= htmlspecialchars($_POST["a6"], ENT_QUOTES);
		$pic		= paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname']);
		$arrData	= array();
		$arrData[] 	= array("nomor"=>str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["evaluation_number"][0], ENT_QUOTES)));
		$arrData[] 	= array("nomor"=>htmlspecialchars($_POST["evaluationA"], ENT_QUOTES), "lain"=>$lain1);
		$arrData[] 	= array("nomor"=>htmlspecialchars($_POST["evaluationB"], ENT_QUOTES), "lain"=>$lain2);
		$arrData[] 	= array("nomor"=>str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["evaluation_number"][1], ENT_QUOTES)));
		$arrData[] 	= array("nomor"=>htmlspecialchars($_POST["evaluationC"], ENT_QUOTES), "lain"=>$lain3);
		$arrData[] 	= array("nomor"=>htmlspecialchars($_POST["evaluationD"], ENT_QUOTES), "lain"=>$lain4);
		$arrData[] 	= array("nomor"=>htmlspecialchars($_POST["evaluationE"], ENT_QUOTES), "lain"=>$lain5);
		$arrData[] 	= array("nomor"=>htmlspecialchars($_POST["evaluationF"], ENT_QUOTES), "lain"=>$lain6);
		$arrData[] 	= array("nomor"=>str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["desc_condition"], ENT_QUOTES)));
		$arrData[] 	= array("nomor"=>str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["desc_stor_fac"], ENT_QUOTES)));

		if($approval){
			$cek1 = $con->getRecord("select finance_result, legal_result from pro_customer_verification where id_verification = '".$idr."' for update");
			$disp = ($cek1['finance_result'] != 0 ) ? 2 : 1;
			$ems1 = ($disp == 2) ? "select email_user from acl_user where id_role = 7 and id_wilayah = (select id_wilayah from pro_customer where id_customer = '".$idc."')" : "";
			
			$sql1 = "
				update pro_customer_verification set logistik_data = '".json_encode($arrData)."', logistik_summary = '".$summary."', logistik_result = '".$approval."', 
				logistik_tgl_proses = NOW(), logistik_pic = '".$pic."', disposisi_result = '".$disp."' 
				where id_verification = '".$idr."'
			";
			$con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();
		} else{
			$ems1 = "";
			$sql1 = "update pro_customer_verification set logistik_data = '".json_encode($arrData)."', logistik_summary = '".$summary."' where id_verification = '".$idr."'";
			$con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();
		}
	}

	//Branch Manager
	else if($role == 7){
		$summary 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["sm_summary"], ENT_QUOTES));
		$approval	= htmlspecialchars($_POST["sm_result"], ENT_QUOTES);
		$pic		= paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname']);

		if($approval == '1'){
			$disp = 3;
			$cek1 = "select a.id_group, a.id_marketing, b.id_role, b.id_om from pro_customer a join acl_user b on a.id_marketing = b.id_user where a.id_customer = '".$idc."'";
			$row1 = $con->getRecord($cek1);
			if($row1['id_role'] == 11)
				$ems1 = "select email_user from acl_user where id_role = 6 and id_group = '".$row1['id_group']."'";
			else if($row1['id_role'] == 17)
				$ems1 = "select email_user from acl_user where id_user = '".$row1['id_om']."'";

			$sql1 = "update pro_customer_verification set sm_pic = '".$pic."', sm_summary = '".$summary."', sm_result = '".$approval."', sm_tgl_proses = NOW(), 
					 disposisi_result = '".$disp."' where id_verification = '".$idr."'";
			$con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();

		}else{
			$ems1 = "";
			$sql1 = "update pro_customer_verification set sm_summary = '".$summary."', disposisi_result = '0' where id_verification = '".$idr."'";
			$con->setQuery($sql1);
			$oke = $oke && !$con->hasError();
			$oke = approve_this($idr, $idc, $role, $approval, $con, $oke);
		}
	}

	//Operation Manager
	else if($role == 6){
		$summary 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["om_summary"], ENT_QUOTES));
		$approval	= htmlspecialchars($_POST["om_result"], ENT_QUOTES);
		$pic		= paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname']);

		if($approval == '1'){
			$ems1 = "";
			$sql1 = "update pro_customer_verification set om_pic = '".$pic."', om_summary = '".$summary."', om_result = '".$approval."', om_tgl_proses = NOW(), 
					 is_approved = '".$approval."', tanggal_approved = NOW(), role_approve = '".$role."', is_active = 0 where id_verification = '".$idr."'";
			$con->setQuery($sql1);
			$oke = $oke && !$con->hasError();

			$sql2 = "update pro_customer set is_verified = 1, status_customer = 2, prospect_customer_date = NOW() 
					where id_customer = '".$idc."'";
			$con->setQuery($sql2);
			$oke  = $oke && !$con->hasError();
		} else{
			$ems1 = "";
			$sql1 = "update pro_customer_verification set om_summary = '".$summary."', disposisi_result = '0' where id_verification = '".$idr."'";
			$con->setQuery($sql1);
			$oke = $oke && !$con->hasError();
			$oke = approve_this($idr, $idc, $role, $approval, $con, $oke);
		}
	}

	else if($role == 4){}
	else if($role == 3){}

	if ($oke){
		if(count($arrFilesnya) > 0){
			$mantab = true;
			foreach($arrFilesnya as $idx=>$data){
				$urutnya = $data['urutnya'];
				$filenya = $data['filenya'];
				$tempnya = $data['tempnya'];
				$tmpPot = glob($pathfile."/dokumen_lainnya_file".$idc."_*.{jpg,jpeg,gif,png,pdf,zip,rar}", GLOB_BRACE);
				if(count($tmpPot) > 0){
					foreach($tmpPot as $datj)
						if(file_exists($datj)) unlink($datj);
				}
				$file_path	= $public_base_directory."/files/uploaded_user/images";
				$tujuan  	= $file_path."/".$filenya;
				$mantab  	= $mantab && move_uploaded_file($tempnya, $tujuan);
				if(file_exists($tempnya)) unlink($tempnya);
			}
		}

		if($ems1){
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
			$mail->Subject = "Verifikasi data customer [".date('d/m/Y H:i:s')."]";
			$mail->msgHTML(paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])." meminta anda untuk melakukan verifikasi data customer <p>".BASE_SERVER."</p>");
			$mail->send();
		}

		if($role==6){
			$queryget_customer = "SELECT a.*, b.nama_prov, c.nama_kab, d.nama_cabang, d.inisial_cabang 
							FROM pro_customer a 
							JOIN pro_master_provinsi b ON a.prov_customer= b.id_prov 
							JOIN pro_master_kabupaten c ON a.kab_customer = c.id_kab
							JOIN pro_master_cabang d ON a.id_wilayah=d.id_master
							WHERE a.id_customer = '" . $idc . "'";
			$rowget_customer = $con->getRecord($queryget_customer);

			$npwp=str_replace(array(",",".","-"),"",$rowget_customer['nomor_npwp']);

			$queryget_lcr = "SELECT a.alamat_survey, b.nama_prov, c.nama_kab 
							FROM pro_customer_lcr a 
							JOIN pro_master_provinsi b ON a.prov_survey= b.id_prov 
							JOIN pro_master_kabupaten c ON a.kab_survey = c.id_kab
							WHERE a.id_customer ='" . $idc . "'";
			$rowget_lcr = $con->getResult($queryget_lcr);

			$query_kode = "SELECT COUNT(*) FROM pro_customer WHERE kode_pelanggan LIKE '%TEMP%' ORDER BY kode_pelanggan";
			$getkode = $con->getOne($query_kode);
			$kode=$getkode+1;

			$urlnya = 'https://zeus.accurate.id/accurate/api/customer/save.do';
			// Data yang akan dikirim dalam format JSON
			$data = array(
				'name'         				=> $rowget_customer['nama_customer'],
				'customerNo'        		=> 'TEMP'.$kode,
				'transDate'        			=> date('d/m/Y'),
				'billCity'          		=> $rowget_customer['nama_kab'],
				'billCountry'       		=> 'Indonesia',
				'billProvince'  			=> $rowget_customer['nama_prov'],
				'billStreet'    			=> $rowget_customer['alamat_customer'],
				'billZipCode'    			=> $rowget_customer['postalcode_customer'],
				'branchName'    			=> $rowget_customer['nama_cabang'],
				'npwpNo'      				=> $npwp,
				'workPhone' 				=> $rowget_customer['telp_customer'],
				'email'		 				=> $rowget_customer['email_customer'],
				'fax' 						=> $rowget_customer['fax_customer'],
				'customerLimitAge' 			=> true,
				'customerLimitAgeValue'		=> $rowget_customer['top_payment'],
				'customerLimitAmount'		=> true,
				'customerLimitAmountValue'	=> $rowget_customer['credit_limit'],
				'shipSameAsBill'			=> true,
				'taxSameAsBill'				=> true,
				'termName'					=> 'net '.$rowget_customer['top_payment'],
				'detailShipAddress'			=>[],
			);

			foreach ($rowget_lcr as $lcr) {
				$dataShip = [
					'street'    => $lcr['alamat_survey'],
					'city'   	=> $lcr['nama_kab'],
					'country'  	=> 'Indonesia',
					'province'  => $lcr['nama_prov'],
				];


				$data['detailShipAddress'][] = $dataShip;
			}

			// Mengonversi data menjadi format JSON
			$jsonData = json_encode($data);
			$result = curl_post($urlnya, $jsonData);
			if ($result['s'] == true) {
				$sql2 = "update pro_customer set id_accurate = '" . $result['r']['id'] . "', kode_pelanggan ='TEMP".$kode."' 
						where id_customer = '".$idc."'";
				$con->setQuery($sql2);
				$oke  = $oke && !$con->hasError();

				$con->commit();
				$con->close();
				header("location: " . BASE_URL_CLIENT . "/verifikasi-data-customer.php");
				exit();
			} else {
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", $result_close["d"][0] . " - Response dari Accurate", BASE_REFERER);
			}
		}else{
			$con->commit();
			$con->close();
		}

		if(count($arrimg) > 0){
			foreach($arrimg as $data){
				 $tujuan  = $data['filepath'];
				 $mantab  = move_uploaded_file($data['tmp_name'], $tujuan);
				 if(file_exists($data['tmp_name'])) unlink($data['tmp_name']);
			}
		}

		if(count($arrdel) > 0){
			foreach($arrdel as $data){
				if($data && file_exists($data)) unlink($data);
			}
		}

		if($role == 10){
			$jenisnya 	= htmlspecialchars($_POST["jenis_datanya"], ENT_QUOTES);
			if($jenisnya == '1'){
				$flash->add("success", "SUKSES_MASUK", BASE_URL_CLIENT."/verifikasi-data-customer-detail.php?".paramEncrypt('idr='.$idr));
			} else{
				header("location: ".BASE_URL_CLIENT."/verifikasi-data-customer.php");	
				exit();
			}
		}else{
			header("location: ".BASE_URL_CLIENT."/verifikasi-data-customer.php");	
			exit();
		}
	} else{
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}

	function approve_this($idr, $idc, $role, $approval, $con, $oke){
		$sql1 = "update pro_customer_verification set is_approved = '".$approval."', role_approve = '".$role."', tanggal_approved = NOW(), is_active = 0 where id_verification = '".$idr."'";
		$con->setQuery($sql1);
		$oke = $oke && !$con->hasError();

		if($approval == 1){
			$cek1 = "select legal_data, logistik_data from pro_customer_verification where id_verification = '".$idr."'";
			$row1 = $con->getRecord($cek1);
			$arr1 = json_decode($row1['legal_data'], true);
			$arr2 = json_decode($row1['logistik_data'], true);

			$sql2 = "update pro_customer set id_verification='".$idr."', nomor_sertifikat = '".$arr1[0]['nomor']."', nomor_npwp = '".$arr1[1]['nomor']."', 
					nomor_siup = '".$arr1[2]['nomor']."', nomor_tdp = '".$arr1[3]['nomor']."', is_verified = 1, status_customer = 2, prospect_customer_date = NOW() 
					where id_customer = '".$idc."'";

			$con->setQuery($sql2);
			
			$oke  = $oke && !$con->hasError();
			
			$sql3 = "update pro_customer_logistik set logistik_area = '".$arr2[0]['nomor']."', logistik_env = '".$arr2[1]['nomor']."', 
					logistik_env_other = '".$arr2[1]['lain']."', logistik_storage = '".$arr2[2]['nomor']."', logistik_storage_other = '".$arr2[2]['lain']."', 
					logistik_bisnis = '".$arr2[3]['nomor']."', logistik_hour = '".$arr2[4]['nomor']."', logistik_hour_other = '".$arr2[4]['lain']."', 
					logistik_volume = '".$arr2[5]['nomor']."', logistik_volume_other = '".$arr2[5]['lain']."', logistik_quality = '".$arr2[6]['nomor']."', 
					logistik_quality_other = '".$arr2[6]['lain']."', logistik_truck = '".$arr2[7]['nomor']."', logistik_truck_other = '".$arr2[7]['lain']."', 
					desc_condition = '".$arr2[8]['nomor']."', desc_stor_fac = '".$arr2[9]['nomor']."' where id_customer = '".$idc."'";
			
			$con->setQuery($sql3);

			$oke  = $oke && !$con->hasError();				
		} else{
			$cek1 = $con->getRecord("select id_marketing, nama_customer from pro_customer where id_customer = '".$idc."' ");
			$cek = ($cek1)?$con->getRecord("select email_user from acl_user where id_user = '".$cek1['id_marketing']."' "):"";
			
			if($cek){
				send_email(
					$cek['email_user'],
					"Penolakan Data Customer ".date('d/m/Y'),
					paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])." telah menolak verifikasi data customer ".$cek1['nama_customer']
				);
			}
		}

		return $oke;
	}

	function send_email($email, $subject, $message){
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
		$mail->addAddress($email);

		$mail->Subject = $subject;
		$mail->msgHTML($message);
		$mail->send();
	}
?>

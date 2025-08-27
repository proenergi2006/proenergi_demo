<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload", "htmlawed", "mailgen");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$act	= ($enk['act'] == "")?htmlspecialchars($_POST["act"], ENT_QUOTES):$enk['act'];
	$idr	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
	$idk	= htmlspecialchars($_POST["idk"], ENT_QUOTES);

	$dt1 		= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["dt1"], ENT_QUOTES));
	$dt2 		= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["dt2"], ENT_QUOTES));
	$dt3 		= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["dt3"], ENT_QUOTES));
	$summary 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["summary"], ENT_QUOTES));
	$result 	= htmlspecialchars($_POST["result"], ENT_QUOTES);
	$extend 	= htmlspecialchars($_POST["extend"], ENT_QUOTES);
	$tanggal 	= date("d/m/Y H:i:s");
	$pic		= paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname']);
	$wilayah	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
	$group		= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']);
	$role 		= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);

	if($role == "11"){
		$url = BASE_URL_CLIENT."/customer-evaluasi.php";
		if($dt1 == "" && $dt2 == "" && $dt3 == "" && $summary == "" && $result == ""){
			$con->close();
			$flash->add("error", "KOSONG", BASE_REFERER);
		} else{
			$oke = true;
			$con->beginTransaction();
			$con->clearError();
			if($act == "add"){
				$prospek = $con->getOne("select prospect_customer_date from pro_customer where id_customer = '".$idr."'");
				if($result){
					$sql1 = "insert into pro_customer_evaluasi(id_customer, prospek_tanggal, marketing_evaluasi1, marketing_evaluasi2, marketing_evaluasi3, marketing_result, marketing_summary, disposisi_result) values ('".$idr."', '".$prospek."', '".$dt1."', '".$dt2."', '".$dt3."', '".$result."', '".json_encode(array("summary"=>$summary, "pic"=>$pic, "tanggal"=>$tanggal))."', 1)";
					$con->setQuery($sql1);
					$oke = $oke && !$con->hasError();

					$ems1 = "select email_user from acl_user where id_role in (9,10) and id_wilayah = (select id_wilayah from pro_customer where id_customer = '".$idr."')";
				} else{
					$sql1 = "insert into pro_customer_evaluasi(id_customer, prospek_tanggal, marketing_evaluasi1, marketing_evaluasi2, marketing_evaluasi3, marketing_summary) values ('".$idr."', '".$prospek."', '".$dt1."', '".$dt2."', '".$dt3."', '".json_encode(array("summary"=>$summary))."')";
					$con->setQuery($sql1);
					$oke = $oke && !$con->hasError();
				}

				$sql2 = "update pro_customer set prospect_evaluated = 1 where id_customer = '".$idr."'";
				$con->setQuery($sql2);
				$oke = $oke && !$con->hasError();
				$msg = "GAGAL_MASUK";
			} else if($act == "update"){
				if($result)
					$sql1 = "update pro_customer_evaluasi set marketing_evaluasi1 = '".$dt1."', marketing_evaluasi2 = '".$dt2."', marketing_evaluasi3 = '".$dt3."', marketing_result = '".$result."', marketing_summary = '".json_encode(array("summary"=>$summary, "pic"=>$pic, "tanggal"=>$tanggal))."', disposisi_result = 1 where id_customer = '".$idr."' and id_evaluasi = '".$idk."'";
				else
					$sql1 = "update pro_customer_evaluasi set marketing_evaluasi1 = '".$dt1."', marketing_evaluasi2 = '".$dt2."', marketing_evaluasi3 = '".$dt3."', marketing_summary = '".json_encode(array("summary"=>$summary))."' where id_customer = '".$idr."' and id_evaluasi = '".$idk."'";
				$con->setQuery($sql1);
				$oke = $oke && !$con->hasError();
				$msg = "GAGAL_UBAH";
			}
		}
	}

	else if($role == "10"){
		$url = BASE_URL_CLIENT."/evaluasi-data-customer.php";
		if($dt1 == "" && $dt2 == "" && $dt3 == "" && $summary == "" && $result == ""){
			$con->close();
			$flash->add("error", "KOSONG", BASE_REFERER);
		} else{
			$oke = true;
			$con->beginTransaction();
			$con->clearError();

			$cek = $con->getOne("select logistik_result from pro_customer_evaluasi where id_customer = '".$idr."' and id_evaluasi = '".$idk."' for update");
			$dis = ($cek)?2:1;
			$msg = "GAGAL_UBAH";
			if($result){
				$ems1 = ($dis == 2)?"select email_user from acl_user where id_role = 7 and id_wilayah = (select id_wilayah from pro_customer where id_customer = '".$idr."')":"";
				$sql1 = "update pro_customer_evaluasi set finance_evaluasi1 = '".$dt1."', finance_evaluasi2 = '".$dt2."', finance_evaluasi3 = '".$dt3."', finance_result = '".$result."', finance_summary = '".json_encode(array("summary"=>$summary, "pic"=>$pic, "tanggal"=>$tanggal))."', disposisi_result = '".$dis."' where id_customer = '".$idr."' and id_evaluasi = '".$idk."'";
				$con->setQuery($sql1);
				$oke = $oke && !$con->hasError();
			} else{
				$ems1 = "";
				$sql1 = "update pro_customer_evaluasi set finance_evaluasi1 = '".$dt1."', finance_evaluasi2 = '".$dt2."', finance_evaluasi3 = '".$dt3."', finance_summary = '".json_encode(array("summary"=>$summary))."' where id_customer = '".$idr."' and id_evaluasi = '".$idk."'";
				$con->setQuery($sql1);
				$oke = $oke && !$con->hasError();
			}
		}
	}

	else if($role == "9"){
		$url = BASE_URL_CLIENT."/evaluasi-data-customer.php";
		if($result && ($dt1 == "" || $dt2 == "" || $dt3 == "" || $summary == "")){
			$con->close();
			$flash->add("error", "KOSONG", BASE_REFERER);
		} else{
			$oke = true;
			$con->beginTransaction();
			$con->clearError();

			$cek = $con->getOne("select finance_result from pro_customer_evaluasi where id_customer = '".$idr."' and id_evaluasi = '".$idk."' for update");
			$dis = ($cek)?2:1;
			$msg = "GAGAL_UBAH";
			if($result){
				$ems1 = ($dis == 2)?"select email_user from acl_user where id_role = 7 and id_wilayah = (select id_wilayah from pro_customer where id_customer = '".$idr."')":"";
				$sql1 = "update pro_customer_evaluasi set logistik_evaluasi1 = '".$dt1."', logistik_evaluasi2 = '".$dt2."', logistik_evaluasi3 = '".$dt3."', logistik_result = '".$result."', logistik_summary = '".json_encode(array("summary"=>$summary, "pic"=>$pic, "tanggal"=>$tanggal))."', disposisi_result = '".$dis."' where id_customer = '".$idr."' and id_evaluasi = '".$idk."'";
				$con->setQuery($sql1);
				$oke = $oke && !$con->hasError();
			} else{
				$ems1 = "";
				$sql1 = "update pro_customer_evaluasi set logistik_evaluasi1 = '".$dt1."', logistik_evaluasi2 = '".$dt2."', logistik_evaluasi3 = '".$dt3."', logistik_summary = '".json_encode(array("summary"=>$summary))."' where id_customer = '".$idr."' and id_evaluasi = '".$idk."'";
				$con->setQuery($sql1);
				$oke = $oke && !$con->hasError();
			}
		}
	}

	else if($role == "7"){
		$url = BASE_URL_CLIENT."/evaluasi-data-customer.php";
		if($result && $summary == ""){
			$con->close();
			$flash->add("error", "KOSONG", BASE_REFERER);
		} else{
			$oke = true;
			$con->beginTransaction();
			$con->clearError();

			$dis = 3;
			$msg = "GAGAL_UBAH";
			if($result){
				$ems1 = "select email_user from acl_user where id_role = 6 and id_group = (select id_group from pro_customer where id_customer = '".$idr."')";
				$sql1 = "update pro_customer_evaluasi set sm_result = '".$result."', sm_summary = '".json_encode(array("summary"=>$summary, "pic"=>$pic, "tanggal"=>$tanggal))."', disposisi_result = '".$dis."' where id_customer = '".$idr."' and id_evaluasi = '".$idk."'";
				$con->setQuery($sql1);
				$oke = $oke && !$con->hasError();				
			} else{
				$ems1 = "";
				$sql1 = "update pro_customer_evaluasi set sm_summary = '".json_encode(array("summary"=>$summary))."' where id_customer = '".$idr."' and id_evaluasi = '".$idk."'";
				$con->setQuery($sql1);
				$oke = $oke && !$con->hasError();
			}
		}
	}

	else if($role == "6"){
		$url = BASE_URL_CLIENT."/evaluasi-data-customer.php";
		if($result && ($summary == "" || $extend == "")){
			$con->close();
			$flash->add("error", "KOSONG", BASE_REFERER);
		} else{
			$oke = true;
			$con->beginTransaction();
			$con->clearError();

			$dis = 4;
			$msg = "GAGAL_UBAH";
			if($result){
				if($extend == 2){
					$ems1 = "";
					if($result == 1){
						$sql1 = "update pro_customer_evaluasi set om_result = '".$result."', 
								 om_summary = '".json_encode(array("summary"=>$summary, "pic"=>$pic, "tanggal"=>$tanggal))."', is_approved = '".$result."' 
								 where id_customer = '".$idr."' and id_evaluasi = '".$idk."'";
						$con->setQuery($sql1);
						$oke = $oke && !$con->hasError();

						$sql2 = "update pro_customer set prospect_evaluated = 0, status_customer = 3, fix_customer_since = NOW(), fix_customer_redate = NOW() 
								 where id_customer = '".$idr."'";
						$con->setQuery($sql2);
						$oke = $oke && !$con->hasError();
					} else if($result == 2){
						$sql1 = "update pro_customer_evaluasi set om_result = '".$result."', 
								 om_summary = '".json_encode(array("summary"=>$summary, "pic"=>$pic, "tanggal"=>$tanggal))."', is_approved = '".$result."' 
								 where id_customer = '".$idr."' and id_evaluasi = '".$idk."'";
						$con->setQuery($sql1);
						$oke = $oke && !$con->hasError();

						$sql2 = "update pro_customer set prospect_evaluated = 0, status_customer = 1 where id_customer = '".$idr."'";
						$con->setQuery($sql2);
						$oke = $oke && !$con->hasError();
					}
				} else{
					$ems1 = "select email_user from acl_user where id_role = 4";
					$sql1 = "update pro_customer_evaluasi set om_result = '".$result."', om_summary = '".json_encode(array("summary"=>$summary, "pic"=>$pic, "tanggal"=>$tanggal))."', disposisi_result = '".$dis."' where id_customer = '".$idr."' and id_evaluasi = '".$idk."'";
					$con->setQuery($sql1);
					$oke = $oke && !$con->hasError();
				}
			} else{
				$ems1 = "";
				$sql1 = "update pro_customer_evaluasi set om_summary = '".json_encode(array("summary"=>$summary))."' where id_customer = '".$idr."' and id_evaluasi = '".$idk."'";
				$con->setQuery($sql1);
				$oke = $oke && !$con->hasError();
			}
		}
	}

	else if($role == "4"){
		$url = BASE_URL_CLIENT."/evaluasi-data-customer.php";
		if($result && ($summary == "" || $extend == "")){
			$con->close();
			$flash->add("error", "KOSONG", BASE_REFERER);
		} else{
			$oke = true;
			$con->beginTransaction();
			$con->clearError();

			$dis = 5;
			$msg = "GAGAL_UBAH";
			if($result){
				if($extend == 2){
					$ems1 = "";
					if($result == 1){
						$sql1 = "update pro_customer_evaluasi set cfo_result = '".$result."', 
								 cfo_summary = '".json_encode(array("summary"=>$summary, "pic"=>$pic, "tanggal"=>$tanggal))."', is_approved = '".$result."' 
								 where id_customer = '".$idr."' and id_evaluasi = '".$idk."'";
						$con->setQuery($sql1);
						$oke = $oke && !$con->hasError();

						$sql2 = "update pro_customer set prospect_evaluated = 0, status_customer = 3, fix_customer_since = NOW(), fix_customer_redate = NOW() 
								 where id_customer = '".$idr."'";
						$con->setQuery($sql2);
						$oke = $oke && !$con->hasError();
					} else if($result == 2){
						$sql1 = "update pro_customer_evaluasi set cfo_result = '".$result."', 
								 cfo_summary = '".json_encode(array("summary"=>$summary, "pic"=>$pic, "tanggal"=>$tanggal))."', is_approved = '".$result."' 
								 where id_customer = '".$idr."' and id_evaluasi = '".$idk."'";
						$con->setQuery($sql1);
						$oke = $oke && !$con->hasError();

						$sql2 = "update pro_customer set prospect_evaluated = 0, status_customer = 1 where id_customer = '".$idr."'";
						$con->setQuery($sql2);
						$oke = $oke && !$con->hasError();
					}
				} else{
					$ems1 = "select email_user from acl_user where id_role = 3";
					$sql1 = "update pro_customer_evaluasi set cfo_result = '".$result."', 
							 cfo_summary = '".json_encode(array("summary"=>$summary, "pic"=>$pic, "tanggal"=>$tanggal))."', disposisi_result = '".$dis."' 
							 where id_customer = '".$idr."' and id_evaluasi = '".$idk."'";
					$con->setQuery($sql1);
					$oke = $oke && !$con->hasError();
				}
			} else{
				$ems1 = "";
				$sql1 = "update pro_customer_evaluasi set cfo_summary = '".json_encode(array("summary"=>$summary))."' where id_customer = '".$idr."' and id_evaluasi = '".$idk."'";
				$con->setQuery($sql1);
				$oke = $oke && !$con->hasError();
			}
		}
	}

	else if($role == "3"){
		$url = BASE_URL_CLIENT."/evaluasi-data-customer.php";
		if($result && $summary == ""){
			$con->close();
			$flash->add("error", "KOSONG", BASE_REFERER);
		} else{
			$oke = true;
			$con->beginTransaction();
			$con->clearError();

			$msg = "GAGAL_UBAH";
			if($result == 1){
				$sql1 = "update pro_customer_evaluasi set ceo_result = '".$result."', 
						 ceo_summary = '".json_encode(array("summary"=>$summary, "pic"=>$pic, "tanggal"=>$tanggal))."', is_approved = '".$result."' 
						 where id_customer = '".$idr."' and id_evaluasi = '".$idk."'";
				$con->setQuery($sql1);
				$oke = $oke && !$con->hasError();

				$sql2 = "update pro_customer set prospect_evaluated = 0, status_customer = 3, fix_customer_since = NOW(), fix_customer_redate = NOW() 
						 where id_customer = '".$idr."'";
				$con->setQuery($sql2);
				$oke = $oke && !$con->hasError();
			} else if($result == 2){
				$sql1 = "update pro_customer_evaluasi set ceo_result = '".$result."', 
						 ceo_summary = '".json_encode(array("summary"=>$summary, "pic"=>$pic, "tanggal"=>$tanggal))."', is_approved = '".$result."' 
						 where id_customer = '".$idr."' and id_evaluasi = '".$idk."'";
				$con->setQuery($sql1);
				$oke = $oke && !$con->hasError();

				$sql2 = "update pro_customer set prospect_evaluated = 0, status_customer = 1 where id_customer = '".$idr."'";
				$con->setQuery($sql2);
				$oke = $oke && !$con->hasError();
			} else{
				$sql1 = "update pro_customer_evaluasi set ceo_summary = '".json_encode(array("summary"=>$summary))."' where id_customer = '".$idr."' and id_evaluasi = '".$idk."'";
				$con->setQuery($sql1);
				$oke = $oke && !$con->hasError();
			}
		}
	}


	if ($oke){
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
			$mail->Subject = "Persetujuan Evaluasi Data Customer [".date('d/m/Y H:i:s')."]";
			$mail->msgHTML(paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])." meminta persetujuan untuk mengevaluasi data customer <p>".BASE_SERVER."</p>");
			$mail->send();
		}

		$con->commit();
		$con->close();
		header("location: ".$url);	
		exit();
	} else{
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", $msg, BASE_REFERER);
	}	
?>

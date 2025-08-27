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
	$url 	= BASE_URL_CLIENT."/purchase-request.php";
	$pic	= paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname']);
	
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 3){
		if(is_array_empty($_POST["cek"])){
			$oke = false;
			$con->close();
			$flash->add("error", "Anda belum memilih data DR", BASE_REFERER);
		} else{
			$oke 		= true;
			$emailPur 	= 0;
			$emailCeo 	= 0;
			$arrEmail 	= array();
			
			foreach($_POST['ket'] as $idx1=>$val1){
				$revert	 		= htmlspecialchars($_POST["revert"][$idx1], ENT_QUOTES);
				$summary_revert = str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["summary_revert"][$idx1], ENT_QUOTES));
				$extend	 		= htmlspecialchars($_POST["extend"][$idx1], ENT_QUOTES);
				$summary 		= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["summary"][$idx1], ENT_QUOTES));
				$wilayah 		= htmlspecialchars($_POST["idw"][$idx1], ENT_QUOTES);
				
				if($revert == 1){
					$emailPur = 1;	
					$sql1 = "update pro_pr set revert_ceo = 1, revert_ceo_summary = '".$summary_revert."', purchasing_result = 0, disposisi_pr = 3 where id_pr = '".$idx1."'";
					$con->setQuery($sql1);
					$oke  = $oke && !$con->hasError();
				} else if($revert == 2){
					foreach($_POST['ket'][$idx1] as $idx2=>$val2){
						$cek = htmlspecialchars($_POST['cek'][$idx1][$idx2], ENT_QUOTES);
						$dp2 = htmlspecialchars(str_replace(array(".",","),array("",""),$_POST['dp2'][$idx1][$idx2]), ENT_QUOTES);
						$ket = htmlspecialchars(str_replace(array(".",","),array("",""),$_POST['ket'][$idx1][$idx2]), ENT_QUOTES);
						$tmp = ($ket)?"volume = '".$ket."', ":"";
						$sql1 = "update pro_pr_detail set ".$tmp." is_approved = '".$cek."', pr_harga_beli = '".$dp2."' where id_prd = '".$idx2."'";
						$con->setQuery($sql1);
						$oke  = $oke && !$con->hasError();				
					}
					$sql2 = "update pro_pr set ceo_summary = '".$summary."', ceo_result = 1, ceo_pic = '".$pic."', ceo_tanggal = NOW(), disposisi_pr = 5 
							 where id_pr = '".$idx1."'";
					$con->setQuery($sql2);
					$oke  = $oke && !$con->hasError();
					if(!in_array($wilayah, $arrEmail)) array_push($arrEmail,$wilayah);
	
					$cek1 = "
						select a.id_pr, a.id_plan, a.jumlah, b.jumlah as jumlah_approved
						from(select count(*) as jumlah, id_plan, id_pr from pro_pr_detail where 1=1 and id_pr = '".$idx1."' group by id_pr, id_plan) a 
						left join(select count(*) as jumlah, id_plan, id_pr from pro_pr_detail where is_approved = 1 and id_pr = '".$idx1."' group by id_pr, id_plan) b 
						on a.id_pr = b.id_pr and a.id_plan = b.id_plan";
					$res1 = $con->getResult($cek1);
					if(count($res1) > 0){
						foreach($res1 as $data1){
							if(!$data1['jumlah_approved']){
								$sql3 = "update pro_po_customer_plan set status_plan = 2  where id_plan = '".$data1['id_plan']."'";
								$con->setQuery($sql3);
								$oke  = $oke && !$con->hasError();
							}
						}
					}
				}
			}
		}
	}

	else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 4){
		if(is_array_empty($_POST["cek"])){
			$oke = false;
			$con->close();
			$flash->add("error", "Anda belum memilih data DR", BASE_REFERER);
		} else{
			$oke 		= true;
			$emailPur 	= 0;
			$emailCeo 	= 0;
			$arrEmail 	= array();

			foreach($_POST['ket'] as $idx1=>$val1){
				$revert	 		= htmlspecialchars($_POST["revert"][$idx1], ENT_QUOTES);
				$summary_revert = str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["summary_revert"][$idx1], ENT_QUOTES));
				$extend	 		= htmlspecialchars($_POST["extend"][$idx1], ENT_QUOTES);
				$summary 		= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["summary"][$idx1], ENT_QUOTES));
				$wilayah 		= htmlspecialchars($_POST["idw"][$idx1], ENT_QUOTES);
				
				if($revert == 1){
					$emailPur = 1;	
					$sql1 = "update pro_pr set revert_cfo = 1, revert_cfo_summary = '".$summary_revert."', purchasing_result = 0, disposisi_pr = 3 where id_pr = '".$idx1."'";
					$con->setQuery($sql1);
					$oke  = $oke && !$con->hasError();
				} else if($revert == 2){
					if($extend == 1){
						$emailCeo = 1;	
						foreach($_POST['ket'][$idx1] as $idx2=>$val2){
							$cek = htmlspecialchars($_POST['cek'][$idx1][$idx2], ENT_QUOTES);
							$dp2 = htmlspecialchars(str_replace(array(".",","),array("",""),$_POST['dp2'][$idx1][$idx2]), ENT_QUOTES);
							$ket = htmlspecialchars(str_replace(array(".",","),array("",""),$_POST['ket'][$idx1][$idx2]), ENT_QUOTES);

							$sql1 = "update pro_pr_detail set vol_ket = '".$ket."', is_approved = '".$cek."', pr_harga_beli = '".$dp2."' where id_prd = '".$idx2."'";
							$con->setQuery($sql1);
							$oke  = $oke && !$con->hasError();				
						}
						$sql2 = "update pro_pr set cfo_summary = '".$summary."', cfo_result = 1, cfo_pic = '".$pic."', cfo_tanggal = NOW(), is_ceo = 1 where id_pr = '".$idx1."'";
						$con->setQuery($sql2);
						$oke  = $oke && !$con->hasError();
					} else if($extend == 2){
						foreach($_POST['ket'][$idx1] as $idx2=>$val2){
							$cek = htmlspecialchars($_POST['cek'][$idx1][$idx2], ENT_QUOTES);
							$dp2 = htmlspecialchars(str_replace(array(".",","),array("",""),$_POST['dp2'][$idx1][$idx2]), ENT_QUOTES);
							$ket = htmlspecialchars(str_replace(array(".",","),array("",""),$_POST['ket'][$idx1][$idx2]), ENT_QUOTES);
							$tmp = ($ket)?"volume = '".$ket."', ":"";
							$sql1 = "update pro_pr_detail set ".$tmp." is_approved = '".$cek."', pr_harga_beli = '".$dp2."' where id_prd = '".$idx2."'";
							$con->setQuery($sql1);
							$oke  = $oke && !$con->hasError();				
						}
						$sql2 = "update pro_pr set cfo_summary = '".$summary."', cfo_result = 1, cfo_pic = '".$pic."', cfo_tanggal = NOW(), disposisi_pr = 5 
								 where id_pr = '".$idx1."'";
						$con->setQuery($sql2);
						$oke  = $oke && !$con->hasError();
						if(!in_array($wilayah, $arrEmail)) array_push($arrEmail,$wilayah);
		
						$cek1 = "
							select a.id_pr, a.id_plan, a.jumlah, b.jumlah as jumlah_approved
							from(select count(*) as jumlah, id_plan, id_pr from pro_pr_detail where 1=1 and id_pr = '".$idx1."' group by id_pr, id_plan) a 
							left join(select count(*) as jumlah, id_plan, id_pr from pro_pr_detail where is_approved = 1 and id_pr = '".$idx1."' group by id_pr, id_plan) b 
							on a.id_pr = b.id_pr and a.id_plan = b.id_plan";
						$res1 = $con->getResult($cek1);
						if(count($res1) > 0){
							foreach($res1 as $data1){
								if(!$data1['jumlah_approved']){
									$sql3 = "update pro_po_customer_plan set status_plan = 2  where id_plan = '".$data1['id_plan']."'";
									$con->setQuery($sql3);
									$oke  = $oke && !$con->hasError();
								}
							}
						}
					}
				}
			}
		}
	}

	if ($oke){
		if($emailPur){
			$ems1 = "select email_user from acl_user where id_role = 5";
			$sbjk = "Pengembalian DR [".date('d/m/Y H:i:s')."]";
			$pesn = paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])." meminta anda untuk merevisi ulang DR";
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
			$mail->Subject = $sbjk;
			$mail->msgHTML($pesn);
			$mail->send();
		}
		if($emailCeo){
			$ems1 = "select email_user from acl_user where id_role = 3";
			$sbjk = "Persetujuan DR [".date('d/m/Y H:i:s')."]";
			$pesn = paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])." meminta persetujuan untuk DR <p>".BASE_SERVER."</p>";
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
			$mail->Subject = $sbjk;
			$mail->msgHTML($pesn);
			$mail->send();
		}
		if(!is_array_empty($arrEmail["ext2"])){
			$ems1 = "select email_user from acl_user where id_role = 9 and id_wilayah in (".implode(",",$arrEmail["ext2"]).")";
			$sbjk = "Persetujuan DR [".date('d/m/Y H:i:s')."]";
			$pesn = paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])." telah melakukan verifikasi DR <p>".BASE_SERVER."</p>";
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
			$mail->Subject = $sbjk;
			$mail->msgHTML($pesn);
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
		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}
?>

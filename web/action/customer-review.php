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
	$act	= !isset($enk['act'])?htmlspecialchars($_POST["act"], ENT_QUOTES):$enk['act'];
	$idr	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
	$idk	= htmlspecialchars($_POST["idk"], ENT_QUOTES);
	$idn	= htmlspecialchars($_POST["idn"], ENT_QUOTES);
	$idc	= htmlspecialchars($_POST["idc"], ENT_QUOTES);

	$review1 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["dt1"], ENT_QUOTES));
	$review3 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["dt3"], ENT_QUOTES));
	$review4 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["dt4"], ENT_QUOTES));
	$review6 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["dt6"], ENT_QUOTES));
	$review12 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["dt12"], ENT_QUOTES));
	$review13 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["dt13"], ENT_QUOTES));
	$review14 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["dt14"], ENT_QUOTES));
	$review15 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["dt15"], ENT_QUOTES));
	$review9	= htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["dt9"]), ENT_QUOTES);	
	$cl_aju		= htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["cl_aju"]), ENT_QUOTES);	
	$review9	= ($review9 ? $review9 : 0);
	$cl_aju		= ($cl_aju ? $cl_aju : 0);


	$review2 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["dt2"], ENT_QUOTES));
	$review5 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["dt5"], ENT_QUOTES));
	$review7 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["dt7"], ENT_QUOTES));
	$review8 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["dt8"], ENT_QUOTES));
	$review10 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["dt10"], ENT_QUOTES));
	$review16 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["dt16"], ENT_QUOTES));
	$review17 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["dt17"], ENT_QUOTES));
	$review18 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["dt18"], ENT_QUOTES));
	$review19 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["dt19"], ENT_QUOTES));
	$review20 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["dt20"], ENT_QUOTES));
	$review11 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["dt11"], ENT_QUOTES));
	$review21 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["dt21"], ENT_QUOTES));
	$review22 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["dt22"], ENT_QUOTES));
	$review23 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["dt23"], ENT_QUOTES));
	$summary 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["summary"], ENT_QUOTES));

	$max_size 	= 2 * 1024 * 1024;
	$allow_type	= array(".jpg", ".jpeg", ".JPG", ".png", ".pdf", ".zip", ".rar");
	$pathfile	= $public_base_directory.'/files/uploaded_user/lampiran';
	$user_ip	= $_SERVER['REMOTE_ADDR'];
	$user_pic	= paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname']);
	
	$arrFilesnya = array();
	$arrHapusnya = array();

	if(count($_FILES['review_attach_ekstra']['name']) > 0){
		foreach($_FILES['review_attach_ekstra']['name'] as $idx=>$val){
			$fileAttach = htmlspecialchars($_FILES['review_attach_ekstra']['name'][$idx],ENT_QUOTES);
			$sizeAttach = htmlspecialchars($_FILES['review_attach_ekstra']['size'][$idx],ENT_QUOTES);
			$tempAttach = htmlspecialchars($_FILES['review_attach_ekstra']['tmp_name'][$idx],ENT_QUOTES);
			$extAttach 	= substr($fileAttach,strrpos($fileAttach,'.'));
			if($fileAttach == "" && $sizeAttach > $max_size){
				$con->close();
				$flash->add("error", "Ukuran file lampiran terlalu besar", BASE_REFERER);
			} else if($fileAttach != "" && !in_array($extAttach, $allow_type)){
				$con->close();
				$flash->add("error", "Tipe file lampiran yang diperbolehkan hanya .jpg, .png, .pdf, .zip, .rar", BASE_REFERER);
			}
		}
	}

	if($idr == ""){
		$con->close();
		$flash->add("error", "Sistem gagal", BASE_REFERER);
	} else{
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		if($act == "add"){
			$ems1 = "select email_user from acl_user where id_role in(15,9,10) and id_wilayah = '".$idc."'";
			$sql1 = "
				insert into pro_customer_review(id_verification, review1, review2, review3, review4, review5, review6, review7, review8, review9, review10, 
				review11, review12, review13, review14, review15, review16, review_result, review_pic, review_tanggal, review_summary, 
				jenis_asset, kelengkapan_dok_tagihan, alur_proses_periksaan, jadwal_penerimaan, background_bisnis, lokasi_depo, opportunity_bisnis) 
				values ('".$idr."', '".$review1."', '".$review2."', '".$review3."', '".$review4."', '".$review5."', '".$review6."', '".$review7."', '".$review8."', '".$review9."', '".$review10."', 
				'".$review11."', '".$review12."', '".$review13."', '".$review14."', '".$review15."', '".$review16."', 1, '".$user_pic."', NOW(), '".$summary."', 
				'".$review17."', '".$review18."', '".$review19."', '".$review20."', '".$review21."', '".$review22."', '".$review23."')
			";
			$idk  = $con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();
	
			$sql2 = "update pro_customer_verification set is_reviewed = 1, disposisi_result = 0 where id_verification = '".$idr."'";
			$con->setQuery($sql2);
			$oke  = $oke && !$con->hasError();

			$sql3 = "update pro_customer set credit_limit_diajukan = '".$cl_aju."' where id_customer = '".$idn."'";
			$con->setQuery($sql3);
			$oke  = $oke && !$con->hasError();

			$sql4 = "delete from pro_customer_review_attchment where id_review = '".$idk."'";
			$con->setQuery($sql4);
			$oke  = $oke && !$con->hasError();

			if(count($_FILES['review_attach_ekstra']['name']) > 0){
				$urut = 0;
				foreach($_FILES['review_attach_ekstra']['name'] as $idx=>$val){
					$fileAttach = htmlspecialchars($_FILES['review_attach_ekstra']['name'][$idx],ENT_QUOTES);
					$sizeAttach = htmlspecialchars($_FILES['review_attach_ekstra']['size'][$idx],ENT_QUOTES);
					$tempAttach = htmlspecialchars($_FILES['review_attach_ekstra']['tmp_name'][$idx],ENT_QUOTES);
					$extAttach 	= substr($fileAttach,strrpos($fileAttach,'.'));
					
					if($fileAttach){
						$urut++;
						$kol1 = 'RA_'.$idk.'_'.$urut.'_'.sanitize_filename($fileAttach);
						$sql5 = "
						insert into pro_customer_review_attchment (id_review, id_verification, no_urut, review_attach, review_attach_ori) 
						values (".$idk.", ".$idr.", '".$urut."', '".$kol1."', '".sanitize_filename($fileAttach)."')";
						$con->setQuery($sql5);
						$oke  = $oke && !$con->hasError();
	
						if($oke){
							array_push($arrFilesnya, array("urutnya"=>$urut, "filenya"=>$kol1, "tempnya"=>$tempAttach)); 
						} else{
							$arrFilesnya = array();
						}
					}
				}
			}

			$url = BASE_URL_CLIENT."/customer-review-detail.php?".paramEncrypt("idr=".$idr."&idk=".$idk);
			$msg = "GAGAL_MASUK";
		} 
		
		else if($act == "update"){
			$ems1 = "";
			
			$sql1 = "
				update pro_customer_review set review1 = '".$review1."', review2 = '".$review2."', review3 = '".$review3."', review4 = '".$review4."', review5 = '".$review5."', 
				review6 = '".$review6."', review7 = '".$review7."', review8 = '".$review8."', review9 = '".$review9."', review10 = '".$review10."', 
				review11 = '".$review11."', review12 = '".$review12."', review13 = '".$review13."', review14 = '".$review14."', review15 = '".$review15."', 
				review16 = '".$review16."',  jenis_asset = '".$review17."', kelengkapan_dok_tagihan = '".$review18."', alur_proses_periksaan = '".$review19."', 
				jadwal_penerimaan = '".$review20."', background_bisnis = '".$review21."', lokasi_depo = '".$review22."', opportunity_bisnis = '".$review23."', 
				review_pic = '".$user_pic."', review_tanggal = NOW(), review_summary = '".$summary."' 
				where id_verification = '".$idr."' and id_review = '".$idk."'
			";
			$con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();

			$sql2 = "update pro_customer set credit_limit_diajukan = '".$cl_aju."', ajukan = 0 where id_customer = '".$idn."'";
			$con->setQuery($sql2);
			$oke  = $oke && !$con->hasError();

			$maxnom = 0;
			$arrnom = array();
			if(count($_POST['review_attach_urut']) > 0){
				foreach($_POST['review_attach_urut'] as $idx1=>$data1){
					$urutawal 	= htmlspecialchars($data1,ENT_QUOTES);
					$maxnom 	= max($maxnom, $urutawal);
					array_push($arrnom, $urutawal); 
				}
				$sql3 = "select no_urut from pro_customer_review_attchment where id_review = '".$idk."' and no_urut not in (".implode(',', $arrnom).")";
				$res3 = $con->getResult($sql3);

				$sql4 = "delete from pro_customer_review_attchment where id_review = '".$idk."' and no_urut not in (".implode(',', $arrnom).")";
				$con->setQuery($sql4);
				$oke  = $oke && !$con->hasError();
			} else{
				$sql3 = "select no_urut from pro_customer_review_attchment where id_review = '".$idk."'";
				$res3 = $con->getResult($sql3);

				$sql4 = "delete from pro_customer_review_attchment where id_review = '".$idk."'";
				$con->setQuery($sql4);
				$oke  = $oke && !$con->hasError();
			}

			$arrHapusnya = $res3;
			
			if(count($_FILES['review_attach_ekstra']['name']) > 0){
				$urut = $maxnom;
				foreach($_FILES['review_attach_ekstra']['name'] as $idx=>$val){
					$fileAttach = htmlspecialchars($_FILES['review_attach_ekstra']['name'][$idx],ENT_QUOTES);
					$sizeAttach = htmlspecialchars($_FILES['review_attach_ekstra']['size'][$idx],ENT_QUOTES);
					$tempAttach = htmlspecialchars($_FILES['review_attach_ekstra']['tmp_name'][$idx],ENT_QUOTES);
					$extAttach 	= substr($fileAttach,strrpos($fileAttach,'.'));
					
					if($fileAttach){
						$urut++;
						$kol1 = 'RA_'.$idk.'_'.$urut.'_'.sanitize_filename($fileAttach);
						$sql5 = "
						insert into pro_customer_review_attchment (id_review, id_verification, no_urut, review_attach, review_attach_ori) 
						values (".$idk.", ".$idr.", '".$urut."', '".$kol1."', '".sanitize_filename($fileAttach)."')";
						$con->setQuery($sql5);
						$oke  = $oke && !$con->hasError();
	
						if($oke){
							array_push($arrFilesnya, array("urutnya"=>$urut, "filenya"=>$kol1, "tempnya"=>$tempAttach)); 
						} else{
							$arrFilesnya = array();
						}
					}
				}
			}

			$url = BASE_URL_CLIENT."/customer-review-detail.php?".paramEncrypt("idr=".$idr."&idk=".$idk);
			$msg = "GAGAL_UBAH";
		}

		if ($oke){
			$mantab  = true;
			if(count($arrFilesnya) > 0){
				foreach($arrFilesnya as $idx=>$data){
					$urutnya = $data['urutnya'];
					$filenya = $data['filenya'];
					$tempnya = $data['tempnya'];
					
					$tmpPot = glob($pathfile."/RA_".$idk.'_'.$urutnya."_*.{jpg,jpeg,gif,png,pdf,zip,rar}", GLOB_BRACE);
					if(count($tmpPot) > 0){
						foreach($tmpPot as $datj)
							if(file_exists($datj)) unlink($datj);
					}
					$tujuan  = $pathfile."/".$filenya;
					$mantab  = $mantab && move_uploaded_file($tempnya, $tujuan);
					if(file_exists($tempnya)) unlink($tempnya);
				}
			}

			if(count($arrHapusnya) > 0){
				foreach($arrHapusnya as $idx=>$data){
					$urutnya 	= $data['no_urut'];
					$tmpPot 	= glob($pathfile."/RA_".$idk.'_'.$urutnya."_*.{jpg,jpeg,gif,png,pdf,zip,rar}", GLOB_BRACE);
					if(count($tmpPot) > 0){
						foreach($tmpPot as $datj)
							if(file_exists($datj)) unlink($datj);
					}
				}
			}

			if($mantab){
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
		} else{
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", $msg, BASE_REFERER);
		}
	}
	
?>

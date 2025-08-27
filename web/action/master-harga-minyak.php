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

	$periode_awal	= htmlspecialchars($_POST["periode_awal"], ENT_QUOTES);	
	$periode_akhir 	= htmlspecialchars($_POST["periode_akhir"], ENT_QUOTES);
	$user_ip		= $_SERVER['REMOTE_ADDR'];
	$user_pic		= paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname']);
	$user_role		= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	$url_back 		= ($user_role == 6)?BASE_URL_CLIENT."/master-harga-minyak.php":BASE_URL_CLIENT."/master-approval-harga.php";
	
	if(isset($_POST["periode_awal_edit"]))
	{
		$periode_awal	= htmlspecialchars($_POST["periode_awal_edit"], ENT_QUOTES);	
		$periode_akhir 	= htmlspecialchars($_POST["periode_akhir_edit"], ENT_QUOTES);
	}

	if($periode_awal == "" || $periode_akhir == ""){
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else if(is_array_empty($_POST["harga_nm"])){
		$con->close();
		$flash->add("error", "Harga Jual belum diisi", BASE_REFERER);
	} else{
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		if($act == 'add'){
			$msg = "GAGAL_MASUK";
			foreach($_POST["area"] as $idx1=>$val1){
				$area 	= htmlspecialchars($_POST["area"][$idx1], ENT_QUOTES);
				$produk = htmlspecialchars($_POST["produk"][$idx1], ENT_QUOTES);
				$note 	= htmLawed($_POST["note"][$idx1], array('safe'=>1));
				foreach($_POST["harga_nm"][$idx1] as $idx2=>$val2){
					$harga_nm 	= ($_POST["harga_nm"][$idx1][$idx2]) ? htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["harga_nm"][$idx1][$idx2]), ENT_QUOTES) : 0;
					$loco 		= ($_POST["loco"][$idx1][$idx2]) ? htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["loco"][$idx1][$idx2]), ENT_QUOTES) : 0;
					$skp 		= ($_POST["skp"][$idx1][$idx2]) ? htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["skp"][$idx1][$idx2]), ENT_QUOTES) : 0;
					$harga_sm 	= ($_POST["harga_sm"][$idx1][$idx2]) ? htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["harga_sm"][$idx1][$idx2]), ENT_QUOTES) : 0;
					$harga_om 	= ($_POST["harga_om"][$idx1][$idx2]) ? htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["harga_om"][$idx1][$idx2]), ENT_QUOTES) : 0;
					$harga_coo 	= ($_POST["harga_coo"][$idx1][$idx2]) ? htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["harga_coo"][$idx1][$idx2]), ENT_QUOTES) : 0;
					$harga_ceo 	= ($_POST["harga_ceo"][$idx1][$idx2]) ? htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["harga_ceo"][$idx1][$idx2]), ENT_QUOTES) : 0;
					$pbbkb_nm 	= htmlspecialchars($idx2, ENT_QUOTES);
					
					if($area && $produk && $harga_nm){
						$acf = ($user_role == 21)?", is_approved, is_evaluated":"";
						$acv = ($user_role == 21)?", 1, 1":"";
						$sql = "
						insert into pro_master_harga_minyak(periode_awal, periode_akhir, id_area, pajak, produk, harga_normal, loco, skp, harga_sm, harga_om, harga_coo, harga_ceo, 
						note_jual, created_time, created_ip, created_by".$acf.") values ('".tgl_db($periode_awal)."', '".tgl_db($periode_akhir)."', '".$area."', '".$pbbkb_nm."', 
						'".$produk."', '".$harga_nm."', '".$loco."', '".$skp."', '".$harga_sm."', '".$harga_om."', '".$harga_coo."', '".$harga_ceo."', '".$note."', 
						NOW(), '".$user_ip."', '".$user_pic."'".$acv.")";
						$con->setQuery($sql);
						$oke  = $oke && !$con->hasError();
					}
				}
			}
		} 
		
		else if($act == 'update'){
			$area 	= htmlspecialchars($_POST["area"], ENT_QUOTES);
			$produk = htmlspecialchars($_POST["produk"], ENT_QUOTES);
			$note 	= htmLawed($_POST["note"], array('safe'=>1));

			$msg  = "GAGAL_UBAH";
			$sql1 = "delete from pro_master_harga_minyak where periode_awal = '".tgl_db($periode_awal)."' and periode_akhir = '".tgl_db($periode_akhir)."' 
					 and id_area = '".$area."' and produk = '".$produk."'";
			$con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();
			
			foreach($_POST["harga_nm"] as $idx=>$val){
				$harga_nm 	= ($_POST["harga_nm"][$idx]) ? htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["harga_nm"][$idx]), ENT_QUOTES) : 0;
				$loco 		= ($_POST["loco"][$idx]) ? htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["loco"][$idx]), ENT_QUOTES) : 0;
				$skp 		= ($_POST["skp"][$idx]) ? htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["skp"][$idx]), ENT_QUOTES) : 0;
				$harga_sm 	= ($_POST["harga_sm"][$idx]) ? htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["harga_sm"][$idx]), ENT_QUOTES) : 0;
				$harga_om 	= ($_POST["harga_om"][$idx]) ? htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["harga_om"][$idx]), ENT_QUOTES) : 0;
				$harga_coo 	= ($_POST["harga_coo"][$idx]) ? htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["harga_coo"][$idx]), ENT_QUOTES) : 0;
				$harga_ceo 	= ($_POST["harga_ceo"][$idx]) ? htmlspecialchars(str_replace(array(".",","), array("",""), $_POST["harga_ceo"][$idx]), ENT_QUOTES) : 0;
				$pbbkb_nm 	= htmlspecialchars($idx, ENT_QUOTES);

				$sql2 = "
				insert into pro_master_harga_minyak(periode_awal, periode_akhir, id_area, pajak, produk, harga_normal, loco, skp, harga_sm, harga_om, harga_coo, harga_ceo, note_jual, 
				is_approved, is_evaluated, lastupdate_time, lastupdate_ip, lastupdate_by, is_edited) values ('".tgl_db($periode_awal)."', '".tgl_db($periode_akhir)."', '".$area."', 
				'".$pbbkb_nm."', '".$produk."', '".$harga_nm."', '".$loco."', '".$skp."','".$harga_sm."', '".$harga_om."', '".$harga_coo."', '".$harga_ceo."', '".$note."', 
				1, 1, NOW(), '".$user_ip."', '".$user_pic."', 1)";
				$con->setQuery($sql2);
				$oke  = $oke && !$con->hasError();
			}
		}

		if ($oke){
			if($user_role == 6 && $act == "add"){
				$ems = "select email_user from acl_user where id_role = 21";
				$rms = $con->getResult($ems);
				
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
				foreach($rms as $datms){
					$mail->addAddress($datms['email_user']);
				}
				$mail->Subject = "Persetujuan Harga Jual [".date('d/m/Y H:i:s')."]";
				$mail->msgHTML($user_pic." meminta persetujuan harga jual <p>".BASE_SERVER."</p>");
				$mail->send();
			}
			$con->commit();
			$con->close();
			header("location: ".$url_back);
			exit();
		} else{
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", $msg, BASE_REFERER);
		}
	}
?>

<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "htmlawed", "mailgen");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;
$enk  	= decode($_SERVER['REQUEST_URI']);
// $act	= ($enk['act'] ? $enk['act'] : htmlspecialchars($_POST["act"], ENT_QUOTES));
$act = (!isset($enk['act']) or $enk['act'] == "") ? (isset($_POST["act"]) ? htmlspecialchars($_POST["act"], ENT_QUOTES) : null) : $enk['act'];
$idr 	= isset($_POST["idr"]) ? $_POST["idr"] : null;
$idsr 	= isset($_POST["idsr"]) ? $_POST["idsr"] : null;

$dt1					= htmlspecialchars($_POST["dt1"], ENT_QUOTES);
$dt2					= htmlspecialchars($_POST["dt2"], ENT_QUOTES);
// $id_vessel	= htmlspecialchars($_POST["id_vessel"], ENT_QUOTES);
$cargo					= htmlspecialchars($_POST["cargo"], ENT_QUOTES);
$volume_po				= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["volume_po"]), ENT_QUOTES);
$flag					= htmlspecialchars($_POST["flag"], ENT_QUOTES);
$shipper				= htmlspecialchars($_POST["shipper"], ENT_QUOTES);
$signee_name			= htmlspecialchars($_POST["signee_name"], ENT_QUOTES);

$load_port				= htmlspecialchars($_POST["load_port"], ENT_QUOTES);
$discharge_terminal		= htmlspecialchars($_POST["discharge_terminal_id"], ENT_QUOTES);
$bill					= htmlspecialchars($_POST["bill"], ENT_QUOTES);
$loss	    			= htmlspecialchars(str_replace(array(","), array("", ""), $_POST["loss"]), ENT_QUOTES);
$bl_ship				= htmlspecialchars($_POST["bl_ship"], ENT_QUOTES);
$ket_ship				= htmlspecialchars($_POST["ket_ship"], ENT_QUOTES);
$ket_cancel				= htmlspecialchars($_POST["ket_cancel"], ENT_QUOTES);

$country	    = htmlspecialchars($_POST["country"], ENT_QUOTES);
$tgl_etl_first  = htmlspecialchars($_POST["tgl_etl_awal"], ENT_QUOTES);
$tgl_etl_last  = htmlspecialchars($_POST["tgl_etl_akhir"], ENT_QUOTES);

$id_vessel  = htmlspecialchars($_POST["id_vessel"], ENT_QUOTES);
$freight    = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["freight"]), ENT_QUOTES);
$loss	    = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["loss"]), ENT_QUOTES);
$demurrage	    = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["demurrage"]), ENT_QUOTES);
// $year                 = date("Y");
// $month                 = date("m");
// $arrRomawi             = array("1" => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
// $monthnow_romawi     = $arrRomawi[intval($month)];
// $query_no_req = "SELECT * FROM new_pro_inventory_vendor_po_ship_req WHERE nomor_req LIKE '%" . "/" . $rowWil['inisial_cabang'] . "/" . $year . "/" . $monthnow_romawi . "/" . "%' ORDER BY nomor_req DESC ";
// $row2 = $con->getRecord($query_no_req);
// $no_req = $row2['no_req'];
// $explode = explode("/", $no_req);
// $year_req = $explode[3] ? $explode[3] : $year;
// $month_req = $explode[4];
// $sum = $explode[2]+1;

// $urut_req = $explode[0] ? $explode[0]+1 : 1;
// $no_req = sprintf("%03s", $urut_req);
// $noms_req = $no_req . '/PE-Purch/' .$sum . '/' . $arrRomawi[intval($month)]. '/' . $year_req;

if ($act == 'add') {
	if ($tgl_etl_first == "" || $tgl_etl_last == "" || $idr == "") {
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else {
		$year                 = date("Y");
		$month                 = date("m");
		$arrRomawi             = array("1" => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
		$monthnow_romawi     = $arrRomawi[intval($month)];
		$query_no_req = "SELECT * FROM new_pro_inventory_vendor_po_ship_req WHERE nomor_req LIKE '%" . "/" . $monthnow_romawi . "/" . $year . "%' ORDER BY nomor_req DESC ";
		$row2 = $con->getRecord($query_no_req);
		$no_req = $row2['nomor_req'];
		$explode = explode("/", $no_req);
		$year_req = $explode[4] ? $explode[4] : $year;
		$month_req = $explode[3];
		$sum = $explode[2]+1;

		$urut_req = $explode[0] ? $explode[0]+1 : 1;
		$no_req = sprintf("%03s", $urut_req);
		$noms_req = $no_req . '/PE-Purch/250/' . $arrRomawi[intval($month)]. '/' . $year_req;

		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		// $msg = "BERHASIL_MASUK";
		// $ems1 = "select email_user from acl_user where id_role = 4";
		$sql = "
				insert into new_pro_inventory_vendor_po_ship_req (id_vendor_po, id_vessel, id_terminal_discharging, flag, quantity, nomor_req, loading_port, etl_date_first, etl_date_last, cargo_name, bill_lading, loss_tolerance, freight, demurrage, ket_ship, country_origin, shipper, consignee, bl_ship, created_at, created_by)
					values ('" . $idr . "', '" . $id_vessel . "', '" . $discharge_terminal . "', '" . $flag . "', '" . $volume_po . "', '" . $noms_req. "' , '" . $load_port. "' ,'" . tgl_db($tgl_etl_first) . "','" . tgl_db($tgl_etl_last) . "', '" . $cargo . "', '" . $bill . "', '" . $loss . "','" . $freight . "', '" . $demurrage . "','" . $ket_ship . "', '" . $country . "','" . $shipper . "', '" . $signee_name . "', '" . $bl_ship . "', NOW(),  '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "')";
		$get_idsr= $con->setQuery($sql);
		$oke  = $oke && !$con->hasError();

		

		// $sbjk = "Shipping Request[" . date('d/m/Y H:i:s') . "]";
		// $pesn = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " meminta untuk Shipping instruction";
		// $pesn .= "<p>" . BASE_SERVER . "</p>";

		if ($oke) {

			// if ($ems1) {
			// 	$rms1 = $con->getResult($ems1);
			// 	$mail = new PHPMailer;
			// 	$mail->isSMTP();
			// 	$mail->Host = 'smtp.gmail.com';
			// 	$mail->Port = 465;
			// 	$mail->SMTPSecure = 'ssl';
			// 	$mail->SMTPAuth = true;
			// 	$mail->SMTPKeepAlive = true;
			// 	$mail->Username = USR_EMAIL_PROENERGI202389;
			// 	$mail->Password = PWD_EMAIL_PROENERGI202389;

			// 	$mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');
			// 	foreach ($rms1 as $datms) {
			// 		$mail->addAddress($datms['email_user']);
			// 	}
			// 	$mail->Subject = $sbjk;
			// 	$mail->msgHTML($pesn);
			// 	$mail->send();
			// }

			$con->commit();
			$con->close();
			$flash->add("success", "Data berhasil disimpan", BASE_URL_CLIENT . "/vendor-po-ship-req.php?" . paramEncrypt('idr=' . $idr. '&idsr=' . $get_idsr));
		// header("location: " . BASE_URL_CLIENT . "/vendor-po-ship-req.php");
			exit();
		} else {
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", 'Maaf Data Gagal Disimpan', BASE_REFERER);
		}
	}
} else if ($act == 'update') {
	
	if ($idsr == "") {
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else {
		$id1nya = $idsr;

		if ($id1nya) {
			$oke = true;
			$con->beginTransaction();
			$con->clearError();

			$msg = "GAGAL_UBAH";
			$sql = "
					update new_pro_inventory_vendor_po_ship_req set id_vessel = '" . $id_vessel . "', id_terminal_discharging = '" . $discharge_terminal . "', flag = 'INDONESIA', quantity = '" . $volume_po . "', etl_date_first = '" .   tgl_db($tgl_etl_first) . "', etl_date_last = '" .   tgl_db($tgl_etl_last) . "', cargo_name = '" . $cargo . "',
					bill_lading = '" . $bill . "', loss_tolerance = '" . $loss . "', freight = '" . $freight . "',  demurrage = '" . $demurrage . "'country_origin = '" . $country . "', shipper = '" . $shipper . "', consignee ='" . $consignee . "', bl_Ship =  '" . $bl_ship . "', ket_ship = '" . $ket_ship . "',
					nomor_si = NULL, loading_port = '" . $load_port . "',
					updated_at = NOW(), updated_by = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "', status = 0, ket_log = NULL, log_pic = NULL, log_tanggal = NULL,
					mgrfin_result = NULL, mgrfin_pic = NULL, mgrfin_tanggal = NULL, mgrfin_summary = NULL, cfo_result = NULL, cfo_pic = NULL, cfo_tanggal = NULL, cfo_summary = NULL,
					ceo_result = NULL, ceo_pic = NULL, ceo_tanggal = NULL, ceo_summary = NULL
					where id_master = '" . $idsr . "'
				";
			$con->setQuery($sql);
			$oke  = $oke && !$con->hasError();

			if ($oke) {
				$con->commit();
				$con->close();
				$flash->add("success", "Data berhasil diubah", BASE_URL_CLIENT . "/vendor-po-ship-req.php?" . paramEncrypt('idr=' . $idr. '&idsr=' . $idsr));
				exit();
			} else {
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", 'Maaf Data Gagal diubah', BASE_REFERER);
			}
		}
	}
}else if ($act == 'cancel') {
	if ($idsr == "") {
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else {
		$id1nya = paramDecrypt($idsr);

		if ($id1nya) {
			$oke = true;
			$con->beginTransaction();
			$con->clearError();
			$answer	= array();

			$msg = "GAGAL_UBAH";
			$sql = "
					update new_pro_inventory_vendor_po_ship_req set is_cancel = 1, ket_cancel = '" . $ket_cancel . "' 
					where id_master = '" . $id1nya . "'
				";
			$con->setQuery($sql);
			$oke  = $oke && !$con->hasError();

			if ($oke) {
				$con->commit();
				$con->close();
				$answer["status"] = true;
				// header("location: " . BASE_URL_CLIENT . "/vendor-po-new.php");
				// exit();
			} else {
				$con->rollBack();
				$con->clearError();
				$con->close();
				$answer["status"] = false;
				// $flash->add("error", $msg, BASE_REFERER);
			}
			echo json_encode($answer);
		}
	}
}
//  else if ($act == 'close') {
// 	if ($tgl_close == "") {
// 		$con->close();
// 		$flash->add("error", "KOSONG", BASE_REFERER);
// 	} else {
// 		$id1nya = $idr;

// 		if ($id1nya) {
// 			$oke = true;
// 			$con->beginTransaction();
// 			$con->clearError();

// 			$msg = "GAGAL_UBAH";
// 			$sql = "
// 					update new_pro_inventory_vendor_po set is_close = 1, tanggal_close = '" . tgl_db($tgl_close) . "',  volume_close = '" . $volume_close . "' 
// 					where id_master = '" . $idr . "'
// 				";
// 			$con->setQuery($sql);
// 			$oke  = $oke && !$con->hasError();

// 			if ($oke) {
// 				$con->commit();
// 				$con->close();
// 				header("location: " . BASE_URL_CLIENT . "/vendor-po-new.php");
// 				exit();
// 			} else {
// 				$con->rollBack();
// 				$con->clearError();
// 				$con->close();
// 				$flash->add("error", $msg, BASE_REFERER);
// 			}
// 		}
// 	}
// }

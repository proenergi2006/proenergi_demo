
<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "htmlawed");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;
$enk  	= decode($_SERVER['REQUEST_URI']);
$act	= !isset($enk['act']) ? htmlspecialchars($_POST["act"], ENT_QUOTES) : $enk['act'];
$idr	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
$id_invoice_enc = paramDecrypt($_POST["id_invoice_encrypt"]);
$refund = paramDecrypt($_POST["refund"]);

$id_customer			= htmlspecialchars($_POST["id_customer"], ENT_QUOTES);
$no_invoice_manual		= htmlspecialchars($_POST["no_invoice"], ENT_QUOTES);
$no_invoice_customer 	= htmlspecialchars($_POST["no_invoice_customer"], ENT_QUOTES);
$tgl_invoice 			= htmlspecialchars($_POST["tgl_invoice"], ENT_QUOTES);
$tgl_invoice_dikirim 	= htmlspecialchars($_POST["tgl_invoice_dikirim"], ENT_QUOTES);
$split_invoice 			= htmlspecialchars($_POST["split_invoice"], ENT_QUOTES);
$tipe					= htmlspecialchars($_POST["tipe"], ENT_QUOTES);
$ketentuan				= htmlspecialchars($_POST["next_month"], ENT_QUOTES);
$tgl_delivered			= htmlspecialchars($_POST["tanggal"], ENT_QUOTES);
$tgl_kirim_awal			= htmlspecialchars($_POST["tgl_kirim_awal"], ENT_QUOTES);
$tgl_kirim_akhir		= htmlspecialchars($_POST["tgl_kirim_akhir"], ENT_QUOTES);
$lunas					= $_POST["lunas"];
$is_cetakan				= $_POST["cetakan_invoice"];
$no_invoice_customer	= htmlspecialchars($_POST["no_invoice_customer"], ENT_QUOTES);

if (isset($lunas)) {
	$fix_lunas = 1;
} else {
	$fix_lunas = NULL;
}

$total_invoice	= htmlspecialchars(str_replace(array(","), array(""), $_POST["total_invoice"]), ENT_QUOTES);

$total_invoice_harga_dasar	= htmlspecialchars(str_replace(array(","), array(""), $_POST["total_invoice_harga_dasar"]), ENT_QUOTES);

$total_invoice_ongkos_angkut	= htmlspecialchars(str_replace(array(","), array(""), $_POST["total_invoice_ongkos_angkut"]), ENT_QUOTES);

$total_invoice_pbbkb	= htmlspecialchars(str_replace(array(","), array(""), $_POST["total_invoice_pbbkb"]), ENT_QUOTES);

$total_invoice_harga_dasar_oa	= htmlspecialchars(str_replace(array(","), array(""), $_POST["total_invoice_harga_dasar_oa"]), ENT_QUOTES);

$total_invoice_harga_dasar_pbbkb	= htmlspecialchars(str_replace(array(","), array(""), $_POST["total_invoice_harga_dasar_pbbkb"]), ENT_QUOTES);

$total_bayar		= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["total_bayar"]), ENT_QUOTES);

$total_bayar_potongan		= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["total_bayar_potongan"]), ENT_QUOTES);

$created_time		= date("Y/m/d H:i:s");
$created_ip			= $_SERVER['REMOTE_ADDR'];
$created_by			= paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']);
$lastupdate_time	= $created_time;
$lastupdate_ip		= $created_ip;
$lastupdate_by		= $created_by;

$total_invoice		= ($total_invoice ? $total_invoice : 0);
$total_invoice_harga_dasar	 = ($total_invoice_harga_dasar ? $total_invoice_harga_dasar : 0);
$total_invoice_ongkos_angkut = ($total_invoice_ongkos_angkut ? $total_invoice_ongkos_angkut : 0);
$total_invoice_pbbkb = ($total_invoice_pbbkb ? $total_invoice_pbbkb : 0);
$total_invoice_harga_dasar_oa = ($total_invoice_harga_dasar_oa ? $total_invoice_harga_dasar_oa : 0);
$total_invoice_harga_dasar_pbbkb = ($total_invoice_harga_dasar_pbbkb ? $total_invoice_harga_dasar_pbbkb : 0);
$total_bayar		= ($total_bayar ? $total_bayar : 0);

$arrRomawi 			= array("1" => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");

if ($act == "add") {
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	if ($ketentuan == "next_month") {
		$today 				= date("Y-m-d", strtotime(tgl_db($tgl_invoice)));
		$today02 			= date("Y-m-d", strtotime("+1 month", strtotime($today)));
		$next_year 			= date("y", strtotime($today02));
		$next_month 		= date("m", strtotime($today02));
		$nextmonth_romawi 	= $arrRomawi[intval($next_month)];
		$id_wilayah			= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

		$sql_approval = "SELECT * FROM pro_master_approval_invoice WHERE cabang='" . $id_wilayah . "' and is_active='1'";
		$approval = $con->getRecord($sql_approval);

		if ($approval == NULL || $approval == "") {
			$con->close();
			$flash->add("error", "Belum ada approval pada cabang penagihan invoice, mohon tambahkan approval invoice", BASE_REFERER);
		}

		if ($id_wilayah == '4' || $id_wilayah == '5' || $id_wilayah == '7') {
			$query_no_inv = "SELECT * FROM pro_invoice_admin WHERE no_invoice = '" . $no_invoice_manual . "' ORDER BY no_invoice DESC";
			$row2 = $con->getRecord($query_no_inv);

			if ($row2) {
				$con->close();
				$flash->add("error", "Nomor Invoice sudah ada", BASE_REFERER);
			} else {
				$noms_inv = $no_invoice_manual;
			}
		} else {
			$sqlGetWil = "SELECT * FROM pro_master_cabang WHERE id_master='" . $id_wilayah . "'";
			$row = $con->getRecord($sqlGetWil);

			$query_no_inv = "SELECT * FROM pro_invoice_admin WHERE no_invoice LIKE '%" . "/" . $row['inisial_cabang'] . "/" . $next_year . "/" . $nextmonth_romawi . "/" . "%' ORDER BY no_invoice DESC";
			$row2 = $con->getRecord($query_no_inv);

			if ($row2) {
				$no_invoice = $row2['no_invoice'];
				$explode = explode("/", $no_invoice);
				$year_inv = $explode[3];
				$month_inv = $explode[4];

				switch ($month_inv) {
					case "I":
						$bulan = '01';
						break;
					case "II":
						$bulan = '02';
						break;
					case "III":
						$bulan = '03';
						break;
					case "IV":
						$bulan = '04';
						break;
					case "V":
						$bulan = '05';
						break;
					case "VI":
						$bulan = '06';
						break;
					case "VII":
						$bulan = '07';
						break;
					case "VIII":
						$bulan = '08';
						break;
					case "IX":
						$bulan = '09';
						break;
					case "X":
						$bulan = '10';
						break;
					case "XI":
						$bulan = '11';
						break;
					case "XII":
						$bulan = '12';
						break;
				}

				if ($bulan == $next_month && $year_inv == $next_year) {
					$urut_inv = $explode[5] + 1;
					$no_inv = sprintf("%03s", $urut_inv);
					$noms_inv = 'SI/' . 'PE/' . $row['inisial_cabang'] . '/' . $year_inv . '/' . $arrRomawi[intval($bulan)] . '/' . $no_inv;
				} else {
					$urut_inv = 1;
					$no_inv = sprintf("%03s", $urut_inv);
					$noms_inv = 'SI/' . 'PE/' . $row['inisial_cabang'] . '/' . $next_year . '/' . $arrRomawi[intval($next_month)] . '/' . $no_inv;
				}
			} else {
				$urut_inv 	= 1;
				$no_inv 	= sprintf("%03s", $urut_inv);
				$noms_inv 	= 'SI/' . 'PE/' . $row['inisial_cabang'] . '/' . $next_year . '/' . $arrRomawi[intval($next_month)] . '/' . $no_inv;
			}
		}
	} else {
		$year 				= date("y", strtotime(tgl_db($tgl_invoice)));
		$month 				= date("m", strtotime(tgl_db($tgl_invoice)));
		$id_wilayah			= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
		$monthnow_romawi 	= $arrRomawi[intval($month)];

		$sql_approval = "SELECT * FROM pro_master_approval_invoice WHERE cabang='" . $id_wilayah . "' and is_active='1'";
		$approval = $con->getRecord($sql_approval);

		if ($approval == NULL || $approval == "") {
			$con->close();
			$flash->add("error", "Belum ada approval pada cabang penagihan invoice, mohon tambahkan approval invoice", BASE_REFERER);
		}

		if ($id_wilayah == '4' || $id_wilayah == '5' || $id_wilayah == '7') {

			if ($no_invoice_manual == "") {
				$con->close();
				$flash->add("error", "Nomor Invoice tidak boleh kosong", BASE_REFERER);
			} else {
				$query_no_inv = "SELECT * FROM pro_invoice_admin WHERE no_invoice = '" . $no_invoice_manual . "' ORDER BY no_invoice DESC";
				$row2 = $con->getRecord($query_no_inv);

				if ($row2) {
					$con->close();
					$flash->add("error", "Nomor Invoice sudah ada", BASE_REFERER);
				} else {
					$noms_inv = $no_invoice_manual;
				}
			}
		} else {
			$sqlGetWil = "SELECT * FROM pro_master_cabang WHERE id_master='" . $id_wilayah . "'";
			$row = $con->getRecord($sqlGetWil);

			$query_no_inv = "SELECT * FROM pro_invoice_admin WHERE no_invoice LIKE '%" . "/" . $row['inisial_cabang'] . "/" . $year . "/" . $monthnow_romawi . "/" . "%' ORDER BY no_invoice DESC";
			$row2 = $con->getRecord($query_no_inv);

			if ($row2) {
				$no_invoice = $row2['no_invoice'];
				$explode = explode("/", $no_invoice);
				$year_inv = $explode[3];
				$month_inv = $explode[4];

				switch ($month_inv) {
					case "I":
						$bulan = '01';
						break;
					case "II":
						$bulan = '02';
						break;
					case "III":
						$bulan = '03';
						break;
					case "IV":
						$bulan = '04';
						break;
					case "V":
						$bulan = '05';
						break;
					case "VI":
						$bulan = '06';
						break;
					case "VII":
						$bulan = '07';
						break;
					case "VIII":
						$bulan = '08';
						break;
					case "IX":
						$bulan = '09';
						break;
					case "X":
						$bulan = '10';
						break;
					case "XI":
						$bulan = '11';
						break;
					case "XII":
						$bulan = '12';
						break;
				}

				if ($ketentuan == "next_month") {
					$today 		= date("Y-m-d", strtotime(tgl_db($tgl_invoice)));
					$today02 	= date("Y-m-d", strtotime("+1 month", strtotime($today)));
					$next_month = date("m", strtotime($today02));

					if ($bulan == $month && $year_inv == $year) {
						$urut_inv = $explode[5] + 1;
						$no_inv = sprintf("%03s", $urut_inv);
						$noms_inv = 'SI/' . 'PE/' . $row['inisial_cabang'] . '/' . $year_inv . '/' . $arrRomawi[intval($bulan)] . '/' . $no_inv;
					} else {
						$urut_inv = 1;
						$no_inv = sprintf("%03s", $urut_inv);
						$noms_inv = 'SI/' . 'PE/' . $row['inisial_cabang'] . '/' . $year . '/' . $arrRomawi[intval($next_month)] . '/' . $no_inv;
					}
				} else {
					if ($bulan == $month && $year_inv == $year) {
						$urut_inv = $explode[5] + 1;
						$no_inv = sprintf("%03s", $urut_inv);
						$noms_inv = 'SI/' . 'PE/' . $row['inisial_cabang'] . '/' . $year_inv . '/' . $arrRomawi[intval($bulan)] . '/' . $no_inv;
					} else {
						$urut_inv = 1;
						$no_inv = sprintf("%03s", $urut_inv);
						$noms_inv = 'SI/' . 'PE/' . $row['inisial_cabang'] . '/' . $year . '/' . $arrRomawi[intval(date("m", strtotime(tgl_db($tgl_invoice))))] . '/' . $no_inv;
					}
				}
			} else {

				if ($ketentuan == "next_month") {
					$today 		= date("Y-m-d", strtotime(tgl_db($tgl_invoice)));
					$today02 	= date("Y-m-d", strtotime("+1 month", strtotime($today)));
					$next_month = date("m", strtotime($today02));
					$urut_inv 	= 1;
					$no_inv 	= sprintf("%03s", $urut_inv);
					$noms_inv 	= 'SI/' . 'PE/' . $row['inisial_cabang'] . '/' . $year . '/' . $arrRomawi[intval($next_month)] . '/' . $no_inv;
				} else {
					$urut_inv = 1;
					$no_inv = sprintf("%03s", $urut_inv);
					$noms_inv = 'SI/' . 'PE/' . $row['inisial_cabang'] . '/' . $year . '/' . $arrRomawi[intval(date("m", strtotime(tgl_db($tgl_invoice))))] . '/' . $no_inv;
				}
			}
		}
	}

	if ($tipe == "tanggal") {
		$tgl_delivered_awal 	= $tgl_delivered;
		$tgl_delivered_akhir 	= $tgl_delivered;
	} else {
		$tgl_delivered_awal 	= $tgl_kirim_awal;
		$tgl_delivered_akhir 	= $tgl_kirim_akhir;
	}

	if ($split_invoice == "all_in") {
		$sql1 = "
		insert into pro_invoice_admin(id_customer, no_invoice, tgl_invoice, tgl_kirim_awal, tgl_kirim_akhir, total_invoice, is_cetakan, no_invoice_customer, status_ar, jenis, id_approval,
		created_time, created_ip, created_by) values 
		('" . $id_customer . "', '" . $noms_inv . "', '" . tgl_db($tgl_invoice) . "', '" . tgl_db($tgl_delivered_awal) . "', '" . tgl_db($tgl_delivered_awal) . "', '" . round($total_invoice) . "', '" . $is_cetakan . "','" . $no_invoice_customer . "', 'notyet', 'all_in', '" . $approval['id_master'] . "',
		'" . $created_time . "', '" . $created_ip . "', '" . $created_by . "')";
		$res1 = $con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();

		$sql4 = "update pro_customer_admin_arnya set not_yet = not_yet + '" . round($total_invoice) . "' where id_customer = '" . $id_customer . "'";
		$con->setQuery($sql4);
		$oke  = $oke && !$con->hasError();
	} elseif ($split_invoice == "split_oa") {

		// Harga Dasar + PBBKB + PPN x Volume Kirim
		$sql_split_oa1 = "
		insert into pro_invoice_admin(id_customer, no_invoice, tgl_invoice, tgl_kirim_awal, tgl_kirim_akhir, total_invoice, is_cetakan, no_invoice_customer, status_ar, jenis, id_approval,
		created_time, created_ip, created_by) values 
		('" . $id_customer . "', '" . $noms_inv . "', '" . tgl_db($tgl_invoice) . "', '" . tgl_db($tgl_delivered_awal) . "', '" . tgl_db($tgl_delivered_awal) . "', '" . round($total_invoice_harga_dasar_pbbkb) . "', '" . $is_cetakan . "','" . $no_invoice_customer . "', 'notyet', 'harga_dasar_pbbkb', '" . $approval['id_master'] . "',
		'" . $created_time . "', '" . $created_ip . "', '" . $created_by . "')";
		$res1 = $con->setQuery($sql_split_oa1);
		$oke  = $oke && !$con->hasError();

		// Ongkos Kirim + PPN x Volume Kirim
		$noms_inv_split_oa = $noms_inv . "A";
		$sql_split_oa2 = "
		insert into pro_invoice_admin(id_customer, no_invoice, tgl_invoice, tgl_kirim_awal, tgl_kirim_akhir, total_invoice, is_cetakan, no_invoice_customer, status_ar, jenis, id_approval,
		created_time, created_ip, created_by) values 
		('" . $id_customer . "', '" . $noms_inv_split_oa . "', '" . tgl_db($tgl_invoice) . "', '" . tgl_db($tgl_delivered_awal) . "', '" . tgl_db($tgl_delivered_awal) . "', '" . round($total_invoice_ongkos_angkut) . "', '" . $is_cetakan . "','" . $no_invoice_customer . "', 'notyet', 'split_oa', '" . $approval['id_master'] . "',
		'" . $created_time . "', '" . $created_ip . "', '" . $created_by . "')";
		$res2 = $con->setQuery($sql_split_oa2);
		$oke  = $oke && !$con->hasError();

		$total_invoice_split_oa = $total_invoice_harga_dasar_pbbkb + $total_invoice_ongkos_angkut;

		$sql4 = "update pro_customer_admin_arnya set not_yet = not_yet + '" . round($total_invoice_split_oa) . "' where id_customer = '" . $id_customer . "'";
		$con->setQuery($sql4);
		$oke  = $oke && !$con->hasError();
	} elseif ($split_invoice == "split_pbbkb") {
		// Harga Dasar + OA + PPN x Volume Kirim
		$sql_split_pbbkb1 = "
		insert into pro_invoice_admin(id_customer, no_invoice, tgl_invoice, tgl_kirim_awal, tgl_kirim_akhir, total_invoice, is_cetakan, no_invoice_customer, status_ar, jenis, id_approval,
		created_time, created_ip, created_by) values 
		('" . $id_customer . "', '" . $noms_inv . "', '" . tgl_db($tgl_invoice) . "', '" . tgl_db($tgl_delivered_awal) . "', '" . tgl_db($tgl_delivered_awal) . "', '" . round($total_invoice_harga_dasar_oa) . "', '" . $is_cetakan . "','" . $no_invoice_customer . "', 'notyet', 'harga_dasar_oa', '" . $approval['id_master'] . "',
		'" . $created_time . "', '" . $created_ip . "', '" . $created_by . "')";
		$res1 = $con->setQuery($sql_split_pbbkb1);
		$oke  = $oke && !$con->hasError();

		// PBBKB x Volume Kirim
		$noms_inv_split_pbbkb = $noms_inv . "B";
		$sql_split_pbbkb2 = "
		insert into pro_invoice_admin(id_customer, no_invoice, tgl_invoice, tgl_kirim_awal, tgl_kirim_akhir, total_invoice, is_cetakan, no_invoice_customer, status_ar, jenis, id_approval,
		created_time, created_ip, created_by) values 
		('" . $id_customer . "', '" . $noms_inv_split_pbbkb . "', '" . tgl_db($tgl_invoice) . "', '" . tgl_db($tgl_delivered_awal) . "', '" . tgl_db($tgl_delivered_awal) . "', '" . round($total_invoice_pbbkb) . "', '" . $is_cetakan . "','" . $no_invoice_customer . "', 'notyet', 'split_pbbkb', '" . $approval['id_master'] . "',
		'" . $created_time . "', '" . $created_ip . "', '" . $created_by . "')";
		$res2 = $con->setQuery($sql_split_pbbkb2);
		$oke  = $oke && !$con->hasError();

		$total_invoice_split_pbbkb = $total_invoice_harga_dasar_oa + $total_invoice_pbbkb;

		$sql4 = "update pro_customer_admin_arnya set not_yet = not_yet + '" . round($total_invoice_split_pbbkb) . "' where id_customer = '" . $id_customer . "'";
		$con->setQuery($sql4);
		$oke  = $oke && !$con->hasError();
	} elseif ($split_invoice == "split_all") {

		// Harga Dasar + PPN x Volume Kirim
		$sql_split_all1 = "
		insert into pro_invoice_admin(id_customer, no_invoice, tgl_invoice, tgl_kirim_awal, tgl_kirim_akhir, total_invoice, is_cetakan, no_invoice_customer, status_ar, jenis, id_approval,
		created_time, created_ip, created_by) values 
		('" . $id_customer . "', '" . $noms_inv . "', '" . tgl_db($tgl_invoice) . "', '" . tgl_db($tgl_delivered_awal) . "', '" . tgl_db($tgl_delivered_awal) . "', '" . round($total_invoice_harga_dasar) . "', '" . $is_cetakan . "','" . $no_invoice_customer . "', 'notyet', 'harga_dasar', '" . $approval['id_master'] . "',
		'" . $created_time . "', '" . $created_ip . "', '" . $created_by . "')";
		$res1 = $con->setQuery($sql_split_all1);
		$oke  = $oke && !$con->hasError();

		// Ongkos Kirim + PPN x Volume Kirim
		$noms_inv_split_oa = $noms_inv . "A";
		$sql_split_all2 = "
		insert into pro_invoice_admin(id_customer, no_invoice, tgl_invoice, tgl_kirim_awal, tgl_kirim_akhir, total_invoice, is_cetakan, no_invoice_customer, status_ar, jenis, id_approval,
		created_time, created_ip, created_by) values 
		('" . $id_customer . "', '" . $noms_inv_split_oa . "', '" . tgl_db($tgl_invoice) . "', '" . tgl_db($tgl_delivered_awal) . "', '" . tgl_db($tgl_delivered_awal) . "', '" . round($total_invoice_ongkos_angkut) . "', '" . $is_cetakan . "','" . $no_invoice_customer . "', 'notyet', 'split_oa', '" . $approval['id_master'] . "',
		'" . $created_time . "', '" . $created_ip . "', '" . $created_by . "')";
		$res2 = $con->setQuery($sql_split_all2);
		$oke  = $oke && !$con->hasError();

		// PBBKB x Volume Kirim
		$noms_inv_split_pbbkb = $noms_inv . "B";
		$sql_split_all3 = "
		insert into pro_invoice_admin(id_customer, no_invoice, tgl_invoice, tgl_kirim_awal, tgl_kirim_akhir, total_invoice, is_cetakan, no_invoice_customer, status_ar, jenis, id_approval,
		created_time, created_ip, created_by) values 
		('" . $id_customer . "', '" . $noms_inv_split_pbbkb . "', '" . tgl_db($tgl_invoice) . "', '" . tgl_db($tgl_delivered_awal) . "', '" . tgl_db($tgl_delivered_awal) . "', '" . round($total_invoice_pbbkb) . "', '" . $is_cetakan . "','" . $no_invoice_customer . "', 'notyet', 'split_pbbkb', '" . $approval['id_master'] . "',
		'" . $created_time . "', '" . $created_ip . "', '" . $created_by . "')";
		$res3 = $con->setQuery($sql_split_all3);
		$oke  = $oke && !$con->hasError();

		$total_invoice_split_all = $total_invoice_harga_dasar + $total_invoice_ongkos_angkut + $total_invoice_pbbkb;

		$sql4 = "update pro_customer_admin_arnya set not_yet = not_yet + '" . round($total_invoice_split_all) . "' where id_customer = '" . $id_customer . "'";
		$con->setQuery($sql4);
		$oke  = $oke && !$con->hasError();
	}

	// $sql3 = "delete from pro_invoice_admin_detail where id_invoice = '" . $res1 . "'";
	// $con->setQuery($sql3);
	// $oke  = $oke && !$con->hasError();

	$noms01 = 0;
	// $arrayId = array();
	// array_push($arrayId, array("id_invoice_1" => $res1, "id_invoice_2" => $res2, "id_invoice_3" => $res3));
	if (count($_POST["id_dsd"]) > 0) {
		foreach ($_POST["id_dsd"] as $idx => $nilai) {
			$noms01++;

			$id_dsd			= htmlspecialchars($_POST["id_dsd"][$idx], ENT_QUOTES);
			$tgl_delivered	= htmlspecialchars($_POST["tgl_delivered"][$idx], ENT_QUOTES);
			$refund_tawar	= htmlspecialchars($_POST["refund_tawar"][$idx], ENT_QUOTES);
			$vol_kirim		= htmlspecialchars(str_replace(array(","), array(""), $_POST["vol_kirim"][$idx]), ENT_QUOTES);

			$harga_kirim	= htmlspecialchars(str_replace(array(","), array(""), $_POST["harga_kirim_fix"][$idx]), ENT_QUOTES);

			$harga_dasar	= htmlspecialchars($_POST["harga_dasar"][$idx], ENT_QUOTES);

			$ongkos_angkut	= htmlspecialchars($_POST["ongkos_angkut"][$idx], ENT_QUOTES);

			$ppn			= htmlspecialchars($_POST["ppn"][$idx], ENT_QUOTES);
			$nilai_ppn		= htmlspecialchars($_POST["nilai_ppn"][$idx], ENT_QUOTES);

			$pbbkb			= htmlspecialchars($_POST["pbbkb"][$idx], ENT_QUOTES);

			$jenisnya		= htmlspecialchars($_POST["jenisnya"][$idx], ENT_QUOTES);

			$harga_kirim	= ($harga_kirim ? $harga_kirim : 0);
			$vol_kirim		= ($vol_kirim ? $vol_kirim : 0);

			$sql1 = "SELECT b.tanggal_poc FROM pro_po_ds_detail a JOIN pro_po_customer b ON a.id_poc=b.id_poc WHERE a.id_dsd = '" . $id_dsd . "'";
			$row1 = $con->getRecord($sql1);

			if ($split_invoice == "all_in") {

				$sql4 = "
				insert into pro_invoice_admin_detail(id_invoice_detail, id_invoice, id_dsd, tgl_delivered, vol_kirim, harga_kirim, jenisnya) values 
				('" . $noms01 . "', '" . $res1 . "', '" . $id_dsd . "', '" . tgl_db($tgl_delivered) . "', '" . $vol_kirim . "', '" . $harga_kirim . "', '" . $jenisnya . "')";
				$con->setQuery($sql4);
				$oke  = $oke && !$con->hasError();
			} elseif ($split_invoice == "split_oa") {

				// Harga Dasar + PBBKB + PPN
				$harga_dasar_split_oa = $harga_dasar + $pbbkb + (($harga_dasar + $pbbkb) * $nilai_ppn / 100);
				$sql_split_oa1 = "
				insert into pro_invoice_admin_detail(id_invoice_detail, id_invoice, id_dsd, tgl_delivered, vol_kirim, harga_kirim, jenisnya) values 
				('" . $noms01 . "', '" . $res1 . "', '" . $id_dsd . "', '" . tgl_db($tgl_delivered) . "', '" . $vol_kirim . "', '" . $harga_dasar_split_oa . "', '" . $jenisnya . "')";
				$con->setQuery($sql_split_oa1);
				$oke  = $oke && !$con->hasError();

				// Ongkos Angkut + PPN
				$ongkos_angkut_split_oa = $ongkos_angkut + ($ongkos_angkut * $nilai_ppn / 100);
				$sql_split_oa2 = "
				insert into pro_invoice_admin_detail(id_invoice_detail, id_invoice, id_dsd, tgl_delivered, vol_kirim, harga_kirim, jenisnya) values 
				('" . $noms01 . "', '" . $res2 . "', '" . $id_dsd . "', '" . tgl_db($tgl_delivered) . "', '" . $vol_kirim . "', '" . $ongkos_angkut_split_oa . "', '" . $jenisnya . "')";
				$con->setQuery($sql_split_oa2);
				$oke  = $oke && !$con->hasError();
			} elseif ($split_invoice == "split_pbbkb") {

				// Harga Dasar + OA + PPN
				$harga_dasar_split_pbbkb = ($harga_dasar + $ongkos_angkut) + (($harga_dasar + $ongkos_angkut) * $nilai_ppn / 100);
				$sql_split_pbbkb1 = "
				insert into pro_invoice_admin_detail(id_invoice_detail, id_invoice, id_dsd, tgl_delivered, vol_kirim, harga_kirim, jenisnya) values 
				('" . $noms01 . "', '" . $res1 . "', '" . $id_dsd . "', '" . tgl_db($tgl_delivered) . "', '" . $vol_kirim . "', '" . $harga_dasar_split_pbbkb . "', '" . $jenisnya . "')";
				$con->setQuery($sql_split_pbbkb1);
				$oke  = $oke && !$con->hasError();

				// PBBKB + PPN
				$split_pbbkb = $pbbkb;
				$sql_split_pbbkb2 = "
				insert into pro_invoice_admin_detail(id_invoice_detail, id_invoice, id_dsd, tgl_delivered, vol_kirim, harga_kirim, jenisnya) values 
				('" . $noms01 . "', '" . $res2 . "', '" . $id_dsd . "', '" . tgl_db($tgl_delivered) . "', '" . $vol_kirim . "', '" . $split_pbbkb . "', '" . $jenisnya . "')";
				$con->setQuery($sql_split_pbbkb2);
				$oke  = $oke && !$con->hasError();
			} elseif ($split_invoice == "split_all") {

				// Harga Dasar + PPN
				$harga_dasar_split_all = $harga_dasar + ($harga_dasar * $nilai_ppn / 100);
				$sql_split_all1 = "
				insert into pro_invoice_admin_detail(id_invoice_detail, id_invoice, id_dsd, tgl_delivered, vol_kirim, harga_kirim, jenisnya) values 
				('" . $noms01 . "', '" . $res1 . "', '" . $id_dsd . "', '" . tgl_db($tgl_delivered) . "', '" . $vol_kirim . "', '" . $harga_dasar_split_all . "', '" . $jenisnya . "')";
				$con->setQuery($sql_split_all1);
				$oke  = $oke && !$con->hasError();

				// Ongkos Angkut + PPN
				$ongkos_angkut_split_all = $ongkos_angkut + ($ongkos_angkut * $nilai_ppn / 100);
				$sql_split_all2 = "
				insert into pro_invoice_admin_detail(id_invoice_detail, id_invoice, id_dsd, tgl_delivered, vol_kirim, harga_kirim, jenisnya) values 
				('" . $noms01 . "', '" . $res2 . "', '" . $id_dsd . "', '" . tgl_db($tgl_delivered) . "', '" . $vol_kirim . "', '" . $ongkos_angkut_split_all . "', '" . $jenisnya . "')";
				$con->setQuery($sql_split_all2);
				$oke  = $oke && !$con->hasError();

				// PBBKB + PPN
				$split_pbbkb = $pbbkb;
				$sql_split_all3 = "
				insert into pro_invoice_admin_detail(id_invoice_detail, id_invoice, id_dsd, tgl_delivered, vol_kirim, harga_kirim, jenisnya) values 
				('" . $noms01 . "', '" . $res3 . "', '" . $id_dsd . "', '" . tgl_db($tgl_delivered) . "', '" . $vol_kirim . "', '" . $split_pbbkb . "', '" . $jenisnya . "')";
				$con->setQuery($sql_split_all3);
				$oke  = $oke && !$con->hasError();
			}
			// if ($row1['tanggal_poc'] > '2023-07-29') {
			// 	if ($refund_tawar != 0) {
			// 		$sql_refund = "
			// 		insert into pro_refund(id_invoice, id_dsd, created_at, updated_at) values 
			// 		('" . json_encode($arrayId) . "', '" . $id_dsd . "', NOW(), NOW())";
			// 		$con->setQuery($sql_refund);
			// 		$oke  = $oke && !$con->hasError();
			// 	}
			// }
		}
	}

	$url  = BASE_URL_CLIENT . "/invoice_customer.php";
	if ($oke) {
		$con->commit();
		$con->close();
		header("location: " . $url);
		exit();
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}
	// echo json_encode($noms_inv);
} else if ($act == "update") {
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	$cek1 = "select total_invoice, id_customer from pro_invoice_admin where id_invoice = '" . $idr . "'";
	$row1 = $con->getRecord($cek1);

	$sql1 = "
		update pro_invoice_admin set tgl_invoice = '" . tgl_db($tgl_invoice) . "', total_invoice = '" . $total_invoice . "', is_cetakan = '" . $is_cetakan . "', no_invoice_customer = '" . $no_invoice_customer . "', status_ar = 'notyet', lastupdate_time = '" . $lastupdate_time . "', lastupdate_ip = '" . $lastupdate_ip . "', lastupdate_by = '" . $lastupdate_by . "' where id_invoice = '" . $idr . "'";
	$con->setQuery($sql1);
	$oke  = $oke && !$con->hasError();

	$sql3 = "delete from pro_invoice_admin_detail where id_invoice = '" . $idr . "'";
	$con->setQuery($sql3);
	$oke  = $oke && !$con->hasError();

	$query = "SELECT * FROM pro_invoice_admin WHERE id_invoice = '" . $idr . "'";
	$invoice = $con->getRecord($query);

	$noms01 = 0;
	if (count($_POST["id_dsd"]) > 0) {
		$items2 = [];
		$total_pengiriman = count($_POST["id_dsd"]);
		foreach ($_POST["id_dsd"] as $idx => $nilai) {
			$noms01++;

			$id_dsd			= htmlspecialchars($_POST["id_dsd"][$idx], ENT_QUOTES);
			$tgl_delivered	= htmlspecialchars($_POST["tgl_delivered"][$idx], ENT_QUOTES);
			$vol_kirim		= htmlspecialchars(str_replace(array(","), array(""), $_POST["vol_kirim"][$idx]), ENT_QUOTES);
			$harga_kirim	= htmlspecialchars(str_replace(array(","), array(""), $_POST["harga_kirim"][$idx]), ENT_QUOTES);
			$jenisnya		= htmlspecialchars($_POST["jenisnya"][$idx], ENT_QUOTES);

			$harga_kirim	= ($harga_kirim ? $harga_kirim : 0);
			$vol_kirim		= ($vol_kirim ? $vol_kirim : 0);

			$sql4 = "
					insert into pro_invoice_admin_detail(id_invoice_detail, id_invoice, id_dsd, tgl_delivered, vol_kirim, harga_kirim, jenisnya) values 
					('" . $noms01 . "', '" . $idr . "', '" . $id_dsd . "', '" . tgl_db($tgl_delivered) . "', '" . $vol_kirim . "', '" . $harga_kirim . "', '" . $jenisnya . "')
				";
			$con->setQuery($sql4);
			$oke  = $oke && !$con->hasError();

			$query3 	= "SELECT * FROM pro_invoice_admin_detail WHERE id_dsd = '" . $id_dsd . "' AND id_invoice = '" . $idr . "'";
			$res_dsd 	= $con->getResult($query3);

			$query4 = "SELECT a.id_dsd, IF(c.gabung_oa=1,'gabung_oa',IF(c.gabung_pbbkb=1,'gabung_pbbkb',IF(c.all_in=1 OR c.gabung_pbbkboa=1,'all_in','break_all'))) AS jenis_penawaran FROM pro_po_ds_detail as a JOIN pro_po_customer as b ON a.id_poc=b.id_poc JOIN pro_penawaran as c ON b.id_penawaran=c.id_penawaran WHERE id_dsd = '" . $id_dsd . "'";
			$res4 	= $con->getRecord($query4);
		}
	}

	$sql4 = "update pro_customer_admin_arnya set not_yet = ((not_yet + '" . $total_invoice . "') - '" . $row1['total_invoice'] . "') where id_customer = '" . $id_customer . "'";
	$con->setQuery($sql4);
	$oke  = $oke && !$con->hasError();

	$url  = BASE_URL_CLIENT . "/invoice_customer.php";
	if ($oke) {
		$con->commit();
		$con->close();
		header("location: " . $url);
		exit();
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}
} else if ($act == "bayar") {
	$cek1 = "select total_invoice, total_bayar, id_customer, status_ar from pro_invoice_admin where id_invoice = '" . $idr . "'";
	$row1 = $con->getRecord($cek1);

	$cek2 = "select * from pro_invoice_admin_detail where id_invoice = '" . $idr . "'";
	$row2 = $con->getRecord($cek2);

	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	if (($total_bayar + $total_bayar_potongan) > $row1['total_invoice']) {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "Total Pembayaran Melebihi Total Invoice", BASE_REFERER);
	} else {

		$sql1 = "update pro_invoice_admin set total_bayar = '" . $total_bayar . "', is_lunas='" . $fix_lunas . "', lastupdate_time = '" . $lastupdate_time . "', lastupdate_ip = '" . $lastupdate_ip . "', lastupdate_by = '" . $lastupdate_by . "' where id_invoice = '" . $idr . "'";

		// echo json_encode($fix_lunas);
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();

		$sql3 = "delete from pro_invoice_admin_detail_bayar where id_invoice = '" . $idr . "'";
		$con->setQuery($sql3);
		$oke  = $oke && !$con->hasError();

		$noms01 = 0;
		if (count($_POST["tgl_bayar"]) > 0) {
			foreach ($_POST["tgl_bayar"] as $idx => $nilai) {
				$noms01++;

				$tgl_bayar	= htmlspecialchars($_POST["tgl_bayar"][$idx], ENT_QUOTES);
				$jml_bayar	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["jml_bayar"][$idx]), ENT_QUOTES);

				$jml_bayar	= ($jml_bayar ? $jml_bayar : 0);

				if ($tgl_bayar && $jml_bayar) {
					$sql4 = "
							insert into pro_invoice_admin_detail_bayar(id_invoice_bayar, id_invoice, tgl_bayar, jumlah_bayar) values 
							('" . $noms01 . "', '" . $idr . "', '" . tgl_db($tgl_bayar) . "', '" . $jml_bayar . "')
						";
					$con->setQuery($sql4);
					$oke  = $oke && !$con->hasError();
				}
			}
		}

		if ($fix_lunas == 1) {

			$ambil_dsd = "SELECT id_dsd FROM pro_invoice_admin_detail WHERE id_invoice='" . $idr . "'";
			$row_dsd = $con->getRecord($ambil_dsd);

			$ambil_invoice = "SELECT * FROM pro_invoice_admin_detail WHERE id_dsd='" . $row_dsd['id_dsd'] . "'";
			$res_invoice = $con->getResult($ambil_invoice);

			$status_lunas = true;
			$id_invoice_hsd = "";
			foreach ($res_invoice as $i => $ris) {
				$invoice = "SELECT * FROM pro_invoice_admin WHERE id_invoice = '" . $ris['id_invoice'] . "'";
				$row_invoice = $con->getRecord($invoice);

				if ($row_invoice['is_lunas'] != 1) {
					$status_lunas = false; // ditemukan satu yang belum lunas
				}

				if ($row_invoice['jenis'] == "all_in" || $row_invoice['jenis'] == "harga_dasar" || $row_invoice['jenis'] == "harga_dasar_oa" || $row_invoice['jenis'] == "harga_dasar_pbbkb") {
					$id_invoice_hsd = $row_invoice['id_invoice'];
				}
			}

			if ($status_lunas == true) {
				// UPDATE REFUND
				$sql_refund = "update pro_refund set disposisi = 1 where id_invoice = '" . $id_invoice_hsd . "'";
				$con->setQuery($sql_refund);
				$oke  = $oke && !$con->hasError();

				// UPDATE INCENTIVE
				$sql_incentive = "SELECT a.id_dsd as id_dsdnya, a.id_invoice as id_invoicenya, a.total_incentive, a.disposisi as statusnya, i.nama_customer, i.kode_pelanggan, i.jenis_payment, i.top_payment, i.id_customer as id_customernya, e.alamat_survey, f.nama_prov, g.nama_kab, j.id_user, j.fullname, j.id_role, h.nomor_poc, h.tanggal_poc, h.id_poc as id_pocnya, h.produk_poc, b.volume_po, k.refund_tawar, l.nama_area, l.id_master as id_areanya, m.wilayah_angkut, k.id_penawaran, k.harga_asli as harga_dasarnya, k.harga_tier, k.tier, ppdd.tanggal_delivered, n.no_invoice, n.tgl_invoice_dikirim, n.tgl_invoice, k.masa_awal, k.masa_akhir, CONCAT(o.jenis_produk,' - ', o.merk_dagang) as nama_produk, p.vol_kirim as volume_invoice, n.is_lunas, q.nama_cabang, (SELECT SUM(vol_kirim) FROM pro_invoice_admin_detail WHERE id_invoice=a.id_invoice) as total_vol_invoice
				from pro_incentive a 
				join pro_po_ds_detail ppdd on ppdd.id_dsd = a.id_dsd
				join pro_po_detail b on ppdd.id_pod = b.id_pod
				join pro_pr_detail c on ppdd.id_prd = c.id_prd 
				join pro_po_customer_plan d on ppdd.id_plan = d.id_plan 
				join pro_customer_lcr e on d.id_lcr = e.id_lcr
				join pro_master_provinsi f on e.prov_survey = f.id_prov 
				join pro_master_kabupaten g on e.kab_survey = g.id_kab
				join pro_po_customer h on d.id_poc = h.id_poc 
				join pro_customer i on h.id_customer = i.id_customer 
				join acl_user j on a.id_marketing = j.id_user 
				join pro_penawaran k on h.id_penawaran = k.id_penawaran	
				join pro_master_area l on k.id_area = l.id_master 
				join pro_master_wilayah_angkut m on e.id_wil_oa = m.id_master and e.prov_survey = m.id_prov and e.kab_survey = m.id_kab
				join pro_invoice_admin n on a.id_invoice = n.id_invoice
				join pro_master_produk o on o.id_master=h.produk_poc
				join pro_invoice_admin_detail p on a.id_invoice=p.id_invoice
				join pro_master_cabang q on q.id_master=i.id_wilayah
				WHERE k.created_time > '2025-03-01' AND n.id_invoice='" . $id_invoice_hsd . "'";
				$row_incentive = $con->getRecord($sql_incentive);

				if ($row_incentive) {
					if ($row_incentive["harga_tier"] == 0) {
						$tiernya = "Harga Tier 0";
						$harganya = "";
					} else {
						$tiernya = "Tier " . $row_incentive['tier'];
						$harganya = number_format($row_incentive["harga_tier"]);
					}

					$sql_bayar_1 = "SELECT MAX(tgl_bayar) as tanggal_bayar FROM pro_invoice_admin_detail_bayar WHERE id_invoice='" . $id_invoice_hsd . "'";
					$row_bayar_1 = $con->getRecord($sql_bayar_1);

					$due_date_indo = tgl_indo(date('Y-m-d', strtotime($row_incentive['tgl_invoice_dikirim'] . "+" . $row_incentive['top_payment'] . " days")));
					$due_date = date('Y-m-d', strtotime($row_incentive['tgl_invoice_dikirim'] . "+" . $row_incentive['top_payment'] . " days"));

					$cek_top = "SELECT * FROM pro_top_incentive ORDER BY id ASC";
					$res_top = $con->getResult($cek_top);

					$cek_non_penerima = "SELECT id_user FROM pro_non_penerima_incentive WHERE id_user = '" . $row_incentive['id_user'] . "'";
					$res_non_penerima = $con->getRecord($cek_non_penerima);

					$week1 = 0;
					$week2 = 0;
					$week3 = 0;
					$week4 = 0;
					$week5 = 0;
					$week6 = 0;
					$total_incentive_fix = 0;

					foreach ($res_top as $rt) {
						if ($rt['top'] == "0") {
							$term = "CBD";
							$keterangan1 = $rt['keterangan'];
							$top = 0;
						} elseif ($rt['top'] == "14") {
							$term = "14";
							$keterangan2 = $rt['keterangan'];
							$top1 = 14;
						} elseif ($rt['top'] == "35") {
							$term = "35";
							$keterangan3 = $rt['keterangan'];
							$top2 = 21;
						} elseif ($rt['top'] == "54") {
							$term = "54";
							$keterangan4 = $rt['keterangan'];
							$top3 = 19;
						} elseif ($rt['top'] == "75") {
							$term = "75";
							$keterangan5 = $rt['keterangan'];
							$top4 = 21;
						} elseif ($rt['top'] == "76") {
							$term = "76";
							$keterangan6 = $rt['keterangan'];
							$top5 = 1;
						}
					}

					$due_date_week2 = date('Y-m-d', strtotime($row_incentive['tgl_invoice_dikirim'] . "+" . $top1 . " days"));
					$due_date_week3 = date('Y-m-d', strtotime($due_date_week2 . "+" . $top2 . " days"));
					$due_date_week4 = date('Y-m-d', strtotime($due_date_week3 . "+" . $top3 . " days"));
					$due_date_week5 = date('Y-m-d', strtotime($due_date_week4 . "+" . $top4 . " days"));
					$due_date_week6 = date('Y-m-d', strtotime($due_date_week5 . "+" . $top5 . " days"));

					if ($row_incentive['jenis_payment'] == "CBD") {
						$cek_point = "SELECT * FROM pro_point_incentive WHERE id_role='" . $row_incentive['id_role'] . "' AND id_top ='1' AND tier='" . $tiernya . "'";
						$res_point = $con->getRecord($cek_point);

						if ($res_non_penerima) {
							$total_point = 0;
						} else {
							$total_point = $res_point['point'];
						}

						$week1 += $row_incentive['total_vol_invoice'] * $total_point;
						$week2 += 0;
						$week3 += 0;
						$week4 += 0;
						$week5 += 0;
						$week6 += 0;
						$total_incentive_fix = $week1;
					} else {
						if ($row_bayar_1['tanggal_bayar'] <= $due_date_week2) {
							$cek_point = "SELECT * FROM pro_point_incentive WHERE id_role='" . $row_incentive['id_role'] . "' AND id_top ='2' AND tier='" . $tiernya . "'";
							$res_point = $con->getRecord($cek_point);

							if ($res_non_penerima) {
								$total_point = 0;
							} else {
								$total_point = $res_point['point'];
							}

							$week1 += 0;
							$week2 += $row_incentive['total_vol_invoice'] * $total_point;
							$week3 += 0;
							$week4 += 0;
							$week5 += 0;
							$week6 += 0;
							$total_incentive_fix = $week2;
						} elseif ($row_bayar_1['tanggal_bayar'] > $due_date_week2 && $row_bayar_1['tanggal_bayar'] <= $due_date_week3) {
							$cek_point = "SELECT * FROM pro_point_incentive WHERE id_role='" . $row_incentive['id_role'] . "' AND id_top ='3' AND tier='" . $tiernya . "'";
							$res_point = $con->getRecord($cek_point);

							if ($res_non_penerima) {
								$total_point = 0;
							} else {
								$total_point = $res_point['point'];
							}

							$week1 += 0;
							$week2 += 0;
							$week3 += $row_incentive['total_vol_invoice'] * $total_point;
							$week4 += 0;
							$week5 += 0;
							$week6 += 0;
							$total_incentive_fix = $week3;
						} elseif ($row_bayar_1['tanggal_bayar'] > $due_date_week3 && $row_bayar_1['tanggal_bayar'] <= $due_date_week4) {
							$cek_point = "SELECT * FROM pro_point_incentive WHERE id_role='" . $row_incentive['id_role'] . "' AND id_top ='4' AND tier='" . $tiernya . "'";
							$res_point = $con->getRecord($cek_point);

							if ($res_non_penerima) {
								$total_point = 0;
							} else {
								$total_point = $res_point['point'];
							}

							$week1 += 0;
							$week2 += 0;
							$week3 += 0;
							$week4 += $row_incentive['total_vol_invoice'] * $total_point;
							$week5 += 0;
							$week6 += 0;
							$total_incentive_fix = $week4;
						} elseif ($row_bayar_1['tanggal_bayar'] > $due_date_week4 && $row_bayar_1['tanggal_bayar'] <= $due_date_week5) {
							$cek_point = "SELECT * FROM pro_point_incentive WHERE id_role='" . $row_incentive['id_role'] . "' AND id_top ='5' AND tier='" . $tiernya . "'";
							$res_point = $con->getRecord($cek_point);

							if ($res_non_penerima) {
								$total_point = 0;
							} else {
								$total_point = $res_point['point'];
							}

							$week1 += 0;
							$week2 += 0;
							$week3 += 0;
							$week4 += 0;
							$week5 += $row_incentive['total_vol_invoice'] * $total_point;
							$week6 += 0;
							$total_incentive_fix = $week5;
						} elseif ($row_bayar_1['tanggal_bayar'] >= $due_date_week6) {
							$res_point = [
								'point' => 0
							];

							$week1 += 0;
							$week2 += 0;
							$week3 += 0;
							$week4 += 0;
							$week5 += 0;
							$week6 += 0;
							$total_incentive_fix = $week6;
						}
					}

					$sql_update_incentive = "UPDATE pro_incentive set volume = " . $row_incentive['total_vol_invoice'] . ", harga_dasar = " . $row_incentive['harga_dasarnya'] . ", point_incentive = " . $total_point . ", tier = '" . $tiernya . "', total_incentive = " . round($total_incentive_fix) . ", disposisi = 1, updated_at = NOW() where id_invoice = '" . $id_invoice_hsd . "'";
					$con->setQuery($sql_update_incentive);
					$oke  = $oke && !$con->hasError();
				}
			}

			// UPDATE AR
			if ($row1['status_ar'] == 'notyet') {
				$sql4 = "update pro_customer_admin_arnya set not_yet = ((not_yet - '" . $row1['total_invoice'] . "')) where id_customer = '" . $row1['id_customer'] . "'";
			} else if ($row1['status_ar'] == 'ov_up_07') {
				$sql4 = "update pro_customer_admin_arnya set ov_up_07 = ((ov_up_07 - '" . $row1['total_invoice'] . "')) where id_customer = '" . $row1['id_customer'] . "'";
			} else if ($row1['status_ar'] == 'ov_under_30') {
				$sql4 = "update pro_customer_admin_arnya set ov_under_30 = ((ov_under_30 - '" . $row1['total_invoice'] . "')) where id_customer = '" . $row1['id_customer'] . "'";
			} else if ($row1['status_ar'] == 'ov_under_60') {
				$sql4 = "update pro_customer_admin_arnya set ov_under_60 = ((ov_under_60 - '" . $row1['total_invoice'] . "')) where id_customer = '" . $row1['id_customer'] . "'";
			} else if ($row1['status_ar'] == 'ov_under_90') {
				$sql4 = "update pro_customer_admin_arnya set ov_under_90 = ((ov_under_90 - '" . $row1['total_invoice'] . "')) where id_customer = '" . $row1['id_customer'] . "'";
			} else if ($row1['status_ar'] == 'ov_up_90') {
				$sql4 = "update pro_customer_admin_arnya set ov_up_90 = ((ov_up_90 - '" . $row1['total_invoice'] . "')) where id_customer = '" . $row1['id_customer'] . "'";
			}
			$con->setQuery($sql4);
			$oke  = $oke && !$con->hasError();

			if (count($_POST["kategori_potongan"]) > 0) {
				foreach ($_POST["kategori_potongan"] as $idx => $nilai) {

					$kategori_potongan	= htmlspecialchars($_POST["kategori_potongan"][$idx], ENT_QUOTES);
					$jml_bayar_potongan	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["jml_bayar_potongan"][$idx]), ENT_QUOTES);

					$jml_bayar_potongan	= ($jml_bayar_potongan ? $jml_bayar_potongan : 0);

					$sql_bupot = "
							insert into pro_invoice_bukti_potong(id_invoice, kategori, nominal, created_at) values 
							('" . $idr . "', '" . $kategori_potongan . "', '" . $jml_bayar_potongan . "' , NOW())
						";
					$con->setQuery($sql_bupot);
					$oke  = $oke && !$con->hasError();
				}
			}

			// echo json_encode($id_invoice_hsd);
		} else {
			if ($row1['status_ar'] == 'notyet') {
				$sql4 = "update pro_customer_admin_arnya set not_yet = ((not_yet - '" . $total_bayar . "') + '" . $row1['total_bayar'] . "') where id_customer = '" . $row1['id_customer'] . "'";
			} else if ($row1['status_ar'] == 'ov_up_07') {
				$sql4 = "update pro_customer_admin_arnya set ov_up_07 = ((ov_up_07 - '" . $total_bayar . "') + '" . $row1['total_bayar'] . "') where id_customer = '" . $row1['id_customer'] . "'";
			} else if ($row1['status_ar'] == 'ov_under_30') {
				$sql4 = "update pro_customer_admin_arnya set ov_under_30 = ((ov_under_30 - '" . $total_bayar . "') + '" . $row1['total_bayar'] . "') where id_customer = '" . $row1['id_customer'] . "'";
			} else if ($row1['status_ar'] == 'ov_under_60') {
				$sql4 = "update pro_customer_admin_arnya set ov_under_60 = ((ov_under_60 - '" . $total_bayar . "') + '" . $row1['total_bayar'] . "') where id_customer = '" . $row1['id_customer'] . "'";
			} else if ($row1['status_ar'] == 'ov_under_90') {
				$sql4 = "update pro_customer_admin_arnya set ov_under_90 = ((ov_under_90 - '" . $total_bayar . "') + '" . $row1['total_bayar'] . "') where id_customer = '" . $row1['id_customer'] . "'";
			} else if ($row1['status_ar'] == 'ov_up_90') {
				$sql4 = "update pro_customer_admin_arnya set ov_up_90 = ((ov_up_90 - '" . $total_bayar . "') + '" . $row1['total_bayar'] . "') where id_customer = '" . $row1['id_customer'] . "'";
			}
			$con->setQuery($sql4);
			$oke  = $oke && !$con->hasError();
		}

		$url  = BASE_URL_CLIENT . "/invoice_customer.php";
		if ($oke) {
			$con->commit();
			$con->close();
			header("location: " . $url);
			exit();
		} else {
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
	}


	// echo json_encode($lunas);
} else if ($act == "hapus") {
	$arr	= array();
	$param 	= htmlspecialchars(paramDecrypt($_POST["param"]), ENT_QUOTES);
	$post 	= explode("#|#", $param);
	$file	= htmlspecialchars($post[0], ENT_QUOTES);
	$id1	= htmlspecialchars($post[1], ENT_QUOTES);

	$sql1 = "select total_invoice, id_customer, no_invoice from pro_invoice_admin where id_invoice = '" . $id1 . "'";
	$row1 = $con->getRecord($sql1);

	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	$sql2 = "delete from pro_invoice_admin where id_invoice = '" . $id1 . "'";
	$con->setQuery($sql2);
	$oke  = $oke && !$con->hasError();

	$sql3 = "update pro_customer_admin_arnya set not_yet = not_yet - '" . $row1['total_invoice'] . "' where id_customer = '" . $row1['id_customer'] . "'";
	$con->setQuery($sql3);
	$oke  = $oke && !$con->hasError();

	// $sql4 = "delete from pro_refund where id_invoice = '" . $id1 . "'";
	// $con->setQuery($sql4);
	// $oke  = $oke && !$con->hasError();

	if ($oke) {
		$con->commit();
		$con->close();
		$result = [
			"status" 	=> true,
			"pesan" 	=> "Berhasil di hapus",
		];
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$result = [
			"status" 	=> false,
			"pesan" 	=> "Gagal di hapus",
		];
	}
	echo json_encode($result);
} else if ($act == "update_tanggal") {

	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	$sql1 = "
		update pro_invoice_admin set tgl_invoice_dikirim = '" . tgl_db($tgl_invoice_dikirim) . "' where id_invoice = '" . $id_invoice_enc . "'";
	$con->setQuery($sql1);
	$oke  = $oke && !$con->hasError();

	$url  = BASE_URL_CLIENT . "/invoice_customer.php";
	if ($oke) {
		$cek1 = "select * from pro_invoice_admin_detail where id_invoice = '" . $id_invoice_enc . "'";
		$row1 = $con->getRecord($cek1);

		$cek2 = "select * from pro_refund where id_invoice = '" . $id_invoice_enc . "'";
		$row2 = $con->getRecord($cek2);

		$cek3 = "select * from pro_invoice_admin where id_invoice = '" . $id_invoice_enc . "'";
		$row3 = $con->getRecord($cek3);

		if ($row2 == "" || $row2 == NULL) {
			if ($row3['tgl_invoice'] > "2024-10-06") {
				if ($row3['jenis'] == "all_in" || $row3['jenis'] == "harga_dasar_pbbkb" || $row3['jenis'] == "harga_dasar_oa" || $row3['jenis'] == "harga_dasar") {
					if ($refund != 0) {
						$sql2 = "
							insert into pro_refund(id_invoice, id_dsd, created_at, updated_at) values 
							('" . $id_invoice_enc . "','" . $row1['id_dsd'] . "', '" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") . "')";
						$con->setQuery($sql2);
						$oke  = $oke && !$con->hasError();
					}
				}
			}
		}

		// Array semua Id dengan username office, bm dan om
		// $arrayId = ['78', '79', '80', '81', '106', '107', '108', '124', '125', '192', '208', '209', '210', '345', '444', '523', '559', '561', '571', '576', '579'];

		$ambil_id_mkt = "SELECT c.id_marketing FROM pro_po_ds_detail a JOIN pro_po_customer b ON a.id_poc=b.id_poc JOIN pro_customer c ON b.id_customer=c.id_customer WHERE a.id_dsd='" . $row1['id_dsd'] . "'";
		$row_id_mkt = $con->getRecord($ambil_id_mkt);

		$cek_incentive = "SELECT * FROM pro_incentive WHERE id_invoice = '" . $id_invoice_enc . "'";
		$row_incentive = $con->getRecord($cek_incentive);

		// Pengecekan jika marketing bukan office, bm dan om tidak masuk incentive
		if ($row_incentive == null || $row_incentive == "") {
			if ($row3['jenis'] == "all_in" || $row3['jenis'] == "harga_dasar" || $row3['jenis'] == "harga_dasar_oa" || $row3['jenis'] == "harga_dasar_pbbkb") {
				$sql3 = "
					insert into pro_incentive(id_invoice, id_dsd, id_marketing, created_at, updated_at) values 
					('" . $id_invoice_enc . "','" . $row1['id_dsd'] . "', '" . $row_id_mkt['id_marketing'] . "', '" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") . "')";
				$con->setQuery($sql3);
				$oke  = $oke && !$con->hasError();
			}
		}

		$con->commit();
		$con->close();
		$flash->add("success", "Data berhasil di update", BASE_REFERER);
		header("location: " . $url);
		exit();
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "Data gagal di update", BASE_REFERER);
	}
} else if ($act == "detail_pembayaran") {
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	$id_invoice = htmlspecialchars($_POST["param"], ENT_QUOTES);

	$data_invoice = "SELECT * FROM pro_invoice_admin WHERE id_invoice = '" . $id_invoice . "'";
	$res_invoice = $con->getRecord($data_invoice);

	$data_invoice_pembayaran = "SELECT * FROM pro_invoice_admin_detail_bayar WHERE id_invoice = '" . $id_invoice . "'";
	$res_pembayaran = $con->getResult($data_invoice_pembayaran);

	$data_invoice_potongan = "SELECT * FROM pro_invoice_bukti_potong WHERE id_invoice = '" . $id_invoice . "'";
	$res_potongan = $con->getResult($data_invoice_potongan);

	if ($oke) {
		$result = [
			"status" 					=> 200,
			"data_invoice" 				=> $res_invoice,
			"data_invoice_pembayaran" 	=> $res_pembayaran,
			"data_invoice_potongan" 	=> $res_potongan,
		];
	} else {
		$result = [
			"status" 	=> 100,
		];
	}
	echo json_encode($result);
}

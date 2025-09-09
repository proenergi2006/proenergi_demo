
<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
require_once($public_base_directory . "/libraries/helper/curl.php");
load_helper("autoload", "htmlawed");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;
$enk  	= decode($_SERVER['REQUEST_URI']);
$act	= !isset($enk['act']) ? htmlspecialchars($_POST["act"], ENT_QUOTES) : $enk['act'];
$idr	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
$id_invoice_enc = paramDecrypt($_POST["id_invoice_encrypt"]);
$refund = paramDecrypt($_POST["refund"]);

$id_cabang = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

$queryget_cabang = "SELECT * FROM pro_master_cabang WHERE id_master = '" . $id_cabang . "'";
$rowget_cabang = $con->getRecord($queryget_cabang);

$id_customer			= htmlspecialchars($_POST["id_customer"], ENT_QUOTES);
$no_invoice_manual		= htmlspecialchars($_POST["no_invoice"], ENT_QUOTES);
$no_invoice_customer 	= htmlspecialchars($_POST["no_invoice_customer"], ENT_QUOTES);
$tgl_invoice 			= htmlspecialchars($_POST["tgl_invoice"], ENT_QUOTES);
$tgl_invoice_dikirim 	= htmlspecialchars($_POST["tgl_invoice_dikirim"], ENT_QUOTES);
$split_invoice 			= htmlspecialchars($_POST["split_invoice"], ENT_QUOTES);
$nomor_po_oa 			= htmlspecialchars($_POST["no_po_splitoa"], ENT_QUOTES);
$nomor_po_pbbkb 		= htmlspecialchars($_POST["no_po_splitpbbkb"], ENT_QUOTES);
$tipe					= htmlspecialchars($_POST["tipe"], ENT_QUOTES);
$ketentuan				= htmlspecialchars($_POST["next_month"], ENT_QUOTES);
$tgl_delivered			= htmlspecialchars($_POST["tanggal"], ENT_QUOTES);
$tgl_kirim_awal			= htmlspecialchars($_POST["tgl_kirim_awal"], ENT_QUOTES);
$tgl_kirim_akhir		= htmlspecialchars($_POST["tgl_kirim_akhir"], ENT_QUOTES);
$lunas					= $_POST["lunas"];
$is_cetakan				= $_POST["cetakan_invoice"];
// $kode_oa				= htmlspecialchars($_POST["kode_oa"], ENT_QUOTES);
// $kode_pbbkb				= htmlspecialchars($_POST["kode_pbbkb"], ENT_QUOTES);

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

// $data_item_post = array();
// if (!empty($kode_pbbkb) && !empty($kode_oa)) {
// 	$data_item_post[] = ['kode' => $kode_pbbkb, 'keterangan' => 'kode_pbbkb'];
// 	$data_item_post[] = ['kode' => $kode_oa, 'keterangan' => 'kode_oa'];
// }
// if (!empty($kode_pbbkb) && $split_invoice != "split_pbbkb") {
// 	$data_item_post[] = ['kode' => $kode_pbbkb, 'keterangan' => 'kode_pbbkb'];
// }

// if (!empty($kode_oa) && $split_invoice != "split_oa") {
// 	$data_item_post[] = ['kode' => $kode_oa, 'keterangan' => 'kode_oa'];
// }

$arrRomawi 			= array("1" => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");


if ($act == "add") {
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	$get_kodecust = "select kode_pelanggan FROM pro_customer WHERE id_customer='" . $id_customer . "'";
	$kodecust = $con->getRecord($get_kodecust);

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

		$sql4 = "UPDATE pro_customer_admin_arnya SET not_yet = not_yet + '" . round($total_invoice) . "' WHERE id_customer = '" . $id_customer . "'";
		$con->setQuery($sql4);
		$oke  = $oke && !$con->hasError();

		$update_cl = "UPDATE pro_customer SET credit_limit_reserved = credit_limit_reserved - round($total_invoice), credit_limit_used = credit_limit_used + round($total_invoice) WHERE id_customer = '" . $id_customer . "'";
		$con->setQuery($update_cl);
		$oke  = $oke && !$con->hasError();

		$history_ar_customer = "INSERT into pro_history_ar_customer(id_invoice, kategori, keterangan, nominal, created_time, created_by) values ('" . $res1 . "', '2', 'Create Invoice', -round($total_invoice), NOW(), '" . $lastupdate_by . "')";
		$con->setQuery($history_ar_customer);
		$oke = $oke && !$con->hasError();
	} elseif ($split_invoice == "split_oa") {

		// Harga Dasar + PBBKB + PPN x Volume Kirim
		$sql_split_oa1 = "
		insert into pro_invoice_admin(id_customer, no_invoice, tgl_invoice, tgl_kirim_awal, tgl_kirim_akhir, total_invoice, is_cetakan, no_invoice_customer, status_ar, jenis, id_approval,
		created_time, created_ip, created_by) values 
		('" . $id_customer . "', '" . $noms_inv . "', '" . tgl_db($tgl_invoice) . "', '" . tgl_db($tgl_delivered_awal) . "', '" . tgl_db($tgl_delivered_awal) . "', '" . round($total_invoice_harga_dasar_pbbkb) . "', '" . $is_cetakan . "','" . $no_invoice_customer . "', 'notyet', 'harga_dasar_pbbkb', '" . $approval['id_master'] . "',
		'" . $created_time . "', '" . $created_ip . "', '" . $created_by . "')";
		$res1 = $con->setQuery($sql_split_oa1);
		$oke  = $oke && !$con->hasError();

		$history_ar_customer = "INSERT into pro_history_ar_customer(id_invoice, kategori, keterangan, nominal, created_time, created_by) values ('" . $res1 . "', '2', 'Create Invoice', -round($total_invoice_harga_dasar_pbbkb), NOW(), '" . $lastupdate_by . "')";
		$con->setQuery($history_ar_customer);
		$oke = $oke && !$con->hasError();

		// Ongkos Kirim + PPN x Volume Kirim
		$noms_inv_split_oa = $noms_inv . "A";
		$sql_split_oa2 = "
		insert into pro_invoice_admin(id_customer, no_invoice, tgl_invoice, tgl_kirim_awal, tgl_kirim_akhir, total_invoice, is_cetakan, no_invoice_customer, no_po_splitoa, status_ar, jenis, id_approval,
		created_time, created_ip, created_by) values 
		('" . $id_customer . "', '" . $noms_inv_split_oa . "', '" . tgl_db($tgl_invoice) . "', '" . tgl_db($tgl_delivered_awal) . "', '" . tgl_db($tgl_delivered_awal) . "', '" . round($total_invoice_ongkos_angkut) . "', '" . $is_cetakan . "','" . $no_invoice_customer . "', '" . $nomor_po_oa . "', 'notyet', 'split_oa', '" . $approval['id_master'] . "',
		'" . $created_time . "', '" . $created_ip . "', '" . $created_by . "')";
		$res2 = $con->setQuery($sql_split_oa2);
		$oke  = $oke && !$con->hasError();

		$history_ar_customer2 = "INSERT into pro_history_ar_customer(id_invoice, kategori, keterangan, nominal, created_time, created_by) values ('" . $res2 . "', '2', 'Create Invoice', -round($total_invoice_ongkos_angkut), NOW(), '" . $lastupdate_by . "')";
		$con->setQuery($history_ar_customer2);
		$oke = $oke && !$con->hasError();

		$total_invoice_split_oa = $total_invoice_harga_dasar_pbbkb + $total_invoice_ongkos_angkut;

		$sql4 = "UPDATE pro_customer_admin_arnya SET not_yet = not_yet + '" . round($total_invoice_split_oa) . "' WHERE id_customer = '" . $id_customer . "'";
		$con->setQuery($sql4);
		$oke  = $oke && !$con->hasError();

		$update_cl = "UPDATE pro_customer SET credit_limit_reserved = credit_limit_reserved - round($total_invoice_split_oa), credit_limit_used = credit_limit_used + round($total_invoice_split_oa) WHERE id_customer = '" . $id_customer . "'";
		$con->setQuery($update_cl);
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

		$history_ar_customer = "INSERT into pro_history_ar_customer(id_invoice, kategori, keterangan, nominal, created_time, created_by) values ('" . $res1 . "', '2', 'Create Invoice', -round($total_invoice_harga_dasar_oa), NOW(), '" . $lastupdate_by . "')";
		$con->setQuery($history_ar_customer);
		$oke = $oke && !$con->hasError();

		// PBBKB x Volume Kirim
		$noms_inv_split_pbbkb = $noms_inv . "B";
		$sql_split_pbbkb2 = "
		insert into pro_invoice_admin(id_customer, no_invoice, tgl_invoice, tgl_kirim_awal, tgl_kirim_akhir, total_invoice, is_cetakan, no_invoice_customer, no_po_splitpbbkb, status_ar, jenis, id_approval,
		created_time, created_ip, created_by) values 
		('" . $id_customer . "', '" . $noms_inv_split_pbbkb . "', '" . tgl_db($tgl_invoice) . "', '" . tgl_db($tgl_delivered_awal) . "', '" . tgl_db($tgl_delivered_awal) . "', '" . round($total_invoice_pbbkb) . "', '" . $is_cetakan . "','" . $no_invoice_customer . "', '" . $nomor_po_pbbkb . "', 'notyet', 'split_pbbkb', '" . $approval['id_master'] . "',
		'" . $created_time . "', '" . $created_ip . "', '" . $created_by . "')";
		$res2 = $con->setQuery($sql_split_pbbkb2);
		$oke  = $oke && !$con->hasError();

		$history_ar_customer2 = "INSERT into pro_history_ar_customer(id_invoice, kategori, keterangan, nominal, created_time, created_by) values ('" . $res2 . "', '2', 'Create Invoice', -round($total_invoice_pbbkb), NOW(), '" . $lastupdate_by . "')";
		$con->setQuery($history_ar_customer2);
		$oke = $oke && !$con->hasError();

		$total_invoice_split_pbbkb = $total_invoice_harga_dasar_oa + $total_invoice_pbbkb;

		$sql4 = "UPDATE pro_customer_admin_arnya SET not_yet = not_yet + '" . round($total_invoice_split_pbbkb) . "' WHERE id_customer = '" . $id_customer . "'";
		$con->setQuery($sql4);
		$oke  = $oke && !$con->hasError();

		$update_cl = "UPDATE pro_customer SET credit_limit_reserved = credit_limit_reserved - round($total_invoice_split_pbbkb), credit_limit_used = credit_limit_used + round($total_invoice_split_pbbkb) WHERE id_customer = '" . $id_customer . "'";
		$con->setQuery($update_cl);
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

		$history_ar_customer = "INSERT into pro_history_ar_customer(id_invoice, kategori, keterangan, nominal, created_time, created_by) values ('" . $res1 . "', '2', 'Create Invoice', -round($total_invoice_harga_dasar), NOW(), '" . $lastupdate_by . "')";
		$con->setQuery($history_ar_customer);
		$oke = $oke && !$con->hasError();

		// Ongkos Kirim + PPN x Volume Kirim
		$noms_inv_split_oa = $noms_inv . "A";
		$sql_split_all2 = "
		insert into pro_invoice_admin(id_customer, no_invoice, tgl_invoice, tgl_kirim_awal, tgl_kirim_akhir, total_invoice, is_cetakan, no_invoice_customer, no_po_splitoa, status_ar, jenis, id_approval,
		created_time, created_ip, created_by) values 
		('" . $id_customer . "', '" . $noms_inv_split_oa . "', '" . tgl_db($tgl_invoice) . "', '" . tgl_db($tgl_delivered_awal) . "', '" . tgl_db($tgl_delivered_awal) . "', '" . round($total_invoice_ongkos_angkut) . "', '" . $is_cetakan . "','" . $no_invoice_customer . "', '" . $nomor_po_oa . "', 'notyet', 'split_oa', '" . $approval['id_master'] . "',
		'" . $created_time . "', '" . $created_ip . "', '" . $created_by . "')";
		$res2 = $con->setQuery($sql_split_all2);
		$oke  = $oke && !$con->hasError();

		$history_ar_customer2 = "INSERT into pro_history_ar_customer(id_invoice, kategori, keterangan, nominal, created_time, created_by) values ('" . $res2 . "', '2', 'Create Invoice', -round($total_invoice_ongkos_angkut), NOW(), '" . $lastupdate_by . "')";
		$con->setQuery($history_ar_customer2);
		$oke = $oke && !$con->hasError();

		// PBBKB x Volume Kirim
		$noms_inv_split_pbbkb = $noms_inv . "B";
		$sql_split_all3 = "
		insert into pro_invoice_admin(id_customer, no_invoice, tgl_invoice, tgl_kirim_awal, tgl_kirim_akhir, total_invoice, is_cetakan, no_invoice_customer, no_po_splitpbbkb, status_ar, jenis, id_approval,
		created_time, created_ip, created_by) values 
		('" . $id_customer . "', '" . $noms_inv_split_pbbkb . "', '" . tgl_db($tgl_invoice) . "', '" . tgl_db($tgl_delivered_awal) . "', '" . tgl_db($tgl_delivered_awal) . "', '" . round($total_invoice_pbbkb) . "', '" . $is_cetakan . "','" . $no_invoice_customer . "', '" . $nomor_po_pbbkb . "', 'notyet', 'split_pbbkb', '" . $approval['id_master'] . "',
		'" . $created_time . "', '" . $created_ip . "', '" . $created_by . "')";
		$res3 = $con->setQuery($sql_split_all3);
		$oke  = $oke && !$con->hasError();

		$history_ar_customer3 = "INSERT into pro_history_ar_customer(id_invoice, kategori, keterangan, nominal, created_time, created_by) values ('" . $res3 . "', '2', 'Create Invoice', -round($total_invoice_pbbkb), NOW(), '" . $lastupdate_by . "')";
		$con->setQuery($history_ar_customer3);
		$oke = $oke && !$con->hasError();

		$total_invoice_split_all = $total_invoice_harga_dasar + $total_invoice_ongkos_angkut + $total_invoice_pbbkb;

		$sql4 = "UPDATE pro_customer_admin_arnya SET not_yet = not_yet + '" . round($total_invoice_split_all) . "' WHERE id_customer = '" . $id_customer . "'";
		$con->setQuery($sql4);
		$oke  = $oke && !$con->hasError();

		$update_cl = "UPDATE pro_customer SET credit_limit_reserved = credit_limit_reserved - round($total_invoice_split_all), credit_limit_used = credit_limit_used + round($total_invoice_split_all) WHERE id_customer = '" . $id_customer . "'";
		$con->setQuery($update_cl);
		$oke  = $oke && !$con->hasError();
	}

	$sql3 = "delete from pro_invoice_admin_detail where id_invoice = '" . $res1 . "'";
	$con->setQuery($sql3);
	$oke  = $oke && !$con->hasError();

	$noms01 = 0;
	if (count($_POST["id_dsd"]) > 0) {
		$detailItems 	= [];
		$detailItems_oa = [];
		$detailItems_pbbkb = [];
		$total_pengiriman = count($_POST["id_dsd"]);
		foreach ($_POST["id_dsd"] as $idx => $nilai) {
			$noms01++;
			$data_item 		= [];
			$data_item2		= [];
			$id_dsd			= htmlspecialchars($_POST["id_dsd"][$idx], ENT_QUOTES);
			$pembulatan		= htmlspecialchars($_POST["pembulatan"][$idx], ENT_QUOTES);
			$tgl_delivered	= htmlspecialchars($_POST["tgl_delivered"][$idx], ENT_QUOTES);
			$refund_tawar	= htmlspecialchars($_POST["refund_tawar"][$idx], ENT_QUOTES);
			$vol_kirim		= htmlspecialchars(str_replace(array(","), array(""), $_POST["vol_kirim"][$idx]), ENT_QUOTES);

			$discount		= htmlspecialchars(str_replace(array(","), array(""), $_POST["discount"][$idx]), ENT_QUOTES);

			$harga_kirim	= htmlspecialchars(str_replace(array(","), array(""), $_POST["harga_kirim_fix"][$idx]), ENT_QUOTES);

			$harga_dasar	= htmlspecialchars($_POST["harga_dasar"][$idx], ENT_QUOTES);

			$ongkos_angkut	= htmlspecialchars($_POST["ongkos_angkut"][$idx], ENT_QUOTES);

			$ppn			= htmlspecialchars($_POST["ppn"][$idx], ENT_QUOTES);
			$nilai_ppn		= htmlspecialchars($_POST["nilai_ppn"][$idx], ENT_QUOTES);

			$pbbkb			= htmlspecialchars($_POST["pbbkb"][$idx], ENT_QUOTES);

			$jenisnya		= htmlspecialchars($_POST["jenisnya"][$idx], ENT_QUOTES);

			$kategori		= htmlspecialchars($_POST["kategori"][$idx], ENT_QUOTES);

			$harga_kirim	= ($harga_kirim ? $harga_kirim : 0);
			$vol_kirim		= ($vol_kirim ? $vol_kirim : 0);
			$discount		= ($discount ? $discount : 0);

			$get_idprd = "select * FROM pro_po_ds_detail WHERE id_dsd='" . $id_dsd . "'";
			$id_prds = $con->getRecord($get_idprd);

			$get_id_so_accurate = "select * FROM pro_po_customer_plan WHERE id_plan='" . $id_prds['id_plan'] . "'";
			$data_plan = $con->getRecord($get_id_so_accurate);

			$get_id_do_accurate = "select * FROM pro_pr_detail WHERE id_prd='" . $id_prds['id_prd'] . "'";
			$data_prd = $con->getRecord($get_id_do_accurate);

			$query = http_build_query([
				'id' => $data_plan['id_accurate'],
			]);

			$urlnya = 'https://zeus.accurate.id/accurate/api/sales-order/detail.do?' . $query;

			$result = curl_get($urlnya);
			// echo json_encode($result['d']['detailItem']);
			// exit();

			if ($result['s'] == false) {
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", $result['d'][0] . " - Response dari Accurate", BASE_REFERER);
			}

			$kode_item = $result['d']['detailItem'];
			$no_customer = $result['d']['customer']['customerNo'];
			$po_number = $result['d']['poNumber'];
			if ($nomor_po_oa != NULL || $nomor_po_oa != "") {
				$po_number_oa = $nomor_po_oa;
			} else {
				$po_number_oa = $po_number;
			}

			if ($nomor_po_pbbkb != NULL || $nomor_po_pbbkb != "") {
				$po_number_pbbkb = $nomor_po_pbbkb;
			} else {
				$po_number_pbbkb = $po_number;
			}

			// $data_item2[] = ['kode' => $kode_item, 'keterangan' => 'kode_item'];

			$sql1 = "SELECT b.tanggal_poc FROM pro_po_ds_detail a JOIN pro_po_customer b ON a.id_poc=b.id_poc WHERE a.id_dsd = '" . $id_dsd . "'";
			$row1 = $con->getRecord($sql1);

			$query4 = "SELECT a.id_dsd, IF(c.gabung_oa=1,'gabung_oa',IF(c.gabung_pbbkb=1,'gabung_pbbkb',IF(c.all_in=1 OR c.gabung_pbbkboa=1,'all_in','break_all'))) AS jenis_penawaran FROM pro_po_ds_detail as a JOIN pro_po_customer as b ON a.id_poc=b.id_poc JOIN pro_penawaran as c ON b.id_penawaran=c.id_penawaran WHERE id_dsd = '" . $id_dsd . "'";
			$res4 	= $con->getRecord($query4);

			if ($split_invoice == "all_in") {

				// $data_item2 = array_merge($data_item_post, $data_item2);
				foreach ($kode_item as $item) {
					if ($item['item']['no'] == 'NS-001') {
						$detailItems['detailItem'][] = [
							'itemNo'       => $item['item']['no'],
							'quantity'     => $vol_kirim,
							'unitPrice'    => $item['unitPrice'],
							'deliveryOrderNumber' => $data_prd['no_do_syop'],
							'salesmanListNumber'=>$item['salesmanList'][0]['number']
						];
					} elseif ($item['item']['no'] == 'PBBKB') {
						$detailItems['detailItem'][] = [
							'itemNo'       => $item['item']['no'],
							'quantity'     => $vol_kirim,
							'unitPrice'    => $item['unitPrice'],
							'deliveryOrderNumber' => $data_prd['no_do_syop'],
							'salesmanListNumber'=>$item['salesmanList'][0]['number']
						];
					} else {
						$detailItems['detailItem'][] = [
							'itemNo'       => $item['item']['no'],
							'quantity'     => $vol_kirim,
							'unitPrice'    => $item['unitPrice'],
							'deliveryOrderNumber' => $data_prd['no_do_syop'],
							'itemCashDiscount' => $discount,
							'salesmanListNumber'=>$item['salesmanList'][0]['number']
						];
					}

					// $detailItems['detailItem'][] = [
					// 	'itemNo'       => $item['item']['no'],
					// 	'quantity'     => $vol_kirim,
					// 	'unitPrice'    => $item['unitPrice'],
					// 	'deliveryOrderNumber' => $data_prd['no_do_syop'],
					// 	'itemCashDiscount' => $discount
					// ];
				}

				$sql4 = "
				insert into pro_invoice_admin_detail(id_invoice_detail, id_invoice, id_dsd, tgl_delivered, vol_kirim, harga_kirim, discount, jenisnya) values 
				('" . $noms01 . "', '" . $res1 . "', '" . $id_dsd . "', '" . tgl_db($tgl_delivered) . "', '" . $vol_kirim . "', '" . $harga_kirim . "', '" . $discount . "', '" . $jenisnya . "')";
				$con->setQuery($sql4);
				$oke  = $oke && !$con->hasError();


				if ($noms01 == $total_pengiriman) {
					// Eksekusi API request dengan data item yang sudah lengkap
					$urlnya = 'https://zeus.accurate.id/accurate/api/sales-invoice/save.do';
					$data = array(
						"customerNo"        => $no_customer,
						"number"            => $noms_inv,
						"transDate"         => $tgl_invoice,
						"taxable"           => true,
						'branchName'        => ($rowget_cabang['nama_cabang'] == 'HO' ? 'Head Office' : $rowget_cabang['nama_cabang']),
						"detailItem"        => $detailItems['detailItem'],
					);

					$jsonData = json_encode($data);
					// var_dump($detailItems['detailItem']);
					// exit();

					$result_save = curl_post($urlnya, $jsonData);
					if ($result_save['s'] == true) {
						$id_accurate = $result_save['r']['id'];
						$update = 'update pro_invoice_admin set id_accurate = "' . $id_accurate . '" where id_invoice = "' . $res1 . '"';
						$con->setQuery($update);
						$oke  = $oke && !$con->hasError();
					} else {
						$con->rollBack();
						$con->clearError();
						$con->close();
						$flash->add("error", $result_save['d'][0] . " - Response dari Accurate", BASE_REFERER);
					}
				}
			} elseif ($split_invoice == "split_oa") {

				if ($res4['jenis_penawaran'] == "gabung_oa" || $res4['jenis_penawaran'] == "gabung_pbbkboa") {
					$con->rollBack();
					$con->clearError();
					$con->close();
					$flash->add("error", "Gagal simpan invoice, tidak dapat di split OA karena pada penawaran jenis harga nya GABUNG OA", BASE_REFERER);
				} else {
					foreach ($kode_item as $item) {

						if ($item['item']['no'] == 'PBBKB') {
							$detailItems['detailItem'][] = [
								'itemNo'       => $item['item']['no'],
								'quantity'     => $vol_kirim,
								'unitPrice'    => $item['unitPrice'],
								'deliveryOrderNumber' => $data_prd['no_do_syop'],
								'salesmanListNumber'=>$item['salesmanList'][0]['number']
							];
						} elseif ($item['item']['no'] == 'NS-001') {
							$detailItems_oa['detailItem'][] = [
								'itemNo'       => $item['item']['no'],
								'quantity'     => $vol_kirim,
								'unitPrice'    => $item['unitPrice'],
								'deliveryOrderNumber' => $data_prd['no_do_syop'],
								'salesmanListNumber'=>$item['salesmanList'][0]['number']
							];
						} else {
							$detailItems['detailItem'][] = [
								'itemNo'       => $item['item']['no'],
								'quantity'     => $vol_kirim,
								'unitPrice'    => $item['unitPrice'],
								'deliveryOrderNumber' => $data_prd['no_do_syop'],
								'itemCashDiscount' => $discount,
								'salesmanListNumber'=>$item['salesmanList'][0]['number']
							];
						}
					}


					// Harga Dasar + PBBKB + PPN
					if ($kategori == "gabung_pbbkb" || $kategori == "gabung_pbbkboa") {
						$harga_dasar_split_oa = (($harga_dasar + $pbbkb) * $nilai_ppn / 100) + ($harga_dasar + $pbbkb);
					} else {
						$harga_dasar_split_oa = $harga_dasar + $pbbkb + ($harga_dasar * $nilai_ppn / 100);
					}

					$sql_split_oa1 = "
					insert into pro_invoice_admin_detail(id_invoice_detail, id_invoice, id_dsd, tgl_delivered, vol_kirim, harga_kirim, discount, jenisnya) values 
					('" . $noms01 . "', '" . $res1 . "', '" . $id_dsd . "', '" . tgl_db($tgl_delivered) . "', '" . $vol_kirim . "', '" . $harga_dasar_split_oa . "', '" . $discount . "', '" . $jenisnya . "')";
					$con->setQuery($sql_split_oa1);
					$oke  = $oke && !$con->hasError();

					// INPUT OA
					$ongkos_angkut_split_oa = $ongkos_angkut + ($ongkos_angkut * $nilai_ppn / 100);

					$sql_split_oa2 = "
					insert into pro_invoice_admin_detail(id_invoice_detail, id_invoice, id_dsd, tgl_delivered, vol_kirim, harga_kirim, jenisnya) values 
					('" . $noms01 . "', '" . $res2 . "', '" . $id_dsd . "', '" . tgl_db($tgl_delivered) . "', '" . $vol_kirim . "', '" . $ongkos_angkut_split_oa . "', '" . $jenisnya . "')";
					$con->setQuery($sql_split_oa2);
					$oke  = $oke && !$con->hasError();

					if ($noms01 == $total_pengiriman) {
						$urlnya = 'https://zeus.accurate.id/accurate/api/sales-invoice/save.do';

						$data = array(
							"customerNo"        => $no_customer,
							"poNumber"        	=> $po_number,
							"number"           	=> $noms_inv,
							"transDate" 		=> $tgl_invoice,
							'branchName'        => $rowget_cabang['nama_cabang'] == 'HO' ? 'Head Office' : $rowget_cabang['nama_cabang'],
							"taxable" 			=> true,
							"detailItem"       	=> $detailItems['detailItem'],
						);

						$jsonData = json_encode($data);

						$result_save = curl_post($urlnya, $jsonData);

						if ($result_save['s'] == true) {
							$id_accurate = $result_save['r']['id'];
							$update = 'update pro_invoice_admin set id_accurate = "' . $id_accurate . '" where id_invoice = "' . $res1 . '"';
							$con->setQuery($update);
							$oke  = $oke && !$con->hasError();
						} else {
							$con->rollBack();
							$con->clearError();
							$con->close();
							$flash->add("error", $result_save['d'][0] . " - Response dari Accurate", BASE_REFERER);
						}

						$urlnya2 = 'https://zeus.accurate.id/accurate/api/sales-invoice/save.do';

						$data2 = array(
							"customerNo"        => $no_customer,
							"poNumber"	        => $po_number_oa,
							"number"           	=> $noms_inv_split_oa,
							"transDate" 		=> $tgl_invoice,
							"taxable" 			=> true,
							'branchName'        => $rowget_cabang['nama_cabang'] == 'HO' ? 'Head Office' : $rowget_cabang['nama_cabang'],
							"detailItem"       	=> $detailItems_oa['detailItem']
						);
						$jsonData2 = json_encode($data2);
						// echo $jsonData2;
						// exit();
						$result_save2 = curl_post($urlnya2, $jsonData2);

						if ($result_save2['s'] == true) {
							$id_accurate = $result_save2['r']['id'];
							$update = 'update pro_invoice_admin set id_accurate = "' . $id_accurate . '" where id_invoice = "' . $res2 . '"';
							$con->setQuery($update);
							$oke  = $oke && !$con->hasError();
						} else {
							$con->rollBack();
							$con->clearError();
							$con->close();
							$flash->add("error", $result_save2['d'][0] . " - Response dari Accurate", BASE_REFERER);
						}
					}
				}
			} elseif ($split_invoice == "split_pbbkb") {

				if ($res4['jenis_penawaran'] == "gabung_pbbkb" || $res4['jenis_penawaran'] == "gabung_pbbkboa") {
					$con->rollBack();
					$con->clearError();
					$con->close();
					$flash->add("error", "Gagal simpan invoice, tidak dapat di split PBBKB karena pada penawaran jenis harga nya GABUNG PBBKB", BASE_REFERER);
				} else {

					foreach ($kode_item as $item) {

						if ($item['item']['no'] == 'NS-001') {
							$detailItems['detailItem'][] = [
								'itemNo'       => $item['item']['no'],
								'quantity'     => $vol_kirim,
								'unitPrice'    => $item['unitPrice'],
								'deliveryOrderNumber' => $data_prd['no_do_syop'],
								'salesmanListNumber'=>$item['salesmanList'][0]['number']
							];
						} elseif ($item['item']['no'] == 'PBBKB') {
							$detailItems_pbbkb['detailItem'][] = [
								'itemNo'       => $item['item']['no'],
								'quantity'     => $vol_kirim,
								'unitPrice'    => $item['unitPrice'],
								'deliveryOrderNumber' => $data_prd['no_do_syop'],
								'salesmanListNumber'=>$item['salesmanList'][0]['number']
							];
						} else {
							$detailItems['detailItem'][] = [
								'itemNo'       => $item['item']['no'],
								'quantity'     => $vol_kirim,
								'unitPrice'    => $item['unitPrice'],
								'deliveryOrderNumber' => $data_prd['no_do_syop'],
								'itemCashDiscount' => $discount,
								'salesmanListNumber'=>$item['salesmanList'][0]['number']
							];
						}
					}

					// Harga Dasar + OA + PPN
					if ($pembulatan == 1) {
						$harga_dasar_split_pbbkb = round(($harga_dasar + $ongkos_angkut) + (($harga_dasar + $ongkos_angkut) * $nilai_ppn / 100));
					} else {
						$harga_dasar_split_pbbkb = ($harga_dasar + $ongkos_angkut) + (($harga_dasar + $ongkos_angkut) * $nilai_ppn / 100);
					}

					$sql_split_pbbkb1 = "
					insert into pro_invoice_admin_detail(id_invoice_detail, id_invoice, id_dsd, tgl_delivered, vol_kirim, harga_kirim, discount, jenisnya) values 
					('" . $noms01 . "', '" . $res1 . "', '" . $id_dsd . "', '" . tgl_db($tgl_delivered) . "', '" . $vol_kirim . "', '" . $harga_dasar_split_pbbkb . "', '" . $discount . "', '" . $jenisnya . "')";
					$con->setQuery($sql_split_pbbkb1);
					$oke  = $oke && !$con->hasError();

					// PBBKB
					$split_pbbkb = $pbbkb;
					$sql_split_pbbkb2 = "insert into pro_invoice_admin_detail(id_invoice_detail, id_invoice, id_dsd, tgl_delivered, vol_kirim, harga_kirim, jenisnya) values 
					('" . $noms01 . "', '" . $res2 . "', '" . $id_dsd . "', '" . tgl_db($tgl_delivered) . "', '" . $vol_kirim . "', '" . $split_pbbkb . "', '" . $jenisnya . "')";
					$con->setQuery($sql_split_pbbkb2);
					$oke  = $oke && !$con->hasError();

					if ($noms01 == $total_pengiriman) {
						$urlnya = 'https://zeus.accurate.id/accurate/api/sales-invoice/save.do';
						// Data yang akan dikirim dalam format JSON
						$data = array(
							"customerNo"        => $no_customer,
							"poNumber"        	=> $po_number,
							"number"           	=> $noms_inv,
							"transDate" 		=> $tgl_invoice,
							"taxable" 			=> true,
							'branchName'        => $rowget_cabang['nama_cabang'] == 'HO' ? 'Head Office' : $rowget_cabang['nama_cabang'],
							"detailItem"       	=> $detailItems['detailItem']
						);

						$jsonData = json_encode($data);
						$result_save = curl_post($urlnya, $jsonData);

						if ($result_save['s'] == true) {
							$id_accurate = $result_save['r']['id'];
							$update = 'update pro_invoice_admin set id_accurate = "' . $id_accurate . '" where id_invoice = "' . $res1 . '"';
							$con->setQuery($update);
							$oke  = $oke && !$con->hasError();
						} else {
							$con->rollBack();
							$con->clearError();
							$con->close();
							$flash->add("error", $result_save['d'][0] . " - Response dari Accurate 1", BASE_REFERER);
						}

						$urlnya2 = 'https://zeus.accurate.id/accurate/api/sales-invoice/save.do';
						// Data yang akan dikirim dalam format JSON
						$data2 = array(
							"customerNo"        => $no_customer,
							"poNumber"	        => $po_number_pbbkb,
							"number"           	=> $noms_inv_split_pbbkb,
							"transDate" 		=> $tgl_invoice,
							"taxable" 			=> false,
							'branchName'        => $rowget_cabang['nama_cabang'] == 'HO' ? 'Head Office' : $rowget_cabang['nama_cabang'],
							"detailItem"       	=> $detailItems_pbbkb['detailItem']
						);

						$jsonData2 = json_encode($data2);
						$result_save2 = curl_post($urlnya2, $jsonData2);

						if ($result_save2['s'] == true) {
							$id_accurate = $result_save2['r']['id'];
							$update = 'update pro_invoice_admin set id_accurate = "' . $id_accurate . '" where id_invoice = "' . $res2 . '"';
							$con->setQuery($update);
							$oke  = $oke && !$con->hasError();
						} else {
							$con->rollBack();
							$con->clearError();
							$con->close();
							$flash->add("error", $result_save2['d'][0] . " - Response dari Accurate 2", BASE_REFERER);
						}
					}
				}
			} elseif ($split_invoice == "split_all") {

				if ($res4['jenis_penawaran'] == "gabung_oa" || $res4['jenis_penawaran'] == "gabung_pbbkboa" || $res4['jenis_penawaran'] == "gabung_pbbkb") {
					$con->rollBack();
					$con->clearError();
					$con->close();
					$flash->add("error", "Gagal simpan invoice, tidak dapat di split ALL karena pada penawaran jenis harga nya GABUNG OA / GABUNG PBBKB", BASE_REFERER);
				} else {
					foreach ($kode_item as $item) {

						if ($item['item']['no'] == 'NS-001') {
							$detailItems_oa['detailItem'][] = [
								'itemNo'       => $item['item']['no'],
								'quantity'     => $vol_kirim,
								'unitPrice'    => $item['unitPrice'],
								'deliveryOrderNumber' => $data_prd['no_do_syop'],
								'salesmanListNumber'=>$item['salesmanList'][0]['number']
							];
						} elseif ($item['item']['no'] == 'PBBKB') {
							$detailItems_pbbkb['detailItem'][] = [
								'itemNo'       => $item['item']['no'],
								'quantity'     => $vol_kirim,
								'unitPrice'    => $item['unitPrice'],
								'deliveryOrderNumber' => $data_prd['no_do_syop'],
								'salesmanListNumber'=>$item['salesmanList'][0]['number']
							];
						} else {
							$detailItems['detailItem'][] = [
								'itemNo'       => $item['item']['no'],
								'quantity'     => $vol_kirim,
								'unitPrice'    => $item['unitPrice'],
								'deliveryOrderNumber' => $data_prd['no_do_syop'],
								'itemCashDiscount' => $discount,
								'salesmanListNumber'=>$item['salesmanList'][0]['number']
							];
						}
					}

					// Harga Dasar + PPN
					$harga_dasar_split_all = $harga_dasar + ($harga_dasar * $nilai_ppn / 100);

					$sql_split_all1 = "
				insert into pro_invoice_admin_detail(id_invoice_detail, id_invoice, id_dsd, tgl_delivered, vol_kirim, harga_kirim, discount, jenisnya) values 
				('" . $noms01 . "', '" . $res1 . "', '" . $id_dsd . "', '" . tgl_db($tgl_delivered) . "', '" . $vol_kirim . "', '" . $harga_dasar_split_all . "', '" . $discount . "', '" . $jenisnya . "')";
					$con->setQuery($sql_split_all1);
					$oke  = $oke && !$con->hasError();

					// Ongkos Angkut + PPN
					$ongkos_angkut_split_all = $ongkos_angkut + ($ongkos_angkut * $nilai_ppn / 100);

					$sql_split_all2 = "
				insert into pro_invoice_admin_detail(id_invoice_detail, id_invoice, id_dsd, tgl_delivered, vol_kirim, harga_kirim, jenisnya) values 
				('" . $noms01 . "', '" . $res2 . "', '" . $id_dsd . "', '" . tgl_db($tgl_delivered) . "', '" . $vol_kirim . "', '" . $ongkos_angkut_split_all . "', '" . $jenisnya . "')";
					$con->setQuery($sql_split_all2);
					$oke  = $oke && !$con->hasError();

					// PBBKB
					$split_pbbkb = $pbbkb;
					$sql_split_all3 = "
				insert into pro_invoice_admin_detail(id_invoice_detail, id_invoice, id_dsd, tgl_delivered, vol_kirim, harga_kirim, jenisnya) values 
				('" . $noms01 . "', '" . $res3 . "', '" . $id_dsd . "', '" . tgl_db($tgl_delivered) . "', '" . $vol_kirim . "', '" . $split_pbbkb . "', '" . $jenisnya . "')";
					$con->setQuery($sql_split_all3);
					$oke  = $oke && !$con->hasError();

					if ($noms01 == $total_pengiriman) {
						// API HSD
						$urlnya = 'https://zeus.accurate.id/accurate/api/sales-invoice/save.do';
						$data = array(
							"customerNo"        => $no_customer,
							"poNumber"        	=> $po_number,
							"number"           	=> $noms_inv,
							"transDate" 		=> $tgl_invoice,
							"taxable" 			=> true,
							'branchName'        => $rowget_cabang['nama_cabang'] == 'HO' ? 'Head Office' : $rowget_cabang['nama_cabang'],
							"detailItem"       	=> $detailItems['detailItem']
						);
						$jsonData = json_encode($data);

						$result_save = curl_post($urlnya, $jsonData);

						if ($result_save['s'] == true) {
							$id_accurate = $result_save['r']['id'];
							$update = 'update pro_invoice_admin set id_accurate = "' . $id_accurate . '" where id_invoice = "' . $res1 . '"';
							$con->setQuery($update);
							$oke  = $oke && !$con->hasError();
						} else {
							$con->rollBack();
							$con->clearError();
							$con->close();
							$flash->add("error", $result_save['d'][0] . " - Response dari Accurate", BASE_REFERER);
						}

						// // API OA
						$urlnya2 = 'https://zeus.accurate.id/accurate/api/sales-invoice/save.do';
						$data2 = array(
							"customerNo"        => $no_customer,
							"poNumber"	        => $po_number_oa,
							"number"           	=> $noms_inv_split_oa,
							"transDate" 		=> $tgl_invoice,
							"taxable" 			=> true,
							'branchName'        => $rowget_cabang['nama_cabang'] == 'HO' ? 'Head Office' : $rowget_cabang['nama_cabang'],
							"detailItem"       	=> $detailItems_oa['detailItem']
						);
						$jsonData2 = json_encode($data2);

						$result_save2 = curl_post($urlnya2, $jsonData2);

						if ($result_save2['s'] == true) {
							$id_accurate2 = $result_save2['r']['id'];
							$update2 = 'update pro_invoice_admin set id_accurate = "' . $id_accurate2 . '" where id_invoice = "' . $res2 . '"';
							$con->setQuery($update2);
							$oke  = $oke && !$con->hasError();
						} else {
							$con->rollBack();
							$con->clearError();
							$con->close();
							$flash->add("error", $result_save2['d'][0] . " - Response dari Accurate", BASE_REFERER);
						}

						// // API PBBKB
						$urlnya3 = 'https://zeus.accurate.id/accurate/api/sales-invoice/save.do';
						$data3 = array(
							"customerNo"        => $no_customer,
							"poNumber"	        => $po_number_pbbkb,
							"number"           	=> $noms_inv_split_pbbkb,
							"transDate" 		=> $tgl_invoice,
							"taxable" 			=> false,
							'branchName'        => $rowget_cabang['nama_cabang'] == 'HO' ? 'Head Office' : $rowget_cabang['nama_cabang'],
							"detailItem"       	=> $detailItems_pbbkb['detailItem']
						);
						$jsonData3 = json_encode($data3);
						$result_save3 = curl_post($urlnya3, $jsonData3);

						if ($result_save3['s'] == true) {
							$id_accurate3 = $result_save3['r']['id'];
							$update3 = 'update pro_invoice_admin set id_accurate = "' . $id_accurate3 . '" where id_invoice = "' . $res3 . '"';
							$con->setQuery($update3);
							$oke  = $oke && !$con->hasError();
						} else {
							$con->rollBack();
							$con->clearError();
							$con->close();
							$flash->add("error", $result_save3['d'][0] . " - Response dari Accurate", BASE_REFERER);
						}
					}
				}
			}
			// INI DI PAKE
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
		$query = array(
			'id' => $id_accurate
		);

		$url_del = 'https://zeus.accurate.id/accurate/api/sales-invoice/delete.do';
		$delete_accurate = curl_delete($url_del, json_encode($query));
		// print_r($delete_accurate);
		if ($delete_accurate['s'] == true) {
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		} else {
			$flash->add("error", "Invoice Gagal dihapus, silahkan lakukan hapus manual atau hubungi IT - Response dari Accurate", BASE_REFERER);
		}
	}
} else if ($act == "update") {
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	$cek1 = "select total_invoice, id_customer from pro_invoice_admin where id_invoice = '" . $idr . "'";
	$row1 = $con->getRecord($cek1);

	$query = "SELECT * FROM pro_invoice_admin WHERE id_invoice = '" . $idr . "'";
	$invoice = $con->getRecord($query);

	if ($split_invoice == "all_in") {
		$grand_total_invoice = $total_invoice;
	} elseif ($split_invoice == "harga_dasar") {
		$grand_total_invoice = $total_invoice_harga_dasar;
	} elseif ($split_invoice == "harga_dasar_oa") {
		$grand_total_invoice = $total_invoice_harga_dasar_oa;
	} elseif ($split_invoice == "harga_dasar_pbbkb") {
		$grand_total_invoice = $total_invoice_harga_dasar_pbbkb;
	} elseif ($split_invoice == "split_pbbkb") {
		$grand_total_invoice = $total_invoice_pbbkb;
	} elseif ($split_invoice == "split_oa") {
		$grand_total_invoice = $total_invoice_ongkos_angkut;
	}

	if ($grand_total_invoice == 0) {
		$grand_total_invoice = $total_invoice;
	}

	$update_cl = "UPDATE pro_customer SET credit_limit_used = (credit_limit_used - '" . $invoice['total_invoice'] . "') + $grand_total_invoice WHERE id_customer = '" . $invoice['id_customer'] . "'";
	$con->setQuery($update_cl);
	$oke  = $oke && !$con->hasError();

	$query_cust = "SELECT * FROM pro_customer WHERE id_customer = '" . $invoice['id_customer'] . "'";
	$res_cust = $con->getRecord($query_cust);

	$sql4 = "UPDATE pro_customer_admin_arnya SET not_yet = (not_yet - '" . $invoice['total_invoice'] . "') + '" . round($grand_total_invoice) . "' WHERE id_customer = '" . $row1['id_customer'] . "'";
	$con->setQuery($sql4);
	$oke  = $oke && !$con->hasError();

	$sql1 = "
		update pro_invoice_admin set tgl_invoice = '" . tgl_db($tgl_invoice) . "', total_invoice = '" . $grand_total_invoice . "', is_cetakan = '" . $is_cetakan . "', no_invoice_customer = '" . $no_invoice_customer . "', status_ar = 'notyet', lastupdate_time = '" . $lastupdate_time . "', lastupdate_ip = '" . $lastupdate_ip . "', lastupdate_by = '" . $lastupdate_by . "' where id_invoice = '" . $idr . "'";
	$con->setQuery($sql1);
	$oke  = $oke && !$con->hasError();

	$sql_history = "UPDATE pro_history_ar_customer SET nominal = -$grand_total_invoice WHERE id_invoice = '" . $invoice['id_invoice'] . "' and kategori = 2";
	$con->setQuery($sql_history);
	$oke = $oke && !$con->hasError();

	// echo $no_invoice_customer;
	// exit();


	$invoice_split = "SELECT * FROM pro_invoice_admin WHERE no_invoice REGEXP '^" . $invoice['no_invoice'] . "[^0-9]?$'";
	$res_inv_split = $con->getResult($invoice_split);

	// Ambil semua id_invoice
	$id_invoices = array_column($res_inv_split, 'id_invoice');

	if (!empty($id_invoices)) {
		// Ubah array menjadi string untuk query SQL
		$id_invoice_str = implode(",", array_map('intval', $id_invoices)); // pastikan semua integer

		// Hapus dari detail
		$sql3 = "DELETE FROM pro_invoice_admin_detail WHERE id_invoice IN (" . $id_invoice_str . ")";
		$con->setQuery($sql3);
		$oke = $oke && !$con->hasError();
	}
	// echo json_encode($res_inv_split);
	// exit();


	if (count($_POST["id_dsd"]) > 0) {
		$detailItems 	= [];
		$noms01 = 0;
		$total_pengiriman = count($_POST["id_dsd"]);
		foreach ($_POST["id_dsd"] as $idx => $nilai) {
			$noms01++;
			$id_dsd			= htmlspecialchars($_POST["id_dsd"][$idx], ENT_QUOTES);
			$tgl_delivered	= htmlspecialchars($_POST["tgl_delivered"][$idx], ENT_QUOTES);
			$vol_kirim		= htmlspecialchars(str_replace(array(","), array(""), $_POST["vol_kirim"][$idx]), ENT_QUOTES);
			$harga_kirim	= htmlspecialchars(str_replace(array(","), array(""), $_POST["harga_kirim_fix"][$idx]), ENT_QUOTES);
			$discount		= htmlspecialchars(str_replace(array(","), array(""), $_POST["discount"][$idx]), ENT_QUOTES);
			$jenisnya		= htmlspecialchars($_POST["jenisnya"][$idx], ENT_QUOTES);
			$harga_dasar	= htmlspecialchars($_POST["harga_dasar"][$idx], ENT_QUOTES);

			$ongkos_angkut	= htmlspecialchars($_POST["ongkos_angkut"][$idx], ENT_QUOTES);

			$ppn			= htmlspecialchars($_POST["ppn"][$idx], ENT_QUOTES);
			$nilai_ppn		= htmlspecialchars($_POST["nilai_ppn"][$idx], ENT_QUOTES);

			$pbbkb			= htmlspecialchars($_POST["pbbkb"][$idx], ENT_QUOTES);

			$discount		= ($discount ? $discount : 0);
			$harga_kirim	= ($harga_kirim ? $harga_kirim : 0);
			$vol_kirim		= ($vol_kirim ? $vol_kirim : 0);

			$query_ds = "SELECT * FROM pro_po_ds_detail WHERE id_dsd = '" . $id_dsd . "'";
			$res_ds = $con->getRecord($query_ds);

			$get_id_so_accurate = "select * FROM pro_po_customer_plan WHERE id_plan='" . $res_ds['id_plan'] . "'";
			$data_plan = $con->getRecord($get_id_so_accurate);

			$get_id_do_accurate = "select * FROM pro_pr_detail WHERE id_prd='" . $res_ds['id_prd'] . "'";
			$data_prd = $con->getRecord($get_id_do_accurate);

			// echo json_encode($res_do);
			// exit();

			$query = http_build_query([
				'id' => $data_plan['id_accurate'],
			]);

			$urlnya = 'https://zeus.accurate.id/accurate/api/sales-order/detail.do?' . $query;

			$result = curl_get($urlnya);

			if ($result['s'] == false) {
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", $result['d'][0] . " - Response dari Accurate", BASE_REFERER);
			}

			$kode_item = $result['d']['detailItem'];
			$no_customer = $result['d']['customer']['customerNo'];

			if (count($res_inv_split) > 0) {
				foreach ($res_inv_split as $ris) {

					if ($ris['jenis'] == "all_in" || $ris['jenis'] == "harga_dasar_oa" || $ris['jenis'] == "harga_dasar_pbbkb" || $ris['jenis'] == "harga_dasar") {
						$harganya = $harga_kirim;
					} elseif ($ris['jenis'] == "split_pbbkb") {
						$harganya = $pbbkb;
					} elseif ($ris['jenis'] == "split_oa") {
						$harganya = $ongkos_angkut;
					}

					// var_dump($harganya);

					$sql4 = "
					insert into pro_invoice_admin_detail(id_invoice_detail, id_invoice, id_dsd, tgl_delivered, vol_kirim, harga_kirim, discount, jenisnya) values 
					('" . $noms01 . "', '" . $ris['id_invoice'] . "', '" . $id_dsd . "', '" . tgl_db($tgl_delivered) . "', '" . $vol_kirim . "', '" . $harganya . "', '" . $discount . "', '" . $jenisnya . "')";

					$con->setQuery($sql4);
					$oke  = $oke && !$con->hasError();

					$sql_get_max = "SELECT id_invoice FROM pro_invoice_admin_detail WHERE id_invoice = '" . $ris['id_invoice'] . "'";
					$row_max = $con->getRecord($sql_get_max);

					if ($row_max['id_invoice'] != $ris['id_invoice']) {
						$noms01 = 1; // increment id_invoice_detail untuk invoice ini
					}

					foreach ($kode_item as $item) {
						if ($ris['jenis'] == "all_in") {
							$detailItems['detailItem']['all_in']['no_invoice'] = $ris['no_invoice'];
							$detailItems['detailItem']['all_in']['id_invoice'] = $ris['id_invoice'];
							$detailItems['detailItem']['all_in']['items'][] = [
								'itemNo'       => $item['item']['no'],
								'quantity'     => $vol_kirim,
								'unitPrice'    => $item['unitPrice'],
								'deliveryOrderNumber' => $data_prd['no_do_syop'],
								'itemCashDiscount' => $discount,
								'salesmanListNumber'=>$item['salesmanList'][0]['number']
							];
						} elseif ($ris['jenis'] == "harga_dasar_oa" || $ris['jenis'] == "harga_dasar_pbbkb" || $ris['jenis'] == "harga_dasar") {
							if ($item['item']['no'] != 'NS-001' && $item['item']['no'] != 'PBBKB') {
								$detailItems['detailItem']['harga_dasar']['no_invoice'] = $ris['no_invoice'];
								$detailItems['detailItem']['harga_dasar']['id_invoice'] = $ris['id_invoice'];
								$detailItems['detailItem']['harga_dasar']['items'][] = [
									'itemNo'       => $item['item']['no'],
									'quantity'     => $vol_kirim,
									'unitPrice'    => $item['unitPrice'],
									'deliveryOrderNumber' => $data_prd['no_do_syop'],
									'itemCashDiscount' => $discount,
									'salesmanListNumber'=>$item['salesmanList'][0]['number']
								];
							}
						} elseif ($ris['jenis'] == "split_oa") {
							if ($item['item']['no'] == 'NS-001') {
								$detailItems['detailItem']['oa']['no_invoice'] = $ris['no_invoice'];
								$detailItems['detailItem']['oa']['id_invoice'] = $ris['id_invoice'];
								$detailItems['detailItem']['oa']['items'][] = [
									'itemNo'       => $item['item']['no'],
									'quantity'     => $vol_kirim,
									'unitPrice'    => $item['unitPrice'],
									'deliveryOrderNumber' => $data_prd['no_do_syop'],
									'itemCashDiscount' => $discount,
									'salesmanListNumber'=>$item['salesmanList'][0]['number']
								];
							}
						} elseif ($ris['jenis'] == "split_pbbkb") {
							if ($item['item']['no'] == 'PBBKB') {
								$detailItems['detailItem']['pbbkb']['no_invoice'] = $ris['no_invoice'];
								$detailItems['detailItem']['pbbkb']['id_invoice'] = $ris['id_invoice'];
								$detailItems['detailItem']['pbbkb']['items'][] = [
									'itemNo'       => $item['item']['no'],
									'quantity'     => $vol_kirim,
									'unitPrice'    => $item['unitPrice'],
									'deliveryOrderNumber' => $data_prd['no_do_syop'],
									'itemCashDiscount' => $discount,
									'salesmanListNumber'=>$item['salesmanList'][0]['number']
								];
							}
						}
					}
				}
			}
		}
	}
	// echo json_encode($detailItems['detailItem']);
	// exit();
	$url  = BASE_URL_CLIENT . "/invoice_customer.php";
	if ($oke) {
		foreach ($res_inv_split as $ris2) {
			$id_accurate_si = array(
				'id' => $ris2['id_accurate']
			);

			$url_del = 'https://zeus.accurate.id/accurate/api/sales-invoice/delete.do';
			$delete_accurate = curl_delete($url_del, json_encode($id_accurate_si));
			// print_r($delete_accurate);
			if ($delete_accurate['s'] == false) {
				$con->rollBack();
				$con->clearError();
				$con->close();
				$result = [
					"status" 	=> false,
					"pesan" 	=> $delete_accurate['d'][0],
				];
			}
		}
		$detail_data = $detailItems['detailItem'];
		foreach ($detail_data as $i => $subData) {
			$url_save = 'https://zeus.accurate.id/accurate/api/sales-invoice/save.do';
			$data = array(
				"customerNo"        => $res_cust['kode_pelanggan'],
				"number"            => $subData['no_invoice'],
				'branchName'        => ($rowget_cabang['nama_cabang'] == 'HO' ? 'Head Office' : $rowget_cabang['nama_cabang']),
				'transDate'        	=> $tgl_invoice,
				"detailItem"        => $subData['items'],
			);

			$jsonData = json_encode($data);
			// var_dump($jsonData);
			// echo $jsonData;
			// exit();

			$result_save = curl_post($url_save, $jsonData);

			// echo json_encode($result_save);
			// exit();

			if ($result_save['s'] == false) {
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", $result_save['d'][0] . " - Response dari Accurate", BASE_REFERER);
			}

			$update_id_accurate = 'update pro_invoice_admin set total_invoice = "' . $result_save['r']['totalAmount'] . '", id_accurate = "' . $result_save['r']['id'] . '" where id_invoice = "' . $subData['id_invoice'] . '"';
			$con->setQuery($update_id_accurate);
			$oke  = $oke && !$con->hasError();
		}
		// exit();

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

							$total_point = 0;

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

					if ($jml_bayar_potongan != 0) {
						$sql_bupot = "
								insert into pro_invoice_bukti_potong(id_invoice, kategori, nominal, created_at) values 
								('" . $idr . "', '" . $kategori_potongan . "', '" . $jml_bayar_potongan . "' , NOW())
							";
						$con->setQuery($sql_bupot);
						$oke  = $oke && !$con->hasError();
					}
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
			// $con->commit();
			// $con->close();
			// header("location: " . $url);
			// exit();
		} else {
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
	}


	// echo json_encode($lunas);
} else if ($act == "hapus") {
	$oke = true;
	$con->beginTransaction();
	$con->clearError();
	$param 	= htmlspecialchars(paramDecrypt($_POST["param"]), ENT_QUOTES);
	$post 	= explode("#|#", $param);
	$id1	= htmlspecialchars($post[1], ENT_QUOTES);
	$id2	= htmlspecialchars($post[2], ENT_QUOTES);

	$sql1 = "select total_invoice, id_customer, no_invoice, status_ar from pro_invoice_admin where id_invoice = '" . $id1 . "'";
	$row1 = $con->getRecord($sql1);

	$status_ar = $row1['status_ar'];


	// UPDATE AR
	if ($status_ar == 'notyet') {
		$sql4 = "UPDATE pro_customer_admin_arnya SET not_yet = not_yet - " . $row1['total_invoice'] . " WHERE id_customer = '" . $row1['id_customer'] . "'";
	} elseif ($status_ar == 'ov_up_07') {
		$sql4 = "UPDATE pro_customer_admin_arnya SET ov_up_07 = ov_up_07 - " . $row1['total_invoice'] . " WHERE id_customer = '" . $row1['id_customer'] . "'";
	} elseif ($status_ar == 'ov_under_30') {
		$sql4 = "UPDATE pro_customer_admin_arnya SET ov_under_30 = ov_under_30 - " . $row1['total_invoice'] . " WHERE id_customer = '" . $row1['id_customer'] . "'";
	} elseif ($status_ar == 'ov_under_60') {
		$sql4 = "UPDATE pro_customer_admin_arnya SET ov_under_60 = ov_under_60 - " . $row1['total_invoice'] . " WHERE id_customer = '" . $row1['id_customer'] . "'";
	} elseif ($status_ar == 'ov_under_90') {
		$sql4 = "UPDATE pro_customer_admin_arnya SET ov_under_90 = ov_under_90 - " . $row1['total_invoice'] . " WHERE id_customer = '" . $row1['id_customer'] . "'";
	} elseif ($status_ar == 'ov_up_90') {
		$sql4 = "UPDATE pro_customer_admin_arnya SET ov_up_90 = ov_up_90 - " . $row1['total_invoice'] . " WHERE id_customer = '" . $row1['id_customer'] . "'";
	}
	$con->setQuery($sql4);
	$oke  = $oke && !$con->hasError();

	$sql2 = "delete from pro_invoice_admin where id_invoice = '" . $id1 . "'";
	$con->setQuery($sql2);
	$oke  = $oke && !$con->hasError();


	$update_cl = "UPDATE pro_customer SET credit_limit_used = credit_limit_used - '" . $row1['total_invoice'] . "', credit_limit_reserved = credit_limit_reserved + '" . $row1['total_invoice'] . "' WHERE id_customer = '" . $row1['id_customer'] . "'";
	$con->setQuery($update_cl);
	$oke  = $oke && !$con->hasError();

	$delete_history_ar = "delete from pro_history_ar_customer where id_invoice = '" . $id1 . "'";
	$con->setQuery($delete_history_ar);
	$oke  = $oke && !$con->hasError();
	// echo json_encode($row1);
	// exit();

	// $sql4 = "delete from pro_refund where id_invoice = '" . $id1 . "'";
	// $con->setQuery($sql4);
	// $oke  = $oke && !$con->hasError();

	if ($oke) {

		$query = array(
			'id' => $id2
		);

		$url_del = 'https://zeus.accurate.id/accurate/api/sales-invoice/delete.do';
		$delete_accurate = curl_delete($url_del, json_encode($query));
		// print_r($delete_accurate);
		if ($delete_accurate['s'] == true) {
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
				"pesan" 	=> $delete_accurate['d'][0],
			];
		}
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

	$sql1 = "UPDATE pro_invoice_admin set tgl_invoice_dikirim = '" . tgl_db($tgl_invoice_dikirim) . "' where id_invoice = '" . $id_invoice_enc . "'";
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

		$data_customer = "SELECT id_customer, top_payment FROM pro_customer WHERE id_customer = '" . $row3['id_customer'] . "'";
		$res_customer = $con->getRecord($data_customer);

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

		$now = DateTime::createFromFormat('Y-m-d', date('Y-m-d'));

		$tgl_dikirim = new DateTime(tgl_db($tgl_invoice_dikirim));
		$topDays = (int) $res_customer['top_payment']; // pastikan bertipe int

		// Tambahkan TOP (misalnya 30 hari)
		$tgl_jatuh_tempo = clone $tgl_dikirim; // supaya tidak merusak original
		$tgl_jatuh_tempo->add(new DateInterval('P' . $topDays . 'D'));

		// Hitung selisih dari hari ini ke tanggal jatuh tempo
		$interval = $tgl_jatuh_tempo->diff($now);
		$selisih = (int) $interval->format('%a');

		$total_invoice = $row3['total_invoice'];
		$status_ar = "";

		// echo json_encode($tgl_jatuh_tempo);
		// exit();

		// 1. Ambil status AR lama
		$status_ar_lama = $row3['status_ar'];

		// echo $status_ar_lama;
		// exit();

		// Kurangi dari not_yet (hanya ini yang dikoreksi)
		if ($status_ar_lama === 'notyet') {
			$sql_kurang = "UPDATE pro_customer_admin_arnya SET not_yet = not_yet - $total_invoice WHERE id_customer = '" . $row3['id_customer'] . "'";
			$con->setQuery($sql_kurang);
			$oke = $oke && !$con->hasError();
		}

		// 2. Kurangi aging lama
		if (!empty($status_ar_lama)) {
			$sql_kurang = "UPDATE pro_customer_admin_arnya SET $status_ar_lama = $status_ar_lama - $total_invoice WHERE id_customer = '" . $row3['id_customer'] . "'";

			$con->setQuery($sql_kurang);
			$oke = $oke && !$con->hasError();
		}

		if ($interval->invert == 1) {
			$aging_col = 'not_yet';
			$status_ar = 'notyet';
		} else {
			if ($selisih <= 7) {
				$aging_col = 'not_yet';
				$status_ar = 'notyet';
			} elseif ($selisih <= 30) {
				$aging_col = 'ov_up_07';
				$status_ar = 'ov_up_07';
			} elseif ($selisih <= 60) {
				$aging_col = 'ov_under_30';
				$status_ar = 'ov_under_30';
			} elseif ($selisih <= 90) {
				$aging_col = 'ov_under_60';
				$status_ar = 'ov_under_60';
			} elseif ($selisih <= 120) {
				$aging_col = 'ov_under_90';
				$status_ar = 'ov_under_90';
			} else {
				$aging_col = 'ov_up_90';
				$status_ar = 'ov_up_90';
			}
		}

		$sql4 = "UPDATE pro_customer_admin_arnya SET $aging_col = COALESCE($aging_col, 0) + " . $total_invoice . " WHERE id_customer = '" . $row3['id_customer'] . "'";
		$con->setQuery($sql4);
		$oke  = $oke && !$con->hasError();

		$sql5 = "UPDATE pro_invoice_admin SET status_ar = '$status_ar' WHERE id_invoice = '" . $id_invoice_enc . "'";
		$con->setQuery($sql5);
		$oke  = $oke && !$con->hasError();

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

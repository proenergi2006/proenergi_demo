<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;
$enk  	= decode($_SERVER['REQUEST_URI']);
$act	= ($enk['act'] == "") ? htmlspecialchars($_POST["act"], ENT_QUOTES) : $enk['act'];
$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
$picnya = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]);
$ipnya 	= $_SERVER['REMOTE_ADDR'];

$id_jenis			= htmlspecialchars($_POST["id_jenis"], ENT_QUOTES);
$id_produk			= htmlspecialchars($_POST["id_produk"], ENT_QUOTES);
$tgl				= htmlspecialchars($_POST["tgl"], ENT_QUOTES);

$id_terminal		= htmlspecialchars($_POST["id_terminal"], ENT_QUOTES);
$awal_inven_total	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["awal_inven_total"]), ENT_QUOTES);

$adj_inven_sign		= htmlspecialchars($_POST["adj_inven_sign"], ENT_QUOTES);
$adj_inven			= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["adj_inven"]), ENT_QUOTES);

$transfer_tanki_satu_dari	= htmlspecialchars($_POST["transfer_tanki_satu_dari"], ENT_QUOTES);
$transfer_tanki_satu_ke		= htmlspecialchars($_POST["transfer_tanki_satu_ke"], ENT_QUOTES);
$tank_satu_total			= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["tank_satu_total"]), ENT_QUOTES);

$keterangan	= htmlspecialchars($_POST["keterangan"], ENT_QUOTES);


if ($id_jenis == "" || $id_produk == "" || $tgl == "") {
	$con->close();
	$flash->add("error", "KOSONG", BASE_REFERER);
} else {
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	//id_datanya, id_jenis, id_produk, id_terminal, id_vendor, id_po_supplier, id_inven_vendor, tanggal_inven, awal_inven, in_inven, out_inven, adj_inven, out_inven_virtual, keterangan, created_time, created_ip, created_by, lastupdate_time, lastupdate_ip, lastupdate_by,

	if ($act == 'add') {
		$created_time 	= date('Y-m-d H:i:s');
		$id_datanya 	= md5(uniqid("1089", $id_jenis) . '-' . intval(microtime(true)) . '-' . date('YmdHis'));

		if ($id_jenis == '1') {
			$isian = false;
			if (count($_POST["awal_inven_vendor_id"]) > 0) {
				foreach ($_POST["awal_inven_vendor_id"] as $idx => $val) {
					$awal_inven_vendor_id		= htmlspecialchars($_POST["awal_inven_vendor_id"][$idx], ENT_QUOTES);
					$awal_inven_vendor_nilai 	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["awal_inven_vendor_nilai"][$idx]), ENT_QUOTES);
					$awal_inven_vendor_nilai 	= ($awal_inven_vendor_nilai ? $awal_inven_vendor_nilai : 0);

					if ($awal_inven_vendor_id) {
						$isian = $isian || true;
						$sql1 = "
								insert into pro_inventory_depot (
									id_datanya, id_jenis, id_produk, tanggal_inven, keterangan, 
									id_terminal, id_vendor, awal_inven, 
									created_time, created_ip, created_by, lastupdate_time, lastupdate_ip, lastupdate_by
								) values (
									'" . $id_datanya . "', '" . $id_jenis . "', '" . $id_produk . "', '" . tgl_db($tgl) . "', '" . $keterangan . "', 
									'" . $id_terminal . "', '" . $awal_inven_vendor_id . "', '" . $awal_inven_vendor_nilai . "', 
									'" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "', '" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "'
								)
							";
						$con->setQuery($sql1);
						$oke  = $oke && !$con->hasError();
					}
				}
			}
			/*if(!$isian){
					$sql1 = "insert into pro_inventory_depot (id_datanya) values (NULL)";
					$con->setQuery($sql1);
					$oke  = $oke && !$con->hasError();
				}*/
		} else if ($id_jenis == '3') {
			$isian 		= false;
			$id_vendor 	= htmlspecialchars($_POST["id_vendor"], ENT_QUOTES);
			$id_vendor 	= ($id_vendor ? "'" . $id_vendor . "'" : 'NULL');

			if ($adj_inven > 0) {
				$isian = $isian || true;
				$sql1 = "
						insert into pro_inventory_depot (
							id_datanya, id_jenis, id_produk, tanggal_inven, keterangan, 
							id_terminal, id_vendor, adj_inven, 
							created_time, created_ip, created_by, lastupdate_time, lastupdate_ip, lastupdate_by
						) values (
							'" . $id_datanya . "', '" . $id_jenis . "', '" . $id_produk . "', '" . tgl_db($tgl) . "', '" . $keterangan . "', 
							'" . $id_terminal . "', " . $id_vendor . ", '" . ($adj_inven_sign == '-' ? $adj_inven_sign : '') . $adj_inven . "', 
							'" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "', '" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "'
						)
					";
				$con->setQuery($sql1);
				$oke  = $oke && !$con->hasError();
			}
			if (!$isian) {
				$sql1 = "insert into pro_inventory_depot (id_datanya) values (NULL)";
				$con->setQuery($sql1);
				$oke  = $oke && !$con->hasError();
			}
		} else if ($id_jenis == '4') {
			$isian 			= false;
			if (count($_POST["tank_satu_vendor_id"]) > 0) {
				foreach ($_POST["tank_satu_vendor_id"] as $idx => $val) {
					$tank_satu_vendor_id		= htmlspecialchars($_POST["tank_satu_vendor_id"][$idx], ENT_QUOTES);
					$tank_satu_vendor_nilai 	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["tank_satu_vendor_nilai"][$idx]), ENT_QUOTES);

					if ($tank_satu_vendor_id && $tank_satu_vendor_nilai) {
						$isian = $isian || true;
						$sql1 = "
								insert into pro_inventory_depot (
									id_datanya, id_jenis, id_produk, tanggal_inven, keterangan, 
									id_terminal, id_vendor, adj_inven, 
									created_time, created_ip, created_by, lastupdate_time, lastupdate_ip, lastupdate_by
								) values (
									'" . $id_datanya . "', '" . $id_jenis . "', '" . $id_produk . "', '" . tgl_db($tgl) . "', '" . $keterangan . "', 
									'" . $transfer_tanki_satu_dari . "', '" . $tank_satu_vendor_id . "', '-" . $tank_satu_vendor_nilai . "', 
									'" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "', '" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "'
								)
							";
						$con->setQuery($sql1);
						$oke  = $oke && !$con->hasError();

						$sql2 = "
								insert into pro_inventory_depot (
									id_datanya, id_jenis, id_produk, tanggal_inven, keterangan, 
									id_terminal, id_vendor, adj_inven, 
									created_time, created_ip, created_by, lastupdate_time, lastupdate_ip, lastupdate_by
								) values (
									'" . $id_datanya . "', '" . $id_jenis . "', '" . $id_produk . "', '" . tgl_db($tgl) . "', '" . $keterangan . "', 
									'" . $transfer_tanki_satu_ke . "', '" . $tank_satu_vendor_id . "', '" . $tank_satu_vendor_nilai . "', 
									'" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "', '" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "'
								)
							";
						$con->setQuery($sql2);
						$oke  = $oke && !$con->hasError();
					}
				}
			}
			if (!$isian) {
				$sql1 = "insert into pro_inventory_depot (id_datanya) values (NULL)";
				$con->setQuery($sql1);
				$oke  = $oke && !$con->hasError();
			}
		}
	}

	if ($oke) {
		$con->commit();
		$con->close();
		header("location: " . BASE_URL_CLIENT . "/vendor-inven-terminal.php");
		exit();
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}
}

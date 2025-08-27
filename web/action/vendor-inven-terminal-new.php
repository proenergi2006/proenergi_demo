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
$tgl_penerimaan		= htmlspecialchars($_POST["tgl_penerimaan"], ENT_QUOTES);

$id_terminal		= htmlspecialchars($_POST["id_terminal"], ENT_QUOTES);
$awal_inven_total	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["awal_inven_total"]), ENT_QUOTES);
$awal_inven_total 	= ($awal_inven_total ? $awal_inven_total : 0);

$adj_inven_sign		= htmlspecialchars($_POST["adj_inven_sign"], ENT_QUOTES);
$adj_inven			= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["adj_inven"]), ENT_QUOTES);
$adj_inven 			= ($adj_inven ? $adj_inven : 0);

$transfer_tanki_satu_dari	= htmlspecialchars($_POST["transfer_tanki_satu_dari"], ENT_QUOTES);
$transfer_tanki_satu_ke		= htmlspecialchars($_POST["transfer_tanki_satu_ke"], ENT_QUOTES);
$tank_satu_total			= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["tank_satu_total"]), ENT_QUOTES);

$keterangan	= htmlspecialchars($_POST["keterangan"], ENT_QUOTES);
//id_master, id_datanya, id_jenis, id_produk, id_terminal, id_vendor, id_po_supplier, id_po_receive, tanggal_inven, awal_inven, in_inven, out_inven, adj_inven, out_inven_virtual, keterangan, created_time, created_ip, created_by, lastupdate_time, lastupdate_ip, lastupdate_by

if ($act == 'add') {
	if ($id_jenis == "" || $id_produk == "" || $tgl == "") {
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else {
		$created_time 	= date('Y-m-d H:i:s');
		$id_datanya 	= md5(uniqid("1089", $id_jenis) . '-' . intval(microtime(true)) . '-' . date('YmdHis'));

		if ($id_jenis == '1') {
			$cek01 = "
					select a1.id_po_supplier, a1.id_po_receive 
					from new_pro_inventory_vendor_po a 
					join new_pro_inventory_vendor_po_receive a1 on a.id_master = a1.id_po_supplier 
					where a.id_produk = '" . $id_produk . "' and a.id_terminal = '" . $id_terminal . "' and a1.tgl_terima < '" . tgl_db($tgl) . "'
				";
			$res01 = $con->getResult($cek01);

			$oke = true;
			$con->beginTransaction();
			$con->clearError();

			if (count($res01) > 0) {
				foreach ($res01 as $data01) {
					$sql1 = "
							update new_pro_inventory_vendor_po_receive set is_aktif = 0 
							where id_po_supplier = '" . $data01['id_po_supplier'] . "' and id_po_receive = '" . $data01['id_po_receive'] . "'
						";
					$con->setQuery($sql1);
					$oke  = $oke && !$con->hasError();
				}
			}

			$isian = false;
			if (count($_POST["id_po_supplier"]) > 0) {
				foreach ($_POST["id_po_supplier"] as $idx => $val) {
					$id_po_supplier			= htmlspecialchars($_POST["id_po_supplier"][$idx], ENT_QUOTES);
					$id_po_receive			= htmlspecialchars($_POST["id_po_receive"][$idx], ENT_QUOTES);
					$id_invennya			= htmlspecialchars($_POST["id_invennya"][$idx], ENT_QUOTES);
					$awal_inven_nilai 		= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["awal_inven_nilai"][$idx]), ENT_QUOTES);
					$awal_inven_nilai 		= ($awal_inven_nilai ? $awal_inven_nilai : 0);

					if ($id_invennya) {
						$isian = $isian || true;
						$sql1 = "
								update new_pro_inventory_depot set id_jenis = '" . $id_jenis . "', tanggal_inven = '" . tgl_db($tgl) . "', 
								lastupdate_time = '" . $created_time . "', lastupdate_ip = '" . $_SERVER['REMOTE_ADDR'] . "', lastupdate_by = '" . $picnya . "' 
								where id_master = '" . $id_invennya . "'
							";
						$con->setQuery($sql1);
						$oke  = $oke && !$con->hasError();

						$sql2 = "
								update new_pro_inventory_vendor_po_receive set is_aktif = 1 
								where id_po_supplier = '" . $id_po_supplier . "' and id_po_receive = '" . $id_po_receive . "'
							";
						$con->setQuery($sql2);
						$oke  = $oke && !$con->hasError();
					}
				}
			} else {
				$isian = true;
				$sql1 = "
						insert into new_pro_inventory_depot (
							id_datanya, id_jenis, id_produk, id_terminal, tanggal_inven, awal_inven, keterangan, 
							created_time, created_ip, created_by, lastupdate_time, lastupdate_ip, lastupdate_by
						) values (
							'" . $id_datanya . "', '" . $id_jenis . "', '" . $id_produk . "', '" . $id_terminal . "', '" . tgl_db($tgl) . "', '" . $awal_inven_total . "', '" . $keterangan . "', 
							'" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "', '" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "'
						)
					";
				$con->setQuery($sql1);
				$oke  = $oke && !$con->hasError();
			}
		} else if ($id_jenis == '2') {
			$id_po_supplier = htmlspecialchars($_POST["id_po_supplier_sales"], ENT_QUOTES);
			$id_po_receive 	= htmlspecialchars($_POST["id_po_receive_sales"], ENT_QUOTES);
			$sales_inven 	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["sales_inven"]), ENT_QUOTES);
			$sales_inven 	= ($sales_inven ? $sales_inven : 0);

			$oke = true;
			$con->beginTransaction();
			$con->clearError();

			if ($sales_inven > 0) {
				$sql01 = "
						insert into new_pro_inventory_depot (
							id_datanya, id_jenis, id_produk, id_terminal, id_po_supplier, id_po_receive, tanggal_inven, out_inven, keterangan, 
							created_time, created_ip, created_by, lastupdate_time, lastupdate_ip, lastupdate_by
						) values (
							'" . $id_datanya . "', '" . $id_jenis . "', '" . $id_produk . "', '" . $id_terminal . "', '" . $id_po_supplier . "', '" . $id_po_receive . "', 
							'" . tgl_db($tgl) . "', '" . $sales_inven . "', '" . $keterangan . "', 
							'" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "', '" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "'
						)
					";
				$con->setQuery($sql01);
				$oke  = $oke && !$con->hasError();
			} else {
				$sql01 = "update new_pro_inventory_depot set keterangan = '' where id_master = '0'";
				$con->setQuery($sql01);
				$oke  = $oke && !$con->hasError();
			}
		} else if ($id_jenis == '3') {
			$oke = true;
			$con->beginTransaction();
			$con->clearError();

			if ($adj_inven > 0) {
				$sql01 = "
						insert into new_pro_inventory_depot (
							id_datanya, id_jenis, id_produk, id_terminal, tanggal_inven, adj_inven, keterangan, 
							created_time, created_ip, created_by, lastupdate_time, lastupdate_ip, lastupdate_by
						) values (
							'" . $id_datanya . "', '" . $id_jenis . "', '" . $id_produk . "', '" . $id_terminal . "', '" . tgl_db($tgl) . "', 
							'" . ($adj_inven_sign == '-' ? $adj_inven_sign : '') . $adj_inven . "', '" . $keterangan . "', 
							'" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "', '" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "'
						)
					";
				$con->setQuery($sql01);
				$oke  = $oke && !$con->hasError();
			} else {
				$sql01 = "update new_pro_inventory_depot set keterangan = '' where id_master = '0'";
				$con->setQuery($sql01);
				$oke  = $oke && !$con->hasError();
			}
		} else if ($id_jenis == '4') {
			$oke = true;
			$con->beginTransaction();
			$con->clearError();

			$isian 	= false;
			if (count($_POST["id_po_supplier_tf"]) > 0) {
				foreach ($_POST["id_po_supplier_tf"] as $idx => $val) {
					$id_po_supplier			= htmlspecialchars($_POST["id_po_supplier_tf"][$idx], ENT_QUOTES);
					$id_po_receive			= htmlspecialchars($_POST["id_po_receive_tf"][$idx], ENT_QUOTES);
					$tank_satu_vendor_nilai = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["tank_satu_vendor_nilai"][$idx]), ENT_QUOTES);
					$tank_satu_vendor_nilai = ($tank_satu_vendor_nilai ? $tank_satu_vendor_nilai : 0);

					if ($id_po_supplier && $id_po_receive && $tank_satu_vendor_nilai > 0) {
						$isian = $isian || true;
						$sql01 = "
								insert into new_pro_inventory_depot (
									id_datanya, id_jenis, id_produk, id_terminal, id_po_supplier, id_po_receive, tanggal_inven, adj_inven, keterangan, 
									created_time, created_ip, created_by, lastupdate_time, lastupdate_ip, lastupdate_by
								) values (
									'" . $id_datanya . "', '" . $id_jenis . "', '" . $id_produk . "', '" . $transfer_tanki_satu_dari . "', '" . $id_po_supplier . "', '" . $id_po_receive . "', 
									'" . tgl_db($tgl) . "', '-" . $tank_satu_vendor_nilai . "', '" . $keterangan . "', 
									'" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "', '" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "'
								)
							";
						$con->setQuery($sql01);
						$oke  = $oke && !$con->hasError();

						$sql02 = "
								insert into new_pro_inventory_depot (
									id_datanya, id_jenis, id_produk, id_terminal, id_po_supplier, id_po_receive, tanggal_inven, adj_inven, keterangan, 
									created_time, created_ip, created_by, lastupdate_time, lastupdate_ip, lastupdate_by
								) values (
									'" . $id_datanya . "', '" . $id_jenis . "', '" . $id_produk . "', '" . $transfer_tanki_satu_ke . "', '" . $id_po_supplier . "', '" . $id_po_receive . "', 
									'" . tgl_db($tgl_penerimaan) . "', '" . $tank_satu_vendor_nilai . "', '" . $keterangan . "', 
									'" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "', '" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "'
								)
							";
						$con->setQuery($sql02);
						$oke  = $oke && !$con->hasError();
					}
				}
			}

			if (!isian) {
				$sql01 = "update new_pro_inventory_depot set keterangan = '' where id_master = '0'";
				$con->setQuery($sql01);
				$oke  = $oke && !$con->hasError();
			}
		}
	}

	if ($oke) {
		$con->commit();
		$con->close();
		header("location: " . BASE_URL_CLIENT . "/vendor-inven-terminal-new.php");
		exit();
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}
} else if ($act == 'hapus') {
	$param 	= htmlspecialchars(paramDecrypt($_POST["param"]), ENT_QUOTES);
	$post 	= explode("#|#", $param);
	$file	= isset($post[0]) ? htmlspecialchars($post[0], ENT_QUOTES) : null;
	$id1	= isset($post[1]) ? htmlspecialchars($post[1], ENT_QUOTES) : null;
	$id2	= isset($post[2]) ? htmlspecialchars($post[2], ENT_QUOTES) : null;
	$id3	= isset($post[3]) ? htmlspecialchars($post[3], ENT_QUOTES) : null;
	$id4	= isset($post[4]) ? htmlspecialchars($post[4], ENT_QUOTES) : null;

	if ($id1 == "Data Awal") {
		$created_time = date('Y-m-d H:i:s');

		$cek01 = "
				select a.id_master, a.tanggal_inven, a.id_po_supplier, a.id_po_receive 
				from new_pro_inventory_depot a 
				where a.id_produk = '" . $id3 . "' and a.id_terminal = '" . $id4 . "' and a.id_jenis = 1 
			";
		$res01 = $con->getResult($cek01);
		if (count($res01) > 0) {
			$oke = true;
			$con->beginTransaction();
			$con->clearError();

			foreach ($res01 as $data01) {
				if ($data01['id_po_supplier'] && $data01['id_po_receive']) {
					$sql01 = "
							update new_pro_inventory_depot a  
							join new_pro_inventory_vendor_po_receive b on a.id_po_supplier = b.id_po_supplier and a.id_po_receive = b.id_po_receive 
							set a.id_jenis = 21, a.tanggal_inven = b.tgl_terima, 
							a.lastupdate_time = '" . $created_time . "', a.lastupdate_ip = '" . $_SERVER['REMOTE_ADDR'] . "', a.lastupdate_by = '" . $picnya . "'  
							where a.id_master = '" . $data01['id_master'] . "' 
						";
					$con->setQuery($sql01);
					$oke  = $oke && !$con->hasError();
				} else {
					$sql01 = "delete from new_pro_inventory_depot where id_master = '" . $data01['id_master'] . "'";
					$con->setQuery($sql01);
					$oke  = $oke && !$con->hasError();
				}
			}

			$sql02 = "
					update new_pro_inventory_vendor_po a  
					join new_pro_inventory_vendor_po_receive a1 on a.id_master = a1.id_po_supplier    
					set a1.is_aktif = 1 
					where a.id_produk = '" . $id3 . "' and a.id_terminal = '" . $id4 . "' and a1.tgl_terima < '" . $res01[0]['tanggal_inven'] . "' 
				";
			$con->setQuery($sql02);
			$oke  = $oke && !$con->hasError();

			if ($oke) {
				$con->commit();
				$con->close();
				$arr["error"] = "";
			} else {
				$con->rollBack();
				$con->clearError();
				$con->close();
				$arr["error"] = "Maaf Data tidak dapat dihapus..";
			}
		}
	} else {
		$sql01 = "delete from new_pro_inventory_depot where id_datanya = '" . $id2 . "'";
		$con->setQuery($sql01);

		if (!$con->hasError()) {
			$con->close();
			$arr["error"] = "";
		} else {
			$con->clearError();
			$con->close();
			$arr["error"] = "Maaf Data tidak dapat dihapus..";
		}
	}

	echo json_encode($arr);
	exit;
}

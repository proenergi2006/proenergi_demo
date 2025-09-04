<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$arr	= array();
$param 	= htmlspecialchars(paramDecrypt($_POST["param"]), ENT_QUOTES);
$post 	= explode("#|#", $param);
$file	= isset($post[0]) ? htmlspecialchars($post[0], ENT_QUOTES) : null;
$id1	= isset($post[1]) ? htmlspecialchars($post[1], ENT_QUOTES) : null;
$id2	= isset($post[2]) ? htmlspecialchars($post[2], ENT_QUOTES) : null;
$id3	= isset($post[3]) ? htmlspecialchars($post[3], ENT_QUOTES) : null;
$id4	= isset($post[4]) ? htmlspecialchars($post[4], ENT_QUOTES) : null;
$msg	= "Maaf Data tidak dapat dihapus..";
$trans	= false;
$extend	= false;

switch ($file) {
	case "roles":
		$sql = "delete from acl_role where id_role = '" . $id1 . "'";
		break;
	case "user":
		$sql = "delete from acl_user where id_user = '" . $id1 . "'";
		break;
	case "activeAcc":
		$msg = "Maaf sistem mengalami gangguan silahkan coba lagi";
		$id2 = htmlspecialchars($post[2], ENT_QUOTES);
		$arr = array("deactivate" => 0, "activate" => 1);
		$sql = "update acl_user set is_active = '" . $arr[$id1] . "', lastupdate_time = NOW(), lastupdate_ip = '" . $_SERVER['REMOTE_ADDR'] . "', 
					lastupdate_by = '" . $_SESSION['NAMA'] . "' where id_user = '" . $id2 . "'";
		break;
	case "form_unblock":
		// Ambil semua file terkait dari DB
		$sqlFile = "SELECT nama_file FROM pro_lampiran_unblock WHERE id_unblock = '$id1'";
		$resultFile = $con->getResult($sqlFile);

		$hapusSemuaFile = true;

		if ($resultFile) {
			foreach ($resultFile as $fileRow) {
				$filePath = $public_base_directory . "/files/uploaded_user/file_unblock/" . $fileRow['nama_file'];

				if (file_exists($filePath)) {
					if (!unlink($filePath)) {
						// Gagal hapus file
						$hapusSemuaFile = false;
						error_log("Gagal hapus file: $filePath");
						break; // stop looping, tidak perlu coba hapus file lainnya
					}
				}
			}
		}

		if ($hapusSemuaFile) {
			// Hapus lampiran di DB
			$sqlLampiran = "DELETE FROM pro_lampiran_unblock WHERE id_unblock = '$id1'";
			$con->setQuery($sqlLampiran);
			$oke  = $oke && !$con->hasError();

			$sql = "DELETE from pro_unblock_customer where id = '" . $id1 . "' AND disposisi = 0";
			break;
		}
	case "master_group_cabang":
		$sql = "delete from pro_master_group_cabang where id_gu = '" . $id1 . "'";
		break;
	case "master_cabang":
		$sql = "delete from pro_master_cabang where id_master = '" . $id1 . "'";
		break;
	case "master_volume":
		$sql = "delete from pro_master_volume where id_master = '" . $id1 . "'";
		break;
	case "master_harga_minyak":
		$sql = "delete from pro_master_harga_minyak where periode_awal = '" . $id1 . "' and periode_akhir = '" . $id2 . "' and id_area = '" . $id3 . "' and produk = '" . $id4 . "'";
		break;
	case "attach_harga_minyak":
		$sql 	= "delete from pro_attach_harga_minyak where id_master = '" . $id1 . "'";
		$extend = true;
		break;
	case "master_harga_pertamina":
		$sql = "delete from pro_master_harga_pertamina where periode_awal = '" . $id1 . "' and periode_akhir = '" . $id2 . "' and id_area = '" . $id3 . "' and id_produk = '" . $id4 . "'";
		break;
	case "manual_segel":
		$sql = "delete from pro_manual_segel where id_master = '" . $id1 . "'";
		break;
	case "master_harga_tebus":
		$sql = "delete from pro_master_harga_tebus where id_master = '" . $id1 . "'";
		break;
	case "master_ongkos_angkut":
		$sql = "delete from pro_master_ongkos_angkut where id_transportir = '" . $id1 . "' and id_wil_angkut = '" . $id2 . "'";
		break;
	case "master_transportir":
		$sql 	= "delete from pro_master_transportir where id_master = '" . $id1 . "'";
		$extend = true;
		break;
	case "master_terminal":
		$sql 	= "delete from pro_master_terminal where id_master = '" . $id1 . "'";
		$extend = true;
		break;
	case "master_produk":
		$sql = "delete from pro_master_produk where id_master = '" . $id1 . "'";
		break;
	case "master_transportir_sopir":
		$cek = "select count(*) from pro_po_detail where sopir_po = '" . $id1 . "'";
		$row = $con->getOne($cek);
		if ($row > 0) {
			$arr["error"] = "Maaf Data tidak dapat dihapus..";
			echo json_encode($arr);
			exit;
		} else {
			$sql = "delete from pro_master_transportir_sopir where id_master = '" . $id1 . "'";
			$extend = true;
		}
		break;
	case "master_transportir_mobil":
		$cek = "select count(*) from pro_po_detail where mobil_po = '" . $id1 . "'";
		$row = $con->getOne($cek);
		if ($row > 0) {
			$arr["error"] = "Maaf Data tidak dapat dihapus..";
			echo json_encode($arr);
			exit;
		} else {
			$sql = "delete from pro_master_transportir_mobil where id_master = '" . $id1 . "'";
			$extend = true;
		}
		break;
	case "master_wilayah_angkut":
		$sql = "delete from pro_master_wilayah_angkut where id_master = '" . $id1 . "'";
		break;
	case "master_insentif_pricelist":
		$sql = "delete from pro_master_pl_insentif 
					where id_master = '" . $id1 . "'
					 ";
		break;
	case "master_insentif_poin":
		$sql = "delete from pro_master_poin_insentif 
					where id_master = '" . $id1 . "'
					 ";
		break;
	case "master_volume_angkut":
		$sql = "delete from pro_master_volume_angkut where id_master = '" . $id1 . "'";
		break;
	case "master_pbbkb":
		$sql = "delete from pro_master_pbbkb where id_master = '" . $id1 . "'";
		break;
	case "master_area":
		$sql = "delete from pro_master_area where id_master = '" . $id1 . "'";
		break;
	case "master_vendor":
		$sql = "delete from pro_master_vendor where id_master = '" . $id1 . "'";
		break;
	case "master_permintaan_penawaran":
		$sql 	= "delete from pro_permintaan_penawaran where id_pmnt = '" . $id1 . "'";
		$extend = true;
		break;
	case "master_permintaan_order":
		$sql 	= "delete from pro_permintaan_order where id_pmnt_order = '" . $id1 . "'";
		$extend = true;
		break;
	case "master_oa_kapal":
		$sql = "delete from pro_master_oa_kapal where id_master = '" . $id1 . "'";
		break;
	case "master_ruangan":
		$sql = "delete from pro_master_ruangan where id_ruangan = '" . $id1 . "'";
		$extend = true;
		break;
	case "master_zoom":
		$sql = "delete from pro_master_zoom where id_zoom = '" . $id1 . "'";
		$extend = true;
		break;
	case "master_mobil":
		$sql = "delete from pro_master_mobil where id_mobil = '" . $id1 . "'";
		$extend = true;
		break;
	case "customer":
		$sql = "delete from pro_customer where id_customer = '" . $id1 . "'";
		break;
	case "penawaran":
		$cek = "select flag_approval from pro_penawaran where id_penawaran = '" . $id1 . "'";
		$row = $con->getRecord($cek);
		if (!$row['flag_approval']) {
			$sql = "delete from pro_penawaran where id_penawaran = '" . $id1 . "'";
		} else {
			$arr["error"] = "Penawaran dengan No. Ref " . str_pad($id1, 4, '0', STR_PAD_LEFT) . " tidak dapat dihapus";
			echo json_encode($arr);
			exit;
		}
		break;
	case "inventory_vendor":
		$cek = "select * from pro_inventory_vendor_po where id_master = '" . $id1 . "'";
		$row = $con->getRecord($cek);
		if (!$row['is_diterima']) {
			$sql = "delete from pro_inventory_vendor_po where id_master = '" . $id1 . "'";
		} else {
			$arr["error"] = "Maaf, data tidak dapat dihapus, karena sudah terdapat data inventory";
			echo json_encode($arr);
			exit;
		}
		break;
	case "lcr":
		$sql 	= "delete from pro_customer_lcr where id_customer = '" . $id1 . "' and id_lcr = '" . $id2 . "'";
		$extend = true;
		break;
	case "customer_update":
		$sql 	= "delete from pro_customer_update where id_cu = '" . $id1 . "'";
		$extend = true;
		break;
	case "po_customer":
		$data_poc = "select id_poc, id_customer, harga_poc, volume_poc from pro_po_customer where id_poc = '" . $id1 . "'";
		$row_poc = $con->getRecord($data_poc);

		$total_order = (float)$row_poc['harga_poc'] * (float)$row_poc['volume_poc'];

		$update_cl_cust = "update pro_customer set credit_limit_reserved = credit_limit_reserved - '" . $total_order . "' where id_customer = '" . $row_poc['id_customer'] . "'";
		$con->setQuery($update_cl_cust);
		$oke  = $oke && !$con->hasError();

		$sql 	= "delete from pro_po_customer where id_poc = '" . $id1 . "'";
		$con->setQuery($sql);
		$oke  = $oke && !$con->hasError();

		$delete_history_ar = "delete from pro_history_ar_customer where id_poc = '" . $id1 . "' and kategori = 1";
		$con->setQuery($delete_history_ar);
		$oke  = $oke && !$con->hasError();

		$sql2 	= "delete from pro_poc_penerima_refund where id_poc = '" . $id1 . "'";
		$con->setQuery($sql2);
		$oke  = $oke && !$con->hasError();
		$extend = true;
		break;
	case "do_truck":
		$trans	= true;
		$msg = "Maaf, sistem mengalami kendala teknis. Silahkan coba lagi";
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		$sql1 = "update pro_po_ds_detail set is_cancel = 1, tanggal_cancel = NOW() where id_dsd = '" . $id1 . "'";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();

		$tTgl = explode("-", $id2);
		$tJam = explode(":", $id3);
		if (intval($tJam[0]) < 7) {
			$malam 	= true;
			$oTgl 	= date("Y/m/d", mktime(0, 0, 0, $tTgl[1], $tTgl[2] - 1, $tTgl[0]));
		} else {
			$malam 	= false;
			$oTgl 	= $id2;
		}

		$cols = ($malam) ? "out_malam = out_malam - " . $id4 : "out_pagi = out_pagi - " . $id4;
		$sql2 = "update pro_master_inventory_out set lastupdate_time = NOW(), lastupdate_ip = '" . $_SERVER['REMOTE_ADDR'] . "', lastupdate_by = '" . $pic . "', out_cancel = out_cancel + " . $id4 . ", " . $cols . " where tanggal_inv = '" . $oTgl . "' and id_terminal = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["terminal"]) . "'";
		$con->setQuery($sql2);
		$oke  = $oke && !$con->hasError();

		break;
	case "disposisi_akta":
		$trans	= true;
		$oke 	= true;
		$con->beginTransaction();
		$con->clearError();

		$cek1 = "select id_order, id_akta, is_akhir from sinori_disposisi_akta where id_da = '" . $id1 . "'";
		$res1 = $con->getRecord($cek1);

		$sql1 = "delete from sinori_disposisi_akta where id_da = '" . $id1 . "'";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();

		if ($res1['is_akhir'] == 1) {
			$cek2 = "select id_da from sinori_disposisi_akta where id_order = '" . $res1['id_order'] . "' and id_akta = '" . $res1['id_akta'] . "' order by id_da desc";
			$res2 = $con->getOne($cek2);

			$sql2 = "update sinori_disposisi_akta set is_akhir = 1 where id_da = '" . $res2 . "'";
			$con->setQuery($sql2);
			$oke  = $oke && !$con->hasError();
		}
		break;
	case "tagihan_po_transportir":
		$sql = "update pro_po set po_accepted = 1, tgl_po_accepted = NOW() where id_po = '" . $id1 . "'";
		break;
	case "delivery_kapal":
		$cek = "select * from pro_po_ds_kapal where id_dsk = '" . $id1 . "'";
		$row = $con->getRecord($cek);
		if ($row['is_delivered'] || $row['is_cancel']) {
			$arr["error"] = "Maaf, data tidak dapat dihapus..";
			echo json_encode($arr);
			exit;
		} else {
			$sql = "delete from pro_po_ds_kapal where id_dsk = '" . $id1 . "'";
		}
		break;
	case "test_employee":
		$sql = "delete from menu_employee where id = '" . $id1 . "'";
		break;
	case "peminjaman_mobil":
		$sql 	= "delete from pro_peminjaman_mobil where id_peminjaman = '" . $id1 . "'";
		//$sql 	= "update pro_peminjaman_mobil set deleted_time = NOW() where id_peminjaman = '".$id1."'";
		$extend = true;
		break;
	case "forecast":
		$sql 	= "delete from forecast where id = '" . $id1 . "'";
		$extend = true;
		break;
	case "database_fuel":
		$sql 	= "update pro_database_fuel set deleted_time = NOW() where id_database_fuel = '" . $id1 . "'";
		$extend = true;
		break;
	case "database_lubricant_oil":
		$sql 	= "update pro_database_lubricant_oil set deleted_time = NOW() where id_database_lubricant_oil = '" . $id1 . "'";
		$extend = true;
		break;
	case "marketing_report":
		$sql 	= "update pro_marketing_report_master set deleted_time = NOW() where id_mkt_report = '" . $id1 . "'";
		$extend = true;
		break;
	case "marketing_reimbursement":
		$sql 	= "update pro_marketing_reimbursement set deleted_time = NOW() where id_marketing_reimbursement = '" . $id1 . "'";
		$extend = true;
		break;
	case "marketing_mom":
		$sql 	= "update pro_marketing_mom set deleted_time = NOW() where id_marketing_mom = '" . $id1 . "'";
		$extend = true;
		break;
	case "vendor_inven_terminal":
		$sql = "delete from pro_inventory_depot where id_datanya = '" . $id1 . "'";
		break;
	case "mapping_marketing":
		$sql = "delete from pro_mapping_marketing where id_mapping = '" . $id1 . "'";
		break;
}

if (!$trans) {
	$con->setQuery($sql);
	if (!$con->hasError()) {
		$con->close();
		if ($extend) extend_delete_file($public_base_directory, $file, $id1, $id2, $id3, $id4);
		$arr["error"] = "";
	} else {
		$con->clearError();
		$con->close();
		$arr["error"] = $msg;
	}
} else {
	if ($oke) {
		$con->commit();
		$con->close();
		if ($extend) extend_delete_file($public_base_directory, $file, $id1, $id2, $id3, $id4);
		$arr["error"] = "";
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$arr["error"] = $msg;
	}
}

echo json_encode($arr);

function extend_delete_file($path, $file, $id1, $id2 = '', $id3 = '', $id4 = '')
{
	switch ($file) {
		case "attach_harga_minyak":
			$arrFiles = glob($path . "/files/uploaded_user/lampiran/aPrice_" . $id1 . "_*.{jpg,jpeg,gif,png,pdf,xls,xlsx}", GLOB_BRACE);
			if (count($arrFiles) > 0) {
				foreach ($arrFiles as $data) {
					unlink($data);
				}
			}
			break;
		case "master_transportir":
			$arrFiles = glob($path . "/files/uploaded_user/lampiran/sup_" . $id1 . "_*.{jpg,jpeg,gif,png,pdf,zip,rar}", GLOB_BRACE);
			if (count($arrFiles) > 0) {
				foreach ($arrFiles as $data) {
					unlink($data);
				}
			}
			break;
		case "master_terminal":
			$arrFiles = glob($path . "/files/uploaded_user/lampiran/dokTerminal_" . $id1 . "_*.{jpg,jpeg,gif,png,pdf,zip,rar}", GLOB_BRACE);
			if (count($arrFiles) > 0) {
				foreach ($arrFiles as $data) {
					unlink($data);
				}
			}
			break;
		case "master_transportir_sopir":
			$arrFiles1 = glob($path . "/files/uploaded_user/lampiran/sopir_" . $id1 . "_*.{jpg,jpeg,gif,png,pdf,zip,rar}", GLOB_BRACE);
			if (count($arrFiles1) > 0) {
				foreach ($arrFiles1 as $data1)
					unlink($data1);
			}
			$arrFiles2 = glob($path . "/files/uploaded_user/lampiran/photo_" . $id1 . "_*.{jpg,jpeg,gif,png}", GLOB_BRACE);
			if (count($arrFiles2) > 0) {
				foreach ($arrFiles2 as $data2)
					unlink($data2);
			}
			break;
		case "master_transportir_mobil":
			$arrFiles1 = glob($path . "/files/uploaded_user/lampiran/mobil_" . $id1 . "_*.{jpg,jpeg,gif,png,pdf,zip,rar}", GLOB_BRACE);
			if (count($arrFiles1) > 0) {
				foreach ($arrFiles1 as $data1)
					unlink($data1);
			}
			$arrFiles2 = glob($path . "/files/uploaded_user/lampiran/pics_" . $id1 . "_*.{jpg,jpeg,gif,png}", GLOB_BRACE);
			if (count($arrFiles2) > 0) {
				foreach ($arrFiles2 as $data2)
					unlink($data2);
			}
			break;
		case "master_permintaan_penawaran":
			$arrFiles = glob($path . "/files/uploaded_user/lampiran/tawar_" . $id1 . "_*.{jpg,jpeg,gif,png,pdf,zip,rar}", GLOB_BRACE);
			if (count($arrFiles) > 0) {
				foreach ($arrFiles as $data) {
					unlink($data);
				}
			}
			break;
		case "master_permintaan_order":
			$arrFiles = glob($path . "/files/uploaded_user/lampiran/order_" . $id1 . "_*.{jpg,jpeg,gif,png,pdf,zip,rar}", GLOB_BRACE);
			if (count($arrFiles) > 0) {
				foreach ($arrFiles as $data) {
					unlink($data);
				}
			}
			break;
		case "lcr":
			$tmp = array("peta_", "bongkar_", "kantor_", "media_", "storage_", "inlet_", "ukur_", "keterangan_", "jalan_");
			foreach ($tmp as $ktg) {
				foreach (glob($path . "/files/uploaded_user/files/" . $ktg . $id1 . "_" . $id2 . "_*.{jpg,jpeg,gif,png}", GLOB_BRACE) as $arr1) {
					$thumb = str_replace(basename($arr1), "", $arr1) . "thumbnail/" . basename($arr1);
					if (file_exists($arr1)) unlink($arr1);
					if (file_exists($thumb)) unlink($thumb);
				}
			}
			break;
		case "customer_update":
			$arrFiles = glob($path . "/files/uploaded_user/lampiran/PUD_" . $id1 . "_*.{jpg,jpeg,gif,png,pdf,zip,rar}", GLOB_BRACE);
			if (count($arrFiles) > 0) {
				foreach ($arrFiles as $data) {
					unlink($data);
				}
			}
			break;
		case "master_ruangan":
			$arrFiles = glob($path . "/files/uploaded_user/lampiran/ruangan/PICRM_" . $id1 . "_*.{jpg,jpeg,gif,png,pdf,zip,rar}", GLOB_BRACE);
			if (count($arrFiles) > 0) {
				foreach ($arrFiles as $data) {
					unlink($data);
				}
			}
			break;
		case "master_mobil":
			$arrFiles = glob($path . "/files/uploaded_user/lampiran/mobil_opr/PICMM_" . $id1 . "_*.{jpg,jpeg,gif,png,pdf,zip,rar}", GLOB_BRACE);
			if (count($arrFiles) > 0) {
				foreach ($arrFiles as $data) {
					unlink($data);
				}
			}
			break;
	}
}

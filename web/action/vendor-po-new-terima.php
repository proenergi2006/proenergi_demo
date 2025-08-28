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
$act	= ($enk['act'] ? $enk['act'] : htmlspecialchars($_POST["act"], ENT_QUOTES));

$idnya01 		= htmlspecialchars($_POST["idnya01"], ENT_QUOTES);
$idnya02 		= htmlspecialchars($_POST["idnya02"], ENT_QUOTES);
$tgl_terima		= htmlspecialchars($_POST["tgl_terima"], ENT_QUOTES);
$harga_tebus	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["harga_tebus"]), ENT_QUOTES);
$volume_bol	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["volume_bol"]), ENT_QUOTES);
$volume_terima	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["volume_terima"]), ENT_QUOTES);
$nama_pic		= htmlspecialchars($_POST["nama_pic"], ENT_QUOTES);

//Parameter untuk Accurate
$gudang			= htmlspecialchars($_POST["gudang"], ENT_QUOTES);
$kode_item_accurate = $_POST["kode_item_accurate"];
$no_terima		= $_POST["no_terima"];
$unit_price		= $_POST["unit_price"];
$nomor_po		= $_POST["nomor_po"];

$filePhoto1 	= htmlspecialchars($_FILES['file_template']['name'], ENT_QUOTES);
$sizePhoto1 	= htmlspecialchars($_FILES['file_template']['size'], ENT_QUOTES);
$tempPhoto1 	= htmlspecialchars($_FILES['file_template']['tmp_name'], ENT_QUOTES);
$tipePhoto1 	= htmlspecialchars($_FILES['file_template']['type'], ENT_QUOTES);

$folder 		= date("Ym");
$pathnya 		= $public_base_directory . '/files/uploaded_user/lampiran';

$maxFileSize = 10 * 1024 * 1024;

if ($sizePhoto1 > $maxFileSize) {
	$flash->add("error", 'Maaf, ukuran file melebihi batas maksimum 10 MB.');
	header("location: " . BASE_URL_CLIENT . "/vendor-po-new-terima.php?" . paramEncrypt("idr=" . $idnya01));
	exit;
}

//get cabang 
$id_cabang = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

$queryget_cabang = "SELECT * FROM pro_master_cabang WHERE id_master = '" . $id_cabang . "'";
$rowget_cabang = $con->getRecord($queryget_cabang);

if ($act == 'cek') {
} else if ($act == 'add') {
	$kuenya 	= "select LPAD(cast((select nextval(new_pro_inventory_vendor_po_receive_seq)) as varchar(10)), 9, '0') as idnya";
	$arrkue 	= $con->getRecord($kuenya);
	$idnya02 	= date("Ym") . $arrkue['idnya'];

	if ($idnya02) {
		if ($filePhoto1) {
			$fileExt 		= strtolower(pathinfo($filePhoto1, PATHINFO_EXTENSION));
			$fileName 		= $folder . '/terimaposupplier_' . $idnya02 . '_' . md5(basename($filePhoto1, $fileExt)) . '.' . $fileExt;
			$fileOriginName = sanitize_filename($filePhoto1);
			$isUpload 		= true;
		} else {
			$fileName 		= $arrget[$idx]['filenya'];
			$fileOriginName = $arrget[$idx]['file_upload_ori'];
			$isUpload 		= false;
		}

		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		$sql1 = "
				insert into new_pro_inventory_vendor_po_receive(id_po_receive, id_po_supplier, nama_pic, tgl_terima, volume_bol, volume_terima, harga_tebus, file_upload, file_upload_ori, 
				created_time, created_ip, created_by) values ('" . $idnya02 . "', '" . $idnya01 . "', '" . $nama_pic . "', '" . tgl_db($tgl_terima) . "', '" . $volume_bol . "', '" . $volume_terima . "', '" . $harga_tebus . "', 
				'" . $fileName . "', '" . $fileOriginName . "', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "')";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();

		$sql2 = "
				insert into new_pro_inventory_depot (id_datanya, id_jenis, id_produk, id_terminal, id_vendor, id_po_supplier, id_po_receive, tanggal_inven, in_inven, keterangan, 
				created_time, created_ip, created_by, lastupdate_time, lastupdate_ip, lastupdate_by) (
					select 'generated_po', '21', id_produk, id_terminal, id_vendor, id_master, '" . $idnya02 . "', '" . tgl_db($tgl_terima) . "', '" . $volume_terima . "', 
					'Penerimaan stock dari PO supplier', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "', 
					NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "' 
					from new_pro_inventory_vendor_po where id_master = '" . $idnya01 . "'
				)";
		$con->setQuery($sql2);
		$oke  = $oke && !$con->hasError();

		$sql3 = "
				insert into new_pro_inventory_harga_tebus (id_po_supplier, id_po_receive, id_produk, id_terminal, tgl_terima, harga_tebus) (
					select '" . $idnya01 . "', '" . $idnya02 . "', id_produk, id_terminal, '" . tgl_db($tgl_terima) . "', '" . $harga_tebus . "' 
					from new_pro_inventory_vendor_po where id_master = '" . $idnya01 . "'
				)";
		$con->setQuery($sql3);
		$oke  = $oke && !$con->hasError();

		if ($oke) {
			$get_po_supplier = "SELECT * FROM new_pro_inventory_vendor_po WHERE id_master = '" . $idnya01 . "'";
			$res_po = $con->getRecord($get_po_supplier);

			$id_accurate_po = $res_po['id_accurate'];
			$data_po = http_build_query([
				'id' => $id_accurate_po,
			]);

			//GET detail PO Accurate
			$url_ponya = 'https://zeus.accurate.id/accurate/api/purchase-order/detail.do?' . $data_po;

			$result_po = curl_get($url_ponya);

			if ($result_po['s'] == true) {

				//Save ke Accurate
				$urlnya = 'https://zeus.accurate.id/accurate/api/receive-item/save.do';
				// Data yang akan dikirim dalam format JSON
				$data = array(
					"receiveNumber" => $no_terima,
					"number" => $no_terima,
					"transDate" => $tgl_terima,
					"vendorNo" => $result_po['d']['vendor']['vendorNo'],
					"description" => "Terima barang dari PO " . $res_po['nomor_po'],
					"toAddress" => $result_po['d']['toAddress'],
					'branchName'  => $rowget_cabang['nama_cabang'] == 'Kantor Pusat' ? 'Head Office' : $rowget_cabang['nama_cabang'],
					'detailItem' => array([
						'itemNo'       => paramDecrypt($kode_item_accurate),
						'quantity'     => $volume_terima,
						'unitPrice'    => paramDecrypt($unit_price),
						'purchaseOrderNumber' => paramDecrypt($nomor_po),
						"warehouseName" => paramDecrypt($gudang),
					])
				);


				// Mengonversi data menjadi format JSON
				$jsonData = json_encode($data);

				$result = curl_post($urlnya, $jsonData);

				if ($result['s'] == true) {
					$update = "UPDATE new_pro_inventory_vendor_po_receive set id_accurate = '" . $result['r']['id'] . "', no_terima = '" . $no_terima . "' WHERE id_po_receive = " . $idnya02;
					$con->setQuery($update);

					$con->commit();
					$con->close();

					if ($isUpload) {
						if (!file_exists($pathnya . '/' . $folder . '/')) mkdir($pathnya . '/' . $folder, 0777);

						$tujuan  = $pathnya . '/' . $fileName;
						$mantab  = move_uploaded_file($tempPhoto1, $tujuan);
					}

					header("location: " . BASE_URL_CLIENT . "/vendor-po-new-terima.php?" . paramEncrypt("idr=" . $idnya01));
					exit();
				} else {
					$con->rollBack();
					$con->clearError();
					$con->close();
					$flash->add("error", $result["d"][0] . " - Response dari Accurate", BASE_REFERER);
				}
			} else {
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", "PO Supplier pada accurate tidak ada", BASE_REFERER);
			}
			// $con->commit();
			// $con->close();

			// if ($isUpload) {
			// 	if (!file_exists($pathnya . '/' . $folder . '/')) mkdir($pathnya . '/' . $folder, 0777);

			// 	$tujuan  = $pathnya . '/' . $fileName;
			// 	$mantab  = move_uploaded_file($tempPhoto1, $tujuan);
			// }

			// header("location: " . BASE_URL_CLIENT . "/vendor-po-new-terima.php?" . paramEncrypt("idr=" . $idnya01));
			// exit();
		} else {
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", 'Maaf Data Gagal Disimpan...', BASE_REFERER);
		}
	}
} else if ($act == 'update') {
	$isUpload = false;
	if ($filePhoto1) {
		$cek01 = "
				select a.file_upload, a.file_upload_ori from new_pro_inventory_vendor_po_receive a 
				where a.id_po_supplier = '" . $idnya01 . "' and a.id_po_receive = '" . $idnya02 . "'
			";
		$res01 = $con->getResult($cek01);

		$fileExt 		= strtolower(pathinfo($filePhoto1, PATHINFO_EXTENSION));
		$fileName 		= $folder . '/terimaposupplier_' . $idnya02 . '_' . md5(basename($filePhoto1, $fileExt)) . '.' . $fileExt;
		$fileOriginName = sanitize_filename($filePhoto1);
		$isUpload 		= true;
	}

	$cek02 = "select id_produk, id_terminal, harga_tebus from new_pro_inventory_vendor_po a where a.id_master = '" . $idnya01 . "'";
	$res02 = $con->getRecord($cek02);

	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	$sql1 = "update new_pro_inventory_vendor_po_receive set nama_pic = '" . $nama_pic . "', tgl_terima = '" . tgl_db($tgl_terima) . "', 
		volume_bol = '" . $volume_bol . "', volume_terima = '" . $volume_terima . "', harga_tebus = '" . $harga_tebus . "'";

	if ($isUpload) {
		$sql1 .= ", file_upload = '" . $fileName . "', file_upload_ori = '" . $fileOriginName . "'";
	}

	$sql1 .= ", lastupdate_time = NOW(), lastupdate_ip = '" . $_SERVER['REMOTE_ADDR'] . "', lastupdate_by = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "'
		where id_po_supplier = '" . $idnya01 . "' and id_po_receive = '" . $idnya02 . "'";
	$con->setQuery($sql1);
	$oke  = $oke && !$con->hasError();

	$sql2 = "
			update new_pro_inventory_depot set in_inven = '" . $volume_terima . "', tanggal_inven = '" . tgl_db($tgl_terima) . "', 
			lastupdate_time = NOW(), lastupdate_ip = '" . $_SERVER['REMOTE_ADDR'] . "', lastupdate_by = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "' 
			where id_master = (
				select id_master from new_pro_inventory_depot a 
				where a.id_po_supplier = '" . $idnya01 . "' and a.id_po_receive = '" . $idnya02 . "' and a.id_jenis = 21
			)
		";
	$con->setQuery($sql2);
	$oke  = $oke && !$con->hasError();

	if ($res02['harga_tebus'] != $harga_tebus) {
		$sql3 = "
				insert into new_pro_inventory_harga_tebus (id_po_supplier, id_po_receive, id_produk, id_terminal, tgl_terima, harga_tebus) values (
				'" . $idnya01 . "', '" . $idnya02 . "', '" . $res02['id_produk'] . "', '" . $res02['id_terminal'] . "', '" . tgl_db($tgl_terima) . "', '" . $harga_tebus . "')";
		$con->setQuery($sql3);
		$oke  = $oke && !$con->hasError();
	}

	if ($oke) {
		$data_receive = "SELECT * FROM new_pro_inventory_vendor_po_receive WHERE id_po_receive = '" . $idnya02 . "'";
		$rowget = $con->getRecord($data_receive);

		$id_accurate2 = $rowget['id_accurate'];
		$data_ri_receive_detail = http_build_query(
			[
				'id' => $id_accurate2,
			]
		);

		//GET detail DO Accurate
		$url_detail_ri = 'https://zeus.accurate.id/accurate/api/receive-item/detail.do?' . $data_ri_receive_detail;

		$result_ri_receive_detail = curl_get($url_detail_ri);

		if ($result_ri_receive_detail['s'] == true) {
			$data_receive = array(
				'id' => $id_accurate2,
			);

			// DELETE DO Existing Accurate
			$url_delete_ri = 'https://zeus.accurate.id/accurate/api/receive-item/delete.do';

			$result_ri_receive = curl_delete($url_delete_ri, json_encode($data_receive));

			if ($result_ri_receive['s'] == true) {

				$get_po_supplier = "SELECT * FROM new_pro_inventory_vendor_po WHERE id_master = '" . $rowget['id_po_supplier'] . "'";
				$res_po = $con->getRecord($get_po_supplier);

				$id_accurate_po = $res_po['id_accurate'];
				$data_po = http_build_query([
					'id' => $id_accurate_po,
				]);

				//GET detail PO Accurate
				$url_ponya = 'https://zeus.accurate.id/accurate/api/purchase-order/detail.do?' . $data_po;

				$result_po = curl_get($url_ponya);

				if ($result_po['s'] == true) {

					//Save RI ke Accurate
					$urlnya = 'https://zeus.accurate.id/accurate/api/receive-item/save.do';
					// Data yang akan dikirim dalam format JSON
					$data = array(
						"receiveNumber" => $no_terima,
						"transDate" => $tgl_terima,
						"vendorNo" => $result_po['d']['vendor']['vendorNo'],
						"description" => "Terima barang dari PO " . $res_po['nomor_po'],
						"toAddress" => $result_po['d']['toAddress'],
						'branchName'  => $rowget_cabang['nama_cabang'] == 'Kantor Pusat' ? 'Head Office' : $rowget_cabang['nama_cabang'],
						'detailItem' => array([
							'itemNo'       => paramDecrypt($kode_item_accurate),
							'quantity'     => $volume_terima,
							'unitPrice'    => paramDecrypt($unit_price),
							'purchaseOrderNumber' => paramDecrypt($nomor_po),
							"warehouseName" => paramDecrypt($gudang),
						])
					);

					// Mengonversi data menjadi format JSON
					$jsonData = json_encode($data);

					$result = curl_post($urlnya, $jsonData);

					if ($result['s'] == true) {
						$update = "UPDATE new_pro_inventory_vendor_po_receive set id_accurate = '" . $result['r']['id'] . "', no_terima = '" . $no_terima . "' WHERE id_po_receive = " . $idnya02;
						$con->setQuery($update);

						$con->commit();
						$con->close();

						if ($isUpload) {
							$pathnya = $public_base_directory . '/files/uploaded_user/lampiran';
							if (count($res01) > 0) {
								foreach ($res01 as $data) {
									if ($data['file_upload'] && file_exists($pathnya . '/' . $data['file_upload'])) unlink($pathnya . '/' . $data['file_upload']);
								}
							}

							if (!file_exists($pathnya . '/' . $folder . '/')) mkdir($pathnya . '/' . $folder, 0777);
							$tujuan  = $pathnya . '/' . $fileName;
							$mantab  = move_uploaded_file($tempPhoto1, $tujuan);
						}

						header("location: " . BASE_URL_CLIENT . "/vendor-po-new-terima.php?" . paramEncrypt("idr=" . $idnya01));
						exit();
					} else {
						$con->rollBack();
						$con->clearError();
						$con->close();
						$flash->add("error", $result["d"][0] . " - Response dari Accurate", BASE_REFERER);
					}
				} else {
					$con->rollBack();
					$con->clearError();
					$con->close();
					$flash->add("error", "PO Supplier pada accurate tidak ada", BASE_REFERER);
				}
			} else {
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", $result_ri_receive["d"][0] . " - Response dari Accurate", BASE_REFERER);
			}
		} else {
			//APabila bellum ada RI di Accurate
			$get_po_supplier = "SELECT * FROM new_pro_inventory_vendor_po WHERE id_master = '" . $rowget['id_po_supplier'] . "'";
			$res_po = $con->getRecord($get_po_supplier);

			$id_accurate_po = $res_po['id_accurate'];
			$data_po = http_build_query([
				'id' => $id_accurate_po,
			]);

			$url_ponya = 'https://zeus.accurate.id/accurate/api/purchase-order/detail.do?' . $data_po;

			$result_po = curl_get($url_ponya);

			if ($result_po['s'] == true) {


				$urlnya = 'https://zeus.accurate.id/accurate/api/receive-item/save.do';
				// Data yang akan dikirim dalam format JSON
				$data = array(
					"receiveNumber" => $no_terima,
					"transDate" => $tgl_terima,
					"vendorNo" => $result_po['d']['vendor']['vendorNo'],
					"description" => "Terima barang dari PO " . $res_po['nomor_po'],
					"toAddress" => $result_po['d']['toAddress'],
					'branchName'  => $rowget_cabang['nama_cabang'] == 'Kantor Pusat' ? 'Head Office' : $rowget_cabang['nama_cabang'],
					'detailItem' => array([
						'itemNo'       => paramDecrypt($kode_item_accurate),
						'quantity'     => $volume_terima,
						'unitPrice'    => paramDecrypt($unit_price),
						'purchaseOrderNumber' => paramDecrypt($nomor_po),
						"warehouseName" => paramDecrypt($gudang),
					])
				);

				$jsonData = json_encode($data);
				$result = curl_post($urlnya, $jsonData);

				if ($result['s'] == true) {
					$update = "UPDATE new_pro_inventory_vendor_po_receive set id_accurate = '" . $result['r']['id'] . "', no_terima = '" . $no_terima . "' WHERE id_po_receive = " . $idnya02;
					$con->setQuery($update);

					$con->commit();
					$con->close();

					if ($isUpload) {
						$pathnya = $public_base_directory . '/files/uploaded_user/lampiran';
						if (count($res01) > 0) {
							foreach ($res01 as $data) {
								if ($data['file_upload'] && file_exists($pathnya . '/' . $data['file_upload'])) unlink($pathnya . '/' . $data['file_upload']);
							}
						}

						if (!file_exists($pathnya . '/' . $folder . '/')) mkdir($pathnya . '/' . $folder, 0777);
						$tujuan  = $pathnya . '/' . $fileName;
						$mantab  = move_uploaded_file($tempPhoto1, $tujuan);
					}

					header("location: " . BASE_URL_CLIENT . "/vendor-po-new-terima.php?" . paramEncrypt("idr=" . $idnya01));
					exit();
				} else {
					$con->rollBack();
					$con->clearError();
					$con->close();
					$flash->add("error", $result["d"][0] . " - Response dari Accurate", BASE_REFERER);
				}
			} else {
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", "PO Supplier pada accurate tidak ada", BASE_REFERER);
			}
		}
		// $con->commit();
		// $con->close();

		// if ($isUpload) {
		// 	$pathnya = $public_base_directory . '/files/uploaded_user/lampiran';
		// 	if (count($res01) > 0) {
		// 		foreach ($res01 as $data) {
		// 			if ($data['file_upload'] && file_exists($pathnya . '/' . $data['file_upload'])) unlink($pathnya . '/' . $data['file_upload']);
		// 		}
		// 	}

		// 	if (!file_exists($pathnya . '/' . $folder . '/')) mkdir($pathnya . '/' . $folder, 0777);
		// 	$tujuan  = $pathnya . '/' . $fileName;
		// 	$mantab  = move_uploaded_file($tempPhoto1, $tujuan);
		// }

		// header("location: " . BASE_URL_CLIENT . "/vendor-po-new-terima.php?" . paramEncrypt("idr=" . $idnya01));
		// exit();
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", 'Maaf Data Gagal Disimpan...', BASE_REFERER);
	}
} else if ($act == 'hapus') {
	$param 	= htmlspecialchars(paramDecrypt($_POST["param"]), ENT_QUOTES);
	$post 	= explode("|#|", $param);
	$id1	= isset($post[0]) ? htmlspecialchars($post[0], ENT_QUOTES) : null;
	$id2	= isset($post[1]) ? htmlspecialchars($post[1], ENT_QUOTES) : null;

	$cek01 = "
			select a.file_upload, a.file_upload_ori from new_pro_inventory_vendor_po_receive a 
			where a.id_po_supplier = '" . $id1 . "' and a.id_po_receive = '" . $id2 . "'
		";
	$res01 = $con->getResult($cek01);

	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	$sql01 = "delete from new_pro_inventory_vendor_po_receive where id_po_supplier = '" . $id1 . "' and id_po_receive = '" . $id2 . "'";
	$con->setQuery($sql01);
	$oke  = $oke && !$con->hasError();

	$sql02 = "delete from new_pro_inventory_depot where id_po_supplier = '" . $id1 . "' and id_po_receive = '" . $id2 . "'";
	$con->setQuery($sql02);
	$oke  = $oke && !$con->hasError();

	if ($oke) {
		$con->commit();
		$con->close();
		$arr["error"] = "";

		if (count($res01) > 0) {
			foreach ($res01 as $data) {
				$pathnya = $public_base_directory . '/files/uploaded_user/lampiran';
				if ($data['file_upload'] && file_exists($pathnya . '/' . $data['file_upload'])) unlink($pathnya . '/' . $data['file_upload']);
			}
		}
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$arr["error"] = "Maaf, data tidak dapat dihapus";
	}

	echo json_encode($arr);
	exit;
}

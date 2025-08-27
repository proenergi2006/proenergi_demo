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

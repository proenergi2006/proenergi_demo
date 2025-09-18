<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "htmlawed");

$sesid 	= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);
$seswil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$sesgroup = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);
$sesrole = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$user_pic = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']);

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;
$enk  	= decode($_SERVER['REQUEST_URI']);
$act	= ($enk['act'] == "") ? htmlspecialchars($_POST["act"], ENT_QUOTES) : $enk['act'];
$id_cust 	= paramDecrypt(htmlspecialchars($_POST["id_cust"], ENT_QUOTES));
$id_poc 	= paramDecrypt(htmlspecialchars($_POST["idk"], ENT_QUOTES));

$nomor_poc	= htmlspecialchars($_POST["nomor_poc"], ENT_QUOTES);
$cl_temp	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["cl_temp"]), ENT_QUOTES);
// echo $cl_temp;
// exit();
$top_temp	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["top_temp"]), ENT_QUOTES);
$keterangan	= htmlspecialchars($_POST["keterangan"], ENT_QUOTES);
$user_pic	= paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']);
$user_ip	= $_SERVER['REMOTE_ADDR'];

$max_size	= 2 * 1024 * 1024;
$allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'];
$pathfile	= $public_base_directory . '/files/uploaded_user/file_unblock';

// echo $pathfile;
// exit();

$url = BASE_URL_CLIENT . "/form-unblock-marketing.php";

$oke = true;
$con->beginTransaction();
$con->clearError();

$prefix = "FRM-UNBLOCK/PE";
$tahun  = date('Y');

$sql = "SELECT nomor_dokumen FROM pro_unblock_customer WHERE nomor_dokumen LIKE 'FRM-UNBLOCK/PE/" . $tahun . "/%' ORDER BY nomor_dokumen DESC LIMIT 1";
$row = $con->getRecord($sql);

if ($row) {
	$parts = explode('/', $row['nomor_dokumen']);
	if (count($parts) === 4) {
		$lastNumber = (int)$parts[3];
	}
}

$nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
$no_dokumen = "$prefix/$tahun/$nextNumber";

// echo $no_dokumen;
// exit();

if ($keterangan == "") {
	$con->rollBack();
	$con->clearError();
	$con->close();
	$flash->add("error", "Keterangan tidak boleh kosong", BASE_REFERER);
}

$sqlAktif = "SELECT disposisi, nomor_dokumen FROM pro_unblock_customer WHERE id_customer = '" . $id_cust . "' AND disposisi IN (0,1)";
$rowAktif = $con->getRecord($sqlAktif);

if ($rowAktif) {
	$con->rollBack();
	$con->clearError();
	$con->close();
	$flash->add("error", "Masih terdapat data unblock yang belum terbayar lunas, tidak bisa membuat yang baru", BASE_REFERER);
}

$insert_unblock = "INSERT into pro_unblock_customer(id_poc, nomor_dokumen, id_customer, cl_temp, top_temp, keterangan, total_po, created_by, ip_user, date_created) values ('" . $id_poc . "', '" . $no_dokumen . "', '" . $id_cust . "', " . $cl_temp . ", " . $top_temp . ", '" . $keterangan . "', " . $cl_temp . ", '" . $user_pic . "', '" . $user_ip . "', NOW())";
$id_unblock = $con->setQuery($insert_unblock);
$oke = $oke && !$con->hasError();

if ($oke) {
	$upload = true;
	if (!empty($_FILES['lampiran']['name'][0])) {
		foreach ($_FILES['lampiran']['name'] as $index => $originalName) {
			$tmpName = $_FILES['lampiran']['tmp_name'][$index];
			$error = $_FILES['lampiran']['error'][$index];
			$size = $_FILES['lampiran']['size'][$index];
			$ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

			// Error upload
			if ($error !== UPLOAD_ERR_OK) {
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", "Gagal upload file: $originalName (Error code: $error)<br>", BASE_REFERER);
			}

			// Validasi ekstensi
			if (!in_array($ext, $allowedTypes)) {
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", "Tipe file tidak diperbolehkan: $originalName<br>", BASE_REFERER);
			}

			// Validasi ukuran file
			if ($size > $max_size) {
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", "Ukuran file melebihi 2MB: $originalName<br>", BASE_REFERER);
			}

			$newFileName = "Lampiran-unblock-" . date("Ymd-His") . "-" . uniqid() . "." . $ext;
			$destination = $pathfile . '/' . $newFileName;

			if (move_uploaded_file($tmpName, $destination)) {
				$insert_file = "INSERT INTO pro_lampiran_unblock (id_unblock, nama_file_ori, nama_file) values ('" . $id_unblock . "', '" . $originalName . "', '" . $newFileName . "')";
				$con->setQuery($insert_file);
				$upload = $upload && !$con->hasError();
			} else {
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", "Gagal memindahkan file: $originalName<br>", BASE_REFERER);
			}
		}
	}
	if ($upload) {
		$con->commit();
		$con->close();
		header("location: " . $url);
		exit();
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "Gagal Insert data File", BASE_REFERER);
	}
} else {
	$con->rollBack();
	$con->clearError();
	$con->close();
	$flash->add("error", "Gagal Insert data Form Unblock", BASE_REFERER);
}

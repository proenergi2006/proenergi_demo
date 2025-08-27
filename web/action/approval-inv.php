<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
require_once($public_base_directory . "/libraries/helper/passwordHash.php");
load_helper("autoload", "mailgen");
// load_helper("autoload");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;
$enk  	= decode($_SERVER['REQUEST_URI']);
$act 	= (isset($enk['act']) and $enk['act'] !== "") ? $enk["act"] : $_POST["act"];

if ($act == "update") {
	$idr 		= htmlspecialchars($_POST["idr"], ENT_QUOTES);
	$nama		= htmlspecialchars($_POST["nama"], ENT_QUOTES);
	$jabatan 	= htmlspecialchars($_POST["jabatan"], ENT_QUOTES);
	$cabang 	= isset($_POST["cabang"]) ? htmlspecialchars($_POST["cabang"], ENT_QUOTES) : null;
	$active 	= htmlspecialchars($_POST["active"], ENT_QUOTES);

	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	$sqlcek 	= "SELECT * FROM pro_master_approval_invoice WHERE id_master='" . $idr . "'";
	$rsm 		= $con->getRecord($sqlcek);

	if ($rsm['cabang'] == $cabang) {
		$sqlcek2 	= "SELECT * FROM pro_master_approval_invoice WHERE cabang = '" . $cabang . "' AND is_active = '1'";
		$rsm2 		= $con->getRecord($sqlcek2);

		if ($idr == $rsm2['id_master']) {
			$sql1 = "UPDATE pro_master_approval_invoice set nama = '" . $nama . "', jabatan = '" . $jabatan . "', cabang = '" . $cabang . "', is_active = '" . $active . "', updated_at = '" . date("Y-m-d H:i:s") . "' WHERE id_master = '" . $idr . "'";
			$res1 = $con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();
		} else {
			$con->close();
			$flash->add("error", "Approval pada cabang tersebut sudah ada, silahkan nonaktifkan approval sebelumnya terlebih dahulu", BASE_REFERER);
		}
	} else {
		$sqlcek2 	= "SELECT * FROM pro_master_approval_invoice WHERE cabang = '" . $cabang . "' AND is_active = '1'";
		$rsm2 		= $con->getRecord($sqlcek2);

		if ($rsm2 != "") {
			$con->close();
			$flash->add("error", "Approval pada cabang tersebut sudah ada, silahkan nonaktifkan approval sebelumnya terlebih dahulu", BASE_REFERER);
		} else {
			$sql1 = "UPDATE pro_master_approval_invoice set nama = '" . $nama . "', jabatan = '" . $jabatan . "', cabang = '" . $cabang . "', is_active = '" . $active . "', updated_at = '" . date("Y-m-d H:i:s") . "' WHERE id_master = '" . $idr . "'";
			$res1 = $con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();
		}
	}

	if ($oke) {
		$con->commit();
		$con->close();
		$flash->add("success", "Data berhasil di update", BASE_REFERER);
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}
} else if ($act == "add") {
	$nama		= htmlspecialchars($_POST["nama"], ENT_QUOTES);
	$jabatan 	= htmlspecialchars($_POST["jabatan"], ENT_QUOTES);
	$cabang 	= isset($_POST["cabang"]) ? htmlspecialchars($_POST["cabang"], ENT_QUOTES) : null;
	$active 	= htmlspecialchars($_POST["active"], ENT_QUOTES);

	$sqlcek 	= "SELECT * FROM pro_master_approval_invoice WHERE cabang = '" . $cabang . "' AND is_active = '1'";
	$rsm 		= $con->getRecord($sqlcek);

	if ($nama == "" || $jabatan == "" || $cabang == "") {
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else if ($rsm != "") {
		$con->close();
		$flash->add("error", "Approval pada cabang tersebut sudah ada, silahkan nonaktifkan approval sebelumnya terlebih dahulu", BASE_REFERER);
	} else {
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		$sql1 = '
				insert into 
					pro_master_approval_invoice
				(
					nama,
					jabatan,
					cabang,
					is_active,
					created_at,
					updated_at
				) values (
					"' . $nama . '",
					"' . $jabatan . '",
					"' . $cabang . '",
					"' . $active . '",
					"' . date('Y-m-d H:i:s') . '",
					"' . date('Y-m-d H:i:s') . '"
				)';

		$res1 = $con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();
		if ($oke) {
			$con->commit();
			$con->close();
			$flash->add("success", "Data berhasil di simpan", BASE_URL_CLIENT . "/approval-inv.php");
		} else {
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
	}
}

<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
require_once($public_base_directory . "/libraries/helper/passwordHash.php");
load_helper("autoload", "mailgen", "htmlawed");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;
$enk  	= decode($_SERVER['REQUEST_URI']);
$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
$lama 	= htmlspecialchars($_POST["id_lama"], ENT_QUOTES);
$market = htmlspecialchars($_POST["market"], ENT_QUOTES);
$reason = htmlspecialchars($_POST["reason"], ENT_QUOTES);
$all 	= htmlspecialchars((isset($_POST["select_all"]) ? '1' : '0'), ENT_QUOTES);

$oke = true;
$con->beginTransaction();
$con->clearError();

if ($market == "") {
	$con->close();
	$flash->add("error", "KOSONG", BASE_REFERER);
} else {
	if ($all == 1) {
		$sql = "SELECT * from pro_customer where id_marketing='" . $lama . "' AND kode_pelanggan IS NOT NULL";
		$result = $con->getResult($sql);
		// echo json_encode($result);
		// exit();
		foreach ($result as $key => $value) {
			$sql_get = "SELECT * from pro_customer_marketing_history where id_customer='" . $value['id_customer'] . "'";
			$res = $con->getRecord($sql_get);

			if ($res) {
				$sqlClose = "UPDATE pro_customer_marketing_history SET effective_to = NOW() WHERE id_customer = '" . $value['id_customer'] . "' AND effective_to IS NULL";
				$con->setQuery($sqlClose);
				$oke = $oke && !$con->hasError();

				// INSERT ke history jika sebelumnya sudah ada data history
				$sql2 = "INSERT INTO pro_customer_marketing_history (id_customer, id_marketing, effective_from, effective_to, mutasi_at, mutasi_by, reason) VALUES (
				'" . $value['id_customer'] . "',
				'" . $market . "',
				NOW(),
				NULL,
				NOW(),
				'" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "',
				'" . $reason . "'
				)";
				$con->setQuery($sql2);
				$oke = $oke && !$con->hasError();
			} else {
				// INSERT ke history pertama kali
				$sql2 = "INSERT INTO pro_customer_marketing_history (id_customer, id_marketing, effective_from, effective_to, mutasi_at, mutasi_by, reason) VALUES (
				'" . $value['id_customer'] . "',
				'" . $market . "',
				NOW(),
				NULL,
				NOW(),
				'" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "',
				'" . $reason . "'
				)";

				$con->setQuery($sql2);
				$oke = $oke && !$con->hasError();
			}

			$sql = "UPDATE pro_customer set id_marketing = '" . $market . "' where id_customer = '" . $value['id_customer'] . "'";
			$con->setQuery($sql);
		}
	} else {
		$sql_get = "SELECT * from pro_customer_marketing_history where id_customer='" . $idr . "'";
		$res = $con->getRecord($sql_get);

		if ($res) {
			$sqlClose = "UPDATE pro_customer_marketing_history SET effective_to = NOW() WHERE id_customer = '" . $idr . "' AND effective_to IS NULL";
			$con->setQuery($sqlClose);
			$oke = $oke && !$con->hasError();

			// INSERT ke history jika sebelumnya sudah ada data history
			$sql2 = "INSERT INTO pro_customer_marketing_history (id_customer, id_marketing, effective_from, effective_to, mutasi_at, mutasi_by, reason) VALUES (
              '" . $idr . "',
              '" . $market . "',
              NOW(),
              NULL,
              NOW(),
              '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "',
              '" . $reason . "'
          	)";
			$con->setQuery($sql2);
			$oke = $oke && !$con->hasError();

			$sql = "UPDATE pro_customer set id_marketing = '" . $market . "' where id_customer = '" . $idr . "'";
			$con->setQuery($sql);
			$oke = $oke && !$con->hasError();
		} else {
			// INSERT ke history pertama kali
			$sql2 = "INSERT INTO pro_customer_marketing_history (id_customer, id_marketing, effective_from, effective_to, mutasi_at, mutasi_by, reason) VALUES (
              '" . $idr . "',
              '" . $market . "',
              NOW(),
              NULL,
              NOW(),
              '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "',
              '" . $reason . "'
          	)";

			$con->setQuery($sql2);
			$oke = $oke && !$con->hasError();

			$sql = "UPDATE pro_customer set id_marketing = '" . $market . "' where id_customer = '" . $idr . "'";
			$con->setQuery($sql);
			$oke = $oke && !$con->hasError();
		}
	}
	if ($oke) {
		$con->commit();
		$con->close();
		$flash->add("success", "Marketing Customer telah berhasil diubah", BASE_URL_CLIENT . "/customer-admin-detail.php?" . paramEncrypt("idr=" . $idr));
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}
}

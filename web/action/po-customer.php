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

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;
$enk  	= decode($_SERVER['REQUEST_URI']);
$act	= ($enk['act'] == "") ? htmlspecialchars($_POST["act"], ENT_QUOTES) : $enk['act'];
$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
$idk 	= htmlspecialchars($_POST["idk"], ENT_QUOTES);

$total_inv	= htmlspecialchars($_POST["total_inv"], ENT_QUOTES);
$nomor_po_cust	= htmlspecialchars($_POST["nomor_po_cust"], ENT_QUOTES);
$customer		= htmlspecialchars($_POST["customer"], ENT_QUOTES);
$penawaran		= htmlspecialchars($_POST["penawaran"], ENT_QUOTES);
$tanggal_po		= htmlspecialchars($_POST["tanggal_po"], ENT_QUOTES);
$supply_date	= htmlspecialchars($_POST["supply_date"], ENT_QUOTES);
$nomor_po		= htmlspecialchars($_POST["nomor_po"], ENT_QUOTES);
$top			= htmlspecialchars($_POST["top"], ENT_QUOTES);
$produk			= htmlspecialchars($_POST["produk"], ENT_QUOTES);
$penerima_refund = $_POST["penerima_refund"];
$terima_refund = $_POST["terima_refund"];
$harga_liter	= htmlspecialchars(str_replace(array(","), array(""), $_POST["harga_liter"]), ENT_QUOTES);
$total_volume	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["total_volume"]), ENT_QUOTES);
$top			= ($top ? $top : '0');

$closepo	= ($enk['closepo'] == "") ? htmlspecialchars($_POST["closepo"], ENT_QUOTES) : $enk['closepo'];
$tanggal_close	= ($enk['tanggal_close'] == "") ? htmlspecialchars($_POST["tanggal_close"], ENT_QUOTES) : $enk['tanggal_close'];
$volume_close	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["volume_close"]), ENT_QUOTES);
$catatan_close	= ($enk['catatan_close'] == "") ? htmlspecialchars($_POST["catatan_close"], ENT_QUOTES) : $enk['catatan_close'];

$filePhoto 	= htmlspecialchars($_FILES['attachment_order']['name'], ENT_QUOTES);
$sizePhoto 	= htmlspecialchars($_FILES['attachment_order']['size'], ENT_QUOTES);
$tempPhoto 	= htmlspecialchars($_FILES['attachment_order']['tmp_name'], ENT_QUOTES);
$extPhoto 	= substr($filePhoto, strrpos($filePhoto, '.'));
$max_size	= 2 * 1024 * 1024;
$allow_type	= array(".jpg", ".jpeg", ".JPG", ".png", ".pdf", ".rar", ".zip");
$pathfile	= $public_base_directory . '/files/uploaded_user/lampiran';
$user_pic	= paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']);
$user_ip	= $_SERVER['REMOTE_ADDR'];


if ($act == 'update_no_po' && $nomor_po_cust != "") {
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	if ($total_inv > 0) {
		$data = [
			"status" => false,
			"pesan"  => "Tidak dapat menyimpan data karena sudah terbentuk invoice",
		];
		echo json_encode($data);
		$con->close();
	} else {

		$sql = "update pro_po_customer set nomor_poc = '" . $nomor_po_cust . "', is_edit = 1 where id_customer = '" . $idr . "' and id_poc = '" . $idk . "'";
		$con->setQuery($sql);
		$oke  = $oke && !$con->hasError();
		$url = BASE_URL_CLIENT . "/po-customer.php";

		if ($oke) {

			$con->commit();
			$con->close();
			header("location: " . $url);
			exit();
		} else {
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", $msg, BASE_REFERER);
		}
	}
} else if ($act != 'update_no_po' && $total_volume == "" || $harga_liter == "" || $nomor_po == "" || $tanggal_po == "" || $supply_date == "" || $penawaran == "" || $customer == "" || $produk == "") {
	$con->close();
	$flash->add("error", "KOSONG", BASE_REFERER);
} else if ($filePhoto != "" && $sizePhoto > $max_size) {
	$con->close();
	$flash->add("error", "Ukuran file terlalu besar, melebihi 2MB...", BASE_REFERER);
} else if ($filePhoto != "" && !in_array($extPhoto, $allow_type)) {
	$con->close();
	$flash->add("error", "Tipe file tidak diperbolehkan...", BASE_REFERER);
} else {
	$oke = true;
	$con->beginTransaction();
	$con->clearError();
	$msg = "GAGAL_UBAH";
	$po_status = "Terdaftar";
	if ($act == "add") {
		$cek_reserved_cl = "select * from pro_customer where id_customer = '" . $customer . "'";
		$row_res = $con->getRecord($cek_reserved_cl);

		$credit_limit_reserved = (float)$row_res['credit_limit_reserved'];
		$credit_limit_used = (float)$row_res['credit_limit_used'];
		$credit_limit = (float)$row_res['credit_limit'];

		$sisa_credit_limit = $credit_limit - $credit_limit_used - $credit_limit_reserved;

		$total_order = $harga_liter * $total_volume;

		if ($filePhoto != "") {
			$upl = true;
			$sql = "insert ignore into pro_po_customer(id_customer, id_penawaran, top_poc, nomor_poc, tanggal_poc, supply_date, harga_poc, volume_poc, produk_poc, lampiran_poc_ori, created_time, 
						created_ip, created_by) values ('" . $customer . "', '" . $penawaran . "', '" . $top . "', '" . $nomor_po . "', '" . tgl_db($tanggal_po) . "', '" . tgl_db($supply_date) . "', '" . $harga_liter . "', 
						'" . $total_volume . "', '" . $produk . "', '" . sanitize_filename($filePhoto) . "', NOW(), '" . $user_ip . "', '" . $user_pic . "')";
			$idk = $con->setQuery($sql);
			$oke  = $oke && !$con->hasError();

			$nqu = 'POC_' . $idk . '_' . sanitize_filename($filePhoto);
			$que = "update pro_po_customer set lampiran_poc = '" . $nqu . "' where id_poc = '" . $idk . "'";
			$con->setQuery($que);
			$oke = $oke && !$con->hasError();

			if ($oke) {
				if ($penerima_refund != "") {
					foreach ($penerima_refund as $i => $key) {
						$terima = htmlspecialchars(str_replace(array(".", ","), array("", ""), $terima_refund[$i]), ENT_QUOTES);
						if ($key != "") {
							$sql_penerima_refund = "insert into pro_poc_penerima_refund(id_poc, penerima_refund, persentasi_refund, created_at) values ('" . $idk . "', '" . $key . "', '" . $terima . "', NOW())";
							$con->setQuery($sql_penerima_refund);
							$oke  = $oke && !$con->hasError();
						}
					}
				}
			}
		} else {
			$upl = false;
			$nqu = '';
			$sql = "insert ignore into pro_po_customer(id_customer, id_penawaran, top_poc, nomor_poc, tanggal_poc, supply_date, harga_poc, volume_poc, produk_poc, created_time, 
						created_ip, created_by) values ('" . $customer . "', '" . $penawaran . "', '" . $top . "', '" . $nomor_po . "', '" . tgl_db($tanggal_po) . "', '" . tgl_db($supply_date) . "', '" . $harga_liter . "',
						'" . $total_volume . "', '" . $produk . "', NOW(), '" . $user_ip . "', '" . $user_pic . "')";
			$idk = $con->setQuery($sql);
			$oke  = $oke && !$con->hasError();

			if ($oke) {
				if ($penerima_refund != "") {
					foreach ($penerima_refund as $i => $key) {
						$terima = htmlspecialchars(str_replace(array(".", ","), array("", ""), $terima_refund[$i]), ENT_QUOTES);
						if ($key != "") {
							$sql_penerima_refund = "insert into pro_poc_penerima_refund(id_poc, penerima_refund, persentasi_refund, created_at) values ('" . $idk . "', '" . $key . "', '" . $terima . "', NOW())";
							$con->setQuery($sql_penerima_refund);
							$oke  = $oke && !$con->hasError();
						}
					}
				}
			}
		}

		$history_ar_customer = "INSERT into pro_history_ar_customer(id_poc, kategori, keterangan, nominal, created_time, created_by) values ('" . $idk . "', '1', 'Generate PO Customer', -$total_order, NOW(), '" . $user_pic . "')";
		$con->setQuery($history_ar_customer);
		$oke = $oke && !$con->hasError();

		$sql = "UPDATE pro_customer SET credit_limit_reserved = credit_limit_reserved + $total_order  WHERE id_customer = '" . $customer . "'";
		$con->setQuery($sql);
		$oke  = $oke && !$con->hasError();

		if ($sisa_credit_limit < $total_order) {
			$po_status = "draft";

			$update_po = "UPDATE pro_po_customer SET is_draft = 1 WHERE id_poc = '" . $idk . "'";
			$con->setQuery($update_po);
			$oke  = $oke && !$con->hasError();
		} else {
			$po_status = "terdaftar";
		}

		$url = BASE_URL_CLIENT . "/po-customer-detail.php?" . paramEncrypt("idr=" . $customer . "&idk=" . $idk);
	} else if ($act == 'update') {
		if ($closepo == '1') {
			if ($filePhoto != "") {
				$upl = true;
				$nqu = 'POC_' . $idk . '_' . sanitize_filename($filePhoto);
				$nqu_ori = sanitize_filename($filePhoto);
			} else {
				$cekClosePo = "SELECT 
				                    lampiran_close_po,
				                    lampiran_close_po_ori
				                FROM pro_po_customer_close
				                WHERE ST_AKTIF='Y'
				                AND ID_POC='" . $idk . "'";
				$rowClosePo = $con->getRecord($cekClosePo);

				$nqu = $rowClosePo['lampiran_close_po'];
				$nqu_ori = $rowClosePo['lampiran_close_po_ori'];
			}

			$sql = "UPDATE pro_po_customer_close
						SET
						st_aktif = 'T'
						WHERE id_poc = '" . $idk . "'
						and st_aktif = 'Y'";

			$con->setQuery($sql);
			$oke  = $oke && !$con->hasError();

			$total_order = $volume_close * $harga_liter;
			$sql = "UPDATE pro_customer SET credit_limit_reserved = credit_limit_reserved - $total_order WHERE id_customer = '" . $customer . "'";

			$con->setQuery($sql);
			$oke  = $oke && !$con->hasError();

			$history_ar_customer = "INSERT into pro_history_ar_customer(id_poc, kategori, keterangan, nominal, created_time, created_by) values ('" . $idk . "', '5', 'Close PO Customer', $total_order, NOW(), '" . $user_pic . "')";
			$con->setQuery($history_ar_customer);
			$oke = $oke && !$con->hasError();

			$sql = "INSERT ignore INTO pro_po_customer_close
								(id_poc,
								tgl_close,
								volume_close,
								realisasi_close,
								created_time,
								created_ip,
								created_by,
								id_user,
								id_role,
								keterangan,
								st_aktif,
								lampiran_close_po,
								lampiran_close_po_ori)
								VALUES
								('" . $idk . "',
								'" . tgl_db($tanggal_close) . "',
								" . $volume_close . ",
								" . $volume_close . ",
								NOW(), 
								'" . $user_ip . "', 
								'" . $user_pic . "',
								'" . $sesid . "',
								'" . $sesrole . "',
								'" . $catatan_close . "',
								'Y',
								'" . $nqu . "',
								'" . $nqu_ori . "')";
			// echo $sql;
			// die;

			$con->setQuery($sql);
			$oke  = $oke && !$con->hasError();

			if ($oke) {
				$msg = "SUKSES_MASUK";
				$sql_delete = "DELETE FROM pro_poc_penerima_refund WHERE id_poc = '" . $idk . "'";
				$con->setQuery($sql_delete);
			} else {
				$msg = "GAGAL_MASUK";
			}
			$url = BASE_URL_CLIENT . "/po-customer-detail.php?" . paramEncrypt("idr=" . $idr . "&idk=" . $idk);
		} else {
			$sql_prev = "SELECT volume_poc FROM pro_po_customer WHERE id_customer = '" . $idr . "' AND id_poc = '" . $idk . "'";
			$row_prev = $con->getRecord($sql_prev);

			$volume_lama = (float)$row_prev['volume_poc'];
			$total_order_lama = $volume_lama * $harga_liter;

			$total_order = $total_volume * $harga_liter;

			// Jika ada perubahan nilai total order, lakukan validasi credit limit
			if ($total_volume != $volume_lama) {

				$cek_reserved_cl = "SELECT * FROM pro_customer WHERE id_customer = '" . $idr . "'";
				$row_res = $con->getRecord($cek_reserved_cl);

				$credit_limit_reserved = (float)$row_res['credit_limit_reserved'];
				$credit_limit_used = (float)$row_res['credit_limit_used'];
				$credit_limit = (float)$row_res['credit_limit'];

				$sisa_credit_limit = $credit_limit - $credit_limit_used - $credit_limit_reserved + $total_order_lama;

				if ($sisa_credit_limit < $total_order) {
					$oke = false;
					$msg = "Credit Limit tidak mencukupi untuk perubahan volume";
				}

				$sql = "UPDATE pro_customer SET credit_limit_reserved = (credit_limit_reserved - $total_order_lama) + $total_order  WHERE id_customer = '" . $idr . "'";

				$con->setQuery($sql);
				$oke  = $oke && !$con->hasError();

				$sql_history = "UPDATE pro_history_ar_customer SET nominal = -$total_order WHERE id_poc = '" . $idk . "' and kategori = 1";

				$con->setQuery($sql_history);
				$oke  = $oke && !$con->hasError();
			}

			if ($filePhoto != "") {
				$upl = true;
				$nqu = 'POC_' . $idk . '_' . sanitize_filename($filePhoto);
				$sql = "update pro_po_customer set nomor_poc = '" . $nomor_po . "', tanggal_poc = '" . tgl_db($tanggal_po) . "', supply_date = '" . tgl_db($supply_date) . "', volume_poc = '" . $total_volume . "', harga_poc = '" . $harga_liter . "',
							lampiran_poc = '" . $nqu . "', lampiran_poc_ori = '" . sanitize_filename($filePhoto) . "', lastupdate_time = NOW(), lastupdate_ip = '" . $user_ip . "', 
							lastupdate_by = '" . $user_pic . "' where id_customer = '" . $idr . "' and id_poc = '" . $idk . "'";
				$con->setQuery($sql);
				$oke  = $oke && !$con->hasError();

				if ($oke) {
					if ($penerima_refund != "") {
						$sql_delete = "DELETE FROM pro_poc_penerima_refund WHERE id_poc = '" . $idk . "'";
						$con->setQuery($sql_delete);
						foreach ($penerima_refund as $i => $key) {
							$terima = htmlspecialchars(str_replace(array(".", ","), array("", ""), $terima_refund[$i]), ENT_QUOTES);
							if ($key != "") {
								$sql_penerima_refund = "insert into pro_poc_penerima_refund(id_poc, penerima_refund, persentasi_refund, created_at) values ('" . $idk . "', '" . $key . "', '" . $terima . "', NOW())";
								$con->setQuery($sql_penerima_refund);
								$oke  = $oke && !$con->hasError();
							}
						}
					}
				}
			} else {
				$upl = false;
				$nqu = '';
				$sql = "update pro_po_customer set nomor_poc = '" . $nomor_po . "', tanggal_poc = '" . tgl_db($tanggal_po) . "', supply_date = '" . tgl_db($supply_date) . "', volume_poc = '" . $total_volume . "', harga_poc = '" . $harga_liter . "',
							lastupdate_time = NOW(), lastupdate_ip = '" . $user_ip . "', lastupdate_by = '" . $user_pic . "' where id_customer = '" . $idr . "' and id_poc = '" . $idk . "'";
				$con->setQuery($sql);
				$oke  = $oke && !$con->hasError();

				if ($oke) {
					if ($penerima_refund != "") {
						$sql_delete = "DELETE FROM pro_poc_penerima_refund WHERE id_poc = '" . $idk . "'";
						$con->setQuery($sql_delete);
						foreach ($penerima_refund as $i => $key) {
							$terima = htmlspecialchars(str_replace(array(".", ","), array("", ""), $terima_refund[$i]), ENT_QUOTES);
							if ($key != "") {
								$sql_penerima_refund = "insert into pro_poc_penerima_refund(id_poc, penerima_refund, persentasi_refund, created_at) values ('" . $idk . "', '" . $key . "', '" . $terima . "', NOW())";
								$con->setQuery($sql_penerima_refund);
								$oke  = $oke && !$con->hasError();
							}
						}
					}
				}
			}
			$msg = "GAGAL_UBAH";
			$url = BASE_URL_CLIENT . "/po-customer-detail.php?" . paramEncrypt("idr=" . $idr . "&idk=" . $idk);
		}
	}

	if ($oke) {
		$mantab  = true;
		if ($upl) {
			$tmpPot = glob($pathfile . "/POC_" . $idk . "_*.{jpg,jpeg,gif,png,pdf,rar,zip}", GLOB_BRACE);

			if (count($tmpPot) > 0) {
				foreach ($tmpPot as $datj)
					if (file_exists($datj)) unlink($datj);
			}
			$tujuan  = $pathfile . "/" . $nqu;
			$mantab  = $mantab && move_uploaded_file($tempPhoto, $tujuan);
			if (file_exists($tempPhoto)) unlink($tempPhoto);
		}
		if ($mantab) {
			$con->commit();
			$con->close();

			if ($po_status == 'draft') {
				$flash->add("warning", "Credit limit tidak mencukupi, PO akan disimpan sebagai Draft");
			} else {
				$flash->add("success", "Data berhasil disimpan");
			}

			header("location: " . $url);
			exit();

			header("location: " . $url);
			exit();
		} else {
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", $msg, BASE_REFERER);
		}
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", $msg, BASE_REFERER);
	}
}

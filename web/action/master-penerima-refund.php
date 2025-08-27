<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "mailgen");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;
$enk  	= decode($_SERVER['REQUEST_URI']);
$act	= ($enk['act'] == "") ? htmlspecialchars($_POST["act"], ENT_QUOTES) : $enk['act'];
$fullname = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']);
$id_wilayah = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$url_back = BASE_URL_CLIENT . "/master-penerima-refund.php";

$customer	= htmlspecialchars($_POST["customer"], ENT_QUOTES);
$nama_penerima	= htmlspecialchars($_POST["nama_penerima"], ENT_QUOTES);
$divisi	= htmlspecialchars($_POST["divisi"], ENT_QUOTES);
$no_ktp	= htmlspecialchars($_POST["no_ktp"], ENT_QUOTES);
$filePhoto 	= htmlspecialchars($_FILES['foto_ktp']['name'], ENT_QUOTES);
$sizePhoto 	= htmlspecialchars($_FILES['foto_ktp']['size'], ENT_QUOTES);
$tempPhoto 	= htmlspecialchars($_FILES['foto_ktp']['tmp_name'], ENT_QUOTES);
$fileNpwp 	= htmlspecialchars($_FILES['foto_npwp']['name'], ENT_QUOTES);
$sizeNpwp 	= htmlspecialchars($_FILES['foto_npwp']['size'], ENT_QUOTES);
$tempNpwp 	= htmlspecialchars($_FILES['foto_npwp']['tmp_name'], ENT_QUOTES);

$extPhoto 	= substr($filePhoto, strrpos($filePhoto, '.'));
$max_size	= 1 * 1024 * 1024;
$pathfile	= $public_base_directory . '/files/uploaded_user/ktp_penerima_refund';

$extNpwp 	= substr($fileNpwp, strrpos($fileNpwp, '.'));
$max_size_npwp	= 1 * 1024 * 1024;
$pathfileNpwp	= $public_base_directory . '/files/uploaded_user/npwp_penerima_refund';

$bank	= htmlspecialchars($_POST["bank"], ENT_QUOTES);
$kode_bank	= htmlspecialchars($_POST["kode_bank"], ENT_QUOTES);
$no_rekening = htmlspecialchars($_POST["no_rekening"], ENT_QUOTES);
$atas_nama	= htmlspecialchars($_POST["atas_nama"], ENT_QUOTES);
$active = htmlspecialchars($_POST["active"], ENT_QUOTES);
$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
$catatan_bm = htmlspecialchars($_POST["catatan_bm"], ENT_QUOTES);
$catatan_ceo = htmlspecialchars($_POST["catatan_ceo"], ENT_QUOTES);
$id_penerima_refund = paramDecrypt(isset($_POST["id_refund"]) ? htmlspecialchars($_POST["id_refund"], ENT_QUOTES) : NULL);

$oke = true;
$con->beginTransaction();
$con->clearError();

if ($act == 'add') {
	$sqlcek = "SELECT * FROM pro_master_penerima_refund WHERE id_customer = '" . $customer . "' AND is_active = '1'";
	$res = $con->getResult($sqlcek);

	if (count($res) == 3) {
		$msg = "Data penerima pada customer tersebut sudah ada 3 yang berstatus aktif";
		$oke = false;
	} elseif ($filePhoto != "" && $sizePhoto > $max_size) {
		$msg = "Ukuran foto KTP terlalu besar, melebihi 1 MB.";
		$oke = false;
	} elseif ($fileNpwp != "" && $sizeNpwp > $max_size_npwp) {
		$msg = "Ukuran foto NPWP terlalu besar, melebihi 1 MB.";
		$oke = false;
	} else {
		$msg = "Data berhasil disimpan";
		$oke = true;

		if ($filePhoto) {
			if ($fileNpwp) {
				$sql = "
				insert into pro_master_penerima_refund(id_customer, nama, divisi, no_ktp, foto_ktp, foto_npwp, bank, kode_bank, no_rekening, atas_nama, is_active, created_at, created_by) 
				values ('" . $customer . "', '" . $nama_penerima . "', '" . $divisi . "' , '" . $no_ktp . "' , '" . sanitize_filename($filePhoto) . "' , '" . sanitize_filename($fileNpwp) . "' , '" . $bank . "', '" . $kode_bank . "', '" . $no_rekening . "', '" . $atas_nama . "', '" . $active . "', '" . date("Y-m-d H:i:s") . "', '" . $fullname . "')";
				$idnya = $con->setQuery($sql);
				$oke = $oke && !$con->hasError();

				$fileUploadName = "foto_ktp_" . $idnya . "_" . sanitize_filename($filePhoto);
				$sql_updatefoto = "update pro_master_penerima_refund set foto_ktp = '" . $fileUploadName . "' where id = " . $idnya;
				$con->setQuery($sql_updatefoto);
				$oke  = $oke && !$con->hasError();

				$fileUploadNameNpwp = "foto_npwp_" . $idnya . "_" . sanitize_filename($fileNpwp);
				$sql_updatefotoNpwp = "update pro_master_penerima_refund set foto_npwp = '" . $fileUploadNameNpwp . "' where id = " . $idnya;
				$con->setQuery($sql_updatefotoNpwp);
				$oke  = $oke && !$con->hasError();
			} else {
				$sql = "
				insert into pro_master_penerima_refund(id_customer, nama, divisi, no_ktp, foto_ktp, bank, kode_bank, no_rekening, atas_nama, is_active, created_at, created_by) 
				values ('" . $customer . "', '" . $nama_penerima . "', '" . $divisi . "' , '" . $no_ktp . "' , '" . sanitize_filename($filePhoto) . "' , '" . $bank . "', '" . $kode_bank . "', '" . $no_rekening . "', '" . $atas_nama . "', '" . $active . "', '" . date("Y-m-d H:i:s") . "', '" . $fullname . "')";
				$idnya = $con->setQuery($sql);
				$oke = $oke && !$con->hasError();

				$fileUploadName = "foto_ktp_" . $idnya . "_" . sanitize_filename($filePhoto);
				$sql_updatefoto = "update pro_master_penerima_refund set foto_ktp = '" . $fileUploadName . "' where id = " . $idnya;
				$con->setQuery($sql_updatefoto);
				$oke  = $oke && !$con->hasError();
			}
		}
	}
	if ($oke) {
		$mantab  = true;
		$tmpPot = glob($pathfile . "/foto_ktp_" . $idnya . "_*.{jpg,jpeg,gif,png,pdf,rar,zip}", GLOB_BRACE);

		if (count($tmpPot) > 0) {
			foreach ($tmpPot as $datj)
				if (file_exists($datj)) unlink($datj);
		}
		$tujuan  = $pathfile . "/" . $fileUploadName;
		$mantab  = $mantab && move_uploaded_file($tempPhoto, $tujuan);
		if (file_exists($tempPhoto)) unlink($tempPhoto);

		if ($fileNpwp) {
			$tmpPotNpwp = glob($pathfileNpwp . "/foto_npwp_" . $idnya . "_*.{jpg,jpeg,gif,png,pdf,rar,zip}", GLOB_BRACE);

			if (count($tmpPotNpwp) > 0) {
				foreach ($tmpPotNpwp as $datjNpwp)
					if (file_exists($datjNpwp)) unlink($datjNpwp);
			}
			$tujuanNpwp  = $pathfileNpwp . "/" . $fileUploadNameNpwp;
			$mantab  = $mantab && move_uploaded_file($tempNpwp, $tujuanNpwp);
			if (file_exists($tempNpwp)) unlink($tempNpwp);
		}

		if ($mantab) {
			$ems1 = "SELECT distinct email_user FROM acl_user WHERE id_wilayah ='" . $id_wilayah . "' AND id_role='7' AND is_active=1";
			$sql_customer = "SELECT * FROM pro_customer WHERE id_customer=" . $customer . "";
			$res_cust = $con->getRecord($sql_customer);

			if ($ems1) {
				$rms1 = $con->getResult($ems1);
				$mail = new PHPMailer;
				$mail->isSMTP();
				$mail->Host = 'smtp.gmail.com';
				$mail->Port = 465;
				$mail->SMTPSecure = 'ssl';
				$mail->SMTPAuth = true;
				$mail->SMTPKeepAlive = true;
				$mail->Username = USR_EMAIL_PROENERGI202389;
				$mail->Password = PWD_EMAIL_PROENERGI202389;

				$mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');
				foreach ($rms1 as $datms) {
					$mail->addAddress($datms['email_user']);
				}
				$mail->Subject = "Approval Penerima Refund";
				$mail->msgHTML("" . $fullname . " mengajukan permohonan approval untuk customer " . $res_cust['nama_customer'] . " atas nama " . ucwords($nama_penerima) . " <p>" . BASE_SERVER . "</p>");
				$mail->send();
			}

			$con->commit();
			$con->close();
			header("location: " . $url_back);
			exit();
		} else {
			$msg = "Foto KTP atau NPWP gagal di input ke server";
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
} else if ($act == 'update') {
	if ($active == 1) {
		$sqlrow = "SELECT * FROM pro_master_penerima_refund WHERE id = '" . $idr . "'";
		$row = $con->getRecord($sqlrow);
		if ($row['is_bm'] == 1) {
			$msg = "Data berhasil di ubah";
			$sql = "
			update pro_master_penerima_refund set is_active = '" . $active . "', updated_at = '" . date("Y-m-d H:i:s") . "', updated_by = '" . $fullname . "' where id = " . $idr;

			$con->setQuery($sql);
			$oke  = $oke && !$con->hasError();
		} else {
			if ($filePhoto != "") {
				$upl = true;
				$fileUploadName = 'foto_ktp_' . $idr . '_' . sanitize_filename($filePhoto);

				if ($fileNpwp) {
					$fileUploadNameNpwp = 'foto_npwp_' . $idr . '_' . sanitize_filename($fileNpwp);
					if ($filePhoto != "" && $sizePhoto > $max_size) {
						$msg = "Ukuran foto KTP terlalu besar, melebihi 2 MB.";
						$oke = false;
					} elseif ($fileNpwp != "" && $sizeNpwp > $max_size_npwp) {
						$msg = "Ukuran foto NPWP terlalu besar, melebihi 2 MB.";
						$oke = false;
					} else {
						if ($active == $row['is_active']) {
							$msg = "Data berhasil di ubah";
							$sql = "
							update pro_master_penerima_refund set id_customer = '" . $customer . "', nama = '" . $nama_penerima . "', divisi = '" . $divisi . "', no_ktp = '" . $no_ktp . "', foto_ktp = '" . $fileUploadName . "', foto_npwp = '" . $fileUploadNameNpwp . "', bank = '" . $bank . "', kode_bank = '" . $kode_bank . "', no_rekening = '" . $no_rekening . "', atas_nama = '" . $atas_nama . "', is_active = '" . $active . "', updated_at = '" . date("Y-m-d H:i:s") . "', updated_by = '" . $fullname . "'
							where id = " . $idr;

							$con->setQuery($sql);
							$oke  = $oke && !$con->hasError();
						} else {
							$sqlcek = "SELECT * FROM pro_master_penerima_refund WHERE id_customer = '" . $customer . "' AND is_active = '1'";
							$res = $con->getResult($sqlcek);
							if (count($res) == 3) {
								$msg = "Data penerima pada customer tersebut sudah ada 3 yang berstatus aktif";
								$oke = false;
							} else {
								$msg = "Data berhasil di ubah";
								$sql = "
								update pro_master_penerima_refund set id_customer = '" . $customer . "', nama = '" . $nama_penerima . "', divisi = '" . $divisi . "', no_ktp = '" . $no_ktp . "', foto_ktp = '" . $fileUploadName . "', foto_npwp = '" . $fileUploadNameNpwp . "', bank = '" . $bank . "', kode_bank = '" . $kode_bank . "', no_rekening = '" . $no_rekening . "', atas_nama = '" . $atas_nama . "', is_active = '" . $active . "', updated_at = '" . date("Y-m-d H:i:s") . "', updated_by = '" . $fullname . "'
								where id = " . $idr;

								$con->setQuery($sql);
								$oke  = $oke && !$con->hasError();
							}
						}
					}
				} else {
					if ($filePhoto != "" && $sizePhoto > $max_size) {
						$msg = "Ukuran foto KTP terlalu besar, melebihi 2 MB.";
						$oke = false;
					} else {
						if ($active == $row['is_active']) {
							$msg = "Data berhasil di ubah";
							$sql = "
							update pro_master_penerima_refund set id_customer = '" . $customer . "', nama = '" . $nama_penerima . "', divisi = '" . $divisi . "', no_ktp = '" . $no_ktp . "', foto_ktp = '" . $fileUploadName . "', bank = '" . $bank . "', kode_bank = '" . $kode_bank . "', no_rekening = '" . $no_rekening . "', atas_nama = '" . $atas_nama . "', is_active = '" . $active . "', updated_at = '" . date("Y-m-d H:i:s") . "', updated_by = '" . $fullname . "'
							where id = " . $idr;

							$con->setQuery($sql);
							$oke  = $oke && !$con->hasError();
						} else {
							$sqlcek = "SELECT * FROM pro_master_penerima_refund WHERE id_customer = '" . $customer . "' AND is_active = '1'";
							$res = $con->getResult($sqlcek);
							if (count($res) == 3) {
								$msg = "Data penerima pada customer tersebut sudah ada 3 yang berstatus aktif";
								$oke = false;
							} else {
								$msg = "Data berhasil di ubah";
								$sql = "
								update pro_master_penerima_refund set id_customer = '" . $customer . "', nama = '" . $nama_penerima . "', divisi = '" . $divisi . "', no_ktp = '" . $no_ktp . "', foto_ktp = '" . $fileUploadName . "', bank = '" . $bank . "', kode_bank = '" . $kode_bank . "', no_rekening = '" . $no_rekening . "', atas_nama = '" . $atas_nama . "', is_active = '" . $active . "', updated_at = '" . date("Y-m-d H:i:s") . "', updated_by = '" . $fullname . "'
								where id = " . $idr;

								$con->setQuery($sql);
								$oke  = $oke && !$con->hasError();
							}
						}
					}
				}
			} else {
				if ($fileNpwp) {
					$upl = true;
					$fileUploadNameNpwp = 'foto_npwp_' . $idr . '_' . sanitize_filename($fileNpwp);

					if ($fileNpwp != "" && $sizeNpwp > $max_size_npwp) {
						$msg = "Ukuran foto NPWP terlalu besar, melebihi 2 MB.";
						$oke = false;
					} else {

						if ($active == $row['is_active']) {
							$msg = "Data berhasil di ubah";
							$sql = "
							update pro_master_penerima_refund set id_customer = '" . $customer . "', nama = '" . $nama_penerima . "', divisi = '" . $divisi . "', no_ktp = '" . $no_ktp . "', foto_npwp = '" . $fileUploadNameNpwp . "', bank = '" . $bank . "', kode_bank = '" . $kode_bank . "', no_rekening = '" . $no_rekening . "', atas_nama = '" . $atas_nama . "', is_active = '" . $active . "', updated_at = '" . date("Y-m-d H:i:s") . "', updated_by = '" . $fullname . "'
							where id = " . $idr;

							$con->setQuery($sql);
							$oke  = $oke && !$con->hasError();
						} else {
							$sqlcek = "SELECT * FROM pro_master_penerima_refund WHERE id_customer = '" . $customer . "' AND is_active = '1'";
							$res = $con->getResult($sqlcek);
							if (count($res) == 3) {
								$msg = "Data penerima pada customer tersebut sudah ada 3 yang berstatus aktif";
								$oke = false;
							} else {
								$msg = "Data berhasil di ubah";
								$sql = "
								update pro_master_penerima_refund set id_customer = '" . $customer . "', nama = '" . $nama_penerima . "', divisi = '" . $divisi . "', no_ktp = '" . $no_ktp . "', foto_npwp = '" . $fileUploadNameNpwp . "', bank = '" . $bank . "', kode_bank = '" . $kode_bank . "', no_rekening = '" . $no_rekening . "', atas_nama = '" . $atas_nama . "', is_active = '" . $active . "', updated_at = '" . date("Y-m-d H:i:s") . "', updated_by = '" . $fullname . "'
								where id = " . $idr;

								$con->setQuery($sql);
								$oke  = $oke && !$con->hasError();
							}
						}
					}
				} else {
					$upl = false;
					if ($active == $row['is_active']) {
						$msg = "Data berhasil di ubah";
						$sql = "
						update pro_master_penerima_refund set id_customer = '" . $customer . "', nama = '" . $nama_penerima . "', divisi = '" . $divisi . "', no_ktp = '" . $no_ktp . "', bank = '" . $bank . "', kode_bank = '" . $kode_bank . "', no_rekening = '" . $no_rekening . "', atas_nama = '" . $atas_nama . "', is_active = '" . $active . "', updated_at = '" . date("Y-m-d H:i:s") . "', updated_by = '" . $fullname . "'
						where id = " . $idr;

						$con->setQuery($sql);
						$oke  = $oke && !$con->hasError();
					} else {
						$sqlcek = "SELECT * FROM pro_master_penerima_refund WHERE id_customer = '" . $customer . "' AND is_active = '1'";
						$res = $con->getResult($sqlcek);
						if (count($res) == 3) {
							$msg = "Data penerima pada customer tersebut sudah ada 3 yang berstatus aktif";
							$oke = false;
						} else {
							$msg = "Data berhasil di ubah";
							$sql = "
							update pro_master_penerima_refund set id_customer = '" . $customer . "', nama = '" . $nama_penerima . "', divisi = '" . $divisi . "', no_ktp = '" . $no_ktp . "', bank = '" . $bank . "', kode_bank = '" . $kode_bank . "', no_rekening = '" . $no_rekening . "', atas_nama = '" . $atas_nama . "', is_active = '" . $active . "', updated_at = '" . date("Y-m-d H:i:s") . "', updated_by = '" . $fullname . "'
							where id = " . $idr;

							$con->setQuery($sql);
							$oke  = $oke && !$con->hasError();
						}
					}
				}
			}
		}
	} else {
		$upl = false;
		$sqlrow = "SELECT * FROM pro_master_penerima_refund WHERE id = '" . $idr . "'";
		$row = $con->getRecord($sqlrow);
		if ($row['is_bm'] == 1) {
			$msg = "Data berhasil di ubah";
			$sql = "
			update pro_master_penerima_refund set is_active = 0, updated_at = '" . date("Y-m-d H:i:s") . "', updated_by = '" . $fullname . "' where id = " . $idr;

			$con->setQuery($sql);
			$oke  = $oke && !$con->hasError();
		} else {
			$msg = "Data berhasil di ubah";
			$sql = "
				update pro_master_penerima_refund set id_customer = '" . $customer . "', nama = '" . $nama_penerima . "', divisi = '" . $divisi . "', no_ktp = '" . $no_ktp . "', bank = '" . $bank . "', kode_bank = '" . $kode_bank . "', no_rekening = '" . $no_rekening . "', atas_nama = '" . $atas_nama . "', is_active = 0, updated_at = '" . date("Y-m-d H:i:s") . "', updated_by = '" . $fullname . "'
				where id = " . $idr;

			$con->setQuery($sql);
			$oke  = $oke && !$con->hasError();
		}
	}
	if ($oke) {
		$mantab  = true;
		if ($upl) {
			if ($filePhoto != "") {
				$tmpPot = glob($pathfile . "/foto_ktp_" . $idr . "_*.{jpg,jpeg,gif,png,pdf,rar,zip}", GLOB_BRACE);

				if (count($tmpPot) > 0) {
					foreach ($tmpPot as $datj)
						if (file_exists($datj)) unlink($datj);
				}
				$tujuan  = $pathfile . "/" . $fileUploadName;
				$mantab  = $mantab && move_uploaded_file($tempPhoto, $tujuan);
				if (file_exists($tempPhoto)) unlink($tempPhoto);
			}

			if ($fileNpwp != "") {
				$tmpPotNpwp = glob($pathfileNpwp . "/foto_npwp_" . $idr . "_*.{jpg,jpeg,gif,png,pdf,rar,zip}", GLOB_BRACE);

				if (count($tmpPotNpwp) > 0) {
					foreach ($tmpPotNpwp as $datjNpwp)
						if (file_exists($datjNpwp)) unlink($datjNpwp);
				}
				$tujuanNpwp  = $pathfileNpwp . "/" . $fileUploadNameNpwp;
				$mantab  = $mantab && move_uploaded_file($tempNpwp, $tujuanNpwp);
				if (file_exists($tempNpwp)) unlink($tempNpwp);
			}
		}
		if ($mantab) {
			$con->commit();
			$con->close();
			header("location: " . $url_back);
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
		$flash->add("error", "Data gagal diubah", BASE_REFERER);
	}
} else if ($act == 'approve') {
	$sql = "
		update pro_master_penerima_refund set is_bm = '1', bm_by = '" . $fullname . "', bm_date = NOW(), catatan_bm = '" . $catatan_bm . "' where id = " . $id_penerima_refund;

	$con->setQuery($sql);
	$oke  = $oke && !$con->hasError();

	if ($oke) {
		$ems1 = "SELECT distinct email_user FROM acl_user WHERE id_role='21' AND is_active=1";
		$sql_penerima_refund = "SELECT a.*, CONCAT(b.kode_pelanggan,' ',b.nama_customer) as nama_customer, c.fullname as marketingnya FROM pro_master_penerima_refund a JOIN pro_customer b ON a.id_customer=b.id_customer JOIN acl_user c ON b.id_marketing=c.id_user WHERE a.id='" . $id_penerima_refund . "'";
		$res_penerima = $con->getRecord($sql_penerima_refund);

		if ($ems1) {
			$rms1 = $con->getResult($ems1);
			$mail = new PHPMailer;
			$mail->isSMTP();
			$mail->Host = 'smtp.gmail.com';
			$mail->Port = 465;
			$mail->SMTPSecure = 'ssl';
			$mail->SMTPAuth = true;
			$mail->SMTPKeepAlive = true;
			$mail->Username = USR_EMAIL_PROENERGI202389;
			$mail->Password = PWD_EMAIL_PROENERGI202389;

			$mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');
			foreach ($rms1 as $datms) {
				$mail->addAddress($datms['email_user']);
			}
			$mail->Subject = "Approval Penerima Refund";
			$mail->msgHTML("" . $res_penerima['created_by'] . " mengajukan permohonan approval untuk customer " . $res_penerima['nama_customer'] . " atas nama " . ucwords($res_penerima['nama']) . " <p>" . BASE_SERVER . "</p>");
			$mail->send();
		}


		$con->commit();
		$con->close();
		$data = [
			"status" => true,
			"pesan"  => "Data Penerima Refund berhasil di Approve",
		];
		echo json_encode($data);
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$data = [
			"status" => false,
			"pesan"  => "Data Penerima Refund gagal di Approve",
		];
		echo json_encode($data);
	}
} else if ($act == 'reject') {
	$sql = "
		update pro_master_penerima_refund set is_bm = '2', bm_by = '" . $fullname . "', bm_date = NOW(), catatan_bm = '" . $catatan_bm . "' where id = " . $id_penerima_refund;

	$con->setQuery($sql);
	$oke  = $oke && !$con->hasError();

	if ($oke) {
		$con->commit();
		$con->close();
		$data = [
			"status" => true,
			"pesan"  => "Data Penerima Refund berhasil di Tolak",
		];
		echo json_encode($data);
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$data = [
			"status" => false,
			"pesan"  => "Data Penerima Refund gagal di Tolak",
		];
		echo json_encode($data);
	}
} else if ($act == 'approve_ceo') {
	$sql = "
		update pro_master_penerima_refund set is_ceo = '1', ceo_by = '" . $fullname . "', ceo_date = NOW(), catatan_ceo = '" . $catatan_ceo . "' where id = " . $id_penerima_refund;

	$con->setQuery($sql);
	$oke  = $oke && !$con->hasError();

	if ($oke) {
		$sql_penerima_refund = "SELECT a.*, CONCAT(b.kode_pelanggan,' ',b.nama_customer) as nama_customer, c.fullname as marketingnya FROM pro_master_penerima_refund a JOIN pro_customer b ON a.id_customer=b.id_customer JOIN acl_user c ON b.id_marketing=c.id_user WHERE a.id='" . $id_penerima_refund . "'";
		$res_penerima = $con->getRecord($sql_penerima_refund);

		$ems1 = "SELECT distinct email_user FROM acl_user WHERE fullname='" . $res_penerima['created_by'] . "' AND is_active=1";

		if ($ems1) {
			$rms1 = $con->getResult($ems1);
			$mail = new PHPMailer;
			$mail->isSMTP();
			$mail->Host = 'smtp.gmail.com';
			$mail->Port = 465;
			$mail->SMTPSecure = 'ssl';
			$mail->SMTPAuth = true;
			$mail->SMTPKeepAlive = true;
			$mail->Username = USR_EMAIL_PROENERGI202389;
			$mail->Password = PWD_EMAIL_PROENERGI202389;

			$mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');
			foreach ($rms1 as $datms) {
				$mail->addAddress($datms['email_user']);
			}
			$mail->Subject = "Approval Penerima Refund";
			$mail->msgHTML("" . $fullname . " telah melakukan approval untuk customer " . $res_penerima['nama_customer'] . " atas nama " . $res_penerima['nama'] . " <p>" . BASE_SERVER . "</p>");
			$mail->send();
		}

		$con->commit();
		$con->close();
		$data = [
			"status" => true,
			"pesan"  => "Data Penerima Refund berhasil di Approve",
		];
		echo json_encode($data);
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$data = [
			"status" => false,
			"pesan"  => "Data Penerima Refund gagal di Approve",
		];
		echo json_encode($data);
	}
} else if ($act == 'reject_ceo') {
	$sql = "
		update pro_master_penerima_refund set is_ceo = '2', ceo_by = '" . $fullname . "', ceo_date = NOW(), catatan_ceo = '" . $catatan_ceo . "' where id = " . $id_penerima_refund;

	$con->setQuery($sql);
	$oke  = $oke && !$con->hasError();

	if ($oke) {
		$con->commit();
		$con->close();
		$data = [
			"status" => true,
			"pesan"  => "Data Penerima Refund berhasil di Tolak",
		];
		echo json_encode($data);
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$data = [
			"status" => false,
			"pesan"  => "Data Penerima Refund gagal di Tolak",
		];
		echo json_encode($data);
	}
}

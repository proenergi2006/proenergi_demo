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
$pic 	= paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]);
$term 	= paramDecrypt($_SESSION["sinori" . SESSIONID]["terminal"]);
$id_wil = paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]);
$answer	= array();

$file 	= htmlspecialchars($_POST["file"], ENT_QUOTES);
$aksi 	= htmlspecialchars($_POST["aksi"], ENT_QUOTES);
$status	= htmlspecialchars($_POST["status"], ENT_QUOTES);
$dt1 	= htmlspecialchars($_POST["dt1"], ENT_QUOTES);
$dt2 	= htmlspecialchars($_POST["dt2"], ENT_QUOTES);
$dt3 	= htmlspecialchars($_POST["dt3"], ENT_QUOTES);
$tera_depo 	= htmlspecialchars($_POST["tera_depo"], ENT_QUOTES);
$tera_site 	= htmlspecialchars($_POST["tera_site"], ENT_QUOTES);
$dt4 	= htmlspecialchars(str_replace(array(","), array(""), $_POST["dt4"]), ENT_QUOTES);
$dt5 	= htmlspecialchars($_POST["dt5"], ENT_QUOTES);
$customer_dr_kapal 	= htmlspecialchars($_POST["customer_kapal"], ENT_QUOTES);
$customer_alamat_dr 	= htmlspecialchars($_POST["customer_alamat_dr"], ENT_QUOTES);


$param 	= htmlspecialchars(paramDecrypt($_POST["param"]), ENT_QUOTES);
$param_kapal 	= htmlspecialchars(paramDecrypt($_POST["param-kapal"]), ENT_QUOTES);
$param 	= explode("|#|", $param);
$param_kapal 	= explode("|#|", $param_kapal);

//truck
$idnya 	= $param[0];
$volume = $param[1];

$pic_cs = $param[2];
$pic_marketing = $param[3];
$id_cust = $param[4];
$nama_cust = $param[5];
$alamat_survey = $param[6];
$no_plat = $param[7];
$nama_sopir = $param[8];

//kapal
$idnya_kapal 	= $param[0];
$customer_kapal = $param_kapal[1];

$catatan_losses = $_POST["catatan_losses"] ?? "";









$tipe 	= htmlspecialchars($_POST["tipe"], ENT_QUOTES);

if ($file == "logistik") {
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	if ($aksi == "ubah") {
		$arrSql = array(1 => array("table" => "pro_po_ds_detail", "key" => "id_dsd"));
		$cek1 = "select status_pengiriman from " . $arrSql[$tipe]["table"] . " where " . $arrSql[$tipe]["key"] . " = '" . $idnya . "'";
		$row1 = $con->getOne($cek1);
		$temp = json_decode($row1, true);
		$arrS = ($temp == NULL) ? array() : $temp;
		array_push($arrS, array("status" => $status, "tanggal" => $dt1 . " " . $dt2 . ":" . $dt3));

		$sql1 = "update " . $arrSql[$tipe]["table"] . " set status_pengiriman = '" . json_encode($arrS) . "' where " . $arrSql[$tipe]["key"] . " = '" . $idnya . "'";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();
	} else if ($aksi == "ubah-kapal") {
		$arrSql = array(1 => array("table" => "pro_po_ds_kapal", "key" => "id_dsk"));
		$cek1 = "select status_pengiriman from " . $arrSql[$tipe]["table"] . " where " . $arrSql[$tipe]["key"] . " = '" . $idnya . "'";
		$row1 = $con->getOne($cek1);
		$temp = json_decode($row1, true);
		$arrS = ($temp == NULL) ? array() : $temp;
		array_push($arrS, array("status" => $status, "tanggal" => $dt1 . " " . $dt2 . ":" . $dt3));

		$sql1 = "update " . $arrSql[$tipe]["table"] . " set status_pengiriman = '" . json_encode($arrS) . "' where " . $arrSql[$tipe]["key"] . " = '" . $idnya . "'";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();
	} else if ($aksi == "selesai") {
		$arrSql = array(1 => array("table" => "pro_po_ds_detail", "key" => "id_dsd"));
		$tglD 	= tgl_db($dt1) . " " . $dt2 . ":" . $dt3 . ":00";

		$cek1 = "select a.id_plan, a.status_pengiriman, c.id_customer, c.status_customer from " . $arrSql[$tipe]["table"] . " a join pro_po_customer b on a.id_poc = b.id_poc 
					 join pro_customer c on b.id_customer = c.id_customer where a." . $arrSql[$tipe]["key"] . " = '" . $idnya . "'";
		$row1 = $con->getRecord($cek1);
		$temp = json_decode($row1['status_pengiriman'], true);
		$arrS = ($temp == NULL) ? array() : $temp;
		array_push($arrS, array("status" => "Produk telah terkirim ke customer", "tanggal" => $dt1 . " " . $dt2 . ":" . $dt3));

		$sql1 = "update " . $arrSql[$tipe]["table"] . " set status_pengiriman = '" . json_encode($arrS) . "', is_delivered = 1, tanggal_delivered = '" . $tglD . "' 
					 where " . $arrSql[$tipe]["key"] . " = '" . $idnya . "'";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();

		$sql2 = "update pro_po_customer_plan set realisasi_kirim = realisasi_kirim + " . $volume . " where id_plan = '" . $row1['id_plan'] . "'";
		$con->setQuery($sql2);
		$oke  = $oke && !$con->hasError();

		if ($row1['status_customer'] == 3) {
			$sql3 = "update pro_customer set fix_customer_redate = '" . tgl_db($dt1) . "' where id_customer = '" . $row1['id_customer'] . "'";
			$con->setQuery($sql3);
			$oke  = $oke && !$con->hasError();
		}
	} else if ($aksi == "selesai-kapal") {
		$arrSql = array(1 => array("table" => "pro_po_ds_kapal", "key" => "id_dsk"));
		$tglD 	= tgl_db($dt1) . " " . $dt2 . ":" . $dt3 . ":00";

		$cek1 = "select a.id_plan, a.status_pengiriman, c.id_customer, c.status_customer from " . $arrSql[$tipe]["table"] . " a join pro_po_customer b on a.id_poc = b.id_poc 
					 join pro_customer c on b.id_customer = c.id_customer where a." . $arrSql[$tipe]["key"] . " = '" . $idnya . "'";
		$row1 = $con->getRecord($cek1);
		$temp = json_decode($row1['status_pengiriman'], true);
		$arrS = ($temp == NULL) ? array() : $temp;
		array_push($arrS, array("status" => "Produk telah terkirim ke customer", "tanggal" => $dt1 . " " . $dt2 . ":" . $dt3));

		$sql1 = "update " . $arrSql[$tipe]["table"] . " set status_pengiriman = '" . json_encode($arrS) . "', is_delivered = 1, tanggal_delivered = '" . $tglD . "' 
					 where " . $arrSql[$tipe]["key"] . " = '" . $idnya_kapal . "'";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();

		$sql2 = "update pro_po_customer_plan set realisasi_kirim = realisasi_kirim + " . $volume . " where id_plan = '" . $row1['id_plan'] . "'";
		$con->setQuery($sql2);
		$oke  = $oke && !$con->hasError();

		if ($row1['status_customer'] == 3) {
			$sql3 = "update pro_customer set fix_customer_redate = '" . tgl_db($dt1) . "' where id_customer = '" . $row1['id_customer'] . "'";
			$con->setQuery($sql3);
			$oke  = $oke && !$con->hasError();
		}
	} else if ($aksi == "realisasi") {
		$arrSql = array(1 => array("table" => "pro_po_ds_detail", "key" => "id_dsd"));
		$tglD 	= tgl_db($dt1) . " " . $dt2 . ":" . $dt3 . ":00";

		$cek1 = "select a.id_plan, a.status_pengiriman, c.id_customer, c.status_customer from " . $arrSql[$tipe]["table"] . " a join pro_po_customer b on a.id_poc = b.id_poc 
					 join pro_customer c on b.id_customer = c.id_customer where a." . $arrSql[$tipe]["key"] . " = '" . $idnya . "'";
		$row1 = $con->getRecord($cek1);
		$temp = json_decode($row1['status_pengiriman'], true);
		$arrS = ($temp == NULL) ? array() : $temp;
		array_push($arrS, array("status" => "Entry data terima surat jalan dan realisasi volume", "tanggal" => $dt1 . " " . $dt2 . ":" . $dt3));


		$pathfile = $public_base_directory . '/files/uploaded_user/lampiran';

		$oke = true;
		if (!empty($_FILES['losses_file']['name'])) {
			$filePhoto = $_FILES['losses_file']['name'];
			$sizePhoto = $_FILES['losses_file']['size'];
			$tempPhoto = $_FILES['losses_file']['tmp_name'];
			$extPhoto  = strtolower(pathinfo($filePhoto, PATHINFO_EXTENSION));
			$max_size  = 2 * 1024 * 1024;
			$allow_type = ["jpg", "jpeg", "png", "pdf", "zip", "rar"];

			if (!in_array($extPhoto, $allow_type)) {
				echo json_encode(["error" => "Tipe file tidak diperbolehkan."]);
				exit;
			}

			if ($sizePhoto > $max_size) {
				echo json_encode(["error" => "Ukuran file maksimal 2MB."]);
				exit;
			}

			$safeName = 'LOS_' . $idnya . '_' . sanitize_filename($filePhoto);
			move_uploaded_file($tempPhoto, "$pathfile/$safeName");

			$sql1 = "UPDATE " . $arrSql[$tipe]["table"] . "
				SET status_pengiriman = '" . json_encode($arrS) . "',
					realisasi_volume = '$dt4',
					tgl_realisasi = '$tglD',
					terima_jalan = '$dt5',
					tera_depo = '$tera_depo',
					tera_site = '$tera_site',
					lampiran_losses = '$safeName',
					lampiran_losses_ori = '$filePhoto',
					catatan_losses = '$catatan_losses'
				WHERE " . $arrSql[$tipe]["key"] . " = '$idnya'";
		} else {
			$sql1 = "UPDATE " . $arrSql[$tipe]["table"] . "
				SET status_pengiriman = '" . json_encode($arrS) . "',
					realisasi_volume = '$dt4',
					tgl_realisasi = '$tglD',
					terima_jalan = '$dt5',
					tera_depo = '$tera_depo',
					tera_site = '$tera_site',
					catatan_losses = '$catatan_losses'
				WHERE " . $arrSql[$tipe]["key"] . " = '$idnya'";
		}

		$con->setQuery($sql1);
		$oke = $oke && !$con->hasError();

		// $sql1 = "update " . $arrSql[$tipe]["table"] . " set status_pengiriman = '" . json_encode($arrS) . "', realisasi_volume = '" . $dt4 . "', tgl_realisasi = '" . $tglD . "', terima_jalan = '" . $dt5 . "', tera_depo = '" . $tera_depo . "', tera_site = '" . $tera_site . "' where " . $arrSql[$tipe]["key"] . " = '" . $idnya . "'";
		// $con->setQuery($sql1);
		// $oke  = $oke && !$con->hasError();

		if ($oke) {
			$cek2 = "select b.volume_po,
					a.realisasi_volume,
					c.harga_poc,
					d.tol_susut
					from pro_po_ds_detail a
					join pro_po_detail b on a.id_pod = b.id_pod 
					join pro_po_customer c on a.id_poc = c.id_poc
					join pro_penawaran d on c.id_penawaran = d.id_penawaran
					join pro_po e on a.id_po = e.id_po
					
					where a.id_dsd = '" . $idnya . "'";
			$row2 = $con->getRecord($cek2);

			$realisasi_volume = $row2['realisasi_volume'];
			$volume_po = $row2['volume_po'];
			$harga = $row2['harga_poc'];
			$tol_susut = $row2['tol_susut'];
			$tol_susut_trans = $row2['toleransi_susut'];

			// Hitung losses
			$losses = $volume_po - $realisasi_volume;

			// Hitung harga total dari losses
			$total = $harga * $losses;

			// Hitung batas toleransi penyusutan (dalam liter)
			$toleransi = ($volume_po * $tol_susut) / 100;

			$toleransi_transportir = ($volume_po * $tol_susut_trans) / 100;

			// Hitung losses setelah dikurangi toleransi
			$losses_setelah_toleransi = $losses - $toleransi;



			$hrglossestoleransi = $harga * $losses_setelah_toleransi;

			// Tentukan disposisi berdasarkan aturan
			// 

			if ($losses_setelah_toleransi <= 0) {
				// Jika losses setelah toleransi = 0 → disposisi 0 (Tidak perlu approval)
				$disposisi_losses = 0;
				$ems2 = ""; // Tidak ada email yang dikirim
			} elseif ($losses_setelah_toleransi > 50 || $hrglossestoleransi > 1000000) {
				// Jika losses setelah toleransi > 50L ATAU total > 1.000.000 → disposisi 2 (OM & Finance Approve)
				$disposisi_losses = 2;
				$ems2 = "select distinct email_user from acl_user where id_role = 6";
			} else {
				// Jika losses setelah toleransi ≤ 50L ATAU total ≤ 1.000.000 → disposisi 1 (BM Approve)
				$disposisi_losses = 1;
				$ems2 = "select distinct email_user from acl_user where id_role = 7 and id_wilayah = '" . $id_wil . "'";
			}

			// Update ke database
			$sql = "update pro_po_ds_detail 
       				 SET losses = '" . $losses . "', 
          			 harga_losses = '" . $total . "', 
					 batas_toleransi = '" . $toleransi . "', 
					 batas_toleransi_transportir = '" . $toleransi_transportir . "',
           			 disposisi_losses = '" . $disposisi_losses . "' 
       				 WHERE id_dsd = '" . $idnya . "'";

			$con->setQuery($sql);
			$oke  = $oke && !$con->hasError();
		}
	} else if ($aksi == "realisasi-kapal") {
		$arrSql = array(1 => array("table" => "pro_po_ds_kapal", "key" => "id_dsk"));
		$tglD 	= tgl_db($dt1) . " " . $dt2 . ":" . $dt3 . ":00";

		$cek1 = "select a.id_plan, a.status_pengiriman, c.id_customer, c.status_customer from " . $arrSql[$tipe]["table"] . " a join pro_po_customer b on a.id_poc = b.id_poc 
					 join pro_customer c on b.id_customer = c.id_customer where a." . $arrSql[$tipe]["key"] . " = '" . $idnya . "'";
		$row1 = $con->getRecord($cek1);
		$temp = json_decode($row1['status_pengiriman'], true);
		$arrS = ($temp == NULL) ? array() : $temp;
		array_push($arrS, array("status" => "Entry data terima surat jalan dan realisasi volume", "tanggal" => $dt1 . " " . $dt2 . ":" . $dt3));

		$sql1 = "update " . $arrSql[$tipe]["table"] . " set status_pengiriman = '" . json_encode($arrS) . "', realisasi_volume = '" . $dt4 . "', tgl_realisasi = '" . $tglD . "', terima_jalan = '" . $dt5 . "' where " . $arrSql[$tipe]["key"] . " = '" . $idnya . "'";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();
	}



	if ($oke) {
		$con->commit();
		/*notif email yg punya id_role in (11,18) yg di wilayah nya*/
		if ($aksi == 'selesai' || $aksi == 'realisasi') {
			//$ems1 = "select distinct email_user from acl_user where  id_wilayah ='".$id_wil."' and id_user='".$pic_marketing."' or id_role in('18') and is_active=1";
			$ems1 = "select distinct email_user FROM acl_user WHERE id_wilayah ='" . $id_wil . "' AND (id_user='" . $pic_marketing . "' OR id_role='18' OR id_role='10') AND is_active=1";


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
				$mail->Subject = "Delivered  [" . $nama_cust . ', ' . date('d/m/Y H:i:s') . "]";
				$mail->msgHTML(paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " telah menyelesaikan delivery di [" . $alamat_survey . "] [" . $no_plat . "] [" . $nama_sopir . "]");
				$mail->send();
			}

			if ($ems2) {
				$rms2 = $con->getResult($ems2);
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
				foreach ($rms2 as $datms2) {
					$mail->addAddress($datms2['email_user']);
				}
				$mail->Subject = "Pengajuan Losses  [" . $nama_cust . ', ' . date('d/m/Y H:i:s') . "]";
				$mail->msgHTML(paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " meminta persetujuan losses ");
				$mail->send();
			}
		} else if ($aksi == 'selesai-kapal') {
			//$ems1 = "select distinct email_user from acl_user where  id_wilayah ='".$id_wil."' and id_user='".$pic_marketing."' or id_role in('18') and is_active=1";
			$ems1 = "select distinct email_user FROM acl_user WHERE id_wilayah ='" . $id_wil . "' AND (id_user='" . $pic_marketing . "' OR id_role='18' OR id_role='10') AND is_active=1";


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
				$mail->Subject = "Delivered  [" . $customer_dr_kapal . ', ' . date('d/m/Y H:i:s') . "]";
				$mail->msgHTML(paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " telah menyelesaikan delivery di [" . $customer_alamat_dr . "] ");
				$mail->send();
			}
		}
		/*end notif email*/
		$con->close();
		$answer["error"] = "";
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$answer["error"] = "Maaf, sistem mengalami kendala teknis. Silahkan coba lagi..";
	}
	echo json_encode($answer);
} else if ($file == "logtrans") {
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	if ($aksi == "ubah") {
		$isiTab = "";
		$arrSql = array(1 => array("table" => "pro_po_ds_detail", "key" => "id_dsd"), array("table" => "pro_po_ds_kapal", "key" => "id_dsk"));
		$arrSts	= array();

		if (count($_POST["edit1"]) > 0) {
			foreach ($_POST["edit1"] as $idx => $val) {
				$tgl = htmlspecialchars($_POST["edit1"][$idx], ENT_QUOTES);
				$jam = htmlspecialchars($_POST["edit2"][$idx], ENT_QUOTES);
				$mnt = htmlspecialchars($_POST["edit3"][$idx], ENT_QUOTES);
				$sts = htmlspecialchars($_POST["edit4"][$idx], ENT_QUOTES);
				if ($tgl && $jam && $mnt && $sts) array_push($arrSts, array("status" => $sts, "tanggal" => $tgl . " " . $jam . ":" . $mnt));
			}
		}

		$sql1 = "update " . $arrSql[$tipe]["table"] . " set status_pengiriman = '" . json_encode($arrSts) . "' where " . $arrSql[$tipe]["key"] . " = '" . $idnya . "'";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();
	}

	if ($oke) {
		$con->commit();

		$cek1 	= "select status_pengiriman, is_delivered, is_cancel from " . $arrSql[$tipe]["table"] . " where " . $arrSql[$tipe]["key"] . " = '" . $idnya . "'";
		$row1 	= $con->getRecord($cek1);
		$arrS 	= json_decode($row1['status_pengiriman'], true);
		$tmids	= paramEncrypt($idnya . "|#|");
		if (count($arrS) > 0) {
			$nom = 0;
			foreach ($arrS as $idxnya => $data) {
				$nom++;
				$tanggal = tgl_indo(substr($data['tanggal'], 0, 10), 'long', 'ndb') . ' ' . substr($data['tanggal'], 11);
				$opt_tgl = "<option></option>";
				for ($i = 0; $i < 24; $i++) {
					$select = (substr($data['tanggal'], 11, 2) == $i) ? ' selected' : '';
					$opt_tgl .= '<option' . $select . '>' . str_pad($i, 2, '0', STR_PAD_LEFT) . '</option>';
				}

				$opt_jam = "<option></option>";
				for ($i = 0; $i < 60; $i++) {
					$select = (substr($data['tanggal'], 14, 2) == $i) ? ' selected' : '';
					$opt_jam .= '<option' . $select . '>' . str_pad($i, 2, '0', STR_PAD_LEFT) . '</option>';
				}

				$isiTab .= '<tr>
						<td class="text-center">' . $nom . '</td>
						<td class="text-center">
							<p style="margin-bottom:0px;" class="histori-text' . $nom . '">' . $tanggal . '</p>
							<p style="margin-bottom:0px;" class="histori-form' . $nom . ' hide">
								<input type="text" name="edit1[]" id="edit1_' . $nom . '" class="input-date" value="' . substr($data['tanggal'], 0, 10) . '" />
								<select name="edit2[]" id="edit2_' . $nom . '" style="height:28px; width:40px;">' . $opt_tgl . '</select> : 
								<select name="edit3[]" id="edit3_' . $nom . '" style="height:28px; width:40px;">' . $opt_jam . '</select>
							</p>
						</td>
						<td class="text-left">
							<p style="margin-bottom:0px;" class="histori-text' . $nom . '">' . $data['status'] . '</p>
							<div style="margin-bottom:0px;" class="histori-form' . $nom . ' hide">
								<div class="input-group">
									<input type="text" name="edit4[]" id="edit4_' . $nom . '" class="input-list" value="' . $data['status'] . '" />
									<div class="input-group-btn">
										<a data-idx="' . $nom . '" data-ids="' . $tmids . '" data-jns="' . $tipe . '" class="fa-simpan-sts btn btn-primary"><i class="fa fa-floppy-o"></i></a>
									</div>
								</div>
							</div>
						</td>
						<td class="text-center">
							' . (!$row1['is_delivered'] && !$row1['is_cancel'] && $nom > 1
					? '<a data-idx="' . $nom . '" class="fa-ubah-sts btn btn-info histori-text' . $nom . '"><i class="fa fa-edit"></i></a>' : '&nbsp;') . '
						</td>
					</tr>';
			}
		} else $isiTab .= '<tr><td colspan="4" class="text-center">Histori pengiriman belum ada</td></tr>';

		echo $isiTab;
		$con->close();
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
	}
}

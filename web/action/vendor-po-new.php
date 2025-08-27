<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "htmlawed", "mailgen");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;
$enk  	= decode($_SERVER['REQUEST_URI']);
$act	= ($enk['act'] ? $enk['act'] : htmlspecialchars($_POST["act"], ENT_QUOTES));
$idr 	= isset($_POST["idr"]) ? $_POST["idr"] : null;

$dt1	= htmlspecialchars($_POST["dt1"], ENT_QUOTES);
$dt2	= htmlspecialchars($_POST["dt2"], ENT_QUOTES);
$dt3	= htmlspecialchars($_POST["dt3"], ENT_QUOTES);
$dt4	= htmlspecialchars($_POST["dt4"], ENT_QUOTES);
$dt5	= htmlspecialchars($_POST["dt5"], ENT_QUOTES);
$dt6	= htmlspecialchars($_POST["dt6"], ENT_QUOTES);
$dt7	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["dt7"]), ENT_QUOTES);
$dt8	= htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt8"]), ENT_QUOTES);
$subTotal	= htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt9"]), ENT_QUOTES);
$dt10	= htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt10"]), ENT_QUOTES);
$ppn_11	= htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt11"]), ENT_QUOTES);
$pph_22	= htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt12"]), ENT_QUOTES);
$pbbkb	= htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt13"]), ENT_QUOTES);
$totalOrder	= htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt14"]), ENT_QUOTES);
$kategori_oa	= htmlspecialchars($_POST["kategori_oa"], ENT_QUOTES);
if ($kategori_oa == 1) {
	$ongkos_angkut = 0;
} else {
	$ongkos_angkut	= htmlspecialchars(str_replace(array(","), array("", ""), $_POST["ongkos_angkut"]), ENT_QUOTES);
}
$pbbkb_tawar	= htmlspecialchars(str_replace(array(","), array("", ""), $_POST["pbbkb_tawar"]), ENT_QUOTES);

$kd_tax		= htmlspecialchars($_POST["kd_tax"], ENT_QUOTES);
$terms		= htmlspecialchars($_POST["terms"], ENT_QUOTES);
$terms_day	= htmlspecialchars($_POST["terms_day"], ENT_QUOTES);
$ket	    = htmlspecialchars($_POST["ket"], ENT_QUOTES);

$cancel	    = htmlspecialchars($_POST["cancel"], ENT_QUOTES);
$tgl_close  = htmlspecialchars($_POST["tgl_close"], ENT_QUOTES);
$volume_close     = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["volume"]), ENT_QUOTES);
$kategori_plat	= htmlspecialchars($_POST["kategori_plat"], ENT_QUOTES);
$iuran_migas	= htmlspecialchars($_POST["iuran_migas"], ENT_QUOTES);
$nominal_iuran	= htmlspecialchars(str_replace(array(","), array("", ""), $_POST["nominal_iuran"]), ENT_QUOTES);

if ($iuran_migas == "") {
	$iuran = "0";
} else {
	$iuran = "1";
}




//echo $act; exit;
if ($act == 'cek') {
	echo json_encode(array("hasil" => true, "pesan" => ""));
	exit;
	$dt1 	= htmlspecialchars($_POST["q1"], ENT_QUOTES);
	$dt2 	= htmlspecialchars($_POST["q2"], ENT_QUOTES);
	$cek1 	= "select id_master from new_pro_inventory_vendor_po where nomor_po = '" . $dt2 . "'";
	$row1 	= $con->getRecord($cek1);

	if ($row1['id_master'] && $row1['id_master'] != $dt1) {
		echo json_encode(array("hasil" => false, "pesan" => "Nomor PO Sudah Ada..."));
		exit;
	} else {
		echo json_encode(array("hasil" => true, "pesan" => ""));
		exit;
	}
} else if ($act == 'add') {
	if ($dt1 == "" || $dt3 == "" || $dt5 == "" || $dt6 == "") {
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else {
		$kuenya = "select LPAD(cast((select nextval(new_pro_inventory_vendor_po_seq)) as varchar(10)), 9, '0') as idnya";
		$arrkue = $con->getRecord($kuenya);
		$id1nya = date("Ym") . $arrkue['idnya'];

		$sql01 = "
		select coalesce(max(cast(substr(a.nomor_po, 1, 3) as integer)), 0) as nomor, 
			c.inisial_cabang, 
			d.inisial_vendor  
			from new_pro_inventory_vendor_po a  
			join pro_master_terminal b on a.id_terminal = b.id_master 
			join pro_master_cabang c on b.id_cabang = c.id_master 
			join pro_master_vendor d on a.id_vendor = d.id_master 
			where d.id_master = '" . $dt5 . "' 
			and c.id_master = (select id_cabang from pro_master_terminal where id_master = '" . $dt6 . "')
			and year(a.tanggal_inven) = '" . substr($dt1, 6, 4) . "'
			";
		$arrNom = $con->getRecord($sql01);
		$arrRom = array(1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
		$blnThn = $arrRom[intval(substr($dt1, 3, 2))] . '/' . substr($dt1, 8, 2);
		$dt2 	= str_pad(($arrNom['nomor'] + 1), 3, '0', STR_PAD_LEFT) . '/' . strtoupper($arrNom['inisial_vendor']) . '/' . strtoupper($arrNom['inisial_cabang']) . '/' . $blnThn;




		if ($id1nya) {
			$oke = true;
			$con->beginTransaction();
			$con->clearError();

			$msg = "BERHASIL_MASUK";
			$ems1 = "select email_user from acl_user where id_role = 4";
			$sql = "
					insert into new_pro_inventory_vendor_po(id_master, id_vendor, id_produk, id_terminal, nomor_po, tanggal_inven, volume_po, harga_tebus, kategori_oa, ongkos_angkut, kategori_plat, iuran_migas, nominal_migas, kd_tax, subtotal, ppn_11, pph_22, nilai_pbbkb, pbbkb, total_order,  terms, terms_day, keterangan,
					created_time, created_ip, created_by, disposisi_po) values ('" . $id1nya . "', '" . $dt5 . "', '" . $dt3 . "', '" . $dt6 . "', '" . $dt2 . "', '" . tgl_db($dt1) . "', '" . $dt10 . "', '" . $dt8 . "', '" . $kategori_oa . "', '" . $ongkos_angkut . "', '" . $kategori_plat . "', '" . $iuran . "', '" . $nominal_iuran . "', '" . $kd_tax . "', '" . $subTotal . "', '" . $ppn_11 . "', '" . $pph_22 . "', '" . $pbbkb_tawar . "', '" . $pbbkb . "', '" . $totalOrder . "', '" . $terms . "', '" . $terms_day . "', '" . $ket . "', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "', 1)";
			$con->setQuery($sql);
			$oke  = $oke && !$con->hasError();

			$sbjk = "Persetujuan PO Supplier[" . date('d/m/Y H:i:s') . "]";
			$pesn = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " meminta persetujuan untuk PO supplier";
			$pesn .= "<p>" . BASE_SERVER . "</p>";

			if ($oke) {

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
					$mail->Subject = $sbjk;
					$mail->msgHTML($pesn);
					$mail->send();
				}

				$con->commit();
				$con->close();
				header("location: " . BASE_URL_CLIENT . "/vendor-po-new.php");
				exit();
			} else {
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", $msg, BASE_REFERER);
			}
		}
	}
} else if ($act == 'update') {
	if ($dt1 == "" || $dt8 == "" || $dt10 == "") {
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else {
		$id1nya = $idr;

		if ($id1nya) {
			$oke = true;
			$con->beginTransaction();
			$con->clearError();

			$msg = "GAGAL_UBAH";
			$sql = "
					update new_pro_inventory_vendor_po set harga_tebus = '" . $dt8 . "', tanggal_inven = '" .   tgl_db($dt1) . "', disposisi_po = 1, cfo_result = 0, ceo_result = 0, revert_cfo = 0, revert_ceo = 0, volume_po = '" . $dt10 . "', kategori_oa = '" . $kategori_oa . "', ongkos_angkut = '" . $ongkos_angkut . "', kategori_plat = '" . $kategori_plat . "', iuran_migas = '" . $iuran . "', nominal_migas = '" . $nominal_iuran . "', kd_tax = '" . $kd_tax . "', subtotal = '" . $subTotal . "',  subtotal = '" . $subTotal . "', ppn_11 = '" . $ppn_11 . "', pph_22 = '" . $pph_22 . "', nilai_pbbkb = '" . $pbbkb_tawar . "', pbbkb = '" . $pbbkb . "', total_order = '" . $totalOrder . "',
					terms = '" . $terms . "', terms_day = '" . $terms_day . "', keterangan =  '" . $ket . "',
					lastupdate_time = NOW(), lastupdate_ip = '" . $_SERVER['REMOTE_ADDR'] . "', lastupdate_by = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "' 
					where id_master = '" . $idr . "'
				";
			$con->setQuery($sql);
			$oke  = $oke && !$con->hasError();

			if ($oke) {
				$con->commit();
				$con->close();
				header("location: " . BASE_URL_CLIENT . "/vendor-po-new.php");
				exit();
			} else {
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", $msg, BASE_REFERER);
			}
		}
	}
} else if ($act == 'hapus') {
	$param 	= htmlspecialchars(paramDecrypt($_POST["param"]), ENT_QUOTES);
	$post 	= explode("#|#", $param);
	$file	= isset($post[0]) ? htmlspecialchars($post[0], ENT_QUOTES) : null;
	$id1	= isset($post[1]) ? htmlspecialchars($post[1], ENT_QUOTES) : null;
	$id2	= isset($post[2]) ? htmlspecialchars($post[2], ENT_QUOTES) : null;
	$id3	= isset($post[3]) ? htmlspecialchars($post[3], ENT_QUOTES) : null;
	$id4	= isset($post[4]) ? htmlspecialchars($post[4], ENT_QUOTES) : null;

	$cek = "select id_po_supplier from new_pro_inventory_vendor_po_receive where id_po_supplier = '" . $id1 . "'";
	$row = $con->getRecord($cek);

	if (!$row['id_po_supplier']) {
		$sql = "delete from new_pro_inventory_vendor_po where id_master = '" . $id1 . "'";
		$con->setQuery($sql);

		if (!$con->hasError()) {
			$con->close();
			$arr["error"] = "";
		} else {
			$con->clearError();
			$con->close();
			$arr["error"] = "Maaf Data tidak dapat dihapus..";
		}
	} else {
		$con->close();
		$arr["error"] = "Maaf, data tidak dapat dihapus, karena sudah terdapat data inventory";
	}

	echo json_encode($arr);
	exit;
} else if ($act == 'cancel') {
	if ($cancel == "") {
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else {
		$id1nya = $idr;

		if ($id1nya) {
			$oke = true;
			$con->beginTransaction();
			$con->clearError();

			$msg = "GAGAL_UBAH";
			$sql = "
					update new_pro_inventory_vendor_po set is_cancel = 1, keterangan_cancel = '" . $cancel . "' 
					where id_master = '" . $idr . "'
				";
			$con->setQuery($sql);
			$oke  = $oke && !$con->hasError();

			if ($oke) {
				$con->commit();
				$con->close();
				header("location: " . BASE_URL_CLIENT . "/vendor-po-new.php");
				exit();
			} else {
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", $msg, BASE_REFERER);
			}
		}
	}
} else if ($act == 'close') {
	if ($tgl_close == "") {
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else {
		$id1nya = $idr;

		if ($id1nya) {
			$oke = true;
			$con->beginTransaction();
			$con->clearError();

			$msg = "GAGAL_UBAH";
			$sql = "
					update new_pro_inventory_vendor_po set is_close = 1, tanggal_close = '" . tgl_db($tgl_close) . "',  volume_close = '" . $volume_close . "' 
					where id_master = '" . $idr . "'
				";
			$con->setQuery($sql);
			$oke  = $oke && !$con->hasError();

			if ($oke) {
				$con->commit();
				$con->close();
				header("location: " . BASE_URL_CLIENT . "/vendor-po-new.php");
				exit();
			} else {
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", $msg, BASE_REFERER);
			}
		}
	}
}

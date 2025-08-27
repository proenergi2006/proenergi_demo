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
$act	= !isset($enk['act']) ? (isset($_POST["act"]) ? htmlspecialchars($_POST["act"], ENT_QUOTES) : null) : $enk['act'];
$idr	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
$idk	= htmlspecialchars($_POST["idk"], ENT_QUOTES);

$alamat_lokasi		= htmlspecialchars($_POST["alamat_lokasi"], ENT_QUOTES);
$prov_lokasi		= htmlspecialchars($_POST["prov_lokasi"], ENT_QUOTES);
$kab_lokasi 		= htmlspecialchars($_POST["kab_lokasi"], ENT_QUOTES);
$id_wil_oa			= isset($_POST["id_wil_oa"]) ? htmlspecialchars($_POST["id_wil_oa"], ENT_QUOTES) : 0;
$telp_lokasi		= htmlspecialchars($_POST["telp_lokasi"], ENT_QUOTES);
$fax_lokasi			= htmlspecialchars($_POST["fax_lokasi"], ENT_QUOTES);
$tgl_survey 		= htmlspecialchars($_POST["tgl_survey"], ENT_QUOTES);
$review_lokasi		= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["review_lokasi"], ENT_QUOTES));
$jenis_usaha		= htmlspecialchars($_POST["jenis_usaha"], ENT_QUOTES);
$website_lokasi		= htmlspecialchars($_POST["website_lokasi"], ENT_QUOTES);
$alat_ukur			= htmlspecialchars($_POST["alat_ukur"], ENT_QUOTES);
$toleransi 			= htmlspecialchars($_POST["toleransi"], ENT_QUOTES);
$jam_operasional1	= htmlspecialchars($_POST["jam_operasional1"], ENT_QUOTES);
$jam_operasional2	= htmlspecialchars($_POST["jam_operasional2"], ENT_QUOTES);
$jam_operasional3	= htmlspecialchars($_POST["jam_operasional3"], ENT_QUOTES);
$latitude			= htmlspecialchars($_POST["latitude"], ENT_QUOTES);
$longitude			= htmlspecialchars($_POST["longitude"], ENT_QUOTES);
$link_google_maps	= htmlspecialchars($_POST["link_google_maps"], ENT_QUOTES);
$jarak_depot		= htmlspecialchars($_POST["jarak_depot"], ENT_QUOTES);
$rute_lokasi		= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["rute_lokasi"], ENT_QUOTES));
$note_lokasi		= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["note_lokasi"], ENT_QUOTES));
$max_truk			= htmlspecialchars($_POST["max_truk"], ENT_QUOTES);
$lsm_portal			= htmlspecialchars($_POST["lsm_portal"], ENT_QUOTES);
$min_vol_kirim		= htmlspecialchars($_POST["min_vol_kirim"], ENT_QUOTES);
$penjelasan_bongkar	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["penjelasan_bongkar"], ENT_QUOTES));
$catatan_tangki		= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["catatan_tangki"], ENT_QUOTES));
$catatan_kapal		= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["catatan_kapal"], ENT_QUOTES));
$wilayah			= $con->getOne("select id_wilayah from pro_customer where id_customer = '" . $idr . "'");
$rever				= isset($_POST["rever"]) ? htmlspecialchars($_POST["rever"], ENT_QUOTES) : null;
$forceEdit			= htmlspecialchars($_POST["forceEdit"], ENT_QUOTES);

$arrSurveyor = array();
foreach ($_POST["surveyor"] as $idxA => $data1) {
	array_push($arrSurveyor, htmlspecialchars($data1, ENT_QUOTES));
}
$arrHasil = array();
foreach ($_POST["hasilsurv"] as $idxA => $data5) {
	array_push($arrHasil, htmlspecialchars($data5, ENT_QUOTES));
}
$arrProdukvol = array();
foreach ($_POST["produk"] as $idxB => $data2) {
	$tangki1 = htmlspecialchars($_POST["produk"][$idxB], ENT_QUOTES);
	$tangki2 = htmlspecialchars($_POST["volbul"][$idxB], ENT_QUOTES);
	$arrProdukvol[] = array("produk" => $tangki1, "volbul" => $tangki2);
}
$arrKompetitor = array();
foreach ($_POST["kompetitor"] as $idxC => $data3) {
	array_push($arrKompetitor, htmlspecialchars($data3, ENT_QUOTES));
}
$arrPIC = array();
foreach ($_POST["namacus"] as $idxD => $data4) {
	$tangki1 = htmlspecialchars($_POST["namacus"][$idxD], ENT_QUOTES);
	$tangki2 = htmlspecialchars($_POST["posisicus"][$idxD], ENT_QUOTES);
	$tangki3 = htmlspecialchars($_POST["telpcus"][$idxD], ENT_QUOTES);
	$arrPIC[] = array("nama" => $tangki1, "posisi" => $tangki2, "telepon" => $tangki3);
}
$arrTangki = array();
foreach ($_POST["tangki"]["tipe"] as $idx1 => $val1) {
	$tangki1 = htmlspecialchars($_POST["tangki"]["tipe"][$idx1], ENT_QUOTES);
	$tangki2 = htmlspecialchars($_POST["tangki"]["kapasitas"][$idx1], ENT_QUOTES);
	$tangki3 = htmlspecialchars($_POST["tangki"]["jumlah"][$idx1], ENT_QUOTES);
	$tangki4 = htmlspecialchars($_POST["tangki"]["produk"][$idx1], ENT_QUOTES);
	$tangki5 = htmlspecialchars($_POST["tangki"]["inlet"][$idx1], ENT_QUOTES);
	$tangki6 = htmlspecialchars($_POST["tangki"]["ukuran"][$idx1], ENT_QUOTES);
	$arrTangki[] = array("tipe" => $tangki1, "kapasitas" => $tangki2, "jumlah" => $tangki3, "produk" => $tangki4, "inlet" => $tangki5, "ukuran" => $tangki6);
}
$arrSupport = array();
foreach ($_POST["support"]["pompa"] as $idx1 => $val1) {
	$tangki1 = htmlspecialchars($_POST["support"]["pompa"][$idx1], ENT_QUOTES);
	$tangki2 = htmlspecialchars($_POST["support"]["aliran"][$idx1], ENT_QUOTES);
	$tangki3 = htmlspecialchars($_POST["support"]["selang"][$idx1], ENT_QUOTES);
	$tangki4 = htmlspecialchars($_POST["support"]["valve"][$idx1], ENT_QUOTES);
	$tangki5 = htmlspecialchars($_POST["support"]["grounding"][$idx1], ENT_QUOTES);
	$tangki6 = htmlspecialchars($_POST["support"]["sinyal"][$idx1], ENT_QUOTES);
	$arrSupport[] = array("pompa" => $tangki1, "aliran" => $tangki2, "selang" => $tangki3, "valve" => $tangki4, "ground" => $tangki5, "sinyal" => $tangki6);
}
$arrKuantitasTangki = array();
foreach ($_POST["kuantitas1"]["alat"] as $idx1 => $val1) {
	$tangki1 = htmlspecialchars($_POST["kuantitas1"]["alat"][$idx1], ENT_QUOTES);
	$tangki2 = htmlspecialchars($_POST["kuantitas1"]["merk"][$idx1], ENT_QUOTES);
	$tangki3 = htmlspecialchars($_POST["kuantitas1"]["tera"][$idx1], ENT_QUOTES);
	$tangki4 = htmlspecialchars($_POST["kuantitas1"]["masa"][$idx1], ENT_QUOTES);
	$tangki5 = htmlspecialchars($_POST["kuantitas1"]["flowmeter"][$idx1], ENT_QUOTES);
	$arrKuantitasTangki[] = array("alat" => $tangki1, "merk" => $tangki2, "tera" => $tangki3, "masa" => $tangki4, "flowmeter" => $tangki5);
}
$arrKualitasTangki = array();
foreach ($_POST["kualitas1"]["spec"] as $idx1 => $val1) {
	$tangki1 = htmlspecialchars($_POST["kualitas1"]["spec"][$idx1], ENT_QUOTES);
	$tangki2 = htmlspecialchars($_POST["kualitas1"]["lab"][$idx1], ENT_QUOTES);
	$tangki3 = htmlspecialchars($_POST["kualitas1"]["coq"][$idx1], ENT_QUOTES);
	$arrKualitasTangki[] = array("spec" => $tangki1, "lab" => $tangki2, "coq" => $tangki3);
}
$arrKapal = array();
foreach ($_POST["kapal"]["tipe"] as $idx1 => $val1) {
	$tangki1 = htmlspecialchars($_POST["kapal"]["tipe"][$idx1], ENT_QUOTES);
	$tangki2 = htmlspecialchars($_POST["kapal"]["kapasitas"][$idx1], ENT_QUOTES);
	$tangki3 = htmlspecialchars($_POST["kapal"]["jumlah"][$idx1], ENT_QUOTES);
	$tangki4 = htmlspecialchars($_POST["kapal"]["metode"][$idx1], ENT_QUOTES);
	$tangki5 = htmlspecialchars($_POST["kapal"]["inlet"][$idx1], ENT_QUOTES);
	$tangki6 = htmlspecialchars($_POST["kapal"]["ukuran"][$idx1], ENT_QUOTES);
	$arrKapal[] = array("tipe" => $tangki1, "kapasitas" => $tangki2, "jumlah" => $tangki3, "metode" => $tangki4, "inlet" => $tangki5, "ukuran" => $tangki6);
}
$arrJetty = array();
foreach ($_POST["jetty"]["loa"] as $idx1 => $val1) {
	$tangki1 = htmlspecialchars($_POST["jetty"]["loa"][$idx1], ENT_QUOTES);
	$tangki2 = htmlspecialchars($_POST["jetty"]["pbl"][$idx1], ENT_QUOTES);
	$tangki3 = htmlspecialchars($_POST["jetty"]["lws"][$idx1], ENT_QUOTES);
	$tangki4 = htmlspecialchars($_POST["jetty"]["sandar"][$idx1], ENT_QUOTES);
	$tangki5 = htmlspecialchars($_POST["jetty"]["izin"][$idx1], ENT_QUOTES);
	$tangki6 = htmlspecialchars($_POST["jetty"]["syarat"][$idx1], ENT_QUOTES);
	$arrJetty[] = array("loa" => $tangki1, "pbl" => $tangki2, "lws" => $tangki3, "sandar" => $tangki4, "izin" => $tangki5, "syarat" => $tangki6);
}
$arrKuantitasKapal = array();
foreach ($_POST["kuantitas2"]["alat"] as $idx1 => $val1) {
	$tangki1 = htmlspecialchars($_POST["kuantitas2"]["alat"][$idx1], ENT_QUOTES);
	$tangki2 = htmlspecialchars($_POST["kuantitas2"]["merk"][$idx1], ENT_QUOTES);
	$tangki3 = htmlspecialchars($_POST["kuantitas2"]["tera"][$idx1], ENT_QUOTES);
	$tangki4 = htmlspecialchars($_POST["kuantitas2"]["masa"][$idx1], ENT_QUOTES);
	$tangki5 = htmlspecialchars($_POST["kuantitas2"]["flowmeter"][$idx1], ENT_QUOTES);

	$arrKuantitasKapal[] = array("alat" => $tangki1, "merk" => $tangki2, "tera" => $tangki3, "masa" => $tangki4, "flowmeter" => $tangki5);
}
$arrKualitasKapal = array();
foreach ($_POST["kualitas2"]["spec"] as $idx1 => $val1) {
	$tangki1 = htmlspecialchars($_POST["kualitas2"]["spec"][$idx1], ENT_QUOTES);
	$tangki2 = htmlspecialchars($_POST["kualitas2"]["lab"][$idx1], ENT_QUOTES);
	$tangki3 = htmlspecialchars($_POST["kualitas2"]["coq"][$idx1], ENT_QUOTES);
	$arrKualitasKapal[] = array("spec" => $tangki1, "lab" => $tangki2, "coq" => $tangki3);
}

if ($act == "add") {
	if ($tgl_survey == "" || $alamat_lokasi == "" || $prov_lokasi == "" || $kab_lokasi == "") {
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else {
		$oke = true;
		$con->beginTransaction();
		$con->clearError();
		$sql1 = "insert into pro_customer_lcr(id_customer, id_wilayah, alamat_survey, prov_survey, kab_survey, telp_survey, tgl_survey, nama_surveyor, review, jenis_usaha, website, produkvol, picustomer, alat_ukur, toleransi, kompetitor, jam_operasional, latitude_lokasi, longitude_lokasi, link_google_maps, jarak_depot, max_truk, lsm_portal, min_vol_kirim, rute_lokasi, note_lokasi, tangki, pendukung, quantity_tangki, quality_tangki, catatan_tangki, kapal, jetty, quantity_kapal, quality_kapal, catatan_kapal, penjelasan_bongkar, fax_survey, hasilsurv, created_time, created_ip, created_by) values ('" . $idr . "', '" . $wilayah . "', '" . $alamat_lokasi . "', '" . $prov_lokasi . "', '" . $kab_lokasi . "', '" . $telp_lokasi . "', '" . tgl_db($tgl_survey) . "', '" . json_encode($arrSurveyor) . "', '" . $review_lokasi . "', '" . $jenis_usaha . "', '" . $website_lokasi . "', '" . json_encode($arrProdukvol) . "', '" . json_encode($arrPIC) . "', '" . $alat_ukur . "', '" . $toleransi . "', '" . json_encode($arrKompetitor) . "', '" . json_encode(array($jam_operasional1, $jam_operasional2, $jam_operasional3)) . "', '" . $latitude . "', '" . $longitude . "', '" . $link_google_maps . "', '" . $jarak_depot . "', '" . $max_truk . "', '" . $lsm_portal . "', '" . $min_vol_kirim . "', '" . $rute_lokasi . "', '" . $note_lokasi . "', '" . json_encode($arrTangki) . "', '" . json_encode($arrSupport) . "', '" . json_encode($arrKuantitasTangki) . "', '" . json_encode($arrKualitasTangki) . "', '" . $catatan_tangki . "', '" . json_encode($arrKapal) . "', '" . json_encode($arrJetty) . "', '" . json_encode($arrKuantitasKapal) . "', '" . json_encode($arrKualitasKapal) . "', '" . $catatan_kapal . "', '" . $penjelasan_bongkar . "', '" . $fax_lokasi . "', '" . json_encode($arrHasil) . "', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "')";
		$res1 = $con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();
		if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 11 || paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 17 || paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 18) {
			$url = BASE_URL_CLIENT . "/lcr-add.php?" . paramEncrypt("idr=" . $idr . "&idk=" . $res1);
			$msg = "Silahkan upload gambar-gambar pendukung lcr";
		} else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 9) {
			$url = BASE_URL_CLIENT . "/lcr-detail.php?" . paramEncrypt("idr=" . $idr . "&idk=" . $res1);
			$msg = "Data behasil disimpan";
		}

		if ($oke) {
			$con->commit();
			$con->close();
			$flash->add("success", $msg, $url);
		} else {
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
	}
} else if ($act == "update") {
	if ($tgl_survey == "" || $alamat_lokasi == "" || $prov_lokasi == "" || $kab_lokasi == "") {
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else {
		$oke = true;
		$con->beginTransaction();
		$con->clearError();
		$force_edit_marketing = ($forceEdit != "" && $forceEdit == 1) ? ", sm_result = 0, flag_disposisi = 0, flag_approval = 0, logistik_result = 0 " : "";
		$verifikasi = ($rever != "" && $rever == 1) ? ", sm_result = 0, flag_disposisi = 2, flag_approval = 0" : "";
		$sql1 = "update pro_customer_lcr set alamat_survey = '" . $alamat_lokasi . "', prov_survey = '" . $prov_lokasi . "', kab_survey = '" . $kab_lokasi . "', telp_survey = '" . $telp_lokasi . "', tgl_survey = '" . tgl_db($tgl_survey) . "', nama_surveyor = '" . json_encode($arrSurveyor) . "', review = '" . $review_lokasi . "', jenis_usaha = '" . $jenis_usaha . "', website = '" . $website_lokasi . "', produkvol = '" . json_encode($arrProdukvol) . "', picustomer = '" . json_encode($arrPIC) . "', alat_ukur = '" . $alat_ukur . "', toleransi = '" . $toleransi . "', kompetitor = '" . json_encode($arrKompetitor) . "', jam_operasional = '" . json_encode(array($jam_operasional1, $jam_operasional2, $jam_operasional3)) . "', latitude_lokasi = '" . $latitude . "', longitude_lokasi = '" . $longitude . "', link_google_maps = '" . $link_google_maps . "', jarak_depot = '" . $jarak_depot . "', max_truk = '" . $max_truk . "', lsm_portal = '" . $lsm_portal . "', min_vol_kirim = '" . $min_vol_kirim . "', rute_lokasi = '" . $rute_lokasi . "', note_lokasi = '" . $note_lokasi . "', tangki = '" . json_encode($arrTangki) . "', pendukung = '" . json_encode($arrSupport) . "', quantity_tangki = '" . json_encode($arrKuantitasTangki) . "', quality_tangki = '" . json_encode($arrKualitasTangki) . "', catatan_tangki = '" . $catatan_tangki . "', kapal = '" . json_encode($arrKapal) . "', jetty = '" . json_encode($arrJetty) . "', quantity_kapal = '" . json_encode($arrKuantitasKapal) . "', quality_kapal = '" . json_encode($arrKualitasKapal) . "', catatan_kapal = '" . $catatan_kapal . "', penjelasan_bongkar = '" . $penjelasan_bongkar . "', id_wil_oa = '" . $id_wil_oa . "', fax_survey = '" . $fax_lokasi . "', hasilsurv = '" . json_encode($arrHasil) . "' " . $verifikasi . " " . $force_edit_marketing . " where id_lcr = '" . $idk . "'";
		$res1 = $con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();

		if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 11 || paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 17 || paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 18)
			$url  = BASE_URL_CLIENT . "/lcr-detail.php?" . paramEncrypt("idr=" . $idr . "&idk=" . $idk);
		else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 9)
			$url  = BASE_URL_CLIENT . "/verifikasi-lcr-detail.php?" . paramEncrypt("idr=" . $idr . "&idk=" . $idk);
		$msg = "Data behasil diupdate";

		if ($oke) {
			$con->commit();
			$con->close();
			$flash->add("success", $msg, $url);
			// header("location: ".$url);	
			exit();
		} else {
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
	}
}

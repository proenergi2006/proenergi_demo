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
$act	= !isset($enk['act']) ? htmlspecialchars($_POST["act"], ENT_QUOTES) : $enk['act'];
$idr	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
$idk	= htmlspecialchars($_POST["idk"], ENT_QUOTES);
$cabang		= htmlspecialchars($_POST["cabang"], ENT_QUOTES);
$area		= htmlspecialchars($_POST["area"], ENT_QUOTES);
$diupdate 	= isset($_POST["diupdate"]) ? htmlspecialchars($_POST["diupdate"], ENT_QUOTES) : 0;
$gelar		= htmlspecialchars($_POST["gelar"], ENT_QUOTES);
$nama_up	= htmlspecialchars($_POST["nama_up"], ENT_QUOTES);
$jabatan_up	= htmlspecialchars($_POST["jabatan_up"], ENT_QUOTES);
$alamat_up	= htmlspecialchars($_POST["alamat_up"], ENT_QUOTES);
$telp_up	= htmlspecialchars($_POST["telp_up"], ENT_QUOTES);
$fax_up		= htmlspecialchars($_POST["fax_up"], ENT_QUOTES);
$produk		= htmlspecialchars($_POST["produk_tawar"], ENT_QUOTES);
$volume		= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["volume"]), ENT_QUOTES);
$top		= htmlspecialchars($_POST["top"], ENT_QUOTES);
$order		= htmlspecialchars($_POST["order_method"], ENT_QUOTES);
$refund 	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["refund"]), ENT_QUOTES);
$oa_kirim 	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["oa_kirim"]), ENT_QUOTES);
$other_cost = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["other_cost"]), ENT_QUOTES);
$tol_susut	= htmlspecialchars($_POST["tol_susut"], ENT_QUOTES);
$lok_kirim	= htmlspecialchars($_POST["lok_kirim"], ENT_QUOTES);
$masa_awal	= htmlspecialchars($_POST["masa_awal"], ENT_QUOTES);
$masa_akhir	= htmlspecialchars($_POST["masa_akhir"], ENT_QUOTES);
$masa_awal_tier = htmlspecialchars($_POST["masa_awal"], ENT_QUOTES);
$masa_akhir_tier = htmlspecialchars($_POST["masa_akhir"], ENT_QUOTES);
$pbbkb		= htmlspecialchars($_POST["pbbkb_tawar"], ENT_QUOTES);
$flagHitung	= htmlspecialchars($_POST["perhitungan"], ENT_QUOTES);
$hargaDasar = htmlspecialchars(str_replace(array(","), array(""), $_POST["harga_dasar"]), ENT_QUOTES);
$is_rinci 	= isset($_POST["is_rinci"]) ? htmlspecialchars($_POST["is_rinci"], ENT_QUOTES) : 0;
if ($is_rinci == '') $is_rinci = 0;
$reflag 	= htmlspecialchars($_POST["reflag"], ENT_QUOTES);
$ket_harga 	= htmlspecialchars($_POST["ket_harga"], ENT_QUOTES);
$jenis_net 	= isset($_POST["jenis_net"]) ? htmlspecialchars($_POST["jenis_net"], ENT_QUOTES) : 0;
if ($jenis_net == '') $jenis_net = 0;
$catatan 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["catatan"], ENT_QUOTES));

$jns_waktu	= ($_POST["jenis_waktu"] ? htmlspecialchars($_POST["jenis_waktu"], ENT_QUOTES) : htmlspecialchars($_POST["jenis_payment"], ENT_QUOTES));
$top		= ($jns_waktu == "CREDIT" ? $top : 0);
$user_pic	= paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']);
$user_ip	= $_SERVER['REMOTE_ADDR'];
$rincian 	= array();
$formula 	= array();
$harga_asli = 0;

$term_condition = str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["term_condition"], ENT_QUOTES));
$metode 		= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["metode"], ENT_QUOTES));

$kalkulasi_oa 	= array();
$transportir 	= htmlspecialchars($_POST["cb_transportir"], ENT_QUOTES);
$wiloa_po 		= htmlspecialchars($_POST["wiloa_po"], ENT_QUOTES);
$voloa_po 		= htmlspecialchars($_POST["voloa_po"], ENT_QUOTES);
$ongoa_po 		= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["ongoa_po"]), ENT_QUOTES);
array_push($kalkulasi_oa, array("transportir" => $transportir, "wiloa_po" => $wiloa_po, "voloa_po" => $voloa_po, "ongoa_po" => $ongoa_po));

$pembulatan 	= htmlspecialchars($_POST["pembulatan"], ENT_QUOTES);
$gabung_oa 		= htmlspecialchars($_POST["gabung_oa"], ENT_QUOTES);
$all_in 		= htmlspecialchars($_POST["all_in"], ENT_QUOTES);
$gabung_pbbkb		= htmlspecialchars($_POST["gabung_pbbkb"], ENT_QUOTES);
$gabung_pbbkboa		= htmlspecialchars($_POST["gabung_pbbkboa"], ENT_QUOTES);

//develope iwan*
$harga_tier 	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["totnyatier"]), ENT_QUOTES);
$refund 	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["refund"]), ENT_QUOTES);
//end

$pembulatan 	= ($pembulatan) ? $pembulatan : 0;
$gabung_oa 		= ($gabung_oa) ? $gabung_oa : 0;
$all_in 		= ($all_in) ? $all_in : 0;
$gabung_pbbkb		= ($gabung_pbbkb) ? $gabung_pbbkb : 0;
$gabung_pbbkboa		= ($gabung_pbbkboa) ? $gabung_pbbkboa : 0;

$refund 	= ($refund ? $refund : 0);
$oa_kirim 	= ($oa_kirim ? $oa_kirim : 0);
$other_cost = ($other_cost ? $other_cost : 0);

if (!is_array_empty($_POST['jnsHarga'])) {
	foreach ($_POST['jnsHarga'] as $idx => $val) {
		$cetak 	= 1;
		$jenis 	= htmlspecialchars($_POST['jnsHarga'][$idx], ENT_QUOTES);
		$nilai 	= ($idx < 2) ? 0 : htmlspecialchars($_POST['clcHarga'][$idx], ENT_QUOTES);
		if ($pembulatan == 1) {
			$biaya	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['rncHarga'][$idx]), ENT_QUOTES);
		} else {
			$biaya	= htmlspecialchars(str_replace(array(","), array(""), $_POST['rncHarga'][$idx]), ENT_QUOTES);
		}
		if ($jenis != "") {
			array_push($rincian, array("rinci" => $cetak, "rincian" => $jenis, "nilai" => $nilai, "biaya" => $biaya));
		}
		if ($idx == 0) $harga_asli = $biaya;
	}
}
if (!is_array_empty($_POST['jnsfor']) && $flagHitung == 2) {
	foreach ($_POST['jnsfor'] as $idx => $val) {
		$jenis = htmlspecialchars($val, ENT_QUOTES);
		if ($jenis) {
			array_push($formula, $jenis);
		}
	}
}

//develope iwan*

$ongkosAngkut = 0; // Default jika tidak ditemukan

foreach ($rincian as $item) {
	if ($item['rincian'] === "Ongkos Angkut") {
		$ongkosAngkut = $item['biaya'];
	}
}

//END


if ($act == "add") {
	if ($idr == "" || $gelar == "" || $nama_up == "" || $masa_awal == "" || $masa_akhir == "" || $area == "" || $volume == "" || $flagHitung == "" || $produk == "" || $order == "" || $pbbkb == "") {
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else {
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		//develope Iwan* cek masuk tier berapa penawaran
		$sql1 = "select harga_sm, harga_om, harga_coo from pro_master_harga_minyak where id_area = '" . $area . "' AND periode_awal = '" . tgl_db($masa_awal_tier) . "' AND periode_akhir = '" . tgl_db($masa_akhir_tier) . "'";
		$row4 = $con->getRecord($sql1);

		$harga_sm = $row4['harga_sm'];
		$harga_om = $row4['harga_om'];
		$harga_coo = $row4['harga_coo'];

		if ($harga_tier > $harga_sm) {
			$tier = 'I';
		} elseif ($harga_tier >= $harga_om && $harga_tier < $harga_sm) {
			$tier = 'II';
		} elseif ($harga_tier >= $harga_coo && $harga_tier < $harga_om) {
			$tier = 'III';
		} else {
			$tier = 'III';
		}

		//end 

		$cek1 = "select id_group_cabang, inisial_cabang, urut_penawaran from pro_master_cabang where id_master = '" . $cabang . "' for update";
		$row1 = $con->getRecord($cek1);
		$tmp1 = $row1['urut_penawaran'] + 1;
		$tmp2 = array("1" => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
		$noms = str_pad($tmp1, 4, '0', STR_PAD_LEFT) . '/PE-PN/' . $row1['inisial_cabang'] . '/220/' . $tmp2[intval(date("m"))] . '/' . date("Y");
		$grcb = $row1['id_group_cabang'];

		$cek2 = "select id_wilayah, id_group from pro_customer where id_customer = '" . $idr . "'";
		$row2 = $con->getRecord($cek2);

		$sql1 = "insert ignore into pro_penawaran(id_customer, nomor_surat, id_cabang, id_group, id_area, gelar, nama_up, jabatan_up, alamat_up, telp_up, fax_up, jenis_payment, jenis_net, 
					jangka_waktu, masa_awal, masa_akhir, volume_tawar, perhitungan, harga_dasar, is_rinci, detail_rincian, detail_formula, produk_tawar, pbbkb_tawar, method_order, 
					catatan, refund_tawar, ket_harga, harga_asli, oa_kirim, other_cost, tol_susut, lok_kirim, created_time, created_ip, created_by, term_condition, metode, 
					kalkulasi_oa, pembulatan, gabung_oa, all_in, gabung_pbbkb, gabung_pbbkboa, harga_tier, tier) 
					values ('" . $idr . "', '" . $noms . "', '" . $cabang . "', '" . $grcb . "', '" . $area . "', 
					'" . $gelar . "', '" . $nama_up . "', '" . $jabatan_up . "', '" . $alamat_up . "', '" . $telp_up . "', '" . $fax_up . "', '" . $jns_waktu . "', '" . $jenis_net . "', '" . $top . "', '" . tgl_db($masa_awal) . "', 
					'" . tgl_db($masa_akhir) . "', '" . $volume . "', '" . $flagHitung . "', '" . $hargaDasar . "', '" . $is_rinci . "', '" . json_encode($rincian) . "', '" . json_encode($formula) . "', 
					'" . $produk . "', '" . $pbbkb . "', '" . $order . "', '" . $catatan . "', '" . $refund . "', '" . $ket_harga . "', '" . $harga_asli . "', '" . $oa_kirim . "', '" . $other_cost . "', '" . $tol_susut . "', 
					'" . $lok_kirim . "', NOW(), '" . $user_ip . "', '" . $user_pic . "', '" . $term_condition . "', '" . $metode . "', '" . json_encode($kalkulasi_oa) . "', '" . $pembulatan . "', '" . $gabung_oa . "', '" . $all_in . "', '" . $gabung_pbbkb . "', '" . $gabung_pbbkboa . "', '" . $harga_tier . "', '" . $tier . "')";
		// print_r($sql1);
		// exit();
		$res1 = $con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();

		//develope iwan* cek insert id terakhir
		$cek_id = "select LAST_INSERT_ID() as id";
		$row_id = $con->getRecord($cek_id);
		$id_penawaran = $row_id['id'];
		// //end 

		// // develope iwan* Simpan other cost ke tabel pro_other_cost_detail
		if (!empty($_POST['other_cost_keterangan']) && !empty($_POST['nominal'])) {
			foreach ($_POST['other_cost_keterangan'] as $index => $keterangan) {
				$nominal = htmlspecialchars($_POST['nominal'][$index], ENT_QUOTES);

				// Validasi data
				if (!empty($keterangan) && !empty($nominal)) {
					$sqlOtherCost = "insert pro_other_cost_detail (id_penawaran, keterangan, nominal) 
		                     VALUES ('" . $id_penawaran . "','" . $keterangan . "', '" . $nominal . "')";
					$resOtherCost = $con->setQuery($sqlOtherCost);
					$oke = $oke && !$con->hasError();
				}
			}
		}
		//end 

		if (!$row2['id_wilayah'] && !$row2['id_group']) {
			$sql2 = "update pro_customer set id_wilayah = '" . $cabang . "', id_group = '" . $grcb . "' where id_customer = '" . $idr . "'";
			$con->setQuery($sql2);
			$oke  = $oke && !$con->hasError();
		}
		if ($diupdate == 1) {
			$sql3 = "update pro_customer set need_update = 1, is_generated_link = 0, is_verified = 0, status_customer = 1 where id_customer = '" . $idr . "'";
			$con->setQuery($sql3);
			$oke  = $oke && !$con->hasError();
		}

		$sql4 = "update pro_master_cabang set urut_penawaran = '" . $tmp1 . "' where id_master = '" . $cabang . "'";
		$con->setQuery($sql4);
		$oke  = $oke && !$con->hasError();

		$url = BASE_URL_CLIENT . "/penawaran-detail.php?" . paramEncrypt("idr=" . $idr . "&idk=" . $res1);
		if ($oke) {
			$con->commit();
			$con->close();
			header("location: " . $url);
			exit();
		} else {
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
	}
} else if ($act == "update") {
	if ($idr == "" || $gelar == "" || $nama_up == "" || $masa_awal == "" || $masa_akhir == "" || $area == "" || $volume == "" || $flagHitung == "" || $produk == "" || $order == "" || $pbbkb == "") {
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else {
		$oke = true;
		$con->beginTransaction();
		$con->clearError();

		$sql1 = "select harga_sm, harga_om, harga_coo from pro_master_harga_minyak where id_area = '" . $area . "' AND periode_awal = '" . tgl_db($masa_awal_tier) . "' AND periode_akhir = '" . tgl_db($masa_akhir_tier) . "'";
		$row4 = $con->getRecord($sql1);

		$harga_sm = $row4['harga_sm'];
		$harga_om = $row4['harga_om'];
		$harga_coo = $row4['harga_coo'];

		if ($harga_tier > $harga_sm) {
			$tier = 'I';
		} elseif ($harga_tier >= $harga_om && $harga_tier < $harga_sm) {
			$tier = 'II';
		} elseif ($harga_tier >= $harga_coo && $harga_tier < $harga_om) {
			$tier = 'III';
		} else {
			$tier = 'III';
		}


		$sql1 = "update pro_penawaran set gelar = '" . $gelar . "', nama_up = '" . $nama_up . "', jabatan_up = '" . $jabatan_up . "', alamat_up = '" . $alamat_up . "', 
					telp_up = '" . $telp_up . "', fax_up = '" . $fax_up . "', jenis_payment = '" . $jns_waktu . "', jenis_net = '" . $jenis_net . "', jangka_waktu = '" . $top . "', masa_awal = '" . tgl_db($masa_awal) . "', 
					masa_akhir = '" . tgl_db($masa_akhir) . "', id_area = '" . $area . "', volume_tawar = '" . $volume . "', perhitungan = '" . $flagHitung . "', harga_dasar = '" . $hargaDasar . "', 
					is_rinci = '" . $is_rinci . "', detail_rincian = '" . json_encode($rincian) . "', detail_formula = '" . json_encode($formula) . "', produk_tawar = '" . $produk . "', 
					pbbkb_tawar = '" . $pbbkb . "', method_order = '" . $order . "', refund_tawar = '" . $refund . "', ket_harga = '" . $ket_harga . "', catatan = '" . $catatan . "', harga_asli = '" . $harga_asli . "', 
					oa_kirim = '" . $oa_kirim . "', other_cost = '" . $other_cost . "', tol_susut = '" . $tol_susut . "', lok_kirim = '" . $lok_kirim . "',
					lastupdate_time = NOW(), lastupdate_ip = '" . $user_ip . "', lastupdate_by = '" . $user_pic . "', 
					term_condition = '" . $term_condition . "', metode = '" . $metode . "', kalkulasi_oa = '" . json_encode($kalkulasi_oa) . "', pembulatan = '" . $pembulatan . "', gabung_oa = '" . $gabung_oa . "', all_in = '" . $all_in . "', gabung_pbbkb = '" . $gabung_pbbkb . "',  gabung_pbbkboa = '" . $gabung_pbbkboa . "', harga_tier = '" . $harga_tier . "',  tier = '" . $tier . "', is_edited=1
					where id_penawaran = '" . $idk . "'";
		$q = $con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();

		if ($reflag) {
			$sql2 = "update pro_penawaran set sm_mkt_summary = '', sm_mkt_pic = '', sm_mkt_result = 0, sm_wil_summary = '', sm_wil_pic = '', sm_wil_result = 0, 
						om_summary = '', om_pic = '', om_result = 0, ceo_summary = '', ceo_pic = '', ceo_result = 0, flag_disposisi = 0, flag_approval = 0 
						where id_penawaran = '" . $idk . "'";
			$con->setQuery($sql2);
			$oke  = $oke && !$con->hasError();
		}

		//develope *iwan 
		$sql_delete = "Delete FROM pro_other_cost_detail WHERE id_penawaran = '" . $idk . "'";
		$con->setQuery($sql_delete);

		// // Simpan other cost ke tabel pro_other_cost_detail
		if (!empty($_POST['other_cost_keterangan']) && !empty($_POST['nominal'])) {
			foreach ($_POST['other_cost_keterangan'] as $index => $keterangan) {
				$nominal = htmlspecialchars($_POST['nominal'][$index], ENT_QUOTES);
				// Validasi data
				if (!empty($keterangan) && !empty($nominal)) {
					$sqlOtherCost = "insert pro_other_cost_detail (id_penawaran, keterangan, nominal) 
					 VALUES ('" . $idk . "','" . $keterangan . "', '" . $nominal . "')";
					$resOtherCost = $con->setQuery($sqlOtherCost);
					$oke = $oke && !$con->hasError();
				}
			}
		}


		$url = BASE_URL_CLIENT . "/penawaran-detail.php?" . paramEncrypt("idr=" . $idr . "&idk=" . $idk);
		if ($oke) {
			$con->commit();
			$con->close();
			header("location: " . $url);
			exit();
		} else {
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
		}
	}
}

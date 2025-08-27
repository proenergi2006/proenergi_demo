<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;
$enk  	= decode($_SERVER['REQUEST_URI']);
$act	= ($enk['act'] == "") ? htmlspecialchars($_POST["act"], ENT_QUOTES) : $enk['act'];
$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);

$code_pr 		= htmlspecialchars($_POST["code_pr"], ENT_QUOTES);
$code_prd 		= htmlspecialchars($_POST["code_prd"], ENT_QUOTES);
//$nomor_dn		= htmlspecialchars($_POST["nomor_dn"], ENT_QUOTES);

$oa_penawaran   =  htmlspecialchars($_POST["oa_penawaran"], ENT_QUOTES);
$oa_disetujui   =  htmlspecialchars($_POST["oa_disetujui"], ENT_QUOTES);
$oa_transportir =  htmlspecialchars($_POST["oa_transportir"], ENT_QUOTES);
$tgl_etl        =  htmlspecialchars($_POST["tgl_etl"], ENT_QUOTES);
$jam_etl        =  htmlspecialchars($_POST["jam_etl"], ENT_QUOTES);
$tgl_eta        =  htmlspecialchars($_POST["tgl_eta"], ENT_QUOTES);
$jam_eta        =  htmlspecialchars($_POST["jam_eta"], ENT_QUOTES);


$tanggal_dn		= htmlspecialchars($_POST["tanggal_dn"], ENT_QUOTES);
$consignor_name	= htmlspecialchars($_POST["signor_name"], ENT_QUOTES);
$consignor_addr	= htmlspecialchars($_POST["signor_addr"], ENT_QUOTES);
$consignee_name	= htmlspecialchars($_POST["signee_name"], ENT_QUOTES);
$consignee_addr	= htmlspecialchars($_POST["signee_addr"], ENT_QUOTES);
$notify_name	= htmlspecialchars($_POST["notify_name"], ENT_QUOTES);
$notify_addr	= htmlspecialchars($_POST["notify_addr"], ENT_QUOTES);
$produk			= htmlspecialchars($_POST["produk"], ENT_QUOTES);
$bl1			= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["bl1"]), ENT_QUOTES);
$bl2			= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["bl2"]), ENT_QUOTES);
$bl3			= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["bl3"]), ENT_QUOTES);
$sf1			= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["sf1"]), ENT_QUOTES);
$sf2			= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["sf2"]), ENT_QUOTES);
$sf3			= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["sf3"]), ENT_QUOTES);
$terminal		= htmlspecialchars($_POST["terminal"], ENT_QUOTES);
$port_addr		= htmlspecialchars($_POST["port_addr"], ENT_QUOTES);
$transportir	= htmlspecialchars($_POST["transportir"], ENT_QUOTES);
$kapten			= htmlspecialchars($_POST["kapten"], ENT_QUOTES);
$vessel_name	= htmlspecialchars($_POST["vessel_name"], ENT_QUOTES);
$shipment		= htmlspecialchars($_POST["shipment"], ENT_QUOTES);
$manifold1		= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["manifold1"]), ENT_QUOTES);
$manifold2		= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["manifold2"]), ENT_QUOTES);
$pump1			= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["pump1"]), ENT_QUOTES);
$pump2			= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["pump2"]), ENT_QUOTES);
$catatan 		= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["catatan"], ENT_QUOTES));

//$volume	= htmlspecialchars(str_replace(array(".",","), array("","."), $_POST["volume"]), ENT_QUOTES);
//$catatan 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["catatan"], ENT_QUOTES));
$wilayah	= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$group		= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);
$pic		= paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']);
$arrPump 	= array("jumlah_kiri" => "", "pump_kiri_awal" => "", "pump_kiri_akhir" => "", "jumlah_kanan" => "", "pump_kanan_awal" => "", "pump_kanan_akhir" => "");
$arrMani 	= array("jumlah_kiri" => "", "mani_kiri_awal" => "", "mani_kiri_akhir" => "", "jumlah_kanan" => "", "mani_kanan_awal" => "", "mani_kanan_akhir" => "");
$arrTank 	= array();
$arrLain 	= array();
$jum_segel 	= 0;

$oke = true;
$con->beginTransaction();
$con->clearError();

$cek1 = "select inisial_segel, urut_segel, stok_segel from pro_master_cabang where id_master = '" . $wilayah . "' for update";
$row1 = $con->getRecord($cek1);
$stok = $row1['stok_segel'];
$seal = $row1['urut_segel'];
$pref = $row1['inisial_segel'];

$jum_kiri 	= 0;
$jum_kanan 	= 0;
$jum_lain1 	= 0;
$jum_lain2 	= 0;

foreach ($_POST["tank_kiri"] as $idx1 => $tank1) {
	$tank1 = htmlspecialchars(str_replace(array(".", ","), array("", ""), $tank1), ENT_QUOTES);
	if ($tank1 && $tank1 > 1) {
		$tank_kiri_awal = $seal + 1;
		$tank_kiri_last = $seal + $tank1;
		$seal = $seal + $tank1;
	} else if ($tank1 && $tank1 == 1) {
		$tank_kiri_awal = $seal + $tank1;
		$tank_kiri_last = 0;
		$seal = $seal + $tank1;
	}
	$jum_kiri = $jum_kiri + $tank1;
	if (!array_key_exists($idx1, $arrTank))
		$arrTank[$idx1] = array("jumlah_kiri" => "", "tank_kiri_awal" => "", "tank_kiri_akhir" => "", "jumlah_kanan" => "", "tank_kanan_awal" => "", "tank_kanan_akhir" => "");
	$arrTank[$idx1]["jumlah_kiri"] 		= $tank1;
	$arrTank[$idx1]["tank_kiri_awal"] 	= $tank_kiri_awal;
	$arrTank[$idx1]["tank_kiri_akhir"] 	= $tank_kiri_last;
}

foreach ($_POST["tank_kanan"] as $idx2 => $tank2) {
	$tank2 = htmlspecialchars(str_replace(array(".", ","), array("", ""), $tank2), ENT_QUOTES);
	if ($tank2 && $tank2 > 1) {
		$tank_kanan_awal = $seal + 1;
		$tank_kanan_last = $seal + $tank2;
		$seal = $seal + $tank2;
	} else if ($tank2 && $tank2 == 1) {
		$tank_kanan_awal = $seal + $tank2;
		$tank_kanan_last = 0;
		$seal = $seal + $tank2;
	}
	$jum_kanan = $jum_kanan + $tank2;
	if (!array_key_exists($idx2, $arrTank))
		$arrTank[$idx2] = array("jumlah_kiri" => "", "tank_kiri_awal" => "", "tank_kiri_akhir" => "", "jumlah_kanan" => "", "tank_kanan_awal" => "", "tank_kanan_akhir" => "");
	$arrTank[$idx2]["jumlah_kanan"] 	= $tank2;
	$arrTank[$idx2]["tank_kanan_awal"] 	= $tank_kanan_awal;
	$arrTank[$idx2]["tank_kanan_akhir"] = $tank_kanan_last;
}

if ($manifold1 && $manifold1 > 1) {
	$manifold1_awal = $seal + 1;
	$manifold1_last = $seal + $manifold1;
	$seal = $seal + $manifold1;
} else if ($manifold1 && $manifold1 == 1) {
	$manifold1_awal = $seal + $manifold1;
	$manifold1_last = 0;
	$seal = $seal + $manifold1;
}

if ($manifold2 && $manifold2 > 1) {
	$manifold2_awal = $seal + 1;
	$manifold2_last = $seal + $manifold2;
	$seal = $seal + $manifold2;
} else if ($manifold2 && $manifold2 == 1) {
	$manifold2_awal = $seal + $manifold2;
	$manifold2_last = 0;
	$seal = $seal + $manifold2;
}
$arrMani["jumlah_kiri"] 	= $manifold1;
$arrMani["mani_kiri_awal"] 	= $manifold1_awal;
$arrMani["mani_kiri_akhir"] = $manifold1_last;
$arrMani["jumlah_kanan"] 	= $manifold2;
$arrMani["mani_kanan_awal"] = $manifold2_awal;
$arrMani["mani_kanan_akhir"] = $manifold2_last;

if ($pump1 && $pump1 > 1) {
	$pump1_awal = $seal + 1;
	$pump1_last = $seal + $pump1;
	$seal = $seal + $pump1;
} else if ($pump1 && $pump1 == 1) {
	$pump1_awal = $seal + $pump1;
	$pump1_last = 0;
	$seal = $seal + $pump1;
}

if ($pump2 && $pump2 > 1) {
	$pump2_awal = $seal + 1;
	$pump2_last = $seal + $pump2;
	$seal = $seal + $pump2;
} else if ($pump2 && $pump2 == 1) {
	$pump2_awal = $seal + $pump2;
	$pump2_last = 0;
	$seal = $seal + $pump2;
}
$arrPump["jumlah_kiri"] 	= $pump1;
$arrPump["pump_kiri_awal"] 	= $pump1_awal;
$arrPump["pump_kiri_akhir"] = $pump1_last;
$arrPump["jumlah_kanan"] 	= $pump2;
$arrPump["pump_kanan_awal"] = $pump2_awal;
$arrPump["pump_kanan_akhir"] = $pump2_last;

if (!is_array_empty($_POST["jns_kiri"])) {
	foreach ($_POST["jns_kiri"] as $idx3 => $jns1) {
		$jns = htmlspecialchars($_POST["jns_kiri"][$idx3], ENT_QUOTES);
		$sgl = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["segel_kiri"][$idx3]), ENT_QUOTES);
		if ($jns && $sgl) {
			if ($sgl > 1) {
				$sgl_kiri_awal = $seal + 1;
				$sgl_kiri_last = $seal + $sgl;
				$seal = $seal + $sgl;
			} else {
				$sgl_kiri_awal = $seal + $sgl;
				$sgl_kiri_last = 0;
				$seal = $seal + $sgl;
			}
			$jum_lain1 = $jum_lain1 + $sgl;
			if (!array_key_exists($idx3, $arrLain))
				$arrLain[$idx3] = array(
					"jns_kiri" => "",
					"jumlah_kiri" => "",
					"sgl_kiri_awal" => "",
					"sgl_kiri_akhir" => "",
					"jns_kanan" => "",
					"jumlah_kanan" => "",
					"sgl_kanan_awal" => "",
					"sgl_kanan_akhir" => ""
				);
			$arrLain[$idx3]["jns_kiri"] 		= $jns;
			$arrLain[$idx3]["jumlah_kiri"] 		= $sgl;
			$arrLain[$idx3]["sgl_kiri_awal"] 	= $sgl_kiri_awal;
			$arrLain[$idx3]["sgl_kiri_akhir"] 	= $sgl_kiri_last;
		}
	}
}
if (!is_array_empty($_POST["jns_kanan"])) {
	foreach ($_POST["jns_kanan"] as $idx4 => $jns2) {
		$jns = htmlspecialchars($_POST["jns_kanan"][$idx4], ENT_QUOTES);
		$sgl = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["segel_kanan"][$idx4]), ENT_QUOTES);
		if ($jns && $sgl) {
			if ($sgl > 1) {
				$sgl_kanan_awal = $seal + 1;
				$sgl_kanan_last = $seal + $sgl;
				$seal = $seal + $sgl;
			} else {
				$sgl_kanan_awal = $seal + $sgl;
				$sgl_kanan_last = 0;
				$seal = $seal + $sgl;
			}
			$jum_lain2 = $jum_lain2 + $sgl;
			if (!array_key_exists($idx4, $arrLain))
				$arrLain[$idx4] = array(
					"jns_kiri" => "",
					"jumlah_kiri" => "",
					"sgl_kiri_awal" => "",
					"sgl_kiri_akhir" => "",
					"jns_kanan" => "",
					"jumlah_kanan" => "",
					"sgl_kanan_awal" => "",
					"sgl_kanan_akhir" => ""
				);
			$arrLain[$idx4]["jns_kanan"] 		= $jns;
			$arrLain[$idx4]["jumlah_kanan"] 	= $sgl;
			$arrLain[$idx4]["sgl_kanan_awal"] 	= $sgl_kanan_awal;
			$arrLain[$idx4]["sgl_kanan_akhir"] 	= $sgl_kanan_last;
		}
	}
}

$need_segel = $manifold1 + $manifold2 + $pump1 + $pump2 + $jum_lain1 + $jum_lain2 + $jum_kiri + $jum_kanan;
if ($stok < $need_segel) {
	$con->rollBack();
	$con->clearError();
	$con->close();
	$flash->add("error", "Maaf stok segel tidak cukup...", BASE_REFERER);
} else {
	$cek1 = "select inisial_cabang, urut_dn_kpl from pro_master_cabang where id_master = '" . $wilayah . "' for update";
	$row1 = $con->getRecord($cek1);
	$tmp1 = $row1['urut_dn_kpl'] + 1;
	$noms = "DN/" . $row1['inisial_cabang'] . "/SHP/" . str_pad($tmp1, 6, '0', STR_PAD_LEFT);

	$cek2 = "select inisial_cabang, urut_po_kpl from pro_master_cabang where id_master = '" . $wilayah . "' for update";
	$row2 = $con->getRecord($cek2);
	$tmp2 = $row2['urut_po_kpl'] + 1;
	$tmp3 = array("1" => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
	$noms2 = str_pad($tmp2, 4, '0', STR_PAD_LEFT) . '/PE/PO/SHP/' . $row2['inisial_cabang'] . '/' . $tmp3[intval(date("m"))] . '/' . date("Y");

	$sql1 = "insert into pro_po_ds_kapal(id_wilayah, id_pr, id_prd, id_plan, id_poc, oa_penawaran, oa_disetujui, oa_transportir, tgl_etl, jam_etl, tgl_eta, jam_eta,  tanggal_loading, nomor_po, nomor_dn_kapal, consignor_nama, consignor_alamat, consignee_nama, consignee_alamat, notify_nama, notify_alamat, produk_dn, bl_lo_jumlah, bl_lc_jumlah, bl_mt_jumlah, sfal_lo_jumlah, sfal_lc_jumlah, sfal_mt_jumlah, terminal, port_discharge, transportir, kapten_name, vessel_name, shipment, tank_seal, manifold_seal, pump_seal, other_seal, keterangan, created_time, created_ip, created_by)
										(select '" . $wilayah . "', a.id_pr, a.id_prd, a.id_plan, b.id_poc, '" . $oa_penawaran . "', '" . $oa_disetujui . "', '" . $oa_transportir . "', '" . tgl_db($tgl_etl) . "', '" . $jam_etl . "',  '" . tgl_db($tgl_eta) . "', '" . $jam_eta . "' ,'" . tgl_db($tanggal_dn) . "', '" . $noms2 . "', '" . $noms . "', '" . $consignor_name . "', '" . $consignor_addr . "', '" . $consignee_name . "', '" . $consignee_addr . "', '" . $notify_name . "', '" . $notify_addr . "', '" . $produk . "', '" . $bl1 . "', '" . $bl2 . "', '" . $bl3 . "', '" . $sf1 . "', '" . $sf2 . "', '" . $sf3 . "', '" . $terminal . "', '" . $port_addr . "', '" . $transportir . "', '" . $kapten . "', '" . $vessel_name . "', '" . $shipment . "', '" . json_encode($arrTank) . "', '" . json_encode($arrMani) . "', '" . json_encode($arrPump) . "', '" . json_encode($arrLain) . "', '" . $catatan . "', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . $pic . "' from pro_pr_detail a join pro_po_customer_plan b on a.id_plan = b.id_plan where a.id_prd = '" . $code_prd . "')";
	$res1 = $con->setQuery($sql1);
	$oke  = $oke && !$con->hasError();


	$url = BASE_URL_CLIENT . "/delivery-kapal-detail.php?" . paramEncrypt("idr=" . $res1);

	$sql2 = "update pro_master_cabang set urut_dn_kpl = '" . $tmp1 . "',  urut_segel = '" . $seal . "', stok_segel = stok_segel - " . $need_segel . " where id_master = '" . $wilayah . "'";
	$con->setQuery($sql2);
	$oke  = $oke && !$con->hasError();

	$sql3 = "update pro_master_cabang set urut_po_kpl = '" . $tmp2 . "' where id_master = '" . $wilayah . "'";
	$con->setQuery($sql3);
	$oke  = $oke && !$con->hasError();

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

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
$baru 	= htmlspecialchars($_POST["baru"], ENT_QUOTES);
$loco 	= htmlspecialchars($_POST['loco'], ENT_QUOTES);
$depot 	= htmlspecialchars($_POST['dpt'], ENT_QUOTES);
$tgl_ds = htmlspecialchars($_POST['tgl'], ENT_QUOTES);
$nom_ds = htmlspecialchars($_POST['nods'], ENT_QUOTES);
$note 	= str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["catatan"], ENT_QUOTES));
$colext = ($note) ? ", catatan = '" . $note . "'" : "";
$url 	= BASE_URL_CLIENT . "/delivery-loading-detail.php?" . paramEncrypt("idr=" . $idr);



if ($loco == 0) {
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	$jum_segel = 0;
	$arr_segel = array();
	if (count($_POST["dt1"]) > 0) {
		foreach ($_POST["dt1"] as $idx => $val) {
			$nomor_urut_ds 	= htmlspecialchars($_POST["ck1"][$idx], ENT_QUOTES);
			$nomor_ref_dn 	= htmlspecialchars($_POST["dt1"][$idx], ENT_QUOTES);
			$tgl_loading 	= htmlspecialchars($_POST["dt2"][$idx], ENT_QUOTES);
			$jam_loading 	= htmlspecialchars($_POST["dt3"][$idx], ENT_QUOTES);
			$nomor_oc 		= htmlspecialchars($_POST["dt4"][$idx], ENT_QUOTES);
			$nomor_order 	= htmlspecialchars($_POST["dt5"][$idx], ENT_QUOTES);
			$jumlah_segel 	= htmlspecialchars($_POST["dt6"][$idx], ENT_QUOTES);
			$manual_segel 	= htmlspecialchars($_POST["dtsegel"][$idx], ENT_QUOTES);
			$source 		= htmlspecialchars($_POST["dt7"][$idx], ENT_QUOTES);
			$sold_to 		= htmlspecialchars($_POST["dt8"][$idx], ENT_QUOTES);
			$nomor_do 		= htmlspecialchars($_POST["dt9"][$idx], ENT_QUOTES);
			$remark         = htmlspecialchars($_POST["dt18"][$idx], ENT_QUOTES);

			if ($nomor_ref_dn) {
				$jum_segel = $jum_segel + $jumlah_segel;
				if ($jumlah_segel > 0) {
					$ext_segel = "jumlah_segel = '" . $jumlah_segel . "', ";
					array_push($arr_segel, $idx);
				} else {
					$ext_segel = "";
				}

				$sql2 = "update pro_po_ds_detail set " . $ext_segel . " nomor_urut_ds = '" . $nomor_urut_ds . "', manual_segel = '" . $manual_segel . "', nomor_ref_dn = '" . $nomor_ref_dn . "', 
							 tanggal_loading = '" . tgl_db($tgl_loading) . "', jam_loading = '" . $jam_loading . "', nomor_oc = '" . $nomor_oc . "', nomor_order = '" . $nomor_order . "', 
							 source = '" . $source . "', sold_to = '" . $sold_to . "', remark_depo = '" . $remark . "' where id_dsd = '" . $idx . "'";
				$con->setQuery($sql2);
				$oke  = $oke && !$con->hasError();
			}
		}
	}

	$sql1 = "update pro_po_ds set is_submitted = 1" . $colext . " where id_ds = '" . $idr . "'";
	$con->setQuery($sql1);
	$oke  = $oke && !$con->hasError();

	$cek1 = "select a.id_master, a.inisial_cabang, a.inisial_segel, a.stok_segel, a.urut_dn, a.urut_segel, a.urut_oslog 
				 from pro_master_cabang a join pro_po_ds b on a.id_master = b.id_wilayah where b.id_ds = '" . $idr . "' for update";
	$row1 = $con->getRecord($cek1);
	$idm1 = $row1['id_master'];
	$noDo = $row1['urut_dn'];
	$seal = $row1['urut_segel'];
	$oslog = $row1['urut_oslog'];

	$arrRomawi 	= array("1" => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
	$arrDn 		= array();

	if ($row1['stok_segel'] < $jum_segel) {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "Maaf stok segel tidak cukup...", BASE_REFERER);
	} else {
		$cek2 = "select id_dsd, jumlah_segel, nomor_ref_dn, nomor_do from pro_po_ds_detail where id_ds = '" . $idr . "' order by nomor_urut_ds";
		$row2 = $con->getResult($cek2);
		foreach ($row2 as $data1) {
			$ext_col1 = "";
			if (in_array($data1['id_dsd'], $arr_segel)) {
				if ($data1['jumlah_segel'] && $data1['jumlah_segel'] > 1) {
					$segel_awal = $seal + 1;
					$segel_last = $seal + $data1['jumlah_segel'];
					$seal = $seal + $data1['jumlah_segel'];
				} else if ($data1['jumlah_segel'] && $data1['jumlah_segel'] == 1) {
					$segel_awal = $seal + $data1['jumlah_segel'];
					$segel_last = 0;
					$seal = $seal + $data1['jumlah_segel'];
				}
				$ext_col1 = ", nomor_segel_awal='" . $segel_awal . "', nomor_segel_akhir='" . $segel_last . "'";
			}

			$ext_col2 = "";
			if (!$data1['nomor_do']) {
				if (!array_key_exists($data1['nomor_ref_dn'], $arrDn)) {
					$noDo++;
					$nomor_do = $noDo;
					$arrDn[$data1['nomor_ref_dn']] = $noDo;
				} else {
					$nomor_do = $arrDn[$data1['nomor_ref_dn']];
				}
				$nomor_dn = "DN/" . $row1['inisial_cabang'] . "/" . str_pad($nomor_do, 6, '0', STR_PAD_LEFT);
				$ext_col2 = ", nomor_do = '" . $nomor_dn . "'";
			}

			$sql3 = "update pro_po_ds_detail set pre_segel = '" . $row1['inisial_segel'] . "' " . $ext_col1 . $ext_col2 . " where id_dsd = '" . $data1['id_dsd'] . "'";
			$con->setQuery($sql3);
			$oke  = $oke && !$con->hasError();
		}
		$sql4 = "update pro_master_cabang set urut_dn = '" . $noDo . "', stok_segel = stok_segel - " . $jum_segel . ", urut_segel = '" . $seal . "' where id_master = '" . $idm1 . "'";
		$con->setQuery($sql4);
		$oke  = $oke && !$con->hasError();
	}

	if ($oke) {
		$cek01 = "
				select m.id_po, b.id_pod, o.id_ds, k.nomor_plat, b.trip_po, b.multidrop_po, b.nomor_oslog 
				from pro_po_ds_detail a 
				join pro_po_ds o on a.id_ds = o.id_ds 
				join pro_po_detail b on a.id_pod = b.id_pod 
				join pro_po m on a.id_po = m.id_po 
				join pro_master_transportir_mobil k on b.mobil_po = k.id_master 
				join pro_master_transportir_sopir l on b.sopir_po = l.id_master
				where o.id_ds = '" . $idr . "'
				order by m.id_po, k.nomor_plat, b.trip_po, b.multidrop_po, nomor_ds, tanggal_ds 
			";
		$res01 = $con->getResult($cek01);
		$txt02 = "";
		$noms = 0;
		$nom_cabang = $oslog;
		foreach ($res01 as $idx01 => $datanya) {
			$noms++;
			$datacek02 = $datanya['id_ds'] . '|#|' . $datanya['nomor_plat'] . '|#|' . $datanya['trip_po'];

			if ($txt02 != $datacek02) {
				if (!$datanya['nomor_oslog']) {
					$nom_cabang++;
					$nom_formatnya 	= 'trip' . strtolower($row1['inisial_cabang']) . '-' . str_pad($nom_cabang, 5, '0', STR_PAD_LEFT);
					$sql2 = "update pro_po_detail set nomor_oslog = '" . $nom_formatnya . "' where id_pod = '" . $datanya['id_pod'] . "'";
					$con->setQuery($sql2);
				}
				$txt02 = $datacek02;
			} else {
				if (!$datanya['nomor_oslog'] && $datanya['multidrop_po'] > 1) {
					$nom_formatnya 	= 'trip' . strtolower($row1['inisial_cabang']) . '-' . str_pad($nom_cabang, 5, '0', STR_PAD_LEFT);
					$sql2 = "update pro_po_detail set nomor_oslog = '" . $nom_formatnya . "' where id_pod = '" . $datanya['id_pod'] . "'";
					$con->setQuery($sql2);
				} else {
					if (!$datanya['nomor_oslog']) {
						$nom_cabang++;
						$nom_formatnya 	= 'trip' . strtolower($row1['inisial_cabang']) . '-' . str_pad($nom_cabang, 5, '0', STR_PAD_LEFT);
						$sql2 = "update pro_po_detail set nomor_oslog = '" . $nom_formatnya . "' where id_pod = '" . $datanya['id_pod'] . "'";
						$con->setQuery($sql2);
					}
				}
			}
		}
		$cek02 = "update pro_master_cabang set urut_oslog = '" . $nom_cabang . "' where id_master = '" . $idm1 . "'";
		$con->setQuery($cek02);
		$oke  = $oke && !$con->hasError();

		$cekds = "SELECT a.id_dsd as id, o.nomor_ds, o.tanggal_ds, b.no_spj, k.nomor_plat, l.nama_sopir, 
		c.produk, b.volume_po, b.nomor_oslog as nomor_pengiriman, 
		CONCAT(r.tanki_terminal, ' ' ,r.nama_terminal) as terminal_name, r.lokasi_terminal, r.latitude as lat_terminal, r.longitude as long_terminal,
		i.kode_pelanggan, i.nama_customer,   
		e.alamat_survey, g.nama_kab, f.nama_prov, concat('LCR', lpad(e.id_lcr, 4, '0')) as kode_lcr, e.latitude_lokasi, e.longitude_lokasi, 
		b.ongkos_po, b.tgl_etl_po, b.jam_etl_po, b.tgl_eta_po, b.jam_eta_po, s.wilayah_angkut, a.is_loaded
		FROM pro_po_ds_detail a 
		JOIN pro_po_ds o on a.id_ds = o.id_ds 
		JOIN pro_po_detail b on a.id_pod = b.id_pod 
		JOIN pro_po m on a.id_po = m.id_po 
		JOIN pro_pr_detail c on a.id_prd = c.id_prd 
		JOIN pro_po_customer_plan d on a.id_plan = d.id_plan 
		JOIN pro_po_customer h on d.id_poc = h.id_poc 
		JOIN pro_customer_lcr e on d.id_lcr = e.id_lcr
		JOIN pro_customer i on h.id_customer = i.id_customer 
		JOIN acl_user j on i.id_marketing = j.id_user 
		JOIN pro_master_provinsi f on e.prov_survey = f.id_prov 
		JOIN pro_master_kabupaten g on e.kab_survey = g.id_kab
		JOIN pro_penawaran p on h.id_penawaran = p.id_penawaran  
		JOIN pro_master_area q on p.id_area = q.id_master 
		JOIN pro_master_transportir_mobil k on b.mobil_po = k.id_master 
		JOIN pro_master_transportir_sopir l on b.sopir_po = l.id_master
		JOIN pro_master_transportir n on m.id_transportir = n.id_master 
		JOIN pro_master_terminal r on o.id_terminal = r.id_master 
		JOIN pro_master_wilayah_angkut s on e.id_wil_oa = s.id_master and e.prov_survey = s.id_prov and e.kab_survey = s.id_kab WHERE 1=1 AND k.link_gps = 'OSLOG' AND o.id_ds='" . $idr . "'";

		$resds = $con->getResult($cekds);
		$logFilePath = realpath(__DIR__ . '/../../post-data-api-oslog.log.txt');
		if (!empty($resds)) {
			foreach ($resds as $key) {
				$tempal = ($key['nama_kab'] ? strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $key['nama_kab'])) : '');
				$alamat	= $key['alamat_survey'] . ($tempal ? ' ' . ucwords($tempal) : '') . ($key['nama_prov'] ? ' ' . $key['nama_prov'] : '');

				// URL API yang akan diakses
				$url_api = 'https://oslog.id/javaz-api/shipment-syop/new';

				// Token Bearer
				$token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJJZCI6MTE1OSwiTmFtZSI6InByb2VuZXJnaSIsIlJvbGUiOiJhZG1fcHJvZW5lcmdpIiwiQ29tcGFueSI6NjA2LCJVc2VyUG9kSWQiOjAsImlzcyI6Ik9TTE9HIDUgQVBJIn0.H-ljfy7I0zVzpvXsar3FddpUT2RHChNaEP8uw50kmV8';

				$id_dsdnya = $key['id'];

				// Data yang akan dikirim (misalnya, dalam format JSON)
				$data = [
					"id" 					=> "$id_dsdnya",
					"nomor_pengiriman" 		=> $key['nomor_pengiriman'],
					"nomor_ds"				=> $key['nomor_ds'],
					"tanggal_ds" 			=> $key['tanggal_ds'],
					"no_spj" 				=> $key['no_spj'],
					"nomor_plat" 			=> $key['nomor_plat'],
					"nama_sopir" 			=> $key['nama_sopir'],
					"produk" 				=> $key['produk'],
					"volume"	 			=> $key['volume_po'],
					"loading_destination" 	=> $key['nama_terminal'] . ($key['tanki_terminal'] ? ' - ' . $key['tanki_terminal'] : '') . ($key['lokasi_terminal'] ? ', ' . $key['lokasi_terminal'] : ''),
					"nama_customer" 		=> ($key['kode_pelanggan'] ? $key['kode_pelanggan'] . ' : ' : '') . $key['nama_customer'],
					"lokasi_tujuan" 		=> $alamat,
					"nomor_lcr" 			=> $key['kode_lcr'],
					"latitude_lokasi" 		=> $key['latitude_lokasi'],
					"longitude_lokasi"		=> $key['longitude_lokasi'],
					"wilayah_oa" 			=> $key['wilayah_angkut'],
					"ongkos_angkut" 		=> $key['ongkos_po'],
					"tgl_etl" 				=> $key['tgl_etl_po'] . ($key['jam_etl_po'] ? ' ' . $key['jam_etl_po'] : ' 00:00:00'),
					"tgl_eta" 				=> $key['tgl_eta_po'] . ($key['jam_eta_po'] ? ' ' . $key['jam_eta_po'] : ' 00:00:00'),
					"is_loaded" 			=> true,
					"nama_terminal" 		=> $key['terminal_name'],
					"lokasi_terminal" 		=> $key['lokasi_terminal'],
					"latitude_terminal" 	=> $key['lat_terminal'],
					"longitude_terminal" 	=> $key['long_terminal']
					// "is_loaded" 			=> ($key['is_loaded'] ? true : false),
				];

				// Mengonversi data ke format JSON
				$jsonData = json_encode($data);

				// Catat data POST ke file (append agar data baru ditambahkan ke bawah)
				$logEntry = "Timestamp: " . date("Y-m-d H:i:s") . "\n";
				$logEntry .= "Endpoint: " . $url_api . "\n";
				$logEntry .= "POST Data: " . $jsonData . "\n\n";

				// Menulis log ke file
				file_put_contents($logFilePath, $logEntry, FILE_APPEND);

				// Inisialisasi cURL
				$ch = curl_init($url_api);

				// Setel opsi cURL
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
				curl_setopt($ch, CURLOPT_HTTPHEADER, [
					'Content-Type: application/json',
					'Authorization: Bearer ' . $token,
					'Content-Length: ' . strlen($jsonData)
				]);

				// Eksekusi permintaan dan ambil respons
				$response = curl_exec($ch);

				// Cek jika terjadi kesalahan
				if (curl_errno($ch)) {
					$con->rollBack();
					$con->clearError();
					$con->close();
					$flash->add("error", "Server Error", BASE_REFERER);
				} else {
					// Menampilkan respons dari server
					// Tutup cURL
					// echo $key['latitude_lokasi'] . " " .  $key['longitude_lokasi'];
					$result = json_decode($response, true);
					curl_close($ch);
					// echo $response;
					if ($result['code'] != 200) {
						if ($result['message'] != "shipment is already exists") {
							if ($result['code'] == 400) {
								if ($result['message'] == "lon is required, lon is zero value") {
									$con->rollBack();
									$con->clearError();
									$con->close();
									$flash->add("error", "Longitude lokasi customer " . $key['kode_pelanggan'] . " " . $key['nama_customer'] . " tidak valid", BASE_REFERER);
								} elseif ($result['message'] == "lat is required, lat is zero value") {
									$con->rollBack();
									$con->clearError();
									$con->close();
									$flash->add("error", "Latitude lokasi customer " . $key['kode_pelanggan'] . " " . $key['nama_customer'] .   " tidak valid", BASE_REFERER);
								} elseif ($result['message'] == "nomor_plat is not found") {
									$con->rollBack();
									$con->clearError();
									$con->close();
									$flash->add("error", "Plat nomor " . $key['nomor_plat'] . " belum terdaftar di OSLOG, silahkan daftarkan dulu plat nomor tersebut pada OSLOG", BASE_REFERER);
								} else {
									$con->rollBack();
									$con->clearError();
									$con->close();
									$flash->add("error", $result['message'], BASE_REFERER);
								}
							} else {
								$con->rollBack();
								$con->clearError();
								$con->close();
								$flash->add("error", $result['message'], BASE_REFERER);
							}
						}
					}
				}
			}
		}
		// echo $realPath;
		$con->commit();
		$con->close();
		$flash->add("success", "Data DS telah disimpan", $url);
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}
} else if ($loco == 1) {
	$jum_segel 	= 0;
	$arr_segel 	= array();
	$arr_trans 	= array();
	$ip_user 	= $_SERVER['REMOTE_ADDR'];
	$pic_user 	= paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]);
	$wilayah 	= paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]);

	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	if (count($_POST["dt1"]) > 0) {
		foreach ($_POST["dt1"] as $idx => $val) {
			$nomor_urut_ds 	= htmlspecialchars($_POST["ck1"][$idx], ENT_QUOTES);
			$nomor_ref_dn 	= htmlspecialchars($_POST["dt1"][$idx], ENT_QUOTES);
			$tgl_loading 	= htmlspecialchars($_POST["dt2"][$idx], ENT_QUOTES);
			$jam_loading 	= htmlspecialchars($_POST["dt3"][$idx], ENT_QUOTES);
			$nomor_oc 		= htmlspecialchars($_POST["dt4"][$idx], ENT_QUOTES);
			$nomor_order 	= htmlspecialchars($_POST["dt5"][$idx], ENT_QUOTES);
			$jumlah_segel 	= htmlspecialchars($_POST["dt6"][$idx], ENT_QUOTES);
			$manual_segel 	= htmlspecialchars($_POST["dtsegel"][$idx], ENT_QUOTES);
			$source 		= htmlspecialchars($_POST["dt7"][$idx], ENT_QUOTES);
			$sold_to 		= htmlspecialchars($_POST["dt8"][$idx], ENT_QUOTES);
			$nomor_do 		= htmlspecialchars($_POST["dt9"][$idx], ENT_QUOTES);
			$transportir 	= htmlspecialchars($_POST["dt10"][$idx], ENT_QUOTES);
			$truck 			= htmlspecialchars($_POST["dt11"][$idx], ENT_QUOTES);
			$sopir 			= htmlspecialchars($_POST["dt12"][$idx], ENT_QUOTES);
			$remark			= htmlspecialchars($_POST["dt18"][$idx], ENT_QUOTES);
			$remark_to_depo = htmlspecialchars($_POST["remark"][$idx], ENT_QUOTES);
			$ext_id_po 		= htmlspecialchars($_POST["ext_id_po"][$idx], ENT_QUOTES);
			$ext_id_pod 	= htmlspecialchars($_POST["ext_id_pod"][$idx], ENT_QUOTES);
			$ext_id_pr 		= htmlspecialchars($_POST["ext_id_pr"][$idx], ENT_QUOTES);
			$ext_id_prd 	= htmlspecialchars($_POST["ext_id_prd"][$idx], ENT_QUOTES);

			if ($nomor_ref_dn) {
				$jum_segel 	= $jum_segel + $jumlah_segel;
				if (!array_key_exists($transportir . '#' . $ext_id_pr, $arr_trans)) $arr_trans[$transportir . '#' . $ext_id_pr] = array();
				$arr_ds = array('id_prd' => $ext_id_prd, 'id_pr' => $ext_id_pr, 'id_dsd' => $idx, 'mobil' => $truck, 'sopir' => $sopir, 'tgletl' => tgl_db($tgl_loading), 'jametl' => $jam_loading);
				array_push($arr_trans[$transportir . '#' . $ext_id_pr], $arr_ds);

				if ($jumlah_segel > 0) {
					$ext_segel = "jumlah_segel = '" . $jumlah_segel . "', ";
					array_push($arr_segel, $idx);
				} else {
					$ext_segel = "";
				}

				$sql2 = "update pro_po_ds_detail set " . $ext_segel . " nomor_urut_ds = '" . $nomor_urut_ds . "', manual_segel = '" . $manual_segel . "', nomor_ref_dn = '" . $nomor_ref_dn . "', 
							 tanggal_loading = '" . tgl_db($tgl_loading) . "', jam_loading = '" . $jam_loading . "', nomor_oc = '" . $nomor_oc . "', nomor_order = '" . $nomor_order . "', 
							 source = '" . $source . "', sold_to = '" . $sold_to . "', remark_depo = '" . $remark . "' where id_dsd = '" . $idx . "'";
				$con->setQuery($sql2);
				$oke  = $oke && !$con->hasError();
			}
		}
	}

	/* TRANSAKSI DATA TABEL PO */
	if (count($arr_trans) > 0) {
		foreach ($arr_trans as $idtr1 => $valtr1) {
			$tmptr = explode("#", $idtr1);
			$cekpo1 = "select id_po from pro_po where disposisi_po = -1 and id_wilayah = '" . $wilayah . "' and id_transportir = '" . $tmptr[0] . "' 
							and tanggal_po = '" . $tgl_ds . "' and id_pr = '" . $tmptr[1] . "'";
			$rowpo1 = $con->getRecord($cekpo1);
			if (!$rowpo1['id_po']) {
				$sqlpo1 = "insert into pro_po(id_pr, id_wilayah, id_transportir, tanggal_po, nomor_po, disposisi_po, po_approved, tgl_approved, created_by) 
							 	values ('" . $tmptr[1] . "', '" . $wilayah . "', '" . $tmptr[0] . "', '" . $tgl_ds . "', '" . $nom_ds . "', -1, 1, NOW(), '" . $pic_user . "')";
				$rowpo1['id_po'] = $con->setQuery($sqlpo1);
				$oke  = $oke && !$con->hasError();
			}

			foreach ($arr_trans[$idtr1] as $idtr2 => $valtr2) {
				$id_prd = htmlspecialchars($valtr2['id_prd'], ENT_QUOTES);
				$id_pr 	= htmlspecialchars($valtr2['id_pr'], ENT_QUOTES);
				$id_dsd = htmlspecialchars($valtr2['id_dsd'], ENT_QUOTES);
				$mobil 	= htmlspecialchars($valtr2['mobil'], ENT_QUOTES);
				$sopir 	= htmlspecialchars($valtr2['sopir'], ENT_QUOTES);
				$tgletl = htmlspecialchars($valtr2['tgletl'], ENT_QUOTES);
				$jametl = htmlspecialchars($valtr2['jametl'], ENT_QUOTES);
				$urut 	= $idtr2 + 1;

				$cekpo2 = "select id_pod from pro_po_detail where id_prd = '" . $id_prd . "'";
				$rowpo2 = $con->getRecord($cekpo2);
				if (!$rowpo2['id_pod']) {
					$sqlpo2 = "insert into pro_po_detail(id_po, id_prd, id_plan, volume_po, ongkos_po, no_urut_po, mobil_po, sopir_po, terminal_po, tgl_kirim_po, tgl_eta_po, 
									jam_eta_po, tgl_etl_po, jam_etl_po, pod_approved) 
									(select '" . $rowpo1['id_po'] . "', a.id_prd, a.id_plan, a.volume, a.transport, '" . $urut . "', '" . $mobil . "', '" . $sopir . "', a.pr_terminal, 
									b.tanggal_kirim, c.tanggal_loading, c.jam_loading, c.tanggal_loading, c.jam_loading, 1 from pro_pr_detail a 
									join pro_po_customer_plan b on a.id_plan = b.id_plan join pro_po_ds_detail c on a.id_prd = c.id_prd where a.id_prd = '" . $id_prd . "')";
					$rowpo2['id_pod'] = $con->setQuery($sqlpo2);
					$oke  = $oke && !$con->hasError();
				} else {
					$sqlpo2 = "update pro_po_detail set id_po = '" . $rowpo1['id_po'] . "', mobil_po = '" . $mobil . "', sopir_po = '" . $sopir . "', tgl_etl_po = '" . $tgletl . "', 
									jam_etl_po = '" . $jametl . "' where id_pod = '" . $rowpo2['id_pod'] . "'";
					$con->setQuery($sqlpo2);
					$oke  = $oke && !$con->hasError();
				}

				$sqlpo3 = "update pro_po_ds_detail set id_po = '" . $rowpo1['id_po'] . "', id_pod = '" . $rowpo2['id_pod'] . "' where id_dsd = '" . $id_dsd . "'";
				$con->setQuery($sqlpo3);
				$oke  = $oke && !$con->hasError();
			}
		}
	}
	$cekpo2 = "
			select a.id_po, count(b.id_pod) as jum 
			from pro_po a 
			left join pro_po_detail b on a.id_po = b.id_po 
			where a.tanggal_po = '" . $tgl_ds . "' and a.disposisi_po = -1 and id_wilayah = '" . $wilayah . "' 
			group by a.id_po";
	$respo2 = $con->getResult($cekpo2);
	if (count($respo2) > 0) {
		foreach ($respo2 as $datpo2) {
			if ($datpo2['jum'] == 0) {
				$sqlpo1 = "delete from pro_po where id_po = '" . $datpo2['id_po'] . "'";
				$con->setQuery($sqlpo1);
				$oke  = $oke && !$con->hasError();
			}
		}
	}
	/* END TRANSAKSI DATA TABEL PO */

	$sql1 = "update pro_po_ds set is_submitted = 1" . $colext . " where id_ds = '" . $idr . "'";
	$con->setQuery($sql1);
	$oke  = $oke && !$con->hasError();

	$cek1 = "select a.id_master, a.inisial_cabang, a.inisial_segel, a.stok_segel, a.urut_dn, a.urut_segel 
				 from pro_master_cabang a join pro_po_ds b on a.id_master = b.id_wilayah where b.id_ds = '" . $idr . "' for update";
	$row1 = $con->getRecord($cek1);
	$idm1 = $row1['id_master'];
	$noDo = $row1['urut_dn'];
	$seal = $row1['urut_segel'];
	$arrRomawi 	= array("1" => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
	$arrDn 		= array();

	if ($row1['stok_segel'] < $jum_segel) {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "Maaf stok segel tidak cukup...", BASE_REFERER);
	} else {
		$cek2 = "select id_dsd, jumlah_segel, nomor_ref_dn, nomor_do from pro_po_ds_detail where id_ds = '" . $idr . "' order by nomor_urut_ds";
		$row2 = $con->getResult($cek2);
		foreach ($row2 as $data1) {
			$ext_col1 = "";
			if (in_array($data1['id_dsd'], $arr_segel)) {
				if ($data1['jumlah_segel'] && $data1['jumlah_segel'] > 1) {
					$segel_awal = $seal + 1;
					$segel_last = $seal + $data1['jumlah_segel'];
					$seal = $seal + $data1['jumlah_segel'];
				} else if ($data1['jumlah_segel'] && $data1['jumlah_segel'] == 1) {
					$segel_awal = $seal + $data1['jumlah_segel'];
					$segel_last = 0;
					$seal = $seal + $data1['jumlah_segel'];
				}
				$ext_col1 = ", nomor_segel_awal='" . $segel_awal . "', nomor_segel_akhir='" . $segel_last . "'";
			}

			$ext_col2 = "";
			if (!$data1['nomor_do']) {
				if (!array_key_exists($data1['nomor_ref_dn'], $arrDn)) {
					$noDo++;
					$nomor_do = $noDo;
					$arrDn[$data1['nomor_ref_dn']] = $noDo;
				} else {
					$nomor_do = $arrDn[$data1['nomor_ref_dn']];
				}
				$nomor_dn = "DN/" . $row1['inisial_cabang'] . "/" . str_pad($nomor_do, 6, '0', STR_PAD_LEFT);
				$ext_col2 = ", nomor_do = '" . $nomor_dn . "'";
			}

			$sql3 = "update pro_po_ds_detail set pre_segel = '" . $row1['inisial_segel'] . "' " . $ext_col1 . $ext_col2 . " where id_dsd = '" . $data1['id_dsd'] . "'";
			$con->setQuery($sql3);
			$oke  = $oke && !$con->hasError();
		}
		$sql4 = "update pro_master_cabang set urut_dn = '" . $noDo . "', stok_segel = stok_segel - " . $jum_segel . ", urut_segel = '" . $seal . "' where id_master = '" . $idm1 . "'";
		$con->setQuery($sql4);
		$oke  = $oke && !$con->hasError();
	}

	if ($oke) {
		$con->commit();
		$con->close();
		$flash->add("success", "Data DS telah disimpan", $url);
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}
}

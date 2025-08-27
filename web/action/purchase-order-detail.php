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
$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
$url 	= BASE_URL_CLIENT . "/purchase-order.php";
$tombol = htmlspecialchars($_POST["tombol_klik"], ENT_QUOTES);

if ($tombol == 1) {
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	$arrDelete 	= array();
	$arrDepo 	= array();
	$ada_oa 	= 0;
	$jum_po 	= 0;

	foreach ($_POST["dt1"] as $idx => $val) {
		$no_urut_po 	= htmlspecialchars($_POST['dt1'][$idx], ENT_QUOTES);
		$oa_flag 		= htmlspecialchars($_POST['dt2'][$idx], ENT_QUOTES);
		$ongkos_po 		= htmlspecialchars(str_replace(array(","), array(""), $_POST['dt3'][$idx]), ENT_QUOTES);
		$ext_oa_dr 		= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['ext_oa_dr'][$idx]), ENT_QUOTES);
		$mobil_po 		= htmlspecialchars($_POST['dt4'][$idx], ENT_QUOTES);
		$sopir_po 		= htmlspecialchars($_POST['dt5'][$idx], ENT_QUOTES);
		$tgl_eta_po 	= htmlspecialchars($_POST['dt6'][$idx], ENT_QUOTES);
		$jam_eta_po 	= htmlspecialchars($_POST['dt7'][$idx], ENT_QUOTES);
		$tgl_etl_po 	= htmlspecialchars($_POST['dt8'][$idx], ENT_QUOTES);
		$jam_etl_po 	= htmlspecialchars($_POST['dt9'][$idx], ENT_QUOTES);
		$terminal_po 	= htmlspecialchars($_POST['dt10'][$idx], ENT_QUOTES);
		$trip_po 		= htmlspecialchars($_POST['dt11'][$idx], ENT_QUOTES);
		$multidrop_po 	= htmlspecialchars($_POST['dt12'][$idx], ENT_QUOTES);
		$ket_po 		= htmlspecialchars($_POST['dt13'][$idx], ENT_QUOTES);
		$tgl_kirim_po 	= htmlspecialchars($_POST['dt14'][$idx], ENT_QUOTES);
		$volume_po 		= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['dt15'][$idx]), ENT_QUOTES);
		$ongkos_po_real = htmlspecialchars(str_replace(array(","), array(""), $_POST['dt16'][$idx]), ENT_QUOTES);
		$ongkos_po_real = ($ongkos_po_real ? $ongkos_po_real : '0');


		if ($ongkos_po_real > $ongkos_po) {
			$con->rollBack();
			$con->clearError();
			$con->close();
			$flash->add("error", "OA Transportir tidak boleh lebih besar dari OA Disetujui", BASE_REFERER);
		}

		// if(($ext_oa_dr < $ongkos_po) && $oa_flag == 0){
		// 	//echo '1'; exit;
		// 	$ada_oa++;
		// 	$sql2 = "
		// 		update pro_po_detail set no_urut_po = '".$no_urut_po."', tgl_kirim_po = '".$tgl_kirim_po."', tgl_eta_po = '".tgl_db($tgl_eta_po)."', 
		// 		jam_eta_po = '".$jam_eta_po."', tgl_etl_po = '".tgl_db($tgl_etl_po)."', jam_etl_po = '".$jam_etl_po."', volume_po = '".$volume_po."', 
		// 		mobil_po = '".$mobil_po."', sopir_po = '".$sopir_po."', ongkos_po = '".$ongkos_po."', ongkos_po_real = '".$ongkos_po_real."', 
		// 		terminal_po = '".$terminal_po."', trip_po = '".$trip_po."', multidrop_po = '".$multidrop_po."', ket_po = '".$ket_po."', oa_flag = '1' 
		// 		where id_pod = '".$idx."'
		// 	";
		// } else{
		// 	//echo '2'; exit;
		// 	$arrDelete[$no_urut_po] = $idx;
		// 	if(!array_key_exists($terminal_po, $arrDepo)) $arrDepo[$terminal_po] = array();
		// 	if(!in_array($idx, $arrDepo[$terminal_po])) array_push($arrDepo[$terminal_po], $idx);
		// 	$sql2 = "
		// 		update pro_po_detail set no_urut_po = '".$no_urut_po."', tgl_kirim_po = '".$tgl_kirim_po."', tgl_eta_po = '".tgl_db($tgl_eta_po)."', 
		// 		jam_eta_po = '".$jam_eta_po."', tgl_etl_po = '".tgl_db($tgl_etl_po)."', jam_etl_po = '".$jam_etl_po."', volume_po = '".$volume_po."', 
		// 		mobil_po = '".$mobil_po."', sopir_po = '".$sopir_po."', ongkos_po = '".$ongkos_po."', ongkos_po_real = '".$ongkos_po_real."', 
		// 		terminal_po = '".$terminal_po."', trip_po = '".$trip_po."', multidrop_po = '".$multidrop_po."', ket_po = '".$ket_po."' 
		// 		where id_pod = '".$idx."'
		// 	";
		// }

		$arrDelete[$no_urut_po] = $idx;
		if (!array_key_exists($terminal_po, $arrDepo)) $arrDepo[$terminal_po] = array();
		if (!in_array($idx, $arrDepo[$terminal_po])) array_push($arrDepo[$terminal_po], $idx);
		$sql2 = "
					update pro_po_detail set no_urut_po = '" . $no_urut_po . "', tgl_kirim_po = '" . $tgl_kirim_po . "', tgl_eta_po = '" . tgl_db($tgl_eta_po) . "', 
					jam_eta_po = '" . $jam_eta_po . "', tgl_etl_po = '" . tgl_db($tgl_etl_po) . "', jam_etl_po = '" . $jam_etl_po . "', volume_po = '" . $volume_po . "', 
					mobil_po = '" . $mobil_po . "', sopir_po = '" . $sopir_po . "', ongkos_po = '" . $ongkos_po . "', ongkos_po_real = '" . $ongkos_po_real . "', 
					terminal_po = '" . $terminal_po . "', trip_po = '" . $trip_po . "', multidrop_po = '" . $multidrop_po . "', ket_po = '" . $ket_po . "' 
					where id_pod = '" . $idx . "'
			";
		$con->setQuery($sql2);
		$oke  = $oke && !$con->hasError();
		$jum_po++;
	}

	if ($ada_oa > 0) {
		$ems1 = "select email_user from acl_user where id_role = 16";
		$sql1 = "update pro_po set ada_selisih = 2, po_approved = 1, tgl_approved = NOW(), is_new = 1 where id_po = '" . $idr . "'";
	} else {
		$ems1 = "";
		$sql1 = "update pro_po set po_approved = 1, tgl_approved = NOW(), is_new = 1 where id_po = '" . $idr . "'";
	}
	$con->setQuery($sql1);
	$oke  = $oke && !$con->hasError();

	ksort($arrDelete, SORT_NUMERIC);
	if (count($arrDelete) > 0) {
		$ip_user 	= $_SERVER['REMOTE_ADDR'];
		$pic_user 	= paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]);
		$wilayah 	= paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]);
		$tanggal_ds = date("Y/m/d");

		$cek2 = "select inisial_cabang, urut_spj, urut_ds from pro_master_cabang where id_master = '" . $wilayah . "' for update";
		$row2 = $con->getRecord($cek2);
		$arrRomawi 	= array("1" => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
		$tmp_ds 	= $row2['urut_ds'];
		$nspj 		= $row2['urut_spj'];

		foreach ($arrDepo as $idx => $val) {
			$cek1 = "select id_ds from pro_po_ds where id_wilayah = '" . $wilayah . "' and id_terminal = '" . $idx . "' and tanggal_ds = '" . $tanggal_ds . "' and is_submitted = 0 
						 and is_loco = 0";
			$row1 = $con->getRecord($cek1);
			if ($row1['id_ds']) {
				foreach ($val as $idy => $nilai) {
					$sql5 = "insert into pro_po_ds_detail(id_ds, id_pod, id_po, id_prd, id_pr, id_plan, id_poc, tanggal_loading, jam_loading) 
								 (select '" . $row1['id_ds'] . "', a.id_pod, a.id_po, a.id_prd, b.id_pr, a.id_plan, c.id_poc, a.tgl_etl_po, a.jam_etl_po from pro_po_detail a 
								 join pro_pr_detail b on a.id_prd = b.id_prd join pro_po_customer_plan c on a.id_plan = c.id_plan where a.id_pod = '" . $nilai . "')";
					$con->setQuery($sql5);
					$oke  = $oke && !$con->hasError();
				}
			} else {
				$tmp_ds = $tmp_ds + 1;
				$nom_ds = str_pad($tmp_ds, 4, '0', STR_PAD_LEFT) . '/LOG/' . $row2['inisial_cabang'] . '/' . $arrRomawi[intval(date("m"))] . '/' . date("Y");

				$sql5 = "insert into pro_po_ds(id_wilayah, id_terminal, nomor_ds, tanggal_ds, created_time, created_ip, created_by) values ('" . $wilayah . "', '" . $idx . "', 
							'" . $nom_ds . "', '" . $tanggal_ds . "', NOW(), '" . $ip_user . "', '" . $pic_user . "')";
				$res2 = $con->setQuery($sql5);
				$oke  = $oke && !$con->hasError();

				foreach ($val as $idy => $nilai) {
					$sql6 = "insert into pro_po_ds_detail(id_ds, id_pod, id_po, id_prd, id_pr, id_plan, id_poc, tanggal_loading, jam_loading) 
								 (select '" . $res2 . "', a.id_pod, a.id_po, a.id_prd, b.id_pr, a.id_plan, c.id_poc, a.tgl_etl_po, a.jam_etl_po from pro_po_detail a 
								  join pro_pr_detail b on a.id_prd = b.id_prd join pro_po_customer_plan c on a.id_plan = c.id_plan where a.id_pod = '" . $nilai . "')";
					$con->setQuery($sql6);
					$oke  = $oke && !$con->hasError();
				}
			}
		}

		foreach ($arrDelete as $id_pod) {
			$nspj++;
			$sql7 = "update pro_po_detail set no_spj = '" . $row2['inisial_cabang'] . "-" . str_pad($nspj, 6, '0', STR_PAD_LEFT) . "' where id_pod = '" . $id_pod . "'";
			$con->setQuery($sql7);
			$oke  = $oke && !$con->hasError();
		}
		$sql8 = "update pro_master_cabang set urut_spj = '" . $nspj . "', urut_ds = '" . $tmp_ds . "' where id_master = '" . $wilayah . "'";
		$con->setQuery($sql8);
		$oke  = $oke && !$con->hasError();
	}

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
			$mail->Subject = "Verifikasi PO untuk Truck dan Sopir  [" . date('d/m/Y H:i:s') . "]";
			$mail->msgHTML(paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " meminta anda untuk melakukan verifikasi PO<p>" . BASE_SERVER . "</p>");
			//$mail->send();
		}

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
} else if ($tombol == 2) {
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	foreach ($_POST["dt1"] as $idx => $val) {
		$no_urut_po 	= htmlspecialchars($_POST['dt1'][$idx], ENT_QUOTES);
		$oa_flag 		= htmlspecialchars($_POST['dt2'][$idx], ENT_QUOTES);
		$ongkos_po 		= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['dt3'][$idx]), ENT_QUOTES);
		$mobil_po 		= htmlspecialchars($_POST['dt4'][$idx], ENT_QUOTES);
		$sopir_po 		= htmlspecialchars($_POST['dt5'][$idx], ENT_QUOTES);
		$tgl_eta_po 	= htmlspecialchars($_POST['dt6'][$idx], ENT_QUOTES);
		$jam_eta_po 	= htmlspecialchars($_POST['dt7'][$idx], ENT_QUOTES);
		$tgl_etl_po 	= htmlspecialchars($_POST['dt8'][$idx], ENT_QUOTES);
		$jam_etl_po 	= htmlspecialchars($_POST['dt9'][$idx], ENT_QUOTES);
		$terminal_po 	= htmlspecialchars($_POST['dt10'][$idx], ENT_QUOTES);
		$trip_po 		= htmlspecialchars($_POST['dt11'][$idx], ENT_QUOTES);
		$multidrop_po 	= htmlspecialchars($_POST['dt12'][$idx], ENT_QUOTES);
		$ket_po 		= htmlspecialchars($_POST['dt13'][$idx], ENT_QUOTES);
		$tgl_kirim_po 	= htmlspecialchars($_POST['dt14'][$idx], ENT_QUOTES);
		$volume_po 		= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['dt15'][$idx]), ENT_QUOTES);
		$ongkos_po_real = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['dt16'][$idx]), ENT_QUOTES);
		$ongkos_po_real = ($ongkos_po_real ? $ongkos_po_real : '0');

		$sql1 = "
				update pro_po_detail set no_urut_po = '" . $no_urut_po . "', tgl_kirim_po = '" . $tgl_kirim_po . "', tgl_eta_po = '" . tgl_db($tgl_eta_po) . "', 
				jam_eta_po = '" . $jam_eta_po . "', tgl_etl_po = '" . tgl_db($tgl_etl_po) . "', jam_etl_po = '" . $jam_etl_po . "', volume_po = '" . $volume_po . "', 
				mobil_po = '" . $mobil_po . "', sopir_po = '" . $sopir_po . "', ongkos_po = '" . $ongkos_po . "', ongkos_po_real = '" . $ongkos_po_real . "', 
				terminal_po = '" . $terminal_po . "', trip_po = '" . $trip_po . "', multidrop_po = '" . $multidrop_po . "', ket_po = '" . $ket_po . "' 
				where id_pod = '" . $idx . "'
			";
		$con->setQuery($sql1);
		$oke  = $oke && !$con->hasError();
	}

	$sql2 = "update pro_po set disposisi_po = 2, po_approved = 0, is_new = 1 where id_po = '" . $idr . "'";
	$con->setQuery($sql2);
	$oke  = $oke && !$con->hasError();

	if ($oke) {
		$ems1 = "select email_user from acl_user where id_role = 12 and id_transportir = (select id_transportir from pro_po where id_po = '" . $idr . "')";
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
		$mail->Subject = "Verifikasi PO untuk Truck dan Sopir  [" . date('d/m/Y H:i:s') . "]";
		$mail->msgHTML(paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " meminta anda untuk melakukan verifikasi PO<p>" . BASE_SERVER . "</p>");
		$mail->send();

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
} else if ($tombol == 3) {

	//Source Lama
	// $oke = true;
	// $con->beginTransaction();
	// $con->clearError();

	// if (count($_POST["dt4"]) > 0) {
	// 	foreach ($_POST["dt4"] as $idx => $val) {
	// 		$ongkos_po 		= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['dt3'][$idx]), ENT_QUOTES);
	// 		$mobil_po 		= htmlspecialchars($_POST['dt4'][$idx], ENT_QUOTES);
	// 		$sopir_po 		= htmlspecialchars($_POST['dt5'][$idx], ENT_QUOTES);
	// 		$tgl_eta_po 	= htmlspecialchars($_POST['dt6'][$idx], ENT_QUOTES);
	// 		$ongkos_po_real = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['dt16'][$idx]), ENT_QUOTES);
	// 		$ongkos_po_real = ($ongkos_po_real ? $ongkos_po_real : '0');
	// 		if ($ongkos_po) {
	// 			$sql1 = "
	// 					update pro_po_detail set mobil_po = '" . $mobil_po . "', sopir_po = '" . $sopir_po . "', tgl_kirim_po = '" . tgl_db($tgl_eta_po) . "', 
	// 					tgl_eta_po = '" . tgl_db($tgl_eta_po) . "', ongkos_po = '" . $ongkos_po . "', ongkos_po_real = '" . $ongkos_po_real . "' 
	// 					where id_pod = '" . $idx . "'
	// 				";
	// 			$con->setQuery($sql1);
	// 			$oke  = $oke && !$con->hasError();
	// 		}
	// 	}
	// }

	// if ($oke) {
	// 	$con->commit();
	// 	$con->close();
	// 	header("location: " . $url);
	// 	exit();
	// } else {
	// 	$con->rollBack();
	// 	$con->clearError();
	// 	$con->close();
	// 	$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	// }


	//Source Baru
	$oke = true;
	$con->beginTransaction();
	$con->clearError();

	// Token Bearer
	$token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJJZCI6MTE1OSwiTmFtZSI6InByb2VuZXJnaSIsIlJvbGUiOiJhZG1fcHJvZW5lcmdpIiwiQ29tcGFueSI6NjA2LCJVc2VyUG9kSWQiOjAsImlzcyI6Ik9TTE9HIDUgQVBJIn0.H-ljfy7I0zVzpvXsar3FddpUT2RHChNaEP8uw50kmV8';
	$logFilePath = realpath(__DIR__ . '/../../post-data-api-oslog.log.txt');

	if (count($_POST["dt4"]) > 0) {
		foreach ($_POST["dt4"] as $idx => $val) {
			$ongkos_po 		= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['dt3'][$idx]), ENT_QUOTES);
			$mobil_po 		= htmlspecialchars($_POST['dt4'][$idx], ENT_QUOTES);
			$sopir_po 		= htmlspecialchars($_POST['dt5'][$idx], ENT_QUOTES);
			$tgl_eta_po 	= htmlspecialchars($_POST['dt6'][$idx], ENT_QUOTES);
			$ongkos_po_real = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['dt16'][$idx]), ENT_QUOTES);
			$ongkos_po_real = ($ongkos_po_real ? $ongkos_po_real : '0');

			if ($ongkos_po_real > $ongkos_po) {
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", "OA Transportir tidak boleh lebih besar dari OA Disetujui", BASE_REFERER);
			}

			if ($ongkos_po) {
				$sql1 = "update pro_po_detail set mobil_po = '" . $mobil_po . "', sopir_po = '" . $sopir_po . "', tgl_kirim_po = '" . tgl_db($tgl_eta_po) . "', tgl_eta_po = '" . tgl_db($tgl_eta_po) . "', ongkos_po = '" . $ongkos_po . "', ongkos_po_real = '" . $ongkos_po_real . "' where id_pod = '" . $idx . "'";

				$con->setQuery($sql1);
				$oke  = $oke && !$con->hasError();

				$query_mobil = "SELECT * FROM pro_master_transportir_mobil WHERE id_master = '" . $mobil_po . "'";
				$row_mobil = $con->getRecord($query_mobil);

				$query_sopir = "SELECT * FROM pro_master_transportir_sopir WHERE id_master = '" . $sopir_po . "'";
				$row_sopir = $con->getRecord($query_sopir);

				$query = "SELECT a.id_dsd as id, a.nomor_do as nomor_dn, o.nomor_ds, o.tanggal_ds, b.no_spj, k.nomor_plat, l.nama_sopir, 
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
				JOIN pro_master_wilayah_angkut s on e.id_wil_oa = s.id_master and e.prov_survey = s.id_prov and e.kab_survey = s.id_kab WHERE 1=1 AND k.link_gps = 'OSLOG' AND b.id_pod='" . $idx . "'";
				$row = $con->getRecord($query);

				if ($row['nomor_dn'] != NULL) {
					if ($row && ($row['is_loaded'] == 0 || $row['is_cancel'] == 0)) {
						// URL API yang akan diakses
						$url_driver = 'https://oslog.id/apiv5/user-pod/search';

						$data_driver = [
							"paging" => [
								"start" => 0,
								"length" => 1
							],
							"columns" => [
								[
									"name" => "name",
									"logic_operator" => "like",
									"value" => $row_sopir['nama_sopir'], // nama_sopir
									"operator" => "AND"
								]
							]
						];

						// Mengonversi data ke format JSON
						$jsonData_driver = json_encode($data_driver);

						// Inisialisasi cURL
						$ch_driver = curl_init($url_driver);

						// Setel opsi cURL
						curl_setopt($ch_driver, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch_driver, CURLOPT_POST, true);
						curl_setopt($ch_driver, CURLOPT_POSTFIELDS, $jsonData_driver);
						curl_setopt($ch_driver, CURLOPT_HTTPHEADER, [
							'Content-Type: application/json',
							'Authorization: Bearer ' . $token,
							'Content-Length: ' . strlen($jsonData_driver)
						]);

						// Eksekusi permintaan dan ambil respons
						$response_driver = curl_exec($ch_driver);

						// Cek jika terjadi kesalahan
						if (curl_errno($ch_driver)) {
							$con->rollBack();
							$con->clearError();
							$con->close();
							$flash->add("error", "Server Error", BASE_REFERER);
							exit();
						} else {
							$hasil_driver = json_decode($response_driver, true);
							curl_close($ch_driver);
							// echo json_encode($hasil_driver);
							// exit();
							if (!empty($hasil_driver['data'])) {
								$url_api = 'https://oslog.id/javaz-api/shipment-syop/edit/' . $row['id'];

								$data = [
									"nomor_plat" 			=> $row_mobil['nomor_plat'],
									"nama_sopir" 			=> $row_sopir['nama_sopir'],
									"tgl_eta" 				=> $row['tgl_eta_po'] . ($row['jam_eta_po'] ? ' ' . $row['jam_eta_po'] : ' 00:00:00'),
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
									exit();
								} else {
									$hasil = json_decode($response, true);
									curl_close($ch);
									// echo $hasil['code'];
									if ($hasil['status'] == true) {
										$oke = true;
									} else {
										$con->rollBack();
										$con->clearError();
										$con->close();
										$flash->add("error", $hasil['message'], BASE_REFERER);
										exit();
									}
								}
							} else {
								$con->rollBack();
								$con->clearError();
								$con->close();
								$flash->add("error", "Nama Driver '" . $row['nama_sopir'] . "' tidak sesuai dengan OSLOG, silahkan cek lagi pada SYOP. Pastikan nama driver sama dengan user POD.", BASE_REFERER);
								exit();
							}
						}
					}
				}
			}
		}
	}

	if ($oke && $con !== null) {
		$con->commit();
		$con->close();
		header("location: " . $url);
		exit();
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "Transaction failed.", BASE_REFERER);
	}
}

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
$act 	= null;
if (isset($enk['act'])) $act = $enk['act'];
if (isset($_POST['act'])) $act = htmlspecialchars($_POST["act"], ENT_QUOTES);
$idr 	= htmlspecialchars($_POST["idr"], ENT_QUOTES);
$idw 	= htmlspecialchars($_POST["idw"], ENT_QUOTES);
$extend = isset($_POST["extend"]) ? htmlspecialchars($_POST["extend"], ENT_QUOTES) : null;
$revert = isset($_POST["revert"]) ? htmlspecialchars($_POST["revert"], ENT_QUOTES) : null;
$dis_lo = isset($_POST["dis_lo"]) ? htmlspecialchars($_POST["dis_lo"], ENT_QUOTES) : null;
$revisi_dr = htmlspecialchars($_POST["revisiDR"], ENT_QUOTES);
$backlog = htmlspecialchars($_POST["backlog"], ENT_QUOTES);
$id_accurate = htmlspecialchars($_POST["id_accurate"], ENT_QUOTES);
$no_so = htmlspecialchars($_POST["no_so"], ENT_QUOTES);
// $kode_customer = htmlspecialchars($_POST["kode_customer"], ENT_QUOTES);
$alamat = htmlspecialchars(paramDecrypt($_POST["alamat"]), ENT_QUOTES);
$tgl_kirim = htmlspecialchars($_POST["tgl_kirim"], ENT_QUOTES);


$filePhoto 	= htmlspecialchars($_FILES['attachment_condition']['name'], ENT_QUOTES);
$sizePhoto 	= htmlspecialchars($_FILES['attachment_condition']['size'], ENT_QUOTES);
$tempPhoto 	= htmlspecialchars($_FILES['attachment_condition']['tmp_name'], ENT_QUOTES);
$extPhoto 	= substr($filePhoto, strrpos($filePhoto, '.'));
$max_size	= 2 * 1024 * 1024;
$allow_type	= array(".jpg", ".jpeg", ".JPG", ".png", ".pdf");
$pathfile	= $public_base_directory . '/files/uploaded_user/urgent';
//$uploadnya = false;
$tgl_kirim = htmlspecialchars($_POST["tgl_kirim"], ENT_QUOTES);


//echo '<pre>'; print_r($_POST); exit;



$summary = '';
if (isset($_POST["summary"]))
	$summary = str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["summary"], ENT_QUOTES));
$backdis = isset($_POST["summary_revert"]) ? str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["summary_revert"], ENT_QUOTES)) : '';
$url 	= BASE_URL_CLIENT . "/purchase-request-detail.php?" . paramEncrypt("idr=" . $idr);
$pic	= paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']);

$oke = true;
$con->beginTransaction();
$con->clearError();
$item_po = [];

if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 9) {
	$arrDepo = array();

	foreach ($_POST['dt3'] as $idx1 => $val1) {
		//$dt2 = htmlspecialchars(str_replace(array(".",","),array("",""),$_POST['volori'][$idx1]), ENT_QUOTES);
		foreach ($_POST['dt3'][$idx1] as $idx2 => $val2) {
			$cek 	= htmlspecialchars($_POST['cek'][$idx1][$idx2], ENT_QUOTES);
			$dt2 	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['volori'][$idx1][$idx2]), ENT_QUOTES);
			$dt3 	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['dt3'][$idx1][$idx2]), ENT_QUOTES);
			$dt3a 	= htmlspecialchars($_POST['newSplitdt3'][$idx1][$idx2], ENT_QUOTES);
			$dt4 	= htmlspecialchars($_POST['dt4'][$idx1][$idx2], ENT_QUOTES);
			$dt5 	= htmlspecialchars($_POST['dt5'][$idx1][$idx2], ENT_QUOTES);
			$split 	= ($dt3a && $dt3 != $dt2) ? ", splitted_from = '" . $idx2 . "'" : "";

			if ($cek) {
				$sql1 = "update pro_pr_detail set vol_ori = '" . $dt2 . "', volume = '" . $dt3 . "', pr_mobil = '" . $dt4 . "' " . $split . " where id_prd = '" . $idx2 . "'";
				$con->setQuery($sql1);
				$oke  = $oke && !$con->hasError();

				if ($dt4 == 3 && $oke) {
					if (!array_key_exists($dt5, $arrDepo)) $arrDepo[$dt5] = array();
					if (!in_array($idx2, $arrDepo[$dt5])) array_push($arrDepo[$dt5], $idx2);
				}
			}
		}
	}
	if (!is_array_empty($_POST["newdt3"])) {
		//print_r($_POST['newSplitdt3']); echo '<br />';
		foreach ($_POST['newdt3'] as $idx => $val1) {
			foreach ($_POST['newdt3'][$idx] as $idy => $val2) {
				$dt3 	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['newdt3'][$idx][$idy]), ENT_QUOTES);
				$dt3a 	= $_POST['newSplitdt3'][$idx];
				$dt4 	= htmlspecialchars($_POST['newdt4'][$idx][$idy], ENT_QUOTES);
				$dt5 	= htmlspecialchars($_POST['newdt5'][$idx][$idy], ENT_QUOTES);
				if (count($dt3a) > 0) {
					$split = "";
					foreach ($dt3a as $idtmpx => $valtmp) {
						$nilai 	= htmlspecialchars($_POST['newSplitdt3'][$idx][$idtmpx], ENT_QUOTES);
						$split 	= ($nilai) ? $idtmpx : 'NULL';
					}
				}
				//print_r($idtmpx); echo '<br />';

				$sql3 	= "
					insert into pro_pr_detail(id_pr, id_plan, produk, volume, vol_ori, transport, pr_mobil, pr_top, pr_actual_top, pr_pelanggan, pr_ar_notyet, 
					pr_ar_satu, pr_ar_dua, pr_kredit_limit, pr_terminal, pr_vendor, pr_harga_beli, pr_price_list, nomor_lo_pr, schedule_payment, is_approved, splitted_from, 
					splitted_from_pr, vol_ori_pr, no_do_acurate)
					(select distinct id_pr, id_plan, produk, '" . $dt3 . "', vol_ori, transport, '" . $dt4 . "', pr_top, pr_actual_top, pr_pelanggan, pr_ar_notyet, pr_ar_satu, 
					pr_ar_dua, pr_kredit_limit, pr_terminal, pr_vendor, pr_harga_beli, pr_price_list, nomor_lo_pr, schedule_payment, is_approved, " . $split . ", 
					splitted_from_pr, vol_ori_pr, no_do_acurate 
					from pro_pr_detail where id_plan = '" . $idx . "' and id_prd = '" . $idtmpx . "')";
				//echo $sql3.'<br />';

				$idx2 = $con->setQuery($sql3);
				$oke  = $oke && !$con->hasError();

				if ($dt4 == 3 && $oke) {
					if (!array_key_exists($dt5, $arrDepo)) $arrDepo[$dt5] = array();
					if (!in_array($idx2, $arrDepo[$dt5])) array_push($arrDepo[$dt5], $idx2);
				}
			}
		}
	}
	//exit

	$sql2 = "update pro_pr set disposisi_pr = 7, is_edited = 0 where id_pr = '" . $idr . "'";
	$con->setQuery($sql2);
	$oke  = $oke && !$con->hasError();

	if ($oke && count($arrDepo) > 0) {
		$ip_user 	= $_SERVER['REMOTE_ADDR'];
		$pic_user 	= paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]);
		$wilayah 	= paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]);
		$tanggal_ds = date("Y/m/d");

		$cek2 = "select inisial_cabang, urut_ds from pro_master_cabang where id_master = '" . $wilayah . "' for update";
		$row2 = $con->getRecord($cek2);
		$arrRomawi 	= array("1" => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
		$tmp_ds 	= $row2['urut_ds'];

		foreach ($arrDepo as $idx => $val) {
			$cek1 = "select id_ds from pro_po_ds where id_wilayah = '" . $wilayah . "' and id_terminal = '" . $idx . "' and tanggal_ds = '" . $tanggal_ds . "' and is_submitted = 0 
						 and is_loco = 1";
			$row1 = $con->getRecord($cek1);
			if ($row1['id_ds']) {
				foreach ($val as $idy => $nilai) {
					$sql5 = "insert into pro_po_ds_detail(id_ds, id_pod, id_po, id_prd, id_pr, id_plan, id_poc, tanggal_loading, jam_loading) 
								 (select '" . $row1['id_ds'] . "', 0, 0, a.id_prd, a.id_pr, a.id_plan, b.id_poc, b.tanggal_kirim, '04:00:00' from pro_pr_detail a 
								 join pro_po_customer_plan b on a.id_plan = b.id_plan where a.id_prd = '" . $nilai . "')";
					$con->setQuery($sql5);
					$oke  = $oke && !$con->hasError();
				}
			} else {
				$tmp_ds = $tmp_ds + 1;
				$nom_ds = str_pad($tmp_ds, 4, '0', STR_PAD_LEFT) . '/LOG/LOCO/' . $row2['inisial_cabang'] . '/' . $arrRomawi[intval(date("m"))] . '/' . date("Y");

				$sql5 = "insert into pro_po_ds(id_wilayah, id_terminal, nomor_ds, tanggal_ds, is_loco, created_time, created_ip, created_by) values ('" . $wilayah . "', 
							'" . $idx . "', '" . $nom_ds . "', '" . $tanggal_ds . "', 1, NOW(), '" . $ip_user . "', '" . $pic_user . "')";
				$res2 = $con->setQuery($sql5);
				$oke  = $oke && !$con->hasError();

				foreach ($val as $idy => $nilai) {
					$sql6 = "insert into pro_po_ds_detail(id_ds, id_pod, id_po, id_prd, id_pr, id_plan, id_poc, tanggal_loading, jam_loading) 
								 (select '" . $res2 . "', 0, 0, a.id_prd, a.id_pr, a.id_plan, b.id_poc, b.tanggal_kirim, '04:00:00' from pro_pr_detail a 
								  join pro_po_customer_plan b on a.id_plan = b.id_plan where a.id_prd = '" . $nilai . "')";
					$con->setQuery($sql6);
					$oke  = $oke && !$con->hasError();
				}
			}
		}

		$sql7 = "update pro_master_cabang set urut_ds = '" . $tmp_ds . "' where id_master = '" . $wilayah . "'";
		$con->setQuery($sql7);
		$oke  = $oke && !$con->hasError();
	}
} else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 3) {
	if (is_array_empty($_POST["cek"])) {
		$oke = false;
		$con->close();
		$flash->add("error", "Anda belum memilih data DR", BASE_REFERER);
	} else {
		$oke = true;
		if ($revert == 1) {
			$ems1 = "select email_user from acl_user where id_role = 5";
			$sbjk = "Pengembalian DR [" . date('d/m/Y H:i:s') . "]";
			$pesn = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " meminta anda untuk merevisi ulang DR";

			$sql2 = "update pro_pr set revert_ceo = 1, revert_ceo_summary = '" . $backdis . "', purchasing_result = 0, disposisi_pr = 3 where id_pr = '" . $idr . "'";
			$con->setQuery($sql2);
			$oke  = $oke && !$con->hasError();
			$url  = BASE_URL_CLIENT . "/purchase-request.php";
		} else if ($revert == 2) {
			foreach ($_POST['ket'] as $idx => $val) {
				$cek = htmlspecialchars($_POST['cek'][$idx], ENT_QUOTES);
				$dp2 = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['dp2'][$idx]), ENT_QUOTES);
				$ket = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['ket'][$idx]), ENT_QUOTES);
				$tmp = ($ket) ? "volume = '" . $ket . "', vol_ket = '0', " : "vol_ket = '0', ";
				$sql1 = "update pro_pr_detail set " . $tmp . " is_approved = '" . $cek . "', pr_harga_beli = '" . $dp2 . "' where id_prd = '" . $idx . "'";
				$con->setQuery($sql1);
				$oke  = $oke && !$con->hasError();
			}
			$sql2 = "update pro_pr set coo_summary = '" . $summary . "', coo_result = 1, coo_pic = '" . $pic . "', coo_tanggal = NOW(), disposisi_pr = 6 where id_pr = '" . $idr . "'";
			$con->setQuery($sql2);
			$oke  = $oke && !$con->hasError();

			$cek1 = "
					select a.id_pr, a.id_plan, a.jumlah, b.jumlah as jumlah_approved
					from(select count(*) as jumlah, id_plan, id_pr from pro_pr_detail where 1=1 and id_pr = '" . $idr . "' group by id_pr, id_plan) a 
					left join(select count(*) as jumlah, id_plan, id_pr from pro_pr_detail where is_approved = 1 and id_pr = '" . $idr . "' group by id_pr, id_plan) b 
					on a.id_pr = b.id_pr and a.id_plan = b.id_plan";
			$res1 = $con->getResult($cek1);
			if (count($res1) > 0) {
				foreach ($res1 as $data1) {
					if (!$data1['jumlah_approved']) {
						$sql3 = "update pro_po_customer_plan set status_plan = 2  where id_plan = '" . $data1['id_plan'] . "'";
						$con->setQuery($sql3);
						$oke  = $oke && !$con->hasError();
					}
				}
			}

			$ems1 = "select email_user from acl_user where id_role = 9 and id_wilayah = '" . $idw . "'";
			$sbjk = "Persetujuan DR [" . date('d/m/Y H:i:s') . "]";
			$pesn = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " telah melakukan verifikasi DR";
		}
	}
} else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 21) {
	if (is_array_empty($_POST["cek"])) {
		$oke = false;
		$con->close();
		$flash->add("error", "Anda belum memilih data DR", BASE_REFERER);
	} else {
		$oke = true;
		if ($revert == 1) {
			$ems1 = "select email_user from acl_user where id_role = 5";
			$sbjk = "Pengembalian DR [" . date('d/m/Y H:i:s') . "]";
			$pesn = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " meminta anda untuk merevisi ulang DR";

			$sql2 = "update pro_pr set revert_ceo = 1, revert_ceo_summary = '" . $backdis . "', purchasing_result = 0, disposisi_pr = 3 where id_pr = '" . $idr . "'";
			$con->setQuery($sql2);
			$oke  = $oke && !$con->hasError();
			$url  = BASE_URL_CLIENT . "/purchase-request.php";
		} else if ($revert == 2) {
			foreach ($_POST['ket'] as $idx => $val) {
				$cek = htmlspecialchars($_POST['cek'][$idx], ENT_QUOTES);
				$dp2 = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['dp2'][$idx]), ENT_QUOTES);
				$ket = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['ket'][$idx]), ENT_QUOTES);
				$tmp = ($ket) ? "volume = '" . $ket . "', vol_ket = '0', " : "vol_ket = '0', ";
				$sql1 = "update pro_pr_detail set " . $tmp . " is_approved = '" . $cek . "', pr_harga_beli = '" . $dp2 . "' where id_prd = '" . $idx . "'";
				$con->setQuery($sql1);
				$oke  = $oke && !$con->hasError();
			}
			$sql2 = "update pro_pr set ceo_summary = '" . $summary . "', ceo_result = 1, ceo_pic = '" . $pic . "', ceo_tanggal = NOW(), disposisi_pr = 6 where id_pr = '" . $idr . "'";
			$con->setQuery($sql2);
			$oke  = $oke && !$con->hasError();

			$cek1 = "
					select a.id_pr, a.id_plan, a.jumlah, b.jumlah as jumlah_approved
					from(select count(*) as jumlah, id_plan, id_pr from pro_pr_detail where 1=1 and id_pr = '" . $idr . "' group by id_pr, id_plan) a 
					left join(select count(*) as jumlah, id_plan, id_pr from pro_pr_detail where is_approved = 1 and id_pr = '" . $idr . "' group by id_pr, id_plan) b 
					on a.id_pr = b.id_pr and a.id_plan = b.id_plan";
			$res1 = $con->getResult($cek1);
			if (count($res1) > 0) {
				foreach ($res1 as $data1) {
					if (!$data1['jumlah_approved']) {
						$sql3 = "update pro_po_customer_plan set status_plan = 2  where id_plan = '" . $data1['id_plan'] . "'";
						$con->setQuery($sql3);
						$oke  = $oke && !$con->hasError();
					}
				}
			}

			$ems1 = "select email_user from acl_user where id_role = 9 and id_wilayah = '" . $idw . "'";
			$sbjk = "Persetujuan DR [" . date('d/m/Y H:i:s') . "]";
			$pesn = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " telah melakukan verifikasi DR";
		}
	}
} else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 4) {
	if (is_array_empty($_POST["cek"])) {
		$oke = false;
		$con->close();
		$flash->add("error", "Anda belum memilih data DR", BASE_REFERER);
	} else {
		$oke = true;
		if ($revert == 1) {
			$ems1 = "select email_user from acl_user where id_role = 5";
			$sbjk = "Pengembalian DR [" . date('d/m/Y H:i:s') . "]";
			$pesn = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " meminta anda untuk merevisi ulang DR";

			$sql2 = "update pro_pr set revert_cfo = 1, revert_cfo_summary = '" . $backdis . "', purchasing_result = 0, disposisi_pr = 3 where id_pr = '" . $idr . "'";
			$con->setQuery($sql2);
			$oke  = $oke && !$con->hasError();
			$url  = BASE_URL_CLIENT . "/purchase-request.php";
		} else if ($revert == 2) {
			if ($extend == 1) {
				foreach ($_POST['ket'] as $idx => $val) {
					$cek = htmlspecialchars($_POST['cek'][$idx], ENT_QUOTES);
					$dp2 = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['dp2'][$idx]), ENT_QUOTES);
					$ket = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['ket'][$idx]), ENT_QUOTES);
					$sql1 = "update pro_pr_detail set vol_ket = '" . $ket . "', is_approved = '" . $cek . "', pr_harga_beli = '" . $dp2 . "' where id_prd = '" . $idx . "'";
					$con->setQuery($sql1);
					$oke  = $oke && !$con->hasError();
				}
				$ems1 = "select email_user from acl_user where id_role = 3";
				$sbjk = "Persetujuan DR [" . date('d/m/Y H:i:s') . "]";
				$pesn = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " meminta persetujuan untuk DR";

				$sql2 = "update pro_pr set cfo_summary = '" . $summary . "', cfo_result = 1, cfo_pic = '" . $pic . "', cfo_tanggal = NOW(), is_ceo = 1 where id_pr = '" . $idr . "'";
				$con->setQuery($sql2);
				$oke  = $oke && !$con->hasError();
			} else if ($extend == 2) {
				foreach ($_POST['ket'] as $idx => $val) {
					$cek = htmlspecialchars($_POST['cek'][$idx], ENT_QUOTES);
					$dp2 = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['dp2'][$idx]), ENT_QUOTES);
					$ket = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['ket'][$idx]), ENT_QUOTES);
					$tmp = ($ket) ? "volume = '" . $ket . "', " : "";
					$sql1 = "update pro_pr_detail set " . $tmp . " is_approved = '" . $cek . "', pr_harga_beli = '" . $dp2 . "' where id_prd = '" . $idx . "'";
					$con->setQuery($sql1);
					$oke  = $oke && !$con->hasError();
				}

				$sql2 = "update pro_pr set cfo_summary = '" . $summary . "', cfo_result = 1, cfo_pic = '" . $pic . "', cfo_tanggal = NOW(), disposisi_pr = 5, logistik_result = 1 where id_pr = '" . $idr . "'";
				$con->setQuery($sql2);
				$oke  = $oke && !$con->hasError();

				$cek1 = "
						select a.id_pr, a.id_plan, a.jumlah, b.jumlah as jumlah_approved
						from(select count(*) as jumlah, id_plan, id_pr from pro_pr_detail where 1=1 and id_pr = '" . $idr . "' group by id_pr, id_plan) a 
						left join(select count(*) as jumlah, id_plan, id_pr from pro_pr_detail where is_approved = 1 and id_pr = '" . $idr . "' group by id_pr, id_plan) b 
						on a.id_pr = b.id_pr and a.id_plan = b.id_plan";
				$res1 = $con->getResult($cek1);
				if (count($res1) > 0) {
					foreach ($res1 as $data1) {
						if (!$data1['jumlah_approved']) {
							$sql3 = "update pro_po_customer_plan set status_plan = 2  where id_plan = '" . $data1['id_plan'] . "'";
							$con->setQuery($sql3);
							$oke  = $oke && !$con->hasError();
						}
					}
				}

				$ems1 = "select email_user from acl_user where id_role = 9 and id_wilayah = '" . $idw . "'";
				$sbjk = "Persetujuan DR [" . date('d/m/Y H:i:s') . "]";
				$pesn = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " telah melakukan verifikasi DR";
			}
		}
	}
} else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 5) {
	$wilayah	= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

	if (is_array_empty($_POST["cek"])) {
		$oke = false;
		$con->close();
		$flash->add("error", "Anda belum memilih data DR", BASE_REFERER);
	} else {
		if ($revisi_dr == 1) {
			$oke  = true;

			$sql1 = 'update pro_pr set disposisi_pr = 3, purchasing_result= 0 where id_pr = "' . $idr . '"';
			$con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();

			$sql2 = 'delete from new_pro_inventory_depot where id_pr = "' . $idr . '"';
			$con->setQuery($sql2);
			$oke  = $oke && !$con->hasError();

			$sql4 = 'delete from new_pro_inventory_potongan_stock where id_pr = "' . $idr . '"';
			$con->setQuery($sql4);
			$oke  = $oke && !$con->hasError();

			//$sql3 = 'update pro_pr_detail set vol_potongan = NULL, no_do_syop = NULL, nomor_lo_pr = NULL where id_pr = "' . $idr . '"';
			$sql3 = 'update pro_pr_detail set vol_potongan = NULL, nomor_lo_pr = NULL where id_pr = "' . $idr . '"';
			$con->setQuery($sql3);
			$oke  = $oke && !$con->hasError();

			$url  = BASE_URL_CLIENT . "/purchase-request.php";
		} else {

			$oke  = true;
			$cek = "select * from pro_pr a where a.id_pr = '" . $idr . "'";
			$row = $con->getRecord($cek);
			if ($row) {
				$ceo_result = $row['ceo_result'];
				$disposisi_pr = $row['disposisi_pr'];
			}

			$ambilIdWilayah = "select id_wilayah FROM pro_pr WHERE id_pr='" . $idr . "'";
			$rowIdWilayah = $con->getRecord($ambilIdWilayah);
			$query_cabang = "select inisial_cabang from pro_master_cabang where id_master = '" . $rowIdWilayah['id_wilayah'] . "' for update";
			$row1 = $con->getRecord($query_cabang);
			$inisial_cabang = $row1['inisial_cabang'];

			// Mendapatkan nomor urut untuk DO
			$arrRomawi = array("1" => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
			// $year = date("y");
			$date_now = date("Y-m-d");

			$month = date("m-Y");
			$bulan_kirim = date('m-Y', strtotime($tgl_kirim));

			if ($bulan_kirim > $month) {
				$bulan_romawi = $arrRomawi[intval(date('m', strtotime($tgl_kirim)))];
				$year = date('y', strtotime($tgl_kirim));
			} else {
				// Mendapatkan nomor bulan romawi
				$bulan_romawi = $arrRomawi[intval(date("m"))];
				$year = date("y");
			}

			$query_no_do = "select * FROM pro_pr_detail WHERE cabang = '" . $inisial_cabang . "' AND (no_do_syop IS NOT NULL AND no_do_syop LIKE '%/" . $year . "/" . $bulan_romawi . "/%') ORDER BY no_do_syop DESC";
			$row2 = $con->getRecord($query_no_do);

			if ($row2) {
				$no_do_syop = $row2['no_do_syop'];
				$explode = explode("/", $no_do_syop);
				$year_do = $explode[3];

				if ($year_do == $year) {
					$urut_do = intval($explode[5]);
				} else {
					// Jika beralih tahun, nomor urut dimulai dari 1
					$urut_do = 0;
				}
			} else {
				// Jika tidak ada nomor DO sebelumnya, nomor urut dimulai dari 1
				$urut_do = 0;
			}

			// Jika beralih bulan, nomor urut dimulai dari 1
			if ($explode[4] != $bulan_romawi) {
				$urut_do = 0;
			}

			$kode_customer_array = [];

			foreach ($_POST['dp3'] as $idx => $val) {
				$chk 	= htmlspecialchars($_POST['cek'][$idx], ENT_QUOTES);
				$dt1 	= htmlspecialchars($_POST['dv1'][$idx], ENT_QUOTES);
				$dt2 	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['dp2'][$idx]), ENT_QUOTES);
				$dt7 	= htmlspecialchars($_POST['dp7'][$idx], ENT_QUOTES);
				$dt8 	= htmlspecialchars($_POST['dp8'][$idx], ENT_QUOTES);
				$ips 	= htmlspecialchars($_POST['ps1'][$idx], ENT_QUOTES);
				$ipr 	= htmlspecialchars($_POST['pr1'][$idx], ENT_QUOTES);
				$nop 	= htmlspecialchars($_POST['np1'][$idx], ENT_QUOTES);
				$dt13 	= htmlspecialchars($_POST['dp13'][$idx], ENT_QUOTES);
				$hd 	= htmlspecialchars($_POST['Harga_Dasar'][$idx], ENT_QUOTES);
				$oa 	= htmlspecialchars($_POST['Ongkos_Angkut'][$idx], ENT_QUOTES);
				$pbbkb 	= htmlspecialchars($_POST['PBBKB'][$idx], ENT_QUOTES);
				$jenis_penawaran = htmlspecialchars($_POST['jenis_penawaran'][$idx], ENT_QUOTES);
				$kode_customer 	= htmlspecialchars($_POST['kode_customer'][$idx], ENT_QUOTES);
				$dt10 	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['dp10'][$idx]), ENT_QUOTES);
				$volume = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['volume'][$idx]), ENT_QUOTES);
				$top_plan 	= htmlspecialchars($_POST['top_plan'][$idx], ENT_QUOTES);
				$credit_limit 	= htmlspecialchars($_POST['credit_limit'][$idx], ENT_QUOTES);
				$idpro 	    = htmlspecialchars($_POST['id_pro'][$idx], ENT_QUOTES);
				$idplans 	    = htmlspecialchars($_POST['id_plan'][$idx], ENT_QUOTES);

				$cek1 = "select inisial_cabang, urut_lo from pro_master_cabang where id_master = '" . $idw . "' for update";
				$row1 = $con->getRecord($cek1);

				if (!isset($kode_customer_array[$kode_customer])) {
					$kode_customer_array[$kode_customer] = [];
					// $kode_customer_array[$kode_customer][$idplans]["items"] = [];
				}


				if (!isset($kode_customer_array[$kode_customer][$idplans])) {
					$kode_customer_array[$kode_customer][$idplans] = [
						'items' => [] // Inisialisasi array 'items' jika belum ada
					];
				}

				//cek no do apakah sudah ada
				$cek_nodo = "select no_do_syop from pro_pr_detail where id_prd = '" . $idx . "'";
				$row_nodo = $con->getRecord($cek_nodo);


				$tmp1 = $row1['urut_lo'];
				$tmp2 = array("1" => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");

				$eks1 = ($dt10 ? "vol_ori = '" . $dt10 . "', vol_ori_pr = '" . $dt10 . "'," : "vol_ori = volume, vol_ori_pr = volume,");
				//$eks1 = ($dt10 ? "vol_ori_pr = '".$dt10."'," : "vol_ori_pr = volume,");
				$sql1 = "
						update pro_pr_detail set " . $eks1 . " volume = '" . $volume . "', pr_top = '" . $top_plan . "', pr_kredit_limit = '" . $credit_limit . "', pr_vendor = '" . $dt1 . "', pr_harga_beli = '" . $dt2 . "', pr_price_list = '" . $dt7 . "', pr_po = '" . $dt13 . "',
						pr_terminal = '" . $dt8 . "' where id_prd = '" . $idx . "'
					";

				$cek_jam = "select submit_bm from pro_pr  where id_pr = '" . $idr . "'";
				$submit_bm = $con->getResult($cek_jam);

				$cek_cabang = "select inisial_cabang FROM pro_master_cabang WHERE id_master='" . $row['id_wilayah'] . "'";
				$row_cabang = $con->getRecord($cek_cabang);
				$sql_update_cabang = "update pro_pr_detail set cabang='" . $row_cabang['inisial_cabang'] . "' where id_prd = '" . $idx . "'";
				$con->setQuery($sql_update_cabang);
				$oke  = $oke && !$con->hasError();

				$get_gudang = "select * FROM vw_terminal_inventory_receive WHERE nomor_po_supplier='" . $nop . "'";
				$nama_gudang = $con->getRecord($get_gudang);

				//get gudang accurate
				$get_insial_cabang = "select * FROM pro_master_cabang WHERE id_master='" . $nama_gudang['id_cabang'] . "'";
				$row_inisial = $con->getRecord($get_insial_cabang);

				//get item by Detail PO Supplier
				$get_idpo = "select id_accurate FROM new_pro_inventory_vendor_po WHERE nomor_po='" . $nop . "'";
				$id_po_acc = $con->getRecord($get_idpo);

				//get kode accurate marketing
				$get_user = "SELECT b.kode_accurate FROM pro_po_customer_plan a 
							JOIN acl_user b ON a.created_by = b.fullname 
							WHERE a.id_plan='" . $idplans . "'";
				$kode_user = $con->getRecord($get_user);

				$query_po = http_build_query([
					'id' => $id_po_acc['id_accurate']
				]);

				$url_po = 'https://zeus.accurate.id/accurate/api/purchase-order/detail.do?' . $query_po;

				$result_po = curl_get($url_po);
				$detailItem_po = $result_po['d']['detailItem'];

				if ($jenis_penawaran == 'gabung_oa') {
					$kode_customer_array[$kode_customer][$idplans]["items"][] = [
						'itemNo' => 'PBBKB',
						'unitPrice' => $pbbkb,
						'quantity' => $dt10,
						'salesmanListNumber'=> $kode_user['kode_accurate']
					];
					$harga_dasar = $hd + $oa;
				} else if ($jenis_penawaran == 'gabung_pbbkb') {
					$kode_customer_array[$kode_customer][$idplans]["items"][] = [
						'itemNo' => 'NS-001',
						'unitPrice' => $oa,
						'quantity' => $dt10,
						'salesmanListNumber'=> $kode_user['kode_accurate']
					];
					$harga_dasar = ($hd) + ($pbbkb);
				} else if ($jenis_penawaran == 'break_all') {
					$kode_customer_array[$kode_customer][$idplans]["items"] = [
						[
							'itemNo' => 'PBBKB',
							'unitPrice' => $pbbkb,
							'quantity' => $dt10,
							'salesmanListNumber'=>$kode_user['kode_accurate']
						],
						[
							'itemNo' => 'NS-001',
							'unitPrice' => $oa,
							'quantity' => $dt10,
							'salesmanListNumber'=>$kode_user['kode_accurate']
						]
					];
					$harga_dasar = $hd;
				} else {
					$harga_dasar = $hd + $pbbkb + $oa;
				}

				foreach ($detailItem_po as $items) {
					if ($items['item']['itemType'] == 'INVENTORY') {
						$kode_customer_array[$kode_customer][$idplans]["items"][] = [
							'itemNo'       => $items['item']['no'],
							'quantity'     => $dt10,
							'unitPrice'    => $harga_dasar,
							'warehouseName' => $row_inisial['inisial_cabang'],
							'salesmanListNumber'=>$kode_user['kode_accurate']
						];
						// array_unshift($kode_customer_array[$kode_customer][$idplans]["items"], [
						// 	'itemNo'       => $items['item']['no'],
						// 	'quantity'     => $volume,
						// 	'unitPrice'    => $harga_dasar,
						// 	'warehouseName' => $row_inisial['inisial_cabang'],
						// ]);
						break;
					}
				}

				if ($chk && !$dis_lo) {
					//$tmp1++;

					$randnum = str_pad(mt_rand(1, 99999999), 6, '0', STR_PAD_LEFT);
					$noms = 'LO/' . $row1['inisial_cabang'] . '/' . $randnum;

					$urut_do++;
					// Cut off 3 digit dan 4 digit
					$no_do = sprintf("%04s", $urut_do);
					$noms_do = 'DO/' . 'PE/' . $inisial_cabang . '/' . $year . '/' . $bulan_romawi . '/' . $no_do;

					$nomor_do = ($row_nodo['no_do_syop'] ? "" : "no_do_syop = '$noms_do',");
					$nomor_do_accurate = ($row_nodo['no_do_syop'] ? $row_nodo['no_do_syop'] : $noms_do);

					$kode_customer_array[$kode_customer][$idplans]["nomor_do"] = $nomor_do_accurate;

					$sql1 = "update pro_pr_detail set " . $eks1 . " vol_potongan = '" . $volume . "', pr_top = '" . $top_plan . "', pr_kredit_limit = '" . $credit_limit . "', pr_vendor = '" . $dt1 . "', pr_harga_beli = '" . $dt2 . "', 
							pr_price_list = '" . $dt7 . "', pr_po = '" . $dt13 . "', pr_terminal = '" . $dt8 . "',  nomor_lo_pr = '" . $noms . "', " . $nomor_do . " nomor_po_supplier= '" . $nop . "', id_po_supplier = '" . $ips . "', id_po_receive = '" . $ipr . "'  where id_prd = '" . $idx . "'";
				}
				$con->setQuery($sql1);
				$oke  = $oke && !$con->hasError();

				$sql4 = "insert into new_pro_inventory_depot (id_datanya, id_jenis, id_produk, id_terminal, id_vendor, id_po_supplier, id_po_receive, tanggal_inven, out_inven_virtual, keterangan, created_time, created_ip, created_by, id_pr, id_prd) VALUES
				    ('generate DR', '6', '" . $idpro . "', '" . $dt8 . "', '" . $dt1 . "', '" . $ips . "', '" . $ipr . "', NOW(), '" . $volume . "', 'Out Stock Virtual', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "','" . $idr . "', '" . $idx . "')";
				$con->setQuery($sql4);
				$oke  = $oke && !$con->hasError();

				$kode_customer_array[$kode_customer][$idplans]["id_prd"] = $idx;

				// $sql4 = "insert into new_pro_inventory_depot (id_datanya, id_jenis, id_produk, id_terminal, id_vendor, id_po_supplier, id_po_receive, tanggal_inven, out_inven_virtual, keterangan, created_time, created_ip, created_by, id_pr, id_prd) VALUES
				//     ('generate DR', '6', '" . $idpro . "', '" . $dt8 . "', '" . $dt1 . "', '" . $ips . "', '" . $ipr . "', NOW(), '" . $volume . "', 'Out Stock Virtual', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "','" . $idr . "', '" . $idx . "')";
				// $con->setQuery($sql4);
				// $oke  = $oke && !$con->hasError();
			}

			//insert data split
			if (isset($_POST['newdp8'])) {
				foreach ($_POST['newdp8'] as $idx => $val) {
					$chk 			= htmlspecialchars($_POST['cek'][$idx], ENT_QUOTES);
					$pr_vendor 		= htmlspecialchars($_POST['newdv1'][$idx], ENT_QUOTES);
					$pr_harga_beli 	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['newdp2'][$idx]), ENT_QUOTES);
					$pr_terminal 	= htmlspecialchars($_POST['newdp8'][$idx], ENT_QUOTES);
					$newnop 		= htmlspecialchars($_POST['newnp1'][$idx], ENT_QUOTES);
					$newnp 			= htmlspecialchars($_POST['newnp1'][$idx], ENT_QUOTES);
					$newips 		= htmlspecialchars($_POST['newps1'][$idx], ENT_QUOTES);
					$newipr 		= htmlspecialchars($_POST['newpr1'][$idx], ENT_QUOTES);
					$volume_pr 		= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['newVolume'][$idx]), ENT_QUOTES);
					$idx_pr 		= htmlspecialchars($_POST['newIdx'][$idx], ENT_QUOTES);
					$volume 		= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['dp10'][$idx_pr]), ENT_QUOTES);
					$newidplans 	= htmlspecialchars($_POST['newid_plan'][$idx], ENT_QUOTES);
					$idpro 	    = htmlspecialchars($_POST['newid_pro'][$idx], ENT_QUOTES);

					$get_gudang = "select * FROM vw_terminal_inventory_receive WHERE nomor_po_supplier='" . $newnop . "'";
					$nama_gudang = $con->getRecord($get_gudang);

					//get gudang accurate
					$get_insial_cabang = "select * FROM pro_master_cabang WHERE id_master='" . $nama_gudang['id_cabang'] . "'";
					$row_inisial = $con->getRecord($get_insial_cabang);

					//get item by Detail PO Supplier
					$get_idpo = "select id_accurate FROM new_pro_inventory_vendor_po WHERE nomor_po='" . $newnop . "'";
					$id_po_acc = $con->getRecord($get_idpo);

					$query_po = http_build_query([
						'id' => $id_po_acc['id_accurate']
					]);

					$url_po = 'https://zeus.accurate.id/accurate/api/purchase-order/detail.do?' . $query_po;

					$result_po = curl_get($url_po);
					$detailItem_po = $result_po['d']['detailItem'];

					$sql3 = "
						insert into new_pro_inventory_potongan_stock(id_pr, id_prd, volume, pr_terminal, pr_harga_beli, nomor_po_supplier, id_po_supplier, id_po_receive)
						(select distinct id_pr, '" . $idx_pr . "', '" . $volume_pr . "','" . $pr_terminal . "',  '" . $pr_harga_beli . "', '" . $newnop . "', '" . $newips . "', '" . $newipr . "' from pro_pr_detail 
						where id_prd = '" . $idx_pr . "')
					";
					$con->setQuery($sql3);
					$oke  = $oke && !$con->hasError();

					$sql4 = "insert into new_pro_inventory_depot (id_datanya, id_jenis, id_produk, id_terminal, id_vendor, id_po_supplier, id_po_receive, tanggal_inven, out_inven_virtual, keterangan, created_time, created_ip, created_by, id_pr, id_prd) VALUES
			        ('generate DR', '6', '" . $idpro . "', '" . $pr_terminal . "', '" . $pr_vendor . "', '" . $newips  . "', '" . $newipr  . "', NOW(), '" . $volume_pr . "', 'Out Stock Virtual', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "', '" . $idr . "', '" . $idx_pr . "')";
					$con->setQuery($sql4);
					$oke  = $oke && !$con->hasError();


					$sql5 = "
						update pro_pr_detail set is_split = 1  
						where id_prd = '" . $idx_pr . "'
					";

					$con->setQuery($sql5);
					$oke  = $oke && !$con->hasError();
				}
			} else {
			}

			//$is_ceo = isset($_POST["is_ceo"]) ? htmlspecialchars($_POST["is_ceo"], ENT_QUOTES) : null;

			$ems1 = "select email_user from acl_user where id_role IN (9,10) and id_wilayah = '" . $idw . "' and is_active = 1";
			$sql2 = "
						update pro_pr set purchasing_summary = '" . $summary . "', purchasing_result = 1, purchasing_pic = '" . $pic . "', purchasing_tanggal = NOW(), 
						disposisi_pr = 6 , is_ceo = 0, coo_result = 1  
						where id_pr = '" . $idr . "'
					";

			$con->setQuery($sql2);
			$oke  = $oke && !$con->hasError();

			$sbjk = "Verifikasi DR [" . date('d/m/Y H:i:s') . "]";
			$pesn = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " melakukan verifikasi untuk DR";
		}
	}
} else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 7) {
	if (is_array_empty($_POST["cek"])) {
		$oke = false;
		$con->close();
		$flash->add("error", "Anda belum memilih data DR", BASE_REFERER);
	} else {
		$oke  = true;
		foreach ($_POST['cek'] as $idx => $val) {
			$chk = htmlspecialchars($_POST['cek'][$idx], ENT_QUOTES);
			$vol = htmlspecialchars($_POST['vol'][$idx], ENT_QUOTES);
			$ket = ($chk) ? htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST['ket'][$idx]), ENT_QUOTES) : 0;
			$tmp = ($ket) ? "volume = '" . $ket . "', " : "";

			$sql1 = "update pro_pr_detail set " . $tmp . " is_approved = '" . $chk . "' where id_prd = '" . $idx . "'";
			$con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();
		}
		$sql2 = "update pro_pr set sm_summary = '" . $summary . "', sm_result = 1, sm_pic = '" . $pic . "', sm_tanggal = NOW(), submit_bm = NOW(), disposisi_pr = 3 where id_pr = '" . $idr . "'";
		$con->setQuery($sql2);
		$oke  = $oke && !$con->hasError();

		$ems1 = "select email_user from acl_user where id_role = 5";
		$sbjk = "Persetujuan DR [" . date('d/m/Y H:i:s') . "]";
		$pesn = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " meminta persetujuan untuk DR";
	}
} else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 10) {
	$wilayah	= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
	$group		= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);
	$adaAr 		= false;
	$arSelf 	= array();
	$arOther 	= array();

	foreach ($_POST['dt1'] as $idx => $val) {
		$idc = htmlspecialchars($_POST['idc'][$idx], ENT_QUOTES);
		$idg = htmlspecialchars($_POST['idg'][$idx], ENT_QUOTES);
		$dt1 = htmlspecialchars($_POST['dt1'][$idx], ENT_QUOTES);
		$dt2 = htmlspecialchars($_POST['dt2'][$idx], ENT_QUOTES);
		$dt3 = htmlspecialchars(str_replace(array(",", "."), array("", ""), $_POST['dt3'][$idx]), ENT_QUOTES);
		$dt4 = htmlspecialchars(str_replace(array(",", "."), array("", ""), $_POST['dt4'][$idx]), ENT_QUOTES);
		$dt5 = htmlspecialchars(str_replace(array(",", "."), array("", ""), $_POST['dt5'][$idx]), ENT_QUOTES);
		$dt6 = htmlspecialchars(str_replace(array(",", "."), array("", ""), $_POST['dt6'][$idx]), ENT_QUOTES);
		$dt7 = htmlspecialchars($_POST['dt7'][$idx], ENT_QUOTES);
		$ext_id_role = htmlspecialchars($_POST['ext_id_role'][$idx], ENT_QUOTES);
		$ext_id_om 	 = htmlspecialchars($_POST['ext_id_om'][$idx], ENT_QUOTES);
		$no_do_acurate = htmlspecialchars($_POST['no_do_acurate'][$idx], ENT_QUOTES);

		// $sql1 = "update pro_customer set kode_pelanggan = '".$dt2."' where id_customer = '".$idc."'";
		// $con->setQuery($sql1);
		// $oke  = $oke && !$con->hasError();

		$sql2 = "
				update pro_pr_detail set 
					pr_top = '" . $dt1 . "', 
					pr_pelanggan = '" . $dt2 . "', 
					pr_ar_notyet = " . ($dt3 == '' ? 'NULL' : '"' . $dt3 . '"') . ", 
					pr_ar_satu = " . ($dt4 == '' ? 'NULL' : '"' . $dt4 . '"') . ", 
					pr_ar_dua = " . ($dt5 == '' ? 'NULL' : '"' . $dt5 . '"') . ", 
					pr_kredit_limit = '" . $dt6 . "', 
					pr_actual_top = '" . $dt7 . "',  
					no_do_acurate = '" . $no_do_acurate . "'
				
				where id_prd = '" . $idx . "'";
		$con->setQuery($sql2);
		$oke  = $oke && !$con->hasError();

		$total = $dt3 + $dt4 + $dt5;
		if ($dt4 || $dt5 || $total > $dt6) {
			$adaAr = true;
			if ($ext_id_role == 11)
				array_push($arSelf, $idx);
			else if ($ext_id_role == 17) {
				if (!array_key_exists($ext_id_om, $arOther)) $arOther[$ext_id_om] = array();
				if (!in_array($idx, $arOther[$ext_id_om])) array_push($arOther[$ext_id_om], $idx);
			}
		}
	}
	if ($adaAr) {
		if ($backlog == '1') {
			$ems1 = "select email_user from acl_user where id_role = 9 and id_wilayah = '" . $wilayah . "'";
			$sbjk = "Pengembalian DP [" . date('d/m/Y H:i:s') . "]";
			$pesn = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " mengembalikan DP Ke Logistik untuk DR";
			$cek1 = "
					select a.id_pr, a.id_plan, a.jumlah, b.jumlah as jumlah_approved
					from(select count(*) as jumlah, id_plan, id_pr from pro_pr_detail where 1=1 and id_pr = '" . $idr . "' group by id_pr, id_plan) a 
					left join(select count(*) as jumlah, id_plan, id_pr from pro_pr_detail where is_approved = 1 and id_pr = '" . $idr . "' group by id_pr, id_plan) b 
					on a.id_pr = b.id_pr and a.id_plan = b.id_plan";
			$res1 = $con->getResult($cek1);
			if (count($res1) > 0) {
				foreach ($res1 as $data1) {
					if (!$data1['jumlah_approved']) {
						$sql3 = "update pro_po_customer_plan set status_plan = 0  where id_plan = '" . $data1['id_plan'] . "'";
						$con->setQuery($sql3);
						$oke  = $oke && !$con->hasError();
					}
				}
			}


			$sql4 = "update pro_pr set disposisi_pr = 8  where id_pr = '" . $idr . "'";
			$con->setQuery($sql4);
			$oke  = $oke && !$con->hasError();
			$url  = BASE_URL_CLIENT . "/purchase-request.php";
		} else {
			$sql3 = "update pro_pr set ada_ar = 1, finance_result = 1, finance_pic = '" . $pic . "', finance_tanggal = NOW(),  jam_submit = NOW(), where id_pr = '" . $idr . "'";
			$con->setQuery($sql3);
			$oke  = $oke && !$con->hasError();
		}

		if (count($arSelf) > 0) {
			$sql4 = "insert into pro_pr_ar (id_wilayah, id_group, id_pr, tanggal_buat) values ('" . $wilayah . "', '" . $group . "', '" . $idr . "', NOW())";
			$res1 = $con->setQuery($sql4);
			$oke  = $oke && !$con->hasError();
			foreach ($arSelf as $idSelf) {
				$sql5 = "insert into pro_pr_ar_detail(id_par, id_prd) values ('" . $res1 . "', '" . $idSelf . "')";
				$con->setQuery($sql5);
				$oke  = $oke && !$con->hasError();
			}
		}

		if (count($arOther) > 0) {
			foreach ($arOther as $idka1 => $vaka1) {
				$sql5 = "insert into pro_pr_ar (id_wilayah, id_group, id_pr, is_ka, ka_om, tanggal_buat) values ('" . $wilayah . "', '" . $group . "', '" . $idr . "', 1, 
							 '" . $idka1 . "', NOW())";
				$res5 = $con->setQuery($sql5);
				$oke  = $oke && !$con->hasError();
				foreach ($vaka1 as $idka2) {
					$sql6 = "insert into pro_pr_ar_detail(id_par, id_prd) values ('" . $res5 . "', '" . $idka2 . "')";
					$con->setQuery($sql6);
					$oke  = $oke && !$con->hasError();
				}
			}
		}
	} else {
		if ($backlog == '1') {
			$ems1 = "select email_user from acl_user where id_role = 9 and id_wilayah = '" . $wilayah . "'";
			$sbjk = "Pengembalian DR [" . date('d/m/Y H:i:s') . "]";
			$pesn = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " mengembalikan DP Ke Logistik untuk DR";
			$cek1 = "
					select a.id_pr, a.id_plan, a.jumlah, b.jumlah as jumlah_approved
					from(select count(*) as jumlah, id_plan, id_pr from pro_pr_detail where 1=1 and id_pr = '" . $idr . "' group by id_pr, id_plan) a 
					left join(select count(*) as jumlah, id_plan, id_pr from pro_pr_detail where is_approved = 1 and id_pr = '" . $idr . "' group by id_pr, id_plan) b 
					on a.id_pr = b.id_pr and a.id_plan = b.id_plan";
			$res1 = $con->getResult($cek1);
			if (count($res1) > 0) {
				foreach ($res1 as $data1) {
					if (!$data1['jumlah_approved']) {
						$sql3 = "update pro_po_customer_plan set status_plan = 0  where id_plan = '" . $data1['id_plan'] . "'";
						$con->setQuery($sql3);
						$oke  = $oke && !$con->hasError();
					}
				}
			}


			$sql4 = "update pro_pr set disposisi_pr = 8 where id_pr = '" . $idr . "'";
			$con->setQuery($sql4);
			$oke  = $oke && !$con->hasError();
			$url  = BASE_URL_CLIENT . "/purchase-request.php";
		} else {
			$ems1 = "select email_user from acl_user where id_role = 7 and id_wilayah = '" . $wilayah . "'";
			$sbjk = "Persetujuan DR [" . date('d/m/Y H:i:s') . "]";
			$pesn = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " meminta persetujuan untuk DR";
			$sql3 = "update pro_pr set ada_ar = 0, finance_result = 1, finance_pic = '" . $pic . "', finance_tanggal = NOW(),  jam_submit = NOW(), revert_cfo = 0, revert_cfo_summary = '', revert_ceo = 0, revert_ceo_summary = '', disposisi_pr = 2 where id_pr = '" . $idr . "'";
			$con->setQuery($sql3);
			$oke  = $oke && !$con->hasError();
		}
	}
}

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

	if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 5) {
		if ($revisi_dr == 1) {
			$sqlacc = 'select id_do_accurate,id_plan from pro_pr_detail where id_pr = "' . $idr . '"';
			$id_accurate = $con->getResult($sqlacc);

			foreach ($id_accurate as $res) {
				if ($res['id_do_accurate'] != null) {
					// Data yang akan dikirim dalam format JSON
					$data = array(
						'id' => $res['id_do_accurate'],
					);

					$url_del = 'https://zeus.accurate.id/accurate/api/delivery-order/delete.do';
					$delete_accurate_do = curl_delete($url_del, json_encode($data));

					if ($delete_accurate_do['s'] == true) {
						$update02 = 'update pro_pr_detail set id_do_accurate = NULL where id_pr = "' . $idr . '"';
						$con->setQuery($update02);
						$oke  = $oke && !$con->hasError();

						$sqlid_acc = 'select id_accurate from pro_po_customer_plan where id_plan = "' . $res['id_plan'] . '"';
						$id_accurate_so = $con->getRecord($sqlid_acc);

						$data_so = array(
							'id' => $id_accurate_so['id_accurate'],
						);

						$url_del_so = 'https://zeus.accurate.id/accurate/api/sales-order/delete.do';
						$delete_accurate_so = curl_delete($url_del_so, json_encode($data_so));

						if ($delete_accurate_so['s'] == true) {
							$update02 = 'update pro_po_customer_plan set id_accurate = NULL where id_plan = "' . $res['id_plan'] . '"';
							$con->setQuery($update02);
							$oke  = $oke && !$con->hasError();
						} else {
							$con->rollBack();
							$con->clearError();
							$con->close();
							$flash->add("error", $delete_accurate_so['d'][0] . '- response dari accurate so', BASE_REFERER);
						}
					} else {
						$con->rollBack();
						$con->clearError();
						$con->close();
						$flash->add("error", $delete_accurate_do['d'][0] . '- response dari accurate do', BASE_REFERER);
					}
				}
			}


			// if ($delete_accurate['s'] == true) {
			// 	$con->commit();
			// 	$con->close();
			// 	header("location: " . $url);
			// 	exit();
			// } else {
			// 	$con->rollBack();
			// 	$con->clearError();
			// 	$con->close();
			// 	$flash->add("error", $delete_accurate['d'][0] . '- response dari accurate', BASE_REFERER);
			// }
		} else {
			$id_cabang = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

			$queryget_cabang = "SELECT * FROM pro_master_cabang WHERE id_master = '" . $id_cabang . "'";
			$rowget_cabang = $con->getRecord($queryget_cabang);

			$get_gudang = "select * FROM vw_terminal_inventory_receive WHERE nomor_po_supplier='" . $nop . "'";
			$nama_gudang = $con->getRecord($get_gudang);

			// //get gudang accurate
			// $get_insial_cabang = "select * FROM pro_master_cabang WHERE id_master='" . $nama_gudang['id_cabang'] . "'";
			// $row_inisial = $con->getRecord($get_insial_cabang);

			// //get item by Detail PO Supplier
			// $get_idpo = "select id_accurate FROM new_pro_inventory_vendor_po WHERE nomor_po='" . $nop . "'";
			// $id_po_acc = $con->getRecord($get_idpo);

			// $query_po = http_build_query([
			// 	'id' => $id_po_acc['id_accurate']
			// ]);

			// $url_po = 'https://zeus.accurate.id/accurate/api/purchase-order/detail.do?' . $query_po;

			// $result_po = curl_get($url_po);
			// $detailItem_po = $result_po['d']['detailItem'];

			// foreach ($detailItem_po as $items) {
			// 	if ($items['item']['itemType'] == 'INVENTORY') {
			// 		$item_po['detailItem'][] = [
			// 			'itemNo'       => $items['item']['no'],
			// 			'quantity'     => $dt10,
			// 			'unitPrice'    => $harga_dasar,
			// 			'warehouseName' => $row_inisial,
			// 		];
			// 	}
			// }


			foreach ($kode_customer_array as $i => $subData) {
				$detailItems = [];

				foreach ($subData as $subKey => $items) {

					$id_cabang = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

					$queryget_cabang = "SELECT * FROM pro_master_cabang WHERE id_master = '" . $id_cabang . "'";
					$rowget_cabang = $con->getRecord($queryget_cabang);

					$queryget_po = "SELECT a.no_so, a.tanggal_kirim, b.*, c.alamat_customer, c.postalcode_customer, d.nama_prov, e.nama_kab, a.id_lcr FROM pro_po_customer_plan a JOIN pro_po_customer b ON a.id_poc = b.id_poc JOIN pro_customer c ON b.id_customer = c.id_customer JOIN pro_master_provinsi d ON c.prov_customer = d.id_prov JOIN pro_master_kabupaten e ON c.kab_customer = e.id_kab WHERE a.id_plan = '" . $subKey . "'";
					$rowget_po = $con->getRecord($queryget_po);

					$queryget_lcr = "SELECT a.alamat_survey, b.nama_prov, c.nama_kab 
									FROM pro_customer_lcr a 
									JOIN pro_master_provinsi b ON a.prov_survey= b.id_prov 
									JOIN pro_master_kabupaten c ON a.kab_survey = c.id_kab
									WHERE a.id_lcr ='" . $rowget_po['id_lcr'] . "'";
					$rowget_lcr = $con->getRecord($queryget_lcr);

					$alamat_customer = $rowget_po['alamat_customer'] . " " . $rowget_po['nama_prov'] . " " . $rowget_po['nama_kab'] . " Kode Pos : " . $rowget_po['postalcode_customer'];
					$site_customer = $rowget_lcr['alamat_survey'] . " " . $rowget_lcr['nama_prov'] . " " . $rowget_lcr['nama_kab'];

					$url_so = 'https://zeus.accurate.id/accurate/api/sales-order/save.do';
					// Data yang akan dikirim dalam format JSON
					$data_so = array(
						"customerNo"        => $i,
						"number"           	=> $rowget_po['no_so'],
						"toAddress" 		=> $alamat_customer,
						"description" 		=> 'SO dari PO ' . $rowget_po['nomor_poc'],
						"poNumber" 			=> $rowget_po['nomor_poc'],
						"transDate" 		=> date("d/m/Y"),
						"shipDate" 			=> date("d/m/Y", strtotime($rowget_po['tanggal_kirim'])),
						"taxable" 			=> true,
						"branchName"  		=> $rowget_cabang['nama_cabang'] == 'Kantor Pusat' ? 'Head Office' : $rowget_cabang['nama_cabang'],
						"detailItem"       	=> $items['items']
					);
					// $data_so['detailItem'][]=$detailItems;


					$jsonData_so = json_encode($data_so);
					$result_so = curl_post($url_so, $jsonData_so);

					if ($result_so['s'] == true) {
						$cek = true;
						$id_accurate_so = $result_so['r']['id'];

						$sql_up = "update pro_po_customer_plan set id_accurate = '" . $id_accurate_so . "' WHERE id_plan = '" . $subKey . "'";
						$con->setQuery($sql_up);

						$sql_plan = "SELECT tanggal_loading FROM pro_po_customer_plan WHERE id_plan = '" . $subKey . "'";
						$row_plan = $con->getRecord($sql_plan);


						$cek = $cek && !$con->hasError();

						if ($cek) {

							// $all_idprd = implode(",", $idprd_all);
							$urlnya2 = 'https://zeus.accurate.id/accurate/api/delivery-order/save.do';
							// Data yang akan dikirim dalam format JSON
							$data2 = array(
								"customerNo"        => $i,
								"number"           	=> $items['nomor_do'],
								"description"       => $_POST["summary"],
								"poNumber" 			=> $rowget_po['nomor_poc'],
								"toAddress" 		=> $site_customer,
								"transDate" 		=> date("d/m/Y", strtotime($row_plan['tanggal_loading'])),
								"branchName"  		=> $rowget_cabang['nama_cabang'] == 'Kantor Pusat' ? 'Head Office' : $rowget_cabang['nama_cabang'],
								"detailItem"       	=> []
							);

							// Mengonversi data menjadi format JSON
							foreach ($items['items'] as $item2) {

								$dataItem = [
									'itemNo'       => $item2['itemNo'],
									'quantity'     => $item2['quantity'],
									'salesOrderNumber' => $rowget_po['no_so'],
									
								];

								// Jika ada 'warehouseName', tambahkan ke data
								if (isset($item2['warehouseName'])) {
									$dataItem['warehouseName'] = $item2['warehouseName'];
								}

								// Tambahkan item ke dalam detailItem
								$data2['detailItem'][] = $dataItem;
							}
							$jsonData2 = json_encode($data2);
							$result = curl_post($urlnya2, $jsonData2);

							if ($result['s'] == true) {
								$id_accurate = $result['r']['id'];

								$sql3 = 'update pro_pr_detail set id_do_accurate = "' . $id_accurate . '" where id_prd = "' . $items['id_prd'] . '"';
								$con->setQuery($sql3);
								$oke  = $oke && !$con->hasError();

								// $con->commit();
								// $con->close();
								// $flash->add("success", "SUKSES_MASUK", BASE_REFERER);
								// header("location: " . $url);
								// exit();
							} else {
								$con->rollBack();
								$con->clearError();
								$con->close();
								$flash->add("error", $result['d'][0] . " - Response dari Accurate DO", BASE_REFERER);
							}
						} else {
							$con->rollBack();
							$con->clearError();
							$con->close();
							$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
						}
					} else {
						$con->rollBack();
						$con->clearError();
						$con->close();
						$flash->add("error", $result_so['d'][0] . " - Response dari Accurate SO", BASE_REFERER);
					}
				}
			}
		}
	} else {
		$con->commit();
		$con->close();
		$flash->add("success", "SUKSES_MASUK", BASE_REFERER);
		header("location: " . $url);
		exit();
	}

	if ($oke) {
		$mantab  = true;
		if ($uploadnya) {
			$tmpPot = glob($pathfile . "/URG_" . $idr . "_*.{jpg,jpeg,gif,png,pdf}", GLOB_BRACE);

			if (count($tmpPot) > 0) {
				foreach ($tmpPot as $datj)
					if (file_exists($datj)) unlink($datj);
			}
			$tujuan  = $pathfile . "/" . $nqu;
			$mantab  = $mantab && move_uploaded_file($tempPhoto, $tujuan);
			if (file_exists($tempPhoto)) unlink($tempPhoto);
		}
		$con->commit();
		$con->close();
		if ($backlog) {
			$flash->add("success", "Data DP berhasil dikembalikan ke Logistik.");
			header("location: " . $url);
			exit();
		} else {
			$flash->add("success", "Data DR telah berhasil disimpan", $url);
			header("location: " . $url);
			exit();
		}
	} else {
		$con->rollBack();
		$con->clearError();
		$con->close();
		$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
	}
} else {
	$con->rollBack();
	$con->clearError();
	$con->close();
	$flash->add("error", "GAGAL_MASUK", BASE_REFERER);
}

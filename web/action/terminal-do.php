<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "pdfgen", "mailgen");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;
$enk  	= decode($_SERVER['REQUEST_URI']);
$act	= (!isset($enk['act']) or $enk['act'] == "") ? (isset($_POST["act"]) ? htmlspecialchars($_POST["act"], ENT_QUOTES) : null) : $enk['act'];
$pic 	= paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]);
$term 	= paramDecrypt($_SESSION["sinori" . SESSIONID]["terminal"]);
$id_wil = paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]);

$param 	= htmlspecialchars(paramDecrypt($_POST["param"]), ENT_QUOTES);
$sbmt1 	= isset($_POST["btnSbmt1"]) ? htmlspecialchars($_POST["btnSbmt1"], ENT_QUOTES) : null;
$sbmt2 	= isset($_POST["btnSbmt2"]) ? htmlspecialchars($_POST["btnSbmt2"], ENT_QUOTES) : null;
$dt1 	= isset($_POST["dt1"]) ? htmlspecialchars($_POST["dt1"], ENT_QUOTES) : null;
$dt2 	= isset($_POST["dt2"]) ? htmlspecialchars($_POST["dt2"], ENT_QUOTES) : null;
$dt3 	= isset($_POST["dt3"]) ? htmlspecialchars($_POST["dt3"], ENT_QUOTES) : null;
$dt4 	= isset($_POST["dt4"]) ? htmlspecialchars($_POST["dt4"], ENT_QUOTES) : null;
$dt5 	= isset($_POST["dt5"]) ? htmlspecialchars($_POST["dt5"], ENT_QUOTES) : null;
$dt6 	= isset($_POST["dt6"]) ? htmlspecialchars($_POST["dt6"], ENT_QUOTES) : null;
$pic_mkt 	= isset($_POST["pic_mkt"]) ? htmlspecialchars($_POST["pic_mkt"], ENT_QUOTES) : null;
$terminal 	= isset($_POST["terminal"]) ? htmlspecialchars($_POST["terminal"], ENT_QUOTES) : null;
$customer 	= isset($_POST["customer"]) ? htmlspecialchars($_POST["customer"], ENT_QUOTES) : null;
$tgl_loading 	= isset($_POST["tgl_loading"]) ? htmlspecialchars($_POST["tgl_loading"], ENT_QUOTES) : null;
$temp 	= explode("#|#", $param);
$file	= $temp[0];
$tipe	= $temp[1];
$idnya	= $temp[2];
$volume	= $temp[3];
$produk = $temp[4];
$loadTg = $temp[5];
$loadJm = $temp[6];
$vendor = $temp[7];
$area 	= $temp[8];

$id_po_supplier = $temp[18] != "" ? $temp[18] : 0;
$id_po_receive = $temp[19] != "" ? $temp[19] : 0;


$id_cust 	= $temp[9];
$pic_log 	= $temp[10];
$pic_cs 	= $temp[11];

$pic_marketing 	= $temp[12];
$nama_cust 		= $temp[13];
$nama_terminal 	= $temp[14];
$no_plat 		= $temp[15];
$nama_sopir 	= $temp[16];

$term = ($term) ? $term : $temp[17];


$dt5_text = '';
if ($dt5 == 1) {
	$dt5_text = 'Request: Change Depot';
} else if ($dt5 == 2) {
	$dt5_text = 'Request: Reschedule';
} else if ($dt5 == 3) {
	$dt5_text = 'Request: Cancel';
}

if ($file == "do_truck") {
	if ($tipe == "cetak") {
		if (is_array_empty($_POST["cek"])) {
			$con->close();
			$flash->add("error", "Data belum dipilih....", BASE_REFERER);
		} else {
			$idr = implode(",", array_keys($_POST["cek"]));
			if ($sbmt1) {
				$sql = "select a.*, i.nama_customer, e.alamat_survey, f.nama_prov, g.nama_kab, o.nama_terminal, o.tanki_terminal, o.lokasi_terminal, b.no_spj, k.nomor_plat, 
							l.nama_sopir, b.volume_po, j.jenis_produk, j.merk_dagang, n.nama_transportir, n.nama_suplier, p.created_by, q.kode_barcode, p.is_loco,o.initial 
							from pro_po_ds_detail a join pro_po_detail b on a.id_pod = b.id_pod 
							join pro_pr_detail c on a.id_prd = c.id_prd 
							join pro_po_customer_plan d on a.id_plan = d.id_plan 
							join pro_customer_lcr e on d.id_lcr = e.id_lcr
							join pro_master_provinsi f on e.prov_survey = f.id_prov 
							join pro_master_kabupaten g on e.kab_survey = g.id_kab
							join pro_po_customer h on d.id_poc = h.id_poc 
							join pro_customer i on h.id_customer = i.id_customer 
							join pro_master_produk j on h.produk_poc = j.id_master 
							join pro_master_transportir_mobil k on b.mobil_po = k.id_master 
							join pro_master_transportir_sopir l on b.sopir_po = l.id_master
							join pro_po m on a.id_po = m.id_po 
							join pro_master_transportir n on m.id_transportir = n.id_master 
							join pro_master_terminal o on b.terminal_po = o.id_master 
							join pro_po_ds p on a.id_ds = p.id_ds 
							join pro_master_cabang q on p.id_wilayah = q.id_master 
							where a.id_dsd in (" . $idr . ") order by field(a.id_dsd, " . $idr . ")";
				$res = $con->getResult($sql);
				$printe = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]) . " " . date("d/m/Y H:i:s") . " WIB";


				ob_start();
				require_once(realpath("./template/delivery-order-truck.php"));
				$content = ob_get_clean();
				ob_end_flush();
				$con->close();

				$mpdf = null;
				if (PHP_VERSION >= 5.6) {
					$mpdf = new \Mpdf\Mpdf(['format' => 'Letter']);
				} else
					$mpdf = new mPDF('c', 'Letter', 10, 'arial', 10, 10, 20, 10, 0, 5);
				$mpdf->SetDisplayMode('fullpage');
				$mpdf->WriteHTML($content);
				$filename = "DO_TRUCK_";
				$mpdf->Output($filename . '_' . date('dmyHis') . '.pdf', 'I');
				exit;
			} else if ($sbmt2) {
				$sql = "select a.*, b.nomor_po, b.tanggal_po, k.nama_suplier, k.att_suplier, k.fax_suplier, k.telp_suplier, l.nomor_plat, m.nama_sopir, n.nama_terminal, 
							c.is_approved, h.nomor_poc, i.nama_customer, j.fullname, e.alamat_survey, e.picustomer, f.nama_prov, g.nama_kab, o.nama_cabang, c.produk, 
							b.created_by, b.tgl_approved, o.kode_barcode,  d.tanggal_kirim
							from pro_po_detail a join pro_po b on a.id_po = b.id_po 
							join pro_pr_detail c on a.id_prd = c.id_prd
							join pro_po_customer_plan d on a.id_plan = d.id_plan 
							join pro_customer_lcr e on d.id_lcr = e.id_lcr
							join pro_master_provinsi f on e.prov_survey = f.id_prov 
							join pro_master_kabupaten g on e.kab_survey = g.id_kab
							join pro_po_customer h on d.id_poc = h.id_poc 
							join pro_customer i on h.id_customer = i.id_customer 
							join acl_user j on i.id_marketing = j.id_user 
							join pro_master_transportir k on b.id_transportir = k.id_master 
							join pro_master_transportir_mobil l on a.mobil_po = l.id_master 
							join pro_master_transportir_sopir m on a.sopir_po = m.id_master 
							join pro_master_terminal n on a.terminal_po = n.id_master 
							join pro_master_cabang o on b.id_wilayah = o.id_master
							join pro_po_ds_detail p on a.id_pod = p.id_pod 
							where p.id_dsd in (" . $idr . ") order by field(p.id_dsd, " . $idr . ")";
				$res = $con->getResult($sql);
				$printe = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]) . " " . date("d/m/Y H:i:s") . " WIB";

				ob_start();
				require_once(realpath("./template/surat-jalan.php"));
				$content = ob_get_clean();
				ob_end_flush();
				$con->close();

				$mpdfOpt1 	= array(
					'mode' => 'c',
					'format' => 'A4',
					'default_font_size' => '10',
					'default_font' => 'arial',
					'margin_left' => '15',
					'margin_right' => '15',
					'margin_top' => '15',
					'margin_bottom' => '15',
					'margin_header' => '5',
					'margin_footer' => '5'
				);

				// $mpdf = null;
				// if (PHP_VERSION >= 5.6) {
				// 	$mpdf = new \Mpdf\Mpdf(['format' => [216, 139]]);
				// } else
				// 	$mpdf = new mPDF('c', array(210, 148), 10, 'arial', 10, 10, 10, 10, 0, 5);

				$mpdf = null;
				if (PHP_VERSION >= 5.6) {
					$mpdf = new \Mpdf\Mpdf($mpdfOpt1);
				} else {
					$mpdf = new mPDF('c', 'A4', 10, 'arial', 15, 15, 15, 15, 5, 5);
				}
				$mpdf->SetDisplayMode('fullpage');
				$mpdf->WriteHTML($content);
				$filename = "SPJ_";
				$mpdf->Output($filename . '_' . date('dmyHis') . '.pdf', 'I');
				exit;
			}
		}
	} else {
		$oke = true;
		$con->beginTransaction();
		$con->clearError();
		$answer	= array();

		if ($tipe == "loading") {
			if ($dt1 && $dt2 && $dt3) {
				$tTgl = explode("/", $dt1);
				$tJam = array($dt2, $dt3);
				if (intval($tJam[0]) < 7) {
					$malam 	= true;
					$daten 	= date("Y/m/d", mktime(0, 0, 0, $tTgl[1], $tTgl[0] - 1, $tTgl[2]));
				} else {
					$malam 	= false;
					$daten 	= tgl_db($dt1);
				}
				$cek1 = "select id_master from pro_master_inventory_out where tanggal_inv = '" . $daten . "' and id_terminal = '" . $term . "' and id_produk = '" . $produk . "'";
				$ada1 = $con->getOne($cek1);
				if ($ada1) {
					$cols = ($malam) ? "out_malam = out_malam + " . $volume : "out_pagi = out_pagi + " . $volume;
					$sql1 = "update pro_master_inventory_out set " . $cols . ", lastupdate_time = NOW(), lastupdate_ip = '" . $_SERVER['REMOTE_ADDR'] . "', 
								 lastupdate_by = '" . $pic . "' where id_master = '" . $ada1 . "'";
					$con->setQuery($sql1);
					$oke  = $oke && !$con->hasError();
				} else {
					$cols = ($malam) ? "out_malam" : "out_pagi";
					$sql1 = "insert into pro_master_inventory_out(id_terminal, tanggal_inv, id_produk, " . $cols . ", created_time, created_ip, created_by) values 
								('" . $term . "', '" . $daten . "', '" . $produk . "', '" . $volume . "', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . $pic . "')";
					$con->setQuery($sql1);
					$oke  = $oke && !$con->hasError();
				}
				$cek2 = "select id_master from pro_inventory_vendor where tanggal_inven = '" . tgl_db($dt1) . "' and id_terminal = '" . $term . "' and id_produk = '" . $produk . "' 
							 and id_vendor = '" . $vendor . "' and id_area = '" . $area . "'";
				$ada2 = $con->getOne($cek2);
				if ($ada2) {
					$sql3 = "update pro_inventory_vendor set out_inven = out_inven + " . $volume . " where id_master = '" . $ada2 . "'";
					$con->setQuery($sql3);
					$oke  = $oke && !$con->hasError();
				} else {
					$sql3 = "insert into pro_inventory_vendor(id_vendor, id_produk, id_area, id_terminal, tanggal_inven, out_inven, nomor_po, created_time, created_ip, created_by) 
								 values ('" . $vendor . "', '" . $produk . "', '" . $area . "', '" . $term . "', '" . tgl_db($dt1) . "', '" . $volume . "', '', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', 
								 '" . $pic . "')";
					$con->setQuery($sql3);
					$oke  = $oke && !$con->hasError();
				}

				$cek4 = "select nama_terminal, tanki_terminal, lokasi_terminal from pro_master_terminal where id_master = '" . $term . "'";
				$row4 = $con->getRecord($cek4);
				$dept = ($row4['tanki_terminal'] ? ' ' . $row4['tanki_terminal'] : '') . ($row4['lokasi_terminal'] ? ', ' . $row4['lokasi_terminal'] : '');

				$cek3 = "select status_pengiriman from pro_po_ds_detail where id_dsd = '" . $idnya . "'";
				$row3 = $con->getOne($cek3);
				$temp = json_decode($row3, true);
				$arrS = ($temp == NULL) ? array() : $temp;
				array_push($arrS, array("status" => "Loading di depot " . $row4['nama_terminal'] . $dept, "tanggal" => $dt1 . " " . $dt2 . ":" . $dt3));

				$sql2 = "update pro_po_ds_detail set status_pengiriman = '" . json_encode($arrS) . "', is_loaded = 1, tanggal_loaded = '" . tgl_db($dt1) . "', jam_loaded = '" . implode(":", $tJam) . "', catatan = '" . $dt4 . "' where id_dsd = '" . $idnya . "'";
				$con->setQuery($sql2);
				$oke  = $oke && !$con->hasError();

				if ($oke) {

					$cekid = "select id_pr, id_prd from pro_po_ds_detail where id_dsd = '" . $idnya . "'";
					$rowid = $con->getRecord($cekid);

					$id_pr_value = isset($rowid['id_pr']) ? $rowid['id_pr'] : '';
					$id_prd_value = isset($rowid['id_prd']) ? $rowid['id_prd'] : '';

					// $sql4 = "insert into new_pro_inventory_depot (id_datanya, id_jenis, id_produk, id_terminal, id_vendor, id_po_supplier, id_po_receive, tanggal_inven, out_inven, out_inven_virtual, keterangan, created_time, created_ip, created_by, id_dsd, id_pr, id_prd)
					// 		 VALUES
					// 		('generate Loading', '7', '" . $produk . "', '" . $term . "', '" . $vendor . "', '" . $id_po_supplier . "' , '" . $id_po_receive . "', '" . tgl_db($dt1) . "', '" . $volume . "', '-" . $volume . "', 'Loaded', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "', '" . $idnya . "', '" . $id_pr_value . "',  '" . $id_prd_value . "')";
					// $con->setQuery($sql4);
					// $oke  = $oke && !$con->hasError();

					$cekidprd = "select * from new_pro_inventory_depot where id_jenis= '7' and id_prd = '" . $id_prd_value . "'";
					$rowidprd = $con->getRecord($cekidprd);

					if (!$rowidprd) {
						$sql4 = "insert into new_pro_inventory_depot (id_datanya, id_jenis, id_produk, id_terminal, id_vendor, id_po_supplier, id_po_receive, tanggal_inven, out_inven, out_inven_virtual, keterangan, created_time, created_ip, created_by, id_dsd, id_pr, id_prd)
								VALUES
								('generate Loading', '7', '" . $produk . "', '" . $term . "', '" . $vendor . "', '" . $id_po_supplier . "' , '" . $id_po_receive . "', '" . tgl_db($dt1) . "', '" . $volume . "', '-" . $volume . "', 'Loaded', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "', '" . $idnya . "', '" . $id_pr_value . "',  '" . $id_prd_value . "')";
						$con->setQuery($sql4);
						$oke  = $oke && !$con->hasError();
					}
				}
			}
		} else if ($tipe == "revert") {
			$tTgl = explode("-", $loadTg);
			$tJam = explode(":", $loadJm);
			if (intval($tJam[0]) < 7) {
				$malam 	= true;
				$oTgl 	= date("Y/m/d", mktime(0, 0, 0, $tTgl[1], $tTgl[2] - 1, $tTgl[0]));
			} else {
				$malam 	= false;
				$oTgl 	= $loadTg;
			}
			$cols = ($malam) ? "out_malam = out_malam - " . $volume : "out_pagi = out_pagi - " . $volume;
			$sql1 = "update pro_master_inventory_out set lastupdate_time = NOW(), lastupdate_ip = '" . $_SERVER['REMOTE_ADDR'] . "', lastupdate_by = '" . $pic . "', 
						 " . $cols . " where tanggal_inv = '" . $oTgl . "' and id_terminal = '" . $term . "' and id_produk = '" . $produk . "'";
			$con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();

			$cek1 = "select id_master from pro_inventory_vendor where tanggal_inven = '" . $loadTg . "' and id_terminal = '" . $term . "' and id_produk = '" . $produk . "' 
						 and id_vendor = '" . $vendor . "' and id_area = '" . $area . "'";
			$idpv = $con->getOne($cek1);
			$sql2 = "update pro_inventory_vendor set out_inven = out_inven - " . $volume . " where id_master = '" . $idpv . "'";
			$con->setQuery($sql2);
			$oke  = $oke && !$con->hasError();

			$sql3 = "update pro_po_ds_detail set is_loaded = 0, is_cancel = 0, tanggal_loaded = NULL, jam_loaded = '', status_pengiriman = '' where id_dsd = '" . $idnya . "'";
			$con->setQuery($sql3);
			$oke  = $oke && !$con->hasError();

			if ($oke) {
				$sql4 = "Delete From new_pro_inventory_depot where id_dsd = '" . $idnya . "'";
				$con->setQuery($sql4);
				$oke  = $oke && !$con->hasError();
			}
		} else if ($tipe == "cancel") {
			if ($dt1 && $dt2 && $dt3) {
				$bemp = tgl_db($dt1) . " " . $dt2 . ":" . $dt3 . ":00";
				$sql1 = "update pro_po_ds_detail set is_cancel = 1, tanggal_cancel = '" . $bemp . "' where id_dsd = '" . $idnya . "'";
				$con->setQuery($sql1);
				$oke  = $oke && !$con->hasError();

				$tTgl = explode("-", $loadTg);
				$tJam = explode(":", $loadJm);
				if (intval($tJam[0]) < 7) {
					$malam 	= true;
					$oTgl 	= date("Y/m/d", mktime(0, 0, 0, $tTgl[1], $tTgl[2] - 1, $tTgl[0]));
				} else {
					$malam 	= false;
					$oTgl 	= $loadTg;
				}

				$cols = ($malam) ? "out_malam = out_malam - " . $volume : "out_pagi = out_pagi - " . $volume;
				$sql2 = "update pro_master_inventory_out set lastupdate_time = NOW(), lastupdate_ip = '" . $_SERVER['REMOTE_ADDR'] . "', lastupdate_by = '" . $pic . "', 
							 out_cancel = out_cancel + " . $volume . ", " . $cols . " where tanggal_inv = '" . $oTgl . "' and id_terminal = '" . $term . "' and id_produk = '" . $produk . "'";
				$con->setQuery($sql2);
				$oke  = $oke && !$con->hasError();

				$cek1 = "select id_master from pro_inventory_vendor where tanggal_inven = '" . $loadTg . "' and id_terminal = '" . $term . "' and id_produk = '" . $produk . "' 
							 and id_vendor = '" . $vendor . "' and id_area = '" . $area . "'";
				$idpv = $con->getOne($cek1);

				$sql3 = "update pro_inventory_vendor set out_inven = out_inven - " . $volume . " where id_master = '" . $idpv . "'";
				$con->setQuery($sql3);
				$oke  = $oke && !$con->hasError();
			}
		} else if ($tipe == "request") {
			//get data request 
			$cek = "select id_plan, is_request FROM pro_po_ds_detail WHERE id_dsd = '" . $idnya . "'";
			$result = $con->getRecord($cek);
			// cek kondisi apakah sudah melakukan request sebelumnya 

			$sql1 = "update pro_po_ds_detail set is_request = '" . $dt5 . "', request = '" . $dt6 . "',  tanggal_request = NOW(), disposisi_request = 1 where id_dsd = '" . $idnya . "'";
			$con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();

			if($dt5==2){
				$sql1 = "update pro_po_customer_plan set tanggal_loading = '" . tgl_db($tgl_loading) . "' where id_plan = '" . $result['id_plan'] . "'";
				$con->setQuery($sql1);
				$oke  = $oke && !$con->hasError();
			}
		}

		if ($oke) {

			$con->commit();
			$sqlSide = "
					select count(*) from(
						select a.id_dsd 
						from pro_po_ds_detail a 
						join pro_po_ds b on a.id_ds = b.id_ds
						where b.id_terminal = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['terminal']) . "' and b.is_submitted = 1 and a.is_loaded = 0
						union all
						select a.id_dsk 
						from pro_po_ds_kapal a 
						where a.terminal = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['terminal']) . "' and a.is_loaded = 0
					) a";
			$answer["badge"] = $con->getOne($sqlSide);

			/*notif email yg punya id_role in (9,11,13,18) yg di wilayah nya*/
			if ($tipe == 'loading') {
				// $ems1 = "select distinct email_user from acl_user where id_wilayah = '".$id_wil."' and (id_user='".$pic_marketing."'  OR id_user='".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."' OR fullname in('".$pic_log."','".$pic_cs."'))";
				// $cek = "select distinct email_user from acl_user where id_wilayah='" . $id_wil . "' and id_user in('" . $pic_marketing . "') and is_active=1";
				// $result_cek = $con->getResult($cek);
				// if (count($result_cek) == 1) {
				// 	$ems1 = "select distinct *from(
				// 				select distinct email_user from acl_user where id_wilayah='" . $id_wil . "' and id_user in('" . $pic_marketing . "','" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_user"]) . "') and is_active=1
				// 				union all
				// 			  	select distinct email_user from acl_user where id_wilayah='" . $id_wil . "' and id_role in(9,18) and is_active=1
				// 		  )a";
				// } else {
				// 	$ems1 = "select distinct *from(
				// 					select distinct email_user from acl_user where id_wilayah='" . $id_wil . "' and id_user in('" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_user"]) . "') and is_active=1
				// 					union all
				// 				  	select distinct email_user from acl_user where id_wilayah='" . $id_wil . "' and id_role in(9,11,18) and is_active=1
				// 			  )a";
				// }

				$ems1 = "select distinct email_user FROM acl_user WHERE id_wilayah ='" . $id_wil . "' AND (id_user='" . $pic_marketing . "' OR id_role='18' OR id_role='10') AND is_active = 1";

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
					$mail->Subject = "Loaded  " . $nama_cust . ', ' . date('d/m/Y H:i:s') . "";
					$mail->msgHTML(paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " telah menyelesaikan loading di " . $nama_terminal . " " . $no_plat . " " . $nama_sopir . "");
					$mail->send();
				}
			} else if ($tipe == 'request') {

				if ($dt5 == 2) {
					$ems1 = " select DISTINCT email_user 
								FROM acl_user 
								WHERE
							 	(id_wilayah = '" . $id_wil . "'  AND id_role = '18')
								OR 
								(id_user = '" . $pic_mkt  . "' AND is_active = 1)";
				} else if ($dt5 == 3) {
					$ems1 = "select DISTINCT email_user
								FROM acl_user
								WHERE 
								(id_wilayah = '" . $id_wil . "'  AND id_role = '18')
								OR 
								(id_role = 5 OR id_user = '" . $pic_mkt  . "' AND is_active = 1 )";
				}


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
					$mail->Subject = $dt5_text . '. ' . date('d/m/Y H:i:s');
					$mail->msgHTML(paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " " . $dt6 .  "");
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
	}
} else if ($file == "do_kapal") {
	$term = ($term) ? $term : $temp[9];

	if ($tipe == "cetak") {
		if (is_array_empty($_POST["chk"])) {
			$con->close();
			$flash->add("error", "Data belum dipilih....", BASE_REFERER);
		} else {
			$idr = implode(",", array_keys($_POST["chk"]));
			$sql = "select a.*, b.inisial_segel, c.nama_terminal, d.nama_suplier, e.volume, b.kode_barcode 
						from pro_po_ds_kapal a join pro_master_cabang b on a.id_wilayah = b.id_master 
						join pro_master_terminal c on a.terminal = c.id_master join pro_master_transportir d on a.transportir = d.id_master 
						join pro_pr_detail e on a.id_prd = e.id_prd where a.id_dsk in (" . $idr . ") order by field(a.id_dsk, " . $idr . ")";
			$res = $con->getResult($sql);
			$printe = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]) . " " . date("d/m/Y H:i:s") . " WIB";

			ob_start();
			require_once(realpath("./template/delivery-order-kapal.php"));
			$content = ob_get_clean();
			ob_end_flush();
			$con->close();

			$mpdf = null;
			if (PHP_VERSION >= 5.6) {
				$mpdf = new \Mpdf\Mpdf(['format' => 'Letter']);
			} else
				$mpdf = new mPDF('c', 'Letter', 9, 'arial', 10, 10, 10, 10, 0, 5);
			$mpdf->SetDisplayMode('fullpage');
			$mpdf->WriteHTML($content);
			$filename = "DN_KAPAL_";
			$mpdf->Output($filename . '_' . date('dmyHis') . '.pdf', 'I');
			exit;
		}
	} else {
		$oke = true;
		$con->beginTransaction();
		$con->clearError();
		$answer	= array();

		if ($tipe == "loading") {
			if ($dt1 && $dt2 && $dt3) {
				$tTgl = explode("/", $dt1);
				$tJam = array($dt2, $dt3);
				if (intval($tJam[0]) < 7) {
					$malam 	= true;
					$daten 	= date("Y/m/d", mktime(0, 0, 0, $tTgl[1], $tTgl[0] - 1, $tTgl[2]));
				} else {
					$malam 	= false;
					$daten 	= tgl_db($dt1);
				}
				$cek1 = "select id_master from pro_master_inventory_out where tanggal_inv = '" . $daten . "' and id_terminal = '" . $term . "' and id_produk = '" . $produk . "'";
				$ada1 = $con->getOne($cek1);
				if ($ada1) {
					$cols = ($malam) ? "out_malam = out_malam + " . $volume : "out_pagi = out_pagi + " . $volume;
					$sql1 = "update pro_master_inventory_out set " . $cols . ", lastupdate_time = NOW(), lastupdate_ip = '" . $_SERVER['REMOTE_ADDR'] . "', 
								 lastupdate_by = '" . $pic . "' where id_master = '" . $ada1 . "'";
					$con->setQuery($sql1);
					$oke  = $oke && !$con->hasError();
				} else {
					$cols = ($malam) ? "out_malam" : "out_pagi";
					$sql1 = "insert into pro_master_inventory_out(id_terminal, tanggal_inv, id_produk, " . $cols . ", created_time, created_ip, created_by) values 
								('" . $term . "', '" . $daten . "', '" . $produk . "', '" . $volume . "', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . $pic . "')";
					$con->setQuery($sql1);
					$oke  = $oke && !$con->hasError();
				}

				$cek2 = "select id_master from pro_inventory_vendor where tanggal_inven = '" . tgl_db($dt1) . "' and id_terminal = '" . $term . "' and id_produk = '" . $produk . "' 
							 and id_vendor = '" . $vendor . "' and id_area = '" . $area . "'";
				$ada2 = $con->getOne($cek2);
				if ($ada2) {
					$sql3 = "update pro_inventory_vendor set out_inven = out_inven + " . $volume . " where id_master = '" . $ada2 . "'";
					$con->setQuery($sql3);
					$oke  = $oke && !$con->hasError();
				} else {
					$sql3 = "insert into pro_inventory_vendor(id_vendor, id_produk, id_area, id_terminal, tanggal_inven, out_inven, nomor_po, created_time, created_ip, created_by) 
								 values ('" . $vendor . "', '" . $produk . "', '" . $area . "', '" . $term . "', '" . tgl_db($dt1) . "', '" . $volume . "', '', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', 
								 '" . $pic . "')";
					$con->setQuery($sql3);
					$oke  = $oke && !$con->hasError();
				}

				$cek4 = "select nama_terminal, tanki_terminal, lokasi_terminal from pro_master_terminal where id_master = '" . $term . "'";
				$row4 = $con->getRecord($cek4);
				$dept = ($row4['tanki_terminal'] ? ' ' . $row4['tanki_terminal'] : '') . ($row4['lokasi_terminal'] ? ', ' . $row4['lokasi_terminal'] : '');

				$cek3 = "select status_pengiriman from pro_po_ds_kapal where id_dsk = '" . $idnya . "'";
				$row3 = $con->getOne($cek3);
				$temp = json_decode($row3, true);
				$arrS = ($temp == NULL) ? array() : $temp;
				array_push($arrS, array("status" => "Loading di depot " . $row4['nama_terminal'] . $dept, "tanggal" => $dt1 . " " . $dt2 . ":" . $dt3));

				$sql2 = "update pro_po_ds_kapal set status_pengiriman = '" . json_encode($arrS) . "', is_loaded = 1, tanggal_loaded = '" . tgl_db($dt1) . "', 
							 jam_loaded = '" . implode(":", $tJam) . "', catatan = '" . $dt4 . "' where id_dsk = '" . $idnya . "'";
				$con->setQuery($sql2);
				$oke  = $oke && !$con->hasError();


				if ($oke) {

					$cekid = "select id_pr, id_prd from pro_po_ds_kapal where id_dsk = '" . $idnya . "'";
					$rowid = $con->getRecord($cekid);

					$id_pr_value = isset($rowid['id_pr']) ? $rowid['id_pr'] : '';
					$id_prd_value = isset($rowid['id_prd']) ? $rowid['id_prd'] : '';

					$cekiddsd = "select * from new_pro_inventory_depot where id_dsd = '" . $idnya . "'";
					$rowiddsd = $con->getRecord($cekiddsd);

					//develop garry salsabila* 16-01-2025
					if (!$rowiddsd) {
						$sql4 = "insert into new_pro_inventory_depot (id_datanya, id_jenis, id_produk, id_terminal, id_vendor, id_po_supplier, id_po_receive, tanggal_inven, out_inven, out_inven_virtual, keterangan, created_time, created_ip, created_by, id_dsk, id_pr, id_prd)
							 VALUES
							('generate Loading', '7', '" . $produk . "', '" . $term . "', '" . $vendor . "', '" . $id_po_supplier . "' , '" . $id_po_receive . "', '" . tgl_db($dt1) . "', '" . $volume . "', '-" . $volume . "', 'Loaded', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "', '" . $idnya . "', '" . $id_pr_value . "',  '" . $id_prd_value . "')";
						$con->setQuery($sql4);
						$oke  = $oke && !$con->hasError();
					}

					$ems1 = "select distinct email_user FROM acl_user WHERE id_wilayah ='" . $id_wil . "' AND (id_user='" . $pic_marketing . "' OR id_role='18' OR id_role='10') AND is_active = 1";

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
						$mail->Subject = "Loaded  " . $customer . ', ' . date('d/m/Y H:i:s') . "";
						$mail->msgHTML(paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " telah menyelesaikan loading di " . $terminal . "");
						$mail->send();
					}
				}
			}
		} else if ($tipe == "revert") {
			$tTgl = explode("-", $loadTg);
			$tJam = explode(":", $loadJm);
			if (intval($tJam[0]) < 7) {
				$malam 	= true;
				$oTgl 	= date("Y/m/d", mktime(0, 0, 0, $tTgl[1], $tTgl[2] - 1, $tTgl[0]));
			} else {
				$malam 	= false;
				$oTgl 	= $loadTg;
			}
			$cols = ($malam) ? "out_malam = out_malam - " . $volume : "out_pagi = out_pagi - " . $volume;
			$sql1 = "update pro_master_inventory_out set lastupdate_time = NOW(), lastupdate_ip = '" . $_SERVER['REMOTE_ADDR'] . "', lastupdate_by = '" . $pic . "', 
						 " . $cols . " where tanggal_inv = '" . $oTgl . "' and id_terminal = '" . $term . "' and id_produk = '" . $produk . "'";
			$con->setQuery($sql1);
			$oke  = $oke && !$con->hasError();

			$cek1 = "select id_master from pro_inventory_vendor where tanggal_inven = '" . $loadTg . "' and id_terminal = '" . $term . "' and id_produk = '" . $produk . "' 
						 and id_vendor = '" . $vendor . "' and id_area = '" . $area . "'";
			$idpv = $con->getOne($cek1);
			$sql2 = "update pro_inventory_vendor set out_inven = out_inven - " . $volume . " where id_master = '" . $idpv . "'";
			$con->setQuery($sql2);
			$oke  = $oke && !$con->hasError();

			$sql3 = "update pro_po_ds_kapal set is_loaded = 0, is_cancel = 0, tanggal_loaded = NULL, jam_loaded = '', status_pengiriman = '' where id_dsk = '" . $idnya . "'";
			$con->setQuery($sql3);
			$oke  = $oke && !$con->hasError();


			if ($oke) {
				$sql4 = "Delete From new_pro_inventory_depot where id_dsk = '" . $idnya . "'";
				$con->setQuery($sql4);
				$oke  = $oke && !$con->hasError();
			}
		} else if ($tipe == "cancel") {
			if ($dt1 && $dt2 && $dt3) {
				$bemp = tgl_db($dt1) . " " . $dt2 . ":" . $dt3 . ":00";
				$sql1 = "update pro_po_ds_kapal set is_cancel = 1, tanggal_cancel = '" . $bemp . "' where id_dsk = '" . $idnya . "'";
				$con->setQuery($sql1);
				$oke  = $oke && !$con->hasError();

				$tTgl = explode("-", $loadTg);
				$tJam = explode(":", $loadJm);
				if (intval($tJam[0]) < 7) {
					$malam 	= true;
					$oTgl 	= date("Y/m/d", mktime(0, 0, 0, $tTgl[1], $tTgl[2] - 1, $tTgl[0]));
				} else {
					$malam 	= false;
					$oTgl 	= $loadTg;
				}

				$cols = ($malam) ? "out_malam = out_malam - " . $volume : "out_pagi = out_pagi - " . $volume;
				$sql2 = "update pro_master_inventory_out set lastupdate_time = NOW(), lastupdate_ip = '" . $_SERVER['REMOTE_ADDR'] . "', lastupdate_by = '" . $pic . "', 
							 out_cancel = out_cancel + " . $volume . ", " . $cols . " where tanggal_inv = '" . $oTgl . "' and id_terminal = '" . $term . "' and id_produk = '" . $produk . "'";
				$con->setQuery($sql2);
				$oke  = $oke && !$con->hasError();

				$cek1 = "select id_master from pro_inventory_vendor where tanggal_inven = '" . $loadTg . "' and id_terminal = '" . $term . "' and id_produk = '" . $produk . "' 
							 and id_vendor = '" . $vendor . "' and id_area = '" . $area . "'";
				$idpv = $con->getOne($cek1);
				$sql3 = "update pro_inventory_vendor set out_inven = out_inven - " . $volume . " where id_master = '" . $idpv . "'";
				$con->setQuery($sql3);
				$oke  = $oke && !$con->hasError();
			}
		}

		if ($oke) {
			$con->commit();
			$sqlSide = "
					select count(*) from(
						select a.id_dsd 
						from pro_po_ds_detail a 
						join pro_po_ds b on a.id_ds = b.id_ds
						where b.id_terminal = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['terminal']) . "' and b.is_submitted = 1 and a.is_loaded = 0
						union all
						select a.id_dsk 
						from pro_po_ds_kapal a 
						where a.terminal = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['terminal']) . "' and a.is_loaded = 0
					) a";
			$answer["badge"] = $con->getOne($sqlSide);
			$con->close();
			$answer["error"] = "";
		} else {
			$con->rollBack();
			$con->clearError();
			$con->close();
			$answer["error"] = "Maaf, sistem mengalami kendala teknis. Silahkan coba lagi..";
		}
		echo json_encode($answer);
	}
}


<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");



$auth	= new MyOtentikasi();
$con 	= new Connection();
$draw 	= isset($_POST["element"]) ? htmlspecialchars($_POST["element"], ENT_QUOTES) : 0;
$start 	= isset($_POST["start"]) ? htmlspecialchars($_POST["start"], ENT_QUOTES) : 0;
$length	= isset($_POST['length']) ? htmlspecialchars($_POST["length"], ENT_QUOTES) : 25;
$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
// $arrRol = array(11=>"BM", 17=>"OM",18=>"BM");
$arrRol = array(7 => "BM", 6 => "OM", 4 => "CFO", 15 => "MGR Finance");
$arrPosisi 	= array(1 => "Adm Finance", 2 => "BM", 3 => "OM", 4 => "MGR Finance", 5 => "CFO");



$q1	= isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';
$q2	= isset($_POST["q2"]) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : '';

$p = new paging;
$sql = "SELECT a.*,
    b.nama_customer, 
    b.alamat_customer,
    b.kode_pelanggan, 
    c.nama_kab, 
    d.nama_prov, 
    e.fullname, 
    g.nama_cabang, 
    h.realisasi,
    h.vol_plan,
	f.pembulatan,
    COALESCE((SELECT COALESCE(volume_close, 0)
                FROM pro_po_customer_close
                WHERE id_poc = a.id_poc AND st_Aktif = 'Y'),0) as volume_close_po,
    (SELECT role_approved from pro_sales_confirmation where id_customer=a.id_customer and id_poc=a.id_poc order by created_date desc limit 0,1) as role_approved,
    (SELECT disposisi from pro_sales_confirmation where id_customer=a.id_customer and id_poc=a.id_poc order by created_date desc limit 0,1) as disposisi  
FROM pro_po_customer a 
JOIN pro_customer b on a.id_customer = b.id_customer 
JOIN pro_master_kabupaten c on b.kab_customer = c.id_kab 
JOIN pro_master_provinsi d on b.prov_customer = d.id_prov 
JOIN acl_user e on b.id_marketing = e.id_user 
JOIN pro_penawaran f on a.id_penawaran = f.id_penawaran 
JOIN pro_master_cabang g on f.id_cabang = g.id_master 
LEFT JOIN (
    SELECT id_poc,
        sum(if(realisasi_kirim = 0, volume_kirim, realisasi_kirim)) as vol_plan,
        sum(realisasi_kirim) as realisasi 
    FROM pro_po_customer_plan 
    WHERE status_plan not in (2,3)
    group by id_poc
) h on a.id_poc = h.id_poc 
WHERE 1=1";
// $sql = "select a.*, b.nama_customer, b.alamat_customer, b.kode_pelanggan, c.nama_kab, d.nama_prov, e.fullname, g.nama_cabang, h.realisasi, h.vol_plan from pro_po_customer a join pro_customer b on a.id_customer = b.id_customer join pro_master_kabupaten c on b.kab_customer = c.id_kab join pro_master_provinsi d on b.prov_customer = d.id_prov join acl_user e on b.id_marketing = e.id_user join pro_penawaran f on a.id_penawaran = f.id_penawaran join pro_master_cabang g on f.id_cabang = g.id_master left join (select id_poc, sum(if(realisasi_kirim = 0,volume_kirim, realisasi_kirim)) as vol_plan, sum(realisasi_kirim) as realisasi from pro_po_customer_plan where status_plan not in (2,3) group by id_poc) h on a.id_poc = h.id_poc where 1=1";
if ($sesrol == 18) {
	if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']))
		$sql .= " and (b.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "' or b.id_marketing = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "')";
	else if (!paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']))
		$sql .= " and (b.id_group = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']) . "' or b.id_marketing = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "')";
} else if ($sesrol == 17 || $sesrol == 11) {
	$sql .= " and b.id_marketing = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "'";
}


if ($q1 != "")
	$sql .= " and (upper(b.nama_customer) like '%" . strtoupper($q1) . "%' or b.kode_pelanggan = '" . $q1 . "' or a.nomor_poc = '" . $q1 . "' or a.tanggal_poc = '" . tgl_db($q1) . "')";
if ($q2 != "")
	$sql .= " and a.poc_approved = '" . $q2 . "' and sm_result = 1";

$tot_record = $con->num_rows($sql);
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);

if ($sesrol == 17 or $sesrol == 18 or $sesrol == 11) {
	$sql2 = $sql . " ORDER BY CASE 
		WHEN EXISTS (SELECT 1 FROM pro_po_customer_close WHERE id_poc = a.id_poc AND st_Aktif = 'Y') THEN 2
		ELSE 1 END, a.id_poc DESC  limit " . $position . ", " . $length;
	$sql3 = $sql . "
		ORDER BY CASE 
			WHEN EXISTS (SELECT 1 FROM pro_po_customer_close WHERE id_poc = a.id_poc AND st_Aktif = 'Y') THEN 2
			ELSE 1 END, a.id_poc DESC limit " . $position . ", " . $length;
} else {
	$sql2 = $sql . " ORDER BY CASE 
			WHEN EXISTS (SELECT 1 FROM pro_po_customer_close WHERE id_poc = a.id_poc AND st_Aktif = 'Y') THEN 2
			ELSE 1 END, a.id_poc DESC  limit " . $position . ", " . $length;
}

$sql .= " ORDER BY CASE 
		WHEN EXISTS (SELECT 1 FROM pro_po_customer_close WHERE id_poc = a.id_poc AND st_Aktif = 'Y') THEN 2
		ELSE 1
	END, a.id_poc DESC  limit " . $position . ", " . $length;

$content = "";

$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);

$count 		= $position;

if ($tot_record <= 0) {
	$content .= '<tr><td colspan="8" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
	$tot_page 	= ceil($tot_record / $length);
	$id = array();

	$result2 	= $con->getResult($sql2);
	foreach ($result2 as $data) {
		$count++;
		$length--;
		$linkPlan	= BASE_URL_CLIENT . '/po-customer-plan.php?' . paramEncrypt('idr=' . $data['id_customer'] . '&idk=' . $data['id_poc']);
		$linkUnblock = BASE_URL_CLIENT . '/form-unblock-add.php?' . paramEncrypt('idk=' . $data['id_poc']);
		$linkDetail	= BASE_URL_CLIENT . '/po-customer-detail.php?' . paramEncrypt('idr=' . $data['id_customer'] . '&idk=' . $data['id_poc']);
		$linkHapus	= paramEncrypt("po_customer#|#" . $data['id_poc']);
		$temp 		= strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
		$alamat		= $data['alamat_customer'] . " " . ucwords($temp) . " " . $data['nama_prov'];
		$kodeCust	= ($data['kode_pelanggan']) ? '<b>' . $data['kode_pelanggan'] . '</b><br>' : '<b>-------</b><br>';
		$pathPt 	= $public_base_directory . '/files/uploaded_user/lampiran/' . $data['lampiran_poc'];
		$lampPt 	= $data['lampiran_poc_ori'];
		$total_volume_poc = $data['volume_poc'];
		$persentase_terkirim = ($data['realisasi'] / max($total_volume_poc, 1)) * 100;
		$progress_class = '';
		if ($persentase_terkirim == 0) {
			$progress_class = 'progress-bar-info';
		} elseif ($persentase_terkirim <= 20) {
			$progress_class = 'progress-bar-danger';
		} elseif ($persentase_terkirim <= 50) {
			$progress_class = 'progress-bar-warning';
		} elseif ($persentase_terkirim <= 70) {
			$progress_class = 'progress-bar-success';
		} else {
			$progress_class = 'progress-bar-primary';
		}

		$background = 'style="background-color:#f5f5f5"';
		// $background = '';

		if ($data['lampiran_poc'] && file_exists($pathPt)) {
			$linkPt = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=2&ktg=POC_" . $data['id_poc'] . "_&file=" . $lampPt);
			$attach = '<a href="' . $linkPt . '"><i class="fa fa-file-alt" title="' . $lampPt . '"></i></a>';
		} else {
			$attach = '-';
		}

		if ($data['pembulatan'] == 0) {
			$harga_pocnya = number_format($data['harga_poc'], 2);
		} elseif ($data['pembulatan'] == 1) {
			$harga_pocnya = number_format($data['harga_poc'], 0);
		} elseif ($data['pembulatan'] == 2) {
			$harga_pocnya = number_format($data['harga_poc'], 4);
		}

		if ($data['poc_approved'] == 1)
			$disposisi = 'Terverifikasi ' . $arrPosisi[$data['disposisi']] . '<br/><i>' . date("d/m/Y H:i:s", strtotime($data['tgl_approved'])) . '</i> WIB';
		else if ($data['poc_approved'] == 2) {
			$disposisi = 'Ditolak ' . $arrPosisi[$data['disposisi']];
			$background = 'style="background-color:#f5f5f5"';
		} else if ($data['disposisi_poc'] == 0)
			$disposisi = 'Terdaftar';
		else if ($data['disposisi_poc'] == 1)
			$disposisi = 'Verifikasi ' . $arrPosisi[$data['disposisi']];
		else $disposisi = '';

		$id[] = $data['id_poc'];





		$content .= '
				<tr class="clickable-row" data-href="' . $linkDetail . '" ' . $background . '>
					<td class="text-center">' . $count . '</td>
					<td>
						<p style="margin-bottom: 0px"><b>PO-' . str_pad($data['id_poc'], 4, '0', STR_PAD_LEFT) . '</b></p>
						<p style="margin-bottom: 0px"><i>' . $disposisi . '</i></p>
					</td>
					<td>' . $kodeCust . $data['nama_customer'] . '</td>
					<td>
						<p style="margin-bottom: 0px"><b>' . $data['nama_cabang'] . '</b></p>
						<p style="margin-bottom: 0px"><i>' . $data['fullname'] . '</i></p>
					</td>
					<td>
						<p style="margin-bottom: 0px"><b>' . $data['nomor_poc'] . '</b></p>
						<p style="margin-bottom: 0px">' . tgl_indo($data['tanggal_poc']) . '</p>
					</td>
					<td>
						<p style="margin-bottom: 0px">' . number_format($data['volume_poc']) . ' Liter (Rp. ' . $harga_pocnya . '/liter)</p>
						<div class="clearfix">
							<div style="float:left; width:65px;">Terkirim</div>
							<div style="float:left;">' . number_format($data['realisasi']) . ' Liter</div>
						</div>
						<div class="clearfix">
							<div style="float:left; width:65px;">Sisa Aktual</div>
							<div style="float:left;">' . number_format(($data['volume_poc'] - $data['realisasi'] - $data['volume_close_po'])) . ' Liter</div>
						</div>
						<div class="clearfix">
							<div style="float:left; width:65px;">Sisa Buku</div>
							<div style="float:left;">' . number_format(($data['volume_poc'] - $data['vol_plan'] - $data['volume_close_po'])) . ' Liter</div>
						</div>
						<div class="clearfix">
							<div style="float:left; width:65px;">Vol Close PO</div>
							<div style="float:left;">' . number_format(($data['volume_close_po'])) . ' Liter</div>
						</div>
					</td>
					<td class="text-center">
					<div class="progress">
						<div class="progress-bar ' . $progress_class . '" role="progressbar"  aria-valuenow="' . $persentase_terkirim . '"aria-valuemin="0" aria-valuemax="100" style="width:' . $persentase_terkirim . '%">
						<span style="color: black !important;">' . number_format($persentase_terkirim, 2) . '%</span>
						
						</div>
					</div>
				</td>
					<td class="text-center">' . $attach . '</td>
					<td class="text-center action">
						<a class="margin-sm btn btn-action btn-info" title="Detail" href="' . $linkDetail . '">
							<i class="fa fa-info-circle"></i>
						</a>' . ($data['poc_approved'] && $data['poc_approved'] == 1 ? '<a class="margin-sm btn btn-action btn-primary" title="Plan" href="' . $linkPlan . '"><i class="fa fa-file-alt"></i></a>' : '<a class="margin-sm btn btn-action btn-danger" data-param-idx="' . $linkHapus . '" data-action="deleteGrid"><i class="fa fa-trash"></i></a>') . '
            		</td>
				</tr>';
	}

	// if ($sesrol == 17 or $sesrol == 18 or $sesrol == 11) {
	// 	$result3 	= $con->getResult($sql3);
	// 	foreach ($result3 as $data) {
	// 		$count++;
	// 		$length--;
	// 		$linkPlan	= BASE_URL_CLIENT . '/po-customer-plan.php?' . paramEncrypt('idr=' . $data['id_customer'] . '&idk=' . $data['id_poc']);
	// 		$linkDetail	= BASE_URL_CLIENT . '/po-customer-detail.php?' . paramEncrypt('idr=' . $data['id_customer'] . '&idk=' . $data['id_poc']);
	// 		$linkHapus	= paramEncrypt("po_customer#|#" . $data['id_poc']);
	// 		$temp 		= strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
	// 		$alamat		= $data['alamat_customer'] . " " . ucwords($temp) . " " . $data['nama_prov'];
	// 		$kodeCust	= ($data['kode_pelanggan']) ? '<b>' . $data['kode_pelanggan'] . '</b><br>' : '<b>-------</b><br>';
	// 		$pathPt 	= $public_base_directory . '/files/uploaded_user/lampiran/' . $data['lampiran_poc'];
	// 		$lampPt 	= $data['lampiran_poc_ori'];
	// 		$total_volume_poc = $data['volume_poc'];
	// 		$persentase_terkirim = ($data['realisasi'] / max($total_volume_poc, 1)) * 100;
	// 		$progress_class = '';
	// 		if ($persentase_terkirim == 0) {
	// 			$progress_class = 'progress-bar-info';
	// 		} elseif ($persentase_terkirim <= 20) {
	// 			$progress_class = 'progress-bar-danger';
	// 		} elseif ($persentase_terkirim <= 50) {
	// 			$progress_class = 'progress-bar-warning';
	// 		} elseif ($persentase_terkirim <= 70) {
	// 			$progress_class = 'progress-bar-success';
	// 		} else {
	// 			$progress_class = 'progress-bar-primary';
	// 		}
	// 		// $background = 'style="background-color:#f5f5f5"';
	// 		$background = '';

	// 		if ($data['lampiran_poc'] && file_exists($pathPt)) {
	// 			$linkPt = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=2&ktg=POC_" . $data['id_poc'] . "_&file=" . $lampPt);
	// 			$attach = '<a href="' . $linkPt . '"><i class="fa fa-file-alt" title="' . $lampPt . '"></i></a>';
	// 		} else {
	// 			$attach = '-';
	// 		}

	// 		if ($data['poc_approved'] == 1)
	// 			$disposisi = 'Terverifikasi ' . $arrPosisi[$data['disposisi']] . '<br/><i>' . date("d/m/Y H:i:s", strtotime($data['tgl_approved'])) . '</i> WIB';
	// 		else if ($data['poc_approved'] == 2) {
	// 			$disposisi = 'Ditolak ' . $arrPosisi[$data['disposisi']];
	// 			$background = 'style="background-color:#f5f5f5"';
	// 		} else if ($data['disposisi_poc'] == 0)
	// 			$disposisi = 'Terdaftar';
	// 		else if ($data['disposisi_poc'] == 1)
	// 			$disposisi = 'Verifikasi ' . $arrPosisi[$data['disposisi']];
	// 		else $disposisi = '';

	// 		$id[] = $data['id_poc'];
	// 		$background = '';

	// 		$content .= '
	// 				<tr class="clickable-row" data-href="' . $linkDetail . '" ' . $background . '>
	// 					<td class="text-center">' . $count . '</td>
	// 					<td>
	// 						<p style="margin-bottom: 0px"><b>PO-' . str_pad($data['id_poc'], 4, '0', STR_PAD_LEFT) . '</b></p>
	// 						<p style="margin-bottom: 0px"><i>' . $disposisi . '</i></p>
	// 					</td>
	// 					<td>' . $kodeCust . $data['nama_customer'] . '</td>
	// 					<td>
	// 						<p style="margin-bottom: 0px"><b>' . $data['nama_cabang'] . '</b></p>
	// 						<p style="margin-bottom: 0px"><i>' . $data['fullname'] . '</i></p>
	// 					</td>
	// 					<td>
	// 						<p style="margin-bottom: 0px"><b>' . $data['nomor_poc'] . '</b></p>
	// 						<p style="margin-bottom: 0px">' . tgl_indo($data['tanggal_poc']) . '</p>
	// 					</td>
	// 					<td>
	// 						<p style="margin-bottom: 0px">' . number_format($data['volume_poc']) . ' Liter (Rp. ' . number_format($data['harga_poc']) . '/liter)</p>
	// 						<div class="clearfix">
	// 							<div style="float:left; width:65px;">Terkirim</div>
	// 							<div style="float:left;">' . number_format($data['realisasi']) . ' Liter</div>
	// 						</div>
	// 						<div class="clearfix">
	// 							<div style="float:left; width:65px;">Sisa Aktual</div>
	// 							<div style="float:left;">' . number_format(($data['volume_poc'] - $data['realisasi'] - $data['volume_close_po'])) . ' Liter</div>
	// 						</div>
	// 						<div class="clearfix">
	// 							<div style="float:left; width:65px;">Sisa Buku</div>
	// 							<div style="float:left;">' . number_format(($data['volume_poc'] - $data['vol_plan'] - $data['volume_close_po'])) . ' Liter</div>
	// 						</div>
	// 						<div class="clearfix">
	// 							<div style="float:left; width:65px;">Vol Close PO</div>
	// 							<div style="float:left;">' . number_format(($data['volume_close_po'])) . ' Liter</div>
	// 						</div>
	// 					</td>
	// 					<td class="text-center">
	// 				<div class="progress">
	// 					<div class="progress-bar ' . $progress_class . '" role="progressbar"  aria-valuenow="' . $persentase_terkirim . '"aria-valuemin="0" aria-valuemax="100" style="width:' . $persentase_terkirim . '%">
	// 					<span style="color: black !important;">' . number_format($persentase_terkirim, 2) . '%</span>

	// 					</div>
	// 				</div>
	// 			</td>
	// 					<td class="text-center">' . $attach . '</td>
	// 					<td class="text-center action">
	// 						<a class="margin-sm btn btn-action btn-info" title="Detail" href="' . $linkDetail . '"><i class="fa fa-info-circle"></i></a>
	// 						' . ($data['poc_approved'] && $data['poc_approved'] == 1
	// 			? '<a class="margin-sm btn btn-action btn-primary" title="Plan" href="' . $linkPlan . '"><i class="fa fa-file-alt"></i></a>'
	// 			: '<a class="margin-sm btn btn-action btn-danger" data-param-idx="' . $linkHapus . '" data-action="deleteGrid"><i class="fa fa-trash"></i></a>'
	// 		) . '
	//             		</td>
	// 				</tr>';
	// 	}
	// }

	$result 	= $con->getResult($sql);
	foreach ($result as $data) {
		$linkPlan	= BASE_URL_CLIENT . '/po-customer-plan.php?' . paramEncrypt('idr=' . $data['id_customer'] . '&idk=' . $data['id_poc']);
		$linkDetail	= BASE_URL_CLIENT . '/po-customer-detail.php?' . paramEncrypt('idr=' . $data['id_customer'] . '&idk=' . $data['id_poc']);
		$linkHapus	= paramEncrypt("po_customer#|#" . $data['id_poc']);
		$temp 		= strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
		$alamat		= $data['alamat_customer'] . " " . ucwords($temp) . " " . $data['nama_prov'];
		$kodeCust	= ($data['kode_pelanggan']) ? '<b>' . $data['kode_pelanggan'] . '</b><br>' : '<b>-------</b><br>';
		$pathPt 	= $public_base_directory . '/files/uploaded_user/lampiran/' . $data['lampiran_poc'];
		$lampPt 	= $data['lampiran_poc_ori'];
		$total_volume_poc = $data['volume_poc'];
		$persentase_terkirim = ($data['realisasi'] / max($total_volume_poc, 1)) * 100;
		$progress_class = '';
		if ($persentase_terkirim == 0) {
			$progress_class = 'progress-bar-info';
		} elseif ($persentase_terkirim <= 20) {
			$progress_class = 'progress-bar-danger';
		} elseif ($persentase_terkirim <= 50) {
			$progress_class = 'progress-bar-warning';
		} elseif ($persentase_terkirim <= 70) {
			$progress_class = 'progress-bar-success';
		} else {
			$progress_class = 'progress-bar-primary';
		}
		$background = '';

		if ($data['lampiran_poc'] && file_exists($pathPt)) {
			$linkPt = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=2&ktg=POC_" . $data['id_poc'] . "_&file=" . $lampPt);
			$attach = '<a href="' . $linkPt . '"><i class="fa fa-file-alt" title="' . $lampPt . '"></i></a>';
		} else {
			$attach = '-';
		}

		if ($data['poc_approved'] == 1)
			$disposisi = 'Terverifikasi ' . $arrPosisi[$data['disposisi']] . '<br/><i>' . date("d/m/Y H:i:s", strtotime($data['tgl_approved'])) . '</i> WIB';
		else if ($data['poc_approved'] == 2) {
			$disposisi = 'Ditolak ' . $arrPosisi[$data['disposisi']];
			$background = 'style="background-color:#f5f5f5"';
		} else if ($data['disposisi_poc'] == 0)
			$disposisi = 'Terdaftar';
		else if ($data['disposisi_poc'] == 1)
			$disposisi = 'Verifikasi ' . $arrPosisi[$data['disposisi']];
		else $disposisi = '';

		$background = '';

		if ($length > 0 && !in_array($data['id_poc'], $id)) {
			$count++;
			$length--;
			$content .= '
					<tr class="clickable-row" data-href="' . $linkDetail . '" ' . $background . '>
						<td class="text-center">' . $count . '</td>
						<td>
							<p style="margin-bottom: 0px"><b>PO-' . str_pad($data['id_poc'], 4, '0', STR_PAD_LEFT) . '</b></p>
							<p style="margin-bottom: 0px"><i>' . $disposisi . '</i></p>
						</td>
						<td>' . $kodeCust . $data['nama_customer'] . '</td>
						<td>
							<p style="margin-bottom: 0px"><b>' . $data['nama_cabang'] . '</b></p>
							<p style="margin-bottom: 0px"><i>' . $data['fullname'] . '</i></p>
						</td>
						<td>
							<p style="margin-bottom: 0px"><b>' . $data['nomor_poc'] . '</b></p>
							<p style="margin-bottom: 0px">' . tgl_indo($data['tanggal_poc']) . '</p>
						</td>
						<td>
							<p style="margin-bottom: 0px">' . number_format($data['volume_poc']) . ' Liter (Rp. ' . number_format($data['harga_poc']) . '/liter)</p>
							<div class="clearfix">
								<div style="float:left; width:65px;">Terkirim</div>
								<div style="float:left;">' . number_format($data['realisasi']) . ' Liter</div>
							</div>
							<div class="clearfix">
								<div style="float:left; width:65px;">Sisa Aktual</div>
								<div style="float:left;">' . number_format(($data['volume_poc'] - $data['realisasi'])) . ' Liter</div>
							</div>
							<div class="clearfix">
								<div style="float:left; width:65px;">Sisa Buku</div>
								<div style="float:left;">' . number_format(($data['volume_poc'] - $data['vol_plan'])) . ' Liter</div>
							</div>
						</td>
						<td class="text-center">
					<div class="progress">
						<div class="progress-bar ' . $progress_class . '" role="progressbar"  aria-valuenow="' . $persentase_terkirim . '"aria-valuemin="0" aria-valuemax="100" style="width:' . $persentase_terkirim . '%">
						<span style="color: black !important;">' . number_format($persentase_terkirim, 2) . '%</span>
						
						</div>
					</div>
				</td>
						<td class="text-center">' . $attach . '</td>
						<td class="text-center action">
							<a class="margin-sm btn btn-action btn-info" title="Detail" href="' . $linkDetail . '"><i class="fa fa-info-circle"></i></a>
							' . ($data['poc_approved'] && $data['poc_approved'] == 1
				? '<a class="margin-sm btn btn-action btn-primary" title="Plan" href="' . $linkPlan . '"><i class="fa fa-file-alt"></i></a>'
				: '<a class="margin-sm btn btn-action btn-danger" data-param-idx="' . $linkHapus . '" data-action="deleteGrid"><i class="fa fa-trash"></i></a>'
			) . '
						</td>
					</tr>';
		}
	}
}

$json_data = array(
	"items"		=> $content,
	"pages"		=> $tot_page,
	"page"		=> $page,
	"totalData"	=> $tot_record,
	"infoData"	=> "Showing " . ($position + 1) . " to " . $count . " of " . $tot_record . " entries",
);
echo json_encode($json_data);
/*<p style="margin-bottom: 0px">Terkirim : '.number_format($data['realisasi']).' Liter</p>
	<p style="margin-bottom: 0px">Sisa Aktual : '.number_format(($data['volume_poc'] - $data['realisasi'])).' Liter</p>
	<p style="margin-bottom: 0px">Sisa Buku : '.number_format(($data['volume_poc'] - $data['vol_plan'])).' Liter</p>*/

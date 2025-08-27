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
$q1	= isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';
$q2	= isset($_POST["q2"]) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : '';
$q3	= isset($_POST["q3"]) ? htmlspecialchars($_POST["q3"], ENT_QUOTES) : '';
$q4	= isset($_POST["q4"]) ? htmlspecialchars($_POST["q4"], ENT_QUOTES) : '';
$q5	= isset($_POST["q5"]) ? htmlspecialchars($_POST["q5"], ENT_QUOTES) : '';
$cabang	= isset($_POST["cabang"]) ? htmlspecialchars($_POST["cabang"], ENT_QUOTES) : '';

$sess_wil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

if ($sesrol == '25') {
	if ($cabang) {
		$filter_cabang = " a.cabang='" . $cabang . "' AND ";
		$order_by = " ORDER BY a.id_bpuj DESC";
	} else {
		$filter_cabang = "";
		$order_by = " ORDER BY a.id_bpuj DESC";
	}
} else {
	$filter_cabang = " a.cabang='" . $sess_wil . "' AND ";
	$order_by = "order by
	CASE WHEN a.disposisi_bpuj = 1 AND d.disposisi_realisasi IS NULL THEN a.tanggal_bpuj END DESC,
	CASE WHEN a.disposisi_bpuj = 2 AND d.disposisi_realisasi = 0 AND d.tanggal_realisasi > '2024-06-18' THEN a.tanggal_bpuj END DESC,
	a.tanggal_bpuj DESC";
}

$p = new paging;
$sql = "SELECT a.*, b.nomor_do, c.nomor_ds, d.disposisi_realisasi FROM pro_bpuj a JOIN pro_po_ds_detail b ON a.id_dsd=b.id_dsd JOIN pro_po_ds c ON b.id_ds=c.id_ds LEFT JOIN pro_bpuj_realisasi d ON a.id_bpuj=d.id_bpuj WHERE " . $filter_cabang . " a.is_active='1' AND a.disposisi_bpuj != '0'";

if ($q1 != "")
	$sql .= " and (upper(b.nomor_do) like '%" . strtoupper($q1) . "%' or upper(a.nomor_bpuj) like '%" . strtoupper($q1) . "%' or upper(a.nama_driver) like '%" . strtoupper($q1) . "%' or upper(a.no_unit) like '%" . strtoupper($q1) . "%')";

if ($q2 != "" && $q3 != "") {
	$sql .= " and (DATE(a.tanggal_bpuj) between '" . tgl_db($q2) . "' and '" . tgl_db($q3) . "')";
} else {
	if ($q2 != "") $sql .= " and (DATE(a.tanggal_bpuj) = '" . tgl_db($q2) . "')";
	if ($q3 != "") $sql .= " and (DATE(a.tanggal_bpuj) = '" . tgl_db($q3) . "')";
}

if ($q4 != "")
	$sql .= " and a.disposisi_bpuj = '" . $q4 . "'";
if ($q5 != "") {
	if ($q5 == "NULL") {
		$sql .= " and d.disposisi_realisasi IS NULL";
	} else {
		$sql .= " and d.disposisi_realisasi = '" . $q5 . "'";
	}
}

$tot_record = $con->num_rows($sql);
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);
$sql .= " " . $order_by . " limit " . $position . ", " . $length;

$content = "";

$count = 0;
if ($tot_record == 0) {
	$content .= '<tr><td colspan="11" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
	$count 		= $position;
	$tot_page 	= ceil($tot_record / $length);
	$result 	= $con->getResult($sql);
	foreach ($result as $data) {
		$count++;

		$realisasi = "SELECT * FROM pro_bpuj_realisasi WHERE id_bpuj='" . $data['id_bpuj'] . "'";
		$row_realisasi = $con->getRecord($realisasi);

		if ($row_realisasi) {
			if ($row_realisasi['disposisi_realisasi'] == 1) {
				$status_realisasi = "Approved by " . $row_realisasi['approved_by'];
			} else {
				if ($row_realisasi['created_at'] > '2024-06-18') {
					$status_realisasi = "Menunggu Approval";
				} else {
					$status_realisasi = "Approved by System";
				}
			}
		} else {
			$status_realisasi = "Belum Realisasi";
		}

		if ($data['disposisi_bpuj'] == 1) {
			$background = 'style="background-color:#f5f5f5"';
			$status = "Menunggu Verifikasi";
			$linkRealisasi = "";
		} elseif ($data['disposisi_bpuj'] == 2) {
			if ($row_realisasi && $row_realisasi['disposisi_realisasi'] == 0) {
				if ($row_realisasi['created_at'] > '2024-06-18') {
					$background = 'style="background-color:#f5f5f5"';
				} else {
					$background = '';
				}
				$status = "Approved by " . $data['diberikan_oleh'];
			} else {
				$background = "";
				$status = "Approved by " . $data['diberikan_oleh'];
			}
		}
		$linkDetail	= BASE_URL_CLIENT . '/detail_bpuj.php?' . paramEncrypt('id_bpuj=' . $data['id_bpuj']);

		$content .= '
			<tr ' . $background . '>
				<td class="text-center">' . $count . '</td>
				<td class="text-center">' . tgl_indo($data['tanggal_bpuj']) . '</td>
				<td class="text-center">' . $data['nomor_bpuj'] . '</td>
				<td class="text-center">' . $data['nomor_do'] . '</td>
				<td class="text-center">' . $data['nomor_ds'] . '</td>
				<td class="text-right">' . number_format($data['total_uang_bpuj']) . '</td>
				<td class="text-right">' . number_format($data['yang_dibayarkan']) . '</td>
				<td class="text-right">' . number_format($row_realisasi['total_realisasi']) . '</td>
				<td class="text-center">' . $status . '</td>
				<td class="text-center">' . $status_realisasi . '</td>
				<td class="text-center action">
					<a target="_blank" class="margin-sm btn btn-info btn-sm" title="Detail Data" href="' . $linkDetail . '"><i class="fa fa-info"></i></a>
				</td>
			</tr>';
	}
}

$json_data = array(
	"items"		=> $content,
	"pages"		=> $tot_page,
	"page"		=> $page,
	"totalData"	=> $tot_record,
	"infoData"	=> "Showing " . ($position + 1) . " - " . $count . " of " . $tot_record . " entries",
);
echo json_encode($json_data);

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

$sess_wil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

$linkExport = BASE_URL_CLIENT . '/report/bpuj-exp.php?' . paramEncrypt('q1=' . $q1 . '&q2=' . $q2 . '&q3=' . $q3 . '&q4=' . $q4 . '&q5=' . $q5);

$p = new paging;
$sql = "SELECT a.*, a.id_bpuj as id_bpujnya, b.nomor_do, c.nomor_ds, d.id_customer, e.disposisi_realisasi FROM pro_bpuj a JOIN pro_po_ds_detail b ON a.id_dsd=b.id_dsd JOIN pro_po_ds c ON b.id_ds=c.id_ds JOIN pro_po_customer d ON b.id_poc=d.id_poc LEFT JOIN pro_bpuj_realisasi e ON a.id_bpuj=e.id_bpuj WHERE a.cabang='" . $sess_wil . "' AND a.is_active='1'";

if ($q1 != "")
	$sql .= " and (upper(b.nomor_do) like '%" . strtoupper($q1) . "%' or upper(a.nomor_bpuj) like '%" . strtoupper($q1) . "%' or upper(a.nama_driver) like '%" . strtoupper($q1) . "%' or upper(a.no_unit) like '%" . strtoupper($q1) . "%')";

if ($q2 != "" && $q3 != "") {
	$sql .= " and (DATE(a.tanggal_bpuj) between '" . tgl_db($q2) . "' and '" . tgl_db($q3) . "')";
} else {
	if ($q2 != "") $sql .= " and DATE(a.tanggal_bpuj) = '" . tgl_db($q2) . "'";
	if ($q3 != "") $sql .= " and DATE(a.tanggal_bpuj) = '" . tgl_db($q3) . "'";
}

if ($q4 != "")
	$sql .= " and a.disposisi_bpuj = '" . $q4 . "'";
if ($q5 != "") {
	if ($q5 == "NULL") {
		$sql .= " and e.disposisi_realisasi IS NULL";
	} else {
		$sql .= " and e.disposisi_realisasi = '" . $q5 . "'";
	}
}


$tot_record = $con->num_rows($sql);
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);
$sql .= " order by 
CASE WHEN a.disposisi_bpuj = 1 AND e.disposisi_realisasi IS NULL THEN a.tanggal_bpuj END DESC,
CASE WHEN a.disposisi_bpuj = 2 AND e.disposisi_realisasi = 0 AND e.tanggal_realisasi > '2024-06-18' THEN a.tanggal_bpuj END DESC,
CASE WHEN a.disposisi_bpuj = 2 AND e.disposisi_realisasi IS NULL THEN a.tanggal_bpuj END DESC,
CASE WHEN a.disposisi_bpuj = 2 AND e.disposisi_realisasi = 0 THEN a.tanggal_bpuj END DESC,
a.tanggal_bpuj DESC limit " . $position . ", " . $length;

// CASE WHEN a.disposisi_bpuj = 1 AND d.disposisi_realisasi IS NULL THEN a.tanggal_bpuj END DESC,
// CASE WHEN a.disposisi_bpuj = 2 AND d.disposisi_realisasi = 0 AND d.tanggal_realisasi > '2024-06-15' THEN a.tanggal_bpuj END DESC,

$content = "";

$count = 0;
if ($tot_record == 0) {
	$content .= '<tr><td colspan="10" style="text-align:center"><input type="hidden" id="uriExp" value="' . $linkExport . '" />Data tidak ditemukan </td></tr>';
} else {
	$count 		= $position;
	$tot_page 	= ceil($tot_record / $length);
	$result 	= $con->getResult($sql);
	foreach ($result as $data) {
		$count++;

		$realisasi = "SELECT * FROM pro_bpuj_realisasi WHERE id_bpuj='" . $data['id_bpujnya'] . "'";
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

		if ($data['disposisi_bpuj'] == 0) {
			$status = "Belum di kirim ke Admin";
			$linkEdit	= BASE_URL_CLIENT . '/_get_form_bpuj.php?' . paramEncrypt('id_cust=' . $data['id_customer'] . '&id_dsd=' . $data['id_dsd']);
			$buttonEdit = '<a target="_blank" class="margin-sm btn btn-primary btn-sm" title="Edit Data" href="' . $linkEdit . '" data-idnya="' . $data['id_dsd'] . '"><i class="fa fa-edit"></i></a>';
		} elseif ($data['disposisi_bpuj'] == 1) {
			$status = "Verifikasi Admin Finance";
			$linkEdit	= BASE_URL_CLIENT . '/_get_form_bpuj.php?' . paramEncrypt('id_cust=' . $data['id_customer'] . '&id_dsd=' . $data['id_dsd']);
			$buttonEdit = '<a target="_blank" class="margin-sm btn btn-primary btn-sm" title="Edit Data" href="' . $linkEdit . '" data-idnya="' . $data['id_dsd'] . '"><i class="fa fa-edit"></i></a>';
		} elseif ($data['disposisi_bpuj'] == 2) {
			$status = "Approved by " . $data['diberikan_oleh'];
			$buttonEdit = '';
		}

		$linkDetail	= BASE_URL_CLIENT . '/detail_bpuj_log.php?' . paramEncrypt('id_bpuj=' . $data['id_bpuj']);

		if ($data['disposisi_bpuj'] == 2) {
			if ($row_realisasi) {
				if ($row_realisasi['disposisi_realisasi'] == 0) {
					if ($row_realisasi['created_at'] > '2024-06-18') {
						$linkRealisasi	= BASE_URL_CLIENT . '/realisasi_bpuj.php?' . paramEncrypt('id_cust=' . $data['id_customer'] . '&id_dsd=' . $data['id_dsd'] . '&id_bpuj=' . $data['id_bpujnya']);

						$buttonRealisasi = '<a target="_blank" class="margin-sm btn btn-success btn-sm" title="Realisasi" href="' . $linkRealisasi . '" data-idnya="' . $data['id_dsd'] . '" data-id_bpujnya="' . $data['id_bpuj'] . '"><i class="fa fa-table"></i></a>';
					} else {
						$buttonRealisasi = "";
					}
				} else {
					$buttonRealisasi = "";
				}
			} else {
				$linkRealisasi	= BASE_URL_CLIENT . '/realisasi_bpuj.php?' . paramEncrypt('id_cust=' . $data['id_customer'] . '&id_dsd=' . $data['id_dsd'] . '&id_bpuj=' . $data['id_bpujnya']);

				$buttonRealisasi = '<a target="_blank" class="margin-sm btn btn-success btn-sm" title="Realisasi" href="' . $linkRealisasi . '" data-idnya="' . $data['id_dsd'] . '" data-id_bpujnya="' . $data['id_bpuj'] . '"><i class="fa fa-table"></i></a>';
			}
		} else {
			$buttonRealisasi = "";
		}

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
				<td class="text-center">' . $status . '<input type="hidden" id="uriExp" value="' . $linkExport . '" /></td>
				<td class="text-center">' . $status_realisasi . '<input type="hidden" id="uriExp" value="' . $linkExport . '" /></td>
				<td class="text-center action">
					<a target="_blank" class="margin-sm btn btn-info btn-sm" title="Detail Data" href="' . $linkDetail . '"><i class="fa fa-info"></i></a>
					' . $buttonEdit . '
					' . $buttonRealisasi . '
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

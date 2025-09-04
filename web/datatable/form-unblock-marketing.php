
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
$sql = "SELECT a.*, b.nama_customer FROM pro_unblock_customer as a JOIN pro_customer as b ON a.id_customer=b.id_customer ORDER BY a.id DESC";

$tot_record = $con->num_rows($sql);
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);

$content = "";

$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);

$count = 0;
if ($tot_record == 0) {
	$content .= '<tr><td colspan="9" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
	$tot_page 	= ceil($tot_record / $length);
	$count = $position;
	$result = $con->getResult($sql);
	foreach ($result as $data) {
		$count++;

		$sql_file = "SELECT * FROM pro_lampiran_unblock WHERE id_unblock = '" . $data['id'] . "'";
		$res_file = $con->getResult($sql_file);

		if ($data['disposisi'] == 0) {
			$disposisi = "Proses Verifikasi";
			if ($data['is_admin'] == 0) {
				$status = "Verifikasi Admin";
			} else {
				if ($data['is_finance'] == 0) {
					$status = "Verifikasi Finance";
				} else {
					if ($data['is_mgr_fin'] == 0) {
						$status = "Verifikasi Manager Finance";
					} else {
						if ($data['is_cfo'] == 0) {
							$status = "Verifikasi CFO";
						} else {
							if ($data['is_ceo'] == 0) {
								$status = "Verifikasi CEO";
							}
						}
					}
				}
			}
		} elseif ($data['disposisi'] == 1) {
			$disposisi = "Terverifikasi";
			$status = "Approved by " . $data['pic_ceo'] . " " . date("d/m/Y", strtotime($data['date_ceo']));
		} elseif ($data['disposisi'] == 2) {
			$disposisi = "Rejected";
			if ($data['is_admin'] == 2) {
				$status = "Rejected by " . $data['pic_admin'] . " " . date("d/m/Y", strtotime($data['date_admin']));
			} else {
				if ($data['is_finance'] == 2) {
					$status = "Rejected by " . $data['pic_finance'] . " " . date("d/m/Y", strtotime($data['date_finance']));
				} else {
					if ($data['is_mgr_fin'] == 2) {
						$status = "Rejected by " . $data['pic_mgr_fin'] . " " . date("d/m/Y", strtotime($data['date_mgr_fin']));
					} else {
						if ($data['is_cfo'] == 2) {
							$status = "Rejected by " . $data['pic_cfo'] . " " . date("d/m/Y", strtotime($data['date_cfo']));
						} else {
							if ($data['is_ceo'] == 0) {
								$status = "Rejected by " . $data['pic_ceo'] . " " . date("d/m/Y", strtotime($data['date_ceo']));
							}
						}
					}
				}
			}
		} elseif ($data['disposisi'] == 3) {
			$disposisi = "Lunas";
			$status = "Approved by " . $data['pic_ceo'] . " " . date("d/m/Y", strtotime($data['date_ceo']));
		}

		$btnFile = "<button type='button' class='btn btn-primary btn-sm btnShowFiles' title='Lampiran' data-param-idx='" . htmlspecialchars(json_encode($res_file), ENT_QUOTES, 'UTF-8') . "'><i class='fas fa-file-archive'></i></button>";

		$linkHistory = $data['disposisi'] . "#|#" . $data['is_admin'] . "#|#" . $data['date_admin'] . "#|#" . $data['pic_admin'] . "#|#" . $data['is_finance'] . "#|#" . $data['date_finance'] . "#|#" . $data['pic_finance'] . "#|#" . $data['is_mgr_fin'] . "#|#" . $data['date_mgr_fin'] . "#|#" . $data['pic_mgr_fin'] . "#|#" . $data['is_cfo'] . "#|#" . $data['date_cfo'] . "#|#" . $data['pic_cfo'] . "#|#" . $data['is_ceo'] . "#|#" . $data['date_ceo'] . "#|#" . $data['pic_ceo'];

		$linkCetak = ACTION_CLIENT . '/form-unblock-cetak.php?' . paramEncrypt('idr=' . $data['id']);

		$btnCetak = '<a target="_blank" class="btn btn-danger btn-sm" href="' . $linkCetak . '" title="Cetak"><i class="fas fa-file-pdf"></i></a>';

		$btnHistory = '<small><a href="#" class="btnShowHistory" data-param-idx="' . $linkHistory . '">History Approval</a></small>';

		if ($data['disposisi'] == 0) {
			$linkHapus	= paramEncrypt("form_unblock#|#" . $data['id']);
			$btnHapus = '<a class="btn btn-warning btn-sm" title="Delete" data-param-idx="' . $linkHapus . '" data-action="deleteGrid"><i class="fa fa-trash"></i></a>';
		} else {
			$btnHapus = "";
		}

		$content .= '
				<tr>
					<td class="text-center">' . $count . '</td>
					<td class="text-center">' . $data['nomor_dokumen'] . '</td>
					<td class="text-center">' . $data['nama_customer'] . '</td>
					<td class="text-center">Rp. ' . number_format($data['cl_temp']) . '</td>
					<td class="text-center">' . $data['top_temp'] . '</td>
					<td class="text-center">' . tgl_indo($data['date_created']) . '</td>
					<td class="text-center">
					' . $disposisi . '
					<hr>
					' . $status . '
					<br>
					' . $btnHistory . '
					</td>
					<td class="text-center">' . $btnFile . '</td>
					<td class="text-center">
					' . $btnCetak . '
					' . $btnHapus . '
					</td>
				</tr>';
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

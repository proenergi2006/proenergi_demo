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
$length	= isset($_POST['length']) ? htmlspecialchars($_POST["length"], ENT_QUOTES) : 10;
$q1	= isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';

$p = new paging;
$sql = "select * from pro_attach_harga_minyak where 1=1";

if ($q1 != "")
	$sql .= " and periode_awal = '" . tgl_db($q1) . "'";

$tot_record = $con->num_rows($sql);
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);
$sql .= " order by periode_awal desc limit " . $position . ", " . $length;

$content = "";
if ($tot_record <= 0) {
	$content .= '<tr><td colspan="4" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
	$count 		= $position;
	$tot_page 	= ceil($tot_record / $length);
	$result 	= $con->getResult($sql);
	foreach ($result as $data) {
		$count++;
		$linkEdit	= BASE_URL_CLIENT . '/add-attach-harga-minyak.php?' . paramEncrypt('idr=' . $data['id_master']);
		$linkDetail	= BASE_URL_CLIENT . '/detil-attach-harga-minyak.php?' . paramEncrypt('idr=' . $data['id_master']);
		$linkHapus	= paramEncrypt("attach_harga_minyak#|#" . $data['id_master']);
		$pathAt 	= $public_base_directory . '/files/uploaded_user/lampiran/' . $data['attach_harga_ori'];
		$linkAt 	= ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=2&ktg=aPrice_" . $data['id_master'] . "_&file=" . $data['attach_harga_ori']);
		$linkAttach = '<p><a href="' . $linkAt . '" title="' . $data['attach_harga_ori'] . '"><i class="fa fa-file-text-o jarak-kanan"></i>lampiran</a></p>';

		$content .= '
				<tr class="clickable-row" data-href="' . $linkDetail . '">
					<td class="text-center">' . date("d/m/Y", strtotime($data['periode_awal'])) . ' - ' . date("d/m/Y", strtotime($data['periode_akhir'])) . '</td>
					<td>' . $data['note_attach'] . '</td>
					<td class="text-center">' . $linkAttach . '</td>
					<td class="text-center action">
					<a class="margin-sm btn btn-action btn-info" title="Detil" href="' . $linkDetail . '"><i class="fa fa-table"></i></a>
					' . (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role'])  == 21 ? '
						<a class="margin-sm btn btn-action btn-info" title="Edit" href="' . $linkEdit . '"><i class="fa fa-edit"></i></a>
						<a class="margin-sm delete btn btn-action btn-danger" title="Delete" data-param-idx="' . $linkHapus . '" data-action="deleteGrid">
						<i class="fa fa-trash"></i></a>
					' : '&nbsp;') . '
				
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

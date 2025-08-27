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
$q1	= isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';

$sesuser 	= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);
$sesrole 	= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$seswil 	= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$sesgroup 	= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);

if ($sesrole == '1') {
	$filter_wil = "";
} elseif ($sesrole == '14' && $seswil == '1') {
	$filter_wil = "";
} elseif ($sesrole == '14') {
	$filter_wil = " AND a.id_cabang = '" . $seswil . "'";
} else {
	$filter_wil = " AND a.id_cabang = '" . $seswil . "'";
}

$p = new paging;
$sql = "SELECT a.*, b.nama_cabang
		from pro_master_ruangan a
		left join pro_master_cabang b on b.id_master = a.id_cabang
		where 1=1
		" . $filter_wil . "
		and a.is_active = 1";

if ($q1 != "")
	$sql .= " and (upper(a.nama_ruangan) like '%" . strtoupper($q1) . "%' or upper(b.nama_cabang) like '%" . strtoupper($q1) . "%')";

$tot_record = $con->num_rows($sql);
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);
$sql .= " order by a.id_cabang, a.created_time desc limit " . $position . ", " . $length;

$content = "";
$count = 0;
if ($tot_record ==  0) {
	$content .= '<tr><td colspan="4" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
	$count 		= $position;
	$tot_page 	= ceil($tot_record / $length);
	$result 	= $con->getResult($sql);
	foreach ($result as $data) {
		$count++;
		$linkView 	= BASE_URL_CLIENT . '/reservasi-ruangan-master-add.php?' . paramEncrypt('idr=' . $data['id_ruangan']);
		$linkHapus	= paramEncrypt("master_ruangan#|#" . $data['id_ruangan']);

		$sql2 = "SELECT * FROM pro_reservasi_ruangan where id_ruangan = '" . $data['id_ruangan'] . "'";
		$row = $con->getRecord($sql2);

		if ($row) {
			$hideBtn = "hide";
		} else {
			$hideBtn = "";
		}

		$content .= '
				<tr class="clickable-row" data-href="' . $linkView . '">
					<td class="text-center">' . $count . '</td>
					<td>' . $data['nama_cabang'] . '</td>
					<td>' . $data['nama_ruangan'] . '</td>
					<td class="text-center">
						<a class="btn btn-action btn-info jarak-kanan" href="' . $linkView . '"><i class="fa fa-edit"></i></a> 
						<a class="margin-sm btn btn-action btn-danger ' . $hideBtn . '" title="Delete" data-param-idx="' . $linkHapus . '" data-action="deleteGrid">
						<i class="fa fa-trash"></i></a>
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

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
$q2	= isset($_POST["q2"]) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : '';
$q3	= isset($_POST["q3"]) ? htmlspecialchars($_POST["q3"], ENT_QUOTES) : '';

$p = new paging;
$sql = "
		select a.*, b.nama_cabang, c.nama_area 
		from pro_master_terminal a 
		left join pro_master_cabang b on a.id_cabang = b.id_master 
		left join pro_master_area c on a.id_area = c.id_master 
		where 1=1 
	";

if ($q1 != "")
	$sql .= " and upper(a.nama_terminal) like '%" . strtoupper($q1) . "%'";
if ($q2 != "" && $q2 != 2)
	$sql .= " and a.is_active = '" . $q2 . "'";
if ($q3 != "")
	$sql .= " and a.id_cabang = '" . $q3 . "'";

$tot_record = $con->num_rows($sql);
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);
$sql .= " order by a.is_active, a.id_cabang ,a.nama_terminal limit " . $position . ", " . $length;

$content = "";
if ($tot_record <= 0) {
	$content .= '<tr><td colspan="7" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
	/*<td>
			'.($data['id_cabang'] ? '<b>'.$data['nama_cabang'].'</b>' : '<i>Cabang belum dipilih</i>').'<br />
			'.($data['id_area'] ? 'Area '.$data['nama_area'] : '<i>Area belum dipilih</i>').'
		</td>*/

	$count 		= $position;
	$tot_page 	= ceil($tot_record / $length);
	$result 	= $con->getResult($sql);
	foreach ($result as $data) {
		if ($data['kategori_terminal'] == 1) {
			$kategori_terminal = 'Depo';
		} elseif ($data['kategori_terminal'] == 2) {
			$kategori_terminal = 'Dispenser';
		} elseif ($data['kategori_terminal'] == 3) {
			$kategori_terminal = 'Truck Gantung';
		} else {
			$kategori_terminal = '-';
		}

		$count++;
		$linkDetail	= BASE_URL_CLIENT . '/detil-master-terminal.php?' . paramEncrypt('idr=' . $data['id_master']);
		$linkEdit	= BASE_URL_CLIENT . '/add-master-terminal.php?' . paramEncrypt('idr=' . $data['id_master']);
		$linkHapus	= paramEncrypt("master_terminal#|#" . $data['id_master']);
		$active		= ($data["is_active"] == 1) ? "Active" : "Not Active";
		$content .= '
				<tr class="clickable-row" data-href="' . $linkDetail . '">
					<td class="text-center">' . $count . '</td>
					<td>' . ($data['initial'] != '' ? '(' . $data['initial'] . ') ' : '') . $data['nama_terminal'] . '</td>
					<td>' . $data['tanki_terminal'] . '</td>
					<td>' . ($data['id_cabang'] ? $data['nama_cabang'] : '<i>Cabang belum dipilih</i>') . '</td>
					<td>' . $data['lokasi_terminal'] . '</td>
					<td class="text-center">' . $kategori_terminal . '</td>
					<td class="text-right">' . number_format($data['batas_atas']) . ' Liter</td>
					<td class="text-right">' . number_format($data['batas_bawah']) . ' Liter</td>
					<td class="text-center">' . $active . '</td>
					<td class="text-center action">
						<a class="margin-sm btn btn-action btn-info" title="Detil" href="' . $linkDetail . '"><i class="fa fa-info-circle"></i></a>
						<a class="margin-sm btn btn-action btn-info" title="Edit" href="' . $linkEdit . '"><i class="fa fa-edit"></i></a>
						<a class="margin-sm delete btn btn-action btn-danger" title="Delete" data-param-idx="' . $linkHapus . '" data-action="deleteGrid">
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

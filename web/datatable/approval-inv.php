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
$q4	= isset($_POST["q4"]) ? htmlspecialchars($_POST["q4"], ENT_QUOTES) : '';
$path = BASE_IMAGE . "/";


$p = new paging;
$sql = "SELECT a.*, b.nama_cabang FROM pro_master_approval_invoice a JOIN pro_master_cabang b ON a.cabang=b.id_master WHERE 1=1";

if ($q1 != "")
	$sql .= " and (upper(a.nama) like '%" . strtoupper($q1) . "%' or upper(a.jabatan) like '%" . strtoupper($q1) . "%' or upper(b.nama_cabang) like '%" . strtoupper($q1) . "%')";

$tot_record = $con->num_rows($sql);
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);
$sql .= " order by a.is_active DESC limit " . $position . ", " . $length;

$content = "";
$count = 0;
if ($tot_record <= 0) {
	$content .= '<tr><td colspan="6" style="text-align:center">Data not found </td></tr>';
} else {
	$count 		= $position;
	$tot_page 	= ceil($tot_record / $length);
	$result 	= $con->getResult($sql);

	foreach ($result as $data) {
		$count++;
		$linkEdit	= BASE_URL_CLIENT . '/add-approval-invoice.php?' . paramEncrypt('idr=' . $data['id_master']);
		if ($data['is_active'] == '1') {
			$status = "Active";
		} else {
			$status = "Not Active";
		}

		$content .= '
				<tr>
					<td class="text-center">' . $count . '</td>
					<td class="text-left">
						<span>' . ucwords($data['nama']) . '</span>
					</td>
					<td class="text-center">
						<span>' . ucwords($data['jabatan']) . '</span>	
					</td>
					<td class="text-center">
						<span>' . $data['nama_cabang'] . '</span>
					</td>
					<td class="text-center">
						<span>' . $status . '</span>
					</td>
					<td class="text-center action">
						<a class="margin-sm btn btn-action btn-info" href="' . $linkEdit . '" title="Edit"><i class="fa fa-edit"></i></a>
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

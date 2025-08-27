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
$sql = "SELECT * FROM pro_button_control";

$tot_record = $con->num_rows($sql);
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);
$sql .= " order by id DESC limit " . $position . ", " . $length;

$content = "";
$count = 0;
if ($tot_record <= 0) {
	$content .= '<tr><td colspan="7" style="text-align:center">Data not found </td></tr>';
} else {
	$count 		= $position;
	$tot_page 	= ceil($tot_record / $length);
	$result 	= $con->getResult($sql);

	foreach ($result as $data) {
		$count++;
		if ($data['status'] == '1') {
			$status = "Closed";
			$button = '<button class="btn btn-success btn-sm openModal" data-param="' . paramEncrypt('open') . '" data-id="' . paramEncrypt($data['id']) . '">Buka</button>';
		} else {
			$status = "Open";
			$button = '<button class="btn btn-danger btn-sm openModal" data-param="' . paramEncrypt('close') . '" data-id="' . paramEncrypt($data['id']) . '">Tutup</button>';
		}

		$content .= '
				<tr>
					<td class="text-center">' . $count . '</td>
					<td class="text-center">
						<span>' . ucwords($data['button']) . '</span>
					</td>
					<td class="text-center">
						<span>' . ucwords($data['keterangan']) . '</span>	
					</td>
					<td class="text-center">
						<span>' . $status . '</span>
					</td>
					<td class="text-center">
						<span>' . ucwords($data['updated_by']) . '</span>
					</td>
					<td class="text-center">
						<span>' . tgl_indo($data['updated_at']) . ' ' . date("H:i:s", strtotime($data['updated_at'])) . '</span>
            		</td>
					<td class="text-center action">
						' . $button . '
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

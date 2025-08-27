<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth    = new MyOtentikasi();
$con     = new Connection();
$draw     = isset($_POST["element"]) ? htmlspecialchars($_POST["element"], ENT_QUOTES) : 0;
$start     = isset($_POST["start"]) ? htmlspecialchars($_POST["start"], ENT_QUOTES) : 0;
$length    = isset($_POST['length']) ? htmlspecialchars($_POST["length"], ENT_QUOTES) : 10;
$q4 = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$id_user = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);
$id_wilayah = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$p = new paging;
$sql = "SELECT * FROM pro_top_incentive";

$tot_record = $con->num_rows($sql);
$tot_page     = ceil($tot_record / $length);
$page        = ($start > $tot_page) ? $start - 1 : $start;
$position     = $p->findPosition($length, $tot_record, $page);
$sql .= " ORDER BY top ASC limit " . $position . ", " . $length;

$content = "";
$count = 0;
if ($tot_record <= 0) {
    $content .= '<tr><td colspan="12" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
    $count         = $position;
    $tot_page     = ceil($tot_record / $length);
    $result     = $con->getResult($sql);

    foreach ($result as $data) {
        $count++;

        $content .= '
		<tr>
			<td class="text-center">
			' . $count . '
			</td>
			<td class="text-center">
				<p style="margin-bottom:0px">' . $data['top'] . '</p>
			</td>
		</tr>';
    }
}

$json_data = array(
    "items"        => $content,
    "pages"        => $tot_page,
    "page"        => $page,
    "totalData"    => $tot_record,
    "infoData"    => "Showing " . ($position + 1) . " to " . $count . " of " . $tot_record . " entries",
);
echo json_encode($json_data);

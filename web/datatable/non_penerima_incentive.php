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
$q1    = isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';
$q2    = isset($_POST["q2"]) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : '';

$p = new paging;
$sql = "SELECT a.*, b.fullname, REPLACE(c.role_name, 'Role', '') AS role_name, d.nama_cabang FROM pro_non_penerima_incentive as a
JOIN acl_user as b ON a.id_user=b.id_user JOIN acl_role as c ON b.id_role=c.id_role JOIN pro_master_cabang as d ON b.id_wilayah=d.id_master where 1=1";

// if ($q1 != "")
// 	$sql .= " and (upper(nama_vendor) like '%" . strtoupper($q1) . "%')";
// if ($q2 != "" && $q2 != 2)
// 	$sql .= " and is_active = '" . $q2 . "'";

$tot_record = $con->num_rows($sql);
$tot_page     = ceil($tot_record / $length);
$page        = ($start > $tot_page) ? $start - 1 : $start;
$position     = $p->findPosition($length, $tot_record, $page);
$sql .= " order by a.id DESC limit " . $position . ", " . $length;

$content = "";
if ($tot_record <= 0) {
    $content .= '<tr><td colspan="6" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
    $count         = $position;
    $tot_page     = ceil($tot_record / $length);
    $result     = $con->getResult($sql);
    foreach ($result as $data) {
        $count++;

        $linkHapus    = paramEncrypt("non_penerima_incentive#|#" . $data['id']);
        $btnHapus = '<a class="margin-sm btn btn-action btn-danger " title="Delete" data-param-idx="' . $linkHapus . '" data-action="deleteGrid"><i class="fa fa-trash"></i></a>';

        $active        = ($data["status"] == 1) ? "Active" : "Not Active";
        $content .= '
		<tr>
			<td class="text-center">' . $count . '</td>
			<td class="text-center">' . $data['fullname'] . '</td>
			<td class="text-center">' . $data['nama_cabang'] . '</td>
			<td class="text-center">' . $data['role_name'] . '</td>
			<td class="text-center">' . $btnHapus . '</td>
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

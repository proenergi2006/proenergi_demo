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

$role   = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$wilayah = paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]);
$id_user = paramDecrypt($_SESSION["sinori" . SESSIONID]["id_user"]);

$p = new paging;
$sql = "SELECT a.*, CONCAT(b.kode_pelanggan,' ',b.nama_customer) as nama_customer, c.fullname as marketingnya, b.id_wilayah FROM pro_master_penerima_refund a JOIN pro_customer b ON a.id_customer=b.id_customer JOIN acl_user c ON b.id_marketing=c.id_user";

if ($role == '11' || $role == '17') {
	$sql .= " WHERE b.id_marketing = '" . $id_user . "'";
} elseif ($role == '18') {
	$sql .= " WHERE b.id_wilayah = '" . $wilayah . "'";
}

if ($q1 != "")
	$sql .= " and (upper(b.nama_customer) like '%" . strtoupper($q1) . "%' or upper(b.kode_pelanggan) like '%" . strtoupper($q1) . "%' or upper(a.nama) like '%" . strtoupper($q1) . "%')";
if ($q2 != "") {
	$sql .= " and a.is_active = '" . $q2 . "'";
}
$tot_record = $con->num_rows($sql);
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);
$sql .= " order by a.id desc limit " . $position . ", " . $length;

$content = "";
if ($tot_record == 0) {
	$content .= '<tr><td colspan="10" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
	$count 		= $position;
	$tot_page 	= ceil($tot_record / $length);
	$result 	= $con->getResult($sql);
	foreach ($result as $data) {
		$count++;
		$linkEdit	= BASE_URL_CLIENT . '/add-master-penerima-refund.php?' . paramEncrypt('idr=' . $data['id']);
		if ($data['is_active'] == 1) {
			$status = '<div style="background-color:RGBA(0, 209, 70); padding:2px; border-radius: 25px; color:white;">Aktif</div>';
		} else {
			$status = '<div style="background-color:RGBA(237, 2, 2); padding:2px; border-radius: 25px; color:white;">Tidak Aktif</div>';
		}

		if ($data['is_ceo'] == 1) {
			$status_approve_ceo = "Approved by " . $data['ceo_by'] . "<br>" . "<small>" . tgl_indo($data['ceo_date']) . " " . date("H:i", strtotime($data['ceo_date'])) . "</small>";
		} else if ($data['is_ceo'] == 2) {
			$status_approve_ceo = "Rejected by " . $data['ceo_by'] . "<br>" . "<small>" . tgl_indo($data['ceo_date']) . " " . date("H:i", strtotime($data['ceo_date'])) . "</small>";
		} else {
			$status_approve_ceo = "Verifikasi CEO";
		}

		if ($data['is_bm'] == 1) {
			$status_approve_bm = "Approved by " . $data['bm_by'] . "<br>" . "<small>" . tgl_indo($data['bm_date']) . " " . date("H:i", strtotime($data['bm_date'])) . "</small>";
		} else if ($data['is_bm'] == 2) {
			$status_approve_bm = "Rejected by " . $data['bm_by'] . "<br>" . "<small>" . tgl_indo($data['bm_date']) . " " . date("H:i", strtotime($data['bm_date'])) . "</small>";
			$status_approve_ceo = "";
		} else {
			$status_approve_bm = "Verifikasi BM";
		}

		$content .= '
		<tr>
			<td class="text-center">' . $count . '</td>
			<td class="text-center">' . $data['nama_customer'] . ' <br> <small>' . $data['marketingnya'] . '</small></td>
			<td class="text-center">' . $data['nama'] . '</td>
			<td class="text-center">' . ucwords($data['divisi']) . '</td>
			<td class="text-center">' . $status . '</td>
			<td class="text-center">
				' . $status_approve_bm . '
				<hr>
				' . $status_approve_ceo . '
			</td>
			<td class="text-center action">
				<a class="margin-sm btn btn-action btn-info" title="Edit" href="' . $linkEdit . '"><i class="fa fa-edit"></i></a>
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

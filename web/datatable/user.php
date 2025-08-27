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
$sql = "select a.*, b.role_name, c.nama_cabang, d.nama_transportir, e.nama_terminal, f.group_wilayah, g.fullname as nama_omnya
			from acl_user a 
			join acl_role b on a.id_role = b.id_role 
			left join pro_master_cabang c on a.id_wilayah = c.id_master 
			left join pro_master_group_cabang f on a.id_group = f.id_gu 
			left join pro_master_transportir d on a.id_transportir = d.id_master 
			left join pro_master_terminal e on a.id_terminal = e.id_master 
			left join acl_user g on a.id_om = g.id_user 
			where 1=1";

if ($q1 != "")
	$sql .= " and (upper(a.username) like '%" . strtoupper($q1) . "%' or upper(a.fullname) like '%" . strtoupper($q1) . "%' or upper(g.fullname) like '%" . strtoupper($q1) . "%')";
if ($q2 != "" && $q2 != 2)
	$sql .= " and a.is_active = '" . $q2 . "'";
if ($q3 != "")
	$sql .= " and a.id_wilayah = '" . $q3 . "'";
if ($q4 != "")
	$sql .= " and a.id_role = '" . $q4 . "'";

$tot_record = $con->num_rows($sql);
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);
$sql .= " order by a.is_active desc, a.username limit " . $position . ", " . $length;

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
		$linkDetail	= BASE_URL_CLIENT . '/add-acl-user.php?' . paramEncrypt('idr=' . $data['id_user']);
		$linkRole	= BASE_URL_CLIENT . '/acl-user-roles.php?' . paramEncrypt('idu=' . $data['id_user'] . '&idr=' . $data['id_role']);
		$linkPermit	= BASE_URL_CLIENT . '/acl-user-permission.php?' . paramEncrypt('idr=' . $data['id_user']);
		$linkResetManual	= BASE_URL_CLIENT . '/acl-change-password.php?' . paramEncrypt('idr=' . $data['id_user']);
		$linkFoto	= BASE_URL_CLIENT . '/acl-change-foto.php?' . paramEncrypt('idr=' . $data['id_user']);
		$linkReset	= ACTION_CLIENT . '/acl-user.php?' . paramEncrypt('act=reset&idr=' . $data['id_user']);
		$linkHapus	= paramEncrypt("user#|#" . $data['id_user']);
		$linkAcc	= ($data["is_active"] == 1) ? paramEncrypt("activeAcc#|#deactivate#|#" . $data['id_user']) : paramEncrypt("activeAcc#|#activate#|#" . $data['id_user']);
		$active		= ($data["is_active"] == 1) ? "Active" : "Not Active";
		$activeTtl	= ($data["is_active"] == 1) ? "Deactivate" : "Activate";
		$activeIcon	= ($data["is_active"] == 1) ? "fa fa-ban" : "fa fa-check";

		if ($data['id_role'] == 6) $cabang = $data['group_wilayah'];
		else if ($data['id_role'] == 12) $cabang = $data['nama_transportir'];
		else if ($data['id_role'] == 13) $cabang = $data['nama_terminal'];
		else if ($data['id_role'] == 17) $cabang = $data['nama_omnya'];
		else $cabang = $data['nama_cabang'];

		$content .= '
				<tr class="clickable-row" data-href="' . $linkDetail . '">
					<td class="text-center">' . $count . '</td>
					<td>' . $data['username'] . '</td>
					<td>
						<p style="margin:0px">' . $data['fullname'] . '</p>
						<p style="margin:0px"><i>' . $data['email_user'] . '</i></p>
					</td>
					<td class="text-center">' . ($data['last_login_time'] ? date("d-m-Y H:i:s", strtotime($data['last_login_time'])) : '-') . '</td>
					<td class="text-center">' . $data['lastupdate_ip'] . '</td>
					<td>
						<p style="margin:0px">' . $data['role_name'] . '</p>
						<p style="margin:0px"><i>' . $cabang . '</i></p>
					</td>
					<td>' . $active . '</td>
					<td class="text-center">
					<img src="' . BASE_IMAGE . '/' . $data['foto'] . '"  width="100px" height="100px"  />
					</td>
					<td class="text-center">
						<iframe style="width: 100%; height: 50%;" src="https://www.google.com/maps?q=' . $data['latitude'] . ',' . $data['longitude'] . '&hl=es;z=14&output=embed"></iframe>
					</td>
					<td class="text-center action">
						<a class="margin-sm btn btn-action btn-success" href="' . $linkFoto . '" title="Update Foto"><i class="fa fa-camera"></i></a>
						<a class="margin-sm btn btn-action btn-warning" href="' . $linkResetManual . '" title="Reset Password Manual"><i class="fa fa-key"></i></a>
						<a class="margin-sm konfirmasi btn btn-action btn-info" href="' . $linkReset . '" title="Reset Password"><i class="fa fa-unlock"></i></a>
						<a class="margin-sm btn btn-action btn-danger" title="' . $activeTtl . ' this account" data-param-idx="' . $linkAcc . '" data-action="staAcc">
						<i class="' . $activeIcon . '"></i></a>
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

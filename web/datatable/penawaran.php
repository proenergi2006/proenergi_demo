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
$q1	= isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';
$q2	= isset($_POST["q2"]) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : '';
$q3	= isset($_POST["q3"]) ? htmlspecialchars($_POST["q3"], ENT_QUOTES) : '';
$q4	= isset($_POST["q4"]) ? htmlspecialchars($_POST["q4"], ENT_QUOTES) : '';
$sess_wil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

$p = new paging;
$sql = "
select a.*, b.nama_customer, b.kode_pelanggan, c.nama_cabang, d.nama_area, 
		if(a.flag_approval = 0 && a.flag_disposisi > 0, 1, 0) as position,
		CASE 
		WHEN e.id_penawaran IS NOT NULL THEN 'YA'
		WHEN a.flag_disposisi = 0 THEN '-'
		ELSE '-'  
		END AS penawaran_status
		from pro_penawaran a 
		join pro_customer b on a.id_customer = b.id_customer 
		join pro_master_cabang c on a.id_cabang = c.id_master 
		join pro_master_area d on a.id_area = d.id_master 
		LEFT JOIN (
    SELECT id_penawaran
    FROM pro_po_customer
    GROUP BY id_penawaran
) e ON a.id_penawaran = e.id_penawaran
		where 1=1
	";
if ($sesrol == 18) {
	if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']))
		$sql .= " and (b.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "' or b.id_marketing = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "')";
	else if (!paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']))
		$sql .= " and (b.id_group = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']) . "' or b.id_marketing = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "')";
} else if ($sesrol == 17 || $sesrol == 11) {
	$sql .= " and b.id_marketing = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "'";
} else if ($sesrol == 20) {
	if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) == 2) {
		$sql .= " and b.id_marketing = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "'";
	} else {
		$sql .= " and 1=1 ";
	}
}

if ($q1 != "")
	$sql .= " and (upper(b.nama_customer) like '%" . strtoupper($q1) . "%' or b.kode_pelanggan = '" . $q1 . "' or a.nomor_surat like '" . $q1 . "%')";
if ($q2 != "")
	$sql .= " and a.id_cabang = '" . $q2 . "'";
if ($q3 != "")
	$sql .= " and a.id_area = '" . $q3 . "'";

if ($q4) {
	if ($q4 == 4)
		$sql .= " and a.flag_approval = '0' and flag_disposisi > 0";
	else if ($q4 == 3)
		$sql .= "  and a.flag_disposisi = '0'";
	else
		$sql .= " and a.flag_approval = " . $q4;
}

$tot_record = $con->num_rows($sql);
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);
$sql .= " order by a.id_penawaran desc limit " . $position . ", " . $length;

$content = "";

$count = 0;
if ($tot_record == 0) {
	$content .= '<tr><td colspan="8" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
	$count 		= $position;
	$tot_page 	= ceil($tot_record / $length);
	$result 	= $con->getResult($sql);
	foreach ($result as $data) {
		$count++;
		$linkDetail	= BASE_URL_CLIENT . '/penawaran-detail.php?' . paramEncrypt('idr=' . $data['id_customer'] . '&idk=' . $data['id_penawaran']);
		$linkHapus	= paramEncrypt("penawaran#|#" . $data['id_penawaran']);
		$linkPOC	= BASE_URL_CLIENT . '/po-customer-add.php?' . paramEncrypt('idc=' . $data['id_customer']);

		$arrPosisi	= array(1 => "SPV", "BM", "BM", "OM", "COO", "CEO");
		$arrSetuju	= array(1 => "Disetujui", "Ditolak");

		if ($data['flag_approval'] == 0 && $data['flag_disposisi'] == 0) {
			$status = "Terdaftar";
		} else if ($data['flag_approval'] == 0 && $data['flag_disposisi']) {
			if ($data['flag_disposisi'] > 1 && $data['flag_disposisi'] < 4) {
				$status = "Verifikasi " . $arrPosisi[$data['flag_disposisi']] . " " . $data['nama_cabang'];
			} else {
				$status = "Verifikasi " . $arrPosisi[$data['flag_disposisi']];
			}
		} else if ($data['flag_approval']) {
			if ($data['flag_disposisi'] > 1 && $data['flag_disposisi'] < 4) {
				$status = $arrSetuju[$data['flag_approval']] . " " . $arrPosisi[$data['flag_disposisi']] . " " . $data['nama_cabang'];
				$status .= "<br /><i>" . ($data['tgl_approval'] ? date("d/m/Y H:i:s", strtotime($data['tgl_approval'])) . " WIB" : "") . "</i>";
			} else {
				$status = $arrSetuju[$data['flag_approval']] . " " . $arrPosisi[$data['flag_disposisi']];
				$status .= "<br /><i>" . ($data['tgl_approval'] ? date("d/m/Y H:i:s", strtotime($data['tgl_approval'])) . " WIB" : "") . "</i>";
			}
		}

		$background = '';
		if ($sesrol == 11 && $data['flag_approval'] == 2 && $data['view'] == 'No') {
			$background = 'style="background-color:#f5f5f5"';
		}

		$linkExt01 = '';
		if ($data['flag_approval'] != 2 && $data['flag_approval'] != 0) {
			$linkExt01 = ' <a class="btn btn-sm btn-action btn-primary" style="margin:5px 3px 3px 0px;" title="PO Customer" href="' . $linkPOC . '">PO</a>';
		}

		$content .= '
			<tr class="clickable-row" data-href="' . $linkDetail . '" ' . $background . '>
				<td class="text-center">' . $count . '</td>
				<td>' . $data['nomor_surat'] . '</td>
				<td>
					<p style="margin-bottom:0px;"><b>' . ($data['kode_pelanggan'] ? $data['kode_pelanggan'] : '-------') . '</b></p>
					<p style="margin-bottom:0px;">' . $data['nama_customer'] . '</p>
				</td>
				<td>' . $data['nama_cabang'] . '</td>
				<td>' . $data['nama_area'] . '</td>
				<td>' . number_format($data['volume_tawar'], 0) . ' Liter</td>
				<td>' . $status . '</td>
				<td class="text-center" style="color: ' . ($data['penawaran_status'] == 'YA' ? 'green' : 'red') . ';">' . $data['penawaran_status'] . '</td>
				<td class="text-center action">
					<a class="margin-sm btn btn-action btn-info" title="Detail" href="' . $linkDetail . '"><i class="fa fa-table"></i></a>
					<a class="margin-sm btn btn-action btn-danger " title="Delete" data-param-idx="' . $linkHapus . '" data-action="deleteGrid"><i class="fa fa-trash"></i></a>
					' . $linkExt01 . '
				</td>
			</tr>';
	}
}

$json_data = array(
	"items"		=> $content,
	"pages"		=> $tot_page,
	"page"		=> $page,
	"totalData"	=> $tot_record,
	"infoData"	=> "Showing " . ($position + 1) . " - " . $count . " of " . $tot_record . " entries",
);
echo json_encode($json_data);

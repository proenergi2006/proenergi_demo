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
$q2	= isset($_POST["q2"]) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : '';
$q3	= isset($_POST["q3"]) ? htmlspecialchars($_POST["q3"], ENT_QUOTES) : '';
$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

$p = new paging;
$whereadd = '';
if ($sesrol > 1) {
	$whereadd = " and a.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
}
$sql = "select a.*, b.nama_terminal, b.tanki_terminal, b.lokasi_terminal, c.nama_cabang, d.volume, e.nomor_pr, h.nama_customer
			from pro_po_ds_kapal a 
			join pro_master_terminal b on a.terminal = b.id_master 
			join pro_master_cabang c on a.id_wilayah = c.id_master 
			join pro_pr_detail d on a.id_prd = d.id_prd
			join pro_pr e on a.id_pr = e.id_pr
			join pro_po_customer_plan f on a.id_plan = f.id_plan
			join pro_po_customer g on a.id_poc = g.id_poc
			join pro_customer h on g.id_customer = h.id_customer
 			where 1=1 " . $whereadd;

if ($q1 != "")
	$sql .= " and (a.tanggal_loading = '" . tgl_db($q1) . "' or upper(a.nomor_dn_kapal) like '" . strtoupper($q1) . "%' or upper(b.nama_terminal) like '%" . strtoupper($q1) . "%' 
				or upper(b.tanki_terminal) like '%" . strtoupper($q1) . "%' or upper(b.lokasi_terminal) like '%" . strtoupper($q1) . "%' 
				or upper(c.nama_cabang) like '%" . strtoupper($q1) . "%')";

$tot_record = $con->num_rows($sql);
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);
$sql .= " order by a.tanggal_loading desc limit " . $position . ", " . $length;

$count = 0;
$content = "";
if ($tot_record <= 0) {
	$content .= '<tr><td colspan="6" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
	$count 		= $position;
	$tot_page 	= ceil($tot_record / $length);
	$result 	= $con->getResult($sql);
	foreach ($result as $data) {
		$count++;
		$linkDetail	= BASE_URL_CLIENT . '/delivery-kapal-detail.php?' . paramEncrypt('idr=' . $data['id_dsk']);
		$linkCetak	= ACTION_CLIENT . '/delivery-kapal-cetak.php?' . paramEncrypt('idr=' . $data['id_dsk']);
		$linkCetak1	= ACTION_CLIENT . '/delivery-kapal-cetak-po.php?' . paramEncrypt('idr=' . $data['id_dsk']);
		$linkHapus	= paramEncrypt("delivery_kapal#|#" . $data['id_dsk']);
		$disCetak 	= ($data['is_cancel'] ? 'disabled ' : '');
		$disHapus 	= ($data['is_loaded'] ? 'disabled ' : '');

		$content .= '
				<tr class="clickable-row" data-href="' . $linkDetail . '">
					<td class="text-center">' . $count . '</td>
				    <td>' . $data['nomor_pr'] . '</td>
					<td>' . $data['nomor_dn_kapal'] . '</td>
					<td>' . $data['nomor_po'] . '</td>
					<td>' . $data['nama_customer'] . '</td>
					<td class="text-center">' . number_format($data['volume']) . '</td>

					<td class="text-center">' . date("d/m/Y", strtotime($data['tanggal_loading'])) . '</td>
					<td>' . $data['nama_cabang'] . '</td>
					<td>' . $data['nama_terminal'] . '</td>
					<td class="text-center action">
						
						<div class="btn-group jarak-kanan">
							<button type="button" class="' . $disCetak . ' margin-sm btn btn-action btn-success"><i class="fa fa-print"></i></button>
							<button type="button" class="margin-sm btn btn-action btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
								<span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span>
							</button>
							<ul class="dropdown-menu" role="menu">
								<li><a target="_blank" href="'  . $linkCetak .  '"">Cetak DN</a></li>
								<li><a target="_blank" href="'  . $linkCetak1 .  '"">Cetak PO</a></li>
							</ul>
						</div>
						<a class="margin-sm btn btn-action btn-info" title="Detail" href="' . $linkDetail . '"><i class="fa fa-info-circle"></i></a>
				
						<a class="' . $disHapus . 'margin-sm btn btn-action btn-danger delete" title="Delete" data-param-idx="' . $linkHapus . '" data-action="deleteGrid">
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

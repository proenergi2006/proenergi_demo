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
$length    = isset($_POST['length']) ? htmlspecialchars($_POST["length"], ENT_QUOTES) : 25;

$q1    = isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';
$q2    = isset($_POST["q2"]) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : '';

$paging = new pagination_bootstrap;
$sqlnya = "
		select a.id_master, a.id_datanya, 'Adjustment' as jenis_penambahan, 
		a.id_produk, concat(c.jenis_produk, ' - ', c.merk_dagang) as ket_produk, 
		a.id_terminal, concat(b.nama_terminal, ' ', b.tanki_terminal) as ket_terminal, 
		a.tanggal_inven, adj_inven as nilai_jenis, a.keterangan, a.lastupdate_time 
		from new_pro_inventory_depot a 
		join pro_master_terminal b on a.id_terminal = b.id_master 
		join pro_master_produk c on a.id_produk = c.id_master 
		where id_jenis = 3

       
        
	";

if ($q1 != "")
	$sqlnya .= " and b.nama_terminal LIKE '%" . $q1 . "%'";


$tot_record = $con->num_rows($sqlnya);

$config["total_rows"]     = $tot_record;
$config["per_page"]     = $length;
$config["getparams"]     = array("page" => $start);
$config["pageonly"]     = true;

$hasilnya     = $paging->initialize($config);
$position      = $paging->get_offset();
$infonya     = $paging->create_info_bootstrap();
//$linknya 	= $paging->create_links_bootstrap();

$sqlnya .= " order by a.tanggal_inven desc limit " . $position . ", " . $length;

$content = "";
if ($tot_record <= 0) {
	$content .= '<tr><td colspan="8" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
	$count         = $position;
	$result     = $con->getResult($sqlnya);
	foreach ($result as $data) {
		$count++;
		$linkDetail    = BASE_URL_CLIENT . '/adjustment-stock-detail.php?'  . paramEncrypt('idr=' . $data['id_master']);
		$linkHapus    = paramEncrypt("vendor_inven_terminal#|#" . $data['jenis_penambahan'] . "#|#" . $data['id_datanya'] . "#|#" . $data['id_produk'] . "#|#" . $data['id_terminal']);

		$content .= '
				<tr class="clickable-row12" data-href="' . $linkDetail . '">
					<td class="text-center">' . $count . '</td>
					<td class="text-left">' . $data['jenis_penambahan'] . '</td>
					<td class="text-center">' . date("d-m-Y", strtotime($data['tanggal_inven'])) . '</td>
					<td class="text-left">' . $data['ket_produk'] . '</td>
					<td class="text-left">' . $data['ket_terminal'] . '</td>
					<td class="text-right">' . number_format($data['nilai_jenis'], 0) . '</td>
					<td class="text-left">' . nl2br($data['keterangan']) . '</td>
					<td class="text-center action">
						<a class="margin-sm btn btn-action btn-info" title="Detail" href="' . $linkDetail . '"><i class="fa fa-eye"></i></a>
						<a class="margin-sm btn btn-sm btn-danger delete" title="Delete" data-param-idx="' . $linkHapus . '" data-action="deleteGrid" style="padding:3px 7px; min-width:30px;">
						<i class="fa fa-trash"></i></a>
            		</td>
				</tr>';
	}
}

$json_data = array(
	"items"        => $content,
	"totalData"    => $tot_record,
	"infoData"    => $infonya,
	"hasilnya"     => $hasilnya,
);
echo json_encode($json_data);

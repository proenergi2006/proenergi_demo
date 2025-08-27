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
$q5	= isset($_POST["q5"]) ? htmlspecialchars($_POST["q5"], ENT_QUOTES) : '';
$q6	= isset($_POST["q6"]) ? htmlspecialchars($_POST["q6"], ENT_QUOTES) : '';

$p = new paging;
$sql = "select a.*, day(a.tanggal_inven) as day_prd, b.nama_area, c.jenis_produk, c.merk_dagang, d.nama_vendor, e.nama_terminal, e.tanki_terminal, e.lokasi_terminal, 
			cast(if(f.id_inven is null,'0','1') as signed) as ordernya
			from pro_inventory_vendor_po a 
			join pro_master_area b on a.id_area = b.id_master 
			join pro_master_produk c on a.id_produk = c.id_master 
			join pro_master_vendor d on a.id_vendor = d.id_master 
			join pro_master_terminal e on a.id_terminal = e.id_master 
			left join (select distinct id_inven from pro_master_harga_tebus) f on a.id_master = f.id_inven 
			where a.harga_tebus > 0";
if ($q1 != "" && $q2 != "")
	$sql .= " and month(a.tanggal_inven) = '" . $q1 . "' and year(a.tanggal_inven) = '" . $q2 . "'";
if ($q3 != "")
	$sql .= " and a.id_produk = '" . $q3 . "'";
if ($q4 != "")
	$sql .= " and a.id_vendor = '" . $q4 . "'";
if ($q5 != "")
	$sql .= " and a.id_area = '" . $q5 . "'";
if ($q6 != "")
	$sql .= " and a.id_terminal = '" . $q6 . "'";

$tot_record = $con->num_rows($sql);
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);
// $sql .= " order by created_time desc, ordernya desc, a.tanggal_inven desc, a.id_master desc limit ".$position.", ".$length;
$sql .= " order by a.tanggal_inven desc, created_time desc, ordernya desc, a.id_master desc limit " . $position . ", " . $length;

$content = "";
if ($tot_record <= 0) {
	$content .= '<tr><td colspan="9" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
	$count 		= $position;
	$tot_page 	= ceil($tot_record / $length);
	$result 	= $con->getResult($sql);
	foreach ($result as $data) {
		$count++;
		$arrF = array();
		$cek1 = "select day(periode_awal) as periode from pro_master_harga_tebus where id_inven = '" . $data['id_master'] . "'";
		$res1 = $con->getResult($cek1);
		if (count($res1) > 0) {
			foreach ($res1 as $dat1) {
				array_push($arrF, $dat1['periode']);
			}
		}
		$checked1	= (in_array(1, $arrF)) ? 'checked' : '';
		$checked2	= (in_array(15, $arrF)) ? 'checked' : '';
		$disabled1 	= (int) $data['day_prd'] > 15 ? 'disabled' : '';
		if ($checked1 == 'checked') $disabled1 = '';
		$disabled2 	= '';

		$linkEdit	= BASE_URL_CLIENT . '/vendor-po-add.php?' . paramEncrypt('idr=' . $data['id_master']);
		if ($data['in_inven'] == $data['in_inven_po']) {
			$dis = 'disabled';
			$titlenya = 'Terima Barang Sudah Full';
		} else {
			if ($data['in_inven_po'] == '') {
				$dis = 'disabled';
				$titlenya = 'Terima Barang Sudah Full';
			} else {
				$dis = '';
				$titlenya = 'Terima Barang';
			}
		}
		$linkTerima	= '<a class="margin-sm btn btn-action btn-primary" title="' . $titlenya . '" href="' . BASE_URL_CLIENT . '/vendor-po-terima.php?' . paramEncrypt('idr=' . $data['id_master']) . '"><i class="fa fa-truck"></i></a>';

		$linkHapus	= paramEncrypt("inventory_vendor#|#" . $data['id_master']);
		$tmPrm1		= explode("-", $data['tanggal_inven']);
		$param1		= "1#|#" . $tmPrm1[1] . "#|#" . $tmPrm1[0];
		$param2		= "2#|#" . $tmPrm1[1] . "#|#" . $tmPrm1[0];
		$terminal1 	= $data['nama_terminal'];
		$terminal2 	= ($data['tanki_terminal'] ? ' - ' . $data['tanki_terminal'] : '');
		$terminal3 	= ($data['lokasi_terminal'] ? ', ' . $data['lokasi_terminal'] : '');
		$terminal 	= $terminal1 . $terminal2 . $terminal3;

		$sqlpr1 = 'select id_master from pro_master_harga_tebus where id_inven = "' . $data['id_master'] . '" and day(periode_awal) = 1 limit 1';
		$respr1 = $con->getRecord($sqlpr1);

		$sqlpr2 = 'select id_master from pro_master_harga_tebus where id_inven = "' . $data['id_master'] . '" and day(periode_awal) = 15 limit 1';
		$respr2 = $con->getRecord($sqlpr2);

		$content .= '
				<tr class="clickable-row" data-href="' . $linkEdit . '">
					<td class="text-center">' . date("d/m/Y", strtotime($data['tanggal_inven'])) . '</td>
					<td>' . $data['nomor_po'] . '</td>
					<td>
						<p style="margin-bottom:0px;"><b>' . $data['nama_area'] . '</b></p>
						<p style="margin-bottom:0px;">' . $data['jenis_produk'] . ' - ' . $data['merk_dagang'] . '</p>
					</td>
					<td>
						<p style="margin-bottom:0px;"><b>' . $data['nama_vendor'] . '</b></p>
						<p style="margin-bottom:0px;">' . $terminal . '</p>
					</td>
					<td class="text-right">
						<p style="margin-bottom:0px;">PO: ' . number_format($data['in_inven_po']) . '</p>
						<p style="margin-bottom:0px;">Diterima: ' . number_format($data['in_inven']) . '</p>
					</td>
					<td class="text-right">' . number_format($data['harga_tebus']) . '</td>
					<td class="text-center">
						<input type="checkbox" name="prd[1][' . $data['id_master'] . ']" id="prd1' . $count . '" value="' . $param1 . '" ' . $checked1 . ' ' . $disabled1 . ' />
						' . ($checked1 == 'checked' && $respr1 ? '<input type="hidden" name="inp[1][' . $data['id_master'] . ']" value="' . $respr1['id_master'] . '" />' : '') . '
					</td>
					<td class="text-center">
						<input type="checkbox" name="prd[2][' . $data['id_master'] . ']" id="prd2' . $count . '" value="' . $param2 . '" ' . $checked2 . ' ' . $disabled2 . ' />
						' . ($checked2 == 'checked' && $respr2 ? '<input type="hidden" name="inp[2][' . $data['id_master'] . ']" value="' . $respr2['id_master'] . '" />' : '') . '
					</td>
					<td class="text-center action">
						' . $linkTerima . '
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

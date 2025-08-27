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
$q3    = isset($_POST["q3"]) ? htmlspecialchars($_POST["q3"], ENT_QUOTES) : '';
$q4    = isset($_POST["q4"]) ? htmlspecialchars($_POST["q4"], ENT_QUOTES) : '';
$q5    = isset($_POST["q5"]) ? htmlspecialchars($_POST["q5"], ENT_QUOTES) : '';

$p = new paging;
$sql = "select a.*, b.nama_cabang as cabang, c.jenis_produk as produk, c.merk_dagang as merk,  d.nama_vendor as nama, e.nama_terminal as terminal, e.tanki_terminal as tanki, e.lokasi_terminal as lokasi, e.kategori_terminal

FROM 
vw_terminal_inventory_receive a
join pro_master_cabang b on a.id_cabang = b.id_master 
join pro_master_produk c on a.id_produk = c.id_master 
join pro_master_vendor d on a.id_vendor = d.id_master 
join pro_master_terminal e on a.id_terminal = e.id_master 
WHERE 
a.sisa_inven > 0 ";

if ($q2 != "")
    $sql .= " and a.id_produk = '" . $q2 . "'";
if ($q3 != "")
    $sql .= " and a.id_cabang = '" . $q3 . "'";
if ($q4 != "")
    $sql .= " and a.id_vendor = '" . $q4 . "'";
if ($q5 != "")
    $sql .= " and a.id_terminal = '" . $q5 . "'";

$tot_record = $con->num_rows($sql);
$tot_page     = ceil($tot_record / $length);
$page        = ($start > $tot_page) ? $start - 1 : $start;
$position     = $p->findPosition($length, $tot_record, $page);
$sql .= "  order by a.tgl_po_supplier, a.id_terminal, a.id_produk, a.id_cabang, a.id_vendor limit " . $position . ", " . $length;

$content = "";
if ($tot_record <= 0) {
    $content .= '<tr><td colspan="7" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
    $count         = $position;
    $tot_page     = ceil($tot_record / $length);
    $result     = $con->getResult($sql);
    foreach ($result as $data) {
        $count++;
        $linkDetail    = BASE_URL_CLIENT . '/detil-master-harga-tebus.php?' . paramEncrypt('idr=' . $data['id_master']);
        $linkEdit    = BASE_URL_CLIENT . '/add-master-harga-tebus.php?' . paramEncrypt('idr=' . $data['id_master']);
        $linkHapus    = paramEncrypt("master_harga_tebus#|#" . $data['id_master']);
        $content .= '
				<tr>
					<td class="text-center"><b>'  . number_format($data['sisa_inven']) . 'Ltr' . ' </b></td>
					<td>' . $data['nomor_po_supplier'] . '</td>
					<td>' . $data['produk'] . ' - ' . $data['merk'] . '</td>
					<td>' . $data['cabang'] . '</td>
					<td>' . $data['nama'] . '</td>
					<td>' . $data['terminal'] . ($data['tanki'] ? ' - ' . $data['tanki'] : '') . ($data['lokasi'] ? ', ' . $data['lokasi'] : '') . '</td>
                    <td class="text-right">' . number_format($data['harga_tebus']) . '</td>
					
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

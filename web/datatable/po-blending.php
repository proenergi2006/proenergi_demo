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


$paging = new pagination_bootstrap;
$sqlnya = "
	
		select a.*, b.nama_terminal,  c.nama_vendor , d.nomor_po, e.nomor_po as nomor_po_1, f.jenis_produk, g.jenis_produk as jenis_produk1, b.tanki_terminal
		from pro_blending_po a 
		join pro_master_terminal b on a.id_terminal = b.id_master 
		join pro_master_vendor c on a.id_vendor_blending = c.id_master 
        join new_pro_inventory_vendor_po d on a.id_po_blending = d.id_master
        join new_pro_inventory_vendor_po e on a.id_po_blending_1 = e.id_master
        join pro_master_produk f on d.id_produk = f.id_master
        join pro_master_produk g on e.id_produk = g.id_master
		
		

		
	";

if ($q1 != "")
    $sqlnya .= " WHERE a.nomor_blending_po LIKE '%" . $q1 . "%'";


$tot_record = $con->num_rows($sqlnya);

$config["total_rows"]     = $tot_record;
$config["per_page"]     = $length;
$config["getparams"]     = array("page" => $start);
$config["pageonly"]     = true;

$hasilnya     = $paging->initialize($config);
$position      = $paging->get_offset();
$infonya     = $paging->create_info_bootstrap();
//$linknya 	= $paging->create_links_bootstrap();

$sqlnya .= " order by a.tanggal_blending desc limit " . $position . ", " . $length;

$content = "";
if ($tot_record <= 0) {
    $content .= '<tr><td colspan="8" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
    $count         = $position;
    $result     = $con->getResult($sqlnya);
    foreach ($result as $data) {
        $count++;
        // $linkDetail    = BASE_URL_CLIENT . '/vendor-inven-terminal-new-detail.php?' . paramEncrypt('idr=' . $data['id_master']);
        $linkHapus    = paramEncrypt("vendor_inven_terminal#|#" . $data['jenis_penambahan'] . "#|#" . $data['id_datanya'] . "#|#" . $data['id_produk'] . "#|#" . $data['id_terminal']);

        $content .= '
				<tr class="clickable-row12" data-href="' . $linkDetail . '">
					<td class="text-center">' . $count . '</td>
					<td class="text-left">' . $data['nomor_blending_po'] . '</td>
					<td class="text-center">' . date("d-m-Y", strtotime($data['tanggal_blending'])) . '</td>
					<td class="text-left">' . $data['nama_vendor'] . '</td>
					<td class="text-left">' . $data['nama_terminal'] . ' - ' . $data['tanki_terminal'] . '</td>
					<td class="text-right">' . number_format($data['volume_total'], 0) . '</td>
                    <td class="text-right">' . number_format($data['harga_average'], 0) . '</td>
                    <td class="text-center"><p style="margin-bottom:0px">
                    '  . $data['nomor_po'] . ' </p>
                    <p style="margin-bottom:0px">' . $data['nomor_po_1'] . '</p>
                    </td>
                    <td class="text-right"><p style="margin-bottom:0px">
                    '  . $data['jenis_produk'] . ' </p>
                    <p style="margin-bottom:0px">
                    '  . $data['jenis_produk1'] . ' </p></td>
					<td class="text-left">' . nl2br($data['keterangan']) . '</td>
					

					';



        $content .= '
				
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

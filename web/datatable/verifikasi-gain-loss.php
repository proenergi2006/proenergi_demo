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
$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$q1    = isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';
$q2    = isset($_POST["q2"]) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : '';

$p = new paging;
$sql = "select a.*,
        b.nomor_po,
	b.harga_po,
	b.harga_tebus
        from new_pro_inventory_gain_loss  a
        join new_pro_inventory_vendor_po b on a.id_po_supplier = b.id_master 
		where 1=1";

// if ($q1 != "")
//     $sql .= " and (tanggal_inven = '" . tgl_db($q1) . "' or nomor_po like '" . strtoupper($q1) . "%')";


$tot_record = $con->num_rows($sql);
$tot_page     = ceil($tot_record / $length);
$page        = ($start > $tot_page) ? $start - 1 : $start;
$position     = $p->findPosition($length, $tot_record, $page);
$sql .= " order by id_master desc limit " . $position . ", " . $length;

$content = "";
if ($tot_record <= 0) {
    $content .= '<tr><td colspan="6" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
    $count         = $position;
    $tot_page     = ceil($tot_record / $length);
    $result     = $con->getResult($sql);
    foreach ($result as $data) {
        $count++;
        $linkDetail    = BASE_URL_CLIENT . '/verifikasi-gain-loss-detail.php?' . paramEncrypt('idr=' . $data['id_master']);

        if ($sesrol == '21') {
            $background = ($data['ceo_result'] == 0) ? ' style="background-color:#f5f5f5"' : '';
        }

        if ($data['jenis'] == 1)
            $jenis = 'Gain';
        else if ($data['jenis'] == 2)
            $jenis = 'Loss';

        if ($data['disposisi_gain_loss'] == 1)
            $status = 'Verifikasi CEO';
        else if ($data['disposisi_gain_loss'] == 3)
            $status = 'Dikembalikan<br><i>';
        else if ($data['disposisi_gain_loss'] == 2)
            $status = 'Terverifikasi<br><i>' . date("d/m/Y H:i:s", strtotime($data['ceo_tanggal'])) . ' WIB</i>';
        else $status = '';

        $content .= '
				<tr class="clickable-row" data-href="' . $linkDetail . '"' . $background . '>
					<td class="text-center">' . $count . '</td>
                   			<td class="text-center">' . $data['nomor_po'] . '</td>
					<td class="text-right"><p style="margin-bottom:0px;"> PO : Rp.' . number_format($data['harga_po']) . '</p>
							<p style="margin-bottom:0px;"> RI : Rp.' . number_format($data['harga_tebus']) . ' </p></td>
					<td class="text-center">' . number_format($data['volume']) . ' Ltr</td>
					<td class="text-center">' . $jenis . '</td>
					<td class="text-center">' . $status . '</td>
					<td class="text-center action"><a class="margin-sm btn btn-action btn-info" title="Detail" href="' . $linkDetail . '"><i class="fa fa-info-circle"></i></a></td>
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

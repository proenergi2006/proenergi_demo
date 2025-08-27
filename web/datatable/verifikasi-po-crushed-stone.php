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
$sql = "with tbl_realisasi as (
    select id_po_supplier, sum(volume_terima) as vol_terima, volume_bol as vol_bl 
    from new_pro_inventory_vendor_po_crushed_stone_receive 
    group by id_po_supplier
)
select a.*, c.jenis_produk, c.merk_dagang, d.nama_vendor, 
e.nama_terminal, e.tanki_terminal, e.lokasi_terminal, 
a1.id_po_supplier, a1.vol_terima , a1.vol_bl
from new_pro_inventory_vendor_po_crushed_stone a 
join pro_master_produk c on a.id_produk = c.id_master 
join pro_master_vendor d on a.id_vendor = d.id_master 
join pro_master_terminal e on a.id_terminal = e.id_master 
left join tbl_realisasi a1 on a.id_master = a1.id_po_supplier  
where 1=1 and a.harga_tebus > 0 ";

if ($q1 != "")
    $sql .= " and (a.tanggal_inven = '" . tgl_db($q1) . "' or a.nomor_po like '" . strtoupper($q1) . "%')";


$tot_record = $con->num_rows($sql);
$tot_page     = ceil($tot_record / $length);
$page        = ($start > $tot_page) ? $start - 1 : $start;
$position     = $p->findPosition($length, $tot_record, $page);
$sql .= " order by a.tanggal_inven desc limit " . $position . ", " . $length;

$content = "";
if ($tot_record <= 0) {
    $content .= '<tr><td colspan="7" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
    $count         = $position;
    $tot_page     = ceil($tot_record / $length);
    $result     = $con->getResult($sql);
    foreach ($result as $data) {
        $count++;

        $clickRow = "clickable-row";
        $linkDetail = BASE_URL_CLIENT . '/verifikasi-po-crushed-stone-detail.php?' . paramEncrypt('idr=' . $data['id_master']);
        $btnDetail = '<a class="margin-sm btn btn-action btn-info" title="Detail" href="' . $linkDetail . '"><i class="fa fa-info-circle"></i></a>';

        if ($sesrol == '4') {
            $background = ($data['cfo_result'] == 0) ? ' style="background-color:#f5f5f5"' : '';
        } else if ($sesrol == '21') {
            $background = ($data['ceo_result'] == 0) ? ' style="background-color:#f5f5f5"' : '';
        }

        if ($data['disposisi_po'] == 2)
            $status = 'Verifikasi CEO';
        else if ($data['disposisi_po'] == 1)
            $status = 'Verifikasi CFO';
        else if ($data['disposisi_po'] == 3)
            $status = 'Ditolak CFO<br><i>' . date("d/m/Y H:i:s", strtotime($data['cfo_tanggal'])) . ' WIB</i>';
        else if ($data['disposisi_po'] == 5)
            $status = 'Ditolak CEO<br><i>' . date("d/m/Y H:i:s", strtotime($data['ceo_tanggal'])) . ' WIB</i>';

        else if ($data['disposisi_po'] == 4)
            $status = 'Terverifikasi<br><i>' . date("d/m/Y H:i:s", strtotime($data['ceo_tanggal'])) . ' WIB</i>';
        else $status = '';

        $terminal1     = $data['nama_terminal'];
        $terminal2     = ($data['tanki_terminal'] ? ' - ' . $data['tanki_terminal'] : '');
        $terminal3     = ($data['lokasi_terminal'] ? ', ' . $data['lokasi_terminal'] : '');
        $terminal     = $terminal1 . $terminal2 . $terminal3;

        $content .= '
				<tr class="' . $clickRow . '" data-href="' . $linkDetail . '"' . $background . '>
					<td class="text-center">' . $count . '</td>
					<td>
                        ' . $data['nomor_po'] . '
                        <p style="margin-bottom: 0px; color: ' . ($data['is_cancel'] == 1 ? 'red' : 'inherit') . ';">' . ($data['is_cancel'] == 1 ? 'Cancel' : '') . '</p>
                        <p style="margin-bottom:0px;">' . $data['keterangan_cancel'] . '</p>
                    </td>
					<td class="text-center">' . tgl_indo($data['tanggal_inven']) . '</td>
					<td>
                        <p style="margin-bottom:3px;"><b>' . $data['nama_vendor'] . '</b></p>
                        <p style="margin-bottom:0px;">' . $terminal . '</p>
                    </td>
					<td>
                        <p style="margin-bottom:0px;">' . $data['jenis_produk'] . ' - ' . $data['merk_dagang'] . '</p>
                    </td>
					<td>
                        <p style="margin-bottom:3px;">PO : <b>' . number_format($data['volume_po']) . '</b></p>
                        <p style="margin-bottom:0px;">BL : ' . number_format($data['vol_bl']) . '</p>
                       <p style="margin-bottom:0px;">RI : ' . number_format($data['vol_terima']) . '</p>
                       <p style="margin-bottom: 0px; color: ' . ($data['is_close'] == 1 ? 'red' : 'inherit') . ';">' . ($data['is_close'] == 1 ? 'Close PO :' : '') . '  ' . number_format($data['volume_close']) . '</p>
                    </td>
					<td>
                      
                        <p style="margin-bottom:0px;">PO : ' . number_format($data['harga_tebus']) . '</p>
                    </td>
					<td>' . $status . '</td>
					<td class="text-center action">
                        ' . $btnDetail . '
                    </td>
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

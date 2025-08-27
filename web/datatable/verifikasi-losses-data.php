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
$sesrol1 = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

$p = new paging;
$whereadd = '';
$order  = '';


if ($sesrol == 7) {
    $whereadd .= " and a.disposisi_losses IN (1,4) and b.id_wilayah = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]) . "'";
} elseif ($sesrol == 6) {
    $order .= " ORDER BY (a.disposisi_losses = 2) DESC, a.disposisi_losses DESC";
} elseif ($sesrol == 15) {
    // Buat yang disposisi_losses = 3 jadi prioritas (0), sisanya 1
    $order .= " ORDER BY 
        CASE WHEN a.disposisi_losses = 3 THEN 0 ELSE 1 END,
        a.disposisi_losses DESC";
} else {
    $order .= " ORDER BY a.tgl_realisasi DESC, a.disposisi_losses DESC";
}

if (!empty($q1)) {
    $whereadd .= " AND (i.nama_customer LIKE '" . strtoupper($q1) . "%' OR f.nomor_pr LIKE '" . strtoupper($q1) . "%')";
}

if ($q2 != "")
    $whereadd .= " and j.id_master = '" . $q2 . "'";


$sql = "
select a.*,
f.nomor_pr,
f.tanggal_pr,
i.nama_customer,
h.nomor_poc,
e.volume,
d.nomor_po,
j.nama_cabang

from pro_po_ds_detail a 
join pro_po_ds b on a.id_ds = b.id_ds 
join pro_po_detail c on a.id_pod = c.id_pod 
join pro_po d on a.id_po = d.id_po
join pro_pr_detail e on a.id_prd = e.id_prd
join pro_pr f on a.id_pr = f.id_pr
join pro_po_customer_plan g on a.id_plan = g.id_plan 
join pro_po_customer h on g.id_poc = h.id_poc
join pro_customer i on h.id_customer = i.id_customer
join pro_master_cabang j on b.id_wilayah = j.id_master
WHERE a.losses > 0 " . $whereadd . $order;





$tot_record = $con->num_rows($sql);
$tot_page     = ceil($tot_record / $length);
$page        = ($start > $tot_page) ? $start - 1 : $start;
$position     = $p->findPosition($length, $tot_record, $page);
$sql .= " LIMIT {$position}, {$length}";

$content = "";
if ($tot_record <= 0) {
    $content .= '<tr><td colspan="7" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
    $count         = $position;
    $tot_page     = ceil($tot_record / $length);
    $result     = $con->getResult($sql);
    foreach ($result as $data) {
        $count++;
        $volume = $data['volume'];
        $volume_realisasi = $data['realisasi_volume'];

        if ($volume_realisasi > 0) {
            $losses = $volume_realisasi - $volume;
        } else {
            $losses = 0;
        }
        $clickRow = "clickable-row";
        $linkDetail = BASE_URL_CLIENT . '/verifikasi-losses-detail.php?' . paramEncrypt('idr=' . $data['id_dsd']);
        $btnDetail = '<a class="margin-sm btn btn-action btn-info" title="Detail" href="' . $linkDetail . '"><i class="fa fa-info-circle"></i></a>';

        if ($sesrol == '7') {
            $background = ($data['disposisi_losses'] == 1) ? ' style="background-color:#f5f5f5"' : '';
        } elseif ($sesrol == '6') {
            $background = ($data['disposisi_losses'] == 2) ? ' style="background-color:#f5f5f5"' : '';
        } elseif ($sesrol == '15') {
            $background = ($data['disposisi_losses'] == 3) ? ' style="background-color:#f5f5f5"' : '';
        }


        $request = '';
        if ($data['is_request'] == 2) {
            $request = '<span style="color:red;"> Reschedule</span>';
        } else if ($data['is_request'] == 3) {
            $request = '<span style="color:red;">Cancel</span>';
        } else {
            $request = '';
        }

        if ($data['flag_approval'] == 0 && $data['bm_result'] == 0 && $data['disposisi_losses'] == 1)
            $status = 'Menunggu Verifikasi BM';

        elseif ($data['flag_approval'] == 1 && $data['bm_result'] == 1 && $data['disposisi_losses'] == 4)
            $status = 'Terverifikasi  <br><i>' . date("d/m/Y H:i:s", strtotime($data['bm_tanggal'])) . ' WIB</i>';
        elseif ($data['flag_approval'] == 0 && $data['om_result'] == 0 && $data['disposisi_losses'] == 2)
            $status = 'Menunggu Verifikasi OM ';
        else if ($data['flag_approval'] == 0 && $data['om_result'] == 1  && $data['fin_result'] == 0 && $data['disposisi_losses'] == 3)
            $status = 'Menunggu Verifikasi Mgr Finance';
        else if ($data['flag_approval'] == 1 && $data['om_result'] == 1   && $data['fin_result'] == 1 && $data['disposisi_losses'] == 4)
            $status = 'Terverifikasi  <br><i>' . date("d/m/Y H:i:s", strtotime($data['fin_tanggal'])) . ' WIB</i>';
        else $status = '';


        $content .= '
				<tr class="' . $clickRow . '" data-href="' . $linkDetail . '"' . $background . '>
					<td class="text-center">' . $count . '</td>
					<td>
                        <p style="margin-bottom:3px;"><b> ' . $data['nomor_pr'] . '</b></p>
                       
                    </td>
                    <td class="text-center">' . tgl_indo($data['tgl_realisasi']) . '</td>
					<td class="text-center">' . tgl_indo($data['tanggal_pr']) . '</td>
					<td>
                        <p style="margin-bottom:3px;"><b>' . $data['nama_customer'] . '</b></p>
                      
                    </td>
					<td>
                        <p style="margin-bottom:0px;">' . $data['nomor_poc'] .  '</p>
                    </td>
				
					<td>
                        <p style="margin-bottom:0px;">' . $data['nomor_do'] . '</p>
                      
                    </td>
                    <td>
                        <p style="margin-bottom:0px;">' . $data['nama_cabang'] . '</p>
                      
                    </td>
                    <td class="text-center">
                        <p style="margin-bottom:3px;"><b>' . number_format($volume) . '</b></p>
                
                    </td>
                     <td class="text-center">
                        <p style="margin-bottom:3px;"><b>' . number_format($volume_realisasi) . '</b></p>
                
                    </td>
                      <td class="text-center">
                        <p style="margin-bottom:3px;"><b>' . number_format($losses) . '</b></p>
                
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

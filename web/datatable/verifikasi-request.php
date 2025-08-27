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
// $whereadd = '';
// if ($sesrol1 > 1) {
//     $whereadd = " and b.id_wilayah = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]) . "'";
// }
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
                join pro_po_customer h on g.id_poc =  h.id_poc
                join pro_customer i on h.id_customer = i.id_customer
                join pro_master_cabang j on b.id_wilayah = j.id_master
                where 1=1 and (a.is_request = 2 OR a.is_request = 3)";

if (($sesrol == 11 || $sesrol == 17)) {
    $sql .= " and i.id_marketing = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "'";
    // $sql .= " and 1=1 ";
} else if ($sesrol == 18) {
    if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']))
        $sql .= " and (b.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "' or i.id_marketing = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "')";
    else if (!paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']))
        $sql .= " and (i.id_group = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']) . "' or i.id_marketing = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "')";
} else if (($sesrol == 11 || $sesrol == 17) && $q1 != "") {
    $sql .= "";
}

if (!empty($q1)) {
    $sql .= " AND (i.nama_customer LIKE '" . strtoupper($q1) . "%' OR f.nomor_pr LIKE '" . strtoupper($q1) . "%')";
}

$tot_record = $con->num_rows($sql);
$tot_page     = ceil($tot_record / $length);
$page        = ($start > $tot_page) ? $start - 1 : $start;
$position     = $p->findPosition($length, $tot_record, $page);
$sql .= " order by a.tanggal_request desc limit " . $position . ", " . $length;

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
        $linkDetail = BASE_URL_CLIENT . '/verifikasi-request-detail.php?' . paramEncrypt('idr=' . $data['id_dsd']);

        if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("17", "18", "11"))) {
            $btnDetail = '<a class="margin-sm btn btn-action btn-info" title="Detail" href="' . $linkDetail . '"><i class="fa fa-info-circle"></i></a>';
        }
        $background = ($data['is_approved'] == 0) ? ' style="background-color:#f5f5f5"' : '';



        $request = '';
        if ($data['is_request'] == 2) {
            $request = '<span style="color:red;"> Reschedule</span>';
        } else if ($data['is_request'] == 3) {
            $request = '<span style="color:red;">Cancel</span>';
        } else {
            $request = '';
        }

        if ($data['disposisi_request'] == 1)
            $status = 'Menunggu Verifikasi';
        else if ($data['disposisi_request'] == 2 && $data['is_revert'] == 1)
            $status = 'Terverifikasi <br><i>' . date("d/m/Y H:i:s", strtotime($data['tanggal_approved'])) . ' WIB</i>';
        else if ($data['disposisi_request'] == 2 && $data['is_revert'] == 2)
            $status = 'Ditolak<br><i>' . date("d/m/Y H:i:s", strtotime($data['tanggal_revert'])) . ' WIB</i>';
        else $status = '';


        $content .= '
				<tr class="' . $clickRow . '" data-href="' . $linkDetail . '"' . $background . '>
					<td class="text-center">' . $count . '</td>
					<td>
                        <p style="margin-bottom:3px;"><b> ' . $data['nomor_pr'] . '</b></p>
                       
                    </td>
					<td class="text-center">' . tgl_indo($data['tanggal_pr']) . '</td>
					<td>
                        <p style="margin-bottom:3px;"><b>' . $data['nama_customer'] . '</b></p>
                      
                    </td>
					<td>
                        <p style="margin-bottom:0px;">' . $data['nomor_poc'] .  '</p>
                    </td>
					<td>
                        <p style="margin-bottom:3px;"><b>' . number_format($data['volume']) . '</b></p>
                
                    </td>
					<td>
                        <p style="margin-bottom:0px;">' . $data['nomor_do'] . '</p>
                      
                    </td>
                    <td>
                        <p style="margin-bottom:0px;">' . $data['nama_cabang'] . '</p>
                      
                    </td>
                    <td class="text-center">' . $request . '</td>
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

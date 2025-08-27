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

// $arrTgl = array(1 => "cp.tanggal_poc");

$q1    = isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';
// $q2    = isset($_POST["q2"]) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : '';
$q3    = isset($_POST["q3"]) ? htmlspecialchars($_POST["q3"], ENT_QUOTES) : '';
$q4    = isset($_POST["q4"]) ? htmlspecialchars($_POST["q4"], ENT_QUOTES) : '';
$q5   = isset($_POST["q5"]) ? htmlspecialchars($_POST["q5"], ENT_QUOTES) : '';

$seswil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);

//     $sql = "SELECT 
//     a.*, b.nama_customer, c.inisial_cabang, e.fullname as marketing
// FROM pro_po_customer a 
// JOIN pro_customer b ON a.id_customer=b.id_customer
// JOIN pro_master_cabang c ON b.id_wilayah=c.id_master
// JOIN pro_customer d on a.id_customer=d.id_customer
// JOIN acl_user e on d.id_marketing=e.id_user
// WHERE 1=1 AND c.id_master = '$seswil'";
$sql = "SELECT 
    a.*, c.nama_customer, f.inisial_cabang, h.fullname as marketing, cp.volume_kirim,cp.tanggal_kirim
FROM pro_po_customer_plan cp
JOIN pro_po_customer a ON cp.id_poc = a.id_poc
JOIN pro_penawaran b ON a.id_penawaran = b.id_penawaran
JOIN pro_customer c ON a.id_customer=c.id_customer
JOIN pro_po_ds_detail d ON cp.id_plan=d.id_plan
JOIN pro_po_detail e ON d.id_prd = e.id_prd
JOIN pro_master_cabang f ON c.id_wilayah=f.id_master
JOIN pro_customer g ON a.id_customer=g.id_customer
JOIN acl_user h ON g.id_marketing=h.id_user
JOIN pro_pr_detail i ON e.id_plan=i.id_plan
JOIN pro_po j ON d.id_po = j.id_po
WHERE 1=1";

// Eksekusi query SQL
$p = new paging;
// Lakukan proses selanjutnya dengan $sql yang telah diatur
if ($q1 != "") {
    $sql .= " AND (
            UPPER(a.nomor_poc) LIKE '%" . strtoupper($q1) . "%' 
            OR UPPER(b.nama_customer) LIKE '%" . strtoupper($q1) . "%' 
            OR UPPER(a.created_by) LIKE '%" . strtoupper($q1) . "%' 
        )";
}
// if ($q2 != "" && $q3 != "" && $q4 == "")
//     $sql .= " and " . $arrTgl[$q2] . " = '" . tgl_db($q3) . "'";
// else 
if ($q3 != "" && $q4 != "")
    $sql .= " and a.tanggal_poc between '" . tgl_db($q3) . "' and '" . tgl_db($q4) . "'";

if ($q5 != "") {
    $sql .= " and j.id_wilayah = '" . $q5 . "'";
}

if ($sesrol == 10) {
    $sql .= " and f.id_master = '$seswil'";
}


$tot_record = $con->num_rows($sql);
$tot_page     = ceil($tot_record / $length);
$page        = ($start > $tot_page) ? $start - 1 : $start;
$position     = $p->findPosition($length, $tot_record, $page);
$sql .= "  order by a.tanggal_poc desc limit " . $position . ", " . $length;
//$link = BASE_URL_CLIENT . '/report/rekap-pengiriman-exp.php?' . paramEncrypt('q1=' . $q1 . '&q2=' . $q2 . '&q3=' . $q3 . '&q4=' . $q4);


$content = "";
if ($tot_record <= 0) {
    $content .= '<tr><td colspan="7" style="text-align:center"><input type="hidden" id="uriExp1" value="' . $link . '" />Data tidak ditemukan </td></tr>';
} else {
    $count         = $position;
    $tot_page     = ceil($tot_record / $length);
    $result     = $con->getResult($sql);
    $tot_vol    = 0;
    foreach ($result as $data) {
        $count++;
        // $idp         = $data["id_dsd"];
        $tot_vol += $data['volume_poc'];
        // $terminal      =  $data['nama_terminal'] . '-' . $data['tanki_terminal'];
        // $tgl_loading = !empty($data['tanggal_loaded']) ? date("d-m-Y", strtotime($data['tanggal_loaded'])) : '';
        // $tgl_dr = !empty($data['tanggal_pr']) ? date("d-m-Y", strtotime($data['tanggal_pr'])) : '';
        // $status = "";
        // if ($data['is_loaded'] == 0 && $data['is_delivered'] == 0 && $data['is_cancel'] == 0) {
        //     $status = '<p style="margin-bottom:0px"><b>Belum Loading</b></p>';
        // } elseif ($data['is_loaded'] == 1 && $data['is_delivered'] == 0 && $data['is_cancel'] == 0) {
        //     $status = '<p style="margin-bottom:0px"><b>Loading</b></p>' .
        //         '<p style="margin-bottom:0px">' . 'Tgl Loading ' . tgl_indo($data['tanggal_loaded']) . '</p>' .
        //         '<p style="margin-bottom:0px">' . 'Jam Loading ' . ($data['jam_loaded']) . '</p>';
        // } elseif ($data['is_loaded'] == 1 && $data['is_delivered'] == 1 && $data['is_cancel'] == 0) {
        //     $status = '<p style="margin-bottom:0px">' . 'Tgl Loading ' . tgl_indo($data['tanggal_loaded']) . '</p>' .
        //         '<p style="margin-bottom:0px">' . 'Jam Loading ' . ($data['jam_loaded']) . '</p>' .
        //         '<p style="margin-bottom:0px"><b>Delivered</b></p>';
        // } elseif ($data['is_loaded'] == 1 && $data['is_delivered'] == 0 && $data['is_cancel'] == 1) {
        //     $status = '<p style="margin-bottom:0px"><b>Cancel</b></p>' .
        //         '<p style="margin-bottom:0px">' . 'Tgl Cancel ' . tgl_indo($data['tanggal_cancel']) . '</p>';
        // }

        $content .= '
				<tr>
					<td class="text-center">' . $count . '</td>
                    <td class="text-center">' . date('M', strtotime($data['tanggal_poc'])) . '</td>
                    <td class="text-center">' . $data['tanggal_poc'] . '</td>
					<td class="text-center">W' . weekOfMonth($data['tanggal_poc']) . '</td>
					<td class="text-left">' . $data['nomor_poc'] . '</td>
					<td class="text-center">' . number_format($data['volume_poc']) . '</td>
                    <td class="text-left">' . $data['nama_customer']  . '</td>
					<td class="text-left">' . $data['marketing']  . '</td>
                    <td class="text-center">' . $data['inisial_cabang']  . '</td>
				</tr>
			';
    }
    $content .= '
    <tr>
        <td class="text-center bg-gray" colspan="5"><b>TOTAL</b></td>
        <td class="text-center bg-gray"><b>' . number_format($tot_vol) . '</b></td>
         <td class="text-center bg-gray" colspan="5"><b></b></td>
    </tr>';
    $content .= '<tr class="hide"><td colspan="7"><input type="hidden" id="uriExp1" value="' . $link . '" />&nbsp;</td></tr>';
}

$json_data = array(
    "items"        => $content,
    "pages"        => $tot_page,
    "page"        => $page,
    "totalData"    => $tot_record,
    "infoData"    => "Showing " . ($position + 1) . " to " . $count . " of " . $tot_record . " entries",
);
echo json_encode($json_data);

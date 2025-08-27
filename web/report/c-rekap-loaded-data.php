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
$arrTgl = array(1 => "a.tanggal_loaded");

$q1    = isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';
$q2    = isset($_POST["q2"]) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : '';
$q3    = isset($_POST["q3"]) ? htmlspecialchars($_POST["q3"], ENT_QUOTES) : '';
$q4    = isset($_POST["q4"]) ? htmlspecialchars($_POST["q4"], ENT_QUOTES) : '';
$q5    = isset($_POST["q5"]) ? htmlspecialchars($_POST["q5"], ENT_QUOTES) : '';

// $seswil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);

$seswil = null; // Inisialisasi variabel untuk id_wilayah

if ($sesrol == 10) {
    // Jika id_role adalah 10, ambil id_wilayah
    $seswil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
    $sql = "
        SELECT 
            a.*,
            b.nomor_pr,
            c.nomor_lo_pr,
            b.tanggal_pr,
            c.volume,
            h.nama_terminal,
            h.tanki_terminal,
            i.nama_cabang,
            k.nama_customer,
            g.no_spj,
            l.nama_suplier
        FROM pro_po_ds_detail a
        JOIN pro_pr b ON a.id_pr = b.id_pr
        JOIN pro_pr_detail c ON a.id_prd = c.id_prd
        JOIN pro_po_ds d ON a.id_ds = d.id_ds
        JOIN pro_po_ds_detail e ON a.id_dsd = e.id_dsd
        JOIN pro_po f ON a.id_po = f.id_po
        JOIN pro_po_detail g ON a.id_pod = g.id_pod
        JOIN pro_master_terminal h ON c.pr_terminal = h.id_master
        JOIN pro_master_cabang i ON f.id_wilayah = i.id_master
        JOIN pro_po_customer j ON a.id_poc = j.id_poc
        JOIN pro_customer k ON j.id_customer = k.id_customer
        JOIN pro_master_transportir l ON  f.id_transportir = l.id_master
        WHERE b.tanggal_pr > '2024-01-01' AND i.id_master = '$seswil'
      
       

        "; // Tambahkan kondisi untuk id_wilayah
} elseif ($sesrol == 5) {
    // Jika id_role adalah 5, tidak ada filter untuk id_wilayah
    $sql = "
        SELECT 
            a.*,
            b.nomor_pr,
            c.nomor_lo_pr,
            b.tanggal_pr,
            c.volume,
            h.nama_terminal,
            h.tanki_terminal,
            i.nama_cabang,
            k.nama_customer
        FROM pro_po_ds_detail a
        JOIN pro_pr b ON a.id_pr = b.id_pr
        JOIN pro_pr_detail c ON a.id_prd = c.id_prd
        JOIN pro_po_ds d ON a.id_ds = d.id_ds
        JOIN pro_po_ds_detail e ON a.id_dsd = e.id_dsd
        JOIN pro_po f ON a.id_po = f.id_po
        JOIN pro_po_detail g ON a.id_pod = g.id_pod
        JOIN pro_master_terminal h ON c.pr_terminal = h.id_master
        JOIN pro_master_cabang i ON f.id_wilayah = i.id_master
        JOIN pro_po_customer j ON a.id_poc = j.id_poc
        JOIN pro_customer k ON j.id_customer = k.id_customer
        WHERE b.tanggal_pr > '2024-01-01'";
}

// Eksekusi query SQL
$p = new paging;
// Lakukan proses selanjutnya dengan $sql yang telah diatur



if ($q1 != "") {
    $sql .= " AND (
            UPPER(b.nomor_pr) LIKE '%" . strtoupper($q1) . "%' 
            OR UPPER(c.nomor_lo_pr) LIKE '%" . strtoupper($q1) . "%'
            OR UPPER(k.nama_customer) LIKE '%" . strtoupper($q1) . "%'  
            OR UPPER(h.nama_terminal) LIKE '%" . strtoupper($q1) . "%' 
            OR UPPER(h.tanki_terminal) LIKE '%" . strtoupper($q1) . "%' 
        )";
}
if ($q2 != "" && $q3 != "" && $q4 == "")
    $sql .= " and " . $arrTgl[$q2] . " = '" . tgl_db($q3) . "'";
else if ($q2 != "" && $q3 != "" && $q4 != "")
    $sql .= " and " . $arrTgl[$q2] . " between '" . tgl_db($q3) . "' and '" . tgl_db($q4) . "'";

if ($q5 != "") {
    $sql .= " and f.id_wilayah = '" . $q5 . "'";
}


// if ($q1 != "") {
//     $conditionQ1 = " AND (
//         UPPER(b.nomor_pr) LIKE '%" . strtoupper($q1) . "%' 
//         OR UPPER(c.nomor_lo_pr) LIKE '%" . strtoupper($q1) . "%' 
//         OR UPPER(h.nama_terminal) LIKE '%" . strtoupper($q1) . "%' 
//         OR UPPER(h.tanki_terminal) LIKE '%" . strtoupper($q1) . "%'
//     )";
// } else {
//     $conditionQ1 = "";
// }

// if ($q2 != "" && $q3 != "" && $q4 == "") {
//     $conditionDate = " AND " . $arrTgl[$q2] . " = '" . tgl_db($q3) . "'";
// } else if ($q2 != "" && $q3 != "" && $q4 != "") {
//     $conditionDate = " AND " . $arrTgl[$q2] . " BETWEEN '" . tgl_db($q3) . "' AND '" . tgl_db($q4) . "'";
// } else {
//     $conditionDate = "";
// }

// if ($q5 != "") {
//     $conditionQ5 = " AND f.id_wilayah = '" . $q5 . "'";
// } else {
//     $conditionQ5 = "";
// }

$tot_record = $con->num_rows($sql);
$tot_page     = ceil($tot_record / $length);
$page        = ($start > $tot_page) ? $start - 1 : $start;
$position     = $p->findPosition($length, $tot_record, $page);
$sql .= "  order by a.tanggal_loaded desc limit " . $position . ", " . $length;
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
        $idp         = $data["id_dsd"];
        $tot_vol += $data['volume'];
        $terminal      =  $data['nama_terminal'] . '-' . $data['tanki_terminal'];
        $tgl_loading = !empty($data['tanggal_loaded']) ? date("d-m-Y", strtotime($data['tanggal_loaded'])) : '';
        $tgl_dr = !empty($data['tanggal_pr']) ? date("d-m-Y", strtotime($data['tanggal_pr'])) : '';
        $status = "";
        if ($data['is_loaded'] == 0 && $data['is_delivered'] == 0 && $data['is_cancel'] == 0) {
            $status = '<p style="margin-bottom:0px"><b>Belum Loading</b></p>';
        } elseif ($data['is_loaded'] == 1 && $data['is_delivered'] == 0 && $data['is_cancel'] == 0) {
            $status = '<p style="margin-bottom:0px"><b>Loading</b></p>' .
                '<p style="margin-bottom:0px">' . 'Tgl Loading ' . tgl_indo($data['tanggal_loaded']) . '</p>' .
                '<p style="margin-bottom:0px">' . 'Jam Loading ' . ($data['jam_loaded']) . '</p>';
        } elseif ($data['is_loaded'] == 1 && $data['is_delivered'] == 1 && $data['is_cancel'] == 0) {
            $status = '<p style="margin-bottom:0px">' . 'Tgl Loading ' . tgl_indo($data['tanggal_loaded']) . '</p>' .
                '<p style="margin-bottom:0px">' . 'Jam Loading ' . ($data['jam_loaded']) . '</p>' .
                '<p style="margin-bottom:0px"><b>Delivered</b></p>';
        } elseif ($data['is_loaded'] == 1 && $data['is_delivered'] == 0 && $data['is_cancel'] == 1) {
            $status = '<p style="margin-bottom:0px"><b>Cancel</b></p>' .
                '<p style="margin-bottom:0px">' . 'Tgl Cancel ' . tgl_indo($data['tanggal_cancel']) . '</p>';
        }



        $content .= '
				<tr>
					<td class="text-center">' . $count . '</td>
                    <td class="text-left">' . $data['nama_cabang'] . '</td>
                    <td class="text-left">' . $data['nama_customer'] . '</td>
					<td class="text-left">' . $data['nomor_pr'] . '</td>
					<td class="text-left">' . $data['nomor_lo_pr'] . '</td>
                    <td class="text-left">' . $data['nama_suplier'] . '</td>
                    <td class="text-left">' . $data['no_spj'] . '</td>
					<td class="text-center">' . number_format($data['volume']) . '</td>
                    <td class="text-center">' . $tgl_dr . '</td>
					<td class="text-center">' . $tgl_loading . '</td>
                    <td class="text-left">' . $terminal . '</td>
					<td class="text-left">' . $status . '</td>
					
					

				</tr>
			';
    }
    $content .= '
    <tr>
        <td class="text-center bg-gray" colspan="7"><b>TOTAL</b></td>
        <td class="text-right bg-gray"><b>' . number_format(($tot_vol)) . '</b></td>
         <td class="text-center bg-gray" colspan="7"><b></b></td>
    </tr>';
    $content .= '<tr class="hide"><td colspan="9"><input type="hidden" id="uriExp1" value="' . $link . '" />&nbsp;</td></tr>';
}

$json_data = array(
    "items"        => $content,
    "pages"        => $tot_page,
    "page"        => $page,
    "totalData"    => $tot_record,
    "infoData"    => "Showing " . ($position + 1) . " to " . $count . " of " . $tot_record . " entries",
);
echo json_encode($json_data);

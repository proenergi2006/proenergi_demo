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
$arrTgl = array(1 => "a.tanggal_inven");

$q1    = isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';
$q2    = isset($_POST["q2"]) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : '';
$q3    = isset($_POST["q3"]) ? htmlspecialchars($_POST["q3"], ENT_QUOTES) : '';
$q4    = isset($_POST["q4"]) ? htmlspecialchars($_POST["q4"], ENT_QUOTES) : '';
$q5    = isset($_POST["q5"]) ? htmlspecialchars($_POST["q5"], ENT_QUOTES) : '';
$q6    = isset($_POST["q6"]) ? htmlspecialchars($_POST["q6"], ENT_QUOTES) : '';

// $seswil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$seswil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);



// Jika id_role adalah 5, tidak ada filter untuk id_wilayah
$sql = "SELECT 
            a.*, b.nomor_po, e.nama_cabang, d.nama_terminal, c.tujuan, c.keterangan as ket_pengisian,
            c.admin_pic, d.tanki_terminal
            
         FROM new_pro_inventory_depot a
         LEFT JOIN new_pro_inventory_vendor_po b ON a.id_po_supplier = b.id_master
         LEFT JOIN pro_pengisian_solar_mobil_opr c ON a.id_pengisian_solar = c.id
         LEFT JOIN pro_master_terminal d ON a.id_terminal = d.id_master
         LEFT JOIN pro_master_cabang e ON c.id_wilayah =e.id_master
        WHERE a.tanggal_inven > '2025-07-18' and a.id_jenis in  (11,10)";


// Eksekusi query SQL
$p = new paging;
// Lakukan proses selanjutnya dengan $sql yang telah diatur

if ($q1 != "") {
    $sql .= " AND (
            UPPER(b.nomor_po) LIKE '%" . strtoupper($q1) . "%' 
            OR UPPER(d.nama_terminal) LIKE '%" . strtoupper($q1) . "%' 
            OR UPPER(c.tujuan) LIKE '%" . strtoupper($q1) . "%' 
            OR UPPER(c.keterangan) LIKE '%" . strtoupper($q1) . "%' 
        )";
}
if ($q2 != "" && $q3 != "" && $q4 == "")
    $sql .= " and " . $arrTgl[$q2] . " = '" . tgl_db($q3) . "'";
else if ($q2 != "" && $q3 != "" && $q4 != "")
    $sql .= " and " . $arrTgl[$q2] . " between '" . tgl_db($q3) . "' and '" . tgl_db($q4) . "'";

if ($q5 != "") {
    $sql .= " and c.id_wilayah = '" . $q5 . "'";
}
if ($q6 != "") {
    $sql .= " and a.id_terminal = '" . $q6 . "'";
}


$tot_record = $con->num_rows($sql);
$tot_page     = ceil($tot_record / $length);
$page        = ($start > $tot_page) ? $start - 1 : $start;
$position     = $p->findPosition($length, $tot_record, $page);
$sql .= "  order by a.tanggal_inven desc limit " . $position . ", " . $length;
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
        //$idp         = $data["id_dsd"];
        $volume = $data['out_inven'] == 0 ? $data['out_inven_virtual'] : $data['out_inven'];
        $tot_vol += $volume;
        $terminal      =  $data['nama_terminal'] . '-' . $data['tanki_terminal'];
        $tgl_potong = !empty($data['tanggal_inven']) ? date("d-m-Y", strtotime($data['tanggal_inven'])) : '';


        $id = $data['id_master'];
        $tgl = $data['tanggal_inven'];
        $btnaction = '';

        $content .= '
				<tr>
					<td class="text-center">' . $count . '</td>
                    <td class="text-left">' . $data['nama_cabang'] . '</td>
                    <td class="text-left">' . $terminal . '</td>
					<td class="text-left">' . $data['nomor_po'] . '</td>
					<td class="text-left">' . $data['tujuan'] . '</td>
					<td class="text-left">' . $data['ket_pengisian'] . '</td>
					<td class="text-center">' . number_format($volume, 4) . '</td>
                    <td class="text-center">' . $tgl_potong . '</td>
                    <td class="text-center">' . $data['admin_pic'] . '</td>
					<td class="text-center">' . $data['created_by'] . '</td>
				</tr>
			';
    }
    $content .= '
    <tr>
        <td class="text-center bg-gray" colspan="6"><b>TOTAL</b></td>
        <td class="text-right bg-gray"><b>' . number_format(($tot_vol), 4) . '</b></td>
         <td class="text-center bg-gray" colspan="6"><b></b></td>
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

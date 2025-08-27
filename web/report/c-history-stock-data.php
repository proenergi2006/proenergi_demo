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
$sql = "
        SELECT 
            a.*,
            b.nomor_pr,
            c.nomor_lo_pr,
            c.nomor_po_supplier,
            b.tanggal_pr,
            c.volume,
			e.nama_terminal,
            e.tanki_terminal,
		    f.nama_cabang,
	        i.nama_customer
            
         FROM new_pro_inventory_depot a
         LEFT JOIN pro_pr b ON a.id_pr = b.id_pr
         LEFT JOIN pro_pr_detail c ON a.id_prd = c.id_prd
		 LEFT JOIN pro_master_terminal e ON a.id_terminal = e.id_master
		 LEFT JOIN pro_master_cabang f ON b.id_wilayah = f.id_master
		 LEFT JOIN pro_po_customer_plan g ON c.id_plan = g.id_plan
         LEFT JOIN pro_po_customer h ON g.id_poc= h.id_poc
         LEFT JOIN pro_customer i ON h.id_customer = i.id_customer
        WHERE a.tanggal_inven > '2024-09-01' and a.id_jenis in (6,7)";


// Eksekusi query SQL
$p = new paging;
// Lakukan proses selanjutnya dengan $sql yang telah diatur



if ($q1 != "") {
    $sql .= " AND (
            UPPER(b.nomor_pr) LIKE '%" . strtoupper($q1) . "%' 
            OR UPPER(c.nomor_lo_pr) LIKE '%" . strtoupper($q1) . "%' 
            OR UPPER(e.nama_terminal) LIKE '%" . strtoupper($q1) . "%' 
            OR UPPER(e.tanki_terminal) LIKE '%" . strtoupper($q1) . "%' 
            OR UPPER(i.nama_customer) LIKE '%" . strtoupper($q1) . "%' 
            OR UPPER(c.nomor_po_supplier) LIKE '%" . strtoupper($q1) . "%' 
        )";
}
if ($q2 != "" && $q3 != "" && $q4 == "")
    $sql .= " and " . $arrTgl[$q2] . " = '" . tgl_db($q3) . "'";
else if ($q2 != "" && $q3 != "" && $q4 != "")
    $sql .= " and " . $arrTgl[$q2] . " between '" . tgl_db($q3) . "' and '" . tgl_db($q4) . "'";

if ($q5 != "") {
    $sql .= " and b.id_wilayah = '" . $q5 . "'";
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
        $tot_vol += $data['volume'];
        $terminal      =  $data['nama_terminal'] . '-' . $data['tanki_terminal'];
        $tgl_potong = !empty($data['tanggal_inven']) ? date("d-m-Y", strtotime($data['tanggal_inven'])) : '';
        $tgl_dr = !empty($data['tanggal_pr']) ? date("d-m-Y", strtotime($data['tanggal_pr'])) : '';
        $jenis = "";
        if ($data['id_jenis'] == 6) {
            $jenis = '<p style="margin-bottom:0px"><b>Out Stock Virtual</b></p>';
        } elseif ($data['id_jenis'] == 7) {
            $jenis = '<p style="margin-bottom:0px"><b>Loaded</b></p>';
        }

        $id = $data['id_master'];
        $tgl = $data['tanggal_inven'];
        $btnaction = '';
        if ($data['id_jenis'] == 7) {
            $btnaction = '<a class="editStsAction margin-sm btn btn-action btn-info" title="Edit Tanggal Potong" data-jenis="edit" data-tgl="' . $tgl . '" data-id="' . $id . '">
            <i class="fa fa-edit"></i></a>';
        } else {
            $btnaction = '';
        }


        $content .= '
				<tr>
					<td class="text-center">' . $count . '</td>
                    <td class="text-left">' . $data['nama_cabang'] . '</td>
                    <td class="text-left">' . $data['nama_customer'] . '</td>
					<td class="text-left">' . $data['nomor_pr'] . '</td>
                    <td class="text-left">' . $data['nomor_po_supplier'] . '</td>
					<td class="text-left">' . $data['nomor_lo_pr'] . '</td>
					<td class="text-center">' . number_format($data['volume']) . '</td>
                    <td class="text-center">' . $tgl_dr . '</td>
					<td class="text-center">' . $tgl_potong . '</td>
                    <td class="text-center">' . $jenis . '</td>
                    <td class="text-left">' . $terminal . '</td>
					<td class="text-center"> ' . $btnaction . '</td>
					
					

				</tr>
			';
    }
    $content .= '
    <tr>
        <td class="text-center bg-gray" colspan="6"><b>TOTAL</b></td>
        <td class="text-right bg-gray"><b>' . number_format(($tot_vol)) . '</b></td>
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

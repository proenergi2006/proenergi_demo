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
$arrSts = array(1 => "Prospek", "Evaluasi", "Tetap");
$period = "";


$q1    = isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';
$q2    = isset($_POST["q2"]) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : '';
$q3    = isset($_POST["q3"]) ? htmlspecialchars($_POST["q3"], ENT_QUOTES) : '';
$q4    = isset($_POST["q4"]) ? htmlspecialchars($_POST["q4"], ENT_QUOTES) : '';
$q5    = isset($_POST["q5"]) ? htmlspecialchars($_POST["q5"], ENT_QUOTES) : '';


$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$where1 = '';
$whereadd = '';
if ($sesrol > 1) {
    $whereadd = " and i.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
}
if ($q1) {
    $where1 .= " and a.tanggal_loading between '" . tgl_db($q1) . " 00:00:00' and '" . tgl_db($q1) . " 23:59:59'";
}

if ($q2 && !$q3) {
    $where1 .= " and a.tanggal_loading between '" . tgl_db($q2) . " 00:00:00' and '" . tgl_db($q2) . " 23:59:59'";
} else if ($q2 && $q3) {
    $where1 .= " and a.tanggal_loading between '" . tgl_db($q2) . " 00:00:00' and '" . tgl_db($q3) . " 23:59:59'";
}

if ($q4) {
    $where1 .= "  and b.pr_terminal = '" . $q4 . "'";
}


if ($q5) {
    $q5 = strtoupper($q5); // Ubah menjadi uppercase di awal untuk efisiensi
    $where1 .= " and (UPPER(j.nama_terminal) LIKE '" . $q5 . "%' 
                    OR UPPER(j.tanki_terminal) = '" . $q5 . "' 
                    OR UPPER(i.nomor_pr) = '" . $q5 . "' 
                    OR UPPER(b.nomor_lo_pr) LIKE '%" . $q5 . "%' 
                    OR UPPER(a.captain) LIKE '%" . $q5 . "%')";
}


$p = new paging;
$sql = "    select a.*,
            i.nomor_pr, b.nomor_lo_pr, j.nama_terminal, j.tanki_terminal, k.nama_suplier, b.volume
            from pro_po_ds_kapal a 
            join pro_pr_detail b on a.id_prd = b.id_prd 
            join pro_po_customer_plan c on a.id_plan = c.id_plan 
            join pro_po_customer d on c.id_poc = d.id_poc 
            join pro_pr i on a.id_pr = i.id_pr 
            join pro_master_terminal j on b.pr_terminal = j.id_master 
            join pro_master_transportir k on a.transportir = k.id_master
            where a.is_cancel != 1" . $whereadd . $where1;

if (is_numeric($length)) {
    $tot_record = $con->num_rows($sql);
    $tot_page     = ceil($tot_record / $length);
    $page        = ($start > $tot_page) ? $start - 1 : $start;
    $position     = $p->findPosition($length, $tot_record, $page);
    $sql .= " order by a.id_dsk desc limit " . $position . ", " . $length;
} else {
    $tot_record = $con->num_rows($sql);
    $page        = 1;
    $position     = 0;
    $sql .= " order by a.id_dsk desc";
}
$link = BASE_URL_CLIENT . '/report/schedule-by-date-kapal-cetak.php?' . paramEncrypt('q1=' . $q1 . '&q2=' . $q2 . '&q3=' . $q3 . '&q4=' . $q4  . '&q5=' . $q5);


$content = "";
if ($tot_record == 0) {
    $content .= '<tr><td colspan="8" style="text-align:center"><input type="hidden" id="uriExp" value="' . $link . '" />Data tidak ditemukan </td></tr>';
} else {
    $count         = $position;
    $tot_page     = (is_numeric($length)) ? ceil($tot_record / $length) : 1;
    $result     = $con->getResult($sql);
    $tot_vol    = 0;
    $no = 0;
    foreach ($result as $data) {
        $tot_vol += $data['volume'];
        $count++;
        $no++;




        $volume   = number_format($data['volume']);

        $content .= '
				<tr>
                    <td class="text-center">' . $count . '</td>
                     <td class="text-left">' . date("d/m/Y", strtotime($data['tanggal_loading'])) . '</td>
                    <td class="text-left">' . $data['nomor_pr'] . '</td>
                    <td class="text-left">
                    ' . $data['nomor_lo_pr'] . '
                   
                    </td>
                    <td class="text-left">' . $data['nama_terminal'] . ' - ' . $data['tanki_terminal'] . '</td>
					<td class="text-left">' . $data['nama_suplier'] . '</td>
                    <td class="text-left">' . $data['vessel_name'] . '</td>
                    <td class="text-left">' . $data['kapten_name'] . '</td>
                    <td class="text-right">' . $volume . '</td>
                    
                
				</tr>';
    }
    $content .= '
			<tr>
				<td class="text-center bg-gray" colspan="8"><b>TOTAL</b></td>
				<td class="text-right bg-gray"><b>' . number_format(($tot_vol)) . '</b></td>
			</tr>';
    $content .= '<tr class="hide"><td colspan="4"><input type="hide" id="uriExp" value="' . $link . '" /></td></tr>';
}

$json_data = array(
    "items"        => $content,
    "pages"        => $tot_page,
    "page"        => $page,
    "totalData"    => $tot_record,
    "infoData"    => "Showing " . ($position + 1) . " - " . $count . " of " . $tot_record . " entries",
);
echo json_encode($json_data);

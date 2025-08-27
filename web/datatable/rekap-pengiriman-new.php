<?php

use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Number;

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
$arrTgl = array(1 => "e.tanggal_kirim");

$q1    = isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';
$q2    = isset($_POST["q2"]) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : '';
$q3    = isset($_POST["q3"]) ? htmlspecialchars($_POST["q3"], ENT_QUOTES) : '';
$q4    = isset($_POST["q4"]) ? htmlspecialchars($_POST["q4"], ENT_QUOTES) : '';
$q5    = isset($_POST["q5"]) ? htmlspecialchars($_POST["q5"], ENT_QUOTES) : '';

$seswil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);

$p = new paging;
$sql = "

	   SELECT a.*,
       e.tanggal_kirim,
       c.no_spj,
       j.nomor_plat,
       g.nama_sopir,
       i.nama_transportir,
       h.nama_customer,
       c.volume_po,
    
       k.yang_dibayarkan,
       k.jarak_real,
       l.nama_terminal,
       l.tanki_terminal,
       m.nama_cabang,
       o.nama_kab,
       p.pr_mobil
       
      
        FROM pro_po_ds_detail a
        JOIN pro_po_ds b ON a.id_ds = b.id_ds
        JOIN pro_po_detail c ON a.id_pod = c.id_pod
        JOIN pro_po d ON a.id_po = d.id_po
        JOIN pro_po_customer_plan e ON a.id_plan = e.id_plan
        JOIN pro_po_customer f ON a.id_poc = f.id_poc
        JOIN pro_master_transportir_sopir g ON c.sopir_po = g.id_master 
        JOIN pro_customer h ON f.id_customer = h.id_customer 
        JOIN pro_master_transportir i ON d.id_transportir = i.id_master 
        JOIN pro_master_transportir_mobil j ON c.mobil_po = j.id_master 
        left JOIN pro_bpuj k ON a.id_dsd = k.id_dsd
        JOIN pro_master_terminal l ON b.id_terminal = l.id_master 
        JOIN pro_master_cabang m ON b.id_wilayah = m.id_master
        JOIN pro_customer_lcr n on e.id_lcr = n.id_lcr
        JOIN pro_master_kabupaten o on n.kab_survey = o.id_kab
        JOIN pro_pr_detail p on a.id_prd = p.id_prd
		where 1=1 and c.tgl_kirim_po > '2024-01-01' and a.is_cancel = 0
			
	";


if ($sesrol == 9) {
    $sql .= " and d.id_wilayah = '" . $seswil . "'";
}

if ($q5 != "") {
    $sql .= " and d.id_wilayah = '" . $q5 . "'";
} else {
    $sql .= " and d.id_wilayah = '6'";
}

// Tambahkan kondisi pencarian jika ada
if ($q1 != "") {
    $sql .= " AND (UPPER(h.nama_customer) LIKE '" . strtoupper($q1) . "%' 
                 OR UPPER(c.no_spj) = '" . strtoupper($q1) . "' 
                 OR UPPER(j.nomor_plat) = '" . strtoupper($q1) . "' 
                 OR UPPER(g.nama_sopir) LIKE '%" . strtoupper($q1) . "%' 
                 OR UPPER(i.nama_transportir) LIKE '%" . strtoupper($q1) . "%')";
}
if ($q2 != "" && $q3 != "" && $q4 == "")
    $sql .= " and " . $arrTgl[$q2] . " = '" . tgl_db($q3) . "'";
else if ($q2 != "" && $q3 != "" && $q4 != "")
    $sql .= " and " . $arrTgl[$q2] . " between '" . tgl_db($q3) . "' and '" . tgl_db($q4) . "'";

$tot_record = $con->num_rows($sql);
$tot_page     = ceil($tot_record / $length);
$page        = ($start > $tot_page) ? $start - 1 : $start;
$position     = $p->findPosition($length, $tot_record, $page);
$sql .= "  order by a.tanggal_loading desc, a.jam_loading, a.nomor_urut_ds, a.id_dsd limit " . $position . ", " . $length;
$link = BASE_URL_CLIENT . '/report/rekap-pengiriman-exp.php?' . paramEncrypt('q1=' . $q1 . '&q2=' . $q2 . '&q3=' . $q3 . '&q4=' . $q4);

$content = "";
if ($tot_record <= 0) {
    $content .= '<tr><td colspan="7" style="text-align:center"><input type="hidden" id="uriExp1" value="' . $link . '" />Data tidak ditemukan </td></tr>';
} else {
    $count         = $position;
    $tot_page     = ceil($tot_record / $length);
    $result     = $con->getResult($sql);
    foreach ($result as $data) {
        $count++;
        $idp         = $data["id_dsd"];
        $losses      =  $data['realisasi_volume'] - $data['volume_po'];
        $tgl_loading = (!empty($data['tanggal_loaded']) && !empty($data['jam_loaded']))
            ? date("d-m-Y", strtotime($data['tanggal_loaded'])) . ' ' . $data['jam_loaded']
            : '';


        $tgl_delivered = !empty($data['tanggal_delivered']) ? date("d-m-Y H:i", strtotime($data['tanggal_delivered'])) : '';
        $tgl1 = strtotime($data['tanggal_loaded'] . " " . $data['jam_loaded']);
        if (empty($data['tanggal_delivered'])) {
            $leadtm = null; // Atau bisa diganti dengan nilai lain seperti '' atau 0 tergantung kebutuhan
        } else {
            $tgl2 = strtotime($data['tanggal_delivered']);
            $leadtm = ($tgl2 - $tgl1);
        }

        $arrMobil = array(1 => "Truck", "Kapal", "Loco");






        $content .= '
				<tr>
					<td class="text-center">' . $count . '</td>
					<td class="text-left">' . date('d-m-Y', strtotime($data['tanggal_kirim'])) . '</td>
                    <td class="text-left">' . $data['nama_cabang'] . '</td>
                   	<td class="text-left">' . $data['nama_terminal'] . ' ' . $data['tanki_terminal'] . '</td>
					<td class="text-left">' . $data['no_spj'] . '</td>
					<td class="text-left">' . $data['nomor_plat'] . '</td>
					<td class="text-left">' . $data['nama_sopir'] . '</td>
					<td class="text-left">' . $data['nama_transportir'] . '</td>
                    <td class="text-left">' . $arrMobil[$data['pr_mobil']] . '</td>
                    <td class="text-left">' . $data['nama_customer'] . '</td>
					  <td class="text-left">' . $data['nama_kab'] . '</td>
						<td class="text-left">' . number_format($data['volume_po']) . '</td>
					<td class="text-left">' .  $tgl_loading .  '</td>
					<td class="text-left">' .  $tgl_delivered . '</td>
                    <td class="text-left">' .  timeManHours($leadtm) . '</td>
					<td class="text-center">' . number_format($data['realisasi_volume']) . '</td>
                    <td class="text-center">' .  number_format($losses) . '</td>
					
				

				</tr>
			';
    }
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

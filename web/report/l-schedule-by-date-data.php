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

$whereadd = '';
if ($sesrol > 1) {
    $whereadd = " and c.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
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
    $where1 .= "  and b.terminal_po = '" . $q4 . "'";
}


if ($q5) {
    $where1 .= " and (upper(r.nama_terminal) like '" . strtoupper($q5) . "%' or upper(r.tanki_terminal) = '" . strtoupper($q5) . "' or upper(c.nomor_po) = '" . strtoupper($q5) . "' 
                    or upper(n.nama_sopir) like '%" . strtoupper($q5) . "%' or upper(m.nomor_plat) like '%" . strtoupper($q5) . "%')";
}



$p = new paging;
$sql = "    select a.*, h.kode_pelanggan, h.id_customer, h.nama_customer, g.id_lcr, g.alamat_survey, i.nama_prov, j.nama_kab, q.fullname, l.nama_area, 
            o.nama_transportir, o.nama_suplier, b.no_spj, b.mobil_po, m.nomor_plat, n.nama_sopir, b.volume_po, p.wilayah_angkut, d.produk, d.is_split, d.vol_potongan,
            c.nomor_po, b.multidrop_po, b.trip_po, b.nomor_oslog, d.no_do_acurate, d.nomor_lo_pr, f.nomor_poc, e.tanggal_kirim, e.volume_kirim, c.id_wilayah, r.nama_terminal, r.tanki_terminal, s.nomor_pr,
            t.volume as volume_potong,pt.nama_terminal AS terminal_potong,pt.tanki_terminal AS tanki_potong
            from pro_po_ds_detail a 
            join pro_po_detail b on a.id_pod = b.id_pod 
            join pro_po c on a.id_po = c.id_po 
            join pro_pr_detail d on a.id_prd = d.id_prd 
            join pro_po_customer_plan e on a.id_plan = e.id_plan 
            join pro_po_customer f on e.id_poc = f.id_poc 
            join pro_customer_lcr g on e.id_lcr = g.id_lcr
            join pro_customer h on f.id_customer = h.id_customer 
            join pro_master_provinsi i on g.prov_survey = i.id_prov 
            join pro_master_kabupaten j on g.kab_survey = j.id_kab
            join pro_penawaran k on f.id_penawaran = k.id_penawaran  
            join pro_master_area l on k.id_area = l.id_master 
            join pro_master_transportir_mobil m on b.mobil_po = m.id_master 
            join pro_master_transportir_sopir n on b.sopir_po = n.id_master 
            join pro_master_transportir o on c.id_transportir = o.id_master 
            join pro_master_wilayah_angkut p on g.id_wil_oa = p.id_master and g.prov_survey = p.id_prov and g.kab_survey = p.id_kab 
            join acl_user q on h.id_marketing = q.id_user 
            join pro_master_terminal r on b.terminal_po = r.id_master 
            join pro_pr s on a.id_pr = s.id_pr 
            LEFT JOIN new_pro_inventory_potongan_stock t ON d.id_prd = t.id_prd
            LEFT JOIN pro_master_terminal pt ON pt.id_master = t.pr_terminal

            where a.is_cancel != 1" . $whereadd . $where1;

if (is_numeric($length)) {
    $tot_record = $con->num_rows($sql);
    $tot_page     = ceil($tot_record / $length);
    $page        = ($start > $tot_page) ? $start - 1 : $start;
    $position     = $p->findPosition($length, $tot_record, $page);
    $sql .= " order by a.id_dsd desc limit " . $position . ", " . $length;
} else {
    $tot_record = $con->num_rows($sql);
    $page        = 1;
    $position     = 0;
    $sql .= " order by a.id_dsd desc";
}
$link = BASE_URL_CLIENT . '/report/schedule-by-date-cetak.php?' . paramEncrypt('q1=' . $q1 . '&q2=' . $q2 . '&q3=' . $q3 . '&q4=' . $q4  . '&q5=' . $q5);


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
        $tot_vol += $data['volume_po'];
        $count++;
        $no++;

        if ($data['nomor_do']) {
            $querycek = "SELECT * FROM pro_bpuj WHERE is_active='1' AND id_dsd='" . $data['id_dsd'] . "'";
            $row_cek = $con->getRecord($querycek);

            if ($row_cek) {
                $link_bpuj = BASE_URL_CLIENT . '/_get_form_bpuj.php?' . paramEncrypt('id_cust=' . $data['id_customer'] . '&id_dsd=' . $data['id_dsd']);
                $btnBpuj = '<p style="margin-bottom:0px; color:green;">BPUJ sudah dibuat</p>
                            <p style="margin-bottom:0px">
                                <a target="_blank" style="cursor:pointer" href="' . $link_bpuj . '" data-idnya="' . $data["id_dsd"] . '">Lihat BPUJ</a>
                            </p>';
            } else {
                $link_bpuj = BASE_URL_CLIENT . '/_get_form_bpuj.php?' . paramEncrypt('id_cust=' . $data['id_customer'] . '&id_dsd=' . $data['id_dsd']);
                $btnBpuj = '<p style="margin-bottom:0px; color:orange;">BPUJ belum dibuat</p>
                            <p style="margin-bottom:0px">
                                <a target="_blank" style="cursor:pointer" href="' . $link_bpuj . '" data-idnya="' . $data["id_dsd"] . '">Buat BPUJ</a>
                            </p>';
            }
        } else {
            $btnBpuj = "";
        }

        $is_split = $data['is_split'];
        $volume = '';
        $terminal = '';
        if ($is_split == 0) {
            $terminal     = $data['nama_terminal'] . '-' . $data['tanki_terminal'];
            $volume       = number_format($data['volume_po']);
        } else {
            $volume = number_format($data['vol_potongan']) . ' <p> ' . number_format($data['volume_potong']);
            $terminal     = $data['nama_terminal'] . '-' . $data['tanki_terminal'] . ' <p>' . $data['terminal_potong']  . ' - ' . $data['tanki_potong'];
        }

        $content .= '
				<tr>
                    <td class="text-center">' . $count . '</td>
					<td class="text-center">' . $data['trip_po'] . '</td>
                     <td class="text-left">' . date("d/m/Y", strtotime($data['tanggal_loading'])) . '</td>
                    <td class="text-left">' . $data['nomor_pr'] . '</td>
                    <td class="text-left">
                    ' . $data['nomor_lo_pr'] . '
                    ' . $btnBpuj . '
                    </td>
                    <td class="text-left">' . $terminal . '</td>
					<td class="text-left">' . $data['nama_suplier'] . '</td>
                    <td class="text-left">' . $data['nomor_plat'] . '</td>
                    <td class="text-left">' . $data['nama_sopir'] . '</td>
                    <td class="text-right">' . $volume . '</td>
                    <td class="text-left">' . $data['remark_depo'] . '</td>
                
				</tr>';
    }
    $content .= '
			<tr>
				<td class="text-center bg-gray" colspan="9"><b>TOTAL</b></td>
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

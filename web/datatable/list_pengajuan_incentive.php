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
$q1    = isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';
$q2    = isset($_POST["q2"]) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : '';
$q3    = isset($_POST["q3"]) ? htmlspecialchars($_POST["q3"], ENT_QUOTES) : '';
$q4    = isset($_POST["q4"]) ? htmlspecialchars($_POST["q4"], ENT_QUOTES) : '';
$id_role = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$linkExport = BASE_URL_CLIENT . '/report/incentive-cabang-exp.php?' . paramEncrypt('periode=' . $q3 . '&filter_cabang=' . $q4);
$linkExport2 = BASE_URL_CLIENT . '/report/rekap-biaya-incentive-exp.php?' . paramEncrypt('periode=' . $q3 . '&filter_cabang=' . $q4);
$arrTermPayment = array("CREDIT" => "CREDIT", "CBD" => "CBD (Cash Before Delivery)", "COD" => "COD (Cash On Delivery)");

$p = new paging;
$sql = "SELECT *, id as id_pengajuannya FROM pro_pengajuan_incentive WHERE 1=1";

if ($q1) {
    $sql .= " and (upper(nomor_pengajuan) like '%" . strtoupper($q1) . "%')";
}

if ($q2) {
    $sql .= " and is_ceo = '" . $q2 . "'";
}

if ($q3) {
    $explode = explode("-", $q3);
    $year = $explode[0];
    $month = $explode[1];

    if ($id_role == "21") {
        // Untuk role 21, hanya tambahkan filter periode jika is_ceo = 1
        $sql .= " AND (
            is_ceo = 0 
            OR (is_ceo = 1 AND periode_bulan = '" . $month . "' AND periode_tahun = '" . $year . "')
        )";
    } else {
        // Untuk role lain, tambahkan filter periode langsung
        $sql .= " AND periode_bulan = '" . $month . "' AND periode_tahun = '" . $year . "'";
    }
}

if ($q4) {
    $sql .= " and wilayah = '" . $q4 . "'";
}

if ($id_role == "21") {
    $sql .= " AND disposisi > 0";
    $order_by = " is_ceo ASC";
} else {
    $order_by = " id DESC";
}



// if ($q4 == 7 || $q4 == 10) {
// 	$sql .= " and i.id_wilayah = '" . $id_wilayah . "'";
// }

// if ($q1 != "")
// 	$sql .= " and (upper(h.nomor_poc) like '" . strtoupper($q1) . "%' or upper(i.nama_customer) like '%" . strtoupper($q1) . "%' or upper(n.no_invoice) like '%" . strtoupper($q1) . "%')";
// if ($q3 != "") {
// 	if ($q3 == "0") {
// 		$sql .= " and a.disposisi IN (0,1)";
// 	} elseif ($q3 == "1") {
// 		$sql .= " and a.disposisi = 2";
// 	} else {
// 		$sql .= " and a.disposisi = 3";
// 	}
// }

$tot_record = $con->num_rows($sql);
$tot_page     = ceil($tot_record / $length);
$page        = ($start > $tot_page) ? $start - 1 : $start;
$position     = $p->findPosition($length, $tot_record, $page);
$sql .= " order by " . $order_by . "  limit " . $position . ", " . $length;

$content = "";
$count = 0;
if ($tot_record <= 0) {
    $content .= '<tr><td colspan="12" style="text-align:center"><input type="hidden" id="uriExp" value="' . $linkExport . '" /><input type="hidden" id="uriExp2" value="' . $linkExport2 . '" />Data tidak ditemukan </td></tr>';
} else {
    $count         = $position;
    $tot_page     = ceil($tot_record / $length);
    $result     = $con->getResult($sql);

    foreach ($result as $data) {
        $count++;

        $linkDetail    = BASE_URL_CLIENT . '/detail_incentive.php?' . paramEncrypt('id_pengajuan=' . $data['id_pengajuannya']);

        switch ($data['periode_bulan']) {
            case '01':
                $nama_bulan = "Januari";
                break;
            case '02':
                $nama_bulan = "Februari";
                break;
            case '03':
                $nama_bulan = "Maret";
                break;
            case '04':
                $nama_bulan = "April";
                break;
            case '05':
                $nama_bulan = "Mei";
                break;
            case '06':
                $nama_bulan = "Juni";
                break;
            case '07':
                $nama_bulan = "Juli";
                break;
            case '08':
                $nama_bulan = "Agustus";
                break;
            case '09':
                $nama_bulan = "September";
                break;
            case '10':
                $nama_bulan = "Oktober";
                break;
            case '11':
                $nama_bulan = "November";
                break;
            case '12':
                $nama_bulan = "Desember";
                break;
        }

        if ($data['disposisi'] == 0) {
            $status = "Draft";
            $link    = paramEncrypt($data['id_pengajuannya']);
            $btnHapus = '<button class="btn btn-danger btn-sm btnHapus" title="Delete" data-param="' . $link . '"><i class="fas fa-trash"></i></button>';

            $btnKirim = '<button class="btn btn-warning btn-sm btnKirim" title="Kirim Pengajuan" data-param="' . $link . '"><i class="fas fa-paper-plane"></i></button>';
            $background = "";
        } else {
            if ($data['is_ceo'] == 0) {
                $status = "Verifikasi CEO";
                if ($id_role == '21') {
                    $background = ' style="background-color:#f5f5f5"';
                    $btnHapus = "";
                    $btnKirim = "";
                } else {
                    $linkHapus    = paramEncrypt($data['id_pengajuannya']);
                    $btnHapus = "";
                    $background = "";
                    $btnKirim = "";
                }
            } else {
                $status = "Approved by CEO </br>" . tgl_indo($data['ceo_date']);
                $btnHapus = "";
                $background = "";
                $btnKirim = "";
            }
        }

        $content .= '
			<tr ' . $background . '>
				<td class="text-center">' . $count . '<input type="hidden" id="uriExp" value="' . $linkExport . '" /><input type="hidden" id="uriExp2" value="' . $linkExport2 . '" /></td>
				<td class="text-center">
					<p style="margin-bottom:0px">' . $data['nomor_pengajuan'] . '</p>
				</td>
				<td class="text-center">
					<p style="margin-bottom:0px">' . tgl_indo($data['tgl_pengajuan']) . '</p>
				</td>
				<td class="text-center">
					<p style="margin-bottom:0px">' . $nama_bulan . '</p>
				</td>
				<td class="text-center">
					<p style="margin-bottom:0px">' . $data['periode_tahun']  . '</p>
				</td>
				<td class="text-center">
					<p style="margin-bottom:0px">' . $status . '</p>
				</td>
				<td class="text-center">
					' . $btnHapus . '
					<a href="' . $linkDetail . '" class="btn btn-info btn-sm" style="margin-left:10px;"><i class="fas fa-info"></i></a>
					' . $btnKirim . '
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

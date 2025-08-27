<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$draw 	= isset($_POST["element"]) ? htmlspecialchars($_POST["element"], ENT_QUOTES) : 0;
$start 	= isset($_POST["start"]) ? htmlspecialchars($_POST["start"], ENT_QUOTES) : 0;
$length	= isset($_POST['length']) ? htmlspecialchars($_POST["length"], ENT_QUOTES) : 10;
$id_user  = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);
$id_wilayah  = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$id_group  = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);
$id_role  = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$q1	= isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';
$q2	= isset($_POST["q2"]) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : '';
$q3	= isset($_POST["q3"]) ? htmlspecialchars($_POST["q3"], ENT_QUOTES) : '';

if ($q1) {
	$year = $q1;
} else {
	$year = date("Y");
}

if ($id_role == 7) {
	$cabang = " pp.id_cabang = '" . $id_wilayah . "' AND";
} else {
	if ($q3) {
		$cabang = " pp.id_cabang = '" . $q3 . "' AND";
	} else {
		$cabang = "";
	}
}

$p = new paging;
$sql = "SELECT
    a.fullname AS nama_marketing,
    a.id_user AS id_mkt,
    COALESCE(SUM(CASE WHEN MONTH(b.tanggal_kirim) = 1 THEN b.volume_kirim ELSE 0 END), 0) AS total_realisasi_jan,
    COALESCE(SUM(CASE WHEN MONTH(b.tanggal_kirim) = 2 THEN b.volume_kirim ELSE 0 END), 0) AS total_realisasi_feb,
    COALESCE(SUM(CASE WHEN MONTH(b.tanggal_kirim) = 3 THEN b.volume_kirim ELSE 0 END), 0) AS total_realisasi_mar,
    COALESCE(SUM(CASE WHEN MONTH(b.tanggal_kirim) = 4 THEN b.volume_kirim ELSE 0 END), 0) AS total_realisasi_apr,
    COALESCE(SUM(CASE WHEN MONTH(b.tanggal_kirim) = 5 THEN b.volume_kirim ELSE 0 END), 0) AS total_realisasi_mei,
    COALESCE(SUM(CASE WHEN MONTH(b.tanggal_kirim) = 6 THEN b.volume_kirim ELSE 0 END), 0) AS total_realisasi_jun,
    COALESCE(SUM(CASE WHEN MONTH(b.tanggal_kirim) = 7 THEN b.volume_kirim ELSE 0 END), 0) AS total_realisasi_jul,
    COALESCE(SUM(CASE WHEN MONTH(b.tanggal_kirim) = 8 THEN b.volume_kirim ELSE 0 END), 0) AS total_realisasi_ags,
    COALESCE(SUM(CASE WHEN MONTH(b.tanggal_kirim) = 9 THEN b.volume_kirim ELSE 0 END), 0) AS total_realisasi_sep,
    COALESCE(SUM(CASE WHEN MONTH(b.tanggal_kirim) = 10 THEN b.volume_kirim ELSE 0 END), 0) AS total_realisasi_okt,
    COALESCE(SUM(CASE WHEN MONTH(b.tanggal_kirim) = 11 THEN b.volume_kirim ELSE 0 END), 0) AS total_realisasi_nov,
    COALESCE(SUM(CASE WHEN MONTH(b.tanggal_kirim) = 12 THEN b.volume_kirim ELSE 0 END), 0) AS total_realisasi_des
	FROM
		acl_user a
	JOIN pro_customer pc ON a.id_user = pc.id_marketing
	JOIN pro_po_customer ppc ON pc.id_customer = ppc.id_customer
	JOIN pro_po_customer_plan b ON b.id_poc = ppc.id_poc
	JOIN pro_penawaran pp ON pp.id_penawaran = ppc.id_penawaran
	WHERE " . $cabang . " (a.id_role = '11' OR a.id_role = '17')
		AND ppc.poc_approved = 1
		AND b.status_plan = 1
		AND YEAR(b.tanggal_kirim) = '" . $year . "'";

if ($q2) {
	$sql .= "AND a.id_user = '" . $q2 . "'";
}

$sql .= "GROUP BY a.fullname, a.id_user";

$tot_record = $con->num_rows($sql);
// $tot_record = 1;
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);
$sql .= " order by a.fullname asc limit " . $position . ", " . $length;
$link = BASE_URL_CLIENT . '/report/m-volume-report-exp.php?' . paramEncrypt('q1=' . $q1 . '&q2=' . $q2 . '&q3=' . $q3);

// print_r($sql);
// exit();
$content = "";
$count = 0;
if ($tot_record ==  0) {
	$content .= '<tr><td colspan="16" style="text-align:center" id="uriExp" value="' . $link . '">Data tidak ditemukan </td></tr>';
} else {
	$count 		= $position;
	$tot_page 	= ceil($tot_record / $length);
	$result 	= $con->getResult($sql);
	$total_volume = 0;
	$grand_total = 0;
	$total_volume_jan = 0;
	$total_volume_feb = 0;
	$total_volume_mar = 0;
	$total_volume_apr = 0;
	$total_volume_mei = 0;
	$total_volume_jun = 0;
	$total_volume_jul = 0;
	$total_volume_ags = 0;
	$total_volume_sep = 0;
	$total_volume_okt = 0;
	$total_volume_nov = 0;
	$total_volume_des = 0;
	foreach ($result as $data) {
		$count++;
		$total_volume_jan += $data['total_realisasi_jan'];
		$total_volume_feb += $data['total_realisasi_feb'];
		$total_volume_mar += $data['total_realisasi_mar'];
		$total_volume_apr += $data['total_realisasi_apr'];
		$total_volume_mei += $data['total_realisasi_mei'];
		$total_volume_jun += $data['total_realisasi_jun'];
		$total_volume_jul += $data['total_realisasi_jul'];
		$total_volume_ags += $data['total_realisasi_ags'];
		$total_volume_sep += $data['total_realisasi_sep'];
		$total_volume_okt += $data['total_realisasi_okt'];
		$total_volume_nov += $data['total_realisasi_nov'];
		$total_volume_des += $data['total_realisasi_des'];

		$total_volume = $data['total_realisasi_jan'] + $data['total_realisasi_feb'] + $data['total_realisasi_mar'] + $data['total_realisasi_apr'] + $data['total_realisasi_mei'] + $data['total_realisasi_jun'] + $data['total_realisasi_jul'] + $data['total_realisasi_ags'] + $data['total_realisasi_sep'] + $data['total_realisasi_okt'] + $data['total_realisasi_nov'] + $data['total_realisasi_des'];

		$grand_total += $total_volume;

		if ($data['total_realisasi_jan'] > 0) {
			$openDetailJan = "openDetail";
			$styleJan = "cursor:pointer;";
		} else {
			$openDetailJan = "";
			$styleJan = "";
		}

		if ($data['total_realisasi_feb'] > 0) {
			$openDetailFeb = "openDetail";
			$styleFeb = "cursor:pointer;";
		} else {
			$openDetailFeb = "";
			$styleFeb = "";
		}

		if ($data['total_realisasi_mar'] > 0) {
			$openDetailMar = "openDetail";
			$styleMar = "cursor:pointer;";
		} else {
			$openDetailMar = "";
			$styleMar = "";
		}

		if ($data['total_realisasi_apr'] > 0) {
			$openDetailApr = "openDetail";
			$styleApr = "cursor:pointer;";
		} else {
			$openDetailApr = "";
			$styleApr = "";
		}

		if ($data['total_realisasi_mei'] > 0) {
			$openDetailMei = "openDetail";
			$styleMei = "cursor:pointer;";
		} else {
			$openDetailMei = "";
			$styleMei = "";
		}

		if ($data['total_realisasi_jun'] > 0) {
			$openDetailJun = "openDetail";
			$styleJun = "cursor:pointer;";
		} else {
			$openDetailJun = "";
			$styleJun = "";
		}

		if ($data['total_realisasi_jul'] > 0) {
			$openDetailJul = "openDetail";
			$styleJul = "cursor:pointer;";
		} else {
			$openDetailJul = "";
			$styleJul = "";
		}

		if ($data['total_realisasi_ags'] > 0) {
			$openDetailAgs = "openDetail";
			$styleAgs = "cursor:pointer;";
		} else {
			$openDetailAgs = "";
			$styleAgs = "";
		}

		if ($data['total_realisasi_sep'] > 0) {
			$openDetailSep = "openDetail";
			$styleSep = "cursor:pointer;";
		} else {
			$openDetailSep = "";
			$styleSep = "";
		}

		if ($data['total_realisasi_okt'] > 0) {
			$openDetailOkt = "openDetail";
			$styleOkt = "cursor:pointer;";
		} else {
			$openDetailOkt = "";
			$styleOkt = "";
		}

		if ($data['total_realisasi_nov'] > 0) {
			$openDetailNov = "openDetail";
			$styleNov = "cursor:pointer;";
		} else {
			$openDetailNov = "";
			$styleNov = "";
		}

		if ($data['total_realisasi_des'] > 0) {
			$openDetailDes = "openDetail";
			$styleDes = "cursor:pointer;";
		} else {
			$openDetailDes = "";
			$styleDes = "";
		}

		$content .= '
				<tr>
					<td class="text-center" style="width:2%;">' . $count . '</td>
					<td>' . $data['nama_marketing'] . '</td>
					<td align="center" data-wilayah="' . $id_wilayah . '" data-bulan="01" data-tahun="' . $year . '" data-idMkt="' . $data['id_mkt'] . '" class="' . $openDetailJan . '" style="' . $styleJan . '" nowrap>' . number_format($data['total_realisasi_jan']) . '</td>
					<td align="center" data-wilayah="' . $id_wilayah . '" data-bulan="02" data-tahun="' . $year . '" class="' . $openDetailFeb . '" data-idMkt="' . $data['id_mkt'] . '" style="' . $styleFeb . '"  nowrap>' . number_format($data['total_realisasi_feb']) . '</td>
					<td align="center" data-wilayah="' . $id_wilayah . '" data-bulan="03" data-tahun="' . $year . '" class="' . $openDetailMar . '" data-idMkt="' . $data['id_mkt'] . '" style="' . $styleMar . '" nowrap>' . number_format($data['total_realisasi_mar']) . '</td>
					<td align="center" data-wilayah="' . $id_wilayah . '" data-bulan="04" data-tahun="' . $year . '" class="' . $openDetailApr . '" data-idMkt="' . $data['id_mkt'] . '" style="' . $styleApr . '" nowrap>' . number_format($data['total_realisasi_apr']) . '</td>
					<td align="center" data-wilayah="' . $id_wilayah . '" data-bulan="05" data-tahun="' . $year . '" class="' . $openDetailMei . '" data-idMkt="' . $data['id_mkt'] . '" style="' . $styleMei . '" nowrap>' . number_format($data['total_realisasi_mei']) . '</td>
					<td align="center" data-wilayah="' . $id_wilayah . '" data-bulan="06" data-tahun="' . $year . '" class="' . $openDetailJun . '" data-idMkt="' . $data['id_mkt'] . '" style="' . $styleJun . '" nowrap>' . number_format($data['total_realisasi_jun']) . '</td>
					<td align="center" data-wilayah="' . $id_wilayah . '" data-bulan="07" data-tahun="' . $year . '" class="' . $openDetailJul . '" data-idMkt="' . $data['id_mkt'] . '" style="' . $styleJul . '" nowrap>' . number_format($data['total_realisasi_jul']) . '</td>
					<td align="center" data-wilayah="' . $id_wilayah . '" data-bulan="08" data-tahun="' . $year . '" class="' . $openDetailAgs . '" data-idMkt="' . $data['id_mkt'] . '" style="' . $styleAgs . '" nowrap>' . number_format($data['total_realisasi_ags']) . '</td>
					<td align="center" data-wilayah="' . $id_wilayah . '" data-bulan="09" data-tahun="' . $year . '" class="' . $openDetailSep . '" data-idMkt="' . $data['id_mkt'] . '" style="' . $styleSep . '" nowrap>' . number_format($data['total_realisasi_sep']) . '</td>
					<td align="center" data-wilayah="' . $id_wilayah . '" data-bulan="10" data-tahun="' . $year . '" class="' . $openDetailOkt . '" data-idMkt="' . $data['id_mkt'] . '" style="' . $styleOkt . '" nowrap>' . number_format($data['total_realisasi_okt']) . '</td>
					<td align="center" data-wilayah="' . $id_wilayah . '" data-bulan="11" data-tahun="' . $year . '" class="' . $openDetailNov . '" data-idMkt="' . $data['id_mkt'] . '" style="' . $styleNov . '" nowrap>' . number_format($data['total_realisasi_nov']) . '</td>
					<td align="center" data-wilayah="' . $id_wilayah . '" data-bulan="12" data-tahun="' . $year . '" class="' . $openDetailDes . '" data-idMkt="' . $data['id_mkt'] . '" style="' . $styleDes . '" nowrap>' . number_format($data['total_realisasi_des']) . '</td>
					<td align="center" nowrap>' . number_format($total_volume) . '</td>
				</tr>';
	}
	$content .= '
			<tr>
				<td class="text-center bg-gray" colspan="2"><input type="hidden" id="uriExp" value="' . $link . '" /><b>TOTAL</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_volume_jan) . '</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_volume_feb) . '</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_volume_mar) . '</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_volume_apr) . '</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_volume_mei) . '</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_volume_jun) . '</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_volume_jul) . '</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_volume_ags) . '</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_volume_sep) . '</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_volume_okt) . '</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_volume_nov) . '</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_volume_des) . '</b></td>
				<td class="text-center bg-gray"><b>' . number_format($grand_total) . '</b></td>
			</tr>';
}

$json_data = array(
	"items"		=> $content,
	"pages"		=> $tot_page,
	"page"		=> $page,
	"totalData"	=> $tot_record,
	"infoData"	=> "Showing " . ($position + 1) . " to " . $count . " of " . $tot_record . " entries",
);
//var_dump($json_data);exit;

echo json_encode($json_data);
// $volumeData = json_encode($result);
// unset($json_data);

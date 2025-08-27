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
	$sqlWilayah1 = " AND e.id_cabang='" . $id_wilayah . "'";
	$sqlWilayah2 = " AND pp.id_cabang = '" . $id_wilayah . "'";
} else {
	$sqlWilayah1 = "";
	$sqlWilayah2 = "";
	if (!empty($q3)) {
		$id_wilayah = $q3; // pastikan ini di-set
		$sqlWilayah1 = " AND e.id_cabang='" . $id_wilayah . "'";
		$sqlWilayah2 = " AND pp.id_cabang = '" . $id_wilayah . "'";
	}
}


$p = new paging;

$sql = "SELECT a.fullname as nama_marketing, a.id_user as id_mkt, pp.id_cabang,
( SELECT count(DISTINCT d.id_customer) FROM pro_po_customer as d 
JOIN pro_customer as b ON d.id_customer=b.id_customer 
JOIN pro_po_customer_plan as c ON d.id_poc=c.id_poc
JOIN pro_penawaran e ON e.id_penawaran=d.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master 
WHERE 1=1 " . $sqlWilayah1 . " AND b.id_marketing = a.id_user AND c.realisasi_kirim != 0 AND MONTH(d.tanggal_poc) = '01' AND YEAR(d.tanggal_poc) = '" . $year . "') as total_customer_jan,

( SELECT count(DISTINCT d.id_customer) FROM pro_po_customer as d 
JOIN pro_customer as b ON d.id_customer=b.id_customer 
JOIN pro_po_customer_plan as c ON d.id_poc=c.id_poc
JOIN pro_penawaran e ON e.id_penawaran=d.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master 
WHERE 1=1 " . $sqlWilayah1 . " AND b.id_marketing = a.id_user AND c.realisasi_kirim != 0 AND MONTH(d.tanggal_poc) = '02' AND YEAR(d.tanggal_poc) = '" . $year . "') as total_customer_feb,

( SELECT count(DISTINCT d.id_customer) FROM pro_po_customer as d 
JOIN pro_customer as b ON d.id_customer=b.id_customer 
JOIN pro_po_customer_plan as c ON d.id_poc=c.id_poc
JOIN pro_penawaran e ON e.id_penawaran=d.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master 
WHERE 1=1 " . $sqlWilayah1 . " AND b.id_marketing = a.id_user AND c.realisasi_kirim != 0 AND MONTH(d.tanggal_poc) = '03' AND YEAR(d.tanggal_poc) = '" . $year . "') as total_customer_mar,

( SELECT count(DISTINCT d.id_customer) FROM pro_po_customer as d 
JOIN pro_customer as b ON d.id_customer=b.id_customer 
JOIN pro_po_customer_plan as c ON d.id_poc=c.id_poc
JOIN pro_penawaran e ON e.id_penawaran=d.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master 
WHERE 1=1 " . $sqlWilayah1 . " AND b.id_marketing = a.id_user AND c.realisasi_kirim != 0 AND MONTH(d.tanggal_poc) = '04' AND YEAR(d.tanggal_poc) = '" . $year . "') as total_customer_apr,

( SELECT count(DISTINCT d.id_customer) FROM pro_po_customer as d 
JOIN pro_customer as b ON d.id_customer=b.id_customer 
JOIN pro_po_customer_plan as c ON d.id_poc=c.id_poc
JOIN pro_penawaran e ON e.id_penawaran=d.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master 
WHERE 1=1 " . $sqlWilayah1 . " AND b.id_marketing = a.id_user AND c.realisasi_kirim != 0 AND MONTH(d.tanggal_poc) = '05' AND YEAR(d.tanggal_poc) = '" . $year . "') as total_customer_mei,

( SELECT count(DISTINCT d.id_customer) FROM pro_po_customer as d 
JOIN pro_customer as b ON d.id_customer=b.id_customer 
JOIN pro_po_customer_plan as c ON d.id_poc=c.id_poc
JOIN pro_penawaran e ON e.id_penawaran=d.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master 
WHERE 1=1 " . $sqlWilayah1 . " AND b.id_marketing = a.id_user AND c.realisasi_kirim != 0 AND MONTH(d.tanggal_poc) = '06' AND YEAR(d.tanggal_poc) = '" . $year . "') as total_customer_jun,

( SELECT count(DISTINCT d.id_customer) FROM pro_po_customer as d 
JOIN pro_customer as b ON d.id_customer=b.id_customer 
JOIN pro_po_customer_plan as c ON d.id_poc=c.id_poc
JOIN pro_penawaran e ON e.id_penawaran=d.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master 
WHERE 1=1 " . $sqlWilayah1 . " AND b.id_marketing = a.id_user AND c.realisasi_kirim != 0 AND MONTH(d.tanggal_poc) = '07' AND YEAR(d.tanggal_poc) = '" . $year . "') as total_customer_jul,

( SELECT count(DISTINCT d.id_customer) FROM pro_po_customer as d 
JOIN pro_customer as b ON d.id_customer=b.id_customer 
JOIN pro_po_customer_plan as c ON d.id_poc=c.id_poc
JOIN pro_penawaran e ON e.id_penawaran=d.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master 
WHERE 1=1 " . $sqlWilayah1 . " AND b.id_marketing = a.id_user AND c.realisasi_kirim != 0 AND MONTH(d.tanggal_poc) = '08' AND YEAR(d.tanggal_poc) = '" . $year . "') as total_customer_ags,

( SELECT count(DISTINCT d.id_customer) FROM pro_po_customer as d 
JOIN pro_customer as b ON d.id_customer=b.id_customer 
JOIN pro_po_customer_plan as c ON d.id_poc=c.id_poc
JOIN pro_penawaran e ON e.id_penawaran=d.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master 
WHERE 1=1 " . $sqlWilayah1 . " AND b.id_marketing = a.id_user AND c.realisasi_kirim != 0 AND MONTH(d.tanggal_poc) = '09' AND YEAR(d.tanggal_poc) = '" . $year . "') as total_customer_sep,

( SELECT count(DISTINCT d.id_customer) FROM pro_po_customer as d 
JOIN pro_customer as b ON d.id_customer=b.id_customer 
JOIN pro_po_customer_plan as c ON d.id_poc=c.id_poc
JOIN pro_penawaran e ON e.id_penawaran=d.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master 
WHERE 1=1 " . $sqlWilayah1 . " AND b.id_marketing = a.id_user AND c.realisasi_kirim != 0 AND MONTH(d.tanggal_poc) = '10' AND YEAR(d.tanggal_poc) = '" . $year . "') as total_customer_okt,

( SELECT count(DISTINCT d.id_customer) FROM pro_po_customer as d 
JOIN pro_customer as b ON d.id_customer=b.id_customer 
JOIN pro_po_customer_plan as c ON d.id_poc=c.id_poc
JOIN pro_penawaran e ON e.id_penawaran=d.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master 
WHERE 1=1 " . $sqlWilayah1 . " AND b.id_marketing = a.id_user AND c.realisasi_kirim != 0 AND MONTH(d.tanggal_poc) = '11' AND YEAR(d.tanggal_poc) = '" . $year . "') as total_customer_nov,

( SELECT count(DISTINCT d.id_customer) FROM pro_po_customer as d 
JOIN pro_customer as b ON d.id_customer=b.id_customer 
JOIN pro_po_customer_plan as c ON d.id_poc=c.id_poc
JOIN pro_penawaran e ON e.id_penawaran=d.id_penawaran
JOIN pro_master_cabang f on e.id_cabang = f.id_master 
WHERE 1=1 " . $sqlWilayah1 . " AND b.id_marketing = a.id_user AND c.realisasi_kirim != 0 AND MONTH(d.tanggal_poc) = '12' AND YEAR(d.tanggal_poc) = '" . $year . "') as total_customer_des

FROM acl_user a 
JOIN pro_customer pc ON a.id_user=pc.id_marketing
JOIN pro_po_customer ppc ON pc.id_customer=ppc.id_customer
JOIN pro_po_customer_plan ppcp ON ppcp.id_poc=ppc.id_poc
JOIN pro_penawaran pp ON pp.id_penawaran=ppc.id_penawaran
JOIN pro_master_cabang pmc on pp.id_cabang = pmc.id_master
WHERE 1=1 " . $sqlWilayah2 . " AND (a.id_role = '11' OR a.id_role = '17') AND ppcp.realisasi_kirim != 0 AND YEAR(ppc.tanggal_poc) = '" . $year . "'";

if ($q2) {
	$sql .= "AND a.id_user = '" . $q2 . "'";
}

$sql .= "GROUP BY a.id_user";

$tot_record = $con->num_rows($sql);
// $tot_record = 1;
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);
$sql .= " order by a.fullname asc limit " . $position . ", " . $length;
$link = BASE_URL_CLIENT . '/report/m-active-customer-exp.php?' . paramEncrypt('q1=' . $q1 . '&q2=' . $q2 . '&q3=' . $q3);

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

	// echo json_encode($result);
	// exit();
	$total_customer 	= 0;
	$grand_total 		= 0;
	$total_customer_jan = 0;
	$total_customer_feb = 0;
	$total_customer_mar = 0;
	$total_customer_apr = 0;
	$total_customer_mei = 0;
	$total_customer_jun = 0;
	$total_customer_jul = 0;
	$total_customer_ags = 0;
	$total_customer_sep = 0;
	$total_customer_okt = 0;
	$total_customer_nov = 0;
	$total_customer_des = 0;
	foreach ($result as $data) {
		$count++;
		$total_customer_jan += $data['total_customer_jan'];
		$total_customer_feb += $data['total_customer_feb'];
		$total_customer_mar += $data['total_customer_mar'];
		$total_customer_apr += $data['total_customer_apr'];
		$total_customer_mei += $data['total_customer_mei'];
		$total_customer_jun += $data['total_customer_jun'];
		$total_customer_jul += $data['total_customer_jul'];
		$total_customer_ags += $data['total_customer_ags'];
		$total_customer_sep += $data['total_customer_sep'];
		$total_customer_okt += $data['total_customer_okt'];
		$total_customer_nov += $data['total_customer_nov'];
		$total_customer_des += $data['total_customer_des'];

		$total_customer = $data['total_customer_jan'] + $data['total_customer_feb'] + $data['total_customer_mar'] + $data['total_customer_apr'] + $data['total_customer_mei'] + $data['total_customer_jun'] + $data['total_customer_jul'] + $data['total_customer_ags'] + $data['total_customer_sep'] + $data['total_customer_okt'] + $data['total_customer_nov'] + $data['total_customer_des'];

		$grand_total += $total_customer;

		if ($data['total_customer_jan'] > 0) {
			$openDetailJan = "openDetail";
			$styleJan = "cursor:pointer;";
		} else {
			$openDetailJan = "";
			$styleJan = "";
		}

		if ($data['total_customer_feb'] > 0) {
			$openDetailFeb = "openDetail";
			$styleFeb = "cursor:pointer;";
		} else {
			$openDetailFeb = "";
			$styleFeb = "";
		}

		if ($data['total_customer_mar'] > 0) {
			$openDetailMar = "openDetail";
			$styleMar = "cursor:pointer;";
		} else {
			$openDetailMar = "";
			$styleMar = "";
		}

		if ($data['total_customer_apr'] > 0) {
			$openDetailApr = "openDetail";
			$styleApr = "cursor:pointer;";
		} else {
			$openDetailApr = "";
			$styleApr = "";
		}

		if ($data['total_customer_mei'] > 0) {
			$openDetailMei = "openDetail";
			$styleMei = "cursor:pointer;";
		} else {
			$openDetailMei = "";
			$styleMei = "";
		}

		if ($data['total_customer_jun'] > 0) {
			$openDetailJun = "openDetail";
			$styleJun = "cursor:pointer;";
		} else {
			$openDetailJun = "";
			$styleJun = "";
		}

		if ($data['total_customer_jul'] > 0) {
			$openDetailJul = "openDetail";
			$styleJul = "cursor:pointer;";
		} else {
			$openDetailJul = "";
			$styleJul = "";
		}

		if ($data['total_customer_ags'] > 0) {
			$openDetailAgs = "openDetail";
			$styleAgs = "cursor:pointer;";
		} else {
			$openDetailAgs = "";
			$styleAgs = "";
		}

		if ($data['total_customer_sep'] > 0) {
			$openDetailSep = "openDetail";
			$styleSep = "cursor:pointer;";
		} else {
			$openDetailSep = "";
			$styleSep = "";
		}

		if ($data['total_customer_okt'] > 0) {
			$openDetailOkt = "openDetail";
			$styleOkt = "cursor:pointer;";
		} else {
			$openDetailOkt = "";
			$styleOkt = "";
		}

		if ($data['total_customer_nov'] > 0) {
			$openDetailNov = "openDetail";
			$styleNov = "cursor:pointer;";
		} else {
			$openDetailNov = "";
			$styleNov = "";
		}

		if ($data['total_customer_des'] > 0) {
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
				<td align="center" data-wilayah="' . $data['id_cabang'] . '" data-bulan="01" data-tahun="' . $year . '" data-idMkt="' . $data['id_mkt'] . '" class="' . $openDetailJan . '" style="' . $styleJan . '" nowrap>' . number_format($data['total_customer_jan']) . '</td>
				<td align="center" data-wilayah="' . $data['id_cabang'] . '" data-bulan="02" data-tahun="' . $year . '" data-idMkt="' . $data['id_mkt'] . '" class="' . $openDetailFeb . '" style="' . $styleFeb . '" nowrap>' . number_format($data['total_customer_feb']) . '</td>
				<td align="center" data-wilayah="' . $data['id_cabang'] . '" data-bulan="03" data-tahun="' . $year . '" data-idMkt="' . $data['id_mkt'] . '" class="' . $openDetailMar . '" style="' . $styleMar . '" nowrap>' . number_format($data['total_customer_mar']) . '</td>
				<td align="center" data-wilayah="' . $data['id_cabang'] . '" data-bulan="04" data-tahun="' . $year . '" data-idMkt="' . $data['id_mkt'] . '" class="' . $openDetailApr . '" style="' . $styleApr . '" nowrap>' . number_format($data['total_customer_apr']) . '</td>
				<td align="center" data-wilayah="' . $data['id_cabang'] . '" data-bulan="05" data-tahun="' . $year . '" data-idMkt="' . $data['id_mkt'] . '" class="' . $openDetailMei . '" style="' . $styleMei . '" nowrap>' . number_format($data['total_customer_mei']) . '</td>
				<td align="center" data-wilayah="' . $data['id_cabang'] . '" data-bulan="06" data-tahun="' . $year . '" data-idMkt="' . $data['id_mkt'] . '" class="' . $openDetailJun . '" style="' . $styleJun . '" nowrap>' . number_format($data['total_customer_jun']) . '</td>
				<td align="center" data-wilayah="' . $data['id_cabang'] . '" data-bulan="07" data-tahun="' . $year . '" data-idMkt="' . $data['id_mkt'] . '" class="' . $openDetailJul . '" style="' . $styleJul . '" nowrap>' . number_format($data['total_customer_jul']) . '</td>
				<td align="center" data-wilayah="' . $data['id_cabang'] . '" data-bulan="08" data-tahun="' . $year . '" data-idMkt="' . $data['id_mkt'] . '" class="' . $openDetailAgs . '" style="' . $styleAgs . '" nowrap>' . number_format($data['total_customer_ags']) . '</td>
				<td align="center" data-wilayah="' . $data['id_cabang'] . '" data-bulan="09" data-tahun="' . $year . '" data-idMkt="' . $data['id_mkt'] . '" class="' . $openDetailSep . '" style="' . $styleSep . '" nowrap>' . number_format($data['total_customer_sep']) . '</td>
				<td align="center" data-wilayah="' . $data['id_cabang'] . '" data-bulan="10" data-tahun="' . $year . '" data-idMkt="' . $data['id_mkt'] . '" class="' . $openDetailOkt . '" style="' . $styleOkt . '" nowrap>' . number_format($data['total_customer_okt']) . '</td>
				<td align="center" data-wilayah="' . $data['id_cabang'] . '" data-bulan="11" data-tahun="' . $year . '" data-idMkt="' . $data['id_mkt'] . '" class="' . $openDetailNov . '" style="' . $styleNov . '" nowrap>' . number_format($data['total_customer_nov']) . '</td>
				<td align="center" data-wilayah="' . $data['id_cabang'] . '" data-bulan="12" data-tahun="' . $year . '" data-idMkt="' . $data['id_mkt'] . '" class="' . $openDetailDes . '" style="' . $styleDes . '" nowrap>' . number_format($data['total_customer_des']) . '</td>
				<td align="center" nowrap>' . number_format($total_customer) . '</td>
			</tr>';
	}
	$content .= '
			<tr>
				<td class="text-center bg-gray" colspan="2"><input type="hidden" id="uriExp" value="' . $link . '" /><b>TOTAL</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_customer_jan) . '</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_customer_feb) . '</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_customer_mar) . '</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_customer_apr) . '</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_customer_mei) . '</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_customer_jun) . '</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_customer_jul) . '</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_customer_ags) . '</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_customer_sep) . '</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_customer_okt) . '</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_customer_nov) . '</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_customer_des) . '</b></td>
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

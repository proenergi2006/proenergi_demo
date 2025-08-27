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
$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$q1	= isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';
$q2	= isset($_POST["q2"]) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : '';
$q3	= isset($_POST["q3"]) ? htmlspecialchars($_POST["q3"], ENT_QUOTES) : '';
$q4	= isset($_POST["q4"]) ? htmlspecialchars($_POST["q4"], ENT_QUOTES) : '';
$q5	= isset($_POST["q5"]) ? htmlspecialchars($_POST["q5"], ENT_QUOTES) : '';
$q6	= isset($_POST["q6"]) ? htmlspecialchars($_POST["q6"], ENT_QUOTES) : '';

$p = new paging;
$sql = "select 
a.id_customer,
a.nama_customer,
a.kode_pelanggan,
a.alamat_customer,
a.telp_customer,
a.fax_customer, 
a.status_customer, 
a.id_marketing,
b.nama_kab, 
c.nama_prov,
d.fullname, 
e.nama_cabang, 
f.jum_lcr, 
a.prospect_customer_date, 
a.fix_customer_redate,
g.tanggal_approved,
k.tanggal_poc,
l.created_time,
DATE_ADD(a.prospect_customer_date, INTERVAL '3' MONTH) AS tiga_bulan, 
CASE 
	WHEN a.status_customer = 2 THEN DATEDIFF(DATE_ADD(a.prospect_customer_date, INTERVAL '3' MONTH), CURDATE()) 
	ELSE 0 
END AS remaining 
FROM 
pro_customer a 
JOIN pro_master_kabupaten b ON a.kab_customer = b.id_kab 
JOIN pro_master_provinsi c ON a.prov_customer = c.id_prov 
JOIN acl_user d ON a.id_marketing = d.id_user 
LEFT JOIN pro_master_cabang e ON a.id_wilayah = e.id_master
LEFT JOIN (
	SELECT 
		COUNT(*) AS jum_lcr, 
		id_customer 
	FROM 
		pro_customer_lcr 
	WHERE 
		flag_approval = 1 
	GROUP BY 
		id_customer
) f ON a.id_customer = f.id_customer 
LEFT JOIN (
	SELECT 
		id_customer,
		MAX(tanggal_approved) AS tanggal_approved
	FROM 
		pro_customer_verification
	GROUP BY 
		id_customer
) g ON a.id_customer = g.id_customer
left join pro_po_customer h ON a.id_customer = h.id_customer
left join pro_po_customer_plan i ON h.id_poc = i.id_poc
left join pro_pr_detail j ON i.id_plan = j.id_plan
LEFT JOIN (
	SELECT id_customer, MAX(tanggal_poc) as tanggal_poc FROM pro_po_customer GROUP BY id_customer
	) k on a.id_customer = k.id_customer
	LEFT JOIN (
		select id_customer, MAX(created_time) as created_time FROM pro_penawaran GROUP BY id_customer
		) l on a.id_customer = l.id_customer 
	
where
1=1";

if (($sesrol == 6)) {
	$sql .= " and ((d.id_role = 11) or (d.id_role = 17))";
} else if ($sesrol == 7) {
	$sql .= " and (d.id_role = 11 and a.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "')";
}

if ($q1 != "")
	$sql .= " and (upper(a.nama_customer) like '%" . strtoupper($q1) . "%' or upper(a.kode_pelanggan) like '%" . strtoupper($q1) . "%')";
if ($q2 != "") {
	if ($q2 == "1") {
		$sql .= " and a.status_customer = '" . $q2 . "'";
	} else {
		$sql .= " and a.status_customer >= '" . $q2 . "'";
	}
}
if ($q3 != "")
	$sql .= " and a.id_marketing = '" . $q3 . "'";
if ($q4 != "")
	$sql .= " and a.id_wilayah = '" . $q4 . "'";

if ($q5 != "") {
	$enamBulanYangLalu = strtotime("-6 months");

	if ($q5 == "1") {
		// Filter untuk tampilkan data dengan Last Order > 6 bulan
		$sql .= " AND DATE_ADD(k.tanggal_poc, INTERVAL 6 MONTH) <= NOW()";
	} elseif ($q5 == "2") {
		// Filter untuk tampilkan data dengan Last Order < 6 bulan
		$sql .= " AND DATE_ADD(k.tanggal_poc, INTERVAL 6 MONTH) > NOW()";
	}
}

if ($q6 != "") {
	$satuBulanYangLalu = strtotime("-1 months");

	if ($q6 == "1") {
		// Filter untuk tampilkan data dengan Last Quotation > 1 bulan
		$sql .= " AND DATE_ADD(l.created_time, INTERVAL 1 MONTH) <= NOW()";
	} elseif ($q6 == "2") {
		// Filter untuk tampilkan data dengan Last Quotation < 1 bulan
		$sql .= " AND DATE_ADD(l.created_time, INTERVAL 1 MONTH) > NOW()";
	}
}




$tot_record = $con->num_rows($sql);
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);
$sql .= " GROUP BY a.id_customer";
$sql .= " order by a.id_customer desc limit " . $position . ", " . $length;

$content = "";
$count = 0;
if ($tot_record <= 0) {
	$content .= '<tr><td colspan="7" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
	$count 		= $position;
	$tot_page 	= ceil($tot_record / $length);
	$result 	= $con->getResult($sql);
	foreach ($result as $data) {
		$count++;
		$linkDetail	= BASE_URL_CLIENT . '/customer-admin-detail.php?' . paramEncrypt('idr=' . $data['id_customer']);
		$status		= array(1 => "Prospek", "Tetap", "Tetap");
		$alamat		= $data['alamat_customer'] . " " . str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']) . " " . $data['nama_prov'];

		$content .= '
				<tr class="clickable-row" data-href="' . $linkDetail . '">
					<td class="text-center">' . $count . '</td>
					<td>
						<p style="margin-bottom: 0px">' . ($data['kode_pelanggan'] ? '<b>' . $data['kode_pelanggan'] . '</b>' : '--------') . '</b></p>
						<p style="margin-bottom: 0px"><b>' . $data['nama_customer'] . '</b></p>
						<p style="margin-bottom: 0px"><i>' . $data['fullname'] . '</i></p>
					</td>
					<td>
						<p style="margin-bottom: 0px">' . $alamat . '</p>
						<p style="margin-bottom: 0px">Telp : ' . $data['telp_customer'] . ', Fax : ' . $data['fax_customer'] . '</p>
					</td>
					<td>' . $data['nama_cabang'] . '</td>
					<td>
					<p style="margin-bottom: 0px"><b>' . $status[$data['status_customer']] . '</b></p>
					';

		if ($data['tanggal_approved'] !== null) {
			$tglApproved = date("d/m/Y H:i:s", strtotime($data['tanggal_approved']));
			$content .= '<p style="margin-bottom: 0px; ">Verified: ' . $tglApproved . '</p>';
		}

		if ($data['created_time'] !== null) {
			$tglPenawaran = date("d/m/Y", strtotime($data['created_time']));

			// Periksa apakah tgl_quotation dalam rentang lebih dari 1 bulan dari sekarang
			$satuBulanYangLalu = strtotime("-1 months");
			$tglQuotationTimestamp = strtotime($data['created_time']);
			if ($tglQuotationTimestamp >= $satuBulanYangLalu) {
				// Tampilkan dengan warna font hijau
				$content .= '<p style="margin-bottom: 0px; color: green;">Last Quotation  : ' . $tglPenawaran . '</p>';
			} else {
				// Tampilkan dengan warna font merah
				$content .= '<p style="margin-bottom: 0px; color: red;">Last Quotation  : ' . $tglPenawaran . '</p>';
			}
		}

		if ($data['tanggal_poc'] !== null) {
			$tglLastOrder = date("d/m/Y", strtotime($data['tanggal_poc']));

			// Periksa apakah tgl_last_order dalam rentang lebih dari 6 bulan dari sekarang
			$enamBulanYangLalu = strtotime("-6 months");
			$tglLastOrderTimestamp = strtotime($data['tanggal_poc']);
			if ($tglLastOrderTimestamp >= $enamBulanYangLalu) {
				// Tampilkan dengan warna font hijau
				$content .= '<p style="margin-bottom: 0px; color: green;">Last Order : ' . $tglLastOrder . '</p>';
			} else {
				// Tampilkan dengan warna font merah
				$content .= '<p style="margin-bottom: 0px; color: red;">Last Order : ' . $tglLastOrder . '</p>';
			}
		}


		$content .= '
				</td>
					<td class="text-center">' . ($data['jum_user'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>') . '</td>
					<td class="text-center action"><a class="margin-sm btn btn-action btn-info" title="Detail" href="' . $linkDetail . '"><i class="fa fa-info-circle"></i></a> </td>
				</tr>';
	}
}

$json_data = array(
	"items"		=> $content,
	"pages"		=> $tot_page,
	"page"		=> $page,
	"totalData"	=> $tot_record,
	"infoData"	=> "Showing " . ($position + 1) . " to " . $count . " of " . $tot_record . " entries",
);
echo json_encode($json_data);

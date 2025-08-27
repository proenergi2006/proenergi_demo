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
$length	= isset($_POST['length']) ? htmlspecialchars($_POST["length"], ENT_QUOTES) : 25;
$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$q1	= isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';
$q2	= isset($_POST["q2"]) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : '';
$q3	= isset($_POST["q3"]) ? htmlspecialchars($_POST["q3"], ENT_QUOTES) : '';
$q4	= isset($_POST["q4"]) ? htmlspecialchars($_POST["q4"], ENT_QUOTES) : '';
$q5	= isset($_POST["q5"]) ? htmlspecialchars($_POST["q5"], ENT_QUOTES) : '';

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
					m.PN, 
					n.PO,
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
					left JOIN (
						SELECT 
							COUNT(*) AS PN, 
							id_customer 
						FROM 
							pro_penawaran 
						GROUP BY 
							id_customer
					) m ON a.id_customer = m.id_customer 
					left JOIN (
						SELECT 
							COUNT(*) AS PO, 
							id_customer 
						FROM 
							pro_po_customer 
						GROUP BY 
							id_customer
					) n ON a.id_customer = n.id_customer
					LEFT JOIN (
	SELECT id_customer, MAX(tanggal_poc) as tanggal_poc FROM pro_po_customer GROUP BY id_customer
	) k on a.id_customer = k.id_customer
					LEFT JOIN (
						select id_customer, MAX(created_time) as created_time FROM pro_penawaran GROUP BY id_customer
						) l on a.id_customer = l.id_customer 

					where
					1=1";

if (($sesrol == 11 || $sesrol == 17)) {
	$sql .= " and a.id_marketing = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "'"; // alvin
	// $sql .= " and 1=1 ";
} else if ($sesrol == 18) {
	if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']))
		$sql .= " and (a.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "' or a.id_marketing = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "')"; // alvin
	else if (!paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']))
		$sql .= " and (a.id_group = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']) . "' or a.id_marketing = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "')"; // alvin
} else if (($sesrol == 11 || $sesrol == 17) && $q1 != "") {
	$sql .= "";
} else if ($sesrol == 6) {
	$sql .= " and ((d.id_role = 11 and a.id_group = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']) . "') or (d.id_role = 17 and d.id_om = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "'))";
} else if ($sesrol == 7) {
	$sql .= " and (d.id_role = 11 and a.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "')";
}

if ($q1 != "")
	$sql .= " and (upper(a.nama_customer) like '%" . strtoupper($q1) . "%' or a.kode_pelanggan = '" . $q1 . "')";
if ($q2 != "") {
	if ($q2 == "1") {
		$sql .= " and a.status_customer = '" . $q2 . "'";
	} else {
		$sql .= " and a.status_customer >= '" . $q2 . "'";
	}
}
if ($q3 != "")
	$sql .= " and a.id_wilayah = '" . $q3 . "'";

// develop 25/01/2024 *Iwan Hermawan* 

if ($q4 != "") {
	$enamBulanYangLalu = strtotime("-6 months");

	if ($q4 == "1") {
		// Filter untuk tampilkan data dengan Last Order > 6 bulan
		$sql .= " AND DATE_ADD(k.tanggal_poc, INTERVAL 6 MONTH) <= NOW()";
	} elseif ($q4 == "2") {
		// Filter untuk tampilkan data dengan Last Order < 6 bulan
		$sql .= " AND DATE_ADD(k.tanggal_poc, INTERVAL 6 MONTH) > NOW()";
	}
}

if ($q5 != "") {
	$satuBulanYangLalu = strtotime("-1 months");

	if ($q5 == "1") {
		// Filter untuk tampilkan data dengan Last Quotation > 1 bulan
		$sql .= " AND DATE_ADD(l.created_time, INTERVAL 1 MONTH) <= NOW()";
	} elseif ($q5 == "2") {
		// Filter untuk tampilkan data dengan Last Quotation < 1 bulan
		$sql .= " AND DATE_ADD(l.created_time, INTERVAL 1 MONTH) > NOW()";
	}
}

//end 

$tot_record = $con->num_rows($sql);
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);
$sql .= " GROUP BY a.id_customer";
$sql .= " ORDER BY a.id_customer DESC LIMIT " . $position . ", " . $length;

$count = 0;
$content = "";
if ($tot_record <= 0) {
	$content .= '<tr><td colspan="7" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
	$count 		= $position;
	$tot_page 	= ceil($tot_record / $length);
	$result 	= $con->getResult($sql);
	foreach ($result as $data) {
		$count++;
		$linkTawar	= BASE_URL_CLIENT . '/penawaran-add.php?' . paramEncrypt('idc=' . $data['id_customer']);
		$linkLCR	= BASE_URL_CLIENT . '/lcr-add.php?' . paramEncrypt('idc=' . $data['id_customer']);
		$linkPOC	= BASE_URL_CLIENT . '/po-customer-add.php?' . paramEncrypt('idc=' . $data['id_customer']);
		$linkDetail	= BASE_URL_CLIENT . '/customer-detail.php?' . paramEncrypt('idr=' . $data['id_customer']);
		$linkHapus	= paramEncrypt("customer#|#" . $data['id_customer']);
		$status		= array(1 => "Prospect", "Tetap", "Tetap");
		$tmp_addr 	= strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
		$alamat 	= $data['alamat_customer'] . " " . ucwords($tmp_addr) . " " . $data['nama_prov'];
		$tgl_remain = date("Y/m/d", strtotime("+" . $data['remaining'] . " days"));
		$statEval 	= ($data['status_customer'] == 4) ? '<i>' . $data['remaining'] . ' hari menuju evaluasi<br />[' . tgl_indo($tgl_remain, 'short') . ']</i>' : '';

		if ($data['status_customer'] == 1) $status_customer = $status[$data['status_customer']];
		//else if($data['status_customer'] == 2) $status_customer = $status[$data['status_customer']].'<br />'.tgl_indo($data['prospect_customer_date'], 'short');
		else if ($data['status_customer'] == 2) $status_customer = $status[$data['status_customer']] . '<br />' . tgl_indo($data['fix2_customer_redate'], 'short');

		if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) != $data['id_marketing'] and $sesrol != 18 and $sesrol != 7)
			$linkDetail = '#';

		if ($sesrol == '1' or $sesrol == '3' or $sesrol == '6')
			$linkDetail = BASE_URL_CLIENT . '/customer-admin-detail.php?' . paramEncrypt('idr=' . $data['id_customer']);

		$btnAction = '';
		if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) == $data['id_marketing'] or $sesrol == "18" or $sesrol == '1') {
			$btnAction = '
					<div style="margin:3px 3px 3px 3px;">
						<a class="btn btn-sm btn-action btn-primary" style="margin:5px 3px 3px 5px;" title="Penawaran" href="' . $linkTawar . '">PN</a>
						<a class="btn btn-sm btn-action btn-primary" style="margin:5px 3px 3px 0px;" title="LCR" href="' . $linkLCR . '">LCR</a>
						<a class="btn btn-sm btn-action btn-primary" style="margin:5px 3px 3px 0px;" title="PO Customer" href="' . $linkPOC . '">PO</a>
					</div>
					<div style="margin:3px 8px;">
						<a class="btn btn-action btn-info jarak-kanan" title="Detail" href="' . $linkDetail . '"><i class="fa fa-info-circle"></i></a> 
						<a class="delete btn btn-action btn-danger" title="Delete" data-param-idx="' . $linkHapus . '" data-action="deleteGrid">
							 <i class="fa fa-trash"></i></a>
					</div>
				';
		} else 
			if ($sesrol == '3' or $sesrol == '6' or $sesrol == '7') {
			$btnAction = '<a class="btn btn-action btn-info" style="margin:5px 3px 3px 5px;" title="Detail" href="' . $linkDetail . '"><i class="fa fa-info-circle"></i></a>';
		}

		$content .= '
    <tr class="clickable-row" data-href="' . $linkDetail . '">
        <td class="text-center">
            ' . $count . '
        </td>
        <td>
            <p style="margin-bottom: 0px">' . ($data['kode_pelanggan'] ? '<b>' . $data['kode_pelanggan'] . '</b>' : '--------') . '</p>
            <p style="margin-bottom: 0px"><b>' . $data['nama_customer'] . '</b></p>
            <p style="margin-bottom: 0px"><i>' . $data['fullname'] . '</i></p>
        </td>
        <td>
            <p style="margin-bottom: 0px">' . $alamat . '</p>
            <p style="margin-bottom: 0px">Telp : ' . $data['telp_customer'] . ', Fax : ' . $data['fax_customer'] . '</p>
        </td>
        <td>' . $data['nama_cabang'] . '</td>
        <td>
            <p style="margin-bottom: 0px"><b>' . $status_customer . '</b></p>
            <p style="margin-bottom: 0px">' . $statEval . '</p>';

		if ($data['tanggal_approved'] !== null) {
			$tglApproved = date("d/m/Y H:i:s", strtotime($data['tanggal_approved']));
			$content .= '<p style="margin-bottom: 0px; ">Verified  : ' . $tglApproved . '</p>';
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
        <td class="text-center">' . ($data['jum_lcr'] ? $data['jum_lcr'] : '<i class="fa fa-times"></i>') . '</td>
		<td class="text-center">
		<p style="margin-bottom: 0px"><b>PN : ' . $data['PN'] . '</b></p>
		<p style="margin-bottom: 0px"><b>PO : ' . $data['PO'] . '</b></p>
		</td>
        <td class="action" style="min-width: 125px;">
            ' . $btnAction . '
        </td>
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

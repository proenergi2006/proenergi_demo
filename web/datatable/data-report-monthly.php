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

$month_now = date("m");
$year_now = date("Y");

if ($q1 && $q2) {
	$awalBulan = tgl_db($q1);
	$akhirBulan = tgl_db($q2);
} else {
	// Mendapatkan tanggal awal bulan
	$awalBulan = date('Y-m-01', strtotime("$year_now-$month_now-01"));

	// Mendapatkan tanggal akhir bulan
	$akhirBulan = date('Y-m-t', strtotime("$year_now-$month_now-01"));
}

// Menyusun URL
$link = BASE_URL_CLIENT . '/report/data-report-monthly-exp.php?' . 'q1=' . $q1 . '&q2=' . $q2;

$p = new paging;
$sql = "SELECT * FROM pro_master_cabang WHERE is_active = '1' AND id_master NOT IN('1','10','8')";

$tot_record = $con->num_rows($sql);
// $tot_record = 1;
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);
$sql .= " ORDER BY nama_cabang ASC limit " . $position . ", " . $length;

// print_r($sql);
// exit();
$content = "";
$count = 0;
if ($tot_record ==  0) {
	$content .= '<tr><td colspan="15" style="text-align:center"><input type="hidden" id="uriExp" value="' . $link . '" />Data tidak ditemukan </td></tr>';
} else {
	$count 		= $position;
	$tot_page 	= ceil($tot_record / $length);
	$result 	= $con->getResult($sql);

	// Total POC
	$total_qty_poc = 0;
	$total_vol_poc = 0;
	$total_nominal_poc = 0;

	// Total DO
	$total_qty_do = 0;
	$total_vol_do = 0;

	// Total Loaded
	$total_qty_loaded = 0;
	$total_vol_loaded = 0;

	// Total Delivered
	$total_qty_delivered = 0;
	$total_vol_delivered = 0;

	// Total Realisasi
	$total_qty_realisasi = 0;
	$total_vol_realisasi = 0;

	// Total Invoice
	$total_qty_inv = 0;
	$total_vol_inv = 0;
	$total_nominal_inv = 0;

	foreach ($result as $data) {
		$count++;

		$sql_po = "SELECT COUNT(a.id_poc) as qty, SUM(a.volume_poc) as volume_poc, SUM(a.harga_poc * a.volume_poc) as nominal FROM pro_po_customer a
		JOIN pro_customer b ON a.id_customer=b.id_customer
		WHERE b.id_wilayah = " . $data['id_master'] . "
		AND a.disposisi_poc = 1
		AND a.poc_approved = 1
		AND (a.tanggal_poc BETWEEN '" . $awalBulan . "' AND '" . $akhirBulan . "')";
		$result_po = $con->getResult($sql_po);

		foreach ($result_po as $rp) {
			// Total PO Customer
			$qty_poc = $rp['qty'];
			$vol_poc = $rp['volume_poc'];
			$nominal_poc = $rp['nominal'];

			if ($qty_poc > 0 || $vol_poc > 0 || $nominal_poc > 0) {
				$openDetailPO = "openDetail";
				$stylePO = "cursor:pointer;";
			} else {
				$openDetailPO = "";
				$stylePO = "";
			}

			$total_qty_poc += $rp['qty'];
			$total_vol_poc += $rp['volume_poc'];
			$total_nominal_poc += $rp['nominal'];
		}

		$sql_do = "SELECT COUNT(b.no_do_syop) as qty, SUM(b.volume) as volume, SUM(e.harga_dasar * b.volume) as nominal FROM pro_pr a
		JOIN pro_pr_detail b ON a.id_pr=b.id_pr
		JOIN pro_po_customer_plan c ON b.id_plan=c.id_plan
		JOIN pro_po_customer d ON c.id_poc=d.id_poc
		JOIN pro_penawaran e ON d.id_penawaran=e.id_penawaran
		WHERE a.id_wilayah = " . $data['id_master'] . " 
		AND a.disposisi_pr = 7
		AND b.is_approved = 1
		AND (a.tanggal_pr BETWEEN '" . $awalBulan . "' AND '" . $akhirBulan . "')";
		$result_do = $con->getResult($sql_do);

		foreach ($result_do as $rd) {
			// Total DO
			$qty_do = $rd['qty'];
			$vol_do = $rd['volume'];
			$nominal_do = $rd['nominal'];

			if ($qty_do > 0 || $vol_do > 0 || $nominal_do > 0) {
				$openDetailDO = "openDetail";
				$styleDO = "cursor:pointer;";
			} else {
				$openDetailDO = "";
				$styleDO = "";
			}

			$total_qty_do += $rd['qty'];
			$total_vol_do += $rd['volume'];
			$total_nominal_do += $rd['nominal'];
		}

		$sql_loaded = "SELECT COUNT(b.nomor_do) as qty, SUM(c.volume) as volume FROM pro_po_ds a
		JOIN pro_po_ds_detail b ON a.id_ds=b.id_ds
		JOIN pro_pr_detail c ON b.id_prd=c.id_prd
		JOIN pro_pr d ON c.id_pr=d.id_pr
		WHERE a.id_wilayah = " . $data['id_master'] . "
		AND a.is_submitted = 1
		AND (d.tanggal_pr BETWEEN '" . $awalBulan . "' AND '" . $akhirBulan . "')
		AND (b.is_loaded = 1 AND b.is_cancel = 0)";
		$result_loaded = $con->getResult($sql_loaded);

		foreach ($result_loaded as $rl) {
			// Total Loaded
			$qty_loaded = $rl['qty'];
			$vol_loaded = $rl['volume'];

			if ($qty_loaded > 0 || $vol_loaded > 0) {
				$openDetailLoaded = "openDetail";
				$styleLoaded = "cursor:pointer;";
			} else {
				$openDetailLoaded = "";
				$styleLoaded = "";
			}

			$total_qty_loaded += $rl['qty'];
			$total_vol_loaded += $rl['volume'];
		}

		$sql_delivered = "SELECT COUNT(b.nomor_do) as qty, SUM(c.volume) as volume FROM pro_po_ds a
		JOIN pro_po_ds_detail b ON a.id_ds=b.id_ds
		JOIN pro_pr_detail c ON b.id_prd=c.id_prd
		JOIN pro_pr d ON c.id_pr=d.id_pr
		WHERE a.id_wilayah = " . $data['id_master'] . "
		AND a.is_submitted = 1
		AND (d.tanggal_pr BETWEEN '" . $awalBulan . "' AND '" . $akhirBulan . "')
		AND b.is_delivered = 1";
		$result_delivered = $con->getResult($sql_delivered);

		foreach ($result_delivered as $rdv) {
			// Total Delivered
			$qty_delivered = $rdv['qty'];
			$vol_delivered = $rdv['volume'];

			if ($qty_delivered > 0 || $vol_delivered > 0) {
				$openDetailDelivered = "openDetail";
				$styleDelivered = "cursor:pointer;";
			} else {
				$openDetailDelivered = "";
				$styleDelivered = "";
			}

			$total_qty_delivered += $rdv['qty'];
			$total_vol_delivered += $rdv['volume'];
		}

		$sql_realisasi = "SELECT COUNT(b.nomor_do) as qty, SUM(c.volume) as volume FROM pro_po_ds a
		JOIN pro_po_ds_detail b ON a.id_ds=b.id_ds
		JOIN pro_pr_detail c ON b.id_prd=c.id_prd
		JOIN pro_pr d ON c.id_pr=d.id_pr
		WHERE a.id_wilayah = " . $data['id_master'] . "
		AND a.is_submitted = 1
		AND (d.tanggal_pr BETWEEN '" . $awalBulan . "' AND '" . $akhirBulan . "')
		AND b.realisasi_volume != 0";
		$result_realisasi = $con->getResult($sql_realisasi);

		foreach ($result_realisasi as $rr) {
			// Total Realisasi
			$qty_realisasi = $rr['qty'];
			$vol_realisasi = $rr['volume'];

			if ($qty_realisasi > 0 || $vol_realisasi > 0) {
				$openDetailRealisasi = "openDetail";
				$styleRealisasi = "cursor:pointer;";
			} else {
				$openDetailRealisasi = "";
				$styleRealisasi = "";
			}

			$total_qty_realisasi += $rr['qty'];
			$total_vol_realisasi += $rr['volume'];
		}

		$sql_invoice = "SELECT COUNT(a.id_invoice) as qty, SUM(a.total_invoice) as nominal
		FROM pro_invoice_admin a 
		JOIN pro_customer b ON a.id_customer = b.id_customer
		WHERE 1=1 AND b.id_wilayah = " . $data['id_master'] . " AND (a.tgl_invoice BETWEEN '" . $awalBulan . "' AND '" . $akhirBulan . "')";
		$result_invoice = $con->getResult($sql_invoice);

		foreach ($result_invoice as $ri) {
			// Total Invoice
			$qty_inv = $ri['qty'];
			$nominal_inv = $ri['nominal'];

			if ($qty_inv > 0 || $nominal_inv > 0) {
				$openDetailInvoice = "openDetail";
				$styleInvoice = "cursor:pointer;";
			} else {
				$openDetailInvoice = "";
				$styleInvoice = "";
			}

			$total_qty_inv += $ri['qty'];
			$total_nominal_inv += $ri['nominal'];
		}

		$sql_invoice_volume = "SELECT SUM(d.vol_kirim) as total_volume
		from pro_invoice_admin a 
		join pro_customer b on a.id_customer = b.id_customer
		join pro_invoice_admin_detail d ON a.id_invoice=d.id_invoice
		where 1=1 and b.id_wilayah= " . $data['id_master'] . " 
		and (a.tgl_invoice BETWEEN '" . $awalBulan . "' AND '" . $akhirBulan . "')
		AND a.jenis IN('all_in','harga_dasar','harga_dasar_oa','harga_dasar_pbbkb')";
		$result_invoice_volume = $con->getResult($sql_invoice_volume);

		foreach ($result_invoice_volume as $riv) {
			// Total Invoice
			$vol_inv = $riv['total_volume'];

			if ($vol_inv > 0) {
				$openDetailVolInv = "openDetail";
				$styleVolInv = "cursor:pointer;";
			} else {
				$openDetailVolInv = "";
				$styleVolInv = "";
			}

			$total_vol_inv += $riv['total_volume'];
		}


		$content .= '
				<tr>
					<td class="sticky">' . $data['nama_cabang'] . '</td>
					<td class="text-center ' . $openDetailPO . '" data-date-start="' . $awalBulan . '" data-date-end="' . $akhirBulan . '" data-cabang="' . $data['id_master'] . '" data-kategori="POC" style="' . $stylePO . '">' . number_format($qty_poc) . '</td>
					<td class="text-right">' . number_format($vol_poc) . '</td>
					<td class="text-right">' . number_format($nominal_poc) . '</td>
					<td class="text-center ' . $openDetailDO . '" data-date-start="' . $awalBulan . '" data-date-end="' . $akhirBulan . '" data-cabang="' . $data['id_master'] . '" data-kategori="DO" style="' . $styleDO . '">' . number_format($qty_do) . '</td>
					<td class="text-right">' . number_format($vol_do) . '</td>
					<td class="text-right">' . number_format($nominal_do) . '</td>
					<td class="text-center ' . $openDetailLoaded . '" data-date-start="' . $awalBulan . '" data-date-end="' . $akhirBulan . '" data-cabang="' . $data['id_master'] . '" data-kategori="Loaded" style="' . $styleLoaded . '">' . number_format($qty_loaded) . '</td>
					<td class="text-right">' . number_format($vol_loaded) . '</td>
					<td class="text-center ' . $openDetailDelivered . '" data-date-start="' . $awalBulan . '" data-date-end="' . $akhirBulan . '" data-cabang="' . $data['id_master'] . '" data-kategori="Delivered" style="' . $styleDelivered . '">' . number_format($qty_delivered) . '</td>
					<td class="text-right">' . number_format($vol_delivered) . '</td>
					<td class="text-center ' . $openDetailRealisasi . '" data-date-start="' . $awalBulan . '" data-date-end="' . $akhirBulan . '" data-cabang="' . $data['id_master'] . '" data-kategori="Realisasi" style="' . $styleRealisasi . '">' . number_format($qty_realisasi) . '</td>
					<td class="text-right">' . number_format($vol_realisasi) . '</td>
					<td class="text-center ' . $openDetailInvoice . '" data-date-start="' . $awalBulan . '" data-date-end="' . $akhirBulan . '" data-cabang="' . $data['id_master'] . '" data-kategori="Invoice" style="' . $styleInvoice . '">' . number_format($qty_inv) . '</td>
					<td class="text-right ' . $openDetailInvoice . '" data-date-start="' . $awalBulan . '" data-date-end="' . $akhirBulan . '" data-cabang="' . $data['id_master'] . '" data-kategori="Volume Invoice" style="' . $styleInvoice . '">' . number_format($vol_inv) . '</td>
					<td class="text-right">' . number_format($nominal_inv) . '</td>
				</tr>';
	}
	$content .= '
			<tr>
				<td class="text-center bg-gray sticky"><input type="hidden" id="uriExp" value="' . $link . '" /><b>TOTAL</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_qty_poc) . '</b></td>
				<td class="text-right bg-gray"><b>' . number_format($total_vol_poc) . '</b></td>
				<td class="text-right bg-gray"><b>' . number_format($total_nominal_poc) . '</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_qty_do) . '</b></td>
				<td class="text-right bg-gray"><b>' . number_format($total_vol_do) . '</b></td>
				<td class="text-right bg-gray"><b>' . number_format($total_nominal_do) . '</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_qty_loaded) . '</b></td>
				<td class="text-right bg-gray"><b>' . number_format($total_vol_loaded) . '</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_qty_delivered) . '</b></td>
				<td class="text-right bg-gray"><b>' . number_format($total_vol_delivered) . '</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_qty_realisasi) . '</b></td>
				<td class="text-right bg-gray"><b>' . number_format($total_vol_realisasi) . '</b></td>
				<td class="text-center bg-gray"><b>' . number_format($total_qty_inv) . '</b></td>
				<td class="text-right bg-gray"><b>' . number_format($total_vol_inv) . '</b></td>
				<td class="text-right bg-gray"><b>' . number_format($total_nominal_inv) . '</b></td>
			</tr>';
}

$json_data = array(
	"items"		=> $content,
);
//var_dump($json_data);exit;

echo json_encode($json_data);
// $volumeData = json_encode($result);
// unset($json_data);

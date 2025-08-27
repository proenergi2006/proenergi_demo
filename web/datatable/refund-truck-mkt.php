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
$q1	= isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';
$q2	= isset($_POST["q2"]) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : '';
$q3	= isset($_POST["q3"]) ? htmlspecialchars($_POST["q3"], ENT_QUOTES) : '';
$id_user = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);
$id_wilayah = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$id_role = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);

$arrTermPayment = array("CREDIT" => "CREDIT", "CBD" => "CBD (Cash Before Delivery)", "COD" => "COD (Cash On Delivery)");

if ($id_role == '18') {
	$filter = "and i.id_wilayah = '" . $id_wilayah . "'";
} else {
	$filter = "and i.id_marketing = '" . $id_user . "'";
}

$p = new paging;
$sql = "SELECT a.id_dsd as id_dsdnya, a.id_invoice as id_invoicenya, a.total_refund, a.disposisi, i.nama_customer, i.kode_pelanggan, i.jenis_payment, i.top_payment, i.id_customer as id_customernya, e.alamat_survey, f.nama_prov, g.nama_kab, j.fullname, h.nomor_poc, h.tanggal_poc, h.id_poc as id_pocnya, b.volume_po, k.refund_tawar, l.nama_area, m.wilayah_angkut, k.id_penawaran, ppdd.tanggal_delivered, n.no_invoice, n.tgl_invoice_dikirim, n.tgl_invoice, (SELECT SUM(vol_kirim) FROM pro_invoice_admin_detail WHERE id_invoice=a.id_invoice) as total_vol_invoice
from pro_refund a 
join pro_po_ds_detail ppdd on ppdd.id_dsd = a.id_dsd
join pro_po_detail b on ppdd.id_pod = b.id_pod
join pro_pr_detail c on ppdd.id_prd = c.id_prd 
join pro_po_customer_plan d on ppdd.id_plan = d.id_plan 
join pro_customer_lcr e on d.id_lcr = e.id_lcr
join pro_master_provinsi f on e.prov_survey = f.id_prov 
join pro_master_kabupaten g on e.kab_survey = g.id_kab
join pro_po_customer h on d.id_poc = h.id_poc 
join pro_customer i on h.id_customer = i.id_customer 
join acl_user j on i.id_marketing = j.id_user 
join pro_penawaran k on h.id_penawaran = k.id_penawaran	
join pro_master_area l on k.id_area = l.id_master 
join pro_master_wilayah_angkut m on e.id_wil_oa = m.id_master and e.prov_survey = m.id_prov and e.kab_survey = m.id_kab
join pro_invoice_admin n on a.id_invoice = n.id_invoice 
where k.refund_tawar != 0 " . $filter . " ";

// if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 6)
// 	$sql .= "  and ((j.id_role = 11 and i.id_group = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']) . "') or (j.id_role = 17 and j.id_om = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "'))";
// else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 7)
// 	$sql .= "  and i.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
// else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 10)
// 	$sql .= "  and i.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
// else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 11 || paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 17)
// 	$sql .= "  and i.id_marketing = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "'";
// else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 18) {
// 	if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']))
// 		$sql .= " and (i.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "' or i.id_marketing = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "')";
// 	else if (!paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']))
// 		$sql .= " and (i.id_group = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']) . "' or i.id_marketing = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "')";
// }

if ($q1 != "")
	$sql .= " and (upper(h.nomor_poc) like '" . strtoupper($q1) . "%' or upper(i.nama_customer) like '%" . strtoupper($q1) . "%' or upper(n.no_invoice) like '%" . strtoupper($q1) . "%')";
if ($q3 != "") {
	if ($q3 == "0") {
		$sql .= " and a.disposisi IN (0,1)";
	} elseif ($q3 == "1") {
		$sql .= " and a.disposisi = 2";
	} else {
		$sql .= " and a.disposisi = 3";
	}
}

$tot_record = $con->num_rows($sql);
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);
$sql .= "  order by a.id_refund desc limit " . $position . ", " . $length;

$content = "";
$count = 0;
if ($tot_record <= 0) {
	$content .= '<tr><td colspan="12" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
	$count 		= $position;
	$tot_page 	= ceil($tot_record / $length);
	$result 	= $con->getResult($sql);
	$refund = 0;
	$total_refund = 0;
	foreach ($result as $data) {
		$count++;
		$idp 		= $data["id_dsdnya"];
		$arrid_invoice = json_decode($data['id_invoicenya'], true);
		$linkList 	= paramEncrypt($data['id_dsd'] . "|#|1");
		$tempal 	= strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
		$alamat		= $data['alamat_survey'] . " " . ucwords($tempal) . " " . $data['nama_prov'];
		$total_refund = $data['refund_tawar'] * $data['total_vol_invoice'];
		$datenow = date("Y-m-d");
		$linkDetail	= BASE_URL_CLIENT . '/detail_refund.php?' . paramEncrypt('id_dsd=' . $idp . '&id_penawaran=' . $data['id_penawaran']);

		$tgl_invoice = $data['tgl_invoice'];
		$nomor_invoice = $data['no_invoice'];

		$sql_1 = "SELECT * FROM pro_invoice_admin WHERE id_invoice = '" . $data['id_invoicenya'] . "'";
		$row_1 = $con->getRecord($sql_1);
		if (($row_1['total_invoice'] == $row_1['total_bayar']) || $row_1['is_lunas'] == '1') {
			$sql_bayar_1 = "SELECT MAX(tgl_bayar) as tanggal_bayar FROM pro_invoice_admin_detail_bayar WHERE id_invoice='" . $data['id_invoicenya'] . "'";
			$row_bayar_1 = $con->getRecord($sql_bayar_1);
			$status_invoice_1 = "Lunas";
			$date_payment = tgl_indo($row_bayar_1['tanggal_bayar']);
		} else {
			$status_invoice_1 = "Not Yet";
		}

		if ($data['tgl_invoice_dikirim'] == NULL) {
			$tgl_invoice_dikirim = "-";
		} else {
			$tgl_invoice_dikirim = tgl_indo($data['tgl_invoice_dikirim']);
		}

		$due_date_indo = tgl_indo(date('Y-m-d', strtotime($data['tgl_invoice_dikirim'] . "+" . $data['top_payment'] . " days")));
		$due_date = date('Y-m-d', strtotime($data['tgl_invoice_dikirim'] . "+" . $data['top_payment'] . " days"));

		if ($data['disposisi'] == '1') {
			$linkCetak = ACTION_CLIENT . '/refund-cetak.php?' . paramEncrypt('id_dsd=' . $data['id_dsdnya'] . '&status=1');
			$btnCetakRefund = '<a target="_blank" href="' . $linkCetak . '" class="margin-sm btn btn-action btn-primary"><i class="fa fa-print"></i></a>';
		} elseif ($data['disposisi'] == '2') {
			$linkCetak = ACTION_CLIENT . '/refund-cetak.php?' . paramEncrypt('id_dsd=' . $data['id_dsdnya'] . '&status=2');
			$btnCetakRefund = '<a target="_blank" href="' . $linkCetak . '" class="margin-sm btn btn-action btn-primary"><i class="fa fa-print"></i></a>';
		} else {
			$btnCetakRefund = "";
		}

		$week1 = 0;
		$week2 = 0;
		$week3 = 0;
		$week4 = 0;
		$week5 = 0;
		$week6 = 0;
		$week7 = 0;
		if ($status_invoice_1 == "Lunas") {
			$date_payment = tgl_indo($row_bayar_1['tanggal_bayar']);
			$status_payment = "<div class='badge badge-success'>PAID</div>";

			$due_date_week2 = date('Y-m-d', strtotime($due_date . "+" . "14 days"));
			$due_date_week3 = date('Y-m-d', strtotime($due_date_week2 . "+" . "7 days"));
			$due_date_week4 = date('Y-m-d', strtotime($due_date_week3 . "+" . "10 days"));
			$due_date_week5 = date('Y-m-d', strtotime($due_date_week4 . "+" . "14 days"));
			$due_date_week6 = date('Y-m-d', strtotime($due_date_week5 . "+" . "15 days"));
			$due_date_week7 = date('Y-m-d', strtotime($due_date_week6 . "+" . "1 days"));

			if ($row_bayar_1['tanggal_bayar'] <= $due_date) {
				$week1 += ($total_refund * 100) / 100;
				$week2 += 0;
				$week3 += 0;
				$week4 += 0;
				$week5 += 0;
				$week6 += 0;
				$week7 += 0;
				$total_refund_fix = $week1;
			} elseif ($row_bayar_1['tanggal_bayar'] > $due_date && $row_bayar_1['tanggal_bayar'] <= $due_date_week2) {
				$week1 += 0;
				$week2 += ($total_refund * 95) / 100;
				$week3 += 0;
				$week4 += 0;
				$week5 += 0;
				$week6 += 0;
				$week7 += 0;
				$total_refund_fix = $week2;
			} elseif ($row_bayar_1['tanggal_bayar'] > $due_date_week2 && $row_bayar_1['tanggal_bayar'] <= $due_date_week3) {
				$week1 += 0;
				$week2 += 0;
				$week3 += ($total_refund * 85) / 100;
				$week4 += 0;
				$week5 += 0;
				$week6 += 0;
				$week7 += 0;
				$total_refund_fix = $week3;
			} elseif ($row_bayar_1['tanggal_bayar'] > $due_date_week3 && $row_bayar_1['tanggal_bayar'] <= $due_date_week4) {
				$week1 += 0;
				$week2 += 0;
				$week3 += 0;
				$week4 += ($total_refund * 75) / 100;
				$week5 += 0;
				$week6 += 0;
				$week7 += 0;
				$total_refund_fix = $week4;
			} elseif ($row_bayar_1['tanggal_bayar'] > $due_date_week4 && $row_bayar_1['tanggal_bayar'] <= $due_date_week5) {
				$week1 += 0;
				$week2 += 0;
				$week3 += 0;
				$week4 += 0;
				$week5 += ($total_refund * 65) / 100;
				$week6 += 0;
				$week7 += 0;
				$total_refund_fix = $week5;
			} elseif ($row_bayar_1['tanggal_bayar'] > $due_date_week5 && $row_bayar_1['tanggal_bayar'] <= $due_date_week6) {
				$week1 += 0;
				$week2 += 0;
				$week3 += 0;
				$week4 += 0;
				$week5 += 0;
				$week6 += ($total_refund * 50) / 100;
				$week7 += 0;
				$total_refund_fix = $week6;
			} elseif ($row_bayar_1['tanggal_bayar'] > $due_date_week6) {
				$week1 += 0;
				$week2 += 0;
				$week3 += 0;
				$week4 += 0;
				$week5 += 0;
				$week6 += 0;
				$week7 += ($total_refund * 0) / 100;
				$total_refund_fix = $week7;
			}

			if ($total_refund_fix == 0) {
				$status_refund = "HANGUS";
			} else {
				if ($data['disposisi'] == 2) {
					$status_refund = "PAID TO CUSTOMER";
					$background = "";
				} elseif ($data['disposisi'] == 3) {
					$week1 = 0;
					$week2 = 0;
					$week3 = 0;
					$week4 = 0;
					$week5 = 0;
					$week6 = 0;
					$week7 = 0;
					$total_refund_fix = 0;
					$status_refund = "CLOSED";
					$background = "";
				} else {
					$status_refund = "PROGRESS";
					$background = '';
				}
			}
		} else {
			$date_payment = "-";
			$status_payment = "<div class='badge badge-warning'>NOT YET</div>";

			$status_refund = "PROGRESS";
			$btnCetakRefund = "";
			$status_disposisi = "";

			$due_date_week2 = date('Y-m-d', strtotime($due_date . "+" . "14 days"));
			$due_date_week3 = date('Y-m-d', strtotime($due_date_week2 . "+" . "7 days"));
			$due_date_week4 = date('Y-m-d', strtotime($due_date_week3 . "+" . "10 days"));
			$due_date_week5 = date('Y-m-d', strtotime($due_date_week4 . "+" . "14 days"));
			$due_date_week6 = date('Y-m-d', strtotime($due_date_week5 . "+" . "15 days"));
			$due_date_week7 = date('Y-m-d', strtotime($due_date_week6 . "+" . "1 days"));

			if ($datenow <= $due_date) {
				$week1 += ($total_refund * 100) / 100;
				$week2 += 0;
				$week3 += 0;
				$week4 += 0;
				$week5 += 0;
				$week6 += 0;
				$week7 += 0;
				$total_refund_fix = $week1;
			} elseif ($datenow > $due_date && $datenow <= $due_date_week2) {
				$week1 += 0;
				$week2 += ($total_refund * 95) / 100;
				$week3 += 0;
				$week4 += 0;
				$week5 += 0;
				$week6 += 0;
				$week7 += 0;
				$total_refund_fix = $week2;
			} elseif ($datenow > $due_date_week2 && $datenow <= $due_date_week3) {
				$week1 += 0;
				$week2 += 0;
				$week3 += ($total_refund * 85) / 100;
				$week4 += 0;
				$week5 += 0;
				$week6 += 0;
				$week7 += 0;
				$total_refund_fix = $week3;
			} elseif ($datenow > $due_date_week3 && $datenow <= $due_date_week4) {
				$week1 += 0;
				$week2 += 0;
				$week3 += 0;
				$week4 += ($total_refund * 75) / 100;
				$week5 += 0;
				$week6 += 0;
				$week7 += 0;
				$total_refund_fix = $week4;
			} elseif ($datenow > $due_date_week4 && $datenow <= $due_date_week5) {
				$week1 += 0;
				$week2 += 0;
				$week3 += 0;
				$week4 += 0;
				$week5 += ($total_refund * 65) / 100;
				$week6 += 0;
				$week7 += 0;
				$total_refund_fix = $week5;
			} elseif ($datenow > $due_date_week5 && $datenow <= $due_date_week6) {
				$week1 += 0;
				$week2 += 0;
				$week3 += 0;
				$week4 += 0;
				$week5 += 0;
				$week6 += ($total_refund * 50) / 100;
				$week7 += 0;
				$total_refund_fix = $week6;
			} elseif ($datenow > $due_date_week6) {
				$week1 += 0;
				$week2 += 0;
				$week3 += 0;
				$week4 += 0;
				$week5 += 0;
				$week6 += 0;
				$week7 += ($total_refund * 0) / 100;
				$total_refund_fix = $week7;
			}

			if ($total_refund_fix == 0) {
				$status_refund = "HANGUS";
			} else {
				if ($data['disposisi'] == 2) {
					$status_refund = "PAID TO CUSTOMER";
					$background = "";
				} elseif ($data['disposisi'] == 3) {
					$week1 = 0;
					$week2 = 0;
					$week3 = 0;
					$week4 = 0;
					$week5 = 0;
					$week6 = 0;
					$week7 = 0;
					$total_refund_fix = 0;
					$status_refund = "CLOSED";
					$background = "";
				} else {
					$status_refund = "PROGRESS";
					$background = '';
				}
			}
		}

		$content .= '
				<tr>
					<td class="text-center">
					' . $count . '
					</td>
					<td class="text-center">
						<p style="margin-bottom:0px">' . $nomor_invoice . '</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>' . $data['kode_pelanggan'] . '</b></p>
						<p style="margin-bottom:0px">' . $data['nama_customer'] . '</p>
						<p style="margin-bottom:0px"><i>' . $data['fullname'] . '</i></p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>' . $data['nomor_poc'] . '</b></p>
						<p style="margin-bottom:0px">' . date("d/m/Y H:i", strtotime($data['tanggal_delivered'])) . '</p>
						<p style="margin-bottom:0px">' . number_format($data['total_vol_invoice']) . ' Liter</p>
					</td>
					<td class="text-center">
						<b>' . tgl_indo($tgl_invoice) . '</b>
					</td>
					<td class="text-left">
						<b> Invoice Send Date : ' . $tgl_invoice_dikirim . '</b>
						<br><br>
						<p>Payment : ' . $arrTermPayment[$data['jenis_payment']] . '</p>
						<p>TOP : ' . $data['top_payment'] . '</p>
						<p>Due Date : ' . $due_date_indo . '</p>
						<p>Date Payment : ' . $date_payment . '</p>
						<p><b>' . $status_payment . '</b></p>
					</td>
					<td class="text-right">' . number_format($data['refund_tawar']) . '</td>
					<td class="text-left">
						<table width="100%" border="0">
							<tr>
								<td width="65%">
									Due Date : ' . $due_date_indo . ' (100%)
								</td>
								<td width="2%">:</td>
								<td align="right">
									' . number_format($week1) . '
								</td>
							</tr>
							<tr>
								<td width="65%">
									(1-14) = ' . tgl_indo($due_date_week2) . ' (95%)
								</td>
								<td width="2%">:</td>
								<td align="right">
									' . number_format($week2) . '
								</td>
							</tr>
							<tr>
								<td>
									(15-21) = ' . tgl_indo($due_date_week3) . ' (85%)
								</td>
								<td>:</td>
								<td align="right">
									' . number_format($week3) . '
								</td>
							</tr>
							<tr>
								<td>
									(22-31) = ' . tgl_indo($due_date_week4) . ' (75%)
								</td>
								<td>:</td>
								<td align="right">
									' . number_format($week4) . '
								</td>
							</tr>
							<tr>
								<td>
									(31-45) = ' . tgl_indo($due_date_week5) . ' (65%)
								</td>
								<td>:</td>
								<td align="right">
									' . number_format($week5) . '
								</td>
							</tr>
							<tr>
								<td>
									(46-60) = ' . tgl_indo($due_date_week6) . ' (50%)
								</td>
								<td>:</td>
								<td align="right">
									' . number_format($week6) . '
								</td>
							</tr>
							<tr>
								<td>
									(61) = ' . tgl_indo($due_date_week7) . ' (0%)
								</td>
								<td>:</td>
								<td align="right">
									' . number_format($week7) . '
								</td>
							</tr>
						</table>
					</td>
					<td class="text-right">
					' . number_format($total_refund_fix) . '
					</td>
					<td class="text-right">
					' . number_format(($data['total_refund'])) . '
					</td>
					<td class="text-center">
						' . $status_refund . '
						<hr>
						' . $status_disposisi . '
					</td>
					<td class="text-center action">	
						<a href="' . $linkDetail . '" class="margin-sm btn btn-action btn-info"><i class="fa fa-info-circle"></i></a>
            		</td>
				</tr>';
		$refund += $data['refund_tawar'];
		$grandtotal_refund += $total_refund_fix;
	}
	$content .= '
				<tr>
					<td class="text-center" colspan="6"><b>Total</b></td>
					<td class="text-right">' . number_format($refund) . '</td>
					<td></td>
					<td class="text-right">' . number_format($grandtotal_refund) . '</td>
					<td class="text-left" colspan="2"></td>
            		</td>
				</tr>';
}

$json_data = array(
	"items"		=> $content,
	"pages"		=> $tot_page,
	"page"		=> $page,
	"totalData"	=> $tot_record,
	"infoData"	=> "Showing " . ($position + 1) . " to " . $count . " of " . $tot_record . " entries",
);
echo json_encode($json_data);

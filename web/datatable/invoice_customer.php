
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
$cabang	= isset($_POST["cabang"]) ? htmlspecialchars($_POST["cabang"], ENT_QUOTES) : '';

$arrTermPayment = array("CREDIT" => "CREDIT", "CBD" => "CBD (Cash Before Delivery)", "COD" => "COD (Cash On Delivery)");

$linkExport = BASE_URL_CLIENT . '/report/invoice-exp.php?' . paramEncrypt('q1=' . $q1 . '&q2=' . $q2 . '&q3=' . $q3 . '&cabang=' . $cabang);

$sess_wil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

if ($sesrol == '25') {
	if ($cabang) {
		$filter_cabang = " and b.id_wilayah = '" . $cabang . "'";
	} else {
		$filter_cabang = "";
	}
} else {
	$filter_cabang = " and b.id_wilayah = '" . $sess_wil . "'";
}

$p = new paging;
$sql = "SELECT a.*, b.nama_customer, b.jenis_payment, b.top_payment, c.nama_cabang, d.fullname as nama_marketing
		from pro_invoice_admin a 
		join pro_customer b on a.id_customer = b.id_customer 
		join pro_master_cabang c on b.id_wilayah = c.id_master
		join acl_user d on b.id_marketing = d.id_user
		where 1=1 " . $filter_cabang . "";

if ($q1 != "")
	$sql .= " and (upper(b.nama_customer) like '%" . strtoupper($q1) . "%' or upper(a.no_invoice) like '%" . strtoupper($q1) . "%')";

if ($q2 != "" && $q3 != "") {
	$sql .= " and (DATE(a.tgl_invoice) between '" . tgl_db($q2) . "' and '" . tgl_db($q3) . "')";
} else {
	if ($q2 != "") $sql .= " and (DATE(a.tgl_invoice) = '" . tgl_db($q2) . "')";
	if ($q3 != "") $sql .= " and (DATE(a.tgl_invoice) = '" . tgl_db($q3) . "')";
}

$tot_record = $con->num_rows($sql);
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);
$sql .= " order by a.tgl_invoice desc limit " . $position . ", " . $length;

$content = "";

$count = 0;
if ($tot_record == 0) {
	$content .= '<tr><td colspan="9" style="text-align:center"><input type="hidden" id="uriExp" value="' . $linkExport . '" />Data tidak ditemukan </td></tr>';
} else {
	$count 		= $position;
	$tot_page 	= ceil($tot_record / $length);
	$result 	= $con->getResult($sql);
	foreach ($result as $data) {
		$sql02 	= "SELECT 
					a.*, 
					d.harga_dasar, 
					d.detail_rincian, 
					d.pembulatan, 
					d.refund_tawar, 
					d.id_penawaran,
					'truck' AS jenisnya
				FROM 
					pro_invoice_admin_detail a
				JOIN 
					pro_po_ds_detail b ON a.id_dsd = b.id_dsd
				JOIN 
					pro_po_customer c ON b.id_poc = c.id_poc
				JOIN 
					pro_penawaran d ON c.id_penawaran = d.id_penawaran
				WHERE 
					a.id_invoice = '" . $data['id_invoice'] . "' 
					AND a.jenisnya = 'truck'

				UNION ALL

				SELECT 
					a.*, 
					d.harga_dasar, 
					d.detail_rincian, 
					d.pembulatan, 
					d.refund_tawar, 
					d.id_penawaran,
					'kapal' AS jenisnya
				FROM 
					pro_invoice_admin_detail a
				JOIN 
					pro_po_ds_kapal e ON a.id_dsd = e.id_dsk
				JOIN 
					pro_po_customer c ON e.id_poc = c.id_poc
				JOIN 
					pro_penawaran d ON c.id_penawaran = d.id_penawaran
				WHERE 
					a.id_invoice = '" . $data['id_invoice'] . "' 
					AND a.jenisnya = 'kapal'
				LIMIT 1";

		$result02 	= $con->getRecord($sql02);
		$decode = json_decode($result02['detail_rincian'], true);
		$jenis  = "";
		$total_volume = 0;

		if ($result02['pembulatan'] == 2) {
			$harga_kirim = number_format($result02['harga_kirim'], 4);
			$total_invoice = number_format($data['total_invoice'], 0);
		} elseif ($result02['pembulatan'] == 0) {
			$harga_kirim = number_format($result02['harga_kirim'], 2);
			$total_invoice = number_format($data['total_invoice'], 0);
		} else {
			$harga_kirim = number_format($result02['harga_kirim'], 0);
			$total_invoice = number_format($data['total_invoice'], 0);
		}

		if ($data['jenis'] == "all_in" || $data['jenis'] == "harga_dasar" || $data['jenis'] == "harga_dasar_oa") {
			$refund = "Refund : " . $result02['refund_tawar'];
		} else {
			$refund = "";
		}

		foreach ($decode as $arr1) {
			if ($data['jenis'] == "all_in") {
				$nilai = $arr1['nilai'];
				$biaya = ($arr1['biaya']) ? $arr1['biaya'] : 0;
				if ($arr1['rincian'] == "PPN") {
					if (fmod($biaya, 1) !== 0.0000) {
						$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya, 4, ".", ",") . "</p>";
					} else {
						$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya) . "</p>";
					}
				} else {
					if (fmod($biaya, 1) !== 0.0000) {
						$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya, 4, ".", ",") . "</p>";
					} else {
						$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya) . "</p>";
					}
				}
			} elseif ($data['jenis'] == "split_oa") {
				if ($arr1['rincian'] == "Ongkos Angkut" || $arr1['rincian'] == "PPN") {
					$nilai = $arr1['nilai'];
					$biaya = ($arr1['biaya']) ? $arr1['biaya'] : 0;
					if ($arr1['rincian'] == "PPN") {
						$total_oa_ppn = $ongkos_angkut_penawaran * $nilai_ppn / 100;
						if (fmod($total_oa_ppn, 1) !== 0.0000) {
							$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($total_oa_ppn, 4, ".", ",") . "</p>";
						} else {
							$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($total_oa_ppn) . "</p>";
						}
					} else {
						if (fmod($biaya, 1) !== 0.0000) {
							$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya, 4, ".", ",") . "</p>";
						} else {
							$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya) . "</p>";
						}
					}
				}
			} elseif ($data['jenis'] == "harga_dasar") {
				if ($arr1['rincian'] == "Harga Dasar" || $arr1['rincian'] == "PPN") {
					$nilai = $arr1['nilai'];
					$biaya = ($arr1['biaya']) ? $arr1['biaya'] : 0;
					if ($arr1['rincian'] == "PPN") {
						$total_hsd_ppn = $harga_dasar_penawaran * $nilai_ppn / 100;
						if (fmod($total_hsd_ppn, 1) !== 0.0000) {
							$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($total_hsd_ppn, 4, ".", ",") . "</p>";
						} else {
							$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($total_hsd_ppn) . "</p>";
						}
					} else {
						if (fmod($biaya, 1) !== 0.0000) {
							$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya, 4, ".", ",") . "</p>";
						} else {
							$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya) . "</p>";
						}
					}
				}
			} elseif ($data['jenis'] == "split_pbbkb") {
				if ($arr1['rincian'] == "PBBKB") {
					$nilai = $arr1['nilai'];
					$biaya = ($arr1['biaya']) ? $arr1['biaya'] : 0;
					if (fmod($biaya, 1) !== 0.0000) {
						$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya, 4, ".", ",") . "</p>";
					} else {
						$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya) . "</p>";
					}
				}
			} elseif ($data['jenis'] == "harga_dasar_oa") {
				if ($arr1['rincian'] == "Harga Dasar" || $arr1['rincian'] == "Ongkos Angkut" || $arr1['rincian'] == "PPN") {
					$nilai = $arr1['nilai'];
					$biaya = ($arr1['biaya']) ? $arr1['biaya'] : 0;
					if ($arr1['rincian'] == "PPN") {
						$total_hsd_oa_ppn = ($harga_dasar_penawaran + $ongkos_angkut_penawaran) * $nilai_ppn / 100;
						if (fmod($total_hsd_oa_ppn, 1) !== 0.0000) {
							$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($total_hsd_oa_ppn, 4, ".", ",") . "</p>";
						} else {
							$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($total_hsd_oa_ppn) . "</p>";
						}
					} else {
						if (fmod($biaya, 1) !== 0.0000) {
							$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya, 4, ".", ",") . "</p>";
						} else {
							$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya) . "</p>";
						}
					}
				}
			} elseif ($data['jenis'] == "harga_dasar_pbbkb") {
				if ($arr1['rincian'] == "Harga Dasar" || $arr1['rincian'] == "PBBKB" || $arr1['rincian'] == "PPN") {
					$biaya = ($arr1['biaya']) ? $arr1['biaya'] : 0;
					if ($arr1['rincian'] == "PPN") {
						$total_hsd_ppn = ($harga_dasar_penawaran + $pbbkb_penawaran)  * $nilai_ppn / 100;
						if (fmod($total_hsd_ppn, 1) !== 0.0000) {
							$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($total_hsd_ppn, 4, ".", ",") . "</p>";
						} else {
							$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($total_hsd_ppn) . "</p>";
						}
					} else {
						if (fmod($biaya, 1) !== 0.0000) {
							$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya, 4, ".", ",") . "</p>";
						} else {
							$jenis .= "<p>" . $arr1['rincian'] . " : " . number_format($biaya) . "</p>";
						}
					}
				}
			}
		}
		$sql_volume = "SELECT * FROM pro_invoice_admin_detail WHERE id_invoice='" . $data['id_invoice'] . "'";
		$res_volume = $con->getResult($sql_volume);
		$total_volume = 0;
		foreach ($res_volume as $rv) {
			$total_volume += $rv['vol_kirim'];
		}

		$count++;
		$linkDetail	= BASE_URL_CLIENT . '/invoice_customer_detail.php?' . paramEncrypt('idr=' . $data['id_invoice']);
		$linkEdit	= BASE_URL_CLIENT . '/invoice_customer_add.php?' . paramEncrypt('idr=' . $data['id_invoice']);
		$linkHapus	= paramEncrypt("invoice_customer#|#" . $data['id_invoice']);

		if ($data['tgl_invoice_dikirim'] == NULL) {
			if (($data['tgl_invoice'] >= '2024-04-01' && $data['total_bayar'] != $data['total_invoice'] && $data['is_lunas'] == NULL) && $data['tgl_invoice'] < '2024-10-07' && $data['total_bayar'] != $data['total_invoice'] && $data['is_lunas'] == NULL) {
				$linkBayar	= BASE_URL_CLIENT . '/invoice_customer_bayar.php?' . paramEncrypt('idr=' . $data['id_invoice']);
				if ($sesrol == '25') {
					$btnBayar = "";
				} else {
					$btnBayar = '<a target="_blank" class="margin-sm btn btn-action btn-success" title="Pembayaran" href="' . $linkBayar . '"><i class="fas fa-file-invoice"></i></a>';
				}
			} else {
				$linkBayar = "";
				$btnBayar = '';
			}
		} else {
			if ($data['total_bayar'] != $data['total_invoice']) {
				if ($data['is_lunas'] != 1) {
					$linkBayar	= BASE_URL_CLIENT . '/invoice_customer_bayar.php?' . paramEncrypt('idr=' . $data['id_invoice']);
					if ($sesrol == '25') {
						$btnBayar = "";
					} else {
						$btnBayar = '<a target="_blank" class="margin-sm btn btn-action btn-success" title="Pembayaran" href="' . $linkBayar . '"><i class="fas fa-file-invoice"></i></a>';
					}
				}
			} else {
				$linkBayar = "";
				$btnBayar = '';
			}
		}
		// $btnTglInvoice = "<button class='btn btn-primary btn-sm tgl_invoice' title='Tanggal invoice dikirim' data-id='" . paramEncrypt($data['id_invoice']) . "' data-refund='" . paramEncrypt($result02['refund_tawar']) . "'><i class='fas fa-calendar-check'></i></button>";
		$tgl_invoice_dikirim = $data['tgl_invoice_dikirim'];
		if ($tgl_invoice_dikirim != NULL) {
			$btnTglInvoice = "";
		} else {
			if ($data['tgl_invoice'] >= '2024-10-07') {
				if ($sesrol == '25') {
					$btnTglInvoice = "-";
				} else {
					$btnTglInvoice = "<button class='btn btn-primary btn-sm tgl_invoice' title='Tanggal invoice dikirim' data-id='" . paramEncrypt($data['id_invoice']) . "' data-refund='" . paramEncrypt($result02['refund_tawar']) . "'><i class='fas fa-calendar-check'></i></button>";
				}
			} else {
				$btnTglInvoice = "-";
			}
		}

		if ($data['tgl_invoice_dikirim'] != NULL) {
			$due_date = 'Due Date : ' . tgl_indo(date('Y-m-d', strtotime($data['tgl_invoice_dikirim'] . "+" . $data['top_payment'] . " days")));
		} else {
			$due_date = "Due Date : -";
		}

		if ($data['is_lunas'] == 1 || ($data['total_bayar'] == $data['total_invoice'])) {
			$btnHapus 	= '';

			$status_lunas = '<div class="text-center" style="background-color:RGBA(0, 209, 70); padding:2px; border-radius: 25px; color:white;">Lunas</div>';
			$btn_detail_pembayaran = "<span class='detail_pembayaran' data-param='" . $data['id_invoice'] . "' style='color:blue; cursor:pointer;'>Detail Pembayaran</span>";
		} else {
			$bupot = "";
			if (!$data['total_bayar'] || $data['total_bayar'] == 0) {
				if ($sesrol == '25') {
					$btnHapus = "";
				} else {
					if ($data['tgl_invoice_dikirim'] != null) {
						$btnHapus = "";
					} else {
						$btnHapus = '<a class="margin-sm btn btn-action btn-danger " title="Delete" data-param-idx="' . $linkHapus . '" data-action="deleteGrid"><i class="fa fa-trash"></i></a>';
					}
				}

				$status_lunas = '';
				$btn_detail_pembayaran = "";
			} else {
				$btnHapus 	= '';
				$status_lunas = '<div class="text-center" style="background-color:red; padding:2px; border-radius: 25px; color:white;">Not Yet</div>';
				$btn_detail_pembayaran = "<span class='detail_pembayaran' data-param='" . $data['id_invoice'] . "' style='color:blue; cursor:pointer;'>Detail Pembayaran</span>";
			}
		}

		if ($sesrol == '25') {
			$class = "hide";
		} else {
			$class = "";
		}


		// if ($data['jenis'] == 'all_in' || $data['jenis'] == 'harga_dasar' || $data['jenis'] == 'harga_dasar_oa' || $data['jenis'] == 'harga_dasar_pbbkb') {
		// 	if (!$data['total_bayar'] || $data['total_bayar'] == 0) {
		// 		$btnHapus = '<a class="margin-sm btn btn-action btn-danger " title="Delete" data-param-idx="' . $linkHapus . '" data-action="deleteGrid"><i class="fa fa-trash"></i></a>';
		// 	} else {
		// 		$btnHapus 	= '<a class="margin-sm btn btn-action btn-danger disabled" title="Delete"><i class="fa fa-trash"></i></a>';
		// 	}
		// } else {
		// 	$btnHapus = "";
		// }

		if ($data['jenis'] == "split_pbbkb") {
			$linkCetak = ACTION_CLIENT . '/invoice-customer-cetak.php?' . paramEncrypt('idr=' . $data['id_invoice'] . '&tipe=split_pbbkb');
		} elseif ($data['jenis'] == "split_oa") {
			$linkCetak = ACTION_CLIENT . '/invoice-customer-cetak.php?' . paramEncrypt('idr=' . $data['id_invoice'] . '&tipe=split_oa');
		} elseif ($data['jenis'] == "harga_dasar") {
			$linkCetak = ACTION_CLIENT . '/invoice-customer-cetak.php?' . paramEncrypt('idr=' . $data['id_invoice'] . '&tipe=harga_dasar');
		} elseif ($data['jenis'] == "harga_dasar_oa") {
			$linkCetak = ACTION_CLIENT . '/invoice-customer-cetak.php?' . paramEncrypt('idr=' . $data['id_invoice'] . '&tipe=harga_dasar_oa');
		} elseif ($data['jenis'] == "harga_dasar_pbbkb") {
			$linkCetak = ACTION_CLIENT . '/invoice-customer-cetak.php?' . paramEncrypt('idr=' . $data['id_invoice'] . '&tipe=harga_dasar_pbbkb');
		} else {
			$linkCetak = ACTION_CLIENT . '/invoice-customer-cetak.php?' . paramEncrypt('idr=' . $data['id_invoice'] . '&tipe=default');
			$linkCetak_pbbkb = ACTION_CLIENT . '/invoice-customer-cetak.php?' . paramEncrypt('idr=' . $data['id_invoice'] . '&tipe=pbbkb');
		}

		if ($data['jenis'] == "all_in") {
			$btnCetak = '<div class="btn-group text-left">
			<button type="button" class="btn btn-primary btn-sm"><i class="fas fa-print"></i></button>
			<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
				<span class="caret"></span>
				<span class="sr-only">Toggle Dropdown</span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<li>
					<a target="_blank" href="' . $linkCetak . '">Default</a>
				</li>
				<li>
					<a target="_blank" href="' . $linkCetak_pbbkb . '">Pisah PBBKB</a>
				</li>
			</ul>
		</div>';
		} else {
			$btnCetak = '<a target="_blank" class="btn btn-primary btn-sm" href="' . $linkCetak . '">Cetak</a>';
		}

		$content .= '
			<tr class="clickable-row" data-href="' . $linkDetail . '" ' . $background . '>
				<td class="text-center">' . $count . '<input type="hidden" id="uriExp" value="' . $linkExport . '" /></td>
				<td>
				' . $data['nama_customer'] . '
				<br><br>
				<small><i>' . $data['nama_marketing'] . '</i></small>
				</td>
				<td>' . $data['no_invoice'] . '</td>
				<td class="text-left">
					' . tgl_indo($data['tgl_invoice']) . '
					<br><br>
					<p>Payment : ' . $arrTermPayment[$data['jenis_payment']] . '</p>
					<p>TOP : ' . $data['top_payment'] . '</p>
					<p>' . $due_date . '</p>
				</td>
				<td class="text-center">
					' . tgl_indo($tgl_invoice_dikirim) . '
					<br><br>
					' . $btnTglInvoice . '
				</td>
				<td class="text-right" nowrap>
					<p><b>' . $harga_kirim . '</b></p>
					<p>' . $jenis . '</p>
					<p>' . $refund . '</p>
				</td>
				<td class="text-right">' . number_format($total_volume) . '</td>
				<td class="text-right">' . $total_invoice . '</td>
				<td class="text-right">
				' . number_format($data['total_bayar'], 0) . '
				<br>
				<br>
				' . $status_lunas . '
				<br>
				' . $btn_detail_pembayaran . '
				</td>
				<td class="text-center action">
					' . $btnCetak . '
					<a class="margin-sm btn btn-action btn-info ' . $class . '" title="Ubah Data" href="' . $linkEdit . '"><i class="fa fa-edit"></i></a>
					' . $btnHapus . '
					' . $btnBayar . '
				</td>
			</tr>';
	}
}

// button detail invoice
// <a class="margin-sm btn btn-action btn-info" title="Detail Data" href="' . $linkDetail . '"><i class="fa fa-info-circle"></i></a>

$json_data = array(
	"items"		=> $content,
	"pages"		=> $tot_page,
	"page"		=> $page,
	"totalData"	=> $tot_record,
	"infoData"	=> "Showing " . ($position + 1) . " - " . $count . " of " . $tot_record . " entries",
);
echo json_encode($json_data);

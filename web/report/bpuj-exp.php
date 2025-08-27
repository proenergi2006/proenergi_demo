<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
require_once($public_base_directory . "/libraries/helper/class.xlsxwriter.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;
$enk  	= decode($_SERVER['REQUEST_URI']);
$sheet 	= 'Sheet1';
$sess_wil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

$q1	= htmlspecialchars($enk["q1"], ENT_QUOTES);
$q2	= htmlspecialchars($enk["q2"], ENT_QUOTES);
$q3	= htmlspecialchars($enk["q3"], ENT_QUOTES);
$q4	= htmlspecialchars($enk["q4"], ENT_QUOTES);
$q5	= htmlspecialchars($enk["q5"], ENT_QUOTES);
$id_user = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);

$sql_user = "SELECT a.*, b.role_name FROM acl_user a JOIN acl_role b ON a.id_role=b.id_role WHERE a.id_user='" . $id_user . "'";
$row_user = $con->getRecord($sql_user);

$sql_wilayah = "SELECT nama_cabang FROM pro_master_cabang WHERE id_master='" . $sess_wil . "'";
$row_wilayah = $con->getRecord($sql_wilayah);
// echo json_encode($q5);

if ($row_user['id_wilayah'] == 2) {
	$adh = "Eka Riyanti";
} elseif ($row_user['id_wilayah'] == 3) {
	$adh = "Fitri Ayu Ningsih";
} elseif ($row_user['id_wilayah'] == 4) {
	$adh = "Putri Pangestu Wibowo";
} elseif ($row_user['id_wilayah'] == 5) {
	$adh = "Erika Yulyari Nova";
} elseif ($row_user['id_wilayah'] == 6) {
	$adh = "Diah";
} elseif ($row_user['id_wilayah'] == 7) {
	$adh = "Erika Yulyari Nova";
}

$sql = "SELECT a.*, b.nomor_do, c.nomor_ds, d.id_customer, e.total_realisasi, f.no_spj, f.trip_po, g.alamat_customer, CONCAT(i.nama_terminal, ' ', i.tanki_terminal) as depo, e.disposisi_realisasi FROM pro_bpuj a JOIN pro_po_ds_detail b ON a.id_dsd=b.id_dsd JOIN pro_po_ds c ON b.id_ds=c.id_ds JOIN pro_po_customer d ON b.id_poc=d.id_poc LEFT JOIN pro_bpuj_realisasi e ON a.id_bpuj=e.id_bpuj JOIN pro_po_detail f ON b.id_pod=f.id_pod JOIN pro_customer g ON d.id_customer=g.id_customer JOIN pro_pr_detail h ON b.id_prd=h.id_prd JOIN pro_master_terminal i ON h.pr_terminal=i.id_master WHERE a.cabang='" . $sess_wil . "' AND a.diberikan_oleh IS NOT NULL AND a.is_active='1'";

if ($q1 != "")
	$sql .= " and (upper(b.nomor_do) like '%" . strtoupper($q1) . "%' or upper(a.nomor_bpuj) like '%" . strtoupper($q1) . "%' or upper(a.nama_driver) like '%" . strtoupper($q1) . "%' or upper(a.no_unit) like '%" . strtoupper($q1) . "%')";

if ($q2 != "" && $q3 != "") {
	$sql .= " and (DATE(a.tanggal_bpuj) between '" . tgl_db($q2) . "' and '" . tgl_db($q3) . "')";
} else {
	if ($q2 != "") $sql .= " and (DATE(a.tanggal_bpuj) = '" . tgl_db($q2) . "')";
	if ($q3 != "") $sql .= " and (DATE(a.tanggal_bpuj) = '" . tgl_db($q3) . "')";
}

if ($q4 != "")
	$sql .= " and a.disposisi_bpuj = '" . $q4 . "'";
if ($q5 != "") {
	if ($q5 == "NULL") {
		$sql .= " and e.disposisi_realisasi IS NULL ";
	} else {
		$sql .= " and e.disposisi_realisasi = '" . $q5 . "'";
	}
}

$sql .= "order by a.tanggal_bpuj ASC";
$res = $con->getResult($sql);

// echo json_encode($sql);


$filename 	= "Rekapitulasi BPUJ-" . date('dmYHis') . ".xlsx";
$arrOp 		= array(1 => "=", ">=", "<=");
header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
ob_end_clean();
header('Content-type: application/vnd.ms-excel');
// header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');

$writer = new XLSXWriter();
$writer->writeSheetHeader($sheet, array('REKAPITULASI LAPORAN PEMBERIAN UANG JALAN PT. PRO ENERGI' => 'string'));
$writer->newMergeCell($sheet, "A1", "AT1");
$start = 2;
$patok = 1;

$writer->writeSheetHeaderExt($sheet, array("Cabang : " . $row_wilayah['nama_cabang'] => "string"));
$writer->newMergeCell($sheet, "A" . $start, "AT" . $start);
$patok++;
$start++;

if ($q1) {
	$writer->writeSheetHeaderExt($sheet, array("Keywords : " . $q1 => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "AT" . $start);
	$patok++;
	$start++;
}
if ($q2 != "" && $q3 != "") {
	$writer->writeSheetHeaderExt($sheet, array("Periode BPUJ : " . $q2 . " s/d " . $q3 => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "AT" . $start);
	$patok++;
	$start++;
} elseif ($q2 != "" && $q3 == "") {
	$writer->writeSheetHeaderExt($sheet, array("Periode BPUJ : " . $q2  => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "AT" . $start);
	$patok++;
	$start++;
} elseif ($q2 == "" && $q3 != "") {
	$writer->writeSheetHeaderExt($sheet, array("Periode BPUJ : " . $q3  => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "AT" . $start);
	$patok++;
	$start++;
} else {
	$writer->writeSheetHeaderExt($sheet, array("Periode BPUJ : ALL" => "string"));
	$writer->newMergeCell($sheet, "A" . $start, "AT" . $start);
	$patok++;
	$start++;
}

$writer->writeSheetHeaderExt($sheet, array("" => "string"));
$patok++;
$start++;
$writer->setColumnIndex($patok);

$header = array(
	"No" => 'string',
	"Tanggal Kirim BPUJ" => 'string',
	"Nomor BPUJ" => 'string',
	"Nama Driver" => 'string',
	"Status Driver" => 'string',
	"No. Unit" => 'string',
	"Depo" => 'string',
	"Nomor Surat Jalan" => 'string',
	"Customer" => 'string',
	"Alamat" => 'string',
	"Kapasitas" => 'string',
	"Rit" => 'string',
	"Pengisian BBM" => 'string',
	"Fuel-Qty" => 'int',
	"Tanggal Pengisian" => 'int',
	"Pengisian BBM Tambahan" => 'string',
	"Fuel-Qty Tambahan" => 'int',
	"Tanggal Pengisian Tambahan" => 'int',
	"Pengisian BBM Tambahan 2" => 'string',
	"Fuel-Qty Tambahan 2" => 'int',
	"Tanggal Pengisian Tambahan 2" => 'int',
	"Jarak" => 'string',
	"Uang Jasa/Km" => 'int',
	"Insentif" => 'int',
	"Uang BBM" => 'int',
	"Uang Makan + Parkir + Meal" => 'int',
	"Uang Kernet" => 'int',
	"Uang Makan + Parkir + Meal Hari ke 2" => 'int',
	"Uang Kernet Hari ke 2" => 'int',
	"Biaya Perjalanan Hari ke 2" => 'int',
	"Uang Makan + Parkir + Meal Hari ke 3" => 'int',
	"Uang Kernet Hari ke 3" => 'int',
	"Biaya Perjalanan Hari ke 3" => 'int',
	"Uang Makan + Parkir + Meal Hari ke 4" => 'int',
	"Uang Kernet Hari ke 4" => 'int',
	"Biaya Perjalanan Hari ke 4" => 'int',
	"Uang Makan + Parkir + Meal Hari ke 5" => 'int',
	"Uang Kernet Hari ke 5" => 'int',
	"Biaya Perjalanan Hari ke 5" => 'int',
	"Uang Tol" => 'int',
	"Uang Demmurade" => 'int',
	"Uang Koordinasi" => 'int',
	"Uang Multidrop" => 'int',
	"Uang Penyebrangan" => 'int',
	"Biaya Lain" => 'int',
	"Total UJ" => 'int',
);
$writer->writeSheetHeaderExt($sheet, $header);
$start++;

if (count($res) > 0) {
	$tot1 = 0;
	$last = $start - 1;
	$grand_total_bpuj = 0;
	foreach ($res as $data) {
		$last++;
		$tot1++;

		// REALISASI BPUJ
		$query_realisasi = "SELECT * FROM pro_bpuj_realisasi WHERE id_bpuj='" . $data['id_bpuj'] . "'";
		$realisasi = $con->getRecord($query_realisasi);

		if ($realisasi) {
			// if (fmod($realisasi['liter_bbm'], 1) !== 0.000) {
			// 	$liter_bbm = number_format($realisasi['liter_bbm'], 3, ",", ".");
			// } else {
			// 	$liter_bbm = number_format($realisasi['liter_bbm']);
			// }
			$liter_bbm = $realisasi['liter_bbm'];
			$nama_driver = $realisasi['nama_driver'];
			$status_driver = $realisasi['status_driver'];
			$no_unit = $realisasi['no_unit'];

			if ($realisasi['tgl_pengisian'] != NULL) {
				$tgl_pengisian = date("d-m-Y", strtotime($realisasi['tgl_pengisian']));
			} else {
				$tgl_pengisian = "";
			}

			if ($realisasi['tgl_pengisian_tambahan'] != NULL) {
				$tgl_pengisian_tambahan = date("d-m-Y", strtotime($realisasi['tgl_pengisian_tambahan']));
			} else {
				$tgl_pengisian_tambahan = "";
			}

			if ($realisasi['tgl_pengisian_tambahan2'] != NULL) {
				$tgl_pengisian_tambahan3 = date("d-m-Y", strtotime($realisasi['tgl_pengisian_tambahan2']));
			} else {
				$tgl_pengisian_tambahan3 = "";
			}

			// if (fmod($realisasi['liter_bbm_tambahan'], 1) !== 0.000) {
			// 	$liter_bbm_tambahan = number_format($realisasi['liter_bbm_tambahan'], 3, ",", ".") . ' Liter';
			// } else {
			// 	$liter_bbm_tambahan = number_format($realisasi['liter_bbm_tambahan']) . ' Liter';
			// }
			$liter_bbm_tambahan = $realisasi['liter_bbm_tambahan'];
			$liter_bbm_tambahan3 = $realisasi['liter_bbm_tambahan2'];

			$jarak = $realisasi['jarak_real'] . ' KM';
			$total_jasa = $realisasi['total_jasa'];
			$total_bbm = $realisasi['total_bbm'];
			$uang_tol = $realisasi['uang_tol'];
			$uang_demmurade = $realisasi['uang_demmurade'];
			$uang_koordinasi = $realisasi['uang_koordinasi'];
			$biaya_penyebrangan = $realisasi['biaya_penyebrangan'];
			$biaya_lain = $realisasi['biaya_lain'];
			$total_bpuj = $realisasi['total_realisasi'];
			$grand_total_bpuj += $realisasi['total_realisasi'];

			// CUT OFF METODE APPROVAL REALISASI
			if ($realisasi['tanggal_realisasi'] > '2024-06-05' && $realisasi['disposisi_realisasi'] == 1) {
				// TAMBAHAN HARI REALISASI
				$query = "SELECT * FROM pro_bpuj_realisasi_tambahan_hari WHERE id_realisasi='" . $realisasi['id'] . "'";
				$tambahan_hari = $con->getResult($query);

				$biaya_perjalanan = 0;
				$uang_makan2 = 0;
				$uang_kernet2 = 0;
				$biaya_perjalanan2 = 0;
				$uang_makan3 = 0;
				$uang_kernet3 = 0;
				$biaya_perjalanan3 = 0;
				$uang_makan4 = 0;
				$uang_kernet4 = 0;
				$biaya_perjalanan4 = 0;
				$uang_makan5 = 0;
				$uang_kernet5 = 0;
				$biaya_perjalanan5 = 0;
				if ($tambahan_hari != NULL || $tambahan_hari != "") {
					foreach ($tambahan_hari as $key) {
						if (count($tambahan_hari) == 1) {
							$uang_makan2 = $key['uang_makan'];
							$uang_kernet2 = $key['uang_kernet'];
							$biaya_perjalanan2 = $key['biaya_perjalanan'];
						} elseif (count($tambahan_hari) == 2) {
							$uang_makan2 = $key['uang_makan'];
							$uang_kernet2 = $key['uang_kernet'];
							$biaya_perjalanan2 = $key['biaya_perjalanan'];
							$uang_makan3 = $key['uang_makan'];
							$uang_kernet3 = $key['uang_kernet'];
							$biaya_perjalanan3 = $key['biaya_perjalanan'];
						} elseif (count($tambahan_hari) == 3) {
							$uang_makan2 = $key['uang_makan'];
							$uang_kernet2 = $key['uang_kernet'];
							$biaya_perjalanan2 = $key['biaya_perjalanan'];
							$uang_makan3 = $key['uang_makan'];
							$uang_kernet3 = $key['uang_kernet'];
							$biaya_perjalanan3 = $key['biaya_perjalanan'];
							$uang_makan4 = $key['uang_makan'];
							$uang_kernet4 = $key['uang_kernet'];
							$biaya_perjalanan4 = $key['biaya_perjalanan'];
						} elseif (count($tambahan_hari) == 4) {
							$uang_makan2 = $key['uang_makan'];
							$uang_kernet2 = $key['uang_kernet'];
							$biaya_perjalanan2 = $key['biaya_perjalanan'];
							$uang_makan3 = $key['uang_makan'];
							$uang_kernet3 = $key['uang_kernet'];
							$biaya_perjalanan3 = $key['biaya_perjalanan'];
							$uang_makan4 = $key['uang_makan'];
							$uang_kernet4 = $key['uang_kernet'];
							$biaya_perjalanan4 = $key['biaya_perjalanan'];
							$uang_makan5 = $key['uang_makan'];
							$uang_kernet5 = $key['uang_kernet'];
							$biaya_perjalanan5 = $key['biaya_perjalanan'];
						}
					}
				}

				$exp = explode("||", $realisasi['pengisian_bbm']);
				$id_terminal = $exp[1];
				if ($realisasi['dispenser'] != 0) {
					$query_dispenser = "SELECT * FROM pro_master_terminal WHERE id_master = '" . $id_terminal . "'";
					$dispenser = $con->getRecord($query_dispenser);
					$pengisian_bbm = $dispenser['nama_terminal'] . " - " . $dispenser['tanki_terminal'];
				} else {
					$pengisian_bbm = $exp[0];
				}

				$exp2 = explode("||", $realisasi['pengisian_bbm_tambahan']);
				$id_terminal_tambahan = $exp2[1];
				if ($realisasi['dispenser_tambahan'] != 0) {
					$query_dispenser_tambahan = "SELECT * FROM pro_master_terminal WHERE id_master = '" . $id_terminal_tambahan . "'";
					$dispenser_tambahan = $con->getRecord($query_dispenser_tambahan);
					$pengisian_bbm_tambahan = $dispenser_tambahan['nama_terminal'] . " - " . $dispenser_tambahan['tanki_terminal'];
				} else {
					$pengisian_bbm_tambahan = $exp2[0];
				}

				$exp3 = explode("||", $realisasi['pengisian_bbm_tambahan2']);
				$id_terminal_tambahan3 = $exp3[1];
				if ($realisasi['dispenser_tambahan2'] != 0) {
					$query_dispenser_tambahan3 = "SELECT * FROM pro_master_terminal WHERE id_master = '" . $id_terminal_tambahan3 . "'";
					$dispenser_tambahan3 = $con->getRecord($query_dispenser_tambahan3);
					$pengisian_bbm_tambahan3 = $dispenser_tambahan3['nama_terminal'] . " - " . $dispenser_tambahan3['tanki_terminal'];
				} else {
					$pengisian_bbm_tambahan3 = $exp3[0];
				}
			} else {
				// TAMBAHAN HARI BPUJ
				$query = "SELECT * FROM pro_bpuj_tambahan_hari WHERE id_bpuj='" . $realisasi['id_bpuj'] . "'";
				$tambahan_hari = $con->getResult($query);

				$biaya_perjalanan = 0;
				$uang_makan2 = 0;
				$uang_kernet2 = 0;
				$biaya_perjalanan2 = 0;
				$uang_makan3 = 0;
				$uang_kernet3 = 0;
				$biaya_perjalanan3 = 0;
				$uang_makan4 = 0;
				$uang_kernet4 = 0;
				$biaya_perjalanan4 = 0;
				$uang_makan5 = 0;
				$uang_kernet5 = 0;
				$biaya_perjalanan5 = 0;
				if ($tambahan_hari != NULL || $tambahan_hari != "") {
					foreach ($tambahan_hari as $key) {
						if (count($tambahan_hari) == 1) {
							$uang_makan2 = $key['uang_makan'];
							$uang_kernet2 = $key['uang_kernet'];
							$biaya_perjalanan2 = $key['biaya_perjalanan'];
						} elseif (count($tambahan_hari) == 2) {
							$uang_makan2 = $key['uang_makan'];
							$uang_kernet2 = $key['uang_kernet'];
							$biaya_perjalanan2 = $key['biaya_perjalanan'];
							$uang_makan3 = $key['uang_makan'];
							$uang_kernet3 = $key['uang_kernet'];
							$biaya_perjalanan3 = $key['biaya_perjalanan'];
						} elseif (count($tambahan_hari) == 3) {
							$uang_makan2 = $key['uang_makan'];
							$uang_kernet2 = $key['uang_kernet'];
							$biaya_perjalanan2 = $key['biaya_perjalanan'];
							$uang_makan3 = $key['uang_makan'];
							$uang_kernet3 = $key['uang_kernet'];
							$biaya_perjalanan3 = $key['biaya_perjalanan'];
							$uang_makan4 = $key['uang_makan'];
							$uang_kernet4 = $key['uang_kernet'];
							$biaya_perjalanan4 = $key['biaya_perjalanan'];
						} elseif (count($tambahan_hari) == 4) {
							$uang_makan2 = $key['uang_makan'];
							$uang_kernet2 = $key['uang_kernet'];
							$biaya_perjalanan2 = $key['biaya_perjalanan'];
							$uang_makan3 = $key['uang_makan'];
							$uang_kernet3 = $key['uang_kernet'];
							$biaya_perjalanan3 = $key['biaya_perjalanan'];
							$uang_makan4 = $key['uang_makan'];
							$uang_kernet4 = $key['uang_kernet'];
							$biaya_perjalanan4 = $key['biaya_perjalanan'];
							$uang_makan5 = $key['uang_makan'];
							$uang_kernet5 = $key['uang_kernet'];
							$biaya_perjalanan5 = $key['biaya_perjalanan'];
						}
					}
				}

				$exp = explode("||", $realisasi['pengisian_bbm']);
				$id_terminal = $exp[1];
				if ($realisasi['dispenser'] != 0) {
					$query_dispenser = "SELECT * FROM pro_master_terminal WHERE id_master = '" . $id_terminal . "'";
					$dispenser = $con->getRecord($query_dispenser);
					$pengisian_bbm = $dispenser['nama_terminal'] . " - " . $dispenser['tanki_terminal'];
				} else {
					$pengisian_bbm = $exp[0];
				}

				$exp2 = explode("||", $realisasi['pengisian_bbm_tambahan']);
				$id_terminal_tambahan = $exp2[1];
				if ($realisasi['dispenser_tambahan'] != 0) {
					$query_dispenser_tambahan = "SELECT * FROM pro_master_terminal WHERE id_master = '" . $id_terminal_tambahan . "'";
					$dispenser_tambahan = $con->getRecord($query_dispenser_tambahan);
					$pengisian_bbm_tambahan = $dispenser_tambahan['nama_terminal'] . " - " . $dispenser_tambahan['tanki_terminal'];
				} else {
					$pengisian_bbm_tambahan = $exp2[0];
				}

				$exp3 = explode("||", $realisasi['pengisian_bbm_tambahan2']);
				$id_terminal_tambahan3 = $exp3[1];
				if ($realisasi['dispenser_tambahan2'] != 0) {
					$query_dispenser_tambahan3 = "SELECT * FROM pro_master_terminal WHERE id_master = '" . $id_terminal_tambahan3 . "'";
					$dispenser_tambahan3 = $con->getRecord($query_dispenser_tambahan3);
					$pengisian_bbm_tambahan3 = $dispenser_tambahan3['nama_terminal'] . " - " . $dispenser_tambahan3['tanki_terminal'];
				} else {
					$pengisian_bbm_tambahan3 = $exp3[0];
				}
			}
		} else {
			// if (fmod($row['liter_bbm'], 1) !== 0.000) {
			// 	$liter_bbm = number_format($data['liter_bbm'], 3, ",", ".");
			// } else {
			// 	$liter_bbm = number_format($data['liter_bbm']);
			// }
			$liter_bbm = $data['liter_bbm'];
			$nama_driver = $data['nama_driver'];
			$status_driver = $data['status_driver'];
			$no_unit = $data['no_unit'];


			if ($data['tgl_pengisian'] != NULL) {
				$tgl_pengisian = date("d-m-Y", strtotime($data['tgl_pengisian']));
			} else {
				$tgl_pengisian = "";
			}

			if ($data['tgl_pengisian_tambahan'] != NULL) {
				$tgl_pengisian_tambahan = date("d-m-Y", strtotime($data['tgl_pengisian_tambahan']));
			} else {
				$tgl_pengisian_tambahan = "";
			}

			if ($data['tgl_pengisian_tambahan2'] != NULL) {
				$tgl_pengisian_tambahan3 = date("d-m-Y", strtotime($data['tgl_pengisian_tambahan2']));
			} else {
				$tgl_pengisian_tambahan3 = "";
			}
			// if (fmod($row['liter_bbm_tambahan'], 1) !== 0.000) {
			// 	$liter_bbm_tambahan = number_format($data['liter_bbm_tambahan'], 3, ",", ".") . ' Liter';
			// } else {
			// 	$liter_bbm_tambahan = number_format($data['liter_bbm_tambahan']) . ' Liter';
			// }
			$liter_bbm_tambahan = $data['liter_bbm_tambahan'];
			$liter_bbm_tambahan3 = $data['liter_bbm_tambahan2'];

			$jarak = $data['jarak_real'] . ' KM';
			$total_jasa = $data['total_jasa'];
			$total_bbm = $data['total_bbm'];
			$uang_tol = $data['uang_tol'];
			$uang_demmurade = $data['uang_demmurade'];
			$uang_koordinasi = $data['uang_koordinasi'];
			$biaya_penyebrangan = $data['biaya_penyebrangan'];
			$biaya_lain = $data['biaya_lain'];
			$total_bpuj = $data['total_uang_bpuj'];
			$grand_total_bpuj += $data['total_uang_bpuj'];

			// TAMBAHAN HARI BPUJ
			$query = "SELECT * FROM pro_bpuj_tambahan_hari WHERE id_bpuj='" . $data['id_bpuj'] . "'";
			$tambahan_hari = $con->getResult($query);

			$biaya_perjalanan = 0;
			$uang_makan2 = 0;
			$uang_kernet2 = 0;
			$biaya_perjalanan2 = 0;
			$uang_makan3 = 0;
			$uang_kernet3 = 0;
			$biaya_perjalanan3 = 0;
			$uang_makan4 = 0;
			$uang_kernet4 = 0;
			$biaya_perjalanan4 = 0;
			$uang_makan5 = 0;
			$uang_kernet5 = 0;
			$biaya_perjalanan5 = 0;
			if ($tambahan_hari != NULL || $tambahan_hari != "") {
				foreach ($tambahan_hari as $key) {
					if (count($tambahan_hari) == 1) {
						$uang_makan2 = $key['uang_makan'];
						$uang_kernet2 = $key['uang_kernet'];
						$biaya_perjalanan2 = $key['biaya_perjalanan'];
					} elseif (count($tambahan_hari) == 2) {
						$uang_makan2 = $key['uang_makan'];
						$uang_kernet2 = $key['uang_kernet'];
						$biaya_perjalanan2 = $key['biaya_perjalanan'];
						$uang_makan3 = $key['uang_makan'];
						$uang_kernet3 = $key['uang_kernet'];
						$biaya_perjalanan3 = $key['biaya_perjalanan'];
					} elseif (count($tambahan_hari) == 3) {
						$uang_makan2 = $key['uang_makan'];
						$uang_kernet2 = $key['uang_kernet'];
						$biaya_perjalanan2 = $key['biaya_perjalanan'];
						$uang_makan3 = $key['uang_makan'];
						$uang_kernet3 = $key['uang_kernet'];
						$biaya_perjalanan3 = $key['biaya_perjalanan'];
						$uang_makan4 = $key['uang_makan'];
						$uang_kernet4 = $key['uang_kernet'];
						$biaya_perjalanan4 = $key['biaya_perjalanan'];
					} elseif (count($tambahan_hari) == 4) {
						$uang_makan2 = $key['uang_makan'];
						$uang_kernet2 = $key['uang_kernet'];
						$biaya_perjalanan2 = $key['biaya_perjalanan'];
						$uang_makan3 = $key['uang_makan'];
						$uang_kernet3 = $key['uang_kernet'];
						$biaya_perjalanan3 = $key['biaya_perjalanan'];
						$uang_makan4 = $key['uang_makan'];
						$uang_kernet4 = $key['uang_kernet'];
						$biaya_perjalanan4 = $key['biaya_perjalanan'];
						$uang_makan5 = $key['uang_makan'];
						$uang_kernet5 = $key['uang_kernet'];
						$biaya_perjalanan5 = $key['biaya_perjalanan'];
					}
				}
			}

			$exp = explode("||", $data['pengisian_bbm']);
			$id_terminal = $exp[1];
			if ($data['dispenser'] != 0) {
				$query_dispenser = "SELECT * FROM pro_master_terminal WHERE id_master = '" . $id_terminal . "'";
				$dispenser = $con->getRecord($query_dispenser);
				$pengisian_bbm = $dispenser['nama_terminal'] . " - " . $dispenser['tanki_terminal'];
			} else {
				$pengisian_bbm = $exp[0];
			}

			$exp2 = explode("||", $data['pengisian_bbm_tambahan']);
			$id_terminal_tamnbahan = $exp2[1];
			if ($data['dispenser_tambahan'] != 0) {
				$query_dispenser_tambahan = "SELECT * FROM pro_master_terminal WHERE id_master = '" . $id_terminal_tamnbahan . "'";
				$dispenser_tambahan = $con->getRecord($query_dispenser_tambahan);
				$pengisian_bbm_tambahan = $dispenser_tambahan['nama_terminal'] . " - " . $dispenser_tambahan['tanki_terminal'];
			} else {
				$pengisian_bbm_tambahan = $exp2[0];
			}

			$exp3 = explode("||", $data['pengisian_bbm_tambahan2']);
			$id_terminal_tambahan3 = $exp3[1];
			if ($data['dispenser_tambahan2'] != 0) {
				$query_dispenser_tambahan3 = "SELECT * FROM pro_master_terminal WHERE id_master = '" . $id_terminal_tambahan3 . "'";
				$dispenser_tambahan3 = $con->getRecord($query_dispenser_tambahan3);
				$pengisian_bbm_tambahan3 = $dispenser_tambahan3['nama_terminal'] . " - " . $dispenser_tambahan3['tanki_terminal'];
			} else {
				$pengisian_bbm_tambahan3 = $exp3[0];
			}
		}

		$writer->writeSheetRow($sheet, array(
			$tot1,
			date("d-m-Y", strtotime($data['tanggal_bpuj'])),
			$data['nomor_bpuj'],
			$nama_driver,
			$status_driver,
			$no_unit,
			$data['depo'],
			$data['no_spj'],
			$data['nama_customer'],
			$data['alamat_customer'],
			$data['jenis_tangki'] . ',000 Liter',
			$data['trip_po'],
			$pengisian_bbm,
			$liter_bbm,
			$tgl_pengisian,
			$pengisian_bbm_tambahan,
			$liter_bbm_tambahan,
			$tgl_pengisian_tambahan,
			$pengisian_bbm_tambahan3,
			$liter_bbm_tambahan3,
			$tgl_pengisian_tambahan3,
			$jarak,
			$data['jasa'],
			$total_jasa,
			$total_bbm,
			$data['uang_makan'],
			$data['uang_kernet'],
			$uang_makan2,
			$uang_kernet2,
			$biaya_perjalanan2,
			$uang_makan3,
			$uang_kernet3,
			$biaya_perjalanan3,
			$uang_makan4,
			$uang_kernet4,
			$biaya_perjalanan4,
			$uang_makan5,
			$uang_kernet5,
			$biaya_perjalanan5,
			$uang_tol,
			$uang_demmurade,
			$uang_koordinasi,
			$data['uang_multidrop'],
			$biaya_penyebrangan,
			$biaya_lain,
			$total_bpuj
		));
	}
	$writer->writeSheetRow($sheet, array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", $grand_total_bpuj));

	$writer->writeSheetRow($sheet, array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""));

	$writer->writeSheetRow($sheet, array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "Dibuat", "Diketahui"));

	$writer->writeSheetRow($sheet, array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""));
	$writer->writeSheetRow($sheet, array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""));
	$writer->writeSheetRow($sheet, array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""));

	$writer->writeSheetRow($sheet, array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ucwords($row_user['fullname']), $adh));
	$writer->writeSheetRow($sheet, array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", substr($row_user['role_name'], 5), "ADH"));

	$last++;
	$writer->newMergeCell($sheet, "A" . $last, "AT" . $last);
} else {
	$writer->writeSheetRow($sheet, array("Data tidak ada"));
	$writer->newMergeCell($sheet, "A" . $start, "AT" . $start);
	$start++;
}

$con->close();
$writer->writeToStdOut();
exit(0);

<?php
// ini_set('memory_limit', '256M');
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$con = new Connection();
$id_wilayah  = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$q1 = isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';
$q2 = $_POST["q2"];
$q3 = isset($_POST["q3"]) ? htmlspecialchars($_POST["q3"], ENT_QUOTES) : '';

if ($q2) {
    $q2 = array_map('intval', $q2);
    $customer_ids_str = implode(',', $q2);

    $filter_customer = " AND a.id_customer IN ($customer_ids_str)";
} else {
    if ($q3) {
        $filter_customer = " AND a.id_wilayah = '" . $q3 . "'";
    } else {
        $filter_customer = " AND a.id_wilayah = 4";
    }
}


if ($q1) {
    $year = $q1;
} else {
    $year = date("Y");
}

$sql = "SELECT CONCAT(a.kode_pelanggan, ' ', a.nama_customer) as nama_customer,
COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 1 THEN d.realisasi_volume ELSE 0 END), 0) AS total_realisasi_jan,
COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 1 THEN f.volume ELSE 0 END), 0) AS total_delivered_jan,
COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 1 THEN (CASE WHEN d.realisasi_volume = 0 THEN 0 ELSE (d.realisasi_volume - f.volume) END) ELSE 0 END), 0) AS total_losses_jan,

COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 2 THEN d.realisasi_volume ELSE 0 END), 0) AS total_realisasi_feb,
COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 2 THEN f.volume ELSE 0 END), 0) AS total_delivered_feb,
COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 2 THEN (CASE WHEN d.realisasi_volume = 0 THEN 0 ELSE (d.realisasi_volume - f.volume) END) ELSE 0 END), 0) AS total_losses_feb,

COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 3 THEN d.realisasi_volume ELSE 0 END), 0) AS total_realisasi_mar,
COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 3 THEN f.volume ELSE 0 END), 0) AS total_delivered_mar,
COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 3 THEN (CASE WHEN d.realisasi_volume = 0 THEN 0 ELSE (d.realisasi_volume - f.volume) END) ELSE 0 END), 0) AS total_losses_mar,

COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 4 THEN d.realisasi_volume ELSE 0 END), 0) AS total_realisasi_apr,
COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 4 THEN f.volume ELSE 0 END), 0) AS total_delivered_apr,
COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 4 THEN (CASE WHEN d.realisasi_volume = 0 THEN 0 ELSE (d.realisasi_volume - f.volume) END) ELSE 0 END), 0) AS total_losses_apr,

COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 5 THEN d.realisasi_volume ELSE 0 END), 0) AS total_realisasi_mei,
COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 5 THEN f.volume ELSE 0 END), 0) AS total_delivered_mei,
COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 5 THEN (CASE WHEN d.realisasi_volume = 0 THEN 0 ELSE (d.realisasi_volume - f.volume) END) ELSE 0 END), 0) AS total_losses_mei,

COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 6 THEN d.realisasi_volume ELSE 0 END), 0) AS total_realisasi_jun,
COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 6 THEN f.volume ELSE 0 END), 0) AS total_delivered_jun,
COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 6 THEN (CASE WHEN d.realisasi_volume = 0 THEN 0 ELSE (d.realisasi_volume - f.volume) END) ELSE 0 END), 0) AS total_losses_jun,

COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 7 THEN d.realisasi_volume ELSE 0 END), 0) AS total_realisasi_jul,
COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 7 THEN f.volume ELSE 0 END), 0) AS total_delivered_jul,
COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 7 THEN (CASE WHEN d.realisasi_volume = 0 THEN 0 ELSE (d.realisasi_volume - f.volume) END) ELSE 0 END), 0) AS total_losses_jul,

COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 8 THEN d.realisasi_volume ELSE 0 END), 0) AS total_realisasi_ags,
COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 8 THEN f.volume ELSE 0 END), 0) AS total_delivered_ags,
COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 8 THEN (CASE WHEN d.realisasi_volume = 0 THEN 0 ELSE (d.realisasi_volume - f.volume) END) ELSE 0 END), 0) AS total_losses_ags,

COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 9 THEN d.realisasi_volume ELSE 0 END), 0) AS total_realisasi_sep,
COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 9 THEN f.volume ELSE 0 END), 0) AS total_delivered_sep,
COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 9 THEN (CASE WHEN d.realisasi_volume = 0 THEN 0 ELSE (d.realisasi_volume - f.volume) END) ELSE 0 END), 0) AS total_losses_sep,

COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 10 THEN d.realisasi_volume ELSE 0 END), 0) AS total_realisasi_okt,
COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 10 THEN f.volume ELSE 0 END), 0) AS total_delivered_okt,
COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 10 THEN (CASE WHEN d.realisasi_volume = 0 THEN 0 ELSE (d.realisasi_volume - f.volume) END) ELSE 0 END), 0) AS total_losses_okt,

COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 11 THEN d.realisasi_volume ELSE 0 END), 0) AS total_realisasi_nov,
COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 11 THEN f.volume ELSE 0 END), 0) AS total_delivered_nov,
COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 11 THEN (CASE WHEN d.realisasi_volume = 0 THEN 0 ELSE (d.realisasi_volume - f.volume) END) ELSE 0 END), 0) AS total_losses_nov,

COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 12 THEN d.realisasi_volume ELSE 0 END), 0) AS total_realisasi_des,
COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 12 THEN f.volume ELSE 0 END), 0) AS total_delivered_des,
COALESCE(SUM(CASE WHEN MONTH(e.tanggal_ds) = 12 THEN (CASE WHEN d.realisasi_volume = 0 THEN 0 ELSE (d.realisasi_volume - f.volume) END) ELSE 0 END), 0) AS total_losses_des

FROM pro_customer a
JOIN pro_po_customer b ON a.id_customer=b.id_customer
JOIN pro_po_customer_plan c ON b.id_poc=c.id_poc
JOIN pro_po_ds_detail d ON c.id_plan=d.id_plan
JOIN pro_po_ds e ON d.id_ds=e.id_ds
JOIN pro_pr_detail f ON d.id_prd=f.id_prd
WHERE a.is_verified = 1
AND a.status_customer = 2
AND d.is_delivered = 1
" . $filter_customer . "
AND YEAR(e.tanggal_ds) = '" . $year . "'";

$sql .= "GROUP BY a.id_customer ORDER BY a.id_customer ASC";

$tot_record = $con->num_rows($sql);

if ($tot_record == 0) {
    $array = [];
} else {
    $result = $con->getResult($sql);

    $total_volume_jan = 0;
    $total_realisasi_jan = 0;
    $total_losses_jan = 0;

    $total_volume_feb = 0;
    $total_realisasi_feb = 0;
    $total_losses_feb = 0;

    $total_volume_mar = 0;
    $total_realisasi_mar = 0;
    $total_losses_mar = 0;

    $total_volume_apr = 0;
    $total_realisasi_apr = 0;
    $total_losses_apr = 0;

    $total_volume_mei = 0;
    $total_realisasi_mei = 0;
    $total_losses_mei = 0;

    $total_volume_jun = 0;
    $total_realisasi_jun = 0;
    $total_losses_jun = 0;

    $total_volume_jul = 0;
    $total_realisasi_jul = 0;
    $total_losses_jul = 0;

    $total_volume_ags = 0;
    $total_realisasi_ags = 0;
    $total_losses_ags = 0;

    $total_volume_sep = 0;
    $total_realisasi_sep = 0;
    $total_losses_sep = 0;

    $total_volume_okt = 0;
    $total_realisasi_okt = 0;
    $total_losses_okt = 0;

    $total_volume_nov = 0;
    $total_realisasi_nov = 0;
    $total_losses_nov = 0;

    $total_volume_des = 0;
    $total_realisasi_des = 0;
    $total_losses_des = 0;

    foreach ($result as $data) {

        if ($data['total_losses_jan'] > 0) {
            $losses_jan = 0;
            $persentase_selisih_jan = "0.00%";
        } else {
            $losses_jan = $data['total_losses_jan'];
            $convert_positif_jan = abs($losses_jan);

            if ($convert_positif_jan != 0) {
                $persentase_selisih_jan = "-" . number_format(($convert_positif_jan / $data['total_delivered_jan']) * 100, 2) . "%";
            } else {
                $persentase_selisih_jan = "0.00%"; // Menghindari pembagian dengan nol
            }
        }

        if ($data['total_losses_feb'] > 0) {
            $losses_feb = 0;
            $persentase_selisih_feb = "0.00%";
        } else {
            $losses_feb = $data['total_losses_feb'];
            $convert_positif_feb = abs($losses_feb);

            if ($convert_positif_feb != 0) {
                $persentase_selisih_feb = "-" . number_format(($convert_positif_feb / $data['total_delivered_feb']) * 100, 2) . "%";
            } else {
                $persentase_selisih_feb = "0.00%";; // Menghindari pembagian dengan nol
            }
        }

        if ($data['total_losses_mar'] > 0) {
            $losses_mar = 0;
            $persentase_selisih_mar = "0.00%";
        } else {
            $losses_mar = $data['total_losses_mar'];
            $convert_positif_mar = abs($losses_mar);

            if ($convert_positif_mar != 0) {
                $persentase_selisih_mar = "-" . number_format(($convert_positif_mar / $data['total_delivered_mar']) * 100, 2) . "%";
            } else {
                $persentase_selisih_mar = "0.00%"; // Menghindari pembagian dengan nol
            }
        }

        if ($data['total_losses_apr'] > 0) {
            $losses_apr = 0;
            $persentase_selisih_apr = "0.00%";
        } else {
            $losses_apr = $data['total_losses_apr'];
            $convert_positif_apr = abs($losses_apr);

            if ($convert_positif_apr != 0) {
                $persentase_selisih_apr = "-" . number_format(($convert_positif_apr / $data['total_delivered_apr']) * 100, 2) . "%";
            } else {
                $persentase_selisih_apr = "0.00%"; // Menghindari pembagian dengan nol
            }
        }

        if ($data['total_losses_mei'] > 0) {
            $losses_mei = 0;
            $persentase_selisih_mei = "0.00%";
        } else {
            $losses_mei = $data['total_losses_mei'];
            $convert_positif_mei = abs($losses_mei);

            if ($convert_positif_mei != 0) {
                $persentase_selisih_mei = "-" . number_format(($convert_positif_mei / $data['total_delivered_mei']) * 100, 2) . "%";
            } else {
                $persentase_selisih_mei = "0.00%"; // Menghindari pembagian dengan nol
            }
        }

        if ($data['total_losses_jun'] > 0) {
            $losses_jun = 0;
            $persentase_selisih_jun = "0.00%";
        } else {
            $losses_jun = $data['total_losses_jun'];
            $convert_positif_jun = abs($losses_jun);

            if ($convert_positif_jun != 0) {
                $persentase_selisih_jun = "-" . number_format(($convert_positif_jun / $data['total_delivered_jun']) * 100, 2) . "%";
            } else {
                $persentase_selisih_jun = "0.00%"; // Menghindari pembagian dengan nol
            }
        }

        if ($data['total_losses_jul'] > 0) {
            $losses_jul = 0;
            $persentase_selisih_jul = "0.00%";
        } else {
            $losses_jul = $data['total_losses_jul'];
            $convert_positif_jul = abs($losses_jul);

            if ($convert_positif_jul != 0) {
                $persentase_selisih_jul = "-" . number_format(($convert_positif_jul / $data['total_delivered_jul']) * 100, 2) . "%";
            } else {
                $persentase_selisih_jul = "0.00%"; // Menghindari pembagian dengan nol
            }
        }

        if ($data['total_losses_ags'] > 0) {
            $losses_ags = 0;
            $persentase_selisih_ags = "0.00%";
        } else {
            $losses_ags = $data['total_losses_ags'];
            $convert_positif_ags = abs($losses_ags);

            if ($convert_positif_ags != 0) {
                $persentase_selisih_ags = "-" . number_format(($convert_positif_ags / $data['total_delivered_ags']) * 100, 2) . "%";
            } else {
                $persentase_selisih_ags = "0.00%"; // Menghindari pembagian dengan nol
            }
        }

        if ($data['total_losses_sep'] > 0) {
            $losses_sep = 0;
            $persentase_selisih_sep = "0.00%";
        } else {
            $losses_sep = $data['total_losses_sep'];
            $convert_positif_sep = abs($losses_sep);

            if ($convert_positif_sep != 0) {
                $persentase_selisih_sep = "-" . number_format(($convert_positif_sep / $data['total_delivered_sep']) * 100, 2) . "%";
            } else {
                $persentase_selisih_sep = "0.00%"; // Menghindari pembagian dengan nol
            }
        }

        if ($data['total_losses_okt'] > 0) {
            $losses_okt = 0;
            $persentase_selisih_okt = "0.00%";
        } else {
            $losses_okt = $data['total_losses_okt'];
            $convert_positif_okt = abs($losses_okt);

            if ($convert_positif_okt != 0) {
                $persentase_selisih_okt = "-" . number_format(($convert_positif_okt / $data['total_delivered_okt']) * 100, 2) . "%";
            } else {
                $persentase_selisih_okt = "0.00%"; // Menghindari pembagian dengan nol
            }
        }

        if ($data['total_losses_nov'] > 0) {
            $losses_nov = 0;
            $persentase_selisih_nov = "0.00%";
        } else {
            $losses_nov = $data['total_losses_nov'];
            $convert_positif_nov = abs($losses_nov);

            if ($convert_positif_nov != 0) {
                $persentase_selisih_nov = "-" . number_format(($convert_positif_nov / $data['total_delivered_nov']) * 100, 2) . "%";
            } else {
                $persentase_selisih_nov = "0.00%"; // Menghindari pembagian dengan nol
            }
        }

        if ($data['total_losses_des'] > 0) {
            $losses_des = 0;
            $persentase_selisih_des = "0.00%";
        } else {
            $losses_des = $data['total_losses_des'];
            $convert_positif_des = abs($losses_des);

            if ($convert_positif_des != 0) {
                $persentase_selisih_des = "-" . number_format(($convert_positif_des / $data['total_delivered_des']) * 100, 2) . "%";
            } else {
                $persentase_selisih_des = "0.00%"; // Menghindari pembagian dengan nol
            }
        }

        // Total Januari
        $total_volume_jan += $data['total_delivered_jan'];
        $total_realisasi_jan += $data['total_realisasi_jan'];
        $total_losses_jan += $losses_jan;
        // Total Februari
        $total_volume_feb += $data['total_delivered_feb'];
        $total_realisasi_feb += $data['total_realisasi_feb'];
        $total_losses_feb += $losses_feb;
        // Total Maret
        $total_volume_mar += $data['total_delivered_mar'];
        $total_realisasi_mar += $data['total_realisasi_mar'];
        $total_losses_mar += $losses_mar;
        // Total April
        $total_volume_apr += $data['total_delivered_apr'];
        $total_realisasi_apr += $data['total_realisasi_apr'];
        $total_losses_apr += $losses_apr;
        // Total Mei
        $total_volume_mei += $data['total_delivered_mei'];
        $total_realisasi_mei += $data['total_realisasi_mei'];
        $total_losses_mei += $losses_mei;
        // Total Juni
        $total_volume_jun += $data['total_delivered_jun'];
        $total_realisasi_jun += $data['total_realisasi_jun'];
        $total_losses_jun += $losses_jun;
        // Total Juli
        $total_volume_jul += $data['total_delivered_jul'];
        $total_realisasi_jul += $data['total_realisasi_jul'];
        $total_losses_jul += $losses_jul;
        // Total Agustus
        $total_volume_ags += $data['total_delivered_ags'];
        $total_realisasi_ags += $data['total_realisasi_ags'];
        $total_losses_ags += $losses_ags;
        // Total September
        $total_volume_sep += $data['total_delivered_sep'];
        $total_realisasi_sep += $data['total_realisasi_sep'];
        $total_losses_sep += $losses_sep;
        // Total Oktober
        $total_volume_okt += $data['total_delivered_okt'];
        $total_realisasi_okt += $data['total_realisasi_okt'];
        $total_losses_okt += $losses_okt;
        // Total November
        $total_volume_nov += $data['total_delivered_nov'];
        $total_realisasi_nov += $data['total_realisasi_nov'];
        $total_losses_nov += $losses_nov;
        // Total Desember
        $total_volume_des += $data['total_delivered_des'];
        $total_realisasi_des += $data['total_realisasi_des'];
        $total_losses_des += $losses_des;

        $array = [
            "sj_januari" => $total_volume_jan,
            "realisasi_januari" => $total_realisasi_jan,
            "losses_januari" => $total_losses_jan,

            "sj_februari" => $total_volume_feb,
            "realisasi_februari" => $total_realisasi_feb,
            "losses_februari" => $total_losses_feb,

            "sj_maret" => $total_volume_mar,
            "realisasi_maret" => $total_realisasi_mar,
            "losses_maret" => $total_losses_mar,

            "sj_april" => $total_volume_apr,
            "realisasi_april" => $total_realisasi_apr,
            "losses_april" => $total_losses_apr,

            "sj_mei" => $total_volume_mei,
            "realisasi_mei" => $total_realisasi_mei,
            "losses_mei" => $total_losses_mei,

            "sj_juni" => $total_volume_jun,
            "realisasi_juni" => $total_realisasi_jun,
            "losses_juni" => $total_losses_jun,

            "sj_juli" => $total_volume_jul,
            "realisasi_juli" => $total_realisasi_jul,
            "losses_juli" => $total_losses_jul,

            "sj_agustus" => $total_volume_ags,
            "realisasi_agustus" => $total_realisasi_ags,
            "losses_agustus" => $total_losses_ags,

            "sj_september" => $total_volume_sep,
            "realisasi_september" => $total_realisasi_sep,
            "losses_september" => $total_losses_sep,

            "sj_oktober" => $total_volume_okt,
            "realisasi_oktober" => $total_realisasi_okt,
            "losses_oktober" => $total_losses_okt,

            "sj_november" => $total_volume_nov,
            "realisasi_november" => $total_realisasi_nov,
            "losses_november" => $total_losses_nov,

            "sj_desember" => $total_volume_des,
            "realisasi_desember" => $total_realisasi_des,
            "losses_desember" => $total_losses_des,
        ];
    }
}
// echo json_encode($data);
// exit();
if ($q1 != "" || $q2 != "" || $q3 != "") {
    echo json_encode($array);
} else {
    $volumeJSON = json_encode($array);
}
// unset($data);

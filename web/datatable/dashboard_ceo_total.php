<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth    = new MyOtentikasi();
$con     = new Connection();
// Dekripsi session untuk mendapatkan id_wilayah
$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$where = " c.id_wilayah = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]) . "'";
// $q2    = isset($_GET["q2"]) ? htmlspecialchars($_GET["q2"], ENT_QUOTES) : '';
// $q3    = isset($_GET["q3"]) ? htmlspecialchars($_GET["q3"], ENT_QUOTES) : '';
$q4   = isset($_GET["q4"]) ? htmlspecialchars($_GET["q4"], ENT_QUOTES) : '';
$selectBulan   = isset($_GET["selectBulan"]) ? htmlspecialchars($_GET["selectBulan"], ENT_QUOTES) : '';
$selectTahun   = isset($_GET["selectTahun"]) ? htmlspecialchars($_GET["selectTahun"], ENT_QUOTES) : '';

$year = date('Y');
$month = date('m');
// Query untuk mengambil data volume dan tanggal_loaded
$sql_refund = "SELECT SUM(total_refund) as total_refund FROM pro_refund r
            JOIN pro_po_ds_detail a ON r.`id_dsd`= a.`id_dsd`
            JOIN pro_po_customer b ON a.id_poc=b.id_poc
            JOIN pro_customer c ON b.id_customer = c.id_customer
            JOIN pro_master_cabang d ON c.id_wilayah = d.id_master";

$sql_losses = "SELECT a.losses, a.batas_toleransi, b.harga_poc 
                FROM pro_po_ds_detail a 
                JOIN pro_po_customer b ON a.id_poc=b.id_poc
                JOIN pro_customer c ON b.id_customer = c.id_customer
                JOIN pro_master_cabang d ON c.id_wilayah = d.id_master";

$sql_revenue = "
	select a.*, 
	b.purchasing_result,
	c.tanggal_kirim, c.status_plan, c.catatan_reschedule, c.status_jadwal, 
	n.nilai_pbbkb, 
	k.id_penawaran, k.masa_awal, k.masa_akhir, k.id_area, k.flag_approval, 
	k.refund_tawar, k.other_cost, k.perhitungan, k.detail_rincian, k.harga_dasar, k.gabung_oa, k.pembulatan,
	o1.harga_normal, o2.harga_normal as harga_normal_new, 
	h.nama_customer, h.id_customer, 
	i.fullname, l.nama_area, d.harga_poc, 
	m.jenis_produk, e.jenis_usaha, 
	d.nomor_poc, d.produk_poc,
	t.volume as volume_potong, t.nomor_po_supplier as nomor_potong,  t.pr_harga_beli as harga_potong
	from pro_pr_detail a 
	join pro_pr b on a.id_pr = b.id_pr 
	join pro_po_customer_plan c on a.id_plan = c.id_plan 
	join pro_po_customer d on c.id_poc = d.id_poc 
	join pro_customer_lcr e on c.id_lcr = e.id_lcr 
	join pro_master_provinsi f on e.prov_survey = f.id_prov 
	join pro_master_kabupaten g on e.kab_survey = g.id_kab 
	join pro_customer h on d.id_customer = h.id_customer 
	join acl_user i on h.id_marketing = i.id_user 
	join pro_master_cabang j on h.id_wilayah = j.id_master 
	join pro_penawaran k on d.id_penawaran = k.id_penawaran 
	join pro_master_area l on k.id_area = l.id_master 
	join pro_master_produk m on d.produk_poc = m.id_master 
	join pro_master_pbbkb n on k.pbbkb_tawar = n.id_master 
	left join pro_master_harga_minyak o1 on k.masa_awal = o1.periode_awal and k.masa_akhir = o1.periode_akhir and k.id_area = o1.id_area  and k.produk_tawar = o1.produk
		and k.pbbkb_tawar = o1.pajak and o1.is_approved = 1 
	left join pro_master_harga_minyak o2 on k.masa_awal = o2.periode_awal and k.masa_akhir = o2.periode_akhir and k.id_area = o2.id_area  and k.produk_tawar = o2.produk
		and o2.pajak = 1 and o2.is_approved = 1 
	left join pro_master_terminal p on a.pr_terminal = p.id_master 
	left join pro_master_vendor q on a.pr_vendor = q.id_master 
	left join pro_master_wilayah_angkut r on e.id_wil_oa = r.id_master 
	left join pro_po_ds_detail s on a.id_prd = s.id_prd 
	LEFT JOIN new_pro_inventory_potongan_stock t ON a.id_prd = t.id_prd
	where 
		a.is_approved = 1 and s.is_cancel != 1 
		
";

$sql_ar = "
		SELECT 
		SUM((b.not_yet + b.ov_up_07 + b.ov_under_30 + b.ov_under_60 + b.ov_under_90 + b.ov_up_90)) AS total 
		FROM pro_customer a 
		JOIN pro_customer_admin_arnya b ON a.id_customer = b.id_customer 
		JOIN acl_user c ON a.id_marketing = c.id_user 
		JOIN pro_master_cabang d ON a.id_wilayah = d.id_master 
		WHERE 1=1
	";


if ($q4 != '') {
    $sql_refund .= " and c.id_wilayah = '" . $q4 . "'";
    $sql_losses .= " and c.id_wilayah = '" . $q4 . "'";
    $sql_revenue .= " and h.id_wilayah = '" . $q4 . "'";
    $sql_ar .= " and a.id_wilayah = '" . $q4 . "'";
}

if ($selectTahun != "") {
    $sql_refund .= " and YEAR(tgl_realisasi) = '" . $selectTahun . "'";
    $sql_losses .= " and YEAR(a.tanggal_delivered) = '" . $selectTahun . "'";
    $sql_revenue .= " and YEAR(c.tanggal_kirim) = '" . $selectTahun . "'";
}
if ($selectBulan != "") {
    $sql_refund .= " and MONTH(tgl_realisasi) = '" . $selectBulan . "'";
    $sql_losses .= " and MONTH(a.tanggal_delivered) = '" . $selectBulan . "'";
    $sql_revenue .= " and MONTH(c.tanggal_kirim) = '" . $selectBulan . "'";
}

// else{
//     $sql_refund .= "and YEAR(tgl_realisasi) = '" .$year. "' AND MONTH(tgl_realisasi) = '".$month."'";
//     $sql_losses .= "and YEAR(tgl_realisasi) = '" .$year. "' AND MONTH(tgl_realisasi) = '".$month."'";
//     $sql_revenue .= "and YEAR(tgl_realisasi) = '" .$year. "' AND MONTH(tgl_realisasi) = '".$month."'";
// }

// if ($q2 != "" && $q3 != "" ){
//     $sql_refund .= " and date(tgl_realisasi) between '" . tgl_db($q2) . "' and '" . tgl_db($q3) . "'";
//     $sql_losses .= " and date(tgl_realisasi) between '" . tgl_db($q2) . "' and '" . tgl_db($q3) . "'";
//     $sql_revenue .= " and date(tgl_realisasi) between '" . tgl_db($q2) . "' and '" . tgl_db($q3) . "'";
// }

// Menjalankan query dan mendapatkan hasilnya
$result_refund = $con->getRecord($sql_refund);
// Menjalankan query dan mendapatkan hasilnya
$result_losses = $con->getResult($sql_losses);
$result_revenue = $con->getResult($sql_revenue);
$result_ar = $con->getRecord($sql_ar);
// Siapkan data dalam format array untuk JSON
// $data = [];
$losses_total = 0;
if ($result_losses) {

    foreach ($result_losses as $row) {
        if ($row['losses'] > $row['batas_toleransi']) {
            $losses = $row['losses'] - $row['batas_toleransi'];
            $sum_losses = $losses * $row['harga_poc'];
            $losses_total += $sum_losses;
        }
    }
}

$revenue_total = 0;
foreach ($result_revenue as $data_revenue) {
    $volume = $data_revenue['volume'];
    $vol_potongan_pr = $data_revenue['vol_potongan'];
    $volume_potong_split = $data_revenue['volume_potong'];
    $harga_potong = $data_revenue['harga_potong'];
    $volori = ($data_revenue['vol_ori_pr'] ? $data_revenue['vol_ori_pr'] : $data_revenue['volume']);
    $tmphrg = $data_revenue['refund_tawar'] + $data_revenue['other_cost'];
    $nethrg = $data_revenue['harga_poc'] - $tmphrg;
    $pr_harga_beli =  $data_revenue['pr_harga_beli'];
    $pr_harga_beli_potong =  $data_revenue['harga_potong'];
    $netgnl = ($nethrg - $data_revenue['harga_normal']) * $volume;


    $rincian = json_decode($data_revenue['detail_rincian'], true);
    $harga_dasar_new = 0;
    // if ($data['pembulatan'] == 0) {
    // 	$harga_dasarnya = number_format($data['harga_dasar'], 2);
    // } elseif ($data['pembulatan'] == 1) {
    // 	$harga_dasarnya = number_format($data['harga_dasar']);
    // } elseif ($data['pembulatan'] == 2) {
    // 	$harga_dasarnya = number_format($data['harga_dasar'], 4);
    // }
    foreach ($rincian as $idx23 => $arr1) {
        $cetak = 1;
        $nilai = $arr1['nilai'];
        $biaya = ($arr1['biaya'] || $arr1['biaya'] != '') ?  number_format($arr1['biaya'], 2) : 0;
        // $biaya =  ($rsm['pembulatan']) ? number_format($arr1['biaya']) : number_format($arr1['biaya'], 2);
        // echo " Biaya format: $biaya <br>";
        $jenis = $arr1['rincian'];
        if ($idx23 == 0) {

            $harga_dasar_new = str_replace(",", "", $biaya);
        }
        // echo "Index $idx23: Biaya format: $harga_dasar_new<br>";
    }


    if ($vol_potongan_pr == 0) {
        $total_harga_dasar_nett = $harga_dasar_new - $tmphrg;
        $netprt = ($total_harga_dasar_nett - $pr_harga_beli) * $volume;

        $revenue_total += $netprt;
    } else {
        $total_harga_dasar_nett = $harga_dasar_new - $tmphrg;
        $netprt = ($total_harga_dasar_nett - $pr_harga_beli) * $vol_potongan_pr;
        $revenue_total += $netprt;
    }
}

$data[] = [
    'total_refund' => $result_refund['total_refund'],
    'total_losses' => $losses_total,
    'total_revenue' => $revenue_total,
    'total_ar' => $result_ar['total']
];

// Mengirimkan data dalam format JSON
echo json_encode($data);

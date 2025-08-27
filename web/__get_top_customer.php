<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$conSub = new Connection();
$q1 	= htmlspecialchars($_POST["q1"], ENT_QUOTES);
$tgl01 	= date('Y-m-d');
$tgl02 	= explode("-", $tgl01);

// $tgl03_raw = date('Y-m-d', strtotime('+1 month', strtotime($tgl01)));
// $tgl03 	= explode("-", $tgl03_raw);
$tiga_hari_kemudian = date('Y-m-d', strtotime($tgl01 . ' +7 days'));
$tgl03 = explode("-", $tiga_hari_kemudian);

$extra01 = "";

$extra01 = "";


if ($tgl02[2] > 15) {
	$extra01 = "";
} elseif ($tgl02[2] < 15) {
	$extra01 .= " or b.masa_awal between '" . $tgl02[0] . "-" . $tgl02[1] . "-01' and '" . $tgl02[0] . "-" . $tgl02[1] . "-14'";
}

if ($tgl02[2] >= 12) {
	$extra01 .= " or b.masa_awal between '" . $tgl02[0] . "-" . $tgl02[1] . "-15' and '" . $tgl02[0] . "-" . $tgl02[1] . "-31'";
}

// Jika bulan berubah
if ($tgl02[1] != $tgl03[1]) {
	// Periode 1 bulan berikutnya (1-14 September)
	if ($tgl03[2] <= 14) {
		$extra01 .= " or b.masa_awal between '" . $tgl03[0] . "-" . $tgl03[1] . "-01' and '" . $tgl03[0] . "-" . $tgl03[1] . "-14'";
	}
	// Periode 2 bulan berikutnya (15-31 September)
	if ($tgl03[2] > 14) {
		$extra01 .= " or b.masa_awal between '" . $tgl03[0] . "-" . $tgl03[1] . "-15' and '" . $tgl03[0] . "-" . $tgl03[1] . "-31'";
	}
}


$extra01 = substr($extra01, 4);  // Menghilangkan "or" pertama

//$extra01 = substr($extra01, 4);  // Menghilangkan "or" pertama


$sql1 = "
		select 
		if(a.jenis_payment = 'CREDIT', a.top_payment, a.jenis_payment) as top_customer,
		a.top_payment,
		a.credit_limit,
		c.not_yet as not_yet,
		c.ov_up_07 as ov_up_07, 
		c.ov_under_30 as ov_under_30,
		c.ov_under_60 as ov_under_60,
		c.ov_under_90 as ov_under_90,
		c.ov_up_90 as ov_up_90 
		from pro_customer a 
		join pro_customer_admin_arnya c on a.id_customer = c.id_customer 
		where 1=1 and a.id_customer = '" . $q1 . "' 
	";
$row1 = $conSub->getResult($sql1);


$sql2 = "
		select 
		b.id_penawaran, 
		nomor_surat as kode_penawaran  
		from pro_customer a 
		join pro_penawaran b on a.id_customer = b.id_customer and b.flag_approval = 1 
		where 1=1 and a.id_customer = '" . $q1 . "' 
		 " . ($extra01 ? " and (" . $extra01 . ")" : "") . "
		order by b.id_penawaran desc
	";
$row2 = $conSub->getResult($sql2);

if ($row1 != null) {
	$reminding = ($row1[0]['credit_limit'] ? $row1[0]['credit_limit'] - ($row1[0]['not_yet'] + $row1[0]['ov_up_07'] + $row1[0]['ov_under_30'] + $row1[0]['ov_under_60'] + $row1[0]['ov_under_90'] + $row1[0]['ov_up_90']) : 0);

	$answer['top_payment'] 	= $row1[0]['top_payment'];
	$answer['credit_limit'] = 'Rp ' . ($row1[0]['credit_limit'] ? number_format($row1[0]['credit_limit']) : 0);
	$answer['not_yet'] 		= 'Rp ' . ($row1[0]['not_yet'] ? number_format($row1[0]['not_yet']) : 0);
	$answer['ov_up_07'] 	= 'Rp ' . ($row1[0]['ov_up_07'] ? number_format($row1[0]['ov_up_07']) : 0);
	$answer['ov_under_30'] 	= 'Rp ' . ($row1[0]['ov_under_30'] ? number_format($row1[0]['ov_under_30']) : 0);
	$answer['ov_under_60'] 	= 'Rp ' . ($row1[0]['ov_under_60'] ? number_format($row1[0]['ov_under_60']) : 0);
	$answer['ov_under_90'] 	= 'Rp ' . ($row1[0]['ov_under_90'] ? number_format($row1[0]['ov_under_90']) : 0);
	$answer['ov_up_90'] 	= 'Rp ' . ($row1[0]['ov_up_90'] ? number_format($row1[0]['ov_up_90']) : 0);
	$answer['reminding'] 	= 'Rp ' . ($reminding ? number_format($reminding) : 0);

	$answer['nilai_credit_limit'] 	= ($row1[0]['credit_limit'] ? $row1[0]['credit_limit'] : 0);
	$answer['nilai_not_yet'] 		= ($row1[0]['not_yet'] ? $row1[0]['not_yet'] : 0);
	$answer['nilai_ov_up_07'] 		= ($row1[0]['ov_up_07'] ? $row1[0]['ov_up_07'] : 0);
	$answer['nilai_ov_under_30'] 	= ($row1[0]['ov_under_30'] ? $row1[0]['ov_under_30'] : 0);
	$answer['nilai_ov_under_60'] 	= ($row1[0]['ov_under_60'] ? $row1[0]['ov_under_60'] : 0);
	$answer['nilai_ov_under_90'] 	= ($row1[0]['ov_under_90'] ? $row1[0]['ov_under_90'] : 0);
	$answer['nilai_ov_up_90'] 		= ($row1[0]['ov_up_90'] ? $row1[0]['ov_up_90'] : 0);
	$answer['nilai_reminding'] 		= ($reminding ? $reminding : 0);

	$answer['items'][] = array('id' => '', 'text' => '');
	if (count($row2) > 0) {
		foreach ($row2 as $data) {
			$answer['items'][] = array(
				'id' => $data['id_penawaran'],
				'text' => $data['kode_penawaran']
			);
		}
	}
}

$conSub->close();
echo json_encode($answer);

<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
// require_once ($public_base_directory."/libraries/helper/excelgen/PHPExcel/IOFactory.php");
require_once($public_base_directory . "/libraries/helper/class.xlsxwriter.php");
load_helper("autoload");

error_reporting(E_ALL ^ E_DEPRECATED);
// error_reporting(E_ALL ^ (E_NOTICE | E_WARNING | E_DEPRECATED));

$auth   = new MyOtentikasi();
$con    = new Connection();

$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);

$q1 = isset($_REQUEST['q1']) ? $_REQUEST['q1'] : '';
$q2 = isset($_REQUEST['q2']) ? $_REQUEST['q2'] : '';
$q3 = isset($_REQUEST['q3']) ? $_REQUEST['q3'] : '';
$q4 = isset($_REQUEST['q4']) ? $_REQUEST['q4'] : '';
$q5 = isset($_REQUEST['q5']) ? $_REQUEST['q5'] : '';

$sql = "
       select 
a.id_customer,
a.nama_customer,
a.kode_pelanggan,
a.email_customer,
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
k.tanggal_pr,
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
	SELECT id_pr, MAX(tanggal_pr) as tanggal_pr FROM pro_pr GROUP BY id_pr
	) k on j.id_pr = k.id_pr
    LEFT JOIN (
        select id_customer, MAX(created_time) as created_time FROM pro_penawaran GROUP BY id_customer
        ) l on a.id_customer = l.id_customer 

where
1=1
    ";

if (($sesrol == 11 || $sesrol == 17)) {
    $sql .= " and a.id_marketing = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "'";
} else if ($sesrol == 18) {
    if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group'])) {
        $sql .= " and (a.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "' or a.id_marketing = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "')";
    } else if (!paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group'])) {
        $sql .= " and (a.id_group = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']) . "' or a.id_marketing = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "')";
    }
} else if (($sesrol == 11 || $sesrol == 17) && $q1 != "") {
    $sql .= "";
} else if ($sesrol == 6) {
    $sql .= " and ((d.id_role = 11 and a.id_group = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']) . "') or (d.id_role = 17 and d.id_om = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "'))";
} else if ($sesrol == 7) {
    $sql .= " and (d.id_role = 11 and a.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "')";
}

if ($q1 != "")
    $sql .= " and (upper(a.nama_customer) like '%" . strtoupper($q1) . "%' or a.kode_pelanggan = '" . $q1 . "')";
if ($q2 != "")
    $sql .= " and a.status_customer = '" . $q2 . "'";
if ($q3 != "")
    $sql .= " and a.id_wilayah = '" . $q3 . "'";

if ($q4 != "") {
    $enamBulanYangLalu = strtotime("-6 months");

    if ($q4 == "1") {
        // Filter untuk tampilkan data dengan Last Order > 6 bulan
        $sql .= " AND DATE_ADD(k.tanggal_pr, INTERVAL 6 MONTH) <= NOW()";
    } elseif ($q4 == "2") {
        // Filter untuk tampilkan data dengan Last Order < 6 bulan
        $sql .= " AND DATE_ADD(k.tanggal_pr, INTERVAL 6 MONTH) > NOW()";
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


$sql .= " order by a.id_customer desc";

$data_ = [];
$status = array(1 => "Prospect", "Evaluasi", "Tetap");
$result = $con->getResult($sql);
foreach ($result as $data) {
    $data = (object) $data;
    $tmp_addr = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data->nama_kab));
    $alamat = $data->alamat_customer . " " . ucwords($tmp_addr) . " " . $data->nama_prov;
    $tgl_remain = date("Y/m/d", strtotime("+" . $data->remaining . " days"));
    $statEval = ($data->status_customer == 2) ? $data->remaining . ' hari menuju evaluasi [' . tgl_indo($tgl_remain, 'short') . ']' : '';
    if ($data->status_customer == 1) {
        $status_customer = $status[$data->status_customer];
    } else 
        if ($data->status_customer == 2) {
        $status_customer = $status[$data->status_customer] . ' [' . tgl_indo($data->prospect_customer_date, 'short') . ']';
    } else 
        if ($data->status_customer == 3) {
        $status_customer = $status[$data->status_customer] . ' [' . tgl_indo($data->fix_customer_redate, 'short') . ']';
    }
    $data->alamat = $alamat;
    $data->status_customer_ = $status_customer;
    $data->status_eval = $statEval;
    $data_[] = $data;
}
$content = [];
foreach ($data_ as $i => $row) {
    $content[] = array(
        ($i + 1),
        ($row->kode_pelanggan ? $row->kode_pelanggan : '--------'),
        $row->nama_customer,
        preg_replace('/[^a-zA-Z0-9_@.\']/s', '', $row->email_customer),
        $row->fullname,
        $row->alamat,
        $row->telp_customer,
        (string) preg_replace('/[^a-zA-Z0-9_\']/s', '', $row->fax_customer),
        $row->nama_cabang,
        $row->status_customer_,
        tgl_indo($row->created_time),
        tgl_indo($row->tanggal_pr),
        $row->status_eval,
        (int) ($row->jum_lcr ?? '0')
    );
}

$filename = "Data-Customer-" . date('dmYHis') . '.xlsx';

// header('Content-type: application/vnd-ms-excel');
header('Content-Disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
ob_end_clean();
header('Content-type: application/vnd.ms-excel');
// header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');

$sheet  = 'Sheet1';
$writer = new XLSXWriter();
$writer->writeSheetHeader($sheet, array('Data Customer' => 'string'));
$writer->newMergeCell($sheet, 'A1', 'L1');
$writer->writeSheetHeaderExt($sheet, array("" => "string"));
// $writer->setColumnIndex(2);

$header = array(
    'No' => 'string',
    'Kode Customer' => 'string',
    'Nama Customer' => 'string',
    'Email Customer' => 'string',
    'Marketing' => 'string',
    'Alamat Customer' => 'string',
    'No. Telepon Customer' => 'string',
    'No. Fax. Customer' => 'string',
    'Cabang Invoice' => 'string',
    'Status' => 'string',
    'Last Quotation' => 'string',
    'Last Order' => 'string',
    'Evaluasi' => 'string',
    'LCR' => 'string'
);
$writer->writeSheetHeaderExt($sheet, $header);

if (count($data_) > 0) {
    foreach ($content as $row) {
        $writer->writeSheetRow($sheet, $row);
    }
} else {
    $writer->writeSheetRow($sheet, array('Data tidak ada'));
    $writer->newMergeCell($sheet, 'A4', 'L4');
}

$con->close();
$writer->writeToStdOut();
exit(0);

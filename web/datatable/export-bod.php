<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth    = new MyOtentikasi();
$con     = new Connection();
$draw     = isset($_POST["element"]) ? htmlspecialchars($_POST["element"], ENT_QUOTES) : 0;
$start     = isset($_POST["start"]) ? htmlspecialchars($_POST["start"], ENT_QUOTES) : 0;
$length    = isset($_POST['length']) ? htmlspecialchars($_POST["length"], ENT_QUOTES) : 25;
$q1    = isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';
$q2    = isset($_POST["q2"]) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : '';

$seswil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$data_ = array();

$p = new paging;
$sql1 = "
select a.id_customer, a.kode_pelanggan, a.nama_customer, c.fullname, d.nama_cabang, 
a.top_payment, a.credit_limit, 
b.not_yet, b.ov_up_07, b.ov_under_30, b.ov_under_60, b.ov_under_90, b.ov_up_90,
(b.not_yet + b.ov_up_07 + b.ov_under_30 + b.ov_under_60 + b.ov_under_90 + b.ov_up_90) as utangnya, 
(a.credit_limit - (b.not_yet + b.ov_up_07 + b.ov_under_30 + b.ov_under_60 + b.ov_under_90 + b.ov_up_90)) as reminding,
a.credit_limit - (a.credit_limit - (b.not_yet + b.ov_up_07 + b.ov_under_30 + b.ov_under_60 + b.ov_under_90 + b.ov_up_90)) as jumlah_sisa
from pro_customer a 
join pro_customer_admin_arnya b on a.id_customer = b.id_customer 
join acl_user c on a.id_marketing = c.id_user 
join pro_master_cabang d on a.id_wilayah = d.id_master 
where 1=1  
	";

if ($q1) {
    $sql1 .= " and (upper(a.nama_customer) like '%" . strtoupper($q1) . "%')";
}



$tot_record = $con->num_rows($sql1);
$tot_page     = ceil($tot_record / $length);
$page        = ($start > $tot_page) ? $start - 1 : $start;
$position     = $p->findPosition($length, $tot_record, $page);
$sql1 .= " order by jumlah_sisa desc limit " . $position . ", " . $length;

$content = "";
$count = 0;
if ($tot_record <= 0) {
    $content .= '<tr><td colspan="11" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
    $count         = $position;
    $tot_page     = ceil($tot_record / $length);
    $result     = $con->getResult($sql1);
    foreach ($result as $data) {
        $count++;
        $utangnya     = $data['not_yet'] + $data['ov_up_07'] + $data['ov_under_30'] + $data['ov_under_60'] + $data['ov_under_90'] + $data['ov_up_90'];
        $reminding     = $data['credit_limit'] - $utangnya;

        $content .= '
			<tr class="">
				<td class="text-center">' . $count . '</td>
				<td class="text-left">
					<p style="margin-bottom:3px;"><b>' . ($data['kode_pelanggan'] ? $data['kode_pelanggan'] : '-------') . '</b></p>
					<p style="margin-bottom:3px;">' . $data['nama_customer'] . '</p>
					<p style="margin-bottom:3px;">Cabang Penagihan : ' . $data['nama_cabang'] . '</p>
					<p style="margin-bottom:0px;"><i>' . $data['fullname'] . '</i></p>
				</td>
				<td class="text-center">' . ($data['top_payment'] ? $data['top_payment'] : '-') . '</td>
				<td class="text-right">' . number_format($data['credit_limit']) . '</td>
				<td class="text-right">' . number_format($data['not_yet']) . '</td>
				<td class="text-left">
					<div style="display:table; width:100%">
						<div style="display:table-row;">
							<div style="display:table-cell; width:80px;">Up 1-6 : </div>
							<div style="display:table-cell; text-align:right;">' . number_format($data['ov_up_07']) . '</div>
						</div>
						<div style="display:table-row;">
							<div style="display:table-cell; width:80px;">Under 7-30 : </div>
							<div style="display:table-cell; text-align:right;">' . number_format($data['ov_under_30']) . '</div>
						</div>
						<div style="display:table-row;">
							<div style="display:table-cell; width:80px;">Under 31-60 : </div>
							<div style="display:table-cell; text-align:right;">' . number_format($data['ov_under_60']) . '</div>
						</div>
						<div style="display:table-row;">
							<div style="display:table-cell; width:80px;">Under 61-90 : </div>
							<div style="display:table-cell; text-align:right;">' . number_format($data['ov_under_90']) . '</div>
						</div>
						<div style="display:table-row;">
							<div style="display:table-cell; width:80px;">Up 90 : </div>
							<div style="display:table-cell; text-align:right;">' . number_format($data['ov_up_90']) . '</div>
						</div>
					</div>					
				</td>
                <td class="text-right">' . number_format($data['reminding']) . '</td>
                <td class="text-right">' . number_format($data['jumlah_sisa']) . '</td>
			</tr>';
    }
}

$json_data = array(
    "items"        => $content,
    "pages"        => $tot_page,
    "page"        => $page,
    "totalData"    => $tot_record,
    "infoData"    => "Showing " . ($position + 1) . " - " . $count . " of " . $tot_record . " entries",
);
echo json_encode($json_data);

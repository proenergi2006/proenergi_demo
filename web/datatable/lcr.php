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
$q4	= isset($_POST["q4"]) ? htmlspecialchars($_POST["q4"], ENT_QUOTES) : '';
$q5	= isset($_POST["q5"]) ? htmlspecialchars($_POST["q5"], ENT_QUOTES) : '';
$q6	= isset($_POST["q6"]) ? htmlspecialchars($_POST["q6"], ENT_QUOTES) : '';
$q7	= isset($_POST["q7"]) ? htmlspecialchars($_POST["q7"], ENT_QUOTES) : '';

$p = new paging;
$sql = "select a.*, b.nama_prov, c.nama_kab, d.nama_customer, d.kode_pelanggan, e.fullname, f.nama_cabang 
			from pro_customer_lcr a 
			join pro_master_provinsi b on a.prov_survey = b.id_prov join pro_master_kabupaten c on a.kab_survey = c.id_kab 
			join pro_customer d on a.id_customer = d.id_customer join acl_user e on d.id_marketing = e.id_user
			join pro_master_cabang f on a.id_wilayah = f.id_master where 1=1";

if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 11 || paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 17)
	$sql .= " and d.id_marketing = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "'";
else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 18) {
	if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']))
		$sql .= " and (d.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "' or d.id_marketing = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "')";
	else if (!paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']))
		$sql .= " and (d.id_group = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']) . "' or d.id_marketing = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "')";
} else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 9)
	$sql .= " and a.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 6)
	$sql .= " and ((e.id_role = 11) or (e.id_role = 17))";

if ($q1 != "")
	$sql .= " and (upper(d.nama_customer) like '%" . strtoupper($q1) . "%' or d.kode_pelanggan = '" . $q1 . "')";
if ($q2 != "")
	$sql .= " and a.flag_approval = '" . $q2 . "'";
if ($q3 != "")
	$sql .= " and b.id_prov = '" . $q3 . "'";
if ($q4 != "")
	$sql .= " and c.id_kab = '" . $q4 . "'";
if ($q5 != "")
	$sql .= " and d.id_wilayah = '" . $q5 . "'";
if ($q6 != "" && $q7 != "") {
	$sql .= " and (a.tgl_survey between '" . tgl_db($q6) . "' and  '" . tgl_db($q7) . "')";
} else {
	if ($q6 != "")
		$sql .= " and (a.tgl_survey = '" . tgl_db($q6) . "')";
	if ($q7 != "")
		$sql .= " and (a.tgl_survey = '" . tgl_db($q7) . "')";
}

$tot_record = $con->num_rows($sql);
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);
$sql .= " order by a.id_lcr desc limit " . $position . ", " . $length;
// $sql .= " order by a.flag_disposisi asc limit ".$position.", ".$length; ini jika ingin muncul notif terbaru

$content = "";
$count = 0;
if ($tot_record ==  0) {
	$content .= '<tr><td colspan="9" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
	$count 		= $position;
	$tot_page 	= ceil($tot_record / $length);
	$result 	= $con->getResult($sql);
	foreach ($result as $data) {
		$count++;
		$tmp1_loka 	= strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
		$alamatLoka = $data['alamat_survey'] . " " . ucwords($tmp1_loka) . " " . $data['nama_prov'];
		$surveyor	= "";
		$arSurveyor	= json_decode($data['nama_surveyor'], true);
		$linkView 	= BASE_URL_CLIENT . '/lcr-detail.php?' . paramEncrypt('idr=' . $data['id_customer'] . '&idk=' . $data['id_lcr']);
		$linkDel	= paramEncrypt("lcr#|#" . $data['id_customer'] . "#|#" . $data['id_lcr']);
		$background	= "";

		if (count($arSurveyor) > 0) {
			$surveyor = '<ul style="margin:0px; padding-left:12px;">';
			foreach ($arSurveyor as $nilai) {
				$surveyor .= '<li>' . $nilai . '</li>';
			}
			$surveyor .= '</ul>';
		}

		if (((paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 11 || paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 18 || paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 1 || paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 17)) && $data['flag_disposisi'] == -1)
			$background = ' style="background-color:#f5f5f5"';

		if (!$data['flag_disposisi'])
			$status = "Terdaftar";
		else if ($data['flag_approval'] == 1)
			$status = "Disetujui BM<br><i>" . date("d/m/Y H:i:s", strtotime($data['tgl_approval'])) . "</i>";
		else if ($data['flag_approval'] == 2)
			$status = "Ditolak BM<br><i>" . date("d/m/Y H:i:s", strtotime($data['tgl_approval'])) . "</i>";
		else if ($data['flag_disposisi'] == -1)
			$status = "Ditolak Logistik";
		else if ($data['flag_disposisi'] == 1)
			$status = "Verifikasi Logistik";
		else if ($data['flag_disposisi'] == 2)
			$status = "Verifikasi BM";
		if (!$data['layout_lokasi'] && !$data['layout_bongkar'] && !$data['kantor_perusahaan'] && !$data['fasilitas_storage'] && !$data['inlet_pipa'] && !$data['alat_ukur_gambar'] && !$data['media_datar'] && !$data['keterangan_lain'])
			$attachment = '';
		else $attachment = '<i class="fa fa-paperclip"></i>';

		$content .= '
				<tr class="clickable-row" data-href="' . $linkView . '"' . $background . '>
					<td class="text-center">' . $count . '</td>
					<td>
						<p style="margin-bottom:0px"><b>LCR' . str_pad($data['id_lcr'], 4, '0', STR_PAD_LEFT) . '</b></p>
						<p style="margin-bottom:0px">' . date("d/m/Y", strtotime($data['created_time'])) . '</p>
					</td>
					<td>
						<p style="margin-bottom:0px"><b>' . ($data['kode_pelanggan'] ? $data['kode_pelanggan'] : '-------') . '</b></p>
						<p style="margin-bottom:0px">' . $data['nama_customer'] . '</p>
						<p style="margin-bottom:0px"><i>' . $data['fullname'] . '</i></p>
					</td>
					<td>' . $surveyor . '<p style="margin:5px 0px 0px;">Tanggal ' . date("d/m/Y", strtotime($data['tgl_survey'])) . '</p></td>
					<td>
						<p style="margin-bottom:0px">' . $alamatLoka . '</p>
						<p style="margin-bottom:0px">' . $data['latitude_lokasi'] . ', ' . $data['longitude_lokasi'] . '</p>
					</td>
					<td>
						<p style="margin-bottom:0px">Jarak Depot : ' . ($data['jarak_depot'] ? $data['jarak_depot'] . ' Km' : '-') . '</p>
						<p style="margin-bottom:0px">Truck Max : ' . ($data['max_truk'] ? $data['max_truk'] : '-') . '</p>
						<!--<p style="margin-bottom:0px">Truck Min : ' . ($data['min_vol_kirim'] ? $data['min_vol_kirim'] . ' KL' : '-') . '</p>-->
					</td>
					<td>' . $status . '</td>
					<td class="text-center">' . $attachment . '</td>
					<td class="text-center">
						<a class="btn btn-action btn-info" href="' . $linkView . '" style="margin-right:3px;"><i class="fa fa-info-circle"></i></a>
						' . (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array('1', '11', '17', '9')) ?
			'<a class="btn btn-action btn-danger" data-param-idx="' . $linkDel . '" data-action="deleteGrid"><i class="fa fa-trash"></i></a>' : ''
		) . '
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
//var_dump($json_data);exit;

echo json_encode($json_data);

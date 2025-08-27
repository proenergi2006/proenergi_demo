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

$p = new paging;
$sql = "select a.*, c.tanggal_kirim, d.produk_poc, e.nama_customer, f.nama_suplier, b.pr_terminal, g.id_area, h.alamat_survey, i.nama_prov, j.nama_kab, k.wilayah_angkut, 
			m.nama_terminal, m.tanki_terminal, m.lokasi_terminal, b.produk, b.pr_vendor 
			from pro_po_ds_kapal a 
			join pro_pr_detail b on a.id_prd = b.id_prd 
			join pro_po_customer_plan c on b.id_plan = c.id_plan 
			join pro_po_customer d on c.id_poc = d.id_poc 
			join pro_customer e on d.id_customer = e.id_customer 
			join pro_master_transportir f on a.transportir = f.id_master 
			join pro_penawaran g on d.id_penawaran = g.id_penawaran 
			join pro_customer_lcr h on c.id_lcr = h.id_lcr
			join pro_master_provinsi i on h.prov_survey = i.id_prov 
			join pro_master_kabupaten j on h.kab_survey = j.id_kab 
			join pro_master_wilayah_angkut k on h.id_wil_oa = k.id_master and h.prov_survey = k.id_prov and h.kab_survey = k.id_kab 
			join pro_master_area l on g.id_area = l.id_master 
			join pro_master_terminal m on a.terminal = m.id_master 
			where a.terminal = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["terminal"]) . "'";

if ($q1 != "")
	$sql .= " and (upper(a.nomor_dn_kapal) like '" . strtoupper($q1) . "%' or upper(a.notify_nama) like '%" . strtoupper($q1) . "%' 
				or upper(a.vessel_name) like '%" . strtoupper($q1) . "%' or upper(a.kapten_name) like '%" . strtoupper($q1) . "%' or upper(e.nama_customer) like '%" . strtoupper($q1) . "%')";
if ($q2 != "" && $q3 == "")
	$sql .= " and c.tanggal_kirim = '" . tgl_db($q2) . "'";
else if ($q2 != "" && $q3 != "")
	$sql .= " and c.tanggal_kirim between '" . tgl_db($q2) . "' and '" . tgl_db($q3) . "'";

if ($q4 != "" && $q4 == "1")
	$sql .= " and a.is_loaded = 0 and a.is_delivered = 0 and a.is_cancel = 0";
else if ($q4 != "" && $q4 == "2")
	$sql .= " and a.is_loaded = 1 and a.is_delivered = 0 and a.is_cancel = 0";
else if ($q4 != "" && $q4 == "3")
	$sql .= " and a.is_loaded = 1 and a.is_delivered = 1";
else if ($q4 != "" && $q4 == "4")
	$sql .= " and a.is_loaded = 1 and a.is_cancel = 1";

$tot_record = $con->num_rows($sql);
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);
$sql .= "  order by c.tanggal_kirim desc, a.id_dsk limit " . $position . ", " . $length;

$content = "";
$count = 0;
if ($tot_record <= 0) {
	$content .= '<tr><td colspan="7" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
	$count 		= $position;
	$tot_page 	= ceil($tot_record / $length);
	$result 	= $con->getResult($sql);
	foreach ($result as $data) {
		$count++;
		$idp 		= $data["id_dsk"];
		$volpo		= $data['bl_lo_jumlah'];
		$tempal 	= strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
		$alamat		= $data['alamat_survey'] . " " . ucwords($tempal) . " " . $data['nama_prov'];
		$produk_poc	= $data['produk_poc'];
		$tgl_loaded	= $data['tanggal_loaded'];
		$jam_loaded	= $data['jam_loaded'];
		$pr_vendor	= $data['pr_vendor'];
		$id_area	= $data['id_area'];
		$etl     	= $data['tgl_etl'];

		$terminal1 	= $data['nama_terminal'];
		$terminal2 	= ($data['tanki_terminal']) ? ' - ' . $data['tanki_terminal'] : '';
		$terminal3 	= ($data['lokasi_terminal']) ? '<br />' . $data['lokasi_terminal'] : '';
		$terminal 	= $terminal1 . $terminal2 . $terminal3;
		$nama_cust  = $data['nama_customer'];

		$dataInfo1 	= 'kapal#' . $data['nomor_dn_kapal'] . '#' . $data['nama_customer'] . '#' . $alamat . '#' . $data['wilayah_angkut'] . '#' . $volpo . '#' . $data['produk'];
		$dataInfo2 	= '#' . $data['nama_suplier'] . '#' . $data['no_spj'] . '#' . $data['vessel_name'] . '#' . $data['kapten_name'];
		$linkInfo 	= paramEncrypt($dataInfo1 . $dataInfo2);

		$linkCancel = paramEncrypt("do_kapal#|#cancel#|#" . $idp . "#|#" . $volpo . "#|#" . $produk_poc . "#|#" . $tgl_loaded . "#|#" . $jam_loaded . "#|#" . $pr_vendor . "#|#" . $id_area);
		$linkRevert	= paramEncrypt("do_kapal#|#revert#|#" . $idp . "#|#" . $volpo . "#|#" . $produk_poc . "#|#" . $tgl_loaded . "#|#" . $jam_loaded . "#|#" . $pr_vendor . "#|#" . $id_area);
		$linkParam 	= paramEncrypt("do_kapal#|#loading#|#" . $idp . "#|#" . $volpo . "#|#" . $produk_poc . "#|#" . $tgl_loaded . "#|#" . $jam_loaded . "#|#" . $pr_vendor . "#|#" . $id_area);

		if ($data['is_delivered']) {
			$background = '';
			$status 	= '<p style="margin-bottom:0px;"><b>Delivered</b><br/>' . date("d/m/Y H:i", strtotime($data['tanggal_delivered'])) . '</p>';
		} else if ($data['is_cancel']) {
			$background = ' style="background-color:#f0f0f0"';
			$status 	= '
					<p style="margin-bottom:0px;" class="text-red"><b>Canceled</b><br/>' . date("d/m/Y H:i", strtotime($data['tanggal_cancel'])) . '</p>
					<div class="text-left">
						<a data-jenis="revert" data-param="' . $linkRevert . '" data-info="' . $linkInfo . '" class="resetSts btn btn-warning btn-action" 
						title="Reset Loading" style="margin-right:3px;"><i class="fa fa-retweet"></i> Reset</a>
					</div>';
		} else if ($data['is_loaded']) {
			$background = '';
			$status 	= '
					<p style="margin-bottom:5px;"><b>Loaded</b><br/>' . date("d/m/Y", strtotime($data['tanggal_loaded'])) . ' ' . $data['jam_loaded'] . '</p>
					<div class="text-left">
						<a data-jenis="revert" data-param="' . $linkRevert . '" data-info="' . $linkInfo . '" class="resetSts btn btn-warning btn-action" 
						title="Reset Loading" style="margin-right:3px;"><i class="fa fa-retweet"></i> Reset</a>
						<a data-jenis="cancel" data-param="' . $linkCancel . '" data-info="' . $linkInfo . '" class="editStsT btn btn-danger btn-action" title="Pengiriman ditolak user">
						<i class="fa fa-times"></i> Reject</a>
					</div>
					' . ($data['catatan'] ? '<br/>' . $data['catatan'] : '');
		} else {
			$background = ' style="background-color:#f5f5f5"';
			$status 	= '
					<p style="margin-bottom:5px; padding-right:30px;"><i>Belum loading</i></p>
					<div class="text-left">
						<a data-jenis="loading" data-etl="' . $etl . '"  data-etl_val="' . $etl . '" data-terminal="' . $terminal . '" data-customer="' . $nama_cust . '" data-param="' . $linkParam . '" data-info="' . $linkInfo . '" class="editStsT btn btn-info btn-action" title="Loading">
						<i class="fa fa-info-circle"></i> Loading</a>
					</div>';
		}

		$linkCetak  = BASE_URL_CLIENT . "/terminal-view.php?" . paramEncrypt("idr=" . $idp . "&type=do_kapal");

		$content .= '
				<tr' . $background . '>
					<td class="text-center">' . $count . '</td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>' . $data['nomor_dn_kapal'] . '</b></p>
						<p style="margin-bottom:0px">' . $data['nama_customer'] . '</p>
						<p style="margin-bottom:0px">' . $alamat . '</p>
						<p style="margin-bottom:0px">Wilayah OA : ' . $data['wilayah_angkut'] . '</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>' . $data['notify_nama'] . '</b></p>
						<p style="margin-bottom:0px">' . number_format($volpo) . ' Liter ' . $data['produk'] . '</p>
						<p style="margin-bottom:0px">Tgl Kirim : ' . tgl_indo($data['tanggal_kirim'], 'short') . '</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>' . $data['nama_suplier'] . '</b></p>
						<p style="margin-bottom:0px">Vessel &nbsp;: ' . $data['vessel_name'] . '</p>
						<p style="margin-bottom:0px">Captain : ' . $data['kapten_name'] . '</p>
					</td>
					<td class="text-left">' . $terminal . '</td>
					<td class="text-left">' . $status . '</td>
					<td class="text-center">
						' . (!$data['is_cancel'] ? '<input type="checkbox" name="chk[' . $idp . ']" id="chk' . $count . '" class="chkp2" value="1" />' : '&nbsp;') . '
						<a class="margin-sm btn btn-action btn-success" title="Cetak" href="' . $linkCetak . '" target="_blank"><i class="fa fa-print"></i></a>
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
echo json_encode($json_data);

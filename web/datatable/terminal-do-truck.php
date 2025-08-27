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
$arrTgl = array(1 => "m.tanggal_po", "b.tgl_kirim_po", "a.tanggal_loading", "a.tanggal_loaded");

$q1	= isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';
$q2	= isset($_POST["q2"]) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : '';
$q3	= isset($_POST["q3"]) ? htmlspecialchars($_POST["q3"], ENT_QUOTES) : '';
$q4	= isset($_POST["q4"]) ? htmlspecialchars($_POST["q4"], ENT_QUOTES) : '';
$q5	= isset($_POST["q5"]) ? htmlspecialchars($_POST["q5"], ENT_QUOTES) : '';

$p = new paging;
$sql = "select a.*, c.pr_pelanggan, i.nama_customer, e.alamat_survey, f.nama_prov, g.nama_kab, j.fullname, n.nama_transportir, n.nama_suplier, b.no_spj, k.nomor_plat, k.link_gps,
			l.nama_sopir, b.volume_po, h.produk_poc, p.id_area, c.pr_vendor, r.nama_terminal, r.tanki_terminal, r.lokasi_terminal, s.wilayah_angkut, m.nomor_po, m.tanggal_po, m.id_wilayah as id_wilayah_po,
			c.produk, b.tgl_kirim_po, b.tgl_etl_po,
			i.id_customer,
			m.created_by as pic_logistik,
			d.created_by as pic_cs,
			j.id_user as pic_marketing 
			from pro_po_ds_detail a 
			join pro_po_ds o on a.id_ds = o.id_ds 
			join pro_po_detail b on a.id_pod = b.id_pod 
			join pro_po m on a.id_po = m.id_po 
			join pro_pr_detail c on a.id_prd = c.id_prd 
			join pro_po_customer_plan d on a.id_plan = d.id_plan 
			join pro_po_customer h on d.id_poc = h.id_poc 
			join pro_customer_lcr e on d.id_lcr = e.id_lcr
			join pro_customer i on h.id_customer = i.id_customer 
			join acl_user j on i.id_marketing = j.id_user 
			join pro_master_provinsi f on e.prov_survey = f.id_prov 
			join pro_master_kabupaten g on e.kab_survey = g.id_kab
			join pro_penawaran p on h.id_penawaran = p.id_penawaran  
			join pro_master_area q on p.id_area = q.id_master 
			join pro_master_transportir_mobil k on b.mobil_po = k.id_master 
			join pro_master_transportir_sopir l on b.sopir_po = l.id_master
			join pro_master_transportir n on m.id_transportir = n.id_master 
			join pro_master_terminal r on o.id_terminal = r.id_master 
			join pro_master_wilayah_angkut s on e.id_wil_oa = s.id_master and e.prov_survey = s.id_prov and e.kab_survey = s.id_kab
			where o.is_submitted = 1 and o.id_terminal = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["terminal"]) . "'";

if ($q1 != "")
	$sql .= " and (upper(a.nomor_do) like '" . strtoupper($q1) . "%' or upper(b.no_spj) = '" . strtoupper($q1) . "' or upper(k.nomor_plat) = '" . strtoupper($q1) . "' 
					or upper(l.nama_sopir) like '%" . strtoupper($q1) . "%' or upper(i.nama_customer) like '%" . strtoupper($q1) . "%')";
if ($q2 != "" && $q3 != "" && $q4 == "")
	$sql .= " and " . $arrTgl[$q2] . " = '" . tgl_db($q3) . "'";
else if ($q2 != "" && $q3 != "" && $q4 != "")
	$sql .= " and " . $arrTgl[$q2] . " between '" . tgl_db($q3) . "' and '" . tgl_db($q4) . "'";

if ($q5 != "" && $q5 == "1")
	$sql .= " and a.is_loaded = 0 and a.is_delivered = 0 and a.is_cancel = 0";
else if ($q5 != "" && $q5 == "2")
	$sql .= " and a.is_loaded = 1 and a.is_delivered = 0 and a.is_cancel = 0";
else if ($q5 != "" && $q5 == "3")
	$sql .= " and a.is_loaded = 1 and a.is_delivered = 1";
else if ($q5 != "" && $q5 == "4")
	$sql .= " and a.is_loaded = 1 and a.is_cancel = 1";

$tot_record = $con->num_rows($sql);
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);
$sql .= "  order by a.tanggal_loading desc, a.jam_loading, a.nomor_urut_ds, a.id_dsd limit " . $position . ", " . $length;

$content = "";
if ($tot_record <= 0) {
	$content .= '<tr><td colspan="7" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
	$count 		= $position;
	$tot_page 	= ceil($tot_record / $length);
	$result 	= $con->getResult($sql);
	foreach ($result as $data) {
		$count++;
		$idp 		= $data["id_dsd"];
		$tempal 	= strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
		$alamat		= $data['alamat_survey'] . " " . ucwords($tempal) . " " . $data['nama_prov'];
		$volume_po	= $data['volume_po'];
		$produk_poc	= $data['produk_poc'];
		$tgl_loaded	= $data['tanggal_loaded'];
		$jam_loaded	= $data['jam_loaded'];
		$pr_vendor	= $data['pr_vendor'];
		$id_area	= $data['id_area'];
		$id_customer = $data['id_customer'];
		$pic_logistik = $data['pic_logistik'];
		$pic_cs 	= $data['pic_cs'];
		$pic_marketing = $data['pic_marketing'];
		$nama_cust  = $data['nama_customer'];
		$no_plat  = $data['nomor_plat'];
		$nama_sopir  = $data['nama_sopir'];
		$etl         = $data['tgl_etl_po'];

		$terminal1 	= $data['nama_terminal'];
		$terminal2 	= ($data['tanki_terminal']) ? ' - ' . $data['tanki_terminal'] : '';
		$terminal3 	= ($data['lokasi_terminal']) ? '<br />' . $data['lokasi_terminal'] : '';
		$terminal 	= $terminal1 . $terminal2 . $terminal3;

		$dataInfo1 	= 'truck#' . $data['nomor_do'] . '#' . $data['nama_customer'] . '#' . $alamat . '#' . $data['wilayah_angkut'] . '#' . $volume_po . '#' . $data['produk'];
		$dataInfo2 	= '#' . $data['nama_suplier'] . '#' . $data['no_spj'] . '#' . $data['nomor_plat'] . '#' . $data['nama_sopir'];
		$linkInfo 	= paramEncrypt($dataInfo1 . $dataInfo2);

		$linkCancel = paramEncrypt("do_truck#|#cancel#|#" . $idp . "#|#" . $volume_po . "#|#" . $produk_poc . "#|#" . $tgl_loaded . "#|#" . $jam_loaded . "#|#" . $pr_vendor . "#|#" . $id_area . '#|#' . $id_customer . '#|#' . $pic_logistik . '#|#' . $pic_cs . '#|#' . $pic_marketing . '#|#' . $nama_cust . '#|#' . $terminal . '#|#' . $no_plat . '#|#' . $nama_sopir);
		$linkRevert	= paramEncrypt("do_truck#|#revert#|#" . $idp . "#|#" . $volume_po . "#|#" . $produk_poc . "#|#" . $tgl_loaded . "#|#" . $jam_loaded . "#|#" . $pr_vendor . "#|#" . $id_area . '#|#' . $id_customer . '#|#' . $pic_logistik . '#|#' . $pic_cs . '#|#' . $pic_marketing . '#|#' . $nama_cust . '#|#' . $terminal . '#|#' . $no_plat . '#|#' . $nama_sopir);
		$linkParam 	= paramEncrypt("do_truck#|#loading#|#" . $idp . "#|#" . $volume_po . "#|#" . $produk_poc . "#|#" . $tgl_loaded . "#|#" . $jam_loaded . "#|#" . $pr_vendor . "#|#" . $id_area . '#|#' . $id_customer . '#|#' . $pic_logistik . '#|#' . $pic_cs . '#|#' . $pic_marketing . '#|#' . $nama_cust . '#|#' . $terminal . '#|#' . $no_plat . '#|#' . $nama_sopir);

		$seg_aw 	= ($data['nomor_segel_awal']) ? str_pad($data['nomor_segel_awal'], 4, '0', STR_PAD_LEFT) : '';
		$seg_ak 	= ($data['nomor_segel_akhir']) ? str_pad($data['nomor_segel_akhir'], 4, '0', STR_PAD_LEFT) : '';
		if ($data['jumlah_segel'] == 1)
			$nomor_segel = $data['pre_segel'] . "-" . $seg_aw;
		else if ($data['jumlah_segel'] == 2)
			$nomor_segel = $data['pre_segel'] . "-" . $seg_aw . " &amp; " . $data['pre_segel'] . "-" . $seg_ak;
		else if ($data['jumlah_segel'] > 2)
			$nomor_segel = $data['pre_segel'] . "-" . $seg_aw . " s/d " . $data['pre_segel'] . "-" . $seg_ak;
		else $nomor_segel = '';

		if ($data['is_delivered']) {
			$background = '';
			$status 	= '<p style="margin-bottom:0px;"><b>Delivered</b><br/>' . date("d/m/Y H:i", strtotime($data['tanggal_delivered'])) . '</p>';
		} else if ($data['is_cancel']) {
			$background = ' style="background-color:#f0f0f0"';
			$status 	= '
					<p style="margin-bottom:5px;" class="text-red"><b>Canceled</b><br/>' . date("d/m/Y H:i", strtotime($data['tanggal_cancel'])) . '</p>
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
			// if ($data['link_gps'] == 'OSLOG') {
			// 	if ($data['id_wilayah_po'] == '2' && $data['tanggal_loading'] >= '2024-01-01') {
			// 		$mobilnya = '<a class="openModalTracking" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
			// 	} elseif ($data['id_wilayah_po'] == '4' && $data['tanggal_loading'] >= '2024-02-01') {
			// 		$mobilnya = '<a class="openModalTracking" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
			// 	} elseif ($data['id_wilayah_po'] == '6' || $data['id_wilayah_po'] == '3' && $data['tanggal_loading'] >= '2024-03-01') {
			// 		$mobilnya = '<a class="openModalTracking" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
			// 	} else {
			// 		$mobilnya = '<a style="color:black;" class="" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
			// 	}
			// } else {
			// 	$mobilnya = '<a style="color:black;" class="" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
			// }
		} else {
			$background = '';
			$status 	= '
					<p style="margin-bottom:5px; padding-right:30px;"><i>Belum loading</i></p>
					<div class="text-left">
						<a data-jenis="loading" data-param="' . $linkParam . '" data-info="' . $linkInfo . '" data-etl="' . $etl . '" data-etl_val="' . $etl . '" class="editStsT btn btn-info btn-action" title="Loading">
						<i class="fa fa-info-circle"></i> Loading</a>
					</div>';

			// if ($data['link_gps'] == 'OSLOG') {
			// 	if ($data['id_wilayah_po'] == '2' && $data['tanggal_loading'] >= '2024-01-01') {
			// 		$mobilnya = '<a class="openModalTracking" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
			// 	} elseif ($data['id_wilayah_po'] == '4' && $data['tanggal_loading'] >= '2024-02-01') {
			// 		$mobilnya = '<a class="openModalTracking" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
			// 	} elseif ($data['id_wilayah_po'] == '6' || $data['id_wilayah_po'] == '3' && $data['tanggal_loading'] >= '2024-03-01') {
			// 		$mobilnya = '<a class="openModalTracking" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
			// 	} else {
			// 		$mobilnya = '<a style="color:black;" class="" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
			// 	}
			// } else {
			// 	$mobilnya = '<a style="color:black;" class="" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
			// }
		}

		$linkCetak  = BASE_URL_CLIENT . "/terminal-view.php?" . paramEncrypt("idr=" . $idp . "&type=do_truck&code=1");
		$linkCetak2  = BASE_URL_CLIENT . "/terminal-view.php?" . paramEncrypt("idr=" . $idp . "&type=do_truck&code=2");

		$content .= '
				<tr' . $background . '>
					<td class="text-center">' . $count . '</td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>' . $data['nomor_do'] . '</b></p>
						<p style="margin-bottom:0px">' . $data['nama_customer'] . '</p>
						<p style="margin-bottom:0px">' . $alamat . '</p>
						<p style="margin-bottom:0px">Wilayah OA : ' . $data['wilayah_angkut'] . '</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>' . $data['nomor_po'] . '</b></p>
						<p style="margin-bottom:0px">' . number_format($volume_po) . ' Liter ' . $data['produk'] . '</p>
						<p style="margin-bottom:0px">Tgl PO &nbsp;&nbsp;&nbsp;: ' . tgl_indo($data['tanggal_po'], 'short') . '</p>
						<p style="margin-bottom:0px">Tgl Kirim : ' . tgl_indo($data['tgl_kirim_po'], 'short') . '</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>' . $data['nama_suplier'] . '</b></p>
						<p style="margin-bottom:0px">' . $data['no_spj'] . '</p>
						<p style="margin-bottom:0px">Truck &nbsp;: ' . $mobilnya . '</p>
						<p style="margin-bottom:0px">Driver : ' . $data['nama_sopir'] . '</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>' . $terminal . '</b></p>
						<p style="margin-bottom:0px">' . $nomor_segel . '</p>
						<p style="margin-bottom:0px">ETL : ' . tgl_indo($data['tanggal_loading'], 'short') . ' ' . date("H:i", strtotime($data['jam_loading'])) . '</p>
					</td>
					<td class="text-left">' . $status . '</td>
					<td class="text-center">' . (!$data['is_cancel'] ? '<input type="checkbox" name="cek[' . $idp . ']" id="cek' . $count . '" class="chkp" value="1" />' : '&nbsp;') . '</td>
					<td class="text-center"><a class="margin-sm btn btn-action btn-success" title="Cetak dengan inisial" href="' . $linkCetak . '" target="_blank"><i class="fa fa-print"></i></a><a class="margin-sm btn btn-action btn-warning" title="Cetak tanpa inisial" href="' . $linkCetak2 . '" target="_blank"><i class="fa fa-print"></i></a></td>
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

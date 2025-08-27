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
$arrTgl = array(1 => "m.tanggal_po", "b.tgl_kirim_po", "a.tanggal_loading", "b.tgl_eta_po");

$q1	= isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';
$q2	= isset($_POST["q2"]) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : '';
$q3	= isset($_POST["q3"]) ? htmlspecialchars($_POST["q3"], ENT_QUOTES) : '';
$q4	= isset($_POST["q4"]) ? htmlspecialchars($_POST["q4"], ENT_QUOTES) : '';
$q5	= isset($_POST["q5"]) ? htmlspecialchars($_POST["q5"], ENT_QUOTES) : '';
$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

$p = new paging;
$whereadd = '';
if ($sesrol > 1) {
	$whereadd = " and o.id_wilayah = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]) . "'";
}
$sql = "select a.*, c.pr_pelanggan, i.nama_customer, e.alamat_survey, f.nama_prov, g.nama_kab, j.fullname, n.nama_transportir, n.nama_suplier, b.no_spj, k.nomor_plat, k.link_gps, 
			l.nama_sopir, b.volume_po, h.produk_poc, p.id_area, c.pr_vendor, r.nama_terminal, r.tanki_terminal, r.lokasi_terminal, s.wilayah_angkut, h.tanggal_poc, m.nomor_po, m.tanggal_po, c.produk, b.tgl_kirim_po, b.mobil_po, c.no_do_acurate, c.no_do_syop, c.nomor_lo_pr,  c.id_po_supplier, c.id_po_receive,
			h.nomor_poc, d.tanggal_kirim, d.volume_kirim, m.id_wilayah as id_wilayah_po,b.tgl_eta_po, b.jam_eta_po, b.tgl_etl_po,
			d.realisasi_kirim,
			o.is_loco,
			i.id_customer,
			m.created_by as pic_logistik,
			d.created_by as pic_cs,
			j.id_user as pic_marketing, o.id_terminal, p.masa_akhir
			from pro_po_ds_detail a 
			join pro_po_ds o on a.id_ds = o.id_ds 
			join pro_po_detail b on a.id_pod = b.id_pod 
			join pro_po m on a.id_po = m.id_po 
			join pro_pr_detail c on a.id_prd = c.id_prd 
			join pro_po_customer_plan d on a.id_plan = d.id_plan 
			join pro_po_customer h on a.id_poc = h.id_poc 
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
			where 1=1 " . $whereadd;

if ($q1 != "")
	$sql .= " and (upper(a.nomor_do) like '" . strtoupper($q1) . "%' or upper(b.no_spj) = '" . strtoupper($q1) . "' or upper(k.nomor_plat) = '" . strtoupper($q1) . "' 
	or upper(c.nomor_lo_pr) like '%" . strtoupper($q1) . "%'	or upper(l.nama_sopir) like '%" . strtoupper($q1) . "%' or upper(i.nama_customer) like '%" . strtoupper($q1) . "%')";
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
else if ($q5 != "" && $q5 == "5")
	$sql .= " and a.is_loaded = 0 and a.is_cancel = 1";

$tot_record = $con->num_rows($sql);
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);
$sql .= "  order by a.is_loaded desc, a.tanggal_loading desc, a.jam_loading, a.nomor_urut_ds, a.id_dsd limit " . $position . ", " . $length;
$link = BASE_URL_CLIENT . '/report/pengiriman-logistik-truck-exp.php?' . paramEncrypt('q1=' . $q1 . '&q2=' . $q2 . '&q3=' . $q3 . '&q4=' . $q4 . '&q5=' . $q5);

$count = 0;
$content = "";
if ($tot_record <= 0) {
	$content .= '<tr><td colspan="7" style="text-align:center"><input type="hidden" id="uriExp1" value="' . $link . '" />Data tidak ditemukan </td></tr>';
} else {
	$count 		= $position;
	$tot_page 	= ceil($tot_record / $length);
	$result 	= $con->getResult($sql);
	$array_id_dsd = array();
	foreach ($result as $data) {

		// if ($data['is_delivered'] == 0 && $data['link_gps'] == 'OSLOG' && $data['tanggal_loading'] >= '2023-01-01') {
		// 	array_push($array_id_dsd, $data['id_dsd']);
		// }

		$count++;
		$idp 		= $data["id_dsd"];
		$tempal 	= strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
		$alamat		= $data['alamat_survey'] . " " . ucwords($tempal) . " " . $data['nama_prov'];
		$volpo		= $data['volume_po'];
		$produk_poc	= $data['produk_poc'];
		$tgl_loaded	= $data['tanggal_loaded'];
		$jam_loaded	= $data['jam_loaded'];
		$pr_vendor	= $data['pr_vendor'];
		$id_area	= $data['id_area'];
		$is_loaded	= $data['is_loaded'];
		$masa_akhir	= $data['masa_akhir'];
		$etl     	= $data['tgl_etl_po'];
		$is_loco	= $data['is_loco'];

		$terminal1 	= $data['nama_terminal'];
		$terminal2 	= ($data['tanki_terminal']) ? ' - ' . $data['tanki_terminal'] : '';
		$terminal3 	= ($data['lokasi_terminal']) ? '<br />' . $data['lokasi_terminal'] : '';
		$terminal 	= $terminal1 . $terminal2 . $terminal3;

		$revert_summary	= $data['revert_summary'];

		$request = '';
		if ($data['is_request'] == 1) {
			$request = '<span style="color:red;">Change Depot</span>';
		} else if ($data['is_request'] == 2) {
			$request = '<span style="color:red;"> Reschedule</span>';
		} else if ($data['is_request'] == 3) {
			$request = '<span style="color:red;">Cancel</span>';
		} else {
			$request = '';
		}

		$status_request = '';
		if ($data['is_revert'] == 1) {
			$status_request = '<span style="color:green;">Disetujui</span>';
		} else if ($data['is_revert'] == 2) {
			$status_request = '<span style="color:red;"> Ditolak : ' . $revert_summary . '</span>';
		} else {
			$status_request = '';
		}

		$dataInfo1 	= 'truck#' . $data['nomor_do'] . '#' . $data['nama_customer'] . '#' . $alamat . '#' . $data['wilayah_angkut'] . '#' . $volpo . '#' . $data['produk'];
		$dataInfo2 	= '#' . $data['nama_suplier'] . '#' . $data['no_spj'] . '#' . $data['nomor_plat'] . '#' . $data['nama_sopir'];
		$linkInfo 	= paramEncrypt($dataInfo1 . $dataInfo2);

		$dataRequest1 	= "do_truck#|#request#|#" . $idp . "#|#" . $volpo . "#|#" . $produk_poc . "#|#" . $tgl_loaded . "#|#" . $jam_loaded . "#|#"  . $is_loaded . "#|#" . $pr_vendor . "#|#" . $id_area . "#|#" . $id_customer;
		$dataRequest2 	= "#|#" . $pic_logistik . "#|#" . $pic_cs . "#|#" . $pic_marketing . "#|#" . $nama_cust . "#|#" . $terminal . "#|#" . $no_plat . "#|#" . $nama_sopir . "#|#" . $data['id_terminal'] . "#|#" . $id_po_supplier . "#|#" . $id_po_receive;;
		$linkRequest	= paramEncrypt($dataRequest1 . $dataRequest2);

		$linkParam 	= paramEncrypt($data['id_dsd'] . "|#|" . $data['volume_po'] . "|#|" . $data['pic_cs'] . "|#|" . $data['pic_marketing'] . "|#|" . $data['id_customer'] . "|#|" . $data['nama_customer'] . "|#|" . $data['alamat_survey'] . '|#|' . $data['nomor_plat'] . '|#|' . $data['nama_sopir']);

		$seg_aw 	= ($data['nomor_segel_awal']) ? str_pad($data['nomor_segel_awal'], 4, '0', STR_PAD_LEFT) : '';
		$seg_ak 	= ($data['nomor_segel_akhir']) ? str_pad($data['nomor_segel_akhir'], 4, '0', STR_PAD_LEFT) : '';
		$eta_po		= (isset($data['tgl_eta_po']) and isset($data['jam_eta_po'])) ? date("d/m/Y", strtotime($data['tgl_eta_po'])) . ($data['jam_eta_po'] ? ' ' . date("H:i", strtotime($data['jam_eta_po'])) : '') : '';
		if ($data['jumlah_segel'] == 1)
			$nomor_segel = $data['pre_segel'] . "-" . $seg_aw;
		else if ($data['jumlah_segel'] == 2)
			$nomor_segel = $data['pre_segel'] . "-" . $seg_aw . " &amp; " . $data['pre_segel'] . "-" . $seg_ak;
		else if ($data['jumlah_segel'] > 2)
			$nomor_segel = $data['pre_segel'] . "-" . $seg_aw . " s/d " . $data['pre_segel'] . "-" . $seg_ak;
		else $nomor_segel = '';

		if ($data['is_delivered']) {
			$status = '<p style="margin-bottom:5px;"><b>Delivered</b><br/>' . date("d/m/Y H:i", strtotime($data['tanggal_delivered'])) . '</p>';
			$status_loaded = '<p style="margin-bottom:5px;"><b>Loaded</b><br/>' . date("d/m/Y", strtotime($data['tanggal_loaded'])) . " " . $data['jam_loaded'] . '</p>';
			if (!$data['realisasi_volume'] || !$data['terima_jalan']) {
				$status .= '<a data-info="' . $linkInfo . '" data-param="' . $linkParam . '" data-realisasi="1" data-status="realisasi" class="editStsT btn btn-success" 
								title="Realisasi Kirim dan Terima Surat Jalan"><i class="fa fa-sticky-note-o"></i> <span style="font-size:11px;">Realisasi</span></a>';
			}
			if ($data['link_gps'] == 'OSLOG') {
				if ($data['id_wilayah_po'] == '2' && $data['tanggal_loading'] >= '2024-01-01') {
					$mobilnya = '<a style="color:black;" class="" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
					$linkList 	= $data['id_dsd'];
					$classLink  = "openMonitoringDispatch";
					$titleLink  = "Monitoring Dispatch";
					$btnHistoryTrack = '<a class="historyTracking margin-sm btn btn-action btn-success" title="History Tracking" data-jenis="historyTracking" data-param="' . $linkList . '" data-plate="' . $data['nomor_plat'] . '"><i class="fa fa-car"></i></a>';
					$btnRequest = '';
				} elseif ($data['id_wilayah_po'] == '4' && $data['tanggal_loading'] >= '2024-02-01') {
					$mobilnya = '<a style="color:black;" class="" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
					$linkList 	= $data['id_dsd'];
					$classLink  = "openMonitoringDispatch";
					$titleLink  = "Monitoring Dispatch";
					$btnHistoryTrack = '<a class="historyTracking margin-sm btn btn-action btn-success" title="History Tracking" data-jenis="historyTracking" data-param="' . $linkList . '" data-plate="' . $data['nomor_plat'] . '"><i class="fa fa-car"></i></a>';
					$btnRequest = '';
				} elseif ($data['id_wilayah_po'] == '6' || $data['id_wilayah_po'] == '3' && $data['tanggal_loading'] >= '2024-03-01') {
					$mobilnya = '<a style="color:black;" class="" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
					$linkList 	= $data['id_dsd'];
					$classLink  = "openMonitoringDispatch";
					$titleLink  = "Monitoring Dispatch";
					$btnHistoryTrack = '<a class="historyTracking margin-sm btn btn-action btn-success" title="History Tracking" data-jenis="historyTracking" data-param="' . $linkList . '" data-plate="' . $data['nomor_plat'] . '"><i class="fa fa-car"></i></a>';
					$btnRequest = '';
				} else {
					$mobilnya = '<a style="color:black;" class="" data-mobil="">' . $data['nomor_plat'] . '</a></p>';
					$linkList 	= paramEncrypt($data['id_dsd'] . "|#|1|#|" . $data['nomor_do'] . "[]" . $data['nama_customer'] . "|#|" . $data['mobil_po'] . "|#|" . $data['volume_po']);
					$classLink  = "listStsT";
					$titleLink  = "History Pengiriman";
					$btnHistoryTrack = "";
					$btnRequest = '';
				}
			} else {
				$mobilnya = '<a style="color:black;" class="" data-mobil="">' . $data['nomor_plat'] . '</a></p>';
				$linkList 	= paramEncrypt($data['id_dsd'] . "|#|1|#|" . $data['nomor_do'] . "[]" . $data['nama_customer'] . "|#|" . $data['mobil_po'] . "|#|" . $data['volume_po']);
				$classLink  = "listStsT";
				$titleLink  = "History Pengiriman";
				$btnHistoryTrack = "";
			}
		} else if ($data['is_cancel']) {
			$status = '<p style="margin-bottom:0px;" class="text-red"><b>Canceled</b><br/>' . date("d/m/Y H:i", strtotime($data['tanggal_cancel'])) . '</p>';
			$status_loaded = "";
			if ($data['link_gps'] == 'OSLOG') {
				if ($data['id_wilayah_po'] == '2' && $data['tanggal_loading'] >= '2024-01-01') {
					$mobilnya = '<a style="color:black;" class="" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
					$linkList 	= $data['id_dsd'];
					$classLink  = "openMonitoringDispatch";
					$titleLink  = "Monitoring Dispatch";
					$btnHistoryTrack = '';
				} elseif ($data['id_wilayah_po'] == '4' && $data['tanggal_loading'] >= '2024-02-01') {
					$mobilnya = '<a style="color:black;" class="" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
					$linkList 	= $data['id_dsd'];
					$classLink  = "openMonitoringDispatch";
					$titleLink  = "Monitoring Dispatch";
					$btnHistoryTrack = '';
				} elseif ($data['id_wilayah_po'] == '6' || $data['id_wilayah_po'] == '3' && $data['tanggal_loading'] >= '2024-03-01') {
					$mobilnya = '<a style="color:black;" class="" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
					$linkList 	= $data['id_dsd'];
					$classLink  = "openMonitoringDispatch";
					$titleLink  = "Monitoring Dispatch";
					$btnHistoryTrack = '';
				} else {
					$mobilnya = '<a style="color:black;" class="" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
					$linkList 	= paramEncrypt($data['id_dsd'] . "|#|1|#|" . $data['nomor_do'] . "[]" . $data['nama_customer'] . "|#|" . $data['mobil_po'] . "|#|" . $data['volume_po']);
					$classLink  = "listStsT";
					$titleLink  = "History Pengiriman";
					$btnHistoryTrack = "";
				}
			} else {
				$mobilnya = '<a style="color:black;" class="" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
				$linkList 	= paramEncrypt($data['id_dsd'] . "|#|1|#|" . $data['nomor_do'] . "[]" . $data['nama_customer'] . "|#|" . $data['mobil_po'] . "|#|" . $data['volume_po']);
				$classLink  = "listStsT";
				$titleLink  = "History Pengiriman";
				$btnHistoryTrack = "";
			}
		} else {
			$btnExtra1 	= '';
			$btnRequest = '';
			if (!$data['is_loaded']) {
				$id_po_supplier = $data['id_po_supplier'];
				$id_po_receive  = $data['id_po_receive'];

				$id_customer 	= $data['id_customer'];
				$pic_logistik 	= $data['pic_logistik'];
				$pic_cs 		= $data['pic_cs'];
				$pic_marketing 	= $data['pic_marketing'];
				$nama_cust  	= $data['nama_customer'];
				$no_plat 		= $data['nomor_plat'];
				$nama_sopir 	= $data['nama_sopir'];
				$is_loaded 	    = $data['is_loaded'];
				$masa_akhir 	= $data['masa_akhir'];
				$etl     	    = $data['tgl_etl_po'];
				$is_loco 	    = $data['is_loco'];

				$dataInfo1 	= 'truck#' . $data['nomor_do'] . '#' . $data['nama_customer'] . '#' . $alamat . '#' . $data['wilayah_angkut'] . '#' . $volpo . '#' . $data['produk'];
				$dataInfo2 	= '#' . $data['nama_suplier'] . '#' . $data['no_spj'] . '#' . $data['nomor_plat'] . '#' . $data['nama_sopir'];
				$linkInfo 	= paramEncrypt($dataInfo1 . $dataInfo2);

				$dataLoad1 	= "do_truck#|#loading#|#" . $idp . "#|#" . $volpo . "#|#" . $produk_poc . "#|#" . $tgl_loaded . "#|#" . $jam_loaded . "#|#" . $pr_vendor . "#|#" . $id_area . "#|#" . $id_customer;
				$dataLoad2 	= "#|#" . $pic_logistik . "#|#" . $pic_cs . "#|#" . $pic_marketing . "#|#" . $nama_cust . "#|#" . $terminal . "#|#" . $no_plat . "#|#" . $nama_sopir . "#|#" . $data['id_terminal'] . "#|#" . $id_po_supplier . "#|#" . $id_po_receive;;
				$linkLoad 	= paramEncrypt($dataLoad1 . $dataLoad2);

				$dataRequest1 	= "do_truck#|#request#|#" . $idp . "#|#" . $volpo . "#|#" . $produk_poc . "#|#" . $tgl_loaded . "#|#" . $jam_loaded . "#|#"  . $is_loaded . "#|#" . $pr_vendor . "#|#" . $id_area . "#|#" . $id_customer;
				$dataRequest2 	= "#|#" . $pic_logistik . "#|#" . $pic_cs . "#|#" . $pic_marketing . "#|#" . $nama_cust . "#|#" . $terminal . "#|#" . $no_plat . "#|#" . $nama_sopir . "#|#" . $data['id_terminal'] . "#|#" . $id_po_supplier . "#|#" . $id_po_receive;;
				$linkRequest	= paramEncrypt($dataRequest1 . $dataRequest2);

				if ($data['link_gps'] == "OSLOG") {
					if ($data['id_wilayah_po'] == '2' && $data['tanggal_loading'] >= '2024-01-01') {
						$linkList 	= $data['id_dsd'];
						$classLink  = "openMonitoringDispatch";
						$titleLink  = "Monitoring Dispatch";
						$btnHistoryTrack = '';
						$btnExtra1 = "";
						$btnRequest = '';
						if ($data['is_request'] == 0) {
							$btnRequest = '<a class="editStsRequest margin-sm btn btn-action btn-warning" title="Request" data-jenis="request" data-loaded= "' . $is_loaded . '" data-mkt ="' . $pic_marketing . '" data-masa= "' . $masa_akhir . '" data-param="' . $linkRequest . '" data-info="' . $linkInfo . '" data-loco="' . $is_loco . '">
						<i class="fa fa-question-circle"></i></a>';
						}
						$mobilnya = '<a class="openModalTracking" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
					} elseif ($data['id_wilayah_po'] == '4' && $data['tanggal_loading'] >= '2024-02-01') {
						$linkList 	= $data['id_dsd'];
						$classLink  = "openMonitoringDispatch";
						$titleLink  = "Monitoring Dispatch";
						$btnHistoryTrack = '';
						$btnExtra1 = '';
						$btnRequest = '';
						if ($data['is_request'] == 0) {
							$btnRequest = '<a class="editStsRequest margin-sm btn btn-action btn-warning" title="Request" data-jenis="request" data-loaded= "' . $is_loaded . '" data-mkt ="' . $pic_marketing . '" data-masa= "' . $masa_akhir . '" data-param="' . $linkRequest . '" data-info="' . $linkInfo . '" data-loco="' . $is_loco . '">
						<i class="fa fa-question-circle"></i></a>';
						}
						$mobilnya = '<a class="openModalTracking" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
					} elseif ($data['id_wilayah_po'] == '6' || $data['id_wilayah_po'] == '3' && $data['tanggal_loading'] >= '2024-03-01') {
						$linkList 	= $data['id_dsd'];
						$classLink  = "openMonitoringDispatch";
						$titleLink  = "Monitoring Dispatch";
						$btnHistoryTrack = '';
						$btnExtra1 = '';
						$btnRequest = '';
						if ($data['is_request'] == 0) {
							$btnRequest = '<a class="editStsRequest margin-sm btn btn-action btn-warning" title="Request" data-jenis="request" data-loaded= "' . $is_loaded . '" data-mkt ="' . $pic_marketing . '" data-masa= "' . $masa_akhir . '" data-param="' . $linkRequest . '" data-info="' . $linkInfo . '" data-loco="' . $is_loco . '">
						<i class="fa fa-question-circle"></i></a>';
						}
						$mobilnya = '<a class="openModalTracking" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
					} else {
						$linkList 	= paramEncrypt($data['id_dsd'] . "|#|1|#|" . $data['nomor_do'] . "[]" . $data['nama_customer'] . "|#|" . $data['mobil_po'] . "|#|" . $data['volume_po']);
						$classLink  = "listStsT";
						$titleLink  = "History Pengiriman";
						$btnHistoryTrack = "";
						$btnExtra1 	= '
							<a class="editStsLoading margin-sm btn btn-action btn-success" title="Loading" data-jenis="loading" data-param="' . $linkLoad . '" data-info="' . $linkInfo . '" data-etl="' . $etl . '" data-etl_val="' . $etl . '">
							<i class="fa fa-table"></i></a>';
						$btnRequest = '';
						if ($data['is_request'] == 0) {
							$btnRequest = '<a class="editStsRequest margin-sm btn btn-action btn-warning" title="Request" data-jenis="request" data-loaded= "' . $is_loaded . '" data-mkt ="' . $pic_marketing . '" data-masa= "' . $masa_akhir . '" data-param="' . $linkRequest . '" data-info="' . $linkInfo . '" data-loco="' . $is_loco . '">
							<i class="fa fa-question-circle"></i></a>';
						}
						$mobilnya = '<a class="getlokasimobil" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
					}
				} else {
					$linkList 	= paramEncrypt($data['id_dsd'] . "|#|1|#|" . $data['nomor_do'] . "[]" . $data['nama_customer'] . "|#|" . $data['mobil_po'] . "|#|" . $data['volume_po']);
					$classLink  = "listStsT";
					$titleLink  = "History Pengiriman";
					$btnHistoryTrack = "";
					$btnExtra1 	= '
							<a class="editStsLoading margin-sm btn btn-action btn-success" title="Loading" data-jenis="loading" data-param="' . $linkLoad . '" data-info="' . $linkInfo . '" data-etl="' . $etl . '" data-etl_val="' . $etl . '">
							<i class="fa fa-table"></i></a>';
					$btnRequest = '';
					if ($data['is_request'] == 0) {
						$btnRequest = '<a class="editStsRequest margin-sm btn btn-action btn-warning" title="Request" data-jenis="request" data-loaded= "' . $is_loaded . '" data-mkt ="' . $pic_marketing . '" data-masa= "' . $masa_akhir . '" data-param="' . $linkRequest . '" data-info="' . $linkInfo . '" data-loco="' . $is_loco . '">
							<i class="fa fa-question-circle"></i></a>';
					}
					$mobilnya = '<a class="getlokasimobil" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
				}
			} else {
				if ($data['link_gps'] == 'OSLOG') {
					if ($data['status_pengiriman']) {
						$bmp = json_decode($data['status_pengiriman'], true);
						$idb = count($bmp) - 1;
						if ($data['id_wilayah_po'] != '2' && $data['id_wilayah_po'] != '3' && $data['id_wilayah_po'] != '6') {
							$bes = '<a data-info="' . $linkInfo . '" data-param="' . $linkParam . '" data-status="loaded" class="editStsT btn btn-info pull-right"><i class="fa fa-plus"></i></a>';
							$status = $bes . '<div class="status-kirim"><p>' . $bmp[$idb]['tanggal'] . '</p><span>' . $bmp[$idb]['status'] . '</span></div>';
							$status_loaded = "";
							$btnRequest = '';
							if ($data['is_request'] == 0) {
								$btnRequest = '<a class="editStsRequest margin-sm btn btn-action btn-warning" title="Request" data-jenis="request" data-loaded= "' . $is_loaded . '" data-mkt ="' . $pic_marketing . '" data-masa= "' . $masa_akhir . '" data-param="' . $linkRequest . '" data-info="' . $linkInfo . '" data-loco="' . $is_loco . '">
							<i class="fa fa-question-circle"></i></a>';
							}
						} else {
							$status = '<div class="status-kirim"><p>' . $bmp[$idb]['tanggal'] . '</p><span>' . $bmp[$idb]['status'] . '</span></div>';
							$status_loaded = "";
							$btnRequest = '';
							if ($data['is_request'] == 0) {
								$btnRequest = '<a class="editStsRequest margin-sm btn btn-action btn-warning" title="Request" data-jenis="request" data-loaded= "' . $is_loaded . '" data-mkt ="' . $pic_marketing . '" data-masa= "' . $masa_akhir . '" data-param="' . $linkRequest . '" data-info="' . $linkInfo . '" data-loco="' . $is_loco . '">
							<i class="fa fa-question-circle"></i></a>';
							}
						}
					} else {
						if ($data['id_wilayah_po'] != '2' && $data['id_wilayah_po'] != '3' && $data['id_wilayah_po'] != '6') {
							$bes = '<a data-info="' . $linkInfo . '" data-param="' . $linkParam . '" data-status="loaded" class="editStsT btn btn-info pull-right"><i class="fa fa-plus"></i></a>';
							$status = $bes . '<p style="margin-bottom:5px;"><b>Loaded</b><br/>' . date("d/m/Y", strtotime($data['tanggal_loaded'])) . " " . $data['jam_loaded'] . '</p>';
							$status_loaded = "";
							$btnRequest = '';
							if ($data['is_request'] == 0) {
								$btnRequest = '<a class="editStsRequest margin-sm btn btn-action btn-warning" title="Request" data-jenis="request" data-loaded= "' . $is_loaded . '" data-mkt ="' . $pic_marketing . '" data-masa= "' . $masa_akhir . '" data-param="' . $linkRequest . '" data-info="' . $linkInfo . '" data-loco="' . $is_loco . '">
							<i class="fa fa-question-circle"></i></a>';
							}
						} else {
							$status = '<p style="margin-bottom:5px;"><b>Loaded</b><br/>' . date("d/m/Y", strtotime($data['tanggal_loaded'])) . " " . $data['jam_loaded'] . '</p>';
							$status_loaded = "";
							$btnRequest = '';
							if ($data['is_request'] == 0) {
								$btnRequest = '<a class="editStsRequest margin-sm btn btn-action btn-warning" title="Request" data-jenis="request" data-loaded= "' . $is_loaded . '" data-mkt ="' . $pic_marketing . '" data-masa= "' . $masa_akhir . '" data-param="' . $linkRequest . '" data-info="' . $linkInfo . '" data-loco="' . $is_loco . '">
							<i class="fa fa-question-circle"></i></a>';
							}
						}
					}
					if ($data['id_wilayah_po'] == '2' && $data['tanggal_loading'] >= '2024-01-01') {
						$mobilnya = '<a class="openModalTracking" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
						$linkList 	= $data['id_dsd'];
						$classLink  = "openMonitoringDispatch";
						$titleLink  = "Monitoring Dispatch";
						$btnHistoryTrack = '';
						$btnRequest = '';
						if ($data['is_request'] == 0) {
							$btnRequest = '<a class="editStsRequest margin-sm btn btn-action btn-warning" title="Request" data-jenis="request" data-loaded= "' . $is_loaded . '" data-mkt ="' . $pic_marketing . '" data-masa= "' . $masa_akhir . '" data-param="' . $linkRequest . '" data-info="' . $linkInfo . '" data-loco="' . $is_loco . '">
						<i class="fa fa-question-circle"></i></a>';
						}
					} elseif ($data['id_wilayah_po'] == '4' && $data['tanggal_loading'] >= '2024-02-01') {
						$mobilnya = '<a class="openModalTracking" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
						$linkList 	= $data['id_dsd'];
						$classLink  = "openMonitoringDispatch";
						$titleLink  = "Monitoring Dispatch";
						$btnHistoryTrack = '';
						$btnRequest = '';
						if ($data['is_request'] == 0) {
							$btnRequest = '<a class="editStsRequest margin-sm btn btn-action btn-warning" title="Request" data-jenis="request" data-loaded= "' . $is_loaded . '" data-mkt ="' . $pic_marketing . '" data-masa= "' . $masa_akhir . '" data-param="' . $linkRequest . '" data-info="' . $linkInfo . '" data-loco="' . $is_loco . '">
						<i class="fa fa-question-circle"></i></a>';
						}
					} elseif ($data['id_wilayah_po'] == '6' || $data['id_wilayah_po'] == '3' && $data['tanggal_loading'] >= '2024-03-01') {
						$mobilnya = '<a class="openModalTracking" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
						$linkList 	= $data['id_dsd'];
						$classLink  = "openMonitoringDispatch";
						$titleLink  = "Monitoring Dispatch";
						$btnHistoryTrack = '';
						$btnRequest = '';
						if ($data['is_request'] == 0) {
							$btnRequest = '<a class="editStsRequest margin-sm btn btn-action btn-warning" title="Request" data-jenis="request" data-loaded= "' . $is_loaded . '" data-mkt ="' . $pic_marketing . '" data-masa= "' . $masa_akhir . '" data-param="' . $linkRequest . '" data-info="' . $linkInfo . '" data-loco="' . $is_loco . '">
						<i class="fa fa-question-circle"></i></a>';
						}
					} else {
						$mobilnya = '<a class="getlokasimobil" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
						$linkList 	= paramEncrypt($data['id_dsd'] . "|#|1|#|" . $data['nomor_do'] . "[]" . $data['nama_customer'] . "|#|" . $data['mobil_po'] . "|#|" . $data['volume_po']);
						$classLink  = "listStsT";
						$titleLink  = "History Pengiriman";
						$btnHistoryTrack = "";
						$btnRequest = '';
						if ($data['is_request'] == 0) {
							$btnRequest = '<a class="editStsRequest margin-sm btn btn-action btn-warning" title="Request" data-jenis="request" data-loaded= "' . $is_loaded . '" data-mkt ="' . $pic_marketing . '" data-masa= "' . $masa_akhir . '" data-param="' . $linkRequest . '" data-info="' . $linkInfo . '" data-loco="' . $is_loco . '">
						<i class="fa fa-question-circle"></i></a>';
						}
					}
				} else {
					if ($data['status_pengiriman']) {
						$bmp = json_decode($data['status_pengiriman'], true);
						$idb = count($bmp) - 1;
						// if ($data['id_wilayah_po'] != '2') {
						// 	$bes = '<a data-info="' . $linkInfo . '" data-param="' . $linkParam . '" data-status="loaded" class="editStsT btn btn-info pull-right"><i class="fa fa-plus"></i></a>';
						// 	$status = $bes . '<div class="status-kirim"><p>' . $bmp[$idb]['tanggal'] . '</p><span>' . $bmp[$idb]['status'] . '</span></div>';
						// 	$status_loaded = "";
						// } else {
						// 	$status = '<div class="status-kirim"><p>' . $bmp[$idb]['tanggal'] . '</p><span>' . $bmp[$idb]['status'] . '</span></div>';
						// 	$status_loaded = "";
						// }
						$bes = '<a data-info="' . $linkInfo . '" data-param="' . $linkParam . '" data-status="loaded" class="editStsT btn btn-info pull-right"><i class="fa fa-plus"></i></a>';
						$status = $bes . '<div class="status-kirim"><p>' . $bmp[$idb]['tanggal'] . '</p><span>' . $bmp[$idb]['status'] . '</span></div>';
						$status_loaded = "";
					}
					$mobilnya = '<a class="getlokasimobil" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
					$linkList 	= paramEncrypt($data['id_dsd'] . "|#|1|#|" . $data['nomor_do'] . "[]" . $data['nama_customer'] . "|#|" . $data['mobil_po'] . "|#|" . $data['volume_po']);
					$classLink  = "listStsT";
					$titleLink  = "History Pengiriman";
					$btnHistoryTrack = "";
					$btnRequest = '';
					if ($data['is_request'] == 0) {
						$btnRequest = '<a class="editStsRequest margin-sm btn btn-action btn-warning" title="Request" data-jenis="request" data-loaded= "' . $is_loaded . '" data-mkt ="' . $pic_marketing . '" data-masa= "' . $masa_akhir . '" data-param="' . $linkRequest . '" data-info="' . $linkInfo . '" data-loco="' . $is_loco . '">
					<i class="fa fa-question-circle"></i></a>';
					}
				}
			}
		}


		if ($data['no_do_acurate'] == NULL) {
			$delivery_order = $data['no_do_syop'];
		} else {
			$delivery_order = $data['no_do_acurate'];
		}


		$content .= '
				<tr>
					<td class="text-center">' . $count . '</td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>' . $data['nomor_do'] . '</b></p>
						<p style="margin-bottom:0px">' . $data['nama_customer'] . '</p>
						<p style="margin-bottom:0px">' . $alamat . '</p>
						<p style="margin-bottom:0px">Wilayah OA : ' . $data['wilayah_angkut'] . '</p>
					</td>
					<td>
                            <p style="margin-bottom:0px"><b>' . $data['nomor_poc'] . '</b></p>
							<p style="margin-bottom:0px">' . number_format($data['volume_kirim']) . ' Liter ' . $data['produk'] . '</p>
                            <p style="margin-bottom:0px">' . 'Tgl Kirim ' . tgl_indo($data['tanggal_kirim']) . '</p>
							<p style="margin-bottom:0px">' . 'Realisasi ' . number_format($data['realisasi_volume']) . '</p>
							<p style="margin-bottom:0px">Tera Depo : ' . $data['tera_depo'] . '</p>
							<p style="margin-bottom:0px">Tera Site : ' . $data['tera_site'] . '</p>
                    
                    </td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>' . $data['nomor_po'] . '</b></p>
						<p style="margin-bottom:0px">' . number_format($volpo) . ' Liter ' . $data['produk'] . '</p>
						<p style="margin-bottom:0px">Tgl PO &nbsp;&nbsp;&nbsp;: ' . tgl_indo($data['tanggal_poc'], 'short') . '</p>
						<p style="margin-bottom:0px">Tgl Kirim : ' . tgl_indo($data['tgl_kirim_po'], 'short') . '</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>' . $data['nama_suplier'] . '</b></p>
						<p style="margin-bottom:0px">' . $data['no_spj'] . '</p>
						<p style="margin-bottom:0px">Truck &nbsp;: ' . $mobilnya . '</p>
						<p style="margin-bottom:0px">Driver : ' . $data['nama_sopir'] . '</p>
					</td>

					<td class="text-left">
						<p style="margin-bottom:0px"><b>Delivery Order : </b></p> ' . $delivery_order . '</p>
						<p style="margin-bottom:0px"><b>Loading Order : </b></p>' . $data['nomor_lo_pr'] . '</p>
						<p style="margin-bottom:0px"><b>' . $request . '</b></p>
						<p style="margin-bottom:0px"><b>' . $status_request . '</b></p>
					</td>
					
					<td class="text-left">
						<p style="margin-bottom:0px"><b>' . $terminal . '</b></p>
						<p style="margin-bottom:0px">' . $nomor_segel . '</p>
						<p style="margin-bottom:0px">ETL : ' . tgl_indo($data['tanggal_loading'], 'short') . ' ' . date("H:i", strtotime($data['jam_loading'])) . '</p>
							<p style="margin-bottom:0px">ETA : ' . tgl_indo($data['tgl_eta_po'], 'short') . ' ' . date("H:i", strtotime($data['jam_eta_po'])) . '</p>
					</td>
					<td class="text-left">' . $status . '<hr>' . $status_loaded . '</td>
					<td class="text-center action">
						<a class="' . $classLink . ' margin-sm btn btn-action btn-info" title="' . $titleLink . '" data-param="' . $linkList . '"><i class="fa fa-info-circle"></i></a>
						' . $btnExtra1 . '
						' . $btnHistoryTrack . '
						' . $btnRequest . '
            		</td>
				</tr>';
	}
	$content .= '<tr class="hide"><td colspan="7"><input type="hidden" id="uriExp1" value="' . $link . '" />&nbsp;</td></tr>';
}

$json_data = array(
	"items"		=> $content,
	"pages"		=> $tot_page,
	"page"		=> $page,
	"totalData"	=> $tot_record,
	"infoData"	=> "Showing " . ($position + 1) . " to " . $count . " of " . $tot_record . " entries",
);
echo json_encode($json_data);

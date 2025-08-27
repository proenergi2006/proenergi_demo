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
$length	= isset($_POST['length']) ? htmlspecialchars($_POST["length"], ENT_QUOTES) : 5;
$q1	= isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';
$q2	= isset($_POST["q2"]) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : '';
$q3	= isset($_POST["q3"]) ? htmlspecialchars($_POST["q3"], ENT_QUOTES) : '';
$q1lt	= isset($_POST["q1lt"]) ? htmlspecialchars($_POST["q1lt"], ENT_QUOTES) : '';
$q2lt	= isset($_POST["q2lt"]) ? htmlspecialchars($_POST["q2lt"], ENT_QUOTES) : '';
$q3lt	= isset($_POST["q3lt"]) ? htmlspecialchars($_POST["q3lt"], ENT_QUOTES) : '';
$ql1	= isset($_POST["ql1"]) ? htmlspecialchars($_POST["ql1"], ENT_QUOTES) : '';
$ql2	= isset($_POST["ql2"]) ? htmlspecialchars($_POST["ql2"], ENT_QUOTES) : '';
$ql3	= isset($_POST["ql3"]) ? htmlspecialchars($_POST["ql3"], ENT_QUOTES) : '';
$tipe	= isset($_POST["tipe"]) ? htmlspecialchars($_POST["tipe"], ENT_QUOTES) : '';
$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

if ($tipe == "task_pengiriman") {
	// TASK PENGIRIMAN
	$p = new paging;
	$whereadd = '';
	if ($sesrol > 1) {
		$whereadd = " and o.id_wilayah = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]) . "'";
	}
	$sql = "SELECT a.*, c.pr_pelanggan, i.nama_customer, e.alamat_survey, f.nama_prov, g.nama_kab, j.fullname, n.nama_transportir, n.nama_suplier, b.no_spj, k.nomor_plat, k.link_gps, 
	l.nama_sopir, b.volume_po, h.produk_poc, p.id_area, c.pr_vendor, r.nama_terminal, r.tanki_terminal, r.lokasi_terminal, s.wilayah_angkut, m.nomor_po, m.tanggal_po, h.tanggal_poc,
	c.produk, b.tgl_kirim_po, b.mobil_po, c.no_do_acurate, c.nomor_lo_pr, h.nomor_poc, d.tanggal_kirim, d.volume_kirim, m.id_wilayah as id_wilayah_po,
	d.realisasi_kirim,
	i.id_customer,
	m.created_by as pic_logistik,
	d.created_by as pic_cs,
	j.id_user as pic_marketing, o.id_terminal 
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
	where 1=1 AND a.is_delivered = '0' AND d.tanggal_kirim >= '2024-01-01' " . $whereadd;

	if ($q1 != "")
		$sql .= " and (upper(a.nomor_do) like '" . strtoupper($q1) . "%' or upper(b.no_spj) = '" . strtoupper($q1) . "' or upper(k.nomor_plat) = '" . strtoupper($q1) . "' 
					or upper(l.nama_sopir) like '%" . strtoupper($q1) . "%' or upper(i.nama_customer) like '%" . strtoupper($q1) . "%')";

	if ($q2 != "" && $q3 != "")
		$sql .= " and d.tanggal_kirim between '" . tgl_db($q2) . "' and '" . tgl_db($q3) . "'";
	else if ($q2 != "" && $q3 == "")
		$sql .= " and d.tanggal_kirim =" . tgl_db($q2) . "";
	else if ($q3 != "" && $q2 == "")
		$sql .= " and d.tanggal_kirim =" . tgl_db($q3) . "";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record / $length);
	$page		= ($start > $tot_page) ? $start - 1 : $start;
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " ORDER BY a.id_dsd DESC LIMIT " . $position . ", " . $length;

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
			$idp 		= $data["id_dsd"];
			$tempal 	= strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
			$alamat		= $data['alamat_survey'] . " " . ucwords($tempal) . " " . $data['nama_prov'];
			$volpo		= $data['volume_po'];
			$produk_poc	= $data['produk_poc'];
			$tgl_loaded	= $data['tanggal_loaded'];
			$jam_loaded	= $data['jam_loaded'];
			$pr_vendor	= $data['pr_vendor'];
			$id_area	= $data['id_area'];

			$terminal1 	= $data['nama_terminal'];
			$terminal2 	= ($data['tanki_terminal']) ? ' - ' . $data['tanki_terminal'] : '';
			$terminal3 	= ($data['lokasi_terminal']) ? '<br />' . $data['lokasi_terminal'] : '';
			$terminal 	= $terminal1 . $terminal2 . $terminal3;

			$dataInfo1 	= 'truck#' . $data['nomor_do'] . '#' . $data['nama_customer'] . '#' . $alamat . '#' . $data['wilayah_angkut'] . '#' . $volpo . '#' . $data['produk'];
			$dataInfo2 	= '#' . $data['nama_suplier'] . '#' . $data['no_spj'] . '#' . $data['nomor_plat'] . '#' . $data['nama_sopir'];
			$linkInfo 	= paramEncrypt($dataInfo1 . $dataInfo2);

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

			$btnExtra1 	= '';
			if (!$data['is_loaded']) {
				$id_customer 	= $data['id_customer'];
				$pic_logistik 	= $data['pic_logistik'];
				$pic_cs 		= $data['pic_cs'];
				$pic_marketing 	= $data['pic_marketing'];
				$nama_cust  	= $data['nama_customer'];
				$no_plat 		= $data['nomor_plat'];
				$nama_sopir 	= $data['nama_sopir'];

				$dataInfo1 	= 'truck#' . $data['nomor_do'] . '#' . $data['nama_customer'] . '#' . $alamat . '#' . $data['wilayah_angkut'] . '#' . $volpo . '#' . $data['produk'];
				$dataInfo2 	= '#' . $data['nama_suplier'] . '#' . $data['no_spj'] . '#' . $data['nomor_plat'] . '#' . $data['nama_sopir'];
				$linkInfo 	= paramEncrypt($dataInfo1 . $dataInfo2);

				$dataLoad1 	= "do_truck#|#loading#|#" . $idp . "#|#" . $volpo . "#|#" . $produk_poc . "#|#" . $tgl_loaded . "#|#" . $jam_loaded . "#|#" . $pr_vendor . "#|#" . $id_area . "#|#" . $id_customer;
				$dataLoad2 	= "#|#" . $pic_logistik . "#|#" . $pic_cs . "#|#" . $pic_marketing . "#|#" . $nama_cust . "#|#" . $terminal . "#|#" . $no_plat . "#|#" . $nama_sopir . "#|#" . $data['id_terminal'];
				$linkLoad 	= paramEncrypt($dataLoad1 . $dataLoad2);
				$status = "<b>Belum Loading</b>";

				if ($data['link_gps'] == "OSLOG") {
					if ($data['id_wilayah_po'] == '2' && $data['tanggal_loading'] >= '2024-01-01') {
						$linkList 	= $data['id_dsd'];
						$classLink  = "openMonitoringDispatch";
						$titleLink  = "Monitoring Dispatch";
						$btnExtra1 = "";
						$mobilnya = '<a class="openModalTracking" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
					} elseif ($data['id_wilayah_po'] == '4' && $data['tanggal_loading'] >= '2024-02-01') {
						$linkList 	= $data['id_dsd'];
						$classLink  = "openMonitoringDispatch";
						$titleLink  = "Monitoring Dispatch";
						$btnExtra1 = '<a class="editStsLoading margin-sm btn btn-action btn-success" title="Loading" data-jenis="loading" data-param="' . $linkLoad . '" data-info="' . $linkInfo . '">
						<i class="fa fa-table"></i></a>';
						$mobilnya = '<a class="openModalTracking" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
					} elseif ($data['id_wilayah_po'] == '6' || $data['id_wilayah_po'] == '3' && $data['tanggal_loading'] >= '2024-03-01') {
						$linkList 	= $data['id_dsd'];
						$classLink  = "openMonitoringDispatch";
						$titleLink  = "Monitoring Dispatch";
						$btnExtra1 = '<a class="editStsLoading margin-sm btn btn-action btn-success" title="Loading" data-jenis="loading" data-param="' . $linkLoad . '" data-info="' . $linkInfo . '">
						<i class="fa fa-table"></i></a>';
						$mobilnya = '<a class="openModalTracking" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
					} else {
						$linkList 	= paramEncrypt($data['id_dsd'] . "|#|1|#|" . $data['nomor_do'] . "[]" . $data['nama_customer'] . "|#|" . $data['mobil_po'] . "|#|" . $data['volume_po']);
						$classLink  = "listStsT";
						$titleLink  = "History Pengiriman";
						$btnExtra1 	= '
							<a class="editStsLoading margin-sm btn btn-action btn-success" title="Loading" data-jenis="loading" data-param="' . $linkLoad . '" data-info="' . $linkInfo . '">
							<i class="fa fa-table"></i></a>';
						$mobilnya = '<a class="getlokasimobil" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
					}
				} else {
					$linkList 	= paramEncrypt($data['id_dsd'] . "|#|1|#|" . $data['nomor_do'] . "[]" . $data['nama_customer'] . "|#|" . $data['mobil_po'] . "|#|" . $data['volume_po']);
					$classLink  = "listStsT";
					$titleLink  = "History Pengiriman";
					$btnExtra1 	= '
							<a class="editStsLoading margin-sm btn btn-action btn-success" title="Loading" data-jenis="loading" data-param="' . $linkLoad . '" data-info="' . $linkInfo . '">
							<i class="fa fa-table"></i></a>';
					$mobilnya = '<a class="getlokasimobil" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
				}
			} else {
				if ($data['link_gps'] == 'OSLOG') {
					if ($data['status_pengiriman']) {
						$bmp = json_decode($data['status_pengiriman'], true);
						$idb = count($bmp) - 1;
						if ($data['id_wilayah_po'] != '2') {
							$bes = '<a data-info="' . $linkInfo . '" data-param="' . $linkParam . '" data-status="loaded" class="editStsT btn btn-info pull-right"><i class="fa fa-plus"></i></a>';
							$status = $bes . '<div class="status-kirim"><p>' . $bmp[$idb]['tanggal'] . '</p><span>' . $bmp[$idb]['status'] . '</span></div>';
							$status_loaded = "";
						} else {
							$status = '<div class="status-kirim"><p>' . $bmp[$idb]['tanggal'] . '</p><span>' . $bmp[$idb]['status'] . '</span></div>';
							$status_loaded = "";
						}
					} else {
						if ($data['id_wilayah_po'] != '2') {
							$bes = '<a data-info="' . $linkInfo . '" data-param="' . $linkParam . '" data-status="loaded" class="editStsT btn btn-info pull-right"><i class="fa fa-plus"></i></a>';
							$status = $bes . '<p style="margin-bottom:5px;"><b>Loaded</b><br/>' . date("d/m/Y", strtotime($data['tanggal_loaded'])) . " " . $data['jam_loaded'] . '</p>';
							$status_loaded = "";
						} else {
							$status = '<p style="margin-bottom:5px;"><b>Loaded</b><br/>' . date("d/m/Y", strtotime($data['tanggal_loaded'])) . " " . $data['jam_loaded'] . '</p>';
							$status_loaded = "";
						}
					}
					if ($data['id_wilayah_po'] == '2' && $data['tanggal_loading'] >= '2024-01-01') {
						$mobilnya = '<a class="openModalTracking" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
						$linkList 	= $data['id_dsd'];
						$classLink  = "openMonitoringDispatch";
						$titleLink  = "Monitoring Dispatch";
					} elseif ($data['id_wilayah_po'] == '4' && $data['tanggal_loading'] >= '2024-02-01') {
						$mobilnya = '<a class="openModalTracking" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
						$linkList 	= $data['id_dsd'];
						$classLink  = "openMonitoringDispatch";
						$titleLink  = "Monitoring Dispatch";
					} elseif ($data['id_wilayah_po'] == '6' || $data['id_wilayah_po'] == '3' && $data['tanggal_loading'] >= '2024-03-01') {
						$mobilnya = '<a class="openModalTracking" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
						$linkList 	= $data['id_dsd'];
						$classLink  = "openMonitoringDispatch";
						$titleLink  = "Monitoring Dispatch";
					} else {
						$mobilnya = '<a class="getlokasimobil" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
						$linkList 	= paramEncrypt($data['id_dsd'] . "|#|1|#|" . $data['nomor_do'] . "[]" . $data['nama_customer'] . "|#|" . $data['mobil_po'] . "|#|" . $data['volume_po']);
						$classLink  = "listStsT";
						$titleLink  = "History Pengiriman";
					}
				} else {
					if ($data['status_pengiriman']) {
						$bmp = json_decode($data['status_pengiriman'], true);
						$idb = count($bmp) - 1;
						if ($data['id_wilayah_po'] != '2') {
							$bes = '<a data-info="' . $linkInfo . '" data-param="' . $linkParam . '" data-status="loaded" class="editStsT btn btn-info pull-right"><i class="fa fa-plus"></i></a>';
							$status = $bes . '<div class="status-kirim"><p>' . $bmp[$idb]['tanggal'] . '</p><span>' . $bmp[$idb]['status'] . '</span></div>';
							$status_loaded = "";
						} else {
							$status = '<div class="status-kirim"><p>' . $bmp[$idb]['tanggal'] . '</p><span>' . $bmp[$idb]['status'] . '</span></div>';
							$status_loaded = "";
						}
					}
					$mobilnya = '<a class="getlokasimobil" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';
					$linkList 	= paramEncrypt($data['id_dsd'] . "|#|1|#|" . $data['nomor_do'] . "[]" . $data['nama_customer'] . "|#|" . $data['mobil_po'] . "|#|" . $data['volume_po']);
					$classLink  = "listStsT";
					$titleLink  = "History Pengiriman";
				}
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
							<p style="margin-bottom:0px">' . 'Realisasi ' . number_format($data['realisasi_kirim']) . '</p>
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
							<p style="margin-bottom:0px"><b>NO DO Accurate : </b></p> ' . $data['no_do_acurate'] . '</p>
							<p style="margin-bottom:0px"><b>Loading Order : </b></p>' . $data['nomor_lo_pr'] . '</p>
						</td>
						<td class="text-left">
							<p style="margin-bottom:0px"><b>' . $terminal . '</b></p>
							<p style="margin-bottom:0px">' . $nomor_segel . '</p>
							<p style="margin-bottom:0px">ETL : ' . tgl_indo($data['tanggal_loading'], 'short') . ' ' . date("H:i", strtotime($data['jam_loading'])) . '</p>
						</td>
						<td class="text-left">' . $status . '<hr>' . $status_loaded . '</td>
						<td class="text-center action">
							<a class="' . $classLink . ' margin-sm btn btn-action btn-info" title="' . $titleLink . '" data-param="' . $linkList . '"><i class="fa fa-info-circle"></i></a>
							' . $btnExtra1 . '
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
} else if ($tipe == 'lead_time') {
	// LEAD TIME
	$p = new paging;
	$whereadd = '';
	if ($sesrol > 1) {
		$whereadd = " and o.id_wilayah = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]) . "'";
	}
	$sql = "SELECT a.*, c.pr_pelanggan, i.nama_customer, e.alamat_survey, f.nama_prov, g.nama_kab, j.fullname, n.nama_transportir, n.nama_suplier, b.no_spj, k.nomor_plat, k.link_gps, 
	l.nama_sopir, b.volume_po, h.produk_poc, p.id_area, c.pr_vendor, r.nama_terminal, r.tanki_terminal, r.lokasi_terminal, s.wilayah_angkut, m.nomor_po, m.tanggal_po, 
	c.produk, b.tgl_kirim_po, b.mobil_po, c.no_do_acurate, c.nomor_lo_pr, h.nomor_poc, d.tanggal_kirim, d.volume_kirim, m.id_wilayah as id_wilayah_po, t.nama_cabang, q.nama_area,
	d.realisasi_kirim,
	i.id_customer,
	m.created_by as pic_logistik,
	d.created_by as pic_cs,
	j.id_user as pic_marketing, o.id_terminal 
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
	join pro_master_cabang t on i.id_wilayah = t.id_master 
	where 1=1 AND a.is_delivered = '1' " . $whereadd;

	if ($q1lt != "")
		$sql .= " and (upper(h.nomor_poc) like '" . strtoupper($q1lt) . "%' or upper(b.no_spj) = '" . strtoupper($q1lt) . "' or upper(k.nomor_plat) = '" . strtoupper($q1lt) . "' 
					or upper(l.nama_sopir) like '%" . strtoupper($q1lt) . "%' or upper(i.nama_customer) like '%" . strtoupper($q1lt) . "%')";

	if ($q2lt != "" && $q3lt != "")
		$sql .= " and d.tanggal_kirim between '" . tgl_db($q2lt) . "' and '" . tgl_db($q3lt) . "'";
	else if ($q2lt != "" && $q3lt == "")
		$sql .= " and d.tanggal_kirim =" . tgl_db($q2lt) . "";
	else if ($q3lt != "" && $q2lt == "")
		$sql .= " and d.tanggal_kirim =" . tgl_db($q3lt) . "";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record / $length);
	$page		= ($start > $tot_page) ? $start - 1 : $start;
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " ORDER BY a.id_dsd DESC LIMIT " . $position . ", " . $length;

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
			$idp 		= $data["id_dsd"];
			$tempal 	= strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
			$alamat		= $data['alamat_survey'] . " " . ucwords($tempal) . " " . $data['nama_prov'];
			$volpo		= $data['volume_po'];
			$produk_poc	= $data['produk_poc'];
			$tgl_loaded	= $data['tanggal_loaded'];
			$jam_loaded	= $data['jam_loaded'];
			$pr_vendor	= $data['pr_vendor'];
			$id_area	= $data['id_area'];

			$tgl1 	= strtotime($data['tanggal_loaded'] . " " . $data['jam_loaded']);
			$tgl2 	= strtotime($data['tanggal_delivered']);
			$leadtm = ($tgl2 - $tgl1);

			$terminal1 	= $data['nama_terminal'];
			$terminal2 	= ($data['tanki_terminal']) ? ' - ' . $data['tanki_terminal'] : '';
			$terminal3 	= ($data['lokasi_terminal']) ? '<br />' . $data['lokasi_terminal'] : '';
			$terminal 	= $terminal1 . $terminal2 . $terminal3;

			$mobilnya = '<a style="color:black;" class="" data-mobil="' . $data['nomor_plat'] . '">' . $data['nomor_plat'] . '</a></p>';

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


			$content .= '
					<tr>
						<td class="text-center">' . $count . '</td>
						<td class="text-left">
							<p style="margin-bottom:0px"><b>' . $data['nomor_poc'] . '</b></p>
							<p style="margin-bottom:0px">' . $data['nama_customer'] . '</p>
							<p style="margin-bottom:0px">' . $data['nama_cabang'] . '</p>
						</td>
						<td>
							<p style="margin-bottom:0px"><b>' . $data['nama_area'] . '</b></p>
							<p style="margin-bottom:0px">' . $alamat . '</p>
							<p style="margin-bottom:0px">' . $terminal . '</p>
						</td>
						<td class="text-left">
							<p style="margin-bottom:0px"><b>' . $data['nama_suplier'] . '</b></p>
							<p style="margin-bottom:0px">' . $data['nama_sopir'] . '</p>
							<p style="margin-bottom:0px">Truck &nbsp;: ' . $mobilnya . '</p>
							<p style="margin-bottom:0px">Tgl PO &nbsp;&nbsp;&nbsp;: ' . tgl_indo($data['tanggal_po'], 'short') . '</p>
							<p style="margin-bottom:0px">Tgl Kirim : ' . tgl_indo($data['tgl_kirim_po'], 'short') . '</p>
						</td>
						<td class="text-left">
							<p style="margin-bottom:0px;">' . ($data['no_spj'] ? '<b>No. SJ ' . $data['no_spj'] . '</b>' : '') . '</p>
							<p style="margin-bottom:0px;">' . number_format($volpo) . ' Liter</p>
						</td>
						<td class="text-left">
							<p style="margin-bottom:0px">' . date("d/m/Y", strtotime($data['tanggal_loaded'])) . '</p>
							<p style="margin-bottom:0px">' . $data['jam_loaded'] . '</p>
						</td>
						<td class="text-left">
							<p style="margin-bottom:0px">' . date("d/m/Y", strtotime($data['tanggal_delivered'])) . '</p>
							<p style="margin-bottom:0px">' . date("H:i", strtotime($data['tanggal_delivered'])) . '</p>
						</td>
						<td class="text-left">' . timeManHours($leadtm) . '</td>
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
} else {
	// Losess
	if ($ql1 != "") {
		$wherekeywords1 .= " and (upper(h.nomor_poc) like '" . strtoupper($ql1) . "%' or upper(d.no_spj) = '" . strtoupper($ql1) . "' or upper(r.nomor_plat) = '" . strtoupper($ql1) . "' or upper(s.nama_sopir) like '%" . strtoupper($ql1) . "%' or upper(n.nama_customer) like '%" . strtoupper($ql1) . "%')";

		$wherekeywords2 .= " and (upper(h.nomor_poc) like '" . strtoupper($ql1) . "%' or upper(n.nama_customer) like '%" . strtoupper($ql1) . "%')";
	} else {
		$wherekeywords1 .= "";
		$wherekeywords2 .= "";
	}

	if ($ql2 != "" && $ql3 != "") {
		$wheretgl1 .= " and g.tanggal_kirim between '" . tgl_db($ql2) . "' and '" . tgl_db($ql3) . "'";
		$wheretgl2 .= " and g.tanggal_kirim between '" . tgl_db($ql2) . "' and '" . tgl_db($ql3) . "'";
	} else if ($ql2 != "" && $ql3 == "") {
		$wheretgl1 .= " and g.tanggal_kirim =" . tgl_db($ql2) . "";
		$wheretgl2 .= " and g.tanggal_kirim =" . tgl_db($ql2) . "";
	} else if ($ql3 != "" && $ql2 == "") {
		$wheretgl1 .= " and g.tanggal_kirim =" . tgl_db($ql3) . "";
		$wheretgl2 .= " and g.tanggal_kirim =" . tgl_db($ql3) . "";
	} else if ($ql3 == "" && $ql2 == "") {
		$wheretgl1 .= " and g.tanggal_kirim >= '2024-01-01'";
		$wheretgl2 .= " and g.tanggal_kirim >= '2024-01-01'";
	}

	$where1 .= " and n.id_wilayah = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]) . "'";
	$where2 .= " and n.id_wilayah = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]) . "'";
	$p = new paging;
	$sql = "
		select * from (
			select g.tanggal_kirim, d.volume_po as jum_vol, n.nama_customer, n.id_wilayah, o.nama_cabang, i.kab_survey, k.nama_kab, l.id_area, m.nama_area, 
			h.nomor_poc, d.no_spj, q.nama_suplier, q.nama_transportir, q.lokasi_suplier, r.nomor_plat, s.nama_sopir, t.nama_terminal, t.tanki_terminal, t.lokasi_terminal, 
			b.rating, b.realisasi_volume, i.alat_ukur 
			from pro_po_ds a
			join pro_po_ds_detail b on a.id_ds = b.id_ds 
			join pro_po_detail d on b.id_pod = d.id_pod 
			join pro_po c on d.id_po = c.id_po 
			join pro_pr_detail e on d.id_prd = e.id_prd 
			join pro_pr f on e.id_pr = f.id_pr 
			join pro_po_customer_plan g on e.id_plan = g.id_plan 
			join pro_po_customer h on g.id_poc = h.id_poc 
			join pro_customer_lcr i on g.id_lcr = i.id_lcr 
			join pro_master_provinsi j on i.prov_survey = j.id_prov 
			join pro_master_kabupaten k on i.kab_survey = k.id_kab 
			join pro_penawaran l on h.id_penawaran = l.id_penawaran 
			join pro_master_area m on l.id_area = m.id_master 
			join pro_customer n on h.id_customer = n.id_customer 
			join pro_master_cabang o on n.id_wilayah = o.id_master 
			join acl_user p on n.id_marketing = p.id_user 
			join pro_master_transportir q on c.id_transportir = q.id_master 
			join pro_master_transportir_mobil r on d.mobil_po = r.id_master 
			join pro_master_transportir_sopir s on d.sopir_po = s.id_master 
			join pro_master_terminal t on a.id_terminal = t.id_master 
			where b.is_delivered = 1 " . $wherekeywords1 . " " . $wheretgl1 . "  " . $where1 . "
			UNION ALL
			select g.tanggal_kirim, a.bl_lo_jumlah as jum_vol, n.nama_customer, n.id_wilayah, o.nama_cabang, i.kab_survey, k.nama_kab, l.id_area, m.nama_area, 
			h.nomor_poc, '' as no_spj, q.nama_suplier, q.nama_transportir, q.lokasi_suplier, a.vessel_name as nomor_plat, a.kapten_name as nama_sopir, 
			t.nama_terminal, t.tanki_terminal, t.lokasi_terminal, a.rating, a.realisasi_volume, i.alat_ukur 
			from pro_po_ds_kapal a 
			join pro_pr_detail e on a.id_prd = e.id_prd 
			join pro_pr f on e.id_pr = f.id_pr 
			join pro_po_customer_plan g on e.id_plan = g.id_plan 
			join pro_po_customer h on g.id_poc = h.id_poc 
			join pro_customer_lcr i on g.id_lcr = i.id_lcr 
			join pro_master_provinsi j on i.prov_survey = j.id_prov 
			join pro_master_kabupaten k on i.kab_survey = k.id_kab 
			join pro_penawaran l on h.id_penawaran = l.id_penawaran 
			join pro_master_area m on l.id_area = m.id_master 
			join pro_customer n on h.id_customer = n.id_customer 
			join pro_master_cabang o on n.id_wilayah = o.id_master 
			join acl_user p on n.id_marketing = p.id_user 
			join pro_master_transportir q on a.transportir = q.id_master 
			join pro_master_terminal t on a.terminal = t.id_master 
			where a.is_delivered = 1 " . $wherekeywords2 . " " . $wheretgl2 . " " . $where2 . "
		) a ";

	if (is_numeric($length)) {
		$tot_record = $con->num_rows($sql);
		$tot_page 	= ceil($tot_record / $length);
		$page		= ($start > $tot_page) ? $start - 1 : $start;
		$position 	= $p->findPosition($length, $tot_record, $page);
		$sql .= " order by tanggal_kirim desc limit " . $position . ", " . $length;
	} else {
		$tot_record = $con->num_rows($sql);
		$page		= 1;
		$position 	= 0;
		$sql .= " order by tanggal_kirim desc";
	}

	$content = "";
	if ($tot_record == 0) {
		$content .= '<tr><td colspan="8" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else {
		$count 		= $position;
		$tot_page 	= (is_numeric($length)) ? ceil($tot_record / $length) : 1;
		$result 	= $con->getResult($sql);
		$tot1 = 0;
		$tot2 = 0;
		$tot3 = 0;
		foreach ($result as $data) {
			$count++;
			$tempal = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
			$losses = $data['realisasi_volume'] - $data['jum_vol'];
			$tot1	= $tot1 + $data['jum_vol'];
			$tot2	= $tot2 + $data['realisasi_volume'];
			$tot3	= $tot3 + $losses;

			$content .= '
				<tr>
					<td class="text-center">' . date("d/m/Y", strtotime($data['tanggal_kirim'])) . '</td>
					<td class="text-left">
						<p style="margin-bottom:0px;"><b>' . $data['nomor_poc'] . '</b></p>
						<p style="margin-bottom:0px;">' . $data['nama_customer'] . '</p>
						<p style="margin-bottom:0px;">' . $data['nama_cabang'] . '</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px;"><b>' . $data['nama_area'] . '</b></p>
						<p style="margin-bottom:0px;">' . ucwords($tempal) . '</p>
						<p style="margin-bottom:0px;">' . $data['nama_terminal'] . ' ' . $data['tanki_terminal'] . ', ' . $data['lokasi_terminal'] . '</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px;"><b>' . $data['nama_suplier'] . ' - ' . $data['nama_transportir'] . ', ' . $data['lokasi_suplier'] . '</b></p>
						<p style="margin-bottom:0px;">' . $data['nama_sopir'] . '</p>
						<p style="margin-bottom:0px;">' . $data['nomor_plat'] . '</p>
					</td>
					<td class="text-right">' . number_format($data['jum_vol']) . '</td>
					<td class="text-right">' . number_format($data['realisasi_volume']) . '</td>
					<td class="text-right">' . number_format($losses) . '</td>
					<td class="text-left">
						<p style="margin-bottom:0px;">' . ($data['no_spj'] ? '<b>No. SJ ' . $data['no_spj'] . '</b>' : '') . '</p>
						<p style="margin-bottom:0px;">Alat Ukur : ' . ($data['alat_ukur'] ? $data['alat_ukur'] : '-') . '</p>
						<p style="margin-bottom:0px;">' . ($data['rating'] ? 'Rating : ' . $data['rating'] . '/5' : '<i>Unrated</i>') . '</p>
					</td>
				</tr>';
		}
		$content .= '
			<tr>
				<td class="text-center bg-gray" colspan="4"><b>TOTAL</b></td>
				<td class="text-right bg-gray"><b>' . number_format($tot1) . '</b></td>
				<td class="text-right bg-gray"><b>' . number_format($tot2) . '</b></td>
				<td class="text-right bg-gray"><b>' . number_format($tot3) . '</b></td>
				<td class="text-right bg-gray"><input type="hidden" id="uriExp" value="' . $link . '" /></td>
			</tr>';
	}

	$json_data = array(
		"items"		=> $content,
		"pages"		=> $tot_page,
		"page"		=> $page,
		"totalData"	=> $tot_record,
		"infoData"	=> "Showing " . ($position + 1) . " - " . $count . " of " . $tot_record . " entries",
	);
	echo json_encode($json_data);
}

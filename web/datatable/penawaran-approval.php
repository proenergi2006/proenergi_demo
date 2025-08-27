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
$q1	= isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';
$q2	= isset($_POST["q2"]) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : '';
$q3	= isset($_POST["q3"]) ? htmlspecialchars($_POST["q3"], ENT_QUOTES) : '';
$s1 = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$s2 = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);
$s3 = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);

$p = new paging;
if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 3) {
	$sql = "
			select 
			case 
				when (a.flag_disposisi > 4 and a.flag_disposisi = 5 and a.coo_result = 0 and a.flag_approval = 0) then 1 
				when (a.flag_disposisi > 5 and a.flag_approval = 0) then 2 
				else 3 
			end as ordernya, 
		";
} else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 6) {
	$sql = "
			select 
			case 
				when (a.flag_disposisi > 3 and a.flag_disposisi = 4 and a.om_result = 0 and a.flag_approval = 0) then 1 
				when (a.flag_disposisi > 4 and a.flag_approval = 0) then 2 
				else 3 
			end as ordernya, 
		";
} else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 7) {
	$sql = "
			select 
			case 
				when (a.flag_disposisi > 1 and a.flag_disposisi = 2 and a.sm_mkt_result = 0 and a.flag_approval = 0) then 1 
				when (a.flag_disposisi > 1 and a.flag_disposisi = 3 and a.sm_wil_result = 0 and a.flag_approval = 0) then 2 
				when (a.flag_disposisi > 3 and a.flag_approval = 0) then 3 
				else 4 
			end as ordernya, 
		";
} else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 21) {
	$sql = "
			select 
			case 
				when (a.flag_disposisi = 6 and a.ceo_result = 0 and a.flag_approval = 0) then 1 
				when (a.flag_disposisi = 6 and a.flag_approval = 0) then 2 
				else 3 
			end as ordernya, 
		";
} else {
	$sql = "
			select a.id_penawaran as ordernya, 
		";
}

$sql .= "
		a.id_customer, a.id_penawaran, a.nomor_surat, a.volume_tawar, a.flag_approval, a.tgl_approval, a.flag_disposisi, a.harga_dasar, a.detail_rincian, a.created_time,
		b.nama_customer, b.kode_pelanggan, c.id_wilayah, c.fullname, d.nama_cabang, d.id_group_cabang, e.nama_area,
		if(a.flag_approval = 0 && a.flag_disposisi > 0, 1, 0) as position,
		CASE 
		WHEN f.id_penawaran IS NOT NULL THEN 'YA'
		WHEN a.flag_disposisi = 0 THEN '-'
		ELSE '-'  
		END AS penawaran_status
		from pro_penawaran a 
		join pro_customer b on a.id_customer = b.id_customer 
		join acl_user c on b.id_marketing = c.id_user 
		join pro_master_cabang d on a.id_cabang = d.id_master 
		join pro_master_area e on a.id_area = e.id_master
		LEFT JOIN (
		SELECT id_penawaran
		FROM pro_po_customer
		GROUP BY id_penawaran
	) f ON a.id_penawaran = f.id_penawaran 
		where 1=1 and a.created_time > '2025-01-01'
	";

if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 3) {
	//$sql .= " and (a.flag_disposisi > 4 or a.flag_approval > 0)";
} else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 6) {
	$sql .= " and c.id_role in (11,18,17)";
} else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 7) {
	$sql .= " and c.id_role in (11, 18, 17)";
	$sql .= " and a.flag_disposisi > 0";
	$sql .= " and (case when a.flag_disposisi > 2 then (a.id_cabang = '" . $s1 . "' or c.id_wilayah = '" . $s1 . "') else c.id_wilayah = '" . $s1 . "' end)";
}

if ($q1 != "") {
	$sql .= " and (upper(b.nama_customer) like '%" . strtoupper($q1) . "%' or a.nomor_surat like '" . strtoupper($q1) . "%' or b.kode_pelanggan = '" . $q1 . "')";
}
if ($q2 != "") {
	$sql .= " and a.id_cabang = '" . $q2 . "'";
}
if ($q3 != "") {
	$sql .= " and a.id_area = '" . $q3 . "'";
}


$tot_record = $con->num_rows($sql);
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);
$sql .= " order by ordernya, a.id_penawaran desc limit " . $position . ", " . $length;

$arrPosisi	= array(1 => "SPV", "BM", "BM", "OM", "COO", "CEO");
$arrSetuju	= array(1 => "Disetujui", "Ditolak");

$content = "";
if ($tot_record ==  0) {
	$content .= '<tr><td colspan="8" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
	$count 		= $position;
	$tot_page 	= ceil($tot_record / $length);
	$result 	= $con->getResult($sql);
	foreach ($result as $data) {
		$count++;
		$linkDetail	= BASE_URL_CLIENT . '/penawaran-approval-detail.php?' . paramEncrypt('idr=' . $data['id_customer'] . '&idk=' . $data['id_penawaran']);
		$background	= "";

		if ($data['flag_disposisi'] > 1 && $data['flag_disposisi'] < 4 && $data['ordernya'] <= 2) {
			$background	= 'style="background-color:#f5f5f5"';
		} else if ($data['flag_disposisi'] == '4' && $data['ordernya'] <= 1) {
			$background	= 'style="background-color:#f5f5f5"';
		} else if ($data['flag_disposisi'] == '5' && $data['ordernya'] <= 1) {
			$background	= 'style="background-color:#f5f5f5"';
		} else if ($data['flag_disposisi'] == '6' && $data['ordernya'] <= 1) {
			$background	= 'style="background-color:#f5f5f5"';
		}

		if ($data['flag_approval'] == 0 && $data['flag_disposisi'] == 0) {
			$status = "Terdaftar";
		} else if ($data['flag_approval'] == 0 && $data['flag_disposisi']) {
			if ($data['flag_disposisi'] > 1 && $data['flag_disposisi'] < 4) {
				$status = "Verifikasi " . $arrPosisi[$data['flag_disposisi']] . " " . $data['nama_cabang'];
			} else {
				$status = "Verifikasi " . $arrPosisi[$data['flag_disposisi']];
			}
		} else if ($data['flag_approval']) {
			if ($data['flag_disposisi'] > 1 && $data['flag_disposisi'] < 4) {
				$status = $arrSetuju[$data['flag_approval']] . " " . $arrPosisi[$data['flag_disposisi']] . " " . $data['nama_cabang'];
				$status .= "<br /><i>" . ($data['tgl_approval'] ? date("d/m/Y H:i:s", strtotime($data['tgl_approval'])) . " WIB" : "") . "</i>";
			} else {
				$status = $arrSetuju[$data['flag_approval']] . " " . $arrPosisi[$data['flag_disposisi']];
				$status .= "<br /><i>" . ($data['tgl_approval'] ? date("d/m/Y H:i:s", strtotime($data['tgl_approval'])) . " WIB" : "") . "</i>";
			}
		}

		$decode = json_decode($data['detail_rincian'], true);
		$jenis  = "";
		foreach ($decode as $arr1) {
			if ($arr1['rincian'] == "PPN" || $arr1['rincian'] == "PBBKB") {
				$nilai = $arr1['nilai'] . '%';
			} else {
				$nilai = "";
			}
			$jenis .= "<p>" . $arr1['rincian'] . " " . $nilai . " : " . number_format($arr1['biaya']) . "</p>";
		}

		$content .= '
				<tr class="clickable-row" data-href="' . $linkDetail . '"' . $background . '>
					<td class="text-center">' . $count . '</td>
					<td>
						<p style="margin-bottom: 0px"><b>' . $data['nomor_surat'] . '</b></p>
						<p style="margin-bottom: 0px"><i>' . $data['fullname'] . '</i></p>
					</td>
					<td>
						<p style="margin-bottom:0px;"><b>' . ($data['kode_pelanggan'] ? $data['kode_pelanggan'] : '-------') . '</b></p>
						<p style="margin-bottom: 0px">' . $data['nama_customer'] . '</p>
					</td>
					<td>' . $data['nama_cabang'] . '</td>
					<td>' . $data['nama_area'] . '</td>
					<td>
					<b>' . number_format($data['harga_dasar'], 0) . '</b>
					' . $jenis . '
					</td>
					<td>
					' . number_format($data['volume_tawar'], 0) . ' Liter
					</td>
					<td>' . $status . '</td>
				<td class="text-center" style="color: ' . ($data['penawaran_status'] == 'YA' ? 'green' : 'red') . ';">' . $data['penawaran_status'] . '</td>
					<td class="text-center action">
						<a class="margin-sm btn btn-action btn-info" title="Detail" href="' . $linkDetail . '"><i class="fa fa-table"></i></a>
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

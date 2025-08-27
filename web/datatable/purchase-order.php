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
$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);

$p = new paging;
$whereadd = '';
if ($sesrol != 1 and  $sesrol != 16) {
	$whereadd = " and a.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
}
$sql = "select a.*, b.nama_cabang, c.nomor_pr, (
		select 
			group_concat(g.nama_customer separator ', ')
		from
			pro_po_detail d
			left join (
				pro_po_customer_plan e
				join (
					pro_po_customer f
					join pro_customer g on f.id_customer=g.id_customer
				) on e.id_poc=f.id_poc
			) on e.id_plan=d.id_plan
		where a.id_po=d.id_po
	) as nama_customer
	 from pro_po a join pro_master_cabang b on a.id_wilayah = b.id_master join pro_pr c on a.id_pr = c.id_pr where 1=1 " . $whereadd . " and a.disposisi_po != -1";

if ($q1 != "")
	$sql .= " and (a.tanggal_po = '" . tgl_db($q1) . "' or a.nomor_po like '" . strtoupper($q1) . "%' or c.nomor_pr like '" . strtoupper($q1) . "%')";
if ($q2 != "") {
	if ($q2 == 1)
		$sql .= " and a.disposisi_po = 0 and a.po_approved = 0";
	else if ($q2 == 2)
		$sql .= " and a.disposisi_po = 2 and a.po_approved = 0";
	else if ($q2 == 3)
		$sql .= " and a.disposisi_po = 1 and a.po_approved = 0";
	else if ($q2 == 4)
		$sql .= " and a.po_approved = 1";
	else if ($q2 == 5)
		$sql .= " and a.po_approved = 1 and a.po_accepted = 1";
}

$tot_record = $con->num_rows($sql);
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);
$sql .= " order by a.id_po desc limit " . $position . ", " . $length;

$count = 0;
$content = "";
if ($tot_record <= 0) {
	$content .= '<tr><td colspan="6" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
	$count 		= $position;
	$tot_page 	= ceil($tot_record / $length);
	$result 	= $con->getResult($sql);
	foreach ($result as $data) {
		$sqlpo = "SELECT a.*, b.volume_kirim FROM pro_po_detail a JOIN pro_po_customer_plan b ON a.id_plan=b.id_plan WHERE a.id_po='" . $data['id_po'] . "'";
		$respo = $con->getResult($sqlpo);

		$tgl_kirim = "";
		$no_spj = "";
		$volume_kirim = "";

		foreach ($respo as $rp) {
			$tgl_kirim .= tgl_indo($rp['tgl_kirim_po']) . "<br/>";
			$volume_kirim .= number_format($rp['volume_kirim']) . "<br/>";
			$no_spj .= $rp['no_spj'] . "<br/>";
		}

		$count++;
		$linkDetail	= BASE_URL_CLIENT . '/purchase-order-detail.php?' . paramEncrypt('idr=' . $data['id_po']);
		$linkCetak	= ACTION_CLIENT . '/purchase-order-cetak.php?' . paramEncrypt('idr=' . $data['id_po']);
		$linkTagih	= paramEncrypt("tagihan_po_transportir#|#" . $data['id_po']);
		$status		= "";

		if ($data['disposisi_po'] == 1 && $data['is_new'] && !$data['po_approved'])
			$background = ' style="background-color:#f5f5f5"';
		else $background = '';

		if ($data['po_approved'] && !$data['po_accepted']) {
			if ($data['f_proses_selisih'] == 0 && $data['ada_selisih'] == 1) {
				$status = 'Verifikasi CEO';
			} else if ($data['f_proses_selisih'] == 0 && $data['ada_selisih'] == 2) {
				$status = 'Verifikasi Manager Logistik';
			} else {
				$status = 'Terverifikasi<br><i>' . date("d/m/Y H:i:s", strtotime($data['tgl_approved'])) . ' WIB</i>';
			}
		} else if ($data['po_approved'] && $data['po_accepted'])
			$status = 'Tagihan Diterima<br><i>' . date("d/m/Y H:i:s", strtotime($data['tgl_po_accepted'])) . ' WIB</i>';
		else if ($data['disposisi_po'] == 0)
			$status = 'Terdaftar';
		else if ($data['disposisi_po'] == 1)
			$status = 'Konfirmasi Logistik';
		else if ($data['disposisi_po'] == 2)
			$status = 'Verifikasi Transportir';
		else $status = '';

		$nama_customers = '';
		$nama_customer = explode(',', $data['nama_customer']);
		foreach ($nama_customer as $v) $nama_customers .= $v . '<br/>';

		$content .= '
					    <tr class="clickable-row" data-href="' . $linkDetail . '"' . $background . '>
					        <td class="text-center">' . $count . '</td>
					        <td class="text-center">' . tgl_indo($data['tanggal_po']) . '</td>
					        <td>' . $data['nomor_po'] . '</td>
					        <td>' . $data['nomor_pr'] . '</td>
					        <td>' . $nama_customers . '</td>
					        <td class="text-center">' . $volume_kirim . '</td>
					        <td class="text-center">' . $no_spj . '</td>
					        <td class="text-center">' . $tgl_kirim . '</td>
					        <td>' . $status . '</td>
					        <td class="text-center action">
					            ';

		// Tambahkan kondisi untuk menampilkan tombol
		if ($sesrol != 1 && $sesrol != 16) {
			$content .= '<a class="margin-sm btn btn-action btn-info" title="Detail" href="' . $linkDetail . '"><i class="fa fa-info-circle"></i></a>
			    	<a class="margin-sm btn btn-action btn-success" title="Terima Tagihan" data-param-idx="' . $linkTagih . '" data-action="terimaTagihan"><i class="fa fa-check"></i></a>';
		}

		$content .= '
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

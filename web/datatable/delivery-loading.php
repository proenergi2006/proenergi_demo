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
$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$id_role = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);

$p = new paging;
/*$sql = "select a.*, c.nama_terminal, c.lokasi_terminal, c.tanki_terminal, d.nama_cabang from pro_po_ds a join pro_master_terminal c on a.id_terminal = c.id_master 
			join pro_master_cabang d on a.id_wilayah = d.id_master where a.id_wilayah = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."'";*/
$whereadd = '';
if ($sesrol > 1) {
	if ($id_role == 24) {
		$whereadd = " and a.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "' and a.is_submitted = '1' and a.is_loco = '0'";
	} else {
		$whereadd = " and a.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
	}
}
$sql = "
		select a.*, c.nama_terminal, c.lokasi_terminal, c.tanki_terminal, d.nama_cabang, if(a.is_loco = 0,e.arr_nomor_po,'LOCO') as arr_nomor_po, (
			select
				group_concat(pc.nama_customer separator ', ')
			from
				pro_po_ds_detail as ppdsd
				left join (
					pro_po_customer ppc
					join pro_customer pc on ppc.id_customer=pc.id_customer
				) on ppdsd.id_poc=ppc.id_poc
			where ppdsd.id_ds=a.id_ds order by ppdsd.nomor_urut_ds asc
		) as nama_customer
		from pro_po_ds a 
		join pro_master_terminal c on a.id_terminal = c.id_master 
		join pro_master_cabang d on a.id_wilayah = d.id_master 
		left join (
			select group_concat(a.nomor_po SEPARATOR '#') as arr_nomor_po, a.id_ds
			from(
				select distinct a.id_po, a.id_ds, b.nomor_ds, c.nomor_po 
				from pro_po_ds_detail a 
				join pro_po_ds b on a.id_ds = b.id_ds 
				join pro_po c on a.id_po = c.id_po
			) a
			group by a.id_ds
		) e on a.id_ds = e.id_ds
		where 1=1 " . $whereadd;

if ($q1 != "")
	$sql .= " and (a.tanggal_ds = '" . tgl_db($q1) . "' or a.nomor_ds like '" . strtoupper($q1) . "%' or upper(c.nama_terminal) like '%" . strtoupper($q1) . "%' or upper(e.arr_nomor_po) like '%" . strtoupper($q1) . "%')";
if ($q2 != "")
	$sql .= " and a.is_loco = '" . $q2 . "'";

$tot_record = $con->num_rows($sql);
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);
$sql .= " order by
CASE WHEN a.is_submitted = 0 AND a.tanggal_ds >= '2023-01-01' THEN a.is_submitted AND a.tanggal_ds END DESC,
CASE WHEN a.is_submitted = 1 AND a.tanggal_ds >= '2023-01-01' THEN a.tanggal_ds END DESC,
CASE WHEN a.is_submitted = 0 AND a.tanggal_ds < '2023-01-01' THEN a.is_submitted END ASC,
CASE WHEN a.is_submitted = 1 AND a.tanggal_ds < '2023-01-01' THEN a.tanggal_ds END DESC,
a.tanggal_ds DESC limit " . $position . ", " . $length;

$content = "";
$count = 0;
if ($tot_record <= 0) {
	$content .= '<tr><td colspan="7" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
	$count 		= $position;
	$tot_page 	= ceil($tot_record / $length);
	$result 	= $con->getResult($sql);
	foreach ($result as $data) {
		$sqlds = "SELECT a.*, b.tanggal_kirim, c.volume_po FROM pro_po_ds_detail a JOIN pro_po_customer_plan b ON a.id_plan=b.id_plan JOIN pro_po_detail c ON a.id_pod=c.id_pod WHERE a.id_ds='" . $data['id_ds'] . "'";
		$resds = $con->getResult($sqlds);

		$tgl_kirim = "";
		$no_do = "";
		$no_spj = "";
		$volume = "";

		foreach ($resds as $rp) {
			$tgl_kirim .= date("d/m/Y", strtotime($rp['tanggal_kirim'])) . "<br/>";
			$volume .= number_format($rp['volume_po']) . "<br/>";
			$no_do .= $rp['nomor_do'] . "<br/>";
		}

		$count++;
		$linkDetail	= BASE_URL_CLIENT . '/delivery-loading-detail.php?' . paramEncrypt('idr=' . $data['id_ds']);
		$terminal1 	= ($data['nama_terminal']) ? $data['nama_terminal'] : '';
		$terminal2 	= ($data['tanki_terminal']) ? ' - ' . $data['tanki_terminal'] : '';
		$terminal3 	= ($data['lokasi_terminal']) ? '<br />' . $data['lokasi_terminal'] : '';
		$terminal 	= $terminal1 . $terminal2 . $terminal3;
		$tmp_nom_po = explode("#", $data['arr_nomor_po']);
		$arr_nom_po = "";
		foreach ($tmp_nom_po as $row) {
			$arr_nom_po .= '<p style="margin-bottom:0px; font-size:9px; font-family:arial;">' . $row . '</p>';
		}

		if (!$data['is_submitted'] == 2) {

			if ($data['tanggal_ds'] >= '2023-01-01') {
				$background = ' style="background-color:#f5f5f5"';
			} else {
				$background = '';
			}
		} else {
			$background = '';
		}

		$nama_customers = '';
		$nama_customer = explode(',', $data['nama_customer']);
		foreach ($nama_customer as $v) $nama_customers .= $v . '<br/>';

		$content .= '
				<tr class="clickable-row" data-href="' . $linkDetail . '"' . $background . '>
					<td class="text-center">' . $count . '</td>
					<td class="text-center">' . date("d/m/Y", strtotime($data['tanggal_ds'])) . '</td>
					<td>' . $data['nomor_ds'] . '</td>
					<td>' . $arr_nom_po . '</td>
					<td>' . $nama_customers . '</td>
					<td class="text-center">' . $tgl_kirim . '</td>
					<td class="text-center">' . $volume . '</td>
					<td class="text-center">' . $no_do . '</td>
					<td>' . $data['nama_cabang'] . '</td>
					<td>' . $terminal . '</td>
					<td class="text-center action"><a class="margin-sm btn btn-action btn-info" title="Detail" href="' . $linkDetail . '"><i class="fa fa-info-circle"></i></a></td>
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

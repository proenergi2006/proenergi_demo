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
$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$arrRol = array(7 => "BM", 6 => "OM", 4 => "CFO", 15 => "MGR Finance");

$q1	= isset($_POST["q1"]) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : '';
$q2	= isset($_POST["q2"]) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : '';

$p = new paging;
$sql = "
		select 
			n.*, 
			a.tanggal_poc, 
			a.harga_poc, 
			a.volume_poc, 
			a.nomor_poc, 
			b.nama_customer, 
			b.alamat_customer, 
			b.kode_pelanggan, 
			c.nama_kab, 
			d.nama_prov, 
			e.fullname as marketing,
			f.pembulatan
		from pro_sales_confirmation n 
		left join pro_po_customer a on a.id_poc = n.id_poc 
		join pro_customer b on a.id_customer = b.id_customer 
		join pro_master_kabupaten c on b.kab_customer = c.id_kab 
		join pro_master_provinsi d on b.prov_customer = d.id_prov 
		join acl_user e on b.id_marketing = e.id_user
		join pro_penawaran f on f.id_penawaran = a.id_penawaran
		left join pro_sales_confirmation_approval s on s.id_sales = n.id 
		where 1 = 1
	";

$sql2 = "
		select 
			n.*, 
			a.tanggal_poc, 
			a.harga_poc, 
			a.volume_poc, 
			a.nomor_poc, 
			b.nama_customer, 
			b.alamat_customer, 
			b.kode_pelanggan, 
			c.nama_kab, 
			d.nama_prov, 
			e.fullname as marketing,
			f.pembulatan
		from pro_sales_confirmation n 
		left join pro_po_customer a on a.id_poc = n.id_poc 
		join pro_customer b on a.id_customer = b.id_customer 
		join pro_master_kabupaten c on b.kab_customer = c.id_kab 
		join pro_master_provinsi d on b.prov_customer = d.id_prov 
		join acl_user e on b.id_marketing = e.id_user
		join pro_penawaran f on f.id_penawaran = a.id_penawaran
		left join pro_sales_confirmation_approval s on s.id_sales = n.id
		where 1 = 1 
	";

if ($sesrol == 10) {
	$sql .= "and n.id_wilayah = " . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
	$sql2 .= "and n.id_wilayah = " . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
	$sql2 .= " and (disposisi = 1 and n.flag_approval = 0)";
} else if ($sesrol == 7) {
	$sql .= "and n.disposisi > 1 and n.id_wilayah = " . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
	$sql2 .= "and n.disposisi > 1 and n.id_wilayah = " . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
	$sql2 .= " and (disposisi = 2 and n.flag_approval = 0)";
	$sql2 .= " and s.bm_result = 0";
} else if ($sesrol == 6) {
	$sql .= "and n.disposisi > 2 or n.flag_approval != 0";
	$sql2 .= " and (disposisi = 3 and n.flag_approval = 0)";
} else if ($sesrol == 15) {
	$sql .= "and n.disposisi > 3 or n.flag_approval != 0";
	$sql2 .= " and (disposisi = 4 and n.flag_approval = 0)";
} else if ($sesrol == 4) {
	$sql .= "and n.disposisi > 4 or n.flag_approval != 0";
	$sql2 .= " and (disposisi = 5 and n.flag_approval = 0)";
}

if ($q1) {
	$sql .= ' and b.nama_customer like "%' . $q1 . '%"';
	$sql2 .= ' and b.nama_customer like "%' . $q1 . '%"';
}

$tot_record = $con->num_rows($sql);
$tot_page 	= ceil($tot_record / $length);
$page		= ($start > $tot_page) ? $start - 1 : $start;
$position 	= $p->findPosition($length, $tot_record, $page);

$arrPosisi 	= array(1 => "Adm Finance", 2 => "BM", 3 => "OM", 4 => "MGR Finance", 5 => "CFO");
$id = array();

$content = "";
$count = 0;
if ($tot_record <= 0) {
	$content .= '<tr><td colspan="7" style="text-align:center">Data tidak ditemukan </td></tr>';
} else {
	$count 		= $position;
	$tot_page 	= ceil($tot_record / $length);

	if ($sesrol != 3) {
		$sql2 .= " order by id asc limit " . $position . ", " . $length;
		$result2 	= $con->getResult($sql2);

		foreach ($result2 as $data) {
			$count++;
			$length--;
			$linkDetail	= BASE_URL_CLIENT . '/sales_confirmation_form.php?' . paramEncrypt('id=' . $data['id'] . '&idc=' . $data['id_customer'] . '&idp=' . $data['id_poc']);
			$temp 		= strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
			$alamat		= $data['alamat_customer'] . " " . ucwords($temp) . " " . $data['nama_prov'];
			$kodeCust	= ($data['kode_pelanggan']) ? '<b>' . $data['kode_pelanggan'] . '</b> - ' : '';

			$background = 'style="background-color:#f5f5f5"';

			$disposisi = 'Verifikasi ' . $arrPosisi[$data['disposisi']];
			// $disposisi = 'Verifikasi '.$data['id'];

			if ($data['pembulatan'] == 0) {
				$harga_pocnya = number_format($data['harga_poc'], 2);
			} elseif ($data['pembulatan'] == 1) {
				$harga_pocnya = number_format($data['harga_poc'], 0);
			} elseif ($data['pembulatan'] == 2) {
				$harga_pocnya = number_format($data['harga_poc'], 4);
			}

			$id[] = $data['id'];

			$content .= '
					<tr class="clickable-row" data-href="' . $linkDetail . '" ' . $background . '>
	                    <td class="text-center">' . $count . '</td>
						<td class="text-center">
							<p style="margin-bottom: 0px">' . $kodeCust . $data['nama_customer'] . '</p>
						</td>
						<td class="text-center">' . $data['marketing'] . '</td>
						<td class="text-center">
							<p style="margin-bottom: 0px"><b>' . $data['nomor_poc'] . '</b> - ' . tgl_indo($data['tanggal_poc']) . '</p>
						</td>
						<td class="text-center">
							<p style="margin-bottom: 0px">' . number_format($data['volume_poc']) . ' Liter (Rp. ' . $harga_pocnya . '/liter)</p>
	                    </td>
	                    <td class="text-center">
							<p style="margin-bottom: 0px">' . $disposisi . '</p>
						</td>
						<td class="text-center action">
							<a class="margin-sm btn btn-action btn-info" title="Detail" href="' . $linkDetail . '"><i class="fa fa-info-circle"></i></a>
	            		</td>
					</tr>';
		}
	}

	if ($id)
		$sql .= " and n.id not in(" . implode(",", $id) . ")";

	$sql .= " order by id desc limit " . $position . ", " . $length;

	$result 	= $con->getResult($sql);

	foreach ($result as $data) {

		$linkDetail	= BASE_URL_CLIENT . '/sales_confirmation_form.php?' . paramEncrypt('id=' . $data['id'] . '&idc=' . $data['id_customer'] . '&idp=' . $data['id_poc']);
		$temp 		= strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
		$alamat		= $data['alamat_customer'] . " " . ucwords($temp) . " " . $data['nama_prov'];
		$kodeCust	= ($data['kode_pelanggan']) ? '<b>' . $data['kode_pelanggan'] . '</b> - ' : '';
		$background = '';

		if ($data['pembulatan'] == 0) {
			$harga_pocnya = number_format($data['harga_poc'], 2);
		} elseif ($data['pembulatan'] == 1) {
			$harga_pocnya = number_format($data['harga_poc'], 0);
		} elseif ($data['pembulatan'] == 2) {
			$harga_pocnya = number_format($data['harga_poc'], 4);
		}

		if ($data['flag_approval'] == 1)
			$disposisi = 'Terverifikasi ' . $arrRol[$data['role_approved']] . '<br/><i>' . date("d/m/Y H:i:s", strtotime($data['tgl_approved'])) . '</i> WIB';
		else if ($data['flag_approval'] == 2)
			$disposisi = 'Ditolak ' . $arrRol[$data['role_approved']] . '<br/><i>' . date("d/m/Y H:i:s", strtotime($data['tgl_approved'])) . '</i> WIB';
		else if (isset($arrPosisi[$data['disposisi']]))
			$disposisi = 'Verifikasi ' . $arrPosisi[$data['disposisi']];
		else $disposisi = '';
		// $disposisi = $data['disposisi'];

		if ($length > 0) {
			$length--;
			$count++;
			$content .= '
					<tr class="clickable-row" data-href="' . $linkDetail . '" ' . $background . '>
						<td class="text-center">' . $count . '</td>
						<td class="text-center">
							<p style="margin-bottom: 0px">' . $kodeCust . $data['nama_customer'] . '</p>
						</td>
						<td class="text-center">' . $data['marketing'] . '</td>
						<td class="text-center">
							<p style="margin-bottom: 0px"><b>' . $data['nomor_poc'] . '</b> - ' . tgl_indo($data['tanggal_poc']) . '</p>
						</td>
						<td class="text-center">
							<p style="margin-bottom: 0px">' . number_format($data['volume_poc']) . ' Liter (Rp. ' . $harga_pocnya . '/liter)</p>
						</td>
						<td class="text-center">
							<p style="margin-bottom: 0px">' . $disposisi . '</p>
						</td>
						<td class="text-center action">
							<a class="margin-sm btn btn-action btn-info" title="Detail" href="' . $linkDetail . '"><i class="fa fa-info-circle"></i></a>
						</td>
					</tr>';
		}
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

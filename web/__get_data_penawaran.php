<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$conSub = new Connection();
$q1 	= htmlspecialchars($_POST["q1"], ENT_QUOTES);
$id_customer 	= htmlspecialchars($_POST["id_customer"], ENT_QUOTES);

$sql = "select a.*, b.nama_cabang, c.jenis_produk, c.merk_dagang, d.nama_area from pro_penawaran a 
			join pro_master_cabang b on a.id_cabang = b.id_master join pro_master_produk c on a.produk_tawar = c.id_master 
			join pro_master_area d on a.id_area = d.id_master where a.id_penawaran = '" . $q1 . "'";
$rsm = $conSub->getRecord($sql);
$formula = json_decode($rsm['detail_formula'], true);
if ($rsm['perhitungan'] == 1) {
	if ($rsm['pembulatan'] == 0) {
		$nilainya = number_format($rsm['harga_dasar'], 2);
		$harganya = number_format($rsm['harga_dasar'], 2);
	} elseif ($rsm['pembulatan'] == 1) {
		$nilainya = number_format($rsm['harga_dasar'], 0);
		$harganya = number_format($rsm['harga_dasar'], 0);
	} elseif ($rsm['pembulatan'] == 2) {
		$harganya = number_format($rsm['harga_dasar'], 4);
		$nilainya = number_format($rsm['harga_dasar'], 4);
	}
} else {
	$harganya = '';
	$nilainya = '';
	foreach ($formula as $jenis) {
		$harganya .= '<p style="margin-bottom:0px">' . $jenis . '</p>';
	}
}

$sql2 = "SELECT * FROM pro_master_penerima_refund WHERE id_customer='" . $id_customer . "' AND is_active='1'";
$row2 = $conSub->getResult($sql2);

$answer	= array();
if ($rsm["id_penawaran"]) {
	if ($rsm["refund_tawar"] != 0) {
		$refund = '<tr>
						<td>Refund</td>
						<td>' . $rsm["refund_tawar"] . '</td>
					</tr>';
	} else {
		$refund = "";
	}
	$answer['harga'] = $nilainya;
	$answer['produk'] = $rsm['produk_tawar'];
	$answer['refund'] = $rsm['refund_tawar'];
	$answer['addPenerimRefund'] = BASE_URL_CLIENT . '/add-master-penerima-refund.php?' . paramEncrypt('idcust=' . $rsm['id_customer']);
	$answer["items"] =
		'<div class="row">
			<div class="col-md-offset-2 col-sm-6">
				<div class="table-responsive">
					<table class="table table-bordered">
						<tr>
							<td colspan="2" class="text-center bg-gray"><b>KETERANGAN</b></td>
						</tr>
						<tr>
							<td width="160">Masa berlaku harga</td>
							<td>' . tgl_indo($rsm['masa_awal']) . " - " . tgl_indo($rsm["masa_akhir"]) . '</td>
						</tr>
						<tr>
							<td>Cabang</td>
							<td>' . $rsm['nama_cabang'] . '</td>
						</tr>
						<tr>
							<td>Area</td>
							<td>' . $rsm['nama_area'] . '</td>
						</tr>
						<tr>
							<td>Produk</td>
							<td>' . $rsm['jenis_produk'] . ' - ' . $rsm['merk_dagang'] . '</td>
						</tr>
						<tr>
							<td>Ongkos Angkut</td>
							<td>' . number_format($rsm['oa_kirim']) . '</td>
						</tr>
						<tr>
							<td>Volume</td>
							<td>' . number_format($rsm['volume_tawar']) . ' Liter</td>
						</tr>
						<tr>
							<td>Harga</td>
							<td>' . $harganya . '</td>
						</tr>
						' . $refund . '
					</table>
				</div>
			</div>
		</div>';
	// $answer['penerima_refund'][] = array('id' => '', 'nama' => '', 'divisi' => '');
	$answer['penerima_refund'] = $row2;
} else {
	$answer['harga'] = '';
	$answer['refund'] = '';
	$answer['refund_items'] = '';
	$answer["items"] = '';
}
$conSub->close();
echo json_encode($answer);

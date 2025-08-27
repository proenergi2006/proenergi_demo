<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$arr	= array();
$param 	= htmlspecialchars(paramDecrypt($_POST["param"]), ENT_QUOTES);
$post 	= explode("#", $param);
$tipe 	= $post[0];
$nom_dn = $post[1];
$namadn = $post[2];
$alamat = $post[3];
$wil_oa = $post[4];
$volume = $post[5];
$produk = $post[6];
$supler	= $post[7];
$no_spj = $post[8];
$truck 	= $post[9];
$sopir 	= $post[10];
if ($tipe == 'truck') {
	echo '
		<table class="table table-bordered">
			<thead>
				<tr>
					<th class="text-center" width="60%">Customer</th>
					<th class="text-center" width="40%">Detail Pengiriman</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>' . $nom_dn . '</b></p>
						<p style="margin-bottom:0px">' . $namadn . '</p>
						<p style="margin-bottom:0px">' . $alamat . '</p>
						<p style="margin-bottom:0px">Wilayah OA : ' . $wil_oa . '</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>' . $supler . '</b></p>
						<p style="margin-bottom:0px">' . number_format($volume) . ' Liter ' . $produk . '</p>
						<p style="margin-bottom:0px">Truck &nbsp;: ' . $truck . '</p>
						<p style="margin-bottom:0px">Driver : ' . $sopir . '</p>
					</td>
				</tr>
			</tbody>
		</table>
		<input type="hidden" id="volume_detail_hidden" value="' . floatval($volume) . '">';
} else if ($tipe == 'kapal') {
	echo '
		<table class="table table-bordered">
			<thead>
				<tr>
					<th class="text-center" width="60%">Customer</th>
					<th class="text-center" width="40%">Detail Pengiriman</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>' . $nom_dn . '</b></p>
						<p style="margin-bottom:0px">' . $namadn . '</p>
						<p style="margin-bottom:0px">' . $alamat . '</p>
						<p style="margin-bottom:0px">Wilayah OA : ' . $wil_oa . '</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>' . $supler . '</b></p>
						<p style="margin-bottom:0px">' . number_format($volume) . ' Liter ' . $produk . '</p>
						<p style="margin-bottom:0px">Vessel &nbsp;: ' . $truck . '</p>
						<p style="margin-bottom:0px">Captain : ' . $sopir . '</p>
					</td>
				</tr>
			</tbody>
		</table>';
} else if ($tipe == 'po_transportir') {
	echo '
		<table class="table table-bordered">
			<thead>
				<tr>
					<th class="text-center" width="55%">Nomor PO</th>
					<th class="text-center" width="45%">SPJ</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>' . $nom_dn . '</b></p>
						<p style="margin-bottom:0px">' . $namadn . '</p>
						<p style="margin-bottom:0px">' . $alamat . '</p>
						<p style="margin-bottom:0px">Wilayah OA : ' . $wil_oa . '</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>' . $no_spj . '</b></p>
						<p style="margin-bottom:0px">' . number_format($volume) . ' Liter ' . $produk . '</p>
						<p style="margin-bottom:0px">Truck &nbsp;: ' . $truck . '</p>
						<p style="margin-bottom:0px">Driver : ' . $sopir . '</p>
					</td>
				</tr>
			</tbody>
		</table>';
}

<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$conSub = new Connection();
	$q1 	= htmlspecialchars($_POST["q1"], ENT_QUOTES);
	$q2 	= htmlspecialchars($_POST["q2"], ENT_QUOTES);
	$q3 	= htmlspecialchars($_POST["q3"], ENT_QUOTES);
	$q4 	= htmlspecialchars($_POST["q4"], ENT_QUOTES);
	if($q1 && $q2 && $q3 && $q4){
		$cek = "
			select a.nama_vendor, b.nama_area, c.jenis_produk, c.merk_dagang, d.nama_terminal, d.lokasi_terminal, d.tanki_terminal
			from pro_master_vendor a, pro_master_area b, pro_master_produk c, pro_master_terminal d
			where a.id_master = '".$q1."' and b.id_master = '".$q2."' and c.id_master = '".$q3."' and d.id_master = '".$q4."'";
		$row = $conSub->getRecord($cek);
		$tm1 = ($row['nama_terminal'])?$row['nama_terminal']:'';
		$tm2 = ($row['tanki_terminal'])?' - '.$row['tanki_terminal']:'';
		$tm3 = ($row['lokasi_terminal'])?', '.$row['lokasi_terminal']:'';
		$tmn = $tm1.$tm2.$tm3;
				
		$sql = "select a.* from pro_inventory_vendor a 
				where a.out_inven > 0 and a.id_vendor = '".$q1."' and a.id_area = '".$q2."' and a.id_produk = '".$q3."' and a.id_terminal = '".$q4."' 
				order by tanggal_inven desc limit 1";
		$rgs = $conSub->getRecord($sql);
		$tot = ($rgs['awal_inven'] + $rgs['in_inven'] + $rgs['adj_inven']) - $rgs['out_inven'];
		echo '
		<p class="text-center"><b>INVENTORY VENDOR</b></p>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td style="font-size:14px; padding:3px;" width="85">Vendor</td>
				<td style="font-size:14px; padding:3px;" class="text-center" width="15">:</td>
				<td style="font-size:14px; padding:3px;">'.$row['nama_vendor'].'</td>
			</tr>
			<tr>
				<td style="font-size:14px; padding:3px;">Area</td>
				<td style="font-size:14px; padding:3px;" class="text-center">:</td>
				<td style="font-size:14px; padding:3px;">'.$row['nama_area'].'</td>
			</tr>
			<tr>
				<td style="font-size:14px; padding:3px;">Produk</td>
				<td style="font-size:14px; padding:3px;" class="text-center">:</td>
				<td style="font-size:14px; padding:3px;">'.$row['merk_dagang'].'</td>
			</tr>
			<tr>
				<td style="font-size:14px; padding:3px;">Depot</td>
				<td style="font-size:14px; padding:3px;" class="text-center">:</td>
				<td style="font-size:14px; padding:3px;">'.$tmn.'</td>
			</tr>
		</table>';
		
		echo '
		<div class="box-body table-responsive">
			<table class="table table-bordered col-sm-top">
				<thead>
					<tr>
						<th class="text-center" width="16%">TANGGAL</th>
						<th class="text-center" width="16%">BEGINNING</th>
						<th class="text-center" width="17%">INPUT</th>
						<th class="text-center" width="17%">OUTPUT</th>
						<th class="text-center" width="17%">ADJ INV</th>
						<th class="text-center" width="17%">ENDING</th>
					</tr>
				</thead>
				<tbody>';
		if($rgs['id_master']){
			echo '
			<tr>
				<td class="text-center">'.date("d/m/Y", strtotime($rgs['tanggal_inven'])).'</td>
				<td class="text-right">'.number_format($rgs['awal_inven']).'</td>
				<td class="text-right">'.number_format($rgs['in_inven']).'</td>
				<td class="text-right">'.number_format($rgs['out_inven']).'</td>
				<td class="text-right">'.number_format($rgs['adj_inven']).'</td>
				<td class="text-right">'.number_format($tot).'</td>
			</tr>';
		} else echo '<tr><td class="text-center" colspan="6">Tidak ada data transaksi terakhir</td></tr>';

		echo '</tbody></table></div>';
	} else echo '<p class="text-center" style="margin-bottom:0px">Data tidak ditemukan</p>';

	$conSub->close();
?>

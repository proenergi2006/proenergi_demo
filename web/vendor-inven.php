<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$enk  	= decode($_SERVER['REQUEST_URI']);
$con 	= new Connection();
$flash	= new FlashAlerts;
$arrBln = array(1 => "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

$q1 = (isset($enk['q1']) && $enk['q1'] ? htmlspecialchars($enk['q1'], ENT_QUOTES) : NULL);
$q2 = (isset($enk['q2']) && $enk['q2'] ? htmlspecialchars($enk['q2'], ENT_QUOTES) : date('m'));
$q3 = (isset($enk['q3']) && $enk['q3'] ? htmlspecialchars($enk['q3'], ENT_QUOTES) : date('Y'));
$q4 = (isset($enk['q4']) && $enk['q4'] ? htmlspecialchars($enk['q4'], ENT_QUOTES) : NULL);
$q5 = (isset($enk['display']) && $enk['display'] ? 1 : 0);

$sesRole = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$sesGrup = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);
$sesCbng = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

// Cek peran pengguna
$required_role = ['1', '2', '21', '4', '3', '15', '10', '16', '6', '5'];
// Misalnya halaman ini hanya untuk superadmin
if (!in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), $required_role)) {
	// Pengguna tidak memiliki peran yang tepat, redirect ke halaman lain atau tampilkan pesan akses ditolak
	$flash->add("warning", "Akses ditolak.", BASE_URL_CLIENT . "/home.php");
	// exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("formatNumber", "jqueryUI", "myGrid", "scrolltab"), "css" => array("jqueryUI", "scrolltab"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory . "/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
		<aside class="right-side">
			<section class="content-header">
				<h1>Inventory By Vendor</h1>
			</section>
			<section class="content">

				<?php $flash->display(); ?>
				<div class="box box-info">
					<div class="box-header with-border">
						<p style="font-size:18px; margin-bottom:0px;"><b>PENCARIAN</b></p>
					</div>
					<div class="box-body">
						<form name="sFrm" id="sFrm" method="post" class="form-validasi" action="<?php echo BASE_URL_CLIENT . "/vendor-inven-terminal.php"; ?>">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group row">
										<label class="col-md-12">Depot Terminal</label>
										<div class="col-md-12">
											<select id="q1" name="q1" class="form-control select2">
												<option></option>
												<?php $con->fill_select("id_master", "concat(nama_terminal,' ',tanki_terminal)", "pro_master_terminal", $q1, "where is_active=1", "id_master", false); ?>
											</select>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group row">
										<label class="col-md-12">Produk</label>
										<div class="col-md-12">
											<select id="q4" name="q4" class="form-control select2">
												<option></option>
												<?php $con->fill_select("id_master", "concat(jenis_produk,' - ',merk_dagang)", "pro_master_produk", $q4, "where is_active=1", "id_master", false); ?>
											</select>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group row">
										<label class="col-md-12">Bulan</label>
										<div class="col-md-6">
											<select id="q2" name="q2" class="form-control select2">
												<option></option>
												<?php
												foreach ($arrBln as $i3 => $t3) {
													$selected = ($q2 == $i3 ? 'selected' : '');
													echo '<option value="' . $i3 . '" ' . $selected . '>' . $t3 . '</option>';
												}
												?>
											</select>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group row">
										<label class="col-md-12">Tahun</label>
										<div class="col-md-6">
											<select id="q3" name="q3" class="form-control select2">
												<option></option>
												<?php
												for ($i = 2022; $i <= date('Y') + 1; $i++) {
													$selected = ($q3 == $i ? 'selected' : '');
													echo '<option ' . $selected . '>' . $i . '</option>';
												}
												?>
											</select>
										</div>
									</div>
								</div>
							</div>

							<hr style="border-top:4px double #ddd; margin:10px 0 15px;" />
							<button type="button" class="btn btn-primary btn-sm jarak-kanan" name="btnSearch" id="btnSearch" style="min-width:120px;">
								<i class="fa fa-search jarak-kanan"></i> Search
							</button>
							<a href="<?php echo BASE_URL_CLIENT . '/vendor-inven.php'; ?>" class="btn btn-success btn-sm" style="min-width:120px;">
								<i class="fa fa-reply jarak-kanan"></i> Batal
							</a>


						</form>
					</div>
				</div>

				<div id="html-tabelnya">
					<?php
					if ($q5) {
						$nextMonth = date("Y-m-d", strtotime('+1 month', strtotime($q3 . '-' . $q2 . '-01')));

						$cek = "
						select a.id_produk, a.id_terminal, a.id_vendor, a.nama_vendor, 
						case 
							when b.tanggal_inven is null then a.tanggal_inven 
							when b.tanggal_inven is not null and b.tanggal_inven > a.tanggal_inven then b.tanggal_inven 
							else a.tanggal_inven 
						end as tanggal_inven 
						from (
							select min(a.tanggal_inven) as tanggal_inven, a.id_produk, a.id_terminal, 
							a.id_vendor, upper(b.nama_vendor) as nama_vendor  
							from pro_inventory_depot a 
							join pro_master_vendor b on a.id_vendor = b.id_master 
							where id_terminal = '" . $q1 . "' and id_produk = '" . $q4 . "' 
							group by a.id_produk, a.id_terminal, a.id_vendor, upper(b.nama_vendor)
						) a left join (
							select a.tanggal_inven as tanggal_inven, a.id_produk, a.id_terminal, 
							a.id_vendor, upper(b.nama_vendor) as nama_vendor  
							from pro_inventory_depot a 
							left join pro_master_vendor b on a.id_vendor = b.id_master 
							where id_jenis = 1 and id_terminal = '" . $q1 . "' and id_produk = '" . $q4 . "' 
						) b on a.id_produk = b.id_produk and a.id_terminal = b.id_terminal and a.id_vendor = b.id_vendor 
					";
						$row 		= $con->getResult($cek);
						$rowVendor 	= array();
						$hasil 		= "";

						if (count($row) > 0) {
							$rowVendor = $row;
							$hasil = '<div id="wrapper_scrolltabs_tabSet" class="wrapper_scrolltabs">';
							$hasil .= '<ul id="tabSet" class="scroll_tabs_theme_custom1">';
							foreach ($rowVendor as $idxVendor => $dataVendor) {
								$idvendor = $dataVendor['id_vendor'];
								$nmvendor = $dataVendor['nama_vendor'];
								$hasil .= '<li class="' . ($idxVendor == 0 ? 'active tab_selected' : '') . '"><a data-href="#lampiran-nc-' . $idvendor . '">' . $nmvendor . '</a></li>';
							}
							$hasil .= '</ul>';
							$hasil .= '<div class="tab-content">';
							foreach ($rowVendor as $idxVendor => $dataVendor) {
								$idvendor = $dataVendor['id_vendor'];
								$nmvendor = $dataVendor['nama_vendor'];

								$hasil .= '<div class="tab-pane" id="lampiran-nc-' . $idvendor . '" ' . ($idxVendor == 0 ? 'style="display:block;"' : '') . '>';


								$sqlutama01 = "
								select a.id_datanya, a.id_jenis, 'Input' as jenis_penambahan, 
								a.id_produk, concat(c.jenis_produk, ' - ', c.merk_dagang) as ket_produk, 
								a.id_vendor, upper(c2.nama_vendor) as nama_vendor, 
								a.id_terminal, concat(b.nama_terminal, ' ', b.tanki_terminal) as ket_terminal, 
								a.tanggal_inven, 
								NULL as beginningnya, a.in_inven as inputnya, NULL as outputnya, NULL as adjustnya, 
								a.keterangan, a.lastupdate_time 
								from pro_inventory_depot a 
								join pro_master_terminal b on a.id_terminal = b.id_master 
								join pro_master_produk c on a.id_produk = c.id_master 
								join pro_master_vendor c2 on a.id_vendor = c2.id_master 
								join (
									select '" . $dataVendor['id_produk'] . "' as id_produk, '" . $dataVendor['id_terminal'] . "' as id_terminal, 
									'" . $dataVendor['id_vendor'] . "' as id_vendor, cast('" . $dataVendor['tanggal_inven'] . "' as date) as tanggal_inven 
								) d on a.id_terminal = d.id_terminal and a.id_vendor = d.id_vendor and a.tanggal_inven >= d.tanggal_inven   			
								where id_jenis = 21 and a.id_terminal = '" . $q1 . "' and a.id_produk = '" . $q4 . "' and a.id_vendor = '" . $idvendor . "'
									and month(a.tanggal_inven) = '" . $q2 . "' and year(a.tanggal_inven) = '" . $q3 . "'

								UNION ALL 

								select a.id_datanya, a.id_jenis, 'Adjustment' as jenis_penambahan, 
								a.id_produk, concat(c.jenis_produk, ' - ', c.merk_dagang) as ket_produk, 
								a.id_vendor, upper(c2.nama_vendor) as nama_vendor, 
								a.id_terminal, concat(b.nama_terminal, ' ', b.tanki_terminal) as ket_terminal, 
								a.tanggal_inven, 
								NULL as beginningnya, NULL as inputnya, NULL as outputnya, a.adj_inven as adjustnya,   
								a.keterangan, a.lastupdate_time 
								from pro_inventory_depot a 
								join pro_master_terminal b on a.id_terminal = b.id_master 
								join pro_master_produk c on a.id_produk = c.id_master 
								join pro_master_vendor c2 on a.id_vendor = c2.id_master 
								join (
									select '" . $dataVendor['id_produk'] . "' as id_produk, '" . $dataVendor['id_terminal'] . "' as id_terminal, 
									'" . $dataVendor['id_vendor'] . "' as id_vendor, cast('" . $dataVendor['tanggal_inven'] . "' as date) as tanggal_inven 
								) d on a.id_terminal = d.id_terminal and a.id_vendor = d.id_vendor and a.tanggal_inven >= d.tanggal_inven   			
								where id_jenis in (3, 4) and a.id_terminal = '" . $q1 . "' and a.id_produk = '" . $q4 . "' and a.id_vendor = '" . $idvendor . "' 
									and month(a.tanggal_inven) = '" . $q2 . "' and year(a.tanggal_inven) = '" . $q3 . "' 
								order by tanggal_inven, lastupdate_time 
							";
								$resutama01 = $con->getResult($sqlutama01);

								$hasil .= '
							<div class="table-responsive">
								<table class="table table-bordered">
									<thead>
										<tr>
											<th class="text-center" width="100">TANGGAL</th>
											<th class="text-center" width="120">BEGINNING</th>
											<th class="text-center" width="120">INPUT</th>
											<th class="text-center" width="120">OUTPUT</th>
											<th class="text-center" width="120">ADJUSTMENT</th>
											<th class="text-center" width="120">ENDING</th>
											<th class="text-center" width="">KETERANGAN</th>
										</tr>
									</thead>
									<tbody>
							';
								$jumlah01 = 0;
								$jumlah02 = 0;
								$jumlah03 = 0;
								$jumlah04 = 0;
								$jumlah05 = 0;
								$jumlah06 = 0;
								$tempJmlh = 0;
								if (count($resutama01) > 0) {
									foreach ($resutama01 as $idxnya => $datanya) {
										if ($idxnya == 0) {
											$endingnya 	= ($datanya['beginningnya'] + $datanya['inputnya'] + ($datanya['adjustnya'])) - $datanya['outputnya'];
											$tempJmlh 	= $endingnya;
										} else {
											$endingnya 	= ($tempJmlh + $datanya['beginningnya'] + $datanya['inputnya'] + ($datanya['adjustnya'])) - $datanya['outputnya'];
											$tempJmlh 	= $endingnya;
										}
										$jumlah01 = $jumlah01 + $datanya['beginningnya'];
										$jumlah02 = $jumlah02 + $datanya['inputnya'];
										$jumlah03 = $jumlah03 + $datanya['outputnya'];
										$jumlah04 = $jumlah04 + $datanya['adjustnya'];
										$jumlah05 = $jumlah05 + $endingnya;

										$hasil .= '
									<tr>
										<td class="text-center">' . date('d-m-Y', strtotime($datanya['tanggal_inven'])) . '</td>
										<td class="text-right">' . number_format($datanya['beginningnya'], 0) . '</td>
										<td class="text-right">' . number_format($datanya['inputnya'], 0) . '</td>
										<td class="text-right">' . number_format($datanya['outputnya'], 0) . '</td>
										<td class="text-right">' . number_format($datanya['adjustnya'], 0) . '</td>
										<td class="text-right">' . number_format($endingnya, 0) . '</td>
										<td class="text-left">' . nl2br($datanya['keterangan']) . '</td>
									</tr>';
									}
									$hasil .= '</tbody>';
								} else {
									$hasil .= '
								<tr height="40">
									<td class="text-center" colspan="7" style="vertical-align:middle;"><i>Nilai Data Awal Bukan Pada Bulan ' . $arrBln[$q2] . ' Tahun ' . $q3 . '</i></td>
								</tr>';
								}
								$hasil .= '
							<tfoot>
								<tr style="background-color:#f4f4f4;">
									<td class="text-center"><b>TOTAL</b></td>
									<td class="text-right"><b>' . number_format($jumlah01, 0) . '</b></td>
									<td class="text-right"><b>' . number_format($jumlah02, 0) . '</b></td>
									<td class="text-right"><b>' . number_format($jumlah03, 0) . '</b></td>
									<td class="text-right"><b>' . number_format($jumlah04, 0) . '</b></td>
									<td class="text-right"><b>' . number_format($tempJmlh, 0) . '</b></td>
									<td class="text-left">&nbsp;</td>
								</tr>
							</tfoot>';
								$hasil .= '</table></div>';
								$hasil .= '</div>';
							}

							$hasil .= '</div>';
						} else {
							$hasil = '<i>Data Awal untuk terminal dan produk ini belum diset</i>';
						}
						echo $hasil;


						/*$hasil = '<div id="wrapper_scrolltabs_tabSet" class="wrapper_scrolltabs">';
					$hasil .= '<ul id="tabSet" class="scroll_tabs_theme_custom1">';
					foreach($rowProduk as $idxProduk=>$dataProduk){
						$idproduk = $dataProduk['id_master'];
						$nmproduk = strtoupper($dataProduk['jenis_produk'].' - '.$dataProduk['merk_dagang']);
						$hasil .= '<li class="'.($idxProduk == 0 ? 'active tab_selected' : '').'"><a data-href="#lampiran-nc-'.$idproduk.'">'.$nmproduk.'</a></li>';
					}
					$hasil .= '</ul>';				
					$hasil .= '<div class="tab-content">';
	
					foreach($rowProduk as $idxProduk=>$dataProduk){
						$idproduk = $dataProduk['id_master'];
						$nmproduk = strtoupper($dataProduk['jenis_produk'].' - '.$dataProduk['merk_dagang']);
	
						$hasil .= '<div class="tab-pane" id="lampiran-nc-'.$idproduk.'" '.($idxProduk == 0 ? 'style="display:block;"' : '').'>';
						
						$sqlcek01 = "
							select a.id_datanya, a.id_produk, a.id_terminal 
							from pro_inventory_depot a 
							where id_jenis = 1 and id_terminal = '".$q1."' and id_produk = '".$idproduk."' 
						";
						$rescek01 = $con->getResult($sqlcek01);
						if(count($rescek01) > 0){
							$sqlcek01 = "
								select a.id_datanya, a.id_jenis, 'Data Awal' as jenis_penambahan, 
								a.id_produk, concat(c.jenis_produk, ' - ', c.merk_dagang) as ket_produk, 
								a.id_terminal, concat(b.nama_terminal, ' ', b.tanki_terminal) as ket_terminal, 
								a.tanggal_inven, 
								sum(awal_inven) as beginningnya, NULL as inputnya, NULL as outputnya, NULL as adjustnya, NULL as virtualnya,   
								a.keterangan, a.lastupdate_time 
								from pro_inventory_depot a 
								join pro_master_terminal b on a.id_terminal = b.id_master 
								join pro_master_produk c on a.id_produk = c.id_master 
								where id_jenis = 1 and id_terminal = '".$q1."' and id_produk = '".$idproduk."' 
									and month(tanggal_inven) = '".$q2."' and year(tanggal_inven) = '".$q3."' 
								group by a.id_datanya, a.id_produk, a.id_terminal, concat(b.nama_terminal, ' ', b.tanki_terminal), a.tanggal_inven 
							";
							
							$sqlcek02 = "
								select a.tanggal_inven, a.id_produk, a.ket_produk, a.id_terminal, a.ket_terminal, 
								coalesce(a.beginningnya,0) - coalesce(b.out_inven,0) as beginningnya, 
								NULL as inputnya, NULL as outputnya, NULL as adjustnya, NULL as virtualnya, 
								a.keterangan, a.lastupdate_time 
								from (
									select cast('".$q3."-".$q2."-1' as date) as tanggal_inven, 
									a.id_produk, concat(c.jenis_produk, ' - ', c.merk_dagang) as ket_produk, 
									a.id_terminal, concat(b.nama_terminal, ' ', b.tanki_terminal) as ket_terminal, 
									(sum(a.awal_inven) + sum(a.in_inven) + sum(a.adj_inven)) as beginningnya, 
									cast('Otomatisasi Ending Bulan Lalu' as NCHAR) as keterangan, 
									cast('".$q3."-".$q2."-1 00:00:00' as datetime) as lastupdate_time
									from pro_inventory_depot a 
									join pro_master_terminal b on a.id_terminal = b.id_master 
									join pro_master_produk c on a.id_produk = c.id_master 
									join (
										select distinct id_produk, id_terminal, tanggal_inven 
										from pro_inventory_depot 
										where id_jenis = 1 and id_produk = '".$idproduk."' and tanggal_inven <= '".$q3."-".$q2."-1'
									) d on a.id_terminal = d.id_terminal and a.tanggal_inven >= d.tanggal_inven   			
									where 1=1 and a.id_terminal = '".$q1."' and a.id_produk = '".$idproduk."' and a.tanggal_inven < '".$q3."-".$q2."-1' 
									group by a.id_terminal, a.id_produk 
								) a 
								left join (
									select cast('".$q3."-".$q2."-1' as date) as tanggal_inven, 
									h.produk_poc as id_produk, o.id_terminal, 
									sum(case when a.tanggal_loaded < '".$q3."-".$q2."-1' then b.volume_po end) as out_inven
									from pro_po_ds_detail a 
									join pro_po_ds o on a.id_ds = o.id_ds 
									join pro_po_detail b on a.id_pod = b.id_pod 
									join pro_po m on a.id_po = m.id_po 
									join pro_pr_detail c on a.id_prd = c.id_prd 
									join pro_po_customer_plan d on a.id_plan = d.id_plan 
									join pro_po_customer h on d.id_poc = h.id_poc 
									join (
										select distinct id_produk, id_terminal, tanggal_inven 
										from pro_inventory_depot 
										where id_jenis = 1 and id_produk = '".$idproduk."' and tanggal_inven <= '".$q3."-".$q2."-1'
									) d on o.id_terminal = d.id_terminal and a.tanggal_loaded >= d.tanggal_inven 			
									where 1=1 and a.is_loaded = 1 
										and h.produk_poc = '".$idproduk."' and a.tanggal_loaded < '".$q3."-".$q2."-1'
										and o.id_terminal = '".$q1."' 
									group by o.id_terminal, h.produk_poc 
								) b on a.id_produk = b.id_produk and a.id_terminal = b.id_terminal 
							";
							$rescek01 = $con->getResult($sqlcek01);
							$joinnya1 = (count($rescek01) > 0 ? 'left join ' : 'right join ');
						
							$sqlutama01 = "
								select 
								coalesce(a.id_datanya, 'generated') as id_datanya, 
								coalesce(a.id_jenis, 1) as id_jenis, 
								'Data Awal' as jenis_penambahan, 
								coalesce(a.id_produk, b.id_produk) as id_produk,
								coalesce(a.ket_produk, b.ket_produk) as ket_produk,
								coalesce(a.id_terminal, b.id_terminal) as id_terminal,
								coalesce(a.ket_terminal, b.ket_terminal) as ket_terminal,
								coalesce(a.tanggal_inven, b.tanggal_inven) as tanggal_inven,
								coalesce(a.beginningnya, b.beginningnya) as beginningnya,
								coalesce(a.inputnya, b.inputnya) as inputnya,
								coalesce(a.outputnya, b.outputnya) as outputnya,
								coalesce(a.adjustnya, b.adjustnya) as adjustnya,
								coalesce(a.virtualnya, b.virtualnya) as virtualnya,
								coalesce(a.keterangan, b.keterangan) as keterangan,
								coalesce(a.lastupdate_time, b.lastupdate_time) as lastupdate_time 
								from (
									".$sqlcek01."
								) a 
								".$joinnya1." (
									".$sqlcek02." 
								) b on a.id_terminal = b.id_terminal and a.id_produk = b.id_produk
								
								UNION ALL
								
								select a.id_datanya, a.id_jenis, 'Input' as jenis_penambahan, 
								a.id_produk, concat(c.jenis_produk, ' - ', c.merk_dagang) as ket_produk, 
								a.id_terminal, concat(b.nama_terminal, ' ', b.tanki_terminal) as ket_terminal, 
								a.tanggal_inven, 
								NULL as beginningnya, a.in_inven as inputnya, NULL as outputnya, NULL as adjustnya, NULL as virtualnya,   
								a.keterangan, a.lastupdate_time 
								from pro_inventory_depot a 
								join pro_master_terminal b on a.id_terminal = b.id_master 
								join pro_master_produk c on a.id_produk = c.id_master 
								join (
									select distinct id_produk, id_terminal, tanggal_inven 
									from pro_inventory_depot 
									where id_jenis = 1 and id_produk = '".$idproduk."' and tanggal_inven <= '".$q3."-".$q2."-1'
								) d on a.id_terminal = d.id_terminal and a.tanggal_inven >= d.tanggal_inven   			
								where id_jenis = 21 and a.id_terminal = '".$q1."' and a.id_produk = '".$idproduk."'
									and month(a.tanggal_inven) = '".$q2."' and year(a.tanggal_inven) = '".$q3."' 

								UNION ALL 

								select '888888' as id_datanya, 31 as id_jenis, 'Output' as jenis_penambahan, 
								h.produk_poc as id_produk, concat(h2.jenis_produk, ' - ', h2.merk_dagang) as ket_produk, 
								o.id_terminal, concat(o2.nama_terminal, ' ', o2.tanki_terminal) as ket_terminal, 
								a.tanggal_loaded as tanggal_inven, 
								NULL as beginningnya, NULL as inputnya, b.volume_po as outputnya, NULL as adjustnya, NULL as virtualnya,   
								'' as keterangan, a.tanggal_loaded as lastupdate_time 
								from pro_po_ds_detail a 
								join pro_po_ds o on a.id_ds = o.id_ds 
								join pro_po_detail b on a.id_pod = b.id_pod 
								join pro_po m on a.id_po = m.id_po 
								join pro_pr_detail c on a.id_prd = c.id_prd 
								join pro_po_customer_plan d on a.id_plan = d.id_plan 
								join pro_po_customer h on d.id_poc = h.id_poc 
								join pro_master_produk h2 on h.produk_poc = h2.id_master 
								join pro_master_terminal o2 on o.id_terminal = o2.id_master
								join (
									select distinct id_produk, id_terminal 
									from pro_inventory_depot 
									where id_jenis = 1 and id_produk = '".$idproduk."' and tanggal_inven <= '".$q3."-".$q2."-1' 
								) d on o.id_terminal = d.id_terminal 			
								where 1=1 and a.is_loaded = 1 
									and h.produk_poc = '".$idproduk."' 
									and o.id_terminal = '".$q1."'
									and month(a.tanggal_loaded) = '".$q2."' and year(a.tanggal_loaded) = '".$q3."' 

								UNION ALL 

								select a.id_datanya, a.id_jenis, 'Adjustment' as jenis_penambahan, 
								a.id_produk, concat(c.jenis_produk, ' - ', c.merk_dagang) as ket_produk, 
								a.id_terminal, concat(b.nama_terminal, ' ', b.tanki_terminal) as ket_terminal, 
								a.tanggal_inven, 
								NULL as beginningnya, NULL as inputnya, NULL as outputnya, a.adj_inven as adjustnya, NULL as virtualnya,   
								a.keterangan, a.lastupdate_time 
								from pro_inventory_depot a 
								join pro_master_terminal b on a.id_terminal = b.id_master 
								join pro_master_produk c on a.id_produk = c.id_master 
								join (
									select distinct id_produk, id_terminal, tanggal_inven 
									from pro_inventory_depot 
									where id_jenis = 1 and id_produk = '".$idproduk."' and tanggal_inven <= '".$q3."-".$q2."-1'
								) d on a.id_terminal = d.id_terminal and a.tanggal_inven >= d.tanggal_inven   			
								where id_jenis in (3, 4) and a.id_terminal = '".$q1."' and a.id_produk = '".$idproduk."'
									and month(a.tanggal_inven) = '".$q2."' and year(a.tanggal_inven) = '".$q3."' 
								order by tanggal_inven, lastupdate_time 
							";
							$resutama01 = $con->getResult($sqlutama01);
							$hasil .= '
							<div class="table-responsive">
								<table class="table table-bordered">
									<thead>
										<tr>
											<th class="text-center" width="100">TANGGAL</th>
											<th class="text-center" width="120">BEGINNING</th>
											<th class="text-center" width="120">INPUT</th>
											<th class="text-center" width="120">OUTPUT</th>
											<th class="text-center" width="120">ADJUSTMENT</th>
											<th class="text-center" width="120">ENDING</th>
											<th class="text-center" width="">KETERANGAN</th>
										</tr>
									</thead>
									<tbody>
							';
							$jumlah01 = 0; $jumlah02 = 0; $jumlah03 = 0; 
							$jumlah04 = 0; $jumlah05 = 0; $jumlah06 = 0;
							$tempJmlh = 0; 
							if(count($resutama01) > 0){
								foreach($resutama01 as $idxnya=>$datanya){
									if($idxnya == 0){
										$endingnya 	= ($datanya['beginningnya'] + $datanya['inputnya'] + ($datanya['adjustnya'])) - $datanya['outputnya'];
										$tempJmlh 	= $endingnya;
									} else{
										$endingnya 	= ($tempJmlh + $datanya['beginningnya'] + $datanya['inputnya'] + ($datanya['adjustnya'])) - $datanya['outputnya'];
										$tempJmlh 	= $endingnya;
									}
									$jumlah01 = $jumlah01 + $datanya['beginningnya'];
									$jumlah02 = $jumlah02 + $datanya['inputnya'];
									$jumlah03 = $jumlah03 + $datanya['outputnya'];
									$jumlah04 = $jumlah04 + $datanya['adjustnya'];
									$jumlah05 = $jumlah05 + $endingnya;
	
									$hasil .= '
									<tr>
										<td class="text-center">'.date('d-m-Y', strtotime($datanya['tanggal_inven'])).'</td>
										<td class="text-right">'.number_format($datanya['beginningnya'],0).'</td>
										<td class="text-right">'.number_format($datanya['inputnya'],0).'</td>
										<td class="text-right">'.number_format($datanya['outputnya'],0).'</td>
										<td class="text-right">'.number_format($datanya['adjustnya'],0).'</td>
										<td class="text-right">'.number_format($endingnya,0).'</td>
										<td class="text-left">'.nl2br($datanya['keterangan']).'</td>
									</tr>';
								}
								$hasil .= '</tbody>';
							} else{
								$hasil .= '
								<tr height="40">
									<td class="text-center" colspan="7" style="vertical-align:middle;"><i>Nilai Data Awal Bukan Pada Bulan '.$arrBln[$q2].' Tahun '.$q3.'</i></td>
								</tr>';
							}
							$hasil .= '
							<tfoot>
								<tr style="background-color:#f4f4f4;">
									<td class="text-center"><b>TOTAL</b></td>
									<td class="text-right"><b>'.number_format($jumlah01,0).'</b></td>
									<td class="text-right"><b>'.number_format($jumlah02,0).'</b></td>
									<td class="text-right"><b>'.number_format($jumlah03,0).'</b></td>
									<td class="text-right"><b>'.number_format($jumlah04,0).'</b></td>
									<td class="text-right"><b>'.number_format($tempJmlh,0).'</b></td>
									<td class="text-left">&nbsp;</td>
								</tr>
							</tfoot>';
							$hasil .= '</table></div>';
						} else{
							$hasil .= '<i>Data Awal untuk terminal dan produk ini belum diset</i>';
						}
						
						$hasil .= '</div>';
					}
	
					$hasil .= '</div>';
					echo $hasil;*/
					}
					?>

				</div>

				<?php $con->close(); ?>
			</section>
			<?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
		</aside>
	</div>

	<div class="modal fade" id="loading_modal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-blue">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Loading Data ...</h4>
				</div>
				<div class="modal-body text-center modal-loading"></div>
			</div>
		</div>
	</div>

	<style type="text/css">
		.table>tfoot>tr>td {
			border: 1px solid #ddd;
			padding: 5px;
			font-size: 11px;
			font-family: arial;
			vertical-align: middle;
		}

		.swal2-modal .swal2-styled {
			padding: 5px;
			min-width: 130px;
			font-family: arial;
			font-size: 14px;
			margin: 10px;
		}

		.tab-pane {
			padding: 15px;
		}
	</style>
	<script>
		$(document).ready(function() {
			$("#html-tabelnya").find("#tabSet").scrollTabs({
				click_callback: function(e) {
					let elemLi = $(this);
					let wrapper = elemLi.parents(".wrapper_scrolltabs").first();
					let nilai1 = elemLi.children().data("href");
					if (nilai1.substr(0, 1) == '#') {
						let element = wrapper.children(".tab-content");
						element.children().fadeOut().promise().done(function() {
							element.find(nilai1).fadeIn();
						});
					} else {
						window.location.href = nilai1;
					}
				}
			});

			$("#btnSearch").on("click", function() {
				if ($("#q1").val() == "" || $("#q2").val() == "" || $("#q3").val() == "" || $("#q4").val() == "") {
					swal.fire({
						allowOutsideClick: false,
						icon: "warning",
						width: '350px',
						html: '<p style="font-size:14px; font-family:arial;">Nama Terminal, Produk, Bulan dan Tahun<br />harus dipilih</p>'
					});
				} else {
					$("#loading_modal").modal();
					$.post("./action/seturldata.php", {
						q1: $("#q1").val(),
						q2: $("#q2").val(),
						q3: $("#q3").val(),
						q4: $("#q4").val()
					}, function(data) {
						window.location.href = base_url + '/web/vendor-inven.php?' + data;
					});
				}
			});
		});
	</script>
</body>

</html>
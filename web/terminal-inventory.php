<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$draw   = isset($_POST["element"]) ? htmlspecialchars($_POST["element"], ENT_QUOTES) : 0;
$start  = isset($_POST["start"]) ? htmlspecialchars($_POST["start"], ENT_QUOTES) : 0;
$length = isset($_POST['length']) ? htmlspecialchars($_POST["length"], ENT_QUOTES) : 10;
$enk  	= decode($_SERVER['REQUEST_URI']);
$con 	= new Connection();
$flash	= new FlashAlerts;
$arrBln = array(1 => "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
$sesRole = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$sesGrup = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);
$sesCbng = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

$q1 = (isset($_POST["q1"])) ? htmlspecialchars($_POST["q1"], ENT_QUOTES) : (isset($enk['q1']) ? htmlspecialchars($enk['q1'], ENT_QUOTES) : null);
$q2 = (isset($_POST["q2"])) ? htmlspecialchars($_POST["q2"], ENT_QUOTES) : (isset($enk['q2']) ? htmlspecialchars($enk['q2'], ENT_QUOTES) : null);
$q3 = (isset($_POST["q3"])) ? htmlspecialchars($_POST["q3"], ENT_QUOTES) : (isset($enk['q3']) ? htmlspecialchars($enk['q3'], ENT_QUOTES) : null);
$q4 = (isset($_POST["q4"])) ? htmlspecialchars($_POST["q4"], ENT_QUOTES) : (isset($enk['q4']) ? htmlspecialchars($enk['q4'], ENT_QUOTES) : null);
$q5 = (isset($_POST["q5"])) ? htmlspecialchars($_POST["q5"], ENT_QUOTES) : (isset($enk['q5']) ? htmlspecialchars($enk['q5'], ENT_QUOTES) : null);
$q6 = (isset($_POST["q6"])) ? htmlspecialchars($_POST["q6"], ENT_QUOTES) : (isset($enk['q6']) ? htmlspecialchars($enk['q6'], ENT_QUOTES) : null);
$q7 = (isset($_POST["q7"])) ? htmlspecialchars($_POST["q7"], ENT_QUOTES) : (isset($enk['q7']) ? htmlspecialchars($enk['q7'], ENT_QUOTES) : null);
$q8 = (isset($_POST["q8"])) ? htmlspecialchars($_POST["q8"], ENT_QUOTES) : (isset($enk['q8']) ? htmlspecialchars($enk['q8'], ENT_QUOTES) : null);
$q9 = (isset($_POST["q9"])) ? htmlspecialchars($_POST["q9"], ENT_QUOTES) : (isset($enk['q9']) ? htmlspecialchars($enk['q9'], ENT_QUOTES) : null);
$ex = isset($_POST["btnSearch"]) ? htmlspecialchars($_POST["btnSearch"], ENT_QUOTES) : null;

$cek = "select * from pro_master_produk where is_active = 1 order by id_master";
$row = $con->getResult($cek);
$prd = (isset($enk['prd'])) ? htmlspecialchars($enk["prd"], ENT_QUOTES) : $row[0]['id_master'];
$dpt = (paramDecrypt($_SESSION["sinori" . SESSIONID]["id_role"]) == 13) ? paramDecrypt($_SESSION["sinori" . SESSIONID]["terminal"]) : $q9;
$tke = paramEncrypt('q1=' . $q1 . '&q2=' . $q2 . '&q3=' . $q3 . '&q4=' . $q4 . '&q5=' . $q5 . '&q6=' . $q6 . '&q7=' . $q7 . '&q8=' . $q8 . '&q9=' . $dpt . '&q10=' . $prd);
$lke = BASE_URL_CLIENT . '/terminal-inventory-exp.php?' . $tke;

$resq = [];
$res1q = [];

if ($q1 == "") {
	$file  	= BASE_SELF;
	$limit 	= 31;
	$p		= new paging;

	$sql = "select a.*, b.out_pagi, b.out_malam, b.out_cancel from pro_master_inventory a 
				left join pro_master_inventory_out b on a.id_terminal = b.id_terminal and a.tanggal_inv = b.tanggal_inv and a.id_produk = b.id_produk 
				where a.id_produk = " . $prd . "";
	if ($dpt) {
		$sql .= " and a.id_terminal = '" . $dpt . "'";
	}
	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record / $limit);
	// $page		= ($enk[page] > $tot_page)?$enk[page]-1:$enk[page]; 
	$page       = ($start > $tot_page) ? $start - 1 : $start;
	$position 	= $p->findPosition($limit, $tot_record, $page);
	$param_ref	= "&prd=" . $prd . "&q9=" . $q9;
	$sql .= " order by a.tanggal_inv desc limit " . $position . ", " . $limit;
	$res = $con->getResult($sql);
	$resq = $res;
} else if ($q1 == "1") {
	$tgl = ($q3 == 12) ? ($q4 + 1) . "-01-01" : $q4 . "-" . ($q3 + 1) . "-01";
	$sql = "select a.*, b.out_pagi, b.out_malam, b.out_cancel from pro_master_inventory a 
				left join pro_master_inventory_out b on a.id_terminal = b.id_terminal and a.tanggal_inv = b.tanggal_inv and a.id_produk = b.id_produk  
				where a.id_produk = " . $prd . " and a.id_terminal = '" . $dpt . "' and month(a.tanggal_inv) = '" . $q3 . "' and year(a.tanggal_inv) = '" . $q4 . "'
				UNION
				select id_master, id_terminal, id_produk, tanggal_inv, awal_jam, awal_level1, awal_level2, awal_volume_tabel, awal_shrink, awal_nett, awal_temp, awal_density1, 		
				awal_density2, awal_vcf, book_stok, 0 as masuk_ship, 0 as masuk_truck, 0 as masuk_slop, 0 as keluar_slop, 0 as tank_pipe, 0 as gain_loss, created_time, created_ip, 
				created_by, lastupdate_time, lastupdate_ip, lastupdate_by , 0 as out_pagi, 0 as out_malam, 0 as out_cancel from pro_master_inventory
				where id_produk = " . $prd . " and id_terminal = '" . $dpt . "' and tanggal_inv = '" . $tgl . "' order by tanggal_inv";
	$res = $con->getResult($sql);
	$tot_record = count($res);
	$position 	= 0;
	$resq = $res;

	$que = "
			select * from 
			(
				select 1 as idnya, book_stok as end_book_stok_temp, awal_nett as end_actual_temp, book_stok * awal_vcf as end_book_stok_temp_gov, 
				awal_nett * awal_vcf as end_actual_temp_gov from pro_master_inventory where id_produk = " . $prd . " and id_terminal = '" . $dpt . "' 
				and tanggal_inv in(select max(tanggal_inv) from pro_master_inventory where id_produk = " . $prd . " and id_terminal = '" . $dpt . "' 
				and month(tanggal_inv) = '" . $q3 . "' and year(tanggal_inv) = '" . $q4 . "')
			) a left join 
			(
				select 1 as idnya, tank_pipe as end_pipe, tank_pipe * awal_vcf as end_pipe_gov from pro_master_inventory where id_produk = " . $prd . " and id_terminal = '" . $dpt . "' 
				and tanggal_inv in (select max(tanggal_inv) from pro_master_inventory where id_produk = " . $prd . " and id_terminal = '" . $dpt . "' 
				and month(tanggal_inv) = '" . $q3 . "' and year(tanggal_inv) = '" . $q4 . "')
			) b on a.idnya = b.idnya left join 
			(
				select 1 as idnya, awal_nett as opening_stok, awal_nett * awal_vcf as opening_stok_gov from pro_master_inventory where id_produk = " . $prd . " 
				and id_terminal = '" . $dpt . "' and tanggal_inv = '" . $q4 . "-" . $q3 . "-01'
			) c on a.idnya = c.idnya left join 
			(
				select 1 as idnya, book_stok as end_book_stok, awal_nett as end_actual, book_stok * awal_vcf as end_book_stok_gov, awal_nett * awal_vcf as end_actual_gov 
				from pro_master_inventory where id_produk = " . $prd . " and id_terminal = '" . $dpt . "' and tanggal_inv = '" . $tgl . "'
			) d on a.idnya = d.idnya";
	$qus = $con->getRecord($que);
} else if ($q1 == "2") {
	$tgl1 = $q6 . "-" . $q5 . "-01";
	$tgl2 = $q8 . "-" . $q7 . "-31";
	$tgl3 = ($q7 == 12) ? ($q8 + 1) . "-01-01" : $q8 . "-" . ($q7 + 1) . "-01";

	$sql1 = "
			select extract(year_month from a.tanggal_inv) as bulan_tahun, sum(a.masuk_ship) as in_ship, sum(a.masuk_truck) as in_truck, sum(a.masuk_slop) as in_slop, 
			sum(b.out_pagi) + sum(b.out_malam) as customer, sum(a.keluar_slop) as out_slop, 
			sum(a.masuk_ship * a.awal_vcf) as in_ship_gsv, sum(a.masuk_truck * a.awal_vcf) as in_truck_gsv, sum(a.masuk_slop * a.awal_vcf) as in_slop_gsv, 
			sum(b.out_pagi * a.awal_vcf) + sum(b.out_malam * a.awal_vcf) as customer_gsv, sum(a.keluar_slop * a.awal_vcf) as out_slop_gsv 
			from pro_master_inventory a left join pro_master_inventory_out b on a.tanggal_inv = b.tanggal_inv and a.id_terminal = b.id_terminal and a.id_produk = b.id_produk 
			where a.id_produk = " . $prd . " and a.id_terminal = '" . $dpt . "' and a.tanggal_inv between  '" . $tgl1 . "' and '" . $tgl2 . "' 
			group by extract(year_month from tanggal_inv) order by 1";
	$res1 = $con->getResult($sql1);
	$tot_record = count($res1);
	$position 	= 0;
	$res1q = $res1;

	$sql2 = "
			select * from 
			(
				select 1 as idnya, book_stok as end_book_stok_temp, awal_nett as end_actual_temp, book_stok * awal_vcf as end_book_stok_temp_gov, 
				awal_nett * awal_vcf as end_actual_temp_gov from pro_master_inventory where id_produk = " . $prd . " and id_terminal = '" . $dpt . "' 
				and tanggal_inv in(select max(tanggal_inv) from pro_master_inventory 
				where id_produk = " . $prd . " and id_terminal = '" . $dpt . "' and tanggal_inv between  '" . $tgl1 . "' and '" . $tgl2 . "')
			) a left join 
			(
				select 1 as idnya, tank_pipe as end_pipe, tank_pipe * awal_vcf as end_pipe_gov from pro_master_inventory where id_produk = " . $prd . " and id_terminal = '" . $dpt . "' 
				and tanggal_inv in (select max(tanggal_inv) from pro_master_inventory where id_produk = " . $prd . " and id_terminal = '" . $dpt . "' 
				and tanggal_inv between  '" . $tgl1 . "' and '" . $tgl2 . "')
			) b on a.idnya = b.idnya left join 
			(
				select 1 as idnya, book_stok as end_book_stok, awal_nett as end_actual, book_stok * awal_vcf as end_book_stok_gov, awal_nett * awal_vcf as end_actual_gov 
				from pro_master_inventory where id_produk = " . $prd . " and id_terminal = '" . $dpt . "' and tanggal_inv = '" . $tgl3 . "'
			) c on a.idnya = c.idnya";
	$res2 = $con->getRecord($sql2);
}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("formatNumber", "jqueryUI", "myGrid"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory . "/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
		<aside class="right-side">
			<section class="content-header">
				<h1>Inventory</h1>
			</section>
			<section class="content">

				<?php $flash->display(); ?>
				<ul class="nav nav-tabs" role="tablist">
					<?php foreach ($row as $nil) { ?>
						<li role="presentation" class="<?php echo ($nil['id_master'] == $prd) ? 'active' : ''; ?>">
							<a href="<?php echo BASE_URL_CLIENT . "/terminal-inventory.php?" . paramEncrypt("prd=" . $nil['id_master'] . "&q9=" . $q9); ?>" role="tab" data-toggle="tablink">
								<?php echo $nil['jenis_produk'] . " - " . $nil['merk_dagang']; ?></a>
						</li>
					<?php } ?>
				</ul>

				<div class="row">
					<div class="col-sm-12">
						<div class="box box-info">
							<div class="box-header with-border">
								<div class="row">
									<div class="col-sm-6">
										<div style="font-size:18px; padding:4px 0px;"><b>PENCARIAN</b></div>
									</div>
									<div class="col-sm-6">
										<?php if ($sesRole == 5 || $sesRole == 13) { ?>
											<div class="text-right">
												<a href="<?php echo BASE_URL_CLIENT . '/terminal-inventory-add.php?' . paramEncrypt("prd=" . $prd); ?>" class="btn btn-primary">
													<i class="fa fa-plus jarak-kanan"></i>Add Data</a>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
							<div class="box-body">
								<form name="sFrm" id="sFrm" method="post" class="form-validasi" action="<?php echo BASE_URL_CLIENT . "/terminal-inventory.php?" . paramEncrypt("prd=" . $prd); ?>">
									<div class="table-responsive">
										<table border="0" cellpadding="0" cellspacing="0" class="table no-border col-sm-top" id="table-pencarian">
											<tr style="height:35px;">
												<td>Berdasarkan</td>
												<td class="text-center">:</td>
												<td<?php echo (paramDecrypt($_SESSION["sinori" . SESSIONID]["id_role"]) == 13) ? ' colspan="7"' : ''; ?>>
													<select name="q1" id="q1" style="width:120px;">
														<option></option>
														<option value="1" <?php echo ($q1 == 1 ? 'selected' : ''); ?>>Bulan</option>
														<option value="2" <?php echo ($q1 == 2 ? 'selected' : ''); ?>>Periodik</option>
													</select>
													</td>
													<?php if (paramDecrypt($_SESSION["sinori" . SESSIONID]["id_role"]) != 13) { ?>
														<td class="text-right">Terminal</td>
														<td class="text-center">:</td>
														<td colspan="4">
															<select name="q9" id="q9" style="width:230px;">
																<option></option>
																<?php $con->fill_select("id_master", "concat(nama_terminal,' ',tanki_terminal,', ',lokasi_terminal)", "pro_master_terminal", $q9, "where is_active=1", "id_master", false); ?>
															</select>
														</td>
													<?php } ?>
											</tr>
											<tr style="height:35px;">
												<td width="100">Bulan</td>
												<td width="20" class="text-center">:</td>
												<td width="130">
													<select name="q3" id="q3" class="validate[required]" style="width:120px;" <?php echo ($q1 != 1) ? 'disabled' : ''; ?>>
														<option></option>
														<?php
														foreach ($arrBln as $i3 => $t3) {
															$selected = ($q3 == $i3 ? 'selected' : '');
															echo '<option value="' . $i3 . '" ' . $selected . '>' . $t3 . '</option>';
														}
														?>
													</select>
												</td>
												<td width="80" class="text-right">Tahun</td>
												<td width="40" class="text-center">:</td>
												<td width="140">
													<select name="q4" id="q4" class="validate[required]" style="width:80px;" <?php echo ($q1 != 1) ? 'disabled' : ''; ?>>
														<option></option>
														<?php
														for ($i = date('Y'); $i > 2014; $i--) {
															$selected = ($q4 == $i ? 'selected' : '');
															echo '<option ' . $selected . '>' . $i . '</option>';
														}
														?>
													</select>
												</td>
												<td width="70" class="text-right">&nbsp;</td>
												<td width="20" class="text-center">&nbsp;</td>
												<td>&nbsp;</td>
											</tr>
											<tr style="height:35px;">
												<td>Periodik</td>
												<td class="text-center">:</td>
												<td colspan="7">
													<select name="q5" id="q5" class="validate[required]" style="width:120px;" <?php echo ($q1 != 2) ? 'disabled' : ''; ?>>
														<option></option>
														<?php
														foreach ($arrBln as $i5 => $t5) {
															$selected = ($q5 == $i5 ? 'selected' : '');
															echo '<option value="' . $i5 . '" ' . $selected . '>' . $t5 . '</option>';
														}
														?>
													</select>
													<select name="q6" id="q6" class="validate[required]" style="width:80px;" <?php echo ($q1 != 2) ? 'disabled' : ''; ?>>
														<option></option>
														<?php
														for ($i = date('Y'); $i > 2014; $i--) {
															$selected = ($q6 == $i ? 'selected' : '');
															echo '<option ' . $selected . '>' . $i . '</option>';
														}
														?>
													</select>
													<span class="marginX">S/D</span>
													<select name="q7" id="q7" class="validate[required]" style="width:120px;" <?php echo ($q1 != 2) ? 'disabled' : ''; ?>>
														<option></option>
														<?php
														foreach ($arrBln as $i7 => $t7) {
															$selected = ($q7 == $i7 ? 'selected' : '');
															echo '<option value="' . $i7 . '" ' . $selected . '>' . $t7 . '</option>';
														}
														?>
													</select>
													<select name="q8" id="q8" class="validate[required]" style="width:80px;" <?php echo ($q1 != 2) ? 'disabled' : ''; ?>>
														<option></option>
														<?php
														for ($i = date('Y'); $i > 2014; $i--) {
															$selected = ($q8 == $i ? 'selected' : '');
															echo '<option ' . $selected . '>' . $i . '</option>';
														}
														?>
													</select>
												</td>
											</tr>
											<tr style="height:35px;">
												<td colspan="9">
													<button type="submit" class="btn btn-info btn-sm jarak-kanan" name="btnSearch" id="btnSearch" value="1">
														<i class="fa fa-search jarak-kanan"></i>Search</button>
													<?php if ($dpt && $q1 && $ex && $prd) { ?>
														<a class="btn btn-success btn-sm" target="_blank" href="<?php echo $lke; ?>" id="linkexp">
															<i class="fa fa-random jarak-kanan"></i>Export</a>
													<?php } ?>
												</td>
											</tr>
										</table>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<?php if ($q1 != 2) { ?>
					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" class=""><a href="#data-awal" aria-controls="data-awal" role="tab" data-toggle="tab">Data Awal</a></li>
						<li role="presentation" class="active"><a href="#data-gov" aria-controls="data-gov" role="tab" data-toggle="tab">GOV</a></li>
						<li role="presentation" class=""><a href="#data-gsv" aria-controls="data-gsv" role="tab" data-toggle="tab">GSV</a></li>
					</ul>

					<div class="tab-content">
						<div role="tabpanel" class="tab-pane" id="data-awal">
							<p class="text-right">Unit: Liter</p>
							<div class="row">
								<div class="col-sm-12">
									<div class="box box-info">
										<div class="box-body table-responsive">
											<table class="table table-hover no-border col-sm-top" id="table-awal">
												<thead>
													<tr>
														<th class="text-center" width="10%" rowspan="3">TANGGAL</th>
														<th class="text-center" colspan="10">TANK SOUNDING (Pengukuran Tangki)</th>
													</tr>
													<tr>
														<th class="text-center" width="8%" rowspan="2">TIME</th>
														<th class="text-center" colspan="2">LEVEL (MM)</th>
														<th class="text-center" width="10%" rowspan="2">VOLUME TABEL</th>
														<th class="text-center" width="10%" rowspan="2">SHRINKAGE CORRECTION</th>
														<th class="text-center" width="10%" rowspan="2">NETT OBSERVED </th>
														<th class="text-center" width="8%" rowspan="2">TEMP</th>
														<th class="text-center" width="8%" rowspan="2">DENSITY <i>(Oberved)</i></th>
														<th class="text-center" width="8%" rowspan="2">DENSITY <i>(@15<sup>o</sup>C)</i></th>
														<th class="text-center" width="8%" rowspan="2">VCF</th>
													</tr>
													<tr>
														<th class="text-center" width="10%">&nbsp;</th>
														<th class="text-center" width="10%">Datum Plate</th>
													</tr>
												</thead>
												<tbody>
													<?php
													if (count($res) == 0) {
														echo '<tr><td colspan="11" class="text-center">Data tidak ditemukan...</td></tr>';
													} else {
														$nom = $position;
														foreach ($resq as $data1) {
															$awal_level1 = isset($data1['awal_level1']) ? $data1['awal_level1'] : 0;
															$awal_level2 = isset($data1['awal_level2']) ? $data1['awal_level2'] : 0;
															$id_master = isset($data1['id_master']) ? $data1['id_master'] : 0;
															$tanggal_inv = isset($data1['tanggal_inv']) ? $data1['tanggal_inv'] : 0;
															$awal_jam = isset($data1['awal_jam']) ? $data1['awal_jam'] : '-';
															$awal_volume_tabel = isset($data1['awal_volume_tabel']) ? $data1['awal_volume_tabel'] : 0;
															$awal_shrink = isset($data1['awal_shrink']) ? $data1['awal_shrink'] : 0;
															$awal_nett = isset($data1['awal_nett']) ? $data1['awal_nett'] : 0;
															$awal_temp = isset($data1['awal_temp']) ? $data1['awal_temp'] : 0;
															$awal_density1 = isset($data1['awal_density1']) ? $data1['awal_density1'] : 0;
															$awal_density2 = isset($data1['awal_density2']) ? $data1['awal_density2'] : 0;
															$awal_vcf = isset($data1['awal_vcf']) ? $data1['awal_vcf'] : 0;

															$nom++;
															$level2 = ($awal_level1) ? $awal_level1 + $awal_level2 : 0;
															$link	= BASE_URL_CLIENT . "/terminal-inventory-add.php?" . ($id_master ? paramEncrypt("idr=" . $id_master . "&prd=" . $prd) : '');
															$class	= ($sesRole == 5 || $sesRole == 13 ? 'clickable-row' : 'non-clickable-row');
													?>
															<tr class="<?php echo $class; ?>" data-href="<?php echo $link; ?>">
																<td class="text-center"><?php echo $tanggal_inv ? date("d/m/Y", strtotime($tanggal_inv)) : '-'; ?></td>
																<td class="text-center"><?php echo $awal_jam; ?></td>
																<td class="text-right"><?php echo number_format($awal_level1); ?></td>
																<td class="text-right"><?php echo number_format($level2); ?></td>
																<td class="text-right"><?php echo number_format($awal_volume_tabel); ?></td>
																<td class="text-right"><?php echo $awal_shrink; ?></td>
																<td class="text-right"><?php echo number_format($awal_nett); ?></td>
																<td class="text-right"><?php echo $awal_temp; ?></td>
																<td class="text-right"><?php echo $awal_density1; ?></td>
																<td class="text-right"><?php echo $awal_density2; ?></td>
																<td class="text-right"><?php echo $awal_vcf; ?></td>
															</tr>
													<?php }
													} ?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div role="tabpanel" class="tab-pane active" id="data-gov">
							<p class="text-right">Unit: Liter</p>
							<div class="row">
								<div class="col-sm-12">
									<div class="box box-info">
										<div class="box-body table-responsive">
											<table class="table table-hover no-border col-sm-top" id="table-gov">
												<thead>
													<tr>
														<th width="6%" rowspan="2" class="text-center">TANGGAL</th>
														<th width="8%" rowspan="2" class="text-center">BOOK STOCK</th>
														<th width="8%" rowspan="2" class="text-center">OPENING/ CLOSING STOCK</th>
														<th colspan="3" class="text-center">IN<i></i></th>
														<th colspan="3" class="text-center">OUT</th>
														<th width="7%" rowspan="2" class="text-center">CANCEL</th>
														<th width="8%" rowspan="2" class="text-center">CLOSING STOCK</th>
														<th width="6%" rowspan="2" class="text-center">GAIN/ LOSS</th>
														<th width="7%" rowspan="2" class="text-center">TANK PIPE</th>
														<th width="8%" rowspan="2" class="text-center">TOTAL</th>
													</tr>
													<tr>
														<th class="text-center" width="7%">Ship</th>
														<th class="text-center" width="7%">Truck</th>
														<th class="text-center" width="7%">Slop</th>
														<th class="text-center" width="7%">07:00 - 24:00 (H)</th>
														<th class="text-center" width="7%">00:00 - 07:00 (H+1)</th>
														<th class="text-center" width="7%">Slop</th>
													</tr>
												</thead>
												<tbody>
													<?php
													if (count($res) == 0) {
														echo '<tr><td colspan="14" class="text-center">Data tidak ditemukan...</td></tr>';
													} else {
														$nom = $position;
														$in1 = 0;
														$in2 = 0;
														$in3 = 0;
														$ou1 = 0;
														$ou2 = 0;
														$ou3 = 0;
														$can = 0;
														foreach ($resq as $data2) {
															$awal_nett = isset($data2['awal_nett']) ? $data2['awal_nett'] : 0;
															$masuk_ship = isset($data2['masuk_ship']) ? $data2['masuk_ship'] : 0;
															$masuk_truck = isset($data2['masuk_truck']) ? $data2['masuk_truck'] : 0;
															$masuk_slop = isset($data2['masuk_slop']) ? $data2['masuk_slop'] : 0;
															$out_pagi = isset($data2['out_pagi']) ? $data2['out_pagi'] : 0;
															$out_malam = isset($data2['out_malam']) ? $data2['out_malam'] : 0;
															$keluar_slop = isset($data2['keluar_slop']) ? $data2['keluar_slop'] : 0;
															$gain_loss = isset($data2['gain_loss']) ? $data2['gain_loss'] : 0;
															$out_cancel = isset($data2['out_cancel']) ? $data2['out_cancel'] : 0;
															$id_master = isset($data2['id_master']) ? $data2['id_master'] : null;
															$tanggal_inv = isset($data2['tanggal_inv']) ? $data2['tanggal_inv'] : null;
															$book_stok = isset($data2['book_stok']) ? $data2['book_stok'] : 0;
															$tank_pipe = isset($data2['tank_pipe']) ? $data2['tank_pipe'] : 0;

															$nom++;
															$penya 	= ($awal_nett + $masuk_ship + $masuk_truck + $masuk_slop) -
																($out_pagi + $out_malam + $keluar_slop);
															$loss1 	= ($awal_nett && $gain_loss) ? $gain_loss - $penya : 0;
															$loss2 	= ($loss1 < 0) ? '(' . number_format(abs($loss1)) . ')' : number_format($loss1);
															$total 	= ($gain_loss) ? $penya + $loss1 + $tank_pipe : 0;
															$in1 	= $in1 + $masuk_ship;
															$in2 	= $in2 + $masuk_truck;
															$in3 	= $in3 + $masuk_slop;
															$ou1 	= $ou1 + $out_pagi;
															$ou2 	= $ou2 + $out_malam;
															$ou3 	= $ou3 + $keluar_slop;
															$can 	= $can + $out_cancel;
															$link2	= BASE_URL_CLIENT . "/terminal-inventory-add.php?" . ($id_master ? paramEncrypt("idr=" . $id_master . "&prd=" . $prd) : '');
															$class2	= ($sesRole == 5 || $sesRole == 13 ? 'clickable-row' : 'non-clickable-row');
													?>
															<tr class="<?php echo $class2; ?>" data-href="<?php echo $link2; ?>">
																<td class="text-center"><?php echo $tanggal_inv ? date("d/m/Y", strtotime($tanggal_inv)) : '-'; ?></td>
																<td class="text-right"><?php echo number_format($book_stok); ?></td>
																<td class="text-right"><?php echo number_format($awal_nett); ?></td>
																<td class="text-right"><?php echo number_format($masuk_ship); ?></td>
																<td class="text-right"><?php echo number_format($masuk_truck); ?></td>
																<td class="text-right"><?php echo number_format($masuk_slop); ?></td>
																<td class="text-right"><?php echo number_format($out_pagi); ?></td>
																<td class="text-right"><?php echo number_format($out_malam); ?></td>
																<td class="text-right"><?php echo number_format($keluar_slop); ?></td>
																<td class="text-right"><?php echo number_format($out_cancel); ?></td>
																<td class="text-right"><?php echo number_format($penya); ?></td>
																<td class="text-right"><?php echo $loss2; ?></td>
																<td class="text-right"><?php echo number_format($tank_pipe); ?></td>
																<td class="text-right"><?php echo number_format($total); ?></td>
															</tr>
														<?php } ?>
														<tr>
															<td class="text-center" colspan="3">TOTAL</td>
															<td class="text-right"><?php echo number_format($in1); ?></td>
															<td class="text-right"><?php echo number_format($in2); ?></td>
															<td class="text-right"><?php echo number_format($in3); ?></td>
															<td class="text-right"><?php echo number_format($ou1); ?></td>
															<td class="text-right"><?php echo number_format($ou2); ?></td>
															<td class="text-right"><?php echo number_format($ou3); ?></td>
															<td class="text-right"><?php echo number_format($can); ?></td>
															<td class="text-center" colspan="4">&nbsp;</td>
														</tr>
													<?php } ?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div role="tabpanel" class="tab-pane" id="data-gsv">
							<p class="text-right">Unit: Liter</p>
							<div class="row">
								<div class="col-sm-12">
									<div class="box box-info">
										<div class="box-body table-responsive">
											<table class="table table-hover no-border col-sm-top" id="table-gsv">
												<thead>
													<tr>
														<th width="6%" rowspan="2" class="text-center">TANGGAL</th>
														<th width="8%" rowspan="2" class="text-center">BOOK STOCK</th>
														<th width="8%" rowspan="2" class="text-center">OPENING/ CLOSING STOCK</th>
														<th colspan="3" class="text-center">IN<i></i></th>
														<th colspan="3" class="text-center">OUT</th>
														<th width="7%" rowspan="2" class="text-center">CANCEL</th>
														<th width="8%" rowspan="2" class="text-center">CLOSING STOCK</th>
														<th width="6%" rowspan="2" class="text-center">GAIN/ LOSS</th>
														<th width="7%" rowspan="2" class="text-center">TANK PIPE</th>
														<th width="8%" rowspan="2" class="text-center">TOTAL</th>
													</tr>
													<tr>
														<th class="text-center" width="7%">Ship</th>
														<th class="text-center" width="7%">Truck</th>
														<th class="text-center" width="7%">Slop</th>
														<th class="text-center" width="7%">07:00 - 24:00 (H)</th>
														<th class="text-center" width="7%">00:00 - 07:00 (H+1)</th>
														<th class="text-center" width="7%">Slop</th>
													</tr>
												</thead>
												<tbody>
													<?php
													if (count($res) == 0) {
														echo '<tr><td colspan="14" class="text-center">Data tidak ditemukan...</td></tr>';
													} else {
														$nom = $position;
														$ms1 = 0;
														$ms2 = 0;
														$ms3 = 0;
														$kl1 = 0;
														$kl2 = 0;
														$kl3 = 0;
														$gak = 0;
														foreach ($resq as $data2) {
															$book_stok = isset($data2['book_stok']) ? $data2['book_stok'] : 0;
															$awal_vcf = isset($data2['awal_vcf']) ? $data2['awal_vcf'] : 0;
															$awal_nett = isset($data2['awal_nett']) ? $data2['awal_nett'] : 0;
															$masuk_ship = isset($data2['masuk_ship']) ? $data2['masuk_ship'] : 0;
															$masuk_truck = isset($data2['masuk_truck']) ? $data2['masuk_truck'] : 0;
															$masuk_slop = isset($data2['masuk_slop']) ? $data2['masuk_slop'] : 0;
															$out_pagi = isset($data2['out_pagi']) ? $data2['out_pagi'] : 0;
															$out_malam = isset($data2['out_malam']) ? $data2['out_malam'] : 0;
															$keluar_slop = isset($data2['keluar_slop']) ? $data2['keluar_slop'] : 0;
															$out_cancel = isset($data2['out_cancel']) ? $data2['out_cancel'] : 0;
															$gain_loss = isset($data2['gain_loss']) ? $data2['gain_loss'] : 0;
															$tank_pipe = isset($data2['tank_pipe']) ? $data2['tank_pipe'] : 0;
															$id_master = isset($data2['id_master']) ? $data2['id_master'] : null;
															$tanggal_inv = isset($data2['tanggal_inv']) ? $data2['tanggal_inv'] : null;

															$nom++;
															$bookSt = (int)$book_stok * (int)$awal_vcf;
															$awlnet = (int)$awal_nett * (int)$awal_vcf;
															$ship 	= (int)$masuk_ship * (int)$awal_vcf;
															$truck 	= (int)$masuk_truck * (int)$awal_vcf;
															$inslop = (int)$masuk_slop * (int)$awal_vcf;
															$otpagi = (int)$out_pagi * (int)$awal_vcf;
															$otmlm 	= (int)$out_malam * (int)$awal_vcf;
															$otslop	= (int)$keluar_slop * (int)$awal_vcf;
															$cancel	= (int)$out_cancel * (int)$awal_vcf;
															$gainls = (int)$gain_loss * (int)$awal_vcf;
															$pipe 	= (int)$tank_pipe * (int)$awal_vcf;
															// $bookSt = $book_stok * $awal_vcf;
															// $awlnet = $awal_nett * $awal_vcf;
															// $ship 	= $masuk_ship * $awal_vcf;
															// $truck 	= $masuk_truck * $awal_vcf;
															// $inslop = $masuk_slop * $awal_vcf;
															// $otpagi = $out_pagi * $awal_vcf;
															// $otmlm 	= $out_malam * $awal_vcf;
															// $otslop	= $keluar_slop * $awal_vcf;
															// $cancel	= $out_cancel * $awal_vcf;
															// $gainls = $gain_loss * $awal_vcf;
															// $pipe 	= $tank_pipe * $awal_vcf;

															$penya 	= ($awlnet + $ship + $truck + $inslop) - ($otpagi + $otmlm + $otslop);
															$loss1 	= ($awal_nett && $gain_loss) ? $gainls - $penya : 0;
															$loss2 	= ($loss1 < 0) ? '(' . number_format(abs($loss1), 1) . ')' : number_format($loss1, 1);
															$total 	= ($awal_nett && $gain_loss) ? $penya + $loss1 + $pipe : 0;
															$ms1 	= $ms1 + $ship;
															$ms2 	= $ms2 + $truck;
															$ms3 	= $ms3 + $inslop;
															$kl1 	= $kl1 + $otpagi;
															$kl2 	= $kl2 + $otmlm;
															$kl3 	= $kl3 + $otslop;
															$gak 	= $gak + $cancel;
															$link3	= BASE_URL_CLIENT . "/terminal-inventory-add.php?" . ($id_master ? paramEncrypt("idr=" . $id_master . "&prd=" . $prd) : '');
															$class3	= ($sesRole == 5 || $sesRole == 13 ? 'clickable-row' : 'non-clickable-row');
													?>
															<tr class="<?php echo $class3; ?>" data-href="<?php echo $link3; ?>">
																<td class="text-center"><?php echo $tanggal_inv ? date("d/m/Y", strtotime($tanggal_inv)) : '-'; ?></td>
																<td class="text-right"><?php echo number_format($bookSt, 1); ?></td>
																<td class="text-right"><?php echo number_format($awlnet, 1); ?></td>
																<td class="text-right"><?php echo number_format($ship, 1); ?></td>
																<td class="text-right"><?php echo number_format($truck, 1); ?></td>
																<td class="text-right"><?php echo number_format($inslop, 1); ?></td>
																<td class="text-right"><?php echo number_format($otpagi, 1); ?></td>
																<td class="text-right"><?php echo number_format($otmlm, 1); ?></td>
																<td class="text-right"><?php echo number_format($otslop, 1); ?></td>
																<td class="text-right"><?php echo number_format($cancel, 1); ?></td>
																<td class="text-right"><?php echo number_format($penya, 1); ?></td>
																<td class="text-right"><?php echo $loss2; ?></td>
																<td class="text-right"><?php echo number_format($pipe, 1); ?></td>
																<td class="text-right"><?php echo number_format($total, 1); ?></td>
															</tr>
														<?php } ?>
														<tr>
															<td class="text-center" colspan="3">TOTAL</td>
															<td class="text-right"><?php echo number_format($ms1, 1); ?></td>
															<td class="text-right"><?php echo number_format($ms2, 1); ?></td>
															<td class="text-right"><?php echo number_format($ms3, 1); ?></td>
															<td class="text-right"><?php echo number_format($kl1, 1); ?></td>
															<td class="text-right"><?php echo number_format($kl2, 1); ?></td>
															<td class="text-right"><?php echo number_format($kl3, 1); ?></td>
															<td class="text-right"><?php echo number_format($gak, 1); ?></td>
															<td class="text-center" colspan="4">&nbsp;</td>
														</tr>
													<?php } ?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>

					</div>

					<?php if ($q1 == 1 && count($res) > 0) { ?>
						<div class="row">
							<div class="col-sm-12">
								<div class="table-responsive">
									<table class="table table-hover no-border col-sm-top" id="table-summary">
										<thead>
											<tr>
												<th width="8%" rowspan="2" class="text-center">SUMMARY</th>
												<th width="8%" rowspan="2" class="text-center">OPENING STOCK</th>
												<th colspan="3" class="text-center">IN</th>
												<th colspan="2" class="text-center">OUT</th>
												<th colspan="2" class="text-center">END STOCK</th>
												<th colspan="2" class="text-center">GAIN/ LOSS</th>
												<th width="8%" rowspan="2" class="text-center">TANK PIPE</th>
											</tr>
											<tr>
												<th class="text-center" width="8%">Ship</th>
												<th class="text-center" width="8%">Truck</th>
												<th class="text-center" width="8%">Slop</th>
												<th class="text-center" width="8%">Customer</th>
												<th class="text-center" width="8%">Slop</th>
												<th class="text-center" width="8%">Actual</th>
												<th class="text-center" width="8%">Book Stock</th>
												<th class="text-center" width="8%">Actual</th>
												<th class="text-center" width="8%">%</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$sum_a0_gov = $qus['opening_stok'] + $in1 + $in2 + $in3;
											$sum_a1_gov = $ou1 + $ou2;
											$sum_a2_gov = ($qus['end_actual']) ? $qus['end_actual'] : $qus['end_actual_temp'];
											$sum_a3_gov = ($qus['end_book_stok']) ? $qus['end_book_stok'] : $qus['end_book_stok_temp'];
											$sum_a4_gov = $sum_a2_gov - ($sum_a0_gov - ($ou1 + $ou2 + $ou3));
											$sum_a5_gov = ($sum_a0_gov) ? ($sum_a4_gov / $sum_a0_gov) * 100 : 0;

											$sum_a0_gsv = $qus['opening_stok_gov'] + $ms1 + $ms2 + $ms3;
											$sum_a1_gsv = $kl1 + $kl2;
											$sum_a2_gsv = ($qus['end_actual_gov']) ? $qus['end_actual_gov'] : $qus['end_actual_temp_gov'];
											$sum_a3_gsv = ($qus['end_book_stok_gov']) ? $qus['end_book_stok_gov'] : $qus['end_book_stok_temp_gov'];
											$sum_a4_gsv = $sum_a2_gsv - ($sum_a0_gsv - ($kl1 + $kl2 + $kl3));
											$sum_a5_gsv = ($sum_a0_gsv) ? ($sum_a4_gsv / $sum_a0_gsv) * 100 : 0;
											?>
											<tr>
												<td class="text-center">GOV</td>
												<td class="text-right"><?php echo number_format($qus['opening_stok']); ?></td>
												<td class="text-right"><?php echo number_format($in1); ?></td>
												<td class="text-right"><?php echo number_format($in2); ?></td>
												<td class="text-right"><?php echo number_format($in3); ?></td>
												<td class="text-right"><?php echo number_format($sum_a1_gov); ?></td>
												<td class="text-right"><?php echo number_format($ou3); ?></td>
												<td class="text-right"><?php echo number_format($sum_a2_gov); ?></td>
												<td class="text-right"><?php echo number_format($sum_a3_gov); ?></td>
												<td class="text-right"><?php echo number_format($sum_a4_gov); ?></td>
												<td class="text-right"><?php echo round($sum_a5_gov, 3) . " %"; ?></td>
												<td class="text-right"><?php echo number_format($qus['end_pipe']); ?></td>
											</tr>
											<tr>
												<td class="text-center">GSV</td>
												<td class="text-right"><?php echo number_format($qus['opening_stok_gov']); ?></td>
												<td class="text-right"><?php echo number_format($ms1); ?></td>
												<td class="text-right"><?php echo number_format($ms2); ?></td>
												<td class="text-right"><?php echo number_format($ms3); ?></td>
												<td class="text-right"><?php echo number_format($sum_a1_gsv); ?></td>
												<td class="text-right"><?php echo number_format($kl3); ?></td>
												<td class="text-right"><?php echo number_format($sum_a2_gsv); ?></td>
												<td class="text-right"><?php echo number_format($sum_a3_gsv); ?></td>
												<td class="text-right"><?php echo number_format($sum_a4_gsv); ?></td>
												<td class="text-right"><?php echo round($sum_a5_gsv, 3) . " %"; ?></td>
												<td class="text-right"><?php echo number_format($qus['end_pipe_gov']); ?></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					<?php } ?>

					<?php if ($tot_record > 0 && $q1 == "") { ?>
						<div class="row">
							<div class="col-sm-5">
								<div class="text-left-rsp"><?php echo "Showing " . ($position + 1) . " to " . $nom . " of " . $tot_record . " entries " . $tot_page; ?></div>
							</div>
							<div class="col-sm-7 col-sm-top">
								<div class="text-right-rsp">
									<?php echo $p->navPageLp($file, $tot_page, $tot_record, $page, $param_ref); ?>
								</div>
							</div>
						</div>
					<?php }
				} else if ($q1 == 2) { ?>
					<p style="margin:0 0 5px;"><b>GOV</b></p>
					<div class="row">
						<div class="col-sm-12">
							<div class="table-responsive">
								<table class="table table-bordered col-sm-top table-detail">
									<thead>
										<tr>
											<th width="25%" rowspan="2" class="text-center">BULAN</th>
											<th colspan="3" class="text-center">IN</th>
											<th colspan="2" class="text-center">OUT</th>
										</tr>
										<tr>
											<th class="text-center" width="15%">Ship</th>
											<th class="text-center" width="15%">Truck</th>
											<th class="text-center" width="15%">Slop</th>
											<th class="text-center" width="15%">Customer</th>
											<th class="text-center" width="15%">Slop</th>
										</tr>
									</thead>
									<tbody>
										<?php
										if (count($res1) == 0) {
											echo '<tr><td colspan="6" class="text-center">Data tidak ditemukan...</td></tr>';
										} else {
											$masuk1 = 0;
											$masuk2 = 0;
											$masuk3 = 0;
											$keluar1 = 0;
											$keluar2 = 0;
											$abl = array(1 => "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
											$abs = array(1 => "Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des");
											foreach ($res1q as $data1) {
												$text_a1 	= $abl[intval(substr($data1['bulan_tahun'], 4))] . " " . substr($data1['bulan_tahun'], 0, 4);
												$masuk1 	= $masuk1 + $data1['in_ship'];
												$masuk2 	= $masuk2 + $data1['in_truck'];
												$masuk3 	= $masuk3 + $data1['in_slop'];
												$keluar1 	= $keluar1 + $data1['customer'];
												$keluar2 	= $keluar2 + $data1['out_slop'];
										?>
												<tr>
													<td class="text-left"><?php echo $text_a1; ?></td>
													<td class="text-right"><?php echo number_format($data1['in_ship']); ?></td>
													<td class="text-right"><?php echo number_format($data1['in_truck']); ?></td>
													<td class="text-right"><?php echo number_format($data1['in_slop']); ?></td>
													<td class="text-right"><?php echo number_format($data1['customer']); ?></td>
													<td class="text-right"><?php echo number_format($data1['out_slop']); ?></td>
												</tr>
											<?php } ?>
											<tr>
												<td class="text-center" style="border-top-width:2px;">TOTAL</td>
												<td class="text-right" style="border-top-width:2px;"><?php echo number_format($masuk1); ?></td>
												<td class="text-right" style="border-top-width:2px;"><?php echo number_format($masuk2); ?></td>
												<td class="text-right" style="border-top-width:2px;"><?php echo number_format($masuk3); ?></td>
												<td class="text-right" style="border-top-width:2px;"><?php echo number_format($keluar1); ?></td>
												<td class="text-right" style="border-top-width:2px;"><?php echo number_format($keluar2); ?></td>
											</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<p style="margin:0 0 5px;"><b>GSV</b></p>
					<div class="row">
						<div class="col-sm-12">
							<div class="table-responsive">
								<table class="table table-bordered col-sm-top table-detail">
									<thead>
										<tr>
											<th width="25%" rowspan="2" class="text-center">BULAN</th>
											<th colspan="3" class="text-center">IN</th>
											<th colspan="2" class="text-center">OUT</th>
										</tr>
										<tr>
											<th class="text-center" width="15%">Ship</th>
											<th class="text-center" width="15%">Truck</th>
											<th class="text-center" width="15%">Slop</th>
											<th class="text-center" width="15%">Customer</th>
											<th class="text-center" width="15%">Slop</th>
										</tr>
									</thead>
									<tbody>
										<?php
										if (count($res1) == 0) {
											echo '<tr><td colspan="6" class="text-center">Data tidak ditemukan...</td></tr>';
										} else {
											$masuk1g = 0;
											$masuk2g = 0;
											$masuk3g = 0;
											$keluar1g = 0;
											$keluar2g = 0;
											foreach ($res1q as $data2) {
												$text_a2 	= $abl[intval(substr($data2['bulan_tahun'], 4))] . " " . substr($data2['bulan_tahun'], 0, 4);
												$masuk1g 	= $masuk1g + $data2['in_ship_gsv'];
												$masuk2g 	= $masuk2g + $data2['in_truck_gsv'];
												$masuk3g 	= $masuk3g + $data2['in_slop_gsv'];
												$keluar1g 	= $keluar1g + $data2['customer_gsv'];
												$keluar2g 	= $keluar2g + $data2['out_slop_gsv'];
										?>
												<tr>
													<td class="text-left"><?php echo $text_a2; ?></td>
													<td class="text-right"><?php echo number_format($data2['in_ship_gsv']); ?></td>
													<td class="text-right"><?php echo number_format($data2['in_truck_gsv']); ?></td>
													<td class="text-right"><?php echo number_format($data2['in_slop_gsv']); ?></td>
													<td class="text-right"><?php echo number_format($data2['customer_gsv']); ?></td>
													<td class="text-right"><?php echo number_format($data2['out_slop_gsv']); ?></td>
												</tr>
											<?php } ?>
											<tr>
												<td class="text-center" style="border-top-width:2px;">TOTAL</td>
												<td class="text-right" style="border-top-width:2px;"><?php echo number_format($masuk1g); ?></td>
												<td class="text-right" style="border-top-width:2px;"><?php echo number_format($masuk2g); ?></td>
												<td class="text-right" style="border-top-width:2px;"><?php echo number_format($masuk3g); ?></td>
												<td class="text-right" style="border-top-width:2px;"><?php echo number_format($keluar1g); ?></td>
												<td class="text-right" style="border-top-width:2px;"><?php echo number_format($keluar2g); ?></td>
											</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<?php if (count($res1) > 0) { ?>
						<p style="margin:0 0 5px;"><b>SUMMARY</b></p>
						<div class="row">
							<div class="col-sm-12">
								<div class="table-responsive">
									<table class="table col-sm-top" id="table-summary">
										<thead>
											<tr>
												<th width="8%" rowspan="2" class="text-center"><?php echo $abs[intval($q5)] . " " . $q6 . " - " . $abs[intval($q7)] . " " . $q8; ?></th>
												<th colspan="3" class="text-center">IN</th>
												<th colspan="2" class="text-center">OUT</th>
												<th colspan="2" class="text-center">END STOCK</th>
												<th colspan="2" class="text-center">GAIN/ LOSS</th>
												<th width="8%" rowspan="2" class="text-center">TANK PIPE</th>
											</tr>
											<tr>
												<th class="text-center" width="8%">Ship</th>
												<th class="text-center" width="8%">Truck</th>
												<th class="text-center" width="8%">Slop</th>
												<th class="text-center" width="8%">Customer</th>
												<th class="text-center" width="8%">Slop</th>
												<th class="text-center" width="8%">Actual</th>
												<th class="text-center" width="8%">Book Stock</th>
												<th class="text-center" width="8%">Actual</th>
												<th class="text-center" width="8%">%</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$sum_a1_gov = ($res2['end_actual']) ? $res2['end_actual'] : $res2['end_actual_temp'];
											$sum_a2_gov = ($res2['end_book_stok']) ? $res2['end_book_stok'] : $res2['end_book_stok_temp'];
											$sum_a3_gov = $sum_a1_gov - (($masuk1 + $masuk2 + $masuk3) - ($keluar1 + $keluar2));
											$sum_a4_gov = ($sum_a3_gov / ($masuk1 + $masuk2 + $masuk3)) * 100;

											$sum_a1_gsv = ($res2['end_actual_gov']) ? $res2['end_actual_gov'] : $res2['end_actual_temp_gov'];
											$sum_a2_gsv = ($res2['end_book_stok_gov']) ? $res2['end_book_stok_gov'] : $res2['end_book_stok_temp_gov'];
											$sum_a3_gsv = $sum_a1_gsv - (($masuk1g + $masuk2g + $masuk3g) - ($keluar1g + $keluar2g));
											$sum_a4_gsv = ($sum_a3_gsv / ($masuk1g + $masuk2g + $masuk3g)) * 100;
											?>
											<tr>
												<td class="text-center">GOV</td>
												<td class="text-right"><?php echo number_format($masuk1); ?></td>
												<td class="text-right"><?php echo number_format($masuk2); ?></td>
												<td class="text-right"><?php echo number_format($masuk3); ?></td>
												<td class="text-right"><?php echo number_format($keluar1); ?></td>
												<td class="text-right"><?php echo number_format($keluar2); ?></td>
												<td class="text-right"><?php echo number_format($sum_a1_gov); ?></td>
												<td class="text-right"><?php echo number_format($sum_a2_gov); ?></td>
												<td class="text-right"><?php echo number_format($sum_a3_gov); ?></td>
												<td class="text-right"><?php echo round($sum_a4_gov, 3) . " %"; ?></td>
												<td class="text-right"><?php echo number_format($res2['end_pipe']); ?></td>
											</tr>
											<tr>
												<td class="text-center">GSV</td>
												<td class="text-right"><?php echo number_format($masuk1g); ?></td>
												<td class="text-right"><?php echo number_format($masuk2g); ?></td>
												<td class="text-right"><?php echo number_format($masuk3g); ?></td>
												<td class="text-right"><?php echo number_format($keluar1g); ?></td>
												<td class="text-right"><?php echo number_format($keluar2g); ?></td>
												<td class="text-right"><?php echo number_format($sum_a1_gsv); ?></td>
												<td class="text-right"><?php echo number_format($sum_a2_gsv); ?></td>
												<td class="text-right"><?php echo number_format($sum_a3_gsv); ?></td>
												<td class="text-right"><?php echo round($sum_a4_gsv, 3) . " %"; ?></td>
												<td class="text-right"><?php echo number_format($res2['end_pipe_gov']); ?></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
				<?php }
				} ?>

				<?php $con->close(); ?>
			</section>
			<?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
		</aside>
	</div>

	<style type="text/css">
		#table-pencarian {
			margin-bottom: 15px;
		}

		#table-pencarian td {
			padding: 0px 5px;
		}

		#table-awal {
			border: 1px solid #ddd;
			margin-bottom: 15px;
			border-collapse: collapse;
			border-spacing: 0px;
		}

		#table-awal>thead>tr>th,
		#table-awal>tbody>tr>td {
			border: 1px solid #ddd;
			padding: 5px;
			font-size: 11px;
			font-family: arial;
			vertical-align: top;
		}

		#table-awal>thead>tr>th {
			background-color: #f4f4f4;
			vertical-align: middle;
		}

		#table-awal td.action>a.btn {
			height: auto;
			line-height: 0;
			padding: 3px 4px;
		}

		#table-awal td.action>a.btn>.fa {
			font-size: 11px;
		}

		#table-gov,
		#table-gsv {
			border: 1px solid #ddd;
			margin-bottom: 15px;
			border-collapse: collapse;
			border-spacing: 0px;
		}

		#table-gov>thead>tr>th,
		#table-gov>tbody>tr>td,
		#table-summary>thead>tr>th,
		#table-summary>tbody>tr>td,
		#table-gsv>thead>tr>th,
		#table-gsv>tbody>tr>td {
			border: 1px solid #ddd;
			padding: 5px;
			font-size: 9px;
			font-family: arial;
			vertical-align: top;
		}

		#table-gov>thead>tr>th,
		#table-summary>thead>tr>th,
		#table-gsv>thead>tr>th {
			background-color: #f4f4f4;
			vertical-align: middle;
		}

		.table-detail>thead>tr>th,
		.table-detail>tbody>tr>td {
			padding: 5px;
			font-size: 11px;
			font-family: arial;
			vertical-align: top;
		}

		.table-detail>thead>tr>th {
			background-color: #f4f4f4;
			vertical-align: middle;
		}
	</style>
	<script>
		$(document).ready(function() {
			$("#q1").change(function() {
				var nilai = $(this).val();
				if (nilai == 1) {
					$("#q3, #q4").removeAttr('disabled');
					$("#q2, #q5, #q6, #q7, #q8").attr('disabled', 'disabled').val("");
				} else if (nilai == 2) {
					$("#q5, #q6, #q7, #q8").removeAttr('disabled');
					$("#q2, #q3, #q4").attr('disabled', 'disabled').val("");
				} else {
					$("#q2, #q3, #q4, #q5, #q6, #q7, #q8").attr('disabled', 'disabled').val("");
				}
			});

			$("[data-toggle='tablink']").click(function() {
				var $this = $(this);
				var idnya = $this.attr('href');
				if ($this.parent().hasClass("active") == false) {
					window.location.href = idnya;
				}
				return false;
			});

		});
	</script>
</body>

</html>
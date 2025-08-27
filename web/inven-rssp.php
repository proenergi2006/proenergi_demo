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

$arrBln 	= array(1 => "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
$arrBln02 	= array(1 => "JAN", "FEB", "MAR", "APR", "MEI", "JUN", "JUL", "AGU", "SEP", "OKT", "NOV", "DES");

$q1 = (isset($enk['q1']) && $enk['q1'] ? htmlspecialchars($enk['q1'], ENT_QUOTES) : NULL);
$q2 = (isset($enk['q2']) && $enk['q2'] ? htmlspecialchars($enk['q2'], ENT_QUOTES) : date('m'));
$q3 = (isset($enk['q3']) && $enk['q3'] ? htmlspecialchars($enk['q3'], ENT_QUOTES) : date('Y'));
$q4 = (isset($enk['q4']) && $enk['q4'] ? htmlspecialchars($enk['q4'], ENT_QUOTES) : NULL);
$q5 = (isset($enk['q5']) && $enk['q5'] ? htmlspecialchars($enk['q5'], ENT_QUOTES) : NULL);

$display = (isset($enk['display']) && $enk['display'] ? 1 : 0);
$sesRole = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$sesGrup = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);
$sesCbng = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

// Cek peran pengguna
$required_role = ['1', '21', '4', '3', '5'];
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
				<h1>Mutasi Stock</h1>
			</section>
			<section class="content">

				<?php $flash->display(); ?>
				<div class="box box-info">
					<div class="box-header with-border">
						<p style="font-size:18px; margin-bottom:0px;"><b>PENCARIAN</b></p>
					</div>
					<div class="box-body">
						<form name="sFrm" id="sFrm" method="post" action="<?php echo BASE_URL_CLIENT . "/vendor-inven-terminal.php"; ?>">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group row">
										<label class="col-md-12">Berdasarkan</label>
										<div class="col-md-12">
											<select id="q1" name="q1" class="form-control select2">
												<option></option>
												<option value="1" <?php echo ($q1 == 1 ? 'selected' : ''); ?>>Depot Terminal</option>
												<option value="2" <?php echo ($q1 == 2 ? 'selected' : ''); ?>>Cabang</option>
												<option value="3" <?php echo ($q1 == 3 ? 'selected' : ''); ?>>Nasional</option>
											</select>
										</div>
									</div>
								</div>
							</div>

							<div id="group_area" <?php echo (!$q4 ? 'style="display:none;"' : ''); ?>>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group row">
											<label class="col-md-12">Depot Terminal</label>
											<div class="col-md-12">
												<select id="q4" name="q4" class="form-control select2">
													<option></option>
													<?php $con->fill_select("id_master", "concat(nama_terminal,' ',tanki_terminal)", "pro_master_terminal", $q4, "where is_active=1", "id_master", false); ?>
												</select>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div id="group_cabang" <?php echo (!$q5 ? 'style="display:none;"' : ''); ?>>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group row">
											<label class="col-md-12">Cabang</label>
											<div class="col-md-12">
												<select id="q5" name="q5" class="form-control select2">
													<option></option>
													<?php $con->fill_select("id_master", "nama_cabang", "pro_master_cabang", $q5, "where is_active=1 and id_master <> 1", "", false); ?>
												</select>
											</div>
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
							<a href="<?php echo BASE_URL_CLIENT . '/vendor-inven-terminal.php'; ?>" class="btn btn-success btn-sm" style="min-width:120px;">
								<i class="fa fa-reply jarak-kanan"></i> Kembali
							</a>


						</form>
					</div>
				</div>

				<div id="html-tabelnya">
					<?php
					if ($display) {
						$nextMonth 	= date("Y-m-d", strtotime('+1 month', strtotime($q3 . '-' . $q2 . '-01')));
						$tglakhir01 = date('t', strtotime($q3 . '-' . $q2 . '-01'));

						$cek = "select * from pro_master_produk where is_active = 1 order by no_urut";
						$row = $con->getResult($cek);
						$rowProduk = (count($row) > 0) ? $row : array();

						$hasil = '<div id="wrapper_scrolltabs_tabSet" class="wrapper_scrolltabs">';
						$hasil .= '<ul id="tabSet" class="scroll_tabs_theme_custom1">';
						foreach ($rowProduk as $idxProduk => $dataProduk) {
							$idproduk = $dataProduk['id_master'];
							$nmproduk = strtoupper($dataProduk['jenis_produk'] . ' - ' . $dataProduk['merk_dagang']);
							$hasil .= '<li class="' . ($idxProduk == 0 ? 'active tab_selected' : '') . '"><a data-href="#lampiran-nc-' . $idproduk . '">' . $nmproduk . '</a></li>';
						}
						$hasil .= '</ul>';
						$hasil .= '<div class="tab-content">';

						foreach ($rowProduk as $idxProduk => $dataProduk) {
							$idproduk = $dataProduk['id_master'];
							$nmproduk = strtoupper($dataProduk['jenis_produk'] . ' - ' . $dataProduk['merk_dagang']);

							$hasil .= '<div class="tab-pane" id="lampiran-nc-' . $idproduk . '" ' . ($idxProduk == 0 ? 'style="display:block;"' : '') . '>';

							$textExport01 = 'q1=' . $q1 . '&q2=' . $q2 . '&q3=' . $q3 . '&q4=' . $q4 . '&q5=' . $q5 . '&idproduk=' . $idproduk;
							$linkExport01 = BASE_URL_CLIENT . "/inven-rspp-export-excel.php?" . paramEncrypt($textExport01);
							$hasil .= '
						<div style="margin:0px 0px 15px 0px;"><a href="' . $linkExport01 . '" target="_blank" class="btn btn-success btn-sm" style="min-width:120px;">
							<i class="far fa-file-excel jarak-kanan"></i> Export Excel
						</a></div>';

							$sqlcek01 = "
							select a.id_datanya, a.id_produk, a.id_terminal 
							from new_pro_inventory_depot a 
							join pro_master_terminal b on a.id_terminal = b.id_master 
							where a.id_jenis = 1 and a.id_produk = '" . $idproduk . "' 
						";

							if ($q1 == '1' || $q1 == '2') {
								if ($q4) {
									$where01 = " and a.id_terminal = '" . $q4 . "'";
									$where02 = " and a.id_master = '" . $q4 . "'";
									$sqlcek01 .= " and a.id_terminal = '" . $q4 . "'";
									//$where01Output = " and o.id_terminal = '".$q4."'";
									$where01Output = " and a.id_terminal = '" . $q4 . "'";
								}

								if ($q5) {
									$where01 = " and b.id_cabang = '" . $q5 . "'";
									$where02 = " and a.id_cabang = '" . $q5 . "'";
									$sqlcek01 .= " and b.id_cabang = '" . $q5 . "'";
									//$where01Output = " and o.id_terminal = '".$q5."'";
									$where01Output = " and b.id_cabang = '" . $q5 . "'";
								}
							} else {
								$where01 = "";
								$where02 = "";
								$sqlcek01 .= "";
								$where01Output = "";
							}
							$rescek01 = $con->getResult($sqlcek01);
							if (count($rescek01) > 0) {
								require_once($public_base_directory . "/web/models/inven-rspp-data-awal.php");
								$resutama01 = $con->getResult($sqlutama01);

								require_once($public_base_directory . "/web/models/inven-rspp-input.php");
								$resutama02 = $con->getResult($sqlutama02);

								require_once($public_base_directory . "/web/models/inven-rspp-input-adj.php");
								$resutama03 = $con->getResult($sqlutama03);

								require_once($public_base_directory . "/web/models/inven-rspp-output.php");
								$resutama04 = $con->getResult($sqlutama04);

								require_once($public_base_directory . "/web/models/inven-rspp-output-adj.php");
								$resutama05 = $con->getResult($sqlutama05);

								$arrDataUtama = array();
								foreach ($resutama01 as $idx => $data) {
									$arrIsi = array();
									foreach (array_keys($data) as $data12) {
										if (intval($data12) == '0' && $data12 != '0')
											$arrIsi[$data12] = $data[$data12];
									}
									$arrDataUtama[$data['id_terminal']] = $arrIsi;
								}

								foreach ($resutama02 as $idx => $data) {
									if ($idx == 0) {
										$arrDataUtama['supplyTxt']['id_terminal'] 	= NULL;
										$arrDataUtama['supplyTxt']['ket_terminal'] 	= 'SUPPLY';
									}

									$arrDataUtama[$data['id_terminal'] . '.supply']['id_terminal'] 	= $data['id_terminal'];
									$arrDataUtama[$data['id_terminal'] . '.supply']['ket_terminal'] 	= $data['ket_terminal'];

									for ($tglnya = 1; $tglnya <= $tglakhir01; $tglnya++) {
										$txtTglnya = str_pad($tglnya, 2, '0', STR_PAD_LEFT);
										$arrDataUtama[$data['id_terminal'] . '.supply']['col' . $txtTglnya] = $data['col' . $txtTglnya];
									}
								}

								foreach ($resutama03 as $idx => $data) {
									if ($idx == 0) {
										$arrDataUtama['supplyAdjTxt']['id_terminal'] 	= NULL;
										$arrDataUtama['supplyAdjTxt']['ket_terminal'] 	= 'ADJUSTMENT (+)';
									}

									$arrDataUtama[$data['id_terminal'] . '.supplyAdj']['id_terminal'] 	= $data['id_terminal'];
									$arrDataUtama[$data['id_terminal'] . '.supplyAdj']['ket_terminal'] 	= $data['ket_terminal'];

									for ($tglnya = 1; $tglnya <= $tglakhir01; $tglnya++) {
										$txtTglnya = str_pad($tglnya, 2, '0', STR_PAD_LEFT);
										$arrDataUtama[$data['id_terminal'] . '.supplyAdj']['col' . $txtTglnya] = $data['col' . $txtTglnya];
									}
								}

								foreach ($resutama04 as $idx => $data) {
									if ($idx == 0) {
										$arrDataUtama['outputTxt']['id_terminal'] 	= NULL;
										$arrDataUtama['outputTxt']['ket_terminal'] 	= 'SALES';
									}
									$arrDataUtama[$data['id_terminal'] . '.output']['id_terminal'] 	= $data['id_terminal'];
									$arrDataUtama[$data['id_terminal'] . '.output']['ket_terminal'] 	= $data['ket_terminal'];

									for ($tglnya = 1; $tglnya <= $tglakhir01; $tglnya++) {
										$txtTglnya = str_pad($tglnya, 2, '0', STR_PAD_LEFT);
										$arrDataUtama[$data['id_terminal'] . '.output']['col' . $txtTglnya] = $data['col' . $txtTglnya];
									}
								}

								foreach ($resutama05 as $idx => $data) {
									if ($idx == 0) {
										$arrDataUtama['outputAdjTxt']['id_terminal'] 	= NULL;
										$arrDataUtama['outputAdjTxt']['ket_terminal'] 	= 'ADJUSTMENT (-)';
									}

									$arrDataUtama[$data['id_terminal'] . '.outputAdj']['id_terminal'] 	= $data['id_terminal'];
									$arrDataUtama[$data['id_terminal'] . '.outputAdj']['ket_terminal'] 	= $data['ket_terminal'];

									for ($tglnya = 1; $tglnya <= $tglakhir01; $tglnya++) {
										$txtTglnya = str_pad($tglnya, 2, '0', STR_PAD_LEFT);
										$arrDataUtama[$data['id_terminal'] . '.outputAdj']['col' . $txtTglnya] = $data['col' . $txtTglnya] * -1;
									}
								}

								$arrGrandTotal = array("ket_terminal" => "TOTAL ENDING");
								for ($tglnya = 1; $tglnya <= $tglakhir01; $tglnya++) {
									$txtTglnya = str_pad($tglnya, 2, '0', STR_PAD_LEFT);
									$arrGrandTotal['col' . $txtTglnya] = 0;
								}

								$grandTot01 = 0;
								$grandTot02 = 0;
								$grandTot03 = 0;
								$grandTot04 = 0;
								$grandTot05 = 0;
								$grandTot06 = 0;
								$grandTot07 = 0;
								$grandTot08 = 0;
								$grandTot09 = 0;
								$grandTot10 = 0;
								$grandTot11 = 0;
								$grandTot12 = 0;
								$grandTot13 = 0;
								$grandTot14 = 0;
								$grandTot15 = 0;
								$grandTot16 = 0;
								$grandTot17 = 0;
								$grandTot18 = 0;
								$grandTot19 = 0;
								$grandTot20 = 0;
								$grandTot21 = 0;
								$grandTot22 = 0;
								$grandTot23 = 0;
								$grandTot24 = 0;
								$grandTot25 = 0;
								$grandTot26 = 0;
								$grandTot27 = 0;
								$grandTot28 = 0;
								$grandTot29 = 0;
								$grandTot30 = 0;
								$grandTot31 = 0;

								foreach ($resutama01 as $idx => $data) {
									if ($idx == 0) {
										$arrDataUtama['totalTxt']['id_terminal'] 	= NULL;
										$arrDataUtama['totalTxt']['ket_terminal'] 	= 'ENDING';
									}
									$arrDataUtama[$data['id_terminal'] . '.total']['id_terminal'] 	= $data['id_terminal'];
									$arrDataUtama[$data['id_terminal'] . '.total']['ket_terminal'] 	= $data['ket_terminal'];

									for ($tglnya = 1; $tglnya <= $tglakhir01; $tglnya++) {
										if ($tglnya == '1') {
											$txtTglnya = str_pad($tglnya, 2, '0', STR_PAD_LEFT);
											$arrDataUtama[$data['id_terminal'] . '.total']['col' . $txtTglnya] = $data['col' . $txtTglnya] +
												($arrDataUtama[$data['id_terminal'] . '.supply']['col' . $txtTglnya] + $arrDataUtama[$data['id_terminal'] . '.supplyAdj']['col' . $txtTglnya]) -
												($arrDataUtama[$data['id_terminal'] . '.output']['col' . $txtTglnya] + $arrDataUtama[$data['id_terminal'] . '.outputAdj']['col' . $txtTglnya]);
										} else {
											$txtTglnya = str_pad($tglnya, 2, '0', STR_PAD_LEFT);
											$txtTglBef = str_pad(($tglnya - 1), 2, '0', STR_PAD_LEFT);

											$arrDataUtama[$data['id_terminal'] . '.total']['col' . $txtTglnya] = $arrDataUtama[$data['id_terminal'] . '.total']['col' . $txtTglBef] +
												($arrDataUtama[$data['id_terminal'] . '.supply']['col' . $txtTglnya] + $arrDataUtama[$data['id_terminal'] . '.supplyAdj']['col' . $txtTglnya]) -
												($arrDataUtama[$data['id_terminal'] . '.output']['col' . $txtTglnya] + $arrDataUtama[$data['id_terminal'] . '.outputAdj']['col' . $txtTglnya]);
										}

										$arrGrandTotal['col' . $txtTglnya] = $arrGrandTotal['col' . $txtTglnya] + $arrDataUtama[$data['id_terminal'] . '.total']['col' . $txtTglnya];
									}
								}

								foreach ($resutama01 as $idx => $data) {
									for ($tglnya = 2; $tglnya <= $tglakhir01; $tglnya++) {
										$txtTglnya = str_pad($tglnya, 2, '0', STR_PAD_LEFT);
										$txtTglBef = str_pad(($tglnya - 1), 2, '0', STR_PAD_LEFT);
										$arrDataUtama[$data['id_terminal']]['col' . $txtTglnya] = $arrDataUtama[$data['id_terminal'] . '.total']['col' . $txtTglBef];
									}
								}


								//echo '<pre>'; print_r($arrDataUtama); echo '</pre>';
								$theadnya = '';
								for ($tglnya = 1; $tglnya <= $tglakhir01; $tglnya++) {
									$theadnya .= '<th class="text-center" width="100">' . str_pad($tglnya, 2, '0', STR_PAD_LEFT) . ' ' . $arrBln02[$q2] . '</th>';
								}
								$hasil .= '
							<div style="overflow-x: scroll" id="table-long">
								<div style="width:' . (250 + ($tglakhir01 * 100)) . 'px; height:400px;" id="parent_table_laporan">
									<table id="datatable-vo-aktif" class="table table-bordered myTablea">
										<thead>
											<tr style="height:40px;">
												<th class="text-center" width="">DEPOT TERMINAL</th>
												' . $theadnya . '
											</tr>
										</thead>
										<tbody>
							';

								if (count($arrDataUtama) > 0) {
									$arrDataMain = array("supplyTxt", "supplyAdjTxt", "outputTxt", "outputAdjTxt", "totalTxt");
									foreach ($arrDataUtama as $idxnya => $datanya) {
										if (in_array($idxnya, $arrDataMain)) {
											$hasil .= '
										<tr style="background-color:#f4f4f4;">
											<td class="text-left"><b>' . $datanya['ket_terminal'] . '</b></td>
											<td class="text-right" colspan="' . $tglakhir01 . '">&nbsp;</td>
										</tr>';
										} else {
											$tbodynya = '';
											for ($tglnya = 1; $tglnya <= $tglakhir01; $tglnya++) {
												$txtTglnya = str_pad($tglnya, 2, '0', STR_PAD_LEFT);
												$tbodynya .= '<td class="text-right">' . number_format($datanya['col' . $txtTglnya], 0) . '</td>';
											}
											$hasil .= '
										<tr>
											<td class="text-left">' . $datanya['ket_terminal'] . '</td>
											' . $tbodynya . '
										</tr>';
										}
									}
									$tfootnya = '';
									for ($tglnya = 1; $tglnya <= $tglakhir01; $tglnya++) {
										$txtTglnya = str_pad($tglnya, 2, '0', STR_PAD_LEFT);
										$tfootnya .= '<td class="text-right"><b>' . number_format($arrGrandTotal['col' . $txtTglnya], 0) . '</b></td>';
									}

									$hasil .= '
								<tr style="background-color:#f4f4f4;">
									<td class="text-left"><b>' . $arrGrandTotal['ket_terminal'] . '</b></td>
									' . $tfootnya . '
								</tr>';

									$hasil .= '</tbody>';
								} else {
									$hasil .= '
								<tr height="40">
									<td class="text-center" colspan="' . ($tglakhir01 + 1) . '" style="vertical-align:middle;">
										<i>Nilai Data Awal Bukan Pada Bulan ' . $arrBln[$q2] . ' Tahun ' . $q3 . '</i>
									</td>
								</tr>';
								}
								$hasil .= '</table></div></div>';
							} else {
								$hasil .= '<i>Data Awal untuk terminal dan produk ini belum diset</i>';
							}

							$hasil .= '</div>';
						}

						$hasil .= '</div>';
						echo $hasil;
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

		#html-tabelnya .myTablea>thead>tr>th:first-child {
			position: sticky;
			left: 0px;
			background-color: #f4f4f4;
		}

		#html-tabelnya .myTablea>tbody>tr>td:first-child {
			position: sticky;
			left: 0px;
			background-color: #f4f4f4;
		}
	</style>
	<script>
		$(document).ready(function() {
			$("#q1").on("change", function() {
				let nilai = $(this).val();

				if (nilai == '1') {
					$("#group_area").show(400, "swing");
					$("#group_cabang").hide("400", "swing", function() {
						$("#q5").val("").trigger("change");
					});
				} else if (nilai == '2') {
					$("#group_area").hide("400", "swing", function() {
						$("#q4").val("").trigger("change");
					});
					$("#group_cabang").show(400, "swing");
				} else {
					$("#group_area").hide("400", "swing", function() {
						$("#q4").val("").trigger("change");
					});
					$("#group_cabang").hide("400", "swing", function() {
						$("#q5").val("").trigger("change");
					});
				}

			});

			$("#html-tabelnya").find("#tabSet").scrollTabs({
				click_callback: function(e) {
					let elemLi = $(this);
					let wrapper = elemLi.parents(".wrapper_scrolltabs").first();
					let nilai1 = elemLi.children().data("href");
					if (nilai1.substr(0, 1) == '#') {
						let element = wrapper.children(".tab-content");
						element.children().fadeOut().promise().done(function() {
							element.find(nilai1).fadeIn();
							$("#html-tabelnya").find(".myTablea").floatThead('reflow');
						});
					} else {
						window.location.href = nilai1;
					}
				}
			});

			$("#html-tabelnya").find(".myTablea").floatThead({
				position: 'absolute',
				zIndex: 799,
				scrollContainer: function($table) {
					return $table.closest("#table-long");
				},
				responsiveContainer: function($table) {
					return $table.closest("#table-long");
				},
				top: function pageTop() {
					return $(".main-header").height() + $(".content-header").height();
				},
			});

			$("#btnSearch").on("click", function() {
				if ($("#q1").val() == "" || $("#q2").val() == "" || $("#q3").val() == "") {
					swal.fire({
						icon: "warning",
						width: '350px',
						allowOutsideClick: false,
						html: '<p style="font-size:14px; font-family:arial;">Kolom Berdasarkan, Bulan dan Tahun<br />harus dipilih</p>'
					});
				} else if ($("#q1").val() == "1" && $("#q4").val() == "") {
					swal.fire({
						icon: "warning",
						width: '350px',
						allowOutsideClick: false,
						html: '<p style="font-size:14px; font-family:arial;">Kolom Area belum dipilih</p>'
					});
				} else if ($("#q1").val() == "2" && $("#q5").val() == "") {
					swal.fire({
						icon: "warning",
						width: '350px',
						allowOutsideClick: false,
						html: '<p style="font-size:14px; font-family:arial;">Kolom Cabang belum dipilih</p>'
					});
				} else {
					$("#loading_modal").modal();
					$.post("./action/seturldata.php", $("#sFrm").serializeArray(), function(data) {
						window.location.href = base_url + '/web/inven-rssp.php?' + data;
					});
				}
			});
		});
	</script>
</body>

</html>
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
$action = "add";
$section = "Tambah";
$seswil = paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]);
$cek = "select inisial_segel, stok_segel, urut_segel from pro_master_cabang where id_master = '" . $seswil . "'";
$row = $con->getRecord($cek);
$sg1 = ($row['urut_segel']) ? $row['inisial_segel'] . "-" . str_pad($row['urut_segel'], 4, '0', STR_PAD_LEFT) : 'Tidak ada';
$sg2 = ($row['stok_segel']) ? number_format($row['stok_segel'], 0, '', '.') : 'Tidak ada';
$cna = "Gd. Graha Irtama LT. 6 G Jl. HR Rasuna Said, Kuningan, Jakarta";
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("formatNumber", "jqueryUI"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory . "/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
		<aside class="right-side">
			<section class="content-header">
				<h1><?php echo $section . " DN Kapal"; ?></h1>
			</section>
			<section class="content">

				<?php $flash->display(); ?>
				<div class="row">
					<div class="col-sm-12">
						<div class="box box-primary">
							<div class="box-header with-border">
								<h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
							</div>
							<div class="box-body">
								<form action="<?php echo ACTION_CLIENT . '/delivery-kapal.php'; ?>" id="gform" name="gform" method="post" role="form">
									<div class="form-group row">
										<div class="col-sm-6">
											<label>Kode PR *</label>
											<select id="code_pr" name="code_pr" tabindex="1" class="form-control select2 validate[required]">
												<option></option>
												<?php
												$sOpt = "select a.id_pr as id, a.nomor_pr as nama, b.jum_pr, c.jum_po from pro_pr a 
														 join (select count(id_prd) as jum_pr, id_pr from pro_pr_detail where pr_mobil = 2 and is_approved = 1 group by id_pr) b 
														 	on a.id_pr = b.id_pr
														 left join (select id_pr, count(id_dsk) as jum_po from pro_po_ds_kapal group by id_pr) c on a.id_pr = c.id_pr 
														 where a.disposisi_pr = 7 and a.id_wilayah = '" . $seswil . "' and (c.jum_po is null or b.jum_pr > c.jum_po) 
														 order by a.id_pr desc";
												$rOpt = $con->getResult($sOpt);
												if (count($rOpt) > 0) {
													foreach ($rOpt as $datx) {
														echo '<option value="' . $datx['id'] . '">' . $datx['nama'] . '</option>';
													}
												}
												?>
											</select>
										</div>
										<div class="col-sm-3 col-sm-top">
											<label>Tanggal DN *</label>
											<input type="text" id="tanggal_dn" name="tanggal_dn" tabindex="3" autocomplete='off' class="form-control datepicker validate[required,custom[date]]" value="<?php echo date("d/m/Y"); ?>" />
										</div>
									</div>
									<div id="ket-po"></div>
									<div class="form-group row" hidden>
										<div class="col-sm-6">
											<label>Nomor DN *</label>
											<input type="text" id="nomor_dn" name="nomor_dn" tabindex="2" class="form-control validate[required]" />
										</div>

									</div>
									<div class="form-group row">
										<div class="col-sm-6">
											<label>Nama Shipper *</label>
											<input type="text" id="signor_name" name="signor_name" tabindex="4" class="form-control validate[required]" value="PT. PRO ENERGI" />
										</div>
										<div class="col-sm-6 col-sm-top">
											<label>Alamat Shipper *</label>
											<input type="text" id="signor_addr" name="signor_addr" tabindex="5" class="form-control validate[required]" value="<?php echo $cna; ?>" />
										</div>
									</div>
									<div class="form-group row">
										<div class="col-sm-6">
											<label>Nama Consignee *</label>
											<input type="text" id="signee_name" name="signee_name" tabindex="6" class="form-control validate[required]" />
										</div>
										<div class="col-sm-6 col-sm-top">
											<label>Alamat Consignee *</label>
											<input type="text" id="signee_addr" name="signee_addr" tabindex="7" class="form-control validate[required]" />
										</div>
									</div>
									<div class="form-group row">
										<div class="col-sm-6">
											<label>Notify Party</label>
											<input type="text" id="notify_name" name="notify_name" tabindex="8" class="form-control" value="" />
										</div>
										<div class="col-sm-6 col-sm-top">
											<label>Alamat</label>
											<input type="text" id="notify_addr" name="notify_addr" tabindex="9" class="form-control" value="" />
										</div>
									</div>
									<div class="table-responsive">
										<table class="table table-bordered table-grid1">
											<thead>
												<tr>
													<th rowspan="2" class="text-center" width="5%">No</th>
													<th rowspan="2" class="text-center" width="25%">Description</th>
													<th colspan="3" class="text-center">Quantity</th>
													<th rowspan="2" class="text-center" width="25%">Unit</th>
												</tr>
												<tr>
													<th class="text-center" width="15%">SYSTEM</th>
													<th class="text-center" width="15%">BL</th>
													<th class="text-center" width="15%">SFAL</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td rowspan="3" class="text-center">1</td>
													<td rowspan="3" class="text-center">
														<input type="text" name="produk" id="produk" tabindex="10" class="form-control input-sm validate[required]" readonly />
													</td>
													<td rowspan="3">
														<p id="sistem_volume" class="text-right"></p>
													</td>
													<td><input type="text" name="bl1" id="bl1" tabindex="11" class="form-control input-sm validate[required] hitung" readonly /></td>
													<td><input type="text" name="sf1" id="sf1" tabindex="12" class="form-control input-sm hitung" /></td>
													<td>Litres Observe</td>
												</tr>
												<tr>
													<td><input type="text" name="bl2" id="bl2" tabindex="13" class="form-control input-sm hitung" /></td>
													<td><input type="text" name="sf2" id="sf2" tabindex="14" class="form-control input-sm hitung" /></td>
													<td>Litres 15<sup>o</sup>C (GSV)</td>
												</tr>
												<tr>
													<td><input type="text" name="bl3" id="bl3" tabindex="15" class="form-control input-sm hitung" /></td>
													<td><input type="text" name="sf3" id="sf3" tabindex="16" class="form-control input-sm hitung" /></td>
													<td>MT</td>
												</tr>
											</tbody>
										</table>
									</div>
									<div class="form-group row">
										<div class="col-sm-6 col-sm-top">
											<label>Terminal *</label>
											<input type="text" id="terminal_addr" name="terminal_addr" tabindex="18" class="form-control validate[required]" value="" disabled />
											<input type="hidden" id="id_terminal_addr" name="terminal" tabindex="18" class="form-control" value="" />
										</div>

										<div class="col-sm-6 col-sm-top">
											<label>Port of Discharge *</label>
											<input type="text" id="port_addr" name="port_addr" tabindex="18" class="form-control validate[required]" value="" />
										</div>
									</div>

									<div class="form-group row">
										<div class="col-sm-4">
											<label>OA Penawaran *</label>
											<input type="text" id="oa_penawaran" name="oa_penawaran" tabindex="20" class="form-control validate[required]" value="" readonly />
										</div>
										<div class="col-sm-4 col-sm-top">
											<label>OA Disetujui *</label>
											<input type="text" id="oa_disetujui" name="oa_disetujui" tabindex="20" class="form-control validate[required]" value="" readonly />
										</div>

										<div class="col-sm-4 col-sm-top">
											<label>OA Transportir *</label>
											<input type="text" id="oa_transportir" name="oa_transportir" tabindex="20" class="form-control" value="" />
										</div>
									</div>
									<div class="form-group row">
										<div class="col-sm-6">
											<label>Shipping Line *</label>
											<select id="transportir" name="transportir" tabindex="19" class="form-control select2 validate[required]">
												<option></option>
												<?php $con->fill_select("id_master", "concat(nama_suplier,' - ',nama_transportir)", "pro_master_transportir", "", "where is_active=1 and tipe_angkutan in(2,3)", "id_master", false); ?>
											</select>
										</div>
										<div class="col-sm-6 col-sm-top">
											<label>Master/Captain *</label>
											<input type="text" id="kapten" name="kapten" tabindex="20" class="form-control validate[required]" value="" />
										</div>
									</div>
									<div class="form-group row">
										<div class="col-sm-3">
											<label>Estimasi Tgl Loading *</label>
											<input type="text" id="tgl_etl" name="tgl_etl" tabindex="21" class="form-control input-po datepicker" value="" autocomplete="off" />
										</div>
										<div class="col-sm-3">
											<label>Estimasi Jam Loading *</label>
											<input type="text" id="jam_etl" name="jam_etl" tabindex="21" class="form-control input-po timepicker" value="" autocomplete="off" />
										</div>
										<div class="col-sm-3 col-sm-top">
											<label>Estimasi Tiba Customer *</label>
											<input type="text" id="tgl_eta" name="tgl_eta" tabindex="22" class="form-control  datepicker" value="" autocomplete="off" />
										</div>
										<div class="col-sm-3">
											<label>Estimasi Jam Tiba *</label>
											<input type="text" id="jam_eta" name="jam_eta" tabindex="21" class="form-control input-po timepicker" value="" autocomplete="off" />
										</div>
									</div>
									<div class="form-group row">
										<div class="col-sm-6">
											<label>Vessel Name *</label>
											<input type="text" id="vessel_name" name="vessel_name" tabindex="21" class="form-control validate[required]" value="" />
										</div>
										<div class="col-sm-6 col-sm-top">
											<label>Shipment</label>
											<input type="text" id="shipment" name="shipment" tabindex="22" class="form-control" value="By Ship" />
										</div>
									</div>
									<p style="font-size:18px;"><b><u>Seal Number</u></b></p>
									<p style="font-size:12px;"><i>Nomor Segel Terakhir : <?php echo $sg1; ?><span class="marginX">&nbsp;</span>Stock Segel : <?php echo $sg2; ?></i></p>
									<div class="form-group row">
										<div class="col-sm-6">
											<label>Seal</label>
											<input type="text" name="tank_kiri[]" id="tank_kiri1" tabindex="23" class="form-control input-sm hitung" />
										</div>
										<div class="col-sm-6 col-sm-top">
											<label>Keterangan</label>
											<textarea id="catatan" name="catatan" tabindex="30" class="form-control">Saat serah terima, telah diperiksa semua segel dalam keadaan baik,tanpa cacat dan BBM diterima sesuai dengan spesifikasi dan volume tersebut diatas.</textarea>
										</div>
									</div>
									<!-- <div class="table-responsive">
										<table class="table table-bordered table-grid1" id="seal-nomor">
											<thead>
												<tr>
													<th class="text-center" width="20%">ITEMS</th>
													<th class="text-center" width="25%">SEGEL</th>
													<th class="text-center" width="20%">ITEMS</th>
													<th class="text-center" width="25%">SEGEL</th>
													<th class="text-center" width="10%">
														<a class="btn btn-action btn-primary addRow" data-row-count="6"><i class="fa fa-plus"></i></a>
													</th>
												</tr>
											</thead>
											<tbody>
												<tr class="tank">
													<td><span class="jnstank1">1S</span></td>
													<td>
														<input type="text" name="tank_kiri[]" id="tank_kiri1" tabindex="23" class="form-control input-sm hitung" />
													</td>
													<td><span class="jnstank2">1P</span></td>
													<td>
														<input type="text" name="tank_kanan[]" id="tank_kanan1" tabindex="24" class="form-control input-sm hitung" />
													</td>
													<td>&nbsp;</td>
												</tr>
												<tr class="tank">
													<td><span class="jnstank1">2S</span></td>
													<td>
														<input type="text" name="tank_kiri[]" id="tank_kiri2" tabindex="23" class="form-control input-sm hitung" />
													</td>
													<td><span class="jnstank2">2P</span></td>
													<td>
														<input type="text" name="tank_kanan[]" id="tank_kanan2" tabindex="24" class="form-control input-sm hitung" />
													</td>
													<td>&nbsp;</td>
												</tr>
												<tr class="tank">
													<td><span class="jnstank1">3S</span></td>
													<td>
														<input type="text" name="tank_kiri[]" id="tank_kiri3" tabindex="23" class="form-control input-sm hitung" />
													</td>
													<td><span class="jnstank2">3P</span></td>
													<td>
														<input type="text" name="tank_kanan[]" id="tank_kanan3" tabindex="24" class="form-control input-sm hitung" />
													</td>
													<td>&nbsp;</td>
												</tr>
												<tr>
													<td>Manifold (Kiri)</td>
													<td><input type="text" name="manifold1" id="manifold1" tabindex="25" class="form-control input-sm hitung" /></td>
													<td>Manifold (Kanan)</td>
													<td><input type="text" name="manifold2" id="manifold2" tabindex="26" class="form-control input-sm hitung" /></td>
													<td>&nbsp;</td>
												</tr>
												<tr>
													<td>Pump Room</td>
													<td colspan="3">
														<input type="text" name="pump1" id="pump1" tabindex="27" class="form-control input-sm hitung" />
													</td>
													<td>&nbsp;</td>
												</tr>
											</tbody>
										</table>
									</div> -->

									<!-- <p style="font-size:18px;"><b><u>Seal Number Others</u></b></p>
									<div class="table-responsive">
										<table class="table table-bordered table-grid1" id="seal-lain">
											<thead>
												<tr>
													<th class="text-center" width="20%">ITEMS</th>
													<th class="text-center" width="25%">SEGEL</th>
													<th class="text-center" width="20%">ITEMS</th>
													<th class="text-center" width="25%">SEGEL</th>
													<th class="text-center" width="10%">AKSI</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td><input type="text" name="jns_kiri[]" id="jns_kiri1" tabindex="28" class="form-control input-sm" /></td>
													<td><input type="text" name="segel_kiri[]" id="segel_kiri1" tabindex="28" class="form-control input-sm hitung" /></td>
													<td><input type="text" name="jns_kanan[]" id="jns_kanan1" tabindex="29" class="form-control input-sm" /></td>
													<td><input type="text" name="segel_kanan[]" id="segel_kanan1" tabindex="29" class="form-control input-sm hitung" /></td>
													<td class="text-center">
														<a class="btn btn-action btn-primary addRow jarak-kanan"><i class="fa fa-plus"></i></a>
														<span class="nosglo" data-row-count="1"></span>
													</td>
												</tr>
											</tbody>
										</table>
									</div> -->

									<!-- <div class="form-group row">
										<div class="col-sm-8">
											<label>Keterangan</label>
											<textarea id="catatan" name="catatan" tabindex="30" class="form-control">Saat serah terima, telah diperiksa semua segel dalam keadaan baik,tanpa cacat dan BBM diterima sesuai dengan spesifikasi dan volume tersebut diatas.</textarea>
										</div>
									</div> -->

									<div class="row">
										<div class="col-sm-12">
											<div class="pad bg-gray">
												<input type="hidden" name="act" value="<?php echo $action; ?>" />
												<input type="hidden" name="idr" value="<?php echo $idr; ?>" />
												<a href="<?php echo BASE_REFERER; ?>" tabindex="31" class="btn btn-default jarak-kanan">
													<i class="fa fa-reply jarak-kanan"></i> Kembali</a>
												<button type="submit" onclick="validateForm()" tabindex="32" class="btn btn-primary" name="btnSbmt" id="btnSbmt">
													<i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
											</div>
										</div>
									</div>
									<hr style="margin:5px 0" />
									<div class="clearfix">
										<div class="col-sm-12"><small>* Wajib Diisi</small></div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<div class="modal fade" id="loading_modal" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header bg-blue">
								<h4 class="modal-title">Loading Data ...</h4>
							</div>
							<div class="modal-body text-center modal-loading"></div>
						</div>
					</div>
				</div>
				<?php $con->close(); ?>
			</section>
			<?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
		</aside>
	</div>

	<style type="text/css">
		#table-grid3 td,
		#table-grid3 th {
			font-size: 11px;
			font-family: arial;
		}

		.table-grid1 td,
		.table-grid1 th {
			font-size: 14px;
			padding: 5px
		}
	</style>
	<!-- <script>
		$(document).ready(function() {
			$(".hitung").number(true, 0, ".", ",");
			$("form#gform").validationEngine('attach', {
				onValidationComplete: function(form, status) {
					if (status == true) {

						if ($("#oa_transportir").val() === "") {
							Swal.fire({
								icon: 'warning',
								title: 'Perhatian!',
								text: 'Field OA Transportir tidak boleh kosong.',
								confirmButtonText: 'OK'
							});
							return false; // Prevent form submission
						}

						if (confirm("Apakah anda yakin?")) {
							$("#loading_modal").modal({
								backdrop: "static"
							});
							form.validationEngine('detach');
							form.submit();
						}
					}
				}
			});


			$("form#gform").on("ifChecked", "#code_prd", function() {
				var nilai = $(this).val();
				$("#loading_modal").modal({
					backdrop: "static"
				});
				$.ajax({
					type: 'POST',
					url: "./__cek_data_pr_detil.php",
					data: {
						q1: nilai
					},
					cache: false,
					dataType: "json",
					success: function(data) {
						$("#signee_name").val(data.customer);
						$("#signee_addr").val(data.alamat_customer);
						$("#port_addr").val(data.alamat_survey);
						$("#produk").val(data.produk);
						$("#sistem_volume").html(data.volume);
						$("#id_terminal_addr").val(data.id_terminal);
						$("#terminal_addr").val(data.terminal);
						$("#bl1").val(data.bl1);
						$("#oa_penawaran").val(data.oa_kirim);
						$("#oa_disetujui").val(data.oa_disetujui);
						$("#tgl_etl").val(data.tgl_etl);
						$("#tgl_eta").val(data.tgl_eta);
						$("#terminal").val(data.terminal).trigger("change");
					}
				});
				$("#loading_modal").modal("hide");
			});

			$("select#code_pr").change(function() {
				if ($(this).val() != "" && $(this).val() != null) {
					$("#loading_modal").modal({
						backdrop: "static"
					});
					$("#signee_name, #signee_addr, #port_addr, #produk, #terminal_addr").val("");
					$("#terminal").val("").trigger("change");
					$.ajax({
						type: 'POST',
						url: "./__cek_data_pr_kapal.php",
						data: {
							q1: $(this).val()
						},
						cache: false,
						success: function(data) {
							$("#ket-po").html(data);
						}
					});
					$("#loading_modal").modal("hide");
				} else {
					$("#ket-po").html("");
					$("#signee_name, #signee_addr, #port_addr, #produk, #terminal_addr").val("");
					$("#terminal").val("").trigger("change");
				}
			});

			$("#seal-nomor").on("click", "a.addRow", function() {
				var tabel = $(this).parents("#seal-nomor");
				var rwTbl = tabel.find('tbody > tr.tank:last');
				var rwNom = parseInt($(this).data('rowCount'));
				var newId = parseInt(rwNom + 1);
				var nTank = tabel.find("tr").length - 2;
				$(this).data('rowCount', newId);

				var objTr = $("<tr>", {
					class: "tank"
				});
				var objTd1 = $("<td>").appendTo(objTr).html('<span class="jnstank1">' + nTank + 'S</span>');
				var objTd2 = $("<td>").appendTo(objTr);
				var objTd3 = $("<td>").appendTo(objTr).html('<span class="jnstank2">' + nTank + 'P</span>');
				var objTd4 = $("<td>").appendTo(objTr);
				var objTd5 = $("<td>", {
					class: "text-center"
				}).appendTo(objTr);
				objTd2.html('<input type="text" name="tank_kiri[]" id="tank_kiri' + newId + '" tabindex="23" class="form-control input-sm hitung" />');
				objTd4.html('<input type="text" name="tank_kanan[]" id="tank_kanan' + newId + '" tabindex="24" class="form-control input-sm hitung" />');
				objTd5.html('<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>');
				rwTbl.after(objTr);
				tabel.find(".jnstank1").each(function(i, v) {
					$(this).text(i + 1 + 'S');
				});
				tabel.find(".jnstank2").each(function(i, v) {
					$(this).text(i + 1 + 'P');
				});
				$("#tank_kiri" + newId + ", #tank_kanan" + newId).number(true, 0, ".", ",");
			});
			$("#seal-nomor").on("click", "a.hRow", function() {
				var tabel = $(this).parents("#seal-nomor");
				var jTbl = tabel.find("tr").length;
				var cRow = $(this).closest('tr');
				cRow.remove();
				tabel.find(".jnstank1").each(function(i, v) {
					$(this).text(i + 1 + 'S');
				});
				tabel.find(".jnstank2").each(function(i, v) {
					$(this).text(i + 1 + 'P');
				});
			});

			$("#seal-lain").on("click", "a.addRow", function() {
				var tabel = $(this).parents("#seal-lain");
				var rwTbl = tabel.find('tbody > tr:last');
				var rwNom = parseInt(rwTbl.find("span.nosglo").data('rowCount'));
				var newId = parseInt(rwNom + 1);

				var objTr = $("<tr>");
				var objTd1 = $("<td>").appendTo(objTr);
				var objTd2 = $("<td>").appendTo(objTr);
				var objTd3 = $("<td>").appendTo(objTr);
				var objTd4 = $("<td>").appendTo(objTr);
				var objTd5 = $("<td>", {
					class: "text-center"
				}).appendTo(objTr);
				objTd1.html('<input type="text" name="jns_kiri[]" id="jns_kiri' + newId + '" tabindex="28" class="form-control input-sm" />');
				objTd2.html('<input type="text" name="segel_kiri[]" id="segel_kiri' + newId + '" tabindex="28" class="form-control input-sm hitung" />');
				objTd3.html('<input type="text" name="jns_kanan[]" id="jns_kanan' + newId + '" tabindex="29" class="form-control input-sm" />');
				objTd4.html('<input type="text" name="segel_kanan[]" id="segel_kanan' + newId + '" tabindex="29" class="form-control input-sm hitung" />');
				objTd5.html('<a class="btn btn-action btn-primary addRow jarak-kanan"><i class="fa fa-plus"></i></a><a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a><span class="nosglo" data-row-count="' + newId + '"></span>');
				rwTbl.after(objTr);
				$("#segel_kiri" + newId + ", #segel_kanan" + newId).number(true, 0, ".", ",");
			});
			$("#seal-lain").on("click", "a.hRow", function() {
				var tabel = $(this).parents("#seal-lain");
				var jTbl = tabel.find("tr").length;
				if (jTbl > 2) {
					var cRow = $(this).closest('tr');
					cRow.remove();
				}
			});
		});
	</script> -->

	<script>
		$(document).ready(function() {
			$(".hitung").number(true, 0, ".", ",");

			$("form#gform").on('submit', function(e) {
				e.preventDefault(); // Mencegah submit form langsung

				// Validasi untuk setiap field
				if ($("#code_pr").val() === "") {
					Swal.fire({
						icon: 'warning',
						title: 'Perhatian!',
						text: 'Field Code PR tidak boleh kosong.',
						confirmButtonText: 'OK'
					});
					return false;
				}

				if ($("#oa_transportir").val() === "") {
					Swal.fire({
						icon: 'warning',
						title: 'Perhatian!',
						text: 'Field OA Transportir tidak boleh kosong.',
						confirmButtonText: 'OK'
					});
					return false;
				}

				if ($("#transportir").val() === "") {
					Swal.fire({
						icon: 'warning',
						title: 'Perhatian!',
						text: 'Field Shipping line tidak boleh kosong.',
						confirmButtonText: 'OK'
					});
					return false;
				}

				if ($("#kapten").val() === "") {
					Swal.fire({
						icon: 'warning',
						title: 'Perhatian!',
						text: 'Field Captain  tidak boleh kosong.',
						confirmButtonText: 'OK'
					});
					return false;
				}

				if ($("#tgl_etl").val() === "") {
					Swal.fire({
						icon: 'warning',
						title: 'Perhatian!',
						text: 'Field Tgl ETL tidak boleh kosong.',
						confirmButtonText: 'OK'
					});
					return false;
				}

				if ($("#jam_etl").val() === "") {
					Swal.fire({
						icon: 'warning',
						title: 'Perhatian!',
						text: 'Field Jam ETL tidak boleh kosong.',
						confirmButtonText: 'OK'
					});
					return false;
				}

				if ($("#tgl_eta").val() === "") {
					Swal.fire({
						icon: 'warning',
						title: 'Perhatian!',
						text: 'Field Tgl ETA tidak boleh kosong.',
						confirmButtonText: 'OK'
					});
					return false;
				}

				if ($("#jam_eta").val() === "") {
					Swal.fire({
						icon: 'warning',
						title: 'Perhatian!',
						text: 'Field Jam ETA tidak boleh kosong.',
						confirmButtonText: 'OK'
					});
					return false;
				}

				if ($("#vessel_name").val() === "") {
					Swal.fire({
						icon: 'warning',
						title: 'Perhatian!',
						text: 'Field Vessel Name tidak boleh kosong.',
						confirmButtonText: 'OK'
					});
					return false;
				}

				if ($("#tank_kiri1").val() === "") {
					Swal.fire({
						icon: 'warning',
						title: 'Perhatian!',
						text: 'Field Seal Number tidak boleh kosong.',
						confirmButtonText: 'OK'
					});
					return false;
				}

				// Tambahkan validasi lain di sini jika diperlukan


				// Jika semua validasi lolos, tanyakan konfirmasi penyimpanan
				Swal.fire({
					title: 'Apakah Anda yakin?',
					text: "Data akan disimpan.",
					icon: 'question',
					showCancelButton: true,
					confirmButtonText: 'Ya, simpan!',
					cancelButtonText: 'Batal'
				}).then((result) => {
					if (result.isConfirmed) {
						$("#loading_modal").modal({
							backdrop: "static"
						});
						// Submit form setelah validasi SweetAlert
						this.submit();
					}
				});
			});


			$("form#gform").on("ifChecked", "#code_prd", function() {
				var nilai = $(this).val();
				$("#loading_modal").modal({
					backdrop: "static"
				});
				$.ajax({
					type: 'POST',
					url: "./__cek_data_pr_detil.php",
					data: {
						q1: nilai
					},
					cache: false,
					dataType: "json",
					success: function(data) {
						$("#signee_name").val(data.customer);
						$("#signee_addr").val(data.alamat_customer);
						$("#port_addr").val(data.alamat_survey);
						$("#produk").val(data.produk);
						$("#sistem_volume").html(data.volume);
						$("#id_terminal_addr").val(data.id_terminal);
						$("#terminal_addr").val(data.terminal);
						$("#bl1").val(data.bl1);
						$("#oa_penawaran").val(data.oa_kirim);
						$("#oa_disetujui").val(data.oa_disetujui);
						$("#tgl_etl").val(data.tgl_etl);
						$("#tgl_eta").val(data.tgl_eta);
						$("#terminal").val(data.terminal).trigger("change");
					}
				});
				$("#loading_modal").modal("hide");
			});


			$("select#code_pr").change(function() {
				if ($(this).val() != "" && $(this).val() != null) {
					$("#loading_modal").modal({
						backdrop: "static"
					});
					$("#signee_name, #signee_addr, #port_addr, #produk, #terminal_addr").val("");
					$("#terminal").val("").trigger("change");
					$.ajax({
						type: 'POST',
						url: "./__cek_data_pr_kapal.php",
						data: {
							q1: $(this).val()
						},
						cache: false,
						success: function(data) {
							$("#ket-po").html(data);
						}
					});
					$("#loading_modal").modal("hide");
				} else {
					$("#ket-po").html("");
					$("#signee_name, #signee_addr, #port_addr, #produk, #terminal_addr").val("");
					$("#terminal").val("").trigger("change");
				}
			});

			$("#seal-nomor").on("click", "a.addRow", function() {
				var tabel = $(this).parents("#seal-nomor");
				var rwTbl = tabel.find('tbody > tr.tank:last');
				var rwNom = parseInt($(this).data('rowCount'));
				var newId = parseInt(rwNom + 1);
				var nTank = tabel.find("tr").length - 2;
				$(this).data('rowCount', newId);

				var objTr = $("<tr>", {
					class: "tank"
				});
				var objTd1 = $("<td>").appendTo(objTr).html('<span class="jnstank1">' + nTank + 'S</span>');
				var objTd2 = $("<td>").appendTo(objTr);
				var objTd3 = $("<td>").appendTo(objTr).html('<span class="jnstank2">' + nTank + 'P</span>');
				var objTd4 = $("<td>").appendTo(objTr);
				var objTd5 = $("<td>", {
					class: "text-center"
				}).appendTo(objTr);
				objTd2.html('<input type="text" name="tank_kiri[]" id="tank_kiri' + newId + '" tabindex="23" class="form-control input-sm hitung" />');
				objTd4.html('<input type="text" name="tank_kanan[]" id="tank_kanan' + newId + '" tabindex="24" class="form-control input-sm hitung" />');
				objTd5.html('<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>');
				rwTbl.after(objTr);
				tabel.find(".jnstank1").each(function(i, v) {
					$(this).text(i + 1 + 'S');
				});
				tabel.find(".jnstank2").each(function(i, v) {
					$(this).text(i + 1 + 'P');
				});
				$("#tank_kiri" + newId + ", #tank_kanan" + newId).number(true, 0, ".", ",");
			});

			$("#seal-nomor").on("click", "a.hRow", function() {
				var tabel = $(this).parents("#seal-nomor");
				var cRow = $(this).closest('tr');
				cRow.remove();
				tabel.find(".jnstank1").each(function(i, v) {
					$(this).text(i + 1 + 'S');
				});
				tabel.find(".jnstank2").each(function(i, v) {
					$(this).text(i + 1 + 'P');
				});
			});

			$("#seal-lain").on("click", "a.addRow", function() {
				var tabel = $(this).parents("#seal-lain");
				var rwTbl = tabel.find('tbody > tr:last');
				var rwNom = parseInt(rwTbl.find("span.nosglo").data('rowCount'));
				var newId = parseInt(rwNom + 1);

				var objTr = $("<tr>");
				objTr.append("<td><input type='text' name='jns_kiri[]' class='form-control input-sm'></td>");
				objTr.append("<td><input type='text' name='segel_kiri[]' class='form-control input-sm hitung'></td>");
				objTr.append("<td><input type='text' name='jns_kanan[]' class='form-control input-sm'></td>");
				objTr.append("<td><input type='text' name='segel_kanan[]' class='form-control input-sm hitung'></td>");
				objTr.append("<td class='text-center'><a class='btn btn-action btn-primary addRow jarak-kanan'><i class='fa fa-plus'></i></a><a class='btn btn-danger btn-action hRow'><i class='fa fa-times'></i></a><span class='nosglo' data-row-count='" + newId + "'></span></td>");
				rwTbl.after(objTr);
				$(".hitung").number(true, 0, ".", ",");
			});

			$("#seal-lain").on("click", "a.hRow", function() {
				var tabel = $(this).parents("#seal-lain");
				var cRow = $(this).closest('tr');
				if (tabel.find("tr").length > 2) {
					cRow.remove();
				}
			});
		});
	</script>


</body>

</html>
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
$idr 	= isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
$cek = "select a.id_po, a.id_pr, a.tanggal_po, a.id_transportir, a.catatan_transportir, b.nama_transportir, b.nama_suplier, c.nama_cabang, a.disposisi_po, a.po_approved, 
			a.nomor_po, d.nomor_pr, b.lokasi_suplier, a.ada_selisih, a.f_proses_selisih 
			from pro_po a join pro_master_transportir b on a.id_transportir = b.id_master 
			join pro_master_cabang c on a.id_wilayah = c.id_master join pro_pr d on a.id_pr = d.id_pr where a.id_po = '" . $idr . "'";
$row = $con->getRecord($cek);
$catatan = ($row['catatan_transportir']) ? $row['catatan_transportir'] : '&nbsp;';
$linkCetak1	= ACTION_CLIENT . '/purchase-order-cetak.php?' . paramEncrypt('idr=' . $idr);
$linkCetak2	= ACTION_CLIENT . '/purchase-order-cetak-spj.php?' . paramEncrypt('idr=' . $idr);
$linkCetak3	= ACTION_CLIENT . '/purchase-order-cetak-tanpa-ppn.php?' . paramEncrypt('idr=' . $idr);
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("myGrid", "jqueryUI", "formatNumber"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory . "/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
		<aside class="right-side">
			<section class="content-header">
				<h1>Purchase Order Detail</h1>
			</section>
			<section class="content">

				<?php if ($enk['idr'] !== '' && isset($enk['idr'])) { ?>
					<?php $flash->display(); ?>
					<div class="box box-primary">
						<div class="box-body">

							<div class="form-group row">
								<div class="col-sm-6">
									<div class="table-responsive">
										<table class="table no-border table-detail">
											<tr>
												<td width="70">Kode PO</td>
												<td width="10">:</td>
												<td><?php echo $row['nomor_po']; ?></td>
											</tr>
											<tr>
												<td width="70">Kode DR</td>
												<td width="10">:</td>
												<td><?php echo $row['nomor_pr']; ?></td>
											</tr>
											<tr>
												<td>Tanggal</td>
												<td>:</td>
												<td><?php echo tgl_indo($row['tanggal_po']); ?></td>
											</tr>
											<tr>
												<td>Transportir</td>
												<td>:</td>
												<td><?php echo $row['nama_suplier'] .
														($row['nama_transportir'] ? ' - ' . $row['nama_transportir'] : '') .
														($row['lokasi_suplier'] ? ', ' . $row['lokasi_suplier'] : '');
													?></td>
											</tr>
										</table>
									</div>
								</div>
								<div class="col-sm-6 col-sm-top">
									<div class="table-responsive" style="border:1px solid #ddd;">
										<table class="table no-border table-detail">
											<tr>
												<td colspan="2" style="background-color:#f4f4f4; border-bottom:1px solid #ddd;"><b>Kalkulasi OA</b></td>
											</tr>
											<tr>
												<td width="100">Wilayah</td>
												<td><select name="wiloa_po" id="wiloa_po" class="form-control input-po">
														<option></option>
														<?php $con->fill_select("a.id_master", "upper(concat(a.wilayah_angkut,'#',c.nama_kab,' ',b.nama_prov))", "pro_master_wilayah_angkut a join pro_master_provinsi b on a.id_prov = b.id_prov join pro_master_kabupaten c on a.id_kab = c.id_kab", "", "where a.is_active=1", "nama", false); ?>
													</select></td>
											</tr>
											<tr>
												<td>Volume</td>
												<td><select name="voloa_po" id="voloa_po" class="form-control input-po select2">
														<option></option>
														<?php $con->fill_select("volume_angkut", "volume_angkut", "pro_master_volume_angkut", "", "where is_active = 1", "", false); ?>
													</select></td>
											</tr>
											<tr>
												<td>Ongkos Angkut</td>
												<td><input type="text" name="ongoa_po" id="ongoa_po" class="form-control input-po" readonly /></td>
											</tr>
										</table>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-12">
									<form action="<?php echo ACTION_CLIENT . '/purchase-order-detail.php'; ?>" id="gform" name="gform" method="post" role="form">
										<?php require_once($public_base_directory . "/web/__get_data_po_transportir.php"); ?>
									</form>
								</div>
							</div>

						</div>
					</div>
				<?php } ?>

				<div class="modal fade" id="lcr_modal" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog modal-dialog-lg">
						<div class="modal-content">
							<div class="modal-header bg-blue">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">Informasi</h4>
							</div>
							<div class="modal-body"></div>
						</div>
					</div>
				</div>
				<div class="modal fade" id="truck_modal" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header bg-blue">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">Informasi</h4>
							</div>
							<div class="modal-body"></div>
						</div>
					</div>
				</div>
				<div class="modal fade" id="preview_modal" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header bg-blue">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">Peringatan</h4>
							</div>
							<div class="modal-body">
								<div id="preview_alert" class="text-center"></div>
							</div>
						</div>
					</div>
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
					<h4 class="modal-title">Loading Data ...</h4>
				</div>
				<div class="modal-body text-center modal-loading"></div>
			</div>
		</div>
	</div>

	<style type="text/css">
		h3.form-title {
			font-size: 18px;
			margin: 0 0 10px;
			font-weight: 700;
		}

		#table-long,
		#table-grid3,
		.table-detail {
			margin-bottom: 15px;
		}

		#table-grid3>tbody>tr>td,
		#table-grid3>thead>tr>th {
			font-size: 11px;
			font-family: arial;
		}

		.table-detail>thead>tr>th,
		.table-detail>tbody>tr>td {
			padding: 5px;
			font-size: 12px;
		}

		.input-po {
			padding: 5px;
			height: auto;
			font-size: 11px;
			font-family: arial;
		}

		.select2-search--dropdown .select2-search__field {
			font-family: arial;
			font-size: 11px;
			padding: 4px 3px;
		}

		.select2-results__option {
			font-family: arial;
			font-size: 11px;
		}
	</style>
	<script>
		$(document).ready(function() {
			$("#ongoa_po").number(true, 0, ".", ",");
			$(".hitung").number(true, 0, ".", ",");

			$("form#gform").on("click", "button:submit", function() {
				if (confirm("Apakah anda yakin?")) {
					var tombol = $(this).attr("id").split("btnSbmt");
					$("#tombol_klik").val(tombol[1]);
					$.ajax({
						type: 'POST',
						url: "./__cek_po_transportir.php",
						dataType: "json",
						data: $("#gform").serializeArray(),
						cache: false,
						success: function(data) {
							if (data.error) {
								swal.fire({
									icon: "warning",
									width: '350px',
									allowOutsideClick: false,
									html: '<p style="font-size:14px; font-family:arial;">' + data.error + '</p>'
								});
							} else {
								$("#loading_modal").modal({
									backdrop: 'static',
									keyboard: false
								});
								$("button[type='submit']").addClass("disabled");
								$("#gform").submit();
							}
						}
					});
					return false;
				} else return false;
			});

			$("#gform").on("click", "#table-grid3 button.dRow", function() {
				if (confirm("Anda akan menghapus data dalam list ini.\nApakah anda yakin?")) {
					var cRow = $(this).val();
					$("#loading_modal").modal({
						backdrop: "static"
					});
					$.ajax({
						type: 'POST',
						url: "./__del_po_transportir.php",
						data: {
							idr: cRow
						},
						cache: false,
						success: function(data) {
							$("#loading_modal").modal("hide");
							if (data == 'reload') {
								window.location.reload();
								return false;
							} else if (data == 'refresh') {
								window.location.href = '<?php echo BASE_URL_CLIENT . "/purchase-order.php"; ?>'
								return false;
							} else {
								return false;
							}
						}
					});
				}
			});

			$("#gform").on("click", "#table-grid3 button.upRow, #table-grid3 button.downRow", function() {
				var cRow = $(this).closest('tr');
				if ($(this).hasClass("upRow")) cRow.insertBefore(cRow.prev());
				else cRow.insertAfter(cRow.next());
				$("#table-grid3").find(".noFormula").each(function(i, v) {
					$(this).val(i + 1);
				});
			});

			$("#gform").on("click", "a.detLcr", function() {
				var cRow = $(this).data('idnya');
				$.ajax({
					type: 'POST',
					url: "./__get_info_lcr_customer.php",
					data: {
						q1: cRow
					},
					cache: false,
					success: function(data) {
						$("#lcr_modal").find(".modal-body").html(data);
						$("#lcr_modal").modal();
					}
				});
			});

			$("#gform").on("click", "a.detTruck", function() {
				var cRow = $(this).data('idnya');
				$.ajax({
					type: 'POST',
					url: "./__get_info_truck_transportir.php",
					data: {
						q1: $("select[name='dt4[" + cRow + "]']").val(),
						q2: $("input[name='dt4[" + cRow + "]']").val(),
						q3: $("input[name='ext_id_lcr[" + cRow + "]']").val()
					},
					cache: false,
					success: function(data) {
						$("#truck_modal").find(".modal-body").html(data);
						$("#truck_modal").modal();
					}
				});
			});

			$("select#wiloa_po").select2({
				placeholder: "Pilih salah satu",
				allowClear: true,
				templateResult: function(repo) {
					if (repo.loading) return repo.text;
					var text1 = repo.text.split("#");
					var $returnString = $('<span>' + text1[0] + '<br />' + text1[1].replace("KOTA", "").replace("KABUPATEN", "") + '</span>');
					return $returnString;
				},
				templateSelection: function(repo) {
					var text1 = repo.text.split("#");
					var $returnString = $('<span>' + text1[0] + ' ' + (text1[1] ? text1[1].replace("KOTA", "").replace("KABUPATEN", "") : '') + '</span>');
					return $returnString;
				},
			});
			$(".table-detail").on("change", "select#wiloa_po, select#voloa_po", getOngkosAngkut);

			function getOngkosAngkut() {
				var elmTa = $("input#transportir").val();
				var elmVa = $("select#voloa_po").val();
				var elmOa = $("select#wiloa_po").val();
				if (elmTa != "" && elmVa != "" && elmOa != "") {
					$("#loading_modal").modal();
					$.ajax({
						type: 'POST',
						url: "./__get_ongkos_angkut.php",
						data: {
							q1: elmTa,
							q2: elmOa,
							q3: elmVa
						},
						cache: false,
						success: function(data) {
							$("input#ongoa_po").val(data);
						}
					});
					$("#loading_modal").modal("hide");
				} else $("input#ongoa_po").val("");
			}

			var x, y, top, left, down;
			$("#table-long").mousedown(function(e) {
				if (e.target.nodeName != "INPUT" && e.target.nodeName != "SELECT") {
					down = true;
					x = e.pageX;
					y = e.pageY;
					top = $(this).scrollTop();
					left = $(this).scrollLeft();
				}
			});
			$("body").mousemove(function(e) {
				if (down) {
					var newX = e.pageX;
					var newY = e.pageY;
					$("#table-long").scrollTop(top - newY + y);
					$("#table-long").scrollLeft(left - newX + x);
				}
			});
			$("body").mouseup(function(e) {
				down = false;
			});
		});
	</script>
</body>

</html>
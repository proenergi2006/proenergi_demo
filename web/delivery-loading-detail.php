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
$id_role = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$cek = "select a.*, b.nama_terminal, b.tanki_terminal, b.lokasi_terminal, c.urut_segel, c.inisial_segel, c.stok_segel 
			from pro_po_ds a join pro_master_terminal b on a.id_terminal = b.id_master 
			join pro_master_cabang c on a.id_wilayah = c.id_master where a.id_ds = '" . $idr . "'";
$row = $con->getRecord($cek);
$catatan 	= ($row['catatan']) ? ($row['catatan']) : '&nbsp;';
$nom_segel 	= ($row['urut_segel']) ? $row['inisial_segel'] . "-" . str_pad($row['urut_segel'], 4, '0', STR_PAD_LEFT) : 'Tidak ada';
$terminal1 	= ($row['nama_terminal']) ? $row['nama_terminal'] : '';
$terminal2 	= ($row['tanki_terminal']) ? ' - ' . $row['tanki_terminal'] : '';
$terminal3 	= ($row['lokasi_terminal']) ? ', ' . $row['lokasi_terminal'] : '';
$terminal 	= $terminal1 . $terminal2 . $terminal3;
$linkCtk3	= ACTION_CLIENT . '/delivery-ba-cetak.php?' . paramEncrypt('idr=' . $idr);
$linkCtk1 	= ACTION_CLIENT . "/delivery-order-cetak.php?" . paramEncrypt("idr=" . $idr);
/*dengan code customer*/
$linkCtk2 	= ACTION_CLIENT . "/delivery-loading-cetak.php?" . paramEncrypt("idr=" . $idr . "&code=yes");
$linkCtk4 	= ACTION_CLIENT . "/delivery-loading-cetak.php?" . paramEncrypt("idr=" . $idr . "&code=no");
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
				<h1>Delivery Schedule Detail</h1>
			</section>
			<section class="content">

				<?php if ($enk['idr'] !== '' && isset($enk['idr'])) { ?>
					<?php $flash->display(); ?>
					<div class="row">
						<div class="col-sm-12">
							<div class="box box-primary">
								<div class="box-body">

									<table border="0" cellpadding="0" cellspacing="0" id="table-detail">
										<tr>
											<td width="100">Kode DS</td>
											<td width="10">:</td>
											<td><?php echo $row['nomor_ds']; ?></td>
										</tr>
										<tr>
											<td>Tanggal</td>
											<td>:</td>
											<td><?php echo tgl_indo($row['created_time']); ?></td>
										</tr>
										<tr>
											<td>Depo</td>
											<td>:</td>
											<td><?php echo $terminal; ?></td>
										</tr>
										<tr>
											<td>Stock Segel</td>
											<td>:</td>
											<td><?php echo $row['stok_segel']; ?></td>
										</tr>
										<tr>
											<td>No. Segel Terakhir</td>
											<td>:</td>
											<td><?php echo $nom_segel; ?></td>
										</tr>
									</table>

									<div class="row">
										<div class="col-sm-12">
											<form action="<?php echo ACTION_CLIENT . '/delivery-loading.php'; ?>" id="gform" name="gform" method="post" role="form">
												<?php
												if ($row['is_loco'] == 0)
													require_once($public_base_directory . "/web/__get_data_ds_po.php");
												else if ($row['is_loco'] == 1)
													require_once($public_base_directory . "/web/__get_data_ds_loco.php");
												?>
											</form>
										</div>
									</div>

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
		#table-long,
		#table-detail,
		#table-grid3 {
			margin-bottom: 15px;
		}

		#table-grid3 td,
		#table-grid3 th {
			font-size: 11px;
			font-family: arial;
		}

		#table-detail td,
		.table-detail-pr td {
			padding-bottom: 3px;
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
			$(".hitung").number(true, 0, ".", ",");
			$("form#gform").on("click", "button:submit", function() {
				if (confirm("Apakah anda yakin?")) {
					$("#loading_modal").modal({
						backdrop: "static"
					});
					$.ajax({
						type: 'POST',
						url: "./__cek_delivery_loading.php",
						dataType: "json",
						data: $("#gform").serializeArray(),
						cache: false,
						success: function(data) {
							if (data.error) {
								$("#loading_modal").modal("hide");
								$("#loading_modal").on("hidden.bs.modal", function() {
									$("#preview_modal").find("#preview_alert").html(data.error);
									$("#preview_modal").modal();
									return false;
								});
							} else {
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
						url: "./__del_ds_list.php",
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
								window.location.href = '<?php echo BASE_URL_CLIENT . "/delivery-loading.php"; ?>'
								return false;
							} else {
								return false;
							}
						}
					});
				}
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
						q1: $("select[name='dt11[" + cRow + "]']").val(),
						q2: $("input[name='dt11[" + cRow + "]']").val(),
						q3: $("input[name='ext_id_lcr[" + cRow + "]']").val()
					},
					cache: false,
					success: function(data) {
						$("#truck_modal").find(".modal-body").html(data);
						$("#truck_modal").modal();
					}
				});
			});

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
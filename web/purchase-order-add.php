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
$seswil	= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$section = "Tambah Data";
//$con->fill_select("id_pr","nomor_pr","pro_pr",$rsm,"where disposisi_pr = 6 and id_wilayah = '".$seswil."'","id_pr desc, disposisi_pr",false); 
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
				<h1><?php echo $section . " PO"; ?></h1>
			</section>
			<section class="content">

				<?php $flash->display(); ?>
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
					</div>
					<div class="box-body">
						<form action="<?php echo ACTION_CLIENT . '/purchase-order.php'; ?>" id="gform" name="gform" method="post" class="form-horizontal" role="form">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">Transportir *</label>
										<div class="col-md-8">
											<select id="transportir" name="transportir" class="form-control select2" required style="width:100%;">
												<option></option>
												<?php $con->fill_select("id_master", "concat(nama_suplier,' - ',nama_transportir,', ',lokasi_suplier)", "pro_master_transportir", $rsm['id_transportir'], "where is_active=1 and tipe_angkutan in (1,3)", "id_master", false); ?>
											</select>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">Delivery Request *</label>
										<div class="col-md-8">
											<select id="code_pr" name="code_pr" class="form-control select2" required style="width:100%;">
												<option></option>
												<?php
												$sOpt = "
													select a.id_pr as id, a.nomor_pr as nama, b.jum_pr, c.jum_po 
													from pro_pr a 
													join (
														select count(id_prd) as jum_pr, id_pr from pro_pr_detail where pr_mobil = 1 and is_approved = 1 group by id_pr
													) b on a.id_pr = b.id_pr
													left join (
														select a.id_pr, count(b.id_pod) as jum_po 
														from pro_po a 
														join pro_po_detail b on a.id_po = b.id_po 
														join pro_pr_detail c on b.id_prd = c.id_prd 
														where c.pr_mobil = 1
														group by a.id_pr
													) c on a.id_pr = c.id_pr 
													where a.disposisi_pr = 7 and a.id_wilayah = '" . $seswil . "' 
														and (c.jum_po is null or b.jum_pr > c.jum_po) 
														and a.tanggal_pr > '2024-01-01'
													order by a.id_pr desc
												";
												$rOpt = $con->getResult($sOpt);
												if (count($rOpt) > 0) {
													foreach ($rOpt as $datx) {
														echo '<option value="' . $datx['id'] . '">' . $datx['nama'] . '</option>';
													}
												}
												?>
											</select>
										</div>
									</div>
								</div>
							</div>
							<div id="ket-po"></div>

							<hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

							<div style="margin-bottom:15px;">
								<input type="hidden" name="act" value="<?php echo $action; ?>" />
								<input type="hidden" name="idr" value="<?php echo $idr; ?>" />
								<a href="<?php echo BASE_URL_CLIENT . '/purchase-order.php'; ?>" class="btn btn-default jarak-kanan" style="min-width:90px;">
									<i class="fa fa-reply jarak-kanan"></i> Kembali</a>
								<button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">
									<i class="fa fa-save jarak-kanan"></i> Simpan</button>
							</div>

							<p><small>* Wajib Diisi</small></p>
						</form>
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
		#table-grid3 {
			margin-bottom: 15px;
		}

		#table-grid3 td,
		#table-grid3 th {
			font-size: 11px;
			font-family: arial;
		}
	</style>
	<script>
		$(document).ready(function() {
			var formValidasiCfg = {
				submitHandler: function(form) {
					var ceknya = 0;
					if ($(".chkp").length > 0) {
						$(".chkp").each(function(i, v) {
							ceknya = ($(v).is(":checked") ? ceknya + 1 : ceknya);
						});
					}

					if ($("#cekkolnup").is(":checked") && $("#nup_fee").val() == "") {
						$.validator.showErrorField('nup_fee', "Kolom ini belum diisi atau dipilih");
						setErrorFocus($("#nup_fee"), $("form#gform"), false);
					} else if ($(".chkp").length > 0 && ceknya == 0) {
						swal.fire({
							icon: "warning",
							width: '350px',
							allowOutsideClick: false,
							html: '<p style="font-size:14px; font-family:arial;">Belum ada data PR yg dipilih</p>'
						});
					} else {
						$("#loading_modal").modal({
							backdrop: 'static',
							keyboard: false
						});
						form.submit();
					}
				}
			};
			$("form#gform").validate($.extend(true, {}, config.validation, formValidasiCfg));

			$(".hitung").number(true, 0, ",", ".");

			$("#gform").on("ifChecked", "#cekAll", function() {
				$(".chkp").iCheck("check");
			}).on("ifUnchecked", "#cekAll", function() {
				$(".chkp").iCheck("uncheck");
			});

			$("select#code_pr").change(function() {
				if ($(this).val() != "" && $(this).val() != null) {
					$("#loading_modal").modal();
					$.ajax({
						type: 'POST',
						url: "./__get_data_pr.php",
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
				}
			});
		});
	</script>
</body>

</html>
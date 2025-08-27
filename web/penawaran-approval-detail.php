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
$idk 	= htmlspecialchars($enk["idk"], ENT_QUOTES);

$date_min_2_months = date('Y-m-d', strtotime("-2 months"));

$sql = "
		select a.*, b.nama_customer, b.top_payment, b.status_customer, c.fullname, d.nama_cabang, e.jenis_produk, e.merk_dagang, 
		f.harga_normal, f.harga_sm, f.harga_om, f.harga_coo, f.harga_ceo, g.nama_area,  
		i.harga_normal as harga_normal_new, i.harga_sm as harga_sm_new, i.harga_om as harga_om_new, i.harga_coo as harga_coo_new, i.harga_ceo as harga_ceo_new,
		if(a.flag_approval = 0 && a.flag_disposisi > 0, 1, 0) as position,
		CASE 
		WHEN j.id_penawaran IS NOT NULL THEN 'YA'
		WHEN a.flag_disposisi = 0 THEN '-'
		ELSE '-'  
		END AS penawaran_status 
		from pro_penawaran a 
		join pro_customer b on a.id_customer = b.id_customer 
		join acl_user c on b.id_marketing = c.id_user 
		join pro_master_cabang d on a.id_cabang = d.id_master 
		join pro_master_produk e on a.produk_tawar = e.id_master 
		join pro_master_area g on a.id_area = g.id_master 
		left join pro_master_harga_minyak f on a.masa_awal = f.periode_awal and a.masa_akhir = f.periode_akhir and a.id_area = f.id_area and a.pbbkb_tawar = f.pajak and f.is_approved = 1 and a.produk_tawar = f.produk 
		left join pro_master_harga_minyak i on a.masa_awal = i.periode_awal and a.masa_akhir = i.periode_akhir and a.id_area = i.id_area and i.pajak = 1 and i.is_approved = 1 and a.produk_tawar = i.produk 
		LEFT JOIN (
		SELECT id_penawaran
		FROM pro_po_customer
		GROUP BY id_penawaran
	) j ON a.id_penawaran = j.id_penawaran 
		where 1=1 and a.id_customer = '" . $idr . "' 
		order by a.created_time desc
	";
$rsms = $con->getResult($sql);

$sqlOtherCost = "select keterangan, nominal 
                 FROM pro_other_cost_detail 
                 WHERE id_penawaran = '" . $idk . "'";
$rsmOtherCost = $con->getResult($sqlOtherCost);


?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory . "/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
		<aside class="right-side">
			<section class="content-header">
				<h1>Detil Approval Penawaran</h1>
			</section>
			<section class="content">

				<?php if ($enk['idr'] !== '' && isset($enk['idr'])) { ?>
					<?php $flash->display(); ?>
					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" class="active">
							<a href="#form-approval" aria-controls="form-approval" role="tab" data-toggle="tab">Form Approval</a>
						</li>
						<li role="presentation" class="">
							<a href="#data-approval" aria-controls="data-approval" role="tab" data-toggle="tab">Data Penawaran</a>
						</li>
						<li role="presentation" class="">
							<a href="#history-data-approval" aria-controls="history-data-approval" role="tab" data-toggle="tab">History Approval Penawaran</a>
						</li>
					</ul>
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="form-approval">
							<div class="row">
								<div class="col-sm-12">
									<div class="box box-primary">
										<div class="box-body">
											<?php require_once($public_base_directory . "/web/penawaran-approval-detail-data.php"); ?>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div role="tabpanel" class="tab-pane" id="data-approval">
							<?php require_once($public_base_directory . "/web/penawaran-history-data.php"); ?>
						</div>

						<div role="tabpanel" class="tab-pane" id="history-data-approval">
							<?php require_once($public_base_directory . "/web/penawaran-history-approval.php"); ?>
						</div>

					</div>

				<?php } ?>
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
		.table {
			margin-bottom: 10px;
		}

		.table>tbody>tr>td {
			padding: 5px;
		}

		h3.form-title {
			font-size: 18px;
			margin: 0 0 10px;
			font-weight: 700;
		}

		.table-summary>tbody>tr>td {
			padding: 3px 5px;
		}
	</style>
	<script>
		$(document).ready(function() {
			$(window).on("load resize", function() {
				if ($(this).width() < 977) {
					$(".vertical-tab").addClass("collapsed-box");
					$(".vertical-tab").find(".box-tools").show();
					$(".vertical-tab > .vertical-tab-body").hide();
				} else {
					$(".vertical-tab").removeClass("collapsed-box");
					$(".vertical-tab").find(".box-tools").hide();
					$(".vertical-tab > .vertical-tab-body").show();
				}
			});

			var formValidasiCfg = {
				submitHandler: function(form) {
					$("#loading_modal").modal({
						keyboard: false,
						backdrop: 'static'
					});

					if ($("#cekkolnup").is(":checked") && $("#nup_fee").val() == "") {
						$("#loading_modal").modal("hide");
						$.validator.showErrorField('nup_fee', "Kolom ini belum diisi atau dipilih");
						setErrorFocus($("#nup_fee"), $("form#gform"), false);
					} else {
						form.submit();
					}
				}
			};
			$("form#gform").validate($.extend(true, {}, config.validation, formValidasiCfg));

		});
	</script>
</body>

</html>
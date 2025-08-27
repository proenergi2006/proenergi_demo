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
$id_dsd 	= isset($enk["id_dsd"]) ? htmlspecialchars($enk["id_dsd"], ENT_QUOTES) : '';
$id_penawaran 	= isset($enk["id_penawaran"]) ? htmlspecialchars($enk["id_penawaran"], ENT_QUOTES) : '';
$id_role = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$refund = 0;
$total_refund = 0;
$datenow = date("Y-m-d");
$sql = "SELECT a.*, a.id_refund as id_refundnya, a.id_dsd as id_dsdnya, a.id_invoice as id_invoicenya, a.total_refund, a.paid_by, a.tgl_bayar, a.disposisi, i.nama_customer, i.kode_pelanggan, i.jenis_payment, i.top_payment, i.id_customer as id_customernya, e.alamat_survey, f.nama_prov, g.nama_kab, j.fullname, h.nomor_poc, h.tanggal_poc, h.id_poc as id_pocnya, b.volume_po, k.refund_tawar, l.nama_area, m.wilayah_angkut, k.id_penawaran, ppdd.tanggal_delivered, n.no_invoice, n.tgl_invoice, n.tgl_invoice_dikirim, (SELECT SUM(vol_kirim) FROM pro_invoice_admin_detail WHERE id_invoice=a.id_invoice) as total_vol_invoice
from pro_refund a 
join pro_po_ds_detail ppdd on ppdd.id_dsd = a.id_dsd
join pro_po_detail b on ppdd.id_pod = b.id_pod
join pro_pr_detail c on ppdd.id_prd = c.id_prd 
join pro_po_customer_plan d on ppdd.id_plan = d.id_plan 
join pro_customer_lcr e on d.id_lcr = e.id_lcr
join pro_master_provinsi f on e.prov_survey = f.id_prov 
join pro_master_kabupaten g on e.kab_survey = g.id_kab
join pro_po_customer h on d.id_poc = h.id_poc 
join pro_customer i on h.id_customer = i.id_customer 
join acl_user j on i.id_marketing = j.id_user 
join pro_penawaran k on h.id_penawaran = k.id_penawaran	
join pro_master_area l on k.id_area = l.id_master 
join pro_master_wilayah_angkut m on e.id_wil_oa = m.id_master and e.prov_survey = m.id_prov and e.kab_survey = m.id_kab
join pro_invoice_admin n on a.id_invoice = n.id_invoice 
where a.id_dsd='" . $id_dsd . "'";
$result 	= $con->getRecord($sql);
$total_refund = $result['refund_tawar'] * $result['total_vol_invoice'];

$tgl_invoice_dikirim = tgl_indo($result['tgl_invoice_dikirim']);
$tgl_invoice = $result['tgl_invoice'];
$nomor_invoice = $result['no_invoice'];

$sql_1 = "SELECT * FROM pro_invoice_admin WHERE id_invoice = '" . $result['id_invoicenya'] . "'";
$row_1 = $con->getRecord($sql_1);
if (($row_1['total_invoice'] == $row_1['total_bayar']) || $row_1['is_lunas'] == '1') {
	$sql_bayar_1 = "SELECT MAX(tgl_bayar) as tanggal_bayar FROM pro_invoice_admin_detail_bayar WHERE id_invoice='" . $result['id_invoicenya'] . "'";
	$row_bayar_1 = $con->getRecord($sql_bayar_1);
	$status_invoice_1 = "Lunas";
	$date_payment = tgl_indo($row_bayar_1['tanggal_bayar']);
} else {
	$sql_bayar_1 = "SELECT MAX(tgl_bayar) as tanggal_bayar FROM pro_invoice_admin_detail_bayar WHERE id_invoice='" . $result['id_invoicenya'] . "'";
	$row_bayar_1 = $con->getRecord($sql_bayar_1);
	$date_payment = tgl_indo($row_bayar_1['tanggal_bayar']);
	$status_invoice_1 = "Not Yet";
}

$due_date_indo = tgl_indo(date('Y-m-d', strtotime($result['tgl_invoice_dikirim'] . "+" . $result['top_payment'] . " days")));
$due_date = date('Y-m-d', strtotime($result['tgl_invoice_dikirim'] . "+" . $result['top_payment'] . " days"));

$week1 = 0;
$week2 = 0;
$week3 = 0;
$week4 = 0;
$week5 = 0;
$week6 = 0;
$week7 = 0;

if ($status_invoice_1 == "Lunas") {
	$due_date_week2 = date('Y-m-d', strtotime($due_date . "+" . "14 days"));
	$due_date_week3 = date('Y-m-d', strtotime($due_date_week2 . "+" . "7 days"));
	$due_date_week4 = date('Y-m-d', strtotime($due_date_week3 . "+" . "10 days"));
	$due_date_week5 = date('Y-m-d', strtotime($due_date_week4 . "+" . "14 days"));
	$due_date_week6 = date('Y-m-d', strtotime($due_date_week5 . "+" . "15 days"));
	// $due_date_week7 = date('Y-m-d', strtotime($due_date_week6 . "+" . "1 days"));

	if ($row_bayar_1['tanggal_bayar'] <= $due_date) {
		$week1 += round(($total_refund * 100) / 100);
		$week2 += 0;
		$week3 += 0;
		$week4 += 0;
		$week5 += 0;
		$week6 += 0;
		$week7 += 0;
		$total_refund_fix = $week1;
		$persen = 100;
	} elseif ($row_bayar_1['tanggal_bayar'] > $due_date && $row_bayar_1['tanggal_bayar'] <= $due_date_week2) {
		$week1 += 0;
		$week2 += round(($total_refund * 95) / 100);
		$week3 += 0;
		$week4 += 0;
		$week5 += 0;
		$week6 += 0;
		$week7 += 0;
		$total_refund_fix = $week2;
		$persen = 95;
	} elseif ($row_bayar_1['tanggal_bayar'] > $due_date_week2 && $row_bayar_1['tanggal_bayar'] <= $due_date_week3) {
		$week1 += 0;
		$week2 += 0;
		$week3 += round(($total_refund * 85) / 100);
		$week4 += 0;
		$week5 += 0;
		$week6 += 0;
		$week7 += 0;
		$total_refund_fix = $week3;
		$persen = 85;
	} elseif ($row_bayar_1['tanggal_bayar'] > $due_date_week3 && $row_bayar_1['tanggal_bayar'] <= $due_date_week4) {
		$week1 += 0;
		$week2 += 0;
		$week3 += 0;
		$week4 += round(($total_refund * 75) / 100);
		$week5 += 0;
		$week6 += 0;
		$week7 += 0;
		$total_refund_fix = $week4;
		$persen = 75;
	} elseif ($row_bayar_1['tanggal_bayar'] > $due_date_week4 && $row_bayar_1['tanggal_bayar'] <= $due_date_week5) {
		$week1 += 0;
		$week2 += 0;
		$week3 += 0;
		$week4 += 0;
		$week5 += round(($total_refund * 65) / 100);
		$week6 += 0;
		$week7 += 0;
		$total_refund_fix = $week5;
		$persen = 65;
	} elseif ($row_bayar_1['tanggal_bayar'] > $due_date_week5 && $row_bayar_1['tanggal_bayar'] <= $due_date_week6) {
		$week1 += 0;
		$week2 += 0;
		$week3 += 0;
		$week4 += 0;
		$week5 += 0;
		$week6 += round(($total_refund * 50) / 100);
		$week7 += 0;
		$total_refund_fix = $week6;
		$persen = 50;
	} elseif ($row_bayar_1['tanggal_bayar'] > $due_date_week6) {
		$week1 += 0;
		$week2 += 0;
		$week3 += 0;
		$week4 += 0;
		$week5 += 0;
		$week6 += 0;
		$week7 += round(($total_refund * 0) / 100);
		$total_refund_fix = $week7;
		$persen = 0;
	}
} else {
	$due_date_week2 = date('Y-m-d', strtotime($due_date . "+" . "14 days"));
	$due_date_week3 = date('Y-m-d', strtotime($due_date_week2 . "+" . "7 days"));
	$due_date_week4 = date('Y-m-d', strtotime($due_date_week3 . "+" . "10 days"));
	$due_date_week5 = date('Y-m-d', strtotime($due_date_week4 . "+" . "14 days"));
	$due_date_week6 = date('Y-m-d', strtotime($due_date_week5 . "+" . "15 days"));
	// $due_date_week7 = date('Y-m-d', strtotime($due_date_week6 . "+" . "1 days"));

	if ($datenow <= $due_date) {
		$week1 += round(($total_refund * 100) / 100);
		$week2 += 0;
		$week3 += 0;
		$week4 += 0;
		$week5 += 0;
		$week6 += 0;
		$week7 += 0;
		$total_refund_fix = $week1;
		$persen = 100;
	} elseif ($datenow > $due_date && $datenow <= $due_date_week2) {
		$week1 += 0;
		$week2 += round(($total_refund * 95) / 100);
		$week3 += 0;
		$week4 += 0;
		$week5 += 0;
		$week6 += 0;
		$week7 += 0;
		$total_refund_fix = $week2;
		$persen = 95;
	} elseif ($datenow > $due_date_week2 && $datenow <= $due_date_week3) {
		$week1 += 0;
		$week2 += 0;
		$week3 += round(($total_refund * 85) / 100);
		$week4 += 0;
		$week5 += 0;
		$week6 += 0;
		$week7 += 0;
		$total_refund_fix = $week3;
		$persen = 85;
	} elseif ($datenow > $due_date_week3 && $datenow <= $due_date_week4) {
		$week1 += 0;
		$week2 += 0;
		$week3 += 0;
		$week4 += round(($total_refund * 75) / 100);
		$week5 += 0;
		$week6 += 0;
		$week7 += 0;
		$total_refund_fix = $week4;
		$persen = 75;
	} elseif ($datenow > $due_date_week4 && $datenow <= $due_date_week5) {
		$week1 += 0;
		$week2 += 0;
		$week3 += 0;
		$week4 += 0;
		$week5 += round(($total_refund * 65) / 100);
		$week6 += 0;
		$week7 += 0;
		$total_refund_fix = $week5;
		$persen = 65;
	} elseif ($datenow > $due_date_week5 && $datenow <= $due_date_week6) {
		$week1 += 0;
		$week2 += 0;
		$week3 += 0;
		$week4 += 0;
		$week5 += 0;
		$week6 += round(($total_refund * 50) / 100);
		$week7 += 0;
		$total_refund_fix = $week6;
		$persen = 50;
	} elseif ($datenow > $due_date_week6) {
		$week1 += 0;
		$week2 += 0;
		$week3 += 0;
		$week4 += 0;
		$week5 += 0;
		$week6 += 0;
		$week7 += round(($total_refund * 0) / 100);
		$total_refund_fix = $week7;
		$persen = 0;
	}
}

if ($total_refund_fix == 0) {
	$status_refund = "HANGUS";
} else {
	if ($result['disposisi'] == 2) {
		$status_refund = "PAID By " . ucwords($result['paid_by']) . " " . tgl_indo($result['tgl_bayar']);
	} elseif ($result['disposisi'] == 3) {
		$status_refund = "CLOSED By " . ucwords($result['closed_by']) . " " . tgl_indo($result['closed_date']);
	} else {
		$status_refund = "PROGRESS";
	}
}

$sql_penerima_refund = "SELECT a.*, b.* FROM pro_poc_penerima_refund a JOIN pro_master_penerima_refund b ON a.penerima_refund=b.id WHERE a.id_poc = '" . $result['id_pocnya'] . "' ORDER BY is_ceo DESC";
$penerima_refund = $con->getResult($sql_penerima_refund);

if (count($penerima_refund) > 0) {
	foreach ($penerima_refund as $key) {
		$is_bm = $key['is_bm'];
		$is_ceo = $key['is_ceo'];

		if ($is_bm == 1) {
			if ($is_ceo == 1) {
				if ($status_invoice_1 == "Lunas") {
					$status_penerima_refund = "approved";
				} else {
					$status_penerima_refund = "<span style='color:red;'>Invoice belum LUNAS, Refund tidak dapat di proses</span>";
				}
			} else {
				$status_penerima_refund = "<span style='color:red;'>Penerima Refund - " . ucwords($key['nama']) . " Belum di approve CEO</span>";
			}
		} else {
			$status_penerima_refund = "<span style='color:red;'>Penerima Refund - " . ucwords($key['nama']) . " Belum di approve BM</span>";
		}
	}
} else {
	$status_penerima_refund = "<span style='color:red;'>Belum ada data penerima refund pada PO Customer, Refund tidak dapat di proses</span>";
}

// echo json_encode($penerima_refund);
// die();

?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("myGrid", "formatNumber", "jqueryUI"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory . "/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
		<aside class="right-side">
			<section class="content-header">
				<h1>Detail Refund</h1>
			</section>
			<section class="content">
				<?php $flash->display(); ?>
				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active">
						<a href="#form-approval" aria-controls="form-approval" role="tab" data-toggle="tab">Form Approval</a>
					</li>
					<li role="presentation" class="">
						<a href="#data-penawaran" aria-controls="data-penawaran" role="tab" data-toggle="tab">Data Penawaran</a>
					</li>
					<li role="presentation" class="">
						<a href="#data-poc" aria-controls="data-poc" role="tab" data-toggle="tab">Data PO Customer</a>
					</li>
				</ul>
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="form-approval">
						<div class="row">
							<div class="col-md-12">
								<div class="box box-primary">
									<div class="box-body">
										<?php require_once($public_base_directory . "/web/refund-pembayaran-detail-data.php"); ?>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div role="tabpanel" class="tab-pane" id="data-penawaran">
						<div class="row">
							<div class="box box-primary">
								<div class="box-body">
									<?php
									require_once($public_base_directory . "/web/__refund_get_data_penawaran.php");
									?>
								</div>
							</div>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="data-do">

					</div>
					<div role="tabpanel" class="tab-pane" id="data-poc">
						<div class="row">
							<div class="col-sm-12">
								<div class="box box-primary">
									<div class="box-body">
										<?php require_once($public_base_directory . "/web/__refund_get_data_po.php"); ?>
									</div>
								</div>
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
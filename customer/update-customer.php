<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

// require($public_base_directory."/libraries/helper/botdetect/lib/botdetect.php");
require($public_base_directory . "/customer/botdetect.php");

$ExampleCaptcha = new Captcha("ExampleCaptcha");
$ExampleCaptcha->UserInputID = "CaptchaCode";
$ExampleCaptcha->SoundEnabled = true;
$ExampleCaptcha->ReloadEnabled = true;
$ExampleCaptcha->ImageWidth = 230;
$Captcha = $ExampleCaptcha->Html();

$con 	= new Connection();
$flash	= new FlashAlerts;
$enk  	= decode($_SERVER['REQUEST_URI']);
$idr 	= htmlspecialchars($enk["idr"], ENT_QUOTES);
$idk 	= htmlspecialchars($enk["idk"], ENT_QUOTES);
$token 	= htmlspecialchars($enk["token"], ENT_QUOTES);

$sql = "select a.id_customer, a.id_marketing, a.nama_customer, a.print_product, a.alamat_customer, a.prov_customer, a.kab_customer, a.telp_customer, a.fax_customer, a.email_customer,
			a.website_customer, a.tipe_bisnis, a.tipe_bisnis_lain, a.ownership, a.ownership_lain, a.need_update, a.count_update, a.status_customer, a.fix_customer_since, a.induk_perusahaan, 
			a.kecamatan_customer, a.kelurahan_customer, d.kecamatan_billing, d.kelurahan_billing, b.pic_fuelman_name, b.pic_fuelman_position, 
			b.pic_fuelman_telp, b.pic_fuelman_mobile, b.pic_fuelman_email, b.invoice_delivery_addr_primary, e.operational_hour_from, e.operational_hour_to, b.invoice_delivery_addr_secondary, 
			product_delivery_address,d.calculate_method, e.supply_shceme, e.specify_product, e.volume_per_month, e.nico, d.bank_name, d.bank_address, d.curency, d.account_number, 
			d.credit_facility, d.creditor,
			a.fix_customer_redate, a.top_payment, a.lastupdate_time, a.lastupdate_ip, a.lastupdate_by, b.pic_decision_name, b.pic_decision_position, b.pic_decision_telp, 
			b.pic_decision_mobile, b.pic_decision_email, b.pic_ordering_name, b.pic_ordering_position, b.pic_ordering_telp, b.pic_ordering_mobile, b.pic_ordering_email, 
			b.pic_billing_name, b.pic_billing_position, b.pic_billing_telp, b.pic_billing_mobile, b.pic_billing_email, b.pic_invoice_name, b.pic_invoice_position, 
			b.pic_invoice_telp, b.pic_invoice_mobile, b.pic_invoice_email, a.nomor_sertifikat, a.nomor_sertifikat_file, a.nomor_npwp, a.nomor_npwp_file, a.nomor_siup, 
			a.nomor_siup_file, a.nomor_tdp, a.nomor_tdp_file, a.dokumen_lainnya, a.dokumen_lainnya_file, d.email_billing, d.alamat_billing, d.prov_billing, d.kab_billing, d.telp_billing, d.fax_billing, 
			d.payment_schedule, d.payment_schedule_other, d.payment_method, d.payment_method_other, d.invoice, d.ket_extra, e.logistik_area, e.logistik_bisnis, e.logistik_env, 
			e.logistik_env_other, e.logistik_storage, e.logistik_storage_other, e.logistik_hour, e.logistik_hour_other, e.logistik_volume, e.logistik_volume_other, 
			e.logistik_quality, e.logistik_quality_other, e.logistik_truck, e.logistik_truck_other, f.nama_prov as propinsi_customer, g.nama_kab as kabupaten_customer, 
			h.nama_prov as propinsi_payment, i.nama_kab as kabupaten_payment, j.token_verification, j.is_evaluated, j.is_approved, a.jenis_payment, a.jenis_net, j.disposisi_result, j.legal_result, 
			j.finance_result, j.logistik_result, j.is_evaluated,
			a.postalcode_customer, d.postalcode_billing, a.credit_limit_diajukan, a.credit_limit, e.desc_stor_fac, e.desc_condition
			from pro_customer a left join pro_customer_contact b on a.id_customer = b.id_customer 
			left join pro_customer_payment d on a.id_customer = d.id_customer left join pro_customer_logistik e on a.id_customer = e.id_customer 
			left join pro_master_provinsi f on a.prov_customer = f.id_prov left join pro_master_kabupaten g on a.kab_customer = g.id_kab 
			left join pro_master_provinsi h on d.prov_billing = h.id_prov left join pro_master_kabupaten i on d.kab_billing = i.id_kab 
			left join pro_customer_verification j on a.id_customer = j.id_customer where a.id_customer = '" . $idr . "' and j.id_verification = '" . $idk . "'";
$rsm = $con->getRecord($sql);

$act = BASE_URL . "/web/action/customer-update.php";

$base_directory	= $public_base_directory . "/files/uploaded_user/images";
$file_path_sert	= $base_directory . "/sert_file" . $idr . "_" . $rsm['nomor_sertifikat_file'];
$file_path_npwp	= $base_directory . "/npwp_file" . $idr . "_" . $rsm['nomor_npwp_file'];
$file_path_siup	= $base_directory . "/siup_file" . $idr . "_" . $rsm['nomor_siup_file'];
$file_path_tdpn	= $base_directory . "/tdp_file" . $idr . "_" . $rsm['nomor_tdp_file'];
$file_path_dokumen_lainnya = $base_directory . "/dokumen_lainnya_file" . $idr . "_" . $rsm['dokumen_lainnya_file'];

$extIkon1 	= strtolower(substr($rsm['nomor_sertifikat_file'], strrpos($rsm['nomor_sertifikat_file'], '.')));
$extIkon2 	= strtolower(substr($rsm['nomor_npwp_file'], strrpos($rsm['nomor_npwp_file'], '.')));
$extIkon3 	= strtolower(substr($rsm['nomor_siup_file'], strrpos($rsm['nomor_siup_file'], '.')));
$extIkon4 	= strtolower(substr($rsm['nomor_tdp_file'], strrpos($rsm['nomor_tdp_file'], '.')));
$extIkon5 	= strtolower(substr($rsm['dokumen_lainnya_file'], strrpos($rsm['dokumen_lainnya_file'], '.')));
$arrIkon	= array(
	".jpg" => "fa fa-file-image-o jarak-kanan",
	".jpeg" => "fa fa-file-image-o jarak-kanan",
	".png" => "fa fa-file-image-o jarak-kanan",
	".gif" => "fa fa-file-image-o jarak-kanan",
	".pdf" => "fa fa-file-pdf-o jarak-kanan",
	".zip" => "fa fa-file-archive-o jarak-kanan"
);

$tipe_bisnis_lain 		= ($rsm['tipe_bisnis'] == 10) ? 'value="' . $rsm['tipe_bisnis_lain'] . '"' : 'disabled';
$ownership_lain 		= ($rsm['ownership'] == 8) ? 'value="' . $rsm['ownership_lain'] . '"' : 'disabled';
$payment_schedule_other = ($rsm['payment_schedule'] == 2) ? 'value="' . $rsm['payment_schedule_other'] . '"' : 'disabled';
$payment_method_other 	= ($rsm['payment_method'] == 5) ? 'value="' . $rsm['payment_method_other'] . '"' : 'disabled';
$logistik_env_other 	= ($rsm['logistik_env'] == 3) ? 'value="' . $rsm['logistik_env_other'] . '"' : 'disabled';
$logistik_storage_other = ($rsm['logistik_storage'] == 3) ? 'value="' . $rsm['logistik_storage_other'] . '"' : 'disabled';
$logistik_hour_other 	= ($rsm['logistik_hour'] == 3) ? 'value="' . $rsm['logistik_hour_other'] . '"' : 'disabled';
$logistik_volume_other 	= ($rsm['logistik_volume'] == 3) ? 'value="' . $rsm['logistik_volume_other'] . '"' : 'disabled';
$logistik_quality_other = ($rsm['logistik_quality'] == 2) ? 'value="' . $rsm['logistik_quality_other'] . '"' : 'disabled';
$logistik_truck_other 	= ($rsm['logistik_truck'] == 5) ? 'value="' . $rsm['logistik_truck_other'] . '"' : 'disabled';
$creditor			 	= ($rsm['credit_facility'] == 1) ? 'value="' . $rsm['creditor'] . '"' : 'disabled';
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Update Customer</title>
	<link rel="shortcut icon" type="image/png" href="<?php echo BASE_URL . "/images/proenergi.jpg"; ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL . "/libraries/thirdparty/bootstrap/css/bootstrap.min.css"; ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL . "/libraries/thirdparty/fonts/font-awesome/css/fontawesome.min.css"; ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo BASE_PATH_CSS . "/style.bootstrap.css"; ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo BASE_PATH_CSS . "/style.select2.css"; ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo BASE_PATH_CSS . "/style.flash.alert.css"; ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo BASE_PATH_CSS . "/style.jquery.validationEngine.css"; ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo BASE_PATH_CSS . "/style.form.update.css"; ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo BASE_PATH_CSS . "/style.table.custom.css"; ?>" />
	<!--[if lt IE 9]>
		<script language="javascript" type="text/javascript" src="<?php echo BASE_PATH_JS . "/bootstrap/html5shiv.js"; ?>"></script>
		<script language="javascript" type="text/javascript" src="<?php echo BASE_PATH_JS . "/bootstrap/respond.min.js"; ?>"></script>
    <![endif]-->
	<script type="text/javascript">
		var base_url = '<?= getenv('APP_HOST') . getenv('APP_NAME') ?>';
		var role_id = 0;
	</script>
	<script language="javascript" type="text/javascript" src="<?php echo BASE_URL . "/libraries/thirdparty/bootstrap/js/jquery.min.js"; ?>"></script>
	<script language="javascript" type="text/javascript" src="<?php echo BASE_URL . "/libraries/thirdparty/bootstrap/js/bootstrap.min.js"; ?>"></script>
	<script language="javascript" type="text/javascript" src="<?php echo BASE_PATH_JS . "/plugins/jquery.plugin.sidebar.js"; ?>"></script>
	<script language="javascript" type="text/javascript" src="<?php echo BASE_PATH_JS . "/plugins/jquery.plugin.pace.js"; ?>"></script>
	<script language="javascript" type="text/javascript" src="<?php echo BASE_PATH_JS . "/plugins/jquery.plugin.slimscroll.js"; ?>"></script>
	<script language="javascript" type="text/javascript" src="<?php echo BASE_PATH_JS . "/plugins/jquery.plugin.iCheck.js"; ?>"></script>
	<script language="javascript" type="text/javascript" src="<?php echo BASE_PATH_JS . "/plugins/jquery.plugin.select2.min.js"; ?>"></script>
	<script language="javascript" type="text/javascript" src="<?php echo BASE_PATH_JS . "/plugins/jquery.plugin.select2.id.js"; ?>"></script>
	<script language="javascript" type="text/javascript" src="<?php echo BASE_PATH_JS . "/jquery.flash.alert.js"; ?>"></script>
	<script language="javascript" type="text/javascript" src="<?php echo BASE_URL . "/libraries/js/validation/validate/jquery.validate.js"; ?>"></script>
	<script language="javascript" type="text/javascript" src="<?php echo BASE_URL . "/libraries/js/validation/validate/jquery.validate.additional.methods.js"; ?>"></script>
	<script language="javascript" type="text/javascript" src="<?php echo BASE_URL . "/libraries/js/validation/validate/jquery.validate.messages.id.js"; ?>"></script>
	<script language="javascript" type="text/javascript" src="<?php echo BASE_URL . "/libraries/js/sweetalert2.min.js"; ?>"></script>
	<script language="javascript" type="text/javascript" src="<?php echo BASE_PATH_JS . "/jquery.form.update.js"; ?>"></script>
	<script language="javascript" type="text/javascript" src="<?php echo BASE_PATH_JS . "/money-format/jquery.number.min.js"; ?>"></script>
	<script language="javascript" type="text/javascript" src="<?php echo BASE_PATH_JS . "/jquery.mask.min.js"; ?>"></script>
	<script language="javascript" type="text/javascript" src="<?php echo BASE_PATH_JS . "/jquery.generate.js"; ?>"></script>
</head>

<body class="skin-blue fixed">
	<header class="header" style="background-color:#56386a">
		<div class="logo">
			<a style="display:block; margin:0px 15px;">
				<p style="font-family:helvetica; font-size:18px; color:#fff; text-shadow:0px 2px #777; line-height:55px;"><b>SYOP PRO ENERGI</b></p>
			</a>
		</div>
	</header>

	<div class="wrapper row-offcanvas row-offcanvas-left">
		<aside class="right-side">
			<section class="content-header">
				<h1 style="font-family:arial; font-size:16px;">Update Data Customer</h1>
			</section>
			<section class="content">

				<?php $flash->display(); ?>
				<div class="row">
					<div class="col-sm-12">
						<?php
						function changeValue($rsm, $val)
						{
							if ($rsm[$val]) {
								echo $rsm[$val];
							} else {
								if (isset($_SESSION['post'])) {
									echo $_SESSION['post'][$idr][$val];
								} else
									echo '';
							}
						}

						if (($rsm['id_customer'] && $rsm['need_update'] == 1 && $rsm['count_update'] < 2 && $rsm['token_verification'] == $token && !$rsm['is_evaluated']) ||
							(isset($enk['edit']) && $enk['edit'])
						) {
							$gform = (isset($enk['edit']) && $enk['edit'] ? 'gform' : 'gform');
							echo '<form role="form" action="' . $act . '" method="post" class="registration-form" name="gform" id="' . $gform . '">';
							require_once($public_base_directory . "/customer/__get_form_1.php");
							require_once($public_base_directory . "/customer/__get_form_2.php");
							require_once($public_base_directory . "/customer/__get_form_2b.php");
							require_once($public_base_directory . "/customer/__get_form_2c.php");
							require_once($public_base_directory . "/customer/__get_form_3.php");
							echo '</form>';
						} else if ($rsm['id_customer']) {
							require_once($public_base_directory . "/customer/__get_form_customer.php");
						} else {
							echo '<p class="text-center pad" style="background-color:#fff; border:1px solid #ddd;">Data customer tidak ditemukan .....</p>';
						}
						?>
					</div>
				</div>

				<?php $con->close(); ?>
			</section>
		</aside>
	</div>

	<div class="modal fade" id="checkModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-blue">
					<button type="button" class="close hide" id="clsmdl" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Loading Data ...</h4>
				</div>
				<div class="modal-body">
					<p class="status-global text-center hide"><img src="<?php echo BASE_IMAGE . "/loading.gif"; ?>" /></p>
					<div class="progress-status hide"></div>
					<div class="progress hide">
						<div class="progress-bar progress-bar-info" role="progressbar" style="width:0%"></div>
					</div>
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

	<style type="text/css">
		html,
		body {
			font-family: arial;
		}

		textarea.form-control {
			min-height: 95px;
		}

		.angkabiasa {
			text-align: right;
		}
	</style>
	<script type="text/javascript">
		$('.phone-number').mask('00000000000000');
		$('.fax').mask('00000000000000');
		$('.angkabiasa').number(true, 0, ".", ",");
	</script>
</body>

</html>
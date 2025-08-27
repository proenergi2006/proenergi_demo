<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$idr 	= isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
	$idk 	= htmlspecialchars($enk["idk"], ENT_QUOTES);

	$sql = "
		select a.id_customer, a.id_marketing, a.nama_customer, a.alamat_customer, a.prov_customer, a.kab_customer, a.telp_customer, a.fax_customer, a.email_customer,
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
		h.nama_prov as propinsi_payment, i.nama_kab as kabupaten_payment,
		a.jenis_payment, a.jenis_net, 
		a.postalcode_customer, d.postalcode_billing, a.credit_limit_diajukan, a.credit_limit, e.desc_stor_fac, e.desc_condition 
		from pro_customer a 
		left join pro_customer_contact b on a.id_customer = b.id_customer 
		left join pro_customer_payment d on a.id_customer = d.id_customer left join pro_customer_logistik e on a.id_customer = e.id_customer 
		left join pro_master_provinsi f on a.prov_customer = f.id_prov left join pro_master_kabupaten g on a.kab_customer = g.id_kab 
		left join pro_master_provinsi h on d.prov_billing = h.id_prov left join pro_master_kabupaten i on d.kab_billing = i.id_kab 
		where a.id_customer = '".$idr."' 
	";
	$rsm = $con->getRecord($sql);
	$act = ACTION_CLIENT."/customer-update-permohonan.php";

	$base_directory	= $public_base_directory."/files/uploaded_user/images";
	$file_path_sert	= $base_directory."/sert_file".$idr."_".$rsm['nomor_sertifikat_file'];
	$file_path_npwp	= $base_directory."/npwp_file".$idr."_".$rsm['nomor_npwp_file'];
	$file_path_siup	= $base_directory."/siup_file".$idr."_".$rsm['nomor_siup_file'];
	$file_path_tdpn	= $base_directory."/tdp_file".$idr."_".$rsm['nomor_tdp_file'];

	$tipe_bisnis_lain 		= ($rsm['tipe_bisnis'] == 10)?'value="'.$rsm['tipe_bisnis_lain'].'"':'disabled';
	$ownership_lain 		= ($rsm['ownership'] == 8)?'value="'.$rsm['ownership_lain'].'"':'disabled';
	$payment_schedule_other = ($rsm['payment_schedule'] == 2)?'value="'.$rsm['payment_schedule_other'].'"':'disabled';
	$payment_method_other 	= ($rsm['payment_method'] == 5)?'value="'.$rsm['payment_method_other'].'"':'disabled';
	$logistik_env_other 	= ($rsm['logistik_env'] == 3)?'value="'.$rsm['logistik_env_other'].'"':'disabled';
	$logistik_storage_other = ($rsm['logistik_storage'] == 3)?'value="'.$rsm['logistik_storage_other'].'"':'disabled';
	$logistik_hour_other 	= ($rsm['logistik_hour'] == 3)?'value="'.$rsm['logistik_hour_other'].'"':'disabled';
	$logistik_volume_other 	= ($rsm['logistik_volume'] == 3)?'value="'.$rsm['logistik_volume_other'].'"':'disabled';
	$logistik_quality_other = ($rsm['logistik_quality'] == 2)?'value="'.$rsm['logistik_quality_other'].'"':'disabled';
	$logistik_truck_other 	= ($rsm['logistik_truck'] == 5)?'value="'.$rsm['logistik_truck_other'].'"':'disabled';
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("formatNumber", "jqueryUI"), "css"=>array("jqueryUI"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1>PERUBAHAN DATA CUSTOMER</h1>
        	</section>
			<section class="content">

				<?php if($enk['idk'] !== '' && isset($enk['idk'])){ ?>
				<?php $flash->display(); ?>
                <link rel="stylesheet" type="text/css" href="<?php echo BASE_PATH_CSS."/style.form.update02.css"; ?>" />

                <div class="row">
                    <div class="col-sm-12">
                        <form role="form" action="<?php echo $act; ?>" method="post" class="registration-form" name="gform" id="gform">
						<?php 
							function changeValue ($rsm, $val) {
								if (isset($rsm[$val]) and $rsm[$val]) {
									echo $rsm[$val];
								} else {
									if (isset($_SESSION['post']))
										echo $_SESSION['post'][$idr][$val];
									else
										echo '';
								}
							}
							require_once($public_base_directory."/customer/__get_form_1.php");
							require_once($public_base_directory."/customer/__get_form_2.php");
							require_once($public_base_directory."/customer/__get_form_2b.php");
							require_once($public_base_directory."/customer/__get_form_2c.php");
							require_once($public_base_directory."/customer/__get_form_3c.php");
                        ?>
                        </form>
                    </div>
                </div>
			<?php } ?>
			<?php $con->close(); ?>
			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
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
                    <p class="status-global text-center hide"><img src="<?php echo BASE_IMAGE."/loading.gif"; ?>" /></p>
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

<script language="javascript" type="text/javascript" src="<?php echo BASE_PATH_JS."/jquery.form.update02.js"; ?>"></script>
<script type="text/javascript">
	$('.phone-number').mask('00000000000000');
	$('.fax').mask('00000000000000');
	$('.angkabiasa').number(true, 0, ".", ",");
</script>
</body>
</html>      

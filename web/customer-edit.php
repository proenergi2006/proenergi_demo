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
    $action = "edit";
    $section = "Edit Data";
	$idr 	= isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';

    $server_uri = $_SERVER['REQUEST_URI'];
    $server_uri = explode('?', $server_uri);
    $server_uri = $server_uri[1];

	$sql = "select a.id_customer, a.id_marketing, a.nama_customer, a.alamat_customer, a.prov_customer, a.kab_customer, a.jenis_customer,
            a.telp_customer, a.fax_customer, a.email_customer, 
		    a.website_customer, a.tipe_bisnis, a.tipe_bisnis_lain, a.ownership, a.ownership_lain, a.need_update, a.count_update, a.status_customer, a.fix_customer_since, 
			a.fix_customer_redate, a.top_payment, a.lastupdate_time, a.lastupdate_ip, a.lastupdate_by, b.pic_decision_name, b.pic_decision_position, b.pic_decision_telp, 
			b.pic_decision_mobile, b.pic_decision_email, b.pic_ordering_name, b.pic_ordering_position, b.pic_ordering_telp, b.pic_ordering_mobile, b.pic_ordering_email, 
			b.pic_billing_name, b.pic_billing_position, b.pic_billing_telp, b.pic_billing_mobile, b.pic_billing_email, b.pic_invoice_name, b.pic_invoice_position, 
			b.pic_invoice_telp, b.pic_invoice_mobile, b.pic_invoice_email, a.nomor_sertifikat, a.nomor_sertifikat_file, a.nomor_npwp, a.nomor_npwp_file, a.nomor_siup, 
			a.nomor_siup_file, a.nomor_tdp, a.nomor_tdp_file, d.email_billing, d.alamat_billing, d.prov_billing, d.kab_billing, d.telp_billing, d.fax_billing, 
			d.payment_schedule, d.payment_schedule_other, d.payment_method, d.payment_method_other, d.invoice, d.ket_extra, e.logistik_area, e.logistik_bisnis, e.logistik_env, 
			e.logistik_env_other, e.logistik_storage, e.logistik_storage_other, e.logistik_hour, e.logistik_hour_other, e.logistik_volume, e.logistik_volume_other, 
			e.logistik_quality, e.logistik_quality_other, e.logistik_truck, f.nama_prov as propinsi_customer, g.nama_kab as kabupaten_customer, e.logistik_truck_other, 
			h.nama_prov as propinsi_payment, i.nama_kab as kabupaten_payment, j.token_verification, j.is_evaluated, j.legal_data, j.legal_summary, j.legal_result, 
			j.legal_tgl_proses, j.legal_pic, j.finance_data, j.finance_summary, j.finance_result, j.finance_tgl_proses, j.finance_pic, j.logistik_data, j.logistik_summary, 
			j.logistik_result, j.logistik_tgl_proses, j.logistik_pic, j.om_summary, j.om_result, j.om_tgl_proses, j.cfo_summary, j.cfo_result, j.cfo_tgl_proses, 
			j.ceo_summary, j.ceo_result, j.ceo_tgl_proses, k.nama_cabang as wilayah, a.jenis_payment, l.review1, l.review2, l.review3, l.review4, l.review5, l.review6, l.review7, 
			j.disposisi_result, l.review8, l.review9, l.review10, l.review11, l.review12, l.review13, l.review14, l.review15, l.review16, l.review_result, l.review_pic, 
			l.review_tanggal, l.review_summary, l.review_attach, l.review_attach_ori, l.id_review, j.om_pic, j.ceo_pic, j.cfo_pic, j.sm_summary, j.sm_result, j.sm_tgl_proses, 
			j.sm_pic, a.credit_limit, a.credit_limit_diajukan, a.postalcode_customer, d.postalcode_billing, a.jenis_net, e.desc_stor_fac, e.desc_condition
			from pro_customer a left join pro_customer_contact b on a.id_customer = b.id_customer 
			left join pro_customer_payment d on a.id_customer = d.id_customer left join pro_customer_logistik e on a.id_customer = e.id_customer 
			left join pro_master_provinsi f on a.prov_customer = f.id_prov left join pro_master_kabupaten g on a.kab_customer = g.id_kab 
			left join pro_master_provinsi h on d.prov_billing = h.id_prov left join pro_master_kabupaten i on d.kab_billing = i.id_kab 
			left join pro_customer_verification j on a.id_customer = j.id_customer and a.id_verification = j.id_verification 
			left join pro_customer_review l on j.id_verification = l.id_verification 
			left join pro_master_cabang k on a.id_wilayah = k.id_master and a.id_group = k.id_group_cabang 
			where a.id_customer = '".$idr."'";
	$rsm = $con->getRecord($sql);
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("formatNumber"))); ?>

<body class="skin-blue fixed">
    <?php include_once($public_base_directory."/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1><?php echo $section." Customer"; ?></h1>
            </section>
            <section class="content">

                <?php $flash->display(); ?>
                <form action="<?php echo ACTION_CLIENT.'/customer-edit.php'; ?>" id="gform" name="gform" class="form-horizontal" method="post" role="form">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-4">Marketing *</label>
                                    <div class="col-md-8">
										<?php if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) != 11 && paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) != 17 && paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) != 18){ ?>
                                        <select id="marketing" name="marketing" class="form-control select2" required>
                                            <option></option>
                                            <?php $con->fill_select("id_user","fullname","acl_user",$rsm['id_marketing'],"where is_active=1 and id_role=3","fullname",false); ?>
                                        </select>
                                        <?php } else{ ?>
                                        <input type="hidden" id="marketing" name="marketing" value="<?php echo paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);?>" />
                                        <input type="text" id="adN" name="adN" class="form-control" value="<?php echo paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname']);?>" readonly />
                                        <?php } ?>
									</div>
								</div>
							</div>
                            <div class="col-md-6">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-4">Nama Perusahaan *</label>
                                    <div class="col-md-8">
										<input type="text" id="nama_customer" name="nama_customer" class="form-control" required value="<?php echo $rsm['nama_customer']; ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-4">Email *</label>
                                    <div class="col-md-8">
										<input type="text" id="email_customer" name="email_customer" class="form-control" required data-rule-email="true" value="<?php echo $rsm['email_customer']; ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-4">Alamat Perusahaan *</label>
                                    <div class="col-md-8">
										<input type="text" id="alamat_customer" name="alamat_customer" class="form-control" required value="<?php echo $rsm['alamat_customer']; ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-4">Propinsi *</label>
                                    <div class="col-md-8">
                                        <select id="prov_customer" name="prov_customer" class="form-control select2" required>
                                            <option></option>
                                            <?php $con->fill_select("id_prov","nama_prov","pro_master_provinsi",$rsm['prov_customer'],"","nama_prov",false); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-4">Kabupaten/Kota *</label>
                                    <div class="col-md-8">
                                        <select id="kab_customer" name="kab_customer" class="form-control select2" required>
                                            <option></option>
                                            <?php $con->fill_select("id_kab","nama_kab","pro_master_kabupaten",$rsm['kab_customer'],"","nama_kab",false); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-4">Postal Code</label>
                                    <div class="col-md-4">
                                        <input type="text" id="postalcode_customer" name="postalcode_customer" class="form-control" value="<?php echo $rsm['postalcode_customer']; ?>"/>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-4">Telepon *</label>
                                    <div class="col-md-8">
                                        <input type="text" id="telp_customer" name="telp_customer" class="form-control" required value="<?php echo $rsm['telp_customer']; ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-4">Fax</label>
                                    <div class="col-md-8">
                                        <input type="text" id="fax_customer" name="fax_customer" class="form-control" value="<?php echo $rsm['fax_customer']; ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-4">Jenis Customer *</label>
                                    <div class="col-md-4">
                                        <select id="jenis_customer" name="jenis_customer" class="form-control select2" required>
                                            <option></option>
                                            <option value="PROJECT" <?php echo ($rsm['jenis_customer'] == 'PROJECT')?' selected':''; ?>>PROJECT</option>
                                            <option value="RETAIL" <?php echo ($rsm['jenis_customer'] == 'RETAIL')?' selected':''; ?>>RETAIL</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
						</div>

                        <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

                        <div style="margin-bottom:15px;">
                            <input type="hidden" name="act" value="<?php echo $action;?>" />
                            <input type="hidden" name="idr" value="<?php echo $idr;?>" />
                            <input type="hidden" name="server_uri" value="<?php echo $server_uri;?>" />
                            <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">
                            <i class="fa fa-save jarak-kanan"></i> Simpan</button>
                            <a href="<?php echo BASE_URL_CLIENT.'/customer.php'; ?>" class="btn btn-default" style="min-width:90px;">
                            <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                        </div>

						<p><small>* Wajib Diisi</small></p>
                    </div>
                </div>
                </form>

            <?php $con->close(); ?>
            </section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
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

<script>
$(document).ready(function(){
	var formValidasiCfg = {
		submitHandler: function(form) {
			$("#loading_modal").modal({keyboard:false, backdrop:'static'});

			if($("#cekkolnup").is(":checked") && $("#nup_fee").val() == ""){
				$("#loading_modal").modal("hide");
				$.validator.showErrorField('nup_fee', "Kolom ini belum diisi atau dipilih");
				setErrorFocus($("#nup_fee"), $("form#gform"), false);
			} else{
				form.submit();
			}
		}	
	};
	$("form#gform").validate($.extend(true,{},config.validation,formValidasiCfg));

	$("select#prov_customer").change(function(){
		$("select#kab_customer").val("").trigger('change').select2('close');
		$("select#kab_customer option").remove();
		$.ajax({
			type    : "POST",
			url     : "./__get_kabupaten.php",
			dataType: 'json',
			data    : { q1 : $("select#prov_customer").val() },
			cache   : false,
			success : function(data){ 
				if(data.items != ""){
					$("select#kab_customer").select2({ 
						data        : data.items, 
						placeholder : "Pilih salah satu", 
						allowClear  : true, 
					});
					return false;
				}
			}
		});
	});
});
</script>
</body>
</html>      
